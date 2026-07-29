<x-admin-layout title="Roles & Permissions" breadcrumb="Manage system roles and module access permissions">
    <x-slot name="topbarAction">
        <div style="display:flex; gap:8px;">
            @can('roles.create')
            <button type="button" onclick="openModal('createPermissionModal')" class="topbar-btn topbar-btn-ghost">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                New Permission
            </button>
            <a href="{{ route('admin.roles.create') }}" class="topbar-btn topbar-btn-primary" style="text-decoration:none;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                New Role
            </a>
            @endcan
        </div>
    </x-slot>

    <!-- Stats Row -->
    <div class="stats-grid" style="grid-template-columns: repeat(2, 1fr); margin-bottom:24px;">
        <div class="stat-card accent">
            <div class="stat-top">
                <div class="stat-label">Total Roles</div>
                <div class="stat-icon accent">🔑</div>
            </div>
            <div class="stat-value">{{ $stats['total_roles'] }}</div>
        </div>
        <div class="stat-card green">
            <div class="stat-top">
                <div class="stat-label">System Permissions</div>
                <div class="stat-icon green">🛡️</div>
            </div>
            <div class="stat-value">{{ $stats['total_permissions'] }}</div>
        </div>
    </div>

    <!-- Roles Table Card -->
    <div class="card" style="margin-bottom:24px;">
        <x-datatable id="rolesDataTable" title="All System Roles" subtitle="Manage permissions assigned to each role" search-placeholder="Search roles..." :per-page-options="[10, 20, 50, 'all']" :default-per-page="10">
            <x-slot:actions>
                @can('roles.create')
                <div style="display:flex; gap:6px;">
                    <button type="button" onclick="openModal('createPermissionModal')" class="btn btn-ghost" style="font-size:12px;">
                        + New Permission
                    </button>
                    <a href="{{ route('admin.roles.create') }}" class="topbar-btn topbar-btn-primary" style="padding:6px 12px; font-size:12px; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        New Role
                    </a>
                </div>
                @endcan
            </x-slot:actions>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:60px" data-sortable="true">No</th>
                        <th data-sortable="true">Role Name</th>
                        <th data-sortable="true">Users Assigned</th>
                        <th data-sortable="true">Permissions</th>
                        <th style="text-align:right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $index => $role)
                    <tr>
                        <td>
                            <span class="datatable-row-index" style="font-family:'JetBrains Mono',monospace; font-weight:700; color:var(--text-primary); font-size:13px;">{{ $index + 1 }}</span>
                        </td>
                        <td>
                            <div style="font-weight:600; color:var(--text-primary); font-size:14px; display:flex; align-items:center; gap:8px;">
                                <span>{{ $role->name }}</span>
                                @if($role->name === 'Super Admin')
                                    <span style="background:rgba(239,68,68,0.15); color:#ef4444; border:1px solid rgba(239,68,68,0.3); padding:2px 8px; border-radius:12px; font-size:11px; font-weight:600;">Full Access</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span style="background:var(--bg-card-hover, rgba(255,255,255,0.05)); padding:4px 10px; border-radius:6px; font-size:12px; font-weight:600;">
                                👥 {{ $role->users_count }} user(s)
                            </span>
                        </td>
                        <td>
                            @if($role->name === 'Super Admin')
                                <span style="color:var(--text-secondary); font-size:13px; font-style:italic;">Has all permissions (Gate::before)</span>
                            @else
                                <div style="display:flex; flex-wrap:wrap; gap:4px; max-width:400px;">
                                    @forelse($role->permissions->take(6) as $perm)
                                        <span style="background:rgba(99,102,241,0.12); color:#818cf8; border:1px solid rgba(99,102,241,0.2); padding:2px 6px; border-radius:4px; font-size:11px; font-weight:500;">
                                            {{ $perm->name }}
                                        </span>
                                    @empty
                                        <span style="color:var(--text-muted); font-size:12px;">No permissions</span>
                                    @endforelse
                                    @if($role->permissions->count() > 6)
                                        <span style="background:rgba(255,255,255,0.08); color:var(--text-secondary); padding:2px 6px; border-radius:4px; font-size:11px;">
                                            +{{ $role->permissions->count() - 6 }} more
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td style="text-align:right">
                            <div style="display:inline-flex; gap:6px; align-items:center;">
                                @can('roles.edit')
                                <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-ghost" title="Edit Role">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    Edit
                                </a>
                                @endcan

                                @can('roles.delete')
                                @if($role->name !== 'Super Admin')
                                <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="delete-form" style="display:inline;" data-confirm-title="Hapus Role '{{ $role->name }}'?" data-confirm-text="Role ini dan konfigurasinya akan dihapus permanen!">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" title="Delete Role">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                        Delete
                                    </button>
                                </form>
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center; padding:32px; color:var(--text-muted);">
                            No roles found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </x-datatable>
    </div>

    <!-- Permissions Directory Card -->
    <div class="card">
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <div class="card-title">System Permissions Directory</div>
                <div class="card-subtitle">All active permissions grouped by module</div>
            </div>
            @can('roles.create')
            <button type="button" onclick="openModal('createPermissionModal')" class="topbar-btn topbar-btn-primary" style="font-size:12px; padding:6px 12px;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                New Permission
            </button>
            @endcan
        </div>
        <div class="card-body" style="display:flex; flex-direction:column; gap:16px;">
            @foreach($groupedPermissions as $module => $permissions)
            <div style="border:1px solid var(--border-color, rgba(255,255,255,0.08)); border-radius:8px; padding:16px; background:var(--bg-surface, rgba(255,255,255,0.02));">
                <div style="font-weight:700; color:var(--text-primary); font-size:13px; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:10px; display:flex; align-items:center; justify-content:space-between;">
                    <span>📦 {{ $module }} Module</span>
                    <span style="font-size:11px; background:rgba(255,255,255,0.08); padding:2px 8px; border-radius:10px; color:var(--text-secondary);">{{ count($permissions) }} permissions</span>
                </div>
                <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(240px, 1fr)); gap:10px;">
                    @foreach($permissions as $perm)
                    <div style="display:flex; align-items:center; justify-space-between; background:rgba(255,255,255,0.03); padding:8px 12px; border-radius:6px; border:1px solid rgba(255,255,255,0.05);">
                        <span style="font-family:'JetBrains Mono',monospace; font-size:12px; color:var(--text-primary); flex-grow:1;">{{ $perm->name }}</span>
                        @can('roles.delete')
                        <form action="{{ route('admin.permissions.destroy', $perm->id) }}" method="POST" class="delete-form" style="display:inline; margin-left:8px;" data-confirm-title="Hapus Permission '{{ $perm->name }}'?" data-confirm-text="Permission ini akan dihapus dari semua role & user!">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background:none; border:none; color:var(--text-muted); cursor:pointer; font-size:12px; padding:2px 4px;" title="Delete Permission">
                                ❌
                            </button>
                        </form>
                        @endcan
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Create Permission Modal -->
    <x-admin-modal id="createPermissionModal" title="Create New Permission" max-width="480px">
        <form action="{{ route('admin.permissions.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" style="font-weight:600; margin-bottom:6px; display:block;">Permission Name *</label>
                    <input type="text" name="name" required placeholder="e.g. reports.view or settings.edit" class="form-input" style="width:100%;">
                    <div class="form-hint" style="margin-top:8px;">Use the format <code>module.action</code> (e.g. <code>invoices.create</code>, <code>reports.export</code>).</div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" onclick="closeModal('createPermissionModal')" class="btn btn-ghost">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Permission</button>
            </div>
        </form>
    </x-admin-modal>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId)?.classList.add('open');
        }
        function closeModal(modalId) {
            document.getElementById(modalId)?.classList.remove('open');
        }
    </script>
</x-admin-layout>
