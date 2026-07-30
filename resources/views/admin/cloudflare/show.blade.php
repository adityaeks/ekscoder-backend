<x-admin-layout :title="'Manage ' . $zone['name']" breadcrumb="Cloudflare / {{ $zone['name'] }}">
    {{-- Header Navigation & Zone Title --}}
    <div style="margin-bottom:24px;">
        <div style="margin-bottom:12px;">
            <a href="{{ route('admin.cloudflare-zones.index') }}" style="color:var(--text-secondary); text-decoration:none; font-size:12.5px; display:inline-flex; align-items:center; gap:6px;">
                ← Kembali ke Daftar Domain
            </a>
        </div>
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px;">
            <div>
                <h1 style="font-size:24px; font-weight:800; color:var(--text-primary); letter-spacing:-0.5px; display:flex; align-items:center; gap:12px;">
                    🌐 {{ $zone['name'] }}
                    @if(($zone['status'] ?? '') === 'active')
                        <span class="badge badge-green" style="font-size:12px; padding:4px 10px;"><span class="badge-dot"></span> Active</span>
                    @elseif(($zone['status'] ?? '') === 'pending')
                        <span class="badge badge-amber" style="font-size:12px; padding:4px 10px;"><span class="badge-dot"></span> Pending NS</span>
                    @else
                        <span class="badge badge-rose" style="font-size:12px; padding:4px 10px;"><span class="badge-dot"></span> {{ ucfirst($zone['status'] ?? 'Unknown') }}</span>
                    @endif
                </h1>
                <p style="font-size:12.5px; color:var(--text-muted); margin-top:4px; font-family:'JetBrains Mono', monospace;">
                    Zone ID: {{ $zone['id'] }} &bull; Plan: {{ trim(str_ireplace('website', '', $zone['plan']['name'] ?? 'Free')) ?: 'Free' }}
                </p>
            </div>

            <div style="display:flex; gap:10px;">
                <button onclick="openAddDnsModal()" class="topbar-btn topbar-btn-primary" style="background:linear-gradient(135deg, #f57c00, #ff9800); border:none; padding:9px 16px; font-size:13px; border-radius:10px; box-shadow:0 0 20px rgba(245,124,0,0.25);">
                    + Tambah DNS Record
                </button>
            </div>
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

    @if(session('error'))
        <div class="error-list">
            <p>⚠️ {{ session('error') }}</p>
        </div>
    @endif

    @if(($zone['status'] ?? '') === 'pending')
        <div class="card" style="margin-bottom:24px; border-color:rgba(245,158,11,0.3); background:rgba(245,158,11,0.05);">
            <div class="card-body" style="display:flex; align-items:flex-start; gap:16px;">
                <div style="font-size:24px; color:var(--amber);">⏳</div>
                <div>
                    <h3 style="font-size:14px; font-weight:700; color:var(--amber); margin-bottom:4px;">Domain Menunggu Pengaturan Nameserver</h3>
                    <p style="font-size:12.5px; color:var(--text-secondary); line-height:1.5;">
                        Silakan atur Nameserver (NS) domain Anda di Registrar Domain (pendaftar domain) ke 2 server berikut:
                    </p>
                    <div style="display:flex; gap:12px; margin-top:8px;">
                        @foreach(($zone['name_servers'] ?? []) as $ns)
                            <span style="font-family:'JetBrains Mono', monospace; font-size:12px; background:var(--bg-base); padding:6px 12px; border-radius:6px; border:1px solid var(--border); color:#a09af8; font-weight:600;">
                                🔹 {{ $ns }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Tabs Container --}}
    <div style="display:flex; gap:8px; border-bottom:1px solid var(--border); margin-bottom:24px; overflow-x:auto;">
        <button class="tab-btn active" onclick="switchTab('dnsTab', this)" style="padding:10px 18px; font-size:13px; font-weight:600; color:var(--text-primary); background:none; border:none; border-bottom:2px solid #f57c00; cursor:pointer;">
            📡 DNS Records ({{ count($dnsRecords) }})
        </button>
        <button class="tab-btn" onclick="switchTab('cacheTab', this)" style="padding:10px 18px; font-size:13px; font-weight:600; color:var(--text-muted); background:none; border:none; border-bottom:2px solid transparent; cursor:pointer;">
            🧹 Cache Purge
        </button>
        <button class="tab-btn" onclick="switchTab('securityTab', this)" style="padding:10px 18px; font-size:13px; font-weight:600; color:var(--text-muted); background:none; border:none; border-bottom:2px solid transparent; cursor:pointer;">
            🔐 SSL &amp; Keamanan
        </button>
        <button class="tab-btn" onclick="switchTab('infoTab', this)" style="padding:10px 18px; font-size:13px; font-weight:600; color:var(--text-muted); background:none; border:none; border-bottom:2px solid transparent; cursor:pointer;">
            📋 Info Nameservers
        </button>
    </div>

    {{-- TAB 1: DNS RECORDS --}}
    <div id="dnsTab" class="tab-content" style="display:block;">
        {{-- DNS Quick Stats --}}
        @php
            $proxiedCount = count(array_filter($dnsRecords, fn($r) => !empty($r['proxied'])));
            $dnsOnlyCount = count($dnsRecords) - $proxiedCount;
        @endphp
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:12px; margin-bottom:20px;">
            <div style="background:var(--bg-surface); border:1px solid var(--border); border-radius:12px; padding:14px 18px; display:flex; align-items:center; justify-content:space-between;">
                <div>
                    <div style="font-size:11px; color:var(--text-muted); font-weight:600; text-transform:uppercase;">Orange Cloud (Proxied)</div>
                    <div style="font-size:22px; font-weight:800; color:#f57c00; margin-top:2px;">🟠 {{ $proxiedCount }}</div>
                </div>
                <div style="font-size:11px; color:var(--text-muted);">Protected by CF</div>
            </div>
            <div style="background:var(--bg-surface); border:1px solid var(--border); border-radius:12px; padding:14px 18px; display:flex; align-items:center; justify-content:space-between;">
                <div>
                    <div style="font-size:11px; color:var(--text-muted); font-weight:600; text-transform:uppercase;">Grey Cloud (DNS Only)</div>
                    <div style="font-size:22px; font-weight:800; color:var(--text-secondary); margin-top:2px;">⚪ {{ $dnsOnlyCount }}</div>
                </div>
                <div style="font-size:11px; color:var(--text-muted);">Direct IP Routing</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Daftar DNS Records</h2>
                    <p class="card-subtitle">Klik pada tombol Proxy Status untuk mengubah status Orange Cloud (Proxied / DNS Only)</p>
                </div>
                <button onclick="openAddDnsModal()" class="btn btn-sm" style="background:var(--accent-soft); color:#a09af8; border:1px solid rgba(108,99,255,0.3); border-radius:8px; padding:7px 14px; font-weight:600;">
                    + Tambah Record
                </button>
            </div>

            <div class="card-body" style="padding:0;">
                @if(empty($dnsRecords))
                    <div class="empty-state">
                        <div class="empty-state-icon">📡</div>
                        <div class="empty-state-text">Belum ada DNS record untuk domain ini.</div>
                        <button onclick="openAddDnsModal()" class="btn btn-primary" style="margin-top:12px;">+ Tambah DNS Record Pertama</button>
                    </div>
                @else
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Tipe</th>
                                <th>Nama Hostname</th>
                                <th>Nilai Target / IP Content</th>
                                <th>TTL</th>
                                <th>Status Proxy (Cloudflare)</th>
                                <th style="text-align:right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dnsRecords as $r)
                                <tr>
                                    <td>
                                        <span style="font-family:'JetBrains Mono', monospace; font-size:11.5px; font-weight:700; padding:4px 8px; border-radius:6px; background:var(--bg-elevated); border:1px solid var(--border); color:#a09af8;">
                                            {{ $r['type'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <div style="font-weight:700; color:var(--text-primary); font-size:13.5px; font-family:'JetBrains Mono', monospace;">
                                            {{ $r['name'] }}
                                        </div>
                                        @if(!empty($r['comment']))
                                            <div style="font-size:11px; color:var(--text-muted); font-style:italic;">💬 {{ $r['comment'] }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="font-size:13px; font-family:'JetBrains Mono', monospace; color:var(--text-secondary); max-width:280px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $r['content'] }}">
                                            {{ $r['content'] }}
                                        </div>
                                        @if(isset($r['priority']))
                                            <div style="font-size:10.5px; color:var(--text-muted);">Priority: {{ $r['priority'] }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <span style="font-size:12px; color:var(--text-muted);">
                                            {{ $r['ttl'] == 1 ? 'Auto' : $r['ttl'] . 's' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(in_array($r['type'], ['A', 'AAAA', 'CNAME']))
                                            <form method="POST" action="{{ route('admin.cloudflare-dns.toggle-proxy', [$zone['id'], $r['id']]) }}" style="display:inline;">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="proxied" value="{{ !empty($r['proxied']) ? '0' : '1' }}">
                                                @if(!empty($r['proxied']))
                                                    <button type="submit" class="btn btn-sm" style="background:rgba(245, 124, 0, 0.15); color:#f57c00; border:1px solid rgba(245, 124, 0, 0.35); border-radius:20px; padding:4px 12px; font-size:11.5px; font-weight:700; cursor:pointer;" title="Klik untuk ubah jadi DNS Only (Grey Cloud)">
                                                        🟠 Proxied (Orange)
                                                    </button>
                                                @else
                                                    <button type="submit" class="btn btn-sm" style="background:var(--bg-elevated); color:var(--text-muted); border:1px solid var(--border-light); border-radius:20px; padding:4px 12px; font-size:11.5px; font-weight:600; cursor:pointer;" title="Klik untuk ubah jadi Proxied (Orange Cloud)">
                                                        ⚪ DNS Only (Grey)
                                                    </button>
                                                @endif
                                            </form>
                                        @else
                                            <span style="font-size:11px; color:var(--text-muted);">Not eligible</span>
                                        @endif
                                    </td>
                                    <td style="text-align:right;">
                                        <div style="display:inline-flex; gap:6px;">
                                            <button onclick='openEditDnsModal(@json($r))' class="btn btn-sm" style="background:var(--bg-elevated); color:var(--text-secondary); border:1px solid var(--border); border-radius:8px; padding:5px 9px; font-size:12px;">
                                                ✏️ Edit
                                            </button>
                                            <form method="POST" action="{{ route('admin.cloudflare-dns.destroy', [$zone['id'], $r['id']]) }}" onsubmit="return confirm('Hapus DNS Record {{ $r['name'] }}?')" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm" style="background:var(--rose-soft); color:var(--rose); border:1px solid rgba(244,63,94,0.25); border-radius:8px; padding:5px 9px; font-size:12px;">
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
    </div>

    {{-- TAB 2: CACHE PURGE --}}
    <div id="cacheTab" class="tab-content" style="display:none;">
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(320px, 1fr)); gap:20px;">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" style="display:flex; align-items:center; gap:8px;">
                        <span>🧹</span> Purge Everything (Bersihkan Seluruh Cache)
                    </h3>
                </div>
                <div class="card-body">
                    <p style="font-size:13px; color:var(--text-secondary); line-height:1.6; margin-bottom:20px;">
                        Menghapus seluruh file statistik, HTML, CSS, JavaScript, dan media yang tersimpan di edge server Cloudflare secara instan.
                    </p>
                    <form method="POST" action="{{ route('admin.cloudflare-zones.purge-cache', $zone['id']) }}" onsubmit="return confirm('Apakah Anda yakin ingin membersihkan SELURUH cache untuk {{ $zone['name'] }}?')">
                        @csrf
                        <input type="hidden" name="purge_type" value="all">
                        <button type="submit" class="btn btn-primary" style="background:var(--rose); color:#fff; border:none; padding:10px 20px; border-radius:10px; font-weight:700; width:100%; justify-content:center;">
                            ⚡ Purge Everything Now
                        </button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" style="display:flex; align-items:center; gap:8px;">
                        <span>🎯</span> Purge Custom URLs (File Spesifik)
                    </h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.cloudflare-zones.purge-cache', $zone['id']) }}">
                        @csrf
                        <input type="hidden" name="purge_type" value="custom">
                        <div style="margin-bottom:14px;">
                            <label style="display:block; font-size:12px; font-weight:600; color:var(--text-secondary); margin-bottom:6px;">URL Berkas (Satu URL per baris)</label>
                            <textarea name="urls" rows="4" placeholder="https://{{ $zone['name'] }}/css/app.css&#10;https://{{ $zone['name'] }}/js/main.js" class="form-input" style="width:100%; padding:10px; background:var(--bg-base); border:1px solid var(--border); border-radius:8px; color:var(--text-primary); font-family:'JetBrains Mono', monospace; font-size:12px;"></textarea>
                        </div>
                        <button type="submit" class="btn btn-secondary" style="width:100%; justify-content:center; padding:10px; background:var(--bg-elevated); color:var(--text-primary); border:1px solid var(--border); border-radius:10px; font-weight:600;">
                            🧹 Purge Single/Custom URLs
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- TAB 3: SSL & SECURITY SETTINGS --}}
    <div id="securityTab" class="tab-content" style="display:none;">
        <div class="card" style="max-width:680px;">
            <div class="card-header">
                <h3 class="card-title">Pengaturan SSL/TLS &amp; Tingkat Keamanan (Security Level)</h3>
            </div>
            <form method="POST" action="{{ route('admin.cloudflare-zones.update-security', $zone['id']) }}">
                @csrf
                <div class="card-body">
                    {{-- SSL Mode --}}
                    <div style="margin-bottom:24px;">
                        <label style="display:block; font-size:13px; font-weight:700; color:var(--text-primary); margin-bottom:8px;">🔒 SSL/TLS Encryption Mode</label>
                        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(130px, 1fr)); gap:10px;">
                            @foreach(['off' => 'Off (Insecure)', 'flexible' => 'Flexible', 'full' => 'Full', 'strict' => 'Full (Strict)'] as $key => $label)
                                <label style="display:flex; align-items:center; gap:8px; padding:10px 14px; background:var(--bg-base); border:1px solid {{ $sslSetting === $key ? '#f57c00' : 'var(--border)' }}; border-radius:10px; cursor:pointer;">
                                    <input type="radio" name="ssl_mode" value="{{ $key }}" {{ $sslSetting === $key ? 'checked' : '' }}>
                                    <span style="font-size:12.5px; font-weight:600; color:var(--text-primary);">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        <p style="font-size:11.5px; color:var(--text-muted); margin-top:8px;">
                            Rekomendasi: <strong>Full (Strict)</strong> jika server web Anda memiliki sertifikat SSL valid, atau <strong>Flexible</strong> untuk mengizinkan HTTPS di Cloudflare ke HTTP origin server Anda.
                        </p>
                    </div>

                    {{-- Security Level --}}
                    <div>
                        <label style="display:block; font-size:13px; font-weight:700; color:var(--text-primary); margin-bottom:8px;">🛡️ Security Level (WAF &amp; Threat Defense)</label>
                        <select name="security_level" class="form-input" style="width:100%; padding:10px 14px; background:var(--bg-base); border:1px solid var(--border); border-radius:10px; color:var(--text-primary); font-size:13px;">
                            <option value="off" {{ $securityLevel === 'off' ? 'selected' : '' }}>Off - Tanpa Perlindungan</option>
                            <option value="essentially_off" {{ $securityLevel === 'essentially_off' ? 'selected' : '' }}>Essentially Off - Hanya ancaman terparah</option>
                            <option value="low" {{ $securityLevel === 'low' ? 'selected' : '' }}>Low - Menantang pengunjung mencurigakan</option>
                            <option value="medium" {{ $securityLevel === 'medium' ? 'selected' : '' }}>Medium - Standar perlindungan (Disarankan)</option>
                            <option value="high" {{ $securityLevel === 'high' ? 'selected' : '' }}>High - Perlindungan ekstra ketat</option>
                            <option value="under_attack" {{ $securityLevel === 'under_attack' ? 'selected' : '' }}>🚨 I'm Under Attack! - Tampilkan Captcha ke semua pengunjung</option>
                        </select>
                    </div>
                </div>

                <div style="padding:16px 22px; border-top:1px solid var(--border); background:var(--bg-elevated); display:flex; justify-content:flex-end;">
                    <button type="submit" class="btn btn-primary" style="background:linear-gradient(135deg, #f57c00, #ff9800); border:none; color:#fff; padding:10px 20px; border-radius:10px; font-weight:700;">
                        💾 Simpan Pengaturan Keamanan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- TAB 4: INFO NAMESERVERS --}}
    <div id="infoTab" class="tab-content" style="display:none;">
        <div class="card" style="max-width:600px;">
            <div class="card-header">
                <h3 class="card-title">Information &amp; Cloudflare Name Servers</h3>
            </div>
            <div class="card-body">
                <div style="margin-bottom:20px;">
                    <label style="font-size:12px; font-weight:600; color:var(--text-muted); display:block; margin-bottom:6px;">STATUS REGISTRASI DOMAIN</label>
                    <div style="font-size:16px; font-weight:700; color:var(--text-primary);">
                        {{ strtoupper($zone['status'] ?? 'N/A') }}
                    </div>
                </div>

                <div style="margin-bottom:20px;">
                    <label style="font-size:12px; font-weight:600; color:var(--text-muted); display:block; margin-bottom:6px;">NAMESERVERS YANG DIBERIKAN CLOUDFLARE</label>
                    <div style="display:flex; flex-direction:column; gap:8px;">
                        @foreach(($zone['name_servers'] ?? []) as $ns)
                            <div style="display:flex; align-items:center; justify-content:space-between; background:var(--bg-base); border:1px solid var(--border); padding:10px 14px; border-radius:8px; font-family:'JetBrains Mono', monospace; font-size:13px; color:#a09af8;">
                                <span>🔹 {{ $ns }}</span>
                                <button onclick="navigator.clipboard.writeText('{{ $ns }}'); alert('Copied: {{ $ns }}');" style="background:none; border:none; color:var(--text-muted); cursor:pointer; font-size:12px;">📋 Copy</button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL 1: TAMBAH DNS RECORD --}}
    <div id="addDnsModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); backdrop-filter:blur(6px); z-index:100; align-items:center; justify-content:center;">
        <div class="card" style="width:100%; max-width:520px; background:var(--bg-surface); border:1px solid var(--border-light); border-radius:16px; box-shadow:0 24px 48px rgba(0,0,0,0.5);">
            <div class="card-header">
                <h3 class="card-title">📡 Tambah DNS Record Baru</h3>
                <button onclick="closeAddDnsModal()" style="background:none; border:none; color:var(--text-muted); font-size:20px; cursor:pointer;">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.cloudflare-dns.store', $zone['id']) }}">
                @csrf
                <div class="card-body">
                    <div style="display:grid; grid-template-columns:1fr 2fr; gap:12px; margin-bottom:14px;">
                        <div>
                            <label style="display:block; font-size:12px; font-weight:600; color:var(--text-secondary); margin-bottom:4px;">Tipe Record</label>
                            <select name="type" class="form-input" style="width:100%; padding:9px; background:var(--bg-base); border:1px solid var(--border); border-radius:8px; color:var(--text-primary); font-weight:700;">
                                <option value="A">A</option>
                                <option value="AAAA">AAAA</option>
                                <option value="CNAME">CNAME</option>
                                <option value="TXT">TXT</option>
                                <option value="MX">MX</option>
                                <option value="NS">NS</option>
                            </select>
                        </div>
                        <div>
                            <label style="display:block; font-size:12px; font-weight:600; color:var(--text-secondary); margin-bottom:4px;">Name / Subdomain</label>
                            <input type="text" name="name" placeholder="@ atau sub" required class="form-input" style="width:100%; padding:9px; background:var(--bg-base); border:1px solid var(--border); border-radius:8px; color:var(--text-primary); font-family:'JetBrains Mono', monospace; font-size:13px;">
                        </div>
                    </div>

                    <div style="margin-bottom:14px;">
                        <label style="display:block; font-size:12px; font-weight:600; color:var(--text-secondary); margin-bottom:4px;">IPv4 Address / Content Target</label>
                        <input type="text" name="content" placeholder="192.0.2.1 atau domain.com" required class="form-input" style="width:100%; padding:9px; background:var(--bg-base); border:1px solid var(--border); border-radius:8px; color:var(--text-primary); font-family:'JetBrains Mono', monospace; font-size:13px;">
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:14px;">
                        <div>
                            <label style="display:block; font-size:12px; font-weight:600; color:var(--text-secondary); margin-bottom:4px;">TTL</label>
                            <select name="ttl" class="form-input" style="width:100%; padding:9px; background:var(--bg-base); border:1px solid var(--border); border-radius:8px; color:var(--text-primary);">
                                <option value="1">Auto</option>
                                <option value="60">1 min</option>
                                <option value="300">5 min</option>
                                <option value="3600">1 hour</option>
                            </select>
                        </div>
                        <div>
                            <label style="display:block; font-size:12px; font-weight:600; color:var(--text-secondary); margin-bottom:4px;">Priority (MX saja)</label>
                            <input type="number" name="priority" value="10" class="form-input" style="width:100%; padding:9px; background:var(--bg-base); border:1px solid var(--border); border-radius:8px; color:var(--text-primary);">
                        </div>
                    </div>

                    <div style="margin-bottom:16px;">
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer; background:var(--bg-base); padding:10px; border-radius:8px; border:1px solid var(--border);">
                            <input type="checkbox" name="proxied" value="1" checked>
                            <span style="font-size:13px; font-weight:700; color:#f57c00;">🟠 Proxy Status (Cloudflare Orange Cloud Active)</span>
                        </label>
                    </div>

                    <div>
                        <label style="display:block; font-size:12px; font-weight:600; color:var(--text-secondary); margin-bottom:4px;">Catatan (Opsional)</label>
                        <input type="text" name="comment" placeholder="Catatan internal..." class="form-input" style="width:100%; padding:8px; background:var(--bg-base); border:1px solid var(--border); border-radius:8px; color:var(--text-primary); font-size:12px;">
                    </div>
                </div>
                <div style="padding:14px 20px; border-top:1px solid var(--border); background:var(--bg-elevated); display:flex; justify-content:flex-end; gap:10px;">
                    <button type="button" onclick="closeAddDnsModal()" class="btn btn-ghost" style="border:1px solid var(--border); color:var(--text-secondary);">Batal</button>
                    <button type="submit" class="btn btn-primary" style="background:linear-gradient(135deg, #f57c00, #ff9800); border:none; color:#fff; font-weight:700; padding:8px 18px;">+ Simpan Record</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL 2: EDIT DNS RECORD --}}
    <div id="editDnsModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); backdrop-filter:blur(6px); z-index:100; align-items:center; justify-content:center;">
        <div class="card" style="width:100%; max-width:520px; background:var(--bg-surface); border:1px solid var(--border-light); border-radius:16px;">
            <div class="card-header">
                <h3 class="card-title">✏️ Edit DNS Record</h3>
                <button onclick="closeEditDnsModal()" style="background:none; border:none; color:var(--text-muted); font-size:20px; cursor:pointer;">&times;</button>
            </div>
            <form id="editDnsForm" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div style="display:grid; grid-template-columns:1fr 2fr; gap:12px; margin-bottom:14px;">
                        <div>
                            <label style="display:block; font-size:12px; font-weight:600; color:var(--text-secondary); margin-bottom:4px;">Tipe Record</label>
                            <select id="edit_type" name="type" class="form-input" style="width:100%; padding:9px; background:var(--bg-base); border:1px solid var(--border); border-radius:8px; color:var(--text-primary); font-weight:700;">
                                <option value="A">A</option>
                                <option value="AAAA">AAAA</option>
                                <option value="CNAME">CNAME</option>
                                <option value="TXT">TXT</option>
                                <option value="MX">MX</option>
                                <option value="NS">NS</option>
                            </select>
                        </div>
                        <div>
                            <label style="display:block; font-size:12px; font-weight:600; color:var(--text-secondary); margin-bottom:4px;">Name / Subdomain</label>
                            <input type="text" id="edit_name" name="name" required class="form-input" style="width:100%; padding:9px; background:var(--bg-base); border:1px solid var(--border); border-radius:8px; color:var(--text-primary); font-family:'JetBrains Mono', monospace; font-size:13px;">
                        </div>
                    </div>

                    <div style="margin-bottom:14px;">
                        <label style="display:block; font-size:12px; font-weight:600; color:var(--text-secondary); margin-bottom:4px;">Target Content</label>
                        <input type="text" id="edit_content" name="content" required class="form-input" style="width:100%; padding:9px; background:var(--bg-base); border:1px solid var(--border); border-radius:8px; color:var(--text-primary); font-family:'JetBrains Mono', monospace; font-size:13px;">
                    </div>

                    <div style="margin-bottom:16px;">
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer; background:var(--bg-base); padding:10px; border-radius:8px; border:1px solid var(--border);">
                            <input type="checkbox" id="edit_proxied" name="proxied" value="1">
                            <span style="font-size:13px; font-weight:700; color:#f57c00;">🟠 Proxy Status (Cloudflare Orange Cloud Active)</span>
                        </label>
                    </div>
                </div>
                <div style="padding:14px 20px; border-top:1px solid var(--border); background:var(--bg-elevated); display:flex; justify-content:flex-end; gap:10px;">
                    <button type="button" onclick="closeEditDnsModal()" class="btn btn-ghost" style="border:1px solid var(--border); color:var(--text-secondary);">Batal</button>
                    <button type="submit" class="btn btn-primary" style="background:linear-gradient(135deg, #f57c00, #ff9800); border:none; color:#fff; font-weight:700; padding:8px 18px;">Update Record</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function switchTab(tabId, btn) {
            document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.style.color = 'var(--text-muted)';
                el.style.borderBottomColor = 'transparent';
            });
            document.getElementById(tabId).style.display = 'block';
            btn.style.color = 'var(--text-primary)';
            btn.style.borderBottomColor = '#f57c00';
        }

        function openAddDnsModal() {
            document.getElementById('addDnsModal').style.display = 'flex';
        }
        function closeAddDnsModal() {
            document.getElementById('addDnsModal').style.display = 'none';
        }

        function openEditDnsModal(record) {
            document.getElementById('editDnsForm').action = "{{ route('admin.cloudflare-dns.update', [$zone['id'], ':record_id']) }}".replace(':record_id', record.id);
            document.getElementById('edit_type').value = record.type;
            document.getElementById('edit_name').value = record.name;
            document.getElementById('edit_content').value = record.content;
            document.getElementById('edit_proxied').checked = !!record.proxied;
            document.getElementById('editDnsModal').style.display = 'flex';
        }
        function closeEditDnsModal() {
            document.getElementById('editDnsModal').style.display = 'none';
        }
    </script>
</x-admin-layout>
