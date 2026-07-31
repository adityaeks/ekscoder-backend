<x-admin-layout title="Edit Project" breadcrumb="Editing: {{ $project->title }}">
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

        <!-- Project Identifier Bar -->
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:20px; padding:12px 16px; background:var(--bg-surface); border:1px solid var(--border); border-radius:12px;">
            <div style="width:38px; height:38px; border-radius:10px; background:var(--accent-soft); display:flex; align-items:center; justify-content:center; font-family:'JetBrains Mono',monospace; font-weight:700; font-size:14px; color:#a09af8;">
                {{ $project->number }}
            </div>
            <div>
                <div style="font-weight:700; color:var(--text-primary); font-size:14px;">{{ $project->title }}</div>
                <div style="font-family:'JetBrains Mono',monospace; font-size:11px; color:var(--text-muted);">{{ $project->slug }}</div>
            </div>
            <div style="margin-left:auto; display:flex; gap:8px;">
                <span class="badge {{ $project->is_active ? 'badge-green' : 'badge-rose' }}">
                    <span class="badge-dot"></span>
                    {{ $project->is_active ? 'Active' : 'Inactive' }}
                </span>
                @if($project->featured)
                    <span class="badge badge-amber">★ Featured</span>
                @endif
            </div>
        </div>

        <form action="{{ route('admin.projects.update', $project->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Basic Info Card -->
            <div class="card" style="margin-bottom:20px;">
                <div class="card-header">
                    <div>
                        <div class="card-title">Basic Information</div>
                        <div class="card-subtitle">Update project details and slug</div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Slug *</label>
                            <input type="text" name="slug" value="{{ old('slug', $project->slug) }}" required class="form-input">
                            <div class="form-hint">Lowercase with hyphens. e.g. erp-system, vps-control</div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Project Number *</label>
                            <input type="text" name="number" value="{{ old('number', $project->number) }}" required class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Title *</label>
                            <input type="text" name="title" value="{{ old('title', $project->title) }}" required class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Category *</label>
                            <input type="text" name="category" value="{{ old('category', $project->category) }}" required class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Year *</label>
                            <input type="text" name="year" value="{{ old('year', $project->year) }}" required class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Sort Order</label>
                            <input type="number" name="order" value="{{ old('order', $project->order) }}" class="form-input">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description *</label>
                        <textarea name="description" required class="form-textarea">{{ old('description', $project->description) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Technologies (Comma-separated) *</label>
                        <input type="text" name="technologies" value="{{ old('technologies', is_array($project->technologies) ? implode(', ', $project->technologies) : $project->technologies) }}" required class="form-input">
                    </div>
                </div>
            </div>

            <!-- Visual Config Card -->
            <div class="card" style="margin-bottom:20px;">
                <div class="card-header">
                    <div>
                        <div class="card-title">Visual Configuration</div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Image Gradient Class *</label>
                            <input type="text" name="image_bg" value="{{ old('image_bg', $project->image_bg) }}" required class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Accent Color (Hex) *</label>
                            <div style="display:flex; gap:8px; align-items:center;">
                                <input type="color" value="{{ old('accent_color', $project->accent_color) }}" oninput="document.getElementById('edit_accent_input').value = this.value" style="width:42px; height:38px; padding:2px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:8px; cursor:pointer;">
                                <input type="text" name="accent_color" id="edit_accent_input" value="{{ old('accent_color', $project->accent_color) }}" required class="form-input">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Project Link (Optional)</label>
                        <input type="url" name="link" value="{{ old('link', $project->link) }}" placeholder="https://example.com" class="form-input">
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
                        <input type="checkbox" name="featured" value="1" {{ old('featured', $project->featured) ? 'checked' : '' }}>
                        <span class="form-check-label">Featured Project</span>
                    </label>
                    <label class="form-check">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $project->is_active) ? 'checked' : '' }}>
                        <span class="form-check-label">Active — Visible in API</span>
                    </label>
                </div>
            </div>

            <!-- Actions -->
            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <a href="{{ route('admin.projects.index') }}" class="btn btn-ghost" style="padding:10px 20px;">Cancel</a>
                <button type="submit" class="btn btn-primary" style="padding:10px 24px;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Update Project
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
