@extends('wt.layouts.admin')

@section('title', 'All Status Tracking')

@push('styles')
<style>
    .status-color-badge {
        box-shadow: none !important;
    }
    .status-color-badge.status-draft {
        background: #f1f5f9 !important;
        border-color: #cbd5e1 !important;
        color: #475569 !important;
    }
    .status-color-badge.status-processing {
        background: #fef3c7 !important;
        border-color: #fbbf24 !important;
        color: #92400e !important;
    }
    .status-color-badge.status-ready {
        background: #dbeafe !important;
        border-color: #60a5fa !important;
        color: #1d4ed8 !important;
    }
    .status-color-badge.status-approved,
    .status-color-badge.status-completed {
        background: #dcfce7 !important;
        border-color: #4ade80 !important;
        color: #166534 !important;
    }
    .status-color-badge.status-rejected {
        background: #fee2e2 !important;
        border-color: #f87171 !important;
        color: #991b1b !important;
    }
    .status-color-badge.status-unknown {
        background: #e2e8f0 !important;
        border-color: #94a3b8 !important;
        color: #334155 !important;
    }
    .all-status-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        border-radius: 6px;
        border: 1px solid #dbeafe;
        background: #f8fafc;
        color: #2563eb;
        padding: 4px 7px;
        font-size: 7px;
        font-weight: 900;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        white-space: nowrap;
        transition: background-color 0.18s ease, border-color 0.18s ease, color 0.18s ease;
    }
    .all-status-action-btn:hover {
        background: #eff6ff;
        border-color: #bfdbfe;
        color: #1d4ed8;
    }
    .all-status-muted {
        color: #94a3b8;
        font-size: 7px;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .all-status-compact {
        width: min(100%, 1320px);
    }
    .all-status-compact .page-header-block {
        padding: 0.7rem 0.9rem !important;
        margin-bottom: 10px !important;
        border-radius: 9px !important;
    }
    .all-status-compact .page-title-standard {
        font-size: 14px !important;
        line-height: 1.15 !important;
    }
    .all-status-compact .page-subtitle-standard {
        margin-top: 5px !important;
        font-size: 8px !important;
        line-height: 1.25 !important;
        letter-spacing: 0.14em !important;
    }
    .all-status-compact .status-tab-wrap {
        margin-bottom: 10px !important;
        border-radius: 10px !important;
        padding: 3px !important;
    }
    .all-status-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
        margin-bottom: 10px;
    }
    .all-status-summary-card {
        border: 1px solid #dbe3ee;
        border-radius: 10px;
        background: #ffffff;
        padding: 10px 12px;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.05);
    }
    .all-status-summary-label {
        font-size: 8px;
        font-weight: 900;
        letter-spacing: 0.14em;
        line-height: 1.2;
        text-transform: uppercase;
    }
    .all-status-summary-value {
        margin-top: 8px;
        color: #172033;
        font-size: 22px;
        font-weight: 900;
        line-height: 1;
    }
    .all-status-summary-label.is-draft {
        color: #64748b;
    }
    .all-status-summary-label.is-processing {
        color: #d97706;
    }
    .all-status-summary-label.is-ready {
        color: #0284c7;
    }
    .all-status-summary-label.is-approved {
        color: #059669;
    }
    .all-status-summary-label.is-history {
        color: #4f46e5;
    }
    .all-status-summary-label.is-rejected {
        color: #dc2626;
    }
    .dark .all-status-summary-card {
        border-color: #334155;
        background: rgba(30, 41, 59, 0.62);
        box-shadow: none;
    }
    .dark .all-status-summary-value {
        color: #f8fafc;
    }
    .all-status-compact .status-tab-btn {
        min-height: 26px !important;
        padding: 5px 12px !important;
        border-radius: 7px !important;
        font-size: 8px !important;
        letter-spacing: 0.08em !important;
    }
    .all-status-compact .status-tab-btn i {
        margin-right: 6px !important;
        font-size: 9px !important;
    }
    .all-status-compact .status-table-card {
        padding: 12px !important;
        border: 1px solid #dbeafe !important;
        border-radius: 14px !important;
        background: #ffffff !important;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08) !important;
    }
    .dark .all-status-compact .status-table-card {
        border-color: #334155 !important;
        background: rgba(30, 41, 59, 0.72) !important;
        box-shadow: none !important;
    }
    .all-status-compact .dataTables_wrapper {
        border: 1px solid #dbeafe !important;
        border-radius: 12px !important;
        background: #ffffff !important;
        box-shadow: none !important;
        overflow: hidden !important;
    }
    .dark .all-status-compact .dataTables_wrapper {
        border-color: #334155 !important;
        background: #0f172a !important;
    }
    .all-status-compact table.dataTable thead th,
    .all-status-compact table.dataTable tbody td {
        padding: 5px 8px !important;
        font-size: 8px !important;
        line-height: 1.2 !important;
        vertical-align: middle !important;
    }
    .all-status-compact .dataTables_length,
    .all-status-compact .dataTables_filter,
    .all-status-compact .dataTables_info,
    .all-status-compact .dataTables_paginate {
        font-size: 8px !important;
    }
    .all-status-compact .dataTables_wrapper .dataTables_length,
    .all-status-compact .dataTables_wrapper .dataTables_filter {
        margin-bottom: 0 !important;
        padding: 10px 12px 8px !important;
        background: #ffffff !important;
    }
    .dark .all-status-compact .dataTables_wrapper .dataTables_length,
    .dark .all-status-compact .dataTables_wrapper .dataTables_filter {
        background: #0f172a !important;
    }
    .all-status-compact .dataTables_wrapper .dataTables_info,
    .all-status-compact .dataTables_wrapper .dataTables_paginate {
        padding: 10px 12px 12px !important;
        background: #ffffff !important;
    }
    .dark .all-status-compact .dataTables_wrapper .dataTables_info,
    .dark .all-status-compact .dataTables_wrapper .dataTables_paginate {
        background: #0f172a !important;
    }
    .all-status-compact .dataTables_wrapper .dataTables_length select,
    .all-status-compact .dataTables_wrapper .dataTables_filter input {
        min-height: 24px !important;
        height: 24px !important;
        padding: 2px 7px !important;
        font-size: 8px !important;
        border-radius: 6px !important;
    }
    .all-status-compact .dataTables_wrapper .dataTables_filter input {
        width: 150px !important;
    }
    .all-status-compact table.dataTable thead th {
        height: 26px !important;
        letter-spacing: 0.08em !important;
    }
    .all-status-compact table.dataTable tbody td {
        height: 30px !important;
    }
    .all-status-compact table.dataTable tbody td span[class*="bg-"],
    .all-status-compact table.dataTable tbody td span[class*="status-color-badge"] {
        padding: 2px 6px !important;
        font-size: 7px !important;
        letter-spacing: 0.06em !important;
    }
    .all-status-compact table.dataTable tbody td .mt-1 {
        margin-top: 2px !important;
    }
    #allDamagesTable {
        min-width: 1080px !important;
        table-layout: fixed !important;
        width: 100% !important;
    }
    #allDamagesTable th,
    #allDamagesTable td {
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }
    #allDamagesTable th:nth-child(1),
    #allDamagesTable td:nth-child(1) {
        width: 100px !important;
    }
    #allDamagesTable th:nth-child(2),
    #allDamagesTable td:nth-child(2) {
        width: 165px !important;
    }
    #allDamagesTable th:nth-child(3),
    #allDamagesTable td:nth-child(3) {
        width: 155px !important;
    }
    #allDamagesTable th:nth-child(4),
    #allDamagesTable td:nth-child(4) {
        width: 205px !important;
    }
    #allDamagesTable th:nth-child(5),
    #allDamagesTable td:nth-child(5) {
        width: 220px !important;
    }
    #allDamagesTable th:nth-child(6),
    #allDamagesTable td:nth-child(6) {
        width: 130px !important;
    }
    #allDamagesTable th:nth-child(7),
    #allDamagesTable td:nth-child(7) {
        width: 110px !important;
    }
    .all-status-compact .dataTables_scrollHeadInner,
    .all-status-compact .dataTables_scrollHeadInner table,
    .all-status-compact .dataTables_scrollBody table {
        width: 100% !important;
    }
    @media (max-width: 1368px) {
        .all-status-compact {
            width: 100%;
        }
    }
    @media (max-width: 900px) {
        .all-status-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
</style>
@endpush

@section('content')
@php
    $user = auth('wt')->user();
    
    $isSelfRequest = function ($request) use ($user) {
        return (int) ($request->user_id ?? 0) === (int) $user->user_id
            && strtoupper(trim((string) ($request->staff_id ?? ''))) === strtoupper(trim((string) ($user->staff_id ?? '')))
            && strtoupper(trim((string) ($request->full_name ?? ''))) === strtoupper(trim((string) (($user->full_name ?: $user->username) ?? '')));
    };

    $statusBadge = function ($request) {
        if (($request->return_status ?? null) === 'Returned') {
            return ['text' => 'Returned / History', 'class' => 'status-color-badge status-unknown'];
        }

        if (in_array($request->return_status, ['Pending Admin Approval', 'Pending IT Approval'], true)) {
            return ['text' => 'Return Processing', 'class' => 'status-color-badge status-processing'];
        }

        if ($request->status === 'Approved' && ! $request->handover) {
            return ['text' => 'Ready To Collect', 'class' => 'status-color-badge status-ready'];
        }

        return match ($request->status) {
            'Draft' => ['text' => 'Draft', 'class' => 'status-color-badge status-draft'],
            'Pending Admin Approval', 'Pending IT Approval' => ['text' => 'Processing', 'class' => 'status-color-badge status-processing'],
            'Pending Executive Pickup' => ['text' => 'Ready To Collect', 'class' => 'status-color-badge status-ready'],
            'Approved' => ['text' => 'Approved', 'class' => 'status-color-badge status-approved'],
            'Rejected' => ['text' => 'Rejected', 'class' => 'status-color-badge status-rejected'],
            default => ['text' => $request->status ?: 'Unknown', 'class' => 'status-color-badge status-unknown'],
        };
    };

    $damageBadge = function ($record) {
        $status = strtoupper((string) $record->status);
        if (in_array($status, ['REJECTED', 'REFUSED'], true)) {
            return ['text' => 'Rejected', 'class' => 'status-color-badge status-rejected'];
        }
        if ((bool) $record->done || $status === 'DONE') {
            return ['text' => 'Completed / History', 'class' => 'status-color-badge status-completed'];
        }
        if ($status === 'DRAFT') {
            return ['text' => 'Draft', 'class' => 'status-color-badge status-draft'];
        }
        if (in_array($status, ['WAITING FOR ADMIN', 'PENDING ADMIN IT'], true)) {
            return ['text' => 'Processing', 'class' => 'status-color-badge status-processing'];
        }
        if (in_array($status, ['READY TO COLLECT', 'ALREADY FIXED'], true)) {
            return ['text' => 'Ready To Collect', 'class' => 'status-color-badge status-ready'];
        }
        if (in_array($status, ['UNDER REPAIR', 'REPAIRING', 'FAULTY', 'B.E.R'], true)) {
            return ['text' => 'Approved', 'class' => 'status-color-badge status-approved'];
        }
        return ['text' => 'Processing', 'class' => 'status-color-badge status-processing'];
    };

    $resolveReportedBy = function ($request) {
        $submittedBy = $request->user ?: $request->submitToAdmin;
        $role = strtolower((string) ($submittedBy->wt_role ?? 'user'));

        return [
            'name' => strtoupper((string) (($submittedBy->full_name ?? null) ?: ($submittedBy->username ?? null) ?: ($request->full_name ?? '-'))),
            'staff_id' => strtoupper((string) (($submittedBy->staff_id ?? null) ?: ($request->staff_id ?? '-'))),
            'role' => match ($role) {
                'admin' => 'Executive',
                'admin_it' => 'ICT',
                default => 'Executive',
            },
        ];
    };
@endphp

<div class="all-status-compact">
<div class="page-header-block">
    <h3 class="page-title-standard">All Status Tracking</h3>
    <p class="page-subtitle-standard">
        Consolidated view for walkie requests and faulty reports.
    </p>
</div>

<div class="all-status-summary-grid">
    <div class="all-status-summary-card">
        <p class="all-status-summary-label is-processing">Processing</p>
        <p class="all-status-summary-value">{{ $statusSummary['processing'] }}</p>
    </div>
    <div class="all-status-summary-card">
        <p class="all-status-summary-label is-approved">Approved</p>
        <p class="all-status-summary-value">{{ ($statusSummary['approved'] ?? 0) + ($statusSummary['ready'] ?? 0) }}</p>
    </div>
    <div class="all-status-summary-card">
        <p class="all-status-summary-label is-history">History</p>
        <p class="all-status-summary-value">{{ $statusSummary['history'] ?? 0 }}</p>
    </div>
    <div class="all-status-summary-card">
        <p class="all-status-summary-label is-rejected">Rejected</p>
        <p class="all-status-summary-value">{{ $statusSummary['rejected'] }}</p>
    </div>
</div>

{{-- Tabs Navigation --}}
<div class="status-tab-wrap inline-flex items-center rounded-2xl border border-stone-200 bg-stone-100/50 dark:bg-slate-800/50 dark:border-slate-700 p-1.5 shadow-sm">
    <button onclick="switchStatusTab('requests')" id="tab-btn-requests" class="status-tab-btn rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-200">
        <i class="fas fa-list-ul mr-2"></i> Walkie Requests
    </button>
    <button onclick="switchStatusTab('damages')" id="tab-btn-damages" class="status-tab-btn rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-200">
        <i class="fas fa-triangle-exclamation mr-2"></i> Faulty Reports
    </button>
</div>

{{-- Requests Section --}}
<div id="status-section-requests" class="status-section hidden">
    <div class="status-table-card bg-white rounded-[30px] shadow-sm border border-stone-100 p-6 dark:bg-slate-800/50 dark:border-slate-700/50">
        <table id="allRequestsTable" class="w-full text-left display nowrap">
            <thead class="bg-stone-50 text-stone-400 text-[9px] uppercase font-black tracking-[0.15em]">
                <tr>
                    <th class="px-4 py-4">Request</th>
                    <th class="px-4 py-4">Type</th>
                    <th class="px-4 py-4">Account Holder</th>
                    <th class="px-4 py-4">Department / Position</th>
                    <th class="px-4 py-4 text-center">Status</th>
                    <th class="px-4 py-4 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-50 text-[11px]">
                @foreach($requestStatuses as $request)
                    @php
                        $badge = $statusBadge($request);
                        $isSelfIssue = $isSelfRequest($request);
                        $isTemporaryRequest = $request->request_type === 'temporary_walkie_talkie';
                    @endphp
                    <tr class="hover:bg-[#FDFBF7] transition-colors">
                        <td class="px-4 py-3">
                            <div class="font-black text-slate-700 dark:text-slate-100">#{{ str_pad($request->id, 5, '0', STR_PAD_LEFT) }}</div>
                            <div class="all-status-muted mt-1">
                                {{ $request->request_date ? \Carbon\Carbon::parse($request->request_date)->format('d M Y') : '-' }}
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-col gap-1">
                                <span class="inline-flex rounded-full border px-2.5 py-1 text-[9px] font-black uppercase tracking-widest {{ $isSelfIssue ? 'bg-stone-100 text-stone-700 border-stone-200' : 'bg-[#F5EEE6] text-[#0284c7] border-[#E3D4C6]' }}">
                                    {{ $isSelfIssue ? 'Self-Issue' : 'On Behalf' }}
                                </span>
                                @if($isTemporaryRequest)
                                <span class="inline-flex rounded-full border border-violet-200 bg-violet-50 px-2.5 py-1 text-[9px] font-black uppercase tracking-widest text-violet-700">
                                    Temporary x{{ $request->quantity ?: 1 }}
                                </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 font-bold text-slate-700 dark:text-slate-100">{{ $request->full_name }}</td>
                        <td class="px-4 py-3">
                            <div class="text-slate-700 dark:text-slate-100">{{ $request->department ?: '-' }}</div>
                            @if($request->position)
                            <div class="all-status-muted mt-1">{{ $request->position }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex rounded-full border px-2.5 py-1 text-[9px] font-black uppercase tracking-widest {{ $badge['class'] }}">
                                {{ $badge['text'] }}
                            </span>
                            <div class="mt-2 min-w-[230px]">
                                @include('wt.partials.approval-flow', ['request' => $request, 'compact' => true])
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button type="button" onclick="openRequestStatusModal('requestStatusModal-{{ $request->id }}')" class="all-status-action-btn">
                                <i class="fa-solid fa-eye"></i>
                                View Form
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@foreach($requestStatuses as $request)
@php
    $badge = $statusBadge($request);
    $reportedBy = $resolveReportedBy($request);
    $requestor = $request->user
        ?: \App\Models\User::query()
            ->whereRaw('UPPER(full_name) = ?', [strtoupper((string) $request->full_name)])
            ->orWhereRaw('UPPER(username) = ?', [strtoupper((string) $request->full_name)])
            ->first();
    $requestorStaffId = $request->staff_id ?: ($requestor->staff_id ?? null);
    $isTemporaryRequest = $request->request_type === 'temporary_walkie_talkie';
    $requestDays = max(1, (int) ($request->duration_days ?: 1));
    $requestQty = max(1, (int) ($request->quantity ?: 1));
@endphp
<div id="requestStatusModal-{{ $request->id }}" class="request-status-panel mt-6 hidden">
    <div class="damage-status-modal w-full rounded-2xl shadow-xl">
        <div class="damage-status-modal-header relative px-6 py-5">
            <p class="damage-status-kicker text-[9px] font-black uppercase tracking-[0.2em]">Full Request Form</p>
            <div class="mt-2 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                <div>
                    <h3 class="damage-status-heading text-xl font-black">Request #{{ str_pad($request->id, 5, '0', STR_PAD_LEFT) }}</h3>
                    <p class="damage-status-subheading mt-1 text-xs font-bold uppercase">{{ $request->event_name ?: 'Walkie Talkie Request' }}</p>
                </div>
                <span class="inline-flex w-fit rounded-full border px-3 py-1.5 text-[9px] font-black uppercase tracking-widest {{ $badge['class'] }}">
                    {{ $badge['text'] }}
                </span>
            </div>
            <button type="button" onclick="closeRequestStatusModal('requestStatusModal-{{ $request->id }}')" class="damage-status-close absolute right-5 top-5">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="space-y-4 p-5">
            <div class="damage-status-section">
                <div class="damage-status-section-title">Submitted Details</div>
                <div class="damage-status-form-grid">
                    <div class="damage-status-detail">
                        <div class="damage-status-label">Reported By</div>
                        <div class="damage-status-value">{{ $reportedBy['name'] }}<br>{{ $reportedBy['role'] }}</div>
                    </div>
                    <div class="damage-status-detail">
                        <div class="damage-status-label">Submitted ID</div>
                        <div class="damage-status-value">{{ $reportedBy['staff_id'] }}</div>
                    </div>
                    <div class="damage-status-detail">
                        <div class="damage-status-label">Approval Status</div>
                        <div class="damage-status-value">{{ $request->status ?: '-' }}</div>
                    </div>
                    <div class="damage-status-detail">
                        <div class="damage-status-label">Request Type</div>
                        <div class="damage-status-value">{{ $isTemporaryRequest ? 'TEMPORARY WALKIE TALKIE' : 'WALKIE TALKIE' }}</div>
                    </div>
                </div>
            </div>

            <div class="damage-status-section">
                <div class="damage-status-section-title">Requestor Details</div>
                <div class="damage-status-form-grid">
                    <div class="damage-status-detail">
                        <div class="damage-status-label">Request For</div>
                        <div class="damage-status-value">{{ strtoupper($request->full_name ?: '-') }}</div>
                    </div>
                    <div class="damage-status-detail">
                        <div class="damage-status-label">Requestor ID</div>
                        <div class="damage-status-value">{{ strtoupper($requestorStaffId ?: '-') }}</div>
                    </div>
                    <div class="damage-status-detail">
                        <div class="damage-status-label">Department</div>
                        <div class="damage-status-value">{{ strtoupper($request->department ?: '-') }}</div>
                    </div>
                    <div class="damage-status-detail">
                        <div class="damage-status-label">Ownership Type</div>
                        <div class="damage-status-value">{{ strtoupper($request->ownership_type ?: 'INDIVIDUAL') }}</div>
                    </div>
                </div>
            </div>

            <div class="damage-status-section">
                <div class="damage-status-section-title">Request Details</div>
                <div class="damage-status-form-grid">
                    <div class="damage-status-detail">
                        <div class="damage-status-label">{{ $isTemporaryRequest ? 'Start Date' : 'Request Date' }}</div>
                        <div class="damage-status-value">{{ $request->request_date ? \Carbon\Carbon::parse($request->request_date)->format('d M Y') : '-' }}</div>
                    </div>
                    <div class="damage-status-detail">
                        <div class="damage-status-label">End Date</div>
                        <div class="damage-status-value">{{ $isTemporaryRequest && $request->end_date ? \Carbon\Carbon::parse($request->end_date)->format('d M Y') : '-' }}</div>
                    </div>
                    <div class="damage-status-detail">
                        <div class="damage-status-label">Duration / Quantity</div>
                        <div class="damage-status-value">{{ $isTemporaryRequest ? ($requestDays . ' ' . \Illuminate\Support\Str::plural('day', $requestDays) . ', ' . $requestQty . ' ' . \Illuminate\Support\Str::plural('unit', $requestQty)) : ($requestQty . ' ' . \Illuminate\Support\Str::plural('unit', $requestQty)) }}</div>
                    </div>
                    <div class="damage-status-detail">
                        <div class="damage-status-label">Location</div>
                        <div class="damage-status-value">{{ strtoupper($request->location ?: '-') }}</div>
                    </div>
                </div>
            </div>

            <div class="damage-status-section">
                <div class="damage-status-section-title">Event & ICT Handling</div>
                <div class="space-y-3 p-3">
                    <div class="damage-status-detail">
                        <div class="damage-status-label">Event / Purpose</div>
                        <div class="damage-status-value">{{ $request->event_name ?: '-' }}</div>
                    </div>
                    <div class="damage-status-detail">
                        <div class="damage-status-label">Justification</div>
                        <div class="damage-status-value whitespace-pre-line">{{ $request->justifications ?: '-' }}</div>
                    </div>
                    <div class="damage-status-detail">
                        <div class="damage-status-label">Requested / Assigned Accessories</div>
                        <div class="damage-status-value">{{ $request->accessories ?: 'To be selected by ICT' }}</div>
                    </div>
                    <div class="damage-status-detail">
                        <div class="damage-status-label">Executive Signature</div>
                        <div class="damage-status-value">
                            @if($request->request_signature)
                                <img src="{{ $request->request_signature }}" alt="Executive signature" class="max-h-24 rounded-lg border border-stone-200 bg-white p-2 dark:border-slate-700">
                            @else
                                No signature captured.
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @include('wt.partials.ict-update-section', ['request' => $request, 'prefix' => 'damage-status'])
        </div>
    </div>
</div>
@endforeach

{{-- Damages Section --}}
<div id="status-section-damages" class="status-section hidden">
    <div class="status-table-card bg-white rounded-[30px] shadow-sm border border-stone-100 p-6 dark:bg-slate-800/50 dark:border-slate-700/50">
        <table id="allDamagesTable" class="w-full text-left display nowrap">
            <thead class="bg-stone-50 text-stone-400 text-[9px] uppercase font-black tracking-[0.15em]">
                <tr>
                    <th class="px-4 py-4">Report ID</th>
                    <th class="px-4 py-4">Radio ID / Model</th>
                    <th class="px-4 py-4">Reporter</th>
                    <th class="px-4 py-4">Problem</th>
                    <th class="px-4 py-4">ICT Remark</th>
                    <th class="px-4 py-4 text-center">Status</th>
                    <th class="px-4 py-4 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-50 text-[11px]">
                @foreach($damageRecords as $dr)
                    @php $badge = $damageBadge($dr); @endphp
                    <tr class="hover:bg-[#FDFBF7] transition-colors">
                        <td class="px-4 py-3">
                            <div class="font-black text-slate-700 dark:text-slate-100">#{{ str_pad($dr->maintenance_id, 4, '0', STR_PAD_LEFT) }}</div>
                            <div class="all-status-muted mt-1">{{ $dr->received_date ? \Carbon\Carbon::parse($dr->received_date)->format('d M Y') : '-' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-black text-sky-700 dark:text-sky-300">{{ $dr->radio_id ?: '-' }}</div>
                            <div class="mt-1 text-[9px] uppercase tracking-widest text-stone-400">{{ $dr->model ?: '-' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-bold text-slate-700 dark:text-slate-100">{{ $dr->reporter_name }}</div>
                            <div class="mt-1 text-[9px] uppercase tracking-widest text-stone-400">{{ $dr->reporter_staff_id }}</div>
                        </td>
                        <td class="px-4 py-3" title="{{ $dr->problem_possible }}">
                            <div class="truncate">{{ $dr->problem_possible ?: 'No details' }}</div>
                        </td>
                        <td class="px-4 py-3" title="{{ $dr->remarks }}">
                            <div class="truncate">{{ $dr->remarks ?: '-' }}</div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex rounded-full border px-2.5 py-1 text-[9px] font-black uppercase tracking-widest {{ $badge['class'] }}">
                                {{ $badge['text'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button type="button" onclick="openDamageStatusModal('damageStatusModal-{{ $dr->maintenance_id }}')" class="all-status-action-btn">
                                View Form
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@foreach($damageRecords as $dr)
@php
    $badge = $damageBadge($dr);
    $evidenceFiles = [];
    if (! empty($dr->evidence_paths)) {
        $evidenceFiles = is_array($dr->evidence_paths)
            ? $dr->evidence_paths
            : (json_decode($dr->evidence_paths, true) ?: []);
    }
@endphp
<div id="damageStatusModal-{{ $dr->maintenance_id }}" class="damage-status-panel mt-6 hidden">
    <div class="damage-status-modal w-full rounded-2xl shadow-xl">
        <div class="damage-status-modal-header relative px-6 py-5">
            <p class="damage-status-kicker text-[9px] font-black uppercase tracking-[0.2em]">Full Faulty Report Form</p>
            <div class="mt-2 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                <div>
                    <h3 class="damage-status-heading text-xl font-black">Damage Report #{{ str_pad($dr->maintenance_id, 4, '0', STR_PAD_LEFT) }}</h3>
                    <p class="damage-status-subheading mt-1 text-xs font-bold uppercase">{{ $dr->reporter_name ?: '-' }} / {{ $dr->department_name ?: '-' }}</p>
                </div>
                <span class="inline-flex w-fit rounded-full border px-3 py-1.5 text-[9px] font-black uppercase tracking-widest {{ $badge['class'] }}">
                    {{ $badge['text'] }}
                </span>
            </div>
            <button type="button" onclick="closeDamageStatusModal('damageStatusModal-{{ $dr->maintenance_id }}')" class="damage-status-close absolute right-5 top-5">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="space-y-4 p-5">
            <div class="damage-status-section">
                <div class="damage-status-section-title">Reporter Details</div>
                <div class="damage-status-form-grid">
                    <div class="damage-status-detail">
                    <div class="damage-status-label">Reporter</div>
                    <div class="damage-status-value">{{ strtoupper($dr->reporter_name ?: '-') }}</div>
                    </div>
                    <div class="damage-status-detail">
                    <div class="damage-status-label">Department</div>
                    <div class="damage-status-value">{{ strtoupper($dr->department_name ?: '-') }}</div>
                    </div>
                    <div class="damage-status-detail">
                    <div class="damage-status-label">Phone</div>
                    <div class="damage-status-value">{{ $dr->phone_no ?: '-' }}</div>
                    </div>
                    <div class="damage-status-detail">
                    <div class="damage-status-label">Submitted Date</div>
                    <div class="damage-status-value">{{ $dr->received_date ? \Carbon\Carbon::parse($dr->received_date)->format('d M Y') : '-' }}</div>
                    </div>
                </div>
            </div>

            <div class="damage-status-section">
                <div class="damage-status-section-title">Walkie Details</div>
                <div class="damage-status-form-grid">
                    <div class="damage-status-detail">
                    <div class="damage-status-label">Radio ID</div>
                    <div class="damage-status-value">{{ strtoupper($dr->radio_id ?: '-') }}</div>
                    </div>
                    <div class="damage-status-detail">
                    <div class="damage-status-label">Serial / Model</div>
                    <div class="damage-status-value">{{ strtoupper($dr->serial_number ?: '-') }} / {{ strtoupper($dr->model ?: '-') }}</div>
                    </div>
                    <div class="damage-status-detail">
                    <div class="damage-status-label">Ownership Type</div>
                    <div class="damage-status-value">{{ strtoupper($dr->ownership_type ?: '-') }}</div>
                    </div>
                    <div class="damage-status-detail">
                    <div class="damage-status-label">Location</div>
                    <div class="damage-status-value">{{ strtoupper($dr->location ?: '-') }}</div>
                    </div>
                </div>
            </div>

            <div class="damage-status-section">
                <div class="damage-status-section-title">Issue & ICT Update</div>
                <div class="space-y-3 p-3">
                    <div class="damage-status-detail">
                    <div class="damage-status-label">Problem Reported</div>
                    <div class="damage-status-value whitespace-pre-line">{{ $dr->problem_possible ?: ($dr->issue ?: ($dr->issue_description ?: '-')) }}</div>
                    </div>
                    <div class="damage-status-detail">
                    <div class="damage-status-label">Remark From ICT</div>
                    <div class="damage-status-value whitespace-pre-line">{{ $dr->remarks ?: 'No ICT remark yet.' }}</div>
                    </div>
                </div>
            </div>

            <div class="damage-status-section">
                <div class="damage-status-section-title">Evidence</div>
                <div class="p-3">
                    <div class="damage-status-detail">
                    <div class="damage-status-label">Evidence</div>
                    <div class="damage-status-value">
                        @if(!empty($evidenceFiles))
                            <div class="flex flex-wrap gap-2">
                                @foreach($evidenceFiles as $path)
                                <a href="{{ asset('storage/' . $path) }}" target="_blank" rel="noopener noreferrer" class="rounded-lg border border-sky-400/30 px-3 py-2 text-[9px] font-black uppercase tracking-widest text-sky-300 hover:bg-sky-500/10">
                                    Evidence {{ $loop->iteration }}
                                </a>
                                @endforeach
                            </div>
                        @else
                            No evidence uploaded.
                        @endif
                    </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="button" onclick="closeDamageStatusModal('damageStatusModal-{{ $dr->maintenance_id }}')" class="rounded-lg border border-slate-500 bg-slate-800 px-5 py-3 text-[10px] font-black uppercase tracking-widest text-slate-100 hover:bg-slate-700">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach

</div>

@push('styles')
<style>
    .status-tab-btn.active {
        background-color: #0284c7;
        color: white;
        box-shadow: 0 4px 12px rgba(139, 94, 60, 0.2);
    }
    .status-tab-btn:not(.active) {
        color: #64748B;
    }
    .status-tab-btn:not(.active):hover {
        background-color: rgba(139, 94, 60, 0.1);
        color: #0284c7;
    }
    .dark .status-tab-btn:not(.active) {
        color: #94A3B8;
    }
    .dark .status-tab-btn:not(.active):hover {
        background-color: rgba(255, 255, 255, 0.05);
        color: white;
    }
    .damage-status-modal {
        display: flex;
        flex-direction: column;
        border: 1px solid #dbe3ee;
        background: #ffffff;
        color: #172033;
        scrollbar-color: #cbd5e1 #f8fafc;
    }
    .damage-status-modal-header {
        border-bottom: 1px solid #e2e8f0;
        background: linear-gradient(180deg, #ffffff, #f8fafc);
    }
    .damage-status-kicker {
        color: #64748b;
    }
    .damage-status-heading {
        color: #172033;
    }
    .damage-status-subheading {
        color: #64748b;
    }
    .damage-status-close {
        color: #94a3b8;
    }
    .damage-status-close:hover {
        color: #334155;
    }
    .damage-status-modal > .space-y-4 {
        flex: 1;
        background: #f8fafc;
    }
    .damage-status-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        padding: 12px;
    }
    .damage-status-section {
        overflow: hidden;
        border: 1px solid #dbe3ee;
        border-radius: 14px;
        background: #ffffff;
    }
    .damage-status-section-title {
        border-bottom: 1px solid #e2e8f0;
        background: #f1f5f9;
        padding: 10px 14px;
        color: #334155;
        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.16em;
        text-transform: uppercase;
    }
    .damage-status-detail {
        min-height: 64px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #ffffff;
        padding: 12px 14px;
    }
    .damage-status-label {
        margin-bottom: 6px;
        color: #64748b;
        font-size: 8px;
        font-weight: 900;
        letter-spacing: 0.16em;
        text-transform: uppercase;
    }
    .damage-status-value {
        color: #172033;
        font-size: 11px;
        font-weight: 800;
        line-height: 1.45;
        word-break: break-word;
    }
    .dark .damage-status-modal {
        border-color: #334155;
        background: #0f172a;
        color: #e2e8f0;
        scrollbar-color: #475569 #0f172a;
    }
    .dark .damage-status-modal-header {
        border-bottom-color: #334155;
        background: #172033;
    }
    .dark .damage-status-kicker {
        color: #94a3b8;
    }
    .dark .damage-status-heading {
        color: #f8fafc;
    }
    .dark .damage-status-subheading {
        color: #cbd5e1;
    }
    .dark .damage-status-close {
        color: #94a3b8;
    }
    .dark .damage-status-close:hover {
        color: #ffffff;
    }
    .dark .damage-status-modal > .space-y-4 {
        background: #0f172a;
    }
    .dark .damage-status-section {
        border-color: #334155;
        background: #0f172a;
    }
    .dark .damage-status-section-title {
        border-bottom-color: #334155;
        background: #1e293b;
        color: #bfdbfe;
    }
    .dark .damage-status-detail {
        border-color: #334155;
        background: rgba(15, 23, 42, 0.72);
    }
    .dark .damage-status-label {
        color: #94a3b8;
    }
    .dark .damage-status-value {
        color: #e2e8f0;
    }
    @media (max-width: 768px) {
        .damage-status-form-grid {
            grid-template-columns: 1fr;
        }
        .damage-status-detail,
        .damage-status-detail:nth-child(2n) {
            border-right: 0;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function openDamageStatusModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            document.querySelectorAll('.damage-status-panel').forEach(panel => {
                if (panel.id !== modalId) {
                    panel.classList.add('hidden');
                }
            });
            modal.classList.remove('hidden');
            modal.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function closeDamageStatusModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    function openRequestStatusModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            document.querySelectorAll('.request-status-panel').forEach(panel => {
                if (panel.id !== modalId) {
                    panel.classList.add('hidden');
                }
            });
            modal.classList.remove('hidden');
            modal.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function closeRequestStatusModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    function switchStatusTab(tabId) {
        if (!['requests', 'damages'].includes(tabId)) {
            tabId = 'requests';
        }

        // Hide all sections
        $('.status-section').addClass('hidden');
        // Deactivate all buttons
        $('.status-tab-btn').removeClass('active');
        
        // Show current section
        $('#status-section-' + tabId).removeClass('hidden');
        // Activate current button
        $('#tab-btn-' + tabId).addClass('active');
        setTimeout(function () {
            const tableSelector = tabId === 'damages' ? '#allDamagesTable' : '#allRequestsTable';
            if ($.fn.DataTable && $.fn.DataTable.isDataTable(tableSelector)) {
                $(tableSelector).DataTable().columns.adjust();
            }
        }, 0);
        
        // Save to URL if needed or just keep local
        localStorage.setItem('last_status_tab', tabId);
    }

    $(document).ready(function() {
        // Initial tab
        const requestedTab = @json(request()->query('view'));
        const lastTab = requestedTab || localStorage.getItem('last_status_tab') || '{{ $viewMode }}';
        switchStatusTab(lastTab);

        // Initialize DataTables
        const tableOptions = {
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
            ordering: true,
            order: [[0, 'desc']],
            language: {
                search: "",
                searchPlaceholder: "Search records...",
                emptyTable: "No records found.",
                zeroRecords: "No matching records found."
            },
            autoWidth: false,
            scrollX: true,
            responsive: false
        };

        const reqTable = $('#allRequestsTable').DataTable({
            ...tableOptions,
            columnDefs: [
                { targets: -1, orderable: false, searchable: false }
            ]
        });
        const damTable = $('#allDamagesTable').DataTable({
            ...tableOptions,
            columnDefs: [
                { targets: 0, width: '100px' },
                { targets: 1, width: '165px' },
                { targets: 2, width: '155px' },
                { targets: 3, width: '205px' },
                { targets: 4, width: '220px' },
                { targets: 5, width: '130px' },
                { targets: 6, width: '110px', orderable: false, searchable: false }
            ]
        });

        mountAdminTableFooter(reqTable);
        mountAdminTableFooter(damTable);
        setTimeout(function () {
            reqTable.columns.adjust();
            damTable.columns.adjust();
        }, 0);
    });
</script>
@endpush
@endsection
