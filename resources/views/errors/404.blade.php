<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>404 // Halaman Tidak Ditemukan | {{ config('app.name', 'EKSCODER') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('ekscoder.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('ekscoder-logo.png') }}">

    <!-- Google Fonts: Inter & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;600;700;800&display=swap" rel="stylesheet">

    <!-- Vite Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-base:      #08080A;
            --bg-surface:   #111116;
            --bg-elevated:  #14141A;
            --bg-hover:     #1B1B22;
            --border:       #1F1F27;
            --border-light: #2A2A36;
            --text-primary: #F4F4F6;
            --text-secondary: #A1A1AE;
            --text-muted:   #7C7C8A;
            --accent:       #FFFFFF;
            --accent-glow:  rgba(255, 255, 255, 0.14);
            --accent-soft:  rgba(255, 255, 255, 0.08);
        }

        html, body {
            height: 100vh;
            max-height: 100vh;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-base);
            color: var(--text-primary);
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            padding: 24px;
        }

        .error-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 480px;
            text-align: center;
        }

        .logo-wrapper {
            margin-bottom: 24px;
            display: inline-block;
        }

        .error-card {
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 40px 32px;
            box-shadow: 0 24px 48px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            transition: border-color 0.25s ease;
        }

        .error-card:hover {
            border-color: var(--border-light);
        }

        /* 404 Badge & Display */
        .error-code-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 9999px;
            color: var(--accent);
            font-family: 'JetBrains Mono', monospace;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 24px;
        }

        .error-code-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--accent);
            box-shadow: 0 0 8px var(--accent);
            animation: pulse-dot 2s infinite ease-in-out;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.85); }
        }

        .error-title {
            font-size: 22px;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.4px;
            margin-bottom: 10px;
        }

        .error-subtitle {
            font-size: 13.5px;
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 0;
        }

        
    </style>
</head>
<body>
    <!-- Background Elements -->
    <div class="error-container">
        <!-- Logo Header -->
        <div class="logo-wrapper">
            <a href="{{ url('/') }}" title="Ekscoder Home">
                <x-application-logo />
            </a>
        </div>

        <!-- 404 Card -->
        <div class="error-card">
            <div class="error-code-badge">
                <span class="error-code-dot"></span>
                <span>Error 404 &bull; Page Not Found</span>
            </div>

            <h1 class="error-title">Halaman Tidak Ditemukan</h1>
            <p class="error-subtitle" style="margin-bottom:24px;">
                Maaf, halaman yang Anda tuju tidak ditemukan, telah dipindahkan, atau alamat URL yang dimasukkan salah.
            </p>

            <!-- Navigation Actions -->
            <div style="display:flex; align-items:center; justify-content:center; gap:12px; flex-wrap:wrap;">
                <button type="button" onclick="window.history.length > 1 ? window.history.back() : window.location.href='{{ url('/') }}'" style="display:inline-flex; align-items:center; gap:8px; padding:10px 20px; border-radius:10px; font-size:13.5px; font-weight:600; text-decoration:none; cursor:pointer; background:var(--bg-elevated); color:var(--text-primary); border:1px solid var(--border); transition:all 0.2s;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
                    </svg>
                    <span>Kembali</span>
                </button>

                <a href="{{ url('/') }}" style="display:inline-flex; align-items:center; gap:8px; padding:10px 20px; border-radius:10px; font-size:13.5px; font-weight:700; text-decoration:none; cursor:pointer; background:#FFFFFF; color:#08080A; border:1px solid #FFFFFF; transition:opacity 0.2s;" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    <span>Halaman Utama</span>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
