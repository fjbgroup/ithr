@extends(request()->routeIs('admin.*') ? 'layouts.admin' : 'layouts.user')

@php
    $routePrefix = request()->routeIs('admin.*') ? 'admin' : 'user';
    $isAdminRoute = request()->routeIs('admin.*');
@endphp

@section('title', 'Report Faulty')

@push('styles')
<style>
    .faulty-hub-card {
        position: relative;
        overflow: hidden;
        border-radius: 24px;
        border: 1px solid rgba(139, 94, 60, 0.12);
        background: linear-gradient(135deg, #ffffff 0%, #fffaf5 100%);
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
        transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
    }
    .faulty-hub-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 24px 48px rgba(15, 23, 42, 0.12);
        border-color: rgba(139, 94, 60, 0.2);
    }
    .faulty-hub-count {
        font-size: 2rem;
        line-height: 1;
        font-weight: 900;
        color: #3D2B1F;
    }
    .dark .faulty-hub-card {
        background: linear-gradient(135deg, #1e293b 0%, #172033 100%);
        border-color: #334155;
    }
    .dark .faulty-hub-count,
    .dark .faulty-hub-card h4 {
        color: #f8fafc !important;
    }
</style>
@endpush

@section('content')
@php
    $recentDamageRequests = $recentDamageRequests ?? collect();
    $recentCompletedDamageRequests = $recentCompletedDamageRequests ?? collect();
    $recentDamageId = session('recent_damage_id');
@endphp
<div class="page-header-block">
    <div>
        <h3 class="page-title-standard">Report Faulty</h3>
        <p class="page-subtitle-standard">
            Open the faulty reporting module and go straight to the action you need.
        </p>
    </div>
</div>

@if(session('success'))
<div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-[11px] font-bold text-emerald-700">
    <i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <a href="{{ route($routePrefix . '.damages.form', $isAdminRoute ? ['mode' => $mode] : []) }}" class="faulty-hub-card p-5">
        <div class="w-11 h-11 rounded-2xl bg-[#8B5E3C] text-white flex items-center justify-center shadow-lg shadow-[#8B5E3C]/20">
            <i class="fa-solid fa-plus text-base"></i>
        </div>
        <h4 class="mt-4 text-sm font-black text-[#3D2B1F]">New Request</h4>
        <p class="mt-2 text-[11px] font-bold text-stone-500 dark:text-slate-300 leading-relaxed">Create a new faulty report form and submit it for review.</p>
        <div class="mt-4 text-[10px] font-black uppercase tracking-widest text-[#8B5E3C]">Open Form</div>
    </a>

    <a href="{{ route($routePrefix . '.damages.status', array_merge(['bucket' => 'pending'], $isAdminRoute ? ['mode' => $mode] : [])) }}" class="faulty-hub-card p-5">
        <div class="w-11 h-11 rounded-2xl bg-amber-500 text-white flex items-center justify-center shadow-lg shadow-amber-500/20">
            <i class="fa-solid fa-hourglass-half text-base"></i>
        </div>
        <div class="mt-4 faulty-hub-count">{{ $summary['pending'] }}</div>
        <h4 class="mt-2 text-sm font-black text-[#3D2B1F]">Pending Status</h4>
        <p class="mt-2 text-[11px] font-bold text-stone-500 dark:text-slate-300 leading-relaxed">See reports that are waiting for executive review, ICT review, or repair progress.</p>
    </a>

    <a href="{{ route($routePrefix . '.damages.status', array_merge(['bucket' => 'completed'], $isAdminRoute ? ['mode' => $mode] : [])) }}" class="faulty-hub-card p-5">
        <div class="w-11 h-11 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shadow-lg shadow-emerald-600/20">
            <i class="fa-solid fa-circle-check text-base"></i>
        </div>
        <div class="mt-4 faulty-hub-count">{{ $summary['completed'] }}</div>
        <h4 class="mt-2 text-sm font-black text-[#3D2B1F]">Completed</h4>
        <p class="mt-2 text-[11px] font-bold text-stone-500 dark:text-slate-300 leading-relaxed">View faulty reports that have already been completed or resolved.</p>
    </a>
</div>

@if($recentDamageRequests->isNotEmpty())
<div class="mt-6 faulty-hub-card p-5">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h4 class="text-sm font-black text-[#3D2B1F] dark:text-slate-100">Latest Submitted Requests</h4>
            <p class="mt-1 text-[10px] font-bold uppercase tracking-widest text-stone-400 dark:text-slate-400">Your recently submitted faulty reports appear here after submission.</p>
        </div>
        <a href="{{ route($routePrefix . '.damages.status', array_merge(['bucket' => 'pending'], $isAdminRoute ? ['mode' => $mode] : [])) }}" class="text-[10px] font-black uppercase tracking-widest text-[#8B5E3C]">
            View All
        </a>
    </div>

    <div class="mt-4 space-y-3">
        @foreach($recentDamageRequests as $record)
            @php
                $status = strtoupper((string) $record->status);
                $isDone = (bool) $record->done || in_array($status, ['DONE', 'COMPLETED', 'REPAIRED', 'RESOLVED'], true);
                $statusBadgeClass = $isDone
                    ? 'bg-emerald-100 text-emerald-700'
                    : 'bg-amber-100 text-amber-700';
                $hasReplacementRequest = str_contains((string) ($record->remarks ?? ''), 'REPLACEMENT REQUESTED');
                $isHighlighted = (int) $recentDamageId === (int) $record->maintenance_id;
            @endphp
            <div class="rounded-2xl border {{ $isHighlighted ? 'border-emerald-300 bg-emerald-50/40' : 'border-stone-200 bg-white/80' }} px-4 py-4 dark:border-slate-700 dark:bg-slate-900/40">
                <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-[11px] font-black text-[#8B5E3C]">#{{ str_pad($record->maintenance_id, 4, '0', STR_PAD_LEFT) }}</span>
                            <span class="rounded-full px-2.5 py-1 text-[9px] font-black uppercase tracking-widest {{ $statusBadgeClass }}">
                                {{ $isDone ? 'Already Fixed / Ready To Collect' : 'Processing' }}
                            </span>
                            @if($hasReplacementRequest)
                            <span class="rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[9px] font-black uppercase tracking-widest text-emerald-700">
                                Replacement requested
                            </span>
                            @endif
                        </div>
                        <p class="mt-2 text-[12px] font-black text-[#3D2B1F] dark:text-slate-100">{{ $record->model ?: 'NO MODEL' }}{{ $record->radio_id ? ' - ' . $record->radio_id : ($record->serial_number ? ' - ' . $record->serial_number : '') }}</p>
                        <p class="mt-1 text-[11px] font-bold text-stone-500 dark:text-slate-300">{{ $record->problem_possible ?: ($record->issue_description ?: 'No problem details saved yet.') }}</p>
                        <p class="mt-1 text-[10px] font-bold text-stone-400 dark:text-slate-400">
                            Submitted: {{ $record->received_date ? \Carbon\Carbon::parse($record->received_date)->format('d M Y') : '-' }}
                        </p>
                    </div>
                    @if($isHighlighted)
                    <div class="rounded-full border border-emerald-200 bg-white px-3 py-1 text-[9px] font-black uppercase tracking-widest text-emerald-700">
                        Just submitted
                    </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

@if($recentCompletedDamageRequests->isNotEmpty())
<div class="mt-6 faulty-hub-card p-5">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h4 class="text-sm font-black text-[#3D2B1F] dark:text-slate-100">Latest Completed Requests</h4>
            <p class="mt-1 text-[10px] font-bold uppercase tracking-widest text-stone-400 dark:text-slate-400">Your recently completed or resolved faulty reports appear here.</p>
        </div>
        <a href="{{ route($routePrefix . '.damages.status', array_merge(['bucket' => 'completed'], $isAdminRoute ? ['mode' => $mode] : [])) }}" class="text-[10px] font-black uppercase tracking-widest text-[#8B5E3C]">
            View All
        </a>
    </div>

    <div class="mt-4 space-y-3">
        @foreach($recentCompletedDamageRequests as $record)
            @php
                $status = strtoupper((string) $record->status);
                $hasReplacementRequest = str_contains((string) ($record->remarks ?? ''), 'REPLACEMENT REQUESTED');
            @endphp
            <div class="rounded-2xl border border-stone-200 bg-white/80 px-4 py-4 dark:border-slate-700 dark:bg-slate-900/40">
                <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-[11px] font-black text-[#8B5E3C]">#{{ str_pad($record->maintenance_id, 4, '0', STR_PAD_LEFT) }}</span>
                            <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-[9px] font-black uppercase tracking-widest text-emerald-700">
                                {{ in_array($status, ['REJECTED', 'REFUSED'], true) ? 'Rejected' : 'Already Fixed / Ready To Collect' }}
                            </span>
                            @if($hasReplacementRequest)
                            <span class="rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[9px] font-black uppercase tracking-widest text-emerald-700">
                                Replacement requested
                            </span>
                            @endif
                        </div>
                        <p class="mt-2 text-[12px] font-black text-[#3D2B1F] dark:text-slate-100">{{ $record->model ?: 'NO MODEL' }}{{ $record->radio_id ? ' - ' . $record->radio_id : ($record->serial_number ? ' - ' . $record->serial_number : '') }}</p>
                        <p class="mt-1 text-[11px] font-bold text-stone-500 dark:text-slate-300">{{ $record->problem_possible ?: ($record->issue_description ?: 'No problem details saved yet.') }}</p>
                        <p class="mt-1 text-[10px] font-bold text-stone-400 dark:text-slate-400">
                            Completed: {{ $record->finish_date ? \Carbon\Carbon::parse($record->finish_date)->format('d M Y') : ($record->updated_at ? \Carbon\Carbon::parse($record->updated_at)->format('d M Y') : ($record->received_date ? \Carbon\Carbon::parse($record->received_date)->format('d M Y') : '-')) }}
                        </p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif
@endsection

