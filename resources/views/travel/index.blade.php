@extends('layouts.app')

@section('title', 'My Travel')

@section('content')
<!-- Stats row -->
<div class="stats-row" style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:1rem;margin-bottom:1.5rem;">
    <div class="stat-card">
        <div class="stat-icon" style="background:#eff6ff;color:#2563eb;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.6 12 19.79 19.79 0 0 1 1.58 3.47 2 2 0 0 1 3.55 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.27a16 16 0 0 0 6.29 6.29l1.63-1.83a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 14.92z"/></svg>
        </div>
        <div class="stat-info">
            <span class="stat-value">{{ $totalTrips }}</span>
            <span class="stat-label">Total Trips</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#f0fdf4;color:#16a34a;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div class="stat-info">
            <span class="stat-value">{{ $uniqueStaff }}</span>
            <span class="stat-label">Staff Travelling</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fef3c7;color:#d97706;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div class="stat-info">
            <span class="stat-value">{{ $activeNow }}</span>
            <span class="stat-label">Currently Away</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#f5f3ff;color:#7c3aed;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div class="stat-info">
            <span class="stat-value">{{ $upcomingTrips }}</span>
            <span class="stat-label">Upcoming Trips</span>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:.4rem;vertical-align:-.15em;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
            {{ $isStaff ? 'My Travel' : 'Travel Records' }}
        </h3>
        @canwrite
        <button class="btn btn-primary btn-sm" data-requires-active onclick="openAddModal()">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Travel
        </button>
        @endcanwrite
    </div>

    <!-- Filters -->
    <div style="padding:.75rem 1.25rem;border-bottom:1px solid var(--border);display:flex;gap:.75rem;flex-wrap:wrap;align-items:center;">
        <form id="travel-filter-form" method="GET" action="{{ route('travel.index') }}" style="display:contents;">
            @if(!$isStaff)
            <div class="app-search">
                <svg class="app-search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="q" value="{{ $search }}" placeholder="Search staff, destination…">
            </div>
            <select name="dept" style="padding:.5rem .75rem;border:1.5px solid var(--border);border-radius:8px;font-size:.85rem;background:var(--form-input-bg);min-width:180px;">
                <option value="">All Departments</option>
                @foreach($departments as $d)
                <option value="{{ $d->id }}" {{ $dept_filter == $d->id ? 'selected' : '' }}>[{{ $d->company }}] {{ $d->name }}</option>
                @endforeach
            </select>
            @endif
            <input type="month" name="month" value="{{ $month_filter }}" style="padding:.5rem .75rem;border:1.5px solid var(--border);border-radius:8px;font-size:.85rem;background:var(--form-input-bg);">
            <button type="submit" class="btn btn-outline btn-sm">Filter</button>
            @if($search || $dept_filter || $month_filter || $staff_filter)
            <a href="{{ route('travel.index') }}" class="btn btn-ghost btn-sm">Clear</a>
            @endif
        </form>
    </div>

    <div id="travel-results">
    @if($travels->isEmpty())
    <div class="empty-state" style="padding:4rem 2rem;">
        <div style="font-size:3rem;color:var(--border);margin-bottom:1rem;">✈️</div>
        <p style="color:var(--muted);">No travel records found matching your filters.</p>
    </div>
    @else
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    @if(!$isStaff)
                    <th>Staff Member</th>
                    <th>Dept / Co</th>
                    @endif
                    <th>Destination</th>
                    <th>Purpose</th>
                    <th>Departure</th>
                    <th>Return</th>
                    <th style="text-align:center;">Duration</th>
                    <th>Transport</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($travels as $t)
                @php
                    $today_str = date('Y-m-d');
                    $status = 'upcoming';
                    $statusLabel = 'Upcoming';
                    $statusStyle = 'background:#f5f3ff;color:#7c3aed;';
                    if ($t->departure_date <= $today_str && $t->return_date >= $today_str) {
                        $status = 'active'; $statusLabel = 'Away'; $statusStyle = 'background:#fef3c7;color:#d97706;';
                    } elseif ($t->return_date < $today_str) {
                        $status = 'completed'; $statusLabel = 'Completed'; $statusStyle = 'background:#f0fdf4;color:#16a34a;';
                    }
                    $duration = \Carbon\Carbon::parse($t->departure_date)->diffInDays(\Carbon\Carbon::parse($t->return_date)) + 1;
                @endphp
                <tr>
                    @if(!$isStaff)
                    <td>
                        <div style="font-weight:600;line-height:1.3;">{{ $t->staff?->name ?? '—' }}</div>
                        <div style="font-size:.78rem;"><a href="{{ route('staff.show', $t->staff_id) }}" style="text-decoration:none;color:#6366f1;border-bottom:1px dashed #6366f1;">{{ $t->staff?->staff_no ?? '—' }}</a></div>
                    </td>
                    <td>
                        <span class="dept-badge">{{ $t->staff?->department?->name ?? '—' }}</span>
                        @if($t->staff?->department && $t->staff->department->company)
                        <div style="font-size:.75rem;color:var(--muted);margin-top:.2rem;">{{ $t->staff->department->company }}</div>
                        @endif
                    </td>
                    @endif
                    <td>
                        <div style="font-weight:500;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-.1em;margin-right:.25rem;color:var(--muted);"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            {{ $t->destination }}
                        </div>
                    </td>
                    <td style="max-width:160px;">
                        <span style="font-size:.85rem;color:var(--muted);" title="{{ $t->purpose }}">
                            {{ $t->purpose ? \Illuminate\Support\Str::limit($t->purpose, 35) : '—' }}
                        </span>
                    </td>
                    <td style="white-space:nowrap;">{{ \Carbon\Carbon::parse($t->departure_date)->format('d M Y') }}</td>
                    <td style="white-space:nowrap;">{{ \Carbon\Carbon::parse($t->return_date)->format('d M Y') }}</td>
                    <td style="text-align:center;">
                        <span style="font-weight:600;color:var(--navy);">{{ $duration }}</span>
                        <span style="font-size:.78rem;color:var(--muted);"> day{{ $duration != 1 ? 's' : '' }}</span>
                    </td>
                    <td style="font-size:.85rem;color:var(--muted);">{{ $t->transport ?: '—' }}</td>
                    <td>
                        <span class="status-badge" style="{{ $statusStyle }}font-size:.75rem;padding:.2rem .55rem;border-radius:6px;font-weight:600;">     
                            {{ $statusLabel }}
                        </span>
                    </td>
                    <td style="text-align:right;white-space:nowrap;">
                        @canwrite
                        <button class="btn btn-ghost btn-xs" onclick="editTravel({{ $t->id }})" title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </button>
                        <button class="btn btn-danger btn-xs" onclick="deleteTravel({{ $t->id }}, '{{ addslashes($t->staff?->name ?? 'this record') }}')" title="Delete">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                        </button>
                        @endcanwrite
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    </div>
</div>

<!-- ADD / EDIT MODAL -->
<div class="modal" id="travelModal">
    <div class="modal-box" style="max-width:560px;">
        <div class="modal-header">
            <h3 id="travelModalTitle">Add My Travel</h3>
            <button class="modal-close" onclick="closeModal()">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('travel.store') }}" id="travelForm">
            @csrf
            <div id="methodField"></div>
            <input type="hidden" name="travel_type" id="travel_type_input" value="Domestic">
            <input type="hidden" name="staff_id" id="travel_staff_id" value="">

            <!-- WIZARD STEP 1: Type -->
            <div id="wizardStep1" style="padding:1.5rem; text-align:center;">
                <h4 style="margin-bottom:1rem;font-size:1.1rem;">Where are you travelling to?</h4>
                <div style="display:flex;gap:1rem;justify-content:center;">
                    <div onclick="selectTravelType('Domestic')" style="flex:1;padding:2rem;border:2px solid var(--border);border-radius:12px;cursor:pointer;background:var(--surface);transition:.2s;" onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--border)'">
                        <div style="margin-bottom:.5rem;display:flex;justify-content:center;">
                            <img src="https://flagcdn.com/w80/my.png" alt="Malaysia Flag" width="48" style="border-radius:4px;box-shadow:0 1px 3px rgba(0,0,0,0.1);">
                        </div>
                        <div style="font-weight:600;font-size:1.1rem;">Domestic</div>
                        <div style="font-size:.8rem;color:var(--muted);margin-top:.2rem;">Within Malaysia</div>
                    </div>
                    <div onclick="selectTravelType('International')" style="flex:1;padding:2rem;border:2px solid var(--border);border-radius:12px;cursor:pointer;background:var(--surface);transition:.2s;" onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--border)'">
                        <div style="margin-bottom:.5rem;display:flex;justify-content:center;color:var(--primary);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                        </div>
                        <div style="font-weight:600;font-size:1.1rem;">International</div>
                        <div style="font-size:.8rem;color:var(--muted);margin-top:.2rem;">Outside Malaysia</div>
                    </div>
                </div>
            </div>

            <!-- WIZARD STEP 2: Map -->
            <div id="wizardStep2" style="display:none;padding:1.5rem;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.75rem;">
                    <button type="button" class="btn btn-ghost btn-sm" onclick="goToStep(1)">← Back</button>
                    <span style="font-weight:600;">Select Destination</span>
                </div>
                <div id="travelMap" style="width:100%;height:350px;border-radius:8px;border:1px solid var(--border);background:#e2e8f0;margin-bottom:1rem;z-index:1;"></div>
                <div style="display:flex;gap:.5rem;align-items:center;">
                    <input type="text" id="mapDestPreview" placeholder="Type a location to search or click the map..." style="flex:1;padding:.65rem 1rem;border:1.5px solid var(--border);border-radius:var(--radius);font-size:.875rem;background:white;">
                    <button type="button" id="mapSearchBtn" class="btn btn-outline" onclick="searchMapLocation()" style="font-weight:600;padding:.65rem 1.25rem;">Search</button>
                    <button type="button" id="mapProceedBtn" class="btn btn-primary" onclick="goToStep(3)" disabled style="padding:.65rem 1.25rem;">Proceed →</button>
                </div>
            </div>

            <!-- WIZARD STEP 3: Details -->
            <div id="wizardStep3" style="display:none;padding:1.5rem;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:1px solid var(--border);">
                    <div>
                        <span id="finalTypeBadge" style="display:inline-block;padding:.2rem .5rem;background:var(--primary);color:#fff;border-radius:4px;font-size:.7rem;font-weight:600;margin-bottom:.3rem;text-transform:uppercase;"></span>
                        <div style="font-weight:600;font-size:1.1rem;display:flex;align-items:center;gap:.5rem;">
                            📍 <span id="finalDestText"></span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline btn-sm" onclick="goToStep(2)">Edit Location</button>
                </div>
                
                <div class="form-grid">
                <!-- Staff selection (admin only) -->
                @if(!$isStaff)
                <div class="form-group form-full" id="staffSearchGroup">
                    <label>Staff Member <span style="color:var(--danger)">*</span></label>
                    <div style="display:flex;gap:.4rem;margin-bottom:.6rem;">
                        <button type="button" id="tabSearch" onclick="switchStaffTab('search')" style="flex:1;padding:.42rem .75rem;font-size:.78rem;font-weight:600;border-radius:7px;border:1.5px solid var(--primary);background:var(--primary);color:#fff;cursor:pointer;transition:.15s;">🔍 Search</button>
                        <button type="button" id="tabDept" onclick="switchStaffTab('dept')" style="flex:1;padding:.42rem .75rem;font-size:.78rem;font-weight:600;border-radius:7px;border:1.5px solid var(--border);background:var(--surface);color:var(--text);cursor:pointer;transition:.15s;">🏢 Browse</button>
                    </div>

                    <div id="panelSearch">
                        <div style="position:relative;">
                            <svg style="position:absolute;left:.75rem;top:50%;transform:translateY(-50%);color:var(--muted);pointer-events:none;" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            <input type="text" id="staffSearchInput" placeholder="Type name or staff ID…" autocomplete="off" style="width:100%;padding:.65rem 1rem .65rem 2.2rem;border:1.5px solid var(--border);border-radius:var(--radius);font-size:.875rem;">
                        </div>
                        <div id="staffDropdown" style="display:none;background:white;border:1px solid var(--border);border-radius:8px;box-shadow:var(--shadow);z-index:200;max-height:200px;overflow-y:auto;margin-top:.3rem;"></div>
                    </div>

                    <div id="panelDept" style="display:none;">
                        <select id="deptPickerSelect" style="width:100%;padding:.65rem 1rem;border:1.5px solid var(--border);border-radius:var(--radius);font-size:.875rem;background:white;margin-bottom:.4rem;">
                            <option value="">— Select Department —</option>
                            @foreach($departments as $d)
                            <option value="{{ $d->id }}">[{{ $d->company }}] {{ $d->name }}</option>       
                            @endforeach
                        </select>
                        <div id="deptStaffList" style="display:none;background:white;border:1px solid var(--border);border-radius:8px;max-height:190px;overflow-y:auto;"></div>
                    </div>

                    <div id="selectedStaffDisplay" style="display:none;margin-top:.5rem;padding:.55rem .85rem;background:#f0f9ff;border-radius:8px;font-size:.85rem;flex-direction:row;align-items:center;justify-content:space-between;">
                        <div>
                            <span id="selectedStaffName" style="font-weight:600;"></span>
                            <span id="selectedStaffMeta" style="color:var(--muted);font-size:.78rem;margin-left:.4rem;"></span>
                        </div>
                        <button type="button" onclick="clearStaffSelection()" title="Change staff" style="background:none;border:none;cursor:pointer;color:var(--muted);font-size:1.15rem;line-height:1;padding:0 .2rem;">×</button>
                    </div>
                </div>
                @endif

                <div class="form-group form-full" style="display:none;">
                    <label>Destination <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="destination" id="travel_destination" placeholder="e.g. Kuala Lumpur, Malaysia" required style="width:100%;padding:.65rem 1rem;border:1.5px solid var(--border);border-radius:var(--radius);font-size:.875rem;">      
                </div>
                <div class="form-group form-full">
                    <label>Purpose / Event</label>
                    <input type="text" name="purpose" id="travel_purpose" placeholder="e.g. Client meeting, Conference" style="width:100%;padding:.65rem 1rem;border:1.5px solid var(--border);border-radius:var(--radius);font-size:.875rem;">      
                </div>
                <div class="form-group">
                    <label>Departure Date <span style="color:var(--danger)">*</span></label>
                    <input type="date" name="departure_date" id="travel_departure" required style="width:100%;padding:.65rem 1rem;border:1.5px solid var(--border);border-radius:var(--radius);font-size:.875rem;">      
                </div>
                <div class="form-group">
                    <label>Return Date <span style="color:var(--danger)">*</span></label>
                    <input type="date" name="return_date" id="travel_return" required style="width:100%;padding:.65rem 1rem;border:1.5px solid var(--border);border-radius:var(--radius);font-size:.875rem;">      
                </div>
                <div class="form-full" id="durationPreview" style="display:none;padding:.5rem .75rem;background:var(--bg);border-radius:8px;font-size:.85rem;color:var(--muted);text-align:center;"></div>
                <div class="form-group form-full">
                    <label>Mode of Transport</label>
                    <select name="transport" id="travel_transport" onchange="toggleTransportOther()" style="width:100%;padding:.65rem 1rem;border:1.5px solid var(--border);border-radius:var(--radius);font-size:.875rem;background:white;">
                        <option value="">— Select —</option>
                        @foreach($transportModes as $tm)
                        <option value="{{ $tm->name }}">{{ $tm->name }}</option>
                        @endforeach
                        <option value="Others">Others</option>
                    </select>
                    <input type="text" id="travel_transport_other" placeholder="Please specify your mode of transport..." style="display:none;width:100%;padding:.65rem 1rem;border:1.5px solid var(--border);border-radius:var(--radius);font-size:.875rem;margin-top:.5rem;">
                </div>
                <div class="form-group form-full">
                    <label>Notes</label>
                    <textarea name="notes" id="travel_notes" rows="2" placeholder="Any additional info…" style="width:100%;padding:.65rem 1rem;border:1.5px solid var(--border);border-radius:var(--radius);font-size:.875rem;resize:vertical;"></textarea>
                </div>
                </div>
            </div> <!-- End Wizard Step 3 -->
            
            <div class="modal-footer" id="wizardFooter" style="display:none;">
                <button type="button" class="btn btn-outline btn-sm" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary btn-sm" id="travelSubmitBtn">Save Travel Record</button>
            </div>
        </form>
    </div>
</div>

<!-- DELETE CONFIRM MODAL -->
<div class="modal" id="deleteModal">
    <div class="modal-box modal-sm">
        <div class="modal-header">
            <h3>Delete Travel Record</h3>
            <button class="modal-close" onclick="closeModal()">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div style="padding:1.25rem 1.5rem;">
            <p id="deleteConfirmText" style="color:var(--muted);font-size:.9rem;"></p>
        </div>
        <form method="POST" action="" id="deleteTravelForm">
            @csrf
            @method('DELETE')
            <div class="modal-footer">
                <button type="button" class="btn btn-outline btn-sm" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
const IS_STAFF_VIEW = {{ $isStaff ? 'true' : 'false' }};

function openAddModal() {
    document.getElementById('travelModalTitle').textContent = 'Add My Travel';
    const form = document.getElementById('travelForm');
    form.action = "{{ route('travel.store') }}";
    form.reset();
    document.getElementById('methodField').innerHTML = '';
    document.getElementById('travelSubmitBtn').textContent = 'Save Travel Record';
    if (!IS_STAFF_VIEW) {
        document.getElementById('travel_staff_id').value = '';
        clearStaffSelection();
    }
    document.getElementById('durationPreview').style.display = 'none';
    openModal('travelModal');
}
// Modification to openAddModal to start at wizard step 1
const originalOpenAddModal = openAddModal;
window.openAddModal = function() {
    originalOpenAddModal();
    goToStep(1); // Start wizard from beginning
};

async function editTravel(id) {
    const res  = await fetch("{{ url('travel') }}/" + id);
    const data = await res.json();
    if (!data || !data.id) return alert('Could not load record.');

    document.getElementById('travelModalTitle').textContent = 'Edit Travel Record';
    const form = document.getElementById('travelForm');
    form.action = "{{ url('travel') }}/" + id;
    document.getElementById('methodField').innerHTML = '@method("PUT")';
    
    document.getElementById('travel_staff_id').value   = data.staff_id;
    document.getElementById('travel_type_input').value = data.travel_type ?? 'Domestic';
    document.getElementById('travel_destination').value= data.destination;
    document.getElementById('travel_purpose').value    = data.purpose ?? '';
    document.getElementById('travel_departure').value  = data.departure_date;
    document.getElementById('travel_return').value     = data.return_date;
    
    const ts = document.getElementById('travel_transport');
    const to = document.getElementById('travel_transport_other');
    const dbTransport = data.transport ?? '';
    
    let optionExists = false;
    for (let i = 0; i < ts.options.length; i++) {
        if (ts.options[i].value === dbTransport) {
            optionExists = true;
            break;
        }
    }
    
    if (dbTransport && !optionExists) {
        ts.value = 'Others';
        to.value = dbTransport;
        to.style.display = 'block';
    } else {
        ts.value = dbTransport;
        to.style.display = 'none';
        to.value = '';
    }
    
    document.getElementById('travel_notes').value      = data.notes ?? '';
    document.getElementById('travelSubmitBtn').textContent = 'Update Record';

    document.getElementById('finalDestText').textContent = data.destination;
    document.getElementById('finalTypeBadge').textContent = (data.travel_type ?? 'Domestic') + " (Edit)";

    if (!IS_STAFF_VIEW) {
        const staffName = data.staff ? data.staff.name : 'Unknown Staff';
        const staffNo   = data.staff ? data.staff.staff_no : '';
        showSelectedStaff(data.staff_id, staffName, staffNo);
    }
    updateDurationPreview();
    openModal('travelModal');
    goToStep(3);
}

function deleteTravel(id, name) {
    const form = document.getElementById('deleteTravelForm');
    form.action = "{{ url('travel') }}/" + id;
    document.getElementById('deleteConfirmText').textContent = `Are you sure you want to delete the travel record for ${name}? This cannot be undone.`;
    openModal('deleteModal');
}

function switchStaffTab(tab) {
    const isSearch = tab === 'search';
    document.getElementById('panelSearch').style.display = isSearch ? '' : 'none';
    document.getElementById('panelDept').style.display   = isSearch ? 'none' : '';

    const tS = document.getElementById('tabSearch');
    const tD = document.getElementById('tabDept');
    if (isSearch) {
        tS.style.background = 'var(--primary)'; tS.style.color = '#fff'; tS.style.borderColor = 'var(--primary)';
        tD.style.background = '#fff'; tD.style.color = 'var(--text)'; tD.style.borderColor = 'var(--border)';
    } else {
        tD.style.background = 'var(--primary)'; tD.style.color = '#fff'; tD.style.borderColor = 'var(--primary)';
        tS.style.background = '#fff'; tS.style.color = 'var(--text)'; tS.style.borderColor = 'var(--border)';
    }
}

let staffSearchTimeout;
const staffInput = document.getElementById('staffSearchInput');
const staffDropdown = document.getElementById('staffDropdown');

if (staffInput) {
    staffInput.addEventListener('input', function() {
        clearTimeout(staffSearchTimeout);
        const q = this.value.trim();
        if (q.length < 2) { staffDropdown.style.display = 'none'; return; }
        staffSearchTimeout = setTimeout(() => fetchStaff(q), 280);
    });
}

async function fetchStaff(q) {
    const res = await fetch(`{{ route('users.search_staff') }}?search_staff=${encodeURIComponent(q)}`);
    const list = await res.json();
    renderStaffItems(list, staffDropdown);
    staffDropdown.style.display = 'block';
}

function renderStaffItems(list, container) {
    container.innerHTML = '';
    if (!list.length) {
        container.innerHTML = '<div style="padding:.65rem 1rem;color:var(--muted);font-size:.85rem;">No staff found</div>';
        return;
    }
    list.forEach(s => {
        const div = document.createElement('div');
        div.style.cssText = 'padding:.55rem 1rem;cursor:pointer;font-size:.875rem;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:.5rem;';
        div.innerHTML = `
            <div style="width:30px;height:30px;border-radius:50%;background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:.72rem;font-weight:700;flex-shrink:0;">
                ${s.name.charAt(0).toUpperCase()}
            </div>
            <div>
                <div style="font-weight:600;">${s.name}</div>
                <div style="font-size:.76rem;color:var(--muted);">${s.staff_no} · ${s.dept_name ?? '—'}</div>
            </div>`;
        div.addEventListener('mouseenter', () => div.style.background = '#f0f9ff');
        div.addEventListener('mouseleave', () => div.style.background = '');
        div.addEventListener('click', () => {
            showSelectedStaff(s.id, s.name, `${s.staff_no} · ${s.dept_name ?? ''}`);
            staffDropdown.style.display = 'none';
            if (document.getElementById('deptStaffList')) document.getElementById('deptStaffList').style.display = 'none';
        });
        container.appendChild(div);
    });
}

const deptSelect = document.getElementById('deptPickerSelect');
const deptStaffList = document.getElementById('deptStaffList');

if (deptSelect) {
    deptSelect.addEventListener('change', async function() {
        const deptId = this.value;
        if (!deptId) { deptStaffList.style.display = 'none'; return; }
        deptStaffList.innerHTML = '<div style="padding:.65rem 1rem;color:var(--muted);font-size:.85rem;">Loading…</div>';
        deptStaffList.style.display = 'block';
        const res = await fetch("{{ url('master-data/staff-list') }}/" + deptId);
        const list = await res.json();
        renderStaffItems(list, deptStaffList);
    });
}

function showSelectedStaff(id, name, meta) {
    document.getElementById('travel_staff_id').value = id;
    document.getElementById('selectedStaffName').textContent = name;
    document.getElementById('selectedStaffMeta').textContent = meta ?? '';
    document.getElementById('selectedStaffDisplay').style.display = 'flex';
    document.getElementById('panelSearch').style.display = 'none';
    document.getElementById('panelDept').style.display = 'none';
    document.querySelectorAll('#tabSearch,#tabDept').forEach(b => b.style.display = 'none');
}

function clearStaffSelection() {
    document.getElementById('travel_staff_id').value = '';
    document.getElementById('selectedStaffDisplay').style.display = 'none';
    document.querySelectorAll('#tabSearch,#tabDept').forEach(b => b.style.display = '');
    switchStaffTab('search');
    if (staffInput) staffInput.value = '';
    if (staffDropdown) staffDropdown.style.display = 'none';
    if (deptSelect) deptSelect.value = '';
    if (deptStaffList) deptStaffList.style.display = 'none';
}

function updateDurationPreview() {
    const dep = document.getElementById('travel_departure').value;
    const ret = document.getElementById('travel_return').value;
    const el  = document.getElementById('durationPreview');
    if (dep && ret && ret >= dep) {
        const d1 = new Date(dep), d2 = new Date(ret);
        const days = Math.round((d2 - d1) / 86400000) + 1;
        el.textContent = `📅 Duration: ${days} day${days !== 1 ? 's' : ''}`;
        el.style.display = 'block';
    } else {
        el.style.display = 'none';
    }
}

document.getElementById('travel_departure').addEventListener('change', updateDurationPreview);
document.getElementById('travel_return').addEventListener('change', updateDurationPreview);
</script>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
let travelMap = null;
let mapMarker = null;

function initTravelMap(type) {
    if (travelMap) {
        travelMap.remove();
        travelMap = null;
        mapMarker = null;
    }

    const initialZoom = type === 'Domestic' ? 6 : 2;
    const initialCenter = type === 'Domestic' ? [4.2105, 101.9758] : [20, 0];

    let mapOptions = {
        center: initialCenter,
        zoom: initialZoom,
    };
    
    if (type === 'Domestic') {
        // Bounds for Malaysia
        mapOptions.maxBounds = [
            [0.5, 99.0], 
            [8.0, 120.0]
        ];
        mapOptions.maxBoundsViscosity = 1.0;
        mapOptions.minZoom = 5;
    }

    travelMap = L.map('travelMap', mapOptions);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(travelMap);

    travelMap.on('click', function(e) {
        if (mapMarker) travelMap.removeLayer(mapMarker);
        mapMarker = L.marker(e.latlng).addTo(travelMap);

        document.getElementById('mapDestPreview').value = "Loading location...";
        document.getElementById('mapProceedBtn').disabled = true;

        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${e.latlng.lat}&lon=${e.latlng.lng}&zoom=10&addressdetails=1`)
            .then(res => res.json())
            .then(data => {
                let locationName = '';
                if (data.address) {
                    if (type === 'Domestic' && data.address.country_code !== 'my') {
                        document.getElementById('mapDestPreview').value = "Please select a location within Malaysia.";
                        document.getElementById('mapProceedBtn').disabled = true;
                        if (mapMarker) travelMap.removeLayer(mapMarker);
                        mapMarker = null;
                        return;
                    }
                    if (data.address.state && data.address.country) {
                        locationName = `${data.address.state}, ${data.address.country}`;
                    } else if (data.address.country) {
                        locationName = data.address.country;
                    } else {
                        locationName = data.display_name;
                    }
                }
                if (!locationName) locationName = `${e.latlng.lat.toFixed(4)}, ${e.latlng.lng.toFixed(4)}`;
                
                document.getElementById('mapDestPreview').value = locationName;
                document.getElementById('travel_destination').value = locationName;
                document.getElementById('finalDestText').textContent = locationName;
                document.getElementById('mapProceedBtn').disabled = false;
            })
            .catch(() => {
                const loc = `${e.latlng.lat.toFixed(4)}, ${e.latlng.lng.toFixed(4)}`;
                document.getElementById('mapDestPreview').value = loc;
                document.getElementById('travel_destination').value = loc;
                document.getElementById('finalDestText').textContent = loc;
                document.getElementById('mapProceedBtn').disabled = false;
            });
    });

    setTimeout(() => travelMap.invalidateSize(), 100);
}

function searchMapLocation() {
    const query = document.getElementById('mapDestPreview').value.trim();
    if (!query) return;
    
    document.getElementById('mapSearchBtn').textContent = '...';
    document.getElementById('mapSearchBtn').disabled = true;
    
    const type = document.getElementById('travel_type_input').value;
    let url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1&addressdetails=1`;
    if (type === 'Domestic') {
        url += '&countrycodes=my';
    }
    
    fetch(url)
        .then(res => res.json())
        .then(data => {
            document.getElementById('mapSearchBtn').textContent = 'Search';
            document.getElementById('mapSearchBtn').disabled = false;
            
            if (data && data.length > 0) {
                const result = data[0];
                const latlng = [result.lat, result.lon];
                
                if (mapMarker) travelMap.removeLayer(mapMarker);
                mapMarker = L.marker(latlng).addTo(travelMap);
                travelMap.setView(latlng, type === 'Domestic' ? 10 : 8);
                
                let locationName = '';
                if (result.address) {
                    if (result.address.state && result.address.country) {
                        locationName = `${result.address.state}, ${result.address.country}`;
                    } else if (result.address.country) {
                        locationName = result.address.country;
                    } else {
                        locationName = result.display_name;
                    }
                }
                if (!locationName) locationName = result.display_name || `${result.lat}, ${result.lon}`;
                
                document.getElementById('mapDestPreview').value = locationName;
                document.getElementById('travel_destination').value = locationName;
                document.getElementById('finalDestText').textContent = locationName;
                document.getElementById('mapProceedBtn').disabled = false;
            } else {
                alert('Location not found in the specified region. Please try a different search term.');
                document.getElementById('mapProceedBtn').disabled = true;
            }
        })
        .catch(() => {
            document.getElementById('mapSearchBtn').textContent = 'Search';
            document.getElementById('mapSearchBtn').disabled = false;
            alert('Error searching for location.');
        });
}

document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('mapDestPreview');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchMapLocation();
            }
        });
    }
});

function selectTravelType(type) {
    document.getElementById('travel_type_input').value = type;
    document.getElementById('finalTypeBadge').textContent = type;
    
    document.getElementById('mapDestPreview').value = "";
    document.getElementById('travel_destination').value = "";
    document.getElementById('mapProceedBtn').disabled = true;

    goToStep(2);
    initTravelMap(type);
}

function goToStep(step) {
    document.getElementById('wizardStep1').style.display = step === 1 ? 'block' : 'none';
    document.getElementById('wizardStep2').style.display = step === 2 ? 'block' : 'none';
    document.getElementById('wizardStep3').style.display = step === 3 ? 'block' : 'none';
    document.getElementById('wizardFooter').style.display = step === 3 ? 'flex' : 'none';
    
    if(step === 2 && travelMap) {
        setTimeout(() => travelMap.invalidateSize(), 100);
    }
}

function toggleTransportOther() {
    const ts = document.getElementById('travel_transport');
    const to = document.getElementById('travel_transport_other');
    if (ts.value === 'Others') {
        to.style.display = 'block';
        to.required = true;
    } else {
        to.style.display = 'none';
        to.required = false;
        to.value = '';
    }
}

document.getElementById('travelForm').addEventListener('submit', function(e) {
    const staffId = document.getElementById('travel_staff_id').value;
    if (!staffId && !IS_STAFF_VIEW) {
        e.preventDefault();
        alert('Please select a staff member.');
        return;
    }
    
    const ts = document.getElementById('travel_transport');
    const to = document.getElementById('travel_transport_other');
    if (ts.value === 'Others') {
        if (!to.value.trim()) {
            e.preventDefault();
            alert('Please specify your mode of transport.');
            return;
        }
        ts.name = 'transport_ignore';
        to.name = 'transport';
    } else {
        ts.name = 'transport';
        to.name = 'transport_ignore';
    }
});

(function() {
    var f = document.getElementById('travel-filter-form');
    if (f) liveSearch(f, 'travel-results');
})();
</script>
@endsection
