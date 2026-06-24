<style id="inventory-tools-unified-ui">
/* Unified Inventory Tools UI: Inventory List, Under Repair / Faulty, Duplicated ID, Special Use */
:root {
    --wt-tools-bg: #0b1220;
    --wt-tools-panel: #0f172a;
    --wt-tools-cell: #111827;
    --wt-tools-head: #1f2937;
    --wt-tools-border: #2d3748;
    --wt-tools-border-soft: #334155;
    --wt-tools-text: #dbe4f0;
    --wt-tools-muted: #94a3b8;
}

.inventory-page-shell,
.maintenance-page-shell,
.duplicate-page,
.special-page-shell,
.wt-data {
    gap: 8px !important;
}

.clean-admin-filter,
.duplicate-search-panel,
.special-filter-panel,
.wt-data-filter {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    gap: 1rem !important;
    flex-wrap: nowrap !important;
    width: 100% !important;
    min-height: 46px !important;
    margin: 0 !important;
    padding: 7px 10px !important;
    border: 1px solid var(--wt-tools-border) !important;
    border-radius: 8px !important;
    background: var(--wt-tools-panel) !important;
    box-shadow: none !important;
    overflow-x: auto !important;
    overflow-y: hidden !important;
    white-space: nowrap !important;
}

.clean-admin-filter-grid {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    gap: 1rem !important;
    flex-wrap: nowrap !important;
    width: 100% !important;
    min-width: max-content !important;
}

.clean-admin-field,
.unused-filter-field,
.duplicate-filter-field,
.special-filter-field,
.wt-data-field {
    display: inline-flex !important;
    flex-direction: row !important;
    align-items: center !important;
    gap: .5rem !important;
    flex: 0 0 auto !important;
    width: auto !important;
    min-width: 0 !important;
    margin: 0 !important;
}

.clean-admin-label,
.unused-filter-field label,
.duplicate-filter-field label,
.special-filter-field label,
.wt-data-field label {
    display: inline-flex !important;
    align-items: center !important;
    flex: 0 0 auto !important;
    margin: 0 !important;
    color: var(--wt-tools-muted) !important;
    font-size: 11px !important;
    font-weight: 900 !important;
    letter-spacing: .14em !important;
    line-height: 1 !important;
    text-transform: uppercase !important;
    white-space: nowrap !important;
}

.clean-admin-input,
.clean-admin-select,
.clean-admin-reset,
.duplicate-search,
.duplicate-filter-select,
.duplicate-filter-reset,
.special-filter-input,
.special-filter-select,
.special-filter-reset,
.wt-data-input,
.wt-data-select,
.wt-data-reset {
    flex: 0 0 auto !important;
    width: auto !important;
    min-width: 112px !important;
    height: 30px !important;
    min-height: 30px !important;
    padding: 0 10px !important;
    border: 1px solid var(--wt-tools-border-soft) !important;
    border-radius: 7px !important;
    background: var(--wt-tools-cell) !important;
    color: var(--wt-tools-text) !important;
    font-size: 11px !important;
    font-weight: 800 !important;
    line-height: 1 !important;
    box-shadow: none !important;
    outline: none !important;
}

.clean-admin-input,
.duplicate-search,
.special-filter-input,
.wt-data-input {
    width: 260px !important;
    min-width: 220px !important;
    max-width: 280px !important;
}

.clean-admin-reset,
.duplicate-filter-reset,
.special-filter-reset,
.wt-data-reset {
    min-width: 78px !important;
    cursor: pointer !important;
}

.inventory-table-shell,
.clean-admin-table-shell,
.duplicate-table-shell,
.special-table-shell,
.wt-data-table,
#mainTableContainer {
    border: 1px solid var(--wt-tools-border) !important;
    border-radius: 8px !important;
    background: var(--wt-tools-cell) !important;
    box-shadow: none !important;
    overflow: auto !important;
}

.clean-admin-table-scroll,
.duplicate-table-scroll,
.special-table-scroll,
.wt-data-scroll,
.dataTables_scrollBody {
    background: var(--wt-tools-cell) !important;
}

#walkiesTable,
#maintTable,
#maintenanceTable,
#duplicateTable,
#specialTable,
.clean-admin-table,
.wt-data table {
    width: 100% !important;
    border-collapse: collapse !important;
    border-spacing: 0 !important;
    table-layout: fixed !important;
    background: var(--wt-tools-cell) !important;
}

#walkiesTable th,
#walkiesTable td,
#maintTable th,
#maintTable td,
#maintenanceTable th,
#maintenanceTable td,
#duplicateTable th,
#duplicateTable td,
#specialTable th,
#specialTable td,
.clean-admin-table th,
.clean-admin-table td,
.wt-data table th,
.wt-data table td {
    border: 1px solid var(--wt-tools-border) !important;
    text-align: center !important;
    vertical-align: middle !important;
    padding: 6px 8px !important;
    height: 32px !important;
    font-size: 11px !important;
    line-height: 1.15 !important;
    white-space: nowrap !important;
}

#walkiesTable.dataTable th,
#walkiesTable.dataTable td,
#maintTable.dataTable th,
#maintTable.dataTable td,
#maintenanceTable.dataTable th,
#maintenanceTable.dataTable td,
#duplicateTable.dataTable th,
#duplicateTable.dataTable td,
#specialTable.dataTable th,
#specialTable.dataTable td,
.wt-data table.dataTable th,
.wt-data table.dataTable td {
    padding: 6px 8px !important;
    height: 32px !important;
    font-size: 11px !important;
    line-height: 1.15 !important;
}

#walkiesTable thead th,
#maintTable thead th,
#maintenanceTable thead th,
#duplicateTable thead th,
#specialTable thead th,
.clean-admin-table thead th,
.wt-data table thead th {
    background: var(--wt-tools-head) !important;
    color: var(--wt-tools-text) !important;
    font-weight: 900 !important;
    letter-spacing: .06em !important;
    text-transform: uppercase !important;
}

#walkiesTable tbody td,
#maintTable tbody td,
#maintenanceTable tbody td,
#duplicateTable tbody td,
#specialTable tbody td,
.clean-admin-table tbody td,
.wt-data table tbody td {
    background: var(--wt-tools-cell) !important;
    color: var(--wt-tools-text) !important;
    font-weight: 700 !important;
}

#walkiesTable tbody tr:hover td,
#maintTable tbody tr:hover td,
#maintenanceTable tbody tr:hover td,
#duplicateTable tbody tr:hover td,
#specialTable tbody tr:hover td,
.clean-admin-table tbody tr:hover td,
.wt-data table tbody tr:hover td {
    background: #172033 !important;
}

#walkiesTable td:last-child,
#maintTable td:last-child,
#maintenanceTable td:last-child,
#duplicateTable td:last-child,
#specialTable td:last-child,
.wt-data table td:last-child {
    overflow: visible !important;
}

.clean-admin-actions,
.maintenance-action-stack,
.duplicate-action-stack,
.dup-actions,
.special-action-buttons,
.wt-data-actions,
#walkiesTable td:last-child > div,
#maintTable td:last-child > div,
#maintenanceTable td:last-child > div,
#duplicateTable td:last-child > div,
#specialTable td:last-child > div {
    display: inline-flex !important;
    flex-direction: row !important;
    align-items: center !important;
    justify-content: center !important;
    gap: .5rem !important;
    flex-wrap: nowrap !important;
    white-space: nowrap !important;
    width: auto !important;
    min-width: max-content !important;
}

.clean-admin-actions form,
.maintenance-action-stack form,
.duplicate-action-stack form,
.dup-actions form,
.special-action-buttons form,
.wt-data-actions form {
    display: inline-flex !important;
    margin: 0 !important;
}

#walkiesTable td:last-child .btn,
#maintTable td:last-child .btn,
#maintenanceTable td:last-child .btn,
#duplicateTable td:last-child .btn,
#specialTable td:last-child .btn,
#walkiesTable td:last-child .wt-btn,
#maintTable td:last-child .wt-btn,
#maintenanceTable td:last-child .wt-btn,
#duplicateTable td:last-child .wt-btn,
#specialTable td:last-child .wt-btn,
.clean-admin-actions .btn,
.clean-admin-actions .wt-btn,
.maintenance-action-stack .btn,
.maintenance-action-stack .wt-btn,
.duplicate-action-stack .btn,
.duplicate-action-stack .wt-btn,
.dup-actions .btn,
.dup-actions .wt-btn,
.special-action-buttons .btn,
.special-action-buttons .wt-btn,
.wt-data-actions .btn,
.wt-data-actions .wt-btn {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 5px !important;
    height: 26px !important;
    min-height: 26px !important;
    min-width: 68px !important;
    padding: 0 8px !important;
    border-radius: 6px !important;
    font-size: 10px !important;
    font-weight: 900 !important;
    line-height: 1 !important;
    text-transform: none !important;
    white-space: nowrap !important;
}

#walkiesTable :is(.inventory-status-badge, .inventory-type-badge, .inventory-id-chip),
#maintTable :is(.clean-admin-pill, .maintenance-status-badge),
#maintenanceTable :is(.clean-admin-pill, .maintenance-status-badge),
#duplicateTable :is(.dup-status-badge, .dup-done-badge),
#specialTable :is(.clean-admin-pill, .special-status-badge, .inventory-status-badge),
.wt-data table :is(.clean-admin-pill, .inventory-status-badge, .special-status-badge) {
    min-height: 20px !important;
    height: 20px !important;
    padding: 0 7px !important;
    border-radius: 5px !important;
    font-size: 10px !important;
    line-height: 1 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
}
</style>
