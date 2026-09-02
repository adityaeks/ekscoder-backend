<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $e_modul->title }} - Flipbook E-Modul Preview</title>
    
    <!-- PDF.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <!-- StPageFlip CDN -->
    <script src="https://cdn.jsdelivr.net/npm/page-flip@2.0.7/dist/js/page-flip.browser.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #3b3c3d;
            background-image: radial-gradient(circle at center, #4b4c4e 0%, #292a2b 100%);
            color: #ffffff;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            user-select: none;
        }

        /* Top Header */
        .flip-header {
            height: 48px;
            background: rgba(25, 26, 27, 0.85);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            z-index: 50;
        }

        .flip-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.5px;
            color: #e2e8f0;
        }

        .flip-brand a {
            color: inherit;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 4px 10px;
            border-radius: 6px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            font-size: 12px;
            transition: all 0.2s;
        }

        .flip-brand a:hover {
            background: rgba(255,255,255,0.12);
        }

        .flip-title {
            font-size: 13.5px;
            font-weight: 600;
            color: #f8fafc;
            max-width: 50%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-align: center;
        }

        .flip-header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Flipbook Stage Area */
        .flip-stage {
            flex: 1;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: 20px 40px 60px 40px;
        }

        .flip-container-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s ease;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.7);
            border-radius: 4px;
        }

        #flipbook {
            display: none;
            background: transparent;
        }

        .page {
            background-color: #ffffff;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: inset 0 0 30px rgba(0,0,0,0.05);
        }

        .page-content {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .page-content canvas {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            display: block;
        }

        /* Middle spine shadow overlay */
        .page.--left .page-content::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            width: 25px;
            background: linear-gradient(to left, rgba(0,0,0,0.18) 0%, rgba(0,0,0,0) 100%);
            pointer-events: none;
        }

        .page.--right .page-content::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 25px;
            background: linear-gradient(to right, rgba(0,0,0,0.18) 0%, rgba(0,0,0,0) 100%);
            pointer-events: none;
        }

        /* Navigation Arrows */
        .nav-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 46px;
            height: 46px;
            background: rgba(30, 30, 30, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 40;
            transition: all 0.2s ease;
            backdrop-filter: blur(8px);
        }

        .nav-arrow:hover:not(:disabled) {
            background: rgba(99, 102, 241, 0.9);
            border-color: rgba(99, 102, 241, 1);
            transform: translateY(-50%) scale(1.1);
        }

        .nav-arrow:disabled {
            opacity: 0.2;
            cursor: not-allowed;
        }

        .nav-arrow.prev { left: 16px; }
        .nav-arrow.next { right: 16px; }

        /* Bottom Floating Controls Bar (FlipHTML5 style) */
        .flip-controls-bar {
            position: absolute;
            bottom: 12px;
            left: 50%;
            transform: translateX(-50%);
            height: 44px;
            background: rgba(22, 23, 24, 0.9);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 22px;
            display: flex;
            align-items: center;
            padding: 0 16px;
            gap: 12px;
            z-index: 50;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            max-width: 90vw;
        }

        .ctrl-btn {
            background: transparent;
            border: none;
            color: #cbd5e1;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .ctrl-btn:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
        }

        .ctrl-btn.active {
            color: #818cf8;
            background: rgba(99, 102, 241, 0.2);
        }

        .ctrl-separator {
            width: 1px;
            height: 18px;
            background: rgba(255, 255, 255, 0.12);
        }

        .page-indicator-badge {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            font-weight: 600;
            color: #e2e8f0;
            min-width: 65px;
            text-align: center;
        }

        /* Scrubber / Slider */
        .page-slider {
            -webkit-appearance: none;
            width: 140px;
            height: 4px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 2px;
            outline: none;
            cursor: pointer;
        }

        .page-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #ffffff;
            cursor: pointer;
            box-shadow: 0 0 6px rgba(0,0,0,0.5);
            transition: transform 0.1s;
        }

        .page-slider::-webkit-slider-thumb:hover {
            transform: scale(1.3);
            background: #818cf8;
        }

        /* Loading Spinner */
        .loading-overlay {
            position: absolute;
            inset: 0;
            background: rgba(30, 31, 32, 0.92);
            backdrop-filter: blur(10px);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 100;
            gap: 16px;
        }

        .spinner {
            width: 44px;
            height: 44px;
            border: 3px solid rgba(255,255,255,0.1);
            border-top-color: #6366f1;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Thumbnails Drawer */
        .thumbs-drawer {
            position: absolute;
            bottom: 65px;
            left: 50%;
            transform: translateX(-50%) translateY(20px);
            width: 85vw;
            max-width: 900px;
            height: 150px;
            background: rgba(20, 21, 23, 0.95);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            padding: 14px;
            display: flex;
            gap: 12px;
            overflow-x: auto;
            z-index: 60;
            opacity: 0;
            pointer-events: none;
            transition: all 0.25s ease;
            box-shadow: 0 15px 40px rgba(0,0,0,0.6);
        }

        .thumbs-drawer.open {
            opacity: 1;
            pointer-events: auto;
            transform: translateX(-50%) translateY(0);
        }

        .thumb-item {
            flex-shrink: 0;
            width: 80px;
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
            position: relative;
        }

        .thumb-item:hover {
            border-color: rgba(99, 102, 241, 0.7);
            transform: translateY(-2px);
        }

        .thumb-item.active {
            border-color: #6366f1;
            box-shadow: 0 0 12px rgba(99, 102, 241, 0.5);
        }

        .thumb-canvas-wrapper {
            width: 100%;
            flex: 1;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .thumb-canvas-wrapper canvas {
            max-width: 100%;
            max-height: 100%;
        }

        .thumb-page-num {
            font-size: 10px;
            font-family: 'JetBrains Mono', monospace;
            background: rgba(0,0,0,0.6);
            width: 100%;
            text-align: center;
            padding: 2px 0;
            color: #cbd5e1;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="flip-header">
        <div class="flip-brand">
            <a href="{{ route('admin.e-modul.index') }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                Kembali ke Admin
            </a>
            <span style="opacity:0.3; margin:0 4px;">|</span>
            <span style="font-size:12px; color:#94a3b8;">📖 FlipBook Reader</span>
        </div>

        <div class="flip-title" title="{{ $e_modul->title }}">
            {{ $e_modul->title }}
        </div>

        <div class="flip-header-actions">
            @if($e_modul->pdf_url)
            <a href="{{ $e_modul->pdf_url }}" download class="ctrl-btn" title="Download PDF" style="text-decoration:none;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            </a>
            @endif
            <button class="ctrl-btn" id="btnFullscreen" title="Layar Penuh (Fullscreen)">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/></svg>
            </button>
        </div>
    </header>

    <!-- Main Stage -->
    <main class="flip-stage">
        <!-- Navigation Arrows -->
        <button class="nav-arrow prev" id="btnPrevPage" title="Halaman Sebelumnya">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        </button>

        <button class="nav-arrow next" id="btnNextPage" title="Halaman Berikutnya">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
        </button>

        <!-- Loading Overlay -->
        <div class="loading-overlay" id="loadingOverlay">
            <div class="spinner"></div>
            <div style="font-size:14px; font-weight:500; color:#e2e8f0;" id="loadingText">Memuat Dokumen PDF...</div>
        </div>

        <!-- Flipbook Container -->
        <div class="flip-container-wrapper" id="flipWrapper">
            <div id="flipbook"></div>
        </div>

        <!-- Thumbnails Drawer -->
        <div class="thumbs-drawer" id="thumbsDrawer"></div>

        <!-- Bottom Toolbar (FlipHTML5 style) -->
        <div class="flip-controls-bar">
            <!-- First Page -->
            <button class="ctrl-btn" id="btnFirstPage" title="Halaman Pertama">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="11 17 6 12 11 7"/><polyline points="18 17 13 12 18 7"/></svg>
            </button>

            <!-- Prev -->
            <button class="ctrl-btn" id="btnBottomPrev" title="Sebelumnya">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            </button>

            <!-- Zoom in/out -->
            <button class="ctrl-btn" id="btnZoomOut" title="Perkecil (-)">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/></svg>
            </button>
            <button class="ctrl-btn" id="btnZoomIn" title="Perbesar (+)">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            </button>

            <!-- Thumbnails grid -->
            <button class="ctrl-btn" id="btnToggleThumbs" title="Daftar Thumbnail Halaman">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            </button>

            <!-- Sound toggle -->
            <button class="ctrl-btn active" id="btnToggleSound" title="Suara Balik Halaman (Aktif)">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" id="soundIcon"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"/></svg>
            </button>

            <div class="ctrl-separator"></div>

            <!-- Page Number Scrubber -->
            <span class="page-indicator-badge" id="pageIndicator">0 / 0</span>
            <input type="range" class="page-slider" id="pageSlider" min="1" max="1" value="1" title="Geser untuk memilih halaman">

            <div class="ctrl-separator"></div>

            <!-- Next -->
            <button class="ctrl-btn" id="btnBottomNext" title="Berikutnya">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </button>

            <!-- Last Page -->
            <button class="ctrl-btn" id="btnLastPage" title="Halaman Terakhir">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="13 17 18 12 13 7"/><polyline points="6 17 11 12 6 7"/></svg>
            </button>
        </div>
    </main>

    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        const PDF_URL = @json($e_modul->pdf_url);

        let pageFlip = null;
        let pdfDoc = null;
        let totalPages = 0;
        let currentPage = 1;
        let soundEnabled = true;
        let currentZoom = 1;

        // Sound synthesizer (realistic paper page flip sound)
        function playFlipSound() {
            if (!soundEnabled) return;
            try {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!AudioContext) return;
                const ctx = new AudioContext();
                
                // Buffer noise for paper rustle
                const bufferSize = ctx.sampleRate * 0.15; // 150ms
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
                gain.gain.setValueAtTime(0.3, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.15);

                noise.connect(filter);
                filter.connect(gain);
                gain.connect(ctx.destination);

                noise.start();
            } catch (e) {
                console.error("Audio error", e);
            }
        }

        async function initFlipbook() {
            const loadingOverlay = document.getElementById('loadingOverlay');
            const loadingText = document.getElementById('loadingText');
            const flipbookEl = document.getElementById('flipbook');
            const thumbsDrawer = document.getElementById('thumbsDrawer');

            if (!PDF_URL) {
                loadingText.textContent = "File PDF belum diunggah untuk modul ini.";
                return;
            }

            try {
                loadingText.textContent = "Mengunduh file PDF...";
                const loadingTask = pdfjsLib.getDocument(PDF_URL);
                pdfDoc = await loadingTask.promise;
                totalPages = pdfDoc.numPages;

                loadingText.textContent = `Merender 0 dari ${totalPages} halaman...`;

                // Calculate dimensions based on first page aspect ratio
                const firstPage = await pdfDoc.getPage(1);
                const viewport = firstPage.getViewport({ scale: 1.0 });
                const pageWidth = Math.min(550, window.innerWidth * 0.45);
                const pageHeight = pageWidth * (viewport.height / viewport.width);

                flipbookEl.innerHTML = '';
                thumbsDrawer.innerHTML = '';

                // Render each page into canvas elements
                for (let i = 1; i <= totalPages; i++) {
                    loadingText.textContent = `Merender halaman ${i} dari ${totalPages}...`;
                    const page = await pdfDoc.getPage(i);
                    const scale = 2.0; // High DPI crisp text
                    const pageViewport = page.getViewport({ scale: scale });

                    // Flip page wrapper
                    const pageDiv = document.createElement('div');
                    pageDiv.className = `page ${i % 2 === 0 ? '--left' : '--right'}`;
                    
                    const contentDiv = document.createElement('div');
                    contentDiv.className = 'page-content';

                    const canvas = document.createElement('canvas');
                    canvas.width = pageViewport.width;
                    canvas.height = pageViewport.height;
                    const ctx = canvas.getContext('2d');

                    await page.render({ canvasContext: ctx, viewport: pageViewport }).promise;

                    contentDiv.appendChild(canvas);
                    pageDiv.appendChild(contentDiv);
                    flipbookEl.appendChild(pageDiv);

                    // Add thumbnail item
                    const thumbItem = document.createElement('div');
                    thumbItem.className = `thumb-item ${i === 1 ? 'active' : ''}`;
                    thumbItem.setAttribute('data-page', i);
                    
                    const thumbWrapper = document.createElement('div');
                    thumbWrapper.className = 'thumb-canvas-wrapper';
                    const thumbCanvas = document.createElement('canvas');
                    thumbCanvas.width = canvas.width;
                    thumbCanvas.height = canvas.height;
                    thumbCanvas.getContext('2d').drawImage(canvas, 0, 0);
                    thumbWrapper.appendChild(thumbCanvas);

                    const thumbLabel = document.createElement('div');
                    thumbLabel.className = 'thumb-page-num';
                    thumbLabel.textContent = i;

                    thumbItem.appendChild(thumbWrapper);
                    thumbItem.appendChild(thumbLabel);

                    thumbItem.addEventListener('click', () => {
                        if (pageFlip) {
                            pageFlip.flip(i - 1);
                        }
                    });

                    thumbsDrawer.appendChild(thumbItem);
                }

                loadingOverlay.style.display = 'none';
                flipbookEl.style.display = 'block';

                // Initialize St.PageFlip
                pageFlip = new St.PageFlip(flipbookEl, {
                    width: pageWidth,
                    height: pageHeight,
                    size: 'stretch',
                    minWidth: 300,
                    maxWidth: 800,
                    minHeight: 400,
                    maxHeight: 1100,
                    maxShadowOpacity: 0.5,
                    showCover: true,
                    mobileScrollSupport: false,
                    usePortrait: window.innerWidth < 768
                });

                pageFlip.loadFromHTML(document.querySelectorAll('.page'));

                // Update UI Controls
                const slider = document.getElementById('pageSlider');
                slider.max = totalPages;
                slider.value = 1;

                updateIndicator(0);

                pageFlip.on('flip', (e) => {
                    playFlipSound();
                    updateIndicator(e.data);
                });

            } catch (err) {
                console.error("PDF Load Error:", err);
                loadingText.textContent = "Gagal memuat PDF. Pastikan file PDF valid dan dapat diakses.";
            }
        }

        function updateIndicator(pageIndex) {
            const pageNum = pageIndex + 1;
            const slider = document.getElementById('pageSlider');
            const indicator = document.getElementById('pageIndicator');
            
            slider.value = pageNum;

            if (pageFlip && pageFlip.getOrientation() === 'landscape' && pageNum > 1 && pageNum < totalPages) {
                indicator.textContent = `${pageNum}-${pageNum + 1} / ${totalPages}`;
            } else {
                indicator.textContent = `${pageNum} / ${totalPages}`;
            }

            // Update thumbs active state
            document.querySelectorAll('.thumb-item').forEach(th => {
                const p = parseInt(th.getAttribute('data-page'), 10);
                th.classList.toggle('active', p === pageNum || (pageNum > 1 && p === pageNum + 1));
            });

            // Update arrow state
            document.getElementById('btnPrevPage').disabled = (pageIndex === 0);
            document.getElementById('btnBottomPrev').disabled = (pageIndex === 0);
            document.getElementById('btnFirstPage').disabled = (pageIndex === 0);

            document.getElementById('btnNextPage').disabled = (pageIndex >= totalPages - 1);
            document.getElementById('btnBottomNext').disabled = (pageIndex >= totalPages - 1);
            document.getElementById('btnLastPage').disabled = (pageIndex >= totalPages - 1);
        }

        // Event Listeners
        document.getElementById('btnPrevPage').addEventListener('click', () => pageFlip && pageFlip.flipPrev());
        document.getElementById('btnNextPage').addEventListener('click', () => pageFlip && pageFlip.flipNext());
        document.getElementById('btnBottomPrev').addEventListener('click', () => pageFlip && pageFlip.flipPrev());
        document.getElementById('btnBottomNext').addEventListener('click', () => pageFlip && pageFlip.flipNext());
        document.getElementById('btnFirstPage').addEventListener('click', () => pageFlip && pageFlip.flip(0));
        document.getElementById('btnLastPage').addEventListener('click', () => pageFlip && pageFlip.flip(totalPages - 1));

        document.getElementById('pageSlider').addEventListener('input', (e) => {
            const targetPage = parseInt(e.target.value, 10);
            if (pageFlip) pageFlip.flip(targetPage - 1);
        });

        // Zoom Controls
        document.getElementById('btnZoomIn').addEventListener('click', () => {
            currentZoom = Math.min(currentZoom + 0.15, 1.6);
            document.getElementById('flipWrapper').style.transform = `scale(${currentZoom})`;
        });

        document.getElementById('btnZoomOut').addEventListener('click', () => {
            currentZoom = Math.max(currentZoom - 0.15, 0.7);
            document.getElementById('flipWrapper').style.transform = `scale(${currentZoom})`;
        });

        // Thumbnails toggle
        document.getElementById('btnToggleThumbs').addEventListener('click', function() {
            const drawer = document.getElementById('thumbsDrawer');
            drawer.classList.toggle('open');
            this.classList.toggle('active', drawer.classList.contains('open'));
        });

        // Sound toggle
        document.getElementById('btnToggleSound').addEventListener('click', function() {
            soundEnabled = !soundEnabled;
            this.classList.toggle('active', soundEnabled);
            this.title = soundEnabled ? 'Suara Balik Halaman (Aktif)' : 'Suara Balik Halaman (Nonaktif)';
        });

        // Fullscreen toggle
        document.getElementById('btnFullscreen').addEventListener('click', () => {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => alert(err.message));
            } else {
                document.exitFullscreen();
            }
        });

        // Keyboard navigation
        window.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowRight' || e.key === 'PageDown') {
                pageFlip && pageFlip.flipNext();
            } else if (e.key === 'ArrowLeft' || e.key === 'PageUp') {
                pageFlip && pageFlip.flipPrev();
            } else if (e.key === 'Home') {
                pageFlip && pageFlip.flip(0);
            } else if (e.key === 'End') {
                pageFlip && pageFlip.flip(totalPages - 1);
            } else if (e.key === 'f' || e.key === 'F') {
                document.getElementById('btnFullscreen').click();
            }
        });

        window.addEventListener('DOMContentLoaded', initFlipbook);
    </script>
</body>
</html>
