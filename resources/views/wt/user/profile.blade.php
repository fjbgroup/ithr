@extends('wt.layouts.user')

@section('title', 'My Profile')
@section('page_title', 'My Profile')

@push('styles')
<style>
.profile-hero{background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:28px 32px;margin-bottom:24px;display:flex;align-items:center;gap:24px;flex-wrap:wrap}
.profile-hero-initial{width:72px;height:72px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;font-family:'DM Sans',sans-serif;font-size:26px;font-weight:800;color:#fff;flex-shrink:0}
.profile-section-card{background:var(--surface);border:1px solid var(--border);border-radius:12px;overflow:hidden;margin-bottom:20px;box-shadow:0 1px 3px rgba(0,0,0,.06)}
.profile-section-header{padding:16px 22px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:9px}
.profile-section-title{font-size:14px;font-weight:700;color:var(--text)}
.profile-section-body{padding:22px}
.account-row{display:flex;justify-content:space-between;align-items:center;padding:11px 0;border-bottom:1px solid var(--border)}
.account-row:last-child{border-bottom:none}
.account-row-label{font-size:13px;color:var(--muted);font-weight:500}
.account-row-value{font-size:13px;font-weight:600;color:var(--text)}
</style>
@endpush

@section('content')
@php $user = Auth::guard('wt')->user(); @endphp

{{-- Profile Hero --}}
<div class="profile-hero">
  <div class="profile-hero-initial">{{ strtoupper(substr($user->username ?? 'U', 0, 1)) }}</div>
  <div style="flex:1;min-width:0">
    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:4px">Your Account</div>
    <div style="font-size:20px;font-weight:800;color:var(--text);line-height:1.1">{{ strtoupper($user->full_name ?: $user->username) }}</div>
    <div style="display:flex;align-items:center;gap:10px;margin-top:8px;flex-wrap:wrap">
      <span style="background:rgba(2,132,199,.12);color:var(--accent);border-radius:5px;padding:2px 10px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em">{{ strtoupper(str_replace('_',' ',$user->role ?? 'user')) }}</span>
      <span style="font-size:12px;color:var(--muted)"><i class="fas fa-user" style="font-size:11px;margin-right:3px"></i>{{ $user->username }}</span>
      @if($user->staff_id)
      <span style="font-size:12px;color:var(--muted)"><i class="fas fa-id-badge" style="font-size:11px;margin-right:3px"></i>{{ $user->staff_id }}</span>
      @endif
      @if($user->department)
      <span style="font-size:12px;color:var(--muted)"><i class="fas fa-building" style="font-size:11px;margin-right:3px"></i>{{ $user->department }}</span>
      @endif
    </div>
  </div>
</div>

@if(session('success'))
<div class="alert-success-custom mb-4"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

<div class="row g-4" style="max-width:960px">
  <div class="col-md-7">
    <div class="profile-section-card">
      <div class="profile-section-header">
        <i class="fas fa-user-edit" style="color:var(--accent);font-size:15px"></i>
        <div class="profile-section-title">Profile Information</div>
      </div>
      <div class="profile-section-body">
        <form action="{{ route('wt.user.profile.update') }}" method="POST">
          @csrf
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Staff ID</label>
              <input type="text" class="form-control" value="{{ $user->staff_id }}" readonly disabled>
            </div>
            <div class="col-md-6">
              <label class="form-label">Username</label>
              <input type="text" class="form-control" value="{{ $user->username }}" readonly disabled>
            </div>
            <div class="col-12">
              <label class="form-label">Full Name</label>
              <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $user->full_name) }}" placeholder="Enter your full name" required>
            </div>
            <div class="col-12">
              <label class="form-label">Phone No</label>
              <input type="text" name="phone_no" class="form-control" value="{{ old('phone_no', $user->phone_no) }}" placeholder="e.g. 012-3456789" data-preserve-case="true">
            </div>
            <div class="col-md-6">
              <label class="form-label">Department</label>
              <input type="text" name="department" list="department-options" class="form-control" value="{{ old('department', $user->department) }}" placeholder="e.g. IT Department" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Position</label>
              <input type="text" name="position" list="position-options" class="form-control" value="{{ old('position', $user->position) }}" placeholder="e.g. Staff" required>
            </div>
          </div>
          <div style="margin-top:20px;display:flex;justify-content:flex-end">
            <button type="submit" class="btn-primary-custom">
              <i class="fas fa-save"></i> Save Changes
            </button>
          </div>
        </form>
      </div>
    </div>

    {{-- Change Password --}}
    <div class="profile-section-card">
      <div class="profile-section-header">
        <i class="fas fa-lock" style="color:var(--accent);font-size:15px"></i>
        <div class="profile-section-title">Change Password</div>
      </div>
      <div class="profile-section-body">
        <form action="{{ route('wt.user.profile.password') }}" method="POST">
          @csrf
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">Current Password</label>
              <input type="password" name="current_password" class="form-control" placeholder="Enter current password" data-preserve-case="true">
            </div>
            <div class="col-md-6">
              <label class="form-label">New Password</label>
              <input type="password" name="password" class="form-control" placeholder="New password" data-preserve-case="true">
            </div>
            <div class="col-md-6">
              <label class="form-label">Confirm Password</label>
              <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm new password" data-preserve-case="true">
            </div>
          </div>
          <div style="margin-top:20px;display:flex;justify-content:flex-end">
            <button type="submit" class="btn-primary-custom">
              <i class="fas fa-key"></i> Update Password
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-md-5">
    <div class="profile-section-card">
      <div class="profile-section-header">
        <i class="fas fa-info-circle" style="color:var(--accent);font-size:15px"></i>
        <div class="profile-section-title">Account Info</div>
      </div>
      <div class="profile-section-body" style="padding:16px 22px">
        <div class="account-row">
          <span class="account-row-label">Staff ID</span>
          <span class="account-row-value">{{ $user->staff_id ?: '—' }}</span>
        </div>
        <div class="account-row">
          <span class="account-row-label">Username</span>
          <span class="account-row-value">{{ $user->username ?: '—' }}</span>
        </div>
        <div class="account-row">
          <span class="account-row-label">Role</span>
          <span class="account-row-value">{{ strtoupper(str_replace('_',' ',$user->role ?? 'user')) }}</span>
        </div>
        <div class="account-row">
          <span class="account-row-label">Department</span>
          <span class="account-row-value">{{ $user->department ?: '—' }}</span>
        </div>
        <div class="account-row">
          <span class="account-row-label">Position</span>
          <span class="account-row-value">{{ $user->position ?: '—' }}</span>
        </div>
        <div class="account-row">
          <span class="account-row-label">Phone</span>
          <span class="account-row-value">{{ $user->phone_no ?: '—' }}</span>
        </div>
        <div class="account-row">
          <span class="account-row-label">Member Since</span>
          <span class="account-row-value">{{ $user->created_at ? $user->created_at->format('d M Y') : '—' }}</span>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
