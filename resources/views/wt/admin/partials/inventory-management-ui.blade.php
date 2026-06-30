<span class="inventory-management-ui" hidden></span>
<style>
/* Shared Inventory Management UI. Baseline: Under Repair / Faulty page. */
body .content-surface:has(.inventory-management-ui),
body .content-surface:has(.inventory-page-shell),
body .content-surface:has(.maintenance-page-shell),
body .content-surface:has(.unused-page-shell),
body .content-surface:has(.duplicate-page),
body .content-surface:has(#specialTable) {
    padding: 6px !important;
    border-radius: 10px !important;
    background: #eef3f8 !important;
    font-size: 10px !important;
}

body .content-surface:has(.inventory-management-ui) > .breadcrumb,
body .content-surface:has(.inventory-management-ui) > .page-breadcrumb,
body .content-surface:has(.inventory-page-shell) > .breadcrumb,
body .content-surface:has(.inventory-page-shell) > .page-breadcrumb,
body .content-surface:has(.maintenance-page-shell) > .breadcrumb,
body .content-surface:has(.maintenance-page-shell) > .page-breadcrumb,
body .content-surface:has(.unused-page-shell) > .breadcrumb,
body .content-surface:has(.unused-page-shell) > .page-breadcrumb,
body .content-surface:has(.duplicate-page) > .breadcrumb,
body .content-surface:has(.duplicate-page) > .page-breadcrumb,
body .content-surface:has(#specialTable) > .breadcrumb,
body .content-surface:has(#specialTable) > .page-breadcrumb {
    display: none !important;
}

.inventory-page-shell,
.maintenance-page-shell,
.unused-page-shell,
.duplicate-page,
body .content-surface .special-page-shell,
body .content-surface section.special-page-shell {
    display: grid !important;
    gap: 10px !important;
    width: 100% !important;
    max-width: none !important;
    padding: 0 !important;
    margin: 0 !important;
}

body .content-surface .special-page-shell + .wt-data {
    margin-top: 10px !important;
}

.inventory-page-header,
.maintenance-page-shell > .page-header-block,
.unused-page-shell > .page-header-block,
.special-page-shell > .page-header-block,
.duplicate-hero .page-header-block,
body .content-surface .special-page-shell .wt-data-page-hero {
    width: 100% !important;
    min-height: 0 !important;
    margin: 0 !important;
    padding: 0 0 10px 0 !important;
    border: 0 !important;
    border-left: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
    overflow: visible !important;
    align-items: center !important;
}

.inventory-page-header .page-title-standard,
.maintenance-page-shell .page-title-standard,
.unused-page-shell .page-title-standard,
.special-page-shell .page-title-standard,
.duplicate-hero .page-title-standard,
body .content-surface .special-page-shell .wt-data-page-title {
    margin: 0 0 4px !important;
    color: #0f172a !important;
    font-size: 20px !important;
    font-weight: 900 !important;
    line-height: 1.05 !important;
    letter-spacing: 0 !important;
}

.inventory-page-header .page-subtitle-standard,
.maintenance-page-shell .page-subtitle-standard,
.unused-page-shell .page-subtitle-standard,
.special-page-shell .page-subtitle-standard,
.duplicate-hero .page-subtitle-standard,
body .content-surface .special-page-shell .wt-data-page-subtitle {
    margin: 0 !important;
    color: #526781 !important;
    font-size: 10px !important;
    font-weight: 900 !important;
    letter-spacing: 0.24em !important;
    line-height: 1.2 !important;
    text-transform: uppercase !important;
}

.inventory-page-header .wt-btn,
.maintenance-page-shell > .page-header-block .wt-btn,
.unused-page-shell > .page-header-block .wt-btn,
.special-page-shell > .page-header-block .wt-btn,
.duplicate-hero .wt-btn,
body .content-surface .special-page-shell .wt-data-page-actions .wt-btn {
    width: 106px !important;
    min-width: 106px !important;
    height: 28px !important;
    min-height: 28px !important;
    padding: 0 9px !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 7px !important;
    background: #ffffff !important;
    color: #0f172a !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 5px !important;
    font-size: 12px !important;
    font-weight: 900 !important;
    line-height: 1 !important;
    letter-spacing: 0.02em !important;
    white-space: nowrap !important;
}

.inventory-page-header .wt-btn svg,
.maintenance-page-shell > .page-header-block .wt-btn svg,
.unused-page-shell > .page-header-block .wt-btn svg,
.special-page-shell > .page-header-block .wt-btn svg,
.duplicate-hero .wt-btn svg,
.duplicate-hero .wt-btn i,
body .content-surface .special-page-shell .wt-data-page-actions .wt-btn svg {
    width: 11px !important;
    height: 11px !important;
    margin: 0 !important;
    font-size: 13px !important;
    line-height: 1 !important;
    flex: 0 0 auto !important;
}

.clean-admin-filter,
.unused-filter-panel,
.duplicate-search-panel,
.special-filter-panel,
body .content-surface .wt-data-filter {
    min-height: auto !important;
    margin: 0 !important;
    padding: 9px 12px !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 9px !important;
    background: #ffffff !important;
    box-shadow: none !important;
    gap: 10px !important;
    align-items: end !important;
}

.clean-admin-label,
.unused-filter-field label,
.duplicate-filter-field label,
.special-filter-field label,
body .content-surface .wt-data-field label {
    margin: 0 0 5px !important;
    color: #526781 !important;
    font-size: 11px !important;
    font-weight: 800 !important;
    letter-spacing: 0.14em !important;
    line-height: 1 !important;
    text-transform: uppercase !important;
}

.clean-admin-input,
.clean-admin-select,
.clean-admin-reset,
.unused-filter-input,
.unused-filter-select,
.unused-filter-reset,
.duplicate-search,
.duplicate-filter-select,
.duplicate-filter-reset,
.special-filter-input,
.special-filter-select,
.special-filter-reset,
body .content-surface .wt-data-input,
body .content-surface .wt-data-select,
body .content-surface .wt-data-reset {
    height: 34px !important;
    min-height: 34px !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 7px !important;
    background: #ffffff !important;
    color: #0f172a !important;
    font-size: 13px !important;
    font-weight: 650 !important;
    box-shadow: none !important;
}

.clean-admin-reset,
.unused-filter-reset,
.duplicate-filter-reset,
.special-filter-reset,
body .content-surface .wt-data-reset {
    width: auto !important;
    padding: 0 12px !important;
    font-size: 12px !important;
    font-weight: 900 !important;
}

.inventory-table-shell,
.clean-admin-table-shell,
.unused-table-shell,
.duplicate-table-shell,
.special-table-shell,
body .content-surface .wt-data-table {
    margin: 0 !important;
    padding: 0 !important;
    overflow: hidden !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 8px !important;
    background: #ffffff !important;
    box-shadow: none !important;
}

body .content-surface #walkiesTable thead th,
body .content-surface #maintTable thead th,
body .content-surface #maintenanceTable thead th,
body .content-surface .unused-table th,
body .content-surface #duplicateTable thead th,
body .content-surface #specialTable thead th,
body .content-surface .wt-data thead th {
    height: 46px !important;
    padding: 0 16px !important;
    border: 1px solid #d8e1ed !important;
    background: #f8fafc !important;
    color: #526781 !important;
    font-size: 13px !important;
    font-weight: 900 !important;
    letter-spacing: 0.06em !important;
    line-height: 1.15 !important;
    text-transform: uppercase !important;
    white-space: nowrap !important;
}

body .content-surface #walkiesTable tbody td,
body .content-surface #maintTable tbody td,
body .content-surface #maintenanceTable tbody td,
body .content-surface .unused-table td,
body .content-surface #duplicateTable tbody td,
body .content-surface #specialTable tbody td,
body .content-surface .wt-data tbody td {
    height: 38px !important;
    min-height: 38px !important;
    padding: 8px 16px !important;
    border: 1px solid #e2e8f0 !important;
    background: #ffffff !important;
    color: #1f2937 !important;
    font-size: 12px !important;
    font-weight: 650 !important;
    line-height: 1.25 !important;
    vertical-align: middle !important;
}

body .content-surface tr.hidden,
body .content-surface .wt-data tbody tr.hidden {
    display: none !important;
}

body .content-surface table tbody:empty,
body .content-surface .wt-data table tbody:empty {
    display: none !important;
}

body .content-surface .unused-table tbody,
body .content-surface .duplicate-table-shell table tbody,
body .content-surface .special-table-shell table tbody,
body .content-surface .clean-admin-table-shell table tbody,
body .content-surface .inventory-table-shell table tbody,
body .content-surface .wt-data table tbody {
    height: auto !important;
    min-height: 0 !important;
}

body .content-surface :is(
    #walkiesTable,
    #maintTable,
    #duplicateTable,
    #specialTable,
    .unused-table
) {
    min-width: 860px !important;
}

body .content-surface :is(
    .inventory-action-buttons,
    .maintenance-action-stack,
    .dup-actions,
    .special-action-buttons,
    .unused-actions
) {
    display: flex !important;
    flex-wrap: nowrap !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
}

body .content-surface :is(
    .inventory-action-buttons,
    .maintenance-action-stack,
    .dup-actions,
    .special-action-buttons,
    .unused-actions
) form {
    display: inline-flex !important;
    margin: 0 !important;
}

body .content-surface :is(
    .inventory-action-buttons .btn,
    .maintenance-action-stack .wt-btn,
    .dup-actions .btn,
    .special-action-buttons .btn,
    .unused-action-btn
) {
    min-width: 64px !important;
    min-height: 28px !important;
    height: 28px !important;
    padding: 0 9px !important;
    border-radius: 7px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 5px !important;
    font-size: 10px !important;
    font-weight: 900 !important;
    letter-spacing: .08em !important;
    line-height: 1 !important;
    text-transform: uppercase !important;
    white-space: nowrap !important;
}

body .content-surface :is(
    .inventory-action-buttons .btn-info,
    .maintenance-action-view,
    .dup-actions .btn-info,
    .special-action-buttons .btn-info,
    .unused-action-btn:first-child
) {
    border-color: #0284c7 !important;
    background: #0284c7 !important;
    color: #ffffff !important;
}

body .content-surface .unused-table tbody tr:has(td[colspan]),
body .content-surface .duplicate-table-shell tbody tr:has(td[colspan]),
body .content-surface .special-table-shell tbody tr:has(td[colspan]),
body .content-surface .clean-admin-table-shell tbody tr:has(td[colspan]),
body .content-surface .inventory-table-shell tbody tr:has(td[colspan]),
body .content-surface .wt-data tbody tr:has(td[colspan]) {
    display: none !important;
}

body .content-surface td.dataTables_empty,
body .content-surface .wt-data tbody td.dataTables_empty {
    height: 0 !important;
    min-height: 0 !important;
    padding: 0 !important;
    border: 0 !important;
    color: transparent !important;
}

body .content-surface #walkiesTable tbody tr:hover td,
body .content-surface #maintTable tbody tr:hover td,
body .content-surface #maintenanceTable tbody tr:hover td,
body .content-surface .unused-table tbody tr:hover td,
body .content-surface #duplicateTable tbody tr:hover td,
body .content-surface #specialTable tbody tr:hover td,
body .content-surface .wt-data tbody tr:hover td {
    background: #f8fafc !important;
}

.clean-admin-table-scroll,
.unused-scroll,
.duplicate-table-scroll,
.special-table-scroll,
body .content-surface .wt-data-scroll,
body .content-surface .dataTables_scrollBody {
    scrollbar-width: thin !important;
    scrollbar-color: #64758a #e2e8f0 !important;
}

.clean-admin-table-scroll::-webkit-scrollbar,
.unused-scroll::-webkit-scrollbar,
.duplicate-table-scroll::-webkit-scrollbar,
.special-table-scroll::-webkit-scrollbar,
body .content-surface .wt-data-scroll::-webkit-scrollbar,
body .content-surface .dataTables_scrollBody::-webkit-scrollbar {
    height: 10px !important;
}

.clean-admin-table-scroll::-webkit-scrollbar-track,
.unused-scroll::-webkit-scrollbar-track,
.duplicate-table-scroll::-webkit-scrollbar-track,
.special-table-scroll::-webkit-scrollbar-track,
body .content-surface .wt-data-scroll::-webkit-scrollbar-track,
body .content-surface .dataTables_scrollBody::-webkit-scrollbar-track {
    background: #e2e8f0 !important;
}

.clean-admin-table-scroll::-webkit-scrollbar-thumb,
.unused-scroll::-webkit-scrollbar-thumb,
.duplicate-table-scroll::-webkit-scrollbar-thumb,
.special-table-scroll::-webkit-scrollbar-thumb,
body .content-surface .wt-data-scroll::-webkit-scrollbar-thumb,
body .content-surface .dataTables_scrollBody::-webkit-scrollbar-thumb,
body .content-surface .wt-data-scrollbar-thumb {
    background: #64758a !important;
    border-radius: 999px !important;
}

body .content-surface .wt-data-scrollbar {
    height: 10px !important;
    background: #e2e8f0 !important;
}

.inventory-table-footer,
.repair-table-footer,
.unused-pagination-bar,
.duplicate-table-footer,
.special-table-footer,
.adminit-table-footer,
body .content-surface .wt-data-footer {
    min-height: 54px !important;
    padding: 10px 16px !important;
    gap: 16px !important;
    border-top: 1px solid #d8e1ed !important;
    background: #ffffff !important;
}

.inventory-table-info,
.repair-table-info,
.unused-pagination-info,
.duplicate-table-info,
.special-table-info,
.adminit-table-info,
body .content-surface .wt-data-info {
    color: #020617 !important;
    font-size: 13px !important;
    font-weight: 900 !important;
    letter-spacing: 0 !important;
    text-transform: none !important;
}

.inventory-table-pagination,
.repair-table-pagination,
.unused-pagination-actions,
.duplicate-table-pagination,
.special-table-pagination,
.adminit-table-pagination,
body .content-surface .wt-data-pagination {
    gap: 16px !important;
}

.inventory-page-link,
.repair-page-btn,
.repair-page-number,
.unused-page-btn,
.duplicate-page-link,
.special-page-link,
.adminit-page-link,
.adminit-page-current,
body .content-surface .wt-data .wt-data-page {
    height: 34px !important;
    min-height: 34px !important;
    min-width: 38px !important;
    padding: 0 12px !important;
    border: 1px solid #dbe3ef !important;
    border-radius: 8px !important;
    background: #ffffff !important;
    color: #334155 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 13px !important;
    font-weight: 900 !important;
    line-height: 1 !important;
    opacity: 1 !important;
}

.inventory-page-link:disabled,
.repair-page-btn:disabled,
.unused-page-btn:disabled,
.duplicate-page-link:disabled,
.special-page-link:disabled,
.adminit-page-link:disabled,
body .content-surface .wt-data .wt-data-page:disabled,
body .content-surface .inventory-page-link.opacity-30,
body .content-surface .repair-page-btn.opacity-30,
body .content-surface .unused-page-btn.opacity-30,
body .content-surface .duplicate-page-link.opacity-30,
body .content-surface .special-page-link.opacity-30 {
    opacity: 1 !important;
    background: #ffffff !important;
    color: #64748b !important;
    border-color: #e2e8f0 !important;
    cursor: not-allowed !important;
}

.inventory-page-link.is-nav,
.repair-page-btn,
.unused-page-btn.nav-btn,
.duplicate-page-link.is-nav,
.special-page-link.is-nav,
.adminit-page-link,
body .content-surface .wt-data .wt-data-page.is-nav {
    min-width: 92px !important;
}

.inventory-page-link.is-active,
.repair-page-number.is-active,
.unused-page-btn.active,
.duplicate-page-link.is-active,
.special-page-link.is-active,
.adminit-page-current,
body .content-surface .wt-data .wt-data-page.is-active {
    min-width: 42px !important;
    border-color: #7dd3fc !important;
    background: #e0f2fe !important;
    color: #075985 !important;
}

body .content-surface .wt-data-empty,
.inventory-empty-text {
    color: #64748b !important;
    font-size: 12px !important;
    font-weight: 900 !important;
    letter-spacing: 0.14em !important;
}

/* Frozen 9-column inventory table standard for active Inventory Management views. */
body .content-surface #walkiesTable,
body .content-surface #maintenanceTable,
body .content-surface #duplicateTable,
body .content-surface #specialTable,
body .content-surface .wt-data table {
    width: 100% !important;
    table-layout: fixed !important;
    border-collapse: collapse !important;
}

body .content-surface #walkiesTable th:nth-child(1),
body .content-surface #maintenanceTable th:nth-child(1),
body .content-surface #duplicateTable th:nth-child(1),
body .content-surface #specialTable th:nth-child(1),
body .content-surface .wt-data th:nth-child(1) { width: 10% !important; }

body .content-surface #walkiesTable th:nth-child(2),
body .content-surface #maintenanceTable th:nth-child(2),
body .content-surface #duplicateTable th:nth-child(2),
body .content-surface #specialTable th:nth-child(2),
body .content-surface .wt-data th:nth-child(2) { width: 10% !important; }

body .content-surface #walkiesTable th:nth-child(3),
body .content-surface #maintenanceTable th:nth-child(3),
body .content-surface #duplicateTable th:nth-child(3),
body .content-surface #specialTable th:nth-child(3),
body .content-surface .wt-data th:nth-child(3) { width: 12% !important; }

body .content-surface #walkiesTable th:nth-child(4),
body .content-surface #maintenanceTable th:nth-child(4),
body .content-surface #duplicateTable th:nth-child(4),
body .content-surface #specialTable th:nth-child(4),
body .content-surface .wt-data th:nth-child(4) { width: 11% !important; }

body .content-surface #walkiesTable th:nth-child(5),
body .content-surface #maintenanceTable th:nth-child(5),
body .content-surface #duplicateTable th:nth-child(5),
body .content-surface #specialTable th:nth-child(5),
body .content-surface .wt-data th:nth-child(5) { width: 12% !important; }

body .content-surface #walkiesTable th:nth-child(6),
body .content-surface #maintenanceTable th:nth-child(6),
body .content-surface #duplicateTable th:nth-child(6),
body .content-surface #specialTable th:nth-child(6),
body .content-surface .wt-data th:nth-child(6) { width: 13% !important; }

body .content-surface #walkiesTable th:nth-child(7),
body .content-surface #maintenanceTable th:nth-child(7),
body .content-surface #duplicateTable th:nth-child(7),
body .content-surface #specialTable th:nth-child(7),
body .content-surface .wt-data th:nth-child(7) { width: 11% !important; }

body .content-surface #walkiesTable th:nth-child(8),
body .content-surface #maintenanceTable th:nth-child(8),
body .content-surface #duplicateTable th:nth-child(8),
body .content-surface #specialTable th:nth-child(8),
body .content-surface .wt-data th:nth-child(8) { width: 11% !important; }

body .content-surface #walkiesTable th:nth-child(9),
body .content-surface #maintenanceTable th:nth-child(9),
body .content-surface #duplicateTable th:nth-child(9),
body .content-surface #specialTable th:nth-child(9),
body .content-surface .wt-data th:nth-child(9) { width: 10% !important; }

body .content-surface #walkiesTable tr,
body .content-surface #maintenanceTable tr,
body .content-surface #duplicateTable tr,
body .content-surface #specialTable tr,
body .content-surface .wt-data tr {
    height: 48px !important;
}

body .content-surface #walkiesTable thead th,
body .content-surface #maintenanceTable thead th,
body .content-surface #duplicateTable thead th,
body .content-surface #specialTable thead th,
body .content-surface .wt-data thead th {
    height: 48px !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    color: #475569 !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    letter-spacing: 0.05em !important;
    text-transform: uppercase !important;
    white-space: nowrap !important;
}

body .content-surface #walkiesTable tbody td,
body .content-surface #maintenanceTable tbody td,
body .content-surface #duplicateTable tbody td,
body .content-surface #specialTable tbody td,
body .content-surface .wt-data tbody td {
    height: 48px !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    color: #1e293b !important;
    font-size: 14px !important;
    font-weight: 500 !important;
    white-space: nowrap !important;
}

body .content-surface .inventory-table-shell,
body .content-surface .clean-admin-table-shell,
body .content-surface .duplicate-table-shell,
body .content-surface .special-table-shell,
body .content-surface .wt-data-table {
    min-height: 0 !important;
    display: flex !important;
    flex-direction: column !important;
}

body .content-surface .clean-admin-table-scroll,
body .content-surface .duplicate-table-scroll,
body .content-surface .special-table-scroll,
body .content-surface .wt-data-scroll {
    flex: 1 1 auto !important;
    min-height: 0 !important;
}

/* Final font normalization for every Inventory Management table page. */
body .content-surface .page-title-standard,
body .content-surface .wt-data-page-title {
    font-size: 20px !important;
}

body .content-surface .page-subtitle-standard,
body .content-surface .wt-data-page-subtitle {
    font-size: 10px !important;
}

body .content-surface .wt-btn,
body .content-surface .clean-admin-reset,
body .content-surface .unused-filter-reset,
body .content-surface .duplicate-filter-reset,
body .content-surface .special-filter-reset,
body .content-surface .wt-data-reset {
    font-size: 12px !important;
}

body .content-surface .clean-admin-label,
body .content-surface .unused-filter-field label,
body .content-surface .duplicate-filter-field label,
body .content-surface .special-filter-field label,
body .content-surface .wt-data-field label {
    font-size: 11px !important;
}

body .content-surface .clean-admin-input,
body .content-surface .clean-admin-select,
body .content-surface .unused-filter-input,
body .content-surface .unused-filter-select,
body .content-surface .duplicate-search,
body .content-surface .duplicate-filter-select,
body .content-surface .special-filter-input,
body .content-surface .special-filter-select,
body .content-surface .wt-data-input,
body .content-surface .wt-data-select {
    font-size: 13px !important;
}

body .content-surface #walkiesTable thead th,
body .content-surface #maintenanceTable thead th,
body .content-surface .unused-table th,
body .content-surface #duplicateTable thead th,
body .content-surface #specialTable thead th,
body .content-surface .wt-data thead th {
    font-size: 13px !important;
}

body .content-surface #walkiesTable tbody td,
body .content-surface #maintenanceTable tbody td,
body .content-surface .unused-table td,
body .content-surface #duplicateTable tbody td,
body .content-surface #specialTable tbody td,
body .content-surface .wt-data tbody td {
    font-size: 12px !important;
}

body .content-surface .inventory-table-info,
body .content-surface .repair-table-info,
body .content-surface .unused-pagination-info,
body .content-surface .duplicate-table-info,
body .content-surface .special-table-info,
body .content-surface .adminit-table-info,
body .content-surface .wt-data-info,
body .content-surface .inventory-page-link,
body .content-surface .repair-page-btn,
body .content-surface .repair-page-number,
body .content-surface .unused-page-btn,
body .content-surface .duplicate-page-link,
body .content-surface .special-page-link,
body .content-surface .adminit-page-link,
body .content-surface .adminit-page-current,
body .content-surface .wt-data .wt-data-page {
    font-size: 13px !important;
}

html.dark body .content-surface:has(.inventory-management-ui),
html.dark body .content-surface:has(.inventory-page-shell),
html.dark body .content-surface:has(.maintenance-page-shell),
html.dark body .content-surface:has(.unused-page-shell),
html.dark body .content-surface:has(.duplicate-page),
html.dark body .content-surface:has(#specialTable) {
    background: #0f172a !important;
}

html.dark .inventory-page-header,
html.dark .maintenance-page-shell > .page-header-block,
html.dark .unused-page-shell > .page-header-block,
html.dark .special-page-shell > .page-header-block,
html.dark .duplicate-hero .page-header-block,
html.dark body .content-surface .special-page-shell .wt-data-page-hero {
    border: 0 !important;
    background: transparent !important;
}

html.dark .inventory-page-header .page-title-standard,
html.dark .maintenance-page-shell .page-title-standard,
html.dark .unused-page-shell .page-title-standard,
html.dark .special-page-shell .page-title-standard,
html.dark .duplicate-hero .page-title-standard,
html.dark body .content-surface .special-page-shell .wt-data-page-title {
    color: #f8fafc !important;
}

html.dark .inventory-page-header .page-subtitle-standard,
html.dark .maintenance-page-shell .page-subtitle-standard,
html.dark .unused-page-shell .page-subtitle-standard,
html.dark .special-page-shell .page-subtitle-standard,
html.dark .duplicate-hero .page-subtitle-standard,
html.dark body .content-surface .special-page-shell .wt-data-page-subtitle {
    color: #aab5c7 !important;
}

html.dark .clean-admin-filter,
html.dark .unused-filter-panel,
html.dark .duplicate-search-panel,
html.dark .special-filter-panel,
html.dark body .content-surface .wt-data-filter,
html.dark .inventory-table-shell,
html.dark .clean-admin-table-shell,
html.dark .unused-table-shell,
html.dark .duplicate-table-shell,
html.dark .special-table-shell,
html.dark body .content-surface .wt-data-table {
    background: #111827 !important;
    border-color: #263244 !important;
}

html.dark .clean-admin-label,
html.dark .unused-filter-field label,
html.dark .duplicate-filter-field label,
html.dark .special-filter-field label,
html.dark body .content-surface .wt-data-field label {
    color: #94a3b8 !important;
}

html.dark .clean-admin-input,
html.dark .clean-admin-select,
html.dark .clean-admin-reset,
html.dark .unused-filter-input,
html.dark .unused-filter-select,
html.dark .unused-filter-reset,
html.dark .duplicate-search,
html.dark .duplicate-filter-select,
html.dark .duplicate-filter-reset,
html.dark .special-filter-input,
html.dark .special-filter-select,
html.dark .special-filter-reset,
html.dark body .content-surface .wt-data-input,
html.dark body .content-surface .wt-data-select,
html.dark body .content-surface .wt-data-reset,
html.dark .inventory-page-header .wt-btn,
html.dark .maintenance-page-shell > .page-header-block .wt-btn,
html.dark .unused-page-shell > .page-header-block .wt-btn,
html.dark .special-page-shell > .page-header-block .wt-btn,
html.dark .duplicate-hero .wt-btn,
html.dark body .content-surface .special-page-shell .wt-data-page-actions .wt-btn {
    background: #0f172a !important;
    border-color: #334155 !important;
    color: #e2e8f0 !important;
}

html.dark body .content-surface #walkiesTable thead th,
html.dark body .content-surface #maintenanceTable thead th,
html.dark body .content-surface .unused-table th,
html.dark body .content-surface #duplicateTable thead th,
html.dark body .content-surface #specialTable thead th,
html.dark body .content-surface .wt-data thead th {
    background: #111827 !important;
    border-color: #2b3950 !important;
    color: #dbeafe !important;
}

html.dark body .content-surface #walkiesTable tbody td,
html.dark body .content-surface #maintenanceTable tbody td,
html.dark body .content-surface .unused-table td,
html.dark body .content-surface #duplicateTable tbody td,
html.dark body .content-surface #specialTable tbody td,
html.dark body .content-surface .wt-data tbody td {
    background: #111827 !important;
    border-color: #263244 !important;
    color: #dbe4f0 !important;
}

html.dark .inventory-table-footer,
html.dark .repair-table-footer,
html.dark .unused-pagination-bar,
html.dark .duplicate-table-footer,
html.dark .special-table-footer,
html.dark body .content-surface .wt-data-footer,
html.dark body .content-surface .wt-data-scrollbar {
    background: #1f2937 !important;
    border-color: #263244 !important;
}

html.dark .inventory-table-info,
html.dark .repair-table-info,
html.dark .unused-pagination-info,
html.dark .duplicate-table-info,
html.dark .special-table-info,
html.dark body .content-surface .wt-data-info {
    color: #dbeafe !important;
}

html.dark .inventory-page-link:disabled,
html.dark .repair-page-btn:disabled,
html.dark .unused-page-btn:disabled,
html.dark .duplicate-page-link:disabled,
html.dark .special-page-link:disabled,
html.dark .adminit-page-link:disabled,
html.dark body .content-surface .wt-data .wt-data-page:disabled,
html.dark body .content-surface .inventory-page-link.opacity-30,
html.dark body .content-surface .repair-page-btn.opacity-30,
html.dark body .content-surface .unused-page-btn.opacity-30,
html.dark body .content-surface .duplicate-page-link.opacity-30,
html.dark body .content-surface .special-page-link.opacity-30 {
    background: #111827 !important;
    color: #64748b !important;
    border-color: #263244 !important;
}

body .content-surface .inventory-page-link.is-nav,
body .content-surface .repair-page-btn,
body .content-surface .unused-page-btn.nav-btn,
body .content-surface .duplicate-page-link.is-nav,
body .content-surface .special-page-link.is-nav,
body .content-surface .adminit-page-link,
body .content-surface .wt-data .wt-data-page.is-nav {
    color: #1e293b !important;
    font-weight: 900 !important;
}

body .content-surface .inventory-page-link.is-nav:disabled,
body .content-surface .repair-page-btn:disabled,
body .content-surface .unused-page-btn.nav-btn:disabled,
body .content-surface .duplicate-page-link.is-nav:disabled,
body .content-surface .special-page-link.is-nav:disabled,
body .content-surface .adminit-page-link:disabled,
body .content-surface .wt-data .wt-data-page.is-nav:disabled,
body .content-surface .inventory-page-link.is-nav.opacity-30 {
    color: #64748b !important;
    opacity: 1 !important;
    font-weight: 900 !important;
}

html.dark body .content-surface .inventory-page-link.is-nav,
html.dark body .content-surface .repair-page-btn,
html.dark body .content-surface .unused-page-btn.nav-btn,
html.dark body .content-surface .duplicate-page-link.is-nav,
html.dark body .content-surface .special-page-link.is-nav,
html.dark body .content-surface .adminit-page-link,
html.dark body .content-surface .wt-data .wt-data-page.is-nav {
    color: #dbeafe !important;
}

html.dark body .content-surface .inventory-page-link.is-nav:disabled,
html.dark body .content-surface .repair-page-btn:disabled,
html.dark body .content-surface .unused-page-btn.nav-btn:disabled,
html.dark body .content-surface .duplicate-page-link.is-nav:disabled,
html.dark body .content-surface .special-page-link.is-nav:disabled,
html.dark body .content-surface .adminit-page-link:disabled,
html.dark body .content-surface .wt-data .wt-data-page.is-nav:disabled,
html.dark body .content-surface .inventory-page-link.is-nav.opacity-30 {
    color: #94a3b8 !important;
    opacity: 1 !important;
}

/* Absolute final pass: 4 active inventory views must not shift. */
body .content-surface #walkiesTable,
body .content-surface #maintenanceTable,
body .content-surface #duplicateTable,
body .content-surface #specialTable {
    width: 100% !important;
    min-width: 100% !important;
    table-layout: fixed !important;
}

body .content-surface #walkiesTable th,
body .content-surface #maintenanceTable th,
body .content-surface #duplicateTable th,
body .content-surface #specialTable th {
    height: 48px !important;
    padding: 0 10px !important;
    overflow: hidden !important;
    color: #475569 !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    letter-spacing: 0.05em !important;
    line-height: 1.1 !important;
    text-overflow: ellipsis !important;
    text-transform: uppercase !important;
    white-space: nowrap !important;
}

body .content-surface #walkiesTable td,
body .content-surface #maintenanceTable td,
body .content-surface #duplicateTable td,
body .content-surface #specialTable td {
    height: 48px !important;
    padding: 0 10px !important;
    overflow: hidden !important;
    color: #1e293b !important;
    font-size: 14px !important;
    font-weight: 500 !important;
    line-height: 1.25 !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
}

body .content-surface #walkiesTable th:nth-child(1),
body .content-surface #walkiesTable td:nth-child(1),
body .content-surface #maintenanceTable th:nth-child(1),
body .content-surface #maintenanceTable td:nth-child(1),
body .content-surface #duplicateTable th:nth-child(1),
body .content-surface #duplicateTable td:nth-child(1),
body .content-surface #specialTable th:nth-child(1),
body .content-surface #specialTable td:nth-child(1) { width: 10% !important; }

body .content-surface #walkiesTable th:nth-child(2),
body .content-surface #walkiesTable td:nth-child(2),
body .content-surface #maintenanceTable th:nth-child(2),
body .content-surface #maintenanceTable td:nth-child(2),
body .content-surface #duplicateTable th:nth-child(2),
body .content-surface #duplicateTable td:nth-child(2),
body .content-surface #specialTable th:nth-child(2),
body .content-surface #specialTable td:nth-child(2) { width: 10% !important; }

body .content-surface #walkiesTable th:nth-child(3),
body .content-surface #walkiesTable td:nth-child(3),
body .content-surface #maintenanceTable th:nth-child(3),
body .content-surface #maintenanceTable td:nth-child(3),
body .content-surface #duplicateTable th:nth-child(3),
body .content-surface #duplicateTable td:nth-child(3),
body .content-surface #specialTable th:nth-child(3),
body .content-surface #specialTable td:nth-child(3) { width: 12% !important; }

body .content-surface #walkiesTable th:nth-child(4),
body .content-surface #walkiesTable td:nth-child(4),
body .content-surface #maintenanceTable th:nth-child(4),
body .content-surface #maintenanceTable td:nth-child(4),
body .content-surface #duplicateTable th:nth-child(4),
body .content-surface #duplicateTable td:nth-child(4),
body .content-surface #specialTable th:nth-child(4),
body .content-surface #specialTable td:nth-child(4) { width: 11% !important; }

body .content-surface #walkiesTable th:nth-child(5),
body .content-surface #walkiesTable td:nth-child(5),
body .content-surface #maintenanceTable th:nth-child(5),
body .content-surface #maintenanceTable td:nth-child(5),
body .content-surface #duplicateTable th:nth-child(5),
body .content-surface #duplicateTable td:nth-child(5),
body .content-surface #specialTable th:nth-child(5),
body .content-surface #specialTable td:nth-child(5) { width: 12% !important; }

body .content-surface #walkiesTable th:nth-child(6),
body .content-surface #walkiesTable td:nth-child(6),
body .content-surface #maintenanceTable th:nth-child(6),
body .content-surface #maintenanceTable td:nth-child(6),
body .content-surface #duplicateTable th:nth-child(6),
body .content-surface #duplicateTable td:nth-child(6),
body .content-surface #specialTable th:nth-child(6),
body .content-surface #specialTable td:nth-child(6) { width: 13% !important; }

body .content-surface #walkiesTable th:nth-child(7),
body .content-surface #walkiesTable td:nth-child(7),
body .content-surface #maintenanceTable th:nth-child(7),
body .content-surface #maintenanceTable td:nth-child(7),
body .content-surface #duplicateTable th:nth-child(7),
body .content-surface #duplicateTable td:nth-child(7),
body .content-surface #specialTable th:nth-child(7),
body .content-surface #specialTable td:nth-child(7) { width: 11% !important; }

body .content-surface #walkiesTable th:nth-child(8),
body .content-surface #walkiesTable td:nth-child(8),
body .content-surface #maintenanceTable th:nth-child(8),
body .content-surface #maintenanceTable td:nth-child(8),
body .content-surface #duplicateTable th:nth-child(8),
body .content-surface #duplicateTable td:nth-child(8),
body .content-surface #specialTable th:nth-child(8),
body .content-surface #specialTable td:nth-child(8) { width: 11% !important; }

body .content-surface #walkiesTable th:nth-child(9),
body .content-surface #walkiesTable td:nth-child(9),
body .content-surface #maintenanceTable th:nth-child(9),
body .content-surface #maintenanceTable td:nth-child(9),
body .content-surface #duplicateTable th:nth-child(9),
body .content-surface #duplicateTable td:nth-child(9),
body .content-surface #specialTable th:nth-child(9),
body .content-surface #specialTable td:nth-child(9) { width: 10% !important; }

body .content-surface #walkiesTable th:nth-child(n+10),
body .content-surface #walkiesTable td:nth-child(n+10),
body .content-surface #maintenanceTable th:nth-child(n+10),
body .content-surface #maintenanceTable td:nth-child(n+10),
body .content-surface #duplicateTable th:nth-child(n+10),
body .content-surface #duplicateTable td:nth-child(n+10),
body .content-surface #specialTable th:nth-child(n+10),
body .content-surface #specialTable td:nth-child(n+10) {
    display: table-cell !important;
}

body .content-surface .inventory-table-shell,
body .content-surface .clean-admin-table-shell,
body .content-surface .duplicate-table-shell,
body .content-surface .special-table-shell,
body .content-surface .wt-data-table {
    min-height: 0 !important;
}

body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable) {
    width: 100% !important;
    min-width: 100% !important;
    table-layout: fixed !important;
}

body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable) tr {
    height: 48px !important;
}

body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable) th {
    height: 48px !important;
    overflow: hidden !important;
    color: #475569 !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    letter-spacing: 0.05em !important;
    text-overflow: ellipsis !important;
    text-transform: uppercase !important;
    white-space: nowrap !important;
}

body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable) td {
    height: 48px !important;
    padding: 0 10px !important;
    overflow: hidden !important;
    color: #1e293b !important;
    font-size: 14px !important;
    font-weight: 500 !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
}

body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable) :is(th, td):nth-child(1) { width: 10% !important; }
body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable) :is(th, td):nth-child(2) { width: 10% !important; }
body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable) :is(th, td):nth-child(3) { width: 12% !important; }
body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable) :is(th, td):nth-child(4) { width: 11% !important; }
body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable) :is(th, td):nth-child(5) { width: 12% !important; }
body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable) :is(th, td):nth-child(6) { width: 13% !important; }
body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable) :is(th, td):nth-child(7) { width: 11% !important; }
body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable) :is(th, td):nth-child(8) { width: 11% !important; }
body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable) :is(th, td):nth-child(9) { width: 10% !important; }
body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable) :is(th, td):nth-child(n+10) { display: table-cell !important; }

/* New unified table design for all active Inventory Management pages. */
body .content-surface :is(.inventory-table-shell, .clean-admin-table-shell, .duplicate-table-shell, .special-table-shell, .wt-data-table) {
    overflow: hidden !important;
    border: 1px solid #d8e1ed !important;
    border-radius: 12px !important;
    background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%) !important;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06), 0 1px 0 rgba(255, 255, 255, 0.9) inset !important;
}

body .content-surface :is(.clean-admin-table-scroll, .duplicate-table-scroll, .special-table-scroll, .wt-data-scroll) {
    overflow-x: auto !important;
    background: #ffffff !important;
}

body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable, .wt-data table) {
    width: 100% !important;
    min-width: 100% !important;
    table-layout: fixed !important;
    border-collapse: collapse !important;
    background: #ffffff !important;
}

body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable, .wt-data table) thead tr {
    height: 44px !important;
}

body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable, .wt-data table) thead th {
    height: 44px !important;
    padding: 0 12px !important;
    border: 1px solid #e2e8f0 !important;
    background: linear-gradient(180deg, #f8fafc 0%, #edf4fb 100%) !important;
    color: #52627a !important;
    font-size: 11px !important;
    font-weight: 800 !important;
    letter-spacing: 0.08em !important;
    line-height: 1.1 !important;
    text-align: center !important;
    text-overflow: ellipsis !important;
    text-transform: uppercase !important;
    white-space: nowrap !important;
    overflow: hidden !important;
}

body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable, .wt-data table) tbody tr {
    height: 42px !important;
    background: #ffffff !important;
    transition: background-color 150ms ease, box-shadow 150ms ease !important;
}

body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable, .wt-data table) tbody td {
    height: 42px !important;
    padding: 0 12px !important;
    border: 1px solid #eef3f8 !important;
    background: #ffffff !important;
    color: #243044 !important;
    font-size: 12px !important;
    font-weight: 650 !important;
    line-height: 1.2 !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    vertical-align: middle !important;
}

body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable, .wt-data table) tbody tr:hover td {
    background: #f5faff !important;
}

body .content-surface :is(.clean-admin-table-scroll, .duplicate-table-scroll, .special-table-scroll, .wt-data-scroll, .dataTables_scrollBody) {
    scrollbar-width: thin !important;
    scrollbar-color: #60758d #e5edf5 !important;
}

body .content-surface :is(.clean-admin-table-scroll, .duplicate-table-scroll, .special-table-scroll, .wt-data-scroll, .dataTables_scrollBody)::-webkit-scrollbar {
    height: 10px !important;
}

body .content-surface :is(.clean-admin-table-scroll, .duplicate-table-scroll, .special-table-scroll, .wt-data-scroll, .dataTables_scrollBody)::-webkit-scrollbar-track,
body .content-surface .wt-data-scrollbar {
    background: #edf3f9 !important;
}

body .content-surface :is(.clean-admin-table-scroll, .duplicate-table-scroll, .special-table-scroll, .wt-data-scroll, .dataTables_scrollBody)::-webkit-scrollbar-thumb,
body .content-surface .wt-data-scrollbar-thumb {
    border-radius: 999px !important;
    background: linear-gradient(90deg, #64748b, #475569) !important;
}

body .content-surface :is(.inventory-table-footer, .repair-table-footer, .duplicate-table-footer, .special-table-footer, .wt-data-footer, .adminit-table-footer) {
    min-height: 52px !important;
    padding: 9px 14px !important;
    border-top: 1px solid #e2e8f0 !important;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 14px !important;
}

body .content-surface :is(.inventory-table-info, .repair-table-info, .duplicate-table-info, .special-table-info, .wt-data-info, .adminit-table-info) {
    color: #0f172a !important;
    font-size: 13px !important;
    font-weight: 900 !important;
    line-height: 1 !important;
    letter-spacing: 0 !important;
    text-transform: none !important;
}

body .content-surface :is(.inventory-table-pagination, .repair-table-pagination, .duplicate-table-pagination, .special-table-pagination, .wt-data-pagination, .adminit-table-pagination) {
    display: flex !important;
    align-items: center !important;
    justify-content: flex-end !important;
    gap: 10px !important;
}

body .content-surface :is(.inventory-page-link, .repair-page-btn, .repair-page-number, .duplicate-page-link, .special-page-link, .wt-data-page, .adminit-page-link, .adminit-page-current) {
    height: 34px !important;
    min-height: 34px !important;
    min-width: 42px !important;
    padding: 0 12px !important;
    border: 1px solid #d8e1ed !important;
    border-radius: 8px !important;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%) !important;
    color: #1f2a3d !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 13px !important;
    font-weight: 900 !important;
    line-height: 1 !important;
    opacity: 1 !important;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.06) !important;
}

body .content-surface :is(.inventory-page-link.is-nav, .repair-page-btn, .duplicate-page-link.is-nav, .special-page-link.is-nav, .wt-data-page.is-nav, .adminit-page-link) {
    min-width: 92px !important;
}

body .content-surface :is(.inventory-page-link:disabled, .repair-page-btn:disabled, .duplicate-page-link:disabled, .special-page-link:disabled, .wt-data-page:disabled, .adminit-page-link:disabled) {
    color: #64748b !important;
    background: #f8fafc !important;
    border-color: #e2e8f0 !important;
    cursor: not-allowed !important;
    opacity: 1 !important;
}

body .content-surface :is(.inventory-page-link.is-active, .repair-page-number.is-active, .duplicate-page-link.is-active, .special-page-link.is-active, .wt-data-page.is-active, .adminit-page-current) {
    border-color: #38bdf8 !important;
    background: linear-gradient(180deg, #e0f2fe 0%, #bae6fd 100%) !important;
    color: #075985 !important;
}

body .content-surface :is(.inventory-page-link:not(:disabled), .repair-page-btn:not(:disabled), .repair-page-number:not(:disabled), .duplicate-page-link:not(:disabled), .special-page-link:not(:disabled), .wt-data-page:not(:disabled), .adminit-page-link:not(:disabled)):hover {
    border-color: #93c5fd !important;
    background: #eff6ff !important;
    color: #1e3a8a !important;
}

html.dark body .content-surface :is(.inventory-table-shell, .clean-admin-table-shell, .duplicate-table-shell, .special-table-shell, .wt-data-table),
html.dark body .content-surface :is(.clean-admin-table-scroll, .duplicate-table-scroll, .special-table-scroll, .wt-data-scroll),
html.dark body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable, .wt-data table),
html.dark body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable, .wt-data table) tbody tr,
html.dark body .content-surface :is(.inventory-table-footer, .repair-table-footer, .duplicate-table-footer, .special-table-footer, .wt-data-footer, .adminit-table-footer) {
    background: #111827 !important;
    border-color: #263244 !important;
}

html.dark body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable, .wt-data table) thead th {
    background: linear-gradient(180deg, #1f2937 0%, #172033 100%) !important;
    border-color: #334155 !important;
    color: #dbeafe !important;
}

html.dark body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable, .wt-data table) tbody td {
    background: #111827 !important;
    border-color: #263244 !important;
    color: #dbe4f0 !important;
}

html.dark body .content-surface :is(.inventory-table-info, .repair-table-info, .duplicate-table-info, .special-table-info, .wt-data-info, .adminit-table-info) {
    color: #dbeafe !important;
}

html.dark body .content-surface :is(.inventory-page-link, .repair-page-btn, .repair-page-number, .duplicate-page-link, .special-page-link, .wt-data-page, .adminit-page-link, .adminit-page-current) {
    background: linear-gradient(180deg, #111827 0%, #0f172a 100%) !important;
    border-color: #334155 !important;
    color: #e2e8f0 !important;
}

/* Final inventory table lock: keep every Inventory Management table identical to Inventory List. */
body .content-surface :is(
    #mainTableContainer.inventory-table-shell,
    #mainTableContainer.clean-admin-table-shell,
    #mainTableContainer.duplicate-table-shell,
    #mainTableContainer.special-table-shell,
    .inventory-table-shell,
    .clean-admin-table-shell,
    .duplicate-table-shell,
    .special-table-shell,
    .wt-data-table
) {
    margin: 0 !important;
    padding: 0 !important;
    border: 1px solid #263244 !important;
    border-radius: 6px !important;
    background: #111827 !important;
    box-shadow: none !important;
    overflow: hidden !important;
}

body .content-surface :is(
    #inventoryTableScroll.clean-admin-table-scroll,
    .clean-admin-table-scroll,
    #duplicateTableScroll.duplicate-table-scroll,
    .duplicate-table-scroll,
    #specialTableScroll.special-table-scroll,
    .special-table-scroll,
    .wt-data-scroll,
    .dataTables_scrollBody
) {
    overflow-x: hidden !important;
    background: #111827 !important;
    scrollbar-width: none !important;
    scrollbar-color: transparent transparent !important;
    -ms-overflow-style: none !important;
}

body .content-surface :is(
    #inventoryTableScroll.clean-admin-table-scroll,
    .clean-admin-table-scroll,
    #duplicateTableScroll.duplicate-table-scroll,
    .duplicate-table-scroll,
    #specialTableScroll.special-table-scroll,
    .special-table-scroll,
    .wt-data-scroll,
    .dataTables_scrollBody
)::-webkit-scrollbar {
    width: 0 !important;
    height: 0 !important;
    display: none !important;
}

body .content-surface :is(
    #inventoryTableScroll.clean-admin-table-scroll,
    .clean-admin-table-scroll,
    #duplicateTableScroll.duplicate-table-scroll,
    .duplicate-table-scroll,
    #specialTableScroll.special-table-scroll,
    .special-table-scroll,
    .wt-data-scroll,
    .dataTables_scrollBody
)::-webkit-scrollbar-track,
body .content-surface .wt-data-scrollbar {
    display: none !important;
    width: 0 !important;
    height: 0 !important;
    background: transparent !important;
}

body .content-surface :is(
    #inventoryTableScroll.clean-admin-table-scroll,
    .clean-admin-table-scroll,
    #duplicateTableScroll.duplicate-table-scroll,
    .duplicate-table-scroll,
    #specialTableScroll.special-table-scroll,
    .special-table-scroll,
    .wt-data-scroll,
    .dataTables_scrollBody
)::-webkit-scrollbar-thumb,
body .content-surface .wt-data-scrollbar-thumb {
    display: none !important;
    width: 0 !important;
    height: 0 !important;
    border-radius: 999px !important;
    background: transparent !important;
}

body .content-surface :is(
    #walkiesTable.clean-admin-table,
    #maintTable.clean-admin-table,
    #maintenanceTable.clean-admin-table,
    #duplicateTable,
    #specialTable,
    .wt-data table
) {
    width: 100% !important;
    min-width: 100% !important;
    margin: 0 !important;
    border: 0 !important;
    border-collapse: collapse !important;
    table-layout: fixed !important;
}

body .content-surface :is(
    #walkiesTable.clean-admin-table,
    #maintTable.clean-admin-table,
    #maintenanceTable.clean-admin-table,
    #duplicateTable,
    #specialTable,
    .wt-data table
) thead th {
    height: 28px !important;
    padding: 5px 8px !important;
    border: 1px solid #2f3b4f !important;
    background: #1f2937 !important;
    color: #cbd5e1 !important;
    font-size: 8px !important;
    font-weight: 600 !important;
    line-height: 1.1 !important;
    letter-spacing: 0.05em !important;
    text-align: center !important;
    text-transform: uppercase !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
    overflow: hidden !important;
}

body .content-surface :is(
    #walkiesTable.clean-admin-table,
    #maintTable.clean-admin-table,
    #maintenanceTable.clean-admin-table,
    #duplicateTable,
    #specialTable,
    .wt-data table
) tbody td {
    height: 30px !important;
    padding: 5px 8px !important;
    border: 1px solid #263244 !important;
    background: #111827 !important;
    color: #dbe4f0 !important;
    font-size: 9px !important;
    font-weight: 400 !important;
    line-height: 1.25 !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    vertical-align: middle !important;
}

body .content-surface :is(
    #walkiesTable.clean-admin-table,
    #maintTable.clean-admin-table,
    #maintenanceTable.clean-admin-table,
    #duplicateTable,
    #specialTable,
    .wt-data table
) tbody tr:hover td {
    background: #172033 !important;
}

body .content-surface :is(
    #walkiesTable.clean-admin-table,
    #maintTable.clean-admin-table,
    #maintenanceTable.clean-admin-table,
    #duplicateTable,
    #specialTable,
    .wt-data table
) :is(th, td):nth-child(1) { width: 10% !important; }
body .content-surface :is(#walkiesTable.clean-admin-table, #maintTable.clean-admin-table, #maintenanceTable.clean-admin-table, #duplicateTable, #specialTable, .wt-data table) :is(th, td):nth-child(2) { width: 10% !important; }
body .content-surface :is(#walkiesTable.clean-admin-table, #maintTable.clean-admin-table, #maintenanceTable.clean-admin-table, #duplicateTable, #specialTable, .wt-data table) :is(th, td):nth-child(3) { width: 12% !important; }
body .content-surface :is(#walkiesTable.clean-admin-table, #maintTable.clean-admin-table, #maintenanceTable.clean-admin-table, #duplicateTable, #specialTable, .wt-data table) :is(th, td):nth-child(4) { width: 11% !important; }
body .content-surface :is(#walkiesTable.clean-admin-table, #maintTable.clean-admin-table, #maintenanceTable.clean-admin-table, #duplicateTable, #specialTable, .wt-data table) :is(th, td):nth-child(5) { width: 12% !important; }
body .content-surface :is(#walkiesTable.clean-admin-table, #maintTable.clean-admin-table, #maintenanceTable.clean-admin-table, #duplicateTable, #specialTable, .wt-data table) :is(th, td):nth-child(6) { width: 13% !important; }
body .content-surface :is(#walkiesTable.clean-admin-table, #maintTable.clean-admin-table, #maintenanceTable.clean-admin-table, #duplicateTable, #specialTable, .wt-data table) :is(th, td):nth-child(7) { width: 11% !important; }
body .content-surface :is(#walkiesTable.clean-admin-table, #maintTable.clean-admin-table, #maintenanceTable.clean-admin-table, #duplicateTable, #specialTable, .wt-data table) :is(th, td):nth-child(8) { width: 11% !important; }
body .content-surface :is(#walkiesTable.clean-admin-table, #maintTable.clean-admin-table, #maintenanceTable.clean-admin-table, #duplicateTable, #specialTable, .wt-data table) :is(th, td):nth-child(9) { width: 10% !important; }
body .content-surface :is(#walkiesTable.clean-admin-table, #maintTable.clean-admin-table, #maintenanceTable.clean-admin-table, #duplicateTable, #specialTable, .wt-data table) :is(th, td):nth-child(n+10) { display: table-cell !important; }

body .content-surface :is(.inventory-table-footer, .repair-table-footer, .duplicate-table-footer, .special-table-footer, .wt-data-footer, .adminit-table-footer) {
    min-height: 64px !important;
    padding: 10px 18px !important;
    border-top: 1px solid #263244 !important;
    background: #111827 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 16px !important;
}

body .content-surface :is(.inventory-table-info, .repair-table-info, .duplicate-table-info, .special-table-info, .wt-data-info, .adminit-table-info) {
    color: #dbeafe !important;
    font-size: 8px !important;
    font-weight: 700 !important;
    line-height: 1 !important;
    letter-spacing: 0 !important;
    text-transform: none !important;
    white-space: nowrap !important;
}

body .content-surface :is(.inventory-table-pagination, .repair-table-pagination, .duplicate-table-pagination, .special-table-pagination, .unused-pagination-actions, .wt-data-pagination, .adminit-table-pagination) {
    display: flex !important;
    align-items: center !important;
    justify-content: flex-end !important;
    gap: 10px !important;
}

body .content-surface :is(.inventory-page-link, .repair-page-btn, .repair-page-number, .duplicate-page-link, .special-page-link, .unused-page-btn, .wt-data-page, .adminit-page-link, .adminit-page-current) {
    height: 34px !important;
    min-height: 34px !important;
    min-width: 38px !important;
    padding: 0 10px !important;
    border: 1px solid #2f4d74 !important;
    border-radius: 7px !important;
    background: #0f172a !important;
    color: #bfdbfe !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 12px !important;
    font-weight: 900 !important;
    line-height: 1 !important;
    opacity: 1 !important;
    box-shadow: none !important;
}

body .content-surface :is(.inventory-page-link.is-nav, .repair-page-btn, .duplicate-page-link.is-nav, .special-page-link.is-nav, .unused-page-btn.nav-btn, .wt-data-page.is-nav, .adminit-page-link) {
    min-width: 92px !important;
    color: #cbd5e1 !important;
    font-size: 13px !important;
    font-weight: 900 !important;
}

html:not(.dark) body .content-surface :is(#mainTableContainer.inventory-table-shell, #mainTableContainer.clean-admin-table-shell, #mainTableContainer.duplicate-table-shell, #mainTableContainer.special-table-shell, .inventory-table-shell, .clean-admin-table-shell, .duplicate-table-shell, .special-table-shell, .wt-data-table),
html:not(.dark) body .content-surface :is(.inventory-table-footer, .repair-table-footer, .duplicate-table-footer, .special-table-footer, .wt-data-footer, .adminit-table-footer),
html:not(.dark) body .content-surface :is(#inventoryTableScroll.clean-admin-table-scroll, .clean-admin-table-scroll, #duplicateTableScroll.duplicate-table-scroll, .duplicate-table-scroll, #specialTableScroll.special-table-scroll, .special-table-scroll, .wt-data-scroll, .dataTables_scrollBody) {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
}

html:not(.dark) body .content-surface :is(#walkiesTable.clean-admin-table, #maintTable.clean-admin-table, #maintenanceTable.clean-admin-table, #duplicateTable, #specialTable, .wt-data table) thead th {
    border-color: #d8e1ed !important;
    background: #f8fafc !important;
    color: #526781 !important;
}

html:not(.dark) body .content-surface :is(#walkiesTable.clean-admin-table, #maintTable.clean-admin-table, #maintenanceTable.clean-admin-table, #duplicateTable, #specialTable, .wt-data table) tbody td {
    border-color: #e2e8f0 !important;
    background: #ffffff !important;
    color: #1f2937 !important;
}

html:not(.dark) body .content-surface :is(#walkiesTable.clean-admin-table, #maintTable.clean-admin-table, #maintenanceTable.clean-admin-table, #duplicateTable, #specialTable, .wt-data table) tbody tr:hover td {
    background: #f8fafc !important;
}

html:not(.dark) body .content-surface :is(.inventory-table-info, .repair-table-info, .duplicate-table-info, .special-table-info, .wt-data-info, .adminit-table-info) {
    color: #334155 !important;
}

html:not(.dark) body .content-surface :is(.inventory-page-link, .repair-page-btn, .repair-page-number, .duplicate-page-link, .special-page-link, .unused-page-btn, .wt-data-page, .adminit-page-link, .adminit-page-current) {
    border-color: #cbd5e1 !important;
    background: #ffffff !important;
    color: #334155 !important;
}

html:not(.dark) body .content-surface :is(.inventory-page-link.is-active, .repair-page-number.is-active, .duplicate-page-link.is-active, .special-page-link.is-active, .unused-page-btn.active, .wt-data-page.is-active, .adminit-page-current) {
    border-color: #60a5fa !important;
    background: #dbeafe !important;
    color: #1e3a8a !important;
}

body .content-surface :is(
    .inventory-page-shell,
    .maintenance-page-shell,
    .unused-page-shell,
    .duplicate-page,
    .special-page-shell
) {
    display: grid !important;
    gap: 9px !important;
    width: 100% !important;
    max-width: none !important;
    margin: 0 !important;
    padding: 0 !important;
}

body .content-surface :is(
    .inventory-page-header,
    .maintenance-page-shell > .page-header-block,
    .unused-page-shell > .page-header-block,
    .special-page-shell > .page-header-block,
    .duplicate-hero,
    .duplicate-hero .page-header-block,
    .special-page-shell .wt-data-page-hero,
    .wt-data-page-hero,
    .clean-admin-filter,
    .unused-filter-panel,
    .duplicate-search-panel,
    .special-filter-panel,
    .wt-data-filter,
    .inventory-summary-grid,
    .special-summary-grid
) {
    margin-top: 0 !important;
    margin-bottom: 0 !important;
}

body .content-surface :is(
    .inventory-page-header,
    .maintenance-page-shell > .page-header-block,
    .unused-page-shell > .page-header-block,
    .special-page-shell > .page-header-block,
    .duplicate-hero .page-header-block,
    .special-page-shell .wt-data-page-hero,
    .wt-data-page-hero
) {
    border-left-width: 6px !important;
    border-left-style: solid !important;
    border-left-color: #f2c48d !important;
}

body .content-surface :is(
    .inventory-page-header .page-title-standard,
    .maintenance-page-shell .page-title-standard,
    .unused-page-shell .page-title-standard,
    .special-page-shell .page-title-standard,
    .duplicate-hero .page-title-standard,
    .special-page-shell .wt-data-page-title,
    .wt-data-page-title
) {
    font-size: 15px !important;
    font-weight: 900 !important;
    line-height: 1.05 !important;
    letter-spacing: 0 !important;
    margin: 0 0 4px !important;
}

body .content-surface :is(
    .inventory-page-header .page-subtitle-standard,
    .maintenance-page-shell .page-subtitle-standard,
    .unused-page-shell .page-subtitle-standard,
    .special-page-shell .page-subtitle-standard,
    .duplicate-hero .page-subtitle-standard,
    .special-page-shell .wt-data-page-subtitle,
    .wt-data-page-subtitle
) {
    font-size: 7px !important;
    font-weight: 900 !important;
    line-height: 1.2 !important;
    letter-spacing: 0.2em !important;
    margin: 0 !important;
    text-transform: uppercase !important;
}

body .content-surface :is(
    .inventory-page-header,
    .maintenance-page-shell > .page-header-block,
    .unused-page-shell > .page-header-block,
    .special-page-shell > .page-header-block,
    .duplicate-hero .page-header-block,
    .special-page-shell .wt-data-page-hero,
    .wt-data-page-hero
) {
    min-height: 58px !important;
    padding: 10px 16px !important;
    border-radius: 10px !important;
}

body .content-surface :is(
    .inventory-page-header .wt-btn,
    .maintenance-page-shell > .page-header-block .wt-btn,
    .unused-page-shell > .page-header-block .wt-btn,
    .special-page-shell > .page-header-block .wt-btn,
    .duplicate-hero .wt-btn,
    .wt-data-page-actions .wt-btn,
    #mainTableContainer .wt-btn
) {
    height: 28px !important;
    min-height: 28px !important;
    min-width: 86px !important;
    padding: 0 10px !important;
    border-radius: 7px !important;
    font-size: 9px !important;
    font-weight: 800 !important;
    line-height: 1 !important;
}

body .content-surface :is(
    .clean-admin-filter,
    .unused-filter-panel,
    .duplicate-search-panel,
    .special-filter-panel,
    .wt-data-filter
) {
    padding: 7px 10px !important;
    border-radius: 8px !important;
    gap: 8px !important;
    align-items: end !important;
}

body .content-surface :is(
    .clean-admin-filter-grid,
    .duplicate-search-panel,
    .special-filter-panel,
    .wt-data-filter
) {
    display: grid !important;
    grid-template-columns: minmax(260px, 1fr) 180px 74px !important;
    gap: 10px !important;
    align-items: end !important;
}

body .content-surface :is(
    .clean-admin-label,
    .unused-filter-field label,
    .duplicate-filter-field label,
    .special-filter-field label,
    .wt-data-field label
) {
    margin-bottom: 4px !important;
    font-size: 7px !important;
    font-weight: 800 !important;
    letter-spacing: 0.1em !important;
    line-height: 1 !important;
}

body .content-surface :is(
    .clean-admin-input,
    .clean-admin-select,
    .clean-admin-reset,
    .unused-filter-input,
    .unused-filter-select,
    .unused-filter-reset,
    .duplicate-search,
    .duplicate-filter-select,
    .duplicate-filter-reset,
    .special-filter-input,
    .special-filter-select,
    .special-filter-reset,
    .wt-data-input,
    .wt-data-select,
    .wt-data-reset
) {
    height: 28px !important;
    min-height: 28px !important;
    border-radius: 7px !important;
    font-size: 9px !important;
    font-weight: 600 !important;
    line-height: 1 !important;
}

body .content-surface :is(
    .clean-admin-reset,
    .unused-filter-reset,
    .duplicate-filter-reset,
    .special-filter-reset,
    .wt-data-reset
) {
    width: 74px !important;
    min-width: 74px !important;
    padding: 0 8px !important;
    font-size: 9px !important;
    font-weight: 800 !important;
}

@media (max-width: 900px) {
    body .content-surface :is(
        .clean-admin-filter-grid,
        .duplicate-search-panel,
        .special-filter-panel,
        .wt-data-filter
    ) {
        grid-template-columns: 1fr !important;
    }

    body .content-surface :is(
        .clean-admin-reset,
        .unused-filter-reset,
        .duplicate-filter-reset,
        .special-filter-reset,
        .wt-data-reset
    ) {
        width: 100% !important;
    }
}

body .content-surface :is(
    .inventory-table-footer,
    .repair-table-footer,
    .unused-pagination-bar,
    .duplicate-table-footer,
    .special-table-footer,
    .wt-data-footer,
    .adminit-table-footer
) {
    min-height: 64px !important;
    padding: 10px 18px !important;
    gap: 16px !important;
}

body .content-surface :is(
    .inventory-table-pagination,
    .repair-table-pagination,
    .unused-pagination-actions,
    .duplicate-table-pagination,
    .special-table-pagination,
    .wt-data-pagination,
    .adminit-table-pagination
) {
    display: flex !important;
    align-items: center !important;
    justify-content: flex-end !important;
    gap: 10px !important;
}

body .content-surface :is(
    .inventory-page-link,
    .repair-page-btn,
    .repair-page-number,
    .unused-page-btn,
    .duplicate-page-link,
    .special-page-link,
    .wt-data-page,
    .adminit-page-link,
    .adminit-page-current
) {
    width: auto !important;
    height: 34px !important;
    min-height: 34px !important;
    min-width: 38px !important;
    padding: 0 10px !important;
    border-radius: 7px !important;
    font-size: 12px !important;
    font-weight: 900 !important;
    line-height: 1 !important;
    text-transform: none !important;
}

body .content-surface :is(
    .inventory-page-link.is-nav,
    .repair-page-btn,
    .unused-page-btn.nav-btn,
    .duplicate-page-link.is-nav,
    .special-page-link.is-nav,
    .wt-data-page.is-nav,
    .adminit-page-link
) {
    width: auto !important;
    min-width: 92px !important;
    height: 34px !important;
    padding: 0 10px !important;
    font-size: 13px !important;
    font-weight: 900 !important;
}

/* Final shared minimalist inventory page polish. */
body .content-surface:has(.inventory-management-ui),
body .content-surface:has(.inventory-page-shell),
body .content-surface:has(.maintenance-page-shell),
body .content-surface:has(.special-page-shell),
body .content-surface:has(.duplicate-page) {
    background: #0b1220 !important;
    padding: 10px !important;
}

body .content-surface :is(
    .inventory-page-shell,
    .maintenance-page-shell,
    .special-page-shell,
    .duplicate-page
) {
    gap: 12px !important;
    padding: 0 !important;
}

body .content-surface :is(
    .inventory-page-header,
    .maintenance-page-shell > .page-header-block,
    .special-page-shell > .page-header-block,
    .duplicate-hero .page-header-block,
    .special-page-shell .wt-data-page-hero,
    .wt-data-page-hero
) {
    min-height: 0 !important;
    margin: 0 !important;
    padding: 0 2px 10px !important;
    border: 0 !important;
    border-left: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
    overflow: visible !important;
}

body .content-surface :is(
    .inventory-page-header .page-title-standard,
    .maintenance-page-shell .page-title-standard,
    .special-page-shell .page-title-standard,
    .duplicate-hero .page-title-standard,
    .special-page-shell .wt-data-page-title,
    .wt-data-page-title
) {
    margin: 0 !important;
    color: #f8fafc !important;
    font-size: 19px !important;
    font-weight: 900 !important;
    line-height: 1.1 !important;
    letter-spacing: 0 !important;
    text-transform: none !important;
}

body .content-surface :is(
    .inventory-page-header .page-subtitle-standard,
    .maintenance-page-shell .page-subtitle-standard,
    .special-page-shell .page-subtitle-standard,
    .duplicate-hero .page-subtitle-standard,
    .special-page-shell .wt-data-page-subtitle,
    .wt-data-page-subtitle
) {
    max-width: 560px !important;
    margin-top: 5px !important;
    color: #93a4ba !important;
    font-size: 9px !important;
    font-weight: 900 !important;
    line-height: 1.45 !important;
    letter-spacing: .16em !important;
}

body .content-surface :is(
    .inventory-header-actions,
    .maintenance-page-shell > .page-header-block > div:last-child,
    .special-page-shell > .page-header-block > div:last-child,
    .duplicate-hero .page-header-block > div:last-child,
    .wt-data-page-actions
) {
    gap: 8px !important;
}

body .content-surface :is(
    .inventory-page-header .wt-btn,
    .maintenance-page-shell > .page-header-block .wt-btn,
    .special-page-shell > .page-header-block .wt-btn,
    .duplicate-hero .wt-btn,
    .wt-data-page-actions .wt-btn
) {
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
    letter-spacing: 0 !important;
    box-shadow: none !important;
}

body .content-surface :is(
    .inventory-page-header .wt-btn:hover,
    .maintenance-page-shell > .page-header-block .wt-btn:hover,
    .special-page-shell > .page-header-block .wt-btn:hover,
    .duplicate-hero .wt-btn:hover,
    .wt-data-page-actions .wt-btn:hover
) {
    border-color: rgba(56, 189, 248, .42) !important;
    background: #162033 !important;
    color: #ffffff !important;
    transform: none !important;
}

body .content-surface :is(
    .inventory-summary-grid,
    .special-summary-grid,
    .duplicate-summary-grid,
    .duplicate-guide
) {
    display: none !important;
}

body .content-surface :is(
    .clean-admin-filter,
    .special-filter-panel,
    .duplicate-search-panel,
    .unused-filter-panel,
    .wt-data-filter
) {
    margin: 0 !important;
    padding: 12px !important;
    border: 1px solid rgba(148, 163, 184, .18) !important;
    border-radius: 14px !important;
    background: #0f172a !important;
    box-shadow: none !important;
}

body .content-surface :is(
    .clean-admin-filter-grid,
    .special-filter-panel,
    .duplicate-search-panel,
    .wt-data-filter
) {
    grid-template-columns: minmax(240px, 1fr) 180px auto !important;
    gap: 10px !important;
}

body .content-surface :is(
    .clean-admin-label,
    .special-filter-field label,
    .duplicate-filter-field label,
    .unused-filter-field label,
    .wt-data-field label
) {
    margin-bottom: 6px !important;
    color: #8ea0b8 !important;
    font-size: 9px !important;
    letter-spacing: .12em !important;
}

body .content-surface :is(
    .clean-admin-input,
    .clean-admin-select,
    .clean-admin-reset,
    .special-filter-input,
    .special-filter-select,
    .special-filter-reset,
    .duplicate-search,
    .duplicate-filter-select,
    .duplicate-filter-reset,
    .unused-filter-input,
    .unused-filter-select,
    .unused-filter-reset,
    .wt-data-input,
    .wt-data-select,
    .wt-data-reset
) {
    height: 38px !important;
    min-height: 38px !important;
    border-radius: 10px !important;
    border: 1px solid rgba(148, 163, 184, .26) !important;
    background: #111827 !important;
    color: #e5edf7 !important;
    font-size: 12px !important;
    font-weight: 750 !important;
}

body .content-surface :is(
    .clean-admin-reset,
    .special-filter-reset,
    .duplicate-filter-reset,
    .unused-filter-reset,
    .wt-data-reset
) {
    width: auto !important;
    min-width: 82px !important;
    padding: 0 18px !important;
    background: transparent !important;
    color: #dbeafe !important;
}

body .content-surface :is(
    .inventory-table-shell,
    .clean-admin-table-shell,
    .special-table-shell,
    .duplicate-table-shell,
    .unused-table-shell,
    .wt-data-table
) {
    border-radius: 14px !important;
    border: 1px solid rgba(148, 163, 184, .18) !important;
    background: #0f172a !important;
    box-shadow: none !important;
}

body .content-surface :is(
    #walkiesTable,
    #maintTable,
    #maintenanceTable,
    #specialTable,
    #duplicateTable,
    .clean-admin-table,
    .unused-table,
    .wt-data table
) thead th {
    height: 42px !important;
    padding: 0 13px !important;
    border-color: rgba(148, 163, 184, .14) !important;
    background: #172033 !important;
    color: #d7e7fb !important;
    font-size: 10px !important;
    letter-spacing: .07em !important;
}

body .content-surface :is(
    #walkiesTable,
    #maintTable,
    #maintenanceTable,
    #specialTable,
    #duplicateTable,
    .clean-admin-table,
    .unused-table,
    .wt-data table
) tbody td {
    height: 38px !important;
    min-height: 38px !important;
    padding: 8px 13px !important;
    border-color: rgba(148, 163, 184, .12) !important;
    background: #0f172a !important;
    color: #dbe4f0 !important;
    font-size: 11px !important;
    font-weight: 650 !important;
}

body .content-surface :is(
    #walkiesTable,
    #maintenanceTable,
    #specialTable,
    #duplicateTable,
    .clean-admin-table,
    .unused-table,
    .wt-data table
) tbody tr:hover td {
    background: #152033 !important;
}

body .content-surface :is(
    .inventory-table-footer,
    .repair-table-footer,
    .unused-pagination-bar,
    .duplicate-table-footer,
    .special-table-footer,
    .wt-data-footer,
    .adminit-table-footer
) {
    min-height: 46px !important;
    padding: 8px 12px !important;
    border-top: 1px solid rgba(148, 163, 184, .14) !important;
    background: #0f172a !important;
}

body .content-surface :is(
    .inventory-table-info,
    .repair-table-info,
    .unused-pagination-info,
    .duplicate-table-info,
    .special-table-info,
    .wt-data-info,
    .adminit-table-info
) {
    color: #93a4ba !important;
    font-size: 11px !important;
    letter-spacing: .04em !important;
}

@media (max-width: 767px) {
    body .content-surface:has(.inventory-management-ui),
    body .content-surface:has(.inventory-page-shell),
    body .content-surface:has(.maintenance-page-shell),
    body .content-surface:has(.special-page-shell),
    body .content-surface:has(.duplicate-page) {
        padding: 12px !important;
    }

    body .content-surface :is(
        .inventory-page-header,
        .maintenance-page-shell > .page-header-block,
        .special-page-shell > .page-header-block,
        .duplicate-hero .page-header-block,
        .wt-data-page-hero
    ) {
        gap: 12px !important;
    }

    body .content-surface :is(
        .inventory-header-actions,
        .maintenance-page-shell > .page-header-block > div:last-child,
        .special-page-shell > .page-header-block > div:last-child,
        .duplicate-hero .page-header-block > div:last-child,
        .wt-data-page-actions
    ) {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        width: 100% !important;
    }

    body .content-surface :is(
        .inventory-page-header .wt-btn,
        .maintenance-page-shell > .page-header-block .wt-btn,
        .special-page-shell > .page-header-block .wt-btn,
        .duplicate-hero .wt-btn,
        .wt-data-page-actions .wt-btn
    ) {
        width: 100% !important;
        min-width: 0 !important;
    }

    body .content-surface :is(
        .clean-admin-filter-grid,
        .special-filter-panel,
        .duplicate-search-panel,
        .wt-data-filter
    ) {
        grid-template-columns: 1fr !important;
    }

    body .content-surface :is(
        .clean-admin-reset,
        .special-filter-reset,
        .duplicate-filter-reset,
        .unused-filter-reset,
        .wt-data-reset
    ) {
        width: 100% !important;
    }
}

html:not(.dark) body .content-surface:has(.inventory-management-ui),
html:not(.dark) body .content-surface:has(.inventory-page-shell),
html:not(.dark) body .content-surface:has(.maintenance-page-shell),
html:not(.dark) body .content-surface:has(.special-page-shell),
html:not(.dark) body .content-surface:has(.duplicate-page),
html[data-theme="light"] body .content-surface:has(.inventory-management-ui),
html[data-theme="light"] body .content-surface:has(.inventory-page-shell),
html[data-theme="light"] body .content-surface:has(.maintenance-page-shell),
html[data-theme="light"] body .content-surface:has(.special-page-shell),
html[data-theme="light"] body .content-surface:has(.duplicate-page) {
    background: #f4f7fb !important;
}

html:not(.dark) body .content-surface :is(
    .inventory-page-header .page-title-standard,
    .maintenance-page-shell .page-title-standard,
    .special-page-shell .page-title-standard,
    .duplicate-hero .page-title-standard,
    .wt-data-page-title
),
html[data-theme="light"] body .content-surface :is(
    .inventory-page-header .page-title-standard,
    .maintenance-page-shell .page-title-standard,
    .special-page-shell .page-title-standard,
    .duplicate-hero .page-title-standard,
    .wt-data-page-title
) {
    color: #172033 !important;
}

html:not(.dark) body .content-surface :is(
    .inventory-page-header .page-subtitle-standard,
    .maintenance-page-shell .page-subtitle-standard,
    .special-page-shell .page-subtitle-standard,
    .duplicate-hero .page-subtitle-standard,
    .wt-data-page-subtitle,
    .clean-admin-label,
    .special-filter-field label,
    .duplicate-filter-field label,
    .unused-filter-field label,
    .wt-data-field label
),
html[data-theme="light"] body .content-surface :is(
    .inventory-page-header .page-subtitle-standard,
    .maintenance-page-shell .page-subtitle-standard,
    .special-page-shell .page-subtitle-standard,
    .duplicate-hero .page-subtitle-standard,
    .wt-data-page-subtitle,
    .clean-admin-label,
    .special-filter-field label,
    .duplicate-filter-field label,
    .unused-filter-field label,
    .wt-data-field label
) {
    color: #64748b !important;
}

html:not(.dark) body .content-surface :is(
    .inventory-page-header .wt-btn,
    .maintenance-page-shell > .page-header-block .wt-btn,
    .special-page-shell > .page-header-block .wt-btn,
    .duplicate-hero .wt-btn,
    .wt-data-page-actions .wt-btn,
    .clean-admin-filter,
    .special-filter-panel,
    .duplicate-search-panel,
    .unused-filter-panel,
    .inventory-table-shell,
    .clean-admin-table-shell,
    .special-table-shell,
    .duplicate-table-shell,
    .unused-table-shell
),
html[data-theme="light"] body .content-surface :is(
    .inventory-page-header .wt-btn,
    .maintenance-page-shell > .page-header-block .wt-btn,
    .special-page-shell > .page-header-block .wt-btn,
    .duplicate-hero .wt-btn,
    .wt-data-page-actions .wt-btn,
    .clean-admin-filter,
    .special-filter-panel,
    .duplicate-search-panel,
    .unused-filter-panel,
    .inventory-table-shell,
    .clean-admin-table-shell,
    .special-table-shell,
    .duplicate-table-shell,
    .unused-table-shell
) {
    border-color: #d8e1ed !important;
    background: #ffffff !important;
    color: #172033 !important;
}

html:not(.dark) body .content-surface :is(
    .clean-admin-input,
    .clean-admin-select,
    .clean-admin-reset,
    .special-filter-input,
    .special-filter-select,
    .special-filter-reset,
    .duplicate-search,
    .duplicate-filter-select,
    .duplicate-filter-reset,
    .unused-filter-input,
    .unused-filter-select,
    .unused-filter-reset,
    .wt-data-input,
    .wt-data-select,
    .wt-data-reset
),
html[data-theme="light"] body .content-surface :is(
    .clean-admin-input,
    .clean-admin-select,
    .clean-admin-reset,
    .special-filter-input,
    .special-filter-select,
    .special-filter-reset,
    .duplicate-search,
    .duplicate-filter-select,
    .duplicate-filter-reset,
    .unused-filter-input,
    .unused-filter-select,
    .unused-filter-reset,
    .wt-data-input,
    .wt-data-select,
    .wt-data-reset
) {
    border-color: #cbd5e1 !important;
    background: #f8fafc !important;
    color: #172033 !important;
}

html:not(.dark) body .content-surface :is(
    #walkiesTable,
    #maintTable,
    #maintenanceTable,
    #specialTable,
    #duplicateTable,
    .clean-admin-table,
    .unused-table,
    .wt-data table
) thead th,
html[data-theme="light"] body .content-surface :is(
    #walkiesTable,
    #maintTable,
    #maintenanceTable,
    #specialTable,
    #duplicateTable,
    .clean-admin-table,
    .unused-table,
    .wt-data table
) thead th {
    border-color: #d8e1ed !important;
    background: #eef3f8 !important;
    color: #526781 !important;
}

html:not(.dark) body .content-surface :is(
    #walkiesTable,
    #maintTable,
    #maintenanceTable,
    #specialTable,
    #duplicateTable,
    .clean-admin-table,
    .unused-table,
    .wt-data table
) tbody td,
html:not(.dark) body .content-surface :is(
    .inventory-table-footer,
    .repair-table-footer,
    .unused-pagination-bar,
    .duplicate-table-footer,
    .special-table-footer,
    .wt-data-footer,
    .adminit-table-footer
),
html[data-theme="light"] body .content-surface :is(
    #walkiesTable,
    #maintTable,
    #maintenanceTable,
    #specialTable,
    #duplicateTable,
    .clean-admin-table,
    .unused-table,
    .wt-data table
) tbody td,
html[data-theme="light"] body .content-surface :is(
    .inventory-table-footer,
    .repair-table-footer,
    .unused-pagination-bar,
    .duplicate-table-footer,
    .special-table-footer,
    .wt-data-footer,
    .adminit-table-footer
) {
    border-color: #e2e8f0 !important;
    background: #ffffff !important;
    color: #1f2937 !important;
}

html.dark body .content-surface:has(.inventory-management-ui),
html.dark body .content-surface:has(.inventory-page-shell),
html.dark body .content-surface:has(.maintenance-page-shell),
html.dark body .content-surface:has(.special-page-shell),
html.dark body .content-surface:has(.duplicate-page),
html[data-theme="dark"] body .content-surface:has(.inventory-management-ui),
html[data-theme="dark"] body .content-surface:has(.inventory-page-shell),
html[data-theme="dark"] body .content-surface:has(.maintenance-page-shell),
html[data-theme="dark"] body .content-surface:has(.special-page-shell),
html[data-theme="dark"] body .content-surface:has(.duplicate-page) {
    background: #0b1220 !important;
}

html.dark body .content-surface :is(
    .inventory-page-header .page-title-standard,
    .maintenance-page-shell .page-title-standard,
    .special-page-shell .page-title-standard,
    .duplicate-hero .page-title-standard,
    .wt-data-page-title
),
html[data-theme="dark"] body .content-surface :is(
    .inventory-page-header .page-title-standard,
    .maintenance-page-shell .page-title-standard,
    .special-page-shell .page-title-standard,
    .duplicate-hero .page-title-standard,
    .wt-data-page-title
) {
    color: #f8fafc !important;
}

html.dark body .content-surface :is(
    .inventory-page-header .page-subtitle-standard,
    .maintenance-page-shell .page-subtitle-standard,
    .special-page-shell .page-subtitle-standard,
    .duplicate-hero .page-subtitle-standard,
    .wt-data-page-subtitle,
    .clean-admin-label,
    .special-filter-field label,
    .duplicate-filter-field label,
    .unused-filter-field label,
    .wt-data-field label
),
html[data-theme="dark"] body .content-surface :is(
    .inventory-page-header .page-subtitle-standard,
    .maintenance-page-shell .page-subtitle-standard,
    .special-page-shell .page-subtitle-standard,
    .duplicate-hero .page-subtitle-standard,
    .wt-data-page-subtitle,
    .clean-admin-label,
    .special-filter-field label,
    .duplicate-filter-field label,
    .unused-filter-field label,
    .wt-data-field label
) {
    color: #93a4ba !important;
}

html.dark body .content-surface :is(
    .inventory-page-header .wt-btn,
    .maintenance-page-shell > .page-header-block .wt-btn,
    .special-page-shell > .page-header-block .wt-btn,
    .duplicate-hero .wt-btn,
    .wt-data-page-actions .wt-btn,
    .clean-admin-filter,
    .special-filter-panel,
    .duplicate-search-panel,
    .unused-filter-panel,
    .inventory-table-shell,
    .clean-admin-table-shell,
    .special-table-shell,
    .duplicate-table-shell,
    .unused-table-shell
),
html[data-theme="dark"] body .content-surface :is(
    .inventory-page-header .wt-btn,
    .maintenance-page-shell > .page-header-block .wt-btn,
    .special-page-shell > .page-header-block .wt-btn,
    .duplicate-hero .wt-btn,
    .wt-data-page-actions .wt-btn,
    .clean-admin-filter,
    .special-filter-panel,
    .duplicate-search-panel,
    .unused-filter-panel,
    .inventory-table-shell,
    .clean-admin-table-shell,
    .special-table-shell,
    .duplicate-table-shell,
    .unused-table-shell
) {
    border-color: rgba(148, 163, 184, .18) !important;
    background: #0f172a !important;
    color: #e5edf7 !important;
}

/* Duplicated ID requested override: force inline filters and clean centered table grid. */
body .content-surface .duplicate-search-panel {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    gap: 1rem !important;
    flex-wrap: nowrap !important;
    white-space: nowrap !important;
    overflow-x: auto !important;
    overflow-y: hidden !important;
}

body .content-surface .duplicate-search-panel .duplicate-filter-field {
    display: inline-flex !important;
    flex-direction: row !important;
    align-items: center !important;
    gap: .5rem !important;
    flex: 0 0 auto !important;
    width: auto !important;
    margin: 0 !important;
}

body .content-surface .duplicate-search-panel .duplicate-filter-field label {
    display: inline-flex !important;
    align-items: center !important;
    margin: 0 !important;
    white-space: nowrap !important;
}

body .content-surface .duplicate-search-panel .duplicate-search,
body .content-surface .duplicate-search-panel .duplicate-filter-select,
body .content-surface .duplicate-search-panel .duplicate-filter-reset {
    flex: 0 0 auto !important;
}

body .content-surface .duplicate-table-shell #duplicateTable {
    border-collapse: collapse !important;
}

body .content-surface .duplicate-table-shell #duplicateTable th,
.duplicate-table-shell #duplicateTable th {
    border: 1px solid #2d3748 !important;
    text-align: center !important;
    vertical-align: middle !important;
}

body .content-surface .duplicate-table-shell #duplicateTable td,
.duplicate-table-shell #duplicateTable td {
    border: 1px solid #2d3748 !important;
    text-align: center !important;
    vertical-align: middle !important;
}

body .content-surface .duplicate-table-shell #duplicateTable .duplicate-action-stack,
body .content-surface .duplicate-table-shell #duplicateTable .dup-actions {
    display: inline-flex !important;
    flex-direction: row !important;
    align-items: center !important;
    justify-content: center !important;
    gap: .5rem !important;
    flex-wrap: nowrap !important;
    white-space: nowrap !important;
}

body .content-surface .duplicate-table-shell #duplicateTable .duplicate-action-stack form,
body .content-surface .duplicate-table-shell #duplicateTable .dup-actions form {
    display: inline-flex !important;
    margin: 0 !important;
}

/* Actual WT admin layout may render inventory pages directly under .page-body. */
.duplicate-search-panel {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    gap: 1rem !important;
    flex-wrap: nowrap !important;
    white-space: nowrap !important;
    overflow-x: auto !important;
    overflow-y: hidden !important;
}

.duplicate-search-panel .duplicate-filter-field {
    display: inline-flex !important;
    flex-direction: row !important;
    align-items: center !important;
    gap: .5rem !important;
    flex: 0 0 auto !important;
    width: auto !important;
    margin: 0 !important;
}

.duplicate-search-panel .duplicate-filter-field label {
    display: inline-flex !important;
    align-items: center !important;
    margin: 0 !important;
    white-space: nowrap !important;
}

.duplicate-search-panel .duplicate-search,
.duplicate-search-panel .duplicate-filter-select,
.duplicate-search-panel .duplicate-filter-reset {
    flex: 0 0 auto !important;
    width: auto !important;
}

.duplicate-table-shell #duplicateTable {
    border-collapse: collapse !important;
}

.duplicate-table-shell #duplicateTable .duplicate-action-stack,
.duplicate-table-shell #duplicateTable .dup-actions {
    display: inline-flex !important;
    flex-direction: row !important;
    align-items: center !important;
    justify-content: center !important;
    gap: .5rem !important;
    flex-wrap: nowrap !important;
    white-space: nowrap !important;
}

.duplicate-table-shell #duplicateTable .duplicate-action-stack form,
.duplicate-table-shell #duplicateTable .dup-actions form {
    display: inline-flex !important;
    margin: 0 !important;
}

/* Final compact inventory table baseline across Inventory List, Under Repair/Faulty, Duplicated ID, and Special Use. */
body .content-surface :is(
    .inventory-page-shell,
    .maintenance-page-shell,
    .unused-page-shell,
    .duplicate-page,
    .special-page-shell,
    .wt-data
) {
    gap: 6px !important;
}

body .content-surface :is(
    .inventory-page-header,
    .maintenance-page-shell > .page-header-block,
    .unused-page-shell > .page-header-block,
    .duplicate-hero .page-header-block,
    .special-page-shell > .page-header-block,
    .wt-data-page-hero
) {
    min-height: 0 !important;
    padding: 6px 8px !important;
    margin: 0 !important;
}

body .content-surface :is(
    #walkiesTable,
    #maintTable,
    #maintenanceTable,
    #duplicateTable,
    #specialTable,
    .unused-table,
    .wt-data table
) {
    width: 100% !important;
    min-width: 0 !important;
    table-layout: auto !important;
    border-collapse: collapse !important;
}

body .content-surface :is(
    .clean-admin-table-scroll,
    .unused-scroll,
    .duplicate-table-scroll,
    .special-table-scroll,
    .wt-data-scroll,
    .dataTables_scrollBody
) {
    overflow-x: hidden !important;
    overflow-y: hidden !important;
}

body .content-surface :is(
    #walkiesTable,
    #maintTable,
    #maintenanceTable,
    #duplicateTable,
    #specialTable,
    .unused-table,
    .wt-data table
) thead th {
    height: 28px !important;
    padding: 4px 6px !important;
    background: #eef3f8 !important;
    border: 1px solid #d8e1ed !important;
    color: #334155 !important;
    font-size: 9px !important;
    font-weight: 900 !important;
    letter-spacing: .04em !important;
    line-height: 1.05 !important;
    text-align: center !important;
    text-transform: uppercase !important;
    white-space: nowrap !important;
    vertical-align: middle !important;
}

body .content-surface :is(
    #walkiesTable,
    #maintTable,
    #maintenanceTable,
    #duplicateTable,
    #specialTable,
    .unused-table,
    .wt-data table
) tbody td {
    height: 26px !important;
    min-height: 26px !important;
    padding: 3px 6px !important;
    border: 1px solid #e2e8f0 !important;
    color: #0f172a !important;
    font-size: 10px !important;
    font-weight: 650 !important;
    line-height: 1.1 !important;
    text-align: center !important;
    white-space: nowrap !important;
    vertical-align: middle !important;
}

body .content-surface #duplicateTable tbody td:nth-child(5),
body .content-surface #duplicateTable tbody td:nth-child(6),
body .content-surface #duplicateTable tbody td:nth-child(5) :is(.dup-change-id-val, .dup-change-id-empty),
body .content-surface #duplicateTable tbody td:nth-child(6) .dup-done-badge {
    text-align: center !important;
}

body .content-surface :is(
    #walkiesTable,
    #maintTable,
    #maintenanceTable,
    #duplicateTable,
    #specialTable,
    .unused-table,
    .wt-data table
) tbody td :is(.clean-admin-pill, .dup-status-badge, .dup-done-badge) {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-height: 18px !important;
    padding: 2px 6px !important;
    font-size: 8px !important;
    line-height: 1 !important;
}

body .content-surface :is(
    #walkiesTable td.inventory-action-col,
    #maintTable td.maintenance-action-col,
    #maintenanceTable td.maintenance-action-col,
    #duplicateTable td:last-child,
    #specialTable td:last-child,
    .unused-table td:last-child
) {
    text-align: center !important;
}

body .content-surface :is(
    .inventory-table-footer,
    .repair-table-footer,
    .unused-pagination-bar,
    .duplicate-table-footer,
    .special-table-footer,
    .adminit-table-footer,
    .wt-data-footer
) {
    min-height: 34px !important;
    padding: 5px 8px !important;
}

html.dark body .content-surface :is(
    #walkiesTable,
    #maintTable,
    #maintenanceTable,
    #duplicateTable,
    #specialTable,
    .unused-table,
    .wt-data table
) thead th {
    background: #1f2937 !important;
    border-color: #2f3b4f !important;
    color: #e5edf7 !important;
}

html.dark body .content-surface :is(
    #walkiesTable,
    #maintTable,
    #maintenanceTable,
    #duplicateTable,
    #specialTable,
    .unused-table,
    .wt-data table
) tbody td {
    background: #111827 !important;
    border-color: #263244 !important;
    color: #e5edf7 !important;
}

/* Unified final contract: one compact visual language for every inventory module. */
body .content-surface :is(
    .inventory-page-shell,
    .maintenance-page-shell,
    .duplicate-page,
    .special-page-shell,
    .wt-data
),
body .content-surface :is(
    #walkiesTable,
    #maintTable,
    #maintenanceTable,
    #duplicateTable,
    #specialTable,
    .clean-admin-table,
    .wt-data table
) {
    font-family: Inter, "Inter", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif !important;
    font-size: 11px !important;
    line-height: 1.1 !important;
}

body .content-surface :is(
    .inventory-table-shell,
    .clean-admin-table-shell,
    .duplicate-table-shell,
    .special-table-shell,
    .wt-data-table,
    #mainTableContainer
) {
    margin: 0 !important;
    padding: 0 !important;
    border-radius: 6px !important;
    overflow: hidden !important;
}

body .content-surface :is(
    #walkiesTable,
    #maintTable,
    #maintenanceTable,
    #duplicateTable,
    #specialTable,
    .clean-admin-table,
    .wt-data table
) {
    width: 100% !important;
    max-width: 100% !important;
    min-width: 0 !important;
    table-layout: auto !important;
}

body .content-surface :is(
    #walkiesTable,
    #maintTable,
    #maintenanceTable,
    #duplicateTable,
    #specialTable,
    .clean-admin-table,
    .wt-data table
) thead th {
    height: 26px !important;
    padding: 3px 6px !important;
    background: #eef3f8 !important;
    border: 1px solid #d8e1ed !important;
    color: #334155 !important;
    font-size: 9px !important;
    font-weight: 900 !important;
    letter-spacing: .04em !important;
    line-height: 1.05 !important;
    text-align: center !important;
    text-transform: uppercase !important;
    white-space: nowrap !important;
}

body .content-surface :is(
    #walkiesTable,
    #maintTable,
    #maintenanceTable,
    #duplicateTable,
    #specialTable,
    .clean-admin-table,
    .wt-data table
) tbody td {
    height: 24px !important;
    min-height: 24px !important;
    padding: 2px 6px !important;
    color: #0f172a !important;
    font-size: 10px !important;
    font-weight: 650 !important;
    line-height: 1.1 !important;
    text-align: left !important;
    white-space: nowrap !important;
    vertical-align: middle !important;
}

body .content-surface :is(
    #walkiesTable,
    #maintTable,
    #maintenanceTable,
    #duplicateTable,
    #specialTable
) :is(
    .inventory-action-buttons,
    .maintenance-action-stack,
    .clean-admin-actions,
    .dup-actions,
    .special-action-buttons
) {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 4px !important;
    flex-wrap: nowrap !important;
    white-space: nowrap !important;
}

body .content-surface :is(
    #walkiesTable,
    #maintTable,
    #maintenanceTable,
    #duplicateTable,
    #specialTable
) :is(
    .inventory-action-buttons,
    .maintenance-action-stack,
    .clean-admin-actions,
    .dup-actions,
    .special-action-buttons
) form {
    display: inline-flex !important;
    margin: 0 !important;
}

body .content-surface :is(
    #walkiesTable,
    #maintTable,
    #maintenanceTable,
    #duplicateTable,
    #specialTable
) :is(
    .inventory-action-buttons .btn,
    .maintenance-action-stack .wt-btn,
    .clean-admin-actions .wt-btn,
    .dup-actions .btn,
    .special-action-buttons .btn
) {
    width: 54px !important;
    min-width: 54px !important;
    max-width: 54px !important;
    height: 22px !important;
    min-height: 22px !important;
    padding: 0 5px !important;
    border-radius: 5px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 3px !important;
    font-size: 8px !important;
    font-weight: 900 !important;
    line-height: 1 !important;
    letter-spacing: .02em !important;
    text-transform: uppercase !important;
    white-space: nowrap !important;
}

body .content-surface :is(
    #walkiesTable,
    #maintTable,
    #maintenanceTable,
    #duplicateTable,
    #specialTable
) :is(.btn-info, .maintenance-action-view) {
    border-color: #0284c7 !important;
    background: #0284c7 !important;
    color: #ffffff !important;
}

body .content-surface :is(
    #walkiesTable,
    #maintTable,
    #maintenanceTable,
    #duplicateTable,
    #specialTable
) :is(.btn-primary, .maintenance-action-edit) {
    border-color: #2563eb !important;
    background: #2563eb !important;
    color: #ffffff !important;
}

body .content-surface :is(
    #walkiesTable,
    #maintTable,
    #maintenanceTable,
    #duplicateTable,
    #specialTable
) :is(.btn-danger, .wt-btn-danger, .maintenance-action-delete) {
    border-color: #dc2626 !important;
    background: #dc2626 !important;
    color: #ffffff !important;
}

body .content-surface :is(
    #walkiesTable,
    #maintTable,
    #maintenanceTable,
    #duplicateTable,
    #specialTable
) :is(.btn i, .wt-btn i) {
    width: 8px !important;
    min-width: 8px !important;
    font-size: 8px !important;
    line-height: 1 !important;
}

html.dark body .content-surface :is(
    #walkiesTable,
    #maintTable,
    #maintenanceTable,
    #duplicateTable,
    #specialTable,
    .clean-admin-table,
    .wt-data table
) thead th {
    background: #1f2937 !important;
    border-color: #2f3b4f !important;
    color: #e5edf7 !important;
}

html.dark body .content-surface :is(
    #walkiesTable,
    #maintTable,
    #maintenanceTable,
    #duplicateTable,
    #specialTable,
    .clean-admin-table,
    .wt-data table
) tbody td {
    background: #111827 !important;
    border-color: #263244 !important;
    color: #e5edf7 !important;
}

body .content-surface :is(
    #walkiesTable,
    #maintTable,
    #maintenanceTable,
    #duplicateTable,
    #specialTable,
    .unused-table,
    .clean-admin-table,
    .wt-data table
) thead th {
    text-align: center !important;
}

body .content-surface :is(
    .clean-admin-filter,
    .unused-filter-panel,
    .duplicate-search-panel,
    .special-filter-panel,
    .wt-data-filter
) {
    margin-bottom: 12px !important;
}

body .content-surface :is(
    #mainTableContainer.inventory-table-shell,
    #mainTableContainer.clean-admin-table-shell,
    #mainTableContainer.duplicate-table-shell,
    #mainTableContainer.special-table-shell,
    .inventory-table-shell,
    .clean-admin-table-shell,
    .duplicate-table-shell,
    .special-table-shell,
    .wt-data-table
) {
    margin-top: 8px !important;
}

body .content-surface .inventory-table-frame > .clean-admin-filter + #mainTableContainer.inventory-table-shell {
    margin-top: 10px !important;
    
}
</style>
