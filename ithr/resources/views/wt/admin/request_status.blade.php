@extends('wt.layouts.admin')

@section('title', 'Request Status')

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
    .status-color-badge.status-approved {
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
    .request-status-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-height: 32px;
        border-radius: 10px;
        border: 1px solid rgba(56, 189, 248, 0.38);
        background: rgba(14, 165, 233, 0.12);
        color: #0369a1;
        padding: 8px 12px;
        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        transition: background-color 0.18s ease, border-color 0.18s ease, transform 0.18s ease;
        white-space: nowrap;
    }
    .request-status-action-btn:hover {
        background: rgba(14, 165, 233, 0.2);
        border-color: rgba(14, 165, 233, 0.62);
        transform: translateY(-1px);
    }
    .request-status-table-card {
        overflow-x: auto;
    }
    .request-status-table-card .dataTables_wrapper,
    .request-status-table-card table.dataTable {
        width: 100% !important;
    }
    .request-status-table-card table.dataTable {
        min-width: 1120px;
    }
    .request-status-action-cell {
        min-width: 128px;
    }
    .dark .request-status-action-btn {
        border-color: rgba(125, 211, 252, 0.35);
        background: rgba(14, 165, 233, 0.14);
        color: #bae6fd;
    }
    .request-form-sheet {
        background: #f8fafc;
    }
    .request-form-header {
        background: #0f172a;
        border-bottom: 1px solid rgba(148, 163, 184, 0.28);
    }
    .request-form-kicker {
        color: #dbeafe;
    }
    .request-form-title {
        color: #ffffff !important;
        text-shadow: 0 1px 1px rgba(15, 23, 42, 0.45);
    }
    .request-form-subtitle {
        color: #bfdbfe !important;
    }
    .request-form-section {
        background: #ffffff;
        border: 1px solid #dbe3ee;
        border-radius: 8px;
        overflow: hidden;
    }
    .request-form-section-title {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 11px 14px;
        border-bottom: 1px solid #e2e8f0;
        background: #f1f5f9;
        color: #334155;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.13em;
        text-transform: uppercase;
    }
    .request-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .request-form-field {
        min-height: 68px;
        padding: 13px 14px;
        border-right: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
    }
    .request-form-field:nth-child(2n) {
        border-right: 0;
    }
    .request-form-field-wide {
        grid-column: 1 / -1;
        border-right: 0;
    }
    .request-form-label {
        margin-bottom: 6px;
        color: #64748b;
        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }
    .request-form-value {
        color: #0f172a;
        font-size: 12px;
        font-weight: 800;
        line-height: 1.45;
        word-break: break-word;
    }
    .dark .request-form-sheet {
        background: #0f172a;
    }
    .dark .request-form-section {
        background: #111827;
        border-color: #334155;
    }
    .dark .request-form-section-title {
        background: #1e293b;
        border-color: #334155;
        color: #cbd5e1;
    }
    .dark .request-form-field {
        border-color: #334155;
    }
    .dark .request-form-label {
        color: #94a3b8;
    }
    .dark .request-form-value {
        color: #e2e8f0;
    }
    @media (max-width: 640px) {
        .request-form-grid {
            grid-template-columns: 1fr;
        }
        .request-form-field {
            border-right: 0;
        }
    }
</style>
@endpush

@section('content')
@php
    $isSelfRequest = function ($request) {
        $user = auth('wt')->user();

        return (int) ($request->user_id ?? 0) === (int) $user->user_id
            && strtoupper(trim((string) ($request->staff_id ?? ''))) === strtoupper(trim((string) ($user->staff_id ?? '')))
            && strtoupper(trim((string) ($request->full_name ?? ''))) === strtoupper(trim((string) (($user->full_name ?: $user->username) ?? '')));
    };

    $statusBadge = function ($request) {
        return match ($request->status) {
            'Draft' => ['text' => 'Draft', 'class' => 'status-color-badge status-draft'],
            'Pending Admin Approval', 'Pending IT Approval' => ['text' => 'Processing', 'class' => 'status-color-badge status-processing'],
            'Pending Executive Pickup' => ['text' => 'Ready To Collect', 'class' => 'status-color-badge status-ready'],
            'Approved' => ['text' => 'Approved', 'class' => 'status-color-badge status-approved'],
            'Rejected' => ['text' => 'Rejected', 'class' => 'status-color-badge status-rejected'],
            default => ['text' => $request->status ?: 'Unknown', 'class' => 'status-color-badge status-unknown'],
        };
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

<div class="page-header-block">
    <h3 class="page-title-standard">Request Status</h3>
    <p class="page-subtitle-standard">
        Track the current status of all walkie talkie requests you submitted for yourself or on behalf of recipients.
    </p>
</div>

<div class="grid gap-4 md:grid-cols-5 mb-5">
    <div class="bg-white rounded-3xl border border-stone-200 shadow-sm p-5 dark:bg-slate-800/60 dark:border-slate-700">
        <p class="text-[10px] font-black uppercase tracking-[0.18em] text-stone-600">Draft</p>
        <p class="mt-3 text-3xl font-black text-slate-800 dark:text-slate-100">{{ $statusSummary['draft'] }}</p>
    </div>
    <div class="bg-white rounded-3xl border border-amber-100 shadow-sm p-5 dark:bg-slate-800/60 dark:border-slate-700">
        <p class="text-[10px] font-black uppercase tracking-[0.18em] text-amber-600">Processing</p>
        <p class="mt-3 text-3xl font-black text-slate-800 dark:text-slate-100">{{ $statusSummary['processing'] }}</p>
    </div>
    <div class="bg-white rounded-3xl border border-sky-100 shadow-sm p-5 dark:bg-slate-800/60 dark:border-slate-700">
        <p class="text-[10px] font-black uppercase tracking-[0.18em] text-sky-600">Ready To Collect</p>
        <p class="mt-3 text-3xl font-black text-slate-800 dark:text-slate-100">{{ $statusSummary['ready'] }}</p>
    </div>
    <div class="bg-white rounded-3xl border border-emerald-100 shadow-sm p-5 dark:bg-slate-800/60 dark:border-slate-700">
        <p class="text-[10px] font-black uppercase tracking-[0.18em] text-emerald-600">Approved</p>
        <p class="mt-3 text-3xl font-black text-slate-800 dark:text-slate-100">{{ $statusSummary['approved'] }}</p>
    </div>
    <div class="bg-white rounded-3xl border border-red-100 shadow-sm p-5 dark:bg-slate-800/60 dark:border-slate-700">
        <p class="text-[10px] font-black uppercase tracking-[0.18em] text-red-600">Rejected</p>
        <p class="mt-3 text-3xl font-black text-slate-800 dark:text-slate-100">{{ $statusSummary['rejected'] }}</p>
    </div>
</div>

<div class="request-status-table-card bg-white rounded-[30px] shadow-sm border border-stone-100 p-6 dark:bg-slate-800/50 dark:border-slate-700/50">
    <table id="managerRequestStatusTable" class="w-full text-left display nowrap">
        <thead class="bg-stone-50 text-stone-400 text-[9px] uppercase font-black tracking-[0.15em]">
            <tr>
                <th class="px-4 py-4">Request</th>
                <th class="px-4 py-4">Type</th>
                <th class="px-4 py-4">Account Holder</th>
                <th class="px-4 py-4">Department / Position</th>
                <th class="px-4 py-4 text-center">Status</th>
                <th class="px-4 py-4 text-center request-status-action-cell">Action</th>
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
                        <div class="mt-1 text-[9px] uppercase tracking-widest text-stone-400">
                            {{ $request->request_date ? \Carbon\Carbon::parse($request->request_date)->format('d M Y') : '-' }}
                            @if($isTemporaryRequest)
                                - {{ $request->end_date ? \Carbon\Carbon::parse($request->end_date)->format('d M Y') : '-' }}
                            @endif
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
                        <div class="mt-1 text-[9px] uppercase tracking-widest text-stone-400">{{ $request->position ?: '-' }}</div>
                        @if($isTemporaryRequest)
                        <div class="mt-1 text-[9px] uppercase tracking-widest text-violet-500">{{ $request->event_name ?: 'No purpose stated' }}</div>
                        @php $statusDays = max(1, (int) ($request->duration_days ?: 1)); @endphp
                        <div class="mt-1 text-[9px] uppercase tracking-widest text-violet-400">{{ $statusDays }} {{ \Illuminate\Support\Str::plural('day', $statusDays) }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center request-status-action-cell">
                        <span class="inline-flex rounded-full border px-2.5 py-1 text-[9px] font-black uppercase tracking-widest {{ $badge['class'] }}">
                            {{ $badge['text'] }}
                        </span>
                        <div class="mt-3 min-w-[260px]">
                            @include('wt.partials.approval-flow', ['request' => $request, 'compact' => true])
                        </div>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button type="button" onclick="openRequestStatusFormModal('requestStatusFormModal-{{ $request->id }}')" class="request-status-action-btn">
                            <i class="fa-solid fa-eye"></i>
                            View Form
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@foreach($requestStatuses as $request)
@php
    $isTemporaryRequest = $request->request_type === 'temporary_walkie_talkie';
    $reportedBy = $resolveReportedBy($request);
    $requestDays = max(1, (int) ($request->duration_days ?: 1));
    $requestQty = max(1, (int) ($request->quantity ?: 1));
@endphp
<div id="requestStatusFormModal-{{ $request->id }}" class="fixed inset-0 bg-stone-900/60 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4" onclick="if (event.target === this) closeRequestStatusFormModal('requestStatusFormModal-{{ $request->id }}')">
    <div class="request-form-sheet w-full max-w-4xl max-h-[92vh] overflow-y-auto rounded-xl shadow-2xl border border-slate-300 dark:border-slate-700">
        <div class="request-form-header px-6 py-5 relative">
            <p class="request-form-kicker text-[9px] font-black uppercase tracking-[0.2em]">Full Request Form</p>
            <div class="request-form-title text-xl font-black tracking-tight">Request #{{ str_pad($request->id, 5, '0', STR_PAD_LEFT) }}</div>
            <p class="request-form-subtitle mt-1 text-xs font-bold">{{ $request->event_name ?: 'Walkie Talkie Request' }}</p>
            <button type="button" onclick="closeRequestStatusFormModal('requestStatusFormModal-{{ $request->id }}')" class="absolute top-5 right-5 text-white/60 hover:text-white">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="p-5 md:p-6 space-y-4">
            <div class="request-form-section">
                <div class="request-form-section-title"><i class="fa-solid fa-user-check"></i> Submitted Details</div>
                <div class="request-form-grid">
                    <div class="request-form-field">
                        <div class="request-form-label">Reported By</div>
                        <div class="request-form-value">
                            {{ $reportedBy['name'] }}
                            <div class="mt-1 text-[10px] font-black uppercase tracking-widest text-[#0284c7]">{{ $reportedBy['role'] }}</div>
                        </div>
                    </div>
                    <div class="request-form-field">
                        <div class="request-form-label">Submitted ID</div>
                        <div class="request-form-value">{{ $reportedBy['staff_id'] }}</div>
                    </div>
                    <div class="request-form-field request-form-field-wide">
                        <div class="request-form-label">Approval Status</div>
                        <div class="request-form-value">{{ $request->status ?: '-' }}</div>
                    </div>
                </div>
            </div>

            <div class="request-form-section">
                <div class="request-form-section-title"><i class="fa-solid fa-id-card"></i> Requestor Details</div>
                <div class="request-form-grid">
                    <div class="request-form-field">
                        <div class="request-form-label">Request For</div>
                        <div class="request-form-value">{{ strtoupper($request->full_name ?: '-') }}</div>
                    </div>
                    <div class="request-form-field">
                        <div class="request-form-label">Requestor ID</div>
                        <div class="request-form-value">{{ strtoupper($request->staff_id ?: '-') }}</div>
                    </div>
                    <div class="request-form-field">
                        <div class="request-form-label">Department</div>
                        <div class="request-form-value">{{ strtoupper($request->department ?: '-') }}</div>
                    </div>
                    <div class="request-form-field">
                        <div class="request-form-label">Ownership Type</div>
                        <div class="request-form-value">{{ strtoupper($request->ownership_type ?: 'INDIVIDUAL') }}</div>
                    </div>
                </div>
            </div>

            <div class="request-form-section">
                <div class="request-form-section-title"><i class="fa-solid fa-calendar-days"></i> Request Details</div>
                <div class="request-form-grid">
                    <div class="request-form-field">
                        <div class="request-form-label">Request Type</div>
                        <div class="request-form-value">{{ $isTemporaryRequest ? 'TEMPORARY WALKIE TALKIE' : 'WALKIE TALKIE' }}</div>
                    </div>
                    <div class="request-form-field">
                        <div class="request-form-label">{{ $isTemporaryRequest ? 'Start Date' : 'Request Date' }}</div>
                        <div class="request-form-value">{{ $request->request_date ? \Carbon\Carbon::parse($request->request_date)->format('d M Y') : '-' }}</div>
                    </div>
                    <div class="request-form-field">
                        <div class="request-form-label">End Date</div>
                        <div class="request-form-value">{{ $isTemporaryRequest && $request->end_date ? \Carbon\Carbon::parse($request->end_date)->format('d M Y') : '-' }}</div>
                    </div>
                    <div class="request-form-field">
                        <div class="request-form-label">Duration / Quantity</div>
                        <div class="request-form-value">{{ $isTemporaryRequest ? ($requestDays . ' ' . \Illuminate\Support\Str::plural('day', $requestDays) . ', ' . $requestQty . ' ' . \Illuminate\Support\Str::plural('unit', $requestQty)) : ($requestQty . ' ' . \Illuminate\Support\Str::plural('unit', $requestQty)) }}</div>
                    </div>
                    <div class="request-form-field">
                        <div class="request-form-label">Bay From</div>
                        <div class="request-form-value">{{ strtoupper($request->bay_from ?: '-') }}</div>
                    </div>
                    <div class="request-form-field">
                        <div class="request-form-label">Sector / Location</div>
                        <div class="request-form-value">{{ strtoupper($request->sector ?: '-') }} / {{ strtoupper($request->location ?: '-') }}</div>
                    </div>
                </div>
            </div>

            @include('wt.partials.pickup-preference-section', ['request' => $request, 'prefix' => 'request-form'])

            <div class="request-form-section">
                <div class="request-form-section-title"><i class="fa-solid fa-clipboard-list"></i> Event & ICT Handling</div>
                <div class="request-form-grid">
                    <div class="request-form-field request-form-field-wide">
                        <div class="request-form-label">Event / Purpose</div>
                        <div class="request-form-value">{{ $request->event_name ?: '-' }}</div>
                    </div>
                    <div class="request-form-field request-form-field-wide">
                        <div class="request-form-label">Justification</div>
                        <div class="request-form-value whitespace-pre-line">{{ $request->justifications ?: '-' }}</div>
                    </div>
                    <div class="request-form-field request-form-field-wide">
                        <div class="request-form-label">Requested / Assigned Accessories</div>
                        <div class="request-form-value">{{ $request->accessories ?: 'To be selected by ICT' }}</div>
                    </div>
                    <div class="request-form-field">
                        <div class="request-form-label">Assigned Radio ID</div>
                        <div class="request-form-value">{{ strtoupper($request->radio_id ?: '-') }}</div>
                    </div>
                    <div class="request-form-field">
                        <div class="request-form-label">Assigned Serial</div>
                        <div class="request-form-value">{{ strtoupper($request->assigned_serial_number ?: '-') }}</div>
                    </div>
                </div>
            </div>

            @include('wt.partials.ict-update-section', ['request' => $request, 'prefix' => 'request-form'])

            <div class="mt-5 flex justify-end">
                <button type="button" onclick="closeRequestStatusFormModal('requestStatusFormModal-{{ $request->id }}')" class="request-status-action-btn">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@push('scripts')
<script>
    function openRequestStatusFormModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            if (modal.parentElement !== document.body) {
                document.body.appendChild(modal);
            }

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeRequestStatusFormModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('#managerRequestStatusTable')) {
            $('#managerRequestStatusTable').DataTable().destroy();
        }

        const managerRequestStatusTable = $('#managerRequestStatusTable').DataTable({
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
            ordering: true,
            order: [[0, 'desc']],
            language: {
                search: "",
                searchPlaceholder: "Search request status...",
                emptyTable: "No request status records found.",
                zeroRecords: "No matching request status records found."
            },
            columnDefs: [
                { targets: -1, orderable: false, searchable: false }
            ],
            scrollX: true,
            responsive: false,
            autoWidth: false
        });

        mountAdminTableFooter(managerRequestStatusTable);
    });
</script>
@endpush
@endsection


