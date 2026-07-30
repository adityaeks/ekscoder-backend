<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Ekscoder Admin — {{ $title ?? 'Dashboard' }}</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-base:      #0a0a0f;
            --bg-surface:   #111118;
            --bg-elevated:  #16161f;
            --bg-hover:     #1c1c28;
            --border:       rgba(255,255,255,0.07);
            --border-light: rgba(255,255,255,0.12);
            --text-primary: #f0f0f5;
            --text-secondary: #8b8ba0;
            --text-muted:   #55555f;
            --accent:       #6c63ff;
            --accent-glow:  rgba(108,99,255,0.25);
            --accent-soft:  rgba(108,99,255,0.12);
            --green:        #22c55e;
            --green-soft:   rgba(34,197,94,0.12);
            --amber:        #f59e0b;
            --amber-soft:   rgba(245,158,11,0.12);
            --rose:         #f43f5e;
            --rose-soft:    rgba(244,63,94,0.12);
            --cyan:         #06b6d4;
            --cyan-soft:    rgba(6,182,212,0.12);
            --sidebar-w:    260px;
        }

        html, body { height: 100%; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-base);
            color: var(--text-primary);
            min-height: 100vh;
        }

        /* ==============================
           LAYOUT
           ============================== */
        .admin-layout {
            display: flex;
            min-height: 100vh;
        }

        /* ==============================
           SIDEBAR
           ============================== */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--bg-surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 50;
            transition: transform 0.3s ease;
        }

        .sidebar-logo {
            padding: 24px 20px 20px;
            border-bottom: 1px solid var(--border);
        }

        .sidebar-logo .logo-mark {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .sidebar-logo .logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--accent) 0%, #8b5cf6 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 14px;
            color: #fff;
            letter-spacing: -0.5px;
            box-shadow: 0 0 20px var(--accent-glow);
        }

        .sidebar-logo .logo-text {
            display: flex;
            flex-direction: column;
        }

        .sidebar-logo .logo-name {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -0.3px;
        }

        .sidebar-logo .logo-sub {
            font-size: 10px;
            font-weight: 500;
            color: var(--text-muted);
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }

        .sidebar-nav {
            flex: 1;
            padding: 16px 12px;
            overflow-y: auto;
        }

        .nav-section-label {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: var(--text-muted);
            padding: 4px 10px 10px;
            margin-top: 8px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 10px;
            text-decoration: none;
            color: var(--text-secondary);
            font-size: 13.5px;
            font-weight: 500;
            transition: all 0.2s ease;
            margin-bottom: 2px;
            cursor: pointer;
            border: 1px solid transparent;
        }

        .nav-item:hover {
            background: var(--bg-hover);
            color: var(--text-primary);
            border-color: var(--border);
        }

        .nav-item.active {
            background: var(--accent-soft);
            color: #a09af8;
            border-color: rgba(108,99,255,0.2);
        }

        .nav-item .nav-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
            background: transparent;
            transition: background 0.2s;
        }

        .nav-item.active .nav-icon {
            background: var(--accent-soft);
        }

        .nav-item .nav-badge {
            margin-left: auto;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 20px;
            background: var(--accent-soft);
            color: #a09af8;
        }

        .sidebar-footer {
            padding: 12px;
            border-top: 1px solid var(--border);
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            background: var(--bg-elevated);
            border: 1px solid var(--border);
        }

        .user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }

        .user-info { flex: 1; min-width: 0; }

        .user-name {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text-primary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-role {
            font-size: 10.5px;
            color: var(--text-muted);
        }

        .logout-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 7px;
            border: 1px solid var(--border);
            background: transparent;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            font-size: 14px;
            flex-shrink: 0;
        }

        .logout-btn:hover {
            background: var(--rose-soft);
            color: var(--rose);
            border-color: rgba(244,63,94,0.25);
        }

        /* ==============================
           MAIN CONTENT
           ============================== */
        .main-wrapper {
            flex: 1;
            margin-left: var(--sidebar-w);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ==============================
           TOPBAR
           ============================== */
        .topbar {
            position: sticky;
            top: 0;
            z-index: 40;
            background: rgba(10, 10, 15, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
            padding: 0 28px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .topbar-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .topbar-breadcrumb {
            font-size: 12px;
            color: var(--text-muted);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .topbar-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 14px;
            border-radius: 9px;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            border: 1px solid transparent;
        }

        .topbar-btn-primary {
            background: var(--accent);
            color: #fff;
            box-shadow: 0 0 20px var(--accent-glow);
        }

        .topbar-btn-primary:hover {
            background: #7c74ff;
            box-shadow: 0 0 28px var(--accent-glow);
        }

        .topbar-btn-ghost {
            background: var(--bg-elevated);
            color: var(--text-secondary);
            border-color: var(--border);
        }

        .topbar-btn-ghost:hover {
            color: var(--text-primary);
            border-color: var(--border-light);
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
            margin-left: 4px;
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
        }

        .topbar-user-meta {
            display: flex;
            flex-direction: column;
            line-height: 1.15;
            text-align: left;
        }

        .topbar-user-name {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .topbar-user-role {
            font-size: 10px;
            color: var(--text-muted);
        }

        .topbar-profile-dropdown {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            width: 210px;
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 6px;
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.45);
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
            margin: 4px 0;
        }

        .profile-dropdown-item {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 8px 10px;
            border-radius: 8px;
            font-size: 12px;
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

        .api-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            background: var(--green-soft);
            color: var(--green);
            border: 1px solid rgba(34,197,94,0.2);
        }

        .api-status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--green);
            animation: pulse-green 2s infinite;
        }

        @keyframes pulse-green {
            0%, 100% { opacity: 1; box-shadow: 0 0 0 0 rgba(34,197,94,0.4); }
            50% { opacity: 0.7; box-shadow: 0 0 0 4px rgba(34,197,94,0); }
        }

        /* ==============================
           PAGE CONTENT
           ============================== */
        .page-content {
            flex: 1;
            padding: 28px;
        }

        /* ==============================
           CARDS
           ============================== */
        .card {
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
        }

        .card-header {
            padding: 18px 22px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .card-subtitle {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .card-body { padding: 22px; }

        /* ==============================
           STAT CARDS
           ============================== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            position: relative;
            overflow: hidden;
            transition: border-color 0.2s, transform 0.2s;
        }

        .stat-card:hover {
            border-color: var(--border-light);
            transform: translateY(-2px);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
        }

        .stat-card.accent::before { background: linear-gradient(90deg, var(--accent), #8b5cf6); }
        .stat-card.green::before  { background: linear-gradient(90deg, var(--green), #10b981); }
        .stat-card.amber::before  { background: linear-gradient(90deg, var(--amber), #f97316); }
        .stat-card.cyan::before   { background: linear-gradient(90deg, var(--cyan), #3b82f6); }

        .stat-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .stat-label {
            font-size: 11.5px;
            font-weight: 500;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.7px;
        }

        .stat-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
        }

        .stat-icon.accent { background: var(--accent-soft); }
        .stat-icon.green  { background: var(--green-soft); }
        .stat-icon.amber  { background: var(--amber-soft); }
        .stat-icon.cyan   { background: var(--cyan-soft); }

        .stat-value {
            font-size: 34px;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -1.5px;
            line-height: 1;
            margin-bottom: 6px;
        }

        .stat-meta {
            font-size: 11.5px;
            color: var(--text-muted);
        }

        /* ==============================
           TABLE
           ============================== */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead tr {
            border-bottom: 1px solid var(--border);
        }

        .data-table th {
            padding: 11px 16px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--text-muted);
        }

        .data-table tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background 0.15s;
        }

        .data-table tbody tr:last-child { border-bottom: none; }

        .data-table tbody tr:hover { background: var(--bg-hover); }

        .data-table td {
            padding: 14px 16px;
            font-size: 13.5px;
            color: var(--text-secondary);
            vertical-align: middle;
        }

        /* ==============================
           BADGES
           ============================== */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 9px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            border: 1px solid transparent;
        }

        .badge-green  { background: var(--green-soft);  color: var(--green);  border-color: rgba(34,197,94,0.2); }
        .badge-rose   { background: var(--rose-soft);   color: var(--rose);   border-color: rgba(244,63,94,0.2); }
        .badge-amber  { background: var(--amber-soft);  color: var(--amber);  border-color: rgba(245,158,11,0.2); }
        .badge-accent { background: var(--accent-soft); color: #a09af8;       border-color: rgba(108,99,255,0.2); }

        .badge-dot {
            width: 5px; height: 5px;
            border-radius: 50%;
            background: currentColor;
        }

        /* ==============================
           BUTTONS
           ============================== */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid transparent;
            text-decoration: none;
            font-family: 'Inter', sans-serif;
        }

        .btn-primary { background: var(--accent); color: #fff; }
        .btn-primary:hover { background: #7c74ff; }

        .btn-ghost { background: var(--bg-elevated); color: var(--text-secondary); border-color: var(--border); }
        .btn-ghost:hover { color: var(--text-primary); border-color: var(--border-light); }

        .btn-danger { background: var(--rose-soft); color: var(--rose); border-color: rgba(244,63,94,0.2); }
        .btn-danger:hover { background: rgba(244,63,94,0.2); }

        .btn-success { background: var(--green-soft); color: var(--green); border-color: rgba(34,197,94,0.2); }
        .btn-success:hover { background: rgba(34,197,94,0.2); }

        /* ==============================
           TECH PILLS
           ============================== */
        .tech-pills { display: flex; flex-wrap: wrap; gap: 4px; }

        .tech-pill {
            font-family: 'JetBrains Mono', monospace;
            font-size: 10px;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 5px;
            background: var(--bg-elevated);
            border: 1px solid var(--border);
            color: var(--text-muted);
        }

        /* ==============================
           FORM STYLES
           ============================== */
        .form-group { margin-bottom: 18px; }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 7px;
        }

        .form-input, .form-textarea, .form-select {
            width: 100%;
            padding: 9px 13px;
            background: var(--bg-elevated);
            border: 1px solid var(--border);
            border-radius: 9px;
            color: var(--text-primary);
            font-size: 13.5px;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }

        .form-input:focus, .form-textarea:focus, .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        .form-input::placeholder, .form-textarea::placeholder { color: var(--text-muted); }

        .form-input:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .form-textarea { resize: vertical; min-height: 90px; }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

        .form-check {
            display: flex;
            align-items: center;
            gap: 9px;
            cursor: pointer;
        }

        .form-check input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--accent);
            cursor: pointer;
        }

        .form-check-label {
            font-size: 13px;
            font-weight: 500;
            color: var(--text-secondary);
        }

        .form-hint {
            margin-top: 5px;
            font-size: 11px;
            color: var(--text-muted);
        }

        .error-list {
            background: var(--rose-soft);
            border: 1px solid rgba(244,63,94,0.25);
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 20px;
        }

        .error-list p {
            font-size: 12.5px;
            color: var(--rose);
            line-height: 1.6;
        }

        /* ==============================
           FLASH MESSAGE
           ============================== */
        .flash {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .flash-success {
            background: var(--green-soft);
            border: 1px solid rgba(34,197,94,0.2);
            color: var(--green);
        }

        /* ==============================
           EMPTY STATE
           ============================== */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: var(--text-muted);
        }

        .empty-state-icon { font-size: 42px; margin-bottom: 12px; }
        .empty-state-text { font-size: 14px; color: var(--text-secondary); }

        /* ==============================
           MOBILE HAMBURGER
           ============================== */
        .hamburger-btn {
            display: none;
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 6px;
            border-radius: 8px;
            transition: background 0.2s;
        }

        .hamburger-btn:hover { background: var(--bg-elevated); }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            z-index: 49;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .sidebar-overlay.open {
                display: block;
            }
            .main-wrapper {
                margin-left: 0;
            }
            .hamburger-btn {
                display: flex;
            }
            .topbar { padding: 0 16px; }
            .page-content { padding: 16px; }
            .form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="admin-layout">

    <!-- Sidebar Overlay (mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <!-- Logo -->
        <div class="sidebar-logo">
            <a href="{{ route('dashboard') }}" class="logo-mark">
                <div class="logo-icon">EK</div>
                <div class="logo-text">
                    <span class="logo-name">Ekscoder</span>
                    <span class="logo-sub">Admin Panel</span>
                </div>
            </a>
        </div>

        <!-- Navigation -->
        <nav class="sidebar-nav">
            <div class="nav-section-label">Main</div>

            <a href="{{ route('dashboard') }}"
               class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <div class="nav-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"/>
                        <rect x="14" y="3" width="7" height="7"/>
                        <rect x="14" y="14" width="7" height="7"/>
                        <rect x="3" y="14" width="7" height="7"/>
                    </svg>
                </div>
                Dashboard
            </a>

            <div class="nav-section-label" style="margin-top:16px;">Content</div>

            <a href="{{ route('admin.projects.index') }}"
               class="nav-item {{ request()->routeIs('admin.projects.*') ? 'active' : '' }}">
                <div class="nav-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="12 2 2 7 12 12 22 7 12 2"/>
                        <polyline points="2 17 12 22 22 17"/>
                        <polyline points="2 12 12 17 22 12"/>
                    </svg>
                </div>
                Projects
                @php $projectCount = \App\Models\Project::where('is_active', true)->count(); @endphp
                @if($projectCount)
                    <span class="nav-badge">{{ $projectCount }}</span>
                @endif
            </a>

            <div class="nav-section-label" style="margin-top:16px;">Account</div>

            <a href="{{ route('profile.edit') }}"
               class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <div class="nav-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
                Profile
            </a>

            <div class="nav-section-label" style="margin-top:16px;">Infrastructure</div>

            <a href="{{ route('admin.cloudflare-zones.index') }}"
               class="nav-item {{ request()->routeIs('admin.cloudflare-zones.*') ? 'active' : '' }}">
                <div class="nav-icon" style="color:#f57c00;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z"/>
                    </svg>
                </div>
                Cloudflare API
            </a>

            <!-- API Link -->
            <div class="nav-section-label" style="margin-top:16px;">Developer</div>

            <a href="/api/projects" target="_blank" class="nav-item">
                <div class="nav-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="16 18 22 12 16 6"/>
                        <polyline points="8 6 2 12 8 18"/>
                    </svg>
                </div>
                API Preview
            </a>
        </nav>

        <!-- Footer: User card + logout -->
        <div class="sidebar-footer">
            <div class="user-card">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="user-info">
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <div class="user-role">Administrator</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn" title="Logout">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main-wrapper">
        <!-- Topbar -->
        <header class="topbar">
            <div class="topbar-left">
                <button class="hamburger-btn" onclick="toggleSidebar()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <line x1="3" y1="12" x2="21" y2="12"/>
                        <line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>

                <div>
                    <div class="topbar-title">{{ $title ?? 'Dashboard' }}</div>
                    @if(isset($breadcrumb))
                        <div class="topbar-breadcrumb">{{ $breadcrumb }}</div>
                    @endif
                </div>
            </div>

            <div class="topbar-right">
                <div class="api-status-pill">
                    <span class="api-status-dot"></span>
                    API Live
                </div>

                @isset($topbarAction)
                    {{ $topbarAction }}
                @endisset
            </div>
        </header>

        <!-- Page Content -->
        <main class="page-content">
            @if(session('success'))
                <div class="flash flash-success">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
</div>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    sidebar.classList.toggle('open');
    overlay.classList.toggle('open');
}
</script>
</body>
</html>
