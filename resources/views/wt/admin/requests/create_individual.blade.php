@extends('wt.layouts.admin')

@section('title', 'Executive Request (Self-Issue)')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .admin-request-shell {
        max-width: 900px;
        margin: 0 auto;
    }
    .admin-request-card {
        padding: 20px 22px;
    }
    .smart-select + .select2-container,
    .smart-user-select + .select2-container {
        width: 100% !important;
    }
    .smart-select + .select2-container .select2-selection--single,
    .smart-user-select + .select2-container .select2-selection--single {
        min-height: 46px;
        border-radius: 14px;
        border: 1px solid rgba(139, 94, 60, 0.3);
        background: rgba(253, 251, 247, 0.5);
        padding: 6px 14px;
        display: flex;
        align-items: center;
    }
    .smart-select + .select2-container .select2-selection__rendered,
    .smart-user-select + .select2-container .select2-selection__rendered {
        color: #334155 !important;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.4 !important;
        padding-left: 0 !important;
        padding-right: 26px !important;
        text-transform: uppercase;
    }
    .smart-select + .select2-container .select2-selection__placeholder,
    .smart-user-select + .select2-container .select2-selection__placeholder {
        color: #94a3b8 !important;
    }
    .smart-select + .select2-container .select2-selection__arrow,
    .smart-user-select + .select2-container .select2-selection__arrow {
        height: 100% !important;
        right: 12px !important;
    }
    .select2-dropdown {
        border: 1px solid rgba(139, 94, 60, 0.18) !important;
        border-radius: 14px !important;
        overflow: hidden;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12);
        background: #fffaf5 !important;
    }
    .select2-container--default .select2-results > .select2-results__options {
        background: #fffaf5 !important;
    }
    .select2-search--dropdown {
        padding: 10px !important;
        background: #fffaf5;
    }
    .select2-search--dropdown .select2-search__field {
        border: 1px solid rgba(139, 94, 60, 0.28) !important;
        border-radius: 10px !important;
        background: #ffffff !important;
        color: #3d2b1f !important;
        padding: 8px 10px !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        text-transform: uppercase;
    }
    .select2-search--dropdown .select2-search__field::placeholder {
        color: #8b7355 !important;
        opacity: 1 !important;
    }
    .select2-results__option {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        padding: 10px 12px;
        background: #fffaf5;
        color: #3d2b1f !important;
        opacity: 1 !important;
    }
    .select2-container--default .select2-results__option--selected,
    .select2-container--default .select2-results__option[aria-selected=true] {
        background: rgba(179, 138, 90, 0.18) !important;
        color: #724d31 !important;
    }
    .select2-results__option--highlighted.select2-results__option--selectable {
        background: #0284c7 !important;
        color: #ffffff !important;
    }
    .dark .smart-select + .select2-container .select2-selection--single,
    .dark .smart-user-select + .select2-container .select2-selection--single {
        background: #0f172a;
        border-color: #334155;
    }
    .dark .smart-select + .select2-container .select2-selection__rendered,
    .dark .smart-user-select + .select2-container .select2-selection__rendered {
        color: #e2e8f0 !important;
    }
    .dark .select2-dropdown {
        background: #0f172a !important;
        border-color: #475569 !important;
        box-shadow: 0 18px 40px rgba(2, 6, 23, 0.45);
    }
    .dark .select2-container--default .select2-results > .select2-results__options {
        background: #0f172a !important;
    }
    .dark .select2-search--dropdown {
        background: #111827 !important;
        border-bottom: 1px solid rgba(71, 85, 105, 0.45);
    }
    .dark .select2-search--dropdown .select2-search__field {
        background: #0f172a !important;
        color: #e2e8f0 !important;
        border-color: #475569 !important;
    }
    .dark .select2-search--dropdown .select2-search__field::placeholder {
        color: #94a3b8 !important;
        opacity: 1 !important;
    }
    .dark .select2-results {
        background: #0f172a !important;
    }
    .dark .select2-results__option {
        background: #0f172a !important;
        color: #e2e8f0 !important;
        opacity: 1 !important;
    }
    .dark .select2-container--default .select2-results__option--selected,
    .dark .select2-container--default .select2-results__option[aria-selected=true] {
        background: rgba(179, 138, 90, 0.28) !important;
        color: #ffe7c2 !important;
    }
    .dark .select2-results__option--highlighted.select2-results__option--selectable {
        background: #0284c7 !important;
        color: #ffffff !important;
    }
    .admin-request-shell {
        max-width: 980px !important;
        font-size: 10px !important;
    }
    .admin-request-card {
        border-radius: 10px !important;
        padding: 10px 12px !important;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.04) !important;
    }
    .admin-request-card form {
        gap: 9px !important;
    }
    .admin-request-card h2 {
        font-size: 11px !important;
        line-height: 1.2 !important;
    }
    .admin-request-card h4 {
        margin-top: 8px !important;
        margin-bottom: 6px !important;
        padding-bottom: 5px !important;
        font-size: 9.5px !important;
        line-height: 1.2 !important;
        letter-spacing: 0.14em !important;
    }
    .admin-request-card .grid {
        gap: 7px !important;
    }
    .admin-request-card .mb-5,
    .admin-request-card .mb-4,
    .admin-request-card .mb-3 {
        margin-bottom: 7px !important;
    }
    .admin-request-card .mt-5,
    .admin-request-card .mt-4,
    .admin-request-card .mt-3,
    .admin-request-card .mt-2,
    .admin-request-card .mt-1 {
        margin-top: 4px !important;
    }
    .admin-request-card .p-5,
    .admin-request-card .p-4 {
        padding: 8px !important;
    }
    .admin-request-card .px-4,
    .admin-request-card .px-3 {
        padding-left: 8px !important;
        padding-right: 8px !important;
    }
    .admin-request-card .py-3,
    .admin-request-card .py-2,
    .admin-request-card .py-2\.5 {
        padding-top: 5px !important;
        padding-bottom: 5px !important;
    }
    .admin-request-card label,
    .admin-request-card p,
    .admin-request-card span {
        font-size: 9px !important;
        line-height: 1.25 !important;
    }
    .admin-request-card label {
        font-size: 8.5px !important;
        margin-bottom: 3px !important;
        letter-spacing: 0.08em !important;
    }
    .admin-request-card input:not([type="checkbox"]):not([type="radio"]),
    .admin-request-card select,
    .admin-request-card textarea {
        min-height: 28px !important;
        height: 28px !important;
        border-radius: 7px !important;
        padding: 5px 8px !important;
        font-size: 9.5px !important;
        line-height: 1.2 !important;
    }
    .admin-request-card textarea {
        min-height: 44px !important;
        height: auto !important;
    }
    .admin-request-card .smart-select + .select2-container .select2-selection--single,
    .admin-request-card .smart-user-select + .select2-container .select2-selection--single {
        min-height: 28px !important;
        height: 28px !important;
        border-radius: 7px !important;
        padding: 2px 8px !important;
    }
    .admin-request-card .smart-select + .select2-container .select2-selection__rendered,
    .admin-request-card .smart-user-select + .select2-container .select2-selection__rendered {
        font-size: 9.5px !important;
        line-height: 1.2 !important;
    }
    .admin-request-card .rounded-2xl,
    .admin-request-card .rounded-xl {
        border-radius: 8px !important;
    }
    .admin-request-card .flex.items-center.gap-2\.5 {
        gap: 8px !important;
        margin-bottom: 6px !important;
    }
    .admin-request-card .bg-\[\#0284c7\].text-white,
    .admin-request-card .bg-red-800.text-white {
        padding: 6px !important;
        border-radius: 7px !important;
    }
    .admin-request-card button,
    .admin-request-card .request-submit-btn {
        min-height: 28px !important;
        padding: 7px 14px !important;
        border-radius: 8px !important;
        font-size: 9px !important;
    }
    @media (max-width: 768px) {
        .admin-request-shell {
            max-width: 100%;
        }
        .admin-request-card {
            padding: 16px 14px;
            border-radius: 18px;
        }
        .admin-request-card form {
            gap: 20px;
        }
        .admin-request-card .select2-container .select2-selection--single {
            min-height: 42px;
            border-radius: 12px;
            padding: 4px 12px;
        }
        .admin-request-card .select2-container .select2-selection__rendered {
            font-size: 10px !important;
            padding-right: 22px !important;
        }
        .admin-request-card input,
        .admin-request-card textarea,
        .admin-request-card select {
            padding-left: 12px !important;
            padding-right: 12px !important;
        }
        .admin-request-card textarea {
            min-height: 92px;
        }
        .admin-request-card .request-submit-row {
            padding-top: 4px;
        }
        .admin-request-card .request-submit-btn {
            width: 100%;
            justify-content: center;
            padding: 13px 16px;
            border-radius: 16px;
        }
    }
</style>
@endpush

@section('content')
@php
    $requestVariant = $requestVariant ?? 'standard';
    $isTemporaryRequest = $isTemporaryRequest ?? ($requestVariant === 'temporary');
    $formAction = $isTemporaryRequest ? route('wt.admin.requests.store.temporary') : route('wt.admin.requests.store');
    $departmentOptions = $formOptionLists['departments'] ?? [];
    $sectorOptions = $formOptionLists['sectors'] ?? [];
    $locationOptions = $formOptionLists['locations'] ?? [];
    $bayOptions = $formOptionLists['bays'] ?? [];
    $selectedDepartment = old('department', $currentUser->department);
    $lockDepartment = filled($currentUser->department);
@endphp
<style>
    .match-report-faulty.admin-request-shell { max-width: 1470px !important; margin-left: auto !important; margin-right: auto !important; }
    .match-report-faulty .page-header-block { padding: 0 !important; margin: 0 0 12px !important; background: transparent !important; border: 0 !important; box-shadow: none !important; }
    .match-report-faulty .page-title-standard { font-size: 15px !important; line-height: 1.15 !important; letter-spacing: 0 !important; }
    .match-report-faulty .page-subtitle-standard { margin-top: 6px !important; font-size: 9px !important; line-height: 1.25 !important; letter-spacing: 0.16em !important; }
    .match-report-faulty > .mb-3 { margin-bottom: 8px !important; }
    .match-report-faulty > .mb-3 a,
    .match-report-faulty .page-header-block a { min-height: 26px !important; padding: 6px 10px !important; border-radius: 8px !important; font-size: 8.5px !important; }
    .match-report-faulty .admin-request-card { padding: 10px 12px !important; border-radius: 10px !important; }
    .match-report-faulty .admin-request-card > .flex:first-child { gap: 8px !important; margin-bottom: 8px !important; }
    .match-report-faulty .admin-request-card > .flex:first-child > div { width: 36px !important; height: 36px !important; padding: 0 !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; border-radius: 9px !important; }
    .match-report-faulty .admin-request-card h2 { font-size: 11px !important; line-height: 1.2 !important; letter-spacing: 0.08em !important; }
    .match-report-faulty .admin-request-card h4 { margin: 8px 0 6px !important; padding: 0 0 5px 8px !important; font-size: 9.5px !important; line-height: 1.2 !important; letter-spacing: 0.14em !important; border-left-width: 3px !important; }
    .match-report-faulty .admin-request-card .grid { gap: 7px !important; }
    .match-report-faulty .admin-request-card .mb-5,
    .match-report-faulty .admin-request-card .mb-4,
    .match-report-faulty .admin-request-card .mb-3 { margin-bottom: 7px !important; }
    .match-report-faulty .admin-request-card .mt-3,
    .match-report-faulty .admin-request-card .mt-2,
    .match-report-faulty .admin-request-card .mt-1 { margin-top: 4px !important; }
    .match-report-faulty .admin-request-card .rounded-2xl,
    .match-report-faulty .admin-request-card .rounded-xl { border-radius: 8px !important; }
    .match-report-faulty .admin-request-card .px-4,
    .match-report-faulty .admin-request-card .px-3 { padding-left: 8px !important; padding-right: 8px !important; }
    .match-report-faulty .admin-request-card .py-3,
    .match-report-faulty .admin-request-card .py-2,
    .match-report-faulty .admin-request-card .py-2\.5 { padding-top: 5px !important; padding-bottom: 5px !important; }
    .match-report-faulty .admin-request-card label,
    .match-report-faulty .admin-request-card p,
    .match-report-faulty .admin-request-card span { font-size: 9px !important; line-height: 1.25 !important; }
    .match-report-faulty .admin-request-card label { font-size: 8.5px !important; margin-bottom: 3px !important; letter-spacing: 0.08em !important; }
    .match-report-faulty .admin-request-card input:not([type="checkbox"]):not([type="radio"]),
    .match-report-faulty .admin-request-card select,
    .match-report-faulty .admin-request-card textarea { min-height: 28px !important; height: 28px !important; border-radius: 7px !important; padding: 5px 8px !important; font-size: 9.5px !important; line-height: 1.2 !important; }
    .match-report-faulty .admin-request-card textarea { min-height: 44px !important; height: auto !important; }
    .match-report-faulty .admin-request-card .select2-container .select2-selection--single { min-height: 28px !important; height: 28px !important; border-radius: 7px !important; padding: 2px 8px !important; }
    .match-report-faulty .admin-request-card .select2-container .select2-selection__rendered { font-size: 9.5px !important; line-height: 1.2 !important; }
    .match-report-faulty .admin-request-card button,
    .match-report-faulty .admin-request-card .request-submit-btn { min-height: 28px !important; padding: 7px 14px !important; border-radius: 8px !important; font-size: 9px !important; }
</style>
<div class="px-1 sm:px-2 admin-request-shell match-report-faulty">
@if($isTemporaryRequest)
<div class="mb-3">
    <a href="{{ route('wt.admin.requests.create') }}" onclick="event.preventDefault(); if (window.history.length > 1) { window.history.back(); } else { window.location.href = this.href; }" class="inline-flex items-center gap-2 rounded-xl border border-stone-200 bg-white px-4 py-2 text-[10px] font-black uppercase tracking-widest text-stone-600 transition hover:bg-stone-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
        <i class="fas fa-arrow-left"></i>
        Back
    </a>
</div>
@endif
<div class="page-header-block">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h3 class="page-title-standard dark:text-[#f3f4f6]">{{ $isTemporaryRequest ? 'Executive Temporary Request (Self-Issue)' : 'Executive Request (Self-Issue)' }}</h3>
            <p class="page-subtitle-standard dark:text-slate-500">
                {{ $isTemporaryRequest
                    ? 'Create a temporary request using your own executive account details, including the quantity needed and its usage purpose.'
                    : 'Create an individual request using your own executive account details. Available walkie talkies will be assigned by ICT after approval.' }}
            </p>
        </div>
        <a href="{{ route('wt.admin.all.status', ['view' => 'requests']) }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#0284c7] px-4 py-2 text-[10px] font-black uppercase tracking-widest text-white transition hover:bg-[#734C2F]">
            <i class="fa-solid fa-list-check"></i>
            Status Tracking
        </a>
    </div>
</div>

<div class="admin-request-card bg-white dark:bg-slate-800/80 rounded-2xl shadow-sm border border-[#0284c7]/10 dark:border-slate-700/50 overflow-hidden transition-all duration-300">
    <div class="flex items-center gap-2.5 mb-5">
        <div class="bg-[#0284c7] text-white p-2.5 rounded-xl border border-[#A67B5B] shadow-inner">
            <i class="fas fa-user-check text-sm"></i>
        </div>
        <h2 class="text-xs font-black text-[#142b47] dark:text-slate-100 uppercase tracking-widest">{{ $isTemporaryRequest ? 'Temporary Self-Issue Form' : 'Self-Issue Form' }}</h2>
    </div>

    <div class="mb-5 rounded-2xl border border-[#0284c7]/15 bg-[#FDFBF7] px-4 py-3 dark:bg-slate-900/70 dark:border-slate-700">
        <p class="text-[9px] font-black uppercase tracking-[0.18em] text-[#0284c7]">ICT Assignment Notice</p>
        <p class="mt-1 text-[11px] font-semibold text-stone-600 dark:text-slate-300">
            {{ $isTemporaryRequest
                ? 'You only need to complete this temporary request form, state the quantity needed, and explain the usage purpose. ICT will review and prepare the available units later.'
                : 'You only need to complete this request form. The available walkie talkie selection is handled by ICT during approval.' }}
        </p>
    </div>

    <form action="{{ $formAction }}" method="POST" class="space-y-6">
        @csrf
        <input type="hidden" name="ownership_type" value="Individual">
        <input type="hidden" name="user_id" value="{{ $currentUser->user_id }}">
        @unless($isTemporaryRequest)
        <input type="hidden" name="event_name" value="{{ old('event_name', 'General Request') }}">
        @endunless

        <!-- 1. EXECUTIVE INFORMATION -->
        <h4 class="text-[10px] font-black text-[#0284c7] border-l-4 border-[#0284c7] pl-3 uppercase tracking-widest mb-4">1. Person Details</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <div class="rounded-2xl border border-[#0284c7]/15 bg-[#FDFBF7] dark:bg-slate-900/70 dark:border-slate-700 px-4 py-3.5">
                    <p class="text-[9px] font-black text-[#0284c7] uppercase tracking-[0.22em]">Request Routing</p>
                    <p class="mt-1 text-[11px] font-semibold text-stone-600 dark:text-slate-300">
                        This request will use your own details and go directly to ICT approval.
                    </p>
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-stone-600 dark:text-slate-400 mb-2 uppercase tracking-widest">Full Name</label>
                <input type="text" name="full_name" value="{{ old('full_name', $currentUser->full_name ?: $currentUser->username) }}" class="w-full px-4 py-2.5 rounded-xl border border-[#0284c7]/30 bg-stone-100/80 dark:bg-slate-900 dark:border-slate-700 text-[11px] font-bold outline-none transition dark:text-slate-200" readonly required>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-stone-600 dark:text-slate-400 mb-2 uppercase tracking-widest">ID No.</label>
                <input type="text" name="staff_id" value="{{ old('staff_id', $currentUser->staff_id) }}" class="w-full px-4 py-2.5 rounded-xl border border-[#0284c7]/30 bg-stone-100/80 dark:bg-slate-900 dark:border-slate-700 text-[11px] font-bold outline-none transition dark:text-slate-200" readonly required>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-stone-600 dark:text-slate-400 mb-2 uppercase tracking-widest">Department</label>
                @if($lockDepartment)
                <input type="hidden" name="department" value="{{ $selectedDepartment }}">
                <input type="text" value="{{ $selectedDepartment }}" class="w-full px-4 py-2.5 rounded-xl border border-[#0284c7]/30 bg-stone-100/80 dark:bg-slate-900 dark:border-slate-700 text-[11px] font-bold outline-none transition dark:text-slate-200" readonly>
                @else
                <select name="department" class="smart-select w-full" data-placeholder="Type or select department" required>
                    <option value=""></option>
                    @foreach($departmentOptions as $department)
                    <option value="{{ $department }}" @selected(strtoupper(trim((string) $selectedDepartment)) === strtoupper(trim((string) $department)))>{{ $department }}</option>
                    @endforeach
                    @if($selectedDepartment && !collect($departmentOptions)->contains(fn ($department) => strtoupper(trim((string) $department)) === strtoupper(trim((string) $selectedDepartment))))
                    <option value="{{ $selectedDepartment }}" selected>{{ $selectedDepartment }}</option>
                    @endif
                </select>
                @endif
            </div>
            @unless($isTemporaryRequest)
            <div>
                <label class="block text-[10px] font-bold text-stone-600 dark:text-slate-400 mb-2 uppercase tracking-widest">Date</label>
                <input type="date" name="request_date" value="{{ old('request_date', date('Y-m-d')) }}" class="w-full px-4 py-2.5 rounded-xl border border-[#0284c7]/30 bg-[#FDFBF7]/50 dark:bg-slate-900 dark:border-slate-700 text-[11px] font-bold focus:ring-2 focus:ring-[#0284c7]/20 outline-none transition dark:text-slate-200" required>
            </div>
            @endunless
        </div>

        <!-- 2. DEPLOYMENT DETAILS -->
        <h4 class="text-[10px] font-black text-[#0284c7] border-l-4 border-[#0284c7] pl-3 uppercase tracking-widest mb-4">{{ $isTemporaryRequest ? '2. Temporary Request Details' : '2. Deployment' }}</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @if($isTemporaryRequest)
            <div>
                <label class="block text-[10px] font-bold text-stone-600 dark:text-slate-400 mb-2 uppercase tracking-widest">Quantity</label>
                <div class="flex overflow-hidden rounded-xl border border-[#0284c7]/30 bg-[#FDFBF7]/50 focus-within:ring-2 focus-within:ring-[#0284c7]/20 dark:border-slate-700 dark:bg-slate-900">
                    <button type="button" class="temporary-quantity-step flex w-11 items-center justify-center border-r border-[#0284c7]/20 text-sm font-black text-stone-600 hover:bg-[#0284c7]/10 dark:border-slate-700 dark:text-slate-200" data-step="-1" aria-label="Decrease quantity">-</button>
                    <input type="number" name="quantity" min="1" max="999" inputmode="numeric" value="{{ old('quantity', 1) }}" class="w-full border-0 bg-transparent px-4 py-2.5 text-[12px] font-black text-slate-800 outline-none dark:text-slate-200" required>
                    <button type="button" class="temporary-quantity-step flex w-11 items-center justify-center border-l border-[#0284c7]/20 text-sm font-black text-stone-600 hover:bg-[#0284c7]/10 dark:border-slate-700 dark:text-slate-200" data-step="1" aria-label="Increase quantity">+</button>
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-stone-600 dark:text-slate-400 mb-2 uppercase tracking-widest">How Many Days</label>
                <div class="flex overflow-hidden rounded-xl border border-[#0284c7]/30 bg-[#FDFBF7]/50 focus-within:ring-2 focus-within:ring-[#0284c7]/20 dark:border-slate-700 dark:bg-slate-900">
                    <button type="button" class="temporary-day-step flex w-11 items-center justify-center border-r border-[#0284c7]/20 text-sm font-black text-stone-600 hover:bg-[#0284c7]/10 dark:border-slate-700 dark:text-slate-200" data-step="-1" aria-label="Decrease days">-</button>
                    <input type="number" id="temporary_duration_days" name="duration_days" min="1" max="365" inputmode="numeric" placeholder="Type days" value="{{ old('duration_days', 1) }}" class="w-full border-0 bg-transparent px-4 py-2.5 text-[12px] font-black text-slate-800 outline-none dark:text-slate-200" required>
                    <button type="button" class="temporary-day-step flex w-11 items-center justify-center border-l border-[#0284c7]/20 text-sm font-black text-stone-600 hover:bg-[#0284c7]/10 dark:border-slate-700 dark:text-slate-200" data-step="1" aria-label="Increase days">+</button>
                </div>
                <p class="mt-1 text-[9px] font-bold text-stone-400 dark:text-slate-500">Type the number of days, or use - / +. End date will update automatically.</p>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-stone-600 dark:text-slate-400 mb-2 uppercase tracking-widest">Start Date</label>
                <input type="date" id="temporary_start_date" name="request_date" value="{{ old('request_date', date('Y-m-d')) }}" class="w-full px-4 py-2.5 rounded-xl border border-[#0284c7]/30 bg-[#FDFBF7]/50 dark:bg-slate-900 dark:border-slate-700 text-[11px] font-bold focus:ring-2 focus:ring-[#0284c7]/20 outline-none transition dark:text-slate-200" required>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-stone-600 dark:text-slate-400 mb-2 uppercase tracking-widest">End Date</label>
                <input type="date" id="temporary_end_date" name="end_date" value="{{ old('end_date', old('request_date', date('Y-m-d'))) }}" class="w-full px-4 py-2.5 rounded-xl border border-[#0284c7]/30 bg-[#FDFBF7]/50 dark:bg-slate-900 dark:border-slate-700 text-[11px] font-bold focus:ring-2 focus:ring-[#0284c7]/20 outline-none transition dark:text-slate-200" required>
                <p class="mt-1 text-[9px] font-bold text-stone-400 dark:text-slate-500">Please return the walkie talkie to ICT Department on this end date.</p>
            </div>
            <div class="md:col-span-2">
                <label class="block text-[10px] font-bold text-stone-600 dark:text-slate-400 mb-2 uppercase tracking-widest">Purpose / Usage</label>
                <input type="text" name="event_name" value="{{ old('event_name') }}" placeholder="Example: Temporary use for event standby, crowd control, or daily operations" class="w-full px-4 py-2.5 rounded-xl border border-[#0284c7]/30 bg-[#FDFBF7]/50 dark:bg-slate-900 dark:border-slate-700 text-[11px] font-bold focus:ring-2 focus:ring-[#0284c7]/20 outline-none transition dark:text-slate-200" required>
            </div>
            @endif
            @unless($isTemporaryRequest)
            <div>
                <label class="block text-[10px] font-bold text-stone-600 dark:text-slate-400 mb-2 uppercase tracking-widest">Quantity</label>
                <div class="flex overflow-hidden rounded-xl border border-[#0284c7]/30 bg-[#FDFBF7]/50 focus-within:ring-2 focus-within:ring-[#0284c7]/20 dark:border-slate-700 dark:bg-slate-900">
                    <button type="button" class="temporary-quantity-step flex w-11 items-center justify-center border-r border-[#0284c7]/20 text-sm font-black text-stone-600 hover:bg-[#0284c7]/10 dark:border-slate-700 dark:text-slate-200" data-step="-1" aria-label="Decrease quantity">-</button>
                    <input type="number" name="quantity" min="1" max="999" inputmode="numeric" value="{{ old('quantity', 1) }}" class="w-full border-0 bg-transparent px-4 py-2.5 text-[12px] font-black text-slate-800 outline-none dark:text-slate-200" required>
                    <button type="button" class="temporary-quantity-step flex w-11 items-center justify-center border-l border-[#0284c7]/20 text-sm font-black text-stone-600 hover:bg-[#0284c7]/10 dark:border-slate-700 dark:text-slate-200" data-step="1" aria-label="Increase quantity">+</button>
                </div>
            </div>
            @endunless
            <div>
                <label class="block text-[10px] font-bold text-stone-600 dark:text-slate-400 mb-2 uppercase tracking-widest">Sector</label>
                <select name="sector" class="smart-select w-full" data-placeholder="Type or select sector" required>
                    <option value=""></option>
                    @foreach($sectorOptions as $sector)
                    <option value="{{ $sector }}" @selected(old('sector') === $sector)>{{ $sector }}</option>
                    @endforeach
                    @if(old('sector') && !in_array(old('sector'), $sectorOptions, true))
                    <option value="{{ old('sector') }}" selected>{{ old('sector') }}</option>
                    @endif
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-stone-600 dark:text-slate-400 mb-2 uppercase tracking-widest">Bay <span class="text-stone-400">(Optional)</span></label>
                <select name="bay_from" class="smart-select w-full" data-placeholder="Type number only, e.g. 3">
                    <option value=""></option>
                    @foreach($bayOptions as $bay)
                    <option value="{{ $bay }}" @selected(old('bay_from') === $bay)>{{ $bay }}</option>
                    @endforeach
                    @if(old('bay_from') && !in_array(old('bay_from'), $bayOptions, true))
                    <option value="{{ old('bay_from') }}" selected>{{ old('bay_from') }}</option>
                    @endif
                </select>
                @error('bay_from')
                    <div class="text-red-600 text-xs font-bold mt-2">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label class="block text-[10px] font-bold text-stone-600 dark:text-slate-400 mb-2 uppercase tracking-widest">Location</label>
                <select name="location" class="smart-select w-full" data-placeholder="Type or select location" required>
                    <option value=""></option>
                    @foreach($locationOptions as $location)
                    <option value="{{ $location }}" @selected(old('location') === $location)>{{ $location }}</option>
                    @endforeach
                    @if(old('location') && !in_array(old('location'), $locationOptions, true))
                    <option value="{{ old('location') }}" selected>{{ old('location') }}</option>
                    @endif
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-stone-600 dark:text-slate-400 mb-2 uppercase tracking-widest">Justifications</label>
                <textarea name="justifications" rows="1" placeholder="Reason for request..." class="w-full px-4 py-2.5 rounded-xl border border-[#0284c7]/30 bg-[#FDFBF7]/50 dark:bg-slate-900 dark:border-slate-700 text-[11px] font-bold focus:ring-2 focus:ring-[#0284c7]/20 outline-none transition dark:text-slate-200" required>{{ old('justifications') }}</textarea>
            </div>
        </div>

        <!-- 3. HANDOVER / PICKUP -->
        <h4 class="text-[10px] font-black text-[#0284c7] border-l-4 border-[#0284c7] pl-3 uppercase tracking-widest mb-4">{{ $isTemporaryRequest ? '3. Handover / Pickup' : '3. Handover / Pickup' }}</h4>
        <input type="hidden" name="pickup_method" value="self">
        <div class="rounded-2xl border border-sky-200 bg-sky-50 px-4 py-3 dark:border-sky-900/60 dark:bg-sky-950/30">
            <p class="text-[10px] font-black uppercase tracking-widest text-sky-700 dark:text-sky-300">Pickup Info</p>
            <p class="mt-1 text-[11px] font-bold leading-5 text-sky-800 dark:text-sky-200">
                @if($isTemporaryRequest)
                    Pick up the approved walkie talkie for {{ strtoupper($currentUser->full_name ?: $currentUser->username) }} at ICT Department immediately after ICT approves this request. Please return it to ICT Department on the selected end date.
                @else
                    Pick up the approved walkie talkie for {{ strtoupper($currentUser->full_name ?: $currentUser->username) }} at ICT Department immediately after ICT approves this request.
                @endif
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-[10px] font-bold text-stone-600 dark:text-slate-400 mb-2 uppercase tracking-widest">Preferred Pickup Date & Time</label>
                <input type="datetime-local" name="requested_pickup_at" value="{{ old('requested_pickup_at') }}" class="w-full px-4 py-2.5 rounded-xl border border-[#0284c7]/30 bg-[#FDFBF7]/50 dark:bg-slate-900 dark:border-slate-700 text-[11px] font-bold focus:ring-2 focus:ring-[#0284c7]/20 outline-none transition dark:text-slate-200" required>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-stone-600 dark:text-slate-400 mb-2 uppercase tracking-widest">Remark <span class="text-stone-400">(Optional)</span></label>
                <input type="text" name="pickup_note" value="{{ old('pickup_note') }}" placeholder="Example: Pickup by department representative" class="w-full px-4 py-2.5 rounded-xl border border-[#0284c7]/30 bg-[#FDFBF7]/50 dark:bg-slate-900 dark:border-slate-700 text-[11px] font-bold focus:ring-2 focus:ring-[#0284c7]/20 outline-none transition dark:text-slate-200">
            </div>
        </div>

        <div class="request-submit-row pt-8 flex justify-end">
            <button type="submit" class="request-submit-btn bg-[#0284c7] text-white px-10 py-3.5 rounded-2xl font-black text-[11px] tracking-widest uppercase hover:bg-[#724D31] transition shadow-xl shadow-[#0284c7]/20 flex items-center gap-3 border border-[#A67B5B]">
                {{ $isTemporaryRequest ? 'Submit Temporary Request' : 'Submit To ICT' }} <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </form>
</div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        function parseDate(value) {
            const date = new Date(value + 'T00:00:00');
            return Number.isNaN(date.getTime()) ? null : date;
        }

        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function clampDuration(value) {
            const parsed = parseInt(value, 10);
            if (Number.isNaN(parsed) || parsed < 1) return 1;
            return Math.min(parsed, 365);
        }

        function clampQuantity(value) {
            const parsed = parseInt(value, 10);
            if (Number.isNaN(parsed) || parsed < 1) return 1;
            return Math.min(parsed, 999);
        }

        function updateEndDateFromDuration() {
            const startValue = document.getElementById('temporary_start_date')?.value;
            const endField = document.getElementById('temporary_end_date');
            const durationField = document.getElementById('temporary_duration_days');

            if (!startValue || !endField || !durationField) {
                return;
            }

            const startDate = parseDate(startValue);
            if (!startDate) return;

            const durationDays = clampDuration(durationField.value);
            durationField.value = durationDays;
            endField.min = startValue;
            const endDate = new Date(startDate);
            endDate.setDate(startDate.getDate() + durationDays - 1);
            endField.value = formatDate(endDate);
        }

        function syncTemporaryDuration() {
            const startValue = document.getElementById('temporary_start_date')?.value;
            const endField = document.getElementById('temporary_end_date');
            const durationField = document.getElementById('temporary_duration_days');

            if (!startValue || !endField || !durationField) {
                return;
            }

            endField.min = startValue;

            if (!endField.value || endField.value < startValue) {
                endField.value = startValue;
            }

            const startDate = parseDate(startValue);
            const endDate = parseDate(endField.value);
            if (!startDate || !endDate) return;

            const diffTime = endDate.getTime() - startDate.getTime();
            const diffDays = Number.isNaN(diffTime) ? 1 : Math.floor(diffTime / 86400000) + 1;
            durationField.value = clampDuration(diffDays);
        }

        function initTagSelect(selector) {
            $(selector).select2({
                tags: true,
                width: '100%',
                placeholder: function() {
                    return $(this).data('placeholder') || 'Type or select option';
                },
                allowClear: true,
                createTag: function(params) {
                    const term = $.trim(params.term);
                    if (term === '') return null;
                    return { id: term.toUpperCase(), text: term.toUpperCase(), newTag: true };
                },
                insertTag: function(data, tag) {
                    data.unshift(tag);
                }
            });
        }

        initTagSelect('.smart-select');
        $('.temporary-day-step').on('click', function () {
            const durationField = document.getElementById('temporary_duration_days');
            if (!durationField) return;

            const step = parseInt(this.dataset.step || '0', 10);
            durationField.value = clampDuration(clampDuration(durationField.value) + step);
            updateEndDateFromDuration();
        });

        $('.temporary-quantity-step').on('click', function () {
            const quantityField = this.parentElement?.querySelector('input[name="quantity"]');
            if (!quantityField) return;

            const step = parseInt(this.dataset.step || '0', 10);
            quantityField.value = clampQuantity(clampQuantity(quantityField.value) + step);
            quantityField.dispatchEvent(new Event('change', { bubbles: true }));
        });

        updateEndDateFromDuration();
        $('#temporary_duration_days').on('input change', updateEndDateFromDuration);
        $('#temporary_start_date').on('change', updateEndDateFromDuration);
        $('#temporary_end_date').on('change', syncTemporaryDuration);
    });
</script>
@endpush


