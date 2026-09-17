<x-admin-layout title="Setting Prompt AI E-Modul" breadcrumb="Konfigurasi karakter kecerdasan buatan (AI) asisten flipbook, batas materi, dan model gateway">

    <style>
        /* Scoped styles for E-Modul AI Settings (Lime Theme) */
        .header-control-grid {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 20px;
            margin-bottom: 24px;
            align-items: stretch;
        }

        @media (max-width: 960px) {
            .header-control-grid {
                grid-template-columns: 1fr;
            }
        }

        .control-shelf-card {
            background: var(--bg-elevated);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 18px 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            transition: border-color 0.2s ease;
        }

        .control-shelf-card:hover {
            border-color: rgba(184, 255, 0, 0.25);
        }

        .control-shelf-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .control-shelf-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .control-shelf-sub {
            font-size: 11.5px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .modern-select-wrapper {
            position: relative;
            width: 100%;
        }

        .modern-select-wrapper select {
            width: 100%;
            appearance: none;
            -webkit-appearance: none;
            background: var(--bg-surface);
            border: 1px solid var(--border-light);
            border-radius: 10px;
            padding: 10px 38px 10px 38px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
            cursor: pointer;
            transition: all 0.2s ease;
            outline: none;
            font-family: inherit;
        }

        .modern-select-wrapper select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        .modern-select-icon-left {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--accent);
            pointer-events: none;
            display: flex;
            align-items: center;
        }

        .modern-select-arrow-right {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            pointer-events: none;
            display: flex;
            align-items: center;
        }

        .preset-pill-deck {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }

        .preset-card-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 14px;
            border-radius: 9px;
            border: 1px solid var(--border-light);
            background: var(--bg-surface);
            color: var(--text-secondary);
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            white-space: nowrap;
        }

        .preset-card-btn:hover {
            background: var(--bg-hover);
            color: var(--text-primary);
            border-color: var(--accent);
            transform: translateY(-1px);
        }

        .preset-card-btn.active {
            background: var(--accent) !important;
            color: var(--accent-text) !important;
            border-color: var(--accent) !important;
            box-shadow: 0 4px 16px var(--accent-glow);
            font-weight: 700;
            transform: translateY(-1px);
        }

        .preset-sub-badge {
            font-size: 9.5px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 1px 6px;
            border-radius: 4px;
            font-weight: 700;
            background: var(--accent-soft);
            color: var(--badge-accent-text, var(--accent));
            transition: all 0.2s;
        }

        .preset-card-btn.active .preset-sub-badge {
            background: rgba(0, 0, 0, 0.22);
            color: var(--accent-text);
        }

        .editor-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 12px;
        }

        .tag-chips-wrapper {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .tag-insert-btn {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11.5px;
            font-weight: 600;
            color: var(--badge-accent-text, var(--accent));
            background: var(--accent-soft);
            border: 1px solid rgba(184, 255, 0, 0.25);
            border-radius: 6px;
            padding: 3px 9px;
            cursor: pointer;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .tag-insert-btn:hover {
            background: var(--accent);
            color: var(--accent-text);
            border-color: var(--accent);
            box-shadow: 0 0 12px var(--accent-glow);
            transform: translateY(-1px);
        }

        .btn-reset-template {
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            padding: 4px 8px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 6px;
            transition: all 0.15s ease;
        }

        .btn-reset-template:hover {
            color: var(--accent);
            background: var(--accent-soft);
        }

        .modern-prompt-textarea {
            width: 100%;
            min-height: 480px;
            font-family: 'JetBrains Mono', 'Fira Code', Consolas, monospace;
            font-size: 13px;
            line-height: 1.7;
            background: var(--bg-elevated);
            border: 1px solid var(--border-light);
            border-radius: 12px;
            padding: 18px 20px;
            color: var(--text-primary);
            outline: none;
            resize: vertical;
            box-sizing: border-box;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .modern-prompt-textarea:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        .guidance-banner {
            background: var(--accent-soft);
            border: 1px solid rgba(184, 255, 0, 0.2);
            border-radius: 10px;
            padding: 14px 18px;
            margin-bottom: 24px;
        }
    </style>

    <!-- Header Navigation Back Link & Action -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
        <div style="display:flex; align-items:center; gap:12px;">
            <a href="{{ route('admin.e-modul.index') }}" class="topbar-btn" style="text-decoration:none; display:inline-flex; align-items:center; gap:6px; font-size:12.5px; font-weight:600; padding:8px 14px; border-radius:8px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                <span>Kembali ke Koleksi Modul</span>
            </a>
            <span style="font-size:13px; color:var(--text-muted);">|</span>
            <div style="font-size:13px; color:var(--text-secondary); display:flex; align-items:center; gap:6px;">
                <span>✨ Model Aktif:</span>
                <span style="font-size:11px; font-weight:700; color:var(--badge-accent-text, var(--accent)); background:var(--accent-soft); border:1px solid rgba(184,255,0,0.25); padding:3px 9px; border-radius:6px;">
                    {{ $settings['emodul_model'] ?: 'Default Gateway (Auto)' }}
                </span>
            </div>
        </div>

        <!-- <div style="display:flex; align-items:center; gap:8px;">
            <button type="button" onclick="saveEmodulAiSettings()" id="btnHeaderSave" class="topbar-btn topbar-btn-primary" style="padding:8px 20px; font-size:13px; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:7px; font-weight:700; background:var(--accent); color:var(--accent-text); box-shadow:0 0 20px var(--accent-glow);">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                <span>Simpan Pengaturan</span>
            </button>
        </div> -->
    </div>

    <!-- MAIN CONFIGURATION CARD (FULL WIDTH) -->
    <div class="card" style="padding:28px 32px; border:1px solid var(--border); border-radius:14px; width:100%; box-sizing:border-box;">

        <!-- 2. Dual Control Shelf: AI Model Gateway & Preset Gaya Respon -->
        <div class="header-control-grid">
            
            <!-- Shelf Card 1: AI Model Gateway -->
            <div class="control-shelf-card">
                <div>
                    <div class="control-shelf-header">
                        <div class="control-shelf-title">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="color:var(--accent);"><rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/><line x1="9" y1="1" x2="9" y2="4"/><line x1="15" y1="1" x2="15" y2="4"/><line x1="9" y1="20" x2="9" y2="23"/><line x1="15" y1="20" x2="15" y2="23"/><line x1="20" y1="9" x2="23" y2="9"/><line x1="20" y1="14" x2="23" y2="14"/><line x1="1" y1="9" x2="4" y2="9"/><line x1="1" y1="14" x2="4" y2="14"/></svg>
                            <span>Pilih AI Model Gateway</span>
                        </div>
                        <span style="font-size:10.5px; font-weight:700; color:var(--badge-accent-text, var(--accent)); background:var(--accent-soft); border:1px solid rgba(184,255,0,0.25); padding:2px 8px; border-radius:12px;">Engine</span>
                    </div>
                    <div class="control-shelf-sub">Mesin AI untuk memproses percakapan modul</div>
                </div>

                <div class="modern-select-wrapper">
                    <span class="modern-select-icon-left">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1Z"/></svg>
                    </span>
                    <select id="emodulModelSelect">
                        <option value="" {{ empty($settings['emodul_model']) ? 'selected' : '' }}>Default Gateway Model (Auto)</option>
                        @foreach($models as $m)
                            <option value="{{ $m }}" {{ ($settings['emodul_model'] ?? '') === $m ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                    <span class="modern-select-arrow-right">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                    </span>
                </div>
            </div>

            <!-- Shelf Card 2: Preset Gaya Respon & Pedagogi -->
            <div class="control-shelf-card">
                <div>
                    <div class="control-shelf-header">
                        <div class="control-shelf-title">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="color:var(--accent);"><circle cx="12" cy="12" r="10"/><path d="m4.93 4.93 4.24 4.24"/><path d="m14.83 9.17 4.24-4.24"/><path d="m14.83 14.83 4.24 4.24"/><path d="m9.17 14.83-4.24 4.24"/></svg>
                            <span>Preset Gaya Bahasa & Pedagogi</span>
                        </div>
                        <span style="font-size:11px; color:var(--text-muted);">Pilih template respon</span>
                    </div>
                    <div class="control-shelf-sub">Pilih salah satu preset untuk mengisi template prompt di bawah ini secara instan</div>
                </div>

                <div class="preset-pill-deck">
                    <button type="button" class="preset-card-btn" onclick="applyPresetStyle('inquiry')" id="presetBtn_inquiry">
                        <span>🔬</span>
                        <span>Inkuiri & Sokratik</span>
                        <span class="preset-sub-badge">Default</span>
                    </button>
                    <button type="button" class="preset-card-btn" onclick="applyPresetStyle('educational')" id="presetBtn_educational">
                        <span>🎓</span>
                        <span>Guru Edukatif</span>
                    </button>
                    <button type="button" class="preset-card-btn" onclick="applyPresetStyle('friendly')" id="presetBtn_friendly">
                        <span>🧒</span>
                        <span>Santai & Sederhana</span>
                    </button>
                    <button type="button" class="preset-card-btn" onclick="applyPresetStyle('socratic')" id="presetBtn_socratic">
                        <span>🧐</span>
                        <span>Diskusi Bertahap</span>
                    </button>
                    <button type="button" class="preset-card-btn" onclick="applyPresetStyle('concise')" id="presetBtn_concise">
                        <span>⚡</span>
                        <span>Ringkas & Padat</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- 3. System Prompt Workspace Area -->
        <div class="editor-toolbar">
            <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                <label for="emodulSystemPrompt" style="font-size:13.5px; font-weight:700; color:var(--text-primary); display:flex; align-items:center; gap:7px; margin:0;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color:var(--accent);"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    <span>System Prompt (Instruksi Lengkap AI)</span>
                </label>
                <div class="tag-chips-wrapper">
                    <span style="font-size:11.5px; color:var(--text-muted); font-weight:500;">Sisipkan Tag:</span>
                    <button type="button" class="tag-insert-btn" onclick="insertPlaceholderTag('{modul_title}')" title="Klik untuk menyisipkan tag judul modul">
                        <span>+</span>{modul_title}
                    </button>
                    <button type="button" class="tag-insert-btn" onclick="insertPlaceholderTag('{page_number}')" title="Klik untuk menyisipkan tag nomor halaman">
                        <span>+</span>{page_number}
                    </button>
                </div>
            </div>
            <button type="button" class="btn-reset-template" onclick="resetToDefaultPrompt()">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                <span>Reset ke Template Rekomendasi</span>
            </button>
        </div>

        <!-- 4. Textarea Prompt (Lebar & Bersih) -->
        <div style="margin-bottom:18px;">
            <textarea id="emodulSystemPrompt" class="modern-prompt-textarea" rows="22" placeholder="Tuliskan instruksi prompt sistem AI di sini...">{{ $settings['emodul_system_prompt'] ?: $defaultPrompt }}</textarea>
        </div>

        <!-- 5. Guidance Note Card -->
        <div class="guidance-banner">
            <div style="display:flex; gap:12px; align-items:flex-start;">
                <div style="font-size:18px; line-height:1; flex-shrink:0; margin-top:1px;">💡</div>
                <div style="font-size:12.5px; color:var(--text-secondary); line-height:1.6;">
                    <strong style="color:var(--text-primary);">Panduan Penggunaan Tag Dinamis:</strong><br>
                    • Gunakan <code>{modul_title}</code> untuk otomatis menyisipkan judul modul aktif yang sedang dibaca siswa.<br>
                    • Gunakan <code>{page_number}</code> untuk otomatis menyisipkan nomor halaman aktif di reader flipbook (secara default bernilai <em>"Seluruh Modul"</em>).
                </div>
            </div>
        </div>

        <!-- 6. Footer Action Bar -->
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; border-top:1px solid var(--border); padding-top:20px;">
            <div style="font-size:12px; color:var(--text-muted); display:flex; align-items:center; gap:6px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <span>Perubahan konfigurasi prompt langsung berlaku seketika untuk semua pembaca di Flipbook 3D.</span>
            </div>
            <div style="display:flex; gap:10px; align-items:center;">
                <a href="{{ route('admin.e-modul.index') }}" class="topbar-btn" style="padding:10px 20px; font-size:13px; text-decoration:none;">Batal</a>
                <button type="button" onclick="saveEmodulAiSettings()" id="btnSaveAiSettings" class="topbar-btn topbar-btn-primary" style="padding:10px 28px; font-size:13.5px; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:8px; font-weight:700; background:var(--accent); color:var(--accent-text); box-shadow:0 0 20px var(--accent-glow);">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    <span>Simpan Pengaturan</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Scripts for Settings -->
    <script>
        let emodulDefaultPromptTemplate = @json($defaultPrompt);
        let activePromptStyle = @json($settings['emodul_response_style'] ?? 'inquiry');

        const promptStyleTemplates = {
            inquiry: `Anda adalah AI Teaching Assistant & Pembimbing Belajar Interaktif untuk E-Modul "{modul_title}".
Tugas utama Anda adalah MEMBIMBING siswa memahami materi secara mandiri melalui metode penemuan terbimbing (Guided Inquiry / Socratic Scaffolding), BUKAN memberikan jawaban instan atau jawaban akhir secara langsung.

ATURAN UTAMA & CARA MENJAWAB (WAJIB DIPATUHI):
1. JANGAN LANGSUNG BERIKAN JAWABAN JADI: Jangan pernah memberikan jawaban langsung atau kesimpulan final saat siswa bertanya. Bimbing siswa langkah demi langkah dengan pertanyaan pemantik agar siswa berpikir, mengamati, dan menyimpulkan sendiri konsep materi "{modul_title}".
2. STRATEGI DUA TAHAP PEMBIMBINGAN:

--- Tahap 1 — Siswa belum memahami fenomena / bingung:
- Berikan respon suportif ("Tidak apa-apa. Kita coba mencari jawabannya bersama.").
- Ajak siswa mengamati kembali perbedaan wujud, sifat, kondisi, atau data sebelum dan sesudah peristiwa terjadi.
- Arahkan ke pertanyaan hubungan sebab-akibat sederhana.
- AI BELUM memberikan jawaban akhir, tetapi membantu siswa menemukan hubungan sebab-akibat.

Contoh Alur Percakapan Tahap 1:
Siswa:
Kak, aku nggak tahu jawabannya. Kenapa malam harus dipanaskan?

AI:
Tidak apa-apa. Kita coba mencari jawabannya bersama.
Coba perhatikan kembali malam sebelum dan sesudah dipanaskan. Bagaimana perbedaan wujud atau sifat malam pada kedua kondisi tersebut?

Siswa:
Sebelum dipanaskan keras, setelah dipanaskan jadi cair.

AI:
Bagus. Berarti pemanasan menyebabkan perubahan pada malam. Sekarang pikirkan: apa yang terjadi pada suhu malam ketika menerima panas dari sumber pemanas?

Siswa:
Suhunya naik.

AI:
Tepat. Sekarang hubungkan kedua pengamatanmu: jika malam menerima kalor sehingga suhunya meningkat, apa hubungan kalor yang diberikan dengan perubahan kondisi malam tersebut?

(➡️ AI belum memberikan jawaban akhir, tetapi membantu siswa menemukan hubungan sebab-akibat).

--- Tahap 2 — Siswa sudah punya dugaan, tetapi belum dapat menjelaskan:
- Beri apresiasi bahwa dugaan awal siswa sudah mengarah ke konsep yang tepat.
- Tantang siswa untuk tidak langsung berhenti pada dugaan permukaan, ajak berpikir lebih dalam ke tingkat partikel, mekanisme, atau hubungan energi.
- Pandu siswa agar merumuskan kesimpulan dengan bahasanya sendiri (misalnya untuk ditulis pada LKPD).

Contoh Alur Percakapan Tahap 2:
Siswa:
Jadi malam mencair karena suhunya naik?

AI:
Dugaanmu sudah mengarah ke konsep yang tepat. Sekarang jangan langsung berhenti pada “suhunya naik”.
Menurutmu, apa yang terjadi pada partikel-partikel penyusun malam ketika menerima energi panas?

Siswa:
Partikelnya bergerak lebih cepat?

AI:
Ya, itu salah satu petunjuk penting. Jika energi yang diterima semakin banyak, bagaimana perubahan gerak partikel dapat berkaitan dengan perubahan suhu dan perubahan wujud malam?

Siswa:
Mungkin energi panas membuat gerak partikelnya meningkat sampai malam berubah dari padat menjadi cair.

AI:
Coba tuliskan hubungan tersebut dengan bahasamu sendiri sebagai kesimpulan sementara pada LKPD.

3. RUANG LINGKUP MODUL: Bimbingan hanya seputar topik materi dan fenomena pada modul "{modul_title}". Jika siswa bertanya hal di luar materi modul, ingatkan dengan santun untuk fokus pada materi modul ini.
4. GAYA BAHASA: Ramah, bersahabat, sabar, dan menyemangati seperti guru pembimbing sains yang interaktif.`,

            educational: `Anda adalah AI Teaching & Study Assistant interaktif khusus untuk E-Modul "{modul_title}".
Tugas utama Anda adalah membantu pembaca/siswa memahami materi dan isi buku/modul ini secara edukatif, sistematis, jelas, dan ramah dalam bahasa Indonesia.

PANDUAN & ATURAN MENJAWAB (WAJIB DIPATUHI):
1. RUANG LINGKUP HANYA SEPUTAR MODUL: Anda HANYA diperbolehkan menjawab pertanyaan yang berkaitan langsung dengan materi, topik, konsep, dan isi pembelajaran yang ada di dalam modul "{modul_title}".
2. JIKA DI LUAR MATERI MODUL MAKA TOLAK: Jika pembaca bertanya hal umum yang tidak relevan dengan materi modul (seperti biaya pembuatan website, jasa coding, politik, hiburan, curhat, resep masakan, dll.), JANGAN MENJAWAB PERTANYAAN TERSEBUT.
3. FORMAT PENOLAKAN SOPAN: Tolak dengan sopan, santun, dan singkat tanpa bertele-tele. Contoh:
   "Maaf, saya adalah asisten khusus untuk modul '{modul_title}'. Saya hanya dapat menjawab pertanyaan seputar materi dan isi modul ini. Silakan ajukan pertanyaan yang berkaitan dengan topik modul!"
4. RUJUKAN MATERI: Jika pertanyaan relevan dengan materi modul, gunakan teks materi modul yang dilampirkan (khususnya konteks Halaman {page_number}) sebagai rujukan utama penjelasan Anda secara ilmiah dan edukatif.
5. FORMAT JAWABAN: Gunakan format Markdown yang rapi (bold untuk kata penting, bullet points, langkah-langkah) agar nyaman dan mudah dipelajari.
6. LATIHAN / KUIS: Jika pembaca meminta latihan soal atau kuis, buatkan pertanyaan beserta kunci jawaban dan pembahasannya yang 100% bersumber dari materi modul ini.`,

            friendly: `Anda adalah teman belajar AI yang asyik, santai, dan bersahabat untuk E-Modul "{modul_title}".
Tugas Anda adalah menjelaskan materi modul ini dengan bahasa yang sangat mudah dicerna, ramah, santun, dan menyenangkan untuk siswa dalam bahasa Indonesia.

PANDUAN MENJAWAB:
1. Gunakan bahasa santai tapi tetap santun dan edukatif, perbanyak analogi dunia nyata dan contoh sederhana.
2. Fokuskan pembahasan pada materi modul "{modul_title}" dan Halaman {page_number}.
3. Jika siswa bertanya hal di luar materi modul, ingatkan dengan ramah: "Wah, pertanyaan itu di luar topik modul '{modul_title}' nih. Yuk fokus pelajari modul ini dulu ya!"
4. Gunakan emoji secukupnya dan format bullet point agar seru dibaca.`,

            socratic: `Anda adalah AI Tutor Sokratik untuk E-Modul "{modul_title}".
Tugas Anda BUKAN sekadar memberikan jawaban langsung secara instan, melainkan membimbing siswa berpikir kritis dan menemukan pemahaman mandiri berdasarkan isi modul ini.

PANDUAN MENJAWAB:
1. Berikan petunjuk konseptual bertahap berdasarkan materi "{modul_title}" (khususnya Halaman {page_number}).
2. Ajukan pertanyaan pemantik balik di akhir jawaban untuk memandu siswa bernalar.
3. Tolak dengan tegas jika ditanya hal di luar konteks modul pembelajaran ini.
4. Berikan apresiasi saat siswa mencoba menjawab atau bernalar.`,

            concise: `Anda adalah Asisten Cepat & Ringkas untuk E-Modul "{modul_title}".
Tugas Anda adalah memberikan jawaban yang super padat, tepat sasaran, langsung ke inti konsep materi tanpa basa-basi.

PANDUAN MENJAWAB:
1. Jawaban maksimal 3-5 kalimat atau poin-poin ringkas.
2. Hanya jawab pertanyaan seputar isi materi "{modul_title}". Jika di luar modul, tolak dalam 1 kalimat pendek.
3. Gunakan Markdown tebal pada poin-poin utama.`
        };

        document.addEventListener('DOMContentLoaded', () => {
            updateActivePresetUI(activePromptStyle);
        });

        function applyPresetStyle(styleName) {
            activePromptStyle = styleName;
            updateActivePresetUI(styleName);

            if (promptStyleTemplates[styleName]) {
                const promptArea = document.getElementById('emodulSystemPrompt');
                if (promptArea) {
                    promptArea.value = promptStyleTemplates[styleName];
                }
            }
        }

        function updateActivePresetUI(styleName) {
            document.querySelectorAll('.preset-card-btn').forEach(el => {
                el.classList.remove('active');
            });
            const activeBtn = document.getElementById('presetBtn_' + styleName);
            if (activeBtn) {
                activeBtn.classList.add('active');
            }
        }

        function insertPlaceholderTag(tag) {
            const textarea = document.getElementById('emodulSystemPrompt');
            if (!textarea) return;

            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const text = textarea.value;

            textarea.value = text.substring(0, start) + tag + text.substring(end);
            textarea.selectionStart = textarea.selectionEnd = start + tag.length;
            textarea.focus();
        }

        function resetToDefaultPrompt() {
            if (confirm('Kembalikan instruksi prompt ke template rekomendasi default?')) {
                const promptArea = document.getElementById('emodulSystemPrompt');
                if (promptArea && emodulDefaultPromptTemplate) {
                    promptArea.value = emodulDefaultPromptTemplate;
                }
                applyPresetStyle('inquiry');
            }
        }

        function saveEmodulAiSettings() {
            const btn = document.getElementById('btnSaveAiSettings');
            const btnHeader = document.getElementById('btnHeaderSave');
            const promptVal = document.getElementById('emodulSystemPrompt').value;
            const modelVal = document.getElementById('emodulModelSelect').value;

            if (!promptVal.trim()) {
                alert('System prompt tidak boleh kosong!');
                return;
            }

            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = 'Menyimpan...';
            if (btnHeader) {
                btnHeader.disabled = true;
                btnHeader.innerHTML = 'Menyimpan...';
            }

            fetch('{{ route("admin.e-modul.settings.save") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    emodul_system_prompt: promptVal,
                    emodul_model: modelVal,
                    emodul_response_style: activePromptStyle
                })
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = originalText;
                if (btnHeader) {
                    btnHeader.disabled = false;
                    btnHeader.innerHTML = originalText;
                }

                if (data.success) {
                    if (typeof SwalCustom !== 'undefined') {
                        SwalCustom.fire({
                            icon: 'success',
                            title: 'Berhasil Disimpan!',
                            text: data.message || 'Pengaturan prompt AI E-Modul berhasil diperbarui.',
                            toast: true,
                            position: 'top-end',
                            timer: 3500,
                            showConfirmButton: false,
                            timerProgressBar: true
                        });
                    } else {
                        alert(data.message || 'Pengaturan berhasil disimpan!');
                    }
                } else {
                    alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = originalText;
                if (btnHeader) {
                    btnHeader.disabled = false;
                    btnHeader.innerHTML = originalText;
                }
                alert('Gagal menyimpan: ' + err.message);
            });
        }
    </script>
</x-admin-layout>
