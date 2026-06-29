@extends(request()->routeIs('wt.admin.*') ? 'wt.layouts.admin' : 'wt.layouts.user')

@php
    $routePrefix = request()->routeIs('wt.admin.*') ? 'wt.admin' : 'wt.user';
    $draftRecord = $draftRecord ?? null;
    $draftHandoverAt = old('handover_at', $draftRecord?->handover_at ? \Carbon\Carbon::parse($draftRecord->handover_at)->format('Y-m-d\TH:i') : '');
    $draftPickupAt = old('pickup_at', $draftRecord?->pickup_at ? \Carbon\Carbon::parse($draftRecord->pickup_at)->format('Y-m-d\TH:i') : '');
    $splitDamageDateTime = function ($value) {
        if (! $value) {
            return ['year' => '', 'month' => '', 'day' => '', 'time' => ''];
        }

        try {
            $date = \Carbon\Carbon::parse($value);

            return [
                'year' => $date->format('Y'),
                'month' => $date->format('m'),
                'day' => $date->format('d'),
                'time' => $date->format('H:i'),
            ];
        } catch (\Throwable $exception) {
            return ['year' => '', 'month' => '', 'day' => '', 'time' => ''];
        }
    };
    $draftHandoverParts = $splitDamageDateTime($draftHandoverAt);
    $draftPickupParts = $splitDamageDateTime($draftPickupAt);
    $damageMonthOptions = [
        '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
        '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Aug',
        '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec',
    ];
    $damageYearOptions = range(now()->year, now()->addYears(2)->year);
    $damageTimeOptions = [];
    for ($hour = 8; $hour <= 18; $hour++) {
        foreach ([0, 30] as $minute) {
            if ($hour === 18 && $minute === 30) {
                continue;
            }
            $damageTimeOptions[] = sprintf('%02d:%02d', $hour, $minute);
        }
    }
@endphp

@section('title', 'Report Faulty')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .smart-select + .select2-container,
    .admin-select + .select2-container {
        width: 100% !important;
    }
    .smart-select + .select2-container,
    .admin-select + .select2-container {
        width: 100% !important;
    }
    .smart-select + .select2-container .select2-selection--single,
    .admin-select + .select2-container .select2-selection--single {
        min-height: 42px;
        border-radius: 0.75rem !important;
        border: 1px solid var(--border) !important;
        background: var(--body-bg) !important;
        padding: 6px 12px !important;
        display: flex !important;
        align-items: center !important;
    }
    .smart-select + .select2-container .select2-selection__rendered,
    .admin-select + .select2-container .select2-selection__rendered {
        color: var(--text) !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        padding-left: 0 !important;
        padding-right: 24px !important;
        line-height: 1.3 !important;
        text-transform: uppercase;
    }
    .smart-select + .select2-container .select2-selection__placeholder,
    .admin-select + .select2-container .select2-selection__placeholder {
        color: var(--muted) !important;
    }
    .smart-select + .select2-container .select2-selection__arrow,
    .admin-select + .select2-container .select2-selection__arrow {
        height: 100% !important;
        right: 12px !important;
    }
    #managed_user_select + .select2-container .select2-selection--single {
        min-height: 38px;
        border-radius: 9px !important;
        border: 1px solid var(--border) !important;
        background: var(--surface) !important;
        box-shadow: none !important;
    }
    #managed_user_select + .select2-container .select2-selection__rendered {
        width: 100%;
        padding-right: 28px !important;
    }
    .ownership-name-help {
        margin-top: 2px !important;
        line-height: 1.15 !important;
    }
    .faulty-accordion {
        display: grid;
        gap: 10px;
    }
    .damage-form-page .damage-card.table-card {
        width: min(100%, 1120px) !important;
        margin-left: auto !important;
        margin-right: auto !important;
    }
    .faulty-accordion-section {
        border: 1px solid var(--border);
        border-radius: 12px;
        background: rgba(15, 23, 42, 0.18);
        overflow: hidden;
    }
    .faulty-accordion-toggle {
        width: 100%;
        min-height: 44px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 14px;
        border: 0;
        background: rgba(2, 132, 199, 0.08);
        color: var(--accent);
        text-align: left;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .12em;
        text-transform: uppercase;
        cursor: pointer;
    }
    .faulty-accordion-toggle:hover,
    .faulty-accordion-toggle[aria-expanded="true"] {
        background: rgba(2, 132, 199, 0.16);
    }
    .faulty-accordion-title {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 0;
    }
    .faulty-accordion-title::before {
        content: "";
        width: 3px;
        height: 18px;
        border-radius: 999px;
        background: var(--accent);
        flex: 0 0 auto;
    }
    .faulty-accordion-icon {
        color: var(--muted);
        transition: transform .18s ease;
        flex: 0 0 auto;
    }
    .faulty-accordion-toggle[aria-expanded="true"] .faulty-accordion-icon {
        transform: rotate(180deg);
        color: var(--accent);
    }
    .faulty-accordion-panel {
        padding: 16px 14px 18px;
    }
    .faulty-accordion-panel[hidden] {
        display: none !important;
    }
    .staff-account-selection,
    .staff-account-option {
        display: flex;
        align-items: center;
        gap: 0;
        min-width: 0;
    }
    .staff-account-avatar {
        display: none;
        align-items: center;
        justify-content: center;
        width: 22px;
        height: 22px;
        border-radius: 7px;
        background: var(--body-bg);
        border: 1px solid var(--border);
        color: var(--accent);
        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.04em;
        flex: 0 0 auto;
    }
    .staff-account-text {
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 0;
    }
    .staff-account-name {
        color: var(--text);
        font-size: 11px;
        font-weight: 900;
        line-height: 1.15;
        text-transform: uppercase;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .staff-account-meta {
        color: var(--muted);
        font-size: 8px;
        font-weight: 900;
        letter-spacing: 0.12em;
        line-height: 1.15;
        text-transform: uppercase;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .staff-account-new {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #0284c7;
    }
    .staff-account-dropdown.select2-dropdown {
        border-radius: 0 0 14px 14px !important;
        border-color: var(--border) !important;
        background: var(--surface) !important;
        box-shadow: 0 22px 46px rgba(15,23,42,.16);
    }
    .staff-account-dropdown .select2-search--dropdown,
    .staff-account-dropdown .select2-results,
    .staff-account-dropdown .select2-results > .select2-results__options {
        background: var(--surface) !important;
    }
    .staff-account-dropdown .select2-search__field {
        background: var(--body-bg) !important;
        color: var(--text) !important;
        border: 2px solid var(--accent) !important;
        border-radius: 11px !important;
        padding: 9px 11px !important;
        font-size: 12px !important;
        font-weight: 800 !important;
    }
    .staff-account-dropdown .select2-results__option {
        background: var(--surface) !important;
        color: var(--text) !important;
        padding: 9px 16px !important;
    }
    .staff-account-dropdown .select2-results__option--selected,
    .staff-account-dropdown .select2-results__option[aria-selected=true] {
        background: var(--body-bg) !important;
        color: var(--text) !important;
    }
    .staff-account-dropdown .select2-results__option--highlighted.select2-results__option--selectable {
        background: var(--body-bg) !important;
        color: var(--text) !important;
    }
    .select2-dropdown,
    .damage-select2-dropdown {
        border: 1px solid var(--border) !important;
        border-radius: 14px !important;
        overflow: hidden;
        box-shadow: 0 18px 40px rgba(15,23,42,.12);
    }
    .select2-search--dropdown,
    .damage-select2-dropdown .select2-search--dropdown {
        padding: 10px !important;
        background: var(--surface);
    }
    .select2-search--dropdown .select2-search__field,
    .damage-select2-dropdown .select2-search--dropdown .select2-search__field {
        border: 1px solid var(--border) !important;
        border-radius: 10px !important;
        padding: 8px 10px !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        text-transform: uppercase;
        background: var(--body-bg) !important;
        color: var(--text) !important;
    }
    .select2-results__option,
    .damage-select2-dropdown .select2-results__option {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        padding: 10px 12px;
        background: var(--surface) !important;
        color: var(--text) !important;
    }
    .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable,
    .damage-select2-dropdown .select2-results__option--highlighted.select2-results__option--selectable {
        background: var(--accent) !important;
        color: #fff !important;
    }
    .select2-container--default .select2-results__option--selected,
    .select2-container--default .select2-results__option[aria-selected=true],
    .damage-select2-dropdown .select2-results__option--selected,
    .damage-select2-dropdown .select2-results__option[aria-selected=true] {
        background: var(--body-bg) !important;
        color: var(--text) !important;
    }
    .damage-form-page {
        --damage-compact-width: 980px;
        font-size: 10px !important;
    }
    .damage-form-page > .mb-4,
    .damage-form-page > .px-2,
    .damage-form-page > .px-2.mt-6 {
        width: 100% !important;
        max-width: var(--damage-compact-width) !important;
        margin-left: auto !important;
        margin-right: auto !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
    .damage-form-page .damage-card {
        border-radius: 10px !important;
        padding: 10px 12px !important;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.04) !important;
    }
    .damage-form-page form {
        margin-top: 6px !important;
    }
    .damage-form-page h2 {
        font-size: 11px !important;
        line-height: 1.2 !important;
    }
    .damage-form-page h3 {
        margin-top: 8px !important;
        margin-bottom: 6px !important;
        padding-bottom: 5px !important;
        font-size: 9.5px !important;
        line-height: 1.2 !important;
        letter-spacing: 0.14em !important;
    }
    .damage-form-page .damage-status-btn {
        min-height: 40px !important;
        border-radius: 10px !important;
        padding: 10px 14px !important;
        font-size: 9px !important;
        letter-spacing: 0.04em !important;
    }
    .damage-form-page .grid {
        gap: 7px !important;
    }
    .damage-form-page .mb-6 {
        margin-bottom: 9px !important;
    }
    .damage-form-page .mb-4 {
        margin-bottom: 7px !important;
    }
    .damage-form-page .my-5 {
        margin-top: 8px !important;
        margin-bottom: 8px !important;
    }
    .damage-form-page .mt-5,
    .damage-form-page .mt-4 {
        margin-top: 7px !important;
    }
    .damage-form-page .mt-3,
    .damage-form-page .mt-2,
    .damage-form-page .mt-1 {
        margin-top: 4px !important;
    }
    .damage-form-page .p-5,
    .damage-form-page .md\:p-6,
    .damage-form-page .p-4 {
        padding: 8px !important;
    }
    .damage-form-page .px-4 {
        padding-left: 8px !important;
        padding-right: 8px !important;
    }
    .damage-form-page .py-3 {
        padding-top: 5px !important;
        padding-bottom: 5px !important;
    }
    .damage-form-page .px-3 {
        padding-left: 8px !important;
        padding-right: 8px !important;
    }
    .damage-form-page .py-2,
    .damage-form-page .py-2\.5 {
        padding-top: 5px !important;
        padding-bottom: 5px !important;
    }
    .damage-form-page label,
    .damage-form-page .damage-check-text,
    .damage-form-page p {
        font-size: 11.5px !important;
        line-height: 1.35 !important;
        color: var(--text) !important;
    }
    .damage-form-page label {
        font-size: 13px !important;
        margin-bottom: 6px !important;
        letter-spacing: 0.08em !important;
        color: var(--muted) !important;
    }
    .damage-form-page .damage-card,
    .damage-form-page .damage-panel {
        background: var(--surface) !important;
        border-color: var(--border) !important;
    }
    .damage-form-page .damage-muted-panel {
        background: var(--body-bg) !important;
        border-color: var(--border) !important;
    }
    .damage-form-page .theme-note-panel {
        background: var(--body-bg) !important;
        border-color: var(--border) !important;
        color: var(--text) !important;
    }
    .damage-form-page .theme-note-panel p {
        color: var(--text) !important;
    }
    .damage-form-page .theme-note-panel p:first-child {
        color: var(--accent) !important;
    }
    .damage-form-page input:not([type="checkbox"]):not([type="file"]),
    .damage-form-page select,
    .damage-form-page textarea {
        min-height: 28px !important;
        height: 28px !important;
        border-radius: 7px !important;
        padding: 5px 8px !important;
        font-size: 9.5px !important;
        line-height: 1.2 !important;
        border: 1px solid var(--border) !important;
        background: var(--surface) !important;
        color: var(--text) !important;
    }
    .damage-form-page textarea {
        min-height: 44px !important;
        height: auto !important;
    }
    .damage-form-page .smart-select + .select2-container .select2-selection--single,
    .damage-form-page .admin-select + .select2-container .select2-selection--single {
        min-height: 28px !important;
        height: 28px !important;
        border-radius: 7px !important;
        padding: 2px 8px !important;
    }
    .damage-form-page .smart-select + .select2-container .select2-selection__rendered,
    .damage-form-page .admin-select + .select2-container .select2-selection__rendered {
        font-size: 9.5px !important;
    }
    .damage-form-page .damage-muted-panel,
    .damage-form-page .rounded-xl {
        border-radius: 8px !important;
    }
    .damage-form-page .w-10.h-10 {
        width: 26px !important;
        height: 26px !important;
    }
    .damage-form-page .replacement-trigger-btn {
        padding: 6px 9px !important;
        border-radius: 7px !important;
        font-size: 8.5px !important;
    }
    .damage-form-page button[type="submit"] {
        padding: 7px 14px !important;
        border-radius: 8px !important;
        font-size: 9px !important;
    }
    .damage-form-page .inline-flex.rounded-xl,
    .damage-form-page .inline-flex.rounded-lg {
        min-height: 26px !important;
        padding: 5px 8px !important;
        border-radius: 7px !important;
        font-size: 8.5px !important;
    }
    .damage-form-page input[type="checkbox"] {
        width: 12px !important;
        height: 12px !important;
    }
    .damage-form-page .flex.items-center.gap-2\.5 {
        gap: 8px !important;
        margin-bottom: 6px !important;
    }
    .damage-form-page .text-xs,
    .damage-form-page .text-\[11px\],
    .damage-form-page .text-\[10px\],
    .damage-form-page .text-\[9px\] {
        font-size: 9px !important;
    }
    .damage-form-page .damage-check-text {
        font-size: 13px !important;
        line-height: 1.3 !important;
    }
    .damage-form-page .problem-checklist-grid {
        row-gap: 3px !important;
        column-gap: 28px !important;
    }
    @media (max-width: 768px) {
        .damage-form-page .damage-card {
            padding: 8px !important;
        }
    }
    .replacement-trigger-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        border-radius: 12px;
        border: 1px solid var(--border);
        background: var(--body-bg);
        color: var(--accent);
        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        transition: all 0.18s ease;
        cursor: pointer;
    }
    .replacement-trigger-btn:hover {
        background: var(--surface);
        border-color: var(--accent);
    }
    .replacement-chip {
        display: none;
        align-items: center;
        gap: 8px;
        padding: 9px 12px;
        border-radius: 999px;
        background: rgba(22, 101, 52, 0.08);
        border: 1px solid rgba(22, 101, 52, 0.18);
        color: #166534;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.04em;
    }
    .replacement-chip.is-visible {
        display: inline-flex;
    }
    .replacement-preview-card {
        display: none;
        border-radius: 18px;
        border: 1px solid rgba(34,197,94,.2);
        background: var(--body-bg);
        padding: 16px;
        margin-bottom: 22px;
        box-shadow: 0 4px 16px rgba(15,23,42,.06);
    }
    .replacement-preview-card.is-visible {
        display: block;
    }
    .replacement-preview-label {
        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #166534;
    }
    .replacement-preview-value {
        margin-top: 6px;
        font-size: 11px;
        font-weight: 800;
        color: #14532d;
        line-height: 1.5;
    }
    .replacement-preview-summary {
        border-radius: 14px;
        border: 1px solid rgba(34,197,94,.2);
        background: var(--surface);
        padding: 14px 16px;
    }
    .replacement-preview-grid-card {
        border-radius: 14px;
        border: 1px solid var(--border);
        background: var(--surface);
        padding: 14px 16px;
    }
    .replacement-preview-grid-card.is-accent {
        background: rgba(34,197,94,.06);
        border-color: rgba(34,197,94,.2);
    }
    .replacement-preview-saved-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-radius: 999px;
        border: 1px solid rgba(99,102,241,.3);
        background: rgba(99,102,241,.08);
        padding: 8px 14px;
        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #4338ca;
    }
    .replacement-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.55);
        backdrop-filter: blur(4px);
        display: none;
        align-items: flex-start;
        justify-content: center;
        padding: 130px 18px 18px;
        z-index: 1200;
        overflow-y: auto;
    }
    .replacement-modal-backdrop.is-open {
        display: flex;
    }
    .replacement-modal-card {
        width: min(100%, 620px);
        max-height: calc(100vh - 148px);
        border-radius: 22px;
        border: 1px solid var(--border);
        background: var(--surface);
        box-shadow: 0 28px 60px rgba(15,23,42,.22);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    .replacement-modal-body {
        flex: 1 1 auto;
        min-height: 0;
        overflow-y: auto;
    }
    .replacement-agreement-box {
        border-radius: 16px;
        border: 1px solid rgba(245, 158, 11, 0.26);
        background: rgba(255, 251, 235, 0.72);
        padding: 12px;
    }
    .replacement-agreement-error {
        display: none;
        margin-top: 8px;
        color: #b91c1c;
        font-size: 10px;
        font-weight: 900;
    }
    .replacement-agreement-error.is-visible {
        display: block;
    }
    .dark .replacement-modal-card {
        background: #1e293b;
        border-color: #334155;
    }
    .dark .replacement-agreement-box {
        background: rgba(120, 53, 15, 0.18);
        border-color: rgba(251, 191, 36, 0.28);
    }
    .replacement-item-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 12px;
    }
    .replacement-item-btn {
        padding: 9px 12px;
        border-radius: 999px;
        border: 1px solid var(--border);
        background: var(--surface);
        color: var(--muted);
        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        transition: all 0.18s ease;
        cursor: pointer;
    }
    .replacement-item-btn:hover {
        border-color: var(--accent);
        color: var(--accent);
        background: var(--body-bg);
    }
    .replacement-item-btn.is-active {
        border-color: var(--accent);
        background: var(--accent);
        color: #fff;
        box-shadow: 0 4px 12px rgba(2,132,199,.25);
    }

    @media (max-width: 640px) {
        .replacement-modal-backdrop {
            padding: 84px 12px 12px;
        }

        .replacement-modal-card {
            max-height: calc(100vh - 96px);
        }
    }
</style>
@endpush

@section('content')
@php
    $isAdminRoute = request()->routeIs('wt.admin.*');
    $mode = $mode ?? ($isAdminRoute ? 'self' : 'self');
    $currentUser = $currentUser ?? auth('wt')->user();
    $managedUsers = $managedUsers ?? collect();
    $responsibleWalkies = $responsibleWalkies ?? collect();
    $responsibleWalkieAssignments = $responsibleWalkieAssignments ?? collect();
    $submittedRecord = $submittedRecord ?? null;
    $prefillDamage = $prefillDamage ?? [];
    $prefillReporterName = strtoupper((string) ($prefillDamage['reporter_name'] ?? ''));
    $prefillDepartment = strtoupper((string) ($prefillDamage['department'] ?? ''));
    $prefillOwnershipType = strtoupper((string) ($prefillDamage['ownership_type'] ?? ''));
    $prefillSharedWith = strtoupper((string) ($prefillDamage['shared_with'] ?? ''));
    $prefillSector = strtoupper((string) ($prefillDamage['sector'] ?? ''));
    $prefillBayFrom = strtoupper((string) ($prefillDamage['bay_from'] ?? ''));
    $prefillLocation = strtoupper((string) ($prefillDamage['location'] ?? ''));
    $prefillModel = strtoupper((string) ($prefillDamage['model'] ?? ''));
    $prefillRadioId = strtoupper((string) ($prefillDamage['radio_id'] ?? ''));
    $prefillSerialNumber = strtoupper((string) ($prefillDamage['serial_number'] ?? ''));
    $faultyStatusRoute = $isAdminRoute
        ? route('wt.admin.all.status', ['view' => 'damages'])
        : (\Illuminate\Support\Facades\Route::has('user.all.status') ? route('wt.user.all.status', ['view' => 'damages']) : '#');
    $departmentOptions = $formOptionLists['departments'] ?? [];
    $sectorOptions = $formOptionLists['sectors'] ?? [];
    $locationOptions = $formOptionLists['locations'] ?? [];
    $bayOptions = $formOptionLists['bays'] ?? [];
    $personMetaByName = $formOptionLists['person_meta_by_name'] ?? [];
    $managedUserNames = $managedUsers
        ->map(fn ($user) => strtoupper((string) ($user->full_name ?: $user->username)))
        ->filter()
        ->values();
    $extraManagedNames = collect($formOptionLists['ownership_names'] ?? [])
        ->merge($formOptionLists['names'] ?? [])
        ->map(fn ($name) => strtoupper(trim((string) $name)))
        ->filter()
        ->reject(fn ($name) => $managedUserNames->contains($name))
        ->unique()
        ->sort()
        ->values();
    $selectedProblems = old('problem_possible', $draftRecord && $draftRecord->problem_possible ? array_map('trim', explode(',', $draftRecord->problem_possible)) : []);
    $draftOtherProblem = collect($selectedProblems)->first(fn ($item) => str_starts_with($item, 'OTHER:'));
    $selectedProblems = collect($selectedProblems)->reject(fn ($item) => str_starts_with($item, 'OTHER:'))->values()->all();
    $draftReplacementRequested = old('request_replacement', $draftRecord && $draftRecord->remarks && str_contains($draftRecord->remarks, 'REPLACEMENT REQUESTED') ? '1' : '0');
    $draftTemporarySpareRequested = old('need_temporary_spare', $draftRecord && ! is_null($draftRecord->temporary_spare_requested) ? ($draftRecord->temporary_spare_requested ? '1' : '0') : '');
    $draftTemporarySpareNote = old('temporary_spare_request_note', $draftRecord?->temporary_spare_request_note ?? '');
    $draftReplacementNote = old('replacement_note', $draftRecord && $draftRecord->remarks && str_contains($draftRecord->remarks, 'REPLACEMENT REQUESTED:')
        ? trim(\Illuminate\Support\Str::after($draftRecord->remarks, 'REPLACEMENT REQUESTED:'))
        : '');
    $replacementAccessoryOptions = [
        'REMOTE SPEAKER / PALM MIC',
        'ANTENNA',
        'BATTERY',
        'BELT CLIP',
        'DUST COVER',
        'CHARGER / ADAPTER',
        'VOLUME KNOB / CHANNEL KNOB',
        'CARRYING CASE',
    ];
    $replacementAccessoryAliases = [
        'REMOTE SPEAKER MIC' => 'REMOTE SPEAKER / PALM MIC',
        'SINGLE UNIT CHARGER' => 'CHARGER / ADAPTER',
    ];
    $draftReplacementItems = [];
    $draftReplacementFreeNote = $draftReplacementNote;
    $draftReplacementOtherItem = '';
    $oldRecipientDetails = collect(old('recipient_details', []))->values();
    $oldDeviceDetails = collect(old('device_details', []))->values();

    if ($oldDeviceDetails->isEmpty() && ($prefillModel || $prefillRadioId || $prefillSerialNumber)) {
        $oldDeviceDetails = collect([[
            'model' => $prefillModel,
            'radio_id' => $prefillRadioId,
            'serial_number' => $prefillSerialNumber,
        ]]);
    }

    if ($draftReplacementNote !== '' && preg_match('/^\[ITEMS\]\s*(.*?)\s*(?:\|\|\s*(.*))?$/', $draftReplacementNote, $matches)) {
        $parsedReplacementItems = collect(explode(',', $matches[1]))
            ->map(fn ($item) => strtoupper(trim($item)))
            ->map(fn ($item) => $replacementAccessoryAliases[$item] ?? $item)
            ->filter(fn ($item) => $item !== '')
            ->values();

        $draftReplacementOtherItem = $parsedReplacementItems
            ->first(fn ($item) => str_starts_with($item, 'OTHERS:'));

        $draftReplacementItems = $parsedReplacementItems
            ->reject(fn ($item) => str_starts_with($item, 'OTHERS:'))
            ->filter(fn ($item) => in_array($item, $replacementAccessoryOptions, true))
            ->values()
            ->all();

        $draftReplacementFreeNote = isset($matches[2]) ? trim($matches[2]) : '';
    }
@endphp
<div class="damage-form-page">
<div style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:18px">
    <div>
        <div style="font-size:16px;font-weight:800;color:var(--text)">Report Faulty</div>
        <p style="margin-top:4px;font-size:12px;color:var(--muted)">Submit a maintenance request for faulty or damaged Walkie Talkies.</p>
    </div>
    <a href="{{ $faultyStatusRoute }}" class="btn-primary-custom">
        <i class="fa-solid fa-list-check"></i> Status Tracking
    </a>
</div>

@if(session('error'))
<div class="alert-danger-custom mb-4"><i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}</div>
@endif

@if(session('success'))
<div class="alert-success-custom mb-4"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
@endif

@if($errors->any())
<div class="alert-danger-custom mb-4"><i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}</div>
@endif

<div class="damage-card table-card" style="padding:20px 22px">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
        <div style="background:var(--accent);color:#fff;padding:8px;border-radius:8px;display:flex;align-items:center">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <h2 style="font-size:12px;font-weight:900;text-transform:uppercase;letter-spacing:.1em;color:var(--text);margin:0">Walkie Talkie Repair Form</h2>
    </div>

    <form action="{{ $isAdminRoute ? route($routePrefix . '.damages.store', ['mode' => $mode]) : route($routePrefix . '.damages.store') }}" method="POST" enctype="multipart/form-data" class="mt-4">
        @csrf
        <input type="hidden" name="draft_id" value="{{ old('draft_id', $draftRecord->maintenance_id ?? '') }}">
        <input type="hidden" name="reporter_name" id="reporter_name" value="{{ strtoupper(old('reporter_name', ($isAdminRoute && $mode === 'staff') ? ($draftRecord->reporter_name ?? $prefillReporterName) : ($draftRecord->reporter_name ?? ($currentUser->full_name ?: $currentUser->username)))) }}">
        <input type="hidden" name="request_replacement" id="request_replacement" value="{{ $draftReplacementRequested }}">
        <input type="hidden" name="replacement_note" id="replacement_note" value="{{ $draftReplacementNote }}">
        @if($isAdminRoute && $mode === 'staff')
        <input type="hidden" name="user_id" id="target_user_id" value="{{ old('user_id', '') }}">
        @endif

        @if($isAdminRoute)
        <h3 style="font-size:10px;font-weight:900;text-transform:uppercase;letter-spacing:.12em;color:var(--accent);padding-bottom:8px;margin-bottom:16px;border-bottom:1px solid var(--border);padding-left:10px;border-left:3px solid var(--accent)">1. Executive Details</h3>
        <div class="wt-form-row mb-3">
            <div>
                <label class="form-label">Executive Name</label>
                <input type="text" id="manager_name_display" value="{{ strtoupper(auth('wt')->user()->full_name ?: auth('wt')->user()->username) }}" class="form-control" style="text-transform:uppercase;background:var(--body-bg)" readonly>
            </div>
            <div>
                <label class="form-label">Executive Staff ID</label>
                <input type="text" value="{{ strtoupper(auth('wt')->user()->staff_id ?: '-') }}" class="form-control" style="text-transform:uppercase;background:var(--body-bg)" readonly>
            </div>
            <div>
                <label class="form-label">Executive Department</label>
                <input type="text" id="manager_department_display" value="{{ strtoupper(auth('wt')->user()->department ?: 'GENERAL') }}" class="form-control" style="text-transform:uppercase;background:var(--body-bg)" readonly>
            </div>
            <div>
                <label class="form-label">Executive Phone No</label>
                <input type="text" value="{{ old('manager_phone_display', auth('wt')->user()->phone_no ?: '-') }}" class="form-control" style="background:var(--body-bg)" readonly>
            </div>
        </div>
        @endif

        @if($isAdminRoute && $mode === 'staff')
            <input type="hidden" name="quantity" id="damage_quantity" value="1">
        @endif

        <div data-faulty-ownership-section></div>
        {{-- Reporter Details --}}
        <h3 style="font-size:10px;font-weight:900;text-transform:uppercase;letter-spacing:.12em;color:var(--accent);padding-bottom:8px;margin-bottom:16px;border-bottom:1px solid var(--border);padding-left:10px;border-left:3px solid var(--accent)">{{ $isAdminRoute && $mode === 'staff' ? '3. Ownership Information' : 'Reporter Information' }}</h3>
        <div class="theme-note-panel mb-4 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3">
            <p class="text-[10px] font-black uppercase tracking-widest text-sky-700">Profile Note</p>
            <p class="mt-1 text-[10px] font-bold leading-5 text-sky-800">
                @if($isAdminRoute && $mode === 'staff')
                    Search an existing ownership name or type a new one. After submission, the contact will be saved for future on-behalf reports.
                @else
                    If you need to update personal details such as name, department, or phone number, please go to <span class="uppercase">My Profile</span> and update them there.
                @endif
            </p>
        </div>
        <div class="wt-form-row mb-3">
            @if($isAdminRoute && $mode === 'staff')
            <div>
                <label class="form-label">Ownership Name <span class="text-red-500">*</span></label>
                <select id="managed_user_select" class="admin-select w-full px-3 py-2 rounded-lg border border-stone-200 bg-stone-50 focus:border-[#0284c7] focus:bg-white outline-none transition text-[11px] font-bold" required>
                    <option value="">Search ownership name...</option>
                    @foreach($managedUsers as $user)
                        @php
                            $displayName = strtoupper($user->full_name ?: $user->username ?: '-');
                            $displayDepartment = strtoupper($user->department ?: 'NO DEPARTMENT');
                        @endphp
                        <option value="{{ $user->user_id }}"
                            data-user-id="{{ $user->user_id }}"
                            data-name="{{ $displayName }}"
                            data-department="{{ strtoupper($user->department ?: 'NO DEPARTMENT') }}"
                            data-phone="{{ $user->phone_no ?: '' }}"
                            data-ownership-type="{{ strtoupper($user->last_ownership_type ?? '') }}"
                            data-shared-with="{{ strtoupper($user->last_shared_with ?? '') }}"
                            data-sector="{{ strtoupper($user->last_sector ?? '') }}"
                            data-location="{{ strtoupper($user->last_location ?? '') }}"
                            @selected((string) old('user_id', '') === (string) $user->user_id)>
                            {{ $displayName }} - {{ $displayDepartment }}
                        </option>
                    @endforeach
                    @foreach($extraManagedNames as $extraName)
                        @php
                            $extraMeta = $personMetaByName[$extraName] ?? [];
                            $extraDepartment = strtoupper($extraMeta['department'] ?? 'NO DEPARTMENT');
                            $extraPhone = $extraMeta['phone'] ?? '';
                        @endphp
                        <option value="{{ $extraName }}"
                            data-name="{{ $extraName }}"
                            data-department="{{ $extraDepartment }}"
                            data-phone="{{ $extraPhone }}"
                            @selected(strtoupper((string) old('reporter_name', '')) === $extraName)>
                            {{ $extraName }} - {{ $extraDepartment }}
                        </option>
                    @endforeach
                    @php
                        $selectedReporterName = strtoupper((string) old('reporter_name', $prefillReporterName));
                    @endphp
                    @if($selectedReporterName && !old('user_id'))
                        <option value="{{ $selectedReporterName }}" data-name="{{ $selectedReporterName }}" data-department="{{ $prefillDepartment }}" data-ownership-type="{{ $prefillOwnershipType }}" data-shared-with="{{ $prefillSharedWith }}" data-sector="{{ $prefillSector }}" data-location="{{ $prefillLocation }}" selected>{{ $selectedReporterName }}{{ $prefillDepartment ? ' - ' . $prefillDepartment : '' }}</option>
                    @endif
                </select>
                <p class="ownership-name-help text-[9px] font-bold text-stone-500 dark:text-slate-400">
                    Type a new name if it is not listed.
                </p>
            </div>
            @elseif(!$isAdminRoute)
            <div>
                <label class="form-label">Name <span class="text-red-500">*</span></label>
                <input type="text" value="{{ strtoupper($draftRecord->reporter_name ?? ($currentUser->full_name ?: $currentUser->username)) }}" class="form-control" style="text-transform:uppercase;background:var(--body-bg)" readonly>
            </div>
            <div>
                <label class="form-label">Executive <span class="text-red-500">*</span></label>
                <select name="submit_to_admin_id" class="admin-select w-full px-3 py-2 rounded-lg border border-stone-200 bg-stone-50 focus:border-[#0284c7] focus:bg-white outline-none transition text-[11px] font-bold" required>
                    <option value="" disabled selected>Select executive...</option>
                    @foreach($admins as $admin)
                        <option value="{{ $admin->user_id }}" @selected((string) old('submit_to_admin_id', $draftRecord->submit_to_admin_id ?? '') === (string) $admin->user_id)>{{ strtoupper($admin->full_name ?: $admin->username) }} - {{ strtoupper($admin->department ?: 'ADMIN') }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div>
                <label class="form-label">Phone No <span class="text-red-500">*</span></label>
                <input type="text" name="phone_no" id="phone_no" value="{{ old('phone_no', ($isAdminRoute && $mode === 'staff') ? ($draftRecord->phone_no ?? '') : ($draftRecord->phone_no ?? ($currentUser->phone_no ?? ''))) }}" placeholder="E.G. 012-3456789" class="w-full px-3 py-2 rounded-lg border border-stone-200 bg-stone-50 focus:border-[#0284c7] focus:bg-white outline-none transition text-[11px] font-bold" required>
            </div>
            <input type="hidden" name="designation" id="designation" value="{{ strtoupper(old('designation', $isAdminRoute && $mode === 'staff' ? 'STAFF' : (auth('wt')->user()->position ?: 'EXECUTIVE'))) }}">
            @if($isAdminRoute && $mode === 'staff')
            <div>
                <label class="form-label">Department <span class="text-red-500">*</span></label>
                <select name="department" id="department" class="smart-select w-full" data-placeholder="Type or select department" required>
                    <option value=""></option>
                    @foreach($departmentOptions as $department)
                    <option value="{{ $department }}" @selected(strtoupper((string) old('department', $draftRecord->department_name ?? $prefillDepartment)) === $department)>{{ $department }}</option>
                    @endforeach
                    @if(old('department', $draftRecord->department_name ?? $prefillDepartment) && !in_array(strtoupper((string) old('department', $draftRecord->department_name ?? $prefillDepartment)), $departmentOptions, true))
                    <option value="{{ strtoupper((string) old('department', $draftRecord->department_name ?? $prefillDepartment)) }}" selected>{{ strtoupper((string) old('department', $draftRecord->department_name ?? $prefillDepartment)) }}</option>
                    @endif
                </select>
            </div>
            <div>
                <label class="form-label">Ownership Type <span class="text-red-500">*</span></label>
                <select name="ownership_type" id="ownership_type" class="w-full px-3 py-2 rounded-lg border border-stone-200 bg-stone-50 focus:border-[#0284c7] focus:bg-white outline-none transition text-[11px] font-bold uppercase" required>
                    <option value="">Select ownership type</option>
                    <option value="SHARED" @selected(strtoupper((string) old('ownership_type', $draftRecord->ownership_type ?? $prefillOwnershipType)) === 'SHARED')>Shared</option>
                    <option value="INDIVIDUAL" @selected(strtoupper((string) old('ownership_type', $draftRecord->ownership_type ?? $prefillOwnershipType)) === 'INDIVIDUAL')>Individual</option>
                </select>
            </div>
            <div id="sharedWithWrapper" class="@if(strtoupper((string) old('ownership_type', $draftRecord->ownership_type ?? $prefillOwnershipType)) !== 'SHARED') hidden @endif">
                <label class="form-label">Shared With <span class="text-red-500">*</span></label>
                <input type="text" name="shared_with" id="shared_with" value="{{ strtoupper(old('shared_with', $draftRecord->shared_with ?? $prefillSharedWith)) }}" placeholder="E.G. USER - DEPARTMENT" class="w-full px-3 py-2 rounded-lg border border-stone-200 bg-stone-50 focus:border-[#0284c7] focus:bg-white outline-none transition text-[11px] font-bold uppercase">
            </div>
            <div>
                <label class="form-label">Sector <span class="text-red-500">*</span></label>
                <select name="sector" id="sector" class="smart-select w-full" data-placeholder="Type or select sector" required>
                    <option value=""></option>
                    @foreach($sectorOptions as $sector)
                    <option value="{{ $sector }}" @selected(strtoupper((string) old('sector', $draftRecord->sector ?? $prefillSector)) === $sector)>{{ $sector }}</option>
                    @endforeach
                    @if(old('sector', $draftRecord->sector ?? $prefillSector) && !in_array(strtoupper((string) old('sector', $draftRecord->sector ?? $prefillSector)), $sectorOptions, true))
                    <option value="{{ strtoupper((string) old('sector', $draftRecord->sector ?? $prefillSector)) }}" selected>{{ strtoupper((string) old('sector', $draftRecord->sector ?? $prefillSector)) }}</option>
                    @endif
                </select>
            </div>
            <div>
                <label class="form-label">Bay <span class="text-stone-400">(Optional)</span></label>
                <select name="bay_from" id="bay_from" class="smart-select w-full" data-placeholder="Type number only, e.g. 3">
                    <option value=""></option>
                    @foreach($bayOptions as $bay)
                    <option value="{{ $bay }}" @selected(strtoupper((string) old('bay_from', $draftRecord->bay_from ?? $prefillBayFrom)) === $bay)>{{ $bay }}</option>
                    @endforeach
                    @if(old('bay_from', $draftRecord->bay_from ?? $prefillBayFrom) && !in_array(strtoupper((string) old('bay_from', $draftRecord->bay_from ?? $prefillBayFrom)), $bayOptions, true))
                    <option value="{{ strtoupper((string) old('bay_from', $draftRecord->bay_from ?? $prefillBayFrom)) }}" selected>{{ strtoupper((string) old('bay_from', $draftRecord->bay_from ?? $prefillBayFrom)) }}</option>
                    @endif
                </select>
            </div>
            <div>
                <label class="form-label">Location <span class="text-red-500">*</span></label>
                <select name="location" id="location" class="smart-select w-full" data-placeholder="Type or select location" required>
                    <option value=""></option>
                    @foreach($locationOptions as $location)
                    <option value="{{ $location }}" @selected(strtoupper((string) old('location', $draftRecord->location ?? $prefillLocation)) === $location)>{{ $location }}</option>
                    @endforeach
                    @if(old('location', $draftRecord->location ?? $prefillLocation) && !in_array(strtoupper((string) old('location', $draftRecord->location ?? $prefillLocation)), $locationOptions, true))
                    <option value="{{ strtoupper((string) old('location', $draftRecord->location ?? $prefillLocation)) }}" selected>{{ strtoupper((string) old('location', $draftRecord->location ?? $prefillLocation)) }}</option>
                    @endif
                </select>
            </div>
            @else
            <div>
                <label class="form-label">Department <span class="text-red-500">*</span></label>
                @if($isAdminRoute)
                <input type="text" name="department" id="department" value="{{ strtoupper(old('department', ($isAdminRoute && $mode === 'staff') ? ($draftRecord->department_name ?? '') : ($draftRecord->department_name ?? ($currentUser->department ?: 'GENERAL')))) }}" class="w-full px-3 py-2 rounded-lg border border-stone-200 bg-stone-50 text-[11px] font-bold uppercase" required>
                @else
                <select name="department" class="smart-select w-full" data-placeholder="Type or select department" required>
                    <option value=""></option>
                    @foreach($departmentOptions as $department)
                    <option value="{{ $department }}" @selected(old('department') === $department)>{{ $department }}</option>
                    @endforeach
                    @if(old('department') && !in_array(old('department'), $departmentOptions, true))
                    <option value="{{ old('department') }}" selected>{{ old('department') }}</option>
                    @endif
                </select>
                @endif
            </div>
            <div>
                <label class="form-label">Ownership Type <span class="text-red-500">*</span></label>
                <select name="ownership_type" id="ownership_type" class="w-full px-3 py-2 rounded-lg border border-stone-200 bg-stone-50 focus:border-[#0284c7] focus:bg-white outline-none transition text-[11px] font-bold uppercase" required>
                    <option value="">Select ownership type</option>
                    <option value="SHARED" @selected(strtoupper((string) old('ownership_type', $draftRecord->ownership_type ?? '')) === 'SHARED')>Shared</option>
                    <option value="INDIVIDUAL" @selected(strtoupper((string) old('ownership_type', $draftRecord->ownership_type ?? '')) === 'INDIVIDUAL')>Individual</option>
                </select>
            </div>
            <div id="sharedWithWrapper" class="@if(strtoupper((string) old('ownership_type', $draftRecord->ownership_type ?? '')) !== 'SHARED') hidden @endif">
                <label class="form-label">Shared With <span class="text-red-500">*</span></label>
                <input type="text" name="shared_with" id="shared_with" value="{{ strtoupper(old('shared_with', $draftRecord->shared_with ?? '')) }}" placeholder="E.G. USER - DEPARTMENT" class="w-full px-3 py-2 rounded-lg border border-stone-200 bg-stone-50 focus:border-[#0284c7] focus:bg-white outline-none transition text-[11px] font-bold uppercase">
            </div>
            <div>
                <label class="form-label">Sector <span class="text-red-500">*</span></label>
                <select name="sector" id="sector" class="smart-select w-full" data-placeholder="Type or select sector" required>
                    <option value=""></option>
                    @foreach($sectorOptions as $sector)
                    <option value="{{ $sector }}" @selected(strtoupper((string) old('sector', $draftRecord->sector ?? '')) === $sector)>{{ $sector }}</option>
                    @endforeach
                    @if(old('sector', $draftRecord->sector ?? '') && !in_array(strtoupper((string) old('sector', $draftRecord->sector ?? '')), $sectorOptions, true))
                    <option value="{{ strtoupper((string) old('sector', $draftRecord->sector ?? '')) }}" selected>{{ strtoupper((string) old('sector', $draftRecord->sector ?? '')) }}</option>
                    @endif
                </select>
            </div>
            <div>
                <label class="form-label">Bay <span class="text-stone-400">(Optional)</span></label>
                <select name="bay_from" id="bay_from" class="smart-select w-full" data-placeholder="Type number only, e.g. 3">
                    <option value=""></option>
                    @foreach($bayOptions as $bay)
                    <option value="{{ $bay }}" @selected(strtoupper((string) old('bay_from')) === $bay)>{{ $bay }}</option>
                    @endforeach
                    @if(old('bay_from') && !in_array(strtoupper((string) old('bay_from')), $bayOptions, true))
                    <option value="{{ strtoupper((string) old('bay_from')) }}" selected>{{ strtoupper((string) old('bay_from')) }}</option>
                    @endif
                </select>
            </div>
            <div>
                <label class="form-label">Location <span class="text-red-500">*</span></label>
                <select name="location" id="location" class="smart-select w-full" data-placeholder="Type or select location" required>
                    <option value=""></option>
                    @foreach($locationOptions as $location)
                    <option value="{{ $location }}" @selected(strtoupper((string) old('location', $draftRecord->location ?? '')) === $location)>{{ $location }}</option>
                    @endforeach
                    @if(old('location', $draftRecord->location ?? '') && !in_array(strtoupper((string) old('location', $draftRecord->location ?? '')), $locationOptions, true))
                    <option value="{{ strtoupper((string) old('location', $draftRecord->location ?? '')) }}" selected>{{ strtoupper((string) old('location', $draftRecord->location ?? '')) }}</option>
                    @endif
                </select>
            </div>
            @endif
        </div>

        @if($isAdminRoute && $mode === 'staff')
        <div id="additionalRecipientList" class="mb-6 space-y-4"></div>
        @endif

        <h3 class="text-[10px] font-black text-[#0284c7] uppercase tracking-widest mb-4 border-b border-stone-100 pb-2">
            Pickup & Handover Details <span class="text-red-500">*</span>
        </h3>
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3">
            <p class="text-[10px] font-black uppercase tracking-widest text-emerald-700">ICT Collection Info</p>
            <p class="mt-1 text-[10px] font-bold leading-5 text-emerald-800">
                Please hand over the faulty walkie talkie at ICT Department. After ICT approval or when the unit is ready, pickup can also be done at ICT Department.
            </p>
        </div>
        <div class="wt-form-row mb-3">
            <div>
                <label class="form-label">Who Will Handover To ICT <span class="text-red-500">*</span></label>
                <input type="text" name="handover_person" value="{{ strtoupper(old('handover_person', $draftRecord->handover_person ?? (($isAdminRoute && $mode === 'staff') ? '' : ($currentUser->full_name ?: $currentUser->username)))) }}" placeholder="E.G. AHMAD BIN ALI" class="w-full px-3 py-2 rounded-lg border border-stone-200 bg-stone-50 focus:border-[#0284c7] focus:bg-white outline-none transition text-[11px] font-bold uppercase" required>
                @error('handover_person')
                    <p class="mt-2 text-[10px] font-bold text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="form-label">Handover Date & Time <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-[minmax(0,1fr)_auto] gap-2">
                    <input
                        type="datetime-local"
                        id="handover_at"
                        name="handover_at"
                        value="{{ $draftHandoverAt }}"
                        class="w-full px-3 py-2 rounded-lg border border-stone-200 bg-stone-50 focus:border-[#0284c7] focus:bg-white outline-none transition text-[11px] font-bold"
                        required>
                    <button type="button" id="handoverNowBtn" class="rounded-lg bg-[#0284c7] px-3 py-2 text-[9px] font-black uppercase tracking-widest text-white transition hover:bg-[#734C2F]">Now</button>
                </div>
                @error('handover_at')
                    <p class="mt-2 text-[10px] font-bold text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="form-label">Pickup Contact Name <span class="text-red-500">*</span></label>
                <input type="text" name="pickup_person" value="{{ strtoupper(old('pickup_person', $draftRecord->pickup_person ?? (($isAdminRoute && $mode === 'staff') ? '' : ($currentUser->full_name ?: $currentUser->username)))) }}" placeholder="E.G. AHMAD BIN ALI" class="w-full px-3 py-2 rounded-lg border border-stone-200 bg-stone-50 focus:border-[#0284c7] focus:bg-white outline-none transition text-[11px] font-bold uppercase" required>
                @error('pickup_person')
                    <p class="mt-2 text-[10px] font-bold text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="form-label">Pickup Contact Phone No <span class="text-red-500">*</span></label>
                <input type="text" id="pickup_phone_no" value="{{ old('phone_no', ($isAdminRoute && $mode === 'staff') ? ($draftRecord->phone_no ?? '') : ($draftRecord->phone_no ?? ($currentUser->phone_no ?? ''))) }}" placeholder="E.G. 012-3456789" class="w-full px-3 py-2 rounded-lg border border-stone-200 bg-stone-50 focus:border-[#0284c7] focus:bg-white outline-none transition text-[11px] font-bold" required>
                @error('phone_no')
                    <p class="mt-2 text-[10px] font-bold text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 md:col-span-2">
                <p class="text-[10px] font-black uppercase tracking-widest text-sky-700">Pickup After Approval</p>
                <p class="mt-1 text-[10px] font-bold leading-5 text-sky-800">No pickup date is needed now. ICT will contact the pickup person using the phone number above once the report is approved or ready for collection.</p>
            </div>
        </div>

        <div data-faulty-device-section>
        {{-- Device Details --}}
        <h3 style="font-size:10px;font-weight:900;text-transform:uppercase;letter-spacing:.12em;color:var(--accent);padding-bottom:8px;margin-bottom:16px;border-bottom:1px solid var(--border);padding-left:10px;border-left:3px solid var(--accent)">
            {{ $isAdminRoute ? '2. ' : '' }}Device Details <span class="text-red-500">*</span>
        </h3>
        @if($responsibleWalkies->isNotEmpty() && !($prefillModel || $prefillRadioId || $prefillSerialNumber))
        <div class="damage-muted-panel mb-4 bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3">
            <div class="flex flex-col lg:flex-row lg:items-end gap-3">
                <div class="flex-1">
                    <label class="block text-[10px] font-black text-emerald-700 uppercase tracking-wider mb-1">WT Under My Responsibility</label>
                    <select id="responsible_walkie_select" class="smart-select w-full" data-placeholder="Select assigned walkie talkie">
                        <option value=""></option>
                        @foreach($responsibleWalkies as $responsibleWalkie)
                        @php
                            $walkieAssignment = $responsibleWalkieAssignments->get($responsibleWalkie->walkie_id, []);
                        @endphp
                        <option value="{{ $responsibleWalkie->walkie_id }}"
                            data-model="{{ strtoupper($responsibleWalkie->model ?: '') }}"
                            data-radio-id="{{ strtoupper($responsibleWalkie->radio_id ?: '') }}"
                            data-serial-number="{{ strtoupper($responsibleWalkie->serial_number ?: '') }}"
                            data-owner="{{ strtoupper($walkieAssignment['reporter_name'] ?? ($responsibleWalkie->ownership ?: '')) }}"
                            data-ownership-type="{{ strtoupper($walkieAssignment['ownership_type'] ?? ($responsibleWalkie->ownership_type ?: '')) }}"
                            data-shared-with="{{ strtoupper($walkieAssignment['shared_with'] ?? ($responsibleWalkie->shared_with ?: '')) }}"
                            data-department="{{ strtoupper($walkieAssignment['department'] ?? ($responsibleWalkie->department ?: '')) }}"
                            data-sector="{{ strtoupper($walkieAssignment['sector'] ?? '') }}"
                            data-bay-from="{{ strtoupper($walkieAssignment['bay_from'] ?? '') }}"
                            data-location="{{ strtoupper($walkieAssignment['location'] ?? '') }}">
                            {{ strtoupper($responsibleWalkie->radio_id ?: 'NO RADIO ID') }} - {{ strtoupper($responsibleWalkie->model ?: 'NO MODEL') }} - {{ strtoupper($responsibleWalkie->serial_number ?: 'NO SERIAL') }}
                        </option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-[10px] font-bold text-emerald-700 leading-5">Use this button if the faulty WT is already under your responsibility. The device details below will be filled automatically.</p>
                </div>
                <button type="button" id="useResponsibleWalkie" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-700 text-white text-[10px] font-black uppercase tracking-widest hover:bg-emerald-800 transition">
                    <i class="fa-solid fa-walkie-talkie"></i>
                    Use Selected WT
                </button>
            </div>
        </div>
        @endif
        @if($prefillModel || $prefillRadioId || $prefillSerialNumber)
        <div class="damage-muted-panel mb-4 bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3">
            <p class="text-[10px] font-black uppercase tracking-widest text-emerald-700">Assigned Device Loaded</p>
            <p class="mt-1 text-[10px] font-bold leading-5 text-emerald-800">This walkie talkie was assigned by ICT. The device details below have been filled automatically.</p>
        </div>
        @endif
        <div class="damage-muted-panel mb-4 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
            <div class="flex items-start gap-3">
                <span class="w-8 h-8 rounded-lg bg-white border border-amber-200 flex items-center justify-center text-amber-600 shrink-0">
                    <i class="fa-solid fa-circle-info text-[12px]"></i>
                </span>
                <div>
                    <p class="text-[11px] font-black text-amber-800 uppercase tracking-wider">Device detail or evidence is required</p>
                    <p class="mt-1 text-[10px] font-bold text-amber-700 leading-5">Fill in any device detail you know so the report can be linked automatically. If you only have a photo or video, upload it and ICT will identify the Radio ID or Serial Number manually.</p>
                </div>
            </div>
        </div>
        @if($isAdminRoute && $mode === 'staff')
        <div id="damageDeviceDetailList" class="mb-6 space-y-4"></div>
        @else
            <div class="wt-form-row mb-6">
                <div>
                    <label class="form-label">Model <span class="text-red-500">*</span></label>
                    <input type="text" name="model" id="damage_model" value="{{ old('model', $draftRecord->model ?? '') }}" placeholder="Enter model if known" class="w-full px-3 py-2 rounded-lg border border-stone-200 bg-stone-50 focus:border-[#0284c7] focus:bg-white outline-none transition text-[11px] font-bold uppercase">
                </div>
                <div>
                    <label class="form-label">Radio ID <span class="text-red-500">*</span></label>
                    <input type="text" name="radio_id" id="damage_radio_id" value="{{ old('radio_id', $draftRecord->radio_id ?? '') }}" placeholder="Enter radio ID if known" class="w-full px-3 py-2 rounded-lg border border-stone-200 bg-stone-50 focus:border-[#0284c7] focus:bg-white outline-none transition text-[11px] font-bold uppercase">
                </div>
                <div>
                    <label class="form-label">Serial No <span class="text-red-500">*</span></label>
                    <input type="text" name="serial_number" id="damage_serial_number" value="{{ old('serial_number', $draftRecord->serial_number ?? '') }}" placeholder="Enter serial number if known" class="w-full px-3 py-2 rounded-lg border border-stone-200 bg-stone-50 focus:border-[#0284c7] focus:bg-white outline-none transition text-[11px] font-bold uppercase">
                </div>
            </div>
        @endif
        @error('device_details')
            <p class="-mt-3 mb-5 text-[10px] font-bold text-red-600">{{ $message }}</p>
        @enderror
        </div>

        <div class="damage-muted-panel mb-6 bg-sky-50 border border-dashed border-sky-200 rounded-xl p-4">
            <div class="flex flex-col md:flex-row md:items-start gap-4">
                <div class="flex-1">
                    <label for="device_reference_image" class="flex items-start gap-3 cursor-pointer">
                        <span class="damage-panel w-10 h-10 rounded-lg bg-white border border-sky-200 flex items-center justify-center text-sky-600 shrink-0">
                            <i class="fa-solid fa-image"></i>
                        </span>
                        <span>
                            <span class="block text-[11px] font-black text-sky-800 uppercase tracking-widest">Device label photo <span class="text-sky-500">(Optional)</span></span>
                            <span class="block text-[10px] font-bold text-sky-700 leading-5 mt-1">Upload a clear photo of the walkie sticker or label if you only know some details. If no device detail is entered, ICT will identify the Radio ID or Serial Number manually from the uploaded evidence.</span>
                        </span>
                    </label>
                    <input id="device_reference_image" type="file" name="device_reference_image[]" accept="image/jpeg,image/png,image/webp" multiple class="mt-3 w-full text-[10px] font-bold text-sky-700 file:mr-3 file:rounded-lg file:border-0 file:bg-sky-600 file:px-3 file:py-2 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:text-white">
                    @error('device_reference_image')
                        <p class="mt-2 text-[10px] font-bold text-red-600">{{ $message }}</p>
                    @enderror
                    @error('device_reference_image.*')
                        <p class="mt-2 text-[10px] font-bold text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:w-[300px] rounded-xl border border-sky-200 bg-white/80 px-4 py-3">
                    <p class="text-[10px] font-black text-sky-800 uppercase tracking-widest">ID guide image</p>
                    <p class="mt-2 text-[10px] font-bold text-sky-700 leading-5">Radio ID, Model, and Serial Number are usually shown on the label at the back, inside the walkie talkie.</p>
                    <div class="mt-3 overflow-hidden rounded-xl border border-sky-200 bg-slate-100">
                        <img src="{{ asset('wt-images/wtid.jpg') }}" alt="Example showing Radio ID, Model, and Serial Number on a walkie talkie label" class="w-full h-auto object-cover">
                    </div>
                </div>
            </div>
        </div>

        {{-- Problem Checklist --}}
        <h3 class="text-[10px] font-black text-[#0284c7] uppercase tracking-widest mb-4 border-b border-stone-100 pb-2">Problem Checklist (Select all that apply)</h3>
        <div class="damage-muted-panel mb-6 bg-stone-50 rounded-xl border border-stone-100" style="display:flex;align-items:stretch;gap:20px;padding:16px">
            {{-- Left (~65%): 2-column checkbox grid --}}
            <div style="flex:0 0 65%;min-width:0">
                <div class="problem-checklist-grid grid grid-cols-2">
                    @php
                        $problems = [
                            'Cannot power ON',
                            'Cannot charge',
                            'Speaker issue',
                            'Microphone issue',
                            'Battery faulty / drains too fast',
                            'Weak or unstable signal',
                            'Channel interference / cross line',
                            'Antenna broken / loose',
                            'Button faulty (volume / channel)',
                            'Casing / housing'
                        ];
                    @endphp
                    @foreach($problems as $p)
                    <label class="flex items-center gap-2.5 cursor-pointer group">
                        <input type="checkbox" name="problem_possible[]" value="{{ $p }}" class="w-4 h-4 rounded border-stone-300 text-[#0284c7] focus:ring-[#0284c7]" @checked(in_array($p, $selectedProblems, true))>
                        <span class="damage-check-text font-bold text-stone-700 group-hover:text-[#0284c7] transition">{{ $p }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            {{-- Right (~35%): Other / Additional Details --}}
            <div style="flex:1;min-width:0;display:flex;flex-direction:column;gap:6px;border-left:1px solid var(--border);padding-left:20px">
                <label class="form-label">Other / Additional Details</label>
                <textarea name="other_problem" placeholder="Please specify..." class="w-full rounded-lg border border-stone-200 bg-white focus:border-[#0284c7] outline-none transition font-bold" style="flex:1;min-height:80px;resize:vertical;padding:8px 10px">{{ old('other_problem', $draftOtherProblem ? trim(\Illuminate\Support\Str::after($draftOtherProblem, 'OTHER:')) : '') }}</textarea>
            </div>
        </div>

        <div class="mb-6 flex flex-wrap items-center gap-3">
            <button type="button" id="replacementTrigger" class="replacement-trigger-btn">
                <i class="fa-solid fa-arrows-rotate"></i>
                Request Replacement Accessories (Optional)
            </button>
            <div id="replacementChip" class="replacement-chip {{ $draftReplacementRequested === '1' ? 'is-visible' : '' }}">
                <i class="fa-solid fa-circle-check"></i>
                Replacement requested
            </div>
        </div>

        <div class="damage-muted-panel mb-6 rounded-xl border border-sky-200 bg-sky-50 p-4">
            <div style="display:flex;align-items:stretch;gap:16px;justify-content:flex-start">
                {{-- Col 1: title + description --}}
                <div style="flex:0 0 180px;display:flex;flex-direction:column;justify-content:center">
                    <p class="text-[10px] font-black uppercase tracking-widest text-sky-700">Temporary Spare WT <span class="text-red-500">*</span></p>
                    <p class="mt-1 text-[10px] font-medium leading-relaxed text-sky-700">Do you need a spare walkie talkie while this unit is being checked or repaired?</p>
                    <p class="mt-0.5 text-[10px] font-medium leading-relaxed text-sky-700">ICT will decide based on your request, urgency, and available spare stock.</p>
                </div>
                {{-- Col 2: optional note textarea --}}
                <div style="flex:0 0 240px;display:flex;flex-direction:column">
                    <textarea name="temporary_spare_request_note" rows="4" class="rounded-lg border border-sky-200 bg-white px-3 py-2 text-[10px] font-medium text-sky-900 outline-none focus:border-sky-500" style="flex:1;min-height:90px;resize:vertical;width:100%" placeholder="Optional note, e.g. urgent event / operation reason">{{ $draftTemporarySpareNote }}</textarea>
                </div>
                {{-- Col 3: stacked radio buttons --}}
                <div style="flex:0 0 auto;display:flex;flex-direction:column;gap:8px;justify-content:center">
                    <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-sky-200 bg-white px-3 py-2 text-[10px] font-black uppercase tracking-widest text-sky-800 whitespace-nowrap">
                        <input type="radio" name="need_temporary_spare" value="1" class="h-3 w-3 border-sky-300 text-sky-700 focus:ring-sky-600" required @checked($draftTemporarySpareRequested === '1')>
                        Yes, need spare
                    </label>
                    <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-[10px] font-black uppercase tracking-widest text-slate-700 whitespace-nowrap">
                        <input type="radio" name="need_temporary_spare" value="0" class="h-3 w-3 border-slate-300 text-slate-700 focus:ring-slate-500" required @checked($draftTemporarySpareRequested === '0')>
                        No spare needed
                    </label>
                </div>
            </div>
            @error('need_temporary_spare')
                <p class="mt-2 text-[10px] font-bold text-red-600">{{ $message }}</p>
            @enderror
            @error('temporary_spare_request_note')
                <p class="mt-2 text-[10px] font-bold text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div id="replacementPreviewCard" class="replacement-preview-card {{ $draftReplacementRequested === '1' ? 'is-visible' : '' }}">
            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div class="replacement-preview-summary flex-1">
                    <p class="replacement-preview-label">Replacement Request Form</p>
                    <p class="replacement-preview-value">Saved replacement request details will appear here before you submit the overall faulty report.</p>
                </div>
                <div class="replacement-preview-saved-badge">
                    <i class="fa-solid fa-circle-check"></i>
                    Saved
                </div>
            </div>
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="replacement-preview-grid-card">
                    <p class="replacement-preview-label">Replacement Status</p>
                    <p id="replacementPreviewStatus" class="replacement-preview-value">{{ $draftReplacementRequested === '1' ? 'Replacement requested' : '-' }}</p>
                </div>
                <div class="replacement-preview-grid-card">
                    <p class="replacement-preview-label">Agreement</p>
                    <p id="replacementPreviewAgreement" class="replacement-preview-value">{{ $draftReplacementRequested === '1' ? 'Accepted' : '-' }}</p>
                </div>
                <div class="md:col-span-2 replacement-preview-grid-card is-accent">
                    <p class="replacement-preview-label">Replacement Items Needed</p>
                    <p id="replacementPreviewItems" class="replacement-preview-value">{{ !empty($draftReplacementItems) ? implode(', ', $draftReplacementItems) : 'No item selected.' }}</p>
                </div>
                <div class="md:col-span-2 replacement-preview-grid-card">
                    <p class="replacement-preview-label">Replacement Note</p>
                    <p id="replacementPreviewNote" class="replacement-preview-value">{{ $draftReplacementFreeNote !== '' ? $draftReplacementFreeNote : 'No additional note.' }}</p>
                </div>
            </div>
        </div>

        <h3 class="text-[10px] font-black text-[#0284c7] uppercase tracking-widest mb-4 border-b border-stone-100 pb-2">Evidence Upload <span class="text-slate-400">(Optional)</span></h3>
        <div class="damage-muted-panel mb-6 bg-slate-50 border border-dashed border-slate-200 rounded-xl p-4">
            <label for="damage_evidence" class="flex flex-col md:flex-row md:items-center gap-3 cursor-pointer">
                <span class="damage-panel w-10 h-10 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-500">
                    <i class="fa-solid fa-camera"></i>
                </span>
                <span>
                    <span class="block text-[11px] font-black text-slate-700 uppercase tracking-widest">Upload damage photo or video <span class="text-slate-400">(Optional)</span></span>
                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Optional. JPG, PNG, WEBP, MP4, MOV, or AVI. Maximum 3 files, 20MB each.</span>
                </span>
            </label>
            <input id="damage_evidence" type="file" name="damage_evidence[]" accept="image/jpeg,image/png,image/webp,video/mp4,video/quicktime,video/x-msvideo" multiple class="mt-3 w-full text-[10px] font-bold text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-[#0284c7] file:px-3 file:py-2 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:text-white">
            @error('damage_evidence')
                <p class="mt-2 text-[10px] font-bold text-red-600">{{ $message }}</p>
            @enderror
            @error('damage_evidence.*')
                <p class="mt-2 text-[10px] font-bold text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <span data-faulty-detail-flow-end></span>
        <div class="pt-4 flex flex-col sm:flex-row sm:justify-end gap-3">
            <button type="submit" name="submit_action" value="submit" class="bg-[#0284c7] text-white px-8 py-3 rounded-xl font-black text-[11px] tracking-widest hover:bg-[#734C2F] transition shadow-lg shadow-[#0284c7]/10 flex items-center justify-center gap-3">
                <i class="fas fa-paper-plane"></i> Submit Request
            </button>
        </div>
    </form>
</div>
</div>

@if($submittedRecord)
@php
    $submittedStatus = strtoupper((string) $submittedRecord->status);
    $submittedIsDone = (bool) $submittedRecord->done || $submittedStatus === 'DONE';
    $submittedBadgeClass = $submittedIsDone
        ? 'bg-emerald-100 text-emerald-700 border-emerald-200'
        : 'bg-amber-100 text-amber-700 border-amber-200';
    $submittedBadgeText = $submittedIsDone ? 'Already Fixed / Ready To Collect' : 'Processing';
    $submittedReplacementRequested = str_contains((string) ($submittedRecord->remarks ?? ''), 'REPLACEMENT REQUESTED');
    $submittedTemporarySpareAnswered = ! is_null($submittedRecord->temporary_spare_requested);
@endphp
<div class="px-2 mt-6">
    <div id="submittedRequestSummary" class="damage-card bg-white rounded-2xl shadow-sm border border-emerald-200 overflow-hidden p-5 md:p-6">
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-emerald-600">Submitted Request Details</p>
                <h3 class="mt-2 text-sm font-extrabold text-[#142b47] dark:text-slate-100">Faulty Report #{{ str_pad($submittedRecord->maintenance_id, 4, '0', STR_PAD_LEFT) }}</h3>
                <p class="mt-1 text-[10px] font-bold uppercase tracking-widest text-stone-400">Here is the full information that was just submitted.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex rounded-full border px-3 py-1 text-[9px] font-black uppercase tracking-widest {{ $submittedBadgeClass }}">
                    {{ $submittedBadgeText }}
                </span>
                @if($submittedReplacementRequested)
                <span class="inline-flex rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-[9px] font-black uppercase tracking-widest text-emerald-700">
                    <i class="fa-solid fa-circle-check mr-2"></i>Replacement requested
                </span>
                @endif
                @if($submittedRecord->temporary_spare_requested)
                <span class="inline-flex rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-[9px] font-black uppercase tracking-widest text-sky-700">
                    <i class="fa-solid fa-walkie-talkie mr-2"></i>Temporary WT requested
                </span>
                @endif
                <a href="{{ $faultyStatusRoute }}" class="inline-flex items-center gap-2 rounded-full border border-[#0284c7]/20 bg-[#0284c7] px-3 py-1 text-[9px] font-black uppercase tracking-widest text-white hover:bg-[#734C2F]">
                    <i class="fa-solid fa-list-check"></i> View Faulty Status
                </a>
            </div>
        </div>

        @if(! $submittedTemporarySpareAnswered && ! $submittedRecord->temporary_spare_assigned_at)
        <form method="POST" action="{{ route($routePrefix . '.damages.temporarySpare', ['damage' => $submittedRecord->maintenance_id]) }}" class="mt-5 rounded-2xl border border-sky-200 bg-sky-50 px-4 py-4">
            @csrf
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] font-black uppercase tracking-widest text-sky-700">Temporary Walkie Talkie</p>
                    <p class="mt-2 text-[12px] font-black leading-5 text-sky-950">Perlukan walkie talkie sementara sementara unit asal diuruskan?</p>
                    <p class="mt-1 text-[10px] font-bold leading-5 text-sky-700">ICT akan semak stok spare. Jika diluluskan, unit sementara akan kekal kategori SPARE dan dipulangkan selepas unit asal selesai.</p>
                    <textarea name="temporary_spare_request_note" rows="2" class="mt-3 w-full rounded-lg border border-sky-200 bg-white px-3 py-2 text-[11px] font-bold text-sky-900 outline-none focus:border-sky-500" placeholder="Optional note, e.g. urgent event / operational use"></textarea>
                </div>
                <div class="flex shrink-0 flex-col gap-2 sm:flex-row lg:flex-col">
                    <button type="submit" name="need_temporary_spare" value="1" class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-700 px-4 py-2 text-[10px] font-black uppercase tracking-widest text-white hover:bg-sky-800">
                        <i class="fa-solid fa-circle-check"></i> Yes, Need Temporary WT
                    </button>
                    <button type="submit" name="need_temporary_spare" value="0" class="inline-flex items-center justify-center gap-2 rounded-xl border border-sky-200 bg-white px-4 py-2 text-[10px] font-black uppercase tracking-widest text-sky-700 hover:bg-sky-100">
                        No Temporary WT
                    </button>
                </div>
            </div>
        </form>
        @elseif($submittedTemporarySpareAnswered)
        <div class="mt-5 rounded-2xl border {{ $submittedRecord->temporary_spare_requested ? 'border-sky-200 bg-sky-50' : 'border-stone-200 bg-stone-50' }} px-4 py-3">
            <p class="text-[9px] font-black uppercase tracking-widest {{ $submittedRecord->temporary_spare_requested ? 'text-sky-700' : 'text-stone-500' }}">Temporary Walkie Response</p>
            <p class="mt-1 text-[11px] font-bold {{ $submittedRecord->temporary_spare_requested ? 'text-sky-900' : 'text-stone-600' }}">
                {{ $submittedRecord->temporary_spare_requested ? 'Temporary walkie requested. ICT will review spare availability.' : 'No temporary walkie requested.' }}
                @if($submittedRecord->temporary_spare_request_note)
                    Note: {{ $submittedRecord->temporary_spare_request_note }}
                @endif
            </p>
        </div>
        @endif

        <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4 text-[11px]">
            <div class="rounded-xl border border-stone-200 bg-stone-50 px-4 py-3">
                <p class="text-[9px] font-black uppercase tracking-widest text-stone-500">Reporter</p>
                <p class="mt-2 font-black text-[#142b47]">{{ strtoupper($submittedRecord->reporter_name ?: '-') }}</p>
                <p class="mt-1 font-bold text-stone-500">{{ $submittedRecord->phone_no ?: strtoupper($submittedRecord->reporter_staff_id ?: '-') }}</p>
            </div>
            <div class="rounded-xl border border-stone-200 bg-stone-50 px-4 py-3">
                <p class="text-[9px] font-black uppercase tracking-widest text-stone-500">Department</p>
                <p class="mt-2 font-black text-[#142b47]">{{ strtoupper($submittedRecord->department_name ?: '-') }}</p>
            </div>
            <div class="rounded-xl border border-stone-200 bg-stone-50 px-4 py-3">
                <p class="text-[9px] font-black uppercase tracking-widest text-stone-500">Ownership / Deployment</p>
                <p class="mt-2 font-black text-[#142b47]">Ownership: {{ strtoupper($submittedRecord->ownership_type ?: '-') }}</p>
                <p class="mt-1 font-bold text-stone-500">Shared With: {{ strtoupper($submittedRecord->shared_with ?: '-') }}</p>
                <p class="mt-1 font-bold text-stone-500">Sector: {{ strtoupper($submittedRecord->sector ?: '-') }}</p>
                <p class="mt-1 font-bold text-stone-500">Location: {{ strtoupper($submittedRecord->location ?: '-') }}</p>
            </div>
            <div class="rounded-xl border border-stone-200 bg-stone-50 px-4 py-3">
                <p class="text-[9px] font-black uppercase tracking-widest text-stone-500">Device Details</p>
                <p class="mt-2 font-black text-[#142b47]">Model: {{ strtoupper($submittedRecord->model ?: '-') }}</p>
                <p class="mt-1 font-bold text-stone-500">Radio ID: {{ strtoupper($submittedRecord->radio_id ?: '-') }}</p>
                <p class="mt-1 font-bold text-stone-500">Serial No: {{ strtoupper($submittedRecord->serial_number ?: '-') }}</p>
            </div>
            <div class="rounded-xl border border-stone-200 bg-stone-50 px-4 py-3">
                <p class="text-[9px] font-black uppercase tracking-widest text-stone-500">Submission Info</p>
                <p class="mt-2 font-black text-[#142b47]">Submitted: {{ $submittedRecord->received_date ? \Carbon\Carbon::parse($submittedRecord->received_date)->format('d M Y') : '-' }}</p>
                <p class="mt-1 font-bold text-stone-500">Phone No: {{ $submittedRecord->phone_no ?: '-' }}</p>
                <p class="mt-1 font-bold text-stone-500">Current Status: {{ $submittedRecord->status ?: '-' }}</p>
            </div>
            <div class="md:col-span-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3">
                <p class="text-[9px] font-black uppercase tracking-widest text-emerald-700">Pickup & Handover</p>
                <p class="mt-2 font-black text-[#142b47]">Handover: {{ strtoupper($submittedRecord->handover_person ?: '-') }} | {{ $submittedRecord->handover_at ? \Carbon\Carbon::parse($submittedRecord->handover_at)->format('d M Y, h:i A') : '-' }}</p>
                <p class="mt-1 font-bold text-emerald-800">Pickup contact after ICT approval: {{ strtoupper($submittedRecord->pickup_person ?: '-') }} | {{ $submittedRecord->phone_no ?: '-' }}</p>
                <p class="mt-1 font-bold text-emerald-800">Location: ICT Department Sejurumus</p>
            </div>
            <div class="md:col-span-2 rounded-xl border border-stone-200 bg-stone-50 px-4 py-3">
                <p class="text-[9px] font-black uppercase tracking-widest text-stone-500">Problem Reported</p>
                <p class="mt-2 font-bold text-[#142b47] leading-relaxed">{{ $submittedRecord->problem_possible ?: ($submittedRecord->issue_description ?: '-') }}</p>
            </div>
            <div class="md:col-span-2 rounded-xl border border-stone-200 bg-stone-50 px-4 py-3">
                <p class="text-[9px] font-black uppercase tracking-widest text-stone-500">Remarks / Replacement Details</p>
                <p class="mt-2 font-bold text-[#142b47] leading-relaxed">{{ $submittedRecord->remarks ?: 'No additional remarks.' }}</p>
            </div>
            <div class="md:col-span-2 rounded-xl border border-stone-200 bg-stone-50 px-4 py-3">
                <p class="text-[9px] font-black uppercase tracking-widest text-stone-500">Evidence Uploaded</p>
                <p class="mt-2 font-bold text-[#142b47]">
                    {{ is_array($submittedRecord->evidence_paths) && count($submittedRecord->evidence_paths) ? count($submittedRecord->evidence_paths) . ' file(s) uploaded' : 'No evidence uploaded.' }}
                </p>
            </div>
        </div>
    </div>
</div>
@endif

<div id="replacementModal" class="replacement-modal-backdrop">
    <div class="replacement-modal-card">
        <div class="px-6 py-4 border-b border-stone-100 dark:border-slate-700 shrink-0">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h4 class="text-sm font-black text-[#142b47] dark:text-slate-100">Request Accessories Replacement</h4>
                    <p class="mt-1 text-[11px] font-bold text-stone-500 dark:text-slate-300">Optional. Use this if you want ICT to consider preparing a replacement walkie while checking the faulty unit.</p>
                </div>
                <button type="button" id="replacementModalClose" class="text-stone-400 hover:text-stone-700 dark:text-slate-400 dark:hover:text-slate-100">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
        </div>
        <div class="replacement-modal-body px-6 py-4">
            <div class="replacement-agreement-box mb-4">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" id="replacementAgreementCheckbox" class="mt-0.5 w-4 h-4 rounded border-amber-300 text-[#0284c7] focus:ring-[#0284c7]">
                    <span>
                        <span class="block text-[11px] font-black text-amber-800 dark:text-amber-300 uppercase tracking-wider">Agreement</span>
                        <span class="block mt-1 text-[10px] font-bold text-amber-700 dark:text-amber-200 leading-5">I, <span id="replacementAgreementName">{{ strtoupper(($isAdminRoute && $mode === 'staff') ? (auth('wt')->user()->full_name ?: auth('wt')->user()->username) : old('reporter_name', $draftRecord->reporter_name ?? ($currentUser->full_name ?: $currentUser->username))) }}</span> - <span id="replacementAgreementDepartment">{{ strtoupper(($isAdminRoute && $mode === 'staff') ? (auth('wt')->user()->department ?: 'GENERAL') : old('department', $draftRecord->department_name ?? ($currentUser->department ?: 'GENERAL'))) }}</span>, have read and understood the terms and conditions for the use of the walkie-talkie provided. I agree to comply with those terms and conditions and will be responsible for taking proper care of and using the walkie-talkie appropriately.</span>
                    </span>
                </label>
                <p id="replacementAgreementError" class="replacement-agreement-error">Please tick the agreement before saving a replacement request.</p>
            </div>
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" id="replacementModalCheckbox" class="mt-0.5 w-4 h-4 rounded border-stone-300 text-[#0284c7] focus:ring-[#0284c7]" {{ $draftReplacementRequested === '1' ? 'checked' : '' }}>
                <span>
                    <span class="block text-[11px] font-black text-stone-700 dark:text-slate-100 uppercase tracking-wider">Yes, request accessories replacement </span>
                    <span class="block mt-1 text-[10px] font-bold text-stone-500 dark:text-slate-300">ICT will review availability. Replacement is not guaranteed and depends on stock and approval.</span>
                </span>
            </label>
            <div class="mt-4">
                <label class="block text-[10px] font-black text-stone-500 dark:text-slate-300 uppercase tracking-wider mb-1">Replacement Items Needed</label>
                <p class="mb-2 text-[10px] font-bold text-stone-500 dark:text-slate-400">Click the item you want to request. You can select more than one.</p>
                <div id="replacementItemGrid" class="replacement-item-grid">
                    @foreach($replacementAccessoryOptions as $item)
                    <button
                        type="button"
                        class="replacement-item-btn {{ in_array($item, $draftReplacementItems, true) ? 'is-active' : '' }}"
                        data-value="{{ $item }}">
                        {{ $item }}
                    </button>
                    @endforeach
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-[10px] font-black text-stone-500 dark:text-slate-300 uppercase tracking-wider mb-1">Others</label>
                <textarea id="replacementModalOtherItem" rows="2" placeholder="Please specify other replacement item..." class="w-full px-3 py-2 rounded-lg border border-stone-200 bg-stone-50 focus:border-[#0284c7] focus:bg-white outline-none transition text-[11px] font-bold dark:bg-slate-900 dark:border-slate-700 dark:text-slate-100">{{ $draftReplacementOtherItem ? trim(\Illuminate\Support\Str::after($draftReplacementOtherItem, 'OTHERS:')) : '' }}</textarea>
            </div>
            <div class="mt-4">
                <label class="block text-[10px] font-black text-stone-500 dark:text-slate-300 uppercase tracking-wider mb-1">Replacement Note</label>
                <textarea id="replacementModalNote" rows="3" placeholder="Optional note for ICT, for example urgent work usage or affected operation." class="w-full px-3 py-2 rounded-lg border border-stone-200 bg-stone-50 focus:border-[#0284c7] focus:bg-white outline-none transition text-[11px] font-bold dark:bg-slate-900 dark:border-slate-700 dark:text-slate-100">{{ $draftReplacementFreeNote }}</textarea>
            </div>
        </div>
        <div class="px-6 py-4 bg-stone-50 dark:bg-slate-900/60 border-t border-stone-100 dark:border-slate-700 flex justify-end gap-3 shrink-0">
            <button type="button" id="replacementModalCancel" class="px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest text-stone-500 border border-stone-200 bg-white dark:bg-slate-800 dark:border-slate-700 dark:text-slate-200">Cancel</button>
            <button type="button" id="replacementModalSave" class="px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest text-white bg-[#0284c7] hover:bg-[#734C2F]">Save</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        const faultyOwnershipSection = document.querySelector('[data-faulty-ownership-section]');
        const faultyDeviceSection = document.querySelector('[data-faulty-device-section]');
        const faultyDetailFlowEnd = document.querySelector('[data-faulty-detail-flow-end]');

        if (faultyOwnershipSection && faultyDeviceSection && faultyDetailFlowEnd) {
            const flowNodes = [];
            let currentNode = faultyDeviceSection;

            while (currentNode && currentNode !== faultyDetailFlowEnd) {
                flowNodes.push(currentNode);
                currentNode = currentNode.nextSibling;
            }

            flowNodes.forEach((node) => {
                faultyOwnershipSection.parentNode.insertBefore(node, faultyOwnershipSection);
            });
        }

        const repairForm = document.querySelector('.damage-card form');
        if (repairForm) {
            const sectionHeadings = Array.from(repairForm.querySelectorAll(':scope > h3'));

            if (sectionHeadings.length) {
                const accordion = document.createElement('div');
                accordion.className = 'faulty-accordion';
                repairForm.insertBefore(accordion, sectionHeadings[0]);

                const sections = sectionHeadings.map((heading, index) => {
                    const section = document.createElement('section');
                    section.className = 'faulty-accordion-section';

                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'faulty-accordion-toggle';
                    button.setAttribute('aria-expanded', 'false');

                    const panelId = `faultyAccordionPanel${index + 1}`;
                    const panel = document.createElement('div');
                    panel.className = 'faulty-accordion-panel';
                    panel.id = panelId;
                    panel.hidden = true;
                    button.setAttribute('aria-controls', panelId);

                    const title = document.createElement('span');
                    title.className = 'faulty-accordion-title';
                    title.innerHTML = heading.innerHTML;

                    const icon = document.createElement('i');
                    icon.className = 'fa-solid fa-chevron-down faulty-accordion-icon';

                    button.append(title, icon);
                    section.append(button, panel);
                    accordion.appendChild(section);

                    let node = heading.nextSibling;
                    heading.remove();

                    while (node) {
                        const nextNode = node.nextSibling;
                        if (node.nodeType === Node.ELEMENT_NODE && node.matches('h3')) {
                            break;
                        }
                        if (node.nodeType === Node.ELEMENT_NODE && node.matches('.pt-4')) {
                            break;
                        }
                        panel.appendChild(node);
                        node = nextNode;
                    }

                    return { section, button, panel };
                });

                const openSection = (targetSection) => {
                    sections.forEach(({ button, panel }) => {
                        const isOpen = panel === targetSection.panel;
                        button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                        panel.hidden = !isOpen;
                    });
                };

                const closeAllSections = () => {
                    sections.forEach(({ button, panel }) => {
                        button.setAttribute('aria-expanded', 'false');
                        panel.hidden = true;
                    });
                };

                sections.forEach((section) => {
                    section.button.addEventListener('click', () => {
                        if (section.button.getAttribute('aria-expanded') === 'true') {
                            closeAllSections();
                            return;
                        }

                        openSection(section);
                    });
                });

                repairForm.addEventListener('invalid', (event) => {
                    const panel = event.target.closest('.faulty-accordion-panel');
                    const section = sections.find((item) => item.panel === panel);
                    if (section) {
                        openSection(section);
                    }
                }, true);
            }
        }

        $('.admin-select').not('#managed_user_select').select2({
            tags: true,
            width: '100%',
            placeholder: 'Select option...',
            allowClear: true,
            dropdownCssClass: 'damage-select2-dropdown',
            createTag: function(params) {
                const term = $.trim(params.term);
                if (term === '') return null;
                return {
                    id: term.toUpperCase(),
                    text: term.toUpperCase(),
                    isManualEntry: true,
                    newTag: true
                };
            },
            insertTag: function(data, tag) {
                data.unshift(tag);
            }
        });

        const staffInitials = (name) => {
            const words = String(name || '').trim().split(/\s+/).filter(Boolean);
            if (!words.length) return 'ST';
            return words.slice(0, 2).map((word) => word.charAt(0)).join('').toUpperCase();
        };

        const staffAccountMarkup = (data, isSelection = false) => {
            if (!data.id) {
                return $('<span>').text(data.text || '');
            }

            if (data.newTag) {
                return $('<span>')
                    .addClass('staff-account-new')
                    .append($('<i>').addClass('fa-solid fa-plus'))
                    .append(document.createTextNode(`Add new staff: ${String(data.text || '').toUpperCase()}`));
            }

            const element = data.element;
            const name = element?.dataset?.name || data.text || '-';
            const department = element?.dataset?.department || 'NO DEPARTMENT';
            const phone = element?.dataset?.phone || 'NO PHONE';
            const meta = isSelection ? department : `${department} - ${phone}`;

            const wrapper = $('<span>').addClass(isSelection ? 'staff-account-selection' : 'staff-account-option');
            const textWrapper = $('<span>').addClass('staff-account-text');

            wrapper.append($('<span>').addClass('staff-account-avatar').text(staffInitials(name)));
            textWrapper.append($('<span>').addClass('staff-account-name').text(name));
            textWrapper.append($('<span>').addClass('staff-account-meta').text(meta));
            wrapper.append(textWrapper);

            return wrapper;
        };

        $('#managed_user_select').select2({
            tags: true,
            width: '100%',
            placeholder: 'Search ownership name...',
            allowClear: true,
            dropdownCssClass: 'damage-select2-dropdown staff-account-dropdown',
            templateResult: function(data) {
                return staffAccountMarkup(data, false);
            },
            templateSelection: function(data) {
                return staffAccountMarkup(data, true);
            },
            createTag: function(params) {
                const term = $.trim(params.term);
                if (term === '') return null;
                return {
                    id: term.toUpperCase(),
                    text: term.toUpperCase(),
                    isManualEntry: true,
                    newTag: true
                };
            },
            insertTag: function(data, tag) {
                data.unshift(tag);
            }
        });

        $('.smart-select').select2({
            tags: true,
            width: '100%',
            allowClear: true,
            dropdownCssClass: 'damage-select2-dropdown',
            placeholder: function() {
                return $(this).data('placeholder') || 'Type or select option';
            },
            createTag: function(params) {
                const term = $.trim(params.term);
                if (term === '') return null;
                return { id: term.toUpperCase(), text: term.toUpperCase(), newTag: true };
            },
            insertTag: function(data, tag) {
                data.unshift(tag);
            }
        });

        const managedUserSelect = document.getElementById('managed_user_select');
        const targetUserId = document.getElementById('target_user_id');
        const reporterName = document.getElementById('reporter_name');
        const phoneNo = document.getElementById('phone_no');
        const pickupPhoneNo = document.getElementById('pickup_phone_no');
        const department = document.getElementById('department');
        const sector = document.getElementById('sector');
        const bayFrom = document.getElementById('bay_from');
        const location = document.getElementById('location');
        const ownershipType = document.getElementById('ownership_type');
        const sharedWith = document.getElementById('shared_with');
        const sharedWithWrapper = document.getElementById('sharedWithWrapper');
        const responsibleWalkieSelect = document.getElementById('responsible_walkie_select');
        const useResponsibleWalkie = document.getElementById('useResponsibleWalkie');
        const damageModel = document.getElementById('damage_model');
        const damageRadioId = document.getElementById('damage_radio_id');
        const damageSerialNumber = document.getElementById('damage_serial_number');

        const setFieldValue = (field, value) => {
            if (!field) {
                return;
            }

            const normalizedValue = value ?? '';

            if (window.jQuery && $(field).hasClass('select2-hidden-accessible')) {
                const hasOption = Array.from(field.options).some((option) => option.value === normalizedValue);
                if (normalizedValue !== '' && !hasOption) {
                    const option = new Option(normalizedValue, normalizedValue, true, true);
                    field.add(option);
                }

                $(field).val(normalizedValue).trigger('change');
                return;
            }

            field.value = normalizedValue;
        };

        const syncPickupPhoneToMain = () => {
            if (phoneNo && pickupPhoneNo) {
                phoneNo.value = pickupPhoneNo.value;
            }
        };

        const syncMainPhoneToPickup = () => {
            if (phoneNo && pickupPhoneNo) {
                pickupPhoneNo.value = phoneNo.value;
            }
        };

        phoneNo?.addEventListener('input', syncMainPhoneToPickup);
        pickupPhoneNo?.addEventListener('input', syncPickupPhoneToMain);

        if (responsibleWalkieSelect && useResponsibleWalkie) {
            useResponsibleWalkie.addEventListener('click', function () {
                const option = responsibleWalkieSelect.options[responsibleWalkieSelect.selectedIndex];
                if (!option || !option.value) {
                    return;
                }

                setFieldValue(damageModel, option.dataset.model || '');
                setFieldValue(damageRadioId, option.dataset.radioId || '');
                setFieldValue(damageSerialNumber, option.dataset.serialNumber || '');
                setFieldValue(reporterName, option.dataset.owner || '');
                setFieldValue(ownershipType, option.dataset.ownershipType || '');
                setFieldValue(sharedWith, option.dataset.sharedWith || '');
                setFieldValue(sector, option.dataset.sector || '');
                setFieldValue(bayFrom, option.dataset.bayFrom || '-');
                setFieldValue(location, option.dataset.location || '');

                if (option.dataset.department) {
                    setFieldValue(department, option.dataset.department);
                }

                toggleSharedWith();
            });
        }

        const toggleSharedWith = () => {
            if (!sharedWithWrapper || !sharedWith) {
                return;
            }

            const isShared = (ownershipType?.value || '').toUpperCase() === 'SHARED';
            sharedWithWrapper.classList.toggle('hidden', !isShared);
            sharedWith.required = isShared;

            if (!isShared) {
                sharedWith.value = '';
            }
        };

        if (ownershipType) {
            ownershipType.addEventListener('change', toggleSharedWith);
        }

        if (managedUserSelect) {
            const $managedUserSelect = $(managedUserSelect);

            const syncManagedUser = () => {
                const option = $managedUserSelect.find('option:selected')[0];
                if (!option || !option.value) {
                    if (targetUserId) targetUserId.value = '';
                    if (reporterName) reporterName.value = '';
                    setFieldValue(phoneNo, '');
                    setFieldValue(pickupPhoneNo, '');
                    setFieldValue(department, '');
                    setFieldValue(sector, '');
                    setFieldValue(location, '');
                    setFieldValue(ownershipType, '');
                    setFieldValue(sharedWith, '');
                    toggleSharedWith();
                    return;
                }

                const isExistingUser = Boolean(option.dataset.userId);
                const name = option.dataset.name || option.value || '-';
                const selectedPhoneNo = option.dataset.phone || '';
                const selectedDepartment = option.dataset.department || '';
                const selectedOwnershipType = option.dataset.ownershipType || '';
                const selectedSharedWith = option.dataset.sharedWith || '';
                const selectedSector = option.dataset.sector || '';
                const selectedLocation = option.dataset.location || '';

                if (targetUserId) targetUserId.value = isExistingUser ? option.value : '';
                if (reporterName) reporterName.value = name;
                setFieldValue(phoneNo, selectedPhoneNo);
                setFieldValue(pickupPhoneNo, selectedPhoneNo);
                setFieldValue(department, selectedDepartment);
                setFieldValue(ownershipType, selectedOwnershipType);
                setFieldValue(sharedWith, selectedSharedWith);
                setFieldValue(sector, selectedSector);
                setFieldValue(location, selectedLocation);
                toggleSharedWith();
            };

            $managedUserSelect.on('change select2:select select2:clear', syncManagedUser);
            syncManagedUser();
        }

        toggleSharedWith();

        const replacementTrigger = document.getElementById('replacementTrigger');
        const replacementModal = document.getElementById('replacementModal');
        const replacementModalClose = document.getElementById('replacementModalClose');
        const replacementModalCancel = document.getElementById('replacementModalCancel');
        const replacementModalSave = document.getElementById('replacementModalSave');
        const replacementModalCheckbox = document.getElementById('replacementModalCheckbox');
        const replacementModalNote = document.getElementById('replacementModalNote');
        const replacementAgreementCheckbox = document.getElementById('replacementAgreementCheckbox');
        const replacementAgreementError = document.getElementById('replacementAgreementError');
        const replacementAgreementName = document.getElementById('replacementAgreementName');
        const replacementAgreementDepartment = document.getElementById('replacementAgreementDepartment');
        const replacementInput = document.getElementById('request_replacement');
        const replacementNoteInput = document.getElementById('replacement_note');
        const replacementModalOtherItem = document.getElementById('replacementModalOtherItem');
        const replacementChip = document.getElementById('replacementChip');
        const reporterNameInput = document.getElementById('reporter_name');
        const departmentField = document.querySelector('[name="department"]');
        const managerNameDisplay = document.getElementById('manager_name_display');
        const managerDepartmentDisplay = document.getElementById('manager_department_display');
        const replacementItemButtons = Array.from(document.querySelectorAll('.replacement-item-btn'));
        const replacementPreviewCard = document.getElementById('replacementPreviewCard');
        const replacementPreviewStatus = document.getElementById('replacementPreviewStatus');
        const replacementPreviewAgreement = document.getElementById('replacementPreviewAgreement');
        const replacementPreviewItems = document.getElementById('replacementPreviewItems');
        const replacementPreviewNote = document.getElementById('replacementPreviewNote');
        const damageRequestForm = replacementInput?.closest('form') || document.querySelector('.damage-form-page form');

        const submittedRequestSummary = document.getElementById('submittedRequestSummary');
        if (submittedRequestSummary) {
            setTimeout(() => {
                submittedRequestSummary.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 200);
        }

        const syncReplacementAgreementIdentity = () => {
            const useManagerIdentity = Boolean(managerNameDisplay && managerDepartmentDisplay);
            const agreementName = useManagerIdentity
                ? (managerNameDisplay.value?.trim() || '-')
                : (reporterNameInput?.value?.trim() || '-');
            const agreementDepartment = useManagerIdentity
                ? (managerDepartmentDisplay.value?.trim() || 'GENERAL')
                : (departmentField?.value?.trim() || 'GENERAL');

            if (replacementAgreementName) {
                replacementAgreementName.textContent = agreementName;
            }

            if (replacementAgreementDepartment) {
                replacementAgreementDepartment.textContent = agreementDepartment;
            }
        };

        departmentField?.addEventListener('change', syncReplacementAgreementIdentity);
        syncReplacementAgreementIdentity();

        const getSelectedReplacementItems = () => replacementItemButtons
            .filter((button) => button.classList.contains('is-active'))
            .map((button) => button.dataset.value.trim().toUpperCase());

        const buildReplacementNoteValue = () => {
            const selectedItems = getSelectedReplacementItems();
            const otherItem = replacementModalOtherItem?.value?.trim().toUpperCase() || '';
            const freeNote = replacementModalNote?.value?.trim().toUpperCase() || '';
            const allItems = [...selectedItems];

            if (otherItem !== '') {
                allItems.push(`OTHERS: ${otherItem}`);
            }

            const itemsSection = allItems.length ? `[ITEMS] ${allItems.join(', ')}` : '';

            if (itemsSection && freeNote) {
                return `${itemsSection} || ${freeNote}`;
            }

            return itemsSection || freeNote;
        };

        replacementItemButtons.forEach((button) => {
            button.addEventListener('click', function () {
                button.classList.toggle('is-active');
            });
        });

        const syncReplacementChip = () => {
            if (!replacementChip || !replacementInput) return;
            replacementChip.classList.toggle('is-visible', replacementInput.value === '1');
        };

        const syncReplacementPreview = () => {
            if (!replacementPreviewCard || !replacementInput) return;

            const isRequested = replacementInput.value === '1';
            replacementPreviewCard.classList.toggle('is-visible', isRequested);

            if (!isRequested) {
                return;
            }

            const selectedItems = getSelectedReplacementItems();
            const otherItem = replacementModalOtherItem?.dataset.savedValue?.trim() || '';
            const savedNote = replacementModalNote?.dataset.savedNote?.trim() || '';
            const previewItems = [...selectedItems];

            if (otherItem !== '') {
                previewItems.push(`OTHERS: ${otherItem}`);
            }

            if (replacementPreviewStatus) {
                replacementPreviewStatus.textContent = 'Replacement requested';
            }

            if (replacementPreviewAgreement) {
                replacementPreviewAgreement.textContent = 'Accepted';
            }

            if (replacementPreviewItems) {
                replacementPreviewItems.textContent = previewItems.length ? previewItems.join(', ') : 'No item selected.';
            }

            if (replacementPreviewNote) {
                replacementPreviewNote.textContent = savedNote !== '' ? savedNote : 'No additional note.';
            }
        };

        const openReplacementModal = () => {
            if (!replacementModal) return;
            syncReplacementAgreementIdentity();
            if (replacementModalCheckbox && replacementInput) {
                replacementModalCheckbox.checked = replacementInput.value === '1';
            }
            if (replacementModalNote && replacementNoteInput) {
                replacementModalNote.value = replacementModalNote.dataset.savedNote || replacementModalNote.value;
            }
            if (replacementModalOtherItem) {
                replacementModalOtherItem.value = replacementModalOtherItem.dataset.savedValue || replacementModalOtherItem.value;
            }
            if (replacementAgreementCheckbox) {
                replacementAgreementCheckbox.checked = replacementInput && replacementInput.value === '1';
            }
            replacementAgreementError?.classList.remove('is-visible');
            replacementModal.classList.add('is-open');
            document.body.style.overflow = 'hidden';
        };

        const closeReplacementModal = () => {
            if (!replacementModal) return;
            replacementModal.classList.remove('is-open');
            document.body.style.overflow = '';
        };

        if (replacementTrigger && replacementModal && replacementModalCheckbox && replacementModalNote && replacementInput && replacementNoteInput) {
            replacementTrigger.addEventListener('click', openReplacementModal);
            replacementModalClose?.addEventListener('click', closeReplacementModal);
            replacementModalCancel?.addEventListener('click', closeReplacementModal);

            replacementModalSave?.addEventListener('click', function () {
                if (replacementModalCheckbox.checked && replacementAgreementCheckbox && !replacementAgreementCheckbox.checked) {
                    replacementAgreementError?.classList.add('is-visible');
                    replacementAgreementCheckbox.focus();
                    return;
                }

                replacementInput.value = replacementModalCheckbox.checked ? '1' : '0';
                replacementModalNote.dataset.savedNote = replacementModalNote.value.trim().toUpperCase();
                if (replacementModalOtherItem) {
                    replacementModalOtherItem.dataset.savedValue = replacementModalOtherItem.value.trim().toUpperCase();
                }
                replacementNoteInput.value = buildReplacementNoteValue();
                syncReplacementChip();
                syncReplacementPreview();
                closeReplacementModal();
            });

            replacementAgreementCheckbox?.addEventListener('change', function () {
                if (replacementAgreementCheckbox.checked) {
                    replacementAgreementError?.classList.remove('is-visible');
                }
            });

            replacementModal.addEventListener('click', function (event) {
                if (event.target === replacementModal) {
                    closeReplacementModal();
                }
            });

            syncReplacementChip();
            syncReplacementPreview();
        }

        const quantityInput = document.getElementById('damage_quantity');
        const quantitySummary = document.getElementById('damageQuantitySummary');
        const additionalRecipientList = document.getElementById('additionalRecipientList');
        const deviceDetailList = document.getElementById('damageDeviceDetailList');
        const oldRecipientDetails = @json($oldRecipientDetails);
        const oldDeviceDetails = @json($oldDeviceDetails);
        const departmentOptions = @json($departmentOptions);
        const sectorOptions = @json($sectorOptions);
        const locationOptions = @json($locationOptions);
        const bayOptions = @json($bayOptions);
        @php
            $managedUserOptions = $managedUsers->map(fn ($user) => [
                'user_id' => $user->user_id,
                'name' => strtoupper($user->full_name ?: $user->username ?: ''),
                'department' => strtoupper($user->department ?: ''),
                'phone' => $user->phone_no ?: '',
                'ownership_type' => strtoupper($user->last_ownership_type ?? ''),
                'shared_with' => strtoupper($user->last_shared_with ?? ''),
                'sector' => strtoupper($user->last_sector ?? ''),
                'location' => strtoupper($user->last_location ?? ''),
            ])->values();
        @endphp
        const managedUsers = @json($managedUserOptions);

        const escapeHtml = (value) => String(value || '').replace(/[&<>"']/g, (char) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        })[char]);

        const optionRows = (options, selectedValue, placeholder) => {
            const selected = String(selectedValue || '').toUpperCase();
            const rows = [`<option value="">${escapeHtml(placeholder)}</option>`];
            const seen = new Set();

            options.forEach((option) => {
                const value = String(option || '').toUpperCase();
                if (!value || seen.has(value)) return;
                seen.add(value);
                rows.push(`<option value="${escapeHtml(value)}"${value === selected ? ' selected' : ''}>${escapeHtml(value)}</option>`);
            });

            if (selected && !seen.has(selected)) {
                rows.push(`<option value="${escapeHtml(selected)}" selected>${escapeHtml(selected)}</option>`);
            }

            return rows.join('');
        };

        const clampDamageQuantity = (value) => {
            const parsed = parseInt(value, 10);
            if (Number.isNaN(parsed) || parsed < 1) return 1;
            return Math.min(parsed, 999);
        };

        const syncRecipientSharedWith = (row) => {
            const typeSelect = row.querySelector('[data-recipient-ownership-type]');
            const sharedWrapper = row.querySelector('[data-recipient-shared-wrapper]');
            const sharedInput = row.querySelector('[data-recipient-shared-with]');
            const isShared = String(typeSelect?.value || '').toUpperCase() === 'SHARED';
            sharedWrapper?.classList.toggle('hidden', !isShared);
            if (sharedInput) {
                sharedInput.required = isShared;
                if (!isShared) sharedInput.value = '';
            }
        };

        const fillRecipientFromName = (row) => {
            const nameInput = row.querySelector('[data-recipient-name]');
            const selectedName = String(nameInput?.value || '').trim().toUpperCase();
            const matchedUser = managedUsers.find((user) => user.name === selectedName);
            if (!matchedUser) return;

            row.querySelector('[data-recipient-user-id]').value = matchedUser.user_id || '';
            row.querySelector('[data-recipient-phone]').value = matchedUser.phone || '';
            row.querySelector('[data-recipient-department]').value = matchedUser.department || '';
            row.querySelector('[data-recipient-ownership-type]').value = matchedUser.ownership_type || '';
            row.querySelector('[data-recipient-shared-with]').value = matchedUser.shared_with || '';
            row.querySelector('[data-recipient-sector]').value = matchedUser.sector || '';
            row.querySelector('[data-recipient-location]').value = matchedUser.location || '';
            syncRecipientSharedWith(row);
        };

        const renderAdditionalRecipients = () => {
            if (!quantityInput || !additionalRecipientList) return;

            const quantity = clampDamageQuantity(quantityInput.value);
            quantityInput.value = quantity;
            if (quantitySummary) {
                quantitySummary.textContent = `${quantity} ${quantity === 1 ? 'report' : 'reports'}`;
            }

            const existingRows = Array.from(additionalRecipientList.querySelectorAll('[data-recipient-row]')).map((row) => ({
                user_id: row.querySelector('[data-recipient-user-id]')?.value || '',
                reporter_name: row.querySelector('[data-recipient-name]')?.value || '',
                phone_no: row.querySelector('[data-recipient-phone]')?.value || '',
                department: row.querySelector('[data-recipient-department]')?.value || '',
                ownership_type: row.querySelector('[data-recipient-ownership-type]')?.value || '',
                shared_with: row.querySelector('[data-recipient-shared-with]')?.value || '',
                sector: row.querySelector('[data-recipient-sector]')?.value || '',
                bay_from: row.querySelector('[data-recipient-bay]')?.value || '',
                location: row.querySelector('[data-recipient-location]')?.value || '',
            }));

            additionalRecipientList.innerHTML = '';

            for (let index = 1; index < quantity; index += 1) {
                const saved = existingRows[index - 1] || oldRecipientDetails[index - 1] || {};
                const row = document.createElement('div');
                row.className = 'rounded-2xl border border-[#0284c7]/15 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-900/70';
                row.setAttribute('data-recipient-row', '1');
                row.innerHTML = `
                    <div class="mb-4 border-b border-stone-100 pb-2 text-[10px] font-black uppercase tracking-widest text-slate-800 dark:border-slate-700 dark:text-slate-100">Ownership ${index + 1}</div>
                    <input type="hidden" name="recipient_details[${index}][user_id]" data-recipient-user-id value="${escapeHtml(saved.user_id || '')}">
                    <div class="wt-form-row">
                        <div>
                            <label class="form-label">Ownership Name <span class="text-red-500">*</span></label>
                            <input type="text" name="recipient_details[${index}][reporter_name]" data-recipient-name list="managedRecipientOptions" value="${escapeHtml(saved.reporter_name || '')}" placeholder="Search ownership name..." class="w-full px-3 py-2 rounded-lg border border-stone-200 bg-stone-50 focus:border-[#0284c7] focus:bg-white outline-none transition text-[11px] font-bold uppercase" required>
                        </div>
                        <div>
                            <label class="form-label">Phone No <span class="text-red-500">*</span></label>
                            <input type="text" name="recipient_details[${index}][phone_no]" data-recipient-phone value="${escapeHtml(saved.phone_no || '')}" placeholder="E.G. 012-3456789" class="w-full px-3 py-2 rounded-lg border border-stone-200 bg-stone-50 focus:border-[#0284c7] focus:bg-white outline-none transition text-[11px] font-bold" required>
                        </div>
                        <div>
                            <label class="form-label">Department <span class="text-red-500">*</span></label>
                            <select name="recipient_details[${index}][department]" data-recipient-department class="w-full px-3 py-2 rounded-lg border border-stone-200 bg-stone-50 focus:border-[#0284c7] focus:bg-white outline-none transition text-[11px] font-bold uppercase" required>
                                ${optionRows(departmentOptions, saved.department || '', 'Type or select department')}
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Ownership Type <span class="text-red-500">*</span></label>
                            <select name="recipient_details[${index}][ownership_type]" data-recipient-ownership-type class="w-full px-3 py-2 rounded-lg border border-stone-200 bg-stone-50 focus:border-[#0284c7] focus:bg-white outline-none transition text-[11px] font-bold uppercase" required>
                                ${optionRows(['SHARED', 'INDIVIDUAL'], saved.ownership_type || '', 'Select ownership type')}
                            </select>
                        </div>
                        <div data-recipient-shared-wrapper class="${String(saved.ownership_type || '').toUpperCase() === 'SHARED' ? '' : 'hidden'}">
                            <label class="form-label">Shared With <span class="text-red-500">*</span></label>
                            <input type="text" name="recipient_details[${index}][shared_with]" data-recipient-shared-with value="${escapeHtml(saved.shared_with || '')}" placeholder="E.G. USER - DEPARTMENT" class="w-full px-3 py-2 rounded-lg border border-stone-200 bg-stone-50 focus:border-[#0284c7] focus:bg-white outline-none transition text-[11px] font-bold uppercase">
                        </div>
                        <div>
                            <label class="form-label">Sector <span class="text-red-500">*</span></label>
                            <select name="recipient_details[${index}][sector]" data-recipient-sector class="w-full px-3 py-2 rounded-lg border border-stone-200 bg-stone-50 focus:border-[#0284c7] focus:bg-white outline-none transition text-[11px] font-bold uppercase" required>
                                ${optionRows(sectorOptions, saved.sector || '', 'Type or select sector')}
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Bay <span class="text-stone-400">(Optional)</span></label>
                            <select name="recipient_details[${index}][bay_from]" data-recipient-bay class="w-full px-3 py-2 rounded-lg border border-stone-200 bg-stone-50 focus:border-[#0284c7] focus:bg-white outline-none transition text-[11px] font-bold uppercase">
                                ${optionRows(bayOptions, saved.bay_from || '', 'Type number only, e.g. 3')}
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Location <span class="text-red-500">*</span></label>
                            <select name="recipient_details[${index}][location]" data-recipient-location class="w-full px-3 py-2 rounded-lg border border-stone-200 bg-stone-50 focus:border-[#0284c7] focus:bg-white outline-none transition text-[11px] font-bold uppercase" required>
                                ${optionRows(locationOptions, saved.location || '', 'Type or select location')}
                            </select>
                        </div>
                    </div>
                `;

                row.querySelector('[data-recipient-name]')?.addEventListener('change', () => fillRecipientFromName(row));
                row.querySelector('[data-recipient-ownership-type]')?.addEventListener('change', () => syncRecipientSharedWith(row));
                additionalRecipientList.appendChild(row);
                syncRecipientSharedWith(row);
            }
        };

        const renderDeviceDetails = () => {
            if (!quantityInput || !deviceDetailList) return;

            const quantity = clampDamageQuantity(quantityInput.value);
            const existingRows = Array.from(deviceDetailList.querySelectorAll('[data-device-row]')).map((row) => ({
                model: row.querySelector('[data-device-model]')?.value || '',
                radio_id: row.querySelector('[data-device-radio-id]')?.value || '',
                serial_number: row.querySelector('[data-device-serial-number]')?.value || '',
            }));

            deviceDetailList.innerHTML = '';

            for (let index = 0; index < quantity; index += 1) {
                const saved = existingRows[index] || oldDeviceDetails[index] || {};
                const row = document.createElement('div');
                row.className = 'rounded-xl border border-[#0284c7]/15 bg-white p-3 shadow-sm dark:border-slate-700 dark:bg-slate-900/70';
                row.setAttribute('data-device-row', '1');
                row.innerHTML = `
                    <div class="mb-3 border-b border-stone-100 pb-2 text-[10px] font-black uppercase tracking-widest text-slate-800 dark:border-slate-700 dark:text-slate-100">Walkie Talkie Details ${index + 1}</div>
                    <div class="wt-form-row">
                        <div>
                            <label class="form-label">Model <span class="text-red-500">*</span></label>
                            <input type="text" name="device_details[${index}][model]" data-device-model value="${escapeHtml(saved.model || '')}" placeholder="Enter model if known" class="w-full px-3 py-2 rounded-lg border border-stone-200 bg-stone-50 focus:border-[#0284c7] focus:bg-white outline-none transition text-[11px] font-bold uppercase">
                        </div>
                        <div>
                            <label class="form-label">Radio ID <span class="text-red-500">*</span></label>
                            <input type="text" name="device_details[${index}][radio_id]" data-device-radio-id value="${escapeHtml(saved.radio_id || '')}" placeholder="Enter radio ID if known" class="w-full px-3 py-2 rounded-lg border border-stone-200 bg-stone-50 focus:border-[#0284c7] focus:bg-white outline-none transition text-[11px] font-bold uppercase">
                        </div>
                        <div>
                            <label class="form-label">Serial No <span class="text-red-500">*</span></label>
                            <input type="text" name="device_details[${index}][serial_number]" data-device-serial-number value="${escapeHtml(saved.serial_number || '')}" placeholder="Enter serial number if known" class="w-full px-3 py-2 rounded-lg border border-stone-200 bg-stone-50 focus:border-[#0284c7] focus:bg-white outline-none transition text-[11px] font-bold uppercase">
                        </div>
                    </div>
                `;
                deviceDetailList.appendChild(row);
            }
        };

        if (quantityInput && additionalRecipientList) {
            const dataList = document.createElement('datalist');
            dataList.id = 'managedRecipientOptions';
            dataList.innerHTML = managedUsers.map((user) => `<option value="${escapeHtml(user.name)}">${escapeHtml(user.department || '')}</option>`).join('');
            document.body.appendChild(dataList);
            quantityInput.addEventListener('input', renderAdditionalRecipients);
            quantityInput.addEventListener('change', renderAdditionalRecipients);
            quantityInput.addEventListener('input', renderDeviceDetails);
            quantityInput.addEventListener('change', renderDeviceDetails);
            document.querySelectorAll('.damage-quantity-step').forEach((button) => {
                button.addEventListener('click', () => {
                    const step = parseInt(button.dataset.step || '0', 10);
                    quantityInput.value = clampDamageQuantity(clampDamageQuantity(quantityInput.value) + step);
                    quantityInput.dispatchEvent(new Event('change', { bubbles: true }));
                });
            });
            renderAdditionalRecipients();
            renderDeviceDetails();
        }

        document.getElementById('handoverNowBtn')?.addEventListener('click', () => {
            const now = new Date();
            const handoverAt = document.getElementById('handover_at');
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hour = String(now.getHours()).padStart(2, '0');
            const minute = String(now.getMinutes()).padStart(2, '0');

            if (handoverAt) handoverAt.value = `${year}-${month}-${day}T${hour}:${minute}`;
        });

        document.querySelector('form')?.addEventListener('submit', () => {
            syncPickupPhoneToMain();
        });

        syncMainPhoneToPickup();

    });
</script>
@endpush
