@extends('layouts.app')

@section('title', 'Family Information')

@section('content')
<div class="page-header">
    <div>
        <h2>Family Member Information</h2>
        <p class="page-subtitle">
            {{ $isAdmin ? 'Manage staff family member records' : 'View and edit your family records' }}
        </p>
    </div>
    <div style="display:flex;gap:.5rem;align-items:center;">
        @if($isAdmin)
        <button class="btn btn-outline" onclick="openModal('familyImportModal')">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Import
        </button>
        @endif
        <button class="btn btn-primary" onclick="resetFamilyModal(); openModal('addFamilyModal')">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Add Record
        </button>
    </div>
</div>

@if($isAdmin)
<form id="family-search-form" method="GET" action="{{ route('family.index') }}" class="search-bar">
    <div class="search-input-wrap">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="search-icon"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        <input type="text" name="search" placeholder="Search by staff name, staff no, family member, or department..." value="{{ $search }}" class="{{ $search ? 'has-clear' : '' }}">
        @if($search)
        <a href="{{ route('family.index') }}" class="search-clear-btn" title="Clear search">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </a>
        @endif
    </div>
</form>
@endif

<div id="family-results">
@if($records->isEmpty())
<div class="card">
    <div class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
        <p>No family records found.</p>
    </div>
</div>
@else
<div style="display:flex;align-items:center;gap:.75rem;padding:.55rem 1rem;margin-bottom:.75rem;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;">
    <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;user-select:none;margin:0;">
        <input type="checkbox" id="fc-global-all" onchange="fcToggleAll(this)" style="width:16px;height:16px;cursor:pointer;accent-color:#6366f1;">
        <span style="font-size:.82rem;color:#64748b;font-weight:500;">Select All</span>
    </label>
    <span id="fc-global-count" style="font-size:.78rem;color:#94a3b8;margin-left:.25rem;"></span>
</div>
@foreach($grouped as $staff_id => $members)
@php $first = $members[0]; @endphp
<details class="card fc-staff-card" style="margin-bottom: 1rem; border: none; background: transparent;" {{ count($grouped) == 1 ? 'open' : '' }}>
    <summary class="card-header" style="cursor: pointer; list-style: none; display: flex; align-items: center; justify-content: space-between; background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <div style="width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; transition: transform 0.2s;" class="staff-toggle-arrow">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>
            @if($isAdmin)
            <div class="fc-staff-meta">
                <span class="fc-staff-name">{{ $first->staff?->name ?? '—' }}</span>
                @if($first->staff?->staff_no)
                <a href="{{ route('staff.show', $first->staff_id) }}" style="text-decoration:none;" onclick="event.stopPropagation()">
                    <code style="font-size:.8rem;color:#6366f1;border-bottom:1px dashed #6366f1;">{{ $first->staff->staff_no }}</code>
                </a>
                @endif
                @if($first->staff?->department)
                <span class="dept-badge">{{ $first->staff->department->name }}</span>
                @endif
            </div>
            @else
            <span class="fc-staff-name">Your Family Members</span>
            @endif
        </div>
        <span class="fc-member-count">{{ count($members) }} member{{ count($members) !== 1 ? 's' : '' }}</span>
    </summary>

    <div class="fc-list" style="padding: 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-top: none; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;">
        @if(count($members) > 1)
        <label style="display:inline-flex;align-items:center;gap:.4rem;padding:.3rem .5rem;margin-bottom:.6rem;cursor:pointer;user-select:none;font-size:.78rem;color:#64748b;">
            <input type="checkbox" class="fc-section-all" data-staff="{{ $staff_id }}" onchange="fcToggleSection(this,'{{ $staff_id }}')" onclick="event.stopPropagation()" style="width:14px;height:14px;cursor:pointer;accent-color:#6366f1;">
            Select all in this group
        </label>
        @endif
        <style>
        .fc-staff-card[open] .staff-toggle-arrow { transform: rotate(180deg); }
        details.spouse-group[open] summary .spouse-arrow { transform: rotate(0deg); }
        details.spouse-group:not([open]) summary .spouse-arrow { transform: rotate(-90deg); }
        details.spouse-group summary::-webkit-details-marker { display: none; }
        details.spouse-group summary { outline: none; }
        </style>
        @php
        $spouses = $members->filter(fn($m) => strtolower($m->relationship) === 'spouse');
        $children = $members->filter(fn($m) => strtolower($m->relationship) === 'child');
        $others = $members->filter(fn($m) => !in_array(strtolower($m->relationship), ['spouse', 'child']));
        @endphp

        @if($spouses->isNotEmpty())
            @php $childrenRendered = false; @endphp
            @foreach($spouses as $spouse)
            <details class="spouse-group" open style="margin-bottom: 0.5rem;">
                <summary style="cursor: pointer; list-style: none; position: relative; outline: none; padding-left: 1.5rem;">
                    <div style="position: absolute; left: 0.5rem; top: 1.5rem; font-size: 0.8rem; color: #64748b; transition: transform 0.2s;" class="spouse-arrow">▼</div>
                    @include('family.card', ['r' => $spouse, 'isIndented' => false])
                </summary>
                @if(!$childrenRendered && $children->isNotEmpty())
                <div class="children-list" style="margin-top: 0; padding-left: 0;">
                    @foreach($children as $child)
                        @include('family.card', ['r' => $child, 'isIndented' => true])
                    @endforeach
                </div>
                @php $childrenRendered = true; @endphp
                @endif
            </details>
            @endforeach
        @else
            @foreach($children as $child)
                @include('family.card', ['r' => $child, 'isIndented' => false])
            @endforeach
        @endif

        @foreach($others as $other)
            @include('family.card', ['r' => $other, 'isIndented' => false])
        @endforeach
    </div>
</details>
@endforeach
@endif
</div>

<!-- Add/Edit Modal -->
<div class="modal" id="addFamilyModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 id="familyModalTitle">Add Family Record</h3>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <form id="familyForm" method="POST" action="{{ route('family.store') }}">
            @csrf
            <div id="methodField"></div>
            <div class="form-grid">
                @if($isAdmin)
                <div class="form-group form-full">
                    <label>Staff Member *</label>
                    <select name="staff_id" id="ff_staff_id" required>
                        <option value="">— Select Staff —</option>
                        @foreach($all_staff as $st)
                        <option value="{{ $st->id }}">
                            [{{ $st->staff_no }}] {{ $st->name }}
                            {{ $st->department ? '— ' . $st->department->name : '' }}
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="form-group">
                    <label>Family Member Name *</label>
                    <input type="text" name="name" id="ff_name" required>
                </div>
                <div class="form-group">
                    <label>Relationship *</label>
                    <select name="relationship" id="ff_rel" required>
                        <option value="">Select</option>
                        @foreach($relationships as $r)
                        <option value="{{ $r }}">{{ $r }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" name="date_of_birth" id="ff_dob">
                </div>
                <div class="form-group">
                    <label>Emergency Contact</label>
                    <select name="emergency_contact" id="ff_ec">
                        <option value="No">No</option>
                        <option value="Yes">Yes</option>
                    </select>
                </div>
                <div class="form-group form-full">
                    <label>Phone Number</label>
                    <input type="text" name="phone_number" id="ff_phone" placeholder="e.g. 012-3456789">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Record</button>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Delete Modal -->
<div class="modal" id="bulkDeleteFamilyModal">
    <div class="modal-box modal-sm">
        <div class="modal-header">
            <h3>Confirm Delete</h3>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <p id="fcBulkDeleteMsg" style="padding:1rem 0;">Are you sure you want to delete these records?</p>
        <form id="fcBulkDeleteForm" method="POST" action="{{ route('family.bulk-destroy') }}">
            @csrf
            <div id="fcBulkDeleteIds"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-danger">Delete</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete modal -->
<div class="modal" id="deleteFamilyModal">
    <div class="modal-box modal-sm">
        <div class="modal-header">
            <h3>Confirm Delete</h3>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <p style="padding:1rem 0;">Are you sure you want to delete this family record?</p>
        <form id="deleteFamilyForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-danger">Delete</button>
            </div>
        </form>
    </div>
</div>


<!-- Family Import Modal -->
<div class="modal" id="familyImportModal" onclick="if(event.target===this)closeModal()">
    <div class="modal-box" style="max-width:520px;">
        <div class="modal-header">
            <h3 class="modal-title">Import Dependents from Excel / CSV</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <!-- Step 1: file selection -->
            <div id="familyImportStep1">
                <p style="font-size:.85rem;color:#64748b;margin-bottom:.75rem;">Select your file — the system will read your column headers so you can map them to the correct fields.</p>
                <a href="{{ route('family.import-template') }}" class="btn btn-outline btn-sm" style="margin-bottom:1.25rem;display:inline-flex;align-items:center;gap:.4rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Download Template
                </a>
                <div class="form-group">
                    <label class="form-label">Select File <span style="color:#64748b;font-weight:400;">(.xlsx, .xls, .csv)</span></label>
                    <input type="file" id="familyImportFile" class="form-input" accept=".xlsx,.xls,.csv">
                </div>
                <div class="form-group" style="margin-top:.75rem;">
                    <label class="form-label" style="font-size:.82rem;">Header row <span style="font-weight:400;color:#94a3b8;">(row number containing column names)</span></label>
                    <div style="display:flex;align-items:center;gap:.5rem;">
                        <input type="number" id="familyHeaderRow" class="form-input" value="1" min="1" max="100" style="width:80px;">
                        <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('familyHeaderRow').value=5" style="white-space:nowrap;">Set to 5 (GOLD)</button>
                    </div>
                    <p style="font-size:.75rem;color:#94a3b8;margin-top:.3rem;">For GOLD-format files (e.g. Dependents export), the header row is row 5.</p>
                </div>
                <div style="display:flex;justify-content:flex-end;gap:.5rem;margin-top:1rem;">
                    <button type="button" class="btn btn-outline" onclick="closeModal()">Cancel</button>
                    <button type="button" id="familyImportNextBtn" class="btn btn-primary" onclick="familyImportNext()">Next: Map Columns &rarr;</button>
                </div>
            </div>
            <!-- Step 2: column mapping -->
            <div id="familyImportStep2" style="display:none;">
                <form method="POST" action="{{ route('family.import') }}" enctype="multipart/form-data" id="familyImportForm">
                    @csrf
                    <input type="file" name="file" id="familyImportFileHidden" style="display:none;" tabindex="-1">
                    <input type="hidden" name="sheet_index" id="familySheetIndexInput" value="0">
                    <input type="hidden" name="header_row" id="familyHeaderRowInput" value="1">
                    <div id="familySheetSelector"></div>
                    <div id="familyMappingTable"></div>
                    <div style="display:flex;justify-content:flex-end;gap:.5rem;margin-top:1rem;">
                        <button type="button" class="btn btn-outline" onclick="familyImportBack()">&larr; Back</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Floating bulk-action bar -->
<div id="fc-bulk-bar" style="display:none;position:fixed;bottom:1.5rem;left:50%;transform:translateX(-50%);background:#1e293b;color:#fff;padding:.65rem 1.1rem;border-radius:10px;align-items:center;gap:1rem;box-shadow:0 8px 30px rgba(0,0,0,.3);z-index:9999;white-space:nowrap;">
    <span id="fc-bulk-count" style="font-size:.88rem;font-weight:600;"></span>
    <button type="button" class="btn btn-danger btn-sm" onclick="fcConfirmBulkDelete()">Delete Selected</button>
    <button type="button" onclick="fcClearAll()" style="background:none;border:none;color:#94a3b8;cursor:pointer;font-size:.82rem;padding:0;">Clear</button>
</div>

@endsection

@section('styles')
<style>
.fc-staff-card { margin-bottom: 1rem; }
.fc-staff-card:last-child { margin-bottom: 0; }
.fc-staff-meta { display: flex; align-items: center; gap: .6rem; flex-wrap: wrap; }
.fc-staff-name { font-weight: 600; font-size: .95rem; color: var(--text); }
.fc-member-count {
    font-size: .75rem; font-weight: 600;
    background: var(--bg); color: var(--muted);
    padding: .2rem .6rem; border-radius: 20px;
}

.fc-list { display: flex; flex-direction: column; gap: 0; }
.fc-card {
    display: flex; align-items: flex-start; gap: 1rem;
    padding: .9rem 1.25rem;
    border-bottom: 1px solid var(--border);
    transition: background .12s;
}
.fc-card:last-child { border-bottom: none; }
.fc-card:hover { background: #fafbff; }

.fc-avatar {
    width: 38px; height: 38px; border-radius: 50%; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: .9rem;
}

.fc-body { flex: 1; min-width: 0; }

.fc-top {
    display: flex; align-items: center; gap: .5rem;
    margin-bottom: .45rem; flex-wrap: wrap;
}
.fc-name { font-size: .92rem; font-weight: 600; color: var(--text); }

.fc-fields { display: flex; flex-direction: column; gap: .22rem; }
.fc-field {
    display: flex; align-items: baseline; gap: .5rem;
    font-size: .8rem;
}
.fc-lbl { color: var(--muted); font-size: .74rem; min-width: 110px; flex-shrink: 0; }
.fc-val { color: var(--text); }
.fc-val.muted { color: var(--muted); }

.fc-actions {
    display: flex; flex-direction: column; gap: .35rem;
    flex-shrink: 0;
}

@media (max-width: 768px) {
    .fc-staff-name { font-size: .9rem; }
    .fc-member-count { font-size: .68rem; padding: .15rem .45rem; }
}

@media (max-width: 600px) {
    .fc-card { flex-wrap: wrap; gap: .6rem; padding: .75rem; }
    .fc-actions { flex-direction: row; width: 100%; justify-content: flex-end; padding-top: .4rem; border-top: 1px dashed var(--border); }
    .fc-lbl { min-width: 80px; font-size: .72rem; }
    .fc-val { font-size: .78rem; }
    .fc-avatar { width: 34px; height: 34px; font-size: .8rem; }
}

@media (max-width: 480px) {
    .page-header { flex-direction: column; align-items: flex-start; gap: .6rem; }
    .page-header .btn { width: 100%; justify-content: center; }
    .fc-staff-card summary { padding: .75rem .875rem !important; }
}

@media (max-width: 360px) {
    .fc-staff-meta { flex-direction: column; align-items: flex-start; gap: .2rem; }
}
</style>
@endsection

@section('scripts')
<script>
(function() {
    var f = document.getElementById('family-search-form');
    if (f) liveSearch(f, 'family-results');
})();

function editFamily(data) {
    document.getElementById('familyModalTitle').textContent = 'Edit Family Record';
    const form = document.getElementById('familyForm');
    form.action = "{{ url('family') }}/" + data.id;
    document.getElementById('methodField').innerHTML = '@method("PUT")';
    
    const staffSel = document.getElementById('ff_staff_id');
    if (staffSel) staffSel.value = data.staff_id || '';
    
    document.getElementById('ff_name').value = data.display_name || data.name || data.family_member_name || data.employee_name || '';
    document.getElementById('ff_rel').value = data.relationship;
    document.getElementById('ff_dob').value = data.date_of_birth || '';
    document.getElementById('ff_ec').value = data.emergency_contact;
    document.getElementById('ff_phone').value = data.phone_number || '';
    openModal('addFamilyModal');
}

function resetFamilyModal() {
    document.getElementById('familyModalTitle').textContent = 'Add Family Record';
    const form = document.getElementById('familyForm');
    form.action = "{{ route('family.store') }}";
    document.getElementById('methodField').innerHTML = '';
    form.reset();
}

function confirmFamilyDelete(id) {
    const form = document.getElementById('deleteFamilyForm');
    form.action = "{{ url('family') }}/" + id;
    openModal('deleteFamilyModal');
}

// --- Bulk select ---
const fcSelected = new Set();
const fcTotal = {{ $records->count() }};

function fcUpdateBar() {
    const bar = document.getElementById('fc-bulk-bar');
    const countEl = document.getElementById('fc-bulk-count');
    const globalCount = document.getElementById('fc-global-count');
    if (fcSelected.size > 0) {
        countEl.textContent = fcSelected.size + ' selected';
        bar.style.display = 'flex';
    } else {
        bar.style.display = 'none';
    }
    if (globalCount) globalCount.textContent = fcSelected.size > 0 ? fcSelected.size + ' of ' + fcTotal + ' selected' : '';
    // Sync section checkboxes
    document.querySelectorAll('.fc-section-all').forEach(cb => {
        const sid = cb.dataset.staff;
        const boxes = document.querySelectorAll('.fc-checkbox[data-staff="' + sid + '"]');
        const checked = Array.from(boxes).filter(b => fcSelected.has(b.value)).length;
        cb.indeterminate = checked > 0 && checked < boxes.length;
        cb.checked = boxes.length > 0 && checked === boxes.length;
    });
    // Sync global checkbox
    const ga = document.getElementById('fc-global-all');
    if (ga) {
        ga.indeterminate = fcSelected.size > 0 && fcSelected.size < fcTotal;
        ga.checked = fcTotal > 0 && fcSelected.size === fcTotal;
    }
}

function fcOnCheck(cb) {
    if (cb.checked) fcSelected.add(cb.value); else fcSelected.delete(cb.value);
    fcUpdateBar();
}

function fcToggleSection(sectionCb, staffId) {
    document.querySelectorAll('.fc-checkbox[data-staff="' + staffId + '"]').forEach(cb => {
        cb.checked = sectionCb.checked;
        if (sectionCb.checked) fcSelected.add(cb.value); else fcSelected.delete(cb.value);
    });
    fcUpdateBar();
}

function fcToggleAll(globalCb) {
    document.querySelectorAll('.fc-checkbox').forEach(cb => {
        cb.checked = globalCb.checked;
        if (globalCb.checked) fcSelected.add(cb.value); else fcSelected.delete(cb.value);
    });
    fcUpdateBar();
}

function fcClearAll() {
    fcSelected.clear();
    document.querySelectorAll('.fc-checkbox').forEach(cb => cb.checked = false);
    document.querySelectorAll('.fc-section-all').forEach(cb => { cb.checked = false; cb.indeterminate = false; });
    const ga = document.getElementById('fc-global-all');
    if (ga) { ga.checked = false; ga.indeterminate = false; }
    fcUpdateBar();
}

// ===== Family Import Wizard =====
const FAMILY_IMPORT_FIELDS = [
    { key: 'staff_no',                  label: 'Employee ID (Staff No)',        required: true,  aliases: ['employee_id', 'staff_no'] },
    { key: 'name',                      label: 'Dependent Name',                required: true,  aliases: ['name', 'legal_name_first_name'] },
    { key: 'relationship',              label: 'Relationship',                  required: true,  aliases: ['relationship', 'related_person_relationship'] },
    { key: 'date_of_birth',             label: 'Date of Birth',                 required: false, aliases: ['date_of_birth'] },
    { key: 'effective_date',            label: 'Effective Date',                required: false, aliases: ['effective_date'] },
    { key: 'nric_no',                   label: 'NRIC No',                       required: false, aliases: ['nric_no', 'dependent_nric_no'] },
    { key: 'dependent_id',              label: 'Dependent ID',                  required: false, aliases: ['dependent_id'] },
    { key: 'gender',                    label: 'Gender',                        required: false, aliases: ['gender'] },
    { key: 'emergency_contact',         label: 'Emergency Contact',             required: false, aliases: ['emergency_contact'] },
    { key: 'city_of_birth',             label: 'City of Birth',                 required: false, aliases: ['city_of_birth'] },
    { key: 'country_of_birth',          label: 'Country of Birth',              required: false, aliases: ['country_of_birth'] },
    { key: 'nationality',               label: 'Nationality',                   required: false, aliases: ['nationality', 'primary_nationality_dependent'] },
    { key: 'citizenship_status',        label: 'Citizenship Status',            required: false, aliases: ['citizenship_status', 'citizenship_status_locale_sensitive'] },
    { key: 'region_of_birth',           label: 'Region of Birth',               required: false, aliases: ['region_of_birth'] },
    { key: 'use_employee_address',      label: 'Use Employee Address',          required: false, aliases: ['use_employee_address'] },
    { key: 'use_employee_phone',        label: 'Use Employee Phone',            required: false, aliases: ['use_employee_phone'] },
    { key: 'phone_country_code',        label: 'Phone Country Code',            required: false, aliases: ['phone_country_code', 'cf_ss_country_phone'] },
    { key: 'phone_number',              label: 'Phone Number',                  required: false, aliases: ['phone_number'] },
    { key: 'phone_device_type',         label: 'Phone Device Type',             required: false, aliases: ['phone_device_type', 'cf_lrv_phone_device_type'] },
    { key: 'is_fulltime_student',       label: 'Full-time Student',             required: false, aliases: ['is_fulltime_student', 'full_time_student'] },
    { key: 'student_start_date',        label: 'Student Start Date',            required: false, aliases: ['student_start_date', 'student_status_start_date'] },
    { key: 'student_end_date',          label: 'Student End Date',              required: false, aliases: ['student_end_date', 'student_status_end_date'] },
    { key: 'occupation',                label: 'Occupation',                    required: false, aliases: ['occupation', 'dependent_occupation'] },
    { key: 'occupation_effective_date', label: 'Occupation Effective Date',     required: false, aliases: ['occupation_effective_date'] },
    { key: 'is_disabled',               label: 'Disabled',                      required: false, aliases: ['is_disabled', 'disabled'] },
    { key: 'is_terminated',             label: 'Terminated',                    required: false, aliases: ['is_terminated', 'terminated'] },
    { key: 'company_code',              label: 'Company Code',                  required: false, aliases: ['company_code'] },
    { key: 'company_name',              label: 'Company Name',                  required: false, aliases: ['company_name'] },
    { key: 'region_name',               label: 'Region Name',                   required: false, aliases: ['region_name'] },
];

function familyImportNext() {
    const fileInput = document.getElementById('familyImportFile');
    if (!fileInput.files.length) { alert('Please select a file first.'); return; }
    const headerRow = document.getElementById('familyHeaderRow').value || 1;
    const btn = document.getElementById('familyImportNextBtn');
    btn.disabled = true; btn.textContent = 'Detecting…';
    const fd = new FormData();
    fd.append('file', fileInput.files[0]);
    fd.append('header_row', headerRow);
    fd.append('sheet_index', 0);
    fd.append('_token', '{{ csrf_token() }}');
    fetch('{{ route("family.import-preview") }}', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            document.getElementById('familySheetIndexInput').value = 0;
            document.getElementById('familyHeaderRowInput').value = headerRow;
            _importRenderSheetSelector('familySheetSelector', data.sheets || [], 'familyRedetect');
            _importRenderMapping('familyMappingTable', FAMILY_IMPORT_FIELDS, data.headers || []);
            document.getElementById('familyImportStep1').style.display = 'none';
            document.getElementById('familyImportStep2').style.display = '';
        })
        .catch(() => alert('Could not read file headers. Please check your file format.'))
        .finally(() => { btn.disabled = false; btn.textContent = 'Next: Map Columns →'; });
}

function familyRedetect() {
    const fileInput = document.getElementById('familyImportFile');
    const headerRow = document.getElementById('familyHeaderRow').value || 1;
    const sheetSel = document.getElementById('familySheetSelectorSelect');
    const sheetIndex = sheetSel ? sheetSel.value : 0;
    const fd = new FormData();
    fd.append('file', fileInput.files[0]);
    fd.append('header_row', headerRow);
    fd.append('sheet_index', sheetIndex);
    fd.append('_token', '{{ csrf_token() }}');
    fetch('{{ route("family.import-preview") }}', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            document.getElementById('familySheetIndexInput').value = sheetIndex;
            document.getElementById('familyHeaderRowInput').value = headerRow;
            _importRenderMapping('familyMappingTable', FAMILY_IMPORT_FIELDS, data.headers || []);
        })
        .catch(() => alert('Could not re-detect columns. Please check your settings.'));
}

function familyImportBack() {
    document.getElementById('familyImportStep1').style.display = '';
    document.getElementById('familyImportStep2').style.display = 'none';
}

document.getElementById('familyImportForm').addEventListener('submit', function() {
    const file = document.getElementById('familyImportFile').files[0];
    if (file) {
        try { const dt = new DataTransfer(); dt.items.add(file); document.getElementById('familyImportFileHidden').files = dt.files; } catch(e) {}
    }
});

function fcConfirmBulkDelete() {
    if (fcSelected.size === 0) return;
    document.getElementById('fcBulkDeleteMsg').textContent =
        'Are you sure you want to delete ' + fcSelected.size + ' family record(s)? This cannot be undone.';
    const container = document.getElementById('fcBulkDeleteIds');
    container.innerHTML = '';
    fcSelected.forEach(id => {
        const inp = document.createElement('input');
        inp.type = 'hidden'; inp.name = 'ids[]'; inp.value = id;
        container.appendChild(inp);
    });
    openModal('bulkDeleteFamilyModal');
}
</script>
@endsection
