<x-admin-layout 
    :title="($isAdmin ?? false) ? 'Dashboard Overview' : 'Dashboard'" 
    :breadcrumb="($isAdmin ?? false) ? 'Executive Command Center & Business Pipeline' : 'Workspace / Selamat Datang'">

@if($isAdmin ?? false)
    <style>
        .dashboard-stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }
        @media (max-width: 1200px) {
            .dashboard-stats-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        @media (max-width: 768px) {
            .dashboard-stats-grid {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 10px !important;
                margin-bottom: 18px !important;
            }
            .dashboard-stats-grid .stat-card-hero {
                grid-column: span 2;
                background: linear-gradient(135deg, var(--bg-surface) 0%, rgba(184, 255, 0, 0.05) 100%);
                border-color: rgba(184, 255, 0, 0.25);
            }
            .dashboard-stats-grid .stat-card-hero .stat-value {
                font-size: 22px !important;
            }
            .dashboard-stats-grid .stat-card-currency .stat-value {
                font-size: clamp(14px, 3.8vw, 17px) !important;
                letter-spacing: -0.3px;
            }
        }
        @media (max-width: 360px) {
            .dashboard-stats-grid {
                grid-template-columns: 1fr !important;
            }
            .dashboard-stats-grid .stat-card-hero {
                grid-column: span 1;
            }
        }
        .dashboard-main-grid {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 20px;
            margin-bottom: 24px;
        }
        @media (max-width: 1024px) {
            .dashboard-main-grid {
                grid-template-columns: 1fr;
                gap: 16px;
                margin-bottom: 20px;
            }
        }
    </style>

    <!-- Top Stats Grid -->
    <div class="stats-grid dashboard-stats-grid">
        <div class="stat-card accent stat-card-hero">
            <div class="stat-top">
                <div class="stat-label">Pipeline Value</div>
                <div class="stat-icon accent">💰</div>
            </div>
            <div class="stat-value" style="font-size:22px;">Rp {{ number_format($stats['total_pipeline'] ?? 0, 0, ',', '.') }}</div>
            <div class="stat-meta">Gross contract value (excl. cancelled)</div>
        </div>

        <div class="stat-card green stat-card-currency">
            <div class="stat-top">
                <div class="stat-label">Collected Payments</div>
                <div class="stat-icon green">💵</div>
            </div>
            <div class="stat-value" style="font-size:22px; color:var(--green);">Rp {{ number_format($stats['total_paid'] ?? 0, 0, ',', '.') }}</div>
            <div class="stat-meta">Received DP & full payments</div>
        </div>

        <div class="stat-card cyan">
            <div class="stat-top">
                <div class="stat-label">Active Orders</div>
                <div class="stat-icon cyan">⚡</div>
            </div>
            <div class="stat-value">{{ $stats['active_orders'] ?? 0 }}</div>
            <div class="stat-meta">Ongoing projects in pipeline</div>
        </div>

        <div class="stat-card amber">
            <div class="stat-top">
                <div class="stat-label">Completed Orders</div>
                <div class="stat-icon amber">🎉</div>
            </div>
            <div class="stat-value">{{ $stats['completed_orders'] ?? 0 }}</div>
            <div class="stat-meta">Finished and delivered</div>
        </div>

        <div class="stat-card accent">
            <div class="stat-top">
                <div class="stat-label">Porto Projects</div>
                <div class="stat-icon accent">📂</div>
            </div>
            <div class="stat-value">{{ $stats['total_projects'] ?? 0 }}</div>
            <div class="stat-meta">{{ $stats['active_projects'] ?? 0 }} live on public API</div>
        </div>
    </div>

    <!-- 2-Column Dashboard Grid: Orders Pipeline + Audit Feed -->
    <div class="dashboard-main-grid">
        
        <!-- Left: Recent Project Orders -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <div>
                    <div class="card-title">Recent Project Orders</div>
                    <div class="card-subtitle">Latest client contracts, status, and target dates</div>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-ghost">
                    Order Board
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            </div>
            <div style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Project & Client</th>
                            <th>Status Stage</th>
                            <th>Target Dates</th>
                            <th>Budget & Paid</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                            <tr>
                                <td>
                                    <div style="font-weight:700; color:var(--text-primary); font-size:13px;">{{ $order->title }}</div>
                                    <div style="font-size:11px; color:var(--text-secondary);">👤 {{ $order->client_name }}</div>
                                </td>
                                <td>
                                    @if($order->status === 'requirement')
                                        <span class="badge badge-amber">📝 Requirement</span>
                                    @elseif($order->status === 'in_progress')
                                        <span class="badge badge-cyan">⚡ In Progress</span>
                                    @elseif($order->status === 'review')
                                        <span class="badge badge-accent">🔍 Review</span>
                                    @elseif($order->status === 'completed')
                                        <span class="badge badge-green">✅ Completed</span>
                                    @else
                                        <span class="badge badge-rose">⛔ Cancelled</span>
                                    @endif
                                </td>
                                @php
                                    $isOverdue = $order->deadline && $order->deadline->isPast() && $order->status !== 'completed';
                                    $deadlineColor = $isOverdue ? 'var(--rose)' : 'var(--text-muted)';
                                    $deadlineWeight = $isOverdue ? '700' : '400';
                                @endphp
                                <td>
                                    <div style="font-size:11px; color:var(--text-secondary);">
                                        🚀 {{ $order->start_date ? $order->start_date->format('d M Y') : '-' }}
                                    </div>
                                    <div style="font-size:11px; color: {{ $deadlineColor }}; font-weight: {{ $deadlineWeight }};">
                                        🏁 {{ $order->deadline ? $order->deadline->format('d M Y') : '-' }}
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size:12.5px; font-weight:700; color:var(--text-primary);">{{ $order->formatted_budget }}</div>
                                    <div style="font-size:10.5px; color:var(--green); font-weight:600;">Paid: {{ $order->formatted_paid }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="empty-state" style="padding: 30px 10px;">
                                        <div class="empty-state-text">No project orders recorded yet.</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Recent Activity Audit Logs -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <div>
                    <div class="card-title">Activity Audit Feed</div>
                    <div class="card-subtitle">Real-time user actions</div>
                </div>
                <a href="{{ route('admin.logs.index') }}" class="btn btn-ghost" style="padding: 4px 8px; font-size: 11px;">
                    View All
                </a>
            </div>
            <div class="card-body" style="padding: 14px;">
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @forelse($recentLogs as $log)
                        <div style="display: flex; align-items: flex-start; gap: 10px; padding-bottom: 10px; border-bottom: 1px solid var(--border);">
                            <div style="width: 28px; height: 28px; border-radius: 50%; background: var(--bg-elevated); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: var(--text-primary); flex-shrink: 0; margin-top: 2px;">
                                {{ strtoupper(substr($log->user_name ?? 'S', 0, 1)) }}
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="display: flex; align-items: center; justify-content: space-between; gap: 6px; margin-bottom: 2px;">
                                    <span style="font-size: 11.5px; font-weight: 700; color: var(--text-primary);">{{ $log->user_name ?? 'System' }}</span>
                                    @if($log->action === 'create')
                                        <span class="badge badge-green" style="font-size: 9px; padding: 1px 6px;">CREATE</span>
                                    @elseif($log->action === 'update')
                                        <span class="badge badge-accent" style="font-size: 9px; padding: 1px 6px;">UPDATE</span>
                                    @elseif($log->action === 'delete')
                                        <span class="badge badge-rose" style="font-size: 9px; padding: 1px 6px;">DELETE</span>
                                    @elseif($log->action === 'login')
                                        <span class="badge" style="font-size: 9px; padding: 1px 6px; background: rgba(6,182,212,0.15); color: #22d3ee; border: 1px solid rgba(6,182,212,0.25);">LOGIN</span>
                                    @elseif($log->action === 'logout')
                                        <span class="badge badge-amber" style="font-size: 9px; padding: 1px 6px;">LOGOUT</span>
                                    @else
                                        <span class="badge badge-amber" style="font-size: 9px; padding: 1px 6px;">{{ strtoupper($log->action) }}</span>
                                    @endif
                                </div>
                                <div style="font-size: 11.5px; color: var(--text-secondary); line-height: 1.3; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $log->description }}
                                </div>
                                <div style="font-size: 10px; color: var(--text-muted); margin-top: 3px;">
                                    {{ $log->created_at->diffForHumans() }} • <span style="font-family:'JetBrains Mono',monospace;">{{ $log->module }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state" style="padding: 20px 10px;">
                            <div class="empty-state-text">No activity logged yet.</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    <!-- Web Portfolio Projects Table -->
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Porto Projects Overview</div>
                <div class="card-subtitle">Public portfolio items served via `/api/projects`</div>
            </div>
            <a href="{{ route('admin.projects.index') }}" class="btn btn-ghost">
                Manage Projects
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
        </div>

        <div style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Project</th>
                        <th>Category & Year</th>
                        <th>Tech Stack</th>
                        <th>Featured</th>
                        <th>API Status</th>
                        <th style="text-align:right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $project)
                    <tr>
                        <td>
                            <span style="font-family:'JetBrains Mono',monospace; font-size:12px; font-weight:700; color:var(--text-muted);">#{{ $project->number }}</span>
                        </td>
                        <td>
                            <div style="font-weight:700; color:var(--text-primary); font-size:13.5px;">{{ $project->title }}</div>
                            <div style="font-size:11px; color:var(--text-muted); font-family:'JetBrains Mono',monospace; margin-top:1px;">{{ $project->slug }}</div>
                        </td>
                        <td>
                            <div style="font-weight:500; color:var(--text-secondary); font-size:12.5px;">{{ $project->category }}</div>
                            <div style="font-size:11px; color:var(--text-muted);">{{ $project->year }}</div>
                        </td>
                        <td>
                            <div class="tech-pills">
                                @foreach(array_slice($project->technologies, 0, 3) as $tech)
                                    <span class="tech-pill">{{ $tech }}</span>
                                @endforeach
                                @if(count($project->technologies) > 3)
                                    <span class="tech-pill">+{{ count($project->technologies) - 3 }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($project->featured)
                                <span class="badge badge-amber">★ Featured</span>
                            @else
                                <span style="color:var(--text-muted); font-size:12px;">Standard</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.projects.toggle-active', $project->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="badge {{ $project->is_active ? 'badge-green' : 'badge-rose' }}" style="cursor:pointer; border:none; font-family:inherit;">
                                    <span class="badge-dot"></span>
                                    {{ $project->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>
                        <td style="text-align:right">
                            <div style="display:flex; gap:6px; justify-content:flex-end;">
                                <a href="{{ route('admin.projects.edit', $project->id) }}" class="btn btn-ghost" style="padding:4px 10px; font-size:11.5px;">Edit</a>
                                <form action="{{ route('admin.projects.destroy', $project->id) }}" method="POST" onsubmit="event.preventDefault(); confirmDelete('Delete \'{{ addslashes($project->title) }}\'?', 'This project will be permanently deleted.', this);">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding:4px 10px; font-size:11.5px;">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-state-icon">📭</div>
                                <div class="empty-state-text">No projects yet. <a href="{{ route('admin.projects.create') }}" style="color:var(--accent)">Create your first one.</a></div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@else
    <!-- Styles for Guest Dashboard (Adaptive Dark & Light Mode) -->
    <style>
        .welcome-hero-card {
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 28px 26px;
            position: relative;
            overflow: hidden;
            transition: all 0.25s ease;
        }

        html[data-theme="dark"] .welcome-hero-card {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.12) 0%, var(--bg-surface) 100%);
            border-color: rgba(99, 102, 241, 0.25);
            box-shadow: 0 12px 30px -8px rgba(0, 0, 0, 0.45);
        }

        html[data-theme="light"] .welcome-hero-card {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, #ffffff 100%);
            border-color: rgba(99, 102, 241, 0.18);
            box-shadow: 0 8px 25px -4px rgba(99, 102, 241, 0.08), 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .welcome-avatar {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 800;
            color: #ffffff;
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.28);
            flex-shrink: 0;
        }

        .guest-feature-card {
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 22px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .guest-feature-card:hover {
            border-color: var(--accent);
            transform: translateY(-3px);
            box-shadow: 0 12px 24px -6px rgba(0, 0, 0, 0.08);
        }

        html[data-theme="dark"] .guest-feature-card:hover {
            box-shadow: 0 12px 24px -6px rgba(0, 0, 0, 0.4);
        }

        .guest-feature-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 14px;
            background: var(--bg-elevated);
            border: 1px solid var(--border);
        }

        .guest-info-box {
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
    </style>

    <!-- Guest / Limited User Welcome Dashboard -->
    <div style="display:flex; flex-direction:column; gap:24px;">
        
        <!-- Welcome Hero Banner -->
        <div class="welcome-hero-card">
            <div style="display:flex; align-items:center; gap:20px; flex-wrap:wrap; position:relative; z-index:1;">
                <div class="welcome-avatar">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div style="flex:1; min-width:260px;">
                    <h1 style="font-size:22px; font-weight:800; color:var(--text-primary); margin:0 0 6px 0; letter-spacing:-0.4px;">
                        Selamat Datang, {{ Auth::user()->name }}! 👋
                    </h1>
                    <p style="font-size:13.5px; color:var(--text-secondary); margin:0; line-height:1.6;">
                        Senang melihat Anda kembali di <strong>Ekscoder Platform</strong>. Silakan pilih menu layanan di bawah ini atau navigasi sidebar untuk mulai mengakses fitur Anda.
                    </p>
                </div>
            </div>
        </div>

        <!-- Section: Fitur & Modul Tersedia -->
        <div>
            <h2 style="font-size:15px; font-weight:700; color:var(--text-primary); margin-bottom:14px; display:flex; align-items:center; gap:8px;">
                <span>🚀</span> Fitur & Layanan Anda
            </h2>

            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:16px;">
                @can('emodul.view')
                <div class="guest-feature-card">
                    <div>
                        <div class="guest-feature-icon" style="color:#6366f1;">
                            📖
                        </div>
                        <div style="font-size:15.5px; font-weight:700; color:var(--text-primary); margin-bottom:6px;">
                            Pustaka E-Modul
                        </div>
                        <p style="font-size:13px; color:var(--text-secondary); line-height:1.5; margin:0 0 16px 0;">
                            Akses dan pelajari modul digital dengan tampilan 3D flipbook reader interaktif serta bantuan AI cerdas.
                        </p>
                    </div>
                    <a href="{{ route('admin.e-modul.index') }}" class="btn btn-primary" style="align-self:flex-start; font-size:12.5px; padding:7px 16px;">
                        Buka E-Modul &rarr;
                    </a>
                </div>
                @endcan

                @can('ai_chat.view')
                <div class="guest-feature-card">
                    <div>
                        <div class="guest-feature-icon" style="color:#06b6d4;">
                            🤖
                        </div>
                        <div style="font-size:15.5px; font-weight:700; color:var(--text-primary); margin-bottom:6px;">
                            AI Assistant
                        </div>
                        <p style="font-size:13px; color:var(--text-secondary); line-height:1.5; margin:0 0 16px 0;">
                            Gunakan asisten kecerdasan buatan untuk berdiskusi, menganalisis materi, atau konsultasi ide tugas Anda.
                        </p>
                    </div>
                    <a href="{{ route('admin.ai-chat.index') }}" class="btn btn-primary" style="align-self:flex-start; font-size:12.5px; padding:7px 16px;">
                        Mulai Diskusi &rarr;
                    </a>
                </div>
                @endcan

                @can('ai_cs.view')
                <div class="guest-feature-card">
                    <div>
                        <div class="guest-feature-icon" style="color:#f59e0b;">
                            💬
                        </div>
                        <div style="font-size:15.5px; font-weight:700; color:var(--text-primary); margin-bottom:6px;">
                            AI Customer Service
                        </div>
                        <p style="font-size:13px; color:var(--text-secondary); line-height:1.5; margin:0 0 16px 0;">
                            Lihat log pesan dan konfigurasi respons percakapan otomatis bot customer service.
                        </p>
                    </div>
                    <a href="{{ route('admin.ai-cs.index') }}" class="btn btn-ghost" style="align-self:flex-start; font-size:12.5px; padding:7px 16px; border:1px solid var(--border);">
                        Lihat Sesi CS &rarr;
                    </a>
                </div>
                @endcan

                <!-- Profil Akun Card (Selalu Ada) -->
                <div class="guest-feature-card">
                    <div>
                        <div class="guest-feature-icon" style="color:var(--text-secondary);">
                            ⚙️
                        </div>
                        <div style="font-size:15.5px; font-weight:700; color:var(--text-primary); margin-bottom:6px;">
                            Pengaturan Profil
                        </div>
                        <p style="font-size:13px; color:var(--text-secondary); line-height:1.5; margin:0 0 16px 0;">
                            Perbarui data akun, email login, serta kelola kata sandi pribadi Anda secara mandiri.
                        </p>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="btn btn-ghost" style="align-self:flex-start; font-size:12.5px; padding:7px 16px; border:1px solid var(--border);">
                        Kelola Profil &rarr;
                    </a>
                </div>
            </div>
        </div>

        <!-- Simple Footer Notice -->
        <div class="guest-info-box">
            <div style="font-size:18px;">💡</div>
            <div style="font-size:12.5px; color:var(--text-muted); line-height:1.4;">
                Memerlukan akses ke modul atau fitur manajemen lainnya? Silakan hubungi <strong>Super Admin</strong> untuk penyesuaian hak akses (role & permission).
            </div>
        </div>

    </div>
@endif

</x-admin-layout>
