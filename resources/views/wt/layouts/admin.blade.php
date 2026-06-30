<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>WT System</title>
@include('partials.favicons')
<script>
  document.documentElement.classList.add('wt-render-lock');

  (function () {
    try {
      var savedTheme = localStorage.getItem('fjb-theme') || localStorage.getItem('color-theme');
      var theme = savedTheme || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
      var vars = theme === 'dark' ? {
        '--body-bg': '#0f172a',
        '--surface': '#1e293b',
        '--border': '#334155',
        '--text': '#e2e8f0',
        '--muted': '#94a3b8',
        '--table-hover': 'rgba(255,255,255,.04)',
        '--form-input-bg': '#0f172a',
        '--form-input-border': '#334155',
        '--form-input-color': '#e2e8f0',
        '--table-head-bg': '#111827',
        '--table-head-color': '#94a3b8'
      } : {
        '--body-bg': '#f0f4f8',
        '--surface': '#ffffff',
        '--border': '#e2e8f0',
        '--text': '#1e293b',
        '--muted': '#64748b',
        '--table-hover': '#f0f9ff',
        '--form-input-bg': '#ffffff',
        '--form-input-border': '#e2e8f0',
        '--form-input-color': '#1e293b',
        '--table-head-bg': '#f8fafc',
        '--table-head-color': '#475569'
      };

      Object.keys(vars).forEach(function (key) {
        document.documentElement.style.setProperty(key, vars[key]);
      });
      document.documentElement.classList.toggle('dark', theme === 'dark');
      document.documentElement.setAttribute('data-theme', theme);
      document.documentElement.style.colorScheme = theme;
    } catch (error) {
      document.documentElement.setAttribute('data-theme', 'light');
      document.documentElement.style.colorScheme = 'light';
    }
  })();
</script>
<style>
  html.wt-render-lock body {
    opacity: 0 !important;
  }

  html.wt-render-ready body {
    opacity: 1 !important;
  }

  body {
    transition: none !important;
  }
</style>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={darkMode:'class',theme:{extend:{colors:{corp:{navy:'#1F2937',brown:'#075985',gold:'#38bdf8'}}}}}</script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="{{ asset('assets/css/wtsystem.css') }}?v={{ time() }}" rel="stylesheet">
<link href="{{ asset('assets/css/wt-inventory.css') }}" rel="stylesheet">
<style>
/* wtsystem.css is the single source of truth */
/* Layout integrity: prevent any page-specific body CSS from breaking the sidebar position */
body#main-body { flex-direction: row !important; }
body#main-body > .sidebar { order: 0 !important; flex-shrink: 0 !important; }
body#main-body > .main-content { order: 1 !important; flex: 1 !important; min-width: 0 !important; }
.sidebar-brand-row { min-width: 0; overflow: hidden; }
.sidebar-brand { min-width: 0; overflow: hidden; }
.brand-name { min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.brand-name span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.sb-tooltip{position:fixed;transform:translateY(-50%);background:#1e293b;color:#fff;padding:.4rem .65rem;border-radius:7px;font-size:.75rem;font-weight:600;white-space:nowrap;z-index:2000;pointer-events:none;opacity:0;transition:opacity .12s ease;box-shadow:0 6px 16px rgba(0,0,0,.22)}
.sb-tooltip.show{opacity:1}
.sb-tooltip::before{content:'';position:absolute;right:100%;top:50%;transform:translateY(-50%);border:5px solid transparent;border-right-color:#1e293b}
.global-walkie-summary{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:0;border:1px solid var(--border);border-radius:10px;background:var(--surface);overflow:hidden}
.global-walkie-summary-item{display:grid;grid-template-columns:minmax(120px,38%) minmax(0,1fr);align-items:center;min-width:0;padding:11px 14px;border-right:1px solid var(--border);border-bottom:1px solid var(--border)}
.global-walkie-summary-item:nth-child(even){border-right:0}
.global-walkie-summary-label,.global-walkie-warranty-label{color:var(--muted);font-size:9px;font-weight:800;letter-spacing:.12em;line-height:1.25;text-transform:uppercase}
.global-walkie-summary-value,.global-walkie-warranty-value{min-width:0;color:var(--text);font-size:12px;font-weight:900;line-height:1.35;overflow-wrap:anywhere}
.global-walkie-muted{color:var(--muted)!important;font-weight:800!important}
.global-walkie-warranty-section{grid-column:1/-1;padding:14px;background:var(--surface)}
.global-walkie-section-title{margin:0 0 10px;color:var(--muted);font-size:10px;font-weight:900;letter-spacing:.16em;line-height:1.25;text-transform:uppercase}
.global-walkie-warranty-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));border:1px solid var(--border);border-radius:8px;overflow:hidden}
.global-walkie-warranty-item{display:grid;grid-template-columns:minmax(112px,34%) minmax(0,1fr);align-items:center;min-width:0;padding:11px 14px;border-right:1px solid var(--border);background:var(--surface)}
.global-walkie-warranty-item:last-child{border-right:0}
@media (max-width:640px){.global-walkie-summary,.global-walkie-warranty-grid{grid-template-columns:1fr}.global-walkie-summary-item,.global-walkie-warranty-item{grid-template-columns:1fr;gap:4px;padding:10px 12px;border-right:0}.global-walkie-summary-item:nth-child(even){border-right:0}.global-walkie-warranty-item{border-bottom:1px solid var(--border)}.global-walkie-warranty-item:last-child{border-bottom:0}}
</style>
@stack('styles')
@stack('final_styles')
</head>
<body id="main-body">
<script>
  if (localStorage.getItem('wt-sb-collapsed') === '1' && window.innerWidth > 768) {
    document.body.classList.add('sidebar-collapsed');
  }
</script>

@php
    $actualRole = Auth::guard('wt')->user()->wt_role;
    $effectiveRole = $actualRole === 'admin_it'
        ? session('view_mode', $actualRole)
        : $actualRole;
    $isAdminItView = $effectiveRole === 'admin_it';
    $accountRoleLabel = $actualRole === 'admin_it' ? 'ICT' : 'Executive';
    $impersonatorAdminItId = session('impersonator_admin_it_id');
    $isExecutiveImpersonation = $actualRole === 'admin' && filled($impersonatorAdminItId);
    $executiveSwitcherAccounts = $actualRole === 'admin_it'
        ? \App\Models\WT\User::where('wt_role', 'admin')
            ->orderBy('name')
            ->orderBy('staff_no')
            ->get(['id', 'staff_no', 'name', 'dept_name'])
        : collect();
    $approvalBadgeCount = \App\Models\WT\AccessRequest::query()
        ->where(function ($query) use ($effectiveRole) {
            $query->where('status', $effectiveRole === 'admin_it' ? 'Pending IT Approval' : 'Pending Admin Approval')
                ->orWhere('return_status', $effectiveRole === 'admin_it' ? 'Pending IT Approval' : 'Pending Admin Approval');
        })
        ->when($effectiveRole === 'admin', function ($query) {
            $query->where(function ($scoped) {
                $scoped->whereNull('submit_to_admin_id')
                    ->orWhere('submit_to_admin_id', auth()->id());
            });
        })
        ->count();
    if ($effectiveRole === 'admin_it') {
        $approvalBadgeCount += \App\Models\WT\MaintenanceRecord::where('status', 'PENDING ADMIN IT')->count();
    } else {
        $approvalBadgeCount += \App\Models\WT\MaintenanceRecord::where('status', 'WAITING FOR ADMIN')
            ->where('submit_to_admin_id', auth()->id())
            ->count();
    }


@endphp

@php
    $inventoryNavOnDuplicate = request()->routeIs('wt.admin.walkies.duplicateIds') || request()->routeIs('wt.admin.walkies.create.duplicate');
    $inventoryNavOnSpecialUse = request()->routeIs('wt.admin.walkies.specialUse') || request()->routeIs('wt.admin.walkies.create.specialUse');
    $inventoryNavOnMaintenance = request()->routeIs('wt.admin.maintenance.*') || request()->routeIs('wt.admin.walkies.repairFaulty');
    $inventoryNavOnInventory = request()->routeIs('wt.admin.walkies.*') && !($inventoryNavOnDuplicate || $inventoryNavOnSpecialUse || $inventoryNavOnMaintenance || request()->routeIs('wt.admin.walkies.myInventory'));
    $inventoryManagementOpen = $inventoryNavOnInventory || $inventoryNavOnMaintenance || $inventoryNavOnDuplicate || $inventoryNavOnSpecialUse;
    $approvalNavOnPending = request()->routeIs('wt.admin.requests.index') || request()->routeIs('wt.admin.requests.approve') || request()->routeIs('wt.admin.requests.reject') || request()->routeIs('wt.admin.requests.forwardToIT') || request()->routeIs('wt.admin.requests.confirmReturn') || request()->routeIs('wt.admin.damageReports.*');
    $approvalNavOnHistory = request()->routeIs('wt.admin.requests.history');
    $approvalManagementOpen = $approvalNavOnPending || $approvalNavOnHistory;
    $faultyNavOnUserReports = request()->routeIs('wt.admin.faultyReports.*');
    $faultyNavOnThreeMonths = request()->routeIs('wt.admin.reports.faulty3Months');
    $faultyManagementOpen = $faultyNavOnUserReports || $faultyNavOnThreeMonths;
    $requestCreateOpen = request()->routeIs('wt.admin.requests.create') || request()->routeIs('wt.admin.requests.create.*') || request()->routeIs('wt.admin.requests.store') || request()->routeIs('wt.admin.requests.store.*') || request()->routeIs('wt.admin.requests.staffSearch');
@endphp

{{-- Mobile overlay --}}
<div id="mobileSidebarOverlay" onclick="closeMobileSidebar()" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:99;backdrop-filter:blur(2px)"></div>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand-row flex items-center gap-x-3 pl-4 pr-3">
    <a href="{{ request()->fullUrl() }}" class="sidebar-brand flex min-w-0 flex-1 items-center gap-x-3" title="Refresh page">
      <div class="sidebar-logo-shell flex shrink-0 items-center justify-center">
        <img src="{{ asset('assets/images/fjb-logo.svg') }}" alt="FJB" class="sidebar-brand-logo" onerror="this.onerror=null;this.src='{{ asset('assets/img/logo_transparent.png') }}'">
      </div>
      <div class="brand-name">WT System<span>Walkie Talkie Management</span></div>
    </a>
    <button class="sidebar-collapse-toggle" onclick="toggleWTSidebar()" aria-label="Toggle Sidebar" title="Collapse Sidebar">
      <i class="fas fa-chevron-left"></i>
    </button>
  </div>

  <nav class="sidebar-nav">
    @if($isAdminItView)
    <div class="nav-section-label" style="margin-top:4px">Main</div>
    <a href="{{ route('wt.admin.dashboard') }}" class="nav-link {{ request()->routeIs('wt.admin.dashboard') ? 'active-sidebar' : '' }}" title="Dashboard">
      <i class="fas fa-home"></i> <span>Dashboard</span>
    </a>
    @endif

    <div class="nav-section-label">{{ $isAdminItView ? 'Management' : 'Personal Assets' }}</div>

    @if($isAdminItView)
    {{-- Inventory Tools --}}
    <div class="dropdown-wrapper {{ $inventoryManagementOpen ? 'open' : '' }}">
      <button type="button" class="dropdown-trigger has-info {{ $inventoryManagementOpen ? 'active-sidebar' : '' }}" onclick="toggleDropdown(this)">
        <i class="fas fa-layer-group" style="width:20px;text-align:center;flex-shrink:0;font-size:15px"></i>
        <span style="flex:1">Inventory Tools</span>
        @include('wt.partials.sidebar-info', ['text' => 'Open inventory management tools including inventory list, repair monitoring, duplicate IDs, and special use units.'])
        <i class="fas fa-chevron-right dropdown-chevron"></i>
      </button>
      <div class="dropdown-content">
        <a href="{{ route('wt.admin.walkies.index') }}" class="sub-nav-link {{ $inventoryNavOnInventory ? 'active' : '' }}">
          <i class="fas fa-list" style="font-size:12px;width:14px"></i> Inventory List
        </a>
        <a href="{{ route('wt.admin.maintenance.index') }}" class="sub-nav-link {{ $inventoryNavOnMaintenance ? 'active' : '' }}">
          <i class="fas fa-wrench" style="font-size:12px;width:14px"></i> Under Repair / Faulty
        </a>
        <a href="{{ route('wt.admin.walkies.duplicateIds') }}" class="sub-nav-link {{ $inventoryNavOnDuplicate ? 'active' : '' }}">
          <i class="fas fa-copy" style="font-size:12px;width:14px"></i> Duplicated ID
        </a>
        <a href="{{ route('wt.admin.walkies.specialUse') }}" class="sub-nav-link {{ $inventoryNavOnSpecialUse ? 'active' : '' }}">
          <i class="fas fa-star" style="font-size:12px;width:14px"></i> Special Use
        </a>
      </div>
    </div>

    {{-- Approvals --}}
    <div class="dropdown-wrapper {{ $approvalManagementOpen ? 'open' : '' }}">
      <button type="button" class="dropdown-trigger has-info {{ $approvalManagementOpen ? 'active-sidebar' : '' }}" onclick="toggleDropdown(this)">
        <i class="fas fa-inbox" style="width:20px;text-align:center;flex-shrink:0;font-size:15px"></i>
        <span style="flex:1">Approvals
          @if($approvalBadgeCount > 0)
          <span class="pending-nav-badge approval-new-badge" style="margin-left:4px">New</span>
          @endif
        </span>
        @include('wt.partials.sidebar-info', ['text' => 'Review approvals and open ICT approval history.'])
        <i class="fas fa-chevron-right dropdown-chevron"></i>
      </button>
      <div class="dropdown-content">
        <a href="{{ route('wt.admin.requests.index') }}" class="sub-nav-link {{ $approvalNavOnPending ? 'active' : '' }}">
          <i class="fas fa-clock" style="font-size:12px;width:14px"></i> Pending
          @if($approvalBadgeCount > 0)
          <span class="pending-nav-badge">{{ $approvalBadgeCount > 9 ? '9+' : $approvalBadgeCount }}</span>
          @endif
        </a>
        <a href="{{ route('wt.admin.requests.history') }}" class="sub-nav-link {{ $approvalNavOnHistory ? 'active' : '' }}">
          <i class="fas fa-history" style="font-size:12px;width:14px"></i> History
        </a>
      </div>
    </div>

    {{-- Faulty Reports --}}
    <div class="dropdown-wrapper {{ $faultyManagementOpen ? 'open' : '' }}">
      <button type="button" class="dropdown-trigger has-info {{ $faultyManagementOpen ? 'active-sidebar' : '' }}" onclick="toggleDropdown(this)">
        <i class="fa-solid fa-triangle-exclamation" style="width:20px;text-align:center;flex-shrink:0;font-size:15px"></i>
        <span style="flex:1">Faulty Reports</span>
        @include('wt.partials.sidebar-info', ['text' => 'Consolidated view for walkie requests, handovers, and faulty reports status.'])
        <i class="fas fa-chevron-right dropdown-chevron"></i>
      </button>
      <div class="dropdown-content">
        <a href="{{ route('wt.admin.faultyReports.index') }}" class="sub-nav-link {{ $faultyNavOnUserReports ? 'active' : '' }}">
          <i class="fas fa-user-times" style="font-size:12px;width:14px"></i> User Reports
        </a>
        <a href="{{ route('wt.admin.reports.faulty3Months') }}" class="sub-nav-link {{ $faultyNavOnThreeMonths ? 'active' : '' }}">
          <i class="fas fa-chart-bar" style="font-size:12px;width:14px"></i> Monthly Report
        </a>
      </div>
    </div>

    @else
    {{-- Executive/Admin view personal assets --}}
    <a href="{{ route('wt.admin.walkies.myInventory') }}" class="nav-link has-info {{ request()->routeIs('wt.admin.walkies.myInventory') ? 'active-sidebar' : '' }}" title="My Inventory">
      <i class="fa-solid fa-box" style="width:20px;text-align:center;flex-shrink:0"></i> <span>My Inventory</span>
      @include('wt.partials.sidebar-info', ['text' => 'View walkie talkies currently assigned to you.'])
    </a>

    <div class="nav-section-label">Asset Interactive</div>
    <a href="{{ route('wt.admin.returns.create', ['mode' => 'self']) }}" class="nav-link has-info {{ request()->routeIs('wt.admin.returns.*') ? 'active-sidebar' : '' }}" title="Return Unit">
      <i class="fa-solid fa-rotate-left" style="width:20px;text-align:center;flex-shrink:0"></i> <span>Return Unit</span>
      @include('wt.partials.sidebar-info', ['text' => 'Submit walkie return records for your own assigned unit.'])
    </a>
    <a href="{{ route('wt.admin.damages.form', ['mode' => 'staff']) }}" class="nav-link has-info {{ request()->routeIs('wt.admin.damages.*') ? 'active-sidebar' : '' }}" title="Report Faulty">
      <i class="fa-solid fa-triangle-exclamation" style="width:20px;text-align:center;flex-shrink:0"></i> <span>Report Faulty</span>
      @include('wt.partials.sidebar-info', ['text' => 'Report damaged, faulty, missing, or problem units for yourself or on behalf of a recipient.'])
    </a>

    {{-- Request Walkie Talkie --}}
    <div class="dropdown-wrapper {{ $requestCreateOpen ? 'open' : '' }}">
      <button type="button" class="dropdown-trigger has-info {{ $requestCreateOpen ? 'active-sidebar' : '' }}" onclick="toggleDropdown(this)" title="Request Walkie Talkie">
        <i class="fas fa-plus-circle" style="width:20px;text-align:center;flex-shrink:0;font-size:15px"></i>
        <span style="flex:1">Request Walkie Talkie</span>
        @include('wt.partials.sidebar-info', ['text' => 'Submit a walkie talkie request for yourself or on behalf of a recipient. ICT will assign the available unit later.'])
        <i class="fas fa-chevron-right dropdown-chevron"></i>
      </button>
      <div class="dropdown-content">
        <a href="{{ route('wt.admin.requests.create') }}" class="sub-nav-link {{ (request()->routeIs('wt.admin.requests.create') || request()->routeIs('wt.admin.requests.create.individual') || request()->routeIs('wt.admin.requests.create.shared')) ? 'active' : '' }}">
          <i class="fas fa-file-alt" style="font-size:12px;width:14px"></i> Long Term Request
        </a>
        <a href="{{ route('wt.admin.requests.create.temporary') }}" class="sub-nav-link {{ request()->routeIs('wt.admin.requests.create.temporary*') ? 'active' : '' }}">
          <i class="fas fa-clock" style="font-size:12px;width:14px"></i> Temporary Request
        </a>
      </div>
    </div>

    <a href="{{ route('wt.admin.all.status') }}" class="nav-link has-info {{ request()->routeIs('wt.admin.all.status') ? 'active-sidebar' : '' }}" title="All Status">
      <i class="fa-solid fa-list-check" style="width:20px;text-align:center;flex-shrink:0"></i> <span>All Status</span>
      @include('wt.partials.sidebar-info', ['text' => 'Consolidated view for walkie requests, handovers, and faulty reports status.'])
    </a>
    @endif

    {{-- My Account --}}
    <div class="nav-section-label">My Account</div>
    <a href="{{ route('wt.admin.profile') }}" class="nav-link has-info {{ request()->routeIs('wt.admin.profile') ? 'active-sidebar' : '' }}" title="My Profile">
      <i class="fas fa-user-circle" style="width:20px;text-align:center;flex-shrink:0"></i> <span>My Profile</span>
      @include('wt.partials.sidebar-info', ['text' => 'View and update your account profile information.'])
    </a>
    <a href="{{ route('wt.admin.policies') }}" class="nav-link has-info {{ request()->routeIs('wt.admin.policies') ? 'active-sidebar' : '' }}" title="Role Permissions Matrix">
      <i class="fa-solid fa-table-list" style="width:20px;text-align:center;flex-shrink:0"></i> <span>Role Matrix</span>
      @include('wt.partials.sidebar-info', ['text' => 'View WT System access permissions for ICT and executive users.'])
    </a>

    {{-- System Control (IT only) --}}
    @if($isAdminItView)
    <div class="nav-section-label">System Control</div>
    <a href="{{ route('wt.admin.users.index') }}" class="nav-link has-info {{ request()->routeIs('wt.admin.users.*') ? 'active-sidebar' : '' }}" title="Users Control">
      <i class="fas fa-users" style="width:20px;text-align:center;flex-shrink:0"></i> <span>Users Control</span>
      @include('wt.partials.sidebar-info', ['text' => 'Manage WT system user accounts and access roles.'])
    </a>
    <a href="{{ route('wt.admin.masterData.index') }}" class="nav-link has-info {{ request()->routeIs('wt.admin.masterData.*') ? 'active-sidebar' : '' }}" title="Master Data">
      <i class="fas fa-database" style="width:20px;text-align:center;flex-shrink:0"></i> <span>Master Data</span>
      @include('wt.partials.sidebar-info', ['text' => 'Manage dropdown options and master records used across the WT system.'])
    </a>
    <a href="{{ route('wt.admin.activity.index') }}" class="nav-link has-info {{ request()->routeIs('wt.admin.activity.*') ? 'active-sidebar' : '' }}" title="System Logs">
      <i class="fas fa-clipboard-list" style="width:20px;text-align:center;flex-shrink:0"></i> <span>System Logs</span>
      @include('wt.partials.sidebar-info', ['text' => 'Review WT system activity logs and audit actions.'])
    </a>
    @endif

    <div style="border-top:1px solid rgba(255,255,255,.08);margin:12px 0 8px"></div>
    <a href="{{ route('home') }}" class="nav-link" title="Back to Portal">
      <i class="fas fa-th-large" style="width:20px;text-align:center;flex-shrink:0"></i> <span>Back to Portal</span>
    </a>
    <a href="{{ route('it.login') }}" class="nav-link" title="IT System">
      <i class="fas fa-desktop" style="width:20px;text-align:center;flex-shrink:0"></i> <span>IT System</span>
    </a>
    <a href="{{ route('dashboard') }}" class="nav-link" title="HR Portal">
      <i class="fas fa-building" style="width:20px;text-align:center;flex-shrink:0"></i> <span>HR Portal</span>
    </a>
  </nav>

  <div class="sidebar-footer">
    <div class="user-card">
      <div class="user-avatar">{{ strtoupper(substr(Auth::guard('wt')->user()->username ?? 'A', 0, 1)) }}</div>
      <div class="user-info">
        <div class="user-name">{{ Auth::guard('wt')->user()->username ?? 'User' }}</div>
        <div class="user-role">{{ $accountRoleLabel }}</div>
      </div>
    </div>
  </div>
</aside>
<!-- MAIN -->
<div class="main-content">
  <div class="topbar">
    <button class="sidebar-toggle" onclick="toggleMobileSidebar()">
      <i class="fas fa-bars"></i>
    </button>
    @php $topbarTitle = View::yieldContent('page_title') ?: View::yieldContent('title') ?: 'Dashboard'; @endphp
    <div class="topbar-left">
      <div class="topbar-title">{{ $topbarTitle }}</div>
      <div class="topbar-breadcrumb">
        FGV Johor Bulkers &rsaquo;
        <a href="{{ route('wt.admin.dashboard') }}" style="color:var(--muted);text-decoration:none">Dashboard</a>
        &rsaquo; {{ $topbarTitle }}
      </div>
    </div>
    <div class="topbar-right">
      <span style="font-size:12px;color:var(--muted)" id="liveClock"></span>

      {{-- ICT/Executive role switcher --}}
      @if($actualRole === 'admin_it')
      <div class="topbar-role-switcher">
        <a href="{{ route('wt.switch_view', 'admin_it') }}"
          class="topbar-role-switcher-link {{ $effectiveRole === 'admin_it' ? 'active-switcher' : '' }}"
          style="display:flex;align-items:center;gap:5px;padding:5px 10px;border-radius:6px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:{{ $effectiveRole === 'admin_it' ? '#fff' : 'var(--muted)' }};background:{{ $effectiveRole === 'admin_it' ? 'var(--navy)' : 'transparent' }};text-decoration:none;transition:all .15s">
          <i class="fas fa-user-shield" style="font-size:10px"></i> ICT
        </a>
        <form action="{{ route('wt.switch_executive_account') }}" method="POST" style="display:flex;align-items:center">
          @csrf
          <select name="executive_user_id" onchange="if(this.value) this.form.submit()"
            style="border:none;background:transparent;color:var(--muted);font-size:10px;font-weight:700;outline:none;padding:4px 8px;cursor:pointer;font-family:'Inter',sans-serif;text-transform:uppercase;max-width:180px">
            <option value="">Executive Account</option>
            @foreach($executiveSwitcherAccounts as $executiveAccount)
              <option value="{{ $executiveAccount->user_id ?? $executiveAccount->id }}">
                {{ strtoupper($executiveAccount->full_name ?: ($executiveAccount->staff_id ?? '')) }}{{ $executiveAccount->department ? ' - ' . strtoupper($executiveAccount->department) : '' }}
              </option>
            @endforeach
          </select>
        </form>
      </div>
      @endif

      {{-- Return to ICT --}}
      @if($isExecutiveImpersonation)
      <form action="{{ route('wt.return_to_ict_account') }}" method="POST" style="margin:0">
        @csrf
        <button type="submit" class="topbar-return-btn">
          <i class="fas fa-arrow-left" style="font-size:10px"></i> Return ICT
        </button>
      </form>
      @endif

      {{-- Theme toggle --}}
      <button id="theme-toggle" type="button" class="topbar-action-btn" title="Toggle dark mode">
        <i id="theme-toggle-dark-icon" class="hidden fas fa-moon" style="font-size:16px"></i>
        <i id="theme-toggle-light-icon" class="hidden fas fa-sun" style="font-size:16px;color:#f59e0b"></i>
      </button>

      {{-- User badge --}}
      <a href="{{ route('wt.admin.profile') }}" class="topbar-user" title="My profile">
        <span class="topbar-role-badge">{{ $accountRoleLabel }}</span>
        <span class="topbar-user-name">{{ Auth::guard('wt')->user()->username ?? 'User' }}</span>
      </a>

      {{-- Logout --}}
      <form id="logout-form" action="{{ route('wt.logout') }}" method="POST" class="d-none" style="display:none">@csrf</form>
      <button onclick="handleLogout()" class="topbar-signout-btn" title="Sign out">
        Sign Out <i class="fas fa-sign-out-alt"></i>
      </button>
    </div>
  </div>

  <div class="page-body">
      <div class="content-surface">
      @include('wt.partials.flash-alerts')
      @yield('content')
      @include('components.ui.standardizer')
      <style>
        body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable, .clean-admin-table) {
          width: 100% !important;
          min-width: 100% !important;
          table-layout: fixed !important;
          border-collapse: collapse !important;
          margin: 0 !important;
          background: #ffffff !important;
        }
        body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable, .clean-admin-table) thead th {
          height: 36px !important;
          padding: 9px 10px !important;
          background: #f8fafc !important;
          border: 1px solid #cbd5e1 !important;
          border-radius: 0 !important;
          color: #526781 !important;
          font-size: 12px !important;
          font-weight: 900 !important;
          letter-spacing: .03em !important;
          line-height: 1.2 !important;
          text-align: center !important;
          text-transform: uppercase !important;
          vertical-align: middle !important;
          white-space: nowrap !important;
        }
        body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable, .clean-admin-table) tbody td {
          height: 42px !important;
          max-width: 1px !important;
          padding: 8px 10px !important;
          background: #ffffff !important;
          border: 1px solid #e2e8f0 !important;
          border-radius: 0 !important;
          color: #0f172a !important;
          font-size: 12px !important;
          font-weight: 700 !important;
          line-height: 1.25 !important;
          overflow: hidden !important;
          text-align: center !important;
          text-overflow: ellipsis !important;
          vertical-align: middle !important;
          white-space: nowrap !important;
        }
        body .content-surface :is(#walkiesTable, #duplicateTable, #specialTable, .clean-admin-table) tbody td :is(
          .inventory-item-title,
          .inventory-assigned-primary,
          .inventory-assigned-meta,
          .dup-change-id-val,
          .dup-change-id-empty,
          .line-clamp-1,
          .font-black,
          .text-slate-500
        ) {
          text-align: center !important;
        }
        body .content-surface :is(#walkiesTable, #duplicateTable, #specialTable, .clean-admin-table) tbody td :is(
          .inventory-action-buttons,
          .dup-actions,
          .special-action-buttons,
          .clean-admin-actions,
          .inline-flex,
          .flex
        ) {
          justify-content: center !important;
        }
        body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable, .clean-admin-table) tbody tr:hover td {
          background: #f8fafc !important;
        }
        body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable, .clean-admin-table) :is(th, td):nth-child(1) {
          width: 12% !important;
          text-align: center !important;
        }
        body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable, .clean-admin-table) :is(th, td):nth-child(2) {
          width: 12% !important;
          text-align: center !important;
        }
        body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable, .clean-admin-table) :is(th, td):nth-child(3) {
          width: 14% !important;
          text-align: center !important;
        }
        body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable, .clean-admin-table) :is(th, td):nth-child(4) {
          width: 14% !important;
          text-align: center !important;
        }
        body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable, .clean-admin-table) :is(th, td):last-child {
          width: 15% !important;
          text-align: center !important;
        }
        body .content-surface :is(.clean-admin-table-shell, .inventory-table-shell, .special-table-shell, .duplicate-table-shell, #mainTableContainer) {
          overflow: hidden !important;
          border: 1px solid #cbd5e1 !important;
          border-radius: 8px !important;
          background: #ffffff !important;
        }
        body .content-surface :is(.clean-admin-table-scroll, #inventoryTableScroll) {
          overflow-x: hidden !important;
          background: #ffffff !important;
        }
        body .content-surface :is(.clean-admin-pill, .maintenance-status-pill, .maintenance-done-pill) {
          display: inline-flex !important;
          align-items: center !important;
          justify-content: center !important;
          min-height: 28px !important;
          padding: 0 12px !important;
          border-radius: 7px !important;
          font-size: 11px !important;
          font-weight: 900 !important;
          letter-spacing: .02em !important;
          line-height: 1 !important;
          text-transform: uppercase !important;
        }
        html.dark body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable, .clean-admin-table),
        html[data-theme="dark"] body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable, .clean-admin-table) {
          background: #111827 !important;
        }
        html.dark body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable, .clean-admin-table) thead th,
        html[data-theme="dark"] body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable, .clean-admin-table) thead th {
          background: #1f2937 !important;
          border-color: #2f3b4f !important;
          color: #e5edf7 !important;
        }
        html.dark body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable, .clean-admin-table) tbody td,
        html[data-theme="dark"] body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable, .clean-admin-table) tbody td {
          background: #111827 !important;
          border-color: #263244 !important;
          color: #e5edf7 !important;
        }
        html.dark body .content-surface :is(.clean-admin-table-shell, .inventory-table-shell, .special-table-shell, .duplicate-table-shell, #mainTableContainer),
        html[data-theme="dark"] body .content-surface :is(.clean-admin-table-shell, .inventory-table-shell, .special-table-shell, .duplicate-table-shell, #mainTableContainer),
        html.dark body .content-surface :is(.clean-admin-table-scroll, #inventoryTableScroll),
        html[data-theme="dark"] body .content-surface :is(.clean-admin-table-scroll, #inventoryTableScroll) {
          background: #111827 !important;
          border-color: #263244 !important;
        }
        body .content-surface .maintenance-page-shell #mainTableContainer.clean-admin-table-shell,
        body .content-surface .maintenance-page-shell #mainTableContainer .clean-admin-table-scroll {
          max-width: 100% !important;
          overflow-x: hidden !important;
          overflow-y: hidden !important;
          padding-bottom: 0 !important;
          cursor: default !important;
          scrollbar-width: none !important;
          background: #ffffff !important;
          border-color: #cbd5e1 !important;
        }
        body .content-surface .maintenance-page-shell #mainTableContainer .clean-admin-table-scroll::-webkit-scrollbar {
          display: none !important;
          width: 0 !important;
          height: 0 !important;
        }
        body .content-surface .maintenance-page-shell #maintTable.clean-admin-table,
        body .content-surface .maintenance-page-shell #maintenanceTable.clean-admin-table {
          width: 100% !important;
          min-width: 100% !important;
          max-width: 100% !important;
          table-layout: fixed !important;
          margin: 0 !important;
          background: #ffffff !important;
        }
        body .content-surface .maintenance-page-shell #maintTable.clean-admin-table thead th,
        body .content-surface .maintenance-page-shell #maintenanceTable.clean-admin-table thead th {
          height: 36px !important;
          padding: 9px 10px !important;
          background: #f8fafc !important;
          border: 1px solid #cbd5e1 !important;
          color: #526781 !important;
          font-size: 12px !important;
          font-weight: 900 !important;
          letter-spacing: .03em !important;
          line-height: 1.2 !important;
          text-align: center !important;
          text-transform: uppercase !important;
          vertical-align: middle !important;
          white-space: nowrap !important;
        }
        body .content-surface .maintenance-page-shell #maintTable.clean-admin-table tbody td,
        body .content-surface .maintenance-page-shell #maintenanceTable.clean-admin-table tbody td,
        body .content-surface .maintenance-page-shell #maintTable.clean-admin-table tbody td.maintenance-action-col,
        body .content-surface .maintenance-page-shell #maintenanceTable.clean-admin-table tbody td.maintenance-action-col {
          height: 42px !important;
          min-width: 0 !important;
          max-width: none !important;
          padding: 8px 10px !important;
          background: #ffffff !important;
          border: 1px solid #e2e8f0 !important;
          color: #0f172a !important;
          font-size: 12px !important;
          font-weight: 700 !important;
          line-height: 1.25 !important;
          overflow: hidden !important;
          text-overflow: ellipsis !important;
          vertical-align: middle !important;
          white-space: nowrap !important;
        }
        body .content-surface .maintenance-page-shell #maintTable.clean-admin-table :is(th, td):nth-child(1),
        body .content-surface .maintenance-page-shell #maintenanceTable.clean-admin-table :is(th, td):nth-child(1) { width: 12% !important; text-align: center !important; }
        body .content-surface .maintenance-page-shell #maintTable.clean-admin-table :is(th, td):nth-child(2),
        body .content-surface .maintenance-page-shell #maintenanceTable.clean-admin-table :is(th, td):nth-child(2) { width: 12% !important; text-align: center !important; }
        body .content-surface .maintenance-page-shell #maintTable.clean-admin-table :is(th, td):nth-child(3),
        body .content-surface .maintenance-page-shell #maintenanceTable.clean-admin-table :is(th, td):nth-child(3) { width: 16% !important; text-align: center !important; }
        body .content-surface .maintenance-page-shell #maintTable.clean-admin-table :is(th, td):nth-child(4),
        body .content-surface .maintenance-page-shell #maintenanceTable.clean-admin-table :is(th, td):nth-child(4) { width: 16% !important; text-align: center !important; }
        body .content-surface .maintenance-page-shell #maintTable.clean-admin-table :is(th, td):nth-child(5),
        body .content-surface .maintenance-page-shell #maintenanceTable.clean-admin-table :is(th, td):nth-child(5) { width: 29% !important; text-align: center !important; }
        body .content-surface .maintenance-page-shell #maintTable.clean-admin-table :is(th, td):nth-child(6),
        body .content-surface .maintenance-page-shell #maintenanceTable.clean-admin-table :is(th, td):nth-child(6),
        body .content-surface .maintenance-page-shell #maintTable.clean-admin-table .maintenance-action-col,
        body .content-surface .maintenance-page-shell #maintenanceTable.clean-admin-table .maintenance-action-col {
          width: 15% !important;
          min-width: 0 !important;
          max-width: none !important;
          text-align: center !important;
        }
        body .content-surface .maintenance-page-shell #maintTable.clean-admin-table thead th,
        body .content-surface .maintenance-page-shell #maintenanceTable.clean-admin-table thead th,
        body .content-surface .maintenance-page-shell #maintTable.clean-admin-table thead th.maintenance-action-col,
        body .content-surface .maintenance-page-shell #maintenanceTable.clean-admin-table thead th.maintenance-action-col {
          background: #f8fafc !important;
          border: 1px solid #cbd5e1 !important;
          color: #526781 !important;
          font-weight: 900 !important;
          text-align: center !important;
        }
        body .content-surface .maintenance-page-shell #maintTable.clean-admin-table tbody td,
        body .content-surface .maintenance-page-shell #maintenanceTable.clean-admin-table tbody td {
          text-align: center !important;
        }
        body .content-surface .maintenance-page-shell #maintTable .maintenance-action-stack,
        body .content-surface .maintenance-page-shell #maintenanceTable .maintenance-action-stack,
        body .content-surface .maintenance-page-shell #maintenanceTable .clean-admin-actions {
          display: inline-flex !important;
          align-items: center !important;
          justify-content: center !important;
          gap: 4px !important;
          width: auto !important;
          max-width: 100% !important;
        }
        body .content-surface .maintenance-page-shell #maintTable .maintenance-action-stack .wt-btn,
        body .content-surface .maintenance-page-shell #maintenanceTable .clean-admin-actions .wt-btn {
          width: 28px !important;
          min-width: 28px !important;
          max-width: 28px !important;
          height: 28px !important;
          min-height: 28px !important;
          padding: 0 !important;
          border-radius: 6px !important;
          font-size: 0 !important;
        }
        body .content-surface .maintenance-page-shell #maintTable .maintenance-action-stack .wt-btn i,
        body .content-surface .maintenance-page-shell #maintenanceTable .clean-admin-actions .wt-btn i {
          font-size: 12px !important;
          margin: 0 !important;
        }
        html.dark body .content-surface .maintenance-page-shell #mainTableContainer.clean-admin-table-shell,
        html.dark body .content-surface .maintenance-page-shell #mainTableContainer .clean-admin-table-scroll,
        html.dark body .content-surface .maintenance-page-shell #maintTable.clean-admin-table,
        html.dark body .content-surface .maintenance-page-shell #maintenanceTable.clean-admin-table,
        html[data-theme="dark"] body .content-surface .maintenance-page-shell #mainTableContainer.clean-admin-table-shell,
        html[data-theme="dark"] body .content-surface .maintenance-page-shell #mainTableContainer .clean-admin-table-scroll,
        html[data-theme="dark"] body .content-surface .maintenance-page-shell #maintTable.clean-admin-table,
        html[data-theme="dark"] body .content-surface .maintenance-page-shell #maintenanceTable.clean-admin-table {
          background: #111827 !important;
          border-color: #263244 !important;
        }
        html.dark body .content-surface .maintenance-page-shell #maintTable.clean-admin-table tbody td,
        html.dark body .content-surface .maintenance-page-shell #maintenanceTable.clean-admin-table tbody td,
        html[data-theme="dark"] body .content-surface .maintenance-page-shell #maintTable.clean-admin-table tbody td,
        html[data-theme="dark"] body .content-surface .maintenance-page-shell #maintenanceTable.clean-admin-table tbody td {
          background: #111827 !important;
          border-color: #263244 !important;
          color: #e5edf7 !important;
        }
        body .content-surface .page-header-block,
        body .content-surface .inventory-page-header,
        body .content-surface .maintenance-page-shell > .page-header-block,
        body .content-surface .unused-page-shell > .page-header-block,
        body .content-surface .special-page-shell > .page-header-block,
        body .content-surface .duplicate-hero .page-header-block,
        body .content-surface .adminit-section-header {
          background: transparent !important;
          background-color: transparent !important;
          border: 0 !important;
          border-left: 0 !important;
          box-shadow: none !important;
          padding-left: 0 !important;
          padding-right: 0 !important;
        }
        body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable) :is(
          .inventory-action-buttons,
          .maintenance-action-stack,
          .clean-admin-actions,
          .dup-actions,
          .special-action-buttons
        ) {
          display: inline-flex !important;
          align-items: center !important;
          justify-content: center !important;
          gap: 6px !important;
          width: auto !important;
          max-width: 100% !important;
          margin: 0 !important;
          white-space: nowrap !important;
        }
        body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable) :is(
          .inventory-action-buttons form,
          .maintenance-action-stack form,
          .clean-admin-actions form,
          .dup-actions form,
          .special-action-buttons form
        ) {
          display: inline-flex !important;
          align-items: center !important;
          margin: 0 !important;
          padding: 0 !important;
          line-height: 1 !important;
        }
        body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable) :is(
          .inventory-action-buttons .btn,
          .inventory-action-buttons .wt-btn,
          .maintenance-action-stack .btn,
          .maintenance-action-stack .wt-btn,
          .clean-admin-actions .btn,
          .clean-admin-actions .wt-btn,
          .dup-actions .btn,
          .dup-actions .wt-btn,
          .special-action-buttons .btn,
          .special-action-buttons .wt-btn
        ) {
          position: static !important;
          display: inline-flex !important;
          align-items: center !important;
          justify-content: center !important;
          gap: 5px !important;
          width: 66px !important;
          min-width: 66px !important;
          max-width: 66px !important;
          height: 28px !important;
          min-height: 28px !important;
          padding: 0 7px !important;
          border: 1px solid transparent !important;
          border-radius: 7px !important;
          box-shadow: none !important;
          color: #ffffff !important;
          font-size: 10px !important;
          font-weight: 900 !important;
          letter-spacing: 0 !important;
          line-height: 1 !important;
          text-align: center !important;
          text-decoration: none !important;
          text-transform: uppercase !important;
          vertical-align: middle !important;
          white-space: nowrap !important;
          overflow: hidden !important;
        }
        body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable) :is(
          .inventory-action-buttons .btn i,
          .inventory-action-buttons .wt-btn i,
          .maintenance-action-stack .btn i,
          .maintenance-action-stack .wt-btn i,
          .clean-admin-actions .btn i,
          .clean-admin-actions .wt-btn i,
          .dup-actions .btn i,
          .dup-actions .wt-btn i,
          .special-action-buttons .btn i,
          .special-action-buttons .wt-btn i
        ) {
          position: static !important;
          display: inline-flex !important;
          align-items: center !important;
          justify-content: center !important;
          width: 12px !important;
          min-width: 12px !important;
          height: 12px !important;
          margin: 0 !important;
          font-size: 11px !important;
          line-height: 1 !important;
          transform: none !important;
        }
        body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable) :is(
          .inventory-action-buttons .btn span,
          .inventory-action-buttons .wt-btn span,
          .maintenance-action-stack .btn span,
          .maintenance-action-stack .wt-btn span,
          .clean-admin-actions .btn span,
          .clean-admin-actions .wt-btn span,
          .dup-actions .btn span,
          .dup-actions .wt-btn span,
          .special-action-buttons .btn span,
          .special-action-buttons .wt-btn span
        ) {
          display: inline-block !important;
          min-width: 0 !important;
          overflow: hidden !important;
          text-overflow: ellipsis !important;
          line-height: 1 !important;
        }
        body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable) :is(.btn-info, .maintenance-action-view) {
          border-color: #0284c7 !important;
          background: #0284c7 !important;
        }
        body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable) :is(.btn-primary, .maintenance-action-edit) {
          border-color: #2563eb !important;
          background: #2563eb !important;
        }
        body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable) :is(.btn-danger, .wt-btn-danger, .maintenance-action-delete) {
          border-color: #dc2626 !important;
          background: #dc2626 !important;
        }
        body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable) :is(.btn-success) {
          border-color: #16a34a !important;
          background: #16a34a !important;
        }
        body .content-surface :is(#walkiesTable, #maintTable, #maintenanceTable, #duplicateTable, #specialTable) :is(.btn-secondary, .btn:disabled, .wt-btn:disabled) {
          border-color: #94a3b8 !important;
          background: #94a3b8 !important;
          color: #ffffff !important;
          opacity: .75 !important;
        }
        body .content-surface #specialTable :is(th, td):nth-child(1) {
          width: 9% !important;
          text-align: center !important;
        }
        body .content-surface #specialTable :is(th, td):nth-child(2) {
          width: 10% !important;
          text-align: center !important;
        }
        body .content-surface #specialTable :is(th, td):nth-child(3) {
          width: 15% !important;
          text-align: center !important;
        }
        body .content-surface #specialTable :is(th, td):nth-child(4) {
          width: 12% !important;
          text-align: center !important;
        }
        body .content-surface #specialTable :is(th, td):nth-child(5) {
          width: 31% !important;
          text-align: center !important;
        }
        body .content-surface #specialTable :is(th, td):nth-child(6) {
          width: 8% !important;
          text-align: center !important;
        }
        body .content-surface #specialTable :is(th, td):nth-child(7) {
          width: 15% !important;
          text-align: center !important;
        }
        body .content-surface :is(
          .page-header-block,
          .inventory-page-header,
          .adminit-section-header,
          .wt-data-page-header,
          .clean-admin-filter,
          .inventory-table-shell,
          .clean-admin-table-shell,
          .duplicate-table-shell,
          .special-table-shell,
          #mainTableContainer,
          #walkiesTable,
          #maintTable,
          #maintenanceTable,
          #duplicateTable,
          #specialTable
        ),
        body .content-surface :is(
          .inventory-page-shell,
          .maintenance-page-shell,
          .unused-page-shell,
          .special-page-shell,
          .duplicate-hero
        ) :is([class*="border-l-"], [class*="border-left"], tr, th:first-child, td:first-child) {
          border-left: 0 !important;
          border-left-width: 0 !important;
          border-left-color: transparent !important;
        }
        body .content-surface :is(
          #walkiesTable,
          #maintTable,
          #maintenanceTable,
          #duplicateTable,
          #specialTable
        ) :is(th:first-child, td:first-child)::before,
        body .content-surface :is(
          .page-header-block,
          .inventory-page-header,
          .adminit-section-header,
          .wt-data-page-header
        )::before {
          display: none !important;
          width: 0 !important;
          content: none !important;
        }
        body .content-surface :is(.wt-data-scrollbar, .wt-data-scrollbar-thumb) {
          display: none !important;
          width: 0 !important;
          height: 0 !important;
          background: transparent !important;
          border: 0 !important;
          opacity: 0 !important;
        }
        body .content-surface .clean-admin-table-scroll::-webkit-scrollbar,
        body .content-surface .clean-admin-table-scroll::-webkit-scrollbar-thumb,
        body .content-surface .duplicate-table-scroll::-webkit-scrollbar,
        body .content-surface .duplicate-table-scroll::-webkit-scrollbar-thumb,
        body .content-surface .special-table-scroll::-webkit-scrollbar,
        body .content-surface .special-table-scroll::-webkit-scrollbar-thumb,
        body .content-surface .wt-data-scroll::-webkit-scrollbar,
        body .content-surface .wt-data-scroll::-webkit-scrollbar-thumb,
        body .content-surface .dataTables_scrollBody::-webkit-scrollbar,
        body .content-surface .dataTables_scrollBody::-webkit-scrollbar-thumb {
          display: none !important;
          width: 0 !important;
          height: 0 !important;
          background: transparent !important;
          border: 0 !important;
        }
        body .content-surface :is(
          .clean-admin-table-scroll,
          .duplicate-table-scroll,
          .special-table-scroll,
          .wt-data-scroll,
          .dataTables_scrollBody
        ) {
          scrollbar-width: none !important;
          scrollbar-color: transparent transparent !important;
        }
      </style>
    </div>
    <div style="text-align:center;margin-top:3rem;padding:2rem 0;border-top:1px solid rgba(255,255,255,.08);clear:both;">
      <div style="margin-bottom:.5rem;">
        <img src="{{ asset('assets/images/footer.jpg') }}" alt="FJB" style="max-height:45px;width:auto;object-fit:contain;display:block;margin:0 auto;">
      </div>
      <div style="font-size:.85rem;color:var(--muted,#64748b);font-weight:500;">Develop by IT team</div>
    </div>
  </div>
</div>

{{-- Logout Modal --}}
<div id="logoutModal" class="logout-modal-overlay" aria-hidden="true">
  <div class="logout-modal" style="padding:20px 24px">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px">
      <div class="logout-modal-icon"><i class="fas fa-right-from-bracket"></i></div>
      <button type="button" class="logout-modal-close" onclick="closeLogoutModal()"><i class="fas fa-times"></i></button>
    </div>
    <div style="margin-top:16px">
      <h3 class="logout-modal-title">Logout From This Account?</h3>
      <p class="logout-modal-copy" style="margin-top:8px">You are about to end your current session and return to the sign in page.</p>
    </div>
    <div class="logout-modal-actions" style="margin-top:24px">
      <button type="button" class="logout-modal-btn logout-modal-btn-cancel" onclick="closeLogoutModal()">Stay Signed In</button>
      <button type="button" class="logout-modal-btn logout-modal-btn-confirm" onclick="submitLogout()">Yes, Logout</button>
    </div>
  </div>
</div>

{{-- Modern Confirm Modal --}}
<div id="modernConfirmModal" class="modern-confirm-overlay" aria-hidden="true">
  <div class="modern-confirm-card" role="dialog" aria-modal="true" aria-labelledby="modernConfirmTitle">
    <div class="modern-confirm-header">
      <div class="modern-confirm-icon"><i class="fas fa-bolt"></i></div>
      <div style="flex:1">
        <div id="modernConfirmTitle" class="modern-confirm-title">Confirm Action</div>
        <div class="modern-confirm-subtitle">Remark is optional</div>
      </div>
      <button type="button" class="modern-confirm-close" onclick="closeModernConfirm()"><i class="fas fa-times"></i></button>
    </div>
    <div class="modern-confirm-body">
      <div id="modernConfirmMessage" class="modern-confirm-message"></div>
      <div id="modernConfirmRemarkGroup">
        <label for="modernConfirmRemark" class="modern-confirm-label">Remark (optional)</label>
        <textarea id="modernConfirmRemark" class="modern-confirm-textarea" placeholder="Add a short note if needed..." data-preserve-case="true"></textarea>
      </div>
    </div>
    <div class="modern-confirm-footer">
      <button type="button" class="modern-confirm-btn cancel" onclick="closeModernConfirm()">Cancel</button>
      <button type="button" class="modern-confirm-btn confirm" onclick="submitModernConfirm()">Confirm</button>
    </div>
  </div>
</div>

{{-- Policies Modal --}}
<div id="policiesModal" style="display:none;position:fixed;inset:0;z-index:9200;overflow-y:auto" role="dialog" aria-modal="true">
  <div style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px">
    <div style="position:fixed;inset:0;background:rgba(15,23,42,.5);backdrop-filter:blur(4px)" onclick="closePoliciesModal()"></div>
    <div style="position:relative;background:var(--surface);border:1px solid var(--border);border-radius:20px;width:100%;max-width:560px;overflow:hidden;box-shadow:0 40px 100px rgba(0,0,0,.25)">
      <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid var(--border)">
        <div>
          <div style="font-size:13px;font-weight:700;color:var(--text);text-transform:uppercase;letter-spacing:.08em">System Policies</div>
          <div style="font-size:10px;color:var(--muted);margin-top:2px;text-transform:uppercase;letter-spacing:.12em">Walkie Talkie Usage Terms &amp; Conditions</div>
        </div>
        <button onclick="closePoliciesModal()" style="width:32px;height:32px;border-radius:8px;border:1px solid var(--border);background:var(--body-bg);color:var(--muted);display:flex;align-items:center;justify-content:center;cursor:pointer">
          <i class="fas fa-times" style="font-size:11px"></i>
        </button>
      </div>
      <div style="max-height:55vh;overflow-y:auto;padding:20px 24px">
        @include('wt.partials.walkie-policy-content')
      </div>
      <div style="padding:14px 24px;border-top:1px solid var(--border);background:var(--body-bg);display:flex;justify-content:flex-end">
        <button onclick="closePoliciesModal()" class="btn-primary-custom">I Understand</button>
      </div>
    </div>
  </div>
</div>

{{-- Global Walkie Timeline Modal --}}
<div id="globalWalkieTimelineModal" class="modal-overlay" onclick="closeGlobalWalkieTimelineOutside(event)" aria-hidden="true">
  <div class="modal-box global-walkie-modal" role="dialog" aria-modal="true" aria-labelledby="globalWalkieTimelineTitle">
    <div class="modal-header">
      <div style="min-width:0">
        <div style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.1em;color:var(--muted)">Walkie Talkie Details</div>
        <h2 id="globalWalkieTimelineTitle" class="modal-title" style="margin-top:4px">-</h2>
        <p id="globalWalkieTimelineSubtitle" style="font-size:11px;color:var(--muted);margin-top:2px">-</p>
      </div>
      <button type="button" class="modal-close-btn" onclick="closeGlobalWalkieTimeline()"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body" style="padding:24px 28px">
      <div id="globalWalkieTimelineSummary" class="global-walkie-summary"></div>
      <div style="margin-top:20px">
        <p style="margin-bottom:12px;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.16em;color:var(--muted)">History</p>
        <div id="globalWalkieTimelineBody" class="global-walkie-history"></div>
      </div>
    </div>
  </div>
</div>

{{-- Scroll controls --}}
<div id="systemScrollControls">
  <button class="system-scroll-btn" data-scroll-target="top" title="Scroll to top"><i class="fas fa-chevron-up" style="font-size:12px"></i></button>
  <button class="system-scroll-btn" data-scroll-target="bottom" title="Scroll to bottom"><i class="fas fa-chevron-down" style="font-size:12px"></i></button>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
// ── RENDER LOCK ──
(function() {
  var unlocked = false;
  function unlockRender() {
    if (unlocked) return;
    unlocked = true;
    requestAnimationFrame(function() {
      document.documentElement.classList.remove('wt-render-lock');
      document.documentElement.classList.add('wt-render-ready');
    });
  }

  if (document.readyState === 'complete') {
    unlockRender();
  } else {
    window.addEventListener('load', unlockRender, { once: true });
    setTimeout(unlockRender, 1400);
  }
})();

// ── LIVE CLOCK ──
(function() {
  var days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
  var months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
  function pad(n){ return n < 10 ? '0'+n : n; }
  function tick() {
    var d = new Date();
    var str = days[d.getDay()] + ', ' + pad(d.getDate()) + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
    var el = document.getElementById('liveClock');
    if (el) el.textContent = str;
  }
  tick();
  setInterval(tick, 60000);
})();

// ── THEME ──
const DARK_VARS = {
  '--body-bg':            '#0f172a',
  '--surface':            '#1e293b',
  '--border':             '#334155',
  '--text':               '#e2e8f0',
  '--muted':              '#94a3b8',
  '--table-hover':        'rgba(255,255,255,.04)',
  '--form-input-bg':      '#0f172a',
  '--form-input-border':  '#334155',
  '--form-input-color':   '#e2e8f0',
  '--table-head-bg':      '#111827',
  '--table-head-color':   '#94a3b8',
};
const LIGHT_VARS = {
  '--body-bg':            '#f0f4f8',
  '--surface':            '#ffffff',
  '--border':             '#e2e8f0',
  '--text':               '#1e293b',
  '--muted':              '#64748b',
  '--table-hover':        '#f0f9ff',
  '--form-input-bg':      '#ffffff',
  '--form-input-border':  '#e2e8f0',
  '--form-input-color':   '#1e293b',
  '--table-head-bg':      '#f8fafc',
  '--table-head-color':   '#475569',
};

function applyTheme(dark) {
  const vars = dark ? DARK_VARS : LIGHT_VARS;
  for (const [k,v] of Object.entries(vars))
    document.documentElement.style.setProperty(k, v);
  // Sync IT-style icon
  const icon = document.getElementById('themeIcon');
  if (icon) icon.className = dark ? 'bi bi-moon-fill' : 'bi bi-sun-fill';
  // Sync FA icons
  const darkIcon = document.getElementById('theme-toggle-dark-icon');
  const lightIcon = document.getElementById('theme-toggle-light-icon');
  if (darkIcon && lightIcon) {
    darkIcon.classList.toggle('hidden', dark);
    lightIcon.classList.toggle('hidden', !dark);
  }
  document.documentElement.setAttribute('data-theme', dark ? 'dark' : 'light');
  document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
  // Sync Tailwind dark class for content views that use Tailwind dark: variants
  document.documentElement.classList.toggle('dark', dark);
  document.body.style.backgroundColor = vars['--body-bg'];
  document.body.style.color = vars['--text'];
  if (typeof window._chartThemeUpdate === 'function') window._chartThemeUpdate();
}

function toggleTheme() {
  const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
  const next = isDark ? 'light' : 'dark';
  localStorage.setItem('fjb-theme', next);
  // Also keep color-theme in sync for WT partials that read it
  localStorage.setItem('color-theme', next);
  localStorage.setItem('theme', next);
  applyTheme(next === 'dark');
}

// Theme toggle button
const themeToggleBtn = document.getElementById('theme-toggle');
if (themeToggleBtn) {
  themeToggleBtn.addEventListener('click', function() { toggleTheme(); });
}

// Initialize theme: prefer fjb-theme, fall back to color-theme
(function(){
  const fjbTheme = localStorage.getItem('fjb-theme');
  const colorTheme = localStorage.getItem('color-theme');
  const legacyTheme = localStorage.getItem('theme');
  const theme = fjbTheme || colorTheme || legacyTheme || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
  if (!fjbTheme) localStorage.setItem('fjb-theme', theme);
  localStorage.setItem('color-theme', theme);
  localStorage.setItem('theme', theme);
  applyTheme(theme === 'dark');
})();

// ── SIDEBAR TOGGLE (mobile) ──
function toggleMobileSidebar() {
  const sb = document.getElementById('sidebar');
  const ov = document.getElementById('mobileSidebarOverlay');
  if (sb.classList.contains('open')) {
    sb.classList.remove('open');
    ov.style.display = 'none';
    document.body.style.overflow = '';
  } else {
    sb.classList.add('open');
    ov.style.display = 'block';
    document.body.style.overflow = 'hidden';
  }
}
function closeMobileSidebar() {
  const sb = document.getElementById('sidebar');
  const ov = document.getElementById('mobileSidebarOverlay');
  if (sb) sb.classList.remove('open');
  if (ov) ov.style.display = 'none';
  document.body.style.overflow = '';
}
// Mobile sidebar overlay also closes it
var sidebarOv = document.getElementById('mobileSidebarOverlay');
if (sidebarOv) sidebarOv.onclick = closeMobileSidebar;

// -- SIDEBAR ALWAYS VISIBLE (collapse disabled) --
localStorage.removeItem('wt-sidebar-collapsed');
document.body.classList.remove('sidebar-collapsed');

// Collapsed-sidebar hover tooltips: show each item's label when the rail is collapsed
(function () {
  var tip = null;
  function ensureTip() {
    if (!tip) { tip = document.createElement('div'); tip.className = 'sb-tooltip'; document.body.appendChild(tip); }
    return tip;
  }
  function isCollapsed() {
    return document.body.classList.contains('sidebar-collapsed') && window.innerWidth > 768;
  }
  function labelOf(el) {
    var clone = el.cloneNode(true);
    clone.querySelectorAll('i, svg, .nav-info-slot, .pending-nav-badge, .badge-count, .dropdown-chevron, .toggle-arrow').forEach(function (n) { n.remove(); });
    return clone.textContent.replace(/\s+/g, ' ').trim();
  }
  function showTip(el) {
    if (!isCollapsed()) return;
    var label = el.getAttribute('data-tooltip');
    if (!label) return;
    var t = ensureTip();
    t.textContent = label;
    var r = el.getBoundingClientRect();
    t.style.top = (r.top + r.height / 2) + 'px';
    t.style.left = (r.right + 12) + 'px';
    t.classList.add('show');
  }
  function hideTip() { if (tip) tip.classList.remove('show'); }

  document.querySelectorAll('.sidebar-nav > .nav-link, .sidebar-nav .dropdown-trigger, .sidebar-footer .btn-logout').forEach(function (el) {
    var label = labelOf(el);
    if (!label) return;
    el.setAttribute('data-tooltip', label);
    el.addEventListener('mouseenter', function () { showTip(el); });
    el.addEventListener('mouseleave', hideTip);
    el.addEventListener('click', hideTip);
  });
})();

// ── DROPDOWN TOGGLE ──
function toggleDropdown(trigger) {
  const wrapper = trigger.closest('.dropdown-wrapper');
  const isOpen = wrapper.classList.contains('open');
  // Close all other dropdowns
  document.querySelectorAll('.dropdown-wrapper.open').forEach(function(dw) {
    if (dw !== wrapper) {
      dw.classList.remove('open');
      const otherTrigger = dw.querySelector('.dropdown-trigger');
      if (otherTrigger) otherTrigger.setAttribute('aria-expanded', 'false');
    }
  });
  if (!isOpen) {
    wrapper.classList.add('open');
    trigger.setAttribute('aria-expanded', 'true');
  } else {
    wrapper.classList.remove('open');
    trigger.setAttribute('aria-expanded', 'false');
  }
}

// ── LOGOUT ──
function handleLogout() { openLogoutModal(); }
function openLogoutModal() {
  var m = document.getElementById('logoutModal');
  if (!m) return;
  m.classList.add('active');
  m.setAttribute('aria-hidden','false');
  document.body.style.overflow = 'hidden';
}
function closeLogoutModal() {
  var m = document.getElementById('logoutModal');
  if (!m) return;
  m.classList.remove('active');
  m.setAttribute('aria-hidden','true');
  document.body.style.overflow = '';
}
function submitLogout() {
  document.getElementById('logout-form').submit();
}

// ── MODERN CONFIRM ──
let modernConfirmForm = null;
function openModernConfirm(form) {
  modernConfirmForm = form;
  var modal = document.getElementById('modernConfirmModal');
  var message = document.getElementById('modernConfirmMessage');
  var title = document.getElementById('modernConfirmTitle');
  var remark = document.getElementById('modernConfirmRemark');
  var remarkGroup = document.getElementById('modernConfirmRemarkGroup');
  var confirmBtn = modal ? modal.querySelector('.modern-confirm-btn.confirm') : null;
  if (!modal || !message || !title || !remark) return false;
  var showRemark = form.dataset.modernConfirmRemark !== 'false';
  title.textContent = form.dataset.modernConfirmTitle || 'Confirm Action';
  message.textContent = form.dataset.modernConfirm || 'Confirm this action?';
  remark.value = '';
  if (remarkGroup) remarkGroup.classList.toggle('hidden', !showRemark);
  modal.classList.add('active');
  modal.setAttribute('aria-hidden','false');
  document.body.style.overflow = 'hidden';
  setTimeout(function(){ (showRemark ? remark : confirmBtn) && (showRemark ? remark : confirmBtn).focus(); }, 80);
  return false;
}
function closeModernConfirm() {
  var modal = document.getElementById('modernConfirmModal');
  if (!modal) return;
  modal.classList.remove('active');
  modal.setAttribute('aria-hidden','true');
  modernConfirmForm = null;
  document.body.style.overflow = '';
}
function submitModernConfirm() {
  if (!modernConfirmForm) return;
  var remark = document.getElementById('modernConfirmRemark');
  var existing = modernConfirmForm.querySelector('input[name="action_remark"]');
  if (existing) existing.remove();
  if (remark && remark.value.trim() !== '') {
    var input = document.createElement('input');
    input.type = 'hidden'; input.name = 'action_remark'; input.value = remark.value.trim();
    modernConfirmForm.appendChild(input);
  }
  modernConfirmForm.dataset.modernConfirmed = 'true';
  document.body.style.overflow = '';
  modernConfirmForm.submit();
}
document.addEventListener('submit', function(event) {
  var form = event.target.closest('form[data-modern-confirm]');
  if (!form || form.dataset.modernConfirmed === 'true') return;
  event.preventDefault();
  openModernConfirm(form);
}, true);
document.addEventListener('click', function(event) {
  var m = document.getElementById('modernConfirmModal');
  if (event.target === m) closeModernConfirm();
});

// ── POLICIES MODAL ──
function openPoliciesModal() {
  var m = document.getElementById('policiesModal');
  if (m) { m.style.display = 'block'; document.body.style.overflow = 'hidden'; }
}
function closePoliciesModal() {
  var m = document.getElementById('policiesModal');
  if (m) { m.style.display = 'none'; document.body.style.overflow = ''; }
}

// ── AUTO UPPERCASE ──
function bindAutoUppercase(root) {
  root = root || document;
  var fields = root.querySelectorAll('input[type="text"], input[type="search"], textarea');
  fields.forEach(function(field) {
    if (field.name === 'username' || field.id === 'edit_username' || field.dataset.preserveCase === 'true') return;
    if (field.dataset.uppercaseBound === 'true') return;
    field.dataset.uppercaseBound = 'true';
    field.addEventListener('input', function() {
      var start = this.selectionStart, end = this.selectionEnd;
      this.value = this.value.toUpperCase();
      if (typeof start === 'number' && typeof end === 'number') this.setSelectionRange(start, end);
    });
  });
}

// ── SIDEBAR INFO POPOVERS ──
function closeSidebarInfoPopovers() {
  document.querySelectorAll('.nav-info-popover').forEach(function(item) {
    item.classList.add('hidden');
    item.style.left = ''; item.style.top = ''; item.style.visibility = '';
  });
  document.querySelectorAll('.nav-info-btn').forEach(function(btn) {
    btn.classList.remove('is-open');
    btn.setAttribute('aria-expanded','false');
  });
}
function positionSidebarInfoPopover(button, popover) {
  var spacing = 12, vp = 16;
  var rect = button.getBoundingClientRect();
  popover.classList.remove('hidden');
  popover.style.visibility = 'hidden';
  var maxW = Math.min(280, window.innerWidth - (vp * 2));
  popover.style.width = Math.max(220, maxW) + 'px';
  var left = rect.right + spacing;
  var pw = popover.offsetWidth;
  if (left + pw > window.innerWidth - vp) left = rect.left - pw - spacing;
  left = Math.max(vp, Math.min(left, window.innerWidth - vp - pw));
  var top = rect.top + (rect.height / 2) - (popover.offsetHeight / 2);
  top = Math.max(vp, Math.min(top, window.innerHeight - vp - popover.offsetHeight));
  popover.style.left = left + 'px';
  popover.style.top = top + 'px';
  popover.style.visibility = 'visible';
}

// ── ADMIN TABLE UTILITIES ──
var canUseAdminExports = false;
function syncAdminTableFooter(tableApi, footerParts) {
  if (!tableApi || !footerParts) return;
  var info = tableApi.page.info();
  var hasPages = info.pages > 0;
  var start = info.recordsDisplay === 0 ? 0 : info.start + 1;
  footerParts.info.textContent = 'Showing ' + start + ' to ' + info.end + ' of ' + info.recordsDisplay + ' entries';
  footerParts.current.textContent = hasPages ? info.page + 1 : 1;
  footerParts.current.classList.toggle('hidden', !hasPages);
  footerParts.prev.disabled = !hasPages || info.page === 0;
  footerParts.next.disabled = !hasPages || info.page >= info.pages - 1;
}
function mountAdminTableFooter(tableApi) {
  if (!tableApi || typeof tableApi.table !== 'function') return;
  var wrapper = tableApi.table().container();
  if (!wrapper) return;
  wrapper.classList.add('adminit-footer-mounted');
  var mountedFooters = Array.from(wrapper.querySelectorAll('.adminit-table-footer'));
  if (wrapper.dataset.adminitFooterMounted === 'true') {
    var activeFooter = wrapper._adminitFooterParts && wrapper._adminitFooterParts.footer ? wrapper._adminitFooterParts.footer : mountedFooters[0];
    mountedFooters.forEach(function(footer, index) { if (footer !== activeFooter || index > 0) footer.remove(); });
    if (activeFooter && activeFooter.isConnected) { syncAdminTableFooter(tableApi, wrapper._adminitFooterParts); return; }
    wrapper.dataset.adminitFooterMounted = 'false';
  }
  wrapper.dataset.adminitFooterMounted = 'true';
  wrapper.querySelectorAll('.adminit-table-footer').forEach(function(f) { f.remove(); });
  wrapper.querySelectorAll('.dataTables_info, .dataTables_paginate').forEach(function(c) { c.remove(); });
  var footer = document.createElement('div');
  footer.className = 'adminit-table-footer';
  footer.innerHTML = '<div class="adminit-table-info">Showing 0 to 0 of 0 entries</div><div class="adminit-table-pagination"><button type="button" class="adminit-page-link adminit-prev-page">Previous</button><span class="adminit-page-current hidden">1</span><button type="button" class="adminit-page-link adminit-next-page">Next</button></div>';
  wrapper.appendChild(footer);
  var footerParts = {
    footer: footer,
    info: footer.querySelector('.adminit-table-info'),
    prev: footer.querySelector('.adminit-prev-page'),
    current: footer.querySelector('.adminit-page-current'),
    next: footer.querySelector('.adminit-next-page'),
  };
  wrapper._adminitFooterParts = footerParts;
  footerParts.prev.addEventListener('click', function() { if (!this.disabled) tableApi.page('previous').draw('page'); });
  footerParts.next.addEventListener('click', function() { if (!this.disabled) tableApi.page('next').draw('page'); });
  tableApi.on('draw.dt', function() {
    wrapper.querySelectorAll('.dataTables_info, .dataTables_paginate').forEach(function(c) { c.remove(); });
    wrapper.querySelectorAll('.adminit-table-footer ~ .adminit-table-footer').forEach(function(f) { f.remove(); });
    syncAdminTableFooter(tableApi, footerParts);
  });
  syncAdminTableFooter(tableApi, footerParts);
}
function getAdminTableExportButtons(title, exportColumns) { return []; }
function mountAdminTableExports(tableApi, hostSelector) {}
function mountAdminTableExportDropdown(tableApi, hostSelector, label) {
  var host = typeof hostSelector === 'string' ? document.querySelector(hostSelector) : hostSelector;
  if (!host) return;
  host.innerHTML = '';
}

// ── GLOBAL WALKIE TIMELINE ──
function globalTimelineEscape(value) {
  return String(value ?? '').replace(/[&<>"']/g, function(c) {
    return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c];
  });
}
function globalTimelineHasValue(value) {
  var normalized = String(value ?? '').trim();
  return normalized !== '' && normalized !== '-' && normalized.toUpperCase() !== 'N/A';
}
function globalTimelineValueHtml(value, fallback) {
  var display = globalTimelineHasValue(value) ? value : (fallback || 'N/A');
  var muted = globalTimelineHasValue(value) ? '' : ' global-walkie-muted';
  return '<div class="global-walkie-summary-value'+muted+'">'+globalTimelineEscape(display)+'</div>';
}
function globalTimelineWarrantyRange(start, end) {
  var hasStart = globalTimelineHasValue(start);
  var hasEnd = globalTimelineHasValue(end);
  if (!hasStart && !hasEnd) return 'No Warranty Data';
  return (hasStart ? start : 'N/A') + ' - ' + (hasEnd ? end : 'N/A');
}
function globalTimelineWarrantyItem(label, start, end) {
  var value = globalTimelineWarrantyRange(start, end);
  var muted = value === 'No Warranty Data' ? ' global-walkie-muted' : '';
  return '<div class="global-walkie-warranty-item"><div class="global-walkie-warranty-label">'+globalTimelineEscape(label)+'</div><div class="global-walkie-warranty-value'+muted+'">'+globalTimelineEscape(value)+'</div></div>';
}
async function openGlobalWalkieTimeline(walkieId) {
  var url = @json(route('wt.admin.walkies.timeline', ['walkie' => '__WALKIE__'])).replace('__WALKIE__', encodeURIComponent(walkieId));
  await openGlobalWalkieTimelineUrl(url);
}
async function openGlobalMaintenanceTimeline(maintenanceId) {
  var url = @json(route('wt.admin.maintenance.timeline', ['maintenance' => '__MAINTENANCE__'])).replace('__MAINTENANCE__', encodeURIComponent(maintenanceId));
  await openGlobalWalkieTimelineUrl(url);
}
async function openGlobalWalkieTimelineUrl(url) {
  var modal = document.getElementById('globalWalkieTimelineModal');
  var title = document.getElementById('globalWalkieTimelineTitle');
  var subtitle = document.getElementById('globalWalkieTimelineSubtitle');
  var summaryHost = document.getElementById('globalWalkieTimelineSummary');
  var bodyHost = document.getElementById('globalWalkieTimelineBody');
  if (!modal || !title || !subtitle || !summaryHost || !bodyHost) return;
  title.textContent = 'Loading...'; subtitle.textContent = '-';
  summaryHost.innerHTML = ''; bodyHost.innerHTML = '<div class="global-walkie-history-row"><div class="global-walkie-history-detail">Loading history...</div></div>';
  modal.classList.add('active'); modal.setAttribute('aria-hidden','false');
  document.body.style.overflow = 'hidden';
  try {
    var response = await fetch(url, { headers: {'Accept':'application/json'} });
    if (!response.ok) throw new Error('Unable to load walkie details.');
    var timeline = await response.json();
    var summary = timeline.summary || {};
    var events = Array.isArray(timeline.events) ? timeline.events : [];
    title.textContent = summary.radio_id || '-';
    subtitle.textContent = (summary.model || '-') + ' / ' + (summary.serial_number || '-') + ' / ' + (summary.status || 'UNKNOWN');
    var summaryItems = [
      ['Walkie ID', summary.walkie_id||'-'],['Radio ID', summary.radio_id||'-'],['Serial No.', summary.serial_number||'-'],
      ['Model', summary.model||'-'],['Status', summary.status||'-'],['Ownership Type', summary.ownership_type||'-'],
      ['Current Ownership', summary.ownership||'-'],['Shared With', summary.shared_with||'-'],['Position', summary.position||'-'],
      ['Department', summary.department||'-'],['Executive', summary.executive||'-'],['Received Date', summary.received_date||'-'],['Repair Date', summary.repair_date||'-'],
      ['Temporary Radio ID', summary.temporary_radio_id||'-'],['Tracking Ref', summary.tracking_ref||'-'],
      ['Need Change ID', summary.need_to_change_id||'-'],['Change Done', summary.id_change_done||'-'],
      ['Ownership Type To Be', summary.ownership_type_to_be||'-'],['Special Use', summary.is_special_use||'-'],
      ['Returned', summary.special_use_returned||'-'],['Remarks', summary.remark||'-'],
    ];
    summaryHost.innerHTML = summaryItems.map(function(item) {
      return '<div class="global-walkie-summary-item"><div class="global-walkie-summary-label">'+globalTimelineEscape(item[0])+'</div>'+globalTimelineValueHtml(item[1], '-')+'</div>';
    }).join('')
    + '<div class="global-walkie-warranty-section">'
    + '<p class="global-walkie-section-title">Warranty Information</p>'
    + '<div class="global-walkie-warranty-grid">'
    + globalTimelineWarrantyItem('WT Warranty', summary.wt_warranty_start_date, summary.wt_warranty_end_date)
    + globalTimelineWarrantyItem('Battery Warranty', summary.battery_warranty_start_date, summary.battery_warranty_end_date)
    + '</div></div>';
    bodyHost.innerHTML = events.length
      ? events.map(function(event) {
          return '<div class="global-walkie-history-row"><div class="global-walkie-history-date">'+globalTimelineEscape(event.date||'-')+'<span class="global-walkie-history-time">'+globalTimelineEscape(event.time||'')+'</span></div><div><p class="global-walkie-history-title">'+globalTimelineEscape(event.title||'Activity')+'</p><p class="global-walkie-history-detail">'+globalTimelineEscape(event.detail||'-')+'</p></div></div>';
        }).join('')
      : '<div class="global-walkie-history-row"><div class="global-walkie-history-detail">No history records found for this unit yet.</div></div>';
  } catch(error) {
    title.textContent = 'Unable to load details';
    bodyHost.innerHTML = '<div class="global-walkie-history-row"><div class="global-walkie-history-detail">'+globalTimelineEscape(error.message||'Unable to load walkie details.')+'</div></div>';
  }
}
function closeGlobalWalkieTimeline() {
  var modal = document.getElementById('globalWalkieTimelineModal');
  if (!modal) return;
  modal.classList.remove('active'); modal.setAttribute('aria-hidden','true');
  document.body.style.overflow = '';
}
function closeGlobalWalkieTimelineOutside(event) {
  if (event.target === document.getElementById('globalWalkieTimelineModal')) closeGlobalWalkieTimeline();
}

// ── DOM READY ──
document.addEventListener('DOMContentLoaded', function() {
  // Notification toggle
  var notifToggle = document.getElementById('notificationToggle');
  var notifDropdown = document.getElementById('notificationDropdown');
  if (notifToggle && notifDropdown) {
    notifToggle.addEventListener('click', function(e) {
      e.stopPropagation();
      notifDropdown.classList.toggle('hidden');
    });
    document.addEventListener('click', function(e) {
      if (!notifDropdown.contains(e.target) && !notifToggle.contains(e.target)) {
        notifDropdown.classList.add('hidden');
      }
    });
  }

  // Logout modal click outside
  var logoutModal = document.getElementById('logoutModal');
  if (logoutModal) {
    logoutModal.addEventListener('click', function(e) { if (e.target === logoutModal) closeLogoutModal(); });
  }

  // Auto uppercase
  bindAutoUppercase();
  document.querySelectorAll('form').forEach(function(form) {
    form.addEventListener('submit', function() {
      bindAutoUppercase(form);
      form.querySelectorAll('input[type="text"], input[type="search"], textarea').forEach(function(field) {
        if (field.name === 'username' || field.id === 'edit_username' || field.dataset.preserveCase === 'true') return;
        field.value = field.value.toUpperCase();
      });
    });
  });

  // Sidebar info popovers
  document.addEventListener('click', function(event) {
    var infoBtn = event.target.closest('.nav-info-btn');
    if (infoBtn) {
      event.preventDefault(); event.stopPropagation();
      var popover = infoBtn.parentElement && infoBtn.parentElement.querySelector('.nav-info-popover');
      var willOpen = popover && popover.classList.contains('hidden');
      closeSidebarInfoPopovers();
      if (popover && willOpen) {
        positionSidebarInfoPopover(infoBtn, popover);
        infoBtn.classList.add('is-open');
        infoBtn.setAttribute('aria-expanded','true');
      }
      return;
    }
    if (!event.target.closest('.nav-info-btn') && !event.target.closest('.nav-info-popover')) {
      closeSidebarInfoPopovers();
    }
  }, true);

  // Scroll controls — targets .main-content (flex layout: body overflow:hidden, page scrolls inside .main-content)
  var mainContent = document.querySelector('.main-content');
  var scrollControls = document.getElementById('systemScrollControls');
  var scrollTopBtn = scrollControls && scrollControls.querySelector('[data-scroll-target="top"]');
  var scrollBottomBtn = scrollControls && scrollControls.querySelector('[data-scroll-target="bottom"]');
  function updateScrollControls() {
    if (!scrollControls) return;
    var scrollEl = mainContent || document.documentElement;
    var maxScroll = Math.max(0, scrollEl.scrollHeight - scrollEl.clientHeight);
    var hasScroll = maxScroll > 24;
    scrollControls.classList.toggle('is-visible', hasScroll);
    var currentScroll = scrollEl.scrollTop;
    if (scrollTopBtn) scrollTopBtn.disabled = !hasScroll || currentScroll <= 8;
    if (scrollBottomBtn) scrollBottomBtn.disabled = !hasScroll || currentScroll >= maxScroll - 8;
  }
  if (scrollTopBtn) scrollTopBtn.addEventListener('click', function() { (mainContent || window).scrollTo({top:0,behavior:'smooth'}); });
  if (scrollBottomBtn) scrollBottomBtn.addEventListener('click', function() {
    var el = mainContent || document.documentElement;
    el.scrollTo({top:el.scrollHeight,behavior:'smooth'});
  });
  if (mainContent) mainContent.addEventListener('scroll', updateScrollControls, {passive:true});
  window.addEventListener('scroll', updateScrollControls, {passive:true});
  window.addEventListener('resize', function() { updateScrollControls(); closeSidebarInfoPopovers(); });
  document.addEventListener('scroll', closeSidebarInfoPopovers, true);
  setTimeout(updateScrollControls, 80);

  // Escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeSidebarInfoPopovers();
      closeLogoutModal();
      closeModernConfirm();
      closePoliciesModal();
      if (typeof closeGlobalWalkieTimeline === 'function') closeGlobalWalkieTimeline();
    }
  });

  // Flash alert auto-dismiss
  setTimeout(function() {
    document.querySelectorAll('.alert-success-custom, .alert-danger-custom').forEach(function(el) {
      el.style.transition = 'opacity .5s'; el.style.opacity = '0';
      setTimeout(function() { if(el.parentNode) el.parentNode.removeChild(el); }, 500);
    });
  }, 4000);
});
  function toggleWTSidebar() {
    document.body.classList.toggle('sidebar-collapsed');
    let isCollapsed = document.body.classList.contains('sidebar-collapsed');
    localStorage.setItem('wt-sb-collapsed', isCollapsed ? '1' : '0');
  }
</script>

@include('wt.partials.assistant-chatbox', ['assistantRole' => $effectiveRole])
@include('wt.partials.form-option-datalists')
@include('wt.partials.phone-format-script')
@include('wt.partials.popup-redirect')

@stack('scripts')
</body>
</html>
