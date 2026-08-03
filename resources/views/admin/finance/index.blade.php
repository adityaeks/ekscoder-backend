<x-admin-layout title="Financial Management & Cashflow" breadcrumb="Keuangan & Ledger Transaksi Kas">

    <!-- Top Action Header & Quick Buttons -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 20px; font-weight: 700; color: var(--text-primary); margin: 0;">Laporan Kas & Transaksi Keuangan</h2>
            <p style="font-size: 13px; color: var(--text-muted); margin: 4px 0 0;">Monitoring arus kas masuk, pengeluaran operasional, serta otomatisasi pembayaran project.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            @can('finance.manage')
            <button onclick="openModal('modalCategory')" class="btn" style="background: rgba(255,255,255,0.08); color: var(--text-primary); border: 1px solid var(--border); border-radius: 8px; padding: 9px 16px; font-weight: 600; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Kelola Kategori
            </button>
            <button onclick="openModal('modalTransaction')" class="btn" style="background: var(--accent); color: #000; font-weight: 700; border-radius: 8px; padding: 9px 18px; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 0 15px var(--accent-glow);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Catat Transaksi Kas
            </button>
            @endcan
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.4); color: #10b981; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 13.5px; display: flex; align-items: center; gap: 10px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        {{ session('success') }}
    </div>
    @endif

    <!-- Top Financial Metrics Grid -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <!-- Saldo Kas Utama -->
        <div class="stat-card accent" style="background: var(--bg-card); border: 1px solid var(--border); padding: 20px; border-radius: 14px; position: relative; overflow: hidden;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                <span style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.8px; color: var(--text-muted);">Total Saldo Kas</span>
                <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(184, 255, 0, 0.15); color: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 18px;">
                    💰
                </div>
            </div>
            @php $totalBalanceColor = $totalBalance >= 0 ? 'var(--accent)' : '#ef4444'; @endphp
            <div style="font-size: 24px; font-weight: 800; color: {{ $totalBalanceColor }};">
                Rp {{ number_format($totalBalance, 0, ',', '.') }}
            </div>
            <div style="font-size: 11px; color: var(--text-muted); margin-top: 6px;">Accumulated net cash balance</div>
        </div>

        <!-- Pemasukan Bulan Ini -->
        <div class="stat-card green" style="background: var(--bg-card); border: 1px solid var(--border); padding: 20px; border-radius: 14px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                <span style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.8px; color: var(--text-muted);">Pemasukan (Bulan Ini)</span>
                <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(16, 185, 129, 0.15); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                    📈
                </div>
            </div>
            <div style="font-size: 24px; font-weight: 800; color: #10b981;">
                Rp {{ number_format($monthlyIncome, 0, ',', '.') }}
            </div>
            <div style="font-size: 11px; color: var(--text-muted); margin-top: 6px;">Total pemasukan periode {{ now()->translatedFormat('F Y') }}</div>
        </div>

        <!-- Pengeluaran Bulan Ini -->
        <div class="stat-card red" style="background: var(--bg-card); border: 1px solid var(--border); padding: 20px; border-radius: 14px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                <span style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.8px; color: var(--text-muted);">Pengeluaran (Bulan Ini)</span>
                <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(239, 68, 68, 0.15); color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                    📉
                </div>
            </div>
            <div style="font-size: 24px; font-weight: 800; color: #ef4444;">
                Rp {{ number_format($monthlyExpense, 0, ',', '.') }}
            </div>
            <div style="font-size: 11px; color: var(--text-muted); margin-top: 6px;">Total pengeluaran operasional bulan ini</div>
        </div>

        <!-- Estimasi Piutang Client -->
        <div class="stat-card cyan" style="background: var(--bg-card); border: 1px solid var(--border); padding: 20px; border-radius: 14px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                <span style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.8px; color: var(--text-muted);">Piutang Client (Pending)</span>
                <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(6, 182, 212, 0.15); color: #06b6d4; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                    ⏳
                </div>
            </div>
            <div style="font-size: 24px; font-weight: 800; color: #06b6d4;">
                Rp {{ number_format($unpaidReceivables, 0, ',', '.') }}
            </div>
            <div style="font-size: 11px; color: var(--text-muted); margin-top: 6px;">Sisa tagihan berjalan pada Project Orders</div>
        </div>
    </div>

    <!-- Financial Analytics Collapsible Card -->
    <div id="financialChartsCard" style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 14px; margin-bottom: 24px; overflow: hidden; transition: all 0.25s ease;">
        <!-- Card Header (Clickable Toggle) -->
        <div onclick="toggleFinancialCharts()" style="padding: 16px 20px; background: rgba(255, 255, 255, 0.02); cursor: pointer; display: flex; justify-content: space-between; align-items: center; user-select: none; transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.background='rgba(255,255,255,0.02)'">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 34px; height: 34px; border-radius: 10px; background: rgba(184, 255, 0, 0.12); color: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 16px;">
                    📈
                </div>
                <div>
                    <h3 style="font-size: 15px; font-weight: 700; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 8px;">
                        Grafik & Analisis Keuangan
                    </h3>
                    <p style="font-size: 12px; color: var(--text-muted); margin: 2px 0 0;">Tren cashflow bulanan dan distribusi pengeluaran berkategori</p>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <span id="chartsToggleText" style="font-size: 12px; font-weight: 600; color: var(--text-muted); background: rgba(255,255,255,0.06); padding: 4px 12px; border-radius: 20px;">Tutup</span>
                <div id="chartsToggleIcon" style="width: 30px; height: 30px; border-radius: 50%; background: rgba(255,255,255,0.06); display: flex; align-items: center; justify-content: center; color: var(--text-primary); transition: transform 0.3s ease;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card Body (Charts Content) -->
        <div id="financialChartsBody" style="padding: 20px; border-top: 1px solid var(--border); transition: all 0.3s ease;">
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
                <!-- Chart 1: Cashflow Trend -->
                <div style="background: rgba(0, 0, 0, 0.2); border: 1px solid var(--border); border-radius: 12px; padding: 18px;">
                    <h4 style="font-size: 14px; font-weight: 700; color: var(--text-primary); margin: 0 0 14px; display: flex; align-items: center; gap: 8px;">
                        📊 Tren Cashflow (Pemasukan vs Pengeluaran)
                    </h4>
                    <div style="position: relative; height: 260px;">
                        <canvas id="cashflowChart" data-labels="{{ json_encode($chartLabels) }}" data-income="{{ json_encode($chartIncome) }}" data-expense="{{ json_encode($chartExpense) }}"></canvas>
                    </div>
                </div>

                <!-- Chart 2: Category Breakdown -->
                <div style="background: rgba(0, 0, 0, 0.2); border: 1px solid var(--border); border-radius: 12px; padding: 18px;">
                    <h4 style="font-size: 14px; font-weight: 700; color: var(--text-primary); margin: 0 0 14px; display: flex; align-items: center; gap: 8px;">
                        🍕 Distribusi Pengeluaran
                    </h4>
                    <div style="position: relative; height: 260px; display: flex; align-items: center; justify-content: center;">
                        @if(count($expenseCategories) > 0)
                            <canvas id="categoryChart" data-names="{{ json_encode($expenseCategories->pluck('name')) }}" data-sums="{{ json_encode($expenseCategories->pluck('transactions_sum_amount')) }}" data-colors="{{ json_encode($expenseCategories->pluck('color')->map(fn($c) => $c ?? '#8b5cf6')) }}"></canvas>
                        @else
                            <div style="text-align: center; color: var(--text-muted); font-size: 13px;">Belum ada data pengeluaran berkategori</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 14px; padding: 18px; margin-bottom: 20px;">
        <form method="GET" action="{{ route('admin.finance.index') }}" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
            <!-- Search Keyword -->
            <div style="flex: 1; min-width: 200px;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari transaksi, kode, atau catatan..." style="width: 100%; background: var(--bg-dark, #0d1117); border: 1px solid var(--border); color: var(--text-primary); border-radius: 8px; padding: 9px 12px; font-size: 13px;">
            </div>

            <!-- Type Filter -->
            <div style="width: 150px;">
                <select name="type" style="width: 100%; background: var(--bg-dark, #0d1117); border: 1px solid var(--border); color: var(--text-primary); border-radius: 8px; padding: 9px 12px; font-size: 13px;">
                    <option value="">Semua Tipe</option>
                    <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>🟢 Pemasukan</option>
                    <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>🔴 Pengeluaran</option>
                </select>
            </div>

            <!-- Category Filter -->
            <div style="width: 180px;">
                <select name="category_id" style="width: 100%; background: var(--bg-dark, #0d1117); border: 1px solid var(--border); color: var(--text-primary); border-radius: 8px; padding: 9px 12px; font-size: 13px;">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->type == 'income' ? '[+]' : '[-]' }} {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Submit & Reset Buttons -->
            <button type="submit" style="background: var(--accent); color: #000; border: none; border-radius: 8px; padding: 9px 16px; font-weight: 700; font-size: 13px; cursor: pointer;">
                Filter
            </button>
            @if(request()->hasAny(['search', 'type', 'category_id', 'date_from', 'date_to']))
                <a href="{{ route('admin.finance.index') }}" style="background: rgba(255,255,255,0.08); color: var(--text-muted); border: 1px solid var(--border); border-radius: 8px; padding: 9px 14px; font-size: 13px; text-decoration: none;">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Transactions Data Table -->
    <div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 14px; overflow: hidden;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 15px; font-weight: 700; color: var(--text-primary); margin: 0;">Riwayat Transaksi Kas</h3>
            <span style="font-size: 12px; color: var(--text-muted);">Total {{ $transactions->total() }} Record</span>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13.5px;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border); background: rgba(255,255,255,0.02); color: var(--text-muted); font-size: 11px; text-transform: uppercase; letter-spacing: 0.8px;">
                        <th style="padding: 12px 18px;">Kode & Tanggal</th>
                        <th style="padding: 12px 18px;">Judul / Transaksi</th>
                        <th style="padding: 12px 18px;">Tipe</th>
                        <th style="padding: 12px 18px;">Kategori</th>
                        <th style="padding: 12px 18px; text-align: right;">Nominal</th>
                        <th style="padding: 12px 18px;">Sumber / Order</th>
                        <th style="padding: 12px 18px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $trx)
                        <tr style="border-bottom: 1px solid var(--border); transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 14px 18px;">
                                <div style="font-weight: 700; font-family: monospace; color: var(--text-primary);">{{ $trx->transaction_code }}</div>
                                <div style="font-size: 11px; color: var(--text-muted);">{{ $trx->transaction_date ? $trx->transaction_date->format('d M Y') : '-' }}</div>
                            </td>
                            <td style="padding: 14px 18px;">
                                <div style="font-weight: 600; color: var(--text-primary);">{{ $trx->title }}</div>
                                @if($trx->notes)
                                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;">{{ Str::limit($trx->notes, 60) }}</div>
                                @endif
                            </td>
                            <td style="padding: 14px 18px; white-space: nowrap;">
                                @if($trx->type == 'income')
                                    <span style="display: inline-flex; align-items: center; white-space: nowrap; background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700;">+ Pemasukan</span>
                                @else
                                    <span style="display: inline-flex; align-items: center; white-space: nowrap; background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700;">- Pengeluaran</span>
                                @endif
                            </td>
                            <td style="padding: 14px 18px;">
                                @if($trx->category)
                                    @php $catColor = $trx->category->color ?? '#94a3b8'; @endphp
                                    <span style="display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 500; color: var(--text-primary);">
                                        <span style="width: 8px; height: 8px; border-radius: 50%; background: {{ $catColor }};"></span>
                                        {{ $trx->category->name }}
                                    </span>
                                @else
                                    <span style="font-size: 12px; color: var(--text-muted);">-</span>
                                @endif
                            </td>
                            @php $trxAmountColor = $trx->type == 'income' ? '#10b981' : '#ef4444'; @endphp
                            <td style="padding: 14px 18px; text-align: right; font-weight: 800; font-size: 14px; color: {{ $trxAmountColor }};">
                                {{ $trx->type == 'income' ? '+' : '-' }} Rp {{ number_format($trx->amount, 0, ',', '.') }}
                            </td>
                            <td style="padding: 14px 18px; font-size: 12px;">
                                @if($trx->projectOrder)
                                    <a href="{{ route('admin.orders.index') }}" style="color: var(--accent); text-decoration: none; font-weight: 600;">
                                        📦 Order #{{ $trx->projectOrder->id }}
                                    </a>
                                @else
                                    <span style="color: var(--text-muted);">Manual Input</span>
                                @endif
                            </td>
                            <td style="padding: 14px 18px; text-align: center;">
                                @can('finance.manage')
                                <form action="{{ route('admin.finance.destroy', $trx) }}" method="POST" class="delete-form" data-confirm-title="Hapus Transaksi Kas" data-confirm-text="Apakah Anda yakin ingin menghapus transaksi '{{ $trx->title }}' ({{ $trx->transaction_code }})?" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: rgba(239, 68, 68, 0.12); color: #ef4444; border: none; padding: 6px 10px; border-radius: 6px; cursor: pointer; font-size: 12px;" title="Hapus Transaksi">
                                        🗑️
                                    </button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding: 40px; text-align: center; color: var(--text-muted); font-size: 14px;">
                                Belum ada riwayat transaksi keuangan yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div style="padding: 16px 20px; border-top: 1px solid var(--border);">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

    <!-- Modal 1: Catat Transaksi Baru -->
    <div id="modalTransaction" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 100; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
        <div style="background: var(--bg-card, #161b22); border: 1px solid var(--border); width: 100%; max-width: 520px; border-radius: 16px; padding: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.5);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0;">Catat Transaksi Kas Baru</h3>
                <button onclick="closeModal('modalTransaction')" style="background: transparent; border: none; color: var(--text-muted); font-size: 20px; cursor: pointer;">&times;</button>
            </div>

            <form action="{{ route('admin.finance.store') }}" method="POST">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px;">Tipe Transaksi *</label>
                        <select name="type" required style="width: 100%; background: var(--bg-dark, #0d1117); border: 1px solid var(--border); color: var(--text-primary); border-radius: 8px; padding: 10px; font-size: 13px;">
                            <option value="expense">🔴 Pengeluaran (Kas Keluar)</option>
                            <option value="income">🟢 Pemasukan (Kas Masuk)</option>
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px;">Kategori *</label>
                        <select name="category_id" required style="width: 100%; background: var(--bg-dark, #0d1117); border: 1px solid var(--border); color: var(--text-primary); border-radius: 8px; padding: 10px; font-size: 13px;">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }} ({{ ucfirst($cat->type) }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 14px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px;">Judul Transaksi *</label>
                    <input type="text" name="title" required placeholder="Contoh: Perpanjang Server VPS DigitalOcean" style="width: 100%; background: var(--bg-dark, #0d1117); border: 1px solid var(--border); color: var(--text-primary); border-radius: 8px; padding: 10px; font-size: 13px;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px;">Nominal (Rp) *</label>
                        <input type="text" id="amount_display" required placeholder="50.000" oninput="formatRupiahLive(this)" style="width: 100%; background: var(--bg-dark, #0d1117); border: 1px solid var(--border); color: var(--text-primary); border-radius: 8px; padding: 10px; font-size: 13.5px; font-weight: 600;">
                        <input type="hidden" name="amount" id="amount_real" required>
                    </div>

                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px;">Tanggal Transaksi *</label>
                        <input type="date" name="transaction_date" required value="{{ date('Y-m-d') }}" style="width: 100%; background: var(--bg-dark, #0d1117); border: 1px solid var(--border); color: var(--text-primary); border-radius: 8px; padding: 10px; font-size: 13px;">
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px;">Catatan (Opsional)</label>
                    <textarea name="notes" rows="3" placeholder="Keterangan tambahan..." style="width: 100%; background: var(--bg-dark, #0d1117); border: 1px solid var(--border); color: var(--text-primary); border-radius: 8px; padding: 10px; font-size: 13px;"></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" onclick="closeModal('modalTransaction')" style="background: rgba(255,255,255,0.08); color: var(--text-muted); border: none; border-radius: 8px; padding: 10px 16px; font-size: 13px; cursor: pointer;">Batal</button>
                    <button type="submit" style="background: var(--accent); color: #000; border: none; border-radius: 8px; padding: 10px 20px; font-weight: 700; font-size: 13px; cursor: pointer;">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 2: Kelola Kategori -->
    <div id="modalCategory" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 100; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
        <div style="background: var(--bg-card, #161b22); border: 1px solid var(--border); width: 100%; max-width: 500px; border-radius: 16px; padding: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.5);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0;">Kelola Kategori Keuangan</h3>
                <button onclick="closeModal('modalCategory')" style="background: transparent; border: none; color: var(--text-muted); font-size: 20px; cursor: pointer;">&times;</button>
            </div>

            <!-- Form Tambah Kategori -->
            <form action="{{ route('admin.finance.categories.store') }}" method="POST" style="margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--border);">
                @csrf
                <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 10px; margin-bottom: 10px;">
                    <input type="text" name="name" required placeholder="Nama Kategori..." style="background: var(--bg-dark, #0d1117); border: 1px solid var(--border); color: var(--text-primary); border-radius: 8px; padding: 9px; font-size: 13px;">
                    <select name="type" required style="background: var(--bg-dark, #0d1117); border: 1px solid var(--border); color: var(--text-primary); border-radius: 8px; padding: 9px; font-size: 12px;">
                        <option value="expense">Pengeluaran</option>
                        <option value="income">Pemasukan</option>
                    </select>
                    <input type="color" name="color" value="#8b5cf6" style="height: 38px; width: 100%; background: transparent; border: 1px solid var(--border); border-radius: 8px; cursor: pointer;">
                </div>
                <button type="submit" style="width: 100%; background: rgba(184, 255, 0, 0.15); color: var(--accent); border: 1px solid var(--accent); border-radius: 8px; padding: 8px; font-weight: 700; font-size: 12.5px; cursor: pointer;">
                    + Tambah Kategori Baru
                </button>
            </form>

            <!-- List Kategori Eksisting -->
            <div style="max-height: 220px; overflow-y: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <tbody>
                        @foreach($categories as $cat)
                            <tr style="border-bottom: 1px solid var(--border);">
                                <td style="padding: 8px 0; display: flex; align-items: center; gap: 8px;">
                                    @php $categoryBgColor = $cat->color ?? '#94a3b8'; @endphp
                                    <span style="width: 10px; height: 10px; border-radius: 50%; background: {{ $categoryBgColor }};"></span>
                                    <span style="color: var(--text-primary); font-weight: 500;">{{ $cat->name }}</span>
                                </td>
                                <td style="padding: 8px 0; color: var(--text-muted); font-size: 11px;">
                                    {{ ucfirst($cat->type) }}
                                </td>
                                <td style="padding: 8px 0; text-align: right;">
                                    <form action="{{ route('admin.finance.categories.destroy', $cat) }}" method="POST" class="delete-form" data-confirm-title="Hapus Kategori Keuangan" data-confirm-text="Apakah Anda yakin ingin menghapus kategori '{{ $cat->name }}'?" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background: transparent; border: none; color: #ef4444; cursor: pointer; font-size: 12px;">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Chart.js CDN and Initialization Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function openModal(id) {
            document.getElementById(id).style.display = 'flex';
        }
        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        function formatRupiahLive(input) {
            let rawValue = input.value.replace(/\D/g, '');
            document.getElementById('amount_real').value = rawValue;
            if (rawValue) {
                input.value = new Intl.NumberFormat('id-ID').format(rawValue);
            } else {
                input.value = '';
            }
        }

        function toggleFinancialCharts() {
            const body = document.getElementById('financialChartsBody');
            const icon = document.getElementById('chartsToggleIcon');
            const text = document.getElementById('chartsToggleText');
            if (!body) return;

            const isHidden = body.style.display === 'none';
            if (isHidden) {
                body.style.display = 'block';
                if (icon) icon.style.transform = 'rotate(0deg)';
                if (text) text.textContent = 'Tutup';
                localStorage.setItem('ekscoder_finance_charts_collapsed', 'false');
            } else {
                body.style.display = 'none';
                if (icon) icon.style.transform = 'rotate(-90deg)';
                if (text) text.textContent = 'Buka';
                localStorage.setItem('ekscoder_finance_charts_collapsed', 'true');
            }
        }

        document.addEventListener("DOMContentLoaded", function () {
            // Restore saved collapsed state for financial charts card
            if (localStorage.getItem('ekscoder_finance_charts_collapsed') === 'true') {
                const body = document.getElementById('financialChartsBody');
                const icon = document.getElementById('chartsToggleIcon');
                const text = document.getElementById('chartsToggleText');
                if (body) body.style.display = 'none';
                if (icon) icon.style.transform = 'rotate(-90deg)';
                if (text) text.textContent = 'Buka';
            }

            // Chart 1: Cashflow Trend Line Bar
            const ctxCashflow = document.getElementById('cashflowChart');
            if (ctxCashflow) {
                const chartLabels = JSON.parse(ctxCashflow.dataset.labels || '[]');
                const chartIncome = JSON.parse(ctxCashflow.dataset.income || '[]');
                const chartExpense = JSON.parse(ctxCashflow.dataset.expense || '[]');

                new Chart(ctxCashflow.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: chartLabels,
                        datasets: [
                            {
                                label: 'Pemasukan (Rp)',
                                data: chartIncome,
                                backgroundColor: 'rgba(16, 185, 129, 0.7)',
                                borderColor: '#10b981',
                                borderWidth: 1,
                                borderRadius: 6
                            },
                            {
                                label: 'Pengeluaran (Rp)',
                                data: chartExpense,
                                backgroundColor: 'rgba(239, 68, 68, 0.7)',
                                borderColor: '#ef4444',
                                borderWidth: 1,
                                borderRadius: 6
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: { color: '#94a3b8', font: { size: 12 } }
                            }
                        },
                        scales: {
                            x: { grid: { display: false }, ticks: { color: '#94a3b8' } },
                            y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#94a3b8' } }
                        }
                    }
                });
            }

            // Chart 2: Expense Category Breakdown
            const ctxCategory = document.getElementById('categoryChart');
            if (ctxCategory) {
                const expenseCategoryNames = JSON.parse(ctxCategory.dataset.names || '[]');
                const expenseCategorySums = JSON.parse(ctxCategory.dataset.sums || '[]');
                const expenseCategoryColors = JSON.parse(ctxCategory.dataset.colors || '[]');

                if (expenseCategoryNames.length > 0) {
                    new Chart(ctxCategory.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: expenseCategoryNames,
                            datasets: [{
                                data: expenseCategorySums,
                                backgroundColor: expenseCategoryColors,
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: { color: '#94a3b8', font: { size: 11 } }
                                }
                            }
                        }
                    });
                }
            }
        });
    </script>
</x-admin-layout>
