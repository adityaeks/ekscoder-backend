<x-admin-layout title="User Activity Logs" breadcrumb="Track & audit all create, update, and delete actions in real-time">

    <!-- Logs Table -->
    <div class="card">
        <x-datatable id="logsDataTable" title="Audit Trail & System Logs" subtitle="Real-time audit log of system activities & user actions" search-placeholder="Search description, user, IP..." :per-page-options="[10, 20, 50, 'all']" :default-per-page="10">
            @if($logs->count() > 0)
            <x-slot:actions>
                <form action="{{ route('admin.logs.clear') }}" method="POST" onsubmit="event.preventDefault(); confirmDelete('Clear Activity Logs?', 'All recorded system audit logs will be deleted permanently.', this);">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="padding: 6px 12px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                        Clear Logs
                    </button>
                </form>
            </x-slot:actions>
            @endif

            <x-slot:filters>
                <select class="datatable-filter-select" data-column="3">
                    <option value="">All Modules</option>
                    @foreach($modules as $mod)
                        <option value="{{ $mod }}">{{ $mod }}</option>
                    @endforeach
                </select>

                <select class="datatable-filter-select" data-column="2">
                    <option value="">All Actions</option>
                    @foreach($actions as $act)
                        <option value="{{ $act }}">{{ strtoupper($act) }}</option>
                    @endforeach
                </select>
            </x-slot:filters>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 160px;" data-sortable="true">Timestamp</th>
                        <th data-sortable="true">User</th>
                        <th style="width: 100px;" data-sortable="true">Action</th>
                        <th style="width: 130px;" data-sortable="true">Module</th>
                        <th data-sortable="true">Description & Details</th>
                        <th style="width: 120px;" data-sortable="true">IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td style="font-family: 'JetBrains Mono', monospace; font-size: 11.5px; white-space: nowrap;">
                                {{ $log->created_at->format('Y-m-d H:i:s') }}
                                <div style="font-size: 10px; color: var(--text-muted);">{{ $log->created_at->diffForHumans() }}</div>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 26px; height: 26px; border-radius: 50%; background: var(--bg-elevated); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: var(--text-primary);">
                                        {{ strtoupper(substr($log->user_name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div style="font-weight: 600; font-size: 13px; color: var(--text-primary);">
                                        {{ $log->user_name ?? 'System' }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($log->action === 'create')
                                    <span class="badge badge-green"><span class="badge-dot"></span> CREATE</span>
                                @elseif($log->action === 'update')
                                    <span class="badge badge-accent"><span class="badge-dot"></span> UPDATE</span>
                                @elseif($log->action === 'delete')
                                    <span class="badge badge-rose"><span class="badge-dot"></span> DELETE</span>
                                @else
                                    <span class="badge badge-amber"><span class="badge-dot"></span> {{ strtoupper($log->action) }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="tech-pill" style="font-size: 11px;">{{ $log->module }}</span>
                            </td>
                            <td>
                                <div style="font-size: 13px; color: var(--text-primary); font-weight: 500;">
                                    {{ $log->description }}
                                </div>
                                @if($log->changes)
                                    <details style="margin-top: 4px; font-size: 11px;">
                                        <summary style="cursor: pointer; color: var(--text-muted); font-weight: 600;">View Changed Attributes</summary>
                                        <pre style="background: var(--bg-elevated); border: 1px solid var(--border); padding: 8px 10px; border-radius: 6px; font-family: 'JetBrains Mono', monospace; font-size: 11px; margin-top: 4px; max-height: 150px; overflow-y: auto; color: var(--text-secondary);">{{ json_encode($log->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    </details>
                                @endif
                            </td>
                            <td style="font-family: 'JetBrains Mono', monospace; font-size: 11px; color: var(--text-muted);">
                                {{ $log->ip_address ?? 'N/A' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-state-icon">📋</div>
                                    <div class="empty-state-text">No activity logs recorded yet.</div>
                                    <p style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">User actions like creating or updating projects will appear here automatically.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-datatable>
    </div>
</x-admin-layout>
