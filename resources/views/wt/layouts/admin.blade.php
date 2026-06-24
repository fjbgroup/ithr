<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Dashboard') — WT System</title>
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
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={darkMode:'class',theme:{extend:{colors:{corp:{navy:'#1F2937',brown:'#075985',gold:'#38bdf8'}}}}}</script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="{{ asset('assets/css/wtsystem.css') }}" rel="stylesheet">
<style>
/* wtsystem.css is the single source of truth */
</style>
@stack('styles')
@stack('final_styles')
</head>
<body id="main-body">

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
    $inventoryNavOnInventory = request()->routeIs('wt.admin.walkies.index') || request()->routeIs('wt.admin.walkies.create');
    $inventoryNavOnMaintenance = request()->routeIs('wt.admin.maintenance.index') || request()->routeIs('wt.admin.maintenance.create');
    $inventoryNavOnDuplicate = request()->routeIs('wt.admin.walkies.duplicateIds') || request()->routeIs('wt.admin.walkies.create.duplicate');
    $inventoryNavOnSpecialUse = request()->routeIs('wt.admin.walkies.specialUse') || request()->routeIs('wt.admin.walkies.create.specialUse');
    $inventoryManagementOpen = $inventoryNavOnInventory || $inventoryNavOnMaintenance || $inventoryNavOnDuplicate || $inventoryNavOnSpecialUse;
    $approvalNavOnPending = request()->routeIs('wt.admin.requests.index');
    $approvalNavOnHistory = request()->routeIs('wt.admin.requests.history');
    $approvalManagementOpen = $approvalNavOnPending || $approvalNavOnHistory;
    $faultyNavOnUserReports = request()->routeIs('wt.admin.faultyReports.*');
    $faultyNavOnThreeMonths = request()->routeIs('wt.admin.reports.faulty3Months');
    $faultyManagementOpen = $faultyNavOnUserReports || $faultyNavOnThreeMonths;
@endphp

{{-- Mobile overlay --}}
<div id="mobileSidebarOverlay" onclick="closeMobileSidebar()" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:99;backdrop-filter:blur(2px)"></div>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
  <a href="{{ request()->fullUrl() }}" class="sidebar-brand" title="Refresh page">
    <div style="width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;overflow:hidden;background:#fff;border:1px solid rgba(255,255,255,.18)">
      <img src="{{ asset('assets/images/fjb-logo.svg') }}" alt="FJB" class="sidebar-brand-logo" onerror="this.onerror=null;this.src='{{ asset('assets/img/logo_transparent.png') }}'">
    </div>
    <div class="brand-name">WT System<span>Walkie Talkie Management</span></div>
  </a>

  <nav class="sidebar-nav">
    {{-- Dashboard (ICT only) --}}
    @if($isAdminItView)
    <div class="nav-section-label" style="margin-top:4px">Main</div>
    <a href="{{ route('wt.admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active-sidebar' : '' }}">
      <i class="fas fa-home"></i> <span>Dashboard</span>
    </a>
    @endif

    <div class="nav-section-label">{{ $isAdminItView ? 'Management' : 'Personal Assets' }}</div>

    @if($isAdminItView)
    {{-- Inventory Tools --}}
    <div class="dropdown-wrapper">
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
    <div class="dropdown-wrapper">
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
    <div class="dropdown-wrapper">
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
    <a href="{{ route('wt.admin.walkies.myInventory') }}" class="nav-link has-info {{ request()->routeIs('wt.admin.walkies.myInventory') ? 'active-sidebar' : '' }}">
      <i class="fa-solid fa-box" style="width:20px;text-align:center;flex-shrink:0"></i> <span>My Inventory</span>
      @include('wt.partials.sidebar-info', ['text' => 'View walkie talkies currently assigned to you.'])
    </a>

    <div class="nav-section-label">Asset Interactive</div>
    <a href="{{ route('wt.admin.returns.create', ['mode' => 'self']) }}" class="nav-link has-info {{ request()->routeIs('admin.returns.*') ? 'active-sidebar' : '' }}">
      <i class="fa-solid fa-rotate-left" style="width:20px;text-align:center;flex-shrink:0"></i> <span>Return Unit</span>
      @include('wt.partials.sidebar-info', ['text' => 'Submit walkie return records for your own assigned unit.'])
    </a>
    <a href="{{ route('wt.admin.damages.form', ['mode' => 'staff']) }}" class="nav-link has-info {{ request()->routeIs('admin.damages.*') ? 'active-sidebar' : '' }}">
      <i class="fa-solid fa-triangle-exclamation" style="width:20px;text-align:center;flex-shrink:0"></i> <span>Report Faulty</span>
      @include('wt.partials.sidebar-info', ['text' => 'Report damaged, faulty, missing, or problem units for yourself or on behalf of a recipient.'])
    </a>

    {{-- Request Walkie Talkie --}}
    @php
      $reqOpen = request()->routeIs('admin.requests.create') || request()->routeIs('admin.requests.create.*') || request()->routeIs('admin.requests.create.temporary*');
    @endphp
    <div class="dropdown-wrapper">
      <button type="button" class="dropdown-trigger has-info {{ $reqOpen ? 'active-sidebar' : '' }}" onclick="toggleDropdown(this)">
        <i class="fas fa-plus-circle" style="width:20px;text-align:center;flex-shrink:0;font-size:15px"></i>
        <span style="flex:1">Request Walkie Talkie</span>
        @include('wt.partials.sidebar-info', ['text' => 'Submit a walkie talkie request for yourself or on behalf of a recipient. ICT will assign the available unit later.'])
        <i class="fas fa-chevron-right dropdown-chevron"></i>
      </button>
      <div class="dropdown-content">
        <a href="{{ route('wt.admin.requests.create') }}" class="sub-nav-link {{ (request()->routeIs('admin.requests.create') || request()->routeIs('admin.requests.create.individual') || request()->routeIs('admin.requests.create.shared')) ? 'active' : '' }}">
          <i class="fas fa-file-alt" style="font-size:12px;width:14px"></i> Long Term Request
        </a>
        <a href="{{ route('wt.admin.requests.create.temporary') }}" class="sub-nav-link {{ request()->routeIs('admin.requests.create.temporary*') ? 'active' : '' }}">
          <i class="fas fa-clock" style="font-size:12px;width:14px"></i> Temporary Request
        </a>
      </div>
    </div>

    <a href="{{ route('wt.admin.all.status') }}" class="nav-link has-info {{ request()->routeIs('admin.all.status') ? 'active-sidebar' : '' }}">
      <i class="fa-solid fa-list-check" style="width:20px;text-align:center;flex-shrink:0"></i> <span>All Status</span>
      @include('wt.partials.sidebar-info', ['text' => 'Consolidated view for walkie requests, handovers, and faulty reports status.'])
    </a>
    @endif

    {{-- My Account --}}
    <div class="nav-section-label">My Account</div>
    <a href="{{ route('wt.admin.profile') }}" class="nav-link has-info {{ request()->routeIs('admin.profile') ? 'active-sidebar' : '' }}">
      <i class="fas fa-user-circle" style="width:20px;text-align:center;flex-shrink:0"></i> <span>My Profile</span>
      @include('wt.partials.sidebar-info', ['text' => 'View and update your account profile information.'])
    </a>
    @if(!$isAdminItView)
    <a href="javascript:void(0)" onclick="openPoliciesModal()" class="nav-link has-info">
      <i class="fa-solid fa-file-contract" style="width:20px;text-align:center;flex-shrink:0"></i> <span>Policies</span>
      @include('wt.partials.sidebar-info', ['text' => 'Read the policies and rules for walkie talkie usage.'])
    </a>
    @endif

    {{-- Executive Tools (IT only) --}}
    @if($isAdminItView)
    <div class="nav-section-label">Executive Tools (IT)</div>
    @php
      $sysCtrlOpen = request()->routeIs('admin.users.index') || request()->routeIs('admin.activity.index') || request()->routeIs('admin.masterData.index');
    @endphp
    <div class="dropdown-wrapper">
      <button type="button" class="dropdown-trigger has-info {{ $sysCtrlOpen ? 'active-sidebar' : '' }}" onclick="toggleDropdown(this)">
        <i class="fas fa-sliders-h" style="width:20px;text-align:center;flex-shrink:0;font-size:15px"></i>
        <span style="flex:1">System Control</span>
        @include('wt.partials.sidebar-info', ['text' => 'Open system management tools for user accounts and activity logs.'])
        <i class="fas fa-chevron-right dropdown-chevron"></i>
      </button>
      <div class="dropdown-content">
        <a href="{{ route('wt.admin.users.index') }}" class="sub-nav-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
          <i class="fas fa-users" style="font-size:12px;width:14px"></i> Users Control
        </a>
        <a href="{{ route('wt.admin.masterData.index') }}" class="sub-nav-link {{ request()->routeIs('admin.masterData.index') ? 'active' : '' }}">
          <i class="fas fa-database" style="font-size:12px;width:14px"></i> Master Data
        </a>
        <a href="{{ route('wt.admin.activity.index') }}" class="sub-nav-link {{ request()->routeIs('admin.activity.index') ? 'active' : '' }}">
          <i class="fas fa-clipboard-list" style="font-size:12px;width:14px"></i> System Logs
        </a>
      </div>
    </div>
    @endif

    <div style="border-top:1px solid rgba(255,255,255,.08);margin:12px 0 8px"></div>
    <a href="{{ route('it.dashboard') }}" class="nav-link">
      <i class="fas fa-desktop" style="width:20px;text-align:center;flex-shrink:0"></i> <span>IT System</span>
    </a>
    <a href="{{ route('dashboard') }}" class="nav-link">
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
        <a href="{{ $isAdminItView ? route('wt.admin.dashboard') : route('wt.admin.requests.create.shared') }}" style="color:var(--muted);text-decoration:none">Dashboard</a>
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
            style="border:none;background:transparent;color:var(--muted);font-size:10px;font-weight:700;outline:none;padding:4px 8px;cursor:pointer;font-family:'DM Sans',sans-serif;text-transform:uppercase;max-width:180px">
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
    @include('wt.partials.flash-alerts')
    @yield('content')
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
  const isDark = localStorage.getItem('fjb-theme') === 'dark';
  const next = isDark ? 'light' : 'dark';
  localStorage.setItem('fjb-theme', next);
  // Also keep color-theme in sync for WT partials that read it
  localStorage.setItem('color-theme', next);
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
  const theme = fjbTheme || colorTheme || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
  if (!fjbTheme) localStorage.setItem('fjb-theme', theme);
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
      ['Department', summary.department||'-'],['Received Date', summary.received_date||'-'],['Repair Date', summary.repair_date||'-'],
      ['Temporary Radio ID', summary.temporary_radio_id||'-'],['Tracking Ref', summary.tracking_ref||'-'],
      ['Need Change ID', summary.need_to_change_id||'-'],['Change Done', summary.id_change_done||'-'],
      ['Ownership Type To Be', summary.ownership_type_to_be||'-'],['Special Use', summary.is_special_use||'-'],
      ['Returned', summary.special_use_returned||'-'],['Remarks', summary.remark||'-'],
    ];
    summaryHost.innerHTML = summaryItems.map(function(item) {
      return '<div class="global-walkie-summary-item"><div class="global-walkie-summary-label">'+globalTimelineEscape(item[0])+'</div><div class="global-walkie-summary-value">'+globalTimelineEscape(item[1])+'</div></div>';
    }).join('');
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

  // Scroll controls
  var mainContent = document.querySelector('.main-content');
  var scrollControls = document.getElementById('systemScrollControls');
  var scrollTopBtn = scrollControls && scrollControls.querySelector('[data-scroll-target="top"]');
  var scrollBottomBtn = scrollControls && scrollControls.querySelector('[data-scroll-target="bottom"]');
  function updateScrollControls() {
    if (!scrollControls) return;
    var scrollEl = document.documentElement;
    var maxScroll = Math.max(0, document.body.scrollHeight - window.innerHeight);
    var hasScroll = maxScroll > 24;
    scrollControls.classList.toggle('is-visible', hasScroll);
    if (scrollTopBtn) scrollTopBtn.disabled = !hasScroll || window.scrollY <= 8;
    if (scrollBottomBtn) scrollBottomBtn.disabled = !hasScroll || window.scrollY >= maxScroll - 8;
  }
  if (scrollTopBtn) scrollTopBtn.addEventListener('click', function() { window.scrollTo({top:0,behavior:'smooth'}); });
  if (scrollBottomBtn) scrollBottomBtn.addEventListener('click', function() { window.scrollTo({top:document.body.scrollHeight,behavior:'smooth'}); });
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
</script>

@include('wt.partials.assistant-chatbox', ['assistantRole' => $effectiveRole])
@include('wt.partials.form-option-datalists')
@include('wt.partials.phone-format-script')
@include('wt.partials.popup-redirect')

@stack('scripts')
</body>
</html>
