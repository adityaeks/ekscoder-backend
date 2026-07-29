<x-admin-layout title="Edit User: {{ $user->name }}" breadcrumb="Update user account, role, and direct permissions">
    <x-slot name="topbarAction">
        <a href="{{ route('admin.users.index') }}" class="topbar-btn topbar-btn-ghost" style="text-decoration:none;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Back to Users
        </a>
    </x-slot>

    <div style="max-width:920px;">
        @if ($errors->any())
            <div style="background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.25); color:#f87171; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:13px;">
                @foreach ($errors->all() as $error)
                    <p>• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Basic Info Card -->
            <div class="card" style="margin-bottom:20px;">
                <div class="card-header">
                    <div>
                        <div class="card-title">User Account Details</div>
                        <div class="card-subtitle">Personal info and role configuration</div>
                    </div>
                </div>
                <div class="card-body">
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div class="form-group">
                            <label class="form-label" style="font-weight:600; margin-bottom:6px; display:block;">Full Name *</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="form-input" style="width:100%;">
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-weight:600; margin-bottom:6px; display:block;">Email Address *</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="form-input" style="width:100%;">
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-top:16px;">
                        <div class="form-group">
                            <label class="form-label" style="font-weight:600; margin-bottom:6px; display:block;">New Password (Optional)</label>
                            <input type="password" name="password" placeholder="Leave empty to keep current password" class="form-input" style="width:100%;">
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-weight:600; margin-bottom:6px; display:block;">Assigned Role *</label>
                            <select name="role" required class="form-input" style="width:100%;">
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ old('role', $userRole) === $role->name ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Direct Extra Permissions (Optional) -->
            <div class="card" style="margin-bottom:20px;">
                <div class="card-header">
                    <div>
                        <div class="card-title">Direct Extra Permissions (Optional)</div>
                        <div class="card-subtitle">Grant additional specific permissions directly to this user beyond their role</div>
                    </div>
                </div>
                <div class="card-body" style="display:flex; flex-direction:column; gap:16px;">
                    @foreach($groupedPermissions as $module => $permissions)
                    <div style="border:1px solid var(--border-color, rgba(255,255,255,0.08)); border-radius:8px; padding:14px; background:var(--bg-surface, rgba(255,255,255,0.02));">
                        <div style="font-weight:700; color:var(--text-primary); font-size:13px; text-transform:uppercase; margin-bottom:8px;">
                            📦 {{ $module }} Module
                        </div>
                        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(200px, 1fr)); gap:8px;">
                            @foreach($permissions as $perm)
                            <label style="display:flex; align-items:center; gap:8px; font-size:12px; color:var(--text-primary); cursor:pointer; background:rgba(255,255,255,0.03); padding:6px 10px; border-radius:6px; border:1px solid rgba(255,255,255,0.05);">
                                <input type="checkbox" name="direct_permissions[]" value="{{ $perm->name }}" {{ in_array($perm->name, old('direct_permissions', $userDirectPermissions)) ? 'checked' : '' }}>
                                <span style="font-family:'JetBrains Mono',monospace;">{{ $perm->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:24px;">
                <a href="{{ route('admin.users.index') }}" class="topbar-btn topbar-btn-ghost" style="text-decoration:none;">Cancel</a>
                <button type="submit" class="topbar-btn topbar-btn-primary">Update User</button>
            </div>
        </form>
    </div>
</x-admin-layout>
