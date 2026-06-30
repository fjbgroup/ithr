@extends('wt.layouts.admin')

@section('title', 'Duplicated ID Management')

@section('content')

@include('wt.admin.partials.inventory-management-ui')

@php
    $totalRecords = $records->count();
    $pendingCount = $records->filter(fn($r) => (int)($r->id_change_done ?? 0) !== 1)->count();
    $doneCount    = $records->filter(fn($r) => (int)($r->id_change_done ?? 0) === 1)->count();
@endphp

<div class="duplicate-page" style="display:flex;flex-direction:column;gap:10px;">

    {{-- Header --}}
    <div class="duplicate-hero">
        <div class="page-header-block flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h1 class="page-title-standard">Duplicated ID Management</h1>
                <p class="page-subtitle-standard">Review units that share a Radio ID or still need an ID change.</p>
            </div>
            @if(auth('wt')->user()->wt_role === 'admin_it')
            <div class="flex flex-wrap items-center gap-2">
                <button onclick="openImportModal()" class="wt-btn wt-btn-soft">
                    <i class="fa-solid fa-file-import"></i> Import Excel
                </button>
                <a href="{{ route('wt.admin.walkies.create.duplicate') }}" class="wt-btn wt-btn-soft">
                    <i class="fa-solid fa-plus"></i> Add Item
                </a>
            </div>
            @endif
        </div>
    </div>

    {{-- Stats Bar --}}
    <div class="dup-stats-row">
        <div class="dup-stat-card">
            <span class="dup-stat-num" id="statTotal">{{ $totalRecords }}</span>
            <span class="dup-stat-lbl">Total Records</span>
        </div>
        <div class="dup-stat-card is-pending">
            <span class="dup-stat-num" id="statPending">{{ $pendingCount }}</span>
            <span class="dup-stat-lbl">Pending Change</span>
        </div>
        <div class="dup-stat-card is-done">
            <span class="dup-stat-num" id="statDone">{{ $doneCount }}</span>
            <span class="dup-stat-lbl">ID Changed</span>
        </div>
        <div class="dup-stat-card is-visible">
            <span class="dup-stat-num" id="statVisible">{{ $totalRecords }}</span>
            <span class="dup-stat-lbl">Showing</span>
        </div>
    </div>

    {{-- Filters --}}
    <div class="duplicate-search-panel" role="search" aria-label="Duplicated ID filters" style="justify-content: flex-start !important; width: auto !important;">
        <div class="duplicate-filter-field">
            <label for="duplicateSearchInput">Search</label>
            <input id="duplicateSearchInput" type="search" class="duplicate-search" placeholder="Radio ID, serial, ownership…" style="width: 250px !important; max-width: 250px !important;">
        </div>
        <div class="duplicate-filter-field">
            <label for="duplicateStatusFilter">Status</label>
            <select id="duplicateStatusFilter" class="duplicate-filter-select">
                <option value="">All Status</option>
                @foreach($statusOptions as $statusOption)
                    <option value="{{ $statusOption }}">{{ $statusOption }}</option>
                @endforeach
            </select>
        </div>
        <div class="duplicate-filter-field">
            <label for="duplicateDoneFilter">ID Change Done</label>
            <select id="duplicateDoneFilter" class="duplicate-filter-select">
                <option value="">All</option>
                <option value="YES">Done</option>
                <option value="NO">Pending</option>
            </select>
        </div>
        <button type="button" id="duplicateResetFilters" class="duplicate-filter-reset">Reset</button>
    </div>

    @if(session('success'))
    <div id="alertBox" style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;color:#15803d;font-size:12px;font-weight:600;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Table --}}
    <div id="mainTableContainer" class="duplicate-table-shell">
        <div id="duplicateTableScroll" class="duplicate-table-scroll">
            <table id="duplicateTable" class="table-auto w-full">
                <colgroup>
                    <col style="width:9%">
                    <col style="width:10%">
                    <col style="width:15%">
                    <col style="width:12%">
                    <col style="width:15%">
                    <col style="width:8%">
                    <col style="width:31%">
                </colgroup>
                <thead>
                    <tr>
                        <th class="sortable text-center" style="text-align:center !important;" data-col="0" data-type="num">Radio ID <i class="dup-sort-icon">↕</i></th>
                        <th class="text-center" style="text-align:center !important;">Status</th>
                        <th class="sortable text-center" style="text-align:center !important;" data-col="2" data-type="text">Serial No. <i class="dup-sort-icon">↕</i></th>
                        <th class="sortable text-center" style="text-align:center !important;" data-col="3" data-type="text">Model <i class="dup-sort-icon">↕</i></th>
                        <th class="sortable text-center" style="text-align:center !important;" data-col="4" data-type="num">Change ID To <i class="dup-sort-icon">&#8597;</i></th>
                        <th class="text-center" style="text-align:center !important;">Done</th>
                        <th class="text-center" style="text-align:center !important;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $r)
                    @php
                        $done = (int)($r->id_change_done ?? 0) === 1;
                        $statusClass = match(strtoupper($r->status ?? '')) {
                            'IN USE'    => 'is-in-use',
                            'REPAIRING' => 'is-repairing',
                            'UNKNOWN'   => 'is-unknown',
                            'UNUSED'    => 'is-unused',
                            'CHANGE ID' => 'is-change-id',
                            default     => 'is-other',
                        };
                    @endphp
                    <tr class="duplicate-row{{ $done ? ' is-done-row' : '' }}"
                        data-status="{{ strtoupper((string)($r->status ?? '')) }}"
                        data-done="{{ $done ? 'YES' : 'NO' }}"
                        data-search="{{ strtoupper(trim(($r->radio_id ?? '') . ' ' . ($r->serial_number ?? '') . ' ' . ($r->model ?? '') . ' ' . ($r->status ?? '') . ' ' . ($r->ownership_type ?? '') . ' ' . ($r->shared_with ?? '') . ' ' . ($r->ownership ?? '') . ' ' . ($r->department ?? '') . ' ' . ($r->location ?? '') . ' ' . ($r->remark ?? '') . ' ' . ($r->need_to_change_id ?? '') . ' ' . ($r->ownership_type_to_be ?? ''))) }}">
                        <td style="font-weight:700;">{{ $r->radio_id ?: '-' }}</td>
                        <td>
                            <span class="dup-status-badge {{ $statusClass }}">{{ $r->status ?: '-' }}</span>
                        </td>
                        <td class="text-center" style="text-align:center !important;">{{ $r->serial_number ?: '-' }}</td>
                        <td class="text-center" style="text-align:center !important;">{{ $r->model ?: '-' }}</td>
                        <td>
                            @if($r->need_to_change_id)
                                <span class="dup-change-id-val">{{ $r->need_to_change_id }}</span>
                            @else
                                <span class="dup-change-id-empty">—</span>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            <span class="dup-done-badge {{ $done ? 'is-yes' : 'is-no' }}">{{ $done ? 'YES' : 'NO' }}</span>
                        </td>
                        <td style="text-align:center;">
                            @if(auth('wt')->user()->wt_role === 'admin_it')
                            <div class="dup-actions">
                                <button type="button" class="btn btn-info" onclick="openGlobalWalkieTimeline('{{ $r->walkie_id }}')">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                <a href="{{ route('wt.admin.walkies.edit', ['walkie' => $r->walkie_id, 'source' => 'duplicate']) }}" class="btn btn-primary">
                                    <i class="fa-solid fa-edit"></i>
                                </a>
                                <form action="{{ route('wt.admin.walkies.destroy', $r->walkie_id) }}" method="POST" class="inline" data-modern-confirm="Delete duplicated ID record for {{ $r->radio_id ?? '-' }}?">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                            @else
                            <button type="button" class="btn btn-info" onclick="openGlobalWalkieTimeline('{{ $r->walkie_id }}')">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="duplicate-table-footer">
            <div class="duplicate-table-info" id="duplicateTableInfo">
                @if($totalRecords > 0)
                    Showing <span id="duplicateTotalItems">{{ $totalRecords }}</span> {{ \Illuminate\Support\Str::plural('record', $totalRecords) }}
                @else
                    No records found
                @endif
            </div>
            <div class="duplicate-table-pagination" id="dupPaginationBar"></div>
        </div>
    </div>

</div>

{{-- ===================== ADD RECORD MODAL ===================== --}}
@if(auth('wt')->user()->wt_role === 'admin_it')
<div id="addModal" class="modal-overlay" onclick="closeAddModalOutside(event)">
    <div class="modal-box">
        <div class="modal-header">
            <h2 class="modal-title">Add Duplicated ID Record</h2>
            <button onclick="closeAddModal()" class="modal-close-btn"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('wt.admin.walkies.store') }}" method="POST" class="flex flex-col h-full overflow-hidden">
            @csrf
            <input type="hidden" name="is_duplicated" value="1">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Radio ID</label>
                        <input type="text" name="radio_id" class="form-input" placeholder="e.g. 2212">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-input">
                            @foreach($statusOptions as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Serial No. <span class="required">*</span></label>
                        <input type="text" name="serial_number" class="form-input" placeholder="e.g. 977TPA0829" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Model (MC)</label>
                        <select name="model" class="form-input">
                            <option value="">-- Leave Empty --</option>
                            @foreach($walkieModels as $m)
                            <option value="{{ $m }}">{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Current Ownership Type</label>
                        <select name="ownership_type" class="form-input ownership-type-control">
                            @foreach($ownershipTypeOptions as $ot)
                            <option value="{{ $ot }}">{{ $ot }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group shared-with-group hidden">
                        <label class="form-label">Shared With <span class="required">*</span></label>
                        <input type="text" name="shared_with" class="form-input shared-with-input" placeholder="E.G. USER / TEAM / DEPARTMENT">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Current Ownership</label>
                        <input type="text" name="ownership" class="form-input" placeholder="Current ownership">
                    </div>
                    <div class="form-group" style="grid-column:span 3;">
                        <label class="form-label">Department</label>
                        <input type="text" name="department" list="department-options" class="form-input" placeholder="Department">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Location</label>
                        <select name="location" class="form-input">
                            <option value="">-- None --</option>
                            @foreach($locationOptions as $location)
                            <option value="{{ $location }}">{{ $location }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Need To Change Into</label>
                        <input type="text" name="need_to_change_id" class="form-input" placeholder="Target Radio ID e.g. 2220">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Done</label>
                        <select name="id_change_done" class="form-input">
                            <option value="0">NO</option>
                            <option value="1">YES</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ownership Type To Be</label>
                        <select name="ownership_type_to_be" class="form-input">
                            <option value="">-- None --</option>
                            @foreach($ownershipTypeOptions as $tot)
                            <option value="{{ $tot }}">{{ $tot }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="grid-column:span 3;">
                        <label class="form-label">Remarks</label>
                        <textarea name="remark" class="form-input" style="height:60px;resize:none;" placeholder="Remarks"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeAddModal()" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-submit">Save Record</button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- ===================== EDIT MODAL ===================== --}}
<div id="editModal" class="modal-overlay" onclick="closeEditModalOutside(event)">
    <div class="modal-box" id="editModalBox">
        <div class="modal-header">
            <h2 class="modal-title">Update Unit Details</h2>
            <button onclick="closeEditModal()" class="modal-close-btn"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" id="editWalkieForm" class="flex flex-col h-full overflow-hidden">
            @csrf
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Radio ID <span class="required">*</span></label>
                        <input type="text" name="radio_id" id="edit_radio_id" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Serial No. <span class="required">*</span></label>
                        <input type="text" name="serial_number" id="edit_serial_number" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Model <span class="required">*</span></label>
                        <select name="model" id="edit_model" class="form-input" required>
                            @foreach($walkieModels as $editModel)
                            <option value="{{ $editModel }}">{{ $editModel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status <span class="required">*</span></label>
                        <select name="status" id="edit_status" class="form-input" required>
                            @foreach($statusOptions as $editStatus)
                            <option value="{{ $editStatus }}">{{ $editStatus }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ownership Type <span class="required">*</span></label>
                        <select name="ownership_type" id="edit_ownership_type" class="form-input ownership-type-control" required>
                            @foreach($ownershipTypeOptions as $editOwnershipType)
                            <option value="{{ $editOwnershipType }}">{{ $editOwnershipType }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group shared-with-group hidden">
                        <label class="form-label">Shared With <span class="required">*</span></label>
                        <input type="text" name="shared_with" id="edit_shared_with" class="form-input shared-with-input" placeholder="E.G. USER / TEAM / DEPARTMENT">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ownership</label>
                        <input type="text" name="ownership" id="edit_ownership" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Position</label>
                        <input type="text" name="position" id="edit_position" list="position-options" class="form-input">
                    </div>
                    <div class="form-group" style="grid-column:span 2;">
                        <label class="form-label">Department</label>
                        <input type="text" name="department" id="edit_department" list="department-options" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Location</label>
                        <select name="location" id="edit_location" class="form-input">
                            <option value="">-- None --</option>
                            @foreach($locationOptions as $location)
                            <option value="{{ $location }}">{{ $location }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Temp / Swapped Radio ID</label>
                        <input type="text" name="temporary_radio_id" id="edit_temporary_radio_id" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tracking REF</label>
                        <input type="text" name="tracking_ref" id="edit_tracking_ref" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Need To Change ID</label>
                        <input type="text" name="need_to_change_id" id="edit_need_to_change_id" class="form-input" placeholder="Target Radio ID">
                    </div>
                    <div class="form-group">
                        <label class="form-label">ID Change Done</label>
                        <select name="id_change_done" id="edit_id_change_done" class="form-input">
                            <option value="0">NO</option>
                            <option value="1">YES</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ownership Type To Be</label>
                        <select name="ownership_type_to_be" id="edit_ownership_type_to_be" class="form-input">
                            <option value="">Select target...</option>
                            @foreach($ownershipTypeOptions as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="grid-column:span 3;">
                        <label class="form-label">Remarks</label>
                        <textarea name="remark" id="edit_remark" class="form-input" style="height:60px;resize:none;"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeEditModal()" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-submit">Save Changes</button>
            </div>
        </form>
    </div>
</div>

{{-- ===================== IMPORT MODAL ===================== --}}
@if(auth('wt')->user()->wt_role === 'admin_it')
<div id="importModal" class="modal-overlay" onclick="closeImportModalOutside(event)">
    <div class="modal-box" style="max-width:480px;">
        <div class="modal-header">
            <h2 class="modal-title">Bulk Import Duplicated IDs</h2>
            <button onclick="closeImportModal()" class="modal-close-btn"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('wt.admin.walkies.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body" style="padding:24px;">
                <div style="border:2px dashed var(--border);border-radius:12px;padding:32px;text-align:center;background:var(--body-bg);">
                    <input type="file" name="file" id="import_file" class="hidden" required onchange="updateFileName(this)">
                    <label for="import_file" style="display:block;cursor:pointer;">
                        <div style="width:48px;height:48px;border-radius:50%;background:var(--surface);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                            <i class="fas fa-cloud-upload-alt" style="font-size:18px;color:var(--accent);"></i>
                        </div>
                        <p id="fileNameDisplay" style="font-size:12px;font-weight:600;color:var(--muted);">Click to upload Excel or CSV</p>
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeImportModal()" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-submit">Start Import</button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
$(document).ready(function () {
    const searchInput  = document.getElementById('duplicateSearchInput');
    const statusFilter = document.getElementById('duplicateStatusFilter');
    const doneFilter   = document.getElementById('duplicateDoneFilter');
    const resetBtn     = document.getElementById('duplicateResetFilters');
    const allRows      = Array.from(document.querySelectorAll('#duplicateTable tbody .duplicate-row'));
    const infoEl       = document.getElementById('duplicateTableInfo');
    const totalEl      = document.getElementById('duplicateTotalItems');
    const paginationEl = document.getElementById('dupPaginationBar');
    const statVisible  = document.getElementById('statVisible');
    const statPending  = document.getElementById('statPending');
    const statDone     = document.getElementById('statDone');
    const PER_PAGE     = 15;
    let currentPage    = 1;
    let filtered       = [];
    let sortCol        = 0;
    let sortDir        = 'asc';

    /* ── Sorting ── */
    function getSortVal(row, col) {
        const text = (row.cells[col]?.textContent || '').trim();
        if (col === 0 || col === 7) {
            const n = parseFloat(text.replace(/[^0-9.\-]/g, ''));
            if (!isNaN(n)) return n;
        }
        return text.toLowerCase();
    }

    function sortRows(rows) {
        return [...rows].sort((a, b) => {
            const av = getSortVal(a, sortCol);
            const bv = getSortVal(b, sortCol);
            if (typeof av === 'number' && typeof bv === 'number')
                return sortDir === 'asc' ? av - bv : bv - av;
            return sortDir === 'asc'
                ? String(av).localeCompare(String(bv))
                : String(bv).localeCompare(String(av));
        });
    }

    function assignGroups(rows) {
        let gIdx = 0, lastId = null;
        rows.forEach(r => {
            const rid = (r.cells[0]?.textContent || '').trim();
            if (rid !== lastId) { gIdx++; lastId = rid; }
            r.setAttribute('data-group', gIdx % 2);
        });
    }

    /* ── Sort header indicators ── */
    document.querySelectorAll('#duplicateTable thead th.sortable').forEach(th => {
        th.addEventListener('click', () => {
            const col = parseInt(th.dataset.col);
            if (sortCol === col) {
                sortDir = sortDir === 'asc' ? 'desc' : 'asc';
            } else {
                sortCol = col;
                sortDir = 'asc';
            }
            updateSortHeaders();
            currentPage = 1;
            applyFilter();
        });
    });

    function updateSortHeaders() {
        document.querySelectorAll('#duplicateTable thead th.sortable').forEach(th => {
            const col = parseInt(th.dataset.col);
            const icon = th.querySelector('.dup-sort-icon');
            th.classList.remove('sort-asc', 'sort-desc');
            if (icon) icon.textContent = '↕';
            if (col === sortCol) {
                th.classList.add(sortDir === 'asc' ? 'sort-asc' : 'sort-desc');
                if (icon) icon.textContent = sortDir === 'asc' ? '↑' : '↓';
            }
        });
    }

    /* ── Render ── */
    function render() {
        const sorted = sortRows(filtered);
        assignGroups(sorted);

        allRows.forEach(r => r.style.display = 'none');
        const start = (currentPage - 1) * PER_PAGE;
        sorted.slice(start, start + PER_PAGE).forEach(r => r.style.display = '');

        const pending = filtered.filter(r => r.dataset.done === 'NO').length;
        const done    = filtered.filter(r => r.dataset.done === 'YES').length;

        if (infoEl) {
            infoEl.textContent = filtered.length
                ? `Showing ${filtered.length} ${filtered.length === 1 ? 'record' : 'records'}`
                : 'No records found';
        } else if (totalEl) {
            totalEl.textContent = filtered.length;
        }
        if (statVisible) statVisible.textContent = filtered.length;
        if (statPending) statPending.textContent  = pending;
        if (statDone)    statDone.textContent     = done;

        renderPagination(sorted.length);
    }

    function renderPagination(total) {
        if (!paginationEl) return;
        const pages = Math.ceil(total / PER_PAGE);
        if (pages <= 1) { paginationEl.innerHTML = ''; return; }
        let html = `<button class="duplicate-page-link is-nav" ${currentPage===1?'disabled':''} onclick="dupPage(${currentPage-1})">‹ Prev</button>`;
        for (let i = 1; i <= pages; i++) {
            if (i===1 || i===pages || Math.abs(i-currentPage)<=1)
                html += `<button class="duplicate-page-link${i===currentPage?' is-active':''}" onclick="dupPage(${i})">${i}</button>`;
            else if (Math.abs(i-currentPage)===2)
                html += `<span style="padding:0 2px;color:#64748b;font-size:11px">…</span>`;
        }
        html += `<button class="duplicate-page-link is-nav" ${currentPage===pages?'disabled':''} onclick="dupPage(${currentPage+1})">Next ›</button>`;
        paginationEl.innerHTML = html;
    }

    window.dupPage = function (p) { currentPage = p; render(); };

    function normalizeFilterValue(value) {
        return String(value || '').trim().replace(/\s+/g, ' ').toUpperCase();
    }

    function applyFilter() {
        const s  = normalizeFilterValue(searchInput?.value);
        const st = normalizeFilterValue(statusFilter?.value);
        const dn = normalizeFilterValue(doneFilter?.value);
        filtered = allRows.filter(r =>
            (!s  || normalizeFilterValue(r.dataset.search).includes(s)) &&
            (!st || normalizeFilterValue(r.dataset.status) === st) &&
            (!dn || normalizeFilterValue(r.dataset.done) === dn)
        );
        currentPage = 1;
        render();
    }

    if (searchInput)  searchInput.addEventListener('input', applyFilter);
    if (statusFilter) statusFilter.addEventListener('change', applyFilter);
    if (doneFilter)   doneFilter.addEventListener('change', applyFilter);
    if (resetBtn) resetBtn.addEventListener('click', () => {
        if (searchInput)  searchInput.value = '';
        if (statusFilter) statusFilter.value = '';
        if (doneFilter)   doneFilter.value = '';
        applyFilter();
    });

    /* Default sort: Radio ID ascending */
    updateSortHeaders();
    applyFilter();
});

function openEditModal(id,radio,serialNumber,model,status,ownershipType,ownership,position,department,location,temporaryRadioId,trackingRef,remark,needToChangeId,idChangeDone,ownershipTypeToBe){
    const form=document.getElementById('editWalkieForm');
    form.action="{{ route('wt.admin.walkies.updateMeta',['walkie'=>'__ID__']) }}".replace('__ID__',id);
    document.getElementById('edit_radio_id').value=radio||'';
    document.getElementById('edit_serial_number').value=serialNumber||'';
    document.getElementById('edit_model').value=model||'';
    document.getElementById('edit_status').value=status||'';
    document.getElementById('edit_ownership_type').value=ownershipType||'';
    document.getElementById('edit_ownership').value=ownership||'';
    document.getElementById('edit_position').value=position||'';
    document.getElementById('edit_department').value=department||'';
    document.getElementById('edit_location').value=location||'';
    document.getElementById('edit_temporary_radio_id').value=temporaryRadioId||'';
    document.getElementById('edit_tracking_ref').value=trackingRef||'';
    document.getElementById('edit_remark').value=remark||'';
    document.getElementById('edit_need_to_change_id').value=needToChangeId||'';
    document.getElementById('edit_id_change_done').value=idChangeDone||'0';
    document.getElementById('edit_ownership_type_to_be').value=ownershipTypeToBe||'';
    document.getElementById('editModal').classList.add('active');
    document.body.style.overflow='hidden';
}
function closeEditModal(){ document.getElementById('editModal').classList.remove('active'); document.body.style.overflow=''; }
function closeEditModalOutside(e){ if(e.target===document.getElementById('editModal')) closeEditModal(); }

function syncSharedWith(select){
    const form=select.closest('form'); if(!form) return;
    const isShared=(select.value||'').toUpperCase()==='SHARED';
    const g=form.querySelector('.shared-with-group'), i=form.querySelector('.shared-with-input');
    if(g) g.classList.toggle('hidden',!isShared);
    if(i){ i.required=isShared; if(!isShared) i.value=''; }
}
document.querySelectorAll('.ownership-type-control').forEach(s=>{ s.addEventListener('change',()=>syncSharedWith(s)); syncSharedWith(s); });

function openImportModal(){ document.getElementById('importModal').classList.add('active'); document.body.style.overflow='hidden'; }
function closeImportModal(){ document.getElementById('importModal').classList.remove('active'); document.body.style.overflow=''; }
function closeImportModalOutside(e){ if(e.target===document.getElementById('importModal')) closeImportModal(); }
function openAddModal(){ document.getElementById('addModal').classList.add('active'); document.body.style.overflow='hidden'; }
function closeAddModal(){ document.getElementById('addModal').classList.remove('active'); document.body.style.overflow=''; }
function closeAddModalOutside(e){ if(e.target===document.getElementById('addModal')) closeAddModal(); }
function updateFileName(input){
    const el=document.getElementById('fileNameDisplay');
    el.textContent=input.files?.length ? 'Selected: '+input.files[0].name : 'Click to upload Excel or CSV';
    el.style.color=input.files?.length ? '#16a34a' : '';
}
document.addEventListener('keydown', e => {
    if(e.key==='Escape'){ closeEditModal(); closeAddModal(); closeImportModal(); }
});
</script>
@include('wt.admin.partials.inventory-tools-table-skin')

@endsection
