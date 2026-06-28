@extends('wt.layouts.admin')

@section('title', 'Under Repair / Faulty Units')

@push('styles')

@endpush

@section('content')

@include('wt.admin.partials.inventory-management-ui')

<div class="maintenance-page-shell">
<div class="page-header-block flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h3 class="page-title-standard">Under Repair / Faulty</h3>
        <p class="page-subtitle-standard">
            Manage unit maintenance, repair logs, and faulty equipment.
        </p>
    </div>
    <div class="flex flex-shrink-0 items-center gap-2">
        <button onclick="openImportModal()" class="wt-btn wt-btn-soft">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="margin-right:5px;">
                <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
            </svg>
            Import Excel
        </button>

        <a href="{{ route('wt.admin.maintenance.create') }}" class="wt-btn wt-btn-soft">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 1a.5.5 0 0 1 .5.5V7.5H14a.5.5 0 0 1 0 1H8.5V14a.5.5 0 0 1-1 0V8.5H2a.5.5 0 0 1 0-1h5.5V1.5A.5.5 0 0 1 8 1z"/>
            </svg>
            Add Item
        </a>
    </div>
</div>

{{-- Alerts --}}
@if(session('success'))
<div class="alert-success mb-6" id="alertBox">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="mr-2 flex-shrink-0">
        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
    </svg>
    {{ session('success') }}
</div>
@endif

@php 
    $anyErrors = ($errors instanceof \Illuminate\Support\ViewErrorBag) && $errors->any();
@endphp
@if($anyErrors)
<div class="alert-error mb-6" id="errorBox">
    <ul class="list-disc list-inside">
        @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="clean-admin-filter" style="justify-content: flex-start !important; width: auto !important;">
    <div class="clean-admin-filter-grid" style="justify-content: flex-start !important; width: auto !important;">
        <div>
            <label class="clean-admin-label" for="maintSearch">Search</label>
            <input type="text" id="maintSearch" class="clean-admin-input" placeholder="Keywords" style="width: 250px !important; max-width: 250px !important;">
        </div>
        <div>
            <label class="clean-admin-label" for="maintStatus">Status</label>
            <select id="maintStatus" class="clean-admin-select">
                <option value="">All Status</option>
                <option value="FAULTY">Faulty</option>
                <option value="UNDER REPAIR">Under Repair</option>
                <option value="REPAIRING">Repairing</option>
                <option value="B.E.R">B.E.R</option>
                <option value="READY TO COLLECT">Ready To Collect</option>
                <option value="ALREADY FIXED">Already Fixed</option>
                <option value="DONE">Done</option>
            </select>
        </div>
        <button type="button" id="maintReset" class="clean-admin-reset">Reset</button>
    </div>
</div>
<div class="clean-admin-table-shell" id="mainTableContainer">
    <div class="clean-admin-table-scroll">
    <table id="maintTable" class="clean-admin-table table-auto w-full text-left">
        <thead>
            <tr>
                <th class="px-2 py-1 text-center">Radio ID</th>
                <th class="px-2 py-1 text-center">Status</th>
                <th class="px-2 py-1 text-center">Serial No.</th>
                <th class="px-2 py-1 text-center">Model</th>
                <th class="px-2 py-1 text-center">Issue / Received</th>
                <th class="px-2 py-1 text-center maintenance-action-col">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $r)
            <tr class="maint-row"
                data-status="{{ strtoupper((string) $r->status) }}"
                data-search="{{ strtoupper(trim(($r->radio_id ?? '') . ' ' . ($r->status ?? '') . ' ' . ($r->serial_number ?? '') . ' ' . ($r->model ?? '') . ' ' . ($r->walkieTalkie->ownership_type ?? $r->ownership_type ?? '') . ' ' . ($r->current_ownership ?? '') . ' ' . ($r->department_name ?? '') . ' ' . ($r->location ?? $r->walkieTalkie->location ?? '') . ' ' . ($r->received_date ?? '') . ' ' . ($r->repair_date ?? '') . ' ' . ($r->finish_date ?? '') . ' ' . ($r->issue ?? '') . ' ' . ($r->issue_description ?? '') . ' ' . ($r->remarks ?? ''))) }}">
                <td>{{ $r->radio_id ?? '-' }}</td>
                <td>
                    @php
                        $badgeBg = '#f5f5f4'; $badgeColor = '#78716c';
                        if($r->status === 'FAULTY') { $badgeBg = '#fee2e2'; $badgeColor = '#ef4444'; }
                        elseif($r->status === 'UNDER REPAIR' || $r->status === 'REPAIRING') { $badgeBg = '#ffedd5'; $badgeColor = '#f97316'; }
                        elseif($r->status === 'READY TO COLLECT') { $badgeBg = '#ecfccb'; $badgeColor = '#65a30d'; }
                        elseif($r->status === 'DONE' || $r->status === 'ALREADY FIXED') { $badgeBg = '#dcfce7'; $badgeColor = '#22c55e'; }
                    @endphp
                    <span class="clean-admin-pill">{{ $r->status }}</span>
                </td>
                <td>{{ $r->serial_number ?? '-' }}</td>
                <td>{{ $r->model ?? '-' }}</td>
                <td>
                    <div class="inventory-item-title">{{ $r->issue ?? $r->issue_description ?? '-' }}</div>
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ $r->received_date ?? '-' }}</div>
                </td>
                <td class="maintenance-action-col">
                    <div class="maintenance-action-stack">
                        <button type="button" class="wt-btn wt-btn-sm maintenance-action-view" onclick="openGlobalMaintenanceTimeline('{{ $r->maintenance_id }}')">
                            <i class="fa-solid fa-eye"></i>
                            <span>View</span>
                        </button>
                        <a href="{{ route('wt.admin.maintenance.edit', $r->maintenance_id) }}" class="wt-btn wt-btn-sm maintenance-action-edit">
                            <i class="fa-solid fa-pen-to-square"></i>
                            <span>Edit</span>
                        </a>
                        @if(auth('wt')->user()->wt_role === 'admin_it')
                            <form action="{{ route('wt.admin.maintenance.destroy', $r->maintenance_id) }}" method="POST" data-modern-confirm="Delete maintenance record for {{ $r->radio_id ?? '-' }}?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="wt-btn wt-btn-sm wt-btn-danger maintenance-action-delete">
                                    <i class="fa-solid fa-trash"></i>
                                    <span>Delete</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    <div class="repair-table-footer">
        <div id="maintPageInfo" class="repair-table-info">Total: 0 items</div>
    </div>
</div>
</div>

{{-- ===================== ADD MODAL ===================== --}}
<div id="repairModal" class="modal-overlay" onclick="closeModalOutside(event)">
    <div class="modal-box p-8">
        <div class="modal-header">
            <div>
                <h2 class="modal-title">New Repair Record</h2>
                <p class="modal-subtitle">Register a unit maintenance or breakdown entry.</p>
            </div>
            <button onclick="closeRepairModal()" class="modal-close-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg>
            </button>
        </div>

        <form action="{{ route('wt.admin.maintenance.store') }}" method="POST" class="flex flex-col h-full overflow-hidden">
            @csrf
            <div class="modal-body p-6">
                <div class="modal-form-grid">
                    <div class="modal-form-group" style="grid-column: span 2;">
                        <label class="modal-form-label text-stone-500 font-bold uppercase tracking-wider">Unit to Repair (Radio ID) <span class="text-red-500">*</span></label>
                        <select name="walkie_id" class="modal-form-input focus:border-stone-800" required>
                            <option value="" disabled selected>Select unit...</option>
                            @foreach($walkies as $w)
                            <option value="{{ $w->walkie_id }}">{{ $w->radio_id }} / {{ $w->serial_number }} ({{ $w->department }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-form-group">
                        <label class="modal-form-label text-stone-500 font-bold uppercase tracking-wider">Received Date <span class="text-red-500">*</span></label>
                        <input type="date" name="received_date" class="modal-form-input focus:border-stone-800" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="modal-form-group">
                        <label class="modal-form-label text-stone-500 font-bold uppercase tracking-wider">Repair Date</label>
                        <input type="date" name="repair_date" class="modal-form-input focus:border-stone-800">
                    </div>
                    <div class="modal-form-group">
                        <label class="modal-form-label text-stone-500 font-bold uppercase tracking-wider">Status <span class="text-red-500">*</span></label>
                        <select name="status" class="modal-form-input focus:border-stone-800" required>
                            <option value="UNDER REPAIR" selected>UNDER REPAIR</option>
                            <option value="FAULTY">FAULTY</option>
                            <option value="B.E.R">B.E.R</option>
                            <option value="READY TO COLLECT">READY TO COLLECT</option>
                            <option value="ALREADY FIXED">ALREADY FIXED</option>
                            <option value="DONE">DONE / RESOLVED</option>
                        </select>
                    </div>
                    <div class="modal-form-group">
                        <label class="modal-form-label text-stone-500 font-bold uppercase tracking-wider">Done?</label>
                        <select name="done" class="modal-form-input focus:border-stone-800">
                            <option value="0">NO (Pending)</option>
                            <option value="1">YES (Finished)</option>
                        </select>
                    </div>
                    <div class="modal-form-group" style="grid-column: span 2;">
                        <label class="modal-form-label text-stone-500 font-bold uppercase tracking-wider">Issue Decription <span class="text-red-500">*</span></label>
                        <textarea name="issue" class="modal-form-input focus:border-stone-800" style="height:55px; resize:none;" placeholder="e.g. Broken PTT" required></textarea>
                    </div>
                    <div class="modal-form-group" style="grid-column: span 2;">
                        <label class="modal-form-label text-stone-500 font-bold uppercase tracking-wider">Remarks</label>
                        <textarea name="remarks" class="modal-form-input focus:border-stone-800" style="height:55px; resize:none;" placeholder="Notes..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-stone-50/50 border-t border-stone-100 flex justify-end gap-3 p-4">
                <button type="button" onclick="closeRepairModal()" class="text-[11px] font-bold text-stone-400 hover:text-stone-600 uppercase tracking-widest transition">Cancel</button>
                <button type="submit" class="flex items-center gap-2 px-6 py-2.5 bg-[#1e293b] text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-[#0f172a] transition-all border border-blue-400/20">
                    <i class="fas fa-plus text-[8px]"></i>
                    Submit Record
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===================== EDIT MODAL ===================== --}}
<div id="editModal" class="modal-overlay" onclick="closeEditOutside(event)">
    <div class="modal-box p-8">
        <div class="modal-header">
            <div>
                <h2 class="modal-title">Update Repair Record</h2>
                <p class="modal-subtitle" id="editModalSubtitle">Updating record...</p>
            </div>
            <button onclick="closeEditModal()" class="modal-close-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg>
            </button>
        </div>

        <form method="POST" id="editMaintenanceForm" class="flex flex-col h-full overflow-hidden">
            @csrf
            @method('PATCH')
            <div class="modal-body p-6">
                <div class="modal-form-grid">
                    <div class="modal-form-group">
                        <label class="modal-form-label text-stone-500 font-bold uppercase tracking-wider">Received Date <span class="text-red-500">*</span></label>
                        <input type="date" name="received_date" id="edit_received_date" class="modal-form-input focus:border-stone-800" required>
                    </div>
                    <div class="modal-form-group">
                        <label class="modal-form-label text-stone-500 font-bold uppercase tracking-wider">Repair Date</label>
                        <input type="date" name="repair_date" id="edit_repair_date" class="modal-form-input focus:border-stone-800">
                    </div>
                    <div class="modal-form-group">
                        <label class="modal-form-label text-stone-500 font-bold uppercase tracking-wider">Status <span class="text-red-500">*</span></label>
                        <select name="status" id="edit_status" class="modal-form-input focus:border-stone-800" required>
                            <option value="UNDER REPAIR">UNDER REPAIR</option>
                            <option value="FAULTY">FAULTY</option>
                            <option value="B.E.R">B.E.R</option>
                            <option value="READY TO COLLECT">READY TO COLLECT</option>
                            <option value="ALREADY FIXED">ALREADY FIXED</option>
                            <option value="DONE">DONE</option>
                        </select>
                    </div>
                    <div class="modal-form-group">
                        <label class="modal-form-label text-stone-500 font-bold uppercase tracking-wider">Mark Done</label>
                        <select name="done" id="edit_done" class="modal-form-input focus:border-stone-800">
                            <option value="0">NO (Pending)</option>
                            <option value="1">YES</option>
                        </select>
                    </div>
                    <div class="modal-form-group" style="grid-column: span 2;">
                        <label class="modal-form-label text-stone-500 font-bold uppercase tracking-wider">Finish Date</label>
                        <input type="date" name="finish_date" id="edit_finish_date" class="modal-form-input focus:border-stone-800">
                    </div>
                    <div class="modal-form-group" style="grid-column: span 2;">
                        <label class="modal-form-label text-stone-500 font-bold uppercase tracking-wider">Issue Decription <span class="text-red-500">*</span></label>
                        <textarea name="issue" id="edit_issue" class="modal-form-input focus:border-stone-800" style="height:55px; resize:none;" required></textarea>
                    </div>
                    <div class="modal-form-group" style="grid-column: span 2;">
                        <label class="modal-form-label text-stone-500 font-bold uppercase tracking-wider">Remarks</label>
                        <textarea name="remarks" id="edit_remarks" class="modal-form-input focus:border-stone-800" style="height:55px; resize:none;"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-stone-50/50 border-t border-stone-100 flex justify-end gap-3 p-4">
                <button type="button" onclick="closeEditModal()" class="text-[11px] font-bold text-stone-400 hover:text-stone-600 uppercase tracking-widest transition">Cancel</button>
                <button type="submit" class="flex items-center gap-2 px-6 py-2.5 bg-[#1e293b] text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-[#0f172a] transition-all border border-blue-400/20">
                    <i class="fas fa-check text-[8px]"></i>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('maintSearch');
    const statusSelect = document.getElementById('maintStatus');
    const resetButton = document.getElementById('maintReset');
    const rows = Array.from(document.querySelectorAll('#maintTable tbody .maint-row'));
    const pageInfo = document.getElementById('maintPageInfo');
    const perPage = 10;
    let currentPage = 1;
    let filteredRows = rows;

    function renderPageNumbers(totalPages) {
        const pageNumbers = document.getElementById('maintPageNumbers');
        if (!pageNumbers) return;
        pageNumbers.innerHTML = '';
        const pages = [];

        if (totalPages <= 1) {
            return;
        }

        if (totalPages <= 6) {
            for (let page = 1; page <= totalPages; page += 1) pages.push(page);
        } else if (currentPage <= 3) {
            pages.push(1, 2, 3, 4, 'ellipsis', totalPages);
        } else if (currentPage >= totalPages - 2) {
            pages.push(1, 'ellipsis', totalPages - 3, totalPages - 2, totalPages - 1, totalPages);
        } else {
            pages.push(1, 'ellipsis', currentPage - 1, currentPage, currentPage + 1, 'ellipsis', totalPages);
        }

        pages.forEach((page) => {
            if (page === 'ellipsis') {
                const ellipsis = document.createElement('span');
                ellipsis.className = 'repair-page-ellipsis';
                ellipsis.textContent = '...';
                pageNumbers.appendChild(ellipsis);
                return;
            }

            const button = document.createElement('button');
            button.type = 'button';
            button.className = `repair-page-number${page === currentPage ? ' is-active' : ''}`;
            button.textContent = page;
            button.addEventListener('click', function () {
                currentPage = page;
                renderMaintenancePage();
            });
            pageNumbers.appendChild(button);
        });
    }

    function renderMaintenancePage() {
        const totalItems = filteredRows.length;
        rows.forEach((row) => row.classList.add('hidden'));
        filteredRows.forEach((row) => row.classList.remove('hidden'));

        if (pageInfo) {
            pageInfo.textContent = `Total: ${totalItems} items`;
        }
    }

    function applyMaintFilters() {
        const searchValue = (searchInput?.value || '').trim().toUpperCase();
        const statusValue = (statusSelect?.value || '').trim().toUpperCase();

        filteredRows = rows.filter((row) => {
            const matchesSearch = !searchValue || (row.dataset.search || '').includes(searchValue);
            const matchesStatus = !statusValue || row.dataset.status === statusValue;
            return matchesSearch && matchesStatus;
        });
        currentPage = 1;
        renderMaintenancePage();
    }

    if (searchInput) searchInput.addEventListener('input', applyMaintFilters);
    if (statusSelect) statusSelect.addEventListener('change', applyMaintFilters);
    if (resetButton) {
        resetButton.addEventListener('click', function() {
            if (searchInput) searchInput.value = '';
            if (statusSelect) statusSelect.value = '';
            applyMaintFilters();
        });
    }

    applyMaintFilters();

    const maintTableScroll = document.querySelector('.maintenance-page-shell #mainTableContainer .clean-admin-table-scroll');
    if (maintTableScroll) {
        let isDraggingTable = false;
        let tableDragStartX = 0;
        let tableDragStartLeft = 0;
        const stopTableDrag = function(event) {
            if (!isDraggingTable) return;
            isDraggingTable = false;
            maintTableScroll.classList.remove('is-dragging');
            if (event?.pointerId && maintTableScroll.hasPointerCapture?.(event.pointerId)) {
                maintTableScroll.releasePointerCapture(event.pointerId);
            }
        };

        maintTableScroll.addEventListener('pointerdown', function(event) {
            if (event.button !== 0 || event.target.closest('a, button, input, select, textarea, form')) return;
            const maxScroll = this.scrollWidth - this.clientWidth;
            if (maxScroll <= 0) return;

            isDraggingTable = true;
            tableDragStartX = event.clientX;
            tableDragStartLeft = this.scrollLeft;
            this.classList.add('is-dragging');
            this.setPointerCapture?.(event.pointerId);
        });

        maintTableScroll.addEventListener('pointermove', function(event) {
            if (!isDraggingTable) return;
            event.preventDefault();
            this.scrollLeft = tableDragStartLeft - (event.clientX - tableDragStartX);
        });

        maintTableScroll.addEventListener('pointerup', stopTableDrag);
        maintTableScroll.addEventListener('pointercancel', stopTableDrag);
        maintTableScroll.addEventListener('pointerleave', stopTableDrag);

        maintTableScroll.addEventListener('wheel', function(event) {
            const maxScroll = this.scrollWidth - this.clientWidth;
            if (maxScroll <= 0) return;

            const delta = Math.abs(event.deltaX) > Math.abs(event.deltaY) ? event.deltaX : event.deltaY;
            const nextScroll = Math.max(0, Math.min(maxScroll, this.scrollLeft + delta));

            if (nextScroll !== this.scrollLeft) {
                event.preventDefault();
                this.scrollLeft = nextScroll;
            }
        }, { passive: false });
    }
});

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

function openRepairModal() { document.getElementById('repairModal').classList.add('active'); document.body.style.overflow = 'hidden'; }
function closeRepairModal() { document.getElementById('repairModal').classList.remove('active'); document.body.style.overflow = ''; }
function closeModalOutside(e) { if (e.target === document.getElementById('repairModal')) closeRepairModal(); }

function openEditModal(id, radio, serial, received, repair, done, finish, issue, remarks, status) {
    const form = document.getElementById('editMaintenanceForm');
    form.action = "{{ route('wt.admin.maintenance.update', ['maintenance' => '__ID__']) }}".replace('__ID__', id);
    document.getElementById('editModalSubtitle').innerText = `Unit ${radio} (${serial})`;
    document.getElementById('edit_received_date').value = received || '';
    document.getElementById('edit_repair_date').value = repair || '';
    document.getElementById('edit_status').value = status || 'UNDER REPAIR';
    document.getElementById('edit_done').value = done || '0';
    document.getElementById('edit_finish_date').value = finish || '';
    document.getElementById('edit_issue').value = issue || '';
    document.getElementById('edit_remarks').value = remarks || '';
    document.getElementById('editModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeEditModal() { document.getElementById('editModal').classList.remove('active'); document.body.style.overflow = ''; }
function closeEditOutside(e) { if (e.target === document.getElementById('editModal')) closeEditModal(); }

@if(session('success'))
    setTimeout(() => { const box = document.getElementById('alertBox'); if (box) { box.style.transition='opacity 0.4s'; box.style.opacity='0'; setTimeout(()=>box.remove(), 400); } }, 4000);
@endif
</script>

{{-- ===================== IMPORT EXCEL MODAL ===================== --}}
@if(auth('wt')->user()->wt_role === 'admin_it')
<div id="importModal" class="modal-overlay maintenance-import-modal" onclick="closeImportModalOutside(event)">
    <div class="modal-box maintenance-import-box" style="max-width: 500px;">
        <div class="modal-header">
            <div>
                <h2 class="modal-title">Bulk Import Repair Units</h2>
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
            <input type="hidden" name="import_context" value="maintenance">
            <div class="modal-body p-6">
                <div class="maintenance-import-dropzone rounded-2xl p-8 text-center">
                    <input type="file" name="file" id="import_file" class="hidden" required onchange="updateFileName(this)">
                    <label for="import_file" class="cursor-pointer">
                        <div class="maintenance-import-icon w-12 h-12 rounded-full shadow-sm flex items-center justify-center mx-auto mb-4">
                             <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                                <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
                            </svg>
                        </div>
                        <p class="maintenance-import-file-name text-xs font-bold" id="fileNameDisplay">Click to upload Excel or CSV</p>
                        <p class="maintenance-import-help text-[10px] mt-1 uppercase font-black tracking-widest">Required Headings: radio_id, serial_no, status:FAULTY...</p>
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
// ESC key to close
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeRepairModal();
        closeEditModal();
        closeImportModal();
    }
});
</script>

@endsection
