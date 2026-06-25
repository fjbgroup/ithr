@extends('wt.layouts.admin')

@section('title', 'Special Use Management')

@section('content')
<style>
/* Thin and sleek scrollbar style to prevent 'blank row' look */
#topScrollContainer::-webkit-scrollbar { height: 6px; }
#topScrollContainer::-webkit-scrollbar-track { background: transparent; }
#topScrollContainer::-webkit-scrollbar-thumb { background: #E7E5E4; border-radius: 10px; }
#topScrollContainer::-webkit-scrollbar-thumb:hover { background: #142b47; }
#topScrollContainer { scrollbar-width: thin; scrollbar-color: #E7E5E4 transparent; }
/* Hide scroller if no overflow */
#topScrollContainer.hidden { display: none !important; }

.special-table-shell .dataTables_wrapper .dataTables_length,
.special-table-shell .dataTables_wrapper .dataTables_filter {
    padding-top: 18px !important;
    padding-left: 18px !important;
    padding-right: 18px !important;
}

.special-table-shell .dataTables_wrapper .dataTables_info,
.special-table-shell .dataTables_wrapper .dataTables_paginate {
    padding-left: 18px !important;
    padding-right: 18px !important;
    padding-bottom: 12px !important;
}

.special-table-shell table.dataTable,
.special-table-shell .dataTables_wrapper {
    width: 100% !important;
}

.special-table-shell table.dataTable {
    table-layout: auto;
}

.special-table-shell table.dataTable thead th {
    white-space: nowrap;
}

.special-table-shell table.dataTable tbody td.dataTables_empty {
    padding: 0 !important;
    background: transparent !important;
    border: 0 !important;
    text-align: center !important;
}
.special-summary-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 12px;
    margin-bottom: 12px;
}
.special-summary-card {
    border: 1px solid #dbe3ef;
    border-radius: 8px;
    background: #ffffff;
    padding: 12px 16px;
}
.special-summary-label {
    color: #64748b;
    font-size: 10px;
    font-weight: 900;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}
.special-summary-value {
    margin-top: 6px;
    color: #0f172a;
    font-size: 30px;
    line-height: 1;
    font-weight: 900;
}
.special-summary-value.in-use { color: #2563eb; }
.special-summary-value.unused { color: #16a34a; }
.special-summary-value.repair { color: #ea580c; }
.special-filter-panel {
    display: grid;
    grid-template-columns: minmax(260px, 1fr) 270px auto;
    align-items: end;
    gap: 14px;
    margin: 0 0 16px;
    border: 1px solid #263244;
    border-radius: 8px;
    background: #111827;
    padding: 14px 18px 16px;
}
.special-filter-field label {
    display: block;
    margin-bottom: 7px;
    color: #94a3b8;
    font-size: 10px;
    line-height: 1;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}
.special-filter-input,
.special-filter-select {
    width: 100%;
    height: 48px;
    border: 1px solid #334155;
    border-radius: 8px;
    background: #0f172a;
    color: #e5e7eb;
    padding: 0 12px;
    font-size: 16px;
    font-weight: 500;
    outline: none;
}
.special-filter-input::placeholder { color: #94a3b8; }
.special-filter-input:focus,
.special-filter-select:focus {
    border-color: #38bdf8;
    box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.16);
}
.special-filter-reset {
    width: 62px;
    height: 48px;
    border: 1px solid #334155;
    border-radius: 8px;
    background: #0f172a;
    color: #e5e7eb;
    font-size: 11px;
    font-weight: 800;
    cursor: pointer;
}
.special-filter-reset:hover {
    border-color: #38bdf8;
    color: #0284c7;
}
.special-table-shell {
    border: 1px solid #263244;
    border-radius: 10px;
    background: #111827;
    padding: 0;
    overflow: hidden;
    box-shadow: none;
}
.dark .special-summary-card,
.dark .special-filter-panel,
.dark .special-table-shell {
    border-color: #263244;
    background: #111827;
}
.dark .special-summary-label,
.dark .special-filter-field label {
    color: #94a3b8;
}
.dark .special-summary-value {
    color: #f8fafc;
}
.dark .special-filter-input,
.dark .special-filter-select,
.dark .special-filter-reset {
    border-color: #334155;
    background: #0f172a;
    color: #e2e8f0;
}
body .content-surface .special-table-shell #specialTable thead th {
    height: 54px !important;
    padding: 0 20px !important;
    border: 1px solid #263244 !important;
    background: #111827 !important;
    color: #dbeafe !important;
    font-size: 17px !important;
    font-weight: 900 !important;
    line-height: 1.1 !important;
    letter-spacing: 0.02em !important;
    text-transform: uppercase !important;
}
body .content-surface .special-table-shell #specialTable tbody td {
    height: 40px !important;
    padding: 8px 20px !important;
    border: 1px solid #263244 !important;
    background: #111827 !important;
    color: #dbe4f0 !important;
    font-size: 14px !important;
    font-weight: 600 !important;
}
.content-surface .special-table-shell #specialTable tbody td.dataTables_empty {
    height: 0 !important;
    padding: 0 !important;
    border: 0 !important;
    color: transparent !important;
}
.content-surface .special-table-shell .dataTables_scrollHead,
.content-surface .special-table-shell .dataTables_scrollHeadInner,
.content-surface .special-table-shell .dataTables_scrollHead table,
.content-surface .special-table-shell .dataTables_scrollBody,
.content-surface .special-table-shell .dataTables_scrollBody table {
    background: #111827 !important;
}
.content-surface .special-table-shell .dataTables_scrollBody {
    border-top: 0 !important;
    scrollbar-width: auto;
    scrollbar-color: #526174 #111827;
}
.content-surface .special-table-shell .dataTables_scrollBody::-webkit-scrollbar {
    height: 11px !important;
}
.content-surface .special-table-shell .dataTables_scrollBody::-webkit-scrollbar-track {
    background: #111827 !important;
}
.content-surface .special-table-shell .dataTables_scrollBody::-webkit-scrollbar-thumb {
    background: #526174 !important;
    border-radius: 999px !important;
}
.content-surface .special-table-shell .adminit-table-footer {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 12px !important;
    min-height: 64px !important;
    padding: 10px 20px !important;
    border-top: 1px solid #263244 !important;
    background: #1e293b !important;
}
.content-surface .special-table-shell .adminit-table-info {
    color: #dbeafe !important;
    font-size: 17px !important;
    font-weight: 900 !important;
    letter-spacing: 0 !important;
    text-transform: none !important;
}
.content-surface .special-table-shell .adminit-table-pagination {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
}
.content-surface .special-table-shell .adminit-page-link,
.content-surface .special-table-shell .adminit-page-current {
    min-width: 52px !important;
    height: 44px !important;
    border-radius: 8px !important;
    border: 1px solid #2f4d74 !important;
    background: #111827 !important;
    color: #c7d7ee !important;
    font-size: 16px !important;
    font-weight: 900 !important;
    letter-spacing: 0 !important;
    text-transform: none !important;
}
.content-surface .special-table-shell .adminit-page-link {
    min-width: 124px !important;
    padding: 0 18px !important;
}
.content-surface .special-table-shell .adminit-page-current {
    background: #0f3a72 !important;
    border-color: #3b82f6 !important;
    color: #ffffff !important;
}
.content-surface .special-table-shell .adminit-page-link:disabled {
    opacity: 0.48 !important;
    color: #6b7788 !important;
    cursor: not-allowed !important;
}
.content-surface .special-table-shell .adminit-prev-page::before {
    content: '\2039';
    margin-right: 8px;
    font-size: 26px;
    line-height: 0;
}
.content-surface .special-table-shell .adminit-next-page::after {
    content: '\203A';
    margin-left: 8px;
    font-size: 26px;
    line-height: 0;
}
html:not(.dark) body .content-surface .special-filter-panel {
    background: #f8fafc !important;
    border-color: #d6e0ec !important;
}
html:not(.dark) body .content-surface .special-filter-field label {
    color: #64748b !important;
}
html:not(.dark) body .content-surface .special-filter-input,
html:not(.dark) body .content-surface .special-filter-select,
html:not(.dark) body .content-surface .special-filter-reset {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
    color: #1f2937 !important;
}
html:not(.dark) body .content-surface .special-table-shell,
html:not(.dark) body .content-surface .special-table-shell .dataTables_scrollHead,
html:not(.dark) body .content-surface .special-table-shell .dataTables_scrollHeadInner,
html:not(.dark) body .content-surface .special-table-shell .dataTables_scrollHead table,
html:not(.dark) body .content-surface .special-table-shell .dataTables_scrollBody,
html:not(.dark) body .content-surface .special-table-shell .dataTables_scrollBody table {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
}
html:not(.dark) body .content-surface .special-table-shell #specialTable thead th {
    background: #f8fafc !important;
    border-color: #e2e8f0 !important;
    color: #1e3a5f !important;
}
html:not(.dark) body .content-surface .special-table-shell .adminit-table-footer {
    background: #ffffff !important;
    border-top-color: #d6e0ec !important;
}
html:not(.dark) body .content-surface .special-table-shell .adminit-table-info {
    color: #334155 !important;
}
.special-filter-panel {
    margin: 0 0 10px !important;
    padding: 10px 12px !important;
    border-radius: 6px !important;
    border: 1px solid #263244 !important;
    background: #111827 !important;
}
.special-filter-field label {
    display: block !important;
    margin-bottom: 5px !important;
    color: #94a3b8 !important;
    font-size: 9px !important;
    line-height: 1 !important;
    letter-spacing: 0.08em !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
}
.special-filter-input,
.special-filter-select,
.special-filter-reset {
    width: 100% !important;
    height: 32px !important;
    border-radius: 6px !important;
    background: #0f172a !important;
    border: 1px solid #334155 !important;
    color: #e5e7eb !important;
    font-size: 11px !important;
    font-weight: 400 !important;
    box-shadow: none !important;
    outline: none !important;
}
.special-filter-input { padding: 0 10px !important; }
.special-filter-select { padding: 0 28px 0 10px !important; }
.special-filter-reset {
    width: auto !important;
    padding: 0 12px !important;
    cursor: pointer !important;
}
.special-table-shell {
    margin: 0 !important;
    padding: 0 !important;
    border-radius: 6px !important;
    overflow: hidden !important;
    background: #111827 !important;
    border: 1px solid #263244 !important;
    box-shadow: none !important;
}
.special-table-scroll { overflow-x: auto !important; }
body .content-surface .special-table-shell #specialTable {
    width: 100% !important;
    min-width: 1180px !important;
    margin: 0 !important;
    border-collapse: collapse !important;
    border: 0 !important;
}
body .content-surface .special-table-shell #specialTable thead th {
    height: 34px !important;
    padding: 8px 10px !important;
    background: #1f2937 !important;
    border: 1px solid #2f3b4f !important;
    color: #cbd5e1 !important;
    font-size: 10px !important;
    font-weight: 600 !important;
    line-height: 1.1 !important;
    letter-spacing: 0.05em !important;
    text-transform: uppercase !important;
    white-space: nowrap !important;
}
body .content-surface .special-table-shell #specialTable tbody td {
    height: 38px !important;
    padding: 7px 10px !important;
    background: #111827 !important;
    border: 1px solid #263244 !important;
    color: #dbe4f0 !important;
    font-size: 11px !important;
    line-height: 1.25 !important;
    font-weight: 400 !important;
    vertical-align: middle !important;
}
body .content-surface .special-table-shell #specialTable tbody tr:hover td {
    background: #172033 !important;
}
.special-table-footer {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 12px !important;
    min-height: 64px !important;
    padding: 10px 20px !important;
    background: #111827 !important;
    border-top: 1px solid #263244 !important;
}
.special-table-info {
    color: #dbeafe !important;
    font-size: 17px !important;
    font-weight: 900 !important;
    letter-spacing: 0 !important;
    text-transform: none !important;
}
.special-table-pagination {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
}
.special-page-link {
    min-width: 52px !important;
    height: 44px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    border-radius: 8px !important;
    border: 1px solid #2f4d74 !important;
    background: #0f172a !important;
    color: #bfdbfe !important;
    font-size: 13px !important;
    font-weight: 900 !important;
}
.special-page-link.is-nav {
    min-width: 128px !important;
    padding: 0 18px !important;
    color: #cbd5e1 !important;
    font-size: 16px !important;
}
.special-page-link.is-active {
    background: #0f3a72 !important;
    border-color: #3b82f6 !important;
    color: #ffffff !important;
}
.special-page-link:disabled {
    opacity: 0.35 !important;
    cursor: not-allowed !important;
}
.special-page-ellipsis {
    min-width: 26px !important;
    height: 44px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #64748b !important;
    font-size: 14px !important;
    font-weight: 900 !important;
}
html:not(.dark) body .content-surface .special-filter-panel,
html:not(.dark) body .content-surface .special-table-shell,
html:not(.dark) body .content-surface .special-table-footer {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
}
html:not(.dark) body .content-surface .special-filter-input,
html:not(.dark) body .content-surface .special-filter-select,
html:not(.dark) body .content-surface .special-filter-reset {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
    color: #1f2937 !important;
}
html:not(.dark) body .content-surface .special-table-shell #specialTable thead th {
    background: #e8eef5 !important;
    border-color: #d6e0ec !important;
    color: #475569 !important;
}
html:not(.dark) body .content-surface .special-table-shell #specialTable tbody td {
    background: #ffffff !important;
    border-color: #e2e8f0 !important;
    color: #334155 !important;
}
@media (max-width: 900px) {
    .special-summary-grid,
    .special-filter-panel {
        grid-template-columns: 1fr;
    }
    .special-filter-reset { width: 100%; }
}
.special-page-shell {
    display: grid !important;
    gap: 14px !important;
    padding: 0 14px !important;
}
.special-page-shell > .page-header-block {
    position: relative !important;
    margin: 0 !important;
    padding: 18px 28px !important;
    border: 1px solid rgba(148, 163, 184, 0.08) !important;
    border-left: 7px solid #f2c48d !important;
    border-radius: 14px !important;
    background: linear-gradient(90deg, rgba(31, 41, 55, 0.95), rgba(30, 41, 59, 0.95)) !important;
    box-shadow: none !important;
    overflow: hidden !important;
}
.special-page-shell .page-title-standard {
    color: #f8fafc !important;
    font-size: 22px !important;
    font-weight: 900 !important;
    line-height: 1.1 !important;
    margin: 0 0 8px !important;
}
.special-page-shell .page-subtitle-standard {
    color: #aab5c7 !important;
    font-size: 12px !important;
    font-weight: 900 !important;
    letter-spacing: 0.22em !important;
    line-height: 1.2 !important;
    margin: 0 !important;
    text-transform: uppercase !important;
}
.special-summary-grid {
    display: none !important;
}
.special-count-pill {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-height: 40px !important;
    padding: 0 18px !important;
    border-radius: 999px !important;
    border: 1px solid #25364f !important;
    background: #0f172a !important;
    color: #e5e7eb !important;
    font-size: 13px !important;
    font-weight: 900 !important;
    text-transform: uppercase !important;
}
.special-page-shell > section {
    margin: 0 !important;
}
</style>

@php
    $totalRecords = $records->count();
    $inUseRecords = $records->filter(fn ($record) => strtoupper((string) $record->status) === 'IN USE')->count();
    $unusedRecords = $records->filter(fn ($record) => strtoupper((string) $record->status) === 'UNUSED')->count();
    $repairRecords = $records->filter(fn ($record) => in_array(strtoupper((string) $record->status), ['REPAIRING', 'REPAIR / FAULTY', 'FAULTY', 'B.E.R', 'UNDER REPAIR'], true))->count();
@endphp

<div class="special-page-shell inventory-management-ui">
<div class="page-header-block flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h3 class="page-title-standard">Special Use Walkie Talkies</h3>
        <p class="page-subtitle-standard">Records marked for special use, temporary, or spares.</p>
    </div>
    @if(auth('wt')->user()->wt_role === 'admin_it')
    <div class="flex flex-shrink-0 items-center gap-2">
        <button onclick="openImportModal()" class="wt-btn wt-btn-soft">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="margin-right:5px;">
                <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
            </svg>
            Import Excel
        </button>

        <a href="{{ route('wt.admin.walkies.create.specialUse') }}" class="wt-btn wt-btn-soft">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="margin-right:5px;">
                <path d="M8 1a.5.5 0 0 1 .5.5V7.5H14a.5.5 0 0 1 0 1H8.5V14a.5.5 0 0 1-1 0V8.5H2a.5.5 0 0 1 0-1h5.5V1.5A.5.5 0 0 1 8 1z"/>
            </svg>
            Add Item
        </a>
    </div>
    @endif
</div>

<div class="special-summary-grid">
    <div class="special-summary-card">
        <div class="special-summary-label">Total Records</div>
        <div class="special-summary-value">{{ $totalRecords }}</div>
    </div>
    <div class="special-summary-card">
        <div class="special-summary-label">In Use</div>
        <div class="special-summary-value in-use">{{ $inUseRecords }}</div>
    </div>
    <div class="special-summary-card">
        <div class="special-summary-label">Unused</div>
        <div class="special-summary-value unused">{{ $unusedRecords }}</div>
    </div>
    <div class="special-summary-card">
        <div class="special-summary-label">Repair / Faulty</div>
        <div class="special-summary-value repair">{{ $repairRecords }}</div>
    </div>
</div>
</div>

{{-- Success / Error Alert --}}
@if(session('success'))
<div class="alert-success mb-6" id="alertBox">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="mr-2 flex-shrink-0">
        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
    </svg>
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert-error mb-6" id="errorBox">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="mr-2 flex-shrink-0">
        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
    </svg>
    {{ session('error') }}
</div>
@endif

<x-wt::dark-datatable
    table-id="specialTable"
    search-id="specialSearchInput"
    status-id="specialStatusFilter"
    reset-id="specialResetFilters"
    :items-per-page="10"
    min-width="0"
    :columns="[
        'RADIO ID',
        'STATUS',
        'SERIAL NO.',
        'MODEL',
        'LOCATION',
        'ASSIGNED TO',
        'RETURNED',
        'Action',
    ]"
    :status-options="$records->pluck('status')->filter()->unique()->sort()->values()->all()"
    row-selector="tbody .special-row"
>
    @foreach($records as $record)
        <tr class="special-row {{ $record->is_special_use ? 'border-l-4 border-violet-400' : '' }}"
            data-status="{{ strtoupper((string) ($record->status ?: '')) }}"
            data-search="{{ strtoupper(trim(($record->radio_id ?? '') . ' ' . ($record->status ?? '') . ' ' . ($record->serial_number ?? '') . ' ' . ($record->model ?? '') . ' ' . ($record->location ?? '') . ' ' . ($record->ownership_type ?? '') . ' ' . ($record->ownership ?? '') . ' ' . ($record->department ?? '') . ' ' . ($record->remark ?? '') . ' ' . ((int) ($record->special_use_returned ?? 0) === 1 ? 'YES RETURNED' : 'NO NOT RETURNED'))) }}">
            <td>{{ $record->radio_id ?: '-' }}</td>
            <td><span class="inline-flex rounded border border-slate-600 bg-slate-800 px-2 py-1 text-[10px] font-black uppercase">{{ $record->status ?: '-' }}</span></td>
            <td>{{ $record->serial_number ?: '-' }}</td>
            <td>{{ $record->model ?: '-' }}</td>
            <td>{{ $record->location ?: '-' }}</td>
            <td>
                <div class="font-black text-slate-900 dark:text-slate-100">{{ $record->ownership ?: '-' }}</div>
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ $record->department ?: '-' }} / {{ $record->ownership_type ?: '-' }}</div>
            </td>
            <td>{{ (int) ($record->special_use_returned ?? 0) === 1 ? 'YES' : 'NO' }}</td>
            <td>
                @if(auth('wt')->user()->wt_role === 'admin_it')
                    <div class="special-action-buttons">
                        <button type="button" class="btn btn-info btn-sm" title="View Details" onclick="openGlobalWalkieTimeline('{{ $record->walkie_id }}')">
                            <i class="fa-solid fa-eye"></i>
                            <span>View</span>
                        </button>
                        <a href="{{ route('wt.admin.walkies.edit', ['walkie' => $record->walkie_id, 'source' => 'special_use']) }}" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-edit"></i>
                            <span>Edit</span>
                        </a>
                        <form action="{{ route('wt.admin.walkies.destroy', $record->walkie_id) }}" method="POST" class="inline" data-modern-confirm="Delete this record?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fa-solid fa-trash"></i>
                                <span>Delete</span>
                            </button>
                        </form>
                    </div>
                @else
                    <button type="button" class="btn btn-info btn-sm" title="View Details" onclick="openGlobalWalkieTimeline('{{ $record->walkie_id }}')">
                        <i class="fa-solid fa-eye"></i>
                        <span>View</span>
                    </button>
                @endif
            </td>
        </tr>
    @endforeach
</x-wt::dark-datatable>

@foreach($records as $record)
<div id="specialViewModal-{{ $record->walkie_id }}" class="modal-overlay" onclick="closeSpecialViewModalOutside(event, 'specialViewModal-{{ $record->walkie_id }}')" aria-hidden="true">
    <div class="modal-box max-w-3xl">
        <div class="modal-header">
            <div>
                <h2 class="modal-title">Special Use Unit</h2>
                <p class="modal-subtitle">Radio ID {{ $record->radio_id ?: '-' }}</p>
            </div>
            <button type="button" onclick="closeSpecialViewModal('specialViewModal-{{ $record->walkie_id }}')" class="modal-close-btn" title="Close">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach([
                    'Status' => $record->status ?: '-',
                    'Serial No.' => $record->serial_number ?: '-',
                    'Model' => $record->model ?: '-',
                    'Location' => $record->location ?: '-',
                    'Ownership Type' => $record->ownership_type ?: '-',
                    'Current Ownership' => $record->ownership ?: '-',
                    'Department' => $record->department ?: '-',
                    'Received Date' => $record->received_date ?? '-',
                    'Repair Date' => $record->repair_date ?? '-',
                    'Returned' => (int) ($record->special_use_returned ?? 0) === 1 ? 'YES' : 'NO',
                    'Remarks' => $record->remark ?: '-',
                ] as $label => $value)
                    <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-900/60">
                        <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">{{ $label }}</p>
                        <p class="mt-1 text-sm font-bold text-slate-900 dark:text-slate-100">{{ $value }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endforeach

<style>
/* Final compact special-use pass. Placed after components so it wins. */
body .content-surface:has(#specialTable) {
    padding: 16px !important;
    border: 1px solid #273449 !important;
    border-radius: 14px !important;
    background: #111827 !important;
    box-shadow: none !important;
}
body .content-surface:has(#specialTable) > .breadcrumb,
body .content-surface:has(#specialTable) > .page-breadcrumb {
    display: none !important;
}
body .content-surface section.wt-data-page {
    gap: 10px !important;
    padding: 0 !important;
    color-scheme: normal !important;
    width: 100% !important;
    max-width: none !important;
    margin: 0 0 10px !important;
}
body .content-surface .wt-data-page-hero {
    min-height: 74px !important;
    padding: 14px 22px !important;
    border-radius: 10px !important;
    border-left-width: 4px !important;
    gap: 8px !important;
    align-items: center !important;
    width: 100% !important;
    max-width: none !important;
    box-sizing: border-box !important;
    overflow: visible !important;
}
body .content-surface .wt-data-page-title {
    font-size: 22px !important;
    line-height: 1.05 !important;
    margin: 0 0 7px !important;
}
body .content-surface .wt-data-page-subtitle {
    font-size: 10px !important;
    line-height: 1.2 !important;
    letter-spacing: 0.24em !important;
}
body .content-surface .wt-data-page-actions {
    gap: 8px !important;
    flex: 0 0 auto !important;
    justify-content: flex-end !important;
}
body .content-surface .wt-data-page-actions .wt-btn {
    width: 106px !important;
    min-width: 106px !important;
    height: 28px !important;
    min-height: 28px !important;
    padding: 0 9px !important;
    border-radius: 7px !important;
    gap: 5px !important;
    font-size: 10px !important;
    font-weight: 900 !important;
    letter-spacing: 0.02em !important;
}
body .content-surface .wt-data-page-actions .wt-btn svg {
    width: 11px !important;
    height: 11px !important;
    margin-right: 0 !important;
}
body .content-surface .wt-data {
    gap: 10px !important;
    color-scheme: normal !important;
}
body .content-surface .wt-data-filter {
    grid-template-columns: minmax(360px, 1fr) 208px 124px !important;
    min-height: auto !important;
    gap: 16px !important;
    align-items: end !important;
    width: 100% !important;
    padding: 16px 18px 18px !important;
    border: 1px solid #d8e1ed !important;
    border-radius: 18px !important;
    background: #ffffff !important;
    box-shadow: none !important;
}
body .content-surface .wt-data-field label {
    margin: 0 0 10px !important;
    color: #64748b !important;
    font-size: 10px !important;
    font-weight: 900 !important;
    letter-spacing: 0.18em !important;
    line-height: 1 !important;
}
body .content-surface .wt-data-input,
body .content-surface .wt-data-select,
body .content-surface .wt-data-reset {
    height: 46px !important;
    min-height: 46px !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 9px !important;
    background: #ffffff !important;
    color: #172033 !important;
    font-size: 14px !important;
    font-weight: 800 !important;
    box-shadow: none !important;
}
body .content-surface .wt-data-input {
    padding: 0 16px !important;
}
body .content-surface .wt-data-select {
    padding: 0 36px 0 16px !important;
}
body .content-surface .wt-data-reset {
    width: 124px !important;
    padding: 0 !important;
}
body .content-surface .wt-data thead th {
    height: 34px !important;
    padding: 7px 10px !important;
    font-size: 11px !important;
}
body .content-surface .wt-data tbody td {
    height: 34px !important;
    min-height: 34px !important;
    padding: 6px 10px !important;
    font-size: 11px !important;
}
body .content-surface #specialTable th:last-child,
body .content-surface #specialTable td:last-child {
    display: table-cell !important;
    width: 280px !important;
    min-width: 280px !important;
    max-width: 280px !important;
    text-align: center !important;
}
body .content-surface #specialTable .special-action-buttons {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    width: 100% !important;
    white-space: nowrap !important;
}
body .content-surface #specialTable .special-action-buttons form {
    display: inline-flex !important;
    margin: 0 !important;
}
body .content-surface #specialTable .btn {
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
body .content-surface #specialTable .btn i {
    font-size: 14px !important;
}
body .content-surface #specialTable .btn-info {
    border-color: #0dcaf0 !important;
    background: #0dcaf0 !important;
    color: #052c33 !important;
}
body .content-surface #specialTable .btn-primary {
    border-color: #0d6efd !important;
    background: #0d6efd !important;
}
body .content-surface #specialTable .btn-danger {
    border-color: #dc3545 !important;
    background: #dc3545 !important;
}
body .content-surface .wt-data-footer {
    min-height: 44px !important;
    padding: 7px 12px !important;
    gap: 10px !important;
}
body .content-surface .wt-data-info {
    font-size: 12px !important;
    font-weight: 800 !important;
}
body .content-surface .wt-data-pagination {
    gap: 8px !important;
}
body .content-surface .wt-data .wt-data-page {
    height: 30px !important;
    min-height: 30px !important;
    min-width: 34px !important;
    padding: 0 10px !important;
    font-size: 11px !important;
}
body .content-surface .wt-data .wt-data-page.is-nav {
    min-width: 74px !important;
    font-size: 11px !important;
}
body .content-surface .wt-data .wt-data-page.is-active {
    width: 34px !important;
    min-width: 34px !important;
}
body .content-surface .wt-data-empty {
    font-size: 12px !important;
    letter-spacing: 0.14em !important;
}
html:not(.dark) body .content-surface .wt-data-page-hero,
html:not(.dark) body .content-surface .wt-data-filter,
html:not(.dark) body .content-surface .wt-data-table,
html:not(.dark) body .content-surface .wt-data-footer,
html:not(.dark) body .content-surface .wt-data-scrollbar {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
}
html:not(.dark) body .content-surface .wt-data-page-hero {
    border: 1px solid #d8e1ed !important;
    border-left: 4px solid #0284c7 !important;
    box-shadow: none !important;
}
html:not(.dark) body .content-surface .wt-data-page-title,
html:not(.dark) body .content-surface .wt-data-info {
    color: #0f172a !important;
}
.dark body .content-surface .wt-data-filter {
    border-color: #263244 !important;
    background: #111827 !important;
}
.dark body .content-surface .wt-data-input,
.dark body .content-surface .wt-data-select,
.dark body .content-surface .wt-data-reset {
    border-color: #334155 !important;
    background: #0f172a !important;
    color: #e2e8f0 !important;
}
html:not(.dark) body .content-surface .wt-data-page-subtitle,
html:not(.dark) body .content-surface .wt-data-field label,
html:not(.dark) body .content-surface .wt-data-empty {
    color: #64748b !important;
}
html:not(.dark) body .content-surface .wt-data-page-actions .wt-btn,
html:not(.dark) body .content-surface .wt-data-input,
html:not(.dark) body .content-surface .wt-data-select,
html:not(.dark) body .content-surface .wt-data-reset,
html:not(.dark) body .content-surface .wt-data .wt-data-page {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
    color: #0f172a !important;
}
html:not(.dark) body .content-surface .wt-data thead th {
    background: #f8fafc !important;
    border-color: #d8e1ed !important;
    color: #526781 !important;
}
html:not(.dark) body .content-surface .wt-data tbody td {
    background: #ffffff !important;
    border-color: #e2e8f0 !important;
    color: #1f2937 !important;
}
html:not(.dark) body .content-surface .wt-data tbody tr:hover td {
    background: #f8fafc !important;
}
html:not(.dark) body .content-surface:has(#specialTable) {
    background: #f5f8fc !important;
    border-color: #d8e1ed !important;
}
.dark body .content-surface .wt-data-page-hero {
    background: linear-gradient(90deg, rgba(31, 41, 55, 0.98), rgba(30, 41, 59, 0.98)) !important;
    border-color: rgba(148, 163, 184, 0.12) !important;
    border-left-color: #f2c48d !important;
}
</style>

@include('wt.admin.partials.inventory-management-ui')

<style>
/* Special Use final header correction: match Faulty page without clipping. */
body .content-surface .special-page-shell .wt-data-page-hero {
    min-height: 74px !important;
    padding: 14px 22px !important;
    overflow: visible !important;
}
body .content-surface .special-page-shell .wt-data-page-title {
    font-size: 22px !important;
    line-height: 1.05 !important;
    margin: 0 0 7px !important;
}
body .content-surface .special-page-shell .wt-data-page-subtitle {
    font-size: 10px !important;
    line-height: 1.2 !important;
}

body .content-surface .special-page-shell .wt-data-page-actions .wt-btn {
    min-width: 96px !important;
    width: auto !important;
    height: 30px !important;
    min-height: 30px !important;
    padding: 0 11px !important;
    border-radius: 7px !important;
    gap: 6px !important;
    font-size: 10px !important;
}

body .content-surface .special-page-shell .wt-data-page-actions .wt-btn svg {
    width: 11px !important;
    height: 11px !important;
    margin-right: 0 !important;
}

body .content-surface .special-page-shell + .wt-data .wt-data-filter,
body .content-surface:has(.special-page-shell) .wt-data-filter {
    grid-template-columns: minmax(0, 1fr) 360px 82px !important;
    width: 100% !important;
    max-width: none !important;
    gap: 5px !important;
    padding: 10px 12px 12px !important;
    border-radius: 12px !important;
}

body .content-surface .special-page-shell + .wt-data .wt-data-field label,
body .content-surface:has(.special-page-shell) .wt-data-field label {
    margin: 0 0 5px !important;
    font-size: 8px !important;
    letter-spacing: .14em !important;
}

body .content-surface .special-page-shell + .wt-data .wt-data-input,
body .content-surface .special-page-shell + .wt-data .wt-data-select,
body .content-surface .special-page-shell + .wt-data .wt-data-reset,
body .content-surface:has(.special-page-shell) .wt-data-input,
body .content-surface:has(.special-page-shell) .wt-data-select,
body .content-surface:has(.special-page-shell) .wt-data-reset {
    height: 32px !important;
    min-height: 32px !important;
    border-radius: 8px !important;
    font-size: 11px !important;
}

body .content-surface .special-page-shell + .wt-data .wt-data-input,
body .content-surface:has(.special-page-shell) .wt-data-input {
    width: 100% !important;
    max-width: none !important;
    padding: 0 10px !important;
}

body .content-surface .special-page-shell + .wt-data .wt-data-select,
body .content-surface:has(.special-page-shell) .wt-data-select {
    padding: 0 28px 0 10px !important;
}

body .content-surface .special-page-shell + .wt-data .wt-data-reset,
body .content-surface:has(.special-page-shell) .wt-data-reset {
    width: 82px !important;
    min-width: 82px !important;
    padding: 0 !important;
}

@media (max-width: 900px) {
    body .content-surface .special-page-shell + .wt-data .wt-data-filter,
    body .content-surface:has(.special-page-shell) .wt-data-filter {
        grid-template-columns: 1fr !important;
        width: 100% !important;
    }

    body .content-surface .special-page-shell + .wt-data .wt-data-reset,
    body .content-surface:has(.special-page-shell) .wt-data-reset {
        width: 100% !important;
    }
}
</style>

{{-- ===================== ADD RECORD MODAL ===================== --}}
<div id="addModal" class="modal-overlay" onclick="closeAddModalOutside(event)">
    <div class="modal-box">
        <div class="modal-header">
            <div>
                <h2 class="modal-title">Add Special Use Unit</h2>
            </div>
            <button onclick="closeAddModal()" class="modal-close-btn" title="Close">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('wt.admin.walkies.store') }}" class="flex flex-col h-full overflow-hidden">
            @csrf
            <input type="hidden" name="is_special_use" value="1">
            <input type="hidden" name="special_use_returned" value="0">
            
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Radio ID <span class="required">*</span></label>
                        <input type="text" name="radio_id" class="form-input" placeholder="Radio ID" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Serial No. <span class="required">*</span></label>
                        <input type="text" name="serial_number" class="form-input" placeholder="Serial number" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Model <span class="required">*</span></label>
                        <select name="model" class="form-input" required>
                            @foreach($walkieModels as $m)
                            <option value="{{ $m }}">{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status <span class="required">*</span></label>
                        <select name="status" class="form-input" required>
                            @foreach($statusOptions as $st)
                            <option value="{{ $st }}" {{ $st === 'UNUSED' ? 'selected' : '' }}>{{ $st }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ownership Type <span class="required">*</span></label>
                        <select name="ownership_type" class="form-input ownership-type-control" required>
                            @foreach($ownershipTypeOptions as $ot)
                            <option value="{{ $ot }}" {{ $ot === 'SPARE' ? 'selected' : '' }}>{{ $ot }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group shared-with-group hidden">
                        <label class="form-label">Shared With <span class="required">*</span></label>
                        <input type="text" name="shared_with" class="form-input shared-with-input" placeholder="E.G. USER / TEAM / DEPARTMENT">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ownership</label>
                        <input type="text" name="ownership" class="form-input" placeholder="Ownership name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Department</label>
                        <input type="text" name="department" list="department-options" class="form-input" placeholder="Department">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Location</label>
                        <select name="location" class="form-input">
                            <option value="">-- None --</option>
                            @foreach($locationOptions as $location)
                            <option value="{{ $location }}">{{ $location }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-span-2">
                        <label class="form-label">Remark</label>
                        <textarea name="remark" class="form-input" placeholder="Remarks / Purpose"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeAddModal()" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-submit">Add Unit</button>
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
                <p class="modal-subtitle" id="editModalSubtitle">Modify status and ownership details for special use units.</p>
            </div>
            <button onclick="closeEditModal()" class="modal-close-btn" title="Close">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                </svg>
            </button>
        </div>

        <form method="POST" id="editWalkieForm" class="flex flex-col h-full overflow-hidden">
            @csrf
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Radio ID <span class="required">*</span></label>
                        <input type="text" name="radio_id" id="edit_radio_id" class="form-input" placeholder="Radio ID" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Serial No. <span class="required">*</span></label>
                        <input type="text" name="serial_number" id="edit_serial_number" class="form-input" placeholder="Serial number" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Model <span class="required">*</span></label>
                        <select name="model" id="edit_model" class="form-input" required>
                            @foreach($walkieModels as $editModel)
                            <option value="{{ $editModel }}">{{ $editModel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status <span class="required">*</span></label>
                        <select name="status" id="edit_status" class="form-input" required>
                            @foreach($statusOptions as $editStatus)
                            <option value="{{ $editStatus }}">{{ $editStatus }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ownership Type <span class="required">*</span></label>
                        <select name="ownership_type" id="edit_ownership_type" class="form-input ownership-type-control" required>
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
                        <input type="text" name="ownership" id="edit_ownership" class="form-input" placeholder="Owner name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Position</label>
                        <input type="text" name="position" id="edit_position" list="position-options" class="form-input" placeholder="Position">
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label">Department</label>
                        <input type="text" name="department" id="edit_department" list="department-options" class="form-input" placeholder="Department">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Location</label>
                        <select name="location" id="edit_location" class="form-input">
                            <option value="">-- None --</option>
                            @foreach($locationOptions as $location)
                            <option value="{{ $location }}">{{ $location }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Temporary / Swapped WT Radio ID</label>
                        <input type="text" name="temporary_radio_id" id="edit_temporary_radio_id" class="form-input" placeholder="Temporary / swapped radio ID">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tracking REF</label>
                        <input type="text" name="tracking_ref" id="edit_tracking_ref" class="form-input" placeholder="Tracking reference">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Need To Change ID</label>
                        <select name="need_to_change_id" id="edit_need_to_change_id" class="form-input">
                            <option value="0">NO</option>
                            <option value="1">YES</option>
                        </select>
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
                            <option value="">Select target ownership type...</option>
                            @foreach($ownershipTypeOptions as $targetOwnershipType)
                            <option value="{{ $targetOwnershipType }}">{{ $targetOwnershipType }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Special Use</label>
                        <select name="is_special_use" id="edit_is_special_use" class="form-input">
                            <option value="0">NO</option>
                            <option value="1">YES</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Returned</label>
                        <select name="special_use_returned" id="edit_special_use_returned" class="form-input">
                            <option value="0">NO</option>
                            <option value="1">YES</option>
                        </select>
                    </div>
                    <div class="form-group" style="grid-column: span 3;">
                        <label class="form-label">Remarks</label>
                        <textarea name="remark" id="edit_remark" class="form-input" style="height:35px; resize:none;" placeholder="Remarks"></textarea>
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

{{-- ===================== IMPORT EXCEL MODAL ===================== --}}
@if(auth('wt')->user()->wt_role === 'admin_it')
<div id="importModal" class="modal-overlay" onclick="closeImportModalOutside(event)">
    <div class="modal-box" style="max-width: 500px;">
        <div class="modal-header">
            <div>
                <h2 class="modal-title">Bulk Import Special Use</h2>
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
            <input type="hidden" name="import_context" value="special_use">
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
                        <p class="text-[10px] text-stone-400 mt-1 uppercase font-black tracking-widest">Required Headings: radio_id, serial_no, is_special_use:1...</p>
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

<style>
/* Match Under Repair / Faulty controls exactly. */
body .content-surface:has(#specialTable) {
    background: #0b1220 !important;
    border: 0 !important;
    border-radius: 0 !important;
    padding: 10px !important;
    box-shadow: none !important;
}

body .content-surface .special-page-shell,
body .content-surface .special-page-shell + .wt-data {
    background: transparent !important;
    border: 0 !important;
    padding: 0 !important;
}

body .content-surface .special-page-shell {
    gap: 12px !important;
}

body .content-surface .special-page-shell > .page-header-block {
    padding: 0 2px 10px !important;
    background: transparent !important;
    border: 0 !important;
    box-shadow: none !important;
}

body .content-surface .special-page-shell .page-title-standard {
    margin: 0 !important;
    color: #f8fafc !important;
    font-size: 19px !important;
    line-height: 1.1 !important;
}

body .content-surface .special-page-shell .page-subtitle-standard {
    max-width: 560px !important;
    margin-top: 5px !important;
    color: #93a4ba !important;
    font-size: 9px !important;
    letter-spacing: .16em !important;
    line-height: 1.45 !important;
}

body .content-surface .special-page-shell > .page-header-block .wt-btn {
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

body .content-surface:has(.special-page-shell) .wt-data-filter {
    display: grid !important;
    grid-template-columns: minmax(240px, 1fr) 180px auto !important;
    gap: 10px !important;
    align-items: end !important;
    width: 100% !important;
    max-width: none !important;
    margin: 0 !important;
    padding: 12px !important;
    border: 1px solid rgba(148, 163, 184, .18) !important;
    border-radius: 14px !important;
    background: #0f172a !important;
    box-shadow: none !important;
}

body .content-surface:has(.special-page-shell) .wt-data-field label {
    margin: 0 0 6px !important;
    color: #8ea0b8 !important;
    font-size: 9px !important;
    letter-spacing: .12em !important;
}

body .content-surface:has(.special-page-shell) .wt-data-input,
body .content-surface:has(.special-page-shell) .wt-data-select,
body .content-surface:has(.special-page-shell) .wt-data-reset {
    height: 38px !important;
    min-height: 38px !important;
    border-radius: 10px !important;
    border: 1px solid rgba(148, 163, 184, .26) !important;
    background: #111827 !important;
    color: #e5edf7 !important;
    font-size: 12px !important;
    font-weight: 750 !important;
}

body .content-surface:has(.special-page-shell) .wt-data-input {
    width: 100% !important;
    padding: 0 14px !important;
}

body .content-surface:has(.special-page-shell) .wt-data-select {
    width: 100% !important;
    padding: 0 30px 0 14px !important;
}

body .content-surface:has(.special-page-shell) .wt-data-reset {
    width: auto !important;
    min-width: 82px !important;
    padding: 0 18px !important;
    background: transparent !important;
    color: #dbeafe !important;
}
</style>

<script>
function openSpecialViewModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.classList.add('active');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
}

function closeSpecialViewModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.classList.remove('active');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
}

function closeSpecialViewModalOutside(event, id) {
    if (event.target === document.getElementById(id)) {
        closeSpecialViewModal(id);
    }
}

function openImportModal() {
    document.getElementById('importModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeImportModal() {
    document.getElementById('importModal').classList.remove('active');
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
        display.innerText = "Selected: " + input.files[0].name;
    }
}

function openEditModal(id, radio, serialNumber, model, status, ownershipType, ownership, position, department, location, temporaryRadioId, trackingRef, remark, needToChangeId, idChangeDone, ownershipTypeToBe, isSpecialUse, specialUseReturned) {
    const form = document.getElementById('editWalkieForm');
    form.action = "{{ route('wt.admin.walkies.updateMeta', ['walkie' => '__ID__']) }}".replace('__ID__', id);
    document.getElementById('editModalSubtitle').innerText = `Updating unit ${radio}`;
    document.getElementById('edit_radio_id').value = radio || '';
    document.getElementById('edit_serial_number').value = serialNumber || '';
    document.getElementById('edit_model').value = model || 'R7';
    document.getElementById('edit_status').value = status || 'UNUSED';
    document.getElementById('edit_ownership_type').value = ownershipType || 'UNALLOCATED';
    document.getElementById('edit_ownership').value = ownership || '';
    document.getElementById('edit_position').value = position || '';
    document.getElementById('edit_department').value = department || '';
    document.getElementById('edit_location').value = location || '';
    document.getElementById('edit_temporary_radio_id').value = temporaryRadioId || '';
    document.getElementById('edit_tracking_ref').value = trackingRef || '';
    document.getElementById('edit_remark').value = remark || '';
    document.getElementById('edit_need_to_change_id').value = needToChangeId || '0';
    document.getElementById('edit_id_change_done').value = idChangeDone || '0';
    document.getElementById('edit_ownership_type_to_be').value = ownershipTypeToBe || '';
    document.getElementById('edit_is_special_use').value = isSpecialUse || '0';
    document.getElementById('edit_special_use_returned').value = specialUseReturned || '0';
    document.getElementById('editModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function openAddModal() {
    document.getElementById('addModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeAddModal() {
    document.getElementById('addModal').classList.remove('active');
    document.body.style.overflow = '';
}

function closeAddModalOutside(event) {
    if (event.target === document.getElementById('addModal')) {
        closeAddModal();
    }
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

function syncSharedWith(select) {
    const form = select.closest('form');
    if (!form) return;

    const isShared = (select.value || '').toUpperCase() === 'SHARED';
    const sharedGroup = form.querySelector('.shared-with-group');
    const sharedInput = form.querySelector('.shared-with-input');

    if (sharedGroup) sharedGroup.classList.toggle('hidden', !isShared);
    if (sharedInput) {
        sharedInput.required = isShared;
        if (!isShared) sharedInput.value = '';
    }
}

document.querySelectorAll('.ownership-type-control').forEach((select) => {
    select.addEventListener('change', () => syncSharedWith(select));
    syncSharedWith(select);
});

// ESC key to close
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeEditModal();
        closeAddModal();
        closeImportModal();
    }
});

// Auto-dismiss success alert
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
</script>

<style>
.line-clamp-2 { 
    display: -webkit-box; 
    -webkit-line-clamp: 2; 
    -webkit-box-orient: vertical; 
    overflow: hidden; 
}

#specialTable.dataTable tbody td {
    color: #e5edf8 !important;
}
#specialTable.dataTable tbody tr:nth-child(odd) {
    background: rgba(51, 65, 85, 0.28) !important;
}
#specialTable.dataTable tbody tr:nth-child(even) {
    background: rgba(30, 41, 59, 0.42) !important;
}
#specialTable.dataTable tbody tr:hover {
    background: rgba(71, 85, 105, 0.45) !important;
}

/* ===== Alerts ===== */
.alert-success, .alert-error {
    display: flex;
    align-items: flex-start;
    padding: 10px 14px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    animation: slideDown 0.3s ease;
}
.alert-success { background: #ecfdf5; color: #166534; border: 1px solid #bbf7d0; }
.alert-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

        /* Global layout styles are now used for Modals */
    }
}
</style>

</style>

@include('wt.admin.partials.inventory-management-ui')

<style>
/* Absolute final Special Use action buttons: match inventory action styling. */
body .content-surface:has(#specialTable) #specialTable th:last-child,
body .content-surface:has(#specialTable) #specialTable td:last-child {
    width: 280px !important;
    min-width: 280px !important;
    max-width: 280px !important;
    text-align: center !important;
}

body .content-surface:has(#specialTable) #specialTable .special-action-buttons {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    width: 100% !important;
    white-space: nowrap !important;
}

body .content-surface:has(#specialTable) #specialTable .special-action-buttons form {
    display: inline-flex !important;
    margin: 0 !important;
}

body .content-surface:has(#specialTable) #specialTable .btn {
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
    box-shadow: none !important;
}

body .content-surface:has(#specialTable) #specialTable .btn i {
    font-size: 14px !important;
    line-height: 1 !important;
}

body .content-surface:has(#specialTable) #specialTable .btn-info {
    border-color: #0dcaf0 !important;
    background: #0dcaf0 !important;
    color: #052c33 !important;
}

body .content-surface:has(#specialTable) #specialTable .btn-primary {
    border-color: #0d6efd !important;
    background: #0d6efd !important;
    color: #ffffff !important;
}

body .content-surface:has(#specialTable) #specialTable .btn-danger {
    border-color: #dc3545 !important;
    background: #dc3545 !important;
    color: #ffffff !important;
}

/* Absolute final Special Use filter sizing: keep it identical to Under Repair / Faulty. */
body .content-surface:has(#specialTable) .wt-data-filter,
body .content-surface .special-page-shell + .wt-data .wt-data-filter {
    display: grid !important;
    grid-template-columns: minmax(0, 1fr) 360px 82px !important;
    width: 100% !important;
    max-width: none !important;
    min-height: auto !important;
    margin: 0 !important;
    padding: 12px !important;
    gap: 5px !important;
    align-items: end !important;
    border: 1px solid rgba(148, 163, 184, .18) !important;
    border-radius: 14px !important;
    background: #0f172a !important;
    box-shadow: none !important;
}

body .content-surface:has(#specialTable) .wt-data-field {
    width: 100% !important;
    min-width: 0 !important;
    max-width: none !important;
}

body .content-surface:has(#specialTable) .wt-data-input,
body .content-surface:has(#specialTable) .wt-data-select,
body .content-surface:has(#specialTable) .wt-data-reset {
    height: 38px !important;
    min-height: 38px !important;
    border-radius: 10px !important;
    border: 1px solid rgba(148, 163, 184, .26) !important;
    background: #111827 !important;
    color: #e5edf7 !important;
    font-size: 12px !important;
    font-weight: 750 !important;
}

body .content-surface:has(#specialTable) .wt-data-input {
    width: 100% !important;
    max-width: none !important;
    padding: 0 14px !important;
}

body .content-surface:has(#specialTable) .wt-data-select {
    width: 100% !important;
    padding: 0 30px 0 14px !important;
}

body .content-surface:has(#specialTable) .wt-data-reset {
    width: 82px !important;
    min-width: 82px !important;
    padding: 0 !important;
    background: transparent !important;
    color: #dbeafe !important;
}

body .content-surface:has(#specialTable) .wt-data-field label {
    margin: 0 0 6px !important;
    color: #8ea0b8 !important;
    font-size: 9px !important;
    letter-spacing: .12em !important;
}

@media (max-width: 1250px) {
    body .content-surface:has(#specialTable) .wt-data-filter,
    body .content-surface .special-page-shell + .wt-data .wt-data-filter {
        grid-template-columns: minmax(240px, 1fr) 360px 82px !important;
    }
}
</style>

<style id="special-use-theme-sync-final">
html:not(.dark) body .content-surface:has(#specialTable),
html[data-theme="light"] body .content-surface:has(#specialTable) {
    background: #f5f8fc !important;
    border-color: #d8e1ed !important;
    color: #0f172a !important;
}

html:not(.dark) body .content-surface:has(#specialTable) .special-page-shell,
html:not(.dark) body .content-surface:has(#specialTable) .special-page-shell + .wt-data,
html[data-theme="light"] body .content-surface:has(#specialTable) .special-page-shell,
html[data-theme="light"] body .content-surface:has(#specialTable) .special-page-shell + .wt-data {
    background: transparent !important;
    color: #0f172a !important;
}

html:not(.dark) body .content-surface:has(#specialTable) .wt-data-filter,
html:not(.dark) body .content-surface .special-page-shell + .wt-data .wt-data-filter,
html[data-theme="light"] body .content-surface:has(#specialTable) .wt-data-filter,
html[data-theme="light"] body .content-surface .special-page-shell + .wt-data .wt-data-filter,
html:not(.dark) body .content-surface:has(#specialTable) .wt-data-table,
html:not(.dark) body .content-surface:has(#specialTable) .wt-data-footer,
html[data-theme="light"] body .content-surface:has(#specialTable) .wt-data-table,
html[data-theme="light"] body .content-surface:has(#specialTable) .wt-data-footer {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
    color: #0f172a !important;
}

html:not(.dark) body .content-surface:has(#specialTable) .wt-data-field label,
html[data-theme="light"] body .content-surface:has(#specialTable) .wt-data-field label {
    color: #64748b !important;
}

html:not(.dark) body .content-surface:has(#specialTable) .wt-data-input,
html:not(.dark) body .content-surface:has(#specialTable) .wt-data-select,
html:not(.dark) body .content-surface:has(#specialTable) .wt-data-reset,
html[data-theme="light"] body .content-surface:has(#specialTable) .wt-data-input,
html[data-theme="light"] body .content-surface:has(#specialTable) .wt-data-select,
html[data-theme="light"] body .content-surface:has(#specialTable) .wt-data-reset {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
    color: #0f172a !important;
}

html:not(.dark) body .content-surface:has(#specialTable) #specialTable,
html:not(.dark) body .content-surface:has(#specialTable) #specialTable tbody,
html:not(.dark) body .content-surface:has(#specialTable) #specialTable tbody tr,
html[data-theme="light"] body .content-surface:has(#specialTable) #specialTable,
html[data-theme="light"] body .content-surface:has(#specialTable) #specialTable tbody,
html[data-theme="light"] body .content-surface:has(#specialTable) #specialTable tbody tr {
    background: #ffffff !important;
    color: #0f172a !important;
}

html:not(.dark) body .content-surface:has(#specialTable) #specialTable thead th,
html[data-theme="light"] body .content-surface:has(#specialTable) #specialTable thead th {
    background: #eef3f8 !important;
    border-color: #cbd5e1 !important;
    color: #334155 !important;
}

html:not(.dark) body .content-surface:has(#specialTable) #specialTable tbody td,
html[data-theme="light"] body .content-surface:has(#specialTable) #specialTable tbody td {
    background: #ffffff !important;
    border-color: #e2e8f0 !important;
    color: #1f2937 !important;
}

html:not(.dark) body .content-surface:has(#specialTable) #specialTable tbody tr:hover td,
html[data-theme="light"] body .content-surface:has(#specialTable) #specialTable tbody tr:hover td {
    background: #f8fafc !important;
}

html:not(.dark) body .content-surface:has(#specialTable) .wt-data-info,
html:not(.dark) body .content-surface:has(#specialTable) .wt-data-empty,
html[data-theme="light"] body .content-surface:has(#specialTable) .wt-data-info,
html[data-theme="light"] body .content-surface:has(#specialTable) .wt-data-empty {
    color: #334155 !important;
}

html:not(.dark) body .content-surface:has(#specialTable) .wt-data .wt-data-page,
html[data-theme="light"] body .content-surface:has(#specialTable) .wt-data .wt-data-page {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
    color: #0f172a !important;
}

html:not(.dark) body .content-surface:has(#specialTable) .wt-data .wt-data-page.is-active,
html[data-theme="light"] body .content-surface:has(#specialTable) .wt-data .wt-data-page.is-active {
    background: #0f3a72 !important;
    border-color: #0f3a72 !important;
    color: #ffffff !important;
}

html.dark body .content-surface:has(#specialTable),
html[data-theme="dark"] body .content-surface:has(#specialTable) {
    background: #0b1220 !important;
    border-color: #273449 !important;
    color: #e5edf7 !important;
}

html.dark body .content-surface:has(#specialTable) .wt-data-filter,
html.dark body .content-surface .special-page-shell + .wt-data .wt-data-filter,
html[data-theme="dark"] body .content-surface:has(#specialTable) .wt-data-filter,
html[data-theme="dark"] body .content-surface .special-page-shell + .wt-data .wt-data-filter {
    background: #0f172a !important;
    border-color: rgba(148, 163, 184, .18) !important;
    color: #e5edf7 !important;
}

html.dark body .content-surface:has(#specialTable) .wt-data-input,
html.dark body .content-surface:has(#specialTable) .wt-data-select,
html.dark body .content-surface:has(#specialTable) .wt-data-reset,
html[data-theme="dark"] body .content-surface:has(#specialTable) .wt-data-input,
html[data-theme="dark"] body .content-surface:has(#specialTable) .wt-data-select,
html[data-theme="dark"] body .content-surface:has(#specialTable) .wt-data-reset {
    background: #111827 !important;
    border-color: rgba(148, 163, 184, .26) !important;
    color: #e5edf7 !important;
}

html.dark body .content-surface:has(#specialTable) #specialTable thead th,
html[data-theme="dark"] body .content-surface:has(#specialTable) #specialTable thead th {
    background: #111827 !important;
    border-color: #334155 !important;
    color: #dbe4f0 !important;
}

html.dark body .content-surface:has(#specialTable) #specialTable tbody td,
html[data-theme="dark"] body .content-surface:has(#specialTable) #specialTable tbody td {
    background: #111827 !important;
    border-color: #263244 !important;
    color: #dbe4f0 !important;
}
</style>

@include('wt.admin.partials.inventory-tools-table-skin')
@include('wt.admin.partials.inventory-tools-unified-ui')
<style id="special-compact-final-override">
    body .content-surface:has(#specialTable),
    body .content-surface:has(#specialTable) .wt-data,
    body .content-surface:has(#specialTable) .wt-data-table,
    body .content-surface:has(#specialTable) .wt-data-scroll {
        overflow: hidden !important;
    }
    body .content-surface:has(#specialTable) #specialTable {
        width: 100% !important;
        min-width: 0 !important;
        table-layout: auto !important;
    }
    body .content-surface:has(#specialTable) #specialTable thead th {
        height: 28px !important;
        padding: 4px 6px !important;
        text-align: center !important;
        font-size: 9px !important;
        line-height: 1.05 !important;
    }
    body .content-surface:has(#specialTable) #specialTable tbody td {
        height: 26px !important;
        padding: 3px 6px !important;
        text-align: left !important;
        font-size: 10px !important;
        line-height: 1.1 !important;
        white-space: nowrap !important;
    }
    body .content-surface:has(#specialTable) #specialTable tbody td:last-child {
        text-align: center !important;
    }
</style>

@endsection
