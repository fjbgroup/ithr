@once
<style>
  :where(.content-area, .page-body, .content-surface) :is(.table, table.dataTable, .clean-admin-table, .wt-data table):not(.no-ui-standard) {
    width: 100% !important;
    table-layout: fixed !important;
    border-collapse: collapse !important;
    font-size: 12px !important;
  }
  :where(.content-area, .page-body, .content-surface) :is(.table, table.dataTable, .clean-admin-table, .wt-data table):not(.no-ui-standard) thead th {
    height: 34px !important;
    padding: 7px 10px !important;
    background: var(--table-head-bg, #f8fafc) !important;
    color: var(--table-head-color, var(--muted, #64748b)) !important;
    font-size: 10px !important;
    font-weight: 900 !important;
    letter-spacing: .07em !important;
    line-height: 1.1 !important;
    text-transform: uppercase !important;
    vertical-align: middle !important;
    white-space: nowrap !important;
  }
  :where(.content-area, .page-body, .content-surface) :is(.table, table.dataTable, .clean-admin-table, .wt-data table):not(.no-ui-standard) tbody td {
    height: 36px !important;
    max-width: 1px;
    padding: 7px 10px !important;
    color: var(--text, #1e293b) !important;
    font-size: 12px !important;
    font-weight: 650 !important;
    line-height: 1.25 !important;
    overflow: hidden;
    text-overflow: ellipsis;
    vertical-align: middle !important;
    white-space: nowrap !important;
  }
  :where(.content-area, .page-body, .content-surface) :is(.table, table.dataTable, .clean-admin-table, .wt-data table):not(.no-ui-standard) tbody tr:hover td {
    background: var(--table-hover, #f0f9ff) !important;
  }
  :where(.content-area, .page-body, .content-surface) :is(th, td):is(:last-child, .td-actions, .actions, .action-cell, .ui-col-actions) {
    text-align: center !important;
    white-space: nowrap !important;
  }
  :where(.content-area, .page-body, .content-surface) :is(.btn-view, .btn-edit, .btn-delete, .btn-icon) {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 4px !important;
    min-width: 28px !important;
    height: 28px !important;
    padding: 0 8px !important;
    border-radius: 6px !important;
    font-size: 11px !important;
    font-weight: 800 !important;
    line-height: 1 !important;
    text-decoration: none !important;
  }
  :where(.content-area, .page-body, .content-surface) .btn-view {
    border: 1px solid rgba(2,132,199,.16) !important;
    background: rgba(2,132,199,.08) !important;
    color: #0369a1 !important;
  }
  :where(.content-area, .page-body, .content-surface) .btn-edit {
    border: 1px solid rgba(79,70,229,.16) !important;
    background: rgba(79,70,229,.08) !important;
    color: #4f46e5 !important;
  }
  :where(.content-area, .page-body, .content-surface) .btn-delete {
    border: 1px solid rgba(220,38,38,.16) !important;
    background: rgba(220,38,38,.08) !important;
    color: #dc2626 !important;
  }
  @media (max-width: 900px) {
    :where(.content-area, .page-body, .content-surface) :is(.table, table.dataTable, .clean-admin-table, .wt-data table):not(.no-ui-standard) {
      table-layout: auto !important;
    }
  }
</style>
@endonce
