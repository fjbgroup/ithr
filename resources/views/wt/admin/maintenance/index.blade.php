@extends('wt.layouts.admin')

@section('title', 'Under Repair / Faulty Units')

@push('styles')
<style>
    .clean-admin-filter {
        margin: 0 0 10px;
        padding: 10px 12px;
        border-radius: 6px;
        background: #111827;
        border: 1px solid #263244;
        box-shadow: none;
    }
    .clean-admin-filter-grid {
        display: grid;
        grid-template-columns: minmax(260px, 1fr) 180px auto;
        gap: 10px;
        align-items: end;
    }
    .clean-admin-label {
        display: block;
        margin-bottom: 5px;
        color: #94a3b8;
        font-size: 9px;
        line-height: 1;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .clean-admin-input,
    .clean-admin-select,
    .clean-admin-reset {
        width: 100%;
        height: 32px;
        border-radius: 6px;
        background: #0f172a;
        border: 1px solid #334155;
        color: #e5e7eb;
        font-size: 11px;
        font-weight: 400;
        box-shadow: none;
        outline: none;
    }
    .clean-admin-input { padding: 0 10px; }
    .clean-admin-select { padding: 0 28px 0 10px; }
    .clean-admin-reset {
        width: auto;
        padding: 0 12px;
        cursor: pointer;
    }
    .clean-admin-table-shell {
        margin: 0;
        padding: 0;
        border-radius: 6px;
        overflow: hidden;
        background: #111827;
        border: 1px solid #263244;
        box-shadow: none;
    }
    .clean-admin-table-scroll { overflow-x: auto; }
    .clean-admin-table {
        width: 100%;
        min-width: 1180px;
        margin: 0;
        border-collapse: collapse;
        border: 0;
    }
    .clean-admin-table thead th {
        height: 34px;
        padding: 8px 10px;
        background: #1f2937;
        border: 1px solid #2f3b4f;
        color: #cbd5e1;
        font-size: 10px;
        font-weight: 600;
        line-height: 1.1;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .clean-admin-table tbody td {
        height: 38px;
        padding: 7px 10px;
        background: #111827;
        border: 1px solid #263244;
        color: #dbe4f0;
        font-size: 11px;
        font-weight: 400;
        line-height: 1.25;
        vertical-align: middle;
    }
    .clean-admin-table tbody tr:hover td { background: #172033; }
    .clean-admin-pill {
        display: inline-flex;
        align-items: center;
        padding: 3px 7px;
        border-radius: 4px;
        background: #243044;
        border: 1px solid #3b4a60;
        color: #f1f5f9;
        font-size: 9px;
        font-weight: 500;
        line-height: 1.2;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .clean-admin-actions {
        display: flex;
        align-items: center;
        gap: 5px;
        flex-wrap: nowrap;
    }
    .clean-admin-actions .wt-btn {
        min-height: 24px !important;
        height: 24px !important;
        padding: 3px 8px !important;
        border-radius: 4px !important;
        font-size: 9px !important;
        font-weight: 500 !important;
        letter-spacing: 0.03em !important;
        white-space: nowrap !important;
    }
    #maintTable .maintenance-action-col {
        display: table-cell !important;
        width: 190px !important;
        min-width: 190px !important;
        max-width: 190px !important;
        text-align: center !important;
    }
    #maintTable .maintenance-action-stack {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 6px !important;
        width: 100% !important;
        white-space: nowrap !important;
    }
    #maintTable .maintenance-action-stack form {
        display: inline-flex !important;
        margin: 0 !important;
    }
    #maintTable .maintenance-action-stack .wt-btn {
        min-height: 28px !important;
        height: 28px !important;
        padding: 0 8px !important;
        border-radius: 6px !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        letter-spacing: 0 !important;
        text-transform: none !important;
        white-space: nowrap !important;
    }
    #maintTable .maintenance-action-stack .wt-btn-repairing {
        border-color: #f59e0b !important;
        background: #f59e0b !important;
        color: #111827 !important;
    }
    #maintTable .maintenance-action-stack .wt-btn-fixed {
        border-color: #16a34a !important;
        background: #16a34a !important;
        color: #ffffff !important;
    }
    .repair-table-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        border-top: 1px solid #263244;
        min-height: 64px;
        padding: 10px 20px;
        background: #111827;
    }
    .repair-table-info {
        color: #dbeafe;
        font-size: 17px;
        font-weight: 900;
        letter-spacing: 0;
        text-transform: none;
        white-space: nowrap;
    }
    .repair-table-pagination {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
    }
    .repair-page-btn,
    .repair-page-number {
        min-width: 42px;
        height: 34px;
        border: 1px solid #2f4d74;
        border-radius: 7px;
        background: #0f172a;
        color: #bfdbfe;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        transition: border-color 0.15s ease, background 0.15s ease, color 0.15s ease, opacity 0.15s ease;
    }
    .repair-page-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-width: 92px;
        padding: 0 12px;
        color: #cbd5e1;
        font-size: 12px;
    }
    .repair-page-btn:hover:not(:disabled),
    .repair-page-number:hover {
        border-color: #3b82f6;
        background: #172033;
        color: #ffffff;
    }
    .repair-page-btn:disabled {
        cursor: not-allowed;
        border-color: #223047;
        color: #536477;
        opacity: 0.55;
    }
    .repair-page-number.is-active {
        border-color: #3b82f6;
        background: #0f3a72;
        color: #ffffff;
    }
    .repair-page-ellipsis {
        display: inline-flex;
        align-items: center;
        height: 34px;
        color: #94a3b8;
        font-size: 11px;
        font-weight: 900;
    }
    html:not(.dark) body .content-surface .clean-admin-filter {
        background: #f8fafc !important;
        border: 1px solid #d6e0ec !important;
    }
    html:not(.dark) body .content-surface .clean-admin-label {
        color: #64748b !important;
    }
    html:not(.dark) body .content-surface .clean-admin-input,
    html:not(.dark) body .content-surface .clean-admin-select,
    html:not(.dark) body .content-surface .clean-admin-reset {
        background: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        color: #1f2937 !important;
    }
    html:not(.dark) body .content-surface .clean-admin-input::placeholder {
        color: #94a3b8 !important;
    }
    html:not(.dark) body .content-surface .clean-admin-table-shell {
        background: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
    }
    html:not(.dark) body .content-surface .clean-admin-table thead th {
        background: #e8eef5 !important;
        border: 1px solid #d6e0ec !important;
        color: #475569 !important;
    }
    html:not(.dark) body .content-surface .clean-admin-table tbody td {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        color: #334155 !important;
    }
    html:not(.dark) body .content-surface .clean-admin-table tbody tr:hover td {
        background: #f8fafc !important;
    }
    html:not(.dark) body .content-surface .clean-admin-pill {
        background: #eef4fb !important;
        border: 1px solid #d6e0ec !important;
        color: #334155 !important;
    }
    html:not(.dark) body .content-surface .repair-table-footer {
        background: #ffffff !important;
        border-top: 1px solid #d6e0ec !important;
    }
    html:not(.dark) body .content-surface .repair-table-info {
        color: #64748b !important;
    }
    html:not(.dark) body .content-surface .repair-page-btn,
    html:not(.dark) body .content-surface .repair-page-number {
        background: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        color: #475569 !important;
    }
    html:not(.dark) body .content-surface .repair-page-btn:hover:not(:disabled),
    html:not(.dark) body .content-surface .repair-page-number:hover {
        background: #eef4fb !important;
        border-color: #b9c7d8 !important;
        color: #1f2937 !important;
    }
    html:not(.dark) body .content-surface .repair-page-btn:disabled {
        border-color: #e2e8f0 !important;
        color: #94a3b8 !important;
    }
    html:not(.dark) body .content-surface .repair-page-number.is-active {
        background: #e0f2fe !important;
        border-color: #7dd3fc !important;
        color: #075985 !important;
    }
    html:not(.dark) body .content-surface .bg-slate-700\/80 {
        background: #e2e8f0 !important;
        border-color: #cbd5e1 !important;
    }
    html:not(.dark) body .content-surface .text-slate-400 {
        color: #64748b !important;
    }
    @media (max-width: 900px) {
        .clean-admin-filter-grid { grid-template-columns: 1fr; }
        .repair-table-footer {
            align-items: stretch;
            flex-direction: column;
        }
        .repair-table-pagination {
            justify-content: flex-start;
            overflow-x: auto;
        }
    }
    .maintenance-page-shell {
        display: grid !important;
        gap: 14px !important;
        padding: 0 14px !important;
    }
    .maintenance-page-shell > .page-header-block {
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
    .maintenance-page-shell .page-title-standard {
        color: #f8fafc !important;
        font-size: 22px !important;
        font-weight: 900 !important;
        line-height: 1.1 !important;
        margin: 0 0 8px !important;
    }
    .maintenance-page-shell .page-subtitle-standard {
        color: #aab5c7 !important;
        font-size: 12px !important;
        font-weight: 900 !important;
        letter-spacing: 0.22em !important;
        line-height: 1.2 !important;
        margin: 0 !important;
        text-transform: uppercase !important;
    }
    .maintenance-count-pill {
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
    .maintenance-page-shell > .page-header-block .wt-btn {
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
    .maintenance-page-shell > .page-header-block .wt-btn svg {
        width: 11px !important;
        height: 11px !important;
        margin-right: 0 !important;
        flex: 0 0 auto !important;
    }
    body .content-surface .maintenance-page-shell .repair-page-btn,
    body .content-surface .maintenance-page-shell .repair-page-number {
        height: 30px !important;
        min-height: 30px !important;
        min-width: 34px !important;
        padding: 0 10px !important;
        border-radius: 7px !important;
        font-size: 11px !important;
        line-height: 1 !important;
    }
    body .content-surface .maintenance-page-shell .repair-page-btn {
        min-width: 74px !important;
    }
    body .content-surface .maintenance-page-shell .repair-page-number.is-active {
        min-width: 34px !important;
        width: 34px !important;
    }
    html:not(.dark) body .content-surface .maintenance-page-shell > .page-header-block .wt-btn {
        background: #ffffff !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }
    .dark body .content-surface .maintenance-page-shell > .page-header-block .wt-btn {
        background: #0f172a !important;
        border-color: #334155 !important;
        color: #e2e8f0 !important;
    }

    body .content-surface .maintenance-page-shell .clean-admin-filter {
        width: 100% !important;
        margin: 0 0 10px !important;
        padding: 16px 18px 18px !important;
        border: 1px solid #d8e1ed !important;
        border-radius: 18px !important;
        background: #ffffff !important;
        box-shadow: none !important;
    }
    body .content-surface .maintenance-page-shell .clean-admin-filter-grid {
        display: grid !important;
        grid-template-columns: minmax(360px, 1fr) 208px 124px !important;
        gap: 16px !important;
        align-items: end !important;
        width: 100% !important;
    }
    body .content-surface .maintenance-page-shell .clean-admin-label {
        margin: 0 0 10px !important;
        color: #64748b !important;
        font-size: 10px !important;
        font-weight: 900 !important;
        letter-spacing: .18em !important;
        line-height: 1 !important;
        text-transform: uppercase !important;
    }
    body .content-surface .maintenance-page-shell .clean-admin-input,
    body .content-surface .maintenance-page-shell .clean-admin-select,
    body .content-surface .maintenance-page-shell .clean-admin-reset {
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
    body .content-surface .maintenance-page-shell .clean-admin-input {
        padding: 0 16px !important;
    }
    body .content-surface .maintenance-page-shell .clean-admin-input::placeholder {
        color: #94a3b8 !important;
    }
    body .content-surface .maintenance-page-shell .clean-admin-select {
        padding: 0 36px 0 16px !important;
    }
    body .content-surface .maintenance-page-shell .clean-admin-reset {
        width: 124px !important;
        padding: 0 !important;
    }
    .dark body .content-surface .maintenance-page-shell .clean-admin-filter {
        border-color: #263244 !important;
        background: #111827 !important;
    }
    .dark body .content-surface .maintenance-page-shell .clean-admin-input,
    .dark body .content-surface .maintenance-page-shell .clean-admin-select,
    .dark body .content-surface .maintenance-page-shell .clean-admin-reset {
        border-color: #334155 !important;
        background: #0f172a !important;
        color: #e2e8f0 !important;
    }
    @media (max-width: 900px) {
        body .content-surface .maintenance-page-shell .clean-admin-filter-grid {
            grid-template-columns: 1fr !important;
        }
        body .content-surface .maintenance-page-shell .clean-admin-reset {
            width: 100% !important;
        }
    }
</style>
@endpush

@section('content')

@include('wt.admin.partials.inventory-management-ui')

<style>
    body .content-surface .maintenance-page-shell #maintTable :is(th, td),
    body .content-surface #maintTable.clean-admin-table :is(th, td) {
        display: table-cell !important;
    }

    body .content-surface .maintenance-page-shell #maintTable {
        min-width: 1480px !important;
        width: max-content !important;
        table-layout: fixed !important;
    }

    body .content-surface .maintenance-page-shell #maintTable .maintenance-action-col {
        width: 280px !important;
        min-width: 280px !important;
        max-width: 280px !important;
    }
</style>

<div class="maintenance-page-shell">
<div class="page-header-block flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h3 class="page-title-standard">Under Repair / Faulty</h3>
        <p class="page-subtitle-standard">
            Manage unit maintenance, repair logs, and faulty equipment.
        </p>
    </div>
    <div class="flex flex-shrink-0 items-center gap-2">
        <button onclick="openImportModal()" class="wt-btn wt-btn-soft">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="margin-right:5px;">
                <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
            </svg>
            Import Excel
        </button>

        <a href="{{ route('wt.admin.maintenance.create') }}" class="wt-btn wt-btn-soft">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 1a.5.5 0 0 1 .5.5V7.5H14a.5.5 0 0 1 0 1H8.5V14a.5.5 0 0 1-1 0V8.5H2a.5.5 0 0 1 0-1h5.5V1.5A.5.5 0 0 1 8 1z"/>
            </svg>
            Add Item
        </a>
    </div>
</div>

{{-- Alerts --}}
@if(session('success'))
<div class="alert-success mb-6" id="alertBox">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="mr-2 flex-shrink-0">
        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
    </svg>
    {{ session('success') }}
</div>
@endif

@php 
    $anyErrors = ($errors instanceof \Illuminate\Support\ViewErrorBag) && $errors->any();
@endphp
@if($anyErrors)
<div class="alert-error mb-6" id="errorBox">
    <ul class="list-disc list-inside">
        @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="clean-admin-filter" style="justify-content: flex-start !important; width: auto !important;">
    <div class="clean-admin-filter-grid" style="justify-content: flex-start !important; width: auto !important;">
        <div>
            <label class="clean-admin-label" for="maintSearch">Search</label>
            <input type="text" id="maintSearch" class="clean-admin-input" placeholder="Keywords" style="width: 250px !important; max-width: 250px !important;">
        </div>
        <div>
            <label class="clean-admin-label" for="maintStatus">Status</label>
            <select id="maintStatus" class="clean-admin-select">
                <option value="">All Status</option>
                <option value="FAULTY">Faulty</option>
                <option value="UNDER REPAIR">Under Repair</option>
                <option value="REPAIRING">Repairing</option>
                <option value="B.E.R">B.E.R</option>
                <option value="READY TO COLLECT">Ready To Collect</option>
                <option value="ALREADY FIXED">Already Fixed</option>
                <option value="DONE">Done</option>
            </select>
        </div>
        <button type="button" id="maintReset" class="clean-admin-reset">Reset</button>
    </div>
</div>
<div class="clean-admin-table-shell" id="mainTableContainer">
    <div class="clean-admin-table-scroll">
    <table id="maintTable" class="clean-admin-table table-auto w-full text-left">
        <thead>
            <tr>
                <th class="px-2 py-1 text-center">Radio ID</th>
                <th class="px-2 py-1 text-center">Status</th>
                <th class="px-2 py-1 text-center">Serial No.</th>
                <th class="px-2 py-1 text-center">Model</th>
                <th class="px-2 py-1 text-center">Location</th>
                <th class="px-2 py-1 text-center">Issue / Received</th>
                <th class="px-2 py-1 text-center maintenance-action-col">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $r)
            <tr class="maint-row"
                data-status="{{ strtoupper((string) $r->status) }}"
                data-search="{{ strtoupper(trim(($r->radio_id ?? '') . ' ' . ($r->status ?? '') . ' ' . ($r->serial_number ?? '') . ' ' . ($r->model ?? '') . ' ' . ($r->walkieTalkie->ownership_type ?? $r->ownership_type ?? '') . ' ' . ($r->current_ownership ?? '') . ' ' . ($r->department_name ?? '') . ' ' . ($r->location ?? $r->walkieTalkie->location ?? '') . ' ' . ($r->received_date ?? '') . ' ' . ($r->repair_date ?? '') . ' ' . ($r->finish_date ?? '') . ' ' . ($r->issue ?? '') . ' ' . ($r->issue_description ?? '') . ' ' . ($r->remarks ?? ''))) }}">
                <td>{{ $r->radio_id ?? '-' }}</td>
                <td>
                    @php
                        $badgeBg = '#f5f5f4'; $badgeColor = '#78716c';
                        if($r->status === 'FAULTY') { $badgeBg = '#fee2e2'; $badgeColor = '#ef4444'; }
                        elseif($r->status === 'UNDER REPAIR' || $r->status === 'REPAIRING') { $badgeBg = '#ffedd5'; $badgeColor = '#f97316'; }
                        elseif($r->status === 'READY TO COLLECT') { $badgeBg = '#ecfccb'; $badgeColor = '#65a30d'; }
                        elseif($r->status === 'DONE' || $r->status === 'ALREADY FIXED') { $badgeBg = '#dcfce7'; $badgeColor = '#22c55e'; }
                    @endphp
                    <span class="clean-admin-pill">{{ $r->status }}</span>
                </td>
                <td>{{ $r->serial_number ?? '-' }}</td>
                <td>{{ $r->model ?? '-' }}</td>
                <td>{{ $r->location ?? $r->walkieTalkie->location ?? '-' }}</td>
                <td>
                    <div class="inventory-item-title">{{ $r->issue ?? $r->issue_description ?? '-' }}</div>
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ $r->received_date ?? '-' }}</div>
                </td>
                <td class="maintenance-action-col">
                    <div class="maintenance-action-stack">
                        <button type="button" class="wt-btn wt-btn-sm maintenance-action-view" onclick="openGlobalMaintenanceTimeline('{{ $r->maintenance_id }}')">
                            <i class="fa-solid fa-eye"></i>
                            <span>View</span>
                        </button>
                        <a href="{{ route('wt.admin.maintenance.edit', $r->maintenance_id) }}" class="wt-btn wt-btn-sm maintenance-action-edit">
                            <i class="fa-solid fa-pen-to-square"></i>
                            <span>Edit</span>
                        </a>
                        @if(auth('wt')->user()->wt_role === 'admin_it')
                            <form action="{{ route('wt.admin.maintenance.destroy', $r->maintenance_id) }}" method="POST" data-modern-confirm="Delete maintenance record for {{ $r->radio_id ?? '-' }}?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="wt-btn wt-btn-sm wt-btn-danger maintenance-action-delete">
                                    <i class="fa-solid fa-trash"></i>
                                    <span>Delete</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    <div class="repair-table-footer">
        <div id="maintPageInfo" class="repair-table-info">Total: 0 items</div>
    </div>
</div>
</div>

{{-- ===================== ADD MODAL ===================== --}}
<div id="repairModal" class="modal-overlay" onclick="closeModalOutside(event)">
    <div class="modal-box p-8">
        <div class="modal-header">
            <div>
                <h2 class="modal-title">New Repair Record</h2>
                <p class="modal-subtitle">Register a unit maintenance or breakdown entry.</p>
            </div>
            <button onclick="closeRepairModal()" class="modal-close-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg>
            </button>
        </div>

        <form action="{{ route('wt.admin.maintenance.store') }}" method="POST" class="flex flex-col h-full overflow-hidden">
            @csrf
            <div class="modal-body p-6">
                <div class="modal-form-grid">
                    <div class="modal-form-group" style="grid-column: span 2;">
                        <label class="modal-form-label text-stone-500 font-bold uppercase tracking-wider">Unit to Repair (Radio ID) <span class="text-red-500">*</span></label>
                        <select name="walkie_id" class="modal-form-input focus:border-stone-800" required>
                            <option value="" disabled selected>Select unit...</option>
                            @foreach($walkies as $w)
                            <option value="{{ $w->walkie_id }}">{{ $w->radio_id }} / {{ $w->serial_number }} ({{ $w->department }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-form-group">
                        <label class="modal-form-label text-stone-500 font-bold uppercase tracking-wider">Received Date <span class="text-red-500">*</span></label>
                        <input type="date" name="received_date" class="modal-form-input focus:border-stone-800" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="modal-form-group">
                        <label class="modal-form-label text-stone-500 font-bold uppercase tracking-wider">Repair Date</label>
                        <input type="date" name="repair_date" class="modal-form-input focus:border-stone-800">
                    </div>
                    <div class="modal-form-group">
                        <label class="modal-form-label text-stone-500 font-bold uppercase tracking-wider">Status <span class="text-red-500">*</span></label>
                        <select name="status" class="modal-form-input focus:border-stone-800" required>
                            <option value="UNDER REPAIR" selected>UNDER REPAIR</option>
                            <option value="FAULTY">FAULTY</option>
                            <option value="B.E.R">B.E.R</option>
                            <option value="READY TO COLLECT">READY TO COLLECT</option>
                            <option value="ALREADY FIXED">ALREADY FIXED</option>
                            <option value="DONE">DONE / RESOLVED</option>
                        </select>
                    </div>
                    <div class="modal-form-group">
                        <label class="modal-form-label text-stone-500 font-bold uppercase tracking-wider">Done?</label>
                        <select name="done" class="modal-form-input focus:border-stone-800">
                            <option value="0">NO (Pending)</option>
                            <option value="1">YES (Finished)</option>
                        </select>
                    </div>
                    <div class="modal-form-group" style="grid-column: span 2;">
                        <label class="modal-form-label text-stone-500 font-bold uppercase tracking-wider">Issue Decription <span class="text-red-500">*</span></label>
                        <textarea name="issue" class="modal-form-input focus:border-stone-800" style="height:55px; resize:none;" placeholder="e.g. Broken PTT" required></textarea>
                    </div>
                    <div class="modal-form-group" style="grid-column: span 2;">
                        <label class="modal-form-label text-stone-500 font-bold uppercase tracking-wider">Remarks</label>
                        <textarea name="remarks" class="modal-form-input focus:border-stone-800" style="height:55px; resize:none;" placeholder="Notes..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-stone-50/50 border-t border-stone-100 flex justify-end gap-3 p-4">
                <button type="button" onclick="closeRepairModal()" class="text-[11px] font-bold text-stone-400 hover:text-stone-600 uppercase tracking-widest transition">Cancel</button>
                <button type="submit" class="flex items-center gap-2 px-6 py-2.5 bg-[#1e293b] text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-[#0f172a] transition-all border border-blue-400/20">
                    <i class="fas fa-plus text-[8px]"></i>
                    Submit Record
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===================== EDIT MODAL ===================== --}}
<div id="editModal" class="modal-overlay" onclick="closeEditOutside(event)">
    <div class="modal-box p-8">
        <div class="modal-header">
            <div>
                <h2 class="modal-title">Update Repair Record</h2>
                <p class="modal-subtitle" id="editModalSubtitle">Updating record...</p>
            </div>
            <button onclick="closeEditModal()" class="modal-close-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg>
            </button>
        </div>

        <form method="POST" id="editMaintenanceForm" class="flex flex-col h-full overflow-hidden">
            @csrf
            @method('PATCH')
            <div class="modal-body p-6">
                <div class="modal-form-grid">
                    <div class="modal-form-group">
                        <label class="modal-form-label text-stone-500 font-bold uppercase tracking-wider">Received Date <span class="text-red-500">*</span></label>
                        <input type="date" name="received_date" id="edit_received_date" class="modal-form-input focus:border-stone-800" required>
                    </div>
                    <div class="modal-form-group">
                        <label class="modal-form-label text-stone-500 font-bold uppercase tracking-wider">Repair Date</label>
                        <input type="date" name="repair_date" id="edit_repair_date" class="modal-form-input focus:border-stone-800">
                    </div>
                    <div class="modal-form-group">
                        <label class="modal-form-label text-stone-500 font-bold uppercase tracking-wider">Status <span class="text-red-500">*</span></label>
                        <select name="status" id="edit_status" class="modal-form-input focus:border-stone-800" required>
                            <option value="UNDER REPAIR">UNDER REPAIR</option>
                            <option value="FAULTY">FAULTY</option>
                            <option value="B.E.R">B.E.R</option>
                            <option value="READY TO COLLECT">READY TO COLLECT</option>
                            <option value="ALREADY FIXED">ALREADY FIXED</option>
                            <option value="DONE">DONE</option>
                        </select>
                    </div>
                    <div class="modal-form-group">
                        <label class="modal-form-label text-stone-500 font-bold uppercase tracking-wider">Mark Done</label>
                        <select name="done" id="edit_done" class="modal-form-input focus:border-stone-800">
                            <option value="0">NO (Pending)</option>
                            <option value="1">YES</option>
                        </select>
                    </div>
                    <div class="modal-form-group" style="grid-column: span 2;">
                        <label class="modal-form-label text-stone-500 font-bold uppercase tracking-wider">Finish Date</label>
                        <input type="date" name="finish_date" id="edit_finish_date" class="modal-form-input focus:border-stone-800">
                    </div>
                    <div class="modal-form-group" style="grid-column: span 2;">
                        <label class="modal-form-label text-stone-500 font-bold uppercase tracking-wider">Issue Decription <span class="text-red-500">*</span></label>
                        <textarea name="issue" id="edit_issue" class="modal-form-input focus:border-stone-800" style="height:55px; resize:none;" required></textarea>
                    </div>
                    <div class="modal-form-group" style="grid-column: span 2;">
                        <label class="modal-form-label text-stone-500 font-bold uppercase tracking-wider">Remarks</label>
                        <textarea name="remarks" id="edit_remarks" class="modal-form-input focus:border-stone-800" style="height:55px; resize:none;"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-stone-50/50 border-t border-stone-100 flex justify-end gap-3 p-4">
                <button type="button" onclick="closeEditModal()" class="text-[11px] font-bold text-stone-400 hover:text-stone-600 uppercase tracking-widest transition">Cancel</button>
                <button type="submit" class="flex items-center gap-2 px-6 py-2.5 bg-[#1e293b] text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-[#0f172a] transition-all border border-blue-400/20">
                    <i class="fas fa-check text-[8px]"></i>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('maintSearch');
    const statusSelect = document.getElementById('maintStatus');
    const resetButton = document.getElementById('maintReset');
    const rows = Array.from(document.querySelectorAll('#maintTable tbody .maint-row'));
    const pageInfo = document.getElementById('maintPageInfo');
    const perPage = 10;
    let currentPage = 1;
    let filteredRows = rows;

    function renderPageNumbers(totalPages) {
        const pageNumbers = document.getElementById('maintPageNumbers');
        if (!pageNumbers) return;
        pageNumbers.innerHTML = '';
        const pages = [];

        if (totalPages <= 1) {
            return;
        }

        if (totalPages <= 6) {
            for (let page = 1; page <= totalPages; page += 1) pages.push(page);
        } else if (currentPage <= 3) {
            pages.push(1, 2, 3, 4, 'ellipsis', totalPages);
        } else if (currentPage >= totalPages - 2) {
            pages.push(1, 'ellipsis', totalPages - 3, totalPages - 2, totalPages - 1, totalPages);
        } else {
            pages.push(1, 'ellipsis', currentPage - 1, currentPage, currentPage + 1, 'ellipsis', totalPages);
        }

        pages.forEach((page) => {
            if (page === 'ellipsis') {
                const ellipsis = document.createElement('span');
                ellipsis.className = 'repair-page-ellipsis';
                ellipsis.textContent = '...';
                pageNumbers.appendChild(ellipsis);
                return;
            }

            const button = document.createElement('button');
            button.type = 'button';
            button.className = `repair-page-number${page === currentPage ? ' is-active' : ''}`;
            button.textContent = page;
            button.addEventListener('click', function () {
                currentPage = page;
                renderMaintenancePage();
            });
            pageNumbers.appendChild(button);
        });
    }

    function renderMaintenancePage() {
        const totalItems = filteredRows.length;
        rows.forEach((row) => row.classList.add('hidden'));
        filteredRows.forEach((row) => row.classList.remove('hidden'));

        if (pageInfo) {
            pageInfo.textContent = `Total: ${totalItems} items`;
        }
    }

    function applyMaintFilters() {
        const searchValue = (searchInput?.value || '').trim().toUpperCase();
        const statusValue = (statusSelect?.value || '').trim().toUpperCase();

        filteredRows = rows.filter((row) => {
            const matchesSearch = !searchValue || (row.dataset.search || '').includes(searchValue);
            const matchesStatus = !statusValue || row.dataset.status === statusValue;
            return matchesSearch && matchesStatus;
        });
        currentPage = 1;
        renderMaintenancePage();
    }

    if (searchInput) searchInput.addEventListener('input', applyMaintFilters);
    if (statusSelect) statusSelect.addEventListener('change', applyMaintFilters);
    if (resetButton) {
        resetButton.addEventListener('click', function() {
            if (searchInput) searchInput.value = '';
            if (statusSelect) statusSelect.value = '';
            applyMaintFilters();
        });
    }

    applyMaintFilters();

    const maintTableScroll = document.querySelector('.maintenance-page-shell #mainTableContainer .clean-admin-table-scroll');
    if (maintTableScroll) {
        let isDraggingTable = false;
        let tableDragStartX = 0;
        let tableDragStartLeft = 0;
        const stopTableDrag = function(event) {
            if (!isDraggingTable) return;
            isDraggingTable = false;
            maintTableScroll.classList.remove('is-dragging');
            if (event?.pointerId && maintTableScroll.hasPointerCapture?.(event.pointerId)) {
                maintTableScroll.releasePointerCapture(event.pointerId);
            }
        };

        maintTableScroll.addEventListener('pointerdown', function(event) {
            if (event.button !== 0 || event.target.closest('a, button, input, select, textarea, form')) return;
            const maxScroll = this.scrollWidth - this.clientWidth;
            if (maxScroll <= 0) return;

            isDraggingTable = true;
            tableDragStartX = event.clientX;
            tableDragStartLeft = this.scrollLeft;
            this.classList.add('is-dragging');
            this.setPointerCapture?.(event.pointerId);
        });

        maintTableScroll.addEventListener('pointermove', function(event) {
            if (!isDraggingTable) return;
            event.preventDefault();
            this.scrollLeft = tableDragStartLeft - (event.clientX - tableDragStartX);
        });

        maintTableScroll.addEventListener('pointerup', stopTableDrag);
        maintTableScroll.addEventListener('pointercancel', stopTableDrag);
        maintTableScroll.addEventListener('pointerleave', stopTableDrag);

        maintTableScroll.addEventListener('wheel', function(event) {
            const maxScroll = this.scrollWidth - this.clientWidth;
            if (maxScroll <= 0) return;

            const delta = Math.abs(event.deltaX) > Math.abs(event.deltaY) ? event.deltaX : event.deltaY;
            const nextScroll = Math.max(0, Math.min(maxScroll, this.scrollLeft + delta));

            if (nextScroll !== this.scrollLeft) {
                event.preventDefault();
                this.scrollLeft = nextScroll;
            }
        }, { passive: false });
    }
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

function openRepairModal() { document.getElementById('repairModal').classList.add('active'); document.body.style.overflow = 'hidden'; }
function closeRepairModal() { document.getElementById('repairModal').classList.remove('active'); document.body.style.overflow = ''; }
function closeModalOutside(e) { if (e.target === document.getElementById('repairModal')) closeRepairModal(); }

function openEditModal(id, radio, serial, received, repair, done, finish, issue, remarks, status) {
    const form = document.getElementById('editMaintenanceForm');
    form.action = "{{ route('wt.admin.maintenance.update', ['maintenance' => '__ID__']) }}".replace('__ID__', id);
    document.getElementById('editModalSubtitle').innerText = `Unit ${radio} (${serial})`;
    document.getElementById('edit_received_date').value = received || '';
    document.getElementById('edit_repair_date').value = repair || '';
    document.getElementById('edit_status').value = status || 'UNDER REPAIR';
    document.getElementById('edit_done').value = done || '0';
    document.getElementById('edit_finish_date').value = finish || '';
    document.getElementById('edit_issue').value = issue || '';
    document.getElementById('edit_remarks').value = remarks || '';
    document.getElementById('editModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeEditModal() { document.getElementById('editModal').classList.remove('active'); document.body.style.overflow = ''; }
function closeEditOutside(e) { if (e.target === document.getElementById('editModal')) closeEditModal(); }

@if(session('success'))
    setTimeout(() => { const box = document.getElementById('alertBox'); if (box) { box.style.transition='opacity 0.4s'; box.style.opacity='0'; setTimeout(()=>box.remove(), 400); } }, 4000);
@endif
</script>

{{-- ===================== IMPORT EXCEL MODAL ===================== --}}
@if(auth('wt')->user()->wt_role === 'admin_it')
<div id="importModal" class="modal-overlay maintenance-import-modal" onclick="closeImportModalOutside(event)">
    <div class="modal-box maintenance-import-box" style="max-width: 500px;">
        <div class="modal-header">
            <div>
                <h2 class="modal-title">Bulk Import Repair Units</h2>
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
            <input type="hidden" name="import_context" value="maintenance">
            <div class="modal-body p-6">
                <div class="maintenance-import-dropzone rounded-2xl p-8 text-center">
                    <input type="file" name="file" id="import_file" class="hidden" required onchange="updateFileName(this)">
                    <label for="import_file" class="cursor-pointer">
                        <div class="maintenance-import-icon w-12 h-12 rounded-full shadow-sm flex items-center justify-center mx-auto mb-4">
                             <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                                <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
                            </svg>
                        </div>
                        <p class="maintenance-import-file-name text-xs font-bold" id="fileNameDisplay">Click to upload Excel or CSV</p>
                        <p class="maintenance-import-help text-[10px] mt-1 uppercase font-black tracking-widest">Required Headings: radio_id, serial_no, status:FAULTY...</p>
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
    body .content-surface .maintenance-page-shell #mainTableContainer.clean-admin-table-shell {
        overflow-x: auto !important;
        overflow-y: visible !important;
        max-width: 100% !important;
    }

    body .content-surface .maintenance-page-shell #mainTableContainer .clean-admin-table-scroll {
        display: block !important;
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: scroll !important;
        overflow-y: visible !important;
        cursor: grab !important;
        scrollbar-width: thin !important;
        scrollbar-color: #38bdf8 #0f172a !important;
        -webkit-overflow-scrolling: touch !important;
        user-select: none !important;
        padding-bottom: 10px !important;
    }

    body .content-surface .maintenance-page-shell #mainTableContainer .clean-admin-table-scroll.is-dragging {
        cursor: grabbing !important;
    }

    body .content-surface .maintenance-page-shell #mainTableContainer .clean-admin-table-scroll::-webkit-scrollbar {
        display: block !important;
        height: 14px !important;
    }

    body .content-surface .maintenance-page-shell #mainTableContainer .clean-admin-table-scroll::-webkit-scrollbar-track {
        background: #0f172a !important;
        border-radius: 999px !important;
    }

    body .content-surface .maintenance-page-shell #mainTableContainer .clean-admin-table-scroll::-webkit-scrollbar-thumb {
        background: #38bdf8 !important;
        border: 3px solid #0f172a !important;
        border-radius: 999px !important;
    }

    body .content-surface .maintenance-page-shell #maintTable.clean-admin-table {
        width: max-content !important;
        min-width: 1900px !important;
        table-layout: fixed !important;
    }

    body .content-surface .maintenance-page-shell #maintTable :is(th, td) {
        display: table-cell !important;
    }

    body .content-surface .maintenance-page-shell #maintTable.clean-admin-table :is(th, td):nth-child(n+10),
    body .content-surface #maintTable.clean-admin-table :is(th, td):nth-child(n+10) {
        display: table-cell !important;
        visibility: visible !important;
    }

    body .content-surface .maintenance-page-shell #maintTable .maintenance-action-col {
        display: table-cell !important;
        width: 190px !important;
        min-width: 190px !important;
        max-width: 190px !important;
        position: static !important;
        right: auto !important;
        z-index: auto !important;
        background: #111827 !important;
        box-shadow: none !important;
    }

    body .content-surface .maintenance-page-shell #maintTable .maintenance-issue-col,
    body .content-surface .maintenance-page-shell #maintTable .maintenance-remarks-col {
        display: table-cell !important;
        width: 220px !important;
        min-width: 220px !important;
        max-width: 220px !important;
    }

    body .content-surface .maintenance-page-shell #maintTable thead .maintenance-action-col {
        z-index: auto !important;
        background: #1f2937 !important;
        text-align: center !important;
    }

    body .content-surface .maintenance-page-shell #maintTable .maintenance-action-stack {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 4px !important;
        width: 100% !important;
        white-space: nowrap !important;
    }

    body .content-surface .maintenance-page-shell #maintTable .maintenance-action-stack form {
        display: inline-flex !important;
        margin: 0 !important;
    }

    body .content-surface .maintenance-page-shell #maintTable .maintenance-action-stack .wt-btn {
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

    body .content-surface .maintenance-page-shell #maintTable .maintenance-action-stack .wt-btn i {
        font-size: 10px !important;
        line-height: 1 !important;
    }

    body .content-surface .maintenance-page-shell #maintTable .maintenance-action-view {
        width: 46px !important;
        min-width: 46px !important;
        max-width: 46px !important;
        border-color: #0dcaf0 !important;
        background: #0dcaf0 !important;
        color: #052c33 !important;
    }

    body .content-surface .maintenance-page-shell #maintTable .maintenance-action-edit {
        width: 52px !important;
        min-width: 52px !important;
        max-width: 52px !important;
        border-color: #0d6efd !important;
        background: #0d6efd !important;
        color: #ffffff !important;
    }

    body .content-surface .maintenance-page-shell #maintTable .maintenance-action-delete {
        width: 62px !important;
        min-width: 62px !important;
        max-width: 62px !important;
        border-color: #dc3545 !important;
        background: #dc3545 !important;
        color: #ffffff !important;
    }

    html:not(.dark) .maintenance-import-modal .maintenance-import-box {
        border-color: #d8e1ed !important;
        background: #ffffff !important;
        color: #172033 !important;
    }

    html:not(.dark) .maintenance-import-modal .modal-header,
    html:not(.dark) .maintenance-import-modal .modal-body,
    html:not(.dark) .maintenance-import-modal .modal-footer {
        border-color: #e2e8f0 !important;
        background: #ffffff !important;
    }

    html:not(.dark) .maintenance-import-modal .modal-title {
        color: #172033 !important;
    }

    html:not(.dark) .maintenance-import-modal .modal-subtitle,
    html:not(.dark) .maintenance-import-modal .maintenance-import-help {
        color: #64748b !important;
    }

    html:not(.dark) .maintenance-import-modal .maintenance-import-dropzone {
        border: 2px dashed #cbd5e1 !important;
        background: #f8fafc !important;
    }

    html:not(.dark) .maintenance-import-modal .maintenance-import-icon {
        border: 1px solid #e2e8f0 !important;
        background: #ffffff !important;
        color: #8b5e3c !important;
    }

    html:not(.dark) .maintenance-import-modal .maintenance-import-file-name {
        color: #172033 !important;
    }

    html:not(.dark) .maintenance-import-modal .modal-close-btn,
    html:not(.dark) .maintenance-import-modal .btn-cancel {
        border-color: #cbd5e1 !important;
        background: #ffffff !important;
        color: #334155 !important;
    }

    html:not(.dark) .maintenance-import-modal .btn-submit {
        border: 1px solid #172033 !important;
        background: #172033 !important;
        color: #ffffff !important;
    }

    html.dark .maintenance-import-modal .maintenance-import-box,
    .dark .maintenance-import-modal .maintenance-import-box {
        border-color: #334155 !important;
        background: #1e293b !important;
        color: #f8fafc !important;
    }

    html.dark .maintenance-import-modal .modal-header,
    .dark .maintenance-import-modal .modal-header,
    html.dark .maintenance-import-modal .modal-body,
    .dark .maintenance-import-modal .modal-body,
    html.dark .maintenance-import-modal .modal-footer,
    .dark .maintenance-import-modal .modal-footer {
        border-color: #334155 !important;
        background: #1e293b !important;
    }

    html.dark .maintenance-import-modal .modal-title,
    .dark .maintenance-import-modal .modal-title {
        color: #f8fafc !important;
    }

    html.dark .maintenance-import-modal .modal-subtitle,
    .dark .maintenance-import-modal .modal-subtitle,
    html.dark .maintenance-import-modal .maintenance-import-help,
    .dark .maintenance-import-modal .maintenance-import-help {
        color: #94a3b8 !important;
    }

    html.dark .maintenance-import-modal .maintenance-import-dropzone,
    .dark .maintenance-import-modal .maintenance-import-dropzone {
        border: 2px dashed #475569 !important;
        background: #0f172a !important;
    }

    html.dark .maintenance-import-modal .maintenance-import-icon,
    .dark .maintenance-import-modal .maintenance-import-icon {
        border: 1px solid #334155 !important;
        background: #111827 !important;
        color: #d1ae7b !important;
    }

    html.dark .maintenance-import-modal .maintenance-import-file-name,
    .dark .maintenance-import-modal .maintenance-import-file-name {
        color: #e2e8f0 !important;
    }

    html.dark .maintenance-import-modal .modal-close-btn,
    .dark .maintenance-import-modal .modal-close-btn,
    html.dark .maintenance-import-modal .btn-cancel,
    .dark .maintenance-import-modal .btn-cancel {
        border-color: #475569 !important;
        background: #f8fafc !important;
        color: #64748b !important;
    }

    html.dark .maintenance-import-modal .btn-submit,
    .dark .maintenance-import-modal .btn-submit {
        border: 1px solid #0f172a !important;
        background: #0f172a !important;
        color: #ffffff !important;
    }

</style>

<style id="under-repair-table-bright-final">
.maintenance-page-shell #mainTableContainer.clean-admin-table-shell,
.maintenance-page-shell #mainTableContainer .clean-admin-table-scroll,
.maintenance-page-shell #maintTable.clean-admin-table,
.maintenance-page-shell #maintTable.clean-admin-table tbody,
.maintenance-page-shell #maintTable.clean-admin-table tbody tr {
    background: #ffffff !important;
    color: #0f172a !important;
}

.maintenance-page-shell #mainTableContainer.clean-admin-table-shell {
    border-color: #cbd5e1 !important;
}

.maintenance-page-shell #maintTable.clean-admin-table {
    border-collapse: collapse !important;
    border: 1px solid #cbd5e1 !important;
}

.maintenance-page-shell #maintTable.clean-admin-table thead th {
    background: #eef3f8 !important;
    border-color: #cbd5e1 !important;
    color: #334155 !important;
}

.maintenance-page-shell #maintTable.clean-admin-table tbody td,
.maintenance-page-shell #maintTable.clean-admin-table .maintenance-action-col {
    background: #ffffff !important;
    border-color: #e2e8f0 !important;
    color: #1f2937 !important;
}

.maintenance-page-shell #maintTable.clean-admin-table tbody tr:hover td {
    background: #f8fafc !important;
}

.maintenance-page-shell #maintTable.clean-admin-table th,
.maintenance-page-shell #maintTable.clean-admin-table td {
    border-width: 1px !important;
    border-style: solid !important;
}

.maintenance-page-shell .repair-table-footer {
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
}

.maintenance-page-shell .repair-table-info {
    color: #0f172a !important;
}

html.dark .maintenance-page-shell #mainTableContainer.clean-admin-table-shell,
html.dark .maintenance-page-shell #mainTableContainer .clean-admin-table-scroll,
html.dark .maintenance-page-shell #maintTable.clean-admin-table,
html.dark .maintenance-page-shell #maintTable.clean-admin-table tbody,
html.dark .maintenance-page-shell #maintTable.clean-admin-table tbody tr {
    background: #111827 !important;
    color: #dbe4f0 !important;
}

html.dark .maintenance-page-shell #mainTableContainer.clean-admin-table-shell {
    border-color: #263244 !important;
}

html.dark .maintenance-page-shell #maintTable.clean-admin-table {
    border-color: #263244 !important;
}

html.dark .maintenance-page-shell #maintTable.clean-admin-table thead th {
    background: #1f2937 !important;
    border-color: #2f3b4f !important;
    color: #cbd5e1 !important;
}

html.dark .maintenance-page-shell #maintTable.clean-admin-table tbody td,
html.dark .maintenance-page-shell #maintTable.clean-admin-table .maintenance-action-col {
    background: #111827 !important;
    border-color: #263244 !important;
    color: #dbe4f0 !important;
}

html.dark .maintenance-page-shell #maintTable.clean-admin-table tbody tr:hover td {
    background: #172033 !important;
}

html.dark .maintenance-page-shell .repair-table-footer {
    background: #111827 !important;
    border-color: #263244 !important;
}

html.dark .maintenance-page-shell .repair-table-info {
    color: #dbeafe !important;
}
</style>

<style id="under-repair-compact-standard-final">
.maintenance-page-shell #mainTableContainer.clean-admin-table-shell {
    border: 1px solid #cbd5e1 !important;
    border-radius: 8px !important;
    background: #ffffff !important;
    overflow: hidden !important;
    box-shadow: none !important;
}

.maintenance-page-shell #mainTableContainer .clean-admin-table-scroll {
    background: #ffffff !important;
}

.maintenance-page-shell #maintTable.clean-admin-table {
    width: 100% !important;
    min-width: 860px !important;
    border: 0 !important;
    border-collapse: collapse !important;
    table-layout: fixed !important;
}

.maintenance-page-shell #maintTable.clean-admin-table thead th {
    height: 46px !important;
    padding: 0 16px !important;
    border: 1px solid #d8e1ed !important;
    background: #eef3f8 !important;
    color: #526781 !important;
    font-size: 13px !important;
    font-weight: 900 !important;
    letter-spacing: .06em !important;
    text-transform: uppercase !important;
    white-space: nowrap !important;
}

.maintenance-page-shell #maintTable.clean-admin-table tbody td {
    height: 38px !important;
    padding: 8px 16px !important;
    border: 1px solid #e2e8f0 !important;
    background: #ffffff !important;
    color: #1f2937 !important;
    font-size: 12px !important;
    font-weight: 650 !important;
    line-height: 1.25 !important;
    vertical-align: middle !important;
}

.maintenance-page-shell #maintTable.clean-admin-table :is(th, td):nth-child(1) { width: 12% !important; }
.maintenance-page-shell #maintTable.clean-admin-table :is(th, td):nth-child(2) { width: 14% !important; }
.maintenance-page-shell #maintTable.clean-admin-table :is(th, td):nth-child(3) { width: 16% !important; }
.maintenance-page-shell #maintTable.clean-admin-table :is(th, td):nth-child(4) { width: 14% !important; }
.maintenance-page-shell #maintTable.clean-admin-table :is(th, td):nth-child(5) { width: 24% !important; }
.maintenance-page-shell #maintTable.clean-admin-table :is(th, td):nth-child(6) { width: 20% !important; }

.maintenance-page-shell #maintTable.clean-admin-table tbody tr:hover td {
    background: #f8fafc !important;
}

.maintenance-page-shell #maintTable .maintenance-action-col {
    width: auto !important;
    min-width: 0 !important;
    max-width: none !important;
    text-align: center !important;
}

.maintenance-page-shell #maintTable .maintenance-action-stack {
    display: flex !important;
    flex-wrap: nowrap !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    width: 100% !important;
}

.maintenance-page-shell #maintTable .maintenance-action-stack form {
    display: inline-flex !important;
    margin: 0 !important;
}

.maintenance-page-shell #maintTable .maintenance-action-stack .wt-btn {
    min-width: 64px !important;
    width: auto !important;
    max-width: none !important;
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
}

.maintenance-page-shell #maintTable .maintenance-action-view {
    border-color: #0284c7 !important;
    background: #0284c7 !important;
    color: #ffffff !important;
}

.maintenance-page-shell #maintTable .maintenance-action-edit {
    border-color: #2563eb !important;
    background: #2563eb !important;
    color: #ffffff !important;
}

.maintenance-page-shell #maintTable .maintenance-action-delete {
    border-color: #dc2626 !important;
    background: #dc2626 !important;
    color: #ffffff !important;
}

.maintenance-page-shell .repair-table-footer {
    min-height: 54px !important;
    padding: 10px 16px !important;
    border-top: 1px solid #d8e1ed !important;
    background: #ffffff !important;
}

.maintenance-page-shell .repair-table-info {
    color: #020617 !important;
    font-size: 13px !important;
    font-weight: 900 !important;
}

html.dark .maintenance-page-shell #mainTableContainer.clean-admin-table-shell,
html.dark .maintenance-page-shell #mainTableContainer .clean-admin-table-scroll,
html.dark .maintenance-page-shell #maintTable.clean-admin-table,
html.dark .maintenance-page-shell #maintTable.clean-admin-table tbody tr,
html.dark .maintenance-page-shell .repair-table-footer {
    border-color: #334155 !important;
    background: #111827 !important;
}

html.dark .maintenance-page-shell #maintTable.clean-admin-table thead th {
    border-color: #334155 !important;
    background: #1f2937 !important;
    color: #cbd5e1 !important;
}

html.dark .maintenance-page-shell #maintTable.clean-admin-table tbody td {
    border-color: #334155 !important;
    background: #111827 !important;
    color: #dbe4f0 !important;
}

html.dark .maintenance-page-shell #maintTable.clean-admin-table tbody tr:hover td {
    background: #172033 !important;
}

html.dark .maintenance-page-shell .repair-table-info {
    color: #dbeafe !important;
}
</style>

<script>
// ESC key to close
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeRepairModal();
        closeEditModal();
        closeImportModal();
    }
});
</script>
@include('wt.admin.partials.inventory-tools-unified-ui')
<style id="maintenance-compact-final-override">
    body .content-surface .maintenance-page-shell .clean-admin-table-shell,
    body .content-surface .maintenance-page-shell .clean-admin-table-scroll {
        overflow: hidden !important;
    }
    body .content-surface .maintenance-page-shell #maintTable {
        width: 100% !important;
        min-width: 0 !important;
        table-layout: auto !important;
    }
    body .content-surface .maintenance-page-shell #maintTable :is(th, td):nth-child(1),
    body .content-surface .maintenance-page-shell #maintTable :is(th, td):nth-child(2),
    body .content-surface .maintenance-page-shell #maintTable :is(th, td):nth-child(3),
    body .content-surface .maintenance-page-shell #maintTable :is(th, td):nth-child(4),
    body .content-surface .maintenance-page-shell #maintTable :is(th, td):nth-child(5),
    body .content-surface .maintenance-page-shell #maintTable :is(th, td):nth-child(7) {
        white-space: nowrap !important;
    }
    body .content-surface .maintenance-page-shell #maintTable :is(th, td):nth-child(1) { width: 10% !important; }
    body .content-surface .maintenance-page-shell #maintTable :is(th, td):nth-child(2) { width: 12% !important; }
    body .content-surface .maintenance-page-shell #maintTable :is(th, td):nth-child(3) { width: 18% !important; }
    body .content-surface .maintenance-page-shell #maintTable :is(th, td):nth-child(4) { width: 12% !important; }
    body .content-surface .maintenance-page-shell #maintTable :is(th, td):nth-child(5) {
        width: 12% !important;
        min-width: 100px !important;
    }
    body .content-surface .maintenance-page-shell #maintTable :is(th, td):nth-child(6) {
        width: 24% !important;
        max-width: 220px !important;
        min-width: 0 !important;
        white-space: normal !important;
        overflow-wrap: anywhere !important;
    }
    body .content-surface .maintenance-page-shell #maintTable :is(th, td):nth-child(7) {
        width: 12% !important;
        min-width: 70px !important;
    }
    body .content-surface .maintenance-page-shell #maintTable thead th {
        white-space: nowrap !important;
        overflow: visible !important;
        text-overflow: clip !important;
    }
    body .content-surface .maintenance-page-shell #maintTable thead th {
        height: 28px !important;
        padding: 4px 6px !important;
        text-align: center !important;
        font-size: 9px !important;
        line-height: 1.05 !important;
    }
    body .content-surface .maintenance-page-shell #maintTable tbody td {
        height: 26px !important;
        padding: 3px 6px !important;
        text-align: left !important;
        font-size: 10px !important;
        line-height: 1.1 !important;
        white-space: nowrap !important;
    }
    body .content-surface .maintenance-page-shell #maintTable tbody td.maintenance-action-col,
    body .content-surface .maintenance-page-shell #maintTable tbody td:has(.clean-admin-pill) {
        text-align: center !important;
    }
    body .content-surface .maintenance-page-shell #maintTable .maintenance-action-stack {
        gap: 4px !important;
    }
    body .content-surface .maintenance-page-shell #maintTable .maintenance-action-stack .wt-btn {
        width: 54px !important;
        min-width: 54px !important;
        max-width: 54px !important;
        height: 22px !important;
        min-height: 22px !important;
        padding: 0 5px !important;
        border-radius: 5px !important;
        font-size: 8px !important;
        gap: 3px !important;
    }
    body .content-surface .maintenance-page-shell #maintTable .maintenance-action-view { border-color: #0284c7 !important; background: #0284c7 !important; color: #fff !important; }
    body .content-surface .maintenance-page-shell #maintTable .maintenance-action-edit { border-color: #2563eb !important; background: #2563eb !important; color: #fff !important; }
    body .content-surface .maintenance-page-shell #maintTable .maintenance-action-delete { border-color: #dc2626 !important; background: #dc2626 !important; color: #fff !important; }
</style>
@endsection
