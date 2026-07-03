@extends(request()->routeIs('wt.admin.*') ? 'wt.layouts.admin' : 'wt.layouts.user')

@php
    $routePrefix = request()->routeIs('wt.admin.*') ? 'wt.admin' : 'wt.user';
@endphp

@section('title', 'Return Unit')

@push('styles')
<style>
    .return-shell { color: var(--text); }
    .return-panel,
    .return-unit-card,
    .return-empty {
        border: 1px solid var(--border);
        background: var(--surface);
        box-shadow: none;
    }
    .return-kicker { font-size:9px;font-weight:900;letter-spacing:.18em;text-transform:uppercase;color:var(--sky); }
    .return-title { margin-top:0;font-size:16px;font-weight:900;color:var(--text);letter-spacing:-.01em; }
    .return-subtitle { margin-top:7px;font-size:9px;font-weight:900;letter-spacing:.18em;text-transform:uppercase;color:var(--muted); }
    .return-panel { border-radius:8px;padding:18px; }
    .return-section-title { display:flex;align-items:center;gap:9px;margin-bottom:16px;font-size:10px;font-weight:900;letter-spacing:.14em;text-transform:uppercase;color:var(--text); }
    .return-section-title i { display:inline-flex;width:26px;height:26px;align-items:center;justify-content:center;border-radius:6px;border:1px solid var(--border);background:var(--body-bg);color:var(--muted); }
    .return-readonly { width:100%;min-height:34px;border-radius:10px;border:1.5px solid var(--border);background:var(--body-bg);padding:8px 10px;color:var(--text);font-size:10px;font-weight:900;text-transform:uppercase; }
    .return-unit-card { display:block;min-height:0;border-radius:7px;padding:0;cursor:pointer;overflow:hidden;transition:border-color .16s ease,background .16s ease; }
    .return-unit-card:hover { border-color:var(--muted);background:var(--body-bg); }
    .return-unit-radio:checked + .return-unit-card { border-color:var(--navy);background:var(--surface); }
    .return-unit-check { display:inline-flex;width:20px;height:20px;align-items:center;justify-content:center;border-radius:5px;border:1px solid var(--border);color:transparent;transition:all .16s ease; }
    .return-unit-radio:checked + .return-unit-card .return-unit-check { border-color:var(--navy);background:var(--navy);color:#fff; }
    .return-date-input { width:100%;min-height:36px;border-radius:6px;border:1.5px solid var(--border);background:var(--surface);padding:8px 10px;color:var(--text);font-size:11px;font-weight:900;outline:none; }
    .return-date-input:focus { border-color:var(--sky-dark);box-shadow:0 0 0 3px rgba(56,189,248,.15); }
    .return-submit-btn { display:inline-flex;width:100%;min-height:36px;align-items:center;justify-content:center;gap:8px;border-radius:6px;background:var(--navy);color:#fff;font-size:9px;font-weight:900;letter-spacing:.14em;text-transform:uppercase;transition:background .16s ease;border:none;cursor:pointer; }
    .return-submit-btn:hover { background:var(--navy-mid); }
    .return-empty { border-radius:12px;padding:18px 14px;text-align:center; }
    .return-empty-icon { width:38px;height:38px;border-radius:12px; }
    .return-empty-icon i { font-size:13px !important; }
    .return-empty-title { font-size:12px;line-height:1.15;color:var(--text); }
    .return-empty-copy { margin-top:5px;font-size:8px;line-height:1.25;letter-spacing:.12em;color:var(--muted); }
    .return-unit-title { color:var(--text); }
    .return-muted-copy { color:var(--muted); }
    .return-unit-list { margin-top:0;display:grid;gap:4px; }
    .return-unit-pill { display:flex;align-items:center;justify-content:space-between;gap:10px;border-radius:5px;border:1px solid var(--border);background:var(--body-bg);padding:5px 7px;font-size:8px;font-weight:900;letter-spacing:.08em;text-transform:uppercase;color:var(--text); }
    .return-unit-pill span:last-child { color:var(--muted); }
    .return-page-card { border-radius:8px;border:1px solid var(--border);background:var(--surface);box-shadow:var(--shadow);overflow:hidden; }
    .return-assignment-head { display:grid;grid-template-columns:1fr auto;gap:12px;align-items:start;border-bottom:1px solid var(--border);padding:12px 14px; }
    .return-assignment-meta { border-top:1px solid var(--border);background:var(--surface); }
    .return-assignment-cell { display:grid;grid-template-columns:120px 1fr;gap:12px;align-items:center;border-bottom:1px solid var(--border);padding:9px 14px; }
    .return-assignment-cell:last-child { border-bottom:0; }
    .return-assignment-cell-value { min-width:0; }
    .return-review-box { border:1px solid var(--border);border-radius:7px;background:var(--body-bg);padding:12px; }
    .return-search-wrap { margin-bottom:12px; }
    .return-search-input { width:100%;min-height:36px;border-radius:7px;border:1.5px solid var(--border);background:var(--surface);padding:8px 12px;color:var(--text);font-size:11px;font-weight:800;outline:none; }
    .return-search-input:focus { border-color:var(--sky-dark);box-shadow:0 0 0 3px rgba(56,189,248,.15); }
    .return-radio-prefix { position:relative;min-width:0; }
    .return-radio-prefix::before { content:'G';position:absolute;left:12px;top:50%;transform:translateY(-50%);z-index:1;color:var(--text);font-size:11px;font-weight:900;pointer-events:none; }
    .return-radio-prefix .return-search-input,
    .return-radio-prefix .return-date-input { padding-left:28px; }
    .return-search-empty { display:none;border:1px dashed var(--border);border-radius:7px;padding:14px;text-align:center;color:var(--muted);font-size:9px;font-weight:900;letter-spacing:.14em;text-transform:uppercase; }
    .return-manual-search { display:grid;grid-template-columns:1fr auto;gap:10px; }
    .return-search-btn { min-height:36px;border:0;border-radius:7px;background:var(--navy);color:#fff;padding:0 16px;font-size:9px;font-weight:900;letter-spacing:.14em;text-transform:uppercase; }
    .return-search-results { display:none;margin-top:10px;border:1px solid var(--border);border-radius:8px;overflow:hidden;background:var(--surface); }
    .return-search-result { width:100%;display:grid;grid-template-columns:1fr auto;gap:10px;align-items:center;border:0;border-bottom:1px solid var(--border);background:transparent;padding:10px 12px;text-align:left;cursor:pointer; }
    .return-search-result:last-child { border-bottom:0; }
    .return-search-result:hover { background:var(--body-bg); }
    .return-search-result strong { display:block;color:var(--text);font-size:10px;font-weight:900;text-transform:uppercase; }
    .return-search-result span { display:block;margin-top:3px;color:var(--muted);font-size:8px;font-weight:900;letter-spacing:.1em;text-transform:uppercase; }
    .return-found-box { display:none;margin-top:14px;border:1px solid var(--border);border-radius:8px;background:var(--body-bg);padding:12px; }
    .return-found-grid { display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px; }
    .return-found-field p:first-child { margin:0 0 4px;font-size:8px;font-weight:900;letter-spacing:.14em;text-transform:uppercase;color:var(--muted); }
    .return-found-field p:last-child { margin:0;font-size:11px;font-weight:900;text-transform:uppercase;color:var(--text); }
    .return-form-alert { display:none;border:1px solid #fecaca;border-radius:7px;background:#fef2f2;padding:10px 12px;color:#b91c1c;font-size:9px;font-weight:900;letter-spacing:.12em;text-transform:uppercase; }
    .return-manual-box { margin-top:14px;border:1px dashed var(--border);border-radius:8px;background:var(--body-bg);padding:12px; }
    .return-manual-head { display:flex;align-items:center;justify-content:space-between;gap:12px; }
    .return-manual-copy { margin:4px 0 0;font-size:9px;font-weight:800;line-height:1.45;color:var(--muted); }
    .return-manual-toggle { display:inline-flex;align-items:center;gap:8px;font-size:9px;font-weight:900;letter-spacing:.12em;text-transform:uppercase;color:var(--text);cursor:pointer; }
    .return-manual-toggle input { width:16px;height:16px;accent-color:var(--navy); }
    .return-manual-fields { display:none;margin-top:12px;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px; }
    .return-manual-fields.is-open { display:grid; }
    .return-manual-fields .wide { grid-column:1 / -1; }
    .return-note-input { min-height:68px;resize:vertical; }
    .return-person-field { position:relative; }
    .return-person-combobox { position:relative; }
    .return-person-toggle { position:absolute;right:10px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:10px;pointer-events:none; }
    .return-person-suggestions { position:absolute;z-index:40;top:calc(100% + 4px);left:0;right:0;display:none;max-height:260px;overflow:hidden;border:1px solid var(--border);border-radius:0 0 14px 14px;background:var(--surface);box-shadow:0 12px 24px rgba(15,23,42,.12); }
    .return-person-list { max-height:220px;overflow-y:auto;padding:8px 0; }
    .return-person-option { width:100%;border-bottom:0;padding:11px 16px;text-align:left;background:none;border:none;cursor:pointer; }
    .return-person-option:hover, .return-person-option:focus { background:var(--body-bg);outline:none; }
    .return-person-option-name { display:block;color:var(--text);font-size:11px;font-weight:900;text-transform:uppercase; }
    .return-person-option-meta { margin-top:3px;display:block;color:var(--muted);font-size:8px;font-weight:900;letter-spacing:.12em;text-transform:uppercase; }
    .return-signature-pad { border:1px solid var(--border);border-radius:7px;background:var(--surface);overflow:hidden; }
    .return-signature-pad canvas { display:block;width:100%;height:136px;background:#fff;touch-action:none; }
    .return-signature-actions { display:flex;align-items:center;justify-content:space-between;gap:10px;border-top:1px solid var(--border);background:var(--body-bg);padding:8px 10px; }
    .return-signature-hint { font-size:8px;font-weight:900;letter-spacing:.12em;text-transform:uppercase;color:var(--muted); }
    .return-signature-clear { border:1px solid var(--border);border-radius:6px;background:var(--surface);color:var(--text);font-size:8px;font-weight:900;letter-spacing:.12em;text-transform:uppercase;padding:6px 10px; }
    @media (max-width: 640px) {
        .return-assignment-cell { grid-template-columns:1fr;gap:3px; }
        .return-manual-head { align-items:flex-start;flex-direction:column; }
        .return-manual-fields { grid-template-columns:1fr; }
    }
    @media (min-width: 992px) {
        .return-form-grid { grid-template-columns: 1fr 340px !important; }
    }
</style>
@endpush

@section('content')
@php
    $isAdminRoute = request()->routeIs('wt.admin.*');
    $mode = $mode ?? ($isAdminRoute ? 'self' : 'self');
    $returnStoreUrl = $isAdminRoute
        ? route($routePrefix . '.returns.store', ['mode' => $mode])
        : route($routePrefix . '.returns.store', []);
    $returnSearchUrl = $isAdminRoute
        ? route($routePrefix . '.returns.search', ['mode' => $mode])
        : route($routePrefix . '.returns.search', []);
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
    <div style="margin-bottom:18px">
        <div style="font-size:16px;font-weight:800;color:var(--text)">Return Unit</div>
        <p style="margin-top:4px;font-size:12px;color:var(--muted)">Submit return requests for active walkie talkie assignments.</p>
    </div>

    <div class="return-page-card">
    @if($isAdminRoute && $mode === 'staff')
    <div class="m-4 return-panel">
        <h4 class="return-section-title"><i class="fa-solid fa-user-tie"></i> Executive Details</h4>
        <div class="wt-form-row">
            <div>
                <label class="form-label">Executive Name</label>
                <input type="text" value="{{ strtoupper(auth('wt')->user()->full_name ?: auth('wt')->user()->username) }}" class="return-readonly" readonly>
            </div>
            <div>
                <label class="form-label">Executive Staff ID</label>
                <input type="text" value="{{ strtoupper(auth('wt')->user()->staff_id ?: '-') }}" class="return-readonly" readonly>
            </div>
            <div>
                <label class="form-label">Executive Department</label>
                <input type="text" value="{{ strtoupper(auth('wt')->user()->department ?: 'GENERAL') }}" class="return-readonly" readonly>
            </div>
        </div>
    </div>
    @endif

    @if($activeAssets->isEmpty())
        <form action="{{ $returnStoreUrl }}" method="POST" class="return-form-grid" style="display:grid;grid-template-columns:1fr;gap:16px;padding:16px" id="returnUnitForm" novalidate>
            @csrf
            <div class="return-panel">
                <h4 class="return-section-title"><i class="fa-solid fa-magnifying-glass"></i> Search Assignment</h4>
                <div class="return-manual-search">
                    <div class="return-radio-prefix">
                        <input type="search" id="manualReturnSearch" class="return-search-input" placeholder="0001, serial no, staff ID, or name...">
                    </div>
                    <button type="button" id="manualReturnSearchBtn" class="return-search-btn">Search</button>
                </div>
                <div id="manualReturnResults" class="return-search-results"></div>
                <div id="manualReturnEmpty" class="return-search-empty mt-3">No matching active assignment found.</div>

                <div id="manualReturnFound" class="return-found-box">
                    <div class="return-found-grid">
                        <div class="return-found-field">
                            <p>Radio ID</p>
                            <p id="manualFoundRadio">-</p>
                        </div>
                        <div class="return-found-field">
                            <p>Serial No</p>
                            <p id="manualFoundSerial">-</p>
                        </div>
                        <div class="return-found-field">
                            <p>Assigned To</p>
                            <p id="manualFoundName">-</p>
                        </div>
                        <div class="return-found-field">
                            <p>Department</p>
                            <p id="manualFoundDepartment">-</p>
                        </div>
                    </div>
                </div>

                <div class="return-manual-box">
                    <div class="return-manual-head">
                        <div>
                            <h4 class="return-section-title" style="margin-bottom:0"><i class="fa-solid fa-pen-to-square"></i> Manual Unit Entry</h4>
                            <p class="return-manual-copy">Use this only when the walkie talkie does not appear in the system search.</p>
                        </div>
                        <label class="return-manual-toggle">
                            <input type="checkbox" id="manualReturnToggle" name="manual_return" value="1">
                            Enter Manually
                        </label>
                    </div>

                    <div class="return-manual-fields" id="manualReturnFields">
                        <div>
                            <label class="form-label">Radio ID</label>
                            <div class="return-radio-prefix">
                                <input type="text" name="manual_radio_id" id="manualRadioIdInput" class="return-date-input" placeholder="0001" data-radio-id-number-only>
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Serial No</label>
                            <input type="text" name="manual_serial_number" class="return-date-input" placeholder="Serial number">
                        </div>
                        <div>
                            <label class="form-label">Model</label>
                            <input type="text" name="manual_model" class="return-date-input" placeholder="Model">
                        </div>
                        <div>
                            <label class="form-label">Staff ID</label>
                            <input type="text" name="manual_staff_id" class="return-date-input" placeholder="Staff ID">
                        </div>
                        <div>
                            <label class="form-label">Ownership Name</label>
                            <input type="text" name="manual_ownership_name" id="manualOwnershipInput" class="return-date-input" placeholder="Current holder name">
                        </div>
                        <div>
                            <label class="form-label">Department</label>
                            <input type="text" name="manual_department" id="manualDepartmentInput" class="return-date-input" placeholder="Department">
                        </div>
                        <div>
                            <label class="form-label">Position</label>
                            <input type="text" name="manual_position" class="return-date-input" placeholder="Position">
                        </div>
                        <div>
                            <label class="form-label">Location</label>
                            <input type="text" name="manual_location" class="return-date-input" placeholder="Location">
                        </div>
                        <div class="wide">
                            <label class="form-label">Remark</label>
                            <textarea name="manual_note" class="return-date-input return-note-input" placeholder="Enter remark"></textarea>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="access_request_id" id="manualAccessRequestId">
                <input type="hidden" name="selected_walkie_inventory_id" id="selectedWalkieInventoryId">
                <input type="hidden" name="selected_radio_id" id="selectedRadioId">
                <input type="hidden" name="selected_serial_number" id="selectedSerialNumber">
            </div>

            <div class="return-panel h-fit">
                <h4 class="return-section-title"><i class="fa-solid fa-calendar-check"></i> Return Details</h4>
                <div>
                    <label class="form-label">Return Date</label>
                    <input type="date" name="return_date" value="{{ date('Y-m-d') }}" class="return-date-input" required>
                </div>

                <div style="margin-top:16px;display:grid;gap:12px">
                    <div class="return-person-field">
                        <label class="form-label">Returned By</label>
                        <div class="return-person-combobox">
                            <input type="text" name="return_person" id="returnPersonInput" value="{{ old('return_person', auth('wt')->user()->full_name ?: auth('wt')->user()->username) }}" class="return-date-input" style="padding-right:32px" placeholder="Search or type returner's name" autocomplete="off" required>
                            <span class="return-person-toggle"><i class="fa-solid fa-caret-down"></i></span>
                        </div>
                        <div id="returnPersonSuggestions" class="return-person-suggestions"></div>
                    </div>
                    <div>
                        <label class="form-label">Department</label>
                        <input type="text" name="return_department" id="returnDepartmentInput" value="{{ old('return_department', auth('wt')->user()->department) }}" class="return-date-input" placeholder="Department" required>
                    </div>
                    <div>
                        <label class="form-label">Phone No</label>
                        <input type="text" name="return_phone_no" id="returnPhoneInput" value="{{ old('return_phone_no', auth('wt')->user()->phone_no) }}" class="return-date-input" placeholder="E.g. 012-3456789" required>
                    </div>
                </div>

                <div style="margin-top:16px">
                    <label class="form-label">Returner Signature</label>
                    <div class="return-signature-pad" data-return-signature-pad>
                        <canvas></canvas>
                        <div class="return-signature-actions">
                            <span class="return-signature-hint">Sign inside the box</span>
                            <button type="button" class="return-signature-clear" data-return-signature-clear>Clear</button>
                        </div>
                    </div>
                    <input type="hidden" name="return_signature" data-return-signature-input required>
                </div>

                <div class="return-review-box" style="margin-top:16px">
                    <p style="font-size:9px;font-weight:900;text-transform:uppercase;letter-spacing:.14em;color:var(--muted);margin:0">Review</p>
                    <p class="return-muted-copy" style="margin-top:4px;font-size:10px;font-weight:700;line-height:1.5;margin-bottom:0">Search one active assignment, confirm the unit details, then submit it for ICT return confirmation.</p>
                </div>
                <div class="return-form-alert mt-3" id="returnFormAlert"></div>

                <button type="submit" class="return-submit-btn mt-4">
                    Submit Return <i class="fas fa-check"></i>
                </button>
            </div>
        </form>
    @else
        <form action="{{ $returnStoreUrl }}" method="POST" class="return-form-grid" style="display:grid;grid-template-columns:1fr;gap:16px;padding:16px" id="returnUnitForm" novalidate>
            @csrf

            <div class="return-panel">
                <h4 class="return-section-title"><i class="fa-solid fa-walkie-talkie"></i> {{ $isAdminRoute && $mode === 'staff' ? 'Select Recipient Unit' : 'Select Unit' }}</h4>
                <div class="return-search-wrap">
                    <div class="return-radio-prefix">
                        <input type="search" id="returnUnitSearch" class="return-search-input" placeholder="0001, request, serial, purpose, or date...">
                    </div>
                </div>
                <div style="display:grid;gap:12px" id="returnUnitList">
                    @foreach($activeAssets as $asset)
                    @php
                        $isDirectWalkieReturn = (bool) ($asset->direct_walkie_return ?? false);
                        $assetKey = $isDirectWalkieReturn ? 'direct_walkie_' . $asset->walkie_inventory_id : $asset->id;
                        $assetLabel = $isDirectWalkieReturn ? 'Direct Inventory' : 'Request #' . str_pad($asset->id, 5, '0', STR_PAD_LEFT);
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
                        $unitInputId = 'asset_' . \Illuminate\Support\Str::slug((string) $assetKey, '_') . '_unit_' . $unitIndex;
                    @endphp
                    <div style="position:relative" data-return-unit-item data-return-search="{{ strtoupper($assetLabel . ' ' . $radioId . ' ' . ($serials->get($unitIndex) ?: '') . ' ' . $unitOwnership . ' ' . $unitOwnershipType . ' ' . $unitDepartment . ' ' . ($asset->event_name ?: 'Walkie Talkie Request') . ' ' . ($asset->request_date ? \Carbon\Carbon::parse($asset->request_date)->format('d M Y') : '')) }}">
                        <input
                            type="radio"
                            name="access_request_id"
                            id="{{ $unitInputId }}"
                            value="{{ $assetKey }}"
                            class="return-unit-radio"
                            style="position:absolute;width:1px;height:1px;opacity:0"
                            data-return-walkie-id="{{ $walkieIds->get($unitIndex) }}"
                            data-return-radio-id="{{ $radioId }}"
                            data-return-serial-number="{{ $serials->get($unitIndex) }}"
                            required
                        >
                        <label for="{{ $unitInputId }}" class="return-unit-card" data-return-unit-card>
                            <div class="return-assignment-head">
                                <div style="min-width:0">
                                    <p style="font-size:8px;font-weight:900;text-transform:uppercase;letter-spacing:.18em;color:var(--muted);margin:0">{{ $assetLabel }}</p>
                                    <p class="return-unit-title" style="margin-top:4px;font-size:13px;font-weight:900;margin-bottom:0">
                                        Radio ID {{ $radioId }} To Return
                                    </p>
                                    @if($isAdminRoute && $mode === 'staff')
                                    <p style="margin-top:4px;font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:0">Recipient: {{ strtoupper($asset->full_name ?: optional($asset->user)->username ?: '-') }}</p>
                                    @endif
                                </div>
                                <div class="return-unit-check">
                                    <i class="fa-solid fa-check" style="font-size:10px"></i>
                                </div>
                            </div>
                            <div class="return-assignment-meta">
                                <div class="return-assignment-cell">
                                    <p style="font-size:8px;font-weight:900;text-transform:uppercase;letter-spacing:.14em;color:var(--muted);margin:0">Selected Unit</p>
                                    <div class="return-assignment-cell-value return-unit-list">
                                        <div class="return-unit-pill">
                                            <span>{{ $radioId }}</span>
                                            <span>{{ $serials->get($unitIndex) ?: 'Serial -' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="return-assignment-cell">
                                    <p style="font-size:8px;font-weight:900;text-transform:uppercase;letter-spacing:.14em;color:var(--muted);margin:0">Ownership</p>
                                    <div class="return-assignment-cell-value">
                                        <p class="return-muted-copy" style="font-size:11px;font-weight:900;text-transform:uppercase;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;margin:0">{{ $unitOwnership }}</p>
                                        <p style="margin-top:2px;font-size:8px;font-weight:900;text-transform:uppercase;letter-spacing:.14em;color:var(--muted);margin-bottom:0">{{ $unitOwnershipType }}</p>
                                    </div>
                                </div>
                                <div class="return-assignment-cell">
                                    <p style="font-size:8px;font-weight:900;text-transform:uppercase;letter-spacing:.14em;color:var(--muted);margin:0">Department</p>
                                    <p class="return-assignment-cell-value return-muted-copy" style="font-size:11px;font-weight:900;text-transform:uppercase;margin:0">{{ $unitDepartment }}</p>
                                </div>
                                <div class="return-assignment-cell is-purpose">
                                    <p style="font-size:8px;font-weight:900;text-transform:uppercase;letter-spacing:.14em;color:var(--muted);margin:0">Issued For</p>
                                    <p class="return-assignment-cell-value return-muted-copy" style="font-size:11px;font-weight:700;margin:0">{{ $asset->event_name ?: 'Walkie Talkie Request' }}</p>
                                </div>
                                <div class="return-assignment-cell">
                                    <p style="font-size:8px;font-weight:900;text-transform:uppercase;letter-spacing:.14em;color:var(--muted);margin:0">Request Date</p>
                                    <p class="return-assignment-cell-value" style="font-size:11px;font-weight:900;text-transform:uppercase;color:var(--text);margin:0">{{ $asset->request_date ? \Carbon\Carbon::parse($asset->request_date)->format('d M Y') : '-' }}</p>
                                </div>
                            </div>
                        </label>
                    </div>
                    @empty
                    <div style="position:relative" data-return-unit-item data-return-search="{{ strtoupper('REQUEST ' . str_pad($asset->id, 5, '0', STR_PAD_LEFT) . ' UNIT NOT ASSIGNED ' . ($asset->event_name ?: 'Walkie Talkie Request') . ' ' . ($asset->request_date ? \Carbon\Carbon::parse($asset->request_date)->format('d M Y') : '')) }}">
                        <input type="radio" name="access_request_id" id="asset_{{ $asset->id }}_unit_empty" value="{{ $asset->id }}" class="return-unit-radio" style="position:absolute;width:1px;height:1px;opacity:0" required>
                        <label for="asset_{{ $asset->id }}_unit_empty" class="return-unit-card" data-return-unit-card>
                            <div class="return-assignment-head">
                                <div style="min-width:0">
                                    <p style="font-size:8px;font-weight:900;text-transform:uppercase;letter-spacing:.18em;color:var(--muted);margin:0">Request #{{ str_pad($asset->id, 5, '0', STR_PAD_LEFT) }}</p>
                                    <p class="return-unit-title" style="margin-top:4px;font-size:13px;font-weight:900;margin-bottom:0">Unit Not Assigned</p>
                                </div>
                                <div class="return-unit-check"><i class="fa-solid fa-check" style="font-size:10px"></i></div>
                            </div>
                        </label>
                    </div>
                    @endforelse
                    @endforeach
                </div>
                <div id="returnUnitSearchEmpty" class="return-search-empty mt-3">No matching unit found.</div>

                <div class="return-manual-box">
                    <div class="return-manual-head">
                        <div>
                            <h4 class="return-section-title" style="margin-bottom:0"><i class="fa-solid fa-pen-to-square"></i> Manual Unit Entry</h4>
                            <p class="return-manual-copy">Use this only when the walkie talkie does not appear in the system list.</p>
                        </div>
                        <label class="return-manual-toggle">
                            <input type="checkbox" id="manualReturnToggle" name="manual_return" value="1">
                            Enter Manually
                        </label>
                    </div>

                    <div class="return-manual-fields" id="manualReturnFields">
                        <div>
                            <label class="form-label">Radio ID</label>
                            <div class="return-radio-prefix">
                                <input type="text" name="manual_radio_id" id="manualRadioIdInput" class="return-date-input" placeholder="0001" data-radio-id-number-only>
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Serial No</label>
                            <input type="text" name="manual_serial_number" class="return-date-input" placeholder="Serial number">
                        </div>
                        <div>
                            <label class="form-label">Model</label>
                            <input type="text" name="manual_model" class="return-date-input" placeholder="Model">
                        </div>
                        <div>
                            <label class="form-label">Staff ID</label>
                            <input type="text" name="manual_staff_id" class="return-date-input" placeholder="Staff ID">
                        </div>
                        <div>
                            <label class="form-label">Ownership Name</label>
                            <input type="text" name="manual_ownership_name" id="manualOwnershipInput" class="return-date-input" placeholder="Current holder name">
                        </div>
                        <div>
                            <label class="form-label">Department</label>
                            <input type="text" name="manual_department" id="manualDepartmentInput" class="return-date-input" placeholder="Department">
                        </div>
                        <div>
                            <label class="form-label">Position</label>
                            <input type="text" name="manual_position" class="return-date-input" placeholder="Position">
                        </div>
                        <div>
                            <label class="form-label">Location</label>
                            <input type="text" name="manual_location" class="return-date-input" placeholder="Location">
                        </div>
                        <div class="wide">
                            <label class="form-label">Remark</label>
                            <textarea name="manual_note" class="return-date-input return-note-input" placeholder="Enter remark"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="return-panel h-fit">
                <h4 class="return-section-title"><i class="fa-solid fa-calendar-check"></i> Return Details</h4>
                <input type="hidden" name="selected_walkie_inventory_id" id="selectedWalkieInventoryId">
                <input type="hidden" name="selected_radio_id" id="selectedRadioId">
                <input type="hidden" name="selected_serial_number" id="selectedSerialNumber">
                <div>
                    <label class="form-label">Return Date</label>
                    <input type="date" name="return_date" value="{{ date('Y-m-d') }}" class="return-date-input" required>
                </div>

                <div style="margin-top:16px;display:grid;gap:12px">
                    <div class="return-person-field">
                        <label class="form-label">Returned By</label>
                        <div class="return-person-combobox">
                            <input type="text" name="return_person" id="returnPersonInput" value="{{ old('return_person', auth('wt')->user()->full_name ?: auth('wt')->user()->username) }}" class="return-date-input" style="padding-right:32px" placeholder="Search or type returner's name" autocomplete="off" required>
                            <span class="return-person-toggle"><i class="fa-solid fa-caret-down"></i></span>
                        </div>
                        <div id="returnPersonSuggestions" class="return-person-suggestions"></div>
                    </div>
                    <div>
                        <label class="form-label">Department</label>
                        <input type="text" name="return_department" id="returnDepartmentInput" value="{{ old('return_department', auth('wt')->user()->department) }}" class="return-date-input" placeholder="Department" required>
                    </div>
                    <div>
                        <label class="form-label">Phone No</label>
                        <input type="text" name="return_phone_no" id="returnPhoneInput" value="{{ old('return_phone_no', auth('wt')->user()->phone_no) }}" class="return-date-input" placeholder="E.g. 012-3456789" required>
                    </div>
                </div>

                <div style="margin-top:16px">
                    <label class="form-label">Returner Signature</label>
                    <div class="return-signature-pad" data-return-signature-pad>
                        <canvas></canvas>
                        <div class="return-signature-actions">
                            <span class="return-signature-hint">Sign inside the box</span>
                            <button type="button" class="return-signature-clear" data-return-signature-clear>Clear</button>
                        </div>
                    </div>
                    <input type="hidden" name="return_signature" data-return-signature-input required>
                </div>

                <div class="return-review-box" style="margin-top:16px">
                    <p style="font-size:9px;font-weight:900;text-transform:uppercase;letter-spacing:.14em;color:var(--muted);margin:0">Review</p>
                    <p class="return-muted-copy" style="margin-top:4px;font-size:10px;font-weight:700;line-height:1.5;margin-bottom:0">Select one active assignment and submit it for ICT return confirmation.</p>
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
        const returnSignatureInput = document.querySelector('[data-return-signature-input]');
        const selectedWalkieInventoryId = document.getElementById('selectedWalkieInventoryId');
        const selectedRadioId = document.getElementById('selectedRadioId');
        const selectedSerialNumber = document.getElementById('selectedSerialNumber');
        const manualAccessRequestId = document.getElementById('manualAccessRequestId');
        const manualReturnSearch = document.getElementById('manualReturnSearch');
        const manualReturnSearchBtn = document.getElementById('manualReturnSearchBtn');
        const manualReturnResults = document.getElementById('manualReturnResults');
        const manualReturnEmpty = document.getElementById('manualReturnEmpty');
        const manualReturnFound = document.getElementById('manualReturnFound');
        const manualFoundRadio = document.getElementById('manualFoundRadio');
        const manualFoundSerial = document.getElementById('manualFoundSerial');
        const manualFoundName = document.getElementById('manualFoundName');
        const manualFoundDepartment = document.getElementById('manualFoundDepartment');
        const manualReturnToggle = document.getElementById('manualReturnToggle');
        const manualReturnFields = document.getElementById('manualReturnFields');
        const manualRadioIdInput = document.getElementById('manualRadioIdInput');
        const manualOwnershipInput = document.getElementById('manualOwnershipInput');
        const manualDepartmentInput = document.getElementById('manualDepartmentInput');
        const manualReturnSearchUrl = @json($returnSearchUrl);
        const returnPrefillQuery = @json((string) request()->query('q', ''));

        document.querySelectorAll('[data-radio-id-number-only]').forEach((input) => {
            const stripLeadingG = () => {
                input.value = input.value.replace(/^g/i, '');
            };

            stripLeadingG();
            input.addEventListener('input', stripLeadingG);
        });

        function setupReturnSignaturePad(container) {
            const canvas = container.querySelector('canvas');
            const input = container.parentElement.querySelector('[data-return-signature-input]');
            const clearButton = container.querySelector('[data-return-signature-clear]');
            const context = canvas.getContext('2d');
            let drawing = false;
            let hasSignature = false;

            function resizeCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                const rect = canvas.getBoundingClientRect();
                const image = hasSignature ? canvas.toDataURL('image/png') : null;

                canvas.width = rect.width * ratio;
                canvas.height = rect.height * ratio;
                context.setTransform(ratio, 0, 0, ratio, 0, 0);
                context.lineWidth = 2;
                context.lineCap = 'round';
                context.lineJoin = 'round';
                context.strokeStyle = '#111827';

                if (image) {
                    const img = new Image();
                    img.onload = () => context.drawImage(img, 0, 0, rect.width, rect.height);
                    img.src = image;
                }
            }

            function point(event) {
                const rect = canvas.getBoundingClientRect();
                const source = event.touches ? event.touches[0] : event;
                return { x: source.clientX - rect.left, y: source.clientY - rect.top };
            }

            function updateInput() {
                input.value = hasSignature ? canvas.toDataURL('image/png') : '';
            }

            function start(event) {
                event.preventDefault();
                drawing = true;
                const pos = point(event);
                context.beginPath();
                context.moveTo(pos.x, pos.y);
            }

            function move(event) {
                if (!drawing) return;
                event.preventDefault();
                const pos = point(event);
                context.lineTo(pos.x, pos.y);
                context.stroke();
                hasSignature = true;
                updateInput();
            }

            function stop() {
                drawing = false;
                updateInput();
            }

            resizeCanvas();
            window.addEventListener('resize', resizeCanvas);
            canvas.addEventListener('mousedown', start);
            canvas.addEventListener('mousemove', move);
            canvas.addEventListener('mouseup', stop);
            canvas.addEventListener('mouseleave', stop);
            canvas.addEventListener('touchstart', start, { passive: false });
            canvas.addEventListener('touchmove', move, { passive: false });
            canvas.addEventListener('touchend', stop);
            clearButton.addEventListener('click', function () {
                context.clearRect(0, 0, canvas.width, canvas.height);
                hasSignature = false;
                updateInput();
            });
        }

        document.querySelectorAll('[data-return-signature-pad]').forEach(setupReturnSignaturePad);

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

        function isManualReturnEnabled() {
            return Boolean(manualReturnToggle && manualReturnToggle.checked);
        }

        if (manualReturnToggle && manualReturnFields) {
            manualReturnToggle.addEventListener('change', function () {
                manualReturnFields.classList.toggle('is-open', manualReturnToggle.checked);

                if (manualReturnToggle.checked) {
                    document.querySelectorAll('.return-unit-radio:checked').forEach((radio) => {
                        radio.checked = false;
                        syncSelectedUnit(radio);
                    });

                    if (manualAccessRequestId) {
                        manualAccessRequestId.value = '';
                    }

                    if (manualRadioIdInput) {
                        manualRadioIdInput.focus();
                    }
                }
            });
        }

        const searchInput = document.getElementById('returnUnitSearch');
        const searchEmpty = document.getElementById('returnUnitSearchEmpty');
        const unitItems = Array.from(document.querySelectorAll('[data-return-unit-item]'));

        if (searchInput) {
            function filterReturnUnits() {
                const query = searchInput.value.trim().toUpperCase();
                let visibleCount = 0;

                unitItems.forEach((item) => {
                    const isMatch = query === '' || (item.dataset.returnSearch || '').includes(query);
                    item.style.display = isMatch ? '' : 'none';
                    if (isMatch) {
                        visibleCount += 1;
                    }
                });

                if (searchEmpty) {
                    searchEmpty.style.display = visibleCount === 0 ? 'block' : 'none';
                }
            }

            searchInput.addEventListener('input', filterReturnUnits);

            if (returnPrefillQuery) {
                searchInput.value = returnPrefillQuery;
                filterReturnUnits();

                const firstVisibleRadio = unitItems
                    .filter((item) => item.style.display !== 'none')
                    .map((item) => item.querySelector('.return-unit-radio'))
                    .find(Boolean);

                if (firstVisibleRadio) {
                    firstVisibleRadio.checked = true;
                    syncSelectedUnit(firstVisibleRadio);
                }
            }
        }

        function selectManualReturnResult(result) {
            if (!result || !manualAccessRequestId) {
                return;
            }

            if (manualReturnToggle) {
                manualReturnToggle.checked = false;
            }

            if (manualReturnFields) {
                manualReturnFields.classList.remove('is-open');
            }

            manualAccessRequestId.value = result.id || '';

            if (selectedWalkieInventoryId) {
                selectedWalkieInventoryId.value = result.walkie_inventory_id || '';
            }

            if (selectedRadioId) {
                selectedRadioId.value = result.radio_id || '';
            }

            if (selectedSerialNumber) {
                selectedSerialNumber.value = result.serial_number || '';
            }

            if (manualFoundRadio) {
                manualFoundRadio.textContent = result.radio_id || '-';
            }

            if (manualFoundSerial) {
                manualFoundSerial.textContent = result.serial_number || '-';
            }

            if (manualFoundName) {
                manualFoundName.textContent = result.full_name || '-';
            }

            if (manualFoundDepartment) {
                manualFoundDepartment.textContent = result.department || '-';
            }

            if (manualReturnFound) {
                manualReturnFound.style.display = 'block';
            }

            if (manualReturnResults) {
                manualReturnResults.style.display = 'none';
                manualReturnResults.innerHTML = '';
            }

            if (manualReturnEmpty) {
                manualReturnEmpty.style.display = 'none';
            }

            if (manualReturnSearch) {
                manualReturnSearch.value = result.label || result.radio_id || '';
            }

            if (returnFormAlert) {
                returnFormAlert.style.display = 'none';
            }
        }

        async function runManualReturnSearch() {
            if (!manualReturnSearch || !manualReturnResults) {
                return;
            }

            const query = manualReturnSearch.value.trim();

            manualAccessRequestId && (manualAccessRequestId.value = '');
            if (manualReturnFound) {
                manualReturnFound.style.display = 'none';
            }

            if (query.length < 2) {
                manualReturnResults.style.display = 'none';
                if (manualReturnEmpty) {
                    manualReturnEmpty.textContent = 'Enter at least 2 characters to search.';
                    manualReturnEmpty.style.display = 'block';
                }
                return;
            }

            manualReturnSearchBtn && (manualReturnSearchBtn.disabled = true);
            manualReturnSearchBtn && (manualReturnSearchBtn.textContent = 'Searching');

            try {
                const url = new URL(manualReturnSearchUrl, window.location.origin);
                url.searchParams.set('q', query);

                const response = await fetch(url.toString(), {
                    headers: {
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) {
                    throw new Error('Search failed');
                }

                const payload = await response.json();
                const results = Array.isArray(payload.results) ? payload.results : [];

                if (results.length === 1) {
                    selectManualReturnResult(results[0]);
                    return;
                }

                if (results.length === 0) {
                    manualReturnResults.style.display = 'none';
                    if (manualReturnEmpty) {
                        manualReturnEmpty.textContent = 'No matching active assignment found.';
                        manualReturnEmpty.style.display = 'block';
                    }
                    return;
                }

                manualReturnResults.innerHTML = results.map((result, index) => `
                    <button type="button" class="return-search-result" data-manual-return-index="${index}">
                        <span>
                            <strong>${result.label || 'Return assignment'}</strong>
                            <span>${result.staff_id || '-'} / ${result.department || '-'} / ${result.request_date || '-'}</span>
                        </span>
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                `).join('');

                Array.from(manualReturnResults.querySelectorAll('[data-manual-return-index]')).forEach((button) => {
                    button.addEventListener('click', () => {
                        selectManualReturnResult(results[Number(button.dataset.manualReturnIndex)]);
                    });
                });

                manualReturnResults.style.display = 'block';
                if (manualReturnEmpty) {
                    manualReturnEmpty.style.display = 'none';
                }
            } catch (error) {
                manualReturnResults.style.display = 'none';
                if (manualReturnEmpty) {
                    manualReturnEmpty.textContent = 'Search failed. Please try again.';
                    manualReturnEmpty.style.display = 'block';
                }
            } finally {
                manualReturnSearchBtn && (manualReturnSearchBtn.disabled = false);
                manualReturnSearchBtn && (manualReturnSearchBtn.textContent = 'Search');
            }
        }

        if (manualReturnSearchBtn) {
            manualReturnSearchBtn.addEventListener('click', runManualReturnSearch);
        }

        if (manualReturnSearch) {
            if (returnPrefillQuery) {
                manualReturnSearch.value = returnPrefillQuery;
                setTimeout(runManualReturnSearch, 150);
            }

            manualReturnSearch.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    runManualReturnSearch();
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
                        `).join('') : '<div style="padding:12px 16px;font-size:9px;font-weight:900;text-transform:uppercase;letter-spacing:.12em;color:var(--muted)">No match found</div>'}
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
                const selectedManualUnit = manualAccessRequestId && manualAccessRequestId.value.trim() !== '';
                const selectedManualEntry = isManualReturnEnabled();
                const requiredFields = [
                    { field: selectedUnit || (selectedManualUnit ? manualAccessRequestId : null) || (selectedManualEntry ? manualReturnToggle : null), message: 'Please search and select one walkie talkie unit to return, or enter it manually.' },
                    { field: selectedManualEntry ? manualRadioIdInput : returnUnitForm.querySelector('[name="access_request_id"]'), message: 'Please enter the walkie talkie radio ID.' },
                    { field: selectedManualEntry ? manualOwnershipInput : returnUnitForm.querySelector('[name="access_request_id"]'), message: 'Please enter the current holder name.' },
                    { field: selectedManualEntry ? manualDepartmentInput : returnUnitForm.querySelector('[name="access_request_id"]'), message: 'Please enter the unit department.' },
                    { field: returnUnitForm.querySelector('[name="return_date"]'), message: 'Please enter return date.' },
                    { field: returnPersonInput, message: 'Please enter who returned this unit.' },
                    { field: returnDepartmentInput, message: 'Please enter returner department.' },
                    { field: returnPhoneInput, message: 'Please enter returner phone no.' },
                    { field: returnSignatureInput, message: 'Please sign before submitting the return.' },
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
