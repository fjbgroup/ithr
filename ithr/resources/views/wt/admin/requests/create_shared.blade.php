@extends('wt.layouts.admin')

@section('title', $isTemporaryRequest ? 'Executive Temporary Request' : 'Executive Long Term Request')

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
    .smart-select + .select2-container {
        width: 100% !important;
    }
    .smart-select + .select2-container .select2-selection--single {
        min-height: 46px;
        border-radius: 14px;
        border: 1px solid rgba(139, 94, 60, 0.3);
        background: rgba(253, 251, 247, 0.5);
        padding: 6px 14px;
        display: flex;
        align-items: center;
    }
    .smart-select + .select2-container .select2-selection__rendered {
        color: #334155 !important;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.4 !important;
        padding-left: 0 !important;
        padding-right: 26px !important;
        text-transform: uppercase;
    }
    .smart-select + .select2-container .select2-selection__placeholder {
        color: #94a3b8 !important;
    }
    .smart-select + .select2-container .select2-selection__arrow {
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
    .dark .smart-select + .select2-container .select2-selection--single {
        background: #0f172a;
        border-color: #334155;
    }
    .dark .smart-select + .select2-container .select2-selection__rendered {
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
    .admin-request-card .smart-select + .select2-container .select2-selection--single {
        min-height: 28px !important;
        height: 28px !important;
        border-radius: 7px !important;
        padding: 2px 8px !important;
    }
    .admin-request-card .smart-select + .select2-container .select2-selection__rendered {
        font-size: 9.5px !important;
        line-height: 1.2 !important;
    }
    .admin-request-card .pic-tag-select + .select2-container {
        width: 100% !important;
    }
    .admin-request-card .pic-tag-select + .select2-container .select2-selection--single {
        min-height: 28px !important;
        height: 28px !important;
        border-radius: 7px !important;
        border: 1px solid rgba(139, 94, 60, 0.3) !important;
        background: rgba(253, 251, 247, 0.5) !important;
        padding: 2px 8px !important;
        display: flex !important;
        align-items: center !important;
    }
    .admin-request-card .pic-tag-select + .select2-container .select2-selection__rendered {
        color: #1e293b !important;
        font-size: 9.5px !important;
        font-weight: 700 !important;
        line-height: 1.2 !important;
        padding-left: 0 !important;
        padding-right: 20px !important;
        text-transform: uppercase;
    }
    .admin-request-card .pic-tag-select + .select2-container .select2-selection__arrow {
        height: 100% !important;
        right: 8px !important;
    }
    .dark .admin-request-card .pic-tag-select + .select2-container .select2-selection--single {
        background: #0f172a !important;
        border-color: #334155 !important;
    }
    .dark .admin-request-card .pic-tag-select + .select2-container .select2-selection__rendered {
        color: #e2e8f0 !important;
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
    .admin-request-card [data-pic-row] {
        padding: 8px !important;
        border-radius: 8px !important;
    }
    .corporate-combobox {
        position: relative;
    }
    .corporate-combobox select {
        display: none !important;
    }
    .corporate-combobox-input {
        width: 100% !important;
        min-height: 28px !important;
        height: 28px !important;
        border-radius: 7px !important;
        border: 1px solid rgba(139, 94, 60, 0.3) !important;
        background: #ffffff !important;
        padding: 5px 24px 5px 8px !important;
        color: #1e293b !important;
        font-size: 9.5px !important;
        font-weight: 800 !important;
        line-height: 1.2 !important;
        text-transform: uppercase;
        outline: none !important;
    }
    .corporate-combobox-input::placeholder {
        color: #94a3b8 !important;
        opacity: 1 !important;
    }
    .corporate-combobox-input:focus {
        border-color: #0284c7 !important;
        box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.2) !important;
    }
    .corporate-combobox-toggle {
        position: absolute;
        right: 9px;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        font-size: 9px;
        pointer-events: none;
    }
    .corporate-combobox-menu {
        position: absolute;
        z-index: 60;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        display: none;
        max-height: 255px;
        overflow-y: auto;
        border: 1px solid #cbd5e1;
        border-radius: 0 0 14px 14px;
        background: #fffaf5;
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.12);
    }
    .corporate-combobox-option {
        display: block;
        width: 100%;
        padding: 10px 16px;
        text-align: left;
        background: transparent;
    }
    .corporate-combobox-option:hover,
    .corporate-combobox-option:focus {
        background: #f1e4d5;
        outline: none;
    }
    .corporate-combobox-name {
        display: block;
        color: #3d2b1f;
        font-size: 11px;
        font-weight: 900;
        line-height: 1.15;
        text-transform: uppercase;
    }
    .corporate-combobox-meta {
        display: block;
        margin-top: 3px;
        color: #8b6f58;
        font-size: 8px;
        font-weight: 900;
        letter-spacing: 0.12em;
        line-height: 1.15;
        text-transform: uppercase;
    }
    .dark .corporate-combobox-input {
        border-color: #334155 !important;
        background: #0f172a !important;
        color: #e2e8f0 !important;
    }
    .dark .corporate-combobox-menu {
        border-color: rgba(148, 163, 184, 0.24);
        background: #152033;
    }
    .dark .corporate-combobox-option:hover,
    .dark .corporate-combobox-option:focus {
        background: #19263b;
    }
    .dark .corporate-combobox-name {
        color: #f8fafc;
    }
    .dark .corporate-combobox-meta {
        color: #94a3b8;
    }
</style>
@endpush

@section('content')
@php
    $requestVariant = $requestVariant ?? 'standard';
    $isTemporaryRequest = $isTemporaryRequest ?? ($requestVariant === 'temporary');
    $formAction = $isTemporaryRequest ? route('wt.admin.requests.store.temporary') : route('wt.admin.requests.store');
    $ownershipNameOptions = collect($formOptionLists['ownership_names'] ?? [])
        ->merge($formOptionLists['names'] ?? [])
        ->unique()
        ->sort()
        ->values();
    $pickupPersonOptions = collect($formOptionLists['pickup_person_names'] ?? [])
        ->merge($formOptionLists['names'] ?? [])
        ->unique()
        ->sort()
        ->values();
    $departmentOptions = $formOptionLists['departments'] ?? [];
    $sectorOptions = $formOptionLists['sectors'] ?? [];
    $locationOptions = $formOptionLists['locations'] ?? [];
    $bayOptions = $formOptionLists['bays'] ?? [];
    $oldPicDetails = collect(old('pic_details', []))->values();
@endphp
<style>
    .match-report-faulty.admin-request-shell { max-width: 1470px !important; margin-left: auto !important; margin-right: auto !important; }
    .match-report-faulty .page-header-block { padding: 0.85rem 1rem !important; margin: 0 0 12px !important; background: linear-gradient(90deg, rgba(139, 94, 60, 0.10), rgba(248, 250, 252, 0.92) 42%, #ffffff) !important; border: 0 !important; border-left: 5px solid #0284c7 !important; border-radius: 10px !important; box-shadow: none !important; }
    .match-report-faulty .page-title-standard { font-size: 16px !important; line-height: 1.15 !important; letter-spacing: -0.01em !important; }
    .match-report-faulty .page-subtitle-standard { margin-top: 7px !important; font-size: 9px !important; line-height: 1.35 !important; letter-spacing: 0.18em !important; }
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

    .longterm-modern.match-report-faulty { width: 100% !important; max-width: 1470px !important; }
    html.dark .admin-request-shell.match-report-faulty,
    html.dark .admin-request-shell.match-report-faulty.longterm-modern {
        width: 100% !important;
        max-width: 1470px !important;
    }
    .longterm-modern .page-header-block {
        margin-bottom: 10px !important;
        border: 0 !important;
        border-left: 5px solid #0284c7 !important;
        border-radius: 10px !important;
        background: linear-gradient(90deg, rgba(139, 94, 60, 0.10), rgba(248, 250, 252, 0.92) 42%, #ffffff) !important;
        padding: 0.85rem 1rem !important;
    }
    .longterm-modern .page-title-standard {
        color: #1f2937 !important;
        font-size: 16px !important;
        font-weight: 900 !important;
    }
    .longterm-modern .page-subtitle-standard {
        color: #64748b !important;
        font-size: 9px !important;
    }
    .longterm-modern .admin-request-card {
        border: 1px solid rgba(148, 163, 184, 0.16) !important;
        border-radius: 14px !important;
        background: #111a2a !important;
        padding: 12px !important;
        color: #e2e8f0 !important;
    }
    .longterm-modern .admin-request-card > .flex:first-child {
        border-bottom: 1px solid rgba(148, 163, 184, 0.14) !important;
        padding-bottom: 8px !important;
    }
    .longterm-modern .admin-request-card h2,
    .longterm-modern .admin-request-card h4 {
        color: #f8fafc !important;
    }
    .longterm-modern .admin-request-card h4 {
        border-left-color: #0284c7 !important;
        margin-top: 10px !important;
        margin-bottom: 8px !important;
        padding-left: 8px !important;
    }
    .longterm-modern .admin-request-card form {
        gap: 10px !important;
    }
    .longterm-modern .admin-request-card form > .grid,
    .longterm-modern .admin-request-card form > .rounded-2xl,
    .longterm-modern .longterm-owner-card {
        border: 1px solid rgba(148, 163, 184, 0.16) !important;
        border-radius: 12px !important;
        background: #152033 !important;
        padding: 10px !important;
    }
    .longterm-modern .admin-request-card form > .mb-5 {
        border-color: rgba(217, 179, 140, 0.22) !important;
        border-radius: 10px !important;
        background: rgba(139, 94, 60, 0.12) !important;
    }
    .longterm-modern .admin-request-card label {
        color: #9fb0c8 !important;
        font-weight: 900 !important;
    }
    .longterm-modern .admin-request-card input:not([type="checkbox"]):not([type="radio"]),
    .longterm-modern .admin-request-card select,
    .longterm-modern .admin-request-card textarea,
    .longterm-modern .admin-request-card .select2-container .select2-selection--single {
        min-height: 30px !important;
        height: 30px !important;
        border-color: rgba(148, 163, 184, 0.28) !important;
        border-radius: 8px !important;
        background: #ffffff !important;
        color: #1e293b !important;
    }
    .dark .longterm-modern .admin-request-card input:not([type="checkbox"]):not([type="radio"]),
    .dark .longterm-modern .admin-request-card select,
    .dark .longterm-modern .admin-request-card textarea,
    .dark .longterm-modern .admin-request-card .select2-container .select2-selection--single {
        border-color: rgba(148, 163, 184, 0.26) !important;
        background: #0e1626 !important;
        color: #f8fafc !important;
    }
    .longterm-modern .admin-request-card textarea {
        min-height: 46px !important;
        height: auto !important;
    }
    .longterm-modern .admin-request-card input::placeholder,
    .longterm-modern .admin-request-card textarea::placeholder {
        color: #8493aa !important;
    }
    .longterm-modern .admin-request-card .temporary-quantity-step {
        min-height: 28px !important;
        width: 34px !important;
        border-color: rgba(139, 94, 60, 0.16) !important;
        color: #0284c7 !important;
        background: #fffaf4 !important;
    }
    .dark .longterm-modern .admin-request-card .temporary-quantity-step {
        border-color: rgba(148, 163, 184, 0.22) !important;
        color: #38bdf8 !important;
        background: #111827 !important;
    }
    .longterm-modern #temporaryPicCount {
        border-color: rgba(217, 179, 140, 0.28) !important;
        background: rgba(139, 94, 60, 0.16) !important;
        color: #38bdf8 !important;
        border-radius: 999px !important;
        padding: 4px 10px !important;
    }
    .longterm-modern .longterm-owner-heading {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        border-bottom: 1px solid rgba(148, 163, 184, 0.14) !important;
        color: #f8fafc !important;
        font-size: 10px !important;
        margin-bottom: 8px !important;
        padding-bottom: 8px !important;
        min-height: 28px !important;
    }
    .longterm-modern .longterm-owner-heading::before {
        content: "";
        width: 6px;
        height: 20px;
        border-radius: 999px;
        background: #0284c7;
        flex: 0 0 6px;
    }
    .longterm-modern .longterm-owner-card {
        padding: 10px !important;
        border-radius: 12px !important;
    }
    .longterm-modern .longterm-note-box {
        margin-bottom: 8px !important;
        min-height: 56px !important;
        padding: 8px 10px !important;
        border-radius: 10px !important;
    }
    .longterm-modern .longterm-note-box p:first-child {
        margin: 0 !important;
        font-size: 9px !important;
        line-height: 1.2 !important;
    }
    .longterm-modern .longterm-note-box p:last-child {
        margin-top: 4px !important;
        font-size: 9px !important;
        line-height: 1.35 !important;
    }
    .dark .longterm-modern .longterm-note-box {
        border-color: rgba(217, 179, 140, 0.2) !important;
        background: rgba(139, 94, 60, 0.12) !important;
    }
    .dark .longterm-modern .longterm-note-box p:first-child {
        color: #38bdf8 !important;
    }
    .dark .longterm-modern .longterm-note-box p:last-child {
        color: #d8e2ef !important;
    }
    .longterm-modern .request-submit-row {
        padding-top: 4px !important;
    }
    .longterm-modern .request-submit-btn {
        min-height: 34px !important;
        border-radius: 10px !important;
        background: #0284c7 !important;
        box-shadow: none !important;
    }
    .dark .longterm-modern .page-header-block,
    .dark .match-report-faulty .page-header-block {
        border-left-color: #38bdf8 !important;
        background: linear-gradient(90deg, rgba(217, 179, 140, 0.14), rgba(30, 41, 59, 0.96) 46%, #1e293b) !important;
    }
    .dark .longterm-modern .page-title-standard,
    .dark .match-report-faulty .page-title-standard {
        color: #f8fafc !important;
    }
    .dark .longterm-modern .page-subtitle-standard,
    .dark .match-report-faulty .page-subtitle-standard {
        color: #94a3b8 !important;
    }

    html:not(.dark) .longterm-modern .page-header-block,
    html:not(.dark) .longterm-modern .admin-request-card,
    html:not(.dark) .longterm-modern .admin-request-card form > .grid,
    html:not(.dark) .longterm-modern .admin-request-card form > .rounded-2xl,
    html:not(.dark) .longterm-modern .longterm-owner-card {
        border-color: #e7e5e4 !important;
        background: #ffffff !important;
    }
    html:not(.dark) .longterm-modern .page-title-standard,
    html:not(.dark) .longterm-modern .admin-request-card h2,
    html:not(.dark) .longterm-modern .admin-request-card h4,
    html:not(.dark) .longterm-modern .longterm-owner-heading {
        color: #1f2937 !important;
    }
    html:not(.dark) .longterm-modern .page-subtitle-standard {
        color: #64748b !important;
    }
    html:not(.dark) .longterm-modern .admin-request-card input:not([type="checkbox"]):not([type="radio"]),
    html:not(.dark) .longterm-modern .admin-request-card select,
    html:not(.dark) .longterm-modern .admin-request-card textarea,
    html:not(.dark) .longterm-modern .admin-request-card .select2-container .select2-selection--single {
        border-color: #d6d3d1 !important;
        background: #f8fafc !important;
        color: #1f2937 !important;
    }

    html .admin-request-shell.match-report-faulty,
    html.dark .admin-request-shell.match-report-faulty {
        width: 100% !important;
        max-width: 1470px !important;
        margin-left: auto !important;
        margin-right: auto !important;
    }
    html.dark .content-surface:has(.admin-request-shell.match-report-faulty) {
        align-items: stretch !important;
    }
    html .match-report-faulty .admin-request-card,
    html .longterm-modern.match-report-faulty .admin-request-card,
    html.dark .match-report-faulty .admin-request-card,
    html.dark .longterm-modern.match-report-faulty .admin-request-card {
        width: 100% !important;
        padding: 10px 12px !important;
        border-radius: 10px !important;
        box-shadow: none !important;
    }
    html .match-report-faulty .admin-request-card h4,
    html .longterm-modern.match-report-faulty .admin-request-card h4,
    html.dark .match-report-faulty .admin-request-card h4,
    html.dark .longterm-modern.match-report-faulty .admin-request-card h4 {
        margin: 8px 0 6px !important;
        padding: 0 0 5px 8px !important;
        font-size: 9.5px !important;
        line-height: 1.2 !important;
        border-left-width: 3px !important;
    }
    html .match-report-faulty .admin-request-card form > .grid,
    html .match-report-faulty .admin-request-card form > .rounded-2xl,
    html .longterm-modern.match-report-faulty .longterm-owner-card,
    html.dark .match-report-faulty .admin-request-card form > .grid,
    html.dark .match-report-faulty .admin-request-card form > .rounded-2xl,
    html.dark .longterm-modern.match-report-faulty .longterm-owner-card {
        padding: 10px !important;
        border-radius: 10px !important;
    }
    html .match-report-faulty .admin-request-card input:not([type="checkbox"]):not([type="radio"]),
    html .match-report-faulty .admin-request-card select,
    html .match-report-faulty .admin-request-card textarea,
    html .match-report-faulty .admin-request-card .select2-container .select2-selection--single,
    html.dark .match-report-faulty .admin-request-card input:not([type="checkbox"]):not([type="radio"]),
    html.dark .match-report-faulty .admin-request-card select,
    html.dark .match-report-faulty .admin-request-card textarea,
    html.dark .match-report-faulty .admin-request-card .select2-container .select2-selection--single {
        min-height: 28px !important;
        height: 28px !important;
        border-radius: 7px !important;
        padding: 5px 8px !important;
        font-size: 9.5px !important;
        line-height: 1.2 !important;
    }
    html .match-report-faulty .admin-request-card textarea,
    html.dark .match-report-faulty .admin-request-card textarea {
        min-height: 44px !important;
        height: auto !important;
    }
    html .match-report-faulty .admin-request-card button,
    html .match-report-faulty .admin-request-card .request-submit-btn,
    html.dark .match-report-faulty .admin-request-card button,
    html.dark .match-report-faulty .admin-request-card .request-submit-btn {
        min-height: 28px !important;
        padding: 7px 14px !important;
        border-radius: 8px !important;
        font-size: 9px !important;
    }
    html .longterm-modern.match-report-faulty .longterm-note-box,
    html.dark .longterm-modern.match-report-faulty .longterm-note-box {
        min-height: 56px !important;
        padding: 8px 10px !important;
        border-radius: 10px !important;
        margin-bottom: 8px !important;
    }
</style>
<div class="px-1 sm:px-2 admin-request-shell match-report-faulty {{ $isTemporaryRequest ? '' : 'longterm-modern' }}">
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
            <h3 class="page-title-standard dark:text-[#f3f4f6]">{{ $isTemporaryRequest ? 'Executive Temporary Request' : 'Executive Long Term Request' }}</h3>
            <p class="page-subtitle-standard dark:text-slate-500">
                {{ $isTemporaryRequest
                    ? 'Create a temporary request with ownership details and send it straight to ICT for approval and follow-up.'
                    : 'Create a long term request with ownership details and send it straight to ICT for approval and follow-up.' }}
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
            <i class="fas fa-people-arrows text-sm"></i>
        </div>
        <h2 class="text-xs font-black text-[#142b47] dark:text-slate-100 uppercase tracking-widest">{{ $isTemporaryRequest ? 'Temporary Request Form' : 'Long Term Request Form' }}</h2>
    </div>

    <div class="mb-5 rounded-2xl border border-[#0284c7]/15 bg-[#FDFBF7] px-4 py-3 dark:bg-slate-900/70 dark:border-slate-700">
        <p class="text-[9px] font-black uppercase tracking-[0.18em] text-[#0284c7]">Request Review</p>
        <p class="mt-1 text-[11px] font-semibold text-stone-600 dark:text-slate-300">
            {{ $isTemporaryRequest
                ? 'Fill in the temporary request details. ICT will check the request and update the status once it is processed.'
                : 'Fill in the walkie talkie request details. ICT will check the request and update the status once it is processed.' }}
        </p>
    </div>

    <form action="{{ $formAction }}" method="POST" class="space-y-6">
        @csrf
        <input type="hidden" name="request_scope" value="on_behalf">
        <input type="hidden" name="full_name" value="{{ old('full_name', auth('wt')->user()->full_name ?: auth('wt')->user()->username) }}">
        <input type="hidden" name="department" value="{{ old('department', auth('wt')->user()->department) }}">
        <input type="hidden" id="request_ownership_type" name="ownership_type" value="{{ old('ownership_type', 'Individual') }}">
        <input type="hidden" id="request_shared_with" name="shared_with" value="{{ strtoupper(old('shared_with', '')) }}">
        @unless($isTemporaryRequest)
        <input type="hidden" name="event_name" value="{{ old('event_name', 'General Request') }}">
        @endunless

        <h4 class="text-[10px] font-black text-[#0284c7] border-l-4 border-[#0284c7] pl-3 uppercase tracking-widest mb-4">1. Executive Details</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-[10px] font-bold text-stone-600 dark:text-slate-400 mb-2 uppercase tracking-widest">Executive Name</label>
                <input type="text" value="{{ strtoupper(auth('wt')->user()->full_name ?: auth('wt')->user()->username) }}" class="w-full px-4 py-2.5 rounded-xl border border-[#0284c7]/30 bg-stone-100/80 dark:bg-slate-900 dark:border-slate-700 text-[11px] font-bold outline-none transition dark:text-slate-200" readonly>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-stone-600 dark:text-slate-400 mb-2 uppercase tracking-widest">Executive ID</label>
                <input type="text" value="{{ strtoupper(auth('wt')->user()->staff_id ?: '-') }}" class="w-full px-4 py-2.5 rounded-xl border border-[#0284c7]/30 bg-stone-100/80 dark:bg-slate-900 dark:border-slate-700 text-[11px] font-bold outline-none transition dark:text-slate-200" readonly>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-stone-600 dark:text-slate-400 mb-2 uppercase tracking-widest">Executive Department</label>
                <input type="text" value="{{ strtoupper(auth('wt')->user()->department ?: '-') }}" class="w-full px-4 py-2.5 rounded-xl border border-[#0284c7]/30 bg-stone-100/80 dark:bg-slate-900 dark:border-slate-700 text-[11px] font-bold outline-none transition dark:text-slate-200" readonly>
            </div>
        </div>

        <h4 class="text-[10px] font-black text-[#0284c7] border-l-4 border-[#0284c7] pl-3 uppercase tracking-widest mb-4">{{ $isTemporaryRequest ? '2. Temporary Request Details' : '2. Long Term Request Details' }}</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @if($isTemporaryRequest)
            <div>
                <label class="block text-[10px] font-bold text-stone-600 dark:text-slate-400 mb-2 uppercase tracking-widest">Quantity</label>
                <div class="flex max-w-[220px] overflow-hidden rounded-lg border border-[#0284c7]/20 bg-white focus-within:ring-2 focus-within:ring-[#0284c7]/15 dark:border-slate-700 dark:bg-slate-900">
                    <button type="button" class="temporary-quantity-step flex items-center justify-center border-r border-[#0284c7]/15 text-xs font-black hover:bg-[#0284c7]/10 dark:border-slate-700" data-step="-1" aria-label="Decrease quantity">-</button>
                    <input type="number" name="quantity" min="1" max="999" inputmode="numeric" value="{{ old('quantity', 1) }}" class="w-full border-0 bg-transparent px-3 py-1.5 text-center text-[11px] font-black text-slate-800 outline-none dark:text-slate-200" required>
                    <button type="button" class="temporary-quantity-step flex items-center justify-center border-l border-[#0284c7]/15 text-xs font-black hover:bg-[#0284c7]/10 dark:border-slate-700" data-step="1" aria-label="Increase quantity">+</button>
                </div>
            </div>
            <div class="md:col-span-2">
                <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <label class="block text-[10px] font-bold text-stone-600 dark:text-slate-400 uppercase tracking-widest">Owner Per Unit</label>
                        <p class="mt-1 text-[9px] font-bold text-stone-500 dark:text-slate-400">1 unit = 1 owner. Add owner and pickup contact for each unit.</p>
                    </div>
                    <span id="temporaryPicCount" class="rounded-lg border border-[#0284c7]/20 bg-[#FDFBF7] px-3 py-1 text-[9px] font-black uppercase tracking-widest text-[#0284c7] dark:border-slate-700 dark:bg-slate-900">1 unit</span>
                </div>
                <div id="temporaryPicList" class="space-y-3"></div>
                <datalist id="picOwnershipNameOptions">
                    @foreach($ownershipNameOptions as $ownershipName)
                    <option value="{{ $ownershipName }}"></option>
                    @endforeach
                </datalist>
                <datalist id="picPickupPersonOptions">
                    @foreach($pickupPersonOptions as $pickupPerson)
                    <option value="{{ $pickupPerson }}"></option>
                    @endforeach
                </datalist>
                <datalist id="picDepartmentOptions">
                    @foreach($departmentOptions as $department)
                    <option value="{{ $department }}"></option>
                    @endforeach
                </datalist>
                <datalist id="picSectorOptions">
                    @foreach($sectorOptions as $sector)
                    <option value="{{ $sector }}"></option>
                    @endforeach
                </datalist>
                <datalist id="picLocationOptions">
                    @foreach($locationOptions as $location)
                    <option value="{{ $location }}"></option>
                    @endforeach
                </datalist>
                @error('pic_details')
                    <div class="mt-2 text-xs font-bold text-red-600">{{ $message }}</div>
                @enderror
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
                <input type="text" id="temporary_purpose_usage" name="event_name" value="{{ old('event_name') }}" placeholder="Example: Temporary use for event standby, operations support, or short-term team coordination" class="w-full px-4 py-2.5 rounded-xl border border-[#0284c7]/30 bg-[#FDFBF7]/50 dark:bg-slate-900 dark:border-slate-700 text-[11px] font-bold focus:ring-2 focus:ring-[#0284c7]/20 outline-none transition dark:text-slate-200" required>
                <input type="hidden" id="request_sector_fallback" name="sector" value="{{ old('sector') }}">
                <input type="hidden" id="request_location_fallback" name="location" value="{{ old('location') }}">
                <input type="hidden" id="request_justification_fallback" name="justifications" value="{{ old('justifications', old('event_name')) }}">
                <input type="hidden" id="request_bay_fallback" name="bay_from" value="{{ old('bay_from') }}">
            </div>
            @endif
            @unless($isTemporaryRequest)
            <div>
                <label class="block text-[10px] font-bold text-stone-600 dark:text-slate-400 mb-2 uppercase tracking-widest">Quantity</label>
                <div class="flex max-w-[220px] overflow-hidden rounded-lg border border-[#0284c7]/20 bg-white focus-within:ring-2 focus-within:ring-[#0284c7]/15 dark:border-slate-700 dark:bg-slate-900">
                    <button type="button" class="temporary-quantity-step flex items-center justify-center border-r border-[#0284c7]/15 text-xs font-black hover:bg-[#0284c7]/10 dark:border-slate-700" data-step="-1" aria-label="Decrease quantity">-</button>
                    <input type="number" name="quantity" min="1" max="999" inputmode="numeric" value="{{ old('quantity', 1) }}" class="w-full border-0 bg-transparent px-3 py-1.5 text-center text-[11px] font-black text-slate-800 outline-none dark:text-slate-200" required>
                    <button type="button" class="temporary-quantity-step flex items-center justify-center border-l border-[#0284c7]/15 text-xs font-black hover:bg-[#0284c7]/10 dark:border-slate-700" data-step="1" aria-label="Increase quantity">+</button>
                </div>
            </div>
            <div class="md:col-span-2">
                <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <label class="block text-[10px] font-bold text-stone-600 dark:text-slate-400 uppercase tracking-widest">Owner Per Unit</label>
                        <p class="mt-1 text-[9px] font-bold text-stone-500 dark:text-slate-400">1 unit = 1 owner. Add owner and pickup contact for each unit.</p>
                    </div>
                    <span id="temporaryPicCount" class="rounded-lg border border-[#0284c7]/20 bg-[#FDFBF7] px-3 py-1 text-[9px] font-black uppercase tracking-widest text-[#0284c7] dark:border-slate-700 dark:bg-slate-900">1 unit</span>
                </div>
                <div id="temporaryPicList" class="space-y-3"></div>
                <datalist id="picOwnershipNameOptions">
                    @foreach($ownershipNameOptions as $ownershipName)
                    <option value="{{ $ownershipName }}"></option>
                    @endforeach
                </datalist>
                <datalist id="picPickupPersonOptions">
                    @foreach($pickupPersonOptions as $pickupPerson)
                    <option value="{{ $pickupPerson }}"></option>
                    @endforeach
                </datalist>
                <datalist id="picDepartmentOptions">
                    @foreach($departmentOptions as $department)
                    <option value="{{ $department }}"></option>
                    @endforeach
                </datalist>
                <datalist id="picSectorOptions">
                    @foreach($sectorOptions as $sector)
                    <option value="{{ $sector }}"></option>
                    @endforeach
                </datalist>
                <datalist id="picLocationOptions">
                    @foreach($locationOptions as $location)
                    <option value="{{ $location }}"></option>
                    @endforeach
                </datalist>
                @error('pic_details')
                    <div class="mt-2 text-xs font-bold text-red-600">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label class="block text-[10px] font-bold text-stone-600 dark:text-slate-400 mb-2 uppercase tracking-widest">Start Date</label>
                <input type="date" name="request_date" value="{{ old('request_date', date('Y-m-d')) }}" class="w-full px-4 py-2.5 rounded-xl border border-[#0284c7]/30 bg-[#FDFBF7]/50 dark:bg-slate-900 dark:border-slate-700 text-[11px] font-bold focus:ring-2 focus:ring-[#0284c7]/20 outline-none transition dark:text-slate-200" required>
            </div>
            <div class="md:col-span-2">
                <label class="block text-[10px] font-bold text-stone-600 dark:text-slate-400 mb-2 uppercase tracking-widest">Remark / Purpose</label>
                <textarea name="justifications" rows="1" placeholder="Example: Long-term usage, department coordination, or shared daily usage" class="w-full px-4 py-2.5 rounded-xl border border-[#0284c7]/30 bg-[#FDFBF7]/50 dark:bg-slate-900 dark:border-slate-700 text-[11px] font-bold focus:ring-2 focus:ring-[#0284c7]/20 outline-none transition dark:text-slate-200" required>{{ old('justifications') }}</textarea>
                <input type="hidden" id="request_sector_fallback" name="sector" value="{{ old('sector') }}">
                <input type="hidden" id="request_location_fallback" name="location" value="{{ old('location') }}">
                <input type="hidden" id="request_bay_fallback" name="bay_from" value="{{ old('bay_from') }}">
            </div>
            @endunless
        </div>

        <input type="hidden" name="pickup_method" value="self">

        <div class="request-submit-row pt-8 flex justify-end">
            <button type="submit" name="submit_action" value="submit" class="request-submit-btn bg-[#0284c7] text-white px-10 py-3.5 rounded-2xl font-black text-[11px] tracking-widest uppercase hover:bg-[#724D31] transition shadow-xl shadow-[#0284c7]/20 flex items-center gap-3 border border-[#A67B5B]">
                {{ $isTemporaryRequest ? 'Submit Temporary Request To ICT' : 'Submit Long Term Request To ICT' }} <i class="fas fa-paper-plane"></i>
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
        const oldPicDetails = @json($oldPicDetails);
        const oldRequestSharedWith = @json(strtoupper(old('shared_with', '')));
        const isTemporaryRequest = @json($isTemporaryRequest);
        const ownershipTypeOptions = ['Individual', 'Shared'];
        const ownershipNameOptions = @json($ownershipNameOptions);
        const pickupPersonOptions = @json($pickupPersonOptions);
        const departmentOptions = @json($departmentOptions);
        const sectorOptions = @json($sectorOptions);
        const locationOptions = @json($locationOptions);
        const bayOptions = @json($bayOptions);
        const phoneByName = @json($formOptionLists['phone_by_name'] ?? []);
        const personMetaByName = @json($formOptionLists['person_meta_by_name'] ?? []);

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

        function escapeHtml(value) {
            return String(value || '').replace(/[&<>"']/g, function(char) {
                return {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                }[char];
            });
        }

        function escapeAttribute(value) {
            return escapeHtml(value).replace(/`/g, '&#096;');
        }

        function normalizeLookupName(value) {
            return String(value || '')
                .replace(/\s*\+\s*\d+\s*OWNER$/i, '')
                .replace(/\s+/g, ' ')
                .trim()
                .toUpperCase();
        }

        function contactMetaForName(value) {
            const key = normalizeLookupName(value);
            return personMetaByName[key] || {
                department: '',
                phone: phoneByName[key] || ''
            };
        }

        function applyPhoneValue(field, phone) {
            if (!field || !phone) return;

            field.value = phone;
            field.dispatchEvent(new Event('input', { bubbles: true }));
            field.dispatchEvent(new Event('change', { bubbles: true }));
        }

        function fillPhoneFromName(nameField, phoneSelector) {
            const row = nameField?.closest('[data-pic-row]');
            const phoneField = row?.querySelector(phoneSelector);
            const phone = phoneByName[normalizeLookupName(nameField?.value)];

            applyPhoneValue(phoneField, phone);
        }

        function setSelectOrFieldValue(field, value) {
            if (!field || !value) return;

            const normalizedValue = String(value || '').toUpperCase();

            if (field.tagName === 'SELECT') {
                let option = Array.from(field.options).find((item) => item.value.toUpperCase() === normalizedValue);

                if (!option) {
                    option = new Option(normalizedValue, normalizedValue, true, true);
                    field.add(option);
                }

                field.value = option.value;
                if (window.jQuery && $(field).hasClass('select2-hidden-accessible')) {
                    $(field).trigger('change');
                } else {
                    field.dispatchEvent(new Event('change', { bubbles: true }));
                }
                return;
            }

            field.value = normalizedValue;
            field.dispatchEvent(new Event('input', { bubbles: true }));
            field.dispatchEvent(new Event('change', { bubbles: true }));
        }

        function fillDepartmentFromName(nameField) {
            const row = nameField?.closest('[data-pic-row]');
            const departmentField = row?.querySelector('[data-pic-department]');
            const meta = contactMetaForName(nameField?.value);

            if (departmentField && meta.department && !departmentField.value) {
                setSelectOrFieldValue(departmentField, meta.department);
            }
        }

        function hydratePicPhonesFromNames() {
            document.querySelectorAll('#temporaryPicList [data-pic-name]').forEach((field) => {
                const row = field.closest('[data-pic-row]');
                const phoneField = row?.querySelector('[data-pic-phone]');

                if (!phoneField?.value) {
                    fillPhoneFromName(field, '[data-pic-phone]');
                }
            });

            document.querySelectorAll('#temporaryPicList [data-pic-pickup-person]').forEach((field) => {
                const row = field.closest('[data-pic-row]');
                const phoneField = row?.querySelector('[data-pic-pickup-phone]');

                if (!phoneField?.value) {
                    fillPhoneFromName(field, '[data-pic-pickup-phone]');
                }
            });
        }

        function renderOptions(options, selectedValue, placeholder) {
            const selected = String(selectedValue || '').toUpperCase();
            const rows = [`<option value="">${escapeHtml(placeholder)}</option>`];
            const seen = new Set();

            options.forEach((option) => {
                const value = String(option || '');
                const key = value.trim().toUpperCase();
                if (!key || seen.has(key)) return;
                seen.add(key);
                rows.push(`<option value="${escapeAttribute(value)}" ${value.toUpperCase() === selected ? 'selected' : ''}>${escapeHtml(value)}</option>`);
            });

            if (selectedValue && !seen.has(selected)) {
                rows.push(`<option value="${escapeAttribute(selectedValue)}" selected>${escapeHtml(selectedValue)}</option>`);
            }

            return rows.join('');
        }

        function ensureSelectValue(select, value) {
            const normalized = String(value || '').toUpperCase();

            if (normalized === '') {
                select.value = '';
                select.dispatchEvent(new Event('change', { bubbles: true }));
                return;
            }

            let option = Array.from(select.options).find((item) => item.value.toUpperCase() === normalized);

            if (!option) {
                option = new Option(normalized, normalized, true, true);
                option.dataset.manual = '1';
                select.add(option);
            }

            select.value = option.value;
            select.dispatchEvent(new Event('change', { bubbles: true }));
        }

        function enhancePersonSelect(select) {
            if (!select || select.dataset.corporateComboboxReady === '1') return;

            select.dataset.corporateComboboxReady = '1';
            const inputRequired = select.required;
            select.required = false;
            select.classList.add('hidden');
            select.style.display = 'none';

            const wrapper = document.createElement('div');
            wrapper.className = 'corporate-combobox';

            const input = document.createElement('input');
            input.type = 'text';
            input.className = 'corporate-combobox-input';
            input.placeholder = select.dataset.placeholder || 'Search or type name';
            input.autocomplete = 'off';
            input.value = select.value || '';
            input.required = inputRequired;

            const toggle = document.createElement('span');
            toggle.className = 'corporate-combobox-toggle';
            toggle.innerHTML = '<i class="fa-solid fa-caret-down"></i>';

            const menu = document.createElement('div');
            menu.className = 'corporate-combobox-menu';

            select.parentNode.insertBefore(wrapper, select);
            wrapper.append(input, toggle, menu, select);

            const optionRows = () => Array.from(select.options)
                .filter((option) => option.value)
                .map((option) => {
                    const name = option.value.toUpperCase();
                    const meta = contactMetaForName(name);
                    return {
                        name,
                        department: meta.department || '',
                        phone: meta.phone || phoneByName[normalizeLookupName(name)] || ''
                    };
                });

            const renderMenu = () => {
                const query = input.value.trim().toUpperCase();
                const matches = optionRows()
                    .filter((person) => `${person.name} ${person.department} ${person.phone}`.includes(query))
                    .slice(0, 12);

                menu.innerHTML = matches.length ? matches.map((person, index) => {
                    const meta = [person.department, person.phone].filter(Boolean).join(' / ');
                    return `
                        <button type="button" class="corporate-combobox-option" data-combobox-index="${index}">
                            <span class="corporate-combobox-name">${escapeHtml(person.name)}</span>
                            ${meta ? `<span class="corporate-combobox-meta">${escapeHtml(meta)}</span>` : ''}
                        </button>
                    `;
                }).join('') : '<div class="corporate-combobox-name px-4 py-3 text-slate-400">No match found</div>';

                Array.from(menu.querySelectorAll('[data-combobox-index]')).forEach((button) => {
                    button.addEventListener('click', () => {
                        const person = matches[Number(button.dataset.comboboxIndex)];
                        input.value = person.name;
                        ensureSelectValue(select, person.name);
                        menu.style.display = 'none';
                    });
                });

                menu.style.display = 'block';
            };

            input.addEventListener('input', () => {
                ensureSelectValue(select, input.value);
                renderMenu();
            });
            input.addEventListener('focus', renderMenu);
            input.addEventListener('click', renderMenu);
            input.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    menu.style.display = 'none';
                }
            });

            document.addEventListener('click', (event) => {
                if (!wrapper.contains(event.target)) {
                    menu.style.display = 'none';
                }
            });
        }

        function renderTemporaryPicRows() {
            const quantityField = document.querySelector('input[name="quantity"]');
            const list = document.getElementById('temporaryPicList');
            const countBadge = document.getElementById('temporaryPicCount');
            if (!quantityField || !list) return;

            const quantity = clampQuantity(quantityField.value);
            quantityField.value = quantity;

            const existingRows = Array.from(list.querySelectorAll('[data-pic-row]')).map((row) => ({
                name: row.querySelector('[data-pic-name]')?.value || '',
                phone_no: row.querySelector('[data-pic-phone]')?.value || '',
                department: row.querySelector('[data-pic-department]')?.value || '',
                ownership_type: row.querySelector('[data-pic-ownership-type]')?.value || '',
                sector: row.querySelector('[data-pic-sector]')?.value || '',
                bay_from: row.querySelector('[data-pic-bay]')?.value || '',
                location: row.querySelector('[data-pic-location]')?.value || '',
                shared_with: row.querySelector('[data-pic-shared-with]')?.value || '',
                pickup_person: row.querySelector('[data-pic-pickup-person]')?.value || '',
                pickup_phone_no: row.querySelector('[data-pic-pickup-phone]')?.value || '',
            }));

            list.innerHTML = '';

            for (let index = 0; index < quantity; index += 1) {
                const saved = existingRows[index] || oldPicDetails[index] || {};
                const row = document.createElement('div');
                row.className = isTemporaryRequest
                    ? 'rounded-2xl border border-[#0284c7]/15 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-900/70'
                    : 'longterm-owner-card';
                row.setAttribute('data-pic-row', '1');
                row.innerHTML = `
                    <div class="longterm-owner-heading mb-4 border-b border-stone-100 pb-2 text-[10px] font-black uppercase tracking-widest text-slate-800 dark:border-slate-700 dark:text-slate-100">${index + 1}. Ownership Information</div>
                    <div class="longterm-note-box mb-4 rounded-xl border border-stone-200 bg-white px-4 py-3 shadow-sm dark:border-slate-700 dark:bg-slate-900/80">
                        <p class="text-[10px] font-black uppercase tracking-widest text-[#0284c7] dark:text-[#38bdf8]">Profile Note</p>
                        <p class="mt-1 text-[10px] font-bold leading-5 text-slate-600 dark:text-slate-300">Search an existing ownership name or type a new one. Each walkie talkie unit must have one ownership profile.</p>
                    </div>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-stone-500 dark:text-slate-400">Ownership Name <span class="text-red-500">*</span></label>
                            <select name="pic_details[${index}][name]" data-pic-name data-placeholder="Search ownership name..." class="pic-tag-select w-full" required>
                                ${renderOptions(ownershipNameOptions, saved.name || '', 'Search ownership name...')}
                            </select>
                            <p class="mt-2 text-[10px] text-stone-500 dark:text-slate-400">If the name is not listed yet, type it. The ownership profile will be saved with this request.</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-stone-500 dark:text-slate-400">Ownership Phone No</label>
                            <input type="text" name="pic_details[${index}][phone_no]" data-pic-phone value="${escapeAttribute(saved.phone_no || '')}" placeholder="Auto-filled if available" class="w-full rounded-xl border border-[#0284c7]/30 bg-[#FDFBF7]/50 px-4 py-2.5 text-[11px] font-bold outline-none transition focus:ring-2 focus:ring-[#0284c7]/20 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200">
                            <p class="mt-2 text-[10px] text-stone-500 dark:text-slate-400">Use (-) if not applicable.</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-stone-500 dark:text-slate-400">Department <span class="text-red-500">*</span></label>
                            <select name="pic_details[${index}][department]" data-pic-department data-placeholder="Type or select department" class="pic-tag-select w-full" required>
                                ${renderOptions(departmentOptions, saved.department || '', 'Type or select department')}
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-stone-500 dark:text-slate-400">Ownership Type <span class="text-red-500">*</span></label>
                            <select name="pic_details[${index}][ownership_type]" data-pic-ownership-type class="w-full rounded-xl border border-[#0284c7]/30 bg-[#FDFBF7]/50 px-4 py-2.5 text-[11px] font-bold uppercase outline-none transition focus:ring-2 focus:ring-[#0284c7]/20 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200" required>
                                ${renderOptions(ownershipTypeOptions, saved.ownership_type || '', 'Select ownership type')}
                            </select>
                        </div>
                        <div data-pic-shared-with-group class="${String(saved.ownership_type || '').toUpperCase() === 'SHARED' ? '' : 'hidden'}">
                            <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-stone-500 dark:text-slate-400">Shared With <span class="text-red-500">*</span></label>
                            <input type="text" data-pic-shared-with value="${escapeAttribute(saved.shared_with || (index === 0 ? oldRequestSharedWith : ''))}" placeholder="E.G. NAME / TEAM / DEPARTMENT" class="w-full rounded-xl border border-[#0284c7]/30 bg-[#FDFBF7]/50 px-4 py-2.5 text-[11px] font-bold uppercase outline-none transition focus:ring-2 focus:ring-[#0284c7]/20 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200">
                        </div>
                        <div>
                            <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-stone-500 dark:text-slate-400">Sector <span class="text-red-500">*</span></label>
                            <select name="pic_details[${index}][sector]" data-pic-sector data-placeholder="Type or select sector" class="pic-tag-select w-full" required>
                                ${renderOptions(sectorOptions, saved.sector || '', 'Type or select sector')}
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-stone-500 dark:text-slate-400">Bay</label>
                            <select name="pic_details[${index}][bay_from]" data-pic-bay data-placeholder="Type number only, e.g. 3" class="pic-tag-select w-full">
                                ${renderOptions(['-', ...bayOptions], saved.bay_from || '-', 'Type number only, e.g. 3')}
                            </select>
                            <p class="mt-2 text-[10px] text-stone-500 dark:text-slate-400">Use (-) if not applicable.</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-stone-500 dark:text-slate-400">Location <span class="text-red-500">*</span></label>
                            <select name="pic_details[${index}][location]" data-pic-location data-placeholder="Type or select location" class="pic-tag-select w-full" required>
                                ${renderOptions(locationOptions, saved.location || '', 'Type or select location')}
                            </select>
                        </div>
                        <div class="longterm-note-box rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 shadow-sm dark:border-slate-700 dark:bg-slate-950/70 md:col-span-2">
                            <p class="text-[10px] font-black uppercase tracking-widest text-[#0284c7] dark:text-[#38bdf8]">Pickup Info</p>
                            <p class="mt-1 text-[10px] font-bold leading-5 text-slate-600 dark:text-slate-300">This unit is for the ownership name entered above. Pick up the approved walkie talkie at ICT Department after ICT approves this request.</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-stone-500 dark:text-slate-400">Who Will Pick Up This Walkie Talkie? <span class="text-red-500">*</span></label>
                            <select name="pic_details[${index}][pickup_person]" data-pic-pickup-person data-placeholder="Search pickup person..." class="pic-tag-select w-full" required>
                                ${renderOptions(pickupPersonOptions, saved.pickup_person || '', 'Search pickup person...')}
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-[10px] font-black uppercase tracking-wider text-stone-500 dark:text-slate-400">Pickup Phone No <span class="text-red-500">*</span></label>
                            <input type="text" name="pic_details[${index}][pickup_phone_no]" data-pic-pickup-phone value="${escapeAttribute(saved.pickup_phone_no || '')}" placeholder="E.G. 012-3456789" class="w-full rounded-xl border border-[#0284c7]/30 bg-[#FDFBF7]/50 px-4 py-2.5 text-[11px] font-bold outline-none transition focus:ring-2 focus:ring-[#0284c7]/20 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200" required>
                        </div>
                        <div class="md:col-span-2">
                            <p class="mt-2 text-[10px] text-stone-500 dark:text-slate-400">This name will be shown to ICT for pickup at ICT Department after approval.</p>
                        </div>
                    </div>
                `;
                list.appendChild(row);
            }

            syncPicSharedWithRows();
            initTagSelect('#temporaryPicList .pic-tag-select');
            hydratePicPhonesFromNames();

            if (countBadge) {
                countBadge.textContent = `${quantity} ${quantity === 1 ? 'unit' : 'units'}`;
            }

            syncRequestFallbackFields();
        }

        function syncRequestFallbackFields() {
            const ownershipFallback = document.getElementById('request_ownership_type');
            const sharedWithFallback = document.getElementById('request_shared_with');
            const sectorFallback = document.getElementById('request_sector_fallback');
            const bayFallback = document.getElementById('request_bay_fallback');
            const locationFallback = document.getElementById('request_location_fallback');

            if (!ownershipFallback && !sharedWithFallback && !sectorFallback && !bayFallback && !locationFallback) {
                return;
            }

            const firstRow = document.querySelector('#temporaryPicList [data-pic-row]');
            const firstOwnership = firstRow?.querySelector('[data-pic-ownership-type]')?.value || 'Individual';
            const firstSharedWith = firstRow?.querySelector('[data-pic-shared-with]')?.value || '';
            const firstName = firstRow?.querySelector('[data-pic-name]')?.value || '';
            const firstSector = firstRow?.querySelector('[data-pic-sector]')?.value || '';
            const firstBay = firstRow?.querySelector('[data-pic-bay]')?.value || '';
            const firstLocation = firstRow?.querySelector('[data-pic-location]')?.value || '';

            if (ownershipFallback) {
                ownershipFallback.value = firstOwnership || 'Individual';
            }

            if (sharedWithFallback) {
                sharedWithFallback.value = String(firstOwnership || '').toUpperCase() === 'SHARED'
                    ? (firstSharedWith || firstName)
                    : '';
            }

            if (sectorFallback) {
                sectorFallback.value = firstSector;
            }

            if (bayFallback) {
                bayFallback.value = firstBay;
            }

            if (locationFallback) {
                locationFallback.value = firstLocation;
            }
        }

        function syncPicSharedWithRows() {
            document.querySelectorAll('#temporaryPicList [data-pic-row]').forEach((row) => {
                const typeField = row.querySelector('[data-pic-ownership-type]');
                const sharedGroup = row.querySelector('[data-pic-shared-with-group]');
                const sharedField = row.querySelector('[data-pic-shared-with]');
                const isShared = String(typeField?.value || '').toUpperCase() === 'SHARED';

                if (sharedGroup) {
                    sharedGroup.classList.toggle('hidden', !isShared);
                }

                if (sharedField) {
                    sharedField.required = isShared;
                }
            });
        }

        function syncJustificationFallback() {
            const purposeField = document.getElementById('temporary_purpose_usage');
            const justificationFallback = document.getElementById('request_justification_fallback');

            if (purposeField && justificationFallback) {
                justificationFallback.value = purposeField.value;
            }
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
            const personSelects = [];
            const tagSelects = [];

            $(selector).each(function () {
                if (this.matches('[data-pic-name], [data-pic-pickup-person]')) {
                    personSelects.push(this);
                } else {
                    tagSelects.push(this);
                }
            });

            personSelects.forEach(enhancePersonSelect);

            if (!tagSelects.length) {
                return;
            }

            $(tagSelects).select2({
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

        $('input[name="quantity"]').on('input change', renderTemporaryPicRows);
        $('#temporaryPicList').on('input change', '[data-pic-name], [data-pic-sector], [data-pic-bay], [data-pic-location], [data-pic-shared-with]', syncRequestFallbackFields);
        $('#temporaryPicList').on('change select2:select', '[data-pic-name]', function () {
            fillPhoneFromName(this, '[data-pic-phone]');
            fillDepartmentFromName(this);
        });
        $('#temporaryPicList').on('change select2:select', '[data-pic-pickup-person]', function () {
            fillPhoneFromName(this, '[data-pic-pickup-phone]');
        });
        $('#temporaryPicList').on('change', '[data-pic-ownership-type]', function () {
            syncPicSharedWithRows();
            syncRequestFallbackFields();
        });
        $('#temporary_purpose_usage').on('input change', syncJustificationFallback);
        renderTemporaryPicRows();
        syncJustificationFallback();
        updateEndDateFromDuration();
        $('#temporary_duration_days').on('input change', updateEndDateFromDuration);
        $('#temporary_start_date').on('change', updateEndDateFromDuration);
        $('#temporary_end_date').on('change', syncTemporaryDuration);
    });
</script>
@endpush

