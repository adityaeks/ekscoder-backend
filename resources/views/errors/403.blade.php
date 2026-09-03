<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>403 — Akses Ditolak | {{ config('app.name', 'Ekscoder') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('ekscoder.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('ekscoder-logo.png') }}">

    <!-- Google Fonts: Inter & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

    <!-- Vite Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-base:        #0a0a0f;
            --bg-surface:     #111118;
            --bg-elevated:    #16161f;
            --border:         rgba(255, 255, 255, 0.08);
            --border-light:   rgba(255, 255, 255, 0.16);
            --text-primary:   #f8fafc;
            --text-secondary: #94a3b8;
            --rose:           #f43f5e;
            --rose-glow:      rgba(244, 63, 94, 0.25);
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
            padding: 24px;
        }

        /* Ambient Glows */
        .bg-glow-rose {
            position: absolute;
            top: -120px;
            left: 50%;
            transform: translateX(-50%);
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(244, 63, 94, 0.12) 0%, rgba(10, 10, 15, 0) 70%);
            pointer-events: none;
            z-index: 0;
        }

        .grid-pattern {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.04) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
            z-index: 0;
            opacity: 0.7;
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
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7), 0 0 0 1px rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(16px);
            position: relative;
            overflow: hidden;
            transition: border-color 0.25s ease;
        }

        .error-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--rose), transparent);
        }

        .error-card:hover {
            border-color: rgba(244, 63, 94, 0.3);
        }

        /* Lock Icon */
        .icon-shield {
            width: 72px;
            height: 72px;
            border-radius: 20px;
            background: rgba(244, 63, 94, 0.12);
            border: 1px solid rgba(244, 63, 94, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px auto;
            color: var(--rose);
            box-shadow: 0 8px 24px var(--rose-glow);
        }

        .error-code-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: rgba(244, 63, 94, 0.1);
            border: 1px solid rgba(244, 63, 94, 0.25);
            border-radius: 9999px;
            color: var(--rose);
            font-family: 'JetBrains Mono', monospace;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 18px;
        }

        .error-code-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--rose);
            box-shadow: 0 0 8px var(--rose);
            animation: pulse-dot 2s infinite ease-in-out;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.85); }
        }

        .error-title {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.5px;
            margin-bottom: 10px;
        }

        .error-subtitle {
            font-size: 14px;
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 28px;
        }

        /* Action Button */
        .btn-back {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 28px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            background: var(--bg-elevated);
            color: #f8fafc;
            border: 1px solid var(--border-light);
            text-decoration: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.3);
            color: #ffffff;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <!-- Ambient Background Elements -->
    <div class="grid-pattern"></div>
    <div class="bg-glow-rose"></div>

    <div class="error-container">
        <!-- Logo Header -->
        <div class="logo-wrapper">
            <a href="{{ url('/') }}" title="Ekscoder Home">
                <x-application-logo />
            </a>
        </div>

        <!-- 403 Card -->
        <div class="error-card">
            <!-- Lock Icon -->
            <div class="icon-shield">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    <circle cx="12" cy="16" r="1"/>
                </svg>
            </div>

            <!-- Code Badge -->
            <div class="error-code-badge">
                <span class="error-code-dot"></span>
                <span>Error 403 &bull; Akses Ditolak</span>
            </div>

            <h1 class="error-title">Tidak Memiliki Izin Akses</h1>
            <p class="error-subtitle">
                Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.
            </p>

            <!-- Action Button -->
            <div>
                <button type="button" onclick="window.history.length > 1 ? window.history.back() : window.location.href='{{ route('dashboard') }}'" class="btn-back">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
                    </svg>
                    <span>Kembali</span>
                </button>
            </div>
        </div>
    </div>
</body>
</html>
