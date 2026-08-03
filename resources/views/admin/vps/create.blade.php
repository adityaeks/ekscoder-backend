<x-admin-layout title="Add New VPS Server" breadcrumb="Register a Linux VPS to receive automatic health & resource monitoring metrics">
    <div style="max-width: 680px; margin: 0 auto;">
        <div class="card" style="padding: 28px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; padding-bottom:16px; border-bottom:1px solid var(--border);">
                <div>
                    <h3 style="margin:0; font-size:18px; font-weight:700; color:var(--text-primary);">🖥️ VPS Server Details</h3>
                    <p style="margin:4px 0 0; font-size:13px; color:var(--text-secondary);">Enter identification details for your server.</p>
                </div>
                <a href="{{ route('admin.vps.index') }}" class="btn btn-ghost" style="font-size:13px;">← Back to List</a>
            </div>

            <form action="{{ route('admin.vps.store') }}" method="POST">
                @csrf

                <div style="margin-bottom: 20px;">
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:8px;">Server Name <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Production Web VPS 01" required style="width:100%; padding:10px 14px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:8px; color:var(--text-primary); font-size:14px;">
                    @error('name')
                        <span style="color:#ef4444; font-size:12px; margin-top:4px; display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:8px;">IP Address (Optional)</label>
                    <input type="text" name="ip_address" value="{{ old('ip_address') }}" placeholder="e.g. 103.150.190.12 or 203.0.113.195" style="width:100%; padding:10px 14px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:8px; color:var(--text-primary); font-size:14px; font-family:'JetBrains Mono',monospace;">
                    <span style="font-size:12px; color:var(--text-muted); margin-top:4px; display:block;">If left empty, IP address will be auto-detected when the agent sends its first ping.</span>
                    @error('ip_address')
                        <span style="color:#ef4444; font-size:12px; margin-top:4px; display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:8px;">Check Interval (Minutes) <span style="color:#ef4444;">*</span></label>
                    <select name="check_interval" required style="width:100%; padding:10px 14px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:8px; color:var(--text-primary); font-size:14px;">
                        <option value="1" {{ old('check_interval') == '1' ? 'selected' : '' }}>Every 1 Minute (High Precision)</option>
                        <option value="5" {{ old('check_interval', '5') == '5' ? 'selected' : '' }}>Every 5 Minutes (Standard - Recommended)</option>
                        <option value="10" {{ old('check_interval') == '10' ? 'selected' : '' }}>Every 10 Minutes</option>
                        <option value="15" {{ old('check_interval') == '15' ? 'selected' : '' }}>Every 15 Minutes</option>
                        <option value="30" {{ old('check_interval') == '30' ? 'selected' : '' }}>Every 30 Minutes</option>
                    </select>
                    @error('check_interval')
                        <span style="color:#ef4444; font-size:12px; margin-top:4px; display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="display:flex; justify-content:flex-end; gap:12px; padding-top:16px; border-top:1px solid var(--border);">
                    <a href="{{ route('admin.vps.index') }}" class="btn btn-ghost">Cancel</a>
                    <button type="submit" class="topbar-btn topbar-btn-primary" style="padding:10px 20px; font-size:14px;">
                        Register Server & Get Install Command →
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
