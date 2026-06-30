<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Dashboard') — FJB Inventory System</title>
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
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="{{ asset('it-assets/css/style.css') }}" rel="stylesheet">
<style>
:root {
  --sidebar-bg:        #f8fafc;
  --sidebar-hover:     #f1f5f9;
  --sidebar-active-bg: #e0f2fe;
  --sidebar-text:      #334155;
  --sidebar-head:      #1e293b;
  --sidebar-border:    #e2e8f0;
  --sidebar-muted:     #64748b;
  --sidebar-active-tx: #0284c7;
  --sidebar-w:         250px;
  --accent:            #0284c7;
  --accent-h:          #0369a1;
  --accent-rgb:        2,132,199;
  --navy:              #142b47;
  --navy-mid:          #254a78;
  --sky:               #38bdf8;
  --sky-dark:          #0284c7;
  --body-bg:           #f1f5f9;
  --surface:           #ffffff;
  --border:            #e2e8f0;
  --text:              #1e293b;
  --muted:             #64748b;
  --red:               #ef4444;
  --green:             #22c55e;
  --yellow:            #f59e0b;
  --blue:              #3b82f6;
  --shadow:            0 1px 3px rgba(0,0,0,.08), 0 4px 16px rgba(0,0,0,.06);
  --shadow-lg:         0 8px 30px rgba(0,0,0,.12);
  --table-hover:       #f0f9ff;
}
/* Dark mode — keyed off the `dark` class the early head-script sets synchronously,
   so the sidebar (and the rest of the shell) renders dark at first paint instead of
   relying solely on the JS theme pass. Values mirror DARK_VARS below. */
html.dark{
  --sidebar-bg:        #1a2235;
  --sidebar-hover:     #263042;
  --sidebar-active-bg: rgba(56,189,248,.14);
  --sidebar-text:      #cbd5e1;
  --sidebar-head:      #f1f5f9;
  --sidebar-border:    #374151;
  --sidebar-muted:     #94a3b8;
  --sidebar-active-tx: #38bdf8;
  --bg:                #111827;
  --body-bg:           #111827;
  --white:             #1f2937;
  --surface:           #1f2937;
  --surface2:          #263042;
  --border:            #374151;
  --text:              #d1d5db;
  --muted:             #6b7280;
  --table-hover:       rgba(255,255,255,.04);
  --form-input-bg:     #263042;
  --form-input-border: #374151;
  --form-input-color:  #d1d5db;
  --table-head-bg:     #1a2235;
  --table-head-color:  #9ca3af;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{background:var(--body-bg);color:var(--text);font-family:'Inter',sans-serif;font-size:14px;min-height:100vh}

/* ── SIDEBAR ── */
.sidebar{
  position:fixed;top:0;left:0;width:var(--sidebar-w);height:100vh;
  background:var(--sidebar-bg);display:flex;flex-direction:column;z-index:100;
  border-right:1px solid var(--sidebar-border);
  transition:width .3s ease;overflow:hidden;
}
.sidebar-brand{
  padding:20px 20px 16px;border-bottom:1px solid var(--sidebar-border);
  display:flex;align-items:center;gap:12px;
}
.sidebar-brand img{width:42px;height:42px;object-fit:contain;background:transparent}
.brand-name{font-family:'Inter',sans-serif;font-size:13px;font-weight:700;color:var(--sidebar-head);
  text-transform:uppercase;letter-spacing:.05em;line-height:1.3}
.brand-name span{color:var(--sidebar-muted);display:block;font-size:10px;letter-spacing:.1em;font-weight:700}

.sidebar-nav{flex:1;overflow-y:auto;padding:16px 12px;scrollbar-width:thin;scrollbar-color:rgba(15,23,42,.15) transparent}
.sidebar-nav::-webkit-scrollbar{width:3px}
.sidebar-nav::-webkit-scrollbar-track{background:transparent}
.sidebar-nav::-webkit-scrollbar-thumb{background:rgba(15,23,42,.15);border-radius:99px}
.sidebar-nav::-webkit-scrollbar-thumb:hover{background:rgba(2,132,199,.4)}
.sidebar-nav:hover::-webkit-scrollbar-thumb{background:rgba(15,23,42,.22)}
.nav-section-label{font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.12em;
  color:var(--sidebar-muted);padding:0 8px;margin:20px 0 6px}
.nav-section-label:first-child{margin-top:4px}

.nav-link{
  display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:8px;
  color:var(--sidebar-text);text-decoration:none;font-size:13.5px;font-weight:500;
  transition:all .15s;margin-bottom:2px;
}
.nav-link i{font-size:16px;width:20px;text-align:center;flex-shrink:0}
.nav-link:hover{background:var(--sidebar-hover);color:var(--sidebar-head)}
/* collapsible section buttons follow the sidebar theme (override inline color) */
.sidebar-nav>div>button{color:var(--sidebar-text)!important}
.sidebar-nav>div>button.sb-active{color:var(--sidebar-active-tx)!important}
.sidebar-nav>div>button:hover{background:var(--sidebar-hover)!important}
.nav-link.active{background:var(--sidebar-active-bg);color:var(--sidebar-active-tx);font-weight:600}
.nav-link .badge-count, button .badge-count{
  margin-left:auto;background:var(--red);color:#fff;
  border-radius:20px;padding:1px 7px;font-size:10px;font-weight:700;flex-shrink:0;
}
button .badge-count{ margin-left:0; }

.sidebar-footer{padding:14px 12px;border-top:1px solid var(--sidebar-border)}
.user-card{
  display:flex;align-items:center;gap:10px;padding:10px 12px;
  border-radius:8px;background:rgba(15,23,42,.05);
  margin-bottom:10px;text-decoration:none;transition:background .15s;cursor:pointer;
}
.user-card:hover{background:rgba(15,23,42,.09)}
.user-avatar{
  width:34px;height:34px;border-radius:50%;background:var(--accent);
  display:flex;align-items:center;justify-content:center;
  font-family:'Inter',sans-serif;font-size:13px;font-weight:700;color:#fff;flex-shrink:0;
}
.user-info{min-width:0;flex:1}
.user-name{font-size:13px;font-weight:500;color:var(--sidebar-head);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.user-role{font-size:10px;text-transform:uppercase;letter-spacing:.06em;font-weight:600}
.btn-logout{
  display:flex;align-items:center;gap:8px;width:100%;padding:9px 12px;
  background:rgba(15,23,42,.05);border:none;border-radius:8px;
  color:var(--sidebar-muted);font-size:13px;cursor:pointer;text-decoration:none;
  transition:all .15s;font-family:'Inter',sans-serif;
}
.btn-logout:hover{background:rgba(239,68,68,.15);color:#ef4444}

/* ── MAIN ── */
.main-content{margin-left:var(--sidebar-w);min-height:100vh;display:flex;flex-direction:column;padding-top:60px}
.topbar{
  background:var(--surface);border-bottom:1px solid var(--border);
  padding:0 28px;height:60px;display:flex;align-items:center;gap:16px;
  position:fixed;top:0;left:var(--sidebar-w);right:0;z-index:50;
}
.topbar-left{flex:1}
.topbar-title{font-family:'Inter',sans-serif;font-size:16px;font-weight:700;color:var(--text)}
.topbar-breadcrumb{font-size:11px;color:var(--muted);margin-top:1px}
.topbar-right{display:flex;align-items:center;gap:12px}
.topbar-user{
  display:flex;align-items:center;gap:8px;
  background:var(--body-bg);border:1px solid var(--border);
  border-radius:8px;padding:6px 12px;
  text-decoration:none;transition:border-color .15s,box-shadow .15s;
}
.topbar-user:hover{border-color:var(--accent);box-shadow:0 0 0 3px rgba(var(--accent-rgb),.1)}
.topbar-user-name{font-size:13px;font-weight:600;color:var(--text)}
.topbar-role-badge{
  background:#dbeafe;color:#1d4ed8;
  border-radius:5px;padding:2px 8px;font-size:10px;font-weight:700;text-transform:uppercase;
}
.theme-toggle{
  width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;
  background:var(--body-bg);border:1px solid var(--border);
  color:var(--muted);cursor:pointer;font-size:16px;transition:all .15s;
}
.theme-toggle:hover{border-color:var(--accent);color:var(--accent)}
.page-body{padding:0 28px;flex:1}

/* ── STAT CARDS ── */
.stat-card{
  background:var(--surface);border:1px solid var(--border);
  border-radius:12px;padding:20px 22px;
  border-left:4px solid var(--accent);
  box-shadow:0 1px 3px rgba(0,0,0,.08),0 4px 16px rgba(0,0,0,.06);
  transition:box-shadow .2s,transform .2s;cursor:default;
}
.stat-card:hover{box-shadow:0 8px 30px rgba(0,0,0,.12);transform:translateY(-2px)}
.stat-icon{
  width:44px;height:44px;border-radius:10px;
  display:flex;align-items:center;justify-content:center;font-size:20px;margin-bottom:14px;
}
.stat-value{font-family:'Inter',sans-serif;font-size:30px;font-weight:800;color:var(--text);line-height:1}
.stat-label{font-size:12px;color:var(--muted);margin-top:4px;font-weight:500}

/* ── TABLE CARD ── */
.table-card{background:var(--surface);border:1px solid var(--border);border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.08),0 4px 16px rgba(0,0,0,.06)}
.table-card-header{
  padding:16px 20px;border-bottom:1px solid var(--border);
  display:flex;align-items:center;gap:12px;background:var(--surface);
}
.table-card-title{font-family:'Inter',sans-serif;font-size:14px;font-weight:700;color:var(--text);flex:1}
.table{color:var(--text);margin:0}
.table thead th{
  background:var(--table-head-bg, #e2e8f0) !important;
  border-color:var(--border) !important;
  color:var(--table-head-color, #475569);font-size:11px;font-weight:700;
  text-transform:uppercase;letter-spacing:.08em;
  padding:12px 16px;white-space:nowrap;
}
.table tbody tr{background:var(--surface) !important;color:var(--text) !important}
.table tbody tr:hover{background:var(--table-hover) !important}
.table tbody td{
  border-color:var(--border) !important;padding:12px 16px;
  vertical-align:middle;color:var(--text) !important;background:transparent !important;
}
.table tbody td span:not(.badge-status),.table tbody td div,.table tbody td a{color:var(--text) !important}
.table tbody td code{color:var(--accent) !important}

/* ── BADGES ── */
.badge-status{padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em}
.bs-active  {background:rgba(34,197,94,.12);color:#16a34a}
.bs-disposed{background:rgba(239,68,68,.12);color:#dc2626}
.bs-pending {background:rgba(245,158,11,.12);color:#d97706}
.bs-repair  {background:rgba(59,130,246,.12);color:#2563eb}

/* ── FORM ── */
.form-card{background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:24px;box-shadow:0 1px 3px rgba(0,0,0,.08),0 4px 16px rgba(0,0,0,.06)}
.form-label{color:var(--muted);font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px}
.form-control,.form-select{
  background:var(--form-input-bg, #fff) !important;
  border:1.5px solid var(--form-input-border, var(--border)) !important;
  color:var(--form-input-color, var(--text)) !important;
  border-radius:8px;padding:9px 13px;
  font-family:'Inter',sans-serif;font-size:14px;transition:border-color .2s;
}
.form-control:focus,.form-select:focus{
  border-color:var(--sky-dark,var(--accent)) !important;
  box-shadow:0 0 0 3px rgba(56,189,248,.15) !important;outline:none;
}
textarea.form-control{min-height:90px;resize:vertical}
.form-control::placeholder{color:var(--muted);opacity:.7}

/* ── BUTTONS ── */
.btn-primary-custom{
  background:var(--navy,#142b47);color:#fff;border:none;border-radius:8px;
  padding:9px 20px;font-family:'Inter',sans-serif;font-size:13px;font-weight:600;
  cursor:pointer;transition:all .15s;text-decoration:none;
  display:inline-flex;align-items:center;gap:7px;
}
.btn-primary-custom:hover{background:var(--navy-mid,#254a78);color:#fff;transform:translateY(-1px)}
.btn-secondary-custom{
  background:#fff;color:var(--text);border:1.5px solid var(--border);border-radius:8px;
  padding:9px 18px;font-size:13px;font-weight:500;cursor:pointer;
  transition:all .15s;text-decoration:none;display:inline-flex;align-items:center;gap:7px;
  font-family:'Inter',sans-serif;
}
.btn-secondary-custom:hover{border-color:var(--navy,#142b47);color:var(--navy,#142b47)}
.btn-icon{
  width:30px;height:30px;border-radius:6px;
  display:inline-flex;align-items:center;justify-content:center;
  font-size:14px;border:none;cursor:pointer;transition:all .15s;text-decoration:none;
}
.btn-edit      {background:rgba(59,130,246,.1);color:#2563eb}
.btn-edit:hover{background:rgba(59,130,246,.2);color:#2563eb}
.btn-delete      {background:rgba(239,68,68,.1);color:#dc2626}
.btn-delete:hover{background:rgba(239,68,68,.2);color:#dc2626}
.btn-view      {background:rgba(var(--accent-rgb),.1);color:var(--accent)}
.btn-view:hover{background:rgba(var(--accent-rgb),.2);color:var(--accent)}

/* ── ALERTS ── */
.alert-success-custom{
  background:#dcfce7;border:1px solid #bbf7d0;
  color:#166534;border-radius:8px;padding:12px 16px;
  margin-bottom:20px;display:flex;align-items:center;gap:8px;font-size:13px;
}
.alert-danger-custom{
  background:#fee2e2;border:1px solid #fecaca;
  color:#991b1b;border-radius:8px;padding:12px 16px;
  margin-bottom:20px;display:flex;align-items:center;gap:8px;font-size:13px;
}

.dataTables_wrapper .dataTables_filter input,
.dataTables_wrapper .dataTables_length select{
  background:var(--form-input-bg, #fff) !important;
  border:1.5px solid var(--form-input-border, var(--border)) !important;
  color:var(--form-input-color, var(--text)) !important;
  border-radius:6px;padding:5px 10px;
}
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_filter label,
.dataTables_wrapper .dataTables_length label{color:var(--muted)}
.dataTables_wrapper .dataTables_paginate .paginate_button{background:transparent !important;border:none !important;margin:0 !important;padding:0 !important;}
.dataTables_wrapper .pagination{gap:3px;flex-wrap:wrap}
.dataTables_wrapper .pagination .page-item .page-link{
  font-size:12px !important;font-weight:600 !important;font-family:'Inter',sans-serif !important;
  padding:4px 10px !important;line-height:1.5 !important;
  border-radius:6px !important;border:1px solid var(--border) !important;
  background:transparent !important;color:var(--muted) !important;transition:all .15s;
}
.dataTables_wrapper .pagination .page-item.active .page-link{background:var(--navy,#142b47) !important;border-color:var(--navy,#142b47) !important;color:#fff !important;}
.dataTables_wrapper .pagination .page-item .page-link:hover{background:#f1f5f9 !important;color:var(--text) !important;}
.dataTables_wrapper .dataTables_info{font-size:13px;color:var(--muted);font-family:'Inter',sans-serif;padding-top:6px;}
table.dataTable tbody tr,
table.dataTable tbody tr.odd,
table.dataTable tbody tr.even{background-color:var(--surface) !important;color:var(--text) !important}
table.dataTable tbody tr:hover,
table.dataTable tbody tr.odd:hover,
table.dataTable tbody tr.even:hover{background-color:var(--table-hover) !important}
table.dataTable tbody td{color:var(--text) !important}
code{color:var(--accent);background:rgba(2,132,199,.08);padding:1px 5px;border-radius:4px;font-size:12px}

/* ── TOPBAR TOGGLE — mobile only ── */
.sidebar-toggle{display:none;align-items:center;background:none;border:1.5px solid var(--border);
  border-radius:7px;color:var(--text);padding:6px 10px;cursor:pointer;font-size:18px;font-family:'Inter',sans-serif;
  transition:border-color .15s,color .15s}
.sidebar-toggle:hover{border-color:var(--accent);color:var(--accent)}

/* ── SIDEBAR CLOSE BUTTON (inside sidebar, expanded state only) ── */
.sb-close-btn{
  flex-shrink:0;margin-left:auto;
  width:28px;height:28px;
  background:rgba(15,23,42,.06);border:none;border-radius:6px;
  color:var(--sidebar-muted);cursor:pointer;font-size:15px;
  display:flex;align-items:center;justify-content:center;
  transition:background .15s,color .15s,font-size .15s;
}
.sb-close-btn:hover{background:rgba(15,23,42,.12);color:#1e293b;font-size:12px}
html.sidebar-collapsed .sb-close-btn{display:none!important}

/* ── LOGO AS OPEN BUTTON (collapsed state) ── */
.sb-open-icon{display:none}
html.sidebar-collapsed .sb-logo-btn{cursor:pointer!important;position:relative!important}
html.sidebar-collapsed .sb-logo-btn img{transition:opacity .15s}
html.sidebar-collapsed .sb-logo-btn .sb-open-icon{
  position:absolute;inset:0;display:flex;align-items:center;justify-content:center;
  opacity:0;transition:opacity .15s;font-size:16px;color:#1e2433;pointer-events:none
}
html.sidebar-collapsed .sb-logo-btn:hover img{opacity:0}
html.sidebar-collapsed .sb-logo-btn:hover .sb-open-icon{opacity:1}

/* ── COLLAPSE TRANSITIONS ── */
.main-content{transition:margin-left .3s ease}
.topbar{transition:left .3s ease}

/* ── DESKTOP COLLAPSED (icon rail) ── */
html.sidebar-collapsed .sidebar{width:64px}
html.sidebar-collapsed .main-content{margin-left:64px}
html.sidebar-collapsed .topbar{left:64px}

/* ── ICON RAIL STYLES ── */
html.sidebar-collapsed .sidebar-nav{padding:16px 0}
html.sidebar-collapsed .sidebar-brand{padding:14px 8px;justify-content:center}
html.sidebar-collapsed .sidebar-brand .brand-name{display:none!important}
html.sidebar-collapsed .sidebar-brand .sb-logo-btn{display:flex!important}
html.sidebar-collapsed .nav-section-label{display:none}
html.sidebar-collapsed .sidebar-nav .nav-link{font-size:0!important;padding:10px 0!important;justify-content:center!important;gap:0!important;border-radius:0!important}
html.sidebar-collapsed .sidebar-nav .nav-link i{font-size:16px!important;width:auto!important;margin:0!important;text-align:center}
html.sidebar-collapsed .sidebar-nav .nav-link .badge-count{display:none!important}
html.sidebar-collapsed .sidebar-nav>div>button{padding:10px 0!important;justify-content:center!important;gap:0!important;border-radius:0!important;background:none!important}
html.sidebar-collapsed .sidebar-nav>div>button span{display:none!important}
html.sidebar-collapsed .sidebar-nav>div>button .bi-chevron-down,.sidebar-nav>div>button .badge-count{display:none!important}
html.sidebar-collapsed .sidebar-nav>div>div{display:none!important}
html.sidebar-collapsed .sidebar-footer .user-card{justify-content:center!important;padding:10px 0!important}
html.sidebar-collapsed .sidebar-footer .user-info{display:none!important}
html.sidebar-collapsed .sidebar-footer .user-avatar{margin:0!important}
html.sidebar-collapsed .sidebar-footer .user-card i{display:none!important}
html.sidebar-collapsed .btn-logout{font-size:0!important;padding:10px 0!important;justify-content:center!important;gap:0!important}
html.sidebar-collapsed .btn-logout i{font-size:15px!important}

/* ── COLLAPSED ACTIVE STATE ── */
html.sidebar-collapsed .sidebar-nav .nav-link.active {
  background: var(--sidebar-active-bg) !important;
  border-left: 3px solid var(--sidebar-active-tx) !important;
}
html.sidebar-collapsed .sidebar-nav .nav-link.active i {
  color: var(--sidebar-active-tx) !important;
}
html.sidebar-collapsed .sidebar-nav > div > button.sb-active {
  background: var(--sidebar-active-bg) !important;
  border-left: 3px solid var(--sidebar-active-tx) !important;
}
html.sidebar-collapsed .sidebar-nav > div > button.sb-active i:first-of-type {
  color: var(--sidebar-active-tx) !important;
}

/* ── MOBILE ── */
@media(max-width:768px){
  .sidebar{transform:translateX(-100%);width:var(--sidebar-w)!important;overflow-y:auto}
  .sidebar.open{transform:none}
  .main-content{margin-left:0}
  .topbar{left:0}
  .sidebar-toggle{display:flex}
}

/* ── COLLAPSED SIDEBAR HOVER TOOLTIPS ── */
.sb-tooltip{position:fixed;transform:translateY(-50%);background:#1e293b;color:#fff;padding:.4rem .65rem;border-radius:7px;font-size:.75rem;font-weight:600;white-space:nowrap;z-index:2000;pointer-events:none;opacity:0;transition:opacity .12s ease;box-shadow:0 6px 16px rgba(0,0,0,.22)}
.sb-tooltip.show{opacity:1}
.sb-tooltip::before{content:'';position:absolute;right:100%;top:50%;transform:translateY(-50%);border:5px solid transparent;border-right-color:#1e293b}
</style>
<script>
(function(){
  if(localStorage.getItem('fjb-sb-collapsed')==='1'&&window.innerWidth>768)
    document.documentElement.classList.add('sidebar-collapsed');
})();
</script>
@stack('styles')
</head>
<body>

@php $user = auth('it')->user(); @endphp

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand" onclick="_eggClick()" style="cursor:default;user-select:none">
    <div class="sb-logo-btn" onclick="event.stopPropagation();if(document.documentElement.classList.contains('sidebar-collapsed'))toggleSidebar();"
      style="width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;overflow:hidden;background:#fff;padding:4px;transition:opacity .15s">
      <img src="{{ asset('assets/img/logo_transparent.png') }}" alt="FJB Logo" style="width:100%;height:100%;object-fit:contain" onerror="this.style.display='none'">
      <i class="bi bi-layout-sidebar sb-open-icon"></i>
    </div>
    <div class="brand-name">FJB Inventory<span>Management System</span></div>
    <button class="sb-close-btn" onclick="event.stopPropagation();toggleSidebar()" title="Close sidebar">
      <i class="bi bi-layout-sidebar-reverse"></i>
    </button>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-section-label" style="margin-top:4px">Main</div>
    <a href="{{ route('it.dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
      <i class="bi bi-speedometer2"></i> Dashboard
    </a>

    <div class="nav-section-label">Inventory</div>

    @php
      $itActive = request()->routeIs('inventory.*') || request()->routeIs('non-it.*');
      $itPending = request()->routeIs('inventory.index') && request()->get('view') === 'pending_requests';
    @endphp

    @if($user->isAdmin())
    {{-- Admin: All Assets with sub-nav --}}
    <div>
      <button onclick="toggleITAssets()" id="itAssetsToggle" class="{{ $itActive ? 'sb-active' : '' }}"
        style="width:100%;display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:8px;
          background:{{ $itActive ? 'var(--sidebar-active-bg)' : 'none' }};
          color:{{ $itActive ? 'var(--sidebar-active-tx)' : '#475569' }};
          border:none;font-size:13.5px;font-weight:{{ $itActive ? '600' : '500' }};
          cursor:pointer;font-family:inherit;margin-bottom:2px;transition:all .15s;text-align:left">
        <i class="bi bi-box-seam-fill" style="font-size:16px;width:20px;text-align:center;flex-shrink:0"></i>
        <span style="flex:1">All Assets</span>
        <i class="bi bi-chevron-down" id="itAssetsChevron"
          style="font-size:11px;transition:transform .2s;{{ $itActive ? 'transform:rotate(180deg)' : '' }}"></i>
      </button>
      <div id="itAssetsMenu" style="{{ $itActive ? '' : 'display:none;' }}padding-left:14px;margin-top:2px">
        <a href="{{ route('it.inventory.index') }}" class="nav-link {{ request()->routeIs('inventory.index') && !request()->get('view') ? 'active' : '' }}"
          style="padding:7px 12px;font-size:13px">
          <i class="bi bi-box-seam" style="font-size:14px"></i> IT Assets
        </a>
        <a href="{{ route('it.non-it.index') }}" class="nav-link {{ request()->routeIs('non-it.index') ? 'active' : '' }}"
          style="padding:7px 12px;font-size:13px">
          <i class="bi bi-boxes" style="font-size:14px"></i> Non-IT Assets
        </a>
        <a href="{{ route('it.inventory.index') }}?view=pending_requests" class="nav-link {{ $itPending ? 'active' : '' }}"
          style="padding:7px 12px;font-size:13px">
          <i class="bi bi-hourglass-split" style="font-size:14px"></i> Pending Requests
        </a>
      </div>
    </div>
    @else
    {{-- Non-admin: IT + Non-IT + My Requests --}}
    <div>
      <button onclick="toggleITAssets()" id="itAssetsToggle" class="{{ $itActive ? 'sb-active' : '' }}"
        style="width:100%;display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:8px;
          background:{{ $itActive ? 'var(--sidebar-active-bg)' : 'none' }};
          color:{{ $itActive ? 'var(--sidebar-active-tx)' : '#475569' }};
          border:none;font-size:13.5px;font-weight:{{ $itActive ? '600' : '500' }};
          cursor:pointer;font-family:inherit;margin-bottom:2px;transition:all .15s;text-align:left">
        <i class="bi bi-box-seam-fill" style="font-size:16px;width:20px;text-align:center;flex-shrink:0"></i>
        <span style="flex:1">All Assets</span>
        <i class="bi bi-chevron-down" id="itAssetsChevron"
          style="font-size:11px;transition:transform .2s;{{ $itActive ? 'transform:rotate(180deg)' : '' }}"></i>
      </button>
      <div id="itAssetsMenu" style="{{ $itActive ? '' : 'display:none;' }}padding-left:14px;margin-top:2px">
        <a href="{{ route('it.inventory.index') }}" class="nav-link {{ request()->routeIs('inventory.index') ? 'active' : '' }}"
          style="padding:7px 12px;font-size:13px">
          <i class="bi bi-box-seam" style="font-size:14px"></i> IT Assets
        </a>
        <a href="{{ route('it.non-it.index') }}" class="nav-link {{ request()->routeIs('non-it.index') ? 'active' : '' }}"
          style="padding:7px 12px;font-size:13px">
          <i class="bi bi-boxes" style="font-size:14px"></i> Non-IT Assets
        </a>
        @if(!$user->isReadOnlyViewer())
        <a href="{{ route('it.inventory.index') }}?view=my_requests" class="nav-link"
          style="padding:7px 12px;font-size:13px">
          <i class="bi bi-clock-history" style="font-size:14px"></i> My Requests
        </a>
        @endif
      </div>
    </div>
    @endif
    <script>
    function toggleITAssets() {
      const menu = document.getElementById('itAssetsMenu');
      const chevron = document.getElementById('itAssetsChevron');
      const open = menu.style.display !== 'none';
      menu.style.display = open ? 'none' : 'block';
      chevron.style.transform = open ? 'rotate(0deg)' : 'rotate(180deg)';
    }
    </script>

    {{-- Write Off — all users --}}
    <a href="{{ route('it.writeoff.index') }}" class="nav-link {{ request()->routeIs('writeoff.index') || request()->routeIs('writeoff.hou-sign') || request()->routeIs('writeoff.gm-sign') || request()->routeIs('writeoff.ceo-approve') || request()->routeIs('writeoff.assign-hou') ? 'active' : '' }}">
      <i class="bi bi-pen-fill"></i> Write Off
    </a>

    {{-- Write Off Inventory — Finance Admin only --}}
    @if($user->isFinanceAdmin())
    <a href="{{ route('it.writeoff-inventory.index') }}" class="nav-link {{ request()->routeIs('writeoff-inventory.*') ? 'active' : '' }}">
      <i class="bi bi-clipboard2-x-fill"></i> Write Off Inventory
    </a>
    @endif

    {{-- E-Waste — Admin + Finance only --}}
    @if($user->isAdminOrFinance())
    @php $ewActive = request()->routeIs('ewaste.*') || request()->routeIs('ewaste.collected'); @endphp
    <div>
      <button onclick="toggleEwaste()" id="ewasteToggle" class="{{ $ewActive ? 'sb-active' : '' }}"
        style="width:100%;display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:8px;
          background:{{ $ewActive ? 'var(--sidebar-active-bg)' : 'none' }};
          color:{{ $ewActive ? 'var(--sidebar-active-tx)' : '#475569' }};
          border:none;font-size:13.5px;font-weight:{{ $ewActive ? '600' : '500' }};
          cursor:pointer;font-family:inherit;margin-bottom:2px;transition:all .15s;text-align:left">
        <i class="bi bi-recycle" style="font-size:16px;width:20px;text-align:center;flex-shrink:0"></i>
        <span style="flex:1">E-Waste</span>
        <i class="bi bi-chevron-down" id="ewasteChevron"
          style="font-size:11px;transition:transform .2s;{{ $ewActive ? 'transform:rotate(180deg)' : '' }}"></i>
      </button>
      <div id="ewasteMenu" style="{{ $ewActive ? '' : 'display:none;' }}padding-left:14px;margin-top:2px">
        <a href="{{ route('it.ewaste.index') }}" class="nav-link {{ request()->routeIs('ewaste.index') ? 'active' : '' }}"
          style="padding:7px 12px;font-size:13px">
          <i class="bi bi-recycle" style="font-size:14px"></i> E-Waste Items
        </a>
        <a href="{{ route('it.ewaste.collected') }}" class="nav-link {{ request()->routeIs('ewaste.collected') ? 'active' : '' }}"
          style="padding:7px 12px;font-size:13px">
          <i class="bi bi-patch-check-fill" style="font-size:14px"></i> Collected Proofs
        </a>
      </div>
    </div>
    <script>
    function toggleEwaste() {
      const menu = document.getElementById('ewasteMenu');
      const chevron = document.getElementById('ewasteChevron');
      const open = menu.style.display !== 'none';
      menu.style.display = open ? 'none' : 'block';
      chevron.style.transform = open ? 'rotate(0deg)' : 'rotate(180deg)';
    }
    </script>

    {{-- Disposal — Admin + Finance only --}}
    @php $dispActive = request()->routeIs('disposal.*'); @endphp
    <div>
      <button onclick="toggleDisposal()" id="disposalToggle" class="{{ $dispActive ? 'sb-active' : '' }}"
        style="width:100%;display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:8px;
          background:{{ $dispActive ? 'var(--sidebar-active-bg)' : 'none' }};
          color:{{ $dispActive ? 'var(--sidebar-active-tx)' : '#475569' }};
          border:none;font-size:13.5px;font-weight:{{ $dispActive ? '600' : '500' }};
          cursor:pointer;font-family:inherit;margin-bottom:2px;transition:all .15s;text-align:left">
        <i class="bi bi-trash3-fill" style="font-size:16px;width:20px;text-align:center;flex-shrink:0"></i>
        <span style="flex:1">Disposal</span>
        <i class="bi bi-chevron-down" id="disposalChevron"
          style="font-size:11px;transition:transform .2s;{{ $dispActive ? 'transform:rotate(180deg)' : '' }}"></i>
      </button>
      <div id="disposalMenu" style="{{ $dispActive ? '' : 'display:none;' }}padding-left:14px;margin-top:2px">
        <a href="{{ route('it.disposal.index') }}" class="nav-link {{ request()->routeIs('disposal.index') ? 'active' : '' }}"
          style="padding:7px 12px;font-size:13px">
          <i class="bi bi-box-arrow-right" style="font-size:14px"></i> Disposal Items
        </a>
        <a href="{{ route('it.disposal.proofs') }}" class="nav-link {{ request()->routeIs('disposal.proofs') ? 'active' : '' }}"
          style="padding:7px 12px;font-size:13px">
          <i class="bi bi-patch-check-fill" style="font-size:14px"></i> Collected Proofs
        </a>
      </div>
    </div>
    <script>
    function toggleDisposal() {
      const menu = document.getElementById('disposalMenu');
      const chevron = document.getElementById('disposalChevron');
      const open = menu.style.display !== 'none';
      menu.style.display = open ? 'none' : 'block';
      chevron.style.transform = open ? 'rotate(0deg)' : 'rotate(180deg)';
    }
    </script>

    {{-- Reports — Admin + Finance only --}}
    @php $rptActive = request()->routeIs('reports.*'); @endphp
    <div>
      <button onclick="toggleReports()" id="reportsToggle" class="{{ $rptActive ? 'sb-active' : '' }}"
        style="width:100%;display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:8px;
          background:{{ $rptActive ? 'var(--sidebar-active-bg)' : 'none' }};
          color:{{ $rptActive ? 'var(--sidebar-active-tx)' : '#475569' }};
          border:none;font-size:13.5px;font-weight:{{ $rptActive ? '600' : '500' }};
          cursor:pointer;font-family:inherit;margin-bottom:2px;transition:all .15s;text-align:left">
        <i class="bi bi-bar-chart-line-fill" style="font-size:16px;width:20px;text-align:center;flex-shrink:0"></i>
        <span style="flex:1">Reports</span>
        <i class="bi bi-chevron-down" id="reportsChevron"
          style="font-size:11px;transition:transform .2s;{{ $rptActive ? 'transform:rotate(180deg)' : '' }}"></i>
      </button>
      <div id="reportsMenu" style="{{ $rptActive ? '' : 'display:none;' }}padding-left:14px;margin-top:2px">
        <a href="{{ route('it.reports.it') }}" class="nav-link {{ request()->routeIs('reports.it') ? 'active' : '' }}"
          style="padding:7px 12px;font-size:13px">
          <i class="bi bi-box-seam" style="font-size:14px"></i> IT Assets
        </a>
        <a href="{{ route('it.reports.non-it') }}" class="nav-link {{ request()->routeIs('reports.non-it') ? 'active' : '' }}"
          style="padding:7px 12px;font-size:13px">
          <i class="bi bi-archive" style="font-size:14px"></i> Non-IT Assets
        </a>
      </div>
    </div>
    <script>
    function toggleReports() {
      const menu = document.getElementById('reportsMenu');
      const chevron = document.getElementById('reportsChevron');
      const open = menu.style.display !== 'none';
      menu.style.display = open ? 'none' : 'block';
      chevron.style.transform = open ? 'rotate(0deg)' : 'rotate(180deg)';
    }
    </script>
    @endif

    {{-- Request Form — all users --}}
    <div class="nav-section-label">Request Form</div>
    @php
      $itrActive = request()->routeIs('it-request-form') || request()->routeIs('it-request-form.drafts');
    @endphp
    @if(!$user->isAdmin() && $itDraftCount > 0)
    <div>
      <button onclick="toggleITRequestForm()" id="itrToggle" class="{{ $itrActive ? 'sb-active' : '' }}"
        style="width:100%;display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:8px;
          background:{{ $itrActive ? 'var(--sidebar-active-bg)' : 'none' }};
          color:{{ $itrActive ? 'var(--sidebar-active-tx)' : '#475569' }};
          border:none;font-size:13.5px;font-weight:{{ $itrActive ? '600' : '500' }};
          cursor:pointer;font-family:inherit;margin-bottom:2px;transition:all .15s;text-align:left">
        <i class="bi bi-file-earmark-text-fill" style="font-size:16px;width:20px;text-align:center;flex-shrink:0"></i>
        <span style="flex:1">IT Request Form</span>
        <i class="bi bi-chevron-down" id="itrChevron"
          style="font-size:11px;transition:transform .2s;{{ $itrActive ? 'transform:rotate(180deg)' : '' }}"></i>
      </button>
      <div id="itrMenu" style="{{ $itrActive ? '' : 'display:none;' }}padding-left:14px;margin-top:2px">
        <a href="{{ route('it.it-request-form') }}" class="nav-link {{ request()->routeIs('it-request-form') ? 'active' : '' }}"
          style="padding:7px 12px;font-size:13px">
          <i class="bi bi-plus-circle" style="font-size:14px"></i> New Request
        </a>
        <a href="{{ route('it.it-request-form.drafts') }}" class="nav-link {{ request()->routeIs('it-request-form.drafts') ? 'active' : '' }}"
          style="padding:7px 12px;font-size:13px">
          <i class="bi bi-floppy-fill" style="font-size:14px"></i> Saved Drafts
          <span style="display:inline-flex;align-items:center;justify-content:center;width:18px;height:18px;border-radius:50%;background:var(--accent);color:#fff;font-size:10px;font-weight:700;margin-left:4px">{{ $itDraftCount }}</span>
        </a>
      </div>
    </div>
    <script>
    function toggleITRequestForm() {
      const menu = document.getElementById('itrMenu');
      const chevron = document.getElementById('itrChevron');
      const open = menu.style.display !== 'none';
      menu.style.display = open ? 'none' : 'block';
      chevron.style.transform = open ? 'rotate(0deg)' : 'rotate(180deg)';
    }
    </script>
    @else
    <a href="{{ route('it.it-request-form') }}" class="nav-link {{ $itrActive ? 'active' : '' }}">
      <i class="bi bi-file-earmark-text-fill"></i> IT Request Form
    </a>
    @endif

    {{-- Admin section --}}
    @if($user->isAdminOrFinance())
    <div class="nav-section-label">Admin</div>

    @if($user->isAdmin())
    <a href="{{ route('it.users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
      <i class="bi bi-people-fill"></i> Manage Users
    </a>
    @endif

    @if($user->isAdmin())
    <a href="{{ route('it.activity.index') }}" class="nav-link {{ request()->routeIs('activity.*') ? 'active' : '' }}">
      <i class="bi bi-clock-history"></i> Activity Log
    </a>
    @endif

    <a href="{{ route('it.email-settings.index') }}" class="nav-link {{ request()->routeIs('email-settings.*') ? 'active' : '' }}">
      <i class="bi bi-envelope-fill"></i> Email {{ $user->isAdmin() ? 'Settings' : 'Notifications' }}
    </a>
    @endif

    {{-- Masterdata section --}}
    @if($user->isAdminOrFinance())
    <div class="nav-section-label">Masterdata</div>
    @php $mdActive = request()->routeIs('masterdata.*') || request()->routeIs('asset-classes.*') || request()->routeIs('brands.*') || request()->routeIs('locations.*'); @endphp
    <a href="{{ route('it.masterdata.index') }}" class="nav-link {{ $mdActive ? 'active' : '' }}">
      <i class="bi bi-collection-fill"></i> Masterdata
    </a>
    @endif

    {{-- Documentation section --}}
    @if($user->isAdmin())
    <div class="nav-section-label">Documentation</div>
    @endif

    <div style="border-top:1px solid var(--sidebar-border);margin:12px 0 8px"></div>
    <a href="{{ route('home') }}" class="nav-link">
      <i class="bi bi-grid-fill"></i> Back to Portal
    </a>
    <a href="{{ route('wt.admin.requests.create.shared') }}" class="nav-link">
      <i class="bi bi-broadcast"></i> WT System
    </a>
    <a href="{{ route('dashboard') }}" class="nav-link">
      <i class="bi bi-house-door-fill"></i> HR Portal
    </a>

  </nav>

  <div class="sidebar-footer">
    <a href="{{ route('it.profile') }}" class="user-card">
      @if($user->avatar)
        <img src="{{ Storage::url($user->avatar) }}" alt="avatar"
          style="width:34px;height:34px;border-radius:50%;object-fit:cover;flex-shrink:0">
      @else
        <div class="user-avatar">{{ strtoupper(substr($user->full_name, 0, 1)) }}</div>
      @endif
      <div class="user-info">
        <div class="user-name">{{ $user->full_name }}</div>
        <div class="user-role" style="color:{{ match($user->it_role) { 'admin_it', 'admin' => 'var(--accent)', 'finance_admin' => '#0284c7', 'ceo' => '#d97706', 'gm' => '#0d9488', 'hou' => '#7c3aed', default => '#64748b' } }}">
          {{ $user->getItRoleLabel() }}
        </div>
      </div>
      <i class="bi bi-gear" style="color:var(--sidebar-muted);font-size:13px;flex-shrink:0"></i>
    </a>
  </div>
</aside>
<!-- MAIN -->
<div class="main-content">
  <div class="topbar">
    <button class="sidebar-toggle" onclick="toggleSidebar()" title="Toggle sidebar">
      <i class="bi bi-list"></i>
    </button>
    <div class="topbar-left">
      <div class="topbar-title">@yield('page_title', 'Dashboard')</div>
      <div class="topbar-breadcrumb">FGV Johor Bulkers Sdn Bhd &rsaquo; @yield('page_title', 'Dashboard')</div>
    </div>
    <div class="topbar-right">
      <span style="font-size:12px;color:var(--muted)" id="liveClock"></span>

      {{-- Bell notification --}}
      <div style="position:relative" id="notifWrap">
        <button id="notifBell" onclick="toggleNotifDropdown()"
          style="position:relative;width:36px;height:36px;border-radius:8px;background:transparent;border:1.5px solid var(--border);cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--muted);transition:all .15s"
          onmouseover="this.style.background='var(--body-bg)';this.style.color='var(--navy)'"
          onmouseout="this.style.background='transparent';this.style.color='var(--muted)'">
          <i class="bi bi-bell-fill" style="font-size:16px"></i>
          <span id="notifBadge" style="display:none;position:absolute;top:-5px;right:-5px;min-width:18px;height:18px;background:#dc2626;color:#fff;border-radius:9px;font-size:10px;font-weight:700;align-items:center;justify-content:center;padding:0 4px;border:2px solid #fff"></span>
        </button>
        <div id="notifDropdown" style="display:none;position:absolute;top:calc(100% + 10px);right:0;width:340px;background:#fff;border:1px solid var(--border);border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,.13);z-index:7000;overflow:hidden">
          <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid var(--border)">
            <div style="font-size:13px;font-weight:700;color:var(--text)">Notifications</div>
            <div style="display:flex;align-items:center;gap:10px">
              <button onclick="markAllRead()" style="background:none;border:none;cursor:pointer;font-size:11px;font-weight:600;color:var(--accent);font-family:inherit">Mark all read</button>
              @if($user->isAdmin())
              <a href="{{ route('it.email-settings.index') }}" style="font-size:11px;font-weight:600;color:var(--muted);text-decoration:none"><i class="bi bi-gear"></i></a>
              @endif
            </div>
          </div>
          <div id="notifList" style="max-height:360px;overflow-y:auto">
            <div style="padding:32px 16px;text-align:center;color:var(--muted);font-size:13px">Loading...</div>
          </div>
        </div>
      </div>

      <a href="{{ route('it.profile') }}" class="topbar-user">
        <span class="topbar-role-badge">{{ $user->getItRoleLabel() }}</span>
        <span class="topbar-user-name">{{ $user->full_name }}</span>
      </a>
      <button class="theme-toggle" id="themeToggle" title="Toggle light/dark" onclick="toggleTheme()">
        <i class="bi bi-sun-fill" id="themeIcon"></i>
      </button>
      <form method="POST" action="{{ route('it.logout') }}" style="margin:0">
        @csrf
        <button type="submit"
          style="display:flex;align-items:center;gap:7px;padding:7px 14px;background:rgba(239,68,68,.08);border:1.5px solid rgba(239,68,68,.2);border-radius:8px;color:#dc2626;font-size:13px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif;transition:all .15s"
          onmouseover="this.style.background='rgba(239,68,68,.15)'" onmouseout="this.style.background='rgba(239,68,68,.08)'">
          <i class="bi bi-box-arrow-right"></i> Logout
        </button>
      </form>
    </div>
  </div>
  <div class="page-body">

    @include('it.partials.alerts')

    @yield('content')
    @include('components.ui.standardizer')

    <div class="app-footer" style="text-align: center; margin-top: 3rem; padding: 2rem 0; border-top: 1px solid var(--border, rgba(0,0,0,0.05)); clear: both;">
        <div style="margin-bottom: 0.5rem;">
            <img src="{{ asset('assets/images/footer.jpg') }}" alt="IT Logo" style="max-height: 45px; width: auto; object-fit: contain;">
        </div>
        <div style="font-size: 0.85rem; color: var(--muted, #64748b); font-weight: 500;">
            Develop by IT team
        </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
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
  '--sidebar-bg':        '#1a2235',
  '--sidebar-hover':     '#263042',
  '--sidebar-active-bg': 'rgba(56,189,248,.14)',
  '--sidebar-text':      '#cbd5e1',
  '--sidebar-head':      '#f1f5f9',
  '--sidebar-border':    '#374151',
  '--sidebar-muted':     '#94a3b8',
  '--sidebar-active-tx': '#38bdf8',
  '--bg':            '#111827',
  '--body-bg':       '#111827',
  '--white':         '#1f2937',
  '--surface':       '#1f2937',
  '--surface2':      '#263042',
  '--border':        '#374151',
  '--text':          '#d1d5db',
  '--muted':         '#6b7280',
  '--table-hover':   'rgba(255,255,255,.04)',
  '--form-input-bg': '#263042',
  '--form-input-border': '#374151',
  '--form-input-color': '#d1d5db',
  '--table-head-bg': '#1a2235',
  '--table-head-color': '#9ca3af',
};
const LIGHT_VARS = {
  '--sidebar-bg':        '#f8fafc',
  '--sidebar-hover':     '#f1f5f9',
  '--sidebar-active-bg': '#e0f2fe',
  '--sidebar-text':      '#334155',
  '--sidebar-head':      '#1e293b',
  '--sidebar-border':    '#e2e8f0',
  '--sidebar-muted':     '#64748b',
  '--sidebar-active-tx': '#0284c7',
  '--bg':            '#f1f5f9',
  '--body-bg':       '#f1f5f9',
  '--white':         '#ffffff',
  '--surface':       '#ffffff',
  '--surface2':      '#f8fafc',
  '--border':        '#e2e8f0',
  '--text':          '#1e293b',
  '--muted':         '#64748b',
  '--table-hover':   '#f8fafc',
  '--form-input-bg': '#ffffff',
  '--form-input-border': '#e2e8f0',
  '--form-input-color': '#1e293b',
  '--table-head-bg': '#e2e8f0',
  '--table-head-color': '#475569',
};
function applyTheme(dark) {
  const vars = dark ? DARK_VARS : LIGHT_VARS;
  for (const [k,v] of Object.entries(vars))
    document.documentElement.style.setProperty(k, v);
  const icon = document.getElementById('themeIcon');
  if (icon) icon.className = dark ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
  document.querySelectorAll('.form-control, .form-select').forEach(el => {
    el.style.background = vars['--form-input-bg'];
    el.style.borderColor = vars['--form-input-border'];
    el.style.color = vars['--form-input-color'];
  });
  document.querySelectorAll('thead th').forEach(el => {
    el.style.backgroundColor = vars['--table-head-bg'];
    el.style.color = vars['--table-head-color'];
  });
  document.querySelectorAll('table.dataTable tbody tr, tbody tr').forEach(el => {
    el.style.backgroundColor = vars['--surface'];
    el.style.color = vars['--text'];
  });
  document.body.style.backgroundColor = vars['--body-bg'];
  document.body.style.color = vars['--text'];
  document.documentElement.setAttribute('data-theme', dark ? 'dark' : 'light');
  document.documentElement.classList.toggle('dark', dark);
  document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
  if (typeof window._chartThemeUpdate === 'function') window._chartThemeUpdate();
}
function toggleTheme() {
  const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
  const next = isDark ? 'light' : 'dark';
  localStorage.setItem('fjb-theme', next);
  localStorage.setItem('color-theme', next);
  localStorage.setItem('theme', next);
  applyTheme(next === 'dark');
}
(function(){
  const saved = localStorage.getItem('fjb-theme') || localStorage.getItem('color-theme') || localStorage.getItem('theme');
  const theme = saved || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
  applyTheme(theme === 'dark');
})();

// ── DATATABLES ──
$(document).ready(function () {
  const isInventory = window.location.pathname.includes('inventory');
  const isEwaste    = window.location.pathname.includes('ewaste');
  if ($('.data-table').length && !isEwaste && !isInventory) {
    window._dt = $('.data-table').DataTable({
      pageLength: 25,
      responsive: true,
      columnDefs: [{ orderable: false, targets: 0 }],
      dom: '<"d-flex align-items-center justify-content-between mb-2"<"dt-len"l><"dt-search"f>><"dt-table"t><"d-flex align-items-center justify-content-between mt-3"<"dt-info"i><"dt-pages"p>>',
      language: {
        search: '',
        searchPlaceholder: 'Search...',
        lengthMenu: 'Show _MENU_',
        info: 'Showing _START_-_END_ of _TOTAL_ items',
        paginate: { previous: 'â† Previous', next: 'Next →' }
      },
      drawCallback: function() {
        if (typeof window._onDtDraw === 'function') window._onDtDraw();
      }
    });
  }
  if (typeof window._pageInit === 'function') window._pageInit();
  setTimeout(() => $('.alert-success-custom, .alert-danger-custom').fadeOut(500), 4000);
});

// ── BELL NOTIFICATION ──
(function() {
  var bellWrap = document.getElementById('notifWrap');
  if (!bellWrap) return;
  var badge    = document.getElementById('notifBadge');
  var dropdown = document.getElementById('notifDropdown');
  var listEl   = document.getElementById('notifList');
  var open     = false;

  function typeIcon(type) {
    var map = {
      'add_request':      ['#2563eb','bi-box-seam-fill'],
      'edit_request':     ['#7c3aed','bi-pencil-square'],
      'delete_request':   ['#dc2626','bi-trash3-fill'],
      'request_approved': ['#16a34a','bi-check-circle-fill'],
      'request_rejected': ['#dc2626','bi-x-circle-fill'],
      'non_it_request':   ['#0891b2','bi-boxes'],
      'writeoff':         ['#d97706','bi-pen-fill'],
      'ewaste':           ['#16a34a','bi-recycle'],
      'it_request':       ['#0284c7','bi-file-earmark-text-fill'],
      'general':          ['#0284c7','bi-bell-fill'],
    };
    return map[type] || map['general'];
  }

  function typeRoute(type) {
    var map = {
      'add_request':      '{{ route("it.inventory.index") }}',
      'edit_request':     '{{ route("it.inventory.index") }}',
      'delete_request':   '{{ route("it.inventory.index") }}',
      'request_approved': '{{ route("it.inventory.index") }}',
      'request_rejected': '{{ route("it.inventory.index") }}',
      'non_it_request':   '{{ route("it.non-it.index") }}',
      'writeoff':         '{{ route("it.writeoff.index") }}',
      'ewaste':           '{{ route("it.ewaste.index") }}',
      'it_request':       '{{ route("it.it-request-form") }}',
    };
    return map[type] || '';
  }

  function timeAgo(dt) {
    var diff = Math.floor((Date.now() - new Date(dt.replace(' ','T'))) / 1000);
    if (diff < 60)   return 'just now';
    if (diff < 3600) return Math.floor(diff/60) + 'm ago';
    if (diff < 86400) return Math.floor(diff/3600) + 'h ago';
    return Math.floor(diff/86400) + 'd ago';
  }

  function renderList(items) {
    if (!items.length) {
      listEl.innerHTML = '<div style="padding:32px 16px;text-align:center;color:#94a3b8;font-size:13px">No notifications yet.</div>';
      return;
    }
    listEl.innerHTML = items.map(function(n) {
      var ic   = typeIcon(n.type);
      var read = !!n.is_read;
      return '<div onclick="openNotif('+n.id+',\''+encodeURIComponent(n.link||'')+'\',\''+n.type+'\')" style="display:flex;gap:12px;align-items:flex-start;padding:12px 18px;border-bottom:1px solid #f8fafc;cursor:pointer;background:'+(read?'#fff':'#f0f9ff')+';transition:background .12s" onmouseover="this.style.background=\'#f8fafc\'" onmouseout="this.style.background=\''+(read?'#fff':'#f0f9ff')+'\'">'
        + '<div style="width:34px;height:34px;border-radius:8px;background:'+ic[0]+'18;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px"><i class="bi '+ic[1]+'" style="color:'+ic[0]+';font-size:15px"></i></div>'
        + '<div style="flex:1;min-width:0">'
        + '<div style="font-size:12px;font-weight:'+(read?'500':'700')+';color:#1e293b;line-height:1.4">'+n.title+'</div>'
        + '<div style="font-size:11px;color:#64748b;margin-top:3px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">'+n.message+'</div>'
        + '<div style="font-size:10px;color:#94a3b8;margin-top:4px">'+timeAgo(n.created_at)+'</div>'
        + '</div>'
        + (read ? '' : '<div style="width:7px;height:7px;border-radius:50%;background:#0284c7;flex-shrink:0;margin-top:6px"></div>')
        + '</div>';
    }).join('');
  }

  function fetchCount() {
    fetch('{{ route("it.notifications.ajax") }}?action=count')
      .then(r => r.json())
      .then(d => {
        var c = d.count || 0;
        if (c > 0) { badge.textContent = c > 99 ? '99+' : c; badge.style.display = 'flex'; }
        else { badge.style.display = 'none'; }
      }).catch(function(){});
  }

  function fetchList() {
    fetch('{{ route("it.notifications.ajax") }}?action=list')
      .then(r => r.json())
      .then(renderList)
      .catch(function(){ listEl.innerHTML = '<div style="padding:20px;text-align:center;color:#94a3b8;font-size:13px">Could not load notifications.</div>'; });
  }

  window.toggleNotifDropdown = function() {
    open = !open;
    dropdown.style.display = open ? 'block' : 'none';
    if (open) fetchList();
  };

  window.openNotif = function(id, link, type) {
    fetch('{{ route("it.notifications.mark-read") }}', {
      method: 'POST',
      headers: {'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Content-Type': 'application/json'},
      body: JSON.stringify({id: id})
    }).catch(function(){});
    var url = decodeURIComponent(link) || typeRoute(type);
    if (url) { window.location.href = url; }
    else { fetchList(); fetchCount(); }
  };

  window.markAllRead = function() {
    fetch('{{ route("it.notifications.mark-read") }}', {
      method: 'POST',
      headers: {'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Content-Type': 'application/json'},
      body: JSON.stringify({all: true})
    }).then(function(){ fetchList(); fetchCount(); }).catch(function(){});
  };

  document.addEventListener('click', function(e) {
    if (open && !document.getElementById('notifWrap').contains(e.target)) {
      open = false;
      dropdown.style.display = 'none';
    }
  });

  fetchCount();
  setInterval(fetchCount, 60000);
})();
</script>

{{-- â•”â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•— --}}
{{-- ║                        EASTER EGG                           ║ --}}
{{-- ║  Triggered by clicking the sidebar header 5 times.          ║ --}}
{{-- ║  Developed by: Muhammad Irfan bin Zuraili                   ║ --}}
{{-- ║  DO NOT REMOVE OR MODIFY THIS BLOCK.                        ║ --}}
{{-- ║  Protected by git pre-commit hook (.git/hooks/pre-commit)   ║ --}}
{{-- â•šâ•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
<!-- EASTER EGG -->
<style>
@keyframes egg-bd-in  {from{opacity:0}to{opacity:1}}
@keyframes egg-bd-out {from{opacity:1}to{opacity:0}}
@keyframes egg-card-in {from{opacity:0;transform:scale(.9) translateY(30px)}to{opacity:1;transform:none}}
@keyframes egg-card-out{from{opacity:1;transform:none}to{opacity:0;transform:scale(.9) translateY(30px)}}
#eggModal.egg-open             {animation:egg-bd-in   .3s ease both}
#eggModal.egg-open  #eggCard   {animation:egg-card-in .4s cubic-bezier(.16,.84,.44,1) .04s both}
#eggModal.egg-close            {animation:egg-bd-out  .35s ease both}
#eggModal.egg-close #eggCard   {animation:egg-card-out .3s cubic-bezier(.55,0,1,.45) both}
</style>

<div id="eggModal" style="display:none;position:fixed;inset:0;z-index:999999;background:rgba(10,18,32,.75);backdrop-filter:blur(5px);align-items:center;justify-content:center;padding:20px">
  <div id="eggCard" style="background:var(--surface);border:1px solid var(--border);border-radius:20px;width:100%;max-width:430px;box-shadow:0 32px 80px rgba(0,0,0,.45);font-family:'Inter',sans-serif;position:relative;overflow:hidden">

    {{-- Header --}}
    <div style="background:linear-gradient(135deg,#142b47 0%,#1a4b8c 100%);padding:28px 28px 22px;text-align:center;border-radius:20px 20px 0 0">
      <div style="margin:0 auto 14px;width:72px;height:72px;display:flex;align-items:center;justify-content:center">
        <img id="eggLogoImg" src="" alt="FJB Logo" style="width:68px;height:68px;object-fit:contain">
      </div>
      <div style="color:#fff;font-size:18px;font-weight:800;letter-spacing:.02em">FJB Inventory System</div>
      <div style="color:rgba(255,255,255,.5);font-size:12px;margin-top:5px;font-weight:500">Internal Management Platform</div>
    </div>

    {{-- Body --}}
    <div style="padding:22px 28px 0">
      <table style="width:100%;border-collapse:collapse;font-size:13px">
        <tr style="border-bottom:1px solid var(--border)">
          <td style="padding:9px 0;width:22px"><i class="bi bi-tag-fill" style="color:var(--accent)"></i></td>
          <td style="padding:9px 0 9px 10px;color:var(--muted);font-weight:600;width:110px">System</td>
          <td style="padding:9px 0;color:var(--text);font-weight:700">FJB Inventory System</td>
        </tr>
        <tr style="border-bottom:1px solid var(--border)">
          <td style="padding:9px 0"><i class="bi bi-patch-check-fill" style="color:#16a34a"></i></td>
          <td style="padding:9px 0 9px 10px;color:var(--muted);font-weight:600">Version</td>
          <td style="padding:9px 0;color:var(--text);font-weight:700">v1.0.0</td>
        </tr>
        <tr style="border-bottom:1px solid var(--border)">
          <td style="padding:9px 0"><i class="bi bi-person-fill" style="color:#7c3aed"></i></td>
          <td style="padding:9px 0 9px 10px;color:var(--muted);font-weight:600">Developed by</td>
          <td style="padding:9px 0;color:var(--text);font-weight:700">Muhammad Irfan bin Zuraili</td>
        </tr>
        <tr style="border-bottom:1px solid var(--border)">
          <td style="padding:9px 0"><i class="bi bi-calendar3" style="color:#0d9488"></i></td>
          <td style="padding:9px 0 9px 10px;color:var(--muted);font-weight:600">Period</td>
          <td style="padding:9px 0;color:var(--text);font-weight:700">UniKL MIIT Internship Programme (02/03/2026 - 17/07/2026)</td>
        </tr>
        <tr>
          <td style="padding:9px 0"><i class="bi bi-building" style="color:#d97706"></i></td>
          <td style="padding:9px 0 9px 10px;color:var(--muted);font-weight:600">Department</td>
          <td style="padding:9px 0;color:var(--text);font-weight:700">ICT Department</td>
        </tr>
      </table>
      <div style="margin-top:18px;padding:16px;background:var(--body-bg);border-radius:10px;border:1px solid var(--border)">
        <div style="font-size:13px;color:var(--muted);font-style:italic;margin-bottom:16px">Built with care to simplify your workflow.</div>
        <div style="font-size:13px;color:var(--muted);font-style:italic;margin-bottom:4px">Yours truly,</div>
        <img src="{{ asset('assets/images/irfan-signature.png') }}" alt="Irfan Zuraili Signature" style="max-height:80px;max-width:200px;object-fit:contain;opacity:0.85;display:block">
      </div>
    </div>

    {{-- Footer --}}
    <div style="padding:16px 28px 22px;text-align:center">
      <button id="eggClose"
        style="background:#142b47;color:#fff;border:none;border-radius:9px;padding:9px 36px;font-size:13px;font-weight:700;cursor:pointer;font-family:'Inter',sans-serif;transition:background .15s"
        onmouseover="this.style.background='#1e3f6e'" onmouseout="this.style.background='#142b47'">
        Close
      </button>
    </div>

    {{-- Cutout character — inside card, anchored bottom-right --}}
    <img id="eggCharImg" src="" alt="Irfan"
      style="position:absolute;bottom:0;right:0;height:260px;width:auto;pointer-events:none;user-select:none;display:block">
  </div>
</div>
<script>
(function(){
  var modal   = document.getElementById('eggModal');
  var _eggN   = 0, _eggT, _closing = false, _closeTimer;
  var _base   = window.location.origin + '{{ request()->getBasePath() }}';
  document.getElementById('eggLogoImg').src = _base + '/assets/images/fjb-logo-egg.png';
  document.getElementById('eggCharImg').src = _base + '/assets/images/dev-irfan-cutout.png';

  function openEgg() {
    clearTimeout(_closeTimer);
    _closing = false;
    modal.classList.remove('egg-open','egg-close');
    modal.style.display = 'flex';
    void modal.offsetWidth;
    modal.classList.add('egg-open');
  }

  function closeEgg() {
    if (_closing) return;
    _closing = true;
    modal.classList.remove('egg-open');
    void modal.offsetWidth;
    modal.classList.add('egg-close');
    _closeTimer = setTimeout(function(){
      modal.style.display = 'none';
      modal.classList.remove('egg-close');
      _closing = false;
    }, 380);
  }

  window._eggClick = function() {
    _eggN++;
    clearTimeout(_eggT);
    _eggT = setTimeout(function(){ _eggN = 0; }, 1800);
    if (_eggN >= 5) { _eggN = 0; openEgg(); }
  };

  document.getElementById('eggClose').addEventListener('click', closeEgg);
  modal.addEventListener('click', function(e){ if (e.target === modal) closeEgg(); });
  document.addEventListener('keydown', function(e){
    if (e.key === 'Escape') closeEgg();
  });
})();
</script>

<!-- PAGE TRANSITIONS -->
<style>
@keyframes fjb-page-in  { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:none} }
@keyframes fjb-page-out { from{opacity:1;transform:translateY(0)}     to{opacity:0;transform:translateY(-6px)} }
.page-body { animation: fjb-page-in .22s ease both; }
.page-leaving .page-body { animation: fjb-page-out .18s ease both; pointer-events:none; }
</style>
<script>
(function(){
  document.addEventListener('click', function(e) {
    var a = e.target.closest('a');
    if (!a) return;
    var href = a.getAttribute('href');
    if (!href || href === '#' || href.startsWith('#') || href.startsWith('javascript:')) return;
    if (a.getAttribute('target') === '_blank') return;
    if (e.ctrlKey || e.metaKey || e.shiftKey) return;
    try {
      var url = new URL(href, window.location.href);
      if (url.hostname !== window.location.hostname) return;
    } catch(ex) { return; }
    e.preventDefault();
    document.body.classList.add('page-leaving');
    setTimeout(function(){ window.location.href = href; }, 180);
  });
})();
</script>

<script>
// ── SIDEBAR COLLAPSE ──
function toggleSidebar() {
  if (window.innerWidth <= 768) {
    document.getElementById('sidebar').classList.toggle('open');
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

  document.querySelectorAll('.sidebar-nav > .nav-link, .sidebar-nav > div > button, .sidebar-footer .btn-logout').forEach(function (el) {
    var label = Array.from(el.childNodes)
      .filter(function (n) { return n.nodeType === 3; })
      .map(function (n) { return n.textContent; })
      .join('').replace(/\s+/g, ' ').trim();
    if (!label) {
      var span = el.querySelector(':scope > span');
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
@stack('scripts')
</body>
</html>
