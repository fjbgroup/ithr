@extends('it.layouts.app')

@section('title', 'Role Metric')
@section('page_title', 'Role Metric')

@push('styles')
<style>
.rm-wrap{max-width:1180px;margin:0 auto}
.rm-head{display:flex;align-items:flex-end;justify-content:space-between;gap:18px;margin-bottom:18px}
.rm-kicker{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--accent);margin-bottom:6px}
.rm-title{font-size:24px;font-weight:800;color:var(--text);line-height:1.2}
.rm-sub{font-size:13px;color:var(--muted);margin-top:6px;max-width:720px}
.rm-readonly{display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid var(--border);border-radius:8px;background:var(--surface);color:var(--muted);font-size:12px;font-weight:700;white-space:nowrap}
.rm-card{background:var(--surface);border:1px solid var(--border);border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 14px rgba(0,0,0,.07)}
.rm-table{width:100%;border-collapse:collapse;font-size:13px}
.rm-table th{padding:13px 14px;background:var(--table-head-bg,#e2e8f0);color:var(--table-head-color,#475569);font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.07em;border-bottom:1px solid var(--border);text-align:center;white-space:nowrap}
.rm-table th:first-child,.rm-table td:first-child{text-align:left}
.rm-table td{padding:14px;border-bottom:1px solid var(--border);vertical-align:middle;text-align:center;color:var(--text)}
.rm-table tbody tr:last-child td{border-bottom:none}
.rm-table tbody tr:hover td{background:var(--table-hover)}
.rm-feature{font-weight:750;color:var(--text);display:block}
.rm-desc{font-size:11px;color:var(--muted);display:block;margin-top:3px;line-height:1.45}
.rm-role{display:inline-flex;align-items:center;justify-content:center;padding:4px 8px;border-radius:999px;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.04em;line-height:1.2}
.rm-admin{background:rgba(14,165,233,.13);color:#0284c7}
.rm-finance{background:rgba(245,158,11,.14);color:#d97706}
.rm-hou{background:rgba(99,102,241,.13);color:#4f46e5}
.rm-gm{background:rgba(20,184,166,.13);color:#0f766e}
.rm-ceo{background:rgba(239,68,68,.13);color:#dc2626}
.rm-user{background:rgba(34,197,94,.13);color:#16a34a}
.rm-icon{display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;font-size:15px}
.rm-yes{color:#16a34a;background:rgba(34,197,94,.1)}
.rm-no{color:#dc2626;background:rgba(239,68,68,.1)}
.rm-partial{color:#d97706;background:rgba(245,158,11,.12);cursor:help}
.rm-note{display:flex;align-items:center;justify-content:center;gap:18px;flex-wrap:wrap;padding:13px 16px;background:var(--body-bg);border-top:1px solid var(--border);font-size:12px;color:var(--muted);font-weight:650}
.rm-note span{display:inline-flex;align-items:center;gap:7px}
html.dark .rm-admin{background:rgba(56,189,248,.15);color:#7dd3fc}
html.dark .rm-finance{background:rgba(245,158,11,.15);color:#fcd34d}
html.dark .rm-hou{background:rgba(129,140,248,.15);color:#c4b5fd}
html.dark .rm-gm{background:rgba(45,212,191,.15);color:#5eead4}
html.dark .rm-ceo{background:rgba(248,113,113,.15);color:#fca5a5}
html.dark .rm-user{background:rgba(74,222,128,.15);color:#86efac}
@media(max-width:900px){.rm-head{align-items:flex-start;flex-direction:column}.rm-card{overflow-x:auto}.rm-table{min-width:980px}}
</style>
@endpush

@section('content')
@php
  $yes = '<span class="rm-icon rm-yes" title="Full access"><i class="bi bi-check-lg"></i></span>';
  $no = '<span class="rm-icon rm-no" title="No access"><i class="bi bi-x-lg"></i></span>';
@endphp

<div class="rm-wrap">
  <div class="rm-head">
    <div>
      <div class="rm-kicker">IT System</div>
      <div class="rm-title">Role Permissions Matrix</div>
      <div class="rm-sub">A read-only breakdown of module access and approval capabilities for each IT System role.</div>
    </div>
    <div class="rm-readonly"><i class="bi bi-eye-fill"></i> Read only</div>
  </div>

  <div class="rm-card">
    <div class="table-responsive">
      <table class="rm-table">
        <thead>
          <tr>
            <th style="width:30%">Module / Feature</th>
            <th><span class="rm-role rm-admin">Admin IT</span></th>
            <th><span class="rm-role rm-finance">Finance Admin</span></th>
            <th><span class="rm-role rm-hou">HOU</span></th>
            <th><span class="rm-role rm-gm">GM</span></th>
            <th><span class="rm-role rm-ceo">CEO</span></th>
            <th><span class="rm-role rm-user">User</span></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><span class="rm-feature">Dashboard</span><span class="rm-desc">View IT System overview, quick actions, and account context.</span></td>
            <td>{!! $yes !!}</td><td>{!! $yes !!}</td><td>{!! $yes !!}</td><td>{!! $yes !!}</td><td>{!! $yes !!}</td><td>{!! $yes !!}</td>
          </tr>
          <tr>
            <td><span class="rm-feature">IT Assets</span><span class="rm-desc">View asset records. Admin and finance can maintain records; users submit change requests.</span></td>
            <td>{!! $yes !!}</td><td>{!! $yes !!}</td><td><span class="rm-icon rm-partial" title="Read-only access"><i class="bi bi-eye-fill"></i></span></td><td><span class="rm-icon rm-partial" title="Read-only access"><i class="bi bi-eye-fill"></i></span></td><td><span class="rm-icon rm-partial" title="Read-only access"><i class="bi bi-eye-fill"></i></span></td><td><span class="rm-icon rm-partial" title="Submit requests, not direct edits"><i class="bi bi-send"></i></span></td>
          </tr>
          <tr>
            <td><span class="rm-feature">Non-IT Assets</span><span class="rm-desc">View non-IT assets and submit or process asset requests.</span></td>
            <td>{!! $yes !!}</td><td>{!! $yes !!}</td><td><span class="rm-icon rm-partial" title="Read-only access"><i class="bi bi-eye-fill"></i></span></td><td><span class="rm-icon rm-partial" title="Read-only access"><i class="bi bi-eye-fill"></i></span></td><td><span class="rm-icon rm-partial" title="Read-only access"><i class="bi bi-eye-fill"></i></span></td><td><span class="rm-icon rm-partial" title="Submit requests, not direct edits"><i class="bi bi-send"></i></span></td>
          </tr>
          <tr>
            <td><span class="rm-feature">Pending / My Requests</span><span class="rm-desc">Track pending inventory requests and personal request history.</span></td>
            <td>{!! $yes !!}</td><td>{!! $yes !!}</td><td>{!! $no !!}</td><td>{!! $no !!}</td><td>{!! $no !!}</td><td>{!! $yes !!}</td>
          </tr>
          <tr>
            <td><span class="rm-feature">Write Off</span><span class="rm-desc">Submit and route write-off items through HOU, GM, and CEO approval.</span></td>
            <td>{!! $yes !!}</td><td>{!! $yes !!}</td><td><span class="rm-icon rm-partial" title="HOU signature step"><i class="bi bi-pen-fill"></i></span></td><td><span class="rm-icon rm-partial" title="GM recommendation step"><i class="bi bi-pen-fill"></i></span></td><td><span class="rm-icon rm-partial" title="CEO approval step"><i class="bi bi-check2-circle"></i></span></td><td>{!! $yes !!}</td>
          </tr>
          <tr>
            <td><span class="rm-feature">Write Off Inventory</span><span class="rm-desc">Finance inventory staging for routing approved write-off batches.</span></td>
            <td>{!! $no !!}</td><td>{!! $yes !!}</td><td>{!! $no !!}</td><td>{!! $no !!}</td><td>{!! $no !!}</td><td>{!! $no !!}</td>
          </tr>
          <tr>
            <td><span class="rm-feature">E-Waste / Disposal</span><span class="rm-desc">Register, review, collect, restore, and manage disposal records.</span></td>
            <td>{!! $yes !!}</td><td>{!! $yes !!}</td><td><span class="rm-icon rm-partial" title="View records only"><i class="bi bi-eye-fill"></i></span></td><td><span class="rm-icon rm-partial" title="View records only"><i class="bi bi-eye-fill"></i></span></td><td><span class="rm-icon rm-partial" title="View records only"><i class="bi bi-eye-fill"></i></span></td><td><span class="rm-icon rm-partial" title="Submit requests where available"><i class="bi bi-send"></i></span></td>
          </tr>
          <tr>
            <td><span class="rm-feature">IT Request Form</span><span class="rm-desc">Create IT requests and process HOU, validator, or admin approval steps.</span></td>
            <td>{!! $yes !!}</td><td>{!! $no !!}</td><td><span class="rm-icon rm-partial" title="Create requests and perform HOU review"><i class="bi bi-check2-square"></i></span></td><td>{!! $no !!}</td><td>{!! $no !!}</td><td>{!! $no !!}</td>
          </tr>
          <tr>
            <td><span class="rm-feature">IT Validator</span><span class="rm-desc">Special reviewer permission controlled by the validator flag, not the role alone.</span></td>
            <td>{!! $no !!}</td><td>{!! $no !!}</td><td><span class="rm-icon rm-partial" title="Only users marked as IT Validator"><i class="bi bi-shield-check"></i></span></td><td>{!! $no !!}</td><td>{!! $no !!}</td><td>{!! $no !!}</td>
          </tr>
          <tr>
            <td><span class="rm-feature">Reports</span><span class="rm-desc">Generate IT and Non-IT asset reports and exports.</span></td>
            <td>{!! $yes !!}</td><td>{!! $yes !!}</td><td>{!! $no !!}</td><td>{!! $no !!}</td><td>{!! $no !!}</td><td>{!! $no !!}</td>
          </tr>
          <tr>
            <td><span class="rm-feature">User, Activity, and Masterdata Admin</span><span class="rm-desc">Manage accounts, review activity logs, and maintain asset classes, brands, and locations.</span></td>
            <td>{!! $yes !!}</td><td>{!! $no !!}</td><td>{!! $no !!}</td><td>{!! $no !!}</td><td>{!! $no !!}</td><td>{!! $no !!}</td>
          </tr>
          <tr>
            <td><span class="rm-feature">Profile, Notifications, and Role Metric</span><span class="rm-desc">Maintain own profile, receive notifications, and read this matrix.</span></td>
            <td>{!! $yes !!}</td><td>{!! $yes !!}</td><td>{!! $yes !!}</td><td>{!! $yes !!}</td><td>{!! $yes !!}</td><td>{!! $yes !!}</td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="rm-note">
      <span><span class="rm-icon rm-yes"><i class="bi bi-check-lg"></i></span> Full access</span>
      <span><span class="rm-icon rm-partial"><i class="bi bi-dash-lg"></i></span> Conditional / limited access</span>
      <span><span class="rm-icon rm-no"><i class="bi bi-x-lg"></i></span> No access</span>
    </div>
  </div>
</div>
@endsection
