@extends('wt.layouts.user')

@section('title', 'My Profile')

@push('styles')
<style>
    .profile-shell {
        max-width: 1180px;
    }
    .profile-card {
        overflow: hidden;
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 18px;
        background: #162236;
        box-shadow: none;
    }
    .profile-aside {
        border-right: 1px solid rgba(148, 163, 184, 0.14);
        background: linear-gradient(180deg, #1d2b42 0%, #142033 100%);
        padding: 18px;
    }
    .profile-avatar {
        display: flex;
        width: 58px;
        height: 58px;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        border: 1px solid rgba(125, 211, 252, 0.24);
        background: #075985;
        color: #f8fafc;
        font-size: 24px;
        font-weight: 900;
    }
    .profile-name {
        margin-top: 14px;
        color: #f8fafc;
        font-size: 15px;
        font-weight: 900;
        line-height: 1.2;
        text-transform: uppercase;
    }
    .profile-role {
        margin-top: 5px;
        color: #93c5fd;
        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.16em;
        text-transform: uppercase;
    }
    .profile-meta {
        margin-top: 16px;
        display: grid;
        gap: 8px;
    }
    .profile-meta-item {
        border-radius: 10px;
        border: 1px solid rgba(148, 163, 184, 0.14);
        background: rgba(15, 23, 42, 0.36);
        padding: 10px;
    }
    .profile-meta-label,
    .profile-label {
        color: #94a3b8;
        font-size: 8px;
        font-weight: 900;
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }
    .profile-meta-value {
        margin-top: 4px;
        color: #e2e8f0;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
    }
    .profile-form {
        padding: 18px;
    }
    .profile-section-title {
        margin-bottom: 14px;
        color: #f8fafc;
        font-size: 12px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .profile-input {
        width: 100%;
        min-height: 38px;
        border-radius: 10px;
        border: 1px solid rgba(148, 163, 184, 0.22);
        background: #0f172a;
        padding: 9px 11px;
        color: #f8fafc;
        font-size: 11px;
        font-weight: 800;
        outline: none;
        transition: border-color 0.16s ease, box-shadow 0.16s ease;
    }
    .profile-input:focus {
        border-color: rgba(56, 189, 248, 0.62) !important;
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.12) !important;
    }
    .profile-input[disabled] {
        color: #94a3b8;
        cursor: not-allowed;
        background: rgba(15, 23, 42, 0.52);
    }
    .profile-save-btn {
        display: inline-flex;
        min-height: 36px;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border-radius: 10px;
        border: 1px solid rgba(56, 189, 248, 0.34);
        background: rgba(14, 165, 233, 0.14);
        padding: 9px 14px;
        color: #bae6fd;
        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        transition: background 0.16s ease, border-color 0.16s ease;
    }
    .profile-save-btn:hover {
        border-color: rgba(125, 211, 252, 0.56);
        background: rgba(14, 165, 233, 0.22);
    }
    @media (max-width: 768px) {
        .profile-aside {
            border-right: 0;
            border-bottom: 1px solid rgba(148, 163, 184, 0.14);
        }
    }
</style>
@endpush

@section('content')
<div class="page-header-block">
    <div>
        <h3 class="page-title-standard">My Profile</h3>
        <p class="page-subtitle-standard">
            Manage your personal information and account details.
        </p>
    </div>
</div>

<div class="px-2">
    @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl flex items-center gap-3">
        <i class="fas fa-check-circle"></i>
        <span class="font-bold text-sm">{{ session('success') }}</span>
    </div>
    @endif

    <div class="profile-shell mx-auto">
        <div class="profile-card grid grid-cols-1 md:grid-cols-[260px_1fr]">
            <aside class="profile-aside">
                <div class="profile-avatar">
                    {{ strtoupper(substr(Auth::guard('wt')->user()->username, 0, 1)) }}
                </div>
                <h4 class="profile-name">{{ strtoupper(Auth::guard('wt')->user()->full_name ?: Auth::guard('wt')->user()->username) }}</h4>
                <p class="profile-role">{{ strtoupper(Auth::guard('wt')->user()->role ?: 'User Account') }}</p>

                <div class="profile-meta">
                    <div class="profile-meta-item">
                        <div class="profile-meta-label">Staff ID</div>
                        <div class="profile-meta-value">{{ Auth::guard('wt')->user()->staff_id ?: '-' }}</div>
                    </div>
                    <div class="profile-meta-item">
                        <div class="profile-meta-label">Username</div>
                        <div class="profile-meta-value">{{ Auth::guard('wt')->user()->username ?: '-' }}</div>
                    </div>
                </div>
            </aside>

            <form action="{{ route('wt.user.profile.update') }}" method="POST" class="profile-form">
                @csrf
                <h4 class="profile-section-title">Account Details</h4>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="profile-label mb-1 block">Staff ID</label>
                        <input type="text" value="{{ Auth::guard('wt')->user()->staff_id }}" class="profile-input" readonly disabled>
                    </div>
                    <div>
                        <label class="profile-label mb-1 block">Username</label>
                        <input type="text" value="{{ Auth::guard('wt')->user()->username }}" class="profile-input" readonly disabled>
                    </div>
                    <div class="md:col-span-2">
                        <label class="profile-label mb-1 block">Full Name</label>
                        <input type="text" name="full_name" value="{{ old('full_name', Auth::guard('wt')->user()->full_name) }}" placeholder="Enter your full name" class="profile-input" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="profile-label mb-1 block">Phone No</label>
                        <input type="text" name="phone_no" value="{{ old('phone_no', Auth::guard('wt')->user()->phone_no) }}" placeholder="e.g. 012-3456789" class="profile-input">
                    </div>
                    <div>
                        <label class="profile-label mb-1 block">Department</label>
                        <input type="text" name="department" list="department-options" value="{{ old('department', Auth::guard('wt')->user()->department) }}" placeholder="e.g. IT Department" class="profile-input" required>
                    </div>
                    <div>
                        <label class="profile-label mb-1 block">Position</label>
                        <input type="text" name="position" list="position-options" value="{{ old('position', Auth::guard('wt')->user()->position) }}" placeholder="e.g. Staff" class="profile-input" required>
                    </div>
                </div>

                <div class="mt-5 flex justify-end">
                    <button type="submit" class="profile-save-btn">
                        Save Changes <i class="fas fa-save"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


