<x-admin-layout title="E-Modul & Flipbook" breadcrumb="Kelola modul pembelajaran interaktif dengan format 3D Flipbook & AI Assistant">

    <!-- PDF.js, StPageFlip & Marked Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/page-flip@2.0.7/dist/js/page-flip.browser.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <!-- Upload Hero Dropzone Section -->
    @can('emodul.create')
    <div class="card" style="padding:28px; margin-bottom:28px; background:linear-gradient(135deg, var(--accent-soft) 0%, rgba(184, 255, 0, 0.02) 100%); border:1px solid rgba(184, 255, 0, 0.2);">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-bottom:20px;">
            <div>
                <h2 style="font-size:18px; font-weight:700; color:var(--text-primary); display:flex; align-items:center; gap:8px;">
                    <span style="font-size:22px;">📚</span> Upload File PDF E-Modul
                </h2>
                <p style="font-size:13px; color:var(--text-secondary); margin-top:4px;">
                    Unggah file PDF modul Anda. Otomatis dikonversi menjadi <strong>Interactive 3D Flipbook</strong> dengan <strong>AI Asisten Modul</strong>.
                </p>
            </div>
            <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                @can('emodul.edit')
                <a href="{{ route('admin.e-modul.settings') }}" class="topbar-btn" style="text-decoration:none; padding:8px 16px; font-size:13px; border:1px solid rgba(184, 255, 0, 0.3); background:var(--accent-soft); color:var(--badge-accent-text, var(--accent)); cursor:pointer; display:inline-flex; align-items:center; gap:8px; font-weight:700; border-radius:8px;" title="Buka Halaman Setting Prompt & AI E-Modul">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l-.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l-.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1Z"/></svg>
                    <span>Setting Prompt AI</span>
                </a>
                @endcan

                <button type="button" onclick="openUploadModal()" class="topbar-btn topbar-btn-primary" style="padding:8px 18px; font-size:13px; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:8px;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    Pilih File PDF
                </button>
            </div>
        </div>

        <!-- Drag & Drop Area -->
        <div id="quickDropzone" onclick="openUploadModal()" style="border:2px dashed var(--border-light, #3b4252); border-radius:14px; padding:32px 20px; text-align:center; background:var(--bg-elevated); cursor:pointer; transition:all 0.25s ease;" ondragover="event.preventDefault(); this.style.borderColor='var(--accent)';" ondragleave="this.style.borderColor='var(--border-light)';" ondrop="handleDropFile(event)">
            <div style="width:52px; height:52px; border-radius:12px; background:var(--accent-soft); display:inline-flex; align-items:center; justify-content:center; color:var(--accent); font-size:24px; margin-bottom:12px;">
                📄
            </div>
            <div style="font-size:14.5px; font-weight:600; color:var(--text-primary);">
                Tarik dan lepaskan file PDF di sini, atau <span style="color:var(--accent); text-decoration:underline;">klik untuk memilih</span>
            </div>
            <div style="font-size:12px; color:var(--text-muted); margin-top:6px;">
                Mendukung format PDF (Maksimal 100 MB). Otomatis render preview flipbook interaktif.
            </div>
        </div>
    </div>
    @endcan

    <!-- E-Modul Library Grid Header -->
    <div style="margin-bottom:16px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div style="display:flex; align-items:center; gap:12px;">
            <h3 style="font-size:16px; font-weight:700; color:var(--text-primary); margin:0;">Koleksi E-Modul</h3>
            <span style="font-size:12.5px; color:var(--text-secondary);">{{ $moduls->count() }} modul tersedia</span>
        </div>
        <!-- @can('emodul.edit') -->
        <!-- <a href="{{ route('admin.e-modul.settings') }}" class="topbar-btn" style="text-decoration:none; padding:6px 14px; font-size:12px; color:var(--badge-accent-text, var(--accent)); border:1px solid rgba(184, 255, 0, 0.25); background:var(--accent-soft); font-weight:700; display:inline-flex; align-items:center; gap:6px; cursor:pointer; border-radius:8px;" title="Buka Halaman Setting Prompt AI">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l-.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l-.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1Z"/></svg>
            <span>Setting Prompt AI</span>
        </a>
        @endcan --> 
    </div>

    @if($moduls->count() > 0)
    <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap:20px; margin-bottom:30px;">
        @foreach($moduls as $modul)
        <div class="card modul-card" style="display:flex; flex-direction:column; overflow:hidden; border:1px solid var(--border); transition:transform 0.2s, box-shadow 0.2s;">
            <!-- Card Thumbnail Top (Clickable) -->
            <div onclick="openModulFlipbookById('{{ $modul->id }}')" style="height:170px; background:linear-gradient(135deg, #1e1e24 0%, #2b2d42 100%); position:relative; display:flex; align-items:center; justify-content:center; overflow:hidden; border-bottom:1px solid var(--border); cursor:pointer;" title="Klik untuk membuka Flipbook">
                <!-- Book 3D Mockup Icon -->
                <div style="width:100px; height:130px; background:#ffffff; border-radius:4px 8px 8px 4px; box-shadow:-5px 5px 15px rgba(0,0,0,0.5), inset 4px 0 8px rgba(0,0,0,0.15); display:flex; flex-direction:column; align-items:center; justify-content:center; padding:10px; border-left:4px solid var(--accent); position:relative; transition:transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    <div style="font-size:26px; margin-bottom:4px;">📖</div>
                    <div style="font-size:9px; font-weight:700; color:#1e293b; text-align:center; line-height:1.2; max-width:80px; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;">
                        {{ $modul->title }}
                    </div>
                    <span style="position:absolute; bottom:6px; font-size:8px; color:#64748b; font-family:'JetBrains Mono',monospace;">FLIPBOOK</span>
                </div>

                <!-- Status Badge -->
                <div style="position:absolute; top:12px; right:12px;" onclick="event.stopPropagation()">
                    @can('emodul.toggle-active')
                    <form action="{{ route('admin.e-modul.toggle-active', $modul->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" style="background:none; border:none; padding:0; cursor:pointer;" title="Klik untuk mengubah status (Published / Draft)">
                            @if($modul->is_active)
                                <span class="badge badge-success" style="font-size:10px; padding:3px 8px;">Published</span>
                            @else
                                <span class="badge badge-warning" style="font-size:10px; padding:3px 8px;">Draft</span>
                            @endif
                        </button>
                    </form>
                    @else
                        @if($modul->is_active)
                            <span class="badge badge-success" style="font-size:10px; padding:3px 8px;">Published</span>
                        @else
                            <span class="badge badge-warning" style="font-size:10px; padding:3px 8px;">Draft</span>
                        @endif
                    @endcan
                </div>

                <!-- Category Pill -->
                <div style="position:absolute; top:12px; left:12px;" onclick="event.stopPropagation()">
                    <span style="background:rgba(0,0,0,0.6); backdrop-filter:blur(6px); color:#e2e8f0; font-size:10.5px; font-weight:600; padding:2px 8px; border-radius:6px; border:1px solid rgba(255,255,255,0.1);">
                        {{ $modul->category ?? 'Umum' }}
                    </span>
                </div>
            </div>

            <!-- Card Body -->
            <div style="padding:16px; flex:1; display:flex; flex-direction:column;">
                <h4 style="font-size:14.5px; font-weight:700; color:var(--text-primary); margin-bottom:6px; line-height:1.3; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $modul->title }}">
                    {{ $modul->title }}
                </h4>
                <p style="font-size:12px; color:var(--text-secondary); margin-bottom:14px; flex:1; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; line-height:1.4;">
                    {{ $modul->description ?: 'Tidak ada deskripsi modul.' }}
                </p>

                <!-- Meta Details -->
                <div style="display:flex; justify-content:space-between; align-items:center; font-size:11.5px; color:var(--text-muted); margin-bottom:14px; padding-top:10px; border-top:1px solid var(--border); flex-wrap:wrap; gap:6px;">
                    <span style="display:inline-flex; align-items:center; gap:4px;">
                        📄 {{ $modul->total_pages > 0 ? $modul->total_pages . ' Hal' : 'PDF' }}
                    </span>
                    <span>💾 {{ $modul->formatted_size }}</span>
                    @if($modul->hasCache())
                        <span style="font-size:10px; font-weight:700; color:#b8ff00; background:rgba(184,255,0,0.12); border:1px solid rgba(184,255,0,0.25); border-radius:4px; padding:1px 6px;" title="Cache WebP siap: buka modul instan tanpa download ulang PDF">⚡ Cached</span>
                    @else
                        <span style="font-size:10px; color:var(--text-muted); background:var(--bg-surface); border:1px solid var(--border-light); border-radius:4px; padding:1px 6px;" title="Belum di-cache: akan otomatis di-cache saat dibuka pertama kali">PDF Asli</span>
                    @endif
                </div>

                <!-- Actions -->
                <div style="display:flex; gap:8px;">
                    <!-- Preview Flipbook Button -->
                    <!-- <button type="button" 
                        id="btn-flip-{{ $modul->id }}"
                        data-id="{{ $modul->id }}"
                        data-title="{{ $modul->title }}" 
                        data-pdf-url="{{ $modul->pdf_url }}" 
                        data-show-url="{{ route('admin.e-modul.show', $modul->id) }}"
                        onclick="handleFlipbookButtonClick(this)" 
                        class="topbar-btn topbar-btn-primary" 
                        style="flex:1; padding:7px 10px; font-size:12px; border:none; cursor:pointer; justify-content:center; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                        Flipbook
                    </button> -->

                    <!-- Open in New Tab Fullscreen -->
                    <a href="{{ route('admin.e-modul.show', $modul->id) }}" target="_blank" class="topbar-btn" title="Buka Fullscreen Flipbook" style="padding:7px 9px; font-size:12px; text-decoration:none; display:inline-flex; align-items:center; justify-content:center;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                        <p>Buka</p>
                    </a>

                    <!-- Copy Share Link (ekscoder.com) -->
                    <button type="button" 
                        class="topbar-btn" 
                        title="Salin Link Publik (ekscoder.com)" 
                        style="padding:7px 9px; font-size:12px; cursor:pointer; color:#b8ff00; border:1px solid rgba(184, 255, 0, 0.25); background:rgba(184, 255, 0, 0.06); display:inline-flex; align-items:center; justify-content:center;"
                        onclick="copyModulShareLink('{{ $modul->public_share_url }}', this)">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                    </button>


                    <!-- Edit -->
                    @can('emodul.edit')
                    <a href="{{ route('admin.e-modul.edit', $modul->id) }}" class="topbar-btn" title="Edit Modul" style="padding:7px 9px; font-size:12px; text-decoration:none; display:inline-flex; align-items:center; justify-content:center;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </a>
                    @endcan

                    <!-- Delete -->
                    @can('emodul.delete')
                    <form action="{{ route('admin.e-modul.destroy', $modul->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus e-modul ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="topbar-btn" title="Hapus Modul" style="padding:7px 9px; font-size:12px; color:var(--rose); border:1px solid var(--border); background:var(--bg-elevated); cursor:pointer; display:inline-flex; align-items:center; justify-content:center; border-radius:8px;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="card" style="padding:40px 20px; text-align:center; margin-bottom:30px;">
        <div style="font-size:36px; margin-bottom:12px;">📚</div>
        <h4 style="font-size:16px; font-weight:700; color:var(--text-primary); margin-bottom:6px;">Belum Ada File E-Modul yang Diunggah</h4>
        <p style="font-size:13px; color:var(--text-muted); max-width:420px; margin:0 auto 18px auto;">
            Unggah modul berformat PDF pertama Anda untuk menikmati sensasi membaca buku interaktif layaknya FlipHTML5.
        </p>
        @can('emodul.create')
        <button type="button" onclick="openUploadModal()" class="topbar-btn topbar-btn-primary" style="margin:0 auto; padding:8px 20px; font-size:13px; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Upload Dokumen PDF Sekarang
        </button>
        @endcan
    </div>
    @endif



    <!-- Upload Modal -->
    @can('emodul.create')
    <div id="uploadModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.75); backdrop-filter:blur(8px); z-index:9999; align-items:center; justify-content:center; padding:20px;">
        <div class="card" style="width:100%; max-width:600px; padding:26px; border:1px solid var(--border); box-shadow:0 25px 50px -12px rgba(0,0,0,0.7); max-height:90vh; overflow-y:auto;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; border-bottom:1px solid var(--border); padding-bottom:14px;">
                <div>
                    <h3 style="font-size:17px; font-weight:700; color:var(--text-primary);">Upload File PDF E-Modul</h3>
                    <p style="font-size:12.5px; color:var(--text-secondary); margin-top:2px;">Dokumen akan diproses otomatis untuk Flipbook reader</p>
                </div>
                <button type="button" onclick="closeUploadModal()" style="background:none; border:none; color:var(--text-muted); font-size:20px; cursor:pointer; padding:4px 8px; line-height:1;">✕</button>
            </div>

            <form id="uploadForm" action="{{ route('admin.e-modul.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="total_pages" id="detectedTotalPages" value="0">

                <div style="display:flex; flex-direction:column; gap:16px;">
                    <!-- File Picker Input -->
                    <div>
                        <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">Pilih File PDF <span style="color:var(--rose);">*</span></label>
                        <div style="border:2px dashed var(--border); border-radius:10px; padding:20px; text-align:center; background:var(--bg-elevated); cursor:pointer; position:relative;" onclick="document.getElementById('pdfFileInput').click()">
                            <input type="file" id="pdfFileInput" name="pdf_file" accept=".pdf" required style="position:absolute; inset:0; opacity:0; cursor:pointer;" onchange="handleFileSelect(this)">
                            <div id="filePickerPlaceholder">
                                <div style="font-size:28px; margin-bottom:4px;">📄</div>
                                <div style="font-size:13.5px; font-weight:600; color:var(--text-primary);">Klik atau geser file PDF ke sini</div>
                                <div style="font-size:11.5px; color:var(--text-muted); margin-top:2px;">Format .PDF hingga 100MB</div>
                            </div>
                            <div id="filePickerSelected" style="display:none; text-align:left;">
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <div style="font-size:28px;">📑</div>
                                    <div style="flex:1; overflow:hidden;">
                                        <div id="selectedFileName" style="font-size:13.5px; font-weight:600; color:var(--text-primary); text-overflow:ellipsis; overflow:hidden; white-space:nowrap;">filename.pdf</div>
                                        <div id="selectedFileSize" style="font-size:12px; color:var(--accent);">0 KB | Mendeteksi halaman...</div>
                                    </div>
                                    <button type="button" onclick="event.stopPropagation(); resetFilePicker();" style="background:none; border:none; color:var(--rose); font-size:16px; cursor:pointer;">✕</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Title -->
                    <div>
                        <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">Judul E-Modul <span style="color:var(--rose);">*</span></label>
                        <input type="text" id="modulTitleInput" name="title" required placeholder="Contoh: Modul Pembelajaran IPA Kelas 7" style="width:100%; background:var(--bg-elevated); border:1px solid var(--border); border-radius:8px; padding:10px 14px; font-size:13.5px; color:var(--text-primary); outline:none;">
                    </div>

                    <!-- Category -->
                    <div>
                        <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">Kategori / Mata Pelajaran</label>
                        <input type="text" name="category" placeholder="Contoh: IPA, Pemrograman, Sejarah" style="width:100%; background:var(--bg-elevated); border:1px solid var(--border); border-radius:8px; padding:10px 14px; font-size:13.5px; color:var(--text-primary); outline:none;">
                    </div>

                    <!-- Description -->
                    <div>
                        <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">Deskripsi Singkat (Opsional)</label>
                        <textarea name="description" rows="2" placeholder="Tuliskan ringkasan modul..." style="width:100%; background:var(--bg-elevated); border:1px solid var(--border); border-radius:8px; padding:10px 14px; font-size:13.5px; color:var(--text-primary); outline:none;"></textarea>
                    </div>

                    <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:8px; padding-top:14px; border-top:1px solid var(--border);">
                        <button type="button" onclick="closeUploadModal()" class="topbar-btn" style="padding:8px 16px; font-size:13px;">Batal</button>
                        <button type="button" id="btnPreviewBeforeUpload" onclick="previewLocalPdf()" class="topbar-btn" style="padding:8px 16px; font-size:13px; display:none;">
                            👁️ Tes Flipbook
                        </button>
                        <button type="submit" id="btnSubmitUpload" class="topbar-btn topbar-btn-primary" style="padding:8px 22px; font-size:13px; border:none; cursor:pointer;">
                            Upload & Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endcan

    <!-- FlipHTML5-style Interactive Modal Reader with AI Assistant -->
    <div id="flipModal" style="display:none; position:fixed; inset:0; background:#3b3c3d; background-image:radial-gradient(circle at center, #4b4c4e 0%, #292a2b 100%); z-index:100000; flex-direction:column; overflow:hidden; user-select:none;">
        <!-- Modal Top Bar -->
        <div style="height:48px; background:rgba(25, 26, 27, 0.9); backdrop-filter:blur(10px); border-bottom:1px solid rgba(255, 255, 255, 0.08); display:flex; align-items:center; justify-content:space-between; padding:0 20px; z-index:50;">
            <div style="display:flex; align-items:center; gap:12px;">
                <button type="button" onclick="closeFlipModal()" style="background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.12); color:#e2e8f0; padding:5px 12px; border-radius:6px; font-size:12px; cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    Tutup Preview
                </button>
                <span style="color:#94a3b8; font-size:12px; font-weight:600;">📖 FLIPHTML5 Reader</span>
            </div>

            <div id="modalBookTitle" style="font-size:14px; font-weight:600; color:#ffffff; max-width:40%; text-overflow:ellipsis; overflow:hidden; white-space:nowrap; text-align:center;">
                E-Modul Flipbook Preview
            </div>

            <div style="display:flex; align-items:center; gap:8px;">
                <!-- AI Assistant Trigger -->
                @can('emodul.ask-ai')
                <button type="button" onclick="toggleModalAiDrawer()" style="color:#38bdf8; background:rgba(56, 189, 248, 0.15); border:1px solid rgba(56, 189, 248, 0.3); padding:5px 12px; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
                    <span>✨</span> Tanya AI
                </button>
                @endcan

                <a id="modalStandaloneLink" href="#" target="_blank" style="background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.12); color:#cbd5e1; text-decoration:none; padding:5px 10px; border-radius:6px; font-size:12px; display:inline-flex; align-items:center; gap:5px;" title="Buka di tab baru">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                    Tab Baru
                </a>
                <button type="button" onclick="toggleModalFullscreen()" style="background:none; border:none; color:#cbd5e1; padding:6px; cursor:pointer; border-radius:6px;" title="Layar Penuh">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/></svg>
                </button>
            </div>
        </div>

        <!-- Flipbook Area -->
        <div style="flex:1; position:relative; display:flex; align-items:center; justify-content:center; padding:20px 40px 60px 40px; overflow:hidden;">
            <!-- Navigation Arrows -->
            <button id="modalPrevArrow" style="position:absolute; left:20px; top:50%; transform:translateY(-50%); width:46px; height:46px; background:rgba(30,30,30,0.8); border:1px solid rgba(255,255,255,0.15); color:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; cursor:pointer; z-index:40; backdrop-filter:blur(8px); transition:all 0.2s;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            </button>

            <button id="modalNextArrow" style="position:absolute; right:20px; top:50%; transform:translateY(-50%); width:46px; height:46px; background:rgba(30,30,30,0.8); border:1px solid rgba(255,255,255,0.15); color:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; cursor:pointer; z-index:40; backdrop-filter:blur(8px); transition:all 0.2s;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </button>

            <!-- Loading Spinner inside Modal -->
            <div id="modalLoadingOverlay" style="position:absolute; inset:0; background:rgba(30, 31, 32, 0.94); backdrop-filter:blur(10px); display:flex; flex-direction:column; align-items:center; justify-content:center; z-index:100; gap:16px;">
                <div style="width:44px; height:44px; border:3px solid rgba(255,255,255,0.1); border-top-color:#6366f1; border-radius:50%; animation:modalSpin 0.8s linear infinite;"></div>
                <div id="modalLoadingText" style="font-size:14px; font-weight:500; color:#e2e8f0;">Memuat Flipbook E-Modul...</div>
            </div>

            <!-- Flipbook Container -->
            <div id="modalFlipWrapper" style="position:relative; display:flex; align-items:center; justify-content:center; transition:transform 0.2s ease; box-shadow:0 25px 60px -15px rgba(0,0,0,0.7); border-radius:4px;">
                <div id="modalFlipbook" style="display:none; background:transparent;"></div>
            </div>

            <!-- Thumbnails Drawer -->
            <div id="modalThumbsDrawer" style="position:absolute; bottom:65px; left:50%; transform:translateX(-50%) translateY(20px); width:85vw; max-width:900px; height:140px; background:rgba(20,21,23,0.95); backdrop-filter:blur(16px); border:1px solid rgba(255,255,255,0.15); border-radius:12px; padding:12px; display:none; gap:10px; overflow-x:auto; z-index:60; box-shadow:0 15px 40px rgba(0,0,0,0.6);"></div>

            <!-- Floating AI Trigger Button inside Modal (Bottom Right) -->
            <button type="button" onclick="toggleModalAiDrawer()" style="position:absolute; bottom:20px; right:20px; height:46px; padding:0 16px 0 12px; background:linear-gradient(135deg, #0284c7 0%, #3b82f6 50%, #6366f1 100%); color:#fff; border-radius:23px; border:1px solid rgba(255,255,255,0.25); display:flex; align-items:center; gap:8px; cursor:pointer; z-index:90; font-size:13px; font-weight:600; box-shadow:0 10px 25px -5px rgba(2, 132, 199, 0.6);">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <span>Tanya AI</span>
            </button>

            <!-- Bottom Controls Bar (FlipHTML5 style) -->
            <div style="position:absolute; bottom:12px; left:50%; transform:translateX(-50%); height:44px; background:rgba(22, 23, 24, 0.92); backdrop-filter:blur(12px); border:1px solid rgba(255, 255, 255, 0.12); border-radius:22px; display:flex; align-items:center; padding:0 16px; gap:10px; z-index:50; box-shadow:0 10px 30px rgba(0,0,0,0.5);">
                <!-- First Page -->
                <button type="button" id="modalBtnFirst" style="background:none; border:none; color:#cbd5e1; width:30px; height:30px; border-radius:6px; cursor:pointer; display:flex; align-items:center; justify-content:center;" title="Awal">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="11 17 6 12 11 7"/><polyline points="18 17 13 12 18 7"/></svg>
                </button>

                <!-- Zoom Out / In -->
                <button type="button" id="modalBtnZoomOut" style="background:none; border:none; color:#cbd5e1; width:30px; height:30px; border-radius:6px; cursor:pointer; display:flex; align-items:center; justify-content:center;" title="Perkecil (-)">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </button>
                <button type="button" id="modalBtnZoomIn" style="background:none; border:none; color:#cbd5e1; width:30px; height:30px; border-radius:6px; cursor:pointer; display:flex; align-items:center; justify-content:center;" title="Perbesar (+)">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </button>

                <!-- Thumbnails Grid -->
                <button type="button" id="modalBtnToggleThumbs" style="background:none; border:none; color:#cbd5e1; width:30px; height:30px; border-radius:6px; cursor:pointer; display:flex; align-items:center; justify-content:center;" title="Thumbnail">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                </button>

                <!-- Sound Toggle -->
                <button type="button" id="modalBtnToggleSound" style="background:none; border:none; color:#cbd5e1; width:30px; height:30px; border-radius:6px; cursor:pointer; display:flex; align-items:center; justify-content:center;" title="Suara Kertas (Mati)">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"/></svg>
                </button>

                <div style="width:1px; height:18px; background:rgba(255,255,255,0.12);"></div>

                <!-- Page Indicator & Scrubber -->
                <span id="modalPageIndicator" style="font-family:'JetBrains Mono',monospace; font-size:12px; font-weight:600; color:#e2e8f0; min-width:65px; text-align:center;">0 / 0</span>
                <input type="range" id="modalPageSlider" min="1" max="1" value="1" style="-webkit-appearance:none; width:130px; height:4px; background:rgba(255,255,255,0.2); border-radius:2px; cursor:pointer;">

                <div style="width:1px; height:18px; background:rgba(255,255,255,0.12);"></div>

                <!-- Last Page -->
                <button type="button" id="modalBtnLast" style="background:none; border:none; color:#cbd5e1; width:30px; height:30px; border-radius:6px; cursor:pointer; display:flex; align-items:center; justify-content:center;" title="Akhir">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="13 17 18 12 13 7"/><polyline points="6 17 11 12 6 7"/></svg>
                </button>
            </div>
        </div>

        <!-- AI Assistant Sidebar Drawer (Inside Modal) -->
        @can('emodul.ask-ai')
        <aside id="modalAiDrawer" style="position:absolute; top:48px; right:0; bottom:0; width:390px; max-width:92vw; background:rgba(18, 20, 24, 0.96); backdrop-filter:blur(20px); border-left:1px solid rgba(255,255,255,0.12); display:flex; flex-direction:column; z-index:100; transform:translateX(100%); transition:transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow:-15px 0 40px rgba(0,0,0,0.6);">
            <!-- AI Header -->
            <div style="padding:14px 18px; border-bottom:1px solid rgba(255,255,255,0.08); display:flex; flex-direction:column; gap:10px; background:rgba(255,255,255,0.02);">
                <div style="display:flex; align-items:center; justify-content:space-between;">
                    <div style="display:flex; align-items:center; gap:8px; font-size:14px; font-weight:700; color:#f8fafc;">
                        <span style="font-size:16px;">✨</span> AI Asisten Modul
                    </div>
                    <button type="button" onclick="toggleModalAiDrawer()" style="background:none; border:none; color:#94a3b8; font-size:18px; cursor:pointer; padding:4px 8px; line-height:1;">✕</button>
                </div>

                <!-- Scope pills inside Modal -->
                <div style="display:flex; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); border-radius:8px; padding:2px; gap:2px;">
                    <button type="button" id="modalScopeActiveBtn" onclick="setModalAiScope('active')" style="flex:1; padding:5px 8px; font-size:11.5px; font-weight:600; border-radius:6px; background:#6366f1; border:none; color:#ffffff; cursor:pointer; transition:all 0.15s; text-align:center;">
                        📍 <span id="modalAiActivePageBadge">Hal 1</span>
                    </button>
                    <button type="button" id="modalScopeGlobalBtn" onclick="setModalAiScope('global')" style="flex:1; padding:5px 8px; font-size:11.5px; font-weight:600; border-radius:6px; background:transparent; border:none; color:#94a3b8; cursor:pointer; transition:all 0.15s; text-align:center;">
                        🌐 Seluruh Modul
                    </button>
                </div>
            </div>

            <!-- Messages Area -->
            <div id="modalAiMessagesContainer" style="flex:1; padding:16px; overflow-y:auto; display:flex; flex-direction:column; gap:14px;">
                <div class="ai-msg assistant" style="align-self:flex-start; background:rgba(255,255,255,0.07); border:1px solid rgba(255,255,255,0.1); color:#e2e8f0; padding:12px 14px; border-radius:14px 14px 14px 2px; font-size:13px; line-height:1.5;">
                    <p style="margin-bottom:6px;">Halo! Saya adalah <strong>AI Asisten Modul</strong>.</p>
                    <p style="margin:0;">Tanyakan materi pada <strong>halaman aktif</strong>, minta pencarian <strong>halaman lain (misal: hal 55)</strong>, atau pertanyaan <strong>global seputar modul</strong>!</p>
                </div>
            </div>

            <!-- Suggestion Chips -->
            <div style="padding:8px 16px; display:flex; gap:6px; overflow-x:auto; scrollbar-width:none; border-top:1px solid rgba(255,255,255,0.06);">
                <button type="button" onclick="askModalQuickPrompt('Jelaskan rangkuman materi pada halaman yang sedang dibuka ini')" style="white-space:nowrap; font-size:11.5px; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); color:#cbd5e1; padding:5px 10px; border-radius:14px; cursor:pointer;">
                    💡 Rangkum Halaman
                </button>
                <button type="button" onclick="askModalQuickPrompt('Jelaskan pokok bahasan dan isi modul ini secara menyeluruh')" style="white-space:nowrap; font-size:11.5px; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); color:#cbd5e1; padding:5px 10px; border-radius:14px; cursor:pointer;">
                    🌐 Rangkuman Global
                </button>
                <button type="button" onclick="askModalQuickPrompt('Buatkan 3 pertanyaan kuis pilihan ganda beserta kunci jawabannya dari materi ini')" style="white-space:nowrap; font-size:11.5px; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); color:#cbd5e1; padding:5px 10px; border-radius:14px; cursor:pointer;">
                    ❓ Buat 3 Kuis
                </button>
            </div>

            <!-- Input Area -->
            <div style="padding:12px 16px 16px 16px; border-top:1px solid rgba(255,255,255,0.08); background:rgba(15, 17, 20, 0.98);">
                <form id="modalAiChatForm" onsubmit="handleSendModalAiMessage(event)">
                    <div style="display:flex; align-items:center; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.15); border-radius:20px; padding:4px 6px 4px 14px;">
                        <input type="text" id="modalAiInputText" placeholder="Tanya materi halaman ini, hal 55, atau seputar modul ini..." autocomplete="off" style="flex:1; background:transparent; border:none; outline:none; color:#ffffff; font-size:13px;">
                        <button type="submit" id="modalAiSubmitBtn" style="width:32px; height:32px; border-radius:50%; background:#6366f1; border:none; color:#ffffff; display:flex; align-items:center; justify-content:center; cursor:pointer;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                        </button>
                    </div>
                </form>
            </div>
        </aside>
        @endcan
    </div>

    <style>
        @keyframes modalSpin {
            to { transform: rotate(360deg); }
        }

        .modal-page {
            background-color: #ffffff;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: inset 0 0 30px rgba(0,0,0,0.05);
        }

        .modal-page-content {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .modal-page-content canvas {
            max-width: 100%;
            max-height: 100%;
            display: block;
        }

        /* 3D Spine Crease Shadows */
        .modal-page.--left .modal-page-content::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            width: 25px;
            background: linear-gradient(to left, rgba(0,0,0,0.18) 0%, rgba(0,0,0,0) 100%);
            pointer-events: none;
        }

        .modal-page.--right .modal-page-content::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 25px;
            background: linear-gradient(to right, rgba(0,0,0,0.18) 0%, rgba(0,0,0,0) 100%);
            pointer-events: none;
        }

        .modal-thumb-item {
            flex-shrink: 0;
            width: 75px;
            height: 100%;
            background: #2a2b2e;
            border: 2px solid transparent;
            border-radius: 6px;
            overflow: hidden;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: all 0.15s;
        }

        .modal-thumb-item:hover {
            border-color: rgba(99, 102, 241, 0.7);
        }

        .modal-thumb-item.active {
            border-color: #6366f1;
            box-shadow: 0 0 10px rgba(99, 102, 241, 0.5);
        }

        .modal-thumb-item canvas {
            max-width: 100%;
            max-height: 85px;
        }

        .modal-thumb-label {
            font-size: 10px;
            font-family: 'JetBrains Mono', monospace;
            background: rgba(0,0,0,0.6);
            width: 100%;
            text-align: center;
            padding: 2px 0;
            color: #cbd5e1;
        }

        .typing-dot {
            width: 5px;
            height: 5px;
            background: #a5b4fc;
            border-radius: 50%;
            animation: typingBounce 1.2s infinite ease-in-out;
        }
        .typing-dot:nth-child(1) { animation-delay: 0s; }
        .typing-dot:nth-child(2) { animation-delay: 0.2s; }
        .typing-dot:nth-child(3) { animation-delay: 0.4s; }
        @keyframes typingBounce {
            0%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-5px); }
        }
    </style>

    <script>
        if (typeof pdfjsLib !== 'undefined') {
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        }

        const GENERAL_ASK_AI_URL = "{{ route('admin.e-modul.ask-ai-general') }}";
        const CSRF_TOKEN = "{{ csrf_token() }}";

        let selectedPdfData = null;
        let modalPageFlip = null;
        let modalSoundEnabled = false;
        let modalZoom = 1;
        let modalTotalPages = 0;
        let currentActiveModulTitle = 'E-Modul';
        let currentActiveModulId = null;
        let modalPageTexts = {};
        let modalChatHistory = [];
        let modalScope = 'active'; // 'active' or 'global'

        function setModalAiScope(scope) {
            modalScope = scope;
            const btnActive = document.getElementById('modalScopeActiveBtn');
            const btnGlobal = document.getElementById('modalScopeGlobalBtn');
            if (scope === 'active') {
                btnActive.style.background = '#6366f1';
                btnActive.style.color = '#ffffff';
                btnGlobal.style.background = 'transparent';
                btnGlobal.style.color = '#94a3b8';
            } else {
                btnGlobal.style.background = '#6366f1';
                btnGlobal.style.color = '#ffffff';
                btnActive.style.background = 'transparent';
                btnActive.style.color = '#94a3b8';
            }
        }

        // Realistic Page Flip Audio Synthesizer
        function playFlipSound() {
            if (!modalSoundEnabled) return;
            try {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!AudioContext) return;
                const ctx = new AudioContext();
                const bufferSize = ctx.sampleRate * 0.15;
                const buffer = ctx.createBuffer(1, bufferSize, ctx.sampleRate);
                const data = buffer.getChannelData(0);
                for (let i = 0; i < bufferSize; i++) {
                    data[i] = Math.random() * 2 - 1;
                }
                const noise = ctx.createBufferSource();
                noise.buffer = buffer;
                const filter = ctx.createBiquadFilter();
                filter.type = 'bandpass';
                filter.frequency.setValueAtTime(800, ctx.currentTime);
                filter.frequency.exponentialRampToValueAtTime(300, ctx.currentTime + 0.15);
                const gain = ctx.createGain();
                gain.gain.setValueAtTime(0.25, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.15);
                noise.connect(filter);
                filter.connect(gain);
                gain.connect(ctx.destination);
                noise.start();
            } catch (e) {}
        }

        function openUploadModal() {
            document.getElementById('uploadModal').style.display = 'flex';
        }

        function closeUploadModal() {
            document.getElementById('uploadModal').style.display = 'none';
        }

        function resetFilePicker() {
            document.getElementById('pdfFileInput').value = '';
            document.getElementById('filePickerPlaceholder').style.display = 'block';
            document.getElementById('filePickerSelected').style.display = 'none';
            document.getElementById('btnPreviewBeforeUpload').style.display = 'none';
            selectedPdfData = null;
        }

        function handleDropFile(e) {
            e.preventDefault();
            e.currentTarget.style.borderColor = 'var(--border-light)';
            if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                const file = e.dataTransfer.files[0];
                if (file.type === 'application/pdf' || file.name.endsWith('.pdf')) {
                    openUploadModal();
                    const input = document.getElementById('pdfFileInput');
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    input.files = dt.files;
                    handleFileSelect(input);
                } else {
                    alert('Harap pilih file dengan format PDF.');
                }
            }
        }

        async function handleFileSelect(input) {
            if (!input.files || !input.files[0]) return;
            const file = input.files[0];

            document.getElementById('filePickerPlaceholder').style.display = 'none';
            document.getElementById('filePickerSelected').style.display = 'block';
            document.getElementById('selectedFileName').textContent = file.name;
            
            const sizeStr = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
            document.getElementById('selectedFileSize').textContent = `${sizeStr} | Membaca dokumen...`;

            const titleInput = document.getElementById('modulTitleInput');
            if (!titleInput.value) {
                const cleanName = file.name.replace(/\.pdf$/i, '').replace(/[-_]/g, ' ');
                titleInput.value = cleanName.charAt(0).toUpperCase() + cleanName.slice(1);
            }

            try {
                const arrayBuffer = await file.arrayBuffer();
                selectedPdfData = arrayBuffer;
                const pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;
                document.getElementById('detectedTotalPages').value = pdf.numPages;
                document.getElementById('selectedFileSize').textContent = `${sizeStr} • ${pdf.numPages} Halaman`;
                document.getElementById('btnPreviewBeforeUpload').style.display = 'inline-flex';
            } catch (err) {
                console.error("PDF read error:", err);
                document.getElementById('selectedFileSize').textContent = `${sizeStr} (Gagal membaca jumlah halaman)`;
            }
        }

        function previewLocalPdf() {
            if (!selectedPdfData) return;
            const title = document.getElementById('modulTitleInput').value || 'Preview Modul';
            closeUploadModal();
            renderFlipbookModal(title, { data: selectedPdfData }, null);
        }

        function handleFlipbookButtonClick(btn) {
            const id = btn.getAttribute('data-id') || null;
            const title = btn.getAttribute('data-title') || 'E-Modul';
            const pdfUrl = btn.getAttribute('data-pdf-url') || '';
            const showUrl = btn.getAttribute('data-show-url') || '#';

            document.getElementById('modalStandaloneLink').href = showUrl;
            renderFlipbookModal(title, pdfUrl, id);
        }

        function openModulFlipbookById(id) {
            const btn = document.getElementById('btn-flip-' + id);
            if (btn) {
                handleFlipbookButtonClick(btn);
            }
        }

        function toggleModalAiDrawer() {
            const drawer = document.getElementById('modalAiDrawer');
            if (!drawer) return;
            const isClosed = drawer.style.transform === 'translateX(100%)' || !drawer.style.transform;
            drawer.style.transform = isClosed ? 'translateX(0)' : 'translateX(100%)';
            if (isClosed) {
                const input = document.getElementById('modalAiInputText');
                if (input) input.focus();
            }
        }

        function getModalActivePageLabel() {
            if (!modalPageFlip) return 'Hal 1';
            const pageIndex = modalPageFlip.getCurrentPageIndex();
            const pageNum = pageIndex + 1;
            if (modalPageFlip.getOrientation() === 'landscape' && pageNum > 1 && pageNum < modalTotalPages) {
                return `Hal ${pageNum}-${pageNum + 1}`;
            }
            return `Hal ${pageNum}`;
        }

        function getModalActivePageTextOnly() {
            if (!modalPageFlip) return '';
            const pageIndex = modalPageFlip.getCurrentPageIndex();
            const pageNum = pageIndex + 1;
            let combined = '';

            if (modalPageFlip.getOrientation() === 'landscape' && pageNum > 1 && pageNum < modalTotalPages) {
                combined = `[Halaman ${pageNum}]: ` + (modalPageTexts[pageNum] || '') + "\n\n" +
                           `[Halaman ${pageNum + 1}]: ` + (modalPageTexts[pageNum + 1] || '');
            } else {
                combined = `[Halaman ${pageNum}]: ` + (modalPageTexts[pageNum] || '');
            }
            return combined;
        }

        function getModalSmartContext(question) {
            // Check specific page mention
            const pageMatch = question.match(/(?:halaman|hal|page)\s*(\d+)/i);
            if (pageMatch && pageMatch[1]) {
                const targetPage = parseInt(pageMatch[1], 10);
                if (modalPageTexts[targetPage]) {
                    return {
                        label: `Halaman ${targetPage}`,
                        text: `[MATERI HALAMAN ${targetPage}]:\n` + modalPageTexts[targetPage]
                    };
                }
            }

            if (modalScope === 'global') {
                return getModalGlobalContext(question);
            }

            const activeLabel = getModalActivePageLabel();
            const activeText = getModalActivePageTextOnly();
            const relevantExtra = searchModalPages(question, 2, [modalPageFlip ? modalPageFlip.getCurrentPageIndex() + 1 : 1]);

            let combined = `[MATERI HALAMAN AKTIF (${activeLabel})]:\n` + activeText;
            if (relevantExtra) {
                combined += "\n\n[BAGIAN LAIN DALAM MODUL YANG TERKAIT]:\n" + relevantExtra;
            }

            return {
                label: activeLabel,
                text: combined
            };
        }

        function getModalGlobalContext(question) {
            let tocText = '';
            for (let i = 1; i <= Math.min(3, modalTotalPages); i++) {
                if (modalPageTexts[i]) tocText += `[Halaman ${i}]:\n` + modalPageTexts[i].substring(0, 800) + "\n\n";
            }

            const searchResults = searchModalPages(question, 4);
            let combined = `[RINGKASAN & DAFTAR ISI MODUL]:\n` + tocText;
            if (searchResults) {
                combined += `\n[BAGIAN-BAGIAN UTAMA TERKAIT TOPIK]:\n` + searchResults;
            }

            return {
                label: `Seluruh Modul (${modalTotalPages} Halaman)`,
                text: combined
            };
        }

        function searchModalPages(query, maxPages = 3, excludePages = []) {
            const words = query.toLowerCase()
                .replace(/[^a-zA-Z0-9\s]/g, '')
                .split(/\s+/)
                .filter(w => w.length >= 3 && !['apa', 'yang', 'dan', 'di', 'pada', 'ini', 'itu', 'saya', 'bisa', 'tolong', 'jelaskan', 'halaman', 'modul'].includes(w));

            if (words.length === 0) return '';

            const scoredPages = [];
            for (let p = 1; p <= modalTotalPages; p++) {
                if (excludePages.includes(p)) continue;
                const text = (modalPageTexts[p] || '').toLowerCase();
                if (!text) continue;

                let score = 0;
                words.forEach(w => {
                    const count = (text.match(new RegExp(w, 'g')) || []).length;
                    score += count;
                });

                if (score > 0) {
                    scoredPages.push({ page: p, score: score, text: modalPageTexts[p] });
                }
            }

            scoredPages.sort((a, b) => b.score - a.score);
            const top = scoredPages.slice(0, maxPages);
            if (top.length === 0) return '';

            return top.map(item => `[Halaman ${item.page}]:\n` + item.text.substring(0, 1200)).join("\n\n");
        }

        function appendModalAiMessage(role, text) {
            const container = document.getElementById('modalAiMessagesContainer');
            const msgDiv = document.createElement('div');
            msgDiv.className = `ai-msg ${role}`;
            msgDiv.style.display = 'flex';
            msgDiv.style.flexDirection = 'column';
            msgDiv.style.fontSize = '13px';
            msgDiv.style.lineHeight = '1.5';
            msgDiv.style.maxWidth = '90%';

            if (role === 'user') {
                msgDiv.style.alignSelf = 'flex-end';
                msgDiv.style.background = '#4f46e5';
                msgDiv.style.color = '#ffffff';
                msgDiv.style.padding = '10px 14px';
                msgDiv.style.borderRadius = '14px 14px 2px 14px';
                msgDiv.textContent = text;
            } else {
                msgDiv.style.alignSelf = 'flex-start';
                msgDiv.style.background = 'rgba(255, 255, 255, 0.07)';
                msgDiv.style.border = '1px solid rgba(255, 255, 255, 0.1)';
                msgDiv.style.color = '#e2e8f0';
                msgDiv.style.padding = '12px 14px';
                msgDiv.style.borderRadius = '14px 14px 14px 2px';
                if (typeof marked !== 'undefined') {
                    msgDiv.innerHTML = marked.parse(text);
                } else {
                    msgDiv.textContent = text;
                }
            }

            container.appendChild(msgDiv);
            container.scrollTop = container.scrollHeight;
            return msgDiv;
        }

        function askModalQuickPrompt(promptText) {
            const drawer = document.getElementById('modalAiDrawer');
            drawer.style.transform = 'translateX(0)';
            document.getElementById('modalAiInputText').value = promptText;
            document.getElementById('modalAiChatForm').dispatchEvent(new Event('submit'));
        }

        async function handleSendModalAiMessage(e) {
            e.preventDefault();
            const input = document.getElementById('modalAiInputText');
            const submitBtn = document.getElementById('modalAiSubmitBtn');
            const question = input.value.trim();
            if (!question) return;

            appendModalAiMessage('user', question);
            input.value = '';
            submitBtn.disabled = true;

            const contextData = getModalSmartContext(question);

            const container = document.getElementById('modalAiMessagesContainer');
            const typingDiv = document.createElement('div');
            typingDiv.style.alignSelf = 'flex-start';
            typingDiv.style.background = 'rgba(255, 255, 255, 0.07)';
            typingDiv.style.padding = '10px 14px';
            typingDiv.style.borderRadius = '14px 14px 14px 2px';
            typingDiv.innerHTML = '<div style="display:flex; gap:4px;"><div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div></div>';
            container.appendChild(typingDiv);
            container.scrollTop = container.scrollHeight;

            const targetUrl = currentActiveModulId ? `/admin/e-modul/${currentActiveModulId}/ask-ai` : GENERAL_ASK_AI_URL;

            try {
                const response = await fetch(targetUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        question: question,
                        modul_title: currentActiveModulTitle,
                        page_number: contextData.label,
                        page_text: contextData.text,
                        history: modalChatHistory.slice(-6)
                    })
                });

                const data = await response.json();
                typingDiv.remove();

                if (data.success && data.reply) {
                    appendModalAiMessage('assistant', data.reply);
                    modalChatHistory.push({ role: 'user', content: question });
                    modalChatHistory.push({ role: 'assistant', content: data.reply });
                } else {
                    appendModalAiMessage('assistant', data.fallback_reply || data.message || 'Gagal memproses respons AI.');
                }
            } catch (err) {
                typingDiv.remove();
                appendModalAiMessage('assistant', '⚠️ Gagal menghubungi AI: ' + err.message);
            } finally {
                submitBtn.disabled = false;
            }
        }

        async function renderFlipbookModal(title, source, modulId = null) {
            const modal = document.getElementById('flipModal');
            const flipbookEl = document.getElementById('modalFlipbook');
            const thumbsDrawer = document.getElementById('modalThumbsDrawer');
            const loadingOverlay = document.getElementById('modalLoadingOverlay');
            const loadingText = document.getElementById('modalLoadingText');
            
            currentActiveModulTitle = title;
            currentActiveModulId = modulId;
            modalPageTexts = {};
            modalChatHistory = [];
            modalScope = 'active';

            modal.style.display = 'flex';
            document.getElementById('modalBookTitle').textContent = title;
            loadingOverlay.style.display = 'flex';
            loadingText.textContent = "Mengunduh dan menyiapkan Flipbook...";
            thumbsDrawer.style.display = 'none';
            thumbsDrawer.innerHTML = '';
            flipbookEl.innerHTML = '';
            flipbookEl.style.display = 'none';

            if (modalPageFlip) {
                try { modalPageFlip.destroy(); } catch(e) {}
                modalPageFlip = null;
            }

            try {
                let pdfDoc = null;
                
                if (typeof source === 'string') {
                    loadingText.textContent = "Mengunduh file PDF...";
                    const response = await fetch(source);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const pdfData = await response.arrayBuffer();
                    const loadingTask = pdfjsLib.getDocument({ data: pdfData });
                    pdfDoc = await loadingTask.promise;
                } else {
                    const loadingTask = pdfjsLib.getDocument(source);
                    pdfDoc = await loadingTask.promise;
                }

                modalTotalPages = pdfDoc.numPages;

                const firstPage = await pdfDoc.getPage(1);
                const viewport = firstPage.getViewport({ scale: 1.0 });
                const pageWidth = Math.min(520, window.innerWidth * 0.44);
                const pageHeight = pageWidth * (viewport.height / viewport.width);

                for (let i = 1; i <= modalTotalPages; i++) {
                    loadingText.textContent = `Merender halaman ${i} dari ${modalTotalPages}...`;
                    const page = await pdfDoc.getPage(i);
                    const scale = 2.0;
                    const pageViewport = page.getViewport({ scale: scale });

                    page.getTextContent().then(tc => {
                        modalPageTexts[i] = tc.items.map(item => item.str).join(' ');
                    }).catch(e => {});

                    const pageDiv = document.createElement('div');
                    pageDiv.className = `modal-page ${i % 2 === 0 ? '--left' : '--right'}`;
                    
                    const contentDiv = document.createElement('div');
                    contentDiv.className = 'modal-page-content';

                    const canvas = document.createElement('canvas');
                    canvas.width = pageViewport.width;
                    canvas.height = pageViewport.height;
                    const ctx = canvas.getContext('2d');

                    await page.render({ canvasContext: ctx, viewport: pageViewport }).promise;

                    contentDiv.appendChild(canvas);
                    pageDiv.appendChild(contentDiv);
                    flipbookEl.appendChild(pageDiv);

                    // Thumbnail item
                    const thumbItem = document.createElement('div');
                    thumbItem.className = `modal-thumb-item ${i === 1 ? 'active' : ''}`;
                    thumbItem.setAttribute('data-page', i);
                    
                    const thumbCanvas = document.createElement('canvas');
                    thumbCanvas.width = canvas.width;
                    thumbCanvas.height = canvas.height;
                    thumbCanvas.getContext('2d').drawImage(canvas, 0, 0);

                    const thumbLabel = document.createElement('div');
                    thumbLabel.className = 'modal-thumb-label';
                    thumbLabel.textContent = i;

                    thumbItem.appendChild(thumbCanvas);
                    thumbItem.appendChild(thumbLabel);

                    thumbItem.addEventListener('click', () => {
                        if (modalPageFlip) modalPageFlip.flip(i - 1);
                    });

                    thumbsDrawer.appendChild(thumbItem);
                }

                loadingOverlay.style.display = 'none';
                flipbookEl.style.display = 'block';

                if (typeof St !== 'undefined' && St.PageFlip) {
                    modalPageFlip = new St.PageFlip(flipbookEl, {
                        width: pageWidth,
                        height: pageHeight,
                        size: 'stretch',
                        minWidth: 300,
                        maxWidth: 750,
                        minHeight: 400,
                        maxHeight: 1050,
                        maxShadowOpacity: 0.5,
                        showCover: true,
                        mobileScrollSupport: false,
                        usePortrait: window.innerWidth < 768
                    });

                    modalPageFlip.loadFromHTML(flipbookEl.querySelectorAll('.modal-page'));

                    const slider = document.getElementById('modalPageSlider');
                    slider.max = modalTotalPages;
                    slider.value = 1;

                    updateModalControls(0);

                    modalPageFlip.on('flip', (e) => {
                        playFlipSound();
                        updateModalControls(e.data);
                    });
                }

            } catch (err) {
                console.error("Flipbook error:", err);
                loadingText.innerHTML = `Gagal memuat dokumen PDF.<br><span style="font-size:12px; color:#f87171;">${err.message || ''}</span><br><br><a href="${document.getElementById('modalStandaloneLink').href}" target="_blank" style="color:#818cf8; text-decoration:underline;">Buka di Tab Baru</a>`;
            }
        }

        function updateModalControls(pageIndex) {
            const pageNum = pageIndex + 1;
            const slider = document.getElementById('modalPageSlider');
            const indicator = document.getElementById('modalPageIndicator');
            const activePageLabel = getModalActivePageLabel();
            
            slider.value = pageNum;

            if (modalPageFlip && modalPageFlip.getOrientation() === 'landscape' && pageNum > 1 && pageNum < modalTotalPages) {
                indicator.textContent = `${pageNum}-${pageNum + 1} / ${modalTotalPages}`;
            } else {
                indicator.textContent = `${pageNum} / ${modalTotalPages}`;
            }

            document.getElementById('modalAiActivePageBadge').textContent = activePageLabel;

            document.querySelectorAll('.modal-thumb-item').forEach(th => {
                const p = parseInt(th.getAttribute('data-page'), 10);
                th.classList.toggle('active', p === pageNum || (pageNum > 1 && p === pageNum + 1));
            });

            document.getElementById('modalPrevArrow').style.opacity = (pageIndex === 0) ? '0.3' : '1';
            document.getElementById('modalNextArrow').style.opacity = (pageIndex >= modalTotalPages - 1) ? '0.3' : '1';
        }

        function closeFlipModal() {
            document.getElementById('flipModal').style.display = 'none';
            document.getElementById('modalAiDrawer').style.transform = 'translateX(100%)';
            if (modalPageFlip) {
                try { modalPageFlip.destroy(); } catch(e) {}
                modalPageFlip = null;
            }
        }

        function toggleModalFullscreen() {
            const modal = document.getElementById('flipModal');
            if (!document.fullscreenElement) {
                modal.requestFullscreen().catch(err => alert(err.message));
            } else {
                document.exitFullscreen();
            }
        }

        // Modal Controls Event Listeners
        document.getElementById('modalPrevArrow').addEventListener('click', () => modalPageFlip && modalPageFlip.flipPrev());
        document.getElementById('modalNextArrow').addEventListener('click', () => modalPageFlip && modalPageFlip.flipNext());
        document.getElementById('modalBtnFirst').addEventListener('click', () => modalPageFlip && modalPageFlip.flip(0));
        document.getElementById('modalBtnLast').addEventListener('click', () => modalPageFlip && modalPageFlip.flip(modalTotalPages - 1));

        document.getElementById('modalPageSlider').addEventListener('input', (e) => {
            const targetPage = parseInt(e.target.value, 10);
            if (modalPageFlip) modalPageFlip.flip(targetPage - 1);
        });

        document.getElementById('modalBtnZoomIn').addEventListener('click', () => {
            modalZoom = Math.min(modalZoom + 0.15, 1.5);
            document.getElementById('modalFlipWrapper').style.transform = `scale(${modalZoom})`;
        });

        document.getElementById('modalBtnZoomOut').addEventListener('click', () => {
            modalZoom = Math.max(modalZoom - 0.15, 0.7);
            document.getElementById('modalFlipWrapper').style.transform = `scale(${modalZoom})`;
        });

        document.getElementById('modalBtnToggleThumbs').addEventListener('click', function() {
            const drawer = document.getElementById('modalThumbsDrawer');
            drawer.style.display = (drawer.style.display === 'flex') ? 'none' : 'flex';
        });

        document.getElementById('modalBtnToggleSound').addEventListener('click', function() {
            modalSoundEnabled = !modalSoundEnabled;
            this.style.color = modalSoundEnabled ? '#818cf8' : '#94a3b8';
            this.style.background = modalSoundEnabled ? 'rgba(99,102,241,0.2)' : 'none';
        });

        // Key bindings for modal
        window.addEventListener('keydown', (e) => {
            if (document.getElementById('flipModal').style.display === 'flex') {
                if (e.key === 'ArrowRight' || e.key === 'PageDown') {
                    modalPageFlip && modalPageFlip.flipNext();
                } else if (e.key === 'ArrowLeft' || e.key === 'PageUp') {
                    modalPageFlip && modalPageFlip.flipPrev();
                } else if (e.key === 'Escape') {
                    closeFlipModal();
                }
            }
        });

        // Copy Public Share Link (ekscoder.com/modul/{slug})
        function copyModulShareLink(url, btn) {
            navigator.clipboard.writeText(url).then(() => {
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#b8ff00" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>';
                btn.title = 'Tautan Berhasil Disalin!';
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                    btn.title = 'Salin Link Publik (ekscoder.com)';
                }, 2000);
            }).catch(() => {
                prompt('Salin link e-modul:', url);
            });
        }


    </script>
</x-admin-layout>
