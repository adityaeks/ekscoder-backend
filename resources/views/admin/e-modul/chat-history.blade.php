<x-admin-layout :title="'Riwayat Chat AI - ' . $e_modul->title" breadcrumb="Daftar siswa yang pernah berdiskusi dengan Asisten AI pada e-modul">
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

        <!-- Search Student -->
        <div class="card" style="padding:16px 20px; margin-bottom:20px; border:1px solid var(--border);">
            <form method="GET" action="{{ route('admin.e-modul.chat-history', $e_modul->id) }}" style="display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
                <div style="flex:1; min-width:220px;">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama siswa atau kata kunci percakapan..." style="width:100%; background:var(--bg-elevated); border:1px solid var(--border); border-radius:8px; padding:9px 14px; font-size:13px; color:var(--text-primary); outline:none;">
                </div>

                <div style="display:flex; gap:8px;">
                    <button type="submit" class="topbar-btn topbar-btn-primary" style="padding:9px 16px; font-size:13px; border:none; cursor:pointer;">
                        Cari
                    </button>
                    @if($search !== '')
                        <a href="{{ route('admin.e-modul.chat-history', $e_modul->id) }}" class="topbar-btn" style="padding:9px 14px; font-size:13px; text-decoration:none;">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Student Cards Grid -->
        @if($studentCards->count() > 0)
            <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(310px, 1fr)); gap:16px; margin-bottom:24px;">
                @foreach($studentCards as $card)
                    @php
                        $lastLog = $card->last_log;
                        $lastChatTime = \Carbon\Carbon::parse($card->last_chat_at);
                    @endphp
                    <a href="{{ route('admin.e-modul.chat-history.student', ['e_modul' => $e_modul->id, 'student' => $card->student_name]) }}"
                       class="card student-chat-card"
                       style="display:flex; flex-direction:column; justify-content:space-between; padding:18px 20px; border:1px solid var(--border); text-decoration:none; transition:transform 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease; cursor:pointer;">
                        <div>
                            <!-- Top Row: Avatar, Name, Count -->
                            <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px; margin-bottom:12px;">
                                <div style="display:flex; align-items:center; gap:11px; min-width:0;">
                                    <div style="width:40px; height:40px; border-radius:50%; background:var(--accent-soft); color:var(--accent); font-weight:700; font-size:15px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                        {{ strtoupper(mb_substr($card->student_name, 0, 1)) }}
                                    </div>
                                    <div style="min-width:0;">
                                        <div style="font-size:15px; font-weight:700; color:var(--text-primary); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                            {{ $card->student_name }}
                                        </div>
                                        <div style="font-size:11.5px; color:var(--text-muted); margin-top:2px;">
                                            Aktif {{ $lastChatTime->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>

                                <span style="font-size:11px; font-weight:700; padding:4px 10px; border-radius:999px; background:var(--accent-soft); color:var(--accent); white-space:nowrap;">
                                    {{ $card->total_chats }} Chat
                                </span>
                            </div>

                            <!-- Last Question Preview -->
                            @if($lastLog)
                                <div style="background:var(--bg-elevated); border:1px solid var(--border); border-radius:10px; padding:10px 12px; margin-bottom:12px;">
                                    <div style="font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.04em; color:var(--text-muted); margin-bottom:4px;">
                                        Pertanyaan Terakhir
                                    </div>
                                    <div style="font-size:12.5px; color:var(--text-primary); line-height:1.45; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;">
                                        {{ $lastLog->question }}
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Footer -->
                        <div style="display:flex; justify-content:space-between; align-items:center; padding-top:10px; border-top:1px solid var(--border); font-size:11.5px; color:var(--text-muted);">
                            <span>
                                @if($lastLog && $lastLog->page_number)
                                    📄 {{ Str::limit($lastLog->page_number, 24) }}
                                @else
                                    {{ $lastChatTime->format('d M Y, H:i') }}
                                @endif
                            </span>
                            <span style="color:var(--accent); font-weight:600; display:inline-flex; align-items:center; gap:4px;">
                                Lihat Obrolan
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="9 18 15 12 9 6"/></svg>
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Pagination -->
            <div style="margin-bottom:24px;">
                {{ $studentCards->links() }}
            </div>
        @else
            <div class="card" style="padding:48px 20px; text-align:center; border:1px solid var(--border);">
                <div style="font-size:36px; margin-bottom:10px;">💬</div>
                <h4 style="font-size:16px; font-weight:700; color:var(--text-primary); margin-bottom:6px;">
                    Belum Ada Riwayat Percakapan
                </h4>
                <p style="font-size:13px; color:var(--text-muted); max-width:440px; margin:0 auto;">
                    @if($search !== '')
                        Tidak ada siswa atau percakapan yang cocok dengan pencarian "{{ $search }}".
                    @else
                        Percakapan antara siswa dan Asisten AI pada e-modul ini akan dikelompokkan per siswa di sini secara otomatis.
                    @endif
                </p>
            </div>
        @endif
    </div>

    <style>
        .student-chat-card:hover {
            transform: translateY(-2px);
            border-color: var(--accent) !important;
            box-shadow: 0 10px 25px -10px rgba(0, 0, 0, 0.25);
        }
    </style>
</x-admin-layout>
