<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ config('app.name', 'HR Admin System') }}</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}?v={{ time() }}">
@yield('styles')
</head>
<body class="app-body">
<aside class="sidebar" id="sidebar">
    <button class="sidebar-close-btn" onclick="toggleSidebar()" aria-label="Close menu">✕</button>
    <a class="sidebar-brand" href="{{ Auth::check() ? url('/dashboard') : route('login') }}" style="text-decoration:none;cursor:pointer;" title="Go to Dashboard">
        <div class="brand-icon-sm">
            <img src="{{ asset('assets/images/logo.png') }}" alt="FJB" style="width:26px;height:26px;object-fit:contain;">
        </div>
        <span>HR Admin</span>
    </a>
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

        @if(Auth::user()->isAdmin() || Auth::user()->isCeo())
        <div class="nav-divider"></div>
        <a href="{{ url('/master-data') }}" class="nav-item {{ request()->is('master-data*') ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
            Master Data
        </a>
        @if(Auth::user()->isAdminIT() || Auth::user()->isCeo())
        <a href="{{ url('/users') }}" class="nav-item {{ request()->is('users*') ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 10-16 0"/></svg>
            User Accounts
        </a>
        <a href="{{ route('audit-log.index') }}" class="nav-item {{ request()->is('audit-log*') ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            Audit Log
        </a>
        @endif
        @endif
        @if(Auth::user()->isHrUser())
        <a href="{{ route('account.security') }}" class="nav-item {{ request()->is('account/security') ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            Account Security
            @if(!Auth::user()->hasTotpSetup())
                <span class="badge-count" style="background:#f59e0b;">!</span>
            @endif
        </a>
        @endif
        <div class="nav-divider" style="border-top:1px solid rgba(255,255,255,.08);margin:12px 0 8px"></div>
        <a href="{{ route('it.dashboard') }}" class="nav-item">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            IT System
        </a>
        <a href="{{ route('wt.admin.requests.create.shared') }}" class="nav-item">
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
                Sign In
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

        <div class="app-footer" style="text-align: center; margin-top: 3rem; padding: 2rem 0; border-top: 1px solid var(--border, rgba(0,0,0,0.05)); clear: both;">
            <div style="margin-bottom: 0.5rem;">
                <img src="{{ asset('assets/images/footer.jpg') }}" alt="IT Logo" style="max-height: 45px; width: auto; object-fit: contain;">
            </div>
            <div id="eggFooterText" style="font-size: 0.85rem; color: var(--muted, #64748b); font-weight: 500; cursor: default; user-select: none;">
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
<script src="{{ asset('assets/js/app.js') }}"></script>

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
    const items = document.querySelectorAll('#notifList .notif-item.unread');
    if (!items.length) return;

    const ids = Array.from(items).map(el => parseInt(el.dataset.notifId)).filter(Boolean);
    const types = new Set(Array.from(items).map(el => el.dataset.notifType));

    items.forEach(el => el.classList.remove('unread'));
    ids.forEach(id => { notifReadIds.add(id); notifSeenIds.add(id); });

    document.getElementById('notifBellBadge')?.remove();
    types.forEach(type => document.getElementById('nav-badge-' + type)?.remove());

    fetch('{{ url("notifications/mark-read") }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: JSON.stringify({ids})
    });
}
</script>
<script>
function liveSearch(form, resultId, delay) {
    var el = document.getElementById(resultId);
    if (!el) return;
    var t;
    function run() {
        clearTimeout(t);
        t = setTimeout(function() {
            var p = new URLSearchParams();
            new FormData(form).forEach(function(v, k) { p.append(k, v); });
            var url = form.action + '?' + p.toString();
            el.style.transition = 'opacity .15s';
            el.style.opacity = '.45';
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function(r) { return r.text(); })
                .then(function(html) {
                    var doc = new DOMParser().parseFromString(html, 'text/html');
                    var fresh = doc.getElementById(resultId);
                    if (fresh) { el.innerHTML = fresh.innerHTML; history.replaceState(null, '', url); }
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
@yield('scripts')
<script>
(function() {
    var content = document.querySelector('.content-area');
    document.querySelectorAll('.nav-item, .nav-child').forEach(function(link) {
        link.addEventListener('click', function(e) {
            var href = this.getAttribute('href');
            if (!href || href === '#' || href.startsWith('javascript') || this.closest('form')) return;
            e.preventDefault();
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

{{-- ╔═══════════════════════════════════════════════════════════════╗
     ║  Easter Egg — Creator credit                                  ║
     ║  Triggers: Konami code (↑↑↓↓←→←→ B A)  ·  footer clicked 5×   ║
     ╚═══════════════════════════════════════════════════════════════╝ --}}
<div class="egg-overlay" id="eggOverlay" aria-hidden="true" onclick="if(event.target===this)closeEgg()">
    <div class="egg-card" role="dialog" aria-label="Creator credit">
        <button class="egg-close" onclick="closeEgg()" aria-label="Close">&times;</button>
        <div class="egg-confetti" id="eggConfetti"></div>
        <div class="egg-badge">
            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
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
    border-radius:18px; color:#0f223b;
    background:linear-gradient(135deg,#38bdf8,#7dd3fc);
    box-shadow:0 10px 26px -8px rgba(56,189,248,.8);
    animation:eggSpin 6s linear infinite;
}
@keyframes eggSpin{ to{ transform:rotate(360deg) } }
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
    const colors   = ['#38bdf8','#7dd3fc','#fcd34d','#fff','#0284c7'];
    let open = false;

    window.closeEgg = function(){ overlay.classList.remove('show'); overlay.setAttribute('aria-hidden','true'); open = false; };

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
</body>
</html>
