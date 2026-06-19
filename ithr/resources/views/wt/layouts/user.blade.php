<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Dashboard') — WT System</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config={darkMode:'class',theme:{extend:{colors:{corp:{navy:'#1F2937',brown:'#075985',gold:'#38bdf8'}}}}}</script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
:root {
  --sidebar-bg:        #1e293b;
  --sidebar-hover:     rgba(255,255,255,.08);
  --sidebar-active-bg: #075985;
  --sidebar-text:      rgba(255,255,255,.65);
  --sidebar-head:      #ffffff;
  --sidebar-w:         200px;
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
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{font-size:14px}
body{
  background:
    radial-gradient(circle at top right,rgba(59,130,246,.08),transparent 24%),
    radial-gradient(circle at bottom left,rgba(148,163,184,.14),transparent 28%),
    linear-gradient(180deg,#F4F7FB 0%,#EDF2F7 100%);
  color:var(--text);font-family:'DM Sans',sans-serif;font-size:11px;min-height:100vh;
  transition:background-color .3s,color .3s;
}
.dark body{background:#0f172a !important;color:#cbd5e1 !important}

/* ── SIDEBAR ── */
.sidebar{
  position:fixed;top:0;left:0;width:var(--sidebar-w);height:100vh;
  background:var(--sidebar-bg);display:flex;flex-direction:column;z-index:100;
  transition:transform .3s;
}
.sidebar-brand{
  padding:20px 20px 16px;border-bottom:1px solid rgba(255,255,255,.08);
  display:flex;align-items:center;gap:12px;text-decoration:none;
}
.sidebar-brand img{width:36px;height:36px;object-fit:contain;background:transparent}
.brand-name{font-family:'DM Sans',sans-serif;font-size:13px;font-weight:700;color:#fff;
  text-transform:uppercase;letter-spacing:.05em;line-height:1.3}
.brand-name span{color:#fff;display:block;font-size:10px;letter-spacing:.1em;font-weight:700}

.sidebar-nav{flex:1;overflow-y:auto;padding:16px 12px;scrollbar-width:thin;scrollbar-color:rgba(255,255,255,.08) transparent}
.sidebar-nav::-webkit-scrollbar{width:3px}
.sidebar-nav::-webkit-scrollbar-track{background:transparent}
.sidebar-nav::-webkit-scrollbar-thumb{background:rgba(255,255,255,.08);border-radius:99px}
.nav-section-label{font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.12em;
  color:rgba(255,255,255,.3);padding:0 8px;margin:20px 0 6px}
.nav-section-label:first-child{margin-top:4px}

.nav-link{
  display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:8px;
  color:var(--sidebar-text);text-decoration:none;font-size:13.5px;font-weight:500;
  transition:all .15s;margin-bottom:2px;
}
.nav-link i{font-size:16px;width:20px;text-align:center;flex-shrink:0}
.nav-link:hover{background:rgba(255,255,255,.08);color:#fff}
.nav-link.active,.nav-link.active-sidebar,.nav-link.sidebar-active{background:#075985 !important;color:#f8fafc !important;border-color:rgba(56,189,248,.34) !important;font-weight:600}

.sidebar-footer{padding:14px 12px;border-top:1px solid rgba(255,255,255,.08)}
.user-card{
  display:flex;align-items:center;gap:10px;padding:10px 12px;
  border-radius:8px;background:rgba(255,255,255,.06);
  text-decoration:none;transition:background .15s;cursor:pointer;
}
.user-card:hover{background:rgba(255,255,255,.1)}
.user-avatar{
  width:34px;height:34px;border-radius:50%;background:var(--accent);
  display:flex;align-items:center;justify-content:center;
  font-family:'DM Sans',sans-serif;font-size:13px;font-weight:700;color:#fff;flex-shrink:0;
}
.user-info{min-width:0;flex:1}
.user-name{font-size:13px;font-weight:500;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.user-role{font-size:10px;text-transform:uppercase;letter-spacing:.06em;font-weight:600;color:var(--sky)}

/* ── MAIN ── */
.main-content{margin-left:var(--sidebar-w);min-height:100vh;display:flex;flex-direction:column}
.topbar{
  background:rgba(255,255,255,.9);backdrop-filter:blur(14px);
  border-bottom:1px solid rgba(226,232,240,.95);
  padding:0 20px;min-height:46px;display:flex;align-items:center;gap:12px;
  position:sticky;top:0;z-index:50;transition:all .3s;
}
.dark .topbar{background:rgba(15,23,42,.9) !important;border-color:rgba(51,65,85,.7) !important}
.topbar-left{flex:1;min-width:0}
.topbar-title{font-family:'DM Sans',sans-serif;font-size:16px;font-weight:700;color:var(--navy,var(--text))}
.topbar-breadcrumb{font-size:11px;color:var(--muted);margin-top:1px}
.topbar-right{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.topbar-user{
  display:flex;align-items:center;gap:8px;
  background:var(--body-bg);border:1px solid var(--border);
  border-radius:8px;padding:6px 12px;
}
.topbar-user-name{font-size:13px;font-weight:600;color:var(--text)}
.topbar-role-badge{
  background:#dbeafe;color:#1d4ed8;
  border-radius:5px;padding:2px 8px;font-size:10px;font-weight:700;text-transform:uppercase;
}
.theme-toggle-btn{
  width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;
  background:var(--body-bg);border:1px solid var(--border);
  color:var(--muted);cursor:pointer;font-size:16px;transition:all .15s;
}
.theme-toggle-btn:hover{border-color:var(--accent);color:var(--accent)}
.page-body{padding:.75rem;flex:1}

/* ── TABLE CARD ── */
.table-card{background:var(--surface);border:1px solid var(--border);border-radius:12px;overflow:hidden;box-shadow:var(--shadow)}
.table-card-header{padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:12px}
.table-card-title{font-family:'DM Sans',sans-serif;font-size:14px;font-weight:700;color:var(--text);flex:1}
.table{color:var(--text);margin:0}
.table thead th{background:var(--table-head-bg,#e2e8f0) !important;border-color:var(--border) !important;color:var(--table-head-color,#475569);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;padding:12px 16px;white-space:nowrap}
.table tbody tr{background:var(--surface) !important;color:var(--text) !important}
.table tbody tr:hover{background:var(--table-hover) !important}
.table tbody td{border-color:var(--border) !important;padding:12px 16px;vertical-align:middle;color:var(--text) !important}

/* ── BADGES ── */
.badge-status{padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em}
.bs-active{background:rgba(34,197,94,.12);color:#16a34a}
.bs-pending{background:rgba(245,158,11,.12);color:#d97706}
.bs-repair{background:rgba(59,130,246,.12);color:#2563eb}
.bs-disposed{background:rgba(239,68,68,.12);color:#dc2626}

/* ── FORM ── */
.form-label{color:var(--muted);font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px}
.form-control,.form-select{
  background:var(--form-input-bg,#fff) !important;
  border:1.5px solid var(--form-input-border,var(--border)) !important;
  color:var(--form-input-color,var(--text)) !important;
  border-radius:8px;padding:9px 13px;font-family:'DM Sans',sans-serif;font-size:14px;transition:border-color .2s;
}
.form-control:focus,.form-select:focus{border-color:var(--sky-dark,var(--accent)) !important;box-shadow:0 0 0 3px rgba(56,189,248,.15) !important;outline:none}
textarea.form-control{min-height:90px;resize:vertical}
.form-control::placeholder{color:var(--muted);opacity:.7}

/* ── BUTTONS ── */
.btn-primary-custom{background:var(--navy,#142b47);color:#fff;border:none;border-radius:8px;padding:9px 20px;font-family:'DM Sans',sans-serif;font-size:13px;font-weight:600;cursor:pointer;transition:all .15s;text-decoration:none;display:inline-flex;align-items:center;gap:7px}
.btn-primary-custom:hover{background:var(--navy-mid,#254a78);color:#fff}
.btn-secondary-custom{background:#fff;color:var(--text);border:1.5px solid var(--border);border-radius:8px;padding:9px 18px;font-size:13px;font-weight:500;cursor:pointer;transition:all .15s;text-decoration:none;display:inline-flex;align-items:center;gap:7px;font-family:'DM Sans',sans-serif}
.btn-secondary-custom:hover{border-color:var(--navy,#142b47);color:var(--navy,#142b47)}
.btn-icon{width:30px;height:30px;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;font-size:14px;border:none;cursor:pointer;transition:all .15s;text-decoration:none}
.btn-edit{background:rgba(59,130,246,.1);color:#2563eb}
.btn-edit:hover{background:rgba(59,130,246,.2);color:#2563eb}
.btn-delete{background:rgba(239,68,68,.1);color:#dc2626}
.btn-delete:hover{background:rgba(239,68,68,.2);color:#dc2626}
.btn-view{background:rgba(var(--accent-rgb),.1);color:var(--accent)}
.btn-view:hover{background:rgba(var(--accent-rgb),.2);color:var(--accent)}

/* ── ALERTS ── */
.alert-success-custom{background:#dcfce7;border:1px solid #bbf7d0;color:#166534;border-radius:8px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:8px;font-size:13px}
.alert-danger-custom{background:#fee2e2;border:1px solid #fecaca;color:#991b1b;border-radius:8px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:8px;font-size:13px}

/* ── DATATABLES ── */
.dataTables_wrapper .dataTables_filter input,.dataTables_wrapper .dataTables_length select{background:var(--form-input-bg,#fff) !important;border:1.5px solid var(--form-input-border,var(--border)) !important;color:var(--form-input-color,var(--text)) !important;border-radius:6px;padding:5px 10px}
.dataTables_wrapper .dataTables_info,.dataTables_wrapper .dataTables_filter label,.dataTables_wrapper .dataTables_length label{color:var(--muted)}
table.dataTable tbody tr,table.dataTable tbody tr.odd,table.dataTable tbody tr.even{background-color:var(--surface) !important;color:var(--text) !important}
table.dataTable tbody tr:hover{background-color:var(--table-hover) !important}
table.dataTable tbody td{color:var(--text) !important}

/* ── CONTENT SURFACE ── */
.content-surface{background:rgba(255,255,255,.98) !important;border:1px solid rgba(226,232,240,.95) !important;border-radius:8px !important;padding:.85rem !important;box-shadow:none !important}
.dark .content-surface{background:#0f172a !important;border-color:#334155 !important;}

/* Page header standard classes */
.page-header-block{display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:18px}
.page-title-standard{font-size:16px;font-weight:800;color:var(--text);margin:0;line-height:1.2}
.page-subtitle-standard{margin-top:4px;font-size:12px;color:var(--muted);margin-bottom:0}

/* ── WT BUTTON COMPAT ── */
.wt-btn{display:inline-flex;align-items:center;justify-content:center;gap:5px;padding:6px 12px;border-radius:6px;border:1px solid var(--border);background:var(--surface);color:var(--text);font-size:12px;font-weight:600;cursor:pointer;transition:all .15s;text-decoration:none}
.wt-btn:hover{background:var(--body-bg);border-color:var(--navy)}
.wt-btn-danger{background:rgba(239,68,68,.1);border-color:rgba(239,68,68,.3);color:#dc2626}
.wt-btn-danger:hover{background:rgba(239,68,68,.2);color:#dc2626}
.wt-btn-success{background:rgba(34,197,94,.1);border-color:rgba(34,197,94,.3);color:#16a34a}
.wt-btn-success:hover{background:rgba(34,197,94,.2);color:#16a34a}

/* ── SIDEBAR INFO POPOVERS ── */
.has-info{position:relative;overflow:visible;padding-right:50px !important}
.nav-info-slot{position:absolute;right:12px;top:50%;margin-top:-7.5px;display:inline-flex;align-items:center;justify-content:center;z-index:2}
.nav-info-btn{display:inline-flex;align-items:center;justify-content:center;width:15px;height:15px;border-radius:999px;border:1px solid rgba(148,163,184,.24);background:rgba(15,23,42,.18);color:rgba(226,232,240,.88);font-size:7px;font-weight:900;line-height:1;cursor:pointer;transition:all .18s}
.nav-info-btn:hover,.nav-info-btn.is-open{background:#0ea5e9;color:#fff;border-color:rgba(14,165,233,.72)}
.nav-info-popover{position:fixed;left:0;top:0;width:min(280px,calc(100vw - 32px));padding:12px 14px;border:1px solid rgba(148,163,184,.18);border-radius:14px;background:rgba(15,23,42,.98);color:#e2e8f0;box-shadow:0 24px 44px rgba(2,6,23,.4);font-size:11px;font-weight:700;line-height:1.55;z-index:1200}
.nav-info-popover.hidden{display:none !important}
.sidebar-brand-logo{width:28px;height:28px;object-fit:contain}

/* ── TOPBAR ACTION BTNS ── */
.topbar-action-btn{width:36px;height:36px;border-radius:8px;background:transparent;border:1.5px solid var(--border);cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--muted);transition:all .15s;font-size:16px;position:relative}
.topbar-action-btn:hover{background:var(--body-bg);color:var(--navy)}
.topbar-signout-btn{display:inline-flex;align-items:center;gap:6px;padding:5px 12px;background:#fff;border:1px solid rgba(226,232,240,.95);border-radius:20px;color:#64748b;font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .15s;box-shadow:0 1px 3px rgba(0,0,0,.06)}
.topbar-signout-btn:hover{background:rgba(254,242,242,.95);border-color:rgba(248,113,113,.38);color:#b91c1c}

/* ── NOTIFICATION DROPDOWN ── */
#notificationDropdown{background:#fff !important;border:1px solid var(--border) !important;border-radius:12px !important;box-shadow:0 18px 42px rgba(15,23,42,.16) !important}
.notification-item-btn{display:flex;width:100%;padding:12px 18px;text-align:left;background:none;border:none;cursor:pointer;transition:background .12s}
.notification-item-btn:hover{background:#f8fafc}
.notification-item-action{font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--accent);white-space:nowrap;flex-shrink:0}

/* ── LOGOUT MODAL ── */
.logout-modal-overlay{position:fixed;inset:0;display:flex;align-items:center;justify-content:center;padding:20px;background:rgba(15,23,42,.62);backdrop-filter:blur(8px);opacity:0;visibility:hidden;pointer-events:none;transition:opacity .22s,visibility .22s;z-index:9000}
.logout-modal-overlay.active{opacity:1;visibility:visible;pointer-events:auto}
.logout-modal{width:100%;max-width:430px;border-radius:20px;border:1px solid rgba(14,165,233,.2);background:linear-gradient(180deg,#fff 0%,#f8fafc 100%);box-shadow:0 28px 60px rgba(15,23,42,.28);transform:translateY(12px) scale(.98);transition:transform .22s;overflow:hidden}
.logout-modal-overlay.active .logout-modal{transform:translateY(0) scale(1)}
.logout-modal-icon{width:54px;height:54px;border-radius:16px;display:inline-flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#0284c7,#075985);color:#fff;font-size:22px}
.logout-modal-title{font-size:1.1rem;font-weight:800;letter-spacing:-.02em;color:var(--text)}
.logout-modal-copy{font-size:11px;line-height:1.65;font-weight:600;letter-spacing:.04em;text-transform:uppercase;color:var(--muted)}
.logout-modal-close{width:36px;height:36px;border-radius:10px;border:1px solid rgba(148,163,184,.25);background:rgba(255,255,255,.75);color:#64748b;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;transition:all .18s}
.logout-modal-close:hover{color:#ef4444;border-color:rgba(239,68,68,.28)}
.logout-modal-actions{display:flex;gap:12px;justify-content:flex-end}
.logout-modal-btn{min-width:130px;padding:10px 16px;border-radius:12px;font-size:10px;font-weight:800;letter-spacing:.14em;text-transform:uppercase;transition:all .18s;cursor:pointer;border:none}
.logout-modal-btn-cancel{border:1px solid rgba(148,163,184,.28);background:rgba(255,255,255,.82);color:#475569}
.logout-modal-btn-cancel:hover{background:#f1f5f9;color:#0f172a}
.logout-modal-btn-confirm{border:1px solid rgba(239,68,68,.22);background:linear-gradient(135deg,#b91c1c,#dc2626);color:#fff}
.logout-modal-btn-confirm:hover{transform:translateY(-1px)}

/* ── MODAL ── */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(15,23,42,.45);backdrop-filter:blur(8px);z-index:1000;align-items:center;justify-content:center;padding:20px}
.modal-overlay.active{display:flex}
.modal-box{background:var(--surface);border-radius:20px;width:95%;max-width:820px;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 40px 100px rgba(15,23,42,.25);overflow:hidden;border:1px solid var(--border)}
.modal-header{display:flex;justify-content:space-between;align-items:center;padding:20px 24px;border-bottom:1px solid var(--border);background:var(--surface);flex-shrink:0}
.modal-title{font-size:14px;font-weight:700;color:var(--text);text-transform:uppercase;letter-spacing:.05em}
.modal-close-btn{background:var(--body-bg);border:1px solid var(--border);border-radius:10px;padding:8px;cursor:pointer;color:var(--muted);transition:all .2s}
.modal-close-btn:hover{color:#ef4444}
.modal-body{padding:24px;overflow-y:auto;flex:1}
.modal-footer{display:flex;justify-content:flex-end;align-items:center;gap:12px;padding:16px 24px;border-top:1px solid var(--border);background:var(--body-bg);flex-shrink:0}
.modal-form-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:16px}
.modal-form-group{display:flex;flex-direction:column;gap:6px}
.modal-form-label{font-size:10px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.05em}
.modal-form-input{border:1px solid var(--border);border-radius:8px;padding:8px 12px;font-size:13px;color:var(--text);background:var(--surface);transition:all .2s}
.modal-form-input:focus{border-color:var(--sky-dark);box-shadow:0 0 0 3px rgba(56,189,248,.14);outline:none}

/* ── MOBILE ── */
.sidebar-toggle{display:none;background:none;border:1.5px solid var(--border);border-radius:7px;color:var(--text);padding:6px 10px;cursor:pointer;font-size:18px}
@media(max-width:768px){
  .sidebar{transform:translateX(-100%)}
  .sidebar.open{transform:none}
  .main-content{margin-left:0}
  .sidebar-toggle{display:flex;align-items:center}
  .page-body{padding:16px}
  .topbar{padding:0 16px}
  .modal-form-grid{grid-template-columns:1fr}
  .modal-footer{flex-direction:column-reverse;align-items:stretch}
  .modal-footer > *{width:100%}
  .logout-modal-actions{flex-direction:column-reverse}
  .logout-modal-btn{width:100%;min-width:0}
}

/* ── INPUT UPPERCASE ── */
input[type="text"]:not([data-preserve-case="true"]):not([name="username"]),
input[type="search"],
textarea:not([data-preserve-case="true"]){text-transform:uppercase}
input[data-preserve-case="true"],textarea[data-preserve-case="true"]{text-transform:none !important}

/* ── SCROLLBAR ── */
::-webkit-scrollbar{width:5px;height:5px}
::-webkit-scrollbar-track{background:transparent}
::-webkit-scrollbar-thumb{background:#94a3b8;border-radius:10px}

/* ── COMPACT SCALE (matches reference) ── */
.sidebar{width:var(--sidebar-w)}
.sidebar-nav{padding:.65rem .55rem}
.nav-link{min-height:28px !important;padding:.35rem .65rem !important;gap:.5rem !important;font-size:9px !important;line-height:1.2 !important;border-radius:4px !important;border:1px solid transparent}
.nav-link:hover{border-color:rgba(56,189,248,.12)}
.nav-link.active,.nav-link.active-sidebar,.nav-link.sidebar-active{border-radius:4px !important}
.main-content{margin-left:var(--sidebar-w)}
.topbar-title{font-size:13px;font-weight:700;color:var(--text)}
.topbar-breadcrumb{font-size:8px;color:var(--muted);margin-top:1px}
.page-title-standard{font-size:16px !important;font-weight:900 !important;line-height:1.15 !important;letter-spacing:-.01em !important;color:#1f2937 !important}
.dark .page-title-standard{color:#f8fafc !important}
.page-subtitle-standard{margin-top:.45rem !important;font-size:9px !important;font-weight:900 !important;letter-spacing:.18em !important;text-transform:uppercase !important;color:#64748b !important}
.dark .page-subtitle-standard{color:#94a3b8 !important}
.page-header-block{margin-bottom:.85rem !important}
.wt-btn,.btn-primary-custom,.btn-secondary-custom,.btn-submit,.btn-cancel{min-height:24px !important;padding:.35rem .6rem !important;border-radius:5px !important;font-size:8px !important;line-height:1 !important;letter-spacing:.06em !important;gap:.35rem !important}
.content-surface input,.content-surface select,.content-surface textarea,.modal-form-input{min-height:30px !important;padding:.4rem .55rem !important;border-radius:6px !important;font-size:10px !important;line-height:1.25 !important}
table.dataTable thead th{padding:.45rem .5rem !important;font-size:8px !important;letter-spacing:.08em !important}
table.dataTable tbody td{padding:.45rem .5rem !important;font-size:9px !important}
.sidebar-shell{background:#1e293b !important;border-right:1px solid rgba(255,255,255,.05) !important}

/* ── DARK HELPERS FOR TAILWIND VIEWS ── */
.dark div.bg-white,.dark section.bg-white{background-color:#1e293b !important;border-color:#334155 !important}
.dark .text-stone-800,.dark .text-stone-700,.dark .text-slate-800,.dark .text-slate-900{color:#f1f5f9 !important}
.dark .text-stone-600,.dark .text-stone-500,.dark .text-slate-600,.dark .text-slate-500{color:#94a3b8 !important}
.dark input,.dark select,.dark textarea{background-color:#0f172a !important;border-color:#334155 !important;color:#f1f5f9 !important}
.dark .select2-container--default .select2-selection--single,.dark .select2-container--default .select2-selection--multiple{background-color:#0f172a !important;border-color:#334155 !important;color:#f1f5f9 !important}
html:not(.dark) .content-surface{background:rgba(255,255,255,.98) !important;border:1px solid rgba(226,232,240,.95) !important}
</style>
@stack('styles')
</head>
<body id="main-body">

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
  <a href="{{ request()->fullUrl() }}" class="sidebar-brand" title="Refresh page">
    <div style="width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;overflow:hidden;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1)">
      <img src="{{ asset('assets/images/fjb-logo.svg') }}" alt="FJB" class="sidebar-brand-logo" onerror="this.onerror=null;this.src='{{ asset('assets/img/logo_transparent.png') }}'">
    </div>
    <div class="brand-name">WT System<span>Walkie Talkie Management</span></div>
  </a>

  <nav class="sidebar-nav">
    <div class="nav-section-label" style="margin-top:4px">Asset Interactive</div>
    <a href="{{ route('wt.user.returns.create') }}" class="nav-link has-info {{ request()->routeIs('user.returns.create') ? 'sidebar-active' : '' }}">
      <i class="fa-solid fa-rotate-left" style="width:20px;text-align:center;flex-shrink:0"></i> <span>Return Unit</span>
      @include('wt.partials.sidebar-info', ['text' => 'Submit a return request when a walkie unit is no longer being used.'])
    </a>
    <a href="{{ route('wt.user.damages.create') }}" class="nav-link has-info {{ request()->routeIs('user.damages.*') ? 'sidebar-active' : '' }}">
      <i class="fa-solid fa-triangle-exclamation" style="width:20px;text-align:center;flex-shrink:0"></i> <span>Report Faulty</span>
      @include('wt.partials.sidebar-info', ['text' => 'Report faulty, damaged, missing, or problem walkie talkie units.'])
    </a>
    <a href="{{ route('wt.user.requests.status') }}" class="nav-link has-info {{ request()->routeIs('user.requests.status') ? 'sidebar-active' : '' }}">
      <i class="fa-solid fa-list-ul" style="width:20px;text-align:center;flex-shrink:0"></i> <span>Request Status</span>
      @include('wt.partials.sidebar-info', ['text' => 'Check the latest status of your walkie talkie requests.'])
    </a>

    <div class="nav-section-label">My Account</div>
    <a href="{{ route('wt.user.profile') }}" class="nav-link has-info {{ request()->routeIs('user.profile') ? 'sidebar-active' : '' }}">
      <i class="fa-solid fa-user-circle" style="width:20px;text-align:center;flex-shrink:0"></i> <span>My Profile</span>
      @include('wt.partials.sidebar-info', ['text' => 'View and update your account profile information.'])
    </a>
    <a href="{{ route('wt.user.policies') }}" class="nav-link has-info {{ request()->routeIs('user.policies') ? 'sidebar-active' : '' }}">
      <i class="fa-solid fa-file-contract" style="width:20px;text-align:center;flex-shrink:0"></i> <span>Policies</span>
      @include('wt.partials.sidebar-info', ['text' => 'Read the rules and guidelines for using company walkie talkies.'])
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
      <div class="topbar-user">
        <span class="topbar-role-badge">{{ $accountRoleLabel }}</span>
        <span class="topbar-user-name">{{ Auth::guard('wt')->user()->username ?? 'User' }}</span>
      </div>

      {{-- Logout --}}
      <form id="logout-form" action="{{ route('wt.logout') }}" method="POST" style="display:none">@csrf</form>
      <button onclick="handleLogout()" class="topbar-signout-btn" title="Sign out">
        Sign Out <i class="fas fa-sign-out-alt"></i>
      </button>
    </div>
  </div>

  <div class="page-body">
    <div class="content-surface" style="max-width:900px;margin:0 auto">
      @include('wt.partials.flash-alerts')
      @yield('content')
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
// ── THEME ──
const DARK_VARS = {
  '--body-bg':'#111827','--surface':'#1f2937','--border':'#374151','--text':'#d1d5db','--muted':'#6b7280',
  '--table-hover':'rgba(255,255,255,.04)','--form-input-bg':'#263042','--form-input-border':'#374151','--form-input-color':'#d1d5db',
  '--table-head-bg':'#1a2235','--table-head-color':'#9ca3af',
};
const LIGHT_VARS = {
  '--body-bg':'#f1f5f9','--surface':'#ffffff','--border':'#e2e8f0','--text':'#1e293b','--muted':'#64748b',
  '--table-hover':'#f0f9ff','--form-input-bg':'#ffffff','--form-input-border':'#e2e8f0','--form-input-color':'#1e293b',
  '--table-head-bg':'#e2e8f0','--table-head-color':'#475569',
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
    const isDark = localStorage.getItem('fjb-theme') === 'dark';
    const next = isDark ? 'light' : 'dark';
    localStorage.setItem('fjb-theme', next);
    localStorage.setItem('color-theme', next);
    applyTheme(next === 'dark');
  });
}

(function(){
  const t = localStorage.getItem('fjb-theme') || localStorage.getItem('color-theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
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
  var spacing = 12, vp = 16, rect = button.getBoundingClientRect();
  popover.classList.remove('hidden'); popover.style.visibility = 'hidden';
  var maxW = Math.min(280, window.innerWidth - vp * 2);
  popover.style.width = Math.max(220, maxW) + 'px';
  var left = rect.right + spacing, pw = popover.offsetWidth;
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
</script>

@include('wt.partials.assistant-chatbox', ['assistantRole' => $effectiveRole])
@include('wt.partials.form-option-datalists')
@include('wt.partials.phone-format-script')

@stack('scripts')
</body>
</html>
