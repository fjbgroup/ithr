@extends(request()->routeIs('wt.admin.*') ? 'wt.layouts.admin' : 'wt.layouts.user')

@php
    $routePrefix = request()->routeIs('wt.admin.*') ? 'wt.admin' : 'wt.user';
@endphp

@section('title', 'Return Unit')

@push('styles')
<style>
    .return-shell {
        color: #1f2937;
    }
    .return-panel,
    .return-unit-card,
    .return-empty {
        border: 1px solid #e7e5e4;
        background: #ffffff;
        box-shadow: none;
    }
    .return-kicker {
        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: #38bdf8;
    }
    .return-title {
        margin-top: 0;
        font-size: 16px;
        font-weight: 900;
        color: #1f2937;
        letter-spacing: -0.01em;
    }
    .return-subtitle {
        margin-top: 7px;
        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: #64748b;
    }
    .return-panel {
        border-radius: 8px;
        padding: 18px;
    }
    .return-section-title {
        display: flex;
        align-items: center;
        gap: 9px;
        margin-bottom: 16px;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: #1f2937;
    }
    .return-section-title i {
        display: inline-flex;
        width: 26px;
        height: 26px;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #64748b;
    }
    .return-readonly {
        width: 100%;
        min-height: 34px;
        border-radius: 10px;
        border: 1px solid #d6d3d1;
        background: #f8fafc;
        padding: 8px 10px;
        color: #1f2937;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
    }
    .return-unit-card {
        display: block;
        min-height: 0;
        border-radius: 7px;
        padding: 0;
        cursor: pointer;
        overflow: hidden;
        transition: border-color 0.16s ease, background 0.16s ease;
    }
    .return-unit-card:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
    }
    .return-unit-radio:checked + .return-unit-card {
        border-color: #334155;
        background: #ffffff;
    }
    .return-unit-check {
        display: inline-flex;
        width: 20px;
        height: 20px;
        align-items: center;
        justify-content: center;
        border-radius: 5px;
        border: 1px solid #cbd5e1;
        color: transparent;
        transition: all 0.16s ease;
    }
    .return-unit-radio:checked + .return-unit-card .return-unit-check {
        border-color: #334155;
        background: #334155;
        color: #ffffff;
    }
    .return-date-input {
        width: 100%;
        min-height: 36px;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        padding: 8px 10px;
        color: #1f2937;
        font-size: 11px;
        font-weight: 900;
        outline: none;
    }
    .return-submit-btn {
        display: inline-flex;
        width: 100%;
        min-height: 36px;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border-radius: 6px;
        background: #334155;
        color: #ffffff;
        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        transition: background 0.16s ease;
    }
    .return-submit-btn:hover {
        background: #1e293b;
    }
    .return-empty {
        border-radius: 12px;
        padding: 18px 14px;
        text-align: center;
    }
    .return-empty-icon {
        width: 38px;
        height: 38px;
        border-radius: 12px;
    }
    .return-empty-icon i {
        font-size: 13px !important;
    }
    .return-empty-title {
        font-size: 12px;
        line-height: 1.15;
    }
    .return-empty-copy {
        margin-top: 5px;
        font-size: 8px;
        line-height: 1.25;
        letter-spacing: 0.12em;
    }
    .return-empty-title,
    .return-unit-title {
        color: #1f2937;
    }
    .return-muted-copy {
        color: #475569;
    }
    .return-unit-list {
        margin-top: 0;
        display: grid;
        gap: 4px;
    }
    .return-unit-pill {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        border-radius: 5px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        padding: 5px 7px;
        font-size: 8px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #334155;
    }
    .return-unit-pill span:last-child {
        color: #94a3b8;
    }
    .dark .return-shell {
        color: #e2e8f0;
    }
    .dark .return-panel,
    .dark .return-unit-card,
    .dark .return-empty {
        border-color: rgba(148, 163, 184, 0.16);
        background: #152033;
    }
    .dark .return-title,
    .dark .return-section-title,
    .dark .return-empty-title,
    .dark .return-unit-title {
        color: #f8fafc;
    }
    .dark .return-subtitle {
        color: #94a3b8;
    }
    .dark .return-readonly,
    .dark .return-date-input {
        border-color: rgba(148, 163, 184, 0.18);
        background: rgba(15, 23, 42, 0.36);
        color: #e2e8f0;
    }
    .dark .return-unit-card:hover {
        background: #19263b;
    }
    .dark .return-unit-radio:checked + .return-unit-card {
        background: #152033;
    }
    .dark .return-muted-copy {
        color: #cbd5e1;
    }
    .dark .return-unit-pill {
        border-color: rgba(148, 163, 184, 0.16);
        background: rgba(15, 23, 42, 0.34);
        color: #e2e8f0;
    }
    .return-page-card {
        border-radius: 8px;
        border: 1px solid #dbe3ef;
        background: #ffffff;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }
    .return-assignment-head {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 12px;
        align-items: start;
        border-bottom: 1px solid #e2e8f0;
        padding: 12px 14px;
    }
    .return-assignment-meta {
        border-top: 1px solid #e2e8f0;
        background: #ffffff;
    }
    .return-assignment-cell {
        display: grid;
        grid-template-columns: 120px 1fr;
        gap: 12px;
        align-items: center;
        border-bottom: 1px solid #e2e8f0;
        padding: 9px 14px;
    }
    .return-assignment-cell:last-child {
        border-bottom: 0;
    }
    .return-assignment-cell-value {
        min-width: 0;
    }
    .return-review-box {
        border: 1px solid #e2e8f0;
        border-radius: 7px;
        background: #f8fafc;
        padding: 12px;
    }
    .return-search-wrap {
        margin-bottom: 12px;
    }
    .return-search-input {
        width: 100%;
        min-height: 36px;
        border-radius: 7px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        padding: 8px 12px;
        color: #1f2937;
        font-size: 11px;
        font-weight: 800;
        outline: none;
    }
    .return-search-input:focus {
        border-color: #334155;
        box-shadow: 0 0 0 3px rgba(51, 65, 85, 0.08);
    }
    .return-search-empty {
        display: none;
        border: 1px dashed #cbd5e1;
        border-radius: 7px;
        padding: 14px;
        text-align: center;
        color: #94a3b8;
        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }
    .return-form-alert {
        display: none;
        border: 1px solid #fecaca;
        border-radius: 7px;
        background: #fef2f2;
        padding: 10px 12px;
        color: #b91c1c;
        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }
    .return-person-field {
        position: relative;
    }
    .return-person-combobox {
        position: relative;
    }
    .return-person-toggle {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        font-size: 10px;
        pointer-events: none;
    }
    .return-person-suggestions {
        position: absolute;
        z-index: 40;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        display: none;
        max-height: 260px;
        overflow: hidden;
        border: 1px solid #cbd5e1;
        border-radius: 0 0 14px 14px;
        background: #fffaf5;
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.12);
    }
    .return-person-list {
        max-height: 220px;
        overflow-y: auto;
        padding: 8px 0;
    }
    .return-person-option {
        width: 100%;
        border-bottom: 0;
        padding: 11px 16px;
        text-align: left;
    }
    .return-person-option:hover,
    .return-person-option:focus {
        background: #f1e4d5;
        outline: none;
    }
    .return-person-option-name {
        display: block;
        color: #3d2b1f;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
    }
    .return-person-option-meta {
        margin-top: 3px;
        display: block;
        color: #8b6f58;
        font-size: 8px;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }
    .dark .return-page-card,
    .dark .return-assignment-cell {
        border-color: rgba(148, 163, 184, 0.16);
        background: #152033;
    }
    .dark .return-assignment-head {
        border-color: rgba(148, 163, 184, 0.16);
    }
    .dark .return-assignment-meta {
        border-color: rgba(148, 163, 184, 0.16);
        background: #152033;
    }
    .dark .return-review-box {
        border-color: rgba(148, 163, 184, 0.16);
        background: rgba(15, 23, 42, 0.34);
    }
    .dark .return-search-input {
        border-color: rgba(148, 163, 184, 0.18);
        background: rgba(15, 23, 42, 0.36);
        color: #e2e8f0;
    }
    .dark .return-search-empty {
        border-color: rgba(148, 163, 184, 0.24);
        color: #64748b;
    }
    .dark .return-form-alert {
        border-color: rgba(248, 113, 113, 0.28);
        background: rgba(127, 29, 29, 0.22);
        color: #fecaca;
    }
    .dark .return-person-suggestions {
        border-color: rgba(148, 163, 184, 0.24);
        background: #152033;
    }
    .dark .return-person-option {
        border-color: rgba(148, 163, 184, 0.16);
    }
    .dark .return-person-option:hover,
    .dark .return-person-option:focus {
        background: #19263b;
    }
    .dark .return-person-option-name {
        color: #f8fafc;
    }
    .dark .return-person-option-meta {
        color: #94a3b8;
    }
    @media (max-width: 640px) {
        .return-assignment-cell {
            grid-template-columns: 1fr;
            gap: 3px;
        }
    }
</style>
@endpush

@section('content')
@php
    $isAdminRoute = request()->routeIs('wt.admin.*');
    $mode = $mode ?? ($isAdminRoute ? 'self' : 'self');
    $returnPeople = $returnPeople ?? collect();
    $returnPeopleOptions = $returnPeople->map(function ($person) {
        return [
            'name' => strtoupper((string) ($person['name'] ?? '')),
            'department' => strtoupper((string) ($person['department'] ?? '')),
            'phone_no' => (string) ($person['phone_no'] ?? ''),
        ];
    })->values();
@endphp
<div class="return-shell">
    <div class="page-header-block flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h3 class="page-title-standard">Return Unit</h3>
            <p class="page-subtitle-standard">Submit return requests for active walkie talkie assignments.</p>
        </div>
    </div>

    <div class="return-page-card">
    @if($isAdminRoute && $mode === 'staff')
    <div class="m-4 return-panel">
        <h4 class="return-section-title"><i class="fa-solid fa-user-tie"></i> Executive Details</h4>
        <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
            <div>
                <label class="mb-1 block text-[9px] font-black uppercase tracking-widest text-slate-400">Executive Name</label>
                <input type="text" value="{{ strtoupper(auth('wt')->user()->full_name ?: auth('wt')->user()->username) }}" class="return-readonly" readonly>
            </div>
            <div>
                <label class="mb-1 block text-[9px] font-black uppercase tracking-widest text-slate-400">Executive Staff ID</label>
                <input type="text" value="{{ strtoupper(auth('wt')->user()->staff_id ?: '-') }}" class="return-readonly" readonly>
            </div>
            <div>
                <label class="mb-1 block text-[9px] font-black uppercase tracking-widest text-slate-400">Executive Department</label>
                <input type="text" value="{{ strtoupper(auth('wt')->user()->department ?: 'GENERAL') }}" class="return-readonly" readonly>
            </div>
        </div>
    </div>
    @endif

    @if($activeAssets->isEmpty())
        <div class="m-4 return-empty">
            <div class="return-empty-icon mx-auto mb-2 flex items-center justify-center border border-[#0284c7]/20 bg-[#0284c7]/10 text-[#38bdf8]">
                <i class="fa-solid fa-box-open text-lg"></i>
            </div>
            <h4 class="return-empty-title font-black">No Active Units</h4>
            <p class="return-empty-copy font-bold uppercase text-slate-400">There are no active walkie talkie assignments available for return.</p>
        </div>
    @else
        <form action="{{ $isAdminRoute ? route($routePrefix . '.returns.store', ['mode' => $mode]) : route($routePrefix . '.returns.store') }}" method="POST" class="grid grid-cols-1 gap-4 p-4 lg:grid-cols-[1fr_340px]" id="returnUnitForm" novalidate>
            @csrf

            <div class="return-panel">
                <h4 class="return-section-title"><i class="fa-solid fa-walkie-talkie"></i> {{ $isAdminRoute && $mode === 'staff' ? 'Select Recipient Unit' : 'Select Unit' }}</h4>
                <div class="return-search-wrap">
                    <input type="search" id="returnUnitSearch" class="return-search-input" placeholder="Search request, radio ID, serial, purpose, or date...">
                </div>
                <div class="grid grid-cols-1 gap-3" id="returnUnitList">
                    @foreach($activeAssets as $asset)
                    @php
                        $assignedWalkieIds = is_array($asset->assigned_walkie_inventory_ids) ? $asset->assigned_walkie_inventory_ids : [];
                        $walkieIds = collect($assignedWalkieIds)->filter()->values();
                        if ($walkieIds->isEmpty() && $asset->walkie_inventory_id) {
                            $walkieIds = collect([$asset->walkie_inventory_id]);
                        }
                        $assignedRadioIds = is_array($asset->assigned_radio_ids) ? $asset->assigned_radio_ids : [];
                        $assignedSerials = is_array($asset->assigned_serial_numbers) ? $asset->assigned_serial_numbers : [];
                        $unitIds = collect($assignedRadioIds)->filter()->values();
                        if ($unitIds->isEmpty() && $asset->radio_id) {
                            $unitIds = collect([$asset->radio_id]);
                        }
                        $serials = collect($assignedSerials)->filter()->values();
                        if ($serials->isEmpty() && $asset->assigned_serial_number) {
                            $serials = collect([$asset->assigned_serial_number]);
                        }
                        $picDetails = collect($asset->pic_details ?? [])->filter(fn ($pic) => is_array($pic))->values();
                        $displayQuantity = max((int) ($asset->quantity ?: 1), $unitIds->count(), 1);
                    @endphp
                    @forelse($unitIds as $unitIndex => $radioId)
                    @php
                        $unitPic = $picDetails->get($unitIndex, []);
                        $unitOwnership = !empty($unitPic['name'])
                            ? $unitPic['name']
                            : ($asset->full_name ?: optional($asset->user)->username ?: '-');
                        $unitOwnershipType = !empty($unitPic['ownership_type'])
                            ? $unitPic['ownership_type']
                            : ($asset->ownership_type ?: '-');
                        $unitDepartment = !empty($unitPic['department'])
                            ? $unitPic['department']
                            : ($asset->department ?: '-');
                    @endphp
                    <div class="relative" data-return-unit-item data-return-search="{{ strtoupper('REQUEST ' . str_pad($asset->id, 5, '0', STR_PAD_LEFT) . ' ' . $radioId . ' ' . ($serials->get($unitIndex) ?: '') . ' ' . $unitOwnership . ' ' . $unitOwnershipType . ' ' . $unitDepartment . ' ' . ($asset->event_name ?: 'Walkie Talkie Request') . ' ' . ($asset->request_date ? \Carbon\Carbon::parse($asset->request_date)->format('d M Y') : '')) }}">
                        <input
                            type="radio"
                            name="access_request_id"
                            id="asset_{{ $asset->id }}_unit_{{ $unitIndex }}"
                            value="{{ $asset->id }}"
                            class="return-unit-radio peer sr-only"
                            data-return-walkie-id="{{ $walkieIds->get($unitIndex) }}"
                            data-return-radio-id="{{ $radioId }}"
                            data-return-serial-number="{{ $serials->get($unitIndex) }}"
                            required
                        >
                        <label for="asset_{{ $asset->id }}_unit_{{ $unitIndex }}" class="return-unit-card" data-return-unit-card>
                            <div class="return-assignment-head">
                                <div class="min-w-0">
                                    <p class="text-[8px] font-black uppercase tracking-[0.18em] text-slate-400">Request #{{ str_pad($asset->id, 5, '0', STR_PAD_LEFT) }}</p>
                                    <p class="return-unit-title mt-1 text-[13px] font-black">
                                        Radio ID {{ $radioId }} To Return
                                    </p>
                                    @if($isAdminRoute && $mode === 'staff')
                                    <p class="mt-1 text-[9px] font-bold uppercase tracking-wider text-slate-400">Recipient: {{ strtoupper($asset->full_name ?: optional($asset->user)->username ?: '-') }}</p>
                                    @endif
                                </div>
                                <div class="return-unit-check">
                                    <i class="fa-solid fa-check text-[10px]"></i>
                                </div>
                            </div>
                            <div class="return-assignment-meta">
                                <div class="return-assignment-cell">
                                    <p class="text-[8px] font-black uppercase tracking-widest text-slate-400">Selected Unit</p>
                                    <div class="return-assignment-cell-value return-unit-list">
                                        <div class="return-unit-pill">
                                            <span>{{ $radioId }}</span>
                                            <span>{{ $serials->get($unitIndex) ?: 'Serial -' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="return-assignment-cell">
                                    <p class="text-[8px] font-black uppercase tracking-widest text-slate-400">Ownership</p>
                                    <div class="return-assignment-cell-value">
                                        <p class="return-muted-copy line-clamp-1 text-[11px] font-black uppercase leading-4">{{ $unitOwnership }}</p>
                                        <p class="mt-0.5 text-[8px] font-black uppercase tracking-widest text-slate-400">{{ $unitOwnershipType }}</p>
                                    </div>
                                </div>
                                <div class="return-assignment-cell">
                                    <p class="text-[8px] font-black uppercase tracking-widest text-slate-400">Department</p>
                                    <p class="return-assignment-cell-value return-muted-copy line-clamp-2 text-[11px] font-black uppercase leading-4">{{ $unitDepartment }}</p>
                                </div>
                                <div class="return-assignment-cell is-purpose">
                                    <p class="text-[8px] font-black uppercase tracking-widest text-slate-400">Issued For</p>
                                    <p class="return-assignment-cell-value return-muted-copy line-clamp-2 text-[11px] font-bold leading-4">{{ $asset->event_name ?: 'Walkie Talkie Request' }}</p>
                                </div>
                                <div class="return-assignment-cell">
                                    <p class="text-[8px] font-black uppercase tracking-widest text-slate-400">Request Date</p>
                                    <p class="return-assignment-cell-value text-[11px] font-black uppercase tracking-wider text-slate-700 dark:text-slate-200">{{ $asset->request_date ? \Carbon\Carbon::parse($asset->request_date)->format('d M Y') : '-' }}</p>
                                </div>
                            </div>
                        </label>
                    </div>
                    @empty
                    <div class="relative" data-return-unit-item data-return-search="{{ strtoupper('REQUEST ' . str_pad($asset->id, 5, '0', STR_PAD_LEFT) . ' UNIT NOT ASSIGNED ' . ($asset->event_name ?: 'Walkie Talkie Request') . ' ' . ($asset->request_date ? \Carbon\Carbon::parse($asset->request_date)->format('d M Y') : '')) }}">
                        <input type="radio" name="access_request_id" id="asset_{{ $asset->id }}_unit_empty" value="{{ $asset->id }}" class="return-unit-radio peer sr-only" required>
                        <label for="asset_{{ $asset->id }}_unit_empty" class="return-unit-card" data-return-unit-card>
                            <div class="return-assignment-head">
                                <div class="min-w-0">
                                    <p class="text-[8px] font-black uppercase tracking-[0.18em] text-slate-400">Request #{{ str_pad($asset->id, 5, '0', STR_PAD_LEFT) }}</p>
                                    <p class="return-unit-title mt-1 text-[13px] font-black">Unit Not Assigned</p>
                                </div>
                                <div class="return-unit-check"><i class="fa-solid fa-check text-[10px]"></i></div>
                            </div>
                        </label>
                    </div>
                    @endforelse
                    @endforeach
                </div>
                <div id="returnUnitSearchEmpty" class="return-search-empty mt-3">No matching unit found.</div>
            </div>

            <div class="return-panel h-fit">
                <h4 class="return-section-title"><i class="fa-solid fa-calendar-check"></i> Return Details</h4>
                <input type="hidden" name="selected_walkie_inventory_id" id="selectedWalkieInventoryId">
                <input type="hidden" name="selected_radio_id" id="selectedRadioId">
                <input type="hidden" name="selected_serial_number" id="selectedSerialNumber">
                <div>
                    <label class="mb-1.5 block text-[9px] font-black uppercase tracking-widest text-slate-400">Return Date</label>
                    <input type="date" name="return_date" value="{{ date('Y-m-d') }}" class="return-date-input" required>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-3">
                    <div class="return-person-field">
                        <label class="mb-1.5 block text-[9px] font-black uppercase tracking-widest text-slate-400">Returned By</label>
                        <div class="return-person-combobox">
                            <input type="text" name="return_person" id="returnPersonInput" value="{{ old('return_person') }}" class="return-date-input pr-8" placeholder="Search or type returner's name" autocomplete="off" required>
                            <span class="return-person-toggle"><i class="fa-solid fa-caret-down"></i></span>
                        </div>
                        <div id="returnPersonSuggestions" class="return-person-suggestions"></div>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-[9px] font-black uppercase tracking-widest text-slate-400">Department</label>
                        <input type="text" name="return_department" id="returnDepartmentInput" value="{{ old('return_department') }}" class="return-date-input" placeholder="Department" required>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-[9px] font-black uppercase tracking-widest text-slate-400">Phone No</label>
                        <input type="text" name="return_phone_no" id="returnPhoneInput" value="{{ old('return_phone_no') }}" class="return-date-input" placeholder="E.g. 012-3456789" required>
                    </div>
                </div>

                <div class="return-review-box mt-4">
                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-500">Review</p>
                    <p class="return-muted-copy mt-1 text-[10px] font-bold leading-5">Select one active assignment and submit it for ICT return confirmation.</p>
                </div>
                <div class="return-form-alert mt-3" id="returnFormAlert"></div>

                <button type="submit" class="return-submit-btn mt-4">
                    Submit Return <i class="fas fa-check"></i>
                </button>
            </div>
        </form>
    @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const returnPeople = @json($returnPeopleOptions);
        const returnPersonInput = document.getElementById('returnPersonInput');
        const returnPersonSuggestions = document.getElementById('returnPersonSuggestions');
        const returnDepartmentInput = document.getElementById('returnDepartmentInput');
        const returnPhoneInput = document.getElementById('returnPhoneInput');
        const returnUnitForm = document.getElementById('returnUnitForm');
        const returnFormAlert = document.getElementById('returnFormAlert');
        const selectedWalkieInventoryId = document.getElementById('selectedWalkieInventoryId');
        const selectedRadioId = document.getElementById('selectedRadioId');
        const selectedSerialNumber = document.getElementById('selectedSerialNumber');

        function syncSelectedUnit(radio) {
            if (!radio || !selectedWalkieInventoryId || !selectedRadioId || !selectedSerialNumber) {
                return;
            }

            selectedWalkieInventoryId.value = radio.checked ? (radio.dataset.returnWalkieId || '') : '';
            selectedRadioId.value = radio.checked ? (radio.dataset.returnRadioId || '') : '';
            selectedSerialNumber.value = radio.checked ? (radio.dataset.returnSerialNumber || '') : '';
        }

        document.querySelectorAll('.return-unit-radio').forEach((radio) => {
            radio.addEventListener('change', () => syncSelectedUnit(radio));
        });

        const searchInput = document.getElementById('returnUnitSearch');
        const searchEmpty = document.getElementById('returnUnitSearchEmpty');
        const unitItems = Array.from(document.querySelectorAll('[data-return-unit-item]'));

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const query = searchInput.value.trim().toUpperCase();
                let visibleCount = 0;

                unitItems.forEach((item) => {
                    const isMatch = query === '' || (item.dataset.returnSearch || '').includes(query);
                    item.classList.toggle('hidden', !isMatch);
                    if (isMatch) {
                        visibleCount += 1;
                    }
                });

                if (searchEmpty) {
                    searchEmpty.style.display = visibleCount === 0 ? 'block' : 'none';
                }
            });
        }

        if (returnPersonInput) {
            const fillReturnPerson = (person) => {
                returnPersonInput.value = person.name || '';

                if (returnDepartmentInput) {
                    returnDepartmentInput.value = person.department || '';
                }

                if (returnPhoneInput) {
                    returnPhoneInput.value = person.phone_no || '';
                }

                if (returnPersonSuggestions) {
                    returnPersonSuggestions.style.display = 'none';
                    returnPersonSuggestions.innerHTML = '';
                }
            };

            const renderReturnPersonSuggestions = () => {
                if (!returnPersonSuggestions) {
                    return;
                }

                const query = returnPersonInput.value.trim().toUpperCase();

                const matches = returnPeople
                    .filter((person) => `${person.name} ${person.department} ${person.phone_no}`.includes(query))
                    .slice(0, 10);

                returnPersonSuggestions.innerHTML = `
                    <div class="return-person-list">
                        ${matches.length > 0 ? matches.map((person, index) => `
                            <button type="button" class="return-person-option" data-return-person-index="${index}">
                                <span class="return-person-option-name">${person.name}</span>
                                ${person.department || person.phone_no ? `<span class="return-person-option-meta">${person.department || '-'}${person.phone_no ? ` / ${person.phone_no}` : ''}</span>` : ''}
                            </button>
                        `).join('') : '<div class="return-person-option-name px-4 py-3 text-slate-400">No match found</div>'}
                    </div>
                `;

                Array.from(returnPersonSuggestions.querySelectorAll('[data-return-person-index]')).forEach((button) => {
                    button.addEventListener('click', () => {
                        fillReturnPerson(matches[Number(button.dataset.returnPersonIndex)]);
                    });
                });

                returnPersonSuggestions.style.display = 'block';
            };

            returnPersonInput.addEventListener('input', function () {
                const selectedName = returnPersonInput.value.trim().toUpperCase();
                const matchedPerson = returnPeople.find((person) => person.name === selectedName);

                if (matchedPerson) {
                    fillReturnPerson(matchedPerson);
                } else {
                    renderReturnPersonSuggestions();
                }
            });

            returnPersonInput.addEventListener('focus', renderReturnPersonSuggestions);
            returnPersonInput.addEventListener('click', renderReturnPersonSuggestions);
            returnPersonInput.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && returnPersonSuggestions) {
                    returnPersonSuggestions.style.display = 'none';
                }
            });

            document.addEventListener('click', function (event) {
                if (!returnPersonSuggestions || event.target === returnPersonInput || returnPersonSuggestions.contains(event.target)) {
                    return;
                }

                returnPersonSuggestions.style.display = 'none';
            });
        }

        if (returnUnitForm) {
            returnUnitForm.addEventListener('submit', function (event) {
                const selectedUnit = document.querySelector('.return-unit-radio:checked');
                const requiredFields = [
                    { field: selectedUnit, message: 'Please select one walkie talkie unit to return.' },
                    { field: returnUnitForm.querySelector('[name="return_date"]'), message: 'Please enter return date.' },
                    { field: returnPersonInput, message: 'Please enter who returned this unit.' },
                    { field: returnDepartmentInput, message: 'Please enter returner department.' },
                    { field: returnPhoneInput, message: 'Please enter returner phone no.' },
                ];

                const missing = requiredFields.find((item) => {
                    if (!item.field) {
                        return true;
                    }

                    if (item.field.type === 'radio') {
                        return false;
                    }

                    return item.field.value.trim() === '';
                });

                if (!missing) {
                    return;
                }

                event.preventDefault();
                if (returnFormAlert) {
                    returnFormAlert.textContent = missing.message;
                    returnFormAlert.style.display = 'block';
                }

                if (missing.field && typeof missing.field.focus === 'function') {
                    missing.field.focus();
                }
            });
        }

        document.querySelectorAll('[data-return-unit-card]').forEach((card) => {
            card.addEventListener('click', function (event) {
                const radioId = card.getAttribute('for');
                const radio = radioId ? document.getElementById(radioId) : null;

                if (!radio || radio.type !== 'radio') {
                    return;
                }

                if (radio.checked) {
                    event.preventDefault();
                    radio.checked = false;
                    syncSelectedUnit(radio);
                    radio.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
        });
    });
</script>
@endpush

