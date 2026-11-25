<x-admin-layout title="Riwayat Chat AI - {{ $e_modul->title }}" breadcrumb="Log percakapan antara siswa dan Asisten AI pada e-modul">
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <div style="max-width:1100px; margin:0 auto;">
        <!-- Header Card -->
        <div class="card" style="padding:20px 24px; margin-bottom:20px; border:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">
            <div>
                <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                    <h2 style="font-size:18px; font-weight:700; color:var(--text-primary); margin:0;">
                        💬 Riwayat Percakapan Siswa & AI
                    </h2>
                    <span class="badge" style="background:var(--accent-soft); color:var(--accent); font-size:11px; padding:3px 10px; border-radius:999px; font-weight:600;">
                        {{ $e_modul->category ?? 'Umum' }}
                    </span>
                </div>
                <p style="font-size:13px; color:var(--text-secondary); margin:6px 0 0 0;">
                    Modul: <strong style="color:var(--text-primary);">{{ $e_modul->title }}</strong>
                </p>
            </div>

            <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                <a href="{{ route('admin.e-modul.index') }}" class="topbar-btn" style="padding:8px 14px; font-size:13px; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                    Kembali
                </a>

                <a href="{{ route('admin.e-modul.show', $e_modul->id) }}" target="_blank" class="topbar-btn" style="padding:8px 14px; font-size:13px; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                    Buka Modul
                </a>

                @can('emodul.delete')
                    @if($stats['total_chats'] > 0)
                    <form action="{{ route('admin.e-modul.chat-history.clear', $e_modul->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus seluruh riwayat percakapan siswa & AI pada modul ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="topbar-btn" style="padding:8px 14px; font-size:13px; color:var(--rose); border:1px solid var(--border); background:var(--bg-elevated); cursor:pointer; display:inline-flex; align-items:center; gap:6px; border-radius:8px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                            Hapus Semua
                        </button>
                    </form>
                    @endif
                @endcan
            </div>
        </div>

        <!-- Summary Stats -->
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:14px; margin-bottom:20px;">
            <div class="card" style="padding:16px 20px; border:1px solid var(--border);">
                <div style="font-size:12px; color:var(--text-muted); margin-bottom:4px;">Total Percakapan</div>
                <div style="font-size:24px; font-weight:700; color:var(--text-primary);">{{ number_format($stats['total_chats']) }}</div>
            </div>
            <div class="card" style="padding:16px 20px; border:1px solid var(--border);">
                <div style="font-size:12px; color:var(--text-muted); margin-bottom:4px;">Siswa Unik Bertanya</div>
                <div style="font-size:24px; font-weight:700; color:var(--accent);">{{ number_format($stats['unique_students']) }}</div>
            </div>
            <div class="card" style="padding:16px 20px; border:1px solid var(--border);">
                <div style="font-size:12px; color:var(--text-muted); margin-bottom:4px;">Aktivitas Terakhir</div>
                <div style="font-size:15px; font-weight:600; color:var(--text-primary); margin-top:6px;">
                    @if($stats['last_chat_at'])
                        {{ \Carbon\Carbon::parse($stats['last_chat_at'])->diffForHumans() }}
                    @else
                        Belum ada aktivitas
                    @endif
                </div>
            </div>
        </div>

        <!-- Filter & Search -->
        <div class="card" style="padding:16px 20px; margin-bottom:20px; border:1px solid var(--border);">
            <form method="GET" action="{{ route('admin.e-modul.chat-history', $e_modul->id) }}" style="display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
                <div style="flex:1; min-width:220px;">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama siswa, pertanyaan, jawaban AI, atau halaman..." style="width:100%; background:var(--bg-elevated); border:1px solid var(--border); border-radius:8px; padding:9px 14px; font-size:13px; color:var(--text-primary); outline:none;">
                </div>

                <div style="min-width:200px;">
                    <select name="student_name" style="width:100%; background:var(--bg-elevated); border:1px solid var(--border); border-radius:8px; padding:9px 14px; font-size:13px; color:var(--text-primary); outline:none;">
                        <option value="">Semua Siswa ({{ $students->count() }})</option>
                        @foreach($students as $student)
                            <option value="{{ $student }}" {{ $studentFilter === $student ? 'selected' : '' }}>{{ $student }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="display:flex; gap:8px;">
                    <button type="submit" class="topbar-btn topbar-btn-primary" style="padding:9px 16px; font-size:13px; border:none; cursor:pointer;">
                        Filter
                    </button>
                    @if($search !== '' || $studentFilter !== '')
                        <a href="{{ route('admin.e-modul.chat-history', $e_modul->id) }}" class="topbar-btn" style="padding:9px 14px; font-size:13px; text-decoration:none;">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Chat Logs List -->
        @if($chatLogs->count() > 0)
            <div style="display:flex; flex-direction:column; gap:16px; margin-bottom:24px;">
                @foreach($chatLogs as $log)
                    <div class="card" style="padding:18px 20px; border:1px solid var(--border);">
                        <!-- Meta Header -->
                        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; padding-bottom:12px; margin-bottom:14px; border-bottom:1px solid var(--border);">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="width:32px; height:32px; border-radius:50%; background:var(--accent-soft); color:var(--accent); font-weight:700; font-size:13px; display:flex; align-items:center; justify-content:center;">
                                    {{ strtoupper(mb_substr($log->student_name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-size:13.5px; font-weight:700; color:var(--text-primary);">{{ $log->student_name }}</div>
                                    <div style="font-size:11.5px; color:var(--text-muted);">
                                        {{ $log->created_at->format('d M Y, H:i') }} ({{ $log->created_at->diffForHumans() }})
                                    </div>
                                </div>
                            </div>

                            <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                                @if($log->page_number)
                                    <span style="font-size:11px; font-weight:600; padding:3px 9px; border-radius:6px; background:var(--bg-elevated); border:1px solid var(--border-light); color:var(--text-primary);">
                                        📄 {{ $log->page_number }}
                                    </span>
                                @endif
                                @if($log->ip_address)
                                    <span style="font-size:11px; color:var(--text-muted); font-family:'JetBrains Mono', monospace;">
                                        {{ $log->ip_address }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Student Question -->
                        <div style="margin-bottom:12px;">
                            <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.04em; color:var(--text-muted); margin-bottom:6px;">
                                Pertanyaan Siswa
                            </div>
                            <div style="background:var(--bg-elevated); border:1px solid var(--border); border-radius:10px; padding:12px 14px; font-size:13.5px; color:var(--text-primary); line-height:1.55; white-space:pre-wrap;">{{ $log->question }}</div>
                        </div>

                        <!-- AI Answer -->
                        <div>
                            <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.04em; color:var(--accent); margin-bottom:6px;">
                                Jawaban AI
                            </div>
                            <div class="ai-markdown-reply" data-raw="{{ e($log->answer) }}" style="background:rgba(184, 255, 0, 0.04); border:1px solid var(--border-light); border-radius:10px; padding:12px 14px; font-size:13.5px; color:var(--text-primary); line-height:1.6; white-space:pre-wrap;">{{ $log->answer }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div style="margin-bottom:24px;">
                {{ $chatLogs->links() }}
            </div>
        @else
            <div class="card" style="padding:48px 20px; text-align:center; border:1px solid var(--border);">
                <div style="font-size:36px; margin-bottom:10px;">💬</div>
                <h4 style="font-size:16px; font-weight:700; color:var(--text-primary); margin-bottom:6px;">
                    Belum Ada Riwayat Percakapan
                </h4>
                <p style="font-size:13px; color:var(--text-muted); max-width:440px; margin:0 auto;">
                    @if($search !== '' || $studentFilter !== '')
                        Tidak ada percakapan yang cocok dengan filter pencarian saat ini.
                    @else
                        Percakapan antara siswa dan Asisten AI pada e-modul ini akan muncul di sini secara otomatis.
                    @endif
                </p>
            </div>
        @endif
    </div>

    <style>
        .ai-markdown-reply p { margin: 0 0 8px 0; }
        .ai-markdown-reply p:last-child { margin-bottom: 0; }
        .ai-markdown-reply ul, .ai-markdown-reply ol { margin: 6px 0 8px 18px; padding: 0; }
        .ai-markdown-reply code { background: var(--bg-elevated); padding: 2px 5px; border-radius: 4px; font-size: 12.5px; }
        .ai-markdown-reply pre { background: var(--bg-elevated); padding: 10px; border-radius: 8px; overflow-x: auto; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof marked !== 'undefined') {
                document.querySelectorAll('.ai-markdown-reply').forEach(function (el) {
                    const raw = el.getAttribute('data-raw');
                    if (raw) {
                        el.style.whiteSpace = 'normal';
                        el.innerHTML = marked.parse(raw);
                    }
                });
            }
        });
    </script>
</x-admin-layout>
