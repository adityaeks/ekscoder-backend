<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the projects.
     */
    public function index()
    {
        $projects = Project::orderBy('order', 'asc')->get();

        $stats = [
            'total' => $projects->count(),
            'active' => $projects->where('is_active', true)->count(),
            'featured' => $projects->where('featured', true)->count(),
        ];

        return view('admin.projects.index', compact('projects', 'stats'));
    }

    /**
     * Show the form for creating a new project.
     */
    public function create()
    {
        return view('admin.projects.create');
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|string|unique:projects,id|max:255',
            'number' => 'required|string|max:10',
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'year' => 'required|string|max:10',
            'description' => 'required|string',
            'technologies' => 'required',
            'image_bg' => 'required|string',
            'accent_color' => 'required|string',
            'link' => 'nullable|url',
            'featured' => 'boolean',
            'is_active' => 'boolean',
            'order' => 'integer',
        ]);

        if (is_string($validated['technologies'])) {
            $validated['technologies'] = array_map('trim', explode(',', $validated['technologies']));
        }

        $validated['featured'] = $request->has('featured');
        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = $validated['order'] ?? 0;

        Project::create($validated);

        return redirect()->route('admin.projects.index')->with('success', 'Project created successfully!');
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit(Project $project)
    {
        return view('admin.projects.edit', compact('project'));
    }

    /**
     * Update the specified project in storage.
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'number' => 'required|string|max:10',
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'year' => 'required|string|max:10',
            'description' => 'required|string',
            'technologies' => 'required',
            'image_bg' => 'required|string',
            'accent_color' => 'required|string',
            'link' => 'nullable|url',
            'order' => 'integer',
        ]);

        if (is_string($validated['technologies'])) {
            $validated['technologies'] = array_map('trim', explode(',', $validated['technologies']));
        }

        $validated['featured'] = $request->has('featured');
        $validated['is_active'] = $request->has('is_active');

        $project->update($validated);

        return redirect()->route('admin.projects.index')->with('success', 'Project updated successfully!');
    }

    /**
     * Remove the specified project from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('admin.projects.index')->with('success', 'Project deleted successfully!');
    }

    /**
     * Toggle active status of a project.
     */
    public function toggleActive(Project $project)
    {
        $project->update([
            'is_active' => !$project->is_active
        ]);

        return redirect()->back()->with('success', "Project status updated to " . ($project->is_active ? 'Active' : 'Inactive'));
    }
}
