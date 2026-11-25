<x-admin-layout :title="'Obrolan ' . $studentName . ' - ' . $e_modul->title" breadcrumb="Detail percakapan antara siswa dan Asisten AI">
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <div style="max-width:960px; margin:0 auto;">
        <!-- Header Card -->
        <div class="card" style="padding:20px 24px; margin-bottom:20px; border:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">
            <div style="display:flex; align-items:center; gap:14px;">
                <div style="width:46px; height:46px; border-radius:50%; background:var(--accent-soft); color:var(--accent); font-weight:700; font-size:18px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    {{ strtoupper(mb_substr($studentName, 0, 1)) }}
                </div>
                <div>
                    <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                        <h2 style="font-size:18px; font-weight:700; color:var(--text-primary); margin:0;">
                            {{ $studentName }}
                        </h2>
                        <span style="font-size:11px; font-weight:700; padding:3px 10px; border-radius:999px; background:var(--accent-soft); color:var(--accent);">
                            {{ $studentStats['total_chats'] }} Percakapan
                        </span>
                    </div>
                    <p style="font-size:12.5px; color:var(--text-secondary); margin:4px 0 0 0;">
                        Modul: <strong style="color:var(--text-primary);">{{ $e_modul->title }}</strong>
                        @if($studentStats['last_chat_at'])
                            · Terakhir aktif {{ \Carbon\Carbon::parse($studentStats['last_chat_at'])->diffForHumans() }}
                        @endif
                    </p>
                </div>
            </div>

            <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                <a href="{{ route('admin.e-modul.chat-history', $e_modul->id) }}" class="topbar-btn" style="padding:8px 14px; font-size:13px; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                    Daftar Siswa
                </a>

                @can('emodul.delete')
                    @if($studentStats['total_chats'] > 0)
                    <form action="{{ route('admin.e-modul.chat-history.student.clear', $e_modul->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus seluruh riwayat obrolan milik {{ addslashes($studentName) }}?')">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="student" value="{{ $studentName }}">
                        <button type="submit" class="topbar-btn" style="padding:8px 14px; font-size:13px; color:var(--rose); border:1px solid var(--border); background:var(--bg-elevated); cursor:pointer; display:inline-flex; align-items:center; gap:6px; border-radius:8px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                            Hapus Obrolan
                        </button>
                    </form>
                    @endif
                @endcan
            </div>
        </div>

        <!-- Search within Student Chat -->
        <div class="card" style="padding:14px 18px; margin-bottom:20px; border:1px solid var(--border);">
            <form method="GET" action="{{ route('admin.e-modul.chat-history.student', $e_modul->id) }}" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
                <input type="hidden" name="student" value="{{ $studentName }}">
                <div style="flex:1; min-width:200px;">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari di dalam obrolan {{ $studentName }}..." style="width:100%; background:var(--bg-elevated); border:1px solid var(--border); border-radius:8px; padding:8px 14px; font-size:13px; color:var(--text-primary); outline:none;">
                </div>
                <button type="submit" class="topbar-btn topbar-btn-primary" style="padding:8px 16px; font-size:13px; border:none; cursor:pointer;">
                    Cari
                </button>
                @if($search !== '')
                    <a href="{{ route('admin.e-modul.chat-history.student', ['e_modul' => $e_modul->id, 'student' => $studentName]) }}" class="topbar-btn" style="padding:8px 14px; font-size:13px; text-decoration:none;">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Conversation Thread -->
        @if($chatLogs->count() > 0)
            <div style="display:flex; flex-direction:column; gap:18px; margin-bottom:24px;">
                @foreach($chatLogs as $log)
                    <div class="card" style="padding:18px 20px; border:1px solid var(--border);">
                        <!-- Timestamp & Page Info -->
                        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px; padding-bottom:10px; margin-bottom:14px; border-bottom:1px solid var(--border);">
                            <span style="font-size:12px; color:var(--text-muted);">
                                🕒 {{ $log->created_at->format('d M Y, H:i') }} ({{ $log->created_at->diffForHumans() }})
                            </span>
                            <div style="display:flex; align-items:center; gap:8px;">
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

                        <!-- Student Message -->
                        <div style="display:flex; justify-content:flex-end; margin-bottom:14px;">
                            <div style="max-width:85%; background:var(--bg-elevated); border:1px solid var(--border-light); border-radius:14px 14px 4px 14px; padding:12px 16px;">
                                <div style="font-size:11px; font-weight:700; color:var(--text-muted); margin-bottom:4px;">
                                    {{ $studentName }}
                                </div>
                                <div style="font-size:13.5px; color:var(--text-primary); line-height:1.55; white-space:pre-wrap;">{{ $log->question }}</div>
                            </div>
                        </div>

                        <!-- AI Reply -->
                        <div style="display:flex; justify-content:flex-start;">
                            <div style="max-width:90%; background:rgba(184, 255, 0, 0.04); border:1px solid var(--border-light); border-radius:14px 14px 14px 4px; padding:12px 16px;">
                                <div style="font-size:11px; font-weight:700; color:var(--accent); margin-bottom:6px;">
                                    🤖 Asisten AI
                                </div>
                                <div class="ai-markdown-reply" data-raw="{{ e($log->answer) }}" style="font-size:13.5px; color:var(--text-primary); line-height:1.6; white-space:pre-wrap;">{{ $log->answer }}</div>
                            </div>
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
                    Tidak Ada Percakapan
                </h4>
                <p style="font-size:13px; color:var(--text-muted); max-width:420px; margin:0 auto;">
                    @if($search !== '')
                        Tidak ditemukan pesan yang cocok dengan kata kunci "{{ $search }}".
                    @else
                        Belum ada riwayat percakapan untuk siswa ini.
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
