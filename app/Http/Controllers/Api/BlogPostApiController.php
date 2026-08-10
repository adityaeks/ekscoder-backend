<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogCategoryResource;
use App\Http\Resources\BlogPostResource;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlogPostApiController extends Controller
{
    /**
     * Display a listing of published blog posts.
     */
    public function index(Request $request): JsonResponse
    {
        $query = BlogPost::with(['category', 'author'])
            ->published();

        // Filter by Search Keyword
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Filter by Category Slug or ID
        if ($request->filled('category')) {
            $categoryParam = $request->input('category');
            $query->whereHas('category', function ($q) use ($categoryParam) {
                if (is_numeric($categoryParam)) {
                    $q->where('id', $categoryParam);
                } else {
                    $q->where('slug', $categoryParam);
                }
            });
        }

        // Filter by Featured Status
        if ($request->boolean('featured')) {
            $query->featured();
        }

        // Sorting
        $sort = $request->input('sort', 'latest');
        match ($sort) {
            'popular' => $query->orderBy('views_count', 'desc'),
            'oldest'  => $query->orderBy('published_at', 'asc'),
            default   => $query->orderBy('published_at', 'desc'),
        };

        $perPage = min((int) $request->input('per_page', 10), 50);
        $posts = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => BlogPostResource::collection($posts),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ],
        ]);
    }

    /**
     * Display details of a single post by slug.
     */
    public function show(string $slug): JsonResponse
    {
        $post = BlogPost::with(['category', 'author'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment view count
        $post->increment('views_count');

        // Fetch related posts from same category
        $relatedPosts = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->where('category_id', $post->category_id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => new BlogPostResource($post),
            'related' => BlogPostResource::collection($relatedPosts),
        ]);
    }

    /**
     * Get featured posts for widgets or home page.
     */
    public function featured(): JsonResponse
    {
        $posts = BlogPost::with(['category', 'author'])
            ->published()
            ->featured()
            ->latest('published_at')
            ->limit(6)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => BlogPostResource::collection($posts),
        ]);
    }

    /**
     * List all categories with active post count.
     */
    public function categories(): JsonResponse
    {
        $categories = BlogCategory::withCount(['posts as published_posts_count' => function ($q) {
            $q->where('status', 'published');
        }])->get();

        return response()->json([
            'status' => 'success',
            'data' => BlogCategoryResource::collection($categories),
        ]);
    }
}
