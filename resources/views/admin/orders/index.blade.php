<x-admin-layout title="Project Orders" breadcrumb="Track client project pipeline, status & payments">
    <x-slot name="topbarAction">
        <button onclick="openCreateModal()" class="topbar-btn topbar-btn-primary">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Project Order
        </button>
    </x-slot>

    <!-- Financial Stats Bar -->
    <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 20px;">
        <div class="stat-card accent">
            <div class="stat-top">
                <div class="stat-label">Pipeline Value</div>
                <div class="stat-icon accent">💰</div>
            </div>
            <div class="stat-value" id="statPipelineValue" style="font-size:22px;">Rp {{ number_format($stats['total_pipeline'], 0, ',', '.') }}</div>
            <div class="stat-meta">Total gross contract value (excl. cancelled)</div>
        </div>

        <div class="stat-card green">
            <div class="stat-top">
                <div class="stat-label">Collected Payments</div>
                <div class="stat-icon green">💵</div>
            </div>
            <div class="stat-value" id="statTotalPaid" style="font-size:22px; color:var(--green)">Rp {{ number_format($stats['total_paid'], 0, ',', '.') }}</div>
            <div class="stat-meta">Received DP & full payments (excl. cancelled)</div>
        </div>

        <div class="stat-card cyan">
            <div class="stat-top">
                <div class="stat-label">Active Orders</div>
                <div class="stat-icon cyan">⚡</div>
            </div>
            <div class="stat-value" id="statActiveOrders">{{ $stats['active_orders'] }}</div>
            <div class="stat-meta">Ongoing projects in pipeline</div>
        </div>

        <div class="stat-card amber">
            <div class="stat-top">
                <div class="stat-label">Completed</div>
                <div class="stat-icon amber">🎉</div>
            </div>
            <div class="stat-value" id="statCompletedOrders">{{ $stats['completed_count'] }}</div>
            <div class="stat-meta">Finished and delivered</div>
        </div>
    </div>

    <!-- View Mode Switcher Control Bar (Below 4 Cards) -->
    <div class="control-toolbar" style="display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:20px; background:var(--bg-surface); border:1px solid var(--border); padding:10px 16px; border-radius:14px;">
        <div style="display:flex; align-items:center; gap:12px;">
            <div class="view-mode-toggle">
                <button type="button" id="btnModeBoard" onclick="switchViewMode('board')" class="mode-btn active" title="Kanban Board View">
                    BOARD
                </button>
                <button type="button" id="btnModeTable" onclick="switchViewMode('table')" class="mode-btn" title="Table Data View">
                    TABLE
                </button>
            </div>
            <span style="font-size:12px; color:var(--text-muted);">Switch between Drag & Drop Kanban Board and Tabular view</span>
        </div>
    </div>

    <!-- 1. KANBAN BOARD VIEW -->
    <div class="kanban-container" id="ordersBoardView">
        @foreach($statuses as $statusKey => $statusMeta)
            <div class="kanban-column" data-status="{{ $statusKey }}" ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)" ondrop="handleDrop(event, '{{ $statusKey }}')">
                <div class="kanban-column-header">
                    <div class="column-title">
                        <span>{{ $statusMeta['icon'] }}</span>
                        <span>{{ $statusMeta['label'] }}</span>
                    </div>
                    <span class="column-count" id="count-{{ $statusKey }}">{{ count($groupedOrders[$statusKey]) }}</span>
                </div>

                <div class="kanban-cards-wrapper" id="column-{{ $statusKey }}">
                    <div class="column-empty" id="empty-{{ $statusKey }}" style="display: {{ count($groupedOrders[$statusKey]) === 0 ? 'block' : 'none' }};">
                        Drag cards here
                    </div>

                    @foreach($groupedOrders[$statusKey] as $order)
                        <div class="kanban-card" 
                             id="order-card-{{ $order->id }}"
                             draggable="true" 
                             ondragstart="handleDragStart(event, {{ $order->id }}, '{{ $order->status }}')"
                             ondragend="handleDragEnd(event)"
                             data-id="{{ $order->id }}"
                             data-title="{{ $order->title }}"
                             data-client-name="{{ $order->client_name }}"
                             data-client-contact="{{ $order->client_contact }}"
                             data-budget="{{ $order->budget }}"
                             data-paid-amount="{{ $order->paid_amount }}"
                             data-status="{{ $order->status }}"
                             data-priority="{{ $order->priority }}"
                             data-start-date="{{ $order->start_date ? $order->start_date->format('Y-m-d') : '' }}"
                             data-deadline="{{ $order->deadline ? $order->deadline->format('Y-m-d') : '' }}"
                             data-description="{{ $order->description }}">
                            
                            <!-- Card Header: Priority & Action -->
                            <div class="card-top">
                                @if($order->priority === 'high')
                                    <span class="badge badge-rose">HIGH</span>
                                @elseif($order->priority === 'medium')
                                    <span class="badge badge-amber">MEDIUM</span>
                                @else
                                    <span class="badge badge-cyan">LOW</span>
                                @endif

                                <div class="card-menu">
                                    <button class="card-menu-btn" onclick="openEditModalFromCard({{ $order->id }})">✏️ Edit</button>
                                </div>
                            </div>

                            <!-- Card Main Content -->
                            <div class="card-project-title">{{ $order->title }}</div>
                            <div class="card-client">👤 {{ $order->client_name }}</div>

                            @if($order->description)
                                <div class="card-desc">{{ Str::limit($order->description, 80) }}</div>
                            @endif

                            <!-- Financial Progress -->
                            <div class="card-financials">
                                <div class="financial-row">
                                    <span>Value: <strong>{{ $order->formatted_budget }}</strong></span>
                                    <span>Paid: <strong style="color:var(--green)">{{ $order->formatted_paid }}</strong></span>
                                </div>
                                <div class="progress-bar-bg">
                                    <div class="progress-bar-fill" style="width: {{ $order->payment_progress }}%;"></div>
                                </div>
                            </div>

                            <!-- Card Footer: Target Mulai & Deadline -->
                            <div class="card-footer" style="flex-wrap:wrap; gap:6px;">
                                <div style="display:flex; flex-direction:column; gap:2px;">
                                    @if($order->start_date)
                                        <div class="card-date" style="font-size:10.5px; color:var(--text-secondary);">
                                            🚀 Mulai: <strong>{{ $order->start_date->format('d M Y') }}</strong>
                                        </div>
                                    @endif
                                    @if($order->deadline)
                                        <div class="card-date {{ $order->deadline->isPast() && $order->status !== 'completed' ? 'overdue' : '' }}" style="font-size:10.5px;">
                                            🏁 Deadline: <strong>{{ $order->deadline->format('d M Y') }}</strong>
                                        </div>
                                    @else
                                        <div class="card-date" style="font-size:10.5px;">🏁 Deadline: -</div>
                                    @endif
                                </div>

                                @if($order->client_contact)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->client_contact) }}" target="_blank" class="card-contact" onclick="event.stopPropagation()">
                                        💬 WhatsApp
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <button class="add-card-btn" onclick="openCreateModal('{{ $statusKey }}')">+ Add Card</button>
            </div>
        @endforeach
    </div>

    <!-- 2. TABLE DATA VIEW -->
    <div id="ordersTableView" class="card" style="display: none;">
        <x-datatable id="ordersDataTable" title="All Project Orders Table" subtitle="Tabular list view with inline status management & client info" search-placeholder="Search project, client, contact..." :per-page-options="[10, 20, 50, 'all']" :default-per-page="10">
            <x-slot:actions>
                <button onclick="openCreateModal()" class="topbar-btn topbar-btn-primary" style="padding:6px 12px; font-size:12px;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    New Project Order
                </button>
            </x-slot:actions>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:50px" data-sortable="true">No</th>
                        <th data-sortable="true">Project & Client</th>
                        <th data-sortable="true">Contact</th>
                        <th data-sortable="true">Budget & Payment</th>
                        <th data-sortable="true">Priority</th>
                        <th data-sortable="true">Status Stage</th>
                        <th data-sortable="true">Target Mulai</th>
                        <th data-sortable="true">Deadline</th>
                        <th style="text-align:right">Actions</th>
                    </tr>
                </thead>
                <tbody id="ordersTableBody">
                    @php $rowCounter = 1; @endphp
                    @foreach($statuses as $stKey => $stMeta)
                        @foreach($groupedOrders[$stKey] as $order)
                        <tr class="order-table-row">
                            <td>
                                <span class="datatable-row-index" style="font-family:'JetBrains Mono',monospace; font-weight:700; color:var(--text-muted); font-size:12px;">#{{ $rowCounter++ }}</span>
                            </td>
                            <td>
                                <div style="font-weight:700; color:var(--text-primary); font-size:13.5px; margin-bottom:2px;">{{ $order->title }}</div>
                                <div style="font-size:11.5px; color:var(--text-secondary);">👤 {{ $order->client_name }}</div>
                            </td>
                            <td>
                                @if($order->client_contact)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->client_contact) }}" target="_blank" style="color:#25d366; font-size:12px; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                                        💬 WhatsApp
                                    </a>
                                    <div style="font-size:10.5px; color:var(--text-muted); font-family:'JetBrains Mono',monospace;">{{ $order->client_contact }}</div>
                                @else
                                    <span style="color:var(--text-muted); font-size:12px;">—</span>
                                @endif
                            </td>
                            <td>
                                <div style="font-size:13px; font-weight:700; color:var(--text-primary);">{{ $order->formatted_budget }}</div>
                                <div style="font-size:11px; color:var(--green); font-weight:600; margin-top:2px;">
                                    Paid: {{ $order->formatted_paid }} ({{ $order->payment_progress }}%)
                                </div>
                            </td>
                            <td>
                                @if($order->priority === 'high')
                                    <span class="badge badge-rose">HIGH</span>
                                @elseif($order->priority === 'medium')
                                    <span class="badge badge-amber">MEDIUM</span>
                                @else
                                    <span class="badge badge-cyan">LOW</span>
                                @endif
                            </td>
                            <td>
                                <select onchange="quickUpdateStatus({{ $order->id }}, this.value)" class="form-select" style="padding:4px 8px; font-size:11.5px; width:auto; border-radius:7px; background:var(--bg-elevated); color:var(--text-primary); border-color:var(--border);">
                                    @foreach($statuses as $key => $meta)
                                        <option value="{{ $key }}" {{ $order->status === $key ? 'selected' : '' }}>
                                            {{ $meta['icon'] }} {{ $meta['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                @if($order->start_date)
                                    <span style="font-size:11.5px; color:var(--text-secondary); font-weight:500;">
                                        🚀 {{ $order->start_date->format('d M Y') }}
                                    </span>
                                @else
                                    <span style="color:var(--text-muted); font-size:11.5px;">—</span>
                                @endif
                            </td>
                            <td>
                                @if($order->deadline)
                                    <span style="font-size:11.5px; color:{{ $order->deadline->isPast() && $order->status !== 'completed' ? 'var(--rose)' : 'var(--text-muted)' }}; font-weight:{{ $order->deadline->isPast() && $order->status !== 'completed' ? '700' : '500' }};">
                                        🏁 {{ $order->deadline->format('d M Y') }}
                                    </span>
                                @else
                                    <span style="color:var(--text-muted); font-size:11.5px;">—</span>
                                @endif
                            </td>
                            <td style="text-align:right">
                                <button type="button" onclick="openEditModalFromCard({{ $order->id }})" class="btn btn-ghost" style="padding:4px 10px; font-size:11.5px;">Edit</button>
                            </td>
                        </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </x-datatable>
    </div>

    <!-- CREATE ORDER MODAL -->
    <x-admin-modal id="createModal" title="Create New Project Order">
        <form action="{{ route('admin.orders.store') }}" method="POST" onsubmit="cleanRupiahForm(this)">
            @csrf
            <div class="modal-body space-y-4">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Client Name *</label>
                        <input type="text" name="client_name" required placeholder="e.g. PT Maju Bersama / Budi" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Client Contact (WA/Email)</label>
                        <input type="text" name="client_contact" placeholder="e.g. 081234567890" class="form-input">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Project Title *</label>
                    <input type="text" name="title" required placeholder="e.g. E-Commerce Website & Payment Gateway" class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label">Description / Scope</label>
                    <textarea name="description" placeholder="Short scope of work or notes..." class="form-textarea"></textarea>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Total Budget *</label>
                        <div class="input-prefix-group">
                            <span class="input-prefix">Rp</span>
                            <input type="text" name="budget" id="create_budget" required placeholder="15.000.000" oninput="handleRupiahInput(this)" class="form-input prefixed">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">DP / Amount Paid</label>
                        <div class="input-prefix-group">
                            <span class="input-prefix">Rp</span>
                            <input type="text" name="paid_amount" id="create_paid_amount" placeholder="5.000.000" oninput="handleRupiahInput(this)" class="form-input prefixed">
                        </div>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Status Column *</label>
                        <select name="status" id="createStatusSelect" class="form-select">
                            <option value="requirement">📝 Requirement & DP</option>
                            <option value="in_progress">⚡ In Progress</option>
                            <option value="review">🔍 Review / Testing</option>
                            <option value="completed">✅ Completed</option>
                            <option value="cancelled">⛔ Cancelled / Hold</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Priority *</label>
                        <select name="priority" class="form-select">
                            <option value="medium">Medium</option>
                            <option value="high">High Priority</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Target Mulai</label>
                        <input type="date" name="start_date" id="create_start_date" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Target Deadline</label>
                        <input type="date" name="deadline" id="create_deadline" class="form-input">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('createModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Order Card</button>
            </div>
        </form>
    </x-admin-modal>

    <!-- EDIT ORDER MODAL -->
    <x-admin-modal id="editModal" title="✏️ Edit Project Order">
        <form id="editForm" method="POST" onsubmit="cleanRupiahForm(this)">
            @csrf
            @method('PUT')
            <div class="modal-body space-y-4">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Client Name *</label>
                        <input type="text" name="client_name" id="edit_client_name" required class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Client Contact</label>
                        <input type="text" name="client_contact" id="edit_client_contact" class="form-input">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Project Title *</label>
                    <input type="text" name="title" id="edit_title" required class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label">Description / Scope</label>
                    <textarea name="description" id="edit_description" class="form-textarea"></textarea>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Total Budget *</label>
                        <div class="input-prefix-group">
                            <span class="input-prefix">Rp</span>
                            <input type="text" name="budget" id="edit_budget" required oninput="handleRupiahInput(this)" class="form-input prefixed">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">DP / Amount Paid</label>
                        <div class="input-prefix-group">
                            <span class="input-prefix">Rp</span>
                            <input type="text" name="paid_amount" id="edit_paid_amount" oninput="handleRupiahInput(this)" class="form-input prefixed">
                        </div>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Status Column *</label>
                        <select name="status" id="edit_status" class="form-select">
                            <option value="requirement">📝 Requirement & DP</option>
                            <option value="in_progress">⚡ In Progress</option>
                            <option value="review">🔍 Review / Testing</option>
                            <option value="completed">✅ Completed</option>
                            <option value="cancelled">⛔ Cancelled / Hold</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Priority *</label>
                        <select name="priority" id="edit_priority" class="form-select">
                            <option value="low">🌱 Low</option>
                            <option value="medium">⚡ Medium</option>
                            <option value="high">🔥 High Priority</option>
                        </select>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Target Mulai</label>
                        <input type="date" name="start_date" id="edit_start_date" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Target Deadline</label>
                        <input type="date" name="deadline" id="edit_deadline" class="form-input">
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="justify-content: space-between;">
                <button type="button" class="btn btn-danger" onclick="submitDeleteOrder()">Delete Order</button>
                <div style="display:flex; gap:8px;">
                    <button type="button" class="btn btn-ghost" onclick="closeModal('editModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Order Card</button>
                </div>
            </div>
        </form>

        <form id="deleteOrderForm" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
    </x-admin-modal>

    <!-- STYLES -->
    <style>
        .input-prefix-group {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .input-prefix {
            position: absolute;
            left: 14px;
            font-weight: 700;
            font-size: 13px;
            color: var(--accent);
            pointer-events: none;
            user-select: none;
            z-index: 2;
        }

        .form-input.prefixed {
            padding-left: 38px !important;
        }

        .view-mode-toggle {
            display: inline-flex;
            background: var(--bg-elevated);
            border: 1px solid var(--border);
            border-radius: 9px;
            padding: 2px;
        }

        .mode-btn {
            background: transparent;
            border: none;
            padding: 5px 12px;
            border-radius: 7px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-family: 'Inter', sans-serif;
        }

        .mode-btn:hover {
            color: var(--text-primary);
        }

        .mode-btn.active {
            background: var(--accent);
            color: var(--accent-text);
            font-weight: 700;
            box-shadow: 0 0 14px var(--accent-glow);
        }

        .kanban-container {
            display: flex;
            gap: 16px;
            overflow-x: auto;
            padding-bottom: 20px;
            min-height: calc(100vh - 280px);
            align-items: flex-start;
            max-width: 100%;
            width: 100%;
        }

        .kanban-column {
            flex: 0 0 310px;
            width: 310px;
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            display: flex;
            flex-direction: column;
            transition: border-color 0.2s;
        }

        .kanban-column.drag-over {
            border-color: var(--accent);
            box-shadow: 0 0 15px var(--accent-glow);
        }

        .kanban-column-header {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .column-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13.5px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .column-count {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 20px;
            background: var(--accent-soft);
            color: var(--badge-accent-text);
            border: 1px solid rgba(184, 255, 0, 0.25);
        }

        .kanban-cards-wrapper {
            padding: 12px;
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-height: 570px;
        }

        .column-empty {
            text-align: center;
            padding: 30px 10px;
            border: 2px dashed var(--border);
            border-radius: 12px;
            font-size: 12px;
            color: var(--text-muted);
        }

        /* KANBAN CARD */
        .kanban-card {
            background: var(--bg-elevated);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 12px 13px;
            cursor: grab;
            transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
            position: relative;
        }

        .kanban-card:hover {
            border-color: var(--border-light);
            box-shadow: 0 4px 14px rgba(0,0,0,0.3);
            transform: translateY(-2px);
        }

        .kanban-card.dragging {
            opacity: 0.4;
            transform: scale(0.96);
        }

        .card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .card-menu-btn {
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 11px;
            cursor: pointer;
            padding: 2px 6px;
            border-radius: 4px;
            transition: background 0.2s;
        }

        .card-menu-btn:hover {
            background: var(--bg-hover);
            color: var(--text-primary);
        }

        .card-project-title {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 3px;
            line-height: 1.35;
        }

        .card-client {
            font-size: 11.5px;
            color: var(--text-secondary);
            font-weight: 500;
            margin-bottom: 6px;
        }

        .card-desc {
            font-size: 11px;
            color: var(--text-muted);
            line-height: 1.35;
            margin-bottom: 8px;
        }

        /* Financial Progress Bar */
        .card-financials {
            background: var(--bg-surface);
            padding: 6px 9px;
            border-radius: 8px;
            margin-bottom: 8px;
            border: 1px solid var(--border);
        }

        .financial-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: var(--text-muted);
            margin-bottom: 5px;
        }

        .progress-bar-bg {
            height: 4px;
            background: var(--bg-elevated);
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--accent), #7ecb00);
            border-radius: 4px;
        }

        .card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 6px;
            border-top: 1px solid var(--border);
            font-size: 11px;
        }

        .card-date {
            color: var(--text-muted);
        }

        .card-date.overdue {
            color: var(--rose);
            font-weight: 700;
        }

        .card-contact {
            color: #25d366;
            text-decoration: none;
            font-weight: 600;
        }

        .card-contact:hover {
            text-decoration: underline;
        }

        .add-card-btn {
            margin: 8px 12px 12px;
            padding: 8px;
            border-radius: 8px;
            background: transparent;
            border: 1px dashed var(--border);
            color: var(--text-muted);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
        }

        .add-card-btn:hover {
            background: var(--bg-hover);
            color: var(--text-primary);
            border-color: var(--border-light);
        }

        /* MODAL STYLES */
        .modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.75);
            backdrop-filter: blur(8px);
            z-index: 100;
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
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
            margin: auto;
            transition: border-color 0.25s ease;
        }

        .modal-box form {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            flex: 1;
        }

        @keyframes modalShake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-6px); }
            40%, 80% { transform: translateX(6px); }
        }

        .modal-box.modal-shake {
            animation: modalShake 0.35s ease-in-out !important;
            border-color: var(--amber) !important;
        }

        .modal-header {
            padding: 18px 22px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .modal-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 20px;
            color: var(--text-muted);
            cursor: pointer;
        }

        .modal-close:hover { color: var(--text-primary); }

        .modal-body {
            padding: 22px;
            overflow-y: auto;
            flex: 1;
            max-height: calc(100vh - 200px);
        }

        .modal-footer {
            padding: 16px 22px;
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            background: var(--bg-elevated);
            flex-shrink: 0;
        }
    </style>

    <!-- JS SCRIPT -->
    <script>
        let draggedCardId = null;
        let originalStatus = null;
        let activeEditOrderId = null;

        // MODE SWITCHER (BOARD / TABLE)
        function switchViewMode(mode) {
            const boardView = document.getElementById('ordersBoardView');
            const tableView = document.getElementById('ordersTableView');
            const btnBoard = document.getElementById('btnModeBoard');
            const btnTable = document.getElementById('btnModeTable');

            if (mode === 'table') {
                boardView.style.display = 'none';
                tableView.style.display = 'block';
                btnBoard.classList.remove('active');
                btnTable.classList.add('active');
                window.ekscoderDataTables['ordersDataTable']?.refreshRows();
            } else {
                boardView.style.display = 'flex';
                tableView.style.display = 'none';
                btnBoard.classList.add('active');
                btnTable.classList.remove('active');
            }
            localStorage.setItem('ekscoder_orders_view', mode);
        }

        // QUICK STATUS UPDATE FROM TABLE
        function quickUpdateStatus(orderId, newStatus) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            fetch(`/admin/orders/${orderId}/update-status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: newStatus })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Failed to update status');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Connection error. Failed to update status.');
            });
        }

        // KANBAN DRAG & DROP
        function handleDragStart(e, orderId, status) {
            draggedCardId = orderId;
            originalStatus = status;
            e.target.classList.add('dragging');
            e.dataTransfer.setData('text/plain', orderId);
        }

        function handleDragEnd(e) {
            e.target.classList.remove('dragging');
        }

        function handleDragOver(e) {
            e.preventDefault();
            const column = e.currentTarget;
            column.classList.add('drag-over');
        }

        function handleDragLeave(e) {
            const column = e.currentTarget;
            column.classList.remove('drag-over');
        }

        function handleDrop(e, newStatus) {
            e.preventDefault();
            const column = e.currentTarget;
            column.classList.remove('drag-over');

            if (!draggedCardId) return;

            const card = document.getElementById(`order-card-${draggedCardId}`);
            if (!card) return;

            const oldStatus = card.getAttribute('data-status');
            if (oldStatus === newStatus) return;

            const targetWrapper = document.getElementById(`column-${newStatus}`);
            const emptyNotice = document.getElementById(`empty-${newStatus}`);
            if (emptyNotice) emptyNotice.style.display = 'none';

            targetWrapper.appendChild(card);
            card.setAttribute('data-status', newStatus);

            updateColumnCounts();

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            fetch(`/admin/orders/${draggedCardId}/update-status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: newStatus })
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) alert('Failed to update status');
            })
            .catch(err => {
                console.error(err);
                alert('Connection error. Failed to move card.');
            });
        }

        function updateColumnCounts() {
            const columns = ['requirement', 'in_progress', 'review', 'completed', 'cancelled'];
            let activeOrdersCount = 0;
            let completedOrdersCount = 0;
            let totalPipelineValue = 0;
            let totalPaidValue = 0;

            columns.forEach(st => {
                const wrapper = document.getElementById(`column-${st}`);
                const countBadge = document.getElementById(`count-${st}`);
                if (wrapper && countBadge) {
                    const cards = wrapper.querySelectorAll('.kanban-card');
                    countBadge.textContent = cards.length;

                    if (st === 'completed') {
                        completedOrdersCount = cards.length;
                    } else if (st !== 'cancelled') {
                        activeOrdersCount += cards.length;
                    }

                    // Exclude cancelled/hold from financial contract value & paid sums!
                    if (st !== 'cancelled') {
                        cards.forEach(card => {
                            const b = parseFloat(card.getAttribute('data-budget')) || 0;
                            const p = parseFloat(card.getAttribute('data-paid-amount')) || 0;
                            totalPipelineValue += b;
                            totalPaidValue += p;
                        });
                    }

                    const emptyNotice = document.getElementById(`empty-${st}`);
                    if (emptyNotice) {
                        emptyNotice.style.display = cards.length === 0 ? 'block' : 'none';
                    }
                }
            });

            // Live update sidebar badge
            const sidebarBadge = document.getElementById('sidebarOrdersBadge');
            if (sidebarBadge) {
                sidebarBadge.textContent = activeOrdersCount;
                sidebarBadge.style.display = activeOrdersCount > 0 ? 'inline-block' : 'none';
            }

            // Live update stats cards
            const statActive = document.getElementById('statActiveOrders');
            if (statActive) statActive.textContent = activeOrdersCount;

            const statCompleted = document.getElementById('statCompletedOrders');
            if (statCompleted) statCompleted.textContent = completedOrdersCount;

            const statPipeline = document.getElementById('statPipelineValue');
            if (statPipeline) statPipeline.textContent = 'Rp ' + (totalPipelineValue > 0 ? formatRupiahDisplay(totalPipelineValue) : '0');

            const statPaid = document.getElementById('statTotalPaid');
            if (statPaid) statPaid.textContent = 'Rp ' + (totalPaidValue > 0 ? formatRupiahDisplay(totalPaidValue) : '0');
        }

        // LIVE SEARCH FILTER FOR TABLE VIEW
        function filterOrdersTable() {
            const input = document.getElementById('tableSearchInput');
            if (!input) return;
            const filter = input.value.toLowerCase().trim();
            const rows = document.querySelectorAll('#ordersTableBody tr.order-table-row');
            let matchCount = 0;

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(filter)) {
                    row.style.display = '';
                    matchCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            const noMatchRow = document.getElementById('noTableSearchMatchRow');
            if (noMatchRow) {
                if (matchCount === 0 && filter !== '') {
                    noMatchRow.style.display = '';
                    document.getElementById('searchQueryTerm').textContent = input.value;
                } else {
                    noMatchRow.style.display = 'none';
                }
            }
        }

        // RUPIAH LIVE FORMATTING HELPERS
        function formatRupiahDisplay(val) {
            if (val === null || val === undefined || val === '') return '';
            const clean = val.toString().replace(/[^0-9]/g, '');
            if (!clean) return '';
            return new Intl.NumberFormat('id-ID').format(parseInt(clean, 10));
        }

        function handleRupiahInput(input) {
            const rawDigits = input.value.replace(/[^0-9]/g, '');
            if (rawDigits) {
                input.value = formatRupiahDisplay(rawDigits);
            } else {
                input.value = '';
            }
        }

        function cleanRupiahForm(form) {
            const budgetInput = form.querySelector('[name="budget"]');
            const paidInput = form.querySelector('[name="paid_amount"]');
            if (budgetInput) budgetInput.value = budgetInput.value.replace(/[^0-9]/g, '');
            if (paidInput) paidInput.value = paidInput.value.replace(/[^0-9]/g, '');
        }

        let modalFocusState = {};

        // Track focusin on any modal form field to prevent accidental backdrop close
        document.addEventListener('focusin', function(e) {
            const modalBox = e.target.closest('.modal-box');
            if (modalBox) {
                const modalBackdrop = modalBox.closest('.modal-backdrop');
                if (modalBackdrop && modalBackdrop.id) {
                    modalFocusState[modalBackdrop.id] = true;
                }
            }
        });

        // MODAL FUNCTIONS
        function openCreateModal(defaultStatus = 'requirement') {
            modalFocusState['createModal'] = false;
            document.getElementById('createStatusSelect').value = defaultStatus;
            document.getElementById('create_budget').value = '';
            document.getElementById('create_paid_amount').value = '';
            const now = new Date();
            const todayStr = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;
            if (document.getElementById('create_start_date')) {
                document.getElementById('create_start_date').value = todayStr;
            }
            if (document.getElementById('create_deadline')) document.getElementById('create_deadline').value = '';
            document.getElementById('createModal').classList.add('open');
        }

        function openEditModalFromCard(orderId) {
            modalFocusState['editModal'] = false;
            const card = document.getElementById(`order-card-${orderId}`);
            if (!card) return;

            activeEditOrderId = orderId;

            document.getElementById('edit_client_name').value = card.getAttribute('data-client-name') || '';
            document.getElementById('edit_client_contact').value = card.getAttribute('data-client-contact') || '';
            document.getElementById('edit_title').value = card.getAttribute('data-title') || '';
            document.getElementById('edit_description').value = card.getAttribute('data-description') || '';
            
            const rawBudget = card.getAttribute('data-budget') || 0;
            const rawPaid = card.getAttribute('data-paid-amount') || 0;

            document.getElementById('edit_budget').value = rawBudget ? formatRupiahDisplay(rawBudget) : '';
            document.getElementById('edit_paid_amount').value = rawPaid ? formatRupiahDisplay(rawPaid) : '';

            document.getElementById('edit_status').value = card.getAttribute('data-status') || 'requirement';
            document.getElementById('edit_priority').value = card.getAttribute('data-priority') || 'medium';
            if (document.getElementById('edit_start_date')) document.getElementById('edit_start_date').value = card.getAttribute('data-start-date') || '';
            document.getElementById('edit_deadline').value = card.getAttribute('data-deadline') || '';

            document.getElementById('editForm').action = `/admin/orders/${orderId}`;
            document.getElementById('deleteOrderForm').action = `/admin/orders/${orderId}`;

            document.getElementById('editModal').classList.add('open');
        }

        function closeModal(modalId) {
            modalFocusState[modalId] = false;
            document.getElementById(modalId)?.classList.remove('open');
        }

        function handleAdminModalBackdropClick(e, modalId, closeOnBackdrop = false) {
            if (!e.target.classList.contains('modal-backdrop')) return;

            if (!closeOnBackdrop) {
                // Completely block closing modal on backdrop click!
                const modalBox = document.getElementById(modalId)?.querySelector('.modal-box');
                if (modalBox) {
                    modalBox.classList.add('modal-shake');
                    setTimeout(() => modalBox.classList.remove('modal-shake'), 350);
                }
                return;
            }
            
            closeModal(modalId);
        }

        function closeModalOnBackdrop(e, modalId) {
            handleAdminModalBackdropClick(e, modalId, true);
        }

        function submitDeleteOrder() {
            confirmDelete('Delete Project Order?', 'This project order card will be permanently deleted from your pipeline.', function() {
                document.getElementById('deleteOrderForm').submit();
            });
        }

        // Initial sync on load
        document.addEventListener('DOMContentLoaded', function() {
            const savedMode = localStorage.getItem('ekscoder_orders_view') || 'board';
            switchViewMode(savedMode);
        });
    </script>
</x-admin-layout>
