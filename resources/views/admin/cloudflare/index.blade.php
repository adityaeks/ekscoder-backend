<x-admin-layout title="Cloudflare Management" breadcrumb="Infrastructure / Cloudflare Zones">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:16px;">
        <div>
            <h1 style="font-size:22px; font-weight:800; color:var(--text-primary); letter-spacing:-0.5px; display:flex; align-items:center; gap:10px;">
                <span style="display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; border-radius:10px; background:rgba(245, 124, 0, 0.15); color:#f57c00;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z"/>
                    </svg>
                </span>
                Cloudflare Zone & Domain Management
            </h1>
            <p style="font-size:13px; color:var(--text-muted); margin-top:4px;">
                Kelola domain, DNS records, proxy orange cloud, cache purge, dan pengaturan keamanan langsung via Cloudflare API.
            </p>
        </div>

        <div style="display:flex; align-items:center; gap:10px;">
            <form action="{{ route('admin.cloudflare-pin.lock') }}" method="POST" style="margin:0;">
                @csrf
                <button type="submit" class="btn btn-ghost" style="padding:9px 14px; font-size:12.5px; border:1px solid rgba(255,255,255,0.12); color:var(--text-muted); display:inline-flex; align-items:center; gap:6px;" title="Kunci kembali akses Cloudflare">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    Lock Cloudflare
                </button>
            </form>

            @if($isConfigured)
                <button onclick="openAddModal()" class="topbar-btn topbar-btn-primary" style="background:linear-gradient(135deg, #f57c00, #ff9800); border:none; padding:10px 18px; font-size:13px; border-radius:10px; box-shadow: 0 0 20px rgba(245, 124, 0, 0.3);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Tambah Domain Baru
                </button>
            @endif
        </div>
    </div>

    {{-- Flash Notifications --}}
    @if(session('success'))
        <div class="flash flash-success">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error') || $error)
        <div class="error-list">
            <p style="display:flex; align-items:center; gap:8px; font-weight:600;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                {{ session('error') ?? $error }}
            </p>
        </div>
    @endif

    @if(!$isConfigured)
        <div class="card" style="margin-bottom:24px; border-color:rgba(245,158,11,0.3); background:rgba(245,158,11,0.04);">
            <div class="card-body" style="display:flex; align-items:flex-start; gap:16px;">
                <div style="width:44px; height:44px; border-radius:12px; background:var(--amber-soft); color:var(--amber); display:flex; align-items:center; justify-content:center; font-size:22px; flex-shrink:0;">
                    ⚠️
                </div>
                <div>
                    <h3 style="font-size:15px; font-weight:700; color:var(--amber); margin-bottom:4px;">Cloudflare API Credentials Belum Dikonfigurasi</h3>
                    <p style="font-size:13px; color:var(--text-secondary); line-height:1.6; margin-bottom:12px;">
                        Untuk mengaktifkan fitur manajemen domain & DNS Cloudflare, silakan tambahkan kredensial API berikut ke file <code>.env</code> aplikasi Anda:
                    </p>
                    <div style="background:var(--bg-base); padding:12px 16px; border-radius:8px; border:1px solid var(--border); font-family:'JetBrains Mono', monospace; font-size:12px; color:#a09af8; margin-bottom:12px;">
                        CLOUDFLARE_API_TOKEN=token_api_cloudflare_anda<br>
                        CLOUDFLARE_ACCOUNT_ID=account_id_cloudflare_anda
                    </div>
                    <p style="font-size:12px; color:var(--text-muted);">
                        Anda dapat membuat API Token dari Dashboard Cloudflare &gt; <strong>My Profile &gt; API Tokens &gt; Create Token</strong> (Pilih template <em>Edit zone DNS</em> atau buat custom token dengan izin Zone &amp; DNS Edit).
                    </p>
                </div>
            </div>
        </div>
    @else

        {{-- Stats Grid --}}
        @php
            $activeCount = count(array_filter($zones, fn($z) => ($z['status'] ?? '') === 'active'));
            $pendingCount = count(array_filter($zones, fn($z) => ($z['status'] ?? '') === 'pending'));
            $warningCount = count(array_filter($zones, fn($z) => in_array($z['expiration']['status'] ?? '', ['warning', 'expired'])));
        @endphp
        <div class="stats-grid">
            <div class="stat-card accent">
                <div class="stat-top">
                    <span class="stat-label">Total Zones/Domain</span>
                    <div class="stat-icon accent" style="color:var(--accent);">🌐</div>
                </div>
                <div class="stat-value">{{ count($zones) }}</div>
                <div class="stat-meta">Domain terdaftar di account</div>
            </div>

            <div class="stat-card green">
                <div class="stat-top">
                    <span class="stat-label">Active Domains</span>
                    <div class="stat-icon green" style="color:var(--green);">✅</div>
                </div>
                <div class="stat-value">{{ $activeCount }}</div>
                <div class="stat-meta">Status NS Aktif & Proxied</div>
            </div>

            <div class="stat-card amber">
                <div class="stat-top">
                    <span class="stat-label">Pending NS</span>
                    <div class="stat-icon amber" style="color:var(--amber);">⏳</div>
                </div>
                <div class="stat-value">{{ $pendingCount }}</div>
                <div class="stat-meta">Menunggu perubahan Nameserver</div>
            </div>

            <div class="stat-card {{ $warningCount > 0 ? 'rose' : 'purple' }}">
                <div class="stat-top">
                    <span class="stat-label">Domain Expired Alert</span>
                    <div class="stat-icon {{ $warningCount > 0 ? 'rose' : 'purple' }}" style="color:{{ $warningCount > 0 ? 'var(--rose)' : '#a09af8' }};">📅</div>
                </div>
                <div class="stat-value">{{ $warningCount }}</div>
                <div class="stat-meta">Domain &lt; 60 hari / expired</div>
            </div>
        </div>

        {{-- Main Data Table --}}
        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Daftar Zone / Domain</h2>
                    <p class="card-subtitle">Semua domain yang terhubung dengan akun Cloudflare Anda</p>
                </div>

                <form method="GET" action="{{ route('admin.cloudflare-zones.index') }}" style="display:flex; gap:8px;">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari domain..." class="form-input" style="width:200px; padding:7px 12px; font-size:12.5px; background:var(--bg-elevated); border:1px solid var(--border); border-radius:8px; color:var(--text-primary);">
                    <button type="submit" class="btn btn-secondary" style="background:var(--bg-hover); color:var(--text-primary); border:1px solid var(--border); border-radius:8px; padding:7px 14px;">Cari</button>
                    <a href="{{ route('admin.cloudflare-zones.index', array_merge(request()->query(), ['refresh_exp' => 1])) }}" class="btn btn-secondary" style="background:var(--bg-elevated); color:var(--text-muted); border:1px solid var(--border); border-radius:8px; padding:7px 12px; font-size:12px; text-decoration:none; display:inline-flex; align-items:center; gap:4px;" title="Refresh WHOIS/RDAP Expiration Data">
                        🔄 Refresh
                    </a>
                </form>
            </div>

            <div class="card-body" style="padding:0;">
                @if(empty($zones))
                    <div class="empty-state">
                        <div class="empty-state-icon">🌐</div>
                        <div class="empty-state-text">Belum ada domain/zone di akun Cloudflare ini.</div>
                        <button onclick="openAddModal()" class="btn btn-primary" style="margin-top:14px; background:var(--accent); color:#fff; border-radius:8px; padding:8px 16px;">+ Tambah Domain Pertama</button>
                    </div>
                @else
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nama Domain</th>
                                <th>Expired Domain</th>
                                <th>Status</th>
                                <th>Plan</th>
                                <th>Name Servers (NS)</th>
                                <!-- <th>Development Mode</th> -->
                                <th style="text-align:right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($zones as $z)
                                <tr>
                                    <td>
                                        <div style="font-weight:700; color:var(--text-primary); font-size:14px;">
                                            <a href="{{ route('admin.cloudflare-zones.show', $z['id']) }}" style="color:var(--text-primary); text-decoration:none;" onmouseover="this.style.color='#f57c00'" onmouseout="this.style.color='var(--text-primary)'">
                                                {{ $z['name'] }} ↗
                                            </a>
                                        </div>
                                        <div style="font-size:11px; color:var(--text-muted); font-family:'JetBrains Mono', monospace; margin-top:2px;">ID: {{ $z['id'] }}</div>
                                    </td>
                                    <td>
                                        @php
                                            $exp = $z['expiration'] ?? [];
                                        @endphp
                                        @if(!empty($exp['formatted']) && $exp['formatted'] !== 'Tidak Terdeteksi')
                                            <div style="font-weight:600; color:var(--text-primary); font-size:13px; display:flex; align-items:center; gap:6px;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--text-muted);">
                                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                                </svg>
                                                {{ $exp['formatted'] }}
                                            </div>
                                            <div style="margin-top:4px;">
                                                <span class="badge {{ $exp['badge_class'] ?? 'badge-gray' }}" style="font-size:11px;">
                                                    <span class="badge-dot"></span> {{ $exp['human'] }}
                                                </span>
                                            </div>
                                        @else
                                            <span style="color:var(--text-muted); font-size:12px;">Tidak Terdeteksi</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(($z['status'] ?? '') === 'active')
                                            <span class="badge badge-green"><span class="badge-dot"></span> Active</span>
                                        @elseif(($z['status'] ?? '') === 'pending')
                                            <span class="badge badge-amber"><span class="badge-dot"></span> Pending NS</span>
                                        @else
                                            <span class="badge badge-rose"><span class="badge-dot"></span> {{ ucfirst($z['status'] ?? 'Unknown') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span style="font-size:12px; font-weight:600; color:var(--text-secondary); background:var(--bg-elevated); padding:3px 8px; border-radius:6px; border:1px solid var(--border); white-space:nowrap;">
                                            {{ trim(str_ireplace('website', '', $z['plan']['name'] ?? 'Free')) ?: 'Free' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(!empty($z['name_servers']))
                                            <div style="display:flex; flex-direction:column; gap:2px; font-size:11.5px; font-family:'JetBrains Mono', monospace; color:var(--text-secondary);">
                                                @foreach($z['name_servers'] as $ns)
                                                    <div>🔹 {{ $ns }}</div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span style="color:var(--text-muted); font-size:12px;">Default / Managed</span>
                                        @endif
                                    </td>
                                    <!-- <td>
                                        @if(!empty($z['development_mode']) && $z['development_mode'] > 0)
                                            <span class="badge badge-amber">⚡ ON</span>
                                        @else
                                            <span style="color:var(--text-muted); font-size:12px;">OFF</span>
                                        @endif
                                    </td> -->
                                    <td style="text-align:right;">
                                        <div style="display:inline-flex; gap:6px;">
                                            <a href="{{ route('admin.cloudflare-zones.show', $z['id']) }}" class="btn btn-sm" style="background:rgba(245, 124, 0, 0.12); color:#f57c00; border:1px solid rgba(245, 124, 0, 0.25); border-radius:8px; padding:6px 12px; font-size:12px; font-weight:600; text-decoration:none;">
                                                ⚙️ Kelola DNS &amp; Fitur
                                            </a>

                                            <form method="POST" action="{{ route('admin.cloudflare-zones.destroy', $z['id']) }}" class="delete-form" data-confirm-title="Hapus Zone Cloudflare" data-confirm-text="Apakah Anda yakin ingin menghapus zone {{ $z['name'] }} dari Cloudflare?" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm" style="background:var(--rose-soft); color:var(--rose); border:1px solid rgba(244,63,94,0.25); border-radius:8px; padding:6px 10px; font-size:12px;" title="Hapus Zone">
                                                    🗑️
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

    @endif

    {{-- Modal Tambah Domain --}}
    <div id="addZoneModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); backdrop-filter:blur(6px); z-index:100; align-items:center; justify-content:center;">
        <div class="card" style="width:100%; max-width:480px; background:var(--bg-surface); border:1px solid var(--border-light); border-radius:16px; box-shadow:0 24px 48px rgba(0,0,0,0.5); animation:modalPop 0.2s ease-out;">
            <div class="card-header">
                <h3 class="card-title" style="display:flex; align-items:center; gap:8px;">
                    <span>🌐</span> Tambah Domain Baru ke Cloudflare
                </h3>
                <button onclick="closeAddModal()" style="background:none; border:none; color:var(--text-muted); font-size:20px; cursor:pointer;">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.cloudflare-zones.store') }}">
                @csrf
                <div class="card-body">
                    <p style="font-size:12.5px; color:var(--text-secondary); margin-bottom:16px; line-height:1.5;">
                        Masukkan nama domain utama Anda. Cloudflare akan otomatis mengimpor DNS record yang ada dan memberikan 2 Name Servers (NS).
                    </p>

                    <div style="margin-bottom:16px;">
                        <label style="display:block; font-size:12px; font-weight:600; color:var(--text-secondary); margin-bottom:6px;">Nama Domain (Zone Name)</label>
                        <input type="text" name="name" placeholder="contoh: domainanda.com" required class="form-input" style="width:100%; padding:10px 14px; background:var(--bg-base); border:1px solid var(--border); border-radius:9px; color:var(--text-primary); font-size:13.5px;">
                        <span style="font-size:11px; color:var(--text-muted); margin-top:4px; display:block;">Jangan sertakan www atau http://</span>
                    </div>
                </div>
                <div style="padding:16px 22px; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:10px; background:var(--bg-elevated);">
                    <button type="button" onclick="closeAddModal()" class="btn btn-ghost" style="background:transparent; color:var(--text-secondary); border:1px solid var(--border); padding:8px 16px; border-radius:8px;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="background:linear-gradient(135deg, #f57c00, #ff9800); color:#fff; border:none; padding:8px 18px; border-radius:8px; font-weight:600;">+ Tambah Domain</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('addZoneModal').style.display = 'flex';
        }
        function closeAddModal() {
            document.getElementById('addZoneModal').style.display = 'none';
        }
    </script>
</x-admin-layout>
