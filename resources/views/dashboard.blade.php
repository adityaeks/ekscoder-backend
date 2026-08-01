<x-admin-layout title="Dashboard Overview" breadcrumb="Executive Command Center & Business Pipeline">

    <!-- Top Stats Grid -->
    <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); margin-bottom: 24px;">
        <div class="stat-card accent">
            <div class="stat-top">
                <div class="stat-label">Pipeline Value</div>
                <div class="stat-icon accent">💰</div>
            </div>
            <div class="stat-value" style="font-size:22px;">Rp {{ number_format($stats['total_pipeline'], 0, ',', '.') }}</div>
            <div class="stat-meta">Gross contract value (excl. cancelled)</div>
        </div>

        <div class="stat-card green">
            <div class="stat-top">
                <div class="stat-label">Collected Payments</div>
                <div class="stat-icon green">💵</div>
            </div>
            <div class="stat-value" style="font-size:22px; color:var(--green);">Rp {{ number_format($stats['total_paid'], 0, ',', '.') }}</div>
            <div class="stat-meta">Received DP & full payments</div>
        </div>

        <div class="stat-card cyan">
            <div class="stat-top">
                <div class="stat-label">Active Orders</div>
                <div class="stat-icon cyan">⚡</div>
            </div>
            <div class="stat-value">{{ $stats['active_orders'] }}</div>
            <div class="stat-meta">Ongoing projects in pipeline</div>
        </div>

        <div class="stat-card amber">
            <div class="stat-top">
                <div class="stat-label">Completed Orders</div>
                <div class="stat-icon amber">🎉</div>
            </div>
            <div class="stat-value">{{ $stats['completed_orders'] }}</div>
            <div class="stat-meta">Finished and delivered</div>
        </div>

        <div class="stat-card accent">
            <div class="stat-top">
                <div class="stat-label">Porto Projects</div>
                <div class="stat-icon accent">📂</div>
            </div>
            <div class="stat-value">{{ $stats['total_projects'] }}</div>
            <div class="stat-meta">{{ $stats['active_projects'] }} live on public API</div>
        </div>
    </div>

    <!-- 2-Column Dashboard Grid: Orders Pipeline + Audit Feed -->
    <div style="display: grid; grid-template-columns: 1fr 380px; gap: 20px; margin-bottom: 24px;">
        
        <!-- Left: Recent Project Orders -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <div>
                    <div class="card-title">Recent Project Orders</div>
                    <div class="card-subtitle">Latest client contracts, status, and target dates</div>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-ghost">
                    Order Board
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            </div>
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Project & Client</th>
                            <th>Status Stage</th>
                            <th>Target Dates</th>
                            <th>Budget & Paid</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                            <tr>
                                <td>
                                    <div style="font-weight:700; color:var(--text-primary); font-size:13px;">{{ $order->title }}</div>
                                    <div style="font-size:11px; color:var(--text-secondary);">👤 {{ $order->client_name }}</div>
                                </td>
                                <td>
                                    @if($order->status === 'requirement')
                                        <span class="badge badge-amber">📝 Requirement</span>
                                    @elseif($order->status === 'in_progress')
                                        <span class="badge badge-cyan">⚡ In Progress</span>
                                    @elseif($order->status === 'review')
                                        <span class="badge badge-accent">🔍 Review</span>
                                    @elseif($order->status === 'completed')
                                        <span class="badge badge-green">✅ Completed</span>
                                    @else
                                        <span class="badge badge-rose">⛔ Cancelled</span>
                                    @endif
                                </td>
                                @php
                                    $isOverdue = $order->deadline && $order->deadline->isPast() && $order->status !== 'completed';
                                    $deadlineColor = $isOverdue ? 'var(--rose)' : 'var(--text-muted)';
                                    $deadlineWeight = $isOverdue ? '700' : '400';
                                @endphp
                                <td>
                                    <div style="font-size:11px; color:var(--text-secondary);">
                                        🚀 {{ $order->start_date ? $order->start_date->format('d M Y') : '-' }}
                                    </div>
                                    <div style="font-size:11px; color: {{ $deadlineColor }}; font-weight: {{ $deadlineWeight }};">
                                        🏁 {{ $order->deadline ? $order->deadline->format('d M Y') : '-' }}
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size:12.5px; font-weight:700; color:var(--text-primary);">{{ $order->formatted_budget }}</div>
                                    <div style="font-size:10.5px; color:var(--green); font-weight:600;">Paid: {{ $order->formatted_paid }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="empty-state" style="padding: 30px 10px;">
                                        <div class="empty-state-text">No project orders recorded yet.</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Recent Activity Audit Logs -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <div>
                    <div class="card-title">Activity Audit Feed</div>
                    <div class="card-subtitle">Real-time user actions</div>
                </div>
                <a href="{{ route('admin.logs.index') }}" class="btn btn-ghost" style="padding: 4px 8px; font-size: 11px;">
                    View All
                </a>
            </div>
            <div class="card-body" style="padding: 14px;">
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @forelse($recentLogs as $log)
                        <div style="display: flex; align-items: flex-start; gap: 10px; padding-bottom: 10px; border-bottom: 1px solid var(--border);">
                            <div style="width: 28px; height: 28px; border-radius: 50%; background: var(--bg-elevated); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: var(--text-primary); flex-shrink: 0; margin-top: 2px;">
                                {{ strtoupper(substr($log->user_name ?? 'S', 0, 1)) }}
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="display: flex; align-items: center; justify-content: space-between; gap: 6px; margin-bottom: 2px;">
                                    <span style="font-size: 11.5px; font-weight: 700; color: var(--text-primary);">{{ $log->user_name ?? 'System' }}</span>
                                    @if($log->action === 'create')
                                        <span class="badge badge-green" style="font-size: 9px; padding: 1px 6px;">CREATE</span>
                                    @elseif($log->action === 'update')
                                        <span class="badge badge-accent" style="font-size: 9px; padding: 1px 6px;">UPDATE</span>
                                    @elseif($log->action === 'delete')
                                        <span class="badge badge-rose" style="font-size: 9px; padding: 1px 6px;">DELETE</span>
                                    @else
                                        <span class="badge badge-amber" style="font-size: 9px; padding: 1px 6px;">{{ strtoupper($log->action) }}</span>
                                    @endif
                                </div>
                                <div style="font-size: 11.5px; color: var(--text-secondary); line-height: 1.3; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $log->description }}
                                </div>
                                <div style="font-size: 10px; color: var(--text-muted); margin-top: 3px;">
                                    {{ $log->created_at->diffForHumans() }} • <span style="font-family:'JetBrains Mono',monospace;">{{ $log->module }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state" style="padding: 20px 10px;">
                            <div class="empty-state-text">No activity logged yet.</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    <!-- Web Portfolio Projects Table -->
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Porto Projects Overview</div>
                <div class="card-subtitle">Public portfolio items served via `/api/projects`</div>
            </div>
            <a href="{{ route('admin.projects.index') }}" class="btn btn-ghost">
                Manage Projects
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Project</th>
                        <th>Category & Year</th>
                        <th>Tech Stack</th>
                        <th>Featured</th>
                        <th>API Status</th>
                        <th style="text-align:right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $project)
                    <tr>
                        <td>
                            <span style="font-family:'JetBrains Mono',monospace; font-size:12px; font-weight:700; color:var(--text-muted);">#{{ $project->number }}</span>
                        </td>
                        <td>
                            <div style="font-weight:700; color:var(--text-primary); font-size:13.5px;">{{ $project->title }}</div>
                            <div style="font-size:11px; color:var(--text-muted); font-family:'JetBrains Mono',monospace; margin-top:1px;">{{ $project->slug }}</div>
                        </td>
                        <td>
                            <div style="font-weight:500; color:var(--text-secondary); font-size:12.5px;">{{ $project->category }}</div>
                            <div style="font-size:11px; color:var(--text-muted);">{{ $project->year }}</div>
                        </td>
                        <td>
                            <div class="tech-pills">
                                @foreach(array_slice($project->technologies, 0, 3) as $tech)
                                    <span class="tech-pill">{{ $tech }}</span>
                                @endforeach
                                @if(count($project->technologies) > 3)
                                    <span class="tech-pill">+{{ count($project->technologies) - 3 }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($project->featured)
                                <span class="badge badge-amber">★ Featured</span>
                            @else
                                <span style="color:var(--text-muted); font-size:12px;">Standard</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.projects.toggle-active', $project->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="badge {{ $project->is_active ? 'badge-green' : 'badge-rose' }}" style="cursor:pointer; border:none; font-family:inherit;">
                                    <span class="badge-dot"></span>
                                    {{ $project->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>
                        <td style="text-align:right">
                            <div style="display:flex; gap:6px; justify-content:flex-end;">
                                <a href="{{ route('admin.projects.edit', $project->id) }}" class="btn btn-ghost" style="padding:4px 10px; font-size:11.5px;">Edit</a>
                                <form action="{{ route('admin.projects.destroy', $project->id) }}" method="POST" onsubmit="event.preventDefault(); confirmDelete('Delete \'{{ addslashes($project->title) }}\'?', 'This project will be permanently deleted.', this);">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding:4px 10px; font-size:11.5px;">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-state-icon">📭</div>
                                <div class="empty-state-text">No projects yet. <a href="{{ route('admin.projects.create') }}" style="color:var(--accent)">Create your first one.</a></div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
