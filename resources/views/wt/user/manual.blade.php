@extends(request()->routeIs('wt.admin.*') ? 'wt.layouts.admin' : 'wt.layouts.user')

@section('title', 'WT System User Manual')
@section('page_title', 'WT System User Manual')

@section('content')
<style>
  .readme-manual { width:100%;max-width:1120px;margin:0 auto;border:1px solid var(--border);border-radius:14px;background:var(--surface);box-shadow:var(--shadow);color:var(--text);overflow:hidden; }
  .readme-manual-header { padding:28px 32px 22px;border-bottom:1px solid var(--border);background:var(--soft-surface,#f8fafc); }
  .readme-manual-title { margin:0;color:var(--text);font-size:28px;font-weight:900;line-height:1.1;letter-spacing:0; }
  .readme-manual-lead { margin:10px 0 0;max-width:760px;color:var(--muted);font-size:14px;font-weight:700;line-height:1.6; }
  .readme-manual-body { padding:28px 32px 36px; }
  .readme-manual h2 { margin:30px 0 12px;padding-bottom:8px;border-bottom:1px solid var(--border);color:var(--text);font-size:20px;font-weight:900;letter-spacing:0; }
  .readme-manual h2:first-child { margin-top:0; }
  .readme-manual h3 { margin:22px 0 10px;color:var(--text);font-size:15px;font-weight:900;letter-spacing:.04em;text-transform:uppercase; }
  .readme-manual p,.readme-manual li { color:var(--text);font-size:13px;font-weight:700;line-height:1.65; }
  .readme-manual ol,.readme-manual ul { margin:0 0 14px 20px;padding:0; }
  .readme-manual li + li { margin-top:5px; }
  .readme-manual code { border:1px solid var(--border);border-radius:6px;background:var(--body-bg);padding:2px 6px;color:var(--text);font-size:12px;font-weight:900; }
  .readme-manual table { width:100%;margin:12px 0 18px;border-collapse:collapse;overflow:hidden;border:1px solid var(--border);border-radius:10px; }
  .readme-manual th,.readme-manual td { padding:12px 14px;border:1px solid var(--border);color:var(--text);font-size:12px;line-height:1.5;text-align:left;vertical-align:top; }
  .readme-manual th { background:var(--soft-surface,#f8fafc);color:var(--muted);font-weight:900;letter-spacing:.08em;text-transform:uppercase; }
  .readme-toc { display:flex;flex-wrap:wrap;gap:8px;margin-top:18px; }
  .readme-toc a { display:inline-flex;min-height:34px;align-items:center;border:1px solid var(--border);border-radius:999px;background:var(--surface);padding:0 12px;color:var(--text);font-size:11px;font-weight:900;letter-spacing:.06em;text-decoration:none;text-transform:uppercase; }
  .readme-toc a:hover { border-color:var(--accent);color:var(--accent); }
  .readme-callout { margin:18px 0;border-left:4px solid #0284c7;border-radius:10px;background:rgba(2,132,199,.08);padding:14px 16px; }
  @media (max-width:768px) {
    .readme-manual-header,.readme-manual-body { padding:20px; }
    .readme-manual-title { font-size:22px; }
    .readme-manual table { display:block;overflow-x:auto;white-space:nowrap; }
  }
</style>

<article class="readme-manual">
  <header class="readme-manual-header">
    <h1 class="readme-manual-title">WT System User Manual</h1>
    <p class="readme-manual-lead">This page works like a README inside the system. It explains how each user role should use the Walkie Talkie Management system, from login to requests, approvals, inventory, returns, faulty reports, and profile updates.</p>
    <nav class="readme-toc" aria-label="Manual sections">
      <a href="#login-and-navigation">Login</a>
      <a href="#user-roles">Roles</a>
      <a href="#ict-user-manual">ICT</a>
      <a href="#executive-admin-user-manual">Executive/Admin</a>
      <a href="#user-manual">User</a>
      <a href="#common-tips">Tips</a>
      <a href="#suggested-training-flow">Training Flow</a>
    </nav>
  </header>

  <div class="readme-manual-body">
    <h2 id="login-and-navigation">Login And Navigation</h2>
    <ol>
      <li>Open the WT System login page at <code>/wt</code>.</li>
      <li>Enter your username and password.</li>
      <li>Use the left sidebar to open modules.</li>
      <li>Use the top-right role switcher if your ICT account can switch between ICT and Executive view.</li>
      <li>Use <code>My Profile</code> to update your name, phone number, department, and position.</li>
      <li>Use <code>Sign Out</code> when finished.</li>
    </ol>

    <h2 id="user-roles">User Roles</h2>
    <table>
      <thead><tr><th>Role</th><th>Main Purpose</th></tr></thead>
      <tbody>
        <tr><td>ICT</td><td>Manage inventory, approvals, maintenance, users, master data, and audit records.</td></tr>
        <tr><td>Executive/Admin</td><td>Submit requests, review requests, forward approved items to ICT, and monitor status.</td></tr>
        <tr><td>User</td><td>Submit requests, returns, handovers, and faulty reports for personal WT usage.</td></tr>
      </tbody>
    </table>

    <h2 id="ict-user-manual">ICT User Manual</h2>
    <h3>1. Dashboard</h3>
    <ol><li>Login as an ICT account.</li><li>Open <code>Dashboard</code>.</li><li>Review the summary cards for requests, inventory, faulty reports, and system activity.</li><li>Use sidebar shortcuts to continue to the required module.</li></ol>
    <h3>2. Manage Inventory</h3>
    <ol><li>Open <code>Inventory Tools</code>.</li><li>Select <code>Inventory List</code>.</li><li>Search or filter the walkie talkie records.</li><li>Use row actions to view, edit, update status, or open a unit timeline.</li><li>To add units, use the create/import options available in the inventory page.</li><li>Use <code>Duplicated ID</code> to handle duplicate radio ID records.</li><li>Use <code>Special Use</code> for units assigned to special cases.</li></ol>
    <h3>3. Handle Approvals</h3>
    <ol><li>Open <code>Approvals</code>.</li><li>Select <code>Pending</code>.</li><li>Review each request, return, or faulty report.</li><li>Click <code>View Form</code> where available to inspect full details.</li><li>Approve, reject, confirm return, or forward based on the action shown.</li><li>Open <code>History</code> to review past decisions.</li></ol>
    <h3>4. Manage Faulty Reports</h3>
    <ol><li>Open <code>Faulty Reports</code>.</li><li>Select <code>User Reports</code>.</li><li>Review submitted faulty or damage reports.</li><li>Receive faulty WT units when they arrive at ICT.</li><li>Update repair status and remarks.</li><li>Return the original unit or assign temporary spare handling where applicable.</li><li>Select <code>Monthly Report</code> to review faulty trends for the last three months.</li></ol>
    <h3>5. Manage Users</h3>
    <ol><li>Open <code>System Control</code>.</li><li>Select <code>Users Control</code>.</li><li>Use <code>Add Account</code> to create or grant an Executive account.</li><li>Search staff by name or staff number to auto-fill details.</li><li>Use <code>More</code> on a user row to <code>View</code>, <code>Edit</code>, <code>Reset Password</code>, or <code>Delete</code>.</li><li>Review pending forgot-password requests and approve or reject them.</li></ol>
    <h3>6. Manage Master Data</h3>
    <ol><li>Open <code>Master Data</code>.</li><li>Add or edit dropdown options used across request forms.</li><li>Keep department, position, location, bay, and similar option lists clean.</li><li>Delete only unused or incorrect records.</li></ol>
    <h3>7. Review Logs And Backup</h3>
    <ol><li>Open <code>System Logs</code> to review WT system activity.</li><li>Use activity filters/search to inspect user actions.</li><li>Use database backup only when an authorized backup is required.</li></ol>

    <h2 id="executive-admin-user-manual">Executive/Admin User Manual</h2>
    <h3>1. Dashboard</h3>
    <ol><li>Login as an Executive/Admin account.</li><li>Open <code>Dashboard</code>.</li><li>Review pending approval counts, request status, and quick action areas.</li></ol>
    <h3>2. Submit A Walkie Talkie Request</h3>
    <ol><li>Open <code>Request Walkie Talkie</code>.</li><li>Choose <code>Long Term Request</code> or <code>Temporary Request</code>.</li><li>Fill in ownership or recipient details.</li><li>Enter quantity, purpose, date, location, and pickup details.</li><li>Complete the signature area if required.</li><li>Submit the form to send it for approval.</li></ol>
    <h3>3. Review And Forward Requests</h3>
    <ol><li>Open <code>Approvals</code>.</li><li>Select <code>Pending</code>.</li><li>Click a request row or <code>View Form</code> to inspect the submitted details.</li><li>Approve and forward valid requests to ICT.</li><li>Reject requests that are incomplete or incorrect, adding remarks when prompted.</li></ol>
    <h3>4. Return A Unit</h3>
    <ol><li>Open <code>Return Unit</code>.</li><li>Search for the assigned unit or request.</li><li>Fill in return person and return details.</li><li>Submit the return.</li><li>Monitor return progress in status pages.</li></ol>
    <h3>5. Report A Faulty Unit</h3>
    <ol><li>Open <code>Report Faulty</code>.</li><li>Select the affected walkie talkie unit.</li><li>Fill in reporter details, issue description, and evidence if required.</li><li>Submit the faulty report.</li><li>Monitor the report status from the relevant status page.</li></ol>
    <h3>6. Check My Inventory And Status</h3>
    <ol><li>Open <code>My Inventory</code> to view assigned WT units.</li><li>Use available action buttons to return or report faulty units.</li><li>Open <code>All Status</code> or <code>Request Status</code> to track requests, returns, and faulty reports.</li></ol>

    <h2 id="user-manual">User Manual</h2>
    <h3>1. Dashboard</h3>
    <ol><li>Login as a User account.</li><li>Open <code>Dashboard</code>.</li><li>Use the dashboard cards or sidebar to create requests, return units, report faulty units, or check status.</li></ol>
    <h3>2. Submit A WT Request</h3>
    <ol><li>Open <code>Create Request</code>.</li><li>Choose the approver if requested.</li><li>Fill in your name, staff ID, department, position, ownership type, bay if applicable, location, purpose, and justification.</li><li>Sign the request if the form asks for a signature.</li><li>Submit the request.</li><li>Open <code>Request Status</code> to track approval progress.</li></ol>
    <h3>3. Submit A Handover</h3>
    <ol><li>Open <code>Handover</code>.</li><li>Select or enter the WT unit details.</li><li>Fill in the handover recipient and required notes.</li><li>Submit the handover form.</li></ol>
    <h3>4. Return A Unit</h3>
    <ol><li>Open <code>Return Unit</code>.</li><li>Search for your assigned unit.</li><li>Confirm the return information.</li><li>Submit the return request.</li><li>Track the status until ICT confirms the return.</li></ol>
    <h3>5. Report Faulty</h3>
    <ol><li>Open <code>Report Faulty</code>.</li><li>Select the faulty WT unit.</li><li>Describe the issue clearly.</li><li>Add evidence if requested.</li><li>Submit the report.</li><li>Check <code>Pending</code>, <code>Drafts</code>, or <code>Completed</code> faulty-report buckets to track progress.</li></ol>
    <h3>6. Update Profile</h3>
    <ol><li>Open <code>My Profile</code>.</li><li>Update full name, phone number, department, or position.</li><li>Click <code>Save Changes</code>.</li><li>Keep profile information current because request forms may use it.</li></ol>

    <h2 id="common-tips">Common Tips</h2>
    <ol><li>Use search boxes in tables to find records quickly.</li><li>Use the <code>More</code> action button when a table row has several actions.</li><li>If a form cannot submit, check required fields and validation messages.</li><li>If a dropdown option is missing, ICT can add it from <code>Master Data</code>.</li><li>For approval issues, check <code>History</code>, <code>All Status</code>, or <code>System Logs</code>.</li><li>For account access issues, contact ICT or submit forgot-password where available.</li></ol>
    <div class="readme-callout"><p>Important: users should keep profile details updated because request forms and approval records may reuse name, department, position, and phone information.</p></div>

    <h2 id="suggested-training-flow">Suggested Training Flow</h2>
    <ol><li>Start with login and profile update.</li><li>Demonstrate one long-term request.</li><li>Demonstrate one temporary request.</li><li>Demonstrate approval and forwarding to ICT.</li><li>Demonstrate inventory assignment or status update.</li><li>Demonstrate return and faulty report handling.</li><li>End with status tracking and logs.</li></ol>
  </div>
</article>

@if(false)

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
        <li><span>3</span><div>Create, update, import, assign, return, or remove walkie talkie units from the inventory tools. Keep radio IDs clean; user-facing forms show the <strong>G</strong> prefix so users type only the number portion.</div></li>
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
        <li><span>4</span><div>Submit <strong>Return Unit</strong> when a unit is no longer needed. Use manual unit entry if the unit does not appear in system data.</div></li>
        <li><span>5</span><div>Submit <strong>Report Faulty</strong> when a unit has an issue, and type only the number portion in radio ID fields because the <strong>G</strong> prefix is shown on screen.</div></li>
        <li><span>6</span><div>Open <strong>All Status</strong> to track request, handover, return, and faulty-report progress.</div></li>
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
        <p class="manual-flow-text">User or Executive submits the return, uses manual unit entry if the unit is missing from system data, Executive confirms where required, and ICT inventory is updated after confirmation.</p>
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
      <p class="manual-note">Radio ID fields show the G prefix in the layout. Type only the number portion, for example 0001 for G0001.</p>
      <p class="manual-note">Use the bottom Help section of the sidebar to open this manual, and keep My Profile updated with photo, contact details, and saved signature.</p>
      <p class="manual-note">ICT users should keep master data and inventory records clean because those records control dropdowns, reports, assignment history, and approval accuracy across the WT System.</p>
    </div>
  </section>
</div>

@endif
@endsection
