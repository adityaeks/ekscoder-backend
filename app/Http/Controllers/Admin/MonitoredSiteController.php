<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MonitoredSite;
use App\Services\SiteCheckerService;
use Illuminate\Http\Request;

class MonitoredSiteController extends Controller
{
    /**
     * Display a listing of the monitored sites.
     */
    public function index()
    {
        $sites = MonitoredSite::with(['logs' => function ($query) {
            $query->latest()->limit(10);
        }])->latest()->get();

        $stats = [
            'total' => $sites->count(),
            'up' => $sites->where('status', 'up')->count(),
            'down' => $sites->where('status', 'down')->count(),
            'avg_response_time' => (int) round($sites->where('status', 'up')->avg('last_response_time') ?? 0),
        ];

        return view('admin.sites.index', compact('sites', 'stats'));
    }

    /**
     * Show the form for creating a new monitored site.
     */
    public function create()
    {
        return view('admin.sites.create');
    }

    /**
     * Store a newly created monitored site in storage and check immediately.
     */
    public function store(Request $request, SiteCheckerService $checker)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'check_interval' => 'required|integer|min:1|max:1440',
        ]);

        $site = MonitoredSite::create([
            'name' => $validated['name'],
            'url' => $validated['url'],
            'check_interval' => $validated['check_interval'],
            'is_active' => true,
        ]);

        // Immediate Health Check
        $checker->check($site);

        return redirect()->route('admin.sites.index')->with('success', 'Website added and initial health check performed!');
    }

    /**
     * Show the form for editing the specified monitored site.
     */
    public function edit(MonitoredSite $site)
    {
        return view('admin.sites.edit', compact('site'));
    }

    /**
     * Update the specified monitored site in storage.
     */
    public function update(Request $request, MonitoredSite $site)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'check_interval' => 'required|integer|min:1|max:1440',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $site->update($validated);

        return redirect()->route('admin.sites.index')->with('success', 'Monitored website updated successfully!');
    }

    /**
     * Instant Manual Health Check Trigger.
     */
    public function check(MonitoredSite $site, SiteCheckerService $checker)
    {
        $checker->check($site);

        return back()->with('success', 'Health check completed for ' . $site->name . '! Status: ' . strtoupper($site->status));
    }

    /**
     * Remove the specified monitored site from storage.
     */
    public function destroy(MonitoredSite $site)
    {
        $site->delete();

        return redirect()->route('admin.sites.index')->with('success', 'Monitored website removed successfully!');
    }
}
