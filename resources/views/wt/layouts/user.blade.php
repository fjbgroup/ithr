<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>WT System</title>
@include('partials.favicons')
<script>
  (function () {
    try {
      var savedTheme = localStorage.getItem('fjb-theme') || localStorage.getItem('color-theme') || localStorage.getItem('theme');
      var theme = savedTheme === 'dark' || savedTheme === 'light' ? savedTheme : 'light';
      localStorage.setItem('fjb-theme', theme);
      localStorage.setItem('color-theme', theme);
      localStorage.setItem('theme', theme);
      document.documentElement.classList.toggle('dark', theme === 'dark');
      document.documentElement.setAttribute('data-theme', theme);
      document.documentElement.style.colorScheme = theme;
    } catch (error) {
      document.documentElement.setAttribute('data-theme', 'light');
      document.documentElement.style.colorScheme = 'light';
    }
  })();
</script>
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
<style>
/* wtsystem.css is the single source of truth */
</style>
@stack('styles')
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
    $effectiveRoleLabel = match ($effectiveRole) {
        'admin' => 'EXECUTIVE',
        default => strtoupper(str_replace('_', ' ', $effectiveRole)),
    };
    $accountRoleLabel = $actualRole === 'admin_it' ? 'ICT' : $effectiveRoleLabel;

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
    <div class="nav-section-label" style="margin-top:4px">Guides</div>
    <a href="{{ route('wt.user.manual') }}" class="nav-link has-info {{ request()->routeIs('wt.user.manual') ? 'sidebar-active' : '' }}">
      <i class="fa-solid fa-book-open" style="width:20px;text-align:center;flex-shrink:0"></i> <span>User Manual</span>
      @include('wt.partials.sidebar-info', ['text' => 'View WT System guidance for ICT and executive users.'])
    </a>

    <div class="nav-section-label" style="margin-top:4px">Asset Interactive</div>
    <a href="{{ route('wt.user.returns.create') }}" class="nav-link has-info {{ request()->routeIs('wt.user.returns.*') ? 'sidebar-active' : '' }}">
      <i class="fa-solid fa-rotate-left" style="width:20px;text-align:center;flex-shrink:0"></i> <span>Return Unit</span>
      @include('wt.partials.sidebar-info', ['text' => 'Submit a return request when a walkie unit is no longer being used.'])
    </a>
    <a href="{{ route('wt.user.damages.create') }}" class="nav-link has-info {{ request()->routeIs('wt.user.damages.*') ? 'sidebar-active' : '' }}">
      <i class="fa-solid fa-triangle-exclamation" style="width:20px;text-align:center;flex-shrink:0"></i> <span>Report Faulty</span>
      @include('wt.partials.sidebar-info', ['text' => 'Report faulty, damaged, missing, or problem walkie talkie units.'])
    </a>
    <a href="{{ route('wt.user.requests.status') }}" class="nav-link has-info {{ request()->routeIs('wt.user.requests.*') ? 'sidebar-active' : '' }}">
      <i class="fa-solid fa-list-ul" style="width:20px;text-align:center;flex-shrink:0"></i> <span>Request Status</span>
      @include('wt.partials.sidebar-info', ['text' => 'Check the latest status of your walkie talkie requests.'])
    </a>

    <div class="nav-section-label">My Account</div>
    <a href="{{ route('wt.user.profile') }}" class="nav-link has-info {{ request()->routeIs('wt.user.profile*') ? 'sidebar-active' : '' }}">
      <i class="fa-solid fa-user-circle" style="width:20px;text-align:center;flex-shrink:0"></i> <span>My Profile</span>
      @include('wt.partials.sidebar-info', ['text' => 'View and update your account profile information.'])
    </a>
    <a href="{{ route('wt.user.policies') }}" class="nav-link has-info {{ request()->routeIs('wt.user.policies') ? 'sidebar-active' : '' }}">
      <i class="fa-solid fa-table-list" style="width:20px;text-align:center;flex-shrink:0"></i> <span>Role Matrix</span>
      @include('wt.partials.sidebar-info', ['text' => 'View WT System access permissions for ICT and executive users.'])
    </a>

    <div style="border-top:1px solid rgba(255,255,255,.08);margin:12px 0 8px"></div>
    <a href="{{ route('home') }}" class="nav-link" title="Back to Portal">
      <i class="fas fa-th-large" style="width:20px;text-align:center;flex-shrink:0"></i> <span>Back to Portal</span>
    </a>
  </nav>

  <div class="sidebar-footer">
    <div class="user-card">
      <div class="user-avatar">{{ strtoupper(substr(Auth::guard('wt')->user()->username ?? 'U', 0, 1)) }}</div>
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
        <a href="{{ route('wt.user.dashboard') }}" style="color:var(--muted);text-decoration:none">Dashboard</a>
        &rsaquo; {{ $topbarTitle }}
      </div>
    </div>
    <div class="topbar-right">

      {{-- Theme toggle --}}
      <button id="theme-toggle" type="button" class="topbar-action-btn" title="Toggle dark mode">
        <i id="theme-toggle-dark-icon" class="hidden fas fa-moon" style="font-size:16px"></i>
        <i id="theme-toggle-light-icon" class="hidden fas fa-sun" style="font-size:16px;color:#f59e0b"></i>
      </button>

      {{-- User badge --}}
      <a href="{{ route('wt.user.profile') }}" class="topbar-user" title="My profile">
        <span class="topbar-role-badge">{{ $accountRoleLabel }}</span>
        <span class="topbar-user-name">{{ Auth::guard('wt')->user()->username ?? 'User' }}</span>
      </a>

      {{-- Logout --}}
      <form id="logout-form" action="{{ route('wt.logout') }}" method="POST" style="display:none">@csrf</form>
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
      </style>
    </div>
    <div style="text-align:center;margin-top:3rem;padding:2rem 0;border-top:1px solid rgba(255,255,255,.08);clear:both;">
      <div style="margin-bottom:.5rem;">
        <img src="{{ asset('assets/images/footer.jpg') }}" alt="FJB" style="max-height:45px;width:auto;object-fit:contain;display:block;margin:0 auto;">
      </div>
      <div data-credit-secret style="font-size:.85rem;color:var(--muted,#64748b);font-weight:500;cursor:pointer;user-select:none;">Develop by IT team</div>
    </div>
  </div>
</div>

@include('wt.partials.credit-secret')

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
// ── THEME ──
const DARK_VARS = {
  '--body-bg':'#0f172a','--surface':'#1e293b','--border':'#334155','--text':'#e2e8f0','--muted':'#94a3b8',
  '--table-hover':'rgba(255,255,255,.04)','--form-input-bg':'#0f172a','--form-input-border':'#334155','--form-input-color':'#e2e8f0',
  '--table-head-bg':'#111827','--table-head-color':'#94a3b8',
};
const LIGHT_VARS = {
  '--body-bg':'#f0f4f8','--surface':'#ffffff','--border':'#e2e8f0','--text':'#1e293b','--muted':'#64748b',
  '--table-hover':'#f0f9ff','--form-input-bg':'#ffffff','--form-input-border':'#e2e8f0','--form-input-color':'#1e293b',
  '--table-head-bg':'#f8fafc','--table-head-color':'#475569',
};

function applyTheme(dark) {
  const vars = dark ? DARK_VARS : LIGHT_VARS;
  for (const [k,v] of Object.entries(vars)) document.documentElement.style.setProperty(k, v);
  const darkIcon = document.getElementById('theme-toggle-dark-icon');
  const lightIcon = document.getElementById('theme-toggle-light-icon');
  if (darkIcon && lightIcon) {
    darkIcon.style.display = dark ? 'none' : 'inline-block';
    lightIcon.style.display = dark ? 'inline-block' : 'none';
  }
  document.documentElement.setAttribute('data-theme', dark ? 'dark' : 'light');
  document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
  document.documentElement.classList.toggle('dark', dark);
  document.body.style.backgroundColor = vars['--body-bg'];
  document.body.style.color = vars['--text'];
}

const themeToggleBtn = document.getElementById('theme-toggle');
if (themeToggleBtn) {
  themeToggleBtn.addEventListener('click', function() {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const next = isDark ? 'light' : 'dark';
    localStorage.setItem('fjb-theme', next);
    localStorage.setItem('color-theme', next);
    localStorage.setItem('theme', next);
    applyTheme(next === 'dark');
  });
}

function resolveSavedTheme() {
  const fjbTheme = localStorage.getItem('fjb-theme');
  const colorTheme = localStorage.getItem('color-theme');
  const legacyTheme = localStorage.getItem('theme');
  const t = [fjbTheme, colorTheme, legacyTheme].find(value => value === 'dark' || value === 'light') || 'light';
  localStorage.setItem('fjb-theme', t);
  localStorage.setItem('color-theme', t);
  localStorage.setItem('theme', t);
  return t;
}

(function(){
  const t = resolveSavedTheme();
  applyTheme(t === 'dark');
})();

// ── SIDEBAR ──
function toggleMobileSidebar() {
  const sb = document.getElementById('sidebar');
  const ov = document.getElementById('mobileSidebarOverlay');
  if (sb.classList.contains('open')) {
    sb.classList.remove('open'); ov.style.display = 'none'; document.body.style.overflow = '';
  } else {
    sb.classList.add('open'); ov.style.display = 'block'; document.body.style.overflow = 'hidden';
  }
}
function closeMobileSidebar() {
  const sb = document.getElementById('sidebar'); const ov = document.getElementById('mobileSidebarOverlay');
  if (sb) sb.classList.remove('open'); if (ov) ov.style.display = 'none'; document.body.style.overflow = '';
}

// ── LOGOUT ──
function handleLogout() { openLogoutModal(); }
function openLogoutModal() {
  const m = document.getElementById('logoutModal'); if (!m) return;
  m.classList.add('active'); m.setAttribute('aria-hidden','false'); document.body.style.overflow = 'hidden';
}
function closeLogoutModal() {
  const m = document.getElementById('logoutModal'); if (!m) return;
  m.classList.remove('active'); m.setAttribute('aria-hidden','true'); document.body.style.overflow = '';
}
function submitLogout() {
  document.getElementById('main-body').style.opacity = '0';
  setTimeout(function() { document.getElementById('logout-form').submit(); }, 250);
}

// ── AUTO UPPERCASE ──
function bindAutoUppercase(root) {
  root = root || document;
  root.querySelectorAll('input[type="text"], input[type="search"], textarea').forEach(function(field) {
    if (field.name === 'username' || field.id === 'edit_username' || field.dataset.preserveCase === 'true') return;
    if (field.dataset.uppercaseBound === 'true') return;
    field.dataset.uppercaseBound = 'true';
    field.addEventListener('input', function() {
      var s = this.selectionStart, e = this.selectionEnd;
      this.value = this.value.toUpperCase();
      if (typeof s === 'number') this.setSelectionRange(s, e);
    });
  });
}

// ── SIDEBAR INFO POPOVERS ──
function closeSidebarInfoPopovers() {
  document.querySelectorAll('.nav-info-popover').forEach(function(item) {
    item.classList.add('hidden'); item.style.left = ''; item.style.top = ''; item.style.visibility = '';
  });
  document.querySelectorAll('.nav-info-btn').forEach(function(btn) {
    btn.classList.remove('is-open'); btn.setAttribute('aria-expanded','false');
  });
}
function positionSidebarInfoPopover(button, popover) {
  var spacing = 10, vp = 12, rect = button.getBoundingClientRect();
  var sidebar = button.closest('.sidebar, aside, nav');
  var sidebarRect = sidebar ? sidebar.getBoundingClientRect() : null;
  popover.classList.remove('hidden'); popover.style.visibility = 'hidden';
  var maxW = Math.min(240, window.innerWidth - vp * 2);
  popover.style.width = Math.max(190, maxW) + 'px';
  var left = (sidebarRect ? sidebarRect.right : rect.right) + spacing, pw = popover.offsetWidth;
  if (left + pw > window.innerWidth - vp) left = rect.left - pw - spacing;
  left = Math.max(vp, Math.min(left, window.innerWidth - vp - pw));
  var top = rect.top + rect.height / 2 - popover.offsetHeight / 2;
  top = Math.max(vp, Math.min(top, window.innerHeight - vp - popover.offsetHeight));
  popover.style.left = left + 'px'; popover.style.top = top + 'px'; popover.style.visibility = 'visible';
}

document.addEventListener('DOMContentLoaded', function() {
  // Notification toggle
  const notifToggle = document.getElementById('notificationToggle');
  const notifDropdown = document.getElementById('notificationDropdown');
  if (notifToggle && notifDropdown) {
    notifToggle.addEventListener('click', function(e) { e.stopPropagation(); notifDropdown.classList.toggle('hidden'); });
    document.addEventListener('click', function(e) {
      if (!notifDropdown.contains(e.target) && !notifToggle.contains(e.target)) notifDropdown.classList.add('hidden');
    });
  }
  // Logout modal
  const logoutModal = document.getElementById('logoutModal');
  if (logoutModal) logoutModal.addEventListener('click', function(e) { if (e.target === logoutModal) closeLogoutModal(); });
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
    const infoBtn = event.target.closest('.nav-info-btn');
    if (infoBtn) {
      event.preventDefault(); event.stopPropagation();
      const popover = infoBtn.parentElement && infoBtn.parentElement.querySelector('.nav-info-popover');
      const willOpen = popover && popover.classList.contains('hidden');
      closeSidebarInfoPopovers();
      if (popover && willOpen) {
        positionSidebarInfoPopover(infoBtn, popover);
        infoBtn.classList.add('is-open'); infoBtn.setAttribute('aria-expanded','true');
      }
      return;
    }
    if (!event.target.closest('.nav-info-btn') && !event.target.closest('.nav-info-popover')) closeSidebarInfoPopovers();
  }, true);
  // Keyboard
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') { closeLogoutModal(); closeSidebarInfoPopovers(); closeMobileSidebar(); }
  });
  window.addEventListener('resize', closeSidebarInfoPopovers);
  document.addEventListener('scroll', closeSidebarInfoPopovers, true);
  // Flash alerts
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

@stack('scripts')

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
    }, 30000);
})();
</script>
</body>
</html>
