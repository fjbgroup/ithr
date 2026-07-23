@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <h2>User Accounts</h2>
        <p class="page-subtitle">{{ $users->count() }} user(s) — Manage system access</p>
    </div>
    @canwrite<button class="btn btn-primary" onclick="openAddUserPanel()">+ Add User</button>@endcanwrite
</div>

<!-- Staff Registry Search Panel -->
<div id="staffSearchPanel" style="display:none;margin-bottom:1.5rem;">
    <div class="card" style="border:1.5px solid var(--sky,#38bdf8);">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
            <strong style="font-size:.95rem;">Search Staff Registry to create user account</strong>
            <button class="btn btn-ghost btn-sm" onclick="closeStaffPanel()">✕ Close</button>
        </div>
        <div style="display:flex;gap:.75rem;flex-wrap:wrap;margin-bottom:1rem;">
            <input type="text" id="srStaffId" placeholder="Staff ID / No" oninput="filterStaff()" style="flex:1;min-width:140px;">
            <input type="text" id="srName" placeholder="Name" oninput="filterStaff()" style="flex:2;min-width:180px;">
            <select id="srDept" onchange="filterStaff()" style="flex:1;min-width:160px;">
                <option value="">All Departments</option>
                @foreach ($departments as $d)
                <option value="{{ $d->id }}">[{{ $d->company ?? '' }}] {{ $d->name }}</option>
                @endforeach
            </select>
        </div>
        <div id="srResults" style="max-height:260px;overflow-y:auto;">
            <p style="color:var(--muted);font-size:.875rem;text-align:center;padding:1rem;">Type to search staff…</p>
        </div>
    </div>
</div>

@if (auth()->user()->isHrUser())
<div class="card" style="margin-bottom:1.5rem;max-width:560px;">
    <div style="padding:.85rem 1.25rem;display:flex;align-items:center;gap:.75rem;">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" style="flex-shrink:0;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        <div style="flex:1;">
            <span style="font-size:.88rem;font-weight:600;">Microsoft Authenticator</span>
            @if (auth()->user()->hasTotpSetup())
                <span class="status-badge status-completed" style="margin-left:.5rem;">Configured</span>
            @else
                <span style="font-size:.8rem;color:#92400e;background:#fef3c7;padding:.2rem .5rem;border-radius:.3rem;margin-left:.5rem;">Not set up</span>
            @endif
        </div>
        <a href="{{ route('account.security') }}" class="btn btn-sm btn-outline">Manage</a>
    </div>
</div>
@endif

@if (auth()->user()->isAdminIT())
<style>
.sys-switch { position: relative; display: inline-block; width: 44px; height: 24px; flex-shrink:0; }
.sys-switch input { opacity: 0; width: 0; height: 0; }
.sys-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #fca5a5; transition: .3s; border-radius: 24px; }
.sys-slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .3s; border-radius: 50%; box-shadow: 0 1px 2px rgba(0,0,0,0.2); }
input:checked + .sys-slider { background-color: #10b981; }
input:checked + .sys-slider:before { transform: translateX(20px); }
</style>
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">

@php $emailOn = \App\Models\IT\EmailSetting::emailEnabled(); @endphp
<div class="card" style="border:1px solid {{ $emailOn ? 'var(--border)' : '#fca5a5' }};">
    <div style="padding:.85rem 1.25rem;display:flex;align-items:center;gap:.75rem;">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="{{ $emailOn ? '#0284c7' : '#dc2626' }}" stroke-width="2" style="flex-shrink:0;"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 5L2 7"/></svg>
        <div style="flex:1;">
            <span style="font-size:.88rem;font-weight:600;">System Email Sending</span>
            @if ($emailOn)
                <span class="status-badge status-completed" style="margin-left:.5rem;">Enabled</span>
            @else
                <span style="font-size:.8rem;color:#991b1b;background:#fee2e2;padding:.2rem .5rem;border-radius:.3rem;margin-left:.5rem;">Disabled</span>
            @endif
            <div style="font-size:.78rem;color:var(--muted,#64748b);margin-top:.2rem;">
                {{ $emailOn
                    ? 'The system can send email (OTP, notifications). Disable to stop all outgoing email temporarily.'
                    : 'All outgoing email is paused. Authenticator (2FA) users are unaffected.' }}
            </div>
        </div>
        <form method="POST" action="{{ route('system.email.toggle') }}" style="margin:0;">
            @csrf
            <input type="hidden" name="enable" value="{{ $emailOn ? 0 : 1 }}">
            <label class="sys-switch">
                <input type="checkbox" onchange="this.form.submit()" {{ $emailOn ? 'checked' : '' }}>
                <span class="sys-slider"></span>
            </label>
        </form>
    </div>
</div>

@php $totpOn = \App\Models\IT\EmailSetting::totpEnabled(); @endphp
<div class="card" style="border:1px solid {{ $totpOn ? 'var(--border)' : '#fca5a5' }};">
    <div style="padding:.85rem 1.25rem;display:flex;align-items:center;gap:.75rem;">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="{{ $totpOn ? '#6366f1' : '#dc2626' }}" stroke-width="2" style="flex-shrink:0;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        <div style="flex:1;">
            <span style="font-size:.88rem;font-weight:600;">Microsoft Authenticator (2FA)</span>
            @if ($totpOn)
                <span class="status-badge status-completed" style="margin-left:.5rem;">Enabled</span>
            @else
                <span style="font-size:.8rem;color:#991b1b;background:#fee2e2;padding:.2rem .5rem;border-radius:.3rem;margin-left:.5rem;">Disabled</span>
            @endif
            <div style="font-size:.78rem;color:var(--muted,#64748b);margin-top:.2rem;">
                {{ $totpOn
                    ? 'Microsoft Authenticator (2FA) is active for login and password reset. Turn off to skip it system-wide.'
                    : '2FA is turned off system-wide. Login and password reset skip the authenticator until you turn it back on. Existing user setups are kept.' }}
            </div>
        </div>
        <form method="POST" action="{{ route('system.totp.toggle') }}" style="margin:0;">
            @csrf
            <input type="hidden" name="enable" value="{{ $totpOn ? 0 : 1 }}">
            <label class="sys-switch">
                <input type="checkbox" onchange="this.form.submit()" {{ $totpOn ? 'checked' : '' }}>
                <span class="sys-slider"></span>
            </label>
        </form>
    </div>
</div>

@php $reqStaffOn = \App\Models\IT\EmailSetting::requireStaffRegistry(); @endphp
<div class="card" style="border:1px solid {{ $reqStaffOn ? 'var(--border)' : '#fca5a5' }};">
    <div style="padding:.85rem 1.25rem;display:flex;align-items:center;gap:.75rem;">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="{{ $reqStaffOn ? '#6366f1' : '#dc2626' }}" stroke-width="2" style="flex-shrink:0;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        <div style="flex:1;">
            <span style="font-size:.88rem;font-weight:600;">Require Staff Registry for New Accounts</span>
            @if ($reqStaffOn)
                <span class="status-badge status-completed" style="margin-left:.5rem;">Enabled</span>
            @else
                <span style="font-size:.8rem;color:#991b1b;background:#fee2e2;padding:.2rem .5rem;border-radius:.3rem;margin-left:.5rem;">Disabled</span>
            @endif
            <div style="font-size:.78rem;color:var(--muted,#64748b);margin-top:.2rem;">
                {{ $reqStaffOn
                    ? 'New user accounts in IT, Walkie Talkie, and HR systems MUST be linked to an existing Staff Registry record.'
                    : 'Unlinked user accounts can be created manually in the system.' }}
            </div>
        </div>
        <form method="POST" action="{{ route('system.require-staff.toggle') }}" style="margin:0;">
            @csrf
            <input type="hidden" name="enable" value="{{ $reqStaffOn ? 0 : 1 }}">
            <label class="sys-switch">
                <input type="checkbox" onchange="this.form.submit()" {{ $reqStaffOn ? 'checked' : '' }}>
                <span class="sys-slider"></span>
            </label>
        </form>
    </div>
</div>

<!-- IT SYSTEM TOGGLE -->
@php $itOn = \App\Models\IT\EmailSetting::systemEnabled('it'); @endphp
<div class="card" style="border:1px solid {{ $itOn ? 'var(--border)' : '#fca5a5' }};">
    <div style="padding:.85rem 1.25rem;display:flex;align-items:center;gap:.75rem;">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="{{ $itOn ? '#6366f1' : '#dc2626' }}" stroke-width="2" style="flex-shrink:0;"><rect x="4" y="4" width="16" height="16" rx="2" ry="2"/><rect x="9" y="9" width="6" height="6"/></svg>
        <div style="flex:1;">
            <span style="font-size:.88rem;font-weight:600;">IT System Status</span>
            @if ($itOn)
                <span class="status-badge status-completed" style="margin-left:.5rem;">Online</span>
            @else
                <span style="font-size:.8rem;color:#991b1b;background:#fee2e2;padding:.2rem .5rem;border-radius:.3rem;margin-left:.5rem;">Maintenance Mode</span>
            @endif
            <div style="font-size:.78rem;color:var(--muted,#64748b);margin-top:.2rem;">
                {{ $itOn
                    ? 'The IT System is active and available to all users.'
                    : 'The IT System is offline. Only ADMIN IT can access it.' }}
            </div>
        </div>
        <form method="POST" action="{{ route('system.status.toggle', 'it') }}" style="margin:0;">
            @csrf
            <input type="hidden" name="enable" value="{{ $itOn ? 0 : 1 }}">
            <label class="sys-switch">
                <input type="checkbox" onchange="this.form.submit()" {{ $itOn ? 'checked' : '' }}>
                <span class="sys-slider"></span>
            </label>
        </form>
    </div>
</div>

<!-- WT SYSTEM TOGGLE -->
@php $wtOn = \App\Models\IT\EmailSetting::systemEnabled('wt'); @endphp
<div class="card" style="border:1px solid {{ $wtOn ? 'var(--border)' : '#fca5a5' }};">
    <div style="padding:.85rem 1.25rem;display:flex;align-items:center;gap:.75rem;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="{{ $wtOn ? '#6366f1' : '#dc2626' }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><rect x="7" y="9" width="10" height="13" rx="2"/><path d="M9 9V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v5"/><path d="M10 3V2"/><circle cx="12" cy="16" r="2"/></svg>
        <div style="flex:1;">
            <span style="font-size:.88rem;font-weight:600;">Walkie Talkie System Status</span>
            @if ($wtOn)
                <span class="status-badge status-completed" style="margin-left:.5rem;">Online</span>
            @else
                <span style="font-size:.8rem;color:#991b1b;background:#fee2e2;padding:.2rem .5rem;border-radius:.3rem;margin-left:.5rem;">Maintenance Mode</span>
            @endif
            <div style="font-size:.78rem;color:var(--muted,#64748b);margin-top:.2rem;">
                {{ $wtOn
                    ? 'The Walkie Talkie System is active and available to all users.'
                    : 'The Walkie Talkie System is offline. Only ADMIN IT can access it.' }}
            </div>
        </div>
        <form method="POST" action="{{ route('system.status.toggle', 'wt') }}" style="margin:0;">
            @csrf
            <input type="hidden" name="enable" value="{{ $wtOn ? 0 : 1 }}">
            <label class="sys-switch">
                <input type="checkbox" onchange="this.form.submit()" {{ $wtOn ? 'checked' : '' }}>
                <span class="sys-slider"></span>
            </label>
        </form>
    </div>
</div>

<!-- LMS SYSTEM TOGGLE -->
@php $lmsOn = \App\Models\IT\EmailSetting::systemEnabled('lms'); @endphp
<div class="card" style="border:1px solid {{ $lmsOn ? 'var(--border)' : '#fca5a5' }};">
    <div style="padding:.85rem 1.25rem;display:flex;align-items:center;gap:.75rem;">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="{{ $lmsOn ? '#6366f1' : '#dc2626' }}" stroke-width="2" style="flex-shrink:0;"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg>
        <div style="flex:1;">
            <span style="font-size:.88rem;font-weight:600;">LMS System Status</span>
            @if ($lmsOn)
                <span class="status-badge status-completed" style="margin-left:.5rem;">Online</span>
            @else
                <span style="font-size:.8rem;color:#991b1b;background:#fee2e2;padding:.2rem .5rem;border-radius:.3rem;margin-left:.5rem;">Maintenance Mode</span>
            @endif
            <div style="font-size:.78rem;color:var(--muted,#64748b);margin-top:.2rem;">
                {{ $lmsOn
                    ? 'The LMS System is active and available to all users.'
                    : 'The LMS System is offline. Only ADMIN IT can access it.' }}
            </div>
        </div>
        <form method="POST" action="{{ route('system.status.toggle', 'lms') }}" style="margin:0;">
            @csrf
            <input type="hidden" name="enable" value="{{ $lmsOn ? 0 : 1 }}">
            <label class="sys-switch">
                <input type="checkbox" onchange="this.form.submit()" {{ $lmsOn ? 'checked' : '' }}>
                <span class="sys-slider"></span>
            </label>
        </form>
    </div>
</div>

</div>
@endif

<div style="display:flex;gap:.75rem;flex-wrap:wrap;align-items:center;margin-bottom:1rem;">
    <div class="app-search" style="min-width:220px;">
        <svg class="app-search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="userSearch" placeholder="Search name, email, staff no…" oninput="filterUsers()">
    </div>
    <select id="userRoleFilter" onchange="filterUsers()" style="min-width:150px;">
        <option value="">All Roles</option>
        <option value="staff">Staff</option>
        <option value="admin_hr">Admin (HR)</option>
        <option value="admin_it">Admin (IT)</option>
        <option value="ceo">CEO</option>
    </select>
    <select id="userStatusFilter" onchange="filterUsers()" style="min-width:130px;">
        <option value="">All Status</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
    </select>
    <span id="userFilterCount" style="font-size:.82rem;color:var(--muted);white-space:nowrap;"></span>
</div>

<div class="card card-sticky">
    <div class="table-wrap table-wrap-sticky">
        <table class="table table-sticky users-table">
            <thead>
                <tr>
                    <th style="width: 28%;">Name</th>
                    <th style="width: 28%;">Email</th>
                    <th style="white-space: nowrap;">Role</th>
                    <th style="white-space: nowrap;">Staff No</th>
                    <th>Department</th>
                    <th style="white-space: nowrap;">Status</th>
                    <th style="width: 140px; min-width: 140px; text-align: right; white-space: nowrap;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $u)
                <tr data-name="{{ strtolower($u->name) }}" data-email="{{ strtolower($u->email) }}" data-staffno="{{ strtolower($u->staff_no ?? '') }}" data-dept="{{ strtolower($u->department->name ?? $u->staff?->department?->name ?? '') }}" data-role="{{ $u->role }}" data-status="{{ $u->is_active ? 'active' : 'inactive' }}">
                    <td style="white-space: nowrap;"><strong>{{ $u->name }}</strong></td>
                    <td style="font-size:.85rem; white-space: nowrap;">{{ $u->email }}</td>
                    <td><span class="role-badge {{ str_replace('_','-',$u->role) }}">{{ $u->getRoleLabel() }}</span></td>
                    <td>
                        @if ($u->staff_id)
                        <a href="{{ route('staff.show', $u->staff_id) }}" style="text-decoration:none;">
                            <code style="font-size:.82rem;color:#6366f1;border-bottom:1px dashed #6366f1;">{{ $u->staff_no ?? '—' }}</code>
                        </a>
                        @else
                        <code style="font-size:.82rem;color:#6366f1;">{{ $u->staff_no ?? '—' }}</code>
                        @endif
                    </td>
                    <td>{{ $u->department->name ?? $u->staff?->department?->name ?? '—' }}</td>
                    <td>
                        @if($u->is_active)
                        <span class="status-badge status-completed">Active</span>
                        @else
                        <span class="status-badge status-scheduled">Inactive</span>
                        @endif
                        @if($u->isStaff() && $u->staff && !$u->staff->is_active)
                        <br><span class="status-badge" style="font-size:.68rem;margin-top:.25rem;background:#fee2e2;color:#991b1b;">HR Inactive</span>
                        @endif
                    </td>
                    <td class="td-actions" style="white-space: nowrap; text-align: right;">
                        @if ($u->isStaff() && $u->staff_id)
                        <a href="{{ route('users.show', $u->id) }}" class="btn btn-sm btn-ghost">View</a>
                        @endif
                        @canwrite
                        <button class="btn btn-sm btn-outline" onclick="editUser({{ json_encode($u) }})">Edit</button>
                        @endcanwrite
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit User Modal -->
<div class="modal" id="addUserModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 id="userModalTitle">Add User</h3>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <form id="userForm" method="POST" action="{{ route('users.store') }}">
            @csrf
            <div id="methodField"></div>
            <input type="hidden" name="id" id="userId" value="">
            <div class="form-grid">
                <div class="form-group"><label>Full Name *</label><input type="text" name="name" id="f_uname" required></div>
                <div class="form-group"><label>Email *</label><input type="email" name="email" id="f_uemail" required></div>
                <div class="form-group"><label>Role *</label>
                    <select name="role" id="f_urole" required>
                        <option value="staff">Staff</option>
                        <option value="admin_hr">Admin (HR)</option>
                        <option value="admin_it">Admin (IT)</option>
                        <option value="ceo">CEO</option>
                    </select>
                </div>
                <div class="form-group"><label>Staff No</label><input type="text" name="staff_no" id="f_ustaffno"></div>
                <div class="form-group"><label>Department</label>
                    <select name="department_id" id="f_udept">
                        <option value="">— None —</option>
                        @foreach ($departments as $d)
                        <option value="{{ $d->id }}">[{{ $d->company }}] {{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group"><label>Position</label>
                    <input type="text" name="position" id="f_upos" list="positions_list_u" autocomplete="off">
                    <datalist id="positions_list_u">
                        @foreach ($positions as $pos)
                        <option value="{{ $pos->title }}">
                        @endforeach
                    </datalist>
                </div>
                <div class="form-group form-full">
                    <label>Password <span id="pwLabel">(required for new user)</span></label>
                    <input type="password" name="password" id="f_upw" placeholder="Leave blank to keep unchanged">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save User</button>
            </div>
        </form>
        <div id="editDangerZone" style="display:none;border-top:1px solid var(--border);padding:.9rem 1.5rem;gap:.5rem;align-items:center;flex-wrap:wrap;">
            <button type="button" id="editResetPwBtn" class="btn btn-sm btn-outline" style="color:#b45309; border-color:#fcd34d; background:#fffbeb;">Reset Password</button>
            <button type="button" id="editDisableBtn" class="btn btn-sm" style="background:#fef3c7;color:#92400e;">Disable</button>
            <button type="button" id="editStaffBtn" class="btn btn-sm" style="display:none;background:#fce7f3;color:#9d174d;">Set Inactive</button>
            <div style="flex:1;"></div>
            <button type="button" id="editDeleteBtn" class="btn btn-sm btn-danger">Delete</button>
        </div>
    </div>
</div>

<div class="modal" id="deleteUserModal">
    <div class="modal-box modal-sm">
        <div class="modal-header">
            <h3>Delete User</h3>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <p style="padding:1rem 0;">Delete this user account? This cannot be undone.</p>
        <form id="deleteUserForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-danger">Delete</button>
            </div>
        </form>
    </div>
</div>

<div class="modal" id="toggleStaffModal">
    <div class="modal-box modal-sm">
        <div class="modal-header">
            <h3 id="toggleStaffModalTitle">Set Staff Inactive</h3>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <p id="toggleStaffModalBody" style="padding:1rem 0;"></p>
        <form id="toggleStaffForm" method="POST" action="">
            @csrf
            @method('PATCH')
            <input type="hidden" name="is_active" id="toggleStaffActiveValue" value="">
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal()">Cancel</button>
                <button type="submit" id="toggleStaffConfirmBtn" class="btn btn-primary">Confirm</button>
            </div>
        </form>
    </div>
</div>

<div class="modal" id="toggleUserModal">
    <div class="modal-box modal-sm">
        <div class="modal-header">
            <h3 id="toggleModalTitle">Disable Access</h3>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <p id="toggleModalBody" style="padding:1rem 0;"></p>
        <form id="toggleUserForm" method="POST" action="">
            @csrf
            @method('PATCH')
            <input type="hidden" name="is_active" id="toggleActiveValue" value="">
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal()">Cancel</button>
                <button type="submit" id="toggleConfirmBtn" class="btn btn-primary">Confirm</button>
            </div>
        </form>
    </div>
</div>

<form id="resetPwUserForm" method="POST" action="" style="display:none;">
    @csrf
</form>

<script>
const existingStaffNos = {!! json_encode($users->pluck('staff_no')->filter()->values()) !!};

function openAddUserPanel() {
    document.getElementById('staffSearchPanel').style.display = 'block';
    document.getElementById('srStaffId').focus();
}

function closeStaffPanel() {
    document.getElementById('staffSearchPanel').style.display = 'none';
    document.getElementById('srResults').innerHTML = '<p style="color:var(--muted);font-size:.875rem;text-align:center;padding:1rem;">Type to search staff…</p>';
    document.getElementById('srStaffId').value = '';
    document.getElementById('srName').value = '';
    document.getElementById('srDept').value = '';
}

let srTimer = null;
function filterStaff() {
    clearTimeout(srTimer);
    srTimer = setTimeout(doSearch, 250);
}

function doSearch() {
    const sid = document.getElementById('srStaffId').value.trim();
    const name = document.getElementById('srName').value.trim();
    const dept = document.getElementById('srDept').value;
    if (!sid && !name && !dept) {
        document.getElementById('srResults').innerHTML = '<p style="color:var(--muted);font-size:.875rem;text-align:center;padding:1rem;">Type to search staff…</p>';
        return;
    }
    const params = new URLSearchParams({ sr_staffid: sid, sr_name: name, sr_dept: dept });
    fetch('{{ route('users.search_staff') }}?' + params)
        .then(r => r.json())
        .then(data => {
            if (!data.length) {
                document.getElementById('srResults').innerHTML = '<p style="color:var(--muted);font-size:.875rem;text-align:center;padding:1rem;">No staff found.</p>';
                return;
            }
            let html = '<table class="table" style="font-size:.85rem;"><thead><tr><th>Staff No</th><th>Name</th><th>Position</th><th>Department</th><th></th></tr></thead><tbody>';
            data.forEach(s => {
                const already = existingStaffNos.includes(s.staff_no);
                html += `<tr style="${already ? 'opacity:.5' : ''}">
                    <td><code>${s.staff_no}</code></td>
                    <td>${s.name}</td>
                    <td style="color:var(--muted)">${s.position || '—'}</td>
                    <td>${s.dept_name || '—'}</td>
                    <td>${already
                        ? '<span style="font-size:.78rem;color:var(--muted)">Already has account</span>'
                        : `<button class="btn btn-sm btn-primary" onclick='prefillFromStaff(${JSON.stringify(s)})'>Create Account</button>`   
                    }</td>
                </tr>`;
            });
            html += '</tbody></table>';
            document.getElementById('srResults').innerHTML = html;
        });
}

function prefillFromStaff(s) {
    closeStaffPanel();
    document.getElementById('userModalTitle').textContent = 'Create Account — ' + s.name;
    document.getElementById('userForm').action = '{{ route('users.store') }}';
    document.getElementById('methodField').innerHTML = '';
    document.getElementById('userId').value = '';
    document.getElementById('f_uname').value = s.name;
    document.getElementById('f_uemail').value = s.email || '';
    document.getElementById('f_urole').value = 'staff';
    document.getElementById('f_ustaffno').value = s.staff_no;
    document.getElementById('f_udept').value = s.department_id || '';
    document.getElementById('f_upos').value = s.position || '';
    document.getElementById('f_upw').required = true;
    document.getElementById('pwLabel').textContent = '(required for new user)';
    document.getElementById('editDangerZone').style.display = 'none';
    openModal('addUserModal');
}

function editUser(u) {
    document.getElementById('userModalTitle').textContent = 'Edit User';
    document.getElementById('userForm').action = '{{ url('users') }}/' + u.id;
    document.getElementById('methodField').innerHTML = '@method('PUT')';
    document.getElementById('userId').value = u.id;
    document.getElementById('f_uname').value = u.name;
    document.getElementById('f_uemail').value = u.email;
    document.getElementById('f_urole').value = u.role;
    document.getElementById('f_ustaffno').value = u.staff_no || '';
    document.getElementById('f_udept').value = u.department_id || '';
    document.getElementById('f_upos').value = u.position || '';
    document.getElementById('f_upw').required = false;
    document.getElementById('pwLabel').textContent = '(leave blank to keep current)';

    const isSelf = u.id == {{ auth()->id() }};
    const dz = document.getElementById('editDangerZone');

    if (!isSelf) {
        dz.style.display = 'flex';

        // Disable / Enable button
        const disableBtn = document.getElementById('editDisableBtn');
        disableBtn.textContent = u.is_active ? 'Disable' : 'Enable';
        disableBtn.style.background = u.is_active ? '#fef3c7' : '#dcfce7';
        disableBtn.style.color     = u.is_active ? '#92400e' : '#166534';
        disableBtn.onclick = function() { closeModal(); confirmToggleUser(u.id, u.is_active ? 0 : 1, u.name); };

        // Set Inactive / Set Active button (staff only)
        const staffBtn = document.getElementById('editStaffBtn');
        const hasStaff = u.role === 'staff' && u.staff;
        if (hasStaff) {
            const staffActive = !!u.staff.is_active;
            staffBtn.style.display   = '';
            staffBtn.textContent     = staffActive ? 'Set Inactive' : 'Set Active';
            staffBtn.style.background = staffActive ? '#fce7f3' : '#d1fae5';
            staffBtn.style.color     = staffActive ? '#9d174d'  : '#065f46';
            staffBtn.onclick = function() { closeModal(); confirmToggleStaff(u.id, staffActive ? 0 : 1, u.name); };
        } else {
            staffBtn.style.display = 'none';
        }

        // Reset PW button
        document.getElementById('editResetPwBtn').onclick = function() { confirmResetUserPassword(u.id, u.name); };

        // Delete button
        document.getElementById('editDeleteBtn').onclick = function() { closeModal(); confirmDeleteUser(u.id); };
    } else {
        dz.style.display = 'none';
    }

    openModal('addUserModal');
}

function confirmDeleteUser(id) {
    document.getElementById('deleteUserForm').action = '{{ url('users') }}/' + id;
    openModal('deleteUserModal');
}

function confirmResetUserPassword(id, name) {
    if (confirm('Reset password to \'password\' for ' + name + '?')) {
        document.getElementById('resetPwUserForm').action = '{{ url('users') }}/' + id + '/reset-password';
        document.getElementById('resetPwUserForm').submit();
    }
}

function filterUsers() {
    const q = document.getElementById('userSearch').value.toLowerCase().trim();
    const role = document.getElementById('userRoleFilter').value;
    const status = document.getElementById('userStatusFilter').value;
    const rows = document.querySelectorAll('tbody tr[data-name]');
    let visible = 0;
    rows.forEach(row => {
        const matchText = !q || row.dataset.name.includes(q) || row.dataset.email.includes(q) || row.dataset.staffno.includes(q) || row.dataset.dept.includes(q);
        const matchRole = !role || row.dataset.role === role;
        const matchStatus = !status || row.dataset.status === status;
        const show = matchText && matchRole && matchStatus;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    const countEl = document.getElementById('userFilterCount');
    countEl.textContent = (q || role || status) ? `${visible} of ${rows.length} shown` : '';
}

function confirmToggleStaff(id, newActive, name) {
    document.getElementById('toggleStaffForm').action = '{{ url('users') }}/' + id + '/toggle-staff-status';
    document.getElementById('toggleStaffActiveValue').value = newActive;
    if (newActive) {
        document.getElementById('toggleStaffModalTitle').textContent = 'Set Staff Active';
        document.getElementById('toggleStaffModalBody').textContent = 'Mark ' + name + ' as HR Active? They will be able to make room bookings and attend training.';
        document.getElementById('toggleStaffConfirmBtn').className = 'btn btn-primary';
        document.getElementById('toggleStaffConfirmBtn').textContent = 'Set Active';
    } else {
        document.getElementById('toggleStaffModalTitle').textContent = 'Set Staff Inactive';
        document.getElementById('toggleStaffModalBody').textContent = 'Mark ' + name + ' as HR Inactive? They can still log in but cannot make room bookings or attend training.';
        document.getElementById('toggleStaffConfirmBtn').className = 'btn btn-danger';
        document.getElementById('toggleStaffConfirmBtn').textContent = 'Set Inactive';
    }
    openModal('toggleStaffModal');
}

function confirmToggleUser(id, newActive, name) {
    document.getElementById('toggleUserForm').action = '{{ url('users') }}/' + id + '/toggle-active';
    document.getElementById('toggleActiveValue').value = newActive;
    document.getElementById('toggleUserForm').submit();
}

@if ($errors->any() && (old('name') || old('email')))
// Validation failed on the Add/Edit user form — re-open the modal and restore the admin's input
// so they can fix the flagged field (shown in the error toast) without re-entering everything.
document.addEventListener('DOMContentLoaded', function () {
    const editId = @json(old('id'));
    if (editId) {
        document.getElementById('userModalTitle').textContent = 'Edit User';
        document.getElementById('userForm').action = '{{ url('users') }}/' + editId;
        document.getElementById('methodField').innerHTML = '@method('PUT')';
        document.getElementById('userId').value = editId;
        document.getElementById('f_upw').required = false;
        document.getElementById('pwLabel').textContent = '(leave blank to keep current)';
        document.getElementById('editDangerZone').style.display = (editId == {{ auth()->id() }}) ? 'none' : 'flex';
    } else {
        document.getElementById('userModalTitle').textContent = 'Add User';
        document.getElementById('userForm').action = '{{ route('users.store') }}';
        document.getElementById('methodField').innerHTML = '';
        document.getElementById('userId').value = '';
        document.getElementById('f_upw').required = true;
        document.getElementById('pwLabel').textContent = '(required for new user)';
        document.getElementById('editDangerZone').style.display = 'none';
    }
    document.getElementById('f_uname').value = @json(old('name'));
    document.getElementById('f_uemail').value = @json(old('email'));
    document.getElementById('f_urole').value = @json(old('role') ?? 'staff');
    document.getElementById('f_ustaffno').value = @json(old('staff_no'));
    document.getElementById('f_udept').value = @json(old('department_id'));
    document.getElementById('f_upos').value = @json(old('position'));
    openModal('addUserModal');
});
@endif
</script>
@endsection
