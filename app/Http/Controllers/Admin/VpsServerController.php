<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VpsMetricsLog;
use App\Models\VpsServer;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class VpsServerController extends Controller
{
    /**
     * Display a listing of VPS servers.
     */
    public function index()
    {
        // Automatically mark servers as offline if last_ping_at is older than (check_interval * 2) minutes
        $servers = VpsServer::with('latestLog')->latest()->get();

        foreach ($servers as $server) {
            if ($server->is_active && $server->last_ping_at) {
                $threshold = Carbon::now()->subMinutes($server->check_interval * 2);
                if ($server->last_ping_at->lt($threshold) && $server->status !== 'offline') {
                    $server->update(['status' => 'offline']);
                }
            } elseif ($server->is_active && !$server->last_ping_at) {
                if ($server->status !== 'offline') {
                    $server->update(['status' => 'offline']);
                }
            }
        }

        $stats = [
            'total' => $servers->count(),
            'online' => $servers->where('status', 'online')->count(),
            'warning' => $servers->where('status', 'warning')->count(),
            'offline' => $servers->where('status', 'offline')->count(),
        ];

        return view('admin.vps.index', compact('servers', 'stats'));
    }

    /**
     * Show form for creating a new VPS server.
     */
    public function create()
    {
        return view('admin.vps.create');
    }

    /**
     * Store a newly created VPS server in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'nullable|string|max:45',
            'check_interval' => 'required|integer|min:1|max:60',
        ]);

        $token = 'vps_' . Str::random(36);

        $server = VpsServer::create([
            'name' => $validated['name'],
            'ip_address' => $validated['ip_address'],
            'check_interval' => $validated['check_interval'],
            'auth_token' => $token,
            'status' => 'offline',
            'is_active' => true,
        ]);

        return redirect()->route('admin.vps.show', $server)
            ->with('success', 'VPS Server registered successfully! Copy the installation command below to link your VPS.');
    }

    /**
     * Display the specified VPS server details & historical metrics.
     */
    public function show(VpsServer $vps)
    {
        $vps->load('latestLog');

        // Fetch logs for the last 24 hours (or last 60 records)
        $logs = VpsMetricsLog::where('vps_server_id', $vps->id)
            ->orderBy('created_at', 'desc')
            ->take(60)
            ->get()
            ->reverse()
            ->values();

        $chartData = [
            'labels' => $logs->map(fn($l) => Carbon::parse($l->created_at)->format('H:i')),
            'cpu' => $logs->pluck('cpu_usage'),
            'ram_used' => $logs->map(fn($l) => round($l->ram_used_mb / 1024, 2)), // GB
            'ram_total' => $logs->map(fn($l) => round($l->ram_total_mb / 1024, 2)), // GB
            'disk_used' => $logs->pluck('disk_used_gb'),
            'disk_total' => $logs->pluck('disk_total_gb'),
            'load' => $logs->pluck('load_avg_1m'),
        ];

        return view('admin.vps.show', compact('vps', 'chartData', 'logs'));
    }

    /**
     * Show form for editing VPS server settings.
     */
    public function edit(VpsServer $vps)
    {
        return view('admin.vps.edit', compact('vps'));
    }

    /**
     * Update the specified VPS server in storage.
     */
    public function update(Request $request, VpsServer $vps)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'nullable|string|max:45',
            'check_interval' => 'required|integer|min:1|max:60',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $vps->update($validated);

        return redirect()->route('admin.vps.show', $vps)->with('success', 'VPS Server updated successfully!');
    }

    /**
     * Remove the specified VPS server from storage.
     */
    public function destroy(VpsServer $vps)
    {
        $vps->delete();

        return redirect()->route('admin.vps.index')->with('success', 'VPS Server removed successfully!');
    }

    /**
     * Serve the dynamic vps-agent.sh installer script for a given server.
     */
    public function installScript(Request $request, $token)
    {
        $server = VpsServer::where('auth_token', $token)->firstOrFail();
        $baseUrl = url('/');

        $script = view('scripts.vps-agent', compact('server', 'baseUrl'))->render();

        return response($script, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
        ]);
    }
}
