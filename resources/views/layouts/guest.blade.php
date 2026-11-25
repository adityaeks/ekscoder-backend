<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'EKSCODER') }} // Admin Portal</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('ekscoder.png') }}">
        <link rel="shortcut icon" type="image/png" href="{{ asset('ekscoder-logo.png') }}">

        <!-- Google Fonts: Inter & JetBrains Mono -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

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
                --accent-soft:  rgba(255, 255, 255, 0.10);
                --rose:         #f43f5e;
            }

            a, button, input[type="submit"], label[for] { cursor: pointer; }
            :focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }

            html, body {
                min-height: 100dvh;
                margin: 0;
                padding: 0;
                overflow-x: hidden;
            }

            body {
                font-family: 'Inter', system-ui, -apple-system, sans-serif;
                background-color: var(--bg-base);
                color: var(--text-primary);
                min-height: 100dvh;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                position: relative;
                padding: 20px 0;
            }

            .auth-container {
                position: relative;
                z-index: 10;
                width: 100%;
                max-width: 420px;
                padding: 16px;
            }

            .auth-logo-header {
                text-align: center;
                margin-bottom: 22px;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 6px;
            }

            .auth-card {
                background: var(--bg-surface);
                border: 1px solid var(--border);
                border-radius: 14px;
                padding: 28px 24px;
                transition: border-color 0.2s ease;
            }

            .auth-card:hover {
                border-color: var(--border-light);
            }

            .auth-title {
                font-size: 20px;
                font-weight: 800;
                color: var(--text-primary);
                letter-spacing: -0.03em;
                margin-bottom: 6px;
            }

            .auth-subtitle {
                font-size: 13px;
                color: var(--text-secondary);
                margin-bottom: 22px;
                line-height: 1.5;
            }

            .auth-footer-note {
                text-align: center;
                margin-top: 24px;
                font-family: 'JetBrains Mono', monospace;
                font-size: 11px;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                color: var(--text-muted);
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }
        </style>
    </head>
    <body>
        <div class="auth-container">
            <!-- Logo Header -->
            <div class="auth-logo-header">
                <x-application-logo />
            </div>

            <!-- Auth Card -->
            <div class="auth-card">
                {{ $slot }}
            </div>

            <!-- <div class="auth-footer-note">
                <span>SURABAYA, ID</span>
                <span>&bull;</span>
                <span>EKSCODER STUDIO</span>
            </div> -->
        </div>
    </body>
</html>
