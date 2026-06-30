    @extends('wt.layouts.admin')

    @section('title', 'Inventory List')

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script>
        (function () {
            if ('scrollRestoration' in history) {
                history.scrollRestoration = 'manual';
            }

            function resetInventoryScroll() {
                window.scrollTo(0, 0);
                document.documentElement.scrollTop = 0;
                document.body.scrollTop = 0;

                var mainContent = document.querySelector('.main-content');
                if (mainContent) mainContent.scrollTop = 0;

                var tableScroll = document.getElementById('inventoryTableScroll');
                if (tableScroll) {
                    tableScroll.scrollTop = 0;
                    tableScroll.scrollLeft = 0;
                }
            }

            resetInventoryScroll();
            document.addEventListener('DOMContentLoaded', resetInventoryScroll);
            window.addEventListener('load', resetInventoryScroll);
            window.addEventListener('pageshow', resetInventoryScroll);
        })();
    </script>
    @endpush

    @push('scripts')
    <script>
        (function () {
            function normalizeInventorySearch(value) {
                return String(value || '').trim().toUpperCase();
            }

            function applyInventorySearchFilter() {
                var searchInput = document.getElementById('globalSearch');
                var statusFilter = document.getElementById('filterStatus');
                var rows = Array.from(document.querySelectorAll('#walkiesTable tbody tr.inventory-row'));
                var keyword = normalizeInventorySearch(searchInput ? searchInput.value : '');
                var status = normalizeInventorySearch(statusFilter ? statusFilter.value : '');

                rows.forEach(function (row) {
                    var haystack = row.dataset.search || row.textContent || '';
                    var rowStatus = normalizeInventorySearch(row.dataset.status || '');
                    var matchesKeyword = !keyword || normalizeInventorySearch(haystack).indexOf(keyword) !== -1;
                    var matchesStatus = !status || rowStatus === status;
                    row.style.display = matchesKeyword && matchesStatus ? '' : 'none';
                });

                var totalItems = document.getElementById('totalItems');
                if (totalItems) {
                    totalItems.textContent = rows.filter(function (row) {
                        return row.style.display !== 'none';
                    }).length;
                }

                if (typeof window.paintInventoryTableTheme === 'function') {
                    window.paintInventoryTableTheme();
                }
            }

            function bindInventorySearchFilter() {
                var searchInput = document.getElementById('globalSearch');
                var statusFilter = document.getElementById('filterStatus');
                var resetBtn = document.getElementById('resetFilters');

                if (searchInput && searchInput.dataset.inventorySearchBound !== 'true') {
                    searchInput.dataset.inventorySearchBound = 'true';
                    searchInput.addEventListener('input', applyInventorySearchFilter);
                    searchInput.addEventListener('keyup', applyInventorySearchFilter);
                    searchInput.addEventListener('keydown', function (event) {
                        if (event.key === 'Enter') {
                            event.preventDefault();
                            applyInventorySearchFilter();
                        }
                    });
                }

                if (statusFilter && statusFilter.dataset.inventorySearchBound !== 'true') {
                    statusFilter.dataset.inventorySearchBound = 'true';
                    statusFilter.addEventListener('change', applyInventorySearchFilter);
                }

                if (resetBtn && resetBtn.dataset.inventorySearchBound !== 'true') {
                    resetBtn.dataset.inventorySearchBound = 'true';
                    resetBtn.addEventListener('click', function () {
                        if (searchInput) searchInput.value = '';
                        if (statusFilter) statusFilter.value = '';
                        applyInventorySearchFilter();
                    });
                }

                applyInventorySearchFilter();
            }

            document.addEventListener('DOMContentLoaded', bindInventorySearchFilter);
            window.addEventListener('load', bindInventorySearchFilter);
        })();
    </script>
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    @endpush

    @section('content')
    @php
        $walkieRadioIds = $walkies->pluck('radio_id')->filter()->unique()->sort()->values();
        $walkieSerials = $walkies->pluck('serial_number')->filter()->unique()->sort()->values();
        // $walkieModels, $walkieDepartments, $walkiePositions come from the
        // controller (WT master data, merged with existing values).
        $walkieTemporaryIds = $walkies->pluck('temporary_radio_id')->filter()->unique()->sort()->values();
        $walkieTrackingRefs = $walkies->pluck('tracking_ref')->filter()->unique()->sort()->values();
        // $statusOptions comes from the controller (ALLOWED_STATUSES constant).
        $inventorySummary = [
            'total' => $walkies->count(),
            'in_use' => $walkies->filter(fn ($walkie) => strtoupper((string) $walkie->status) === 'IN USE')->count(),
            'unused' => $walkies->filter(fn ($walkie) => strtoupper((string) $walkie->status) === 'UNUSED')->count(),
            'repair_faulty' => $walkies->filter(fn ($walkie) => in_array(strtoupper((string) $walkie->status), ['REPAIRING', 'FAULTY', 'B.E.R'], true))->count(),
        ];
        // $ownershipTypeOptions comes from the controller (WT master data).
        $yesNoOptions = collect([['value' => '0', 'label' => 'NO'], ['value' => '1', 'label' => 'YES']]);
    @endphp

    <div class="inventory-page-shell">
    <div class="inventory-page-header page-header-block">
        <div class="inventory-header-copy">
            <h1 class="page-title-standard text-slate-100">Inventory List</h1>
            <p class="page-subtitle-standard text-slate-400">{{ auth('wt')->user()->wt_role === 'admin_it' ? 'Displaying all walkie talkies with full management access.' : 'Displaying all walkie talkies in read-only mode for executive review.' }}</p>
        </div>
        <div class="inventory-header-actions">
            @if(auth('wt')->user()->wt_role === 'admin_it')
            <button onclick="openImportModal()" class="wt-btn wt-btn-soft">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="margin-right:5px;">
                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
                </svg>
                Import Excel
            </button>
            @endif

            @if(auth('wt')->user()->wt_role === 'admin_it')
            <a href="{{ route('wt.admin.walkies.create') }}" class="wt-btn wt-btn-soft">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="margin-right:5px;">
                    <path d="M8 1a.5.5 0 0 1 .5.5V7.5H14a.5.5 0 0 1 0 1H8.5V14a.5.5 0 0 1-1 0V8.5H2a.5.5 0 0 1 0-1h5.5V1.5A.5.5 0 0 1 8 1z"/>
                </svg>
                Add Item
            </a>
            @else
            <div class="px-4 py-2 rounded-2xl bg-slate-800 border border-slate-700 text-slate-300 text-xs font-black uppercase tracking-[0.15em]">Read Only</div>
            @endif
        </div>
    </div>

    <div class="inventory-summary-grid">
        <div class="inventory-summary-card">
            <p class="inventory-summary-label">Total Records</p>
            <p class="inventory-summary-value">{{ number_format($inventorySummary['total']) }}</p>
        </div>
        <div class="inventory-summary-card">
            <p class="inventory-summary-label">In Use</p>
            <p class="inventory-summary-value is-use">{{ number_format($inventorySummary['in_use']) }}</p>
        </div>
        <div class="inventory-summary-card">
            <p class="inventory-summary-label">Unused</p>
            <p class="inventory-summary-value is-unused">{{ number_format($inventorySummary['unused']) }}</p>
        </div>
        <div class="inventory-summary-card">
            <p class="inventory-summary-label">Repair / Faulty</p>
            <p class="inventory-summary-value is-repair">{{ number_format($inventorySummary['repair_faulty']) }}</p>
        </div>
    </div>

    {{-- Success / Error Alert --}}
    @if(session('success'))
    <div class="alert-success" id="alertBox">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right:8px;flex-shrink:0;">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert-error" id="errorBoxSession">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right:8px;flex-shrink:0;">
            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
        </svg>
        {{ session('error') }}
    </div>
    @endif

    @if($errors instanceof \Illuminate\Support\ViewErrorBag && $errors->any())
    <div class="alert-error mb-6" id="errorBox">
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

        <div class="inventory-table-frame max-w-screen-xl mx-auto px-4">
        {{-- ===== SEARCH & FILTER BAR ===== --}}
        <div class="clean-admin-filter">
            <div class="clean-admin-filter-grid inventory-filter-inline" style="display:flex !important;flex-direction:row !important;flex-wrap:nowrap !important;align-items:center !important;justify-content:flex-start !important;gap:10px !important;width:auto !important;">
                {{-- Search Input --}}
                <div class="inventory-filter-field" style="display:flex !important;flex-direction:row !important;align-items:center !important;gap:8px !important;width:auto !important;min-width:0 !important;">
                    <label class="clean-admin-label" for="globalSearch" style="margin:0 !important;line-height:30px !important;white-space:nowrap !important;">Search</label>
                    <input type="text" id="globalSearch" class="clean-admin-input" placeholder="Keywords" style="width: 250px !important; max-width: 250px !important;">
                </div>

                {{-- Status Filter --}}
                <div class="inventory-filter-field" style="display:flex !important;flex-direction:row !important;align-items:center !important;gap:8px !important;width:auto !important;min-width:0 !important;">
                    <label class="clean-admin-label" for="filterStatus" style="margin:0 !important;line-height:30px !important;white-space:nowrap !important;">Status</label>
                    <select id="filterStatus" class="clean-admin-select" style="width:160px !important;">
                        <option value="">All Status</option>
                        @foreach($statusOptions as $s)
                        <option value="{{ $s }}">{{ $s }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Reset Button --}}
                <button type="button" id="resetFilters" class="clean-admin-reset" title="Reset all filters" style="width:68px !important;min-width:68px !important;">Reset</button>
            </div>
        </div>

        @php
        $showInventoryBulk = false;
        @endphp

        <div id="mainTableContainer" class="clean-admin-table-shell inventory-table-shell border border-gray-200 rounded-xl overflow-hidden">
            <div id="inventoryTableScroll" class="clean-admin-table-scroll overflow-hidden">
                <table id="walkiesTable" class="clean-admin-table table-fixed w-full border-collapse">
                <colgroup>
                    @if($showInventoryBulk)
                    <col class="inventory-select-colgroup">
                    @endif
                    <col class="inventory-radio-colgroup">
                    <col class="inventory-status-colgroup">
                    <col class="inventory-serial-colgroup">
                    <col class="inventory-model-colgroup">
                    <col class="inventory-ownership-colgroup">
                    <col class="inventory-action-colgroup">
                </colgroup>
                <thead>
                    <tr>
                        @if($showInventoryBulk)
                        <th class="px-3 py-3 text-center inventory-select-col"></th>
                        @endif
                        <th class="inventory-radio-col text-center" style="width:10%; text-align:center !important;">RADIO ID</th>
                        <th class="inventory-status-col text-center" style="width:10%; text-align:center !important;">STATUS</th>
                        <th class="inventory-serial-col text-center" style="width:15%; text-align:center !important;">SERIAL NO.</th>
                        <th class="inventory-model-col text-center" style="width:15%; text-align:center !important;">MODEL</th>
                        <th class="inventory-assigned-col text-center" style="width:35%; text-align:center !important;">ASSIGNED TO</th>
                        <th class="inventory-action-col text-center" style="width:15%; text-align:center !important;">ACTION</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/40 text-[11px]">
                    @foreach($walkies as $w)
                    @php
                        $statusValue = strtoupper((string) ($w->status ?: 'UNKNOWN'));
                    @endphp
                    <tr class="inventory-row hover:bg-slate-700/30 transition"
                        data-walkie-id="{{ $w->walkie_id }}"
                        data-status="{{ $statusValue }}"
                        data-model="{{ strtoupper((string) ($w->model ?: 'NO MODEL')) }}"
                        data-ownership-type="{{ strtoupper((string) ($w->ownership_type ?: '-')) }}"
                        data-search="{{ strtoupper(trim(($w->radio_id ?? '') . ' ' . ($w->model ?? '') . ' ' . ($w->serial_number ?? '') . ' ' . ($w->ownership_type ?? '') . ' ' . ($w->shared_with ?? '') . ' ' . ($w->ownership ?? '') . ' ' . ($w->department ?? '') . ' ' . ($w->location ?? '') . ' ' . ($w->position ?? '') . ' ' . ($w->status ?? '') . ' ' . ($w->temporary_radio_id ?? '') . ' ' . ($w->tracking_ref ?? '') . ' ' . ($w->remark ?? '') . ' ' . ($w->need_to_change_id ?? '') . ' ' . ($w->ownership_type_to_be ?? ''))) }}">
                        @if($showInventoryBulk)
                        <td class="text-center inventory-select-col">
                            <input type="checkbox" class="inventory-row-checkbox inventory-bulk-checkbox" value="{{ $w->walkie_id }}" data-label="{{ $w->radio_id ?: $w->serial_number ?: $w->walkie_id }}">
                        </td>
                        @endif
                        <td class="inventory-radio-col">
                            <span class="clean-admin-pill inventory-id-chip">{{ $w->radio_id ?: '-' }}</span>
                            @if($w->need_to_change_id)
                            <span class="ml-1 text-yellow-300" title="DUPLICATE ID: Need to change">ðŸš©</span>
                            @endif
                        </td>
                        <td class="inventory-status-col">
                            <span class="clean-admin-pill inventory-status-badge" data-status="{{ $statusValue }}">{{ $statusValue }}</span>
                        </td>
                        <td class="inventory-serial-col text-center" style="text-align:center !important;">
                            {{ $w->serial_number ?: '-' }}
                        </td>
                        <td class="inventory-model-col text-center" style="text-align:center !important;">
                            <div class="inventory-item-title" style="text-align:center !important;">{{ $w->model ?: 'NO MODEL' }}</div>
                        </td>
                        <td class="inventory-assigned-col">
                            <div class="inventory-assigned-primary">{{ $w->ownership ?: '-' }}</div>
                            <div class="inventory-assigned-meta">{{ $w->department ?: '-' }} / {{ $w->ownership_type ?: '-' }}</div>
                        </td>
                        <td class="inventory-action-col">
                            @if(auth('wt')->user()->wt_role === 'admin_it')
                            <div class="inventory-action-buttons">
                                <button type="button" class="btn btn-info btn-sm" title="View Details" onclick="openGlobalWalkieTimeline('{{ $w->walkie_id }}')">
                                    <i class="fa-solid fa-eye"></i>
                                    <span>View</span>
                                </button>

                                <a href="{{ route('wt.admin.walkies.edit', ['walkie' => $w->walkie_id, 'source' => 'index']) }}" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-edit"></i>
                                    <span>Edit</span>
                                </a>

                                @if($statusValue === 'IN USE')
                                <form action="{{ route('wt.admin.walkies.update.status', $w->walkie_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Mark this unit as UNUSED after handover?');">
                                    @csrf
                                    <input type="hidden" name="status" value="UNUSED">
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fa-solid fa-handshake"></i>
                                        <span>Handover</span>
                                    </button>
                                </form>
                                @else
                                <button type="button" class="btn btn-secondary btn-sm" disabled title="Only IN USE units show handover action">
                                    <i class="fa-solid fa-handshake"></i>
                                    <span>Handover</span>
                                </button>
                                @endif

                                <form action="{{ route('wt.admin.walkies.forceDelete', $w->walkie_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this walkie-talkie record?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fa-solid fa-trash"></i>
                                        <span>Delete</span>
                                    </button>
                                </form>
                            </div>
                        @else
                        <button type="button" class="btn btn-info btn-sm" title="View Details" onclick="openGlobalWalkieTimeline('{{ $w->walkie_id }}')">
                            <i class="fa-solid fa-eye"></i>
                            <span>View</span>
                        </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            </table>
            </div>
            <div id="inventoryPagination" class="inventory-table-footer flex items-center justify-between px-4 py-3 bg-[#111827] border-t border-[#263244]">
                <div class="inventory-table-info text-[10px] text-slate-400 font-semibold uppercase tracking-wider">
                    Total: <span id="totalItems">{{ $walkies->count() }}</span> items
                </div>
            </div>
        </div>
        </div>

    </div>

    <div id="walkieTimelineModal" class="modal-overlay" onclick="closeWalkieTimelineOutside(event)" aria-hidden="true">
        <div class="modal-box walkie-timeline-modal" role="dialog" aria-modal="true" aria-labelledby="timelineTitle">
            <div class="walkie-timeline-header">
                <div class="min-w-0">
                    <p class="walkie-timeline-kicker">Unit Timeline</p>
                    <h2 id="timelineTitle" class="walkie-timeline-title">-</h2>
                    <p id="timelineSubtitle" class="walkie-timeline-subtitle">-</p>
                </div>
                <button type="button" class="walkie-timeline-close" onclick="closeWalkieTimeline()" aria-label="Close timeline">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="timelineSummary" class="walkie-timeline-summary"></div>
            <div id="timelineBody" class="walkie-timeline-body"></div>
        </div>
    </div>

    <div id="walkieQrModal" class="modal-overlay" onclick="closeWalkieQrOutside(event)" aria-hidden="true">
        <div class="modal-box walkie-qr-modal" role="dialog" aria-modal="true" aria-labelledby="walkieQrTitle">
            <div class="walkie-qr-header">
                <div class="min-w-0">
                    <p class="walkie-qr-kicker">Unit QR Code</p>
                    <h2 id="walkieQrTitle" class="walkie-qr-title">-</h2>
                    <p id="walkieQrSubtitle" class="walkie-qr-subtitle">-</p>
                </div>
                <button type="button" class="walkie-qr-close" onclick="closeWalkieQr()" aria-label="Close QR modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="walkie-qr-content">
                <div class="walkie-qr-card">
                    <div id="walkieQrCanvas" class="walkie-qr-canvas"></div>
                    <p id="walkieQrFallback" class="walkie-qr-fallback"></p>
                </div>
                <div id="walkieQrDetails" class="walkie-qr-details"></div>
            </div>
            <div class="walkie-qr-footer">
                <button type="button" class="walkie-qr-action" onclick="downloadWalkieQr()">
                    <i class="fa-solid fa-download"></i>
                    Download
                </button>
                <button type="button" class="walkie-qr-action" onclick="printWalkieQr()">
                    <i class="fa-solid fa-print"></i>
                    Print
                </button>
            </div>
        </div>
    </div>

    @if(auth('wt')->user()->wt_role === 'admin_it')
    {{-- ===================== IMPORT EXCEL MODAL ===================== --}}
    <div id="importModal" class="modal-overlay" onclick="closeImportModalOutside(event)">
        <div class="modal-box">
            <div class="modal-header">
                <div>
                    <h2 class="modal-title">Bulk Import Walkie Talkies</h2>
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
                            <p class="text-[10px] text-stone-400 mt-1 uppercase font-black tracking-widest">Required Headings: radio_id, serial_number, model...</p>
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

    {{-- ===================== ADD DATA MODAL ===================== --}}
    <div id="addModal" class="modal-overlay" onclick="closeModalOutside(event)">
        <div class="modal-box" id="modalBox">

            {{-- Header --}}
            <div class="modal-header">
                <div>
                    <h2 class="modal-title">Add New Walkie Talkie</h2>
                    <p class="modal-subtitle">Fill in all required fields to register a new unit.</p>
                </div>
                <button onclick="closeAddModal()" class="modal-close-btn" title="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                    </svg>
                </button>
            </div>

            {{-- Form --}}
            <form action="{{ route('wt.admin.walkies.store') }}" method="POST" id="addWalkieForm" class="flex flex-col h-full overflow-hidden">
                @csrf
                <div class="modal-body">
                    <div class="form-grid">
                    {{-- Radio ID --}}
                    <div class="form-group">
                        <label class="form-label">Radio ID <span class="required">*</span></label>
                        <select name="radio_id" id="add_radio_id" class="form-input modal-tag-select" data-placeholder="Type or select radio id" required>
                            <option value=""></option>
                            @foreach($walkieRadioIds as $radioId)
                            <option value="{{ $radioId }}" @selected(old('radio_id') === $radioId)>{{ $radioId }}</option>
                            @endforeach
                            @if(old('radio_id') && !$walkieRadioIds->contains(old('radio_id')))
                            <option value="{{ old('radio_id') }}" selected>{{ old('radio_id') }}</option>
                            @endif
                        </select>
                    </div>

                    {{-- Serial Number --}}
                    <div class="form-group">
                        <label class="form-label">Serial Number <span class="required">*</span></label>
                        <select name="serial_number" id="add_serial_number" class="form-input modal-tag-select" data-placeholder="Type or select serial number" required>
                            <option value=""></option>
                            @foreach($walkieSerials as $serial)
                            <option value="{{ $serial }}" @selected(old('serial_number') === $serial)>{{ $serial }}</option>
                            @endforeach
                            @if(old('serial_number') && !$walkieSerials->contains(old('serial_number')))
                            <option value="{{ old('serial_number') }}" selected>{{ old('serial_number') }}</option>
                            @endif
                        </select>
                    </div>

                    {{-- Status --}}
                    <div class="form-group">
                        <label class="form-label">Status <span class="required">*</span></label>
                        <select name="status" id="statusSelect" class="form-input modal-smart-select" data-placeholder="Search status" required onchange="toggleMaintenanceFields()">
                            <option value="" disabled selected>Select status...</option>
                            @foreach($statusOptions as $status)
                            <option value="{{ $status }}" {{ old('status') == $status ? 'selected' : '' }}>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Ownership Type --}}
                    <div class="form-group">
                        <label class="form-label">Ownership Type <span class="required">*</span></label>
                        <select name="ownership_type" id="add_ownership_type" class="form-input modal-tag-select ownership-type-control" data-placeholder="Type or search ownership type" required>
                            <option value="" disabled selected>Select type...</option>
                            @foreach($ownershipTypeOptions as $ownershipType)
                            <option value="{{ $ownershipType }}" {{ old('ownership_type') == $ownershipType ? 'selected' : '' }}>{{ $ownershipType }}</option>
                            @endforeach
                            @if(old('ownership_type') && !$ownershipTypeOptions->contains(strtoupper(trim(old('ownership_type')))))
                            <option value="{{ strtoupper(trim(old('ownership_type'))) }}" selected>{{ strtoupper(trim(old('ownership_type'))) }}</option>
                            @endif
                        </select>
                    </div>

                    <div class="form-group shared-with-group hidden">
                        <label class="form-label">Shared With <span class="required">*</span></label>
                        <input type="text" name="shared_with" id="add_shared_with" value="{{ strtoupper(old('shared_with', '')) }}" class="form-input shared-with-input" placeholder="E.G. USER / TEAM / DEPARTMENT">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Model <span class="required">*</span></label>
                        <select name="model" id="add_model" class="form-input modal-tag-select" data-placeholder="Type or select model" required>
                            <option value="" disabled selected>Select model...</option>
                            @foreach($walkieModels as $model)
                            <option value="{{ $model }}" {{ old('model') == $model ? 'selected' : '' }}>{{ $model }}</option>
                            @endforeach
                            @if(old('model') && !$walkieModels->contains(old('model')))
                            <option value="{{ old('model') }}" selected>{{ old('model') }}</option>
                            @endif
                        </select>
                    </div>

                    {{-- Ownership --}}
                    <div class="form-group">
                        <label class="form-label">Ownership Name</label>
                        <select name="ownership" id="add_ownership" class="form-input modal-smart-select" data-placeholder="Select staff">
                            <option value=""></option>
                            @foreach($staffOwnerships as $ownership)
                            <option value="{{ $ownership }}" @selected(old('ownership') === $ownership)>{{ $ownership }}</option>
                            @endforeach
                            @if(old('ownership') && !$staffOwnerships->contains(old('ownership')))
                            <option value="{{ old('ownership') }}" selected>{{ old('ownership') }}</option>
                            @endif
                        </select>
                    </div>

                    {{-- Department --}}
                    <div class="form-group">
                        <label class="form-label">Department</label>
                        <select name="department" id="add_department" class="form-input modal-tag-select" data-placeholder="Type or select department">
                            <option value=""></option>
                            @foreach($walkieDepartments as $department)
                            <option value="{{ $department }}" @selected(old('department') === $department)>{{ $department }}</option>
                            @endforeach
                            @if(old('department') && !$walkieDepartments->contains(old('department')))
                            <option value="{{ old('department') }}" selected>{{ old('department') }}</option>
                            @endif
                        </select>
                    </div>

                    {{-- Location --}}
                    <div class="form-group">
                        <label class="form-label">Location</label>
                        <select name="location" id="add_location" class="form-input modal-tag-select" data-placeholder="Type or select location">
                            <option value=""></option>
                            @foreach($walkieLocations as $location)
                            <option value="{{ $location }}" @selected(old('location') === $location)>{{ $location }}</option>
                            @endforeach
                            @if(old('location') && !$walkieLocations->contains(old('location')))
                            <option value="{{ old('location') }}" selected>{{ old('location') }}</option>
                            @endif
                        </select>
                    </div>

                    {{-- Position --}}
                    <div class="form-group">
                        <label class="form-label">Position</label>
                        <select name="position" id="add_position" class="form-input modal-tag-select" data-placeholder="Type or select position">
                            <option value=""></option>
                            @foreach($walkiePositions as $position)
                            <option value="{{ $position }}" @selected(old('position') === $position)>{{ $position }}</option>
                            @endforeach
                            @if(old('position') && !$walkiePositions->contains(old('position')))
                            <option value="{{ old('position') }}" selected>{{ old('position') }}</option>
                            @endif
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Temporary / Swapped WT Radio ID</label>
                        <select name="temporary_radio_id" id="add_temporary_radio_id" class="form-input modal-tag-select" data-placeholder="Type or select temporary radio id">
                            <option value=""></option>
                            @foreach($walkieTemporaryIds as $temporaryRadioId)
                            <option value="{{ $temporaryRadioId }}" @selected(old('temporary_radio_id') === $temporaryRadioId)>{{ $temporaryRadioId }}</option>
                            @endforeach
                            @if(old('temporary_radio_id') && !$walkieTemporaryIds->contains(old('temporary_radio_id')))
                            <option value="{{ old('temporary_radio_id') }}" selected>{{ old('temporary_radio_id') }}</option>
                            @endif
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tracking REF</label>
                        <select name="tracking_ref" id="add_tracking_ref" class="form-input modal-tag-select" data-placeholder="Type or select tracking ref">
                            <option value=""></option>
                            @foreach($walkieTrackingRefs as $trackingRef)
                            <option value="{{ $trackingRef }}" @selected(old('tracking_ref') === $trackingRef)>{{ $trackingRef }}</option>
                            @endforeach
                            @if(old('tracking_ref') && !$walkieTrackingRefs->contains(old('tracking_ref')))
                            <option value="{{ old('tracking_ref') }}" selected>{{ old('tracking_ref') }}</option>
                            @endif
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Need To Change ID</label>
                        <select name="need_to_change_id" id="add_need_to_change_id" class="form-input modal-smart-select" data-placeholder="Search option">
                            @foreach($yesNoOptions as $option)
                            <option value="{{ $option['value'] }}" {{ old('need_to_change_id', '0') == $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">ID Change Done</label>
                        <select name="id_change_done" id="add_id_change_done" class="form-input modal-smart-select" data-placeholder="Search option">
                            @foreach($yesNoOptions as $option)
                            <option value="{{ $option['value'] }}" {{ old('id_change_done', '0') == $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Ownership Type To Be</label>
                        <select name="ownership_type_to_be" id="add_ownership_type_to_be" class="form-input modal-tag-select" data-placeholder="Type or search target ownership type">
                            <option value="">Select target ownership type...</option>
                            @foreach($ownershipTypeOptions as $ownershipTypeTarget)
                            <option value="{{ $ownershipTypeTarget }}" {{ old('ownership_type_to_be') == $ownershipTypeTarget ? 'selected' : '' }}>{{ $ownershipTypeTarget }}</option>
                            @endforeach
                            @if(old('ownership_type_to_be') && !$ownershipTypeOptions->contains(strtoupper(trim(old('ownership_type_to_be')))))
                            <option value="{{ strtoupper(trim(old('ownership_type_to_be'))) }}" selected>{{ strtoupper(trim(old('ownership_type_to_be'))) }}</option>
                            @endif
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Special Use</label>
                        <select name="is_special_use" id="add_is_special_use" class="form-input modal-smart-select" data-placeholder="Search option">
                            @foreach($yesNoOptions as $option)
                            <option value="{{ $option['value'] }}" {{ old('is_special_use', '0') == $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Returned</label>
                        <select name="special_use_returned" id="add_special_use_returned" class="form-input modal-smart-select" data-placeholder="Search option">
                            @foreach($yesNoOptions as $option)
                            <option value="{{ $option['value'] }}" {{ old('special_use_returned', '0') == $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Remark --}}
                    <div class="form-group" style="margin-top:10px;">
                        <label class="form-label">Remark</label>
                        <textarea name="remark" class="form-input" style="height:35px; resize:none;" placeholder="Additional notes...">{{ old('remark') }}</textarea>
                    </div>
                </div>

                {{-- Footer Buttons --}}
                <div class="modal-footer">
                    <button type="button" onclick="closeAddModal()" class="btn-cancel">Cancel</button>
                    <button type="submit" class="btn-submit">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="margin-right:6px;">
                            <path d="M8 1a.5.5 0 0 1 .5.5V7.5H14a.5.5 0 0 1 0 1H8.5V14a.5.5 0 0 1-1 0V8.5H2a.5.5 0 0 1 0-1h5.5V1.5A.5.5 0 0 1 8 1z"/>
                        </svg>
                        Save Unit
                    </button>
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
                    <p class="modal-subtitle" id="editModalSubtitle">Modify status and ownership details.</p>
                </div>
                <button onclick="closeEditModal()" class="modal-close-btn" title="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                    </svg>
                </button>
            </div>

            <form method="POST" id="editWalkieForm">
                @csrf
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Radio ID <span class="required">*</span></label>
                        <select name="radio_id" id="edit_radio_id" class="form-input modal-tag-select" data-placeholder="Type or select radio id" required>
                            <option value=""></option>
                            @foreach($walkieRadioIds as $radioId)
                            <option value="{{ $radioId }}">{{ $radioId }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Serial No. <span class="required">*</span></label>
                        <select name="serial_number" id="edit_serial_number" class="form-input modal-tag-select" data-placeholder="Type or select serial number" required>
                            <option value=""></option>
                            @foreach($walkieSerials as $serial)
                            <option value="{{ $serial }}">{{ $serial }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Model <span class="required">*</span></label>
                        <select name="model" id="edit_model" class="form-input modal-tag-select" data-placeholder="Type or select model" required>
                            <option value=""></option>
                            @foreach($walkieModels as $editModel)
                            <option value="{{ $editModel }}">{{ $editModel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status <span class="required">*</span></label>
                        <select name="status" id="edit_status" class="form-input modal-smart-select" data-placeholder="Search status" required>
                            @foreach($statusOptions as $editStatus)
                            <option value="{{ $editStatus }}">{{ $editStatus }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ownership Type <span class="required">*</span></label>
                        <select name="ownership_type" id="edit_ownership_type" class="form-input modal-tag-select ownership-type-control" data-placeholder="Type or search ownership type" required>
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
                        <select name="ownership" id="edit_ownership" class="form-input modal-smart-select" data-placeholder="Select staff">
                            <option value=""></option>
                            @foreach($staffOwnerships as $ownership)
                            <option value="{{ $ownership }}">{{ $ownership }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Position</label>
                        <select name="position" id="edit_position" class="form-input modal-tag-select" data-placeholder="Type or select position">
                            <option value=""></option>
                            @foreach($walkiePositions as $position)
                            <option value="{{ $position }}">{{ $position }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group form-group-full">
                        <label class="form-label">Department</label>
                        <select name="department" id="edit_department" class="form-input modal-tag-select" data-placeholder="Type or select department">
                            <option value=""></option>
                            @foreach($walkieDepartments as $department)
                            <option value="{{ $department }}">{{ $department }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Location</label>
                        <select name="location" id="edit_location" class="form-input modal-tag-select" data-placeholder="Type or select location">
                            <option value=""></option>
                            @foreach($walkieLocations as $location)
                            <option value="{{ $location }}">{{ $location }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Temporary / Swapped WT Radio ID</label>
                        <select name="temporary_radio_id" id="edit_temporary_radio_id" class="form-input modal-tag-select" data-placeholder="Type or select temporary radio id">
                            <option value=""></option>
                            @foreach($walkieTemporaryIds as $temporaryRadioId)
                            <option value="{{ $temporaryRadioId }}">{{ $temporaryRadioId }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tracking REF</label>
                        <select name="tracking_ref" id="edit_tracking_ref" class="form-input modal-tag-select" data-placeholder="Type or select tracking ref">
                            <option value=""></option>
                            @foreach($walkieTrackingRefs as $trackingRef)
                            <option value="{{ $trackingRef }}">{{ $trackingRef }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Need To Change ID</label>
                        <select name="need_to_change_id" id="edit_need_to_change_id" class="form-input modal-smart-select" data-placeholder="Search option">
                            @foreach($yesNoOptions as $option)
                            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">ID Change Done</label>
                        <select name="id_change_done" id="edit_id_change_done" class="form-input modal-smart-select" data-placeholder="Search option">
                            @foreach($yesNoOptions as $option)
                            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ownership Type To Be</label>
                        <select name="ownership_type_to_be" id="edit_ownership_type_to_be" class="form-input modal-tag-select" data-placeholder="Type or search target ownership type">
                            <option value="">Select target ownership type...</option>
                            @foreach($ownershipTypeOptions as $targetOwnershipType)
                            <option value="{{ $targetOwnershipType }}">{{ $targetOwnershipType }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Special Use</label>
                        <select name="is_special_use" id="edit_is_special_use" class="form-input modal-smart-select" data-placeholder="Search option">
                            @foreach($yesNoOptions as $option)
                            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Returned</label>
                        <select name="special_use_returned" id="edit_special_use_returned" class="form-input modal-smart-select" data-placeholder="Search option">
                            @foreach($yesNoOptions as $option)
                            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-section-title form-group-full">Warranty Information</div>
                    <div class="form-group">
                        <label class="form-label">WT Warranty Start Date</label>
                        <input type="date" name="wt_warranty_start_date" id="edit_wt_warranty_start_date" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">WT Warranty End Date</label>
                        <input type="date" name="wt_warranty_end_date" id="edit_wt_warranty_end_date" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Battery Warranty Start Date</label>
                        <input type="date" name="battery_warranty_start_date" id="edit_battery_warranty_start_date" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Battery Warranty End Date</label>
                        <input type="date" name="battery_warranty_end_date" id="edit_battery_warranty_end_date" class="form-input">
                    </div>
                    <div class="form-group form-group-full">
                        <label class="form-label">Remarks</label>
                        <textarea name="remark" id="edit_remark" class="form-input" rows="3" placeholder="Remarks"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeEditModal()" class="btn-cancel">Cancel</button>
                    <button type="submit" class="btn-submit">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @include('wt.admin.partials.inventory-management-ui')

    <script>
        const walkieTimelineData = @json($walkieTimelines ?? []);
        const inventoryActionData = @json($walkieActions ?? []);

        window.setTimeout(() => {
            document.documentElement.classList.add('inventory-page-ready');
        }, 1200);

        function timelineEscape(value) {
            return String(value ?? '').replace(/[&<>"']/g, function (character) {
                return {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;',
                }[character];
            });
        }

        function openWalkieTimeline(walkieId) {
            const modal = document.getElementById('walkieTimelineModal');
            const title = document.getElementById('timelineTitle');
            const subtitle = document.getElementById('timelineSubtitle');
            const summaryHost = document.getElementById('timelineSummary');
            const bodyHost = document.getElementById('timelineBody');
            const timeline = walkieTimelineData[String(walkieId)] || {};
            const summary = timeline.summary || {};
            const events = Array.isArray(timeline.events) ? timeline.events : [];

            if (!modal || !title || !subtitle || !summaryHost || !bodyHost) return;

            title.textContent = summary.radio_id || '-';
            subtitle.textContent = `${summary.model || '-'} · ${summary.serial_number || '-'} · ${summary.status || 'UNKNOWN'}`;

            const summaryItems = [
                ['Owner', summary.ownership || '-'],
                ['Department', summary.department || '-'],
                ['Location', summary.location || '-'],
                ['Status', summary.status || 'UNKNOWN'],
            ];

            summaryHost.innerHTML = summaryItems.map(([label, value]) => `
                <div class="walkie-timeline-summary-item">
                    <div class="walkie-timeline-summary-label">${timelineEscape(label)}</div>
                    <div class="walkie-timeline-summary-value" title="${timelineEscape(value)}">${timelineEscape(value)}</div>
                </div>
            `).join('');

            if (events.length === 0) {
                bodyHost.innerHTML = '<div class="walkie-timeline-empty">No timeline records found for this unit yet.</div>';
            } else {
                bodyHost.innerHTML = `
                    <div class="walkie-timeline-list">
                        ${events.map((event) => `
                            <div class="walkie-timeline-row">
                                <div class="walkie-timeline-date">
                                    ${timelineEscape(event.date || '-')}
                                    <span class="walkie-timeline-time">${timelineEscape(event.time || '')}</span>
                                </div>
                                <div class="walkie-timeline-dot ${timelineEscape(event.type || 'info')}"></div>
                                <div class="walkie-timeline-card">
                                    <p class="walkie-timeline-event-title">${timelineEscape(event.title || 'Activity')}</p>
                                    <p class="walkie-timeline-event-detail">${timelineEscape(event.detail || '-')}</p>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                `;
            }

            modal.classList.add('active');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function closeWalkieTimeline() {
            const modal = document.getElementById('walkieTimelineModal');
            if (!modal) return;
            modal.classList.remove('active');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        function closeWalkieTimelineOutside(event) {
            if (event.target === document.getElementById('walkieTimelineModal')) {
                closeWalkieTimeline();
            }
        }

        let currentWalkieQr = null;
        let currentActionDropdown = null;

        function restoreInventoryActionMenu(dropdown) {
            if (!dropdown || !dropdown._actionMenu || !dropdown._actionPlaceholder) return;

            dropdown._actionMenu.classList.remove('is-portal');
            dropdown._actionMenu.removeAttribute('style');
            dropdown.insertBefore(dropdown._actionMenu, dropdown._actionPlaceholder);
            dropdown._actionPlaceholder.remove();
            dropdown._actionMenu = null;
            dropdown._actionPlaceholder = null;
        }

        function closeInventoryActionMenus(exceptMenu = null) {
            document.querySelectorAll('.inventory-action-dropdown.is-open').forEach((menu) => {
                if (menu !== exceptMenu) {
                    menu.classList.remove('is-open');
                    menu.querySelector('.inventory-action-toggle')?.setAttribute('aria-expanded', 'false');
                    restoreInventoryActionMenu(menu);
                }
            });
        }

        function toggleInventoryActionMenu(event, button) {
            event.stopPropagation();
            const dropdown = button.closest('.inventory-action-dropdown');
            if (!dropdown) return;

            const shouldOpen = !dropdown.classList.contains('is-open');
            closeInventoryActionMenus(dropdown);
            dropdown.classList.toggle('is-open', shouldOpen);
            button.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');

            if (!shouldOpen) {
                restoreInventoryActionMenu(dropdown);
                currentActionDropdown = null;
                return;
            }

            const menu = dropdown.querySelector('.inventory-action-menu');
            if (!menu) return;

            const rect = button.getBoundingClientRect();
            const placeholder = document.createComment('inventory-action-menu');
            dropdown.insertBefore(placeholder, menu);
            document.body.appendChild(menu);
            menu.classList.add('is-portal');
            menu.onclick = (menuEvent) => menuEvent.stopPropagation();
            menu.style.minWidth = `${Math.max(rect.width, 176)}px`;
            menu.style.right = 'auto';
            menu.style.left = `${Math.max(8, Math.min(rect.right - 176, window.innerWidth - 188))}px`;
            menu.style.top = `${Math.max(8, Math.min(rect.bottom + 6, window.innerHeight - menu.offsetHeight - 12))}px`;
            dropdown._actionMenu = menu;
            dropdown._actionPlaceholder = placeholder;
            currentActionDropdown = dropdown;
        }

        function decodeWalkieQrPayload(payload) {
            try {
                const bytes = Uint8Array.from(atob(payload), (character) => character.charCodeAt(0));
                return JSON.parse(new TextDecoder().decode(bytes));
            } catch (error) {
                return null;
            }
        }

        function buildWalkieQrText(walkie) {
            return [
                'WT SYSTEM WALKIE TALKIE',
                `WT ID: ${walkie.walkie_id || '-'}`,
                `Radio ID: ${walkie.radio_id || '-'}`,
                `Serial No: ${walkie.serial_number || '-'}`,
                `Model: ${walkie.model || '-'}`,
                `Status: ${walkie.status || '-'}`,
                `Ownership Type: ${walkie.ownership_type || '-'}`,
                `Owner: ${walkie.owner || '-'}`,
                `Department: ${walkie.department || '-'}`,
                `Location: ${walkie.location || '-'}`,
                `URL: ${walkie.url || window.location.href}`,
            ].join('\n');
        }

        function renderWalkieQrDetails(walkie) {
            const detailHost = document.getElementById('walkieQrDetails');
            if (!detailHost) return;

            const items = [
                ['Radio ID', walkie.radio_id || '-'],
                ['Serial No', walkie.serial_number || '-'],
                ['Model', walkie.model || '-'],
                ['Status', walkie.status || '-'],
                ['Owner', walkie.owner || '-'],
                ['Department', walkie.department || '-'],
            ];

            detailHost.innerHTML = items.map(([label, value]) => `
                <div class="walkie-qr-detail">
                    <span>${timelineEscape(label)}</span>
                    <strong title="${timelineEscape(value)}">${timelineEscape(value)}</strong>
                </div>
            `).join('');
        }

        function openWalkieQrFromButton(button) {
            closeInventoryActionMenus();
            const walkie = decodeWalkieQrPayload(button?.dataset?.qrPayload || '');
            if (!walkie) return;
            openWalkieQr(walkie);
        }

        function openWalkieQr(walkie) {
            const modal = document.getElementById('walkieQrModal');
            const title = document.getElementById('walkieQrTitle');
            const subtitle = document.getElementById('walkieQrSubtitle');
            const qrHost = document.getElementById('walkieQrCanvas');
            const fallback = document.getElementById('walkieQrFallback');

            if (!modal || !title || !subtitle || !qrHost || !fallback) return;

            currentWalkieQr = {
                ...walkie,
                text: buildWalkieQrText(walkie),
            };

            title.textContent = walkie.radio_id || '-';
            subtitle.textContent = `${walkie.model || '-'} | ${walkie.serial_number || '-'} | ${walkie.status || 'UNKNOWN'}`;
            renderWalkieQrDetails(walkie);

            qrHost.innerHTML = '';
            fallback.style.display = 'none';
            fallback.textContent = '';

            if (typeof QRCode === 'undefined') {
                fallback.style.display = 'block';
                fallback.textContent = 'QR generator is not loaded. Unit details are still shown below.';
            } else {
                new QRCode(qrHost, {
                    text: currentWalkieQr.text,
                    width: 196,
                    height: 196,
                    colorDark: '#0f172a',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.M,
                });
            }

            modal.classList.add('active');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function closeWalkieQr() {
            const modal = document.getElementById('walkieQrModal');
            if (!modal) return;
            modal.classList.remove('active');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        function closeWalkieQrOutside(event) {
            if (event.target === document.getElementById('walkieQrModal')) {
                closeWalkieQr();
            }
        }

        function getWalkieQrImageSource() {
            const qrHost = document.getElementById('walkieQrCanvas');
            const canvas = qrHost?.querySelector('canvas');
            const image = qrHost?.querySelector('img');

            if (canvas) return canvas.toDataURL('image/png');
            if (image) return image.src;

            return '';
        }

        function downloadWalkieQr() {
            if (!currentWalkieQr) return;
            const source = getWalkieQrImageSource();
            if (!source) return;

            const link = document.createElement('a');
            const radioId = String(currentWalkieQr.radio_id || currentWalkieQr.walkie_id || 'walkie').replace(/[^a-z0-9_-]+/gi, '-');
            link.href = source;
            link.download = `walkie-qr-${radioId}.png`;
            link.click();
        }

        function printWalkieQr() {
            if (!currentWalkieQr) return;

            const source = getWalkieQrImageSource();
            const details = timelineEscape(currentWalkieQr.text).replace(/\n/g, '<br>');
            const printWindow = window.open('', '_blank', 'width=520,height=680');
            if (!printWindow) return;

            printWindow.document.write(`
                <!doctype html>
                <html>
                <head>
                    <title>Walkie QR - ${timelineEscape(currentWalkieQr.radio_id || '-')}</title>
                    
                </head>
                <body>
                    <div class="label">
                        <h1>${timelineEscape(currentWalkieQr.radio_id || '-')}</h1>
                        ${source ? `<img src="${source}" alt="Walkie QR Code">` : ''}
                        <p>${details}</p>
                    </div>
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
        }

        function getVisibleInventoryRows() {
            return Array.from(document.querySelectorAll('#walkiesTable tbody .inventory-row'))
                .filter((row) => row.style.display !== 'none');
        }

        function getSelectedInventoryCheckboxes() {
            return Array.from(document.querySelectorAll('.inventory-row-checkbox:checked'));
        }

        function syncBulkActionState() {
            const selected = getSelectedInventoryCheckboxes();
            const selectedCount = document.getElementById('bulkSelectedCount');
            const applyBtn = document.getElementById('bulkApplyBtn');
            const selectAll = document.getElementById('bulkSelectAll');
            const bulkAction = document.getElementById('bulkActionSelect');
            const bulkStatus = document.getElementById('bulkStatusSelect');
            const bulkRemark = document.getElementById('bulkRemarkInput');
            const visibleCheckboxes = getVisibleInventoryRows()
                .map((row) => row.querySelector('.inventory-row-checkbox'))
                .filter(Boolean);

            if (selectedCount) selectedCount.textContent = selected.length;
            if (applyBtn) applyBtn.disabled = selected.length === 0 || !bulkAction?.value || (bulkAction.value === 'set_status' && !bulkStatus?.value);

            if (bulkStatus && bulkAction) {
                const needsStatus = bulkAction.value === 'set_status';
                bulkStatus.disabled = !needsStatus;
                bulkStatus.required = needsStatus;
                if (!needsStatus) bulkStatus.value = '';
            }

            if (bulkRemark && bulkAction) {
                const canRemark = selected.length > 0 && Boolean(bulkAction.value);
                bulkRemark.disabled = !canRemark;
                if (!canRemark) bulkRemark.value = '';
            }

            if (selectAll) {
                const visibleSelected = visibleCheckboxes.filter((checkbox) => checkbox.checked).length;
                selectAll.checked = visibleCheckboxes.length > 0 && visibleSelected === visibleCheckboxes.length;
                selectAll.indeterminate = visibleSelected > 0 && visibleSelected < visibleCheckboxes.length;
            }
        }

        function bindInventoryBulkActions() {
            const selectAll = document.getElementById('bulkSelectAll');
            const bulkAction = document.getElementById('bulkActionSelect');
            const bulkStatus = document.getElementById('bulkStatusSelect');
            const bulkForm = document.getElementById('bulkActionForm');
            const selectedInputs = document.getElementById('bulkSelectedInputs');

            document.querySelectorAll('.inventory-row-checkbox').forEach((checkbox) => {
                checkbox.addEventListener('change', syncBulkActionState);
            });

            if (selectAll) {
                selectAll.addEventListener('change', function () {
                    getVisibleInventoryRows().forEach((row) => {
                        const checkbox = row.querySelector('.inventory-row-checkbox');
                        if (checkbox) checkbox.checked = this.checked;
                    });
                    syncBulkActionState();
                });
            }

            bulkAction?.addEventListener('change', syncBulkActionState);
            bulkStatus?.addEventListener('change', syncBulkActionState);

            bulkForm?.addEventListener('submit', function (event) {
                const selected = getSelectedInventoryCheckboxes();

                if (selected.length === 0) {
                    event.preventDefault();
                    syncBulkActionState();
                    return;
                }

                if (selectedInputs) {
                    selectedInputs.innerHTML = selected.map((checkbox) => (
                        `<input type="hidden" name="selected_ids[]" value="${checkbox.value}">`
                    )).join('');
                }
            });

            syncBulkActionState();
        }

        // ===== Basic Inventory Filtering =====

        function ensureSelectOption(selectId, value) {
            const normalizedValue = (value || '').toString().trim().toUpperCase();
            const select = document.getElementById(selectId);

            if (!select || normalizedValue === '') {
                if (select) {
                    select.value = '';
                    $(select).trigger('change');
                }
                return;
            }

            const hasOption = Array.from(select.options).some(option => option.value === normalizedValue);

            if (!hasOption) {
                select.add(new Option(normalizedValue, normalizedValue, true, true));
            }

            select.value = normalizedValue;
            $(select).trigger('change');
        }

        $(document).ready(function() {
            function focusOpenSelect2Search() {
                window.setTimeout(function () {
                    const searchField = document.querySelector('.select2-container--open .select2-search__field');

                    if (searchField) {
                        searchField.removeAttribute('readonly');
                        searchField.focus();
                    }
                }, 0);
            }

            function initModalSelects() {
                $('.modal-tag-select').each(function() {
                    const $select = $(this);

                    if ($select.hasClass('select2-hidden-accessible')) {
                        $select.select2('destroy');
                    }

                    $select.select2({
                        width: '100%',
                        tags: true,
                        allowClear: !$select.prop('required'),
                        placeholder: $select.data('placeholder') || 'Type or select option',
                        dropdownParent: $select.closest('.modal-box'),
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

                    $select.off('select2:open.modalFocus').on('select2:open.modalFocus', focusOpenSelect2Search);
                });

                $('.modal-smart-select').each(function() {
                    const $select = $(this);

                    if ($select.hasClass('select2-hidden-accessible')) {
                        $select.select2('destroy');
                    }

                    $select.select2({
                        width: '100%',
                        allowClear: !$select.prop('required'),
                        placeholder: $select.data('placeholder') || 'Search option',
                        dropdownParent: $select.closest('.modal-box')
                    });

                    $select.off('select2:open.modalFocus').on('select2:open.modalFocus', focusOpenSelect2Search);
                });
            }

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

            initModalSelects();

            $('.ownership-type-control')
                .off('change.sharedWith select2:select.sharedWith')
                .on('change.sharedWith select2:select.sharedWith', function() {
                    syncSharedWith($(this));
                })
                .each(function() {
                    syncSharedWith($(this));
                });

            const table = document.getElementById('walkiesTable');
            if (!table) return;

            const actionHeader = table.querySelector('thead th.inventory-action-col');
            table.style.minWidth = '0';
            if (actionHeader) {
                actionHeader.removeAttribute('data-label');
                actionHeader.innerHTML = '';
                actionHeader.style.position = 'static';
                actionHeader.style.right = 'auto';
                actionHeader.style.left = 'auto';
                actionHeader.style.width = '360px';
                actionHeader.style.minWidth = '360px';
                actionHeader.style.maxWidth = '360px';
                actionHeader.style.overflow = 'visible';
            }

            document.querySelectorAll('#walkiesTable_wrapper').forEach((wrapper) => {
                const originalTable = wrapper.querySelector('#walkiesTable');
                if (originalTable && wrapper.parentElement) {
                    wrapper.parentElement.insertBefore(originalTable, wrapper);
                    wrapper.remove();
                }
            });

            const searchInput = document.getElementById('globalSearch');
            const statusFilter = document.getElementById('filterStatus');
            const resetBtn = document.getElementById('resetFilters');
            const rows = Array.from(document.querySelectorAll('#walkiesTable tbody .inventory-row'));
            const tableScroll = document.getElementById('inventoryTableScroll');

            let currentPage = 1;
            const itemsPerPage = 10;
            const maxVisiblePages = 4;
            let filteredRows = [];

            function escapeInventoryAttribute(value) {
                return String(value ?? '').replace(/[&<>"']/g, function (character) {
                    return {
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#039;',
                    }[character];
                });
            }

            function renderInventoryActionCell(row) {
                const actionCell = row.querySelector('.inventory-action-col');
                if (!actionCell) return;

                actionCell.style.position = 'static';
                actionCell.style.right = 'auto';
                actionCell.style.left = 'auto';
                actionCell.style.width = '360px';
                actionCell.style.minWidth = '360px';
                actionCell.style.maxWidth = '360px';
                actionCell.style.overflow = 'visible';

                const walkieId = row.dataset.walkieId || '';
                const action = inventoryActionData[String(walkieId)] || {};

                if (!action.can_manage) {
                    actionCell.innerHTML = `
                        <button type="button" class="btn btn-info btn-sm" title="View Details" onclick="openGlobalWalkieTimeline('${escapeInventoryAttribute(walkieId)}')">
                            <i class="fa-solid fa-eye"></i>
                            <span>View</span>
                        </button>
                    `;
                    return;
                }

                actionCell.innerHTML = `
                    <div class="inventory-action-buttons">
                        <button type="button" class="btn btn-info btn-sm" title="View Details" onclick="openGlobalWalkieTimeline('${escapeInventoryAttribute(walkieId)}')">
                            <i class="fa-solid fa-eye"></i>
                            <span>View</span>
                        </button>

                        <a href="${escapeInventoryAttribute(action.edit_url || '#')}" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-edit"></i>
                            <span>Edit</span>
                        </a>

                        ${action.handover_url ? `
                            <form action="${escapeInventoryAttribute(action.handover_url)}" method="POST" class="d-inline" onsubmit="return confirm('Mark this unit as UNUSED after handover?');">
                                @csrf
                                <input type="hidden" name="status" value="UNUSED">
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fa-solid fa-handshake"></i>
                                    <span>Handover</span>
                                </button>
                            </form>
                        ` : `
                            <button type="button" class="btn btn-secondary btn-sm" disabled title="Only IN USE units show handover action">
                                <i class="fa-solid fa-handshake"></i>
                                <span>Handover</span>
                            </button>
                        `}

                        <form action="${escapeInventoryAttribute(action.delete_url || '#')}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this walkie-talkie record?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fa-solid fa-trash"></i>
                                <span>Delete</span>
                            </button>
                        </form>
                    </div>
                `;
            }

            function renderInventoryActionColumn(targetRows = rows) {
                targetRows.forEach(renderInventoryActionCell);
            }

            function syncInventoryHorizontalScroll() {
                if (!tableScroll) return;
                tableScroll.classList.toggle('has-horizontal-scroll', tableScroll.scrollWidth > tableScroll.clientWidth + 1);
            }

            if (tableScroll) {
                tableScroll.addEventListener('wheel', function (event) {
                    const maxScroll = Math.max(0, this.scrollWidth - this.clientWidth);
                    if (maxScroll === 0) return;

                    const delta = Math.abs(event.deltaX) > Math.abs(event.deltaY) ? event.deltaX : event.deltaY;
                    const nextScroll = Math.max(0, Math.min(maxScroll, this.scrollLeft + delta));

                    if (nextScroll !== this.scrollLeft) {
                        event.preventDefault();
                        this.scrollLeft = nextScroll;
                    }
                }, { passive: false });

                tableScroll.addEventListener('scroll', function () {
                    closeInventoryActionMenus();
                });

                window.addEventListener('resize', syncInventoryHorizontalScroll);
                if ('ResizeObserver' in window) {
                    new ResizeObserver(syncInventoryHorizontalScroll).observe(tableScroll);
                }
            }

            function renderPagination() {
                const paginationContainer = document.querySelector('.inventory-table-pagination');
                if (paginationContainer) paginationContainer.innerHTML = '';
                const infoTotal = document.getElementById('totalItems');
                if (infoTotal) infoTotal.innerText = filteredRows.length || rows.filter(row => row.style.display !== 'none').length;
            }

            function changePage(page) {
                currentPage = page;
                updateTableDisplay();
                renderPagination();
                const scrollContainer = document.getElementById('inventoryTableScroll');
                if (scrollContainer) scrollContainer.scrollTop = 0;
            }

            function updateTableDisplay() {
                rows.forEach(row => row.style.display = 'none');
                filteredRows.forEach(row => row.style.display = '');
                renderInventoryActionColumn(filteredRows);
            }

            function applyInventoryFilters() {
                const searchValue = (searchInput?.value || '').trim().toUpperCase();
                const statusFilterValue = (statusFilter?.value || '').trim().toUpperCase();

                filteredRows = rows.filter((row) => {
                    const matchesSearch = !searchValue || (row.dataset.search || '').includes(searchValue);
                    const matchesStatus = !statusFilterValue || row.dataset.status === statusFilterValue;
                    return matchesSearch && matchesStatus;
                });

                currentPage = 1;
                updateTableDisplay();
                renderPagination();
                syncBulkActionState();
                syncInventoryHorizontalScroll();
            }

            if (searchInput) {
                searchInput.addEventListener('input', applyInventoryFilters);
                searchInput.addEventListener('keyup', applyInventoryFilters);
                searchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        applyInventoryFilters();
                    }
                });
            }

            if (statusFilter) statusFilter.addEventListener('change', applyInventoryFilters);

            if (resetBtn) {
                resetBtn.addEventListener('click', function() {
                    if (searchInput) searchInput.value = '';
                    if (statusFilter) statusFilter.value = '';
                    applyInventoryFilters();
                });
            }

            applyInventoryFilters();
            renderInventoryActionColumn(rows);
            bindInventoryBulkActions();
            syncInventoryHorizontalScroll();
            document.documentElement.classList.add('inventory-page-ready');

            document.addEventListener('click', function(event) {
                if (!event.target.closest('.inventory-action-dropdown') && !event.target.closest('.inventory-action-menu')) {
                    closeInventoryActionMenus();
                }
            });
        });

        // ===== Modal Open / Close =====
        function openAddModal() {
            const modal = document.getElementById('addModal');
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
            $('.modal-tag-select, .modal-smart-select').trigger('change.select2');
        }

        function closeAddModal() {
            const modal = document.getElementById('addModal');
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }

        // ===== Import Modal Functions =====
        function openImportModal() {
            const modal = document.getElementById('importModal');
            if (modal && modal.parentElement !== document.body) {
                document.body.appendChild(modal);
            }
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        function closeImportModal() {
            const modal = document.getElementById('importModal');
            if (modal) {
                modal.classList.remove('active');
            }
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
                display.innerText = input.files[0].name;
                display.style.color = '#166534';
            }
        }

        function closeModalOutside(event) {
            if (event.target === document.getElementById('addModal')) {
                closeAddModal();
            }
        }

        function openEditModal(id, radio, serialNumber, model, status, ownershipType, ownership, position, department, location, temporaryRadioId, trackingRef, remark, needToChangeId, idChangeDone, ownershipTypeToBe, isSpecialUse, specialUseReturned, sharedWith, wtWarrantyStartDate, wtWarrantyEndDate, batteryWarrantyStartDate, batteryWarrantyEndDate) {
            const form = document.getElementById('editWalkieForm');
            form.action = "{{ route('wt.admin.walkies.updateMeta', ['walkie' => '__ID__']) }}".replace('__ID__', id);
            document.getElementById('editModalSubtitle').innerText = `Updating unit ${radio}`;
            ensureSelectOption('edit_radio_id', radio || '');
            ensureSelectOption('edit_serial_number', serialNumber || '');
            ensureSelectOption('edit_model', model || 'R7');
            ensureSelectOption('edit_status', status || 'UNUSED');
            ensureSelectOption('edit_ownership_type', ownershipType || 'UNALLOCATED');
            document.getElementById('edit_shared_with').value = sharedWith || '';
            ensureSelectOption('edit_ownership', ownership || '');
            ensureSelectOption('edit_position', position || '');
            ensureSelectOption('edit_department', department || '');
            ensureSelectOption('edit_location', location || '');
            ensureSelectOption('edit_temporary_radio_id', temporaryRadioId || '');
            ensureSelectOption('edit_tracking_ref', trackingRef || '');
            document.getElementById('edit_remark').value = remark || '';
            ensureSelectOption('edit_need_to_change_id', needToChangeId || '0');
            ensureSelectOption('edit_id_change_done', idChangeDone || '0');
            ensureSelectOption('edit_ownership_type_to_be', ownershipTypeToBe || '');
            ensureSelectOption('edit_is_special_use', isSpecialUse || '0');
            ensureSelectOption('edit_special_use_returned', specialUseReturned || '0');
            const wtWarrantyStart = document.getElementById('edit_wt_warranty_start_date');
            const wtWarrantyEnd = document.getElementById('edit_wt_warranty_end_date');
            const batteryWarrantyStart = document.getElementById('edit_battery_warranty_start_date');
            const batteryWarrantyEnd = document.getElementById('edit_battery_warranty_end_date');
            if (wtWarrantyStart) wtWarrantyStart.value = wtWarrantyStartDate || '';
            if (wtWarrantyEnd) wtWarrantyEnd.value = wtWarrantyEndDate || '';
            if (batteryWarrantyStart) batteryWarrantyStart.value = batteryWarrantyStartDate || '';
            if (batteryWarrantyEnd) batteryWarrantyEnd.value = batteryWarrantyEndDate || '';
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

        // ===== Toggle Maintenance Fields =====
        function toggleMaintenanceFields() {
            return;
        }

        // ===== Auto-dismiss success alert =====
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

        // ===== ESC key to close =====
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeInventoryActionMenus();
                closeWalkieTimeline();
                closeWalkieQr();
                closeImportModal();
                closeAddModal();
                closeEditModal();
            }
        });
    </script>

    <script>
        (function () {
            function paintInventoryTableTheme() {
                var isDark = document.documentElement.classList.contains('dark')
                    || document.documentElement.getAttribute('data-theme') === 'dark';
                var tableShell = document.getElementById('mainTableContainer');
                var table = document.getElementById('walkiesTable');
                var bulkBar = document.getElementById('bulkActionForm');

                var colors = isDark ? {
                    shellBg: '#0f172a',
                    shellBorder: '#273449',
                    headBg: '#111827',
                    headText: '#d7e7fb',
                    rowBg: '#0f172a',
                    rowText: '#e2e8f0',
                    rowBorder: '#273449',
                    chipBg: '#1f2937',
                    chipBorder: '#334155',
                    chipText: '#e2e8f0'
                } : {
                    shellBg: '#ffffff',
                    shellBorder: '#cbd5e1',
                    headBg: '#eef3f8',
                    headText: '#334155',
                    rowBg: '#ffffff',
                    rowText: '#1f2937',
                    rowBorder: '#e2e8f0',
                    chipBg: '#f1f5f9',
                    chipBorder: '#cbd5e1',
                    chipText: '#334155'
                };

                [tableShell, bulkBar].forEach(function (element) {
                    if (!element) return;
                    element.style.setProperty('background', colors.shellBg, 'important');
                    element.style.setProperty('border-color', colors.shellBorder, 'important');
                    element.style.setProperty('color', colors.rowText, 'important');
                });

                if (!table) return;
                table.style.setProperty('background', colors.shellBg, 'important');
                table.querySelectorAll('thead th').forEach(function (cell) {
                    cell.style.setProperty('background', colors.headBg, 'important');
                    cell.style.setProperty('border-color', colors.shellBorder, 'important');
                    cell.style.setProperty('color', colors.headText, 'important');
                });
                table.querySelectorAll('tbody tr, tbody, thead').forEach(function (row) {
                    row.style.setProperty('background', colors.rowBg, 'important');
                    row.style.setProperty('color', colors.rowText, 'important');
                });
                table.querySelectorAll('tbody td').forEach(function (cell) {
                    cell.style.setProperty('background', colors.rowBg, 'important');
                    cell.style.setProperty('border-color', colors.rowBorder, 'important');
                    cell.style.setProperty('color', colors.rowText, 'important');
                });
                table.querySelectorAll('.inventory-item-title, .inventory-remark-cell').forEach(function (text) {
                    text.style.setProperty('color', colors.rowText, 'important');
                });
                table.querySelectorAll('.inventory-id-chip, .inventory-type-badge').forEach(function (chip) {
                    chip.style.setProperty('background', colors.chipBg, 'important');
                    chip.style.setProperty('border-color', colors.chipBorder, 'important');
                    chip.style.setProperty('color', colors.chipText, 'important');
                });
            }

            document.addEventListener('DOMContentLoaded', paintInventoryTableTheme);
            window.addEventListener('load', paintInventoryTableTheme);
            new MutationObserver(paintInventoryTableTheme).observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['class', 'data-theme']
            });
            window.paintInventoryTableTheme = paintInventoryTableTheme;
        })();
    </script>

    @endsection
