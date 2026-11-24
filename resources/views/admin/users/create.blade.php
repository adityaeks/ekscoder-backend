<x-admin-layout title="Add New User" breadcrumb="Create a new user account and assign a role">
    <x-slot name="topbarAction">
        <a href="{{ route('admin.users.index') }}" class="topbar-btn topbar-btn-ghost" style="text-decoration:none;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Back to Users
        </a>
    </x-slot>

    <div style="max-width:860px;">
        @if ($errors->any())
            <div style="background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.25); color:#f87171; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:13px;">
                @foreach ($errors->all() as $error)
                    <p>• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <!-- Basic Info Card -->
            <div class="card" style="margin-bottom:20px;">
                <div class="card-header">
                    <div>
                        <div class="card-title">User Information</div>
                        <div class="card-subtitle">Personal details and login credentials</div>
                    </div>
                </div>
                <div class="card-body">
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div class="form-group">
                            <label class="form-label" style="font-weight:600; margin-bottom:6px; display:block;">Full Name *</label>
                            <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. John Doe" class="form-input" style="width:100%;">
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-weight:600; margin-bottom:6px; display:block;">Email Address *</label>
                            <input type="email" name="email" value="{{ old('email') }}" required placeholder="john@ekscoder.com" class="form-input" style="width:100%;">
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-top:16px;">
                        <div class="form-group">
                            <label class="form-label" style="font-weight:600; margin-bottom:6px; display:block;">Password *</label>
                            <input type="password" name="password" required placeholder="Minimum 8 characters" class="form-input" style="width:100%;">
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-weight:600; margin-bottom:6px; display:block;">Assigned Role *</label>
                            <select name="role" required class="form-input" style="width:100%;">
                                <option value="">-- Select Role --</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ old('role') === $role->name ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div style="margin-top:20px; padding-top:16px; border-top:1px solid var(--border-color, rgba(255,255,255,0.08));">
                        <input type="hidden" name="otp_enabled_submitted" value="1">
                        <label style="display:flex; align-items:flex-start; gap:10px; cursor:pointer;">
                            <input type="checkbox" name="otp_enabled" value="1" {{ old('otp_enabled', '1') ? 'checked' : '' }} style="margin-top:3px; accent-color:var(--accent);">
                            <div>
                                <div style="font-weight:600; color:var(--text-primary); font-size:13px;">Wajibkan Verifikasi OTP saat Login</div>
                                <div style="font-size:12px; color:var(--text-secondary); margin-top:2px;">Jika tidak dicentang, pengguna ini dapat login langsung dengan password tanpa memerlukan kode OTP (Bypass OTP).</div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:24px;">
                <a href="{{ route('admin.users.index') }}" class="topbar-btn topbar-btn-ghost" style="text-decoration:none;">Cancel</a>
                <button type="submit" class="topbar-btn topbar-btn-primary">Create User</button>
            </div>
        </form>
    </div>
</x-admin-layout>
