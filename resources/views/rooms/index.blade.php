@extends('layouts.app')

@section('title', 'Meeting Rooms')

@section('styles')
<style>
    /* Ported Styles from rooms.php */
    .pub-container { max-width: 100%; margin: 0; padding: 0; }
    .pub-controls { display:flex; align-items:center; gap:.75rem; flex-wrap:wrap; margin-bottom:1.1rem; }
    .pub-view-tabs { display:flex; gap:.25rem; background:var(--bg-faded, #f1f5f9); padding:.22rem; border-radius:8px; }
    .pvt-btn { padding:.32rem .85rem; border-radius:6px; font-size:.8rem; font-weight:600; color:#64748b; text-decoration:none; transition:background .15s,color .15s; }
    .pvt-active { background:#fff !important; color:#003b95 !important; box-shadow:0 1px 3px rgba(0,0,0,.1); }
    .pub-date-nav { display:flex; align-items:center; gap:.5rem; }
    .pub-datepicker-lbl { position:relative; display:flex; align-items:center; gap:.35rem; cursor:pointer; border:1.5px solid var(--border); border-radius:7px; padding:.28rem .6rem; background:#fff; }
    .pub-datepicker-lbl .pub-cal-ico { color:#64748b; flex-shrink:0; }
    .pub-datepicker-lbl .pub-date-text { font-size:.85rem; font-weight:700; color:#1e293b; white-space:nowrap; }
    /* native input kept for the picker, but visually hidden so only one date label shows */
    .pub-datepicker-lbl input[type=date] { position:absolute; left:0; top:0; width:1px; height:1px; opacity:0; padding:0; margin:0; border:0; pointer-events:none; }

    /* Centralized (centered) custom date picker */
    .rbdp-overlay { display:none; position:fixed; inset:0; background:rgba(15,23,42,.45); z-index:1000; align-items:center; justify-content:center; padding:1rem; }
    .rbdp-overlay.active { display:flex; }
    .rbdp-modal { background:#fff; border-radius:16px; box-shadow:0 20px 50px rgba(0,0,0,.25); width:330px; max-width:92vw; padding:1.15rem 1.2rem 1rem; }
    .rbdp-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:.9rem; }
    .rbdp-title { font-size:1rem; font-weight:800; color:#1e293b; }
    .rbdp-nav { width:34px; height:34px; border:none; background:#f1f5f9; border-radius:9px; cursor:pointer; font-size:1.25rem; line-height:1; color:#334155; display:flex; align-items:center; justify-content:center; transition:background .15s; }
    .rbdp-nav:hover { background:#e2e8f0; }
    .rbdp-weekdays { display:grid; grid-template-columns:repeat(7,1fr); gap:.25rem; margin-bottom:.35rem; }
    .rbdp-weekdays span { text-align:center; font-size:.68rem; font-weight:800; color:#94a3b8; text-transform:uppercase; }
    .rbdp-grid { display:grid; grid-template-columns:repeat(7,1fr); gap:.25rem; }
    .rbdp-cell { aspect-ratio:1; display:flex; align-items:center; justify-content:center; font-size:.85rem; font-weight:600; color:#334155; border-radius:9px; cursor:pointer; transition:background .12s, color .12s; }
    .rbdp-cell.empty { cursor:default; }
    .rbdp-cell:not(.empty):hover { background:#E6F1FB; color:#185FA5; }      /* light blue on hover */
    .rbdp-cell.today { color:#185FA5; font-weight:800; }
    .rbdp-cell.selected, .rbdp-cell.selected:hover { background:#003b95; color:#fff; }  /* dark blue when selected */
    .rbdp-footer { margin-top:.9rem; display:flex; justify-content:flex-end; }
    .rbdp-today-link { background:none; border:none; color:#185FA5; font-weight:700; font-size:.82rem; cursor:pointer; padding:.3rem .55rem; border-radius:7px; }
    .rbdp-today-link:hover { background:#E6F1FB; }
    [data-theme="dark"] .rbdp-modal { background:#1e293b; }
    [data-theme="dark"] .rbdp-title { color:#f1f5f9; }
    [data-theme="dark"] .rbdp-nav { background:#334155; color:#e2e8f0; }
    [data-theme="dark"] .rbdp-cell { color:#cbd5e1; }

    .pub-day-stats { display:flex; align-items:stretch; background:#fff; border-radius:10px; box-shadow:var(--shadow); margin-bottom:.75rem; overflow:hidden; border:1px solid var(--border); }
    .pub-dstat { flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; padding:.7rem .35rem; gap:.1rem; border-right:1px solid #f1f5f9; }
    .pub-dstat:last-child { border-right:none; }
    .pub-dstat-num { font-size:1.4rem; font-weight:800; color:var(--text); line-height:1; }
    .pub-dstat-lbl { font-size:.65rem; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:.04em; text-align:center; }     
    .pub-dstat-green .pub-dstat-num { color:#16a34a; }
    .pub-dstat-blue  .pub-dstat-num { color:#1d4ed8; }
    .pub-dstat-red   .pub-dstat-num { color:#dc2626; }
    .rb-today-btn { background:#fff; border:1.5px solid var(--border); border-radius:7px; padding:.28rem .75rem; font-size:.8rem; font-weight:700; color:#334155; cursor:pointer; text-decoration:none; }

    /* Timeline & Grid */
    .rb-grid-container { background:#fff; border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow); position:relative; overflow:hidden; }
    .rb-timeline-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .rb-timeline-inner { min-width: 800px; }
    .rb-grid-header { display:flex; background:#f8fafc; border-bottom:1px solid var(--border); }
    .rb-grid-corner { width:220px; border-right:1px solid var(--border); flex-shrink:0; }
    .rb-grid-tlabels { flex:1; display:flex; position:relative; height:40px; align-items:center; }
    .rb-tlabel { position:absolute; transform:translateX(-50%); font-size:.7rem; font-weight:700; color:#94a3b8; }
    
    .rb-room-row { display:flex; border-bottom:1px solid #f1f5f9; min-height:85px; transition:background .1s; }
    .rb-room-row:last-child { border-bottom:none; }
    .rb-room-row:hover { background:#fcfdfe; }
    .rb-room-meta-col { width:220px; border-right:1px solid var(--border); padding:.85rem 1rem; flex-shrink:0; display:flex; flex-direction:column; justify-content:center; gap:.25rem; }
    .rb-rm-name { font-size:.95rem; font-weight:800; color:var(--navy); line-height:1.2; }
    .rb-rm-sub { font-size:.72rem; color:#64748b; font-weight:500; }
    .rb-timeline-col { flex:1; position:relative; background-image: linear-gradient(90deg, #f1f5f9 1px, transparent 1px); background-size: calc(100% / 13) 100%; }
    
    .rb-booking-bar { position:absolute; top:12px; height:60px; border-radius:8px; padding:.4rem .6rem; cursor:pointer; transition:transform .15s, box-shadow .15s; z-index:2; overflow:hidden; border:1px solid rgba(0,0,0,0.05); }
    .rb-booking-bar:hover { transform:translateY(-1px); box-shadow:0 4px 12px rgba(0,0,0,0.08); z-index:10; }
    .rb-bar-title { font-size:.72rem; font-weight:800; line-height:1.1; margin-bottom:2px; display:block; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .rb-bar-time { font-size:.65rem; font-weight:600; opacity:.85; display:block; }
    .rb-status-dot { width:6px; height:6px; border-radius:50%; display:inline-block; margin-right:4px; }
    
    .rb-now-line { position:absolute; top:0; bottom:0; width:2px; background:#ef4444; z-index:5; pointer-events:none; }
    .rb-now-line::after { content:''; position:absolute; top:0; left:50%; transform:translateX(-50%); border-left:5px solid transparent; border-right:5px solid transparent; border-top:6px solid #ef4444; }

    /* Status Colors */
    .st-Pending { background:#fefce8; border-color:#fde68a; color:#713f12; }
    .st-Pending .rb-status-dot { background:#eab308; }
    .st-Approved { background:#f0fdf4; border-color:#bbf7d0; color:#166534; }
    .st-Approved .rb-status-dot { background:#22c55e; }
    .st-EditRequested { background:#eff6ff; border-color:#bfdbfe; color:#1e40af; }
    .st-EditRequested .rb-status-dot { background:#3b82f6; }
    .st-CancelRequested { background:#fef2f2; border-color:#fecaca; color:#991b1b; }
    .st-CancelRequested .rb-status-dot { background:#ef4444; }
    .st-Rejected { background:#fef2f2; border-color:#fecaca; color:#991b1b; }
    .st-Rejected .rb-status-dot { background:#ef4444; }

    /* Modals & Other */
    .modal-box.modal-lg { max-width: 900px; }
    .modal-box.modal-sm { max-width: 400px; }
    .clock-picker  { display:flex; gap:.75rem; align-items:flex-end; }
    .clock-col     { flex:1; position:relative; }
    .clock-arrow   { padding-bottom:1.05rem; color:#94a3b8; flex-shrink:0; }
    .clock-display { background:#fff; border:1.5px solid var(--border); border-radius:10px; padding:.65rem .9rem; cursor:pointer; display:flex; align-items:center; justify-content:space-between; gap:.5rem; transition:all .2s; }
    .clock-display:hover { border-color:#3b82f6; background:#f8fafc; }
    .clock-display.open  { border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,0.1); }
    .clock-display.locked { opacity:.5; cursor:not-allowed; background:#f8fafc; }
    .clock-display.locked:hover { border-color:var(--border); background:#f8fafc; }
    .clock-lbl  { font-size:.6rem; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.05em; margin-bottom:.1rem; }
    .clock-val  { font-size:1.3rem; font-weight:800; color:#1e293b; font-variant-numeric:tabular-nums; line-height:1; }
    .clock-val.unset { color:#cbd5e1; }
    .clock-dropdown    { display:none; position:fixed; width:230px; background:#fff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 12px 30px rgba(0,0,0,.12); z-index:400; padding:.75rem; }
    .clock-section-lbl { font-size:.6rem; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:.06em; margin-bottom:.4rem; }
    .clock-divider     { height:1px; background:#f1f5f9; margin:.55rem -.75rem .55rem; }
    .clock-hour-grid   { display:grid; grid-template-columns:repeat(4,1fr); gap:.3rem; }
    .clock-min-row     { display:grid; grid-template-columns:repeat(4,1fr); gap:.3rem; }
    .clock-btn         { padding:.45rem .2rem; text-align:center; border-radius:8px; cursor:pointer; font-weight:700; color:#475569; font-size:.82rem; border:1.5px solid transparent; transition:all .12s; }
    .clock-btn:hover:not(.disabled) { background:#eff6ff; border-color:#bfdbfe; color:#1e40af; }
    .clock-btn.h-selected { background:#e0f2fe; color:#0369a1; border-color:#bae6fd; }
    .clock-btn.selected   { background:#3b82f6; color:#fff; border-color:#3b82f6; }
    .clock-btn.disabled   { opacity:.28; cursor:not-allowed; pointer-events:none; }

    @keyframes pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }

    /* Dashboard Modal Responsive Layout */
    .dash-modal-body {
        display: flex;
        gap: 1.5rem;
    }
    .dash-form-row {
        display: flex;
        gap: 1rem;
    }
    .dash-cart-panel {
        flex: 0.8;
        background: #f8fafc;
        border-radius: 12px;
        padding: 1.2rem;
        display: flex;
        flex-direction: column;
        border: 1px solid var(--border);
    }
    .dash-form-panel {
        flex: 1.2;
        display: flex;
        flex-direction: column;
        gap: 1.1rem;
    }
    .dash-room-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: .5rem;
    }

    /* Mobile overrides — 768px */
    @media (max-width: 768px) {
        /* Keep the booking modal strictly within the viewport — no sideways scroll */
        #rbDashBookModal .modal-box { max-width: 100% !important; overflow-x: hidden; }
        #rbDashBookModal .modal-body { padding: 1rem !important; }
        .dash-modal-body { flex-direction: column !important; gap: 1rem !important; }
        /* min-width:0 lets flex children shrink below their content width instead of overflowing */
        .dash-form-panel, .dash-cart-panel, .clock-col, .clock-display { min-width: 0 !important; }
        .dash-form-panel > *, .dash-cart-panel > * { max-width: 100%; }
        .dash-form-row { flex-direction: column !important; gap: 0.75rem !important; }
        .dash-room-grid { grid-template-columns: 1fr !important; gap: 0.4rem !important; }
        .rb-room-option {
            flex-direction: row !important;
            justify-content: space-between !important;
            align-items: center !important;
            padding: 0.8rem 1rem !important;
        }
        .clock-picker { flex-direction: column !important; align-items: stretch !important; gap: 0.75rem !important; }
        .clock-arrow { display: none !important; }
        .dash-cart-panel { flex: none !important; margin-top: 1rem; }


        /* Day view */
        .rb-grid-corner, .rb-room-meta-col { width: 110px !important; padding: 0.5rem !important; }
        .rb-rm-name { font-size: 0.8rem !important; word-break: break-word; }
        .rb-rm-sub { font-size: 0.65rem !important; }
        .card-header { flex-direction: column !important; align-items: flex-start !important; gap: 0.5rem; }

        /* Week view: smooth horizontal scroll on iOS */
        .rb-grid-container { -webkit-overflow-scrolling: touch; }

        /* Month view: compact cells */
        .rb-month-grid-days, .rb-month-grid-cells {
            grid-template-columns: repeat(7, minmax(0, 1fr)) !important;
        }
        .rb-month-cell { min-height: 60px !important; padding: .35rem !important; }
        .rb-m-booking-text { display: none !important; }
        .rb-m-booking-dot {
            display: inline-block !important;
            width: 7px; height: 7px; border-radius: 50%;
            margin: 1px; vertical-align: middle; flex-shrink: 0;
        }
        .rb-month-dots { display: flex !important; flex-wrap: wrap; gap: 2px; margin-top: .2rem; }
    }

    /* 640px — table → card layout */
    @media (max-width: 640px) {
        /* Pending action buttons stretch full width */
        .rb-pending-acts { width: 100% !important; justify-content: stretch !important; }
        .rb-pending-acts .btn { flex: 1 !important; justify-content: center !important; }

        /* Table → card transformation */
        .rb-m-stack thead { display: none; }
        .rb-m-stack tbody tr {
            display: block;
            border: 1px solid var(--border) !important;
            border-radius: 10px;
            margin: 0 0 .75rem;
            padding: .85rem 1rem;
            background: #fff;
        }
        .rb-m-stack tbody td {
            display: block;
            padding: .2rem 0 !important;
            border: none !important;
            text-align: left !important;
            white-space: normal !important;
        }
        .rb-m-stack tbody td[data-label]::before {
            content: attr(data-label);
            display: block;
            font-size: .6rem;
            font-weight: 800;
            text-transform: uppercase;
            color: #94a3b8;
            letter-spacing: .04em;
            margin-bottom: .15rem;
        }
        .rb-m-stack tbody td.rb-td-actions {
            margin-top: .6rem;
            padding-top: .6rem !important;
            border-top: 1px solid #f1f5f9 !important;
            display: flex !important;
            flex-wrap: wrap;
            gap: .4rem;
        }
        .rb-m-stack tbody td.rb-td-actions .btn { flex: 1; justify-content: center; }
        .rb-m-stack tbody td.rb-td-hide { display: none; }
    }

    /* 480px — very small screens */
    @media (max-width: 480px) {
        .pub-dstat-num { font-size: 1rem !important; }
        .pub-dstat-lbl { font-size: .55rem !important; }
        .pvt-btn { padding: .25rem .5rem !important; font-size: .72rem !important; }
        .rb-booking-bar { top: 6px; height: 48px; border-radius: 6px; }
        .rb-bar-title { font-size: .62rem !important; }
        .rb-bar-time { font-size: .55rem !important; }
        .rb-grid-corner, .rb-room-meta-col { width: 90px !important; padding: 0.4rem !important; }
        .rb-rm-name { font-size: 0.75rem !important; }
        .rb-timeline-col { background-size: calc(100% / 7) 100% !important; } /* fewer grid lines on small screens */
    }

    @media (max-width: 360px) {
        .pub-dstat-num { font-size: .9rem !important; }
        .pub-dstat { padding: .5rem .2rem !important; }
        .rb-grid-corner, .rb-room-meta-col { width: 80px !important; }
        .rb-rm-name { font-size: .7rem !important; }
        .rb-bar-title { display: none !important; } /* only show time if very tight */
        .rb-bar-time { font-size: .5rem !important; }
    }

    /* Dots are hidden by default on desktop */
    .rb-m-booking-dot { display: none; }
    .rb-month-dots { display: none; }

    /* Status dot colors for month view mobile dots */
    .rb-m-booking-dot.st-Pending        { background: #eab308; }
    .rb-m-booking-dot.st-Approved       { background: #22c55e; }
    .rb-m-booking-dot.st-EditRequested  { background: #3b82f6; }
    .rb-m-booking-dot.st-CancelRequested{ background: #ef4444; }
    .rb-m-booking-dot.st-Rejected       { background: #ef4444; }

    /* ── Mobile-only elements: hidden on desktop by default ── */
    .rb-mob-nav     { display: none; }
    .rb-fab         { display: none; }
    .rb-mob-dayview { display: none; }

    @media (max-width: 768px) {

        /* 1. BOTTOM NAVIGATION BAR */
        .rb-mob-nav {
            display: flex;
            position: fixed;
            bottom: 0; left: 0; right: 0;
            z-index: 500;
            background: #ffffff;
            border-top: 1px solid var(--border);
            padding-bottom: env(safe-area-inset-bottom, 0px);
            box-shadow: 0 -2px 12px rgba(0,0,0,.08);
        }
        .rb-mob-nav-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: .5rem .25rem;
            gap: .2rem;
            text-decoration: none;
            color: #94a3b8;
            font-size: .6rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .03em;
            transition: color .15s;
            min-height: 52px;
        }
        .rb-mob-nav-item.active,
        .rb-mob-nav-item:hover { color: #003b95; }
        .rb-mob-nav-item.active svg { stroke: #003b95; }
        .rb-mob-nav-item svg { width: 22px; height: 22px; flex-shrink: 0; }

        /* 2. CONTROLS STRIP — single date-nav row */
        .pub-view-tabs { display: none !important; }
        .pub-controls {
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            gap: .5rem !important;
            margin-bottom: .75rem;
        }
        .pub-controls > div:last-child { display: none !important; }
        .pub-date-nav { flex: 1; justify-content: space-between; gap: .3rem; }
        .pub-datepicker-lbl { flex: 1; justify-content: center; }
        .pub-datepicker-lbl .pub-date-text { font-size: .82rem; }
        .rb-today-btn { padding: .28rem .6rem; font-size: .78rem; white-space: nowrap; }

        /* 3. FLOATING ACTION BUTTON */
        .rb-fab {
            display: flex;
            position: fixed;
            bottom: calc(60px + env(safe-area-inset-bottom, 0px) + 1rem);
            right: 1.1rem;
            z-index: 490;
            width: 52px; height: 52px;
            border-radius: 50%;
            background: #003b95;
            color: #fff;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 18px rgba(0,59,149,.38);
            cursor: pointer;
            border: none;
            transition: transform .15s, box-shadow .15s;
        }
        .rb-fab:active { transform: scale(.94); box-shadow: 0 2px 8px rgba(0,59,149,.3); }

        /* 4. DAY VIEW — hide desktop timeline, show room cards */
        .rb-desktop-timeline { display: none !important; }
        .rb-mob-dayview { display: flex; flex-direction: column; gap: .65rem; }

        .rb-mob-room-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid var(--border);
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
            overflow: hidden;
            cursor: pointer;
            transition: box-shadow .15s;
        }
        .rb-mob-room-card:active { box-shadow: 0 2px 10px rgba(0,0,0,.10); }
        .rb-mob-card-header {
            display: flex;
            align-items: center;
            padding: .75rem 1rem;
            gap: .75rem;
            border-bottom: 1px solid #f1f5f9;
        }
        .rb-mob-card-stripe { width: 4px; align-self: stretch; border-radius: 4px; flex-shrink: 0; }
        .rb-mob-card-info { flex: 1; min-width: 0; }
        .rb-mob-card-name {
            font-size: .9rem; font-weight: 800; color: var(--navy);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .rb-mob-card-meta {
            font-size: .68rem; color: #64748b; margin-top: .15rem;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .rb-mob-avail-badge {
            font-size: .62rem; font-weight: 800;
            padding: .2rem .5rem; border-radius: 20px; white-space: nowrap; flex-shrink: 0;
        }
        .rb-mob-avail-free { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .rb-mob-avail-busy { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        .rb-mob-card-bookings {
            padding: .5rem .85rem .65rem;
            display: flex; flex-direction: column; gap: .35rem;
        }
        .rb-mob-chip {
            display: flex; align-items: center; gap: .45rem;
            padding: .38rem .6rem; border-radius: 8px;
            font-size: .72rem; cursor: pointer;
            border: 1px solid transparent;
            transition: opacity .12s; line-height: 1.35;
        }
        .rb-mob-chip:active { opacity: .75; }
        .rb-mob-chip-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
        .rb-mob-chip-time { font-weight: 800; white-space: nowrap; flex-shrink: 0; }
        .rb-mob-chip-purpose {
            flex: 1; overflow: hidden; text-overflow: ellipsis;
            white-space: nowrap; font-weight: 600;
        }
        .rb-mob-chip-booker { font-size: .65rem; opacity: .75; white-space: nowrap; flex-shrink: 0; }

        /* Chip status colors */
        .rb-mob-chip.st-Pending         { background:#fefce8; border-color:#fde68a; color:#713f12; }
        .rb-mob-chip.st-Pending         .rb-mob-chip-dot { background:#eab308; }
        .rb-mob-chip.st-Approved        { background:#f0fdf4; border-color:#bbf7d0; color:#166534; }
        .rb-mob-chip.st-Approved        .rb-mob-chip-dot { background:#22c55e; }
        .rb-mob-chip.st-EditRequested   { background:#eff6ff; border-color:#bfdbfe; color:#1e40af; }
        .rb-mob-chip.st-EditRequested   .rb-mob-chip-dot { background:#3b82f6; }
        .rb-mob-chip.st-CancelRequested { background:#fef2f2; border-color:#fecaca; color:#991b1b; }
        .rb-mob-chip.st-CancelRequested .rb-mob-chip-dot { background:#ef4444; }
        .rb-mob-chip.st-Rejected        { background:#fef2f2; border-color:#fecaca; color:#991b1b; }
        .rb-mob-chip.st-Rejected        .rb-mob-chip-dot { background:#ef4444; }

        /* 5. STATS BAR — single horizontal scroll row */
        .pub-day-stats {
            display: flex !important;
            grid-template-columns: unset !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
            scrollbar-width: none !important;
            flex-wrap: nowrap !important;
            margin-bottom: .65rem;
        }
        .pub-day-stats::-webkit-scrollbar { display: none; }
        .pub-dstat {
            flex: 0 0 auto !important;
            min-width: 80px;
            border-bottom: none !important;
            border-right: 1px solid #f1f5f9 !important;
            padding: .6rem .75rem !important;
        }
        .pub-dstat:last-child { border-right: none !important; }

        /* 6. CONTENT BOTTOM PADDING */
        .pub-container {
            padding-bottom: calc(68px + env(safe-area-inset-bottom, 0px)) !important;
        }

        .clock-dropdown {
            width: 280px !important;
            z-index: 9999 !important;
        }
    }

    /* Dark mode adjustments for mobile elements */
    [data-theme="dark"] .rb-mob-nav  { background: #1e293b; border-top-color: var(--border); }
    [data-theme="dark"] .rb-mob-room-card { background: #1e293b; }
    [data-theme="dark"] .rb-fab      { background: #1d4ed8; box-shadow: 0 4px 18px rgba(29,78,216,.4); }
</style>
@endsection


@php
    function toMinutes($t) {
        if (!$t) return 0;
        $parts = explode(':', $t);
        return (int)$parts[0] * 60 + (int)$parts[1]; 
    }
    $GRID_START = 7 * 60; 
    $GRID_END = 20 * 60; 
    $GRID_SPAN = $GRID_END - $GRID_START;
    
    $timelineLeft = function($t) use ($GRID_START, $GRID_SPAN) {
        return max(0, min(100, (toMinutes($t) - $GRID_START) / $GRID_SPAN * 100));
    };
    
    $timelineWidth = function($s, $e) use ($GRID_START, $GRID_SPAN) {
        return max(1, min(100, (toMinutes($e) - toMinutes($s)) / $GRID_SPAN * 100));
    };

    $nowMin = (int)date('H') * 60 + (int)date('i');
    $isToday = ($viewDate === date('Y-m-d'));
    $nowPct = ($isToday && $nowMin >= $GRID_START && $nowMin <= $GRID_END) ? (($nowMin - $GRID_START) / $GRID_SPAN * 100) : -1;

    $colorMap = [
        'room-blue'  => ['dot'=>'#185FA5','light'=>'#E6F1FB'],
        'room-green' => ['dot'=>'#1D9E75','light'=>'#E1F5EE'],
        'room-amber' => ['dot'=>'#BA7517','light'=>'#FAEEDA'],
        'room-teal'  => ['dot'=>'#1D9E75','light'=>'#E1F5EE'],
        'room-red'   => ['dot'=>'#A32D2D','light'=>'#FCEBEB'],
        'room-orange'=> ['dot'=>'#BA7517','light'=>'#FFF3E0'],
        'room-purple'=> ['dot'=>'#534AB7','light'=>'#EEEDFE'],
        'room-yellow'=> ['dot'=>'#BA7517','light'=>'#FFFDE7'],
    ];

    $navLabel = '';
    $prevNav = '';
    $nextNav = '';
    $ts = strtotime($viewDate);
    if ($viewMode === 'week') {
        $navLabel = date('d M', strtotime($rangeStart)) . ' – ' . date('d M Y', strtotime($rangeEnd));
        $prevNav = route('rooms.index', ['date' => date('Y-m-d', strtotime('-7 days', strtotime($rangeStart))), 'view' => 'week']);
        $nextNav = route('rooms.index', ['date' => date('Y-m-d', strtotime('+7 days', strtotime($rangeStart))), 'view' => 'week']);
    } elseif ($viewMode === 'month') {
        $navLabel = date('F Y', $ts);
        $prevNav = route('rooms.index', ['date' => date('Y-m-d', strtotime('-1 month', $ts)), 'view' => 'month']);
        $nextNav = route('rooms.index', ['date' => date('Y-m-d', strtotime('+1 month', $ts)), 'view' => 'month']);
    } else {
        $navLabel = date('l, d M Y', $ts);
        $prevNav = route('rooms.index', ['date' => date('Y-m-d', $ts - 86400), 'view' => 'day']);
        $nextNav = route('rooms.index', ['date' => date('Y-m-d', $ts + 86400), 'view' => 'day']);
    }

    $dayBookings = $allRangeBookings->filter(fn($b) => $b->booking_date === $viewDate);
    $bookingsByRoom = $dayBookings->groupBy('room_id');
    $freeCount = $rooms->filter(fn($r) => !isset($bookingsByRoom[$r->id]))->count();
@endphp

@section('content')
<div class="pub-container">
    @if($canApprove && $pendingForMe->count() > 0)
    @php
        $newBookings    = $pendingForMe->where('status', 'Pending');
        $cancelRequests = $pendingForMe->where('status', 'CancelRequested');
        $editRequests   = $pendingForMe->where('status', 'EditRequested');
    @endphp
    <style>
        .rb-approval-wrap { border: 1px solid #fed7aa; background: #fffcf9; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,.1); margin-bottom: 1.5rem; }
        .rb-approval-header { background: #fff7ed; border-bottom: 1px solid #fed7aa; padding: .85rem 1.25rem; display: flex; justify-content: space-between; align-items: center; }
        .rb-approval-header h3 { font-size: 1rem; color: #9a3412; margin: 0; display: flex; align-items: center; gap: .6rem; font-weight: 800; }
        .rb-approval-badge { font-size: .72rem; color: #c2410c; font-weight: 700; background: #ffedd5; padding: .22rem .6rem; border-radius: 20px; border: 1px solid #fed7aa; }
        .rb-panel { border-bottom: 1px solid #fed7aa; }
        .rb-panel:last-child { border-bottom: none; }
        .rb-panel-toggle { width: 100%; display: flex; align-items: center; gap: .65rem; padding: .85rem 1.25rem; background: transparent; border: none; cursor: pointer; text-align: left; transition: background .15s; }
        .rb-panel-toggle:hover { background: rgba(0,0,0,.025); }
        .rb-panel-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .rb-panel-label { font-size: .9rem; font-weight: 800; flex: 1; }
        .rb-panel-count { font-size: .7rem; font-weight: 800; padding: .2rem .55rem; border-radius: 20px; }
        .rb-panel-chevron { transition: transform .22s; flex-shrink: 0; color: #94a3b8; }
        .rb-panel-toggle[aria-expanded="false"] .rb-panel-chevron { transform: rotate(-90deg); }
        .rb-panel-body { display: none; }
        .rb-panel-body.open { display: block; }
        .rb-panel-new    .rb-panel-icon  { background: #fef9c3; color: #854d0e; }
        .rb-panel-new    .rb-panel-label { color: #78350f; }
        .rb-panel-new    .rb-panel-count { background: #fef9c3; color: #854d0e; border: 1px solid #fde68a; }
        .rb-panel-cancel .rb-panel-icon  { background: #fee2e2; color: #991b1b; }
        .rb-panel-cancel .rb-panel-label { color: #7f1d1d; }
        .rb-panel-cancel .rb-panel-count { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .rb-panel-edit   .rb-panel-icon  { background: #dbeafe; color: #1e40af; }
        .rb-panel-edit   .rb-panel-label { color: #1e3a8a; }
        .rb-panel-edit   .rb-panel-count { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
    </style>

    <div class="rb-approval-wrap">
        <div class="rb-approval-header">
            <h3>
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                Pending Approvals
            </h3>
            <span class="rb-approval-badge">{{ $pendingForMe->count() }} Action{{ $pendingForMe->count() !== 1 ? 's' : '' }} Required</span>
        </div>

        {{-- Panel: New Bookings --}}
        @if($newBookings->count() > 0)
        <div class="rb-panel rb-panel-new">
            <button class="rb-panel-toggle" aria-expanded="true" onclick="rbTogglePanel(this, 'panel-new')">
                <span class="rb-panel-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="12" y1="14" x2="12" y2="18"/><line x1="10" y1="16" x2="14" y2="16"/></svg>
                </span>
                <span class="rb-panel-label">New Bookings</span>
                <span class="rb-panel-count">{{ $newBookings->count() }}</span>
                <svg class="rb-panel-chevron" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </button>
            <div id="panel-new" class="rb-panel-body open">
                <div class="table-responsive">
                    <table class="table rb-m-stack" style="margin-bottom:0; border-collapse:separate; border-spacing:0;">
                        <thead style="background:#fefce8; border-top:1px solid #fde68a; border-bottom:1px solid #fde68a;">
                            <tr>
                                <th style="padding:.75rem 1.25rem; font-size:.7rem; color:#854d0e; text-transform:uppercase; letter-spacing:.05em;">Request Details</th>
                                <th style="padding:.75rem 1.25rem; font-size:.7rem; color:#854d0e; text-transform:uppercase; letter-spacing:.05em;">Purpose</th>
                                <th style="padding:.75rem 1.25rem; font-size:.7rem; color:#854d0e; text-transform:uppercase; letter-spacing:.05em;">Attendees</th>
                                <th style="padding:.75rem 1.25rem; font-size:.7rem; color:#854d0e; text-align:right; text-transform:uppercase; letter-spacing:.05em;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($newBookings as $p)
                            <tr style="border-bottom:1px solid #fef9c3; transition:background .15s;">
                                <td data-label="Request Details" style="padding:.9rem 1.25rem;">
                                    <div style="font-weight:800; color:var(--navy); font-size:.92rem;">{{ $p->booked_by_name }}</div>
                                    <div style="font-weight:600; color:var(--text); font-size:.82rem; margin-top:.1rem;">{{ $p->room->name }}</div>
                                    <div style="font-size:.72rem; color:#64748b; margin-top:.28rem; display:flex; align-items:center; gap:.35rem;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                        {{ date('d M Y', strtotime($p->booking_date)) }} &bull; {{ substr($p->start_time,0,5) }}–{{ substr($p->end_time,0,5) }}
                                    </div>
                                </td>
                                <td data-label="Purpose" style="padding:.9rem 1.25rem;">
                                    <div style="font-size:.83rem; color:var(--text); font-weight:500; line-height:1.4;">"{{ $p->purpose }}"</div>
                                </td>
                                <td data-label="Attendees" style="padding:.9rem 1.25rem;">
                                    <div style="font-size:.83rem; color:#475569; font-weight:600; display:flex; align-items:center; gap:.3rem;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                        {{ $p->attendees ?? 1 }}
                                    </div>
                                </td>
                                <td class="rb-td-actions" style="padding:.9rem 1.25rem; text-align:right; white-space:nowrap;">
                                    <div class="rb-pending-acts" style="display:flex; gap:.4rem; justify-content:flex-end;">
                                        <button class="btn btn-success btn-sm" onclick="confirmAction('{{ route('rooms.bookings.approve', $p->id) }}', 'Approve this booking?')">Approve</button>
                                        <button class="btn btn-danger btn-sm" onclick="openRejectModal('{{ $p->id }}', '{{ addslashes($p->booked_by_name) }}')">Reject</button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- Panel: Cancellation Requests --}}
        @if($cancelRequests->count() > 0)
        <div class="rb-panel rb-panel-cancel">
            <button class="rb-panel-toggle" aria-expanded="true" onclick="rbTogglePanel(this, 'panel-cancel')">
                <span class="rb-panel-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                </span>
                <span class="rb-panel-label">Cancellation Requests</span>
                <span class="rb-panel-count">{{ $cancelRequests->count() }}</span>
                <svg class="rb-panel-chevron" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </button>
            <div id="panel-cancel" class="rb-panel-body open">
                <div class="table-responsive">
                    <table class="table rb-m-stack" style="margin-bottom:0; border-collapse:separate; border-spacing:0;">
                        <thead style="background:#fff5f5; border-top:1px solid #fecaca; border-bottom:1px solid #fecaca;">
                            <tr>
                                <th style="padding:.75rem 1.25rem; font-size:.7rem; color:#991b1b; text-transform:uppercase; letter-spacing:.05em;">Request Details</th>
                                <th style="padding:.75rem 1.25rem; font-size:.7rem; color:#991b1b; text-transform:uppercase; letter-spacing:.05em;">Original Booking</th>
                                <th style="padding:.75rem 1.25rem; font-size:.7rem; color:#991b1b; text-transform:uppercase; letter-spacing:.05em;">Reason</th>
                                <th style="padding:.75rem 1.25rem; font-size:.7rem; color:#991b1b; text-align:right; text-transform:uppercase; letter-spacing:.05em;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cancelRequests as $p)
                            <tr style="border-bottom:1px solid #fee2e2; transition:background .15s;">
                                <td data-label="Request Details" style="padding:.9rem 1.25rem;">
                                    <div style="font-weight:800; color:var(--navy); font-size:.92rem;">{{ $p->booked_by_name }}</div>
                                    <div style="font-weight:600; color:var(--text); font-size:.82rem; margin-top:.1rem;">{{ $p->room->name }}</div>
                                    <div style="font-size:.7rem; color:#64748b; margin-top:.28rem; display:inline-block; background:#fee2e2; padding:.15rem .45rem; border-radius:20px; color:#991b1b; font-weight:700;">Cancel Request</div>
                                </td>
                                <td data-label="Original Booking" style="padding:.9rem 1.25rem;">
                                    <div style="font-size:.83rem; color:var(--text); font-weight:600;">{{ date('d M Y', strtotime($p->booking_date)) }}</div>
                                    <div style="font-size:.75rem; color:#64748b; margin-top:.1rem;">{{ substr($p->start_time,0,5) }}–{{ substr($p->end_time,0,5) }}</div>
                                    <div style="font-size:.75rem; color:#475569; margin-top:.2rem; font-style:italic;">"{{ $p->purpose }}"</div>
                                </td>
                                <td data-label="Reason" style="padding:.9rem 1.25rem;">
                                    @if($p->cancel_reason)
                                        <div style="font-size:.82rem; color:#991b1b; font-style:italic; line-height:1.4;">"{{ $p->cancel_reason }}"</div>
                                    @else
                                        <span style="font-size:.78rem; color:#94a3b8;">No reason provided</span>
                                    @endif
                                </td>
                                <td class="rb-td-actions" style="padding:.9rem 1.25rem; text-align:right; white-space:nowrap;">
                                    <div class="rb-pending-acts" style="display:flex; gap:.4rem; justify-content:flex-end;">
                                        <button class="btn btn-danger btn-sm" onclick="confirmAction('{{ route('rooms.bookings.approve-cancel', $p->id) }}', 'Approve this cancellation? The booking will be permanently removed.')">Approve</button>
                                        <button class="btn btn-ghost btn-sm" onclick="confirmAction('{{ route('rooms.bookings.reject-cancel', $p->id) }}', 'Decline cancellation? The booking will remain active.')">Decline</button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- Panel: Edit Requests --}}
        @if($editRequests->count() > 0)
        <div class="rb-panel rb-panel-edit">
            <button class="rb-panel-toggle" aria-expanded="true" onclick="rbTogglePanel(this, 'panel-edit')">
                <span class="rb-panel-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </span>
                <span class="rb-panel-label">Edit Requests</span>
                <span class="rb-panel-count">{{ $editRequests->count() }}</span>
                <svg class="rb-panel-chevron" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </button>
            <div id="panel-edit" class="rb-panel-body open">
                <div class="table-responsive">
                    <table class="table rb-m-stack" style="margin-bottom:0; border-collapse:separate; border-spacing:0;">
                        <thead style="background:#eff6ff; border-top:1px solid #bfdbfe; border-bottom:1px solid #bfdbfe;">
                            <tr>
                                <th style="padding:.75rem 1.25rem; font-size:.7rem; color:#1e40af; text-transform:uppercase; letter-spacing:.05em;">Request Details</th>
                                <th style="padding:.75rem 1.25rem; font-size:.7rem; color:#1e40af; text-transform:uppercase; letter-spacing:.05em;">Current Booking</th>
                                <th style="padding:.75rem 1.25rem; font-size:.7rem; color:#1e40af; text-transform:uppercase; letter-spacing:.05em;">Proposed Changes</th>
                                <th style="padding:.75rem 1.25rem; font-size:.7rem; color:#1e40af; text-align:right; text-transform:uppercase; letter-spacing:.05em;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($editRequests as $p)
                            <tr style="border-bottom:1px solid #dbeafe; transition:background .15s;">
                                <td data-label="Request Details" style="padding:.9rem 1.25rem;">
                                    <div style="font-weight:800; color:var(--navy); font-size:.92rem;">{{ $p->booked_by_name }}</div>
                                    <div style="font-weight:600; color:var(--text); font-size:.82rem; margin-top:.1rem;">{{ $p->room->name }}</div>
                                    @if($p->edit_reason)
                                    <div style="font-size:.72rem; color:#1e40af; font-style:italic; margin-top:.25rem;">Reason: {{ $p->edit_reason }}</div>
                                    @endif
                                </td>
                                <td data-label="Current Booking" style="padding:.9rem 1.25rem;">
                                    <div style="font-size:.83rem; color:var(--text); font-weight:600;">{{ date('d M Y', strtotime($p->booking_date)) }}</div>
                                    <div style="font-size:.75rem; color:#64748b; margin-top:.1rem;">{{ substr($p->start_time,0,5) }}–{{ substr($p->end_time,0,5) }}</div>
                                    <div style="font-size:.75rem; color:#475569; margin-top:.2rem; font-style:italic;">"{{ $p->purpose }}"</div>
                                </td>
                                <td data-label="Proposed Changes" style="padding:.9rem 1.25rem;">
                                    <div style="padding:.5rem .75rem; border-radius:8px; background:#eff6ff; border:1px solid #dbeafe; display:inline-block;">
                                        <div style="font-size:.78rem; color:#1e40af; font-weight:700;">
                                            {{ $p->proposed_room_id ? $p->proposedRoom->name : $p->room->name }}
                                        </div>
                                        <div style="font-size:.75rem; color:#3b82f6; margin-top:.15rem;">
                                            {{ date('d M Y', strtotime($p->proposed_date ?: $p->booking_date)) }}<br>
                                            {{ substr($p->proposed_start_time ?: $p->start_time,0,5) }}–{{ substr($p->proposed_end_time ?: $p->end_time,0,5) }}
                                        </div>
                                    </div>
                                </td>
                                <td class="rb-td-actions" style="padding:.9rem 1.25rem; text-align:right; white-space:nowrap;">
                                    <div class="rb-pending-acts" style="display:flex; gap:.4rem; justify-content:flex-end;">
                                        <button class="btn btn-success btn-sm" onclick="confirmAction('{{ route('rooms.bookings.approve-edit', $p->id) }}', 'Approve these changes?')">Approve</button>
                                        <button class="btn btn-danger btn-sm" onclick="confirmAction('{{ route('rooms.bookings.reject-edit', $p->id) }}', 'Decline these changes?')">Decline</button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    @auth
    @if($myPendingBookings->count() > 0)
    <div class="card mb-4" style="border:1px solid #bfdbfe; background:#f8fbff; border-radius:12px; overflow:hidden; box-shadow:0 4px 6px -1px rgba(0,0,0,0.07);">
        <div class="card-header" style="background:#eff6ff; border-bottom:1px solid #bfdbfe; padding:1rem 1.25rem; display:flex; justify-content:space-between; align-items:center;">
            <h3 style="font-size:1rem; color:#1e40af; margin:0; display:flex; align-items:center; gap:.6rem; font-weight:800;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                My Pending Requests ({{ $myPendingBookings->count() }})
            </h3>
            <span style="font-size:.75rem; color:#1d4ed8; font-weight:700; background:#dbeafe; padding:.25rem .6rem; border-radius:20px; border:1px solid #bfdbfe;">Awaiting Review</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table rb-m-stack" style="margin-bottom:0; border-collapse:separate; border-spacing:0;">
                    <thead style="background:#f0f7ff; border-bottom:1px solid #bfdbfe;">
                        <tr>
                            <th style="padding:1rem 1.25rem; font-size:.75rem; color:#1e40af; text-transform:uppercase; letter-spacing:.05em;">Booking</th>
                            <th style="padding:1rem 1.25rem; font-size:.75rem; color:#1e40af; text-transform:uppercase; letter-spacing:.05em;">Purpose</th>
                            <th style="padding:1rem 1.25rem; font-size:.75rem; color:#1e40af; text-transform:uppercase; letter-spacing:.05em;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($myPendingBookings as $pb)
                        <tr style="border-bottom:1px solid #dbeafe; transition:background .15s;">
                            <td data-label="Booking" style="padding:1rem 1.25rem;">
                                <div style="font-weight:800; color:var(--navy); font-size:.95rem;">{{ $pb->room->name }}</div>
                                <div style="font-size:.75rem; color:#64748b; margin-top:.3rem; display:flex; align-items:center; gap:.4rem;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                    {{ date('d M Y', strtotime($pb->booking_date)) }} &bull; {{ substr($pb->start_time,0,5) }}–{{ substr($pb->end_time,0,5) }}
                                </div>
                            </td>
                            <td data-label="Purpose" style="padding:1rem 1.25rem;">
                                <div style="font-size:.85rem; color:var(--text); font-weight:500; line-height:1.4;">"{{ $pb->purpose }}"</div>
                                @if($pb->status === 'EditRequested')
                                    <div style="margin-top:.6rem; padding:.5rem .75rem; border-radius:8px; background:#eff6ff; border:1px solid #dbeafe;">
                                        <div style="font-size:.7rem; font-weight:800; text-transform:uppercase; color:#1e40af; margin-bottom:.2rem;">Proposed Changes:</div>
                                        <div style="font-size:.8rem; color:#1e40af; font-weight:600;">
                                            {{ $pb->proposed_room_id ? $pb->proposedRoom->name : $pb->room->name }}<br>
                                            {{ date('d M', strtotime($pb->proposed_date ?: $pb->booking_date)) }} &bull;
                                            {{ substr($pb->proposed_start_time ?: $pb->start_time,0,5) }}–{{ substr($pb->proposed_end_time ?: $pb->end_time,0,5) }}
                                        </div>
                                        @if($pb->edit_reason)
                                        <div style="font-size:.75rem; color:#3b82f6; font-style:italic; margin-top:.3rem;">Reason: {{ $pb->edit_reason }}</div>
                                        @endif
                                    </div>
                                @elseif($pb->status === 'CancelRequested' && $pb->cancel_reason)
                                    <div style="font-size:.75rem; color:#991b1b; font-style:italic; margin-top:.4rem;">Cancel reason: {{ $pb->cancel_reason }}</div>
                                @endif
                            </td>
                            <td data-label="Status" style="padding:1rem 1.25rem;">
                                @php
                                    $myBadge = match($pb->status) {
                                        'Pending'        => ['class'=>'warning', 'label'=>'AWAITING'],
                                        'EditRequested'  => ['class'=>'info',    'label'=>'EDIT PENDING'],
                                        'CancelRequested'=> ['class'=>'danger',  'label'=>'CANCEL PENDING'],
                                        default          => ['class'=>'ghost',   'label'=>$pb->status],
                                    };
                                @endphp
                                <span class="badge badge-{{ $myBadge['class'] }}" style="font-size:.65rem; font-weight:800; letter-spacing:.02em;">{{ $myBadge['label'] }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
    @endauth

    @if($canApprove && in_array($viewMode, ['day','week','month']))
    @php
        $periodLabel = match($viewMode) {
            'week'  => 'This Week',
            'month' => 'This Month',
            default => 'Today',
        };
        $totalCount    = $allRangeBookings->count();
        $approvedCount = $allRangeBookings->where('status','Approved')->count();
        $pendingCount  = $allRangeBookings->where('status','Pending')->count();
        $editCount     = $allRangeBookings->where('status','EditRequested')->count();
        $cancelCount   = $allRangeBookings->where('status','CancelRequested')->count();
    @endphp
    <div class="pub-day-stats mb-3" style="border:1px solid var(--border);">
        <div class="pub-dstat" style="flex:none; padding:.7rem 1.1rem; border-right:1px solid #f1f5f9; min-width:90px;">
            <span class="pub-dstat-num pub-dstat-blue" style="font-size:1rem; color:#64748b;">{{ $periodLabel }}</span>
            <span class="pub-dstat-lbl">Booking Summary</span>
        </div>
        <div class="pub-dstat pub-dstat-blue">
            <span class="pub-dstat-num">{{ $totalCount }}</span>
            <span class="pub-dstat-lbl">Total</span>
        </div>
        <div class="pub-dstat pub-dstat-green">
            <span class="pub-dstat-num">{{ $approvedCount }}</span>
            <span class="pub-dstat-lbl">Approved</span>
        </div>
        <div class="pub-dstat" style="border-right:1px solid #f1f5f9;">
            <span class="pub-dstat-num" style="color:#854d0e;">{{ $pendingCount }}</span>
            <span class="pub-dstat-lbl">Pending</span>
        </div>
        <div class="pub-dstat" style="border-right:1px solid #f1f5f9;">
            <span class="pub-dstat-num" style="color:#1e40af;">{{ $editCount }}</span>
            <span class="pub-dstat-lbl">Edit Req.</span>
        </div>
        <div class="pub-dstat" style="border-right:none;">
            <span class="pub-dstat-num pub-dstat-red">{{ $cancelCount }}</span>
            <span class="pub-dstat-lbl">Cancel Req.</span>
        </div>
    </div>
    @endif

    <div class="pub-controls">
        <div class="pub-view-tabs">
            <a href="{{ route('rooms.index', ['date' => $viewDate, 'view' => 'day']) }}" class="pvt-btn {{ $viewMode === 'day' ? 'pvt-active' : '' }}">Day</a>
            <a href="{{ route('rooms.index', ['date' => $viewDate, 'view' => 'week']) }}" class="pvt-btn {{ $viewMode === 'week' ? 'pvt-active' : '' }}">Week</a>
            <a href="{{ route('rooms.index', ['date' => $viewDate, 'view' => 'month']) }}" class="pvt-btn {{ $viewMode === 'month' ? 'pvt-active' : '' }}">Month</a>
            @auth
            <a href="{{ route('rooms.index', ['date' => $viewDate, 'view' => 'my-bookings']) }}" class="pvt-btn {{ $viewMode === 'my-bookings' ? 'pvt-active' : '' }}">My Bookings</a>
            @endauth
            @if(Auth::check() && Auth::user()->isAdminIT())
              <a href="{{ route('rooms.index', ['date' => $viewDate, 'view' => 'manage']) }}" class="pvt-btn {{ $viewMode === 'manage' ? 'pvt-active' : '' }}">Manage</a>
            @endif
        </div>

        <div class="pub-date-nav">
            <a href="{{ $prevNav }}" class="rb-today-btn" title="Previous">&larr;</a>
            <label class="pub-datepicker-lbl" onclick="rbOpenDatePicker(event)">
                <svg class="pub-cal-ico" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <span class="pub-date-text">{{ $navLabel }}</span>
                <input type="date" value="{{ $viewDate }}" onchange="window.location.href='{{ route('rooms.index') }}?view={{ $viewMode }}&date=' + this.value">
            </label>
            <a href="{{ $nextNav }}" class="rb-today-btn" title="Next">&rarr;</a>
            <a href="{{ route('rooms.index', ['view' => $viewMode, 'date' => date('Y-m-d')]) }}" class="rb-today-btn">Today</a>
        </div>

        <div style="margin-left:auto; display:flex; gap:.5rem;">
            @if($viewMode === 'month' && (Auth::user()->isAdmin() || Auth::user()->isCeo()))
            <button class="btn btn-ghost btn-sm" onclick="openMonthReport()">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right:.25rem;vertical-align:middle;"><circle cx="12" cy="12" r="10"/><path d="M12 2a10 10 0 0 1 10 10H12z"/></svg>Monthly Report
            </button>
            @endif
            @canwrite
            <button class="btn btn-primary btn-sm" data-requires-active onclick="openRoomBookingModal('', '')">
                + New Booking
            </button>
            @endcanwrite
            @if($viewMode === 'manage' && Auth::user()->isAdminIT())
                <button class="btn btn-navy btn-sm" onclick="openRoomMgmtModal()">+ Add Room</button>
            @endif
        </div>
    </div>

    @if($viewMode === 'day')
    <div class="pub-day-stats">
        <div class="pub-dstat pub-dstat-blue">
            <span class="pub-dstat-num">{{ $rooms->count() }}</span>
            <span class="pub-dstat-lbl">Total Rooms</span>
        </div>
        <div class="pub-dstat pub-dstat-green">
            <span class="pub-dstat-num" id="rtStatFree">{{ $freeCount }}</span>
            <span class="pub-dstat-lbl">Available Now</span>
        </div>
        <div class="pub-dstat pub-dstat-red">
            <span class="pub-dstat-num" id="rtStatBooked">{{ $dayBookings->count() }}</span>
            <span class="pub-dstat-lbl">Bookings Today</span>
        </div>
    </div>

    <div class="rb-grid-container rb-desktop-timeline">
        <div class="rb-timeline-wrap">
            <div class="rb-timeline-inner">
                <div class="rb-grid-header">
                    <div class="rb-grid-corner"></div>
                    <div class="rb-grid-tlabels">
                        @for($h = 7; $h <= 20; $h++)
                            <div class="rb-tlabel" style="left: {{ ($h*60 - $GRID_START) / $GRID_SPAN * 100 }}%">{{ sprintf('%02d:00', $h) }}</div>
                        @endfor
                    </div>
                </div>

                @foreach($rooms as $room)
                <div class="rb-room-row">
                    <div class="rb-room-meta-col">
                        <div class="rb-rm-name">{{ $room->name }}</div>
                        <div class="rb-rm-sub">Cap: {{ $room->capacity }} • Level {{ $room->level ?? 1 }}</div>
                        @php $pics = $room->pics->pluck('name')->toArray(); @endphp
                        @if(!empty($pics))
                        <div class="rb-rm-sub" style="font-size:.65rem; color:#94a3b8;">PIC: {{ implode(', ', $pics) }}</div>
                        @endif
                    </div>
                    <div class="rb-timeline-col" data-room-id="{{ $room->id }}" onclick="openRoomBookingModal('{{ $room->id }}', '{{ $room->name }}')">
                        @if($nowPct >= 0)
                            <div class="rb-now-line" style="left: {{ $nowPct }}%"></div>
                        @endif
                        
                        @foreach($bookingsByRoom->get($room->id, collect()) as $b)
                            @php
                                $left = $timelineLeft($b->start_time);
                                $width = $timelineWidth($b->start_time, $b->end_time);
                            @endphp
                            <div class="rb-booking-bar st-{{ $b->status }}" 
                                 style="left: {{ $left }}%; width: {{ $width }}%;"
                                 onclick="event.stopPropagation(); openEditModal('{{ $b->id }}', '{{ $b->room_id }}', '{{ $b->booking_date }}', '{{ substr($b->start_time, 0, 5) }}', '{{ substr($b->end_time, 0, 5) }}', '{{ addslashes($b->purpose) }}', '{{ $b->attendees }}', '{{ $b->status }}')">
                                <span class="rb-bar-title"><span class="rb-status-dot"></span>{{ $b->purpose }}@if($b->is_full_day) <span style="font-size:.6rem; font-weight:800; text-transform:uppercase; letter-spacing:.03em; opacity:.85;">• Full Day</span>@endif</span>
                                <span class="rb-bar-time">{{ $b->is_full_day ? 'Full Day' : substr($b->start_time, 0, 5) . ' - ' . substr($b->end_time, 0, 5) }} • {{ $b->booked_by_name }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- MOBILE DAY VIEW: Room Cards (hidden on desktop via CSS) --}}
    <div class="rb-mob-dayview" id="rbMobDayView">
        @foreach($rooms as $room)
        @php
            $roomBookings = $bookingsByRoom->get($room->id, collect());
            $dotColor     = $colorMap[$room->color_class]['dot']   ?? '#185FA5';
            $bookingCount = $roomBookings->count();
            $pics         = $room->pics->pluck('name')->toArray();
        @endphp
        <div class="rb-mob-room-card" data-room-id="{{ $room->id }}"
             onclick="openRoomBookingModal('{{ $room->id }}', '{{ addslashes($room->name) }}')">
            <div class="rb-mob-card-header">
                <div class="rb-mob-card-stripe" style="background:{{ $dotColor }};"></div>
                <div class="rb-mob-card-info">
                    <div class="rb-mob-card-name">{{ $room->name }}</div>
                    <div class="rb-mob-card-meta">
                        Cap: {{ $room->capacity }}@if(!empty($pics)) &bull; PIC: {{ implode(', ', $pics) }}@endif
                    </div>
                </div>
                @if($bookingCount === 0)
                    <span class="rb-mob-avail-badge rb-mob-avail-free">Available</span>
                @else
                    <span class="rb-mob-avail-badge rb-mob-avail-busy">{{ $bookingCount }} booked</span>
                @endif
            </div>
            @if($bookingCount > 0)
            <div class="rb-mob-card-bookings">
                @foreach($roomBookings->sortBy('start_time') as $b)
                <div class="rb-mob-chip st-{{ $b->status }}"
                     onclick="event.stopPropagation(); openEditModal('{{ $b->id }}', '{{ $b->room_id }}', '{{ $b->booking_date }}', '{{ substr($b->start_time, 0, 5) }}', '{{ substr($b->end_time, 0, 5) }}', '{{ addslashes($b->purpose) }}', '{{ $b->attendees }}', '{{ $b->status }}')">
                    <span class="rb-mob-chip-dot"></span>
                    <span class="rb-mob-chip-time">{{ $b->is_full_day ? 'Full Day' : substr($b->start_time,0,5) . '–' . substr($b->end_time,0,5) }}</span>
                    <span class="rb-mob-chip-purpose">{{ $b->purpose }}</span>
                    <span class="rb-mob-chip-booker">{{ Str::limit($b->booked_by_name, 14) }}</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @endforeach
    </div>
    {{-- END MOBILE DAY VIEW --}}

    @elseif($viewMode === 'week')
        <div class="rb-grid-container" style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; min-width:800px;">
                <thead>
                    <tr style="background:#f8fafc; border-bottom:1px solid var(--border);">
                        <th style="width:200px; padding:1rem; text-align:left; border-right:1px solid var(--border);">Room</th>
                        @php
                            $current = Carbon\Carbon::parse($rangeStart);
                            $end = Carbon\Carbon::parse($rangeEnd);
                        @endphp
                        @while($current <= $end)
                            <th style="padding:1rem; text-align:center; border-right:1px solid var(--border); {{ $current->isToday() ? 'background:#eff6ff;' : '' }}">
                                <div style="font-size:.7rem; color:#64748b; text-transform:uppercase;">{{ $current->format('D') }}</div>
                                <div style="font-size:1rem; font-weight:800; color:#1e293b;">{{ $current->format('d M') }}</div>
                            </th>
                            @php $current->addDay(); @endphp
                        @endwhile
                    </tr>
                </thead>
                <tbody>
                    @foreach($rooms as $room)
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:1rem; border-right:1px solid var(--border);">
                            <div class="rb-rm-name">{{ $room->name }}</div>
                            <div class="rb-rm-sub">Cap: {{ $room->capacity }}</div>
                        </td>
                        @php $current = Carbon\Carbon::parse($rangeStart); @endphp
                        @while($current <= $end)
                            <td style="padding:.5rem; border-right:1px solid var(--border); vertical-align:top; {{ $current->isToday() ? 'background:#fcfdfe;' : '' }}">
                                @php
                                    $dayB = $allRangeBookings->filter(fn($b) => $b->room_id == $room->id && $b->booking_date == $current->toDateString());
                                @endphp
                                @foreach($dayB as $b)
                                    <div style="font-size:.65rem; padding:.25rem .4rem; border-radius:4px; margin-bottom:4px; border:1px solid rgba(0,0,0,0.05); cursor:pointer;" 
                                         class="st-{{ $b->status }}"
                                         onclick="openEditModal('{{ $b->id }}', '{{ $b->room_id }}', '{{ $b->booking_date }}', '{{ substr($b->start_time, 0, 5) }}', '{{ substr($b->end_time, 0, 5) }}', '{{ addslashes($b->purpose) }}', '{{ $b->attendees }}', '{{ $b->status }}')">
                                        <strong>{{ substr($b->start_time, 0, 5) }}–{{ substr($b->end_time, 0, 5) }}</strong><br>{{ Str::limit($b->booked_by_name, 14) }}
                                    </div>
                                @endforeach
                            </td>
                            @php $current->addDay(); @endphp
                        @endwhile
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @elseif($viewMode === 'month')
        @php
            $firstDay = Carbon\Carbon::parse($rangeStart);
            $lastDay = Carbon\Carbon::parse($rangeEnd);
            $startOfCalendar = $firstDay->copy()->startOfWeek(Carbon\Carbon::MONDAY);
            $endOfCalendar = $lastDay->copy()->endOfWeek(Carbon\Carbon::SUNDAY);
        @endphp
        <div class="rb-grid-container">
            <div class="rb-month-grid-days" style="display:grid; grid-template-columns: repeat(7, 1fr); background:#f8fafc; border-bottom:1px solid var(--border);">
                @foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $day)
                    <div style="padding:.75rem; text-align:center; font-size:.75rem; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:.05em;">{{ $day }}</div>
                @endforeach
            </div>
            <div class="rb-month-grid-cells" style="display:grid; grid-template-columns: repeat(7, 1fr); grid-auto-rows: minmax(120px, auto);">
                @php $curr = $startOfCalendar->copy(); @endphp
                @while($curr <= $endOfCalendar)
                    @php
                        $isCurrentMonth = $curr->month == $firstDay->month;
                        $dayB = $allRangeBookings->filter(fn($b) => $b->booking_date == $curr->toDateString());
                    @endphp
                    <div class="rb-month-cell" style="padding:.5rem; border-right:1px solid #f1f5f9; border-bottom:1px solid #f1f5f9; {{ $curr->isToday() ? 'background:#eff6ff;' : (!$isCurrentMonth ? 'background:#f8fafc; opacity:.5;' : '') }}">
                        <div style="text-align:right; font-size:.85rem; font-weight:800; color:{{ $curr->isToday() ? '#2563eb' : '#64748b' }}; margin-bottom:.5rem;">{{ $curr->day }}</div>
                        @foreach($dayB->take(4) as $b)
                            <div class="rb-m-booking-text st-{{ $b->status }}" style="font-size:.6rem; padding:2px 4px; border-radius:3px; margin-bottom:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; border:1px solid rgba(0,0,0,0.05); cursor:pointer;"
                                 onclick="openEditModal('{{ $b->id }}', '{{ $b->room_id }}', '{{ $b->booking_date }}', '{{ substr($b->start_time, 0, 5) }}', '{{ substr($b->end_time, 0, 5) }}', '{{ addslashes($b->purpose) }}', '{{ $b->attendees }}', '{{ $b->status }}')">
                                <strong>{{ substr($b->start_time, 0, 5) }}–{{ substr($b->end_time, 0, 5) }}</strong> {{ Str::limit($b->booked_by_name, 12) }}
                            </div>
                        @endforeach
                        @if($dayB->count() > 0)
                        <div class="rb-month-dots">
                            @foreach($dayB->take(6) as $b)
                                <span class="rb-m-booking-dot st-{{ $b->status }}" title="{{ substr($b->start_time,0,5) }} {{ $b->booked_by_name }}"
                                      onclick="openEditModal('{{ $b->id }}', '{{ $b->room_id }}', '{{ $b->booking_date }}', '{{ substr($b->start_time, 0, 5) }}', '{{ substr($b->end_time, 0, 5) }}', '{{ addslashes($b->purpose) }}', '{{ $b->attendees }}', '{{ $b->status }}')"
                                      style="cursor:pointer;"></span>
                            @endforeach
                            @if($dayB->count() > 6)
                                <span style="font-size:.55rem; color:#94a3b8; line-height:7px;">+{{ $dayB->count() - 6 }}</span>
                            @endif
                        </div>
                        @endif
                        @if($dayB->count() > 4)
                            <div class="rb-m-booking-text" style="font-size:.6rem; color:#94a3b8; text-align:center;">+{{ $dayB->count() - 4 }} more</div>
                        @endif
                    </div>
                    @php $curr->addDay(); @endphp
                @endwhile
            </div>
        </div>
    @elseif($viewMode === 'my-bookings')
        <div class="rb-grid-container">
            <div class="card-header" style="background: #f8fafc; border-bottom: 1px solid var(--border); padding: 1.25rem; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 1.1rem; color: var(--navy); margin: 0; font-weight: 800; display: flex; align-items: center; gap: .75rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                    My Booking History & Schedule
                </h3>
            </div>
            <div class="table-responsive">
                <table class="table rb-m-stack" style="width:100%; border-collapse: separate; border-spacing: 0;">
                    <thead style="background:#f8fafc; border-bottom: 2px solid var(--border);">
                        <tr>
                            <th style="padding:1.25rem; font-size: .75rem; color: #64748b; text-transform: uppercase; letter-spacing: .05em; font-weight: 800;">Meeting / Room</th>
                            <th style="padding:1.25rem; font-size: .75rem; color: #64748b; text-transform: uppercase; letter-spacing: .05em; font-weight: 800;">Date & Time</th>
                            <th style="padding:1.25rem; font-size: .75rem; color: #64748b; text-transform: uppercase; letter-spacing: .05em; font-weight: 800;">Status</th>
                            <th style="padding:1.25rem; font-size: .75rem; color: #64748b; text-transform: uppercase; letter-spacing: .05em; font-weight: 800; text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allRangeBookings as $b)
                        @php
                            $startTs = strtotime($b->booking_date . ' ' . $b->start_time);
                            $endTs = strtotime($b->booking_date . ' ' . $b->end_time);
                            $nowTs = time();
                            
                            $isLive = ($nowTs >= $startTs && $nowTs <= $endTs);
                            $isPast = ($nowTs > $endTs);
                            $isUpcoming = ($nowTs < $startTs);
                            
                            $realTimeStatus = $isLive ? 'LIVE' : ($isPast ? 'COMPLETED' : 'UPCOMING');
                            $rtColor = $isLive ? '#ef4444' : ($isPast ? '#64748b' : '#3b82f6');
                        @endphp
                        <tr style="border-bottom:1px solid #f1f5f9; transition: background .15s;">
                            <td data-label="Meeting / Room" style="padding:1.25rem;">
                                <div style="font-weight:800; color:var(--navy); font-size: 1rem;">{{ $b->purpose }}</div>
                                <div style="font-size:.8rem; color:#64748b; margin-top: .2rem; display: flex; align-items: center; gap: .4rem;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                                    {{ $b->room->name }}
                                </div>
                            </td>
                            <td data-label="Date & Time" style="padding:1.25rem;">
                                <div style="font-weight:700; color:var(--text); font-size: .9rem;">{{ date('D, d M Y', strtotime($b->booking_date)) }}</div>
                                <div style="font-size:.8rem; color:#64748b; margin-top: .15rem; font-variant-numeric: tabular-nums;">
                                    {{ substr($b->start_time,0,5) }} – {{ substr($b->end_time,0,5) }}
                                </div>
                            </td>
                            <td data-label="Status" style="padding:1.25rem;">
                                <div style="display: flex; flex-direction: column; gap: .4rem;">
                                    @php
                                        $badgeClass = match($b->status) {
                                            'Pending' => 'warning',
                                            'Approved' => 'success',
                                            'Rejected' => 'danger',
                                            'EditRequested' => 'info',
                                            'CancelRequested' => 'danger',
                                            default => 'ghost'
                                        };
                                    @endphp
                                    <span class="badge badge-{{ $badgeClass }}" style="font-size:.65rem; font-weight: 800; width: fit-content;">{{ strtoupper($b->status) }}</span>
                                    @if($b->status === 'Approved')
                                        <div style="display: flex; align-items: center; gap: .35rem; font-size: .65rem; font-weight: 800; color: {{ $rtColor }};">
                                            @if($isLive)
                                                <span style="width: 8px; height: 8px; background: #ef4444; border-radius: 50%; display: inline-block; animation: pulse 1.5s infinite;"></span>
                                            @endif
                                            {{ $realTimeStatus }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="rb-td-actions" style="padding:1.25rem; text-align:right;">
                                @if(!$isPast && in_array($b->status, ['Pending', 'Approved', 'EditRequested']))
                                    @canwrite
                                    <button class="btn btn-ghost btn-sm" onclick="openEditModal('{{ $b->id }}', '{{ $b->room_id }}', '{{ $b->booking_date }}', '{{ substr($b->start_time, 0, 5) }}', '{{ substr($b->end_time, 0, 5) }}', '{{ addslashes($b->purpose) }}', '{{ $b->attendees }}', '{{ $b->status }}')">Edit</button>
                                    <button class="btn btn-ghost btn-sm" style="color:#dc2626;" onclick="openRequestCancelModal('{{ $b->id }}', '{{ addslashes($b->purpose) }}')">Cancel</button>
                                    @else
                                    <span style="font-size: .75rem; color: #94a3b8; font-weight: 600; font-style: italic;">No actions</span>
                                    @endcanwrite
                                @else
                                    <span style="font-size: .75rem; color: #94a3b8; font-weight: 600; font-style: italic;">No actions</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="padding: 4rem 1.25rem; text-align: center; color: #94a3b8;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom: 1rem; opacity: .5;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                <p style="font-size: 1rem; font-weight: 600;">You haven't made any bookings yet.</p>
                                @canwrite<button class="btn btn-primary btn-sm mt-3" onclick="openRoomBookingModal('', '')">Create Your First Booking</button>@endcanwrite
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($allRangeBookings instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div style="padding: 1.25rem; border-top: 1px solid var(--border); background: #f8fafc;">
                    {{ $allRangeBookings->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    @elseif($viewMode === 'manage')
        <div class="rb-grid-container">
            <table class="table rb-m-stack" style="width:100%;">
                <thead style="background:#f8fafc;">
                    <tr>
                        <th style="text-align:left; padding:1rem;">Room Name</th>
                        <th style="text-align:left; padding:1rem;">Description</th>
                        <th style="text-align:center; padding:1rem;">Capacity</th>
                        <th style="text-align:left; padding:1rem;">Color</th>
                        <th style="text-align:left; padding:1rem;">PICs</th>
                        <th style="text-align:right; padding:1rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rooms as $room)
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td data-label="Room Name" style="padding:1rem;"><strong>{{ $room->name }}</strong></td>
                        <td data-label="Description" style="padding:1rem; color:#64748b; font-size:.8rem;">{{ $room->description ?: '-' }}</td>
                        <td data-label="Capacity" style="padding:1rem; text-align:center;">{{ $room->capacity }}</td>
                        <td data-label="Color" style="padding:1rem;">
                            <span style="display:inline-block; width:12px; height:12px; border-radius:3px; background:{{ $colorMap[$room->color_class]['dot'] ?? '#ccc' }}; margin-right:.4rem;"></span>
                            {{ ucfirst(str_replace('room-', '', $room->color_class)) }}
                        </td>
                        <td data-label="PICs" style="padding:1rem; font-size:.8rem;">
                            @foreach($room->pics as $pic)
                                <div style="white-space:nowrap;">L{{ $pic->pivot->level }}: {{ $pic->name }}</div>
                            @endforeach
                        </td>
                        <td class="rb-td-actions" style="padding:1rem; text-align:right;">
                            <button class="btn btn-ghost btn-sm" onclick='openRoomMgmtModal({!! json_encode($room) !!}, {!! json_encode($room->pics->pluck("id")) !!})'>Edit</button>
                            <button class="btn btn-danger btn-sm" onclick="confirmDeleteRoom('{{ $room->id }}', '{{ addslashes($room->name) }}')">Delete</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- MOBILE BOTTOM NAVIGATION (hidden on desktop via CSS) --}}
<nav class="rb-mob-nav" aria-label="View navigation">
    <a href="{{ route('rooms.index', ['date' => $viewDate, 'view' => 'day']) }}"
       class="rb-mob-nav-item {{ $viewMode === 'day' ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        Day
    </a>
    <a href="{{ route('rooms.index', ['date' => $viewDate, 'view' => 'week']) }}"
       class="rb-mob-nav-item {{ $viewMode === 'week' ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="8" y1="14" x2="16" y2="14"/></svg>
        Week
    </a>
    <a href="{{ route('rooms.index', ['date' => $viewDate, 'view' => 'month']) }}"
       class="rb-mob-nav-item {{ $viewMode === 'month' ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><circle cx="8" cy="16" r="1.5" fill="currentColor" stroke="none"/><circle cx="12" cy="16" r="1.5" fill="currentColor" stroke="none"/><circle cx="16" cy="16" r="1.5" fill="currentColor" stroke="none"/></svg>
        Month
    </a>
    @auth
    <a href="{{ route('rooms.index', ['date' => $viewDate, 'view' => 'my-bookings']) }}"
       class="rb-mob-nav-item {{ $viewMode === 'my-bookings' ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        Mine
    </a>
    @endauth
    @if(Auth::check() && Auth::user()->isAdminIT())
    <a href="{{ route('rooms.index', ['date' => $viewDate, 'view' => 'manage']) }}"
       class="rb-mob-nav-item {{ $viewMode === 'manage' ? 'active' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
        Manage
    </a>
    @endif
</nav>
{{-- END MOBILE BOTTOM NAVIGATION --}}

{{-- FLOATING ACTION BUTTON (hidden on desktop via CSS) --}}
@canwrite
<button class="rb-fab" data-requires-active onclick="openRoomBookingModal('', '')" aria-label="New Booking">
    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24"
         fill="none" stroke="currentColor" stroke-width="2.5"
         stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="5" x2="12" y2="19"/>
        <line x1="5" y1="12" x2="19" y2="12"/>
    </svg>
</button>
@endcanwrite
{{-- END FAB --}}

<!-- MODALS -->

<!-- BATCH BOOKING MODAL -->
<div class="modal" id="rbDashBookModal">
    <div class="modal-box modal-lg">
        <div class="modal-header">
            <h3>New Booking</h3>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <form action="{{ route('rooms.bookings.store') }}" method="POST" onsubmit="return validateAndSubmitBatch()">
            @csrf
            <input type="hidden" name="slots" id="rbDashSlotsJson">
            <div class="modal-body dash-modal-body">
                <!-- Left: Form -->
                <div class="dash-form-panel">
                    <div>
                        <label class="form-label" style="font-size:.72rem; font-weight:800; text-transform:uppercase; letter-spacing:.05em; color:#94a3b8; margin-bottom:.5rem; display:block;">1. Select Room</label>
                        <div class="dash-room-grid">
                            @foreach($rooms as $rm)
                                <label class="rb-room-option" id="rbOptLabel_{{ $rm->id }}" style="border:1.5px solid var(--border); border-radius:8px; padding:.6rem; cursor:pointer; display:flex; flex-direction:column; gap:.2rem; transition:all .2s;">
                                    <input type="radio" name="dash_room_radio" value="{{ $rm->id }}" style="display:none;" onchange="dashOnRoomChange(this.value)">
                                    <span style="font-size:.8rem; font-weight:800; color:var(--navy);">{{ $rm->name }}</span>
                                    <span style="font-size:.65rem; color:#94a3b8;">Cap: {{ $rm->capacity }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="dash-form-row">
                        <div style="flex:1;">
                            <label class="form-label">Booking Date</label>
                            <input type="date" id="rbDashDate" class="form-control" value="{{ $viewDate }}" min="{{ date('Y-m-d') }}" onchange="dashCheckPastDate(); dashRefreshOccupied(); buildTimePicker('start'); buildTimePicker('end'); dashUpdateFullDay(); dashSyncEndDateMin();">
                            <div id="rbDashPastWarn" style="display:none; color:#dc2626; font-size:.72rem; font-weight:600; margin-top:.3rem;">⚠️ Past day cannot book</div>
                        </div>
                        <div style="flex:1;">
                            <label class="form-label">Attendees</label>
                            <input type="number" id="rbDashAttendees" class="form-control" min="1" oninput="dashCheckCapacity()">
                            <div id="rbDashCapacityHint" style="font-size:.72rem; margin-top:.3rem; font-weight:600;"></div>
                        </div>
                    </div>

                    <input type="hidden" name="is_full_day" id="rbDashFullDayInput" value="0">
                    <div id="rbDashFullDayWrap" style="display:flex; flex-direction:column; gap:.3rem; padding:.6rem .75rem; border:1.5px solid var(--border); border-radius:8px; background:#f8fafc;">
                        <label id="rbDashFullDayLabel" style="display:flex; align-items:center; gap:.5rem; cursor:pointer; font-weight:700; font-size:.82rem; color:var(--navy);">
                            <input type="checkbox" id="rbDashFullDay" onchange="dashToggleFullDay()" style="width:1rem; height:1rem; cursor:pointer;">
                            Full Day <span style="font-weight:600; color:#64748b;">(07:00 – 20:00)</span>
                        </label>
                        <div id="rbDashFullDayHint" style="font-size:.7rem; color:#94a3b8;">Available only for future dates with no existing bookings.</div>
                        <div id="rbDashEndDateWrap" style="display:none; margin-top:.5rem; padding-top:.5rem; border-top:1px dashed var(--border);">
                            <label style="font-size:.72rem; font-weight:700; color:var(--navy); display:block; margin-bottom:.3rem;">End Date <span style="font-weight:400; color:#94a3b8;">(optional — to book multiple days)</span></label>
                            <input type="date" id="rbDashEndDate" class="form-control" style="font-size:.82rem;" onchange="dashUpdateMultiDayHint()">
                            <div id="rbDashMultiDayHint" style="font-size:.68rem; color:#475569; margin-top:.28rem; font-weight:600;"></div>
                        </div>
                    </div>

                    <div class="clock-picker" id="rbDashClockWrap">
                        <div class="clock-col">
                            <label class="form-label">Start Time</label>
                            <div class="clock-display" id="startDisplay" onclick="toggleClock('start')">
                                <div>
                                    <div class="clock-lbl">From</div>
                                    <span class="clock-val unset" id="startVal">--:--</span>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            </div>
                            <input type="hidden" id="rbDashStart">
                            <div class="clock-dropdown" id="startDrop">
                                <div class="clock-section-lbl">Hour</div>
                                <div class="clock-hour-grid" id="startHourGrid"></div>
                                <div class="clock-divider"></div>
                                <div class="clock-section-lbl">Minute</div>
                                <div class="clock-min-row" id="startMinRow"></div>
                            </div>
                        </div>
                        <div class="clock-arrow">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </div>
                        <div class="clock-col">
                            <label class="form-label">End Time</label>
                            <div class="clock-display" id="endDisplay" onclick="toggleClock('end')">
                                <div>
                                    <div class="clock-lbl">To</div>
                                    <span class="clock-val unset" id="endVal">--:--</span>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            </div>
                            <input type="hidden" id="rbDashEnd">
                            <div class="clock-dropdown" id="endDrop">
                                <div class="clock-section-lbl">Hour</div>
                                <div class="clock-hour-grid" id="endHourGrid"></div>
                                <div class="clock-divider"></div>
                                <div class="clock-section-lbl">Minute</div>
                                <div class="clock-min-row" id="endMinRow"></div>
                            </div>
                        </div>
                    </div>

                    <div id="rbDashOccSlots" style="display:none; padding:.75rem; border-radius:10px; border:1px solid #f97316; background:#fff7ed;">
                        <div style="font-size:.72rem; font-weight:800; color:#c2410c; text-transform:uppercase; margin-bottom:.5rem;">Occupied Slots</div>
                        <div id="rbDashOccList" style="display:flex; flex-wrap:wrap; gap:.4rem;"></div>
                    </div>

                    <div>
                        <label class="form-label">Purpose of Meeting</label>
                        <textarea id="rbDashPurpose" class="form-control" rows="2" placeholder="e.g. Project Alpha Sync" oninput="dashUpdateSummary()"></textarea>
                    </div>

                    <div id="rbDashConflictWarn" style="display:none; padding:.75rem; border-radius:8px; background:#fef2f2; border:1px solid #fee2e2; color:#dc2626; font-size:.8rem; font-weight:700;">
                        ⚠️ This slot overlaps with an existing booking.
                    </div>

                    <div id="rbDashSummary" style="display:none; padding:1rem; border-radius:10px; background:#f8fafc; border:1.5px dashed var(--border); font-size:.85rem; color:var(--navy); line-height:1.4;"></div>

                    @guest
                    <div style="margin-top:1rem; padding:1.25rem; background:#f0f7ff; border:1.5px solid #bfdbfe; border-radius:12px;">
                        <h4 style="font-size:.85rem; font-weight:800; color:#1e40af; margin-bottom:1rem; display:flex; align-items:center; gap:.5rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                            Confirm Identity
                        </h4>
                        <div style="display:flex; flex-direction:column; gap:.75rem;">
                            <div class="form-group">
                                <label class="form-label" style="color:#1e40af;">Staff ID</label>
                                <input type="text" name="staff_no" class="form-control" placeholder="e.g. 0000001" required
                                       autocapitalize="none" autocorrect="off" spellcheck="false"
                                       autocomplete="username" style="font-size:1rem;">
                            </div>
                            <div class="form-group">
                                <label class="form-label" style="color:#1e40af;">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="••••••••" required
                                       autocomplete="current-password" style="font-size:1rem;">
                            </div>
                        </div>
                    </div>
                    @endguest

                    <div style="display:flex; gap:.75rem;">
                        <button type="button" class="btn btn-navy" style="flex:1;" onclick="dashAddToCart()">Add to List</button>
                    </div>
                </div>

                <!-- Right: Cart -->
                <div class="dash-cart-panel">
                    <h4 style="font-size:.85rem; font-weight:800; color:var(--navy); margin-bottom:1rem; display:flex; align-items:center; gap:.5rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                        Booking List
                    </h4>
                    <div id="rbDashCartWrap" style="flex:1; overflow-y:auto; margin-bottom:1rem; display:none;">
                        <div id="rbDashCartList" style="display:flex; flex-direction:column; gap:.6rem;"></div>
                    </div>
                    <div id="rbDashCartEmpty" style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; color:#94a3b8; text-align:center; gap:.5rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="opacity:.4;"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg>
                        <p style="font-size:.75rem; font-weight:600;">Your list is empty.<br>Add slots from the left.</p>
                    </div>
                    <button type="submit" id="rbDashSubmitBtn" class="btn btn-primary btn-block" style="display:none; padding:1rem; font-size:.95rem;">Submit All Bookings</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- EDIT BOOKING MODAL -->
<div class="modal" id="rbEditModal">
    <div class="modal-box" style="max-width:500px;">
        <div class="modal-header"><h3 id="rbEditTitle">Edit Booking</h3><button class="modal-close" onclick="closeModal()">×</button></div>
        <form action="" method="POST" id="rbEditForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="rbEditId">
            <div class="modal-body">
                <div id="rbEditApprovalNote" style="display:none; padding:.75rem; border-radius:8px; background:#eff6ff; border:1px solid #dbeafe; color:#1e40af; font-size:.8rem; margin-bottom:1rem;">
                    <strong>Note:</strong> Since this booking is already approved, changes will require new approval.
                </div>
                <div class="form-group">
                    <label>Meeting Room</label>
                    <select name="room_id" id="rbEditRoomId" class="form-control">
                        @foreach($rooms as $rm)
                        <option value="{{ $rm->id }}">{{ $rm->name }} (Cap: {{ $rm->capacity }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="booking_date" id="rbEditDate" class="form-control" required>
                </div>
                <div class="form-grid">
                    <div class="form-group"><label>Start Time</label><input type="time" name="start_time" id="rbEditStart" class="form-control" required></div>
                    <div class="form-group"><label>End Time</label><input type="time" name="end_time" id="rbEditEnd" class="form-control" required></div>
                </div>
                <div class="form-group">
                    <label>Purpose</label>
                    <textarea name="purpose" id="rbEditPurpose" class="form-control" rows="2" required></textarea>
                </div>
                <div class="form-group">
                    <label>Attendees</label>
                    <input type="number" name="attendees" id="rbEditAttendees" class="form-control" required min="1">
                </div>
                <div class="form-group" id="rbEditReasonWrap" style="display:none;">
                    <label>Reason for Change</label>
                    <textarea name="edit_reason" id="rbEditReason" class="form-control" rows="2" placeholder="Briefly explain why..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal()">Cancel</button>
                <button type="button" class="btn btn-danger" id="rbEditCancelBtn" onclick="openRequestCancelModal(document.getElementById('rbEditId').value, document.getElementById('rbEditPurpose').value)">Cancel Booking</button>
                <button type="submit" class="btn btn-primary" id="rbEditSubmitBtn">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- PIC BOOKING VIEW MODAL -->
<div class="modal" id="rbPicViewModal">
    <div class="modal-box" style="max-width:500px;">
        <div class="modal-header">
            <h3>Booking Details</h3>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <div class="modal-body">
            <div style="display:grid; grid-template-columns:1fr; gap:1.25rem;">
                <div style="display:flex; gap:1rem; align-items:center; background:#f8fafc; padding:1rem; border-radius:12px; border:1px solid #e2e8f0;">
                    <div style="width:48px; height:48px; border-radius:50%; background:#e0f2fe; color:#0369a1; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:1.2rem; flex-shrink:0;" id="pvInitials">
                        ?
                    </div>
                    <div>
                        <div id="pvBookedBy" style="font-weight:800; color:var(--navy); font-size:1rem;">—</div>
                        <div id="pvStaffInfo" style="font-size:.75rem; color:#64748b; font-weight:600;">—</div>
                    </div>
                </div>

                <div class="form-grid" style="grid-template-columns:1fr 1fr; gap:1rem;">
                    <div>
                        <label style="font-size:.65rem; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:.05em; display:block; margin-bottom:.3rem;">Room</label>
                        <div id="pvRoom" style="font-weight:700; color:#334155; font-size:.9rem;">—</div>
                    </div>
                    <div>
                        <label style="font-size:.65rem; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:.05em; display:block; margin-bottom:.3rem;">Status</label>
                        <span id="pvStatusBadge" class="badge">Pending</span>
                    </div>
                    <div>
                        <label style="font-size:.65rem; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:.05em; display:block; margin-bottom:.3rem;">Date</label>
                        <div id="pvDate" style="font-weight:700; color:#334155; font-size:.9rem;">—</div>
                    </div>
                    <div>
                        <label style="font-size:.65rem; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:.05em; display:block; margin-bottom:.3rem;">Time</label>
                        <div id="pvTime" style="font-weight:700; color:#334155; font-size:.9rem;">—</div>
                    </div>
                </div>

                <div>
                    <label style="font-size:.65rem; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:.05em; display:block; margin-bottom:.3rem;">Purpose</label>
                    <div id="pvPurpose" style="font-weight:600; color:#334155; line-height:1.5; background:#f8fafc; padding:.75rem; border-radius:8px; border:1px solid #f1f5f9;">—</div>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <div style="font-size:.8rem; font-weight:700; color:#64748b;">Attendees: <span id="pvAttendees" style="color:var(--navy);">—</span></div>
                </div>
            </div>
        </div>
        <div class="modal-footer" id="pvActions">
            <button class="btn btn-ghost" onclick="closeModal()">Close</button>
        </div>
    </div>
</div>

<!-- REJECT MODAL -->
<div class="modal" id="rbRejectModal">
    <div class="modal-box modal-sm">
        <div class="modal-header"><h3>Reject Booking</h3><button class="modal-close" onclick="closeModal()">×</button></div>
        <form action="" method="POST" id="rbRejectForm">
            @csrf
            <input type="hidden" name="id" id="rbRejectId">
            <div class="modal-body">
                <p style="font-size:.85rem; color:#64748b; margin-bottom:1rem;">Rejecting booking for <strong id="rbRejectUser"></strong>.</p>
                <div class="form-group">
                    <label>Reason for Rejection</label>
                    <textarea name="rejection_reason" class="form-control" rows="3" placeholder="e.g. Overlapping with high-priority meeting" required></textarea>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-ghost" onclick="closeModal()">Cancel</button><button type="submit" class="btn btn-danger">Confirm Rejection</button></div>
        </form>
    </div>
</div>

<!-- REQUEST CANCEL MODAL -->
<div class="modal" id="rbCancelModal">
    <div class="modal-box modal-sm">
        <div class="modal-header"><h3>Cancel Booking</h3><button class="modal-close" onclick="closeModal()">×</button></div>
        <form action="" method="POST" id="rbCancelForm">
            @csrf
            <input type="hidden" name="id" id="rbCancelId">
            <div class="modal-body">
                <p style="font-size:.85rem; color:#64748b; margin-bottom:1rem;">Are you sure you want to cancel: <strong id="rbCancelPurpose"></strong>?</p>
                <div class="form-group">
                    <label>Reason for Cancellation (Optional)</label>
                    <textarea name="cancel_reason" class="form-control" rows="3" placeholder="Brief explanation..."></textarea>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-ghost" onclick="closeModal()">Back</button><button type="submit" class="btn btn-danger">Confirm Cancellation</button></div>
        </form>
    </div>
</div>

<!-- PIC BOOKING VIEW MODAL -->

@if(Auth::check() && Auth::user()->isAdminIT())
<!-- ROOM MANAGEMENT MODAL -->
<div class="modal" id="roomMgmtModal">
    <div class="modal-box" style="max-width:500px;">
        <div class="modal-header"><h3 id="rmmTitle">Add Room</h3><button class="modal-close" onclick="closeModal()">×</button></div>
        <form action="{{ route('rooms.store') }}" method="POST" id="roomMgmtForm">
            @csrf
            <input type="hidden" name="_method" id="rmmMethod" value="POST">
            <div class="modal-body">
                <div class="form-group">
                    <label>Room Name <span class="req">*</span></label>
                    <input type="text" name="room_name" id="rmmName" class="form-control" required>      
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="room_description" id="rmmDesc" class="form-control" rows="2"></textarea>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Capacity <span class="req">*</span></label>
                        <input type="number" name="room_capacity" id="rmmCap" class="form-control" required min="1">
                    </div>
                    <div class="form-group">
                        <label>Color Theme</label>
                        <select name="room_color" id="rmmColor" class="form-control">
                            @foreach($colorMap as $code => $info)
                            <option value="{{ $code }}">{{ ucfirst(str_replace('room-', '', $code)) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div style="margin-top:1rem; padding-top:1rem; border-top:1px solid #f1f5f9;">
                    <label style="font-weight:700; font-size:.85rem; color:#1e293b; display:block; margin-bottom:.75rem;">Assign PICs (Max 2)</label>
                    <div class="form-group">
                        <label style="font-size:.72rem; font-weight:700;">Primary PIC</label>
                        <select name="room_pics[]" id="rmmPic1" class="form-control">
                            <option value="">-- No PIC --</option>
                            @foreach($allUsers as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->staff_no }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="font-size:.72rem; font-weight:700;">Secondary PIC (Optional)</label>
                        <select name="room_pics[]" id="rmmPic2" class="form-control">
                            <option value="">-- No PIC --</option>
                            @foreach($allUsers as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->staff_no }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary" id="rmmSubmit">Add Room</button>
            </div>
        </form>
    </div>
</div>

<!-- DELETE ROOM MODAL -->
<div class="modal" id="deleteRoomModal">
    <div class="modal-box modal-sm">
        <div class="modal-header"><h3>Delete Room</h3><button class="modal-close" onclick="closeModal()">×</button></div>
        <div class="modal-body">
            <p>Are you sure you want to delete <strong id="drName"></strong>?</p>
            <form action="" method="POST" id="deleteRoomForm">
                @csrf
                @method('DELETE')
            </form>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-ghost" onclick="closeModal()">Cancel</button><button type="submit" form="deleteRoomForm" class="btn btn-danger">Delete Room</button></div>
    </div>
</div>
@endif

<form id="confirmActionForm" method="POST" style="display:none;">@csrf</form>

@if($viewMode === 'month' && (Auth::user()->isAdmin() || Auth::user()->isCeo()))
@php
    $topBookers = $allRangeBookings
        ->groupBy('booked_by_name')
        ->map(fn($g) => $g->count())
        ->sortDesc()
        ->take(10);

    $roomCounts = $allRangeBookings
        ->groupBy(fn($b) => $b->room->name)
        ->map(fn($g) => $g->count())
        ->sortDesc();
@endphp
<div class="modal" id="monthReportModal">
    <div class="modal-box modal-lg" style="max-width:900px; max-height:90vh; display:flex; flex-direction:column;">
        <div class="modal-header">
            <h3 style="font-weight:800;">Monthly Report &mdash; {{ date('F Y', strtotime($rangeStart)) }}</h3>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <div style="overflow-y:auto; padding:1.25rem; flex:1; display:flex; flex-direction:column; gap:1.5rem;">

            {{-- All Bookings List --}}
            <div>
                <h4 style="font-size:.85rem; font-weight:800; color:var(--navy); text-transform:uppercase; letter-spacing:.05em; margin:0 0 .75rem;">All Bookings ({{ $allRangeBookings->count() }})</h4>
                <div style="max-height:280px; overflow-y:auto; border:1px solid var(--border); border-radius:8px;">
                    <table class="table" style="margin-bottom:0; font-size:.82rem;">
                        <thead style="background:#f8fafc; position:sticky; top:0; z-index:1;">
                            <tr>
                                <th style="padding:.6rem .9rem; font-size:.72rem; color:#64748b; text-transform:uppercase; font-weight:700;">Booked By</th>
                                <th style="padding:.6rem .9rem; font-size:.72rem; color:#64748b; text-transform:uppercase; font-weight:700;">Room</th>
                                <th style="padding:.6rem .9rem; font-size:.72rem; color:#64748b; text-transform:uppercase; font-weight:700;">Date</th>
                                <th style="padding:.6rem .9rem; font-size:.72rem; color:#64748b; text-transform:uppercase; font-weight:700;">Time</th>
                                <th style="padding:.6rem .9rem; font-size:.72rem; color:#64748b; text-transform:uppercase; font-weight:700;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allRangeBookings->sortBy('booking_date')->sortBy('start_time') as $b)
                            <tr style="border-bottom:1px solid #f1f5f9;">
                                <td style="padding:.55rem .9rem; font-weight:600; color:var(--navy);">{{ $b->booked_by_name }}</td>
                                <td style="padding:.55rem .9rem; color:var(--text);">{{ $b->room->name }}</td>
                                <td style="padding:.55rem .9rem; color:#64748b; white-space:nowrap;">{{ date('d M, D', strtotime($b->booking_date)) }}</td>
                                <td style="padding:.55rem .9rem; font-variant-numeric:tabular-nums; white-space:nowrap;">{{ substr($b->start_time,0,5) }}–{{ substr($b->end_time,0,5) }}</td>
                                <td style="padding:.55rem .9rem;">
                                    @php $sc = match($b->status){'Pending'=>'warning','Approved'=>'success','Rejected'=>'danger','EditRequested'=>'info','CancelRequested'=>'danger',default=>'ghost'}; @endphp
                                    <span class="badge badge-{{ $sc }}" style="font-size:.6rem; font-weight:800;">{{ strtoupper($b->status) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" style="padding:1.5rem; text-align:center; color:#94a3b8;">No bookings this month.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Top Bookers + Room Chart --}}
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem;">

                {{-- Top Bookers --}}
                <div>
                    <h4 style="font-size:.85rem; font-weight:800; color:var(--navy); text-transform:uppercase; letter-spacing:.05em; margin:0 0 .75rem;">Top Bookers</h4>
                    <div style="border:1px solid var(--border); border-radius:8px; overflow:hidden;">
                        <table class="table" style="margin-bottom:0; font-size:.82rem;">
                            <thead style="background:#f8fafc;">
                                <tr>
                                    <th style="padding:.6rem .9rem; font-size:.72rem; color:#64748b; text-transform:uppercase; font-weight:700; width:36px;">#</th>
                                    <th style="padding:.6rem .9rem; font-size:.72rem; color:#64748b; text-transform:uppercase; font-weight:700;">Name</th>
                                    <th style="padding:.6rem .9rem; font-size:.72rem; color:#64748b; text-transform:uppercase; font-weight:700; text-align:right;">Bookings</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topBookers as $name => $count)
                                <tr style="border-bottom:1px solid #f1f5f9;">
                                    <td style="padding:.55rem .9rem; color:#94a3b8; font-weight:700; font-size:.75rem;">
                                        @if($loop->index === 0) <span style="color:#f59e0b;">&#9733;</span>
                                        @elseif($loop->index === 1) <span style="color:#94a3b8;">&#9734;</span>
                                        @else {{ $loop->iteration }}
                                        @endif
                                    </td>
                                    <td style="padding:.55rem .9rem; font-weight:700; color:var(--navy);">{{ $name }}</td>
                                    <td style="padding:.55rem .9rem; text-align:right;">
                                        <span style="display:inline-flex; align-items:center; gap:.35rem;">
                                            <span style="display:inline-block; height:6px; border-radius:3px; background:#3b82f6; width:{{ min(60, $count / max($topBookers->values()->first(), 1) * 60) }}px;"></span>
                                            <strong>{{ $count }}</strong>
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="3" style="padding:1.5rem; text-align:center; color:#94a3b8;">No bookings.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Room Utilization Pie Chart --}}
                <div>
                    <h4 style="font-size:.85rem; font-weight:800; color:var(--navy); text-transform:uppercase; letter-spacing:.05em; margin:0 0 .75rem;">Room Utilization</h4>
                    <div style="border:1px solid var(--border); border-radius:8px; padding:1rem; display:flex; flex-direction:column; align-items:center; gap:.75rem;">
                        <canvas id="roomPieChart" width="200" height="200"></canvas>
                        <div id="roomPieLegend" style="display:flex; flex-wrap:wrap; gap:.4rem .9rem; justify-content:center; font-size:.72rem;"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endif

{{-- Centralized date picker --}}
<div id="rbDatePickerOverlay" class="rbdp-overlay" onclick="if(event.target===this)rbCloseDatePicker()">
    <div class="rbdp-modal" role="dialog" aria-label="Pick a date">
        <div class="rbdp-header">
            <button type="button" class="rbdp-nav" onclick="rbDpChangeMonth(-1)" aria-label="Previous month">&lsaquo;</button>
            <span class="rbdp-title" id="rbdpTitle"></span>
            <button type="button" class="rbdp-nav" onclick="rbDpChangeMonth(1)" aria-label="Next month">&rsaquo;</button>
        </div>
        <div class="rbdp-weekdays"><span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span></div>
        <div class="rbdp-grid" id="rbdpGrid"></div>
        <div class="rbdp-footer">
            <button type="button" class="rbdp-today-link" onclick="rbDpGoToday()">Today</button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const dashBookingsRaw = {!! $allRangeBookings->toJson() !!};
    const dashBookings = Array.isArray(dashBookingsRaw) ? dashBookingsRaw : (dashBookingsRaw.data ?? []);
    const dashRooms = {!! $rooms->toJson() !!};
    const dashViewDate = "{{ $viewDate }}";
    const dashViewMode = "{{ $viewMode }}";
    const canApprove = {{ json_encode($canApprove) }};
    let dashRoomId = null;
    let clockState = { start:{h:null,m:null}, end:{h:null,m:null} };
    let dashCart = [];

    // ── Centralized (centered) custom date picker ──
    const RBDP_BASEURL  = "{{ route('rooms.index') }}";
    const RBDP_VIEW     = "{{ $viewMode }}";
    const RBDP_SELECTED = "{{ $viewDate }}";
    let rbDpViewMonth = null; // Date pointing at the 1st of the displayed month

    function rbDpFmt(d) {
        return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    }

    function rbOpenDatePicker(e) {
        if (e) e.preventDefault();
        const sel = new Date(RBDP_SELECTED + 'T00:00:00');
        rbDpViewMonth = new Date(sel.getFullYear(), sel.getMonth(), 1);
        rbDpRender();
        document.getElementById('rbDatePickerOverlay').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function rbCloseDatePicker() {
        document.getElementById('rbDatePickerOverlay').classList.remove('active');
        document.body.style.overflow = '';
    }

    function rbDpChangeMonth(delta) {
        rbDpViewMonth = new Date(rbDpViewMonth.getFullYear(), rbDpViewMonth.getMonth() + delta, 1);
        rbDpRender();
    }

    function rbDpGoToday() { rbDpSelect(rbDpFmt(new Date())); }

    function rbDpSelect(dateStr) {
        window.location.href = RBDP_BASEURL + '?view=' + RBDP_VIEW + '&date=' + dateStr;
    }

    function rbDpRender() {
        const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        const y = rbDpViewMonth.getFullYear(), m = rbDpViewMonth.getMonth();
        document.getElementById('rbdpTitle').textContent = months[m] + ' ' + y;
        const firstDay = new Date(y, m, 1).getDay();
        const daysInMonth = new Date(y, m + 1, 0).getDate();
        const todayStr = rbDpFmt(new Date());
        let html = '';
        for (let i = 0; i < firstDay; i++) html += '<div class="rbdp-cell empty"></div>';
        for (let d = 1; d <= daysInMonth; d++) {
            const ds = y + '-' + String(m + 1).padStart(2, '0') + '-' + String(d).padStart(2, '0');
            let cls = 'rbdp-cell';
            if (ds === RBDP_SELECTED) cls += ' selected';
            if (ds === todayStr) cls += ' today';
            html += '<div class="' + cls + '" onclick="rbDpSelect(\'' + ds + '\')">' + d + '</div>';
        }
        document.getElementById('rbdpGrid').innerHTML = html;
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') rbCloseDatePicker();
    });

    function gToMin(t) { if(!t) return 0; const [h,m]=t.substring(0,5).split(':').map(Number); return h*60+m; }
    function gGetNowMin() { const n=new Date(); return n.getHours()*60+n.getMinutes(); }
    function gIsToday(date) { return date === new Date().toISOString().slice(0,10); }
    function gIsPastDate(date) { return date < new Date().toISOString().slice(0,10); }

    function openModal(id) {
        const m = document.getElementById(id);
        if (m) {
            document.querySelectorAll('.modal.active').forEach(x => x.classList.remove('active'));
            m.classList.add('active');
            document.getElementById('modalOverlay').classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }
    function closeModal() {
        document.querySelectorAll('.modal.active').forEach(m => m.classList.remove('active'));
        document.getElementById('modalOverlay').classList.remove('active');
        document.body.style.overflow = '';
    }

    function rbTogglePanel(btn, panelId) {
        const body = document.getElementById(panelId);
        const expanded = btn.getAttribute('aria-expanded') === 'true';
        btn.setAttribute('aria-expanded', expanded ? 'false' : 'true');
        body.classList.toggle('open', !expanded);
    }

    function confirmAction(url, msg) {
        if (confirm(msg)) {
            const form = document.getElementById('confirmActionForm');
            form.action = url;
            form.submit();
        }
    }

    function openRejectModal(id, name) {
        document.getElementById('rbRejectId').value = id;
        document.getElementById('rbRejectUser').textContent = name;
        document.getElementById('rbRejectForm').action = "{{ url('rooms/bookings') }}/" + id + "/reject";
        openModal('rbRejectModal');
    }

    function openRequestCancelModal(id, purp) {
        document.getElementById('rbCancelId').value = id;
        document.getElementById('rbCancelPurpose').textContent = purp;
        document.getElementById('rbCancelForm').action = "{{ url('rooms/bookings') }}/" + id + "/cancel-request";
        openModal('rbCancelModal');
    }

    function openPicViewModal(id, roomId, date, startTime, endTime, purpose, attendees, status) {
        const booking = dashBookings.find(b => b.id == id);
        const room = dashRooms.find(r => r.id == roomId);
        
        const staffNo = booking?.user?.staff_no || '';
        const dept = booking?.user?.department?.name || '';
        const name = booking?.booked_by_name || '—';

        document.getElementById('pvBookedBy').textContent = name;
        document.getElementById('pvStaffInfo').textContent = (staffNo ? staffNo + ' • ' : '') + dept;
        document.getElementById('pvRoom').textContent = room?.name || '—';
        document.getElementById('pvDate').textContent = date;
        document.getElementById('pvTime').textContent = startTime + ' – ' + endTime;
        document.getElementById('pvPurpose').textContent = purpose;
        document.getElementById('pvAttendees').textContent = attendees;
        
        // Initials
        const initials = name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
        document.getElementById('pvInitials').textContent = initials;

        const badgeEl = document.getElementById('pvStatusBadge');
        badgeEl.textContent = status;
        badgeEl.className = 'badge badge-' + (status === 'Pending' ? 'warning' : status === 'Rejected' ? 'danger' : status === 'EditRequested' ? 'info' : status === 'CancelRequested' ? 'danger' : 'success');

        const actionsEl = document.getElementById('pvActions');
        actionsEl.innerHTML = '';
        if (status === 'Pending') {
            actionsEl.innerHTML =
                `<button class="btn btn-ghost btn-sm" onclick="closeModal()">Close</button>` +
                `<button class="btn btn-danger btn-sm" onclick="closeModal(); openRejectModal('${id}', '${name.replace(/'/g,"\\'")}')">Reject</button>` +
                `<button class="btn btn-success btn-sm" onclick="closeModal(); confirmAction('{{ url('rooms/bookings') }}/${id}/approve', 'Approve this booking?')">Approve</button>`;
        } else if (status === 'EditRequested') {
            actionsEl.innerHTML =
                `<button class="btn btn-ghost btn-sm" onclick="closeModal()">Close</button>` +
                `<button class="btn btn-danger btn-sm" onclick="closeModal(); confirmAction('{{ url('rooms/bookings') }}/${id}/reject-edit', 'Decline these changes?')">Decline Edit</button>` +
                `<button class="btn btn-success btn-sm" onclick="closeModal(); confirmAction('{{ url('rooms/bookings') }}/${id}/approve-edit', 'Approve these changes?')">Approve Edit</button>`;
        } else if (status === 'CancelRequested') {
            actionsEl.innerHTML =
                `<button class="btn btn-ghost btn-sm" onclick="closeModal()">Close</button>` +
                `<button class="btn btn-ghost btn-sm" onclick="closeModal(); confirmAction('{{ url('rooms/bookings') }}/${id}/reject-cancel', 'Decline cancellation?')">Decline Cancel</button>` +
                `<button class="btn btn-danger btn-sm" onclick="closeModal(); confirmAction('{{ url('rooms/bookings') }}/${id}/approve-cancel', 'Approve cancellation?')">Approve Cancel</button>`;
        } else {
            actionsEl.innerHTML = `<button class="btn btn-ghost btn-sm" onclick="closeModal()">Close</button>`;
        }
        openModal('rbPicViewModal');
    }

    function rtUpdateNowLine() {
        if (!gIsToday(dashViewDate)) return;
        const now = new Date();
        const min = now.getHours() * 60 + now.getMinutes();
        const GRID_START = 7 * 60;
        const GRID_SPAN  = 13 * 60;
        
        if (min >= GRID_START && min <= GRID_START + GRID_SPAN) {
            const pct = ((min - GRID_START) / GRID_SPAN * 100).toFixed(2);
            document.querySelectorAll('.rb-now-line').forEach(el => {
                el.style.left = pct + '%';
                el.style.display = 'block';
            });
        } else {
            document.querySelectorAll('.rb-now-line').forEach(el => el.style.display = 'none');
        }
    }
    setInterval(rtUpdateNowLine, 30000);
    rtUpdateNowLine();

    function openEditModal(id, roomId, date, startTime, endTime, purpose, attendees, status) {
        const needsApproval = (status === 'Approved' || status === 'Pending');
        const userIsOwner = (dashBookings.find(b => b.id == id)?.booked_by_id == {!! json_encode(Auth::id()) !!});

        if (!userIsOwner) {
            if (canApprove) {
                openPicViewModal(id, roomId, date, startTime, endTime, purpose, attendees, status);
            } else {
                alert('You can only edit or cancel your own bookings.');
            }
            return;
        }

        document.getElementById('rbEditId').value = id;
        document.getElementById('rbEditRoomId').value = roomId;
        document.getElementById('rbEditDate').value = date;
        document.getElementById('rbEditStart').value = startTime;
        document.getElementById('rbEditEnd').value = endTime;
        document.getElementById('rbEditPurpose').value = purpose;
        document.getElementById('rbEditAttendees').value = attendees;
        document.getElementById('rbEditReason').value = '';
        document.getElementById('rbEditTitle').textContent = needsApproval ? 'Request Booking Change' : 'Edit Booking';
        document.getElementById('rbEditSubmitBtn').textContent = needsApproval ? 'Submit Edit Request' : 'Save Changes';
        document.getElementById('rbEditApprovalNote').style.display = needsApproval ? 'block' : 'none';
        document.getElementById('rbEditReasonWrap').style.display = needsApproval ? 'block' : 'none';
        
        // Update note text
        document.getElementById('rbEditApprovalNote').innerHTML = '<strong>Note:</strong> Changes to this booking will require PIC approval.';

        document.getElementById('rbEditForm').action = "{{ url('rooms/bookings') }}/" + id;
        
        openModal('rbEditModal');
    }

    function openRoomMgmtModal(data = null, pics = []) {
        const isEdit = !!data;
        document.getElementById('rmmTitle').textContent = isEdit ? 'Edit Room' : 'Add Room';
        document.getElementById('rmmMethod').value = isEdit ? 'PUT' : 'POST';
        document.getElementById('roomMgmtForm').action = isEdit ? "{{ url('rooms') }}/" + data.id : "{{ route('rooms.store') }}";
        document.getElementById('rmmSubmit').textContent = isEdit ? 'Save Changes' : 'Add Room';
        document.getElementById('rmmName').value = isEdit ? data.name : '';
        document.getElementById('rmmDesc').value = isEdit ? (data.description || '') : '';
        document.getElementById('rmmCap').value = isEdit ? data.capacity : '10';
        document.getElementById('rmmColor').value = isEdit ? data.color_class : 'room-blue';
        document.getElementById('rmmPic1').value = pics[0] || '';
        document.getElementById('rmmPic2').value = pics[1] || '';
        openModal('roomMgmtModal');
    }

    function confirmDeleteRoom(id, name) {
        document.getElementById('drName').textContent = name;
        document.getElementById('deleteRoomForm').action = "{{ url('rooms') }}/" + id;
        openModal('deleteRoomModal');
    }

    /* Booking Wizard Logic */
    function dashOnRoomChange(val) {
        dashRoomId = parseInt(val);
        document.querySelectorAll('.rb-room-option').forEach(el => el.style.borderColor = 'var(--border)');
        document.getElementById('rbOptLabel_' + val).style.borderColor = '#3b82f6';
        dashCheckCapacity();
        dashCheckConflict();
        dashRefreshOccupied();
        buildTimePicker('start'); buildTimePicker('end');
        dashUpdateFullDay();
        dashUpdateSummary();
    }

    function dashCheckPastDate() {
        const date = document.getElementById('rbDashDate').value;
        document.getElementById('rbDashPastWarn').style.display = gIsPastDate(date) ? 'block' : 'none';
    }

    function dashDayIsClear() {
        const date = document.getElementById('rbDashDate').value;
        if (!dashRoomId || !date) return false;
        const dbBusy = dashBookings.some(b => b.room_id == dashRoomId && b.booking_date === date && b.status !== 'Rejected');
        const cartBusy = dashCart.some(s => s.room_id == dashRoomId && s.booking_date === date);
        return !dbBusy && !cartBusy;
    }

    function dashCanFullDay() {
        const date = document.getElementById('rbDashDate').value;
        return !!dashRoomId && !!date && !gIsPastDate(date) && !gIsToday(date) && dashDayIsClear();
    }

    function dashUpdateFullDay() {
        const cb = document.getElementById('rbDashFullDay');
        const hint = document.getElementById('rbDashFullDayHint');
        const eligible = dashCanFullDay();
        cb.disabled = !eligible;
        document.getElementById('rbDashFullDayLabel').style.opacity = eligible ? '1' : '.5';
        // If it was checked but is no longer eligible, revert.
        if (cb.checked && !eligible) {
            cb.checked = false;
            dashToggleFullDay();
        }
        if (!cb.checked) {
            hint.textContent = eligible
                ? 'Reserves the whole day (07:00 – 20:00) for this room.'
                : 'Available only for future dates with no existing bookings.';
        }
    }

    function dashToggleFullDay() {
        const cb = document.getElementById('rbDashFullDay');
        const clockWrap = document.getElementById('rbDashClockWrap');
        const hidden = document.getElementById('rbDashFullDayInput');
        const endDateWrap = document.getElementById('rbDashEndDateWrap');
        const endDateInput = document.getElementById('rbDashEndDate');
        if (cb.checked) {
            hidden.value = '1';
            clockState.start = { h: 7, m: 0 };
            clockState.end = { h: 20, m: 0 };
            document.getElementById('rbDashStart').value = '07:00';
            document.getElementById('rbDashEnd').value = '20:00';
            ['start','end'].forEach(w => {
                const v = document.getElementById(w + 'Val');
                v.textContent = w === 'start' ? '07:00' : '20:00';
                v.classList.remove('unset');
            });
            clockWrap.style.opacity = '.4';
            clockWrap.style.pointerEvents = 'none';
            dashRefreshEndLock();
            document.getElementById('rbDashConflictWarn').style.display = 'none';
            document.getElementById('rbDashFullDayHint').textContent = 'Whole day reserved (07:00 – 20:00).';
            endDateWrap.style.display = 'block';
            dashSyncEndDateMin();
            endDateInput.value = '';
            document.getElementById('rbDashMultiDayHint').textContent = '';
        } else {
            hidden.value = '0';
            clockState.start = { h: null, m: null };
            clockState.end = { h: null, m: null };
            document.getElementById('rbDashStart').value = '';
            document.getElementById('rbDashEnd').value = '';
            ['start','end'].forEach(w => {
                const v = document.getElementById(w + 'Val');
                v.textContent = '--:--';
                v.classList.add('unset');
            });
            clockWrap.style.opacity = '';
            clockWrap.style.pointerEvents = '';
            dashRefreshEndLock();
            buildTimePicker('start'); buildTimePicker('end');
            endDateWrap.style.display = 'none';
            endDateInput.value = '';
            document.getElementById('rbDashMultiDayHint').textContent = '';
            dashUpdateFullDay();
        }
        dashUpdateSummary();
    }

    function dashSyncEndDateMin() {
        const startDate = document.getElementById('rbDashDate').value;
        const endDateInput = document.getElementById('rbDashEndDate');
        if (startDate) {
            endDateInput.min = startDate;
            if (endDateInput.value && endDateInput.value < startDate) {
                endDateInput.value = '';
                document.getElementById('rbDashMultiDayHint').textContent = '';
            }
        }
        dashUpdateMultiDayHint();
    }

    function dashUpdateMultiDayHint() {
        const startDate = document.getElementById('rbDashDate').value;
        const endDate = document.getElementById('rbDashEndDate').value;
        const hint = document.getElementById('rbDashMultiDayHint');
        if (!startDate || !endDate || endDate <= startDate) {
            hint.textContent = endDate && endDate === startDate ? 'Same as start date — single day booking.' : '';
            hint.style.color = '#94a3b8';
            return;
        }
        const start = new Date(startDate + 'T00:00:00');
        const end = new Date(endDate + 'T00:00:00');
        const days = Math.round((end - start) / 86400000) + 1;
        hint.textContent = `${days} day(s) selected (${startDate} → ${endDate})`;
        hint.style.color = '#2563eb';
    }

    function dashIsStartDisabled(h, m) {
        const tMin = h*60+m;
        const date = document.getElementById('rbDashDate').value;
        if (gIsToday(date) && tMin <= gGetNowMin()) return true;
        if (!dashRoomId || !date) return false;
        const t = String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0');
        const dbConflict = dashBookings.some(b => b.room_id == dashRoomId && b.booking_date === date && t >= b.start_time.substring(0,5) && t < b.end_time.substring(0,5));
        if (dbConflict) return true;
        return dashCart.some(s => s.room_id == dashRoomId && s.booking_date === date && t >= s.start_time && t < s.end_time);
    }

    function dashIsEndDisabled(h, m) {
        const tMin = h*60+m;
        const startMin = (clockState.start.h !== null) ? clockState.start.h*60 + clockState.start.m : null;
        if (startMin !== null && tMin <= startMin) return true;
        return dashIsStartDisabled(h, m); // Simplification: if you can't start there, you probably can't be booked through there
    }

    function buildTimePicker(which) {
        const hGrid = document.getElementById(which + 'HourGrid');
        hGrid.innerHTML = '';
        for (let h = 7; h <= 20; h++) {
            const allOff = [0,15,30,45].every(m =>
                which === 'start' ? dashIsStartDisabled(h,m) : dashIsEndDisabled(h,m));
            const isSel = clockState[which].h === h;
            const d = document.createElement('div');
            d.className = 'clock-btn'
                + (allOff ? ' disabled' : '')
                + (isSel && clockState[which].m === null ? ' h-selected' : '')
                + (isSel && clockState[which].m !== null ? ' selected' : '');
            d.textContent = String(h).padStart(2,'0');
            if (!allOff) d.onclick = () => pickHour(which, h);
            hGrid.appendChild(d);
        }
        const mRow = document.getElementById(which + 'MinRow');
        mRow.innerHTML = '';
        [0, 15, 30, 45].forEach(m => {
            const h = clockState[which].h;
            const isOff = h !== null
                ? (which === 'start' ? dashIsStartDisabled(h, m) : dashIsEndDisabled(h, m))
                : false;
            const isSel = clockState[which].m === m;
            const d = document.createElement('div');
            d.className = 'clock-btn'
                + (isOff ? ' disabled' : '')
                + (isSel ? ' selected' : '');
            d.textContent = String(m).padStart(2,'0');
            if (!isOff) d.onclick = () => pickMinute(which, m);
            mRow.appendChild(d);
        });
    }

    // Start time is "complete" only once both its hour and minute are chosen.
    function dashStartComplete() {
        return clockState.start.h !== null && clockState.start.m !== null;
    }

    // Lock the End Time picker until the start time is fully set.
    function dashRefreshEndLock() {
        document.getElementById('endDisplay')
            .classList.toggle('locked', !dashStartComplete());
    }

    function pickHour(which, h) {
        clockState[which].h = h;
        if (clockState[which].m !== null) {
            const off = which === 'start'
                ? dashIsStartDisabled(h, clockState[which].m)
                : dashIsEndDisabled(h, clockState[which].m);
            if (off) clockState[which].m = null;
        }
        // Picking the start hour may clear an invalid start minute; keep the
        // end picker lock in sync with whether the start time is still complete.
        if (which === 'start') dashRefreshEndLock();
        buildTimePicker(which);
    }

    function pickMinute(which, m) {
        if (clockState[which].h === null) return;
        clockState[which].m = m;
        const t = String(clockState[which].h).padStart(2,'0') + ':' + String(m).padStart(2,'0');
        document.getElementById('rbDash' + (which === 'start' ? 'Start' : 'End')).value = t;
        const valEl = document.getElementById(which + 'Val');
        valEl.textContent = t;
        valEl.classList.remove('unset');
        document.getElementById(which + 'Drop').style.display = 'none';
        document.getElementById(which + 'Display').classList.remove('open');
        // Start must be fully chosen before the end picker unlocks; end stays
        // empty until the user sets it themselves (hour, then minute).
        dashRefreshEndLock();
        buildTimePicker(which);
        dashCheckConflict();
        dashUpdateSummary();
    }

    function toggleClock(which) {
        // End picker is locked until the start time is fully chosen.
        if (which === 'end' && !dashStartComplete()) return;
        const drop = document.getElementById(which + 'Drop');
        const disp = document.getElementById(which + 'Display');
        const isOpen = drop.style.display === 'block';
        document.querySelectorAll('.clock-dropdown').forEach(d => d.style.display = 'none');
        document.querySelectorAll('.clock-display').forEach(d => d.classList.remove('open'));
        if (!isOpen) {
            buildTimePicker(which);
            if (window.innerWidth <= 768) {
                drop.style.top = '50%';
                drop.style.left = '50%';
                drop.style.transform = 'translate(-50%, -50%)';
            } else {
                const rect = disp.getBoundingClientRect();
                drop.style.top  = (rect.bottom + 6) + 'px';
                drop.style.left = rect.left + 'px';
                drop.style.transform = '';
            }
            drop.style.display = 'block';
            disp.classList.add('open');
        }
    }

    // Close clock dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.clock-dropdown') && !e.target.closest('.clock-display')) {
            document.querySelectorAll('.clock-dropdown').forEach(d => d.style.display = 'none');
            document.querySelectorAll('.clock-display').forEach(d => d.classList.remove('open'));
        }
    });

    function dashCheckCapacity() {
        const att = parseInt(document.getElementById('rbDashAttendees').value) || 0;
        const rm = dashRooms.find(r => r.id == dashRoomId);
        const hint = document.getElementById('rbDashCapacityHint');
        if (rm && att > rm.capacity) {
            hint.innerHTML = '<span style="color:#dc2626;">Exceeds limit (max '+rm.capacity+')</span>';
            return false;
        }
        hint.innerHTML = rm ? '<span style="color:#16a34a;">Seats available</span>' : '';
        return true;
    }

    function dashCheckConflict() {
        const start = document.getElementById('rbDashStart').value;
        const end = document.getElementById('rbDashEnd').value;
        const date = document.getElementById('rbDashDate').value;
        if (!dashRoomId || !start || !end || !date) return false;
        const conflict = dashBookings.some(b => b.room_id == dashRoomId && b.booking_date === date && start < b.end_time.substring(0,5) && end > b.start_time.substring(0,5));
        document.getElementById('rbDashConflictWarn').style.display = conflict ? 'block' : 'none';
        return conflict;
    }

    function dashUpdateSummary() {
        const start = document.getElementById('rbDashStart').value;
        const end = document.getElementById('rbDashEnd').value;
        const rm = dashRooms.find(r => r.id == dashRoomId);
        const el = document.getElementById('rbDashSummary');
        if (rm && start && end) {
            el.innerHTML = `<strong>${rm.name}</strong> • ${start} – ${end}`;
            el.style.display = 'block';
        } else {
            el.style.display = 'none';
        }
    }

    function dashRefreshOccupied() {
        const date = document.getElementById('rbDashDate').value;
        if (!dashRoomId || !date) return;
        const occ = dashBookings.filter(b => b.room_id == dashRoomId && b.booking_date === date);
        const list = document.getElementById('rbDashOccList');
        list.innerHTML = '';
        if (occ.length > 0) {
            document.getElementById('rbDashOccSlots').style.display = 'block';
            occ.forEach(b => {
                const s = document.createElement('span'); s.className = 'badge badge-outline';
                s.textContent = b.start_time.substring(0,5) + '-' + b.end_time.substring(0,5);
                s.style.fontSize = '.65rem';
                list.appendChild(s);
            });
        } else {
            document.getElementById('rbDashOccSlots').style.display = 'none';
        }
    }

    function dashAddToCart() {
        const isFullDay = document.getElementById('rbDashFullDay').checked;
        const start = document.getElementById('rbDashStart').value;
        const end = document.getElementById('rbDashEnd').value;
        const date = document.getElementById('rbDashDate').value;
        const endDate = document.getElementById('rbDashEndDate')?.value || '';
        const purp = document.getElementById('rbDashPurpose').value;
        const att = document.getElementById('rbDashAttendees').value;

        if (!dashRoomId || !start || !end || !date || !purp || !att) {
            alert('Please fill all fields'); return;
        }
        if (gIsPastDate(date)) { alert('Past day cannot book.'); return; }

        const rm = dashRooms.find(r => r.id == dashRoomId);

        // Multi-day full-day booking
        if (isFullDay && endDate && endDate > date) {
            let added = 0, skipped = 0;
            const cur = new Date(date + 'T00:00:00');
            const last = new Date(endDate + 'T00:00:00');
            while (cur <= last) {
                const d = cur.toISOString().slice(0, 10);
                if (!gIsPastDate(d) && !gIsToday(d)) {
                    const dbBusy = dashBookings.some(b => b.room_id == dashRoomId && b.booking_date === d && b.status !== 'Rejected');
                    const cartBusy = dashCart.some(s => s.room_id == dashRoomId && s.booking_date === d);
                    if (!dbBusy && !cartBusy) {
                        dashCart.push({ room_id: dashRoomId, room_name: rm.name, booking_date: d, start_time: '07:00', end_time: '20:00', is_full_day: true, purpose: purp, attendees: att });
                        added++;
                    } else {
                        skipped++;
                    }
                } else {
                    skipped++;
                }
                cur.setDate(cur.getDate() + 1);
            }
            if (added === 0) {
                alert('No valid dates to add. All selected dates may have conflicts or are not in the future.'); return;
            }
            if (skipped > 0) alert(added + ' date(s) added to list. ' + skipped + ' skipped (conflict or invalid).');
            document.getElementById('rbDashFullDay').checked = false;
            dashToggleFullDay();
            renderCart();
            dashUpdateFullDay();
            return;
        }

        // Single booking
        if (isFullDay && (gIsToday(date) || !dashDayIsClear())) {
            alert('Full day requires a future date with no existing bookings.'); return;
        }
        if (!isFullDay && dashCheckConflict()) {
            alert('Time conflict detected'); return;
        }

        dashCart.push({
            room_id: dashRoomId,
            room_name: rm.name,
            booking_date: date,
            start_time: start,
            end_time: end,
            is_full_day: isFullDay,
            purpose: purp,
            attendees: att
        });
        // Reset full-day toggle so the next slot starts fresh.
        if (isFullDay) {
            document.getElementById('rbDashFullDay').checked = false;
            dashToggleFullDay();
        }
        renderCart();
        dashUpdateFullDay();
    }

    function renderCart() {
        const list = document.getElementById('rbDashCartList');
        const empty = document.getElementById('rbDashCartEmpty');
        const wrap = document.getElementById('rbDashCartWrap');
        const btn = document.getElementById('rbDashSubmitBtn');
        
        list.innerHTML = '';
        if (dashCart.length > 0) {
            empty.style.display = 'none';
            wrap.style.display = 'block';
            btn.style.display = 'block';
            dashCart.forEach((s, idx) => {
                const item = document.createElement('div');
                item.style.padding = '.75rem'; item.style.background = '#fff'; item.style.border = '1px solid var(--border)'; item.style.borderRadius = '8px';
                const timeLabel = s.is_full_day ? 'Full Day (07:00-20:00)' : `${s.start_time}-${s.end_time}`;
                item.innerHTML = `<div style="font-weight:700; font-size:.8rem;">${s.room_name}</div>
                                  <div style="font-size:.7rem; color:#64748b;">${s.booking_date} • ${timeLabel}</div>
                                  <button type="button" class="btn btn-ghost btn-xs" style="color:#dc2626; margin-top:.3rem;" onclick="dashCart.splice(${idx},1); renderCart(); dashUpdateFullDay();">Remove</button>`;
                list.appendChild(item);
            });
            document.getElementById('rbDashSlotsJson').value = JSON.stringify(dashCart);
        } else {
            empty.style.display = 'flex';
            wrap.style.display = 'none';
            btn.style.display = 'none';
        }
    }

    function validateAndSubmitBatch() {
        if (dashCart.length === 0) {
            // Try adding current if valid
            dashAddToCart();
        }
        return dashCart.length > 0;
    }

    function openRoomBookingModal(rid, name) {
        dashCart = []; renderCart();
        const fullDayCb = document.getElementById('rbDashFullDay');
        if (fullDayCb.checked) { fullDayCb.checked = false; dashToggleFullDay(); }
        if (rid) {
            const radio = document.querySelector('input[value="'+rid+'"]');
            if (radio) { radio.checked = true; dashOnRoomChange(rid); }
        }
        dashUpdateFullDay();
        openModal('rbDashBookModal');
    }

    // Initialize
    buildTimePicker('start'); buildTimePicker('end');
    dashRefreshEndLock();

    @if($viewMode === 'month' && (Auth::user()->isAdmin() || Auth::user()->isCeo()))
    // ── Monthly Report ────────────────────────────────────────────────
    const _pieRoomLabels = @json($roomCounts->keys()->values());
    const _pieRoomData   = @json($roomCounts->values()->values());
    const _pieColors = ['#3b82f6','#22c55e','#f59e0b','#ef4444','#a855f7',
                        '#ec4899','#06b6d4','#84cc16','#f97316','#64748b'];

    function openMonthReport() {
        openModal('monthReportModal');
        setTimeout(drawRoomPie, 50); // wait for modal to be visible
    }

    function drawRoomPie() {
        const canvas = document.getElementById('roomPieChart');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        const W = canvas.width, H = canvas.height;
        const cx = W / 2, cy = H / 2;
        const r = Math.min(cx, cy) - 10;
        const total = _pieRoomData.reduce((a, b) => a + b, 0);
        if (total === 0) return;

        ctx.clearRect(0, 0, W, H);
        let start = -Math.PI / 2;
        _pieRoomData.forEach((val, i) => {
            const slice = (val / total) * 2 * Math.PI;
            ctx.beginPath();
            ctx.moveTo(cx, cy);
            ctx.arc(cx, cy, r, start, start + slice);
            ctx.closePath();
            ctx.fillStyle = _pieColors[i % _pieColors.length];
            ctx.fill();
            ctx.strokeStyle = '#fff';
            ctx.lineWidth = 2;
            ctx.stroke();
            start += slice;
        });

        // Centre hole (donut)
        ctx.beginPath();
        ctx.arc(cx, cy, r * 0.48, 0, 2 * Math.PI);
        ctx.fillStyle = '#fff';
        ctx.fill();

        // Centre label
        ctx.fillStyle = '#1e293b';
        ctx.font = 'bold 18px system-ui';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(total, cx, cy - 6);
        ctx.font = '11px system-ui';
        ctx.fillStyle = '#94a3b8';
        ctx.fillText('bookings', cx, cy + 10);

        // Legend
        const legend = document.getElementById('roomPieLegend');
        if (legend) {
            legend.innerHTML = _pieRoomLabels.map((lbl, i) =>
                `<span style="display:flex;align-items:center;gap:.3rem;">
                    <span style="width:10px;height:10px;border-radius:2px;background:${_pieColors[i % _pieColors.length]};flex-shrink:0;"></span>
                    ${lbl} <span style="color:#94a3b8;">(${_pieRoomData[i]})</span>
                </span>`
            ).join('');
        }
    }
    @endif

    // ── Real-Time Polling ──────────────────────────────────────────────────
    (function() {
        const POLL_MS = 10000;
        const GRID_START = 7 * 60;
        const GRID_SPAN  = 13 * 60;
        const myUserId   = {!! json_encode(Auth::id()) !!};

        // Snapshot current state for change detection
        let trackedStatuses = {};
        let trackedIds = new Set();
        dashBookings.forEach(b => { trackedStatuses[b.id] = b.status; trackedIds.add(b.id); });

        // ── Helpers ──────────────────────────────────────────────────────
        function toMin(t) {
            const [h, m] = t.substring(0, 5).split(':').map(Number);
            return h * 60 + m;
        }

        function escHtml(s) {
            return String(s || '')
                .replace(/&/g,'&amp;').replace(/</g,'&lt;')
                .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }

        function escOnclick(s) {
            return String(s || '').replace(/\\/g,'\\\\').replace(/'/g,"\\'").replace(/"/g,'&quot;');
        }

        function rtToast(msg, type) {
            const stack = document.getElementById('notifPopupStack');
            if (!stack) return;
            const colors = { success:'#16a34a', warning:'#b45309', info:'#1d4ed8', danger:'#dc2626' };
            const c = colors[type] || colors.info;
            const el = document.createElement('div');
            el.style.cssText = `background:#fff;border:1px solid #e2e8f0;border-left:4px solid ${c};border-radius:10px;padding:.75rem 1rem;font-size:.82rem;font-weight:600;color:#1e293b;pointer-events:all;box-shadow:0 4px 16px rgba(0,0,0,.1);transition:opacity .4s;`;
            el.textContent = msg;
            stack.appendChild(el);
            setTimeout(() => el.style.opacity = '0', 4600);
            setTimeout(() => el.remove(), 5000);
        }

        // ── Day-view grid renderer ────────────────────────────────────────
        function makeBar(b) {
            const left  = Math.max(0, Math.min(100, (toMin(b.start_time) - GRID_START) / GRID_SPAN * 100));
            const width = Math.max(1, Math.min(100, (toMin(b.end_time) - toMin(b.start_time)) / GRID_SPAN * 100));
            const s = b.start_time.substring(0, 5);
            const e = b.end_time.substring(0, 5);
            return `<div class="rb-booking-bar st-${b.status}" data-booking-id="${b.id}"
                style="left:${left}%;width:${width}%;"
                onclick="event.stopPropagation();openEditModal('${b.id}','${b.room_id}','${b.booking_date}','${s}','${e}','${escOnclick(b.purpose)}','${b.attendees}','${b.status}')">
                <span class="rb-bar-title"><span class="rb-status-dot"></span>${escHtml(b.purpose)}${b.is_full_day ? ' <span style="font-size:.6rem;font-weight:800;text-transform:uppercase;letter-spacing:.03em;opacity:.85;">&bull; Full Day</span>' : ''}</span>
                <span class="rb-bar-time">${b.is_full_day ? 'Full Day' : s + ' - ' + e} &bull; ${escHtml(b.booked_by_name)}</span>
            </div>`;
        }

        function updateDayGrid(bookings) {
            const dayBookings = bookings.filter(b => b.booking_date === dashViewDate);
            const byRoom = {};
            dayBookings.forEach(b => {
                if (!byRoom[b.room_id]) byRoom[b.room_id] = [];
                byRoom[b.room_id].push(b);
            });

            document.querySelectorAll('.rb-timeline-col[data-room-id]').forEach(col => {
                const roomId = parseInt(col.dataset.roomId);
                col.querySelectorAll('.rb-booking-bar').forEach(el => el.remove());
                (byRoom[roomId] || []).forEach(b => col.insertAdjacentHTML('beforeend', makeBar(b)));
            });

            const freeEl   = document.getElementById('rtStatFree');
            const bookedEl = document.getElementById('rtStatBooked');
            if (freeEl && bookedEl) {
                const bookedRooms = new Set(dayBookings.map(b => b.room_id)).size;
                freeEl.textContent   = dashRooms.length - bookedRooms;
                bookedEl.textContent = dayBookings.length;
            }
        }

        function updateMobDayView(bookings) {
            const dayBs = bookings.filter(b => b.booking_date === dashViewDate);
            const byRoom = {};
            dayBs.forEach(b => { if (!byRoom[b.room_id]) byRoom[b.room_id] = []; byRoom[b.room_id].push(b); });
            document.querySelectorAll('#rbMobDayView .rb-mob-room-card').forEach(card => {
                const roomId   = parseInt(card.dataset.roomId);
                const chipWrap = card.querySelector('.rb-mob-card-bookings');
                const badge    = card.querySelector('.rb-mob-avail-badge');
                const newBs    = (byRoom[roomId] || []).sort((a, b) => a.start_time.localeCompare(b.start_time));
                if (badge) {
                    badge.textContent = newBs.length === 0 ? 'Available' : newBs.length + ' booked';
                    badge.className   = 'rb-mob-avail-badge ' + (newBs.length === 0 ? 'rb-mob-avail-free' : 'rb-mob-avail-busy');
                }
                if (chipWrap) {
                    chipWrap.innerHTML = newBs.map(b => {
                        const s  = b.start_time.substring(0, 5);
                        const e  = b.end_time.substring(0, 5);
                        const nm = (b.booked_by_name || '').substring(0, 14);
                        const p  = (b.purpose || '').replace(/'/g, "\\'");
                        return `<div class="rb-mob-chip st-${b.status}" onclick="event.stopPropagation();openEditModal('${b.id}','${b.room_id}','${b.booking_date}','${s}','${e}','${p}','${b.attendees}','${b.status}')"><span class="rb-mob-chip-dot"></span><span class="rb-mob-chip-time">${b.is_full_day ? 'Full Day' : s + '–' + e}</span><span class="rb-mob-chip-purpose">${b.purpose || ''}</span><span class="rb-mob-chip-booker">${nm}</span></div>`;
                    }).join('');
                }
                const header = card.querySelector('.rb-mob-card-header');
                if (header) header.style.borderBottom = newBs.length > 0 ? '1px solid #f1f5f9' : 'none';
            });
        }

        // ── Refresh banner (non-day views) ───────────────────────────────
        let bannerShown = false;
        function showRefreshBanner() {
            if (bannerShown) return;
            bannerShown = true;
            const banner = document.createElement('div');
            banner.id = 'rtRefreshBanner';
            banner.style.cssText = 'position:fixed;top:4rem;left:50%;transform:translateX(-50%);z-index:9000;background:#1d4ed8;color:#fff;padding:.45rem 1.1rem;border-radius:999px;font-size:.78rem;font-weight:700;cursor:pointer;box-shadow:0 4px 16px rgba(0,0,0,.18);display:flex;align-items:center;gap:.4rem;white-space:nowrap;';
            banner.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg> Schedule updated — click to refresh`;
            banner.onclick = () => window.location.reload();
            document.body.appendChild(banner);
        }

        // ── Change detection ─────────────────────────────────────────────
        function detectAndNotify(freshBookings) {
            const freshIds = new Set(freshBookings.map(b => b.id));
            let changed = false;

            // Status changes for my bookings → toast
            freshBookings.forEach(b => {
                if (myUserId && b.booked_by_id === myUserId) {
                    const prev = trackedStatuses[b.id];
                    if (prev !== undefined && prev !== b.status) {
                        const msgs = {
                            Approved:        'Your booking was approved!',
                            Rejected:        'Your booking was rejected.',
                            EditRequested:   'Booking edit request pending approval.',
                            CancelRequested: 'Cancellation request is pending.',
                        };
                        rtToast(msgs[b.status] || `Booking status changed to ${b.status}`,
                            b.status === 'Approved' ? 'success' : 'warning');
                    }
                }
                if (!trackedIds.has(b.id) || trackedStatuses[b.id] !== b.status) changed = true;
            });

            // Deleted bookings
            trackedIds.forEach(id => { if (!freshIds.has(id)) changed = true; });

            // Update snapshot
            trackedIds = freshIds;
            trackedStatuses = {};
            freshBookings.forEach(b => { trackedStatuses[b.id] = b.status; });

            return changed;
        }

        // ── Booking poll ─────────────────────────────────────────────────
        async function pollBookings() {
            try {
                const res = await fetch('{{ url("rooms/bookings/poll") }}?date=' + encodeURIComponent(dashViewDate) + '&view=' + encodeURIComponent(dashViewMode));
                if (!res.ok) return;
                const fresh = await res.json();

                const changed = detectAndNotify(fresh);
                if (!changed) return;

                // Sync dashBookings (used by booking modal for conflict detection)
                dashBookings.length = 0;
                fresh.forEach(b => dashBookings.push(b));

                if (dashViewMode === 'day') {
                    updateDayGrid(fresh);
                    updateMobDayView(fresh);
                } else {
                    showRefreshBanner();
                }
            } catch (_) {}
        }

        // ── Notification poll (auth only) ────────────────────────────────
        @auth
        let lastNotifId = 0;

        async function pollNotifications() {
            try {
                const res = await fetch('{{ url("notifications/count") }}');
                if (!res.ok) return;
                const data = await res.json();

                // Update bell badge
                const badge = document.getElementById('notifBellBadge');
                if (data.count > 0) {
                    if (badge) {
                        badge.textContent = data.count;
                    } else {
                        const btn = document.getElementById('notifBellBtn');
                        if (btn) {
                            const s = document.createElement('span');
                            s.className = 'notif-badge';
                            s.id = 'notifBellBadge';
                            s.textContent = data.count;
                            btn.appendChild(s);
                        }
                    }
                } else if (badge) {
                    badge.remove();
                }

                // New notification arrived → toast + refresh open dropdown
                if (lastNotifId && data.latest_id > lastNotifId) {
                    rtToast('You have a new notification', 'info');
                    const dd = document.getElementById('notifDropdown');
                    if (dd && dd.style.display !== 'none') loadNotifDropdown();
                }
                if (data.latest_id) lastNotifId = data.latest_id;
            } catch (_) {}
        }

        pollNotifications();
        setInterval(pollNotifications, POLL_MS);
        @endauth

        // Start polling after 10 s (page just loaded — no immediate need)
        setTimeout(() => {
            pollBookings();
            setInterval(pollBookings, POLL_MS);
        }, POLL_MS);
    })();
</script>
@endsection
