<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Dashboard') — WT System</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
:root {
  --sidebar-bg:        #142b47;
  --sidebar-hover:     rgba(255,255,255,.08);
  --sidebar-active-bg: rgba(56,189,248,.18);
  --sidebar-text:      rgba(255,255,255,.65);
  --sidebar-head:      #ffffff;
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
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{background:var(--body-bg);color:var(--text);font-family:'DM Sans',sans-serif;font-size:14px;min-height:100vh}

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
.sidebar-nav::-webkit-scrollbar-thumb:hover{background:rgba(2,132,199,.4)}
.sidebar-nav:hover::-webkit-scrollbar-thumb{background:rgba(255,255,255,.14)}
.nav-section-label{font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.12em;
  color:rgba(255,255,255,.3);padding:0 8px;margin:20px 0 6px}
.nav-section-label:first-child{margin-top:4px}

.nav-link{
  display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:8px;
  color:var(--sidebar-text);text-decoration:none;font-size:13.5px;font-weight:500;
  transition:all .15s;margin-bottom:2px;cursor:pointer;border:none;background:none;width:100%;
  font-family:'DM Sans',sans-serif;text-align:left;
}
.nav-link i{font-size:16px;width:20px;text-align:center;flex-shrink:0}
.nav-link:hover{background:rgba(255,255,255,.08);color:#fff}
.nav-link.active,.nav-link.active-sidebar{background:rgba(56,189,248,.18);color:var(--sky);font-weight:600}

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
  background:var(--surface);border-bottom:1px solid var(--border);
  padding:0 28px;min-height:60px;display:flex;align-items:center;gap:16px;flex-wrap:wrap;
  position:sticky;top:0;z-index:50;
}
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
.theme-toggle{
  width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;
  background:var(--body-bg);border:1px solid var(--border);
  color:var(--muted);cursor:pointer;font-size:16px;transition:all .15s;
}
.theme-toggle:hover{border-color:var(--accent);color:var(--accent)}
.page-body{padding:24px 28px;flex:1}

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
.stat-value{font-family:'DM Sans',sans-serif;font-size:30px;font-weight:800;color:var(--text);line-height:1}
.stat-label{font-size:12px;color:var(--muted);margin-top:4px;font-weight:500}

/* ── TABLE CARD ── */
.table-card{background:var(--surface);border:1px solid var(--border);border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.08),0 4px 16px rgba(0,0,0,.06)}
.table-card-header{
  padding:16px 20px;border-bottom:1px solid var(--border);
  display:flex;align-items:center;gap:12px;background:var(--surface);
}
.table-card-title{font-family:'DM Sans',sans-serif;font-size:14px;font-weight:700;color:var(--text);flex:1}
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
  font-family:'DM Sans',sans-serif;font-size:14px;transition:border-color .2s;
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
  padding:9px 20px;font-family:'DM Sans',sans-serif;font-size:13px;font-weight:600;
  cursor:pointer;transition:all .15s;text-decoration:none;
  display:inline-flex;align-items:center;gap:7px;
}
.btn-primary-custom:hover{background:var(--navy-mid,#254a78);color:#fff;transform:translateY(-1px)}
.btn-secondary-custom{
  background:#fff;color:var(--text);border:1.5px solid var(--border);border-radius:8px;
  padding:9px 18px;font-size:13px;font-weight:500;cursor:pointer;
  transition:all .15s;text-decoration:none;display:inline-flex;align-items:center;gap:7px;
  font-family:'DM Sans',sans-serif;
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

/* ── DATATABLES ── */
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
  font-size:12px !important;font-weight:600 !important;font-family:'DM Sans',sans-serif !important;
  padding:4px 10px !important;line-height:1.5 !important;
  border-radius:6px !important;border:1px solid var(--border) !important;
  background:transparent !important;color:var(--muted) !important;transition:all .15s;
}
.dataTables_wrapper .pagination .page-item.active .page-link{background:var(--navy,#142b47) !important;border-color:var(--navy,#142b47) !important;color:#fff !important;}
.dataTables_wrapper .pagination .page-item .page-link:hover{background:#f1f5f9 !important;color:var(--text) !important;}
.dataTables_wrapper .dataTables_info{font-size:13px;color:var(--muted);font-family:'DM Sans',sans-serif;padding-top:6px;}
table.dataTable tbody tr,
table.dataTable tbody tr.odd,
table.dataTable tbody tr.even{background-color:var(--surface) !important;color:var(--text) !important}
table.dataTable tbody tr:hover,
table.dataTable tbody tr.odd:hover,
table.dataTable tbody tr.even:hover{background-color:var(--table-hover) !important}
table.dataTable tbody td{color:var(--text) !important}
code{color:var(--accent);background:rgba(2,132,199,.08);padding:1px 5px;border-radius:4px;font-size:12px}

/* ── MOBILE ── */
.sidebar-toggle{display:none;background:none;border:1.5px solid var(--border);
  border-radius:7px;color:var(--text);padding:6px 10px;cursor:pointer;font-size:18px;font-family:'DM Sans',sans-serif}
@media(max-width:768px){
  .sidebar{transform:translateX(-100%)}
  .sidebar.open{transform:none}
  .main-content{margin-left:0}
  .sidebar-toggle{display:flex;align-items:center}
  .page-body{padding:16px}
  .topbar{padding:0 16px}
}

/* ── WT COMPAT CLASSES ── */
.content-surface{background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:24px;box-shadow:var(--shadow);}
.dark .content-surface{background:#1f2937 !important;border-color:#374151 !important;}

/* WT dropdown nav */
.dropdown-content{display:none;padding-left:16px;margin-top:2px}
.dropdown-wrapper.open .dropdown-content{display:block}
.dropdown-chevron{transition:transform .2s;font-size:11px;margin-left:auto;flex-shrink:0}
.dropdown-wrapper.open .dropdown-chevron{transform:rotate(180deg)}
.dropdown-trigger{
  display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:8px;
  color:var(--sidebar-text);font-size:13.5px;font-weight:500;
  transition:all .15s;margin-bottom:2px;cursor:pointer;width:100%;
  background:none;border:none;font-family:'DM Sans',sans-serif;text-align:left;
}
.dropdown-trigger:hover{background:rgba(255,255,255,.08);color:#fff}
.dropdown-trigger.active-sidebar{background:rgba(56,189,248,.18);color:var(--sky);font-weight:600}
.sub-nav-link{
  display:flex;align-items:center;gap:8px;padding:7px 12px;border-radius:6px;
  color:rgba(255,255,255,.55);font-size:13px;text-decoration:none;
  transition:all .15s;margin-bottom:1px;
}
.sub-nav-link:hover{background:rgba(255,255,255,.08);color:#fff}
.sub-nav-link.active{color:var(--sky);font-weight:600;background:rgba(56,189,248,.1)}
.pending-nav-badge{background:#ef4444;color:#fff;border-radius:20px;padding:1px 6px;font-size:10px;font-weight:700;margin-left:auto;flex-shrink:0}
.approval-new-badge{margin-left:4px}

/* WT button compat */
.wt-btn{display:inline-flex;align-items:center;justify-content:center;gap:5px;padding:6px 12px;border-radius:6px;border:1px solid var(--border);background:var(--surface);color:var(--text);font-size:12px;font-weight:600;cursor:pointer;transition:all .15s;text-decoration:none;}
.wt-btn:hover{background:var(--body-bg);border-color:var(--navy);}
.wt-btn-danger{background:rgba(239,68,68,.1);border-color:rgba(239,68,68,.3);color:#dc2626;}
.wt-btn-danger:hover{background:rgba(239,68,68,.2);color:#dc2626;}
.wt-btn-success{background:rgba(34,197,94,.1);border-color:rgba(34,197,94,.3);color:#16a34a;}
.wt-btn-success:hover{background:rgba(34,197,94,.2);color:#16a34a;}
.wt-btn-soft{display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:6px;border:1px solid var(--border);background:var(--surface);color:var(--text);font-size:11px;font-weight:600;cursor:pointer;transition:all .15s;}

/* Sidebar info popovers */
.has-info{position:relative;overflow:visible;padding-right:50px !important}
.has-info > span:not(.nav-info-slot){min-width:0;padding-right:4px}
.nav-info-slot{position:absolute;right:12px;top:50%;margin-top:-7.5px;display:inline-flex;align-items:center;justify-content:center;z-index:2}
.nav-info-btn{display:inline-flex;align-items:center;justify-content:center;width:15px;height:15px;border-radius:999px;border:1px solid rgba(148,163,184,.24);background:rgba(15,23,42,.18);color:rgba(226,232,240,.88);font-size:7px;font-weight:900;line-height:1;cursor:pointer;transition:all .18s}
.nav-info-btn:hover,.nav-info-btn.is-open{background:#0ea5e9;color:#fff;border-color:rgba(14,165,233,.72)}
.nav-info-popover{position:fixed;left:0;top:0;width:min(280px,calc(100vw - 32px));padding:12px 14px;border:1px solid rgba(148,163,184,.18);border-radius:14px;background:rgba(15,23,42,.98);color:#e2e8f0;box-shadow:0 24px 44px rgba(2,6,23,.4);font-size:11px;font-weight:700;line-height:1.55;z-index:1200}
.nav-info-popover.hidden{display:none !important}

/* Sidebar brand logo sizing */
.sidebar-brand-logo{width:28px;height:28px;object-fit:contain}

/* ── MODALS ── */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(15,23,42,.45);backdrop-filter:blur(8px);z-index:1000;align-items:center;justify-content:center;padding:20px}
.modal-overlay.active{display:flex}
.modal-box{background:#fff;border-radius:20px;width:95%;max-width:820px;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 40px 100px rgba(15,23,42,.25);overflow:hidden;border:1px solid rgba(226,232,240,.8)}
.modal-header{display:flex;justify-content:space-between;align-items:center;padding:20px 24px;border-bottom:1px solid var(--border);background:var(--surface);flex-shrink:0}
.modal-title{font-size:14px;font-weight:700;color:var(--text);text-transform:uppercase;letter-spacing:.05em}
.modal-close-btn{background:var(--body-bg);border:1px solid var(--border);border-radius:10px;padding:8px;cursor:pointer;color:var(--muted);transition:all .2s}
.modal-close-btn:hover{background:var(--body-bg);color:#ef4444}
.modal-body{padding:24px;overflow-y:auto;flex:1}
.modal-footer{display:flex;justify-content:flex-end;align-items:center;gap:12px;padding:16px 24px;border-top:1px solid var(--border);background:var(--body-bg);flex-shrink:0}

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
.logout-modal-btn-confirm{border:1px solid rgba(239,68,68,.22);background:linear-gradient(135deg,#b91c1c,#dc2626);color:#fff;box-shadow:0 8px 20px rgba(220,38,38,.2)}
.logout-modal-btn-confirm:hover{transform:translateY(-1px)}

/* ── MODERN CONFIRM MODAL ── */
.modern-confirm-overlay{display:none;position:fixed;inset:0;background:rgba(15,23,42,.52);backdrop-filter:blur(6px);z-index:9100;align-items:center;justify-content:center;padding:20px}
.modern-confirm-overlay.active{display:flex}
.modern-confirm-card{background:var(--surface);border:1px solid var(--border);border-radius:18px;width:100%;max-width:440px;box-shadow:0 24px 60px rgba(0,0,0,.22);overflow:hidden}
.modern-confirm-header{display:flex;align-items:center;gap:14px;padding:20px 22px;border-bottom:1px solid var(--border);background:var(--surface)}
.modern-confirm-icon{width:42px;height:42px;border-radius:12px;background:rgba(2,132,199,.12);color:var(--accent);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
.modern-confirm-title{font-size:14px;font-weight:700;color:var(--text);flex:1}
.modern-confirm-subtitle{font-size:11px;color:var(--muted)}
.modern-confirm-close{width:32px;height:32px;border-radius:8px;border:1px solid var(--border);background:var(--body-bg);color:var(--muted);display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:12px}
.modern-confirm-close:hover{color:#ef4444;border-color:rgba(239,68,68,.3)}
.modern-confirm-body{padding:20px 22px}
.modern-confirm-message{font-size:13px;color:var(--text);margin-bottom:14px;line-height:1.5}
.modern-confirm-label{font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;display:block;margin-bottom:6px}
.modern-confirm-textarea{width:100%;border:1.5px solid var(--border);border-radius:8px;padding:9px 12px;font-family:'DM Sans',sans-serif;font-size:13px;color:var(--text);background:var(--body-bg);resize:vertical;min-height:80px;outline:none;transition:border-color .2s}
.modern-confirm-textarea:focus{border-color:var(--sky-dark);box-shadow:0 0 0 3px rgba(56,189,248,.14)}
.modern-confirm-footer{display:flex;gap:10px;justify-content:flex-end;padding:14px 22px;border-top:1px solid var(--border);background:var(--body-bg)}
.modern-confirm-btn{padding:8px 20px;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;border:none;transition:all .15s;font-family:'DM Sans',sans-serif}
.modern-confirm-btn.cancel{background:var(--surface);border:1.5px solid var(--border);color:var(--text)}
.modern-confirm-btn.cancel:hover{border-color:var(--navy);color:var(--navy)}
.modern-confirm-btn.confirm{background:var(--navy);color:#fff}
.modern-confirm-btn.confirm:hover{background:var(--navy-mid)}

/* ── TOPBAR ACTION BTN ── */
.topbar-action-btn{width:36px;height:36px;border-radius:8px;background:transparent;border:1.5px solid var(--border);cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--muted);transition:all .15s;font-size:16px;position:relative}
.topbar-action-btn:hover{background:var(--body-bg);color:var(--navy)}
.topbar-signout-btn{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:rgba(239,68,68,.08);border:1.5px solid rgba(239,68,68,.2);border-radius:8px;color:#dc2626;font-size:12px;font-weight:700;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .15s}
.topbar-signout-btn:hover{background:rgba(239,68,68,.15)}

/* ── ROLE SWITCHER ── */
.topbar-role-switcher{display:flex;align-items:center;background:var(--body-bg);border:1px solid var(--border);border-radius:8px;overflow:hidden;gap:2px;padding:3px}
.topbar-role-switcher a{display:flex;align-items:center;gap:6px;padding:5px 10px;border-radius:6px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);text-decoration:none;transition:all .15s}
.topbar-role-switcher a.active-switcher{background:var(--navy);color:#fff}
.topbar-role-switcher select{border:none;background:transparent;color:var(--muted);font-size:10px;font-weight:700;outline:none;padding:4px 8px;cursor:pointer;font-family:'DM Sans',sans-serif;text-transform:uppercase}
.topbar-return-btn{display:inline-flex;align-items:center;gap:6px;border:1px solid #bae6fd;background:#f0f9ff;padding:5px 12px;border-radius:20px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#0369a1;cursor:pointer;transition:all .15s;font-family:'DM Sans',sans-serif}
.topbar-return-btn:hover{background:#e0f2fe}

/* ── NOTIFICATION DROPDOWN ── */
#notificationDropdown{background:#fff !important;border:1px solid var(--border) !important;border-radius:12px !important;box-shadow:0 18px 42px rgba(15,23,42,.16) !important;color:var(--text) !important}
#notificationDropdown > div:first-child{background:#f8fafc !important;border-bottom:1px solid var(--border) !important}
#notificationDropdown p{color:var(--text) !important}
.notification-item-btn{display:flex;width:100%;padding:12px 18px;text-align:left;background:none;border:none;cursor:pointer;transition:background .12s}
.notification-item-btn:hover{background:#f8fafc}
.notification-item-action{font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--accent);white-space:nowrap}

/* ── WALKIE TIMELINE MODAL ── */
.global-walkie-modal{width:min(920px,calc(100vw - 32px)) !important;max-width:920px !important}
.global-walkie-summary{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px}
.global-walkie-summary-item{border:1px solid var(--border);border-radius:8px;background:var(--body-bg);padding:10px 12px}
.global-walkie-summary-label{color:var(--muted);font-size:9px;font-weight:900;letter-spacing:.14em;text-transform:uppercase}
.global-walkie-summary-value{margin-top:5px;color:var(--text);font-size:12px;font-weight:900;line-height:1.25;word-break:break-word}
.global-walkie-history{display:grid;gap:8px;max-height:320px;overflow-y:auto;padding-right:4px}
.global-walkie-history-row{display:grid;grid-template-columns:104px 1fr;gap:12px;border:1px solid var(--border);border-radius:8px;background:var(--surface);padding:10px 12px}
.global-walkie-history-date{color:var(--muted);font-size:10px;font-weight:900;line-height:1.35}
.global-walkie-history-time{display:block;color:#94a3b8;font-size:9px;font-weight:800}
.global-walkie-history-title{margin:0;color:var(--text);font-size:12px;font-weight:900}
.global-walkie-history-detail{margin:3px 0 0;color:#475569;font-size:11px;font-weight:700;line-height:1.45}

/* ── ADMIN TABLE FOOTER ── */
.adminit-table-footer{display:flex;align-items:center;justify-content:space-between;padding:10px 16px;border-top:1px solid var(--border);font-size:12px;color:var(--muted);font-family:'DM Sans',sans-serif}
.adminit-table-info{color:var(--muted);font-size:12px}
.adminit-table-pagination{display:flex;align-items:center;gap:6px}
.adminit-page-link{padding:4px 12px;border-radius:6px;border:1px solid var(--border);background:var(--surface);color:var(--text);font-size:11px;font-weight:600;cursor:pointer;transition:all .15s;font-family:'DM Sans',sans-serif}
.adminit-page-link:hover:not(:disabled){background:var(--body-bg);border-color:var(--navy)}
.adminit-page-link:disabled{opacity:.4;cursor:not-allowed}
.adminit-page-current{display:inline-flex;align-items:center;justify-content:center;min-width:28px;height:28px;border-radius:6px;background:var(--navy);color:#fff;font-size:11px;font-weight:700;padding:0 6px}

/* ── SYSTEM SCROLL CONTROLS ── */
#systemScrollControls{position:fixed;right:20px;bottom:20px;display:flex;flex-direction:column;gap:6px;z-index:200;opacity:0;pointer-events:none;transition:opacity .2s}
#systemScrollControls.is-visible{opacity:1;pointer-events:auto}
.system-scroll-btn{width:36px;height:36px;border-radius:10px;border:1px solid var(--border);background:var(--surface);color:var(--muted);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .15s;box-shadow:var(--shadow)}
.system-scroll-btn:hover{background:var(--navy);color:#fff;border-color:var(--navy)}

/* ── INPUT UPPERCASE ── */
input[type="text"]:not([data-preserve-case="true"]):not([name="username"]),
input[type="search"],
textarea:not([data-preserve-case="true"]){text-transform:uppercase}
input[data-preserve-case="true"],textarea[data-preserve-case="true"]{text-transform:none !important}

/* ── SCROLLBAR ── */
::-webkit-scrollbar{width:5px;height:5px}
::-webkit-scrollbar-track{background:transparent}
::-webkit-scrollbar-thumb{background:#94a3b8;border-radius:10px}
.dark ::-webkit-scrollbar-thumb{background:#334155}

/* ── DARK THEME OVERRIDES ── */
.dark body{background:#111827;color:#d1d5db}
.dark .modal-box{background:#1e293b;border-color:#334155}
.dark .modal-header{background:#1e293b;border-color:#334155}
.dark .modal-body{background:#1e293b}
.dark .modal-footer{background:#0f172a;border-color:#334155}
.dark .global-walkie-summary-item,.dark .global-walkie-history-row{border-color:#334155;background:#0f172a}
.dark .global-walkie-summary-value,.dark .global-walkie-history-title{color:#f8fafc}
.dark .global-walkie-history-detail{color:#cbd5e1}
@media(max-width:760px){.global-walkie-summary,.global-walkie-history-row{grid-template-columns:1fr}}

/* Table overrides for specific WT inventory tables */
.content-surface #walkiesTable thead th,
.content-surface #maintenanceTable thead th,
.content-surface #unusedTable thead th,
.content-surface #duplicateTable thead th,
.content-surface #specialTable thead th{
  height:34px !important;padding:8px 12px !important;border:1px solid #263244 !important;
  background:#1e293b !important;color:#cbd5e1 !important;font-size:10px !important;
  font-weight:900 !important;line-height:1.2 !important;letter-spacing:.08em !important;
  text-transform:uppercase !important;white-space:nowrap !important;
}
.content-surface #walkiesTable tbody td,
.content-surface #maintenanceTable tbody td,
.content-surface #unusedTable tbody td,
.content-surface #duplicateTable tbody td,
.content-surface #specialTable tbody td{
  min-height:30px !important;padding:4px 10px !important;border:1px solid #263244 !important;
  background:#111827 !important;color:#dbe4f0 !important;font-size:11px !important;
  font-weight:700 !important;line-height:1.35 !important;vertical-align:middle !important;
}
.content-surface #walkiesTable tbody tr:hover td,
.content-surface #maintenanceTable tbody tr:hover td,
.content-surface #unusedTable tbody tr:hover td,
.content-surface #duplicateTable tbody tr:hover td,
.content-surface #specialTable tbody tr:hover td{background:#172033 !important}
html:not(.dark) body .content-surface #walkiesTable thead th,
html:not(.dark) body .content-surface #maintenanceTable thead th,
html:not(.dark) body .content-surface #unusedTable thead th,
html:not(.dark) body .content-surface #duplicateTable thead th,
html:not(.dark) body .content-surface #specialTable thead th{border-color:#e2e8f0 !important;background:#f8fafc !important;color:#64748b !important}
html:not(.dark) body .content-surface #walkiesTable tbody td,
html:not(.dark) body .content-surface #maintenanceTable tbody td,
html:not(.dark) body .content-surface #unusedTable tbody td,
html:not(.dark) body .content-surface #duplicateTable tbody td,
html:not(.dark) body .content-surface #specialTable tbody td{border-color:#eef2f7 !important;background:#ffffff !important;color:#334155 !important}
html:not(.dark) body .content-surface #walkiesTable tbody tr:hover td,
html:not(.dark) body .content-surface #maintenanceTable tbody tr:hover td,
html:not(.dark) body .content-surface #unusedTable tbody tr:hover td,
html:not(.dark) body .content-surface #duplicateTable tbody tr:hover td,
html:not(.dark) body .content-surface #specialTable tbody tr:hover td{background:#f8fafc !important}

.content-surface #walkiesTable{table-layout:fixed !important;width:2620px !important;min-width:2620px !important}
.content-surface #walkiesTable th:last-child,.content-surface #walkiesTable td:last-child{position:sticky !important;right:0 !important;z-index:30 !important;width:142px !important;min-width:142px !important;max-width:142px !important;box-shadow:-1px 0 0 rgba(148,163,184,.22) !important}
.content-surface #walkiesTable thead th:last-child{z-index:40 !important}
html:not(.dark) .content-surface #walkiesTable th:last-child{background:#f8fafc !important}
html:not(.dark) .content-surface #walkiesTable td:last-child{background:#ffffff !important}
html.dark .content-surface #walkiesTable th:last-child,.dark .content-surface #walkiesTable th:last-child{background:#172033 !important}
html.dark .content-surface #walkiesTable td:last-child,.dark .content-surface #walkiesTable td:last-child{background:#0f172a !important}
.content-surface #walkiesTable th.inventory-action-col,.content-surface #walkiesTable td.inventory-action-col{position:sticky !important;right:0 !important;z-index:32 !important;width:132px !important;min-width:132px !important;max-width:132px !important;text-align:center !important;box-shadow:-1px 0 0 rgba(148,163,184,.22) !important}
.content-surface #walkiesTable thead th.inventory-action-col{z-index:45 !important}
.content-surface .dataTables_scrollBody thead,.content-surface .dataTables_scrollBody thead tr,.content-surface .dataTables_scrollBody thead th,.content-surface .dataTables_scrollBody thead td{height:0 !important;max-height:0 !important;padding-top:0 !important;padding-bottom:0 !important;border-top:0 !important;border-bottom:0 !important;line-height:0 !important;overflow:hidden !important;visibility:collapse !important}
#inventoryTableScroll.clean-admin-table-scroll{display:block !important;max-width:100% !important;overflow-x:scroll !important;overflow-y:visible !important;scrollbar-gutter:stable both-edges !important;scrollbar-width:thin !important;scrollbar-color:#9ca3af transparent !important;cursor:grab !important;-webkit-overflow-scrolling:touch !important}
#inventoryTableScroll.clean-admin-table-scroll:active{cursor:grabbing !important}
</style>
@stack('styles')
</head>
<body id="main-body" class="transition-opacity duration-500">

@php
    $actualRole = Auth::guard('wt')->user()->role;
    $effectiveRole = $actualRole === 'admin_it'
        ? session('view_mode', $actualRole)
        : $actualRole;
    $isAdminItView = $effectiveRole === 'admin_it';
    $accountRoleLabel = $actualRole === 'admin_it' ? 'ICT' : 'Executive';
    $impersonatorAdminItId = session('impersonator_admin_it_id');
    $isExecutiveImpersonation = $actualRole === 'admin' && filled($impersonatorAdminItId);
    $executiveSwitcherAccounts = $actualRole === 'admin_it'
        ? \App\Models\WT\User::where('role', 'admin')
            ->orderBy('name')
            ->orderBy('staff_no')
            ->get(['id', 'staff_no', 'name', 'dept_name'])
        : collect();
    $headerUnreadNotifications = Auth::guard('wt')->user()->unreadNotifications()->count();
    $headerNotifications = Auth::guard('wt')->user()->notifications()->latest()->take(8)->get();
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

    $resolveNotificationUrl = function ($notification) use ($effectiveRole) {
        $storedUrl = $notification->data['url'] ?? null;
        if (is_string($storedUrl) && $storedUrl !== '') {
            return $storedUrl;
        }
        $title = strtolower((string) ($notification->data['title'] ?? ''));
        $message = strtolower((string) ($notification->data['message'] ?? ''));
        $category = strtolower((string) ($notification->data['category'] ?? ''));
        $text = trim($title . ' ' . $message . ' ' . $category);
        if (str_contains($text, 'password')) {
            return route('wt.admin.it.index');
        }
        if (str_contains($text, 'damage report') || str_contains($text, 'laporan kerosakan')) {
            if ($effectiveRole === 'admin_it') {
                return str_contains($text, 'submitted') || str_contains($text, 'forwarded') || str_contains($text, 'menunggu')
                    ? route('wt.admin.requests.index')
                    : route('wt.admin.requests.history');
            }
            return route('wt.admin.all.status', ['view' => 'damages']);
        }
        if (str_contains($text, 'ready for pickup') || str_contains($text, 'ready to collect') || str_contains($text, 'pick up walkie')) {
            return route('wt.admin.all.status');
        }
        if ($effectiveRole === 'admin_it') {
            return in_array($category, ['approved', 'rejected'], true)
                ? route('wt.admin.requests.history')
                : route('wt.admin.requests.index');
        }
        return route('wt.admin.all.status');
    };
@endphp

@php
    $inventoryNavOnInventory = request()->routeIs('admin.walkies.index') || request()->routeIs('admin.walkies.create');
    $inventoryNavOnMaintenance = request()->routeIs('admin.maintenance.index') || request()->routeIs('admin.maintenance.create');
    $inventoryNavOnDuplicate = request()->routeIs('admin.walkies.duplicateIds') || request()->routeIs('admin.walkies.create.duplicate');
    $inventoryNavOnSpecialUse = request()->routeIs('admin.walkies.specialUse') || request()->routeIs('admin.walkies.create.specialUse');
    $inventoryManagementOpen = $inventoryNavOnInventory || $inventoryNavOnMaintenance || $inventoryNavOnDuplicate || $inventoryNavOnSpecialUse;
    $approvalNavOnPending = request()->routeIs('admin.requests.index');
    $approvalNavOnHistory = request()->routeIs('admin.requests.history');
    $approvalManagementOpen = $approvalNavOnPending || $approvalNavOnHistory;
    $faultyNavOnUserReports = request()->routeIs('admin.faultyReports.*');
    $faultyNavOnThreeMonths = request()->routeIs('admin.reports.faulty3Months');
    $faultyManagementOpen = $faultyNavOnUserReports || $faultyNavOnThreeMonths;
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
    <div class="dropdown-wrapper {{ $inventoryManagementOpen ? 'open' : '' }}">
      <button class="dropdown-trigger has-info {{ $inventoryManagementOpen ? 'active-sidebar' : '' }}" onclick="toggleDropdown(this)">
        <i class="fas fa-layer-group" style="width:20px;text-align:center;flex-shrink:0;font-size:15px"></i>
        <span style="flex:1">Inventory Tools</span>
        @include('wt.partials.sidebar-info', ['text' => 'Open inventory management tools including inventory list, repair monitoring, duplicate IDs, and special use units.'])
        <i class="fas fa-chevron-down dropdown-chevron"></i>
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
      <button class="dropdown-trigger has-info {{ $approvalManagementOpen ? 'active-sidebar' : '' }}" onclick="toggleDropdown(this)">
        <i class="fas fa-inbox" style="width:20px;text-align:center;flex-shrink:0;font-size:15px"></i>
        <span style="flex:1">Approvals
          @if($approvalBadgeCount > 0)
          <span class="pending-nav-badge approval-new-badge" style="margin-left:4px">New</span>
          @endif
        </span>
        @include('wt.partials.sidebar-info', ['text' => 'Review approvals and open ICT approval history.'])
        <i class="fas fa-chevron-down dropdown-chevron"></i>
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
      <button class="dropdown-trigger has-info {{ $faultyManagementOpen ? 'active-sidebar' : '' }}" onclick="toggleDropdown(this)">
        <i class="fa-solid fa-triangle-exclamation" style="width:20px;text-align:center;flex-shrink:0;font-size:15px"></i>
        <span style="flex:1">Faulty Reports</span>
        @include('wt.partials.sidebar-info', ['text' => 'Consolidated view for walkie requests, handovers, and faulty reports status.'])
        <i class="fas fa-chevron-down dropdown-chevron"></i>
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
    <a href="{{ route('wt.admin.walkies.myInventory') }}" class="nav-link has-info {{ request()->routeIs('admin.walkies.myInventory') ? 'active-sidebar' : '' }}">
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
    <div class="dropdown-wrapper {{ $reqOpen ? 'open' : '' }}">
      <button class="dropdown-trigger has-info {{ $reqOpen ? 'active-sidebar' : '' }}" onclick="toggleDropdown(this)">
        <i class="fas fa-plus-circle" style="width:20px;text-align:center;flex-shrink:0;font-size:15px"></i>
        <span style="flex:1">Request Walkie Talkie</span>
        @include('wt.partials.sidebar-info', ['text' => 'Submit a walkie talkie request for yourself or on behalf of a recipient. ICT will assign the available unit later.'])
        <i class="fas fa-chevron-down dropdown-chevron"></i>
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
      $sysCtrlOpen = request()->routeIs('admin.users.index') || request()->routeIs('admin.activity.index');
    @endphp
    <div class="dropdown-wrapper {{ $sysCtrlOpen ? 'open' : '' }}">
      <button class="dropdown-trigger has-info {{ $sysCtrlOpen ? 'active-sidebar' : '' }}" onclick="toggleDropdown(this)">
        <i class="fas fa-sliders-h" style="width:20px;text-align:center;flex-shrink:0;font-size:15px"></i>
        <span style="flex:1">System Control</span>
        @include('wt.partials.sidebar-info', ['text' => 'Open system management tools for user accounts and activity logs.'])
        <i class="fas fa-chevron-down dropdown-chevron"></i>
      </button>
      <div class="dropdown-content">
        <a href="{{ route('wt.admin.users.index') }}" class="sub-nav-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
          <i class="fas fa-users" style="font-size:12px;width:14px"></i> Users Control
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
    <div class="topbar-left">
      <div class="topbar-title">@yield('page_title', @yield('title', 'Dashboard'))</div>
      <div class="topbar-breadcrumb">
        FGV Johor Bulkers &rsaquo;
        <a href="{{ $isAdminItView ? route('wt.admin.dashboard') : route('wt.admin.requests.create.shared') }}" style="color:var(--muted);text-decoration:none">Dashboard</a>
        &rsaquo; @yield('page_title', @yield('title', 'Dashboard'))
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
                {{ strtoupper($executiveAccount->full_name ?: ($executiveAccount->username ?? $executiveAccount->name ?? '')) }}{{ isset($executiveAccount->dept_name) && $executiveAccount->dept_name ? ' - ' . strtoupper($executiveAccount->dept_name) : '' }}
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

      {{-- Notification bell --}}
      <div style="position:relative" id="notifWrap">
        <button id="notificationToggle" type="button" class="topbar-action-btn" title="Notifications">
          <i class="fas fa-bell" style="font-size:16px"></i>
          @if($headerUnreadNotifications > 0)
          <span style="position:absolute;top:-5px;right:-5px;min-width:18px;height:18px;background:#dc2626;color:#fff;border-radius:9px;font-size:10px;font-weight:700;display:flex;align-items:center;justify-content:center;padding:0 4px;border:2px solid var(--surface)">{{ $headerUnreadNotifications > 99 ? '99+' : $headerUnreadNotifications }}</span>
          @endif
        </button>
        <div id="notificationDropdown" class="hidden" style="position:absolute;top:calc(100% + 10px);right:0;width:320px;max-width:86vw;background:#fff;border:1px solid var(--border);border-radius:12px;box-shadow:0 18px 42px rgba(15,23,42,.16);z-index:7000;overflow:hidden">
          <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-bottom:1px solid var(--border);background:var(--body-bg)">
            <div style="font-size:12px;font-weight:700;color:var(--text);text-transform:uppercase;letter-spacing:.08em">Notifications</div>
            @if($headerUnreadNotifications > 0)
            <form action="{{ route('wt.notifications.read_all') }}" method="POST">
              @csrf
              <button style="background:none;border:none;cursor:pointer;font-size:10px;font-weight:700;color:var(--accent);font-family:inherit;text-transform:uppercase;letter-spacing:.05em">Mark all read</button>
            </form>
            @endif
          </div>
          <div style="max-height:360px;overflow-y:auto">
            @forelse($headerNotifications as $notification)
            @php $notificationUrl = $resolveNotificationUrl($notification); @endphp
            <div style="border-bottom:1px solid #f8fafc;{{ is_null($notification->read_at) ? 'background:#f0f9ff' : 'background:#fff' }}">
              <form action="{{ route('wt.notifications.read', $notification->id) }}" method="POST">
                @csrf
                <input type="hidden" name="redirect_url" value="{{ $notificationUrl }}">
                <button type="submit" class="notification-item-btn">
                  <span style="min-width:0;flex:1">
                    <span style="display:block;font-size:10px;font-weight:{{ is_null($notification->read_at) ? '700' : '600' }};color:var(--text);text-transform:uppercase;letter-spacing:.06em">{{ $notification->data['title'] ?? 'Notification' }}</span>
                    <span style="display:block;margin-top:3px;font-size:11px;color:var(--muted);line-height:1.4">{{ $notification->data['message'] ?? '' }}</span>
                    <span style="display:block;margin-top:3px;font-size:10px;color:#94a3b8">{{ $notification->created_at?->diffForHumans() }}</span>
                  </span>
                  <span class="notification-item-action">{{ is_null($notification->read_at) ? 'Read' : 'Open' }}</span>
                </button>
              </form>
            </div>
            @empty
            <div style="padding:32px 16px;text-align:center;color:var(--muted);font-size:13px">No notifications yet.</div>
            @endforelse
          </div>
        </div>
      </div>

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
      <form id="logout-form" action="{{ route('wt.logout') }}" method="POST" class="d-none" style="display:none">@csrf</form>
      <button onclick="handleLogout()" class="topbar-signout-btn" title="Sign out">
        Sign Out <i class="fas fa-sign-out-alt"></i>
      </button>
    </div>
  </div>

  <div class="page-body">
    @include('wt.partials.flash-alerts')
    @yield('content')
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
        <div style="display:flex;flex-direction:column;gap:16px;font-size:13px;color:var(--text)">
          <div style="display:flex;gap:12px">
            <div style="min-width:28px;height:28px;border-radius:8px;border:1px solid var(--border);background:var(--body-bg);display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:900;color:var(--accent)">01</div>
            <div>
              <div style="font-size:11px;font-weight:700;color:var(--text);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Device Responsibility</div>
              <div style="font-size:12px;line-height:1.6">All users are responsible for the safe keeping of assigned walkie talkie units. Any loss or damage due to negligence must be reported immediately.</div>
            </div>
          </div>
          <div style="display:flex;gap:12px">
            <div style="min-width:28px;height:28px;border-radius:8px;border:1px solid var(--border);background:var(--body-bg);display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:900;color:var(--accent)">02</div>
            <div>
              <div style="font-size:11px;font-weight:700;color:var(--text);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Usage Restrictions</div>
              <div style="font-size:12px;line-height:1.6">Devices are strictly for official FGV Johor Bulkers business use only. Unauthorized modifications or tampering with settings is prohibited.</div>
            </div>
          </div>
          <div style="display:flex;gap:12px">
            <div style="min-width:28px;height:28px;border-radius:8px;border:1px solid var(--border);background:var(--body-bg);display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:900;color:var(--accent)">03</div>
            <div>
              <div style="font-size:11px;font-weight:700;color:var(--text);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Return Policy</div>
              <div style="font-size:12px;line-height:1.6">Temporary units must be returned by the specified end date. Permanent units must be returned upon resignation or transfer from the current department.</div>
            </div>
          </div>
          <div style="padding:12px;border-radius:10px;background:#fef3c7;border:1px solid #fde68a;display:flex;gap:10px">
            <i class="fas fa-info-circle" style="color:#d97706;margin-top:2px;font-size:13px"></i>
            <div style="font-size:11px;font-weight:600;color:#92400e;text-transform:uppercase;letter-spacing:.06em;line-height:1.5">Note: Full documentation and detailed legal terms are available upon request from the ICT Department.</div>
          </div>
        </div>
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
    <div class="modal-body" style="padding:24px">
      <div id="globalWalkieTimelineSummary" class="global-walkie-summary"></div>
      <div style="margin-top:20px">
        <p style="margin-bottom:12px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.14em;color:var(--muted)">History</p>
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
  '--sidebar-bg':    '#1a2235',
  '--sidebar-hover': '#243044',
  '--body-bg':       '#111827',
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
  '--sidebar-bg':    '#142b47',
  '--sidebar-hover': 'rgba(255,255,255,.08)',
  '--body-bg':       '#f1f5f9',
  '--surface':       '#ffffff',
  '--surface2':      '#f8fafc',
  '--border':        '#e2e8f0',
  '--text':          '#1e293b',
  '--muted':         '#64748b',
  '--table-hover':   '#f0f9ff',
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
  // Sync IT-style icon
  const icon = document.getElementById('themeIcon');
  if (icon) icon.className = dark ? 'bi bi-moon-fill' : 'bi bi-sun-fill';
  // Sync FA icons
  const darkIcon = document.getElementById('theme-toggle-dark-icon');
  const lightIcon = document.getElementById('theme-toggle-light-icon');
  if (darkIcon && lightIcon) {
    darkIcon.style.display = dark ? 'none' : 'inline-block';
    lightIcon.style.display = dark ? 'inline-block' : 'none';
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
    if (dw !== wrapper) dw.classList.remove('open');
  });
  if (!isOpen) wrapper.classList.add('open');
  else wrapper.classList.remove('open');
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
