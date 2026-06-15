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
    .clean-admin-input {
        padding: 0 10px;
    }
    .clean-admin-select {
        padding: 0 28px 0 10px;
    }
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
    .clean-admin-table-scroll {
        overflow-x: auto;
    }
    .clean-admin-table {
        width: 100%;
        min-width: 1380px;
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
    .clean-admin-table tbody tr:hover td {
        background: #172033;
    }
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
        justify-content: center;
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
        min-width: 52px;
        height: 44px;
        border: 1px solid #2f4d74;
        border-radius: 8px;
        background: #0f172a;
        color: #bfdbfe;
        font-size: 13px;
        font-weight: 900;
        text-transform: uppercase;
        transition: border-color 0.15s ease, background 0.15s ease, color 0.15s ease, opacity 0.15s ease;
    }
    .repair-page-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-width: 128px;
        padding: 0 14px;
        color: #cbd5e1;
        font-size: 16px;
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
        height: 44px;
        color: #94a3b8;
        font-size: 13px;
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
        .clean-admin-filter-grid {
            grid-template-columns: 1fr;
        }
        .repair-table-footer {
            align-items: stretch;
            flex-direction: column;
        }
        .repair-table-pagination {
            justify-content: flex-start;
            overflow-x: auto;
        }
    }
</style>
@endpush

@section('content')
@include('admin.partials.inventory-management-ui')
<style>
    body .content-surface .maintenance-page-shell #maintenanceTable :is(th, td),
    body .content-surface #maintenanceTable.clean-admin-table :is(th, td) {
        display: table-cell !important;
    }

    body .content-surface .maintenance-page-shell #maintenanceTable {
        min-width: 1900px !important;
        width: max-content !important;
        table-layout: fixed !important;
    }

    body .content-surface .maintenance-page-shell #maintenanceTable.clean-admin-table :is(th, td):nth-child(n+10),
    body .content-surface #maintenanceTable.clean-admin-table :is(th, td):nth-child(n+10) {
        display: table-cell !important;
        visibility: visible !important;
    }

    body .content-surface .maintenance-page-shell #maintenanceTable .maintenance-action-col {
        display: table-cell !important;
        width: 230px !important;
        min-width: 230px !important;
        max-width: 230px !important;
        position: static !important;
        right: auto !important;
        z-index: auto !important;
        text-align: center !important;
        background: #111827 !important;
        box-shadow: none !important;
    }

    body .content-surface .maintenance-page-shell #maintenanceTable thead .maintenance-action-col {
        z-index: auto !important;
        background: #1f2937 !important;
    }

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

    body .content-surface .maintenance-page-shell .clean-admin-filter {
        min-height: auto !important;
        margin: 0 !important;
        padding: 10px 12px !important;
        border-radius: 8px !important;
    }
    body .content-surface .maintenance-page-shell .clean-admin-filter-grid {
        display: grid !important;
        grid-template-columns: minmax(260px, 1fr) 180px 74px !important;
        gap: 10px !important;
        align-items: end !important;
    }
    body .content-surface .maintenance-page-shell .clean-admin-label {
        margin: 0 0 5px !important;
        font-size: 10px !important;
        line-height: 1 !important;
        letter-spacing: 0.08em !important;
    }
    body .content-surface .maintenance-page-shell .clean-admin-input,
    body .content-surface .maintenance-page-shell .clean-admin-select,
    body .content-surface .maintenance-page-shell .clean-admin-reset {
        height: 34px !important;
        min-height: 34px !important;
        border-radius: 7px !important;
        font-size: 13px !important;
        line-height: 1 !important;
        box-shadow: none !important;
    }
    body .content-surface .maintenance-page-shell .clean-admin-input {
        padding: 0 10px !important;
    }
    body .content-surface .maintenance-page-shell .clean-admin-select {
        padding: 0 28px 0 10px !important;
    }
    body .content-surface .maintenance-page-shell .clean-admin-reset {
        width: 74px !important;
        padding: 0 12px !important;
        font-weight: 900 !important;
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
<div class="maintenance-page-shell">
<div class="page-header-block flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h3 class="page-title-standard">Under Repair / Faulty Units</h3>
        <p class="page-subtitle-standard">Records marked as REPAIRING, FAULTY, or B.E.R.</p>
    </div>
    @if(auth('wt')->user()->role === 'admin_it')
    <div class="flex flex-shrink-0 items-center gap-2">
        <button onclick="openImportModal()" class="wt-btn wt-btn-soft">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="margin-right:5px;">
                <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
            </svg>
            Import Excel
        </button>
    </div>
    @endif
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

<div class="clean-admin-filter">
    <div class="clean-admin-filter-grid">
        <div>
            <label class="clean-admin-label" for="maintenanceSearch">Search</label>
            <input type="text" id="maintenanceSearch" class="clean-admin-input" placeholder="Keywords">
        </div>
        <div>
            <label class="clean-admin-label" for="maintenanceStatus">Status</label>
            <select id="maintenanceStatus" class="clean-admin-select">
                <option value="">All Status</option>
                <option value="WAITING FOR ADMIN">Waiting For Admin</option>
                <option value="PENDING ADMIN IT">Pending Admin IT</option>
                <option value="UNDER REPAIR">Under Repair</option>
                <option value="REPAIRING">Repairing</option>
                <option value="FAULTY">Faulty</option>
                <option value="B.E.R">B.E.R</option>
                <option value="READY TO COLLECT">Ready To Collect</option>
                <option value="ALREADY FIXED">Already Fixed</option>
                <option value="DONE">Done</option>
            </select>
        </div>
        <button type="button" id="maintenanceReset" class="clean-admin-reset">Reset</button>
    </div>
</div>
<div class="clean-admin-table-shell" id="mainTableContainer">
    <div class="clean-admin-table-scroll">
    <table id="maintenanceTable" class="clean-admin-table text-left">
        <thead>
            <tr>
                <th class="px-3 py-3">Radio ID</th>
                <th class="px-3 py-3">Serial No</th>
                <th class="px-3 py-3">Model</th>
                <th class="px-3 py-3">Ownership Type</th>
                <th class="px-3 py-3">Department</th>
                <th class="px-3 py-3">Date Received</th>
                <th class="px-3 py-3">Reporter</th>
                <th class="px-3 py-3">Issue</th>
                <th class="px-3 py-3">Status</th>
                <th class="px-3 py-3">Done</th>
                <th class="px-3 py-3">Finish Date</th>
                <th class="px-3 py-3">Remarks</th>
                <th class="px-3 py-3">Repair Progress</th>
                <th class="px-3 py-3 maintenance-action-col">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
            @php
                $repairProgress = $record->done ? 100 : match($record->status) {
                    'WAITING FOR ADMIN' => 24,
                    'PENDING ADMIN IT' => 48,
                    'UNDER REPAIR', 'REPAIRING' => 68,
                    'FAULTY' => 36,
                    'B.E.R' => 82,
                    'READY TO COLLECT' => 92,
                    'ALREADY FIXED' => 100,
                    default => 32,
                };
            @endphp
            <tr class="maintenance-row"
                data-status="{{ $record->done ? 'DONE' : strtoupper((string) $record->status) }}"
                data-search="{{ strtoupper(trim(($record->radio_id ?? '') . ' ' . ($record->serial_number ?? '') . ' ' . ($record->model ?? '') . ' ' . ($record->ownership_type ?? '') . ' ' . ($record->walkieTalkie->ownership_type ?? '') . ' ' . ($record->department_name ?? '') . ' ' . ($record->received_date ?? '') . ' ' . ($record->reporter_name ?? '') . ' ' . ($record->reporter_staff_id ?? '') . ' ' . ($record->issue ?? '') . ' ' . ($record->issue_description ?? '') . ' ' . ($record->finish_date ?? '') . ' ' . ($record->remarks ?? '') . ' ' . ($record->status ?? ''))) }}">
                <td>{{ $record->radio_id ?? '-' }}</td>
                <td>{{ $record->serial_number ?? '-' }}</td>
                <td>{{ $record->model ?? '-' }}</td>
                <td>
                    @php
                        $ownershipType = strtoupper(trim((string) ($record->walkieTalkie->ownership_type ?? $record->ownership_type ?? '')));
                    @endphp
                    {{ in_array($ownershipType, ['INDIVIDUAL', 'SHARED', 'SPARE'], true) ? $ownershipType : '-' }}
                </td>
                <td>{{ $record->department_name ?? '-' }}</td>
                <td>{{ $record->received_date ?? '-' }}</td>
                <td>
                    @if($record->request_source === 'user')
                    <div class="flex flex-col gap-0">
                        <span>{{ $record->reporter_name }}</span>
                        <span class="text-[9px] text-slate-400">{{ $record->reporter_staff_id }}</span>
                    </div>
                    @else
                    <span class="text-[9px] text-slate-400 italic">SYSTEM</span>
                    @endif
                </td>
                <td class="max-w-xs">
                    <div class="line-clamp-1">{{ $record->issue ?? $record->issue_description ?? '-' }}</div>
                </td>
                <td class="text-center">
                    @if($record->status === 'WAITING FOR ADMIN')
                    <span class="clean-admin-pill">WAITING</span>
                    @elseif($record->status === 'PENDING ADMIN IT')
                    <span class="clean-admin-pill">PENDING IT</span>
                    @elseif($record->done || $record->status === 'ALREADY FIXED')
                    <span class="clean-admin-pill">DONE</span>
                    @else
                    <span class="clean-admin-pill">{{ $record->status ?: 'WARNING' }}</span>
                    @endif
                </td>
                <td class="text-center">
                    <span class="clean-admin-pill">{{ $record->done ? 'YES' : 'NO' }}</span>
                </td>
                <td>{{ $record->finish_date ?? '-' }}</td>
                <td class="max-w-xs">
                    <div class="line-clamp-1 italic">{{ $record->remarks ?? '-' }}</div>
                </td>
                <td>
                    <div class="h-1 w-24 rounded-[4px] bg-slate-700/80 overflow-hidden border border-white/5">
                        <div class="h-full rounded-[4px] bg-[#38bdf8]" style="width: {{ $repairProgress }}%;"></div>
                    </div>
                    <div class="mt-1 text-[9px] text-slate-400">{{ $repairProgress }}%</div>
                </td>
                <td class="maintenance-action-col">
                    <div class="clean-admin-actions">
                        <button type="button" class="wt-btn wt-btn-sm" onclick="openGlobalMaintenanceTimeline('{{ $record->maintenance_id }}')">
                            View
                        </button>
                        @if(auth('wt')->user()->role === 'admin_it')
                        <button type="button" 
                            class="wt-btn wt-btn-sm"
                            onclick="openEditModal(
                                '{{ $record->maintenance_id }}',
                                '{{ $record->radio_id }}',
                                '{{ $record->serial_number }}',
                                '{{ $record->model }}',
                                '{{ $record->current_ownership }}',
                                '{{ $record->department_name }}',
                                '{{ $record->received_date }}',
                                '{{ $record->repair_date }}',
                                '{{ $record->done ? 1 : 0 }}',
                                '{{ $record->finish_date }}',
                                '{{ addslashes($record->issue ?: $record->issue_description) }}',
                                '{{ addslashes($record->remarks ?? '') }}',
                                '{{ $record->status }}'
                            )">
                            Edit
                        </button>
                        <form action="{{ route('wt.admin.maintenance.destroy', $record->maintenance_id) }}" method="POST" style="display:inline;" data-modern-confirm="Delete maintenance record for {{ $record->radio_id ?? '-' }}?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="wt-btn wt-btn-danger wt-btn-sm">Delete</button>
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
        <div id="maintenancePageInfo" class="repair-table-info">Total: 0 items</div>
    </div>
</div>
</div>

{{-- ===================== MAINTENANCE EDIT MODAL ===================== --}}
<div id="editModal" class="modal-overlay" onclick="closeEditModalOutside(event)">
    <div class="modal-box" id="editModalBox">
        <div class="modal-header">
            <div>
                <h2 class="modal-title">Edit Maintenance Record</h2>
                <p class="modal-subtitle" id="editModalSubtitle">Update repair status and technician notes.</p>
            </div>
            <button onclick="closeEditModal()" class="modal-close-btn" title="Close">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                </svg>
            </button>
        </div>

        <form method="POST" id="editMaintenanceForm">
            @csrf
            @method('PATCH')
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Received Date <span class="required">*</span></label>
                    <input type="date" name="received_date" id="edit_received_date" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Repair Date</label>
                    <input type="date" name="repair_date" id="edit_repair_date" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Status <span class="required">*</span></label>
                    <select name="status" id="edit_status" class="form-input" required>
                        @foreach(['UNDER REPAIR','FAULTY','B.E.R','READY TO COLLECT','ALREADY FIXED','DONE'] as $status)
                        <option value="{{ $status }}">{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Mark as Done</label>
                    <select name="done" id="edit_done" class="form-input">
                        <option value="0">PENDING</option>
                        <option value="1">DONE</option>
                    </select>
                </div>
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label">Finish Date</label>
                    <input type="date" name="finish_date" id="edit_finish_date" class="form-input">
                </div>
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label">Issue Decription <span class="required">*</span></label>
                    <textarea name="issue" id="edit_issue" class="form-input" rows="3" required></textarea>
                </div>
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label">Maintenance Remarks</label>
                    <textarea name="remarks" id="edit_remarks" class="form-input" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeEditModal()" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-submit">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('maintenanceSearch');
    const statusSelect = document.getElementById('maintenanceStatus');
    const resetButton = document.getElementById('maintenanceReset');
    const rows = Array.from(document.querySelectorAll('#maintenanceTable tbody .maintenance-row'));
    const pageInfo = document.getElementById('maintenancePageInfo');
    const perPage = 10;
    let currentPage = 1;
    let filteredRows = rows;

    function renderPageNumbers(totalPages) {
        const pageNumbers = document.getElementById('maintenancePageNumbers');
        if (!pageNumbers) return;
        pageNumbers.innerHTML = '';
        const pages = [];

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

    function applyMaintenanceFilters() {
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

    if (searchInput) {
        searchInput.addEventListener('input', applyMaintenanceFilters);
    }

    if (statusSelect) {
        statusSelect.addEventListener('change', applyMaintenanceFilters);
    }
    if (resetButton) {
        resetButton.addEventListener('click', function() {
            if (searchInput) searchInput.value = '';
            if (statusSelect) statusSelect.value = '';
            applyMaintenanceFilters();
        });
    }

    applyMaintenanceFilters();

    const maintenanceTableScroll = document.querySelector('.maintenance-page-shell #mainTableContainer .clean-admin-table-scroll');
    if (maintenanceTableScroll) {
        let isDraggingTable = false;
        let tableDragStartX = 0;
        let tableDragStartLeft = 0;
        const stopTableDrag = function(event) {
            if (!isDraggingTable) return;
            isDraggingTable = false;
            maintenanceTableScroll.classList.remove('is-dragging');
            if (event?.pointerId && maintenanceTableScroll.hasPointerCapture?.(event.pointerId)) {
                maintenanceTableScroll.releasePointerCapture(event.pointerId);
            }
        };

        maintenanceTableScroll.addEventListener('pointerdown', function(event) {
            if (event.button !== 0 || event.target.closest('a, button, input, select, textarea, form')) return;
            const maxScroll = this.scrollWidth - this.clientWidth;
            if (maxScroll <= 0) return;

            isDraggingTable = true;
            tableDragStartX = event.clientX;
            tableDragStartLeft = this.scrollLeft;
            this.classList.add('is-dragging');
            this.setPointerCapture?.(event.pointerId);
        });

        maintenanceTableScroll.addEventListener('pointermove', function(event) {
            if (!isDraggingTable) return;
            event.preventDefault();
            this.scrollLeft = tableDragStartLeft - (event.clientX - tableDragStartX);
        });

        maintenanceTableScroll.addEventListener('pointerup', stopTableDrag);
        maintenanceTableScroll.addEventListener('pointercancel', stopTableDrag);
        maintenanceTableScroll.addEventListener('pointerleave', stopTableDrag);

        maintenanceTableScroll.addEventListener('wheel', function(event) {
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

function openEditModal(id, radio, serial, model, ownership, department, receivedDate, repairDate, done, finishDate, issue, remarks, status) {
    const form = document.getElementById('editMaintenanceForm');
    form.action = "{{ route('wt.admin.maintenance.update', ['maintenance' => '__ID__']) }}".replace('__ID__', id);
    
    document.getElementById('editModalSubtitle').innerText = `Repair Record for Unit ${radio} (${serial})`;
    document.getElementById('edit_received_date').value = receivedDate || '';
    document.getElementById('edit_repair_date').value = repairDate || '';
    document.getElementById('edit_status').value = status || 'FAULTY';
    document.getElementById('edit_done').value = done || '0';
    document.getElementById('edit_finish_date').value = finishDate || '';
    document.getElementById('edit_issue').value = issue || '';
    document.getElementById('edit_remarks').value = remarks || '';
    
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

// ESC key to close
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeEditModal();
    }
});

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
</script>

<style>
.line-clamp-2 { 
    display: -webkit-box; 
    -webkit-line-clamp: 2; 
    -webkit-box-orient: vertical; 
    overflow: hidden; 
}

.maintenance-table-controls {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 12px 16px;
    flex-wrap: wrap;
    margin-bottom: 12px;
    padding: 0 6px;
}
.maintenance-table-controls .dataTables_length,
.maintenance-table-controls .dataTables_filter {
    margin: 0 !important;
    float: none !important;
}
.maintenance-table-controls .dataTables_length label,
.maintenance-table-controls .dataTables_filter label {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 5px;
    margin: 0;
    color: #64748b !important;
    font-size: 11px;
    font-weight: 800 !important;
    line-height: 1.2;
}
.maintenance-table-controls .dt-inline-label {
    display: inline-block;
    color: #64748b;
    font-size: 11px;
    font-weight: 800;
    line-height: 1.2;
}
.maintenance-table-controls .dataTables_length select,
.maintenance-table-controls .dataTables_filter input {
    margin: 0 !important;
    border: 1px solid rgba(203, 213, 225, 0.85) !important;
    border-radius: 12px !important;
    background: #ffffff !important;
    color: #1f2937 !important;
    box-sizing: border-box;
}
.maintenance-table-controls .dataTables_length select {
    width: 112px !important;
    min-height: 34px;
    padding: 6px 28px 6px 10px !important;
}
.maintenance-table-controls .dataTables_filter input {
    width: 220px !important;
    min-height: 34px;
    padding: 6px 10px !important;
}

/* ===== Alerts ===== */
.alert-success, .alert-error {
    display: flex;
    align-items: flex-start;
    padding: 14px 18px;
    border-radius: 14px;
    font-size: 13px;
    font-weight: 600;
    animation: slideDown 0.3s ease;
}
.alert-success { background: #ecfdf5; color: #166534; border: 1px solid #bbf7d0; }
.alert-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

/* ===== Modal Overlay ===== */
.modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(30,20,10,0.45);
    backdrop-filter: blur(4px);
    z-index: 1000;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.modal-overlay.active { display: flex; }

.modal-box {
    background: #fff;
    border-radius: 24px;
    width: 100%;
    max-width: 680px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 24px 60px rgba(0,0,0,0.18);
    animation: modalPop 0.3s cubic-bezier(0.34,1.56,0.64,1);
    padding: 32px;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 24px;
}
.modal-title { font-size: 20px; font-weight: 800; color: #3D2B1F; letter-spacing: -0.02em; }
.modal-subtitle { font-size: 12px; color: #a8a29e; margin-top: 3px; font-weight: 500; }
.modal-close-btn {
    background: #f5f5f4; border: none; border-radius: 10px; padding: 8px; cursor: pointer; color: #78716c;
    display: flex; align-items: center; transition: all 0.15s; flex-shrink: 0;
}
.modal-close-btn:hover { background: #e7e5e4; color: #3D2B1F; }

.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-label { font-size: 11px; font-weight: 700; color: #78716c; text-transform: uppercase; letter-spacing: 0.06em; }
.required { color: #dc2626; }
.form-input {
    border: 1.5px solid #e7e5e4; border-radius: 12px; padding: 10px 14px; font-size: 13px; color: #1c1917;
    background: #fafaf9; outline: none; width: 100%; box-sizing: border-box; transition: all 0.15s; font-family: inherit;
}
.form-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.12); background: #fff; }
select.form-input {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 16 16'%3E%3Cpath fill='%23a8a29e' d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 14px center; padding-right: 36px;
}

.modal-footer { display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 18px; border-top: 1px solid #f5f5f4; }
.btn-cancel { padding: 10px 22px; border-radius: 12px; border: 1.5px solid #e7e5e4; background: white; color: #78716c; font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.15s; }
.btn-submit {
    display: inline-flex; align-items: center; padding: 11px 24px; border-radius: 14px; background: rgba(15, 23, 42, 0.96);
    color: #e2e8f0; font-size: 13px; font-weight: 800; cursor: pointer; transition: all 0.2s; letter-spacing: 0.08em; text-transform: uppercase;
}
.btn-submit:hover { background: #162033; transform: translateY(-1px); }

@keyframes modalPop { from { opacity: 0; transform: scale(0.92) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }
@keyframes slideDown { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }

@media (max-width: 600px) { .form-grid { grid-template-columns: 1fr; } .modal-box { padding: 22px 16px; } }
@media (max-width: 768px) {
    .maintenance-table-controls {
        display: grid;
        grid-template-columns: 1fr;
        gap: 8px;
        padding: 0 4px;
        margin-bottom: 10px;
    }
    .maintenance-table-controls .dataTables_length,
    .maintenance-table-controls .dataTables_filter {
        width: 100%;
    }
    .maintenance-table-controls .dataTables_length label,
    .maintenance-table-controls .dataTables_filter label {
        gap: 4px;
        font-size: 10px;
    }
    .maintenance-table-controls .dt-inline-label {
        font-size: 10px;
    }
    .maintenance-table-controls .dataTables_length select {
        width: 100% !important;
        max-width: 112px;
        min-height: 32px;
        border-radius: 10px !important;
    }
    .maintenance-table-controls .dataTables_filter input {
        width: 100% !important;
        min-height: 32px;
        border-radius: 10px !important;
    }
}

</style>

{{-- ===================== IMPORT EXCEL MODAL ===================== --}}
@if(auth('wt')->user()->role === 'admin_it')
<div id="importModal" class="modal-overlay" onclick="closeImportModalOutside(event)">
    <div class="modal-box" style="max-width: 500px;">
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
                        <p class="text-[10px] text-stone-400 mt-1 uppercase font-black tracking-widest">Required Headings: radio_id, serial_no, status:FAULTY...</p>
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
// ESC key to close
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeEditModal();
        closeImportModal();
    }
});
</script>
@endsection


