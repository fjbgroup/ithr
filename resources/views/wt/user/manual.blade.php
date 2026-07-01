@extends(request()->routeIs('wt.admin.*') ? 'wt.layouts.admin' : 'wt.layouts.user')

@section('title', 'WT User Manual')
@section('page_title', 'WT User Manual')

@section('content')

@php
  $manualRole = auth('wt')->user()?->wt_role;
  $manualIsIct = $manualRole === 'admin_it';
  $manualIsExecutive = $manualRole === 'admin';
@endphp

<style>
  .manual-page {
    display: grid;
    gap: 20px;
  }

  .manual-hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
    min-height: 96px;
    padding: 18px 24px;
    border-radius: 13px;
    background: linear-gradient(90deg, rgba(31, 41, 55, 0.98), rgba(30, 41, 59, 0.98));
    color: #f8fafc;
  }

  html:not(.dark) .manual-hero,
  html[data-theme="light"] .manual-hero {
    border: 1px solid #d8e1ed;
    background: #ffffff;
    color: #172033;
  }

  .manual-title {
    margin: 0 0 8px;
    font-size: 28px;
    font-weight: 900;
    line-height: 1;
    letter-spacing: 0;
  }

  .manual-subtitle {
    margin: 0;
    color: #aab5c7;
    font-size: 13px;
    font-weight: 900;
    letter-spacing: 0.2em;
    line-height: 1.25;
    text-transform: uppercase;
  }

  html:not(.dark) .manual-subtitle,
  html[data-theme="light"] .manual-subtitle {
    color: #64748b;
  }

  .manual-role-switch {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-end;
    gap: 10px;
  }

  .manual-role-chip {
    display: inline-flex;
    min-height: 38px;
    align-items: center;
    justify-content: center;
    gap: 8px;
    border: 1px solid rgba(148, 163, 184, 0.28);
    border-radius: 999px;
    padding: 0 14px;
    color: inherit;
    font-size: 11px;
    font-weight: 900;
    letter-spacing: 0.08em;
    text-decoration: none;
    text-transform: uppercase;
  }

  .manual-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 18px;
  }

  .manual-page.role-executive .manual-card-executive,
  .manual-page.role-user .manual-card-executive {
    order: -1;
  }

  .manual-card,
  .manual-section {
    overflow: hidden;
    border: 1px solid var(--border);
    border-radius: 16px;
    background: var(--surface);
    box-shadow: var(--shadow);
  }

  .manual-card {
    padding: 20px;
  }

  .manual-card-head {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 14px;
  }

  .manual-icon {
    display: inline-flex;
    width: 42px;
    height: 42px;
    align-items: center;
    justify-content: center;
    border-radius: 13px;
    background: #e0f2fe;
    color: #0369a1;
    flex: 0 0 auto;
  }

  html.dark .manual-icon {
    background: rgba(14, 165, 233, 0.16);
    color: #7dd3fc;
  }

  .manual-card-title {
    margin: 0;
    color: var(--text);
    font-size: 16px;
    font-weight: 900;
    line-height: 1.2;
  }

  .manual-card-subtitle {
    margin: 3px 0 0;
    color: var(--muted);
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
  }

  .manual-list {
    display: grid;
    gap: 10px;
    margin: 0;
    padding: 0;
    list-style: none;
  }

  .manual-list li {
    display: grid;
    grid-template-columns: 24px 1fr;
    gap: 10px;
    color: var(--text);
    font-size: 13px;
    font-weight: 700;
    line-height: 1.5;
  }

  .manual-list li span {
    display: inline-flex;
    width: 24px;
    height: 24px;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    background: var(--soft-surface, #f8fafc);
    color: #0284c7;
    font-size: 10px;
    font-weight: 900;
  }

  .manual-section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 16px 20px;
    border-bottom: 1px solid var(--border);
    background: var(--soft-surface, #f8fafc);
  }

  .manual-section-title {
    margin: 0;
    color: var(--text);
    font-size: 14px;
    font-weight: 900;
    letter-spacing: 0.08em;
    text-transform: uppercase;
  }

  .manual-section-body {
    display: grid;
    gap: 14px;
    padding: 18px 20px 20px;
  }

  .manual-flow {
    display: grid;
    grid-template-columns: 140px 1fr;
    gap: 14px;
    align-items: start;
    padding: 14px;
    border: 1px solid var(--border);
    border-radius: 12px;
    background: color-mix(in srgb, var(--surface) 88%, var(--body-bg) 12%);
  }

  .manual-flow-label {
    display: inline-flex;
    min-height: 30px;
    align-items: center;
    justify-content: center;
    border-radius: 999px;
    background: #fef3c7;
    color: #b45309;
    padding: 0 10px;
    font-size: 10px;
    font-weight: 900;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    white-space: nowrap;
  }

  .manual-flow-text {
    margin: 0;
    color: var(--text);
    font-size: 13px;
    font-weight: 700;
    line-height: 1.55;
  }

  .manual-note {
    margin: 0;
    border-left: 4px solid #38bdf8;
    border-radius: 12px;
    background: rgba(56, 189, 248, 0.10);
    padding: 14px 16px;
    color: var(--text);
    font-size: 13px;
    font-weight: 700;
    line-height: 1.55;
  }

  @media (max-width: 900px) {
    .manual-hero,
    .manual-section-header {
      align-items: flex-start;
      flex-direction: column;
    }

    .manual-role-switch {
      justify-content: flex-start;
    }

    .manual-grid {
      grid-template-columns: 1fr;
    }

    .manual-flow {
      grid-template-columns: 1fr;
    }
  }
</style>

<div class="manual-page {{ $manualIsIct ? 'role-ict' : ($manualIsExecutive ? 'role-executive' : 'role-user') }}">
  <section class="manual-hero">
    <div>
      <h1 class="manual-title">{{ $manualIsIct ? 'ICT User Manual' : ($manualIsExecutive ? 'Executive User Manual' : 'WT User Manual') }}</h1>
      <p class="manual-subtitle">{{ $manualIsIct ? 'ICT approval, inventory, and system control guide' : ($manualIsExecutive ? 'Executive request, review, and status guide' : 'Guidance for ICT and Executive users') }}</p>
    </div>
    <div class="manual-role-switch" aria-label="Manual sections">
      <a class="manual-role-chip" href="#ict-manual"><i class="fa-solid fa-screwdriver-wrench"></i> ICT</a>
      <a class="manual-role-chip" href="#executive-manual"><i class="fa-solid fa-user-tie"></i> Executive</a>
    </div>
  </section>

  <div class="manual-grid">
    <section class="manual-card manual-card-ict" id="ict-manual">
      <div class="manual-card-head">
        <span class="manual-icon"><i class="fa-solid fa-screwdriver-wrench"></i></span>
        <div>
          <h2 class="manual-card-title">ICT User Manual</h2>
          <p class="manual-card-subtitle">System control and final approval</p>
        </div>
      </div>
      <ul class="manual-list">
        <li><span>1</span><div>Open <strong>Approval Inbox</strong> to review requests forwarded by Executive users and approve or reject them.</div></li>
        <li><span>2</span><div>Use <strong>Walkie Talkie List</strong>, <strong>Unused Unit</strong>, and <strong>Special Use</strong> to manage inventory records.</div></li>
        <li><span>3</span><div>Create, update, import, assign, return, or remove walkie talkie units from the inventory tools.</div></li>
        <li><span>4</span><div>Use <strong>Faulty Report</strong> and <strong>Maintenance</strong> to receive faulty units, track repair status, and close maintenance records.</div></li>
        <li><span>5</span><div>Manage users, master data, reports, activity logs, and database backup from the ICT-only system control menu.</div></li>
      </ul>
    </section>

    <section class="manual-card manual-card-executive" id="executive-manual">
      <div class="manual-card-head">
        <span class="manual-icon"><i class="fa-solid fa-user-tie"></i></span>
        <div>
          <h2 class="manual-card-title">Executive User Manual</h2>
          <p class="manual-card-subtitle">Request, review, and forward</p>
        </div>
      </div>
      <ul class="manual-list">
        <li><span>1</span><div>Open <strong>Request Walkie Talkie</strong> to submit long-term or temporary requests for yourself or another recipient.</div></li>
        <li><span>2</span><div>Use <strong>Approval Inbox</strong> to review user requests, returns, and faulty reports before forwarding them to ICT.</div></li>
        <li><span>3</span><div>Use <strong>My Inventory</strong> to check walkie talkies currently assigned to you.</div></li>
        <li><span>4</span><div>Submit <strong>Return Unit</strong> when a unit is no longer needed or <strong>Report Faulty</strong> when a unit has an issue.</div></li>
        <li><span>5</span><div>Open <strong>All Status</strong> to track request, handover, return, and faulty-report progress.</div></li>
      </ul>
    </section>
  </div>

  <section class="manual-section">
    <div class="manual-section-header">
      <h2 class="manual-section-title">Common Workflows</h2>
      <span class="manual-role-chip"><i class="fa-solid fa-route"></i> Quick Reference</span>
    </div>
    <div class="manual-section-body">
      <div class="manual-flow">
        <span class="manual-flow-label">New Request</span>
        <p class="manual-flow-text">Executive submits the request, Executive reviews and forwards it, then ICT gives the final approval and assigns the available walkie talkie unit.</p>
      </div>
      <div class="manual-flow">
        <span class="manual-flow-label">Return Unit</span>
        <p class="manual-flow-text">User or Executive submits the return, Executive confirms the returned unit where required, and ICT inventory is updated after confirmation.</p>
      </div>
      <div class="manual-flow">
        <span class="manual-flow-label">Faulty Unit</span>
        <p class="manual-flow-text">Reporter submits the faulty report with device details or evidence, Executive forwards it to ICT, and ICT manages repair, replacement, or closure.</p>
      </div>
      <div class="manual-flow">
        <span class="manual-flow-label">Status Check</span>
        <p class="manual-flow-text">Use Request Status or All Status to see whether an item is pending Executive review, forwarded to ICT, approved, rejected, returned, or completed.</p>
      </div>
    </div>
  </section>

  <section class="manual-section">
    <div class="manual-section-header">
      <h2 class="manual-section-title">Good Practice</h2>
      <span class="manual-role-chip"><i class="fa-solid fa-circle-info"></i> Notes</span>
    </div>
    <div class="manual-section-body">
      <p class="manual-note">Fill in complete staff, department, phone, date, quantity, and device details before submitting. For faulty reports, upload clear evidence when the Radio ID or Serial Number is not known.</p>
      <p class="manual-note">ICT users should keep master data and inventory records clean because those records control dropdowns, reports, assignment history, and approval accuracy across the WT System.</p>
    </div>
  </section>
</div>

@endsection
