<x-admin-layout title="VPS Server Monitoring" breadcrumb="Infrastructure / VPS Monitoring">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:16px;">

        <div>
            <h1 style="font-size:22px; font-weight:800; color:var(--text-primary); letter-spacing:-0.5px; display:flex; align-items:center; gap:10px;">
                <span style="display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; border-radius:10px; background:rgba(99, 102, 241, 0.15); color:#818cf8;">
                    🖥️
                </span>
                VPS Server Monitoring
            </h1>
            <p style="font-size:13px; color:var(--text-muted); margin-top:4px;">
                Real-time monitoring of Linux VPS resources (CPU, RAM, Disk & System Load)
            </p>
        </div>

        <div style="display:flex; align-items:center; gap:10px;">
            <form action="{{ route('admin.vps-pin.lock') }}" method="POST" style="margin:0;">
                @csrf
                <button type="submit" class="btn btn-ghost" style="padding:9px 14px; font-size:12.5px; border:1px solid rgba(255,255,255,0.12); color:var(--text-muted); display:inline-flex; align-items:center; gap:6px;" title="Kunci kembali akses VPS Monitoring">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    Lock VPS
                </button>
            </form>

            <a href="{{ route('admin.vps.create') }}" class="topbar-btn topbar-btn-primary" style="text-decoration:none; padding:9px 16px;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add VPS Server
            </a>
        </div>
    </div>



    <!-- Stats Grid -->
    <div class="stats-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom:24px;">
        <div class="stat-card accent">
            <div class="stat-top">
                <div class="stat-label">Total Servers</div>
                <div class="stat-icon accent">🖥️</div>
            </div>
            <div class="stat-value">{{ $stats['total'] }}</div>
        </div>
        <div class="stat-card green">
            <div class="stat-top">
                <div class="stat-label">Online (Healthy)</div>
                <div class="stat-icon green">🟢</div>
            </div>
            <div class="stat-value">{{ $stats['online'] }}</div>
        </div>
        <div class="stat-card amber">
            <div class="stat-top">
                <div class="stat-label">High Resource Warning</div>
                <div class="stat-icon amber">⚠️</div>
            </div>
            <div class="stat-value">{{ $stats['warning'] }}</div>
        </div>
        <div class="stat-card red">
            <div class="stat-top">
                <div class="stat-label">Offline / No Ping</div>
                <div class="stat-icon red">🔴</div>
            </div>
            <div class="stat-value">{{ $stats['offline'] }}</div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card">
        <x-datatable id="vpsDataTable" title="Monitored VPS Servers" subtitle="Metrics pushed automatically via Agent Script" search-placeholder="Search server by name or IP address..." :per-page-options="[10, 20, 50, 'all']" :default-per-page="10">
            <x-slot:actions>
                <a href="{{ route('admin.vps.create') }}" class="topbar-btn topbar-btn-primary" style="padding:6px 12px; font-size:12px; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    New VPS
                </a>
            </x-slot:actions>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:60px" data-sortable="true">No</th>
                        <th data-sortable="true">Server & IP</th>
                        <th data-sortable="true">Status</th>
                        <th data-sortable="true">CPU Usage</th>
                        <th data-sortable="true">RAM Usage</th>
                        <th data-sortable="true">Disk Storage</th>
                        <th data-sortable="true">Last Ping</th>
                        <th style="text-align:right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($servers as $index => $server)
                    @php
                        $log = $server->latestLog;
                        $ramPct = $log && $log->ram_total_mb > 0 ? round(($log->ram_used_mb / $log->ram_total_mb) * 100) : 0;
                        $diskPct = $log && $log->disk_total_gb > 0 ? round(($log->disk_used_gb / $log->disk_total_gb) * 100) : 0;
                    @endphp
                    <tr>
                        <td>
                            <span class="datatable-row-index" style="font-family:'JetBrains Mono',monospace; font-weight:700; color:var(--text-primary); font-size:13px;">{{ $index + 1 }}</span>
                        </td>
                        <td>
                            <div>
                                <a href="{{ route('admin.vps.show', $server->id) }}" style="font-weight:700; color:var(--text-primary); font-size:14px; text-decoration:none;">
                                    {{ $server->name }}
                                </a>
                                <div style="font-size:12px; color:var(--text-secondary); font-family:'JetBrains Mono',monospace;">
                                    {{ $server->ip_address ?? 'No IP registered' }}
                                    @if($server->os_info) • {{ $server->os_info }} @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($server->status === 'online')
                                <span style="background:rgba(16,185,129,0.15); color:#10b981; border:1px solid rgba(16,185,129,0.3); padding:4px 10px; border-radius:12px; font-size:12px; font-weight:700; display:inline-flex; align-items:center; gap:6px;">
                                    <span style="width:6px; height:6px; border-radius:50%; background:#10b981; box-shadow:0 0 6px #10b981;"></span>
                                    ONLINE
                                </span>
                            @elseif($server->status === 'warning')
                                <span style="background:rgba(245,158,11,0.15); color:#fbbf24; border:1px solid rgba(245,158,11,0.3); padding:4px 10px; border-radius:12px; font-size:12px; font-weight:700; display:inline-flex; align-items:center; gap:6px;">
                                    <span style="width:6px; height:6px; border-radius:50%; background:#fbbf24; box-shadow:0 0 6px #fbbf24;"></span>
                                    WARNING
                                </span>
                            @else
                                <span style="background:rgba(239,68,68,0.15); color:#ef4444; border:1px solid rgba(239,68,68,0.3); padding:4px 10px; border-radius:12px; font-size:12px; font-weight:700; display:inline-flex; align-items:center; gap:6px;">
                                    <span style="width:6px; height:6px; border-radius:50%; background:#ef4444; box-shadow:0 0 6px #ef4444;"></span>
                                    OFFLINE
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($log)
                                <div style="display:flex; flex-direction:column; gap:4px; min-width:90px;">
                                    <div style="display:flex; justify-content:space-between; font-size:11.5px; font-family:'JetBrains Mono',monospace;">
                                        <span>{{ $log->cpu_usage }}%</span>
                                    </div>
                                    <div style="width:100%; height:6px; background:rgba(255,255,255,0.08); border-radius:4px; overflow:hidden;">
                                        <div style="width:{{ min(100, $log->cpu_usage) }}%; height:100%; background:{{ $log->cpu_usage > 90 ? '#ef4444' : ($log->cpu_usage > 75 ? '#fbbf24' : '#10b981') }}; border-radius:4px;"></div>
                                    </div>
                                </div>
                            @else
                                <span style="color:var(--text-muted); font-size:12px;">—</span>
                            @endif
                        </td>
                        <td>
                            @if($log)
                                <div style="display:flex; flex-direction:column; gap:4px; min-width:110px;">
                                    <div style="display:flex; justify-content:space-between; font-size:11.5px; font-family:'JetBrains Mono',monospace;">
                                        <span>{{ round($log->ram_used_mb / 1024, 1) }}/{{ round($log->ram_total_mb / 1024, 1) }} GB</span>
                                        <span style="color:var(--text-secondary);">{{ $ramPct }}%</span>
                                    </div>
                                    <div style="width:100%; height:6px; background:rgba(255,255,255,0.08); border-radius:4px; overflow:hidden;">
                                        <div style="width:{{ min(100, $ramPct) }}%; height:100%; background:{{ $ramPct > 90 ? '#ef4444' : ($ramPct > 75 ? '#fbbf24' : '#6366f1') }}; border-radius:4px;"></div>
                                    </div>
                                </div>
                            @else
                                <span style="color:var(--text-muted); font-size:12px;">—</span>
                            @endif
                        </td>
                        <td>
                            @if($log)
                                <div style="display:flex; flex-direction:column; gap:4px; min-width:110px;">
                                    <div style="display:flex; justify-content:space-between; font-size:11.5px; font-family:'JetBrains Mono',monospace;">
                                        <span>{{ $log->disk_used_gb }}/{{ $log->disk_total_gb }} GB</span>
                                        <span style="color:var(--text-secondary);">{{ $diskPct }}%</span>
                                    </div>
                                    <div style="width:100%; height:6px; background:rgba(255,255,255,0.08); border-radius:4px; overflow:hidden;">
                                        <div style="width:{{ min(100, $diskPct) }}%; height:100%; background:{{ $diskPct > 90 ? '#ef4444' : ($diskPct > 75 ? '#fbbf24' : '#14b8a6') }}; border-radius:4px;"></div>
                                    </div>
                                </div>
                            @else
                                <span style="color:var(--text-muted); font-size:12px;">—</span>
                            @endif
                        </td>
                        <td>
                            @if($server->last_ping_at)
                                <span style="font-size:12px; color:var(--text-secondary);" title="{{ $server->last_ping_at->format('Y-m-d H:i:s') }}">
                                    {{ $server->last_ping_at->diffForHumans() }}
                                </span>
                            @else
                                <span style="color:var(--text-muted); font-size:12px;">Never</span>
                            @endif
                        </td>
                        <td style="text-align:right">
                            <div style="display:inline-flex; gap:6px; align-items:center;">
                                <a href="{{ route('admin.vps.show', $server->id) }}" class="btn btn-ghost" title="View Detail & Analytics" style="padding:4px 8px; font-size:11.5px;">
                                    📊
                                </a>

                                <a href="{{ route('admin.vps.edit', $server->id) }}" class="btn btn-ghost" title="Edit VPS" style="padding:4px 8px; font-size:11.5px;">
                                    ✏️
                                </a>

                                <form action="{{ route('admin.vps.destroy', $server->id) }}" method="POST" class="delete-form" style="display:inline;" data-confirm-title="Hapus VPS '{{ $server->name }}'?" data-confirm-text="Semua riwayat log metrik untuk VPS ini akan dihapus permanen!">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" title="Delete VPS" style="padding:4px 8px; font-size:11.5px;">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align:center; padding:36px; color:var(--text-muted);">
                            No VPS servers monitored yet. <a href="{{ route('admin.vps.create') }}" style="color:var(--accent);">Add your first Linux VPS.</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </x-datatable>
    </div>
</x-admin-layout>
