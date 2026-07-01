@extends('wt.layouts.admin')

@section('title', $pageTitle ?? 'Add Walkie Talkie')
@section('page_title', $pageTitle ?? 'Add Walkie Talkie')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endpush

@section('content')
@php
    $inventoryOnly = (bool) ($inventoryOnly ?? false);
    $hasTemporaryRadio = old('has_temporary_radio', filled(old('temporary_radio_id', $defaults['temporary_radio_id'] ?? null)) ? '1' : '0');
@endphp
<div class="page-header-block flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
    <div>
        <h1 class="page-title-standard text-slate-100">{{ $pageTitle ?? 'Add Walkie Talkie' }}</h1>
        <p class="page-subtitle-standard text-slate-400">{{ $pageSubtitle ?? 'Fill in all required fields to register a new unit.' }}</p>
    </div>
    <a href="{{ $backRoute ?? route('wt.admin.walkies.index') }}" class="wt-btn wt-btn-soft">
        <i class="fas fa-arrow-left text-[13px]"></i>
        Back
    </a>
</div>

@if($errors instanceof \Illuminate\Support\ViewErrorBag && $errors->any())
<div class="alert-error mb-6">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right:8px;flex-shrink:0;">
        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
    </svg>
    <ul class="list-disc pl-5 mt-1">
        @foreach($errors->all() as $e)
        <li>{{ $e }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="walkie-create-shell">
    <form action="{{ $formAction ?? route('wt.admin.walkies.store') }}" method="POST" id="createWalkieForm" class="walkie-create-form">
        @csrf
        @if(($formMethod ?? 'POST') !== 'POST')
        @method($formMethod)
        @endif
        <input type="hidden" name="return_route" value="{{ $returnRouteName ?? 'wt.admin.walkies.index' }}">
        @foreach(($hiddenFields ?? []) as $hiddenName => $hiddenValue)
        <input type="hidden" name="{{ $hiddenName }}" value="{{ $hiddenValue }}">
        @endforeach
        <div class="walkie-form-header">
            <div>
                <h2 class="walkie-form-title">{{ $formTitle ?? 'New Unit Registration' }}</h2>
                <p class="walkie-form-subtitle">{{ $formSubtitle ?? 'Complete the form below and save when ready.' }}</p>
            </div>
            @if(($showModeActions ?? false) && ($formMethod ?? 'POST') === 'POST')
            <div class="walkie-form-mode-actions">
                @if($inventoryOnly)
                    <a href="{{ route('wt.admin.walkies.create') }}" class="walkie-mode-link">
                        <i class="fas fa-user-tag"></i>
                        Add With User Details
                    </a>
                @else
                    <a href="{{ route('wt.admin.walkies.create.unassigned') }}" class="walkie-mode-link">
                        <i class="fas fa-box-open"></i>
                        Add Without User
                    </a>
                @endif
            </div>
            @endif
        </div>

        <div class="walkie-form-body">
            <div class="form-grid">
                <div class="form-section-title form-group-full">Unit Identity</div>

                <div class="form-group">
                    <label class="form-label">Radio ID <span class="required">*</span></label>
                    <select name="radio_id" class="form-input page-tag-select" data-placeholder="Type or select radio id" required>
                        <option value=""></option>
                        @foreach($walkieRadioIds as $radioId)
                        <option value="{{ $radioId }}" @selected(old('radio_id', $defaults['radio_id'] ?? null) === $radioId)>{{ $radioId }}</option>
                        @endforeach
                        @if(old('radio_id') && !$walkieRadioIds->contains(old('radio_id')))
                        <option value="{{ old('radio_id') }}" selected>{{ old('radio_id') }}</option>
                        @endif
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Serial Number <span class="required">*</span></label>
                    <select name="serial_number" class="form-input page-tag-select" data-placeholder="Type or select serial number" required>
                        <option value=""></option>
                        @foreach($walkieSerials as $serial)
                        <option value="{{ $serial }}" @selected(old('serial_number', $defaults['serial_number'] ?? null) === $serial)>{{ $serial }}</option>
                        @endforeach
                        @if(old('serial_number') && !$walkieSerials->contains(old('serial_number')))
                        <option value="{{ old('serial_number') }}" selected>{{ old('serial_number') }}</option>
                        @endif
                    </select>
                </div>

                @unless($inventoryOnly)
                <div class="form-group">
                    <label class="form-label">Status <span class="required">*</span></label>
                    <select name="status" class="form-input page-smart-select" data-placeholder="Search status" required>
                        <option value="" disabled selected>Select status...</option>
                        @foreach($statusOptions as $status)
                        <option value="{{ $status }}" {{ old('status', $defaults['status'] ?? null) == $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Ownership Type <span class="required">*</span></label>
                    <select name="ownership_type" id="ownership_type" class="form-input page-tag-select ownership-type-control" data-placeholder="Type or search ownership type" required>
                        <option value="" disabled selected>Select type...</option>
                        @foreach($ownershipTypeOptions as $ownershipType)
                        <option value="{{ $ownershipType }}" {{ old('ownership_type', $defaults['ownership_type'] ?? null) == $ownershipType ? 'selected' : '' }}>{{ $ownershipType }}</option>
                        @endforeach
                        @if(old('ownership_type') && !$ownershipTypeOptions->contains(strtoupper(trim(old('ownership_type')))))
                        <option value="{{ strtoupper(trim(old('ownership_type'))) }}" selected>{{ strtoupper(trim(old('ownership_type'))) }}</option>
                        @endif
                    </select>
                </div>

                <div class="form-group shared-with-group hidden">
                    <label class="form-label">Shared With <span class="required">*</span></label>
                    <input type="text" name="shared_with" value="{{ strtoupper(old('shared_with', $defaults['shared_with'] ?? '')) }}" class="form-input shared-with-input" placeholder="E.G. USER / TEAM / DEPARTMENT">
                </div>
                @endunless

                <div class="form-group">
                    <label class="form-label">Model <span class="required">*</span></label>
                    <select name="model" class="form-input page-tag-select" data-placeholder="Type or select model" required>
                        <option value="" disabled selected>Select model...</option>
                        @foreach($walkieModels as $model)
                        <option value="{{ $model }}" {{ old('model', $defaults['model'] ?? null) == $model ? 'selected' : '' }}>{{ $model }}</option>
                        @endforeach
                        @if(old('model') && !$walkieModels->contains(old('model')))
                        <option value="{{ old('model') }}" selected>{{ old('model') }}</option>
                        @endif
                    </select>
                </div>

                @unless($inventoryOnly)
                <div class="form-section-title form-group-full">Assignment Details</div>

                <div class="form-group">
                    <label class="form-label">Ownership Name</label>
                    @php($currentOwnership = old('ownership', $defaults['ownership'] ?? null))
                    <select name="ownership" class="form-input page-tag-select" data-placeholder="Type or select ownership name">
                        <option value=""></option>
                        @foreach($staffOwnerships as $ownership)
                        <option value="{{ $ownership }}" @selected($currentOwnership === $ownership)>{{ $ownership }}</option>
                        @endforeach
                        @if($currentOwnership && !$staffOwnerships->contains($currentOwnership))
                        <option value="{{ $currentOwnership }}" selected>{{ $currentOwnership }}</option>
                        @endif
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Department</label>
                    <select name="department" class="form-input page-tag-select" data-placeholder="Type or select department">
                        <option value=""></option>
                        @foreach($walkieDepartments as $department)
                        <option value="{{ $department }}" @selected(old('department', $defaults['department'] ?? null) === $department)>{{ $department }}</option>
                        @endforeach
                        @if(old('department') && !$walkieDepartments->contains(old('department')))
                        <option value="{{ old('department') }}" selected>{{ old('department') }}</option>
                        @endif
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Location</label>
                    @php($currentLocation = strtoupper((string) old('location', $defaults['location'] ?? '')))
                    <select name="location" class="form-input page-combo-select" data-placeholder="Type or search location">
                        <option value=""></option>
                        @foreach($walkieLocations as $location)
                        <option value="{{ $location }}" @selected($currentLocation === $location)>{{ $location }}</option>
                        @endforeach
                        @if($currentLocation !== '' && !$walkieLocations->contains($currentLocation))
                        <option value="{{ $currentLocation }}" selected>{{ $currentLocation }}</option>
                        @endif
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Executive</label>
                    @php($currentExecutive = strtoupper((string) old('executive', $defaults['executive'] ?? '')))
                    <select name="executive" class="form-input page-combo-select" data-placeholder="Type or search executive">
                        <option value=""></option>
                        @foreach($executiveOptions as $executiveName)
                        <option value="{{ $executiveName }}" @selected($currentExecutive === $executiveName)>{{ $executiveName }}</option>
                        @endforeach
                        @if($currentExecutive !== '' && !$executiveOptions->contains($currentExecutive))
                        <option value="{{ $currentExecutive }}" selected>{{ $currentExecutive }}</option>
                        @endif
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Position</label>
                    @php($currentPosition = strtoupper((string) old('position', $defaults['position'] ?? '')))
                    <select name="position" class="form-input page-combo-select" data-placeholder="Type or search position">
                        <option value=""></option>
                        @foreach($walkiePositions as $position)
                        <option value="{{ $position }}" @selected($currentPosition === $position)>{{ $position }}</option>
                        @endforeach
                        @if($currentPosition !== '' && !$walkiePositions->contains($currentPosition))
                        <option value="{{ $currentPosition }}" selected>{{ $currentPosition }}</option>
                        @endif
                    </select>
                </div>

                @endunless

                <div class="form-section-title form-group-full">Tracking & Status</div>

                @unless($inventoryOnly)
                    <div class="form-group">
                        <label class="form-label">Temporary / Swapped WT?</label>
                        <select name="has_temporary_radio" id="has_temporary_radio" class="form-input page-smart-select" data-placeholder="Search option">
                            @foreach($yesNoOptions as $option)
                            <option value="{{ $option['value'] }}" {{ (string) $hasTemporaryRadio === (string) $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group temporary-radio-id-group {{ (string) $hasTemporaryRadio === '1' ? '' : 'hidden' }}" style="{{ (string) $hasTemporaryRadio === '1' ? '' : 'display:none;' }}">
                        <label class="form-label">State Temporary / Swapped WT Radio ID <span class="required">*</span></label>
                        <select name="temporary_radio_id" id="temporary_radio_id" class="form-input page-tag-select temporary-radio-id-input" data-placeholder="Type or select temporary radio id">
                            <option value=""></option>
                            @foreach($walkieTemporaryIds as $temporaryRadioId)
                            <option value="{{ $temporaryRadioId }}" @selected(old('temporary_radio_id', $defaults['temporary_radio_id'] ?? null) === $temporaryRadioId)>{{ $temporaryRadioId }}</option>
                            @endforeach
                            @if(old('temporary_radio_id') && !$walkieTemporaryIds->contains(old('temporary_radio_id')))
                            <option value="{{ old('temporary_radio_id') }}" selected>{{ old('temporary_radio_id') }}</option>
                            @endif
                        </select>
                    </div>
                @endunless

                <div class="form-group">
                    <label class="form-label">Tracking REF</label>
                    @php($currentTrackingRef = strtoupper((string) old('tracking_ref', $defaults['tracking_ref'] ?? '')))
                    <select name="tracking_ref" class="form-input page-combo-select" data-placeholder="Type or search tracking ref">
                        <option value=""></option>
                        @foreach($walkieTrackingRefs as $trackingRef)
                        <option value="{{ $trackingRef }}" @selected($currentTrackingRef === $trackingRef)>{{ $trackingRef }}</option>
                        @endforeach
                        @if($currentTrackingRef !== '' && !$walkieTrackingRefs->contains($currentTrackingRef))
                        <option value="{{ $currentTrackingRef }}" selected>{{ $currentTrackingRef }}</option>
                        @endif
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Need To Change ID</label>
                    <select name="need_to_change_id" class="form-input page-smart-select" data-placeholder="Search option">
                        @foreach($yesNoOptions as $option)
                        <option value="{{ $option['value'] }}" {{ old('need_to_change_id', $defaults['need_to_change_id'] ?? '0') == $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">ID Change Done</label>
                    <select name="id_change_done" class="form-input page-smart-select" data-placeholder="Search option">
                        @foreach($yesNoOptions as $option)
                        <option value="{{ $option['value'] }}" {{ old('id_change_done', $defaults['id_change_done'] ?? '0') == $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Ownership Type To Be</label>
                    <select name="ownership_type_to_be" class="form-input page-tag-select" data-placeholder="Type or search target ownership type">
                        <option value="">Select target ownership type...</option>
                        @foreach($ownershipTypeOptions as $ownershipTypeTarget)
                        <option value="{{ $ownershipTypeTarget }}" {{ old('ownership_type_to_be', $defaults['ownership_type_to_be'] ?? null) == $ownershipTypeTarget ? 'selected' : '' }}>{{ $ownershipTypeTarget }}</option>
                        @endforeach
                        @if(old('ownership_type_to_be') && !$ownershipTypeOptions->contains(strtoupper(trim(old('ownership_type_to_be')))))
                        <option value="{{ strtoupper(trim(old('ownership_type_to_be'))) }}" selected>{{ strtoupper(trim(old('ownership_type_to_be'))) }}</option>
                        @endif
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Special Use</label>
                    <select name="is_special_use" class="form-input page-smart-select" data-placeholder="Search option">
                        @foreach($yesNoOptions as $option)
                        <option value="{{ $option['value'] }}" {{ old('is_special_use', $defaults['is_special_use'] ?? '0') == $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Returned</label>
                    <select name="special_use_returned" class="form-input page-smart-select" data-placeholder="Search option">
                        @foreach($yesNoOptions as $option)
                        <option value="{{ $option['value'] }}" {{ old('special_use_returned', $defaults['special_use_returned'] ?? '0') == $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-section-title form-group-full">Warranty Information</div>

                <div class="form-group">
                    <label class="form-label">WT Warranty Start Date</label>
                    <input type="date" name="wt_warranty_start_date" class="form-input" value="{{ old('wt_warranty_start_date', $defaults['wt_warranty_start_date'] ?? '') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">WT Warranty End Date</label>
                    <input type="date" name="wt_warranty_end_date" class="form-input" value="{{ old('wt_warranty_end_date', $defaults['wt_warranty_end_date'] ?? '') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Battery Warranty Start Date</label>
                    <input type="date" name="battery_warranty_start_date" class="form-input" value="{{ old('battery_warranty_start_date', $defaults['battery_warranty_start_date'] ?? '') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Battery Warranty End Date</label>
                    <input type="date" name="battery_warranty_end_date" class="form-input" value="{{ old('battery_warranty_end_date', $defaults['battery_warranty_end_date'] ?? '') }}">
                </div>

                <div class="form-group form-group-full">
                    <div class="form-section-title">Remarks</div>
                    <label class="form-label">Remark</label>
                    <textarea name="remark" class="form-input form-textarea" placeholder="Additional notes...">{{ old('remark', $defaults['remark'] ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <div class="walkie-form-footer">
            <a href="{{ $backRoute ?? route('wt.admin.walkies.index') }}" class="btn-cancel">Back</a>
            <button type="submit" class="btn-submit">{{ $submitLabel ?? 'Save Unit' }}</button>
        </div>
    </form>
</div>

<style>
    .walkie-create-shell {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 22px;
        overflow: hidden;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
    }

    .dark .walkie-create-shell {
        background: #111827;
        border-color: #334155;
        box-shadow: 0 20px 44px rgba(0, 0, 0, 0.42);
    }

    .walkie-form-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        padding: 30px 48px;
        border-bottom: 1px solid #e8edf4;
        background: #ffffff;
    }

    .walkie-form-title {
        margin: 0;
        font-size: 18px;
        line-height: 1.2;
        font-weight: 900;
        letter-spacing: 0.02em;
        color: #1e293b;
        text-transform: uppercase;
    }

    .walkie-form-subtitle {
        margin-top: 8px;
        font-size: 12px;
        line-height: 1.55;
        color: #64748b;
        font-weight: 700;
    }

    .walkie-form-mode-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex: 0 0 auto;
    }

    .walkie-mode-link {
        display: inline-flex;
        min-height: 40px;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border-radius: 10px;
        border: 1px solid #dbe3ee;
        background: #ffffff;
        padding: 0 15px;
        color: #334155;
        font-size: 11px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        white-space: nowrap;
        transition: background-color 0.18s ease, border-color 0.18s ease, color 0.18s ease;
    }

    .walkie-mode-link:hover {
        border-color: #0284c7;
        background: #eff6ff;
        color: #075985;
    }

    .dark .walkie-form-header {
        border-bottom-color: #243041;
        background: linear-gradient(180deg, #172033 0%, #111827 100%);
    }

    .dark .walkie-form-title {
        color: #f8fafc;
    }

    .dark .walkie-form-subtitle {
        color: #cbd5e1;
    }

    .dark .walkie-mode-link {
        border-color: #334155;
        background: #0f172a;
        color: #e2e8f0;
    }

    .dark .walkie-mode-link:hover {
        border-color: #60a5fa;
        background: #172033;
        color: #bfdbfe;
    }

    .walkie-form-body {
        padding: 34px 48px 42px;
        background: #f8fafc;
    }

    .dark .walkie-form-body {
        background: #111827;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        align-items: start;
        gap: 26px 24px;
    }

    .form-group {
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 9px;
    }

    .form-group-full {
        grid-column: 1 / -1;
        min-width: 0;
    }

    .form-section-title {
        margin: 14px 0 -2px;
        padding: 20px 0 0;
        border-top: 1px solid #dde6f0;
        color: #334155;
        font-size: 12px;
        font-weight: 900;
        letter-spacing: 0.14em;
        line-height: 1.2;
        text-transform: uppercase;
    }

    .form-grid > .form-section-title:first-child {
        margin-top: 0;
        padding-top: 0;
        border-top: 0;
    }

    .dark .form-section-title {
        border-top-color: #334155;
        color: #cbd5e1;
    }

    .form-label {
        margin: 0;
        font-size: 10.5px;
        font-weight: 900;
        letter-spacing: 0.1em;
        line-height: 1.25;
        text-transform: uppercase;
        color: #64748b;
        overflow-wrap: anywhere;
    }

    .dark .form-label {
        color: #dbe4f0;
    }

    .required {
        color: #dc2626;
    }

    .form-input {
        width: 100%;
        max-width: 100%;
        min-height: 48px;
        border-radius: 12px;
        border: 1px solid #d8e1ed;
        background: #ffffff;
        padding: 13px 14px;
        font-size: 13px;
        font-weight: 700;
        line-height: 1.35;
        color: #334155;
        outline: none;
        transition: border-color 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
    }

    .form-input:focus {
        border-color: #0284c7;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.11);
    }

    .dark .form-input {
        border-color: #334155;
        background: #0f172a;
        color: #f8fafc;
    }

    .dark .form-input::placeholder {
        color: #94a3b8;
    }

    .dark .form-input:focus {
        border-color: #60a5fa;
        background: #111827;
        box-shadow: 0 0 0 4px rgba(96, 165, 250, 0.16);
    }

    .form-textarea {
        min-height: 132px;
        resize: vertical;
    }

    .select2-container {
        width: 100% !important;
        max-width: 100% !important;
    }

    .select2-container--default .select2-selection--single,
    .select2-container--default .select2-selection--multiple {
        min-height: 48px;
        border-radius: 12px;
        border: 1px solid #d8e1ed;
        background: #ffffff;
        padding: 7px 14px;
        display: flex;
        align-items: center;
        box-shadow: none;
    }

    .dark .select2-container--default .select2-selection--single,
    .dark .select2-container--default .select2-selection--multiple {
        border-color: #334155;
        background: #0f172a;
    }

    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--focus .select2-selection--multiple,
    .select2-container--default.select2-container--open .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--multiple {
        border-color: #0284c7;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.11);
    }

    .dark .select2-container--default.select2-container--focus .select2-selection--single,
    .dark .select2-container--default.select2-container--focus .select2-selection--multiple,
    .dark .select2-container--default.select2-container--open .select2-selection--single,
    .dark .select2-container--default.select2-container--open .select2-selection--multiple {
        border-color: #60a5fa;
        background: #111827;
        box-shadow: 0 0 0 4px rgba(96, 165, 250, 0.16);
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered,
    .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        color: #334155;
        font-size: 13px;
        font-weight: 700;
        line-height: 1.4;
        padding-left: 0;
        padding-right: 24px;
    }

    .dark .select2-container--default .select2-selection--single .select2-selection__rendered,
    .dark .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        color: #f8fafc;
    }

    .select2-container--default .select2-selection--single .select2-selection__placeholder,
    .select2-container--default .select2-search--inline .select2-search__field::placeholder {
        color: #94a3b8;
        font-weight: 600;
    }

    .dark .select2-container--default .select2-selection--single .select2-selection__placeholder,
    .dark .select2-container--default .select2-search--inline .select2-search__field::placeholder {
        color: #94a3b8;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px;
        right: 12px;
    }

    .dark .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-top-color: #94a3b8;
    }

    .select2-dropdown {
        border: 1px solid #e7e5e4;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.16);
    }

    .dark .select2-dropdown {
        border-color: #334155;
        background: #0f172a;
        box-shadow: 0 18px 40px rgba(0, 0, 0, 0.42);
    }

    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #e7e5e4;
        border-radius: 12px;
        padding: 10px 12px;
        font-size: 12px;
        font-weight: 700;
        color: #334155;
        outline: none;
    }

    .dark .select2-container--default .select2-search--dropdown .select2-search__field {
        border-color: #334155;
        background: #111827;
        color: #f8fafc;
    }

    .select2-results__option {
        font-size: 12px;
        font-weight: 700;
        padding: 10px 14px;
    }

    .dark .select2-results__option {
        color: #e2e8f0;
        background: #0f172a;
    }

    .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
        background: #0284c7;
        color: #fff;
    }

    .select2-container--default .select2-results__option--selected {
        background: #e0f2fe;
        color: #075985;
    }

    .dark .select2-container--default .select2-results__option--selected {
        background: #172033;
        color: #f8fafc;
    }

    .walkie-form-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        padding: 22px 48px;
        border-top: 1px solid #e8edf4;
        background: #ffffff;
    }

    .dark .walkie-form-footer {
        border-top-color: #243041;
        background: #0f172a;
    }

    .alert-error {
        display: flex;
        align-items: flex-start;
        padding: 10px 14px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        background: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    @media (max-width: 1100px) {
        .form-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 768px) {
        .walkie-form-header {
            align-items: stretch;
            flex-direction: column;
        }

        .walkie-form-header,
        .walkie-form-body,
        .walkie-form-footer {
            padding-left: 18px;
            padding-right: 18px;
        }

        .walkie-form-header {
            padding-top: 22px;
            padding-bottom: 20px;
        }

        .walkie-form-footer {
            flex-direction: column-reverse;
        }

        .form-grid {
            grid-template-columns: 1fr;
            gap: 18px;
        }

        .walkie-form-footer > * {
            width: 100%;
            justify-content: center;
        }

        .walkie-form-mode-actions,
        .walkie-mode-link {
            width: 100%;
        }
    }
</style>

<script>
    $(document).ready(function() {
        function focusOpenSelect2Search() {
            window.setTimeout(function () {
                const searchField = document.querySelector('.select2-container--open .select2-search__field');

                if (searchField) {
                    searchField.removeAttribute('readonly');
                    searchField.removeAttribute('disabled');
                    searchField.focus();
                }
            }, 80);
        }

        $('.page-tag-select').each(function() {
            const $select = $(this);

            $select.select2({
                width: '100%',
                tags: true,
                allowClear: !$select.prop('required'),
                minimumResultsForSearch: 0,
                placeholder: $select.data('placeholder') || 'Type or select option',
                createTag: function(params) {
                    const term = $.trim(params.term);

                    if (term === '') {
                        return null;
                    }

                    const normalizedTerm = term.toUpperCase();

                    return {
                        id: normalizedTerm,
                        text: normalizedTerm,
                        newTag: true
                    };
                },
                insertTag: function(data, tag) {
                    data.unshift(tag);
                }
            });

            $select.on('select2:open', focusOpenSelect2Search);
        });

        $('.page-combo-select').each(function() {
            const $select = $(this);

            $select.select2({
                width: '100%',
                tags: true,
                allowClear: !$select.prop('required'),
                minimumResultsForSearch: 0,
                placeholder: $select.data('placeholder') || 'Type or search option',
                createTag: function(params) {
                    const term = $.trim(params.term);

                    if (term === '') {
                        return null;
                    }

                    const normalizedTerm = term.toUpperCase();

                    return {
                        id: normalizedTerm,
                        text: normalizedTerm,
                        newTag: true
                    };
                },
                insertTag: function(data, tag) {
                    data.unshift(tag);
                }
            });

            $select.on('select2:open', focusOpenSelect2Search);
        });

        $('.page-smart-select').each(function() {
            const $select = $(this);

            $select.select2({
                width: '100%',
                allowClear: !$select.prop('required'),
                placeholder: $select.data('placeholder') || 'Search option'
            });

            $select.on('select2:open', focusOpenSelect2Search);
        });

        function syncSharedWith($select) {
            const $form = $select.closest('form');
            const isShared = String($select.val() || '').toUpperCase() === 'SHARED';
            const $group = $form.find('.shared-with-group');
            const $input = $form.find('.shared-with-input');

            $group.toggleClass('hidden', !isShared);
            $input.prop('required', isShared);

            if (!isShared) {
                $input.val('');
            }
        }

        $('.ownership-type-control').on('change select2:select', function() {
            syncSharedWith($(this));
        }).each(function() {
            syncSharedWith($(this));
        });

        function syncTemporaryRadioField() {
            if (!$('#has_temporary_radio').length || !$('#temporary_radio_id').length) {
                return;
            }

            const hasTemporary = String($('#has_temporary_radio').val() || '0') === '1';
            const $group = $('.temporary-radio-id-group');
            const $input = $('#temporary_radio_id');

            $group.toggleClass('hidden', !hasTemporary);
            $group.css('display', hasTemporary ? '' : 'none');
            $input.prop('required', hasTemporary).prop('disabled', !hasTemporary);

            if (!hasTemporary) {
                $input.val(null).trigger('change');
            }
        }

        $('#has_temporary_radio').on('change select2:select', syncTemporaryRadioField);
        syncTemporaryRadioField();
    });
</script>
@endsection
