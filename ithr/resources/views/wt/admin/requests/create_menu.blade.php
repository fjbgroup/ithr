@extends('wt.layouts.admin')

@php
    $requestVariant = $requestVariant ?? 'standard';
    $isTemporaryRequest = $isTemporaryRequest ?? ($requestVariant === 'temporary');
@endphp

@section('title', $isTemporaryRequest ? 'Temporary Request' : 'Long Term Request')

@section('content')
<div class="max-w-5xl mx-auto px-2">
    <div class="page-header-block">
        <h3 class="page-title-standard">{{ $isTemporaryRequest ? 'Temporary Request' : 'Long Term Request' }}</h3>
        <p class="page-subtitle-standard">
            {{ $isTemporaryRequest
                ? 'Executive fills in the temporary request form with recipient ownership, quantity, and purpose. ICT reviews the request and handles unit assignment later.'
                : 'Executive fills in recipient ownership details. ICT will assign the available walkie talkie during approval.' }}
        </p>
    </div>

    <div class="mb-6 rounded-3xl border border-[#0284c7]/15 bg-[#FDFBF7] px-5 py-4 shadow-sm dark:bg-slate-800/70 dark:border-slate-700">
        <div class="flex items-start gap-3">
            <div class="mt-0.5 flex h-10 w-10 items-center justify-center rounded-2xl bg-[#0284c7] text-white">
                <i class="fas fa-circle-info text-sm"></i>
            </div>
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.18em] text-[#0284c7]">ICT Assignment Flow</p>
                <p class="mt-1 text-[12px] font-semibold text-stone-600 dark:text-slate-300">
                    {{ $isTemporaryRequest
                        ? 'No need to choose units here. Executive only submits temporary request details, quantity, and usage purpose. ICT will review the quantity and assign available units later.'
                        : 'No need to choose an available unit here. Executive only submits long term request details. ICT will select the available walkie talkie after review.' }}
                </p>
            </div>
        </div>
    </div>

    <div class="grid gap-4">
        <a href="{{ $isTemporaryRequest ? route('wt.admin.requests.create.temporary.shared') : route('wt.admin.requests.create.shared') }}" class="group relative overflow-hidden rounded-2xl border border-amber-500/25 bg-slate-950/95 p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-amber-400/70 hover:shadow-lg hover:shadow-amber-950/20 dark:border-amber-500/30 dark:bg-slate-900">
            <div class="absolute inset-y-0 left-0 w-1 bg-amber-400"></div>
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-amber-400/30 bg-amber-500/15 text-amber-200">
                        <i class="fas fa-people-arrows text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-[0.22em] text-amber-300">On Behalf</p>
                        <h4 class="mt-1 text-[15px] font-black leading-tight text-white">{{ $isTemporaryRequest ? 'Temporary Request on Behalf' : 'Request on Behalf' }}</h4>
                    </div>
                </div>
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-slate-700 text-slate-300 transition group-hover:border-amber-400 group-hover:text-amber-200">
                    <i class="fas fa-arrow-right text-[11px] transition group-hover:translate-x-0.5"></i>
                </div>
            </div>
            <p class="mt-4 text-[12px] font-semibold leading-relaxed text-slate-400">
                {{ $isTemporaryRequest
                    ? 'Fill recipient ownership, quantity, usage purpose, and pickup details before ICT review.'
                    : 'Fill recipient ownership details and send the request directly to ICT for approval.' }}
            </p>
        </a>
    </div>
</div>
@endsection


