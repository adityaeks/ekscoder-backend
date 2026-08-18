<x-admin-layout title="Kategori Blog" breadcrumb="Kelola kategori artikel blog">
    <div style="margin-bottom:18px;">
        <a href="{{ route('admin.posts.index') }}" class="topbar-btn topbar-btn-ghost" style="display:inline-flex; align-items:center; gap:8px; padding:8px 16px; border-radius:10px; text-decoration:none; font-weight:600; font-size:13px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><polyline points="12 19 5 12 12 5"/></svg>
            Kembali ke Artikel
        </a>
    </div>

    <div style="display:grid; grid-template-columns: 1fr 2fr; gap:24px;">
        <!-- Form Add Category -->
        <div class="card" style="padding:24px;">
            <h3 style="font-size:16px; font-weight:800; color:var(--text-primary); margin-bottom:16px;">Tambah Kategori</h3>

            <form method="POST" action="{{ route('admin.blog-categories.store') }}" style="display:flex; flex-direction:column; gap:14px;">
                @csrf

                <div>
                    <label style="display:block; font-size:12px; font-weight:700; color:var(--text-secondary); text-transform:uppercase; margin-bottom:6px;">Nama Kategori *</label>
                    <input type="text" name="name" required placeholder="Contoh: Web Development" style="width:100%; padding:10px 14px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:10px; color:var(--text-primary); font-size:13.5px;">
                </div>

                <div>
                    <label style="display:block; font-size:12px; font-weight:700; color:var(--text-secondary); text-transform:uppercase; margin-bottom:6px;">Slug (URL Custom)</label>
                    <input type="text" name="slug" placeholder="web-development" style="width:100%; padding:10px 14px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:10px; color:var(--text-primary); font-size:13px; font-family:'JetBrains Mono', monospace;">
                </div>

                <div>
                    <label style="display:block; font-size:12px; font-weight:700; color:var(--text-secondary); text-transform:uppercase; margin-bottom:6px;">Warna Label Badge</label>
                    <div style="display:flex; gap:10px; align-items:center;">
                        <input type="color" name="color" value="#b8ff00" style="width:40px; height:40px; padding:2px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:8px; cursor:pointer;">
                        <span style="font-size:12px; color:var(--text-secondary);">Pilih warna penanda kategori</span>
                    </div>
                </div>

                <div>
                    <label style="display:block; font-size:12px; font-weight:700; color:var(--text-secondary); text-transform:uppercase; margin-bottom:6px;">Deskripsi</label>
                    <textarea name="description" rows="3" placeholder="Deskripsi singkat mengenai kategori ini..." style="width:100%; padding:10px 14px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:10px; color:var(--text-primary); font-size:13px;"></textarea>
                </div>

                <button type="submit" class="topbar-btn topbar-btn-primary" style="padding:10px 16px; font-size:13.5px; font-weight:800; justify-content:center; margin-top:6px;">
                    Simpan Kategori
                </button>
            </form>
        </div>

        <!-- Table List Categories -->
        <div class="card" style="padding:0; overflow:hidden;">
            <div style="padding:20px 24px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
                <h3 style="font-size:16px; font-weight:800; color:var(--text-primary);">Daftar Kategori</h3>
                <span style="font-size:12px; color:var(--text-muted);">Total: {{ $categories->count() }}</span>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Kategori</th>
                        <th>Slug</th>
                        <th>Total Artikel</th>
                        <th style="width:100px; text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                    <tr>
                        <td>
                            <div style="display:flex; align-items:center; gap:8px;">
                                <span style="width:12px; height:12px; border-radius:50%; background:{{ $cat->color }}; display:inline-block;"></span>
                                <span style="font-weight:700; color:var(--text-primary); font-size:13.5px;">{{ $cat->name }}</span>
                            </div>
                            @if($cat->description)
                                <div style="font-size:11.5px; color:var(--text-muted); margin-top:2px;">{{ \Illuminate\Support\Str::limit($cat->description, 50) }}</div>
                            @endif
                        </td>
                        <td>
                            <span style="font-family:'JetBrains Mono', monospace; font-size:12px; color:var(--text-secondary);">{{ $cat->slug }}</span>
                        </td>
                        <td>
                            <span class="badge badge-accent" style="font-family:'JetBrains Mono', monospace;">{{ $cat->posts_count }} artikel</span>
                        </td>
                        <td style="text-align:right;">
                            @can('posts.delete')
                            <form method="POST" action="{{ route('admin.blog-categories.destroy', $cat) }}" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn text-danger" title="Hapus Kategori" style="background:none; border:none; cursor:pointer;" {{ $cat->posts_count > 0 ? 'disabled style=opacity:0.4;cursor:not-allowed;' : '' }}>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                </button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center; padding:32px; color:var(--text-muted);">
                            Belum ada kategori blog.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
