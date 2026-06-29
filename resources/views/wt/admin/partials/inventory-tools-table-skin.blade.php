<style id="inventory-tools-table-skin-final">
body .content-surface :is(
    #mainTableContainer.clean-admin-table-shell,
    #mainTableContainer.duplicate-table-shell,
    #mainTableContainer.special-table-shell,
    .clean-admin-table-shell,
    .duplicate-table-shell,
    .special-table-shell,
    .wt-data-table
) {
    border: 1px solid #cbd5e1 !important;
    border-radius: 12px !important;
    background: #ffffff !important;
    box-shadow: none !important;
    overflow: hidden !important;
}

body .content-surface :is(
    #maintenanceTable.clean-admin-table,
    #duplicateTable,
    #specialTable,
    .wt-data table
) {
    width: 100% !important;
    border-collapse: collapse !important;
    border-spacing: 0 !important;
    background: #ffffff !important;
    color: #0f172a !important;
    font-size: 11px !important;
}

body .content-surface :is(
    #maintenanceTable.clean-admin-table,
    #duplicateTable,
    #specialTable,
    .wt-data table
) thead th {
    height: 28px !important;
    padding: 5px 8px !important;
    background: #eef3f8 !important;
    border: 1px solid #cbd5e1 !important;
    color: #1e293b !important;
    font-size: 11px !important;
    font-weight: 900 !important;
    line-height: 1.2 !important;
    letter-spacing: .04em !important;
    text-transform: uppercase !important;
    vertical-align: middle !important;
    white-space: nowrap !important;
}

body .content-surface :is(
    #maintenanceTable.clean-admin-table,
    #duplicateTable,
    #specialTable,
    .wt-data table
) tbody td {
    height: 30px !important;
    min-height: 30px !important;
    padding: 5px 8px !important;
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    color: #0f172a !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    line-height: 1.25 !important;
    vertical-align: middle !important;
}

body .content-surface :is(
    #maintenanceTable.clean-admin-table,
    #duplicateTable,
    #specialTable,
    .wt-data table
) tbody tr:hover td {
    background: #f8fafc !important;
}

body .content-surface :is(
    #maintenanceTable,
    #duplicateTable,
    #specialTable
) :is(.maintenance-status-pill, .maintenance-done-pill, .dup-status-badge, .dup-done-badge, td:nth-child(2) > span) {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-width: 54px !important;
    height: 18px !important;
    padding: 0 6px !important;
    border: 1px solid transparent !important;
    border-radius: 5px !important;
    font-size: 9px !important;
    font-weight: 900 !important;
    line-height: 1 !important;
    letter-spacing: .02em !important;
    text-transform: uppercase !important;
    opacity: 1 !important;
    filter: none !important;
    box-shadow: none !important;
}

body .content-surface :is(#maintenanceTable, #duplicateTable, #specialTable) :is(
    .maintenance-status-pill[data-status="UNUSED"],
    .maintenance-status-pill[data-status="DONE"],
    .maintenance-status-pill[data-status="ALREADY FIXED"],
    .maintenance-done-pill[data-done="YES"],
    .maintenance-done-pill.maintenance-done-yes,
    .dup-status-badge.is-unused,
    .dup-done-badge.is-yes
) {
    background: #dcfce7 !important;
    border-color: #86efac !important;
    color: #166534 !important;
}

body .content-surface :is(#maintenanceTable, #duplicateTable, #specialTable) :is(
    .maintenance-status-pill[data-status="IN USE"],
    .maintenance-status-pill[data-status="READY TO COLLECT"],
    .dup-status-badge.is-in-use
) {
    background: #dbeafe !important;
    border-color: #93c5fd !important;
    color: #1d4ed8 !important;
}

body .content-surface :is(#maintenanceTable, #duplicateTable, #specialTable) :is(
    .maintenance-status-pill[data-status="REPAIRING"],
    .maintenance-status-pill[data-status="UNDER REPAIR"],
    .maintenance-status-pill[data-status="FAULTY"],
    .maintenance-done-pill[data-done="NO"],
    .maintenance-done-pill.maintenance-done-no,
    .dup-status-badge.is-repairing,
    .dup-done-badge.is-no
) {
    background: #fee2e2 !important;
    border-color: #fca5a5 !important;
    color: #b91c1c !important;
}

body .content-surface :is(#maintenanceTable, #duplicateTable, #specialTable) :is(
    .maintenance-status-pill[data-status="B.E.R"],
    .maintenance-status-pill[data-status="LOST"],
    .maintenance-status-pill[data-status="UNKNOWN"],
    .maintenance-status-pill[data-status="CALIBRATING"],
    .dup-status-badge.is-unknown,
    .dup-status-badge.is-change-id,
    .dup-status-badge.is-other
) {
    background: #f3e8ff !important;
    border-color: #d8b4fe !important;
    color: #7e22ce !important;
}

body .content-surface #specialTable td:nth-child(2) > span {
    background: #dcfce7 !important;
    border-color: #86efac !important;
    color: #166534 !important;
}

body .content-surface #specialTable tr[data-status="IN USE"] td:nth-child(2) > span {
    background: #dbeafe !important;
    border-color: #93c5fd !important;
    color: #1d4ed8 !important;
}

body .content-surface #specialTable tr[data-status="REPAIRING"] td:nth-child(2) > span,
body .content-surface #specialTable tr[data-status="FAULTY"] td:nth-child(2) > span {
    background: #fee2e2 !important;
    border-color: #fca5a5 !important;
    color: #b91c1c !important;
}

body .content-surface #specialTable tr[data-status="B.E.R"] td:nth-child(2) > span,
body .content-surface #specialTable tr[data-status="UNKNOWN"] td:nth-child(2) > span,
body .content-surface #specialTable tr[data-status="LOST"] td:nth-child(2) > span {
    background: #f3e8ff !important;
    border-color: #d8b4fe !important;
    color: #7e22ce !important;
}

body .content-surface :is(
    .repair-table-footer,
    .duplicate-table-footer,
    .special-table-footer,
    .wt-data-footer
) {
    min-height: 44px !important;
    padding: 7px 12px !important;
    border-top: 1px solid #d6e0ec !important;
    background: #ffffff !important;
}

body .content-surface :is(
    .repair-table-info,
    .duplicate-table-info,
    .special-table-info,
    .wt-data-info
) {
    color: #334155 !important;
    font-size: 12px !important;
    font-weight: 800 !important;
    letter-spacing: .02em !important;
}

body .content-surface :is(#maintenanceTable, #duplicateTable, #specialTable) :is(
    .clean-admin-actions,
    .dup-actions,
    .special-action-buttons
) {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 4px !important;
    width: 100% !important;
    white-space: nowrap !important;
}

body .content-surface :is(#maintenanceTable, #duplicateTable, #specialTable) :is(
    .wt-btn,
    .btn
) {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 5px !important;
    min-width: 62px !important;
    height: 30px !important;
    min-height: 30px !important;
    padding: 0 10px !important;
    border-radius: 6px !important;
    font-size: 11px !important;
    font-weight: 800 !important;
    line-height: 1 !important;
    text-decoration: none !important;
    box-shadow: none !important;
}

html.dark body .content-surface :is(
    #mainTableContainer.clean-admin-table-shell,
    #mainTableContainer.duplicate-table-shell,
    #mainTableContainer.special-table-shell,
    .clean-admin-table-shell,
    .duplicate-table-shell,
    .special-table-shell,
    .wt-data-table
),
html[data-theme="dark"] body .content-surface :is(
    #mainTableContainer.clean-admin-table-shell,
    #mainTableContainer.duplicate-table-shell,
    #mainTableContainer.special-table-shell,
    .clean-admin-table-shell,
    .duplicate-table-shell,
    .special-table-shell,
    .wt-data-table
) {
    border-color: #263244 !important;
    background: #111827 !important;
}

html.dark body .content-surface :is(
    #maintenanceTable.clean-admin-table,
    #duplicateTable,
    #specialTable,
    .wt-data table
) thead th,
html[data-theme="dark"] body .content-surface :is(
    #maintenanceTable.clean-admin-table,
    #duplicateTable,
    #specialTable,
    .wt-data table
) thead th {
    background: #1f2937 !important;
    border-color: #2f3b4f !important;
    color: #cbd5e1 !important;
}

html.dark body .content-surface :is(
    #maintenanceTable.clean-admin-table,
    #duplicateTable,
    #specialTable,
    .wt-data table
) tbody td,
html[data-theme="dark"] body .content-surface :is(
    #maintenanceTable.clean-admin-table,
    #duplicateTable,
    #specialTable,
    .wt-data table
) tbody td {
    background: #111827 !important;
    border-color: #263244 !important;
    color: #dbe4f0 !important;
}

html.dark body .content-surface :is(
    #maintenanceTable.clean-admin-table,
    #duplicateTable,
    #specialTable,
    .wt-data table
) tbody tr:hover td,
html[data-theme="dark"] body .content-surface :is(
    #maintenanceTable.clean-admin-table,
    #duplicateTable,
    #specialTable,
    .wt-data table
) tbody tr:hover td {
    background: #152033 !important;
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
</style>
