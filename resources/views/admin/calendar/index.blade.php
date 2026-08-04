<x-admin-layout title="Kalender & Agenda" breadcrumb="Kalender">
    <div style="max-width: 1200px; margin: 0 auto; padding-bottom: 50px;">

        <!-- Flash Messages -->
        @if(session('success'))
            <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; padding: 12px 18px; border-radius: 12px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <span style="font-size:18px;">✅</span>
                    <span style="font-size: 14px; font-weight: 500;">{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; cursor:pointer; font-size:18px;">&times;</button>
            </div>
        @endif

        <!-- Header Bar & Month Controls -->
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-bottom: 24px;">
            <div>
                <h1 style="font-size:24px; font-weight:800; color:var(--text-primary); letter-spacing:-0.5px; display:flex; align-items:center; gap:12px; margin:0;">
                    <span style="display:inline-flex; align-items:center; justify-content:center; width:40px; height:40px; border-radius:12px; background:rgba(99, 102, 241, 0.15); color:#818cf8;">
                        📅
                    </span>
                    Kalender & Agenda
                </h1>
                <p style="font-size:13.5px; color:var(--text-muted); margin:4px 0 0;">Pantau deadline project, kadaluarsa domain, dan jadwal agenda internal secara terpusat.</p>
            </div>

            <!-- Action Controls & Month Navigation -->
            <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                <!-- Month Navigator -->
                <div style="display:flex; align-items:center; background:var(--bg-elevated); border:1px solid var(--border); border-radius:12px; padding:4px; gap:4px;">
                    @php
                        $prevMonth = $month - 1;
                        $prevYear = $year;
                        if ($prevMonth < 1) { $prevMonth = 12; $prevYear--; }

                        $nextMonth = $month + 1;
                        $nextYear = $year;
                        if ($nextMonth > 12) { $nextMonth = 1; $nextYear++; }
                    @endphp
                    <a href="{{ route('admin.calendar.index', ['year' => $prevYear, 'month' => $prevMonth]) }}" class="btn btn-ghost" style="padding:6px 12px; border-radius:8px;" title="Bulan Sebelumnya">
                        ‹
                    </a>
                    <span style="font-weight:700; font-size:15px; color:var(--text-primary); padding:0 10px; min-width:140px; text-align:center;">
                        {{ $currentMonth->translatedFormat('F Y') }}
                    </span>
                    <a href="{{ route('admin.calendar.index', ['year' => $nextYear, 'month' => $nextMonth]) }}" class="btn btn-ghost" style="padding:6px 12px; border-radius:8px;" title="Bulan Selanjutnya">
                        ›
                    </a>
                </div>

                <!-- Today Button -->
                <a href="{{ route('admin.calendar.index') }}" class="btn btn-secondary" style="padding:8px 14px; border-radius:10px; font-size:13px; font-weight:600;">
                    Hari Ini
                </a>

                <!-- 12 Months Overview Button -->
                <button onclick="switchView('year')" class="btn btn-secondary" style="padding:8px 14px; border-radius:10px; font-size:13px; font-weight:600; display:flex; align-items:center; gap:6px;">
                    📅 Lihat Semua (12 Bulan)
                </button>

                @can('calendar.create')
                <!-- Add Event Button -->
                <button onclick="openCreateModal()" class="btn btn-primary" style="padding:8px 16px; border-radius:10px; font-size:13.5px; font-weight:600; display:flex; align-items:center; gap:6px;">
                    <span>+</span> Tambah Agenda
                </button>
                @endcan
            </div>
        </div>

        <!-- Filter Category Tabs -->
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom: 20px; background:var(--bg-surface); padding:10px 16px; border-radius:14px; border:1px solid var(--border);">
            <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                <span style="font-size:12px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; margin-right:8px;">Filter Tampilan:</span>
                <button onclick="filterEvents('all')" class="filter-tab active" id="tab-all">Semua Event</button>
                <button onclick="filterEvents('custom')" class="filter-tab" id="tab-custom">
                    <span style="width:8px; height:8px; border-radius:50%; background:#6366f1; inline-block;"></span> Agenda Custom
                </button>
                <button onclick="filterEvents('project')" class="filter-tab" id="tab-project">
                    <span style="width:8px; height:8px; border-radius:50%; background:#f43f5e; inline-block;"></span> Project Orders
                </button>
                <button onclick="filterEvents('domain')" class="filter-tab" id="tab-domain">
                    <span style="width:8px; height:8px; border-radius:50%; background:#f59e0b; inline-block;"></span> Expired Domain
                </button>
            </div>

            <!-- View Switcher (Grid / List / Year) -->
            <div style="display:flex; background:rgba(255,255,255,0.05); padding:3px; border-radius:8px; border:1px solid var(--border); gap:2px;">
                <button onclick="switchView('grid')" id="viewBtnGrid" class="view-btn active" title="Tampilan Grid Bulan">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    <span style="font-size:11.5px; font-weight:600; margin-left:4px;">Bulan</span>
                </button>
                <button onclick="switchView('list')" id="viewBtnList" class="view-btn" title="Tampilan List Agenda">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                    <span style="font-size:11.5px; font-weight:600; margin-left:4px;">List</span>
                </button>
                <button onclick="switchView('year')" id="viewBtnYear" class="view-btn" title="Tampilan 12 Bulan (Tahun)">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <span style="font-size:11.5px; font-weight:600; margin-left:4px;">12 Bulan</span>
                </button>
            </div>
        </div>

        <!-- CALENDAR GRID VIEW -->
        <div id="calendarGridView" class="card" style="padding:0; overflow:hidden; border-radius:16px; border:1px solid var(--border); background:var(--bg-surface); box-shadow:0 10px 30px rgba(0,0,0,0.25);">
            <!-- Day of Week Header -->
            <div style="display:grid; grid-template-columns: repeat(7, 1fr); background:var(--bg-elevated); border-bottom:1px solid var(--border); text-align:center;">
                @php $daysOfWeek = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']; @endphp
                @foreach($daysOfWeek as $index => $day)
                    <div style="padding:12px 6px; font-size:12.5px; font-weight:700; color:{{ $index === 0 ? '#f43f5e' : 'var(--text-muted)' }}; text-transform:uppercase; letter-spacing:0.5px;">
                        {{ $day }}
                    </div>
                @endforeach
            </div>

            <!-- Calendar Days Grid -->
            @php
                $todayDate = \Carbon\Carbon::now()->format('Y-m-d');
                $iterDate = $startOfMonth->copy();
                $allEventsByDate = [];
                foreach ($formattedEvents as $evt) {
                    $d = $evt['start_date'];
                    if (!isset($allEventsByDate[$d])) {
                        $allEventsByDate[$d] = [];
                    }
                    $allEventsByDate[$d][] = $evt;
                }
            @endphp

            <div style="display:grid; grid-template-columns: repeat(7, 1fr); auto-rows: 125px; gap: 1px; background:var(--border);">
                @while($iterDate <= $endOfMonth)
                    @php
                        $dateStr = $iterDate->format('Y-m-d');
                        $isCurrentMonth = $iterDate->month == $month;
                        $isToday = $dateStr === $todayDate;
                        $dayEvents = $allEventsByDate[$dateStr] ?? [];
                    @endphp

                    <div class="calendar-day-cell {{ $isCurrentMonth ? '' : 'other-month' }} {{ $isToday ? 'is-today' : '' }}" 
                         onclick="handleDayClick('{{ $dateStr }}', event)">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px; flex-shrink:0;">
                            <span class="day-number {{ $isToday ? 'today-badge' : '' }}">
                                {{ $iterDate->day }}
                            </span>
                            @if($isCurrentMonth)
                                <button onclick="openCreateModalWithDate('{{ $dateStr }}'); event.stopPropagation();" class="add-day-btn" title="Tambah event pada tanggal ini">+</button>
                            @endif
                        </div>

                        <!-- Event Items List in Day Cell -->
                        <div style="display:flex; flex-direction:column; gap:4px; overflow-y:auto; flex-grow:1; max-height:82px;" class="custom-scroll">
                            @foreach($dayEvents as $evt)
                                <div class="event-pill event-type-{{ $evt['category'] }}" 
                                     data-type="{{ $evt['category'] }}"
                                     style="border-left: 3px solid {{ $evt['color'] }}; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);"
                                     onclick="openDetailModal({{ json_encode($evt) }}); event.stopPropagation();">
                                    <span style="display:inline-block; width:6px; height:6px; border-radius:50%; background:{{ $evt['color'] }}; flex-shrink:0;"></span>
                                    <span style="font-size:11.5px; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; color:var(--text-primary); {{ $evt['is_completed'] ? 'text-decoration:line-through; opacity:0.6;' : '' }}">
                                        {{ $evt['title'] }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @php $iterDate->addDay(); @endphp
                @endwhile
            </div>
        </div>

        <!-- AGENDA LIST VIEW (Hidden by Default) -->
        <div id="calendarListView" class="card" style="display:none; padding:20px; border-radius:16px; border:1px solid var(--border); background:var(--bg-surface);">
            <h3 style="font-size:16px; font-weight:700; color:var(--text-primary); margin-bottom:16px;">
                Daftar Agenda {{ $currentMonth->translatedFormat('F Y') }}
            </h3>

            @if(count($formattedEvents) === 0)
                <div style="text-align:center; padding:40px 20px; color:var(--text-muted);">
                    <span style="font-size:36px; display:block; margin-bottom:10px;">📅</span>
                    <p style="font-size:14px; margin:0;">Tidak ada agenda atau deadline pada bulan ini.</p>
                </div>
            @else
                <div style="display:flex; flex-direction:column; gap:10px;">
                    @foreach($formattedEvents as $evt)
                        <div class="list-event-card event-type-{{ $evt['category'] }}" 
                             style="display:flex; justify-content:space-between; align-items:center; padding:14px 18px; border-radius:12px; background:var(--bg-elevated); border:1px solid var(--border); border-left:4px solid {{ $evt['color'] }};"
                             onclick="openDetailModal({{ json_encode($evt) }})">
                            <div style="display:flex; align-items:center; gap:14px;">
                                <div style="text-align:center; min-width:60px; background:rgba(255,255,255,0.05); padding:6px 10px; border-radius:8px;">
                                    <span style="font-size:11px; font-weight:700; color:var(--text-muted); display:block; text-transform:uppercase;">
                                        {{ \Carbon\Carbon::parse($evt['start_date'])->translatedFormat('M') }}
                                    </span>
                                    <span style="font-size:18px; font-weight:800; color:var(--text-primary); display:block; line-height:1;">
                                        {{ \Carbon\Carbon::parse($evt['start_date'])->format('d') }}
                                    </span>
                                </div>

                                <div>
                                    <div style="display:flex; align-items:center; gap:8px;">
                                        <h4 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0; {{ $evt['is_completed'] ? 'text-decoration:line-through; opacity:0.6;' : '' }}">
                                            {{ $evt['title'] }}
                                        </h4>
                                        <span class="badge" style="background:rgba(255,255,255,0.08); color:{{ $evt['color'] }}; font-size:10px; padding:2px 8px; border-radius:6px;">
                                            {{ $evt['badge'] }}
                                        </span>
                                    </div>
                                    @if($evt['description'])
                                        <p style="font-size:12.5px; color:var(--text-muted); margin:4px 0 0;">{{ Str::limit($evt['description'], 90) }}</p>
                                    @endif
                                </div>
                            </div>

                            <div style="display:flex; align-items:center; gap:10px;">
                                @if($evt['type'] === 'custom')
                                    @can('calendar.edit')
                                    <form action="{{ route('admin.calendar.toggle-complete', $evt['raw_id']) }}" method="POST" onclick="event.stopPropagation();">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-ghost" style="padding:6px 10px; font-size:12px;" title="Tandai Selesai">
                                            {{ $evt['is_completed'] ? '🔄 Batal Selesai' : '✓ Tandai Selesai' }}
                                        </button>
                                    </form>
                                    @endcan
                                @elseif(!empty($evt['url']))
                                    <a href="{{ $evt['url'] }}" onclick="event.stopPropagation();" class="btn btn-ghost" style="padding:6px 12px; font-size:12px;" target="_blank">
                                        Lihat Detail →
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- 12-MONTH OVERVIEW / YEAR VIEW (Hidden by Default) -->
        <div id="calendarYearView" style="display:none;">
            <!-- <div style="text-align:center; margin-bottom:24px; background:var(--bg-surface); padding:16px; border-radius:14px; border:1px solid var(--border);">
                <h2 style="font-size:22px; font-weight:800; color:var(--text-primary); letter-spacing:-0.5px; margin:0;">
                    🗓️ Ringkasan Kalender Tahun {{ $year }}
                </h2>
                <p style="font-size:13px; color:var(--text-muted); margin:4px 0 0;">Menampilkan 12 bulan sekaligus. Klik pada nama bulan atau tanggal untuk memperbesar tampilan bulan tersebut.</p>
            </div> -->

            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:18px;">
                @foreach($yearMonthsData as $mItem)
                    <div class="mini-month-card" style="background:var(--bg-surface); border:1px solid var(--border); border-radius:14px; padding:14px; box-shadow:0 6px 20px rgba(0,0,0,0.2); transition:transform 0.2s, border-color 0.2s;">
                        <!-- Month Header -->
                        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border); padding-bottom:8px; margin-bottom:10px;">
                            <a href="{{ route('admin.calendar.index', ['year' => $year, 'month' => $mItem['month_num']]) }}" 
                               style="font-size:14px; font-weight:800; color:var(--text-primary); text-transform:uppercase; letter-spacing:0.5px; text-decoration:none;">
                                {{ $mItem['name'] }}
                            </a>
                            <a href="{{ route('admin.calendar.index', ['year' => $year, 'month' => $mItem['month_num']]) }}" class="btn btn-ghost" style="padding:2px 8px; font-size:11px; border-radius:6px;">
                                Buka ↗
                            </a>
                        </div>

                        <!-- Mini Week Headers -->
                        <div style="display:grid; grid-template-columns: repeat(7, 1fr); text-align:center; margin-bottom:6px;">
                            @foreach(['M','S','S','R','K','J','S'] as $idx => $dName)
                                <span style="font-size:11px; font-weight:700; color:{{ $idx === 0 ? '#f43f5e' : 'var(--text-muted)' }};">
                                    {{ $dName }}
                                </span>
                            @endforeach
                        </div>

                        <!-- Mini Day Cells Grid -->
                        <div style="display:grid; grid-template-columns: repeat(7, 1fr); gap:3px; text-align:center;">
                            @foreach($mItem['days'] as $dayCell)
                                @php
                                    $hasEvents = count($dayCell['events']) > 0;
                                @endphp
                                <a href="{{ route('admin.calendar.index', ['year' => $year, 'month' => $mItem['month_num']]) }}" 
                                   title="{{ $dayCell['date'] }}: {{ count($dayCell['events']) }} agenda"
                                   style="display:flex; flex-direction:column; align-items:center; justify-content:center; height:28px; border-radius:6px; font-size:11px; font-weight:600; text-decoration:none; position:relative; color:{{ $dayCell['is_current_month'] ? 'var(--text-primary)' : 'rgba(255,255,255,0.2)' }}; {{ $dayCell['is_today'] ? 'background:#6366f1; color:#ffffff !important; font-weight:800; box-shadow:0 0 6px rgba(99,102,241,0.6);' : '' }}">
                                    <span>{{ $dayCell['day'] }}</span>
                                    @if($hasEvents && !$dayCell['is_today'])
                                        <span style="position:absolute; bottom:2px; width:4px; height:4px; border-radius:50%; background:{{ $dayCell['events'][0]['color'] ?? '#6366f1' }};"></span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- MODAL CREATE AGENDA -->
    <div id="createModal" class="modal-overlay" style="display:none;">
        <div class="modal-content" style="max-width:480px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; border-bottom:1px solid var(--border); padding-bottom:12px;">
                <h3 style="font-size:18px; font-weight:700; color:var(--text-primary); margin:0;">
                    ➕ Tambah Agenda Baru
                </h3>
                <button onclick="closeModal('createModal')" style="background:none; border:none; color:var(--text-muted); font-size:20px; cursor:pointer;">&times;</button>
            </div>

            <form action="{{ route('admin.calendar.store') }}" method="POST">
                @csrf
                <div style="display:flex; flex-direction:column; gap:16px;">
                    <div>
                        <label style="font-size:12.5px; font-weight:600; color:var(--text-muted); display:block; margin-bottom:6px;">Judul Agenda / Event <span style="color:#f43f5e;">*</span></label>
                        <input type="text" name="title" required placeholder="Contoh: Rapat Progress Project, Kickoff Meeting" style="width:100%; padding:10px 14px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:10px; color:var(--text-primary); font-size:13.5px; outline:none;">
                    </div>

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
                        <div>
                            <label style="font-size:12.5px; font-weight:600; color:var(--text-muted); display:block; margin-bottom:6px;">Kategori <span style="color:#f43f5e;">*</span></label>
                            <select name="category" required style="width:100%; padding:10px 14px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:10px; color:var(--text-primary); font-size:13.5px; outline:none;">
                                <option value="event">📌 Event / Kegiatan</option>
                                <option value="task">📝 Task / Tugas</option>
                                <option value="meeting">🤝 Meeting / Rapat</option>
                                <option value="reminder">🔔 Reminder / Pengingat</option>
                            </select>
                        </div>

                        <div>
                            <label style="font-size:12.5px; font-weight:600; color:var(--text-muted); display:block; margin-bottom:6px;">Warna Label</label>
                            <input type="color" name="color" value="#6366f1" style="width:100%; height:40px; border:1px solid var(--border); border-radius:10px; background:var(--bg-elevated); cursor:pointer;">
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
                        <div>
                            <label style="font-size:12.5px; font-weight:600; color:var(--text-muted); display:block; margin-bottom:6px;">Mulai Tanggal <span style="color:#f43f5e;">*</span></label>
                            <input type="datetime-local" name="start_date" id="modalStartDate" required style="width:100%; padding:10px 14px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:10px; color:var(--text-primary); font-size:13px; outline:none;">
                        </div>
                        <div>
                            <label style="font-size:12.5px; font-weight:600; color:var(--text-muted); display:block; margin-bottom:6px;">Selesai (Opsional)</label>
                            <input type="datetime-local" name="end_date" style="width:100%; padding:10px 14px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:10px; color:var(--text-primary); font-size:13px; outline:none;">
                        </div>
                    </div>

                    <div>
                        <label style="font-size:12.5px; font-weight:600; color:var(--text-muted); display:block; margin-bottom:6px;">Keterangan / Catatan Tambahan</label>
                        <textarea name="description" rows="3" placeholder="Tuliskan catatan detail agenda di sini..." style="width:100%; padding:10px 14px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:10px; color:var(--text-primary); font-size:13px; outline:none; resize:vertical;"></textarea>
                    </div>

                    <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:10px;">
                        <button type="button" onclick="closeModal('createModal')" class="btn btn-secondary" style="padding:9px 16px; font-size:13px;">Batal</button>
                        <button type="submit" class="btn btn-primary" style="padding:9px 20px; font-size:13px; font-weight:600;">Simpan Agenda</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL DETAIL EVENT -->
    <div id="detailModal" class="modal-overlay" style="display:none;">
        <div class="modal-content" style="max-width:460px;">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:16px;">
                <div>
                    <span id="detailBadge" class="badge" style="background:rgba(99, 102, 241, 0.2); color:#818cf8; font-size:11px; padding:3px 10px; border-radius:6px; margin-bottom:8px; display:inline-block;">Event</span>
                    <h3 id="detailTitle" style="font-size:18px; font-weight:700; color:var(--text-primary); margin:0;">Detail Agenda</h3>
                </div>
                <button onclick="closeModal('detailModal')" style="background:none; border:none; color:var(--text-muted); font-size:20px; cursor:pointer;">&times;</button>
            </div>

            <div style="display:flex; flex-direction:column; gap:14px; margin-bottom:24px;">
                <div style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--text-muted);">
                    <span>📆 Tanggal:</span>
                    <strong id="detailDate" style="color:var(--text-primary);"></strong>
                </div>

                <div id="detailDescContainer" style="background:var(--bg-elevated); padding:12px 14px; border-radius:10px; border:1px solid var(--border);">
                    <p id="detailDesc" style="font-size:13px; color:var(--text-primary); margin:0; white-space:pre-line;"></p>
                </div>
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--border); padding-top:14px;">
                <div id="detailCustomActions" style="display:flex; gap:8px;">
                    <!-- Edit & Delete buttons will be dynamically rendered here if custom -->
                </div>
                <a id="detailUrlBtn" href="#" style="display:none;" class="btn btn-primary" target="_blank">Lihat di System →</a>
                <button type="button" onclick="closeModal('detailModal')" class="btn btn-secondary" style="padding:8px 16px; font-size:13px;">Tutup</button>
            </div>
        </div>
    </div>

    <!-- STYLES & INTERACTIVE SCRIPT -->
    <style>
        .filter-tab {
            background: transparent;
            border: 1px solid transparent;
            color: var(--text-muted);
            padding: 5px 12px;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .filter-tab:hover {
            color: var(--text-primary);
            background: rgba(255, 255, 255, 0.04);
        }
        .filter-tab.active {
            background: var(--bg-elevated);
            border-color: var(--border);
            color: var(--text-primary);
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .view-btn {
            background: transparent;
            border: none;
            color: var(--text-muted);
            padding: 6px 10px;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .view-btn.active {
            background: var(--bg-elevated);
            color: var(--text-primary);
        }

        .calendar-day-cell {
            background: var(--bg-surface);
            padding: 8px;
            height: 125px;
            max-height: 125px;
            box-sizing: border-box;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: background 0.15s ease;
            cursor: pointer;
            position: relative;
        }
        .calendar-day-cell:hover {
            background: rgba(255, 255, 255, 0.02);
        }
        .calendar-day-cell.other-month {
            opacity: 0.35;
            background: rgba(0, 0, 0, 0.15);
        }
        .calendar-day-cell.is-today {
            background: rgba(99, 102, 241, 0.06) !important;
        }
        .today-badge {
            background: #6366f1;
            color: #ffffff !important;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800 !important;
            box-shadow: 0 0 10px rgba(99, 102, 241, 0.5);
        }
        .day-number {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .add-day-btn {
            display: none;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: var(--text-primary);
            width: 20px;
            height: 20px;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }
        .calendar-day-cell:hover .add-day-btn {
            display: flex;
        }

        .event-pill {
            padding: 4px 8px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            transition: transform 0.15s ease, filter 0.15s ease;
        }
        .event-pill:hover {
            transform: translateY(-1px);
            filter: brightness(1.15);
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.65);
            backdrop-filter: blur(6px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 999;
            padding: 16px;
        }
        .modal-content {
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 24px;
            width: 100%;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            animation: modalFadeIn 0.2s ease-out;
        }

        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        .custom-scroll::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 4px;
        }
    </style>

    <script>
        function filterEvents(category) {
            document.querySelectorAll('.filter-tab').forEach(el => el.classList.remove('active'));
            document.getElementById('tab-' + category).classList.add('active');

            const allEvents = document.querySelectorAll('.event-pill, .list-event-card');
            allEvents.forEach(el => {
                if (category === 'all') {
                    el.style.display = 'flex';
                } else if (category === 'custom') {
                    el.style.display = (el.classList.contains('event-type-event') || el.classList.contains('event-type-task') || el.classList.contains('event-type-meeting') || el.classList.contains('event-type-reminder')) ? 'flex' : 'none';
                } else if (category === 'project') {
                    el.style.display = el.classList.contains('event-type-project') ? 'flex' : 'none';
                } else if (category === 'domain') {
                    el.style.display = el.classList.contains('event-type-domain') ? 'flex' : 'none';
                }
            });
        }

        function switchView(viewType) {
            document.getElementById('viewBtnGrid').classList.toggle('active', viewType === 'grid');
            document.getElementById('viewBtnList').classList.toggle('active', viewType === 'list');
            document.getElementById('viewBtnYear').classList.toggle('active', viewType === 'year');

            document.getElementById('calendarGridView').style.display = viewType === 'grid' ? 'block' : 'none';
            document.getElementById('calendarListView').style.display = viewType === 'list' ? 'block' : 'none';
            document.getElementById('calendarYearView').style.display = viewType === 'year' ? 'block' : 'none';
        }

        function openCreateModal() {
            const now = new Date();
            const localIso = new Date(now.getTime() - (now.getTimezoneOffset() * 60000)).toISOString().slice(0, 16);
            document.getElementById('modalStartDate').value = localIso;
            document.getElementById('createModal').style.display = 'flex';
        }

        function openCreateModalWithDate(dateStr) {
            document.getElementById('modalStartDate').value = dateStr + 'T09:00';
            document.getElementById('createModal').style.display = 'flex';
        }

        function handleDayClick(dateStr, event) {
            openCreateModalWithDate(dateStr);
        }

        function openDetailModal(evt) {
            document.getElementById('detailTitle').innerText = evt.title;
            document.getElementById('detailBadge').innerText = evt.badge || 'Event';
            document.getElementById('detailBadge').style.color = evt.color;
            document.getElementById('detailDate').innerText = evt.start_date;

            const descEl = document.getElementById('detailDesc');
            descEl.innerText = evt.description || 'Tidak ada keterangan tambahan.';

            const urlBtn = document.getElementById('detailUrlBtn');
            if (evt.url) {
                urlBtn.href = evt.url;
                urlBtn.style.display = 'inline-flex';
            } else {
                urlBtn.style.display = 'none';
            }

            const customActions = document.getElementById('detailCustomActions');
            customActions.innerHTML = '';

            if (evt.type === 'custom') {
                customActions.innerHTML = `
                    <form action="/admin/calendar/${evt.raw_id}" method="POST" class="delete-form" data-confirm-title="Hapus Agenda?" data-confirm-text="Agenda ini akan dihapus secara permanen.">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-ghost" style="color:#f43f5e; padding:8px 14px; font-size:12.5px;">🗑️ Hapus Agenda</button>
                    </form>
                `;
            }

            document.getElementById('detailModal').style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        window.onclick = function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                e.target.style.display = 'none';
            }
        };
    </script>
</x-admin-layout>
