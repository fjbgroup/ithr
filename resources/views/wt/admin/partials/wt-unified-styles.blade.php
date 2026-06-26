<style id="wt-unified-dashboard-styles">
/*
 * WT ADMIN — UNIFIED DASHBOARD LAYOUT SYSTEM
 * Single source of truth for all 4 inventory pages:
 *   1. Inventory List       (.inventory-page-shell)
 *   2. Under Repair/Faulty  (.maintenance-page-shell)
 *   3. Duplicated ID        (.duplicate-page)
 *   4. Special Use          (.special-page-shell)
 *
 * This partial is @included LAST in each page's @section('content')
 * so it wins on cascade order without requiring excessive specificity.
 *
 * CSS only — no Tailwind dependency.
 */

/* ══════════════════════════════════════════════════════════
   1. DESIGN TOKENS
   ══════════════════════════════════════════════════════════ */
:root {
    /* Surfaces */
    --wu-page-bg:       #f4f7fb;
    --wu-surface:       #ffffff;
    --wu-surface-alt:   #f8fafc;
    --wu-head-bg:       #f1f5f9;
    --wu-border:        #e2e8f0;
    --wu-border-soft:   #d8e1ed;
    /* Text */
    --wu-text:          #1f2937;
    --wu-text-muted:    #64748b;
    --wu-text-head:     #475569;
    /* Filter bar */
    --wu-filter-bg:     #f8fafc;
    --wu-input-bg:      #ffffff;
    --wu-input-border:  #d1d9e6;
    --wu-input-text:    #1f2937;
    /* Hover */
    --wu-row-hover:     #f1f5f9;
    /* Pagination */
    --wu-page-btn-bg:   #ffffff;
    --wu-page-btn-border: #d1d9e6;
    --wu-page-btn-text: #475569;
    --wu-page-active-bg: #0284c7;
    --wu-page-active-text: #ffffff;
    /* Status badges */
    --wu-badge-unused-bg:     #dcfce7;
    --wu-badge-unused-bdr:    #86efac;
    --wu-badge-unused-txt:    #166534;
    --wu-badge-inuse-bg:      #dbeafe;
    --wu-badge-inuse-bdr:     #93c5fd;
    --wu-badge-inuse-txt:     #1d4ed8;
    --wu-badge-repair-bg:     #fee2e2;
    --wu-badge-repair-bdr:    #fca5a5;
    --wu-badge-repair-txt:    #b91c1c;
    --wu-badge-ber-bg:        #f3e8ff;
    --wu-badge-ber-bdr:       #d8b4fe;
    --wu-badge-ber-txt:       #7e22ce;
    --wu-badge-wait-bg:       #fef3c7;
    --wu-badge-wait-bdr:      #fcd34d;
    --wu-badge-wait-txt:      #92400e;
    --wu-badge-ready-bg:      #e0f2fe;
    --wu-badge-ready-bdr:     #7dd3fc;
    --wu-badge-ready-txt:     #0369a1;
    --wu-badge-done-bg:       #dcfce7;
    --wu-badge-done-bdr:      #86efac;
    --wu-badge-done-txt:      #166534;
    --wu-badge-special-bg:    #ede9fe;
    --wu-badge-special-bdr:   #c4b5fd;
    --wu-badge-special-txt:   #6d28d9;
    /* Action buttons */
    --wu-btn-view-bg:   #0284c7;
    --wu-btn-edit-bg:   #2563eb;
    --wu-btn-del-bg:    #dc2626;
}

html.dark {
    --wu-page-bg:       #0b1220;
    --wu-surface:       #111827;
    --wu-surface-alt:   #172033;
    --wu-head-bg:       #1f2937;
    --wu-border:        #263244;
    --wu-border-soft:   #2f3b4f;
    --wu-text:          #dbe4f0;
    --wu-text-muted:    #94a3b8;
    --wu-text-head:     #cbd5e1;
    --wu-filter-bg:     #0f172a;
    --wu-input-bg:      #111827;
    --wu-input-border:  #334155;
    --wu-input-text:    #e2e8f0;
    --wu-row-hover:     #172033;
    --wu-page-btn-bg:   #0f172a;
    --wu-page-btn-border: #2f4d74;
    --wu-page-btn-text: #bfdbfe;
    --wu-page-active-bg: #0f3a72;
    --wu-page-active-text: #ffffff;
    --wu-badge-unused-bg:     #14532d;
    --wu-badge-unused-bdr:    #22c55e;
    --wu-badge-unused-txt:    #bbf7d0;
    --wu-badge-inuse-bg:      #1e3a8a;
    --wu-badge-inuse-bdr:     #60a5fa;
    --wu-badge-inuse-txt:     #dbeafe;
    --wu-badge-repair-bg:     #7f1d1d;
    --wu-badge-repair-bdr:    #ef4444;
    --wu-badge-repair-txt:    #fecaca;
    --wu-badge-ber-bg:        #581c87;
    --wu-badge-ber-bdr:       #a855f7;
    --wu-badge-ber-txt:       #f3e8ff;
    --wu-badge-wait-bg:       #78350f;
    --wu-badge-wait-bdr:      #f59e0b;
    --wu-badge-wait-txt:      #fde68a;
    --wu-badge-ready-bg:      #0c4a6e;
    --wu-badge-ready-bdr:     #38bdf8;
    --wu-badge-ready-txt:     #e0f2fe;
    --wu-badge-done-bg:       #14532d;
    --wu-badge-done-bdr:      #22c55e;
    --wu-badge-done-txt:      #bbf7d0;
    --wu-badge-special-bg:    #3b0764;
    --wu-badge-special-bdr:   #a78bfa;
    --wu-badge-special-txt:   #ede9fe;
}

/* ══════════════════════════════════════════════════════════
   2. PAGE HEADER — shared across all 4 pages
   ══════════════════════════════════════════════════════════ */
.inventory-page-shell .inventory-page-header,
.maintenance-page-shell .page-header-block,
.duplicate-page .page-header-block,
.special-page-shell .page-header-block {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 12px !important;
    flex-wrap: wrap !important;
    padding: 0 0 10px !important;
    margin: 0 !important;
    background: transparent !important;
    border: 0 !important;
    box-shadow: none !important;
    border-radius: 0 !important;
}
.inventory-page-shell .page-title-standard,
.maintenance-page-shell .page-title-standard,
.duplicate-page .page-title-standard,
.special-page-shell .page-title-standard {
    margin: 0 !important;
    font-size: 18px !important;
    font-weight: 900 !important;
    line-height: 1.1 !important;
    color: var(--wu-text) !important;
    letter-spacing: 0 !important;
}
.inventory-page-shell .page-subtitle-standard,
.maintenance-page-shell .page-subtitle-standard,
.duplicate-page .page-subtitle-standard,
.special-page-shell .page-subtitle-standard {
    display: block !important;
    margin-top: 4px !important;
    font-size: 9px !important;
    font-weight: 700 !important;
    letter-spacing: .14em !important;
    line-height: 1.4 !important;
    text-transform: uppercase !important;
    color: var(--wu-text-muted) !important;
}

/* Top-right action buttons (Import Excel, Add Item) */
.inventory-page-shell .inventory-header-actions .wt-btn,
.maintenance-page-shell .page-header-block .wt-btn,
.duplicate-page .page-header-block .wt-btn,
.special-page-shell .page-header-block .wt-btn {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    height: 32px !important;
    min-height: 32px !important;
    padding: 0 12px !important;
    border-radius: 8px !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    letter-spacing: .02em !important;
    white-space: nowrap !important;
    text-decoration: none !important;
    cursor: pointer !important;
    /* Light mode */
    background: var(--wu-surface) !important;
    border: 1px solid var(--wu-input-border) !important;
    color: var(--wu-text) !important;
    box-shadow: none !important;
}
.inventory-page-shell .inventory-header-actions .wt-btn:hover,
.maintenance-page-shell .page-header-block .wt-btn:hover,
.duplicate-page .page-header-block .wt-btn:hover,
.special-page-shell .page-header-block .wt-btn:hover {
    background: var(--wu-surface-alt) !important;
    border-color: #94a3b8 !important;
}

/* ══════════════════════════════════════════════════════════
   3. FILTER BAR — unified across all 4 pages
   ══════════════════════════════════════════════════════════ */
.clean-admin-filter,
.duplicate-search-panel,
.special-filter-panel,
.wt-data-filter {
    display: grid !important;
    grid-template-columns: minmax(220px, 1fr) 180px 80px !important;
    align-items: end !important;
    gap: 10px !important;
    width: 100% !important;
    margin: 0 !important;
    padding: 10px 12px !important;
    border: 1px solid var(--wu-border-soft) !important;
    border-radius: 8px !important;
    background: var(--wu-filter-bg) !important;
    box-shadow: none !important;
    overflow: visible !important;
    white-space: normal !important;
    flex-direction: unset !important;
    flex-wrap: unset !important;
    min-height: unset !important;
}
/* Duplicate page has extra "Done" filter — extend the grid */
.duplicate-search-panel {
    grid-template-columns: minmax(180px, 1fr) 140px 140px 80px !important;
}
/* Label above each filter field */
.clean-admin-label,
.duplicate-filter-field label,
.special-filter-field label,
.wt-data-field label,
.clean-admin-field label {
    display: block !important;
    margin: 0 0 5px !important;
    font-size: 9px !important;
    font-weight: 700 !important;
    letter-spacing: .1em !important;
    line-height: 1 !important;
    text-transform: uppercase !important;
    color: var(--wu-text-muted) !important;
    white-space: nowrap !important;
    flex: none !important;
}
/* The filter field wrappers */
.clean-admin-filter .clean-admin-filter-grid,
.clean-admin-filter > div,
.duplicate-filter-field,
.special-filter-field,
.wt-data-field {
    display: block !important;
    flex: none !important;
    width: 100% !important;
}
/* All input/select/reset controls */
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
    width: 100% !important;
    height: 32px !important;
    min-height: 32px !important;
    padding: 0 10px !important;
    border: 1px solid var(--wu-input-border) !important;
    border-radius: 6px !important;
    background: var(--wu-input-bg) !important;
    color: var(--wu-input-text) !important;
    font-size: 11px !important;
    font-weight: 500 !important;
    line-height: 1 !important;
    outline: none !important;
    box-shadow: none !important;
    appearance: none !important;
    -webkit-appearance: none !important;
}
.clean-admin-select,
.duplicate-filter-select,
.special-filter-select,
.wt-data-select {
    padding-right: 26px !important;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 16 16'%3E%3Cpath fill='%2394a3b8' d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E") !important;
    background-repeat: no-repeat !important;
    background-position: right 8px center !important;
}
.clean-admin-reset,
.duplicate-filter-reset,
.special-filter-reset,
.wt-data-reset {
    cursor: pointer !important;
    font-weight: 700 !important;
    padding: 0 8px !important;
}
.clean-admin-input:focus,
.clean-admin-select:focus,
.duplicate-search:focus,
.duplicate-filter-select:focus,
.special-filter-input:focus,
.special-filter-select:focus,
.wt-data-input:focus,
.wt-data-select:focus {
    border-color: #0284c7 !important;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, .14) !important;
}

/* ── Filter bar responsive ── */
@media (max-width: 768px) {
    .clean-admin-filter,
    .duplicate-search-panel,
    .special-filter-panel,
    .wt-data-filter {
        grid-template-columns: 1fr !important;
    }
    .clean-admin-reset,
    .duplicate-filter-reset,
    .special-filter-reset,
    .wt-data-reset {
        width: 100% !important;
    }
}

/* ══════════════════════════════════════════════════════════
   4. TABLE SHELL & SCROLL CONTAINER
   ══════════════════════════════════════════════════════════ */
.inventory-table-shell,
.clean-admin-table-shell,
.duplicate-table-shell,
.special-table-shell,
#mainTableContainer {
    border: 1px solid var(--wu-border-soft) !important;
    border-radius: 8px !important;
    background: var(--wu-surface) !important;
    overflow: hidden !important;
    box-shadow: none !important;
    width: 100% !important;
}

/* ── CRITICAL: No horizontal overflow ──
   The table must stay within 100% of the container.
   We achieve this by:
   1. Setting table-layout: fixed so columns honour widths
   2. Setting width: 100% with no min-width
   3. Hiding overflow on cells with text-overflow: ellipsis
*/

/* Shared table resets */
.inventory-page-shell #walkiesTable.clean-admin-table,
.maintenance-page-shell #maintenanceTable.clean-admin-table,
.duplicate-table-shell #duplicateTable,
.special-table-shell #specialTable {
    width: 100% !important;
    min-width: 0 !important;
    max-width: 100% !important;
    margin: 0 !important;
    border-collapse: collapse !important;
    border: 0 !important;
    table-layout: fixed !important;
}

/* ── INVENTORY LIST column widths ──
   7 cols: [checkbox] Radio ID | Status | Serial No | Model | Assigned To | Action
*/
.inventory-page-shell #walkiesTable.clean-admin-table :is(th,td):nth-child(1) { width: 38px !important; min-width: 38px !important; max-width: 38px !important; }
.inventory-page-shell #walkiesTable.clean-admin-table :is(th,td):nth-child(2) { width: 9%  !important; }
.inventory-page-shell #walkiesTable.clean-admin-table :is(th,td):nth-child(3) { width: 10% !important; }
.inventory-page-shell #walkiesTable.clean-admin-table :is(th,td):nth-child(4) { width: 13% !important; }
.inventory-page-shell #walkiesTable.clean-admin-table :is(th,td):nth-child(5) { width: 10% !important; }
.inventory-page-shell #walkiesTable.clean-admin-table :is(th,td):nth-child(6) { width: auto !important; }  /* Assigned To: takes remaining */
.inventory-page-shell #walkiesTable.clean-admin-table :is(th,td):nth-child(7) { width: 22% !important; }   /* Action */

/* ── REPAIR/FAULTY column widths ──
   14 cols: Radio ID | Serial | Model | Ownership | Dept | Date | Reporter | Issue | Status | Done | FinishDate | Remarks | Progress | Action
*/
.maintenance-page-shell #maintenanceTable.clean-admin-table :is(th,td):nth-child(1)  { width: 6%  !important; }
.maintenance-page-shell #maintenanceTable.clean-admin-table :is(th,td):nth-child(2)  { width: 8%  !important; }
.maintenance-page-shell #maintenanceTable.clean-admin-table :is(th,td):nth-child(3)  { width: 6%  !important; }
.maintenance-page-shell #maintenanceTable.clean-admin-table :is(th,td):nth-child(4)  { width: 6%  !important; }
.maintenance-page-shell #maintenanceTable.clean-admin-table :is(th,td):nth-child(5)  { width: 7%  !important; }
.maintenance-page-shell #maintenanceTable.clean-admin-table :is(th,td):nth-child(6)  { width: 7%  !important; }
.maintenance-page-shell #maintenanceTable.clean-admin-table :is(th,td):nth-child(7)  { width: 8%  !important; }
.maintenance-page-shell #maintenanceTable.clean-admin-table :is(th,td):nth-child(8)  { width: 9%  !important; }
.maintenance-page-shell #maintenanceTable.clean-admin-table :is(th,td):nth-child(9)  { width: 7%  !important; }
.maintenance-page-shell #maintenanceTable.clean-admin-table :is(th,td):nth-child(10) { width: 4%  !important; }
.maintenance-page-shell #maintenanceTable.clean-admin-table :is(th,td):nth-child(11) { width: 6%  !important; }
.maintenance-page-shell #maintenanceTable.clean-admin-table :is(th,td):nth-child(12) { width: 8%  !important; }
.maintenance-page-shell #maintenanceTable.clean-admin-table :is(th,td):nth-child(13) { width: 7%  !important; }
.maintenance-page-shell #maintenanceTable.clean-admin-table :is(th,td):nth-child(14) { width: 11% !important; }

/* ── DUPLICATED ID column widths ──
   7 cols: Radio ID | Status | Serial No | Model | Change ID To | Done | Action
*/
.duplicate-table-shell #duplicateTable :is(th,td):nth-child(1) { width: 10% !important; }
.duplicate-table-shell #duplicateTable :is(th,td):nth-child(2) { width: 11% !important; }
.duplicate-table-shell #duplicateTable :is(th,td):nth-child(3) { width: 15% !important; }
.duplicate-table-shell #duplicateTable :is(th,td):nth-child(4) { width: 13% !important; }
.duplicate-table-shell #duplicateTable :is(th,td):nth-child(5) { width: 14% !important; }
.duplicate-table-shell #duplicateTable :is(th,td):nth-child(6) { width: 8%  !important; }
.duplicate-table-shell #duplicateTable :is(th,td):nth-child(7) { width: 29% !important; }

/* ── SPECIAL USE column widths ──
   7 cols: Radio ID | Status | Serial No | Model | Assigned To | Returned | Action
*/
.special-table-shell #specialTable :is(th,td):nth-child(1) { width: 8%  !important; }
.special-table-shell #specialTable :is(th,td):nth-child(2) { width: 9%  !important; }
.special-table-shell #specialTable :is(th,td):nth-child(3) { width: 13% !important; }
.special-table-shell #specialTable :is(th,td):nth-child(4) { width: 9%  !important; }
.special-table-shell #specialTable :is(th,td):nth-child(5) { width: auto !important; }  /* Assigned To */
.special-table-shell #specialTable :is(th,td):nth-child(6) { width: 7%  !important; }
.special-table-shell #specialTable :is(th,td):nth-child(7) { width: 25% !important; }

/* ══════════════════════════════════════════════════════════
   5. TABLE HEADER — unified
   ══════════════════════════════════════════════════════════ */
.inventory-page-shell #walkiesTable.clean-admin-table thead th,
.maintenance-page-shell #maintenanceTable.clean-admin-table thead th,
.duplicate-table-shell #duplicateTable thead th,
.special-table-shell #specialTable thead th {
    height: 32px !important;
    padding: 0 8px !important;
    background: var(--wu-head-bg) !important;
    border: 1px solid var(--wu-border-soft) !important;
    color: var(--wu-text-head) !important;
    font-size: 9px !important;
    font-weight: 700 !important;
    letter-spacing: .08em !important;
    line-height: 1 !important;
    text-transform: uppercase !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    vertical-align: middle !important;
    text-align: left !important;
}

/* ══════════════════════════════════════════════════════════
   6. TABLE BODY CELLS — unified
   ══════════════════════════════════════════════════════════ */
.inventory-page-shell #walkiesTable.clean-admin-table tbody td,
.maintenance-page-shell #maintenanceTable.clean-admin-table tbody td,
.duplicate-table-shell #duplicateTable tbody td,
.special-table-shell #specialTable tbody td {
    height: 32px !important;
    padding: 4px 8px !important;
    background: var(--wu-surface) !important;
    border: 1px solid var(--wu-border) !important;
    color: var(--wu-text) !important;
    font-size: 10px !important;
    font-weight: 500 !important;
    line-height: 1.2 !important;
    vertical-align: middle !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
}
/* Row hover */
.inventory-page-shell #walkiesTable.clean-admin-table tbody tr:hover td,
.maintenance-page-shell #maintenanceTable.clean-admin-table tbody tr:hover td,
.duplicate-table-shell #duplicateTable tbody tr:hover td,
.special-table-shell #specialTable tbody tr:hover td {
    background: var(--wu-row-hover) !important;
}
/* Allow wrapping in long-text columns (Issue, Remarks, Assigned To) */
.maintenance-page-shell #maintenanceTable.clean-admin-table tbody td:nth-child(8),
.maintenance-page-shell #maintenanceTable.clean-admin-table tbody td:nth-child(12),
.inventory-page-shell #walkiesTable.clean-admin-table tbody td:nth-child(6),
.special-table-shell #specialTable tbody td:nth-child(5) {
    white-space: normal !important;
    word-break: break-word !important;
    line-height: 1.3 !important;
    padding-top: 5px !important;
    padding-bottom: 5px !important;
}
/* Centre-aligned columns (Status, Done/Yes/No, Action) */
.inventory-page-shell #walkiesTable.clean-admin-table :is(th,td):nth-child(3),
.inventory-page-shell #walkiesTable.clean-admin-table :is(th,td):nth-child(7),
.maintenance-page-shell #maintenanceTable.clean-admin-table :is(th,td):nth-child(9),
.maintenance-page-shell #maintenanceTable.clean-admin-table :is(th,td):nth-child(10),
.maintenance-page-shell #maintenanceTable.clean-admin-table :is(th,td):nth-child(14),
.duplicate-table-shell #duplicateTable :is(th,td):nth-child(2),
.duplicate-table-shell #duplicateTable :is(th,td):nth-child(6),
.duplicate-table-shell #duplicateTable :is(th,td):nth-child(7),
.special-table-shell #specialTable :is(th,td):nth-child(2),
.special-table-shell #specialTable :is(th,td):nth-child(6),
.special-table-shell #specialTable :is(th,td):nth-child(7) {
    text-align: center !important;
}

/* ══════════════════════════════════════════════════════════
   7. STATUS BADGES — unified semantic colours
   ══════════════════════════════════════════════════════════ */

/* Base badge reset — applies to all existing badge variants */
.clean-admin-pill,
.dup-status-badge,
.dup-done-badge,
.maintenance-status-pill,
.maintenance-done-pill,
.wt-status-badge,
[class*="status-pill"],
[class*="status-badge"] {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 2px 7px !important;
    border-radius: 4px !important;
    border: 1px solid transparent !important;
    font-size: 9px !important;
    font-weight: 700 !important;
    letter-spacing: .04em !important;
    text-transform: uppercase !important;
    line-height: 1.2 !important;
    white-space: nowrap !important;
}

/* Status: UNUSED */
[data-status="UNUSED"],
.dup-status-badge.is-unused {
    background: var(--wu-badge-unused-bg) !important;
    border-color: var(--wu-badge-unused-bdr) !important;
    color: var(--wu-badge-unused-txt) !important;
}
/* Status: IN USE */
[data-status="IN USE"],
.dup-status-badge.is-in-use {
    background: var(--wu-badge-inuse-bg) !important;
    border-color: var(--wu-badge-inuse-bdr) !important;
    color: var(--wu-badge-inuse-txt) !important;
}
/* Status: REPAIRING / FAULTY / UNDER REPAIR */
[data-status="REPAIRING"],
[data-status="FAULTY"],
[data-status="UNDER REPAIR"],
.dup-status-badge.is-repairing {
    background: var(--wu-badge-repair-bg) !important;
    border-color: var(--wu-badge-repair-bdr) !important;
    color: var(--wu-badge-repair-txt) !important;
}
/* Status: B.E.R / LOST / UNKNOWN / CALIBRATING */
[data-status="B.E.R"],
[data-status="LOST"],
[data-status="UNKNOWN"],
[data-status="CALIBRATING"],
.dup-status-badge.is-unknown {
    background: var(--wu-badge-ber-bg) !important;
    border-color: var(--wu-badge-ber-bdr) !important;
    color: var(--wu-badge-ber-txt) !important;
}
/* Status: WAITING FOR ADMIN / PENDING ADMIN IT */
[data-status="WAITING FOR ADMIN"],
[data-status="PENDING ADMIN IT"] {
    background: var(--wu-badge-wait-bg) !important;
    border-color: var(--wu-badge-wait-bdr) !important;
    color: var(--wu-badge-wait-txt) !important;
}
/* Status: READY TO COLLECT */
[data-status="READY TO COLLECT"] {
    background: var(--wu-badge-ready-bg) !important;
    border-color: var(--wu-badge-ready-bdr) !important;
    color: var(--wu-badge-ready-txt) !important;
}
/* Status: DONE / ALREADY FIXED */
[data-status="DONE"],
[data-status="ALREADY FIXED"],
[data-done="YES"],
.dup-done-badge.is-yes,
.maintenance-done-pill[data-done="YES"],
.maintenance-done-pill.maintenance-done-yes {
    background: var(--wu-badge-done-bg) !important;
    border-color: var(--wu-badge-done-bdr) !important;
    color: var(--wu-badge-done-txt) !important;
}
/* Done: NO */
[data-done="NO"],
.dup-done-badge.is-no,
.maintenance-done-pill[data-done="NO"],
.maintenance-done-pill.maintenance-done-no {
    background: var(--wu-badge-repair-bg) !important;
    border-color: var(--wu-badge-repair-bdr) !important;
    color: var(--wu-badge-repair-txt) !important;
}
/* Special use badge generic override */
.dup-status-badge.is-change-id {
    background: var(--wu-badge-wait-bg) !important;
    border-color: var(--wu-badge-wait-bdr) !important;
    color: var(--wu-badge-wait-txt) !important;
}
.dup-status-badge.is-other {
    background: var(--wu-surface-alt) !important;
    border-color: var(--wu-border-soft) !important;
    color: var(--wu-text-muted) !important;
}

/* ══════════════════════════════════════════════════════════
   8. ACTION BUTTONS — unified style
   ══════════════════════════════════════════════════════════ */
/* Wrapper */
.clean-admin-actions,
.dup-actions,
.special-action-buttons {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 4px !important;
    flex-wrap: nowrap !important;
    width: 100% !important;
    white-space: nowrap !important;
}
.dup-actions form,
.special-action-buttons form {
    display: inline-flex !important;
    margin: 0 !important;
}

/* Common button base */
.clean-admin-actions .wt-btn,
.clean-admin-actions .wt-btn-sm,
#duplicateTable .dup-actions .btn,
#specialTable .special-action-buttons .btn {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 4px !important;
    height: 26px !important;
    min-height: 26px !important;
    min-width: 50px !important;
    padding: 0 8px !important;
    border: 1px solid transparent !important;
    border-radius: 5px !important;
    font-size: 9px !important;
    font-weight: 700 !important;
    letter-spacing: .04em !important;
    line-height: 1 !important;
    text-decoration: none !important;
    white-space: nowrap !important;
    cursor: pointer !important;
    box-shadow: none !important;
    text-transform: none !important;
}
/* View (info/teal) */
.clean-admin-actions .wt-btn:not(.wt-btn-danger),
.clean-admin-actions .wt-btn-soft,
#duplicateTable .dup-actions .btn-info,
#specialTable .special-action-buttons .btn-info {
    background: var(--wu-btn-view-bg) !important;
    border-color: var(--wu-btn-view-bg) !important;
    color: #ffffff !important;
}
/* Edit (blue) */
#duplicateTable .dup-actions .btn-primary,
#specialTable .special-action-buttons .btn-primary {
    background: var(--wu-btn-edit-bg) !important;
    border-color: var(--wu-btn-edit-bg) !important;
    color: #ffffff !important;
}
/* Delete (red) */
.clean-admin-actions .wt-btn-danger,
.clean-admin-actions .wt-btn.wt-btn-danger,
#duplicateTable .dup-actions .btn-danger,
#specialTable .special-action-buttons .btn-danger {
    background: var(--wu-btn-del-bg) !important;
    border-color: var(--wu-btn-del-bg) !important;
    color: #ffffff !important;
}

/* ══════════════════════════════════════════════════════════
   9. TABLE FOOTER & PAGINATION — unified
   ══════════════════════════════════════════════════════════ */
.repair-table-footer,
.duplicate-table-footer,
.special-table-footer,
.inventory-table-footer,
.wt-data-footer {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 10px !important;
    flex-wrap: wrap !important;
    min-height: 46px !important;
    padding: 8px 14px !important;
    border-top: 1px solid var(--wu-border-soft) !important;
    background: var(--wu-surface) !important;
}
/* Info text */
.repair-table-info,
.duplicate-table-info,
.special-table-info,
.inventory-table-info,
.wt-data-info {
    font-size: 12px !important;
    font-weight: 700 !important;
    color: var(--wu-text) !important;
    white-space: nowrap !important;
}
/* Pagination container */
.repair-table-pagination,
.duplicate-table-pagination,
.special-table-pagination,
.wt-data-pagination {
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
    flex-wrap: wrap !important;
}
/* Page buttons */
.repair-page-btn,
.repair-page-number,
.duplicate-page-link,
.special-page-link,
.wt-data-page {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-width: 32px !important;
    height: 30px !important;
    padding: 0 8px !important;
    border: 1px solid var(--wu-page-btn-border) !important;
    border-radius: 6px !important;
    background: var(--wu-page-btn-bg) !important;
    color: var(--wu-page-btn-text) !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    line-height: 1 !important;
    cursor: pointer !important;
    transition: all .14s ease !important;
    text-decoration: none !important;
    box-shadow: none !important;
}
.repair-page-btn.is-nav,
.duplicate-page-link.is-nav,
.special-page-link.is-nav,
.wt-data-page.is-nav {
    min-width: 68px !important;
    font-size: 11px !important;
}
.repair-page-number.is-active,
.duplicate-page-link.is-active,
.special-page-link.is-active,
.wt-data-page.is-active {
    background: var(--wu-page-active-bg) !important;
    border-color: var(--wu-page-active-bg) !important;
    color: var(--wu-page-active-text) !important;
}
.repair-page-btn:disabled,
.duplicate-page-link:disabled,
.special-page-link:disabled,
.wt-data-page:disabled {
    opacity: .38 !important;
    cursor: not-allowed !important;
}
.repair-page-btn:hover:not(:disabled),
.repair-page-number:hover,
.duplicate-page-link:hover:not(:disabled),
.special-page-link:hover:not(:disabled),
.wt-data-page:hover:not(:disabled):not(.is-active) {
    background: var(--wu-surface-alt) !important;
    border-color: #0284c7 !important;
    color: #0284c7 !important;
}

/* ══════════════════════════════════════════════════════════
   10. REPAIR PROGRESS BAR — unified
   ══════════════════════════════════════════════════════════ */
.maintenance-page-shell .clean-admin-table tbody td.maintenance-progress-col {
    text-align: left !important;
    padding: 4px 8px !important;
    white-space: nowrap !important;
}
.maintenance-page-shell .clean-admin-table .bg-slate-700\/80 {
    background: var(--wu-border-soft) !important;
    border: 1px solid transparent !important;
    border-radius: 3px !important;
    height: 4px !important;
    width: 64px !important;
    overflow: hidden !important;
    display: block !important;
    margin-bottom: 2px !important;
}

/* ══════════════════════════════════════════════════════════
   11. DUPLICATE-PAGE STATS ROW — unified
   ══════════════════════════════════════════════════════════ */
.dup-stats-row {
    display: flex !important;
    gap: 8px !important;
    flex-wrap: wrap !important;
}
.dup-stat-card {
    display: flex !important;
    flex-direction: column !important;
    gap: 2px !important;
    padding: 8px 14px !important;
    border-radius: 8px !important;
    border: 1px solid var(--wu-border-soft) !important;
    background: var(--wu-surface) !important;
    min-width: 100px !important;
}
.dup-stat-num {
    font-size: 20px !important;
    font-weight: 900 !important;
    color: var(--wu-text) !important;
    line-height: 1 !important;
}
.dup-stat-lbl {
    font-size: 9px !important;
    font-weight: 700 !important;
    letter-spacing: .1em !important;
    text-transform: uppercase !important;
    color: var(--wu-text-muted) !important;
}
.dup-stat-card.is-pending { border-color: #fde68a !important; }
.dup-stat-card.is-pending .dup-stat-num { color: #b45309 !important; }
.dup-stat-card.is-done { border-color: #86efac !important; }
.dup-stat-card.is-done .dup-stat-num { color: #166534 !important; }
.dup-stat-card.is-visible { border-color: #93c5fd !important; }
.dup-stat-card.is-visible .dup-stat-num { color: #1d4ed8 !important; }
html.dark .dup-stat-card { background: var(--wu-surface) !important; border-color: var(--wu-border) !important; }
html.dark .dup-stat-card.is-pending .dup-stat-num { color: #fbbf24 !important; }
html.dark .dup-stat-card.is-done .dup-stat-num { color: #4ade80 !important; }
html.dark .dup-stat-card.is-visible .dup-stat-num { color: #38bdf8 !important; }

/* ══════════════════════════════════════════════════════════
   12. DUPLICATE SORT ICONS
   ══════════════════════════════════════════════════════════ */
.dup-sort-icon {
    display: inline-block !important;
    margin-left: 3px !important;
    opacity: .4 !important;
    font-style: normal !important;
    font-size: 8px !important;
}
th.sort-asc .dup-sort-icon,
th.sort-desc .dup-sort-icon {
    opacity: 1 !important;
    color: #0284c7 !important;
}
.duplicate-table-shell #duplicateTable thead th.sortable { cursor: pointer !important; }
.duplicate-table-shell #duplicateTable thead th.sortable:hover { background: var(--wu-row-hover) !important; }
.duplicate-table-shell #duplicateTable thead th.sort-asc,
.duplicate-table-shell #duplicateTable thead th.sort-desc {
    background: #dbeafe !important;
    color: #0369a1 !important;
    border-bottom-color: #0369a1 !important;
}
html.dark .duplicate-table-shell #duplicateTable thead th.sort-asc,
html.dark .duplicate-table-shell #duplicateTable thead th.sort-desc {
    background: #1e3a8a !important;
    color: #38bdf8 !important;
    border-bottom-color: #38bdf8 !important;
}

/* ══════════════════════════════════════════════════════════
   13. ALERTS
   ══════════════════════════════════════════════════════════ */
.alert-success,
.alert-error {
    display: flex !important;
    align-items: flex-start !important;
    gap: 8px !important;
    padding: 10px 14px !important;
    border-radius: 8px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    animation: wu-slide-down .25s ease !important;
}
.alert-success { background: #f0fdf4 !important; color: #166534 !important; border: 1px solid #bbf7d0 !important; }
.alert-error   { background: #fef2f2 !important; color: #991b1b !important; border: 1px solid #fecaca !important; }
@keyframes wu-slide-down {
    from { opacity: 0; transform: translateY(-4px); }
    to   { opacity: 1; transform: translateY(0);    }
}

/* ══════════════════════════════════════════════════════════
   14. RADIO ID "CHIP" — small grey box for ID numbers
   ══════════════════════════════════════════════════════════ */
.wu-radio-id {
    display: inline-block !important;
    padding: 1px 6px !important;
    border-radius: 4px !important;
    background: var(--wu-head-bg) !important;
    border: 1px solid var(--wu-border-soft) !important;
    color: var(--wu-text) !important;
    font-size: 9px !important;
    font-weight: 700 !important;
    font-variant-numeric: tabular-nums !important;
    letter-spacing: .04em !important;
}

/* ══════════════════════════════════════════════════════════
   15. SCROLL CONTAINER — thin, themed scrollbar when needed
   ══════════════════════════════════════════════════════════ */
.clean-admin-table-scroll,
.duplicate-table-scroll,
.special-table-scroll {
    overflow: hidden !important;  /* no horizontal scroll needed with fixed layout */
}

/* ══════════════════════════════════════════════════════════
   16. MOBILE FALLBACK — allow scroll on very small screens
   ══════════════════════════════════════════════════════════ */
@media (max-width: 900px) {
    .clean-admin-table-scroll,
    .duplicate-table-scroll,
    .special-table-scroll {
        overflow-x: auto !important;
        scrollbar-width: thin !important;
        scrollbar-color: #94a3b8 transparent !important;
    }
    .inventory-page-shell #walkiesTable.clean-admin-table,
    .maintenance-page-shell #maintenanceTable.clean-admin-table,
    .duplicate-table-shell #duplicateTable,
    .special-table-shell #specialTable {
        min-width: 680px !important;
    }
    .repair-table-footer,
    .duplicate-table-footer,
    .special-table-footer,
    .inventory-table-footer,
    .wt-data-footer {
        flex-direction: column !important;
        align-items: flex-start !important;
    }
}
</style>
