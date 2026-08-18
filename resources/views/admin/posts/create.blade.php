<x-admin-layout title="Buat Artikel Baru" breadcrumb="Tambahkan artikel baru untuk blog">


    <!-- Marked.js for Live Formatted Visual Preview -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <style>
        /* Visual Rich Editor Formatting - Adaptive Light/Dark */
        #visualEditorBox {
            color: var(--text-primary);
        }
        #visualEditorBox h1, #visualEditorBox h2, #visualEditorBox h3, #visualEditorBox h4 {
            color: var(--text-primary);
            font-weight: 700;
            margin-top: 1.5em;
            margin-bottom: 0.6em;
            line-height: 1.3;
        }
        #visualEditorBox h1 { font-size: 24px; }
        #visualEditorBox h2 { font-size: 20px; border-bottom: 2px solid var(--border); padding-bottom: 8px; }
        #visualEditorBox h3 { font-size: 17px; color: #059669; }
        #visualEditorBox p { margin-bottom: 1.1em; line-height: 1.7; color: var(--text-primary); }
        #visualEditorBox pre {
            background: #1e1e2e !important;
            border: 1px solid rgba(0, 0, 0, 0.15);
            border-radius: 10px;
            padding: 16px 20px;
            margin: 1.2em 0;
            overflow-x: auto;
        }
        #visualEditorBox code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            color: #059669;
            background: rgba(5, 150, 105, 0.1);
            padding: 3px 8px;
            border-radius: 6px;
            border: 1px solid rgba(5, 150, 105, 0.2);
        }
        #visualEditorBox pre code {
            background: transparent !important;
            border: none !important;
            padding: 0;
            color: #86efac;
            display: block;
            line-height: 1.5;
        }
        #visualEditorBox ul, #visualEditorBox ol {
            padding-left: 24px;
            margin-bottom: 1.1em;
        }
        #visualEditorBox li { margin-bottom: 0.5em; color: var(--text-primary); }
        #visualEditorBox strong { color: var(--text-primary); font-weight: 700; }
        #visualEditorBox blockquote {
            border-left: 4px solid #059669;
            padding: 10px 16px;
            margin: 1.2em 0;
            background: rgba(5, 150, 105, 0.06);
            border-radius: 0 8px 8px 0;
            color: var(--text-secondary);
            font-style: italic;
        }
        /* Formatting Toolbar */
        .editor-toolbar {
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 8px 12px;
            background: var(--bg-elevated);
            border: 1px solid var(--border);
            border-bottom: none;
            border-radius: 10px 10px 0 0;
            flex-wrap: wrap;
        }
        .toolbar-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border: 1px solid transparent;
            border-radius: 6px;
            background: transparent;
            color: var(--text-secondary);
            cursor: pointer;
            font-size: 13px;
            font-weight: 700;
            transition: all 0.15s ease;
            font-family: 'Inter', sans-serif;
        }
        .toolbar-btn:hover {
            background: var(--bg-surface);
            border-color: var(--border);
            color: var(--text-primary);
        }
        .toolbar-btn.active {
            background: rgba(16, 185, 129, 0.12);
            border-color: #10b981;
            color: #059669;
        }
        .toolbar-divider {
            width: 1px;
            height: 22px;
            background: var(--border);
            margin: 0 4px;
            flex-shrink: 0;
        }
        .toolbar-select {
            padding: 4px 8px;
            font-size: 12px;
            font-weight: 600;
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: 6px;
            color: var(--text-primary);
            cursor: pointer;
            height: 30px;
        }
        #visualEditorBox {
            border-radius: 0 0 10px 10px !important;
        }

        /* AI Loading Modal Keyframe Animations */
        @keyframes aiSpin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @keyframes aiSpinReverse {
            0% { transform: rotate(360deg); }
            100% { transform: rotate(0deg); }
        }
        @keyframes aiProgress {
            0% { left: -100%; width: 40%; }
            50% { left: 30%; width: 60%; }
            100% { left: 100%; width: 40%; }
        }
        @keyframes aiModalPulse {
            0% { box-shadow: 0 25px 60px rgba(0,0,0,0.8), 0 0 30px rgba(16, 185, 129, 0.15); }
            100% { box-shadow: 0 25px 60px rgba(0,0,0,0.8), 0 0 50px rgba(16, 185, 129, 0.35); }
        }
    </style>

    <!-- AI Loading Modal Overlay -->
    <div id="aiLoadingModal" style="position:fixed; inset:0; background:rgba(10, 10, 16, 0.82); backdrop-filter:blur(10px); display:flex; align-items:center; justify-content:center; z-index:99999; opacity:0; pointer-events:none; transition:all 0.3s ease;">
        <div style="background:#161622; border:1px solid rgba(16, 185, 129, 0.3); border-radius:24px; padding:40px 36px; text-align:center; max-width:440px; width:90%; box-shadow:0 25px 60px rgba(0,0,0,0.8), 0 0 40px rgba(16, 185, 129, 0.2); animation:aiModalPulse 3s ease-in-out infinite alternate;">
            
            <!-- Futuristic Spinner Icon -->
            <div style="position:relative; width:80px; height:80px; margin:0 auto 24px auto;">
                <div style="position:absolute; inset:0; border-radius:50%; border:3.5px solid transparent; border-top-color:#10b981; border-right-color:#34d399; animation:aiSpin 1.2s linear infinite;"></div>
                <div style="position:absolute; inset:8px; border-radius:50%; border:3.5px solid transparent; border-bottom-color:#38bdf8; animation:aiSpinReverse 0.9s linear infinite;"></div>
                <div style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center; color:#10b981;">
                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                </div>
            </div>

            <h3 style="margin:0 0 10px 0; font-size:20px; font-weight:800; color:#ffffff; letter-spacing:-0.02em;">
                ✨ AI Agent Sedang Bekerja...
            </h3>
            <p id="aiModalStatusText" style="margin:0 0 22px 0; font-size:13.5px; color:#94a3b8; line-height:1.6;">
                Menganalisis topik, menyusun struktur artikel, dan membuatkan Meta SEO...
            </p>

            <!-- Loading Progress Bar Animation -->
            <div style="width:100%; height:6px; background:rgba(255,255,255,0.08); border-radius:10px; overflow:hidden; position:relative;">
                <div id="aiProgressBar" style="position:absolute; top:0; left:-100%; width:100%; height:100%; background:linear-gradient(90deg, #10b981 0%, #38bdf8 100%); border-radius:10px; animation:aiProgress 2.5s ease-in-out infinite;"></div>
            </div>
            
            <span style="display:block; margin-top:16px; font-size:11px; font-weight:700; color:#10b981; text-transform:uppercase; letter-spacing:0.08em;">Mohon Tunggu Sebentar</span>
        </div>
    </div>

    <div style="margin-bottom:18px;">
        <a href="{{ route('admin.posts.index') }}" class="topbar-btn topbar-btn-ghost" style="display:inline-flex; align-items:center; gap:8px; padding:8px 16px; border-radius:10px; text-decoration:none; font-weight:600; font-size:13px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><polyline points="12 19 5 12 12 5"/></svg>
            Kembali ke Daftar Artikel
        </a>
    </div>

    <div class="card" style="max-width:100%; margin:0; padding:24px;">
        <form method="POST" action="{{ route('admin.posts.store') }}" enctype="multipart/form-data">
            @csrf

            <div style="display:flex; flex-direction:column; gap:18px;">
                <!-- Main Form Body -->
                <div style="display:flex; flex-direction:column; gap:18px;">
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:var(--text-secondary); text-transform:uppercase; margin-bottom:8px;">Judul Artikel *</label>
                        <input type="text" name="title" id="postTitle" value="{{ old('title') }}" required placeholder="Contoh: Panduan Lengkap Deploy Laravel ke VPS" style="width:100%; padding:10px 14px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:10px; color:var(--text-primary); font-size:14px;">
                        @error('title') <span style="color:#ef4444; font-size:12px; margin-top:4px; display:block;">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:var(--text-secondary); text-transform:uppercase; margin-bottom:8px;">Slug (URL Custom)</label>
                        <input type="text" name="slug" id="postSlug" value="{{ old('slug') }}" placeholder="Otomatis dibuat dari judul jika dikosongkan" style="width:100%; padding:10px 14px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:10px; color:var(--text-primary); font-size:13px; font-family:'JetBrains Mono', monospace;">
                        @error('slug') <span style="color:#ef4444; font-size:12px; margin-top:4px; display:block;">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:var(--text-secondary); text-transform:uppercase; margin-bottom:8px;">Ringkasan / Excerpt</label>
                        <textarea name="excerpt" rows="3" placeholder="Ringkasan singkat artikel yang akan muncul di daftar blog..." style="width:100%; padding:10px 14px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:10px; color:var(--text-primary); font-size:13.5px;">{{ old('excerpt') }}</textarea>
                    </div>

                    <div>
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px; flex-wrap:wrap; gap:8px;">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <label style="display:block; font-size:12px; font-weight:700; color:var(--text-secondary); text-transform:uppercase;">Konten Artikel *</label>
                                
                                <div style="display:inline-flex; background:var(--bg-elevated); border:1px solid var(--border); border-radius:8px; padding:2px;">
                                    <button type="button" id="tabVisual" onclick="switchEditorTab('visual')" style="padding:4px 10px; font-size:11px; font-weight:700; border:none; border-radius:6px; background:#10b981; color:#ffffff; cursor:pointer; transition:all 0.2s;">
                                        👁️ Tampilan Visual (Hasil Akhir)
                                    </button>
                                    <button type="button" id="tabCode" onclick="switchEditorTab('code')" style="padding:4px 10px; font-size:11px; font-weight:600; border:none; border-radius:6px; background:transparent; color:var(--text-muted); cursor:pointer; transition:all 0.2s;">
                                        📝 Kode HTML
                                    </button>
                                </div>
                            </div>
                            
                            <!-- AI Article Generator Button -->
                            <button type="button" id="btnGenerateAi" onclick="generateAiArticleContent()" style="display:inline-flex; align-items:center; gap:6px; padding:6px 14px; background:linear-gradient(135deg, #10b981 0%, #059669 100%); color:#ffffff; border:none; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer; transition:all 0.2s ease; box-shadow:0 4px 12px rgba(16, 185, 129, 0.25);">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                                <span id="btnGenerateAiText">Generate Artikel dengan AI</span>
                            </button>
                        </div>

                        <!-- Formatting Toolbar -->
                        <div class="editor-toolbar" id="editorToolbar">
                            <!-- Heading Selector -->
                            <select class="toolbar-select" onchange="applyHeading(this.value); this.value=''" title="Heading">
                                <option value="" disabled selected>Heading</option>
                                <option value="H1">H1 - Judul Besar</option>
                                <option value="H2">H2 - Sub Judul</option>
                                <option value="H3">H3 - Sub-Sub Judul</option>
                                <option value="P">Paragraf Normal</option>
                            </select>

                            <div class="toolbar-divider"></div>

                            <!-- Text Formatting -->
                            <button type="button" class="toolbar-btn" onclick="execFmt('bold')" title="Bold (Ctrl+B)"><b>B</b></button>
                            <button type="button" class="toolbar-btn" onclick="execFmt('italic')" title="Italic (Ctrl+I)"><i>I</i></button>
                            <button type="button" class="toolbar-btn" onclick="execFmt('underline')" title="Underline (Ctrl+U)" style="text-decoration:underline;">U</button>
                            <button type="button" class="toolbar-btn" onclick="execFmt('strikeThrough')" title="Strikethrough" style="text-decoration:line-through;">S</button>

                            <div class="toolbar-divider"></div>

                            <!-- Lists -->
                            <button type="button" class="toolbar-btn" onclick="execFmt('insertUnorderedList')" title="Bullet List">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="9" y1="6" x2="20" y2="6"/><line x1="9" y1="12" x2="20" y2="12"/><line x1="9" y1="18" x2="20" y2="18"/><circle cx="4" cy="6" r="1.5" fill="currentColor"/><circle cx="4" cy="12" r="1.5" fill="currentColor"/><circle cx="4" cy="18" r="1.5" fill="currentColor"/></svg>
                            </button>
                            <button type="button" class="toolbar-btn" onclick="execFmt('insertOrderedList')" title="Numbered List">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="10" y1="6" x2="21" y2="6"/><line x1="10" y1="12" x2="21" y2="12"/><line x1="10" y1="18" x2="21" y2="18"/><path d="M4 6h1v4" stroke="currentColor" stroke-width="1.8"/><path d="M4 10h2" stroke="currentColor" stroke-width="1.8"/><path d="M6 18H4c0-1 2-2 2-3s-1-1.5-2-1.5" stroke="currentColor" stroke-width="1.8"/></svg>
                            </button>

                            <div class="toolbar-divider"></div>

                            <!-- Alignment -->
                            <button type="button" class="toolbar-btn" onclick="execFmt('justifyLeft')" title="Align Left">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="15" y2="12"/><line x1="3" y1="18" x2="18" y2="18"/></svg>
                            </button>
                            <button type="button" class="toolbar-btn" onclick="execFmt('justifyCenter')" title="Align Center">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="6" y1="12" x2="18" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/></svg>
                            </button>

                            <div class="toolbar-divider"></div>

                            <!-- Block Formatting -->
                            <button type="button" class="toolbar-btn" onclick="execFmt('formatBlock', 'blockquote')" title="Blockquote">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"/><path d="M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v3c0 1 0 1 1 1z"/></svg>
                            </button>
                            <button type="button" class="toolbar-btn" onclick="insertCodeBlock()" title="Code Block / Bash Terminal" style="font-family:'JetBrains Mono',monospace; font-size:11px; font-weight:800; width:auto; padding:0 8px;">&gt;_</button>
                            <button type="button" class="toolbar-btn" onclick="insertHorizontalRule()" title="Garis Pemisah">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="3" y1="12" x2="21" y2="12"/></svg>
                            </button>
                            <button type="button" class="toolbar-btn" onclick="insertLink()" title="Tambah Link">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                            </button>

                            <div class="toolbar-divider"></div>

                            <!-- Undo / Redo -->
                            <button type="button" class="toolbar-btn" onclick="execFmt('undo')" title="Undo (Ctrl+Z)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M9 14 4 9l5-5"/><path d="M4 9h10.5a5.5 5.5 0 0 1 0 11H11"/></svg>
                            </button>
                            <button type="button" class="toolbar-btn" onclick="execFmt('redo')" title="Redo (Ctrl+Y)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m15 14 5-5-5-5"/><path d="M20 9H9.5a5.5 5.5 0 0 0 0 11H13"/></svg>
                            </button>
                        </div>

                        <!-- 1. Visual Rendered Editor Box -->
                        <div id="visualEditorBox" contenteditable="true" oninput="syncVisualToTextarea()" style="min-height:350px; max-height:600px; overflow-y:auto; padding:16px 20px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:10px; color:var(--text-primary); font-size:14.5px; line-height:1.7; font-family:'Inter', sans-serif;">
                            {!! old('content') ? old('content') : '<p style="color:var(--text-muted); font-style:italic;">Gunakan AI Generator di atas atau tuliskan artikel Anda. Tampilan akhir visual artikel Anda akan otomatis langsung dirender di sini tanpa kode/simbol mentah...</p>' !!}
                        </div>

                        <!-- 2. Form Textarea for POST submission -->
                        <textarea name="content" id="postContent" rows="14" style="display:none; width:100%; padding:12px 14px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:10px; color:var(--text-primary); font-size:13.5px; font-family:'JetBrains Mono', monospace; line-height:1.6;">{{ old('content') }}</textarea>

                        @error('content') <span style="color:#ef4444; font-size:12px; margin-top:4px; display:block;">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Sidebar Settings -->
                <div style="display:flex; flex-direction:column; gap:18px; background:var(--bg-surface); padding:20px; border-radius:16px; border:1px solid var(--border);">
                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:var(--text-secondary); text-transform:uppercase; margin-bottom:8px;">Status *</label>
                        <select name="status" style="width:100%; padding:10px 14px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:10px; color:var(--text-primary); font-size:13.5px;">
                            <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>✏️ Draft (Internal)</option>
                            <option value="published" {{ old('status', 'published') === 'published' ? 'selected' : '' }}>⚡ Published (Public API)</option>
                        </select>
                    </div>

                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:var(--text-secondary); text-transform:uppercase; margin-bottom:8px;">Kategori</label>
                        <select name="category_id" style="width:100%; padding:10px 14px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:10px; color:var(--text-primary); font-size:13.5px;">
                            <option value="">-- Tanpa Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:var(--text-secondary); text-transform:uppercase; margin-bottom:8px;">Cover Image (Foto / Banner)</label>
                        
                        <!-- Upload File Foto -->
                        <div style="margin-bottom:10px;">
                            <label style="display:block; font-size:11px; color:var(--text-muted); margin-bottom:4px;">📷 Upload Foto File (PNG, JPG, WEBP - Max 5MB):</label>
                            <input type="file" name="cover_file" accept="image/*" onchange="previewCreateCover(this)" style="width:100%; padding:8px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:10px; color:var(--text-primary); font-size:12px;">
                            @error('cover_file') <span style="color:#ef4444; font-size:11px; margin-top:2px; display:block;">{{ $message }}</span> @enderror
                        </div>

                        <!-- Image Preview -->
                        <div id="coverPreviewBox" style="margin-bottom:10px; display:none;">
                            <img id="coverPreviewImg" src="" alt="Cover Preview" style="max-height:120px; width:100%; object-fit:cover; border-radius:10px; border:1px solid var(--border);">
                        </div>

                        <!-- URL External Alternative -->
                        <div>
                            <label style="display:block; font-size:11px; color:var(--text-muted); margin-bottom:4px;">Atau Tempel URL Gambar External:</label>
                            <input type="text" name="cover_image" value="{{ old('cover_image') }}" placeholder="https://images.unsplash.com/photo-..." style="width:100%; padding:9px 12px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:10px; color:var(--text-primary); font-size:12.5px;">
                        </div>
                    </div>

                    <div style="padding-top:8px;">
                        <label style="display:inline-flex; align-items:center; gap:8px; cursor:pointer;">
                            <input type="checkbox" name="featured" value="1" {{ old('featured') ? 'checked' : '' }} style="accent-color:#b8ff00; width:16px; height:16px;">
                            <span style="font-size:13px; font-weight:600; color:var(--text-primary);">⭐ Tandai sebagai Featured</span>
                        </label>
                    </div>

                    <div style="border-top:1px solid var(--border); padding-top:16px; margin-top:8px;">
                        <span style="display:block; font-size:12px; font-weight:700; color:#b8ff00; text-transform:uppercase; margin-bottom:12px;">Meta SEO (Opsional)</span>
                        
                        <div style="margin-bottom:12px;">
                            <label style="display:block; font-size:11px; color:var(--text-secondary); margin-bottom:4px;">Meta Title</label>
                            <input type="text" name="meta_title" id="metaTitle" value="{{ old('meta_title') }}" placeholder="Judul untuk Google Search" style="width:100%; padding:8px 12px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:8px; color:var(--text-primary); font-size:12.5px;">
                        </div>

                        <div>
                            <label style="display:block; font-size:11px; color:var(--text-secondary); margin-bottom:4px;">Meta Keywords</label>
                            <input type="text" name="meta_keywords" id="metaKeywords" value="{{ old('meta_keywords') }}" placeholder="laravel, php, vps" style="width:100%; padding:8px 12px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:8px; color:var(--text-primary); font-size:12.5px;">
                        </div>
                    </div>

                    <div style="margin-top:16px;">
                        <button type="submit" class="topbar-btn topbar-btn-primary" style="width:100%; padding:12px; font-size:14px; font-weight:800; justify-content:center;">
                            Simpan Artikel →
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Toast Notification Banner -->
    <div id="aiToastNotification" style="position:fixed; top:24px; right:24px; z-index:999999; background:#10b981; color:#ffffff; padding:12px 20px; border-radius:12px; font-weight:700; font-size:13.5px; display:flex; align-items:center; gap:10px; box-shadow:0 10px 30px rgba(16, 185, 129, 0.35); opacity:0; transform:translateY(-20px); pointer-events:none; transition:all 0.3s cubic-bezier(0.16, 1, 0.3, 1);">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
        <span id="aiToastMessage">✨ Artikel dan Meta SEO berhasil digenerate oleh AI!</span>
    </div>

    <script>
        function showToast(message, type = 'success') {
            const toast = document.getElementById('aiToastNotification');
            const msgEl = document.getElementById('aiToastMessage');
            if (toast && msgEl) {
                msgEl.textContent = message;
                toast.style.background = type === 'error' ? '#ef4444' : '#10b981';
                toast.style.boxShadow = type === 'error' ? '0 10px 30px rgba(239, 68, 68, 0.35)' : '0 10px 30px rgba(16, 185, 129, 0.35)';
                toast.style.opacity = '1';
                toast.style.transform = 'translateY(0)';

                setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(-20px)';
                }, 3500);
            }
        }

        /* ===== RICH TEXT TOOLBAR FUNCTIONS ===== */
        function execFmt(command, value = null) {
            document.getElementById('visualEditorBox').focus();
            document.execCommand(command, false, value);
            syncVisualToTextarea();
        }

        function applyHeading(tag) {
            document.getElementById('visualEditorBox').focus();
            document.execCommand('formatBlock', false, tag);
            syncVisualToTextarea();
        }

        function insertLink() {
            const url = prompt('Masukkan URL tautan:', 'https://');
            if (url && url !== 'https://') {
                document.getElementById('visualEditorBox').focus();
                const selected = window.getSelection().toString();
                const text = selected || url;
                document.execCommand('insertHTML', false, `<a href="${url}" target="_blank" style="color:#059669; text-decoration:underline;">${text}</a>`);
                syncVisualToTextarea();
            }
        }

        function insertHorizontalRule() {
            document.getElementById('visualEditorBox').focus();
            document.execCommand('insertHTML', false, '<hr style="border:none; border-top:2px solid var(--border); margin:1.5em 0;"><p></p>');
            syncVisualToTextarea();
        }

        function insertCodeBlock() {
            document.getElementById('visualEditorBox').focus();
            const html = `<pre style="background:#1a1a2e; border-radius:10px; padding:16px 20px; margin:1.2em 0; overflow-x:auto; font-family:'JetBrains Mono',monospace; font-size:13px; color:#86efac; line-height:1.6;"><code contenteditable="true" spellcheck="false">// Tulis kode Anda di sini</code></pre><p></p>`;
            document.execCommand('insertHTML', false, html);
            syncVisualToTextarea();
        }

        /* Update active toolbar button state */
        document.addEventListener('selectionchange', () => {
            const commands = ['bold', 'italic', 'underline', 'strikeThrough'];
            commands.forEach(cmd => {
                const btn = document.querySelector(`[onclick="execFmt('${cmd}')"]`);
                if (btn) {
                    btn.classList.toggle('active', document.queryCommandState(cmd));
                }
            });
        });

        /* Show/hide toolbar based on active editor tab */
        function updateToolbarVisibility(tab) {
            const toolbar = document.getElementById('editorToolbar');
            if (toolbar) {
                toolbar.style.display = tab === 'visual' ? 'flex' : 'none';
            }
        }

        function showAiLoadingModal() {
            const modal = document.getElementById('aiLoadingModal');
            if (modal) {
                modal.style.opacity = '1';
                modal.style.pointerEvents = 'auto';
            }
        }

        function hideAiLoadingModal() {
            const modal = document.getElementById('aiLoadingModal');
            if (modal) {
                modal.style.opacity = '0';
                modal.style.pointerEvents = 'none';
            }
        }

        function renderVisualPreview() {
            const visualBox = document.getElementById('visualEditorBox');
            const textarea = document.getElementById('postContent');
            if (visualBox && textarea) {
                const rawMarkdown = textarea.value;
                if (typeof marked !== 'undefined' && rawMarkdown) {
                    visualBox.innerHTML = marked.parse(rawMarkdown);
                } else if (rawMarkdown) {
                    visualBox.innerHTML = rawMarkdown;
                }
            }
        }

        function syncVisualToTextarea() {
            const visualBox = document.getElementById('visualEditorBox');
            const textarea = document.getElementById('postContent');
            if (visualBox && textarea) {
                textarea.value = visualBox.innerHTML;
            }
        }

        function switchEditorTab(tab) {
            const visualBox = document.getElementById('visualEditorBox');
            const textarea = document.getElementById('postContent');
            const tabVisual = document.getElementById('tabVisual');
            const tabCode = document.getElementById('tabCode');

            if (tab === 'visual') {
                renderVisualPreview();
                visualBox.style.display = 'block';
                textarea.style.display = 'none';
                tabVisual.style.background = '#10b981';
                tabVisual.style.color = '#ffffff';
                tabVisual.style.fontWeight = '700';
                tabCode.style.background = 'transparent';
                tabCode.style.color = 'var(--text-muted)';
                tabCode.style.fontWeight = '600';
            } else {
                visualBox.style.display = 'none';
                textarea.style.display = 'block';
                tabCode.style.background = '#10b981';
                tabCode.style.color = '#ffffff';
                tabCode.style.fontWeight = '700';
                tabVisual.style.background = 'transparent';
                tabVisual.style.color = 'var(--text-muted)';
                tabVisual.style.fontWeight = '600';
            }

            updateToolbarVisibility(tab);
        }

        function previewCreateCover(input) {
            const preview = document.getElementById('coverPreviewImg');
            const box = document.getElementById('coverPreviewBox');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    box.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        async function generateAiArticleContent() {
            const titleInput = document.getElementById('postTitle');
            const excerptInput = document.querySelector('textarea[name="excerpt"]');
            const contentTextarea = document.getElementById('postContent');
            const btn = document.getElementById('btnGenerateAi');
            const btnText = document.getElementById('btnGenerateAiText');

            const title = titleInput ? titleInput.value.trim() : '';
            const excerpt = excerptInput ? excerptInput.value.trim() : '';

            if (!title) {
                showToast('Silakan isi "Judul Artikel" terlebih dahulu sebelum melakukan Generate AI.', 'error');
                if (titleInput) titleInput.focus();
                return;
            }

            btn.disabled = true;
            btn.style.opacity = '0.75';
            btn.style.cursor = 'not-allowed';
            btnText.textContent = '⚡ AI Sedang Menulis Artikel & Meta SEO...';

            showAiLoadingModal();

            try {
                const response = await fetch("{{ route('admin.posts.generate-ai') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'text/event-stream'
                    },
                    body: JSON.stringify({
                        title: title,
                        excerpt: excerpt
                    })
                });

                if (!response.ok) {
                    const errText = await response.text();
                    throw new Error(`Server status ${response.status}: ${errText}`);
                }

                const reader = response.body.getReader();
                const decoder = new TextDecoder('utf-8');
                let fullText = '';
                let buffer = '';
                let streamError = null;

                while (true) {
                    const { done, value } = await reader.read();
                    if (done) break;

                    buffer += decoder.decode(value, { stream: true });
                    const lines = buffer.split('\n');
                    buffer = lines.pop();

                    for (const line of lines) {
                        const trimmed = line.trim();
                        if (trimmed.startsWith('data:')) {
                            const dataStr = trimmed.substring(5).trim();
                            if (dataStr === '[DONE]') {
                                break;
                            }
                            try {
                                const parsed = JSON.parse(dataStr);
                                if (parsed.error) {
                                    streamError = parsed.error;
                                } else if (parsed.chunk) {
                                    fullText += parsed.chunk;
                                }
                            } catch (e) {}
                        }
                    }
                }

                if (streamError) {
                    throw new Error(streamError);
                }

                if (!fullText.trim()) {
                    throw new Error('Respon dari AI Gateway kosong.');
                }

                // Clean markdown codeblocks if AI wraps JSON in ```json
                let cleanJson = fullText.trim().replace(/^```(?:json)?\s*|\s*```$/i, '').trim();
                let rawContent = '';
                let metaTitle = '';
                let metaKeywords = '';

                try {
                    const parsedData = JSON.parse(cleanJson);
                    if (parsedData && typeof parsedData === 'object' && parsedData.content) {
                        rawContent = parsedData.content;
                        metaTitle = parsedData.meta_title || '';
                        metaKeywords = parsedData.meta_keywords || '';
                    } else {
                        rawContent = fullText;
                    }
                } catch (e) {
                    rawContent = fullText;
                }

                contentTextarea.value = rawContent.trim();

                renderVisualPreview();
                switchEditorTab('visual');

                const metaTitleInput = document.getElementById('metaTitle') || document.querySelector('input[name="meta_title"]');
                const metaKeywordsInput = document.getElementById('metaKeywords') || document.querySelector('input[name="meta_keywords"]');

                if (metaTitleInput && metaTitle) metaTitleInput.value = metaTitle;
                if (metaKeywordsInput && metaKeywords) metaKeywordsInput.value = metaKeywords;

                showToast('✨ Artikel dan Meta SEO berhasil digenerate oleh AI!', 'success');

            } catch (err) {
                console.error('generateAiArticle error:', err);
                showToast('Gagal me-generate artikel: ' + (err.message || 'Terjadi kesalahan pada AI Gateway.'), 'error');
            } finally {
                hideAiLoadingModal();
                btn.disabled = false;
                btn.style.opacity = '1';
                btn.style.cursor = 'pointer';
                btnText.textContent = 'Generate Artikel dengan AI';
            }
        }

        // On DOM Content Loaded
        document.addEventListener('DOMContentLoaded', () => {
            renderVisualPreview();
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', () => {
                    syncVisualToTextarea();
                });
            }
        });
    </script>
</x-admin-layout>
