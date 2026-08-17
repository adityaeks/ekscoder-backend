<x-admin-layout title="Catatan & Quick Notes" breadcrumb="Manajemen Catatan">
    <div style="max-width: 1080px; margin: 0 auto; padding-bottom: 40px;">

        <!-- Header Bar & Search -->
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-bottom: 28px;">
            <div>
                <h1 style="font-size:22px; font-weight:800; color:var(--text-primary); letter-spacing:-0.5px; display:flex; align-items:center; gap:10px; margin:0;">
                    <span style="display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; border-radius:10px; background:rgba(245, 158, 11, 0.15); color:#fbbf24;">
                        📝
                    </span>
                    Catatan
                </h1>
                <p style="font-size:13px; color:var(--text-muted); margin:4px 0 0;">Catat ide, tugas, atau ringkasan penting dengan mudah & cepat.</p>
            </div>

            <!-- Search Input -->
            <form action="{{ route('admin.notes.index') }}" method="GET" style="display:flex; gap:10px; align-items:center; width:100%; max-width:340px;">
                <div style="position:relative; width:100%;">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari catatan..." style="width:100%; padding:9px 14px 9px 36px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:10px; color:var(--text-primary); font-size:13.5px; outline:none;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted);"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </div>
                @if(request('search'))
                    <a href="{{ route('admin.notes.index') }}" class="btn btn-ghost" style="padding:8px 12px; font-size:12px;">Reset</a>
                @endif
            </form>
        </div>

        <!-- Take a Note Input Box (Google Keep Style) -->
        <div class="card" id="noteInputCard" style="max-width: 580px; margin: 0 auto 36px; padding: 14px 18px; border-radius: 16px; border: 1px solid var(--border); background: var(--bg-surface); box-shadow: 0 10px 30px rgba(0,0,0,0.3); transition: all 0.25s ease;">
            <form action="{{ route('admin.notes.store') }}" method="POST" id="createNoteForm">
                @csrf
                <input type="hidden" name="color" id="noteColorInput" value="default">
                <input type="hidden" name="is_pinned" id="notePinInput" value="0">

                <!-- Collapsed Placeholder -->
                <div id="noteCollapsedView" style="display: flex; justify-content: space-between; align-items: center; cursor: text;" onclick="expandNoteBox()">
                    <span style="color: var(--text-muted); font-size: 14px; font-weight: 500;">Buat catatan...</span>
                    <div style="display: flex; gap: 8px; color: var(--text-muted);">
                        <span title="Pin Catatan" style="cursor:pointer;">📌</span>
                        <span title="Pilih Warna" style="cursor:pointer;">🎨</span>
                    </div>
                </div>

                <!-- Expanded Input Form -->
                <div id="noteExpandedView" style="display: none;">
                    <!-- Title & Pin Button -->
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                        <input type="text" name="title" placeholder="Judul" style="width: 100%; background: transparent; border: none; outline: none; font-size: 16px; font-weight: 700; color: var(--text-primary); padding: 4px 0;">
                        <button type="button" id="pinToggleBtn" onclick="togglePinForm()" style="background: none; border: none; cursor: pointer; padding: 4px; font-size: 18px; opacity: 0.4; filter: grayscale(1); transition: all 0.2s;" title="Sematkan catatan">
                            📌
                        </button>

                    </div>

                    <!-- Formatting Toolbar -->
                    <div style="display: flex; gap: 4px; margin-bottom: 8px; flex-wrap: wrap; background: rgba(255,255,255,0.04); padding: 4px 6px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.06);">
                        <button type="button" id="createFmt_bold" onclick="execFmt('bold', 'create')" class="fmt-tool-btn" title="Tebal (Bold)" style="font-weight:700;">B</button>
                        <button type="button" id="createFmt_italic" onclick="execFmt('italic', 'create')" class="fmt-tool-btn" title="Miring (Italic)" style="font-style:italic;">I</button>
                        <div style="width:1px; height:18px; background:rgba(255,255,255,0.1); margin:auto 4px;"></div>
                        <button type="button" id="createFmt_insertUnorderedList" onclick="execFmt('insertUnorderedList', 'create')" class="fmt-tool-btn" title="Daftar Poin (Bullet List)">•</button>
                        <button type="button" id="createFmt_insertOrderedList" onclick="execFmt('insertOrderedList', 'create')" class="fmt-tool-btn" title="Daftar Angka (Number List)">1.</button>
                    </div>

                    <!-- Content Rich Editor -->
                    <div contenteditable="true" id="createNoteEditor" class="rich-editor" placeholder="Buat catatan..." oninput="syncCreateContent()" onkeyup="checkActiveFmt('create')" onclick="checkActiveFmt('create')"></div>
                    <input type="hidden" name="content" id="noteContentArea">

                    <!-- Form Footer Toolbar -->
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 10px; border-top: 1px solid rgba(255,255,255,0.06);">
                        <!-- Color Palette Picker -->
                        <div style="display: flex; gap: 6px; align-items: center;">
                            <span style="font-size: 12px; color: var(--text-muted); margin-right: 4px;">Warna:</span>
                            <div onclick="selectNoteColor('default', this)" class="color-dot active" style="width: 20px; height: 20px; border-radius: 50%; background: var(--bg-surface); border: 2px solid var(--text-muted); cursor: pointer;" title="Default"></div>
                            <div onclick="selectNoteColor('purple', this)" class="color-dot" style="width: 20px; height: 20px; border-radius: 50%; background: #8b5cf6; cursor: pointer;" title="Ungu"></div>
                            <div onclick="selectNoteColor('blue', this)" class="color-dot" style="width: 20px; height: 20px; border-radius: 50%; background: #3b82f6; cursor: pointer;" title="Biru"></div>
                            <div onclick="selectNoteColor('green', this)" class="color-dot" style="width: 20px; height: 20px; border-radius: 50%; background: #10b981; cursor: pointer;" title="Hijau"></div>
                            <div onclick="selectNoteColor('amber', this)" class="color-dot" style="width: 20px; height: 20px; border-radius: 50%; background: #f59e0b; cursor: pointer;" title="Kuning/Amber"></div>
                            <div onclick="selectNoteColor('red', this)" class="color-dot" style="width: 20px; height: 20px; border-radius: 50%; background: #ef4444; cursor: pointer;" title="Merah"></div>
                        </div>

                        <!-- Action Buttons -->
                        <div style="display: flex; gap: 8px;">
                            <button type="button" onclick="collapseNoteBox()" class="btn btn-ghost" style="padding: 6px 14px; font-size: 13px;">Batal</button>
                            <button type="submit" class="topbar-btn topbar-btn-primary" style="padding: 6px 16px; font-size: 13px;">Simpan</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- PINNED NOTES SECTION -->
        <div id="pinnedSection" style="margin-bottom: 32px; {{ $pinnedNotes->count() == 0 ? 'display:none;' : '' }}">
            <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1.2px; margin-bottom: 14px; display: flex; align-items: center; gap: 6px;">
                📌 DISEMATKAN (<span id="pinnedCount">{{ $pinnedNotes->count() }}</span>)
            </div>

            <div class="notes-grid" id="pinnedNotesGrid" style="min-height: 80px; padding: 4px; border: 1px dashed rgba(245, 158, 11, 0.3); border-radius: 16px; transition: border-color 0.2s;">
                @foreach($pinnedNotes as $note)
                    @include('admin.notes.partials.card', ['note' => $note])
                @endforeach
            </div>
        </div>

        <!-- OTHER NOTES SECTION -->
        <div id="otherSection">
            <div id="otherSectionHeader" style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1.2px; margin-bottom: 14px; {{ $pinnedNotes->count() == 0 ? 'display:none;' : '' }}">
                CATATAN LAINNYA
            </div>

            <div class="notes-grid" id="otherNotesGrid" style="min-height: 100px;">
                @foreach($otherNotes as $note)
                    @include('admin.notes.partials.card', ['note' => $note])
                @endforeach
            </div>

            <div id="notesEmptyState" style="text-align: center; padding: 60px 20px; color: var(--text-muted); {{ ($pinnedNotes->count() + $otherNotes->count()) > 0 ? 'display:none;' : '' }}">
                <div style="font-size: 48px; margin-bottom: 12px;">💡</div>
                <h3 style="font-size: 16px; font-weight: 700; color: var(--text-primary); margin: 0 0 6px;">Belum Ada Catatan</h3>
                <p style="font-size: 13px; margin: 0;">Catatan yang Anda buat akan muncul di sini.</p>
            </div>
        </div>


    </div>

    <!-- Edit Note Modal -->
    <div id="editNoteModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
        <div class="card" id="editModalCard" style="width: 100%; max-width: 560px; padding: 24px; border-radius: 16px; background: var(--bg-surface); border: 1px solid var(--border); box-shadow: 0 20px 50px rgba(0,0,0,0.5);">
            <form id="editNoteForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <input type="text" name="title" id="editModalTitle" placeholder="Judul" style="width: 100%; background: transparent; border: none; outline: none; font-size: 17px; font-weight: 700; color: var(--text-primary);">
                    <button type="button" id="editModalPinBtn" onclick="toggleEditPin()" style="background: none; border: none; cursor: pointer; font-size: 18px;" title="Pin / Unpin">📌</button>
                    <input type="hidden" name="is_pinned" id="editModalIsPinned" value="0">
                </div>
                <div id="editModalAuthorBadge" style="font-size: 11px; color: var(--text-muted); margin-bottom: 12px; display: flex; align-items: center; gap: 6px;"></div>

                <!-- Formatting Toolbar -->
                <div style="display: flex; gap: 4px; margin-bottom: 8px; flex-wrap: wrap; background: rgba(255,255,255,0.04); padding: 4px 6px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.06);">
                    <button type="button" id="editFmt_bold" onclick="execFmt('bold', 'edit')" class="fmt-tool-btn" title="Tebal (Bold)" style="font-weight:700;">B</button>
                    <button type="button" id="editFmt_italic" onclick="execFmt('italic', 'edit')" class="fmt-tool-btn" title="Miring (Italic)" style="font-style:italic;">I</button>
                    <div style="width:1px; height:18px; background:rgba(255,255,255,0.1); margin:auto 4px;"></div>
                    <button type="button" id="editFmt_insertUnorderedList" onclick="execFmt('insertUnorderedList', 'edit')" class="fmt-tool-btn" title="Daftar Poin (Bullet List)">•</button>
                    <button type="button" id="editFmt_insertOrderedList" onclick="execFmt('insertOrderedList', 'edit')" class="fmt-tool-btn" title="Daftar Angka (Number List)">1.</button>
                </div>

                <!-- Content Rich Editor -->
                <div contenteditable="true" id="editNoteEditor" class="rich-editor" placeholder="Buat catatan..." oninput="syncEditContent()" onkeyup="checkActiveFmt('edit')" onclick="checkActiveFmt('edit')"></div>
                <input type="hidden" name="content" id="editModalContent">

                <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 14px; border-top: 1px solid rgba(255,255,255,0.06);">
                    <!-- Color Picker for Edit -->
                    <div style="display: flex; gap: 6px; align-items: center;">
                        <input type="hidden" name="color" id="editModalColor" value="default">
                        <div onclick="selectEditColor('default', this)" class="edit-color-dot" style="width: 20px; height: 20px; border-radius: 50%; background: var(--bg-surface); border: 2px solid var(--text-muted); cursor: pointer;"></div>
                        <div onclick="selectEditColor('purple', this)" class="edit-color-dot" style="width: 20px; height: 20px; border-radius: 50%; background: #8b5cf6; cursor: pointer;"></div>
                        <div onclick="selectEditColor('blue', this)" class="edit-color-dot" style="width: 20px; height: 20px; border-radius: 50%; background: #3b82f6; cursor: pointer;"></div>
                        <div onclick="selectEditColor('green', this)" class="edit-color-dot" style="width: 20px; height: 20px; border-radius: 50%; background: #10b981; cursor: pointer;"></div>
                        <div onclick="selectEditColor('amber', this)" class="edit-color-dot" style="width: 20px; height: 20px; border-radius: 50%; background: #f59e0b; cursor: pointer;"></div>
                        <div onclick="selectEditColor('red', this)" class="edit-color-dot" style="width: 20px; height: 20px; border-radius: 50%; background: #ef4444; cursor: pointer;"></div>
                    </div>

                    <div style="display: flex; gap: 8px;">
                        <button type="button" onclick="closeEditModal()" class="btn btn-ghost" style="padding: 6px 14px; font-size: 13px;">Batal</button>
                        <button type="submit" class="topbar-btn topbar-btn-primary" style="padding: 6px 16px; font-size: 13px;">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- SortableJS CDN for Drag & Drop Reordering -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <!-- Styles & Client-side Script -->
    <style>
        .notes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 16px;
        }

        .note-card {
            border-radius: 14px;
            padding: 16px 18px;
            transition: all 0.2s ease;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 120px;
            cursor: grab;
        }

        .note-card:active {
            cursor: grabbing;
        }

        .note-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }

        .note-ghost {
            opacity: 0.45;
            border: 2px dashed var(--accent) !important;
            transform: scale(0.98);
        }

        .note-chosen {
            box-shadow: 0 15px 35px rgba(0,0,0,0.5) !important;
            cursor: grabbing !important;
        }

        .note-drag {
            opacity: 0.9;
        }

        .note-card .note-actions {
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .note-card:hover .note-actions {
            opacity: 1;
        }

        .color-dot.active, .edit-color-dot.active {
            box-shadow: 0 0 0 2px var(--accent);
            transform: scale(1.15);
        }


        .rich-editor {
            width: 100%;
            min-height: 90px;
            max-height: 250px;
            overflow-y: auto;
            background: transparent;
            border: none;
            outline: none;
            font-size: 14px;
            color: var(--text-primary);
            font-family: inherit;
            line-height: 1.6;
            padding: 4px 0;
            margin-bottom: 12px;
        }

        .rich-editor[placeholder]:empty:before {
            content: attr(placeholder);
            color: var(--text-muted);
            cursor: text;
        }

        .rich-editor ul, .rich-editor ol,
        .note-card ul, .note-card ol {
            padding-left: 20px;
            margin: 4px 0;
        }

        .rich-editor li,
        .note-card li {
            margin-bottom: 2px;
        }


        .fmt-tool-btn {
            background: transparent;
            border: 1px solid transparent;
            color: var(--text-secondary);
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.15s ease;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .fmt-tool-btn:hover {
            background: rgba(255,255,255,0.08);
            color: var(--text-primary);
        }

        .fmt-tool-btn.active {
            background: rgba(184, 255, 0, 0.18) !important;
            color: #b8ff00 !important;
            border-color: rgba(184, 255, 0, 0.4) !important;
            box-shadow: 0 0 10px rgba(184, 255, 0, 0.2);
        }
    </style>

    <script>
        function execFmt(cmd, scope) {
            const editorId = scope === 'create' ? 'createNoteEditor' : 'editNoteEditor';
            const editor = document.getElementById(editorId);
            if (editor) editor.focus();
            document.execCommand(cmd, false, null);
            checkActiveFmt(scope);
            if (scope === 'create') syncCreateContent();
            else syncEditContent();
        }

        function checkActiveFmt(scope) {
            const prefix = scope === 'create' ? 'createFmt' : 'editFmt';
            ['bold', 'italic', 'insertUnorderedList', 'insertOrderedList'].forEach(cmd => {
                const btn = document.getElementById(prefix + '_' + cmd);
                if (btn) {
                    if (document.queryCommandState(cmd)) {
                        btn.classList.add('active');
                    } else {
                        btn.classList.remove('active');
                    }
                }
            });
        }

        function syncCreateContent() {
            const html = document.getElementById('createNoteEditor').innerHTML;
            document.getElementById('noteContentArea').value = (html === '<br>' || html.trim() === '') ? '' : html;
        }

        function syncEditContent() {
            const html = document.getElementById('editNoteEditor').innerHTML;
            document.getElementById('editModalContent').value = (html === '<br>' || html.trim() === '') ? '' : html;
        }

        const colorStyles = {
            default: { bg: 'var(--bg-surface)', border: 'var(--border)' },
            purple:  { bg: 'rgba(139, 92, 246, 0.15)', border: 'rgba(139, 92, 246, 0.35)' },
            blue:    { bg: 'rgba(59, 130, 246, 0.15)', border: 'rgba(59, 130, 246, 0.35)' },
            green:   { bg: 'rgba(16, 185, 129, 0.15)', border: 'rgba(16, 185, 129, 0.35)' },
            amber:   { bg: 'rgba(245, 158, 11, 0.15)', border: 'rgba(245, 158, 11, 0.35)' },
            red:     { bg: 'rgba(239, 68, 68, 0.15)', border: 'rgba(239, 68, 68, 0.35)' }
        };

        function selectNoteColor(color, el) {
            document.getElementById('noteColorInput').value = color;
            document.querySelectorAll('.color-dot').forEach(d => d.classList.remove('active'));
            if (el) el.classList.add('active');

            const style = colorStyles[color] || colorStyles.default;
            const card = document.getElementById('noteInputCard');
            if (card) {
                card.style.background = style.bg;
                card.style.borderColor = style.border;
            }
        }

        function selectEditColor(color, el) {
            document.getElementById('editModalColor').value = color;
            document.querySelectorAll('.edit-color-dot').forEach(d => d.classList.remove('active'));
            if (el) el.classList.add('active');

            const style = colorStyles[color] || colorStyles.default;
            const card = document.getElementById('editModalCard');
            if (card) {
                card.style.background = style.bg;
                card.style.borderColor = style.border;
            }
        }

        function expandNoteBox() {
            document.getElementById('noteCollapsedView').style.display = 'none';
            document.getElementById('noteExpandedView').style.display = 'block';
            document.getElementById('createNoteEditor').focus();
        }

        function collapseNoteBox() {
            document.getElementById('createNoteForm').reset();
            document.getElementById('createNoteEditor').innerHTML = '';
            document.getElementById('noteContentArea').value = '';
            document.getElementById('noteCollapsedView').style.display = 'flex';
            document.getElementById('noteExpandedView').style.display = 'none';
            document.getElementById('noteColorInput').value = 'default';
            document.getElementById('notePinInput').value = '0';
            document.getElementById('pinToggleBtn').style.opacity = '0.4';
            document.getElementById('pinToggleBtn').style.filter = 'grayscale(1)';
            selectNoteColor('default', document.querySelector('.color-dot'));
        }

        function togglePinForm() {
            const input = document.getElementById('notePinInput');
            const btn = document.getElementById('pinToggleBtn');
            if (input.value === '1') {
                input.value = '0';
                btn.style.opacity = '0.4';
                btn.style.filter = 'grayscale(1)';
                btn.style.transform = 'scale(1)';
            } else {
                input.value = '1';
                btn.style.opacity = '1';
                btn.style.filter = 'none';
                btn.style.transform = 'scale(1.2)';
            }
        }

        function openEditModal(id, title, content, color, isPinned, authorName) {
            const form = document.getElementById('editNoteForm');
            form.action = '/admin/notes/' + id;
            document.getElementById('editModalTitle').value = title || '';
            document.getElementById('editNoteEditor').innerHTML = content || '';
            document.getElementById('editModalContent').value = content || '';
            document.getElementById('editModalColor').value = color || 'default';
            document.getElementById('editModalIsPinned').value = isPinned ? '1' : '0';
            document.getElementById('editModalPinBtn').style.opacity = isPinned ? '1' : '0.6';

            const authorBadge = document.getElementById('editModalAuthorBadge');
            if (authorBadge) {
                authorBadge.innerHTML = authorName ? `<span>✍️ Ditulis oleh: <strong style="color:var(--text-primary);">${authorName}</strong></span>` : '';
            }

            selectEditColor(color || 'default', null);

            document.querySelectorAll('.edit-color-dot').forEach(d => {
                if (d.getAttribute('onclick') && d.getAttribute('onclick').includes(`'${color}'`)) {
                    d.classList.add('active');
                } else {
                    d.classList.remove('active');
                }
            });

            document.getElementById('editNoteModal').style.display = 'flex';
            checkActiveFmt('edit');
        }

        function closeEditModal() {
            document.getElementById('editNoteModal').style.display = 'none';
        }

        function toggleEditPin() {
            const input = document.getElementById('editModalIsPinned');
            const btn = document.getElementById('editModalPinBtn');
            if (input.value === '1') {
                input.value = '0';
                btn.style.opacity = '0.6';
            } else {
                input.value = '1';
                btn.style.opacity = '1';
            }
        }


        // Initialize SortableJS for Drag & Drop Reordering & Pinning
        document.addEventListener('DOMContentLoaded', function() {
            const pinnedGrid = document.getElementById('pinnedNotesGrid');
            const otherGrid = document.getElementById('otherNotesGrid');

            const sortableOptions = {
                group: 'notesGroup',
                animation: 200,
                ghostClass: 'note-ghost',
                chosenClass: 'note-chosen',
                dragClass: 'note-drag',
                filter: '.note-actions, button, form',
                preventOnFilter: false,
                onEnd: function() {
                    updateSectionVisibility();
                    saveNoteOrders();
                }
            };

            if (pinnedGrid) new Sortable(pinnedGrid, sortableOptions);
            if (otherGrid) new Sortable(otherGrid, sortableOptions);
        });

        function updateSectionVisibility() {
            const pinnedGrid = document.getElementById('pinnedNotesGrid');
            const pinnedSection = document.getElementById('pinnedSection');
            const otherHeader = document.getElementById('otherSectionHeader');
            const pinnedCount = document.getElementById('pinnedCount');

            if (pinnedGrid && pinnedSection) {
                const count = pinnedGrid.querySelectorAll('.note-card').length;
                if (pinnedCount) pinnedCount.textContent = count;
                if (count > 0) {
                    pinnedSection.style.display = 'block';
                    if (otherHeader) otherHeader.style.display = 'block';
                } else {
                    pinnedSection.style.display = 'none';
                    if (otherHeader) otherHeader.style.display = 'none';
                }
            }
        }

        function saveNoteOrders() {
            const pinnedGrid = document.getElementById('pinnedNotesGrid');
            const otherGrid = document.getElementById('otherNotesGrid');

            const pinnedIds = pinnedGrid ? Array.from(pinnedGrid.querySelectorAll('.note-card')).map(el => el.dataset.id).filter(Boolean) : [];
            const otherIds = otherGrid ? Array.from(otherGrid.querySelectorAll('.note-card')).map(el => el.dataset.id).filter(Boolean) : [];

            fetch("{{ route('admin.notes.reorder') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    pinned: pinnedIds,
                    others: otherIds
                })
            });
        }
    </script>
</x-admin-layout>



