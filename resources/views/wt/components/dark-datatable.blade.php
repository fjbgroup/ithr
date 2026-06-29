@props([
    'columns' => [
        'RADIO_ID',
        'SERIAL_NO',
        'MODEL',
        'STATUS',
        'CURRENT_OWNERSHIP_TYPE',
        'SHARED_WITH',
        'CURRENT_OWNERSHIP',
    ],
    'statusOptions' => [],
    'searchId' => 'datatableSearch',
    'statusId' => 'datatableStatus',
    'resetId' => 'datatableReset',
    'tableId' => 'datatable',
    'itemsPerPage' => 10,
    'emptyText' => 'NO ITEMS FOUND',
    'minWidth' => '1180px',
    'rowSelector' => 'tbody tr',
])

<section {{ $attributes->merge(['class' => 'wt-data']) }}>
    <style>
        .wt-data {
            --wt-bg: #0f172a;
            --wt-panel: #111827;
            --wt-panel-2: #1f2937;
            --wt-line: #2b3950;
            --wt-line-soft: #263244;
            --wt-text: #dbeafe;
            --wt-muted: #94a3b8;
            display: grid !important;
            gap: 22px !important;
            color-scheme: dark !important;
        }
        .wt-data * {
            box-sizing: border-box;
        }
        .wt-data-filter {
            display: grid !important;
            grid-template-columns: minmax(260px, 1fr) 405px 74px !important;
            gap: 24px !important;
            align-items: end !important;
            min-height: 134px !important;
            padding: 16px 18px !important;
            border: 1px solid var(--wt-line-soft) !important;
            border-radius: 8px !important;
            background: var(--wt-panel) !important;
        }
        .wt-data-field label {
            display: block !important;
            margin: 0 0 14px !important;
            color: var(--wt-muted) !important;
            font-size: 12px !important;
            font-weight: 800 !important;
            letter-spacing: 0.14em !important;
            line-height: 1 !important;
            text-transform: uppercase !important;
        }
        .wt-data-input,
        .wt-data-select,
        .wt-data-reset {
            width: 100% !important;
            height: 48px !important;
            border: 1px solid #334155 !important;
            border-radius: 8px !important;
            background: var(--wt-bg) !important;
            color: #e5e7eb !important;
            font-size: 16px !important;
            font-weight: 650 !important;
            outline: none !important;
            box-shadow: none !important;
        }
        .wt-data-input {
            padding: 0 18px !important;
        }
        .wt-data-select {
            padding: 0 38px 0 14px !important;
        }
        .wt-data-reset {
            padding: 0 !important;
            cursor: pointer !important;
            font-size: 14px !important;
            font-weight: 800 !important;
        }
        .wt-data-input::placeholder {
            color: #7f8ba0 !important;
        }
        .wt-data-input:focus,
        .wt-data-select:focus,
        .wt-data-reset:hover {
            border-color: #3b82f6 !important;
        }
        .wt-data-table {
            overflow: hidden !important;
            border: 1px solid var(--wt-line) !important;
            border-radius: 8px !important;
            background: var(--wt-panel) !important;
        }
        .wt-data-scroll {
            overflow-x: auto !important;
        }
        .wt-data table {
            width: 100% !important;
            margin: 0 !important;
            border: 0 !important;
            border-collapse: collapse !important;
        }
        .wt-data thead th {
            height: 52px !important;
            padding: 0 16px !important;
            border: 1px solid var(--wt-line) !important;
            background: var(--wt-panel) !important;
            color: var(--wt-text) !important;
            font-size: 15px !important;
            font-weight: 900 !important;
            letter-spacing: 0.04em !important;
            line-height: 1.1 !important;
            text-align: center !important;
            text-transform: uppercase !important;
            white-space: nowrap !important;
        }
        .wt-data tbody td {
            height: 42px !important;
            padding: 8px 16px !important;
            border: 1px solid var(--wt-line-soft) !important;
            background: var(--wt-panel) !important;
            color: #dbe4f0 !important;
            font-size: 13px !important;
            font-weight: 650 !important;
            line-height: 1.25 !important;
            vertical-align: middle !important;
            white-space: nowrap !important;
        }
        .wt-data tbody tr:hover td {
            background: #172033 !important;
        }
        .wt-data-scrollbar {
            height: 10px !important;
            border-top: 1px solid var(--wt-line-soft) !important;
            background: var(--wt-panel) !important;
        }
        .wt-data-scrollbar-thumb {
            width: 39% !important;
            height: 10px !important;
            border-radius: 999px !important;
            background: #53657a !important;
        }
        .wt-data-footer {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            min-height: 64px !important;
            gap: 16px !important;
            padding: 10px 18px !important;
            border-top: 1px solid var(--wt-line-soft) !important;
            background: var(--wt-panel-2) !important;
        }
        .wt-data-info {
            color: var(--wt-text) !important;
            font-size: 16px !important;
            font-weight: 900 !important;
            letter-spacing: 0 !important;
            white-space: nowrap !important;
        }
        .wt-data-pagination {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-end !important;
            gap: 10px !important;
        }
        .wt-data-empty {
            color: #9fb0c8 !important;
            font-size: 18px !important;
            font-weight: 900 !important;
            letter-spacing: 0.22em !important;
            text-transform: uppercase !important;
            white-space: nowrap !important;
        }
        .wt-data-page {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 38px !important;
            height: 34px !important;
            padding: 0 10px !important;
            border: 1px solid #2f4d74 !important;
            border-radius: 7px !important;
            background: var(--wt-bg) !important;
            color: #bfdbfe !important;
            font-size: 12px !important;
            font-weight: 900 !important;
        }
        .wt-data-page.is-nav {
            min-width: 92px !important;
            color: #cbd5e1 !important;
            font-size: 13px !important;
        }
        .wt-data-page.is-active {
            border-color: #3b82f6 !important;
            background: #0f3a72 !important;
            color: #ffffff !important;
        }
        .wt-data-page:disabled {
            cursor: not-allowed !important;
            opacity: 0.42 !important;
        }
        @media (max-width: 900px) {
            .wt-data-filter {
                grid-template-columns: 1fr !important;
                min-height: auto !important;
            }
            .wt-data-footer {
                align-items: stretch !important;
                flex-direction: column !important;
            }
            .wt-data-pagination {
                justify-content: flex-start !important;
                overflow-x: auto !important;
            }
        }
    </style>

    <div class="wt-data-filter" style="justify-content: flex-start !important; width: auto !important;">
        <div class="wt-data-field">
            <label for="{{ $searchId }}">Search</label>
            <input id="{{ $searchId }}" type="search" placeholder="Keywords" class="wt-data-input" style="width: 250px !important; max-width: 250px !important;">
        </div>

        <div class="wt-data-field">
            <label for="{{ $statusId }}">Status</label>
            <select id="{{ $statusId }}" class="wt-data-select">
                <option value="">All Status</option>
                @foreach($statusOptions as $value => $label)
                    @php
                        $optionValue = is_int($value) ? $label : $value;
                    @endphp
                    <option value="{{ $optionValue }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <button id="{{ $resetId }}" type="button" class="wt-data-reset">Reset</button>
    </div>

    <div class="wt-data-table">
        <div class="wt-data-scroll">
            <table id="{{ $tableId }}" style="min-width: {{ $minWidth }}">
                <thead>
                    <tr>
                        @foreach($columns as $column)
                            <th>{{ $column }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    {{ $slot }}
                </tbody>
            </table>
        </div>

        <div class="wt-data-scrollbar">
            <div class="wt-data-scrollbar-thumb"></div>
        </div>

        <div class="wt-data-footer">
            <div id="{{ $tableId }}Showing" class="wt-data-info" data-empty-text="{{ $emptyText }}">
                Total: 0 items
            </div>
        </div>
    </div>
</section>

@once
    @push('scripts')
        <script>
            window.mountDarkDatatable = function mountDarkDatatable(config) {
                const table = document.getElementById(config.tableId);
                if (!table) return;

                const searchInput = document.getElementById(config.searchId);
                const statusSelect = document.getElementById(config.statusId);
                const resetButton = document.getElementById(config.resetId);
                const showing = document.getElementById(`${config.tableId}Showing`);
                const rows = Array.from(table.querySelectorAll(config.rowSelector || 'tbody tr'));
                const perPage = Number(config.itemsPerPage || 10);
                const maxNumbers = 4;
                let currentPage = 1;
                let filteredRows = rows;

                function pageSet(totalPages) {
                    const pages = [];
                    if (totalPages <= maxNumbers + 2) {
                        for (let page = 1; page <= totalPages; page += 1) pages.push(page);
                    } else if (currentPage <= 3) {
                        pages.push(1, 2, 3, 4, 'ellipsis', totalPages);
                    } else if (currentPage >= totalPages - 2) {
                        pages.push(1, 'ellipsis', totalPages - 3, totalPages - 2, totalPages - 1, totalPages);
                    } else {
                        pages.push(1, 'ellipsis', currentPage - 1, currentPage, currentPage + 1, 'ellipsis', totalPages);
                    }
                    return pages;
                }

                function makeButton(label, className, disabled, onClick) {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = className;
                    button.innerHTML = label;
                    button.disabled = disabled;
                    button.addEventListener('click', onClick);
                    return button;
                }

                function render() {
                    const totalItems = filteredRows.length;

                    rows.forEach((row) => row.classList.add('hidden'));
                    filteredRows.forEach((row) => row.classList.remove('hidden'));

                    if (showing) {
                        showing.textContent = `Total: ${totalItems} items`;
                    }
                }

                function applyFilters() {
                    const searchValue = (searchInput?.value || '').trim().toUpperCase();
                    const statusValue = (statusSelect?.value || '').trim().toUpperCase();

                    filteredRows = rows.filter((row) => {
                        const rowSearch = (row.dataset.search || row.textContent || '').toUpperCase();
                        const rowStatus = (row.dataset.status || '').toUpperCase();
                        return (!searchValue || rowSearch.includes(searchValue))
                            && (!statusValue || rowStatus === statusValue);
                    });

                    currentPage = 1;
                    render();
                }

                searchInput?.addEventListener('input', applyFilters);
                statusSelect?.addEventListener('change', applyFilters);
                resetButton?.addEventListener('click', () => {
                    if (searchInput) searchInput.value = '';
                    if (statusSelect) statusSelect.value = '';
                    applyFilters();
                });

                applyFilters();
            };
        </script>
    @endpush
@endonce

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.mountDarkDatatable({
                tableId: @json($tableId),
                searchId: @json($searchId),
                statusId: @json($statusId),
                resetId: @json($resetId),
                itemsPerPage: @json($itemsPerPage),
                rowSelector: @json($rowSelector),
            });
        });
    </script>
@endpush
