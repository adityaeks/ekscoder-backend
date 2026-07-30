<x-admin-layout title="Website Uptime & Health Monitoring" breadcrumb="Real-time monitoring of website availability, latency, and SSL certificate expiration">
    <x-slot name="topbarAction">
        @can('sites.create')
        <a href="{{ route('admin.sites.create') }}" class="topbar-btn topbar-btn-primary" style="text-decoration:none;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Monitored Site
        </a>
        @endcan
    </x-slot>

    <!-- Stats Grid -->
    <div class="stats-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom:24px;">
        <div class="stat-card accent">
            <div class="stat-top">
                <div class="stat-label">Total Monitored</div>
                <div class="stat-icon accent">🌐</div>
            </div>
            <div class="stat-value">{{ $stats['total'] }}</div>
        </div>
        <div class="stat-card green">
            <div class="stat-top">
                <div class="stat-label">Operational (UP)</div>
                <div class="stat-icon green">⚡</div>
            </div>
            <div class="stat-value">{{ $stats['up'] }}</div>
        </div>
        <div class="stat-card red">
            <div class="stat-top">
                <div class="stat-label">Downtime (DOWN)</div>
                <div class="stat-icon red">🚨</div>
            </div>
            <div class="stat-value">{{ $stats['down'] }}</div>
        </div>
        <div class="stat-card amber">
            <div class="stat-top">
                <div class="stat-label">Avg Response Time</div>
                <div class="stat-icon amber">⏱️</div>
            </div>
            <div class="stat-value">{{ $stats['avg_response_time'] }} <span style="font-size:14px; font-weight:500;">ms</span></div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card">
        <x-datatable id="sitesDataTable" title="Monitored Websites & APIs" subtitle="Automated background health checks every 5 minutes" search-placeholder="Search websites by name or URL..." :per-page-options="[10, 20, 50, 'all']" :default-per-page="10">
            <x-slot:actions>
                @can('sites.create')
                <a href="{{ route('admin.sites.create') }}" class="topbar-btn topbar-btn-primary" style="padding:6px 12px; font-size:12px; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    New Site
                </a>
                @endcan
            </x-slot:actions>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:60px" data-sortable="true">No</th>
                        <th data-sortable="true">Website & URL</th>
                        <th data-sortable="true">Uptime Status</th>
                        <th data-sortable="true">Response Time</th>
                        <th data-sortable="true">HTTP Code</th>
                        <th data-sortable="true">SSL Certificate</th>
                        <th data-sortable="true">Last Checked</th>
                        <th style="text-align:right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sites as $index => $site)
                    <tr>
                        <td>
                            <span class="datatable-row-index" style="font-family:'JetBrains Mono',monospace; font-weight:700; color:var(--text-primary); font-size:13px;">{{ $index + 1 }}</span>
                        </td>
                        <td>
                            <div>
                                <div style="font-weight:700; color:var(--text-primary); font-size:14px;">{{ $site->name }}</div>
                                <a href="{{ $site->url }}" target="_blank" style="font-size:12px; color:#818cf8; text-decoration:none; font-family:'JetBrains Mono',monospace;">
                                    {{ $site->url }} ↗
                                </a>
                            </div>
                        </td>
                        <td>
                            @if($site->status === 'up')
                                <span style="background:rgba(16,185,129,0.15); color:#10b981; border:1px solid rgba(16,185,129,0.3); padding:4px 10px; border-radius:12px; font-size:12px; font-weight:700; display:inline-flex; align-items:center; gap:6px;">
                                    <span style="width:6px; height:6px; border-radius:50%; background:#10b981; box-shadow:0 0 6px #10b981;"></span>
                                    UP
                                </span>
                            @elseif($site->status === 'down')
                                <span style="background:rgba(239,68,68,0.15); color:#ef4444; border:1px solid rgba(239,68,68,0.3); padding:4px 10px; border-radius:12px; font-size:12px; font-weight:700; display:inline-flex; align-items:center; gap:6px;">
                                    <span style="width:6px; height:6px; border-radius:50%; background:#ef4444; box-shadow:0 0 6px #ef4444;"></span>
                                    DOWN
                                </span>
                            @else
                                <span style="background:rgba(245,158,11,0.15); color:#fbbf24; border:1px solid rgba(245,158,11,0.3); padding:4px 10px; border-radius:12px; font-size:12px; font-weight:600;">
                                    UNKNOWN
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($site->last_response_time)
                                <span style="font-family:'JetBrains Mono',monospace; font-size:13px; font-weight:600; color:{{ $site->last_response_time < 500 ? '#10b981' : ($site->last_response_time < 1500 ? '#fbbf24' : '#ef4444') }};">
                                    ⚡ {{ $site->last_response_time }} ms
                                </span>
                            @else
                                <span style="color:var(--text-muted); font-size:12px;">—</span>
                            @endif
                        </td>
                        <td>
                            @if($site->last_status_code)
                                <span style="font-family:'JetBrains Mono',monospace; font-size:12px; padding:3px 8px; border-radius:6px; background:rgba(255,255,255,0.05); font-weight:600; color:var(--text-primary);">
                                    {{ $site->last_status_code }}
                                </span>
                            @else
                                <span style="color:var(--text-muted); font-size:12px;">—</span>
                            @endif
                        </td>
                        <td>
                            @if($site->ssl_status === 'valid')
                                <div style="display:flex; flex-direction:column;">
                                    <span style="color:#10b981; font-size:12px; font-weight:600;">🔒 Valid SSL</span>
                                    @if($site->ssl_expires_at)
                                        <span style="font-size:10.5px; color:var(--text-secondary);">
                                            Exp: {{ $site->ssl_expires_at->format('M d, Y') }} ({{ (int) now()->diffInDays($site->ssl_expires_at, false) }}d left)
                                        </span>
                                    @endif
                                </div>
                            @elseif($site->ssl_status === 'expired' || $site->ssl_status === 'invalid')
                                <span style="color:#ef4444; font-size:12px; font-weight:700;">⚠️ Invalid / Expired</span>
                            @else
                                <span style="color:var(--text-muted); font-size:12px;">HTTP (No SSL)</span>
                            @endif
                        </td>
                        <td>
                            @if($site->last_checked_at)
                                <span style="font-size:12px; color:var(--text-secondary);" title="{{ $site->last_checked_at->format('Y-m-d H:i:s') }}">
                                    {{ $site->last_checked_at->diffForHumans() }}
                                </span>
                            @else
                                <span style="color:var(--text-muted); font-size:12px;">Never</span>
                            @endif
                        </td>
                        <td style="text-align:right">
                            <div style="display:inline-flex; gap:6px; align-items:center;">
                                @can('sites.check')
                                <form action="{{ route('admin.sites.check', $site->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-ghost" title="Check Health Now" style="padding:4px 8px; font-size:11.5px;">
                                        🔄 Check
                                    </button>
                                </form>
                                @endcan

                                @can('sites.edit')
                                <a href="{{ route('admin.sites.edit', $site->id) }}" class="btn btn-ghost" title="Edit Site" style="padding:4px 8px; font-size:11.5px;">
                                    ✏️
                                </a>
                                @endcan

                                @can('sites.delete')
                                <form action="{{ route('admin.sites.destroy', $site->id) }}" method="POST" class="delete-form" style="display:inline;" data-confirm-title="Hapus Monitoring '{{ $site->name }}'?" data-confirm-text="Website ini akan dihapus dari sistem monitoring!">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" title="Delete Site" style="padding:4px 8px; font-size:11.5px;">
                                        🗑️
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align:center; padding:36px; color:var(--text-muted);">
                            No monitored websites configured yet. <a href="{{ route('admin.sites.create') }}" style="color:var(--accent);">Add your first website to monitor.</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </x-datatable>
    </div>
</x-admin-layout>
