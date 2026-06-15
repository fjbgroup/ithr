@extends(request()->routeIs('admin.*') ? 'layouts.admin' : 'layouts.user')

@php
    $routePrefix = request()->routeIs('admin.*') ? 'admin' : 'user';
    $isAdminRoute = request()->routeIs('admin.*');
@endphp

@section('title', $pageTitle)

@push('styles')
<style>
    .faulty-status-card {
        border-radius: 22px;
        border: 1px solid rgba(139, 94, 60, 0.12);
        background: #ffffff;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
    }
    .dark .faulty-status-card {
        background: #1e293b;
        border-color: #334155;
    }
</style>
@endpush

@section('content')
<div class="page-header-block flex flex-col md:flex-row md:items-center md:justify-between gap-3">
    <div>
        <h3 class="page-title-standard">{{ $pageTitle }}</h3>
        <p class="page-subtitle-standard">{{ $pageDescription }}</p>
    </div>
    <a href="{{ route($routePrefix . '.damages.create', $isAdminRoute ? ['mode' => $mode] : []) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#8B5E3C] text-white text-[10px] font-black uppercase tracking-widest hover:bg-[#734C2F]">
        <i class="fa-solid fa-grid-2"></i> Back To Faulty Module
    </a>
</div>

<div class="grid grid-cols-1 mb-5">
    <div class="faulty-status-card p-4">
        <p class="text-[10px] font-black uppercase tracking-widest text-stone-400 dark:text-slate-400">{{ strtoupper($bucket) }}</p>
        <p class="mt-2 text-2xl font-black text-[#3D2B1F] dark:text-slate-100">
            {{ $bucket === 'pending' ? $summary['pending'] : ($bucket === 'drafts' ? $summary['drafts'] : $summary['completed']) }}
        </p>
    </div>
</div>

<div class="faulty-status-card overflow-hidden">
    <div class="px-5 py-4 border-b border-stone-100 dark:border-slate-700 flex items-center justify-between gap-3">
        <h4 class="text-[11px] font-black uppercase tracking-widest text-[#3D2B1F] dark:text-slate-100">{{ $pageTitle }}</h4>
        @if($bucket === 'drafts')
        <a href="{{ route($routePrefix . '.damages.form', $isAdminRoute ? ['mode' => $mode] : []) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-[#8B5E3C] text-white text-[10px] font-black uppercase tracking-widest hover:bg-[#734C2F]">
            <i class="fa-solid fa-plus"></i> New Request
        </a>
        @endif
    </div>

    @if($records->isEmpty())
        <div class="px-5 py-10 text-center text-[11px] font-bold text-stone-400 dark:text-slate-400">
            No records found in this section.
        </div>
    @else
        <div class="divide-y divide-stone-100 dark:divide-slate-700">
            @foreach($records as $record)
            <div class="px-5 py-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="min-w-0">
                    @php
                        $status = strtoupper((string) $record->status);
                        $isManagerOwnedDraft = strtoupper((string) $record->status) === 'DRAFT'
                            && ($record->request_source ?? null) === 'manager_on_behalf_draft';
                    @endphp
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-[11px] font-black text-[#8B5E3C]">#{{ str_pad($record->maintenance_id, 4, '0', STR_PAD_LEFT) }}</span>
                        <span class="px-2 py-1 rounded-full text-[9px] font-black uppercase tracking-widest
                            {{ strtoupper((string) $record->status) === 'DRAFT' ? 'bg-slate-100 text-slate-700' : (strtoupper((string) $record->status) === 'READY TO COLLECT' ? 'bg-lime-100 text-lime-700' : ((bool) $record->done || strtoupper((string) $record->status) === 'DONE' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700')) }}">
                            {{ $record->status ?: 'Pending' }}
                        </span>
                        @if($isManagerOwnedDraft)
                        <span class="px-2 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-sky-100 text-sky-700">
                            Saved by Executive
                        </span>
                        @endif
                        @if($record->ict_received_at && ! $record->done)
                        <span class="px-2 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-sky-100 text-sky-700">
                            Repairing
                        </span>
                        @endif
                        @if($record->temporary_spare_assigned_at && ! $record->temporary_spare_returned_at)
                        <span class="px-2 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-indigo-100 text-indigo-700">
                            Spare / New Given
                        </span>
                        @elseif($record->temporary_spare_requested)
                        <span class="px-2 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-sky-100 text-sky-700">
                            Temporary WT Requested
                        </span>
                        @endif
                        @if($record->original_returned_at)
                        <span class="px-2 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-emerald-100 text-emerald-700">
                            Original WT Returned
                        </span>
                        @endif
                    </div>
                    <p class="mt-2 text-[12px] font-black text-[#3D2B1F] dark:text-slate-100">{{ $record->model ?: 'NO MODEL' }} {{ $record->radio_id ? '- ' . $record->radio_id : '' }}</p>
                    <p class="mt-1 text-[11px] font-bold text-stone-500 dark:text-slate-300">{{ $record->problem_possible ?: ($record->issue_description ?: 'No problem details saved yet.') }}</p>
                    <p class="mt-1 text-[10px] font-bold text-stone-400 dark:text-slate-400">
                        Reporter: {{ $record->reporter_name ?: '-' }} | Submitted: {{ $record->received_date ? \Carbon\Carbon::parse($record->received_date)->format('d M Y') : '-' }}
                        @if($record->ict_received_at)
                            | ICT Received: {{ \Carbon\Carbon::parse($record->ict_received_at)->format('d M Y') }}
                        @endif
                        @if($record->temporary_spare_assigned_at)
                            | Spare/New Given: {{ \Carbon\Carbon::parse($record->temporary_spare_assigned_at)->format('d M Y') }}
                        @elseif($record->temporary_spare_requested)
                            | Temporary WT Requested
                        @endif
                        @if($record->original_returned_at)
                            | Original Returned: {{ \Carbon\Carbon::parse($record->original_returned_at)->format('d M Y') }}
                        @endif
                    </p>
                    <p class="mt-1 text-[10px] font-bold text-emerald-700 dark:text-emerald-300">
                        Handover: {{ $record->handover_at ? \Carbon\Carbon::parse($record->handover_at)->format('d M Y, h:i A') : '-' }} by {{ $record->handover_person ?: '-' }}
                        | Pickup: {{ $record->pickup_at ? \Carbon\Carbon::parse($record->pickup_at)->format('d M Y, h:i A') : '-' }} by {{ $record->pickup_person ?: '-' }}
                        | ICT Department Sejurumus after ICT approval
                    </p>
                    @if(!empty($record->remarks))
                    <p class="mt-2 text-[10px] font-bold text-[#8B5E3C] dark:text-amber-300">{{ $record->remarks }}</p>
                    @endif
                    @if(!empty($record->temporary_spare_request_note))
                    <p class="mt-2 text-[10px] font-bold text-sky-700 dark:text-sky-300">Temporary WT: {{ $record->temporary_spare_request_note }}</p>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    @if($bucket === 'drafts' && !($isManagerOwnedDraft && !$isAdminRoute))
                    <a href="{{ route($routePrefix . '.damages.form', array_merge($isAdminRoute ? ['mode' => $mode] : [], ['draft' => $record->maintenance_id])) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-stone-200 dark:border-slate-700 text-[10px] font-black uppercase tracking-widest text-stone-600 dark:text-slate-200">
                        <i class="fa-solid fa-pen"></i> Continue
                    </a>
                    @elseif($bucket === 'drafts' && $isManagerOwnedDraft && !$isAdminRoute)
                    <a href="{{ route($routePrefix . '.damages.show', ['damage' => $record->maintenance_id]) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-sky-200 dark:border-sky-800 text-[10px] font-black uppercase tracking-widest text-sky-700 dark:text-sky-300">
                        <i class="fa-solid fa-eye"></i> View Only
                    </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

