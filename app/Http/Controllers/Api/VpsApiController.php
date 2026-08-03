<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VpsMetricsLog;
use App\Models\VpsServer;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class VpsApiController extends Controller
{
    /**
     * Handle incoming ping metrics from VPS Agent.
     */
    public function ping(Request $request)
    {
        $token = $request->bearerToken() ?? $request->input('token') ?? $request->header('X-VPS-Token');

        if (!$token) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized: Missing API token'], 401);
        }

        $server = VpsServer::where('auth_token', $token)->where('is_active', true)->first();

        if (!$server) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized: Invalid or inactive token'], 403);
        }

        $validated = $request->validate([
            'cpu_usage'     => 'required|numeric|min:0',
            'ram_used_mb'   => 'required|numeric|min:0',
            'ram_total_mb'  => 'required|numeric|min:1',
            'disk_used_gb'  => 'required|numeric|min:0',
            'disk_total_gb' => 'required|numeric|min:0.1',
            'load_avg_1m'   => 'nullable|numeric',
            'uptime_seconds'=> 'nullable|numeric',
            'os_info'       => 'nullable|string|max:255',
            'cpu_cores'     => 'nullable|integer',
            'ip_address'    => 'nullable|string|max:45',
        ]);

        $ramPercent = ($validated['ram_used_mb'] / $validated['ram_total_mb']) * 100;
        $diskPercent = ($validated['disk_used_gb'] / $validated['disk_total_gb']) * 100;
        $cpuUsage = $validated['cpu_usage'];

        // Determine status
        $status = 'online';
        if ($ramPercent > 90 || $diskPercent > 90 || $cpuUsage > 90) {
            $status = 'warning';
        }

        // Update server details
        $server->update([
            'last_ping_at' => Carbon::now(),
            'status'       => $status,
            'ip_address'   => $validated['ip_address'] ?? $server->ip_address ?? $request->ip(),
            'os_info'      => $validated['os_info'] ?? $server->os_info,
            'cpu_cores'    => $validated['cpu_cores'] ?? $server->cpu_cores,
        ]);

        // Create metrics log record
        VpsMetricsLog::create([
            'vps_server_id' => $server->id,
            'cpu_usage'     => round($cpuUsage, 2),
            'ram_used_mb'   => round($validated['ram_used_mb']),
            'ram_total_mb'  => round($validated['ram_total_mb']),
            'disk_used_gb'  => round($validated['disk_used_gb'], 2),
            'disk_total_gb' => round($validated['disk_total_gb'], 2),
            'load_avg_1m'   => round($validated['load_avg_1m'] ?? 0, 2),
            'uptime_seconds'=> (int) ($validated['uptime_seconds'] ?? 0),
            'created_at'    => Carbon::now(),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Metrics recorded successfully',
            'server'  => $server->name,
        ]);
    }
}
