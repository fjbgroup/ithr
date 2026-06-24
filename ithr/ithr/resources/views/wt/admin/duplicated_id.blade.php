@extends('wt.layouts.admin')

@section('title', 'Duplicated ID Management')

@section('content')
<style>
.duplicate-page { display: grid; gap: 14px; }
.duplicate-hero,
.duplicate-guide,
.duplicate-table-shell {
    border: 1px solid rgba(148, 163, 184, 0.18);
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
}
.dark .duplicate-hero,
.dark .duplicate-guide,
.dark .duplicate-table-shell {
    background: #111827;
    border-color: rgba(148, 163, 184, 0.16);
    box-shadow: none;
}
.duplicate-hero {
    padding: 0;
    border: 0;
    background: transparent;
    box-shadow: none;
}
.dark .duplicate-hero {
    background: transparent;
    box-shadow: none;
}
.duplicate-search-panel {
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
.dark .duplicate-search-panel {
    border-color: #263244;
    background: #111827;
}
.duplicate-filter-field label {
    display: block;
    margin-bottom: 7px;
    color: #94a3b8;
    font-size: 10px;
    line-height: 1;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}
.duplicate-search,
.duplicate-filter-select {
    width: 100%;
    height: 48px;
    border-radius: 8px;
    border: 1px solid #334155;
    background: #0f172a;
    padding: 0 12px;
    color: #e5e7eb;
    font-size: 16px;
    font-weight: 500;
    outline: none;
}
.duplicate-search::placeholder { color: #94a3b8; }
.duplicate-search:focus,
.duplicate-filter-select:focus {
    border-color: #38bdf8;
    box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.16);
}
.duplicate-filter-reset {
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
.duplicate-filter-reset:hover {
    border-color: #38bdf8;
    color: #0284c7;
}
.dark .duplicate-filter-field label { color: #94a3b8; }
.dark .duplicate-search,
.dark .duplicate-filter-select,
.dark .duplicate-filter-reset {
    border-color: #334155;
    background: #0f172a;
    color: #e2e8f0;
}
.duplicate-summary-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
}
.duplicate-stat {
    border: 1px solid rgba(148, 163, 184, 0.18);
    border-radius: 6px;
    padding: 12px;
    background: #f8fafc;
}
.dark .duplicate-stat {
    background: #0f172a;
    border-color: rgba(148, 163, 184, 0.16);
}
.duplicate-stat-label,
.duplicate-step-kicker {
    color: #64748b;
    font-size: 10px;
    font-weight: 900;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}
.duplicate-stat-value {
    margin-top: 6px;
    color: #0f172a;
    font-size: 24px;
    line-height: 1;
    font-weight: 900;
}
.dark .duplicate-stat-value { color: #f8fafc; }
.duplicate-guide {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    overflow: hidden;
}
.duplicate-step {
    padding: 14px 16px;
    border-right: 1px solid rgba(148, 163, 184, 0.16);
}
.duplicate-step:last-child { border-right: 0; }
.duplicate-step-kicker { color: #0ea5e9; }
.duplicate-step-title {
    margin-top: 4px;
    color: #0f172a;
    font-size: 12px;
    font-weight: 900;
}
.duplicate-step-copy {
    margin-top: 4px;
    color: #64748b;
    font-size: 11px;
    font-weight: 700;
    line-height: 1.45;
}
.dark .duplicate-step-title { color: #f8fafc; }
.dark .duplicate-step-copy,
.dark .duplicate-stat-label { color: #94a3b8; }
.duplicate-table-shell {
    overflow: hidden;
    padding: 0;
    border: 1px solid #263244;
    border-radius: 10px;
    background: #111827;
    box-shadow: none;
}
.duplicate-table-shell .dataTables_wrapper,
.duplicate-table-shell table.dataTable { width: 100% !important; }
.duplicate-table-shell .dataTables_wrapper .dataTables_length,
.duplicate-table-shell .dataTables_wrapper .dataTables_filter { padding: 14px 16px 10px !important; }
.content-surface .duplicate-table-shell .dataTables_length,
.content-surface .duplicate-table-shell .dataTables_info,
.content-surface .duplicate-table-shell .dataTables_paginate,
.content-surface .duplicate-table-shell .adminit-table-info {
    display: none !important;
}
.duplicate-table-shell table.dataTable thead th { white-space: nowrap; }
.duplicate-table-shell table.dataTable tbody td.wrap-cell {
    min-width: 220px;
    max-width: 320px;
    white-space: normal !important;
}
.duplicate-table-shell table.dataTable tbody td.dataTables_empty {
    height: 0 !important;
    padding: 0 !important;
    border: 0 !important;
    color: transparent !important;
    text-align: center !important;
}
.content-surface .duplicate-table-shell .dataTables_scrollHead,
.content-surface .duplicate-table-shell .dataTables_scrollHeadInner,
.content-surface .duplicate-table-shell .dataTables_scrollHead table,
.content-surface .duplicate-table-shell .dataTables_scrollBody,
.content-surface .duplicate-table-shell .dataTables_scrollBody table {
    background: #111827 !important;
}
body .content-surface .duplicate-table-shell #duplicateTable thead th {
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
body .content-surface .duplicate-table-shell #duplicateTable tbody td {
    height: 40px !important;
    padding: 8px 20px !important;
    border: 1px solid #263244 !important;
    background: #111827 !important;
    color: #dbe4f0 !important;
    font-size: 14px !important;
    font-weight: 600 !important;
}
body .content-surface .duplicate-table-shell #duplicateTable tbody td.dataTables_empty {
    height: 0 !important;
    padding: 0 !important;
    border: 0 !important;
    color: transparent !important;
}
.content-surface .duplicate-table-shell .dataTables_scrollBody {
    border-top: 0 !important;
    scrollbar-width: auto;
    scrollbar-color: #526174 #111827;
}
.content-surface .duplicate-table-shell .dataTables_scrollBody::-webkit-scrollbar {
    height: 11px !important;
}
.content-surface .duplicate-table-shell .dataTables_scrollBody::-webkit-scrollbar-track {
    background: #111827 !important;
}
.content-surface .duplicate-table-shell .dataTables_scrollBody::-webkit-scrollbar-thumb {
    background: #526174 !important;
    border-radius: 999px !important;
}
.content-surface .duplicate-table-shell .adminit-table-footer {
    display: flex !important;
    align-items: center !important;
    justify-content: flex-end !important;
    gap: 12px !important;
    min-height: 64px !important;
    padding: 10px 20px !important;
    border-top: 1px solid #263244 !important;
    background: #1e293b !important;
}
.content-surface .duplicate-table-shell .adminit-table-info {
    color: #dbeafe !important;
    font-size: 17px !important;
    font-weight: 900 !important;
    letter-spacing: 0 !important;
    text-transform: none !important;
}
.content-surface .duplicate-table-shell .adminit-table-pagination {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
}
.content-surface .duplicate-table-shell .adminit-page-link,
.content-surface .duplicate-table-shell .adminit-page-current {
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
.content-surface .duplicate-table-shell .adminit-page-link {
    min-width: 124px !important;
    padding: 0 18px !important;
}
.content-surface .duplicate-table-shell .adminit-page-current {
    background: #0f3a72 !important;
    border-color: #3b82f6 !important;
    color: #ffffff !important;
}
.content-surface .duplicate-table-shell .adminit-page-link:disabled {
    opacity: 0.48 !important;
    color: #6b7788 !important;
    cursor: not-allowed !important;
}
.content-surface .duplicate-table-shell .adminit-prev-page::before {
    content: '\2039';
    margin-right: 8px;
    font-size: 26px;
    line-height: 0;
}
.content-surface .duplicate-table-shell .adminit-next-page::after {
    content: '\203A';
    margin-left: 8px;
    font-size: 26px;
    line-height: 0;
}
html:not(.dark) body .content-surface .duplicate-search-panel {
    background: #f8fafc !important;
    border-color: #d6e0ec !important;
}
html:not(.dark) body .content-surface .duplicate-filter-field label {
    color: #64748b !important;
}
html:not(.dark) body .content-surface .duplicate-search,
html:not(.dark) body .content-surface .duplicate-filter-select,
html:not(.dark) body .content-surface .duplicate-filter-reset {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
    color: #1f2937 !important;
}
html:not(.dark) body .content-surface .duplicate-table-shell,
html:not(.dark) body .content-surface .duplicate-table-shell .dataTables_scrollHead,
html:not(.dark) body .content-surface .duplicate-table-shell .dataTables_scrollHeadInner,
html:not(.dark) body .content-surface .duplicate-table-shell .dataTables_scrollHead table,
html:not(.dark) body .content-surface .duplicate-table-shell .dataTables_scrollBody,
html:not(.dark) body .content-surface .duplicate-table-shell .dataTables_scrollBody table {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
}
html:not(.dark) body .content-surface .duplicate-table-shell #duplicateTable thead th {
    background: #f8fafc !important;
    border-color: #e2e8f0 !important;
    color: #1e3a5f !important;
}
html:not(.dark) body .content-surface .duplicate-table-shell .adminit-table-footer {
    background: #ffffff !important;
    border-top-color: #d6e0ec !important;
}
html:not(.dark) body .content-surface .duplicate-table-shell .adminit-table-info {
    color: #334155 !important;
}
.duplicate-action-stack {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    white-space: nowrap;
}
body .content-surface #duplicateTable th:last-child,
body .content-surface #duplicateTable td:last-child {
    display: table-cell !important;
    width: 240px !important;
    min-width: 240px !important;
    max-width: 240px !important;
    text-align: center !important;
}
.duplicate-unit { min-width: 210px; }
.duplicate-unit-id {
    color: #0f172a;
    font-size: 13px;
    font-weight: 900;
}
.dark .duplicate-unit-id { color: #f8fafc; }
.duplicate-unit-meta {
    margin-top: 3px;
    color: #64748b;
    font-size: 10px;
    font-weight: 800;
    line-height: 1.4;
}
.dark .duplicate-unit-meta { color: #94a3b8; }
.duplicate-pill {
    display: inline-flex;
    align-items: center;
    min-height: 22px;
    padding: 4px 8px;
    border-radius: 4px;
    border: 1px solid rgba(148, 163, 184, 0.22);
    background: #f8fafc;
    color: #334155;
    font-size: 10px;
    font-weight: 900;
    text-transform: uppercase;
    white-space: nowrap;
}
.duplicate-count-pill {
    border-color: #bbf7d0;
    background: #dcfce7;
    color: #166534;
    border-radius: 999px;
    padding: 6px 12px;
}
.duplicate-pill-danger {
    border-color: rgba(239, 68, 68, 0.28);
    background: #fef2f2;
    color: #b91c1c;
}
.duplicate-pill-ok {
    border-color: rgba(34, 197, 94, 0.28);
    background: #f0fdf4;
    color: #15803d;
}
.duplicate-pill-warn {
    border-color: rgba(245, 158, 11, 0.3);
    background: #fffbeb;
    color: #b45309;
}
.dark .duplicate-pill {
    background: #0f172a;
    border-color: rgba(148, 163, 184, 0.22);
    color: #cbd5e1;
}
.dark .duplicate-pill-danger {
    background: rgba(127, 29, 29, 0.32);
    color: #fecaca;
}
.dark .duplicate-pill-ok {
    background: rgba(22, 101, 52, 0.26);
    color: #bbf7d0;
}
.dark .duplicate-pill-warn {
    background: rgba(120, 53, 15, 0.28);
    color: #fde68a;
}
.duplicate-search-panel,
.duplicate-table-shell {
    border-radius: 6px !important;
}
.duplicate-search-panel {
    margin: 0 0 10px !important;
    padding: 10px 12px !important;
    border: 1px solid #263244 !important;
    background: #111827 !important;
}
.duplicate-search-panel .duplicate-filter-field label {
    display: block !important;
    margin-bottom: 5px !important;
    color: #94a3b8 !important;
    font-size: 9px !important;
    line-height: 1 !important;
    letter-spacing: 0.08em !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
}
.duplicate-search,
.duplicate-filter-select,
.duplicate-filter-reset {
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
.duplicate-search { padding: 0 10px !important; }
.duplicate-filter-select { padding: 0 28px 0 10px !important; }
.duplicate-filter-reset {
    width: auto !important;
    padding: 0 12px !important;
    cursor: pointer !important;
}
.duplicate-table-shell {
    margin: 0 !important;
    padding: 0 !important;
    overflow: hidden !important;
    background: #111827 !important;
    border: 1px solid #263244 !important;
    box-shadow: none !important;
}
.duplicate-table-scroll { overflow-x: auto !important; }
body .content-surface .duplicate-table-shell #duplicateTable {
    width: 100% !important;
    min-width: 1900px !important;
    margin: 0 !important;
    border-collapse: collapse !important;
    border: 0 !important;
}
body .content-surface .duplicate-table-shell #duplicateTable thead th {
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
body .content-surface .duplicate-table-shell #duplicateTable tbody td {
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
body .content-surface .duplicate-table-shell #duplicateTable tbody tr:hover td {
    background: #172033 !important;
}
.duplicate-table-footer {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 12px !important;
    min-height: 64px !important;
    padding: 10px 20px !important;
    background: #111827 !important;
    border-top: 1px solid #263244 !important;
}
.duplicate-table-info {
    color: #dbeafe !important;
    font-size: 17px !important;
    font-weight: 900 !important;
    letter-spacing: 0 !important;
    text-transform: none !important;
}
.duplicate-table-pagination {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
}
.duplicate-page-link {
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
.duplicate-page-link.is-nav {
    min-width: 128px !important;
    padding: 0 18px !important;
    color: #cbd5e1 !important;
    font-size: 16px !important;
}
.duplicate-page-link.is-active {
    background: #0f3a72 !important;
    border-color: #3b82f6 !important;
    color: #ffffff !important;
}
.duplicate-page-link:disabled {
    opacity: 0.35 !important;
    cursor: not-allowed !important;
}
.duplicate-page-ellipsis {
    min-width: 26px !important;
    height: 44px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #64748b !important;
    font-size: 14px !important;
    font-weight: 900 !important;
}
html:not(.dark) body .content-surface .duplicate-search-panel,
html:not(.dark) body .content-surface .duplicate-table-shell,
html:not(.dark) body .content-surface .duplicate-table-footer {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
}
html:not(.dark) body .content-surface .duplicate-search,
html:not(.dark) body .content-surface .duplicate-filter-select,
html:not(.dark) body .content-surface .duplicate-filter-reset {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
    color: #1f2937 !important;
}
html:not(.dark) body .content-surface .duplicate-table-shell #duplicateTable thead th {
    background: #e8eef5 !important;
    border-color: #d6e0ec !important;
    color: #475569 !important;
}
html:not(.dark) body .content-surface .duplicate-table-shell #duplicateTable tbody td {
    background: #ffffff !important;
    border-color: #e2e8f0 !important;
    color: #334155 !important;
}
@media (max-width: 900px) {
    .duplicate-summary-grid,
    .duplicate-guide,
    .duplicate-search-panel { grid-template-columns: 1fr; }
    .duplicate-filter-reset { width: 100%; }
    .duplicate-step {
        border-right: 0;
        border-bottom: 1px solid rgba(148, 163, 184, 0.16);
    }
    .duplicate-step:last-child { border-bottom: 0; }
}

/* Final compact duplicate page pass. */
body .content-surface:has(.duplicate-page) {
    padding: 16px !important;
    border: 1px solid #273449 !important;
    border-radius: 14px !important;
    background: #111827 !important;
    box-shadow: none !important;
}
.duplicate-page {
    gap: 12px !important;
}
.duplicate-hero .page-header-block {
    min-height: 42px !important;
    padding: 8px 14px !important;
    border-radius: 7px !important;
    border-left-width: 4px !important;
    gap: 8px !important;
    align-items: center !important;
}
.duplicate-hero .page-title-standard {
    font-size: 19px !important;
    line-height: 1 !important;
    margin: 0 0 3px !important;
}
.duplicate-hero .page-subtitle-standard {
    font-size: 8px !important;
    line-height: 1.1 !important;
    letter-spacing: 0.24em !important;
}
body .content-surface .duplicate-hero .wt-btn {
    width: 106px !important;
    min-width: 106px !important;
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
    line-height: 1 !important;
    letter-spacing: 0.02em !important;
    white-space: nowrap !important;
}
body .content-surface .duplicate-hero .wt-btn i {
    width: 11px !important;
    font-size: 11px !important;
    line-height: 1 !important;
    margin: 0 !important;
}
.duplicate-table-footer {
    min-height: 44px !important;
    padding: 7px 12px !important;
    gap: 10px !important;
}
.duplicate-table-info {
    color: #334155 !important;
    font-size: 12px !important;
    font-weight: 800 !important;
    letter-spacing: 0 !important;
}
.duplicate-table-pagination {
    gap: 8px !important;
}
.duplicate-page-link {
    height: 30px !important;
    min-height: 30px !important;
    min-width: 34px !important;
    padding: 0 10px !important;
    border-radius: 7px !important;
    font-size: 11px !important;
    line-height: 1 !important;
}
.duplicate-page-link.is-nav {
    min-width: 74px !important;
    padding: 0 10px !important;
    font-size: 11px !important;
}
.duplicate-page-link.is-active {
    width: 34px !important;
    min-width: 34px !important;
}
.duplicate-page-ellipsis {
    height: 30px !important;
    min-width: 22px !important;
    font-size: 11px !important;
}
html:not(.dark) body .content-surface .duplicate-hero .page-header-block {
    background: #ffffff !important;
    border: 1px solid #d8e1ed !important;
    border-left: 4px solid #c28a48 !important;
}
html:not(.dark) body .content-surface .duplicate-hero .page-title-standard {
    color: #0f172a !important;
}
html:not(.dark) body .content-surface .duplicate-hero .page-subtitle-standard {
    color: #64748b !important;
}
html:not(.dark) body .content-surface .duplicate-hero .wt-btn {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
    color: #0f172a !important;
}
html:not(.dark) body .content-surface .duplicate-table-info {
    color: #334155 !important;
}
.dark body .content-surface .duplicate-hero .page-header-block {
    background: linear-gradient(90deg, rgba(31, 41, 55, 0.98), rgba(30, 41, 59, 0.98)) !important;
    border-color: rgba(148, 163, 184, 0.12) !important;
    border-left-color: #f2c48d !important;
}
.dark body .content-surface .duplicate-hero .wt-btn {
    background: #0f172a !important;
    border-color: #334155 !important;
    color: #e2e8f0 !important;
}
.dark body .content-surface .duplicate-table-info {
    color: #dbeafe !important;
}
html:not(.dark) body .content-surface:has(.duplicate-page) {
    background: #f5f8fc !important;
    border-color: #d8e1ed !important;
}
</style>

@include('wt.admin.partials.inventory-management-ui')

<style>
body .content-surface .duplicate-search-panel {
    display: flex !important;
    flex-direction: row !important;
    flex-wrap: nowrap !important;
    align-items: center !important;
    gap: 12px !important;
    width: 100% !important;
    max-width: none !important;
    margin: 0 0 10px !important;
    padding: 10px 12px 12px !important;
    border: 1px solid #d8e1ed !important;
    border-radius: 12px !important;
    background: #ffffff !important;
    box-shadow: none !important;
}
body .content-surface .duplicate-search-panel .duplicate-filter-field label {
    margin: 0 0 5px !important;
    color: #64748b !important;
    font-size: 8px !important;
    font-weight: 900 !important;
    letter-spacing: .14em !important;
    line-height: 1 !important;
    text-transform: uppercase !important;
}
body .content-surface .duplicate-search,
body .content-surface .duplicate-filter-select,
body .content-surface .duplicate-filter-reset {
    height: 32px !important;
    min-height: 32px !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 8px !important;
    background: #ffffff !important;
    color: #172033 !important;
    font-size: 11px !important;
    font-weight: 800 !important;
    box-shadow: none !important;
}
body .content-surface .duplicate-search {
    width: 100% !important;
    max-width: none !important;
    padding: 0 10px !important;
}
body .content-surface .duplicate-search::placeholder {
    color: #94a3b8 !important;
}
body .content-surface .duplicate-filter-select {
    padding: 0 28px 0 10px !important;
}
body .content-surface .duplicate-filter-reset {
    width: 82px !important;
    min-width: 82px !important;
    padding: 0 !important;
}
.dark body .content-surface .duplicate-search-panel {
    border-color: #263244 !important;
    background: #111827 !important;
}
.dark body .content-surface .duplicate-search,
.dark body .content-surface .duplicate-filter-select,
.dark body .content-surface .duplicate-filter-reset {
    border-color: #334155 !important;
    background: #0f172a !important;
    color: #e2e8f0 !important;
}
@media (max-width: 900px) {
    body .content-surface .duplicate-search-panel {
        grid-template-columns: 1fr !important;
        width: 100% !important;
    }
    body .content-surface .duplicate-filter-reset {
        width: 100% !important;
    }
}
body .content-surface .duplicate-hero,
body .content-surface .duplicate-hero .page-header-block,
html:not(.dark) body .content-surface .duplicate-hero,
html:not(.dark) body .content-surface .duplicate-hero .page-header-block,
html.dark body .content-surface .duplicate-hero,
html.dark body .content-surface .duplicate-hero .page-header-block,
.dark body .content-surface .duplicate-hero,
.dark body .content-surface .duplicate-hero .page-header-block {
    background: transparent !important;
    border: 0 !important;
    border-left: 0 !important;
    box-shadow: none !important;
}

body .content-surface .duplicate-hero .page-header-block {
    padding: 0 2px 12px !important;
    border-radius: 0 !important;
}

body .content-surface .duplicate-hero .wt-btn {
    min-width: 96px !important;
    height: 30px !important;
    min-height: 30px !important;
    padding: 0 11px !important;
    border-radius: 7px !important;
    font-size: 10px !important;
    gap: 6px !important;
}

body .content-surface .duplicate-hero .wt-btn i,
body .content-surface .duplicate-hero .wt-btn svg {
    font-size: 11px !important;
    width: 11px !important;
    height: 11px !important;
}
</style>

@php
    $totalRecords = $records->count();
@endphp

<div class="duplicate-page">
    <div class="duplicate-hero">
        <div class="page-header-block flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <h1 class="page-title-standard">Duplicated ID Management</h1>
                <p class="page-subtitle-standard">Review units that share a Radio ID or still need an ID change.</p>
            </div>
            @if(auth('wt')->user()->role === 'admin_it')
            <div class="flex flex-wrap items-center gap-2">
                <button onclick="openImportModal()" class="wt-btn wt-btn-soft">
                    <i class="fa-solid fa-file-import"></i>
                    Import Excel
                </button>
                <a href="{{ route('wt.admin.walkies.create.duplicate') }}" class="wt-btn wt-btn-soft">
                    <i class="fa-solid fa-plus"></i>
                    Add Item
                </a>
            </div>
            @endif
        </div>
    </div>

    <div class="duplicate-search-panel" role="search" aria-label="Duplicated ID filters">
        <div class="duplicate-filter-field">
            <label for="duplicateSearchInput">Search</label>
            <input id="duplicateSearchInput" type="search" class="duplicate-search" placeholder="Radio ID, serial, own">
        </div>
        <div class="duplicate-filter-field">
            <label for="duplicateStatusFilter">Status</label>
            <select id="duplicateStatusFilter" class="duplicate-filter-select">
                <option value="">All Status</option>
                @foreach(['IN USE', 'REPAIRING', 'UNKNOWN', 'UNUSED'] as $statusOption)
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
<div id="alertBox" class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-2xl flex items-center gap-3 animate-slide-in">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
    </svg>
    <span class="font-bold text-sm">{{ session('success') }}</span>
</div>
@endif

<div id="mainTableContainer" class="duplicate-table-shell">
    <div id="duplicateTableScroll" class="duplicate-table-scroll">
    <table id="duplicateTable" class="text-left">
        <thead>
            <tr>
                <th>RADIO ID</th>
                <th>STATUS</th>
                <th>SERIAL NO.</th>
                <th>MODEL</th>
                <th>CURRENT OWNERSHIP TYPE</th>
                <th>CURRENT OWNERSHIP</th>
                <th>DEPARTMENT</th>
                <th>NEED TO CHANGE ID INTO</th>
                <th>DONE</th>
                <th>OWNERSHIP TYPE TO BE</th>
                <th>REMARKS</th>
                <th class="text-center">ACTION</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $r)
            <tr class="duplicate-row"
                data-status="{{ strtoupper((string) ($r->status ?: '')) }}"
                data-done="{{ (int) ($r->id_change_done ?? 0) === 1 ? 'YES' : 'NO' }}"
                data-search="{{ strtoupper(trim(($r->radio_id ?? '') . ' ' . ($r->serial_number ?? '') . ' ' . ($r->model ?? '') . ' ' . ($r->status ?? '') . ' ' . ($r->ownership_type ?? '') . ' ' . ($r->shared_with ?? '') . ' ' . ($r->ownership ?? '') . ' ' . ($r->department ?? '') . ' ' . ($r->position ?? '') . ' ' . ($r->temporary_radio_id ?? '') . ' ' . ($r->tracking_ref ?? '') . ' ' . ($r->remark ?? '') . ' ' . ($r->need_to_change_id ?? '') . ' ' . ($r->ownership_type_to_be ?? ''))) }}">
                <td>{{ $r->radio_id ?: '-' }}</td>
                <td>{{ $r->status ?: '-' }}</td>
                <td>{{ $r->serial_number ?: '-' }}</td>
                <td>{{ $r->model ?: '-' }}</td>
                <td>{{ $r->ownership_type ?: '-' }}</td>
                <td>{{ $r->ownership ?: '-' }}</td>
                <td>{{ $r->department ?: '-' }}</td>
                <td>{{ $r->need_to_change_id ?: '-' }}</td>
                <td>{{ (int) ($r->id_change_done ?? 0) === 1 ? 'YES' : 'NO' }}</td>
                <td>{{ $r->ownership_type_to_be ?: '-' }}</td>
                <td class="wrap-cell">{{ $r->remark ?: '-' }}</td>
                <td class="text-center">
                    @if(auth('wt')->user()->role === 'admin_it')
                    <div class="duplicate-action-stack">
                        <button type="button" class="wt-btn wt-btn-sm duplicate-action-view" onclick="openGlobalWalkieTimeline('{{ $r->walkie_id }}')">
                            <i class="fa-solid fa-eye"></i>
                            <span>View</span>
                        </button>
                        <a href="{{ route('wt.admin.walkies.edit', ['walkie' => $r->walkie_id, 'source' => 'duplicate']) }}"
                            class="wt-btn wt-btn-sm duplicate-action-edit">
                            <i class="fa-solid fa-pen-to-square"></i>
                            <span>Edit</span>
                        </a>
                        <form action="{{ route('wt.admin.walkies.destroy', $r->walkie_id) }}" method="POST" class="inline" data-modern-confirm="Delete duplicated ID record for {{ $r->radio_id ?? '-' }}?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="wt-btn wt-btn-danger wt-btn-sm duplicate-action-delete">
                                <i class="fa-solid fa-trash"></i>
                                <span>Delete</span>
                            </button>
                        </form>
                    </div>
                    @else
                    <button type="button" class="wt-btn wt-btn-sm duplicate-action-view" onclick="openGlobalWalkieTimeline('{{ $r->walkie_id }}')">
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
    <div id="duplicatePagination" class="duplicate-table-footer">
        <div class="duplicate-table-info">
            Total: <span id="duplicateTotalItems">0</span> items
        </div>
    </div>
</div>
</div>

{{-- ===================== ADD RECORD MODAL ===================== --}}
@if(auth('wt')->user()->role === 'admin_it')
<div id="addModal" class="modal-overlay" onclick="closeAddModalOutside(event)">
    <div class="modal-box">
        <div class="modal-header">
            <div>
                <h2 class="modal-title">Add Duplicated ID Record</h2>
            </div>
            <button onclick="closeAddModal()" class="modal-close-btn" title="Close">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                </svg>
            </button>
        </div>
        <form action="{{ route('wt.admin.walkies.store') }}" method="POST" class="flex flex-col h-full overflow-hidden">
            @csrf
            {{-- Force this record to be flagged as needing ID change --}}
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
                    <div class="form-group" style="grid-column: span 3;">
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
                    <div class="form-group" style="grid-column: span 3;">
                        <label class="form-label">Remarks</label>
                        <textarea name="remark" class="form-input" style="height:35px; resize:none;" placeholder="Remarks"></textarea>
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

{{-- ===================== QUICK UPDATE MODAL ===================== --}}
<div id="editModal" class="modal-overlay" onclick="closeEditModalOutside(event)">
    <div class="modal-box" id="editModalBox">
        <div class="modal-header">
            <div>
                <h2 class="modal-title">Update Unit Details</h2>
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
                        <label class="form-label">Temporary / Swapped WT Radio ID</label>
                        <input type="text" name="temporary_radio_id" id="edit_temporary_radio_id" class="form-input" placeholder="Temporary / swapped radio ID">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tracking REF</label>
                        <input type="text" name="tracking_ref" id="edit_tracking_ref" class="form-input" placeholder="Tracking reference">
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
                            @foreach(['SPARE','UNALLOCATED'] as $targetOwnershipType)
                            <option value="{{ $targetOwnershipType }}">{{ $targetOwnershipType }}</option>
                            @endforeach
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
@if(auth('wt')->user()->role === 'admin_it')
<div id="importModal" class="modal-overlay" onclick="closeImportModalOutside(event)">
    <div class="modal-box" style="max-width: 500px;">
        <div class="modal-header">
            <div>
                <h2 class="modal-title">Bulk Import Duplicated IDs</h2>
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
                             <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#3D2B1F" viewBox="0 0 16 16">
                                <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                                <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
                            </svg>
                        </div>
                        <p class="text-xs font-bold text-stone-700" id="fileNameDisplay">Click to upload Excel or CSV</p>
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
body .content-surface:has(.duplicate-page) {
    background: #0b1220 !important;
    border: 0 !important;
    border-radius: 0 !important;
    padding: 10px !important;
    box-shadow: none !important;
}

body .content-surface .duplicate-page {
    background: transparent !important;
    border: 0 !important;
    padding: 0 !important;
    gap: 12px !important;
}

body .content-surface .duplicate-hero .page-header-block {
    padding: 0 2px 10px !important;
    background: transparent !important;
    border: 0 !important;
    box-shadow: none !important;
}

body .content-surface .duplicate-hero .page-title-standard {
    margin: 0 !important;
    color: #f8fafc !important;
    font-size: 19px !important;
    line-height: 1.1 !important;
}

body .content-surface .duplicate-hero .page-subtitle-standard {
    max-width: 560px !important;
    margin-top: 5px !important;
    color: #93a4ba !important;
    font-size: 9px !important;
    letter-spacing: .16em !important;
    line-height: 1.45 !important;
}

body .content-surface .duplicate-hero .wt-btn {
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

body .content-surface .duplicate-search-panel {
    display: grid !important;
    grid-template-columns: minmax(0, 1fr) 360px 82px !important;
    gap: 5px !important;
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

body .content-surface .duplicate-search-panel > .duplicate-filter-field {
    display: inline-flex !important;
    flex-direction: row !important;
    align-items: center !important;
    gap: 7px !important;
    width: auto !important;
    min-width: 0 !important;
    max-width: none !important;
}

body .content-surface .duplicate-filter-field label {
    flex: 0 0 auto !important;
    margin: 0 !important;
    color: #8ea0b8 !important;
    font-size: 9px !important;
    letter-spacing: .12em !important;
    white-space: nowrap !important;
}

body .content-surface .duplicate-search,
body .content-surface .duplicate-filter-select,
body .content-surface .duplicate-filter-reset {
    height: 38px !important;
    min-height: 38px !important;
    border-radius: 10px !important;
    border: 1px solid rgba(148, 163, 184, .26) !important;
    background: #111827 !important;
    color: #e5edf7 !important;
    font-size: 12px !important;
    font-weight: 750 !important;
}

body .content-surface .duplicate-search {
    flex: 1 1 260px !important;
    width: auto !important;
    min-width: 190px !important;
    max-width: none !important;
    padding: 0 14px !important;
}

body .content-surface .duplicate-search-panel > .duplicate-filter-field:has(.duplicate-search) {
    flex: 1 1 360px !important;
}

body .content-surface .duplicate-search-panel > .duplicate-filter-field:has(.duplicate-filter-select) {
    flex: 0 0 auto !important;
}

body .content-surface .duplicate-filter-select {
    width: auto !important;
    min-width: 100px !important;
    padding: 0 30px 0 14px !important;
}

body .content-surface .duplicate-filter-reset {
    flex: 0 0 auto !important;
    width: auto !important;
    min-width: 82px !important;
    padding: 0 10px !important;
    background: transparent !important;
    color: #dbeafe !important;
}

body .content-surface .duplicate-table-shell #duplicateTable {
    min-width: 1680px !important;
}

body .content-surface .duplicate-table-shell #duplicateTable th:last-child,
body .content-surface .duplicate-table-shell #duplicateTable td:last-child {
    width: 190px !important;
    min-width: 190px !important;
    max-width: 190px !important;
    text-align: center !important;
}

body .content-surface .duplicate-table-shell #duplicateTable .duplicate-action-stack {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 4px !important;
    width: 100% !important;
    white-space: nowrap !important;
}

body .content-surface .duplicate-table-shell #duplicateTable .duplicate-action-stack form {
    display: inline-flex !important;
    margin: 0 !important;
}

body .content-surface .duplicate-table-shell #duplicateTable .duplicate-action-stack .wt-btn {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 3px !important;
    width: auto !important;
    min-width: 0 !important;
    height: 24px !important;
    min-height: 24px !important;
    padding: 0 6px !important;
    border: 1px solid transparent !important;
    border-radius: 5px !important;
    color: #ffffff !important;
    font-size: 10px !important;
    font-weight: 800 !important;
    line-height: 1 !important;
    letter-spacing: 0 !important;
    text-decoration: none !important;
    text-transform: none !important;
    box-shadow: none !important;
}

body .content-surface .duplicate-table-shell #duplicateTable .duplicate-action-stack .wt-btn i {
    font-size: 10px !important;
    line-height: 1 !important;
}

body .content-surface .duplicate-table-shell #duplicateTable .duplicate-action-view {
    width: 46px !important;
    min-width: 46px !important;
    max-width: 46px !important;
    border-color: #0dcaf0 !important;
    background: #0dcaf0 !important;
    color: #052c33 !important;
}

body .content-surface .duplicate-table-shell #duplicateTable .duplicate-action-edit {
    width: 52px !important;
    min-width: 52px !important;
    max-width: 52px !important;
    border-color: #0d6efd !important;
    background: #0d6efd !important;
    color: #ffffff !important;
}

body .content-surface .duplicate-table-shell #duplicateTable .duplicate-action-delete {
    width: 62px !important;
    min-width: 62px !important;
    max-width: 62px !important;
    border-color: #dc3545 !important;
    background: #dc3545 !important;
    color: #ffffff !important;
}

@media (max-width: 1250px) {
    body .content-surface .duplicate-search-panel {
        flex-wrap: wrap !important;
        align-items: stretch !important;
    }

    body .content-surface .duplicate-search-panel > .duplicate-filter-field {
        flex: 1 1 240px !important;
    }

    body .content-surface .duplicate-filter-reset {
        flex: 1 1 100% !important;
    }
}
</style>

<script>
$(document).ready(function() {
    const duplicateSearchInput = document.getElementById('duplicateSearchInput');
    const duplicateStatusFilter = document.getElementById('duplicateStatusFilter');
    const duplicateDoneFilter = document.getElementById('duplicateDoneFilter');
    const duplicateResetFilters = document.getElementById('duplicateResetFilters');
    const rows = Array.from(document.querySelectorAll('#duplicateTable tbody .duplicate-row'));
    const paginationContainer = document.querySelector('.duplicate-table-pagination');
    const totalItems = document.getElementById('duplicateTotalItems');
    const itemsPerPage = 10;
    const maxVisiblePages = 4;
    let currentPage = 1;
    let filteredRows = [];

    function updateDuplicateTableDisplay() {
        rows.forEach(row => row.style.display = 'none');
        filteredRows.forEach(row => row.style.display = '');
    }

    function renderDuplicatePagination() {
        if (totalItems) totalItems.innerText = filteredRows.length;
        if (paginationContainer) paginationContainer.innerHTML = '';
    }

    function changeDuplicatePage(page) {
        currentPage = page;
        updateDuplicateTableDisplay();
        renderDuplicatePagination();
    }

    function normalizeDuplicateFilter(value) {
        return String(value || '').trim().replace(/\s+/g, ' ').toUpperCase();
    }

    function applyDuplicateFilters() {
        const searchValue = normalizeDuplicateFilter(duplicateSearchInput?.value);
        const statusValue = normalizeDuplicateFilter(duplicateStatusFilter?.value);
        const doneValue = normalizeDuplicateFilter(duplicateDoneFilter?.value);
        filteredRows = rows.filter((row) => {
            const matchesSearch = !searchValue || normalizeDuplicateFilter(row.dataset.search).includes(searchValue);
            const matchesStatus = !statusValue || normalizeDuplicateFilter(row.dataset.status) === statusValue;
            const matchesDone = !doneValue || normalizeDuplicateFilter(row.dataset.done) === doneValue;
            return matchesSearch && matchesStatus && matchesDone;
        });
        currentPage = 1;
        updateDuplicateTableDisplay();
        renderDuplicatePagination();
    }

    if (duplicateSearchInput) duplicateSearchInput.addEventListener('input', applyDuplicateFilters);
    if (duplicateStatusFilter) duplicateStatusFilter.addEventListener('change', applyDuplicateFilters);
    if (duplicateDoneFilter) duplicateDoneFilter.addEventListener('change', applyDuplicateFilters);
    if (duplicateResetFilters) {
        duplicateResetFilters.addEventListener('click', function() {
            if (duplicateSearchInput) duplicateSearchInput.value = '';
            if (duplicateStatusFilter) duplicateStatusFilter.value = '';
            if (duplicateDoneFilter) duplicateDoneFilter.value = '';
            applyDuplicateFilters();
        });
    }
    applyDuplicateFilters();
});

function openEditModal(id, radio, serialNumber, model, status, ownershipType, ownership, position, department, temporaryRadioId, trackingRef, remark, needToChangeId, idChangeDone, ownershipTypeToBe) {
    const form = document.getElementById('editWalkieForm');
    form.action = "{{ route('wt.admin.walkies.updateMeta', ['walkie' => '__ID__']) }}".replace('__ID__', id);
    document.getElementById('edit_radio_id').value = radio || '';
    document.getElementById('edit_serial_number').value = serialNumber || '';
    document.getElementById('edit_model').value = model || '';
    document.getElementById('edit_status').value = status || '';
    document.getElementById('edit_ownership_type').value = ownershipType || '';
    document.getElementById('edit_ownership').value = ownership || '';
    document.getElementById('edit_position').value = position || '';
    document.getElementById('edit_department').value = department || '';
    document.getElementById('edit_temporary_radio_id').value = temporaryRadioId || '';
    document.getElementById('edit_tracking_ref').value = trackingRef || '';
    document.getElementById('edit_remark').value = remark || '';
    document.getElementById('edit_need_to_change_id').value = needToChangeId || '';
    document.getElementById('edit_id_change_done').value = idChangeDone || '0';
    document.getElementById('edit_ownership_type_to_be').value = ownershipTypeToBe || '';
    document.getElementById('editModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
    document.body.style.overflow = '';
}

function closeEditModalOutside(event) {
    if (event.target === document.getElementById('editModal')) closeEditModal();
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

function openImportModal() {
    document.getElementById('importModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeImportModal() {
    document.getElementById('importModal').classList.remove('active');
    document.body.style.overflow = '';
}

function closeImportModalOutside(event) {
    if (event.target === document.getElementById('importModal')) closeImportModal();
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
    if (event.target === document.getElementById('addModal')) closeAddModal();
}

function updateFileName(input) {
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    if (input.files && input.files.length > 0) {
        fileNameDisplay.innerText = "Selected: " + input.files[0].name;
        fileNameDisplay.classList.add('text-green-600');
    } else {
        fileNameDisplay.innerText = "Click to upload Excel or CSV";
        fileNameDisplay.classList.remove('text-green-600');
    }
}
</script>
@endsection

