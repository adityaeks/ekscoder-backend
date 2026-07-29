<x-admin-layout title="Projects" breadcrumb="Manage all your landing page projects">
    <x-slot name="topbarAction">
        <a href="{{ route('admin.projects.create') }}" class="topbar-btn topbar-btn-primary">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Project
        </a>
    </x-slot>

    <!-- Stats Row -->
    <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom:24px;">
        <div class="stat-card accent">
            <div class="stat-top">
                <div class="stat-label">Total Projects</div>
                <div class="stat-icon accent">📂</div>
            </div>
            <div class="stat-value">{{ $stats['total'] }}</div>
        </div>
        <div class="stat-card green">
            <div class="stat-top">
                <div class="stat-label">Active on API</div>
                <div class="stat-icon green">⚡</div>
            </div>
            <div class="stat-value">{{ $stats['active'] }}</div>
        </div>
        <div class="stat-card amber">
            <div class="stat-top">
                <div class="stat-label">Featured</div>
                <div class="stat-icon amber">⭐</div>
            </div>
            <div class="stat-value">{{ $stats['featured'] }}</div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card">
        <x-datatable id="projectsDataTable" title="All Projects" subtitle="Click the status badge to toggle active/inactive" search-placeholder="Search projects..." :per-page-options="[10, 20, 50, 'all']" :default-per-page="10">
            <x-slot:actions>
                <a href="{{ route('admin.projects.create') }}" class="topbar-btn topbar-btn-primary" style="padding:6px 12px; font-size:12px; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    New Project
                </a>
            </x-slot:actions>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:60px" data-sortable="true">No</th>
                        <th data-sortable="true">Project</th>
                        <th data-sortable="true">Category & Year</th>
                        <th data-sortable="true">Tech Stack</th>
                        <th data-sortable="true">Featured</th>
                        <th data-sortable="true">Status</th>
                        <th style="text-align:right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $project)
                    <tr>
                        <td>
                            <span class="datatable-row-index" style="font-family:'JetBrains Mono',monospace; font-weight:700; color:var(--text-primary); font-size:13px;">{{ $project->number }}</span>
                        </td>
                        <td>
                            <div style="font-weight:700; color:var(--text-primary); font-size:13.5px; margin-bottom:2px;">{{ $project->title }}</div>
                            <div style="font-family:'JetBrains Mono',monospace; font-size:10.5px; color:var(--text-muted);">{{ $project->id }}</div>
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
                                <span style="color:var(--text-muted); font-size:12px;">—</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.projects.toggle-active', $project->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="badge {{ $project->is_active ? 'badge-green' : 'badge-rose' }}" style="cursor:pointer; border-color:inherit; font-family:inherit;">
                                    <span class="badge-dot"></span>
                                    {{ $project->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>
                        <td style="text-align:right">
                            <div style="display:inline-flex; gap:6px; align-items:center;">
                                <a href="{{ route('admin.projects.edit', $project->id) }}" class="btn btn-ghost">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    Edit
                                </a>
                                <form action="{{ route('admin.projects.destroy', $project->id) }}" method="POST" onsubmit="event.preventDefault(); confirmDelete('Delete \'{{ addslashes($project->title) }}\'?', 'This project will be permanently removed from the portfolio.', this);">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-state-icon">📭</div>
                                <div class="empty-state-text">No projects found. <a href="{{ route('admin.projects.create') }}" style="color:var(--accent)">Create your first project.</a></div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </x-datatable>
    </div>
</x-admin-layout>
