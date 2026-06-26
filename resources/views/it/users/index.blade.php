@extends('it.layouts.app')

@section('title', 'Manage Users')
@section('page_title', 'Manage Users')

@section('content')
<?php
$roleMeta = [
  'admin'         => ['IT Admin',        'bi-shield-fill',       '#0284c7', 'rgba(2,132,199,.12)',   'rgba(2,132,199,.25)'],
  'finance_admin' => ['Finance Admin',   'bi-currency-dollar',   '#0369a1', 'rgba(56,189,248,.12)',  'rgba(56,189,248,.3)'],
  'ceo'           => ['CEO',             'bi-star-fill',         '#b45309', 'rgba(245,158,11,.12)',  'rgba(245,158,11,.3)'],
  'gm'            => ['General Manager', 'bi-briefcase-fill',    '#0d9488', 'rgba(20,184,166,.12)',  'rgba(20,184,166,.25)'],
  'hou'           => ['Head of Unit',    'bi-person-badge-fill', '#7c3aed', 'rgba(124,58,237,.12)',  'rgba(124,58,237,.25)'],
  'user'          => ['Staff',           'bi-person-fill',       '#2563eb', 'rgba(37,99,235,.08)',   'rgba(37,99,235,.2)'],
];
$roleOrder   = ['ceo','gm','hou','admin','finance_admin','user'];
$avatarColor = fn($name) => ['#0284c7','#2563eb','#16a34a','#7c3aed','#0891b2','#dc2626','#d97706'][ord(strtoupper($name[0] ?? 'A')) % 7];
$activeRole  = $activeRole ?? request('role_tab', 'ceo');
$action      = request('action', 'list');
?>

<style>
/* ── Manage Users: Two-panel layout ── */
.mu-layout{display:grid;grid-template-columns:220px 1fr;gap:20px;align-items:start}
@media(max-width:768px){.mu-layout{grid-template-columns:1fr}}

/* Left role rail */
.mu-rail{background:var(--surface);border:1px solid var(--border);border-radius:14px;overflow:hidden;position:sticky;top:80px}
.mu-rail-header{padding:14px 16px;border-bottom:1px solid var(--border);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted)}
.mu-rail-item{display:flex;align-items:center;gap:10px;padding:11px 16px;cursor:pointer;border-left:3px solid transparent;transition:all .12s;text-decoration:none;color:var(--text)}
.mu-rail-item:hover{background:var(--body-bg)}
.mu-rail-item.active{border-left-color:var(--rail-color,var(--accent));background:var(--rail-bg,rgba(2,132,199,.07))}
.mu-rail-item.active .mu-rail-label{color:var(--rail-color,var(--accent));font-weight:700}
.mu-rail-icon{width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0}
.mu-rail-label{font-size:13px;font-weight:500;flex:1}
.mu-rail-count{font-size:11px;font-weight:700;border-radius:20px;padding:2px 9px;flex-shrink:0}

/* Right panel */
.mu-panel{display:flex;flex-direction:column;gap:16px}

/* Panel header */
.mu-panel-hdr{background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:18px 22px;display:flex;align-items:center;gap:16px}
.mu-panel-hdr-icon{width:48px;height:48px;border-radius:13px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0}
.mu-panel-hdr-title{font-family:'DM Sans',sans-serif;font-size:18px;font-weight:800;color:var(--text);line-height:1}
.mu-panel-hdr-sub{font-size:12px;color:var(--muted);margin-top:4px}

/* User rows */
.mu-table-wrap{background:var(--surface);border:1px solid var(--border);border-radius:14px;overflow:hidden}
.mu-user-row{display:flex;align-items:center;gap:14px;padding:14px 20px;border-bottom:1px solid var(--border);transition:background .1s}
.mu-user-row:last-child{border-bottom:none}
.mu-user-row:hover{background:var(--body-bg)}
.mu-avatar{width:42px;height:42px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:'DM Sans',sans-serif;font-size:16px;font-weight:800;color:#fff;flex-shrink:0;object-fit:cover}
.mu-user-info{flex:1;min-width:0}
.mu-user-name{font-size:14px;font-weight:700;color:var(--text)}
.mu-user-sub{display:flex;align-items:center;gap:10px;margin-top:3px;flex-wrap:wrap}
.mu-user-meta{font-size:11px;color:var(--muted);display:inline-flex;align-items:center;gap:4px}
.mu-status{display:inline-flex;align-items:center;gap:4px;border-radius:20px;padding:3px 10px;font-size:10px;font-weight:700;flex-shrink:0}
.mu-actions{display:flex;align-items:center;gap:5px;flex-shrink:0}
.mu-btn{width:30px;height:30px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:12px;text-decoration:none;border:1px solid;cursor:pointer;transition:opacity .12s;flex-shrink:0;background:none}
.mu-btn:hover{opacity:.7}
.mu-empty{padding:44px 24px;text-align:center}
.mu-empty-icon{font-size:38px;display:block;margin-bottom:12px;opacity:.3}
.mu-empty-title{font-size:14px;font-weight:700;color:var(--text);margin-bottom:6px}
.mu-empty-sub{font-size:12px;color:var(--muted)}

/* Form */
.mu-form-wrap{background:var(--surface);border:1px solid var(--border);border-radius:14px;overflow:hidden;margin-bottom:20px}
.mu-form-hdr{background:linear-gradient(135deg,var(--navy,#142b47) 0%,#254a78 100%);padding:16px 22px;display:flex;align-items:center;justify-content:space-between}

/* Top bar */
.mu-topbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;gap:12px;flex-wrap:wrap}
.mu-topbar-title{font-family:'DM Sans',sans-serif;font-size:22px;font-weight:800;color:var(--text)}
.mu-topbar-sub{font-size:13px;color:var(--muted);margin-top:3px}
.mu-stats-row{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:20px}
.mu-stat-chip{display:flex;align-items:center;gap:8px;background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:10px 16px}
.mu-stat-num{font-size:20px;font-weight:800;color:var(--text);line-height:1}
.mu-stat-lbl{font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-top:2px}
</style>

<!-- TOP BAR -->
<div class="mu-topbar">
  <div>
    <div class="mu-topbar-title">Manage Users</div>
    <div class="mu-topbar-sub">Create, edit and control access for all system users</div>
  </div>
  <a href="{{ route('it.users.index', ['action'=>'add']) }}" class="btn-primary-custom"><i class="bi bi-person-plus-fill"></i> New User</a>
</div>

<!-- STAT CHIPS -->
<div class="mu-stats-row">
  @foreach([
    ['bi-people-fill','rgba(2,132,199,.1)','var(--accent)',$totalUsers,'Total Users'],
    ['bi-check-circle-fill','rgba(22,163,74,.1)','#16a34a',$activeCount,'Active'],
    ['bi-x-circle-fill','rgba(239,68,68,.1)','#dc2626',$inactiveCount,'Inactive'],
    ['bi-key-fill','rgba(245,158,11,.1)','#d97706',$pendingCount,'PW Requests'],
  ] as [$ic,$bg,$cl,$val,$lbl])
  <div class="mu-stat-chip">
    <div style="width:34px;height:34px;border-radius:9px;background:{{ $bg }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
      <i class="bi {{ $ic }}" style="color:{{ $cl }};font-size:14px"></i>
    </div>
    <div>
      <div class="mu-stat-num">{{ $val }}</div>
      <div class="mu-stat-lbl">{{ $lbl }}</div>
    </div>
  </div>
  @endforeach
</div>

<!-- ADD FORM -->
@if($action === 'add')
<div class="mu-form-wrap">
  <div class="mu-form-hdr">
    <div style="display:flex;align-items:center;gap:10px">
      <div style="width:32px;height:32px;background:rgba(255,255,255,.15);border-radius:8px;display:flex;align-items:center;justify-content:center">
        <i class="bi bi-person-plus-fill" style="color:#fff;font-size:15px"></i>
      </div>
      <span style="font-family:'DM Sans',sans-serif;font-weight:700;font-size:14px;color:#fff">Create New User</span>
    </div>
    <a href="{{ route('it.users.index') }}" style="display:inline-flex;align-items:center;gap:5px;color:rgba(255,255,255,.7);font-size:13px;text-decoration:none;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);border-radius:7px;padding:5px 12px">
      <i class="bi bi-x"></i> Cancel
    </a>
  </div>
  <div style="padding:22px">
    <form method="POST" action="{{ route('it.users.store') }}">
      @csrf
      @if($errors->any())
      <div class="alert-danger-custom" style="margin-bottom:16px">
        <i class="bi bi-exclamation-circle-fill"></i>
        <ul style="margin:0;padding-left:18px">
          @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
      @endif

      {{-- Staff search --}}
      <div style="margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid var(--border)">
        <label class="form-label" style="font-weight:700">
          <i class="bi bi-search" style="color:var(--accent)"></i>
          Search HR Staff to auto-fill
        </label>
        <div style="position:relative">
          <input type="text" id="staffSearchInput" class="form-control" autocomplete="off"
            placeholder="Type name or staff number…"
            style="padding-right:38px">
          <i class="bi bi-search" style="position:absolute;right:13px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:14px;pointer-events:none"></i>
        </div>
        <div id="staffSearchResults" style="display:none;position:absolute;z-index:999;background:var(--surface);border:1px solid var(--border);border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.12);width:100%;max-width:520px;max-height:280px;overflow-y:auto;margin-top:4px"></div>
        <div id="staffSelectedBanner" style="display:none;margin-top:10px;padding:10px 14px;background:rgba(2,132,199,.07);border:1px solid rgba(2,132,199,.2);border-radius:8px;align-items:center;gap:10px">
          <i class="bi bi-check-circle-fill" style="color:var(--accent);flex-shrink:0"></i>
          <span id="staffSelectedLabel" style="font-size:13px;font-weight:600;color:var(--text);flex:1"></span>
          <button type="button" onclick="clearStaffSelection()" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:13px;padding:0">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
        <div style="font-size:11px;color:var(--muted);margin-top:6px">
          Select a staff member to auto-fill the fields below, or fill them in manually.
        </div>
      </div>

      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Username (Staff No.) <span style="color:var(--red)">*</span></label>
          <input type="text" id="field_username" name="username" class="form-control" required value="{{ old('username') }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">Full Name <span style="color:var(--red)">*</span></label>
          <input type="text" id="field_full_name" name="full_name" class="form-control" required value="{{ old('full_name') }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">Email</label>
          <input type="email" id="field_email" name="email" class="form-control" value="{{ old('email') }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">Password <span style="color:var(--red)">*</span></label>
          <input type="password" name="password" class="form-control" required placeholder="Set password">
        </div>
        <div class="col-md-4">
          <label class="form-label">Role</label>
          <select name="role" class="form-select">
            @php
            $defaultRole = old('role', request('default_role', 'user'));
            $roleOpts = ['ceo'=>'Chief Executive Officer (C.E.O)','gm'=>'General Manager (G.M)','hou'=>'Head Of Unit (H.O.U)','admin'=>'IT Admin','finance_admin'=>'Finance Admin','user'=>'Staff'];
            @endphp
            @foreach($roleOpts as $rv => $rl)
            <option value="{{ $rv }}" {{ $defaultRole === $rv ? 'selected' : '' }}>{{ $rl }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Department</label>
          <input type="text" id="field_dept_name" name="dept_name" class="form-control" value="{{ old('dept_name') }}" placeholder="e.g. IT Department">
        </div>
        <div class="col-12">
          <button type="submit" class="btn-primary-custom"><i class="bi bi-check-lg"></i> Create User</button>
        </div>
      </div>
    </form>
    <script>
    (function () {
      const input   = document.getElementById('staffSearchInput');
      const results = document.getElementById('staffSearchResults');
      const banner  = document.getElementById('staffSelectedBanner');
      const label   = document.getElementById('staffSelectedLabel');
      let timer;

      input.addEventListener('input', function () {
        clearTimeout(timer);
        const q = this.value.trim();
        if (q.length < 2) { results.style.display = 'none'; return; }
        timer = setTimeout(() => fetchStaff(q), 280);
      });

      input.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') { results.style.display = 'none'; }
      });

      document.addEventListener('click', function (e) {
        if (!input.contains(e.target) && !results.contains(e.target)) {
          results.style.display = 'none';
        }
      });

      function fetchStaff(q) {
        fetch('{{ route('it.users.staff-search') }}?q=' + encodeURIComponent(q), {
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => renderResults(data));
      }

      function renderResults(data) {
        if (!data.length) {
          results.innerHTML = '<div style="padding:14px 16px;font-size:13px;color:var(--muted);text-align:center"><i class="bi bi-search" style="opacity:.4"></i> No staff found</div>';
          results.style.display = 'block';
          return;
        }
        results.innerHTML = data.map(s => `
          <div class="staff-result-item" onclick="selectStaff(${JSON.stringify(s).replace(/"/g, '&quot;')})"
            style="display:flex;align-items:center;gap:12px;padding:11px 16px;cursor:pointer;border-bottom:1px solid var(--border)">
            <div style="width:36px;height:36px;border-radius:50%;background:#0284c7;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:14px;flex-shrink:0">
              ${s.name.charAt(0).toUpperCase()}
            </div>
            <div style="flex:1;min-width:0">
              <div style="font-size:13px;font-weight:700;color:var(--text)">${s.name}</div>
              <div style="font-size:11px;color:var(--muted);margin-top:2px">
                <span style="font-family:monospace">${s.staff_no}</span>
                ${s.dept_name ? ' · ' + s.dept_name : ''}
                ${s.position ? ' · ' + s.position : ''}
              </div>
            </div>
          </div>`).join('');
        results.innerHTML += '<div style="padding:8px 14px;font-size:11px;color:var(--muted);text-align:center;border-top:1px solid var(--border)">' + data.length + ' result' + (data.length > 1 ? 's' : '') + '</div>';
        results.style.display = 'block';
      }

      window.selectStaff = function (s) {
        document.getElementById('field_username').value  = s.staff_no;
        document.getElementById('field_full_name').value = s.name;
        document.getElementById('field_email').value     = s.email;
        document.getElementById('field_dept_name').value = s.dept_name;
        results.style.display = 'none';
        input.value = '';
        label.textContent = s.name + ' (' + s.staff_no + ')' + (s.dept_name ? ' — ' + s.dept_name : '');
        banner.style.display = 'flex';
      };

      window.clearStaffSelection = function () {
        ['field_username','field_full_name','field_email','field_dept_name'].forEach(id => {
          document.getElementById(id).value = '';
        });
        banner.style.display = 'none';
        input.value = '';
      };
    })();
    </script>
  </div>
</div>
@endif

<!-- EDIT USER MODAL -->
<div id="mu-edit-overlay" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;width:100%;height:100%;z-index:1050;background:rgba(0,0,0,.5);align-items:flex-start;justify-content:center;padding:70px 20px 20px;overflow-y:auto">
  <div style="background:var(--surface);border-radius:16px;width:100%;max-width:720px;min-height:70vh;overflow-y:auto;box-shadow:0 24px 64px rgba(0,0,0,.35);position:relative;display:flex;flex-direction:column">

    {{-- Header --}}
    <div class="mu-form-hdr" style="border-radius:16px 16px 0 0;flex-shrink:0">
      <div style="display:flex;align-items:center;gap:12px">
        <div style="width:38px;height:38px;background:rgba(255,255,255,.15);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
          <i class="bi bi-person-gear" style="color:#fff;font-size:18px"></i>
        </div>
        <div>
          <div id="mu-edit-modal-title" style="font-family:'DM Sans',sans-serif;font-weight:700;font-size:15px;color:#fff;line-height:1">Edit User</div>
          <div style="font-size:11px;color:rgba(255,255,255,.55);margin-top:3px">Update account details below</div>
        </div>
      </div>
      <button type="button" onclick="closeMuEditModal()" style="display:inline-flex;align-items:center;gap:5px;color:rgba(255,255,255,.7);font-size:13px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);border-radius:7px;padding:6px 14px;cursor:pointer;transition:background .15s" onmouseover="this.style.background='rgba(255,255,255,.2)'" onmouseout="this.style.background='rgba(255,255,255,.1)'">
        <i class="bi bi-x-lg"></i> Close
      </button>
    </div>

    {{-- Body --}}
    <div style="padding:28px 28px 24px;flex:1">
      <form id="mu-edit-form" method="POST" action="">
        @csrf

        {{-- Identity section --}}
        <div style="margin-bottom:24px">
          <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:14px;display:flex;align-items:center;gap:6px">
            <i class="bi bi-person-fill" style="font-size:12px"></i> Identity
          </div>
          <div class="row g-3">
            <div class="col-md-5">
              <label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted);margin-bottom:6px">
                <i class="bi bi-at" style="color:var(--accent)"></i> Username (Staff No.)
              </label>
              <input type="text" id="mu-edit-username" name="username" class="form-control" readonly
                style="background:var(--body-bg);color:var(--muted);cursor:not-allowed;font-family:monospace;font-size:13px">
            </div>
            <div class="col-md-7">
              <label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted);margin-bottom:6px">
                <i class="bi bi-person-fill" style="color:var(--accent)"></i> Full Name <span style="color:var(--red)">*</span>
              </label>
              <input type="text" id="mu-edit-full-name" name="full_name" class="form-control" required style="font-size:13px">
            </div>
            <div class="col-12">
              <label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted);margin-bottom:6px">
                <i class="bi bi-envelope-fill" style="color:var(--accent)"></i> Email Address
              </label>
              <input type="email" id="mu-edit-email" name="email" class="form-control" style="font-size:13px" placeholder="user@example.com">
            </div>
          </div>
        </div>

        <div style="border-top:1px solid var(--border);margin-bottom:24px"></div>

        {{-- Access section --}}
        <div style="margin-bottom:24px">
          <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:14px;display:flex;align-items:center;gap:6px">
            <i class="bi bi-shield-lock-fill" style="font-size:12px"></i> Access & Role
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted);margin-bottom:6px">
                <i class="bi bi-shield-fill" style="color:var(--accent)"></i> Role
              </label>
              <select id="mu-edit-role" name="role" class="form-select" style="font-size:13px">
                @foreach(['ceo'=>'Chief Executive Officer (C.E.O)','gm'=>'General Manager (G.M)','hou'=>'Head Of Unit (H.O.U)','admin'=>'IT Admin','finance_admin'=>'Finance Admin','user'=>'Staff'] as $rv => $rl)
                <option value="{{ $rv }}">{{ $rl }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted);margin-bottom:6px">
                <i class="bi bi-building" style="color:var(--accent)"></i> Department
              </label>
              <input type="text" id="mu-edit-dept" name="dept_name" class="form-control" style="font-size:13px" placeholder="e.g. IT Department">
            </div>
            <div class="col-12">
              <label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted);margin-bottom:6px">
                <i class="bi bi-key-fill" style="color:var(--accent)"></i> Password
                <span style="font-weight:400;color:var(--muted)"> — leave blank to keep current</span>
              </label>
              <input type="password" id="mu-edit-password" name="password" class="form-control" style="font-size:13px" placeholder="Enter new password to change…">
            </div>
          </div>
        </div>

        <div style="border-top:1px solid var(--border);padding-top:20px;display:flex;align-items:center;justify-content:flex-end;gap:10px">
          <button type="button" onclick="closeMuEditModal()"
            style="padding:9px 20px;background:var(--body-bg);border:1px solid var(--border);border-radius:8px;font-size:13px;font-weight:600;color:var(--muted);cursor:pointer;font-family:inherit">
            Cancel
          </button>
          <button type="submit" class="btn-primary-custom" style="padding:9px 24px">
            <i class="bi bi-check-lg"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- TWO-PANEL: RAIL + TABLE -->
<div class="mu-layout">

  <!-- LEFT RAIL -->
  <div class="mu-rail">
    <div class="mu-rail-header">Roles</div>
    @foreach($roleOrder as $r)
    @php [$label,$icon,$color,$bg,$border] = $roleMeta[$r]; $cnt = $roleCounts[$r]; $isAct = $activeRole === $r; @endphp
    <a href="{{ route('it.users.index', ['role_tab' => $r]) }}"
       class="mu-rail-item {{ $isAct ? 'active' : '' }}"
       style="--rail-color:{{ $color }};--rail-bg:{{ $bg }}">
      <div class="mu-rail-icon" style="background:{{ $bg }}">
        <i class="bi {{ $icon }}" style="color:{{ $color }}"></i>
      </div>
      <span class="mu-rail-label">{{ $label }}</span>
      <span class="mu-rail-count" style="background:{{ $bg }};color:{{ $color }}">{{ $cnt }}</span>
    </a>
    @endforeach
  </div>

  <!-- RIGHT PANEL -->
  <div class="mu-panel">
    @php [$label,$icon,$color,$bg,$border] = $roleMeta[$activeRole]; @endphp

    <!-- Search bar -->
    <form method="GET" action="{{ route('it.users.index') }}" style="display:flex;gap:8px;align-items:center">
      <input type="hidden" name="role_tab" value="{{ $activeRole }}">
      <div style="flex:1;position:relative">
        <input type="text" id="mu-search-input" name="search" value="{{ $search }}"
          placeholder="Search name, username, email, department…"
          autocomplete="off"
          style="width:100%;padding:9px 36px 9px 14px;border:1px solid var(--border);border-radius:9px;background:var(--surface);color:var(--text);font-size:13px;outline:none">
        <i class="bi bi-search" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:13px;pointer-events:none"></i>
      </div>
      <a id="mu-clear-btn" href="{{ route('it.users.index', ['role_tab' => $activeRole]) }}"
        style="padding:9px 14px;border:1px solid var(--border);border-radius:9px;font-size:13px;color:var(--muted);text-decoration:none;white-space:nowrap;background:var(--surface);display:{{ $search ? 'inline-flex' : 'none' }};align-items:center;gap:5px"><i class="bi bi-x-lg"></i> Clear</a>
    </form>

    <!-- Panel header -->
    <div class="mu-panel-hdr">
      <div class="mu-panel-hdr-icon" style="background:{{ $bg }}">
        <i class="bi {{ $icon }}" style="color:{{ $color }};font-size:22px"></i>
      </div>
      <div style="flex:1">
        <div class="mu-panel-hdr-title">{{ $label }}</div>
        <div class="mu-panel-hdr-sub" id="mu-users-count">
          {{ $users->total() }} account{{ $users->total() !== 1 ? 's' : '' }}{{ $search ? ' matching "'.e($search).'"' : '' }}
        </div>
      </div>
      <a href="{{ route('it.users.index', ['action'=>'add','default_role'=>$activeRole,'role_tab'=>$activeRole]) }}"
        style="display:inline-flex;align-items:center;gap:6px;background:{{ $color }};color:#fff;border:none;border-radius:9px;padding:9px 18px;font-size:13px;font-weight:700;text-decoration:none;white-space:nowrap">
        <i class="bi bi-plus-lg"></i> Add {{ $label }}
      </a>
    </div>

    <!-- Users table + pagination (swapped by live search) -->
    <div id="mu-users-list">
    <div class="mu-table-wrap">
      @forelse($users as $row)
      @php
        $aColor = $avatarColor($row->full_name);
        $isMe   = $row->id == auth()->id();
      @endphp
      <div class="mu-user-row">
        <!-- Avatar -->
        @if($row->avatar)
        <img src="{{ Storage::url($row->avatar) }}" class="mu-avatar">
        @else
        <div class="mu-avatar" style="background:{{ $aColor }}">{{ strtoupper(substr($row->full_name,0,1)) }}</div>
        @endif

        <!-- Info -->
        <div class="mu-user-info">
          <div class="mu-user-name">
            {{ $row->full_name }}
            @if($isMe)
            <span style="font-size:9px;background:rgba(2,132,199,.15);color:var(--accent);border-radius:4px;padding:1px 6px;font-weight:700;vertical-align:middle;margin-left:4px">YOU</span>
            @endif
            @if($row->must_change_password)
            <span style="font-size:9px;background:rgba(220,38,38,.1);color:#dc2626;border-radius:4px;padding:1px 6px;font-weight:700;vertical-align:middle;margin-left:4px"><i class="bi bi-key-fill"></i> Temp PW</span>
            @endif
          </div>
          <div class="mu-user-sub">
            <span class="mu-user-meta"><i class="bi bi-person-fill"></i>{{ $row->username }}</span>
            @if($row->email)
            <span class="mu-user-meta"><i class="bi bi-envelope-fill"></i>{{ $row->email }}</span>
            @endif
            @if($row->department)
            <span class="mu-user-meta"><i class="bi bi-person-badge-fill"></i>{{ $row->department }}</span>
            @endif
            <span class="mu-user-meta"><i class="bi bi-clock-history"></i>{{ $row->last_login ? $row->last_login->format('d/m/Y H:i') : 'Never logged in' }}</span>
          </div>
        </div>

        <!-- Status -->
        @if($row->is_active)
        <span class="mu-status" style="background:rgba(22,163,74,.1);color:#16a34a">
          <span style="width:6px;height:6px;border-radius:50%;background:#16a34a;display:inline-block"></span> Active
        </span>
        @else
        <span class="mu-status" style="background:rgba(239,68,68,.1);color:#dc2626">
          <span style="width:6px;height:6px;border-radius:50%;background:#dc2626;display:inline-block"></span> Inactive
        </span>
        @endif

        <!-- Actions -->
        <div class="mu-actions">
          <button type="button" class="mu-btn mu-edit-trigger"
            style="background:rgba(2,132,199,.08);color:var(--accent);border-color:rgba(2,132,199,.2)" title="Edit"
            data-action="{{ route('it.users.update', $row->id) }}"
            data-name="{{ e($row->full_name) }}"
            data-username="{{ e($row->username) }}"
            data-full-name="{{ e($row->full_name) }}"
            data-email="{{ e($row->email ?? '') }}"
            data-role="{{ $row->it_role === 'admin_it' ? 'admin' : $row->it_role }}"
            data-dept="{{ e($row->dept_name ?? '') }}">
            <i class="bi bi-pencil-fill"></i>
          </button>
          @if(!$isMe)
          <form method="POST" action="{{ route('it.users.toggle', $row->id) }}" style="display:contents"
            onsubmit="return confirm('{{ $row->is_active ? 'Deactivate' : 'Activate' }} {{ $row->full_name }}?')">
            @csrf
            <button type="submit" class="mu-btn"
              style="background:{{ $row->is_active ? 'rgba(22,163,74,.08)' : 'rgba(239,68,68,.08)' }};color:{{ $row->is_active ? '#16a34a' : '#dc2626' }};border-color:{{ $row->is_active ? 'rgba(22,163,74,.2)' : 'rgba(239,68,68,.2)' }}"
              title="{{ $row->is_active ? 'Deactivate' : 'Activate' }}">
              <i class="bi bi-toggle-{{ $row->is_active ? 'on' : 'off' }}"></i>
            </button>
          </form>
          @if($row->it_role !== 'admin_it' && $row->it_role !== 'admin')
          <form method="POST" action="{{ route('it.users.reset-password', $row->id) }}" style="display:contents"
            onsubmit="return confirm('Reset password for {{ $row->full_name }} to default?')">
            @csrf
            <button type="submit" class="mu-btn"
              style="background:rgba(245,158,11,.08);color:#d97706;border-color:rgba(245,158,11,.2)" title="Reset Password">
              <i class="bi bi-key-fill"></i>
            </button>
          </form>
          @endif
          @if($row->it_role !== 'user')
          <form method="POST" action="{{ route('it.users.destroy', $row->id) }}" style="display:contents"
            onsubmit="return confirm('Reset {{ $row->full_name }} to the default Staff role?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="mu-btn"
              style="background:rgba(239,68,68,.08);color:#dc2626;border-color:rgba(239,68,68,.2)" title="Reset to Staff role">
              <i class="bi bi-arrow-counterclockwise"></i>
            </button>
          </form>
          @endif
          @endif
        </div>
      </div>
      @empty
      <div class="mu-empty">
        <i class="bi {{ $icon }} mu-empty-icon" style="color:{{ $color }}"></i>
        <div class="mu-empty-title">{{ $search ? 'No results for "'.e($search).'"' : 'No '.$label.' accounts' }}</div>
        <div class="mu-empty-sub">
          @if($search)
          <a href="{{ route('it.users.index', ['role_tab' => $activeRole]) }}" style="color:{{ $color }};font-weight:700;text-decoration:none">Clear search</a>
          @else
          <a href="{{ route('it.users.index', ['action'=>'add','default_role'=>$activeRole,'role_tab'=>$activeRole]) }}"
            style="color:{{ $color }};font-weight:700;text-decoration:none">Create the first one</a>
          @endif
        </div>
      </div>
      @endforelse
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
    <div style="padding:16px 20px;background:var(--surface);border:1px solid var(--border);border-radius:14px">
      {{ $users->links() }}
    </div>
    @endif
    </div>{{-- #mu-users-list --}}

  </div>
</div>

<!-- PASSWORD RESET REQUESTS -->
<div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;overflow:hidden;margin-top:24px">
  <div style="padding:16px 22px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
    <div style="display:flex;align-items:center;gap:10px">
      <i class="bi bi-key-fill" style="color:#d97706;font-size:16px"></i>
      <div>
        <div style="font-family:'DM Sans',sans-serif;font-weight:700;font-size:14px;color:var(--text)">Password Reset Requests</div>
        <div style="font-size:12px;color:var(--muted);margin-top:2px">Verify Staff ID before approving</div>
      </div>
    </div>
    @if($pendingCount > 0)
    <span style="background:#dc2626;color:#fff;border-radius:20px;padding:3px 12px;font-size:11px;font-weight:700">{{ $pendingCount }} pending</span>
    @endif
  </div>
  @if($resetRequests->isEmpty())
  <div style="padding:40px;text-align:center;color:var(--muted)">
    <i class="bi bi-inbox" style="font-size:32px;opacity:.35;display:block;margin-bottom:10px"></i>
    <div style="font-size:13px;font-weight:600;color:var(--text);margin-bottom:4px">No requests yet</div>
    <div style="font-size:12px">Password reset requests will appear here</div>
  </div>
  @else
  @foreach($resetRequests as $req)
  <div style="display:flex;align-items:center;gap:14px;padding:14px 22px;border-bottom:1px solid var(--border);{{ $req->status==='pending' ? 'background:rgba(245,158,11,.03)' : '' }}">
    <div style="width:38px;height:38px;border-radius:50%;background:{{ $avatarColor($req->full_name) }};display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:14px;flex-shrink:0">
      {{ strtoupper(substr($req->full_name,0,1)) }}
    </div>
    <div style="flex:1;min-width:0">
      <div style="font-size:13px;font-weight:700;color:var(--text)">{{ $req->full_name }}</div>
      <div style="font-size:11px;color:var(--muted);margin-top:2px">@{{ $req->username }} · {{ \Carbon\Carbon::parse($req->requested_at)->format('d M Y H:i') }}</div>
    </div>
    <div style="text-align:center;flex-shrink:0">
      <div style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:4px">Staff ID</div>
      <span style="background:rgba(37,99,235,.08);color:#1d4ed8;border:1px solid rgba(37,99,235,.2);border-radius:6px;padding:3px 10px;font-size:12px;font-family:monospace;font-weight:700">{{ $req->staff_id ?: '—' }}</span>
    </div>
    <div style="flex-shrink:0">
      @if($req->status==='pending')
      <span style="display:inline-flex;align-items:center;gap:4px;background:rgba(245,158,11,.1);color:#92400e;border:1px solid rgba(245,158,11,.25);border-radius:20px;padding:3px 10px;font-size:11px;font-weight:700"><i class="bi bi-clock-fill" style="font-size:9px"></i> Pending</span>
      @elseif($req->status==='resolved')
      <span style="display:inline-flex;align-items:center;gap:4px;background:rgba(22,163,74,.1);color:#15803d;border:1px solid rgba(22,163,74,.2);border-radius:20px;padding:3px 10px;font-size:11px;font-weight:700"><i class="bi bi-check-circle-fill" style="font-size:9px"></i> Resolved</span>
      @else
      <span style="display:inline-flex;align-items:center;gap:4px;background:rgba(220,38,38,.08);color:#b91c1c;border:1px solid rgba(220,38,38,.2);border-radius:20px;padding:3px 10px;font-size:11px;font-weight:700"><i class="bi bi-x-circle-fill" style="font-size:9px"></i> Rejected</span>
      @endif
    </div>
    <div class="mu-actions" style="flex-shrink:0">
      @if($req->status==='pending')
      <form method="POST" action="{{ route('it.users.reset-password', $req->user_id) }}" style="display:contents"
        onsubmit="return confirm('Reset password for {{ $req->full_name }} to default?\n\nStaff ID: {{ $req->staff_id }}\n\nVerify before proceeding.')">
        @csrf
        <input type="hidden" name="reset_request_id" value="{{ $req->id }}">
        <button type="submit" class="mu-btn"
          style="background:rgba(22,163,74,.08);color:#15803d;border-color:rgba(22,163,74,.2)" title="Approve">
          <i class="bi bi-check-lg"></i>
        </button>
      </form>
      <form method="POST" action="{{ route('it.users.reject-reset', $req->id) }}" style="display:contents"
        onsubmit="return confirm('Reject this password reset request?')">
        @csrf
        <button type="submit" class="mu-btn"
          style="background:rgba(239,68,68,.08);color:#dc2626;border-color:rgba(239,68,68,.2)" title="Reject">
          <i class="bi bi-x-lg"></i>
        </button>
      </form>
      @else
      <span style="font-size:11px;color:var(--muted)">—</span>
      @endif
    </div>
  </div>
  @endforeach
  @endif
</div>

<script>
// ── Edit User Modal ───────────────────────────────────────────────────────────
function openMuEditModal(data) {
  const overlay = document.getElementById('mu-edit-overlay');
  document.getElementById('mu-edit-form').action           = data.action;
  document.getElementById('mu-edit-modal-title').textContent = 'Edit User — ' + data.name;
  document.getElementById('mu-edit-username').value        = data.username;
  document.getElementById('mu-edit-full-name').value       = data.fullName;
  document.getElementById('mu-edit-email').value           = data.email;
  document.getElementById('mu-edit-role').value            = data.role;
  document.getElementById('mu-edit-dept').value            = data.dept;
  document.getElementById('mu-edit-password').value        = '';
  overlay.style.display = 'flex';
}

function closeMuEditModal() {
  document.getElementById('mu-edit-overlay').style.display = 'none';
}

// Backdrop click closes modal
document.getElementById('mu-edit-overlay').addEventListener('click', function (e) {
  if (e.target === this) closeMuEditModal();
});

// Escape key closes modal
document.addEventListener('keydown', function (e) {
  if (e.key === 'Escape' && document.getElementById('mu-edit-overlay').style.display === 'flex') {
    closeMuEditModal();
  }
});

// Event delegation — works for both initial render and AJAX-swapped partials
document.addEventListener('click', function (e) {
  const btn = e.target.closest('.mu-edit-trigger');
  if (!btn) return;
  e.preventDefault();
  openMuEditModal({
    action:   btn.dataset.action,
    name:     btn.dataset.name,
    username: btn.dataset.username,
    fullName: btn.dataset.fullName,
    email:    btn.dataset.email,
    role:     btn.dataset.role,
    dept:     btn.dataset.dept,
  });
});
// ─────────────────────────────────────────────────────────────────────────────

// ── Live Search ───────────────────────────────────────────────────────────────
(function () {
  const input    = document.getElementById('mu-search-input');
  const listWrap = document.getElementById('mu-users-list');
  const countEl  = document.getElementById('mu-users-count');
  const clearBtn = document.getElementById('mu-clear-btn');
  const roleTab  = @json($activeRole);
  const baseUrl  = '{{ route('it.users.index') }}';
  let timer;

  function updateCount(total, q) {
    const s = total !== 1 ? 's' : '';
    const m = q ? ` matching "${q}"` : '';
    countEl.textContent = `${total} account${s}${m}`;
  }

  function doSearch(q) {
    const fetchUrl = new URL(baseUrl);
    fetchUrl.searchParams.set('role_tab', roleTab);
    fetchUrl.searchParams.set('partial', '1');
    if (q) fetchUrl.searchParams.set('search', q);

    const histUrl = new URL(baseUrl);
    histUrl.searchParams.set('role_tab', roleTab);
    if (q) histUrl.searchParams.set('search', q);
    history.replaceState(null, '', histUrl.toString());

    if (clearBtn) clearBtn.style.display = q ? 'inline-flex' : 'none';

    fetch(fetchUrl.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(r => r.json())
      .then(data => {
        listWrap.innerHTML = data.html;
        updateCount(data.total, q);
      });
  }

  if (input) {
    input.addEventListener('input', function () {
      clearTimeout(timer);
      timer = setTimeout(() => doSearch(this.value.trim()), 300);
    });
  }

  if (clearBtn) {
    clearBtn.addEventListener('click', function (e) {
      e.preventDefault();
      if (input) input.value = '';
      doSearch('');
    });
  }
})();
// ─────────────────────────────────────────────────────────────────────────────
</script>

@endsection

