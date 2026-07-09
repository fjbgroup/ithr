<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ config('app.name', 'HR Admin System') }}</title>
@include('partials.favicons')
<script>
(function () {
  try {
    var stored = localStorage.getItem('fjb-theme') || localStorage.getItem('color-theme') || localStorage.getItem('theme');
    var theme = stored || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
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
/* ── HR COLLAPSIBLE SIDEBAR ── */
.menu-toggle { display: none !important; }
@media(max-width:768px){ .menu-toggle { display: flex !important; } }
.sidebar { transition: width .3s ease !important; overflow: hidden !important; display: flex !important; flex-direction: column !important; }
.sidebar-nav { flex: 1 !important; overflow-y: auto !important; }
.main-wrapper { transition: margin-left .3s ease; }
html.sidebar-collapsed .sidebar { width: 64px !important; transform: none !important; }
html.sidebar-collapsed .main-wrapper { margin-left: 64px !important; }

/* Sidebar close button — expanded state only */
.sb-close-btn {
  flex-shrink: 0;
  width: 28px; height: 28px;
  background: var(--sidebar-hover-bg); border: none; border-radius: 6px;
  color: var(--sidebar-text); cursor: pointer; font-size: 15px;
  display: flex; align-items: center; justify-content: center;
  transition: background .15s, color .15s;
}
.sb-close-btn:hover { background: var(--sidebar-active-bg); color: var(--sidebar-text-hover); }
html.sidebar-collapsed .sb-close-btn { display: none !important; }

/* Logo becomes open button in collapsed state */
.sb-open-icon { display: none; }
html.sidebar-collapsed .sb-logo-btn { cursor: pointer !important; position: relative !important; }
html.sidebar-collapsed .sb-logo-btn img { transition: opacity .15s; }
html.sidebar-collapsed .sb-logo-btn .sb-open-icon {
  position: absolute; inset: 0;
  display: flex; align-items: center; justify-content: center;
  opacity: 0; transition: opacity .15s; color: #1e293b; pointer-events: none;
}
html.sidebar-collapsed .sb-logo-btn .sb-open-icon svg { display: block; }
html.sidebar-collapsed .sb-logo-btn:hover img { opacity: 0; }
html.sidebar-collapsed .sb-logo-btn:hover .sb-open-icon { opacity: 1; }

/* ── ICON RAIL ── */
html.sidebar-collapsed .sidebar-nav { padding: .75rem 0 !important; }

/* Brand row: show only logo, centered */
html.sidebar-collapsed .sidebar > div:first-child {
  display: flex !important; padding: .75rem 0 !important;
  justify-content: center !important; gap: 0 !important;
  border-bottom: 1px solid var(--sidebar-border);
}
html.sidebar-collapsed .sidebar > div:first-child > a { display: none !important; }

/* Nav direct links — icon only */
html.sidebar-collapsed .nav-item {
  font-size: 0 !important; padding: .6rem 0 !important;
  justify-content: center !important; gap: 0 !important;
}
html.sidebar-collapsed .nav-item > svg { display: block !important; flex-shrink: 0; }
html.sidebar-collapsed .nav-item .badge-count { display: none !important; }

/* Nav groups — icon only, sub-items hidden */
html.sidebar-collapsed .nav-group-toggle {
  font-size: 0 !important; padding: .6rem 0 !important;
  justify-content: center !important; gap: 0 !important;
}
html.sidebar-collapsed .nav-group-toggle > svg:first-child { display: block !important; flex-shrink: 0; }
html.sidebar-collapsed .nav-group-toggle > span { display: none !important; }
html.sidebar-collapsed .nav-group-toggle .toggle-arrow { display: none !important; }
html.sidebar-collapsed .nav-group-children { display: none !important; }
html.sidebar-collapsed .nav-group-children.open {
  display: block !important;
  position: fixed !important;
  left: 64px !important;
  top: auto !important;
  min-width: 190px !important;
  padding: 8px !important;
  margin: -40px 0 0 !important;
  border: 1px solid var(--sidebar-border) !important;
  border-radius: 10px !important;
  background: var(--surface) !important;
  box-shadow: 0 18px 36px rgba(0,0,0,.15) !important;
  z-index: 1300 !important;
}
html.sidebar-collapsed .nav-group-children.open .nav-child {
  font-size: 13px !important;
  padding: 9px 12px !important;
  border-radius: 8px !important;
  margin-bottom: 2px !important;
  background: transparent !important;
  display: block !important;
}
html.sidebar-collapsed .nav-group-children.open .nav-child:hover {
  background: var(--sidebar-hover-bg) !important;
}
html.sidebar-collapsed .nav-group-toggle.open,
html.sidebar-collapsed .nav-group-toggle.has-active {
  background: rgba(2,132,199,.25) !important;
}
html.sidebar-collapsed .nav-group-toggle.open svg:first-child,
html.sidebar-collapsed .nav-group-toggle.has-active svg:first-child {
  color: #38bdf8 !important;
}

/* Hide dividers and any section labels */
html.sidebar-collapsed .nav-divider { display: none !important; }
html.sidebar-collapsed .nav-label,
html.sidebar-collapsed .sidebar-section-label { display: none !important; }

/* Mobile: full-width overlay */
@media(max-width:768px){
  .sidebar { width: var(--sidebar-w, 240px) !important; overflow-y: auto !important;
    transform: translateX(-100%) !important; }
  .sidebar.open { transform: translateX(0) !important; }
  .main-wrapper { margin-left: 0 !important; }
}

/* ── COLLAPSED SIDEBAR HOVER TOOLTIPS ── */
.sb-tooltip {
  position: fixed;
  transform: translateY(-50%);
  background: #1e293b;
  color: #fff;
  padding: .4rem .65rem;
  border-radius: 7px;
  font-size: .75rem;
  font-weight: 600;
  white-space: nowrap;
  z-index: 2000;
  pointer-events: none;
  opacity: 0;
  transition: opacity .12s ease;
  box-shadow: 0 6px 16px rgba(0,0,0,.22);
}
.sb-tooltip.show { opacity: 1; }
.sb-tooltip::before {
  content: '';
  position: absolute;
  right: 100%;
  top: 50%;
  transform: translateY(-50%);
  border: 5px solid transparent;
  border-right-color: #1e293b;
}

/* Sidebar Footer Styling */
.sidebar-footer {
    padding: 0.75rem 1rem;
    border-top: 1px solid var(--sidebar-border);
    margin-top: auto;
    flex-shrink: 0;
}
.sidebar-footer .user-card {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    text-decoration: none;
    padding: 0.5rem;
    border-radius: 8px;
    transition: background 0.2s;
}
.sidebar-footer .user-card:hover {
    background: var(--sidebar-hover-bg);
}
.sidebar-footer .user-avatar {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: var(--primary);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.85rem;
    overflow: hidden;
    position: relative;
    flex-shrink: 0;
    border: 1.5px solid rgba(255,255,255,0.1);
}
.sidebar-footer .user-info {
    display: flex;
    flex-direction: column;
    overflow: hidden;
    flex: 1;
}
.sidebar-footer .user-name {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--sidebar-text);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.sidebar-footer .user-role {
    font-size: 0.65rem;
    color: var(--sidebar-text);
    opacity: 0.6;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Collapsed Sidebar Footer Styling */
html.sidebar-collapsed .sidebar-footer {
    padding: 0.75rem 0 !important;
    display: flex;
    justify-content: center;
}
html.sidebar-collapsed .sidebar-footer .user-card {
    justify-content: center;
    padding: 0;
    width: 100%;
}
html.sidebar-collapsed .sidebar-footer .user-info {
    display: none !important;
}
</style>
<script>
(function() {
  if (localStorage.getItem('fjb-sb-collapsed') === '1' && window.innerWidth > 768)
    document.documentElement.classList.add('sidebar-collapsed');
})();
</script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}?v={{ time() }}">
@yield('styles')
</head>
<body class="app-body">
<aside class="sidebar" id="sidebar">
    <div style="display:flex;align-items:center;padding:1.1rem 1.25rem 1rem;border-bottom:1px solid var(--sidebar-border);gap:.75rem">
        <div class="brand-icon-sm sb-logo-btn"
             onclick="if(document.documentElement.classList.contains('sidebar-collapsed'))toggleSidebar();"
             title="Open sidebar" style="cursor:default;flex-shrink:0;transition:opacity .15s;width:44px;height:44px;border-radius:10px;overflow:hidden;padding:4px">
            <img src="{{ asset('assets/images/logo.png') }}" alt="FJB" style="width:100%;height:100%;object-fit:contain;">
            <span class="sb-open-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="9" y1="3" x2="9" y2="21"/><polyline points="13 8 17 12 13 16"/></svg>
            </span>
        </div>
        <a href="{{ Auth::check() ? url('/dashboard') : route('login') }}" style="text-decoration:none;display:flex;align-items:center;flex:1;font-family:'Inter',sans-serif;font-size:1.05rem;font-weight:700;color:var(--sidebar-brand-text);" title="Go to Dashboard">
            HR Admin
        </a>
        <button class="sb-close-btn" onclick="toggleSidebar()" title="Close sidebar">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="9" y1="3" x2="9" y2="21"/><polyline points="5 8 1 12 5 16"/></svg>
        </button>
    </div>
    <nav class="sidebar-nav">
        @auth
        <a href="{{ url('/dashboard') }}" class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
            Dashboard
        </a>

        <div class="nav-group" id="navGroupRegistry">
            <div class="nav-group-toggle {{ in_array(request()->segment(1), ['staff', 'family', 'report', 'ir', 'archived-staff']) ? 'open has-active' : '' }}"
                 onclick="toggleNavGroup('navGroupRegistry')">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                <span>{{ Auth::user()->isStaff() ? 'My Profile' : 'Staffing' }}</span>
                <svg class="toggle-arrow" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
            </div>
            <div class="nav-group-children {{ in_array(request()->segment(1), ['staff', 'family', 'report', 'ir', 'archived-staff']) ? 'open' : '' }}">
                <div class="nav-group-children-inner">
                    <a href="{{ url('/staff') }}" class="nav-child {{ request()->is('staff*') ? 'active' : '' }}">{{ Auth::user()->isStaff() ? 'My Profile' : 'Staff List' }}</a>
                    <a href="{{ url('/family') }}" class="nav-child {{ request()->is('family*') ? 'active' : '' }}">{{ Auth::user()->isStaff() ? 'My Family' : 'Family Info' }}</a>
                    @if(Auth::user()->isAdmin() || Auth::user()->isCeo())
                    <a href="{{ url('/report') }}" class="nav-child {{ request()->is('report*') ? 'active' : '' }}">Registry Report</a>
                    @endif
                    @if(Auth::user()->isAdmin() || Auth::user()->isCeo())
                    <a href="{{ url('/ir') }}" class="nav-child {{ request()->is('ir*') ? 'active' : '' }}">IR Records</a>
                    <a href="{{ route('archived-staff.index') }}" class="nav-child {{ request()->is('archived-staff*') ? 'active' : '' }}">Archived Staff</a>
                    @endif
                </div>
            </div>
        </div>

        <div class="nav-group" id="navGroupTraining">
            <div class="nav-group-toggle {{ in_array(request()->segment(1), ['training', 'training-report']) ? 'open has-active' : '' }}"        
                 onclick="toggleNavGroup('navGroupTraining')">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                </svg>
                <span>Training</span>
                <svg class="toggle-arrow" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
            </div>
            <div class="nav-group-children {{ in_array(request()->segment(1), ['training', 'training-report']) ? 'open' : '' }}">
                <div class="nav-group-children-inner">
                    <a href="{{ url('/training') }}" class="nav-child {{ request()->is('training') ? 'active' : '' }}">Training Records</a>
                    @if(Auth::user()->isAdmin() || Auth::user()->isCeo())
                    <a href="{{ url('/training-report') }}" class="nav-child {{ request()->is('training-report*') ? 'active' : '' }}">Training Report</a>
                    @endif
                </div>
            </div>
        </div>

        @php
            $isPic = DB::table('room_pics')->where('user_id', Auth::id())->exists();
            $canApproveRooms = Auth::user()->isAdminIT() || $isPic;
            $roomBadge = $canApproveRooms ? Auth::user()->getPendingBookingCount() : Auth::user()->getUnreadBookingCount();
        @endphp

        <a href="{{ url('/rooms') }}" class="nav-item {{ request()->is('rooms*') || request()->is('/') ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            Meeting Rooms
            @if ($roomBadge > 0)
                <span class="badge-count">{{ $roomBadge }}</span>
            @endif
        </a>

        <a href="{{ url('/travel') }}" class="nav-item {{ request()->is('travel*') ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
            {{ (Auth::user()->isAdmin() || Auth::user()->isCeo()) ? 'Travel Records' : 'My Travel' }}
        </a>

        @if(Auth::user()->isAdmin() || Auth::user()->isCeo())
        <a href="{{ url('/requests') }}" class="nav-item {{ request()->is('requests*') ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Update Requests
            @php
                $requestBadge = Auth::user()->isAdminIT() ? Auth::user()->getPendingRequestCount() : Auth::user()->getUnreadRequestCount();
            @endphp
            @if ($requestBadge > 0)
                <span class="badge-count" {{ Auth::user()->isAdminHR() ? 'id=nav-badge-request' : '' }}>{{ $requestBadge }}</span>
            @endif
        </a>
        @endif

        @if(!Auth::user()->isAdmin() && !Auth::user()->isCeo())
        <a href="{{ url('/my-requests') }}" class="nav-item {{ request()->is('my-requests*') ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
            My Requests
            @php
                $myPendingCount = App\Models\UpdateRequest::where('requester_id', Auth::id())->where('status', 'Pending')->count();
                $myRequestBadge = $myPendingCount + Auth::user()->getUnreadRequestCount();
            @endphp
            @if ($myRequestBadge > 0)
                <span class="badge-count" id="nav-badge-request">{{ $myRequestBadge }}</span>
            @endif
        </a>
        @endif

                  @php
              $sysSecActive = request()->is('master-data*') || request()->is('users*') || request()->is('audit-log*') || request()->is('account/security*');
          @endphp
          @if(Auth::user()->isAdmin() || Auth::user()->isCeo() || Auth::user()->isHrUser())
          <div class="nav-divider"></div>
          <div class="nav-group" id="navGroupSystemSecurity">
              <div class="nav-group-toggle {{ $sysSecActive ? 'open has-active' : '' }}" onclick="toggleNavGroup('navGroupSystemSecurity')">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                  <span>System Security</span>
                  @if(Auth::user()->isHrUser() && !Auth::user()->hasTotpSetup())
                      <span class="badge-count" style="background:#f59e0b;">!</span>
                  @endif
                  <svg class="toggle-arrow" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                      <polyline points="9 18 15 12 9 6"/>
                  </svg>
              </div>
              <div class="nav-group-children {{ $sysSecActive ? 'open' : '' }}">
                  <div class="nav-group-children-inner">
                      @if(Auth::user()->isAdmin() || Auth::user()->isCeo())
                      <a href="{{ url('/master-data') }}" class="nav-child {{ request()->is('master-data*') ? 'active' : '' }}">Master Data</a>
                      @endif
                      
                      @if(Auth::user()->isAdminIT() || Auth::user()->isCeo())
                      <a href="{{ url('/users') }}" class="nav-child {{ request()->is('users*') ? 'active' : '' }}">User Accounts</a>
                      <a href="{{ route('audit-log.index') }}" class="nav-child {{ request()->is('audit-log*') ? 'active' : '' }}">Audit Log</a>
                      <a href="{{ route('email-settings.index') }}" class="nav-child {{ request()->routeIs('email-settings.*') ? 'active' : '' }}">Email Settings</a>
                      @endif
                      
                      @if(Auth::user()->isHrUser())
                      <a href="{{ route('account.security') }}" class="nav-child {{ request()->is('account/security*') ? 'active' : '' }}">
                          Account Security
                          @if(!Auth::user()->hasTotpSetup())
                              <span class="badge-count" style="background:#f59e0b; margin-left: 6px;">!</span>
                          @endif
                      </a>
                      @endif
                  </div>
              </div>
          </div>
          @endif
        
        <a href="{{ route('user-manual.index') }}" class="nav-item {{ request()->is('user-manual*') ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
            User Manual
        </a>
        <div class="nav-divider" style="border-top:1px solid rgba(255,255,255,.08);margin:12px 0 8px"></div>
        <a href="javascript:void(0)" onclick="openModal('roleMatrixModal')" class="nav-item">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
            Role Matrix
        </a>
        <a href="{{ route('lms.dashboard') }}" class="nav-item">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
            LMS System
        </a>
        <a href="{{ route('it.dashboard') }}" class="nav-item">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            IT System
        </a>
        <a href="{{ route('wt.login') }}" class="nav-item">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.5 10c-.83 0-1.5-.67-1.5-1.5v-5c0-.83.67-1.5 1.5-1.5s1.5.67 1.5 1.5v5c0 .83-.67 1.5-1.5 1.5z"/><path d="M20.5 10H19V8.5c0-.83.67-1.5 1.5-1.5s1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/><path d="M9.5 14c.83 0 1.5.67 1.5 1.5v5c0 .83-.67 1.5-1.5 1.5S8 21.33 8 20.5v-5c0-.83.67-1.5 1.5-1.5z"/><path d="M3.5 14H5v1.5c0 .83-.67 1.5-1.5 1.5S2 16.33 2 15.5 2.67 14 3.5 14z"/><path d="M14 14.5c0-.83.67-1.5 1.5-1.5h5c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5h-5c-.83 0-1.5-.67-1.5-1.5z"/><path d="M15.5 19H14v1.5c0 .83.67 1.5 1.5 1.5s1.5-.67 1.5-1.5-.67-1.5-1.5-1.5z"/><path d="M10 9.5C10 8.67 9.33 8 8.5 8h-5C2.67 8 2 8.67 2 9.5S2.67 11 3.5 11h5c.83 0 1.5-.67 1.5-1.5z"/><path d="M8.5 5H10V3.5C10 2.67 9.33 2 8.5 2S7 2.67 7 3.5 7.67 5 8.5 5z"/></svg>
            WT System
        </a>
        <a href="{{ route('home') }}" class="nav-item">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
            Back to Portal
        </a>
        @else
        <a href="{{ route('login') }}" class="nav-item">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
            Sign In
        </a>
        @endauth
    </nav>
    @auth
    @php
        $profileUrl = Auth::user()->staff_id ? route('staff.show', Auth::user()->staff_id) : route('users.show', Auth::id());
        $avatarChar = strtoupper(substr(Auth::user()->name ?? 'U', 0, 1));
        $userRoleLabel = Auth::user()->getRoleLabel();
    @endphp
    <div class="sidebar-footer">
        <a href="{{ $profileUrl }}" class="user-card" title="View Profile">
            <div class="user-avatar">
                @if(Auth::user()->avatar && Storage::disk('public')->exists(Auth::user()->avatar))
                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    {{ $avatarChar }}
                @endif
            </div>
            <div class="user-info">
                <div class="user-name">{{ Auth::user()->name }}</div>
                <div class="user-role">{{ $userRoleLabel }}</div>
            </div>
        </a>
    </div>
    @endauth
</aside>
<div class="main-wrapper">
    <header class="topbar">
        <button class="menu-toggle" onclick="toggleSidebar()">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>  
        </button>
        <div class="topbar-title">
            @yield('title', 'Dashboard')
        </div>
        <div class="topbar-right">

            @auth
            <button id="theme-toggle" type="button" class="theme-toggle" title="Toggle light/dark" aria-label="Toggle light or dark mode">
                <svg id="theme-toggle-dark-icon" class="theme-icon" xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
                <svg id="theme-toggle-light-icon" class="theme-icon hidden" xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
            </button>
            <div class="notif-wrap" id="notifBellWrap">
                <button class="notif-bell-btn" id="notifBellBtn" onclick="toggleNotifDropdown()" aria-label="Notifications">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                    @php $totalUnread = Auth::user()->getUnreadBookingCount() + Auth::user()->getUnreadRequestCount() + Auth::user()->getUnreadTravelCount(); @endphp
                    @if ($totalUnread > 0)
                    <span class="notif-badge" id="notifBellBadge">{{ $totalUnread }}</span>
                    @endif
                </button>
                <div class="notif-dropdown" id="notifDropdown" style="display:none;">
                    <div class="notif-dd-header">
                        <span class="notif-dd-title">Notifications</span>
                        <button class="notif-mark-all" onclick="markAllNotifRead()">Mark all as read</button>
                    </div>
                    <div class="notif-list" id="notifList">
                        <div class="notif-empty">Loading…</div>
                    </div>
                </div>
            </div>
            <a href="{{ route('users.show', Auth::id()) }}" class="topbar-user" style="text-decoration:none;color:inherit;cursor:pointer" title="View my profile">
                <strong>{{ Auth::user()->name }}</strong>
                <span class="role-badge {{ str_replace('_', '-', Auth::user()->role) }}">{{ Auth::user()->getRoleLabel() }}</span>
            </a>
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-outline btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    <span class="btn-label">Logout</span>
                </button>
            </form>
            @else
            <a href="{{ route('login') }}" class="btn btn-primary btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:.3rem;"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
                <span class="btn-label">Sign In</span>
            </a>
            @endauth
        </div>
    </header>
    <main class="content-area">
        @if(session('success'))
        <div class="toast-container" id="toastContainer">
            <div class="toast toast-success" id="mainToast">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-.15em;margin-right:.45rem;flex-shrink:0;"><polyline points="20 6 9 17 4 12"/></svg>
                {{ session('success') }}
            </div>
        </div>
        @endif
        @if(session('error'))
        <div class="toast-container" id="toastContainer">
            <div class="toast toast-error" id="mainToast">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-.15em;margin-right:.45rem;flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ session('error') }}
            </div>
        </div>
        @endif
        @if($errors->any())
        <div class="toast-container" id="toastContainer">
            <div class="toast toast-error" id="mainToast">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-.15em;margin-right:.45rem;flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <div>
                    @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        @auth
        @if(Auth::user()->isStaff() && Auth::user()->staff && !Auth::user()->staff->is_active)
        <div style="display:flex;align-items:center;gap:.75rem;background:#fee2e2;border:1px solid #fca5a5;border-radius:8px;padding:.85rem 1.25rem;margin-bottom:1.25rem;color:#991b1b;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <div><strong>Your account is currently inactive.</strong> You can view your records but cannot make room bookings or mark training attendance. Please contact HR for assistance.</div>
        </div>
        @endif
        @endauth

        @yield('content')
        @include('components.ui.standardizer')

        <div class="app-footer" style="text-align: center; margin-top: 3rem; padding: 2rem 0; border-top: 1px solid var(--border, rgba(0,0,0,0.05)); clear: both;">
            <div style="margin-bottom: 0.5rem;">
                <img id="footerLogo" src="{{ asset('assets/images/footer.jpg') }}" alt="IT Logo" style="max-height: 45px; width: auto; object-fit: contain; cursor: default; user-select: none;">
            </div>
            <div id="eggFooterText" style="font-size: 0.85rem; color: var(--muted, #64748b); font-weight: 500; cursor: pointer; user-select: none;">
                Develop by IT team
            </div>
        </div>
    </main>
</div>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
<div class="modal-overlay" id="modalOverlay" onclick="closeModal()"></div>

<!-- Notification popup container -->
<div id="notifPopupStack" style="position:fixed;bottom:1.5rem;right:1.5rem;z-index:8000;display:flex;flex-direction:column-reverse;gap:.6rem;max-width:340px;pointer-events:none;"></div>

@include('partials.chatbot')
<script src="{{ asset('assets/js/app.js') }}?v={{ filemtime(public_path('assets/js/app.js')) }}"></script>
<script>
const HR_DARK_VARS = {
  '--bg': '#0f172a',
  '--body-bg': '#0f172a',
  '--surface': '#1e293b',
  '--white': '#1e293b',
  '--border': '#334155',
  '--text': '#e2e8f0',
  '--muted': '#94a3b8',
  '--navy': '#e2e8f0',
  '--table-hover': 'rgba(255,255,255,.04)',
  '--table-head-bg': '#111827',
  '--table-row-alt': '#172033',
  '--form-input-bg': '#0f172a',
  '--form-input-color': '#e2e8f0'
};
const HR_LIGHT_VARS = {
  '--bg': '#f1f5f9',
  '--body-bg': '#f1f5f9',
  '--surface': '#ffffff',
  '--white': '#ffffff',
  '--border': '#e2e8f0',
  '--text': '#1e293b',
  '--muted': '#64748b',
  '--navy': '#142b47',
  '--table-hover': '#e0f2fe',
  '--table-head-bg': '#f8fafc',
  '--table-row-alt': '#fafbfc',
  '--form-input-bg': '#ffffff',
  '--form-input-color': '#1e293b'
};

function applyTheme(dark) {
  const vars = dark ? HR_DARK_VARS : HR_LIGHT_VARS;
  Object.entries(vars).forEach(([key, value]) => document.documentElement.style.setProperty(key, value));
  document.documentElement.classList.toggle('dark', dark);
  document.documentElement.setAttribute('data-theme', dark ? 'dark' : 'light');
  document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
  document.body.style.backgroundColor = vars['--bg'];
  document.body.style.color = vars['--text'];

  const darkIcon = document.getElementById('theme-toggle-dark-icon');
  const lightIcon = document.getElementById('theme-toggle-light-icon');
  if (darkIcon && lightIcon) {
    darkIcon.classList.toggle('hidden', dark);
    lightIcon.classList.toggle('hidden', !dark);
  }
}

function toggleTheme() {
  const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
  const next = isDark ? 'light' : 'dark';
  localStorage.setItem('fjb-theme', next);
  localStorage.setItem('color-theme', next);
  localStorage.setItem('theme', next);
  applyTheme(next === 'dark');
}

document.getElementById('theme-toggle')?.addEventListener('click', toggleTheme);
(function () {
  const saved = localStorage.getItem('fjb-theme') || localStorage.getItem('color-theme') || localStorage.getItem('theme');
  const theme = saved || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
  applyTheme(theme === 'dark');
})();

// Override to add desktop collapse support
function toggleSidebar() {
  if (window.innerWidth <= 768) {
    var sb = document.getElementById('sidebar');
    var ov = document.getElementById('sidebarOverlay');
    if (sb) sb.classList.toggle('open');
    if (ov) ov.classList.toggle('active');
  } else {
    var c = document.documentElement.classList.toggle('sidebar-collapsed');
    localStorage.setItem('fjb-sb-collapsed', c ? '1' : '0');
  }
}

// Collapsed-sidebar hover tooltips: show each item's label when the rail is collapsed
(function () {
  var tip = null;
  function ensureTip() {
    if (!tip) { tip = document.createElement('div'); tip.className = 'sb-tooltip'; document.body.appendChild(tip); }
    return tip;
  }
  function isCollapsed() {
    return document.documentElement.classList.contains('sidebar-collapsed') && window.innerWidth > 768;
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

  document.querySelectorAll('.sidebar .nav-item, .sidebar .nav-group-toggle').forEach(function (el) {
    var label = Array.from(el.childNodes)
      .filter(function (n) { return n.nodeType === 3; })
      .map(function (n) { return n.textContent; })
      .join('').replace(/\s+/g, ' ').trim();
    if (!label) {
      var span = el.querySelector(':scope > span:not(.badge-count):not(.toggle-arrow)');
      if (span) label = span.textContent.replace(/\s+/g, ' ').trim();
    }
    if (!label) return;
    el.setAttribute('data-tooltip', label);
    el.addEventListener('mouseenter', function () { showTip(el); });
    el.addEventListener('mouseleave', hideTip);
    el.addEventListener('click', hideTip);
  });
})();
</script>

<script>

// Notification system from source dashboard.php
let notifSeenIds = new Set();
const notifIcons = {
    booking: `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>`,
    request: `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>`,
};
const notifColors = { booking: '#6366f1', request: '#d97706' };
let notifReadIds = new Set();

function notifMarkRead(id, link, type) {
    if (notifReadIds.has(id)) { if (link) window.location.href = link; return; }
    notifReadIds.add(id);
    notifSeenIds.add(id);
    fetch('{{ url("notifications/mark-read") }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: JSON.stringify({ids: [id]})
    });
    decrementNavBadge(type);
    if (link) window.location.href = link;
}

function decrementNavBadge(type) {
    const el = document.getElementById('nav-badge-' + type);
    if (!el) return;
    const val = parseInt(el.textContent, 10) - 1;
    if (val <= 0) el.remove();
    else el.textContent = val;
    const bell = document.getElementById('notifBellBadge');
    if (!bell) return;
    const bval = parseInt(bell.textContent, 10) - 1;
    if (bval <= 0) bell.remove();
    else bell.textContent = bval;
}

function showNotifPopup(items) {
    const stack = document.getElementById('notifPopupStack');
    if (!stack) return;
    items.slice(0, 3).forEach((n, i) => {
        setTimeout(() => {
            const card = document.createElement('div');
            card.className = 'notif-popup-card';
            card.style.pointerEvents = 'auto';
            card.innerHTML = `
                <div class="notif-popup-accent" style="background:${notifColors[n.type]||'#6366f1'}"></div>
                <div class="notif-popup-inner">
                    <div class="notif-popup-icon" style="color:${notifColors[n.type]||'#6366f1'}">
                        ${notifIcons[n.type] || notifIcons.request}
                    </div>
                    <div class="notif-popup-body">
                        <div class="notif-popup-title">${n.title}</div>
                        <div class="notif-popup-msg">${n.message || ''}</div>
                    </div>
                    <button class="notif-popup-close" onclick="
                        this.closest('.notif-popup-card').remove();
                        notifMarkRead(${n.id}, null, '${n.type}');
                    ">×</button>
                </div>
            `;
            if (n.link) card.style.cursor = 'pointer';
            card.addEventListener('click', ev => {
                if (ev.target.classList.contains('notif-popup-close')) return;
                card.remove();
                notifMarkRead(n.id, n.link, n.type);
            });
            stack.appendChild(card);
            setTimeout(() => {
                if (card.parentNode) {
                    card.remove();
                    notifMarkRead(n.id, null, n.type);
                }
            }, 8000);
        }, i * 300);
    });
}

let notifDropdownOpen = false;
function toggleNotifDropdown() {
    const dd = document.getElementById('notifDropdown');
    notifDropdownOpen = !notifDropdownOpen;
    dd.style.display = notifDropdownOpen ? 'block' : 'none';
    if (notifDropdownOpen) loadNotifDropdown();
}

function loadNotifDropdown() {
    fetch('{{ url("notifications/list") }}')
        .then(r => r.json())
        .then(items => renderNotifList(items))
        .catch(() => {});
}

function renderNotifList(items) {
    const list = document.getElementById('notifList');
    if (!list) return;
    if (!items || items.length === 0) {
        list.innerHTML = '<div class="notif-empty">No notifications</div>';
        return;
    }
    list.innerHTML = items.map(n => {
        const color = notifColors[n.type] || '#6366f1';
        const icon  = notifIcons[n.type]  || notifIcons.request;
        return `
            <div class="notif-item ${n.is_read ? '' : 'unread'}"
                 data-notif-id="${n.id}" data-notif-type="${n.type}"
                 onclick="notifMarkRead(${n.id}, '${n.link}', '${n.type}')">
                <div class="notif-item-icon" style="background:${color}15; color:${color}">${icon}</div>
                <div class="notif-item-content">
                    <div class="notif-item-title">${n.title}</div>
                    <div class="notif-item-msg">${n.message}</div>
                    <div class="notif-item-time">${new Date(n.created_at).toLocaleString()}</div>
                </div>
            </div>
        `;
    }).join('');
}

function markAllNotifRead() {
    // Visually clear all unread items
    const items = document.querySelectorAll('#notifList .notif-item.unread');
    items.forEach(el => el.classList.remove('unread'));

    const ids = Array.from(items).map(el => parseInt(el.dataset.notifId)).filter(Boolean);
    ids.forEach(id => { notifReadIds.add(id); notifSeenIds.add(id); });

    // Clear all badges
    document.getElementById('notifBellBadge')?.remove();
    ['booking', 'request', 'travel'].forEach(type => {
        document.getElementById('nav-badge-' + type)?.remove();
    });

    // Send request to server
    fetch('{{ url("notifications/mark-read") }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: JSON.stringify({all: true})
    });
}
</script>
<script>
function liveSearch(form, resultId, delay) {
    var el = document.getElementById(resultId);
    if (!el) return;
    var t;
    var ready = false;
    // Delay activation so browser auto-restore of form fields doesn't trigger an AJAX reload
    setTimeout(function() { ready = true; }, 600);
    function run() {
        if (!ready) return;
        clearTimeout(t);
        t = setTimeout(function() {
            var p = new URLSearchParams();
            new FormData(form).forEach(function(v, k) { p.append(k, v); });
            p.set('_t', Date.now()); // cache buster
            var url = form.action + '?' + p.toString();
            el.style.transition = 'opacity .15s';
            el.style.opacity = '.45';
            fetch(url, { 
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Cache-Control': 'no-cache, no-store' },
                cache: 'no-store'
            })
                .then(function(r) { return r.text(); })
                .then(function(html) {
                    var doc = new DOMParser().parseFromString(html, 'text/html');
                    var fresh = doc.getElementById(resultId);
                    // Strip _t param from display URL
                    p.delete('_t');
                    var displayUrl = form.action + (p.toString() ? '?' + p.toString() : '');
                    if (fresh) { el.innerHTML = fresh.innerHTML; history.replaceState(null, '', displayUrl); }
                })
                .catch(function() {})
                .finally(function() { el.style.opacity = ''; });
        }, delay || 320);
    }
    form.querySelectorAll('input[type=text],input[type=search]').forEach(function(i) { i.addEventListener('input', run); });
    form.querySelectorAll('input[type=month],input[type=date]').forEach(function(i) { i.addEventListener('change', run); });
    form.querySelectorAll('select').forEach(function(s) { s.addEventListener('change', function() { clearTimeout(t); run(); }); });
}
</script>

@include('partials.role-matrix-modal')

@yield('scripts')
<script>
(function() {
    var content = document.querySelector('.content-area');
    document.querySelectorAll('.nav-item, .nav-child').forEach(function(link) {
        link.addEventListener('click', function(e) {
            var href = this.getAttribute('href');
            if (!href || href === '#' || href.startsWith('javascript') || this.closest('form')) return;
            e.preventDefault();

            if (this.classList.contains('nav-child') && document.documentElement.classList.contains('sidebar-collapsed')) {
                document.documentElement.classList.remove('sidebar-collapsed');
                localStorage.setItem('fjb-sb-collapsed', '0');
                document.querySelectorAll('.nav-child').forEach(c => c.classList.remove('active'));
                this.classList.add('active');
            }

            if (content) {
                content.classList.add('page-out');
                setTimeout(function() { window.location.href = href; }, 150);
            } else {
                window.location.href = href;
            }
        });
    });
})();
</script>

@auth
@if(Auth::user()->isStaff() && Auth::user()->staff && !Auth::user()->staff->is_active)
<style>
.ib-wrap { display:inline-block; cursor:not-allowed; }
.ib-wrap > * { pointer-events:none; opacity:.4; filter:blur(.6px); transition:none; }
#_ibTip {
    position:fixed; z-index:99999; pointer-events:none; display:none;
    background:#1e293b; color:#fff; font-size:.74rem; line-height:1.5;
    padding:.5rem .85rem; border-radius:8px;
    box-shadow:0 6px 18px rgba(0,0,0,.35);
    max-width:230px;
}
#_ibTip strong { display:block; margin-bottom:.15rem; font-size:.78rem; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tip = document.createElement('div');
    tip.id = '_ibTip';
    tip.innerHTML = '<strong>Access Blocked</strong>Your account is currently inactive. Please contact HR for assistance.';
    document.body.appendChild(tip);

    function applyBlock(btn) {
        if (btn.closest('.ib-wrap')) return;
        const wrap = document.createElement('span');
        wrap.className = 'ib-wrap';
        btn.parentNode.insertBefore(wrap, btn);
        wrap.appendChild(btn);
        wrap.addEventListener('mouseenter', () => tip.style.display = 'block');
        wrap.addEventListener('mousemove', e => {
            tip.style.left = (e.clientX + 14) + 'px';
            tip.style.top  = (e.clientY - 50) + 'px';
        });
        wrap.addEventListener('mouseleave', () => tip.style.display = 'none');
    }

    document.querySelectorAll('[data-requires-active]').forEach(applyBlock);

    // Catch dynamically added buttons
    new MutationObserver(mutations => {
        mutations.forEach(m => m.addedNodes.forEach(n => {
            if (n.nodeType !== 1) return;
            if (n.matches?.('[data-requires-active]')) applyBlock(n);
            n.querySelectorAll?.('[data-requires-active]').forEach(applyBlock);
        }));
    }).observe(document.body, { childList: true, subtree: true });
});
</script>
@endif
@endauth

{{-- â•”â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•—
     ║  Easter Egg — Creator credit                                  ║
     ║  Triggers: Konami code (↑↑↓↓â†→â†→ B A)  ·  footer clicked 5×   ║
     â•šâ•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
<audio id="eggAudio" preload="auto" loop>
    <source src="{{ asset('assets/audio/easter-egg.mp3') }}" type="audio/mpeg">
</audio>
<div class="egg-overlay" id="eggOverlay" aria-hidden="true" onclick="if(event.target===this)closeEgg()">
    <div class="egg-card" role="dialog" aria-label="Creator credit">
        <button class="egg-close" onclick="closeEgg()" aria-label="Close">&times;</button>
        <div class="egg-confetti" id="eggConfetti"></div>
        <div class="egg-badge">
            <img src="{{ asset('assets/images/aren.jpg') }}" alt="" class="egg-badge-img">
        </div>
        <div class="egg-kicker">✦ You found the secret ✦</div>
        <div class="egg-name">ALIF&nbsp;TEOH</div>
        <div class="egg-role">Creator &amp; Developer</div>
        <div class="egg-divider"></div>
        <div class="egg-foot">Crafted with care for the FJB HR System</div>
    </div>
</div>
<style>
.egg-overlay{
    position:fixed; inset:0; z-index:99999;
    display:none; align-items:center; justify-content:center;
    padding:1.25rem;
    background:rgba(8,20,40,.62); backdrop-filter:blur(6px);
}
.egg-overlay.show{ display:flex; animation:eggFade .25s ease both; }
@keyframes eggFade{ from{opacity:0} to{opacity:1} }
.egg-card{
    position:relative; overflow:hidden;
    width:100%; max-width:380px;
    background:linear-gradient(160deg,#0f223b 0%,#1a4b8c 100%);
    border:1px solid rgba(56,189,248,.35);
    border-radius:22px;
    padding:2.5rem 2rem 2rem;
    text-align:center; color:#fff;
    box-shadow:0 35px 80px -20px rgba(2,132,199,.6), 0 4px 14px rgba(0,0,0,.4);
    animation:eggPop .5s cubic-bezier(.16,.84,.44,1) both;
}
@keyframes eggPop{ from{opacity:0;transform:translateY(20px) scale(.92)} to{opacity:1;transform:none} }
.egg-close{
    position:absolute; top:.75rem; right:.9rem;
    background:none; border:none; color:rgba(255,255,255,.55);
    font-size:1.6rem; line-height:1; cursor:pointer; transition:color .15s;
}
.egg-close:hover{ color:#fff; }
.egg-badge{
    width:64px; height:64px; margin:0 auto 1rem;
    display:flex; align-items:center; justify-content:center;
    border-radius:50%; color:#0f223b;
    background:linear-gradient(135deg,#38bdf8,#7dd3fc);
    box-shadow:0 10px 26px -8px rgba(56,189,248,.8);
    animation:eggSpin 6s linear infinite;
}
@keyframes eggSpin{ to{ transform:rotate(360deg) } }
.egg-badge-img{ width:100%; height:100%; object-fit:cover; border-radius:inherit; display:block; }
.egg-kicker{ font-size:.72rem; letter-spacing:.22em; text-transform:uppercase; color:#7dd3fc; font-weight:600; }
.egg-name{
    margin-top:.55rem; font-size:2.1rem; font-weight:700; letter-spacing:.02em;
    background:linear-gradient(90deg,#fff,#bae6fd,#fff);
    -webkit-background-clip:text; background-clip:text; -webkit-text-fill-color:transparent;
    background-size:200% auto; animation:eggShine 3s linear infinite;
}
@keyframes eggShine{ to{ background-position:200% center } }
.egg-role{ margin-top:.2rem; font-size:.9rem; color:rgba(255,255,255,.75); font-weight:500; }
.egg-divider{ width:48px; height:3px; margin:1.15rem auto; border-radius:2px; background:linear-gradient(90deg,#38bdf8,transparent); }
.egg-foot{ font-size:.78rem; color:rgba(255,255,255,.55); }
.egg-confetti{ position:absolute; inset:0; pointer-events:none; overflow:hidden; }
.egg-confetti span{
    position:absolute; top:-12px; width:8px; height:8px; border-radius:2px;
    animation:eggDrop linear forwards;
}
@keyframes eggDrop{ to{ transform:translateY(520px) rotate(540deg); opacity:0; } }
</style>
<script>
(function(){
    const overlay  = document.getElementById('eggOverlay');
    if(!overlay) return;
    const confetti = document.getElementById('eggConfetti');
    const audio    = document.getElementById('eggAudio');
    const colors   = ['#38bdf8','#7dd3fc','#fcd34d','#fff','#0284c7'];
    let open = false;

    window.closeEgg = function(){
        overlay.classList.remove('show'); overlay.setAttribute('aria-hidden','true'); open = false;
        if(audio){ audio.pause(); audio.currentTime = 0; }
    };

    function burstConfetti(){
        if(!confetti) return;
        confetti.innerHTML = '';
        for(let i=0;i<60;i++){
            const s = document.createElement('span');
            s.style.left = Math.random()*100 + '%';
            s.style.background = colors[Math.floor(Math.random()*colors.length)];
            s.style.animationDuration = (1.6 + Math.random()*1.8) + 's';
            s.style.animationDelay = (Math.random()*0.6) + 's';
            if(Math.random()>.5) s.style.borderRadius = '50%';
            confetti.appendChild(s);
        }
    }

    function openEgg(){
        if(open) return;
        open = true;
        overlay.classList.add('show');
        overlay.setAttribute('aria-hidden','false');
        burstConfetti();
        if(audio){ audio.currentTime = 0; audio.play().catch(() => {}); }
    }

    // Trigger 1 — Konami code
    const seq = ['ArrowUp','ArrowUp','ArrowDown','ArrowDown','ArrowLeft','ArrowRight','ArrowLeft','ArrowRight','b','a'];
    let pos = 0;
    document.addEventListener('keydown', function(e){
        if(e.key === 'Escape' && open){ closeEgg(); return; }
        const k = e.key.length === 1 ? e.key.toLowerCase() : e.key;
        pos = (k === seq[pos]) ? pos + 1 : (k === seq[0] ? 1 : 0);
        if(pos === seq.length){ pos = 0; openEgg(); }
    });

    // Trigger 2 — click the footer text 5 times
    const foot = document.getElementById('eggFooterText');
    if(foot){
        let clicks = 0, timer = null;
        foot.addEventListener('click', function(){
            clicks++;
            clearTimeout(timer);
            timer = setTimeout(() => { clicks = 0; }, 1200);
            if(clicks >= 5){ clicks = 0; openEgg(); }
        });
    }

    // Signature in the browser console
    console.log(
        '%c✦ FJB HR System ✦%c\nCrafted by %cALIF TEOH%c\nPsst… try the Konami code ↑↑↓↓←→←→ B A',
        'font-size:15px;font-weight:700;color:#38bdf8',
        'color:#94a3b8',
        'font-size:14px;font-weight:700;color:#0f223b;background:#7dd3fc;padding:2px 8px;border-radius:4px',
        'color:#94a3b8'
    );
})();
</script>
<!-- Back to Top Button -->
<button id="backToTopBtn" onclick="window.scrollTo({top: 0, behavior: 'smooth'})" title="Go to top" style="display: none; position: fixed; bottom: 20px; left: 30px; z-index: 8000; background-color: #38bdf8; color: white; border: none; border-radius: 50%; width: 45px; height: 45px; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.15); align-items: center; justify-content: center; transition: transform 0.2s ease, opacity 0.3s ease;">
    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"></polyline></svg>
</button>
<script>
    window.addEventListener('scroll', function() {
        var btn = document.getElementById('backToTopBtn');
        if (window.scrollY > 300) {
            btn.style.display = 'flex';
        } else {
            btn.style.display = 'none';
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Automatically uncollapse the sidebar on page load if an active inner page is being displayed
        if (document.documentElement.classList.contains('sidebar-collapsed')) {
            var hasActiveInner = document.querySelector('.nav-group-children.open .nav-child.active');
            if (hasActiveInner) {
                document.documentElement.classList.remove('sidebar-collapsed');
                localStorage.setItem('fjb-sb-collapsed', '0');
            }
        }
    });

<!-- [EGG-SHR-2026] Easter egg credit block — do not remove or the footer logo integrity check will fail -->
<script>
(function(){
    /* --- Tamper-proof Easter egg by Siti Hajar binti Abd Razak ---
       Encrypted strings below are Base64 encoded; editing them breaks
       the credit display. The anchor span is checked at runtime —
       if removed, the footer logo will visually degrade. */

    /* --- Integrity anchor --- */
    if(!document.getElementById('__egg_shrabr26')){
        var _anchor = document.createElement('span');
        _anchor.id = '__egg_shrabr26';
        _anchor.style.cssText = 'position:absolute;width:0;height:0;overflow:hidden;pointer-events:none;';
        document.body.appendChild(_anchor);
    }

    /* Guard: if anchor is deleted externally, degrade footer logo as a visual tell */
    var _guardTimer = setInterval(function(){
        if(!document.getElementById('__egg_shrabr26')){
            var fl = document.getElementById('footerLogo');
            if(fl){ fl.style.filter = 'grayscale(1) opacity(0.3)'; fl.title = 'integrity error'; }
            clearInterval(_guardTimer);
        }
    }, 2000);

    /* --- Obfuscated payload — Base64 chunks ---
       Decoded: "You found the creator | developer\nSiti Hajar binti Abd Razak\nStudent Intern from KV Perdagangan, JB\nv1.0.0 - 2026" */
    var _p = [
        'WW91IGZvdW5kIHRoZSBjcmVh',
        'dG9yIHwgZGV2ZWxvcGVyClNp',
        'dGkgSGFqYXIgYmludGkgQWJk',
        'IFJhemFrClN0dWRlbnQgSW50',
        'ZXJuIGZyb20gS1YgUGVyZGFn',
        'YW5nYW4sIEpCCnYxLjAuMCAt',
        'IDIwMjY='
    ];

    function _decode(arr){
        try{ return atob(arr.join('')); }catch(e){ return ''; }
    }

    function _creditLines(){
        var fallback = [
            'YOU FOUND THE SECRET',
            'CREATOR | DEVELOPER',
            'SITI HAJAR BINTI ABD RAZAK',
            'STUDENT INTERN FROM KV PERDAGANGAN, JB',
            '16/3/2026 - 31/7/2026<br>V1.0.0',
            'Crafted to empower your workflow, today and onwards.'
        ];

        try{
            var decoded = _decode(_p).split('\n');
            return [
                'YOU FOUND THE SECRET',
                'CREATOR | DEVELOPER',
                (decoded[1] || fallback[2]).toUpperCase(),
                (decoded[2] || fallback[3]).toUpperCase(),
                fallback[4],
                fallback[5]
            ];
        }catch(e){
            return fallback;
        }
    }

    function _fireCredit(){
        var existing = document.getElementById('creditSecretOverlay');
        if(existing) existing.remove();

        var lines = _creditLines();
        var colors = ['#f0abfc', '#fb7185', '#a78bfa', '#60a5fa', '#67e8f9', '#ffffff'];
        var overlay = document.createElement('div');
        overlay.id = 'creditSecretOverlay';
        overlay.innerHTML =
            '<div class="credit-secret-confetti" aria-hidden="true"></div>' +
            '<div class="credit-secret-card" role="dialog" aria-modal="true" aria-label="Creator credit">' +
                '<button type="button" class="credit-secret-close" aria-label="Close">&times;</button>' +
                '<div class="credit-secret-shine"></div>' +
                '<div class="credit-secret-mark">IT</div>' +
                '<div class="credit-secret-kicker">' + lines[0] + '</div>' +
                '<div class="credit-secret-title">' + lines[1] + '</div>' +
                '<div class="credit-secret-divider"></div>' +
                '<div class="credit-secret-name">' + lines[2] + '</div>' +
                '<div class="credit-secret-copy">' + lines[3] + '</div>' +
                '<div class="credit-secret-version">' + lines[4] + '</div>' +
                '<div class="credit-secret-note">' + lines[5] + '</div>' +
            '</div>';

        document.body.appendChild(overlay);
        var confetti = overlay.querySelector('.credit-secret-confetti');
        if(confetti){
            for(var i = 0; i < 84; i++){
                var piece = document.createElement('span');
                piece.style.left = (Math.random() * 100) + '%';
                piece.style.background = colors[Math.floor(Math.random() * colors.length)];
                piece.style.animationDuration = (2.1 + Math.random() * 1.9) + 's';
                piece.style.animationDelay = (Math.random() * 0.35) + 's';
                piece.style.setProperty('--credit-confetti-x', ((Math.random() * 120) - 60) + 'px');
                piece.style.transform = 'rotate(' + (Math.random() * 180) + 'deg)';
                if(Math.random() > 0.55) piece.style.borderRadius = '999px';
                confetti.appendChild(piece);
            }
        }
        requestAnimationFrame(function(){ overlay.classList.add('show'); });

        function close(){
            overlay.classList.remove('show');
            setTimeout(function(){ overlay.remove(); }, 180);
        }

        overlay.addEventListener('click', function(event){
            if(event.target === overlay) close();
        });
        overlay.querySelector('.credit-secret-close').addEventListener('click', close);
        document.addEventListener('keydown', function esc(event){
            if(event.key !== 'Escape') return;
            document.removeEventListener('keydown', esc);
            close();
        });
    }

    function _bindLongPress(el){
        if(!el) return;
        var t = null;
        var fired = false;

        function start(){
            fired = false;
            clearTimeout(t);
            t = setTimeout(function(){
                fired = true;
                _fireCredit();
            }, 2400);
        }

        function cancel(e){
            clearTimeout(t);
            if(fired && e && e.type === 'click'){
                e.preventDefault();
                e.stopPropagation();
            }
        }

        el.addEventListener('pointerdown', start);
        el.addEventListener('pointerup', cancel);
        el.addEventListener('pointerleave', cancel);
        el.addEventListener('pointercancel', cancel);
        el.addEventListener('click', cancel, true);
        el.addEventListener('contextmenu', function(e){ e.preventDefault(); });
    }

    /* ── Attach & lock ── */
    function _lock(name, val){
        try{
            Object.defineProperty(window, name, {
                value: val, writable: false, configurable: false, enumerable: false
            });
        }catch(e){}
    }

    _bindLongPress(document.getElementById('eggFooterText'));

    _lock('__eggLP',   _bindLongPress);
    _lock('__eggMeta', Object.freeze({ v: '1.0.0', a: 'SHR', y: 2026 }));

})();

</script>
<style>
#creditSecretOverlay {
    position: fixed;
    inset: 0;
    z-index: 100000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
    background: rgba(2, 6, 23, 0.62);
    backdrop-filter: blur(7px);
    opacity: 0;
    pointer-events: none;
    transition: opacity 180ms ease;
}
#creditSecretOverlay.show {
    opacity: 1;
    pointer-events: auto;
}
.credit-secret-card {
    position: relative;
    z-index: 100002;
    width: min(460px, 100%);
    overflow: hidden;
    border-radius: 24px;
    border: 1px solid rgba(240, 171, 252, 0.42);
    background:
        radial-gradient(circle at 15% 0%, rgba(251, 113, 133, 0.28), transparent 34%),
        radial-gradient(circle at 86% 8%, rgba(96, 165, 250, 0.28), transparent 36%),
        linear-gradient(145deg, #2b124c 0%, #1d4ed8 48%, #be185d 100%);
    color: #f8fafc;
    padding: 34px 30px 30px;
    text-align: center;
    box-shadow: 0 30px 90px rgba(2, 6, 23, 0.55), 0 0 52px rgba(217, 70, 239, 0.26);
    transform: translateY(16px) scale(0.96);
    transition: transform 220ms cubic-bezier(.16,.84,.44,1);
}
#creditSecretOverlay.show .credit-secret-card {
    transform: translateY(0) scale(1);
    animation: creditSecretPop 520ms cubic-bezier(.18,.89,.32,1.28) both;
}
@keyframes creditSecretPop {
    0% { opacity: 0; transform: translateY(26px) scale(0.9); }
    60% { opacity: 1; transform: translateY(-6px) scale(1.035); }
    100% { opacity: 1; transform: translateY(0) scale(1); }
}
.credit-secret-shine {
    position: absolute;
    inset: -80px -100px auto auto;
    width: 220px;
    height: 220px;
    border-radius: 50%;
    background: rgba(240, 171, 252, 0.22);
    filter: blur(10px);
    pointer-events: none;
}
.credit-secret-close {
    position: absolute;
    z-index: 3;
    top: 14px;
    right: 16px;
    border: 0;
    background: transparent;
    color: rgba(248, 250, 252, 0.68);
    font-size: 26px;
    line-height: 1;
    cursor: pointer;
    pointer-events: auto;
}
.credit-secret-close:hover { color: #ffffff; }
.credit-secret-mark {
    position: relative;
    display: inline-flex;
    width: 62px;
    height: 62px;
    align-items: center;
    justify-content: center;
    border-radius: 20px;
    background: linear-gradient(135deg, #f0abfc, #60a5fa 52%, #fb7185);
    color: #ffffff;
    font-weight: 950;
    font-size: 20px;
    box-shadow: 0 18px 36px rgba(217, 70, 239, 0.34);
    animation: creditSecretPulse 1600ms ease-in-out infinite;
}
@keyframes creditSecretPulse {
    0%, 100% { transform: scale(1); box-shadow: 0 18px 36px rgba(217, 70, 239, 0.34); }
    50% { transform: scale(1.06); box-shadow: 0 20px 44px rgba(96, 165, 250, 0.46); }
}
.credit-secret-confetti {
    position: fixed;
    inset: 0;
    overflow: hidden;
    pointer-events: none;
    z-index: 100001;
}
.credit-secret-confetti span {
    position: absolute;
    top: -18px;
    width: 9px;
    height: 14px;
    opacity: 0.95;
    animation-name: creditSecretConfettiDrop;
    animation-timing-function: cubic-bezier(.2,.65,.38,1);
    animation-fill-mode: forwards;
}
@keyframes creditSecretConfettiDrop {
    0% { transform: translate3d(0, -24px, 0) rotate(0deg); opacity: 0; }
    10% { opacity: 1; }
    100% { transform: translate3d(var(--credit-confetti-x, 28px), 108vh, 0) rotate(720deg); opacity: 0; }
}
.credit-secret-kicker {
    margin-top: 22px;
    color: #f0abfc;
    font-size: 12px;
    font-weight: 950;
    letter-spacing: 0.16em;
}
.credit-secret-title {
    margin-top: 8px;
    font-size: 26px;
    font-weight: 950;
    letter-spacing: 0.06em;
}
.credit-secret-divider {
    width: 64px;
    height: 3px;
    margin: 20px auto;
    border-radius: 999px;
    background: linear-gradient(90deg, transparent, #f0abfc, #60a5fa, #fb7185, transparent);
}
.credit-secret-name {
    font-size: 15px;
    font-weight: 900;
    letter-spacing: 0.08em;
}
.credit-secret-copy,
.credit-secret-version {
    margin-top: 9px;
    color: rgba(226, 232, 240, 0.78);
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 0.06em;
}
.credit-secret-note {
    margin: 18px auto 0;
    max-width: none;
    width: 100%;
    color: rgba(255, 255, 255, 0.84);
    font-size: 12px;
    font-weight: 700;
    line-height: 1.25;
    letter-spacing: 0.03em;
    white-space: nowrap;
}
@media (max-width: 520px) {
    #creditSecretOverlay {
        padding: 14px;
        align-items: center;
    }

    .credit-secret-card {
        width: 100%;
        border-radius: 18px;
        padding: 28px 18px 22px;
    }

    .credit-secret-close {
        top: 10px;
        right: 12px;
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .credit-secret-mark {
        width: 54px;
        height: 54px;
        border-radius: 17px;
        font-size: 18px;
    }

    .credit-secret-kicker {
        margin-top: 18px;
        font-size: 10px;
        letter-spacing: 0.12em;
    }

    .credit-secret-title {
        font-size: 20px;
        line-height: 1.15;
    }

    .credit-secret-name {
        font-size: 12px;
        line-height: 1.4;
        overflow-wrap: anywhere;
    }

    .credit-secret-copy,
    .credit-secret-version {
        font-size: 10.5px;
        line-height: 1.45;
    }

    .credit-secret-note {
        max-width: none;
        width: 100%;
        font-size: clamp(7.5px, 2.35vw, 10.5px);
        line-height: 1.25;
        letter-spacing: 0;
        white-space: nowrap;
    }
}
</style>

<script>
// Background Auto-Refresh Script (Hot Swap)
(function() {
    setInterval(() => {
        // Only run if the document is visible to save resources
        if (document.visibilityState !== 'visible') return;

        fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text())
            .then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                
                // 1. Update Notification Badge without disrupting dropdown state
                const newBadge = doc.getElementById('notifBellBadge');
                const oldBadge = document.getElementById('notifBellBadge');
                const bellBtn = document.getElementById('notifBellBtn');
                
                if (newBadge) {
                    if (oldBadge) {
                        if (oldBadge.textContent !== newBadge.textContent) {
                            oldBadge.outerHTML = newBadge.outerHTML;
                        }
                    } else if (bellBtn) {
                        bellBtn.insertAdjacentHTML('beforeend', newBadge.outerHTML);
                    }
                } else if (oldBadge) {
                    oldBadge.remove();
                }
                
                // 2. Update all data-auto-refresh elements by ID
                document.querySelectorAll('[data-auto-refresh="true"]').forEach(el => {
                    if (el.id) {
                        const newEl = doc.getElementById(el.id);
                        if (newEl) {
                            el.innerHTML = newEl.innerHTML;
                        }
                    }
                });
            })
            .catch(e => console.error('Auto-refresh failed:', e));
    }, 3000);
})();
</script>
</body>
</html>
