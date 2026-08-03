<x-admin-layout title="Edit VPS Server: {{ $vps->name }}" breadcrumb="Update server parameters and active state">
    <div style="max-width: 680px; margin: 0 auto;">
        <div class="card" style="padding: 28px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; padding-bottom:16px; border-bottom:1px solid var(--border);">
                <div>
                    <h3 style="margin:0; font-size:18px; font-weight:700; color:var(--text-primary);">✏️ Edit VPS Settings</h3>
                    <p style="margin:4px 0 0; font-size:13px; color:var(--text-secondary);">Modify configuration for {{ $vps->name }}</p>
                </div>
                <a href="{{ route('admin.vps.show', $vps->id) }}" class="btn btn-ghost" style="font-size:13px;">← Back to Server</a>
            </div>

            <form action="{{ route('admin.vps.update', $vps->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div style="margin-bottom: 20px;">
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:8px;">Server Name <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $vps->name) }}" required style="width:100%; padding:10px 14px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:8px; color:var(--text-primary); font-size:14px;">
                    @error('name')
                        <span style="color:#ef4444; font-size:12px; margin-top:4px; display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:8px;">IP Address</label>
                    <input type="text" name="ip_address" value="{{ old('ip_address', $vps->ip_address) }}" placeholder="e.g. 103.150.190.12" style="width:100%; padding:10px 14px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:8px; color:var(--text-primary); font-size:14px; font-family:'JetBrains Mono',monospace;">
                    @error('ip_address')
                        <span style="color:#ef4444; font-size:12px; margin-top:4px; display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:8px;">Check Interval (Minutes)</label>
                    <select name="check_interval" required style="width:100%; padding:10px 14px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:8px; color:var(--text-primary); font-size:14px;">
                        <option value="1" {{ old('check_interval', $vps->check_interval) == 1 ? 'selected' : '' }}>Every 1 Minute</option>
                        <option value="5" {{ old('check_interval', $vps->check_interval) == 5 ? 'selected' : '' }}>Every 5 Minutes (Standard)</option>
                        <option value="10" {{ old('check_interval', $vps->check_interval) == 10 ? 'selected' : '' }}>Every 10 Minutes</option>
                        <option value="15" {{ old('check_interval', $vps->check_interval) == 15 ? 'selected' : '' }}>Every 15 Minutes</option>
                        <option value="30" {{ old('check_interval', $vps->check_interval) == 30 ? 'selected' : '' }}>Every 30 Minutes</option>
                    </select>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="display:inline-flex; align-items:center; gap:8px; cursor:pointer; font-size:14px; color:var(--text-primary);">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $vps->is_active) ? 'checked' : '' }} style="width:18px; height:18px; accent-color:var(--accent);">
                        Enable Monitoring Active State
                    </label>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:12px; padding-top:16px; border-top:1px solid var(--border);">
                    <a href="{{ route('admin.vps.show', $vps->id) }}" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="topbar-btn topbar-btn-primary" style="padding:10px 20px; font-size:14px;">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
