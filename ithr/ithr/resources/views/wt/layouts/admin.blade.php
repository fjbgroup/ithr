<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WT System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        corp: {
                            navy: '#1F2937',
                            brown: '#7C5A3A',
                            gold: '#B38A5A',
                        }
                    }
                }
            }
        }
    </script>
    <script>
        const savedTheme = localStorage.getItem('color-theme');
        const initialTheme = savedTheme === 'dark' || savedTheme === 'light'
            ? savedTheme
            : (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        document.documentElement.classList.toggle('dark', initialTheme === 'dark');
        document.documentElement.dataset.theme = initialTheme;
        document.documentElement.style.colorScheme = initialTheme;
    </script>

    <style>
        :root {
            --corp-navy: #1F2937;
            --corp-ink: #334155;
            --corp-brown: #7C5A3A;
            --corp-gold: #B38A5A;
            --corp-line: #E7E0D6;
            --text-primary: #334155;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --surface-soft: #f8fafc;
            --surface-line: #e2e8f0;
        }

        .dark {
            --text-primary: #e2e8f0;
            --text-secondary: #cbd5e1;
            --text-muted: #94a3b8;
            --surface-soft: #0f172a;
            --surface-line: #334155;
        }

        body {
            font-family: 'Inter', sans-serif;
            background:
                radial-gradient(circle at top right, rgba(59, 130, 246, 0.08), transparent 24%),
                radial-gradient(circle at bottom left, rgba(148, 163, 184, 0.14), transparent 28%),
                linear-gradient(180deg, #F4F7FB 0%, #EDF2F7 100%);
            font-size: 12px;
            color: var(--text-primary);
            transition: background-color 0.3s, color 0.3s;
        }

        .dark body {
            background: #0f172a;
            color: #cbd5e1;
        }

        .sidebar-shell {
            background:
                linear-gradient(180deg, rgba(255,255,255,0.04), rgba(255,255,255,0)),
                linear-gradient(180deg, #243041 0%, #1F2937 48%, #18212E 100%);
        }

        .sidebar-link {
            font-size: 10px;
            transition: all 0.2s;
            border: 1px solid transparent;
            color: #CBD5E1;
            padding-top: 0.5rem !important;
            padding-bottom: 0.5rem !important;
        }

        .sidebar-link:hover {
            background: rgba(255,255,255,0.06);
            color: #FFFFFF;
            border-color: rgba(255,255,255,0.06);
        }

        .brand-company {
            white-space: nowrap;
            font-size: 8px !important;
            letter-spacing: 0.14em !important;
        }

        .sidebar-link.has-info,
        .dropdown-trigger.has-info {
            position: relative;
            overflow: visible;
        }

        .sidebar-link.has-info {
            padding-right: 56px !important;
        }

        .dropdown-trigger.has-info {
            padding-right: 72px !important;
        }

        .sidebar-link.has-info > span:not(.nav-info-slot),
        .dropdown-trigger.has-info > span:not(.nav-info-slot) {
            min-width: 0;
            padding-right: 8px;
            flex: 1;
        }

        .nav-info-slot {
            position: absolute;
            top: 50%;
            margin-top: -7.5px;
            right: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
        }

        .dropdown-trigger .nav-info-slot {
            right: 30px;
        }

        .nav-info-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 15px;
            height: 15px;
            border-radius: 999px;
            border: 1px solid rgba(148, 163, 184, 0.24);
            background: rgba(15, 23, 42, 0.18);
            color: rgba(226, 232, 240, 0.88);
            font-size: 7px;
            font-weight: 900;
            line-height: 1;
            cursor: pointer;
            transition: all 0.18s ease;
            box-shadow: none;
        }

        .nav-info-btn:hover,
        .nav-info-btn.is-open {
            background: #0ea5e9;
            color: #ffffff;
            border-color: rgba(14, 165, 233, 0.72);
        }

        .nav-info-popover {
            position: fixed;
            left: 0;
            top: 0;
            width: min(280px, calc(100vw - 32px));
            padding: 12px 14px;
            border: 1px solid rgba(148, 163, 184, 0.18);
            border-radius: 14px;
            background: rgba(15, 23, 42, 0.98);
            color: #e2e8f0;
            box-shadow: 0 24px 44px rgba(2, 6, 23, 0.4);
            font-size: 11px;
            font-weight: 700;
            line-height: 1.55;
            letter-spacing: 0;
            text-transform: none;
            white-space: normal;
            z-index: 1200;
        }

        .nav-info-popover.hidden {
            display: none !important;
        }

        .active-sidebar {
            background: linear-gradient(135deg, #8D6742, #B38A5A) !important;
            color: #FFFFFF !important;
            border-radius: 12px;
        }

        .dropdown-container { display: none; background: rgba(0,0,0,0.2); border-radius: 8px; margin: 4px 0; }

        input[type="text"],
        input[type="search"],
        textarea {
            text-transform: uppercase;
        }

        input[data-preserve-case="true"],
        textarea[data-preserve-case="true"] {
            text-transform: none !important;
        }

        .theme-text-primary { color: var(--text-primary) !important; }
        .theme-text-secondary { color: var(--text-secondary) !important; }
        .theme-text-muted { color: var(--text-muted) !important; }

        .content-surface .text-\[8px\],
        .content-surface .text-\[9px\] {
            font-size: 10px !important;
            line-height: 1.45 !important;
        }

        .content-surface .text-\[10px\],
        .content-surface .text-\[11px\] {
            font-size: 11px !important;
            line-height: 1.5 !important;
        }

        .content-surface .text-xs,
        .content-surface .text-sm {
            line-height: 1.5 !important;
        }

        .content-surface input,
        .content-surface select,
        .content-surface textarea {
            font-size: 11px !important;
            color: var(--text-primary) !important;
        }

        .page-header-block {
            margin-bottom: 1.5rem;
        }

        .page-title-standard {
            font-size: 1.5rem;
            line-height: 1.15;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--text-primary) !important;
        }

        .page-subtitle-standard {
            margin-top: 0.35rem;
            font-size: 11px;
            line-height: 1.45;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--text-secondary) !important;
        }

        @media (max-width: 768px) {
            .page-header-block {
                margin-bottom: 1rem;
            }

            .page-title-standard {
                font-size: 1.125rem;
                line-height: 1.2;
            }

            .page-subtitle-standard {
                margin-top: 0.25rem;
                font-size: 10px;
                letter-spacing: 0.08em;
            }
        }

        .dataTables_wrapper { 
            font-size: 11px;
            color: var(--text-secondary);
        }
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            padding: 12px 16px 10px;
            box-sizing: border-box;
        }
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid rgba(226, 232, 240, 0.95) !important;
            border-radius: 12px !important;
            padding: 8px 12px !important;
            background: #fff !important;
            outline: none !important;
            margin-left: 8px !important;
            transition: all 0.3s;
        }
        .dark .dataTables_wrapper .dataTables_filter input {
            background: #1e293b !important;
            border-color: #334155 !important;
            color: #f1f5f9 !important;
        }
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid rgba(226, 232, 240, 0.95) !important;
            border-radius: 12px !important;
            padding: 6px 10px !important;
            background: #fff !important;
            outline: none !important;
            margin: 0 6px !important;
            transition: all 0.3s;
        }
        .dark .dataTables_wrapper .dataTables_length select {
            background: #1e293b !important;
            border-color: #334155 !important;
            color: #f1f5f9 !important;
        }
        .dataTables_wrapper .dataTables_length label,
        .dataTables_wrapper .dataTables_filter label {
            font-weight: 700;
            font-size: 11px;
            color: var(--text-secondary);
        }
        .dark .dataTables_wrapper .dataTables_length label,
        .dark .dataTables_wrapper .dataTables_filter label {
            color: #cbd5e1 !important;
        }
        /* Prevent controls being clipped by overflow containers */
        .dataTables_wrapper { overflow: visible; }
        table.dataTable thead th {
            border-bottom: 1px solid var(--surface-line) !important;
            background: var(--surface-soft);
            padding: 12px 10px !important;
            color: var(--text-secondary) !important;
            font-size: 10px !important;
            font-weight: 800 !important;
            letter-spacing: 0.14em !important;
            text-transform: uppercase !important;
            transition: all 0.3s;
        }
        table.dataTable tbody td {
            color: var(--text-primary);
            font-size: 11px !important;
            padding: 20px 16px !important;
            vertical-align: middle;
        }
        .content-surface {
            background: transparent;
            border: 1px solid rgba(139, 94, 60, 0.12);
            box-shadow: 
                0 4px 6px -1px rgba(0, 0, 0, 0.05),
                0 10px 15px -3px rgba(0, 0, 0, 0.1),
                0 20px 25px -5px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .dark .content-surface {
            background: rgba(30, 41, 59, 0.7) !important;
            backdrop-filter: blur(20px);
            border-color: rgba(255, 255, 255, 0.05) !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important;
        }
        .dark table.dataTable thead th {
            background: #1e293b !important;
            border-color: #334155 !important;
            color: #94a3b8 !important;
        }
        .dark table.dataTable tbody td {
            border-color: #1e293b !important;
        }

        .dataTables_wrapper.adminit-footer-mounted .dataTables_info,
        .dataTables_wrapper.adminit-footer-mounted .dataTables_paginate {
            display: none !important;
        }

        .adminit-table-footer {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 22px;
            padding: 28px 48px 34px;
            box-sizing: border-box;
            border-top: 0;
            background: transparent;
        }

        .dataTables_wrapper.adminit-footer-mounted .adminit-table-footer ~ .adminit-table-footer,
        .dataTables_wrapper .adminit-table-footer ~ .adminit-table-footer {
            display: none !important;
        }

        .adminit-table-info {
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.4;
            white-space: nowrap;
        }

        .adminit-table-pagination {
            display: inline-flex;
            align-items: center;
            justify-content: flex-end;
            gap: 18px;
            flex-shrink: 0;
            margin-left: auto;
        }

        .adminit-page-link,
        .adminit-page-current {
            min-width: 58px;
            height: 56px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            border: 1px solid transparent;
            background: transparent;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
            line-height: 1;
        }

        .adminit-page-link {
            padding: 0 8px;
            cursor: pointer;
        }

        .adminit-page-link:not(:disabled):hover {
            color: #334155;
        }

        .adminit-page-link:disabled {
            cursor: default;
            opacity: 0.55;
        }

        .adminit-page-current {
            border-color: #cbd5e1;
            background: #ffffff;
            color: #64748b;
        }

        .adminit-page-current.hidden {
            display: none !important;
        }

        .dark .adminit-table-footer {
            background: #182233;
        }

        .dark .adminit-table-info,
        .dark .adminit-page-link,
        .dark .adminit-page-current {
            color: #cbd5e1;
        }

        .dark .adminit-page-current {
            background: #1e293b;
            border-color: #475569;
        }

        @media (max-width: 640px) {
            .adminit-table-footer {
                align-items: flex-start;
                flex-direction: column;
                padding: 20px 24px 26px;
            }

            .adminit-table-pagination {
                margin-left: 0;
            }
        }

        .nav-link.sidebar-active {
            background: linear-gradient(135deg, #B38A5A, #8D6742);
            color: #fff !important;
        }

        .wt-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            flex-shrink: 0;
            min-height: 28px;
            padding: 5px 10px;
            border-radius: 8px;
            border: 1px solid rgba(96, 165, 250, 0.28);
            background: rgba(15, 23, 42, 0.96);
            color: #e2e8f0;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            line-height: 1;
            white-space: nowrap;
            cursor: pointer;
            transition: all 0.18s ease;
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.12);
        }
        .wt-btn svg,
        .wt-btn i {
            flex-shrink: 0;
        }
        .wt-btn:hover {
            transform: translateY(-1px);
            background: #162033;
            border-color: rgba(96, 165, 250, 0.42);
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.18);
        }
        .wt-btn-brown {
            background: #3D2B1F;
            border-color: #5D4037;
            color: #f5f5f4;
        }
        .wt-btn-brown:hover {
            background: #2D1B0F;
            border-color: #7C5A3A;
        }
        .wt-btn-danger {
            background: #7f1d1d;
            border-color: #b91c1c;
            color: #fee2e2;
        }
        .wt-btn-danger:hover {
            background: #991b1b;
            border-color: #ef4444;
        }
        .wt-btn-success {
            background: #166534;
            border-color: #22c55e;
            color: #ecfdf5;
        }
        .wt-btn-success:hover {
            background: #15803d;
            border-color: #4ade80;
        }
        .wt-btn-soft {
            background: #f8fafc;
            border-color: rgba(59, 130, 246, 0.28);
            color: #334155;
            box-shadow: none;
        }
        .wt-btn-soft:hover {
            background: #eef2ff;
            border-color: rgba(59, 130, 246, 0.4);
            color: #1e293b;
        }
        .wt-btn-sm {
            min-height: 24px;
            padding: 3px 7px;
            border-radius: 6px;
            font-size: 8px;
            letter-spacing: 0.04em;
        }
        .dark .wt-btn-soft {
            background: transparent;
            border-color: rgba(96, 165, 250, 0.32);
            color: #e2e8f0;
        }
        .dark .wt-btn-soft:hover {
            background: rgba(30, 41, 59, 0.9);
            border-color: rgba(96, 165, 250, 0.42);
            color: #f8fafc;
        }

        /* ===== Compact Data-Centric Admin Theme ===== */
        .dark body {
            background: #0f172a !important;
            color: #e2e8f0 !important;
        }

        .dark .content-surface {
            background: #0f172a !important;
            border: 0 !important;
            border-radius: 4px !important;
            box-shadow: none !important;
            padding: 12px !important;
        }

        .page-header-block {
            margin-bottom: 12px !important;
        }

        .dark .page-title-standard,
        .dark .approval-inbox .approval-title {
            font-size: 14px !important;
            line-height: 1.2 !important;
            color: #f8fafc !important;
            letter-spacing: 0 !important;
        }

        .dark .page-subtitle-standard,
        .dark .approval-inbox .approval-subtitle {
            margin-top: 4px !important;
            font-size: 9px !important;
            line-height: 1.3 !important;
            color: #94a3b8 !important;
            letter-spacing: 0.08em !important;
        }

        .wt-btn,
        .navy-btn,
        .navy-btn-danger,
        .navy-btn-soft,
        .btn-cancel,
        .btn-submit {
            min-height: 22px !important;
            padding: 4px 8px !important;
            border-radius: 4px !important;
            border: 1px solid rgba(56, 189, 248, 0.35) !important;
            background: rgba(30, 41, 59, 0.72) !important;
            color: #e2e8f0 !important;
            box-shadow: none !important;
            font-size: 9px !important;
            letter-spacing: 0.04em !important;
            gap: 4px !important;
        }

        .wt-btn:hover,
        .navy-btn:hover,
        .btn-submit:hover {
            background: rgba(56, 189, 248, 0.12) !important;
            border-color: #38bdf8 !important;
            color: #f8fafc !important;
            transform: none !important;
        }

        .wt-btn-danger,
        .navy-btn-danger {
            border-color: rgba(148, 163, 184, 0.42) !important;
            color: #cbd5e1 !important;
        }

        .wt-btn-success {
            border-color: rgba(56, 189, 248, 0.44) !important;
            color: #bae6fd !important;
        }

        .wt-btn svg,
        .wt-btn i,
        .navy-btn i {
            width: 12px !important;
            height: 12px !important;
            font-size: 12px !important;
        }

        .filter-bar,
        .approval-card,
        .inventory-table-shell,
        #mainTableContainer.bg-white,
        .bg-white.rounded-2xl,
        .bg-white.rounded-3xl {
            background: #1e293b !important;
            border: 1px solid rgba(255,255,255,0.05) !important;
            border-radius: 4px !important;
            box-shadow: none !important;
            padding: 8px !important;
        }

        .navy-panel {
            background: #1e293b !important;
            border-bottom: 1px solid rgba(255,255,255,0.05) !important;
            padding: 8px !important;
        }

        .navy-panel h4 {
            font-size: 10px !important;
            letter-spacing: 0.08em !important;
        }

        .navy-chip,
        .compact-warning-badge,
        table.dataTable tbody td span[class*="bg-"] {
            border-radius: 4px !important;
            background: rgba(100, 116, 139, 0.22) !important;
            border: 1px solid rgba(148, 163, 184, 0.18) !important;
            color: #cbd5e1 !important;
            padding: 2px 6px !important;
            font-size: 8px !important;
            line-height: 1.2 !important;
        }

        .dataTables_wrapper,
        .dataTables_wrapper label,
        .dataTables_wrapper .dataTables_info,
        .adminit-table-info,
        .inventory-table-info {
            color: #94a3b8 !important;
            font-size: 10px !important;
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .inventory-table-controls,
        .maintenance-table-controls {
            padding: 4px !important;
            margin-bottom: 4px !important;
            gap: 4px !important;
        }

        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select,
        .search-input,
        .filter-select,
        .form-input,
        .select2-container .select2-selection--single {
            min-height: 24px !important;
            height: 24px !important;
            border-radius: 4px !important;
            border: 1px solid rgba(255,255,255,0.08) !important;
            background: #0f172a !important;
            color: #e2e8f0 !important;
            padding: 3px 8px !important;
            font-size: 10px !important;
            box-shadow: none !important;
        }

        table.dataTable,
        table.dataTable.no-footer {
            border-collapse: collapse !important;
            border: 1px solid rgba(255,255,255,0.05) !important;
        }

        table.dataTable thead th,
        .dataTables_wrapper table.dataTable thead th {
            height: 28px !important;
            padding: 4px 8px !important;
            background: #1e293b !important;
            border: 1px solid rgba(255,255,255,0.05) !important;
            color: #94a3b8 !important;
            font-size: 9px !important;
            line-height: 1.2 !important;
            letter-spacing: 0.08em !important;
        }

        table.dataTable tbody td,
        .dataTables_wrapper table.dataTable tbody td {
            height: 32px !important;
            max-height: 32px !important;
            padding: 4px 8px !important;
            border: 1px solid rgba(255,255,255,0.05) !important;
            color: #e2e8f0 !important;
            font-size: 12px !important;
            line-height: 1.2 !important;
            vertical-align: middle !important;
        }

        .dataTables_wrapper table.dataTable tbody tr:nth-child(odd),
        .dataTables_wrapper table.dataTable tbody tr:nth-child(even),
        #specialTable.dataTable tbody tr:nth-child(odd),
        #specialTable.dataTable tbody tr:nth-child(even) {
            background: #1e293b !important;
        }

        .dataTables_wrapper table.dataTable tbody tr:hover,
        #specialTable.dataTable tbody tr:hover {
            background: rgba(56, 189, 248, 0.08) !important;
        }

        .adminit-table-footer,
        .inventory-table-footer {
            padding: 8px 4px 0 !important;
            background: #1e293b !important;
            gap: 8px !important;
        }

        .adminit-page-link,
        .adminit-page-current,
        .inventory-page-link {
            min-width: 38px !important;
            height: 24px !important;
            border-radius: 4px !important;
            font-size: 10px !important;
            color: #94a3b8 !important;
        }

        .request-meta-block {
            margin-top: 4px !important;
            padding: 4px !important;
            border-radius: 4px !important;
            background: #0f172a !important;
            border-color: rgba(255,255,255,0.05) !important;
        }

        .modal-box {
            background: #1e293b !important;
            border-color: rgba(255,255,255,0.06) !important;
            border-radius: 4px !important;
            color: #e2e8f0 !important;
        }

        .modal-header,
        .modal-body,
        .modal-footer {
            padding: 12px !important;
            background: #1e293b !important;
            border-color: rgba(255,255,255,0.05) !important;
        }

        .modal-title {
            color: #f8fafc !important;
            font-size: 14px !important;
        }

        .modal-subtitle,
        .form-label {
            color: #94a3b8 !important;
            font-size: 9px !important;
        }

        .active-sidebar,
        .nav-link.sidebar-active,
        .mobile-role-switcher a.bg-\[\#B38A5A\],
        a.bg-\[\#B38A5A\] {
            background: #075985 !important;
            color: #f8fafc !important;
        }

        .sub-nav-link.active {
            color: #38bdf8 !important;
            background: rgba(56, 189, 248, 0.08) !important;
        }

        .sub-nav-link.active::before {
            background: #38bdf8 !important;
            box-shadow: 0 0 8px rgba(56, 189, 248, 0.45) !important;
        }

        /* Dropdown Styles - Improved Visibility */
        .dropdown-wrapper { position: relative; margin-bottom: 2px; }
        .dropdown-trigger { 
            cursor: pointer; display: flex; align-items: center; justify-content: space-between; 
            padding: 10px 16px; border-radius: 12px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            color: #94a3b8; font-weight: 600;
        }
        .dropdown-trigger:hover { 
            background: rgba(255, 255, 255, 0.05); 
            color: #f1f5f9; 
        }
        .dropdown-wrapper.open .dropdown-trigger {
            color: #fff;
            background: rgba(255, 255, 255, 0.03);
        }
        .dropdown-content { 
            max-height: 0; overflow: hidden; transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); 
            margin-top: 2px; padding-left: 20px; 
            position: relative;
            background: rgba(0, 0, 0, 0.15);
            border-radius: 0 0 16px 16px;
        }
        .dropdown-content::before {
            content: "";
            position: absolute;
            left: 24px;
            top: 0;
            bottom: 10px;
            width: 1.5px;
            background: linear-gradient(to bottom, rgba(209, 174, 123, 0.4), transparent);
        }
        .dropdown-wrapper.open .dropdown-content { max-height: 320px; padding-bottom: 10px; }
        .dropdown-wrapper.open .dropdown-chevron { transform: rotate(180deg); color: #D1AE7B; }
        .dropdown-chevron { transition: transform 0.3s; font-size: 8px; color: #475569; }

        .sub-nav-group-label {
            display: block;
            padding: 10px 16px 4px 18px;
            font-size: 8px;
            font-weight: 900;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .sub-nav-link + .sub-nav-group-label {
            margin-top: 8px;
            padding-top: 10px;
            border-top: 1px solid rgba(148, 163, 184, 0.12);
        }

        .faulty-nav-list {
            padding-left: 20px;
            background: transparent;
            border: 0;
            border-radius: 0;
        }
        
        .sub-nav-link {
            display: flex; align-items: center; gap: 12px; padding: 8px 16px 8px 18px;
            font-size: 10.5px; color: #a1a1aa; border-radius: 10px; transition: all 0.2s;
            margin-bottom: 2px; position: relative;
        }
        .pending-nav-badge {
            margin-left: auto;
            min-width: 14px;
            height: 16px;
            padding: 0 5px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #ef4444;
            color: #ffffff;
            font-size: 7.5px;
            font-weight: 900;
            line-height: 1;
            letter-spacing: 0.08em;
            box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.18);
        }
        .approval-new-badge {
            margin-left: 2px;
            min-width: auto;
            height: auto;
            padding: 0;
            border-radius: 0;
            background: transparent;
            color: #fca5a5;
            font-size: 8px;
            letter-spacing: 0.02em;
            text-transform: none;
            box-shadow: none;
        }
        .sub-nav-link:hover { color: #f8fafc; background: rgba(255, 255, 255, 0.05); }
        .sub-nav-link.active { color: #D1AE7B; font-weight: 800; background: rgba(209, 174, 123, 0.08); }
        .sub-nav-link.active::before {
            content: "";
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: #D1AE7B;
            box-shadow: 0 0 8px #D1AE7B;
        }
        
        .dark .dropdown-content { background: rgba(0, 0, 0, 0.25); }

        .logout-modal-overlay {
            position: fixed;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(15, 23, 42, 0.62);
            backdrop-filter: blur(8px);
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity 0.22s ease, visibility 0.22s ease;
            z-index: 80;
        }

        .logout-modal-overlay.active {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        .logout-modal {
            width: 100%;
            max-width: 430px;
            border-radius: 28px;
            border: 1px solid rgba(179, 138, 90, 0.18);
            background:
                radial-gradient(circle at top right, rgba(179, 138, 90, 0.16), transparent 36%),
                linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            box-shadow: 0 28px 60px rgba(15, 23, 42, 0.28);
            transform: translateY(12px) scale(0.98);
            transition: transform 0.22s ease;
            overflow: hidden;
        }

        .logout-modal-overlay.active .logout-modal {
            transform: translateY(0) scale(1);
        }

        .dark .logout-modal {
            border-color: rgba(209, 174, 123, 0.16);
            background:
                radial-gradient(circle at top right, rgba(179, 138, 90, 0.14), transparent 36%),
                linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            box-shadow: 0 28px 70px rgba(0, 0, 0, 0.45);
        }

        .logout-modal-icon {
            width: 62px;
            height: 62px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #B38A5A, #8D6742);
            color: #fff;
            font-size: 24px;
            box-shadow: 0 16px 32px rgba(179, 138, 90, 0.24);
        }

        .logout-modal-title {
            font-size: 1.15rem;
            line-height: 1.2;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--text-primary);
        }

        .logout-modal-copy {
            font-size: 11px;
            line-height: 1.65;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: var(--text-secondary);
        }

        .logout-modal-close {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            border: 1px solid rgba(148, 163, 184, 0.25);
            background: rgba(255, 255, 255, 0.75);
            color: #64748b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.18s ease;
        }

        .logout-modal-close:hover {
            color: #ef4444;
            border-color: rgba(239, 68, 68, 0.28);
            background: rgba(255, 255, 255, 0.95);
        }

        .dark .logout-modal-close {
            background: rgba(15, 23, 42, 0.72);
            color: #94a3b8;
            border-color: rgba(71, 85, 105, 0.55);
        }

        .dark .logout-modal-close:hover {
            color: #fca5a5;
            border-color: rgba(248, 113, 113, 0.35);
            background: rgba(30, 41, 59, 0.96);
        }

        .logout-modal-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        .logout-modal-btn {
            min-width: 140px;
            padding: 12px 18px;
            border-radius: 16px;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            transition: all 0.18s ease;
        }

        .logout-modal-btn-cancel {
            border: 1px solid rgba(148, 163, 184, 0.28);
            background: rgba(255, 255, 255, 0.82);
            color: #475569;
        }

        .logout-modal-btn-cancel:hover {
            background: rgba(241, 245, 249, 1);
            color: #0f172a;
        }

        .dark .logout-modal-btn-cancel {
            background: rgba(15, 23, 42, 0.76);
            color: #cbd5e1;
            border-color: rgba(71, 85, 105, 0.58);
        }

        .dark .logout-modal-btn-cancel:hover {
            background: rgba(30, 41, 59, 0.96);
            color: #f8fafc;
        }

        .logout-modal-btn-confirm {
            border: 1px solid rgba(239, 68, 68, 0.22);
            background: linear-gradient(135deg, #b91c1c, #dc2626);
            color: #fff;
            box-shadow: 0 16px 30px rgba(220, 38, 38, 0.18);
        }

        .logout-modal-btn-confirm:hover {
            transform: translateY(-1px);
            box-shadow: 0 18px 34px rgba(220, 38, 38, 0.26);
        }

        @media (max-width: 640px) {
            .logout-modal {
                border-radius: 24px;
            }

            .logout-modal-actions {
                flex-direction: column-reverse;
            }

            .logout-modal-btn {
                width: 100%;
                min-width: 0;
            }
        }


        .dark div.bg-white,
        .dark div.bg-stone-50\/50,
        .dark div.bg-slate-50,
        .dark section.bg-white {
            background-color: #1e293b !important;
            border-color: #334155 !important;
        }

        .dark .border-stone-100, .dark .border-stone-50, .dark .border-slate-100 { 
            border-color: #334155 !important; 
        }

        .dark .text-stone-800, .dark .text-stone-700, .dark .text-[#3D2B1F], .dark .text-slate-800, .dark .text-slate-900 {
            color: #f1f5f9 !important;
        }

        .dark .text-stone-600, .dark .text-stone-500, .dark .text-slate-600, .dark .text-slate-500 {
            color: #94a3b8 !important;
        }

        .dark .text-stone-400, .dark .text-slate-400 { color: #64748b !important; }
        
        /* Placeholder Styling */
        input::placeholder, 
        textarea::placeholder {
            color: rgba(100, 116, 139, 0.6) !important;
            text-transform: none !important;
        }

        .dark input, 
        .dark select, 
        .dark textarea {
            background-color: #0f172a !important;
            border-color: #334155 !important;
            color: #f1f5f9 !important;
        }

        .dark input::placeholder, 
        .dark textarea::placeholder {
            color: rgba(148, 163, 184, 0.8) !important;
        }

        /* Select2 Placeholder Fixes */
        .select2-container--default .select2-selection--multiple .select2-selection__placeholder,
        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: rgba(100, 116, 139, 0.6) !important;
        }

        .dark .select2-container--default .select2-selection--multiple .select2-selection__placeholder,
        .dark .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: rgba(148, 163, 184, 0.8) !important;
        }

        .dark .select2-container--default .select2-selection--single,
        .dark .select2-container--default .select2-selection--multiple {
            background-color: #0f172a !important;
            border-color: #334155 !important;
            color: #f1f5f9 !important;
        }

        .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #f1f5f9 !important;
        }

        .dark table tbody tr:hover {
            background-color: rgba(255,255,255,0.03) !important;
        }

        .topbar-shell {
            background: rgba(255,255,255,0.88);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.95);
            transition: all 0.3s;
        }
        .dark .topbar-shell {
            background: rgba(15, 23, 42, 0.88);
            border-color: rgba(51, 65, 85, 0.5);
        }

        .topbar-action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 52px;
            height: 52px;
            border: 1px solid rgba(148, 163, 184, 0.28);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.64);
            color: #475569;
            box-shadow: none;
            transition: background-color 0.2s, border-color 0.2s, color 0.2s;
        }

        .topbar-action-btn:hover {
            background: rgba(241, 245, 249, 0.92);
            border-color: rgba(14, 165, 233, 0.45);
            color: #075985;
        }

        .topbar-action-btn i {
            font-size: 14px;
        }

        .topbar-signout-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 52px;
            padding: 0 24px;
            border: 1px solid rgba(148, 163, 184, 0.28);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.64);
            color: #475569;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            transition: background-color 0.2s, border-color 0.2s, color 0.2s;
        }

        .topbar-signout-btn:hover {
            background: rgba(254, 242, 242, 0.92);
            border-color: rgba(248, 113, 113, 0.38);
            color: #b91c1c !important;
        }

        .dark .topbar-action-btn,
        .dark .topbar-signout-btn {
            background: rgba(15, 23, 42, 0.42);
            border-color: rgba(148, 163, 184, 0.24);
            color: #cbd5e1;
        }

        .dark .topbar-action-btn:hover {
            background: rgba(30, 41, 59, 0.82);
            border-color: rgba(56, 189, 248, 0.4);
            color: #7dd3fc;
        }

        .dark .topbar-signout-btn:hover {
            background: rgba(127, 29, 29, 0.18);
            border-color: rgba(248, 113, 113, 0.32);
            color: #fca5a5 !important;
        }

        .topbar-role-switcher {
            height: 52px;
            padding: 4px;
            border-radius: 14px;
        }

        .topbar-role-switcher > a,
        .topbar-role-switcher select {
            height: 42px;
            border-radius: 10px;
        }

        .page-chip {
            border: 1px solid rgba(51, 65, 85, 0.12);
            background: rgba(31, 41, 55, 0.05);
            color: var(--corp-navy);
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.18em;
            transition: all 0.3s;
        }
        .dark .page-chip {
            border-color: rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.05);
            color: #f1f5f9;
        }

        .content-surface h1,
        .content-surface h2,
        .content-surface h3,
        .content-surface h4,
        .content-surface .modal-title {
            color: #1e293b !important;
            transition: color 0.3s;
        }
        .dark .content-surface h1,
        .dark .content-surface h2,
        .dark .content-surface h3,
        .dark .content-surface h4,
        .dark .content-surface .modal-title {
            color: #f1f5f9 !important;
        }

        /* Global scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .dark ::-webkit-scrollbar-thumb { background: #334155; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .dark ::-webkit-scrollbar-thumb:hover { background: #475569; }
        ::-webkit-scrollbar-track { background: transparent; }

        /* DataTables scrollbar */
        .dataTables_scrollBody::-webkit-scrollbar { height: 6px !important; width: 6px !important; display: block !important; }
        .dataTables_scrollBody::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.06); border-radius: 10px; }
        .dataTables_scrollBody:hover::-webkit-scrollbar-thumb { background: #cbd5e1; }
        .dataTables_scrollBody::-webkit-scrollbar-track { background: transparent; }

        /* ===== MOBILE SIDEBAR OVERLAY ===== */
        #mobileSidebarOverlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 40;
            backdrop-filter: blur(2px);
        }
        #mobileSidebarOverlay.active { display: block; }

        /* Mobile sidebar slide-in */
        #mobileSidebar {
            position: fixed;
            top: 0; left: 0; bottom: 0;
            width: 280px;
            z-index: 50;
            transform: translateX(-100%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #mobileSidebar.active { transform: translateX(0); }

        /* Hamburger button */
        #hamburgerBtn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 34px; height: 34px;
            border-radius: 8px;
            background: rgba(31, 41, 55, 0.06);
            border: 1px solid rgba(31, 41, 55, 0.08);
            cursor: pointer;
            transition: all 0.15s;
        }
        #hamburgerBtn:hover { background: rgba(31, 41, 55, 0.10); }

        /* Hide hamburger on large screens */
        @media (min-width: 1024px) {
            #hamburgerBtn { display: none; }
            #mobileSidebarOverlay, #mobileSidebar { display: none !important; }
        }

        /* Mobile table cards */
        @media (max-width: 768px) {
            .topbar-shell {
                padding-left: 14px;
                padding-right: 14px;
            }
            .content-surface {
                border-radius: 16px;
                padding: 14px;
            }
            .page-chip {
                max-width: calc(100vw - 132px);
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            .wt-btn {
                width: 100%;
                min-height: 42px;
            }
            .mobile-topbar-actions {
                flex-shrink: 0;
                align-self: flex-start;
                gap: 6px;
            }
            .mobile-topbar-actions > button,
            .mobile-topbar-actions > form + button {
                min-width: 40px;
                min-height: 40px;
            }
            .dataTables_wrapper .dataTables_filter,
            .dataTables_wrapper .dataTables_length {
                float: none !important;
                text-align: left !important;
                margin-bottom: 8px;
                padding: 0 0 10px !important;
            }
            .dataTables_wrapper .dataTables_filter input,
            .dataTables_wrapper .dataTables_length select {
                width: 100% !important;
                margin: 8px 0 0 !important;
            }
            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate {
                float: none !important;
                text-align: center !important;
            }
            .dataTables_wrapper .dataTables_paginate {
                display: flex;
                justify-content: center;
                margin-top: 12px;
            }
            .modal-overlay {
                padding: 10px;
                align-items: flex-end;
            }
            .modal-box {
                width: 100%;
                max-height: min(88vh, 720px);
                border-radius: 22px 22px 0 0;
            }
            .modal-header,
            .modal-body,
            .modal-footer {
                padding-left: 16px;
                padding-right: 16px;
            }
            .modal-form-grid {
                grid-template-columns: 1fr;
            }
            .modal-footer {
                flex-direction: column-reverse;
                align-items: stretch;
            }
            .modal-footer > * {
                width: 100%;
            }
            .mobile-topbar-actions .mobile-hide-label {
                display: none;
            }
            .mobile-role-switcher {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                width: 100%;
                margin-top: 0;
                gap: 4px;
            }
            .mobile-role-switcher a,
            .mobile-role-switcher select,
            .mobile-role-switcher button {
                justify-content: center;
                min-height: 38px;
                padding-left: 6px;
                padding-right: 6px;
                font-size: 8px !important;
                letter-spacing: 0.08em;
            }
            .mobile-executive-switch-form {
                grid-column: 1 / -1;
            }
        }

        /* ===== GLOBAL STANDARDIZED MODAL STYLES ===== */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(8px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .modal-overlay.active { display: flex; }
        
        .modal-box {
            background: #fff;
            border-radius: 20px;
            width: 95%;
            max-width: 820px;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 40px 100px rgba(15, 23, 42, 0.25);
            overflow: hidden;
            border: 1px solid rgba(226, 232, 240, 0.8);
            transition: all 0.3s;
        }
        .dark .modal-box {
            background: #1e293b;
            border-color: #334155;
            box-shadow: 0 40px 100px rgba(0, 0, 0, 0.4);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 24px;
            border-bottom: 1px solid #f1f5f9;
            background: #fff;
            flex-shrink: 0;
            transition: all 0.3s;
        }
        .dark .modal-header {
            background: #1e293b;
            border-color: #334155;
        }
        .modal-title {
            font-size: 14px;
            font-weight: 800;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .modal-close-btn {
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 12px;
            padding: 8px;
            cursor: pointer;
            color: #94a3b8;
            transition: all 0.2s;
        }
        .modal-close-btn:hover {
            background: #f1f5f9;
            color: #ef4444;
        }
        .dark .modal-close-btn {
            background: #334155;
            border-color: #475569;
            color: #94a3b8;
        }

        .modal-body {
            padding: 24px;
            overflow-y: auto;
            flex: 1;
            transition: all 0.3s;
        }
        .dark .modal-body {
            background: #1e293b;
        }
        .modal-body::-webkit-scrollbar { width: 5px; }
        .modal-body::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .dark .modal-body::-webkit-scrollbar-thumb { background: #334155; }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 12px;
            padding: 16px 24px;
            border-top: 1px solid #f1f5f9;
            background: #f8fafc;
            flex-shrink: 0;
            transition: all 0.3s;
        }
        .dark .modal-footer {
            background: #0f172a;
            border-color: #334155;
        }

        .modal-form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }
        .modal-form-group { display: flex; flex-direction: column; gap: 6px; }
        .modal-form-label {
            font-size: 10px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .modal-form-input {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 10px 14px;
            font-size: 12px;
            color: #1e293b;
            background: #fff;
            transition: all 0.2s;
        }
        .dark .modal-form-input {
            background: #0f172a;
            border-color: #334155;
            color: #f1f5f9;
        }
        .modal-form-input:focus {
            border-color: #B38A5A;
            box-shadow: 0 0 0 4px rgba(179, 138, 90, 0.1);
            outline: none;
        }
        .admin-table-export-actions .dt-buttons {
            display: flex;
            gap: 0.35rem;
            flex-wrap: wrap;
            float: none;
        }
        .admin-table-export-actions .dt-button {
            margin: 0 !important;
            border: 1px solid rgba(148, 163, 184, 0.35) !important;
            border-radius: 6px !important;
            background: rgba(15, 23, 42, 0.06) !important;
            color: var(--text-primary) !important;
            font-size: 8px !important;
            font-weight: 800 !important;
            letter-spacing: 0.06em !important;
            text-transform: uppercase !important;
            min-height: 26px !important;
            padding: 0.35rem 0.55rem !important;
            line-height: 1 !important;
            box-shadow: none !important;
        }
        .dark .admin-table-export-actions .dt-button {
            background: rgba(15, 23, 42, 0.45) !important;
            color: #e2e8f0 !important;
            border-color: rgba(96, 165, 250, 0.25) !important;
        }
        .dataTables_wrapper > .dt-buttons {
            display: none;
        }

        /* Final menu color normalization: match Inventory List compact blue theme */
        .sidebar-shell {
            background: #1e293b !important;
            border-right: 1px solid rgba(255,255,255,0.05) !important;
        }

        .sidebar-link,
        .dropdown-trigger.sidebar-link,
        .sub-nav-link {
            border-radius: 4px !important;
            color: #cbd5e1 !important;
            background: transparent !important;
            border: 1px solid transparent !important;
            box-shadow: none !important;
        }

        .sidebar-link:hover,
        .dropdown-trigger.sidebar-link:hover,
        .sub-nav-link:hover {
            color: #f8fafc !important;
            background: rgba(56, 189, 248, 0.08) !important;
            border-color: rgba(56, 189, 248, 0.12) !important;
        }

        .active-sidebar,
        .sidebar-link.active-sidebar,
        .nav-link.sidebar-active,
        .sub-nav-link.active,
        .dropdown-wrapper.open > .dropdown-trigger.sidebar-link {
            background: #075985 !important;
            background-image: none !important;
            color: #f8fafc !important;
            border-color: rgba(56, 189, 248, 0.34) !important;
            border-radius: 4px !important;
            box-shadow: none !important;
        }

        .active-sidebar i,
        .sidebar-link.active-sidebar i,
        .sub-nav-link.active i,
        .dropdown-wrapper.open > .dropdown-trigger.sidebar-link i {
            color: #e0f2fe !important;
        }

        .sub-nav-link.active::before {
            background: #38bdf8 !important;
            box-shadow: none !important;
        }

        .mobile-role-switcher a.bg-\[\#B38A5A\],
        a.bg-\[\#B38A5A\],
        .bg-\[\#B38A5A\] {
            background: #075985 !important;
            background-image: none !important;
            color: #f8fafc !important;
        }

        .bg-gradient-to-br.from-\[\#B38A5A\].to-\[\#8D6742\],
        .from-\[\#B38A5A\].to-\[\#8D6742\] {
            background: #075985 !important;
            background-image: none !important;
        }

        .topbar-shell a:hover,
        nav[aria-label="Breadcrumb"] a:hover,
        #notificationDropdown button {
            color: #38bdf8 !important;
        }

        .logout-modal-icon {
            background: #075985 !important;
            box-shadow: none !important;
        }

        /* Unified system theme normalization */
        html:not(.dark) body {
            background:
                radial-gradient(circle at top right, rgba(59, 130, 246, 0.08), transparent 24%),
                radial-gradient(circle at bottom left, rgba(148, 163, 184, 0.14), transparent 28%),
                linear-gradient(180deg, #F4F7FB 0%, #EDF2F7 100%) !important;
            color: #334155 !important;
        }

        html:not(.dark) .content-surface {
            background: rgba(255, 255, 255, 0.98) !important;
            border: 1px solid rgba(226, 232, 240, 0.95) !important;
            box-shadow: 0 24px 54px rgba(15, 23, 42, 0.08) !important;
        }

        html:not(.dark) .filter-bar,
        html:not(.dark) .approval-card,
        html:not(.dark) .inventory-table-shell,
        html:not(.dark) #mainTableContainer.bg-white,
        html:not(.dark) .content-surface .bg-white.rounded-2xl,
        html:not(.dark) .content-surface .bg-white.rounded-3xl,
        html:not(.dark) .content-surface div.bg-white,
        html:not(.dark) .content-surface section.bg-white {
            background: #ffffff !important;
            border-color: #e2e8f0 !important;
            color: #334155 !important;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06) !important;
        }

        html:not(.dark) .navy-panel,
        html:not(.dark) .content-surface .bg-stone-50,
        html:not(.dark) .content-surface .bg-slate-50 {
            background: #f8fafc !important;
            border-color: #e2e8f0 !important;
            color: #334155 !important;
        }

        html:not(.dark) .page-title-standard,
        html:not(.dark) .approval-inbox .approval-title,
        html:not(.dark) .content-surface h1,
        html:not(.dark) .content-surface h2,
        html:not(.dark) .content-surface h3,
        html:not(.dark) .content-surface h4,
        html:not(.dark) .content-surface .modal-title {
            color: #1e293b !important;
        }

        html:not(.dark) .page-subtitle-standard,
        html:not(.dark) .approval-inbox .approval-subtitle,
        html:not(.dark) .theme-text-secondary {
            color: #64748b !important;
        }

        html:not(.dark) .content-surface .text-stone-800,
        html:not(.dark) .content-surface .text-stone-700,
        html:not(.dark) .content-surface .text-slate-800,
        html:not(.dark) .content-surface .text-slate-900,
        html:not(.dark) .content-surface .text-\[\#3D2B1F\] {
            color: #1e293b !important;
        }

        html:not(.dark) .content-surface .text-stone-600,
        html:not(.dark) .content-surface .text-stone-500,
        html:not(.dark) .content-surface .text-slate-600,
        html:not(.dark) .content-surface .text-slate-500 {
            color: #64748b !important;
        }

        html:not(.dark) .topbar-shell {
            background: rgba(255, 255, 255, 0.88) !important;
            border-bottom-color: rgba(226, 232, 240, 0.95) !important;
        }

        html:not(.dark) .dataTables_wrapper,
        html:not(.dark) .dataTables_wrapper label,
        html:not(.dark) .dataTables_wrapper .dataTables_info,
        html:not(.dark) .adminit-table-info,
        html:not(.dark) .inventory-table-info {
            color: #64748b !important;
        }

        html:not(.dark) .adminit-table-footer,
        html:not(.dark) .inventory-table-footer {
            background: #ffffff !important;
            border-top: 1px solid #e2e8f0 !important;
            color: #64748b !important;
        }

        html:not(.dark) .adminit-page-link,
        html:not(.dark) .adminit-page-current,
        html:not(.dark) .inventory-page-link {
            background: #ffffff !important;
            border-color: #e2e8f0 !important;
            color: #64748b !important;
        }

        html:not(.dark) table.dataTable thead th {
            background: #f8fafc !important;
            border-color: #e2e8f0 !important;
            color: #64748b !important;
        }

        html:not(.dark) table.dataTable tbody td {
            color: #334155 !important;
            border-color: #f1f5f9 !important;
        }

        html:not(.dark) .dataTables_wrapper table.dataTable tbody tr,
        html:not(.dark) .dataTables_wrapper table.dataTable tbody tr:nth-child(odd),
        html:not(.dark) .dataTables_wrapper table.dataTable tbody tr:nth-child(even),
        html:not(.dark) #specialTable.dataTable tbody tr:nth-child(odd),
        html:not(.dark) #specialTable.dataTable tbody tr:nth-child(even) {
            background: #ffffff !important;
        }

        html:not(.dark) .dataTables_wrapper table.dataTable tbody tr:hover,
        html:not(.dark) #specialTable.dataTable tbody tr:hover {
            background: #f8fafc !important;
        }

        html:not(.dark) table.dataTable tbody td.dataTables_empty,
        html:not(.dark) .dataTables_wrapper table.dataTable tbody td.dataTables_empty {
            background: #ffffff !important;
            color: #94a3b8 !important;
            border-color: #e2e8f0 !important;
            box-shadow: none !important;
            text-align: center !important;
        }

        html:not(.dark) .dataTables_wrapper .dataTables_filter input,
        html:not(.dark) .dataTables_wrapper .dataTables_length select,
        html:not(.dark) .search-input,
        html:not(.dark) .filter-select,
        html:not(.dark) .form-input,
        html:not(.dark) .select2-container .select2-selection--single {
            background: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #334155 !important;
        }

        html:not(.dark) .navy-chip,
        html:not(.dark) .compact-warning-badge,
        html:not(.dark) table.dataTable tbody td span[class*="bg-"] {
            background: #f1f5f9 !important;
            border-color: #e2e8f0 !important;
            color: #475569 !important;
        }

        html:not(.dark) .wt-btn-soft,
        html:not(.dark) .navy-btn-soft,
        html:not(.dark) .btn-cancel {
            background: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #334155 !important;
        }

        html.dark body {
            background: #0f172a !important;
            color: #cbd5e1 !important;
        }

        html.dark .content-surface {
            background: #0f172a !important;
            border-color: #334155 !important;
            box-shadow: none !important;
        }
        /* ===== System Compact Scale ===== */
        html {
            font-size: 14px;
        }

        body {
            font-size: 11px !important;
        }

        .sidebar-shell.w-56,
        #mobileSidebar {
            width: 12.5rem !important;
        }

        .sidebar-shell .p-4 {
            padding: 0.75rem !important;
        }

        .sidebar-shell nav {
            padding: 0.65rem 0.55rem !important;
        }

        .nav-link,
        .sidebar-link,
        .dropdown-trigger.sidebar-link,
        .sub-nav-link {
            min-height: 28px !important;
            padding: 0.35rem 0.65rem !important;
            gap: 0.5rem !important;
            font-size: 9px !important;
            line-height: 1.2 !important;
            border-radius: 4px !important;
        }

        .sub-nav-link {
            padding-left: 1.65rem !important;
        }

        .sidebar-shell h2 {
            font-size: 10px !important;
            letter-spacing: 0.18em !important;
        }

        .sidebar-shell p {
            font-size: 8px !important;
            letter-spacing: 0.14em !important;
        }

        .topbar-shell {
            min-height: 46px !important;
            padding: 0.45rem 1rem !important;
        }

        .page-chip {
            padding: 0.25rem 0.7rem !important;
            font-size: 8px !important;
            letter-spacing: 0.12em !important;
        }

        main {
            padding: 0.75rem !important;
        }

        main > nav {
            margin-bottom: 0.5rem !important;
            font-size: 8px !important;
        }

        main > .content-surface,
        .content-surface {
            padding: 0.85rem !important;
            border-radius: 8px !important;
            box-shadow: none !important;
        }

        .page-header-block,
        .mb-6,
        .mb-8 {
            margin-bottom: 0.85rem !important;
        }

        .page-title-standard,
        .content-surface h1,
        .content-surface h2,
        .content-surface h3 {
            font-size: 14px !important;
            line-height: 1.2 !important;
        }

        .content-surface h4,
        .modal-title {
            font-size: 11px !important;
            line-height: 1.25 !important;
        }

        .page-subtitle-standard,
        .content-surface label,
        .modal-form-label {
            font-size: 9px !important;
            letter-spacing: 0.08em !important;
        }

        .content-surface .p-8,
        .content-surface .p-6,
        .content-surface .p-5,
        .content-surface .p-4,
        .modal-body,
        .modal-header,
        .modal-footer {
            padding: 0.75rem !important;
        }

        .content-surface .gap-6,
        .content-surface .gap-5,
        .content-surface .gap-4,
        .modal-form-grid {
            gap: 0.65rem !important;
        }

        .content-surface input,
        .content-surface select,
        .content-surface textarea,
        .modal-form-input {
            min-height: 30px !important;
            padding: 0.4rem 0.55rem !important;
            border-radius: 6px !important;
            font-size: 10px !important;
            line-height: 1.25 !important;
        }

        .content-surface textarea {
            min-height: 64px !important;
        }

        .wt-btn,
        .navy-btn,
        .navy-btn-danger,
        .navy-btn-soft,
        .btn-cancel,
        .btn-submit,
        button.dt-button,
        .dataTables_wrapper .dt-button,
        .content-surface button,
        .content-surface a[class*="px-"] {
            min-height: 24px !important;
            padding: 0.35rem 0.6rem !important;
            border-radius: 5px !important;
            font-size: 8px !important;
            line-height: 1 !important;
            letter-spacing: 0.06em !important;
            gap: 0.35rem !important;
        }

        .dataTables_wrapper,
        .dataTables_wrapper label,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            font-size: 9px !important;
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            padding: 0.45rem 0.55rem !important;
            margin: 0 !important;
        }

        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select {
            min-height: 28px !important;
            padding: 0.35rem 0.55rem !important;
            border-radius: 5px !important;
            font-size: 9px !important;
        }

        table.dataTable thead th,
        .dataTables_wrapper table.dataTable thead th {
            padding: 0.45rem 0.5rem !important;
            font-size: 8px !important;
            line-height: 1.2 !important;
            letter-spacing: 0.08em !important;
        }

        table.dataTable tbody td,
        .dataTables_wrapper table.dataTable tbody td {
            padding: 0.45rem 0.5rem !important;
            font-size: 9px !important;
            line-height: 1.3 !important;
        }

        .adminit-table-footer {
            padding: 0.6rem 0.75rem !important;
            gap: 0.75rem !important;
        }

        .adminit-table-info,
        .adminit-page-link,
        .adminit-page-current {
            font-size: 9px !important;
        }

        .adminit-page-link,
        .adminit-page-current {
            min-width: 42px !important;
            height: 34px !important;
        }

        .select2-container--default .select2-selection--single,
        .select2-container--default .select2-selection--multiple {
            min-height: 30px !important;
            border-radius: 6px !important;
        }

        .select2-container--default .select2-selection__rendered,
        .select2-results__option {
            font-size: 10px !important;
        }

        @media (max-width: 768px) {
            main {
                padding: 0.55rem !important;
            }

            main > .content-surface,
            .content-surface {
                padding: 0.65rem !important;
                border-radius: 8px !important;
            }

            .topbar-shell {
                padding: 0.45rem 0.65rem !important;
            }
        }

        /* Notification dropdown must stay readable in both themes */
        #notificationDropdown {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            box-shadow: 0 18px 42px rgba(15, 23, 42, 0.16) !important;
            color: #334155 !important;
        }

        #notificationDropdown > div:first-child {
            background: #f8fafc !important;
            border-bottom: 1px solid #e2e8f0 !important;
        }

        #notificationDropdown > div:first-child p {
            color: #475569 !important;
        }

        #notificationDropdown .max-h-\[360px\] > div {
            background: #ffffff !important;
            border-bottom: 1px solid #e2e8f0 !important;
        }

        #notificationDropdown .max-h-\[360px\] > div:hover {
            background: #f8fafc !important;
        }

        #notificationDropdown p {
            color: #334155 !important;
        }

        #notificationDropdown p.text-slate-500,
        #notificationDropdown p.text-slate-400 {
            color: #64748b !important;
        }

        #notificationDropdown button {
            color: #0284c7 !important;
            background: transparent !important;
            min-height: auto !important;
            padding: 0 !important;
            border: 0 !important;
            box-shadow: none !important;
        }

        #notificationDropdown .notification-item-btn {
            width: 100% !important;
            display: flex !important;
            padding: 0.75rem 1rem !important;
            color: inherit !important;
        }

        #notificationDropdown .notification-item-action {
            color: #0284c7 !important;
        }

        .dark #notificationDropdown {
            background: #0f172a !important;
            border-color: #334155 !important;
            box-shadow: 0 18px 42px rgba(0, 0, 0, 0.45) !important;
            color: #e2e8f0 !important;
        }

        .dark #notificationDropdown > div:first-child {
            background: #111827 !important;
            border-bottom-color: #334155 !important;
        }

        .dark #notificationDropdown > div:first-child p,
        .dark #notificationDropdown p {
            color: #e2e8f0 !important;
        }

        .dark #notificationDropdown .max-h-\[360px\] > div {
            background: #0f172a !important;
            border-bottom-color: #334155 !important;
        }

        .dark #notificationDropdown .max-h-\[360px\] > div:hover {
            background: #172033 !important;
        }

        .dark #notificationDropdown p.text-slate-500,
        .dark #notificationDropdown p.text-slate-400 {
            color: #94a3b8 !important;
        }

        .dark #notificationDropdown button {
            color: #38bdf8 !important;
        }

    </style>
    @stack('styles')
    <style>
        /* Critical theme paint lock: prevent dark inventory styles flashing before CSS asset loads. */
        html:not(.dark) body,
        html:not(.dark) main {
            background: #eef3f8 !important;
            color: #334155 !important;
            color-scheme: light;
        }

        html.dark body,
        html.dark main,
        .dark body,
        .dark main {
            background: #0f172a !important;
            color: #e2e8f0 !important;
            color-scheme: dark;
        }

        html:not(.dark) body .content-surface,
        html:not(.dark) body .content-surface:has(.inventory-page-shell) {
            background: transparent !important;
            color: #334155 !important;
        }

        html:not(.dark) body .content-surface .inventory-page-header {
            background: #ffffff !important;
            border: 1px solid #d8e1ed !important;
            border-left: 7px solid #c28a48 !important;
            color: #172033 !important;
        }

        html:not(.dark) body .content-surface .inventory-page-header .page-title-standard,
        html:not(.dark) body .content-surface .page-title-standard {
            color: #172033 !important;
        }

        html:not(.dark) body .content-surface .inventory-page-header .page-subtitle-standard,
        html:not(.dark) body .content-surface .page-subtitle-standard,
        html:not(.dark) body .content-surface .clean-admin-label,
        html:not(.dark) body .content-surface .inventory-table-info {
            color: #64748b !important;
        }

        html:not(.dark) body .content-surface .inventory-summary-card,
        html:not(.dark) body .content-surface .clean-admin-filter,
        html:not(.dark) body .content-surface .inventory-bulk-bar,
        html:not(.dark) body .content-surface #mainTableContainer.inventory-table-shell,
        html:not(.dark) body .content-surface .clean-admin-table-shell {
            background: #ffffff !important;
            border-color: #d8e1ed !important;
            color: #172033 !important;
            box-shadow: none !important;
        }

        html:not(.dark) body .content-surface .inventory-summary-label {
            color: #64748b !important;
        }

        html:not(.dark) body .content-surface .inventory-summary-value {
            color: #172033 !important;
        }

        html:not(.dark) body .content-surface .clean-admin-input,
        html:not(.dark) body .content-surface .clean-admin-select,
        html:not(.dark) body .content-surface .clean-admin-reset,
        html:not(.dark) body .content-surface .inventory-bulk-select,
        html:not(.dark) body .content-surface .inventory-bulk-input,
        html:not(.dark) body .content-surface .inventory-bulk-count,
        html:not(.dark) body .content-surface input,
        html:not(.dark) body .content-surface select,
        html:not(.dark) body .content-surface textarea {
            background: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #172033 !important;
        }

        html:not(.dark) body .content-surface .clean-admin-input::placeholder,
        html:not(.dark) body .content-surface .inventory-bulk-input::placeholder {
            color: #94a3b8 !important;
        }

        html:not(.dark) body .content-surface #walkiesTable thead th,
        html:not(.dark) body .content-surface .clean-admin-table thead th {
            background: #f8fafc !important;
            border-color: #d8e1ed !important;
            color: #475569 !important;
        }

        html:not(.dark) body .content-surface #walkiesTable tbody td,
        html:not(.dark) body .content-surface .clean-admin-table tbody td {
            background: #ffffff !important;
            border-color: #e2e8f0 !important;
            color: #1f2937 !important;
        }

        html:not(.dark) body .content-surface .clean-admin-pill,
        html:not(.dark) body .content-surface .inventory-id-chip,
        html:not(.dark) body .content-surface .inventory-type-badge,
        html:not(.dark) body .content-surface .inventory-status-badge,
        html:not(.dark) body .content-surface .inventory-meta-pill {
            background: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
            color: #1f2937 !important;
        }

        html:not(.dark) body .content-surface .inventory-bulk-checkbox {
            background: #ffffff !important;
            border: 1px solid #94a3b8 !important;
        }

        html:not(.dark) body .content-surface .inventory-summary-grid,
        html:not(.dark) body .content-surface .inventory-record-pill,
        html.dark body .content-surface .inventory-summary-grid,
        html.dark body .content-surface .inventory-record-pill,
        .dark body .content-surface .inventory-summary-grid,
        .dark body .content-surface .inventory-record-pill {
            display: none !important;
        }

        html:not(.dark) body .content-surface:has(.inventory-page-shell),
        html.dark body .content-surface:has(.inventory-page-shell),
        .dark body .content-surface:has(.inventory-page-shell) {
            padding: 14px !important;
            border: 0 !important;
            border-radius: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
        }

        html:not(.dark) body .content-surface .inventory-page-header,
        html.dark body .content-surface .inventory-page-header,
        .dark body .content-surface .inventory-page-header {
            min-height: 0 !important;
            padding: 0 2px 10px !important;
            border: 0 !important;
            border-radius: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
        }

        html:not(.dark) body .content-surface .inventory-bulk-bar {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            margin: 8px 0 12px !important;
            padding: 8px 10px !important;
            border: 1px solid #d8e1ed !important;
            border-radius: 10px !important;
            background: #ffffff !important;
            background-color: #ffffff !important;
            background-image: none !important;
            box-shadow: 0 1px 2px rgba(15, 23, 42, .04) !important;
            color: #172033 !important;
        }

        html.dark body .content-surface .inventory-bulk-bar,
        .dark body .content-surface .inventory-bulk-bar {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            margin: 8px 0 12px !important;
            padding: 8px 10px !important;
            border: 1px solid rgba(148, 163, 184, .18) !important;
            border-radius: 10px !important;
            background: #0f172a !important;
            box-shadow: none !important;
            color: #dbeafe !important;
        }

        html:not(.dark) body .content-surface .inventory-bulk-count,
        html:not(.dark) body .content-surface .inventory-bulk-select,
        html:not(.dark) body .content-surface .inventory-bulk-input,
        html:not(.dark) body .content-surface .inventory-bulk-btn {
            height: 28px !important;
            min-height: 28px !important;
            border-radius: 7px !important;
            font-size: 9px !important;
            box-shadow: none !important;
        }

        html.dark body .content-surface .inventory-bulk-count,
        html.dark body .content-surface .inventory-bulk-select,
        html.dark body .content-surface .inventory-bulk-input,
        html.dark body .content-surface .inventory-bulk-btn,
        .dark body .content-surface .inventory-bulk-count,
        .dark body .content-surface .inventory-bulk-select,
        .dark body .content-surface .inventory-bulk-input,
        .dark body .content-surface .inventory-bulk-btn {
            height: 28px !important;
            min-height: 28px !important;
            border-radius: 7px !important;
            font-size: 9px !important;
            box-shadow: none !important;
        }

        html:not(.dark) body .content-surface .inventory-bulk-btn {
            background: #f8fafc !important;
            border-color: #cbd5e1 !important;
            color: #475569 !important;
        }

        html:not(.dark) body .content-surface .inventory-bulk-btn:not(:disabled) {
            background: #0f6f98 !important;
            border-color: #0f6f98 !important;
            color: #ffffff !important;
        }

        html:not(.dark) body .content-surface #inventoryTableScroll.clean-admin-table-scroll,
        html.dark body .content-surface #inventoryTableScroll.clean-admin-table-scroll,
        .dark body .content-surface #inventoryTableScroll.clean-admin-table-scroll {
            display: block !important;
            max-width: 100% !important;
            overflow-x: auto !important;
            overflow-y: visible !important;
        }

        html:not(.dark) body .content-surface #mainTableContainer.inventory-table-shell,
        html.dark body .content-surface #mainTableContainer.inventory-table-shell,
        .dark body .content-surface #mainTableContainer.inventory-table-shell {
            margin-top: 0 !important;
        }

        html:not(.dark) body .content-surface .clean-admin-filter,
        html.dark body .content-surface .clean-admin-filter,
        .dark body .content-surface .clean-admin-filter {
            min-height: 0 !important;
            margin: 0 0 2px !important;
            padding: 8px 10px !important;
            border-radius: 10px !important;
            box-shadow: none !important;
        }

        html:not(.dark) body .content-surface .clean-admin-filter-grid,
        html.dark body .content-surface .clean-admin-filter-grid,
        .dark body .content-surface .clean-admin-filter-grid {
            grid-template-columns: minmax(220px, 1fr) 190px 82px !important;
            gap: 8px !important;
            align-items: end !important;
        }

        html:not(.dark) body .content-surface .clean-admin-label,
        html.dark body .content-surface .clean-admin-label,
        .dark body .content-surface .clean-admin-label {
            margin-bottom: 4px !important;
            font-size: 8px !important;
            line-height: 1 !important;
            letter-spacing: .11em !important;
        }

        html:not(.dark) body .content-surface .clean-admin-input,
        html:not(.dark) body .content-surface .clean-admin-select,
        html:not(.dark) body .content-surface .clean-admin-reset,
        html.dark body .content-surface .clean-admin-input,
        html.dark body .content-surface .clean-admin-select,
        html.dark body .content-surface .clean-admin-reset,
        .dark body .content-surface .clean-admin-input,
        .dark body .content-surface .clean-admin-select,
        .dark body .content-surface .clean-admin-reset {
            height: 30px !important;
            min-height: 30px !important;
            border-radius: 8px !important;
            font-size: 10px !important;
            font-weight: 750 !important;
            box-shadow: none !important;
        }

        html:not(.dark) body .content-surface .clean-admin-input,
        html.dark body .content-surface .clean-admin-input,
        .dark body .content-surface .clean-admin-input {
            padding: 0 10px !important;
        }

        html:not(.dark) body .content-surface .clean-admin-select,
        html.dark body .content-surface .clean-admin-select,
        .dark body .content-surface .clean-admin-select {
            padding: 0 28px 0 10px !important;
        }

        html:not(.dark) body .content-surface .clean-admin-reset,
        html.dark body .content-surface .clean-admin-reset,
        .dark body .content-surface .clean-admin-reset {
            padding: 0 12px !important;
            width: auto !important;
            letter-spacing: .04em !important;
        }

        .content-surface .page-header-block {
            margin-bottom: 0.85rem !important;
            border: 0 !important;
            border-radius: 0 !important;
            background: transparent !important;
            padding: 0 !important;
            box-shadow: none !important;
        }

        .content-surface .page-title-standard {
            margin: 0 !important;
            color: #1f2937 !important;
            font-size: 16px !important;
            font-weight: 900 !important;
            line-height: 1.15 !important;
            letter-spacing: -0.01em !important;
        }

        .content-surface .page-subtitle-standard {
            margin-top: 0.45rem !important;
            color: #64748b !important;
            font-size: 9px !important;
            font-weight: 900 !important;
            line-height: 1.35 !important;
            letter-spacing: 0.18em !important;
            text-transform: uppercase !important;
        }

        .dark .content-surface .page-header-block {
            border: 0 !important;
            background: transparent !important;
        }

        .dark .content-surface .page-title-standard {
            color: #f8fafc !important;
        }

        .dark .content-surface .page-subtitle-standard {
            color: #94a3b8 !important;
        }

        @media (max-width: 768px) {
            .content-surface .page-header-block {
                padding: 0 !important;
            }
        }

        /* Shared Inventory Management table style */
        .content-surface .inventory-table-shell,
        .content-surface .clean-admin-table-shell,
        .content-surface .unused-table-shell,
        .content-surface .duplicate-table-shell,
        .content-surface .special-table-shell {
            overflow: hidden !important;
            border: 1px solid #334155 !important;
            border-radius: 8px !important;
            background: #111827 !important;
            box-shadow: none !important;
            padding: 0 !important;
        }

        .content-surface .clean-admin-table-scroll,
        .content-surface .unused-scroll,
        .content-surface .duplicate-table-shell,
        .content-surface .special-table-shell {
            overflow-x: auto !important;
        }

        .content-surface .inventory-table-shell table,
        .content-surface .clean-admin-table-shell table,
        .content-surface .unused-table-shell table,
        .content-surface .duplicate-table-shell table.dataTable,
        .content-surface .special-table-shell table.dataTable {
            width: 100% !important;
            margin: 0 !important;
            border: 0 !important;
            border-collapse: collapse !important;
            background: transparent !important;
        }

        .content-surface .inventory-table-shell thead th,
        .content-surface .clean-admin-table-shell thead th,
        .content-surface .unused-table-shell thead th,
        .content-surface .duplicate-table-shell table.dataTable thead th,
        .content-surface .special-table-shell table.dataTable thead th {
            height: 34px !important;
            padding: 8px 12px !important;
            border: 1px solid #263244 !important;
            background: #1e293b !important;
            color: #cbd5e1 !important;
            font-size: 10px !important;
            font-weight: 900 !important;
            line-height: 1.2 !important;
            letter-spacing: 0.08em !important;
            text-align: left !important;
            text-transform: uppercase !important;
            white-space: nowrap !important;
        }

        .content-surface .inventory-table-shell tbody td,
        .content-surface .clean-admin-table-shell tbody td,
        .content-surface .unused-table-shell tbody td,
        .content-surface .duplicate-table-shell table.dataTable tbody td,
        .content-surface .special-table-shell table.dataTable tbody td {
            min-height: 38px !important;
            padding: 9px 12px !important;
            border: 1px solid #263244 !important;
            background: #111827 !important;
            color: #dbe4f0 !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            line-height: 1.35 !important;
            vertical-align: middle !important;
            white-space: nowrap !important;
        }

        .content-surface .unused-table-shell tbody td.wrap-cell {
            min-width: 220px !important;
            max-width: 320px !important;
            white-space: normal !important;
        }

        .content-surface .inventory-table-shell tbody tr:hover td,
        .content-surface .clean-admin-table-shell tbody tr:hover td,
        .content-surface .unused-table-shell tbody tr:hover td,
        .content-surface .duplicate-table-shell table.dataTable tbody tr:hover td,
        .content-surface .special-table-shell table.dataTable tbody tr:hover td {
            background: #172033 !important;
        }

        .content-surface .duplicate-table-shell .dataTables_wrapper .dataTables_length,
        .content-surface .duplicate-table-shell .dataTables_wrapper .dataTables_filter,
        .content-surface .special-table-shell .dataTables_wrapper .dataTables_length,
        .content-surface .special-table-shell .dataTables_wrapper .dataTables_filter {
            padding: 12px !important;
            margin: 0 !important;
        }

        .content-surface .duplicate-table-shell .dataTables_wrapper .dataTables_info,
        .content-surface .duplicate-table-shell .dataTables_wrapper .dataTables_paginate,
        .content-surface .special-table-shell .dataTables_wrapper .dataTables_info,
        .content-surface .special-table-shell .dataTables_wrapper .dataTables_paginate {
            padding: 12px !important;
            margin: 0 !important;
        }

        .content-surface .clean-admin-table-shell .wt-btn,
        .content-surface .duplicate-table-shell .wt-btn,
        .content-surface .special-table-shell .wt-btn,
        .content-surface .unused-table-shell .unused-action-btn {
            min-height: 26px !important;
            border-radius: 6px !important;
            padding: 5px 9px !important;
            font-size: 9px !important;
            font-weight: 900 !important;
            letter-spacing: 0.04em !important;
            box-shadow: none !important;
            transform: none !important;
        }

        .content-surface .unused-table-shell .unused-action-btn {
            border-color: rgba(96, 165, 250, 0.32) !important;
            background: #0f172a !important;
            color: #e2e8f0 !important;
        }

        .content-surface .unused-table-shell .unused-action-btn.delete {
            border-color: #ef4444 !important;
            background: #7f1d1d !important;
            color: #fee2e2 !important;
        }

        .content-surface .unused-table-shell .unused-action-btn:hover {
            border-color: #64748b !important;
            background: #172033 !important;
            color: #f8fafc !important;
        }

        .content-surface .unused-table-shell .unused-action-btn.delete:hover {
            border-color: #f87171 !important;
            background: #991b1b !important;
            color: #fee2e2 !important;
        }

        html:not(.dark) .content-surface .inventory-table-shell,
        html:not(.dark) .content-surface .clean-admin-table-shell,
        html:not(.dark) .content-surface .unused-table-shell,
        html:not(.dark) .content-surface .duplicate-table-shell,
        html:not(.dark) .content-surface .special-table-shell {
            border-color: #e2e8f0 !important;
            background: #ffffff !important;
        }

        html:not(.dark) .content-surface .inventory-table-shell thead th,
        html:not(.dark) .content-surface .clean-admin-table-shell thead th,
        html:not(.dark) .content-surface .unused-table-shell thead th,
        html:not(.dark) .content-surface .duplicate-table-shell table.dataTable thead th,
        html:not(.dark) .content-surface .special-table-shell table.dataTable thead th {
            border-color: #e2e8f0 !important;
            background: #f8fafc !important;
            color: #64748b !important;
        }

        html:not(.dark) .content-surface .inventory-table-shell tbody td,
        html:not(.dark) .content-surface .clean-admin-table-shell tbody td,
        html:not(.dark) .content-surface .unused-table-shell tbody td,
        html:not(.dark) .content-surface .duplicate-table-shell table.dataTable tbody td,
        html:not(.dark) .content-surface .special-table-shell table.dataTable tbody td {
            border-color: #eef2f7 !important;
            background: #ffffff !important;
            color: #334155 !important;
        }

        html:not(.dark) .content-surface .inventory-table-shell tbody tr:hover td,
        html:not(.dark) .content-surface .clean-admin-table-shell tbody tr:hover td,
        html:not(.dark) .content-surface .unused-table-shell tbody tr:hover td,
        html:not(.dark) .content-surface .duplicate-table-shell table.dataTable tbody tr:hover td,
        html:not(.dark) .content-surface .special-table-shell table.dataTable tbody tr:hover td {
            background: #f8fafc !important;
        }

        html:not(.dark) .content-surface .unused-table-shell .unused-action-btn {
            border-color: #cbd5e1 !important;
            background: #ffffff !important;
            color: #334155 !important;
        }

        html:not(.dark) .content-surface .unused-table-shell .unused-action-btn:hover {
            border-color: #94a3b8 !important;
            background: #f8fafc !important;
            color: #0f172a !important;
        }

        html:not(.dark) .content-surface .unused-table-shell .unused-action-btn.delete {
            border-color: #fecaca !important;
            background: #fef2f2 !important;
            color: #991b1b !important;
        }

        html:not(.dark) .content-surface .unused-table-shell .unused-action-btn.delete:hover {
            border-color: #fca5a5 !important;
            background: #fee2e2 !important;
            color: #7f1d1d !important;
        }

        /* Final form dark-mode pass: keep the form layout identical to light mode,
           blur the backdrop fully, and keep form sheets centered after page CSS loads. */
        .approval-action-row {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-wrap: wrap !important;
            gap: 0.45rem !important;
        }

        .approval-action-row form {
            margin: 0 !important;
        }

        .approval-action-btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 0.35rem !important;
            min-height: 28px !important;
            min-width: 74px !important;
            padding: 0.42rem 0.68rem !important;
            border-radius: 6px !important;
            border: 1px solid #cbd5e1 !important;
            background: #ffffff !important;
            color: #334155 !important;
            box-shadow: none !important;
            font-size: 9px !important;
            font-weight: 900 !important;
            line-height: 1 !important;
            letter-spacing: 0.06em !important;
            text-transform: uppercase !important;
            white-space: nowrap !important;
            cursor: pointer !important;
            transition: background-color 0.16s ease, border-color 0.16s ease, color 0.16s ease !important;
        }

        .approval-action-btn:hover {
            background: #f8fafc !important;
            border-color: #94a3b8 !important;
            color: #0f172a !important;
            transform: none !important;
        }

        .approval-action-btn.approval-action-approve {
            border-color: #86efac !important;
            background: #f0fdf4 !important;
            color: #166534 !important;
        }

        .approval-action-btn.approval-action-approve:hover {
            border-color: #4ade80 !important;
            background: #dcfce7 !important;
            color: #14532d !important;
        }

        .approval-action-btn.approval-action-reject {
            border-color: #fecaca !important;
            background: #fef2f2 !important;
            color: #991b1b !important;
        }

        .approval-action-btn.approval-action-reject:hover {
            border-color: #fca5a5 !important;
            background: #fee2e2 !important;
            color: #7f1d1d !important;
        }

        .approval-action-btn.approval-action-view {
            border-color: #bfdbfe !important;
            background: #eff6ff !important;
            color: #1d4ed8 !important;
        }

        .approval-action-btn.approval-action-view:hover {
            border-color: #93c5fd !important;
            background: #dbeafe !important;
            color: #1e40af !important;
        }

        html.dark .approval-action-btn {
            border-color: #475569 !important;
            background: #0f172a !important;
            color: #e2e8f0 !important;
        }

        html.dark .approval-action-btn:hover {
            background: #172033 !important;
            border-color: #64748b !important;
            color: #f8fafc !important;
        }

        html.dark .approval-action-btn.approval-action-approve {
            border-color: #22c55e !important;
            background: rgba(5, 46, 22, 0.88) !important;
            color: #bbf7d0 !important;
        }

        html.dark .approval-action-btn.approval-action-approve:hover {
            border-color: #4ade80 !important;
            background: rgba(6, 78, 59, 0.92) !important;
            color: #dcfce7 !important;
        }

        html.dark .approval-action-btn.approval-action-reject {
            border-color: #ef4444 !important;
            background: rgba(69, 10, 10, 0.9) !important;
            color: #fecaca !important;
        }

        html.dark .approval-action-btn.approval-action-reject:hover {
            border-color: #f87171 !important;
            background: rgba(127, 29, 29, 0.92) !important;
            color: #fee2e2 !important;
        }

        html.dark .approval-action-btn.approval-action-view {
            border-color: #1e3a8a !important;
            background: #082f49 !important;
            color: #bfdbfe !important;
        }

        html.dark .approval-action-btn.approval-action-view:hover {
            border-color: #2563eb !important;
            background: #0c4a6e !important;
            color: #dbeafe !important;
        }

        .approval-modal-card {
            width: min(100%, 480px) !important;
            overflow: hidden !important;
            border-radius: 14px !important;
            border: 1px solid #e2e8f0 !important;
            background: #ffffff !important;
            box-shadow: 0 24px 54px rgba(15, 23, 42, 0.18) !important;
        }

        html.dark .approval-modal-card {
            border-color: #334155 !important;
            background: #0f172a !important;
            box-shadow: 0 24px 54px rgba(0, 0, 0, 0.42) !important;
        }

        #approveModal,
        #rejectRequestModal,
        #approveDamageModal,
        #rejectDamageModal {
            z-index: 2147483000 !important;
            width: 100vw !important;
            min-height: 100vh !important;
            min-height: 100dvh !important;
            align-items: center !important;
            justify-content: center !important;
            background: rgba(15, 23, 42, 0.48) !important;
            backdrop-filter: blur(10px) !important;
            -webkit-backdrop-filter: blur(10px) !important;
        }

        #approveModal .approval-modal-card,
        #rejectRequestModal .approval-modal-card,
        #approveDamageModal .approval-modal-card,
        #rejectDamageModal .approval-modal-card {
            margin: auto !important;
            color: #1e293b !important;
        }

        #approveModal .navy-panel {
            background: #ffffff !important;
            border-bottom: 1px solid #e2e8f0 !important;
        }

        #approveModal .navy-panel h3 {
            color: #1f2937 !important;
        }

        #approveModal .navy-panel p {
            color: #ffffff !important;
        }

        #approveModal .navy-panel button {
            display: none !important;
        }

        #approveModal form,
        #rejectRequestModal form,
        #approveDamageModal form,
        #rejectDamageModal form {
            background: #ffffff !important;
        }

        #approveModal label,
        #rejectRequestModal label,
        #approveDamageModal label,
        #rejectDamageModal label {
            color: #64748b !important;
        }

        #approveModal textarea,
        #rejectRequestModal textarea,
        #approveDamageModal textarea,
        #rejectDamageModal textarea {
            border-color: #dbe3ee !important;
            background: #f8fafc !important;
            color: #1f2937 !important;
        }

        #approveModal .dark\:border-slate-700,
        #approveModal .dark\:bg-slate-900 {
            border-color: #dbe3ee !important;
            background: #f8fafc !important;
        }

        #rejectRequestModal h3,
        #approveDamageModal h3,
        #rejectDamageModal h3,
        #rejectRequestModal span,
        #approveDamageModal span,
        #rejectDamageModal span {
            color: #1f2937 !important;
        }

        html.dark #approveModal,
        html.dark #rejectRequestModal,
        html.dark #approveDamageModal,
        html.dark #rejectDamageModal {
            background: rgba(2, 6, 23, 0.62) !important;
        }

        html.dark #approveModal .navy-panel {
            background: #0f172a !important;
            border-bottom-color: #334155 !important;
        }

        html.dark #approveModal .navy-panel h3 {
            color: #f8fafc !important;
        }

        html.dark #approveModal .navy-panel p {
            color: #94a3b8 !important;
        }

        html.dark #approveModal form,
        html.dark #rejectRequestModal form,
        html.dark #approveDamageModal form,
        html.dark #rejectDamageModal form {
            background: #0f172a !important;
        }

        html.dark #approveModal label,
        html.dark #rejectRequestModal label,
        html.dark #approveDamageModal label,
        html.dark #rejectDamageModal label {
            color: #cbd5e1 !important;
        }

        html.dark #approveModal textarea,
        html.dark #rejectRequestModal textarea,
        html.dark #approveDamageModal textarea,
        html.dark #rejectDamageModal textarea {
            border-color: #334155 !important;
            background: #111827 !important;
            color: #e2e8f0 !important;
        }

        html.dark #approveModal .dark\:border-slate-700,
        html.dark #approveModal .dark\:bg-slate-900 {
            border-color: #334155 !important;
            background: #111827 !important;
        }

        html.dark #approveModal .select2-container--default .select2-selection--single {
            border-color: #475569 !important;
            background: #111827 !important;
        }

        html.dark #approveModal .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #f8fafc !important;
        }

        html.dark .select2-container--open .select2-dropdown {
            border-color: #475569 !important;
            background: #0f172a !important;
        }

        html.dark .select2-container--default .select2-results__option {
            color: #e2e8f0 !important;
            background: #0f172a !important;
        }

        html.dark .select2-container--default .select2-results__option--highlighted[aria-selected] {
            color: #ffffff !important;
            background: #2563eb !important;
        }

        html.dark .select2-container--default .select2-search--dropdown .select2-search__field {
            border-color: #64748b !important;
            background: #020617 !important;
            color: #f8fafc !important;
        }

        html.dark #rejectRequestModal h3,
        html.dark #approveDamageModal h3,
        html.dark #rejectDamageModal h3,
        html.dark #rejectRequestModal span,
        html.dark #approveDamageModal span,
        html.dark #rejectDamageModal span {
            color: #f8fafc !important;
        }

        html.dark .content-surface:has(.admin-request-shell),
        html.dark .content-surface:has(.walkie-create-shell) {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            background: rgba(15, 23, 42, 0.72) !important;
            border-color: rgba(148, 163, 184, 0.28) !important;
            backdrop-filter: blur(100px) !important;
            -webkit-backdrop-filter: blur(100px) !important;
        }

        html.dark .admin-request-shell,
        html.dark .walkie-create-shell {
            width: min(100%, 900px) !important;
            margin-left: auto !important;
            margin-right: auto !important;
        }

        html.dark .admin-request-card,
        html.dark .walkie-create-shell {
            border-radius: 28px !important;
            box-shadow: 0 24px 54px rgba(2, 6, 23, 0.38) !important;
            backdrop-filter: blur(18px) !important;
            -webkit-backdrop-filter: blur(18px) !important;
        }

        html.dark .walkie-create-shell {
            overflow: hidden !important;
        }

        .modern-confirm-overlay {
            position: fixed;
            inset: 0;
            z-index: 2147483100;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(15, 23, 42, 0.52);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .modern-confirm-overlay.active {
            display: flex;
        }

        .modern-confirm-card {
            width: min(100%, 460px);
            overflow: hidden;
            border-radius: 18px;
            border: 1px solid rgba(148, 163, 184, 0.24);
            background: #ffffff;
            color: #1f2937;
            box-shadow: 0 28px 70px rgba(15, 23, 42, 0.28);
        }

        .modern-confirm-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 18px 20px;
            border-bottom: 1px solid #eef2f7;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        }

        .modern-confirm-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            border: 1px solid #bfdbfe;
            background: #eff6ff;
            color: #2563eb;
        }

        .modern-confirm-title {
            font-size: 13px;
            font-weight: 900;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .modern-confirm-subtitle {
            margin-top: 2px;
            color: #64748b;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .modern-confirm-close {
            margin-left: auto;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #64748b;
            cursor: pointer;
        }

        .modern-confirm-close:hover {
            background: #f8fafc;
            color: #0f172a;
        }

        .modern-confirm-body {
            padding: 20px;
        }

        .modern-confirm-message {
            color: #1f2937;
            font-size: 14px;
            font-weight: 800;
            line-height: 1.55;
        }

        .modern-confirm-label {
            display: block;
            margin: 18px 0 7px;
            color: #64748b;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }

        .modern-confirm-textarea {
            width: 100%;
            min-height: 92px;
            resize: vertical;
            border-radius: 10px;
            border: 1px solid #dbe3ef;
            background: #f8fafc;
            color: #1f2937;
            padding: 11px 12px;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.45;
            outline: none;
            text-transform: none !important;
        }

        .modern-confirm-textarea:focus {
            border-color: #93c5fd;
            box-shadow: 0 0 0 4px rgba(147, 197, 253, 0.24);
            background: #ffffff;
        }

        .modern-confirm-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 16px 20px 20px;
            background: #ffffff;
        }

        .modern-confirm-btn {
            min-height: 34px;
            border-radius: 9px;
            border: 1px solid #cbd5e1;
            padding: 0 16px;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            cursor: pointer;
            transition: 0.16s ease;
        }

        .modern-confirm-btn.cancel {
            background: #ffffff;
            color: #334155;
        }

        .modern-confirm-btn.cancel:hover {
            background: #f8fafc;
            border-color: #94a3b8;
        }

        .modern-confirm-btn.confirm {
            border-color: #2563eb;
            background: #2563eb;
            color: #ffffff;
            box-shadow: 0 12px 24px rgba(37, 99, 235, 0.2);
        }

        .modern-confirm-btn.confirm:hover {
            background: #1d4ed8;
            border-color: #1d4ed8;
        }

        html.dark .modern-confirm-card,
        html.dark .modern-confirm-footer {
            background: #0f172a;
            color: #e2e8f0;
        }

        html.dark .modern-confirm-card,
        html.dark .modern-confirm-header,
        html.dark .modern-confirm-footer {
            border-color: #334155;
        }

        html.dark .modern-confirm-header {
            background: linear-gradient(180deg, #111827 0%, #0f172a 100%);
        }

        html.dark .modern-confirm-message,
        html.dark .modern-confirm-title {
            color: #f8fafc;
        }

        html.dark .modern-confirm-subtitle,
        html.dark .modern-confirm-label {
            color: #94a3b8;
        }

        html.dark .modern-confirm-close,
        html.dark .modern-confirm-btn.cancel {
            border-color: #334155;
            background: #111827;
            color: #cbd5e1;
        }

        html.dark .modern-confirm-close:hover,
        html.dark .modern-confirm-btn.cancel:hover {
            background: #172033;
            color: #f8fafc;
        }

        html.dark .modern-confirm-textarea {
            border-color: #334155;
            background: #111827;
            color: #e2e8f0;
        }

        /* Compact admin typography pass. */
        body,
        .sidebar-shell,
        .topbar-shell,
        .content-surface {
            font-size: 10px !important;
        }

        .sidebar-shell h2,
        .sidebar-shell .text-\[11px\] {
            font-size: 10px !important;
            letter-spacing: 0.14em !important;
        }

        .sidebar-link,
        .dropdown-trigger.sidebar-link,
        .sub-nav-link {
            font-size: 10px !important;
            font-weight: 800 !important;
            line-height: 1.15 !important;
        }

        .sidebar-link i,
        .dropdown-trigger.sidebar-link i {
            font-size: 12px !important;
        }

        .page-chip {
            font-size: 10px !important;
            font-weight: 800 !important;
            line-height: 1 !important;
            letter-spacing: 0 !important;
        }

        .topbar-role-switcher > a,
        .topbar-role-switcher select,
        .topbar-signout-btn,
        .mobile-role-switcher a,
        .mobile-role-switcher select {
            font-size: 7px !important;
            font-weight: 900 !important;
            letter-spacing: 0.14em !important;
        }

        .topbar-action-btn i,
        .topbar-signout-btn i {
            font-size: 12px !important;
        }

        main nav[aria-label="Breadcrumb"],
        main nav[aria-label="Breadcrumb"] * {
            font-size: 9px !important;
            font-weight: 900 !important;
            letter-spacing: 0.08em !important;
        }

        .content-surface :is(h1, h2, h3, h4, .page-title-standard, .modal-title) {
            font-size: 15px !important;
            line-height: 1.1 !important;
        }

        .content-surface :is(.page-subtitle-standard, label, .text-xs, .text-sm) {
            font-size: 8px !important;
        }

        .topbar-shell {
            min-height: 66px !important;
            padding-top: 8px !important;
            padding-bottom: 8px !important;
        }

        .topbar-role-switcher {
            height: 44px !important;
            border-radius: 13px !important;
        }

        .topbar-role-switcher > a,
        .topbar-role-switcher select {
            height: 36px !important;
        }

        .topbar-action-btn {
            width: 54px !important;
            height: 54px !important;
            border-radius: 14px !important;
        }

        .topbar-signout-btn {
            height: 54px !important;
            min-width: 138px !important;
            border-radius: 14px !important;
        }

        main {
            padding: 16px 28px !important;
        }

        main nav[aria-label="Breadcrumb"] {
            margin-bottom: 14px !important;
        }

        main > .content-surface,
        .content-surface {
            padding: 18px !important;
            border-radius: 18px !important;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/css/wtsystem.css') }}?v={{ filemtime(public_path('assets/css/wtsystem.css')) }}">
    <style>
        /* Final topbar compact lock. */
        .topbar-shell {
            min-height: 44px !important;
            padding: 5px 16px !important;
        }

        .topbar-shell .page-chip {
            padding: 0 !important;
            border: 0 !important;
            background: transparent !important;
            font-size: 13px !important;
            font-weight: 800 !important;
            line-height: 1 !important;
            letter-spacing: 0 !important;
        }

        .topbar-role-switcher {
            height: 28px !important;
            padding: 1px !important;
            border-radius: 8px !important;
            background: #ffffff !important;
            border-color: #cbd5e1 !important;
        }

        html.dark .topbar-role-switcher,
        .dark .topbar-role-switcher {
            background: #0f172a !important;
            border-color: #263244 !important;
        }

        .topbar-role-switcher > a,
        .topbar-role-switcher select {
            height: 26px !important;
            min-height: 26px !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            border-radius: 7px !important;
            font-size: 6px !important;
            line-height: 1 !important;
            letter-spacing: 0.09em !important;
        }

        .topbar-role-switcher select {
            min-width: 126px !important;
            color: #334155 !important;
            background: transparent !important;
        }

        html.dark .topbar-role-switcher select,
        .dark .topbar-role-switcher select {
            color: #cbd5e1 !important;
        }

        .topbar-role-switcher > a.bg-\[\#B38A5A\],
        .topbar-role-switcher > a[class*="bg-[#B38A5A]"] {
            background: #0f6f98 !important;
            box-shadow: none !important;
        }

        .topbar-role-switcher > a:first-child {
            background: #0f6f98 !important;
            color: #ffffff !important;
        }

        .topbar-action-btn {
            width: 32px !important;
            height: 32px !important;
            min-width: 32px !important;
            min-height: 32px !important;
            border-radius: 8px !important;
            background: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #475569 !important;
        }

        html.dark .topbar-action-btn,
        .dark .topbar-action-btn {
            background: #0f172a !important;
            border-color: #263244 !important;
            color: #cbd5e1 !important;
        }

        html:not(.dark) .topbar-action-btn:hover {
            background: #f8fafc !important;
            border-color: #93c5fd !important;
            color: #075985 !important;
        }

        .topbar-action-btn i,
        .topbar-signout-btn i {
            font-size: 9px !important;
        }

        .topbar-signout-btn {
            width: 82px !important;
            min-width: 82px !important;
            height: 32px !important;
            min-height: 32px !important;
            padding: 0 8px !important;
            border-radius: 8px !important;
            background: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #475569 !important;
            font-size: 6px !important;
            letter-spacing: 0.1em !important;
            box-shadow: none !important;
        }

        html.dark .topbar-signout-btn,
        .dark .topbar-signout-btn {
            background: #0f172a !important;
            border-color: #263244 !important;
            color: #cbd5e1 !important;
        }

        html:not(.dark) .topbar-signout-btn:hover {
            background: #fef2f2 !important;
            border-color: #fca5a5 !important;
            color: #b91c1c !important;
        }

        .mobile-topbar-actions,
        .topbar-shell .flex.items-center.gap-2 {
            gap: 6px !important;
        }

        @media (max-width: 767px) {
            .topbar-shell {
                padding: 10px 14px 12px !important;
                background:
                    linear-gradient(180deg, rgba(15, 23, 42, .98), rgba(15, 23, 42, .94)) !important;
                border-bottom: 1px solid rgba(56, 189, 248, .22) !important;
                box-shadow: 0 10px 24px rgba(2, 6, 23, .16) !important;
            }

            .topbar-shell > .flex:first-child {
                align-items: center !important;
                gap: 10px !important;
            }

            #hamburgerBtn {
                width: 32px !important;
                height: 32px !important;
                min-width: 32px !important;
                border: 0 !important;
                border-radius: 999px !important;
                background: rgba(148, 163, 184, .12) !important;
                color: #dbeafe !important;
            }

            #hamburgerBtn i {
                color: #dbeafe !important;
                font-size: 13px !important;
            }

            .topbar-shell .page-chip {
                max-width: calc(100vw - 190px) !important;
                color: #f8fafc !important;
                font-size: 13px !important;
                font-weight: 900 !important;
                line-height: 1.15 !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
                white-space: nowrap !important;
            }

            .mobile-topbar-actions {
                align-items: center !important;
                align-self: center !important;
                gap: 7px !important;
            }

            .topbar-action-btn {
                width: 34px !important;
                height: 34px !important;
                min-width: 34px !important;
                min-height: 34px !important;
                border-radius: 12px !important;
                border: 1px solid rgba(148, 163, 184, .22) !important;
                background: rgba(15, 23, 42, .62) !important;
                color: #e2e8f0 !important;
            }

            .topbar-action-btn i {
                font-size: 11px !important;
            }

            .topbar-signout-btn {
                width: 42px !important;
                min-width: 42px !important;
                height: 34px !important;
                min-height: 34px !important;
                padding: 0 !important;
                border-radius: 12px !important;
                border: 1px solid rgba(148, 163, 184, .22) !important;
                background: rgba(15, 23, 42, .62) !important;
                color: #e2e8f0 !important;
            }

            .topbar-signout-btn .mobile-hide-label {
                display: none !important;
            }

            .topbar-signout-btn i {
                font-size: 11px !important;
            }

            .mobile-role-switcher {
                display: grid !important;
                grid-template-columns: 1fr 1fr !important;
                gap: 6px !important;
                margin-top: 10px !important;
                padding: 6px !important;
                border-radius: 14px !important;
                border: 1px solid rgba(148, 163, 184, .18) !important;
                background: rgba(30, 41, 59, .72) !important;
                box-shadow: inset 0 1px 0 rgba(255, 255, 255, .04) !important;
            }

            .mobile-role-switcher a {
                min-height: 34px !important;
                padding: 0 10px !important;
                border-radius: 10px !important;
                background: transparent !important;
                color: #94a3b8 !important;
                font-size: 7px !important;
                font-weight: 900 !important;
                letter-spacing: .14em !important;
                line-height: 1 !important;
            }

            .mobile-role-switcher a.bg-\[\#B38A5A\],
            .mobile-role-switcher a[class*="bg-[#B38A5A]"] {
                background: #0e7490 !important;
                color: #ffffff !important;
                box-shadow: 0 8px 18px rgba(14, 116, 144, .22) !important;
            }

            .mobile-role-switcher a i {
                font-size: 10px !important;
            }

            .mobile-executive-switch-form {
                grid-column: 1 / -1 !important;
            }

            .mobile-role-switcher select {
                height: 36px !important;
                min-height: 36px !important;
                border-radius: 10px !important;
                border: 1px solid rgba(148, 163, 184, .2) !important;
                background: #0b1220 !important;
                color: #cbd5e1 !important;
                font-size: 7px !important;
                font-weight: 900 !important;
                letter-spacing: .14em !important;
                box-shadow: none !important;
            }

            #notificationDropdown {
                right: -52px !important;
                width: min(330px, calc(100vw - 24px)) !important;
                max-width: calc(100vw - 24px) !important;
                border-radius: 14px !important;
            }
        }
    </style>
    @stack('final_styles')
</head>
<body class="overflow-hidden">
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
                ->orderBy('full_name')
                ->orderBy('username')
                ->get(['user_id', 'username', 'full_name', 'department'])
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

    {{-- ===== MOBILE SIDEBAR OVERLAY ===== --}}
    <div id="mobileSidebarOverlay" onclick="closeMobileSidebar()"></div>

    {{-- ===== MOBILE SIDEBAR ===== --}}
    <div id="mobileSidebar" class="sidebar-shell text-stone-300 flex flex-col shadow-2xl lg:hidden">
        <a href="{{ request()->fullUrl() }}" class="p-5 flex flex-row items-center gap-3 border-b border-white/10 transition hover:bg-white/5" title="Refresh page" aria-label="Refresh page">
            <div class="bg-white/5 p-3 rounded-2xl border border-white/10">
                <img src="{{ asset('assets/images/fjb-logo.svg') }}" alt="FJB" class="sidebar-brand-logo">
            </div>
            <h2 class="text-[11px] font-black text-white leading-tight tracking-[0.24em] uppercase">WT System</h2>
        </a>

        <nav class="flex-1 px-4 py-5 space-y-1.5 overflow-y-auto">
            @if($isAdminItView)
            <a href="{{ route('wt.admin.dashboard') }}" class="sidebar-link has-info flex items-center gap-3 py-3 px-4 rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'active-sidebar' : '' }}" onclick="closeMobileSidebar()">
                <i class="fas fa-home w-4 text-center"></i> <span>Dashboard</span>
                @include('wt.partials.sidebar-info', ['text' => 'Overview of system activity, requests, and inventory status.'])
            </a>
            @endif

            <div class="pt-5 mt-5 border-t border-white/10">
                @if($isAdminItView)
                <div class="dropdown-wrapper {{ $inventoryManagementOpen ? 'open' : '' }}">
                    <div class="dropdown-trigger sidebar-link has-info {{ $inventoryManagementOpen ? 'active-sidebar' : '' }}" onclick="toggleDropdown(this)">
                        <span class="flex items-center gap-3"><i class="fas fa-layer-group w-4 text-center"></i> <span>Inventory Tools</span></span>
                        @include('wt.partials.sidebar-info', ['text' => 'Open inventory management tools including inventory list, repair monitoring, duplicate IDs, and special use units.'])
                        <i class="fas fa-chevron-down dropdown-chevron"></i>
                    </div>
                    <div class="dropdown-content">
                        <a href="{{ route('wt.admin.walkies.index') }}" class="sub-nav-link {{ $inventoryNavOnInventory ? 'active' : '' }}" onclick="closeMobileSidebar()">
                            <span>Inventory List</span>
                        </a>
                        <a href="{{ route('wt.admin.maintenance.index') }}" class="sub-nav-link {{ $inventoryNavOnMaintenance ? 'active' : '' }}" onclick="closeMobileSidebar()">
                            <span>Under Repair / Faulty</span>
                        </a>
                        <a href="{{ route('wt.admin.walkies.duplicateIds') }}" class="sub-nav-link {{ $inventoryNavOnDuplicate ? 'active' : '' }}" onclick="closeMobileSidebar()">
                            <span>Duplicated ID</span>
                        </a>
                        <a href="{{ route('wt.admin.walkies.specialUse') }}" class="sub-nav-link {{ $inventoryNavOnSpecialUse ? 'active' : '' }}" onclick="closeMobileSidebar()">
                            <span>Special Use</span>
                        </a>
                    </div>
                </div>
                @endif

                @if($isAdminItView)
                    <div class="dropdown-wrapper {{ $approvalManagementOpen ? 'open' : '' }}">
                        <div class="dropdown-trigger sidebar-link has-info {{ $approvalManagementOpen ? 'active-sidebar' : '' }}" onclick="toggleDropdown(this)">
                            <span class="flex items-center gap-3">
                                <i class="fas fa-inbox w-4 text-center"></i>
                                <span class="inline-flex items-baseline gap-1">
                                    <span>Approvals</span>
                                    @if($approvalBadgeCount > 0)
                                    <span class="pending-nav-badge approval-new-badge" aria-label="New approvals">New</span>
                                    @endif
                                </span>
                            </span>
                            @include('wt.partials.sidebar-info', ['text' => 'Review approvals and open ICT approval history.'])
                            <i class="fas fa-chevron-down dropdown-chevron"></i>
                        </div>
                        <div class="dropdown-content">
                            <a href="{{ route('wt.admin.requests.index') }}" class="sub-nav-link {{ $approvalNavOnPending ? 'active' : '' }}" onclick="closeMobileSidebar()">
                                <span>Pending</span>
                                @if($approvalBadgeCount > 0)
                                <span class="pending-nav-badge" aria-label="{{ $approvalBadgeCount }} pending approvals">{{ $approvalBadgeCount > 9 ? '9+' : $approvalBadgeCount }}</span>
                                @endif
                            </a>
                        <a href="{{ route('wt.admin.requests.history') }}" class="sub-nav-link {{ $approvalNavOnHistory ? 'active' : '' }}" onclick="closeMobileSidebar()">
                            <span>History</span>
                        </a>
                    </div>
                </div>
                @endif

                @if($isAdminItView)
                <div class="dropdown-wrapper {{ $faultyManagementOpen ? 'open' : '' }}">
                    <div class="dropdown-trigger sidebar-link has-info {{ $faultyManagementOpen ? 'active-sidebar' : '' }}" onclick="toggleDropdown(this)">
                        <span class="flex items-center gap-3">
                            <i class="fa-solid fa-triangle-exclamation w-4 text-center"></i>
                            <span>Faulty Reports</span>
                        </span>
                        @include('wt.partials.sidebar-info', ['text' => 'Consolidated view for walkie requests, handovers, and faulty reports status.'])
                        <i class="fas fa-chevron-down dropdown-chevron"></i>
                    </div>
                    <div class="dropdown-content">
                        <a href="{{ route('wt.admin.faultyReports.index') }}" class="sub-nav-link {{ $faultyNavOnUserReports ? 'active' : '' }}" onclick="closeMobileSidebar()">
                            <span>User Reports</span>
                        </a>
                        <a href="{{ route('wt.admin.reports.faulty3Months') }}" class="sub-nav-link {{ $faultyNavOnThreeMonths ? 'active' : '' }}" onclick="closeMobileSidebar()">
                            <span>Monthly Report</span>
                        </a>
                    </div>
                </div>
                @endif
                
                @if(!$isAdminItView)
                <p class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-[0.18em] mb-2 mt-4">Personal Assets</p>
                <a href="{{ route('wt.admin.walkies.myInventory') }}" class="sidebar-link has-info flex items-center gap-3 py-3 px-4 rounded-xl transition {{ request()->routeIs('admin.walkies.myInventory') ? 'active-sidebar' : '' }}" onclick="closeMobileSidebar()">
                    <i class="fa-solid fa-box w-4 text-center"></i> <span>My Inventory</span>
                    @include('wt.partials.sidebar-info', ['text' => 'View walkie talkies currently assigned to you.'])
                </a>

                <p class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-[0.18em] mb-2 mt-4">Asset Interactive</p>
                <a href="{{ route('wt.admin.returns.create', ['mode' => 'self']) }}" class="sidebar-link has-info flex items-center gap-3 py-3 px-4 rounded-xl transition {{ request()->routeIs('admin.returns.*') ? 'active-sidebar' : '' }}" onclick="closeMobileSidebar()">
                    <i class="fa-solid fa-rotate-left w-4 text-center"></i> <span>Return Unit</span>
                    @include('wt.partials.sidebar-info', ['text' => 'Submit walkie return records for your own assigned unit.'])
                </a>

                <a href="{{ route('wt.admin.damages.form', ['mode' => 'staff']) }}" class="sidebar-link has-info flex items-center gap-3 py-3 px-4 rounded-xl transition {{ request()->routeIs('admin.damages.*') ? 'active-sidebar' : '' }}" onclick="closeMobileSidebar()">
                    <i class="fa-solid fa-triangle-exclamation w-4 text-center"></i> <span>Report Faulty</span>
                    @include('wt.partials.sidebar-info', ['text' => 'Report damaged, faulty, missing, or problem units for yourself or on behalf of a recipient.'])
                </a>

                <div class="dropdown-wrapper {{ request()->routeIs('admin.requests.create') || request()->routeIs('admin.requests.create.*') || request()->routeIs('admin.requests.create.temporary*') ? 'open' : '' }}">
                    <div class="dropdown-trigger sidebar-link has-info {{ request()->routeIs('admin.requests.create') || request()->routeIs('admin.requests.create.*') || request()->routeIs('admin.requests.create.temporary*') ? 'active-sidebar' : '' }}" onclick="toggleDropdown(this)">
                        <span class="flex items-center gap-3"><i class="fas fa-plus-circle w-4 text-center"></i> <span>Request Walkie Talkie</span></span>
                        @include('wt.partials.sidebar-info', ['text' => 'Submit a walkie talkie request for yourself or on behalf of a recipient. ICT will assign the available unit later.'])
                        <i class="fas fa-chevron-down dropdown-chevron"></i>
                    </div>
                    <div class="dropdown-content">
                        <a href="{{ route('wt.admin.requests.create') }}" class="sub-nav-link {{ request()->routeIs('admin.requests.create') || request()->routeIs('admin.requests.create.individual') || request()->routeIs('admin.requests.create.shared') ? 'active' : '' }}" onclick="closeMobileSidebar()">
                            <span>Long Term Request</span>
                        </a>
                        <a href="{{ route('wt.admin.requests.create.temporary') }}" class="sub-nav-link {{ request()->routeIs('admin.requests.create.temporary*') ? 'active' : '' }}" onclick="closeMobileSidebar()">
                            <span>Temporary Request</span>
                        </a>
                    </div>
                </div>
                <a href="{{ route('wt.admin.all.status') }}" class="sidebar-link has-info flex items-center gap-3 py-3 px-4 rounded-xl transition {{ request()->routeIs('admin.all.status') ? 'active-sidebar' : '' }}" onclick="closeMobileSidebar()">
                    <i class="fa-solid fa-list-check w-4 text-center"></i> <span>All Status</span>
                    @include('wt.partials.sidebar-info', ['text' => 'Consolidated view for walkie requests, handovers, and faulty reports status.'])
                </a>
                @endif

                </div>

            <div class="pt-5 mt-5 border-t border-white/10">
                <p class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-[0.18em] mb-2">My Account</p>
                <a href="{{ route('wt.admin.profile') }}" class="sidebar-link has-info flex items-center gap-3 py-3 px-4 rounded-xl transition {{ request()->routeIs('admin.profile') ? 'active-sidebar' : '' }}" onclick="closeMobileSidebar()">
                    <i class="fas fa-user-circle w-4 text-center"></i> <span>My Profile</span>
                    @include('wt.partials.sidebar-info', ['text' => 'View and update your account profile information.'])
                </a>
                @if(!$isAdminItView)
                <a href="javascript:void(0)" onclick="openPoliciesModal()" class="sidebar-link has-info flex items-center gap-3 py-3 px-4 rounded-xl transition">
                    <i class="fa-solid fa-file-contract w-4 text-center"></i> <span>Policies</span>
                    @include('wt.partials.sidebar-info', ['text' => 'Read the policies and rules for walkie talkie usage.'])
                </a>
                @endif
            </div>

            @if($isAdminItView)
            <div class="pt-5 mt-5 border-t border-white/10">
                <p class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-[0.18em] mb-2">Executive Tools (IT)</p>
                <div class="dropdown-wrapper {{ request()->routeIs('admin.users.index') || request()->routeIs('admin.activity.index') ? 'open' : '' }}">
                    <div class="dropdown-trigger sidebar-link has-info {{ request()->routeIs('admin.users.index') || request()->routeIs('admin.activity.index') ? 'active-sidebar' : '' }}" onclick="toggleDropdown(this)">
                        <span class="flex items-center gap-3"><i class="fas fa-sliders-h w-4 text-center"></i> <span>System Control</span></span>
                        @include('wt.partials.sidebar-info', ['text' => 'Open system management tools for user accounts and activity logs.'])
                        <i class="fas fa-chevron-down dropdown-chevron"></i>
                    </div>
                    <div class="dropdown-content">
                        <a href="{{ route('wt.admin.users.index') }}" class="sub-nav-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}" onclick="closeMobileSidebar()">
                            <span>Users Control</span>
                        </a>
                        <a href="{{ route('wt.admin.activity.index') }}" class="sub-nav-link {{ request()->routeIs('admin.activity.index') ? 'active' : '' }}" onclick="closeMobileSidebar()">
                            <span>System Logs</span>
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <div class="pt-5 mt-5 border-t border-white/10">
                <a href="{{ route('home') }}" class="sidebar-link flex items-center gap-3 py-3 px-4 rounded-xl transition" onclick="closeMobileSidebar()">
                    <i class="fas fa-th-large w-4 text-center"></i> <span>Back to Portal</span>
                </a>
            </div>
        </nav>

        <div class="p-4 border-t border-white/10">
            <div class="bg-white/5 p-3 rounded-2xl flex items-center gap-3 border border-white/10">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-[#B38A5A] to-[#8D6742] flex items-center justify-center font-bold text-[11px] text-white shadow-lg">
                    {{ strtoupper(substr(Auth::guard('wt')->user()->username ?? 'A', 0, 1)) }}
                </div>
                <div class="overflow-hidden">
                    <p class="!text-white text-[11px] font-bold truncate">{{ Auth::guard('wt')->user()->username ?? 'Executive' }}</p>
                    <p class="!text-slate-200 text-[9px] font-black uppercase tracking-[0.18em]">{{ $accountRoleLabel }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== MAIN LAYOUT ===== --}}
    <div class="h-screen flex">

        {{-- Desktop Sidebar --}}
        <div class="sidebar-shell w-56 text-stone-300 flex-col hidden lg:flex shrink-0 shadow-2xl">
            <a href="{{ request()->fullUrl() }}" class="p-5 flex flex-row items-center gap-3 border-b border-white/10 transition hover:bg-white/5" title="Refresh page" aria-label="Refresh page">
                <div class="bg-white/5 p-2 rounded-xl border border-white/10">
                    <img src="{{ asset('assets/images/fjb-logo.svg') }}" alt="FJB" class="sidebar-brand-logo">
                </div>
                <div>
                    <h2 class="text-[11px] font-black text-white leading-tight tracking-[0.24em] uppercase">WT System</h2>
                </div>
            </a>

            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                @if($isAdminItView)
                <a href="{{ route('wt.admin.dashboard') }}" class="sidebar-link has-info flex items-center gap-3 py-2 px-4 rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'active-sidebar' : '' }}">
                    <i class="fas fa-home w-4 text-center"></i> <span>Dashboard</span>
                    @include('wt.partials.sidebar-info', ['text' => 'Overview of system activity, requests, and inventory status.'])
                </a>
                @endif

                <div class="pt-2 mt-2">
                    @if($isAdminItView)
                    <div class="dropdown-wrapper {{ $inventoryManagementOpen ? 'open' : '' }}">
                        <div class="dropdown-trigger sidebar-link has-info {{ $inventoryManagementOpen ? 'active-sidebar' : '' }}" onclick="toggleDropdown(this)">
                            <span class="flex items-center gap-3"><i class="fas fa-layer-group w-4 text-center"></i> <span>Inventory Tools</span></span>
                            @include('wt.partials.sidebar-info', ['text' => 'Open inventory management tools including inventory list, repair monitoring, duplicate IDs, and special use units.'])
                            <i class="fas fa-chevron-down dropdown-chevron"></i>
                        </div>
                        <div class="dropdown-content">
                            <a href="{{ route('wt.admin.walkies.index') }}" class="sub-nav-link {{ $inventoryNavOnInventory ? 'active' : '' }}">
                                <span>Inventory List</span>
                            </a>
                            <a href="{{ route('wt.admin.maintenance.index') }}" class="sub-nav-link {{ $inventoryNavOnMaintenance ? 'active' : '' }}">
                                <span>Under Repair / Faulty</span>
                            </a>
                            <a href="{{ route('wt.admin.walkies.duplicateIds') }}" class="sub-nav-link {{ $inventoryNavOnDuplicate ? 'active' : '' }}">
                                <span>Duplicated ID</span>
                            </a>
                            <a href="{{ route('wt.admin.walkies.specialUse') }}" class="sub-nav-link {{ $inventoryNavOnSpecialUse ? 'active' : '' }}">
                                <span>Special Use</span>
                            </a>
                        </div>
                    </div>
                    @endif

                    @if($isAdminItView)
                    <div class="dropdown-wrapper {{ $approvalManagementOpen ? 'open' : '' }}">
                        <div class="dropdown-trigger sidebar-link has-info {{ $approvalManagementOpen ? 'active-sidebar' : '' }}" onclick="toggleDropdown(this)">
                            <span class="flex items-center gap-3">
                                <i class="fas fa-inbox w-4 text-center"></i>
                                <span class="inline-flex items-baseline gap-1">
                                    <span>Approvals</span>
                                    @if($approvalBadgeCount > 0)
                                    <span class="pending-nav-badge approval-new-badge" aria-label="New approvals">New</span>
                                    @endif
                                </span>
                            </span>
                            @include('wt.partials.sidebar-info', ['text' => 'Review approvals and open ICT approval history.'])
                            <i class="fas fa-chevron-down dropdown-chevron"></i>
                        </div>
                        <div class="dropdown-content">
                            <a href="{{ route('wt.admin.requests.index') }}" class="sub-nav-link {{ $approvalNavOnPending ? 'active' : '' }}">
                                <span>Pending</span>
                                @if($approvalBadgeCount > 0)
                                <span class="pending-nav-badge" aria-label="{{ $approvalBadgeCount }} pending approvals">{{ $approvalBadgeCount > 9 ? '9+' : $approvalBadgeCount }}</span>
                                @endif
                            </a>
                            <a href="{{ route('wt.admin.requests.history') }}" class="sub-nav-link {{ $approvalNavOnHistory ? 'active' : '' }}">
                                <span>History</span>
                            </a>
                        </div>
                    </div>
                    @endif

                    @if($isAdminItView)
                    <div class="dropdown-wrapper {{ $faultyManagementOpen ? 'open' : '' }}">
                        <div class="dropdown-trigger sidebar-link has-info {{ $faultyManagementOpen ? 'active-sidebar' : '' }}" onclick="toggleDropdown(this)">
                            <span class="flex items-center gap-3">
                                <i class="fa-solid fa-triangle-exclamation w-4 text-center"></i>
                                <span>Faulty Reports</span>
                            </span>
                            @include('wt.partials.sidebar-info', ['text' => 'Consolidated view for walkie requests, handovers, and faulty reports status.'])
                            <i class="fas fa-chevron-down dropdown-chevron"></i>
                        </div>
                        <div class="dropdown-content">
                            <a href="{{ route('wt.admin.faultyReports.index') }}" class="sub-nav-link {{ $faultyNavOnUserReports ? 'active' : '' }}">
                                <span>User Reports</span>
                            </a>
                            <a href="{{ route('wt.admin.reports.faulty3Months') }}" class="sub-nav-link {{ $faultyNavOnThreeMonths ? 'active' : '' }}">
                                <span>Monthly Report</span>
                            </a>
                        </div>
                    </div>
                    @endif
                    
                    @if(!$isAdminItView)
                    <p class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-[0.18em] mb-2 mt-4">Personal Assets</p>
                    <a href="{{ route('wt.admin.walkies.myInventory') }}" class="sidebar-link has-info flex items-center gap-3 py-3 px-4 rounded-xl transition {{ request()->routeIs('admin.walkies.myInventory') ? 'active-sidebar' : '' }}">
                        <i class="fa-solid fa-box w-4 text-center"></i> <span>My Inventory</span>
                        @include('wt.partials.sidebar-info', ['text' => 'View walkie talkies currently assigned to you.'])
                    </a>

                    <p class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-[0.18em] mb-2 mt-4">Asset Interactive</p>
                    <a href="{{ route('wt.admin.returns.create', ['mode' => 'self']) }}" class="sidebar-link has-info flex items-center gap-3 py-3 px-4 rounded-xl transition {{ request()->routeIs('admin.returns.*') ? 'active-sidebar' : '' }}">
                        <i class="fa-solid fa-rotate-left w-4 text-center"></i> <span>Return Unit</span>
                        @include('wt.partials.sidebar-info', ['text' => 'Submit walkie return records for your own assigned unit.'])
                    </a>
                    <a href="{{ route('wt.admin.damages.form', ['mode' => 'staff']) }}" class="sidebar-link has-info flex items-center gap-3 py-3 px-4 rounded-xl transition {{ request()->routeIs('admin.damages.*') ? 'active-sidebar' : '' }}">
                        <i class="fa-solid fa-triangle-exclamation w-4 text-center"></i> <span>Report Faulty</span>
                        @include('wt.partials.sidebar-info', ['text' => 'Report damaged, faulty, missing, or problem units for yourself or on behalf of a recipient.'])
                    </a>
                    <div class="dropdown-wrapper {{ request()->routeIs('admin.requests.create') || request()->routeIs('admin.requests.create.*') || request()->routeIs('admin.requests.create.temporary*') ? 'open' : '' }}">
                        <div class="dropdown-trigger sidebar-link has-info {{ request()->routeIs('admin.requests.create') || request()->routeIs('admin.requests.create.*') || request()->routeIs('admin.requests.create.temporary*') ? 'active-sidebar' : '' }}" onclick="toggleDropdown(this)">
                            <span class="flex items-center gap-3"><i class="fas fa-plus-circle w-4 text-center"></i> <span>Request Walkie Talkie</span></span>
                            @include('wt.partials.sidebar-info', ['text' => 'Submit a walkie talkie request for yourself or on behalf of a recipient. ICT will assign the available unit later.'])
                            <i class="fas fa-chevron-down dropdown-chevron"></i>
                        </div>
                        <div class="dropdown-content">
                            <a href="{{ route('wt.admin.requests.create') }}" class="sub-nav-link {{ request()->routeIs('admin.requests.create') || request()->routeIs('admin.requests.create.individual') || request()->routeIs('admin.requests.create.shared') ? 'active' : '' }}">
                                <span>Long Term Request</span>
                            </a>
                            <a href="{{ route('wt.admin.requests.create.temporary') }}" class="sub-nav-link {{ request()->routeIs('admin.requests.create.temporary*') ? 'active' : '' }}">
                                <span>Temporary Request</span>
                            </a>
                        </div>
                    </div>
                    <a href="{{ route('wt.admin.all.status') }}" class="sidebar-link has-info flex items-center gap-3 py-3 px-4 rounded-xl transition {{ request()->routeIs('admin.all.status') ? 'active-sidebar' : '' }}">
                        <i class="fa-solid fa-list-check w-4 text-center"></i> <span>All Status</span>
                        @include('wt.partials.sidebar-info', ['text' => 'Consolidated view for walkie requests, handovers, and faulty reports status.'])
                    </a>
                    @endif


                </div>

                <div class="pt-5 mt-5 border-t border-white/10">
                    <p class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-[0.18em] mb-2">My Account</p>
                    <a href="{{ route('wt.admin.profile') }}" class="sidebar-link has-info flex items-center gap-3 py-3 px-4 rounded-xl transition {{ request()->routeIs('admin.profile') ? 'active-sidebar' : '' }}">
                        <i class="fas fa-user-circle w-4 text-center"></i> <span>My Profile</span>
                        @include('wt.partials.sidebar-info', ['text' => 'View and update your account profile information.'])
                    </a>
                    @if(!$isAdminItView)
                    <a href="javascript:void(0)" onclick="openPoliciesModal()" class="sidebar-link has-info flex items-center gap-3 py-3 px-4 rounded-xl transition">
                        <i class="fa-solid fa-file-contract w-4 text-center"></i> <span>Policies</span>
                        @include('wt.partials.sidebar-info', ['text' => 'Read the policies and rules for walkie talkie usage.'])
                    </a>
                    @endif
                </div>

                @if($isAdminItView)
                <div class="pt-5 mt-5 border-t border-white/10">
                    <p class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-[0.18em] mb-2">Executive Tools (IT)</p>
                    <div class="dropdown-wrapper {{ request()->routeIs('admin.users.index') || request()->routeIs('admin.activity.index') ? 'open' : '' }}">
                        <div class="dropdown-trigger sidebar-link has-info {{ request()->routeIs('admin.users.index') || request()->routeIs('admin.activity.index') ? 'active-sidebar' : '' }}" onclick="toggleDropdown(this)">
                            <span class="flex items-center gap-3"><i class="fas fa-sliders-h w-4 text-center"></i> <span>System Control</span></span>
                            @include('wt.partials.sidebar-info', ['text' => 'Open system management tools for user accounts and activity logs.'])
                            <i class="fas fa-chevron-down dropdown-chevron"></i>
                        </div>
                        <div class="dropdown-content">
                            <a href="{{ route('wt.admin.users.index') }}" class="sub-nav-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
                                <span>Users Control</span>
                            </a>
                            <a href="{{ route('wt.admin.activity.index') }}" class="sub-nav-link {{ request()->routeIs('admin.activity.index') ? 'active' : '' }}">
                                <span>System Logs</span>
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                <div class="pt-5 mt-5 border-t border-white/10">
                    <a href="{{ route('home') }}" class="sidebar-link flex items-center gap-3 py-2 px-4 rounded-xl transition">
                        <i class="fas fa-th-large w-4 text-center"></i> <span>Back to Portal</span>
                    </a>
                </div>
            </nav>

            <div class="p-4 border-t border-white/10">
                <div class="bg-white/5 p-3 rounded-2xl flex items-center gap-3 border border-white/10">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-[#B38A5A] to-[#8D6742] flex items-center justify-center font-bold text-[11px] text-white">
                        {{ strtoupper(substr(Auth::guard('wt')->user()->username ?? 'A', 0, 1)) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="!text-white text-[11px] font-bold truncate">{{ Auth::guard('wt')->user()->username ?? 'Executive' }}</p>
                        <p class="!text-slate-200 text-[9px] font-black uppercase tracking-[0.18em]">{{ $accountRoleLabel }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content area --}}
        <div class="flex-1 flex flex-col h-screen overflow-hidden">
            <header class="topbar-shell px-4 md:px-8 py-2 md:py-3 sticky top-0 z-10 shrink-0">
                <div class="flex items-start justify-between gap-3 md:items-center">
                <div class="flex items-center gap-3 min-w-0">
                    {{-- Hamburger menu button — only on mobile --}}
                    <button id="hamburgerBtn" onclick="openMobileSidebar()" class="lg:hidden" title="Open navigation menu" aria-label="Open navigation menu">
                        <i class="fas fa-bars text-slate-600 text-sm"></i>
                    </button>
                    <div class="min-w-0">
                    <div class="page-chip inline-flex items-center rounded-full px-3 py-1">@yield('title', 'Executive Dashboard')</div>
                    </div>
                </div>
                <div class="flex items-center gap-2 mobile-topbar-actions">
                    {{-- Interface Switcher (ICT Only) --}}
                    
                    
                    @if($actualRole === 'admin_it')
                    <div class="topbar-role-switcher hidden md:flex items-center bg-stone-100 dark:bg-slate-800/50 border border-stone-200 dark:border-slate-700">
                        <a href="{{ route('wt.switch_view', 'admin_it') }}" class="flex items-center gap-2 px-3 {{ $effectiveRole === 'admin_it' ? 'bg-[#B38A5A] text-white' : 'text-stone-500 hover:text-stone-700 dark:text-slate-400 dark:hover:text-slate-200' }} font-bold text-[9px] uppercase tracking-widest transition">
                            <i class="fas fa-user-shield text-[10px]"></i> ICT
                        </a>
                        <form action="{{ route('wt.switch_executive_account') }}" method="POST" class="flex items-center">
                            @csrf
                            <label class="sr-only" for="executive_account_switcher">Executive Account</label>
                            <select id="executive_account_switcher" name="executive_user_id" onchange="if(this.value) this.form.submit()" class="min-w-[190px] border-0 bg-transparent px-3 text-[9px] font-black uppercase tracking-widest text-stone-500 outline-none transition hover:text-stone-700 dark:text-slate-400 dark:hover:text-slate-200">
                                <option value="">Executive Account</option>
                                @foreach($executiveSwitcherAccounts as $executiveAccount)
                                    <option value="{{ $executiveAccount->user_id }}">
                                        {{ strtoupper($executiveAccount->full_name ?: $executiveAccount->username) }}{{ $executiveAccount->department ? ' - ' . strtoupper($executiveAccount->department) : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                    @endif

                    @if($isExecutiveImpersonation)
                    <form action="{{ route('wt.return_to_ict_account') }}" method="POST" class="hidden md:block">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 rounded-full border border-sky-200 bg-sky-50 px-3 py-2 text-[9px] font-black uppercase tracking-[0.14em] text-sky-700 transition hover:bg-sky-100 dark:border-sky-900/60 dark:bg-sky-950/30 dark:text-sky-200">
                            <i class="fas fa-arrow-left text-[10px]"></i>
                            Return ICT
                        </button>
                    </form>
                    @endif

                    <div class="relative">
                        <button id="notificationToggle" type="button" class="topbar-action-btn relative" title="Open notifications" aria-label="Open notifications">
                            <i class="fas fa-bell text-base"></i>
                            @if($headerUnreadNotifications > 0)
                            <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-red-500 text-white text-[9px] font-black flex items-center justify-center">{{ $headerUnreadNotifications > 99 ? '99+' : $headerUnreadNotifications }}</span>
                            @endif
                        </button>
                        <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-[320px] max-w-[86vw] rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-2xl z-50 overflow-hidden">
                            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 dark:border-slate-700">
                                <p class="text-[10px] font-black uppercase tracking-[0.14em] text-slate-600 dark:text-slate-200">Notifications</p>
                                @if($headerUnreadNotifications > 0)
                                <form action="{{ route('wt.notifications.read_all') }}" method="POST">
                                    @csrf
                                    <button class="text-[9px] font-bold uppercase tracking-[0.08em] text-[#8D6742] hover:text-[#B38A5A]">Mark all read</button>
                                </form>
                                @endif
                            </div>
                            <div class="max-h-[360px] overflow-y-auto">
                                @forelse($headerNotifications as $notification)
                                @php
                                    $notificationUrl = $resolveNotificationUrl($notification);
                                @endphp
                                <div class="border-b border-slate-100 dark:border-slate-700/70 {{ is_null($notification->read_at) ? 'bg-amber-50/40 dark:bg-amber-900/10' : 'bg-transparent' }}">
                                    <form action="{{ route('wt.notifications.read', $notification->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="redirect_url" value="{{ $notificationUrl }}">
                                        <button type="submit" class="notification-item-btn items-start justify-between gap-3 text-left transition hover:bg-slate-50 dark:hover:bg-slate-700/50">
                                            <span class="min-w-0">
                                                <span class="block text-[10px] font-black uppercase tracking-[0.08em] text-slate-700 dark:text-slate-100">{{ $notification->data['title'] ?? 'Notification' }}</span>
                                                <span class="mt-1 block text-[10px] leading-5 text-slate-500 dark:text-slate-300">{{ $notification->data['message'] ?? '' }}</span>
                                                <span class="mt-1 block text-[9px] font-semibold text-slate-400 dark:text-slate-500">{{ $notification->created_at?->diffForHumans() }}</span>
                                            </span>
                                            <span class="notification-item-action shrink-0 text-[9px] font-bold uppercase tracking-[0.08em]">
                                                {{ is_null($notification->read_at) ? 'Read' : 'Open' }}
                                            </span>
                                        </button>
                                    </form>
                                </div>
                                @empty
                                <div class="px-4 py-10 text-center text-[10px] font-semibold uppercase tracking-[0.1em] text-slate-400 dark:text-slate-500">
                                    No notifications yet.
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- Theme Toggle --}}
                    <button id="theme-toggle" type="button" class="topbar-action-btn focus:outline-none" title="Toggle dark mode" aria-label="Toggle dark mode">
                        <i id="theme-toggle-dark-icon" class="hidden fas fa-moon text-base"></i>
                        <i id="theme-toggle-light-icon" class="hidden fas fa-sun text-base text-yellow-500"></i>
                    </button>

                    <form id="logout-form" action="{{ route('wt.logout') }}" method="POST" class="hidden">@csrf</form>
                    <button onclick="handleLogout()" class="topbar-signout-btn" title="Sign out" aria-label="Sign out">
                        <span class="mobile-hide-label hidden md:inline">Sign Out</span>
                        <span class="mobile-hide-label lg:hidden">Logout</span>
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </div>
                </div>
                @if($actualRole === 'admin_it')
                <div class="mobile-role-switcher md:hidden bg-stone-100 dark:bg-slate-800/50 p-1 rounded-xl border border-stone-200 dark:border-slate-700 mt-3">
                    <a href="{{ route('wt.switch_view', 'admin_it') }}" class="flex items-center gap-1 px-2 py-2 rounded-lg {{ $effectiveRole === 'admin_it' ? 'bg-[#B38A5A] text-white' : 'text-stone-500 dark:text-slate-400' }} font-bold text-[9px] uppercase tracking-widest transition">
                        <i class="fas fa-user-shield text-[10px]"></i><span>IT</span>
                    </a>
                    <a href="{{ route('wt.switch_view', 'admin') }}" class="flex items-center gap-1 px-2 py-2 rounded-lg {{ $effectiveRole === 'admin' ? 'bg-[#B38A5A] text-white' : 'text-stone-500 dark:text-slate-400' }} font-bold text-[9px] uppercase tracking-widest transition">
                        <i class="fas fa-user-tie text-[10px]"></i><span>Executive</span>
                    </a>
                    <form action="{{ route('wt.switch_executive_account') }}" method="POST" class="mobile-executive-switch-form">
                        @csrf
                        <label class="sr-only" for="mobile_executive_account_switcher">Executive Account</label>
                        <select id="mobile_executive_account_switcher" name="executive_user_id" onchange="if(this.value) this.form.submit()" class="w-full rounded-lg border border-slate-200 bg-white px-2 py-2 font-black uppercase text-slate-600 outline-none dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200">
                            <option value="">Open Executive Account</option>
                            @foreach($executiveSwitcherAccounts as $executiveAccount)
                                <option value="{{ $executiveAccount->user_id }}">
                                    {{ strtoupper($executiveAccount->full_name ?: $executiveAccount->username) }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
                @endif
                @if($isExecutiveImpersonation)
                <form action="{{ route('wt.return_to_ict_account') }}" method="POST" class="md:hidden mt-3">
                    @csrf
                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-3 py-2 text-[9px] font-black uppercase tracking-[0.14em] text-sky-700 dark:border-sky-900/60 dark:bg-sky-950/30 dark:text-sky-200">
                        <i class="fas fa-arrow-left text-[10px]"></i>
                        Return ICT
                    </button>
                </form>
                @endif
            </header>

            <main id="systemMainScroll" class="p-6 md:p-10 overflow-y-auto w-full">
                <nav class="w-full max-w-[1360px] mx-auto mb-8 text-[11px] font-black uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400" aria-label="Breadcrumb">
                    <ol class="flex flex-wrap items-center gap-2">
                        <li>
                            <a href="{{ $isAdminItView ? route('wt.admin.dashboard') : route('wt.admin.requests.create.shared') }}" class="hover:text-[#8D6742] dark:hover:text-[#D1AE7B]">Dashboard</a>
                        </li>
                        <li class="text-slate-300 dark:text-slate-600">/</li>
                        <li class="text-slate-700 dark:text-slate-200">@yield('title', 'Executive Dashboard')</li>
                    </ol>
                </nav>
                <div class="content-surface w-full max-w-[1360px] mx-auto rounded-xl md:rounded-[40px] p-8 sm:p-10 md:p-12">
                    @include('wt.partials.flash-alerts')
                    @yield('content')
                </div>
                <style>
                    .content-surface #walkiesTable thead th,
                    .content-surface #maintenanceTable thead th,
                    .content-surface #unusedTable thead th,
                    .content-surface #duplicateTable thead th,
                    .content-surface #specialTable thead th {
                        height: 34px !important;
                        padding: 8px 12px !important;
                        border: 1px solid #263244 !important;
                        background: #1e293b !important;
                        color: #cbd5e1 !important;
                        font-size: 10px !important;
                        font-weight: 900 !important;
                        line-height: 1.2 !important;
                        letter-spacing: 0.08em !important;
                        text-transform: uppercase !important;
                        white-space: nowrap !important;
                    }

                    .content-surface #walkiesTable tbody td,
                    .content-surface #maintenanceTable tbody td,
                    .content-surface #unusedTable tbody td,
                    .content-surface #duplicateTable tbody td,
                    .content-surface #specialTable tbody td {
                        min-height: 30px !important;
                        padding: 4px 10px !important;
                        border: 1px solid #263244 !important;
                        background: #111827 !important;
                        color: #dbe4f0 !important;
                        font-size: 11px !important;
                        font-weight: 700 !important;
                        line-height: 1.35 !important;
                        vertical-align: middle !important;
                    }

                    .content-surface #walkiesTable tbody tr:hover td,
                    .content-surface #maintenanceTable tbody tr:hover td,
                    .content-surface #unusedTable tbody tr:hover td,
                    .content-surface #duplicateTable tbody tr:hover td,
                    .content-surface #specialTable tbody tr:hover td {
                        background: #172033 !important;
                    }

                    html:not(.dark) body .content-surface #walkiesTable thead th,
                    html:not(.dark) body .content-surface #maintenanceTable thead th,
                    html:not(.dark) body .content-surface #unusedTable thead th,
                    html:not(.dark) body .content-surface #duplicateTable thead th,
                    html:not(.dark) body .content-surface #specialTable thead th {
                        border-color: #e2e8f0 !important;
                        background: #f8fafc !important;
                        color: #64748b !important;
                    }

                    html:not(.dark) body .content-surface #walkiesTable tbody td,
                    html:not(.dark) body .content-surface #maintenanceTable tbody td,
                    html:not(.dark) body .content-surface #unusedTable tbody td,
                    html:not(.dark) body .content-surface #duplicateTable tbody td,
                    html:not(.dark) body .content-surface #specialTable tbody td {
                        border-color: #eef2f7 !important;
                        background: #ffffff !important;
                        color: #334155 !important;
                    }

                    html:not(.dark) body .content-surface #walkiesTable tbody tr:hover td,
                    html:not(.dark) body .content-surface #maintenanceTable tbody tr:hover td,
                    html:not(.dark) body .content-surface #unusedTable tbody tr:hover td,
                    html:not(.dark) body .content-surface #duplicateTable tbody tr:hover td,
                    html:not(.dark) body .content-surface #specialTable tbody tr:hover td {
                        background: #f8fafc !important;
                    }

                    .content-surface .dataTables_scrollBody thead,
                    .content-surface .dataTables_scrollBody thead tr,
                    .content-surface .dataTables_scrollBody thead th,
                    .content-surface .dataTables_scrollBody thead td {
                        height: 0 !important;
                        max-height: 0 !important;
                        padding-top: 0 !important;
                        padding-bottom: 0 !important;
                        border-top: 0 !important;
                        border-bottom: 0 !important;
                        line-height: 0 !important;
                        overflow: hidden !important;
                        visibility: collapse !important;
                    }

                    .content-surface #walkiesTable td:last-child,
                    .content-surface #maintenanceTable td:last-child,
                    .content-surface #unusedTable td:last-child,
                    .content-surface #duplicateTable td:last-child,
                    .content-surface #specialTable td:last-child {
                        text-align: center !important;
                    }

                    .content-surface #mainTableContainer.inventory-table-shell {
                        overflow: hidden !important;
                    }

                    .content-surface #inventoryTableScroll.clean-admin-table-scroll {
                        display: block !important;
                        max-width: 100% !important;
                        overflow-x: scroll !important;
                        overflow-y: visible !important;
                        scrollbar-gutter: stable both-edges !important;
                        scrollbar-width: thin !important;
                        scrollbar-color: #9ca3af transparent !important;
                        cursor: grab !important;
                        -webkit-overflow-scrolling: touch !important;
                    }

                    .content-surface #inventoryTableScroll.clean-admin-table-scroll:active {
                        cursor: grabbing !important;
                    }

                    .content-surface #inventoryTableScroll.clean-admin-table-scroll::-webkit-scrollbar {
                        height: 11px !important;
                    }

                    .content-surface #inventoryTableScroll.clean-admin-table-scroll::-webkit-scrollbar-track {
                        background: transparent !important;
                    }

                    .content-surface #inventoryTableScroll.clean-admin-table-scroll::-webkit-scrollbar-thumb {
                        border: 3px solid transparent !important;
                        border-radius: 999px !important;
                        background: #9ca3af !important;
                        background-clip: content-box !important;
                    }

                    .content-surface #walkiesTable {
                        table-layout: fixed !important;
                        width: 2620px !important;
                        min-width: 2620px !important;
                    }

                    .content-surface #walkiesTable th:last-child,
                    .content-surface #walkiesTable td:last-child {
                        position: sticky !important;
                        right: 0 !important;
                        z-index: 30 !important;
                        width: 142px !important;
                        min-width: 142px !important;
                        max-width: 142px !important;
                        box-shadow: -1px 0 0 rgba(148, 163, 184, .22) !important;
                    }

                    .content-surface #walkiesTable thead th:last-child {
                        z-index: 40 !important;
                    }

                    html:not(.dark) .content-surface #walkiesTable th:last-child {
                        background: #f8fafc !important;
                    }

                    html:not(.dark) .content-surface #walkiesTable td:last-child {
                        background: #ffffff !important;
                    }

                    html.dark .content-surface #walkiesTable th:last-child,
                    .dark .content-surface #walkiesTable th:last-child {
                        background: #172033 !important;
                    }

                    html.dark .content-surface #walkiesTable td:last-child,
                    .dark .content-surface #walkiesTable td:last-child {
                        background: #0f172a !important;
                    }

                    .content-surface #walkiesTable th:last-child:not(.inventory-action-col),
                    .content-surface #walkiesTable td:last-child:not(.inventory-action-col) {
                        position: static !important;
                        right: auto !important;
                        z-index: auto !important;
                        width: 95px !important;
                        min-width: 95px !important;
                        max-width: 95px !important;
                        box-shadow: none !important;
                    }

                    .content-surface #walkiesTable th.inventory-action-col,
                    .content-surface #walkiesTable td.inventory-action-col {
                        position: sticky !important;
                        left: auto !important;
                        right: 0 !important;
                        z-index: 32 !important;
                        width: 132px !important;
                        min-width: 132px !important;
                        max-width: 132px !important;
                        text-align: center !important;
                        box-shadow: -1px 0 0 rgba(148, 163, 184, .22) !important;
                    }

                    .content-surface #walkiesTable thead th.inventory-action-col {
                        z-index: 45 !important;
                    }

                    html:not(.dark) .content-surface #walkiesTable th.inventory-action-col {
                        background: transparent !important;
                    }

                    html:not(.dark) .content-surface #walkiesTable td.inventory-action-col {
                        background: transparent !important;
                    }

                    html.dark .content-surface #walkiesTable th.inventory-action-col,
                    .dark .content-surface #walkiesTable th.inventory-action-col {
                        background: #172033 !important;
                    }

                    html.dark .content-surface #walkiesTable td.inventory-action-col,
                    .dark .content-surface #walkiesTable td.inventory-action-col {
                        background: #0f172a !important;
                    }

                    .content-surface #walkiesTable td.inventory-action-col > div,
                    .content-surface #walkiesTable td:last-child > div,
                    .content-surface #maintenanceTable td:last-child > div,
                    .content-surface #unusedTable td:last-child .unused-actions,
                    .content-surface #duplicateTable td:last-child > div,
                    .content-surface #specialTable td:last-child > div {
                        display: inline-flex !important;
                        align-items: center !important;
                        justify-content: center !important;
                        gap: 8px !important;
                        flex-wrap: nowrap !important;
                    }

                    .content-surface #walkiesTable td:last-child form,
                    .content-surface #maintenanceTable td:last-child form,
                    .content-surface #unusedTable td:last-child form,
                    .content-surface #duplicateTable td:last-child form,
                    .content-surface #specialTable td:last-child form {
                        display: inline-flex !important;
                        margin: 0 !important;
                    }

                    .content-surface #walkiesTable td:last-child .wt-btn,
                    .content-surface #maintenanceTable td:last-child .wt-btn,
                    .content-surface #unusedTable td:last-child .unused-action-btn,
                    .content-surface #duplicateTable td:last-child .wt-btn,
                    .content-surface #specialTable td:last-child .wt-btn,
                    .content-surface #walkiesTable td:last-child .wt-btn-danger,
                    .content-surface #maintenanceTable td:last-child .wt-btn-danger,
                    .content-surface #duplicateTable td:last-child .wt-btn-danger,
                    .content-surface #specialTable td:last-child .wt-btn-danger,
                    .content-surface #walkiesTable td:last-child .wt-btn-success,
                    .content-surface #maintenanceTable td:last-child .wt-btn-success,
                    .content-surface #duplicateTable td:last-child .wt-btn-success,
                    .content-surface #specialTable td:last-child .wt-btn-success {
                        display: inline-flex !important;
                        align-items: center !important;
                        justify-content: center !important;
                        min-width: 48px !important;
                        min-height: 30px !important;
                        height: 30px !important;
                        padding: 0 12px !important;
                        border-radius: 6px !important;
                        border: 1px solid #4f6275 !important;
                        background: #4f6275 !important;
                        color: #ffffff !important;
                        box-shadow: none !important;
                        font-size: 10px !important;
                        font-weight: 900 !important;
                        line-height: 1 !important;
                        letter-spacing: 0 !important;
                        text-transform: uppercase !important;
                        white-space: nowrap !important;
                        transform: none !important;
                    }

                    .content-surface #walkiesTable td:last-child .wt-btn:not(.wt-btn-danger):not(.wt-btn-success),
                    .content-surface #maintenanceTable td:last-child .wt-btn:not(.wt-btn-danger):not(.wt-btn-success),
                    .content-surface #duplicateTable td:last-child .wt-btn:not(.wt-btn-danger):not(.wt-btn-success),
                    .content-surface #specialTable td:last-child .wt-btn:not(.wt-btn-danger):not(.wt-btn-success),
                    .content-surface #unusedTable td:last-child .unused-action-btn:not(.delete) {
                        border-color: #93c5fd !important;
                        background: #eff6ff !important;
                        color: #1d4ed8 !important;
                    }

                    .content-surface #walkiesTable td:last-child form .wt-btn:not(.wt-btn-danger):not(.wt-btn-success) {
                        border-color: #fcd34d !important;
                        background: #fffbeb !important;
                        color: #92400e !important;
                    }

                    .content-surface #walkiesTable td:last-child .wt-btn-success,
                    .content-surface #maintenanceTable td:last-child .wt-btn-success,
                    .content-surface #duplicateTable td:last-child .wt-btn-success,
                    .content-surface #specialTable td:last-child .wt-btn-success {
                        border-color: #86efac !important;
                        background: #f0fdf4 !important;
                        color: #166534 !important;
                    }

                    .content-surface #walkiesTable td:last-child .wt-btn-danger,
                    .content-surface #maintenanceTable td:last-child .wt-btn-danger,
                    .content-surface #duplicateTable td:last-child .wt-btn-danger,
                    .content-surface #specialTable td:last-child .wt-btn-danger,
                    .content-surface #unusedTable td:last-child .unused-action-btn.delete {
                        border-color: #fca5a5 !important;
                        background: #fef2f2 !important;
                        color: #991b1b !important;
                    }

                    .content-surface #unusedTable td:last-child .unused-action-btn.used {
                        border-color: #86efac !important;
                        background: #f0fdf4 !important;
                        color: #166534 !important;
                    }

                    .content-surface #walkiesTable td:last-child .wt-btn:not(.wt-btn-danger):not(.wt-btn-success):hover,
                    .content-surface #maintenanceTable td:last-child .wt-btn:not(.wt-btn-danger):not(.wt-btn-success):hover,
                    .content-surface #duplicateTable td:last-child .wt-btn:not(.wt-btn-danger):not(.wt-btn-success):hover,
                    .content-surface #specialTable td:last-child .wt-btn:not(.wt-btn-danger):not(.wt-btn-success):hover,
                    .content-surface #unusedTable td:last-child .unused-action-btn:not(.delete):hover {
                        border-color: #2563eb !important;
                        background: #2563eb !important;
                        color: #ffffff !important;
                    }

                    .content-surface #walkiesTable td:last-child form .wt-btn:not(.wt-btn-danger):not(.wt-btn-success):hover {
                        border-color: #d97706 !important;
                        background: #d97706 !important;
                        color: #ffffff !important;
                    }

                    .content-surface #walkiesTable td:last-child .wt-btn-success:hover,
                    .content-surface #maintenanceTable td:last-child .wt-btn-success:hover,
                    .content-surface #duplicateTable td:last-child .wt-btn-success:hover,
                    .content-surface #specialTable td:last-child .wt-btn-success:hover {
                        border-color: #16a34a !important;
                        background: #16a34a !important;
                        color: #ffffff !important;
                    }

                    .content-surface #walkiesTable td:last-child .wt-btn-danger:hover,
                    .content-surface #maintenanceTable td:last-child .wt-btn-danger:hover,
                    .content-surface #duplicateTable td:last-child .wt-btn-danger:hover,
                    .content-surface #specialTable td:last-child .wt-btn-danger:hover,
                    .content-surface #unusedTable td:last-child .unused-action-btn.delete:hover {
                        border-color: #dc2626 !important;
                        background: #dc2626 !important;
                        color: #ffffff !important;
                    }

                    .content-surface #unusedTable td:last-child .unused-action-btn.used:hover {
                        border-color: #16a34a !important;
                        background: #16a34a !important;
                        color: #ffffff !important;
                    }

                    /* Final page header theme lock. Keep title headers readable in both modes. */
                    html:not(.dark) body .content-surface .page-header-block,
                    html:not(.dark) body .content-surface .inventory-page-header,
                    html:not(.dark) body .content-surface .duplicate-hero .page-header-block {
                        background: transparent !important;
                        border-color: transparent !important;
                        color: #1f2937 !important;
                    }

                    html.dark body .content-surface .page-header-block,
                    html.dark body .content-surface .inventory-page-header,
                    html.dark body .content-surface .duplicate-hero .page-header-block,
                    .dark body .content-surface .page-header-block,
                    .dark body .content-surface .inventory-page-header,
                    .dark body .content-surface .duplicate-hero .page-header-block {
                        background: transparent !important;
                        border-color: transparent !important;
                        color: #f8fafc !important;
                    }

                    html:not(.dark) body .content-surface .page-title-standard,
                    html:not(.dark) body .content-surface .page-header h1,
                    html:not(.dark) body .content-surface .page-header h2,
                    html:not(.dark) body .content-surface .page-header h3,
                    html:not(.dark) body .content-surface .inventory-page-header .page-title-standard,
                    html:not(.dark) body .content-surface .duplicate-hero .page-title-standard {
                        color: #1f2937 !important;
                    }

                    html.dark body .content-surface .page-title-standard,
                    html.dark body .content-surface .page-header h1,
                    html.dark body .content-surface .page-header h2,
                    html.dark body .content-surface .page-header h3,
                    html.dark body .content-surface .inventory-page-header .page-title-standard,
                    html.dark body .content-surface .duplicate-hero .page-title-standard,
                    .dark body .content-surface .page-title-standard,
                    .dark body .content-surface .page-header h1,
                    .dark body .content-surface .page-header h2,
                    .dark body .content-surface .page-header h3,
                    .dark body .content-surface .inventory-page-header .page-title-standard,
                    .dark body .content-surface .duplicate-hero .page-title-standard {
                        color: #f8fafc !important;
                    }

                    html:not(.dark) body .content-surface .page-subtitle-standard,
                    html:not(.dark) body .content-surface .page-subtitle,
                    html:not(.dark) body .content-surface .inventory-page-header .page-subtitle-standard,
                    html:not(.dark) body .content-surface .duplicate-hero .page-subtitle-standard {
                        color: #64748b !important;
                    }

                    html:not(.dark) body .content-surface .inventory-bulk-bar {
                        background: #ffffff !important;
                        background-color: #ffffff !important;
                        background-image: none !important;
                        border: 1px solid #d8e1ed !important;
                        box-shadow: 0 1px 2px rgba(15, 23, 42, .04) !important;
                    }

                    html.dark body .content-surface .page-subtitle-standard,
                    html.dark body .content-surface .page-subtitle,
                    html.dark body .content-surface .inventory-page-header .page-subtitle-standard,
                    html.dark body .content-surface .duplicate-hero .page-subtitle-standard,
                    .dark body .content-surface .page-subtitle-standard,
                    .dark body .content-surface .page-subtitle,
                    .dark body .content-surface .inventory-page-header .page-subtitle-standard,
                    .dark body .content-surface .duplicate-hero .page-subtitle-standard {
                        color: #94a3b8 !important;
                    }
                </style>
            </main>
        </div>
    </div>

    <div id="systemScrollControls" class="system-scroll-controls" aria-label="Page scroll controls">
        <button type="button" class="system-scroll-btn" data-scroll-target="top" title="Scroll to top" aria-label="Scroll to top">
            <i class="fa-solid fa-chevron-up"></i>
        </button>
        <button type="button" class="system-scroll-btn" data-scroll-target="bottom" title="Scroll to bottom" aria-label="Scroll to bottom">
            <i class="fa-solid fa-chevron-down"></i>
        </button>
    </div>

    <style>
        .system-scroll-controls {
            position: fixed;
            right: 18px;
            bottom: 96px;
            z-index: 70;
            display: flex;
            flex-direction: column;
            gap: 8px;
            opacity: 0;
            pointer-events: none;
            transform: translateY(8px);
            transition: opacity .18s ease, transform .18s ease;
        }

        .system-scroll-controls.is-visible {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0);
        }

        .system-scroll-btn {
            display: inline-flex;
            width: 38px;
            height: 38px;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            border: 1px solid rgba(203, 213, 225, .9);
            background: rgba(255, 255, 255, .92);
            color: #0f172a;
            box-shadow: 0 12px 30px rgba(15, 23, 42, .14);
            font-size: 12px;
            transition: border-color .15s ease, background-color .15s ease, color .15s ease, transform .15s ease;
        }

        .system-scroll-btn:hover {
            transform: translateY(-1px);
            border-color: rgba(14, 116, 144, .38);
            background: #ffffff;
            color: #0e7490;
        }

        .system-scroll-btn:disabled {
            opacity: .45;
            cursor: not-allowed;
            transform: none;
        }

        html.dark .system-scroll-btn,
        .dark .system-scroll-btn {
            border-color: rgba(51, 65, 85, .95);
            background: rgba(15, 23, 42, .92);
            color: #e2e8f0;
            box-shadow: 0 14px 34px rgba(0, 0, 0, .28);
        }

        html.dark .system-scroll-btn:hover,
        .dark .system-scroll-btn:hover {
            border-color: rgba(56, 189, 248, .45);
            background: #111827;
            color: #7dd3fc;
        }

        @media (max-width: 768px) {
            .system-scroll-controls {
                right: 12px;
                bottom: 86px;
            }

            .system-scroll-btn {
                width: 34px;
                height: 34px;
                border-radius: 10px;
            }
        }
    </style>

    <div id="modernConfirmModal" class="modern-confirm-overlay" aria-hidden="true">
        <div class="modern-confirm-card" role="dialog" aria-modal="true" aria-labelledby="modernConfirmTitle">
            <div class="modern-confirm-header">
                <div class="modern-confirm-icon">
                    <i class="fas fa-bolt"></i>
                </div>
                <div>
                    <div id="modernConfirmTitle" class="modern-confirm-title">Confirm Action</div>
                    <div class="modern-confirm-subtitle">Remark is optional</div>
                </div>
                <button type="button" class="modern-confirm-close" onclick="closeModernConfirm()" title="Close">
                    <i class="fas fa-times"></i>
                </button>
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

    <div id="logoutModal" class="logout-modal-overlay" aria-hidden="true">
        <div class="logout-modal p-5 md:p-6">
            <div class="flex items-start justify-between gap-4">
                <div class="logout-modal-icon">
                    <i class="fas fa-right-from-bracket"></i>
                </div>
                <button type="button" class="logout-modal-close" onclick="closeLogoutModal()" aria-label="Close logout dialog">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mt-4">
                <h3 class="logout-modal-title">Logout From This Account?</h3>
                <p class="logout-modal-copy mt-2">
                    You are about to end your current session and return to the sign in page.
                </p>
            </div>
            <div class="logout-modal-actions mt-6">
                <button type="button" class="logout-modal-btn logout-modal-btn-cancel" onclick="closeLogoutModal()">Stay Signed In</button>
                <button type="button" class="logout-modal-btn logout-modal-btn-confirm" onclick="submitLogout()">Yes, Logout</button>
            </div>
        </div>
    </div>

    <script>
    const themeToggleBtn = document.getElementById('theme-toggle');
    const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
    const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

    function getSystemTheme() {
        const savedTheme = localStorage.getItem('color-theme');
        if (savedTheme === 'dark' || savedTheme === 'light') {
            return savedTheme;
        }

        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    function applySystemTheme(theme) {
        document.documentElement.classList.toggle('dark', theme === 'dark');
        document.documentElement.dataset.theme = theme;
        document.documentElement.style.colorScheme = theme;

        if (themeToggleDarkIcon && themeToggleLightIcon) {
            const isDark = theme === 'dark';
            themeToggleDarkIcon.classList.toggle('hidden', isDark);
            themeToggleLightIcon.classList.toggle('hidden', !isDark);
            themeToggleDarkIcon.style.display = isDark ? 'none' : 'inline-block';
            themeToggleLightIcon.style.display = isDark ? 'inline-block' : 'none';
        }

        if (themeToggleBtn) {
            const nextLabel = theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode';
            themeToggleBtn.setAttribute('aria-label', nextLabel);
            themeToggleBtn.setAttribute('title', nextLabel);
        }
    }

    applySystemTheme(getSystemTheme());

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', function() {
            const nextTheme = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
            localStorage.setItem('color-theme', nextTheme);
            applySystemTheme(nextTheme);
        });
    }

    function handleLogout() {
        openLogoutModal();
    }

    function openLogoutModal() {
        const modal = document.getElementById('logoutModal');
        if (!modal) return;
        modal.classList.add('active');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeLogoutModal() {
        const modal = document.getElementById('logoutModal');
        if (!modal) return;
        modal.classList.remove('active');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    function submitLogout() {
        document.getElementById('logout-form').submit();
    }

    function openMobileSidebar() {
        document.getElementById('mobileSidebar').classList.add('active');
        document.getElementById('mobileSidebarOverlay').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeMobileSidebar() {
        document.getElementById('mobileSidebar').classList.remove('active');
        document.getElementById('mobileSidebarOverlay').classList.remove('active');
        document.body.style.overflow = '';
    }

    function toggleDropdown(trigger) {
        const wrapper = trigger.closest('.dropdown-wrapper');
        const isOpen = wrapper.classList.toggle('open');
        
        // Optional: Close other open dropdowns if any
        document.querySelectorAll('.dropdown-wrapper').forEach(dw => {
            if (dw !== wrapper) dw.classList.remove('open');
        });
    }

    function bindAutoUppercase(root = document) {
        const fields = root.querySelectorAll('input[type="text"], input[type="search"], textarea');
        fields.forEach((field) => {
            if (field.name === 'username' || field.id === 'edit_username' || field.dataset.preserveCase === 'true') return;
            if (field.dataset.uppercaseBound === 'true') return;
            field.dataset.uppercaseBound = 'true';
            field.addEventListener('input', function () {
                const start = this.selectionStart;
                const end = this.selectionEnd;
                this.value = this.value.toUpperCase();
                if (typeof start === 'number' && typeof end === 'number') {
                    this.setSelectionRange(start, end);
                }
            });
        });
    }

    function closeSidebarInfoPopovers() {
        document.querySelectorAll('.nav-info-popover').forEach((item) => {
            item.classList.add('hidden');
            item.style.left = '';
            item.style.top = '';
            item.style.visibility = '';
        });

        document.querySelectorAll('.nav-info-btn').forEach((btn) => {
            btn.classList.remove('is-open');
            btn.setAttribute('aria-expanded', 'false');
        });
    }

    function positionSidebarInfoPopover(button, popover) {
        const spacing = 12;
        const viewportPadding = 16;
        const buttonRect = button.getBoundingClientRect();

        popover.classList.remove('hidden');
        popover.style.visibility = 'hidden';

        const maxWidth = Math.min(280, window.innerWidth - (viewportPadding * 2));
        popover.style.width = `${Math.max(220, maxWidth)}px`;

        let left = buttonRect.right + spacing;
        const popoverWidth = popover.offsetWidth;
        if (left + popoverWidth > window.innerWidth - viewportPadding) {
            left = buttonRect.left - popoverWidth - spacing;
        }
        left = Math.max(viewportPadding, Math.min(left, window.innerWidth - viewportPadding - popoverWidth));

        let top = buttonRect.top + (buttonRect.height / 2) - (popover.offsetHeight / 2);
        top = Math.max(viewportPadding, Math.min(top, window.innerHeight - viewportPadding - popover.offsetHeight));

        popover.style.left = `${left}px`;
        popover.style.top = `${top}px`;
        popover.style.visibility = 'visible';
    }

    function syncAdminTableFooter(tableApi, footerParts) {
        if (!tableApi || !footerParts) return;

        const info = tableApi.page.info();
        const hasPages = info.pages > 0;
        const start = info.recordsDisplay === 0 ? 0 : info.start + 1;

        footerParts.info.textContent = `Showing ${start} to ${info.end} of ${info.recordsDisplay} entries`;
        footerParts.current.textContent = hasPages ? info.page + 1 : 1;
        footerParts.current.classList.toggle('hidden', !hasPages);
        footerParts.prev.disabled = !hasPages || info.page === 0;
        footerParts.next.disabled = !hasPages || info.page >= info.pages - 1;
    }

    function mountAdminTableFooter(tableApi) {
        if (!tableApi || typeof tableApi.table !== 'function') return;

        const wrapper = tableApi.table().container();
        if (!wrapper) return;

        wrapper.classList.add('adminit-footer-mounted');

        const mountedFooters = Array.from(wrapper.querySelectorAll('.adminit-table-footer'));

        if (wrapper.dataset.adminitFooterMounted === 'true') {
            const activeFooter = wrapper._adminitFooterParts?.footer || mountedFooters[0];
            mountedFooters.forEach((footer, index) => {
                if (footer !== activeFooter || index > 0) footer.remove();
            });

            if (activeFooter && activeFooter.isConnected) {
                syncAdminTableFooter(tableApi, wrapper._adminitFooterParts);
                return;
            }

            wrapper.dataset.adminitFooterMounted = 'false';
        }

        wrapper.dataset.adminitFooterMounted = 'true';

        wrapper.querySelectorAll('.adminit-table-footer').forEach((footer) => footer.remove());

        wrapper.querySelectorAll('.dataTables_info, .dataTables_paginate').forEach((child) => {
            child.remove();
        });

        const footer = document.createElement('div');
        footer.className = 'adminit-table-footer';
        footer.innerHTML = `
            <div class="adminit-table-info">Showing 0 to 0 of 0 entries</div>
            <div class="adminit-table-pagination">
                <button type="button" class="adminit-page-link adminit-prev-page">Previous</button>
                <span class="adminit-page-current hidden">1</span>
                <button type="button" class="adminit-page-link adminit-next-page">Next</button>
            </div>
        `;

        wrapper.appendChild(footer);

        const footerParts = {
            footer,
            info: footer.querySelector('.adminit-table-info'),
            prev: footer.querySelector('.adminit-prev-page'),
            current: footer.querySelector('.adminit-page-current'),
            next: footer.querySelector('.adminit-next-page'),
        };

        wrapper._adminitFooterParts = footerParts;

        footerParts.prev.addEventListener('click', function () {
            if (!this.disabled) tableApi.page('previous').draw('page');
        });

        footerParts.next.addEventListener('click', function () {
            if (!this.disabled) tableApi.page('next').draw('page');
        });

        tableApi.on('draw.dt', function () {
            wrapper.querySelectorAll('.dataTables_info, .dataTables_paginate').forEach((child) => {
                child.remove();
            });
            wrapper.querySelectorAll('.adminit-table-footer ~ .adminit-table-footer').forEach((footer) => footer.remove());
            syncAdminTableFooter(tableApi, footerParts);
        });

        syncAdminTableFooter(tableApi, footerParts);
    }

    const canUseAdminExports = false;

    function getAdminTableExportButtons(title, exportColumns = ':visible') {
        if (!canUseAdminExports) {
            return [];
        }

        return [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Export Excel',
                title: title,
                exportOptions: { columns: exportColumns },
                className: 'admin-export-excel'
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> Export PDF',
                title: title,
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: { columns: exportColumns },
                className: 'admin-export-pdf'
            }
        ];
    }

    function mountAdminTableExports(tableApi, hostSelector) {
        if (!canUseAdminExports) return;
        if (!tableApi || typeof tableApi.buttons !== 'function') return;

        const host = typeof hostSelector === 'string' ? document.querySelector(hostSelector) : hostSelector;
        if (!host) return;

        host.innerHTML = '';
        host.appendChild(tableApi.buttons().container()[0]);
    }

    function mountAdminTableExportDropdown(tableApi, hostSelector, label = 'Export') {
        if (!canUseAdminExports) return;
        if (!tableApi || typeof tableApi.button !== 'function') return;

        const host = typeof hostSelector === 'string' ? document.querySelector(hostSelector) : hostSelector;
        if (!host) return;

        const uid = host.id || `adminExport${Math.random().toString(36).slice(2)}`;
        const toggleId = `${uid}Toggle`;
        const menuId = `${uid}Menu`;

        host.innerHTML = `
            <div class="relative">
                <button type="button" id="${toggleId}" class="wt-btn wt-btn-soft" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-download text-[13px]"></i>
                    ${label}
                    <i class="fas fa-chevron-down text-[10px]"></i>
                </button>
                <div id="${menuId}" class="hidden absolute right-0 mt-2 w-44 rounded-xl border border-slate-200 bg-white shadow-2xl z-40 overflow-hidden">
                    <button type="button" data-export-type="excel" class="w-full flex items-center gap-2 px-4 py-3 text-left text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-emerald-50 hover:text-emerald-700">
                        <i class="fas fa-file-excel"></i> Excel
                    </button>
                    <button type="button" data-export-type="pdf" class="w-full flex items-center gap-2 px-4 py-3 text-left text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-red-50 hover:text-red-700">
                        <i class="fas fa-file-pdf"></i> PDF
                    </button>
                </div>
            </div>
        `;

        const toggle = document.getElementById(toggleId);
        const menu = document.getElementById(menuId);
        if (!toggle || !menu) return;

        const closeMenu = () => {
            menu.classList.add('hidden');
            toggle.setAttribute('aria-expanded', 'false');
        };

        toggle.addEventListener('click', function(event) {
            event.stopPropagation();
            const willOpen = menu.classList.contains('hidden');
            menu.classList.toggle('hidden');
            toggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
        });

        menu.querySelectorAll('[data-export-type]').forEach((item) => {
            item.addEventListener('click', function(event) {
                event.stopPropagation();
                const buttonIndex = this.dataset.exportType === 'pdf' ? 1 : 0;
                tableApi.button(buttonIndex).trigger();
                closeMenu();
            });
        });

        document.addEventListener('click', function(event) {
            if (!menu.contains(event.target) && !toggle.contains(event.target)) {
                closeMenu();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const notificationToggle = document.getElementById('notificationToggle');
        const notificationDropdown = document.getElementById('notificationDropdown');
        if (notificationToggle && notificationDropdown) {
            notificationToggle.addEventListener('click', function (event) {
                event.stopPropagation();
                notificationDropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', function (event) {
                if (!notificationDropdown.contains(event.target) && !notificationToggle.contains(event.target)) {
                    notificationDropdown.classList.add('hidden');
                }
            });
        }

        bindAutoUppercase();
        document.querySelectorAll('form').forEach((form) => {
            form.addEventListener('submit', function () {
                bindAutoUppercase(form);
                form.querySelectorAll('input[type="text"], input[type="search"], textarea').forEach((field) => {
                    if (field.name === 'username' || field.id === 'edit_username' || field.dataset.preserveCase === 'true') return;
                    field.value = field.value.toUpperCase();
                });
            });
        });

        const logoutModal = document.getElementById('logoutModal');
        if (logoutModal) {
            logoutModal.addEventListener('click', function (event) {
                if (event.target === logoutModal) {
                    closeLogoutModal();
                }
            });
        }

        const mainScroll = document.getElementById('systemMainScroll');
        const scrollControls = document.getElementById('systemScrollControls');
        const scrollTopButton = scrollControls?.querySelector('[data-scroll-target="top"]');
        const scrollBottomButton = scrollControls?.querySelector('[data-scroll-target="bottom"]');

        function updateSystemScrollControls() {
            if (!mainScroll || !scrollControls || !scrollTopButton || !scrollBottomButton) return;

            const maxScroll = Math.max(0, mainScroll.scrollHeight - mainScroll.clientHeight);
            const hasScroll = maxScroll > 24;
            const nearTop = mainScroll.scrollTop <= 8;
            const nearBottom = mainScroll.scrollTop >= maxScroll - 8;

            scrollControls.classList.toggle('is-visible', hasScroll);
            scrollTopButton.disabled = !hasScroll || nearTop;
            scrollBottomButton.disabled = !hasScroll || nearBottom;
        }

        scrollTopButton?.addEventListener('click', function () {
            mainScroll?.scrollTo({ top: 0, behavior: 'smooth' });
        });

        scrollBottomButton?.addEventListener('click', function () {
            if (!mainScroll) return;
            mainScroll.scrollTo({ top: mainScroll.scrollHeight, behavior: 'smooth' });
        });

        mainScroll?.addEventListener('scroll', updateSystemScrollControls, { passive: true });
        window.addEventListener('resize', updateSystemScrollControls);
        setTimeout(updateSystemScrollControls, 80);

        document.addEventListener('click', function (event) {
            const infoButton = event.target.closest('.nav-info-btn');

            if (infoButton) {
                event.preventDefault();
                event.stopPropagation();

            const popover = infoButton.parentElement?.querySelector('.nav-info-popover');
            const willOpen = popover?.classList.contains('hidden');

            closeSidebarInfoPopovers();

            if (popover && willOpen) {
                positionSidebarInfoPopover(infoButton, popover);
                infoButton.classList.add('is-open');
                infoButton.setAttribute('aria-expanded', 'true');
            }

                return;
            }

            if (!event.target.closest('.nav-info-btn') && !event.target.closest('.nav-info-popover')) {
                closeSidebarInfoPopovers();
            }
        }, true);
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeSidebarInfoPopovers();
            closeLogoutModal();
            closeModernConfirm();
            if (typeof closeGlobalWalkieTimeline === 'function') {
                closeGlobalWalkieTimeline();
            }
        }
    });

    window.addEventListener('resize', closeSidebarInfoPopovers);
    document.addEventListener('scroll', closeSidebarInfoPopovers, true);
    </script>
    <!-- Policies Modal -->
    <div id="policiesModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
            <div class="fixed inset-0 bg-slate-900/45 backdrop-blur-sm transition-opacity dark:bg-black/70" onclick="closePoliciesModal()"></div>
            <div class="relative transform overflow-hidden rounded-3xl border border-stone-200 bg-white text-left shadow-2xl shadow-slate-900/18 transition-all sm:my-8 sm:w-full sm:max-w-xl dark:border-slate-700 dark:bg-slate-900 dark:shadow-black/40">
                <div class="flex items-center justify-between border-b border-stone-100 bg-white px-6 pb-3 pt-6 dark:border-slate-800 dark:bg-slate-900">
                    <div>
                        <h3 class="text-base font-black text-[#3D2B1F] dark:text-slate-100 uppercase tracking-widest">System Policies</h3>
                        <p class="mt-1 text-[9px] font-bold uppercase tracking-[0.18em] text-stone-500 dark:text-slate-400">Walkie Talkie Usage Terms & Conditions</p>
                    </div>
                    <button onclick="closePoliciesModal()" class="flex h-8 w-8 items-center justify-center rounded-xl border border-stone-100 bg-stone-50 text-stone-500 transition-colors hover:bg-stone-100 hover:text-stone-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:bg-slate-700 dark:hover:text-slate-100">
                        <i class="fas fa-times text-[11px]"></i>
                    </button>
                </div>
                <div class="max-h-[62vh] overflow-y-auto p-6 custom-scrollbar bg-white dark:bg-slate-900">
                    <div class="space-y-4 text-sm font-medium leading-relaxed text-stone-600 dark:text-slate-300">
                        <div class="flex gap-3">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg border border-stone-200 bg-stone-50 text-[9px] font-black text-[#8B5E3C] dark:border-slate-700 dark:bg-slate-800 dark:text-[#d9b38c]">01</div>
                            <div>
                                <p class="font-black text-[#3D2B1F] dark:text-slate-100 uppercase tracking-wider text-[10px] mb-1">Device Responsibility</p>
                                <p class="text-[11px] leading-5">All users are responsible for the safe keeping of assigned walkie talkie units. Any loss or damage due to negligence must be reported immediately.</p>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg border border-stone-200 bg-stone-50 text-[9px] font-black text-[#8B5E3C] dark:border-slate-700 dark:bg-slate-800 dark:text-[#d9b38c]">02</div>
                            <div>
                                <p class="font-black text-[#3D2B1F] dark:text-slate-100 uppercase tracking-wider text-[10px] mb-1">Usage Restrictions</p>
                                <p class="text-[11px] leading-5">Devices are strictly for official FGV Johor Bulkers business use only. Unauthorized modifications or tampering with settings is prohibited.</p>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg border border-stone-200 bg-stone-50 text-[9px] font-black text-[#8B5E3C] dark:border-slate-700 dark:bg-slate-800 dark:text-[#d9b38c]">03</div>
                            <div>
                                <p class="font-black text-[#3D2B1F] dark:text-slate-100 uppercase tracking-wider text-[10px] mb-1">Return Policy</p>
                                <p class="text-[11px] leading-5">Temporary units must be returned by the specified end date. Permanent units must be returned upon resignation or transfer from the current department.</p>
                            </div>
                        </div>
                        <div class="flex gap-3 rounded-xl border border-amber-200 bg-amber-50 p-3 dark:border-amber-700/40 dark:bg-amber-950/30">
                            <i class="fas fa-info-circle mt-0.5 text-[11px] text-amber-500 dark:text-amber-300"></i>
                            <p class="text-[10px] font-bold uppercase leading-5 tracking-wider text-amber-800 dark:text-amber-200">Note: Full documentation and detailed legal terms are available upon request from the ICT Department.</p>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end border-t border-stone-100 bg-stone-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-800/60">
                    <button onclick="closePoliciesModal()" class="rounded-xl bg-[#8B5E3C] px-6 py-2.5 text-[9px] font-black uppercase tracking-[0.18em] text-white shadow-lg shadow-[#8B5E3C]/20 transition-all hover:bg-[#734C2F] dark:bg-[#A67B5B] dark:hover:bg-[#8B5E3C]">
                        I Understand
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    let modernConfirmForm = null;

    function openModernConfirm(form) {
        modernConfirmForm = form;
        const modal = document.getElementById('modernConfirmModal');
        const message = document.getElementById('modernConfirmMessage');
        const title = document.getElementById('modernConfirmTitle');
        const remark = document.getElementById('modernConfirmRemark');
        const remarkGroup = document.getElementById('modernConfirmRemarkGroup');
        const confirmButton = modal?.querySelector('.modern-confirm-btn.confirm');

        if (!modal || !message || !title || !remark) return false;

        const showRemark = form.dataset.modernConfirmRemark !== 'false';
        title.textContent = form.dataset.modernConfirmTitle || 'Confirm Action';
        message.textContent = form.dataset.modernConfirm || 'Confirm this action?';
        remark.value = '';
        remarkGroup?.classList.toggle('hidden', !showRemark);
        modal.classList.add('active');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        setTimeout(() => (showRemark ? remark : confirmButton)?.focus(), 80);
        return false;
    }

    function closeModernConfirm() {
        const modal = document.getElementById('modernConfirmModal');
        if (!modal) return;

        modal.classList.remove('active');
        modal.setAttribute('aria-hidden', 'true');
        modernConfirmForm = null;
        document.body.style.overflow = '';
    }

    function submitModernConfirm() {
        if (!modernConfirmForm) return;

        const remark = document.getElementById('modernConfirmRemark');
        const existing = modernConfirmForm.querySelector('input[name="action_remark"]');
        if (existing) existing.remove();

        if (remark && remark.value.trim() !== '') {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'action_remark';
            input.value = remark.value.trim();
            modernConfirmForm.appendChild(input);
        }

        modernConfirmForm.dataset.modernConfirmed = 'true';
        document.body.style.overflow = '';
        modernConfirmForm.submit();
    }

    document.addEventListener('submit', function(event) {
        const form = event.target.closest('form[data-modern-confirm]');
        if (!form || form.dataset.modernConfirmed === 'true') return;

        event.preventDefault();
        openModernConfirm(form);
    }, true);

    document.addEventListener('click', function(event) {
        const modal = document.getElementById('modernConfirmModal');
        if (event.target === modal) {
            closeModernConfirm();
        }
    });

    function openPoliciesModal() {
        const modal = document.getElementById('policiesModal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closePoliciesModal() {
        const modal = document.getElementById('policiesModal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    </script>

    @include('wt.partials.assistant-chatbox', ['assistantRole' => $effectiveRole])
    @include('wt.partials.form-option-datalists')
    @include('wt.partials.phone-format-script')
    @include('wt.partials.popup-redirect')

    <div id="globalWalkieTimelineModal" class="modal-overlay" onclick="closeGlobalWalkieTimelineOutside(event)" aria-hidden="true">
        <div class="modal-box global-walkie-modal" role="dialog" aria-modal="true" aria-labelledby="globalWalkieTimelineTitle">
            <div class="modal-header">
                <div class="min-w-0">
                    <p class="modal-subtitle">Walkie Talkie Details</p>
                    <h2 id="globalWalkieTimelineTitle" class="modal-title">-</h2>
                    <p id="globalWalkieTimelineSubtitle" class="modal-subtitle">-</p>
                </div>
                <button type="button" class="modal-close-btn" onclick="closeGlobalWalkieTimeline()" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body p-6">
                <div id="globalWalkieTimelineSummary" class="global-walkie-summary"></div>
                <div class="mt-5">
                    <p class="mb-3 text-[10px] font-black uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">History</p>
                    <div id="globalWalkieTimelineBody" class="global-walkie-history"></div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .global-walkie-modal {
            width: min(920px, calc(100vw - 32px)) !important;
            max-width: 920px !important;
        }
        .global-walkie-summary {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }
        .global-walkie-summary-item {
            border: 1px solid #d8e1ed;
            border-radius: 8px;
            background: #f8fafc;
            padding: 10px 12px;
        }
        .global-walkie-summary-label {
            color: #64748b;
            font-size: 9px;
            font-weight: 900;
            letter-spacing: .14em;
            text-transform: uppercase;
        }
        .global-walkie-summary-value {
            margin-top: 5px;
            color: #172033;
            font-size: 12px;
            font-weight: 900;
            line-height: 1.25;
            word-break: break-word;
        }
        .global-walkie-history {
            display: grid;
            gap: 8px;
            max-height: 320px;
            overflow-y: auto;
            padding-right: 4px;
        }
        .global-walkie-history-row {
            display: grid;
            grid-template-columns: 104px 1fr;
            gap: 12px;
            border: 1px solid #d8e1ed;
            border-radius: 8px;
            background: #ffffff;
            padding: 10px 12px;
        }
        .global-walkie-history-date {
            color: #64748b;
            font-size: 10px;
            font-weight: 900;
            line-height: 1.35;
        }
        .global-walkie-history-time {
            display: block;
            color: #94a3b8;
            font-size: 9px;
            font-weight: 800;
        }
        .global-walkie-history-title {
            margin: 0;
            color: #172033;
            font-size: 12px;
            font-weight: 900;
        }
        .global-walkie-history-detail {
            margin: 3px 0 0;
            color: #475569;
            font-size: 11px;
            font-weight: 700;
            line-height: 1.45;
        }
        .dark .global-walkie-summary-item,
        .dark .global-walkie-history-row {
            border-color: #334155;
            background: #0f172a;
        }
        .dark .global-walkie-summary-value,
        .dark .global-walkie-history-title {
            color: #f8fafc;
        }
        .dark .global-walkie-history-detail {
            color: #cbd5e1;
        }
        @media (max-width: 760px) {
            .global-walkie-summary,
            .global-walkie-history-row {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <script>
        function globalTimelineEscape(value) {
            return String(value ?? '').replace(/[&<>"']/g, function (character) {
                return {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;',
                }[character];
            });
        }

        async function openGlobalWalkieTimeline(walkieId) {
            const url = @json(route('wt.admin.walkies.timeline', ['walkie' => '__WALKIE__'])).replace('__WALKIE__', encodeURIComponent(walkieId));
            await openGlobalWalkieTimelineUrl(url);
        }

        async function openGlobalMaintenanceTimeline(maintenanceId) {
            const url = @json(route('wt.admin.maintenance.timeline', ['maintenance' => '__MAINTENANCE__'])).replace('__MAINTENANCE__', encodeURIComponent(maintenanceId));
            await openGlobalWalkieTimelineUrl(url);
        }

        async function openGlobalWalkieTimelineUrl(url) {
            const modal = document.getElementById('globalWalkieTimelineModal');
            const title = document.getElementById('globalWalkieTimelineTitle');
            const subtitle = document.getElementById('globalWalkieTimelineSubtitle');
            const summaryHost = document.getElementById('globalWalkieTimelineSummary');
            const bodyHost = document.getElementById('globalWalkieTimelineBody');
            if (!modal || !title || !subtitle || !summaryHost || !bodyHost) return;

            title.textContent = 'Loading...';
            subtitle.textContent = '-';
            summaryHost.innerHTML = '';
            bodyHost.innerHTML = '<div class="global-walkie-history-row"><div class="global-walkie-history-detail">Loading history...</div></div>';
            modal.classList.add('active');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';

            try {
                const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
                if (!response.ok) throw new Error('Unable to load walkie details.');
                const timeline = await response.json();
                const summary = timeline.summary || {};
                const events = Array.isArray(timeline.events) ? timeline.events : [];

                title.textContent = summary.radio_id || '-';
                subtitle.textContent = `${summary.model || '-'} / ${summary.serial_number || '-'} / ${summary.status || 'UNKNOWN'}`;

                const summaryItems = [
                    ['Walkie ID', summary.walkie_id || '-'],
                    ['Radio ID', summary.radio_id || '-'],
                    ['Serial No.', summary.serial_number || '-'],
                    ['Model', summary.model || '-'],
                    ['Status', summary.status || '-'],
                    ['Ownership Type', summary.ownership_type || '-'],
                    ['Current Ownership', summary.ownership || '-'],
                    ['Shared With', summary.shared_with || '-'],
                    ['Position', summary.position || '-'],
                    ['Department', summary.department || '-'],
                    ['Received Date', summary.received_date || '-'],
                    ['Repair Date', summary.repair_date || '-'],
                    ['Temporary Radio ID', summary.temporary_radio_id || '-'],
                    ['Tracking Ref', summary.tracking_ref || '-'],
                    ['Need Change ID', summary.need_to_change_id || '-'],
                    ['Change Done', summary.id_change_done || '-'],
                    ['Ownership Type To Be', summary.ownership_type_to_be || '-'],
                    ['Special Use', summary.is_special_use || '-'],
                    ['Returned', summary.special_use_returned || '-'],
                    ['Remarks', summary.remark || '-'],
                ];

                summaryHost.innerHTML = summaryItems.map(([label, value]) => `
                    <div class="global-walkie-summary-item">
                        <div class="global-walkie-summary-label">${globalTimelineEscape(label)}</div>
                        <div class="global-walkie-summary-value">${globalTimelineEscape(value)}</div>
                    </div>
                `).join('');

                bodyHost.innerHTML = events.length
                    ? events.map((event) => `
                        <div class="global-walkie-history-row">
                            <div class="global-walkie-history-date">
                                ${globalTimelineEscape(event.date || '-')}
                                <span class="global-walkie-history-time">${globalTimelineEscape(event.time || '')}</span>
                            </div>
                            <div>
                                <p class="global-walkie-history-title">${globalTimelineEscape(event.title || 'Activity')}</p>
                                <p class="global-walkie-history-detail">${globalTimelineEscape(event.detail || '-')}</p>
                            </div>
                        </div>
                    `).join('')
                    : '<div class="global-walkie-history-row"><div class="global-walkie-history-detail">No history records found for this unit yet.</div></div>';
            } catch (error) {
                title.textContent = 'Unable to load details';
                bodyHost.innerHTML = `<div class="global-walkie-history-row"><div class="global-walkie-history-detail">${globalTimelineEscape(error.message || 'Unable to load walkie details.')}</div></div>`;
            }
        }

        function closeGlobalWalkieTimeline() {
            const modal = document.getElementById('globalWalkieTimelineModal');
            if (!modal) return;
            modal.classList.remove('active');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        function closeGlobalWalkieTimelineOutside(event) {
            if (event.target === document.getElementById('globalWalkieTimelineModal')) {
                closeGlobalWalkieTimeline();
            }
        }
    </script>

    @stack('scripts')
</body>
</html>

