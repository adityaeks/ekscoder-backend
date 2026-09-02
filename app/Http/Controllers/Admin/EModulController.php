<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EModul;
use App\Services\NineRouterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EModulController extends Controller
{
    protected NineRouterService $aiService;

    public function __construct(NineRouterService $aiService)
    {
        $this->aiService = $aiService;
    }
    /**
     * Display a listing of the e-moduls.
     */
    public function index()
    {
        $moduls = EModul::latest()->get();

        $stats = [
            'total' => $moduls->count(),
            'published' => $moduls->where('is_active', true)->count(),
            'draft' => $moduls->where('is_active', false)->count(),
            'total_categories' => $moduls->pluck('category')->filter()->unique()->count(),
        ];

        return view('admin.e-modul.index', compact('stats', 'moduls'));
    }

    /**
     * Show the form for creating a new e-modul.
     */
    public function create()
    {
        return redirect()->route('admin.e-modul.index', ['action' => 'upload']);
    }

    /**
     * Store a newly created e-modul in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'pdf_file' => 'required|file|mimes:pdf|max:102400', // Max 100MB
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'total_pages' => 'nullable|integer',
        ]);

        $filePath = $request->file('pdf_file')->store('emoduls', 'public');
        $fileSize = $request->file('pdf_file')->getSize();

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('emoduls/covers', 'public');
        }

        // Generate base unique slug
        $baseSlug = Str::slug($validated['title']);
        $slug = $baseSlug;
        $counter = 1;
        while (EModul::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $modul = EModul::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'category' => $validated['category'] ?? 'Umum',
            'description' => $validated['description'] ?? null,
            'file_path' => $filePath,
            'file_size' => $fileSize,
            'total_pages' => $validated['total_pages'] ?? 0,
            'cover_image' => $coverPath,
            'is_active' => true,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'E-Modul berhasil diunggah!',
                'data' => $modul,
                'pdf_url' => $modul->pdf_url,
            ]);
        }

        return redirect()->route('admin.e-modul.index')->with('success', 'E-Modul berhasil diupload!');
    }

    /**
     * Display the specified e-modul (Flipbook Reader).
     */
    public function show(EModul $e_modul)
    {
        return view('admin.e-modul.show', compact('e_modul'));
    }

    /**
     * Show the form for editing the specified e-modul.
     */
    public function edit(EModul $e_modul)
    {
        return view('admin.e-modul.edit', compact('e_modul'));
    }

    /**
     * Update the specified e-modul in storage.
     */
    public function update(Request $request, EModul $e_modul)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'pdf_file' => 'nullable|file|mimes:pdf|max:102400',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('pdf_file')) {
            if ($e_modul->file_path && Storage::disk('public')->exists($e_modul->file_path)) {
                Storage::disk('public')->delete($e_modul->file_path);
            }
            $e_modul->file_path = $request->file('pdf_file')->store('emoduls', 'public');
            $e_modul->file_size = $request->file('pdf_file')->getSize();
        }

        if ($request->hasFile('cover_image')) {
            if ($e_modul->cover_image && Storage::disk('public')->exists($e_modul->cover_image)) {
                Storage::disk('public')->delete($e_modul->cover_image);
            }
            $e_modul->cover_image = $request->file('cover_image')->store('emoduls/covers', 'public');
        }

        $e_modul->title = $validated['title'];
        $e_modul->category = $validated['category'] ?? $e_modul->category;
        $e_modul->description = $validated['description'] ?? $e_modul->description;
        $e_modul->is_active = $request->has('is_active') ? (bool)$request->is_active : $e_modul->is_active;
        $e_modul->save();

        return redirect()->route('admin.e-modul.index')->with('success', 'E-Modul berhasil diperbarui!');
    }

    /**
     * Toggle active state.
     */
    public function toggleActive(EModul $e_modul)
    {
        $e_modul->is_active = !$e_modul->is_active;
        $e_modul->save();

        return back()->with('success', 'Status E-Modul berhasil diubah!');
    }

    /**
     * Remove the specified e-modul from storage.
     */
    public function destroy(EModul $e_modul)
    {
        if ($e_modul->file_path && Storage::disk('public')->exists($e_modul->file_path)) {
            Storage::disk('public')->delete($e_modul->file_path);
        }
        if ($e_modul->cover_image && Storage::disk('public')->exists($e_modul->cover_image)) {
            Storage::disk('public')->delete($e_modul->cover_image);
        }

        $e_modul->delete();

        return redirect()->route('admin.e-modul.index')->with('success', 'E-Modul berhasil dihapus!');
    }

    /**
     * Ask AI question about the e-modul content / current page.
     */
    public function askAi(Request $request, ?EModul $e_modul = null)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:2000',
            'modul_title' => 'nullable|string|max:255',
            'page_number' => 'nullable|string|max:50',
            'page_text' => 'nullable|string|max:20000',
            'history' => 'nullable|array',
        ]);

        $modulTitle = $e_modul ? $e_modul->title : ($validated['modul_title'] ?? 'E-Modul Pembelajaran');
        $pageNumber = $validated['page_number'] ?? 'Semua Halaman';
        $pageText = $validated['page_text'] ?? '';
        $question = $validated['question'];

        $systemPrompt = "Anda adalah AI Teaching & Study Assistant interaktif untuk E-Modul \"{$modulTitle}\".\n"
            . "Tugas Anda adalah membantu pembaca memahami materi buku/modul ini secara komprehensif, mendalam, dan solutif dalam bahasa Indonesia.\n\n"
            . "PANDUAN MENJAWAB:\n"
            . "1. Jika pembaca bertanya tentang halaman tertentu atau modul secara umum, gunakan teks/materi modul yang dilampirkan sebagai rujukan utama.\n"
            . "2. Jika ditanyakan tentang materi di halaman lain atau modul secara keseluruhan, jawablah secara lengkap berdasarkan konteks modul yang relevan.\n"
            . "3. Jika materi yang ditanyakan tidak tertulis secara eksplisit namun berkaitan dengan topik modul, berikan penjelasan ilmiah/edukatif yang akurat dan sebutkan penjelasannya.\n"
            . "4. Gunakan format Markdown yang rapi (bullet point, bold, list, atau tabel jika sesuai) agar nyaman dibaca.\n"
            . "5. Jika diminta membuat kuis/soal, sertakan pertanyaan pilihan ganda atau esai beserta kunci jawaban dan penjelasannya.";

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        // Append historical conversation if provided
        if (!empty($validated['history']) && is_array($validated['history'])) {
            foreach ($validated['history'] as $hist) {
                if (isset($hist['role'], $hist['content']) && in_array($hist['role'], ['user', 'assistant'])) {
                    $messages[] = [
                        'role' => $hist['role'],
                        'content' => (string)$hist['content']
                    ];
                }
            }
        }

        // Prepare User message with context
        $userContent = "";
        if (!empty($pageText)) {
            $userContent .= "[KONTEKS MATERI E-MODUL ({$pageNumber})]:\n\"\"\"\n" . Str::limit($pageText, 10000) . "\n\"\"\"\n\n";
        }
        $userContent .= "[PERTANYAAN PEMBACA]:\n" . $question;

        $messages[] = [
            'role' => 'user',
            'content' => $userContent,
        ];

        try {
            $aiReply = $this->aiService->getChatCompletions($messages);

            return response()->json([
                'success' => true,
                'reply' => $aiReply,
                'page' => $pageNumber,
            ]);
        } catch (\Throwable $e) {
            Log::error("E-Modul AI Error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'fallback_reply' => "⚠️ Gagal mendapatkan jawaban dari AI: " . $e->getMessage(),
            ], 500);
        }
    }
}
