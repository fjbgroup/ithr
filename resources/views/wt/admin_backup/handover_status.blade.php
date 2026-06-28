@extends('wt.layouts.admin')

@section('title', 'Handover Status WT')

@push('styles')
<style>
    .handover-language-toggle {
        display: inline-grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        align-items: center;
        gap: 0;
        padding: 0;
        width: min(100%, 280px);
        border-radius: 999px;
        border: 1px solid rgba(148, 163, 184, 0.22);
        background: rgba(255, 255, 255, 0.78);
        backdrop-filter: blur(8px);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.72);
    }
    .handover-language-btn {
        border: 0;
        border-radius: 0;
        width: 100%;
        padding: 9px 12px;
        min-width: 0;
        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.09em;
        text-transform: uppercase;
        color: #64748b;
        background: transparent;
        line-height: 1;
        transition: all 0.18s ease;
    }
    .handover-language-btn:first-child {
        border-top-left-radius: 999px;
        border-bottom-left-radius: 999px;
    }
    .handover-language-btn:last-child {
        border-top-right-radius: 999px;
        border-bottom-right-radius: 999px;
    }
    .handover-language-btn.is-active {
        color: #ffffff;
        background: linear-gradient(135deg, #0284c7, #6f4a31);
        box-shadow: 0 8px 18px rgba(111, 74, 49, 0.22);
    }
    .handover-language-btn:not(.is-active):hover {
        color: #5b3f2b;
        background: rgba(255, 250, 245, 0.96);
    }
    .handover-terms-panel {
        border: 1px solid rgba(139, 94, 60, 0.12);
        background:
            radial-gradient(circle at top right, rgba(251, 191, 36, 0.10), transparent 26%),
            linear-gradient(135deg, rgba(255, 250, 245, 0.98), rgba(248, 250, 252, 0.98));
    }
    .handover-terms-copy {
        display: none;
    }
    .handover-terms-copy.is-active {
        display: block;
    }
    .handover-terms-list {
        display: grid;
        gap: 12px;
    }
    .handover-terms-item {
        border-radius: 16px;
        border: 1px solid rgba(148, 163, 184, 0.16);
        background: rgba(255, 255, 255, 0.86);
        padding: 14px 16px;
    }
    .handover-terms-item strong {
        color: #0284c7;
    }
    .handover-terms-sublist {
        margin-top: 8px;
        padding-left: 18px;
        list-style: lower-roman;
    }
    .handover-terms-sublist li + li {
        margin-top: 4px;
    }
    .dark .handover-language-toggle {
        background: rgba(15, 23, 42, 0.78);
        border-color: rgba(71, 85, 105, 0.7);
        box-shadow: inset 0 1px 0 rgba(148, 163, 184, 0.08);
    }
    .dark .handover-language-btn {
        color: #94a3b8;
    }
    .dark .handover-language-btn.is-active {
        color: #ffffff;
        background: linear-gradient(135deg, #0284c7, #a16207);
        box-shadow: none;
    }
    .dark .handover-language-btn:not(.is-active):hover {
        color: #e2e8f0;
        background: rgba(30, 41, 59, 0.9);
    }
    .dark .handover-terms-panel {
        border-color: #334155;
        background:
            radial-gradient(circle at top right, rgba(34, 197, 94, 0.12), transparent 26%),
            linear-gradient(135deg, rgba(15, 23, 42, 0.98), rgba(30, 41, 59, 0.98));
    }
    .dark .handover-terms-item {
        border-color: rgba(148, 163, 184, 0.16);
        background: rgba(15, 23, 42, 0.9);
    }
    .dark .handover-terms-item strong {
        color: #fbbf24;
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

    $badgeMap = function ($request) {
        if ($request->handover) {
            return ['text' => 'Collected', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-100'];
        }

        return match ($request->status) {
            'Pending Admin Approval', 'Pending IT Approval' => ['text' => 'Processing', 'class' => 'bg-amber-50 text-amber-700 border-amber-100'],
            'Pending Executive Pickup', 'Approved' => ['text' => 'Ready To Collect', 'class' => 'bg-sky-50 text-sky-700 border-sky-100'],
            'Rejected' => ['text' => 'Rejected', 'class' => 'bg-red-50 text-red-700 border-red-100'],
            default => ['text' => $request->status ?: 'Unknown', 'class' => 'bg-slate-50 text-slate-700 border-slate-100'],
        };
    };

    $recipientHandoverMap = function ($request) {
        if ($request->handover) {
            return ['text' => 'Confirmed By Recipient', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-100'];
        }

        if ($isSelfRequest($request)) {
            return ['text' => 'Self-Issue', 'class' => 'bg-stone-100 text-stone-700 border-stone-200'];
        }

        return match ($request->status) {
            'Pending Executive Pickup' => ['text' => 'Waiting Recipient Pickup', 'class' => 'bg-violet-50 text-violet-700 border-violet-100'],
            'Approved' => ['text' => 'Ready For Recipient Collection', 'class' => 'bg-sky-50 text-sky-700 border-sky-100'],
            'Rejected' => ['text' => 'Request Rejected', 'class' => 'bg-red-50 text-red-700 border-red-100'],
            default => ['text' => 'Pending ICT', 'class' => 'bg-amber-50 text-amber-700 border-amber-100'],
        };
    };
@endphp

<div class="page-header-block">
    <h3 class="page-title-standard">Handover Status WT</h3>
    <p class="page-subtitle-standard">
        Monitor self-issue and on behalf requests, including processing, ready to collect, and recipient handover progress.
    </p>
</div>

<div class="grid gap-4 md:grid-cols-3 mb-5">
    <div class="bg-white rounded-3xl border border-amber-100 shadow-sm p-5 dark:bg-slate-800/60 dark:border-slate-700">
        <p class="text-[10px] font-black uppercase tracking-[0.18em] text-amber-600">Processing</p>
        <p class="mt-3 text-3xl font-black text-slate-800 dark:text-slate-100">{{ $statusSummary['processing'] }}</p>
        <p class="mt-2 text-[11px] font-medium text-slate-500 dark:text-slate-400">Waiting for approval or ICT action.</p>
    </div>
    <div class="bg-white rounded-3xl border border-sky-100 shadow-sm p-5 dark:bg-slate-800/60 dark:border-slate-700">
        <p class="text-[10px] font-black uppercase tracking-[0.18em] text-sky-600">Ready To Collect</p>
        <p class="mt-3 text-3xl font-black text-slate-800 dark:text-slate-100">{{ $statusSummary['ready_to_collect'] }}</p>
        <p class="mt-2 text-[11px] font-medium text-slate-500 dark:text-slate-400">Approved requests waiting for collection.</p>
    </div>
    <div class="bg-white rounded-3xl border border-emerald-100 shadow-sm p-5 dark:bg-slate-800/60 dark:border-slate-700">
        <p class="text-[10px] font-black uppercase tracking-[0.18em] text-emerald-600">Completed</p>
        <p class="mt-3 text-3xl font-black text-slate-800 dark:text-slate-100">{{ $statusSummary['completed'] }}</p>
        <p class="mt-2 text-[11px] font-medium text-slate-500 dark:text-slate-400">Requests already handed over.</p>
    </div>
</div>

<div class="bg-white rounded-[30px] shadow-sm border border-stone-100 p-6 dark:bg-slate-800/50 dark:border-slate-700/50">
    <table id="managerHandoverStatusTable" class="w-full text-left display nowrap">
        <thead class="bg-stone-50 text-stone-400 text-[9px] uppercase font-black tracking-[0.15em]">
            <tr>
                <th class="px-4 py-4">Request</th>
                <th class="px-4 py-4">Type</th>
                <th class="px-4 py-4">Account Holder</th>
                <th class="px-4 py-4">Pickup Info</th>
                <th class="px-4 py-4 text-center">Handover Status WT</th>
                <th class="px-4 py-4 text-center">Recipient Handover</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-50 text-[11px]">
            @forelse($handoverRequests as $request)
                @php
                    $handoverBadge = $badgeMap($request);
                    $staffBadge = $recipientHandoverMap($request);
                    $isSelfIssue = $isSelfRequest($request);
                @endphp
                <tr class="hover:bg-[#FDFBF7] transition-colors">
                    <td class="px-4 py-3">
                        <div class="font-black text-slate-700 dark:text-slate-100">#{{ str_pad($request->id, 5, '0', STR_PAD_LEFT) }}</div>
                        <div class="mt-1 text-[9px] uppercase tracking-widest text-stone-400">
                            {{ $request->request_date ? \Carbon\Carbon::parse($request->request_date)->format('d M Y') : '-' }}
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex rounded-full border px-2.5 py-1 text-[9px] font-black uppercase tracking-widest {{ $isSelfIssue ? 'bg-stone-100 text-stone-700 border-stone-200' : 'bg-[#F5EEE6] text-[#0284c7] border-[#E3D4C6]' }}">
                            {{ $isSelfIssue ? 'Self-Issue' : 'On Behalf' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-bold text-slate-700 dark:text-slate-100">{{ $request->full_name }}</div>
                        <div class="mt-1 text-[9px] uppercase tracking-widest text-stone-400">{{ $request->department ?: '-' }} / {{ $request->position ?: '-' }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-bold text-slate-700 dark:text-slate-100">{{ $request->full_name ?: '-' }}</div>
                        <div class="mt-1 text-[9px] uppercase tracking-widest text-sky-500">
                            {{ $request->requested_pickup_at ? \Carbon\Carbon::parse($request->requested_pickup_at)->format('d M Y H:i') : '-' }}
                        </div>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex rounded-full border px-2.5 py-1 text-[9px] font-black uppercase tracking-widest {{ $handoverBadge['class'] }}">
                            {{ $handoverBadge['text'] }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex rounded-full border px-2.5 py-1 text-[9px] font-black uppercase tracking-widest {{ $staffBadge['class'] }}">
                            {{ $staffBadge['text'] }}
                        </span>
                    </td>
                </tr>
            @empty
            @endforelse
        </tbody>
    </table>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('#managerHandoverStatusTable')) {
            $('#managerHandoverStatusTable').DataTable().destroy();
        }

        const managerHandoverStatusTable = $('#managerHandoverStatusTable').DataTable({
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
            ordering: true,
            order: [[0, 'desc']],
            language: {
                search: "",
                searchPlaceholder: "Search handover status...",
                emptyTable: "No handover status records found.",
                zeroRecords: "No matching handover status records found."
            },
            responsive: true
        });

        mountAdminTableFooter(managerHandoverStatusTable);
    });
</script>
@endpush
@endsection


