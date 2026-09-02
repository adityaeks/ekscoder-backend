<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $e_modul->title }} - Flipbook E-Modul & AI Assistant</title>
    
    <!-- PDF.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <!-- StPageFlip CDN -->
    <script src="https://cdn.jsdelivr.net/npm/page-flip@2.0.7/dist/js/page-flip.browser.js"></script>
    <!-- Marked CDN for Markdown parsing -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

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
            background: rgba(25, 26, 27, 0.9);
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
            color: #e2e8f0;
        }

        .flip-brand a {
            color: inherit;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
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
            max-width: 45%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-align: center;
        }

        .flip-header-actions {
            display: flex;
            align-items: center;
            gap: 8px;
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
            display: block;
        }

        /* Spine Shadows */
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

        /* Controls Bar */
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
            gap: 10px;
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

        .page-slider {
            -webkit-appearance: none;
            width: 130px;
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
        }

        .loading-overlay {
            position: absolute;
            inset: 0;
            background: rgba(30, 31, 32, 0.94);
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
            height: 140px;
            background: rgba(20, 21, 23, 0.95);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            padding: 12px;
            display: flex;
            gap: 10px;
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

        .thumb-item.active {
            border-color: #6366f1;
            box-shadow: 0 0 10px rgba(99, 102, 241, 0.5);
        }

        .thumb-item canvas {
            max-width: 100%;
            max-height: 85px;
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

        /* Floating AI Trigger Widget (FlipHTML5 blue bubble style) */
        .ai-floating-btn {
            position: fixed;
            bottom: 24px;
            right: 24px;
            height: 48px;
            padding: 0 18px 0 14px;
            background: linear-gradient(135deg, #0284c7 0%, #3b82f6 50%, #6366f1 100%);
            color: #ffffff;
            border-radius: 24px;
            border: 1px solid rgba(255,255,255,0.25);
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            z-index: 90;
            box-shadow: 0 10px 25px -5px rgba(2, 132, 199, 0.6), 0 0 15px rgba(99, 102, 241, 0.4);
            transition: all 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            font-size: 13px;
            font-weight: 600;
        }

        .ai-floating-btn:hover {
            transform: scale(1.06) translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(2, 132, 199, 0.8), 0 0 20px rgba(99, 102, 241, 0.6);
        }

        /* AI Chat Sidebar Drawer */
        .ai-drawer {
            position: fixed;
            top: 48px;
            right: 0;
            bottom: 0;
            width: 400px;
            max-width: 95vw;
            background: rgba(18, 20, 24, 0.96);
            backdrop-filter: blur(20px);
            border-left: 1px solid rgba(255, 255, 255, 0.12);
            display: flex;
            flex-direction: column;
            z-index: 100;
            transform: translateX(100%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: -15px 0 40px rgba(0,0,0,0.6);
        }

        .ai-drawer.open {
            transform: translateX(0);
        }

        .ai-header {
            padding: 14px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex;
            flex-direction: column;
            gap: 10px;
            background: rgba(255,255,255,0.02);
        }

        .ai-header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .ai-header-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 700;
            color: #f8fafc;
        }

        .ai-scope-pills {
            display: flex;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            padding: 2px;
            gap: 2px;
        }

        .ai-scope-btn {
            flex: 1;
            padding: 5px 8px;
            font-size: 11.5px;
            font-weight: 600;
            border-radius: 6px;
            background: transparent;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            transition: all 0.15s;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        .ai-scope-btn.active {
            background: #6366f1;
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(99, 102, 241, 0.4);
        }

        .ai-messages {
            flex: 1;
            padding: 16px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .ai-msg {
            display: flex;
            flex-direction: column;
            max-width: 90%;
            font-size: 13px;
            line-height: 1.5;
            animation: fadeIn 0.2s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .ai-msg.user {
            align-self: flex-end;
            background: #4f46e5;
            color: #ffffff;
            padding: 10px 14px;
            border-radius: 14px 14px 2px 14px;
        }

        .ai-msg.assistant {
            align-self: flex-start;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #e2e8f0;
            padding: 12px 14px;
            border-radius: 14px 14px 14px 2px;
        }

        .ai-msg.assistant p { margin-bottom: 8px; }
        .ai-msg.assistant p:last-child { margin-bottom: 0; }
        .ai-msg.assistant ul, .ai-msg.assistant ol { margin-left: 18px; margin-bottom: 8px; }
        .ai-msg.assistant code { background: rgba(0,0,0,0.3); padding: 2px 5px; border-radius: 4px; font-family: monospace; font-size: 11.5px; }

        .ai-chips-container {
            padding: 8px 16px;
            display: flex;
            gap: 6px;
            overflow-x: auto;
            scrollbar-width: none;
            border-top: 1px solid rgba(255,255,255,0.06);
        }

        .ai-chips-container::-webkit-scrollbar { display: none; }

        .ai-chip {
            white-space: nowrap;
            font-size: 11.5px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            color: #cbd5e1;
            padding: 5px 10px;
            border-radius: 14px;
            cursor: pointer;
            transition: all 0.15s;
        }

        .ai-chip:hover {
            background: rgba(99, 102, 241, 0.25);
            color: #ffffff;
            border-color: #6366f1;
        }

        .ai-input-area {
            padding: 12px 16px 16px 16px;
            border-top: 1px solid rgba(255,255,255,0.08);
            background: rgba(15, 17, 20, 0.98);
        }

        .ai-input-box {
            display: flex;
            align-items: center;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 20px;
            padding: 4px 6px 4px 14px;
            transition: border-color 0.2s;
        }

        .ai-input-box:focus-within {
            border-color: #6366f1;
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.3);
        }

        .ai-input-box input {
            flex: 1;
            background: transparent;
            border: none;
            outline: none;
            color: #ffffff;
            font-size: 13px;
        }

        .ai-send-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #6366f1;
            border: none;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform 0.15s;
        }

        .ai-send-btn:hover:not(:disabled) {
            transform: scale(1.08);
            background: #4f46e5;
        }

        .ai-send-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .typing-indicator {
            display: flex;
            gap: 4px;
            padding: 4px 6px;
        }

        .typing-dot {
            width: 6px;
            height: 6px;
            background: #a5b4fc;
            border-radius: 50%;
            animation: typingBounce 1.2s infinite ease-in-out;
        }

        .typing-dot:nth-child(1) { animation-delay: 0s; }
        .typing-dot:nth-child(2) { animation-delay: 0.2s; }
        .typing-dot:nth-child(3) { animation-delay: 0.4s; }

        @keyframes typingBounce {
            0%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-6px); }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="flip-header">
        <div class="flip-brand">
            <a href="{{ route('admin.e-modul.index') }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                Kembali
            </a>
            <span style="opacity:0.3; margin:0 4px;">|</span>
            <span style="font-size:12px; color:#94a3b8;">📖 FLIPHTML5 Reader</span>
        </div>

        <div class="flip-title" title="{{ $e_modul->title }}">
            {{ $e_modul->title }}
        </div>

        <div class="flip-header-actions">
            <!-- AI Toggle Header Button -->
            <button class="ctrl-btn" id="btnToggleAiHeader" title="Tanya AI Asisten Modul" style="color:#38bdf8; background:rgba(56, 189, 248, 0.12); width:auto; padding:0 10px; gap:6px; font-size:12px; font-weight:600;">
                <span>✨</span> Tanya AI
            </button>

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
            <button class="ctrl-btn" id="btnToggleSound" title="Suara Balik Halaman (Mati)">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"/></svg>
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

    <!-- Floating AI Trigger Button (FlipHTML5 blue bubble style) -->
    <button class="ai-floating-btn" id="btnFloatingAi" title="Buka AI Asisten Modul">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        <span>Tanya AI Modul</span>
    </button>

    <!-- AI Assistant Sidebar Drawer -->
    <aside class="ai-drawer" id="aiDrawer">
        <!-- AI Header -->
        <div class="ai-header">
            <div class="ai-header-top">
                <div class="ai-header-title">
                    <span style="font-size:16px;">✨</span> AI Asisten Modul
                </div>
                <button type="button" id="btnCloseAiDrawer" style="background:none; border:none; color:#94a3b8; font-size:18px; cursor:pointer; padding:4px 8px; line-height:1;">✕</button>
            </div>

            <!-- Context Scope Selector -->
            <div class="ai-scope-pills">
                <button type="button" id="scopeActiveBtn" onclick="setAiScope('active')" class="ai-scope-btn active">
                    📍 <span id="scopeActiveLabel">Halaman Aktif</span>
                </button>
                <button type="button" id="scopeGlobalBtn" onclick="setAiScope('global')" class="ai-scope-btn">
                    🌐 Seluruh Modul (Global)
                </button>
            </div>
        </div>

        <!-- Messages Chat Area -->
        <div class="ai-messages" id="aiMessagesContainer">
            <div class="ai-msg assistant">
                <p>Halo! Saya adalah <strong>AI Asisten Modul</strong> untuk buku <em>{{ $e_modul->title }}</em>.</p>
                <p>Anda bisa bertanya materi pada <strong>halaman saat ini</strong>, meminta pencarian di <strong>halaman lain (misal: hal 55)</strong>, atau menanyakan <strong>seluruh isi modul secara global</strong>!</p>
            </div>
        </div>

        <!-- Suggestion Chips -->
        <div class="ai-chips-container">
            <button type="button" class="ai-chip" onclick="askQuickPrompt('Jelaskan rangkuman materi pada halaman yang sedang dibuka ini')">
                💡 Rangkum Halaman Ini
            </button>
            <button type="button" class="ai-chip" onclick="askQuickPrompt('Jelaskan garis besar dan pokok bahasan dari seluruh modul ini secara global')">
                🌐 Rangkuman Seluruh Modul
            </button>
            <button type="button" class="ai-chip" onclick="askQuickPrompt('Buatkan 3 pertanyaan kuis pilihan ganda beserta kunci jawabannya dari materi ini')">
                ❓ Buat 3 Soal Kuis
            </button>
            <button type="button" class="ai-chip" onclick="askQuickPrompt('Jelaskan konsep dan istilah penting yang terdapat pada materi ini')">
                📝 Konsep Penting
            </button>
        </div>

        <!-- Input Area -->
        <div class="ai-input-area">
            <form id="aiChatForm" onsubmit="handleSendAiMessage(event)">
                <div class="ai-input-box">
                    <input type="text" id="aiInputText" placeholder="Tanya halaman ini, halaman 55, atau topik apa saja..." autocomplete="off">
                    <button type="submit" id="aiSubmitBtn" class="ai-send-btn" title="Kirim Pertanyaan">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    </button>
                </div>
            </form>
        </div>
    </aside>

    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        const PDF_URL = @json($e_modul->pdf_url);
        const MODUL_ID = @json($e_modul->id);
        const MODUL_TITLE = @json($e_modul->title);
        const ASK_AI_URL = "{{ route('admin.e-modul.ask-ai', $e_modul->id) }}";
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        let pageFlip = null;
        let pdfDoc = null;
        let totalPages = 0;
        let soundEnabled = false;
        let currentZoom = 1;
        let pageTexts = {}; // Store extracted page text per page number
        let chatHistory = [];
        let currentScope = 'active'; // 'active' or 'global'

        function setAiScope(scope) {
            currentScope = scope;
            document.getElementById('scopeActiveBtn').classList.toggle('active', scope === 'active');
            document.getElementById('scopeGlobalBtn').classList.toggle('active', scope === 'global');
        }

        // Realistic Page Flip Sound Synthesizer
        function playFlipSound() {
            if (!soundEnabled) return;
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
                const res = await fetch(PDF_URL);
                if (!res.ok) throw new Error("Gagal mengunduh PDF status: " + res.status);
                const pdfData = await res.arrayBuffer();

                const loadingTask = pdfjsLib.getDocument({ data: pdfData });
                pdfDoc = await loadingTask.promise;
                totalPages = pdfDoc.numPages;

                const firstPage = await pdfDoc.getPage(1);
                const viewport = firstPage.getViewport({ scale: 1.0 });
                const pageWidth = Math.min(550, window.innerWidth * 0.45);
                const pageHeight = pageWidth * (viewport.height / viewport.width);

                flipbookEl.innerHTML = '';
                thumbsDrawer.innerHTML = '';

                for (let i = 1; i <= totalPages; i++) {
                    loadingText.textContent = `Merender halaman ${i} dari ${totalPages}...`;
                    const page = await pdfDoc.getPage(i);
                    const scale = 2.0;
                    const pageViewport = page.getViewport({ scale: scale });

                    // Asynchronously extract page text for AI Context
                    page.getTextContent().then(textContent => {
                        const str = textContent.items.map(item => item.str).join(' ');
                        pageTexts[i] = str;
                    }).catch(e => {});

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
                    
                    const thumbCanvas = document.createElement('canvas');
                    thumbCanvas.width = canvas.width;
                    thumbCanvas.height = canvas.height;
                    thumbCanvas.getContext('2d').drawImage(canvas, 0, 0);

                    const thumbLabel = document.createElement('div');
                    thumbLabel.className = 'thumb-page-num';
                    thumbLabel.textContent = i;

                    thumbItem.appendChild(thumbCanvas);
                    thumbItem.appendChild(thumbLabel);

                    thumbItem.addEventListener('click', () => {
                        if (pageFlip) pageFlip.flip(i - 1);
                    });

                    thumbsDrawer.appendChild(thumbItem);
                }

                loadingOverlay.style.display = 'none';
                flipbookEl.style.display = 'block';

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
                loadingText.textContent = "Gagal memuat PDF: " + err.message;
            }
        }

        function getActivePageLabel() {
            if (!pageFlip) return 'Hal 1';
            const pageIndex = pageFlip.getCurrentPageIndex();
            const pageNum = pageIndex + 1;
            if (pageFlip.getOrientation() === 'landscape' && pageNum > 1 && pageNum < totalPages) {
                return `Hal ${pageNum}-${pageNum + 1}`;
            }
            return `Hal ${pageNum}`;
        }

        function getActivePagesOnlyText() {
            if (!pageFlip) return '';
            const pageIndex = pageFlip.getCurrentPageIndex();
            const pageNum = pageIndex + 1;
            let combined = '';

            if (pageFlip.getOrientation() === 'landscape' && pageNum > 1 && pageNum < totalPages) {
                combined = `[Halaman ${pageNum}]: ` + (pageTexts[pageNum] || '') + "\n\n" +
                           `[Halaman ${pageNum + 1}]: ` + (pageTexts[pageNum + 1] || '');
            } else {
                combined = `[Halaman ${pageNum}]: ` + (pageTexts[pageNum] || '');
            }
            return combined;
        }

        // Smart Context Search: can search across entire module or specific page number mentioned in prompt
        function getSmartContextData(question) {
            // 1. Check if user explicitly mentioned a page number (e.g. "halaman 55", "hal 12", "page 30")
            const pageMatch = question.match(/(?:halaman|hal|page)\s*(\d+)/i);
            if (pageMatch && pageMatch[1]) {
                const targetPage = parseInt(pageMatch[1], 10);
                if (pageTexts[targetPage]) {
                    return {
                        label: `Halaman ${targetPage}`,
                        text: `[MATERI HALAMAN ${targetPage}]:\n` + pageTexts[targetPage]
                    };
                }
            }

            // 2. If Global Scope selected OR general query:
            if (currentScope === 'global') {
                return getGlobalContext(question);
            }

            // 3. Active Page Scope with automatic relevant keyword booster
            const activeLabel = getActivePageLabel();
            const activeText = getActivePagesOnlyText();

            // Extract keywords to find if relevant terms exist elsewhere in the module
            const relevantExtra = searchModulePages(question, 2, [pageFlip ? pageFlip.getCurrentPageIndex() + 1 : 1]);
            
            let combined = `[MATERI HALAMAN AKTIF (${activeLabel})]:\n` + activeText;
            if (relevantExtra) {
                combined += "\n\n[BAGIAN LAIN DALAM MODUL YANG TERKAIT]:\n" + relevantExtra;
            }

            return {
                label: activeLabel,
                text: combined
            };
        }

        function getGlobalContext(question) {
            // Search across all pages + include table of contents (pages 1-3)
            let tocText = '';
            for (let i = 1; i <= Math.min(3, totalPages); i++) {
                if (pageTexts[i]) tocText += `[Halaman ${i}]:\n` + pageTexts[i].substring(0, 800) + "\n\n";
            }

            const searchResults = searchModulePages(question, 4);
            let combined = `[RINGKASAN & DAFTAR ISI MODUL]:\n` + tocText;
            if (searchResults) {
                combined += `\n[BAGIAN-BAGIAN UTAMA TERKAIT TOPIK]:\n` + searchResults;
            }

            return {
                label: `Seluruh Modul (${totalPages} Halaman)`,
                text: combined
            };
        }

        function searchModulePages(query, maxPages = 3, excludePages = []) {
            const words = query.toLowerCase()
                .replace(/[^a-zA-Z0-9\s]/g, '')
                .split(/\s+/)
                .filter(w => w.length >= 3 && !['apa', 'yang', 'dan', 'di', 'pada', 'ini', 'itu', 'saya', 'bisa', 'tolong', 'jelaskan', 'halaman', 'modul'].includes(w));

            if (words.length === 0) return '';

            const scoredPages = [];
            for (let p = 1; p <= totalPages; p++) {
                if (excludePages.includes(p)) continue;
                const text = (pageTexts[p] || '').toLowerCase();
                if (!text) continue;

                let score = 0;
                words.forEach(w => {
                    const count = (text.match(new RegExp(w, 'g')) || []).length;
                    score += count;
                });

                if (score > 0) {
                    scoredPages.push({ page: p, score: score, text: pageTexts[p] });
                }
            }

            scoredPages.sort((a, b) => b.score - a.score);
            const top = scoredPages.slice(0, maxPages);
            if (top.length === 0) return '';

            return top.map(item => `[Halaman ${item.page}]:\n` + item.text.substring(0, 1200)).join("\n\n");
        }

        function updateIndicator(pageIndex) {
            const pageNum = pageIndex + 1;
            const slider = document.getElementById('pageSlider');
            const indicator = document.getElementById('pageIndicator');
            const activePageLabel = getActivePageLabel();
            
            slider.value = pageNum;

            if (pageFlip && pageFlip.getOrientation() === 'landscape' && pageNum > 1 && pageNum < totalPages) {
                indicator.textContent = `${pageNum}-${pageNum + 1} / ${totalPages}`;
            } else {
                indicator.textContent = `${pageNum} / ${totalPages}`;
            }

            document.getElementById('scopeActiveLabel').textContent = activePageLabel;

            document.querySelectorAll('.thumb-item').forEach(th => {
                const p = parseInt(th.getAttribute('data-page'), 10);
                th.classList.toggle('active', p === pageNum || (pageNum > 1 && p === pageNum + 1));
            });

            document.getElementById('btnPrevPage').disabled = (pageIndex === 0);
            document.getElementById('btnBottomPrev').disabled = (pageIndex === 0);
            document.getElementById('btnFirstPage').disabled = (pageIndex === 0);

            document.getElementById('btnNextPage').disabled = (pageIndex >= totalPages - 1);
            document.getElementById('btnBottomNext').disabled = (pageIndex >= totalPages - 1);
            document.getElementById('btnLastPage').disabled = (pageIndex >= totalPages - 1);
        }

        // AI Drawer Controls
        const aiDrawer = document.getElementById('aiDrawer');
        const btnFloatingAi = document.getElementById('btnFloatingAi');
        const btnToggleAiHeader = document.getElementById('btnToggleAiHeader');
        const btnCloseAiDrawer = document.getElementById('btnCloseAiDrawer');

        function toggleAiDrawer() {
            aiDrawer.classList.toggle('open');
            if (aiDrawer.classList.contains('open')) {
                document.getElementById('aiInputText').focus();
            }
        }

        btnFloatingAi.addEventListener('click', toggleAiDrawer);
        btnToggleAiHeader.addEventListener('click', toggleAiDrawer);
        btnCloseAiDrawer.addEventListener('click', () => aiDrawer.classList.remove('open'));

        function appendMessage(role, text) {
            const container = document.getElementById('aiMessagesContainer');
            const msgDiv = document.createElement('div');
            msgDiv.className = `ai-msg ${role}`;

            if (role === 'assistant') {
                if (typeof marked !== 'undefined') {
                    msgDiv.innerHTML = marked.parse(text);
                } else {
                    msgDiv.textContent = text;
                }
            } else {
                msgDiv.textContent = text;
            }

            container.appendChild(msgDiv);
            container.scrollTop = container.scrollHeight;
            return msgDiv;
        }

        function askQuickPrompt(promptText) {
            if (!aiDrawer.classList.contains('open')) {
                aiDrawer.classList.add('open');
            }
            document.getElementById('aiInputText').value = promptText;
            document.getElementById('aiChatForm').dispatchEvent(new Event('submit'));
        }

        async function handleSendAiMessage(e) {
            e.preventDefault();
            const input = document.getElementById('aiInputText');
            const submitBtn = document.getElementById('aiSubmitBtn');
            const question = input.value.trim();
            if (!question) return;

            // Append User Message
            appendMessage('user', question);
            input.value = '';
            submitBtn.disabled = true;

            const contextData = getSmartContextData(question);

            // Append Typing indicator
            const container = document.getElementById('aiMessagesContainer');
            const typingDiv = document.createElement('div');
            typingDiv.className = 'ai-msg assistant';
            typingDiv.innerHTML = '<div class="typing-indicator"><div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div></div>';
            container.appendChild(typingDiv);
            container.scrollTop = container.scrollHeight;

            try {
                const response = await fetch(ASK_AI_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        question: question,
                        modul_title: MODUL_TITLE,
                        page_number: contextData.label,
                        page_text: contextData.text,
                        history: chatHistory.slice(-6)
                    })
                });

                const data = await response.json();
                typingDiv.remove();

                if (data.success && data.reply) {
                    appendMessage('assistant', data.reply);
                    chatHistory.push({ role: 'user', content: question });
                    chatHistory.push({ role: 'assistant', content: data.reply });
                } else {
                    appendMessage('assistant', data.fallback_reply || data.message || 'Gagal memproses jawaban AI.');
                }
            } catch (err) {
                typingDiv.remove();
                appendMessage('assistant', '⚠️ Terjadi kesalahan jaringan saat menghubungi AI: ' + err.message);
            } finally {
                submitBtn.disabled = false;
            }
        }

        // Toolbar Events
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

        document.getElementById('btnZoomIn').addEventListener('click', () => {
            currentZoom = Math.min(currentZoom + 0.15, 1.6);
            document.getElementById('flipWrapper').style.transform = `scale(${currentZoom})`;
        });

        document.getElementById('btnZoomOut').addEventListener('click', () => {
            currentZoom = Math.max(currentZoom - 0.15, 0.7);
            document.getElementById('flipWrapper').style.transform = `scale(${currentZoom})`;
        });

        document.getElementById('btnToggleThumbs').addEventListener('click', function() {
            const drawer = document.getElementById('thumbsDrawer');
            drawer.classList.toggle('open');
            this.classList.toggle('active', drawer.classList.contains('open'));
        });

        document.getElementById('btnToggleSound').addEventListener('click', function() {
            soundEnabled = !soundEnabled;
            this.classList.toggle('active', soundEnabled);
        });

        document.getElementById('btnFullscreen').addEventListener('click', () => {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => alert(err.message));
            } else {
                document.exitFullscreen();
            }
        });

        window.addEventListener('DOMContentLoaded', initFlipbook);
    </script>
</body>
</html>
