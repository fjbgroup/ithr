@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <h2>User Accounts</h2>
        <p class="page-subtitle">{{ $users->count() }} user(s) — Manage system access</p>
    </div>
    <button class="btn btn-primary" onclick="openAddUserPanel()">+ Add User</button>
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

<div style="display:flex;gap:.75rem;flex-wrap:wrap;align-items:center;margin-bottom:1rem;">
    <div style="position:relative;flex:1;min-width:220px;">
        <svg style="position:absolute;left:.65rem;top:50%;transform:translateY(-50%);width:15px;height:15px;color:var(--muted);pointer-events:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/></svg>
        <input type="text" id="userSearch" placeholder="Search name, email, staff no…" oninput="filterUsers()" style="padding-left:2rem;width:100%;box-sizing:border-box;">
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
        <table class="table table-sticky">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Staff No</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $u)
                <tr data-name="{{ strtolower($u->name) }}" data-email="{{ strtolower($u->email) }}" data-staffno="{{ strtolower($u->staff_no ?? '') }}" data-dept="{{ strtolower($u->department->name ?? '') }}" data-role="{{ $u->role }}" data-status="{{ $u->is_active ? 'active' : 'inactive' }}">
                    <td><strong>{{ $u->name }}</strong></td>
                    <td style="font-size:.85rem;">{{ $u->email }}</td>
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
                    <td>{{ $u->department->name ?? '—' }}</td>
                    <td>
                        @if($u->is_active)
                        <span class="status-badge status-completed">Active</span>
                        @else
                        <span class="status-badge status-scheduled">Inactive</span>
                        @endif
                    </td>
                    <td class="td-actions">
                        @if ($u->isStaff() && $u->staff_id)
                        <a href="{{ route('users.show', $u->id) }}" class="btn btn-sm btn-ghost">View</a>
                        @endif
                        <button class="btn btn-sm btn-outline" onclick="editUser({{ json_encode($u) }})">Edit</button>
                        
                        @if ($u->id != auth()->id())
                            @if ($u->is_active)
                            <button class="btn btn-sm" style="background:#fef3c7;color:#92400e;" onclick="confirmToggleUser({{ $u->id }}, 0, '{{ addslashes($u->name) }}')">Disable</button>
                            @else
                            <button class="btn btn-sm" style="background:#dcfce7;color:#166534;" onclick="confirmToggleUser({{ $u->id }}, 1, '{{ addslashes($u->name) }}')">Enable</button>
                            @endif
                            <button class="btn btn-sm btn-danger" onclick="confirmDeleteUser({{ $u->id }})">Delete</button>
                        @endif
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
                <div class="form-group form-full" id="activeRow" style="display:none;">
                    <label>Account Access</label>
                    <label style="display:inline-flex;align-items:center;gap:.6rem;cursor:pointer;margin-top:.25rem;">
                        <label class="toggle-switch" style="flex-shrink:0;">
                            <input type="checkbox" name="is_active" id="f_uactive" value="1">
                            <span class="toggle-slider"></span>
                        </label>
                        <span id="activeLabel" style="font-size:.875rem;color:var(--muted);">Active — user can log in</span>
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save User</button>
            </div>
        </form>
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
    document.getElementById('activeRow').style.display = 'none';
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
    const activeRow = document.getElementById('activeRow');
    if (!isSelf) {
        activeRow.style.display = '';
        const cb = document.getElementById('f_uactive');
        cb.checked = !!u.is_active;
        document.getElementById('activeLabel').textContent = cb.checked ? 'Active — user can log in' : 'Inactive — login blocked';        
        cb.onchange = function() {
            document.getElementById('activeLabel').textContent = this.checked ? 'Active — user can log in' : 'Inactive — login blocked';  
        };
    } else {
        activeRow.style.display = 'none';
    }
    openModal('addUserModal');
}

function confirmDeleteUser(id) {
    document.getElementById('deleteUserForm').action = '{{ url('users') }}/' + id;
    openModal('deleteUserModal');
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

function confirmToggleUser(id, newActive, name) {
    document.getElementById('toggleUserForm').action = '{{ url('users') }}/' + id + '/toggle-active';
    document.getElementById('toggleActiveValue').value = newActive;
    if (newActive) {
        document.getElementById('toggleModalTitle').textContent = 'Enable Access';
        document.getElementById('toggleModalBody').textContent = 'Enable login access for ' + name + '?';
        document.getElementById('toggleConfirmBtn').className = 'btn btn-primary';
        document.getElementById('toggleConfirmBtn').textContent = 'Enable';
    } else {
        document.getElementById('toggleModalTitle').textContent = 'Disable Access';
        document.getElementById('toggleModalBody').textContent = 'Disable login access for ' + name + '? They will not be able to log in until re-enabled.';
        document.getElementById('toggleConfirmBtn').className = 'btn btn-danger';
        document.getElementById('toggleConfirmBtn').textContent = 'Disable';
    }
    openModal('toggleUserModal');
}
</script>
@endsection
