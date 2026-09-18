<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiSetting;
use App\Models\EModul;
use App\Models\UserLog;
use App\Services\NineRouterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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
        Gate::authorize('emodul.view');

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
        Gate::authorize('emodul.create');

        return redirect()->route('admin.e-modul.index', ['action' => 'upload']);
    }

    /**
     * Store a newly created e-modul in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('emodul.create');
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
        $isPublic = false;
        return view('admin.e-modul.show', compact('e_modul', 'isPublic'));
    }

    /**
     * Display the public e-modul reader by slug (No Auth Required).
     */
    public function publicShow(string $slug)
    {
        $e_modul = EModul::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $isPublic = true;
        return view('admin.e-modul.show', compact('e_modul', 'isPublic'));
    }

    /**
     * Stream PDF file publicly with permissive CORS for flipbook rendering.
     */
    public function publicPdf(string $slug)
    {
        $e_modul = EModul::where('slug', $slug)->where('is_active', true)->firstOrFail();

        if (!$e_modul->file_path || !Storage::disk('public')->exists($e_modul->file_path)) {
            abort(404, 'File modul tidak ditemukan.');
        }

        $fullPath = Storage::disk('public')->path($e_modul->file_path);

        return response()->file($fullPath, [
            'Content-Type' => 'application/pdf',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, HEAD, OPTIONS',
            'Access-Control-Allow-Headers' => '*',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    /**
     * Public AI Assistant question endpoint for the shared e-modul.
     */
    public function publicAskAi(Request $request, string $slug)
    {
        $e_modul = EModul::where('slug', $slug)->where('is_active', true)->firstOrFail();
        return $this->askAi($request, $e_modul);
    }

    /**
     * Show the form for editing the specified e-modul.
     */
    public function edit(EModul $e_modul)
    {
        Gate::authorize('emodul.edit');

        return view('admin.e-modul.edit', compact('e_modul'));
    }

    /**
     * Update the specified e-modul in storage.
     */
    public function update(Request $request, EModul $e_modul)
    {
        Gate::authorize('emodul.edit');

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
        Gate::authorize('emodul.toggle-active');

        $e_modul->is_active = !$e_modul->is_active;
        $e_modul->save();

        return back()->with('success', 'Status E-Modul berhasil diubah!');
    }

    /**
     * Remove the specified e-modul from storage.
     */
    public function destroy(EModul $e_modul)
    {
        Gate::authorize('emodul.delete');

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
     * Get default system prompt template for E-Modul.
     */
    public static function getDefaultSystemPrompt(): string
    {
        return <<<PROMPT
Anda adalah AI Teaching Assistant & Pembimbing Belajar Interaktif untuk E-Modul "{modul_title}".
Tugas utama Anda adalah MEMBIMBING siswa memahami materi secara mandiri melalui metode penemuan terbimbing (Guided Inquiry / Socratic Scaffolding), BUKAN memberikan jawaban instan atau jawaban akhir secara langsung.

ATURAN UTAMA & CARA MENJAWAB (WAJIB DIPATUHI):
1. JANGAN LANGSUNG BERIKAN JAWABAN JADI: Jangan pernah memberikan jawaban langsung atau kesimpulan final saat siswa bertanya. Bimbing siswa langkah demi langkah dengan pertanyaan pemantik agar siswa berpikir, mengamati, dan menyimpulkan sendiri konsep materi "{modul_title}".
2. STRATEGI DUA TAHAP PEMBIMBINGAN:

--- Tahap 1 — Siswa belum memahami fenomena / bingung:
- Berikan respon suportif ("Tidak apa-apa. Kita coba mencari jawabannya bersama.").
- Ajak siswa mengamati kembali perbedaan wujud, sifat, kondisi, atau data sebelum dan sesudah peristiwa terjadi.
- Arahkan ke pertanyaan hubungan sebab-akibat sederhana.
- AI BELUM memberikan jawaban akhir, tetapi membantu siswa menemukan hubungan sebab-akibat.

Contoh Alur Percakapan Tahap 1:
Siswa:
Kak, aku nggak tahu jawabannya. Kenapa malam harus dipanaskan?

AI:
Tidak apa-apa. Kita coba mencari jawabannya bersama.
Coba perhatikan kembali malam sebelum dan sesudah dipanaskan. Bagaimana perbedaan wujud atau sifat malam pada kedua kondisi tersebut?

Siswa:
Sebelum dipanaskan keras, setelah dipanaskan jadi cair.

AI:
Bagus. Berarti pemanasan menyebabkan perubahan pada malam. Sekarang pikirkan: apa yang terjadi pada suhu malam ketika menerima panas dari sumber pemanas?

Siswa:
Suhunya naik.

AI:
Tepat. Sekarang hubungkan kedua pengamatanmu: jika malam menerima kalor sehingga suhunya meningkat, apa hubungan kalor yang diberikan dengan perubahan kondisi malam tersebut?

(➡️ AI belum memberikan jawaban akhir, tetapi membantu siswa menemukan hubungan sebab-akibat).

--- Tahap 2 — Siswa sudah punya dugaan, tetapi belum dapat menjelaskan:
- Beri apresiasi bahwa dugaan awal siswa sudah mengarah ke konsep yang tepat.
- Tantang siswa untuk tidak langsung berhenti pada dugaan permukaan, ajak berpikir lebih dalam ke tingkat partikel, mekanisme, atau hubungan energi.
- Pandu siswa agar merumuskan kesimpulan dengan bahasanya sendiri (misalnya untuk ditulis pada LKPD).

Contoh Alur Percakapan Tahap 2:
Siswa:
Jadi malam mencair karena suhunya naik?

AI:
Dugaanmu sudah mengarah ke konsep yang tepat. Sekarang jangan langsung berhenti pada “suhunya naik”.
Menurutmu, apa yang terjadi pada partikel-partikel penyusun malam ketika menerima energi panas?

Siswa:
Partikelnya bergerak lebih cepat?

AI:
Ya, itu salah satu petunjuk penting. Jika energi yang diterima semakin banyak, bagaimana perubahan gerak partikel dapat berkaitan dengan perubahan suhu dan perubahan wujud malam?

Siswa:
Mungkin energi panas membuat gerak partikelnya meningkat sampai malam berubah dari padat menjadi cair.

AI:
Coba tuliskan hubungan tersebut dengan bahasamu sendiri sebagai kesimpulan sementara pada LKPD.

3. RUANG LINGKUP MODUL: Bimbingan hanya seputar topik materi dan fenomena pada modul "{modul_title}". Jika siswa bertanya hal di luar materi modul, ingatkan dengan santun untuk fokus pada materi modul ini.
4. GAYA BAHASA: Ramah, bersahabat, sabar, dan menyemangati seperti guru pembimbing sains yang interaktif.
PROMPT;
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

        // Load custom prompt from settings or fallback to default
        $rawPrompt = (string) AiSetting::get('emodul_system_prompt', self::getDefaultSystemPrompt());
        if (trim($rawPrompt) === '') {
            $rawPrompt = self::getDefaultSystemPrompt();
        }

        // Replace placeholders
        if (str_contains(strtolower($pageNumber), 'seluruh') || str_contains(strtolower($pageNumber), 'semua')) {
            $systemPrompt = str_replace(
                ['Halaman {page_number}', '{page_number}', '{modul_title}'],
                ['Seluruh Modul', 'Seluruh Modul', $modulTitle],
                $rawPrompt
            );
        } else {
            $systemPrompt = str_replace(
                ['{modul_title}', '{page_number}'],
                [$modulTitle, $pageNumber],
                $rawPrompt
            );
        }

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

        // Get configured model
        $model = AiSetting::get('emodul_model') ?: null;

        try {
            $aiReply = $this->aiService->getChatCompletions($messages, $model);

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

    /**
     * Dedicated Page for E-Modul AI Settings & Simulator.
     */
    public function settings(Request $request)
    {
        Gate::authorize('emodul.edit');

        $models = $this->aiService->getModels();
        if (empty($models)) {
            $models = [
                'google/gemini-2.0-flash-001',
                'google/gemini-1.5-flash',
                'deepseek/deepseek-chat',
                'openai/gpt-4o-mini',
                'Spark',
                'Muse'
            ];
        }

        $settings = [
            'emodul_system_prompt'  => (string) AiSetting::get('emodul_system_prompt', self::getDefaultSystemPrompt()),
            'emodul_model'          => (string) AiSetting::get('emodul_model', ''),
            'emodul_response_style' => (string) AiSetting::get('emodul_response_style', 'inquiry'),
        ];

        $defaultPrompt = self::getDefaultSystemPrompt();
        $moduls = EModul::latest()->get();
        $selectedModulId = $request->query('modul_id');

        return view('admin.e-modul.settings', compact('models', 'settings', 'defaultPrompt', 'moduls', 'selectedModulId'));
    }

    /**
     * Get active E-Modul AI settings and models.
     */
    public function getAiSettings(): JsonResponse
    {
        Gate::authorize('emodul.edit');

        $models = $this->aiService->getModels();
        if (empty($models)) {
            $models = [
                'google/gemini-2.0-flash-001',
                'google/gemini-1.5-flash',
                'deepseek/deepseek-chat',
                'openai/gpt-4o-mini',
                'Spark',
                'Muse'
            ];
        }

        return response()->json([
            'success' => true,
            'settings' => [
                'emodul_system_prompt'  => (string) AiSetting::get('emodul_system_prompt', self::getDefaultSystemPrompt()),
                'emodul_model'          => (string) AiSetting::get('emodul_model', ''),
                'emodul_response_style' => (string) AiSetting::get('emodul_response_style', 'inquiry'),
            ],
            'default_prompt' => self::getDefaultSystemPrompt(),
            'models' => array_values(array_unique($models)),
        ]);
    }

    /**
     * Save E-Modul AI settings and prompt.
     */
    public function saveAiSettings(Request $request): JsonResponse
    {
        Gate::authorize('emodul.edit');

        $validated = $request->validate([
            'emodul_system_prompt'  => 'required|string',
            'emodul_model'          => 'nullable|string|max:100',
            'emodul_response_style' => 'nullable|string|max:50',
        ]);

        $prompt = str_replace(["\r\n", "\r"], "\n", (string) $validated['emodul_system_prompt']);

        AiSetting::set('emodul_system_prompt', trim($prompt));
        AiSetting::set('emodul_model', trim((string) ($validated['emodul_model'] ?? '')));
        AiSetting::set('emodul_response_style', trim((string) ($validated['emodul_response_style'] ?? 'inquiry')));

        UserLog::log(
            action: 'update',
            module: 'E-Modul',
            description: "Pengaturan & Prompt AI E-Modul diperbarui oleh " . (auth()->user()?->name ?? 'Admin')
        );

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan Prompt & AI E-Modul berhasil disimpan!',
        ]);
    }

    /**
     * Test simulator prompt with dummy/real question.
     */
    public function testPrompt(Request $request): JsonResponse
    {
        Gate::authorize('emodul.edit');

        $validated = $request->validate([
            'question'      => 'required|string|max:1000',
            'system_prompt' => 'required|string',
            'model'         => 'nullable|string|max:100',
            'modul_id'      => 'nullable|integer',
            'modul_title'   => 'nullable|string|max:255',
            'page_number'   => 'nullable|string|max:50',
        ]);

        $modulId = $request->input('modul_id');
        $modul = $modulId ? EModul::find($modulId) : null;

        $modulTitle = $modul ? $modul->title : ($request->input('modul_title') ?: 'Modul Pembelajaran (Simulasi)');
        $pageNumber = 'Seluruh Halaman';

        $systemPrompt = str_replace(
            ['Halaman {page_number}', '{page_number}', '{modul_title}'],
            ['Seluruh Halaman', 'Seluruh Halaman', $modulTitle],
            $validated['system_prompt']
        );

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        $userContent = "";
        if ($modul) {
            $userContent .= "[INFORMASI MODUL ACUAN (Cakupan: Seluruh Halaman Modul)]:\n";
            $userContent .= "- Judul Modul: {$modul->title}\n";
            if ($modul->category) {
                $userContent .= "- Kategori/Mata Pelajaran: {$modul->category}\n";
            }
            if ($modul->description) {
                $userContent .= "- Ringkasan Materi/Isi: {$modul->description}\n";
            }
            $userContent .= "- Total Halaman Modul: {$modul->total_pages} Halaman\n\n";
        }

        $userContent .= "[PERTANYAAN PEMBACA]:\n" . $validated['question'];

        $messages[] = [
            'role' => 'user',
            'content' => $userContent,
        ];

        try {
            $model = $request->input('model') ?: null;
            $reply = $this->aiService->getChatCompletions($messages, $model);

            return response()->json([
                'success'     => true,
                'reply'       => $reply,
                'modul_title' => $modulTitle,
                'page_number' => $pageNumber,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses AI: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check cache status of an e-modul.
     */
    public function getCacheStatus(EModul $e_modul): JsonResponse
    {
        return response()->json($this->resolveCacheStatus($e_modul));
    }

    /**
     * Public endpoint to check cache status by slug.
     */
    public function publicGetCacheStatus(string $slug): JsonResponse
    {
        $e_modul = EModul::where('slug', $slug)->where('is_active', true)->firstOrFail();
        return response()->json($this->resolveCacheStatus($e_modul));
    }

    /**
     * Resolve cache manifest and status for an e-modul.
     */
    protected function resolveCacheStatus(EModul $e_modul): array
    {
        $dir = "emoduls/cache/{$e_modul->id}";
        $manifestPath = "{$dir}/manifest.json";

        if (!Storage::disk('public')->exists($manifestPath)) {
            return [
                'has_cache' => false,
                'is_complete' => false,
                'cached_pages' => [],
                'total_pages' => (int) $e_modul->total_pages,
            ];
        }

        try {
            $manifest = json_decode(Storage::disk('public')->get($manifestPath), true) ?: [];
            $cachedPages = $manifest['cached_pages'] ?? [];
            $totalPages = (int) ($manifest['total_pages'] ?? $e_modul->total_pages ?? 0);
            $isComplete = !empty($manifest['is_complete']) || ($totalPages > 0 && count($cachedPages) >= $totalPages);

            return [
                'has_cache' => count($cachedPages) > 0,
                'is_complete' => $isComplete,
                'total_pages' => $totalPages,
                'page_width' => $manifest['page_width'] ?? null,
                'page_height' => $manifest['page_height'] ?? null,
                'cached_pages' => $cachedPages,
                'base_url' => asset("storage/{$dir}"),
                'texts' => $manifest['texts'] ?? [],
            ];
        } catch (\Throwable $e) {
            Log::warning("Failed to read e-modul cache manifest: " . $e->getMessage());
            return [
                'has_cache' => false,
                'is_complete' => false,
                'cached_pages' => [],
                'total_pages' => (int) $e_modul->total_pages,
            ];
        }
    }

    /**
     * Save a batch of pre-rendered page images and texts to cache storage.
     */
    public function saveCacheBatch(Request $request, EModul $e_modul): JsonResponse
    {
        return $this->handleSaveCacheBatch($request, $e_modul);
    }

    /**
     * Public endpoint to save cache batch.
     */
    public function publicSaveCacheBatch(Request $request, string $slug): JsonResponse
    {
        $e_modul = EModul::where('slug', $slug)->where('is_active', true)->firstOrFail();
        return $this->handleSaveCacheBatch($request, $e_modul);
    }

    /**
     * Process and store rendered pages.
     */
    protected function handleSaveCacheBatch(Request $request, EModul $e_modul): JsonResponse
    {
        $request->validate([
            'total_pages' => 'required|integer|min:1',
            'page_width' => 'nullable|numeric',
            'page_height' => 'nullable|numeric',
            'pages' => 'required|array',
            'pages.*.page_number' => 'required|integer|min:1',
            'pages.*.image' => 'required|string',
            'pages.*.thumb' => 'nullable|string',
            'pages.*.text' => 'nullable|string',
        ]);

        $dir = "emoduls/cache/{$e_modul->id}";
        if (!Storage::disk('public')->exists($dir)) {
            Storage::disk('public')->makeDirectory($dir);
        }

        $manifestPath = "{$dir}/manifest.json";
        $manifest = [];
        if (Storage::disk('public')->exists($manifestPath)) {
            $manifest = json_decode(Storage::disk('public')->get($manifestPath), true) ?: [];
        }

        $totalPages = (int) $request->input('total_pages');
        $pageWidth = $request->input('page_width', $manifest['page_width'] ?? null);
        $pageHeight = $request->input('page_height', $manifest['page_height'] ?? null);

        $cachedPages = array_unique(array_merge($manifest['cached_pages'] ?? [], []));
        $texts = $manifest['texts'] ?? [];

        foreach ($request->input('pages') as $pageItem) {
            $pageNum = (int) $pageItem['page_number'];

            // Process high-res page image
            if (!empty($pageItem['image'])) {
                $imgData = $this->decodeBase64Image($pageItem['image']);
                if ($imgData) {
                    Storage::disk('public')->put("{$dir}/page_{$pageNum}.webp", $imgData);
                    $cachedPages[] = $pageNum;
                }
            }

            // Process thumbnail image
            if (!empty($pageItem['thumb'])) {
                $thumbData = $this->decodeBase64Image($pageItem['thumb']);
                if ($thumbData) {
                    Storage::disk('public')->put("{$dir}/thumb_{$pageNum}.webp", $thumbData);
                }
            }

            // Save page text for AI
            if (isset($pageItem['text'])) {
                $texts[$pageNum] = trim($pageItem['text']);
            }
        }

        $cachedPages = array_values(array_unique($cachedPages));
        sort($cachedPages);

        $isComplete = count($cachedPages) >= $totalPages;

        $manifest = [
            'modul_id' => $e_modul->id,
            'total_pages' => $totalPages,
            'page_width' => $pageWidth,
            'page_height' => $pageHeight,
            'cached_pages' => $cachedPages,
            'is_complete' => $isComplete,
            'texts' => $texts,
            'updated_at' => now()->toIso8601String(),
        ];

        Storage::disk('public')->put($manifestPath, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // Update total_pages on model if not set
        if ($e_modul->total_pages !== $totalPages) {
            $e_modul->total_pages = $totalPages;
            $e_modul->save();
        }

        return response()->json([
            'success' => true,
            'cached_count' => count($cachedPages),
            'total_pages' => $totalPages,
            'is_complete' => $isComplete,
        ]);
    }

    /**
     * Decode base64 image data (data:image/webp;base64,...).
     */
    protected function decodeBase64Image(string $base64String): ?string
    {
        if (str_contains($base64String, ',')) {
            $base64String = explode(',', $base64String)[1];
        }
        $data = base64_decode($base64String, true);
        return $data !== false ? $data : null;
    }

    /**
     * Clear pre-rendered page cache for an e-modul.
     */
    public function clearCache(EModul $e_modul): JsonResponse
    {
        Gate::authorize('emodul.edit');

        $dir = "emoduls/cache/{$e_modul->id}";
        if (Storage::disk('public')->exists($dir)) {
            Storage::disk('public')->deleteDirectory($dir);
        }

        return response()->json([
            'success' => true,
            'message' => 'Cache halaman e-modul berhasil dibersihkan! Modul akan dirender ulang saat dibuka berikutnya.',
        ]);
    }
}
