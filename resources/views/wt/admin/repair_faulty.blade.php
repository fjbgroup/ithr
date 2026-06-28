@extends('wt.layouts.admin')

@section('title', 'Under Repair / Faulty Units')

@push('styles')

@endpush

@section('content')
@include('wt.admin.partials.inventory-management-ui')

<div class="maintenance-page-shell">
<div class="page-header-block flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h3 class="page-title-standard">Under Repair / Faulty Units</h3>
        <p class="page-subtitle-standard">Records marked as REPAIRING, FAULTY, or B.E.R.</p>
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
    </div>
    @endif
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

<div class="clean-admin-filter">
    <div class="clean-admin-filter-grid">
        <div>
            <label class="clean-admin-label" for="maintenanceSearch">Search</label>
            <input type="text" id="maintenanceSearch" class="clean-admin-input" placeholder="Keywords">
        </div>
        <div>
            <label class="clean-admin-label" for="maintenanceStatus">Status</label>
            <select id="maintenanceStatus" class="clean-admin-select">
                <option value="">All Status</option>
                <option value="WAITING FOR ADMIN">Waiting For Admin</option>
                <option value="PENDING ADMIN IT">Pending Admin IT</option>
                <option value="UNDER REPAIR">Under Repair</option>
                <option value="REPAIRING">Repairing</option>
                <option value="FAULTY">Faulty</option>
                <option value="B.E.R">B.E.R</option>
                <option value="READY TO COLLECT">Ready To Collect</option>
                <option value="ALREADY FIXED">Already Fixed</option>
                <option value="DONE">Done</option>
            </select>
        </div>
        <button type="button" id="maintenanceReset" class="clean-admin-reset">Reset</button>
    </div>
</div>
<div class="clean-admin-table-shell" id="mainTableContainer">
    <div class="clean-admin-table-scroll">
    <table id="maintenanceTable" class="clean-admin-table table-auto w-full text-left">
        <thead>
            <tr>
                <th class="px-2 py-1 text-center">Radio ID</th>
                <th class="px-2 py-1 text-center">Serial No</th>
                <th class="px-2 py-1 text-center">Model</th>
                <th class="px-2 py-1 text-center">Ownership Type</th>
                <th class="px-2 py-1 text-center">Department</th>
                <th class="px-2 py-1 text-center">Date Received</th>
                <th class="px-2 py-1 text-center">Reporter</th>
                <th class="px-2 py-1 text-center">Issue</th>
                <th class="px-2 py-1 text-center">Status</th>
                <th class="px-2 py-1 text-center">Done</th>
                <th class="px-2 py-1 text-center">Finish Date</th>
                <th class="px-2 py-1 text-center">Remarks</th>
                <th class="px-2 py-1 text-center maintenance-progress-col">Repair Progress</th>
                <th class="px-2 py-1 text-center maintenance-action-col">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
            @php
                $repairProgress = $record->done ? 100 : match($record->status) {
                    'WAITING FOR ADMIN' => 24,
                    'PENDING ADMIN IT' => 48,
                    'UNDER REPAIR', 'REPAIRING' => 68,
                    'FAULTY' => 36,
                    'B.E.R' => 82,
                    'READY TO COLLECT' => 92,
                    'ALREADY FIXED' => 100,
                    default => 32,
                };
            @endphp
            <tr class="maintenance-row"
                data-status="{{ $record->done ? 'DONE' : strtoupper((string) $record->status) }}"
                data-search="{{ strtoupper(trim(($record->radio_id ?? '') . ' ' . ($record->serial_number ?? '') . ' ' . ($record->model ?? '') . ' ' . ($record->ownership_type ?? '') . ' ' . ($record->walkieTalkie->ownership_type ?? '') . ' ' . ($record->department_name ?? '') . ' ' . ($record->location ?? $record->walkieTalkie->location ?? '') . ' ' . ($record->received_date ?? '') . ' ' . ($record->reporter_name ?? '') . ' ' . ($record->reporter_staff_id ?? '') . ' ' . ($record->issue ?? '') . ' ' . ($record->issue_description ?? '') . ' ' . ($record->finish_date ?? '') . ' ' . ($record->remarks ?? '') . ' ' . ($record->status ?? ''))) }}">
                <td>{{ $record->radio_id ?? '-' }}</td>
                <td>{{ $record->serial_number ?? '-' }}</td>
                <td>{{ $record->model ?? '-' }}</td>
                <td>
                    @php
                        $ownershipType = strtoupper(trim((string) ($record->walkieTalkie->ownership_type ?? $record->ownership_type ?? '')));
                    @endphp
                    {{ in_array($ownershipType, ['INDIVIDUAL', 'SHARED', 'SPARE'], true) ? $ownershipType : '-' }}
                </td>
                <td>{{ $record->department_name ?? '-' }}</td>
                <td>{{ $record->received_date ?? '-' }}</td>
                <td>
                    @if($record->request_source === 'user')
                    <div class="flex flex-col gap-0">
                        <span>{{ $record->reporter_name }}</span>
                        <span class="text-[9px] text-slate-400">{{ $record->reporter_staff_id }}</span>
                    </div>
                    @else
                    <span class="text-[9px] text-slate-400 italic">SYSTEM</span>
                    @endif
                </td>
                <td class="max-w-xs">
                    <div class="line-clamp-1">{{ $record->issue ?? $record->issue_description ?? '-' }}</div>
                </td>
                <td class="text-center">
                    @if($record->status === 'WAITING FOR ADMIN')
                    <span class="clean-admin-pill maintenance-status-pill" data-status="WAITING FOR ADMIN">WAITING</span>
                    @elseif($record->status === 'PENDING ADMIN IT')
                    <span class="clean-admin-pill maintenance-status-pill" data-status="PENDING ADMIN IT">PENDING IT</span>
                    @elseif($record->done || $record->status === 'ALREADY FIXED')
                    <span class="clean-admin-pill maintenance-status-pill" data-status="DONE">DONE</span>
                    @else
                    <span class="clean-admin-pill maintenance-status-pill" data-status="{{ strtoupper((string) ($record->status ?: 'WARNING')) }}">{{ $record->status ?: 'WARNING' }}</span>
                    @endif
                </td>
                <td class="text-center">
                    <span class="clean-admin-pill maintenance-done-pill {{ $record->done ? 'maintenance-done-yes' : 'maintenance-done-no' }}" data-done="{{ $record->done ? 'YES' : 'NO' }}">{{ $record->done ? 'YES' : 'NO' }}</span>
                </td>
                <td>{{ $record->finish_date ?? '-' }}</td>
                <td class="max-w-xs">
                    <div class="line-clamp-1 italic">{{ $record->remarks ?? '-' }}</div>
                </td>
                <td class="maintenance-progress-col">
                    <div class="h-1 w-24 rounded-[4px] bg-slate-700/80 overflow-hidden border border-white/5">
                        <div class="h-full rounded-[4px] bg-[#38bdf8]" style="width: {{ $repairProgress }}%;"></div>
                    </div>
                    <div class="mt-1 text-[9px] text-slate-400">{{ $repairProgress }}%</div>
                </td>
                <td class="maintenance-action-col">
                    <div class="clean-admin-actions">
                        <button type="button" class="wt-btn wt-btn-sm" onclick="openGlobalMaintenanceTimeline('{{ $record->maintenance_id }}')">
                            View
                        </button>
                        @if(auth('wt')->user()->wt_role === 'admin_it')
                        <button type="button" 
                            class="wt-btn wt-btn-sm"
                            onclick="openEditModal(
                                '{{ $record->maintenance_id }}',
                                '{{ $record->radio_id }}',
                                '{{ $record->serial_number }}',
                                '{{ $record->model }}',
                                '{{ $record->current_ownership }}',
                                '{{ $record->department_name }}',
                                '{{ $record->received_date }}',
                                '{{ $record->repair_date }}',
                                '{{ $record->done ? 1 : 0 }}',
                                '{{ $record->finish_date }}',
                                '{{ addslashes($record->issue ?: $record->issue_description) }}',
                                '{{ addslashes($record->remarks ?? '') }}',
                                '{{ $record->status }}'
                            )">
                            Edit
                        </button>
                        <form action="{{ route('wt.admin.maintenance.destroy', $record->maintenance_id) }}" method="POST" style="display:inline;" data-modern-confirm="Delete maintenance record for {{ $record->radio_id ?? '-' }}?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="wt-btn wt-btn-danger wt-btn-sm">Delete</button>
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
        <div id="maintenancePageInfo" class="repair-table-info">Total: 0 items</div>
    </div>
</div>
</div>

{{-- ===================== MAINTENANCE EDIT MODAL ===================== --}}
<div id="editModal" class="modal-overlay" onclick="closeEditModalOutside(event)">
    <div class="modal-box" id="editModalBox">
        <div class="modal-header">
            <div>
                <h2 class="modal-title">Edit Maintenance Record</h2>
                <p class="modal-subtitle" id="editModalSubtitle">Update repair status and technician notes.</p>
            </div>
            <button onclick="closeEditModal()" class="modal-close-btn" title="Close">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                </svg>
            </button>
        </div>

        <form method="POST" id="editMaintenanceForm">
            @csrf
            @method('PATCH')
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Received Date <span class="required">*</span></label>
                    <input type="date" name="received_date" id="edit_received_date" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Repair Date</label>
                    <input type="date" name="repair_date" id="edit_repair_date" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Status <span class="required">*</span></label>
                    <select name="status" id="edit_status" class="form-input" required>
                        @foreach(['UNDER REPAIR','FAULTY','B.E.R','READY TO COLLECT','ALREADY FIXED','DONE'] as $status)
                        <option value="{{ $status }}">{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Mark as Done</label>
                    <select name="done" id="edit_done" class="form-input">
                        <option value="0">PENDING</option>
                        <option value="1">DONE</option>
                    </select>
                </div>
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label">Finish Date</label>
                    <input type="date" name="finish_date" id="edit_finish_date" class="form-input">
                </div>
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label">Issue Decription <span class="required">*</span></label>
                    <textarea name="issue" id="edit_issue" class="form-input" rows="3" required></textarea>
                </div>
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label">Maintenance Remarks</label>
                    <textarea name="remarks" id="edit_remarks" class="form-input" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeEditModal()" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-submit">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('maintenanceSearch');
    const statusSelect = document.getElementById('maintenanceStatus');
    const resetButton = document.getElementById('maintenanceReset');
    const rows = Array.from(document.querySelectorAll('#maintenanceTable tbody .maintenance-row'));
    const pageInfo = document.getElementById('maintenancePageInfo');
    const perPage = 10;
    let currentPage = 1;
    let filteredRows = rows;

    function renderPageNumbers(totalPages) {
        const pageNumbers = document.getElementById('maintenancePageNumbers');
        if (!pageNumbers) return;
        pageNumbers.innerHTML = '';
        const pages = [];

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

    function applyMaintenanceFilters() {
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

    if (searchInput) {
        searchInput.addEventListener('input', applyMaintenanceFilters);
    }

    if (statusSelect) {
        statusSelect.addEventListener('change', applyMaintenanceFilters);
    }
    if (resetButton) {
        resetButton.addEventListener('click', function() {
            if (searchInput) searchInput.value = '';
            if (statusSelect) statusSelect.value = '';
            applyMaintenanceFilters();
        });
    }

    applyMaintenanceFilters();

    const maintenanceTableScroll = document.querySelector('.maintenance-page-shell #mainTableContainer .clean-admin-table-scroll');
    if (maintenanceTableScroll) {
        let isDraggingTable = false;
        let tableDragStartX = 0;
        let tableDragStartLeft = 0;
        const stopTableDrag = function(event) {
            if (!isDraggingTable) return;
            isDraggingTable = false;
            maintenanceTableScroll.classList.remove('is-dragging');
            if (event?.pointerId && maintenanceTableScroll.hasPointerCapture?.(event.pointerId)) {
                maintenanceTableScroll.releasePointerCapture(event.pointerId);
            }
        };

        maintenanceTableScroll.addEventListener('pointerdown', function(event) {
            if (event.button !== 0 || event.target.closest('a, button, input, select, textarea, form')) return;
            const maxScroll = this.scrollWidth - this.clientWidth;
            if (maxScroll <= 0) return;

            isDraggingTable = true;
            tableDragStartX = event.clientX;
            tableDragStartLeft = this.scrollLeft;
            this.classList.add('is-dragging');
            this.setPointerCapture?.(event.pointerId);
        });

        maintenanceTableScroll.addEventListener('pointermove', function(event) {
            if (!isDraggingTable) return;
            event.preventDefault();
            this.scrollLeft = tableDragStartLeft - (event.clientX - tableDragStartX);
        });

        maintenanceTableScroll.addEventListener('pointerup', stopTableDrag);
        maintenanceTableScroll.addEventListener('pointercancel', stopTableDrag);
        maintenanceTableScroll.addEventListener('pointerleave', stopTableDrag);

        maintenanceTableScroll.addEventListener('wheel', function(event) {
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

function openEditModal(id, radio, serial, model, ownership, department, receivedDate, repairDate, done, finishDate, issue, remarks, status) {
    const form = document.getElementById('editMaintenanceForm');
    form.action = "{{ route('wt.admin.maintenance.update', ['maintenance' => '__ID__']) }}".replace('__ID__', id);
    
    document.getElementById('editModalSubtitle').innerText = `Repair Record for Unit ${radio} (${serial})`;
    document.getElementById('edit_received_date').value = receivedDate || '';
    document.getElementById('edit_repair_date').value = repairDate || '';
    document.getElementById('edit_status').value = status || 'FAULTY';
    document.getElementById('edit_done').value = done || '0';
    document.getElementById('edit_finish_date').value = finishDate || '';
    document.getElementById('edit_issue').value = issue || '';
    document.getElementById('edit_remarks').value = remarks || '';
    
    document.getElementById('editModal').classList.add('active');
    document.body.style.overflow = 'hidden';
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

// ESC key to close
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeEditModal();
    }
});

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
</script>

{{-- ===================== IMPORT EXCEL MODAL ===================== --}}
@if(auth('wt')->user()->wt_role === 'admin_it')
<div id="importModal" class="modal-overlay" onclick="closeImportModalOutside(event)">
    <div class="modal-box" style="max-width: 500px;">
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
                        <p class="text-[10px] text-stone-400 mt-1 uppercase font-black tracking-widest">Required Headings: radio_id, serial_no, status:FAULTY...</p>
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

@include('wt.admin.partials.inventory-tools-table-skin')

<script>
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeEditModal();
        closeImportModal();
    }
});
</script>

@endsection
