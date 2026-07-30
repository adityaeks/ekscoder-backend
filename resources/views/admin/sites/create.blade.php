<x-admin-layout title="Add Monitored Website" breadcrumb="Add a new URL to monitor uptime, HTTP response, and SSL status">
    <x-slot name="topbarAction">
        <a href="{{ route('admin.sites.index') }}" class="topbar-btn topbar-btn-ghost" style="text-decoration:none;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Back to Monitoring
        </a>
    </x-slot>

    <div style="max-width:820px;">
        @if ($errors->any())
            <div style="background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.25); color:#f87171; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:13px;">
                @foreach ($errors->all() as $error)
                    <p>• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('admin.sites.store') }}" method="POST">
            @csrf

            <!-- Site Configuration Card -->
            <div class="card" style="margin-bottom:20px;">
                <div class="card-header">
                    <div>
                        <div class="card-title">Website Configuration</div>
                        <div class="card-subtitle">Specify website identity and target URL</div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label" style="font-weight:600; margin-bottom:6px; display:block;">Website / App Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Main Portfolio Website, Payment API" class="form-input" style="width:100%;">
                    </div>

                    <div class="form-group" style="margin-top:16px;">
                        <label class="form-label" style="font-weight:600; margin-bottom:6px; display:block;">Target URL *</label>
                        <input type="url" name="url" value="{{ old('url') }}" required placeholder="https://ekscoder.com" class="form-input" style="width:100%;">
                        <div class="form-hint" style="margin-top:6px;">Must include protocol (e.g. <code>https://example.com</code> or <code>http://api.domain.com</code>).</div>
                    </div>

                    <div class="form-group" style="margin-top:16px;">
                        <label class="form-label" style="font-weight:600; margin-bottom:6px; display:block;">Check Frequency (Interval in Minutes) *</label>
                        <select name="check_interval" required class="form-input" style="width:100%;">
                            <option value="1" {{ old('check_interval') == 1 ? 'selected' : '' }}>Every 1 Minute (High Priority)</option>
                            <option value="5" {{ old('check_interval', 5) == 5 ? 'selected' : '' }}>Every 5 Minutes (Standard - Recommended)</option>
                            <option value="15" {{ old('check_interval') == 15 ? 'selected' : '' }}>Every 15 Minutes</option>
                            <option value="30" {{ old('check_interval') == 30 ? 'selected' : '' }}>Every 30 Minutes</option>
                            <option value="60" {{ old('check_interval') == 60 ? 'selected' : '' }}>Every 60 Minutes (1 Hour)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:24px;">
                <a href="{{ route('admin.sites.index') }}" class="topbar-btn topbar-btn-ghost" style="text-decoration:none;">Cancel</a>
                <button type="submit" class="topbar-btn topbar-btn-primary">Save & Start Monitoring</button>
            </div>
        </form>
    </div>
</x-admin-layout>
