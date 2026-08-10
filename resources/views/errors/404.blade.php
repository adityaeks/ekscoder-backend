<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>404 — Halaman Tidak Ditemukan | {{ config('app.name', 'Ekscoder') }}</title>

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
            --bg-base:      #0a0a0f;
            --bg-surface:   #111118;
            --bg-elevated:  #16161f;
            --bg-hover:     #1c1c28;
            --border:       rgba(255, 255, 255, 0.08);
            --border-light: rgba(255, 255, 255, 0.14);
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --text-muted:   #64748b;
            --accent:       #b8ff00;
            --accent-glow:  rgba(184, 255, 0, 0.25);
            --accent-soft:  rgba(184, 255, 0, 0.12);
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

        /* Ambient Background Glows */
        .bg-glow-1 {
            position: absolute;
            top: -150px;
            left: 50%;
            transform: translateX(-50%);
            width: 700px;
            height: 700px;
            background: radial-gradient(circle, rgba(184, 255, 0, 0.08) 0%, rgba(10, 10, 15, 0) 70%);
            pointer-events: none;
            z-index: 0;
        }

        .bg-glow-2 {
            position: absolute;
            bottom: -200px;
            right: -100px;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(184, 255, 0, 0.04) 0%, rgba(10, 10, 15, 0) 70%);
            pointer-events: none;
            z-index: 0;
        }

        .grid-pattern {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 32px 32px;
            pointer-events: none;
            z-index: 0;
            opacity: 0.6;
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
            background: rgba(184, 255, 0, 0.08);
            border: 1px solid rgba(184, 255, 0, 0.2);
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
    <div class="grid-pattern"></div>
    <div class="bg-glow-1"></div>
    <div class="bg-glow-2"></div>

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
            <p class="error-subtitle">
                Maaf, halaman yang Anda tuju tidak ditemukan, telah dipindahkan, atau akses fitur tersebut telah nonaktif.
            </p>

        </div>
    </div>
</body>
</html>
