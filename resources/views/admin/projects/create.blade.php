<x-admin-layout title="Create Project" breadcrumb="Add a new project to the database">
    <x-slot name="topbarAction">
        <a href="{{ route('admin.projects.index') }}" class="topbar-btn topbar-btn-ghost">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Back to Projects
        </a>
    </x-slot>

    <div style="max-width:860px;">
        @if ($errors->any())
            <div class="error-list">
                @foreach ($errors->all() as $error)
                    <p>• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('admin.projects.store') }}" method="POST">
            @csrf

            <!-- Basic Info Card -->
            <div class="card" style="margin-bottom:20px;">
                <div class="card-header">
                    <div>
                        <div class="card-title">Basic Information</div>
                        <div class="card-subtitle">Core project identification and metadata</div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Project ID / Slug *</label>
                            <input type="text" name="id" value="{{ old('id') }}" required placeholder="erp-system" class="form-input">
                            <div class="form-hint">Lowercase with hyphens. e.g. erp-system, vps-control</div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Project Number *</label>
                            <input type="text" name="number" value="{{ old('number', '07') }}" required placeholder="07" class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Title *</label>
                            <input type="text" name="title" value="{{ old('title') }}" required placeholder="AI SMART DASHBOARD" class="form-input">
                            <div class="form-hint">Will be displayed in uppercase on the landing page</div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Category *</label>
                            <input type="text" name="category" value="{{ old('category') }}" required placeholder="Web Platform" class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Year *</label>
                            <input type="text" name="year" value="{{ old('year', date('Y')) }}" required placeholder="2026" class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Sort Order</label>
                            <input type="number" name="order" value="{{ old('order', 7) }}" class="form-input">
                            <div class="form-hint">Lower number = displayed first</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description *</label>
                        <textarea name="description" required placeholder="Describe what this project does..." class="form-textarea">{{ old('description') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Technologies (Comma-separated) *</label>
                        <input type="text" name="technologies" value="{{ old('technologies') }}" required placeholder="Next.js, TypeScript, Laravel, PostgreSQL" class="form-input">
                        <div class="form-hint">Separate each technology with a comma</div>
                    </div>
                </div>
            </div>

            <!-- Visual Config Card -->
            <div class="card" style="margin-bottom:20px;">
                <div class="card-header">
                    <div>
                        <div class="card-title">Visual Configuration</div>
                        <div class="card-subtitle">Gradient and accent color for the landing page card</div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Image Gradient Class *</label>
                            <input type="text" name="image_bg" value="{{ old('image_bg', 'from-emerald-900/40 via-neutral-900 to-black') }}" required class="form-input">
                            <div class="form-hint">Tailwind gradient class e.g. from-cyan-900/40 via-neutral-900 to-black</div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Accent Color (Hex) *</label>
                            <div style="display:flex; gap:8px; align-items:center;">
                                <input type="color" name="accent_color_picker" value="{{ old('accent_color', '#10B981') }}" oninput="document.getElementById('accent_color_input').value = this.value" style="width:42px; height:38px; padding:2px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:8px; cursor:pointer;">
                                <input type="text" name="accent_color" id="accent_color_input" value="{{ old('accent_color', '#10B981') }}" required class="form-input">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Project Link (Optional)</label>
                        <input type="url" name="link" value="{{ old('link') }}" placeholder="https://example.com" class="form-input">
                    </div>
                </div>
            </div>

            <!-- Settings Card -->
            <div class="card" style="margin-bottom:24px;">
                <div class="card-header">
                    <div class="card-title">Visibility Settings</div>
                </div>
                <div class="card-body" style="display:flex; gap:32px;">
                    <label class="form-check">
                        <input type="checkbox" name="featured" value="1" {{ old('featured') ? 'checked' : '' }}>
                        <span class="form-check-label">Featured Project <span style="color:var(--text-muted); font-size:11px;">(shown with ★ badge)</span></span>
                    </label>
                    <label class="form-check">
                        <input type="checkbox" name="is_active" value="1" checked>
                        <span class="form-check-label">Active — Visible in API <span style="color:var(--text-muted); font-size:11px;">(served to Next.js)</span></span>
                    </label>
                </div>
            </div>

            <!-- Actions -->
            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <a href="{{ route('admin.projects.index') }}" class="btn btn-ghost" style="padding:10px 20px;">Cancel</a>
                <button type="submit" class="btn btn-primary" style="padding:10px 24px;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Save Project
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
