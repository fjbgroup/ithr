@extends('wt.layouts.admin')

@section('title', 'Special Use Management')

@section('content')

@php
    $totalRecords = $records->count();
    $inUseRecords = $records->filter(fn ($record) => strtoupper((string) $record->status) === 'IN USE')->count();
    $unusedRecords = $records->filter(fn ($record) => strtoupper((string) $record->status) === 'UNUSED')->count();
    $repairRecords = $records->filter(fn ($record) => in_array(strtoupper((string) $record->status), ['REPAIRING', 'REPAIR / FAULTY', 'FAULTY', 'B.E.R', 'UNDER REPAIR'], true))->count();
@endphp

<div class="special-page-shell inventory-management-ui">
<div class="page-header-block flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h3 class="page-title-standard">Special Use Walkie Talkies</h3>
        <p class="page-subtitle-standard">Records marked for special use, temporary, or spares.</p>
    </div>
    @if(auth('wt')->user()->wt_role === 'admin_it')
    <div class="flex flex-shrink-0 items-center gap-2">
        <button onclick="openImportModal()" class="wt-btn wt-btn-soft">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="margin-right:5px;">
                <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
            </svg>
            Import Excel
        </button>

        <a href="{{ route('wt.admin.walkies.create.specialUse') }}" class="wt-btn wt-btn-soft">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="margin-right:5px;">
                <path d="M8 1a.5.5 0 0 1 .5.5V7.5H14a.5.5 0 0 1 0 1H8.5V14a.5.5 0 0 1-1 0V8.5H2a.5.5 0 0 1 0-1h5.5V1.5A.5.5 0 0 1 8 1z"/>
            </svg>
            Add Item
        </a>
    </div>
    @endif
</div>

<div class="special-summary-grid">
    <div class="special-summary-card">
        <div class="special-summary-label">Total Records</div>
        <div class="special-summary-value">{{ $totalRecords }}</div>
    </div>
    <div class="special-summary-card">
        <div class="special-summary-label">In Use</div>
        <div class="special-summary-value in-use">{{ $inUseRecords }}</div>
    </div>
    <div class="special-summary-card">
        <div class="special-summary-label">Unused</div>
        <div class="special-summary-value unused">{{ $unusedRecords }}</div>
    </div>
    <div class="special-summary-card">
        <div class="special-summary-label">Repair / Faulty</div>
        <div class="special-summary-value repair">{{ $repairRecords }}</div>
    </div>
</div>
</div>

{{-- Success / Error Alert --}}
@if(session('success'))
<div class="alert-success mb-6" id="alertBox">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="mr-2 flex-shrink-0">
        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
    </svg>
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert-error mb-6" id="errorBox">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="mr-2 flex-shrink-0">
        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
    </svg>
    {{ session('error') }}
</div>
@endif

<x-wt::dark-datatable
    table-id="specialTable"
    search-id="specialSearchInput"
    status-id="specialStatusFilter"
    reset-id="specialResetFilters"
    :items-per-page="10"
    min-width="0"
    :columns="[
        'RADIO ID',
        'STATUS',
        'SERIAL NO.',
        'MODEL',
        'ASSIGNED TO',
        'RETURNED',
        'Action',
    ]"
    :status-options="$records->pluck('status')->filter()->unique()->sort()->values()->all()"
    row-selector="tbody .special-row"
>
    @foreach($records as $record)
        <tr class="special-row"
            data-status="{{ strtoupper((string) ($record->status ?: '')) }}"
            data-search="{{ strtoupper(trim(($record->radio_id ?? '') . ' ' . ($record->status ?? '') . ' ' . ($record->serial_number ?? '') . ' ' . ($record->model ?? '') . ' ' . ($record->location ?? '') . ' ' . ($record->ownership_type ?? '') . ' ' . ($record->ownership ?? '') . ' ' . ($record->department ?? '') . ' ' . ($record->remark ?? '') . ' ' . ((int) ($record->special_use_returned ?? 0) === 1 ? 'YES RETURNED' : 'NO NOT RETURNED'))) }}">
            <td>{{ $record->radio_id ?: '-' }}</td>
            <td><span class="inline-flex rounded border border-slate-600 bg-slate-800 px-2 py-1 text-[10px] font-black uppercase">{{ $record->status ?: '-' }}</span></td>
            <td class="text-center" style="text-align:center !important;">{{ $record->serial_number ?: '-' }}</td>
            <td class="text-center" style="text-align:center !important;">{{ $record->model ?: '-' }}</td>
            <td>
                <div class="font-black text-slate-900 dark:text-slate-100">{{ $record->ownership ?: '-' }}</div>
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ $record->department ?: '-' }} / {{ $record->ownership_type ?: '-' }}</div>
            </td>
            <td>{{ (int) ($record->special_use_returned ?? 0) === 1 ? 'YES' : 'NO' }}</td>
            <td>
                @if(auth('wt')->user()->wt_role === 'admin_it')
                    <div class="special-action-buttons">
                        <button type="button" class="btn btn-info btn-sm" title="View Details" onclick="openGlobalWalkieTimeline('{{ $record->walkie_id }}')">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                        <a href="{{ route('wt.admin.walkies.edit', ['walkie' => $record->walkie_id, 'source' => 'special_use']) }}" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-edit"></i>
                        </a>
                        @if((int) ($record->special_use_returned ?? 0) === 0)
                        <form action="{{ route('wt.admin.walkies.update.returned', $record->walkie_id) }}" method="POST" class="inline" data-modern-confirm="Mark {{ $record->radio_id ?: 'this unit' }} as handed over / returned?">
                            @csrf
                            <input type="hidden" name="special_use_returned" value="1">
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fa-solid fa-handshake"></i>
                            </button>
                        </form>
                        @else
                        <button type="button" class="btn btn-secondary btn-sm" disabled title="This special use unit has already been handed over">
                            <i class="fa-solid fa-handshake"></i>
                        </button>
                        @endif
                        <form action="{{ route('wt.admin.walkies.destroy', $record->walkie_id) }}" method="POST" class="inline" data-modern-confirm="Delete this record?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>
                @else
                    <button type="button" class="btn btn-info btn-sm" title="View Details" onclick="openGlobalWalkieTimeline('{{ $record->walkie_id }}')">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                @endif
            </td>
        </tr>
    @endforeach
</x-wt::dark-datatable>

@foreach($records as $record)
<div id="specialViewModal-{{ $record->walkie_id }}" class="modal-overlay" onclick="closeSpecialViewModalOutside(event, 'specialViewModal-{{ $record->walkie_id }}')" aria-hidden="true">
    <div class="modal-box max-w-3xl">
        <div class="modal-header">
            <div>
                <h2 class="modal-title">Special Use Unit</h2>
                <p class="modal-subtitle">Radio ID {{ $record->radio_id ?: '-' }}</p>
            </div>
            <button type="button" onclick="closeSpecialViewModal('specialViewModal-{{ $record->walkie_id }}')" class="modal-close-btn" title="Close">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="modal-body p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach([
                    'Status' => $record->status ?: '-',
                    'Serial No.' => $record->serial_number ?: '-',
                    'Model' => $record->model ?: '-',
                    'Location' => $record->location ?: '-',
                    'Ownership Type' => $record->ownership_type ?: '-',
                    'Current Ownership' => $record->ownership ?: '-',
                    'Department' => $record->department ?: '-',
                    'Received Date' => $record->received_date ?? '-',
                    'Repair Date' => $record->repair_date ?? '-',
                    'Returned' => (int) ($record->special_use_returned ?? 0) === 1 ? 'YES' : 'NO',
                    'Remarks' => $record->remark ?: '-',
                ] as $label => $value)
                    <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-900/60">
                        <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">{{ $label }}</p>
                        <p class="mt-1 text-sm font-bold text-slate-900 dark:text-slate-100">{{ $value }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endforeach

@include('wt.admin.partials.inventory-management-ui')

{{-- ===================== ADD RECORD MODAL ===================== --}}
<div id="addModal" class="modal-overlay" onclick="closeAddModalOutside(event)">
    <div class="modal-box">
        <div class="modal-header">
            <div>
                <h2 class="modal-title">Add Special Use Unit</h2>
            </div>
            <button onclick="closeAddModal()" class="modal-close-btn" title="Close">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('wt.admin.walkies.store') }}" class="flex flex-col h-full overflow-hidden">
            @csrf
            <input type="hidden" name="is_special_use" value="1">
            <input type="hidden" name="special_use_returned" value="0">
            
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Radio ID <span class="required">*</span></label>
                        <input type="text" name="radio_id" class="form-input" placeholder="Radio ID" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Serial No. <span class="required">*</span></label>
                        <input type="text" name="serial_number" class="form-input" placeholder="Serial number" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Model <span class="required">*</span></label>
                        <select name="model" class="form-input" required>
                            @foreach($walkieModels as $m)
                            <option value="{{ $m }}">{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status <span class="required">*</span></label>
                        <select name="status" class="form-input" required>
                            @foreach($statusOptions as $st)
                            <option value="{{ $st }}" {{ $st === 'UNUSED' ? 'selected' : '' }}>{{ $st }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ownership Type <span class="required">*</span></label>
                        <select name="ownership_type" class="form-input ownership-type-control" required>
                            @foreach($ownershipTypeOptions as $ot)
                            <option value="{{ $ot }}" {{ $ot === 'SPARE' ? 'selected' : '' }}>{{ $ot }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group shared-with-group hidden">
                        <label class="form-label">Shared With <span class="required">*</span></label>
                        <input type="text" name="shared_with" class="form-input shared-with-input" placeholder="E.G. USER / TEAM / DEPARTMENT">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ownership</label>
                        <input type="text" name="ownership" class="form-input" placeholder="Ownership name">
                    </div>
                    <div class="form-group">
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
                    <div class="form-group col-span-2">
                        <label class="form-label">Remark</label>
                        <textarea name="remark" class="form-input" placeholder="Remarks / Purpose"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeAddModal()" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-submit">Add Unit</button>
            </div>
        </form>
    </div>
</div>

{{-- ===================== QUICK UPDATE MODAL ===================== --}}
<div id="editModal" class="modal-overlay" onclick="closeEditModalOutside(event)">
    <div class="modal-box" id="editModalBox">
        <div class="modal-header">
            <div>
                <h2 class="modal-title">Update Unit Details</h2>
                <p class="modal-subtitle" id="editModalSubtitle">Modify status and ownership details for special use units.</p>
            </div>
            <button onclick="closeEditModal()" class="modal-close-btn" title="Close">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                </svg>
            </button>
        </div>

        <form method="POST" id="editWalkieForm" class="flex flex-col h-full overflow-hidden">
            @csrf
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Radio ID <span class="required">*</span></label>
                        <input type="text" name="radio_id" id="edit_radio_id" class="form-input" placeholder="Radio ID" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Serial No. <span class="required">*</span></label>
                        <input type="text" name="serial_number" id="edit_serial_number" class="form-input" placeholder="Serial number" required>
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
                        <input type="text" name="ownership" id="edit_ownership" class="form-input" placeholder="Owner name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Position</label>
                        <input type="text" name="position" id="edit_position" list="position-options" class="form-input" placeholder="Position">
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label">Department</label>
                        <input type="text" name="department" id="edit_department" list="department-options" class="form-input" placeholder="Department">
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
                        <label class="form-label">Temporary / Swapped WT Radio ID</label>
                        <input type="text" name="temporary_radio_id" id="edit_temporary_radio_id" class="form-input" placeholder="Temporary / swapped radio ID">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tracking REF</label>
                        <input type="text" name="tracking_ref" id="edit_tracking_ref" class="form-input" placeholder="Tracking reference">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Need To Change ID</label>
                        <select name="need_to_change_id" id="edit_need_to_change_id" class="form-input">
                            <option value="0">NO</option>
                            <option value="1">YES</option>
                        </select>
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
                            <option value="">Select target ownership type...</option>
                            @foreach($ownershipTypeOptions as $targetOwnershipType)
                            <option value="{{ $targetOwnershipType }}">{{ $targetOwnershipType }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Special Use</label>
                        <select name="is_special_use" id="edit_is_special_use" class="form-input">
                            <option value="0">NO</option>
                            <option value="1">YES</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Returned</label>
                        <select name="special_use_returned" id="edit_special_use_returned" class="form-input">
                            <option value="0">NO</option>
                            <option value="1">YES</option>
                        </select>
                    </div>
                    <div class="form-group" style="grid-column: span 3;">
                        <label class="form-label">Remarks</label>
                        <textarea name="remark" id="edit_remark" class="form-input" style="height:35px; resize:none;" placeholder="Remarks"></textarea>
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

{{-- ===================== IMPORT EXCEL MODAL ===================== --}}
@if(auth('wt')->user()->wt_role === 'admin_it')
<div id="importModal" class="modal-overlay" onclick="closeImportModalOutside(event)">
    <div class="modal-box" style="max-width: 500px;">
        <div class="modal-header">
            <div>
                <h2 class="modal-title">Bulk Import Special Use</h2>
                <p class="modal-subtitle">Upload your Excel or CSV file.</p>
            </div>
            <button onclick="closeImportModal()" class="modal-close-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                </svg>
            </button>
        </div>
        <form action="{{ route('wt.admin.walkies.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="import_context" value="special_use">
            <div class="modal-body p-6">
                <div class="bg-stone-50 border-2 border-dashed border-stone-200 rounded-2xl p-8 text-center">
                    <input type="file" name="file" id="import_file" class="hidden" required onchange="updateFileName(this)">
                    <label for="import_file" class="cursor-pointer">
                        <div class="w-12 h-12 bg-white rounded-full shadow-sm flex items-center justify-center mx-auto mb-4 border border-stone-100">
                             <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#142b47" viewBox="0 0 16 16">
                                <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                                <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
                            </svg>
                        </div>
                        <p class="text-xs font-bold text-stone-700" id="fileNameDisplay">Click to upload Excel or CSV</p>
                        <p class="text-[10px] text-stone-400 mt-1 uppercase font-black tracking-widest">Required Headings: radio_id, serial_no, is_special_use:1...</p>
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
function openSpecialViewModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.classList.add('active');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
}

function closeSpecialViewModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.classList.remove('active');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
}

function closeSpecialViewModalOutside(event, id) {
    if (event.target === document.getElementById(id)) {
        closeSpecialViewModal(id);
    }
}

function openImportModal() {
    document.getElementById('importModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeImportModal() {
    document.getElementById('importModal').classList.remove('active');
    document.body.style.overflow = '';
}
function closeImportModalOutside(event) {
    if (event.target === document.getElementById('importModal')) {
        closeImportModal();
    }
}
function updateFileName(input) {
    const display = document.getElementById('fileNameDisplay');
    if (input.files && input.files[0]) {
        display.innerText = "Selected: " + input.files[0].name;
    }
}

function openEditModal(id, radio, serialNumber, model, status, ownershipType, ownership, position, department, location, temporaryRadioId, trackingRef, remark, needToChangeId, idChangeDone, ownershipTypeToBe, isSpecialUse, specialUseReturned) {
    const form = document.getElementById('editWalkieForm');
    form.action = "{{ route('wt.admin.walkies.updateMeta', ['walkie' => '__ID__']) }}".replace('__ID__', id);
    document.getElementById('editModalSubtitle').innerText = `Updating unit ${radio}`;
    document.getElementById('edit_radio_id').value = radio || '';
    document.getElementById('edit_serial_number').value = serialNumber || '';
    document.getElementById('edit_model').value = model || 'R7';
    document.getElementById('edit_status').value = status || 'UNUSED';
    document.getElementById('edit_ownership_type').value = ownershipType || 'UNALLOCATED';
    document.getElementById('edit_ownership').value = ownership || '';
    document.getElementById('edit_position').value = position || '';
    document.getElementById('edit_department').value = department || '';
    document.getElementById('edit_location').value = location || '';
    document.getElementById('edit_temporary_radio_id').value = temporaryRadioId || '';
    document.getElementById('edit_tracking_ref').value = trackingRef || '';
    document.getElementById('edit_remark').value = remark || '';
    document.getElementById('edit_need_to_change_id').value = needToChangeId || '0';
    document.getElementById('edit_id_change_done').value = idChangeDone || '0';
    document.getElementById('edit_ownership_type_to_be').value = ownershipTypeToBe || '';
    document.getElementById('edit_is_special_use').value = isSpecialUse || '0';
    document.getElementById('edit_special_use_returned').value = specialUseReturned || '0';
    document.getElementById('editModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function openAddModal() {
    document.getElementById('addModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeAddModal() {
    document.getElementById('addModal').classList.remove('active');
    document.body.style.overflow = '';
}

function closeAddModalOutside(event) {
    if (event.target === document.getElementById('addModal')) {
        closeAddModal();
    }
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
    document.body.style.overflow = '';
}

function closeEditModalOutside(event) {
    if (event.target === document.getElementById('editModal')) {
        closeEditModal();
    }
}

function syncSharedWith(select) {
    const form = select.closest('form');
    if (!form) return;

    const isShared = (select.value || '').toUpperCase() === 'SHARED';
    const sharedGroup = form.querySelector('.shared-with-group');
    const sharedInput = form.querySelector('.shared-with-input');

    if (sharedGroup) sharedGroup.classList.toggle('hidden', !isShared);
    if (sharedInput) {
        sharedInput.required = isShared;
        if (!isShared) sharedInput.value = '';
    }
}

document.querySelectorAll('.ownership-type-control').forEach((select) => {
    select.addEventListener('change', () => syncSharedWith(select));
    syncSharedWith(select);
});

// ESC key to close
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeEditModal();
        closeAddModal();
        closeImportModal();
    }
});

// Auto-dismiss success alert
@if(session('success'))
    setTimeout(function() {
        const box = document.getElementById('alertBox');
        if (box) {
            box.style.transition = 'opacity 0.4s';
            box.style.opacity = '0';
            setTimeout(() => box.remove(), 400);
        }
    }, 4000);
@endif
</script>

@include('wt.admin.partials.inventory-tools-table-skin')

@endsection
