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
        <button class="btn btn-primary" onclick="openRequestModal('Staff Data')">
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
        <div class="modal-header">
            <h3>Request Information Update</h3>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <form action="{{ route('requests.store') }}" method="POST">
            @csrf
            <input type="hidden" name="record_type" id="ur_record_type" value="Staff Data">
            <input type="hidden" name="record_id" id="ur_record_id" value="{{ $staff->id }}">
            <input type="hidden" name="record_reference" id="ur_record_reference" value="{{ $staff->staff_no }}">
            
            <div class="modal-body">
                <p id="ur_description" style="font-size:.85rem; color:var(--muted); margin-bottom:1rem;">
                    Select the fields you want to update and provide the new information below.
                </p>
                <div id="ur_display_ref_wrap" style="display:none; margin-bottom:1rem;">
                    <span style="font-size:.7rem; font-weight:700; color:var(--muted); text-transform:uppercase;">Record:</span>
                    <div id="ur_display_ref" style="font-weight:600; font-size:.85rem;"></div>
                </div>
                
                <div id="ur_fields_group" class="form-group">
                    <label>Fields to Update</label>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:.5rem; margin-top:.5rem;">
                        <label style="font-weight:400; display:flex; align-items:center; gap:.5rem;">
                            <input type="checkbox" name="fields[]" value="Legal Name"> Legal Name
                        </label>
                        <label style="font-weight:400; display:flex; align-items:center; gap:.5rem;">
                            <input type="checkbox" name="fields[]" value="Date of Birth"> Date of Birth
                        </label>
                        <label style="font-weight:400; display:flex; align-items:center; gap:.5rem;">
                            <input type="checkbox" name="fields[]" value="Gender"> Gender
                        </label>
                        <label style="font-weight:400; display:flex; align-items:center; gap:.5rem;">
                            <input type="checkbox" name="fields[]" value="Email"> Email
                        </label>
                        <label style="font-weight:400; display:flex; align-items:center; gap:.5rem;">
                            <input type="checkbox" name="fields[]" value="Position"> Position
                        </label>
                        <label style="font-weight:400; display:flex; align-items:center; gap:.5rem;">
                            <input type="checkbox" name="fields[]" value="Location"> Location
                        </label>
                    </div>
                </div>

                <div class="form-group" style="margin-top:1rem;">
                    <label>Message / Correct Information <span style="color:var(--danger)">*</span></label>
                    <textarea name="message" rows="4" required placeholder="Please provide the correct information or details about the requested change..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit Request</button>
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
                            <label>Date of Birth</label>
                            <div>{{ $staff->date_of_birth ? date('d M Y', strtotime($staff->date_of_birth)) : '—' }}</div>
                        </div>
                        <div class="detail-item">
                            <label>Gender</label>
                            <div>{{ $staff->gender ?? '—' }}</div>
                        </div>
                        <div class="detail-item">
                            <label>Operation / Support</label>
                            <div>{{ $staff->operation_support ?? '—' }}</div>
                        </div>
                        <div class="detail-item">
                            <label>Location</label>
                            <div>{{ $staff->location ?? '—' }}</div>
                        </div>
                        <div class="detail-item">
                            <label>Critical Position</label>
                            <div>{!! $staff->critical_position ? '<span class="status-badge status-rejected">Yes</span>' : 'No' !!}</div>
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
                        <div class="detail-item">
                            <label>Job Family</label>
                            <div>{{ $staff->job_family ?? '—' }}</div>
                        </div>
                        <div class="detail-item">
                            <label>Job Classification</label>
                            <div>{{ $staff->job_classification ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Family Tab -->
        <div id="tab-family" class="tab-content" style="display:none;">
            <div class="card">
                <div class="table-wrap">
                    <table class="table">
                        <thead><tr><th>Name</th><th>Relationship</th><th>DOB</th><th>Phone</th></tr></thead>
                        <tbody>
                            @forelse($staff->familyMembers as $fm)
                                <tr>
                                    <td><strong>{{ $fm->name }}</strong></td>
                                    <td>{{ $fm->relationship }}</td>
                                    <td>{{ $fm->date_of_birth ? date('d M Y', strtotime($fm->date_of_birth)) : '—' }}</td>
                                    <td>{{ $fm->phone_number ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center p-4 text-muted">No family members recorded.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
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
</style>
@endsection

@section('scripts')
<script>
function openRequestModal(type, id, ref) {
    document.getElementById('ur_record_type').value = type;
    if (id) document.getElementById('ur_record_id').value = id;
    if (ref) {
        document.getElementById('ur_record_reference').value = ref;
        document.getElementById('ur_display_ref').textContent = ref;
        document.getElementById('ur_display_ref_wrap').style.display = 'block';
    } else {
        document.getElementById('ur_record_reference').value = '{{ $staff->staff_no }}';
        document.getElementById('ur_display_ref_wrap').style.display = 'none';
    }
    
    const desc = document.getElementById('ur_description');
    const fieldsGroup = document.getElementById('ur_fields_group');
    
    if (type === 'Training Record') {
        desc.textContent = 'Please specify what is incorrect in this training record and provide the correct details.';
        fieldsGroup.style.display = 'none';
    } else if (type === 'Family Information') {
        desc.textContent = 'Please specify the family member and the information that needs to be updated.';
        fieldsGroup.style.display = 'none';
    } else {
        desc.textContent = 'Select the fields you want to update and provide the new information below.';
        fieldsGroup.style.display = 'block';
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
