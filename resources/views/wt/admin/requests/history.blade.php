@extends('wt.layouts.admin')

@section('title', 'Approval History')

@push('styles')
<style>
    .approval-history-page {
        color: var(--text);
    }
    .approval-history-title {
        font-size: 1.25rem;
        line-height: 1.1;
        font-weight: 800;
        color: var(--text);
        letter-spacing: -0.02em;
    }
    .approval-history-subtitle {
        font-size: 9px;
        font-weight: 700;
        color: var(--muted);
        letter-spacing: 0.1em;
        line-height: 1.35;
        text-transform: uppercase;
    }
    .approval-history-card {
        background: var(--surface);
        border-color: var(--border);
    }
    .approval-history-body-title {
        font-size: 10px;
        font-weight: 800;
        color: var(--text);
    }
    .approval-history-body-meta {
        font-size: 9px;
        color: var(--muted);
    }
    .approval-history-empty-state {
        display: flex;
        min-height: 112px;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 10px;
        color: var(--muted);
        font-size: 11px;
        font-weight: 700;
        text-align: center;
    }
    .approval-history-page table.dataTable tbody td.dataTables_empty {
        padding: 0 !important;
        color: var(--muted) !important;
        background: transparent !important;
    }
    .approval-history-page table.dataTable tbody tr {
        background: #1e293b !important;
        border: 1px solid rgba(255,255,255,0.05) !important;
    }
    .approval-history-page table.dataTable tbody tr:hover {
        background: rgba(56, 189, 248, 0.08) !important;
    }
    .approval-history-page table.dataTable tbody tr.history-clickable-row {
        cursor: pointer;
    }
    .history-form-sheet {
        background: var(--surface);
        color: var(--text);
    }
    .history-form-header {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        color: #ffffff;
    }
    .history-form-kicker {
        color: rgba(255,255,255,0.68);
    }
    .history-form-subtitle {
        color: rgba(255,255,255,0.76);
    }
    .history-form-section {
        overflow: hidden;
        border: 1px solid var(--border);
        border-radius: 14px;
        background: var(--surface);
    }
    .history-form-section-title {
        display: flex;
        align-items: center;
        gap: 8px;
        border-bottom: 1px solid #e7e5e4;
        background: #f8fafc;
        padding: 12px 14px;
        color: #0284c7;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.13em;
        text-transform: uppercase;
    }
    .dark .history-form-section-title {
        border-color: #334155;
        background: #111827;
        color: #f8fafc;
    }
    .history-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .history-form-field {
        min-height: 74px;
        border-right: 1px solid #e7e5e4;
        border-bottom: 1px solid #e7e5e4;
        padding: 12px 14px;
    }
    .history-form-field:nth-child(2n),
    .history-form-field-wide {
        border-right: 0;
    }
    .history-form-field-wide {
        grid-column: 1 / -1;
    }
    .dark .history-form-field {
        border-color: #334155;
    }
    .history-form-label {
        color: var(--muted);
        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }
    .history-form-value {
        margin-top: 7px;
        color: var(--text);
        font-size: 11px;
        font-weight: 800;
        line-height: 1.55;
    }
    @media (max-width: 768px) {
        .history-form-grid {
            grid-template-columns: 1fr;
        }
        .history-form-field,
        .history-form-field:nth-child(2n) {
            border-right: 0;
        }
    }
    .approval-history-page {
        --history-panel: #152033;
        --history-line: rgba(148, 163, 184, 0.16);
        --history-muted: #93a4bd;
        --history-accent: #0284c7;
        --history-accent-soft: rgba(139, 94, 60, 0.16);
    }
    .approval-history-page > .mb-4:first-child {
        margin-bottom: 16px !important;
        padding: 16px 18px;
        border: 1px solid var(--history-line);
        border-radius: 16px;
        background:
            linear-gradient(135deg, rgba(139, 94, 60, 0.13), transparent 34%),
            #111a2a;
        box-shadow: none;
    }
    .approval-history-title {
        font-size: 18px !important;
        letter-spacing: 0 !important;
        color: #f8fafc !important;
    }
    .approval-history-subtitle {
        color: #9fb0c8 !important;
        letter-spacing: 0.12em !important;
    }
    .approval-history-card {
        margin-bottom: 16px !important;
        overflow: hidden;
        border: 1px solid var(--history-line) !important;
        border-radius: 16px !important;
        background: var(--history-panel) !important;
        box-shadow: none !important;
    }
    .approval-history-card > .navy-panel {
        padding: 14px 18px !important;
        border-bottom: 1px solid var(--history-line) !important;
        background: rgba(15, 23, 42, 0.35) !important;
    }
    .approval-history-card > .navy-panel > i {
        display: inline-flex;
        width: 30px;
        height: 30px;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: var(--history-accent-soft);
        color: #38bdf8 !important;
        font-size: 13px !important;
    }
    .approval-history-card > .navy-panel h4 {
        color: #f8fafc !important;
        font-size: 11px !important;
        letter-spacing: 0.08em !important;
    }
    .approval-history-page .navy-chip {
        min-width: 28px;
        border: 1px solid rgba(217, 179, 140, 0.24) !important;
        border-radius: 999px !important;
        background: var(--history-accent) !important;
        color: #fffaf5 !important;
        text-align: center;
        box-shadow: none !important;
    }
    .approval-history-card > .p-5 {
        padding: 12px 16px 16px !important;
    }
    .approval-history-page .dataTables_wrapper .dataTables_length,
    .approval-history-page .dataTables_wrapper .dataTables_filter {
        margin: 8px 0 12px;
        color: #dbe5f2 !important;
        font-size: 10px !important;
        font-weight: 900;
        letter-spacing: 0.04em;
    }
    .approval-history-page .dataTables_wrapper .dataTables_filter input,
    .approval-history-page .dataTables_wrapper .dataTables_length select {
        min-height: 34px !important;
        border: 1px solid rgba(148, 163, 184, 0.22) !important;
        border-radius: 9px !important;
        background: #0e1626 !important;
        color: #e2e8f0 !important;
        box-shadow: none !important;
    }
    .approval-history-page .dataTables_wrapper .dataTables_filter input {
        min-width: 188px;
        padding: 8px 10px !important;
    }
    .approval-history-page table.dataTable {
        overflow: hidden;
        border: 1px solid var(--history-line) !important;
        border-radius: 12px;
        background: transparent !important;
        table-layout: auto !important;
    }
    .approval-history-page table.dataTable thead {
        background: #111a2a !important;
    }
    .approval-history-page table.dataTable thead th {
        padding: 13px 14px !important;
        border-right: 1px solid var(--history-line) !important;
        border-bottom: 1px solid var(--history-line) !important;
        color: #9fb8d8 !important;
        font-size: 9px !important;
        letter-spacing: 0.14em !important;
    }
    .approval-history-page table.dataTable tbody tr,
    .approval-history-page table.dataTable tbody tr:hover {
        background: #172236 !important;
        border: 0 !important;
    }
    .approval-history-page table.dataTable tbody tr.history-clickable-row:hover {
        background: #1b2940 !important;
    }
    .approval-history-page table.dataTable tbody td {
        padding: 14px !important;
        border-top: 1px solid rgba(148, 163, 184, 0.11) !important;
        color: #dbe5f2 !important;
    }
    .approval-history-page table.dataTable tbody td.dataTables_empty {
        background: #172236 !important;
    }
    .approval-history-empty-state {
        min-height: 136px;
        gap: 12px;
        color: #c6d3e5 !important;
    }
    .approval-history-body-title {
        color: #f8fafc !important;
    }
    .approval-history-body-meta {
        color: var(--history-muted) !important;
    }
    .approval-history-page .approval-action-btn {
        display: inline-flex;
        min-height: 32px;
        align-items: center;
        justify-content: center;
        gap: 7px;
        border-radius: 9px;
        border: 1px solid rgba(148, 163, 184, 0.22);
        background: #0e1626;
        padding: 8px 12px;
        color: #dbeafe;
        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.09em;
        text-transform: uppercase;
        transition: border-color 0.16s ease, background 0.16s ease, transform 0.16s ease;
    }
    .approval-history-page .approval-action-btn:hover {
        transform: translateY(-1px);
        border-color: rgba(217, 179, 140, 0.42) !important;
        background: #151f31 !important;
        color: #ffffff !important;
    }
    .approval-history-page .approval-action-view {
        border-color: rgba(96, 165, 250, 0.25);
        color: #bfdbfe;
    }
    .approval-history-page .approval-action-approve {
        border-color: rgba(34, 197, 94, 0.28);
        color: #bbf7d0;
    }
    .approval-history-page .approval-action-reject {
        border-color: rgba(248, 113, 113, 0.28);
        color: #fecaca;
    }
    .approval-history-page .dataTables_wrapper .dataTables_info,
    .approval-history-page .dataTables_wrapper .dataTables_paginate {
        margin-top: 12px;
        color: #92a4bd !important;
        font-size: 10px !important;
        font-weight: 800;
    }
    .approval-history-page .dataTables_wrapper .dataTables_paginate .paginate_button {
        border: 0 !important;
        background: transparent !important;
        color: #92a4bd !important;
        font-size: 10px !important;
        font-weight: 900 !important;
    }
    html:not(.dark) .approval-history-page > .mb-4:first-child,
    html:not(.dark) .approval-history-card,
    html:not(.dark) .approval-history-page table.dataTable tbody tr,
    html:not(.dark) .approval-history-page table.dataTable tbody td.dataTables_empty {
        background: #ffffff !important;
    }
    html:not(.dark) .approval-history-page > .mb-4:first-child {
        background: var(--surface) !important;
        border-color: #e7e5e4 !important;
    }
    html:not(.dark) .approval-history-title,
    html:not(.dark) .approval-history-body-title {
        color: #1f2937 !important;
    }
    html:not(.dark) .approval-history-subtitle,
    html:not(.dark) .approval-history-body-meta {
        color: #64748b !important;
    }
    html:not(.dark) .approval-history-card > .navy-panel,
    html:not(.dark) .approval-history-page table.dataTable thead {
        background: #f8fafc !important;
        border-color: #e2e8f0 !important;
    }
    html:not(.dark) .approval-history-card > .navy-panel h4 {
        color: #1f2937 !important;
    }
    html:not(.dark) .approval-history-page table.dataTable,
    html:not(.dark) .approval-history-page table.dataTable thead th,
    html:not(.dark) .approval-history-page table.dataTable tbody td {
        border-color: #e2e8f0 !important;
    }
    html:not(.dark) .approval-history-page table.dataTable thead th {
        color: #64748b !important;
    }
    html:not(.dark) .approval-history-page .dataTables_wrapper .dataTables_filter input,
    html:not(.dark) .approval-history-page .dataTables_wrapper .dataTables_length select {
        background: #ffffff !important;
        border-color: #cbd5e1 !important;
        color: #1f2937 !important;
    }
    html:not(.dark) .approval-history-page .dataTables_wrapper .dataTables_length,
    html:not(.dark) .approval-history-page .dataTables_wrapper .dataTables_filter,
    html:not(.dark) .approval-history-page .dataTables_wrapper .dataTables_info,
    html:not(.dark) .approval-history-page .dataTables_wrapper .dataTables_paginate {
        color: #475569 !important;
    }
    body .content-surface .approval-history-page .approval-history-header {
        min-height: 0 !important;
        margin-bottom: 14px !important;
        padding: 0 2px 10px !important;
        border: 0 !important;
        border-left: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        display: flex !important;
        align-items: flex-start !important;
        justify-content: space-between !important;
        gap: 12px !important;
    }
    body .content-surface .approval-history-page .approval-history-header > a.wt-btn {
        align-self: center !important;
    }
    body .content-surface .approval-history-page .approval-history-header .page-title-standard {
        color: #f8fafc !important;
        font-size: 16px !important;
        line-height: 1.15 !important;
        font-weight: 800 !important;
        letter-spacing: 0 !important;
    }
    body .content-surface .approval-history-page .approval-history-header .page-subtitle-standard {
        margin-top: 7px !important;
        color: #a8b6c8 !important;
        font-size: 9px !important;
        line-height: 1.35 !important;
        font-weight: 900 !important;
        letter-spacing: 0.18em !important;
        text-transform: uppercase !important;
    }
    body .content-surface .approval-history-page .approval-history-header .wt-btn {
        min-height: 24px !important;
        padding: 0 8px !important;
        border: 1px solid #2f5d86 !important;
        border-radius: 6px !important;
        background: transparent !important;
        color: #e5e7eb !important;
        box-shadow: none !important;
        gap: 5px !important;
        font-size: 9px !important;
        font-weight: 700 !important;
        letter-spacing: 0 !important;
        text-transform: none !important;
        white-space: nowrap !important;
    }
    body .content-surface .approval-history-page .approval-history-header .wt-btn i {
        font-size: 9px !important;
    }
    body .content-surface .approval-history-page .approval-history-header .wt-btn:hover {
        border-color: #3d79ad !important;
        background: rgba(59, 130, 246, 0.08) !important;
        color: #ffffff !important;
    }
    body .content-surface .approval-history-page table.dataTable thead th,
    body .content-surface .approval-history-page .dataTables_wrapper table.dataTable thead th {
        font-size: 8px !important;
        line-height: 1.05 !important;
        letter-spacing: 0.08em !important;
    }
    body .content-surface .approval-history-page .dataTables_info,
    body .content-surface .approval-history-page .dataTables_paginate,
    body .content-surface .approval-history-page .adminit-table-footer,
    body .content-surface .approval-history-page .adminit-table-info,
    body .content-surface .approval-history-page .adminit-table-pagination {
        display: none !important;
    }
    body .content-surface .approval-history-page .dataTables_length {
        display: none !important;
    }
    body .content-surface .approval-history-page .dataTables_filter {
        justify-content: flex-start !important;
        width: 100% !important;
        min-height: 42px !important;
        padding: 6px !important;
        margin: 0 !important;
    }
    body .content-surface .approval-history-page .dataTables_filter label {
        width: 100% !important;
    }
    body .content-surface .approval-history-page .dataTables_filter input {
        width: 100% !important;
        min-width: 0 !important;
        height: 34px !important;
        min-height: 34px !important;
        margin-left: 0 !important;
        padding: 6px 12px !important;
        font-size: 12px !important;
    }
    @media (max-width: 700px) {
        body .content-surface .approval-history-page .approval-history-header {
            align-items: flex-start !important;
            flex-direction: column !important;
            min-height: auto !important;
            padding: 0 2px 10px !important;
        }
        body .content-surface .approval-history-page .approval-history-header .wt-btn {
            min-height: 24px !important;
            font-size: 9px !important;
        }
    }
</style>
@endpush

@section('content')
@php
    $resolveReportedBy = function ($request) {
        $submittedBy = $request->user;

        if (! $submittedBy && $request->submitToAdmin) {
            $submittedBy = $request->submitToAdmin;
        }

        $role = strtolower((string) ($submittedBy->role ?? 'user'));
        $roleLabel = match ($role) {
            'admin' => 'Executive',
            'admin_it' => 'ICT',
            default => 'Executive',
        };

        return [
            'name' => strtoupper((string) (($submittedBy->full_name ?? null) ?: ($submittedBy->username ?? null) ?: ($request->full_name ?? '-'))),
            'staff_id' => strtoupper((string) (($submittedBy->staff_id ?? null) ?: ($request->staff_id ?? '-'))),
            'department' => strtoupper((string) (($submittedBy->department ?? null) ?: ($request->department ?? '-'))),
            'role' => $roleLabel,
        ];
    };

    $resolveExecutiveOwner = function ($record) {
        $executive = $record->submitToAdmin ?? null;

        return [
            'name' => strtoupper((string) (($executive->full_name ?? null) ?: ($executive->username ?? null) ?: '-')),
            'staff_id' => strtoupper((string) (($executive->staff_id ?? null) ?: '-')),
            'department' => strtoupper((string) (($executive->department ?? null) ?: '-')),
        ];
    };
@endphp
<div class="approval-history-page">
    <div class="approval-history-header page-header-block flex flex-col md:flex-row md:items-start md:justify-between gap-4">
        <div>
            <h3 class="page-title-standard">Approval History</h3>
            <p class="page-subtitle-standard">Review past approved and rejected ICT decisions.</p>
        </div>
        <a href="{{ route('wt.admin.requests.index') }}" class="wt-btn wt-btn-soft">
            <i class="fa-solid fa-inbox text-[13px]"></i>
            Pending Inbox
        </a>
    </div>

    <div class="approval-history-card rounded-[26px] shadow-sm border overflow-hidden mb-8">
        <div class="navy-panel px-5 py-3.5 flex flex-wrap items-center gap-3">
            <i class="fa-solid fa-clock-rotate-left text-white text-lg"></i>
            <h4 class="text-white font-bold tracking-widest uppercase text-[11px]">Walkie Request Decisions</h4>
            <div class="ml-auto flex flex-wrap items-center gap-2">
                <span class="navy-chip font-black px-2.5 py-0.5 rounded-full">{{ $historyRequests->count() }}</span>
            </div>
        </div>
        <div class="p-5">
            <table id="historyRequestsTable" class="w-full text-left display nowrap">
                <thead class="bg-stone-50 text-stone-400 text-[10px] uppercase font-black tracking-[0.15em]">
                    <tr>
                        <th class="px-4 py-4">Requestor</th>
                        <th class="px-4 py-4">Decision</th>
                        <th class="px-4 py-4">Assigned Unit</th>
                        <th class="px-4 py-4">Handled By</th>
                        <th class="px-4 py-4">Remark</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-50">
                    @forelse($historyRequests as $history)
                    @php
                        $executiveOwner = $resolveExecutiveOwner($history);
                        $isReturnedHistory = $history->return_status === 'Returned';
                    @endphp
                    <tr class="transition history-clickable-row" onclick="openHistoryFormModal('historyRequestFormModal-{{ $history->id }}')" title="Click to view full form">
                        <td class="px-4 py-4">
                            <div class="approval-history-body-title">{{ $history->full_name ?: '-' }}</div>
                            <div class="approval-history-body-meta mt-1">Request #{{ str_pad($history->id, 5, '0', STR_PAD_LEFT) }}</div>
                            <div class="approval-history-body-meta mt-1 uppercase">{{ $history->department ?: '-' }}</div>
                            <div class="approval-history-body-meta mt-2 uppercase">Executive: {{ $executiveOwner['name'] }} / {{ $executiveOwner['department'] }}</div>
                        </td>
                        <td class="px-4 py-4">
                            <span class="approval-action-btn {{ $history->status === 'Approved' ? 'approval-action-approve' : 'approval-action-reject' }}">
                                {{ $isReturnedHistory ? 'RETURNED / HISTORY' : strtoupper($history->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="approval-history-body-title">{{ $history->radio_id ?: '-' }}</div>
                            <div class="approval-history-body-meta mt-1 uppercase">{{ $history->assigned_serial_number ?: ($history->walkieTalkie->serial_number ?? '-') }}</div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="approval-history-body-title">{{ strtoupper(($history->handler->full_name ?? null) ?: ($history->handler->username ?? '-')) }}</div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="approval-history-body-meta max-w-xs truncate">{{ $history->approval_remark ?: '-' }}</div>
                        </td>
                    </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="approval-history-card rounded-[26px] shadow-sm border overflow-hidden mb-8">
        <div class="navy-panel px-5 py-3.5 flex flex-wrap items-center gap-3">
            <i class="fa-solid fa-triangle-exclamation text-white text-lg"></i>
            <h4 class="text-white font-bold tracking-widest uppercase text-[11px]">Faulty Report Decisions</h4>
            <div class="ml-auto flex flex-wrap items-center gap-2">
                <span class="navy-chip font-black px-2.5 py-0.5 rounded-full">{{ $historyDamageReports->count() }}</span>
            </div>
        </div>
        <div class="p-5">
            <table id="historyDamagesTable" class="w-full text-left display nowrap">
                <thead class="bg-stone-50 text-stone-400 text-[10px] uppercase font-black tracking-[0.15em]">
                    <tr>
                        <th class="px-4 py-4">Reporter</th>
                        <th class="px-4 py-4">Decision</th>
                        <th class="px-4 py-4">Unit</th>
                        <th class="px-4 py-4">Handled By</th>
                        <th class="px-4 py-4">Remark</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-50">
                    @forelse($historyDamageReports as $historyDamage)
                    @php
                        $damageApproved = strtoupper((string) $historyDamage->status) === 'UNDER REPAIR';
                        $executiveOwner = $resolveExecutiveOwner($historyDamage);
                    @endphp
                    <tr class="transition history-clickable-row" onclick="openHistoryFormModal('historyDamageFormModal-{{ $historyDamage->maintenance_id }}')" title="Click to view full form">
                        <td class="px-4 py-4">
                            <div class="approval-history-body-title">{{ $historyDamage->reporter_name ?: '-' }}</div>
                            <div class="approval-history-body-meta mt-1">Report #{{ str_pad($historyDamage->maintenance_id, 4, '0', STR_PAD_LEFT) }}</div>
                            <div class="approval-history-body-meta mt-1 uppercase">{{ $historyDamage->department_name ?: '-' }}</div>
                            <div class="approval-history-body-meta mt-2 uppercase">Executive: {{ $executiveOwner['name'] }} / {{ $executiveOwner['department'] }}</div>
                        </td>
                        <td class="px-4 py-4">
                            <span class="approval-action-btn {{ $damageApproved ? 'approval-action-approve' : 'approval-action-reject' }}">
                                {{ $damageApproved ? 'APPROVED' : 'REJECTED' }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="approval-history-body-title">{{ $historyDamage->radio_id ?: '-' }}</div>
                            <div class="approval-history-body-meta mt-1 uppercase">{{ $historyDamage->serial_number ?: '-' }}</div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="approval-history-body-title">{{ strtoupper(($historyDamage->handler->full_name ?? null) ?: ($historyDamage->handler->username ?? '-')) }}</div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="approval-history-body-meta max-w-xs truncate">{{ $historyDamage->remarks ?: '-' }}</div>
                        </td>
                    </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@foreach($historyRequests as $history)
@php
    $isTemporaryRequest = $history->request_type === 'temporary_walkie_talkie';
    $reportedBy = $resolveReportedBy($history);
@endphp
<div id="historyRequestFormModal-{{ $history->id }}" class="fixed inset-0 bg-stone-900/60 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4" onclick="if (event.target === this) closeHistoryFormModal('historyRequestFormModal-{{ $history->id }}')">
    <div class="history-form-sheet w-full max-w-4xl max-h-[92vh] overflow-y-auto rounded-xl shadow-2xl border border-slate-300 dark:border-slate-700">
        <div class="history-form-header px-6 py-5 relative">
            <p class="history-form-kicker text-[9px] font-black uppercase tracking-[0.2em]">Full Request Form</p>
            <div class="text-xl font-black tracking-tight">Request #{{ str_pad($history->id, 5, '0', STR_PAD_LEFT) }}</div>
            <p class="history-form-subtitle mt-1 text-xs font-bold">{{ $history->event_name ?: 'Walkie Talkie Request' }}</p>
            <button type="button" onclick="closeHistoryFormModal('historyRequestFormModal-{{ $history->id }}')" class="absolute top-5 right-5 text-white/60 hover:text-white">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="p-5 md:p-6 space-y-4">
            <div class="history-form-section">
                <div class="history-form-section-title"><i class="fa-solid fa-user-check"></i> Submitted Details</div>
                <div class="history-form-grid">
                    <div class="history-form-field">
                        <div class="history-form-label">Reported By</div>
                        <div class="history-form-value">
                            {{ $reportedBy['name'] }}
                            <div class="mt-1 text-[10px] font-black uppercase tracking-widest text-[#0284c7]">{{ $reportedBy['role'] }}</div>
                        </div>
                    </div>
                    <div class="history-form-field">
                        <div class="history-form-label">Submitted ID</div>
                        <div class="history-form-value">{{ $reportedBy['staff_id'] }}</div>
                    </div>
                    <div class="history-form-field">
                        <div class="history-form-label">Status</div>
                        <div class="history-form-value">{{ strtoupper($history->status ?: '-') }}</div>
                    </div>
                </div>
            </div>

            <div class="history-form-section">
                <div class="history-form-section-title"><i class="fa-solid fa-id-card"></i> Requestor Details</div>
                <div class="history-form-grid">
                    <div class="history-form-field">
                        <div class="history-form-label">Request For</div>
                        <div class="history-form-value">{{ strtoupper($history->full_name ?: '-') }}</div>
                    </div>
                    <div class="history-form-field">
                        <div class="history-form-label">Requestor ID</div>
                        <div class="history-form-value">{{ strtoupper($history->staff_id ?: '-') }}</div>
                    </div>
                    <div class="history-form-field">
                        <div class="history-form-label">Department</div>
                        <div class="history-form-value">{{ strtoupper($history->department ?: '-') }}</div>
                    </div>
                    <div class="history-form-field">
                        <div class="history-form-label">Ownership Type</div>
                        <div class="history-form-value">{{ strtoupper($history->ownership_type ?: 'INDIVIDUAL') }}</div>
                    </div>
                </div>
            </div>

            @if(! empty($history->pic_details))
            <div class="history-form-section">
                <div class="history-form-section-title"><i class="fa-solid fa-user-check"></i> Ownership Per Unit</div>
                <div class="history-form-grid">
                    @foreach($history->pic_details as $picIndex => $pic)
                    <div class="history-form-field">
                        <div class="history-form-label">Unit {{ $picIndex + 1 }}</div>
                        <div class="history-form-value">
                            {{ strtoupper($pic['name'] ?? '-') }} / {{ strtoupper($pic['phone_no'] ?? '-') }}<br>
                            {{ strtoupper($pic['department'] ?? '-') }} / {{ strtoupper($pic['ownership_type'] ?? '-') }}<br>
                            {{ strtoupper($pic['sector'] ?? '-') }} / {{ strtoupper($pic['location'] ?? '-') }}<br>
                            PICKUP: {{ strtoupper($pic['pickup_person'] ?? '-') }} / {{ strtoupper($pic['pickup_phone_no'] ?? '-') }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="history-form-section">
                <div class="history-form-section-title"><i class="fa-solid fa-calendar-days"></i> Request Details</div>
                <div class="history-form-grid">
                    <div class="history-form-field">
                        <div class="history-form-label">Request Type</div>
                        <div class="history-form-value">{{ $isTemporaryRequest ? 'TEMPORARY WALKIE TALKIE' : 'WALKIE TALKIE' }}</div>
                    </div>
                    <div class="history-form-field">
                        <div class="history-form-label">{{ $isTemporaryRequest ? 'Start Date' : 'Request Date' }}</div>
                        <div class="history-form-value">{{ $history->request_date ? \Carbon\Carbon::parse($history->request_date)->format('d M Y') : '-' }}</div>
                    </div>
                    <div class="history-form-field">
                        <div class="history-form-label">End Date</div>
                        <div class="history-form-value">{{ $isTemporaryRequest && $history->end_date ? \Carbon\Carbon::parse($history->end_date)->format('d M Y') : '-' }}</div>
                    </div>
                    <div class="history-form-field">
                        <div class="history-form-label">Duration / Quantity</div>
                        @php
                            $historyDays = max(1, (int) ($history->duration_days ?: 1));
                            $historyQty = max(1, (int) ($history->quantity ?: 1));
                        @endphp
                        <div class="history-form-value">{{ $isTemporaryRequest ? ($historyDays . ' ' . \Illuminate\Support\Str::plural('day', $historyDays) . ', ' . $historyQty . ' ' . \Illuminate\Support\Str::plural('unit', $historyQty)) : ($historyQty . ' ' . \Illuminate\Support\Str::plural('unit', $historyQty)) }}</div>
                    </div>
                    <div class="history-form-field">
                        <div class="history-form-label">Bay From</div>
                        <div class="history-form-value">{{ strtoupper($history->bay_from ?: '-') }}</div>
                    </div>
                    <div class="history-form-field">
                        <div class="history-form-label">Sector / Location</div>
                        <div class="history-form-value">{{ strtoupper($history->sector ?: '-') }} / {{ strtoupper($history->location ?: '-') }}</div>
                    </div>
                </div>
            </div>

            @include('wt.partials.pickup-preference-section', ['request' => $history, 'prefix' => 'history-form'])

            <div class="history-form-section">
                <div class="history-form-section-title"><i class="fa-solid fa-clipboard-list"></i> Event & ICT Handling</div>
                <div class="history-form-grid">
                    <div class="history-form-field history-form-field-wide">
                        <div class="history-form-label">Event / Purpose</div>
                        <div class="history-form-value">{{ $history->event_name ?: '-' }}</div>
                    </div>
                    <div class="history-form-field history-form-field-wide">
                        <div class="history-form-label">Justification</div>
                        <div class="history-form-value whitespace-pre-line">{{ $history->justifications ?: '-' }}</div>
                    </div>
                    <div class="history-form-field history-form-field-wide">
                        <div class="history-form-label">Requested / Assigned Accessories</div>
                        <div class="history-form-value">{{ $history->accessories ?: 'To be selected by ICT' }}</div>
                    </div>
                    <div class="history-form-field">
                        <div class="history-form-label">Assigned Unit</div>
                        <div class="history-form-value">{{ strtoupper($history->radio_id ?: '-') }} / {{ strtoupper($history->assigned_serial_number ?: ($history->walkieTalkie->serial_number ?? '-')) }}</div>
                    </div>
                    <div class="history-form-field">
                        <div class="history-form-label">ICT Remark</div>
                        <div class="history-form-value whitespace-pre-line">{{ $history->approval_remark ?: '-' }}</div>
                    </div>
                </div>
            </div>

            @include('wt.partials.ict-update-section', ['request' => $history, 'prefix' => 'history-form'])

            <div class="mt-5 flex justify-end">
                <button type="button" onclick="closeHistoryFormModal('historyRequestFormModal-{{ $history->id }}')" class="approval-action-btn approval-action-view">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@foreach($historyDamageReports as $historyDamage)
@php
    $evidenceFiles = [];
    if (! empty($historyDamage->evidence_paths)) {
        $evidenceFiles = is_array($historyDamage->evidence_paths)
            ? $historyDamage->evidence_paths
            : (json_decode($historyDamage->evidence_paths, true) ?: []);
    }
    $replacementRequested = str_contains((string) ($historyDamage->remarks ?? ''), 'REPLACEMENT REQUESTED');
@endphp
<div id="historyDamageFormModal-{{ $historyDamage->maintenance_id }}" class="fixed inset-0 bg-stone-900/60 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4" onclick="if (event.target === this) closeHistoryFormModal('historyDamageFormModal-{{ $historyDamage->maintenance_id }}')">
    <div class="history-form-sheet w-full max-w-5xl max-h-[92vh] overflow-y-auto rounded-xl shadow-2xl border border-slate-300 dark:border-slate-700">
        <div class="history-form-header px-6 py-5 relative">
            <p class="history-form-kicker text-[9px] font-black uppercase tracking-[0.2em]">Faulty Walkie Talkie Application Form</p>
            <div class="text-xl font-black tracking-tight">Damage Report #{{ str_pad($historyDamage->maintenance_id, 4, '0', STR_PAD_LEFT) }}</div>
            <p class="history-form-subtitle mt-1 text-xs font-bold">{{ strtoupper($historyDamage->reporter_name ?: '-') }} / {{ strtoupper($historyDamage->department_name ?: '-') }}</p>
            <button type="button" onclick="closeHistoryFormModal('historyDamageFormModal-{{ $historyDamage->maintenance_id }}')" class="absolute top-5 right-5 text-white/60 hover:text-white">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="p-5 md:p-6 space-y-4">
            <div class="history-form-section">
                <div class="history-form-section-title"><i class="fa-solid fa-user-check"></i> Reporter Details</div>
                <div class="history-form-grid">
                    <div class="history-form-field">
                        <div class="history-form-label">Reporter Name</div>
                        <div class="history-form-value">{{ strtoupper($historyDamage->reporter_name ?: '-') }}</div>
                    </div>
                    <div class="history-form-field">
                        <div class="history-form-label">Department</div>
                        <div class="history-form-value">{{ strtoupper($historyDamage->department_name ?: '-') }}</div>
                    </div>
                    <div class="history-form-field">
                        <div class="history-form-label">Phone No.</div>
                        <div class="history-form-value">{{ $historyDamage->phone_no ?: '-' }}</div>
                    </div>
                    <div class="history-form-field">
                        <div class="history-form-label">Request Source</div>
                        <div class="history-form-value">{{ strtoupper(str_replace('_', ' ', $historyDamage->request_source ?: 'USER')) }}</div>
                    </div>
                </div>
            </div>

            <div class="history-form-section">
                <div class="history-form-section-title"><i class="fa-solid fa-walkie-talkie"></i> Walkie Talkie Details</div>
                <div class="history-form-grid">
                    <div class="history-form-field">
                        <div class="history-form-label">Radio ID</div>
                        <div class="history-form-value">{{ strtoupper($historyDamage->radio_id ?: '-') }}</div>
                    </div>
                    <div class="history-form-field">
                        <div class="history-form-label">Serial Number</div>
                        <div class="history-form-value">{{ strtoupper($historyDamage->serial_number ?: '-') }}</div>
                    </div>
                    <div class="history-form-field">
                        <div class="history-form-label">Model</div>
                        <div class="history-form-value">{{ strtoupper($historyDamage->model ?: '-') }}</div>
                    </div>
                    <div class="history-form-field">
                        <div class="history-form-label">Submitted Date</div>
                        <div class="history-form-value">{{ $historyDamage->received_date ? \Carbon\Carbon::parse($historyDamage->received_date)->format('d M Y') : '-' }}</div>
                    </div>
                </div>
            </div>

            <div class="history-form-section">
                <div class="history-form-section-title"><i class="fa-solid fa-people-arrows"></i> Ownership & Location</div>
                <div class="history-form-grid">
                    <div class="history-form-field">
                        <div class="history-form-label">Ownership Type</div>
                        <div class="history-form-value">{{ strtoupper($historyDamage->ownership_type ?: '-') }}</div>
                    </div>
                    <div class="history-form-field">
                        <div class="history-form-label">Shared With</div>
                        <div class="history-form-value">{{ strtoupper($historyDamage->shared_with ?: '-') }}</div>
                    </div>
                    <div class="history-form-field">
                        <div class="history-form-label">Sector</div>
                        <div class="history-form-value">{{ strtoupper($historyDamage->sector ?: '-') }}</div>
                    </div>
                    <div class="history-form-field">
                        <div class="history-form-label">Location</div>
                        <div class="history-form-value">{{ strtoupper($historyDamage->location ?: '-') }}</div>
                    </div>
                </div>
            </div>

            <div class="history-form-section">
                <div class="history-form-section-title"><i class="fa-solid fa-triangle-exclamation"></i> Decision & Issue</div>
                <div class="history-form-grid">
                    <div class="history-form-field">
                        <div class="history-form-label">Status</div>
                        <div class="history-form-value">{{ strtoupper($historyDamage->status ?: '-') }}</div>
                    </div>
                    <div class="history-form-field">
                        <div class="history-form-label">Handled By</div>
                        <div class="history-form-value">{{ strtoupper(($historyDamage->handler->full_name ?? null) ?: ($historyDamage->handler->username ?? '-') ) }}</div>
                    </div>
                    <div class="history-form-field history-form-field-wide">
                        <div class="history-form-label">Problem Reported</div>
                        <div class="history-form-value whitespace-pre-line">{{ $historyDamage->problem_possible ?: ($historyDamage->issue ?: ($historyDamage->issue_description ?: '-')) }}</div>
                    </div>
                    <div class="history-form-field history-form-field-wide">
                        <div class="history-form-label">Remarks / Replacement Details</div>
                        <div class="history-form-value whitespace-pre-line">
                            {{ $historyDamage->remarks ?: 'No additional remarks.' }}
                            @if($replacementRequested)
                                <div class="mt-2 text-[10px] font-black uppercase tracking-widest text-emerald-600">Replacement Requested</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="history-form-section">
                <div class="history-form-section-title"><i class="fa-solid fa-paperclip"></i> Evidence Uploaded</div>
                <div class="p-4">
                    <div class="history-form-value">
                        @if(!empty($evidenceFiles))
                        <div class="flex flex-wrap gap-2">
                            @foreach($evidenceFiles as $path)
                            <a href="{{ asset('storage/' . $path) }}" target="_blank" rel="noopener noreferrer" onclick="event.stopPropagation()" class="inline-flex items-center gap-2 rounded-lg border border-stone-200 px-3 py-2 text-[10px] font-black uppercase tracking-widest text-[#0284c7] hover:bg-stone-50 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-800">
                                <i class="fa-solid fa-paperclip"></i> Evidence {{ $loop->iteration }}
                            </a>
                            @endforeach
                        </div>
                        @else
                        No evidence uploaded.
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-5 flex justify-end">
                <button type="button" onclick="closeHistoryFormModal('historyDamageFormModal-{{ $historyDamage->maintenance_id }}')" class="approval-action-btn approval-action-view">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('scripts')
<script>
    function openHistoryFormModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            if (modal.parentElement !== document.body) {
                document.body.appendChild(modal);
            }
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeHistoryFormModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    $(document).ready(function() {
        const emptyHistoryState = `
            <div class="approval-history-empty-state">
                <span>No history records.</span>
            </div>
        `;

        const historyTableOptions = {
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
            ordering: false,
            autoWidth: false,
            language: {
                search: "",
                searchPlaceholder: "Search history...",
                emptyTable: emptyHistoryState,
                zeroRecords: emptyHistoryState,
            },
            dom: 'Bfrtip',
            initComplete: function() {
                this.api().columns.adjust();
            }
        };

        if ($('#historyRequestsTable').length) {
            const historyRequestsTable = $('#historyRequestsTable').DataTable({
                ...historyTableOptions,
                buttons: getAdminTableExportButtons('Approval History - Requests', ':not(:last-child)')
            });
            mountAdminTableFooter(historyRequestsTable);
        }

        if ($('#historyDamagesTable').length) {
            const historyDamagesTable = $('#historyDamagesTable').DataTable({
                ...historyTableOptions,
                buttons: getAdminTableExportButtons('Approval History - Faulty Reports', ':not(:last-child)')
            });
            mountAdminTableFooter(historyDamagesTable);
        }
    });
</script>
@endpush

