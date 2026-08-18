<x-admin-layout title="Blog & Artikel" breadcrumb="Kelola konten artikel blog dan kategori">


    <!-- Stats Row -->
    <div class="stats-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom:24px;">
        <div class="stat-card accent">
            <div class="stat-top">
                <div class="stat-label">Total Artikel</div>
                <div class="stat-icon accent">📝</div>
            </div>
            <div class="stat-value">{{ $stats['total'] }}</div>
        </div>
        <div class="stat-card green">
            <div class="stat-top">
                <div class="stat-label">Published (API)</div>
                <div class="stat-icon green">⚡</div>
            </div>
            <div class="stat-value">{{ $stats['published'] }}</div>
        </div>
        <div class="stat-card amber">
            <div class="stat-top">
                <div class="stat-label">Draft</div>
                <div class="stat-icon amber">✏️</div>
            </div>
            <div class="stat-value">{{ $stats['draft'] }}</div>
        </div>
        <div class="stat-card purple">
            <div class="stat-top">
                <div class="stat-label">Featured</div>
                <div class="stat-icon purple">⭐</div>
            </div>
            <div class="stat-value">{{ $stats['featured'] }}</div>
        </div>
    </div>

    <!-- Filters & Table Card -->
    <div class="card">
        <x-datatable id="postsDataTable" title="Daftar Artikel Blog" subtitle="Kelola artikel blog untuk API Frontend" search-placeholder="Cari artikel..." :per-page-options="[10, 20, 50, 'all']" :default-per-page="10">
            <x-slot:actions>
                <div style="display:flex; gap:10px;">
                    <a href="{{ route('admin.blog-categories.index') }}" class="topbar-btn" style="padding:6px 12px; font-size:12px; background:var(--bg-elevated); border:1px solid var(--border); color:var(--text-primary); text-decoration:none;">
                        Kategori
                    </a>
                    @can('posts.create')
                    <a href="{{ route('admin.posts.create') }}" class="topbar-btn topbar-btn-primary" style="padding:6px 12px; font-size:12px; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Buat Artikel
                    </a>
                    @endcan
                </div>
            </x-slot:actions>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:50px">Cover</th>
                        <th>Judul Artikel</th>
                        <th>Kategori</th>
                        <th>Views</th>
                        <th>Status</th>
                        <th>Featured</th>
                        <th>Tanggal Publish</th>
                        <th style="width:120px; text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($posts as $post)
                    <tr>
                        <td>
                            @if($post->cover_image)
                                <img src="{{ $post->cover_image }}" alt="Cover" style="width:40px; height:40px; border-radius:8px; object-fit:cover; border:1px solid var(--border);">
                            @else
                                <div style="width:40px; height:40px; border-radius:8px; background:var(--bg-elevated); display:flex; align-items:center; justify-content:center; color:var(--text-muted); font-size:16px;">📰</div>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight:700; color:var(--text-primary); font-size:13.5px; margin-bottom:2px;">{{ $post->title }}</div>
                            <div style="font-family:'JetBrains Mono', monospace; font-size:11px; color:var(--text-muted);">/{{ $post->slug }}</div>
                        </td>
                        <td>
                            @if($post->category)
                                <span style="display:inline-block; padding:3px 10px; border-radius:999px; font-size:11.5px; font-weight:600; background:{{ $post->category->color }}15; color:{{ $post->category->color }}; border:1px solid {{ $post->category->color }}40;">
                                    {{ $post->category->name }}
                                </span>
                            @else
                                <span style="color:var(--text-muted); font-size:12px;">Uncategorized</span>
                            @endif
                        </td>
                        <td>
                            <span style="font-family:'JetBrains Mono', monospace; font-size:12px; color:var(--text-secondary);">👁️ {{ number_format($post->views_count) }}</span>
                        </td>
                        <td>
                            @can('posts.publish')
                            <form method="POST" action="{{ route('admin.posts.toggle-publish', $post) }}" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" style="background:none; border:none; cursor:pointer; padding:0;">
                                    @if($post->status === 'published')
                                        <span class="badge badge-success" style="cursor:pointer;" title="Klik untuk ubah ke Draft">Published</span>
                                    @else
                                        <span class="badge badge-amber" style="cursor:pointer;" title="Klik untuk Publish">Draft</span>
                                    @endif
                                </button>
                            </form>
                            @else
                                @if($post->status === 'published')
                                    <span class="badge badge-success">Published</span>
                                @else
                                    <span class="badge badge-amber">Draft</span>
                                @endif
                            @endcan
                        </td>
                        <td>
                            @can('posts.edit')
                            <form method="POST" action="{{ route('admin.posts.toggle-featured', $post) }}" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" style="background:none; border:none; cursor:pointer; padding:0;">
                                    @if($post->featured)
                                        <span style="color:#f59e0b; font-size:16px;" title="Featured (Klik untuk un-feature)">⭐</span>
                                    @else
                                        <span style="color:var(--text-muted); font-size:16px;" title="Not Featured (Klik untuk feature)">☆</span>
                                    @endif
                                </button>
                            </form>
                            @else
                                @if($post->featured)
                                    <span style="color:#f59e0b; font-size:16px;">⭐</span>
                                @else
                                    <span style="color:var(--text-muted); font-size:16px;">☆</span>
                                @endif
                            @endcan
                        </td>
                        <td>
                            <span style="font-size:12px; color:var(--text-secondary);">
                                {{ $post->published_at ? $post->published_at->format('d M Y, H:i') : '-' }}
                            </span>
                        </td>
                        <td style="text-align:right;">
                            <div style="display:flex; justify-content:flex-end; gap:6px;">
                                <a href="/api/posts/{{ $post->slug }}" target="_blank" class="action-btn" title="API JSON Preview" style="color:#38bdf8; text-decoration:none;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                                </a>
                                @can('posts.edit')
                                <a href="{{ route('admin.posts.edit', $post) }}" class="action-btn" title="Edit Artikel" style="color:var(--text-primary); text-decoration:none;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </a>
                                @endcan
                                @can('posts.delete')
                                <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus artikel ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn text-danger" title="Hapus Artikel" style="background:none; border:none; cursor:pointer;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align:center; padding:32px; color:var(--text-muted);">
                            Belum ada artikel blog. Klik "Buat Artikel" untuk menambahkan artikel pertama.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </x-datatable>
    </div>
</x-admin-layout>
