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
                <div class="stat-icon accent">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
            </div>
            <div class="stat-value">{{ $stats['total'] }}</div>
        </div>
        <div class="stat-card red">
            <div class="stat-top">
                <div class="stat-label">Super Admins</div>
                <div class="stat-icon red">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
            </div>
            <div class="stat-value">{{ $stats['super_admins'] }}</div>
        </div>
        <div class="stat-card green">
            <div class="stat-top">
                <div class="stat-label">Admins</div>
                <div class="stat-icon green">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                </div>
            </div>
            <div class="stat-value">{{ $stats['admins'] }}</div>
        </div>
        <div class="stat-card amber">
            <div class="stat-top">
                <div class="stat-label">Staff</div>
                <div class="stat-icon amber">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
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
                                <div style="width:36px; height:36px; border-radius:8px; background:var(--bg-elevated); border:1px solid var(--border-light); color:var(--text-primary); display:flex; align-items:center; justify-content:center; font-family:'JetBrains Mono', monospace; font-weight:700; font-size:13px; flex-shrink:0;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:600; color:var(--text-primary); font-size:14px; display:flex; align-items:center; gap:6px;">
                                        {{ $user->name }}
                                        @if($user->id === auth()->id())
                                            <span style="background:rgba(16,185,129,0.15); color:#10b981; border:1px solid rgba(16,185,129,0.3); padding:1px 6px; border-radius:10px; font-size:10px; font-weight:600;">You</span>
                                        @endif
                                        @if(!$user->otp_enabled)
                                            <span style="background:rgba(245,158,11,0.15); color:#f59e0b; border:1px solid rgba(245,158,11,0.3); padding:1px 6px; border-radius:10px; font-size:10px; font-weight:600; display:inline-flex; align-items:center; gap:3px;" title="User ini tidak memerlukan OTP saat login (Bypass OTP)">
                                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                                Bypass OTP
                                            </span>
                                        @endif
                                    </div>
                                    <div style="font-size:12px; color:var(--text-secondary);">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @php $primaryRole = $user->getRoleNames()->first(); @endphp
                            @if($primaryRole === 'Super Admin')
                                <span style="background:rgba(244,63,94,0.12); color:#f43f5e; border:1px solid rgba(244,63,94,0.28); padding:4px 10px; border-radius:9999px; font-size:11.5px; font-weight:600; display:inline-flex; align-items:center; gap:5px;">
                                    Super Admin
                                </span>
                            @elseif($primaryRole === 'Admin')
                                <span style="background:var(--accent-soft); color:var(--badge-accent-text); border:1px solid var(--border-light); padding:4px 10px; border-radius:9999px; font-size:11.5px; font-weight:600; display:inline-flex; align-items:center; gap:5px;">
                                    Admin
                                </span>
                            @elseif($primaryRole)
                                <span style="background:rgba(245,158,11,0.12); color:#f59e0b; border:1px solid rgba(245,158,11,0.28); padding:4px 10px; border-radius:9999px; font-size:11.5px; font-weight:600; display:inline-flex; align-items:center; gap:5px;">
                                    {{ $primaryRole }}
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
                                <form action="{{ route('admin.users.toggle-otp', $user->id) }}" method="POST" style="display:inline;"
                                      data-confirm-title="{{ $user->otp_enabled ? 'Nonaktifkan OTP untuk ' . $user->name . '?' : 'Aktifkan OTP untuk ' . $user->name . '?' }}"
                                      data-confirm-text="{{ $user->otp_enabled ? 'Pengguna ini akan dapat login langsung tanpa verifikasi kode OTP (Bypass OTP).' : 'Pengguna ini akan diwajibkan memasukkan kode verifikasi 6-digit OTP saat login.' }}"
                                      data-confirm-btn="{{ $user->otp_enabled ? 'Nonaktifkan OTP' : 'Aktifkan OTP' }}"
                                      data-confirm-icon="{{ $user->otp_enabled ? 'warning' : 'question' }}">
                                    @csrf
                                    @method('PATCH')
                                    @if($user->otp_enabled)
                                    <button type="submit" class="btn btn-ghost" style="color:#10b981; border-color:rgba(16,185,129,0.35); background:rgba(16,185,129,0.08);" title="OTP Aktif. Klik untuk menonaktifkan / bypass OTP">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
                                        OTP On
                                    </button>
                                    @else
                                    <button type="submit" class="btn btn-ghost" style="color:#f59e0b; border-color:rgba(245,158,11,0.35); background:rgba(245,158,11,0.08);" title="OTP Nonaktif (Bypass). Klik untuk mengaktifkan kembali OTP">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                        Bypass OTP
                                    </button>
                                    @endif
                                </form>
                                @endcan

                                @if($user->id !== auth()->id())
                                @can('users.edit')
                                <form action="{{ route('admin.users.impersonate', $user->id) }}" method="POST" style="display:inline;"
                                      data-confirm-title="Masuk sebagai {{ $user->name }}?"
                                      data-confirm-text="Anda akan masuk ke dashboard akun ini untuk melihat tampilan menu dan hak aksesnya."
                                      data-confirm-btn="Masuk ke User"
                                      data-confirm-icon="question">
                                    @csrf
                                    <button type="submit" class="btn btn-ghost" style="color:var(--accent); border-color:rgba(99,102,241,0.35); background:rgba(99,102,241,0.08);" title="Masuk ke dashboard sebagai {{ $user->name }}">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                                        Masuk User
                                    </button>
                                </form>
                                @endcan
                                @endif

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
