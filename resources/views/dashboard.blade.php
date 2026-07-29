<x-admin-layout title="Dashboard" breadcrumb="Overview of your content">
    <x-slot name="topbarAction">
        <a href="{{ route('admin.projects.create') }}" class="topbar-btn topbar-btn-primary">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Project
        </a>
    </x-slot>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card accent">
            <div class="stat-top">
                <div class="stat-label">Total Projects</div>
                <div class="stat-icon accent">📂</div>
            </div>
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-meta">All projects in database</div>
        </div>

        <div class="stat-card green">
            <div class="stat-top">
                <div class="stat-label">Live on API</div>
                <div class="stat-icon green">⚡</div>
            </div>
            <div class="stat-value">{{ $stats['active'] }}</div>
            <div class="stat-meta">Active & serving requests</div>
        </div>

        <div class="stat-card amber">
            <div class="stat-top">
                <div class="stat-label">Featured</div>
                <div class="stat-icon amber">⭐</div>
            </div>
            <div class="stat-value">{{ $stats['featured'] }}</div>
            <div class="stat-meta">Featured on landing page</div>
        </div>

        <div class="stat-card cyan">
            <div class="stat-top">
                <div class="stat-label">API Endpoint</div>
                <div class="stat-icon cyan">🌐</div>
            </div>
            <div class="stat-value" style="font-size:20px; letter-spacing:-0.5px; padding-top:6px;">GET</div>
            <div class="stat-meta" style="font-family:'JetBrains Mono',monospace; font-size:10.5px;">/api/projects</div>
        </div>
    </div>

    <!-- Projects Table Card -->
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Projects Overview</div>
                <div class="card-subtitle">All projects served to the Next.js landing page</div>
            </div>
            <a href="{{ route('admin.projects.index') }}" class="btn btn-ghost">
                View All
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Project</th>
                    <th>Category</th>
                    <th>Tech Stack</th>
                    <th>Featured</th>
                    <th>Status</th>
                    <th style="text-align:right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $project)
                <tr>
                    <td>
                        <span style="font-family:'JetBrains Mono',monospace; font-size:13px; font-weight:700; color:var(--text-primary)">{{ $project->number }}</span>
                    </td>
                    <td>
                        <div style="font-weight:700; color:var(--text-primary); font-size:13.5px;">{{ $project->title }}</div>
                        <div style="font-size:11px; color:var(--text-muted); font-family:'JetBrains Mono',monospace; margin-top:2px;">{{ $project->id }}</div>
                    </td>
                    <td>
                        <div style="font-weight:500; color:var(--text-secondary);">{{ $project->category }}</div>
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
                            <a href="{{ route('admin.projects.edit', $project->id) }}" class="btn btn-ghost">Edit</a>
                            <form action="{{ route('admin.projects.destroy', $project->id) }}" method="POST" onsubmit="return confirm('Delete this project?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
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
</x-admin-layout>
