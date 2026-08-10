<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogCategoryController extends Controller
{
    /**
     * Display a listing of blog categories.
     */
    public function index()
    {
        $categories = BlogCategory::withCount('posts')->get();
        return view('admin.posts.categories', compact('categories'));
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_categories,slug',
            'description' => 'nullable|string',
            'color' => 'required|string|max:20',
        ]);

        $validated['slug'] = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']);

        BlogCategory::create($validated);

        return redirect()->route('admin.blog-categories.index')->with('success', 'Category created successfully!');
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, BlogCategory $blogCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blog_categories,slug,' . $blogCategory->id,
            'description' => 'nullable|string',
            'color' => 'required|string|max:20',
        ]);

        $validated['slug'] = Str::slug($validated['slug']);

        $blogCategory->update($validated);

        return redirect()->route('admin.blog-categories.index')->with('success', 'Category updated successfully!');
    }

    /**
     * Remove the specified category.
     */
    public function destroy(BlogCategory $blogCategory)
    {
        if ($blogCategory->posts()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete category that contains blog posts.');
        }

        $blogCategory->delete();

        return redirect()->route('admin.blog-categories.index')->with('success', 'Category deleted successfully!');
    }
}
