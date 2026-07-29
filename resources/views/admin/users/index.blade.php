<x-admin-layout title="User Management" breadcrumb="Manage user accounts, assign roles, and configure direct permissions">
    <x-slot name="topbarAction">
        @can('users.create')
        <a href="{{ route('admin.users.create') }}" class="topbar-btn topbar-btn-primary" style="text-decoration:none;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add New User
        </a>
        @endcan
    </x-slot>

    <!-- Stats Row -->
    <div class="stats-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom:24px;">
        <div class="stat-card accent">
            <div class="stat-top">
                <div class="stat-label">Total Users</div>
                <div class="stat-icon accent">👥</div>
            </div>
            <div class="stat-value">{{ $stats['total'] }}</div>
        </div>
        <div class="stat-card red">
            <div class="stat-top">
                <div class="stat-label">Super Admins</div>
                <div class="stat-icon red">👑</div>
            </div>
            <div class="stat-value">{{ $stats['super_admins'] }}</div>
        </div>
        <div class="stat-card green">
            <div class="stat-top">
                <div class="stat-label">Admins</div>
                <div class="stat-icon green">⚡</div>
            </div>
            <div class="stat-value">{{ $stats['admins'] }}</div>
        </div>
        <div class="stat-card amber">
            <div class="stat-top">
                <div class="stat-label">Staff</div>
                <div class="stat-icon amber">👤</div>
            </div>
            <div class="stat-value">{{ $stats['staff'] }}</div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card">
        <x-datatable id="usersDataTable" title="All System Users" subtitle="Manage role assignments and user details" search-placeholder="Search users by name or email..." :per-page-options="[10, 20, 50, 'all']" :default-per-page="10">
            <x-slot:actions>
                @can('users.create')
                <a href="{{ route('admin.users.create') }}" class="topbar-btn topbar-btn-primary" style="padding:6px 12px; font-size:12px; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    New User
                </a>
                @endcan
            </x-slot:actions>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:60px" data-sortable="true">No</th>
                        <th data-sortable="true">User Details</th>
                        <th data-sortable="true">Assigned Role</th>
                        <th data-sortable="true">Direct Permissions</th>
                        <th data-sortable="true">Joined</th>
                        <th style="text-align:right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $user)
                    <tr>
                        <td>
                            <span class="datatable-row-index" style="font-family:'JetBrains Mono',monospace; font-weight:700; color:var(--text-primary); font-size:13px;">{{ $index + 1 }}</span>
                        </td>
                        <td>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="width:36px; height:36px; border-radius:50%; background:linear-gradient(135deg, #6366f1, #8b5cf6); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:14px;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:600; color:var(--text-primary); font-size:14px;">
                                        {{ $user->name }}
                                        @if($user->id === auth()->id())
                                            <span style="background:rgba(16,185,129,0.15); color:#10b981; border:1px solid rgba(16,185,129,0.3); padding:1px 6px; border-radius:10px; font-size:10px; font-weight:600;">You</span>
                                        @endif
                                    </div>
                                    <div style="font-size:12px; color:var(--text-secondary);">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @php $primaryRole = $user->getRoleNames()->first(); @endphp
                            @if($primaryRole === 'Super Admin')
                                <span style="background:rgba(239,68,68,0.15); color:#ef4444; border:1px solid rgba(239,68,68,0.3); padding:4px 10px; border-radius:12px; font-size:12px; font-weight:600;">
                                    👑 Super Admin
                                </span>
                            @elseif($primaryRole === 'Admin')
                                <span style="background:rgba(99,102,241,0.15); color:#818cf8; border:1px solid rgba(99,102,241,0.3); padding:4px 10px; border-radius:12px; font-size:12px; font-weight:600;">
                                    ⚡ Admin
                                </span>
                            @elseif($primaryRole)
                                <span style="background:rgba(245,158,11,0.15); color:#fbbf24; border:1px solid rgba(245,158,11,0.3); padding:4px 10px; border-radius:12px; font-size:12px; font-weight:600;">
                                    👤 {{ $primaryRole }}
                                </span>
                            @else
                                <span style="color:var(--text-muted); font-size:12px; font-style:italic;">No Role</span>
                            @endif
                        </td>
                        <td>
                            @php $directPerms = $user->getDirectPermissions(); @endphp
                            @if($directPerms->count() > 0)
                                <div style="display:flex; flex-wrap:wrap; gap:4px; max-width:250px;">
                                    @foreach($directPerms as $dp)
                                        <span style="background:rgba(16,185,129,0.12); color:#34d399; border:1px solid rgba(16,185,129,0.2); padding:2px 6px; border-radius:4px; font-size:11px;">
                                            {{ $dp->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span style="color:var(--text-muted); font-size:12px;">Standard role access</span>
                            @endif
                        </td>
                        <td>
                            <span style="font-size:12px; color:var(--text-secondary);">{{ $user->created_at->format('M d, Y') }}</span>
                        </td>
                        <td style="text-align:right">
                            <div style="display:inline-flex; gap:6px; align-items:center;">
                                @can('users.edit')
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-ghost" title="Edit User">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    Edit
                                </a>
                                @endcan

                                @can('users.delete')
                                @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="delete-form" style="display:inline;" data-confirm-title="Hapus Pengguna '{{ $user->name }}'?" data-confirm-text="Akun pengguna ini akan dihapus permanen dari sistem!">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" title="Delete User">
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
                        <td colspan="6" style="text-align:center; padding:32px; color:var(--text-muted);">
                            No users found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </x-datatable>
    </div>
</x-admin-layout>
