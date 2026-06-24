@extends('wt.layouts.admin')

@section('title', 'Duplicated ID Management')

@section('content')

@include('wt.admin.partials.inventory-management-ui')

<style>
/* ── Duplicated ID Page ── */

body .content-surface:has(#duplicateTable) {
    padding: 10px !important;
    background: #0b1220 !important;
    border: 0 !important;
    border-radius: 0 !important;
    box-shadow: none !important;
}
html:not(.dark) body .content-surface:has(#duplicateTable) {
    background: #f0f4f8 !important;
}

/* ── Hero ── */
body .content-surface .duplicate-page .page-title-standard {
    margin: 0 !important;
    color: #f8fafc !important;
    font-size: 19px !important;
    font-weight: 900 !important;
    line-height: 1.1 !important;
}
body .content-surface .duplicate-page .page-subtitle-standard {
    margin-top: 5px !important;
    color: #93a4ba !important;
    font-size: 9px !important;
    font-weight: 900 !important;
    letter-spacing: .16em !important;
    line-height: 1.45 !important;
    text-transform: uppercase !important;
}
body .content-surface .duplicate-hero .wt-btn {
    width: auto !important;
    min-width: 118px !important;
    height: 34px !important;
    min-height: 34px !important;
    padding: 0 12px !important;
    border-radius: 10px !important;
    border: 1px solid rgba(148,163,184,.24) !important;
    background: #111827 !important;
    color: #f8fafc !important;
    font-size: 12px !important;
    font-weight: 900 !important;
    box-shadow: none !important;
}
html:not(.dark) body .content-surface .duplicate-page .page-title-standard { color: #0f172a !important; }
html:not(.dark) body .content-surface .duplicate-page .page-subtitle-standard { color: #526781 !important; }
html:not(.dark) body .content-surface .duplicate-hero .wt-btn {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
    color: #0f172a !important;
}

/* ── Stats Row ── */
.dup-stats-row {
    display: flex !important;
    gap: 8px !important;
    flex-wrap: wrap !important;
}
.dup-stat-card {
    display: flex !important;
    flex-direction: column !important;
    align-items: flex-start !important;
    gap: 2px !important;
    padding: 10px 16px !important;
    border-radius: 10px !important;
    border: 1px solid rgba(148,163,184,.16) !important;
    background: #0f172a !important;
    min-width: 110px !important;
}
.dup-stat-num {
    font-size: 22px !important;
    font-weight: 900 !important;
    line-height: 1 !important;
    color: #f1f5f9 !important;
}
.dup-stat-lbl {
    font-size: 9px !important;
    font-weight: 700 !important;
    letter-spacing: .12em !important;
    text-transform: uppercase !important;
    color: #64748b !important;
}
.dup-stat-card.is-pending { border-color: rgba(251,191,36,.22) !important; background: #130f01 !important; }
.dup-stat-card.is-pending .dup-stat-num { color: #fbbf24 !important; }
.dup-stat-card.is-done { border-color: rgba(74,222,128,.2) !important; background: #011308 !important; }
.dup-stat-card.is-done .dup-stat-num { color: #4ade80 !important; }
.dup-stat-card.is-visible { border-color: rgba(56,189,248,.18) !important; background: #00111c !important; }
.dup-stat-card.is-visible .dup-stat-num { color: #38bdf8 !important; }

html:not(.dark) .dup-stat-card { background: #ffffff !important; border-color: #d6e0ec !important; }
html:not(.dark) .dup-stat-num { color: #1e293b !important; }
html:not(.dark) .dup-stat-lbl { color: #64748b !important; }
html:not(.dark) .dup-stat-card.is-pending { background: #fffbeb !important; border-color: #fde68a !important; }
html:not(.dark) .dup-stat-card.is-pending .dup-stat-num { color: #b45309 !important; }
html:not(.dark) .dup-stat-card.is-done { background: #f0fdf4 !important; border-color: #bbf7d0 !important; }
html:not(.dark) .dup-stat-card.is-done .dup-stat-num { color: #166534 !important; }
html:not(.dark) .dup-stat-card.is-visible { background: #f0f9ff !important; border-color: #bae6fd !important; }
html:not(.dark) .dup-stat-card.is-visible .dup-stat-num { color: #0369a1 !important; }

/* ── Filter panel ── */
body .content-surface .duplicate-search-panel {
    display: grid !important;
    grid-template-columns: minmax(200px,1fr) 150px 140px auto !important;
    gap: 10px !important;
    align-items: end !important;
    margin: 0 !important;
    padding: 12px !important;
    border: 1px solid rgba(148,163,184,.18) !important;
    border-radius: 14px !important;
    background: #0f172a !important;
    box-shadow: none !important;
}
body .content-surface .duplicate-filter-field label {
    display: block !important;
    margin: 0 0 6px !important;
    color: #8ea0b8 !important;
    font-size: 9px !important;
    font-weight: 700 !important;
    letter-spacing: .12em !important;
    line-height: 1 !important;
    text-transform: uppercase !important;
}
body .content-surface .duplicate-search,
body .content-surface .duplicate-filter-select,
body .content-surface .duplicate-filter-reset {
    height: 38px !important;
    min-height: 38px !important;
    border-radius: 10px !important;
    border: 1px solid rgba(148,163,184,.26) !important;
    background: #111827 !important;
    color: #e5edf7 !important;
    font-size: 12px !important;
    font-weight: 500 !important;
    box-shadow: none !important;
    outline: none !important;
}
body .content-surface .duplicate-search { padding: 0 14px !important; width: 100% !important; }
body .content-surface .duplicate-filter-select { padding: 0 30px 0 14px !important; width: 100% !important; }
body .content-surface .duplicate-filter-reset {
    padding: 0 18px !important;
    width: auto !important;
    font-weight: 800 !important;
    cursor: pointer !important;
    background: transparent !important;
    color: #dbeafe !important;
}
html:not(.dark) body .content-surface .duplicate-search-panel {
    background: #ffffff !important;
    border-color: #d6e0ec !important;
}
html:not(.dark) body .content-surface .duplicate-filter-field label { color: #64748b !important; }
html:not(.dark) body .content-surface .duplicate-search,
html:not(.dark) body .content-surface .duplicate-filter-select,
html:not(.dark) body .content-surface .duplicate-filter-reset {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
    color: #1f2937 !important;
}

/* ── Table shell ── */
body .content-surface .duplicate-table-shell {
    border: 1px solid #263244 !important;
    border-radius: 6px !important;
    background: #111827 !important;
    padding: 0 !important;
    overflow: hidden !important;
    box-shadow: none !important;
}
body .content-surface .duplicate-table-scroll { overflow-x: auto !important; }

body .content-surface .duplicate-table-shell #duplicateTable {
    min-width: 1485px !important;
    width: 100% !important;
    margin: 0 !important;
    border-collapse: collapse !important;
    border: 0 !important;
    table-layout: fixed !important;
}

/* ── Column widths — driven by <col> elements; these are fallback overrides ── */
#duplicateTable col:nth-child(1)  { width: 75px; }
#duplicateTable col:nth-child(2)  { width: 100px; }
#duplicateTable col:nth-child(3)  { width: 130px; }
#duplicateTable col:nth-child(4)  { width: 80px; }
#duplicateTable col:nth-child(5)  { width: 115px; }
#duplicateTable col:nth-child(6)  { width: 155px; }
#duplicateTable col:nth-child(7)  { width: 145px; }
#duplicateTable col:nth-child(8)  { width: 110px; }
#duplicateTable col:nth-child(9)  { width: 70px; }
#duplicateTable col:nth-child(10) { width: 120px; }
#duplicateTable col:nth-child(11) { width: 185px; }
#duplicateTable col:nth-child(12) { width: 175px; }

/* Centre the Done and Action columns */
#duplicateTable th:nth-child(9),
#duplicateTable td:nth-child(9),
#duplicateTable th:nth-child(12),
#duplicateTable td:nth-child(12) { text-align: center !important; }

/* ── Sortable headers ── */
body .content-surface .duplicate-table-shell #duplicateTable thead th {
    height: 34px !important;
    padding: 8px 10px !important;
    background: #1f2937 !important;
    border: 1px solid #2f3b4f !important;
    color: #cbd5e1 !important;
    font-size: 10px !important;
    font-weight: 600 !important;
    line-height: 1.1 !important;
    letter-spacing: .05em !important;
    text-transform: uppercase !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    user-select: none !important;
}
body .content-surface .duplicate-table-shell #duplicateTable thead th.sortable {
    cursor: pointer !important;
}
body .content-surface .duplicate-table-shell #duplicateTable thead th.sortable:hover {
    background: #273548 !important;
    color: #e2e8f0 !important;
}
body .content-surface .duplicate-table-shell #duplicateTable thead th.sort-asc,
body .content-surface .duplicate-table-shell #duplicateTable thead th.sort-desc {
    background: #1a2f48 !important;
    color: #38bdf8 !important;
    border-bottom-color: #38bdf8 !important;
}
.dup-sort-icon {
    display: inline-block !important;
    margin-left: 4px !important;
    opacity: 0.4 !important;
    font-style: normal !important;
    font-size: 9px !important;
}
th.sort-asc .dup-sort-icon,
th.sort-desc .dup-sort-icon {
    opacity: 1 !important;
    color: #38bdf8 !important;
}

/* ── Body cells ── */
body .content-surface .duplicate-table-shell #duplicateTable tbody td {
    height: 38px !important;
    padding: 7px 10px !important;
    background: #111827 !important;
    border: 1px solid #263244 !important;
    color: #dbe4f0 !important;
    font-size: 11px !important;
    font-weight: 400 !important;
    line-height: 1.25 !important;
    vertical-align: middle !important;
    overflow: hidden !important;
    white-space: nowrap !important;
    text-overflow: ellipsis !important;
    transition: background .1s !important;
}
body .content-surface .duplicate-table-shell #duplicateTable :is(th,td):nth-child(11) {
    white-space: normal !important;
    overflow-wrap: anywhere !important;
    text-overflow: clip !important;
}

/* ── Row group alternating highlight ── */
body .content-surface .duplicate-table-shell #duplicateTable tbody tr[data-group="1"] td {
    background: #0f1e30 !important;
}
body .content-surface .duplicate-table-shell #duplicateTable tbody tr[data-group="0"] td {
    background: #111827 !important;
}

/* ── Row hover ── */
body .content-surface .duplicate-table-shell #duplicateTable tbody tr:hover td {
    background: #172033 !important;
}

/* ── Done row: subtle mute ── */
body .content-surface .duplicate-table-shell #duplicateTable tbody tr.is-done-row td {
    opacity: 0.7 !important;
}
body .content-surface .duplicate-table-shell #duplicateTable tbody tr.is-done-row:hover td {
    opacity: 1 !important;
}

/* ── Light mode table ── */
html:not(.dark) body .content-surface .duplicate-table-shell {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
}
html:not(.dark) body .content-surface .duplicate-table-shell #duplicateTable thead th {
    background: #e8eef5 !important;
    border-color: #d6e0ec !important;
    color: #475569 !important;
}
html:not(.dark) body .content-surface .duplicate-table-shell #duplicateTable thead th.sortable:hover {
    background: #dce5f0 !important;
}
html:not(.dark) body .content-surface .duplicate-table-shell #duplicateTable thead th.sort-asc,
html:not(.dark) body .content-surface .duplicate-table-shell #duplicateTable thead th.sort-desc {
    background: #dbeafe !important;
    color: #0369a1 !important;
    border-bottom-color: #0369a1 !important;
}
html:not(.dark) th.sort-asc .dup-sort-icon,
html:not(.dark) th.sort-desc .dup-sort-icon { color: #0369a1 !important; }
html:not(.dark) body .content-surface .duplicate-table-shell #duplicateTable tbody td {
    background: #ffffff !important;
    border-color: #e2e8f0 !important;
    color: #334155 !important;
}
html:not(.dark) body .content-surface .duplicate-table-shell #duplicateTable tbody tr[data-group="1"] td {
    background: #f8fafc !important;
}
html:not(.dark) body .content-surface .duplicate-table-shell #duplicateTable tbody tr[data-group="0"] td {
    background: #ffffff !important;
}
html:not(.dark) body .content-surface .duplicate-table-shell #duplicateTable tbody tr:hover td {
    background: #f0f9ff !important;
}
html:not(.dark) body .content-surface .duplicate-table-shell #duplicateTable tbody tr.is-done-row td {
    opacity: 0.6 !important;
}

/* ── Footer ── */
body .content-surface .duplicate-table-footer {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 12px !important;
    min-height: 44px !important;
    padding: 7px 12px !important;
    background: #111827 !important;
    border-top: 1px solid #263244 !important;
}
body .content-surface .duplicate-table-info {
    color: #dbeafe !important;
    font-size: 12px !important;
    font-weight: 800 !important;
}
body .content-surface .duplicate-table-pagination {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
}
body .content-surface .duplicate-page-link {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-width: 32px !important;
    height: 28px !important;
    padding: 0 8px !important;
    border-radius: 6px !important;
    border: 1px solid #2f4d74 !important;
    background: #0f172a !important;
    color: #bfdbfe !important;
    font-size: 11px !important;
    font-weight: 800 !important;
    cursor: pointer !important;
}
body .content-surface .duplicate-page-link.is-nav { min-width: 64px !important; color: #cbd5e1 !important; }
body .content-surface .duplicate-page-link.is-active { background: #0f3a72 !important; border-color: #3b82f6 !important; color: #ffffff !important; }
body .content-surface .duplicate-page-link:disabled { opacity: .35 !important; cursor: not-allowed !important; }
html:not(.dark) body .content-surface .duplicate-table-footer { background: #ffffff !important; border-top-color: #d6e0ec !important; }
html:not(.dark) body .content-surface .duplicate-table-info { color: #334155 !important; }
html:not(.dark) body .content-surface .duplicate-page-link { background: #ffffff !important; border-color: #cbd5e1 !important; color: #334155 !important; }
html:not(.dark) body .content-surface .duplicate-page-link.is-active { background: #0284c7 !important; border-color: #0284c7 !important; color: #ffffff !important; }

/* ── Action buttons ── */
body .content-surface #duplicateTable .dup-actions {
    display: flex !important;
    flex-wrap: nowrap !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 4px !important;
}
body .content-surface #duplicateTable .dup-actions form { display: inline-flex !important; margin: 0 !important; }
body .content-surface #duplicateTable .btn {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 4px !important;
    height: 26px !important;
    min-height: 26px !important;
    padding: 0 8px !important;
    border: 1px solid transparent !important;
    border-radius: 5px !important;
    color: #ffffff !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    line-height: 1 !important;
    text-decoration: none !important;
    cursor: pointer !important;
    box-shadow: none !important;
}
body .content-surface #duplicateTable .btn i { font-size: 11px !important; }
body .content-surface #duplicateTable .btn-info    { border-color: #0dcaf0 !important; background: #0dcaf0 !important; color: #052c33 !important; }
body .content-surface #duplicateTable .btn-primary { border-color: #0d6efd !important; background: #0d6efd !important; }
body .content-surface #duplicateTable .btn-danger  { border-color: #dc3545 !important; background: #dc3545 !important; }

/* ── Status badges with per-status colors ── */
.dup-status-badge {
    display: inline-flex !important;
    border-radius: 4px !important;
    padding: 2px 8px !important;
    font-size: 10px !important;
    font-weight: 900 !important;
    text-transform: uppercase !important;
    letter-spacing: .04em !important;
}
.dup-status-badge.is-in-use    { background: #052e16 !important; border: 1px solid #166534 !important; color: #4ade80 !important; }
.dup-status-badge.is-repairing { background: #1a0e00 !important; border: 1px solid #92400e !important; color: #fbbf24 !important; }
.dup-status-badge.is-unknown   { background: #1e293b !important; border: 1px solid #475569 !important; color: #94a3b8 !important; }
.dup-status-badge.is-unused    { background: #0f172a !important; border: 1px solid #334155 !important; color: #64748b !important; }
.dup-status-badge.is-change-id { background: #1c0a00 !important; border: 1px solid #c2410c !important; color: #fb923c !important; }
.dup-status-badge.is-other     { background: #1f2937 !important; border: 1px solid #4b5563 !important; color: #d1d5db !important; }

html:not(.dark) .dup-status-badge.is-in-use    { background: #dcfce7 !important; border-color: #86efac !important; color: #166534 !important; }
html:not(.dark) .dup-status-badge.is-repairing { background: #fffbeb !important; border-color: #fde68a !important; color: #92400e !important; }
html:not(.dark) .dup-status-badge.is-unknown   { background: #f1f5f9 !important; border-color: #cbd5e1 !important; color: #475569 !important; }
html:not(.dark) .dup-status-badge.is-unused    { background: #f8fafc !important; border-color: #e2e8f0 !important; color: #64748b !important; }
html:not(.dark) .dup-status-badge.is-change-id { background: #fff7ed !important; border-color: #fdba74 !important; color: #c2410c !important; }
html:not(.dark) .dup-status-badge.is-other     { background: #e2e8f0 !important; border-color: #cbd5e1 !important; color: #334155 !important; }

/* ── Done badge ── */
.dup-done-badge {
    display: inline-flex !important;
    border-radius: 4px !important;
    padding: 2px 8px !important;
    font-size: 10px !important;
    font-weight: 900 !important;
    text-transform: uppercase !important;
    letter-spacing: .04em !important;
}
.dup-done-badge.is-yes { background: #052e16 !important; border: 1px solid #166534 !important; color: #4ade80 !important; }
.dup-done-badge.is-no  { background: #2d0a0a !important; border: 1px solid #7f1d1d !important; color: #f87171 !important; }
html:not(.dark) .dup-done-badge.is-yes { background: #dcfce7 !important; border-color: #86efac !important; color: #166534 !important; }
html:not(.dark) .dup-done-badge.is-no  { background: #fef2f2 !important; border-color: #fca5a5 !important; color: #b91c1c !important; }

/* ── "Change ID To" cell accent ── */
.dup-change-id-val {
    font-weight: 700 !important;
    color: #38bdf8 !important;
}
.dup-change-id-empty {
    color: #374151 !important;
}
html:not(.dark) .dup-change-id-val  { color: #0369a1 !important; }
html:not(.dark) .dup-change-id-empty { color: #94a3b8 !important; }

/* ── Scrollbar ── */
body .content-surface .duplicate-table-scroll::-webkit-scrollbar { height: 8px; }
body .content-surface .duplicate-table-scroll::-webkit-scrollbar-track { background: #111827; }
body .content-surface .duplicate-table-scroll::-webkit-scrollbar-thumb { background: #374151; border-radius: 999px; }
</style>

@php
    $totalRecords = $records->count();
    $pendingCount = $records->filter(fn($r) => (int)($r->id_change_done ?? 0) !== 1)->count();
    $doneCount    = $records->filter(fn($r) => (int)($r->id_change_done ?? 0) === 1)->count();
@endphp

<div class="duplicate-page" style="display:flex;flex-direction:column;gap:10px;">

    {{-- Header --}}
    <div class="duplicate-hero">
        <div class="page-header-block flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h1 class="page-title-standard">Duplicated ID Management</h1>
                <p class="page-subtitle-standard">Review units that share a Radio ID or still need an ID change.</p>
            </div>
            @if(auth('wt')->user()->wt_role === 'admin_it')
            <div class="flex flex-wrap items-center gap-2">
                <button onclick="openImportModal()" class="wt-btn wt-btn-soft">
                    <i class="fa-solid fa-file-import"></i> Import Excel
                </button>
                <a href="{{ route('wt.admin.walkies.create.duplicate') }}" class="wt-btn wt-btn-soft">
                    <i class="fa-solid fa-plus"></i> Add Item
                </a>
            </div>
            @endif
        </div>
    </div>

    {{-- Stats Bar --}}
    <div class="dup-stats-row">
        <div class="dup-stat-card">
            <span class="dup-stat-num" id="statTotal">{{ $totalRecords }}</span>
            <span class="dup-stat-lbl">Total Records</span>
        </div>
        <div class="dup-stat-card is-pending">
            <span class="dup-stat-num" id="statPending">{{ $pendingCount }}</span>
            <span class="dup-stat-lbl">Pending Change</span>
        </div>
        <div class="dup-stat-card is-done">
            <span class="dup-stat-num" id="statDone">{{ $doneCount }}</span>
            <span class="dup-stat-lbl">ID Changed</span>
        </div>
        <div class="dup-stat-card is-visible">
            <span class="dup-stat-num" id="statVisible">{{ $totalRecords }}</span>
            <span class="dup-stat-lbl">Showing</span>
        </div>
    </div>

    {{-- Filters --}}
    <div class="duplicate-search-panel" role="search" aria-label="Duplicated ID filters">
        <div class="duplicate-filter-field">
            <label for="duplicateSearchInput">Search</label>
            <input id="duplicateSearchInput" type="search" class="duplicate-search" placeholder="Radio ID, serial, ownership…">
        </div>
        <div class="duplicate-filter-field">
            <label for="duplicateStatusFilter">Status</label>
            <select id="duplicateStatusFilter" class="duplicate-filter-select">
                <option value="">All Status</option>
                @foreach(['IN USE', 'REPAIRING', 'UNKNOWN', 'UNUSED', 'CHANGE ID'] as $statusOption)
                    <option value="{{ $statusOption }}">{{ $statusOption }}</option>
                @endforeach
            </select>
        </div>
        <div class="duplicate-filter-field">
            <label for="duplicateDoneFilter">ID Change Done</label>
            <select id="duplicateDoneFilter" class="duplicate-filter-select">
                <option value="">All</option>
                <option value="YES">Done</option>
                <option value="NO">Pending</option>
            </select>
        </div>
        <button type="button" id="duplicateResetFilters" class="duplicate-filter-reset">Reset</button>
    </div>

    @if(session('success'))
    <div id="alertBox" style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;color:#15803d;font-size:12px;font-weight:600;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Table --}}
    <div id="mainTableContainer" class="duplicate-table-shell">
        <div id="duplicateTableScroll" class="duplicate-table-scroll">
            <table id="duplicateTable">
                <colgroup>
                    <col style="width:75px">
                    <col style="width:100px">
                    <col style="width:130px">
                    <col style="width:80px">
                    <col style="width:110px">
                    <col style="width:70px">
                    <col style="width:200px">
                </colgroup>
                <thead>
                    <tr>
                        <th class="sortable" data-col="0" data-type="num">Radio ID <i class="dup-sort-icon">↕</i></th>
                        <th>Status</th>
                        <th class="sortable" data-col="2" data-type="text">Serial No. <i class="dup-sort-icon">↕</i></th>
                        <th class="sortable" data-col="3" data-type="text">Model <i class="dup-sort-icon">↕</i></th>
                        <th class="sortable" data-col="4" data-type="num">Change ID To <i class="dup-sort-icon">&#8597;</i></th>
                        <th>Done</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $r)
                    @php
                        $done = (int)($r->id_change_done ?? 0) === 1;
                        $statusClass = match(strtoupper($r->status ?? '')) {
                            'IN USE'    => 'is-in-use',
                            'REPAIRING' => 'is-repairing',
                            'UNKNOWN'   => 'is-unknown',
                            'UNUSED'    => 'is-unused',
                            'CHANGE ID' => 'is-change-id',
                            default     => 'is-other',
                        };
                    @endphp
                    <tr class="duplicate-row{{ $done ? ' is-done-row' : '' }}"
                        data-status="{{ strtoupper((string)($r->status ?? '')) }}"
                        data-done="{{ $done ? 'YES' : 'NO' }}"
                        data-search="{{ strtoupper(trim(($r->radio_id ?? '') . ' ' . ($r->serial_number ?? '') . ' ' . ($r->model ?? '') . ' ' . ($r->status ?? '') . ' ' . ($r->ownership_type ?? '') . ' ' . ($r->shared_with ?? '') . ' ' . ($r->ownership ?? '') . ' ' . ($r->department ?? '') . ' ' . ($r->remark ?? '') . ' ' . ($r->need_to_change_id ?? '') . ' ' . ($r->ownership_type_to_be ?? ''))) }}">
                        <td style="font-weight:700;">{{ $r->radio_id ?: '-' }}</td>
                        <td>
                            <span class="dup-status-badge {{ $statusClass }}">{{ $r->status ?: '-' }}</span>
                        </td>
                        <td>{{ $r->serial_number ?: '-' }}</td>
                        <td>{{ $r->model ?: '-' }}</td>
                        <td>
                            @if($r->need_to_change_id)
                                <span class="dup-change-id-val">{{ $r->need_to_change_id }}</span>
                            @else
                                <span class="dup-change-id-empty">—</span>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            <span class="dup-done-badge {{ $done ? 'is-yes' : 'is-no' }}">{{ $done ? 'YES' : 'NO' }}</span>
                        </td>
                        <td style="text-align:center;">
                            @if(auth('wt')->user()->wt_role === 'admin_it')
                            <div class="dup-actions">
                                <button type="button" class="btn btn-info" onclick="openGlobalWalkieTimeline('{{ $r->walkie_id }}')">
                                    <i class="fa-solid fa-eye"></i><span>View</span>
                                </button>
                                <a href="{{ route('wt.admin.walkies.edit', ['walkie' => $r->walkie_id, 'source' => 'duplicate']) }}" class="btn btn-primary">
                                    <i class="fa-solid fa-edit"></i><span>Edit</span>
                                </a>
                                <form action="{{ route('wt.admin.walkies.destroy', $r->walkie_id) }}" method="POST" class="inline" data-modern-confirm="Delete duplicated ID record for {{ $r->radio_id ?? '-' }}?">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fa-solid fa-trash"></i><span>Delete</span>
                                    </button>
                                </form>
                            </div>
                            @else
                            <button type="button" class="btn btn-info" onclick="openGlobalWalkieTimeline('{{ $r->walkie_id }}')">
                                <i class="fa-solid fa-eye"></i><span>View</span>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="duplicate-table-footer">
            <div class="duplicate-table-info">Showing <span id="duplicateTotalItems">0</span> records</div>
            <div class="duplicate-table-pagination" id="dupPaginationBar"></div>
        </div>
    </div>

</div>

{{-- ===================== ADD RECORD MODAL ===================== --}}
@if(auth('wt')->user()->wt_role === 'admin_it')
<div id="addModal" class="modal-overlay" onclick="closeAddModalOutside(event)">
    <div class="modal-box">
        <div class="modal-header">
            <h2 class="modal-title">Add Duplicated ID Record</h2>
            <button onclick="closeAddModal()" class="modal-close-btn"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('wt.admin.walkies.store') }}" method="POST" class="flex flex-col h-full overflow-hidden">
            @csrf
            <input type="hidden" name="is_duplicated" value="1">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Radio ID</label>
                        <input type="text" name="radio_id" class="form-input" placeholder="e.g. 2212">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-input">
                            @foreach(['IN USE','REPAIRING','UNKNOWN','UNUSED'] as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Serial No. <span class="required">*</span></label>
                        <input type="text" name="serial_number" class="form-input" placeholder="e.g. 977TPA0829" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Model (MC)</label>
                        <select name="model" class="form-input">
                            <option value="">-- Leave Empty --</option>
                            @foreach(['R7','P8200','P8268','P8600I','P8660I','P8260'] as $m)
                            <option value="{{ $m }}">{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Current Ownership Type</label>
                        <select name="ownership_type" class="form-input ownership-type-control">
                            @foreach(['INDIVIDUAL','SPARE','SHARED'] as $ot)
                            <option value="{{ $ot }}">{{ $ot }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group shared-with-group hidden">
                        <label class="form-label">Shared With <span class="required">*</span></label>
                        <input type="text" name="shared_with" class="form-input shared-with-input" placeholder="E.G. USER / TEAM / DEPARTMENT">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Current Ownership</label>
                        <input type="text" name="ownership" class="form-input" placeholder="Current ownership">
                    </div>
                    <div class="form-group" style="grid-column:span 3;">
                        <label class="form-label">Department</label>
                        <input type="text" name="department" list="department-options" class="form-input" placeholder="Department">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Need To Change Into</label>
                        <input type="text" name="need_to_change_id" class="form-input" placeholder="Target Radio ID e.g. 2220">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Done</label>
                        <select name="id_change_done" class="form-input">
                            <option value="0">NO</option>
                            <option value="1">YES</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ownership Type To Be</label>
                        <select name="ownership_type_to_be" class="form-input">
                            <option value="">-- None --</option>
                            @foreach(['SPARE','UNALLOCATED'] as $tot)
                            <option value="{{ $tot }}">{{ $tot }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="grid-column:span 3;">
                        <label class="form-label">Remarks</label>
                        <textarea name="remark" class="form-input" style="height:60px;resize:none;" placeholder="Remarks"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeAddModal()" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-submit">Save Record</button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- ===================== EDIT MODAL ===================== --}}
<div id="editModal" class="modal-overlay" onclick="closeEditModalOutside(event)">
    <div class="modal-box" id="editModalBox">
        <div class="modal-header">
            <h2 class="modal-title">Update Unit Details</h2>
            <button onclick="closeEditModal()" class="modal-close-btn"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" id="editWalkieForm" class="flex flex-col h-full overflow-hidden">
            @csrf
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Radio ID <span class="required">*</span></label>
                        <input type="text" name="radio_id" id="edit_radio_id" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Serial No. <span class="required">*</span></label>
                        <input type="text" name="serial_number" id="edit_serial_number" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Model <span class="required">*</span></label>
                        <select name="model" id="edit_model" class="form-input" required>
                            @foreach(['R7','P8200','P8268','P8600I','P8660I','P8260'] as $editModel)
                            <option value="{{ $editModel }}">{{ $editModel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status <span class="required">*</span></label>
                        <select name="status" id="edit_status" class="form-input" required>
                            @foreach(['IN USE','REPAIRING','UNKNOWN','UNUSED'] as $editStatus)
                            <option value="{{ $editStatus }}">{{ $editStatus }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ownership Type <span class="required">*</span></label>
                        <select name="ownership_type" id="edit_ownership_type" class="form-input ownership-type-control" required>
                            @foreach(['INDIVIDUAL','SPARE','SHARED'] as $editOwnershipType)
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
                        <input type="text" name="ownership" id="edit_ownership" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Position</label>
                        <input type="text" name="position" id="edit_position" list="position-options" class="form-input">
                    </div>
                    <div class="form-group" style="grid-column:span 2;">
                        <label class="form-label">Department</label>
                        <input type="text" name="department" id="edit_department" list="department-options" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Temp / Swapped Radio ID</label>
                        <input type="text" name="temporary_radio_id" id="edit_temporary_radio_id" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tracking REF</label>
                        <input type="text" name="tracking_ref" id="edit_tracking_ref" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Need To Change ID</label>
                        <input type="text" name="need_to_change_id" id="edit_need_to_change_id" class="form-input" placeholder="Target Radio ID">
                    </div>
                    <div class="form-group">
                        <label class="form-label">ID Change Done</label>
                        <select name="id_change_done" id="edit_id_change_done" class="form-input">
                            <option value="0">NO</option>
                            <option value="1">YES</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ownership Type To Be</label>
                        <select name="ownership_type_to_be" id="edit_ownership_type_to_be" class="form-input">
                            <option value="">Select target...</option>
                            @foreach(['SPARE','UNALLOCATED'] as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="grid-column:span 3;">
                        <label class="form-label">Remarks</label>
                        <textarea name="remark" id="edit_remark" class="form-input" style="height:60px;resize:none;"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeEditModal()" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-submit">Save Changes</button>
            </div>
        </form>
    </div>
</div>

{{-- ===================== IMPORT MODAL ===================== --}}
@if(auth('wt')->user()->wt_role === 'admin_it')
<div id="importModal" class="modal-overlay" onclick="closeImportModalOutside(event)">
    <div class="modal-box" style="max-width:480px;">
        <div class="modal-header">
            <h2 class="modal-title">Bulk Import Duplicated IDs</h2>
            <button onclick="closeImportModal()" class="modal-close-btn"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('wt.admin.walkies.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body" style="padding:24px;">
                <div style="border:2px dashed var(--border);border-radius:12px;padding:32px;text-align:center;background:var(--body-bg);">
                    <input type="file" name="file" id="import_file" class="hidden" required onchange="updateFileName(this)">
                    <label for="import_file" style="display:block;cursor:pointer;">
                        <div style="width:48px;height:48px;border-radius:50%;background:var(--surface);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                            <i class="fas fa-cloud-upload-alt" style="font-size:18px;color:var(--accent);"></i>
                        </div>
                        <p id="fileNameDisplay" style="font-size:12px;font-weight:600;color:var(--muted);">Click to upload Excel or CSV</p>
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
@endif

<script>
$(document).ready(function () {
    const searchInput  = document.getElementById('duplicateSearchInput');
    const statusFilter = document.getElementById('duplicateStatusFilter');
    const doneFilter   = document.getElementById('duplicateDoneFilter');
    const resetBtn     = document.getElementById('duplicateResetFilters');
    const allRows      = Array.from(document.querySelectorAll('#duplicateTable tbody .duplicate-row'));
    const totalEl      = document.getElementById('duplicateTotalItems');
    const paginationEl = document.getElementById('dupPaginationBar');
    const statVisible  = document.getElementById('statVisible');
    const statPending  = document.getElementById('statPending');
    const statDone     = document.getElementById('statDone');
    const PER_PAGE     = 15;
    let currentPage    = 1;
    let filtered       = [];
    let sortCol        = 0;
    let sortDir        = 'asc';

    /* ── Sorting ── */
    function getSortVal(row, col) {
        const text = (row.cells[col]?.textContent || '').trim();
        if (col === 0 || col === 7) {
            const n = parseFloat(text.replace(/[^0-9.\-]/g, ''));
            if (!isNaN(n)) return n;
        }
        return text.toLowerCase();
    }

    function sortRows(rows) {
        return [...rows].sort((a, b) => {
            const av = getSortVal(a, sortCol);
            const bv = getSortVal(b, sortCol);
            if (typeof av === 'number' && typeof bv === 'number')
                return sortDir === 'asc' ? av - bv : bv - av;
            return sortDir === 'asc'
                ? String(av).localeCompare(String(bv))
                : String(bv).localeCompare(String(av));
        });
    }

    function assignGroups(rows) {
        let gIdx = 0, lastId = null;
        rows.forEach(r => {
            const rid = (r.cells[0]?.textContent || '').trim();
            if (rid !== lastId) { gIdx++; lastId = rid; }
            r.setAttribute('data-group', gIdx % 2);
        });
    }

    /* ── Sort header indicators ── */
    document.querySelectorAll('#duplicateTable thead th.sortable').forEach(th => {
        th.addEventListener('click', () => {
            const col = parseInt(th.dataset.col);
            if (sortCol === col) {
                sortDir = sortDir === 'asc' ? 'desc' : 'asc';
            } else {
                sortCol = col;
                sortDir = 'asc';
            }
            updateSortHeaders();
            currentPage = 1;
            applyFilter();
        });
    });

    function updateSortHeaders() {
        document.querySelectorAll('#duplicateTable thead th.sortable').forEach(th => {
            const col = parseInt(th.dataset.col);
            const icon = th.querySelector('.dup-sort-icon');
            th.classList.remove('sort-asc', 'sort-desc');
            if (icon) icon.textContent = '↕';
            if (col === sortCol) {
                th.classList.add(sortDir === 'asc' ? 'sort-asc' : 'sort-desc');
                if (icon) icon.textContent = sortDir === 'asc' ? '↑' : '↓';
            }
        });
    }

    /* ── Render ── */
    function render() {
        const sorted = sortRows(filtered);
        assignGroups(sorted);

        allRows.forEach(r => r.style.display = 'none');
        const start = (currentPage - 1) * PER_PAGE;
        sorted.slice(start, start + PER_PAGE).forEach(r => r.style.display = '');

        const pending = filtered.filter(r => r.dataset.done === 'NO').length;
        const done    = filtered.filter(r => r.dataset.done === 'YES').length;

        if (totalEl)   totalEl.textContent  = filtered.length;
        if (statVisible) statVisible.textContent = filtered.length;
        if (statPending) statPending.textContent  = pending;
        if (statDone)    statDone.textContent     = done;

        renderPagination(sorted.length);
    }

    function renderPagination(total) {
        if (!paginationEl) return;
        const pages = Math.ceil(total / PER_PAGE);
        if (pages <= 1) { paginationEl.innerHTML = ''; return; }
        let html = `<button class="duplicate-page-link is-nav" ${currentPage===1?'disabled':''} onclick="dupPage(${currentPage-1})">‹ Prev</button>`;
        for (let i = 1; i <= pages; i++) {
            if (i===1 || i===pages || Math.abs(i-currentPage)<=1)
                html += `<button class="duplicate-page-link${i===currentPage?' is-active':''}" onclick="dupPage(${i})">${i}</button>`;
            else if (Math.abs(i-currentPage)===2)
                html += `<span style="padding:0 2px;color:#64748b;font-size:11px">…</span>`;
        }
        html += `<button class="duplicate-page-link is-nav" ${currentPage===pages?'disabled':''} onclick="dupPage(${currentPage+1})">Next ›</button>`;
        paginationEl.innerHTML = html;
    }

    window.dupPage = function (p) { currentPage = p; render(); };

    function normalizeFilterValue(value) {
        return String(value || '').trim().replace(/\s+/g, ' ').toUpperCase();
    }

    function applyFilter() {
        const s  = normalizeFilterValue(searchInput?.value);
        const st = normalizeFilterValue(statusFilter?.value);
        const dn = normalizeFilterValue(doneFilter?.value);
        filtered = allRows.filter(r =>
            (!s  || normalizeFilterValue(r.dataset.search).includes(s)) &&
            (!st || normalizeFilterValue(r.dataset.status) === st) &&
            (!dn || normalizeFilterValue(r.dataset.done) === dn)
        );
        currentPage = 1;
        render();
    }

    if (searchInput)  searchInput.addEventListener('input', applyFilter);
    if (statusFilter) statusFilter.addEventListener('change', applyFilter);
    if (doneFilter)   doneFilter.addEventListener('change', applyFilter);
    if (resetBtn) resetBtn.addEventListener('click', () => {
        if (searchInput)  searchInput.value = '';
        if (statusFilter) statusFilter.value = '';
        if (doneFilter)   doneFilter.value = '';
        applyFilter();
    });

    /* Default sort: Radio ID ascending */
    updateSortHeaders();
    applyFilter();
});

function openEditModal(id,radio,serialNumber,model,status,ownershipType,ownership,position,department,temporaryRadioId,trackingRef,remark,needToChangeId,idChangeDone,ownershipTypeToBe){
    const form=document.getElementById('editWalkieForm');
    form.action="{{ route('wt.admin.walkies.updateMeta',['walkie'=>'__ID__']) }}".replace('__ID__',id);
    document.getElementById('edit_radio_id').value=radio||'';
    document.getElementById('edit_serial_number').value=serialNumber||'';
    document.getElementById('edit_model').value=model||'';
    document.getElementById('edit_status').value=status||'';
    document.getElementById('edit_ownership_type').value=ownershipType||'';
    document.getElementById('edit_ownership').value=ownership||'';
    document.getElementById('edit_position').value=position||'';
    document.getElementById('edit_department').value=department||'';
    document.getElementById('edit_temporary_radio_id').value=temporaryRadioId||'';
    document.getElementById('edit_tracking_ref').value=trackingRef||'';
    document.getElementById('edit_remark').value=remark||'';
    document.getElementById('edit_need_to_change_id').value=needToChangeId||'';
    document.getElementById('edit_id_change_done').value=idChangeDone||'0';
    document.getElementById('edit_ownership_type_to_be').value=ownershipTypeToBe||'';
    document.getElementById('editModal').classList.add('active');
    document.body.style.overflow='hidden';
}
function closeEditModal(){ document.getElementById('editModal').classList.remove('active'); document.body.style.overflow=''; }
function closeEditModalOutside(e){ if(e.target===document.getElementById('editModal')) closeEditModal(); }

function syncSharedWith(select){
    const form=select.closest('form'); if(!form) return;
    const isShared=(select.value||'').toUpperCase()==='SHARED';
    const g=form.querySelector('.shared-with-group'), i=form.querySelector('.shared-with-input');
    if(g) g.classList.toggle('hidden',!isShared);
    if(i){ i.required=isShared; if(!isShared) i.value=''; }
}
document.querySelectorAll('.ownership-type-control').forEach(s=>{ s.addEventListener('change',()=>syncSharedWith(s)); syncSharedWith(s); });

function openImportModal(){ document.getElementById('importModal').classList.add('active'); document.body.style.overflow='hidden'; }
function closeImportModal(){ document.getElementById('importModal').classList.remove('active'); document.body.style.overflow=''; }
function closeImportModalOutside(e){ if(e.target===document.getElementById('importModal')) closeImportModal(); }
function openAddModal(){ document.getElementById('addModal').classList.add('active'); document.body.style.overflow='hidden'; }
function closeAddModal(){ document.getElementById('addModal').classList.remove('active'); document.body.style.overflow=''; }
function closeAddModalOutside(e){ if(e.target===document.getElementById('addModal')) closeAddModal(); }
function updateFileName(input){
    const el=document.getElementById('fileNameDisplay');
    el.textContent=input.files?.length ? 'Selected: '+input.files[0].name : 'Click to upload Excel or CSV';
    el.style.color=input.files?.length ? '#16a34a' : '';
}
document.addEventListener('keydown', e => {
    if(e.key==='Escape'){ closeEditModal(); closeAddModal(); closeImportModal(); }
});
</script>
@include('wt.admin.partials.inventory-tools-table-skin')
<style id="duplicate-compact-standard-final">
body .content-surface .duplicate-table-shell {
    border: 1px solid #cbd5e1 !important;
    border-radius: 8px !important;
    background: #ffffff !important;
    overflow: hidden !important;
    box-shadow: none !important;
}

body .content-surface .duplicate-table-scroll {
    background: #ffffff !important;
}

body .content-surface .duplicate-table-shell #duplicateTable {
    width: 100% !important;
    min-width: 860px !important;
    border: 0 !important;
    border-collapse: collapse !important;
    table-layout: fixed !important;
}

body .content-surface .duplicate-table-shell #duplicateTable thead th {
    height: 46px !important;
    padding: 0 16px !important;
    border: 1px solid #d8e1ed !important;
    background: #eef3f8 !important;
    color: #526781 !important;
    font-size: 13px !important;
    font-weight: 900 !important;
    letter-spacing: .06em !important;
    line-height: 1.15 !important;
    text-transform: uppercase !important;
    white-space: nowrap !important;
}

body .content-surface .duplicate-table-shell #duplicateTable tbody td {
    height: 38px !important;
    padding: 8px 16px !important;
    border: 1px solid #e2e8f0 !important;
    background: #ffffff !important;
    color: #1f2937 !important;
    font-size: 12px !important;
    font-weight: 650 !important;
    line-height: 1.25 !important;
    vertical-align: middle !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

body .content-surface .duplicate-table-shell #duplicateTable tbody tr:hover td {
    background: #f8fafc !important;
}

body .content-surface .duplicate-table-shell #duplicateTable tbody tr.is-done-row td {
    opacity: 1 !important;
}

body .content-surface .duplicate-table-shell #duplicateTable :is(th, td):nth-child(1) { width: 12% !important; }
body .content-surface .duplicate-table-shell #duplicateTable :is(th, td):nth-child(2) { width: 14% !important; }
body .content-surface .duplicate-table-shell #duplicateTable :is(th, td):nth-child(3) { width: 16% !important; }
body .content-surface .duplicate-table-shell #duplicateTable :is(th, td):nth-child(4) { width: 14% !important; }
body .content-surface .duplicate-table-shell #duplicateTable :is(th, td):nth-child(5) { width: 14% !important; }
body .content-surface .duplicate-table-shell #duplicateTable :is(th, td):nth-child(6) { width: 10% !important; text-align: center !important; }
body .content-surface .duplicate-table-shell #duplicateTable :is(th, td):nth-child(7) { width: 20% !important; text-align: center !important; }

body .content-surface #duplicateTable .dup-actions {
    display: flex !important;
    flex-wrap: nowrap !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
}

body .content-surface #duplicateTable .dup-actions form {
    display: inline-flex !important;
    margin: 0 !important;
}

body .content-surface #duplicateTable .btn {
    min-width: 64px !important;
    width: auto !important;
    height: 28px !important;
    min-height: 28px !important;
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
    box-shadow: none !important;
}

body .content-surface #duplicateTable .btn-info {
    border-color: #0284c7 !important;
    background: #0284c7 !important;
    color: #ffffff !important;
}

body .content-surface #duplicateTable .btn-primary {
    border-color: #2563eb !important;
    background: #2563eb !important;
    color: #ffffff !important;
}

body .content-surface #duplicateTable .btn-danger {
    border-color: #dc2626 !important;
    background: #dc2626 !important;
    color: #ffffff !important;
}

body .content-surface .duplicate-table-footer {
    min-height: 54px !important;
    padding: 10px 16px !important;
    border-top: 1px solid #d8e1ed !important;
    background: #ffffff !important;
}

body .content-surface .duplicate-table-info {
    color: #020617 !important;
    font-size: 13px !important;
    font-weight: 900 !important;
}

html.dark body .content-surface .duplicate-table-shell,
html.dark body .content-surface .duplicate-table-scroll,
html.dark body .content-surface .duplicate-table-shell #duplicateTable,
html.dark body .content-surface .duplicate-table-shell #duplicateTable tbody tr,
html.dark body .content-surface .duplicate-table-footer {
    border-color: #334155 !important;
    background: #111827 !important;
}

html.dark body .content-surface .duplicate-table-shell #duplicateTable thead th {
    border-color: #334155 !important;
    background: #1f2937 !important;
    color: #cbd5e1 !important;
}

html.dark body .content-surface .duplicate-table-shell #duplicateTable tbody td {
    border-color: #334155 !important;
    background: #111827 !important;
    color: #dbe4f0 !important;
}

html.dark body .content-surface .duplicate-table-shell #duplicateTable tbody tr:hover td {
    background: #172033 !important;
}

html.dark body .content-surface .duplicate-table-info {
    color: #dbeafe !important;
}

body .content-surface .duplicate-search-panel {
    display: flex !important;
    flex-direction: row !important;
    flex-wrap: nowrap !important;
    align-items: center !important;
    gap: 12px !important;
    padding: 10px 12px !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 9px !important;
    background: #ffffff !important;
    box-shadow: none !important;
}

body .content-surface .duplicate-filter-field {
    display: inline-flex !important;
    flex-direction: row !important;
    align-items: center !important;
    gap: 7px !important;
    min-width: 0 !important;
}

body .content-surface .duplicate-filter-field label {
    display: inline-flex !important;
    flex: 0 0 auto !important;
    margin: 0 !important;
    color: #526781 !important;
    font-size: 11px !important;
    font-weight: 800 !important;
    letter-spacing: .14em !important;
    line-height: 1 !important;
    text-transform: uppercase !important;
    white-space: nowrap !important;
}

body .content-surface .duplicate-search,
body .content-surface .duplicate-filter-select,
body .content-surface .duplicate-filter-reset {
    width: 100% !important;
    height: 34px !important;
    min-height: 34px !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 7px !important;
    background: #ffffff !important;
    color: #0f172a !important;
    font-size: 13px !important;
    font-weight: 650 !important;
    line-height: 1 !important;
    box-shadow: none !important;
}

body .content-surface .duplicate-search {
    flex: 1 1 260px !important;
}

body .content-surface .duplicate-filter-field:has(.duplicate-search) {
    flex: 1 1 360px !important;
}

body .content-surface .duplicate-filter-field:has(.duplicate-filter-select) {
    flex: 0 0 auto !important;
}

body .content-surface .duplicate-search {
    padding: 0 12px !important;
    min-width: 190px !important;
}

body .content-surface .duplicate-filter-select {
    padding: 0 30px 0 12px !important;
    width: auto !important;
    min-width: 100px !important;
}

body .content-surface .duplicate-filter-reset {
    flex: 0 0 auto !important;
    width: auto !important;
    min-width: 78px !important;
    padding: 0 10px !important;
    font-size: 12px !important;
    font-weight: 900 !important;
}

body .content-surface .duplicate-table-shell #duplicateTable {
    min-width: 1040px !important;
}

body .content-surface .duplicate-table-shell #duplicateTable :is(th, td):nth-child(7) {
    width: 260px !important;
    min-width: 260px !important;
    max-width: 260px !important;
}

body .content-surface #duplicateTable .dup-actions {
    display: inline-flex !important;
    flex-direction: row !important;
    flex-wrap: nowrap !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    width: auto !important;
    min-width: 0 !important;
    white-space: nowrap !important;
}

body .content-surface #duplicateTable .dup-actions .btn,
body .content-surface #duplicateTable .dup-actions button,
body .content-surface #duplicateTable .dup-actions a {
    width: auto !important;
    min-width: 68px !important;
    max-width: none !important;
    height: 30px !important;
    min-height: 30px !important;
    padding: 0 10px !important;
    border-radius: 7px !important;
    font-size: 11px !important;
    font-weight: 900 !important;
    letter-spacing: .02em !important;
    text-transform: none !important;
}

html.dark body .content-surface .duplicate-search-panel {
    border-color: #334155 !important;
    background: #111827 !important;
}

html.dark body .content-surface .duplicate-filter-field label {
    color: #94a3b8 !important;
}

html.dark body .content-surface .duplicate-search,
html.dark body .content-surface .duplicate-filter-select,
html.dark body .content-surface .duplicate-filter-reset {
    border-color: #334155 !important;
    background: #0f172a !important;
    color: #e2e8f0 !important;
}

@media (max-width: 900px) {
    body .content-surface .duplicate-search-panel {
        flex-wrap: wrap !important;
        align-items: stretch !important;
    }

    body .content-surface .duplicate-filter-field {
        flex: 1 1 240px !important;
    }

    body .content-surface .duplicate-filter-reset {
        flex: 1 1 100% !important;
    }
}
</style>
@endsection
