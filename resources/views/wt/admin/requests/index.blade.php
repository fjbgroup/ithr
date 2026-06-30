@extends('wt.layouts.admin')

@section('title', 'Approval Inbox')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .approval-inbox {
        color: var(--text);
    }
    .approval-inbox .approval-title {
        font-size: 1.25rem;
        line-height: 1.1;
        font-weight: 800;
        color: var(--text);
        letter-spacing: -0.02em;
    }
    .approval-inbox .approval-subtitle {
        font-size: 9px;
        font-weight: 700;
        color: var(--muted);
        letter-spacing: 0.1em;
        line-height: 1.35;
        text-transform: uppercase;
    }
    .approval-inbox .approval-body-title {
        font-size: 10px;
        font-weight: 800;
        color: var(--text);
    }
    .approval-inbox .approval-body-meta {
        font-size: 9px;
        color: var(--muted);
    }
    .approval-inbox .approval-date {
        font-size: 10px;
        font-weight: 700;
        color: var(--text);
    }
    .approval-inbox .approval-empty {
        font-size: 10px;
        font-weight: 600;
        color: var(--muted);
    }
    .approval-inbox .approval-card {
        background: var(--surface);
        border-color: var(--border);
    }
    .approval-inbox .empty-visual {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border-radius: 14px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #94a3b8;
        margin-bottom: 8px;
    }
    .approval-inbox .approval-empty-state {
        display: flex;
        min-height: 112px;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 10px;
        color: var(--muted);
        font-size: 11px;
        font-weight: 700;
        text-align: center;
    }
    .approval-inbox .approval-empty-state .empty-visual {
        margin-bottom: 0;
    }
    .dark .approval-inbox .empty-visual {
        background: #111827;
        border-color: #334155;
        color: #94a3b8;
    }
    #quickWtModal .quick-wt-input {
        width: 100%;
        min-height: 38px;
        border-radius: 9px;
        border: 1px solid #334155;
        background: #0f172a;
        padding: 8px 11px;
        color: #e5e7eb;
        font-size: 11px;
        font-weight: 700;
        outline: none;
    }
    #quickWtModal .quick-wt-input:focus {
        border-color: #38bdf8;
        box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.14);
    }
    #quickWtModal input.quick-wt-input {
        background: #ffffff;
        border-color: #cbd5e1;
        color: #1f2937;
    }
    #quickWtModal input.quick-wt-input::placeholder {
        color: #94a3b8;
    }
    #quickWtModal .select2-container--default .select2-selection--single {
        min-height: 38px;
        border-radius: 9px;
        border: 1px solid #334155;
        background: #0f172a;
    }
    #quickWtModal .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #e5e7eb;
        font-size: 11px;
        font-weight: 700;
        line-height: 36px;
        padding-left: 11px;
    }
    #quickWtModal .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    #approveModal .approval-modal-card {
        margin: 0 !important;
        flex: 0 0 380px;
        width: 380px !important;
        max-height: calc(100vh - 1.5rem);
        overflow-y: auto !important;
    }
    #approveModal .approval-modal-card .navy-panel {
        padding: 16px 18px !important;
    }
    #approveModal .approval-modal-card form {
        padding: 18px !important;
    }
    #approveModal .approval-modal-card .mb-5 {
        margin-bottom: 14px !important;
    }
    #approveModal #accessoryChecklistBox {
        padding: 14px !important;
        border-radius: 10px !important;
    }
    #approveModal #approvalRemark {
        min-height: 78px !important;
        padding: 10px 12px !important;
    }
    #quickWtModal {
        flex: 0 0 480px;
        max-width: none;
    }
    #quickWtModal > div {
        max-height: calc(100vh - 1.5rem) !important;
        border-radius: 12px !important;
    }
    #quickWtModal form {
        padding: 16px 18px !important;
    }
    #quickWtModal form > .grid {
        gap: 10px 12px !important;
    }
    #quickWtModal .border-b {
        padding: 14px 18px !important;
        background: #111827 !important;
        border-bottom-color: #334155 !important;
    }
    #quickWtModal h3 {
        font-size: 14px !important;
    }
    #quickWtModal p,
    #quickWtModal label {
        font-size: 9px !important;
    }
    #quickWtModal textarea.quick-wt-input {
        min-height: 64px !important;
    }
    .approval-inbox table.dataTable tbody td.dataTables_empty {
        padding: 0 !important;
        color: var(--muted) !important;
        background: transparent !important;
    }
    .approval-inbox table.dataTable tbody tr {
        background: #1e293b !important;
        border: 1px solid rgba(255,255,255,0.05) !important;
    }
    .approval-inbox table.dataTable tbody tr:hover {
        background: rgba(56, 189, 248, 0.08) !important;
    }
    .approval-inbox table.dataTable,
    .approval-inbox .dataTables_wrapper {
        width: 100% !important;
    }
    .approval-inbox table.dataTable {
        table-layout: fixed;
    }
    .approval-inbox table.dataTable tbody td.dataTables_empty {
        width: 100% !important;
        text-align: center !important;
    }
    .approval-inbox .request-meta-block {
        background: color-mix(in srgb, var(--surface-soft) 92%, white 8%);
        border-color: var(--surface-line);
    }
    .approval-inbox .request-meta-label {
        font-size: 9px;
        color: var(--muted);
    }
    .approval-inbox .request-meta-value {
        font-size: 10px;
        color: var(--text);
    }
    .approval-inbox .dataTables_wrapper,
    .approval-inbox .dataTables_wrapper label,
    .approval-inbox .dataTables_wrapper .dataTables_info,
    .approval-inbox .dataTables_wrapper .dataTables_paginate {
        font-size: 10px !important;
    }
    .approval-inbox .dataTables_wrapper .dataTables_filter input,
    .approval-inbox .dataTables_wrapper .dataTables_length select {
        font-size: 10px !important;
        min-height: 36px;
    }
    .approval-inbox table.dataTable thead th {
        font-size: 9px !important;
        letter-spacing: 0.12em !important;
    }
    .select2-container .select2-selection--single {
        height: 38px !important;
        border-radius: 10px !important;
        border: 1.5px solid #e2e8f0 !important;
        background: #f8fafc !important;
        padding: 3px 9px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px !important;
        color: #44403c !important;
        font-size: 11px !important;
        font-weight: 700 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
        right: 8px !important;
    }
    .request-meta-block {
        margin-top: 8px;
        padding: 8px 10px;
        border-radius: 12px;
        background: #fafaf9;
        border: 1px solid #ece7e1;
    }
    .request-meta-label {
        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: #475569;
        margin-bottom: 4px;
    }
    .request-meta-value {
        font-size: 10px;
        color: #57534e;
        line-height: 1.4;
    }
    .navy-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.4rem 0.65rem;
        border-radius: 0.7rem;
        border: 1px solid rgba(96, 165, 250, 0.28);
        background: rgba(15, 23, 42, 0.96);
        color: #e2e8f0;
        font-size: 8px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.1em;
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
        box-shadow: 0 0 0 1px rgba(127, 29, 29, 0.38), 0 0 18px rgba(239, 68, 68, 0.18);
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
    .approval-inbox table.dataTable tbody tr:hover {
        background: inherit !important;
    }
    .approval-inbox .navy-btn:hover {
        background: rgba(15, 23, 42, 0.96) !important;
        color: #e2e8f0 !important;
        border-color: rgba(96, 165, 250, 0.28) !important;
        box-shadow: 0 0 0 1px rgba(30, 41, 59, 0.42), 0 0 16px rgba(59, 130, 246, 0.12) !important;
        transform: none !important;
    }
    .approval-inbox .navy-btn-danger:hover {
        background: rgba(69, 10, 10, 0.96) !important;
        color: #fee2e2 !important;
        border-color: rgba(248, 113, 113, 0.28) !important;
        box-shadow: 0 0 0 1px rgba(127, 29, 29, 0.32), 0 0 14px rgba(239, 68, 68, 0.12) !important;
        transform: none !important;
    }
    .approval-inbox .navy-btn-soft:hover {
        background: #f8fafc !important;
        color: #475569 !important;
        border-color: #cbd5e1 !important;
        box-shadow: none !important;
        transform: none !important;
    }
    .approval-inbox a:hover {
        background: inherit !important;
        color: inherit !important;
    }
    .request-form-detail {
        border: 1px solid #e7e5e4;
        background: #fafaf9;
        border-radius: 16px;
        padding: 12px 14px;
    }
    .request-form-detail-label {
        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #78716c;
        margin-bottom: 5px;
    }
    .request-form-detail-value {
        font-size: 12px;
        font-weight: 800;
        line-height: 1.45;
        color: #292524;
        word-break: break-word;
    }
    .damage-form-sheet {
        background: #f8fafc;
    }
    .damage-form-header {
        background: #0f172a;
        border-bottom: 1px solid rgba(148, 163, 184, 0.28);
    }
    .damage-form-kicker {
        color: #dbeafe;
    }
    .damage-form-title {
        color: #ffffff !important;
        text-shadow: 0 1px 1px rgba(15, 23, 42, 0.45);
    }
    .damage-form-subtitle {
        color: #bfdbfe !important;
    }
    .damage-form-section {
        background: #ffffff;
        border: 1px solid #dbe3ee;
        border-radius: 8px;
        overflow: hidden;
    }
    .damage-form-section-title {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 11px 14px;
        border-bottom: 1px solid #e2e8f0;
        background: #f1f5f9;
        color: #334155;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.13em;
        text-transform: uppercase;
    }
    .damage-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .damage-form-field {
        min-height: 68px;
        padding: 13px 14px;
        border-right: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
    }
    .damage-form-field:nth-child(2n) {
        border-right: 0;
    }
    .damage-form-field-wide {
        grid-column: 1 / -1;
        border-right: 0;
    }
    .damage-form-label {
        margin-bottom: 6px;
        color: #64748b;
        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }
    .damage-form-value {
        color: #0f172a;
        font-size: 12px;
        font-weight: 800;
        line-height: 1.45;
        word-break: break-word;
    }
    .damage-form-status,
    .damage-form-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        border-radius: 999px;
        padding: 6px 10px;
        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }
    .damage-form-status {
        border: 1px solid rgba(251, 191, 36, 0.35);
        background: #fffbeb;
        color: #92400e;
    }
    .damage-form-badge {
        border: 1px solid #bbf7d0;
        background: #f0fdf4;
        color: #166534;
    }
    .dark .damage-form-sheet {
        background: #0f172a;
    }
    .dark .damage-form-section {
        background: #111827;
        border-color: #334155;
    }
    .dark .damage-form-section-title {
        background: #1e293b;
        border-color: #334155;
        color: #cbd5e1;
    }
    .dark .damage-form-field {
        border-color: #334155;
    }
    .dark .damage-form-label {
        color: #94a3b8;
    }
    .dark .damage-form-value {
        color: #e2e8f0;
    }
    .dark .request-form-detail {
        border-color: #334155;
        background: #111827;
    }
    .dark .request-form-detail-label {
        color: #94a3b8;
    }
    .dark .request-form-detail-value {
        color: #e2e8f0;
    }
    .navy-panel {
        background: #162033;
    }
    .navy-panel h4,
    .navy-panel h3,
    .navy-panel p,
    .navy-panel i {
        color: #f8fafc !important;
    }
    .navy-chip {
        background: rgba(15, 23, 42, 0.96);
        color: #e2e8f0;
        border: 1px solid rgba(96, 165, 250, 0.24);
        font-size: 10px !important;
    }
    [id^="requestFormModal-"],
    [id^="returnFormModal-"],
    [id^="damageFormModal-"] {
        z-index: 2147483000 !important;
        background: rgba(15, 23, 42, 0.42) !important;
        backdrop-filter: blur(6px) !important;
    }
    [id^="requestFormModal-"] > div,
    [id^="returnFormModal-"] > div,
    [id^="damageFormModal-"] > div {
        width: min(640px, calc(100vw - 32px)) !important;
        max-width: 640px !important;
        max-height: min(84vh, 680px) !important;
        border-radius: 12px !important;
        overflow-y: auto !important;
        background: #ffffff !important;
        border: 1px solid #d6e0ec !important;
        box-shadow: 0 22px 56px rgba(15, 23, 42, 0.26) !important;
    }
    [id^="requestFormModal-"] .navy-panel,
    [id^="returnFormModal-"] .navy-panel,
    [id^="damageFormModal-"] .damage-form-header {
        min-height: 68px !important;
        padding: 14px 18px !important;
        background: #162033 !important;
        border-bottom: 1px solid rgba(203, 213, 225, 0.18) !important;
    }
    [id^="requestFormModal-"] .navy-panel p,
    [id^="returnFormModal-"] .navy-panel p,
    [id^="damageFormModal-"] .damage-form-kicker {
        color: #cbd5e1 !important;
        font-size: 8px !important;
        font-weight: 900 !important;
        letter-spacing: 0.14em !important;
        text-transform: uppercase !important;
        margin: 0 !important;
    }
    [id^="requestFormModal-"] .navy-panel h3,
    [id^="returnFormModal-"] .navy-panel h3,
    [id^="damageFormModal-"] .damage-form-title {
        color: #ffffff !important;
        font-size: 16px !important;
        line-height: 1.15 !important;
        font-weight: 900 !important;
        margin-top: 4px !important;
    }
    [id^="requestFormModal-"] .navy-panel h3 + p,
    [id^="returnFormModal-"] .navy-panel h3 + p,
    [id^="damageFormModal-"] .damage-form-subtitle {
        color: #dbeafe !important;
        font-size: 10px !important;
        font-weight: 700 !important;
        margin-top: 3px !important;
    }
    [id^="requestFormModal-"] > div > .p-6,
    [id^="returnFormModal-"] > div > .p-6,
    [id^="damageFormModal-"] > div > .p-5,
    [id^="damageFormModal-"] > div > .md\:p-6 {
        background: #f8fafc !important;
        padding: 14px !important;
    }
    [id^="requestFormModal-"] .request-form-detail,
    [id^="returnFormModal-"] .request-form-detail,
    [id^="damageFormModal-"] .damage-form-section {
        background: #ffffff !important;
        border: 1px solid #d6e0ec !important;
        border-radius: 8px !important;
        box-shadow: none !important;
    }
    [id^="requestFormModal-"] .request-form-detail,
    [id^="returnFormModal-"] .request-form-detail {
        min-height: 52px !important;
        padding: 9px 11px !important;
    }
    [id^="damageFormModal-"] .damage-form-section {
        overflow: hidden !important;
    }
    [id^="damageFormModal-"] .damage-form-section-title {
        background: #eef4fb !important;
        border-bottom: 1px solid #d6e0ec !important;
        color: #334155 !important;
        min-height: 34px !important;
        padding: 8px 11px !important;
        font-size: 9px !important;
        letter-spacing: 0.11em !important;
    }
    [id^="damageFormModal-"] .damage-form-field {
        min-height: 52px !important;
        padding: 9px 11px !important;
        background: #ffffff !important;
        border-color: #e2e8f0 !important;
    }
    [id^="requestFormModal-"] .request-form-detail-label,
    [id^="returnFormModal-"] .request-form-detail-label,
    [id^="damageFormModal-"] .damage-form-label {
        color: #64748b !important;
        font-size: 8px !important;
        font-weight: 900 !important;
        letter-spacing: 0.11em !important;
        text-transform: uppercase !important;
        margin-bottom: 4px !important;
    }
    [id^="requestFormModal-"] .request-form-detail-value,
    [id^="returnFormModal-"] .request-form-detail-value,
    [id^="damageFormModal-"] .damage-form-value {
        color: #1f2937 !important;
        font-size: 10px !important;
        font-weight: 800 !important;
        line-height: 1.35 !important;
    }
    [id^="requestFormModal-"] .grid,
    [id^="returnFormModal-"] .grid {
        gap: 8px !important;
    }
    [id^="requestFormModal-"] .md\:grid-cols-3,
    [id^="returnFormModal-"] .md\:grid-cols-3 {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }
    [id^="damageFormModal-"] .space-y-4 > :not([hidden]) ~ :not([hidden]) {
        margin-top: 10px !important;
    }
    [id^="damageFormModal-"] .damage-form-status,
    [id^="damageFormModal-"] .damage-form-badge {
        padding: 4px 8px !important;
        font-size: 8px !important;
        letter-spacing: 0.08em !important;
    }
    [id^="requestFormModal-"] button[onclick*="close"],
    [id^="returnFormModal-"] button[onclick*="close"],
    [id^="damageFormModal-"] button[onclick*="close"] {
        font-size: 9px !important;
    }
    [id^="requestFormModal-"] .absolute.top-5.right-5,
    [id^="returnFormModal-"] .absolute.top-5.right-5,
    [id^="damageFormModal-"] .absolute.top-5.right-5 {
        top: 14px !important;
        right: 16px !important;
    }
    [id^="requestFormModal-"] button[onclick^="closeRequestFormModal"],
    [id^="returnFormModal-"] button[onclick^="closeReturnFormModal"],
    [id^="damageFormModal-"] button[onclick^="closeDamageFormModal"] {
        transition: color 0.18s ease, background 0.18s ease !important;
    }
    [id^="requestFormModal-"] .damage-form-header,
    [id^="returnFormModal-"] .damage-form-header,
    [id^="damageFormModal-"] .damage-form-header {
        min-height: 68px !important;
        padding: 14px 18px !important;
        background: #162033 !important;
        border-bottom: 1px solid rgba(203, 213, 225, 0.18) !important;
    }
    [id^="requestFormModal-"] .damage-form-kicker,
    [id^="returnFormModal-"] .damage-form-kicker,
    [id^="damageFormModal-"] .damage-form-kicker {
        color: #cbd5e1 !important;
        font-size: 8px !important;
        font-weight: 900 !important;
        letter-spacing: 0.14em !important;
        text-transform: uppercase !important;
    }
    [id^="requestFormModal-"] .damage-form-title,
    [id^="returnFormModal-"] .damage-form-title,
    [id^="damageFormModal-"] .damage-form-title {
        color: #ffffff !important;
        font-size: 16px !important;
        line-height: 1.15 !important;
        font-weight: 900 !important;
    }
    [id^="requestFormModal-"] .damage-form-subtitle,
    [id^="returnFormModal-"] .damage-form-subtitle,
    [id^="damageFormModal-"] .damage-form-subtitle {
        color: #dbeafe !important;
        font-size: 10px !important;
        font-weight: 700 !important;
    }
    [id^="requestFormModal-"] > div > .p-5,
    [id^="requestFormModal-"] > div > .md\:p-6,
    [id^="returnFormModal-"] > div > .p-5,
    [id^="returnFormModal-"] > div > .md\:p-6,
    [id^="damageFormModal-"] > div > .p-5,
    [id^="damageFormModal-"] > div > .md\:p-6 {
        background: #f8fafc !important;
        padding: 14px !important;
    }
    [id^="requestFormModal-"] .damage-form-section,
    [id^="returnFormModal-"] .damage-form-section,
    [id^="damageFormModal-"] .damage-form-section {
        background: #ffffff !important;
        border: 1px solid #d6e0ec !important;
        border-radius: 8px !important;
        overflow: hidden !important;
        box-shadow: none !important;
    }
    [id^="requestFormModal-"] .damage-form-section-title,
    [id^="returnFormModal-"] .damage-form-section-title,
    [id^="damageFormModal-"] .damage-form-section-title {
        min-height: 34px !important;
        padding: 8px 11px !important;
        background: #eef4fb !important;
        border-bottom: 1px solid #d6e0ec !important;
        color: #334155 !important;
        font-size: 9px !important;
        letter-spacing: 0.11em !important;
    }
    [id^="requestFormModal-"] .damage-form-field,
    [id^="returnFormModal-"] .damage-form-field,
    [id^="damageFormModal-"] .damage-form-field {
        min-height: 52px !important;
        padding: 9px 11px !important;
        background: #ffffff !important;
        border-color: #e2e8f0 !important;
    }
    [id^="requestFormModal-"] .damage-form-label,
    [id^="returnFormModal-"] .damage-form-label,
    [id^="damageFormModal-"] .damage-form-label {
        color: #64748b !important;
        font-size: 8px !important;
        font-weight: 900 !important;
        letter-spacing: 0.11em !important;
        text-transform: uppercase !important;
        margin-bottom: 4px !important;
    }
    [id^="requestFormModal-"] .damage-form-value,
    [id^="returnFormModal-"] .damage-form-value,
    [id^="damageFormModal-"] .damage-form-value {
        color: #1f2937 !important;
        font-size: 10px !important;
        font-weight: 800 !important;
        line-height: 1.35 !important;
    }
    .dark [id^="requestFormModal-"] > div,
    .dark [id^="returnFormModal-"] > div,
    .dark [id^="damageFormModal-"] > div {
        background: #0f172a !important;
        border-color: #334155 !important;
    }
    .dark [id^="requestFormModal-"] > div > .p-5,
    .dark [id^="requestFormModal-"] > div > .md\:p-6,
    .dark [id^="returnFormModal-"] > div > .p-5,
    .dark [id^="returnFormModal-"] > div > .md\:p-6,
    .dark [id^="damageFormModal-"] > div > .p-5,
    .dark [id^="damageFormModal-"] > div > .md\:p-6 {
        background: #0f172a !important;
    }
    .dark [id^="requestFormModal-"] .request-form-detail,
    .dark [id^="requestFormModal-"] .damage-form-section,
    .dark [id^="returnFormModal-"] .request-form-detail,
    .dark [id^="returnFormModal-"] .damage-form-section,
    .dark [id^="damageFormModal-"] .damage-form-section {
        background: #111827 !important;
        border-color: #334155 !important;
    }
    .dark [id^="requestFormModal-"] .damage-form-section-title,
    .dark [id^="returnFormModal-"] .damage-form-section-title,
    .dark [id^="damageFormModal-"] .damage-form-section-title {
        background: #1e293b !important;
        border-color: #334155 !important;
        color: #cbd5e1 !important;
    }
    .dark [id^="requestFormModal-"] .damage-form-field,
    .dark [id^="returnFormModal-"] .damage-form-field,
    .dark [id^="damageFormModal-"] .damage-form-field {
        background: #111827 !important;
        border-color: #334155 !important;
    }
    .dark [id^="requestFormModal-"] .request-form-detail-label,
    .dark [id^="requestFormModal-"] .damage-form-label,
    .dark [id^="returnFormModal-"] .request-form-detail-label,
    .dark [id^="returnFormModal-"] .damage-form-label,
    .dark [id^="damageFormModal-"] .damage-form-label {
        color: #94a3b8 !important;
    }
    .dark [id^="requestFormModal-"] .request-form-detail-value,
    .dark [id^="requestFormModal-"] .damage-form-value,
    .dark [id^="returnFormModal-"] .request-form-detail-value,
    .dark [id^="returnFormModal-"] .damage-form-value,
    .dark [id^="damageFormModal-"] .damage-form-value {
        color: #e2e8f0 !important;
    }
    [id^="requestFormModal-"].request-form-modal {
        align-items: center !important;
        justify-content: center !important;
        padding: 18px !important;
    }
    [id^="requestFormModal-"].request-form-modal > .damage-form-sheet {
        width: min(760px, calc(100vw - 36px)) !important;
        max-width: 760px !important;
        max-height: min(88vh, 760px) !important;
        margin: auto !important;
        border-radius: 18px !important;
    }
    [id^="requestFormModal-"].request-form-modal .damage-form-section {
        border-radius: 14px !important;
    }
    [id^="requestFormModal-"].request-form-modal .damage-form-grid {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 0 !important;
    }
    [id^="requestFormModal-"].request-form-modal .owner-unit-card {
        border-radius: 12px !important;
        border: 1px solid #dbe3ee !important;
        background: #ffffff !important;
        overflow: hidden !important;
    }
    [id^="requestFormModal-"].request-form-modal .owner-unit-title {
        padding: 9px 11px !important;
        border-bottom: 1px solid #e2e8f0 !important;
        background: #f8fafc !important;
        color: #334155 !important;
        font-size: 9px !important;
        font-weight: 900 !important;
        letter-spacing: 0.11em !important;
        text-transform: uppercase !important;
    }
    [id^="requestFormModal-"].request-form-modal .owner-unit-grid {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }
    [id^="requestFormModal-"].request-form-modal .owner-unit-grid > div {
        min-height: 54px !important;
        padding: 9px 11px !important;
        border-right: 1px solid #e2e8f0 !important;
        border-bottom: 1px solid #e2e8f0 !important;
    }
    [id^="requestFormModal-"].request-form-modal .owner-unit-grid > div:nth-child(2n) {
        border-right: 0 !important;
    }
    .dark [id^="requestFormModal-"].request-form-modal .owner-unit-card {
        background: #111827 !important;
        border-color: #334155 !important;
    }
    .dark [id^="requestFormModal-"].request-form-modal .owner-unit-title {
        background: #1e293b !important;
        border-color: #334155 !important;
        color: #cbd5e1 !important;
    }
    .dark [id^="requestFormModal-"].request-form-modal .owner-unit-grid > div {
        border-color: #334155 !important;
    }
    html:not(.dark) .approval-inbox table.dataTable tbody tr {
        background: #ffffff !important;
        border-color: #e2e8f0 !important;
    }
    html:not(.dark) .approval-inbox table.dataTable tbody tr,
    html:not(.dark) .approval-inbox table.dataTable tbody tr td,
    html:not(.dark) .approval-inbox table.dataTable tbody tr:nth-child(odd) td,
    html:not(.dark) .approval-inbox table.dataTable tbody tr:nth-child(even) td {
        background: #ffffff !important;
    }
    html:not(.dark) .approval-inbox table.dataTable tbody tr:nth-child(odd):hover,
    html:not(.dark) .approval-inbox table.dataTable tbody tr:nth-child(odd):hover td,
    html:not(.dark) .approval-inbox table.dataTable tbody tr:nth-child(even):hover,
    html:not(.dark) .approval-inbox table.dataTable tbody tr:nth-child(even):hover td {
        background: #ffffff !important;
    }
    html:not(.dark) .approval-inbox table.dataTable.stripe tbody tr.odd,
    html:not(.dark) .approval-inbox table.dataTable.display tbody tr.odd,
    html:not(.dark) .approval-inbox table.dataTable.display tbody tr.odd > .sorting_1,
    html:not(.dark) .approval-inbox table.dataTable.order-column tbody tr > .sorting_1,
    html:not(.dark) .approval-inbox table.dataTable.display tbody tr > .sorting_2,
    html:not(.dark) .approval-inbox table.dataTable.display tbody tr > .sorting_3,
    html:not(.dark) .approval-inbox table.dataTable.hover tbody tr:hover,
    html:not(.dark) .approval-inbox table.dataTable.display tbody tr:hover,
    html:not(.dark) .approval-inbox table.dataTable.display tbody tr:hover > .sorting_1,
    html:not(.dark) .approval-inbox table.dataTable.display tbody tr:hover > .sorting_2,
    html:not(.dark) .approval-inbox table.dataTable.display tbody tr:hover > .sorting_3,
    html:not(.dark) .approval-inbox table.dataTable tbody th,
    html:not(.dark) .approval-inbox table.dataTable tbody td {
        background: #ffffff !important;
        box-shadow: none !important;
    }
    html:not(.dark) .approval-inbox .request-meta-block {
        background: #f8fafc !important;
        border: 1px solid #d6e0ec !important;
        color: #334155 !important;
        box-shadow: none !important;
    }
    html:not(.dark) .approval-inbox .request-meta-label {
        color: #64748b !important;
    }
    html:not(.dark) .approval-inbox .request-meta-value {
        color: #334155 !important;
    }
    html:not(.dark) .approval-inbox .request-meta-value .font-black {
        color: #8b5e3c !important;
    }
    html:not(.dark) .approval-inbox .request-meta-value .bg-sky-50 {
        background: #e0f2fe !important;
        border-color: #bae6fd !important;
        color: #0369a1 !important;
    }
    html:not(.dark) .approval-inbox .request-meta-value .bg-violet-50 {
        background: #f3e8ff !important;
        border-color: #ddd6fe !important;
        color: #6d28d9 !important;
    }
    html:not(.dark) .approval-inbox .requestor-card,
    html:not(.dark) .approval-inbox .request-summary-card {
        background: #ffffff !important;
        border-color: #dbe3ee !important;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
    }
    html:not(.dark) .approval-inbox .requestor-head {
        background: #f8fafc !important;
        border-color: #e2e8f0 !important;
    }
    html:not(.dark) .approval-inbox .requestor-avatar {
        background: #0f76a8 !important;
    }
    html:not(.dark) .approval-inbox .requestor-name,
    html:not(.dark) .approval-inbox .request-summary-title {
        color: #172033 !important;
    }
    html:not(.dark) .approval-inbox .requestor-meta,
    html:not(.dark) .approval-inbox .request-summary-note {
        color: #64748b !important;
    }
    html:not(.dark) .approval-inbox .requestor-item,
    html:not(.dark) .approval-inbox .request-summary-cell {
        border-color: #e2e8f0 !important;
    }
    html:not(.dark) .approval-inbox .requestor-label,
    html:not(.dark) .approval-inbox .summary-label {
        color: #64748b !important;
    }
    html:not(.dark) .approval-inbox .requestor-value,
    html:not(.dark) .approval-inbox .summary-value {
        color: #243244 !important;
    }
    html:not(.dark) .approval-inbox .request-pill {
        background: #eef7ff !important;
        border-color: #bae6fd !important;
        color: #0369a1 !important;
    }
    html:not(.dark) .approval-inbox .request-pill.is-warm {
        background: #fff7ed !important;
        border-color: #fed7aa !important;
        color: #9a3412 !important;
    }
    html:not(.dark) .approval-inbox .request-pill.is-violet {
        background: #f5f3ff !important;
        border-color: #ddd6fe !important;
        color: #6d28d9 !important;
    }
    html:not(.dark) .approval-inbox .request-summary-grid {
        border-color: #e2e8f0 !important;
    }
    html:not(.dark) .approval-inbox .approval-body-title,
    html:not(.dark) .approval-inbox .approval-date {
        color: #334155 !important;
    }
    html:not(.dark) .approval-inbox .approval-body-meta {
        color: #64748b !important;
    }
    @media (max-width: 768px) {
        .approval-inbox .approval-title {
            font-size: 1rem;
            line-height: 1.2;
        }
        .approval-inbox .approval-subtitle {
            font-size: 8px;
            letter-spacing: 0.08em;
        }
        .approval-inbox .navy-panel {
            padding: 16px 14px !important;
            align-items: flex-start;
            gap: 10px;
            flex-wrap: wrap;
        }
        .approval-inbox .navy-chip {
            margin-left: 0 !important;
        }
        .approval-inbox .navy-btn,
        .approval-inbox .navy-btn-danger,
        .approval-inbox .navy-btn-soft {
            width: 100%;
            min-height: 40px;
        }
        .approval-inbox .request-meta-block {
            padding: 9px 10px;
        }
        .damage-form-grid {
            grid-template-columns: 1fr;
        }
        .damage-form-field,
        .damage-form-field:nth-child(2n) {
            border-right: 0;
        }
        .approval-inbox .p-6 {
            padding: 12px !important;
        }
        .approval-inbox .mb-10,
        .approval-inbox .mb-6 {
            margin-bottom: 14px !important;
        }
        .approval-inbox .rounded-\[30px\] {
            border-radius: 18px !important;
        }
        .approval-inbox table.dataTable tbody td {
            padding-top: 10px !important;
            padding-bottom: 10px !important;
        }
        .approval-inbox td .flex.gap-2.justify-center,
        .approval-inbox td .flex.gap-4.justify-center,
        .approval-inbox td .flex.gap-2 {
            flex-direction: column;
            align-items: stretch;
            width: 100%;
        }
    }
    .approval-inbox {
        --approval-panel: #152033;
        --approval-panel-soft: #101827;
        --approval-line: rgba(148, 163, 184, 0.16);
        --approval-muted: #93a4bd;
        --approval-accent: #0284c7;
        --approval-accent-soft: rgba(139, 94, 60, 0.16);
    }
    .approval-inbox > .mb-4:first-child {
        margin-bottom: 16px !important;
        padding: 16px 18px;
        border: 1px solid var(--approval-line);
        border-radius: 16px;
        background:
            linear-gradient(135deg, rgba(139, 94, 60, 0.13), transparent 34%),
            #111a2a;
        box-shadow: none;
    }
    .approval-inbox .approval-title {
        font-size: 18px !important;
        letter-spacing: 0 !important;
        color: #f8fafc !important;
    }
    .approval-inbox .approval-subtitle {
        max-width: 760px;
        color: #9fb0c8 !important;
        letter-spacing: 0.12em !important;
    }
    .approval-inbox .approval-card {
        margin-bottom: 16px !important;
        overflow: hidden;
        border: 1px solid var(--approval-line) !important;
        border-radius: 16px !important;
        background: var(--approval-panel) !important;
        box-shadow: none !important;
    }
    .approval-inbox .approval-card > .navy-panel {
        padding: 14px 18px !important;
        border-bottom: 1px solid var(--approval-line) !important;
        background: rgba(15, 23, 42, 0.35) !important;
    }
    .approval-inbox .approval-card > .navy-panel > i {
        display: inline-flex;
        width: 30px;
        height: 30px;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: var(--approval-accent-soft);
        color: #38bdf8 !important;
        font-size: 13px !important;
    }
    .approval-inbox .approval-card > .navy-panel h4 {
        color: #f8fafc !important;
        font-size: 11px !important;
        letter-spacing: 0.08em !important;
    }
    .approval-inbox .navy-chip {
        min-width: 28px;
        border: 1px solid rgba(217, 179, 140, 0.24) !important;
        border-radius: 999px !important;
        background: var(--approval-accent) !important;
        color: #fffaf5 !important;
        text-align: center;
        box-shadow: none !important;
    }
    .approval-inbox .approval-card > .p-5 {
        padding: 12px 16px 16px !important;
    }
    .approval-inbox .dataTables_wrapper .dataTables_length,
    .approval-inbox .dataTables_wrapper .dataTables_filter {
        margin: 8px 0 12px;
        color: #dbe5f2 !important;
        font-weight: 900;
        letter-spacing: 0.04em;
    }
    .approval-inbox .dataTables_wrapper .dataTables_filter input,
    .approval-inbox .dataTables_wrapper .dataTables_length select {
        min-height: 34px !important;
        border: 1px solid rgba(148, 163, 184, 0.22) !important;
        border-radius: 9px !important;
        background: #0e1626 !important;
        color: #e2e8f0 !important;
        box-shadow: none !important;
    }
    .approval-inbox .dataTables_wrapper .dataTables_filter input {
        min-width: 188px;
        padding: 8px 10px !important;
    }
    .approval-inbox table.dataTable {
        overflow: hidden;
        border: 1px solid var(--approval-line) !important;
        border-radius: 12px;
        background: transparent !important;
        table-layout: auto !important;
    }
    .approval-inbox table.dataTable thead {
        background: #111a2a !important;
    }
    .approval-inbox table.dataTable thead th {
        padding: 13px 14px !important;
        border-right: 1px solid var(--approval-line) !important;
        border-bottom: 1px solid var(--approval-line) !important;
        color: #9fb8d8 !important;
        font-size: 9px !important;
        letter-spacing: 0.14em !important;
    }
    .approval-inbox table.dataTable tbody tr,
    .approval-inbox table.dataTable tbody tr:hover {
        background: #172236 !important;
        border: 0 !important;
    }
    .approval-inbox table.dataTable tbody td {
        padding: 14px !important;
        border-top: 1px solid rgba(148, 163, 184, 0.11) !important;
        color: #dbe5f2 !important;
    }
    .approval-inbox table.dataTable tbody td.dataTables_empty {
        background: #172236 !important;
    }
    .approval-inbox .approval-empty-state {
        min-height: 136px;
        gap: 12px;
        color: #c6d3e5 !important;
    }
    .approval-inbox .empty-visual {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        border-color: rgba(217, 179, 140, 0.18) !important;
        background: rgba(139, 94, 60, 0.1) !important;
        color: #38bdf8 !important;
    }
    .approval-inbox .approval-body-title,
    .approval-inbox .approval-date {
        color: #f8fafc !important;
    }
    .approval-inbox .approval-body-meta,
    .approval-inbox .approval-empty {
        color: var(--approval-muted) !important;
    }
    .approval-inbox .request-meta-block {
        margin-top: 10px !important;
        border: 1px solid rgba(148, 163, 184, 0.14) !important;
        border-radius: 10px !important;
        background: rgba(15, 23, 42, 0.34) !important;
    }
    .approval-inbox .request-meta-label {
        color: #8ea0b8 !important;
    }
    .approval-inbox .request-meta-value {
        color: #dbe5f2 !important;
    }
    .approval-inbox .approval-action-row {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 7px;
    }
    .approval-inbox .approval-action-btn {
        display: inline-flex;
        min-height: 32px;
        align-items: center;
        justify-content: center;
        gap: 7px;
        border-radius: 9px;
        border: 1px solid rgba(148, 163, 184, 0.22);
        background: #0e1626;
        padding: 8px 12px;
        color: #dbeafe;
        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.09em;
        text-transform: uppercase;
        transition: border-color 0.16s ease, background 0.16s ease, transform 0.16s ease;
    }
    .approval-inbox .approval-action-btn:hover {
        transform: translateY(-1px);
        border-color: rgba(217, 179, 140, 0.42) !important;
        background: #151f31 !important;
        color: #ffffff !important;
    }
    .approval-inbox .approval-action-view {
        border-color: rgba(96, 165, 250, 0.25);
        color: #bfdbfe;
    }
    .approval-inbox .approval-action-approve {
        border-color: rgba(34, 197, 94, 0.28);
        color: #bbf7d0;
    }
    .approval-inbox .approval-action-reject {
        border-color: rgba(248, 113, 113, 0.28);
        color: #fecaca;
    }
    .approval-inbox .requestor-card,
    .approval-inbox .request-summary-card {
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 14px;
        background: rgba(15, 23, 42, 0.28);
        overflow: hidden;
    }
    .approval-inbox .requestor-head {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 11px 12px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.14);
        background: rgba(15, 23, 42, 0.34);
    }
    .approval-inbox .requestor-avatar {
        display: inline-flex;
        width: 34px;
        height: 34px;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        border-radius: 10px;
        background: #0ea5e9;
        color: #ffffff;
        font-size: 13px;
        font-weight: 900;
    }
    .approval-inbox .requestor-name {
        color: #f8fafc;
        font-size: 12px;
        font-weight: 900;
        line-height: 1.2;
        text-transform: uppercase;
    }
    .approval-inbox .requestor-meta {
        color: #9fb2cb;
        font-size: 9px;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .approval-inbox .requestor-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .approval-inbox .requestor-item {
        min-height: 58px;
        padding: 10px 12px;
        border-right: 1px solid rgba(148, 163, 184, 0.12);
        border-bottom: 1px solid rgba(148, 163, 184, 0.12);
    }
    .approval-inbox .requestor-item:nth-child(2n) {
        border-right: 0;
    }
    .approval-inbox .requestor-label,
    .approval-inbox .summary-label {
        color: #8ea0b8;
        font-size: 8px;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }
    .approval-inbox .requestor-value,
    .approval-inbox .summary-value {
        margin-top: 4px;
        color: #e7eef8;
        font-size: 10px;
        font-weight: 850;
        line-height: 1.35;
    }
    .approval-inbox .request-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        padding: 10px 12px;
    }
    .approval-inbox .request-pill {
        display: inline-flex;
        align-items: center;
        min-height: 22px;
        border-radius: 999px;
        border: 1px solid rgba(96, 165, 250, 0.26);
        background: rgba(14, 165, 233, 0.1);
        padding: 4px 8px;
        color: #bae6fd;
        font-size: 8px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .approval-inbox .request-pill.is-warm {
        border-color: rgba(217, 179, 140, 0.28);
        background: rgba(139, 94, 60, 0.14);
        color: #fed7aa;
    }
    .approval-inbox .request-pill.is-violet {
        border-color: rgba(167, 139, 250, 0.28);
        background: rgba(124, 58, 237, 0.13);
        color: #ddd6fe;
    }
    .approval-inbox .return-review-list {
        display: grid;
        overflow: hidden;
        border: 1px solid rgba(148, 163, 184, 0.22);
        border-radius: 10px;
        background: rgba(15, 23, 42, 0.12);
    }
    .approval-inbox .return-review-card {
        display: grid;
        grid-template-columns: minmax(0, 1.35fr) minmax(120px, 0.65fr) minmax(140px, 0.7fr) minmax(150px, 0.55fr);
        gap: 0;
        align-items: center;
        border-bottom: 1px solid rgba(148, 163, 184, 0.18);
        background: rgba(15, 23, 42, 0.1);
    }
    .approval-inbox .return-review-card:last-child {
        border-bottom: 0;
    }
    .approval-inbox .return-review-cell {
        min-height: 92px;
        padding: 16px;
        border-right: 1px solid rgba(148, 163, 184, 0.16);
    }
    .approval-inbox .return-review-cell:last-child {
        border-right: 0;
    }
    .approval-inbox .return-review-kicker {
        color: #7f8da3;
        font-size: 8px;
        font-weight: 900;
        letter-spacing: 0.16em;
        text-transform: uppercase;
    }
    .approval-inbox .return-review-title {
        margin-top: 4px;
        color: #f8fafc;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
    }
    .approval-inbox .return-review-meta {
        margin-top: 4px;
        color: var(--approval-muted);
        font-size: 9px;
        font-weight: 800;
        line-height: 1.45;
        text-transform: uppercase;
    }
    .approval-inbox .return-review-value {
        margin-top: 5px;
        color: #e2e8f0;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
    }
    .approval-inbox .return-review-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        align-items: center;
        gap: 8px;
    }
    .approval-inbox .return-review-status {
        display: inline-flex;
        margin-top: 8px;
        border-left: 3px solid #38bdf8;
        padding-left: 8px;
        color: #38bdf8;
        font-size: 8px;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }
    html:not(.dark) .approval-inbox .return-review-card {
        background: #ffffff;
    }
    html:not(.dark) .approval-inbox .return-review-list {
        border-color: #d7e0ea;
        background: #f8fafc;
    }
    html:not(.dark) .approval-inbox .return-review-cell {
        border-color: #e2e8f0;
    }
    html:not(.dark) .approval-inbox .return-review-title {
        color: #1f2937;
    }
    html:not(.dark) .approval-inbox .return-review-value {
        color: #334155;
    }
    html:not(.dark) .approval-inbox .return-review-status {
        border-left-color: #9a6b43;
        color: #7c4f2d;
    }
    @media (max-width: 900px) {
        .approval-inbox .return-review-card {
            grid-template-columns: 1fr;
        }
        .approval-inbox .return-review-actions {
            justify-content: flex-start;
        }
    }
    .approval-inbox .request-summary-card {
        padding: 12px;
    }
    .approval-inbox .request-summary-title {
        color: #f8fafc;
        font-size: 12px;
        font-weight: 900;
        line-height: 1.25;
    }
    .approval-inbox .request-summary-note {
        margin-top: 4px;
        color: #9fb2cb;
        font-size: 10px;
        font-weight: 750;
        line-height: 1.35;
    }
    .approval-inbox .request-summary-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        margin-top: 10px;
        overflow: hidden;
        border: 1px solid rgba(148, 163, 184, 0.13);
        border-radius: 10px;
    }
    .approval-inbox .request-summary-cell {
        min-height: 52px;
        padding: 9px 10px;
        border-right: 1px solid rgba(148, 163, 184, 0.12);
        border-bottom: 1px solid rgba(148, 163, 184, 0.12);
    }
    .approval-inbox .request-summary-cell:nth-child(2n) {
        border-right: 0;
    }
    .approval-inbox .request-summary-cell.is-wide {
        grid-column: 1 / -1;
        border-right: 0;
    }
    .approval-inbox .dataTables_wrapper .dataTables_info,
    .approval-inbox .dataTables_wrapper .dataTables_paginate {
        margin-top: 12px;
        color: #92a4bd !important;
        font-weight: 800;
    }
    .approval-inbox .dataTables_wrapper .dataTables_paginate .paginate_button {
        border: 0 !important;
        background: transparent !important;
        color: #92a4bd !important;
        font-size: 10px !important;
        font-weight: 900 !important;
    }
    html:not(.dark) .approval-inbox > .mb-4:first-child,
    html:not(.dark) .approval-inbox .approval-card,
    html:not(.dark) .approval-inbox table.dataTable tbody tr,
    html:not(.dark) .approval-inbox table.dataTable tbody td.dataTables_empty {
        background: #ffffff !important;
    }
    html:not(.dark) .approval-inbox > .mb-4:first-child {
        background: var(--surface) !important;
        border-color: #e7e5e4 !important;
    }
    html:not(.dark) .approval-inbox .approval-title,
    html:not(.dark) .approval-inbox .approval-body-title,
    html:not(.dark) .approval-inbox .approval-date {
        color: #1f2937 !important;
    }
    html:not(.dark) .approval-inbox .approval-subtitle,
    html:not(.dark) .approval-inbox .approval-body-meta,
    html:not(.dark) .approval-inbox .approval-empty {
        color: #64748b !important;
    }
    html:not(.dark) .approval-inbox .approval-card > .navy-panel,
    html:not(.dark) .approval-inbox table.dataTable thead {
        background: #f8fafc !important;
        border-color: #e2e8f0 !important;
    }
    html:not(.dark) .approval-inbox .approval-card > .navy-panel h4 {
        color: #1f2937 !important;
    }
    html:not(.dark) .approval-inbox table.dataTable,
    html:not(.dark) .approval-inbox table.dataTable thead th,
    html:not(.dark) .approval-inbox table.dataTable tbody td {
        border-color: #e2e8f0 !important;
    }
    html:not(.dark) .approval-inbox table.dataTable thead th {
        color: #64748b !important;
    }
    html:not(.dark) .approval-inbox .dataTables_wrapper .dataTables_filter input,
    html:not(.dark) .approval-inbox .dataTables_wrapper .dataTables_length select {
        background: #ffffff !important;
        border-color: #cbd5e1 !important;
        color: #1f2937 !important;
    }
    html:not(.dark) .approval-inbox .dataTables_wrapper .dataTables_length,
    html:not(.dark) .approval-inbox .dataTables_wrapper .dataTables_filter,
    html:not(.dark) .approval-inbox .dataTables_wrapper .dataTables_info,
    html:not(.dark) .approval-inbox .dataTables_wrapper .dataTables_paginate {
        color: #475569 !important;
    }
    .approval-inbox .requestor-card {
        position: relative;
        border-radius: 12px !important;
        border: 1px solid rgba(148, 163, 184, 0.16) !important;
        border-left: 4px solid #38bdf8 !important;
        background: rgba(15, 23, 42, 0.2) !important;
        box-shadow: none !important;
    }
    .approval-inbox .requestor-head {
        padding: 10px 12px 8px !important;
        border-bottom: 0 !important;
        background: transparent !important;
    }
    .approval-inbox .requestor-avatar {
        width: 30px !important;
        height: 30px !important;
        border-radius: 999px !important;
        background: #38bdf8 !important;
        color: #082f49 !important;
        font-size: 12px !important;
    }
    .approval-inbox .requestor-name {
        font-size: 12px !important;
        color: #f8fafc !important;
    }
    .approval-inbox .requestor-meta {
        margin-top: 2px;
        font-size: 8px !important;
    }
    .approval-inbox .requestor-grid {
        display: block !important;
        padding: 0 12px 10px !important;
    }
    .approval-inbox .requestor-item {
        min-height: auto !important;
        padding: 8px 0 !important;
        border-right: 0 !important;
        border-bottom: 1px dashed rgba(148, 163, 184, 0.18) !important;
    }
    .approval-inbox .requestor-item:last-child {
        border-bottom: 0 !important;
    }
    .approval-inbox .requestor-value {
        display: grid;
        gap: 2px;
        font-size: 10px !important;
    }
    .approval-inbox .request-badges {
        padding: 0 12px 12px !important;
        gap: 5px !important;
    }
    .approval-inbox .request-pill {
        min-height: 20px !important;
        padding: 3px 7px !important;
        border-radius: 6px !important;
        font-size: 7px !important;
    }
    .approval-inbox .request-summary-card {
        position: relative;
        border-radius: 12px !important;
        border: 1px solid rgba(148, 163, 184, 0.16) !important;
        background: rgba(15, 23, 42, 0.18) !important;
        padding: 12px 12px 12px 16px !important;
        box-shadow: none !important;
    }
    .approval-inbox .request-summary-card::before {
        content: "";
        position: absolute;
        left: 0;
        top: 12px;
        bottom: 12px;
        width: 4px;
        border-radius: 0 999px 999px 0;
        background: #38bdf8;
    }
    .approval-inbox .request-summary-title {
        font-size: 12px !important;
    }
    .approval-inbox .request-summary-note {
        display: inline-flex;
        margin-top: 6px !important;
        border-radius: 8px;
        background: rgba(217, 179, 140, 0.1);
        padding: 5px 7px;
        color: #fed7aa !important;
        font-size: 9px !important;
        font-weight: 900 !important;
        text-transform: uppercase;
    }
    .approval-inbox .request-summary-card .request-meta-block {
        margin-top: 10px !important;
        border-radius: 9px !important;
        background: rgba(15, 23, 42, 0.24) !important;
    }
    .approval-inbox #requestsTable tbody td {
        vertical-align: middle !important;
    }
    .approval-inbox #requestsTable tbody td:nth-child(1) {
        width: 34% !important;
    }
    .approval-inbox #requestsTable tbody td:nth-child(2) {
        width: 34% !important;
    }
    .approval-inbox #requestsTable tbody td:nth-child(3) {
        width: 12% !important;
    }
    .approval-inbox #requestsTable tbody td:nth-child(4) {
        width: 20% !important;
    }
    .approval-inbox #requestsTable .approval-action-row {
        align-items: stretch;
        flex-direction: column;
        max-width: 120px;
        margin-inline: auto;
    }
    .approval-inbox #requestsTable .approval-action-btn {
        width: 100%;
        min-height: 30px;
    }
    html:not(.dark) .approval-inbox .requestor-card {
        background: #ffffff !important;
        border-color: #dbe3ee !important;
        border-left-color: #0f76a8 !important;
    }
    html:not(.dark) .approval-inbox .requestor-avatar {
        background: #0f76a8 !important;
        color: #ffffff !important;
    }
    html:not(.dark) .approval-inbox .requestor-name {
        color: #172033 !important;
    }
    html:not(.dark) .approval-inbox .requestor-item {
        border-bottom-color: #dbe3ee !important;
    }
    html:not(.dark) .approval-inbox .request-summary-card {
        background: #ffffff !important;
        border-color: #dbe3ee !important;
    }
    html:not(.dark) .approval-inbox .request-summary-card::before {
        background: #8b5e3c;
    }
    html:not(.dark) .approval-inbox .request-summary-note {
        background: #fff7ed !important;
        color: #9a3412 !important;
    }
    html:not(.dark) .approval-inbox .request-summary-card .request-meta-block {
        background: #f8fafc !important;
    }
    @media (max-width: 900px) {
        .approval-inbox #requestsTable .approval-action-row {
            flex-direction: row;
            max-width: none;
        }
    }
    .approval-inbox #requestsTable {
        border-collapse: separate !important;
        border-spacing: 0 10px !important;
    }
    .approval-inbox #requestsTable thead th {
        border-bottom: 0 !important;
    }
    .approval-inbox #requestsTable tbody tr {
        outline: 1px solid rgba(148, 163, 184, 0.13);
        outline-offset: -1px;
    }
    .approval-inbox #requestsTable tbody td {
        border-top: 0 !important;
        padding-block: 16px !important;
    }
    .approval-inbox #requestsTable tbody td:first-child {
        border-radius: 16px 0 0 16px;
    }
    .approval-inbox #requestsTable tbody td:last-child {
        border-radius: 0 16px 16px 0;
    }
    .approval-inbox .requestor-card {
        display: grid !important;
        grid-template-columns: minmax(160px, 0.9fr) minmax(190px, 1.1fr);
        gap: 12px;
        border: 0 !important;
        border-left: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        overflow: visible !important;
    }
    .approval-inbox .requestor-head {
        align-items: flex-start !important;
        gap: 9px !important;
        padding: 0 !important;
    }
    .approval-inbox .requestor-avatar {
        width: 28px !important;
        height: 28px !important;
        border-radius: 8px !important;
        background: #38bdf8 !important;
        color: #172033 !important;
    }
    .approval-inbox .requestor-name {
        font-size: 13px !important;
        line-height: 1.15 !important;
    }
    .approval-inbox .requestor-meta {
        max-width: 150px;
        white-space: normal;
        line-height: 1.35;
    }
    .approval-inbox .requestor-grid {
        display: grid !important;
        grid-template-columns: 1fr;
        gap: 8px;
        padding: 0 !important;
    }
    .approval-inbox .requestor-item {
        display: grid;
        grid-template-columns: 82px minmax(0, 1fr);
        gap: 10px;
        padding: 0 !important;
        border: 0 !important;
    }
    .approval-inbox .requestor-label,
    .approval-inbox .summary-label {
        font-size: 7px !important;
        line-height: 1.25;
    }
    .approval-inbox .requestor-value {
        margin-top: 0 !important;
    }
    .approval-inbox .request-badges {
        grid-column: 1 / -1;
        padding: 0 !important;
        margin-top: 2px;
    }
    .approval-inbox .request-pill {
        min-height: 18px !important;
        border-radius: 999px !important;
        border: 0 !important;
        background: rgba(148, 163, 184, 0.14) !important;
        color: #cbd5e1 !important;
    }
    .approval-inbox .request-pill.is-warm {
        background: rgba(217, 179, 140, 0.16) !important;
        color: #fed7aa !important;
    }
    .approval-inbox .request-pill.is-violet {
        background: rgba(129, 140, 248, 0.16) !important;
        color: #c7d2fe !important;
    }
    .approval-inbox .request-summary-card {
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        padding: 0 !important;
    }
    .approval-inbox .request-summary-card::before {
        display: none !important;
    }
    .approval-inbox .request-summary-title {
        position: relative;
        display: inline-flex;
        align-items: center;
        min-height: 24px;
        padding-left: 12px;
    }
    .approval-inbox .request-summary-title::before {
        content: "";
        position: absolute;
        left: 0;
        width: 4px;
        height: 18px;
        border-radius: 999px;
        background: #38bdf8;
    }
    .approval-inbox .request-summary-note {
        display: block !important;
        width: fit-content;
        max-width: 100%;
        margin-top: 8px !important;
        border-radius: 999px !important;
        background: rgba(148, 163, 184, 0.12) !important;
        color: #cbd5e1 !important;
        padding: 4px 8px !important;
        font-size: 8px !important;
    }
    .approval-inbox .request-summary-card .request-meta-block {
        margin-top: 10px !important;
        padding: 0 !important;
        border: 0 !important;
        background: transparent !important;
    }
    .approval-inbox .request-summary-card .request-meta-label {
        margin-bottom: 5px !important;
    }
    .approval-inbox .request-summary-card .request-meta-value {
        border-left: 1px solid rgba(148, 163, 184, 0.2);
        padding-left: 10px;
    }
    .approval-inbox #requestsTable .approval-action-row {
        flex-direction: row !important;
        max-width: 220px !important;
        gap: 6px !important;
    }
    .approval-inbox #requestsTable .approval-action-btn {
        width: auto !important;
        min-width: 64px !important;
        min-height: 28px !important;
        border-radius: 999px !important;
        padding: 7px 10px !important;
        font-size: 8px !important;
    }
    html:not(.dark) .approval-inbox #requestsTable tbody tr {
        outline-color: #e2e8f0;
    }
    html:not(.dark) .approval-inbox .requestor-card,
    html:not(.dark) .approval-inbox .request-summary-card {
        background: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
    }
    html:not(.dark) .approval-inbox .requestor-avatar {
        background: #e0f2fe !important;
        color: #0369a1 !important;
    }
    html:not(.dark) .approval-inbox .request-pill {
        background: #f1f5f9 !important;
        color: #475569 !important;
    }
    html:not(.dark) .approval-inbox .request-pill.is-warm {
        background: #fff7ed !important;
        color: #9a3412 !important;
    }
    html:not(.dark) .approval-inbox .request-pill.is-violet {
        background: #eef2ff !important;
        color: #4338ca !important;
    }
    html:not(.dark) .approval-inbox .request-summary-title::before {
        background: #0f76a8;
    }
    html:not(.dark) .approval-inbox .request-summary-note {
        background: #f1f5f9 !important;
        color: #475569 !important;
    }
    html:not(.dark) .approval-inbox .request-summary-card .request-meta-block {
        background: transparent !important;
        border: 0 !important;
    }
    html:not(.dark) .approval-inbox .request-summary-card .request-meta-value {
        border-left-color: #dbe3ee;
    }
    @media (max-width: 1100px) {
        .approval-inbox .requestor-card {
            grid-template-columns: 1fr;
        }
        .approval-inbox .requestor-meta {
            max-width: none;
        }
    }
    /* Final clean approval-pending layout override */
    .approval-inbox #requestsTable {
        border-collapse: collapse !important;
        border-spacing: 0 !important;
        table-layout: fixed !important;
    }
    .approval-inbox #requestsTable thead th {
        background: #101827 !important;
        border-bottom: 1px solid rgba(148, 163, 184, 0.22) !important;
        color: #cbd5e1 !important;
    }
    .approval-inbox #requestsTable tbody tr {
        outline: 0 !important;
        background: #172236 !important;
        border-bottom: 1px solid rgba(148, 163, 184, 0.14) !important;
    }
    .approval-inbox #requestsTable tbody tr:hover {
        background: #1b2940 !important;
    }
    .approval-inbox #requestsTable tbody td {
        padding: 14px 16px !important;
        vertical-align: top !important;
        border-top: 0 !important;
        border-right: 1px solid rgba(148, 163, 184, 0.12) !important;
    }
    .approval-inbox #requestsTable tbody td:first-child,
    .approval-inbox #requestsTable tbody td:last-child {
        border-radius: 0 !important;
    }
    .approval-inbox #requestsTable tbody td:last-child {
        border-right: 0 !important;
        vertical-align: middle !important;
    }
    .approval-inbox .requestor-card {
        display: block !important;
        border: 1px solid rgba(148, 163, 184, 0.16) !important;
        border-left: 3px solid #38bdf8 !important;
        border-radius: 10px !important;
        background: rgba(15, 23, 42, 0.28) !important;
        padding: 12px !important;
        overflow: hidden !important;
        box-shadow: none !important;
    }
    .approval-inbox .requestor-head {
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        padding: 0 0 10px !important;
        border-bottom: 1px solid rgba(148, 163, 184, 0.12) !important;
        background: transparent !important;
    }
    .approval-inbox .requestor-avatar {
        width: 32px !important;
        height: 32px !important;
        border-radius: 8px !important;
        background: #38bdf8 !important;
        color: #082f49 !important;
        font-size: 12px !important;
        font-weight: 900 !important;
    }
    .approval-inbox .requestor-name {
        color: #f8fafc !important;
        font-size: 12px !important;
        font-weight: 900 !important;
        line-height: 1.2 !important;
    }
    .approval-inbox .requestor-meta {
        max-width: none !important;
        margin-top: 2px !important;
        color: #94a3b8 !important;
        font-size: 8px !important;
        line-height: 1.35 !important;
        white-space: normal !important;
    }
    .approval-inbox .requestor-grid {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 8px !important;
        padding: 10px 0 0 !important;
    }
    .approval-inbox .requestor-item {
        display: block !important;
        min-height: auto !important;
        padding: 0 !important;
        border: 0 !important;
    }
    .approval-inbox .requestor-label,
    .approval-inbox .summary-label {
        color: #8ea0b8 !important;
        font-size: 8px !important;
        font-weight: 900 !important;
        letter-spacing: 0.1em !important;
        text-transform: uppercase !important;
    }
    .approval-inbox .requestor-value,
    .approval-inbox .summary-value {
        margin-top: 3px !important;
        color: #e2e8f0 !important;
        font-size: 10px !important;
        font-weight: 800 !important;
        line-height: 1.35 !important;
    }
    .approval-inbox .request-badges {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 5px !important;
        grid-column: auto !important;
        margin-top: 10px !important;
        padding: 0 !important;
    }
    .approval-inbox .request-pill {
        min-height: 20px !important;
        border: 1px solid rgba(148, 163, 184, 0.18) !important;
        border-radius: 999px !important;
        background: rgba(148, 163, 184, 0.1) !important;
        padding: 3px 7px !important;
        color: #cbd5e1 !important;
        font-size: 7px !important;
        font-weight: 900 !important;
    }
    .approval-inbox .request-pill.is-warm {
        border-color: rgba(217, 179, 140, 0.3) !important;
        background: rgba(217, 179, 140, 0.12) !important;
        color: #fed7aa !important;
    }
    .approval-inbox .request-pill.is-violet {
        border-color: rgba(129, 140, 248, 0.28) !important;
        background: rgba(129, 140, 248, 0.12) !important;
        color: #c7d2fe !important;
    }
    .approval-inbox .request-summary-card {
        display: block !important;
        border: 1px solid rgba(148, 163, 184, 0.16) !important;
        border-radius: 10px !important;
        background: rgba(15, 23, 42, 0.22) !important;
        padding: 12px !important;
        box-shadow: none !important;
    }
    .approval-inbox .request-summary-card::before,
    .approval-inbox .request-summary-title::before {
        display: none !important;
    }
    .approval-inbox .request-summary-title {
        display: block !important;
        min-height: 0 !important;
        padding-left: 0 !important;
        color: #f8fafc !important;
        font-size: 12px !important;
        font-weight: 900 !important;
        line-height: 1.25 !important;
    }
    .approval-inbox .request-summary-note {
        display: block !important;
        width: auto !important;
        margin-top: 5px !important;
        border-radius: 0 !important;
        background: transparent !important;
        padding: 0 !important;
        color: #94a3b8 !important;
        font-size: 10px !important;
        font-weight: 750 !important;
        line-height: 1.35 !important;
        text-transform: none !important;
    }
    .approval-inbox .request-summary-card .request-meta-block {
        margin-top: 10px !important;
        padding: 9px 10px !important;
        border: 1px solid rgba(148, 163, 184, 0.13) !important;
        border-radius: 8px !important;
        background: rgba(15, 23, 42, 0.24) !important;
    }
    .approval-inbox .request-summary-card .request-meta-label {
        margin-bottom: 4px !important;
    }
    .approval-inbox .request-summary-card .request-meta-value {
        border-left: 0 !important;
        padding-left: 0 !important;
    }
    .approval-inbox #requestsTable .approval-action-row {
        display: flex !important;
        flex-direction: column !important;
        align-items: stretch !important;
        justify-content: center !important;
        max-width: 104px !important;
        margin-inline: auto !important;
        gap: 7px !important;
    }
    .approval-inbox #requestsTable .approval-action-btn {
        width: 100% !important;
        min-width: 0 !important;
        min-height: 30px !important;
        border-radius: 8px !important;
        padding: 7px 9px !important;
        font-size: 8px !important;
    }
    html:not(.dark) .approval-inbox #requestsTable thead th {
        background: #f8fafc !important;
        color: #64748b !important;
        border-bottom-color: #e2e8f0 !important;
    }
    html:not(.dark) .approval-inbox #requestsTable tbody tr {
        background: #ffffff !important;
        border-bottom-color: #e2e8f0 !important;
    }
    html:not(.dark) .approval-inbox #requestsTable tbody tr:hover {
        background: #f8fafc !important;
    }
    html:not(.dark) .approval-inbox #requestsTable tbody td {
        border-right-color: #e2e8f0 !important;
    }
    html:not(.dark) .approval-inbox .requestor-card {
        background: #ffffff !important;
        border-color: #dbe3ee !important;
        border-left-color: #0f76a8 !important;
    }
    html:not(.dark) .approval-inbox .requestor-avatar {
        background: #e0f2fe !important;
        color: #0369a1 !important;
    }
    html:not(.dark) .approval-inbox .requestor-name,
    html:not(.dark) .approval-inbox .request-summary-title {
        color: #172033 !important;
    }
    html:not(.dark) .approval-inbox .requestor-meta,
    html:not(.dark) .approval-inbox .request-summary-note {
        color: #64748b !important;
    }
    html:not(.dark) .approval-inbox .requestor-label,
    html:not(.dark) .approval-inbox .summary-label {
        color: #64748b !important;
    }
    html:not(.dark) .approval-inbox .requestor-value,
    html:not(.dark) .approval-inbox .summary-value {
        color: #243244 !important;
    }
    html:not(.dark) .approval-inbox .request-summary-card {
        background: #ffffff !important;
        border-color: #dbe3ee !important;
    }
    html:not(.dark) .approval-inbox .request-summary-card .request-meta-block {
        background: #f8fafc !important;
        border-color: #dbe3ee !important;
    }
    html:not(.dark) .approval-inbox .request-pill {
        background: #f1f5f9 !important;
        border-color: #e2e8f0 !important;
        color: #475569 !important;
    }
    html:not(.dark) .approval-inbox .request-pill.is-warm {
        background: #fff7ed !important;
        border-color: #fed7aa !important;
        color: #9a3412 !important;
    }
    html:not(.dark) .approval-inbox .request-pill.is-violet {
        background: #eef2ff !important;
        border-color: #c7d2fe !important;
        color: #4338ca !important;
    }
    /* Compact pending approval rows */
    .approval-inbox #requestsTable tbody td {
        padding: 10px 12px !important;
        vertical-align: middle !important;
    }
    .approval-inbox .requestor-card {
        padding: 9px 10px !important;
        border-radius: 8px !important;
    }
    .approval-inbox .requestor-head {
        padding-bottom: 7px !important;
        gap: 8px !important;
    }
    .approval-inbox .requestor-avatar {
        width: 26px !important;
        height: 26px !important;
        border-radius: 7px !important;
        font-size: 10px !important;
    }
    .approval-inbox .requestor-name {
        font-size: 11px !important;
    }
    .approval-inbox .requestor-meta {
        font-size: 7px !important;
        line-height: 1.25 !important;
    }
    .approval-inbox .requestor-grid {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 8px !important;
        padding-top: 7px !important;
    }
    .approval-inbox .requestor-label,
    .approval-inbox .summary-label {
        font-size: 7px !important;
        letter-spacing: 0.08em !important;
    }
    .approval-inbox .requestor-value,
    .approval-inbox .summary-value {
        font-size: 9px !important;
        line-height: 1.25 !important;
    }
    .approval-inbox .requestor-value .text-\[9px\] {
        font-size: 7px !important;
        line-height: 1.2 !important;
    }
    .approval-inbox .request-badges {
        margin-top: 7px !important;
        gap: 4px !important;
    }
    .approval-inbox .request-pill {
        min-height: 17px !important;
        padding: 2px 6px !important;
        font-size: 6.5px !important;
    }
    .approval-inbox .request-summary-card {
        padding: 9px 10px !important;
        border-radius: 8px !important;
    }
    .approval-inbox .request-summary-title {
        font-size: 11px !important;
    }
    .approval-inbox .request-summary-note {
        margin-top: 3px !important;
        font-size: 8px !important;
        line-height: 1.25 !important;
    }
    .approval-inbox .request-summary-card .request-meta-block {
        margin-top: 7px !important;
        padding: 7px 8px !important;
    }
    .approval-inbox .request-summary-card .request-meta-value {
        font-size: 9px !important;
        line-height: 1.25 !important;
    }
    .approval-inbox .request-summary-card .request-meta-value .text-\[9px\] {
        font-size: 7px !important;
        line-height: 1.2 !important;
    }
    .approval-inbox .approval-date {
        font-size: 9px !important;
        white-space: nowrap !important;
    }
    .approval-inbox #requestsTable .approval-action-row {
        max-width: 92px !important;
        gap: 5px !important;
    }
    .approval-inbox #requestsTable .approval-action-btn {
        min-height: 26px !important;
        padding: 6px 8px !important;
        font-size: 7px !important;
        border-radius: 7px !important;
    }

    /* Fresh pending queue layout */
    .approval-inbox .pending-queue-card {
        overflow: hidden;
        border: 1px solid var(--approval-line) !important;
        border-radius: 16px !important;
        background: var(--approval-panel) !important;
        box-shadow: none !important;
    }
    .approval-inbox .pending-queue-header {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px;
        padding: 14px 18px;
        border-bottom: 1px solid var(--approval-line);
        background: rgba(15, 23, 42, 0.35);
    }
    .approval-inbox .pending-queue-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: rgba(56, 189, 248, 0.14);
        border: 1px solid rgba(125, 211, 252, 0.28);
        color: #bae6fd;
        flex: 0 0 auto;
    }
    .approval-inbox .pending-queue-title {
        color: #f8fafc;
        font-size: 12px;
        line-height: 1.15;
        font-weight: 900;
        letter-spacing: 0.16em;
        text-transform: uppercase;
    }
    .approval-inbox .pending-queue-subtitle {
        margin-top: 4px;
        color: #94a3b8;
        font-size: 9px;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .approval-inbox .pending-queue-count {
        margin-left: auto;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 32px;
        border-radius: 999px;
        border: 1px solid rgba(251, 191, 36, 0.28);
        background: rgba(251, 191, 36, 0.12);
        padding: 6px 12px;
        color: #fde68a;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .approval-inbox .pending-queue-body {
        padding: 12px 16px 16px;
    }
    .approval-inbox .pending-queue-card table.dataTable tbody tr {
        background: #172236 !important;
        border: 0 !important;
    }
    .approval-inbox .pending-queue-card table.dataTable tbody tr:hover {
        background: #1b2940 !important;
    }
    .approval-inbox .pending-queue-card table.dataTable tbody td {
        padding: 14px !important;
        border-top: 1px solid rgba(148, 163, 184, 0.11) !important;
        vertical-align: middle !important;
    }
    .approval-inbox .pending-queue-card .requestor-card,
    .approval-inbox .pending-queue-card .request-summary-card {
        background: rgba(2, 6, 23, 0.22) !important;
        border-color: rgba(148, 163, 184, 0.14) !important;
    }
    .approval-inbox .pending-queue-card .requestor-card {
        border-left-color: #fbbf24 !important;
    }
    .approval-inbox .pending-queue-card .approval-action-row {
        max-width: 112px !important;
    }
    .approval-inbox .pending-queue-card .approval-action-btn {
        min-height: 30px !important;
        border-radius: 999px !important;
        font-size: 7.5px !important;
    }
    html:not(.dark) .approval-inbox .pending-queue-card {
        background: #ffffff !important;
        border-color: #e2e8f0 !important;
        box-shadow: none !important;
    }
    html:not(.dark) .approval-inbox .pending-queue-header {
        border-bottom-color: #e2e8f0;
        background: #f8fafc;
    }
    html:not(.dark) .approval-inbox .pending-queue-icon {
        background: #e0f2fe;
        border-color: #bae6fd;
        color: #0369a1;
    }
    html:not(.dark) .approval-inbox .pending-queue-title {
        color: #172033;
    }
    html:not(.dark) .approval-inbox .pending-queue-subtitle {
        color: #64748b;
    }
    html:not(.dark) .approval-inbox .pending-queue-count {
        background: #fffbeb;
        border-color: #fde68a;
        color: #92400e;
    }
    html:not(.dark) .approval-inbox .pending-queue-card table.dataTable tbody tr {
        background: #ffffff !important;
    }
    html:not(.dark) .approval-inbox .pending-queue-card table.dataTable tbody tr:hover {
        background: #f8fafc !important;
    }
    html:not(.dark) .approval-inbox .pending-queue-card table.dataTable tbody td {
        border-color: #e2e8f0 !important;
    }
    @media (max-width: 768px) {
        .approval-inbox .pending-queue-count {
            margin-left: 0;
        }
        .approval-inbox .pending-queue-body {
            padding: 12px;
        }
    }

    /* Pending queue sizing — match history page */
    .approval-inbox .pending-queue-card {
        border-radius: 16px !important;
        box-shadow: none !important;
    }
    .approval-inbox .pending-queue-header {
        gap: 12px !important;
        padding: 14px 18px !important;
    }
    .approval-inbox .pending-queue-icon {
        width: 30px !important;
        height: 30px !important;
        border-radius: 10px !important;
    }
    .approval-inbox .pending-queue-icon i {
        font-size: 13px !important;
    }
    .approval-inbox .pending-queue-title {
        font-size: 11px !important;
        letter-spacing: 0.08em !important;
    }
    .approval-inbox .pending-queue-subtitle {
        margin-top: 3px !important;
        font-size: 9px !important;
        letter-spacing: 0.06em !important;
        line-height: 1.3 !important;
    }
    .approval-inbox .pending-queue-count {
        min-height: 28px !important;
        gap: 7px !important;
        padding: 5px 10px !important;
        font-size: 9px !important;
    }
    .approval-inbox .pending-queue-body {
        padding: 12px 16px 16px !important;
    }
    .approval-inbox .pending-queue-card table.dataTable thead th {
        padding: 13px 14px !important;
        font-size: 9px !important;
        letter-spacing: 0.14em !important;
    }
    .approval-inbox .pending-queue-card table.dataTable tbody td {
        padding: 14px !important;
        white-space: normal !important;
    }
    .approval-inbox .pending-queue-card .requestor-card,
    .approval-inbox .pending-queue-card .request-summary-card {
        padding: 6px 7px !important;
        border-radius: 6px !important;
    }
    .approval-inbox .pending-queue-card .requestor-head {
        gap: 6px !important;
        padding-bottom: 5px !important;
    }
    .approval-inbox .pending-queue-card .requestor-avatar {
        width: 22px !important;
        height: 22px !important;
        border-radius: 6px !important;
        font-size: 8px !important;
    }
    .approval-inbox .pending-queue-card .requestor-name,
    .approval-inbox .pending-queue-card .request-summary-title,
    .approval-inbox .pending-queue-card .approval-body-title {
        font-size: 9px !important;
        line-height: 1.18 !important;
    }
    .approval-inbox .pending-queue-card .requestor-meta,
    .approval-inbox .pending-queue-card .approval-body-meta,
    .approval-inbox .pending-queue-card .request-summary-note {
        font-size: 7.5px !important;
        line-height: 1.22 !important;
    }
    .approval-inbox .pending-queue-card .requestor-grid {
        gap: 5px !important;
        padding-top: 5px !important;
    }
    .approval-inbox .pending-queue-card .requestor-label,
    .approval-inbox .pending-queue-card .summary-label,
    .approval-inbox .pending-queue-card .request-meta-label {
        font-size: 6.5px !important;
        letter-spacing: 0.06em !important;
    }
    .approval-inbox .pending-queue-card .requestor-value,
    .approval-inbox .pending-queue-card .summary-value,
    .approval-inbox .pending-queue-card .request-meta-value {
        font-size: 7.5px !important;
        line-height: 1.22 !important;
    }
    .approval-inbox .pending-queue-card .request-summary-card .request-meta-block {
        margin-top: 5px !important;
        padding: 5px 6px !important;
        border-radius: 6px !important;
    }
    .approval-inbox .pending-queue-card .unit-detail-list {
        display: grid;
        gap: 4px;
        margin-top: 6px;
    }
    .approval-inbox .pending-queue-card .unit-detail-item {
        border: 1px solid rgba(148, 163, 184, 0.16);
        border-radius: 6px;
        background: rgba(15, 23, 42, 0.18);
        padding: 5px 6px;
    }
    .approval-inbox .pending-queue-card .unit-detail-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
        color: #e2e8f0;
        font-size: 7.5px;
        font-weight: 900;
        line-height: 1.2;
        text-transform: uppercase;
    }
    .approval-inbox .pending-queue-card .unit-detail-tag {
        flex: 0 0 auto;
        border-radius: 999px;
        background: rgba(129, 140, 248, 0.16);
        padding: 1px 5px;
        color: #c7d2fe;
        font-size: 6px;
        font-weight: 900;
    }
    .approval-inbox .pending-queue-card .unit-detail-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 3px 6px;
        margin-top: 4px;
        color: #94a3b8;
        font-size: 6.8px;
        font-weight: 800;
        line-height: 1.2;
        text-transform: uppercase;
    }
    .approval-inbox .pending-queue-card .unit-detail-grid span {
        min-width: 0;
        overflow-wrap: anywhere;
    }
    html:not(.dark) .approval-inbox .pending-queue-card .unit-detail-item {
        background: #f8fafc;
        border-color: #e2e8f0;
    }
    html:not(.dark) .approval-inbox .pending-queue-card .unit-detail-head {
        color: #172033;
    }
    html:not(.dark) .approval-inbox .pending-queue-card .unit-detail-tag {
        background: #eef2ff;
        color: #4338ca;
    }
    html:not(.dark) .approval-inbox .pending-queue-card .unit-detail-grid {
        color: #64748b;
    }
    .approval-inbox .pending-queue-card .request-badges {
        margin-top: 5px !important;
        gap: 3px !important;
    }
    .approval-inbox .pending-queue-card .request-pill {
        min-height: 15px !important;
        padding: 1px 5px !important;
        font-size: 6px !important;
    }
    .approval-inbox .pending-queue-card .approval-date {
        font-size: 8px !important;
        line-height: 1.25 !important;
    }
    .approval-inbox .pending-queue-card .approval-action-row {
        max-width: 86px !important;
        gap: 4px !important;
    }
    .approval-inbox .pending-queue-card .approval-action-btn {
        min-height: 22px !important;
        padding: 4px 7px !important;
        border-radius: 6px !important;
        font-size: 6.5px !important;
        letter-spacing: 0.07em !important;
    }
    .approval-inbox .pending-queue-card .mt-1 {
        margin-top: 2px !important;
    }
    .approval-inbox .pending-queue-card .mt-2 {
        margin-top: 4px !important;
    }
    html:not(.dark) .approval-inbox .pending-queue-card {
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06) !important;
    }

    /* Final compact approval inbox sizing */
    .approval-inbox {
        display: grid !important;
        gap: 8px !important;
        font-size: 8px !important;
    }
    .approval-inbox > .mb-4:first-child {
        min-height: 42px !important;
        margin-bottom: 0 !important;
        padding: 8px 12px !important;
        align-items: center !important;
        border-radius: 12px !important;
    }
    .approval-inbox .approval-title {
        font-size: 13px !important;
        line-height: 1 !important;
    }
    .approval-inbox .approval-subtitle {
        margin-top: 2px !important;
        font-size: 6.5px !important;
        line-height: 1 !important;
        letter-spacing: 0.08em !important;
    }
    .approval-inbox .approval-action-btn {
        min-height: 24px !important;
        padding: 5px 9px !important;
        border-radius: 7px !important;
        font-size: 7px !important;
        line-height: 1 !important;
    }
    .approval-inbox .pending-queue-card {
        margin-bottom: 8px !important;
        border-radius: 12px !important;
        box-shadow: none !important;
    }
    .approval-inbox .pending-queue-header {
        min-height: 38px !important;
        padding: 7px 9px !important;
        gap: 7px !important;
    }
    .approval-inbox .pending-queue-icon {
        width: 24px !important;
        height: 24px !important;
        border-radius: 8px !important;
    }
    .approval-inbox .pending-queue-icon i {
        font-size: 9px !important;
    }
    .approval-inbox .pending-queue-title {
        font-size: 11px !important;
        line-height: 1 !important;
        letter-spacing: 0.1em !important;
    }
    .approval-inbox .pending-queue-subtitle {
        margin-top: 2px !important;
        font-size: 6px !important;
        line-height: 1 !important;
        letter-spacing: 0.06em !important;
    }
    .approval-inbox .pending-queue-count {
        min-height: 20px !important;
        padding: 3px 7px !important;
        gap: 4px !important;
        font-size: 7px !important;
        line-height: 1 !important;
    }
    .approval-inbox .pending-queue-body {
        padding: 5px !important;
    }
    .approval-inbox .dataTables_wrapper {
        border-radius: 10px !important;
    }
    .approval-inbox .dataTables_wrapper .dataTables_length,
    .approval-inbox .dataTables_wrapper .dataTables_filter {
        min-height: 28px !important;
        padding: 5px 7px !important;
        font-size: 7.5px !important;
        gap: 6px !important;
    }
    .approval-inbox .dataTables_wrapper .dataTables_length label,
    .approval-inbox .dataTables_wrapper .dataTables_filter label,
    .approval-inbox .dataTables_wrapper .dataTables_info,
    .approval-inbox .dataTables_wrapper .dataTables_paginate {
        font-size: 8px !important;
        line-height: 1 !important;
    }
    .approval-inbox .dataTables_wrapper .dataTables_length select,
    .approval-inbox .dataTables_wrapper .dataTables_filter input {
        height: 24px !important;
        min-height: 24px !important;
        padding: 0 7px !important;
        border-radius: 7px !important;
        font-size: 8px !important;
    }
    .approval-inbox table.dataTable,
    .approval-inbox .pending-queue-card table.dataTable {
        border-radius: 9px !important;
        border-spacing: 0 6px !important;
    }
    .approval-inbox table.dataTable thead th,
    .approval-inbox .pending-queue-card table.dataTable thead th {
        padding: 7px 7px !important;
        font-size: 7.5px !important;
        line-height: 1 !important;
        letter-spacing: 0.04em !important;
    }
    .approval-inbox table.dataTable tbody td,
    .approval-inbox .pending-queue-card table.dataTable tbody td {
        padding: 5px 7px !important;
        font-size: 7.5px !important;
        line-height: 1.2 !important;
    }
    .approval-inbox table.dataTable tbody td.dataTables_empty {
        padding: 0 !important;
    }
    .approval-inbox .approval-empty-state {
        min-height: 70px !important;
        gap: 6px !important;
        font-size: 8px !important;
    }
    .approval-inbox .empty-visual {
        width: 28px !important;
        height: 28px !important;
        border-radius: 10px !important;
        margin-bottom: 0 !important;
    }
    .approval-inbox .requestor-card,
    .approval-inbox .request-summary-card {
        padding: 6px !important;
        border-radius: 9px !important;
    }
    .approval-inbox .requestor-head {
        gap: 6px !important;
        margin-bottom: 6px !important;
    }
    .approval-inbox .requestor-avatar {
        width: 22px !important;
        height: 22px !important;
        border-radius: 7px !important;
        font-size: 9px !important;
    }
    .approval-inbox .requestor-name,
    .approval-inbox .request-summary-title,
    .approval-inbox .approval-body-title {
        font-size: 8px !important;
        line-height: 1.1 !important;
    }
    .approval-inbox .requestor-meta,
    .approval-inbox .request-summary-note,
    .approval-inbox .approval-body-meta,
    .approval-inbox .approval-empty {
        font-size: 6.5px !important;
        line-height: 1.2 !important;
    }
    .approval-inbox .requestor-grid {
        gap: 4px !important;
    }
    .approval-inbox .requestor-item,
    .approval-inbox .request-summary-card .request-meta-block {
        padding: 5px !important;
        border-radius: 7px !important;
    }
    .approval-inbox .requestor-label,
    .approval-inbox .summary-label,
    .approval-inbox .request-meta-label {
        font-size: 6.5px !important;
        line-height: 1 !important;
        letter-spacing: 0.07em !important;
    }
    .approval-inbox .requestor-value,
    .approval-inbox .summary-value,
    .approval-inbox .request-meta-value {
        font-size: 7px !important;
        line-height: 1.2 !important;
    }
    .approval-inbox .request-badges {
        gap: 3px !important;
        margin-top: 6px !important;
    }
    .approval-inbox .request-pill {
        min-height: 15px !important;
        padding: 2px 5px !important;
        font-size: 6px !important;
        line-height: 1 !important;
    }
    .approval-inbox .approval-action-row {
        gap: 4px !important;
        max-width: 82px !important;
    }
    .approval-inbox #requestsTable .approval-action-btn,
    .approval-inbox .pending-queue-card .approval-action-btn {
        min-height: 21px !important;
        padding: 4px 6px !important;
        border-radius: 6px !important;
        font-size: 6.5px !important;
    }
    .approval-inbox .dataTables_info,
    .approval-inbox .dataTables_paginate {
        min-height: 28px !important;
        padding: 4px 7px !important;
    }
    .approval-inbox .paginate_button,
    .approval-inbox .adminit-page-link {
        min-height: 24px !important;
        height: 24px !important;
        min-width: 62px !important;
        padding: 0 8px !important;
        border-radius: 7px !important;
        font-size: 8px !important;
    }

    /* Ultra compact queue panels */
    .approval-inbox {
        gap: 6px !important;
    }
    .approval-inbox .pending-queue-card,
    .approval-inbox #approvalPending,
    .approval-inbox #approvalReturns,
    .approval-inbox #approvalDamages {
        margin-bottom: 6px !important;
    }
    .approval-inbox .pending-queue-header {
        min-height: 32px !important;
        padding: 5px 8px !important;
    }
    .approval-inbox .pending-queue-icon {
        width: 20px !important;
        height: 20px !important;
        border-radius: 6px !important;
    }
    .approval-inbox .pending-queue-icon i {
        font-size: 8px !important;
    }
    .approval-inbox .pending-queue-title {
        font-size: 10px !important;
        letter-spacing: 0.08em !important;
    }
    .approval-inbox .pending-queue-subtitle {
        font-size: 5.5px !important;
    }
    .approval-inbox .pending-queue-count {
        min-height: 18px !important;
        padding: 2px 6px !important;
        font-size: 6.5px !important;
    }
    .approval-inbox .pending-queue-body {
        padding: 4px !important;
    }
    .approval-inbox .dataTables_wrapper {
        border-radius: 8px !important;
    }
    .approval-inbox .dataTables_wrapper .dataTables_length,
    .approval-inbox .dataTables_wrapper .dataTables_filter {
        min-height: 24px !important;
        padding: 4px 6px !important;
    }
    .approval-inbox .dataTables_wrapper .dataTables_length select,
    .approval-inbox .dataTables_wrapper .dataTables_filter input {
        height: 22px !important;
        min-height: 22px !important;
        font-size: 7px !important;
    }
    .approval-inbox table.dataTable thead th,
    .approval-inbox .pending-queue-card table.dataTable thead th {
        padding: 6px 7px !important;
        font-size: 7px !important;
    }
    .approval-inbox table.dataTable tbody td,
    .approval-inbox .pending-queue-card table.dataTable tbody td {
        padding: 4px 7px !important;
    }
    .approval-inbox table.dataTable tbody td.dataTables_empty,
    .approval-inbox .pending-queue-card table.dataTable tbody td.dataTables_empty {
        height: 66px !important;
        min-height: 66px !important;
        padding: 0 !important;
    }
    .approval-inbox .approval-empty-state {
        min-height: 58px !important;
        gap: 4px !important;
        font-size: 7px !important;
    }
    .approval-inbox .empty-visual {
        width: 22px !important;
        height: 22px !important;
        border-radius: 8px !important;
    }
    .approval-inbox .empty-visual i {
        font-size: 8px !important;
    }
    .approval-inbox .dataTables_info,
    .approval-inbox .dataTables_paginate,
    .approval-inbox .adminit-table-footer {
        min-height: 24px !important;
        padding: 3px 6px !important;
        font-size: 7px !important;
    }
    .approval-inbox .paginate_button,
    .approval-inbox .adminit-page-link {
        min-height: 22px !important;
        height: 22px !important;
        min-width: 56px !important;
        font-size: 7px !important;
    }
    .approval-inbox .dataTables_info,
    .approval-inbox .dataTables_paginate,
    .approval-inbox .adminit-table-footer {
        display: none !important;
    }

    /* Approval Inbox smaller text */
    .approval-inbox .approval-title {
        font-size: 8px !important;
        font-weight: 800 !important;
    }
    .approval-inbox .approval-subtitle {
        font-size: 6px !important;
        font-weight: 700 !important;
    }
    .approval-inbox .approval-action-btn {
        font-size: 8px !important;
        font-weight: 800 !important;
    }
    .approval-inbox .pending-queue-title {
        font-size: 8px !important;
        font-weight: 900 !important;
    }
    .approval-inbox .pending-queue-subtitle {
        font-size: 6px !important;
        font-weight: 700 !important;
    }
    .approval-inbox .pending-queue-count {
        font-size: 8px !important;
        font-weight: 900 !important;
    }
    .approval-inbox .dataTables_wrapper .dataTables_length,
    .approval-inbox .dataTables_wrapper .dataTables_length label,
    .approval-inbox .dataTables_wrapper .dataTables_length select,
    .approval-inbox .dataTables_wrapper .dataTables_filter,
    .approval-inbox .dataTables_wrapper .dataTables_filter label,
    .approval-inbox .dataTables_wrapper .dataTables_filter input {
        font-size: 8px !important;
        font-weight: 800 !important;
    }
    .approval-inbox table.dataTable thead th,
    .approval-inbox .pending-queue-card table.dataTable thead th {
        font-size: 8px !important;
        font-weight: 800 !important;
    }
    .approval-inbox .approval-empty-state,
    .approval-inbox .approval-empty-state span {
        font-size: 8px !important;
        font-weight: 700 !important;
    }
    body .content-surface .approval-inbox :is(
        label, select, input, th, td, button, a,
        .pending-queue-title,
        .pending-queue-count,
        .approval-empty-state,
        .approval-empty-state span
    ) {
        font-size: 8px !important;
    }
    body .content-surface .approval-inbox .approval-title {
        font-size: 13px !important;
        letter-spacing: 0 !important;
    }
    body .content-surface .approval-inbox .approval-subtitle {
        font-size: 6.5px !important;
    }
    body .content-surface .approval-inbox .pending-queue-subtitle {
        font-size: 6px !important;
    }
    body .content-surface .approval-inbox > .mb-4:first-child {
        min-height: 74px !important;
        padding: 14px 22px !important;
        border-radius: 12px !important;
        border-left: 0 !important;
        background: linear-gradient(90deg, rgba(31, 41, 55, 0.98), rgba(30, 41, 59, 0.98)) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 16px !important;
    }
    body .content-surface .approval-inbox > .mb-4:first-child .approval-title {
        font-size: 22px !important;
        line-height: 1.05 !important;
        font-weight: 900 !important;
        letter-spacing: 0 !important;
        color: #ffffff !important;
    }
    body .content-surface .approval-inbox > .mb-4:first-child .approval-subtitle {
        margin-top: 7px !important;
        font-size: 10px !important;
        line-height: 1.2 !important;
        font-weight: 900 !important;
        letter-spacing: 0.18em !important;
        color: #b7c6d9 !important;
    }
    body .content-surface .approval-inbox > .mb-4:first-child .approval-action-btn {
        min-height: 42px !important;
        padding: 0 22px !important;
        border-radius: 10px !important;
        font-size: 14px !important;
        font-weight: 900 !important;
        letter-spacing: 0.02em !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        white-space: nowrap !important;
    }
    @media (max-width: 700px) {
        body .content-surface .approval-inbox > .mb-4:first-child {
            min-height: auto !important;
            padding: 12px 16px !important;
            align-items: flex-start !important;
        }
        body .content-surface .approval-inbox > .mb-4:first-child .approval-title {
            font-size: 18px !important;
        }
        body .content-surface .approval-inbox > .mb-4:first-child .approval-action-btn {
            min-height: 34px !important;
            font-size: 11px !important;
            padding: 0 14px !important;
        }
    }
    body .content-surface .approval-inbox :is(
        .dataTables_info,
        .dataTables_paginate,
        .dataTables_length,
        .adminit-table-footer,
        .adminit-table-pagination,
        .adminit-table-info
    ) {
        display: none !important;
    }
    body .content-surface .approval-inbox .approval-page-header {
        min-height: 96px !important;
        margin-bottom: 24px !important;
        padding: 18px 24px !important;
        border: 0 !important;
        border-left: 0 !important;
        border-radius: 13px !important;
        background: linear-gradient(90deg, rgba(31, 41, 55, 0.98), rgba(30, 41, 59, 0.98)) !important;
        box-shadow: none !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 18px !important;
    }
    body .content-surface .approval-inbox .approval-page-header .page-title-standard {
        color: #f8fafc !important;
        font-size: 28px !important;
        line-height: 1 !important;
        font-weight: 900 !important;
        letter-spacing: 0 !important;
    }
    body .content-surface .approval-inbox .approval-page-header .page-subtitle-standard {
        margin-top: 8px !important;
        max-width: none !important;
        color: #a8b6c8 !important;
        font-size: 13px !important;
        line-height: 1.25 !important;
        font-weight: 900 !important;
        letter-spacing: 0.2em !important;
        text-transform: uppercase !important;
    }
    body .content-surface .approval-inbox .approval-header-actions {
        display: flex !important;
        flex-wrap: wrap !important;
        align-items: center !important;
        justify-content: flex-end !important;
        gap: 8px !important;
        flex-shrink: 0 !important;
    }
    body .content-surface .approval-inbox .faulty-report-count-pill {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-height: 24px !important;
        padding: 5px 10px !important;
        border: 1px solid #bbf7d0 !important;
        border-radius: 999px !important;
        background: #dcfce7 !important;
        color: #166534 !important;
        font-size: 9px !important;
        font-weight: 900 !important;
        letter-spacing: 0.08em !important;
        line-height: 1 !important;
        text-transform: uppercase !important;
        white-space: nowrap !important;
    }
    body .content-surface .approval-inbox .approval-page-header .wt-btn {
        min-height: 52px !important;
        padding: 0 20px !important;
        border: 1px solid #2f5d86 !important;
        border-radius: 10px !important;
        background: transparent !important;
        color: #e5e7eb !important;
        box-shadow: none !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        font-size: 15px !important;
        font-weight: 900 !important;
        letter-spacing: 0 !important;
        line-height: 1 !important;
        text-transform: none !important;
        white-space: nowrap !important;
    }
    body .content-surface .approval-inbox .approval-page-header .wt-btn:hover {
        border-color: #3d79ad !important;
        background: rgba(59, 130, 246, 0.08) !important;
        color: #ffffff !important;
    }
    @media (max-width: 700px) {
        body .content-surface .approval-inbox .approval-page-header {
            align-items: flex-start !important;
            flex-direction: column !important;
            min-height: auto !important;
            padding: 0 2px 10px !important;
        }
        body .content-surface .approval-inbox .approval-header-actions {
            justify-content: flex-start !important;
        }
        body .content-surface .approval-inbox .faulty-report-count-pill,
        body .content-surface .approval-inbox .approval-page-header .wt-btn {
            min-height: 28px !important;
            font-size: 10px !important;
        }
    }

    body .content-surface .approval-inbox .pending-queue-card {
        padding: 0 !important;
        border: 1px solid var(--approval-line) !important;
        border-radius: 18px !important;
        background: var(--approval-panel) !important;
        box-shadow: none !important;
        margin-bottom: 22px !important;
    }

    body .content-surface .approval-inbox .pending-queue-header,
    html:not(.dark) body .content-surface .approval-inbox .pending-queue-header {
        min-height: 0 !important;
        margin: 0 !important;
        padding: 18px 22px !important;
        border: 0 !important;
        border-bottom: 1px solid var(--approval-line) !important;
        border-radius: 0 !important;
        background: rgba(15, 23, 42, 0.35) !important;
        box-shadow: none !important;
    }

    body .content-surface .approval-inbox .pending-queue-icon {
        width: 38px !important;
        height: 38px !important;
        border-radius: 12px !important;
    }

    body .content-surface .approval-inbox .pending-queue-icon i {
        font-size: 16px !important;
    }

    body .content-surface .approval-inbox .pending-queue-title {
        font-size: 14px !important;
        font-weight: 900 !important;
        letter-spacing: 0.06em !important;
        line-height: 1.2 !important;
    }

    body .content-surface .approval-inbox .pending-queue-subtitle {
        margin-top: 5px !important;
        font-size: 11px !important;
        font-weight: 800 !important;
        letter-spacing: 0.04em !important;
        line-height: 1.35 !important;
    }

    body .content-surface .approval-inbox .pending-queue-count {
        min-height: 34px !important;
        padding: 7px 13px !important;
        font-size: 11px !important;
    }

    body .content-surface .approval-inbox .pending-queue-body {
        padding: 18px 20px 20px !important;
    }

    body .content-surface .approval-inbox .pending-queue-card table.dataTable thead th,
    body .content-surface .approval-inbox table.dataTable thead th {
        padding: 15px 16px !important;
        font-size: 11px !important;
        line-height: 1 !important;
        letter-spacing: 0.1em !important;
    }

    body .content-surface .approval-inbox .pending-queue-card table.dataTable tbody td,
    body .content-surface .approval-inbox table.dataTable tbody td {
        padding: 16px !important;
    }

    body .content-surface .approval-inbox .requestor-name,
    body .content-surface .approval-inbox .request-summary-title,
    body .content-surface .approval-inbox .approval-body-title {
        font-size: 12px !important;
        line-height: 1.25 !important;
    }

    body .content-surface .approval-inbox .requestor-meta,
    body .content-surface .approval-inbox .request-summary-note,
    body .content-surface .approval-inbox .approval-body-meta {
        font-size: 10px !important;
        line-height: 1.45 !important;
    }

    body .content-surface .approval-inbox .requestor-label,
    body .content-surface .approval-inbox .summary-label,
    body .content-surface .approval-inbox .request-meta-label {
        font-size: 9px !important;
        letter-spacing: 0.08em !important;
    }

    body .content-surface .approval-inbox .requestor-value,
    body .content-surface .approval-inbox .summary-value,
    body .content-surface .approval-inbox .request-meta-value {
        font-size: 12px !important;
        line-height: 1.4 !important;
    }

    @media (max-width: 700px) {
        body .content-surface .approval-inbox .pending-queue-header {
            gap: 8px !important;
        }
    }

    /* Match standard WT admin panel sizing */
    .approval-inbox .pending-queue-card {
        border-radius: 18px !important;
        background: var(--approval-panel) !important;
        box-shadow: none !important;
        border: 1px solid var(--approval-line) !important;
        margin-bottom: 22px !important;
    }
    .approval-inbox .pending-queue-header {
        padding: 18px 22px !important;
        gap: 14px !important;
        background: rgba(15, 23, 42, 0.35) !important;
        border-bottom: 1px solid var(--approval-line) !important;
        min-height: unset !important;
    }
    .approval-inbox .pending-queue-icon {
        width: 38px !important;
        height: 38px !important;
        border-radius: 12px !important;
    }
    .approval-inbox .pending-queue-icon i {
        font-size: 16px !important;
    }
    .approval-inbox .pending-queue-title {
        font-size: 14px !important;
        font-weight: 900 !important;
        letter-spacing: 0.06em !important;
        line-height: 1.2 !important;
    }
    .approval-inbox .pending-queue-subtitle {
        margin-top: 5px !important;
        font-size: 11px !important;
        font-weight: 800 !important;
        letter-spacing: 0.04em !important;
        line-height: 1.35 !important;
    }
    .approval-inbox .pending-queue-count {
        min-height: 34px !important;
        padding: 7px 13px !important;
        gap: 8px !important;
        font-size: 11px !important;
        font-weight: 900 !important;
        line-height: 1 !important;
    }
    .approval-inbox .pending-queue-body {
        padding: 18px 20px 20px !important;
    }
    .approval-inbox table.dataTable,
    .approval-inbox .pending-queue-card table.dataTable {
        border-radius: 12px !important;
        border-spacing: 0 !important;
        border-collapse: collapse !important;
    }
    .approval-inbox table.dataTable thead th,
    .approval-inbox .pending-queue-card table.dataTable thead th {
        padding: 15px 16px !important;
        font-size: 11px !important;
        font-weight: 800 !important;
        letter-spacing: 0.1em !important;
        line-height: 1.1 !important;
    }
    .approval-inbox table.dataTable tbody td,
    .approval-inbox .pending-queue-card table.dataTable tbody td {
        padding: 16px !important;
        font-size: unset !important;
        line-height: unset !important;
    }
    .approval-inbox .pending-queue-card table.dataTable tbody td {
        border-top: 1px solid rgba(148, 163, 184, 0.11) !important;
        border-bottom: unset !important;
        border-left: unset !important;
        border-right: unset !important;
        border-radius: unset !important;
    }
    .approval-inbox .pending-queue-card table.dataTable tbody tr {
        background: #172236 !important;
        border: 0 !important;
        box-shadow: none !important;
        transform: none !important;
    }
    .approval-inbox .pending-queue-card table.dataTable tbody tr:hover {
        background: #1b2940 !important;
        transform: none !important;
    }
    .approval-inbox .approval-empty-state {
        min-height: 136px !important;
        gap: 12px !important;
        font-size: 11px !important;
        font-weight: 700 !important;
    }
    .approval-inbox .empty-visual {
        width: 44px !important;
        height: 44px !important;
        border-radius: 14px !important;
    }
    .approval-inbox .empty-visual i {
        font-size: 16px !important;
    }
    .approval-inbox .requestor-name,
    .approval-inbox .request-summary-title,
    .approval-inbox .approval-body-title {
        font-size: 12px !important;
        font-weight: 800 !important;
        line-height: 1.25 !important;
    }
    .approval-inbox .requestor-meta,
    .approval-inbox .request-summary-note,
    .approval-inbox .approval-body-meta {
        font-size: 10px !important;
        line-height: 1.45 !important;
    }
    .approval-inbox .requestor-label,
    .approval-inbox .summary-label,
    .approval-inbox .request-meta-label {
        font-size: 9px !important;
        letter-spacing: 0.08em !important;
        line-height: 1.1 !important;
    }
    .approval-inbox .requestor-value,
    .approval-inbox .summary-value,
    .approval-inbox .request-meta-value {
        font-size: 12px !important;
        font-weight: 800 !important;
        line-height: 1.4 !important;
    }
    .approval-inbox .requestor-avatar {
        width: 36px !important;
        height: 36px !important;
        border-radius: 999px !important;
        font-size: 14px !important;
    }
    .approval-inbox .requestor-card,
    .approval-inbox .request-summary-card {
        border-radius: 12px !important;
        padding: 0 !important;
    }
    .approval-inbox .requestor-item,
    .approval-inbox .request-summary-card .request-meta-block {
        padding: 12px 14px !important;
        border-radius: unset !important;
    }
    .approval-inbox .request-pill {
        min-height: 24px !important;
        padding: 4px 9px !important;
        font-size: 9px !important;
        line-height: 1 !important;
        border-radius: 8px !important;
    }
    .approval-inbox .request-badges {
        gap: 7px !important;
        margin-top: 8px !important;
        padding: 0 14px 14px !important;
    }
    .approval-inbox .approval-action-btn {
        min-height: 36px !important;
        padding: 8px 12px !important;
        border-radius: 10px !important;
        font-size: 10px !important;
        font-weight: 900 !important;
        letter-spacing: 0.07em !important;
        line-height: 1 !important;
    }
    .approval-inbox .approval-action-row {
        gap: 8px !important;
        max-width: 138px !important;
    }
    .approval-inbox .dataTables_wrapper .dataTables_length,
    .approval-inbox .dataTables_wrapper .dataTables_filter {
        font-size: 10px !important;
        font-weight: 900 !important;
        line-height: 1 !important;
    }
    .approval-inbox .dataTables_wrapper .dataTables_length select,
    .approval-inbox .dataTables_wrapper .dataTables_filter input {
        height: 34px !important;
        min-height: 34px !important;
        padding: 6px 10px !important;
        border-radius: 9px !important;
        font-size: 10px !important;
        font-weight: 700 !important;
    }
    .approval-inbox .dataTables_info,
    .approval-inbox .dataTables_paginate,
    .approval-inbox .adminit-table-footer {
        display: block !important;
        min-height: unset !important;
        padding: unset !important;
        font-size: 10px !important;
        font-weight: 800 !important;
        color: #92a4bd !important;
        margin-top: 12px !important;
    }
    .approval-inbox .paginate_button,
    .approval-inbox .adminit-page-link {
        min-height: 32px !important;
        height: 32px !important;
        min-width: 72px !important;
        font-size: 10px !important;
        padding: 0 10px !important;
        border-radius: 8px !important;
    }
    html:not(.dark) .approval-inbox .pending-queue-card {
        background: #ffffff !important;
        border-color: #e2e8f0 !important;
        box-shadow: none !important;
    }
    html:not(.dark) .approval-inbox .pending-queue-header {
        background: #f8fafc !important;
        border-bottom-color: #e2e8f0 !important;
    }
    html:not(.dark) .approval-inbox .pending-queue-title {
        color: #1f2937 !important;
    }
    html:not(.dark) .approval-inbox .pending-queue-subtitle {
        color: #64748b !important;
    }
    html:not(.dark) .approval-inbox .pending-queue-card table.dataTable tbody tr {
        background: #ffffff !important;
    }
    html:not(.dark) .approval-inbox .pending-queue-card table.dataTable tbody tr:hover {
        background: #f8fafc !important;
    }
    html:not(.dark) .approval-inbox .pending-queue-card table.dataTable tbody td {
        border-color: #e2e8f0 !important;
    }
    .approval-inbox {
        display: block !important;
        gap: unset !important;
        font-size: unset !important;
    }

    html:not(.dark) body .content-surface .approval-inbox,
    html[data-theme="light"] body .content-surface .approval-inbox {
        --approval-panel: #ffffff;
        --approval-panel-soft: #f8fafc;
        --approval-line: #d8e1ed;
        color: #172033 !important;
    }

    html:not(.dark) body .content-surface .approval-inbox .approval-page-header,
    html[data-theme="light"] body .content-surface .approval-inbox .approval-page-header {
        background: #ffffff !important;
        border: 1px solid #d8e1ed !important;
    }

    html:not(.dark) body .content-surface .approval-inbox .approval-page-header .page-title-standard,
    html[data-theme="light"] body .content-surface .approval-inbox .approval-page-header .page-title-standard {
        color: #172033 !important;
    }

    html:not(.dark) body .content-surface .approval-inbox .approval-page-header .page-subtitle-standard,
    html[data-theme="light"] body .content-surface .approval-inbox .approval-page-header .page-subtitle-standard {
        color: #64748b !important;
    }

    html:not(.dark) body .content-surface .approval-inbox .approval-page-header .wt-btn,
    html[data-theme="light"] body .content-surface .approval-inbox .approval-page-header .wt-btn {
        border-color: #cbd5e1 !important;
        background: #ffffff !important;
        color: #334155 !important;
    }

    html:not(.dark) body .content-surface .approval-inbox .approval-page-header .wt-btn:hover,
    html[data-theme="light"] body .content-surface .approval-inbox .approval-page-header .wt-btn:hover {
        border-color: #93c5fd !important;
        background: #eff6ff !important;
        color: #1e3a8a !important;
    }

    html:not(.dark) body .content-surface .approval-inbox .pending-queue-card,
    html[data-theme="light"] body .content-surface .approval-inbox .pending-queue-card,
    html:not(.dark) body .content-surface .approval-inbox .pending-queue-body,
    html[data-theme="light"] body .content-surface .approval-inbox .pending-queue-body,
    html:not(.dark) body .content-surface .approval-inbox .dataTables_wrapper,
    html[data-theme="light"] body .content-surface .approval-inbox .dataTables_wrapper {
        background: #ffffff !important;
        border-color: #d8e1ed !important;
        color: #172033 !important;
    }

    html:not(.dark) body .content-surface .approval-inbox .pending-queue-header,
    html[data-theme="light"] body .content-surface .approval-inbox .pending-queue-header {
        background: #ffffff !important;
        border-bottom-color: #d8e1ed !important;
    }

    html:not(.dark) body .content-surface .approval-inbox table.dataTable thead th,
    html[data-theme="light"] body .content-surface .approval-inbox table.dataTable thead th {
        background: #f4f7fb !important;
        border-color: #d8e1ed !important;
        color: #64748b !important;
    }

    html:not(.dark) body .content-surface .approval-inbox table.dataTable tbody td,
    html[data-theme="light"] body .content-surface .approval-inbox table.dataTable tbody td {
        background: #ffffff !important;
        border-color: #e2e8f0 !important;
        color: #1f2937 !important;
    }

    html:not(.dark) body .content-surface .approval-inbox .approval-empty-state,
    html[data-theme="light"] body .content-surface .approval-inbox .approval-empty-state,
    html:not(.dark) body .content-surface .approval-inbox .approval-empty-state span,
    html[data-theme="light"] body .content-surface .approval-inbox .approval-empty-state span {
        color: #94a3b8 !important;
    }

    html:not(.dark) body .content-surface .approval-inbox .empty-visual,
    html[data-theme="light"] body .content-surface .approval-inbox .empty-visual {
        background: #f8fafc !important;
        border-color: #e2e8f0 !important;
        color: #38bdf8 !important;
    }
</style>
@endpush

@section('content')
@php
    $pendingApprovalTotal = $pendingRequests->count() + $pendingReturns->count() + $pendingDamageReports->count();

    $resolveReportedBy = function ($request) {
        $submittedBy = $request->user;

        if (
            $request->submitToAdmin
            && (int) $request->submit_to_admin_id !== (int) $request->user_id
            && empty($request->handled_by)
            && $request->status === 'Pending IT Approval'
        ) {
            $submittedBy = $request->submitToAdmin;
        }

        if (! $submittedBy && $request->submitToAdmin) {
            $submittedBy = $request->submitToAdmin;
        }

        $role = strtolower((string) ($submittedBy->wt_role ?? 'user'));
        $roleLabel = match ($role) {
            'admin' => 'Executive',
            'admin_it' => 'ICT',
            default => 'Executive',
        };

        return [
            'name' => strtoupper((string) (($submittedBy->full_name ?? null) ?: ($submittedBy->username ?? null) ?: ($request->full_name ?? '-'))),
            'staff_id' => strtoupper((string) (($submittedBy->staff_id ?? null) ?: ($request->staff_id ?? '-'))),
            'department' => strtoupper((string) (($submittedBy->department ?? null) ?: ($request->department ?? '-'))),
            'role' => $roleLabel,
        ];
    };

    $resolveExecutiveOwner = function ($record) {
        $executive = $record->submitToAdmin ?? null;

        return [
            'name' => strtoupper((string) (($executive->full_name ?? null) ?: ($executive->username ?? null) ?: '-')),
            'staff_id' => strtoupper((string) (($executive->staff_id ?? null) ?: '-')),
            'department' => strtoupper((string) (($executive->department ?? null) ?: '-')),
        ];
    };
@endphp

@if(session('success'))
<div class="mb-6 bg-emerald-50 text-emerald-700 px-6 py-4 rounded-2xl border border-emerald-200 flex items-center gap-4 shadow-sm">
    <i class="fas fa-check-circle text-xl"></i>
    <span class="font-bold tracking-wide">{{ session('success') }}</span>
</div>
@endif
@if($errors->any())
<div class="mb-6 bg-red-50 text-red-700 px-6 py-4 rounded-2xl border border-red-200 flex items-center gap-4 shadow-sm">
    <i class="fas fa-circle-exclamation text-xl"></i>
    <span class="font-bold tracking-wide">{{ $errors->first() }}</span>
</div>
@endif

<div class="approval-inbox">
<div class="approval-page-header page-header-block flex flex-col md:flex-row md:items-start md:justify-between gap-4">
    <div>
        <h3 class="page-title-standard">Approval Inbox</h3>
        <p class="page-subtitle-standard">
            {{ ($userRole ?? auth('wt')->user()->wt_role) === 'admin_it' ? 'Manage ICT approvals, replacement requests, returns, and forwarded damage reports' : 'Review requests, returns, and damage reports before forwarding them to ICT' }}
        </p>
    </div>
    <div class="approval-header-actions flex flex-wrap items-center gap-2">
        <span class="faulty-report-count-pill">{{ $pendingApprovalTotal }} Pending</span>
        @if(($userRole ?? auth('wt')->user()->wt_role) === 'admin_it')
            <a href="{{ route('wt.admin.requests.history') }}" class="wt-btn wt-btn-soft">
                <i class="fa-solid fa-clock-rotate-left text-[15px]"></i>
                History
            </a>
        @endif
    </div>
</div>

<!-- PENDING BORROW REQUESTS -->
<div id="approvalPending" class="approval-card pending-queue-card mb-8">
    <div class="pending-queue-header">
        <span class="pending-queue-icon"><i class="fa-solid fa-bell text-lg"></i></span>
        <div>
            <h4 class="pending-queue-title">{{ ($userRole ?? auth('wt')->user()->wt_role) === 'admin_it' ? 'Pending IT Approval Requests' : 'Pending Executive Approval Requests' }}</h4>
            <p class="pending-queue-subtitle">Review new walkie talkie applications awaiting action</p>
        </div>
        <span class="pending-queue-count"><i class="fa-solid fa-hourglass-half"></i>{{ $pendingRequests->count() }} Pending</span>
    </div>
    <div class="pending-queue-body">
        <table id="requestsTable" class="w-full text-left display nowrap">
            <thead class="bg-stone-50 text-stone-400 text-[10px] uppercase font-black tracking-[0.15em]">
                <tr>
                    <th class="px-4 py-4">Requestor</th>
                    <th class="px-4 py-4">Event Details</th>
                    <th class="px-4 py-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-50">
                @forelse($pendingRequests as $req)
                @php
                    $isTemporaryRequest = $req->request_type === 'temporary_walkie_talkie';
                    $reportedBy = $resolveReportedBy($req);
                    $executiveOwner = $resolveExecutiveOwner($req);
                    $requestQty = max(1, (int) ($req->quantity ?: 1));
                    $unitDetails = is_array($req->pic_details ?? null) ? $req->pic_details : [];
                    $unitDisplayCount = max($requestQty, count($unitDetails));
                    $sameSubmitterAndExecutive = $reportedBy['staff_id'] === $executiveOwner['staff_id']
                        && $reportedBy['name'] === $executiveOwner['name'];
                @endphp
                <tr class="transition">
                    <td class="px-4 py-4">
                        <div class="requestor-card">
                            <div class="requestor-head">
                                <div class="requestor-avatar">{{ strtoupper(\Illuminate\Support\Str::substr($req->full_name ?: 'R', 0, 1)) }}</div>
                                <div class="min-w-0">
                                    <div class="requestor-name truncate">{{ $req->full_name ?: '-' }}</div>
                                    <div class="requestor-meta">Request #{{ str_pad($req->id, 5, '0', STR_PAD_LEFT) }} &middot; {{ $req->department ?: '-' }}</div>
                                    <div class="requestor-meta">
                                        @if($isTemporaryRequest)
                                            {{ $req->request_date ? \Carbon\Carbon::parse($req->request_date)->format('d M Y') : '-' }}
                                            -
                                            {{ $req->end_date ? \Carbon\Carbon::parse($req->end_date)->format('d M Y') : '-' }}
                                        @else
                                            {{ $req->request_date ? \Carbon\Carbon::parse($req->request_date)->format('d M Y') : '-' }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="requestor-grid">
                                @if($sameSubmitterAndExecutive)
                                <div class="requestor-item md:col-span-2">
                                    <div class="requestor-label">Submitted / Executive</div>
                                    <div class="requestor-value">
                                        <span class="font-black">{{ $reportedBy['name'] }}</span>
                                        <div class="text-[9px] uppercase">ID {{ $reportedBy['staff_id'] }} &middot; {{ $reportedBy['department'] }}</div>
                                    </div>
                                </div>
                                @else
                                <div class="requestor-item">
                                    <div class="requestor-label">Submitted By</div>
                                    <div class="requestor-value">
                                        <span class="font-black">{{ $reportedBy['name'] }}</span>
                                        <div class="text-[9px] uppercase">{{ $reportedBy['role'] }} &middot; ID {{ $reportedBy['staff_id'] }}</div>
                                        <div class="text-[9px] uppercase">{{ $reportedBy['department'] }}</div>
                                    </div>
                                </div>
                                <div class="requestor-item">
                                    <div class="requestor-label">Executive Owner</div>
                                    <div class="requestor-value">
                                        <span class="font-black">{{ $executiveOwner['name'] }}</span>
                                        <div class="text-[9px] uppercase">ID {{ $executiveOwner['staff_id'] }}</div>
                                        <div class="text-[9px] uppercase">{{ $executiveOwner['department'] }}</div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="request-badges">
                                <span class="request-pill is-warm">{{ strtoupper($req->ownership_type ?? 'INDIVIDUAL') }}</span>
                                <span class="request-pill">{{ strtoupper($reportedBy['role']) }} Submitted</span>
                                <span class="request-pill is-violet">{{ $isTemporaryRequest ? 'Temporary' : 'Long Term' }}</span>
                                @if($req->bay_from)
                                    <span class="request-pill is-warm">Bay: {{ $req->bay_from }}</span>
                                @endif
                            </div>
                            <div class="mt-3">
                                @include('wt.partials.approval-flow', ['request' => $req, 'compact' => true])
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <div class="request-summary-card">
                            <div class="request-summary-title">{{ $req->event_name ?: 'General Request' }}</div>
                            <div class="request-summary-note">{{ $req->justifications ?: 'No remark provided.' }}</div>
                            <div class="request-meta-block !mt-3">
                            <div class="request-meta-label">{{ $isTemporaryRequest ? 'Quantity & ICT Handling' : 'ICT Handover Items' }}</div>
                            <div class="request-meta-value">
                                @if($isTemporaryRequest)
                                    @php
                                        $requestDays = max(1, (int) ($req->duration_days ?: 1));
                                    @endphp
                                    Qty Requested: {{ $requestQty }} {{ \Illuminate\Support\Str::plural('unit', $requestQty) }}.<br>
                                    Period:
                                    {{ $req->request_date ? \Carbon\Carbon::parse($req->request_date)->format('d M Y') : '-' }}
                                    -
                                    {{ $req->end_date ? \Carbon\Carbon::parse($req->end_date)->format('d M Y') : '-' }}
                                    · {{ $requestDays }} {{ \Illuminate\Support\Str::plural('day', $requestDays) }}.
                                @else
                                    Qty Requested: {{ $requestQty }} {{ \Illuminate\Support\Str::plural('unit', $requestQty) }}.<br>
                                    {{ $req->accessories ?: 'To be selected by ICT' }}
                                @endif

                                @if($unitDisplayCount > 1 || ! empty($unitDetails))
                                    <div class="unit-detail-list">
                                        @for($unitIndex = 0; $unitIndex < $unitDisplayCount; $unitIndex++)
                                            @php
                                                $pic = $unitDetails[$unitIndex] ?? [];
                                            @endphp
                                            <div class="unit-detail-item">
                                                <div class="unit-detail-head">
                                                    <span>Unit {{ $unitIndex + 1 }}: {{ $pic['name'] ?? ($req->full_name ?: '-') }}</span>
                                                    <span class="unit-detail-tag">{{ strtoupper($pic['ownership_type'] ?? ($req->ownership_type ?? 'INDIVIDUAL')) }}</span>
                                                </div>
                                                <div class="unit-detail-grid">
                                                    <span>Dept: {{ $pic['department'] ?? ($req->department ?: '-') }}</span>
                                                    <span>Phone: {{ $pic['phone_no'] ?? '-' }}</span>
                                                    <span>Sector: {{ $pic['sector'] ?? ($req->sector ?: '-') }}</span>
                                                    <span>Location: {{ $pic['location'] ?? ($req->location ?: '-') }}</span>
                                                    @if(! empty($pic['shared_with']) || ! empty($req->shared_with))
                                                        <span>Shared: {{ $pic['shared_with'] ?? $req->shared_with }}</span>
                                                    @endif
                                                    @if(! empty($pic['pickup_person']) || ! empty($pic['pickup_phone_no']) || ! empty($req->pickup_representative_name))
                                                        <span>Pickup: {{ $pic['pickup_person'] ?? ($req->pickup_representative_name ?: '-') }}{{ ! empty($pic['pickup_phone_no']) ? ' / ' . $pic['pickup_phone_no'] : '' }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endfor
                                    </div>
                                @endif
                            </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4 text-center">
                        <div class="approval-action-row">
                            <button type="button" onclick="openRequestFormModal('requestFormModal-{{ $req->id }}')" class="approval-action-btn approval-action-view">View Form</button>
                            @if(($userRole ?? auth('wt')->user()->wt_role) === 'admin_it')
                                @php
                                    $approvalPayload = [
                                        'id' => $req->id,
                                        'full_name' => $req->full_name,
                                        'accessories' => $req->accessories,
                                        'request_type' => $req->request_type,
                                        'quantity' => max(1, (int) ($req->quantity ?: 1)),
                                        'request_date' => $req->request_date ? \Carbon\Carbon::parse($req->request_date)->format('Y-m-d') : null,
                                        'end_date' => $req->end_date ? \Carbon\Carbon::parse($req->end_date)->format('Y-m-d') : null,
                                        'duration_days' => max(1, (int) ($req->duration_days ?: 1)),
                                        'pic_details' => $req->pic_details ?? [],
                                        'ownership_type' => strtoupper($req->ownership_type ?? 'INDIVIDUAL'),
                                        'shared_with' => $req->shared_with,
                                        'department' => $req->department,
                                        'bay_from' => $req->bay_from,
                                        'position' => $req->position,
                                        'justifications' => $req->justifications,
                                        'pickup_method' => $req->pickup_method,
                                        'pickup_representative_name' => $req->pickup_representative_name,
                                        'requested_pickup_at' => $req->requested_pickup_at ? \Carbon\Carbon::parse($req->requested_pickup_at)->format('Y-m-d H:i') : null,
                                    ];
                                @endphp
                                <button type="button" onclick='openApproveModal(@json($approvalPayload))' class="approval-action-btn approval-action-approve">Assign WT</button>
                            @else
                                <form action="{{ route('wt.admin.requests.forwardToIT', $req->id) }}" method="POST" onsubmit="return confirm('Approve this request and forward it to ICT?');">
                                    @csrf
                                    <button type="submit" class="approval-action-btn approval-action-approve">Approve</button>
                                </form>
                            @endif
                            <button type="button" class="approval-action-btn approval-action-reject" onclick='openRejectRequestModal(@json($req->id), @json($req->full_name))'>Reject</button>
                        </div>
                    </td>
                </tr>
                @empty
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@foreach($pendingRequests as $req)
@php
    $isTemporaryRequest = $req->request_type === 'temporary_walkie_talkie';
    $reportedBy = $resolveReportedBy($req);
    $executiveOwner = $resolveExecutiveOwner($req);
@endphp
<div id="requestFormModal-{{ $req->id }}" class="request-form-modal fixed inset-0 bg-stone-900/60 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4" onclick="if (event.target === this) closeRequestFormModal('requestFormModal-{{ $req->id }}')">
    <div class="damage-form-sheet w-full max-w-4xl max-h-[92vh] overflow-y-auto rounded-xl shadow-2xl border border-slate-300 dark:border-slate-700">
        <div class="damage-form-header px-6 py-5 relative">
            <p class="damage-form-kicker text-[9px] font-black uppercase tracking-[0.2em]">Full Request Form</p>
            <div class="damage-form-title text-xl font-black tracking-tight">Request #{{ str_pad($req->id, 5, '0', STR_PAD_LEFT) }}</div>
            <p class="damage-form-subtitle mt-1 text-xs font-bold">{{ $req->event_name ?: 'Walkie Talkie Request' }}</p>
            <button type="button" onclick="closeRequestFormModal('requestFormModal-{{ $req->id }}')" class="absolute top-5 right-5 text-white/60 hover:text-white">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="p-5 md:p-6 space-y-4">
            <div class="damage-form-section">
                <div class="damage-form-section-title"><i class="fa-solid fa-user-tie"></i> 1. Executive Details</div>
                <div class="damage-form-grid">
                    <div class="damage-form-field">
                        <div class="damage-form-label">Executive Name</div>
                        <div class="damage-form-value">{{ $executiveOwner['name'] }}</div>
                    </div>
                    <div class="damage-form-field">
                        <div class="damage-form-label">Executive ID</div>
                        <div class="damage-form-value">{{ $executiveOwner['staff_id'] }}</div>
                    </div>
                    <div class="damage-form-field">
                        <div class="damage-form-label">Executive Department</div>
                        <div class="damage-form-value">{{ $executiveOwner['department'] }}</div>
                    </div>
                    <div class="damage-form-field">
                        <div class="damage-form-label">Approval Status</div>
                        <div class="damage-form-value">{{ strtoupper($req->status ?: '-') }}</div>
                    </div>
                </div>
            </div>

            <div class="damage-form-section">
                <div class="damage-form-section-title"><i class="fa-solid fa-clipboard-list"></i> {{ $isTemporaryRequest ? '2. Temporary Request Details' : '2. Long Term Request Details' }}</div>
                <div class="damage-form-grid">
                    <div class="damage-form-field">
                        <div class="damage-form-label">Quantity</div>
                        @php
                            $requestDays = max(1, (int) ($req->duration_days ?: 1));
                            $requestQty = max(1, (int) ($req->quantity ?: 1));
                        @endphp
                        <div class="damage-form-value">{{ $requestQty }} {{ \Illuminate\Support\Str::plural('unit', $requestQty) }}</div>
                    </div>
                    @if($isTemporaryRequest)
                    <div class="damage-form-field">
                        <div class="damage-form-label">How Many Days</div>
                        <div class="damage-form-value">{{ $requestDays }} {{ \Illuminate\Support\Str::plural('day', $requestDays) }}</div>
                    </div>
                    @endif
                    <div class="damage-form-field">
                        <div class="damage-form-label">Start Date</div>
                        <div class="damage-form-value">{{ $req->request_date ? \Carbon\Carbon::parse($req->request_date)->format('d M Y') : '-' }}</div>
                    </div>
                    @if($isTemporaryRequest)
                    <div class="damage-form-field">
                        <div class="damage-form-label">End Date</div>
                        <div class="damage-form-value">{{ $req->end_date ? \Carbon\Carbon::parse($req->end_date)->format('d M Y') : '-' }}</div>
                    </div>
                    @endif
                    <div class="damage-form-field damage-form-field-wide">
                        <div class="damage-form-label">{{ $isTemporaryRequest ? 'Purpose / Usage' : 'Remark / Purpose' }}</div>
                        <div class="damage-form-value whitespace-pre-line">{{ $isTemporaryRequest ? ($req->event_name ?: '-') : ($req->justifications ?: '-') }}</div>
                    </div>
                    @if($isTemporaryRequest)
                    <div class="damage-form-field damage-form-field-wide">
                        <div class="damage-form-label">Justification</div>
                        <div class="damage-form-value whitespace-pre-line">{{ $req->justifications ?: '-' }}</div>
                    </div>
                    @endif
                </div>
            </div>

            @if(! empty($req->pic_details))
            <div class="damage-form-section">
                <div class="damage-form-section-title"><i class="fa-solid fa-user-check"></i> Owner Per Unit</div>
                <div class="space-y-3">
                    @foreach($req->pic_details as $picIndex => $pic)
                    <div class="owner-unit-card">
                        <div class="owner-unit-title">{{ $picIndex + 1 }}. Ownership Information</div>
                        <div class="owner-unit-grid">
                            <div>
                                <div class="damage-form-label">Ownership Name</div>
                                <div class="damage-form-value">{{ strtoupper($pic['name'] ?? '-') }}</div>
                            </div>
                            <div>
                                <div class="damage-form-label">Ownership Phone No</div>
                                <div class="damage-form-value">{{ strtoupper($pic['phone_no'] ?? '-') }}</div>
                            </div>
                            <div>
                                <div class="damage-form-label">Department</div>
                                <div class="damage-form-value">{{ strtoupper($pic['department'] ?? '-') }}</div>
                            </div>
                            <div>
                                <div class="damage-form-label">Ownership Type</div>
                                <div class="damage-form-value">{{ strtoupper($pic['ownership_type'] ?? '-') }}</div>
                            </div>
                            @if(strtoupper((string) ($pic['ownership_type'] ?? '')) === 'SHARED')
                            <div>
                                <div class="damage-form-label">Shared With</div>
                                <div class="damage-form-value">{{ strtoupper($pic['shared_with'] ?? '-') }}</div>
                            </div>
                            @endif
                            <div>
                                <div class="damage-form-label">Sector</div>
                                <div class="damage-form-value">{{ strtoupper($pic['sector'] ?? '-') }}</div>
                            </div>
                            <div>
                                <div class="damage-form-label">Location</div>
                                <div class="damage-form-value">{{ strtoupper($pic['location'] ?? '-') }}</div>
                            </div>
                            <div>
                                <div class="damage-form-label">Who Will Pick Up This Walkie Talkie?</div>
                                <div class="damage-form-value">{{ strtoupper($pic['pickup_person'] ?? '-') }}</div>
                            </div>
                            <div>
                                <div class="damage-form-label">Pickup Phone No</div>
                                <div class="damage-form-value">{{ strtoupper($pic['pickup_phone_no'] ?? '-') }}</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="damage-form-section">
                <div class="damage-form-section-title"><i class="fa-solid fa-paper-plane"></i> Submission Info</div>
                <div class="damage-form-grid">
                    <div class="damage-form-field">
                        <div class="damage-form-label">Submitted By</div>
                        <div class="damage-form-value">{{ $reportedBy['name'] }}</div>
                    </div>
                    <div class="damage-form-field">
                        <div class="damage-form-label">Submitter Role</div>
                        <div class="damage-form-value">{{ strtoupper($reportedBy['role']) }}</div>
                    </div>
                    <div class="damage-form-field">
                        <div class="damage-form-label">Submitted ID</div>
                        <div class="damage-form-value">{{ $reportedBy['staff_id'] }}</div>
                    </div>
                    <div class="damage-form-field">
                        <div class="damage-form-label">Request Type</div>
                        <div class="damage-form-value">{{ $isTemporaryRequest ? 'TEMPORARY WALKIE TALKIE' : 'LONG TERM WALKIE TALKIE' }}</div>
                    </div>
                    <div class="damage-form-field">
                        <div class="damage-form-label">Requested / Assigned Accessories</div>
                        <div class="damage-form-value">{{ $req->accessories ?: 'To be selected by ICT' }}</div>
                    </div>
                </div>
            </div>

            @include('wt.partials.ict-update-section', ['request' => $req, 'prefix' => 'damage-form'])

            <div class="mt-5 flex justify-end">
                <button type="button" onclick="closeRequestFormModal('requestFormModal-{{ $req->id }}')" class="navy-btn navy-btn-soft px-5 py-3">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- PENDING RETURNS -->
<div class="approval-card pending-queue-card mb-5">
    <div class="pending-queue-header">
        <span class="pending-queue-icon"><i class="fa-solid fa-rotate-left text-lg"></i></span>
        <div>
            <h4 class="pending-queue-title">Pending Returns</h4>
            <p class="pending-queue-subtitle">Confirm returned units before inventory is updated</p>
        </div>
        <span class="pending-queue-count"><i class="fa-solid fa-hourglass-half"></i>{{ $pendingReturns->count() }} Pending</span>
    </div>
    <div class="pending-queue-body">
        <table id="returnsTable" class="w-full text-left display nowrap">
            <thead class="bg-stone-50 text-stone-400 text-[10px] uppercase font-black tracking-[0.15em]">
                <tr>
                    <th class="px-4 py-4">Requestor</th>
                    <th class="px-4 py-4">Return Details</th>
                    <th class="px-4 py-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-50">
                @forelse($pendingReturns as $ret)
                @php
                    $executiveOwner = $resolveExecutiveOwner($ret);
                @endphp
                <tr class="transition">
                    <td class="px-4 py-4">
                        <div class="requestor-card">
                            <div class="requestor-head">
                                <div class="requestor-avatar">{{ strtoupper(\Illuminate\Support\Str::substr($ret->full_name ?: 'R', 0, 1)) }}</div>
                                <div class="min-w-0">
                                    <div class="requestor-name truncate">{{ $ret->full_name ?: '-' }}</div>
                                    <div class="requestor-meta">Return #{{ str_pad($ret->id, 5, '0', STR_PAD_LEFT) }} &middot; {{ $ret->department ?: '-' }}</div>
                                    <div class="requestor-meta">{{ $ret->return_date ? \Carbon\Carbon::parse($ret->return_date)->format('d M Y') : '-' }}</div>
                                </div>
                            </div>
                            <div class="requestor-grid">
                                <div class="requestor-item md:col-span-2">
                                    <div class="requestor-label">Executive</div>
                                    <div class="requestor-value">
                                        <span class="font-black">{{ $executiveOwner['name'] }}</span>
                                        <div class="text-[9px] uppercase">{{ $executiveOwner['department'] }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="request-badges">
                                <span class="request-pill is-warm">{{ $ret->return_status ?: 'Pending Return' }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <div class="request-summary-card">
                            <div class="request-summary-title">Radio Assigned: {{ $ret->radio_id ?: '-' }}</div>
                            <div class="request-summary-note">Return requested for {{ $ret->return_date ? \Carbon\Carbon::parse($ret->return_date)->format('d M Y') : '-' }}.</div>
                            <div class="request-meta-block !mt-3">
                                <div class="request-meta-label">Return Status</div>
                                <div class="request-meta-value">{{ $ret->return_status ?: ($ret->status ?: 'PENDING') }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4 text-center">
                        <div class="approval-action-row">
                            <button type="button" onclick="event.stopPropagation(); openReturnFormModal('returnFormModal-{{ $ret->id }}')" class="approval-action-btn approval-action-view">View Form</button>
                            @if(($userRole ?? auth('wt')->user()->wt_role) === 'admin_it')
                            <form action="{{ route('wt.admin.requests.confirmReturn', $ret->id) }}" method="POST" onclick="event.stopPropagation()" data-modern-confirm="{{ ($userRole ?? auth('wt')->user()->wt_role) === 'admin_it' ? 'Confirm final return to inventory?' : 'Review this return and forward it to ICT?' }}" data-modern-confirm-title="Confirm Return" data-modern-confirm-remark="false">
                                @csrf
                                <button type="submit" class="approval-action-btn approval-action-approve">Confirm</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@foreach($pendingReturns as $ret)
<div id="returnFormModal-{{ $ret->id }}" class="fixed inset-0 bg-stone-900/60 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4" onclick="if (event.target === this) closeReturnFormModal('returnFormModal-{{ $ret->id }}')">
    <div class="damage-form-sheet w-full max-w-4xl max-h-[92vh] overflow-y-auto rounded-xl shadow-2xl border border-slate-300 dark:border-slate-700">
        <div class="damage-form-header px-6 py-5 relative">
            <p class="damage-form-kicker text-[9px] font-black uppercase tracking-[0.2em]">Pending Return Form</p>
            <div class="damage-form-title text-xl font-black tracking-tight">Return #{{ str_pad($ret->id, 5, '0', STR_PAD_LEFT) }}</div>
            <p class="damage-form-subtitle mt-1 text-xs font-bold">{{ strtoupper($ret->full_name ?: '-') }} / {{ strtoupper($ret->department ?: '-') }}</p>
            <button type="button" onclick="closeReturnFormModal('returnFormModal-{{ $ret->id }}')" class="absolute top-5 right-5 text-white/60 hover:text-white">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="p-5 md:p-6 space-y-4">
            <div class="damage-form-section">
                <div class="damage-form-section-title"><i class="fa-solid fa-rotate-left"></i> Return Details</div>
                <div class="damage-form-grid">
                    <div class="damage-form-field">
                        <div class="damage-form-label">Return By</div>
                        <div class="damage-form-value">{{ strtoupper($ret->full_name ?: '-') }}</div>
                    </div>
                    <div class="damage-form-field">
                        <div class="damage-form-label">Department</div>
                        <div class="damage-form-value">{{ strtoupper($ret->department ?: '-') }}</div>
                    </div>
                    <div class="damage-form-field">
                        <div class="damage-form-label">Radio Assigned</div>
                        <div class="damage-form-value">{{ strtoupper($ret->radio_id ?: '-') }}</div>
                    </div>
                    <div class="damage-form-field">
                        <div class="damage-form-label">Return Date</div>
                        <div class="damage-form-value">{{ $ret->return_date ? \Carbon\Carbon::parse($ret->return_date)->format('d M Y') : '-' }}</div>
                    </div>
                    <div class="damage-form-field">
                        <div class="damage-form-label">Approval Status</div>
                        <div class="damage-form-value">{{ $ret->return_status ?: ($ret->status ?: 'PENDING') }}</div>
                    </div>
                    <div class="damage-form-field">
                        <div class="damage-form-label">Request Type</div>
                        <div class="damage-form-value">RETURN WALKIE TALKIE</div>
                    </div>
                    <div class="damage-form-field damage-form-field-wide">
                        <div class="damage-form-label">Remarks</div>
                        <div class="damage-form-value whitespace-pre-line">{{ $ret->return_remark ?: ($ret->remarks ?: '-') }}</div>
                    </div>
                </div>
            </div>

            <div class="mt-5 flex flex-wrap justify-end gap-2">
                <button type="button" onclick="closeReturnFormModal('returnFormModal-{{ $ret->id }}')" class="approval-action-btn approval-action-view">Close</button>
                @if(($userRole ?? auth('wt')->user()->wt_role) === 'admin_it')
                <form action="{{ route('wt.admin.requests.confirmReturn', $ret->id) }}" method="POST" data-modern-confirm="{{ ($userRole ?? auth('wt')->user()->wt_role) === 'admin_it' ? 'Confirm final return to inventory?' : 'Review this return and forward it to ICT?' }}" data-modern-confirm-title="Confirm Return" data-modern-confirm-remark="false">
                    @csrf
                    <button type="submit" class="approval-action-btn approval-action-approve">Confirm</button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- PENDING REPORT FAULTY -->
<div class="approval-card pending-queue-card mb-5">
    <div class="pending-queue-header">
        <span class="pending-queue-icon"><i class="fa-solid fa-triangle-exclamation text-lg"></i></span>
        <div>
            <h4 class="pending-queue-title">Pending Report Faulty</h4>
            <p class="pending-queue-subtitle">Check faulty reports waiting for ICT or executive approval</p>
        </div>
        <span class="pending-queue-count"><i class="fa-solid fa-hourglass-half"></i>{{ $pendingDamageReports->count() }} Pending</span>
    </div>
    <div class="pending-queue-body">
        <table id="damagesTable" class="w-full text-left display nowrap">
            <thead class="bg-stone-50 text-stone-400 text-[10px] uppercase font-black tracking-[0.15em]">
                <tr>
                    <th class="px-4 py-4">Reporter</th>
                    <th class="px-4 py-4">Unit Details</th>
                    <th class="px-4 py-4">Issue</th>
                    <th class="px-4 py-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-50">
                @forelse($pendingDamageReports as $report)
                @php
                    $executiveOwner = $resolveExecutiveOwner($report);
                @endphp
                <tr class="transition">
                    <td class="px-4 py-4">
                        <div class="approval-body-title">{{ $report->reporter_name }}</div>
                        <div class="approval-body-meta mt-1 uppercase">{{ $report->department_name ?: '-' }}</div>
                        <div class="approval-body-meta mt-2 uppercase">Executive: {{ $executiveOwner['name'] }} / {{ $executiveOwner['department'] }}</div>
                    </td>
                    <td class="px-4 py-4">
                        <div class="approval-body-title">{{ $report->radio_id ?: $report->serial_number }}</div>
                        <div class="approval-body-meta mt-1 uppercase">{{ $report->model }}</div>
                    </td>
                    <td class="px-4 py-4">
                        <div class="approval-body-meta max-w-xs whitespace-normal break-words">{{ $report->problem_possible ?: ($report->issue ?: $report->issue_description) }}</div>
                        @if(!empty($report->evidence_paths))
                            @php
                                $evidenceFiles = is_array($report->evidence_paths)
                                    ? $report->evidence_paths
                                    : json_decode($report->evidence_paths, true);
                            @endphp
                            @if(!empty($evidenceFiles))
                            <a href="{{ asset('storage/' . $evidenceFiles[0]) }}" target="_blank" onclick="event.stopPropagation()" class="inline-flex items-center gap-1 mt-2 text-[9px] font-black uppercase tracking-widest text-[#0284c7] hover:text-[#0284c7]">
                                <i class="fa-solid fa-paperclip"></i> Evidence
                            </a>
                            @endif
                        @endif
                    </td>
                    <td class="px-4 py-4 text-center">
                        @if(($userRole ?? auth('wt')->user()->wt_role) === 'admin_it')
                            <div class="approval-action-row">
                                <button type="button" onclick="event.stopPropagation(); openDamageFormModal('damageFormModal-{{ $report->maintenance_id }}')" class="approval-action-btn approval-action-view">View Form</button>
                                <button type="button" onclick="event.stopPropagation(); openApproveDamageModal({{ $report->maintenance_id }}, '{{ addslashes($report->reporter_name) }}')" class="approval-action-btn approval-action-approve">Approve</button>
                                <button type="button" onclick="event.stopPropagation(); openRejectDamageModal({{ $report->maintenance_id }}, '{{ addslashes($report->reporter_name) }}')" class="approval-action-btn approval-action-reject">Reject</button>
                            </div>
                        @else
                            <div class="approval-action-row">
                                <button type="button" onclick="event.stopPropagation(); openDamageFormModal('damageFormModal-{{ $report->maintenance_id }}')" class="approval-action-btn approval-action-view">View Form</button>
                                <form action="{{ route('wt.admin.damageReports.forwardToIT', $report->maintenance_id) }}" method="POST" onclick="event.stopPropagation()" onsubmit="return confirm('Forward this damage report to ICT?');">
                                    @csrf
                                    <button type="submit" class="approval-action-btn approval-action-approve">Approve</button>
                                </form>
                                <button type="button" onclick="event.stopPropagation(); openRejectDamageModal({{ $report->maintenance_id }}, '{{ addslashes($report->reporter_name) }}')" class="approval-action-btn approval-action-reject">Reject</button>
                            </div>
                        @endif
                    </td>
                </tr>
                @empty
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@foreach($pendingDamageReports as $report)
@php
    $evidenceFiles = [];
    if (! empty($report->evidence_paths)) {
        $evidenceFiles = is_array($report->evidence_paths)
            ? $report->evidence_paths
            : (json_decode($report->evidence_paths, true) ?: []);
    }
    $replacementRequested = str_contains((string) ($report->remarks ?? ''), 'REPLACEMENT REQUESTED');
@endphp
<div id="damageFormModal-{{ $report->maintenance_id }}" class="fixed inset-0 bg-stone-900/60 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4" onclick="if (event.target === this) closeDamageFormModal('damageFormModal-{{ $report->maintenance_id }}')">
    <div class="damage-form-sheet w-full max-w-5xl max-h-[92vh] overflow-y-auto rounded-xl shadow-2xl border border-slate-300 dark:border-slate-700">
        <div class="damage-form-header px-6 py-5 relative">
            <p class="damage-form-kicker text-[9px] font-black uppercase tracking-[0.2em]">Faulty Walkie Talkie Application Form</p>
            <div class="mt-2 flex flex-col md:flex-row md:items-end md:justify-between gap-3">
                <div>
                    <div class="damage-form-title text-xl font-black tracking-tight">Damage Report #{{ str_pad($report->maintenance_id, 4, '0', STR_PAD_LEFT) }}</div>
                    <p class="damage-form-subtitle mt-1 text-xs font-bold">{{ strtoupper($report->reporter_name ?: '-') }} / {{ strtoupper($report->department_name ?: '-') }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2 pr-8 md:justify-end">
                    <span class="damage-form-status"><i class="fa-solid fa-clock"></i>{{ $report->status ?: 'Pending' }}</span>
                    @if($replacementRequested)
                    <span class="damage-form-badge"><i class="fa-solid fa-repeat"></i>Replacement Requested</span>
                    @endif
                </div>
            </div>
            <button type="button" onclick="closeDamageFormModal('damageFormModal-{{ $report->maintenance_id }}')" class="absolute top-5 right-5 text-white/60 hover:text-white">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="p-5 md:p-6 space-y-4">
            <div class="damage-form-section">
                <div class="damage-form-section-title"><i class="fa-solid fa-user-check"></i> Reporter Details</div>
                <div class="damage-form-grid">
                    <div class="damage-form-field">
                        <div class="damage-form-label">Reporter Name</div>
                        <div class="damage-form-value">{{ strtoupper($report->reporter_name ?: '-') }}</div>
                    </div>
                    <div class="damage-form-field">
                        <div class="damage-form-label">Department</div>
                        <div class="damage-form-value">{{ strtoupper($report->department_name ?: '-') }}</div>
                    </div>
                    <div class="damage-form-field">
                        <div class="damage-form-label">Phone No.</div>
                        <div class="damage-form-value">{{ $report->phone_no ?: '-' }}</div>
                    </div>
                    <div class="damage-form-field">
                        <div class="damage-form-label">Request Source</div>
                        <div class="damage-form-value">{{ strtoupper(str_replace('_', ' ', $report->request_source ?: 'USER')) }}</div>
                    </div>
                </div>
            </div>

            <div class="damage-form-section">
                <div class="damage-form-section-title"><i class="fa-solid fa-walkie-talkie"></i> Walkie Talkie Details</div>
                <div class="damage-form-grid">
                    <div class="damage-form-field">
                        <div class="damage-form-label">Radio ID</div>
                        <div class="damage-form-value">{{ strtoupper($report->radio_id ?: '-') }}</div>
                    </div>
                    <div class="damage-form-field">
                        <div class="damage-form-label">Serial Number</div>
                        <div class="damage-form-value">{{ strtoupper($report->serial_number ?: '-') }}</div>
                    </div>
                    <div class="damage-form-field">
                        <div class="damage-form-label">Model</div>
                        <div class="damage-form-value">{{ strtoupper($report->model ?: '-') }}</div>
                    </div>
                    <div class="damage-form-field">
                        <div class="damage-form-label">Submitted Date</div>
                        <div class="damage-form-value">{{ $report->received_date ? \Carbon\Carbon::parse($report->received_date)->format('d M Y') : '-' }}</div>
                    </div>
                </div>
            </div>

            <div class="damage-form-section">
                <div class="damage-form-section-title"><i class="fa-solid fa-people-arrows"></i> Ownership & Location</div>
                <div class="damage-form-grid">
                    <div class="damage-form-field">
                        <div class="damage-form-label">Ownership Type</div>
                        <div class="damage-form-value">{{ strtoupper($report->ownership_type ?: '-') }}</div>
                    </div>
                    <div class="damage-form-field">
                        <div class="damage-form-label">Shared With</div>
                        <div class="damage-form-value">{{ strtoupper($report->shared_with ?: '-') }}</div>
                    </div>
                    <div class="damage-form-field">
                        <div class="damage-form-label">Sector</div>
                        <div class="damage-form-value">{{ strtoupper($report->sector ?: '-') }}</div>
                    </div>
                    <div class="damage-form-field">
                        <div class="damage-form-label">Location</div>
                        <div class="damage-form-value">{{ strtoupper($report->location ?: '-') }}</div>
                    </div>
                </div>
            </div>

            <div class="damage-form-section">
                <div class="damage-form-section-title"><i class="fa-solid fa-handshake"></i> Pickup & Handover</div>
                <div class="damage-form-grid">
                    <div class="damage-form-field">
                        <div class="damage-form-label">Handover Person</div>
                        <div class="damage-form-value">{{ strtoupper($report->handover_person ?: '-') }}</div>
                    </div>
                    <div class="damage-form-field">
                        <div class="damage-form-label">Handover Date & Time</div>
                        <div class="damage-form-value">{{ $report->handover_at ? \Carbon\Carbon::parse($report->handover_at)->format('d M Y, h:i A') : '-' }}</div>
                    </div>
                    <div class="damage-form-field">
                        <div class="damage-form-label">Pickup Person</div>
                        <div class="damage-form-value">{{ strtoupper($report->pickup_person ?: '-') }}</div>
                    </div>
                    <div class="damage-form-field">
                        <div class="damage-form-label">Pickup Date & Time</div>
                        <div class="damage-form-value">{{ $report->pickup_at ? \Carbon\Carbon::parse($report->pickup_at)->format('d M Y, h:i A') : '-' }}</div>
                    </div>
                    <div class="damage-form-field damage-form-field-wide">
                        <div class="damage-form-label">Collection Info</div>
                        <div class="damage-form-value">Pickup can be done at ICT Department Sejurumus after approval from ICT.</div>
                    </div>
                </div>
            </div>

            <div class="damage-form-section">
                <div class="damage-form-section-title"><i class="fa-solid fa-triangle-exclamation"></i> Reported Issue</div>
                <div class="damage-form-grid">
                    <div class="damage-form-field damage-form-field-wide">
                        <div class="damage-form-label">Problem Reported</div>
                        <div class="damage-form-value whitespace-pre-line">{{ $report->problem_possible ?: ($report->issue ?: ($report->issue_description ?: '-')) }}</div>
                    </div>
                    <div class="damage-form-field damage-form-field-wide">
                        <div class="damage-form-label">Remarks / Replacement Details</div>
                        <div class="damage-form-value whitespace-pre-line">{{ $report->remarks ?: 'No additional remarks.' }}</div>
                    </div>
                </div>
            </div>

            <div class="damage-form-section">
                <div class="damage-form-section-title"><i class="fa-solid fa-paperclip"></i> Evidence Uploaded</div>
                <div class="p-4">
                    <div class="damage-form-value">
                        @if(!empty($evidenceFiles))
                        <div class="flex flex-wrap gap-2">
                            @foreach($evidenceFiles as $path)
                            <a href="{{ asset('storage/' . $path) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-lg border border-stone-200 px-3 py-2 text-[10px] font-black uppercase tracking-widest text-[#0284c7] hover:bg-stone-50 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-800">
                                <i class="fa-solid fa-paperclip"></i> Evidence {{ $loop->iteration }}
                            </a>
                            @endforeach
                        </div>
                        @else
                        No evidence uploaded.
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="button" onclick="closeDamageFormModal('damageFormModal-{{ $report->maintenance_id }}')" class="navy-btn navy-btn-soft px-5 py-3">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@if(($userRole ?? auth('wt')->user()->wt_role) === 'admin_it')
<div id="approveModal" class="fixed inset-0 z-50 hidden flex-row items-center justify-center overflow-x-auto overflow-y-hidden bg-slate-950/50 p-3 backdrop-blur-sm" style="gap: 4px;" onclick="if (event.target === this) closeApproveModal()">
    <div class="approval-modal-card" style="margin: 0 !important; width: 430px !important; flex: 0 0 430px !important;">
        <div class="navy-panel px-6 py-5 relative">
            <h3 class="text-white font-black text-base tracking-tight">Assign WT Before Approval</h3>
            <p class="text-slate-300 text-xs font-medium mt-1">Approve only after enough units are selected for <span id="modalUserName" class="text-white font-bold"></span></p>
            <button type="button" onclick="closeApproveModal()" class="absolute top-5 right-5 text-white/60 hover:text-white"><i class="fas fa-times text-lg"></i></button>
        </div>
        <form id="approveForm" method="POST" class="p-6">
            @csrf
            <div id="approvalRequestSummary" class="mb-5 hidden rounded-xl border border-sky-200 bg-sky-50 p-4 text-[11px] font-bold text-slate-700 dark:border-sky-900/60 dark:bg-sky-950/40 dark:text-slate-200">
                <div class="mb-2 flex items-center justify-between gap-3">
                    <p class="text-[10px] font-black uppercase tracking-widest text-sky-700 dark:text-sky-300">Request Details</p>
                    <span id="approvalRequestTypeBadge" class="rounded-full bg-white px-2 py-1 text-[9px] font-black uppercase tracking-widest text-sky-700 shadow-sm dark:bg-slate-900 dark:text-sky-200"></span>
                </div>
                <div class="grid grid-cols-1 gap-2">
                    <div>Quantity: <span id="approvalRequestQuantity" class="text-slate-950 dark:text-white">1 unit</span></div>
                    <div id="approvalPeriodRow" class="hidden">Period: <span id="approvalRequestPeriod" class="text-slate-950 dark:text-white">-</span></div>
                    <div>Pickup: <span id="approvalRequestPickup" class="text-slate-950 dark:text-white">-</span></div>
                </div>
            </div>
            <div class="mb-5">
                <div class="mb-2 flex items-center justify-between gap-3">
                    <div>
                        <label class="block text-[10px] font-black text-slate-600 dark:text-slate-300 uppercase tracking-widest">Available Unit(s)</label>
                        <p id="approvalUnitHint" class="mt-1 text-[9px] font-bold uppercase tracking-wider text-slate-400">Select 1 unused radio.</p>
                    </div>
                    <button type="button" onclick="openQuickWtModal()" class="approval-action-btn approval-action-view !min-h-[24px] !min-w-[68px] !px-2 !text-[8px]">
                        New WT
                    </button>
                </div>
                <div id="approvalStockStatus" class="mb-3 rounded-lg border px-3 py-2 text-[10px] font-black uppercase tracking-wider"></div>
                <select id="approveRadioSelect" name="walkie_inventory_ids[]" class="w-full" multiple required>
                    @foreach($availableRadios as $radio)
                        <option value="{{ $radio->walkie_id }}">
                            SERIAL: {{ $radio->serial_number }} | RADIO ID: {{ $radio->radio_id }} | MODEL: {{ $radio->model }}
                        </option>
                    @endforeach
                </select>
                @if($availableRadios->isEmpty())
                    <p class="mt-2 text-[10px] font-bold text-red-600 dark:text-red-300">No available radios to assign.</p>
                @endif
            </div>

            <div id="assignmentDetailsBox" class="mb-5 hidden rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-900">
                <div class="mb-3">
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-600 dark:text-slate-300">
                        Per Unit Assignment
                    </p>
                    <p class="mt-1 text-[10px] font-bold leading-4 text-slate-500 dark:text-slate-400">
                        Set ownership, bay, and department for each selected WT. Ownership/shared fields can be a name, place, bay, team, or department.
                    </p>
                </div>
                <div id="assignmentDetailsList" class="space-y-3"></div>
            </div>

            <div id="accessoryChecklistBox" class="mb-5 rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-900">
                <div class="mb-3 flex items-center justify-between gap-3">
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-600 dark:text-slate-300">
                        Accessory Checklist
                    </p>
                    <button type="button" id="accessoryClickAllBtn" class="approval-action-btn approval-action-view !min-h-[24px] !min-w-[70px] !px-2 !text-[8px]">
                        Click All
                    </button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @php
                        $possibleAccessories = ['Remote Speaker Mic', 'Antenna', 'Battery', 'Belt Clip', 'Dust Cover', 'Carrying Case', 'Single Unit Charger'];
                    @endphp
                    @foreach($possibleAccessories as $item)
                        <label class="flex items-center gap-2 text-[10px] font-bold text-slate-600 dark:text-slate-300">
                            <input type="checkbox" name="accessories[]" value="{{ $item }}" class="accessory-toggle rounded border-slate-300">
                            {{ $item }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="mb-5">
                <label for="approvalRemark" class="block text-[10px] font-black text-slate-600 dark:text-slate-300 mb-2 uppercase tracking-widest">Remark</label>
                <textarea id="approvalRemark" name="approval_remark" rows="3" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-[11px] font-semibold text-slate-700 outline-none focus:border-slate-400 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" placeholder="Optional pickup note or instruction."></textarea>
            </div>

            <div class="flex justify-end gap-2 border-t border-slate-100 pt-4 dark:border-slate-800">
                <button type="button" onclick="closeApproveModal()" class="approval-action-btn">Cancel</button>
                <button type="submit" id="approveSubmitBtn" class="approval-action-btn approval-action-approve" disabled>Confirm Approval</button>
            </div>
        </form>
    </div>

<div id="quickWtModal" class="hidden" style="margin: 0 !important; width: 430px !important; flex: 0 0 430px !important;">
    <div class="max-h-[calc(100vh-2rem)] w-full overflow-y-auto rounded-2xl border border-slate-700 bg-slate-950 shadow-2xl shadow-slate-950/40">
        <div class="flex items-start justify-between gap-4 border-b border-slate-800 px-6 py-5">
            <div>
                <h3 class="text-base font-black tracking-tight text-white">Add New Walkie Talkie</h3>
                <p class="mt-1 text-xs font-semibold text-slate-400">Create a new unused unit and select it for this approval.</p>
                <p id="quickWtRequestHint" class="mt-2 hidden text-[10px] font-black uppercase tracking-widest text-sky-300"></p>
            </div>
            <button type="button" onclick="closeQuickWtModal()" class="text-white/60 hover:text-white"><i class="fas fa-times text-lg"></i></button>
        </div>

        <form id="quickWtForm" action="{{ route('wt.admin.walkies.store') }}" method="POST" class="px-6 py-5">
            @csrf
            <input type="hidden" name="status" value="UNUSED">
            <input type="hidden" name="ownership_type" id="quick_ownership_type_fallback" value="UNALLOCATED" disabled>
            <div id="quickWtErrors" class="mb-4 hidden rounded-xl border border-red-500/40 bg-red-950/40 px-4 py-3 text-[11px] font-bold text-red-100"></div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-300">Radio ID <span class="text-red-300">*</span></label>
                    <input type="text" name="radio_id" id="quick_radio_id" class="quick-wt-input" placeholder="Enter new radio id" required>
                </div>

                <div>
                    <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-300">Serial Number <span class="text-red-300">*</span></label>
                    <input type="text" name="serial_number" id="quick_serial_number" class="quick-wt-input" placeholder="Enter new serial number" required>
                </div>

                <div>
                    <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-300">Model <span class="text-red-300">*</span></label>
                    <select name="model" id="quick_model" class="quick-wt-input quick-wt-smart-select" data-placeholder="Select model" required>
                        <option value=""></option>
                        @foreach($walkieModels as $model)
                            <option value="{{ $model }}">{{ $model }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="quickOwnershipTypeGroup">
                    <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-300">Ownership Type <span class="text-red-300">*</span></label>
                    <select name="ownership_type" id="quick_ownership_type" class="quick-wt-input quick-wt-tag-select quick-ownership-type-control" data-placeholder="Type or select ownership type" required>
                        <option value=""></option>
                        @foreach($ownershipTypeOptions as $ownershipType)
                            <option value="{{ $ownershipType }}" @selected($ownershipType === 'INDIVIDUAL')>{{ $ownershipType }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="quickSharedWithGroup" class="hidden quick-assignment-field-group">
                    <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-300">Shared With <span class="text-red-300">*</span></label>
                    <input type="text" name="shared_with" id="quick_shared_with" class="quick-wt-input" placeholder="Shared owner / place">
                </div>

                <div id="quickOwnershipGroup" class="quick-assignment-field-group">
                    <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-300">Ownership Name</label>
                    <select name="ownership" id="quick_ownership" class="quick-wt-input quick-wt-tag-select" data-placeholder="Type or select ownership name">
                        <option value=""></option>
                        @foreach($walkieOwnerships as $ownership)
                            <option value="{{ $ownership }}">{{ $ownership }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="quickDepartmentGroup" class="quick-assignment-field-group">
                    <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-300">Department</label>
                    <select name="department" id="quick_department" class="quick-wt-input quick-wt-tag-select" data-placeholder="Type or select department">
                        <option value=""></option>
                        @foreach($walkieDepartments as $department)
                            <option value="{{ $department }}">{{ $department }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="quickPositionGroup" class="quick-assignment-field-group">
                    <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-300">Position</label>
                    <select name="position" id="quick_position" class="quick-wt-input quick-wt-tag-select" data-placeholder="Type or select position">
                        <option value=""></option>
                        @foreach($walkiePositions as $position)
                            <option value="{{ $position }}">{{ $position }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="quickRemarkGroup" class="md:col-span-2 quick-assignment-field-group">
                    <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-300">Remark</label>
                    <textarea name="remark" rows="3" class="quick-wt-input min-h-[84px] resize-none" placeholder="Additional notes..."></textarea>
                </div>
            </div>

            <div class="mt-5 flex flex-wrap items-center justify-between gap-2 border-t border-slate-800 pt-4">
                <a href="{{ route('wt.admin.walkies.index') }}" target="_blank" rel="noopener" class="approval-action-btn approval-action-view">
                    Inventory List
                </a>
                <div class="flex gap-2">
                    <button type="button" onclick="closeQuickWtModal()" class="approval-action-btn">Cancel</button>
                    <button type="submit" id="quickWtSubmitBtn" class="approval-action-btn approval-action-approve">Save Unit</button>
                </div>
            </div>
        </form>
    </div>
</div>
</div>
@endif

<div id="rejectRequestModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm" onclick="if (event.target === this) closeRejectRequestModal()">
    <div class="approval-modal-card">
        <form id="rejectRequestForm" method="POST" class="p-6">
            @csrf
            <p class="text-[10px] font-black uppercase tracking-widest text-red-700 dark:text-red-300">Reject Request</p>
            <h3 class="mt-2 text-base font-black text-slate-950 dark:text-white">Confirm rejection?</h3>
            <p class="mt-2 text-xs font-semibold leading-5 text-slate-500 dark:text-slate-400">
                This will reject <span id="rejectRequestUserName" class="font-black text-slate-800 dark:text-slate-100"></span>'s request.
            </p>
            <div class="mt-5">
                <label for="requestDisapprovalRemark" class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-600 dark:text-slate-300">Rejection Remark</label>
                <textarea id="requestDisapprovalRemark" name="disapproval_remark" rows="4" class="w-full resize-none rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-[11px] font-semibold text-slate-800 outline-none focus:border-red-400 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" placeholder="Type the reason for rejecting this request." required></textarea>
            </div>
            <div class="mt-5 flex justify-end gap-2 border-t border-slate-100 pt-4 dark:border-slate-800">
                <button type="button" onclick="closeRejectRequestModal()" class="approval-action-btn">Cancel</button>
                <button type="submit" class="approval-action-btn approval-action-reject">Reject</button>
            </div>
        </form>
    </div>
</div>

<div id="approveDamageModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm" onclick="if (event.target === this) closeApproveDamageModal()">
    <div class="approval-modal-card">
        <form id="approveDamageForm" method="POST" class="p-6">
            @csrf
            <p class="text-[10px] font-black uppercase tracking-widest text-emerald-700 dark:text-emerald-300">Approve Faulty Report</p>
            <h3 class="mt-2 text-base font-black text-slate-950 dark:text-white">Approve report from <span id="damageApproveUserName"></span>?</h3>
            <div class="mt-5">
                <label for="damageApprovalRemark" class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-600 dark:text-slate-300">Remark</label>
                <textarea id="damageApprovalRemark" name="approval_remark" rows="4" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-[11px] font-semibold text-slate-800 outline-none focus:border-slate-400 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" placeholder="Optional repair note for user."></textarea>
            </div>
            <div class="mt-5 flex justify-end gap-2 border-t border-slate-100 pt-4 dark:border-slate-800">
                <button type="button" onclick="closeApproveDamageModal()" class="approval-action-btn">Cancel</button>
                <button type="submit" class="approval-action-btn approval-action-approve">Approve</button>
            </div>
        </form>
    </div>
</div>

<div id="rejectDamageModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm" onclick="if (event.target === this) closeRejectDamageModal()">
    <div class="approval-modal-card">
        <form id="rejectDamageForm" method="POST" class="p-6" autocomplete="off">
            @csrf
            <p class="text-[10px] font-black uppercase tracking-widest text-red-700 dark:text-red-300">Reject Faulty Report</p>
            <h3 class="mt-2 text-base font-black text-slate-950 dark:text-white">Reject report from <span id="damageRejectUserName"></span>?</h3>
            <div class="mt-5">
                <label for="damageDisapprovalRemark" class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-600 dark:text-slate-300">Rejection Remark</label>
                <textarea id="damageDisapprovalRemark" name="disapproval_remark" rows="4" class="w-full resize-none rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-[11px] font-semibold text-slate-800 outline-none focus:border-red-400 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" placeholder="Type rejection reason here." required></textarea>
            </div>
            <div class="mt-5 flex justify-end gap-2 border-t border-slate-100 pt-4 dark:border-slate-800">
                <button type="button" onclick="closeRejectDamageModal()" class="approval-action-btn">Cancel</button>
                <button type="submit" class="approval-action-btn approval-action-reject">Reject</button>
            </div>
        </form>
    </div>
</div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        const emptyInboxState = `
            <div class="approval-empty-state">
                <span class="empty-visual"><i class="fa-solid fa-inbox"></i></span>
                <span>No pending records.</span>
            </div>
        `;

        const sharedInboxTableOptions = {
            pageLength: 5,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, 'All']],
            ordering: false,
            autoWidth: false,
            language: {
                search: "",
                searchPlaceholder: "Search records...",
                emptyTable: emptyInboxState,
                zeroRecords: emptyInboxState,
            },
            dom: 'Bfrt',
            initComplete: function() {
                this.api().columns.adjust();
            }
        };

        const requestsTable = $('#requestsTable').DataTable({
            ...sharedInboxTableOptions,
            buttons: getAdminTableExportButtons('Pending Approval Requests', ':not(:last-child)')
        });
        mountAdminTableExportDropdown(requestsTable, '#requestsExportActions');

        if ($('#returnsTable').length) {
            const returnsTable = $('#returnsTable').DataTable({
                ...sharedInboxTableOptions,
                buttons: getAdminTableExportButtons('Pending Returns', ':not(:last-child)')
            });
            mountAdminTableExportDropdown(returnsTable, '#returnsExportActions');
        }

        const damagesTable = $('#damagesTable').DataTable({
            ...sharedInboxTableOptions,
            buttons: getAdminTableExportButtons('Pending Report Faulty', ':not(:last-child)')
        });
        mountAdminTableExportDropdown(damagesTable, '#damagesExportActions');

        if ($('#approveRadioSelect').length) {
            configureApproveRadioSelect();
        }

        initQuickWtSelects();
        bindQuickWtForm();
        bindApproveFormValidation();
    });

    let currentApprovalRequest = {
        quantity: 1,
        request_type: '',
        full_name: '',
        department: '',
        bay_from: '',
        position: '',
        ownership_type: 'INDIVIDUAL',
        shared_with: '',
        request_date: '',
        end_date: '',
        duration_days: 1,
        pic_details: [],
        pickup_method: '',
        pickup_representative_name: '',
        requested_pickup_at: '',
        justifications: ''
    };

    function focusOpenSelect2Search() {
        window.setTimeout(function () {
            const searchField = document.querySelector('.select2-container--open .select2-search__field');
            if (searchField) {
                searchField.removeAttribute('readonly');
                searchField.focus();
            }
        }, 0);
    }

    function initQuickWtSelects() {
        if (!$('#quickWtModal').length) return;

        $('.quick-wt-tag-select').each(function() {
            const $select = $(this);
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }

            $select.select2({
                width: '100%',
                tags: true,
                allowClear: !$select.prop('required'),
                placeholder: $select.data('placeholder') || 'Type or select option',
                dropdownParent: $('#quickWtModal'),
                createTag: function(params) {
                    const term = $.trim(params.term);
                    if (term === '') return null;

                    const normalizedTerm = term.toUpperCase();
                    return {
                        id: normalizedTerm,
                        text: normalizedTerm,
                        newTag: true
                    };
                },
                insertTag: function(data, tag) {
                    data.unshift(tag);
                }
            });

            $select.off('select2:open.quickWtFocus').on('select2:open.quickWtFocus', focusOpenSelect2Search);
        });

        $('.quick-wt-smart-select').each(function() {
            const $select = $(this);
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }

            $select.select2({
                width: '100%',
                allowClear: !$select.prop('required'),
                placeholder: $select.data('placeholder') || 'Search option',
                dropdownParent: $('#quickWtModal')
            });

            $select.off('select2:open.quickWtFocus').on('select2:open.quickWtFocus', focusOpenSelect2Search);
        });

        $('.quick-ownership-type-control')
            .off('change.quickShared select2:select.quickShared')
            .on('change.quickShared select2:select.quickShared', syncQuickSharedWith);

        syncQuickSharedWith();
    }

    function syncQuickSharedWith() {
        const ownershipType = String($('#quick_ownership_type').val() || '').toUpperCase();
        const isShared = ownershipType === 'SHARED';
        $('#quickSharedWithGroup').toggleClass('hidden', !isShared);
        $('#quick_shared_with').prop('required', false);

        if (!isShared) {
            $('#quick_shared_with').val('');
        }
    }

    function syncQuickWtFormMode() {
        const registerOnlyMode = true;

        $('#quickOwnershipTypeGroup').toggleClass('hidden', registerOnlyMode);
        $('.quick-assignment-field-group').toggleClass('hidden', registerOnlyMode);
        $('#quick_ownership_type_fallback').prop('disabled', !registerOnlyMode);

        $('#quick_ownership_type')
            .prop('disabled', registerOnlyMode)
            .prop('required', !registerOnlyMode);

        $('#quick_ownership, #quick_department, #quick_position')
            .prop('disabled', registerOnlyMode);

        $('#quickWtForm textarea[name="remark"]')
            .prop('disabled', registerOnlyMode);

        $('#quick_shared_with').prop('disabled', registerOnlyMode).prop('required', false).val('');
    }

    function openQuickWtModal() {
        const modal = document.getElementById('quickWtModal');
        if (!modal) return;

        clearQuickWtErrors();
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        initQuickWtSelects();
        prefillQuickWtFromRequest();
        syncQuickWtFormMode();
    }

    function closeQuickWtModal() {
        const modal = document.getElementById('quickWtModal');
        if (!modal) return;

        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function clearQuickWtErrors() {
        const errorBox = document.getElementById('quickWtErrors');
        if (!errorBox) return;

        errorBox.classList.add('hidden');
        errorBox.innerHTML = '';
    }

    function showQuickWtErrors(messages) {
        const errorBox = document.getElementById('quickWtErrors');
        if (!errorBox) return;

        errorBox.innerHTML = messages.map(message => `<div>${message}</div>`).join('');
        errorBox.classList.remove('hidden');
    }

    function bindQuickWtForm() {
        const form = document.getElementById('quickWtForm');
        if (!form) return;

        form.addEventListener('submit', async function(event) {
            event.preventDefault();
            clearQuickWtErrors();

            const submitButton = document.getElementById('quickWtSubmitBtn');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = 'Saving...';
            }

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new FormData(form)
                });

                const data = await response.json();

                if (!response.ok) {
                    const messages = data.errors
                        ? Object.values(data.errors).flat()
                        : [data.message || 'Unable to save unit.'];
                    showQuickWtErrors(messages);
                    return;
                }

                addNewWalkieToApprovalSelect(data.walkie);
                form.reset();
                $('#quickWtForm select').val(null).trigger('change');
                $('#quick_ownership_type').val('INDIVIDUAL').trigger('change');
                syncQuickWtFormMode();
                closeQuickWtModal();
            } catch (error) {
                showQuickWtErrors(['Unable to save unit. Please try again.']);
            } finally {
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Save Unit';
                }
            }
        });
    }

    function bindApproveFormValidation() {
        const form = document.getElementById('approveForm');
        if (!form) return;

        form.addEventListener('submit', function(event) {
            const selectedCount = ($('#approveRadioSelect').val() || []).length;
            const quantity = getApprovalQuantity();
            const availableCount = getAvailableApprovalUnitCount();

            if (selectedCount !== quantity) {
                event.preventDefault();
                updateApprovalSelectionHint();
                alert(`Please select exactly ${formatCount(quantity, 'unit')} for this request.`);
                return;
            }

            if (availableCount < quantity) {
                event.preventDefault();
                updateApprovalSelectionHint();
                alert(`Not enough unused WT units. This request needs ${formatCount(quantity, 'unit')}, but only ${availableCount} available.`);
            }
        });
    }

    function addNewWalkieToApprovalSelect(walkie) {
        if (!walkie || !walkie.walkie_id) return;

        const option = new Option(walkie.label, walkie.walkie_id, true, true);
        const $select = $('#approveRadioSelect');
        const currentValues = $select.val() || [];
        const limit = getApprovalQuantity();
        const nextValues = currentValues.includes(String(walkie.walkie_id))
            ? currentValues
            : [...currentValues, String(walkie.walkie_id)].slice(0, limit);

        $select.append(option);
        $select.val(nextValues).trigger('change');
        updateApprovalSelectionHint();

        const approveButton = document.querySelector('#approveForm .approval-action-approve');
        if (approveButton) {
            approveButton.disabled = ($('#approveRadioSelect').val() || []).length !== getApprovalQuantity();
        }
    }

    function openApproveModal(payload) {
        if (typeof payload !== 'object' || payload === null) return;

        const modal = document.getElementById('approveModal');
        const modalUserName = document.getElementById('modalUserName');
        const approveForm = document.getElementById('approveForm');
        if (!modal || !modalUserName || !approveForm) return;
        moveApprovalModalToBody(modal);
        closeQuickWtModal();

        currentApprovalRequest = {
            quantity: Math.max(1, parseInt(payload.quantity || 1, 10)),
            request_type: payload.request_type || '',
            full_name: payload.full_name || '',
            department: payload.department || '',
            bay_from: payload.bay_from || '',
            position: payload.position || '',
            ownership_type: String(payload.ownership_type || 'INDIVIDUAL').toUpperCase(),
            shared_with: payload.shared_with || '',
            request_date: payload.request_date || '',
            end_date: payload.end_date || '',
            duration_days: Math.max(1, parseInt(payload.duration_days || 1, 10)),
            pic_details: Array.isArray(payload.pic_details) ? payload.pic_details : [],
            pickup_method: payload.pickup_method || '',
            pickup_representative_name: payload.pickup_representative_name || '',
            requested_pickup_at: payload.requested_pickup_at || '',
            justifications: payload.justifications || ''
        };

        modalUserName.innerText = currentApprovalRequest.full_name;
        approveForm.action = "{{ route('wt.admin.requests.index') }}/" + payload.id + "/approve";
        configureApproveRadioSelect();
        $('#approveRadioSelect').val(null).trigger('change');
        const approvalRemark = document.getElementById('approvalRemark');
        if (approvalRemark) {
            approvalRemark.value = '';
        }
        renderApprovalRequestSummary();
        updateApprovalSelectionHint();
        
        // Handle Accessory Pre-selection
        const accessoriesList = payload.accessories ? payload.accessories.split(',').map(a => a.trim()) : [];
        document.querySelectorAll('#approveModal .accessory-toggle').forEach(checkbox => {
            checkbox.checked = accessoriesList.includes(checkbox.value);
        });
        syncAccessorySelectAll();

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function getApprovalQuantity() {
        return Math.max(1, parseInt(currentApprovalRequest.quantity || 1, 10));
    }

    function getAvailableApprovalUnitCount() {
        return document.querySelectorAll('#approveRadioSelect option').length;
    }

    function formatCount(count, singular, plural = `${singular}s`) {
        const value = Math.max(1, parseInt(count || 1, 10));
        return `${value} ${value === 1 ? singular : plural}`;
    }

    function configureApproveRadioSelect() {
        const $select = $('#approveRadioSelect');
        if (!$select.length) return;

        if ($select.hasClass('select2-hidden-accessible')) {
            $select.select2('destroy');
        }

        const quantity = getApprovalQuantity();
        $select.select2({
            placeholder: quantity > 1 ? `-- Select ${quantity} unused radios --` : "-- Select an unused radio --",
            width: '100%',
            dropdownParent: $('#approveModal'),
            allowClear: true,
            maximumSelectionLength: quantity
        });

        $select.off('change.approvalSync').on('change.approvalSync', updateApprovalSelectionHint);
    }

    function renderApprovalRequestSummary() {
        const summary = document.getElementById('approvalRequestSummary');
        const badge = document.getElementById('approvalRequestTypeBadge');
        const quantity = document.getElementById('approvalRequestQuantity');
        const periodRow = document.getElementById('approvalPeriodRow');
        const period = document.getElementById('approvalRequestPeriod');
        const pickup = document.getElementById('approvalRequestPickup');
        const quickHint = document.getElementById('quickWtRequestHint');
        if (!summary) return;

        const isTemporary = currentApprovalRequest.request_type === 'temporary_walkie_talkie';
        summary.classList.remove('hidden');

        if (badge) {
            badge.textContent = isTemporary ? 'Temporary' : 'Standard';
        }

        if (quantity) {
            quantity.textContent = formatCount(getApprovalQuantity(), 'unit');
        }

        if (periodRow && period) {
            periodRow.classList.toggle('hidden', !isTemporary);
            period.textContent = isTemporary
                ? `${formatApprovalDate(currentApprovalRequest.request_date)} - ${formatApprovalDate(currentApprovalRequest.end_date)} · ${formatCount(currentApprovalRequest.duration_days, 'day')}`
                : '-';
        }

        if (pickup) {
            pickup.textContent = `For ${currentApprovalRequest.full_name || '-'} · ${currentApprovalRequest.requested_pickup_at || '-'} · ICT Department after approval`;
        }

        if (quickHint) {
            const selectedCount = ($('#approveRadioSelect').val() || []).length;
            quickHint.textContent = `Current request needs ${formatCount(getApprovalQuantity(), 'unit')}. Register the unit only; set ownership in the approval form. Selected ${selectedCount}/${getApprovalQuantity()}.`;
            quickHint.classList.remove('hidden');
        }
    }

    function updateApprovalSelectionHint() {
        const selectedCount = ($('#approveRadioSelect').val() || []).length;
        const quantity = getApprovalQuantity();
        const availableCount = getAvailableApprovalUnitCount();
        const hint = document.getElementById('approvalUnitHint');
        const quickHint = document.getElementById('quickWtRequestHint');
        const stockStatus = document.getElementById('approvalStockStatus');
        const submitButton = document.getElementById('approveSubmitBtn');
        const hasEnoughStock = availableCount >= quantity;
        const hasExactSelection = selectedCount === quantity;

        if (hint) {
            hint.textContent = hasEnoughStock
                ? `Select exactly ${quantity} unused radio${quantity > 1 ? 's' : ''}. Selected ${selectedCount}/${quantity}.`
                : `Insufficient stock. Need ${quantity}, available ${availableCount}. Add/register WT first.`;
            hint.classList.toggle('text-red-500', !hasEnoughStock || (selectedCount > 0 && !hasExactSelection));
            hint.classList.toggle('text-emerald-600', hasEnoughStock && hasExactSelection);
        }

        if (quickHint) {
            quickHint.textContent = `Current request needs ${formatCount(quantity, 'unit')}. Register the unit only; set ownership in the approval form. Selected ${selectedCount}/${quantity}.`;
        }

        if (stockStatus) {
            stockStatus.textContent = hasEnoughStock
                ? `Stock available: ${availableCount}. Required: ${quantity}. Selected: ${selectedCount}/${quantity}.`
                : `Cannot approve yet: required ${quantity}, available ${availableCount}. Add ${quantity - availableCount} more WT unit${quantity - availableCount > 1 ? 's' : ''}.`;
            stockStatus.className = hasEnoughStock
                ? 'mb-3 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-[10px] font-black uppercase tracking-wider text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-950/30 dark:text-emerald-200'
                : 'mb-3 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-[10px] font-black uppercase tracking-wider text-red-700 dark:border-red-900/60 dark:bg-red-950/30 dark:text-red-200';
        }

        if (submitButton) {
            submitButton.disabled = !hasEnoughStock || !hasExactSelection;
            submitButton.textContent = hasExactSelection ? 'Confirm Approval' : `Select WT ${selectedCount}/${quantity}`;
        }

        renderAssignmentDetails();
    }

    function renderAssignmentDetails() {
        const selectedIds = ($('#approveRadioSelect').val() || []).map(String);
        const box = document.getElementById('assignmentDetailsBox');
        const list = document.getElementById('assignmentDetailsList');
        if (!box || !list) return;

        box.classList.toggle('hidden', selectedIds.length === 0);
        const existingValues = {};

        list.querySelectorAll('[data-assignment-row]').forEach(function(row) {
            const walkieId = row.getAttribute('data-walkie-id');
            existingValues[walkieId] = {
                ownership_type: row.querySelector(`[name="assignment_details[${walkieId}][ownership_type]"]`)?.value || '',
                ownership: row.querySelector(`[name="assignment_details[${walkieId}][ownership]"]`)?.value || '',
                shared_with: row.querySelector(`[name="assignment_details[${walkieId}][shared_with]"]`)?.value || '',
                department: row.querySelector(`[name="assignment_details[${walkieId}][department]"]`)?.value || '',
                position: row.querySelector(`[name="assignment_details[${walkieId}][position]"]`)?.value || '',
                remark: row.querySelector(`[name="assignment_details[${walkieId}][remark]"]`)?.value || ''
            };
        });

        list.innerHTML = '';

        selectedIds.forEach(function(walkieId, index) {
            const option = Array.from(document.querySelectorAll('#approveRadioSelect option'))
                .find(option => option.value === walkieId);
            const label = option ? option.textContent.trim() : `Walkie ${walkieId}`;
            const saved = existingValues[walkieId] || {};
            const pic = currentApprovalRequest.pic_details[index] || {};
            const defaultOwnershipType = saved.ownership_type || normalizeAssignmentType(pic.ownership_type || currentApprovalRequest.ownership_type);
            const defaultOwnership = saved.ownership || pic.name || defaultAssignmentOwnership(defaultOwnershipType);
            const defaultSharedWith = saved.shared_with || pic.shared_with || defaultAssignmentSharedWith(defaultOwnershipType);
            const defaultDepartment = saved.department || pic.department || currentApprovalRequest.department || '';
            const ownerPhone = pic.phone_no || '-';
            const pickupPerson = pic.pickup_person || currentApprovalRequest.pickup_representative_name || currentApprovalRequest.full_name || '-';
            const pickupPhone = pic.pickup_phone_no || '-';

            const row = document.createElement('div');
            row.className = 'rounded-xl border border-slate-200 bg-white p-3 dark:border-slate-700 dark:bg-slate-950';
            row.setAttribute('data-assignment-row', '1');
            row.setAttribute('data-walkie-id', walkieId);
            row.innerHTML = `
                <div class="mb-3 flex items-start justify-between gap-3">
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-widest text-sky-600 dark:text-sky-300">Unit ${index + 1}</p>
                        <p class="mt-1 text-[10px] font-black uppercase text-slate-700 dark:text-slate-200">${escapeHtml(label)}</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-[9px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400">Ownership Type</label>
                        <select name="assignment_details[${walkieId}][ownership_type]" class="assignment-field assignment-type w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                            <option value="INDIVIDUAL"${defaultOwnershipType === 'INDIVIDUAL' ? ' selected' : ''}>INDIVIDUAL</option>
                            <option value="SHARED"${defaultOwnershipType === 'SHARED' ? ' selected' : ''}>SHARED</option>
                            <option value="SPARE"${defaultOwnershipType === 'SPARE' ? ' selected' : ''}>SPARE</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-[9px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400">Ownership / Area</label>
                        <input name="assignment_details[${walkieId}][ownership]" value="${escapeAttribute(defaultOwnership)}" class="assignment-field assignment-ownership w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" placeholder="Name, place, bay, team, department, or SPARE">
                    </div>
                    <div class="assignment-shared-with-group ${defaultOwnershipType === 'SHARED' ? '' : 'hidden'} sm:col-span-2">
                        <label class="mb-1 block text-[9px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400">Shared With / Place</label>
                        <input name="assignment_details[${walkieId}][shared_with]" value="${escapeAttribute(defaultSharedWith)}" class="assignment-field assignment-shared-with w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" placeholder="Place, bay, team, or department">
                    </div>
                    <div>
                        <label class="mb-1 block text-[9px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400">Department</label>
                        <input name="assignment_details[${walkieId}][department]" value="${escapeAttribute(defaultDepartment)}" class="assignment-field w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" placeholder="Department / place">
                    </div>
                    <div>
                        <label class="mb-1 block text-[9px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400">Owner Phone No.</label>
                        <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">${escapeHtml(ownerPhone)}</div>
                    </div>
                    <div>
                        <label class="mb-1 block text-[9px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400">Who Will Pick Up</label>
                        <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">${escapeHtml(pickupPerson)}</div>
                    </div>
                    <div>
                        <label class="mb-1 block text-[9px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400">Pickup Phone</label>
                        <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">${escapeHtml(pickupPhone)}</div>
                    </div>
                </div>
            `;

            const typeSelect = row.querySelector('.assignment-type');
            const ownershipInput = row.querySelector('.assignment-ownership');
            const sharedWithGroup = row.querySelector('.assignment-shared-with-group');
            const syncOwnershipField = function() {
                sharedWithGroup.classList.toggle('hidden', typeSelect.value !== 'SHARED');
                ownershipInput.placeholder = typeSelect.value === 'SPARE' ? 'SPARE' : 'Name, place, bay, team, or department';
            };
            typeSelect.addEventListener('change', function() {
                syncOwnershipField();
                if (this.value === 'SPARE' && !ownershipInput.value.trim()) {
                    ownershipInput.value = 'SPARE';
                }
                if (this.value !== 'SPARE' && ownershipInput.value.trim().toUpperCase() === 'SPARE') {
                    ownershipInput.value = defaultAssignmentOwnership(this.value);
                }
            });
            syncOwnershipField();

            list.appendChild(row);
        });
    }

    function normalizeAssignmentType(type) {
        const normalized = String(type || 'INDIVIDUAL').toUpperCase();
        return ['INDIVIDUAL', 'SHARED', 'SPARE'].includes(normalized) ? normalized : 'INDIVIDUAL';
    }

    function defaultAssignmentOwnership(type) {
        return '';
    }

    function defaultAssignmentSharedWith(type) {
        return normalizeAssignmentType(type) === 'SHARED'
            ? (currentApprovalRequest.shared_with || currentApprovalRequest.department || '')
            : '';
    }

    function defaultAssignmentRemark() {
        if (currentApprovalRequest.request_type !== 'temporary_walkie_talkie') {
            return currentApprovalRequest.justifications || '';
        }

        return `Temporary request: ${formatApprovalDate(currentApprovalRequest.request_date)} - ${formatApprovalDate(currentApprovalRequest.end_date)} · ${formatCount(currentApprovalRequest.duration_days, 'day')}`;
    }

    function escapeHtml(value) {
        return String(value || '').replace(/[&<>"']/g, function(char) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[char];
        });
    }

    function escapeAttribute(value) {
        return escapeHtml(value).replace(/`/g, '&#096;');
    }

    function formatApprovalDate(dateValue) {
        if (!dateValue) return '-';
        const date = new Date(`${dateValue}T00:00:00`);
        if (Number.isNaN(date.getTime())) return dateValue;

        return date.toLocaleDateString('en-GB', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });
    }

    function ensureQuickSelectValue(selector, value) {
        if (!value) return;

        const $select = $(selector);
        if (!$select.length) return;

        const hasOption = $select.find('option').filter(function() {
            return this.value === String(value);
        }).length > 0;

        if (!hasOption) {
            $select.append(new Option(value, value, true, true));
        }

        $select.val(value).trigger('change');
    }

    function prefillQuickWtFromRequest() {
        ensureQuickSelectValue('#quick_ownership_type', currentApprovalRequest.ownership_type || 'INDIVIDUAL');
        ensureQuickSelectValue('#quick_ownership', currentApprovalRequest.full_name || '');
        ensureQuickSelectValue('#quick_department', currentApprovalRequest.department || '');
        ensureQuickSelectValue('#quick_position', currentApprovalRequest.position || '');

        const sharedWith = document.getElementById('quick_shared_with');
        if (sharedWith && currentApprovalRequest.ownership_type === 'SHARED') {
            sharedWith.value = currentApprovalRequest.shared_with || currentApprovalRequest.full_name || '';
        }

        const remark = document.querySelector('#quickWtForm textarea[name="remark"]');
        if (remark && currentApprovalRequest.request_type === 'temporary_walkie_talkie') {
            remark.value = `Temporary request: ${formatApprovalDate(currentApprovalRequest.request_date)} - ${formatApprovalDate(currentApprovalRequest.end_date)} · ${formatCount(currentApprovalRequest.duration_days, 'day')}`;
        }

        syncQuickSharedWith();
    }

    function syncAccessorySelectAll() {
        const checkboxes = Array.from(document.querySelectorAll('#approveModal .accessory-toggle'));
        if (checkboxes.length === 0) return;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('#approveModal .accessory-toggle');
        const clickAllButton = document.getElementById('accessoryClickAllBtn');

        if (clickAllButton) {
            clickAllButton.addEventListener('click', function() {
                const checkboxes = Array.from(document.querySelectorAll('#approveModal .accessory-toggle'));
                const shouldCheckAll = checkboxes.some(checkbox => !checkbox.checked);
                setAllAccessories(shouldCheckAll);
            });
        }

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', syncAccessorySelectAll);
        });
    });

    function setAllAccessories(isChecked) {
        const checkboxes = document.querySelectorAll('#approveModal .accessory-toggle');

        checkboxes.forEach(checkbox => {
            checkbox.checked = isChecked;
        });
    }

    function openRequestFormModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            if (modal.parentElement !== document.body) {
                document.body.appendChild(modal);
            }
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeRequestFormModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            closeQuickWtModal();
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }
    }

    function openReturnFormModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            if (modal.parentElement !== document.body) {
                document.body.appendChild(modal);
            }
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeReturnFormModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    function openDamageFormModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            if (modal.parentElement !== document.body) {
                document.body.appendChild(modal);
            }
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeDamageFormModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    function closeApproveModal() {
        const modal = document.getElementById('approveModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }
    }

    function openRejectRequestModal(id, name) {
        const modal = document.getElementById('rejectRequestModal');
        const form = document.getElementById('rejectRequestForm');
        const userName = document.getElementById('rejectRequestUserName');
        const remark = document.getElementById('requestDisapprovalRemark');

        if (modal && form && userName && remark) {
            moveApprovalModalToBody(modal);
            form.action = "{{ url('admin/requests') }}/" + id + "/reject";
            userName.innerText = name || 'this user';
            form.reset();
            remark.value = '';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
            window.setTimeout(() => remark.focus(), 80);
        }
    }

    function closeRejectRequestModal() {
        const modal = document.getElementById('rejectRequestModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }
    }

    function openApproveDamageModal(id, name) {
        const modal = document.getElementById('approveDamageModal');
        const modalUserName = document.getElementById('damageApproveUserName');
        const form = document.getElementById('approveDamageForm');
        
        if (modal && modalUserName && form) {
            moveApprovalModalToBody(modal);
            modalUserName.innerText = name;
            form.action = "{{ url('admin/damage-reports') }}/" + id + "/approve";
            document.getElementById('damageApprovalRemark').value = '';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeApproveDamageModal() {
        const modal = document.getElementById('approveDamageModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }
    }

    function openRejectDamageModal(id, name) {
        const modal = document.getElementById('rejectDamageModal');
        const modalUserName = document.getElementById('damageRejectUserName');
        const form = document.getElementById('rejectDamageForm');
        const remark = document.getElementById('damageDisapprovalRemark');
        
        if (modal && modalUserName && form && remark) {
            moveApprovalModalToBody(modal);
            modalUserName.innerText = name;
            form.action = "{{ url('admin/damage-reports') }}/" + id + "/reject";
            form.reset();
            remark.value = '';
            remark.defaultValue = '';
            remark.placeholder = 'Type rejection reason here, example: details incomplete or no fault found.';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
            window.setTimeout(() => remark.focus(), 80);
        }
    }

    function closeRejectDamageModal() {
        const modal = document.getElementById('rejectDamageModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }
    }

    function moveApprovalModalToBody(modal) {
        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeApproveModal();
            closeRejectRequestModal();
            closeApproveDamageModal();
            closeRejectDamageModal();
        }
    });

</script>
@endpush
