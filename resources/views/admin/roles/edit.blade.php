<x-admin-layout title="Edit Role: {{ $role->name }}" breadcrumb="Update role details and module permissions">
    <x-slot name="topbarAction">
        <a href="{{ route('admin.roles.index') }}" class="topbar-btn topbar-btn-ghost" style="text-decoration:none;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Back to Roles
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

        <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Role Info Card -->
            <div class="card" style="margin-bottom:20px;">
                <div class="card-header">
                    <div>
                        <div class="card-title">Role Detail</div>
                        <div class="card-subtitle">Specify the role title</div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label" style="font-weight:600; margin-bottom:6px; display:block;">Role Name *</label>
                        <input type="text" name="name" value="{{ old('name', $role->name) }}" {{ $role->name === 'Super Admin' ? 'readonly' : '' }} required class="form-input" style="width:100%;">
                        @if($role->name === 'Super Admin')
                            <div style="font-size:12px; color:#ef4444; margin-top:4px;">Super Admin role name cannot be modified.</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Permissions Grouped Card -->
            <div class="card" style="margin-bottom:20px;">
                <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        <div class="card-title">Module Permissions</div>
                        <div class="card-subtitle">Check permissions allowed for this role</div>
                    </div>
                    <button type="button" onclick="toggleAllPermissions(true)" class="topbar-btn topbar-btn-ghost" style="font-size:12px; padding:4px 10px;">Select All Permissions</button>
                </div>
                <div class="card-body" style="display:flex; flex-direction:column; gap:20px;">
                    @foreach($groupedPermissions as $module => $permissions)
                    <div style="border:1px solid var(--border-color, rgba(255,255,255,0.08)); border-radius:8px; padding:16px; background:var(--bg-surface, rgba(255,255,255,0.02));">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; border-bottom:1px solid var(--border-color, rgba(255,255,255,0.05)); padding-bottom:8px;">
                            <div style="font-weight:700; color:var(--text-primary); font-size:14px; text-transform:uppercase; letter-spacing:0.5px;">
                                📦 {{ $module }} Module
                            </div>
                            <label style="font-size:12px; color:var(--text-secondary); cursor:pointer;">
                                <input type="checkbox" onchange="toggleModuleGroup('group-{{ Str::slug($module) }}', this.checked)"> Toggle Group
                            </label>
                        </div>
                        <div class="group-{{ Str::slug($module) }}" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(220px, 1fr)); gap:10px;">
                            @foreach($permissions as $perm)
                            <label style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--text-primary); cursor:pointer; background:rgba(255,255,255,0.03); padding:8px 12px; border-radius:6px; border:1px solid rgba(255,255,255,0.05);">
                                <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" class="perm-checkbox" {{ in_array($perm->name, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
                                <span style="font-family:'JetBrains Mono',monospace; font-size:12px;">{{ $perm->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:24px;">
                <a href="{{ route('admin.roles.index') }}" class="topbar-btn topbar-btn-ghost" style="text-decoration:none;">Cancel</a>
                <button type="submit" class="topbar-btn topbar-btn-primary">Update Role</button>
            </div>
        </form>
    </div>

    <script>
        function toggleModuleGroup(groupClass, isChecked) {
            document.querySelectorAll('.' + groupClass + ' .perm-checkbox').forEach(cb => cb.checked = isChecked);
        }
        function toggleAllPermissions(isChecked) {
            document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = isChecked);
        }
    </script>
</x-admin-layout>
