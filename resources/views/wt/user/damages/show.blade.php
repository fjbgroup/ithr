@extends(request()->routeIs('wt.admin.*') ? 'wt.layouts.admin' : 'wt.layouts.user')

@php
    $routePrefix = request()->routeIs('wt.admin.*') ? 'wt.admin' : 'wt.user';
    $isAdminRoute = request()->routeIs('wt.admin.*');
    $recordStatus = strtoupper((string) $record->status);
    $recordIsDone = (bool) $record->done || $recordStatus === 'DONE';
    $recordBadgeClass = $recordIsDone
        ? 'bg-emerald-100 text-emerald-700 border-emerald-200'
        : ($recordStatus === 'DRAFT' ? 'bg-slate-100 text-slate-700 border-slate-200' : 'bg-amber-100 text-amber-700 border-amber-200');
    $replacementRequested = str_contains((string) ($record->remarks ?? ''), 'REPLACEMENT REQUESTED');
    $isManagerOwnedDraft = $recordStatus === 'DRAFT' && ($record->request_source ?? null) === 'manager_on_behalf_draft';
@endphp

@section('title', 'View Faulty Report')

@section('content')
<div class="mb-4 flex flex-col md:flex-row md:items-end md:justify-between gap-3">
    <div>
        <h3 class="text-base font-extrabold text-[#142b47] dark:text-slate-100 tracking-tight">View Faulty Report</h3>
        <p class="text-stone-400 dark:text-slate-400 font-medium mt-1 text-[10px] tracking-widest uppercase">Full read-only details for this faulty report.</p>
    </div>
    <a href="{{ route($routePrefix . '.damages.status', $isAdminRoute ? ['bucket' => 'drafts', 'mode' => $mode] : ['bucket' => 'drafts']) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#0284c7] text-white text-[10px] font-black uppercase tracking-widest hover:bg-[#734C2F]">
        <i class="fa-solid fa-arrow-left"></i> Back To Drafts
    </a>
</div>

<div class="px-2">
    <div class="damage-card bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-stone-200 dark:border-slate-700 overflow-hidden p-5 md:p-6">
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-[#0284c7]">Faulty Report Details</p>
                <h3 class="mt-2 text-sm font-extrabold text-[#142b47] dark:text-slate-100">Faulty Report #{{ str_pad($record->maintenance_id, 4, '0', STR_PAD_LEFT) }}</h3>
                <p class="mt-1 text-[10px] font-bold uppercase tracking-widest text-stone-400">{{ $isManagerOwnedDraft ? 'Saved by executive as an on-behalf draft.' : 'This record is displayed in read-only mode.' }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex rounded-full border px-3 py-1 text-[9px] font-black uppercase tracking-widest {{ $recordBadgeClass }}">
                    {{ $record->status ?: 'Pending' }}
                </span>
                @if($isManagerOwnedDraft)
                <span class="inline-flex rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-[9px] font-black uppercase tracking-widest text-sky-700">
                    Saved by Executive
                </span>
                @endif
                @if($replacementRequested)
                <span class="inline-flex rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-[9px] font-black uppercase tracking-widest text-emerald-700">
                    <i class="fa-solid fa-circle-check mr-2"></i>Replacement requested
                </span>
                @endif
                @if($record->ict_received_at && ! $recordIsDone)
                <span class="inline-flex rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-[9px] font-black uppercase tracking-widest text-sky-700">
                    Repairing
                </span>
                @endif
                @if($record->temporary_spare_assigned_at && ! $record->temporary_spare_returned_at)
                <span class="inline-flex rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-[9px] font-black uppercase tracking-widest text-indigo-700">
                    Spare / New Given
                </span>
                @elseif($record->temporary_spare_requested)
                <span class="inline-flex rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-[9px] font-black uppercase tracking-widest text-sky-700">
                    Temporary WT Requested
                </span>
                @endif
                @if($record->original_returned_at)
                <span class="inline-flex rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-[9px] font-black uppercase tracking-widest text-emerald-700">
                    Original WT Returned
                </span>
                @endif
            </div>
        </div>

        <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4 text-[11px]">
            <div class="rounded-xl border border-stone-200 dark:border-slate-700 bg-stone-50 dark:bg-slate-800 px-4 py-3">
                <p class="text-[9px] font-black uppercase tracking-widest text-stone-500">Reporter</p>
                <p class="mt-2 font-black text-[#142b47] dark:text-slate-100">{{ strtoupper($record->reporter_name ?: '-') }}</p>
                <p class="mt-1 font-bold text-stone-500">{{ strtoupper($record->reporter_staff_id ?: '-') }}</p>
            </div>
            <div class="rounded-xl border border-stone-200 dark:border-slate-700 bg-stone-50 dark:bg-slate-800 px-4 py-3">
                <p class="text-[9px] font-black uppercase tracking-widest text-stone-500">Department</p>
                <p class="mt-2 font-black text-[#142b47] dark:text-slate-100">{{ strtoupper($record->department_name ?: '-') }}</p>
            </div>
            <div class="rounded-xl border border-stone-200 dark:border-slate-700 bg-stone-50 dark:bg-slate-800 px-4 py-3">
                <p class="text-[9px] font-black uppercase tracking-widest text-stone-500">Ownership / Deployment</p>
                <p class="mt-2 font-black text-[#142b47] dark:text-slate-100">Ownership: {{ strtoupper($record->ownership_type ?: '-') }}</p>
                <p class="mt-1 font-bold text-stone-500">Shared With: {{ strtoupper($record->shared_with ?: '-') }}</p>
                <p class="mt-1 font-bold text-stone-500">Sector: {{ strtoupper($record->sector ?: '-') }}</p>
                <p class="mt-1 font-bold text-stone-500">Location: {{ strtoupper($record->location ?: '-') }}</p>
            </div>
            <div class="rounded-xl border border-stone-200 dark:border-slate-700 bg-stone-50 dark:bg-slate-800 px-4 py-3">
                <p class="text-[9px] font-black uppercase tracking-widest text-stone-500">Device Details</p>
                <p class="mt-2 font-black text-[#142b47] dark:text-slate-100">Model: {{ strtoupper($record->model ?: '-') }}</p>
                <p class="mt-1 font-bold text-stone-500">Radio ID: {{ strtoupper($record->radio_id ?: '-') }}</p>
                <p class="mt-1 font-bold text-stone-500">Serial No: {{ strtoupper($record->serial_number ?: '-') }}</p>
            </div>
            <div class="rounded-xl border border-stone-200 dark:border-slate-700 bg-stone-50 dark:bg-slate-800 px-4 py-3">
                <p class="text-[9px] font-black uppercase tracking-widest text-stone-500">Submission Info</p>
                <p class="mt-2 font-black text-[#142b47] dark:text-slate-100">Submitted: {{ $record->received_date ? \Carbon\Carbon::parse($record->received_date)->format('d M Y') : '-' }}</p>
                <p class="mt-1 font-bold text-stone-500">Phone No: {{ $record->phone_no ?: '-' }}</p>
                <p class="mt-1 font-bold text-stone-500">Current Status: {{ $record->status ?: '-' }}</p>
                <p class="mt-1 font-bold text-stone-500">ICT Received: {{ $record->ict_received_at ? \Carbon\Carbon::parse($record->ict_received_at)->format('d M Y') : '-' }}</p>
                <p class="mt-1 font-bold text-stone-500">Temporary WT Needed: {{ is_null($record->temporary_spare_requested) ? 'NOT ANSWERED' : ($record->temporary_spare_requested ? 'YES' : 'NO') }}</p>
                <p class="mt-1 font-bold text-stone-500">Spare/New Given: {{ $record->temporary_spare_assigned_at ? \Carbon\Carbon::parse($record->temporary_spare_assigned_at)->format('d M Y') : '-' }}</p>
                <p class="mt-1 font-bold text-stone-500">Original Returned: {{ $record->original_returned_at ? \Carbon\Carbon::parse($record->original_returned_at)->format('d M Y') : '-' }}</p>
            </div>
            <div class="rounded-xl border border-stone-200 dark:border-slate-700 bg-stone-50 dark:bg-slate-800 px-4 py-3">
                <p class="text-[9px] font-black uppercase tracking-widest text-stone-500">Workflow</p>
                <p class="mt-2 font-black text-[#142b47] dark:text-slate-100">Source: {{ strtoupper(str_replace('_', ' ', $record->request_source ?: 'USER')) }}</p>
                <p class="mt-1 font-bold text-stone-500">Done: {{ $recordIsDone ? 'YES' : 'NO' }}</p>
            </div>
            <div class="md:col-span-2 rounded-xl border border-emerald-200 dark:border-emerald-900/70 bg-emerald-50 dark:bg-emerald-950/30 px-4 py-3">
                <p class="text-[9px] font-black uppercase tracking-widest text-emerald-700 dark:text-emerald-300">Pickup & Handover</p>
                <p class="mt-2 font-black text-[#142b47] dark:text-slate-100">Handover to ICT: {{ strtoupper($record->handover_person ?: '-') }} | {{ $record->handover_at ? \Carbon\Carbon::parse($record->handover_at)->format('d M Y, h:i A') : '-' }}</p>
                <p class="mt-1 font-bold text-emerald-800 dark:text-emerald-200">Pickup after ICT approval: {{ strtoupper($record->pickup_person ?: '-') }} | {{ $record->pickup_at ? \Carbon\Carbon::parse($record->pickup_at)->format('d M Y, h:i A') : '-' }}</p>
                <p class="mt-1 font-bold text-emerald-800 dark:text-emerald-200">Location: ICT Department Sejurumus</p>
            </div>
            <div class="md:col-span-2 rounded-xl border border-stone-200 dark:border-slate-700 bg-stone-50 dark:bg-slate-800 px-4 py-3">
                <p class="text-[9px] font-black uppercase tracking-widest text-stone-500">Problem Reported</p>
                <p class="mt-2 font-bold text-[#142b47] dark:text-slate-100 leading-relaxed">{{ $record->problem_possible ?: ($record->issue_description ?: '-') }}</p>
            </div>
            <div class="md:col-span-2 rounded-xl border border-stone-200 dark:border-slate-700 bg-stone-50 dark:bg-slate-800 px-4 py-3">
                <p class="text-[9px] font-black uppercase tracking-widest text-stone-500">Remarks / Replacement Details</p>
                <p class="mt-2 font-bold text-[#142b47] dark:text-slate-100 leading-relaxed">{{ $record->remarks ?: 'No additional remarks.' }}</p>
                @if($record->temporary_spare_request_note)
                <p class="mt-2 font-bold text-sky-700 dark:text-sky-300 leading-relaxed">Temporary WT note: {{ $record->temporary_spare_request_note }}</p>
                @endif
            </div>
            <div class="md:col-span-2 rounded-xl border border-stone-200 dark:border-slate-700 bg-stone-50 dark:bg-slate-800 px-4 py-3">
                <p class="text-[9px] font-black uppercase tracking-widest text-stone-500">Evidence Uploaded</p>
                @if(is_array($record->evidence_paths) && count($record->evidence_paths))
                <div class="mt-2 flex flex-wrap gap-2">
                    @foreach($record->evidence_paths as $path)
                    <a href="{{ \Illuminate\Support\Facades\Storage::url($path) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-stone-200 dark:border-slate-600 text-[10px] font-black uppercase tracking-widest text-stone-600 dark:text-slate-200">
                        <i class="fa-solid fa-paperclip"></i> File {{ $loop->iteration }}
                    </a>
                    @endforeach
                </div>
                @else
                <p class="mt-2 font-bold text-[#142b47] dark:text-slate-100">No evidence uploaded.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection


