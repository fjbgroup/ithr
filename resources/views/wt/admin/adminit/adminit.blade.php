@extends('wt.layouts.admin')

@section('title', 'ICT')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .ict-users-control .bg-white {
        background: var(--surface) !important;
        border-color: var(--border) !important;
    }
    .ict-users-control .bg-stone-50\/70,
    .ict-users-control .bg-stone-50 {
        background: var(--soft-surface) !important;
        border-color: var(--border) !important;
    }
    .ict-users-control .page-title-standard,
    .ict-users-control .text-slate-700,
    .ict-users-control .text-stone-700,
    .ict-users-control .text-\[\#142b47\] {
        color: var(--text) !important;
    }
    .ict-users-control .page-subtitle-standard,
    .ict-users-control .text-slate-600,
    .ict-users-control .text-stone-600,
    .ict-users-control .text-slate-500,
    .ict-users-control .text-stone-500,
    .ict-users-control .text-slate-400,
    .ict-users-control .text-stone-400 {
        color: var(--muted) !important;
    }
    .ict-users-control .text-\[\#0284c7\] {
        color: var(--accent) !important;
    }
    .ict-users-control table.dataTable thead th,
    .ict-users-control .dataTables_wrapper table.dataTable thead th {
        color: var(--table-head-color) !important;
        background: var(--table-head-bg) !important;
        border-color: var(--border) !important;
    }
    .ict-users-control table.dataTable tbody td,
    .ict-users-control .dataTables_wrapper table.dataTable tbody td {
        color: var(--text) !important;
        background: var(--row-surface) !important;
        border-color: var(--border) !important;
    }
    .ict-users-control table.dataTable tbody tr:hover td {
        background: var(--row-alt) !important;
    }
    .ict-users-control .dataTables_wrapper .dataTables_filter label,
    .ict-users-control .dataTables_wrapper .dataTables_length label,
    .ict-users-control .adminit-table-info {
        color: var(--muted) !important;
    }
    .ict-users-control .dataTables_wrapper .dataTables_filter input,
    .ict-users-control .dataTables_wrapper .dataTables_length select {
        background: var(--form-input-bg) !important;
        border-color: var(--form-input-border) !important;
        color: var(--form-input-color) !important;
    }
    .adminit-reset-table table.dataTable,
    .adminit-reset-table .dataTables_wrapper {
        width: 100% !important;
    }
    .adminit-staff-table table.dataTable,
    .adminit-staff-table .dataTables_wrapper {
        width: 100% !important;
    }
    .adminit-reset-table,
    .adminit-staff-table {
        width: min(100%, 1380px);
        margin-left: auto;
        margin-right: auto;
    }
    body .content-surface .ict-users-control .adminit-reset-table > .adminit-section-header,
    body .content-surface .ict-users-control .adminit-staff-table > .adminit-section-header {
        padding: 22px 34px !important;
        border-bottom: 1px solid var(--border) !important;
    }
    .adminit-reset-table .overflow-x-auto,
    .adminit-staff-table .overflow-x-auto {
        padding: 22px 34px 28px;
        background: var(--surface) !important;
    }
    .adminit-reset-table .dataTables_wrapper,
    .adminit-staff-table .dataTables_wrapper {
        padding: 0;
    }
    @media (max-width: 768px) {
        .adminit-reset-table,
        .adminit-staff-table {
            width: 100%;
        }
        .adminit-reset-table .overflow-x-auto,
        .adminit-staff-table .overflow-x-auto {
            padding: 16px 18px 22px;
        }
        body .content-surface .ict-users-control .adminit-reset-table > .adminit-section-header,
        body .content-surface .ict-users-control .adminit-staff-table > .adminit-section-header {
            padding: 18px 20px !important;
        }
    }
    .adminit-staff-table table.dataTable {
        table-layout: auto;
    }
    .adminit-staff-table table.dataTable thead th,
    .adminit-staff-table table.dataTable tbody td {
        white-space: nowrap;
    }
    .adminit-section-header {
        background: var(--soft-surface) !important;
        border-color: var(--border) !important;
    }
    html.dark .adminit-section-header {
        background: linear-gradient(135deg, #33445f 0%, #28384f 100%) !important;
        border-color: rgba(129, 151, 181, 0.34) !important;
    }
    .adminit-section-heading {
        color: var(--text) !important;
        font-size: 13px;
        font-weight: 900;
        letter-spacing: 0.18em;
        line-height: 1.15;
        text-transform: uppercase;
    }
    html.dark .adminit-section-heading { color: #f8fafc !important; text-shadow: 0 1px 2px rgba(2, 6, 23, 0.55); }
    .adminit-section-copy {
        color: var(--muted) !important;
        font-size: 11px;
        line-height: 1.35;
    }
    .adminit-action-menu {
        position: fixed;
        z-index: 10000;
        width: 208px;
        background: #f8fafc !important;
        border-color: #cbd5e1 !important;
        box-shadow: 0 18px 40px rgba(2, 6, 23, 0.28) !important;
    }
    .adminit-action-menu .adminit-action-item {
        color: #1e293b !important;
        background: #f8fafc !important;
    }
    .adminit-action-menu .adminit-action-item i {
        color: #334155 !important;
    }
    .adminit-action-menu .adminit-action-item:hover {
        background: #eaf0f8 !important;
        color: #0f172a !important;
    }
    .adminit-action-menu .adminit-action-danger {
        color: #dc2626 !important;
    }
    .adminit-action-menu .adminit-action-danger i {
        color: #dc2626 !important;
    }
    .adminit-action-menu .adminit-action-danger:hover {
        background: #fef2f2 !important;
        color: #b91c1c !important;
    }
    .adminit-reset-table table.dataTable {
        table-layout: fixed;
    }
    .adminit-reset-table table.dataTable tbody td.dataTables_empty {
        padding: 0 !important;
        background: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
        text-align: center !important;
    }
    .adminit-empty-state {
        display: flex;
        min-height: 136px;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 10px;
        color: #94a3b8;
        text-align: center;
    }
    .adminit-empty-state .empty-visual {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        border-radius: 14px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #94a3b8;
    }
    .adminit-empty-state .empty-title {
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .adminit-empty-state .empty-copy {
        max-width: 320px;
        font-size: 12px;
        line-height: 1.5;
        color: #94a3b8;
    }
    .navy-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.75rem 1rem;
        border-radius: 0.9rem;
        border: 1px solid rgba(96, 165, 250, 0.28);
        background: rgba(15, 23, 42, 0.96);
        color: #e2e8f0;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.14em;
        box-shadow: 0 0 0 1px rgba(30, 41, 59, 0.42), 0 0 16px rgba(59, 130, 246, 0.12);
        transition: all 0.18s ease;
    }
    .navy-btn:hover {
        background: #162033;
        border-color: rgba(96, 165, 250, 0.44);
        box-shadow: 0 0 0 1px rgba(30, 41, 59, 0.5), 0 0 20px rgba(59, 130, 246, 0.18);
        transform: translateY(-1px);
    }
    .navy-btn-danger {
        background: rgba(69, 10, 10, 0.96);
        color: #fee2e2;
        border-color: rgba(248, 113, 113, 0.28);
        box-shadow: 0 0 0 1px rgba(127, 29, 29, 0.32), 0 0 14px rgba(239, 68, 68, 0.12);
    }
    .navy-btn-danger:hover {
        background: rgba(91, 13, 13, 0.98);
        border-color: rgba(248, 113, 113, 0.42);
    }
    .navy-btn-soft {
        background: #f8fafc;
        color: #475569;
        border-color: #cbd5e1;
        box-shadow: none;
    }
    .navy-btn-soft:hover {
        background: #eef2ff;
        color: #1e293b;
        border-color: #94a3b8;
        box-shadow: none;
    }
    .navy-icon-btn {
        border: 1px solid rgba(96, 165, 250, 0.24);
        background: rgba(15, 23, 42, 0.96);
        color: #e2e8f0;
        box-shadow: 0 0 0 1px rgba(30, 41, 59, 0.42), 0 0 14px rgba(59, 130, 246, 0.12);
    }
    .navy-icon-btn:hover {
        background: #162033;
        color: #f8fafc;
        border-color: rgba(96, 165, 250, 0.4);
    }
    .navy-input {
        background: #f8fafc;
        border-color: #cbd5e1;
    }
    .navy-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
    }
    .quick-icon-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border-radius: 999px;
        border: 1px solid rgba(96, 165, 250, 0.22);
        background: rgba(15, 23, 42, 0.96);
        color: #f8fafc;
        box-shadow: 0 0 0 1px rgba(30, 41, 59, 0.36), 0 0 16px rgba(59, 130, 246, 0.12);
        transition: all 0.18s ease;
    }
    .quick-icon-btn:hover {
        transform: translateY(-1px);
        background: #162033;
        border-color: rgba(96, 165, 250, 0.42);
    }
    .password-eye-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        border-radius: 999px;
        border: 1px solid rgba(148, 163, 184, 0.28);
        background: rgba(15, 23, 42, 0.72);
        color: #dbeafe;
        transition: all 0.18s ease;
    }
    .password-eye-btn:hover {
        background: #0f172a;
        border-color: rgba(96, 165, 250, 0.48);
        color: #ffffff;
    }
    .password-value {
        display: inline-block;
        max-width: 135px;
        overflow: hidden;
        text-overflow: ellipsis;
        vertical-align: middle;
        white-space: nowrap;
    }
    .add-account-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 32px;
        padding: 0 12px;
        border-radius: 10px;
        border: 1px solid rgba(96, 165, 250, 0.32);
        background: #0e7490;
        color: #ffffff;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        box-shadow: 0 12px 24px rgba(14, 116, 144, 0.18);
        transition: all 0.18s ease;
    }
    .add-account-btn:hover {
        background: #0891b2;
        border-color: rgba(125, 211, 252, 0.58);
        transform: translateY(-1px);
    }
    .account-modal-card {
        display: flex;
        flex-direction: column;
        width: min(560px, calc(100vw - 32px));
        max-height: calc(100vh - 112px);
        overflow: hidden;
        border-radius: 16px;
        border: 1px solid var(--border);
        background: var(--surface);
        box-shadow: var(--shadow-lg);
    }
    .account-modal-card > form {
        display: flex;
        min-height: 0;
        flex: 1;
        flex-direction: column;
    }
    #createExecutiveModal {
        top: 56px;
        right: 0;
        bottom: 0;
        left: var(--sidebar-w);
        align-items: center;
        justify-content: center;
        padding: 24px;
        width: auto;
        height: auto;
    }
    html.dark .account-modal-card {
        border-color: rgba(96, 165, 250, 0.28);
        background: #182338;
        box-shadow: 0 28px 70px rgba(2, 6, 23, 0.58);
    }
    .account-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 16px;
        border-bottom: 1px solid var(--border);
        background: var(--soft-surface);
    }
    html.dark .account-modal-header {
        border-bottom-color: rgba(96, 165, 250, 0.18);
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    }
    .account-modal-header .text-white {
        color: var(--text) !important;
    }
    html.dark .account-modal-header .text-white {
        color: #ffffff !important;
    }
    .account-modal-header .text-slate-400 {
        color: var(--muted) !important;
    }
    .account-modal-icon {
        display: inline-flex;
        width: 34px;
        height: 34px;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        border: 1px solid rgba(125, 211, 252, 0.35);
        background: rgba(8, 145, 178, 0.18);
        color: #7dd3fc;
    }
    .account-modal-close {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 999px;
        border: 1px solid rgba(148, 163, 184, 0.2);
        color: var(--muted);
        transition: all 0.18s ease;
    }
    .account-modal-close:hover {
        background: var(--soft-surface);
        color: var(--text);
    }
    html.dark .account-modal-close:hover {
        background: rgba(148, 163, 184, 0.12);
        color: #ffffff;
    }
    .account-modal-body {
        flex: 1;
        min-height: 0;
        max-height: none;
        overflow-y: auto;
        overscroll-behavior: contain;
        padding: 16px;
    }
    .account-field label {
        display: block;
        margin-bottom: 6px;
        color: var(--muted);
        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }
    .account-field input,
    .account-field select,
    .account-field .select2-container--default .select2-selection--single {
        width: 100%;
        min-height: 34px;
        border-radius: 9px;
        border: 1px solid var(--form-input-border);
        background: var(--form-input-bg);
        color: var(--form-input-color);
        padding: 0 11px;
        font-size: 11px;
        font-weight: 700;
        transition: all 0.18s ease;
    }
    .account-field .select2-container {
        width: 100% !important;
    }
    .account-tag-select + .select2-container {
        width: 100% !important;
    }
    .account-tag-select + .select2-container .select2-selection--single {
        min-height: 46px;
        border-radius: 12px;
        border: 1px solid #dbe5f2;
        background: #ffffff;
        padding: 8px 36px 8px 14px;
        display: flex;
        align-items: center;
    }
    .account-tag-select + .select2-container .select2-selection__rendered {
        color: #1e293b !important;
        font-size: 12px;
        font-weight: 900;
        line-height: 1.2 !important;
        padding: 0 !important;
        text-transform: uppercase;
    }
    .account-tag-select + .select2-container .select2-selection__placeholder {
        color: #94a3b8 !important;
    }
    .account-tag-select + .select2-container .select2-selection__arrow {
        height: 100% !important;
        right: 12px !important;
    }
    html.dark .account-tag-select + .select2-container .select2-selection--single {
        border-color: #334155;
        background: #0f172a;
    }
    html.dark .account-tag-select + .select2-container .select2-selection__rendered {
        color: #f8fafc !important;
    }
    .account-field .select2-container--default .select2-selection--single {
        display: flex;
        align-items: center;
        height: 34px;
        padding: 0 30px 0 11px;
    }
    .account-field .select2-container--default .select2-selection__rendered {
        width: 100%;
        color: var(--form-input-color);
        font-size: 11px;
        font-weight: 800;
        line-height: 1.2 !important;
        padding: 0 !important;
        text-transform: uppercase;
    }
    .account-field .select2-container--default .select2-selection__placeholder {
        color: var(--muted);
    }
    .account-field .select2-container--default .select2-selection__arrow {
        height: 100%;
        right: 9px;
    }
    .account-select-dropdown {
        z-index: 10000 !important;
        border: 1px solid var(--border) !important;
        border-radius: 12px !important;
        overflow: hidden;
        box-shadow: 0 18px 42px rgba(15, 23, 42, 0.18);
    }
    .account-select-dropdown .select2-search--dropdown {
        padding: 10px;
    }
    .account-select-dropdown .select2-search__field {
        border: 1px solid var(--form-input-border) !important;
        border-radius: 9px !important;
        padding: 8px 10px !important;
        font-size: 11px !important;
        font-weight: 800 !important;
        text-transform: uppercase;
        outline: none;
    }
    .account-select-dropdown .select2-results__option {
        padding: 10px 12px;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
    }
    .account-field input:focus,
    .account-field select:focus {
        outline: none;
        border-color: #38bdf8;
        box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.12);
    }
    .account-modal-footer {
        display: flex;
        flex-shrink: 0;
        justify-content: flex-end;
        gap: 8px;
        padding: 12px 16px;
        border-top: 1px solid var(--border);
        background: var(--soft-surface);
    }
    .account-modal-card .grid.gap-4 {
        gap: 12px !important;
    }
    .account-modal-card .navy-btn {
        min-height: 36px;
        padding: 0 16px;
        border-radius: 10px;
        font-size: 11px;
    }
    @media (max-width: 900px) {
        #createExecutiveModal {
            top: 56px;
            left: 0;
            padding: 16px;
        }
    }
    html.dark .account-modal-footer {
        border-top-color: rgba(96, 165, 250, 0.18);
        background: #121c2e;
    }
    #viewUserModal {
        top: 56px;
        right: 0;
        bottom: 0;
        left: var(--sidebar-w);
        width: auto;
        height: auto;
        z-index: 90;
        align-items: center;
        justify-content: center;
        padding: 24px;
    }
    .view-account-card {
        display: flex;
        width: min(760px, 100%);
        max-height: calc(100vh - 112px);
        flex-direction: column;
        overflow: hidden;
        border-radius: 18px;
        border: 1px solid var(--border);
        background: var(--surface);
        box-shadow: var(--shadow-lg);
    }
    .view-account-header {
        display: flex;
        flex-shrink: 0;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 16px 20px;
        border-bottom: 1px solid var(--border);
        background: var(--soft-surface);
    }
    .view-account-body {
        min-height: 0;
        overflow-y: auto;
        padding: 18px 20px;
    }
    .view-account-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }
    .view-account-field {
        min-height: 78px;
        border-radius: 12px;
        border: 1px solid var(--form-input-border);
        background: var(--form-input-bg);
        padding: 14px 16px;
    }
    .view-account-field span:first-child {
        display: block;
        margin-bottom: 7px;
        color: var(--muted);
        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }
    .view-account-field span:last-child {
        display: block;
        overflow-wrap: anywhere;
        color: var(--text);
        font-size: 13px;
        font-weight: 900;
        line-height: 1.35;
    }
    .view-account-footer {
        display: flex;
        flex-shrink: 0;
        justify-content: flex-end;
        gap: 10px;
        padding: 14px 20px;
        border-top: 1px solid var(--border);
        background: var(--soft-surface);
    }
    html.dark .view-account-card {
        border-color: rgba(96, 165, 250, 0.28);
        background: #182338;
        box-shadow: 0 28px 70px rgba(2, 6, 23, 0.58);
    }
    html.dark .view-account-header,
    html.dark .view-account-footer {
        border-color: rgba(96, 165, 250, 0.18);
        background: #121c2e;
    }
    @media (max-width: 900px) {
        #viewUserModal {
            top: 56px;
            left: 0;
            padding: 16px;
        }
        .view-account-grid {
            grid-template-columns: 1fr;
        }
    }
    html:not(.dark) #wtStaffSearchInput,
    html[data-theme="light"] #wtStaffSearchInput {
        border-color: #dbe5f2 !important;
        background: #ffffff !important;
        color: #1e293b !important;
        box-shadow: none !important;
    }
    html:not(.dark) #wtStaffSearchInput::placeholder,
    html[data-theme="light"] #wtStaffSearchInput::placeholder {
        color: #94a3b8 !important;
        opacity: 1 !important;
    }
    html:not(.dark) #wtStaffResults,
    html[data-theme="light"] #wtStaffResults {
        border-color: #dbe5f2 !important;
        background: #ffffff !important;
        color: #1e293b !important;
        box-shadow: 0 18px 42px rgba(15, 23, 42, 0.14) !important;
    }
    html:not(.dark) #wtStaffBanner,
    html[data-theme="light"] #wtStaffBanner {
        border-color: #bae6fd !important;
        background: #f0f9ff !important;
    }
    html:not(.dark) #wtStaffBannerLabel,
    html[data-theme="light"] #wtStaffBannerLabel {
        color: #0f172a !important;
    }
    .wt-staff-option {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 16px;
        cursor: pointer;
        border-bottom: 1px solid #e2e8f0;
        transition: background .12s;
    }
    .wt-staff-option:hover {
        background: #f8fafc;
    }
    .wt-staff-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #0ea5e9;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-weight: 900;
        font-size: 14px;
        flex-shrink: 0;
    }
    .wt-staff-name {
        font-size: 12px;
        font-weight: 800;
        color: #1e293b;
    }
    .wt-staff-meta {
        font-size: 10px;
        color: #64748b;
        margin-top: 2px;
    }
    .wt-staff-count {
        padding: 7px 14px;
        font-size: 10px;
        color: #64748b;
        text-align: center;
        border-top: 1px solid #e2e8f0;
    }
    html.dark .wt-staff-option {
        border-bottom-color: rgba(96,165,250,0.15);
    }
    html.dark .wt-staff-option:hover {
        background: rgba(56,189,248,.08);
    }
    html.dark .wt-staff-avatar {
        background: #0e7490;
    }
    html.dark .wt-staff-name {
        color: #f8fafc;
    }
    html.dark .wt-staff-meta,
    html.dark .wt-staff-count {
        color: #94a3b8;
    }
    html.dark .wt-staff-count {
        border-top-color: rgba(96,165,250,0.15);
    }
</style>
@endpush

@section('content')
<div class="space-y-8 ict-users-control">
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl font-bold text-sm">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl font-bold text-sm">
        {{ session('error') }}
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl font-bold text-sm">
        {{ $errors->first() }}
    </div>
    @endif

    <div class="flex flex-col md:flex-row md:items-start justify-between gap-3">
        <div>
            <h2 class="page-title-standard flex items-center gap-2 text-2xl font-black">
                <i class="fas fa-laptop-code text-[#A67B5B]"></i>
                {{ $pageTitle }}
            </h2>
            <p class="text-[10.5px] text-stone-400 mt-1">System account overview for all executives and ICT accounts stored in the database.</p>
        </div>
        <div class="text-right shrink-0">
            <p class="text-[10px] uppercase tracking-[0.2em] text-stone-400 font-black">Last Sync</p>
            <p class="text-sm font-bold text-stone-700">{{ now()->format('d M Y H:i') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-7 rounded-3xl border border-stone-200 shadow-sm transition-all hover:shadow-md">
            <p class="text-[10px] font-bold text-stone-400 uppercase tracking-widest">Total Accounts</p>
            <div class="mt-2 text-3xl font-black text-[#142b47]">{{ $roleCounts['total'] }}</div>
        </div>
        <div class="bg-white p-7 rounded-3xl border border-stone-200 shadow-sm transition-all hover:shadow-md">
            <p class="text-[10px] font-bold text-stone-400 uppercase tracking-widest">ICT</p>
            <div class="mt-2 text-3xl font-black text-[#142b47]">{{ $roleCounts['admin_it'] }}</div>
        </div>
        <div class="bg-white p-7 rounded-3xl border border-stone-200 shadow-sm transition-all hover:shadow-md">
            <p class="text-[10px] font-bold text-stone-400 uppercase tracking-widest">Executive</p>
            <div class="mt-2 text-3xl font-black text-[#142b47]">{{ $roleCounts['admin'] }}</div>
        </div>
    </div>

    <div class="adminit-staff-table bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden">
            <div class="adminit-section-header flex flex-col md:flex-row md:items-center md:justify-between gap-5 p-6 border-b border-stone-200 bg-stone-50/70">
                <div>
                    <h3 class="adminit-section-heading">User Directory</h3>
                    <p class="adminit-section-copy mt-1">All accounts recorded in the database.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">

                    <div id="staffDirectoryExportActions" class="admin-table-export-actions"></div>
                    <button type="button" class="add-account-btn" onclick="openCreateExecutiveModal()">
                        <i class="fas fa-user-plus text-[10px]"></i>
                        <span>Add Account</span>
                    </button>
                    <span class="px-2 py-0.5 rounded-full bg-[#0284c7]/10 text-[#0284c7] text-[9px] font-black tracking-widest">{{ $users->count() }} RECORDS</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="staffDirectoryTable" class="adminit-table w-full text-left display">
                    <thead class="bg-stone-50 text-stone-400 text-[9px] uppercase font-black tracking-[0.15em]">
                        <tr>
                            <th class="px-3 py-3">ID No.</th>
                            <th class="px-3 py-3">Name</th>
                            <th class="px-3 py-3">Role</th>
                            <th class="px-3 py-3">Password</th>
                            <th class="px-3 py-3">Requests</th>
                            <th class="px-3 py-3">Handovers</th>
                            <th class="px-3 py-3">Created</th>
                            <th class="px-3 py-3">Last Activity</th>
                            <th class="px-3 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 text-sm">
                        @foreach($users as $account)
                        @php
                            $roleStyles = match($account->wt_role) {
                                'admin_it' => 'bg-[#142b47] text-white',
                                'admin' => 'bg-amber-100 text-amber-700',
                                default => 'bg-stone-100 text-slate-600',
                            };
                            $roleLabel = match($account->wt_role) {
                                'admin_it' => 'ICT',
                                'admin' => 'EXECUTIVE',
                                default => 'EXECUTIVE',
                            };
                        @endphp
                        <tr class="hover:bg-[#FDFBF7] transition text-[10.5px]">
                            <td class="px-3 py-2.5 font-bold text-slate-700">{{ $account->staff_id ?: '-' }}</td>
                            <td class="px-3 py-2.5">
                                <div class="font-bold text-slate-700">{{ $account->full_name ?: $account->username }}</div>
                            </td>
                            <td class="px-3 py-2.5">
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest {{ $roleStyles }}">
                                    {{ $roleLabel }}
                                </span>
                            </td>
                            <td class="px-3 py-2.5">
                                <div class="flex items-center gap-2">
                                    <code class="password-value text-[9px] font-bold text-stone-400">********</code>
                                    <button
                                        type="button"
                                        class="password-eye-btn"
                                        onclick="togglePasswordVisibility(this)"
                                        title="Show password">
                                        <i class="fas fa-eye text-[10px]"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="px-3 py-2.5 font-bold text-slate-600">{{ $account->request_count ?? 0 }}</td>
                            <td class="px-3 py-2.5 font-bold text-slate-600">{{ $account->handover_count ?? 0 }}</td>
                            <td class="px-3 py-2.5 text-[10px] text-stone-500">
                                {{ $account->created_at ? \Carbon\Carbon::parse($account->created_at)->format('d M Y H:i') : '-' }}
                            </td>
                            <td class="px-3 py-2.5 text-[10px] text-stone-500">
                                {{ $account->last_activity_at ? \Carbon\Carbon::parse($account->last_activity_at)->format('d M Y H:i') : 'No activity' }}
                            </td>
                            <td class="px-3 py-2.5">
                                <div class="relative flex justify-center">
                                    <button
                                        type="button"
                                        class="navy-icon-btn min-w-[74px] h-8 px-3 rounded-full transition text-xs inline-flex items-center justify-center gap-2"
                                        title="More actions"
                                        aria-label="More actions for {{ $account->full_name ?: $account->username }}"
                                        onclick="toggleActionMenu(event, 'action-menu-{{ $account->user_id }}')">
                                        <span class="font-black text-[10px] uppercase tracking-[0.08em]">More</span>
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>

                                    <div id="action-menu-{{ $account->user_id }}" class="adminit-action-menu hidden w-52 rounded-2xl border border-stone-200 bg-white shadow-2xl overflow-hidden">
                                        <button
                                            type="button"
                                            class="adminit-action-item w-full px-4 py-3 text-left text-sm font-bold flex items-center gap-3"
                                            data-staff-id="{{ $account->staff_id ?: '-' }}"
                                            data-username="{{ $account->username ?: '-' }}"
                                            data-full-name="{{ $account->full_name ?: $account->username }}"
                                            data-department="{{ $account->department ?: '-' }}"
                                            data-position="{{ $account->position ?: '-' }}"
                                            data-role="{{ $roleLabel }}"
                                            data-requests="{{ $account->request_count ?? 0 }}"
                                            data-handovers="{{ $account->handover_count ?? 0 }}"
                                            data-created="{{ $account->created_at ? \Carbon\Carbon::parse($account->created_at)->format('d M Y H:i') : '-' }}"
                                            data-last-activity="{{ $account->last_activity_at ? \Carbon\Carbon::parse($account->last_activity_at)->format('d M Y H:i') : 'No activity' }}"
                                            onclick="openViewUserModalFromButton(this); closeAllActionMenus();">
                                            <i class="fas fa-eye w-4"></i>
                                            <span>View</span>
                                        </button>
                                        <button
                                            type="button"
                                            class="adminit-action-item w-full px-4 py-3 text-left text-sm font-bold flex items-center gap-3"
                                            onclick="openEditUserModal('{{ $account->user_id }}', '{{ addslashes($account->staff_id ?? '') }}', '{{ addslashes($account->username ?? '') }}', '{{ addslashes($account->full_name ?? '') }}', '{{ addslashes($account->department ?? '') }}', '{{ addslashes($account->position ?? '') }}', '{{ $account->wt_role }}'); closeAllActionMenus();">
                                            <i class="fas fa-pen w-4"></i>
                                            <span>Edit</span>
                                        </button>
                                        <button
                                            type="button"
                                            class="adminit-action-item w-full px-4 py-3 text-left text-sm font-bold flex items-center gap-3"
                                            onclick="openResetPasswordModal('{{ $account->user_id }}', '{{ addslashes($account->username ?? '') }}'); closeAllActionMenus();">
                                            <i class="fas fa-key w-4"></i>
                                            <span>Reset Password</span>
                                        </button>
                                        <form action="{{ route('wt.admin.users.destroy', $account->user_id) }}" method="POST" onsubmit="return confirm('Delete account {{ addslashes($account->username) }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="adminit-action-item adminit-action-danger w-full px-4 py-3 text-left text-sm font-bold flex items-center gap-3">
                                                <i class="fas fa-trash w-4"></i>
                                                <span>Delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    <div class="adminit-reset-table bg-white rounded-3xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="adminit-section-header flex flex-col md:flex-row md:items-center md:justify-between gap-2 p-5 border-b border-stone-200 bg-stone-50/70">
            <div>
                <h3 class="adminit-section-heading">Pending Forgot Password Requests</h3>
                <p class="adminit-section-copy mt-1">Reset requests that require ICT approval before the password is changed.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <div id="passwordResetExportActions" class="admin-table-export-actions"></div>
                <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-[9px] font-black tracking-widest">{{ $pendingPasswordResetRequests->count() }} PENDING</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table id="passwordResetTable" class="adminit-table w-full text-left display">
                <thead class="bg-stone-50 text-stone-400 text-[9px] uppercase font-black tracking-[0.15em]">
                    <tr>
                        <th class="px-3 py-3">Requested Name</th>
                        <th class="px-3 py-3">ID No.</th>
                        <th class="px-3 py-3">Account</th>
                        <th class="px-3 py-3">Justification</th>
                        <th class="px-3 py-3">Requested At</th>
                        <th class="px-3 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-sm">
                    @foreach($pendingPasswordResetRequests as $resetRequest)
                    <tr class="hover:bg-[#FDFBF7] transition text-[10.5px]">
                        <td class="px-3 py-2.5 font-bold text-slate-700">{{ $resetRequest->requester_name ?: ($resetRequest->user->full_name ?? $resetRequest->user->username ?? '-') }}</td>
                        <td class="px-3 py-2.5 font-bold text-slate-700">{{ $resetRequest->staff_id }}</td>
                        <td class="px-3 py-2.5">
                            <div class="font-bold text-slate-700">{{ $resetRequest->user->username ?? 'Unknown User' }}</div>
                            <div class="text-[9px] text-stone-400 mt-0.5">{{ $resetRequest->user->full_name ?? 'No full name' }}</div>
                        </td>
                        <td class="px-3 py-2.5 text-[10px] text-stone-500 whitespace-pre-line">
                            {{ $resetRequest->justification ?: '-' }}
                        </td>
                        <td class="px-3 py-2.5 text-[10px] text-stone-500">
                            {{ $resetRequest->requested_at ? \Carbon\Carbon::parse($resetRequest->requested_at)->format('d M Y H:i') : '-' }}
                        </td>
                        <td class="px-3 py-2.5 text-center">
                            <div class="approval-action-row">
                                <form action="{{ route('wt.admin.passwordResetRequests.approve', $resetRequest->id) }}" method="POST" onsubmit="return confirm('Approve forgot password request for {{ addslashes($resetRequest->user->username ?? $resetRequest->staff_id) }}?');">
                                    @csrf
                                    <button type="submit" class="approval-action-btn approval-action-approve">Approve</button>
                                </form>
                                <form action="{{ route('wt.admin.passwordResetRequests.reject', $resetRequest->id) }}" method="POST" onsubmit="return confirm('Reject forgot password request for {{ addslashes($resetRequest->user->username ?? $resetRequest->staff_id) }}?');">
                                    @csrf
                                    <button type="submit" class="approval-action-btn approval-action-reject">Reject</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

@php
    $accountDepartmentOptions = collect($formOptionLists['departments'] ?? [])
        ->merge($accounts->pluck('department'))
        ->merge($accounts->pluck('dept_name'))
        ->filter()
        ->map(fn ($value) => strtoupper(trim((string) $value)))
        ->unique()
        ->sort()
        ->values();
    $accountPositionOptions = collect($formOptionLists['positions'] ?? [])
        ->merge($accounts->pluck('position'))
        ->filter()
        ->map(fn ($value) => strtoupper(trim((string) $value)))
        ->unique()
        ->sort()
        ->values();
@endphp

<datalist id="executive-name-options">
    @foreach($accounts as $account)
        @php
            $executiveName = strtoupper((string) ($account->full_name ?: $account->username));
            $executiveDepartment = strtoupper((string) ($account->department ?: $account->dept_name ?: ''));
            $executivePosition = strtoupper((string) ($account->position ?: ''));
            $executiveStaffId = strtoupper((string) ($account->staff_id ?: $account->staff_no ?: $account->username));
        @endphp
        @if($executiveName)
            <option
                value="{{ $executiveName }}"
                data-staff-id="{{ $executiveStaffId }}"
                data-username="{{ $account->username }}"
                data-department="{{ $executiveDepartment }}"
                data-position="{{ $executivePosition }}">
                {{ trim($executiveStaffId . ' ' . $executiveDepartment) }}
            </option>
        @endif
    @endforeach
</datalist>

<div id="createExecutiveModal" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4">
    <div class="account-modal-card">
        <div class="account-modal-header">
            <div class="flex items-center gap-4 min-w-0">
                <span class="account-modal-icon">
                    <i class="fas fa-user-plus text-sm"></i>
                </span>
                <div class="min-w-0">
                    <h3 class="text-base font-black text-white uppercase tracking-[0.08em]">Add Account</h3>
                    <p class="text-[11px] text-slate-400 mt-1">Create an executive account for the walkie talkie system.</p>
                </div>
            </div>
            <button type="button" onclick="closeCreateExecutiveModal()" class="account-modal-close" title="Close">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <form action="{{ route('wt.admin.users.storeManager') }}" method="POST" autocomplete="off">
            @csrf
            <input type="hidden" name="form_context" value="create_executive">
            <div class="account-modal-body">

                {{-- Staff search --}}
                <div style="margin-bottom:14px;padding-bottom:14px;border-bottom:1px solid rgba(96,165,250,0.18)">
                    <label style="display:block;margin-bottom:6px;color:#9fb0c8;font-size:9px;font-weight:900;letter-spacing:.12em;text-transform:uppercase">
                        Search Staff to Auto-fill
                    </label>
                    <div style="position:relative">
                        <input type="text" id="wtStaffSearchInput" autocomplete="off"
                            placeholder="Type name or staff number…"
                            style="width:100%;min-height:34px;border-radius:9px;border:1px solid #334766;background:#0f172a;color:#f8fafc;padding:0 34px 0 11px;font-size:11px;font-weight:700;transition:all .18s ease"
                            onfocus="this.style.borderColor='#38bdf8'" onblur="this.style.borderColor='#334766'">
                        <i class="fas fa-search" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);color:#64748b;font-size:11px;pointer-events:none"></i>
                    </div>
                    <div id="wtStaffResults" style="display:none;position:absolute;z-index:9999;background:#1e293b;border:1px solid rgba(96,165,250,0.28);border-radius:10px;box-shadow:0 12px 32px rgba(2,6,23,.5);width:min(528px,calc(100vw - 48px));max-height:210px;overflow-y:auto;margin-top:4px"></div>
                    <div id="wtStaffBanner" style="display:none;margin-top:8px;padding:8px 11px;background:rgba(14,116,144,.15);border:1px solid rgba(14,116,144,.35);border-radius:9px;align-items:center;gap:8px">
                        <i class="fas fa-check-circle" style="color:#22d3ee;flex-shrink:0"></i>
                        <span id="wtStaffBannerLabel" style="font-size:11px;font-weight:700;color:#f8fafc;flex:1"></span>
                        <button type="button" onclick="wtClearStaff()" style="background:none;border:none;color:#94a3b8;cursor:pointer;font-size:11px;padding:0">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <p style="margin-top:5px;font-size:9px;color:#64748b">Select a staff member to auto-fill the fields below, or fill them in manually.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="account-field md:col-span-2">
                        <label>ID No. / Staff No.</label>
                        <input type="text" id="wt_field_staff_id" name="staff_id" value="{{ old('staff_id') }}" required>
                    </div>
                    <div class="account-field md:col-span-2">
                        <label>Executive Name</label>
                        <input type="text" id="wt_field_full_name" name="full_name" list="executive-name-options" value="{{ old('full_name') }}" placeholder="Search executive name..." data-preserve-case="true" autocomplete="off" required>
                    </div>
                    <div class="account-field">
                        <label>Department</label>
                        @php($currentCreateDepartment = strtoupper((string) old('department', '')))
                        <select id="wt_field_department" name="department" class="account-tag-select" data-placeholder="Type or select department" required>
                            <option value=""></option>
                            @foreach($accountDepartmentOptions as $departmentOption)
                            <option value="{{ $departmentOption }}" @selected($currentCreateDepartment === $departmentOption)>{{ $departmentOption }}</option>
                            @endforeach
                            @if($currentCreateDepartment !== '' && !$accountDepartmentOptions->contains($currentCreateDepartment))
                            <option value="{{ $currentCreateDepartment }}" selected>{{ $currentCreateDepartment }}</option>
                            @endif
                        </select>
                    </div>
                    <div class="account-field">
                        <label>Position</label>
                        @php($currentCreatePosition = strtoupper((string) old('position', '')))
                        <select id="wt_field_position" name="position" class="account-tag-select" data-placeholder="Type or select position" required>
                            <option value=""></option>
                            @foreach($accountPositionOptions as $positionOption)
                            <option value="{{ $positionOption }}" @selected($currentCreatePosition === $positionOption)>{{ $positionOption }}</option>
                            @endforeach
                            @if($currentCreatePosition !== '' && !$accountPositionOptions->contains($currentCreatePosition))
                            <option value="{{ $currentCreatePosition }}" selected>{{ $currentCreatePosition }}</option>
                            @endif
                        </select>
                    </div>
                    <div class="account-field">
                        <label>Password <span style="color:#94a3b8;font-weight:400;text-transform:none;letter-spacing:0">(optional if staff exists)</span></label>
                        <input type="password" name="password" minlength="6" id="wt_field_password">
                    </div>
                    <div class="account-field">
                        <label>Confirm Password</label>
                        <input type="password" name="password_confirmation" minlength="6" id="wt_field_password_confirmation">
                    </div>
                </div>
            </div>
            <div class="account-modal-footer">
                <button type="button" onclick="closeCreateExecutiveModal()" class="navy-btn navy-btn-soft">Cancel</button>
                <button type="submit" class="navy-btn">Grant / Create Account</button>
            </div>
        </form>
    </div>
</div>


<div id="viewUserModal" class="fixed bg-stone-900/60 backdrop-blur-sm hidden flex">
    <div class="view-account-card">
        <div class="view-account-header">
            <div>
                <h3 class="text-lg font-black text-[#142b47]">View Account</h3>
                <p class="text-xs text-stone-400 mt-1">Account details for <span id="view_full_name_heading" class="font-bold text-[#0284c7]"></span>.</p>
            </div>
            <button type="button" onclick="closeViewUserModal()" class="text-stone-400 hover:text-slate-700">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <div class="view-account-body">
            <div class="view-account-grid">
                <div class="view-account-field">
                    <span>ID No.</span>
                    <span id="view_staff_id"></span>
                </div>
                <div class="view-account-field">
                    <span>Username</span>
                    <span id="view_username"></span>
                </div>
                <div class="view-account-field md:col-span-2">
                    <span>Name</span>
                    <span id="view_full_name"></span>
                </div>
                <div class="view-account-field">
                    <span>Department</span>
                    <span id="view_department"></span>
                </div>
                <div class="view-account-field">
                    <span>Position</span>
                    <span id="view_position"></span>
                </div>
                <div class="view-account-field">
                    <span>Role</span>
                    <span id="view_role"></span>
                </div>
                <div class="view-account-field">
                    <span>Requests / Handovers</span>
                    <span id="view_usage"></span>
                </div>
                <div class="view-account-field">
                    <span>Created</span>
                    <span id="view_created"></span>
                </div>
                <div class="view-account-field">
                    <span>Last Activity</span>
                    <span id="view_last_activity"></span>
                </div>
            </div>
        </div>
        <div class="view-account-footer">
            <button type="button" onclick="closeViewUserModal()" class="navy-btn navy-btn-soft">Close</button>
        </div>
    </div>
</div>

<div id="editUserModal" class="fixed inset-0 bg-stone-900/60 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4">
    <div class="w-full max-w-xl bg-white rounded-[28px] border border-stone-200 shadow-2xl overflow-hidden">
        <div class="px-6 py-5 bg-stone-50 border-b border-stone-200 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-black text-[#142b47]">Edit User</h3>
                <p class="text-xs text-stone-400 mt-1">Update the user's ID, name, and role.</p>
            </div>
            <button type="button" onclick="closeEditUserModal()" class="text-stone-400 hover:text-slate-700">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <form id="editUserForm" method="POST" class="p-6 space-y-5" autocomplete="off">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-stone-500 mb-2">ID No.</label>
                    <input type="text" name="staff_id" id="edit_staff_id" class="navy-input w-full px-4 py-3 rounded-xl border" required>
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-stone-500 mb-2">Username</label>
                    <input type="text" name="username" id="edit_username" class="navy-input w-full px-4 py-3 rounded-xl border" autocapitalize="off" autocomplete="off" spellcheck="false" data-preserve-case="true" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-black uppercase tracking-widest text-stone-500 mb-2">Executive Name</label>
                    <input type="text" name="full_name" id="edit_full_name" list="executive-name-options" class="navy-input w-full px-4 py-3 rounded-xl border" placeholder="Search executive name..." data-preserve-case="true" autocomplete="off">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-stone-500 mb-2">Department</label>
                    <select name="department" id="edit_department" class="navy-input account-tag-select w-full px-4 py-3 rounded-xl border" data-placeholder="Type or select department">
                        <option value=""></option>
                        @foreach($accountDepartmentOptions as $departmentOption)
                        <option value="{{ $departmentOption }}">{{ $departmentOption }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-stone-500 mb-2">Position</label>
                    <select name="position" id="edit_position" class="navy-input account-tag-select w-full px-4 py-3 rounded-xl border" data-placeholder="Type or select position">
                        <option value=""></option>
                        @foreach($accountPositionOptions as $positionOption)
                        <option value="{{ $positionOption }}">{{ $positionOption }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-stone-500 mb-2">Role</label>
                    <select name="role" id="edit_role" class="navy-input w-full px-4 py-3 rounded-xl border" required>

                        <option value="admin">Executive</option>
                        <option value="admin_it">ICT</option>
                    </select>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 pt-3">
                <button type="button" onclick="closeEditUserModal()" class="navy-btn navy-btn-soft">Cancel</button>
                <button type="submit" class="navy-btn">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<div id="resetPasswordModal" class="fixed inset-0 bg-stone-900/60 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4">
    <div class="w-full max-w-xl bg-white rounded-[28px] border border-stone-200 shadow-2xl overflow-hidden">
        <div class="px-6 py-5 bg-stone-50 border-b border-stone-200 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-black text-[#142b47]">Reset Password</h3>
                <p class="text-xs text-stone-400 mt-1">Set a new password for <span id="resetUserName" class="font-bold text-[#0284c7]"></span>.</p>
            </div>
            <button type="button" onclick="closeResetPasswordModal()" class="text-stone-400 hover:text-slate-700">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <form id="resetPasswordForm" method="POST" class="p-6 space-y-5">
            @csrf
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-stone-500 mb-2">New Password</label>
                <input type="password" name="password" id="reset_password" class="navy-input w-full px-4 py-3 rounded-xl border" minlength="6" required>
            </div>
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-stone-500 mb-2">Confirm Password</label>
                <input type="password" name="password_confirmation" id="reset_password_confirmation" class="navy-input w-full px-4 py-3 rounded-xl border" minlength="6" required>
            </div>
            <div class="flex items-center justify-end gap-3 pt-3">
                <button type="button" onclick="closeResetPasswordModal()" class="navy-btn navy-btn-soft">Cancel</button>
                <button type="submit" class="navy-btn">Reset Password</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    function openCreateExecutiveModal() {
        document.getElementById('createExecutiveModal').classList.remove('hidden');
    }

    function closeCreateExecutiveModal() {
        document.getElementById('createExecutiveModal').classList.add('hidden');
    }

    function closeAllActionMenus() {
        document.querySelectorAll('[id^="action-menu-"]').forEach(function (menu) {
            menu.classList.add('hidden');
            menu.style.left = '';
            menu.style.top = '';
        });
    }

    function mountActionMenus() {
        document.querySelectorAll('.adminit-action-menu').forEach(function (menu) {
            if (menu.parentElement !== document.body) {
                document.body.appendChild(menu);
            }

            if (menu.dataset.stopClickBound !== 'true') {
                menu.addEventListener('click', function (event) {
                    event.stopPropagation();
                });
                menu.dataset.stopClickBound = 'true';
            }
        });
    }

    function toggleActionMenu(event, menuId) {
        event.stopPropagation();
        mountActionMenus();

        const menu = document.getElementById(menuId);
        const isHidden = menu.classList.contains('hidden');
        closeAllActionMenus();

        if (isHidden) {
            menu.classList.remove('hidden');
            const buttonRect = event.currentTarget.getBoundingClientRect();
            const pageGap = 12;
            const menuWidth = menu.offsetWidth || 208;
            const menuHeight = menu.offsetHeight || 148;
            const viewportWidth = document.documentElement.clientWidth;
            const viewportHeight = document.documentElement.clientHeight;
            const left = Math.min(
                viewportWidth - menuWidth - pageGap,
                Math.max(pageGap, buttonRect.left - menuWidth - 10)
            );
            const hasRoomBelow = buttonRect.bottom + 8 + menuHeight <= viewportHeight - pageGap;
            const top = hasRoomBelow
                ? buttonRect.bottom + 8
                : Math.max(pageGap, buttonRect.top - menuHeight - 8);

            menu.style.left = `${left}px`;
            menu.style.top = `${top}px`;
        }
    }

    function setViewUserText(id, value) {
        const target = document.getElementById(id);
        if (target) target.textContent = value || '-';
    }

    function openViewUserModalFromButton(button) {
        const data = button.dataset;
        setViewUserText('view_full_name_heading', data.fullName);
        setViewUserText('view_staff_id', data.staffId);
        setViewUserText('view_username', data.username);
        setViewUserText('view_full_name', data.fullName);
        setViewUserText('view_department', data.department);
        setViewUserText('view_position', data.position);
        setViewUserText('view_role', data.role);
        setViewUserText('view_usage', `${data.requests || 0} requests / ${data.handovers || 0} handovers`);
        setViewUserText('view_created', data.created);
        setViewUserText('view_last_activity', data.lastActivity);
        document.getElementById('viewUserModal').classList.remove('hidden');
    }

    function closeViewUserModal() {
        document.getElementById('viewUserModal').classList.add('hidden');
    }

    function openEditUserModal(userId, staffId, username, fullName, department, position, role) {
        document.getElementById('editUserForm').action = "{{ url('/wt/admin/users') }}/" + userId + "/update";
        document.getElementById('edit_staff_id').value = staffId || '';
        document.getElementById('edit_username').value = username || '';
        document.getElementById('edit_full_name').value = fullName || '';
        setAccountSelectValue('edit_department', department || '');
        setAccountSelectValue('edit_position', position || '');
        document.getElementById('edit_role').value = role || 'admin';
        document.getElementById('editUserModal').classList.remove('hidden');
    }

    function closeEditUserModal() {
        document.getElementById('editUserModal').classList.add('hidden');
    }

    function openResetPasswordModal(userId, username) {
        document.getElementById('resetPasswordForm').action = "{{ url('/wt/admin/users') }}/" + userId + "/reset-password";
        document.getElementById('resetUserName').innerText = username || '';
        document.getElementById('reset_password').value = '';
        document.getElementById('reset_password_confirmation').value = '';
        document.getElementById('resetPasswordModal').classList.remove('hidden');
    }

    function closeResetPasswordModal() {
        document.getElementById('resetPasswordModal').classList.add('hidden');
    }

    function togglePasswordVisibility(button) {
        const wrapper = button.closest('div');
        const passwordValue = wrapper.querySelector('.password-value');
        const icon = button.querySelector('i');
        const isVisible = button.dataset.visible === 'true';

        if (isVisible) {
            passwordValue.textContent = '********';
            button.dataset.visible = 'false';
            button.title = 'Show password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
            return;
        }

        passwordValue.textContent = 'PROTECTED';
        button.dataset.visible = 'true';
        button.title = 'Hide password';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    }

    document.addEventListener('click', function () {
        closeAllActionMenus();
    });

    document.addEventListener('DOMContentLoaded', mountActionMenus);
    window.addEventListener('scroll', closeAllActionMenus, true);
    window.addEventListener('resize', closeAllActionMenus);

    function normalizeAccountSelectValue(value) {
        return String(value || '').trim().toUpperCase();
    }

    function setAccountSelectValue(id, value) {
        const field = document.getElementById(id);
        if (!field) return;

        const normalizedValue = normalizeAccountSelectValue(value);
        if (field.tagName === 'SELECT' && normalizedValue) {
            const hasOption = Array.from(field.options).some(option => normalizeAccountSelectValue(option.value) === normalizedValue);
            if (!hasOption) {
                field.add(new Option(normalizedValue, normalizedValue, true, true));
            }
        }

        field.value = normalizedValue;
        if (window.jQuery && $(field).hasClass('select2-hidden-accessible')) {
            $(field).trigger('change.select2');
        }
    }

    function initAccountTagSelects() {
        if (!window.jQuery || !$.fn.select2) return;

        $('.account-tag-select').each(function () {
            const $select = $(this);

            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }

            $select.select2({
                width: '100%',
                tags: true,
                allowClear: !$select.prop('required'),
                minimumResultsForSearch: 0,
                placeholder: $select.data('placeholder') || 'Type or select option',
                dropdownParent: $select.closest('#createExecutiveModal, #editUserModal'),
                dropdownCssClass: 'account-select-dropdown',
                createTag: function (params) {
                    const term = $.trim(params.term);
                    if (term === '') return null;

                    const normalizedTerm = term.toUpperCase();
                    return { id: normalizedTerm, text: normalizedTerm, newTag: true };
                },
                insertTag: function (data, tag) {
                    data.unshift(tag);
                }
            });

            $select.off('select2:open.accountFocus').on('select2:open.accountFocus', function () {
                window.setTimeout(function () {
                    const searchField = document.querySelector('.select2-container--open .select2-search__field');
                    if (searchField) {
                        searchField.removeAttribute('readonly');
                        searchField.focus();
                    }
                }, 0);
            });
        });
    }

    document.addEventListener('DOMContentLoaded', initAccountTagSelects);

    @if($errors->any() && old('form_context') === 'create_executive')
    document.addEventListener('DOMContentLoaded', function () {
        openCreateExecutiveModal();
    });
    @endif

    // ── WT staff search ──────────────────────────────────────────────────────
    (function () {
        const input   = document.getElementById('wtStaffSearchInput');
        const results = document.getElementById('wtStaffResults');
        const banner  = document.getElementById('wtStaffBanner');
        const label   = document.getElementById('wtStaffBannerLabel');
        const nameOptions = document.getElementById('executive-name-options');
        const createNameInput = document.getElementById('wt_field_full_name');
        const editNameInput = document.getElementById('edit_full_name');
        let timer;
        let nameSearchTimer;

        if (!input) return;

        function normalizeValue(value) {
            return String(value || '').trim().toUpperCase();
        }

        function setControlValue(id, value) {
            const field = document.getElementById(id);
            if (!field) return;

            const normalizedValue = value || '';
            if (field.tagName === 'SELECT' && normalizedValue) {
                const hasOption = Array.from(field.options).some(option => option.value === normalizedValue);
                if (!hasOption) {
                    field.add(new Option(normalizedValue, normalizedValue, true, true));
                }
            }

            field.value = normalizedValue;
            if (window.jQuery && $(field).hasClass('select2-hidden-accessible')) {
                $(field).trigger('change.select2');
            }
        }

        function upsertExecutiveNameOption(s) {
            if (!nameOptions || !s || !s.name) return;

            const name = normalizeValue(s.name);
            let option = Array.from(nameOptions.options).find(item => normalizeValue(item.value) === name);
            if (!option) {
                option = document.createElement('option');
                option.value = name;
                nameOptions.appendChild(option);
            }

            option.dataset.staffId = normalizeValue(s.staff_no);
            option.dataset.username = normalizeValue(s.staff_no);
            option.dataset.department = normalizeValue(s.dept_name);
            option.dataset.position = normalizeValue(s.position);
            option.label = [option.dataset.staffId, option.dataset.department].filter(Boolean).join(' ');
            option.textContent = option.label;
        }

        function findExecutiveNameOption(value) {
            if (!nameOptions) return null;
            const selectedName = normalizeValue(value);
            return Array.from(nameOptions.options).find(option => normalizeValue(option.value) === selectedName) || null;
        }

        function applyExecutiveNameSelection(mode, value) {
            const option = findExecutiveNameOption(value);
            if (!option) return;

            if (mode === 'edit') {
                setControlValue('edit_staff_id', option.dataset.staffId || '');
                setControlValue('edit_username', option.dataset.username || option.dataset.staffId || '');
                setControlValue('edit_department', option.dataset.department || '');
                setControlValue('edit_position', option.dataset.position || '');
                return;
            }

            setControlValue('wt_field_staff_id', option.dataset.staffId || '');
            setControlValue('wt_field_department', option.dataset.department || '');
            setControlValue('wt_field_position', option.dataset.position || '');
            document.getElementById('wt_field_password')?.removeAttribute('required');
            document.getElementById('wt_field_password_confirmation')?.removeAttribute('required');
        }

        function refreshExecutiveNameOptions(query) {
            const q = String(query || '').trim();
            if (q.length < 2) return;

            clearTimeout(nameSearchTimer);
            nameSearchTimer = setTimeout(function () {
                fetch('{{ route('wt.admin.users.staffSearch') }}?q=' + encodeURIComponent(q), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(data => data.forEach(upsertExecutiveNameOption));
            }, 220);
        }

        [createNameInput, editNameInput].forEach(function (nameInput) {
            if (!nameInput) return;

            nameInput.addEventListener('input', function () {
                refreshExecutiveNameOptions(this.value);
            });

            nameInput.addEventListener('change', function () {
                applyExecutiveNameSelection(this.id === 'edit_full_name' ? 'edit' : 'create', this.value);
            });
        });

        input.addEventListener('input', function () {
            clearTimeout(timer);
            const q = this.value.trim();
            if (q.length < 2) { results.style.display = 'none'; return; }
            timer = setTimeout(() => fetchStaff(q), 280);
        });

        input.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') results.style.display = 'none';
        });

        document.addEventListener('click', function (e) {
            if (!input.contains(e.target) && !results.contains(e.target)) {
                results.style.display = 'none';
            }
        });

        function fetchStaff(q) {
            fetch('{{ route('wt.admin.users.staffSearch') }}?q=' + encodeURIComponent(q), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => renderResults(data));
        }

        function renderResults(data) {
            if (!data.length) {
                results.innerHTML = '<div style="padding:14px 16px;font-size:12px;color:#64748b;text-align:center"><i class="fas fa-search" style="opacity:.4"></i> No staff found</div>';
                results.style.display = 'block';
                return;
            }
            results.innerHTML = data.map(s => `
                <div onclick='wtSelectStaff(${JSON.stringify(s).replace(/'/g, "\\'")})'
                    class="wt-staff-option">
                    <div class="wt-staff-avatar">
                        ${s.name.charAt(0)}
                    </div>
                    <div style="flex:1;min-width:0">
                        <div class="wt-staff-name">${s.name}</div>
                        <div class="wt-staff-meta">
                            <span>${s.staff_no}</span>
                            ${s.dept_name ? ' · ' + s.dept_name : ''}
                            ${s.position ? ' · ' + s.position : ''}
                        </div>
                    </div>
                </div>`).join('');
            results.innerHTML += '<div class="wt-staff-count">' + data.length + ' result' + (data.length > 1 ? 's' : '') + '</div>';
            results.style.display = 'block';
        }

        window.wtSelectStaff = function (s) {
            upsertExecutiveNameOption(s);
            document.getElementById('wt_field_staff_id').value    = s.staff_no;
            document.getElementById('wt_field_full_name').value   = s.name;
            setControlValue('wt_field_department', s.dept_name);
            setControlValue('wt_field_position', s.position || '');
            // Password becomes optional when granting access to existing HR staff
            document.getElementById('wt_field_password').removeAttribute('required');
            document.getElementById('wt_field_password_confirmation').removeAttribute('required');
            results.style.display = 'none';
            input.value = '';
            label.textContent = s.name + ' (' + s.staff_no + ')' + (s.dept_name ? ' — ' + s.dept_name : '');
            banner.style.display = 'flex';
        };

        window.wtClearStaff = function () {
            ['wt_field_staff_id','wt_field_full_name','wt_field_department','wt_field_position'].forEach(function (id) {
                setControlValue(id, '');
            });
            // Restore password as required
            document.getElementById('wt_field_password').setAttribute('required', '');
            document.getElementById('wt_field_password_confirmation').setAttribute('required', '');
            banner.style.display = 'none';
            input.value = '';
        };
    })();

    $(document).ready(function() {
        const passwordResetEmptyState = `
            <div class="adminit-empty-state">
                <span class="empty-visual"><i class="fa-solid fa-key"></i></span>
                <span class="empty-title">No Pending Requests</span>
                <span class="empty-copy">There are no forgot password requests waiting for ICT approval right now.</span>
            </div>
        `;

        const passwordResetTable = $('#passwordResetTable').DataTable({
            pageLength: 5,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, 'All']],
            ordering: true,
            order: [[2, 'desc']],
            dom: 'Blfrtip',
            buttons: getAdminTableExportButtons('Pending Forgot Password Requests', ':not(:last-child)'),
            language: {
                search: '',
                searchPlaceholder: 'Search reset requests...',
                emptyTable: passwordResetEmptyState,
                zeroRecords: passwordResetEmptyState
            },
            responsive: true,
            autoWidth: false
        });
        mountAdminTableFooter(passwordResetTable);
        mountAdminTableExports(passwordResetTable, '#passwordResetExportActions');

        const staffDirectoryTable = $('#staffDirectoryTable').DataTable({
            pageLength: 5,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, 'All']],
            ordering: true,
            order: [[0, 'asc']],
            dom: 'Blfrtip',
            buttons: getAdminTableExportButtons('User Directory', ':not(:last-child)'),
            language: {
                search: '',
                searchPlaceholder: 'Search staff...',
                emptyTable: 'No staff records found.',
                zeroRecords: 'No matching staff records found.'
            },
            responsive: true,
            autoWidth: false
        });
        mountAdminTableFooter(staffDirectoryTable);
        mountAdminTableExports(staffDirectoryTable, '#staffDirectoryExportActions');
    });
</script>
@endpush
