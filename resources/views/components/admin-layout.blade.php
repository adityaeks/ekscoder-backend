@props(['title' => 'Dashboard', 'breadcrumb' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }} — Ekscoder Admin</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('ekscoder-logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('ekscoder-logo.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

    <script>
        (function() {
            const savedTheme = localStorage.getItem('ekscoder_theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root, html[data-theme="dark"] {
            --bg-base:      #0a0a0f;
            --bg-surface:   #111118;
            --bg-elevated:  #16161f;
            --bg-hover:     #1c1c28;
            --border:       rgba(255,255,255,0.07);
            --border-light: rgba(255,255,255,0.12);
            --text-primary: #f0f0f5;
            --text-secondary: #8b8ba0;
            --text-muted:   #55555f;
            --accent:       #b8ff00;
            --accent-glow:  rgba(184,255,0,0.2);
            --accent-soft:  rgba(184,255,0,0.1);
            --green:        #22c55e;
            --green-soft:   rgba(34,197,94,0.12);
            --amber:        #f59e0b;
            --amber-soft:   rgba(245,158,11,0.12);
            --rose:         #f43f5e;
            --rose-soft:    rgba(244,63,94,0.12);
            --cyan:         #06b6d4;
            --cyan-soft:    rgba(6,182,212,0.12);
            --sidebar-w:    260px;
            --topbar-bg:    rgba(10,10,15,0.85);
            --accent-text:  #0a0a0f;
            --badge-accent-text: #b8ff00;
        }

        html[data-theme="light"] {
            --bg-base:      #f4f6f9;
            --bg-surface:   #ffffff;
            --bg-elevated:  #edf0f5;
            --bg-hover:     #e2e8f0;
            --border:       rgba(0,0,0,0.08);
            --border-light: rgba(0,0,0,0.15);
            --text-primary: #0f172a;
            --text-secondary: #334155;
            --text-muted:   #64748b;
            --accent:       #65a30d;
            --accent-glow:  rgba(101,163,13,0.25);
            --accent-soft:  rgba(101,163,13,0.12);
            --green:        #16a34a;
            --green-soft:   rgba(22,163,74,0.12);
            --amber:        #d97706;
            --amber-soft:   rgba(217,119,6,0.12);
            --rose:         #e11d48;
            --rose-soft:    rgba(225,29,72,0.12);
            --cyan:         #0284c7;
            --cyan-soft:    rgba(2,132,199,0.12);
            --sidebar-w:    260px;
            --topbar-bg:    rgba(255,255,255,0.88);
            --accent-text:  #ffffff;
            --badge-accent-text: #4d7c0f;
        }

        /* CUSTOM SCROLLBARS */
        * {
            scrollbar-width: thin;
            scrollbar-color: var(--border-light) transparent;
        }

        ::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
        }

        html[data-theme="light"] ::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.2);
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--accent);
        }

        ::-webkit-scrollbar-corner {
            background: transparent;
        }

        html, body { height: 100%; overflow-x: hidden; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-base);
            color: var(--text-primary);
            min-height: 100vh;
            transition: background 0.25s ease, color 0.25s ease;
            overflow-x: hidden;
        }

        .admin-layout { min-height: 100vh; width: 100%; position: relative; }

        /* SIDEBAR (ALWAYS DARK THEME) */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: #111118 !important;
            border-right: 1px solid rgba(255, 255, 255, 0.08) !important;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 50;
            transition: width 0.25s ease, transform 0.3s ease;
        }

        .sidebar-logo {
            padding: 24px 20px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .logo-mark {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .logo-icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--accent) 0%, #7ecb00 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 13px;
            color: #0a0a0f;
            box-shadow: 0 0 20px var(--accent-glow);
            flex-shrink: 0;
        }

        .logo-text { display: flex; flex-direction: column; }
        .logo-name { font-size: 14px; font-weight: 700; color: #f8fafc !important; letter-spacing: -0.3px; }
        .logo-sub  { font-size: 10px; font-weight: 500; color: #64748b !important; letter-spacing: 0.8px; text-transform: uppercase; }

        .sidebar-nav { flex: 1; padding: 16px 12px; overflow-y: auto; }

        .nav-section-label {
            font-size: 10px; font-weight: 600; text-transform: uppercase;
            letter-spacing: 1.2px; color: #64748b !important;
            padding: 4px 10px 10px; margin-top: 8px;
        }

        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 12px; border-radius: 10px;
            text-decoration: none; color: #94a3b8 !important;
            font-size: 13.5px; font-weight: 500;
            transition: all 0.2s ease; margin-bottom: 2px;
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, 0.06) !important;
            color: #f8fafc !important;
        }

        .nav-item.active {
            background: rgba(184, 255, 0, 0.15) !important;
            color: #b8ff00 !important;
        }

        .nav-icon {
            width: 32px; height: 32px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 15px; flex-shrink: 0;
        }

        .nav-item.active .nav-icon {
            color: #b8ff00 !important;
        }

        .nav-badge {
            margin-left: auto; font-size: 10px; font-weight: 700;
            padding: 2px 7px; border-radius: 20px;
            background: rgba(184, 255, 0, 0.18) !important;
            color: #b8ff00 !important;
        }

        .sidebar-footer {
            padding: 12px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .user-card {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: 10px;
            background: #181824 !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
        }

        .user-avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), #7ecb00);
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700; color: #0a0a0f; flex-shrink: 0;
        }

        .user-info { flex: 1; min-width: 0; }
        .user-name { font-size: 12.5px; font-weight: 600; color: #f8fafc !important; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-role { font-size: 10.5px; color: #64748b !important; }

        .logout-btn {
            display: flex; align-items: center; justify-content: center;
            width: 30px; height: 30px; border-radius: 7px;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            background: transparent !important;
            color: #94a3b8 !important;
            cursor: pointer;
            transition: all 0.2s; font-size: 14px; flex-shrink: 0;
        }

        .logout-btn:hover {
            background: rgba(244, 63, 94, 0.15) !important;
            color: #f43f5e !important;
            border-color: rgba(244, 63, 94, 0.3) !important;
        }

        /* MAIN CONTENT */
        .main-wrapper {
            margin-left: var(--sidebar-w);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .topbar {
            position: sticky; top: 0; z-index: 40;
            background: var(--topbar-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
            padding: 0 24px; height: 60px;
            display: flex; align-items: center; justify-content: space-between; gap: 14px;
            transition: background 0.25s ease, border-color 0.25s ease;
            width: 100%;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
            flex: 1 1 auto;
        }

        .topbar-heading {
            min-width: 0;
            flex: 1 1 auto;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
        }

        .topbar-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-primary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.25;
        }

        .topbar-breadcrumb {
            font-size: 12px;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.25;
            margin-top: 1px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .topbar-action-slot {
            display: flex;
            align-items: center;
        }

        .topbar-btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 7px 14px; border-radius: 9px;
            font-size: 12.5px; font-weight: 700;
            cursor: pointer; transition: all 0.2s;
            text-decoration: none; border: 1px solid transparent;
            font-family: 'Inter', sans-serif;
            white-space: nowrap;
        }

        .topbar-btn-primary { background: var(--accent); color: var(--accent-text); box-shadow: 0 0 20px var(--accent-glow); }
        .topbar-btn-primary:hover { opacity: 0.9; }
        .topbar-btn-ghost { background: var(--bg-elevated); color: var(--text-secondary); border-color: var(--border); }
        .topbar-btn-ghost:hover { color: var(--text-primary); border-color: var(--border-light); }

        .topbar-profile-container {
            position: relative;
            flex-shrink: 0;
        }

        .topbar-profile-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 4px 10px 4px 5px;
            background: var(--bg-elevated);
            border: 1px solid var(--border);
            border-radius: 20px;
            cursor: pointer;
            transition: background 0.2s, border-color 0.2s;
            margin-left: 2px;
            color: var(--text-primary);
            font-family: inherit;
        }

        .topbar-profile-btn:hover {
            background: var(--bg-hover);
            border-color: var(--border-light);
        }

        .topbar-user-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), #7ecb00);
            color: #0a0a0f;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 12px;
            flex-shrink: 0;
        }

        .topbar-user-meta {
            display: flex;
            flex-direction: column;
            line-height: 1.15;
            text-align: left;
            white-space: nowrap;
        }

        .topbar-user-name {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-primary);
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .topbar-user-role {
            font-size: 10px;
            color: var(--text-muted);
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .topbar-profile-chevron {
            margin-left: 2px;
            color: var(--text-muted);
            flex-shrink: 0;
            transition: transform 0.2s ease;
        }

        .topbar-profile-dropdown {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            width: 220px;
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 8px;
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.5);
            display: none;
            flex-direction: column;
            z-index: 100;
            backdrop-filter: blur(12px);
            animation: profileDropdownFade 0.15s ease-out;
        }

        .topbar-profile-dropdown.open {
            display: flex;
        }

        @keyframes profileDropdownFade {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .profile-dropdown-header {
            padding: 8px 10px;
        }

        .profile-dropdown-header .user-fullname {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .profile-dropdown-header .user-email {
            font-size: 11px;
            color: var(--text-muted);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .profile-dropdown-divider {
            height: 1px;
            background: var(--border);
            margin: 6px 0;
        }

        .profile-dropdown-item {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 8px 10px;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text-primary);
            text-decoration: none;
            transition: background 0.15s;
        }

        .profile-dropdown-item:hover {
            background: var(--bg-hover);
        }

        .profile-dropdown-item.text-danger {
            color: var(--rose);
        }

        .profile-dropdown-item.text-danger:hover {
            background: rgba(244, 63, 94, 0.12);
        }

        .theme-toggle-btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 6px 12px; border-radius: 9px;
            background: var(--bg-elevated); border: 1px solid var(--border);
            color: var(--text-secondary); font-size: 12px; font-weight: 600;
            cursor: pointer; transition: all 0.2s ease;
        }

        .theme-toggle-btn:hover {
            color: var(--text-primary); border-color: var(--border-light);
            background: var(--bg-hover);
        }

        .topbar-clock-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 5px 12px;
            border-radius: 20px;
            background: var(--accent-soft);
            border: 1px solid rgba(184, 255, 0, 0.25);
            color: var(--badge-accent-text);
            font-size: 12px;
            font-weight: 700;
        }

        .topbar-clock-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--accent);
            box-shadow: 0 0 8px var(--accent);
            animation: pulseClock 2s infinite;
        }

        @keyframes pulseClock {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.8); }
        }

        .page-content { flex: 1; padding: 28px; }

        /* Cards */
        .card { background: var(--bg-surface); border: 1px solid var(--border); border-radius: 16px; overflow: hidden; transition: background 0.25s, border-color 0.25s; }
        .card-header { padding: 18px 22px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
        .card-title { font-size: 14px; font-weight: 700; color: var(--text-primary); }
        .card-subtitle { font-size: 12px; color: var(--text-muted); margin-top: 2px; }
        .card-body { padding: 22px; }

        /* Stats */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px; }

        .stat-card {
            background: var(--bg-surface); border: 1px solid var(--border);
            border-radius: 16px; padding: 20px;
            position: relative; overflow: hidden;
            transition: border-color 0.2s, transform 0.2s, background 0.25s;
        }

        .stat-card:hover { border-color: var(--border-light); transform: translateY(-2px); }
        .stat-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; }
        .stat-card.accent::before { background: linear-gradient(90deg, var(--accent), #7ecb00); }
        .stat-card.green::before  { background: linear-gradient(90deg, var(--green), #10b981); }
        .stat-card.amber::before  { background: linear-gradient(90deg, var(--amber), #f97316); }
        .stat-card.cyan::before   { background: linear-gradient(90deg, var(--cyan), #3b82f6); }

        .stat-top { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 16px; }
        .stat-label { font-size: 11.5px; font-weight: 500; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.7px; }
        .stat-icon { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 17px; }
        .stat-icon.accent { background: var(--accent-soft); }
        .stat-icon.green  { background: var(--green-soft); }
        .stat-icon.amber  { background: var(--amber-soft); }
        .stat-icon.cyan   { background: var(--cyan-soft); }
        .stat-value { font-size: 34px; font-weight: 800; color: var(--text-primary); letter-spacing: -1.5px; line-height: 1; margin-bottom: 6px; }
        .stat-meta { font-size: 11.5px; color: var(--text-muted); }

        /* Table */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table thead tr { border-bottom: 1px solid var(--border); }
        .data-table th { padding: 11px 16px; text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.8px; color: var(--text-muted); }
        .data-table tbody tr { border-bottom: 1px solid var(--border); transition: background 0.15s; }
        .data-table tbody tr:last-child { border-bottom: none; }
        .data-table tbody tr:hover { background: var(--bg-hover); }
        .data-table td { padding: 14px 16px; font-size: 13.5px; color: var(--text-secondary); vertical-align: middle; }

        /* Badges */
        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 9px; border-radius: 20px; font-size: 11px; font-weight: 600; border: 1px solid transparent; }
        .badge-green  { background: var(--green-soft);  color: var(--green);  border-color: rgba(34,197,94,0.2); }
        .badge-rose   { background: var(--rose-soft);   color: var(--rose);   border-color: rgba(244,63,94,0.2); }
        .badge-amber  { background: var(--amber-soft);  color: var(--amber);  border-color: rgba(245,158,11,0.2); }
        .badge-accent { background: var(--accent-soft); color: var(--badge-accent-text); border-color: rgba(184,255,0,0.2); }
        .badge-dot { width: 5px; height: 5px; border-radius: 50%; background: currentColor; }

        /* Buttons */
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s; border: 1px solid transparent; text-decoration: none; font-family: 'Inter', sans-serif; }
        .btn-primary { background: var(--accent); color: var(--accent-text); font-weight: 700; }
        .btn-primary:hover { opacity: 0.9; }
        .btn-ghost { background: var(--bg-elevated); color: var(--text-secondary); border-color: var(--border); }
        .btn-ghost:hover { color: var(--text-primary); border-color: var(--border-light); background: var(--bg-hover); }
        .btn-danger { background: var(--rose-soft); color: var(--rose); border-color: rgba(244,63,94,0.2); }
        .btn-danger:hover { background: rgba(244,63,94,0.2); color: var(--rose); }

        /* Tech Pills */
        .tech-pills { display: flex; flex-wrap: wrap; gap: 4px; }
        .tech-pill { font-family: 'JetBrains Mono', monospace; font-size: 10px; font-weight: 600; padding: 2px 7px; border-radius: 5px; background: var(--bg-elevated); border: 1px solid var(--border); color: var(--text-muted); }

        /* Form Styles */
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 12px; font-weight: 600; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 7px; }
        .form-input, .form-textarea, .form-select { width: 100%; padding: 9px 13px; background: var(--bg-elevated); border: 1px solid var(--border); border-radius: 9px; color: var(--text-primary); font-size: 13.5px; font-family: 'Inter', sans-serif; transition: border-color 0.2s, box-shadow 0.2s; outline: none; }
        .form-input:focus, .form-textarea:focus, .form-select:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(184,255,0,0.15); }
        .form-input::placeholder, .form-textarea::placeholder { color: var(--text-muted); }
        .form-input:disabled { opacity: 0.5; cursor: not-allowed; }
        .form-textarea { resize: vertical; min-height: 90px; }

        /* DATE INPUT LIME CALENDAR ICON */
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(85%) sepia(90%) saturate(800%) hue-rotate(35deg) brightness(1.2);
            cursor: pointer;
            opacity: 0.9;
            transition: opacity 0.2s, transform 0.2s;
        }
        input[type="date"]::-webkit-calendar-picker-indicator:hover {
            opacity: 1;
            transform: scale(1.1);
        }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-check { display: flex; align-items: center; gap: 9px; cursor: pointer; }
        .form-check input[type="checkbox"] { width: 16px; height: 16px; accent-color: var(--accent); cursor: pointer; }
        .form-check-label { font-size: 13px; font-weight: 500; color: var(--text-secondary); }
        .form-hint { margin-top: 5px; font-size: 11px; color: var(--text-muted); }

        .error-list { background: var(--rose-soft); border: 1px solid rgba(244,63,94,0.25); border-radius: 10px; padding: 12px 16px; margin-bottom: 20px; }
        .error-list p { font-size: 12.5px; color: var(--rose); line-height: 1.6; }

        .flash { padding: 12px 16px; border-radius: 10px; font-size: 13px; font-weight: 500; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }
        .empty-state { text-align: center; padding: 50px 20px; color: var(--text-muted); }

        /* MODAL SYSTEM */
        .modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(8px);
            z-index: 99999;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow-y: auto;
        }

        .modal-backdrop.open {
            display: flex;
        }

        .modal-box {
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: 18px;
            width: 100%;
            max-width: 580px;
            max-height: min(650px, calc(100vh - 40px));
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
            margin: auto;
            transition: border-color 0.25s ease;
        }

        .modal-box form {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            flex: 1;
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
        }

        .modal-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .modal-close {
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 18px;
            cursor: pointer;
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }

        .modal-close:hover {
            background: var(--bg-hover);
            color: var(--text-primary);
        }

        .modal-body {
            padding: 20px;
            overflow-y: auto;
            flex: 1;
        }

        .modal-footer {
            padding: 14px 20px;
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            background: var(--bg-elevated);
        }

        @keyframes modalShake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-6px); }
            40%, 80% { transform: translateX(6px); }
        }

        .modal-box.modal-shake {
            animation: modalShake 0.35s ease-in-out;
            border-color: var(--rose) !important;
        }
        /* COLLAPSIBLE SIDEBAR MINI MODE */
        .topbar-collapse-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: var(--bg-elevated);
            border: 1px solid var(--border);
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .topbar-collapse-btn:hover {
            color: var(--text-primary);
            border-color: var(--border-light);
            background: var(--bg-hover);
        }

        .admin-layout.sidebar-collapsed {
            --sidebar-w: 72px;
        }

        .admin-layout.sidebar-collapsed .sidebar {
            width: 72px;
        }

        .admin-layout.sidebar-collapsed .logo-text,
        .admin-layout.sidebar-collapsed .nav-text,
        .admin-layout.sidebar-collapsed .nav-section-label,
        .admin-layout.sidebar-collapsed .user-info {
            display: none !important;
        }

        .admin-layout.sidebar-collapsed .sidebar-logo {
            padding: 16px 8px;
            justify-content: center;
        }

        .admin-layout.sidebar-collapsed .logo-mark {
            justify-content: center;
        }

        .admin-layout.sidebar-collapsed .nav-item {
            justify-content: center;
            padding: 10px 0;
            position: relative;
        }

        .admin-layout.sidebar-collapsed .nav-icon {
            margin: 0;
        }

        .admin-layout.sidebar-collapsed .user-card {
            padding: 8px 0;
            justify-content: center;
        }

        .admin-layout.sidebar-collapsed .logout-btn {
            display: none;
        }

        .admin-layout.sidebar-collapsed .nav-badge {
            position: absolute;
            top: 4px;
            right: 12px;
            padding: 1px 5px;
            font-size: 9px;
            border-radius: 10px;
        }

        .admin-layout.sidebar-collapsed .topbar-collapse-btn svg {
            transform: rotate(180deg);
        }

        /* Mobile */
        .hamburger-btn { display: none; background: none; border: none; color: var(--text-secondary); cursor: pointer; padding: 6px; border-radius: 8px; transition: background 0.2s; }
        .hamburger-btn:hover { background: var(--bg-elevated); }
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 49; }
        .sidebar-mobile-close-btn { display: none; }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-overlay.open { display: block; }
            .main-wrapper { margin-left: 0; }

            /* Topbar Mobile Styles */
            .topbar {
                padding: 0 14px;
                gap: 8px;
                height: 58px;
            }

            .topbar-left {
                gap: 8px;
                min-width: 0;
                flex: 1;
            }

            .hamburger-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 36px;
                height: 36px;
                background: var(--bg-elevated);
                border: 1px solid var(--border);
                color: var(--text-primary);
                border-radius: 9px;
                cursor: pointer;
                flex-shrink: 0;
                padding: 0;
                transition: all 0.2s ease;
            }

            .hamburger-btn:hover {
                background: var(--bg-hover);
                border-color: var(--border-light);
            }

            /* Hide desktop sidebar width collapse button on mobile */
            .topbar-collapse-btn {
                display: none !important;
            }

            .topbar-heading {
                min-width: 0;
                flex: 1;
            }

            .topbar-title {
                font-size: 14px;
                line-height: 1.2;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            /* Hide breadcrumb subtitle on mobile to prevent navbar overflow */
            .topbar-breadcrumb {
                display: none !important;
            }

            .topbar-right {
                gap: 6px;
                flex-shrink: 0;
            }

            /* Hide digital clock on mobile — native device status bar already shows it */
            .topbar-clock-pill {
                display: none !important;
            }

            /* Theme toggle button: icon only */
            .theme-toggle-btn {
                width: 36px;
                height: 36px;
                padding: 0;
                justify-content: center;
                border-radius: 9px;
                flex-shrink: 0;
            }

            .theme-toggle-label {
                display: none !important;
            }

            /* Profile button: compact avatar circle */
            .topbar-profile-btn {
                padding: 3px;
                border-radius: 50%;
                background: var(--bg-elevated);
                border: 1px solid var(--border);
                margin-left: 0;
            }

            .topbar-user-avatar {
                width: 30px;
                height: 30px;
                font-size: 12px;
            }

            .topbar-user-meta {
                display: none !important;
            }

            .topbar-profile-chevron {
                display: none !important;
            }

            .topbar-profile-dropdown {
                right: 0;
                width: 210px;
                max-width: calc(100vw - 24px);
                top: calc(100% + 8px);
            }

            /* Topbar action slot on mobile */
            .topbar-action-slot .topbar-btn {
                padding: 6px 10px;
                font-size: 11.5px;
                border-radius: 8px;
            }

            /* Sidebar mobile close button */
            .sidebar-mobile-close-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                position: absolute;
                right: 12px;
                top: 50%;
                transform: translateY(-50%);
                width: 32px;
                height: 32px;
                background: rgba(255, 255, 255, 0.06);
                border: 1px solid rgba(255, 255, 255, 0.1);
                color: #94a3b8;
                border-radius: 8px;
                cursor: pointer;
                transition: all 0.2s;
            }

            .sidebar-mobile-close-btn:hover {
                background: rgba(255, 255, 255, 0.12);
                color: #fff;
            }

            .page-content { padding: 14px; }
            .form-grid { grid-template-columns: 1fr; }

            /* Cards & Stats Mobile Responsiveness */
            .card {
                border-radius: 14px;
            }

            .card-header {
                padding: 14px 16px;
                flex-wrap: wrap;
                gap: 10px;
            }

            .card-title {
                font-size: 13.5px;
            }

            .card-subtitle {
                font-size: 11px;
            }

            .card-body {
                padding: 14px 16px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 10px !important;
                margin-bottom: 18px !important;
            }

            .stat-card {
                padding: 14px 14px 12px !important;
                border-radius: 14px !important;
            }

            .stat-top {
                margin-bottom: 10px !important;
                gap: 6px;
            }

            .stat-label {
                font-size: 10px !important;
                letter-spacing: 0.5px !important;
                line-height: 1.2 !important;
            }

            .stat-icon {
                width: 32px !important;
                height: 32px !important;
                font-size: 15px !important;
                border-radius: 8px !important;
                flex-shrink: 0;
            }

            .stat-value {
                font-size: 20px !important;
                letter-spacing: -0.5px !important;
                margin-bottom: 4px !important;
                line-height: 1.15 !important;
                word-break: break-word;
            }

            .stat-meta {
                font-size: 10.5px !important;
                line-height: 1.25 !important;
                white-space: normal;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            .data-table th, .data-table td {
                padding: 10px 12px;
                font-size: 12px;
            }
        }

        @media (max-width: 480px) {
            .page-content {
                padding: 10px;
            }
            .stats-grid {
                gap: 8px !important;
            }
            .stat-card {
                padding: 12px 10px !important;
            }
            .stat-value {
                font-size: 18px !important;
            }
            .stat-icon {
                width: 28px !important;
                height: 28px !important;
                font-size: 13px !important;
            }
            .topbar {
                padding: 0 10px;
                gap: 6px;
            }
            .topbar-left {
                gap: 6px;
            }
            .topbar-title {
                font-size: 13px;
                max-width: 150px;
            }
            .topbar-right {
                gap: 4px;
            }
            .hamburger-btn {
                width: 34px;
                height: 34px;
            }
            .theme-toggle-btn {
                width: 34px;
                height: 34px;
            }
            .topbar-user-avatar {
                width: 28px;
                height: 28px;
            }
        }
    </style>
</head>
<body>
<div class="admin-layout" id="adminLayout">
    <script>
        (function() {
            if (localStorage.getItem('ekscoder_sidebar_collapsed') === 'true') {
                document.getElementById('adminLayout').classList.add('sidebar-collapsed');
            }
        })();
    </script>

    @include('layouts.sidebar')

    <div class="main-wrapper">
        <x-impersonation-banner />

        <header class="topbar">
            <div class="topbar-left">
                <button type="button" class="hamburger-btn" onclick="toggleSidebar()" aria-label="Toggle Navigation Menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
                <button type="button" class="topbar-collapse-btn" onclick="toggleSidebarCollapse()" title="Toggle Sidebar Width (Full Width)">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="9" y1="3" x2="9" y2="21"/><path d="M15 10l-3 3 3 3"/></svg>
                </button>
                <div class="topbar-heading">
                    <div class="topbar-title" title="{{ $title }}">{{ $title }}</div>
                    @if($breadcrumb)
                        <div class="topbar-breadcrumb" title="{{ $breadcrumb }}">{{ $breadcrumb }}</div>
                    @endif
                </div>
            </div>
            <div class="topbar-right">
                @isset($topbarAction)
                    <div class="topbar-action-slot">
                        {{ $topbarAction }}
                    </div>
                @endisset

                <!-- Theme Toggle Button -->
                <button type="button" class="theme-toggle-btn" onclick="toggleTheme()" title="Toggle Dark/Light Mode">
                    <span id="themeIcon">🌙</span>
                    <span id="themeLabel" class="theme-toggle-label">Dark</span>
                </button>

                <!-- Live Digital Clock Pill -->
                <div class="topbar-clock-pill" title="Live Time">
                    <span class="topbar-clock-dot"></span>
                    <span id="topbarClockDisplay" style="font-family:'JetBrains Mono', monospace; font-weight:700; letter-spacing:0.5px;">15:29:00</span>
                </div>

                <!-- Profile Widget & Dropdown on Right Navbar -->
                <div class="topbar-profile-container">
                    <button type="button" class="topbar-profile-btn" onclick="toggleProfileDropdown(event)" title="Account Menu">
                        <div class="topbar-user-avatar">
                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="topbar-user-meta">
                            <span class="topbar-user-name">{{ Auth::user()->name ?? 'Admin' }}</span>
                            <span class="topbar-user-role">{{ Auth::user()->roles->first()->name ?? 'Member' }}</span>
                        </div>
                        <svg class="topbar-profile-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>

                    <div class="topbar-profile-dropdown" id="topbarProfileDropdown">
                        <div class="profile-dropdown-header">
                            <div class="user-fullname">{{ Auth::user()->name ?? 'Admin' }}</div>
                            <div class="user-email">{{ Auth::user()->email ?? 'admin@ekscoder.com' }}</div>
                        </div>
                        @if(session()->has('impersonator_id'))
                        <div class="profile-dropdown-divider"></div>
                        <form method="POST" action="{{ route('impersonate.leave') }}" style="margin: 0;">
                            @csrf
                            <button type="submit" class="profile-dropdown-item" style="width: 100%; border: none; background: rgba(245,158,11,0.12); color: #f59e0b; cursor: pointer; text-align: left; font-weight: 700; gap: 8px;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                Kembali ke Akun Admin
                            </button>
                        </form>
                        @endif
                        <div class="profile-dropdown-divider"></div>
                        <a href="{{ route('profile.edit') }}" class="profile-dropdown-item">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            Profile Settings
                        </a>
                        <div class="profile-dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                            @csrf
                            <button type="submit" class="profile-dropdown-item text-danger" style="width: 100%; border: none; background: none; cursor: pointer; text-align: left;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="page-content">
            {{ $slot }}
        </main>

    </div>
</div>

<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('open');
}

function toggleProfileDropdown(event) {
    event.stopPropagation();
    const dropdown = document.getElementById('topbarProfileDropdown');
    if (dropdown) {
        dropdown.classList.toggle('open');
    }
}

document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('topbarProfileDropdown');
    if (dropdown && !event.target.closest('.topbar-profile-container')) {
        dropdown.classList.remove('open');
    }
});

function updateThemeUI(theme) {
    const icon = document.getElementById('themeIcon');
    const label = document.getElementById('themeLabel');
    if (theme === 'light') {
        if (icon) icon.textContent = '☀️';
        if (label) label.textContent = 'Light';
    } else {
        if (icon) icon.textContent = '🌙';
        if (label) label.textContent = 'Dark';
    }
}

function toggleTheme() {
    const current = document.documentElement.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
    document.documentElement.setAttribute('data-theme', current);
    localStorage.setItem('ekscoder_theme', current);
    updateThemeUI(current);
}

function startTopbarClock() {
    function tick() {
        const now = new Date();
        const h = String(now.getHours()).padStart(2, '0');
        const m = String(now.getMinutes()).padStart(2, '0');
        const s = String(now.getSeconds()).padStart(2, '0');
        const el = document.getElementById('topbarClockDisplay');
        if (el) el.textContent = `${h}:${m}:${s}`;
    }
    tick();
    setInterval(tick, 1000);
}

function toggleSidebarCollapse() {
    const layout = document.querySelector('.admin-layout');
    if (layout) {
        layout.classList.toggle('sidebar-collapsed');
        const isCollapsed = layout.classList.contains('sidebar-collapsed');
        localStorage.setItem('ekscoder_sidebar_collapsed', isCollapsed ? 'true' : 'false');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const theme = document.documentElement.getAttribute('data-theme') || 'dark';
    updateThemeUI(theme);
    startTopbarClock();

    const isCollapsed = localStorage.getItem('ekscoder_sidebar_collapsed') === 'true';
    const layout = document.querySelector('.admin-layout');
    if (isCollapsed && layout) {
        layout.classList.add('sidebar-collapsed');
    }
});
</script>
<x-sweetalert />
</body>
</html>
