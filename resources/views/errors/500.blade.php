<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>500 — Terjadi Kesalahan Server | {{ config('app.name', 'Ekscoder') }}</title>

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
            --bg-base:        #0a0a0f;
            --bg-surface:     #111118;
            --bg-elevated:    #16161f;
            --bg-hover:       #1c1c28;
            --border:         rgba(255, 255, 255, 0.08);
            --border-light:   rgba(255, 255, 255, 0.14);
            --text-primary:   #f8fafc;
            --text-secondary: #94a3b8;
            --text-muted:     #64748b;
            --amber:          #f59e0b;
            --amber-glow:     rgba(245, 158, 11, 0.25);
            --accent:         #6366f1;
        }

        html, body {
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-base);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
            padding: 32px 20px;
        }

        .bg-glow-amber {
            position: absolute;
            top: -120px;
            left: 50%;
            transform: translateX(-50%);
            width: 650px;
            height: 650px;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.12) 0%, rgba(10, 10, 15, 0) 70%);
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
            max-width: 520px;
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
            padding: 42px 36px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7), 0 0 0 1px rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(16px);
            position: relative;
            overflow: hidden;
        }

        .error-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--amber), transparent);
        }

        .icon-alert {
            width: 76px;
            height: 76px;
            border-radius: 20px;
            background: rgba(245, 158, 11, 0.12);
            border: 1px solid rgba(245, 158, 11, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px auto;
            color: var(--amber);
            box-shadow: 0 8px 24px var(--amber-glow);
        }

        .error-code-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.25);
            border-radius: 9999px;
            color: var(--amber);
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
            background: var(--amber);
            box-shadow: 0 0 8px var(--amber);
        }

        .error-title {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.5px;
            margin-bottom: 12px;
        }

        .error-subtitle {
            font-size: 14px;
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 28px;
        }

        .action-buttons {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-secondary {
            background: var(--bg-elevated);
            color: var(--text-primary);
            border: 1px solid var(--border);
        }

        .btn-secondary:hover {
            background: var(--bg-hover);
            color: #ffffff;
        }

        .btn-primary {
            background: #6366f1;
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
        }

        .btn-primary:hover {
            background: #4f46e5;
        }
    </style>
</head>
<body>
    <div class="grid-pattern"></div>
    <div class="bg-glow-amber"></div>

    <div class="error-container">
        <div class="logo-wrapper">
            <a href="{{ url('/') }}" title="Ekscoder Home">
                <x-application-logo />
            </a>
        </div>

        <div class="error-card">
            <div class="icon-alert">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>

            <div class="error-code-badge">
                <span class="error-code-dot"></span>
                <span>Error 500 &bull; Server Error</span>
            </div>

            <h1 class="error-title">Terjadi Kesalahan Server</h1>
            <p class="error-subtitle">
                Maaf, sistem mengalami kendala teknis tak terduga saat memproses permintaan Anda. Tim kami sedang menindaklanjuti.
            </p>

            <div class="action-buttons">
                <button type="button" onclick="window.location.reload()" class="btn-action btn-secondary">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M23 4v6h-6"/><path d="M1 20v-6h6"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
                    </svg>
                    <span>Muat Ulang Halaman</span>
                </button>

                <a href="{{ route('dashboard') }}" class="btn-action btn-primary">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                        <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                    </svg>
                    <span>Ke Dashboard</span>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
