<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EModul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EModulController extends Controller
{
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
}
