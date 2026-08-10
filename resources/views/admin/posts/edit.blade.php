<x-admin-layout title="Edit Artikel" breadcrumb="Perbarui konten artikel blog">
    <x-slot name="topbarAction">
        <a href="{{ route('admin.posts.index') }}" class="topbar-btn" style="background:var(--bg-elevated); color:var(--text-primary); border:1px solid var(--border); text-decoration:none;">
            ← Kembali
        </a>
    </x-slot>

    <div class="card" style="max-width:960px; margin:0 auto; padding:24px;">
        <form method="POST" action="{{ route('admin.posts.update', $post) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div style="display:grid; grid-template-columns: 2fr 1fr; gap:24px;">
                <!-- Main Form Body -->
                <div style="display:flex; flex-direction:column; gap:18px;">
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:var(--text-secondary); text-transform:uppercase; margin-bottom:8px;">Judul Artikel *</label>
                        <input type="text" name="title" value="{{ old('title', $post->title) }}" required style="width:100%; padding:10px 14px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:10px; color:var(--text-primary); font-size:14px;">
                        @error('title') <span style="color:#ef4444; font-size:12px; margin-top:4px; display:block;">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:var(--text-secondary); text-transform:uppercase; margin-bottom:8px;">Slug (URL Custom) *</label>
                        <input type="text" name="slug" value="{{ old('slug', $post->slug) }}" required style="width:100%; padding:10px 14px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:10px; color:var(--text-primary); font-size:13px; font-family:'JetBrains Mono', monospace;">
                        @error('slug') <span style="color:#ef4444; font-size:12px; margin-top:4px; display:block;">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:var(--text-secondary); text-transform:uppercase; margin-bottom:8px;">Ringkasan / Excerpt</label>
                        <textarea name="excerpt" rows="3" style="width:100%; padding:10px 14px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:10px; color:var(--text-primary); font-size:13.5px;">{{ old('excerpt', $post->excerpt) }}</textarea>
                    </div>

                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:var(--text-secondary); text-transform:uppercase; margin-bottom:8px;">Konten Artikel (Markdown / HTML) *</label>
                        <textarea name="content" rows="14" required style="width:100%; padding:12px 14px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:10px; color:var(--text-primary); font-size:14px; font-family:'Inter', sans-serif; line-height:1.6;">{{ old('content', $post->content) }}</textarea>
                        @error('content') <span style="color:#ef4444; font-size:12px; margin-top:4px; display:block;">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Sidebar Settings -->
                <div style="display:flex; flex-direction:column; gap:18px; background:var(--bg-surface); padding:20px; border-radius:16px; border:1px solid var(--border);">
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:var(--text-secondary); text-transform:uppercase; margin-bottom:8px;">Status *</label>
                        <select name="status" style="width:100%; padding:10px 14px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:10px; color:var(--text-primary); font-size:13.5px;">
                            <option value="draft" {{ old('status', $post->status) === 'draft' ? 'selected' : '' }}>✏️ Draft (Internal)</option>
                            <option value="published" {{ old('status', $post->status) === 'published' ? 'selected' : '' }}>⚡ Published (Public API)</option>
                        </select>
                    </div>

                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:var(--text-secondary); text-transform:uppercase; margin-bottom:8px;">Kategori</label>
                        <select name="category_id" style="width:100%; padding:10px 14px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:10px; color:var(--text-primary); font-size:13.5px;">
                            <option value="">-- Tanpa Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $post->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:var(--text-secondary); text-transform:uppercase; margin-bottom:8px;">Cover Image (Foto / Banner)</label>
                        
                        <!-- Upload File Foto -->
                        <div style="margin-bottom:10px;">
                            <label style="display:block; font-size:11px; color:var(--text-muted); margin-bottom:4px;">📷 Upload Foto File Baru (PNG, JPG, WEBP - Max 5MB):</label>
                            <input type="file" name="cover_file" accept="image/*" onchange="previewEditCover(this)" style="width:100%; padding:8px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:10px; color:var(--text-primary); font-size:12px;">
                            @error('cover_file') <span style="color:#ef4444; font-size:11px; margin-top:2px; display:block;">{{ $message }}</span> @enderror
                        </div>

                        <!-- Existing/New Image Preview -->
                        <div id="editCoverPreviewBox" style="margin-bottom:10px; {{ $post->cover_image ? '' : 'display:none;' }}">
                            <img id="editCoverPreviewImg" src="{{ $post->cover_image }}" alt="Cover Preview" style="max-height:120px; width:100%; object-fit:cover; border-radius:10px; border:1px solid var(--border);">
                        </div>

                        <!-- URL External Alternative -->
                        <div>
                            <label style="display:block; font-size:11px; color:var(--text-muted); margin-bottom:4px;">Atau Tempel URL Gambar External:</label>
                            <input type="text" name="cover_image" value="{{ old('cover_image', $post->cover_image) }}" placeholder="https://images.unsplash.com/photo-..." style="width:100%; padding:9px 12px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:10px; color:var(--text-primary); font-size:12.5px;">
                        </div>
                    </div>

                    <div style="padding-top:8px;">
                        <label style="display:inline-flex; align-items:center; gap:8px; cursor:pointer;">
                            <input type="checkbox" name="featured" value="1" {{ old('featured', $post->featured) ? 'checked' : '' }} style="accent-color:#b8ff00; width:16px; height:16px;">
                            <span style="font-size:13px; font-weight:600; color:var(--text-primary);">⭐ Tandai sebagai Featured</span>
                        </label>
                    </div>

                    <div style="border-top:1px solid var(--border); padding-top:16px; margin-top:8px;">
                        <span style="display:block; font-size:12px; font-weight:700; color:#b8ff00; text-transform:uppercase; margin-bottom:12px;">Meta SEO (Opsional)</span>
                        
                        <div style="margin-bottom:12px;">
                            <label style="display:block; font-size:11px; color:var(--text-secondary); margin-bottom:4px;">Meta Title</label>
                            <input type="text" name="meta_title" value="{{ old('meta_title', $post->meta_title) }}" style="width:100%; padding:8px 12px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:8px; color:var(--text-primary); font-size:12.5px;">
                        </div>

                        <div>
                            <label style="display:block; font-size:11px; color:var(--text-secondary); margin-bottom:4px;">Meta Keywords</label>
                            <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $post->meta_keywords) }}" style="width:100%; padding:8px 12px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:8px; color:var(--text-primary); font-size:12.5px;">
                        </div>
                    </div>

                    <div style="margin-top:16px;">
                        <button type="submit" class="topbar-btn topbar-btn-primary" style="width:100%; padding:12px; font-size:14px; font-weight:800; justify-content:center;">
                            Update Artikel →
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script>
        function previewEditCover(input) {
            const preview = document.getElementById('editCoverPreviewImg');
            const box = document.getElementById('editCoverPreviewBox');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    box.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-admin-layout>
