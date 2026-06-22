    @extends('wt.layouts.admin')

    @section('title', 'Inventory List')

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @endpush

    @push('final_styles')
    <style id="inventory-critical-paint">
        body .content-surface:has(.inventory-page-shell) {
            background: #f4f7fb !important;
            padding: 10px !important;
        }

        body .content-surface .inventory-page-shell {
            display: grid !important;
            gap: 12px !important;
            visibility: hidden !important;
        }

        html.inventory-page-ready body .content-surface .inventory-page-shell {
            visibility: visible !important;
        }

        body .content-surface .inventory-page-header {
            min-height: 0 !important;
            margin: 0 !important;
            padding: 0 2px 10px !important;
            border: 0 !important;
            border-radius: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
        }

        body .content-surface .inventory-page-header .page-title-standard {
            margin: 0 !important;
            color: #172033 !important;
            font-size: 19px !important;
            font-weight: 900 !important;
            line-height: 1.1 !important;
            letter-spacing: 0 !important;
        }

        body .content-surface .inventory-page-header .page-subtitle-standard {
            display: block !important;
            max-width: 520px !important;
            margin-top: 5px !important;
            color: #64748b !important;
            font-size: 9px !important;
            font-weight: 900 !important;
            letter-spacing: .16em !important;
            line-height: 1.45 !important;
            text-transform: uppercase !important;
        }

        body .content-surface .inventory-header-actions {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-end !important;
            gap: 8px !important;
        }

        body .content-surface .inventory-page-header .wt-btn {
            min-width: 118px !important;
            height: 34px !important;
            min-height: 34px !important;
            padding: 0 11px !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 10px !important;
            background: #ffffff !important;
            color: #334155 !important;
            box-shadow: none !important;
            font-size: 12px !important;
            font-weight: 900 !important;
            letter-spacing: 0 !important;
            white-space: nowrap !important;
        }

        body .content-surface .inventory-summary-grid,
        body .content-surface .inventory-record-pill {
            display: none !important;
        }

        body .content-surface .clean-admin-filter,
        body .content-surface .inventory-bulk-bar,
        body .content-surface #mainTableContainer.inventory-table-shell {
            border: 1px solid #d8e1ed !important;
            background: transparent !important;
            box-shadow: none !important;
        }

        body .content-surface .clean-admin-filter {
            margin: 0 !important;
            padding: 12px !important;
            border-radius: 14px !important;
        }

        body .content-surface .clean-admin-filter-grid {
            display: grid !important;
            grid-template-columns: minmax(240px, 1fr) 180px auto !important;
            gap: 10px !important;
            align-items: end !important;
        }

        body .content-surface .clean-admin-label {
            margin: 0 0 6px !important;
            color: #64748b !important;
            font-size: 9px !important;
            font-weight: 800 !important;
            letter-spacing: .12em !important;
            text-transform: uppercase !important;
        }

        body .content-surface .clean-admin-input,
        body .content-surface .clean-admin-select,
        body .content-surface .clean-admin-reset {
            height: 38px !important;
            min-height: 38px !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 10px !important;
            background: #ffffff !important;
            color: #172033 !important;
            font-size: 12px !important;
            font-weight: 800 !important;
        }

        body .content-surface .inventory-bulk-bar {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-start !important;
            gap: 8px !important;
            margin: 8px 0 12px !important;
            padding: 8px 10px !important;
            border-radius: 10px !important;
        }

        body .content-surface .inventory-bulk-controls {
            display: grid !important;
            grid-template-columns: 124px 140px minmax(150px, 1fr) auto !important;
            flex: 0 1 560px !important;
            width: min(560px, 100%) !important;
            gap: 7px !important;
            align-items: center !important;
        }

        body .content-surface .inventory-bulk-count,
        body .content-surface .inventory-bulk-select,
        body .content-surface .inventory-bulk-input,
        body .content-surface .inventory-bulk-btn {
            height: 28px !important;
            min-height: 28px !important;
            border-radius: 7px !important;
            font-size: 9px !important;
            box-shadow: none !important;
        }

        body .content-surface #mainTableContainer.inventory-table-shell {
            margin-top: 0 !important;
            border-radius: 14px !important;
            overflow: hidden !important;
            clear: both !important;
        }

        body .content-surface #walkiesTable {
            table-layout: auto !important;
            width: max-content !important;
            min-width: 980px !important;
            border-collapse: collapse !important;
        }

        body .content-surface #walkiesTable thead th {
            height: 30px !important;
            padding: 0 8px !important;
            border: 1px solid #d8e1ed !important;
            background: #eef3f8 !important;
            color: #526781 !important;
            font-size: 10px !important;
            font-weight: 900 !important;
            letter-spacing: .04em !important;
            white-space: nowrap !important;
        }

        body .content-surface #walkiesTable tbody td {
            height: 22px !important;
            min-height: 22px !important;
            padding: 2px 8px !important;
            border: 1px solid #e2e8f0 !important;
            background: #ffffff !important;
            color: #1f2937 !important;
            font-size: 9px !important;
            line-height: 1.15 !important;
            vertical-align: middle !important;
            white-space: nowrap !important;
        }

        body .content-surface #walkiesTable th.inventory-select-col,
        body .content-surface #walkiesTable td.inventory-select-col,
        body .content-surface .inventory-select-col {
            width: 34px !important;
            min-width: 34px !important;
            max-width: 34px !important;
            padding-left: 4px !important;
            padding-right: 4px !important;
            text-align: center !important;
        }

        body .content-surface .inventory-bulk-checkbox {
            width: 11px !important;
            height: 11px !important;
            min-width: 11px !important;
            min-height: 11px !important;
            border-radius: 3px !important;
        }

        body .content-surface .inventory-id-chip,
        body .content-surface .inventory-type-badge,
        body .content-surface .inventory-status-badge,
        body .content-surface .inventory-meta-pill,
        body .content-surface .clean-admin-pill {
            padding: 2px 6px !important;
            border-radius: 4px !important;
            font-size: 8px !important;
            line-height: 1.1 !important;
        }

        body .content-surface #walkiesTable th.inventory-action-col,
        body .content-surface #walkiesTable td.inventory-action-col {
            position: sticky !important;
            left: auto !important;
            right: 0 !important;
            z-index: 34 !important;
            width: 96px !important;
            min-width: 96px !important;
            max-width: 96px !important;
            text-align: center !important;
            box-shadow: -1px 0 0 rgba(148, 163, 184, .22) !important;
        }

        body .content-surface #walkiesTable th:nth-child(2),
        body .content-surface #walkiesTable td:nth-child(2) {
            width: 64px !important;
            min-width: 64px !important;
            max-width: 64px !important;
        }

        body .content-surface #walkiesTable th:nth-child(3),
        body .content-surface #walkiesTable td:nth-child(3) {
            width: 92px !important;
            min-width: 92px !important;
            max-width: 92px !important;
        }

        body .content-surface #walkiesTable th:nth-child(4),
        body .content-surface #walkiesTable td:nth-child(4) {
            width: 116px !important;
            min-width: 116px !important;
            max-width: 116px !important;
        }

        body .content-surface #walkiesTable th:nth-child(5),
        body .content-surface #walkiesTable td:nth-child(5) {
            width: 92px !important;
            min-width: 92px !important;
            max-width: 92px !important;
        }

        body .content-surface #walkiesTable th:nth-child(6),
        body .content-surface #walkiesTable td:nth-child(6) {
            width: 124px !important;
            min-width: 124px !important;
            max-width: 124px !important;
        }

        body .content-surface #walkiesTable th:nth-child(7),
        body .content-surface #walkiesTable td:nth-child(7) {
            width: 150px !important;
            min-width: 150px !important;
            max-width: 150px !important;
        }

        body .content-surface #walkiesTable th:nth-child(8),
        body .content-surface #walkiesTable td:nth-child(8) {
            width: 88px !important;
            min-width: 88px !important;
            max-width: 88px !important;
        }

        html:not(.dark) body .content-surface:has(.inventory-page-shell) {
            background: #f4f7fb !important;
            padding: 10px !important;
        }

        html:not(.dark) body .content-surface .inventory-page-header {
            min-height: 0 !important;
            margin: 0 !important;
            padding: 0 2px 10px !important;
            border: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
        }

        html:not(.dark) body .content-surface .inventory-page-header .page-title-standard {
            color: #172033 !important;
            font-size: 19px !important;
            line-height: 1.1 !important;
        }

        html:not(.dark) body .content-surface .inventory-page-header .page-subtitle-standard,
        html:not(.dark) body .content-surface .clean-admin-label {
            color: #64748b !important;
            font-size: 9px !important;
        }

        html:not(.dark) body .content-surface .inventory-page-header .wt-btn,
        html:not(.dark) body .content-surface .clean-admin-filter,
        html:not(.dark) body .content-surface .inventory-bulk-bar,
        html:not(.dark) body .content-surface #mainTableContainer.inventory-table-shell {
            border-color: #d8e1ed !important;
            background: transparent !important;
            color: #172033 !important;
            box-shadow: none !important;
        }

        html:not(.dark) body .content-surface .clean-admin-filter {
            padding: 12px !important;
            border-radius: 14px !important;
        }

        html:not(.dark) body .content-surface .clean-admin-filter-grid {
            grid-template-columns: minmax(240px, 1fr) 180px auto !important;
            gap: 10px !important;
        }

        html:not(.dark) body .content-surface .clean-admin-input,
        html:not(.dark) body .content-surface .clean-admin-select,
        html:not(.dark) body .content-surface .clean-admin-reset,
        html:not(.dark) body .content-surface .inventory-bulk-select,
        html:not(.dark) body .content-surface .inventory-bulk-input,
        html:not(.dark) body .content-surface .inventory-bulk-count {
            height: 38px !important;
            border-color: #cbd5e1 !important;
            background: #f8fafc !important;
            color: #172033 !important;
            font-size: 12px !important;
        }

        html:not(.dark) body .content-surface .inventory-bulk-count,
        html:not(.dark) body .content-surface .inventory-bulk-select,
        html:not(.dark) body .content-surface .inventory-bulk-input,
        html:not(.dark) body .content-surface .inventory-bulk-btn {
            height: 28px !important;
            min-height: 28px !important;
            font-size: 9px !important;
        }

        html:not(.dark) body .content-surface .inventory-bulk-bar {
            margin: 8px 0 12px !important;
            padding: 0 8px !important;
            border-radius: 10px !important;
        }

        html:not(.dark) body .content-surface #walkiesTable {
            table-layout: auto !important;
            width: max-content !important;
            min-width: 980px !important;
        }

        html:not(.dark) body .content-surface #walkiesTable thead th {
            height: 30px !important;
            padding: 0 8px !important;
            border-color: #d8e1ed !important;
            background: #eef3f8 !important;
            color: #526781 !important;
            font-size: 9px !important;
        }

        html:not(.dark) body .content-surface #walkiesTable tbody td {
            height: 22px !important;
            min-height: 22px !important;
            padding: 2px 8px !important;
            border-color: #e2e8f0 !important;
            background: #ffffff !important;
            color: #1f2937 !important;
            font-size: 10px !important;
            line-height: 1.15 !important;
        }

        html:not(.dark) body .content-surface #walkiesTable th.inventory-select-col,
        html:not(.dark) body .content-surface #walkiesTable td.inventory-select-col {
            width: 34px !important;
            min-width: 34px !important;
            max-width: 34px !important;
        }

        html:not(.dark) body .content-surface #walkiesTable th.inventory-action-col,
        html:not(.dark) body .content-surface #walkiesTable td.inventory-action-col {
            left: auto !important;
            right: 0 !important;
            width: 96px !important;
            min-width: 96px !important;
            max-width: 96px !important;
        }
    </style>
    <style id="inventory-theme-final-overrides">
        html:not(.dark) body {
            background: #eef3f8 !important;
            color: #172033 !important;
        }

        html:not(.dark) body .content-surface:has(.inventory-page-shell) {
            background: #eef3f8 !important;
        }

        html:not(.dark) body .content-surface .inventory-page-shell {
            background: transparent !important;
            border-color: transparent !important;
            color: #172033 !important;
        }

        html:not(.dark) body .content-surface .inventory-page-header .page-title-standard {
            color: #0f172a !important;
        }

        html:not(.dark) body .content-surface .inventory-page-header .page-subtitle-standard,
        html:not(.dark) body .content-surface .clean-admin-label,
        html:not(.dark) body .content-surface .inventory-bulk-count {
            color: #526781 !important;
        }

        html:not(.dark) body .content-surface .clean-admin-filter,
        html:not(.dark) body .content-surface #mainTableContainer.inventory-table-shell {
            background: #ffffff !important;
            border-color: #cbd5e1 !important;
        }

        html:not(.dark) body .content-surface .clean-admin-input,
        html:not(.dark) body .content-surface .clean-admin-select,
        html:not(.dark) body .content-surface .clean-admin-reset,
        html:not(.dark) body .content-surface .inventory-bulk-select,
        html:not(.dark) body .content-surface .inventory-bulk-input,
        html:not(.dark) body .content-surface .inventory-bulk-btn {
            background: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }

        html:not(.dark) body .content-surface .clean-admin-input::placeholder,
        html:not(.dark) body .content-surface .inventory-bulk-input::placeholder {
            color: #94a3b8 !important;
        }

        html:not(.dark) body .content-surface #walkiesTable thead th,
        html:not(.dark) body .content-surface #walkiesTable thead th.inventory-action-col {
            background: #eef3f8 !important;
            border-color: #cbd5e1 !important;
            color: #334155 !important;
        }

        html:not(.dark) body .content-surface #walkiesTable tbody td,
        html:not(.dark) body .content-surface #walkiesTable td.inventory-action-col {
            background: #ffffff !important;
            border-color: #e2e8f0 !important;
            color: #1f2937 !important;
        }

        html:not(.dark) body .content-surface #walkiesTable tbody tr:hover td {
            background: #f8fafc !important;
        }

        html:not(.dark) body .content-surface .inventory-item-title {
            color: #1f2937 !important;
        }

        html:not(.dark) body .content-surface .inventory-id-chip,
        html:not(.dark) body .content-surface .inventory-type-badge {
            background: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
            color: #334155 !important;
        }

        html.dark body {
            background: #0f172a !important;
            color: #e2e8f0 !important;
        }

        html.dark body .content-surface:has(.inventory-page-shell) {
            background: #0f172a !important;
        }

        html.dark body .content-surface .inventory-page-shell {
            background: transparent !important;
            border-color: transparent !important;
            color: #e2e8f0 !important;
        }

        html.dark body .content-surface .clean-admin-filter,
        html.dark body .content-surface #mainTableContainer.inventory-table-shell {
            background: #0f172a !important;
            border-color: #273449 !important;
        }

        html.dark body .content-surface #walkiesTable thead th,
        html.dark body .content-surface #walkiesTable thead th.inventory-action-col {
            background: #111827 !important;
            border-color: #334155 !important;
            color: #d7e7fb !important;
        }

        html.dark body .content-surface #walkiesTable tbody td,
        html.dark body .content-surface #walkiesTable td.inventory-action-col {
            background: #0f172a !important;
            border-color: #273449 !important;
            color: #e2e8f0 !important;
        }

        html.dark body .content-surface #walkiesTable tbody tr:hover td {
            background: #172033 !important;
        }

        body .content-surface .inventory-filter-inline,
        html:not(.dark) body .content-surface .inventory-filter-inline,
        html.dark body .content-surface .inventory-filter-inline {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: wrap !important;
            align-items: center !important;
            justify-content: flex-start !important;
            gap: 10px !important;
            width: 100% !important;
            max-width: none !important;
        }

        body .content-surface .inventory-filter-inline .inventory-filter-field,
        html:not(.dark) body .content-surface .inventory-filter-inline .inventory-filter-field,
        html.dark body .content-surface .inventory-filter-inline .inventory-filter-field {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            gap: 8px !important;
            width: auto !important;
            min-width: 0 !important;
            max-width: none !important;
        }

        body .content-surface .inventory-filter-inline .clean-admin-label {
            margin: 0 !important;
            line-height: 30px !important;
            white-space: nowrap !important;
        }

        @media (max-width: 720px) {
            body .content-surface #globalSearch.clean-admin-input {
                width: 180px !important;
                max-width: calc(100vw - 170px) !important;
            }
        }
    </style>
    @endpush

    @push('final_styles')
    <style id="inventory-no-left-table-gap">
        body .content-surface #mainTableContainer.inventory-table-shell,
        body .content-surface #inventoryTableScroll.clean-admin-table-scroll {
            padding-left: 0 !important;
            margin-left: 0 !important;
        }

        body .content-surface #walkiesTable {
            margin-left: 0 !important;
        }

        body .content-surface #walkiesTable col.inventory-radio-colgroup {
            width: 92px !important;
        }

        body .content-surface #walkiesTable th:first-child,
        body .content-surface #walkiesTable td:first-child {
            width: 92px !important;
            min-width: 92px !important;
            max-width: 92px !important;
            padding-left: 8px !important;
            padding-right: 8px !important;
            text-align: center !important;
        }
    </style>
    @endpush

    @push('final_styles')
    <style id="inventory-handover-action-final">
        body .content-surface #walkiesTable col.inventory-action-colgroup {
            width: 360px !important;
        }

        body .content-surface #walkiesTable th.inventory-action-col,
        body .content-surface #walkiesTable td.inventory-action-col {
            width: 360px !important;
            min-width: 360px !important;
            max-width: 360px !important;
        }

        body .content-surface #walkiesTable .inventory-action-buttons {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 4px !important;
            flex-wrap: nowrap !important;
        }

        body .content-surface #walkiesTable .inventory-action-buttons .btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 4px !important;
            height: 28px !important;
            min-height: 28px !important;
            padding: 0 8px !important;
            border-radius: 6px !important;
            font-size: 10px !important;
            font-weight: 800 !important;
            white-space: nowrap !important;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        (function () {
            function normalizeInventorySearch(value) {
                return String(value || '').trim().toUpperCase();
            }

            function applyInventorySearchFilter() {
                var searchInput = document.getElementById('globalSearch');
                var statusFilter = document.getElementById('filterStatus');
                var rows = Array.from(document.querySelectorAll('#walkiesTable tbody tr.inventory-row'));
                var keyword = normalizeInventorySearch(searchInput ? searchInput.value : '');
                var status = normalizeInventorySearch(statusFilter ? statusFilter.value : '');

                rows.forEach(function (row) {
                    var haystack = row.dataset.search || row.textContent || '';
                    var rowStatus = normalizeInventorySearch(row.dataset.status || '');
                    var matchesKeyword = !keyword || normalizeInventorySearch(haystack).indexOf(keyword) !== -1;
                    var matchesStatus = !status || rowStatus === status;
                    row.style.display = matchesKeyword && matchesStatus ? '' : 'none';
                });

                var totalItems = document.getElementById('totalItems');
                if (totalItems) {
                    totalItems.textContent = rows.filter(function (row) {
                        return row.style.display !== 'none';
                    }).length;
                }

                if (typeof window.paintInventoryTableTheme === 'function') {
                    window.paintInventoryTableTheme();
                }
            }

            function bindInventorySearchFilter() {
                var searchInput = document.getElementById('globalSearch');
                var statusFilter = document.getElementById('filterStatus');
                var resetBtn = document.getElementById('resetFilters');

                if (searchInput && searchInput.dataset.inventorySearchBound !== 'true') {
                    searchInput.dataset.inventorySearchBound = 'true';
                    searchInput.addEventListener('input', applyInventorySearchFilter);
                    searchInput.addEventListener('keyup', applyInventorySearchFilter);
                    searchInput.addEventListener('keydown', function (event) {
                        if (event.key === 'Enter') {
                            event.preventDefault();
                            applyInventorySearchFilter();
                        }
                    });
                }

                if (statusFilter && statusFilter.dataset.inventorySearchBound !== 'true') {
                    statusFilter.dataset.inventorySearchBound = 'true';
                    statusFilter.addEventListener('change', applyInventorySearchFilter);
                }

                if (resetBtn && resetBtn.dataset.inventorySearchBound !== 'true') {
                    resetBtn.dataset.inventorySearchBound = 'true';
                    resetBtn.addEventListener('click', function () {
                        if (searchInput) searchInput.value = '';
                        if (statusFilter) statusFilter.value = '';
                        applyInventorySearchFilter();
                    });
                }

                applyInventorySearchFilter();
            }

            document.addEventListener('DOMContentLoaded', bindInventorySearchFilter);
            window.addEventListener('load', bindInventorySearchFilter);
        })();
    </script>
    @endpush

    @push('final_styles')
    <style id="inventory-bulk-toolbar-clean-final">
        body .content-surface #bulkActionForm.inventory-bulk-bar {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: wrap !important;
            align-items: center !important;
            justify-content: flex-start !important;
            gap: 10px !important;
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 0 12px !important;
            padding: 12px 14px !important;
            border-radius: 10px !important;
            box-shadow: none !important;
        }

        html:not(.dark) body .content-surface #bulkActionForm.inventory-bulk-bar,
        html[data-theme="light"] body .content-surface #bulkActionForm.inventory-bulk-bar {
            background: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            color: #0f172a !important;
        }

        html.dark body .content-surface #bulkActionForm.inventory-bulk-bar,
        html[data-theme="dark"] body .content-surface #bulkActionForm.inventory-bulk-bar {
            background: #0f172a !important;
            border: 1px solid #273449 !important;
            color: #e2e8f0 !important;
        }

        body .content-surface #bulkActionForm .inventory-bulk-count,
        body .content-surface #bulkActionForm .inventory-bulk-select,
        body .content-surface #bulkActionForm .inventory-bulk-input,
        body .content-surface #bulkActionForm .inventory-bulk-btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            height: 34px !important;
            min-height: 34px !important;
            margin: 0 !important;
            border-radius: 8px !important;
            font-size: 12px !important;
            font-weight: 750 !important;
            line-height: 1 !important;
            box-shadow: none !important;
            white-space: nowrap !important;
        }

        body .content-surface #bulkActionForm .inventory-bulk-count {
            gap: 4px !important;
            min-width: 96px !important;
            padding: 0 12px !important;
        }

        body .content-surface #bulkActionForm .inventory-bulk-controls {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: wrap !important;
            align-items: center !important;
            gap: 10px !important;
            width: auto !important;
            max-width: 100% !important;
        }

        body .content-surface #bulkActionForm .inventory-bulk-select {
            width: 136px !important;
            padding: 0 30px 0 12px !important;
            justify-content: flex-start !important;
        }

        body .content-surface #bulkActionForm .inventory-bulk-input {
            width: 176px !important;
            padding: 0 12px !important;
            justify-content: flex-start !important;
        }

        body .content-surface #bulkActionForm .inventory-bulk-btn {
            width: 72px !important;
            padding: 0 14px !important;
            cursor: pointer !important;
        }

        html:not(.dark) body .content-surface #bulkActionForm .inventory-bulk-count,
        html:not(.dark) body .content-surface #bulkActionForm .inventory-bulk-select,
        html:not(.dark) body .content-surface #bulkActionForm .inventory-bulk-input,
        html:not(.dark) body .content-surface #bulkActionForm .inventory-bulk-btn,
        html[data-theme="light"] body .content-surface #bulkActionForm .inventory-bulk-count,
        html[data-theme="light"] body .content-surface #bulkActionForm .inventory-bulk-select,
        html[data-theme="light"] body .content-surface #bulkActionForm .inventory-bulk-input,
        html[data-theme="light"] body .content-surface #bulkActionForm .inventory-bulk-btn {
            background: #f8fafc !important;
            border: 1px solid #cbd5e1 !important;
            color: #0f172a !important;
        }

        html.dark body .content-surface #bulkActionForm .inventory-bulk-count,
        html.dark body .content-surface #bulkActionForm .inventory-bulk-select,
        html.dark body .content-surface #bulkActionForm .inventory-bulk-input,
        html.dark body .content-surface #bulkActionForm .inventory-bulk-btn,
        html[data-theme="dark"] body .content-surface #bulkActionForm .inventory-bulk-count,
        html[data-theme="dark"] body .content-surface #bulkActionForm .inventory-bulk-select,
        html[data-theme="dark"] body .content-surface #bulkActionForm .inventory-bulk-input,
        html[data-theme="dark"] body .content-surface #bulkActionForm .inventory-bulk-btn {
            background: #111827 !important;
            border: 1px solid #334155 !important;
            color: #e2e8f0 !important;
        }

        body .content-surface #bulkActionForm .inventory-bulk-input::placeholder {
            opacity: 1 !important;
        }

        html:not(.dark) body .content-surface #bulkActionForm .inventory-bulk-input::placeholder,
        html[data-theme="light"] body .content-surface #bulkActionForm .inventory-bulk-input::placeholder {
            color: #94a3b8 !important;
        }

        html.dark body .content-surface #bulkActionForm .inventory-bulk-input::placeholder,
        html[data-theme="dark"] body .content-surface #bulkActionForm .inventory-bulk-input::placeholder {
            color: #64748b !important;
        }

        html:not(.dark) body .content-surface #bulkActionForm .inventory-bulk-btn:not(:disabled):hover,
        html[data-theme="light"] body .content-surface #bulkActionForm .inventory-bulk-btn:not(:disabled):hover {
            background: #0f172a !important;
            border-color: #0f172a !important;
            color: #ffffff !important;
        }

        html.dark body .content-surface #bulkActionForm .inventory-bulk-btn:not(:disabled):hover,
        html[data-theme="dark"] body .content-surface #bulkActionForm .inventory-bulk-btn:not(:disabled):hover {
            background: #0ea5e9 !important;
            border-color: #38bdf8 !important;
            color: #ffffff !important;
        }

        html:not(.dark) body .content-surface #walkiesTable .inventory-status-badge,
        html[data-theme="light"] body .content-surface #walkiesTable .inventory-status-badge {
            border-width: 1px !important;
            border-style: solid !important;
            font-weight: 900 !important;
            opacity: 1 !important;
            text-shadow: none !important;
        }

        html:not(.dark) body .content-surface #walkiesTable .inventory-status-badge[data-status="UNUSED"],
        html[data-theme="light"] body .content-surface #walkiesTable .inventory-status-badge[data-status="UNUSED"] {
            background: #dcfce7 !important;
            border-color: #86efac !important;
            color: #166534 !important;
        }

        html:not(.dark) body .content-surface #walkiesTable .inventory-status-badge[data-status="IN USE"],
        html[data-theme="light"] body .content-surface #walkiesTable .inventory-status-badge[data-status="IN USE"] {
            background: #dbeafe !important;
            border-color: #93c5fd !important;
            color: #1d4ed8 !important;
        }

        html:not(.dark) body .content-surface #walkiesTable .inventory-status-badge[data-status="REPAIRING"],
        html:not(.dark) body .content-surface #walkiesTable .inventory-status-badge[data-status="FAULTY"],
        html:not(.dark) body .content-surface #walkiesTable .inventory-status-badge[data-status="B.E.R"],
        html[data-theme="light"] body .content-surface #walkiesTable .inventory-status-badge[data-status="REPAIRING"],
        html[data-theme="light"] body .content-surface #walkiesTable .inventory-status-badge[data-status="FAULTY"],
        html[data-theme="light"] body .content-surface #walkiesTable .inventory-status-badge[data-status="B.E.R"] {
            background: #fee2e2 !important;
            border-color: #fca5a5 !important;
            color: #b91c1c !important;
        }

        html:not(.dark) body .content-surface #walkiesTable .inventory-status-badge[data-status="UNKNOWN"],
        html[data-theme="light"] body .content-surface #walkiesTable .inventory-status-badge[data-status="UNKNOWN"] {
            background: #f3e8ff !important;
            border-color: #d8b4fe !important;
            color: #7e22ce !important;
        }

        html.dark body .content-surface #walkiesTable .inventory-status-badge,
        html[data-theme="dark"] body .content-surface #walkiesTable .inventory-status-badge {
            font-weight: 900 !important;
            opacity: 1 !important;
        }
    </style>
    @endpush


    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    @endpush

    @section('content')
    @php
        $walkieRadioIds = $walkies->pluck('radio_id')->filter()->unique()->sort()->values();
        $walkieSerials = $walkies->pluck('serial_number')->filter()->unique()->sort()->values();
        $walkieModels = $walkies->pluck('model')->filter()->unique()->sort()->values();
        $walkieOwnerships = $walkies->pluck('ownership')->filter()->unique()->sort()->values();
        $walkieDepartments = $walkies->pluck('department')->filter()->unique()->sort()->values();
        $walkiePositions = $walkies->pluck('position')->filter()->unique()->sort()->values();
        $walkieTemporaryIds = $walkies->pluck('temporary_radio_id')->filter()->unique()->sort()->values();
        $walkieTrackingRefs = $walkies->pluck('tracking_ref')->filter()->unique()->sort()->values();
        $statusOptions = collect(['B.E.R','FAULTY','IN USE','LOST','REPAIRING','UNUSED','UNKNOWN']);
        $inventorySummary = [
            'total' => $walkies->count(),
            'in_use' => $walkies->filter(fn ($walkie) => strtoupper((string) $walkie->status) === 'IN USE')->count(),
            'unused' => $walkies->filter(fn ($walkie) => strtoupper((string) $walkie->status) === 'UNUSED')->count(),
            'repair_faulty' => $walkies->filter(fn ($walkie) => in_array(strtoupper((string) $walkie->status), ['REPAIRING', 'FAULTY', 'B.E.R'], true))->count(),
        ];
        $ownershipTypeOptions = collect(['INDIVIDUAL','SHARED','SPARE','UNALLOCATED']);
        $yesNoOptions = collect([['value' => '0', 'label' => 'NO'], ['value' => '1', 'label' => 'YES']]);
    @endphp


    <div class="inventory-page-shell">
    <div class="inventory-page-header page-header-block">
        <div class="inventory-header-copy">
            <h1 class="page-title-standard text-slate-100">Inventory List</h1>
            <p class="page-subtitle-standard text-slate-400">{{ auth('wt')->user()->wt_role === 'admin_it' ? 'Displaying all walkie talkies with full management access.' : 'Displaying all walkie talkies in read-only mode for executive review.' }}</p>
        </div>
        <div class="inventory-header-actions">
            @if(auth('wt')->user()->wt_role === 'admin_it')
            <button onclick="openImportModal()" class="wt-btn wt-btn-soft">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="margin-right:5px;">
                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
                </svg>
                Import Excel
            </button>
            @endif

            @if(auth('wt')->user()->wt_role === 'admin_it')
            <a href="{{ route('wt.admin.walkies.create') }}" class="wt-btn wt-btn-soft">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="margin-right:5px;">
                    <path d="M8 1a.5.5 0 0 1 .5.5V7.5H14a.5.5 0 0 1 0 1H8.5V14a.5.5 0 0 1-1 0V8.5H2a.5.5 0 0 1 0-1h5.5V1.5A.5.5 0 0 1 8 1z"/>
                </svg>
                Add Item
            </a>
            @else
            <div class="px-4 py-2 rounded-2xl bg-slate-800 border border-slate-700 text-slate-300 text-xs font-black uppercase tracking-[0.15em]">Read Only</div>
            @endif
        </div>
    </div>

    <div class="inventory-summary-grid">
        <div class="inventory-summary-card">
            <p class="inventory-summary-label">Total Records</p>
            <p class="inventory-summary-value">{{ number_format($inventorySummary['total']) }}</p>
        </div>
        <div class="inventory-summary-card">
            <p class="inventory-summary-label">In Use</p>
            <p class="inventory-summary-value is-use">{{ number_format($inventorySummary['in_use']) }}</p>
        </div>
        <div class="inventory-summary-card">
            <p class="inventory-summary-label">Unused</p>
            <p class="inventory-summary-value is-unused">{{ number_format($inventorySummary['unused']) }}</p>
        </div>
        <div class="inventory-summary-card">
            <p class="inventory-summary-label">Repair / Faulty</p>
            <p class="inventory-summary-value is-repair">{{ number_format($inventorySummary['repair_faulty']) }}</p>
        </div>
    </div>

    {{-- Success / Error Alert --}}
    @if(session('success'))
    <div class="alert-success" id="alertBox">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right:8px;flex-shrink:0;">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert-error" id="errorBoxSession">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right:8px;flex-shrink:0;">
            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
        </svg>
        {{ session('error') }}
    </div>
    @endif

    @if($errors instanceof \Illuminate\Support\ViewErrorBag && $errors->any())
    <div class="alert-error mb-6" id="errorBox">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right:8px;flex-shrink:0;">
            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
        </svg>
        <ul class="list-disc pl-5 mt-1">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
    @endif

        {{-- ===== SEARCH & FILTER BAR ===== --}}
        <div class="clean-admin-filter">
            <div class="clean-admin-filter-grid inventory-filter-inline" style="display:flex !important;flex-direction:row !important;flex-wrap:wrap !important;align-items:center !important;justify-content:flex-start !important;gap:10px !important;width:100% !important;">
                {{-- Search Input --}}
                <div class="inventory-filter-field" style="display:flex !important;flex-direction:row !important;align-items:center !important;gap:8px !important;width:auto !important;min-width:0 !important;">
                    <label class="clean-admin-label" for="globalSearch" style="margin:0 !important;line-height:30px !important;white-space:nowrap !important;">Search</label>
                    <input type="text" id="globalSearch" class="clean-admin-input" placeholder="Keywords" style="width:220px !important;max-width:32vw !important;">
                </div>

                {{-- Status Filter --}}
                <div class="inventory-filter-field" style="display:flex !important;flex-direction:row !important;align-items:center !important;gap:8px !important;width:auto !important;min-width:0 !important;">
                    <label class="clean-admin-label" for="filterStatus" style="margin:0 !important;line-height:30px !important;white-space:nowrap !important;">Status</label>
                    <select id="filterStatus" class="clean-admin-select" style="width:160px !important;">
                        <option value="">All Status</option>
                        @foreach($statusOptions as $s)
                        <option value="{{ $s }}">{{ $s }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Reset Button --}}
                <button type="button" id="resetFilters" class="clean-admin-reset" title="Reset all filters" style="width:68px !important;min-width:68px !important;">Reset</button>
            </div>
        </div>

        @php
        $showInventoryBulk = false;
        @endphp

        <div id="mainTableContainer" class="clean-admin-table-shell inventory-table-shell">
            <div id="inventoryTableScroll" class="clean-admin-table-scroll">
                <table id="walkiesTable" class="clean-admin-table text-left">
                <colgroup>
                    @if($showInventoryBulk)
                    <col class="inventory-select-colgroup">
                    @endif
                    <col class="inventory-radio-colgroup">
                    <col class="inventory-status-colgroup">
                    <col class="inventory-serial-colgroup">
                    <col class="inventory-model-colgroup">
                    <col class="inventory-ownership-type-colgroup">
                    <col class="inventory-ownership-colgroup">
                    <col class="inventory-position-colgroup">
                    <col class="inventory-department-colgroup">
                    <col class="inventory-temporary-colgroup">
                    <col class="inventory-tracking-colgroup">
                    <col class="inventory-remarks-colgroup">
                    <col class="inventory-action-colgroup">
                </colgroup>
                <thead>
                    <tr>
                        @if($showInventoryBulk)
                        <th class="px-3 py-3 text-center inventory-select-col"></th>
                        @endif
                        <th class="px-3 py-3">RADIO ID</th>
                        <th class="px-3 py-3">STATUS</th>
                        <th class="px-3 py-3">SERIAL NO.</th>
                        <th class="px-3 py-3">MODEL</th>
                        <th class="px-3 py-3">OWNERSHIP TYPE</th>
                        <th class="px-3 py-3">OWNERSHIP</th>
                        <th class="px-3 py-3">POSITION</th>
                        <th class="px-3 py-3">DEPARTMENT</th>
                        <th class="px-3 py-3">TEMP / SWAPPED WT ID</th>
                        <th class="px-3 py-3 inventory-tracking-col">TRACKING REF</th>
                        <th class="px-3 py-3 inventory-remarks-col">REMARKS</th>
                        <th class="px-3 py-3 text-center inventory-action-col" data-label="ACTION"><span class="inventory-action-heading">ACTION</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/40 text-[11px]">
                    @foreach($walkies as $w)
                    @php
                        $statusValue = strtoupper((string) ($w->status ?: 'UNKNOWN'));
                    @endphp
                    <tr class="inventory-row hover:bg-slate-700/30 transition"
                        data-walkie-id="{{ $w->walkie_id }}"
                        data-status="{{ $statusValue }}"
                        data-model="{{ strtoupper((string) ($w->model ?: 'NO MODEL')) }}"
                        data-ownership-type="{{ strtoupper((string) ($w->ownership_type ?: '-')) }}"
                        data-search="{{ strtoupper(trim(($w->radio_id ?? '') . ' ' . ($w->model ?? '') . ' ' . ($w->serial_number ?? '') . ' ' . ($w->ownership_type ?? '') . ' ' . ($w->shared_with ?? '') . ' ' . ($w->ownership ?? '') . ' ' . ($w->department ?? '') . ' ' . ($w->position ?? '') . ' ' . ($w->status ?? '') . ' ' . ($w->temporary_radio_id ?? '') . ' ' . ($w->tracking_ref ?? '') . ' ' . ($w->remark ?? '') . ' ' . ($w->need_to_change_id ?? '') . ' ' . ($w->ownership_type_to_be ?? ''))) }}">
                        @if($showInventoryBulk)
                        <td class="text-center inventory-select-col">
                            <input type="checkbox" class="inventory-row-checkbox inventory-bulk-checkbox" value="{{ $w->walkie_id }}" data-label="{{ $w->radio_id ?: $w->serial_number ?: $w->walkie_id }}">
                        </td>
                        @endif
                        <td>
                            <span class="clean-admin-pill inventory-id-chip">{{ $w->radio_id ?: '-' }}</span>
                            @if($w->need_to_change_id)
                            <span class="ml-1 text-yellow-300" title="DUPLICATE ID: Need to change">ðŸš©</span>
                            @endif
                        </td>
                        <td>
                            <span class="clean-admin-pill inventory-status-badge" data-status="{{ $statusValue }}">{{ $statusValue }}</span>
                        </td>
                        <td>
                            {{ $w->serial_number ?: '-' }}
                        </td>
                        <td>
                            <div class="inventory-item-title">{{ $w->model ?: 'NO MODEL' }}</div>
                        </td>
                        <td>
                            <span class="clean-admin-pill inventory-type-badge">{{ $w->ownership_type ?: '-' }}</span>
                        </td>
                        <td>
                            {{ $w->ownership ?: '-' }}
                        </td>
                        <td>
                            {{ $w->position ?: '-' }}
                        </td>
                        <td>
                            {{ $w->department ?: '-' }}
                        </td>
                        <td>
                            {{ $w->temporary_radio_id ?: '-' }}
                        </td>
                        <td class="inventory-tracking-col">
                            {{ $w->tracking_ref ?: '-' }}
                        </td>
                        <td class="inventory-remarks-col">
                            <div class="inventory-remark-cell" title="{{ $w->remark ?: '-' }}">{{ $w->remark ?: '-' }}</div>
                        </td>
                        <td class="text-center inventory-action-col">
                            @if(auth('wt')->user()->wt_role === 'admin_it')
                            <div class="inventory-action-buttons">
                                <button type="button" class="btn btn-info btn-sm" title="View Details" onclick="openGlobalWalkieTimeline('{{ $w->walkie_id }}')">
                                    <i class="fa-solid fa-eye"></i>
                                    <span>View</span>
                                </button>

                                <a href="{{ route('wt.admin.walkies.edit', ['walkie' => $w->walkie_id, 'source' => 'index']) }}" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-edit"></i>
                                    <span>Edit</span>
                                </a>

                                @if($statusValue === 'IN USE')
                                <form action="{{ route('wt.admin.walkies.update.status', $w->walkie_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Mark this unit as UNUSED after handover?');">
                                    @csrf
                                    <input type="hidden" name="status" value="UNUSED">
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fa-solid fa-handshake"></i>
                                        <span>Handover</span>
                                    </button>
                                </form>
                                @else
                                <button type="button" class="btn btn-secondary btn-sm" disabled title="Only IN USE units show handover action">
                                    <i class="fa-solid fa-handshake"></i>
                                    <span>Handover</span>
                                </button>
                                @endif

                                <form action="{{ route('wt.admin.walkies.forceDelete', $w->walkie_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this walkie-talkie record?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fa-solid fa-trash"></i>
                                        <span>Delete</span>
                                    </button>
                                </form>
                            </div>
                        @else
                        <button type="button" class="btn btn-info btn-sm" title="View Details" onclick="openGlobalWalkieTimeline('{{ $w->walkie_id }}')">
                            <i class="fa-solid fa-eye"></i>
                            <span>View</span>
                        </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            </table>
            </div>
            <div id="inventoryPagination" class="inventory-table-footer flex items-center justify-between px-4 py-3 bg-[#111827] border-t border-[#263244]">
                <div class="inventory-table-info text-[10px] text-slate-400 font-semibold uppercase tracking-wider">
                    Total: <span id="totalItems">{{ $walkies->count() }}</span> items
                </div>
            </div>
        </div>

    </div>

    <div id="walkieTimelineModal" class="modal-overlay" onclick="closeWalkieTimelineOutside(event)" aria-hidden="true">
        <div class="modal-box walkie-timeline-modal" role="dialog" aria-modal="true" aria-labelledby="timelineTitle">
            <div class="walkie-timeline-header">
                <div class="min-w-0">
                    <p class="walkie-timeline-kicker">Unit Timeline</p>
                    <h2 id="timelineTitle" class="walkie-timeline-title">-</h2>
                    <p id="timelineSubtitle" class="walkie-timeline-subtitle">-</p>
                </div>
                <button type="button" class="walkie-timeline-close" onclick="closeWalkieTimeline()" aria-label="Close timeline">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="timelineSummary" class="walkie-timeline-summary"></div>
            <div id="timelineBody" class="walkie-timeline-body"></div>
        </div>
    </div>

    <div id="walkieQrModal" class="modal-overlay" onclick="closeWalkieQrOutside(event)" aria-hidden="true">
        <div class="modal-box walkie-qr-modal" role="dialog" aria-modal="true" aria-labelledby="walkieQrTitle">
            <div class="walkie-qr-header">
                <div class="min-w-0">
                    <p class="walkie-qr-kicker">Unit QR Code</p>
                    <h2 id="walkieQrTitle" class="walkie-qr-title">-</h2>
                    <p id="walkieQrSubtitle" class="walkie-qr-subtitle">-</p>
                </div>
                <button type="button" class="walkie-qr-close" onclick="closeWalkieQr()" aria-label="Close QR modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="walkie-qr-content">
                <div class="walkie-qr-card">
                    <div id="walkieQrCanvas" class="walkie-qr-canvas"></div>
                    <p id="walkieQrFallback" class="walkie-qr-fallback"></p>
                </div>
                <div id="walkieQrDetails" class="walkie-qr-details"></div>
            </div>
            <div class="walkie-qr-footer">
                <button type="button" class="walkie-qr-action" onclick="downloadWalkieQr()">
                    <i class="fa-solid fa-download"></i>
                    Download
                </button>
                <button type="button" class="walkie-qr-action" onclick="printWalkieQr()">
                    <i class="fa-solid fa-print"></i>
                    Print
                </button>
            </div>
        </div>
    </div>

    @if(auth('wt')->user()->wt_role === 'admin_it')
    {{-- ===================== IMPORT EXCEL MODAL ===================== --}}
    <div id="importModal" class="modal-overlay" onclick="closeImportModalOutside(event)">
        <div class="modal-box">
            <div class="modal-header">
                <div>
                    <h2 class="modal-title">Bulk Import Walkie Talkies</h2>
                    <p class="modal-subtitle">Upload your Excel or CSV file.</p>
                </div>
                <button onclick="closeImportModal()" class="modal-close-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                    </svg>
                </button>
            </div>
            <form action="{{ route('wt.admin.walkies.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-6">
                    <div class="bg-stone-50 border-2 border-dashed border-stone-200 rounded-2xl p-8 text-center">
                        <input type="file" name="file" id="import_file" class="hidden" required onchange="updateFileName(this)">
                        <label for="import_file" class="cursor-pointer">
                            <div class="w-12 h-12 bg-white rounded-full shadow-sm flex items-center justify-center mx-auto mb-4 border border-stone-100">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#142b47" viewBox="0 0 16 16">
                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
                                </svg>
                            </div>
                            <p class="text-xs font-bold text-stone-700" id="fileNameDisplay">Click to upload Excel or CSV</p>
                            <p class="text-[10px] text-stone-400 mt-1 uppercase font-black tracking-widest">Required Headings: radio_id, serial_number, model...</p>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeImportModal()" class="btn-cancel">Cancel</button>
                    <button type="submit" class="btn-submit">Start Import</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===================== ADD DATA MODAL ===================== --}}
    <div id="addModal" class="modal-overlay" onclick="closeModalOutside(event)">
        <div class="modal-box" id="modalBox">

            {{-- Header --}}
            <div class="modal-header">
                <div>
                    <h2 class="modal-title">Add New Walkie Talkie</h2>
                    <p class="modal-subtitle">Fill in all required fields to register a new unit.</p>
                </div>
                <button onclick="closeAddModal()" class="modal-close-btn" title="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                    </svg>
                </button>
            </div>

            {{-- Form --}}
            <form action="{{ route('wt.admin.walkies.store') }}" method="POST" id="addWalkieForm" class="flex flex-col h-full overflow-hidden">
                @csrf
                <div class="modal-body">
                    <div class="form-grid">
                    {{-- Radio ID --}}
                    <div class="form-group">
                        <label class="form-label">Radio ID <span class="required">*</span></label>
                        <select name="radio_id" id="add_radio_id" class="form-input modal-tag-select" data-placeholder="Type or select radio id" required>
                            <option value=""></option>
                            @foreach($walkieRadioIds as $radioId)
                            <option value="{{ $radioId }}" @selected(old('radio_id') === $radioId)>{{ $radioId }}</option>
                            @endforeach
                            @if(old('radio_id') && !$walkieRadioIds->contains(old('radio_id')))
                            <option value="{{ old('radio_id') }}" selected>{{ old('radio_id') }}</option>
                            @endif
                        </select>
                    </div>

                    {{-- Serial Number --}}
                    <div class="form-group">
                        <label class="form-label">Serial Number <span class="required">*</span></label>
                        <select name="serial_number" id="add_serial_number" class="form-input modal-tag-select" data-placeholder="Type or select serial number" required>
                            <option value=""></option>
                            @foreach($walkieSerials as $serial)
                            <option value="{{ $serial }}" @selected(old('serial_number') === $serial)>{{ $serial }}</option>
                            @endforeach
                            @if(old('serial_number') && !$walkieSerials->contains(old('serial_number')))
                            <option value="{{ old('serial_number') }}" selected>{{ old('serial_number') }}</option>
                            @endif
                        </select>
                    </div>

                    {{-- Status --}}
                    <div class="form-group">
                        <label class="form-label">Status <span class="required">*</span></label>
                        <select name="status" id="statusSelect" class="form-input modal-smart-select" data-placeholder="Search status" required onchange="toggleMaintenanceFields()">
                            <option value="" disabled selected>Select status...</option>
                            @foreach($statusOptions as $status)
                            <option value="{{ $status }}" {{ old('status') == $status ? 'selected' : '' }}>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Ownership Type --}}
                    <div class="form-group">
                        <label class="form-label">Ownership Type <span class="required">*</span></label>
                        <select name="ownership_type" id="add_ownership_type" class="form-input modal-tag-select ownership-type-control" data-placeholder="Type or search ownership type" required>
                            <option value="" disabled selected>Select type...</option>
                            @foreach($ownershipTypeOptions as $ownershipType)
                            <option value="{{ $ownershipType }}" {{ old('ownership_type') == $ownershipType ? 'selected' : '' }}>{{ $ownershipType }}</option>
                            @endforeach
                            @if(old('ownership_type') && !$ownershipTypeOptions->contains(strtoupper(trim(old('ownership_type')))))
                            <option value="{{ strtoupper(trim(old('ownership_type'))) }}" selected>{{ strtoupper(trim(old('ownership_type'))) }}</option>
                            @endif
                        </select>
                    </div>

                    <div class="form-group shared-with-group hidden">
                        <label class="form-label">Shared With <span class="required">*</span></label>
                        <input type="text" name="shared_with" id="add_shared_with" value="{{ strtoupper(old('shared_with', '')) }}" class="form-input shared-with-input" placeholder="E.G. USER / TEAM / DEPARTMENT">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Model <span class="required">*</span></label>
                        <select name="model" id="add_model" class="form-input modal-tag-select" data-placeholder="Type or select model" required>
                            <option value="" disabled selected>Select model...</option>
                            @foreach($walkieModels as $model)
                            <option value="{{ $model }}" {{ old('model') == $model ? 'selected' : '' }}>{{ $model }}</option>
                            @endforeach
                            @if(old('model') && !$walkieModels->contains(old('model')))
                            <option value="{{ old('model') }}" selected>{{ old('model') }}</option>
                            @endif
                        </select>
                    </div>

                    {{-- Ownership --}}
                    <div class="form-group">
                        <label class="form-label">Ownership Name</label>
                        <select name="ownership" id="add_ownership" class="form-input modal-tag-select" data-placeholder="Type or select ownership">
                            <option value=""></option>
                            @foreach($walkieOwnerships as $ownership)
                            <option value="{{ $ownership }}" @selected(old('ownership') === $ownership)>{{ $ownership }}</option>
                            @endforeach
                            @if(old('ownership') && !$walkieOwnerships->contains(old('ownership')))
                            <option value="{{ old('ownership') }}" selected>{{ old('ownership') }}</option>
                            @endif
                        </select>
                    </div>

                    {{-- Department --}}
                    <div class="form-group">
                        <label class="form-label">Department</label>
                        <select name="department" id="add_department" class="form-input modal-tag-select" data-placeholder="Type or select department">
                            <option value=""></option>
                            @foreach($walkieDepartments as $department)
                            <option value="{{ $department }}" @selected(old('department') === $department)>{{ $department }}</option>
                            @endforeach
                            @if(old('department') && !$walkieDepartments->contains(old('department')))
                            <option value="{{ old('department') }}" selected>{{ old('department') }}</option>
                            @endif
                        </select>
                    </div>

                    {{-- Position --}}
                    <div class="form-group">
                        <label class="form-label">Position</label>
                        <select name="position" id="add_position" class="form-input modal-tag-select" data-placeholder="Type or select position">
                            <option value=""></option>
                            @foreach($walkiePositions as $position)
                            <option value="{{ $position }}" @selected(old('position') === $position)>{{ $position }}</option>
                            @endforeach
                            @if(old('position') && !$walkiePositions->contains(old('position')))
                            <option value="{{ old('position') }}" selected>{{ old('position') }}</option>
                            @endif
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Temporary / Swapped WT Radio ID</label>
                        <select name="temporary_radio_id" id="add_temporary_radio_id" class="form-input modal-tag-select" data-placeholder="Type or select temporary radio id">
                            <option value=""></option>
                            @foreach($walkieTemporaryIds as $temporaryRadioId)
                            <option value="{{ $temporaryRadioId }}" @selected(old('temporary_radio_id') === $temporaryRadioId)>{{ $temporaryRadioId }}</option>
                            @endforeach
                            @if(old('temporary_radio_id') && !$walkieTemporaryIds->contains(old('temporary_radio_id')))
                            <option value="{{ old('temporary_radio_id') }}" selected>{{ old('temporary_radio_id') }}</option>
                            @endif
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tracking REF</label>
                        <select name="tracking_ref" id="add_tracking_ref" class="form-input modal-tag-select" data-placeholder="Type or select tracking ref">
                            <option value=""></option>
                            @foreach($walkieTrackingRefs as $trackingRef)
                            <option value="{{ $trackingRef }}" @selected(old('tracking_ref') === $trackingRef)>{{ $trackingRef }}</option>
                            @endforeach
                            @if(old('tracking_ref') && !$walkieTrackingRefs->contains(old('tracking_ref')))
                            <option value="{{ old('tracking_ref') }}" selected>{{ old('tracking_ref') }}</option>
                            @endif
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Need To Change ID</label>
                        <select name="need_to_change_id" id="add_need_to_change_id" class="form-input modal-smart-select" data-placeholder="Search option">
                            @foreach($yesNoOptions as $option)
                            <option value="{{ $option['value'] }}" {{ old('need_to_change_id', '0') == $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">ID Change Done</label>
                        <select name="id_change_done" id="add_id_change_done" class="form-input modal-smart-select" data-placeholder="Search option">
                            @foreach($yesNoOptions as $option)
                            <option value="{{ $option['value'] }}" {{ old('id_change_done', '0') == $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Ownership Type To Be</label>
                        <select name="ownership_type_to_be" id="add_ownership_type_to_be" class="form-input modal-tag-select" data-placeholder="Type or search target ownership type">
                            <option value="">Select target ownership type...</option>
                            @foreach($ownershipTypeOptions as $ownershipTypeTarget)
                            <option value="{{ $ownershipTypeTarget }}" {{ old('ownership_type_to_be') == $ownershipTypeTarget ? 'selected' : '' }}>{{ $ownershipTypeTarget }}</option>
                            @endforeach
                            @if(old('ownership_type_to_be') && !$ownershipTypeOptions->contains(strtoupper(trim(old('ownership_type_to_be')))))
                            <option value="{{ strtoupper(trim(old('ownership_type_to_be'))) }}" selected>{{ strtoupper(trim(old('ownership_type_to_be'))) }}</option>
                            @endif
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Special Use</label>
                        <select name="is_special_use" id="add_is_special_use" class="form-input modal-smart-select" data-placeholder="Search option">
                            @foreach($yesNoOptions as $option)
                            <option value="{{ $option['value'] }}" {{ old('is_special_use', '0') == $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Returned</label>
                        <select name="special_use_returned" id="add_special_use_returned" class="form-input modal-smart-select" data-placeholder="Search option">
                            @foreach($yesNoOptions as $option)
                            <option value="{{ $option['value'] }}" {{ old('special_use_returned', '0') == $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Remark --}}
                    <div class="form-group" style="margin-top:10px;">
                        <label class="form-label">Remark</label>
                        <textarea name="remark" class="form-input" style="height:35px; resize:none;" placeholder="Additional notes...">{{ old('remark') }}</textarea>
                    </div>
                </div>

                {{-- Footer Buttons --}}
                <div class="modal-footer">
                    <button type="button" onclick="closeAddModal()" class="btn-cancel">Cancel</button>
                    <button type="submit" class="btn-submit">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="margin-right:6px;">
                            <path d="M8 1a.5.5 0 0 1 .5.5V7.5H14a.5.5 0 0 1 0 1H8.5V14a.5.5 0 0 1-1 0V8.5H2a.5.5 0 0 1 0-1h5.5V1.5A.5.5 0 0 1 8 1z"/>
                        </svg>
                        Save Unit
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===================== QUICK UPDATE MODAL ===================== --}}
    <div id="editModal" class="modal-overlay" onclick="closeEditModalOutside(event)">
        <div class="modal-box" id="editModalBox">
            <div class="modal-header">
                <div>
                    <h2 class="modal-title">Update Unit Details</h2>
                    <p class="modal-subtitle" id="editModalSubtitle">Modify status and ownership details.</p>
                </div>
                <button onclick="closeEditModal()" class="modal-close-btn" title="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                    </svg>
                </button>
            </div>

            <form method="POST" id="editWalkieForm">
                @csrf
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Radio ID <span class="required">*</span></label>
                        <select name="radio_id" id="edit_radio_id" class="form-input modal-tag-select" data-placeholder="Type or select radio id" required>
                            <option value=""></option>
                            @foreach($walkieRadioIds as $radioId)
                            <option value="{{ $radioId }}">{{ $radioId }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Serial No. <span class="required">*</span></label>
                        <select name="serial_number" id="edit_serial_number" class="form-input modal-tag-select" data-placeholder="Type or select serial number" required>
                            <option value=""></option>
                            @foreach($walkieSerials as $serial)
                            <option value="{{ $serial }}">{{ $serial }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Model <span class="required">*</span></label>
                        <select name="model" id="edit_model" class="form-input modal-tag-select" data-placeholder="Type or select model" required>
                            <option value=""></option>
                            @foreach($walkieModels as $editModel)
                            <option value="{{ $editModel }}">{{ $editModel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status <span class="required">*</span></label>
                        <select name="status" id="edit_status" class="form-input modal-smart-select" data-placeholder="Search status" required>
                            @foreach($statusOptions as $editStatus)
                            <option value="{{ $editStatus }}">{{ $editStatus }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ownership Type <span class="required">*</span></label>
                        <select name="ownership_type" id="edit_ownership_type" class="form-input modal-tag-select ownership-type-control" data-placeholder="Type or search ownership type" required>
                            @foreach($ownershipTypeOptions as $editOwnershipType)
                            <option value="{{ $editOwnershipType }}">{{ $editOwnershipType }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group shared-with-group hidden">
                        <label class="form-label">Shared With <span class="required">*</span></label>
                        <input type="text" name="shared_with" id="edit_shared_with" class="form-input shared-with-input" placeholder="E.G. USER / TEAM / DEPARTMENT">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ownership</label>
                        <select name="ownership" id="edit_ownership" class="form-input modal-tag-select" data-placeholder="Type or select ownership">
                            <option value=""></option>
                            @foreach($walkieOwnerships as $ownership)
                            <option value="{{ $ownership }}">{{ $ownership }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Position</label>
                        <select name="position" id="edit_position" class="form-input modal-tag-select" data-placeholder="Type or select position">
                            <option value=""></option>
                            @foreach($walkiePositions as $position)
                            <option value="{{ $position }}">{{ $position }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group form-group-full">
                        <label class="form-label">Department</label>
                        <select name="department" id="edit_department" class="form-input modal-tag-select" data-placeholder="Type or select department">
                            <option value=""></option>
                            @foreach($walkieDepartments as $department)
                            <option value="{{ $department }}">{{ $department }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Temporary / Swapped WT Radio ID</label>
                        <select name="temporary_radio_id" id="edit_temporary_radio_id" class="form-input modal-tag-select" data-placeholder="Type or select temporary radio id">
                            <option value=""></option>
                            @foreach($walkieTemporaryIds as $temporaryRadioId)
                            <option value="{{ $temporaryRadioId }}">{{ $temporaryRadioId }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tracking REF</label>
                        <select name="tracking_ref" id="edit_tracking_ref" class="form-input modal-tag-select" data-placeholder="Type or select tracking ref">
                            <option value=""></option>
                            @foreach($walkieTrackingRefs as $trackingRef)
                            <option value="{{ $trackingRef }}">{{ $trackingRef }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Need To Change ID</label>
                        <select name="need_to_change_id" id="edit_need_to_change_id" class="form-input modal-smart-select" data-placeholder="Search option">
                            @foreach($yesNoOptions as $option)
                            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">ID Change Done</label>
                        <select name="id_change_done" id="edit_id_change_done" class="form-input modal-smart-select" data-placeholder="Search option">
                            @foreach($yesNoOptions as $option)
                            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ownership Type To Be</label>
                        <select name="ownership_type_to_be" id="edit_ownership_type_to_be" class="form-input modal-tag-select" data-placeholder="Type or search target ownership type">
                            <option value="">Select target ownership type...</option>
                            @foreach($ownershipTypeOptions as $targetOwnershipType)
                            <option value="{{ $targetOwnershipType }}">{{ $targetOwnershipType }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Special Use</label>
                        <select name="is_special_use" id="edit_is_special_use" class="form-input modal-smart-select" data-placeholder="Search option">
                            @foreach($yesNoOptions as $option)
                            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Returned</label>
                        <select name="special_use_returned" id="edit_special_use_returned" class="form-input modal-smart-select" data-placeholder="Search option">
                            @foreach($yesNoOptions as $option)
                            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group form-group-full">
                        <label class="form-label">Remarks</label>
                        <textarea name="remark" id="edit_remark" class="form-input" rows="3" placeholder="Remarks"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeEditModal()" class="btn-cancel">Cancel</button>
                    <button type="submit" class="btn-submit">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <style>
        /* ===== Add Button ===== */
        .add-btn {
            display: inline-flex; align-items: center; padding: 7px 18px; background: rgba(15, 23, 42, 0.96); 
            color: #e2e8f0; font-size: 11px; font-weight: 800; border-radius: 12px; text-decoration: none; 
            transition: 0.2s; border: 1px solid rgba(96, 165, 250, 0.32); text-transform: uppercase; letter-spacing: 0.05em;
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.12); cursor: pointer;
        }
        .add-btn:hover { background: #162033; transform: translateY(-1px); box-shadow: 0 12px 24px rgba(15, 23, 42, 0.18); }
        .action-btn {
            border: 1px solid rgba(96, 165, 250, 0.28);
            background: rgba(15, 23, 42, 0.96);
            color: #e2e8f0;
            border-radius: 12px;
            padding: 8px 14px;
            font-size: 11px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.15s;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            box-shadow: 0 0 0 1px rgba(30, 41, 59, 0.42), 0 0 14px rgba(59, 130, 246, 0.12);
        }
        .action-btn:hover {
            background: #162033;
            border-color: rgba(96, 165, 250, 0.42);
            box-shadow: 0 0 0 1px rgba(30, 41, 59, 0.5), 0 0 18px rgba(59, 130, 246, 0.18);
            transform: translateY(-1px);
        }
        .danger-btn {
            color: #fee2e2;
            border-color: rgba(248, 113, 113, 0.28);
            background: rgba(69, 10, 10, 0.96);
            box-shadow: 0 0 0 1px rgba(127, 29, 29, 0.35), 0 0 14px rgba(239, 68, 68, 0.12);
        }
        .danger-btn:hover {
            background: rgba(91, 13, 13, 0.98);
            border-color: rgba(248, 113, 113, 0.42);
            transform: translateY(-1px);
        }
        .action-stack {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        /* ===== Alerts ===== */
        .alert-success, .alert-error {
            display: flex;
            align-items: flex-start;
            padding: 10px 14px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            margin-bottom: 12px;
            animation: slideDown 0.3s ease;
        }
        .alert-success {
            background: #ecfdf5;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* ===== Modal Form Styling ===== */
        #addModal,
        #editModal {
            align-items: center !important;
            justify-content: center !important;
            overflow: hidden !important;
            padding: 20px !important;
        }
        #addModal #modalBox,
        #editModal #editModalBox {
            width: calc(100vw - 40px) !important;
            max-width: 1360px !important;
            height: calc(100vh - 40px) !important;
            max-height: calc(100vh - 40px) !important;
            overflow: hidden !important;
            display: flex !important;
            flex-direction: column !important;
            border-radius: 32px !important;
            border: 1px solid rgba(231, 229, 228, 0.9) !important;
            background: #fff !important;
            box-shadow: 0 28px 70px rgba(15, 23, 42, 0.18) !important;
            color: #1e293b !important;
            margin: 0 auto !important;
        }
        #addModal .modal-header,
        #editModal .modal-header {
            display: flex !important;
            align-items: flex-start !important;
            justify-content: space-between !important;
            gap: 16px !important;
            padding: 28px 36px 22px !important;
            border-bottom: 1px solid #f1f5f9 !important;
            background: linear-gradient(180deg, #ffffff 0%, #fffaf5 100%) !important;
        }
        #addModal .modal-title,
        #editModal .modal-title {
            margin: 0 !important;
            font-size: 16px !important;
            line-height: 1.2 !important;
            font-weight: 900 !important;
            letter-spacing: -0.02em !important;
            color: #1e293b !important;
            text-transform: uppercase !important;
        }
        #addModal .modal-subtitle,
        #editModal .modal-subtitle {
            margin-top: 6px !important;
            font-size: 11px !important;
            line-height: 1.5 !important;
            color: #64748b !important;
            font-weight: 600 !important;
        }
        .modal-close-btn {
            width: 52px;
            height: 52px;
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            color: #94a3b8;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.18s ease;
            flex-shrink: 0;
        }
        .modal-close-btn:hover {
            background: #eef2ff;
            color: #475569;
            border-color: #cbd5e1;
        }
        #addModal .modal-body,
        #editModal .modal-body {
            padding: 28px 36px 8px !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            background: #fff !important;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px 22px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .form-group.form-group-full {
            grid-column: 1 / -1;
        }
        #addModal .form-label,
        #editModal .form-label {
            margin: 0 !important;
            font-size: 10px !important;
            font-weight: 900 !important;
            letter-spacing: 0.14em !important;
            text-transform: uppercase !important;
            color: #78716c !important;
        }
        .required {
            color: #dc2626;
        }
        #addModal .form-input,
        #editModal .form-input {
            width: 100% !important;
            min-height: 46px !important;
            height: auto !important;
            border-radius: 16px !important;
            border: 1px solid #e7e5e4 !important;
            background: #fffaf5 !important;
            padding: 12px 14px !important;
            font-size: 12px !important;
            font-weight: 700 !important;
            color: #334155 !important;
            outline: none !important;
            transition: border-color 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
        }
        #addModal .form-input:focus,
        #editModal .form-input:focus {
            border-color: #8b5e3c !important;
            background: #fff !important;
            box-shadow: 0 0 0 4px rgba(139, 94, 60, 0.10) !important;
        }
        #addModal .form-input::placeholder,
        #editModal .form-input::placeholder {
            color: #94a3b8 !important;
            font-weight: 600 !important;
        }
        #addModal .select2-container,
        #editModal .select2-container {
            width: 100% !important;
        }
        #addModal .select2-container--default .select2-selection--single,
        #addModal .select2-container--default .select2-selection--multiple,
        #editModal .select2-container--default .select2-selection--single,
        #editModal .select2-container--default .select2-selection--multiple {
            min-height: 46px !important;
            height: auto !important;
            border-radius: 16px !important;
            border: 1px solid #e7e5e4 !important;
            background: #fffaf5 !important;
            padding: 6px 14px !important;
            display: flex !important;
            align-items: center !important;
            box-shadow: none !important;
        }
        #addModal .select2-container--default.select2-container--focus .select2-selection--single,
        #addModal .select2-container--default.select2-container--focus .select2-selection--multiple,
        #addModal .select2-container--default.select2-container--open .select2-selection--single,
        #addModal .select2-container--default.select2-container--open .select2-selection--multiple,
        #editModal .select2-container--default.select2-container--focus .select2-selection--single,
        #editModal .select2-container--default.select2-container--focus .select2-selection--multiple,
        #editModal .select2-container--default.select2-container--open .select2-selection--single,
        #editModal .select2-container--default.select2-container--open .select2-selection--multiple {
            border-color: #8b5e3c !important;
            background: #fff !important;
            box-shadow: 0 0 0 4px rgba(139, 94, 60, 0.10) !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered,
        .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            color: #334155;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.4;
            padding-left: 0;
            padding-right: 24px;
        }
        .select2-container--default .select2-selection--single .select2-selection__placeholder,
        .select2-container--default .select2-search--inline .select2-search__field::placeholder {
            color: #94a3b8;
            font-weight: 600;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 44px;
            right: 12px;
        }
        .select2-container--default .select2-selection--single .select2-selection__clear {
            margin-right: 8px;
            color: #64748b;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            margin-top: 0;
            background: #f3e8d8;
            border: 1px solid #dcc2a5;
            color: #6b4423;
            border-radius: 999px;
            padding: 3px 8px;
            font-size: 10px;
            font-weight: 800;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            border-right: 0;
            color: #8b5e3c;
            margin-right: 6px;
        }
        .select2-dropdown {
            border: 1px solid #e7e5e4;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.16);
        }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #e7e5e4;
            border-radius: 12px;
            padding: 10px 12px;
            font-size: 12px;
            font-weight: 700;
            color: #334155;
            outline: none;
        }
        .select2-results__option {
            font-size: 12px;
            font-weight: 700;
            padding: 10px 14px;
        }
        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background: #8b5e3c;
            color: #fff;
        }
        .select2-container--default .select2-results__option--selected {
            background: #f5efe6;
            color: #6b4423;
        }
        #addModal textarea.form-input,
        #editModal textarea.form-input {
            min-height: 110px !important;
            resize: vertical !important;
        }
        #addModal .modal-footer,
        #editModal .modal-footer {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-end !important;
            gap: 12px !important;
            padding: 20px 36px 28px !important;
            border-top: 1px solid #f1f5f9 !important;
            background: #f8fafc !important;
        }
        .btn-cancel,
        .btn-submit {
            min-height: 46px;
            padding: 0 18px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            transition: all 0.18s ease;
        }
        .btn-cancel {
            border: 1px solid #d6d3d1;
            background: #fff;
            color: #57534e;
        }
        .btn-cancel:hover {
            background: #f5f5f4;
        }
        .btn-submit {
            border: 1px solid #a67b5b;
            background: #8b5e3c;
            color: #fff;
            box-shadow: 0 14px 28px rgba(139, 94, 60, 0.18);
        }
        .btn-submit:hover {
            background: #724d31;
            transform: translateY(-1px);
        }

        /* ===== DataTable Overrides ===== */
        #walkiesTable {
            min-width: 900px;
        }
        #walkiesTable th,
        #walkiesTable td {
            white-space: nowrap;
        }



        .dark .inventory-table-shell {
            background: #182233;
            border-color: #334155;
            box-shadow: 0 18px 38px rgba(0, 0, 0, 0.32);
        }

        /* ===== SLIDER / SCROLLBAR STYLING ===== */
        #topScrollContainer,
        #walkiesTable_wrapper .dataTables_scrollBody {
            scrollbar-width: auto;
            scrollbar-color: #cbd5e1 transparent;
        }
        
        /* Webkit (Chrome/Safari) */
        #topScrollContainer::-webkit-scrollbar, 
        #walkiesTable_wrapper .dataTables_scrollBody::-webkit-scrollbar {
            height: 10px;
            width: 10px;
        }
        #topScrollContainer::-webkit-scrollbar-track, 
        #walkiesTable_wrapper .dataTables_scrollBody::-webkit-scrollbar-track {
            background: transparent;
            border-radius: 10px;
        }
        #topScrollContainer::-webkit-scrollbar-thumb, 
        #walkiesTable_wrapper .dataTables_scrollBody::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
            border: 2px solid #ffffff;
        }
        #topScrollContainer::-webkit-scrollbar-thumb:hover, 
        #walkiesTable_wrapper .dataTables_scrollBody::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        #walkiesTable_wrapper .dataTables_scrollBody {
            -ms-overflow-style: auto;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
            overflow-x: auto !important;
            padding-bottom: 0;
        }
        #walkiesTable_wrapper .dataTables_scrollBody::-webkit-scrollbar {
            display: block;
            height: 10px;
        }
        #walkiesTable_wrapper .dataTables_scrollBody thead,
        #walkiesTable_wrapper .dataTables_scrollBody thead tr,
        #walkiesTable_wrapper .dataTables_scrollBody thead th {
            height: 0 !important;
            max-height: 0 !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            border-top: 0 !important;
            border-bottom: 0 !important;
            line-height: 0 !important;
            font-size: 0 !important;
            overflow: hidden !important;
        }

        /* Hide scroller if no overflow */
        #topScrollContainer.hidden { display: none !important; }
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #dbe3ef !important;
            border-radius: 10px !important;
            padding: 9px 14px !important;
            background: #ffffff !important;
            color: #1e293b !important;
            outline: none !important;
            margin-bottom: 0;
            font-size: 11px !important;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        }
        .dataTables_wrapper .dataTables_filter input::placeholder {
            color: #94a3b8 !important;
        }
        .dataTables_wrapper .dataTables_filter label,
        .dataTables_wrapper .dataTables_length label {
            color: #475569 !important;
            font-weight: 800 !important;
        }
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #dbe3ef !important;
            border-radius: 10px !important;
            padding: 8px 30px 8px 12px !important;
            background: #ffffff !important;
            color: #1e293b !important;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        }
        .dataTables_wrapper table.dataTable thead th,
        .dataTables_wrapper table.dataTable tbody td {
            padding: 13px 16px !important;
            font-size: 11px;
        }
        .dataTables_wrapper table.dataTable thead th {
            background: #f8fafc !important;
            color: #64748b !important;
            border-bottom: 1px solid #e2e8f0 !important;
        }
        .dataTables_wrapper table.dataTable tbody td {
            color: #334155 !important;
            border-top: 1px solid #eef2f7 !important;
        }
        .inventory-table-shell {
            border: 1px solid rgba(51, 65, 85, 0.42);
            border-radius: 24px;
            overflow: hidden;
            background: linear-gradient(180deg, rgba(30, 41, 59, 0.98), rgba(15, 23, 42, 0.98));
            box-shadow: 0 22px 50px rgba(2, 6, 23, 0.22);
            padding: 24px;
        }
        .inventory-table-shell table.dataTable {
            margin-top: 0 !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
        }
        .inventory-id-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 70px;
            padding: 7px 12px;
            border-radius: 999px;
            border: 1px solid rgba(56, 189, 248, 0.18);
            background: rgba(14, 165, 233, 0.08);
            color: #e0f2fe;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 0.08em;
        }
        .inventory-item-title {
            font-size: 15px;
            font-weight: 900;
            line-height: 1.15;
            letter-spacing: -0.01em;
            color: #f8fafc;
            text-transform: uppercase;
        }
        .inventory-item-meta {
            margin-top: 6px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .inventory-meta-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 10px;
            border-radius: 999px;
            background: rgba(148, 163, 184, 0.1);
            border: 1px solid rgba(148, 163, 184, 0.12);
            color: #cbd5e1;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
        .inventory-type-badge,
        .inventory-status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            border: 1px solid transparent;
            white-space: nowrap;
        }
        .inventory-type-badge {
            background: rgba(148, 163, 184, 0.14);
            border-color: rgba(148, 163, 184, 0.12);
            color: #e2e8f0;
        }
        .inventory-status-badge[data-status="UNUSED"] {
            background: rgba(34, 197, 94, 0.14);
            border-color: rgba(74, 222, 128, 0.18);
            color: #bbf7d0;
        }
        .inventory-status-badge[data-status="IN USE"] {
            background: rgba(59, 130, 246, 0.14);
            border-color: rgba(96, 165, 250, 0.18);
            color: #bfdbfe;
        }
        .inventory-status-badge[data-status="REPAIRING"],
        .inventory-status-badge[data-status="FAULTY"],
        .inventory-status-badge[data-status="B.E.R"] {
            background: rgba(248, 113, 113, 0.14);
            border-color: rgba(248, 113, 113, 0.2);
            color: #fecaca;
        }
        .inventory-status-badge[data-status="TEMPORARY"],
        .inventory-status-badge[data-status="CHANGE ID"] {
            background: rgba(250, 204, 21, 0.14);
            border-color: rgba(250, 204, 21, 0.2);
            color: #fde68a;
        }
        .inventory-status-badge[data-status="UNKNOWN"],
        .inventory-status-badge[data-status="CALIBRATING"],
        .inventory-status-badge[data-status="LOST"] {
            background: rgba(168, 85, 247, 0.14);
            border-color: rgba(192, 132, 252, 0.2);
            color: #e9d5ff;
        }
        .inventory-remark-cell {
            max-width: 220px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .inventory-quantity-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 38px;
            height: 38px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(148, 163, 184, 0.14);
            color: #f8fafc;
            font-size: 13px;
            font-weight: 900;
        }
        .inventory-date-block {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .inventory-date-primary {
            color: #f8fafc;
            font-size: 12px;
            font-weight: 800;
            line-height: 1.2;
        }
        .inventory-date-secondary {
            color: #94a3b8;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        .inventory-action-stack {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .inventory-table-shell .wt-btn {
            min-height: 36px;
            border-radius: 12px;
            padding: 8px 14px;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: 0.08em;
        }
        .dataTables_wrapper table.dataTable tbody tr:nth-child(odd) {
            background: #ffffff !important;
        }
        .dataTables_wrapper table.dataTable tbody tr:nth-child(even) {
            background: #fbfdff !important;
        }
        .dataTables_wrapper table.dataTable tbody tr:hover {
            background: #f1f5f9 !important;
        }
        #walkiesTable_wrapper .dataTables_paginate .paginate_button {
            border-radius: 0 !important;
            border: 1px solid transparent !important;
            background: transparent !important;
            font-weight: 600 !important;
            font-size: 12px !important;
            color: #64748b !important;
            box-shadow: none !important;
            margin-left: 8px !important;
            padding: 0.5rem 0.75rem !important;
        }
        #walkiesTable_wrapper .dataTables_paginate .paginate_button:hover {
            border-color: transparent !important;
            background: transparent !important;
            color: #334155 !important;
        }
        #walkiesTable_wrapper .dataTables_paginate .paginate_button.current,
        #walkiesTable_wrapper .dataTables_paginate .paginate_button.current:hover {
            border-radius: 2px !important;
            background: #ffffff !important;
            color: #64748b !important;
            border-color: #d1d5db !important;
            box-shadow: none !important;
            min-width: 38px;
            text-align: center;
        }

        #walkiesTable tbody td .text-slate-100,
        #walkiesTable tbody td .text-slate-200,
        #walkiesTable tbody td .text-slate-300,
        #walkiesTable tbody td .text-slate-400 {
            color: inherit !important;
        }

        #walkiesTable tbody td:first-child,
        #walkiesTable tbody td .font-mono {
            color: #0f172a !important;
        }

        .dark .dataTables_wrapper .dataTables_filter input,
        .dark .dataTables_wrapper .dataTables_length select {
            background: #111827 !important;
            border-color: #334155 !important;
            color: #f8fafc !important;
        }

        .dark .dataTables_wrapper .dataTables_filter label,
        .dark .dataTables_wrapper .dataTables_length label,
        .dark .dataTables_wrapper .dt-control-label {
            color: #cbd5e1 !important;
        }

        .dark .dataTables_wrapper table.dataTable thead th {
            background: #111827 !important;
            color: #94a3b8 !important;
            border-color: #334155 !important;
        }

        .dark .dataTables_wrapper table.dataTable tbody td {
            color: #cbd5e1 !important;
            border-color: #1f2937 !important;
        }

        .dark .dataTables_wrapper table.dataTable tbody tr:nth-child(odd) {
            background: #0f172a !important;
        }

        .dark .dataTables_wrapper table.dataTable tbody tr:nth-child(even) {
            background: #111827 !important;
        }

        .dark .dataTables_wrapper table.dataTable tbody tr:hover {
            background: #1e293b !important;
        }
        .dark .inventory-table-shell {
            border-color: rgba(51, 65, 85, 0.9);
            background: linear-gradient(180deg, rgba(30, 41, 59, 0.98), rgba(15, 23, 42, 0.98));
            box-shadow: 0 24px 56px rgba(0, 0, 0, 0.3);
        }

        .dark #walkiesTable tbody td:first-child,
        .dark #walkiesTable tbody td .font-mono {
            color: #f8fafc !important;
        }

        @media (max-width: 600px) {
            .form-grid { grid-template-columns: 1fr; }
            .modal-box { padding: 22px 16px; }
            #addModal,
            #editModal {
                padding: 12px !important;
            }
            #addModal #modalBox,
            #editModal #editModalBox {
                width: calc(100vw - 24px) !important;
                height: calc(100vh - 24px) !important;
                max-height: calc(100vh - 24px) !important;
                border-radius: 24px !important;
            }
        }

        /* ===== Filter Bar ===== */
        .filter-bar {
            background: linear-gradient(135deg, rgba(255,255,255,0.96), rgba(253,251,247,0.92));
            border: 1px solid #e7e5e4;
            border-radius: 20px;
            padding: 16px 20px 14px;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
        }
        .dark .filter-bar {
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.98), rgba(15, 23, 42, 0.98));
            border-color: rgba(51, 65, 85, 0.42);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
        }
        .filter-label {
            display: block;
            font-size: 8px;
            font-weight: 900;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.16em;
            margin-bottom: 5px;
        }
        .search-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        .search-icon {
            position: absolute;
            left: 14px;
            color: #a8a29e;
            font-size: 13px;
            pointer-events: none;
            z-index: 1;
        }
        .search-input {
            width: 100%;
            border: 1.5px solid #e7e5e4;
            border-radius: 12px;
            padding: 7px 32px 7px 32px;
            font-size: 11px;
            font-weight: 600;
            color: #1e293b;
            background: #fff;
            outline: none;
            transition: all 0.2s;
            font-family: inherit;
        }
        .search-input:focus {
            border-color: #142b47;
            box-shadow: 0 0 0 3px rgba(61,43,31,0.08);
            background: #fff;
        }
        .search-input::placeholder {
            color: #c8c3be;
            font-weight: 500;
            text-transform: none;
        }
        .dark .search-input::placeholder {
            color: rgba(148, 163, 184, 0.8) !important;
        }
        .clear-search-btn {
            position: absolute;
            right: 10px;
            background: #f5f5f4;
            border: none;
            border-radius: 8px;
            width: 26px; height: 26px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            color: #78716c;
            font-size: 11px;
            transition: all 0.15s;
        }
        .clear-search-btn:hover {
            background: #e7e5e4;
            color: #142b47;
        }
        .filter-group {
            min-width: 140px;
        }
        .filter-select {
            width: 100%;
            border: 1.5px solid #e7e5e4;
            border-radius: 12px;
            padding: 7px 28px 7px 10px;
            font-size: 10px;
            font-weight: 700;
            color: #1e293b;
            background: #fff;
            outline: none;
            cursor: pointer;
            transition: all 0.2s;
            font-family: inherit;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 16 16'%3E%3Cpath fill='%23a8a29e' d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
        }
        .filter-select:focus {
            border-color: #142b47;
            box-shadow: 0 0 0 3px rgba(61,43,31,0.08);
        }
        .filter-select.active-filter {
            border-color: #142b47;
            background-color: #FDFBF7;
        }
        .reset-filter-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 16px;
            border: 1.5px solid #e7e5e4;
            border-radius: 12px;
            background: #fff;
            color: #78716c;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s;
            font-family: inherit;
            white-space: nowrap;
        }
        .reset-filter-btn:hover {
            background: #fef2f2;
            border-color: #fca5a5;
            color: #dc2626;
        }
        .active-filters-bar {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
            padding: 8px 14px;
            background: rgba(61,43,31,0.04);
            border-radius: 10px;
            border: 1px solid rgba(61,43,31,0.06);
        }

        /* Inventory page uses the custom compact filter bar only. */
        #walkiesTable_wrapper > .dataTables_filter,
        #walkiesTable_wrapper > .dataTables_length {
            display: none !important;
        }
        .inventory-table-controls {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 10px 16px;
            flex-wrap: nowrap;
            margin: 0;
            padding: 30px 48px 22px;
            width: 100%;
            box-sizing: border-box;
            position: relative;
            z-index: 2;
            background: transparent;
        }
        .dark .inventory-table-controls {
            background: transparent;
        }
        .inventory-table-controls .dataTables_length,
        .inventory-table-controls .dataTables_filter,
        .inventory-table-controls .dt-buttons {
            margin: 0 !important;
            padding: 0 !important;
            float: none !important;
            width: auto;
        }
        .inventory-table-controls .dt-buttons {
            display: none !important;
        }
        .dataTables_wrapper > .dt-buttons,
        .dataTables_wrapper .dt-buttons.hidden {
            display: none !important;
        }
        .inventory-table-controls .dataTables_filter {
            margin-left: auto !important;
            flex-shrink: 0;
            text-align: right !important;
        }
        .inventory-table-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 22px;
            padding: 28px 48px 34px;
            margin-top: 0;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
            border-top: 0;
            background: transparent;
        }
        .dark .inventory-table-footer {
            background: transparent;
        }
        #walkiesTable_wrapper > .dataTables_info,
        #walkiesTable_wrapper > .dataTables_paginate {
            display: none !important;
        }
        .inventory-table-info {
            min-width: 0;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.4;
            color: #cbd5e1;
            white-space: nowrap;
        }
        .dark .inventory-table-info {
            color: #cbd5e1;
        }
        .inventory-table-pagination {
            display: inline-flex;
            align-items: center;
            justify-content: flex-end;
            gap: 18px;
            flex-shrink: 0;
            margin-left: auto;
        }
        .inventory-page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 58px;
            height: 56px;
            border-radius: 4px;
            border: 1px solid transparent;
            font-size: 12px;
            font-weight: 700;
            color: #cbd5e1;
            line-height: 1;
        }
        .inventory-page-link {
            background: transparent;
            cursor: pointer;
            padding: 0 8px;
        }
        .inventory-page-link:not(:disabled):hover {
            color: #f8fafc;
            background: rgba(148, 163, 184, 0.12);
        }
        .inventory-page-link:disabled {
            opacity: 0.45;
            cursor: default;
        }
        .inventory-page-current {
            display: none !important;
        }
        .dark .inventory-page-link {
            color: #cbd5e1;
        }
        .dataTables_wrapper .dataTables_length label,
        .dataTables_wrapper .dataTables_filter label {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: nowrap;
            color: #475569;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.02em;
            line-height: 1.2;
            margin: 0;
        }
        .dataTables_wrapper .dt-control-label {
            display: inline-block;
            color: #475569;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.02em;
            line-height: 1.1;
            white-space: nowrap;
        }
        .dataTables_wrapper .dataTables_length select {
            min-width: 82px;
            width: 82px !important;
            min-height: 32px;
            padding: 5px 24px 5px 10px !important;
            border-radius: 10px !important;
        }
        .dataTables_wrapper .dataTables_filter input {
            width: 210px !important;
            min-height: 32px;
            padding: 5px 10px !important;
            border-radius: 10px !important;
            margin-bottom: 0 !important;
        }

        /* Pagination styling */
        #walkiesTable_wrapper .dataTables_info {
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            padding-top: 0 !important;
        }
        #walkiesTable_wrapper .dataTables_paginate {
            padding-top: 0 !important;
        }
        @media (max-width: 1024px) {
            .inventory-table-controls {
                align-items: stretch;
                gap: 8px;
                margin: 0;
                padding: 22px 24px 18px;
                flex-wrap: wrap;
            }
            .inventory-table-controls .dataTables_length,
            .inventory-table-controls .dataTables_filter,
            .inventory-table-controls .dt-buttons {
                width: 100%;
            }
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter {
                margin-bottom: 0 !important;
                padding: 0 !important;
                text-align: left !important;
                width: 100%;
            }
            .dataTables_wrapper .dataTables_length label,
            .dataTables_wrapper .dataTables_filter label {
                align-items: center;
                flex-direction: row;
                gap: 8px;
            }
            .dataTables_wrapper .dt-control-label {
                font-size: 11px;
            }
            .dataTables_wrapper .dataTables_length select {
                width: 118px !important;
                margin: 0 !important;
                min-height: 36px;
                border-radius: 10px !important;
            }
            .dataTables_wrapper .dataTables_filter input {
                width: 100% !important;
                margin: 0 !important;
                min-height: 36px;
                border-radius: 10px !important;
            }
        }
        @media (max-width: 768px) {
            .inventory-page-header {
                margin-bottom: 18px;
            }
            .inventory-header-actions {
                width: 100%;
                flex-direction: column;
            }
            .inventory-header-actions .wt-btn {
                width: 100%;
                justify-content: center;
                min-height: 32px;
                padding: 7px 10px;
                border-radius: 8px;
            }
            #mainTableContainer {
                border-radius: 18px !important;
            }
            .inventory-table-controls {
                display: grid;
                grid-template-columns: 110px minmax(0, 1fr);
                gap: 10px;
                align-items: end;
                margin: 0;
                padding: 18px 18px 16px;
            }
            .inventory-table-controls .dataTables_length,
            .inventory-table-controls .dataTables_filter,
            .inventory-table-controls .dt-buttons {
                width: auto;
                min-width: 0;
            }
            .inventory-table-controls .dt-buttons {
                grid-column: 1 / -1;
            }
            .inventory-table-footer {
                align-items: center;
                flex-direction: row;
                padding: 12px 18px 22px;
            }
            .inventory-item-title {
                font-size: 13px;
            }
            .inventory-id-chip,
            .inventory-type-badge,
            .inventory-status-badge,
            .inventory-meta-pill {
                font-size: 9px;
            }
            .inventory-action-stack {
                flex-direction: column;
            }
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter {
                margin-bottom: 0 !important;
                padding: 0 !important;
                text-align: left !important;
            }
            .dataTables_wrapper .dataTables_length label,
            .dataTables_wrapper .dataTables_filter label {
                gap: 4px !important;
                font-size: 10px !important;
                line-height: 1.2 !important;
                white-space: normal !important;
            }
            .dataTables_wrapper .dataTables_length select {
                width: 100% !important;
                min-width: 0 !important;
                min-height: 32px !important;
                padding: 5px 24px 5px 8px !important;
                font-size: 10px !important;
                border-radius: 8px !important;
            }
            .dataTables_wrapper .dataTables_filter input {
                width: 100% !important;
                min-height: 32px !important;
                padding: 5px 8px !important;
                margin-bottom: 0 !important;
                font-size: 10px !important;
                border-radius: 8px !important;
            }
            .dataTables_wrapper .dt-control-label {
                font-size: 10px !important;
                line-height: 1.2 !important;
                letter-spacing: 0.01em !important;
            }
            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate {
                padding-top: 10px !important;
                text-align: center !important;
                float: none !important;
            }
            .dataTables_wrapper .dataTables_paginate .paginate_button {
                padding: 0.45rem 0.7rem !important;
                font-size: 10px !important;
            }
        }

        /* Compact corporate inventory table */
        .inventory-page-header {
            margin-bottom: 14px !important;
        }
        .inventory-page-header .page-title-standard {
            font-size: 22px !important;
            line-height: 1.2 !important;
        }
        .inventory-page-header .page-subtitle-standard {
            font-size: 12px !important;
        }
        .filter-bar {
            margin-bottom: 10px !important;
            padding: 10px 12px !important;
            border-radius: 6px !important;
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: none !important;
        }
        .dark .filter-bar {
            background: #111827 !important;
            border-color: #263244 !important;
        }
        .filter-label {
            font-size: 8px !important;
            letter-spacing: 0.08em !important;
            margin-bottom: 4px !important;
        }
        .search-input,
        .filter-select,
        .reset-filter-btn {
            min-height: 28px !important;
            height: 28px !important;
            border-radius: 4px !important;
            font-size: 10px !important;
            box-shadow: none !important;
        }
        .clear-search-btn {
            width: 22px !important;
            height: 22px !important;
            border-radius: 4px !important;
        }
        #mainTableContainer.inventory-table-shell {
            padding: 0 !important;
            border-radius: 6px !important;
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: none !important;
        }
        .dark #mainTableContainer.inventory-table-shell {
            background: #111827 !important;
            border-color: #263244 !important;
        }
        #walkiesTable {
            border: 0 !important;
            margin: 0 !important;
        }
        #walkiesTable thead th,
        .dataTables_wrapper table.dataTable thead th {
            height: 30px !important;
            padding: 6px 8px !important;
            background: #f8fafc !important;
            color: #475569 !important;
            border: 1px solid #e2e8f0 !important;
            font-size: 9px !important;
            letter-spacing: 0.06em !important;
            white-space: nowrap !important;
        }
        #walkiesTable tbody td,
        .dataTables_wrapper table.dataTable tbody td {
            height: 32px !important;
            padding: 5px 8px !important;
            border: 1px solid #eef2f7 !important;
            color: #334155 !important;
            font-size: 11px !important;
            line-height: 1.2 !important;
            vertical-align: middle !important;
        }
        #walkiesTable tbody tr:nth-child(odd),
        #walkiesTable tbody tr:nth-child(even) {
            background: #ffffff !important;
        }
        #walkiesTable tbody tr:hover {
            background: #f8fafc !important;
        }
        .dark #walkiesTable thead th,
        .dark .dataTables_wrapper table.dataTable thead th {
            background: #1f2937 !important;
            border-color: #263244 !important;
            color: #cbd5e1 !important;
        }
        .dark #walkiesTable tbody td,
        .dark .dataTables_wrapper table.dataTable tbody td {
            background: #111827 !important;
            border-color: #263244 !important;
            color: #d1d5db !important;
        }
        .dark #walkiesTable tbody tr:hover td {
            background: #172033 !important;
        }
        .inventory-id-chip,
        .inventory-type-badge,
        .inventory-status-badge,
        .inventory-meta-pill {
            min-width: 0 !important;
            padding: 3px 6px !important;
            border-radius: 4px !important;
            font-size: 9px !important;
            letter-spacing: 0.04em !important;
            font-weight: 800 !important;
        }
        .inventory-id-chip {
            color: #0f172a !important;
            background: #f1f5f9 !important;
            border-color: #e2e8f0 !important;
        }
        .inventory-item-title {
            font-size: 11px !important;
            line-height: 1.2 !important;
            color: #1e293b !important;
            font-weight: 800 !important;
        }
        .inventory-item-meta {
            margin-top: 3px !important;
            gap: 4px !important;
        }
        .inventory-date-primary {
            font-size: 11px !important;
            color: #334155 !important;
            font-weight: 700 !important;
        }
        .inventory-date-secondary {
            font-size: 9px !important;
            color: #64748b !important;
            letter-spacing: 0.03em !important;
        }
        .dark .inventory-id-chip,
        .dark .inventory-type-badge,
        .dark .inventory-meta-pill {
            color: #e5e7eb !important;
            background: #1f2937 !important;
            border-color: #374151 !important;
        }
        .dark .inventory-item-title,
        .dark .inventory-date-primary {
            color: #f3f4f6 !important;
        }
        .inventory-action-stack {
            gap: 4px !important;
            flex-wrap: nowrap !important;
        }
        .inventory-table-shell .wt-btn {
            min-height: 24px !important;
            padding: 4px 8px !important;
            border-radius: 4px !important;
            font-size: 9px !important;
            letter-spacing: 0.04em !important;
        }
        .inventory-table-footer {
            padding: 10px 16px !important;
            border-top: 1px solid #263244 !important;
            background: #111827 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
        }
        .inventory-table-info {
            font-size: 9px !important;
            color: #94a3b8 !important;
            font-weight: 700 !important;
            letter-spacing: 0.05em !important;
            text-transform: uppercase !important;
        }
        .inventory-table-pagination {
            display: flex !important;
            gap: 5px !important;
            align-items: center !important;
        }
        .inventory-page-link {
            min-width: 32px !important;
            height: 28px !important;
            padding: 0 10px !important;
            border-radius: 6px !important;
            border: 1px solid #334155 !important;
            color: #94a3b8 !important;
            font-size: 10px !important;
            background: #0f172a !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.2s ease !important;
            font-weight: 700 !important;
            cursor: pointer !important;
            text-decoration: none !important;
            white-space: nowrap !important;
        }
        .inventory-page-link:hover:not(:disabled) {
            background: #1e293b !important;
            color: #f1f5f9 !important;
            border-color: #475569 !important;
        }
        .inventory-page-link:disabled {
            opacity: 0.3 !important;
            cursor: not-allowed !important;
        }
        #walkiesTable_wrapper,
        #walkiesTable_wrapper > .dataTables_length,
        #walkiesTable_wrapper > .dataTables_filter,
        #walkiesTable_wrapper > .dataTables_info,
        #walkiesTable_wrapper > .dataTables_paginate,
        #walkiesTable_wrapper .dataTables_scrollHead,
        #walkiesTable_wrapper .dataTables_scrollFoot {
            display: none !important;
        }
        #walkiesTable thead th:not(.inventory-action-col)::before,
        #walkiesTable thead th:not(.inventory-action-col)::after {
            display: none !important;
            content: none !important;
        }

        /* Latest Inventory UI only */
        .inventory-page-header {
            margin: 0 0 14px !important;
            padding: 12px 16px !important;
            align-items: center !important;
        }
        .inventory-page-header .page-title-standard {
            font-size: 18px !important;
            line-height: 1.05 !important;
            margin: 0 !important;
        }
        .inventory-page-header .page-subtitle-standard {
            font-size: 9px !important;
            display: block !important;
            margin-top: 6px !important;
            line-height: 1.25 !important;
            letter-spacing: 0.08em !important;
        }
        body .content-surface .filter-bar {
            display: block !important;
            margin: 0 0 10px !important;
            padding: 10px 12px !important;
            border-radius: 6px !important;
            background: #111827 !important;
            border: 1px solid #263244 !important;
            box-shadow: none !important;
        }
        body .content-surface .filter-bar > .flex {
            display: grid !important;
            grid-template-columns: minmax(260px, 1fr) 170px 170px 190px auto !important;
            gap: 10px !important;
            align-items: end !important;
        }
        body .content-surface .filter-label {
            color: #94a3b8 !important;
            font-size: 9px !important;
            line-height: 1 !important;
            letter-spacing: 0.08em !important;
            margin-bottom: 5px !important;
        }
        body .content-surface .search-input,
        body .content-surface .filter-select,
        body .content-surface .reset-filter-btn {
            height: 32px !important;
            min-height: 32px !important;
            border-radius: 6px !important;
            background: #0f172a !important;
            border: 1px solid #334155 !important;
            color: #e5e7eb !important;
            font-size: 11px !important;
            font-weight: 400 !important;
            box-shadow: none !important;
        }
        body .content-surface #mainTableContainer.inventory-table-shell {
            margin: 0 !important;
            padding: 0 !important;
            border-radius: 6px !important;
            overflow: hidden !important;
            background: #111827 !important;
            border: 1px solid #263244 !important;
            box-shadow: none !important;
        }
        body .content-surface #inventoryTableScroll {
            overflow-x: auto !important;
        }
        body .content-surface #walkiesTable {
            width: 100% !important;
            min-width: 980px !important;
            margin: 0 !important;
            border-collapse: collapse !important;
            border: 0 !important;
        }
        body .content-surface #walkiesTable thead th {
            height: 34px !important;
            padding: 8px 10px !important;
            background: #1f2937 !important;
            border: 1px solid #2f3b4f !important;
            color: #cbd5e1 !important;
            font-size: 10px !important;
            line-height: 1.1 !important;
            letter-spacing: 0.05em !important;
            white-space: nowrap !important;
        }
        body .content-surface #walkiesTable tbody td {
            height: 22px !important;
            min-height: 22px !important;
            max-height: none !important;
            padding: 2px 8px !important;
            background: #111827 !important;
            border: 1px solid #263244 !important;
            color: #dbe4f0 !important;
            font-size: 11px !important;
            line-height: 1.25 !important;
            font-weight: 400 !important;
            vertical-align: middle !important;
        }
        body .content-surface #walkiesTable tbody tr:hover td {
            background: #172033 !important;
        }
        body .content-surface .inventory-id-chip,
        body .content-surface .inventory-type-badge,
        body .content-surface .inventory-status-badge,
        body .content-surface .inventory-meta-pill {
            min-width: 0 !important;
            padding: 3px 7px !important;
            border-radius: 4px !important;
            font-size: 9px !important;
            line-height: 1.2 !important;
            letter-spacing: 0.03em !important;
            font-weight: 500 !important;
            background: #243044 !important;
            border: 1px solid #3b4a60 !important;
            color: #f1f5f9 !important;
        }
        body .content-surface .inventory-status-badge[data-status="IN USE"] {
            background: #16345f !important;
            border-color: #2c5f9e !important;
            color: #dbeafe !important;
        }
        body .content-surface .inventory-status-badge[data-status="UNUSED"] {
            background: #164633 !important;
            border-color: #287a55 !important;
            color: #dcfce7 !important;
        }
        body .content-surface .inventory-item-title {
            color: #f3f4f6 !important;
            font-size: 11px !important;
            line-height: 1.2 !important;
            font-weight: 500 !important;
        }
        body .content-surface .inventory-action-stack {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            gap: 5px !important;
            flex-wrap: nowrap !important;
        }
        body .content-surface .inventory-table-shell .wt-btn {
            min-height: 24px !important;
            height: 24px !important;
            padding: 3px 8px !important;
            border-radius: 4px !important;
            font-size: 9px !important;
            letter-spacing: 0.03em !important;
            font-weight: 500 !important;
            white-space: nowrap !important;
        }
        @media (max-width: 1100px) {
            body .content-surface .filter-bar > .flex {
                grid-template-columns: 1fr 1fr !important;
            }
        }

        /* Inventory light mode fix: keep page controls light when dark theme is off. */
        html:not(.dark) body .content-surface .filter-bar,
        html:not(.dark) body .content-surface .clean-admin-filter {
            background: #f8fafc !important;
            border: 1px solid #d6e0ec !important;
            box-shadow: none !important;
        }

        html:not(.dark) body .content-surface .filter-label,
        html:not(.dark) body .content-surface .clean-admin-label {
            color: #64748b !important;
        }

        html:not(.dark) body .content-surface .inventory-summary-card {
            background: #ffffff !important;
            border: 1px solid #d6e0ec !important;
            box-shadow: none !important;
        }

        html:not(.dark) body .content-surface .inventory-summary-label {
            color: #64748b !important;
        }

        html:not(.dark) body .content-surface .inventory-summary-value {
            color: #1f2937 !important;
        }

        html:not(.dark) body .content-surface .inventory-summary-value.is-use {
            color: #2563eb !important;
        }

        html:not(.dark) body .content-surface .inventory-summary-value.is-unused {
            color: #16a34a !important;
        }

        html:not(.dark) body .content-surface .inventory-summary-value.is-repair {
            color: #ea580c !important;
        }

        html:not(.dark) body .content-surface .search-input,
        html:not(.dark) body .content-surface .filter-select,
        html:not(.dark) body .content-surface .reset-filter-btn,
        html:not(.dark) body .content-surface .clean-admin-input,
        html:not(.dark) body .content-surface .clean-admin-select,
        html:not(.dark) body .content-surface .clean-admin-reset {
            background: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            color: #1f2937 !important;
            box-shadow: none !important;
        }

        html:not(.dark) body .content-surface .search-input::placeholder,
        html:not(.dark) body .content-surface .clean-admin-input::placeholder {
            color: #94a3b8 !important;
        }

        html:not(.dark) body .content-surface #mainTableContainer.inventory-table-shell,
        html:not(.dark) body .content-surface .clean-admin-table-shell {
            background: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            box-shadow: none !important;
        }

        html:not(.dark) body .content-surface #walkiesTable thead th,
        html:not(.dark) body .content-surface .clean-admin-table thead th {
            background: #e8eef5 !important;
            border: 1px solid #d6e0ec !important;
            color: #475569 !important;
        }

        html:not(.dark) body .content-surface #walkiesTable tbody td,
        html:not(.dark) body .content-surface .clean-admin-table tbody td {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            color: #334155 !important;
        }

        html:not(.dark) body .content-surface #walkiesTable tbody tr:hover td,
        html:not(.dark) body .content-surface .clean-admin-table tbody tr:hover td {
            background: #f8fafc !important;
        }

        html:not(.dark) body .content-surface .inventory-id-chip,
        html:not(.dark) body .content-surface .inventory-type-badge,
        html:not(.dark) body .content-surface .inventory-status-badge,
        html:not(.dark) body .content-surface .inventory-meta-pill,
        html:not(.dark) body .content-surface .clean-admin-pill {
            background: #eef4fb !important;
            border: 1px solid #d6e0ec !important;
            color: #334155 !important;
        }

        html:not(.dark) body .content-surface .inventory-item-title {
            color: #1f2937 !important;
        }

        html:not(.dark) body .content-surface .inventory-page-link {
            background: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            color: #475569 !important;
        }

        html:not(.dark) body .content-surface .inventory-page-link:hover:not(:disabled) {
            background: #eef4fb !important;
            color: #1f2937 !important;
            border-color: #b9c7d8 !important;
        }

        #importModal.modal-overlay {
            position: fixed !important;
            inset: 0 !important;
            z-index: 2147483000 !important;
            display: none !important;
            align-items: center !important;
            justify-content: center !important;
            width: 100vw !important;
            height: 100vh !important;
            min-height: 100vh !important;
            padding: 16px !important;
            overflow: auto !important;
            background: rgba(15, 23, 42, 0.38) !important;
            backdrop-filter: blur(6px) !important;
        }

        #importModal.modal-overlay.active {
            display: flex !important;
        }

        #importModal .modal-box {
            position: relative !important;
            top: auto !important;
            right: auto !important;
            bottom: auto !important;
            left: auto !important;
            display: block !important;
            flex: none !important;
            max-width: 500px !important;
            width: min(500px, calc(100vw - 32px)) !important;
            height: auto !important;
            min-height: 0 !important;
            max-height: none !important;
            margin: 0 !important;
            padding: 0 !important;
            overflow: hidden !important;
            border-radius: 14px !important;
            background: #ffffff !important;
            border: 1px solid #d9e2ee !important;
            color: #1f2937 !important;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.25) !important;
            transform: none !important;
        }

        #importModal .modal-box form {
            display: block !important;
            margin: 0 !important;
            padding: 0 !important;
            overflow: visible !important;
        }

        #importModal .modal-header {
            display: flex !important;
            align-items: flex-start !important;
            justify-content: space-between !important;
            gap: 16px !important;
            min-height: 66px !important;
            padding: 16px 20px !important;
            background: #ffffff !important;
            border-bottom: 1px solid #d9e2ee !important;
            color: #1f2937 !important;
        }

        #importModal .modal-title {
            margin: 0 !important;
            color: #172033 !important;
            font-size: 17px !important;
            line-height: 1.1 !important;
            font-weight: 800 !important;
            letter-spacing: 0 !important;
        }

        #importModal .modal-subtitle {
            margin-top: 6px !important;
            color: #475569 !important;
            font-size: 12px !important;
            line-height: 1.25 !important;
            font-weight: 600 !important;
            letter-spacing: 0 !important;
            text-transform: none !important;
        }

        #importModal .modal-close-btn {
            flex: 0 0 auto !important;
            width: 44px !important;
            height: 44px !important;
            min-width: 44px !important;
            min-height: 44px !important;
            padding: 0 !important;
            border-radius: 12px !important;
            background: #f8fafc !important;
            border: 1px solid #d9e2ee !important;
            color: #6b7a90 !important;
            box-shadow: none !important;
        }

        #importModal .modal-close-btn:hover {
            background: #fef2f2 !important;
            border-color: #fecaca !important;
            color: #b91c1c !important;
        }

        #importModal .modal-body {
            padding: 20px !important;
            background: #ffffff !important;
        }

        #importModal .modal-body > div {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-height: 140px !important;
            padding: 24px 20px !important;
            border-radius: 12px !important;
            background: #f8fafc !important;
            border: 2px dashed #c3cedd !important;
            color: #1f2937 !important;
        }

        #importModal label[for="import_file"] {
            display: block !important;
            width: 100% !important;
            color: #1f2937 !important;
        }

        #importModal label[for="import_file"] > div {
            width: 46px !important;
            height: 46px !important;
            background: #ffffff !important;
            border: 1px solid #d9e2ee !important;
            color: #075985 !important;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08) !important;
        }

        #importModal label[for="import_file"] svg {
            fill: currentColor !important;
        }

        #importModal #fileNameDisplay {
            color: #172033 !important;
            font-size: 13px !important;
            line-height: 1.25 !important;
            font-weight: 800 !important;
            letter-spacing: 0.04em !important;
        }

        #importModal #fileNameDisplay + p {
            color: #6b7a90 !important;
            font-size: 10px !important;
            line-height: 1.35 !important;
            margin-top: 8px !important;
            letter-spacing: 0.08em !important;
        }

        #importModal .modal-footer {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-end !important;
            gap: 12px !important;
            padding: 14px 20px !important;
            background: #ffffff !important;
            border-top: 1px solid #d9e2ee !important;
        }

        #importModal .btn-cancel,
        #importModal .btn-submit {
            min-height: 36px !important;
            padding: 0 16px !important;
            border-radius: 8px !important;
            font-size: 12px !important;
            font-weight: 800 !important;
            letter-spacing: 0.03em !important;
            text-transform: uppercase !important;
        }

        #importModal .btn-cancel {
            background: #ffffff !important;
            border: 1px solid #d9e2ee !important;
            color: #334155 !important;
        }

        #importModal .btn-submit {
            background: #64748b !important;
            border: 1px solid #64748b !important;
            color: #ffffff !important;
        }

        .dark #importModal.modal-overlay {
            background: rgba(15, 23, 42, 0.38) !important;
        }

        .dark #importModal .modal-box {
            background: #ffffff !important;
            border-color: #d9e2ee !important;
            color: #1f2937 !important;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.48) !important;
        }

        .dark #importModal .modal-header,
        .dark #importModal .modal-body,
        .dark #importModal .modal-footer {
            background: #ffffff !important;
            border-color: #d9e2ee !important;
            color: #1f2937 !important;
        }

        .dark #importModal .modal-title,
        .dark #importModal #fileNameDisplay {
            color: #172033 !important;
        }

        .dark #importModal .modal-subtitle,
        .dark #importModal #fileNameDisplay + p {
            color: #475569 !important;
        }

        .dark #importModal .modal-body > div {
            background: #f8fafc !important;
            border-color: #c3cedd !important;
        }

        .dark #importModal label[for="import_file"] > div,
        .dark #importModal .modal-close-btn {
            background: #f8fafc !important;
            border-color: #d9e2ee !important;
        }

        @media (max-width: 640px) {
            #importModal.modal-overlay {
                align-items: center !important;
                padding: 14px !important;
            }

            #importModal .modal-box {
                width: calc(100vw - 28px) !important;
                max-height: none !important;
                border-radius: 12px !important;
            }

            #importModal .modal-header,
            #importModal .modal-body,
            #importModal .modal-footer {
                padding-left: 18px !important;
                padding-right: 18px !important;
            }

            #importModal .modal-title {
                font-size: 19px !important;
            }

            #importModal .modal-subtitle,
            #importModal #fileNameDisplay {
                font-size: 14px !important;
            }

            #importModal #fileNameDisplay + p {
                font-size: 11px !important;
            }
        }

        .clean-admin-filter {
            min-height: 134px !important;
            margin: 0 !important;
            padding: 16px 18px !important;
            border-radius: 8px !important;
            background: #111827 !important;
            border: 1px solid #263244 !important;
            box-shadow: none !important;
        }
        .clean-admin-filter-grid {
            grid-template-columns: minmax(260px, 1fr) 405px 74px !important;
            gap: 24px !important;
        }
        .clean-admin-label {
            margin-bottom: 14px !important;
            color: #94a3b8 !important;
            font-size: 12px !important;
            line-height: 1 !important;
            letter-spacing: 0.14em !important;
            font-weight: 800 !important;
            text-transform: uppercase !important;
        }
        .clean-admin-input,
        .clean-admin-select,
        .clean-admin-reset {
            height: 48px !important;
            border-radius: 8px !important;
            background: #0f172a !important;
            border: 1px solid #334155 !important;
            color: #e5e7eb !important;
            font-size: 16px !important;
            font-weight: 650 !important;
            box-shadow: none !important;
        }
        .clean-admin-input {
            padding: 0 18px !important;
        }
        .clean-admin-select {
            padding: 0 38px 0 14px !important;
        }
        .clean-admin-reset {
            padding: 0 !important;
            font-size: 14px !important;
            font-weight: 800 !important;
        }
        #mainTableContainer.inventory-table-shell,
        #mainTableContainer .clean-admin-table-shell {
            margin: 0 !important;
            padding: 0 !important;
            border-radius: 8px !important;
            overflow: hidden !important;
            background: #111827 !important;
            border: 1px solid #2b3950 !important;
            box-shadow: none !important;
        }
        #walkiesTable {
            border-collapse: collapse !important;
        }
        #walkiesTable thead th {
            height: 52px !important;
            padding: 0 16px !important;
            background: #111827 !important;
            border: 1px solid #2b3950 !important;
            color: #dbeafe !important;
            font-size: 15px !important;
            font-weight: 900 !important;
            line-height: 1.1 !important;
            letter-spacing: 0.04em !important;
            text-transform: uppercase !important;
            white-space: nowrap !important;
        }
        #walkiesTable tbody td {
            height: 30px !important;
            padding: 4px 10px !important;
            background: #111827 !important;
            border: 1px solid #263244 !important;
            color: #dbe4f0 !important;
            font-size: 11px !important;
            font-weight: 400 !important;
            line-height: 1.25 !important;
            vertical-align: middle !important;
        }
        #walkiesTable tbody tr:hover td {
            background: #172033 !important;
        }
        .inventory-table-footer {
            min-height: 64px !important;
            padding: 10px 18px !important;
            gap: 16px !important;
            background: #1f2937 !important;
            border-top: 1px solid #263244 !important;
        }
        .inventory-table-info {
            color: #dbeafe !important;
            font-size: 16px !important;
            font-weight: 900 !important;
            letter-spacing: 0 !important;
            text-transform: none !important;
            white-space: nowrap !important;
        }
        .inventory-table-pagination {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-end !important;
            gap: 10px !important;
        }
        .inventory-page-link {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 38px !important;
            height: 34px !important;
            padding: 0 10px !important;
            border-radius: 7px !important;
            border: 1px solid #2f4d74 !important;
            background: #0f172a !important;
            color: #bfdbfe !important;
            font-size: 12px !important;
            font-weight: 900 !important;
            line-height: 1 !important;
            transition: border-color 0.15s ease, background 0.15s ease, color 0.15s ease, opacity 0.15s ease !important;
        }
        .inventory-page-link.is-nav {
            min-width: 92px !important;
            color: #cbd5e1 !important;
            font-size: 13px !important;
        }
        .inventory-page-link:hover:not(:disabled) {
            border-color: #3b82f6 !important;
            background: #172033 !important;
            color: #ffffff !important;
        }
        .inventory-page-link.is-active {
            border-color: #3b82f6 !important;
            background: #0f3a72 !important;
            color: #ffffff !important;
        }
        .inventory-page-link:disabled {
            cursor: not-allowed !important;
            opacity: 0.45 !important;
        }
        .inventory-page-ellipsis,
        .inventory-empty-text {
            color: #9fb0c8 !important;
            font-size: 18px !important;
            font-weight: 900 !important;
            letter-spacing: 0.22em !important;
            text-transform: uppercase !important;
        }
        @media (max-width: 900px) {
            .clean-admin-filter-grid {
                grid-template-columns: 1fr !important;
            }
            .inventory-table-footer {
                align-items: stretch !important;
                flex-direction: column !important;
            }
            .inventory-table-pagination {
                justify-content: flex-start !important;
                overflow-x: auto !important;
            }
        }
        .inventory-page-shell {
            display: grid !important;
            gap: 42px !important;
            padding: 0 14px !important;
        }
        .inventory-page-header {
            position: relative !important;
            margin: 0 !important;
            min-height: 96px !important;
            padding: 18px 24px !important;
            border-left: 7px solid #f2c48d !important;
            border-radius: 13px !important;
            background: linear-gradient(90deg, rgba(31, 41, 55, 0.95), rgba(30, 41, 59, 0.95)) !important;
            box-shadow: none !important;
            overflow: hidden !important;
        }
        .inventory-page-header .page-title-standard {
            color: #f8fafc !important;
            font-size: 28px !important;
            font-weight: 900 !important;
            line-height: 1 !important;
            margin: 0 0 8px !important;
        }
        .inventory-page-header .page-subtitle-standard {
            color: #aab5c7 !important;
            font-size: 13px !important;
            font-weight: 900 !important;
            letter-spacing: 0.28em !important;
            line-height: 1.2 !important;
            margin: 0 !important;
            text-transform: uppercase !important;
        }
        .inventory-record-pill {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-height: 58px !important;
            min-width: 164px !important;
            padding: 0 22px !important;
            border-radius: 999px !important;
            border: 1px solid #25364f !important;
            background: #0f172a !important;
            color: #e5e7eb !important;
            font-size: 19px !important;
            font-weight: 900 !important;
            text-transform: uppercase !important;
        }
        .inventory-summary-grid {
            display: none !important;
        }
        .inventory-page-shell .clean-admin-filter,
        .inventory-page-shell #mainTableContainer {
            width: 100% !important;
        }
        .inventory-page-shell .clean-admin-filter {
            margin: 0 !important;
        }
        .inventory-page-shell #mainTableContainer {
            margin-top: -20px !important;
        }
        body .content-surface,
        body .admin-content,
        body main {
            background: #0f172a !important;
        }
        body .content-surface .breadcrumb,
        body .content-surface .page-breadcrumb,
        body .content-surface > .page-header-block:not(.inventory-page-header) {
            display: none !important;
        }
        body .content-surface .inventory-page-shell {
            color-scheme: dark !important;
        }
        html:not(.dark) body .content-surface .inventory-page-header {
            background: linear-gradient(90deg, rgba(31, 41, 55, 0.98), rgba(30, 41, 59, 0.98)) !important;
            border-left-color: #f2c48d !important;
        }
        html:not(.dark) body .content-surface .inventory-page-header .page-title-standard {
            color: #f8fafc !important;
        }
        html:not(.dark) body .content-surface .inventory-page-header .page-subtitle-standard {
            color: #aab5c7 !important;
        }
        html:not(.dark) body .content-surface .inventory-record-pill,
        html:not(.dark) body .content-surface .inventory-page-header .wt-btn {
            background: #0f172a !important;
            border-color: #2f4d74 !important;
            color: #e5e7eb !important;
        }
        html:not(.dark) body .content-surface .clean-admin-filter {
            background: #111827 !important;
            border-color: #263244 !important;
        }
        html:not(.dark) body .content-surface .clean-admin-label {
            color: #94a3b8 !important;
        }
        html:not(.dark) body .content-surface .clean-admin-input,
        html:not(.dark) body .content-surface .clean-admin-select,
        html:not(.dark) body .content-surface .clean-admin-reset {
            background: #0f172a !important;
            border-color: #334155 !important;
            color: #e5e7eb !important;
        }
        html:not(.dark) body .content-surface #mainTableContainer.inventory-table-shell,
        html:not(.dark) body .content-surface .clean-admin-table-shell {
            background: #111827 !important;
            border-color: #2b3950 !important;
        }
        html:not(.dark) body .content-surface #walkiesTable thead th,
        html:not(.dark) body .content-surface .clean-admin-table thead th {
            background: #111827 !important;
            border-color: #2b3950 !important;
            color: #dbeafe !important;
        }
        html:not(.dark) body .content-surface #walkiesTable tbody td,
        html:not(.dark) body .content-surface .clean-admin-table tbody td {
            background: #111827 !important;
            border-color: #263244 !important;
            color: #dbe4f0 !important;
        }
        html:not(.dark) body .content-surface .inventory-table-footer {
            background: #1f2937 !important;
            border-top-color: #263244 !important;
        }
        html:not(.dark) body .content-surface .inventory-table-info {
            color: #dbeafe !important;
        }
        html:not(.dark) body .content-surface .inventory-empty-text {
            color: #9fb0c8 !important;
        }

        /* Final unified inventory layout: fixes theme switching and excessive spacing. */
        body .content-surface:has(.inventory-page-shell) {
            padding: 10px !important;
            border-radius: 12px !important;
        }
        .inventory-page-shell {
            gap: 10px !important;
            padding: 0 !important;
        }
        .inventory-page-shell #mainTableContainer {
            margin-top: 0 !important;
        }
        .inventory-page-shell .clean-admin-filter {
            min-height: auto !important;
            padding: 10px 12px !important;
            border-radius: 8px !important;
        }
        .inventory-page-header {
            min-height: 42px !important;
            padding: 8px 14px !important;
            border-radius: 7px !important;
            border-left-width: 4px !important;
            gap: 8px !important;
        }
        .inventory-page-header .page-title-standard {
            font-size: 19px !important;
            margin-bottom: 3px !important;
        }
        .inventory-page-header .page-subtitle-standard {
            font-size: 8px !important;
            letter-spacing: 0.24em !important;
            line-height: 1.1 !important;
        }
        .clean-admin-filter-grid {
            gap: 10px !important;
            align-items: end !important;
        }
        .clean-admin-label {
            margin-bottom: 5px !important;
            font-size: 10px !important;
        }
        .clean-admin-input,
        .clean-admin-select,
        .clean-admin-reset {
            height: 34px !important;
            min-height: 34px !important;
            border-radius: 7px !important;
            font-size: 13px !important;
        }
        .inventory-summary-grid,
        .inventory-record-pill {
            display: none !important;
        }

        html:not(.dark) body,
        html:not(.dark) body main,
        html:not(.dark) body .admin-content,
        html:not(.dark) body .content-surface {
            background: #f1f5f9 !important;
            color: #0f172a !important;
        }
        html:not(.dark) body .content-surface .inventory-page-header {
            background: #ffffff !important;
            border: 1px solid #d8e1ed !important;
            border-left: 7px solid #0284c7 !important;
            box-shadow: none !important;
        }
        html:not(.dark) body .content-surface .inventory-page-header .page-title-standard {
            color: #0f172a !important;
        }
        html:not(.dark) body .content-surface .inventory-page-header .page-subtitle-standard {
            color: #64748b !important;
        }
        html:not(.dark) body .content-surface .inventory-page-header .wt-btn {
            background: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }
        html:not(.dark) body .content-surface .clean-admin-filter {
            background: #ffffff !important;
            border-color: #d8e1ed !important;
        }
        html:not(.dark) body .content-surface .clean-admin-label {
            color: #64748b !important;
        }
        html:not(.dark) body .content-surface .clean-admin-input,
        html:not(.dark) body .content-surface .clean-admin-select,
        html:not(.dark) body .content-surface .clean-admin-reset {
            background: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }
        html:not(.dark) body .content-surface .clean-admin-input::placeholder {
            color: #94a3b8 !important;
        }
        html:not(.dark) body .content-surface #mainTableContainer.inventory-table-shell {
            background: #ffffff !important;
            border-color: #cbd5e1 !important;
        }
        html:not(.dark) body .content-surface #walkiesTable thead th {
            background: #f8fafc !important;
            border-color: #d8e1ed !important;
            color: #526781 !important;
        }
        html:not(.dark) body .content-surface #walkiesTable tbody td {
            background: #ffffff !important;
            border-color: #e2e8f0 !important;
            color: #1f2937 !important;
        }
        html:not(.dark) body .content-surface #walkiesTable tbody tr:hover td {
            background: #f8fafc !important;
        }
        html:not(.dark) body .content-surface .inventory-table-footer {
            background: #ffffff !important;
            border-top-color: #d8e1ed !important;
        }
        html:not(.dark) body .content-surface .inventory-table-info {
            color: #334155 !important;
        }
        html:not(.dark) body .content-surface .inventory-empty-text {
            color: #94a3b8 !important;
        }
        html:not(.dark) body .content-surface .inventory-page-link {
            background: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #334155 !important;
        }
        html:not(.dark) body .content-surface .inventory-page-link.is-active {
            background: #dbeafe !important;
            border-color: #60a5fa !important;
            color: #1e3a8a !important;
        }
        body .content-surface .inventory-page-header .wt-btn,
        body .content-surface .duplicate-hero .wt-btn,
        body .content-surface .maintenance-page-shell .wt-btn,
        body .content-surface .unused-page-shell .wt-btn {
            min-width: 108px !important;
            min-height: 30px !important;
            padding: 0 11px !important;
            border-radius: 7px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 5px !important;
            font-size: 12px !important;
            font-weight: 900 !important;
            line-height: 1 !important;
            white-space: nowrap !important;
        }
        body .content-surface .inventory-page-header .wt-btn svg {
            width: 11px !important;
            height: 11px !important;
            margin-right: 0 !important;
        }
        body .content-surface #walkiesTable thead th {
            height: 34px !important;
            padding: 7px 10px !important;
            font-size: 11px !important;
        }
        body .content-surface #walkiesTable tbody td {
            height: 30px !important;
            min-height: 30px !important;
            padding: 4px 10px !important;
            font-size: 11px !important;
        }
        body .content-surface .inventory-table-footer {
            min-height: 44px !important;
            padding: 7px 12px !important;
            gap: 10px !important;
        }
        body .content-surface .inventory-table-info {
            font-size: 13px !important;
        }
        body .content-surface .inventory-table-pagination {
            gap: 8px !important;
        }
        body .content-surface .inventory-page-link {
            height: 30px !important;
            min-width: 34px !important;
            padding: 0 9px !important;
            border-radius: 7px !important;
            font-size: 11px !important;
        }
        body .content-surface .inventory-page-link.is-nav {
            min-width: 76px !important;
            font-size: 12px !important;
        }

        .dark body,
        .dark body main,
        .dark body .admin-content,
        .dark body .content-surface {
            background: #0f172a !important;
            color: #e5e7eb !important;
        }
        .dark body .content-surface .inventory-page-header {
            background: linear-gradient(90deg, rgba(31, 41, 55, 0.98), rgba(30, 41, 59, 0.98)) !important;
            border-left-color: #f2c48d !important;
        }
        .dark body .content-surface .clean-admin-filter,
        .dark body .content-surface #mainTableContainer.inventory-table-shell {
            background: #111827 !important;
            border-color: #263244 !important;
        }
        .dark body .content-surface #walkiesTable thead th {
            background: #111827 !important;
            border-color: #2b3950 !important;
            color: #dbeafe !important;
        }
        .dark body .content-surface #walkiesTable tbody td {
            background: #111827 !important;
            border-color: #263244 !important;
            color: #dbe4f0 !important;
        }
        .dark body .content-surface .inventory-table-footer {
            background: #1f2937 !important;
            border-top-color: #263244 !important;
        }

    </style>

    @include('wt.admin.partials.inventory-management-ui')

    <style>
        /* Final Inventory List redesign: cleaner mobile-first workspace. */
        body .content-surface:has(.inventory-page-shell) {
            background: #0b1220 !important;
            padding: 10px !important;
        }

        body .content-surface .inventory-page-shell {
            gap: 12px !important;
        }

        body .content-surface .inventory-page-header {
            min-height: 0 !important;
            padding: 0 2px 10px !important;
            border: 0 !important;
            border-radius: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
        }

        body .content-surface .inventory-page-header .page-title-standard {
            color: #f8fafc !important;
            font-size: 19px !important;
            line-height: 1.1 !important;
            letter-spacing: 0 !important;
        }

        body .content-surface .inventory-page-header .page-subtitle-standard {
            max-width: 520px !important;
            margin-top: 5px !important;
            color: #93a4ba !important;
            font-size: 9px !important;
            letter-spacing: .16em !important;
            line-height: 1.45 !important;
        }

        body .content-surface .inventory-header-actions {
            gap: 8px !important;
        }

        body .content-surface .inventory-page-header .wt-btn {
            min-width: 118px !important;
            height: 34px !important;
            border-radius: 10px !important;
            border: 1px solid rgba(148, 163, 184, .24) !important;
            background: #111827 !important;
            color: #f8fafc !important;
            font-size: 12px !important;
            box-shadow: none !important;
        }

        body .content-surface .inventory-page-header .wt-btn:hover {
            border-color: rgba(56, 189, 248, .42) !important;
            background: #162033 !important;
        }

        body .content-surface .inventory-page-header {
            display: grid !important;
            grid-template-columns: 1fr !important;
            gap: 30px !important;
            align-items: start !important;
            min-height: 118px !important;
        }

        body .content-surface .inventory-header-copy {
            min-width: 0 !important;
        }

        body .content-surface .inventory-header-actions {
            display: flex !important;
            width: 100% !important;
            justify-content: flex-end !important;
            align-items: center !important;
            gap: 10px !important;
            padding-top: 0 !important;
        }

        body .content-surface .inventory-summary-grid {
            display: none !important;
        }

        body .content-surface .clean-admin-filter {
            padding: 12px !important;
            border: 1px solid rgba(148, 163, 184, .18) !important;
            border-radius: 14px !important;
            background: #0f172a !important;
        }

        body .content-surface .clean-admin-filter-grid {
            grid-template-columns: minmax(240px, 1fr) 180px auto !important;
            gap: 10px !important;
        }

        body .content-surface .clean-admin-label {
            margin-bottom: 6px !important;
            color: #8ea0b8 !important;
            font-size: 9px !important;
            letter-spacing: .12em !important;
        }

        body .content-surface .clean-admin-input,
        body .content-surface .clean-admin-select,
        body .content-surface .clean-admin-reset {
            height: 38px !important;
            min-height: 38px !important;
            border-radius: 10px !important;
            border: 1px solid rgba(148, 163, 184, .26) !important;
            background: #111827 !important;
            color: #e5edf7 !important;
            font-size: 12px !important;
            font-weight: 750 !important;
        }

        body .content-surface .clean-admin-input::placeholder {
            color: #748397 !important;
        }

        body .content-surface .clean-admin-reset {
            padding: 0 18px !important;
            background: transparent !important;
            color: #dbeafe !important;
        }

        body .content-surface .inventory-bulk-bar {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 8px;
            margin: 8px 0 12px;
            padding: 8px 10px;
            border: 1px solid rgba(148, 163, 184, .18);
            border-radius: 10px;
            background: #0f172a;
        }

        body .content-surface .inventory-bulk-count {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            flex: 0 0 auto;
            min-height: 28px;
            border-radius: 8px;
            border: 1px solid rgba(148, 163, 184, .16);
            background: #111827;
            padding: 0 9px;
            color: #dbeafe;
            font-size: 8px;
            font-weight: 900;
            letter-spacing: .07em;
            text-transform: uppercase;
        }

        body .content-surface .inventory-bulk-controls {
            display: grid;
            grid-template-columns: 124px 140px minmax(150px, 1fr) auto;
            gap: 7px;
            align-items: center;
            flex: 0 1 560px;
            width: min(560px, 100%);
        }

        body .content-surface .inventory-bulk-select,
        body .content-surface .inventory-bulk-input {
            width: 100%;
            height: 28px;
            min-height: 28px;
            border-radius: 7px;
            border: 1px solid rgba(148, 163, 184, .26);
            background: #111827;
            color: #e5edf7;
            padding: 0 9px;
            font-size: 9px;
            font-weight: 800;
            outline: none;
        }

        body .content-surface .inventory-bulk-select:disabled,
        body .content-surface .inventory-bulk-input:disabled {
            opacity: .45;
            cursor: not-allowed;
        }

        body .content-surface .inventory-bulk-input::placeholder {
            color: #748397;
        }

        body .content-surface .inventory-bulk-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 28px;
            min-width: 64px;
            border-radius: 7px;
            border: 1px solid rgba(56, 189, 248, .32);
            background: #0e7490;
            color: #ffffff;
            padding: 0 10px;
            font-size: 8px;
            font-weight: 900;
            letter-spacing: .07em;
            text-transform: uppercase;
        }

        body .content-surface .inventory-bulk-btn:disabled {
            opacity: .45;
            cursor: not-allowed;
            background: #111827;
            color: #93a4ba;
        }

        body .content-surface .inventory-bulk-checkbox {
            width: 13px;
            height: 13px;
            accent-color: #0ea5e9;
            cursor: pointer;
        }

        body .content-surface .inventory-select-col {
            width: 34px !important;
            min-width: 34px !important;
            max-width: 34px !important;
            padding-left: 4px !important;
            padding-right: 4px !important;
        }

        body .content-surface #mainTableContainer.inventory-table-shell {
            border-radius: 14px !important;
            border: 1px solid rgba(148, 163, 184, .18) !important;
            background: #0f172a !important;
            overflow: hidden !important;
        }

        body .content-surface #walkiesTable thead th {
            height: 30px !important;
            padding: 0 8px !important;
            border-color: rgba(148, 163, 184, .14) !important;
            background: #172033 !important;
            color: #d7e7fb !important;
            font-size: 9px !important;
            letter-spacing: .07em !important;
        }

        body .content-surface #walkiesTable tbody td {
            height: 22px !important;
            min-height: 22px !important;
            padding: 2px 8px !important;
            border-color: rgba(148, 163, 184, .12) !important;
            background: #0f172a !important;
            color: #dbe4f0 !important;
            font-size: 10px !important;
            line-height: 1.15 !important;
        }

        body .content-surface #walkiesTable tbody tr:hover td {
            background: #152033 !important;
        }

        body .content-surface #walkiesTable tbody td[colspan],
        body .content-surface .inventory-empty-text {
            height: 84px !important;
            color: #9fb0c5 !important;
            font-size: 11px !important;
            letter-spacing: .04em !important;
            text-transform: none !important;
        }

        body .content-surface .inventory-table-footer {
            min-height: 46px !important;
            padding: 8px 12px !important;
            border-top: 1px solid rgba(148, 163, 184, .14) !important;
            background: #0f172a !important;
        }

        body .content-surface .inventory-table-info {
            color: #93a4ba !important;
            font-size: 11px !important;
            letter-spacing: .04em !important;
        }

        @media (max-width: 767px) {
            body .content-surface:has(.inventory-page-shell) {
                padding: 12px !important;
            }

            body .content-surface .inventory-page-header {
                gap: 12px !important;
            }

            body .content-surface .inventory-page-header .page-title-standard {
                font-size: 20px !important;
            }

            body .content-surface .inventory-page-header .page-subtitle-standard {
                font-size: 8px !important;
                letter-spacing: .14em !important;
            }

            body .content-surface .inventory-header-actions {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                width: 100% !important;
            }

            body .content-surface .inventory-page-header .wt-btn {
                width: 100% !important;
                min-width: 0 !important;
            }

            body .content-surface .clean-admin-filter-grid {
                grid-template-columns: 1fr !important;
            }

            body .content-surface .clean-admin-reset {
                width: 100% !important;
            }

            body .content-surface .inventory-bulk-bar {
                margin: 8px 0 12px !important;
                padding: 8px !important;
                align-items: stretch;
                flex-direction: column;
            }

            body .content-surface .inventory-bulk-count {
                justify-content: center;
                width: 100%;
            }

            body .content-surface .inventory-bulk-controls {
                grid-template-columns: 1fr;
            }

            body .content-surface .inventory-bulk-btn {
                width: 100%;
            }

            body .content-surface #mainTableContainer.inventory-table-shell {
                border-radius: 12px !important;
            }

            body .content-surface #walkiesTable thead th {
                height: 38px !important;
                padding: 0 11px !important;
                font-size: 9px !important;
            }

            body .content-surface #walkiesTable tbody td {
                height: 28px !important;
                padding: 3px 9px !important;
                font-size: 10px !important;
            }
        }

        html:not(.dark) body .content-surface:has(.inventory-page-shell) {
            background: #f4f7fb !important;
        }

        html:not(.dark) body .content-surface .inventory-page-header .page-title-standard {
            color: #172033 !important;
        }

        html:not(.dark) body .content-surface .inventory-page-header .page-subtitle-standard,
        html:not(.dark) body .content-surface .clean-admin-label,
        html:not(.dark) body .content-surface .inventory-table-info {
            color: #64748b !important;
        }

        html:not(.dark) body .content-surface .inventory-page-header .wt-btn,
        html:not(.dark) body .content-surface .inventory-bulk-bar,
        html:not(.dark) body .content-surface .clean-admin-filter,
        html:not(.dark) body .content-surface #mainTableContainer.inventory-table-shell {
            border-color: #d8e1ed !important;
            background: #ffffff !important;
            color: #172033 !important;
        }

        html:not(.dark) body .content-surface .clean-admin-input,
        html:not(.dark) body .content-surface .clean-admin-select,
        html:not(.dark) body .content-surface .clean-admin-reset,
        html:not(.dark) body .content-surface .inventory-bulk-select,
        html:not(.dark) body .content-surface .inventory-bulk-input,
        html:not(.dark) body .content-surface .inventory-bulk-count {
            border-color: #cbd5e1 !important;
            background: #f8fafc !important;
            color: #172033 !important;
        }

        html:not(.dark) body .content-surface #walkiesTable thead th {
            border-color: #d8e1ed !important;
            background: #eef3f8 !important;
            color: #526781 !important;
        }

        html:not(.dark) body .content-surface #walkiesTable tbody td,
        html:not(.dark) body .content-surface .inventory-table-footer {
            border-color: #e2e8f0 !important;
            background: #ffffff !important;
            color: #1f2937 !important;
        }

        body .content-surface #walkiesTable thead th,
        html:not(.dark) body .content-surface #walkiesTable thead th,
        html.dark body .content-surface #walkiesTable thead th,
        .dark body .content-surface #walkiesTable thead th {
            height: 30px !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            font-size: 9px !important;
            line-height: 1.05 !important;
        }

        body .content-surface #walkiesTable tbody td,
        html:not(.dark) body .content-surface #walkiesTable tbody td,
        html.dark body .content-surface #walkiesTable tbody td,
        .dark body .content-surface #walkiesTable tbody td {
            height: 22px !important;
            min-height: 22px !important;
            padding-top: 2px !important;
            padding-bottom: 2px !important;
            font-size: 10px !important;
            line-height: 1.15 !important;
        }

        body .content-surface .inventory-id-chip,
        body .content-surface .inventory-type-badge,
        body .content-surface .inventory-status-badge,
        body .content-surface .inventory-meta-pill,
        body .content-surface .clean-admin-pill {
            padding-top: 2px !important;
            padding-bottom: 2px !important;
            font-size: 8px !important;
            line-height: 1.1 !important;
        }

        body .content-surface #walkiesTable,
        html:not(.dark) body .content-surface #walkiesTable,
        html.dark body .content-surface #walkiesTable,
        .dark body .content-surface #walkiesTable {
            table-layout: auto !important;
            width: max-content !important;
            min-width: 980px !important;
        }

        body .content-surface #walkiesTable th,
        body .content-surface #walkiesTable td {
            padding-left: 6px !important;
            padding-right: 6px !important;
            max-width: 140px !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        body .content-surface #walkiesTable th:nth-child(2),
        body .content-surface #walkiesTable td:nth-child(2) {
            width: 64px !important;
            min-width: 64px !important;
            max-width: 64px !important;
        }

        body .content-surface #walkiesTable th:nth-child(3),
        body .content-surface #walkiesTable td:nth-child(3) {
            width: 70px !important;
            min-width: 70px !important;
            max-width: 70px !important;
        }

        body .content-surface #walkiesTable th:nth-child(4),
        body .content-surface #walkiesTable td:nth-child(4) {
            width: 92px !important;
            min-width: 92px !important;
            max-width: 92px !important;
        }

        body .content-surface #walkiesTable th:nth-child(5),
        body .content-surface #walkiesTable td:nth-child(5) {
            width: 74px !important;
            min-width: 74px !important;
            max-width: 74px !important;
        }

        body .content-surface #walkiesTable th:nth-child(6),
        body .content-surface #walkiesTable td:nth-child(6) {
            width: 96px !important;
            min-width: 96px !important;
            max-width: 96px !important;
        }

        body .content-surface #walkiesTable th:nth-child(7),
        body .content-surface #walkiesTable td:nth-child(7) {
            width: 118px !important;
            min-width: 118px !important;
            max-width: 118px !important;
        }

        body .content-surface #walkiesTable th:nth-child(8),
        body .content-surface #walkiesTable td:nth-child(8) {
            width: 68px !important;
            min-width: 68px !important;
            max-width: 68px !important;
        }

        .walkie-timeline-modal {
            width: min(560px, calc(100vw - 24px)) !important;
            max-height: min(82vh, 720px) !important;
            border-radius: 18px !important;
            border: 1px solid rgba(148, 163, 184, .2) !important;
            background: #0f172a !important;
            color: #e5edf7 !important;
            overflow: hidden !important;
            box-shadow: 0 28px 70px rgba(2, 6, 23, .42) !important;
        }

        .walkie-timeline-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            padding: 18px 18px 14px;
            border-bottom: 1px solid rgba(148, 163, 184, .16);
        }

        .walkie-timeline-kicker {
            margin: 0 0 6px;
            color: #8ea0b8;
            font-size: 9px;
            font-weight: 900;
            letter-spacing: .16em;
            text-transform: uppercase;
        }

        .walkie-timeline-title {
            margin: 0;
            color: #f8fafc !important;
            font-size: 20px !important;
            font-weight: 900 !important;
            line-height: 1.1 !important;
        }

        .walkie-timeline-subtitle {
            margin: 6px 0 0;
            color: #9fb0c5;
            font-size: 11px;
            font-weight: 750;
            line-height: 1.45;
        }

        .walkie-timeline-close {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            flex: 0 0 auto;
            border-radius: 12px;
            border: 1px solid rgba(148, 163, 184, .2);
            background: #111827;
            color: #cbd5e1;
            transition: all .16s ease;
        }

        .walkie-timeline-close:hover {
            color: #ffffff;
            border-color: rgba(56, 189, 248, .38);
            background: #162033;
        }

        .walkie-timeline-summary {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
            padding: 14px 18px;
            border-bottom: 1px solid rgba(148, 163, 184, .12);
        }

        .walkie-timeline-summary-item {
            min-width: 0;
            border-radius: 12px;
            border: 1px solid rgba(148, 163, 184, .14);
            background: #111827;
            padding: 10px;
        }

        .walkie-timeline-summary-label {
            color: #8ea0b8;
            font-size: 8px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .walkie-timeline-summary-value {
            margin-top: 5px;
            color: #f8fafc;
            font-size: 11px;
            font-weight: 850;
            line-height: 1.3;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .walkie-timeline-body {
            max-height: 420px;
            overflow-y: auto;
            padding: 16px 18px 18px;
        }

        .walkie-timeline-list {
            position: relative;
            display: grid;
            gap: 0;
        }

        .walkie-timeline-row {
            position: relative;
            display: grid;
            grid-template-columns: 88px 20px minmax(0, 1fr);
            gap: 12px;
            padding-bottom: 18px;
        }

        .walkie-timeline-row:last-child {
            padding-bottom: 0;
        }

        .walkie-timeline-row::before {
            content: "";
            position: absolute;
            top: 19px;
            bottom: 0;
            left: 98px;
            width: 1px;
            background: rgba(148, 163, 184, .18);
        }

        .walkie-timeline-row:last-child::before {
            display: none;
        }

        .walkie-timeline-date {
            color: #93a4ba;
            font-size: 10px;
            font-weight: 800;
            line-height: 1.35;
            text-align: right;
        }

        .walkie-timeline-time {
            display: block;
            margin-top: 2px;
            color: #64748b;
            font-size: 9px;
            font-weight: 750;
        }

        .walkie-timeline-dot {
            width: 11px;
            height: 11px;
            margin-top: 3px;
            border-radius: 999px;
            border: 2px solid #0f172a;
            background: #38bdf8;
            box-shadow: 0 0 0 1px rgba(56, 189, 248, .42);
            z-index: 1;
        }

        .walkie-timeline-dot.created { background: #a78bfa; box-shadow: 0 0 0 1px rgba(167, 139, 250, .42); }
        .walkie-timeline-dot.handover { background: #22c55e; box-shadow: 0 0 0 1px rgba(34, 197, 94, .42); }
        .walkie-timeline-dot.return { background: #14b8a6; box-shadow: 0 0 0 1px rgba(20, 184, 166, .42); }
        .walkie-timeline-dot.maintenance,
        .walkie-timeline-dot.repair { background: #f59e0b; box-shadow: 0 0 0 1px rgba(245, 158, 11, .42); }
        .walkie-timeline-dot.complete { background: #60a5fa; box-shadow: 0 0 0 1px rgba(96, 165, 250, .42); }
        .walkie-timeline-dot.activity { background: #94a3b8; box-shadow: 0 0 0 1px rgba(148, 163, 184, .42); }

        .walkie-timeline-card {
            min-width: 0;
            border-radius: 12px;
            border: 1px solid rgba(148, 163, 184, .14);
            background: #111827;
            padding: 10px 12px;
        }

        .walkie-timeline-event-title {
            margin: 0;
            color: #f8fafc;
            font-size: 12px;
            font-weight: 900;
            line-height: 1.25;
        }

        .walkie-timeline-event-detail {
            margin: 5px 0 0;
            color: #9fb0c5;
            font-size: 10px;
            font-weight: 700;
            line-height: 1.5;
        }

        .walkie-timeline-empty {
            border-radius: 12px;
            border: 1px dashed rgba(148, 163, 184, .24);
            padding: 24px 18px;
            color: #93a4ba;
            text-align: center;
            font-size: 11px;
            font-weight: 800;
        }

        @media (max-width: 560px) {
            .walkie-timeline-summary {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .walkie-timeline-row {
                grid-template-columns: 72px 18px minmax(0, 1fr);
                gap: 10px;
            }

            .walkie-timeline-row::before {
                left: 81px;
            }
        }

        html:not(.dark) .walkie-timeline-modal {
            background: #ffffff !important;
            color: #172033 !important;
        }

        html:not(.dark) .walkie-timeline-title,
        html:not(.dark) .walkie-timeline-summary-value,
        html:not(.dark) .walkie-timeline-event-title {
            color: #172033 !important;
        }

        html:not(.dark) .walkie-timeline-close,
        html:not(.dark) .walkie-timeline-summary-item,
        html:not(.dark) .walkie-timeline-card {
            border-color: #d8e1ed;
            background: #f8fafc;
        }

        .walkie-qr-modal {
            width: min(460px, calc(100vw - 28px)) !important;
            max-height: calc(100vh - 34px);
            overflow-y: auto;
            border-radius: 12px !important;
            border: 1px solid rgba(148, 163, 184, .24) !important;
            background: #0f172a !important;
            color: #e5eefc !important;
            padding: 0 !important;
            box-shadow: 0 28px 70px rgba(0, 0, 0, .42) !important;
        }

        .walkie-qr-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            padding: 18px 18px 14px;
            border-bottom: 1px solid rgba(148, 163, 184, .16);
        }

        .walkie-qr-kicker {
            margin: 0 0 5px;
            color: #8fb7d8;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: .16em;
            text-transform: uppercase;
        }

        .walkie-qr-title {
            margin: 0;
            color: #ffffff;
            font-size: 20px;
            font-weight: 900;
            line-height: 1.1;
            letter-spacing: 0;
        }

        .walkie-qr-subtitle {
            margin: 6px 0 0;
            color: #93a4ba;
            font-size: 11px;
            font-weight: 800;
            line-height: 1.35;
        }

        .walkie-qr-close {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(148, 163, 184, .22);
            background: #111827;
            color: #cbd5e1;
            cursor: pointer;
        }

        .walkie-qr-content {
            display: grid;
            gap: 14px;
            padding: 18px;
        }

        .walkie-qr-card {
            display: grid;
            place-items: center;
            gap: 10px;
            padding: 18px;
            border-radius: 10px;
            border: 1px solid rgba(148, 163, 184, .16);
            background: #111827;
        }

        .walkie-qr-canvas {
            width: 220px;
            min-height: 220px;
            display: grid;
            place-items: center;
            padding: 12px;
            border-radius: 8px;
            background: #ffffff;
        }

        .walkie-qr-canvas canvas,
        .walkie-qr-canvas img {
            width: 196px !important;
            height: 196px !important;
        }

        .walkie-qr-fallback {
            display: none;
            margin: 0;
            max-width: 100%;
            color: #fbbf24;
            font-size: 11px;
            font-weight: 800;
            line-height: 1.45;
            text-align: center;
        }

        .walkie-qr-details {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }

        .walkie-qr-detail {
            min-width: 0;
            border-radius: 8px;
            border: 1px solid rgba(148, 163, 184, .14);
            background: #111827;
            padding: 9px 10px;
        }

        .walkie-qr-detail span {
            display: block;
            color: #7f93ad;
            font-size: 9px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .walkie-qr-detail strong {
            display: block;
            margin-top: 4px;
            color: #f8fafc;
            font-size: 12px;
            font-weight: 900;
            line-height: 1.25;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .walkie-qr-footer {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            padding: 14px 18px 18px;
            border-top: 1px solid rgba(148, 163, 184, .16);
        }

        .walkie-qr-action {
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 0 12px;
            border-radius: 8px;
            border: 1px solid #334155;
            background: #162033;
            color: #f8fafc;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: .06em;
            text-transform: uppercase;
            cursor: pointer;
        }

        .walkie-qr-action:hover {
            background: #1f2b42;
        }

        @media (max-width: 560px) {
            .walkie-qr-details {
                grid-template-columns: 1fr;
            }
        }

        html:not(.dark) .inventory-action-toggle {
            background: #f8fafc;
            color: #172033;
            border-color: #d8e1ed;
        }

        html:not(.dark) .inventory-action-menu {
            background: #ffffff;
            border-color: #d8e1ed;
        }

        html:not(.dark) .inventory-action-item {
            color: #243044;
        }

        html:not(.dark) .inventory-action-item:hover {
            background: #eef4fb;
        }

        html:not(.dark) .walkie-qr-modal {
            background: #ffffff !important;
            color: #172033 !important;
        }

        html:not(.dark) .walkie-qr-title,
        html:not(.dark) .walkie-qr-detail strong {
            color: #172033 !important;
        }

        html:not(.dark) .walkie-qr-close,
        html:not(.dark) .walkie-qr-card,
        html:not(.dark) .walkie-qr-detail {
            border-color: #d8e1ed;
            background: #f8fafc;
        }

        body .content-surface .clean-admin-filter-grid {
            display: grid !important;
            grid-template-columns: minmax(0, 1fr) 360px 82px !important;
            width: 100% !important;
            max-width: 100% !important;
            justify-content: flex-start !important;
            gap: 5px !important;
            align-items: end !important;
        }

        body .content-surface .clean-admin-filter-grid > div {
            display: flex !important;
            flex-direction: column !important;
            align-items: flex-start !important;
            width: 100% !important;
            min-width: 0 !important;
            max-width: none !important;
            gap: 4px !important;
        }

        body .content-surface .clean-admin-filter-grid > #resetFilters {
            align-self: end !important;
            width: 82px !important;
            min-width: 82px !important;
            margin: 0 !important;
        }

        body .content-surface #globalSearch.clean-admin-input {
            width: 100% !important;
            max-width: none !important;
            height: 30px !important;
            min-height: 30px !important;
            border-radius: 8px !important;
            padding: 0 12px !important;
            font-size: 11px !important;
        }

        body .content-surface #filterStatus.clean-admin-select,
        body .content-surface #resetFilters.clean-admin-reset {
            height: 30px !important;
            min-height: 30px !important;
            border-radius: 7px !important;
            font-size: 10px !important;
            font-weight: 800 !important;
        }

        body .content-surface .clean-admin-filter {
            padding: 10px 14px !important;
        }

        body .content-surface .clean-admin-label {
            display: block !important;
            margin: 0 !important;
            font-size: 8px !important;
            letter-spacing: .14em !important;
            line-height: 1 !important;
        }

        body .content-surface #filterStatus.clean-admin-select {
            width: 100% !important;
            padding: 0 28px 0 10px !important;
        }

        body .content-surface #resetFilters.clean-admin-reset {
            width: 82px !important;
            min-width: 82px !important;
            padding: 0 !important;
        }

        body .content-surface #bulkActionForm.inventory-bulk-bar,
        html:not(.dark) body .content-surface #bulkActionForm.inventory-bulk-bar,
        html.dark body .content-surface #bulkActionForm.inventory-bulk-bar,
        .dark body .content-surface #bulkActionForm.inventory-bulk-bar {
            width: fit-content !important;
            max-width: 100% !important;
            border: 0 !important;
            outline: 0 !important;
            padding: 0 !important;
            border-color: transparent !important;
            background: transparent !important;
            background-color: transparent !important;
            box-shadow: none !important;
        }

        body .content-surface #walkiesTable,
        html:not(.dark) body .content-surface #walkiesTable,
        html.dark body .content-surface #walkiesTable,
        .dark body .content-surface #walkiesTable {
            width: 100% !important;
            min-width: 1180px !important;
            table-layout: fixed !important;
        }

        body .content-surface #walkiesTable th.inventory-action-col,
        body .content-surface #walkiesTable td.inventory-action-col,
        html:not(.dark) body .content-surface #walkiesTable th.inventory-action-col,
        html:not(.dark) body .content-surface #walkiesTable td.inventory-action-col {
            width: 92px !important;
            min-width: 92px !important;
            max-width: 92px !important;
        }

        body .content-surface .inventory-action-toggle {
            height: 26px !important;
            min-width: 72px !important;
            border-radius: 7px !important;
            font-size: 9px !important;
            gap: 5px !important;
        }

        body .content-surface #walkiesTable col.inventory-select-colgroup {
            width: 36px !important;
        }

        body .content-surface #walkiesTable col.inventory-radio-colgroup {
            width: 86px !important;
        }

        body .content-surface #walkiesTable col.inventory-status-colgroup {
            width: 96px !important;
        }

        body .content-surface #walkiesTable col.inventory-serial-colgroup {
            width: 126px !important;
        }

        body .content-surface #walkiesTable col.inventory-model-colgroup {
            width: 94px !important;
        }

        body .content-surface #walkiesTable col.inventory-ownership-type-colgroup {
            width: 116px !important;
        }

        body .content-surface #walkiesTable col.inventory-ownership-colgroup {
            width: 112px !important;
        }

        body .content-surface #walkiesTable col.inventory-position-colgroup {
            width: 82px !important;
        }

        body .content-surface #walkiesTable col.inventory-department-colgroup {
            width: 116px !important;
        }

        body .content-surface #walkiesTable col.inventory-temporary-colgroup {
            width: 124px !important;
        }

        body .content-surface #walkiesTable col.inventory-tracking-colgroup {
            width: 110px !important;
        }

        body .content-surface #walkiesTable col.inventory-remarks-colgroup {
            width: 150px !important;
        }

        body .content-surface #walkiesTable col.inventory-action-colgroup {
            width: 92px !important;
        }

        body .content-surface #walkiesTable th.inventory-select-col,
        body .content-surface #walkiesTable td.inventory-select-col,
        html:not(.dark) body .content-surface #walkiesTable th.inventory-select-col,
        html:not(.dark) body .content-surface #walkiesTable td.inventory-select-col {
            width: 36px !important;
            min-width: 36px !important;
            max-width: 36px !important;
            padding-left: 4px !important;
            padding-right: 4px !important;
            text-align: center !important;
        }

        body .content-surface #walkiesTable th.inventory-action-col,
        body .content-surface #walkiesTable td.inventory-action-col,
        html:not(.dark) body .content-surface #walkiesTable th.inventory-action-col,
        html:not(.dark) body .content-surface #walkiesTable td.inventory-action-col {
            position: sticky !important;
            right: 0 !important;
            width: 92px !important;
            min-width: 92px !important;
            max-width: 92px !important;
            text-align: center !important;
        }

        body .content-surface #walkiesTable thead th.inventory-action-col,
        html:not(.dark) body .content-surface #walkiesTable thead th.inventory-action-col {
            background: #eef3f8 !important;
            color: #526781 !important;
            font-size: 0 !important;
            overflow: visible !important;
        }

        body .content-surface #walkiesTable thead th.inventory-action-col::before,
        html:not(.dark) body .content-surface #walkiesTable thead th.inventory-action-col::before,
        html.dark body .content-surface #walkiesTable thead th.inventory-action-col::before,
        .dark body .content-surface #walkiesTable thead th.inventory-action-col::before {
            content: attr(data-label) !important;
            position: absolute !important;
            top: 50% !important;
            left: 50% !important;
            z-index: 5 !important;
            display: block !important;
            transform: translate(-50%, -50%) !important;
            color: #526781 !important;
            font-size: 9px !important;
            font-weight: 900 !important;
            line-height: 1 !important;
            letter-spacing: .04em !important;
            text-transform: uppercase !important;
            white-space: nowrap !important;
            visibility: visible !important;
            opacity: 1 !important;
            pointer-events: none !important;
        }

        html.dark body .content-surface #walkiesTable thead th.inventory-action-col,
        .dark body .content-surface #walkiesTable thead th.inventory-action-col {
            background: #172033 !important;
        }

        html.dark body .content-surface #walkiesTable thead th.inventory-action-col::before,
        .dark body .content-surface #walkiesTable thead th.inventory-action-col::before {
            color: #d7e7fb !important;
        }

        body .content-surface #walkiesTable th.inventory-action-col .inventory-action-heading,
        html:not(.dark) body .content-surface #walkiesTable th.inventory-action-col .inventory-action-heading,
        html.dark body .content-surface #walkiesTable th.inventory-action-col .inventory-action-heading,
        .dark body .content-surface #walkiesTable th.inventory-action-col .inventory-action-heading {
            display: inline-block !important;
            color: #526781 !important;
            font-size: 9px !important;
            font-weight: 900 !important;
            line-height: 1 !important;
            letter-spacing: .04em !important;
            text-transform: uppercase !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        html.dark body .content-surface #walkiesTable th.inventory-action-col .inventory-action-heading,
        .dark body .content-surface #walkiesTable th.inventory-action-col .inventory-action-heading {
            color: #d7e7fb !important;
        }

        body .content-surface #walkiesTable col.inventory-action-colgroup {
            width: 280px !important;
        }

        body .content-surface #walkiesTable,
        html:not(.dark) body .content-surface #walkiesTable,
        html.dark body .content-surface #walkiesTable,
        .dark body .content-surface #walkiesTable {
            min-width: 1280px !important;
        }

        body .content-surface #walkiesTable th.inventory-action-col,
        body .content-surface #walkiesTable td.inventory-action-col,
        html:not(.dark) body .content-surface #walkiesTable th.inventory-action-col,
        html:not(.dark) body .content-surface #walkiesTable td.inventory-action-col,
        html.dark body .content-surface #walkiesTable th.inventory-action-col,
        html.dark body .content-surface #walkiesTable td.inventory-action-col,
        .dark body .content-surface #walkiesTable th.inventory-action-col,
        .dark body .content-surface #walkiesTable td.inventory-action-col {
            width: 280px !important;
            min-width: 280px !important;
            max-width: 280px !important;
            overflow: visible !important;
        }

        body .content-surface #walkiesTable .inventory-action-buttons {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            width: 100% !important;
            white-space: nowrap !important;
        }

        body .content-surface #walkiesTable .inventory-action-buttons form {
            display: inline-flex !important;
            margin: 0 !important;
        }

        body .content-surface #walkiesTable .btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            height: 36px !important;
            min-height: 36px !important;
            padding: 0 12px !important;
            border: 1px solid transparent !important;
            border-radius: 6px !important;
            color: #ffffff !important;
            font-size: 14px !important;
            font-weight: 800 !important;
            line-height: 1 !important;
            text-decoration: none !important;
            cursor: pointer !important;
            box-shadow: none !important;
        }

        body .content-surface #walkiesTable .btn i {
            font-size: 14px !important;
            line-height: 1 !important;
        }

        body .content-surface #walkiesTable .btn-info {
            border-color: #0dcaf0 !important;
            background: #0dcaf0 !important;
            color: #052c33 !important;
        }

        body .content-surface #walkiesTable .btn-primary {
            border-color: #0d6efd !important;
            background: #0d6efd !important;
        }

        body .content-surface #walkiesTable .btn-danger {
            border-color: #dc3545 !important;
            background: #dc3545 !important;
        }

        body .content-surface #walkiesTable col.inventory-action-colgroup {
            width: 280px !important;
        }

        body .content-surface #walkiesTable,
        html:not(.dark) body .content-surface #walkiesTable,
        html.dark body .content-surface #walkiesTable,
        .dark body .content-surface #walkiesTable {
            min-width: 1240px !important;
            width: max-content !important;
        }

        body .content-surface #walkiesTable th.inventory-action-col,
        body .content-surface #walkiesTable td.inventory-action-col,
        html:not(.dark) body .content-surface #walkiesTable th.inventory-action-col,
        html:not(.dark) body .content-surface #walkiesTable td.inventory-action-col,
        html.dark body .content-surface #walkiesTable th.inventory-action-col,
        html.dark body .content-surface #walkiesTable td.inventory-action-col,
        .dark body .content-surface #walkiesTable th.inventory-action-col,
        .dark body .content-surface #walkiesTable td.inventory-action-col {
            display: table-cell !important;
            position: static !important;
            right: auto !important;
            left: auto !important;
            z-index: auto !important;
            width: 280px !important;
            min-width: 280px !important;
            max-width: 280px !important;
            overflow: visible !important;
            text-align: center !important;
            box-shadow: none !important;
        }

        body .content-surface #walkiesTable col.inventory-action-colgroup {
            display: table-column !important;
        }

        body .content-surface #walkiesTable td.inventory-action-col {
            padding-left: 8px !important;
            padding-right: 8px !important;
        }

        body .content-surface #walkiesTable td.inventory-action-col .inventory-action-buttons {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 4px !important;
            min-width: 0 !important;
            width: 100% !important;
        }

        body .content-surface #walkiesTable td.inventory-action-col .btn {
            height: 36px !important;
            min-height: 36px !important;
            padding: 0 12px !important;
            font-size: 14px !important;
            border-radius: 6px !important;
        }

        body .content-surface #walkiesTable .inventory-action-dropdown {
            position: relative !important;
            display: inline-flex !important;
            justify-content: center !important;
            width: 100% !important;
        }

        body .content-surface #walkiesTable .inventory-action-toggle {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 78px !important;
            min-width: 78px !important;
            height: 24px !important;
            min-height: 24px !important;
            padding: 0 8px !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 6px !important;
            background: #ffffff !important;
            color: #172033 !important;
            font-size: 9px !important;
            font-weight: 900 !important;
            line-height: 1 !important;
            cursor: pointer !important;
            box-shadow: none !important;
        }

        body .content-surface #walkiesTable .inventory-action-toggle i {
            font-size: 8px !important;
        }

        body .content-surface .inventory-action-menu {
            position: absolute !important;
            top: calc(100% + 6px) !important;
            right: 0 !important;
            z-index: 1000 !important;
            display: none !important;
            min-width: 150px !important;
            padding: 5px !important;
            border: 1px solid #d8e1ed !important;
            border-radius: 8px !important;
            background: #ffffff !important;
            box-shadow: 0 12px 28px rgba(15, 23, 42, .18) !important;
        }

        body .content-surface .inventory-action-dropdown.is-open .inventory-action-menu,
        body .content-surface .inventory-action-menu.is-portal,
        body .inventory-action-menu.is-portal {
            display: grid !important;
            gap: 3px !important;
        }

        body .content-surface .inventory-action-menu form,
        body .inventory-action-menu.is-portal form {
            margin: 0 !important;
        }

        body .content-surface .inventory-action-item,
        body .inventory-action-menu.is-portal .inventory-action-item {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            width: 100% !important;
            min-height: 30px !important;
            padding: 0 9px !important;
            border: 0 !important;
            border-radius: 6px !important;
            background: transparent !important;
            color: #243044 !important;
            font-size: 10px !important;
            font-weight: 800 !important;
            line-height: 1 !important;
            text-align: left !important;
            text-decoration: none !important;
            cursor: pointer !important;
        }

        body .content-surface .inventory-action-item:hover,
        body .inventory-action-menu.is-portal .inventory-action-item:hover {
            background: #eef4fb !important;
        }

        body .content-surface .inventory-action-danger,
        body .inventory-action-menu.is-portal .inventory-action-danger {
            color: #dc2626 !important;
        }

        body .content-surface:has(.inventory-page-shell) {
            background: #eef3f8 !important;
            padding: 16px !important;
        }

        body .content-surface .inventory-page-shell {
            display: grid !important;
            gap: 20px !important;
            padding: 18px 18px 20px !important;
            border: 1px solid #d8e1ed !important;
            border-radius: 14px !important;
            background: #f5f8fc !important;
            box-shadow: none !important;
        }

        body .content-surface .inventory-page-header {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: flex-start !important;
            gap: 36px !important;
            min-height: 0 !important;
            margin: 0 !important;
            padding: 0 2px 14px !important;
            border: 0 !important;
            border-radius: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
        }

        body .content-surface .inventory-header-copy {
            flex: 0 1 auto !important;
            min-width: 0 !important;
        }

        body .content-surface .inventory-header-actions {
            display: flex !important;
            flex: 0 0 auto !important;
            width: auto !important;
            align-items: center !important;
            justify-content: flex-start !important;
            gap: 10px !important;
            padding-top: 0 !important;
        }

        body .content-surface .inventory-page-header .page-title-standard {
            margin: 0 0 8px !important;
            color: #172033 !important;
            font-size: 22px !important;
            font-weight: 900 !important;
            line-height: 1.1 !important;
            letter-spacing: 0 !important;
        }

        body .content-surface .inventory-page-header .page-subtitle-standard {
            max-width: none !important;
            margin: 0 !important;
            color: #64748b !important;
            font-size: 12px !important;
            font-weight: 900 !important;
            line-height: 1.2 !important;
            letter-spacing: .22em !important;
            text-transform: uppercase !important;
        }

        body .content-surface .inventory-page-header .wt-btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 8px !important;
            min-width: 118px !important;
            height: 42px !important;
            min-height: 42px !important;
            padding: 0 16px !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 9px !important;
            background: #ffffff !important;
            color: #0f172a !important;
            font-size: 13px !important;
            font-weight: 900 !important;
            line-height: 1 !important;
            letter-spacing: 0 !important;
            box-shadow: none !important;
            white-space: nowrap !important;
        }

        body .content-surface .inventory-page-header .wt-btn svg {
            width: 14px !important;
            height: 14px !important;
            margin-right: 0 !important;
            flex: 0 0 auto !important;
        }

        @media (max-width: 767px) {
            body .content-surface .inventory-page-shell {
                padding: 14px !important;
            }

            body .content-surface .inventory-page-header {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 14px !important;
            }

            body .content-surface .inventory-header-actions {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                width: 100% !important;
            }

            body .content-surface .inventory-page-header .wt-btn {
                width: 100% !important;
                min-width: 0 !important;
            }

            body .content-surface .clean-admin-filter-grid {
                grid-template-columns: 1fr !important;
            }

            body .content-surface #globalSearch.clean-admin-input,
            body .content-surface #resetFilters.clean-admin-reset {
                width: 100% !important;
            }

            body .content-surface #bulkActionForm.inventory-bulk-bar {
                width: 100% !important;
            }
        }

        html.dark body .content-surface:has(.inventory-page-shell),
        .dark body .content-surface:has(.inventory-page-shell) {
            background: #0f172a !important;
            border-color: #273449 !important;
            color: #e2e8f0 !important;
        }

        html.dark body .content-surface .inventory-page-shell,
        .dark body .content-surface .inventory-page-shell {
            background: #111827 !important;
            border-color: #273449 !important;
            color: #e2e8f0 !important;
        }

        html.dark body .content-surface .inventory-page-header,
        .dark body .content-surface .inventory-page-header {
            background: transparent !important;
        }

        html.dark body .content-surface .inventory-page-header .page-title-standard,
        .dark body .content-surface .inventory-page-header .page-title-standard {
            color: #f8fafc !important;
            text-shadow: none !important;
        }

        html.dark body .content-surface .inventory-page-header .page-subtitle-standard,
        .dark body .content-surface .inventory-page-header .page-subtitle-standard {
            color: #9fb3cd !important;
        }

        html.dark body .content-surface .inventory-page-header .wt-btn,
        .dark body .content-surface .inventory-page-header .wt-btn {
            background: #0f172a !important;
            border-color: #34445f !important;
            color: #f8fafc !important;
        }

        html.dark body .content-surface .inventory-page-header .wt-btn:hover,
        .dark body .content-surface .inventory-page-header .wt-btn:hover {
            background: #172033 !important;
            border-color: #48617f !important;
        }

        html.dark body .content-surface .clean-admin-filter,
        .dark body .content-surface .clean-admin-filter {
            background: #0f172a !important;
            border-color: #273449 !important;
        }

        html.dark body .content-surface .clean-admin-label,
        .dark body .content-surface .clean-admin-label {
            color: #9fb3cd !important;
        }

        html.dark body .content-surface .clean-admin-input,
        html.dark body .content-surface .clean-admin-select,
        html.dark body .content-surface .clean-admin-reset,
        html.dark body .content-surface .inventory-bulk-count,
        html.dark body .content-surface .inventory-bulk-select,
        html.dark body .content-surface .inventory-bulk-input,
        .dark body .content-surface .clean-admin-input,
        .dark body .content-surface .clean-admin-select,
        .dark body .content-surface .clean-admin-reset,
        .dark body .content-surface .inventory-bulk-count,
        .dark body .content-surface .inventory-bulk-select,
        .dark body .content-surface .inventory-bulk-input {
            background: #111827 !important;
            border-color: #34445f !important;
            color: #f8fafc !important;
        }

        html.dark body .content-surface .clean-admin-input::placeholder,
        html.dark body .content-surface .inventory-bulk-input::placeholder,
        .dark body .content-surface .clean-admin-input::placeholder,
        .dark body .content-surface .inventory-bulk-input::placeholder {
            color: #7f93ad !important;
        }

        html.dark body .content-surface #mainTableContainer.inventory-table-shell,
        html.dark body .content-surface .clean-admin-table-shell,
        .dark body .content-surface #mainTableContainer.inventory-table-shell,
        .dark body .content-surface .clean-admin-table-shell {
            background: #0f172a !important;
            border-color: #273449 !important;
        }

        html.dark body .content-surface #walkiesTable thead th,
        .dark body .content-surface #walkiesTable thead th {
            background: #172033 !important;
            border-color: #34445f !important;
            color: #d7e7fb !important;
        }

        html.dark body .content-surface #walkiesTable tbody td,
        .dark body .content-surface #walkiesTable tbody td {
            background: #0f172a !important;
            border-color: #273449 !important;
            color: #e2e8f0 !important;
        }

        html.dark body .content-surface #walkiesTable tbody tr:hover td,
        .dark body .content-surface #walkiesTable tbody tr:hover td {
            background: #172033 !important;
        }

        html.dark body .content-surface #walkiesTable td.inventory-action-col,
        .dark body .content-surface #walkiesTable td.inventory-action-col {
            background: #0f172a !important;
        }

        html.dark body .content-surface #walkiesTable th.inventory-action-col,
        .dark body .content-surface #walkiesTable th.inventory-action-col {
            background: #172033 !important;
        }

        body .content-surface #walkiesTable,
        html:not(.dark) body .content-surface #walkiesTable,
        html.dark body .content-surface #walkiesTable,
        .dark body .content-surface #walkiesTable {
            min-width: 1180px !important;
        }

        body .content-surface #walkiesTable th.inventory-action-col,
        body .content-surface #walkiesTable td.inventory-action-col,
        html:not(.dark) body .content-surface #walkiesTable th.inventory-action-col,
        html:not(.dark) body .content-surface #walkiesTable td.inventory-action-col,
        html.dark body .content-surface #walkiesTable th.inventory-action-col,
        html.dark body .content-surface #walkiesTable td.inventory-action-col,
        .dark body .content-surface #walkiesTable th.inventory-action-col,
        .dark body .content-surface #walkiesTable td.inventory-action-col {
            position: static !important;
            right: auto !important;
            left: auto !important;
            z-index: auto !important;
        }

        body .content-surface #walkiesTable th.inventory-tracking-col,
        body .content-surface #walkiesTable td.inventory-tracking-col,
        body .content-surface #walkiesTable th.inventory-remarks-col,
        body .content-surface #walkiesTable td.inventory-remarks-col {
            display: table-cell !important;
            visibility: visible !important;
            opacity: 1 !important;
            position: static !important;
        }

        body .content-surface #walkiesTable th.inventory-tracking-col,
        body .content-surface #walkiesTable td.inventory-tracking-col {
            width: 100px !important;
            min-width: 100px !important;
            max-width: 100px !important;
        }

        body .content-surface #walkiesTable th.inventory-remarks-col,
        body .content-surface #walkiesTable td.inventory-remarks-col {
            width: 132px !important;
            min-width: 132px !important;
            max-width: 132px !important;
        }
    </style>

    <style>
        /* Match Under Repair / Faulty controls exactly. */
        body .content-surface:has(.inventory-page-shell) {
            background: #0b1220 !important;
            border: 0 !important;
            border-radius: 0 !important;
            padding: 10px !important;
            box-shadow: none !important;
        }

        body .content-surface .inventory-page-shell {
            background: transparent !important;
            border: 0 !important;
            border-radius: 0 !important;
            padding: 0 !important;
            gap: 12px !important;
        }

        body .content-surface .inventory-page-header {
            padding: 0 2px 10px !important;
            background: transparent !important;
            border: 0 !important;
            box-shadow: none !important;
        }

        body .content-surface .inventory-page-header .page-title-standard {
            margin: 0 !important;
            color: #f8fafc !important;
            font-size: 19px !important;
            line-height: 1.1 !important;
        }

        body .content-surface .inventory-page-header .page-subtitle-standard {
            max-width: 560px !important;
            margin-top: 5px !important;
            color: #93a4ba !important;
            font-size: 9px !important;
            letter-spacing: .16em !important;
            line-height: 1.45 !important;
        }

        body .content-surface .inventory-page-header .wt-btn {
            width: auto !important;
            min-width: 118px !important;
            height: 34px !important;
            min-height: 34px !important;
            padding: 0 12px !important;
            border-radius: 10px !important;
            border: 1px solid rgba(148, 163, 184, .24) !important;
            background: #111827 !important;
            color: #f8fafc !important;
            font-size: 12px !important;
            font-weight: 900 !important;
            box-shadow: none !important;
        }

        body .content-surface .inventory-page-shell .clean-admin-filter {
            width: 100% !important;
            margin: 0 !important;
            padding: 12px !important;
            border: 1px solid rgba(148, 163, 184, .18) !important;
            border-radius: 14px !important;
            background: #0f172a !important;
            box-shadow: none !important;
        }

        body .content-surface .inventory-page-shell .clean-admin-filter-grid {
            display: grid !important;
            grid-template-columns: minmax(0, 1fr) 360px 82px !important;
            gap: 5px !important;
            align-items: end !important;
            width: 100% !important;
            max-width: none !important;
        }

        body .content-surface .inventory-page-shell .clean-admin-filter-grid > div {
            width: 100% !important;
            min-width: 0 !important;
            max-width: none !important;
        }

        body .content-surface .inventory-page-shell .clean-admin-label {
            margin: 0 0 6px !important;
            color: #8ea0b8 !important;
            font-size: 9px !important;
            letter-spacing: .12em !important;
        }

        body .content-surface .inventory-page-shell .clean-admin-input,
        body .content-surface .inventory-page-shell .clean-admin-select,
        body .content-surface .inventory-page-shell .clean-admin-reset {
            height: 38px !important;
            min-height: 38px !important;
            border-radius: 10px !important;
            border: 1px solid rgba(148, 163, 184, .26) !important;
            background: #111827 !important;
            color: #e5edf7 !important;
            font-size: 12px !important;
            font-weight: 750 !important;
        }

        body .content-surface .inventory-page-shell .clean-admin-input {
            width: 100% !important;
            max-width: none !important;
            padding: 0 14px !important;
        }

        body .content-surface .inventory-page-shell .clean-admin-select {
            width: 100% !important;
            padding: 0 30px 0 14px !important;
        }

        body .content-surface .inventory-page-shell .clean-admin-reset {
            width: 82px !important;
            min-width: 82px !important;
            padding: 0 !important;
            background: transparent !important;
            color: #dbeafe !important;
        }

        @media (max-width: 1250px) {
            body .content-surface .inventory-page-shell .clean-admin-filter-grid {
                grid-template-columns: minmax(240px, 1fr) 360px 82px !important;
            }
        }

        /* Final inventory column order: keep Tracking REF and Remarks at the far end before Action. */
        body .content-surface .inventory-page-shell #walkiesTable,
        html:not(.dark) body .content-surface .inventory-page-shell #walkiesTable,
        html.dark body .content-surface .inventory-page-shell #walkiesTable,
        .dark body .content-surface .inventory-page-shell #walkiesTable {
            width: max-content !important;
            min-width: 1460px !important;
            table-layout: fixed !important;
        }

        body .content-surface .inventory-page-shell #walkiesTable th.inventory-action-col,
        body .content-surface .inventory-page-shell #walkiesTable td.inventory-action-col,
        html:not(.dark) body .content-surface .inventory-page-shell #walkiesTable th.inventory-action-col,
        html:not(.dark) body .content-surface .inventory-page-shell #walkiesTable td.inventory-action-col,
        html.dark body .content-surface .inventory-page-shell #walkiesTable th.inventory-action-col,
        html.dark body .content-surface .inventory-page-shell #walkiesTable td.inventory-action-col,
        .dark body .content-surface .inventory-page-shell #walkiesTable th.inventory-action-col,
        .dark body .content-surface .inventory-page-shell #walkiesTable td.inventory-action-col {
            position: static !important;
            right: auto !important;
            left: auto !important;
            z-index: auto !important;
            width: 280px !important;
            min-width: 280px !important;
            max-width: 280px !important;
        }

        body .content-surface .inventory-page-shell #walkiesTable th.inventory-tracking-col,
        body .content-surface .inventory-page-shell #walkiesTable td.inventory-tracking-col {
            display: table-cell !important;
            width: 112px !important;
            min-width: 112px !important;
            max-width: 112px !important;
        }

        body .content-surface .inventory-page-shell #walkiesTable th.inventory-remarks-col,
        body .content-surface .inventory-page-shell #walkiesTable td.inventory-remarks-col {
            display: table-cell !important;
            width: 150px !important;
            min-width: 150px !important;
            max-width: 150px !important;
        }
    </style>

    <script>
        const walkieTimelineData = @json($walkieTimelines ?? []);
        const inventoryActionData = @json($walkieActions ?? []);

        window.setTimeout(() => {
            document.documentElement.classList.add('inventory-page-ready');
        }, 1200);

        function timelineEscape(value) {
            return String(value ?? '').replace(/[&<>"']/g, function (character) {
                return {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;',
                }[character];
            });
        }

        function openWalkieTimeline(walkieId) {
            const modal = document.getElementById('walkieTimelineModal');
            const title = document.getElementById('timelineTitle');
            const subtitle = document.getElementById('timelineSubtitle');
            const summaryHost = document.getElementById('timelineSummary');
            const bodyHost = document.getElementById('timelineBody');
            const timeline = walkieTimelineData[String(walkieId)] || {};
            const summary = timeline.summary || {};
            const events = Array.isArray(timeline.events) ? timeline.events : [];

            if (!modal || !title || !subtitle || !summaryHost || !bodyHost) return;

            title.textContent = summary.radio_id || '-';
            subtitle.textContent = `${summary.model || '-'} Â· ${summary.serial_number || '-'} Â· ${summary.status || 'UNKNOWN'}`;

            const summaryItems = [
                ['Owner', summary.ownership || '-'],
                ['Department', summary.department || '-'],
                ['Status', summary.status || 'UNKNOWN'],
            ];

            summaryHost.innerHTML = summaryItems.map(([label, value]) => `
                <div class="walkie-timeline-summary-item">
                    <div class="walkie-timeline-summary-label">${timelineEscape(label)}</div>
                    <div class="walkie-timeline-summary-value" title="${timelineEscape(value)}">${timelineEscape(value)}</div>
                </div>
            `).join('');

            if (events.length === 0) {
                bodyHost.innerHTML = '<div class="walkie-timeline-empty">No timeline records found for this unit yet.</div>';
            } else {
                bodyHost.innerHTML = `
                    <div class="walkie-timeline-list">
                        ${events.map((event) => `
                            <div class="walkie-timeline-row">
                                <div class="walkie-timeline-date">
                                    ${timelineEscape(event.date || '-')}
                                    <span class="walkie-timeline-time">${timelineEscape(event.time || '')}</span>
                                </div>
                                <div class="walkie-timeline-dot ${timelineEscape(event.type || 'info')}"></div>
                                <div class="walkie-timeline-card">
                                    <p class="walkie-timeline-event-title">${timelineEscape(event.title || 'Activity')}</p>
                                    <p class="walkie-timeline-event-detail">${timelineEscape(event.detail || '-')}</p>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                `;
            }

            modal.classList.add('active');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function closeWalkieTimeline() {
            const modal = document.getElementById('walkieTimelineModal');
            if (!modal) return;
            modal.classList.remove('active');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        function closeWalkieTimelineOutside(event) {
            if (event.target === document.getElementById('walkieTimelineModal')) {
                closeWalkieTimeline();
            }
        }

        let currentWalkieQr = null;
        let currentActionDropdown = null;

        function restoreInventoryActionMenu(dropdown) {
            if (!dropdown || !dropdown._actionMenu || !dropdown._actionPlaceholder) return;

            dropdown._actionMenu.classList.remove('is-portal');
            dropdown._actionMenu.removeAttribute('style');
            dropdown.insertBefore(dropdown._actionMenu, dropdown._actionPlaceholder);
            dropdown._actionPlaceholder.remove();
            dropdown._actionMenu = null;
            dropdown._actionPlaceholder = null;
        }

        function closeInventoryActionMenus(exceptMenu = null) {
            document.querySelectorAll('.inventory-action-dropdown.is-open').forEach((menu) => {
                if (menu !== exceptMenu) {
                    menu.classList.remove('is-open');
                    menu.querySelector('.inventory-action-toggle')?.setAttribute('aria-expanded', 'false');
                    restoreInventoryActionMenu(menu);
                }
            });
        }

        function toggleInventoryActionMenu(event, button) {
            event.stopPropagation();
            const dropdown = button.closest('.inventory-action-dropdown');
            if (!dropdown) return;

            const shouldOpen = !dropdown.classList.contains('is-open');
            closeInventoryActionMenus(dropdown);
            dropdown.classList.toggle('is-open', shouldOpen);
            button.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');

            if (!shouldOpen) {
                restoreInventoryActionMenu(dropdown);
                currentActionDropdown = null;
                return;
            }

            const menu = dropdown.querySelector('.inventory-action-menu');
            if (!menu) return;

            const rect = button.getBoundingClientRect();
            const placeholder = document.createComment('inventory-action-menu');
            dropdown.insertBefore(placeholder, menu);
            document.body.appendChild(menu);
            menu.classList.add('is-portal');
            menu.onclick = (menuEvent) => menuEvent.stopPropagation();
            menu.style.minWidth = `${Math.max(rect.width, 176)}px`;
            menu.style.right = 'auto';
            menu.style.left = `${Math.max(8, Math.min(rect.right - 176, window.innerWidth - 188))}px`;
            menu.style.top = `${Math.max(8, Math.min(rect.bottom + 6, window.innerHeight - menu.offsetHeight - 12))}px`;
            dropdown._actionMenu = menu;
            dropdown._actionPlaceholder = placeholder;
            currentActionDropdown = dropdown;
        }

        function decodeWalkieQrPayload(payload) {
            try {
                const bytes = Uint8Array.from(atob(payload), (character) => character.charCodeAt(0));
                return JSON.parse(new TextDecoder().decode(bytes));
            } catch (error) {
                return null;
            }
        }

        function buildWalkieQrText(walkie) {
            return [
                'WT SYSTEM WALKIE TALKIE',
                `WT ID: ${walkie.walkie_id || '-'}`,
                `Radio ID: ${walkie.radio_id || '-'}`,
                `Serial No: ${walkie.serial_number || '-'}`,
                `Model: ${walkie.model || '-'}`,
                `Status: ${walkie.status || '-'}`,
                `Ownership Type: ${walkie.ownership_type || '-'}`,
                `Owner: ${walkie.owner || '-'}`,
                `Department: ${walkie.department || '-'}`,
                `URL: ${walkie.url || window.location.href}`,
            ].join('\n');
        }

        function renderWalkieQrDetails(walkie) {
            const detailHost = document.getElementById('walkieQrDetails');
            if (!detailHost) return;

            const items = [
                ['Radio ID', walkie.radio_id || '-'],
                ['Serial No', walkie.serial_number || '-'],
                ['Model', walkie.model || '-'],
                ['Status', walkie.status || '-'],
                ['Owner', walkie.owner || '-'],
                ['Department', walkie.department || '-'],
            ];

            detailHost.innerHTML = items.map(([label, value]) => `
                <div class="walkie-qr-detail">
                    <span>${timelineEscape(label)}</span>
                    <strong title="${timelineEscape(value)}">${timelineEscape(value)}</strong>
                </div>
            `).join('');
        }

        function openWalkieQrFromButton(button) {
            closeInventoryActionMenus();
            const walkie = decodeWalkieQrPayload(button?.dataset?.qrPayload || '');
            if (!walkie) return;
            openWalkieQr(walkie);
        }

        function openWalkieQr(walkie) {
            const modal = document.getElementById('walkieQrModal');
            const title = document.getElementById('walkieQrTitle');
            const subtitle = document.getElementById('walkieQrSubtitle');
            const qrHost = document.getElementById('walkieQrCanvas');
            const fallback = document.getElementById('walkieQrFallback');

            if (!modal || !title || !subtitle || !qrHost || !fallback) return;

            currentWalkieQr = {
                ...walkie,
                text: buildWalkieQrText(walkie),
            };

            title.textContent = walkie.radio_id || '-';
            subtitle.textContent = `${walkie.model || '-'} | ${walkie.serial_number || '-'} | ${walkie.status || 'UNKNOWN'}`;
            renderWalkieQrDetails(walkie);

            qrHost.innerHTML = '';
            fallback.style.display = 'none';
            fallback.textContent = '';

            if (typeof QRCode === 'undefined') {
                fallback.style.display = 'block';
                fallback.textContent = 'QR generator is not loaded. Unit details are still shown below.';
            } else {
                new QRCode(qrHost, {
                    text: currentWalkieQr.text,
                    width: 196,
                    height: 196,
                    colorDark: '#0f172a',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.M,
                });
            }

            modal.classList.add('active');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function closeWalkieQr() {
            const modal = document.getElementById('walkieQrModal');
            if (!modal) return;
            modal.classList.remove('active');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        function closeWalkieQrOutside(event) {
            if (event.target === document.getElementById('walkieQrModal')) {
                closeWalkieQr();
            }
        }

        function getWalkieQrImageSource() {
            const qrHost = document.getElementById('walkieQrCanvas');
            const canvas = qrHost?.querySelector('canvas');
            const image = qrHost?.querySelector('img');

            if (canvas) return canvas.toDataURL('image/png');
            if (image) return image.src;

            return '';
        }

        function downloadWalkieQr() {
            if (!currentWalkieQr) return;
            const source = getWalkieQrImageSource();
            if (!source) return;

            const link = document.createElement('a');
            const radioId = String(currentWalkieQr.radio_id || currentWalkieQr.walkie_id || 'walkie').replace(/[^a-z0-9_-]+/gi, '-');
            link.href = source;
            link.download = `walkie-qr-${radioId}.png`;
            link.click();
        }

        function printWalkieQr() {
            if (!currentWalkieQr) return;

            const source = getWalkieQrImageSource();
            const details = timelineEscape(currentWalkieQr.text).replace(/\n/g, '<br>');
            const printWindow = window.open('', '_blank', 'width=520,height=680');
            if (!printWindow) return;

            printWindow.document.write(`
                <!doctype html>
                <html>
                <head>
                    <title>Walkie QR - ${timelineEscape(currentWalkieQr.radio_id || '-')}</title>
                    <style>
                        body { font-family: 'DM Sans', sans-serif; margin: 32px; color: #111827; }
                        .label { width: 320px; border: 1px solid #d1d5db; border-radius: 10px; padding: 20px; }
                        h1 { margin: 0 0 12px; font-size: 20px; }
                        img { width: 220px; height: 220px; display: block; margin: 12px auto; }
                        p { font-size: 12px; line-height: 1.55; margin: 12px 0 0; }
                    </style>
                </head>
                <body>
                    <div class="label">
                        <h1>${timelineEscape(currentWalkieQr.radio_id || '-')}</h1>
                        ${source ? `<img src="${source}" alt="Walkie QR Code">` : ''}
                        <p>${details}</p>
                    </div>
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
        }

        function getVisibleInventoryRows() {
            return Array.from(document.querySelectorAll('#walkiesTable tbody .inventory-row'))
                .filter((row) => row.style.display !== 'none');
        }

        function getSelectedInventoryCheckboxes() {
            return Array.from(document.querySelectorAll('.inventory-row-checkbox:checked'));
        }

        function syncBulkActionState() {
            const selected = getSelectedInventoryCheckboxes();
            const selectedCount = document.getElementById('bulkSelectedCount');
            const applyBtn = document.getElementById('bulkApplyBtn');
            const selectAll = document.getElementById('bulkSelectAll');
            const bulkAction = document.getElementById('bulkActionSelect');
            const bulkStatus = document.getElementById('bulkStatusSelect');
            const bulkRemark = document.getElementById('bulkRemarkInput');
            const visibleCheckboxes = getVisibleInventoryRows()
                .map((row) => row.querySelector('.inventory-row-checkbox'))
                .filter(Boolean);

            if (selectedCount) selectedCount.textContent = selected.length;
            if (applyBtn) applyBtn.disabled = selected.length === 0 || !bulkAction?.value || (bulkAction.value === 'set_status' && !bulkStatus?.value);

            if (bulkStatus && bulkAction) {
                const needsStatus = bulkAction.value === 'set_status';
                bulkStatus.disabled = !needsStatus;
                bulkStatus.required = needsStatus;
                if (!needsStatus) bulkStatus.value = '';
            }

            if (bulkRemark && bulkAction) {
                const canRemark = selected.length > 0 && Boolean(bulkAction.value);
                bulkRemark.disabled = !canRemark;
                if (!canRemark) bulkRemark.value = '';
            }

            if (selectAll) {
                const visibleSelected = visibleCheckboxes.filter((checkbox) => checkbox.checked).length;
                selectAll.checked = visibleCheckboxes.length > 0 && visibleSelected === visibleCheckboxes.length;
                selectAll.indeterminate = visibleSelected > 0 && visibleSelected < visibleCheckboxes.length;
            }
        }

        function bindInventoryBulkActions() {
            const selectAll = document.getElementById('bulkSelectAll');
            const bulkAction = document.getElementById('bulkActionSelect');
            const bulkStatus = document.getElementById('bulkStatusSelect');
            const bulkForm = document.getElementById('bulkActionForm');
            const selectedInputs = document.getElementById('bulkSelectedInputs');

            document.querySelectorAll('.inventory-row-checkbox').forEach((checkbox) => {
                checkbox.addEventListener('change', syncBulkActionState);
            });

            if (selectAll) {
                selectAll.addEventListener('change', function () {
                    getVisibleInventoryRows().forEach((row) => {
                        const checkbox = row.querySelector('.inventory-row-checkbox');
                        if (checkbox) checkbox.checked = this.checked;
                    });
                    syncBulkActionState();
                });
            }

            bulkAction?.addEventListener('change', syncBulkActionState);
            bulkStatus?.addEventListener('change', syncBulkActionState);

            bulkForm?.addEventListener('submit', function (event) {
                const selected = getSelectedInventoryCheckboxes();

                if (selected.length === 0) {
                    event.preventDefault();
                    syncBulkActionState();
                    return;
                }

                if (selectedInputs) {
                    selectedInputs.innerHTML = selected.map((checkbox) => (
                        `<input type="hidden" name="selected_ids[]" value="${checkbox.value}">`
                    )).join('');
                }
            });

            syncBulkActionState();
        }

        // ===== Basic Inventory Filtering =====

        function ensureSelectOption(selectId, value) {
            const normalizedValue = (value || '').toString().trim().toUpperCase();
            const select = document.getElementById(selectId);

            if (!select || normalizedValue === '') {
                if (select) {
                    select.value = '';
                    $(select).trigger('change');
                }
                return;
            }

            const hasOption = Array.from(select.options).some(option => option.value === normalizedValue);

            if (!hasOption) {
                select.add(new Option(normalizedValue, normalizedValue, true, true));
            }

            select.value = normalizedValue;
            $(select).trigger('change');
        }

        $(document).ready(function() {
            function focusOpenSelect2Search() {
                window.setTimeout(function () {
                    const searchField = document.querySelector('.select2-container--open .select2-search__field');

                    if (searchField) {
                        searchField.removeAttribute('readonly');
                        searchField.focus();
                    }
                }, 0);
            }

            function initModalSelects() {
                $('.modal-tag-select').each(function() {
                    const $select = $(this);

                    if ($select.hasClass('select2-hidden-accessible')) {
                        $select.select2('destroy');
                    }

                    $select.select2({
                        width: '100%',
                        tags: true,
                        allowClear: !$select.prop('required'),
                        placeholder: $select.data('placeholder') || 'Type or select option',
                        dropdownParent: $select.closest('.modal-box'),
                        createTag: function(params) {
                            const term = $.trim(params.term);

                            if (term === '') {
                                return null;
                            }

                            const normalizedTerm = term.toUpperCase();

                            return {
                                id: normalizedTerm,
                                text: normalizedTerm,
                                newTag: true
                            };
                        },
                        insertTag: function(data, tag) {
                            data.unshift(tag);
                        }
                    });

                    $select.off('select2:open.modalFocus').on('select2:open.modalFocus', focusOpenSelect2Search);
                });

                $('.modal-smart-select').each(function() {
                    const $select = $(this);

                    if ($select.hasClass('select2-hidden-accessible')) {
                        $select.select2('destroy');
                    }

                    $select.select2({
                        width: '100%',
                        allowClear: !$select.prop('required'),
                        placeholder: $select.data('placeholder') || 'Search option',
                        dropdownParent: $select.closest('.modal-box')
                    });

                    $select.off('select2:open.modalFocus').on('select2:open.modalFocus', focusOpenSelect2Search);
                });
            }

            function syncSharedWith($select) {
                const $form = $select.closest('form');
                const isShared = String($select.val() || '').toUpperCase() === 'SHARED';
                const $group = $form.find('.shared-with-group');
                const $input = $form.find('.shared-with-input');

                $group.toggleClass('hidden', !isShared);
                $input.prop('required', isShared);

                if (!isShared) {
                    $input.val('');
                }
            }

            initModalSelects();

            $('.ownership-type-control')
                .off('change.sharedWith select2:select.sharedWith')
                .on('change.sharedWith select2:select.sharedWith', function() {
                    syncSharedWith($(this));
                })
                .each(function() {
                    syncSharedWith($(this));
                });

            const table = document.getElementById('walkiesTable');
            if (!table) return;

            const actionHeader = table.querySelector('thead th.inventory-action-col');
            table.style.minWidth = '1460px';
            if (actionHeader) {
                actionHeader.dataset.label = 'ACTION';
                actionHeader.innerHTML = '<span class="inventory-action-heading">ACTION</span>';
                actionHeader.style.position = 'static';
                actionHeader.style.right = 'auto';
                actionHeader.style.left = 'auto';
                actionHeader.style.width = '360px';
                actionHeader.style.minWidth = '360px';
                actionHeader.style.maxWidth = '360px';
                actionHeader.style.overflow = 'visible';
            }

            document.querySelectorAll('#walkiesTable_wrapper').forEach((wrapper) => {
                const originalTable = wrapper.querySelector('#walkiesTable');
                if (originalTable && wrapper.parentElement) {
                    wrapper.parentElement.insertBefore(originalTable, wrapper);
                    wrapper.remove();
                }
            });

            const searchInput = document.getElementById('globalSearch');
            const statusFilter = document.getElementById('filterStatus');
            const resetBtn = document.getElementById('resetFilters');
            const rows = Array.from(document.querySelectorAll('#walkiesTable tbody .inventory-row'));
            const tableScroll = document.getElementById('inventoryTableScroll');

            let currentPage = 1;
            const itemsPerPage = 10;
            const maxVisiblePages = 4;
            let filteredRows = [];

            function escapeInventoryAttribute(value) {
                return String(value ?? '').replace(/[&<>"']/g, function (character) {
                    return {
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#039;',
                    }[character];
                });
            }

            function renderInventoryActionCell(row) {
                const actionCell = row.querySelector('.inventory-action-col');
                if (!actionCell) return;

                actionCell.style.position = 'static';
                actionCell.style.right = 'auto';
                actionCell.style.left = 'auto';
                actionCell.style.width = '360px';
                actionCell.style.minWidth = '360px';
                actionCell.style.maxWidth = '360px';
                actionCell.style.overflow = 'visible';

                const walkieId = row.dataset.walkieId || '';
                const action = inventoryActionData[String(walkieId)] || {};

                if (!action.can_manage) {
                    actionCell.innerHTML = `
                        <button type="button" class="btn btn-info btn-sm" title="View Details" onclick="openGlobalWalkieTimeline('${escapeInventoryAttribute(walkieId)}')">
                            <i class="fa-solid fa-eye"></i>
                            <span>View</span>
                        </button>
                    `;
                    return;
                }

                actionCell.innerHTML = `
                    <div class="inventory-action-buttons">
                        <button type="button" class="btn btn-info btn-sm" title="View Details" onclick="openGlobalWalkieTimeline('${escapeInventoryAttribute(walkieId)}')">
                            <i class="fa-solid fa-eye"></i>
                            <span>View</span>
                        </button>

                        <a href="${escapeInventoryAttribute(action.edit_url || '#')}" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-edit"></i>
                            <span>Edit</span>
                        </a>

                        ${action.handover_url ? `
                            <form action="${escapeInventoryAttribute(action.handover_url)}" method="POST" class="d-inline" onsubmit="return confirm('Mark this unit as UNUSED after handover?');">
                                @csrf
                                <input type="hidden" name="status" value="UNUSED">
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fa-solid fa-handshake"></i>
                                    <span>Handover</span>
                                </button>
                            </form>
                        ` : `
                            <button type="button" class="btn btn-secondary btn-sm" disabled title="Only IN USE units show handover action">
                                <i class="fa-solid fa-handshake"></i>
                                <span>Handover</span>
                            </button>
                        `}

                        <form action="${escapeInventoryAttribute(action.delete_url || '#')}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this walkie-talkie record?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fa-solid fa-trash"></i>
                                <span>Delete</span>
                            </button>
                        </form>
                    </div>
                `;
            }

            function renderInventoryActionColumn(targetRows = rows) {
                targetRows.forEach(renderInventoryActionCell);
            }

            function syncInventoryHorizontalScroll() {
                if (!tableScroll) return;
                tableScroll.classList.toggle('has-horizontal-scroll', tableScroll.scrollWidth > tableScroll.clientWidth + 1);
            }

            if (tableScroll) {
                tableScroll.addEventListener('wheel', function (event) {
                    const maxScroll = Math.max(0, this.scrollWidth - this.clientWidth);
                    if (maxScroll === 0) return;

                    const delta = Math.abs(event.deltaX) > Math.abs(event.deltaY) ? event.deltaX : event.deltaY;
                    const nextScroll = Math.max(0, Math.min(maxScroll, this.scrollLeft + delta));

                    if (nextScroll !== this.scrollLeft) {
                        event.preventDefault();
                        this.scrollLeft = nextScroll;
                    }
                }, { passive: false });

                tableScroll.addEventListener('scroll', function () {
                    closeInventoryActionMenus();
                });

                window.addEventListener('resize', syncInventoryHorizontalScroll);
                if ('ResizeObserver' in window) {
                    new ResizeObserver(syncInventoryHorizontalScroll).observe(tableScroll);
                }
            }

            function renderPagination() {
                const paginationContainer = document.querySelector('.inventory-table-pagination');
                if (paginationContainer) paginationContainer.innerHTML = '';
                const infoTotal = document.getElementById('totalItems');
                if (infoTotal) infoTotal.innerText = filteredRows.length || rows.filter(row => row.style.display !== 'none').length;
            }

            function changePage(page) {
                currentPage = page;
                updateTableDisplay();
                renderPagination();
                const scrollContainer = document.getElementById('inventoryTableScroll');
                if (scrollContainer) scrollContainer.scrollTop = 0;
            }

            function updateTableDisplay() {
                rows.forEach(row => row.style.display = 'none');
                filteredRows.forEach(row => row.style.display = '');
                renderInventoryActionColumn(filteredRows);
            }

            function applyInventoryFilters() {
                const searchValue = (searchInput?.value || '').trim().toUpperCase();
                const statusFilterValue = (statusFilter?.value || '').trim().toUpperCase();

                filteredRows = rows.filter((row) => {
                    const matchesSearch = !searchValue || (row.dataset.search || '').includes(searchValue);
                    const matchesStatus = !statusFilterValue || row.dataset.status === statusFilterValue;
                    return matchesSearch && matchesStatus;
                });

                currentPage = 1;
                updateTableDisplay();
                renderPagination();
                syncBulkActionState();
                syncInventoryHorizontalScroll();
            }

            if (searchInput) {
                searchInput.addEventListener('input', applyInventoryFilters);
                searchInput.addEventListener('keyup', applyInventoryFilters);
                searchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        applyInventoryFilters();
                    }
                });
            }

            if (statusFilter) statusFilter.addEventListener('change', applyInventoryFilters);

            if (resetBtn) {
                resetBtn.addEventListener('click', function() {
                    if (searchInput) searchInput.value = '';
                    if (statusFilter) statusFilter.value = '';
                    applyInventoryFilters();
                });
            }

            applyInventoryFilters();
            renderInventoryActionColumn(rows);
            bindInventoryBulkActions();
            syncInventoryHorizontalScroll();
            document.documentElement.classList.add('inventory-page-ready');

            document.addEventListener('click', function(event) {
                if (!event.target.closest('.inventory-action-dropdown') && !event.target.closest('.inventory-action-menu')) {
                    closeInventoryActionMenus();
                }
            });
        });

        // ===== Modal Open / Close =====
        function openAddModal() {
            const modal = document.getElementById('addModal');
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
            $('.modal-tag-select, .modal-smart-select').trigger('change.select2');
        }

        function closeAddModal() {
            const modal = document.getElementById('addModal');
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }

        // ===== Import Modal Functions =====
        function openImportModal() {
            const modal = document.getElementById('importModal');
            if (modal && modal.parentElement !== document.body) {
                document.body.appendChild(modal);
            }
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        function closeImportModal() {
            const modal = document.getElementById('importModal');
            if (modal) {
                modal.classList.remove('active');
            }
            document.body.style.overflow = '';
        }
        function closeImportModalOutside(event) {
            if (event.target === document.getElementById('importModal')) {
                closeImportModal();
            }
        }
        function updateFileName(input) {
            const display = document.getElementById('fileNameDisplay');
            if (input.files && input.files[0]) {
                display.innerText = input.files[0].name;
                display.style.color = '#166534';
            }
        }

        function closeModalOutside(event) {
            if (event.target === document.getElementById('addModal')) {
                closeAddModal();
            }
        }

        function openEditModal(id, radio, serialNumber, model, status, ownershipType, ownership, position, department, temporaryRadioId, trackingRef, remark, needToChangeId, idChangeDone, ownershipTypeToBe, isSpecialUse, specialUseReturned, sharedWith) {
            const form = document.getElementById('editWalkieForm');
            form.action = "{{ route('wt.admin.walkies.updateMeta', ['walkie' => '__ID__']) }}".replace('__ID__', id);
            document.getElementById('editModalSubtitle').innerText = `Updating unit ${radio}`;
            ensureSelectOption('edit_radio_id', radio || '');
            ensureSelectOption('edit_serial_number', serialNumber || '');
            ensureSelectOption('edit_model', model || 'R7');
            ensureSelectOption('edit_status', status || 'UNUSED');
            ensureSelectOption('edit_ownership_type', ownershipType || 'UNALLOCATED');
            document.getElementById('edit_shared_with').value = sharedWith || '';
            ensureSelectOption('edit_ownership', ownership || '');
            ensureSelectOption('edit_position', position || '');
            ensureSelectOption('edit_department', department || '');
            ensureSelectOption('edit_temporary_radio_id', temporaryRadioId || '');
            ensureSelectOption('edit_tracking_ref', trackingRef || '');
            document.getElementById('edit_remark').value = remark || '';
            ensureSelectOption('edit_need_to_change_id', needToChangeId || '0');
            ensureSelectOption('edit_id_change_done', idChangeDone || '0');
            ensureSelectOption('edit_ownership_type_to_be', ownershipTypeToBe || '');
            ensureSelectOption('edit_is_special_use', isSpecialUse || '0');
            ensureSelectOption('edit_special_use_returned', specialUseReturned || '0');
            document.getElementById('editModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
            document.body.style.overflow = '';
        }

        function closeEditModalOutside(event) {
            if (event.target === document.getElementById('editModal')) {
                closeEditModal();
            }
        }

        // ===== Toggle Maintenance Fields =====
        function toggleMaintenanceFields() {
            return;
        }

        // ===== Auto-dismiss success alert =====
        @if(session('success'))
            setTimeout(function() {
                const box = document.getElementById('alertBox');
                if (box) {
                    box.style.transition = 'opacity 0.4s';
                    box.style.opacity = '0';
                    setTimeout(() => box.remove(), 400);
                }
            }, 4000);
        @endif

        // ===== ESC key to close =====
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeInventoryActionMenus();
                closeWalkieTimeline();
                closeWalkieQr();
                closeImportModal();
                closeAddModal();
                closeEditModal();
            }
        });
    </script>

    <style id="inventory-theme-last-word">
        html:not(.dark) body .content-surface:has(.inventory-page-shell) {
            background: #eef3f8 !important;
            color: #172033 !important;
        }

        html:not(.dark) body .content-surface .inventory-page-shell {
            background: transparent !important;
            border: 0 !important;
            color: #172033 !important;
        }

        html:not(.dark) body .content-surface .inventory-page-header .page-title-standard {
            color: #0f172a !important;
        }

        html:not(.dark) body .content-surface .inventory-page-header .page-subtitle-standard,
        html:not(.dark) body .content-surface .clean-admin-label,
        html:not(.dark) body .content-surface #bulkSelectedCount,
        html:not(.dark) body .content-surface .inventory-bulk-count {
            color: #334155 !important;
        }

        html:not(.dark) body .content-surface .clean-admin-filter {
            background: #ffffff !important;
            border-color: #cbd5e1 !important;
        }

        body .content-surface .inventory-page-shell .clean-admin-filter-grid,
        html:not(.dark) body .content-surface .inventory-page-shell .clean-admin-filter-grid,
        html.dark body .content-surface .inventory-page-shell .clean-admin-filter-grid {
            display: flex !important;
            flex-wrap: wrap !important;
            align-items: flex-end !important;
            justify-content: flex-start !important;
            gap: 10px !important;
        }

        body .content-surface .inventory-page-shell .clean-admin-filter-grid > div,
        html:not(.dark) body .content-surface .inventory-page-shell .clean-admin-filter-grid > div,
        html.dark body .content-surface .inventory-page-shell .clean-admin-filter-grid > div {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            gap: 8px !important;
            width: auto !important;
            min-width: 0 !important;
            max-width: none !important;
        }

        body .content-surface .inventory-page-shell .clean-admin-label,
        html:not(.dark) body .content-surface .inventory-page-shell .clean-admin-label,
        html.dark body .content-surface .inventory-page-shell .clean-admin-label {
            margin: 0 !important;
            line-height: 30px !important;
            white-space: nowrap !important;
        }

        body .content-surface #globalSearch.clean-admin-input,
        html:not(.dark) body .content-surface #globalSearch.clean-admin-input,
        html.dark body .content-surface #globalSearch.clean-admin-input {
            width: 220px !important;
            max-width: 32vw !important;
        }

        body .content-surface #filterStatus.clean-admin-select,
        html:not(.dark) body .content-surface #filterStatus.clean-admin-select,
        html.dark body .content-surface #filterStatus.clean-admin-select {
            width: 160px !important;
        }

        body .content-surface #resetFilters.clean-admin-reset,
        html:not(.dark) body .content-surface #resetFilters.clean-admin-reset,
        html.dark body .content-surface #resetFilters.clean-admin-reset {
            align-self: flex-end !important;
            width: 68px !important;
            min-width: 68px !important;
        }

        html:not(.dark) body .content-surface .clean-admin-input,
        html:not(.dark) body .content-surface .clean-admin-select,
        html:not(.dark) body .content-surface .clean-admin-reset,
        html:not(.dark) body .content-surface .inventory-bulk-select,
        html:not(.dark) body .content-surface .inventory-bulk-input,
        html:not(.dark) body .content-surface .inventory-bulk-btn {
            background: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }

        html:not(.dark) body .content-surface #mainTableContainer.inventory-table-shell,
        html:not(.dark) body .content-surface .clean-admin-table-shell {
            background: #ffffff !important;
            border-color: #cbd5e1 !important;
        }

        html:not(.dark) body .content-surface #walkiesTable,
        html:not(.dark) body .content-surface #walkiesTable tr,
        html:not(.dark) body .content-surface #walkiesTable tbody {
            background: #ffffff !important;
            color: #1f2937 !important;
        }

        html:not(.dark) body .content-surface #walkiesTable thead th,
        html:not(.dark) body .content-surface #walkiesTable thead th.inventory-action-col {
            background: #eef3f8 !important;
            border-color: #cbd5e1 !important;
            color: #334155 !important;
        }

        html:not(.dark) body .content-surface #walkiesTable tbody td,
        html:not(.dark) body .content-surface #walkiesTable td.inventory-action-col {
            background: #ffffff !important;
            border-color: #e2e8f0 !important;
            color: #1f2937 !important;
        }

        html:not(.dark) body .content-surface #walkiesTable tbody tr:hover td {
            background: #f8fafc !important;
        }

        html:not(.dark) body .content-surface .inventory-item-title,
        html:not(.dark) body .content-surface .inventory-remark-cell {
            color: #1f2937 !important;
        }

        html:not(.dark) body .content-surface .inventory-id-chip,
        html:not(.dark) body .content-surface .inventory-type-badge {
            background: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
            color: #334155 !important;
        }

        html.dark body .content-surface:has(.inventory-page-shell) {
            background: #0f172a !important;
            color: #e2e8f0 !important;
        }

        html.dark body .content-surface .inventory-page-shell {
            background: transparent !important;
            border: 0 !important;
            color: #e2e8f0 !important;
        }

        html.dark body .content-surface .clean-admin-filter,
        html.dark body .content-surface #mainTableContainer.inventory-table-shell,
        html.dark body .content-surface .clean-admin-table-shell {
            background: #0f172a !important;
            border-color: #273449 !important;
        }

        html.dark body .content-surface #walkiesTable thead th,
        html.dark body .content-surface #walkiesTable thead th.inventory-action-col {
            background: #111827 !important;
            border-color: #334155 !important;
            color: #d7e7fb !important;
        }

        html.dark body .content-surface #walkiesTable tbody td,
        html.dark body .content-surface #walkiesTable td.inventory-action-col {
            background: #0f172a !important;
            border-color: #273449 !important;
            color: #e2e8f0 !important;
        }

        html.dark body .content-surface #walkiesTable tbody tr:hover td {
            background: #172033 !important;
        }
    </style>

    <script>
        (function () {
            function paintInventoryTableTheme() {
                var isDark = document.documentElement.classList.contains('dark')
                    || document.documentElement.getAttribute('data-theme') === 'dark';
                var tableShell = document.getElementById('mainTableContainer');
                var table = document.getElementById('walkiesTable');
                var bulkBar = document.getElementById('bulkActionForm');

                var colors = isDark ? {
                    shellBg: '#0f172a',
                    shellBorder: '#273449',
                    headBg: '#111827',
                    headText: '#d7e7fb',
                    rowBg: '#0f172a',
                    rowText: '#e2e8f0',
                    rowBorder: '#273449',
                    chipBg: '#1f2937',
                    chipBorder: '#334155',
                    chipText: '#e2e8f0'
                } : {
                    shellBg: '#ffffff',
                    shellBorder: '#cbd5e1',
                    headBg: '#eef3f8',
                    headText: '#334155',
                    rowBg: '#ffffff',
                    rowText: '#1f2937',
                    rowBorder: '#e2e8f0',
                    chipBg: '#f1f5f9',
                    chipBorder: '#cbd5e1',
                    chipText: '#334155'
                };

                [tableShell, bulkBar].forEach(function (element) {
                    if (!element) return;
                    element.style.setProperty('background', colors.shellBg, 'important');
                    element.style.setProperty('border-color', colors.shellBorder, 'important');
                    element.style.setProperty('color', colors.rowText, 'important');
                });

                if (!table) return;
                table.style.setProperty('background', colors.shellBg, 'important');
                table.querySelectorAll('thead th').forEach(function (cell) {
                    cell.style.setProperty('background', colors.headBg, 'important');
                    cell.style.setProperty('border-color', colors.shellBorder, 'important');
                    cell.style.setProperty('color', colors.headText, 'important');
                });
                table.querySelectorAll('tbody tr, tbody, thead').forEach(function (row) {
                    row.style.setProperty('background', colors.rowBg, 'important');
                    row.style.setProperty('color', colors.rowText, 'important');
                });
                table.querySelectorAll('tbody td').forEach(function (cell) {
                    cell.style.setProperty('background', colors.rowBg, 'important');
                    cell.style.setProperty('border-color', colors.rowBorder, 'important');
                    cell.style.setProperty('color', colors.rowText, 'important');
                });
                table.querySelectorAll('.inventory-item-title, .inventory-remark-cell').forEach(function (text) {
                    text.style.setProperty('color', colors.rowText, 'important');
                });
                table.querySelectorAll('.inventory-id-chip, .inventory-type-badge').forEach(function (chip) {
                    chip.style.setProperty('background', colors.chipBg, 'important');
                    chip.style.setProperty('border-color', colors.chipBorder, 'important');
                    chip.style.setProperty('color', colors.chipText, 'important');
                });
            }

            document.addEventListener('DOMContentLoaded', paintInventoryTableTheme);
            window.addEventListener('load', paintInventoryTableTheme);
            new MutationObserver(paintInventoryTableTheme).observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['class', 'data-theme']
            });
            window.paintInventoryTableTheme = paintInventoryTableTheme;
        })();
    </script>

    @endsection

    @push('final_styles')
    <style id="inventory-table-theme-authority">
        html:not(.dark) body .content-surface .inventory-page-shell {
            background: #eef3f8 !important;
        }

        html:not(.dark) body .content-surface #bulkActionForm.inventory-bulk-bar,
        html[data-theme="light"] body .content-surface #bulkActionForm.inventory-bulk-bar,
        html:not(.dark) body .content-surface #mainTableContainer.inventory-table-shell,
        html[data-theme="light"] body .content-surface #mainTableContainer.inventory-table-shell {
            background: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            color: #0f172a !important;
            box-shadow: none !important;
        }

        html:not(.dark) body .content-surface #mainTableContainer.inventory-table-shell,
        html[data-theme="light"] body .content-surface #mainTableContainer.inventory-table-shell {
            padding: 0 !important;
            overflow: hidden !important;
        }

        html:not(.dark) body .content-surface #walkiesTable,
        html:not(.dark) body .content-surface #walkiesTable thead,
        html:not(.dark) body .content-surface #walkiesTable tbody,
        html:not(.dark) body .content-surface #walkiesTable tr {
            background: #ffffff !important;
            color: #1f2937 !important;
        }

        html:not(.dark) body .content-surface #walkiesTable th,
        html:not(.dark) body .content-surface #walkiesTable thead th,
        html:not(.dark) body .content-surface #walkiesTable thead th.inventory-action-col {
            background: #eef3f8 !important;
            border-color: #cbd5e1 !important;
            color: #334155 !important;
        }

        html:not(.dark) body .content-surface #walkiesTable td,
        html:not(.dark) body .content-surface #walkiesTable tbody td,
        html:not(.dark) body .content-surface #walkiesTable td.inventory-action-col {
            background: #ffffff !important;
            border-color: #e2e8f0 !important;
            color: #1f2937 !important;
        }

        html:not(.dark) body .content-surface #walkiesTable tbody tr:hover td {
            background: #f8fafc !important;
        }

        html:not(.dark) body .content-surface #walkiesTable .inventory-item-title,
        html:not(.dark) body .content-surface #walkiesTable .inventory-remark-cell {
            color: #1f2937 !important;
        }

        html:not(.dark) body .content-surface #walkiesTable .inventory-id-chip,
        html:not(.dark) body .content-surface #walkiesTable .inventory-type-badge {
            background: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
            color: #334155 !important;
        }

        html[data-theme="light"] body .content-surface #mainTableContainer.inventory-table-shell,
        html[data-theme="light"] body .content-surface .inventory-page-shell .clean-admin-table-scroll {
            background: #ffffff !important;
            border-color: #cbd5e1 !important;
        }

        html[data-theme="light"] body .content-surface #walkiesTable,
        html[data-theme="light"] body .content-surface #walkiesTable thead,
        html[data-theme="light"] body .content-surface #walkiesTable tbody,
        html[data-theme="light"] body .content-surface #walkiesTable tr {
            background: #ffffff !important;
            color: #1f2937 !important;
        }

        html[data-theme="light"] body .content-surface #walkiesTable th,
        html[data-theme="light"] body .content-surface #walkiesTable thead th,
        html[data-theme="light"] body .content-surface #walkiesTable thead th.inventory-action-col {
            background: #eef3f8 !important;
            border-color: #cbd5e1 !important;
            color: #334155 !important;
        }

        html[data-theme="light"] body .content-surface #walkiesTable td,
        html[data-theme="light"] body .content-surface #walkiesTable tbody td,
        html[data-theme="light"] body .content-surface #walkiesTable td.inventory-action-col {
            background: #ffffff !important;
            border-color: #e2e8f0 !important;
            color: #1f2937 !important;
        }

        html[data-theme="light"] body .content-surface #walkiesTable tbody tr:hover td {
            background: #f8fafc !important;
        }

        html[data-theme="light"] body .content-surface #walkiesTable .inventory-item-title,
        html[data-theme="light"] body .content-surface #walkiesTable .inventory-remark-cell {
            color: #1f2937 !important;
        }

        html[data-theme="light"] body .content-surface #walkiesTable .inventory-id-chip,
        html[data-theme="light"] body .content-surface #walkiesTable .inventory-type-badge {
            background: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
            color: #334155 !important;
        }

        html[data-theme="light"] body .content-surface #bulkActionForm.inventory-bulk-bar {
            display: flex !important;
            align-items: center !important;
            gap: 14px !important;
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 0 12px !important;
            padding: 12px 14px !important;
            background: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 10px !important;
            color: #0f172a !important;
            box-shadow: none !important;
        }

        html[data-theme="light"] body .content-surface .inventory-bulk-count {
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            height: 30px !important;
            min-height: 30px !important;
            padding: 0 12px !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 7px !important;
            background: #ffffff !important;
            color: #0f172a !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            white-space: nowrap !important;
            min-width: 92px !important;
        }

        html[data-theme="light"] body .content-surface .inventory-bulk-controls {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            width: auto !important;
            max-width: 100% !important;
        }

        html[data-theme="light"] body .content-surface .inventory-bulk-select,
        html[data-theme="light"] body .content-surface .inventory-bulk-input,
        html[data-theme="light"] body .content-surface .inventory-bulk-btn {
            height: 30px !important;
            min-height: 30px !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 7px !important;
            background: #ffffff !important;
            color: #0f172a !important;
            font-size: 12px !important;
            font-weight: 650 !important;
            box-shadow: none !important;
        }

        html[data-theme="light"] body .content-surface .inventory-bulk-select {
            width: 132px !important;
            padding: 0 28px 0 10px !important;
        }

        html[data-theme="light"] body .content-surface .inventory-bulk-input {
            width: 170px !important;
            padding: 0 10px !important;
        }

        html[data-theme="light"] body .content-surface .inventory-bulk-input::placeholder {
            color: #94a3b8 !important;
        }

        html[data-theme="light"] body .content-surface .inventory-bulk-btn {
            width: 68px !important;
            padding: 0 12px !important;
            border-color: #cbd5e1 !important;
            background: #ffffff !important;
            color: #0f172a !important;
            cursor: pointer !important;
        }

        html[data-theme="light"] body .content-surface .inventory-bulk-btn:not(:disabled):hover {
            border-color: #0f172a !important;
            background: #0f172a !important;
            color: #ffffff !important;
        }

        html[data-theme="light"] body .content-surface .inventory-bulk-btn:disabled,
        html[data-theme="light"] body .content-surface .inventory-bulk-select:disabled,
        html[data-theme="light"] body .content-surface .inventory-bulk-input:disabled {
            opacity: .62 !important;
            cursor: not-allowed !important;
        }

        html[data-theme="dark"] body .content-surface #mainTableContainer.inventory-table-shell,
        html[data-theme="dark"] body .content-surface .inventory-page-shell .clean-admin-table-scroll {
            background: #0f172a !important;
            border-color: #273449 !important;
        }

        html[data-theme="dark"] body .content-surface #walkiesTable th,
        html[data-theme="dark"] body .content-surface #walkiesTable thead th,
        html[data-theme="dark"] body .content-surface #walkiesTable thead th.inventory-action-col {
            background: #111827 !important;
            border-color: #334155 !important;
            color: #d7e7fb !important;
        }

        html[data-theme="dark"] body .content-surface #walkiesTable td,
        html[data-theme="dark"] body .content-surface #walkiesTable tbody td,
        html[data-theme="dark"] body .content-surface #walkiesTable td.inventory-action-col {
            background: #0f172a !important;
            border-color: #273449 !important;
            color: #e2e8f0 !important;
        }

        html[data-theme="dark"] body .content-surface #walkiesTable tbody tr:hover td {
            background: #172033 !important;
        }

        html[data-theme="dark"] body .content-surface #bulkActionForm.inventory-bulk-bar {
            display: flex !important;
            align-items: center !important;
            gap: 14px !important;
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 0 12px !important;
            padding: 12px 14px !important;
            background: #0f172a !important;
            border: 1px solid #273449 !important;
            border-radius: 10px !important;
            color: #e2e8f0 !important;
            box-shadow: none !important;
        }

        html[data-theme="dark"] body .content-surface .inventory-bulk-count {
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            height: 30px !important;
            min-height: 30px !important;
            padding: 0 12px !important;
            border: 1px solid #334155 !important;
            border-radius: 7px !important;
            background: #111827 !important;
            color: #e2e8f0 !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            white-space: nowrap !important;
            min-width: 92px !important;
        }

        html[data-theme="dark"] body .content-surface .inventory-bulk-controls {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            width: auto !important;
            max-width: 100% !important;
        }

        html[data-theme="dark"] body .content-surface .inventory-bulk-select,
        html[data-theme="dark"] body .content-surface .inventory-bulk-input,
        html[data-theme="dark"] body .content-surface .inventory-bulk-btn {
            height: 30px !important;
            min-height: 30px !important;
            border: 1px solid #334155 !important;
            border-radius: 7px !important;
            background: #111827 !important;
            color: #e2e8f0 !important;
            font-size: 12px !important;
            font-weight: 650 !important;
            box-shadow: none !important;
        }

        html[data-theme="dark"] body .content-surface .inventory-bulk-select {
            width: 132px !important;
            padding: 0 28px 0 10px !important;
        }

        html[data-theme="dark"] body .content-surface .inventory-bulk-input {
            width: 170px !important;
            padding: 0 10px !important;
        }

        html[data-theme="dark"] body .content-surface .inventory-bulk-input::placeholder {
            color: #64748b !important;
        }

        html[data-theme="dark"] body .content-surface .inventory-bulk-btn {
            width: 68px !important;
            padding: 0 12px !important;
            border-color: #334155 !important;
            background: #111827 !important;
            color: #e2e8f0 !important;
            cursor: pointer !important;
        }

        html[data-theme="dark"] body .content-surface .inventory-bulk-btn:not(:disabled):hover {
            border-color: #38bdf8 !important;
            background: #0ea5e9 !important;
            color: #ffffff !important;
        }

        html[data-theme="dark"] body .content-surface .inventory-bulk-btn:disabled,
        html[data-theme="dark"] body .content-surface .inventory-bulk-select:disabled,
        html[data-theme="dark"] body .content-surface .inventory-bulk-input:disabled {
            opacity: .55 !important;
            cursor: not-allowed !important;
        }

        html.dark body .content-surface .inventory-page-shell,
        html[data-theme="dark"] body .content-surface .inventory-page-shell {
            background: #0f172a !important;
        }

        html.dark body .content-surface #bulkActionForm.inventory-bulk-bar,
        html[data-theme="dark"] body .content-surface #bulkActionForm.inventory-bulk-bar,
        html.dark body .content-surface #mainTableContainer.inventory-table-shell,
        html[data-theme="dark"] body .content-surface #mainTableContainer.inventory-table-shell {
            background: #0f172a !important;
            border: 1px solid #273449 !important;
            color: #e2e8f0 !important;
            box-shadow: none !important;
        }

        html.dark body .content-surface #mainTableContainer.inventory-table-shell,
        html[data-theme="dark"] body .content-surface #mainTableContainer.inventory-table-shell {
            padding: 0 !important;
            overflow: hidden !important;
        }

        html.dark body .content-surface #walkiesTable th,
        html.dark body .content-surface #walkiesTable thead th,
        html.dark body .content-surface #walkiesTable thead th.inventory-action-col {
            background: #111827 !important;
            border-color: #334155 !important;
            color: #d7e7fb !important;
        }

        html.dark body .content-surface #walkiesTable td,
        html.dark body .content-surface #walkiesTable tbody td,
        html.dark body .content-surface #walkiesTable td.inventory-action-col {
            background: #0f172a !important;
            border-color: #273449 !important;
            color: #e2e8f0 !important;
        }

        @media (max-width: 760px) {
            body .content-surface #bulkActionForm.inventory-bulk-bar,
            html[data-theme="light"] body .content-surface #bulkActionForm.inventory-bulk-bar,
            html[data-theme="dark"] body .content-surface #bulkActionForm.inventory-bulk-bar {
                align-items: flex-start !important;
                flex-direction: column !important;
                width: 100% !important;
            }

            body .content-surface .inventory-bulk-controls,
            html[data-theme="light"] body .content-surface .inventory-bulk-controls,
            html[data-theme="dark"] body .content-surface .inventory-bulk-controls {
                flex-wrap: wrap !important;
                width: 100% !important;
            }
        }
    </style>
    @endpush
