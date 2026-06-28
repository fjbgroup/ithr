<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WT System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
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
            --corp-brown: #075985;
            --corp-gold: #38bdf8;
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
            font-size: 12px;
            background:
                radial-gradient(circle at top right, rgba(59, 130, 246, 0.08), transparent 24%),
                radial-gradient(circle at bottom left, rgba(148, 163, 184, 0.14), transparent 28%),
                linear-gradient(180deg, #F4F7FB 0%, #EDF2F7 100%);
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

        .sidebar-active {
            background: #075985;
            color: white !important;
            border-radius: 4px;
            box-shadow: none;
        }

        .fade-out { opacity: 0; transition: opacity 0.4s ease-in-out; }

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
            border: 1px solid rgba(14, 165, 233, 0.2);
            background:
                radial-gradient(circle at top right, rgba(14, 165, 233, 0.16), transparent 36%),
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
            border-color: rgba(56, 189, 248, 0.18);
            background:
                radial-gradient(circle at top right, rgba(14, 165, 233, 0.16), transparent 36%),
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
            background: linear-gradient(135deg, #0284c7, #075985);
            color: #fff;
            font-size: 24px;
            box-shadow: 0 16px 32px rgba(14, 165, 233, 0.22);
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

        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #FDFBF7; }
        .dark ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #64748B; border-radius: 10px; }
        .dark ::-webkit-scrollbar-thumb { background: #334155; }

        .nav-link {
            font-size: 10px;
            transition: all 0.2s;
            border: 1px solid transparent;
            color: #CBD5E1;
            padding-top: 0.4rem !important;
            padding-bottom: 0.4rem !important;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.06);
            color: #FFFFFF;
            border-color: rgba(255,255,255,0.06);
        }

        .brand-company {
            white-space: nowrap;
            font-size: 8px !important;
            letter-spacing: 0.14em !important;
        }

        .nav-link.has-info {
            position: relative;
            overflow: visible;
            padding-right: 56px !important;
        }

        .nav-link.has-info > span:not(.nav-info-slot) {
            min-width: 0;
            padding-right: 8px;
        }

        .nav-info-slot {
            position: absolute;
            right: 14px;
            top: 50%;
            margin-top: -7.5px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
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

        .content-surface {
            background: rgba(255,255,255,1);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(226, 232, 240, 0.95);
            box-shadow: 0 24px 54px rgba(15, 23, 42, 0.08);
            transition: all 0.3s;
        }
        
        .dark .content-surface {
            background: #1e293b !important;
            border-color: #334155 !important;
            box-shadow: 0 24px 54px rgba(0, 0, 0, 0.4);
        }

        .dark div.bg-white, 
        .dark div.bg-stone-50\/50,
        .dark section.bg-white {
            background-color: #1e293b !important;
            border-color: #334155 !important;
        }

        .dark .bg-stone-50\/50 { background-color: #0f172a / 50% !important; }
        .dark .border-stone-100, .dark .border-stone-50 { border-color: #334155 !important; }
        
        .dark table thead.bg-white, .dark table thead.bg-stone-50 {
            background-color: #0f172a !important;
            color: #94a3b8 !important;
        }

        .dark table tbody tr:hover {
            background-color: rgba(255,255,255,0.03) !important;
        }

        .dark .text-stone-800, .dark .text-stone-700, .dark .text-[#3D2B1F], .dark .text-slate-800 {
            color: #f1f5f9 !important;
        }

        .dark .text-stone-600, .dark .text-stone-500, .dark .text-slate-600 {
            color: #94a3b8 !important;
        }

        .dark .text-stone-400 { color: #64748b !important; }
        
        /* Placeholder Styling */
        input::placeholder, 
        textarea::placeholder {
            color: rgba(100, 116, 139, 0.6) !important; /* slate-500 equivalent */
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
            color: rgba(148, 163, 184, 0.8) !important; /* slate-400 equivalent */
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

        .page-chip {
            border: 1px solid rgba(51, 65, 85, 0.12);
            background: rgba(31, 41, 55, 0.05);
            color: var(--corp-navy);
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 0.18em;
            text-transform: uppercase;
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

        #mobileSidebarOverlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 40;
            backdrop-filter: blur(2px);
        }
        #mobileSidebarOverlay.active { display: block; }

        #mobileSidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 280px;
            z-index: 50;
            transform: translateX(-100%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #mobileSidebar.active { transform: translateX(0); }

        #hamburgerBtn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: rgba(31, 41, 55, 0.06);
            border: 1px solid rgba(31, 41, 55, 0.08);
            cursor: pointer;
            transition: all 0.15s;
        }
        #hamburgerBtn:hover { background: rgba(31, 41, 55, 0.10); }

        @media (min-width: 1024px) {
            #hamburgerBtn { display: none; }
            #mobileSidebarOverlay, #mobileSidebar { display: none !important; }
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
            font-size: 12px;
            font-weight: 800;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            transition: color 0.3s;
        }
        
        .dark .modal-title {
            color: #f1f5f9;
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

        .modal-body {
            padding: 24px;
            overflow-y: auto;
            flex: 1;
            transition: background-color 0.3s;
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
            padding: 8px 12px;
            font-size: 11px;
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
                grid-template-columns: repeat(3, minmax(0, 1fr));
                width: 100%;
                margin-top: 0;
                gap: 4px;
            }
            .mobile-role-switcher a {
                justify-content: center;
                min-height: 38px;
                padding-left: 6px;
                padding-right: 6px;
                font-size: 8px !important;
                letter-spacing: 0.08em;
            }
        }

        /* Unified role theme: ICT and Executive */
        .sidebar-shell {
            background: #1e293b !important;
            border-right: 1px solid rgba(255,255,255,0.05) !important;
        }

        .nav-link,
        .sidebar-link,
        .dropdown-trigger.sidebar-link,
        .sub-nav-link {
            min-height: 34px !important;
            border-radius: 4px !important;
            color: #cbd5e1 !important;
            background: transparent !important;
            border: 1px solid transparent !important;
            box-shadow: none !important;
            font-size: 10px !important;
            font-weight: 600 !important;
        }

        .nav-link:hover,
        .sidebar-link:hover,
        .dropdown-trigger.sidebar-link:hover,
        .sub-nav-link:hover {
            color: #f8fafc !important;
            background: rgba(56, 189, 248, 0.08) !important;
            border-color: rgba(56, 189, 248, 0.12) !important;
        }

        .sidebar-active,
        .active-sidebar,
        .nav-link.sidebar-active,
        .sidebar-link.active-sidebar,
        .sub-nav-link.active,
        .mobile-role-switcher a.bg-\[\#B38A5A\],
        a.bg-\[\#B38A5A\],
        .bg-\[\#B38A5A\] {
            background: #075985 !important;
            background-image: none !important;
            color: #f8fafc !important;
            border-color: rgba(56, 189, 248, 0.34) !important;
            border-radius: 4px !important;
            box-shadow: none !important;
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

        .modal-form-input:focus,
        .content-surface input:focus,
        .content-surface select:focus,
        .content-surface textarea:focus {
            border-color: #38bdf8 !important;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.14) !important;
        }

        .topbar-shell {
            min-height: 58px;
            background: rgba(255,255,255,0.9) !important;
            border-bottom: 1px solid rgba(226, 232, 240, 0.95) !important;
        }

        .dark .topbar-shell {
            background: rgba(15, 23, 42, 0.9) !important;
            border-color: rgba(51, 65, 85, 0.7) !important;
        }

        .page-chip {
            border-radius: 999px !important;
            border-color: #e2e8f0 !important;
            background: #f8fafc !important;
            color: #334155 !important;
            font-size: 10px !important;
            font-weight: 900 !important;
            letter-spacing: 0.14em !important;
            padding: 0.35rem 0.9rem !important;
        }

        .dark .page-chip {
            border-color: #334155 !important;
            background: #1e293b !important;
            color: #e2e8f0 !important;
        }

        main > nav,
        main > .content-surface {
            max-width: 1500px !important;
            width: 100% !important;
        }

        .content-surface {
            background: rgba(255,255,255,0.98) !important;
            border: 1px solid rgba(226, 232, 240, 0.95) !important;
            border-radius: 24px !important;
            box-shadow: 0 24px 54px rgba(15, 23, 42, 0.08) !important;
        }

        .dark .content-surface {
            background: #0f172a !important;
            border-color: #334155 !important;
            box-shadow: none !important;
        }

        .dataTables_wrapper,
        .dataTables_wrapper label,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            color: #64748b !important;
            font-size: 10px !important;
        }

        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select,
        .content-surface input,
        .content-surface select,
        .content-surface textarea {
            min-height: 28px !important;
            border-radius: 6px !important;
            border-color: #cbd5e1 !important;
            background: #ffffff !important;
            color: #334155 !important;
            font-size: 10px !important;
        }

        .dark .dataTables_wrapper .dataTables_filter input,
        .dark .dataTables_wrapper .dataTables_length select,
        .dark .content-surface input,
        .dark .content-surface select,
        .dark .content-surface textarea {
            border-color: #334155 !important;
            background: #0f172a !important;
            color: #f1f5f9 !important;
        }

        table.dataTable thead th {
            background: #f8fafc !important;
            border-color: #e2e8f0 !important;
            color: #64748b !important;
            font-size: 9px !important;
            letter-spacing: 0.1em !important;
        }

        table.dataTable tbody td {
            color: #334155 !important;
            border-color: #f1f5f9 !important;
            font-size: 10px !important;
        }

        .dark table.dataTable thead th {
            background: #1e293b !important;
            border-color: #334155 !important;
            color: #94a3b8 !important;
        }

        .dark table.dataTable tbody td {
            color: #e2e8f0 !important;
            border-color: #334155 !important;
        }

        .wt-btn,
        .btn-submit,
        .btn-cancel,
        .navy-btn,
        .navy-btn-soft,
        .navy-btn-danger {
            min-height: 28px !important;
            padding: 5px 10px !important;
            border-radius: 6px !important;
            font-size: 9px !important;
            letter-spacing: 0.05em !important;
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

        .mb-6,
        .mb-8 {
            margin-bottom: 0.85rem !important;
        }

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

    </style>
    @stack('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/wtsystem.css') }}?v={{ filemtime(public_path('assets/css/wtsystem.css')) }}">
</head>
<body class="transition-opacity duration-500 overflow-hidden" id="main-body">
    @php
        $actualRole = Auth::guard('wt')->user()->role;
        $effectiveRole = $actualRole === 'admin_it'
            ? session('view_mode', $actualRole)
            : $actualRole;
        $effectiveRoleLabel = match ($effectiveRole) {
            'admin' => 'EXECUTIVE',
            default => strtoupper(str_replace('_', ' ', $effectiveRole)),
        };
        $accountRoleLabel = $actualRole === 'admin_it'
            ? 'ICT'
            : $effectiveRoleLabel;
        $headerUnreadNotifications = Auth::guard('wt')->user()->unreadNotifications()->count();
        $headerNotifications = Auth::guard('wt')->user()->notifications()->latest()->take(8)->get();
        $resolveNotificationUrl = function ($notification) {
            $storedUrl = $notification->data['url'] ?? null;
            if (is_string($storedUrl) && $storedUrl !== '') {
                return $storedUrl;
            }

            $title = strtolower((string) ($notification->data['title'] ?? ''));
            $message = strtolower((string) ($notification->data['message'] ?? ''));
            $category = strtolower((string) ($notification->data['category'] ?? ''));
            $text = trim($title . ' ' . $message . ' ' . $category);

            if (str_contains($text, 'damage report') || str_contains($text, 'faulty') || str_contains($text, 'kerosakan')) {
                return route('wt.user.damages.status', ['bucket' => in_array($category, ['approved', 'completed'], true) ? 'completed' : 'pending']);
            }

            if (str_contains($text, 'pickup') || str_contains($text, 'ready to collect') || str_contains($text, 'handover')) {
                return route('wt.user.handover.index');
            }

            return route('wt.user.requests.status');
        };
    @endphp

    <div id="mobileSidebarOverlay" onclick="closeMobileSidebar()"></div>

    <div id="mobileSidebar" class="sidebar-shell text-stone-300 flex flex-col shadow-2xl lg:hidden">
        <div class="p-5 flex flex-row items-center gap-3 border-b border-white/10">
            <div class="bg-white/5 p-3 rounded-2xl border border-white/10">
                <img src="{{ asset('assets/images/fjb-logo.svg') }}" alt="FJB" class="sidebar-brand-logo">
            </div>
            <h2 class="text-[11px] font-black text-white leading-tight tracking-[0.24em] uppercase">WT System</h2>
        </div>

        <nav class="flex-1 px-4 py-5 space-y-1.5 overflow-y-auto">
            <p class="text-[10px] font-bold text-slate-500 uppercase px-4 mb-2 tracking-[0.18em]">Asset Interactive</p>
            <a href="{{ route('wt.user.returns.create') }}" class="nav-link has-info flex items-center gap-3 py-3 px-4 {{ request()->routeIs('user.returns.create') ? 'sidebar-active' : '' }} rounded-xl font-medium" onclick="closeMobileSidebar()">
                <i class="fa-solid fa-rotate-left w-4 text-center"></i> <span>Return Unit</span>
                @include('wt.partials.sidebar-info', ['text' => 'Submit a return request when a walkie unit is no longer being used.'])
            </a>
            <a href="{{ route('wt.user.damages.create') }}" class="nav-link has-info flex items-center gap-3 py-3 px-4 {{ request()->routeIs('user.damages.*') ? 'sidebar-active' : '' }} rounded-xl font-medium" onclick="closeMobileSidebar()">
                <i class="fa-solid fa-triangle-exclamation w-4 text-center"></i> <span>Report Faulty</span>
                @include('wt.partials.sidebar-info', ['text' => 'Report faulty, damaged, missing, or problem walkie talkie units.'])
            </a>
            <a href="{{ route('wt.user.requests.status') }}" class="nav-link has-info flex items-center gap-3 py-3 px-4 {{ request()->routeIs('user.requests.status') ? 'sidebar-active' : '' }} rounded-xl font-medium" onclick="closeMobileSidebar()">
                <i class="fa-solid fa-list-ul w-4 text-center"></i> <span>Request Status</span>
                @include('wt.partials.sidebar-info', ['text' => 'Check the latest status of your walkie talkie requests.'])
            </a>


            <div class="pt-5 mt-5 border-t border-white/10">
                <p class="text-[10px] font-bold text-slate-500 uppercase px-4 mb-2 tracking-[0.18em]">My Account</p>
                <a href="{{ route('wt.user.profile') }}" class="nav-link has-info flex items-center gap-3 py-3 px-4 {{ request()->routeIs('user.profile') ? 'sidebar-active' : '' }} rounded-xl font-medium" onclick="closeMobileSidebar()">
                    <i class="fa-solid fa-user-circle w-4 text-center"></i> <span>My Profile</span>
                    @include('wt.partials.sidebar-info', ['text' => 'View and update your account profile information.'])
                </a>
                <a href="{{ route('wt.user.policies') }}" class="nav-link has-info flex items-center gap-3 py-3 px-4 {{ request()->routeIs('user.policies') ? 'sidebar-active' : '' }} rounded-xl font-medium" onclick="closeMobileSidebar()">
                    <i class="fa-solid fa-file-contract w-4 text-center"></i> <span>Policies</span>
                    @include('wt.partials.sidebar-info', ['text' => 'Read the rules and guidelines for using company walkie talkies.'])
                </a>
            </div>
        </nav>

        <div class="p-4 border-t border-white/10">
            <div class="flex items-center gap-3 bg-white/5 p-3 rounded-2xl border border-white/10">
                <div class="w-9 h-9 bg-gradient-to-br from-[#B38A5A] to-[#8D6742] rounded-xl flex items-center justify-center font-bold text-white text-xs shadow-lg">
                    {{ strtoupper(substr(Auth::guard('wt')->user()->username ?? 'U', 0, 1)) }}
                </div>
                <div class="overflow-hidden">
                    <p class="text-[11px] font-bold text-white truncate leading-none mb-1">{{ Auth::guard('wt')->user()->username ?? 'User' }}</p>
                    <p class="text-[9px] font-black text-slate-200 uppercase tracking-[0.18em]">{{ $accountRoleLabel }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="h-screen flex">
        <div class="sidebar-shell w-56 text-stone-300 flex flex-col hidden lg:flex shrink-0 border-r border-white/10">
            <div class="p-5 flex flex-row items-center gap-3 border-b border-white/10">
                <div class="bg-white/5 p-2 rounded-xl border border-white/10">
                    <img src="{{ asset('assets/images/fjb-logo.svg') }}" alt="FJB" class="sidebar-brand-logo">
                </div>
                <div>
                    <h2 class="text-[11px] font-black text-white leading-tight tracking-[0.24em] uppercase">WT System</h2>
                </div>
            </div>

            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                <p class="text-[10px] font-bold text-slate-500 uppercase px-4 mb-2 mt-2 tracking-[0.18em]">Asset Interactive</p>
                <a href="{{ route('wt.user.returns.create') }}" class="nav-link has-info flex items-center gap-3 py-3 px-4 {{ request()->routeIs('user.returns.create') ? 'sidebar-active' : '' }} rounded-xl font-medium">
                    <i class="fa-solid fa-rotate-left w-4 text-center"></i> <span>Return Unit</span>
                    @include('wt.partials.sidebar-info', ['text' => 'Submit a return request when a walkie unit is no longer being used.'])
                </a>
                <a href="{{ route('wt.user.damages.create') }}" class="nav-link has-info flex items-center gap-3 py-3 px-4 {{ request()->routeIs('user.damages.*') ? 'sidebar-active' : '' }} rounded-xl font-medium">
                    <i class="fa-solid fa-triangle-exclamation w-4 text-center"></i> <span>Report Faulty</span>
                    @include('wt.partials.sidebar-info', ['text' => 'Report faulty, damaged, missing, or problem walkie talkie units.'])
                </a>
                <a href="{{ route('wt.user.requests.status') }}" class="nav-link has-info flex items-center gap-3 py-3 px-4 {{ request()->routeIs('user.requests.status') ? 'sidebar-active' : '' }} rounded-xl font-medium">
                    <i class="fa-solid fa-list-ul w-4 text-center"></i> <span>Request Status</span>
                    @include('wt.partials.sidebar-info', ['text' => 'Check the latest status of your walkie talkie requests.'])
                </a>


                <p class="text-[10px] font-bold text-slate-500 uppercase px-4 mb-2 mt-6 tracking-[0.18em]">My Account</p>
                <a href="{{ route('wt.user.profile') }}" class="nav-link has-info flex items-center gap-3 py-3 px-4 {{ request()->routeIs('user.profile') ? 'sidebar-active' : '' }} rounded-xl font-medium">
                    <i class="fa-solid fa-user-circle w-4 text-center"></i> <span>My Profile</span>
                    @include('wt.partials.sidebar-info', ['text' => 'View and update your account profile information.'])
                </a>
                <a href="{{ route('wt.user.policies') }}" class="nav-link has-info flex items-center gap-3 py-3 px-4 {{ request()->routeIs('user.policies') ? 'sidebar-active' : '' }} rounded-xl font-medium">
                    <i class="fa-solid fa-file-contract w-4 text-center"></i> <span>Policies</span>
                    @include('wt.partials.sidebar-info', ['text' => 'Read the rules and guidelines for using company walkie talkies.'])
                </a>
            </nav>

            <div class="p-4 border-t border-white/10">
                <div class="flex items-center gap-3 bg-white/5 p-3 rounded-2xl border border-white/10">
                    <div class="w-9 h-9 bg-gradient-to-br from-[#B38A5A] to-[#8D6742] rounded-xl flex items-center justify-center font-bold text-white text-xs shadow-lg">
                        {{ strtoupper(substr(Auth::guard('wt')->user()->username ?? 'U', 0, 1)) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-[11px] font-bold text-white truncate leading-none mb-1">{{ Auth::guard('wt')->user()->username ?? 'User' }}</p>
                        <p class="text-[9px] font-black text-slate-200 uppercase tracking-[0.18em]">{{ $accountRoleLabel }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex-1 flex flex-col h-screen overflow-hidden">
            <header class="topbar-shell px-4 md:px-8 py-3 sticky top-0 z-10 shrink-0">
                <div class="flex items-start justify-between gap-3 md:items-center">
                <div class="flex items-center gap-3 min-w-0">
                    <button id="hamburgerBtn" onclick="openMobileSidebar()" class="lg:hidden" title="Open navigation menu" aria-label="Open navigation menu">
                        <i class="fas fa-bars text-slate-600 text-sm"></i>
                    </button>
                    <div class="min-w-0">
                    <div class="page-chip inline-flex items-center rounded-full px-3 py-1">@yield('title', 'User Dashboard')</div>
                    </div>
                </div>
                <div class="flex items-center gap-2 md:gap-4 mobile-topbar-actions">
                    {{-- Interface Switcher (ICT Only) --}}
                    

                    @if($actualRole === 'admin_it')
                    <div class="hidden md:flex items-center bg-stone-100 dark:bg-slate-800/50 p-1 rounded-xl border border-stone-200 dark:border-slate-700 shadow-inner">
                        <a href="{{ route('switch_view', 'admin_it') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-lg {{ $effectiveRole === 'admin_it' ? 'bg-[#B38A5A] text-white shadow-sm' : 'text-stone-500 hover:text-stone-700 dark:text-slate-400 dark:hover:text-slate-200' }} font-bold text-[9px] uppercase tracking-widest transition">
                            <i class="fas fa-user-shield text-[10px]"></i> ICT
                        </a>
                        <a href="{{ route('switch_view', 'admin') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-lg {{ $effectiveRole === 'admin' ? 'bg-[#B38A5A] text-white shadow-sm' : 'text-stone-500 hover:text-stone-700 dark:text-slate-400 dark:hover:text-slate-200' }} font-bold text-[9px] uppercase tracking-widest transition">
                            <i class="fas fa-user-tie text-[10px]"></i> Executive
                        </a>
                    </div>
                    @endif

                    <div class="relative">
                        <button id="notificationToggle" type="button" class="relative text-slate-500 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 rounded-lg p-2.5 transition" title="Open notifications" aria-label="Open notifications">
                            <i class="fas fa-bell text-base"></i>
                            @if($headerUnreadNotifications > 0)
                            <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-red-500 text-white text-[9px] font-black flex items-center justify-center">{{ $headerUnreadNotifications > 99 ? '99+' : $headerUnreadNotifications }}</span>
                            @endif
                        </button>
                        <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-[320px] max-w-[86vw] rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-2xl z-50 overflow-hidden">
                            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 dark:border-slate-700">
                                <p class="text-[10px] font-black uppercase tracking-[0.14em] text-slate-600 dark:text-slate-200">Notifications</p>
                                @if($headerUnreadNotifications > 0)
                                <form action="{{ route('notifications.read_all') }}" method="POST">
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
                                <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700/70 {{ is_null($notification->read_at) ? 'bg-amber-50/40 dark:bg-amber-900/10' : 'bg-transparent' }}">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="text-[10px] font-black uppercase tracking-[0.08em] text-slate-700 dark:text-slate-100">{{ $notification->data['title'] ?? 'Notification' }}</p>
                                            <p class="mt-1 text-[10px] leading-5 text-slate-500 dark:text-slate-300">{{ $notification->data['message'] ?? '' }}</p>
                                            <p class="mt-1 text-[9px] font-semibold text-slate-400 dark:text-slate-500">{{ $notification->created_at?->diffForHumans() }}</p>
                                        </div>
                                        @if(is_null($notification->read_at))
                                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="redirect_url" value="{{ $notificationUrl }}">
                                            <button class="text-[9px] font-bold uppercase tracking-[0.08em] text-[#8D6742] hover:text-[#B38A5A]">Read</button>
                                        </form>
                                        @else
                                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="redirect_url" value="{{ $notificationUrl }}">
                                            <button class="text-[9px] font-bold uppercase tracking-[0.08em] text-[#8D6742] hover:text-[#B38A5A]">Open</button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                                @empty
                                <div class="px-4 py-10 text-center text-[10px] font-semibold uppercase tracking-[0.1em] text-slate-400 dark:text-slate-500">
                                    No notifications yet.
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <button id="theme-toggle" type="button" class="text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700/50 focus:outline-none rounded-lg text-sm p-2.5 transition" title="Toggle dark mode" aria-label="Toggle dark mode">
                        <i id="theme-toggle-dark-icon" class="hidden fas fa-moon text-base"></i>
                        <i id="theme-toggle-light-icon" class="hidden fas fa-sun text-base text-yellow-500"></i>
                    </button>

                    <form id="logout-form" action="{{ route('wt.logout') }}" method="POST" class="hidden">@csrf</form>
                    <button onclick="handleLogout()" class="inline-flex items-center gap-2 rounded-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-1.5 text-slate-500 dark:text-slate-300 hover:text-red-600 font-bold text-[10px] tracking-[0.14em] uppercase transition shadow-sm" title="Sign out" aria-label="Sign out">
                        <span class="mobile-hide-label">Sign Out</span><i class="fas fa-sign-out-alt"></i>
                    </button>
                </div>
                </div>
                @if($actualRole === 'admin_it')
                <div class="mobile-role-switcher md:hidden bg-stone-100 dark:bg-slate-800/50 p-1 rounded-xl border border-stone-200 dark:border-slate-700 shadow-inner mt-3">
                    <a href="{{ route('switch_view', 'admin_it') }}" class="flex items-center gap-1 px-2 py-2 rounded-lg {{ $effectiveRole === 'admin_it' ? 'bg-[#B38A5A] text-white shadow-sm' : 'text-stone-500 dark:text-slate-400' }} font-bold text-[9px] uppercase tracking-widest transition">
                        <i class="fas fa-user-shield text-[10px]"></i><span>IT</span>
                    </a>
                    <a href="{{ route('switch_view', 'admin') }}" class="flex items-center gap-1 px-2 py-2 rounded-lg {{ $effectiveRole === 'admin' ? 'bg-[#B38A5A] text-white shadow-sm' : 'text-stone-500 dark:text-slate-400' }} font-bold text-[9px] uppercase tracking-widest transition">
                        <i class="fas fa-user-tie text-[10px]"></i><span>Executive</span>
                    </a>
                </div>
                @endif
            </header>

            <main class="p-4 md:p-6 overflow-y-auto w-full">
                <nav class="max-w-4xl mx-auto mb-3 text-[10px] font-black uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400" aria-label="Breadcrumb">
                    <ol class="flex flex-wrap items-center gap-2">
                        <li>
                            <a href="{{ route('wt.user.dashboard') }}" class="hover:text-[#8D6742] dark:hover:text-[#D1AE7B]">Dashboard</a>
                        </li>
                        <li class="text-slate-300 dark:text-slate-600">/</li>
                        <li class="text-slate-700 dark:text-slate-200">@yield('title', 'User Dashboard')</li>
                    </ol>
                </nav>
                <div class="content-surface max-w-4xl mx-auto rounded-xl md:rounded-[24px] p-4 sm:p-5 md:p-6">
                    @include('wt.partials.flash-alerts')
                    @yield('content')
                </div>
            </main>
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
            document.getElementById('main-body').classList.add('fade-out');
            setTimeout(() => { document.getElementById('logout-form').submit(); }, 250);
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

        function bindAutoUppercase(root = document) {
            const fields = root.querySelectorAll('input[type="text"], input[type="search"], textarea');
            fields.forEach((field) => {
                if (field.name === 'username' || field.id === 'edit_username' || field.dataset.preserveCase === 'true') {
                    return;
                }

                if (field.dataset.uppercaseBound === 'true') {
                    return;
                }

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

        document.addEventListener('DOMContentLoaded', function () {
            const notificationToggle = document.getElementById('notificationToggle');
            const notificationDropdown = document.getElementById('notificationDropdown');
            const logoutModal = document.getElementById('logoutModal');
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

            if (logoutModal) {
                logoutModal.addEventListener('click', function (event) {
                    if (event.target === logoutModal) {
                        closeLogoutModal();
                    }
                });
            }

            bindAutoUppercase();

            document.querySelectorAll('form').forEach((form) => {
                form.addEventListener('submit', function () {
                    bindAutoUppercase(form);
                    form.querySelectorAll('input[type="text"], input[type="search"], textarea').forEach((field) => {
                        if (field.name === 'username' || field.id === 'edit_username' || field.dataset.preserveCase === 'true') {
                            return;
                        }

                        field.value = field.value.toUpperCase();
                    });
                });
            });

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

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeLogoutModal();
                    closeSidebarInfoPopovers();
                    closeMobileSidebar();
                }
            });

            window.addEventListener('resize', closeSidebarInfoPopovers);
            document.addEventListener('scroll', closeSidebarInfoPopovers, true);
        });
    </script>
    @include('wt.partials.assistant-chatbox', ['assistantRole' => $effectiveRole])
    @include('wt.partials.form-option-datalists')
    @include('wt.partials.phone-format-script')
    @stack('scripts')
</body>
</html>


