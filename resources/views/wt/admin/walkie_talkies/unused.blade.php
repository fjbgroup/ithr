@extends('wt.layouts.admin')

@section('title', 'Unused Walkie Talkies')

@section('content')
<style>
    .unused-table-shell {
        border: 1px solid #dbe3ef;
        border-radius: 8px;
        background: #ffffff;
        overflow: hidden;
    }
    .dark .unused-table-shell {
        border-color: #263244;
        background: #111827;
    }
    .unused-scroll {
        overflow-x: auto;
        width: 100%;
    }
    .unused-table {
        min-width: 1900px;
        width: 100%;
        border-collapse: collapse;
    }
    .unused-table th {
        padding: 10px 12px;
        background: #e9eff7;
        border: 1px solid #d5dfec;
        color: #475569;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        white-space: nowrap;
        text-align: left;
    }
    .unused-table td {
        padding: 9px 12px;
        border: 1px solid #e2e8f0;
        color: #263244;
        font-size: 11px;
        font-weight: 700;
        vertical-align: top;
        white-space: nowrap;
    }
    .unused-table td.wrap-cell {
        white-space: normal;
        min-width: 220px;
        max-width: 320px;
        line-height: 1.35;
    }
    .dark .unused-table th {
        background: #1f2937;
        border-color: #334155;
        color: #cbd5e1;
    }
    .dark .unused-table td {
        background: #111827;
        border-color: #263244;
        color: #dbe4f0;
    }
    .unused-table tbody tr:hover td {
        background: #f8fafc;
    }
.dark .unused-table tbody tr:hover td {
        background: #172033;
    }
    .unused-filter-panel {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 270px auto;
        gap: 14px;
        align-items: end;
        margin: 0 0 14px;
        padding: 12px 16px 16px;
        border: 1px solid #dbe3ef;
        border-radius: 8px;
        background: #f8fafc;
    }
    .dark .unused-filter-panel {
        border-color: #263244;
        background: #111827;
    }
    .unused-filter-field label {
        display: block;
        margin-bottom: 7px;
        color: #526781;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }
    .dark .unused-filter-field label {
        color: #94a3b8;
    }
    .unused-filter-input,
    .unused-filter-select {
        width: 100%;
        height: 38px;
        border: 1px solid #cbd5e1;
        border-radius: 7px;
        background: #ffffff;
        color: #0f172a;
        padding: 0 12px;
        font-size: 12px;
        font-weight: 700;
        outline: none;
    }
    .unused-filter-input:focus,
    .unused-filter-select:focus {
        border-color: #94a3b8;
        box-shadow: 0 0 0 3px rgba(148, 163, 184, 0.18);
    }
    .dark .unused-filter-input,
    .dark .unused-filter-select {
        border-color: #334155;
        background: #0f172a;
        color: #e2e8f0;
    }
    .unused-filter-reset {
        height: 38px;
        border: 1px solid #cbd5e1;
        border-radius: 7px;
        background: #ffffff;
        color: #0f172a;
        padding: 0 14px;
        font-size: 12px;
        font-weight: 800;
    }
    .dark .unused-filter-reset {
        border-color: #334155;
        background: #0f172a;
        color: #e2e8f0;
    }
    .unused-pill {
        display: inline-flex;
        align-items: center;
        min-height: 22px;
        border-radius: 999px;
        border: 1px solid #bbf7d0;
        background: #dcfce7;
        color: #166534;
        padding: 3px 8px;
        font-size: 9px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .dark .unused-pill {
        border-color: #14532d;
        background: #052e16;
        color: #86efac;
    }
    .unused-search {
        width: 100%;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        padding: 10px 12px;
        font-size: 12px;
        font-weight: 700;
        color: #1f2937;
        outline: none;
    }
    .unused-search:focus {
        border-color: #38bdf8;
        box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.16);
    }
    .unused-pagination-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 14px 18px;
        border-top: 1px solid #dbe3ef;
        background: #ffffff;
    }
    .dark .unused-pagination-bar {
        border-top-color: #263244;
        background: #111827;
    }
    .unused-pagination-info {
        color: #64748b;
        font-size: 12px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .unused-pagination-actions {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }
    .unused-page-btn {
        min-width: 38px;
        height: 34px;
        border-radius: 7px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #475569;
        padding: 0 12px;
        font-size: 12px;
        font-weight: 900;
        transition: 0.15s ease;
    }
    .unused-page-btn:hover:not(:disabled),
    .unused-page-btn.active {
        border-color: #94a3b8;
        background: #f1f5f9;
        color: #1f2937;
    }
    .unused-page-btn:disabled {
        cursor: not-allowed;
        opacity: 0.45;
    }
    .unused-page-btn.nav-btn {
        min-width: 92px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        color: #64748b;
    }
    .unused-actions {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }
    .unused-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 28px;
        border-radius: 7px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #334155;
        padding: 0 10px;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        transition: 0.15s ease;
    }
    .unused-action-btn:hover {
        border-color: #94a3b8;
        background: #f1f5f9;
        color: #0f172a;
    }
    .unused-action-btn.delete {
        border-color: #fecaca;
        color: #b91c1c;
    }
    .unused-action-btn.delete:hover {
        border-color: #f87171;
        background: #fef2f2;
        color: #991b1b;
    }
    .unused-use-modal {
        position: fixed;
        inset: 0;
        z-index: 2147483200;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(15, 23, 42, 0.56);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }
    .unused-use-modal.active { display: flex; }
    .unused-use-card {
        width: min(100%, 720px);
        max-height: min(92vh, 760px);
        overflow: hidden;
        border: 1px solid #dbe3ef;
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 28px 70px rgba(15, 23, 42, 0.28);
    }
    .dark .unused-use-card {
        border-color: #334155;
        background: #0f172a;
    }
    .unused-use-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 18px 20px;
        border-bottom: 1px solid #eef2f7;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    }
    .dark .unused-use-header {
        border-bottom-color: #334155;
        background: linear-gradient(180deg, #111827 0%, #0f172a 100%);
    }
    .unused-use-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 11px;
        border: 1px solid #86efac;
        background: #f0fdf4;
        color: #166534;
    }
    .unused-use-title {
        color: #1f2937;
        font-size: 14px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .dark .unused-use-title { color: #f8fafc; }
    .unused-use-subtitle {
        margin-top: 2px;
        color: #64748b;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .dark .unused-use-subtitle { color: #94a3b8; }
    .unused-use-close {
        margin-left: auto;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border: 1px solid #e2e8f0;
        border-radius: 9px;
        background: #ffffff;
        color: #64748b;
    }
    .dark .unused-use-close {
        border-color: #334155;
        background: #111827;
        color: #cbd5e1;
    }
    .unused-use-body {
        max-height: calc(92vh - 150px);
        overflow-y: auto;
        padding: 20px;
    }
    .unused-use-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }
    .unused-use-field.full { grid-column: 1 / -1; }
    .unused-use-field label {
        display: block;
        margin-bottom: 7px;
        color: #64748b;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }
    .dark .unused-use-field label { color: #94a3b8; }
    .unused-use-field input,
    .unused-use-field select,
    .unused-use-field textarea {
        width: 100%;
        min-height: 38px;
        border-radius: 10px;
        border: 1px solid #dbe3ef;
        background: #f8fafc;
        color: #1f2937;
        padding: 9px 11px;
        font-size: 12px;
        font-weight: 800;
        outline: none;
    }
    .unused-use-field textarea {
        min-height: 92px;
        resize: vertical;
        text-transform: none !important;
    }
    .dark .unused-use-field input,
    .dark .unused-use-field select,
    .dark .unused-use-field textarea {
        border-color: #334155;
        background: #111827;
        color: #e2e8f0;
    }
    .unused-use-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 16px 20px 20px;
        border-top: 1px solid #eef2f7;
    }
    .dark .unused-use-footer { border-top-color: #334155; }
    .unused-use-btn {
        min-height: 36px;
        border-radius: 9px;
        border: 1px solid #cbd5e1;
        padding: 0 16px;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .unused-use-btn.cancel {
        background: #ffffff;
        color: #334155;
    }
    .unused-use-btn.submit {
        border-color: #16a34a;
        background: #16a34a;
        color: #ffffff;
    }
    .dark .unused-pagination-info {
        color: #94a3b8;
    }
    .dark .unused-page-btn {
        border-color: #334155;
        background: #0f172a;
        color: #cbd5e1;
    }
    .dark .unused-page-btn:hover:not(:disabled),
    .dark .unused-page-btn.active {
        border-color: #64748b;
        background: #1f2937;
        color: #f8fafc;
    }
    .dark .unused-action-btn {
        border-color: #334155;
        background: #0f172a;
        color: #dbe4f0;
    }
    .dark .unused-action-btn:hover {
        border-color: #64748b;
        background: #1f2937;
        color: #f8fafc;
    }
    .dark .unused-action-btn.delete {
        border-color: #7f1d1d;
        color: #fca5a5;
    }
    .dark .unused-action-btn.delete:hover {
        border-color: #ef4444;
        background: #450a0a;
        color: #fee2e2;
    }
    @media (max-width: 760px) {
        .unused-filter-panel {
            grid-template-columns: 1fr;
        }
        .unused-pagination-bar {
            align-items: flex-start;
            flex-direction: column;
        }
        .unused-pagination-actions {
            justify-content: flex-start;
        }
        .unused-use-grid {
            grid-template-columns: 1fr;
        }
    }

    .unused-filter-panel {
        margin: 0 0 10px !important;
        padding: 10px 12px !important;
        border-radius: 6px !important;
        border: 1px solid #263244 !important;
        background: #111827 !important;
        box-shadow: none !important;
        gap: 10px !important;
    }
    .unused-filter-field label {
        margin-bottom: 5px !important;
        color: #94a3b8 !important;
        font-size: 9px !important;
        line-height: 1 !important;
        letter-spacing: 0.08em !important;
        font-weight: 700 !important;
    }
    .unused-filter-input,
    .unused-filter-select,
    .unused-filter-reset {
        height: 32px !important;
        border-radius: 6px !important;
        background: #0f172a !important;
        border: 1px solid #334155 !important;
        color: #e5e7eb !important;
        font-size: 11px !important;
        font-weight: 400 !important;
        box-shadow: none !important;
    }
    .unused-table-shell {
        border-radius: 6px !important;
        border: 1px solid #263244 !important;
        background: #111827 !important;
        box-shadow: none !important;
    }
    .unused-table th {
        height: 34px !important;
        padding: 8px 10px !important;
        background: #1f2937 !important;
        border: 1px solid #2f3b4f !important;
        color: #cbd5e1 !important;
        font-size: 10px !important;
        font-weight: 600 !important;
        line-height: 1.1 !important;
        letter-spacing: 0.05em !important;
    }
    .unused-table td {
        height: 38px !important;
        padding: 7px 10px !important;
        background: #111827 !important;
        border: 1px solid #263244 !important;
        color: #dbe4f0 !important;
        font-size: 11px !important;
        font-weight: 400 !important;
        line-height: 1.25 !important;
        vertical-align: middle !important;
    }
    .unused-table tbody tr:hover td {
        background: #172033 !important;
    }
    .unused-pagination-bar {
        min-height: 64px !important;
        padding: 10px 20px !important;
        gap: 12px !important;
        border-top: 1px solid #263244 !important;
        background: #111827 !important;
    }
    .unused-pagination-info {
        color: #dbeafe !important;
        font-size: 17px !important;
        font-weight: 900 !important;
        letter-spacing: 0 !important;
        text-transform: none !important;
        white-space: nowrap !important;
    }
    .unused-pagination-actions {
        gap: 10px !important;
        flex-wrap: nowrap !important;
    }
    .unused-page-btn {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 52px !important;
        height: 44px !important;
        padding: 0 14px !important;
        border-radius: 8px !important;
        border: 1px solid #2f4d74 !important;
        background: #0f172a !important;
        color: #bfdbfe !important;
        font-size: 13px !important;
        font-weight: 900 !important;
        line-height: 1 !important;
    }
    .unused-page-btn.nav-btn {
        min-width: 128px !important;
        color: #cbd5e1 !important;
        font-size: 16px !important;
    }
    .unused-page-btn:hover:not(:disabled),
    .unused-page-btn.active {
        border-color: #3b82f6 !important;
        background: #0f3a72 !important;
        color: #ffffff !important;
    }
    .unused-page-btn:disabled {
        cursor: not-allowed !important;
        opacity: 0.45 !important;
    }
    .unused-page-ellipsis {
        display: inline-flex;
        align-items: center;
        height: 44px;
        color: #94a3b8;
        font-size: 13px;
        font-weight: 900;
    }
    @media (max-width: 900px) {
        .unused-filter-panel {
            grid-template-columns: 1fr !important;
        }
        .unused-pagination-bar {
            align-items: stretch !important;
            flex-direction: column !important;
        }
        .unused-pagination-actions {
            justify-content: flex-start !important;
            overflow-x: auto !important;
        }
    }
    .unused-page-shell {
        display: grid !important;
        gap: 14px !important;
        padding: 0 14px !important;
    }
    .unused-page-shell > .page-header-block {
        position: relative !important;
        margin: 0 !important;
        padding: 18px 28px !important;
        border: 1px solid rgba(148, 163, 184, 0.08) !important;
        border-left: 0 !important;
        border-radius: 14px !important;
        background: linear-gradient(90deg, rgba(31, 41, 55, 0.95), rgba(30, 41, 59, 0.95)) !important;
        box-shadow: none !important;
        overflow: hidden !important;
    }
    .unused-page-shell .page-title-standard {
        color: #f8fafc !important;
        font-size: 22px !important;
        font-weight: 900 !important;
        line-height: 1.1 !important;
        margin: 0 0 8px !important;
    }
    .unused-page-shell .page-subtitle-standard {
        color: #aab5c7 !important;
        font-size: 12px !important;
        font-weight: 900 !important;
        letter-spacing: 0.22em !important;
        line-height: 1.2 !important;
        margin: 0 !important;
        text-transform: uppercase !important;
    }
    .unused-page-shell .unused-pill {
        min-height: 40px !important;
        padding: 0 18px !important;
        border-radius: 999px !important;
        border: 1px solid #25364f !important;
        background: #0f172a !important;
        color: #e5e7eb !important;
        font-size: 13px !important;
    }

    /* Final unused page theme pass: keep light and dark modes readable. */
    body .content-surface:has(.unused-page-shell) {
        padding: 10px !important;
        border-radius: 12px !important;
    }
    .unused-page-shell {
        gap: 10px !important;
        padding: 0 !important;
    }
    .unused-page-shell > .page-header-block {
        min-height: 42px !important;
        padding: 8px 14px !important;
        border-radius: 7px !important;
        border-left-width: 4px !important;
        gap: 8px !important;
        align-items: center !important;
    }
    .unused-page-shell .page-title-standard {
        font-size: 19px !important;
        line-height: 1 !important;
        margin: 0 0 3px !important;
    }
    .unused-page-shell .page-subtitle-standard {
        font-size: 8px !important;
        line-height: 1.1 !important;
        letter-spacing: 0.24em !important;
    }
    .unused-page-shell > .page-header-block .wt-btn {
        min-width: 74px !important;
        min-height: 30px !important;
        padding: 0 12px !important;
        border-radius: 7px !important;
        font-size: 12px !important;
    }
    .unused-filter-panel {
        margin: 0 !important;
        padding: 10px 12px !important;
        border-radius: 7px !important;
        gap: 10px !important;
    }
    .unused-filter-field label {
        margin-bottom: 5px !important;
        font-size: 10px !important;
    }
    .unused-filter-input,
    .unused-filter-select,
    .unused-filter-reset {
        height: 34px !important;
        min-height: 34px !important;
        border-radius: 7px !important;
        font-size: 12px !important;
    }
    .unused-table th {
        height: 34px !important;
        padding: 7px 10px !important;
        font-size: 11px !important;
    }
    .unused-table td {
        height: 34px !important;
        min-height: 34px !important;
        padding: 6px 10px !important;
        font-size: 11px !important;
    }
    .unused-pagination-bar {
        min-height: 44px !important;
        padding: 7px 12px !important;
        gap: 10px !important;
    }
    .unused-pagination-info {
        font-size: 12px !important;
    }
    .unused-pagination-actions {
        gap: 8px !important;
    }
    .unused-page-btn {
        height: 30px !important;
        min-width: 34px !important;
        padding: 0 9px !important;
        border-radius: 7px !important;
        font-size: 11px !important;
    }
    .unused-page-btn.nav-btn {
        min-width: 76px !important;
        font-size: 12px !important;
    }

    html:not(.dark) body .content-surface:has(.unused-page-shell) {
        background: #ffffff !important;
        color: #0f172a !important;
    }
    html:not(.dark) body .content-surface .unused-page-shell > .page-header-block {
        background: #ffffff !important;
        border: 1px solid #d8e1ed !important;
        border-left: 4px solid #0284c7 !important;
    }
    html:not(.dark) body .content-surface .unused-page-shell .page-title-standard {
        color: #0f172a !important;
    }
    html:not(.dark) body .content-surface .unused-page-shell .page-subtitle-standard {
        color: #64748b !important;
    }
    html:not(.dark) body .content-surface .unused-page-shell > .page-header-block .wt-btn,
    html:not(.dark) body .content-surface .unused-filter-reset,
    html:not(.dark) body .content-surface .unused-page-btn {
        background: #ffffff !important;
        border-color: #cbd5e1 !important;
        color: #334155 !important;
    }
    html:not(.dark) body .content-surface .unused-filter-panel {
        background: #ffffff !important;
        border-color: #d8e1ed !important;
    }
    html:not(.dark) body .content-surface .unused-filter-field label {
        color: #64748b !important;
    }
    html:not(.dark) body .content-surface .unused-filter-input,
    html:not(.dark) body .content-surface .unused-filter-select {
        background: #ffffff !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }
    html:not(.dark) body .content-surface .unused-filter-input::placeholder {
        color: #94a3b8 !important;
    }
    html:not(.dark) body .content-surface .unused-table-shell {
        background: #ffffff !important;
        border-color: #cbd5e1 !important;
    }
    html:not(.dark) body .content-surface .unused-table th {
        background: #f8fafc !important;
        border-color: #d8e1ed !important;
        color: #526781 !important;
    }
    html:not(.dark) body .content-surface .unused-table td {
        background: #ffffff !important;
        border-color: #e2e8f0 !important;
        color: #1f2937 !important;
    }
    html:not(.dark) body .content-surface .unused-table tbody tr:hover td {
        background: #f8fafc !important;
    }
    html:not(.dark) body .content-surface .unused-pagination-bar {
        background: #ffffff !important;
        border-top-color: #d8e1ed !important;
    }
    html:not(.dark) body .content-surface .unused-pagination-info {
        color: #334155 !important;
    }
    html:not(.dark) body .content-surface .unused-page-btn:hover:not(:disabled),
    html:not(.dark) body .content-surface .unused-page-btn.active {
        background: #dbeafe !important;
        border-color: #60a5fa !important;
        color: #1e3a8a !important;
    }

    .dark body .content-surface:has(.unused-page-shell) {
        background: #0f172a !important;
        color: #e5e7eb !important;
    }
    .dark body .content-surface .unused-page-shell > .page-header-block {
        background: linear-gradient(90deg, rgba(31, 41, 55, 0.98), rgba(30, 41, 59, 0.98)) !important;
        border-color: rgba(148, 163, 184, 0.12) !important;
        border-left-color: #f2c48d !important;
    }
    .dark body .content-surface .unused-page-shell .page-title-standard {
        color: #f8fafc !important;
    }
    .dark body .content-surface .unused-page-shell .page-subtitle-standard {
        color: #aab5c7 !important;
    }
    .dark body .content-surface .unused-filter-panel,
    .dark body .content-surface .unused-table-shell,
    .dark body .content-surface .unused-pagination-bar {
        background: #111827 !important;
        border-color: #263244 !important;
    }
    .dark body .content-surface .unused-filter-input,
    .dark body .content-surface .unused-filter-select,
    .dark body .content-surface .unused-filter-reset,
    .dark body .content-surface .unused-page-btn {
        background: #0f172a !important;
        border-color: #334155 !important;
        color: #e2e8f0 !important;
    }
    .dark body .content-surface .unused-table th {
        background: #111827 !important;
        border-color: #2b3950 !important;
        color: #dbeafe !important;
    }
    .dark body .content-surface .unused-table td {
        background: #111827 !important;
        border-color: #263244 !important;
        color: #dbe4f0 !important;
    }
    .dark body .content-surface .unused-table tbody tr:hover td {
        background: #172033 !important;
    }
    body .content-surface .unused-table th,
    html:not(.dark) body .content-surface .unused-table th,
    .dark body .content-surface .unused-table th {
        padding: 14px 16px !important;
    }
</style>

@include('wt.admin.partials.inventory-management-ui')

@php
    $booleanColumns = ['id_change_done', 'is_special_use', 'special_use_returned'];
    $wrapColumns = ['remark'];
    $columnLabels = [
        'radio_id' => 'Radio ID',
        'serial_number' => 'Serial No',
        'model' => 'Model',
        'status' => 'Status',
        'ownership_type' => 'Current Ownership Type',
        'shared_with' => 'Shared With',
        'ownership' => 'Current Ownership',
        'department' => 'Department',
        'position' => 'Position',
        'temporary_radio_id' => 'Temporary Swapped WT Radio ID',
        'tracking_ref' => 'Tracking Ref',
        'remark' => 'Remarks',
        'need_to_change_id' => 'Need To Change Into',
        'id_change_done' => 'Done',
        'ownership_type_to_be' => 'Ownership Type To Be',
        'is_special_use' => 'Is Special Use',
        'special_use_returned' => 'Returned',
    ];
    $compactColumns = collect($columns)
        ->filter(fn ($column) => in_array($column['key'], ['radio_id', 'status', 'serial_number', 'model', 'ownership'], true))
        ->values();
@endphp

<div class="unused-page-shell">
<div class="page-header-block flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
    <div>
        <h3 class="page-title-standard">Unused Walkie Talkies</h3>
        <p class="page-subtitle-standard">All unused units displayed using the same column order and headings as the Excel import.</p>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('wt.admin.walkies.index') }}" class="wt-btn wt-btn-soft">Back</a>
    </div>
</div>

<div class="unused-filter-panel">
    <div class="unused-filter-field">
        <label for="unusedSearchInput">Search</label>
        <input id="unusedSearchInput" type="search" class="unused-filter-input" placeholder="Keywords">
    </div>
    <div class="unused-filter-field">
        <label for="unusedStatusFilter">Status</label>
        <select id="unusedStatusFilter" class="unused-filter-select">
            <option value="">All Status</option>
            <option value="UNUSED">Unused</option>
        </select>
    </div>
    <button type="button" id="unusedResetFilters" class="unused-filter-reset">Reset</button>
</div>

<div class="unused-table-shell">
    <div class="unused-scroll">
        <table class="unused-table" id="unusedTable">
            <colgroup>
                <col class="col-radio-id">
                <col class="col-status">
                <col class="col-serial-no">
                <col class="col-model">
                <col class="col-ownership">
                <col class="col-action">
            </colgroup>
            <thead>
                <tr>
                    @foreach($compactColumns as $column)
                        <th @class(['text-center' => in_array($column['key'], ['serial_number', 'model'], true)]) style="{{ in_array($column['key'], ['serial_number', 'model'], true) ? 'text-align:center !important;' : '' }}">{{ strtoupper($columnLabels[$column['key']] ?? str_replace('_', ' ', $column['label'])) }}</th>
                    @endforeach
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $record)
                    <tr data-status="{{ strtoupper($record->status ?? 'UNUSED') }}">
                        @foreach($compactColumns as $column)
                            @php
                                $key = $column['key'];
                                $value = $record->getAttribute($key);
                                if (in_array($key, $booleanColumns, true)) {
                                    $value = (int) $value === 1 ? 'YES' : 'NO';
                                }
                            @endphp
                            <td @class([
                                'wrap-cell' => in_array($key, $wrapColumns, true),
                                'text-center' => in_array($key, ['serial_number', 'model'], true),
                            ]) style="{{ in_array($key, ['serial_number', 'model'], true) ? 'text-align:center !important;' : '' }}">
                                {{ filled($value) ? $value : '-' }}
                            </td>
                        @endforeach
                        <td>
                            @if(auth('wt')->user()->wt_role === 'admin_it')
                                <div class="unused-actions">
                                    <button type="button" class="unused-action-btn" onclick="openGlobalWalkieTimeline('{{ $record->walkie_id }}')">View</button>
                                    <button
                                        type="button"
                                        class="unused-action-btn used"
                                        data-action="{{ route('wt.admin.walkies.updateMeta', $record->walkie_id) }}"
                                        data-radio-id="{{ e($record->radio_id) }}"
                                        data-serial-number="{{ e($record->serial_number) }}"
                                        data-model="{{ e($record->model) }}"
                                        data-ownership-type="{{ e($record->ownership_type) }}"
                                        data-shared-with="{{ e($record->shared_with) }}"
                                        data-ownership="{{ e($record->ownership) }}"
                                        data-position="{{ e($record->position) }}"
                                        data-department="{{ e($record->department) }}"
                                        data-remark="{{ e($record->remark) }}"
                                        onclick="openUnusedUseModal(this)"
                                    >Used</button>
                                    <a href="{{ route('wt.admin.walkies.edit', ['walkie' => $record->walkie_id, 'source' => 'unused']) }}" class="unused-action-btn">Edit</a>
                                    <form action="{{ route('wt.admin.walkies.forceDelete', $record->walkie_id) }}" method="POST" data-modern-confirm-title="Confirm Unused Action" data-modern-confirm="Delete unit {{ $record->radio_id }} permanently?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="unused-action-btn delete">Delete</button>
                                    </form>
                                </div>
                            @else
                                <button type="button" class="unused-action-btn" onclick="openGlobalWalkieTimeline('{{ $record->walkie_id }}')">View</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr class="unused-empty-row">
                        <td colspan="{{ $compactColumns->count() + 1 }}" class="text-center dataTables_empty">NO ITEMS FOUND</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="unused-pagination-bar">
        <div id="unusedPaginationInfo" class="unused-pagination-info">Total: 0 items</div>
    </div>
</div>
</div>

<div id="unusedUseModal" class="unused-use-modal" aria-hidden="true">
    <div class="unused-use-card" role="dialog" aria-modal="true" aria-labelledby="unusedUseTitle">
        <div class="unused-use-header">
            <div class="unused-use-icon"><i class="fas fa-play"></i></div>
            <div>
                <div id="unusedUseTitle" class="unused-use-title">Set Unit As Used</div>
                <div class="unused-use-subtitle">Fill required ownership details</div>
            </div>
            <button type="button" class="unused-use-close" onclick="closeUnusedUseModal()" title="Close"><i class="fas fa-times"></i></button>
        </div>
        <form id="unusedUseForm" method="POST">
            @csrf
            <input type="hidden" name="status" value="IN USE">
            <input type="hidden" name="return_route" value="wt.admin.walkies.unused">
            <input type="hidden" name="need_to_change_id" value="0">
            <input type="hidden" name="id_change_done" value="0">
            <input type="hidden" name="is_special_use" value="0">
            <input type="hidden" name="special_use_returned" value="0">

            <div class="unused-use-body">
                <div class="unused-use-grid">
                    <div class="unused-use-field">
                        <label>Radio ID</label>
                        <input type="text" name="radio_id" id="used_radio_id" required>
                    </div>
                    <div class="unused-use-field">
                        <label>Serial No.</label>
                        <input type="text" name="serial_number" id="used_serial_number" required>
                    </div>
                    <div class="unused-use-field">
                        <label>Model</label>
                        <input type="text" name="model" id="used_model" list="unused_model_options" placeholder="Type or select model" required>
                        <datalist id="unused_model_options">
                            @foreach($walkieModels as $model)
                                <option value="{{ $model }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                    <div class="unused-use-field">
                        <label>Ownership Type</label>
                        <input type="text" name="ownership_type" id="used_ownership_type" list="unused_ownership_type_options" placeholder="Type or select type" required oninput="toggleUsedSharedWith()" onchange="toggleUsedSharedWith()">
                        <datalist id="unused_ownership_type_options">
                            @foreach($ownershipTypeOptions as $ot)
                            <option value="{{ $ot }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                    <div class="unused-use-field full" id="used_shared_with_group" style="display:none;">
                        <label>Shared With</label>
                        <input type="text" name="shared_with" id="used_shared_with" placeholder="User / team / department">
                    </div>
                    <div class="unused-use-field">
                        <label>Ownership</label>
                        <input type="text" name="ownership" id="used_ownership" placeholder="Owner name / location">
                    </div>
                    <div class="unused-use-field">
                        <label>Position</label>
                        <input type="text" name="position" id="used_position" placeholder="Position">
                    </div>
                    <div class="unused-use-field full">
                        <label>Department</label>
                        <input type="text" name="department" id="used_department" placeholder="Department">
                    </div>
                    <div class="unused-use-field full">
                        <label>Remark (optional)</label>
                        <textarea name="remark" id="used_remark" placeholder="Add note if needed..." data-preserve-case="true"></textarea>
                    </div>
                </div>
            </div>
            <div class="unused-use-footer">
                <button type="button" class="unused-use-btn cancel" onclick="closeUnusedUseModal()">Cancel</button>
                <button type="submit" class="unused-use-btn submit">Confirm Used</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openUnusedUseModal(button) {
        const modal = document.getElementById('unusedUseModal');
        const form = document.getElementById('unusedUseForm');
        if (!modal || !form) return;

        form.action = button.dataset.action;
        document.getElementById('used_radio_id').value = button.dataset.radioId || '';
        document.getElementById('used_serial_number').value = button.dataset.serialNumber || '';
        document.getElementById('used_model').value = button.dataset.model || '';
        const ownershipType = (button.dataset.ownershipType || '').toUpperCase();
        document.getElementById('used_ownership_type').value = ownershipType;
        document.getElementById('used_shared_with').value = button.dataset.sharedWith || '';
        document.getElementById('used_ownership').value = button.dataset.ownership || '';
        document.getElementById('used_position').value = button.dataset.position || '';
        document.getElementById('used_department').value = button.dataset.department || '';
        document.getElementById('used_remark').value = button.dataset.remark || '';
        toggleUsedSharedWith();

        modal.classList.add('active');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeUnusedUseModal() {
        const modal = document.getElementById('unusedUseModal');
        if (!modal) return;

        modal.classList.remove('active');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    function toggleUsedSharedWith() {
        const type = document.getElementById('used_ownership_type');
        const group = document.getElementById('used_shared_with_group');
        const input = document.getElementById('used_shared_with');
        const isShared = (type.value || '').toUpperCase() === 'SHARED';

        group.style.display = isShared ? 'block' : 'none';
        input.required = isShared;
        if (!isShared) input.value = '';
    }

    document.addEventListener('click', function(event) {
        const modal = document.getElementById('unusedUseModal');
        if (event.target === modal) {
            closeUnusedUseModal();
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeUnusedUseModal();
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('unusedSearchInput');
        const statusFilter = document.getElementById('unusedStatusFilter');
        const resetButton = document.getElementById('unusedResetFilters');
        const rows = Array.from(document.querySelectorAll('#unusedTable tbody tr'));
        const info = document.getElementById('unusedPaginationInfo');
        const actions = document.getElementById('unusedPaginationActions');
        const pageSize = 10;
        let currentPage = 1;
        let filteredRows = rows;

        function renderPagination() {
            const totalItems = filteredRows.length;

            rows.forEach(function(row) {
                row.style.display = 'none';
            });

            filteredRows.forEach(function(row) {
                row.style.display = '';
            });

            if (info) {
                info.textContent = `Total: ${totalItems} items`;
            }

            if (actions) actions.innerHTML = '';
        }

        if (input) {
            input.addEventListener('input', applyUnusedFilters);
        }

        if (statusFilter) {
            statusFilter.addEventListener('change', applyUnusedFilters);
        }

        if (resetButton) {
            resetButton.addEventListener('click', function() {
                if (input) input.value = '';
                if (statusFilter) statusFilter.value = '';
                applyUnusedFilters();
            });
        }

        function applyUnusedFilters() {
            const needle = (input?.value || '').trim().toLowerCase();
            const status = (statusFilter?.value || '').trim().toUpperCase();

            filteredRows = rows.filter(function(row) {
                const matchesSearch = !needle || row.textContent.toLowerCase().includes(needle);
                const matchesStatus = !status || (row.dataset.status || '').toUpperCase() === status;
                return matchesSearch && matchesStatus;
            });
            currentPage = 1;
            renderPagination();
        }

        renderPagination();
    });
</script>
@endsection
