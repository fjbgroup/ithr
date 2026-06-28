@extends('layouts.app')

@section('title', 'Staff Profile')

@section('content')
<div class="page-header">
    <div>
        <h2>Staff Profile</h2>
        <p class="page-subtitle">Detailed information for <strong>{{ $staff->name }}</strong></p>
    </div>
    <div class="header-actions">
        @if(Auth::user()->staff_no === $staff->staff_no || Auth::user()->isAdmin())
        <button class="btn btn-primary" data-requires-active onclick="openRequestModal('Staff Data')">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:4px;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Request Update
        </button>
        @endif
        <a href="{{ route('staff.index') }}" class="btn btn-outline">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:4px;"><polyline points="15 18 9 12 15 6"/></svg>
            Back to List
        </a>
    </div>
</div>

<!-- Request Update Modal -->
<div class="modal" id="requestUpdateModal">
    <div class="modal-box" style="max-width:520px;">
        <div class="modal-header ur-modal-header">
            <div style="display:flex;align-items:center;gap:.75rem;">
                <div class="ur-header-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </div>
                <div>
                    <h3 style="color:white;margin:0;font-size:.95rem;">Request Information Update</h3>
                    <p style="color:rgba(255,255,255,.6);font-size:.75rem;margin:.1rem 0 0;">
                        Staff No: <strong style="color:rgba(255,255,255,.9);">{{ $staff->staff_no }}</strong>
                    </p>
                </div>
            </div>
            <button class="modal-close" onclick="closeModal()" style="color:rgba(255,255,255,.6);font-size:1.4rem;">×</button>
        </div>

        <form action="{{ route('requests.store') }}" method="POST">
            @csrf
            <input type="hidden" name="record_type" id="ur_record_type" value="Staff Data">
            <input type="hidden" name="record_id" id="ur_record_id" value="{{ $staff->id }}">
            <input type="hidden" name="record_reference" id="ur_record_reference" value="{{ $staff->staff_no }}">

            <div class="modal-body">

                <!-- Context banner for Training / Family records -->
                <div id="ur_context_banner" style="display:none;background:var(--bg);border:1px solid var(--border);border-radius:10px;padding:.7rem 1rem;align-items:center;gap:.6rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="2" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <div style="font-size:.8rem;">
                        <span style="font-weight:600;color:var(--muted);text-transform:uppercase;font-size:.68rem;letter-spacing:.04em;">Record</span>
                        <span id="ur_display_ref" style="display:block;font-weight:600;color:var(--navy);font-size:.85rem;"></span>
                    </div>
                </div>

                <p id="ur_description" style="font-size:.84rem;color:var(--muted);margin:0;line-height:1.5;">
                    Select the fields you'd like corrected, then describe the right information below.
                </p>

                <!-- Field chip selectors -->
                <div id="ur_fields_group">
                    <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:.55rem;">Fields to Update</div>
                    <div style="display:flex;flex-wrap:wrap;gap:.4rem;">
                        <label class="ur-chip">
                            <input type="checkbox" name="fields[]" value="Legal Name">
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                Legal Name
                            </span>
                        </label>
                        <label class="ur-chip">
                            <input type="checkbox" name="fields[]" value="Date of Birth">
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                Date of Birth
                            </span>
                        </label>
                        <label class="ur-chip">
                            <input type="checkbox" name="fields[]" value="Gender">
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="4"/><path d="M8 14s-4 1.5-4 5h16c0-3.5-4-5-4-5"/></svg>
                                Gender
                            </span>
                        </label>
                        <label class="ur-chip">
                            <input type="checkbox" name="fields[]" value="Email">
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                Email
                            </span>
                        </label>
                        <label class="ur-chip">
                            <input type="checkbox" name="fields[]" value="Position">
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                Position
                            </span>
                        </label>
                        <label class="ur-chip">
                            <input type="checkbox" name="fields[]" value="Location">
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                Location
                            </span>
                        </label>
                    </div>
                </div>

                <div class="form-group" style="margin:0;">
                    <label>
                        Correct Information
                        <span style="color:var(--danger);margin-left:2px;">*</span>
                    </label>
                    <textarea name="message" rows="4" required
                        style="resize:vertical;min-height:96px;"
                        placeholder="Describe the correct information or the change you need. Be as specific as possible."></textarea>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary" style="display:flex;align-items:center;gap:.4rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    Submit Request
                </button>
            </div>
        </form>
    </div>
</div>

<div class="profile-layout">
    <div class="profile-sidebar">
        <div class="card profile-main-card">
            <div class="profile-header-strip company-{{ strtolower($staff->company) }}"></div>
            <div class="profile-card-body">
                <div class="profile-avatar-large">
                    {{ strtoupper(substr($staff->name, 0, 1)) }}
                </div>
                <h3 class="profile-name">{{ $staff->name }}</h3>
                <p class="profile-pos">{{ $staff->position ?? '—' }}</p>
                <div class="profile-badges">
                    <span class="company-badge company-{{ strtolower($staff->company) }}">{{ $staff->company }}</span>
                    <span class="dept-badge">{{ $staff->department->name ?? '—' }}</span>
                </div>
                
                <div class="profile-quick-info">
                    <div class="qi-item">
                        <span class="qi-label">Staff No</span>
                        <code class="qi-value">{{ $staff->staff_no }}</code>
                    </div>
                    <div class="qi-item">
                        <span class="qi-label">Email</span>
                        <span class="qi-value">{{ $staff->email ?? '—' }}</span>
                    </div>
                    <div class="qi-item">
                        <span class="qi-label">Joined</span>
                        <span class="qi-value">{{ $staff->date_joined ? date('d M Y', strtotime($staff->date_joined)) : '—' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="profile-main-content">
        <div class="md-tabs" id="profileTabs">
            <button class="md-tab active" onclick="switchTab('info')">Info</button>
            <button class="md-tab" onclick="switchTab('family')">Family <span class="md-tab-count">{{ $staff->familyMembers->count() }}</span></button>
            <button class="md-tab" onclick="switchTab('training')">Training <span class="md-tab-count">{{ $staff->courses->count() }}</span></button>
            <button class="md-tab" onclick="switchTab('travel')">Travel <span class="md-tab-count">{{ $staff->travelRecords->count() }}</span></button>
            <button class="md-tab" onclick="switchTab('bookings')">Bookings <span class="md-tab-count">{{ $staff->user && $staff->user->bookings ? $staff->user->bookings->count() : 0 }}</span></button>
            <button class="md-tab" onclick="switchTab('disciplinary')">Disciplinary <span class="md-tab-count">{{ $staff->irRecords->count() }}</span></button>
        </div>

        <!-- Info Tab -->
        <div id="tab-info" class="tab-content active">
            <div class="card">
                <div class="card-header"><h3>Personal & Employment Details</h3></div>
                <div class="card-body p-4">
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>IC Number</label>
                            <div>{{ $staff->ic_number ?? '—' }}</div>
                        </div>
                        <div class="detail-item">
                            <label>Date of Birth</label>
                            <div>{{ $staff->date_of_birth ? date('d M Y', strtotime($staff->date_of_birth)) : '—' }}</div>
                        </div>
                        <div class="detail-item">
                            <label>Age</label>
                            <div>{{ $staff->age !== null ? $staff->age . ' years' : '—' }}</div>
                        </div>
                        <div class="detail-item">
                            <label>Gender</label>
                            <div>{{ $staff->gender ?? '—' }}</div>
                        </div>
                        <div class="detail-item">
                            <label>Date Joined</label>
                            <div>{{ $staff->date_joined ? date('d M Y', strtotime($staff->date_joined)) : '—' }}</div>
                        </div>
                        <div class="detail-item">
                            <label>Years of Service</label>
                            <div>{{ $staff->yos ?? '—' }}</div>
                        </div>
                        <div class="detail-item">
                            <label>Employment Status</label>
                            <div>{{ $staff->employment_status ?? '—' }}</div>
                        </div>
                        <div class="detail-item">
                            <label>Last Promotion Date</label>
                            <div>{{ $staff->last_promotion_date ? date('d M Y', strtotime($staff->last_promotion_date)) : '—' }}</div>
                        </div>
                        <div class="detail-item">
                            <label>Location</label>
                            <div>{{ $staff->location ?? '—' }}</div>
                        </div>
                        <div class="detail-item">
                            <label>Company ID</label>
                            <div>{{ $staff->company_id ?? '—' }}</div>
                        </div>
                    </div>
                    
                    <h4 class="section-title mt-4">Job Classification</h4>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Compensation Grade</label>
                            <div>{{ $staff->compensation_grade ?? '—' }}</div>
                        </div>
                        <div class="detail-item">
                            <label>Management Level</label>
                            <div>{{ $staff->management_level ?? '—' }}</div>
                        </div>
                        <div class="detail-item">
                            <label>Job Level</label>
                            <div>{{ $staff->job_level ?? '—' }}</div>
                        </div>
                        <div class="detail-item">
                            <label>Job Category</label>
                            <div>{{ $staff->job_category ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Family Tab -->
        <div id="tab-family" class="tab-content" style="display:none;">
            @php
                $byBirthYear = fn($fm) => $fm->date_of_birth ? (int) date('Y', strtotime($fm->date_of_birth)) : PHP_INT_MAX;
                $familySorted = $staff->familyMembers->sortBy($byBirthYear);
                $spouses  = $familySorted->filter(fn($fm) => strtolower($fm->relationship) === 'spouse');
                $children = $familySorted->filter(fn($fm) => strtolower($fm->relationship) === 'child');
                $otherFamily = $familySorted->filter(fn($fm) => !in_array(strtolower($fm->relationship), ['spouse', 'child']));
            @endphp

            @if($staff->familyMembers->isEmpty())
                <div class="card">
                    <div class="table-wrap">
                        <table class="table">
                            <tbody>
                                <tr><td class="text-center p-4 text-muted">No family members recorded.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                @php
                    $familyGroups = [
                        ['title' => 'Spouse',           'members' => $spouses],
                        ['title' => 'Children',         'members' => $children],
                        ['title' => 'Other Relatives',  'members' => $otherFamily],
                    ];
                @endphp
                @foreach($familyGroups as $group)
                    @if($group['members']->isNotEmpty())
                    <div class="card" style="margin-bottom:1rem;">
                        <div class="card-header"><h3>{{ $group['title'] }} <span class="md-tab-count">{{ $group['members']->count() }}</span></h3></div>
                        <div class="table-wrap">
                            <table class="table">
                                <thead><tr><th>Name</th><th>Relationship</th><th>DOB</th><th>Phone</th></tr></thead>
                                <tbody>
                                    @foreach($group['members'] as $fm)
                                        <tr>
                                            <td><strong>{{ $fm->family_member_name }}</strong></td>
                                            <td>{{ $fm->relationship }}</td>
                                            <td>{{ $fm->date_of_birth ? date('d M Y', strtotime($fm->date_of_birth)) : '—' }}</td>
                                            <td>{{ $fm->phone_number ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                @endforeach
            @endif
        </div>

        <!-- Training Tab -->
        <div id="tab-training" class="tab-content" style="display:none;">
            <div class="card">
                <div class="table-wrap">
                    <table class="table">
                        <thead><tr><th>Code</th><th>Title</th><th>Type</th><th>Date</th><th>Status</th>@if(Auth::user()->staff_no === $staff->staff_no) <th style="text-align:right;">Actions</th> @endif</tr></thead>
                        <tbody>
                            @forelse($staff->courses as $course)
                                @php
                                    $tStatus = $course->pivot->status ?? 'Completed';
                                    $tType   = $course->pivot->training_type ?? $course->training_type ?? 'External';
                                    $statusClass = $tStatus === 'Completed' ? 'status-completed' : ($tStatus === 'Scheduled' ? 'status-scheduled' : 'status-pending');
                                @endphp
                                <tr>
                                    <td><code>{{ $course->code }}</code></td>
                                    <td>{{ $course->title }}</td>
                                    <td>
                                        <span style="display:inline-flex;padding:.2rem .55rem;border-radius:99px;font-size:.68rem;font-weight:700;text-transform:uppercase;
                                            {{ $tType === 'Internal' ? 'background:#f0fdf4;color:#16a34a;' : 'background:#fff7ed;color:#ea580c;' }}">
                                            {{ $tType }}
                                        </span>
                                    </td>
                                    <td>{{ $course->start_date ? date('d M Y', strtotime($course->start_date)) : '—' }}</td>
                                    <td><span class="status-badge {{ $statusClass }}">{{ $tStatus }}</span></td>
                                    @if(Auth::user()->staff_no === $staff->staff_no)
                                    <td style="text-align:right;">
                                        <button class="btn btn-xs btn-outline" style="font-size:.65rem;padding:.15rem .4rem;"
                                                onclick="openRequestModal('Training Record', {{ $course->pivot->id ?? 0 }}, '{{ $course->title }}')">
                                            Request Update
                                        </button>
                                    </td>
                                    @endif
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center p-4 text-muted">No training records found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Travel Tab -->
        <div id="tab-travel" class="tab-content" style="display:none;">
            <div class="card">
                <div class="table-wrap">
                    <table class="table">
                        <thead><tr><th>Destination</th><th>Purpose</th><th>Departure</th><th>Return</th></tr></thead>
                        <tbody>
                            @forelse($staff->travelRecords as $tr)
                                <tr>
                                    <td><strong>{{ $tr->destination }}</strong></td>
                                    <td>{{ $tr->purpose ?? '—' }}</td>
                                    <td>{{ date('d M Y', strtotime($tr->departure_date)) }}</td>
                                    <td>{{ date('d M Y', strtotime($tr->return_date)) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center p-4 text-muted">No travel records found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Bookings Tab -->
        <div id="tab-bookings" class="tab-content" style="display:none;">
            <div class="card">
                <div class="table-wrap">
                    <table class="table">
                        <thead><tr><th>Room</th><th>Date</th><th>Time</th><th>Status</th></tr></thead>
                        <tbody>
                            @if($staff->user && $staff->user->bookings)
                                @forelse($staff->user->bookings as $bk)
                                    <tr>
                                        <td><strong>{{ $bk->room->name }}</strong></td>
                                        <td>{{ date('d M Y', strtotime($bk->booking_date)) }}</td>
                                        <td>{{ date('H:i', strtotime($bk->start_time)) }} – {{ date('H:i', strtotime($bk->end_time)) }}</td>
                                        <td><span class="status-badge status-{{ strtolower($bk->status) }}">{{ $bk->status }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center p-4 text-muted">No room bookings found.</td></tr>
                                @endforelse
                            @else
                                <tr><td colspan="4" class="text-center p-4 text-muted">No linked user account found.</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Disciplinary Tab -->
        <div id="tab-disciplinary" class="tab-content" style="display:none;">
            <div class="card">
                <div class="table-wrap">
                    <table class="table">
                        <thead><tr><th>Date</th><th>Title</th><th>Type</th></tr></thead>
                        <tbody>
                            @forelse($staff->irRecords as $ir)
                                <tr>
                                    <td>{{ date('d M Y', strtotime($ir->date)) }}</td>
                                    <td>{{ $ir->title }}</td>
                                    <td>
                                        @if($ir->type === 'Verbal')
                                            <span style="background:#fef3c7;color:#92400e;font-size:.75rem;font-weight:600;padding:.2rem .65rem;border-radius:20px;">Verbal</span>
                                        @else
                                            <span style="background:#fee2e2;color:#991b1b;font-size:.75rem;font-weight:600;padding:.2rem .65rem;border-radius:20px;">Written</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center p-4 text-muted">No IR records found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.profile-layout { display: grid; grid-template-columns: 280px 1fr; gap: 1.5rem; align-items: start; }
.profile-main-card { text-align: center; }
.profile-header-strip { height: 8px; }
.profile-header-strip.company-fjb { background: #3b82f6; }
.profile-header-strip.company-fbsb { background: #10b981; }
.profile-header-strip.company-lbsb { background: #f59e0b; }
.profile-header-strip.company-fgt { background: #6366f1; }
.profile-card-body { padding: 2rem 1.5rem; }
.profile-avatar-large {
    width: 80px; height: 80px; border-radius: 50%; background: var(--navy); color: white;
    font-size: 2rem; font-weight: 700; display: flex; align-items: center; justify-content: center;
    margin: 0 auto 1rem;
}
.profile-name { font-size: 1.25rem; font-weight: 700; color: var(--navy); margin-bottom: .25rem; }
.profile-pos { color: var(--muted); font-size: .9rem; margin-bottom: 1rem; }
.profile-badges { display: flex; gap: .5rem; justify-content: center; margin-bottom: 1.5rem; }
.profile-quick-info { text-align: left; border-top: 1px solid var(--border); padding-top: 1.25rem; }
.qi-item { margin-bottom: .75rem; }
.qi-label { display: block; font-size: .7rem; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .05em; margin-bottom: .15rem; }
.qi-value { font-size: .875rem; color: var(--text); }
.section-title { font-size: .85rem; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .05em; border-bottom: 1px solid var(--border); padding-bottom: .5rem; margin-bottom: 1rem; }
.detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
.detail-item label { display: block; font-size: .75rem; color: var(--muted); margin-bottom: .2rem; }
.detail-item div { font-weight: 500; font-size: .9rem; }
.p-4 { padding: 1.5rem !important; }
.mt-4 { margin-top: 2rem !important; }

@media (max-width: 900px) {
    .profile-layout { grid-template-columns: 1fr; }
    .profile-sidebar { max-width: 400px; margin: 0 auto; width: 100%; }
}

/* ---- Request Update Modal ---- */
.ur-modal-header {
    background: linear-gradient(135deg, var(--navy) 0%, #1e3a5f 100%);
    border-radius: 14px 14px 0 0;
    padding: 1.25rem 1.5rem;
    border-bottom: none;
}
.ur-header-icon {
    width: 34px; height: 34px; border-radius: 9px;
    background: rgba(255,255,255,.15);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.ur-chip { cursor: pointer; }
.ur-chip input[type="checkbox"] { display: none; }
.ur-chip span {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .38rem .75rem;
    border-radius: 99px;
    border: 1.5px solid var(--border);
    font-size: .78rem; font-weight: 500;
    color: var(--text); background: var(--surface);
    transition: background .13s, border-color .13s, color .13s;
    user-select: none; white-space: nowrap;
}
.ur-chip:hover span { border-color: var(--navy); color: var(--navy); }
.ur-chip input:checked + span {
    background: var(--navy); border-color: var(--navy); color: white;
}
.ur-chip input:checked + span svg { stroke: white; }
</style>
@endsection

@section('scripts')
<script>
function openRequestModal(type, id, ref) {
    document.getElementById('ur_record_type').value = type;
    if (id) document.getElementById('ur_record_id').value = id;

    const banner   = document.getElementById('ur_context_banner');
    const refLabel = document.getElementById('ur_display_ref');
    const desc     = document.getElementById('ur_description');
    const fields   = document.getElementById('ur_fields_group');

    if (ref) {
        document.getElementById('ur_record_reference').value = ref;
        refLabel.textContent = ref;
        banner.style.display = 'flex';
    } else {
        document.getElementById('ur_record_reference').value = '{{ $staff->staff_no }}';
        banner.style.display = 'none';
    }

    if (type === 'Training Record') {
        desc.textContent = 'Describe what is incorrect in this training record and provide the correct details.';
        fields.style.display = 'none';
    } else if (type === 'Family Information') {
        desc.textContent = 'Specify the family member and the information that needs to be updated.';
        fields.style.display = 'none';
    } else {
        desc.textContent = "Select the fields you'd like corrected, then describe the right information below.";
        fields.style.display = 'block';
        // Reset checkboxes on reopen
        fields.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
    }

    openModal('requestUpdateModal');
}

function switchTab(tabId) {
    // Update tab buttons
    document.querySelectorAll('.md-tab').forEach(tab => {
        tab.classList.remove('active');
        if (tab.getAttribute('onclick').includes(tabId)) {
            tab.classList.add('active');
        }
    });
    
    // Update tab content
    document.querySelectorAll('.tab-content').forEach(content => {
        content.style.display = 'none';
    });
    document.getElementById('tab-' + tabId).style.display = 'block';
}
</script>
@endsection
