<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogPostController extends Controller
{
    /**
     * Display a listing of the blog posts.
     */
    public function index(Request $request)
    {
        $query = BlogPost::with(['category', 'author']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', "%{$search}%");
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $posts = $query->latest()->paginate(10);
        $categories = BlogCategory::all();

        $stats = [
            'total' => BlogPost::count(),
            'published' => BlogPost::where('status', 'published')->count(),
            'draft' => BlogPost::where('status', 'draft')->count(),
            'featured' => BlogPost::where('featured', true)->count(),
        ];

        return view('admin.posts.index', compact('posts', 'categories', 'stats'));
    }

    /**
     * Show the form for creating a new blog post.
     */
    public function create()
    {
        $categories = BlogCategory::all();
        return view('admin.posts.create', compact('categories'));
    }

    /**
     * Store a newly created blog post in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_posts,slug',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'cover_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'cover_image' => 'nullable|string',
            'category_id' => 'nullable|exists:blog_categories,id',
            'status' => 'required|in:draft,published',
            'featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        if ($request->hasFile('cover_file')) {
            $path = $request->file('cover_file')->store('posts', 'public');
            $validated['cover_image'] = asset('storage/' . $path);
        }

        $validated['slug'] = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        $validated['author_id'] = auth()->id();
        $validated['featured'] = $request->has('featured');

        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        BlogPost::create($validated);

        return redirect()->route('admin.posts.index')->with('success', 'Blog post created successfully!');
    }

    /**
     * Show the form for editing the specified blog post.
     */
    public function edit(BlogPost $post)
    {
        $categories = BlogCategory::all();
        return view('admin.posts.edit', compact('post', 'categories'));
    }

    /**
     * Update the specified blog post in storage.
     */
    public function update(Request $request, BlogPost $post)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blog_posts,slug,' . $post->id,
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'cover_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'cover_image' => 'nullable|string',
            'category_id' => 'nullable|exists:blog_categories,id',
            'status' => 'required|in:draft,published',
            'featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        if ($request->hasFile('cover_file')) {
            $path = $request->file('cover_file')->store('posts', 'public');
            $validated['cover_image'] = asset('storage/' . $path);
        }

        $validated['slug'] = Str::slug($validated['slug']);
        $validated['featured'] = $request->has('featured');

        if ($validated['status'] === 'published' && empty($post->published_at)) {
            $validated['published_at'] = now();
        }

        $post->update($validated);

        return redirect()->route('admin.posts.index')->with('success', 'Blog post updated successfully!');
    }

    /**
     * Remove the specified blog post from storage.
     */
    public function destroy(BlogPost $post)
    {
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', 'Blog post deleted successfully!');
    }

    /**
     * Toggle publish status.
     */
    public function togglePublish(BlogPost $post)
    {
        $newStatus = $post->status === 'published' ? 'draft' : 'published';
        $updateData = ['status' => $newStatus];

        if ($newStatus === 'published' && empty($post->published_at)) {
            $updateData['published_at'] = now();
        }

        $post->update($updateData);

        return redirect()->back()->with('success', "Post status updated to " . ucfirst($newStatus));
    }

    /**
     * Toggle featured status.
     */
    public function toggleFeatured(BlogPost $post)
    {
        $post->update([
            'featured' => !$post->featured
        ]);

        return redirect()->back()->with('success', "Post featured status updated");
    }

    /**
     * Generate blog post content & Meta SEO using AI (9Router Gateway SSE Stream).
     */
    public function generateAiArticle(Request $request, \App\Services\NineRouterService $nineRouterService)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:1000',
        ]);

        $title = trim($request->input('title'));
        $excerpt = trim($request->input('excerpt', ''));

        $systemPrompt = "Anda adalah seorang pakar penulisan artikel blog dan spesialis SEO profesional. Tugas Anda adalah menulis artikel blog mendalam berstruktur Markdown rapi (gunakan ## untuk subjudul, **bold** untuk penekanan, dan ```bash untuk perintah terminal) dan membuat Meta Title dan Meta Keywords yang dioptimalkan untuk Google Search.

Format jawaban WAJIB hanya dalam format JSON valid berikut (tanpa blok ```json):
{
  \"content\": \"Isi artikel lengkap format Markdown (## Subjudul, **teks bold**, ```bash kode)...\",
  \"meta_title\": \"Meta Title yang menarik dan ter-SEO (max 60 karakter)\",
  \"meta_keywords\": \"kata kunci 1, kata kunci 2, kata kunci 3, kata kunci 4\"
}";

        $userPrompt = "Judul Artikel: {$title}";
        if (!empty($excerpt)) {
            $userPrompt .= "\nRingkasan / Konteks Tambahan: {$excerpt}";
        }
        $userPrompt .= "\n\nTuliskan artikel lengkap dan buatkan Meta SEO sekarang dalam format JSON Bahasa Indonesia.";

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt]
        ];

        $model = $nineRouterService->getDefaultModel();

        return response()->stream(function () use ($nineRouterService, $messages, $model) {
            @ini_set('output_buffering', 'off');
            @ini_set('zlib.output_compression', false);
            @ini_set('implicit_flush', 1);
            for ($i = 0; $i < ob_get_level(); $i++) {
                ob_end_flush();
            }
            ob_implicit_flush(true);

            // Send initial ping to keep Cloudflare connection active immediately
            echo "data: " . json_encode(['status' => 'connected']) . "\n\n";
            if (ob_get_level() > 0) ob_flush();
            flush();

            try {
                $nineRouterService->streamChatCompletions(
                    $messages,
                    $model,
                    function ($chunk) {
                        echo "data: " . json_encode(['chunk' => $chunk]) . "\n\n";
                        if (ob_get_level() > 0) ob_flush();
                        flush();
                    }
                );

                echo "data: [DONE]\n\n";
                if (ob_get_level() > 0) ob_flush();
                flush();
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('generateAiArticle SSE Error: ' . $e->getMessage());
                echo "data: " . json_encode(['error' => $e->getMessage()]) . "\n\n";
                echo "data: [DONE]\n\n";
                if (ob_get_level() > 0) ob_flush();
                flush();
            }
        }, 200, [
            'Cache-Control'     => 'no-cache, must-revalidate',
            'Content-Type'      => 'text/event-stream',
            'X-Accel-Buffering' => 'no',
            'Connection'        => 'keep-alive',
        ]);
    }
}
