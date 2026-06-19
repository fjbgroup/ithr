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
    .return-search-empty { display:none;border:1px dashed var(--border);border-radius:7px;padding:14px;text-align:center;color:var(--muted);font-size:9px;font-weight:900;letter-spacing:.14em;text-transform:uppercase; }
    .return-form-alert { display:none;border:1px solid #fecaca;border-radius:7px;background:#fef2f2;padding:10px 12px;color:#b91c1c;font-size:9px;font-weight:900;letter-spacing:.12em;text-transform:uppercase; }
    .return-person-field { position:relative; }
    .return-person-combobox { position:relative; }
    .return-person-toggle { position:absolute;right:10px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:10px;pointer-events:none; }
    .return-person-suggestions { position:absolute;z-index:40;top:calc(100% + 4px);left:0;right:0;display:none;max-height:260px;overflow:hidden;border:1px solid var(--border);border-radius:0 0 14px 14px;background:var(--surface);box-shadow:0 12px 24px rgba(15,23,42,.12); }
    .return-person-list { max-height:220px;overflow-y:auto;padding:8px 0; }
    .return-person-option { width:100%;border-bottom:0;padding:11px 16px;text-align:left;background:none;border:none;cursor:pointer; }
    .return-person-option:hover, .return-person-option:focus { background:var(--body-bg);outline:none; }
    .return-person-option-name { display:block;color:var(--text);font-size:11px;font-weight:900;text-transform:uppercase; }
    .return-person-option-meta { margin-top:3px;display:block;color:var(--muted);font-size:8px;font-weight:900;letter-spacing:.12em;text-transform:uppercase; }
    @media (max-width: 640px) {
        .return-assignment-cell { grid-template-columns:1fr;gap:3px; }
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
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Executive Name</label>
                <input type="text" value="{{ strtoupper(auth('wt')->user()->full_name ?: auth('wt')->user()->username) }}" class="return-readonly" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label">Executive Staff ID</label>
                <input type="text" value="{{ strtoupper(auth('wt')->user()->staff_id ?: '-') }}" class="return-readonly" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label">Executive Department</label>
                <input type="text" value="{{ strtoupper(auth('wt')->user()->department ?: 'GENERAL') }}" class="return-readonly" readonly>
            </div>
        </div>
    </div>
    @endif

    @if($activeAssets->isEmpty())
        <div class="m-4 return-empty">
            <div class="return-empty-icon" style="margin:0 auto 8px;display:flex;align-items:center;justify-content:center;border:1px solid rgba(2,132,199,.2);background:rgba(2,132,199,.1);color:#38bdf8">
                <i class="fa-solid fa-box-open" style="font-size:16px"></i>
            </div>
            <h4 class="return-empty-title" style="font-weight:900">No Active Units</h4>
            <p class="return-empty-copy" style="font-weight:700;text-transform:uppercase">There are no active walkie talkie assignments available for return.</p>
        </div>
    @else
        <form action="{{ $isAdminRoute ? route($routePrefix . '.returns.store', ['mode' => $mode]) : route($routePrefix . '.returns.store') }}" method="POST" class="return-form-grid" style="display:grid;grid-template-columns:1fr;gap:16px;padding:16px" id="returnUnitForm" novalidate>
            @csrf

            <div class="return-panel">
                <h4 class="return-section-title"><i class="fa-solid fa-walkie-talkie"></i> {{ $isAdminRoute && $mode === 'staff' ? 'Select Recipient Unit' : 'Select Unit' }}</h4>
                <div class="return-search-wrap">
                    <input type="search" id="returnUnitSearch" class="return-search-input" placeholder="Search request, radio ID, serial, purpose, or date...">
                </div>
                <div style="display:grid;gap:12px" id="returnUnitList">
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
                    <div style="position:relative" data-return-unit-item data-return-search="{{ strtoupper('REQUEST ' . str_pad($asset->id, 5, '0', STR_PAD_LEFT) . ' ' . $radioId . ' ' . ($serials->get($unitIndex) ?: '') . ' ' . $unitOwnership . ' ' . $unitOwnershipType . ' ' . $unitDepartment . ' ' . ($asset->event_name ?: 'Walkie Talkie Request') . ' ' . ($asset->request_date ? \Carbon\Carbon::parse($asset->request_date)->format('d M Y') : '')) }}">
                        <input
                            type="radio"
                            name="access_request_id"
                            id="asset_{{ $asset->id }}_unit_{{ $unitIndex }}"
                            value="{{ $asset->id }}"
                            class="return-unit-radio"
                            style="position:absolute;width:1px;height:1px;opacity:0"
                            data-return-walkie-id="{{ $walkieIds->get($unitIndex) }}"
                            data-return-radio-id="{{ $radioId }}"
                            data-return-serial-number="{{ $serials->get($unitIndex) }}"
                            required
                        >
                        <label for="asset_{{ $asset->id }}_unit_{{ $unitIndex }}" class="return-unit-card" data-return-unit-card>
                            <div class="return-assignment-head">
                                <div style="min-width:0">
                                    <p style="font-size:8px;font-weight:900;text-transform:uppercase;letter-spacing:.18em;color:var(--muted);margin:0">Request #{{ str_pad($asset->id, 5, '0', STR_PAD_LEFT) }}</p>
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
                            <input type="text" name="return_person" id="returnPersonInput" value="{{ old('return_person') }}" class="return-date-input" style="padding-right:32px" placeholder="Search or type returner's name" autocomplete="off" required>
                            <span class="return-person-toggle"><i class="fa-solid fa-caret-down"></i></span>
                        </div>
                        <div id="returnPersonSuggestions" class="return-person-suggestions"></div>
                    </div>
                    <div>
                        <label class="form-label">Department</label>
                        <input type="text" name="return_department" id="returnDepartmentInput" value="{{ old('return_department') }}" class="return-date-input" placeholder="Department" required>
                    </div>
                    <div>
                        <label class="form-label">Phone No</label>
                        <input type="text" name="return_phone_no" id="returnPhoneInput" value="{{ old('return_phone_no') }}" class="return-date-input" placeholder="E.g. 012-3456789" required>
                    </div>
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
                    item.style.display = isMatch ? '' : 'none';
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

