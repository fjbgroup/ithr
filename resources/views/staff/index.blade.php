@extends('layouts.app')

@section('title', 'Staff Registry')

@section('styles')
<style>
/* ── Avatar & name cell ── */
.staff-avatar {
    width: 34px; height: 34px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: .72rem; font-weight: 700; flex-shrink: 0; letter-spacing: .02em;
}
.staff-name-cell { display: flex; align-items: center; gap: .6rem; }
.staff-name-primary {
    font-weight: 600; font-size: .82rem; line-height: 1.25;
    color: var(--text);
    display: block; word-break: break-word;
    transition: color .15s; text-decoration: none;
}
.staff-name-primary:hover { color: #6366f1; }
.staff-id-sub {
    font-size: .68rem; color: var(--muted); display: block; margin-top: 1px;
    font-family: 'Courier New', monospace; letter-spacing: .02em;
}
.staff-pos {
    font-size: .78rem; color: var(--text); max-width: 150px;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: block;
}
.staff-dept {
    font-size: .68rem; color: var(--muted); max-width: 150px;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: block; margin-top: 2px;
}
.joined-yos-th-main { display: block; }
.joined-yos-th-sub  { display: block; font-size: .62rem; color: var(--muted); font-weight: 600; margin-top: 1px; }
.staff-joined { display: block; font-size: .73rem; color: var(--muted); }
.staff-yos    { display: block; font-size: .75rem; font-weight: 600; margin-top: 2px; }

/* ── Meta pills ── */
.mpill {
    display: inline-flex; align-items: center;
    font-size: .65rem; font-weight: 600; padding: .1rem .4rem;
    border-radius: 10px; white-space: nowrap; line-height: 1.6;
    background: var(--bg); color: var(--muted); border: 1px solid var(--border);
}
.mpill-critical { background: #dbeafe; color: #1e40af; border-color: #bfdbfe; }
[data-theme="dark"] .mpill { background: rgba(255,255,255,.06); border-color: var(--border); color: var(--muted); }
[data-theme="dark"] .mpill-critical { background: rgba(59,130,246,.15); color: #93c5fd; border-color: rgba(59,130,246,.25); }

/* ── Icon-only action buttons ── */
.btn-icon {
    display: inline-flex; align-items: center; justify-content: center;
    width: 28px; height: 28px; border-radius: 7px;
    border: 1px solid var(--border); background: transparent;
    color: var(--muted); cursor: pointer; transition: all .15s;
    text-decoration: none; flex-shrink: 0;
}
.btn-icon:hover { background: var(--bg); color: var(--text); border-color: #94a3b8; }
.btn-icon-danger:hover { background: #fee2e2; color: #dc2626; border-color: #fca5a5; }
[data-theme="dark"] .btn-icon:hover { background: rgba(255,255,255,.07); }
[data-theme="dark"] .btn-icon-danger:hover { background: rgba(220,38,38,.15); color: #f87171; border-color: rgba(248,113,113,.3); }

/* ── Search with icon prefix ── */
.search-wrap { position: relative; flex: 1; min-width: 200px; }
.search-wrap .search-ico {
    position: absolute; left: .65rem; top: 50%; transform: translateY(-50%);
    color: var(--muted); pointer-events: none; display: flex;
}
.search-wrap input { padding-left: 2.1rem; }

/* ── Result bar ── */
.result-bar {
    padding: .45rem .75rem; display: flex; align-items: center;
    justify-content: space-between; border-bottom: 1px solid var(--border);
    font-size: .75rem; color: var(--muted); gap: .5rem; flex-wrap: wrap;
    min-height: 36px;
}
.result-count { font-weight: 700; color: var(--text); }

/* ── Table row hover ── */
.table tbody tr:hover { background: var(--bg); }
[data-theme="dark"] .table tbody tr:hover { background: rgba(255,255,255,.03); }
</style>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h2>Staff Registry</h2>
        <p class="page-subtitle">Manage employee records and linked user accounts.</p>
    </div>
    @canwrite
    <div class="header-actions">
        <button id="bulkDeleteBtn" class="btn btn-danger" style="display:none;" onclick="confirmBulkDelete()">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:4px;"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
            Delete (<span id="selectedCount">0</span>)
        </button>
        <button class="btn btn-outline" onclick="openModal('staffImportModal')">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:4px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            Import
        </button>
        <button class="btn btn-outline" onclick="openModal('bulkAddStaffModal')">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:4px;"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="16" y1="11" x2="22" y2="11"/></svg>
            Bulk Add
        </button>
        <button class="btn btn-primary" onclick="openAddModal()">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:4px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Staff
        </button>
    </div>
    @endcanwrite
</div>

<div class="filters-card">
    <form id="staff-filter-form" action="{{ route('staff.index') }}" method="GET" class="filters-form" autocomplete="off">
        <div class="filter-group search-group">
            <div class="app-search" style="width:100%;">
                <svg class="app-search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name, staff no, or position...">
            </div>
        </div>
        <div class="filter-group">
            <select name="company" class="form-select">
                <option value="">All Companies</option>
                @foreach($companies as $co)
                    <option value="{{ $co->code }}" {{ request('company') == $co->code ? 'selected' : '' }}>{{ $co->code }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <select name="dept" class="form-select">
                <option value="">All Departments</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('dept') == $dept->id ? 'selected' : '' }}>[{{ $dept->company }}] {{ $dept->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn btn-outline btn-sm">Filter</button>
            <a href="{{ route('staff.index') }}" class="btn btn-ghost btn-sm">Clear</a>
        </div>
    </form>
</div>

<div class="card card-sticky" id="staff-results">
    @if($staff_list->isEmpty())
        <div class="empty-state">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
            <p>No staff records found.</p>
        </div>
    @else
        <div class="result-bar">
            <span>
                <span class="result-count">{{ $staff_list->count() }}</span>
                staff record{{ $staff_list->count() !== 1 ? 's' : '' }}{{ request('q') ? ' matching "'.e(request('q')).'"' : '' }}
            </span>
        </div>
        <form id="bulkDeleteForm" action="{{ route('staff.bulkDestroy') }}" method="POST" style="display:none;">
            @csrf
        </form>
        <div class="table-wrap table-wrap-sticky">
            <table class="table table-sticky table-xs">
                <thead>
                    <tr>
                        @canwrite<th style="width:32px;text-align:center;"><input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)"></th>@endcanwrite
                        <th style="min-width: 220px;">Employee</th>
                        <th style="min-width: 160px;">Role &amp; Department</th>
                        <th>Co</th>
                        <th style="white-space:nowrap;">Date Joined</th>
                        <th style="white-space:nowrap;">YOS</th>
                        <th class="hide-on-tablet">Grade</th>
                        <th class="hide-on-tablet">Gender</th>
                        <th class="hide-on-tablet">Location</th>
                        <th style="text-align:right;white-space:nowrap;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($staff_list as $s)
                        @php
                            $nameParts = array_values(array_filter(explode(' ', $s->name)));
                            $initials = count($nameParts) >= 2
                                ? strtoupper(substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1))
                                : strtoupper(substr($s->name, 0, 2));
                            $palettes = [
                                ['bg'=>'#ede9fe','fg'=>'#6d28d9'],
                                ['bg'=>'#dbeafe','fg'=>'#1e40af'],
                                ['bg'=>'#dcfce7','fg'=>'#166534'],
                                ['bg'=>'#fce7f3','fg'=>'#9d174d'],
                                ['bg'=>'#fef9c3','fg'=>'#854d0e'],
                                ['bg'=>'#ffedd5','fg'=>'#9a3412'],
                                ['bg'=>'#e0f2fe','fg'=>'#075985'],
                                ['bg'=>'#f0fdf4','fg'=>'#14532d'],
                                ['bg'=>'#fdf4ff','fg'=>'#86198f'],
                            ];
                            $pal = $palettes[ord(strtoupper($s->name[0] ?? 'A')) % count($palettes)];
                        @endphp
                        <tr>
                            @canwrite
                            <td style="text-align:center;">
                                <input type="checkbox" class="staff-checkbox" value="{{ $s->id }}" onclick="updateBulkDeleteButton()">
                            </td>
                            @endcanwrite

                            {{-- Employee: avatar + name + ID --}}
                            <td>
                                <div class="staff-name-cell">
                                    <div class="staff-avatar" style="background:{{ $pal['bg'] }};color:{{ $pal['fg'] }};">{{ $initials }}</div>
                                    <div style="min-width:0;">
                                        <a href="{{ route('staff.show', $s->id) }}" class="staff-name-primary" title="{{ $s->name }}" {!! !$s->is_active ? 'style="color:var(--danger);white-space:normal;word-break:break-word;display:block;"' : 'style="white-space:normal;word-break:break-word;display:block;"' !!}>{{ $s->name }}</a>
                                        <span class="staff-id-sub">
                                            {{ $s->staff_no }}
                                            @if(!$s->is_active)
                                                <span style="color:var(--danger);font-weight:bold;margin-left:4px;">[INACTIVE]</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </td>

                            {{-- Role & Department --}}
                            <td>
                                <span class="staff-pos" title="{{ $s->position }}">{{ $s->position ?? '—' }}</span>
                                <span class="staff-dept" title="{{ optional($s->department)->name }}">{{ optional($s->department)->name ?? '—' }}</span>
                                <div style="display:flex;gap:.2rem;flex-wrap:wrap;margin-top:.2rem;">
                                </div>
                            </td>

                            {{-- Company badge --}}
                            <td>
                                @php
                                    $rawCo = trim($s->company);
                                    $coMap = [
                                        '4810 FGV Johor Bulkers Sdn Bhd' => 'FJB',
                                        'FGV Johor Bulkers Sdn Bhd' => 'FJB',
                                        'FJB' => 'FJB',
                                        '4300 FGV Bulkers Sdn Bhd' => 'FGVB',
                                        'FGV Bulkers Sdn Bhd' => 'FGVB',
                                        'FGV Bulkers Sdn Bhd ..' => 'FGVB',
                                        'FGVB' => 'FGVB',
                                        'FBSB' => 'FGVB',
                                        '4850 Langsat Bulkers Sdn Bhd' => 'LBSB',
                                        'Langsat Bulkers Sdn Bhd' => 'LBSB',
                                        'LBSB' => 'LBSB',
                                        '4310 FGV Grains Terminal Sdn' => 'FGT',
                                        'FGV Grains Terminal Sdn' => 'FGT',
                                        'FGT' => 'FGT'
                                    ];
                                    
                                    $coCode = $coMap[$rawCo] ?? null;
                                    
                                    if (!$coCode) {
                                        $cleanCo = strtolower(str_replace('..', '', $rawCo));
                                        $co = $companies->first(function($c) use ($cleanCo, $rawCo) {
                                            if (strcasecmp($c->code, $rawCo) === 0) return true;
                                            if (strcasecmp($c->name, $rawCo) === 0) return true;
                                            if ($cleanCo && str_contains(strtolower($c->name), trim($cleanCo))) return true;
                                            if (str_contains(strtolower($c->name), strtolower($rawCo))) return true;
                                            if (strcasecmp($cleanCo, 'fgvb') === 0 && strcasecmp($c->code, 'fbsb') === 0) return true;
                                            return false;
                                        });
                                        $coCode = $co ? $co->code : $rawCo;
                                    }
                                @endphp
                                <span class="company-badge company-{{ strtolower($coCode) }}" style="font-size:.62rem;padding:.1rem .35rem;">{{ $coCode }}</span>
                            </td>

                            {{-- Date Joined --}}
                            <td style="white-space:nowrap;font-size:.74rem;">
                                {{ $s->date_joined ? date('d M Y', strtotime($s->date_joined)) : '—' }}
                            </td>

                            {{-- YOS --}}
                            <td style="white-space:nowrap;font-size:.74rem;text-align:center;">
                                {{ $s->yos !== null ? $s->yos : '—' }}
                            </td>

                            {{-- Grade --}}
                            <td class="hide-on-tablet" style="font-size:.74rem;white-space:nowrap;">
                                {{ $s->compensation_grade ?? '—' }}
                            </td>

                            {{-- Gender --}}
                            <td class="hide-on-tablet" style="font-size:.74rem;white-space:nowrap;">
                                {{ $s->gender ?? '—' }}
                            </td>

                            {{-- Location --}}
                            <td class="hide-on-tablet" style="font-size:.74rem;max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $s->location }}">
                                {{ $s->location ?? '—' }}
                            </td>

                            {{-- Actions --}}
                            <td class="td-actions" style="text-align:right;white-space:nowrap;">
                                <div style="display:inline-flex;gap:.25rem;align-items:center;">
                                    <a href="{{ route('staff.show', $s->id) }}" class="btn-icon" title="View Profile">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                    @canwrite
                                    <button class="btn-icon" title="Edit" onclick="editStaff({{ json_encode($s) }})">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </button>
                                    @endcanwrite
                                    @if(Auth::user()->isAdminIT())
                                        <form action="{{ route('staff.destroy', $s->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this staff record? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon btn-icon-danger" title="Delete">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
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
    @endif
</div>

<!-- Add/Edit Staff Modal -->
<div class="modal" id="addStaffModal">
    <div class="modal-box" style="max-width:720px;">
        <div class="modal-header">
            <h3 id="staffModalTitle">Add Staff</h3>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <form method="POST" action="{{ route('staff.store') }}" id="staffForm">
            @csrf
            <div id="methodField"></div>
            <input type="hidden" name="id" id="f_id">
            <div class="form-grid">
                <!-- Identity -->
                <div class="form-group">
                    <label>Employee ID (Staff No) *</label>
                    <div style="display:flex; gap:0.5rem;">
                        <input type="text" name="staff_no" id="f_staff_no" required style="flex:1;">
                        <button type="button" class="btn btn-sm btn-outline" onclick="generateId()">Generate</button>
                    </div>
                </div>
                <div class="form-group">
                    <label>Company *</label>
                    <select name="company" id="f_company" required>
                        @foreach($companies as $co)
                            <option value="{{ $co->code }}">{{ $co->code }} — {{ $co->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group form-full"><label>Legal Full Name *</label><input type="text" name="name" id="f_name" required></div>
                <div class="form-group"><label>Date of Birth</label><input type="date" name="date_of_birth" id="f_date_of_birth"></div>
                <div class="form-group"><label>IC Number</label><input type="text" name="ic_number" id="f_ic_number" placeholder="e.g. 900101-07-1234"></div>
                <div class="form-group">
                    <label>Employment Status</label>
                    <select name="employment_status" id="f_employment_status">
                        <option value="">— Select —</option>
                        <option value="Permanent">Permanent</option>
                        <option value="Contract">Contract</option>
                        <option value="Temporary">Temporary</option>
                        <option value="Intern">Intern</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender" id="f_gender">
                        <option value="">— Select —</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <!-- Employment -->
                <div class="form-group form-full" style="margin-top:.25rem;"><div style="font-size:.7rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.08em;padding:.5rem 0 0;border-top:1px solid var(--border);">Employment</div></div>
                <div class="form-group"><label>Hire Date</label><input type="date" name="date_joined" id="f_date_joined"></div>
                <div class="form-group"><label>Company ID</label><input type="text" name="company_id" id="f_company_id" placeholder="e.g. FJB-001"></div>
                <div class="form-group form-full">
                    <label>Position</label>
                    <input type="text" name="position" id="f_position" list="positions_list" autocomplete="off">
                    <datalist id="positions_list">
                        @foreach($positions as $pos)
                            <option value="{{ $pos->title }}">
                        @endforeach
                    </datalist>
                </div>
                <div class="form-group form-full">
                    <label>Department</label>
                    <select name="department_id" id="f_department_id">
                        <option value="">— None —</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}">[{{ $d->company }}] {{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group"><label>Location</label><input type="text" name="location" id="f_location" placeholder="e.g. Johor Bahru"></div>
                <div class="form-group"><label>Email</label><input type="email" name="email" id="f_email"></div>
                <!-- Classification -->
                <div class="form-group form-full" style="margin-top:.25rem;"><div style="font-size:.7rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.08em;padding:.5rem 0 0;border-top:1px solid var(--border);">Classification</div></div>
                <div class="form-group"><label>Compensation Grade</label><input type="text" name="compensation_grade" id="f_compensation_grade" placeholder="e.g. G7"></div>
                <div class="form-group"><label>Management Level</label><input type="text" name="management_level" id="f_management_level" placeholder="e.g. Senior Manager"></div>
                <div class="form-group form-full"><label>Job Level — Primary Position</label><input type="text" name="job_level" id="f_job_level"></div>
                <div class="form-group"><label>Job Category</label><input type="text" name="job_category" id="f_job_category"></div>
                <div class="form-group"><label>Last Promotion Date</label><input type="date" name="last_promotion_date" id="f_last_promotion_date"></div>
                <div class="form-group form-full" id="group_is_active" style="display:none; margin-top:.5rem; padding-top:.5rem; border-top:1px dashed var(--border);">
                    <label style="color:var(--danger);">Account Status</label>
                    <select name="is_active" id="f_is_active">
                        <option value="1">Active</option>
                        <option value="0">Inactive (Resigned / Deactivated)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Add Staff Modal -->
<div class="modal" id="bulkAddStaffModal">
    <div class="modal-box" style="max-width:860px;">
        <div class="modal-header">
            <h3>Bulk Add Staff</h3>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <form method="POST" action="{{ route('staff.bulkStore') }}" id="bulkAddForm">
            @csrf
            <div style="overflow-x:auto;margin-bottom:1rem;">
                <table class="table" style="font-size:.83rem;min-width:700px;" id="bulkTable">
                    <thead>
                        <tr>
                            <th style="width:32px;">#</th>
                            <th>Staff No <span style="color:var(--danger)">*</span></th>
                            <th>Full Name <span style="color:var(--danger)">*</span></th>
                            <th>Company <span style="color:var(--danger)">*</span></th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Date Joined</th>
                            <th style="width:36px;"></th>
                        </tr>
                    </thead>
                    <tbody id="bulkRows"></tbody>
                </table>
            </div>
            <button type="button" class="btn btn-ghost btn-sm" onclick="addBulkRow()">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add Row
            </button>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save All</button>
            </div>
        </form>
    </div>
</div>

<!-- Staff Import Modal -->
<div class="modal" id="staffImportModal" onclick="if(event.target===this)closeModal()">
    <div class="modal-box" style="max-width:520px;">
        <div class="modal-header">
            <h3 class="modal-title">Import Staff from Excel / CSV</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <!-- Step 1: file selection -->
            <div id="staffImportStep1">
                <p style="font-size:.85rem;color:#64748b;margin-bottom:.75rem;">Select your file — the system will read your column headers so you can map them to the correct fields.</p>
                <a href="{{ route('staff.import-template') }}" class="btn btn-outline btn-sm" style="margin-bottom:1.25rem;display:inline-flex;align-items:center;gap:.4rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Download Template
                </a>
                <div class="form-group">
                    <label class="form-label">Select File <span style="color:#64748b;font-weight:400;">(.xlsx, .xls, .csv)</span></label>
                    <input type="file" id="staffImportFile" class="form-input" accept=".xlsx,.xls,.csv">
                </div>
                <div class="form-group" style="margin-top:.75rem;">
                    <label class="form-label" style="font-size:.82rem;">Header row <span style="font-weight:400;color:#94a3b8;">(row number containing column names — usually 1)</span></label>
                    <input type="number" id="staffHeaderRow" class="form-input" value="1" min="1" max="100" style="width:80px;">
                </div>
                <div style="display:flex;justify-content:flex-end;gap:.5rem;margin-top:1rem;">
                    <button type="button" class="btn btn-outline" onclick="closeModal()">Cancel</button>
                    <button type="button" id="staffImportNextBtn" class="btn btn-primary" onclick="staffImportNext()">Next: Map Columns →</button>
                </div>
            </div>
            <!-- Step 2: column mapping -->
            <div id="staffImportStep2" style="display:none;">
                <form method="POST" action="{{ route('staff.import') }}" enctype="multipart/form-data" id="staffImportForm">
                    @csrf
                    <input type="file" name="file" id="staffImportFileHidden" style="display:none;" tabindex="-1">
                    <input type="hidden" name="sheet_index" id="staffSheetIndexInput" value="0">
                    <input type="hidden" name="header_row" id="staffHeaderRowInput" value="1">
                    <div id="staffSheetSelector"></div>
                    <div id="staffMappingTable"></div>
                    <div style="display:flex;justify-content:flex-end;gap:.5rem;margin-top:1rem;">
                        <button type="button" class="btn btn-outline" onclick="staffImportBack()">← Back</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
liveSearch(document.getElementById('staff-filter-form'), 'staff-results');

function toggleSelectAll(source) {
    const checkboxes = document.querySelectorAll('.staff-checkbox');
    checkboxes.forEach(cb => cb.checked = source.checked);
    updateBulkDeleteButton();
}

function updateBulkDeleteButton() {
    const checked = document.querySelectorAll('.staff-checkbox:checked');
    const btn = document.getElementById('bulkDeleteBtn');
    const count = document.getElementById('selectedCount');

    if (checked.length > 0) {
        btn.style.display = 'inline-flex';
        count.textContent = checked.length;
    } else {
        btn.style.display = 'none';
    }
}

function confirmBulkDelete() {
    const checked = document.querySelectorAll('.staff-checkbox:checked');
    if (checked.length === 0) return;

    if (confirm(`Delete ${checked.length} selected staff records? This cannot be undone.`)) {
        const form = document.getElementById('bulkDeleteForm');
        form.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());

        checked.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = cb.value;
            form.appendChild(input);
        });

        form.submit();
    }
}

function openAddModal() {
    document.getElementById('staffModalTitle').textContent = 'Add Staff';
    document.getElementById('staffForm').action = "{{ route('staff.store') }}";
    document.getElementById('methodField').innerHTML = '';
    document.getElementById('staffForm').reset();
    document.getElementById('f_id').value = '';
    document.getElementById('f_is_active').value = '1';
    document.getElementById('group_is_active').style.display = 'none';
    openModal('addStaffModal');
}

function editStaff(data) {
    document.getElementById('staffModalTitle').textContent = 'Edit Staff';
    document.getElementById('staffForm').action = "{{ url('staff') }}/" + data.id;
    document.getElementById('methodField').innerHTML = '@method("PUT")';

    document.getElementById('f_id').value = data.id;
    document.getElementById('f_staff_no').value = data.staff_no;
    document.getElementById('f_company').value = data.company;
    document.getElementById('f_name').value = data.name;
    document.getElementById('f_date_of_birth').value = data.date_of_birth || '';
    document.getElementById('f_gender').value = data.gender || '';
    document.getElementById('f_ic_number').value = data.ic_number || '';
    document.getElementById('f_employment_status').value = data.employment_status || '';
    document.getElementById('f_date_joined').value = data.date_joined || '';
    document.getElementById('f_company_id').value = data.company_id || '';
    document.getElementById('f_position').value = data.position || '';
    document.getElementById('f_department_id').value = data.department_id || '';
    document.getElementById('f_location').value = data.location || '';
    document.getElementById('f_email').value = data.email || '';
    document.getElementById('f_compensation_grade').value = data.compensation_grade || '';
    document.getElementById('f_management_level').value = data.management_level || '';
    document.getElementById('f_job_level').value = data.job_level || '';
    document.getElementById('f_job_category').value = data.job_category || '';
    document.getElementById('f_last_promotion_date').value = data.last_promotion_date || '';
    
    document.getElementById('f_is_active').value = data.is_active ? '1' : '0';
    document.getElementById('group_is_active').style.display = 'block';

    openModal('addStaffModal');
}

function generateId() {
    fetch("{{ route('staff.generateId') }}")
        .then(response => response.json())
        .then(data => {
            document.getElementById('f_staff_no').value = data.staff_no;
        });
}

const depts = {!! json_encode($departments->map(fn($d) => ['id'=>$d->id, 'name'=>$d->name, 'company'=>$d->company])) !!};

function deptOptions(selId) {
    return '<option value="">— None —</option>' +
        depts.map(d => `<option value="${d.id}" ${d.id==selId?'selected':''}>[${d.company}] ${d.name}</option>`).join('');
}

let rowIdx = 0;
window.addBulkRow = function(data) {
    const i = rowIdx++;
    const tr = document.createElement('tr');
    tr.id = 'brow-' + i;
    tr.innerHTML = `
        <td style="color:var(--muted);text-align:center;">${i+1}</td>
        <td><input type="text" name="rows[${i}][staff_no]" placeholder="e.g. 0001" required style="width:100px;" class="form-input btn-sm"></td>
        <td><input type="text" name="rows[${i}][name]" placeholder="Full name" required style="min-width:160px;" class="form-input btn-sm"></td>
        <td>
            <select name="rows[${i}][company]" style="width:80px;" class="form-select btn-sm">
                <option value="FJB">FJB</option>
                <option value="FBSB">FBSB</option>
                <option value="LBSB">LBSB</option>
                <option value="FGT">FGT</option>
            </select>
        </td>
        <td><select name="rows[${i}][department_id]" style="min-width:150px;" class="form-select btn-sm">${deptOptions('')}</select></td>
        <td><input type="text" name="rows[${i}][position]" placeholder="Position" style="min-width:120px;" class="form-input btn-sm"></td>
        <td><input type="date" name="rows[${i}][date_joined]" style="width:130px;" class="form-input btn-sm"></td>
        <td><button type="button" class="btn btn-sm btn-ghost" style="color:var(--danger);padding:.25rem .4rem;" onclick="document.getElementById('brow-${i}').remove()">×</button></td>
    `;
    document.getElementById('bulkRows').appendChild(tr);
};

// Pre-add 3 rows when bulk modal opens
document.querySelector('[onclick="openModal(\'bulkAddStaffModal\')"]').addEventListener('click', function() {
    const tbody = document.getElementById('bulkRows');
    if (tbody.children.length === 0) {
        for (let k = 0; k < 3; k++) addBulkRow();
    }
});

// ===== Staff Import Wizard =====
const STAFF_IMPORT_FIELDS = [
    { key: 'staff_no',           label: 'Employee ID',                  required: true,  aliases: ['employee_id', 'emp_id', 'staff_no', 'staff_id'] },
    { key: 'name',               label: 'Legal Full Name',              required: true,  aliases: ['legal_full_name', 'full_name', 'legal_name', 'name'] },
    { key: 'date_of_birth',      label: 'Date of Birth',                required: false, aliases: ['date_of_birth', 'date of birth'] },
    { key: 'date_joined',        label: 'Hire Date',                    required: true,  aliases: ['hire_date', 'date_joined', 'date_hired', 'join_date', 'date joined'] },
    { key: 'gender',             label: 'Gender',                       required: false, aliases: ['gender'] },
    { key: 'location',           label: 'Location',                     required: false, aliases: ['location'] },
    { key: 'position',           label: 'Position',                     required: false, aliases: ['position'] },
    { key: 'compensation_grade', label: 'Compensation Grade',           required: false, aliases: ['compensation_grade', 'grade'] },
    { key: 'management_level',   label: 'Management Level',             required: false, aliases: ['management_level'] },
    { key: 'job_level',          label: 'Job Level - Primary Position', required: false, aliases: ['job_level_primary_position', 'job_level'] },
    { key: 'job_category',       label: 'Job Category',                 required: false, aliases: ['job_category'] },
    { key: 'ic_number',          label: 'IC Number',                    required: false, aliases: ['ic_number', 'ic', 'nric', 'ic_no', 'ic. no', 'ic no'] },
    { key: 'employment_status',  label: 'Employment Status',            required: false, aliases: ['employment_status', 'status_of_employment', 'status of employment', 'status'] },
    { key: 'last_promotion_date',label: 'Last Promotion Date',          required: false, aliases: ['last_promotion_date', 'last promotion date'] },
    { key: 'company',            label: 'Company',                      required: false, aliases: ['company'] },
    { key: 'company_id',         label: 'Company - ID',                 required: false, aliases: ['company_id'] },
    { key: 'department',         label: 'Department',                   required: false, aliases: ['department'] },
    { key: 'email',              label: 'Email',                        required: false, aliases: ['email'] },
    { key: 'phone_number',       label: 'Phone',                        required: false, aliases: ['phone_number', 'phone'] },
];

function staffImportNext() {
    const fileInput = document.getElementById('staffImportFile');
    if (!fileInput.files.length) { alert('Please select a file first.'); return; }
    const headerRow = document.getElementById('staffHeaderRow').value || 1;
    const btn = document.getElementById('staffImportNextBtn');
    btn.disabled = true; btn.textContent = 'Detecting…';
    const fd = new FormData();
    fd.append('file', fileInput.files[0]);
    fd.append('header_row', headerRow);
    fd.append('sheet_index', 0);
    fd.append('_token', '{{ csrf_token() }}');
    fetch('{{ route("staff.import-preview") }}', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            document.getElementById('staffSheetIndexInput').value = 0;
            document.getElementById('staffHeaderRowInput').value = headerRow;
            _importRenderSheetSelector('staffSheetSelector', data.sheets || [], 'staffRedetect');
            _importRenderMapping('staffMappingTable', STAFF_IMPORT_FIELDS, data.headers || []);
            document.getElementById('staffImportStep1').style.display = 'none';
            document.getElementById('staffImportStep2').style.display = '';
        })
        .catch(() => alert('Could not read file headers. Please check your file format.'))
        .finally(() => { btn.disabled = false; btn.textContent = 'Next: Map Columns →'; });
}

function staffRedetect() {
    const fileInput = document.getElementById('staffImportFile');
    const headerRow = document.getElementById('staffHeaderRow').value || 1;
    const sheetSel = document.getElementById('staffSheetSelectorSelect');
    const sheetIndex = sheetSel ? sheetSel.value : 0;
    const fd = new FormData();
    fd.append('file', fileInput.files[0]);
    fd.append('header_row', headerRow);
    fd.append('sheet_index', sheetIndex);
    fd.append('_token', '{{ csrf_token() }}');
    fetch('{{ route("staff.import-preview") }}', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            document.getElementById('staffSheetIndexInput').value = sheetIndex;
            document.getElementById('staffHeaderRowInput').value = headerRow;
            _importRenderMapping('staffMappingTable', STAFF_IMPORT_FIELDS, data.headers || []);
        })
        .catch(() => alert('Could not re-detect columns. Please check your settings.'));
}

function staffImportBack() {
    document.getElementById('staffImportStep1').style.display = '';
    document.getElementById('staffImportStep2').style.display = 'none';
}

document.getElementById('staffImportForm').addEventListener('submit', function() {
    const file = document.getElementById('staffImportFile').files[0];
    if (file) {
        try { const dt = new DataTransfer(); dt.items.add(file); document.getElementById('staffImportFileHidden').files = dt.files; } catch(e) {}
    }
});
</script>
@endsection
