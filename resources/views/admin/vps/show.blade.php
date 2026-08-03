<x-admin-layout title="VPS Monitoring Analytics: {{ $vps->name }}" breadcrumb="Detailed system health, resource consumption, and historical trends">
    <x-slot name="topbarAction">
        <div style="display:flex; gap:10px; align-items:center;">
            <form action="{{ route('admin.vps-pin.lock') }}" method="POST" style="margin:0;">
                @csrf
                <button type="submit" class="btn btn-ghost" style="padding:6px 14px; font-size:13px; border:1px solid rgba(255,255,255,0.12); color:var(--text-muted); display:inline-flex; align-items:center; gap:6px;" title="Kunci kembali akses VPS Monitoring">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    Lock VPS
                </button>
            </form>
            <a href="{{ route('admin.vps.edit', $vps->id) }}" class="btn btn-ghost" style="padding:6px 14px; font-size:13px; text-decoration:none;">
                ✏️ Edit Settings
            </a>
            <a href="{{ route('admin.vps.index') }}" class="btn btn-ghost" style="padding:6px 14px; font-size:13px; text-decoration:none;">
                ← Back to List
            </a>
        </div>
    </x-slot>


    @php
        $latest = $vps->latestLog;
        $ramPct = $latest && $latest->ram_total_mb > 0 ? round(($latest->ram_used_mb / $latest->ram_total_mb) * 100, 1) : 0;
        $diskPct = $latest && $latest->disk_total_gb > 0 ? round(($latest->disk_used_gb / $latest->disk_total_gb) * 100, 1) : 0;
        
        $installCmd = "curl -sSL " . url("/vps-agent/{$vps->auth_token}/install.sh") . " | bash";
    @endphp

    <!-- Installation Command Banner -->
    <div class="card" style="padding: 20px; margin-bottom: 24px; background: linear-gradient(135deg, rgba(99,102,241,0.08) 0%, rgba(139,92,246,0.08) 100%); border: 1px solid rgba(99,102,241,0.25);">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <div>
                <h4 style="margin:0 0 4px; font-size:14px; font-weight:700; color:var(--text-primary); display:flex; align-items:center; gap:8px;">
                    🚀 Agent One-Line Installation Command
                </h4>
                <p style="margin:0; font-size:12.5px; color:var(--text-secondary);">Run this command inside your target Linux VPS terminal to enable automated monitoring:</p>
            </div>
            <button onclick="copyInstallCmd()" class="topbar-btn topbar-btn-primary" id="copyBtn" style="padding:6px 14px; font-size:12px;">
                📋 Copy Command
            </button>
        </div>
        <div style="margin-top:12px; background:rgba(0,0,0,0.4); border:1px solid rgba(255,255,255,0.1); border-radius:8px; padding:10px 14px; display:flex; align-items:center; justify-content:space-between;">
            <code id="cmdText" style="font-family:'JetBrains Mono',monospace; font-size:13px; color:#a5b4fc; word-break:break-all;">{{ $installCmd }}</code>
        </div>
    </div>

    <!-- Stats & Live Gauges -->
    <div class="stats-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 24px;">
        <!-- Status Card -->
        <div class="stat-card {{ $vps->status === 'online' ? 'green' : ($vps->status === 'warning' ? 'amber' : 'red') }}">
            <div class="stat-top">
                <div class="stat-label">Server Status</div>
                <div class="stat-icon {{ $vps->status === 'online' ? 'green' : ($vps->status === 'warning' ? 'amber' : 'red') }}">
                    {{ $vps->status === 'online' ? '🟢' : ($vps->status === 'warning' ? '⚠️' : '🔴') }}
                </div>
            </div>
            <div class="stat-value" style="font-size:20px; text-transform:uppercase;">
                {{ $vps->status }}
            </div>
            <div style="font-size:11.5px; color:var(--text-secondary); margin-top:4px;">
                Last Ping: {{ $vps->last_ping_at ? $vps->last_ping_at->diffForHumans() : 'Never' }}
            </div>
        </div>

        <!-- CPU Gauge -->
        <div class="stat-card accent">
            <div class="stat-top">
                <div class="stat-label">CPU Usage</div>
                <div class="stat-icon accent">⚡</div>
            </div>
            <div class="stat-value">{{ $latest ? $latest->cpu_usage : 0 }}%</div>
            <div style="font-size:11.5px; color:var(--text-secondary); margin-top:4px;">
                Cores: {{ $vps->cpu_cores ?? 'Auto' }} | Load 1m: {{ $latest ? $latest->load_avg_1m : 0 }}
            </div>
        </div>

        <!-- RAM Gauge -->
        <div class="stat-card indigo">
            <div class="stat-top">
                <div class="stat-label">RAM Usage</div>
                <div class="stat-icon indigo">💾</div>
            </div>
            <div class="stat-value">{{ $ramPct }}%</div>
            <div style="font-size:11.5px; color:var(--text-secondary); margin-top:4px;">
                {{ $latest ? round($latest->ram_used_mb / 1024, 2) : 0 }} / {{ $latest ? round($latest->ram_total_mb / 1024, 2) : 0 }} GB
            </div>
        </div>

        <!-- Disk Gauge -->
        <div class="stat-card teal">
            <div class="stat-top">
                <div class="stat-label">Disk Storage</div>
                <div class="stat-icon teal">💽</div>
            </div>
            <div class="stat-value">{{ $diskPct }}%</div>
            <div style="font-size:11.5px; color:var(--text-secondary); margin-top:4px;">
                {{ $latest ? $latest->disk_used_gb : 0 }} / {{ $latest ? $latest->disk_total_gb : 0 }} GB
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-bottom:24px;">
        <!-- Chart 1: CPU & System Load -->
        <div class="card" style="padding:20px;">
            <h4 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0 0 16px; display:flex; align-items:center; gap:8px;">
                📊 CPU Usage (%) & Load Average
            </h4>
            <div style="position:relative; height:260px;">
                <canvas id="cpuChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: RAM & Disk Usage -->
        <div class="card" style="padding:20px;">
            <h4 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0 0 16px; display:flex; align-items:center; gap:8px;">
                📈 RAM Usage (GB) & Disk Used
            </h4>
            <div style="position:relative; height:260px;">
                <canvas id="memoryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- History Logs Table -->
    <div class="card">
        <x-datatable id="vpsLogsTable" title="Recent Metrics History Logs" subtitle="Showing last 60 automated metric ping logs" search-placeholder="Search log history..." :per-page-options="[10, 20, 50, 'all']" :default-per-page="10">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:60px">No</th>
                        <th data-sortable="true">Timestamp</th>
                        <th data-sortable="true">CPU (%)</th>
                        <th data-sortable="true">RAM (MB / GB)</th>
                        <th data-sortable="true">Disk (GB)</th>
                        <th data-sortable="true">Load Avg (1m)</th>
                        <th data-sortable="true">Uptime</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs->reverse()->values() as $index => $log)
                    <tr>
                        <td>
                            <span style="font-family:'JetBrains Mono',monospace; font-weight:700; color:var(--text-primary); font-size:13px;">{{ $index + 1 }}</span>
                        </td>
                        <td>
                            <span style="font-size:12px; color:var(--text-primary); font-family:'JetBrains Mono',monospace;">
                                {{ $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : '—' }}
                            </span>
                        </td>
                        <td>
                            <span style="font-family:'JetBrains Mono',monospace; font-size:13px; font-weight:600; color:{{ $log->cpu_usage > 90 ? '#ef4444' : ($log->cpu_usage > 75 ? '#fbbf24' : '#10b981') }};">
                                {{ $log->cpu_usage }}%
                            </span>
                        </td>
                        <td>
                            <span style="font-family:'JetBrains Mono',monospace; font-size:12.5px;">
                                {{ $log->ram_used_mb }} MB <span style="color:var(--text-secondary);">({{ round($log->ram_used_mb / 1024, 2) }} GB)</span>
                            </span>
                        </td>
                        <td>
                            <span style="font-family:'JetBrains Mono',monospace; font-size:12.5px;">
                                {{ $log->disk_used_gb }} / {{ $log->disk_total_gb }} GB
                            </span>
                        </td>
                        <td>
                            <span style="font-family:'JetBrains Mono',monospace; font-size:12.5px; font-weight:600; color:var(--text-primary);">
                                {{ $log->load_avg_1m }}
                            </span>
                        </td>
                        <td>
                            <span style="font-size:12px; color:var(--text-secondary);">
                                {{ (int)($log->uptime_seconds / 86400) }}d {{ (int)(($log->uptime_seconds % 86400) / 3600) }}h {{ (int)(($log->uptime_seconds % 3600) / 60) }}m
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center; padding:32px; color:var(--text-muted);">
                            No metric pings received yet. Run the installation script on your VPS to begin collecting metrics.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </x-datatable>
    </div>

    <!-- Chart.js Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function copyInstallCmd() {
            const cmdText = document.getElementById('cmdText').innerText;
            navigator.clipboard.writeText(cmdText).then(() => {
                const btn = document.getElementById('copyBtn');
                btn.innerText = '✅ Copied!';
                setTimeout(() => { btn.innerText = '📋 Copy Command'; }, 2500);
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            const chartLabels = {!! json_encode($chartData['labels']) !!};
            const cpuData = {!! json_encode($chartData['cpu']) !!};
            const loadData = {!! json_encode($chartData['load']) !!};
            const ramUsedData = {!! json_encode($chartData['ram_used']) !!};
            const diskUsedData = {!! json_encode($chartData['disk_used']) !!};

            // CPU Chart
            const ctxCpu = document.getElementById('cpuChart').getContext('2d');
            new Chart(ctxCpu, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [
                        {
                            label: 'CPU Usage (%)',
                            data: cpuData,
                            borderColor: '#818cf8',
                            backgroundColor: 'rgba(129, 140, 248, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.3,
                            pointRadius: 2
                        },
                        {
                            label: 'Load Average (1m)',
                            data: loadData,
                            borderColor: '#fbbf24',
                            borderWidth: 1.5,
                            borderDash: [4, 4],
                            fill: false,
                            tension: 0.3,
                            pointRadius: 0
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' } },
                        x: { grid: { display: false } }
                    },
                    plugins: { legend: { labels: { color: '#94a3b8' } } }
                }
            });

            // Memory & Disk Chart
            const ctxRam = document.getElementById('memoryChart').getContext('2d');
            new Chart(ctxRam, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [
                        {
                            label: 'RAM Used (GB)',
                            data: ramUsedData,
                            borderColor: '#a855f7',
                            backgroundColor: 'rgba(168, 85, 247, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.3,
                            pointRadius: 2
                        },
                        {
                            label: 'Disk Used (GB)',
                            data: diskUsedData,
                            borderColor: '#14b8a6',
                            borderWidth: 2,
                            fill: false,
                            tension: 0.3,
                            pointRadius: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' } },
                        x: { grid: { display: false } }
                    },
                    plugins: { legend: { labels: { color: '#94a3b8' } } }
                }
            });
        });
    </script>
</x-admin-layout>
