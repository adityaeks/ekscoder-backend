<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Ekscoder') }} — Admin Login</title>

        <!-- Google Fonts: Inter & JetBrains Mono -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

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
                --rose:         #f43f5e;
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
            }

            /* Ambient Background Glows */
            .bg-glow-1 {
                position: absolute;
                top: -150px;
                left: 50%;
                transform: translateX(-50%);
                width: 600px;
                height: 600px;
                background: radial-gradient(circle, rgba(184, 255, 0, 0.07) 0%, rgba(10, 10, 15, 0) 70%);
                pointer-events: none;
                z-index: 0;
            }

            .bg-glow-2 {
                position: absolute;
                bottom: -200px;
                right: -100px;
                width: 500px;
                height: 500px;
                background: radial-gradient(circle, rgba(108, 99, 255, 0.05) 0%, rgba(10, 10, 15, 0) 70%);
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

            .auth-container {
                position: relative;
                z-index: 10;
                width: 100%;
                max-width: 420px;
                padding: 16px;
            }

            .auth-logo-header {
                text-align: center;
                margin-bottom: 20px;
            }

            .auth-card {
                background: var(--bg-surface);
                border: 1px solid var(--border);
                border-radius: 20px;
                padding: 26px 24px;
                box-shadow: 0 24px 48px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.03);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                transition: border-color 0.25s ease;
            }

            .auth-card:hover {
                border-color: var(--border-light);
            }

            .auth-title {
                font-size: 20px;
                font-weight: 800;
                color: var(--text-primary);
                letter-spacing: -0.4px;
                margin-bottom: 6px;
            }

            .auth-subtitle {
                font-size: 13px;
                color: var(--text-muted);
                margin-bottom: 24px;
                line-height: 1.5;
            }

            .auth-footer-note {
                text-align: center;
                margin-top: 28px;
                font-size: 12px;
                color: var(--text-muted);
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }

            .auth-footer-dot {
                width: 6px;
                height: 6px;
                border-radius: 50%;
                background: #22c55e;
                box-shadow: 0 0 8px #22c55e;
            }
        </style>
    </head>
    <body>
        <!-- Background Decorative Elements -->
        <div class="grid-pattern"></div>
        <div class="bg-glow-1"></div>
        <div class="bg-glow-2"></div>

        <div class="auth-container">
            <!-- Logo Header -->
            <div class="auth-logo-header">
                <x-application-logo />
            </div>

            <!-- Auth Card -->
            <div class="auth-card">
                {{ $slot }}
            </div>

            <!-- Footer Info -->
            <!-- <div class="auth-footer-note">
                <span class="auth-footer-dot"></span>
                <span>Ekscoder Security Console &bull; SSL Encrypted</span>
            </div> -->
        </div>
    </body>
</html>
