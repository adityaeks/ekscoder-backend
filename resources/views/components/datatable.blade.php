@props([
    'id' => 'datatable-' . Str::random(8),
    'title' => null,
    'subtitle' => null,
    'searchable' => true,
    'searchPlaceholder' => 'Search table...',
    'perPageOptions' => [10, 20, 50, 'all'],
    'defaultPerPage' => 10,
    'info' => true,
    'class' => '',
])

<div id="{{ $id }}" class="datatable-wrapper {{ $class }}" data-datatable-per-page="{{ $defaultPerPage }}">
    @if($title || isset($headerTitle) || isset($actions))
    <div class="datatable-title-header">
        <div>
            @if(isset($headerTitle))
                {{ $headerTitle }}
            @elseif($title)
                <div class="card-title">{{ $title }}</div>
                @if($subtitle)
                    <div class="card-subtitle">{{ $subtitle }}</div>
                @endif
            @endif
        </div>
        
        @if(isset($actions))
        <div class="datatable-actions">
            {{ $actions }}
        </div>
        @endif
    </div>
    @endif

    <!-- Controls Header -->
    <div class="datatable-header">
        <div class="datatable-per-page">
            <label class="datatable-per-page-label">
                <span>Viewed</span>
                <select class="datatable-per-page-select" onchange="window.ekscoderDataTables['{{ $id }}']?.setPerPage(this.value)">
                    @foreach($perPageOptions as $option)
                        <option value="{{ $option }}" {{ (string)$defaultPerPage === (string)$option ? 'selected' : '' }}>
                            {{ is_numeric($option) ? $option : ucfirst($option) }}
                        </option>
                    @endforeach
                </select>
                <span>entries</span>
            </label>
        </div>

        <div class="datatable-header-right">
            @if(isset($filters))
                <div class="datatable-filters">
                    {{ $filters }}
                </div>
            @endif

            @if($searchable)
            <div class="datatable-search">
                <div class="datatable-search-group">
                    <span class="datatable-search-icon">🔍</span>
                    <input 
                        type="text" 
                        class="datatable-search-input" 
                        placeholder="{{ $searchPlaceholder }}"
                        onkeyup="window.ekscoderDataTables['{{ $id }}']?.search(this.value)"
                        oninput="window.ekscoderDataTables['{{ $id }}']?.search(this.value)"
                    >
                    <button type="button" class="datatable-search-clear" onclick="window.ekscoderDataTables['{{ $id }}']?.clearSearch(this)" style="display:none;" title="Clear search">✕</button>
                </div>
            </div>
            @endif

            @if(isset($headerButtons))
                <div class="datatable-header-buttons">
                    {{ $headerButtons }}
                </div>
            @endif
        </div>
    </div>

    <!-- Table Slot -->
    <div class="datatable-scroll-container">
        {{ $slot }}
    </div>

    <!-- Controls Footer (Info & Pagination) -->
    <div class="datatable-footer">
        <div class="datatable-info">
            <span class="datatable-info-text">Showing 0 to 0 of 0 entries</span>
        </div>
        <div class="datatable-pagination">
            <!-- Dynamic pagination buttons rendered by JS -->
        </div>
    </div>
</div>

@once
<style>
    .datatable-wrapper {
        display: flex;
        flex-direction: column;
        width: 100%;
    }

    .datatable-title-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        padding: 20px 24px 16px 24px;
        border-bottom: 1px solid var(--border);
    }

    .datatable-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-left: auto;
    }

    .datatable-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        padding: 18px 24px 14px 24px;
    }

    .datatable-header-right {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-left: auto;
    }

    .datatable-header-buttons {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .datatable-filters {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .datatable-filter-select, .datatable-filters select, .datatable-filters input {
        background: var(--bg-elevated);
        color: var(--text-primary);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 5px 10px;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .datatable-filter-select:focus, .datatable-filters select:focus, .datatable-filters input:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 2px var(--accent-soft);
    }

    .datatable-per-page-label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .datatable-per-page-select {
        background: var(--bg-elevated);
        color: var(--text-primary);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 5px 10px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .datatable-per-page-select:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 2px var(--accent-soft);
    }

    .datatable-search-group {
        position: relative;
        display: flex;
        align-items: center;
        min-width: 240px;
    }

    .datatable-search-icon {
        position: absolute;
        left: 12px;
        color: var(--text-muted);
        font-size: 12px;
        pointer-events: none;
    }

    .datatable-search-input {
        width: 100%;
        background: var(--bg-elevated);
        color: var(--text-primary);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 6px 30px 6px 32px;
        font-size: 12px;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .datatable-search-input:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px var(--accent-glow);
    }

    .datatable-search-clear {
        position: absolute;
        right: 8px;
        background: none;
        border: none;
        color: var(--text-muted);
        font-size: 14px;
        cursor: pointer;
        padding: 0 4px;
        line-height: 1;
        border-radius: 50%;
    }

    .datatable-search-clear:hover {
        color: var(--rose);
    }

    .datatable-scroll-container {
        width: 100%;
        overflow-x: auto;
    }

    .datatable-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
        padding: 16px 24px 20px 24px;
        border-top: 1px solid var(--border);
    }

    .datatable-info-text {
        font-size: 12px;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .datatable-pagination {
        display: flex;
        align-items: center;
        gap: 4px;
        flex-wrap: wrap;
    }

    .datatable-page-btn {
        background: var(--bg-elevated);
        color: var(--text-secondary);
        border: 1px solid var(--border);
        border-radius: 8px;
        min-width: 30px;
        height: 30px;
        padding: 0 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 11.5px;
        font-weight: 600;
        font-family: 'JetBrains Mono', monospace;
        cursor: pointer;
        transition: all 0.2s ease;
        user-select: none;
    }

    .datatable-page-btn:hover:not(:disabled):not(.active) {
        background: var(--bg-hover);
        color: var(--text-primary);
        border-color: var(--border-light);
    }

    .datatable-page-btn.active {
        background: var(--accent);
        color: var(--accent-text);
        border-color: var(--accent);
        font-weight: 700;
        box-shadow: 0 0 10px var(--accent-glow);
    }

    .datatable-page-btn:disabled {
        opacity: 0.35;
        cursor: not-allowed;
    }

    .datatable-ellipsis {
        color: var(--text-muted);
        padding: 0 4px;
        font-size: 12px;
    }
</style>

<script>
    if (typeof window.EkscoderDataTable === 'undefined') {
        class EkscoderDataTable {
            constructor(wrapperEl, options = {}) {
                this.wrapper = typeof wrapperEl === 'string' ? document.getElementById(wrapperEl) : wrapperEl;
                if (!this.wrapper) return;

                this.id = this.wrapper.id;
                this.table = this.wrapper.querySelector('table');
                if (!this.table) return;

                this.tbody = this.table.querySelector('tbody');
                if (!this.tbody) return;

                this.options = Object.assign({
                    perPage: 10,
                    searchQuery: '',
                    reindexSelector: '.datatable-row-index',
                    lang: {
                        showing: 'Showing',
                        to: 'to',
                        of: 'of',
                        entries: 'entries',
                        noData: 'No matching records found',
                        first: '«',
                        prev: '‹',
                        next: '›',
                        last: '»'
                    }
                }, options);

                this.currentPage = 1;
                this.perPage = this.options.perPage;
                this.searchQuery = this.options.searchQuery;
                this.columnFilters = {};
                this.filteredRows = [];

                this.init();
            }

            init() {
                this.searchSelect = this.wrapper.querySelector('.datatable-per-page-select');
                this.searchInput = this.wrapper.querySelector('.datatable-search-input');
                this.clearBtn = this.wrapper.querySelector('.datatable-search-clear');
                this.infoText = this.wrapper.querySelector('.datatable-info-text');
                this.paginationContainer = this.wrapper.querySelector('.datatable-pagination');

                this.ensureEmptyRow();
                this.initFilters();
                this.refreshRows();
                this.initSorting();
            }

            initFilters() {
                const filterInputs = this.wrapper.querySelectorAll('.datatable-filters [data-filter-column], .datatable-filters [data-column]');
                filterInputs.forEach(el => {
                    const colKey = el.getAttribute('data-filter-column') || el.getAttribute('data-column');
                    const eventName = el.tagName === 'INPUT' ? 'input' : 'change';

                    el.addEventListener(eventName, (e) => {
                        this.filterColumn(colKey, e.target.value);
                    });

                    if (el.value) {
                        this.columnFilters[colKey] = el.value.toLowerCase().trim();
                    }
                });
            }

            filterColumn(colKey, value) {
                const val = (value || '').toLowerCase().trim();
                if (!val) {
                    delete this.columnFilters[colKey];
                } else {
                    this.columnFilters[colKey] = val;
                }
                this.currentPage = 1;
                this.applyFilter();
            }

            ensureEmptyRow() {
                let emptyRow = this.tbody.querySelector('tr.datatable-empty-row');
                if (!emptyRow) {
                    const colCount = this.table.querySelectorAll('thead th').length || 1;
                    emptyRow = document.createElement('tr');
                    emptyRow.className = 'datatable-empty-row';
                    emptyRow.style.display = 'none';
                    emptyRow.innerHTML = `<td colspan="${colCount}" style="text-align:center; padding:35px 20px; color:var(--text-muted); font-size:13px;">
                        🔍 <span class="empty-msg">${this.options.lang.noData}</span>
                    </td>`;
                    this.tbody.appendChild(emptyRow);
                }
                this.emptyRow = emptyRow;
            }

            refreshRows() {
                const allTrs = Array.from(this.tbody.querySelectorAll('tr'));
                this.allRows = allTrs.filter(tr => !tr.classList.contains('datatable-empty-row') && !tr.id?.includes('noTableSearchMatchRow'));
                this.applyFilter();
            }

            search(query) {
                this.searchQuery = (query || '').toLowerCase().trim();
                if (this.clearBtn) {
                    this.clearBtn.style.display = this.searchQuery ? 'inline-block' : 'none';
                }
                this.currentPage = 1;
                this.applyFilter();
            }

            clearSearch(btn) {
                if (this.searchInput) {
                    this.searchInput.value = '';
                    this.search('');
                    this.searchInput.focus();
                }
            }

            setPerPage(val) {
                this.perPage = val === 'all' ? 'all' : parseInt(val, 10);
                this.currentPage = 1;
                this.render();
            }

            goToPage(page) {
                const totalPages = this.getTotalPages();
                if (page < 1 || page > totalPages) return;
                this.currentPage = page;
                this.render();
            }

            applyFilter() {
                this.filteredRows = this.allRows.filter(row => {
                    // Global search match
                    if (this.searchQuery) {
                        const text = row.textContent.toLowerCase();
                        if (!text.includes(this.searchQuery)) return false;
                    }

                    // Column / Custom filters match
                    for (const [colKey, filterVal] of Object.entries(this.columnFilters)) {
                        let cellText = '';
                        if (!isNaN(colKey)) {
                            const colIdx = parseInt(colKey, 10);
                            cellText = row.children[colIdx]?.textContent.toLowerCase() || '';
                        } else {
                            cellText = (row.getAttribute(`data-${colKey}`) || row.textContent).toLowerCase();
                        }

                        if (!cellText.includes(filterVal)) {
                            return false;
                        }
                    }

                    return true;
                });

                this.render();
            }

            getTotalPages() {
                if (this.perPage === 'all' || this.filteredRows.length === 0) return 1;
                return Math.ceil(this.filteredRows.length / this.perPage);
            }

            render() {
                const total = this.filteredRows.length;
                const totalPages = this.getTotalPages();

                if (this.currentPage > totalPages) {
                    this.currentPage = Math.max(1, totalPages);
                }

                let start = 0;
                let end = total;

                if (this.perPage !== 'all') {
                    start = (this.currentPage - 1) * this.perPage;
                    end = Math.min(start + this.perPage, total);
                }

                this.allRows.forEach(row => { row.style.display = 'none'; });

                if (total === 0) {
                    if (this.emptyRow) {
                        const msgSpan = this.emptyRow.querySelector('.empty-msg');
                        if (msgSpan) {
                            msgSpan.textContent = (this.searchQuery || Object.keys(this.columnFilters).length > 0)
                                ? `No item matches your current filter criteria`
                                : this.options.lang.noData;
                        }
                        this.emptyRow.style.display = '';
                    }
                } else {
                    if (this.emptyRow) this.emptyRow.style.display = 'none';
                    const pageRows = this.filteredRows.slice(start, end);
                    pageRows.forEach((row, index) => {
                        row.style.display = '';
                        const indexBadge = row.querySelector(this.options.reindexSelector);
                        if (indexBadge) {
                            indexBadge.textContent = `#${start + index + 1}`;
                        }
                    });
                }

                this.renderInfo(total, start, end);
                this.renderPagination(totalPages);
            }

            renderInfo(total, start, end) {
                if (!this.infoText) return;
                if (total === 0) {
                    this.infoText.textContent = `${this.options.lang.showing} 0 ${this.options.lang.to} 0 ${this.options.lang.of} 0 ${this.options.lang.entries}`;
                } else {
                    const displayStart = start + 1;
                    const displayEnd = end;
                    this.infoText.textContent = `${this.options.lang.showing} ${displayStart} ${this.options.lang.to} ${displayEnd} ${this.options.lang.of} ${total} ${this.options.lang.entries}`;
                }
            }

            renderPagination(totalPages) {
                if (!this.paginationContainer) return;
                this.paginationContainer.innerHTML = '';

                if (totalPages <= 1) return;

                const createBtn = (label, page, disabled = false, active = false) => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = `datatable-page-btn ${active ? 'active' : ''}`;
                    btn.innerHTML = label;
                    btn.disabled = disabled;
                    if (!disabled && !active) {
                        btn.addEventListener('click', () => this.goToPage(page));
                    }
                    return btn;
                };

                this.paginationContainer.appendChild(createBtn(this.options.lang.first, 1, this.currentPage === 1));
                this.paginationContainer.appendChild(createBtn(this.options.lang.prev, this.currentPage - 1, this.currentPage === 1));

                const pages = this.getPageNumbers(totalPages);
                pages.forEach(p => {
                    if (p === '...') {
                        const ellipsis = document.createElement('span');
                        ellipsis.className = 'datatable-ellipsis';
                        ellipsis.textContent = '...';
                        this.paginationContainer.appendChild(ellipsis);
                    } else {
                        this.paginationContainer.appendChild(createBtn(p, p, false, p === this.currentPage));
                    }
                });

                this.paginationContainer.appendChild(createBtn(this.options.lang.next, this.currentPage + 1, this.currentPage === totalPages));
                this.paginationContainer.appendChild(createBtn(this.options.lang.last, totalPages, this.currentPage === totalPages));
            }

            getPageNumbers(totalPages) {
                const current = this.currentPage;
                const delta = 1;
                const range = [];
                const rangeWithDots = [];
                let l;

                for (let i = 1; i <= totalPages; i++) {
                    if (i === 1 || i === totalPages || (i >= current - delta && i <= current + delta)) {
                        range.push(i);
                    }
                }

                for (let i of range) {
                    if (l) {
                        if (i - l === 2) {
                            rangeWithDots.push(l + 1);
                        } else if (i - l !== 1) {
                            rangeWithDots.push('...');
                        }
                    }
                    rangeWithDots.push(i);
                    l = i;
                }

                return rangeWithDots;
            }

            initSorting() {
                const ths = this.table.querySelectorAll('thead th[data-sortable="true"]');
                ths.forEach((th, colIdx) => {
                    th.style.cursor = 'pointer';
                    th.style.userSelect = 'none';

                    if (!th.querySelector('.sort-icon')) {
                        const icon = document.createElement('span');
                        icon.className = 'sort-icon';
                        icon.style.marginLeft = '6px';
                        icon.style.opacity = '0.35';
                        icon.style.fontSize = '10px';
                        icon.textContent = '↕';
                        th.appendChild(icon);
                    }

                    th.addEventListener('click', () => {
                        const currentDir = th.getAttribute('data-sort-dir') === 'asc' ? 'desc' : 'asc';
                        
                        ths.forEach(otherTh => {
                            otherTh.removeAttribute('data-sort-dir');
                            const otherIcon = otherTh.querySelector('.sort-icon');
                            if (otherIcon) {
                                otherIcon.textContent = '↕';
                                otherIcon.style.opacity = '0.35';
                            }
                        });

                        th.setAttribute('data-sort-dir', currentDir);
                        const icon = th.querySelector('.sort-icon');
                        if (icon) {
                            icon.textContent = currentDir === 'asc' ? '↑' : '↓';
                            icon.style.opacity = '1';
                        }

                        this.sortColumn(colIdx, currentDir);
                    });
                });
            }

            sortColumn(colIdx, direction) {
                const isAsc = direction === 'asc';

                this.filteredRows.sort((rowA, rowB) => {
                    const cellA = rowA.children[colIdx]?.textContent.trim() || '';
                    const cellB = rowB.children[colIdx]?.textContent.trim() || '';

                    const numA = parseFloat(cellA.replace(/[^0-9.-]+/g, ''));
                    const numB = parseFloat(cellB.replace(/[^0-9.-]+/g, ''));

                    if (!isNaN(numA) && !isNaN(numB) && cellA.match(/^[Rp$\s0-9.,-]+$/) && cellB.match(/^[Rp$\s0-9.,-]+$/)) {
                        return isAsc ? numA - numB : numB - numA;
                    }

                    return isAsc ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
                });

                this.render();
            }
        }

        window.EkscoderDataTable = EkscoderDataTable;
        window.ekscoderDataTables = window.ekscoderDataTables || {};

        window.initEkscoderDataTables = function() {
            document.querySelectorAll('.datatable-wrapper').forEach(wrapper => {
                if (!wrapper.id) wrapper.id = 'datatable-' + Math.random().toString(36).substr(2, 9);
                if (!window.ekscoderDataTables[wrapper.id]) {
                    const defaultPerPage = wrapper.getAttribute('data-datatable-per-page') || 10;
                    const perPageVal = defaultPerPage === 'all' ? 'all' : parseInt(defaultPerPage, 10);
                    window.ekscoderDataTables[wrapper.id] = new EkscoderDataTable(wrapper, { perPage: perPageVal });
                } else {
                    window.ekscoderDataTables[wrapper.id].refreshRows();
                }
            });
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', window.initEkscoderDataTables);
        } else {
            window.initEkscoderDataTables();
        }
    }
</script>
@endonce
