@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <h2>User Record</h2>
        <p class="page-subtitle">Viewing profile for <strong>{{ $user->name }}</strong></p>
    </div>
    <div style="display:flex;gap:.5rem;align-items:center;">
        @if(Auth::id() === $user->id || Auth::user()->isAdmin())
        <button class="btn btn-primary btn-sm" onclick="openModal('editProfileModal')">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:4px;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Edit Profile
        </button>
        @endif
        <a href="{{ route('users.index') }}" class="btn btn-ghost btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Back to User Accounts
        </a>
    </div>
</div>

@if (!$user->staff_id)
<div class="card" style="padding:1.5rem;max-width:640px;">
    <div style="display:flex;align-items:center;gap:.75rem;color:#92400e;background:#fef3c7;border:1px solid #fde68a;border-radius:8px;padding:1rem 1.25rem;margin-bottom:1.5rem;">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        No staff registry record linked to this account. Assign a Staff No to link automatically.
    </div>
    <div>
        <div style="font-size:.72rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:.3rem;">Login Staff No</div>
        <code style="font-size:.95rem;color:var(--primary);">{{ $user->staff_no ?: '—' }}</code>
    </div>
</div>
@else
@php $s = $user->staff; @endphp
<div class="card" style="padding:2rem;max-width:640px;">
    <div style="display:flex;gap:1.25rem;align-items:center;margin-bottom:2rem;padding-bottom:1.5rem;border-bottom:1px solid var(--border);">
        <div style="width:64px;height:64px;border-radius:50%;background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.6rem;font-weight:700;flex-shrink:0;overflow:hidden;position:relative;">
            @if($user->avatar && Storage::disk('public')->exists($user->avatar))
                <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" style="width:100%;height:100%;object-fit:cover;">
            @else
                {{ strtoupper(substr($s->name, 0, 1)) }}
            @endif
        </div>
        <div>
            <div style="font-size:1.2rem;font-weight:700;color:var(--text);">{{ $s->name }}</div>
            <div style="color:var(--text-muted);font-size:.9rem;margin-top:.2rem;">{{ $s->position ?? '—' }}</div> 
            <div style="margin-top:.4rem;display:flex;gap:.4rem;align-items:center;">
                <span class="company-badge company-{{ strtolower($s->company ?? '') }}">{{ $s->company ?? '' }}</span>
                <span class="role-badge {{ str_replace('_','-',$user->role) }}">{{ $user->getRoleLabel() }}</span>
            </div>
        </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
        <div>
            <div class="profile-lbl">Staff No</div>
            <code style="font-size:.95rem;color:var(--primary);">{{ $s->staff_no }}</code>
        </div>
        <div>
            <div class="profile-lbl">Department</div>
            <span class="dept-badge">{{ $s->department->name ?? '—' }}</span>
        </div>
        <div>
            <div class="profile-lbl">Email</div>
            <div style="font-size:.9rem;color:var(--text);">{{ $s->email ?: '—' }}</div>
        </div>
        <div>
            <div class="profile-lbl">Company</div>
            <div style="font-size:.9rem;color:var(--text);">
                @php
                    $companyNames = [
                        'FJB'  => 'FGV Johor Bulkers',
                        'FBSB' => 'FGV Bulkers Sdn Bhd',
                        'LBSB' => 'Langsat Bulkers Sdn Bhd',
                        'FGT'  => 'FGV Transport'
                    ];
                @endphp
                {{ $companyNames[strtoupper($s->company)] ?? $s->company }}
            </div>
        </div>
        <div>
            <div class="profile-lbl">Date Joined</div>
            <div style="font-size:.9rem;color:var(--text);">
                {{ $s->date_joined ? \Carbon\Carbon::parse($s->date_joined)->format('d M Y') : '—' }}
            </div>
        </div>
        <div>
            <div class="profile-lbl">Account Status</div>
            @if($user->is_active)
                <span class="status-badge status-completed">Active</span>
            @else
                <span class="status-badge status-scheduled">Inactive</span>
            @endif
        </div>
    </div>
    <div style="margin-top:1.75rem;padding-top:1.25rem;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
        <p style="font-size:.8rem;color:var(--text-muted);margin:0;">
            To edit registry details, go to <a href="{{ route('staff.index') }}" style="color:var(--primary);">Staff Registry</a>.
        </p>
        <a href="{{ route('staff.show', $s->id) }}" class="btn btn-outline btn-sm">View in Staff Registry</a>
    </div>
</div>
@endif

@if ($user->staff_id)
<!-- Training Records -->
<div class="card card-sticky" style="margin-top:1.5rem;max-width:800px;">
    <div class="card-header">
        <h3>Training Records <span style="font-size:.8rem;font-weight:400;color:var(--text-muted);">({{ $trainings->count() }})</span></h3>
    </div>
    @if ($trainings->isEmpty())
    <div style="padding:1.5rem;color:var(--text-muted);font-size:.9rem;">No training records.</div>
    @else
    <div class="table-wrap table-wrap-sticky">
        <table class="table table-sticky">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($trainings as $t)
                <tr>
                    <td>
                        @if($t->course)
                        <a href="{{ route('training.index', ['view' => 'course', 'course_id' => $t->course_id]) }}" style="text-decoration:none;">
                            <code style="font-size:.82rem;color:#6366f1;border-bottom:1px dashed #6366f1;">{{ $t->course->code }}</code>
                        </a>
                        @else
                        <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td style="font-size:.85rem;">{{ $t->course->title ?? '—' }}</td>
                    <td><span class="type-badge type-{{ strtolower($t->training_type ?? 'external') }}">{{ $t->training_type ?? 'External' }}</span></td>
                    <td><span class="status-badge status-{{ strtolower(str_replace(' ','-',$t->status)) }}">{{ $t->status }}</span></td>    
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

<!-- My Travel Records -->
<div class="card card-sticky" style="margin-top:1.5rem;max-width:800px;">
    <div class="card-header">
        <h3>My Travel <span style="font-size:.8rem;font-weight:400;color:var(--text-muted);">({{ $user->staff?->travelRecords->count() ?? 0 }})</span></h3>   
    </div>
    @if (!$user->staff || $user->staff->travelRecords->isEmpty())
    <div style="padding:1.5rem;color:var(--text-muted);font-size:.9rem;">No travel records.</div>
    @else
    <div class="table-wrap table-wrap-sticky">
        <table class="table table-sticky">
            <thead>
                <tr>
                    <th>Destination</th>
                    <th>Purpose</th>
                    <th>Departure</th>
                    <th>Return</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($user->staff?->travelRecords ?? [] as $tr)
                <tr>
                    <td style="font-weight:500;">{{ $tr->destination }}</td>
                    <td style="font-size:.85rem;color:var(--text-muted);">{{ $tr->purpose ?? '—' }}</td>
                    <td style="font-size:.85rem;">{{ $tr->departure_date ? \Carbon\Carbon::parse($tr->departure_date)->format('d M Y') : '—' }}</td>
                    <td style="font-size:.85rem;">{{ $tr->return_date ? \Carbon\Carbon::parse($tr->return_date)->format('d M Y') : '—' }}</td>   
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

<!-- Room Booking Records -->
<div class="card card-sticky" style="margin-top:1.5rem;max-width:800px;">
    <div class="card-header">
        <h3>Room Bookings <span style="font-size:.8rem;font-weight:400;color:var(--text-muted);">({{ $user->bookings->count() }})</span></h3>    
    </div>
    @if ($user->bookings->isEmpty())
    <div style="padding:1.5rem;color:var(--text-muted);font-size:.9rem;">No room bookings.</div>
    @else
    <div class="table-wrap table-wrap-sticky">
        <table class="table table-sticky">
            <thead>
                <tr>
                    <th>Room</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Purpose</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($user->bookings as $bk)
                <tr>
                    <td style="font-weight:500;">{{ $bk->room->name }}</td>
                    <td style="font-size:.85rem;">{{ \Carbon\Carbon::parse($bk->booking_date)->format('d M Y') }}</td>
                    <td style="font-size:.85rem;white-space:nowrap;">{{ \Carbon\Carbon::parse($bk->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($bk->end_time)->format('H:i') }}</td>
                    <td style="font-size:.85rem;color:var(--text-muted);">{{ $bk->purpose ?? '—' }}</td>
                    <td><span class="status-badge status-{{ strtolower(str_replace(' ','-',$bk->status)) }}">{{ $bk->status }}</span></td>  
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endif

<!-- Edit Profile Modal -->
@if(Auth::id() === $user->id || Auth::user()->isAdmin())
<div class="modal" id="editProfileModal">
    <div class="modal-box" style="max-width:520px;">
        <div class="modal-header ur-modal-header" style="background: var(--primary); padding: 1.25rem 1.5rem; border-radius: 14px 14px 0 0;">
            <div style="display:flex;align-items:center;gap:.75rem;">
                <div class="ur-header-icon" style="width: 34px; height: 34px; border-radius: 9px; background: rgba(255,255,255,.15); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </div>
                <div>
                    <h3 style="color:white;margin:0;font-size:.95rem;">Edit Profile Information</h3>
                    <p style="color:rgba(255,255,255,.6);font-size:.75rem;margin:.1rem 0 0;">
                        Username/Staff No: <strong style="color:rgba(255,255,255,.9);">{{ $user->staff_no ?: $user->email }}</strong>
                    </p>
                </div>
            </div>
            <button class="modal-close" onclick="closeModal()" style="color:rgba(255,255,255,.6);font-size:1.4rem; background: none; border: none; cursor: pointer;">×</button>
        </div>

        @if(Auth::user()->isAdmin())
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body" style="display: flex; flex-direction: column; gap: 1rem; padding: 1.5rem;">
                <div class="form-group">
                    <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); display: block; margin-bottom: 0.3rem;">Full Name *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 6px; background: var(--form-input-bg); color: var(--form-input-color);">
                </div>

                <div class="form-group">
                    <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); display: block; margin-bottom: 0.3rem;">Email *</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 6px; background: var(--form-input-bg); color: var(--form-input-color);">
                </div>

                @if($user->staff)
                <div class="form-group">
                    <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); display: block; margin-bottom: 0.3rem;">Phone Number</label>
                    <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number', $user->staff->phone_number) }}" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 6px; background: var(--form-input-bg); color: var(--form-input-color);">
                </div>

                <div class="form-group">
                    <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); display: block; margin-bottom: 0.3rem;">Gender</label>
                    <select name="gender" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 6px; background: var(--form-input-bg); color: var(--form-input-color);">
                        <option value="">Select</option>
                        <option value="Male" {{ old('gender', $user->staff->gender) === 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender', $user->staff->gender) === 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender', $user->staff->gender) === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); display: block; margin-bottom: 0.3rem;">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $user->staff->date_of_birth ? date('Y-m-d', strtotime($user->staff->date_of_birth)) : '') }}" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 6px; background: var(--form-input-bg); color: var(--form-input-color);">
                </div>

                <div class="form-group">
                    <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); display: block; margin-bottom: 0.3rem;">IC Number</label>
                    <input type="text" name="ic_number" class="form-control" value="{{ old('ic_number', $user->staff->ic_number) }}" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 6px; background: var(--form-input-bg); color: var(--form-input-color);">
                </div>
                @endif

                <div class="form-group">
                    <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); display: block; margin-bottom: 0.3rem;">Profile Picture</label>
                    <input type="file" name="avatar" class="form-control" accept="image/*" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 6px; background: var(--form-input-bg); color: var(--form-input-color);">
                    <small style="color: var(--muted); font-size: 0.75rem; display: block; margin-top: 0.25rem;">Max file size: 2MB. Accepts images (JPG, PNG, WebP, etc.)</small>
                </div>
            </div>

            <div class="modal-footer" style="padding: 1rem 1.5rem; display: flex; justify-content: flex-end; gap: 0.5rem; border-top: 1px solid var(--border);">
                <button type="button" class="btn btn-ghost" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
        @else
        <div class="modal-body" style="padding: 1.5rem; padding-bottom: 0;">
            <p style="font-size:0.9rem; color:var(--text-muted); margin-bottom:1rem; line-height:1.5;">
                You can update your profile picture directly. For other data changes, please submit an update request to Human Resources.
            </p>
            
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border);">
                @csrf
                <input type="hidden" name="name" value="{{ $user->name }}">
                <input type="hidden" name="email" value="{{ $user->email }}">
                
                <div class="form-group">
                    <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); display: block; margin-bottom: 0.3rem;">Profile Picture</label>
                    <div style="display:flex; gap:10px; align-items:center;">
                        <input type="file" name="avatar" class="form-control" accept="image/*" style="flex:1; padding: 0.5rem; border: 1px solid var(--border); border-radius: 6px; background: var(--form-input-bg); color: var(--form-input-color);">
                        <button type="submit" class="btn btn-primary btn-sm">Upload</button>
                    </div>
                    <small style="color: var(--muted); font-size: 0.75rem; display: block; margin-top: 0.25rem;">Max file size: 2MB. Accepts JPG, PNG, WebP.</small>
                </div>
            </form>
            
            <form action="{{ route('requests.store') }}" method="POST">
                @csrf
                <input type="hidden" name="record_type" value="Staff Data">
                <input type="hidden" name="record_id" value="{{ $user->id }}">
                
                <div class="form-group">
                    <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); display: block; margin-bottom: 0.3rem;">Update Details (HR Request) *</label>
                    <textarea name="message" class="form-control" required rows="4" placeholder="E.g. Please update my phone number to 012-3456789..." style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 6px; background: var(--form-input-bg); color: var(--form-input-color);"></textarea>
                </div>
        </div>

        <div class="modal-footer" style="padding: 1rem 1.5rem; display: flex; justify-content: flex-end; gap: 0.5rem; border-top: 1px solid var(--border);">
            <button type="button" class="btn btn-ghost" onclick="closeModal()">Cancel</button>
            <button type="button" class="btn btn-primary" onclick="this.closest('.modal-box').querySelector('form[action=\'{{ route('requests.store') }}\']').submit()">Submit Update Request</button>
        </div>
            </form>
        @endif
    </div>
</div>
@endif

<style>
.profile-lbl { font-size:.72rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:.3rem; }
</style>
@endsection
