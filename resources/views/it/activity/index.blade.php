@extends('it.layouts.app')

@section('title', 'Activity Log')
@section('page_title', 'Activity Log')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400&display=swap" rel="stylesheet">

<style>
/* ── Scope ── */
.aly-wrap {
    --aly-surface:   #ffffff;
    --aly-bg:        #f1f5f9;
    --aly-border:    #e2e8f0;
    --aly-border-lt: #f1f5f9;
    --aly-text:      #1e293b;
    --aly-muted:     #64748b;
    --aly-faint:     #cbd5e1;
    --aly-input-bg:  #f8fafc;
    --aly-hover:     #f8fafc;
    --aly-thead:     #f8fafc;
    font-family: 'Inter', sans-serif;
    margin-left: -16px;
    margin-right: 16px;
}
.aly-wrap.aly-dark {
    --aly-surface:   #1a1e2a;
    --aly-bg:        #141720;
    --aly-border:    #2a2f3d;
    --aly-border-lt: #1e2230;
    --aly-text:      #e2e8f0;
    --aly-muted:     #94a3b8;
    --aly-faint:     #3f4558;
    --aly-input-bg:  #141720;
    --aly-hover:     #1e2230;
    --aly-thead:     #141720;
}

/* ── Stats ── */
.aly-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 22px;
}
@media (max-width: 900px) { .aly-stats { grid-template-columns: repeat(2,1fr); } }
@media (max-width: 500px) { .aly-stats { grid-template-columns: 1fr; } }

.aly-stat {
    background: var(--aly-surface);
    border: 1px solid var(--aly-border);
    border-left: 4px solid transparent;
    border-radius: 10px;
    padding: 16px 18px;
    display: flex;
    align-items: center;
    gap: 14px;
}
.aly-stat.s-blue   { border-left-color: #3b82f6; }
.aly-stat.s-green  { border-left-color: #16a34a; }
.aly-stat.s-amber  { border-left-color: #d97706; }
.aly-stat.s-sky    { border-left-color: #0284c7; }

.aly-stat-icon {
    width: 40px; height: 40px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; flex-shrink: 0;
}
.s-blue  .aly-stat-icon { background: rgba(59,130,246,.1);  color: #3b82f6; }
.s-green .aly-stat-icon { background: rgba(22,163,74,.1);   color: #16a34a; }
.s-amber .aly-stat-icon { background: rgba(217,119,6,.1);   color: #d97706; }
.s-sky   .aly-stat-icon { background: rgba(2,132,199,.1);   color: #0284c7; }

.aly-stat-body {}
.aly-stat-val   { font-size: 24px; font-weight: 700; line-height: 1; color: var(--aly-text); }
.aly-stat-label { font-size: 11px; font-weight: 600; color: var(--aly-muted); text-transform: uppercase; letter-spacing: .06em; margin-top: 4px; }
.aly-stat-sub   { font-size: 11px; color: var(--aly-faint); margin-top: 2px; }

/* ── Card ── */
.aly-card {
    background: var(--aly-surface);
    border: 1px solid var(--aly-border);
    border-radius: 12px;
    overflow-x: auto;
    overflow-y: hidden;
}

@media (max-width: 768px) {
    .aly-wrap {
        margin-left: 0;
        margin-right: 0;
    }
}

/* Card header */
.aly-card-hdr {
    padding: 14px 20px;
    border-bottom: 1px solid var(--aly-border);
    display: flex; align-items: center; justify-content: space-between;
    background: var(--aly-surface);
}
.aly-card-title {
    display: flex; align-items: center; gap: 8px;
    font-size: 14px; font-weight: 600; color: var(--aly-text);
}
.aly-card-title i { color: #0284c7; font-size: 16px; }
.aly-entry-badge {
    font-size: 11px; font-weight: 600; color: var(--aly-muted);
    background: var(--aly-input-bg);
    border: 1px solid var(--aly-border);
    border-radius: 20px;
    padding: 3px 10px;
}

/* Filter bar */
.aly-filters {
    padding: 12px 20px;
    border-bottom: 1px solid var(--aly-border);
    display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
    background: var(--aly-surface);
}
.aly-search-wrap { flex: 1; min-width: 180px; position: relative; }
.aly-search-wrap i {
    position: absolute; left: 10px; top: 50%;
    transform: translateY(-50%);
    color: var(--aly-muted); font-size: 13px; pointer-events: none;
}
.aly-search {
    width: 100%; box-sizing: border-box;
    background: var(--aly-input-bg);
    border: 1px solid var(--aly-border);
    border-radius: 8px;
    padding: 7px 12px 7px 32px;
    font-size: 13px; font-family: 'Inter', sans-serif;
    color: var(--aly-text); outline: none;
    transition: border-color .15s;
}
.aly-search:focus        { border-color: #0284c7; }
.aly-search::placeholder { color: var(--aly-faint); }

.aly-pill {
    background: var(--aly-input-bg);
    border: 1px solid var(--aly-border);
    border-radius: 20px;
    padding: 5px 12px;
    font-size: 12px; font-weight: 600; font-family: 'Inter', sans-serif;
    color: var(--aly-muted);
    cursor: pointer; display: inline-flex; align-items: center; gap: 5px;
    transition: all .15s; white-space: nowrap;
}
.aly-pill:hover  { border-color: #0284c7; color: #0284c7; }
.aly-pill.active { border-color: #0284c7; color: #0284c7; background: rgba(2,132,199,.08); }

.aly-dot { width: 6px; height: 6px; border-radius: 50%; display: inline-block; flex-shrink: 0; }
.d-green  { background: #16a34a; }
.d-amber  { background: #d97706; }
.d-blue   { background: #3b82f6; }
.d-red    { background: #dc2626; }

/* ── Table ── */
#activityTable_wrapper .dataTables_filter,
#activityTable_wrapper .dataTables_length { display: none !important; }

table#activityTable {
    width: 100% !important;
    border-collapse: collapse;
    font-family: 'Inter', sans-serif;
}
table#activityTable thead th {
    background: var(--aly-thead) !important;
    color: var(--aly-muted) !important;
    font-size: 11px !important; font-weight: 600 !important;
    text-transform: uppercase !important; letter-spacing: .06em !important;
    padding: 10px 12px !important;
    border-bottom: 1px solid var(--aly-border) !important;
    border-top: none !important; white-space: nowrap;
}
table#activityTable thead th:first-child { padding-left: 20px !important; }
table#activityTable thead th:last-child  { padding-right: 20px !important; }
table#activityTable thead th:first-child,
table#activityTable tbody td:first-child {
    width: 46px !important;
    min-width: 46px !important;
    max-width: 46px !important;
    padding-right: 4px !important;
}
table#activityTable thead th:nth-child(2),
table#activityTable tbody td:nth-child(2) {
    padding-left: 4px !important;
}
table#activityTable tbody tr { border-top: 1px solid var(--aly-border-lt); transition: background .1s; }
table#activityTable tbody tr:hover td { background: var(--aly-hover) !important; }
table#activityTable tbody td {
    padding: 11px 12px !important;
    vertical-align: middle !important;
    border: none !important;
    background: var(--aly-surface) !important;
    color: var(--aly-text) !important;
}
table#activityTable tbody td:first-child { padding-left: 20px !important; }
table#activityTable tbody td:last-child  { padding-right: 20px !important; }

/* ── Cell elements ── */
.aly-num    { font-family: 'JetBrains Mono', monospace; font-size: 11px; color: var(--aly-faint); }
.aly-user   { display: flex; align-items: center; gap: 9px; }
.aly-avatar {
    width: 30px; height: 30px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 10px; font-weight: 700; flex-shrink: 0;
}
.av-0 { background: rgba(59,130,246,.12);  color: #2563eb; }
.av-1 { background: rgba(22,163,74,.12);   color: #16a34a; }
.av-2 { background: rgba(217,119,6,.12);   color: #d97706; }
.av-3 { background: rgba(139,92,246,.12);  color: #7c3aed; }
.av-4 { background: rgba(220,38,38,.12);   color: #dc2626; }
.av-5 { background: rgba(6,182,212,.12);   color: #0891b2; }

.aly-uname  { font-size: 13px; font-weight: 500; color: var(--aly-text); line-height: 1.2; }
.aly-uun    { font-size: 11px; color: var(--aly-muted); }

.aly-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 9px; border-radius: 5px;
    font-size: 11px; font-weight: 600; letter-spacing: .04em; white-space: nowrap;
}
.ab-login   { background: rgba(22,163,74,.1);  color: #16a34a; border: 1px solid rgba(22,163,74,.25); }
.ab-logout  { background: rgba(217,119,6,.1);  color: #d97706; border: 1px solid rgba(217,119,6,.25); }
.ab-create  { background: rgba(59,130,246,.1); color: #2563eb; border: 1px solid rgba(59,130,246,.25); }
.ab-update  { background: rgba(139,92,246,.1); color: #7c3aed; border: 1px solid rgba(139,92,246,.25); }
.ab-delete  { background: rgba(220,38,38,.1);  color: #dc2626; border: 1px solid rgba(220,38,38,.25); }
.ab-default { background: rgba(100,116,139,.1);color: #64748b; border: 1px solid rgba(100,116,139,.25);}

.aly-type {
    font-size: 11px; font-weight: 500; color: var(--aly-muted);
    background: var(--aly-input-bg); border: 1px solid var(--aly-border);
    border-radius: 4px; padding: 2px 7px; text-transform: uppercase;
}
.aly-desc { font-size: 12px; color: var(--aly-muted); }
.aly-ip   { font-family: 'JetBrains Mono', monospace; font-size: 11px; color: var(--aly-muted); }
.aly-ts   { font-family: 'JetBrains Mono', monospace; font-size: 11px; color: var(--aly-text); white-space: nowrap; }

/* ── Card footer (info + pagination) ── */
.aly-footer {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 8px;
    padding: 10px 20px;
    border-top: 1px solid var(--aly-border);
    background: var(--aly-surface);
}

/* DataTables info text */
.aly-footer .dataTables_info {
    font-size: 12px !important;
    color: var(--aly-muted) !important;
    font-family: 'Inter', sans-serif !important;
    padding: 0 !important;
    border: none !important;
    background: transparent !important;
}

/* ── PAGINATION FIX: scoped to .aly-footer, NOT .dataTables_wrapper ── */
.aly-footer .dataTables_paginate .paginate_button {
    background: transparent !important;
    border: none !important;
    padding: 0 !important;
    margin: 0 !important;
}
.aly-footer .pagination {
    gap: 2px;
    flex-wrap: nowrap;
    margin: 0;
}
.aly-footer .pagination .page-item .page-link {
    font-size: 11px !important;
    font-weight: 600 !important;
    font-family: 'Inter', sans-serif !important;
    padding: 4px 9px !important;
    line-height: 1.4 !important;
    min-width: 28px;
    text-align: center;
    border-radius: 6px !important;
    border: 1px solid var(--aly-border) !important;
    background: transparent !important;
    color: var(--aly-muted) !important;
    transition: all .15s;
}
.aly-footer .pagination .page-item.active .page-link {
    background: #0284c7 !important;
    border-color: #0284c7 !important;
    color: #fff !important;
}
.aly-footer .pagination .page-item .page-link:hover {
    background: rgba(2,132,199,.08) !important;
    border-color: #0284c7 !important;
    color: #0284c7 !important;
}
.aly-footer .pagination .page-item.disabled .page-link {
    opacity: .4;
    pointer-events: none;
}
</style>
@endpush

@section('content')

<div class="aly-wrap">

  {{-- Stats --}}
  <div class="aly-stats">
    <div class="aly-stat s-blue">
      <div class="aly-stat-icon"><i class="bi bi-list-ul"></i></div>
      <div class="aly-stat-body">
        <div class="aly-stat-val">{{ number_format($totalCount) }}</div>
        <div class="aly-stat-label">Total Entries</div>
        <div class="aly-stat-sub">All time</div>
      </div>
    </div>
    <div class="aly-stat s-green">
      <div class="aly-stat-icon"><i class="bi bi-box-arrow-in-right"></i></div>
      <div class="aly-stat-body">
        <div class="aly-stat-val">{{ $todayStats['LOGIN'] ?? 0 }}</div>
        <div class="aly-stat-label">Logins Today</div>
        <div class="aly-stat-sub">{{ now()->format('d M Y') }}</div>
      </div>
    </div>
    <div class="aly-stat s-amber">
      <div class="aly-stat-icon"><i class="bi bi-box-arrow-right"></i></div>
      <div class="aly-stat-body">
        <div class="aly-stat-val">{{ $todayStats['LOGOUT'] ?? 0 }}</div>
        <div class="aly-stat-label">Logouts Today</div>
        <div class="aly-stat-sub">{{ now()->format('d M Y') }}</div>
      </div>
    </div>
    <div class="aly-stat s-sky">
      <div class="aly-stat-icon"><i class="bi bi-people"></i></div>
      <div class="aly-stat-body">
        <div class="aly-stat-val">{{ $activeUsers }}</div>
        <div class="aly-stat-label">Active Users</div>
        <div class="aly-stat-sub">Logged in today</div>
      </div>
    </div>
  </div>

  {{-- Main card --}}
  <div class="aly-card">

    {{-- Header --}}
    <div class="aly-card-hdr">
      <div class="aly-card-title">
        <i class="bi bi-clock-history"></i>
        System Activity Log
      </div>
      <span class="aly-entry-badge">Last 200 entries</span>
    </div>

    {{-- Filter bar --}}
    <div class="aly-filters">
      <div class="aly-search-wrap">
        <i class="bi bi-search"></i>
        <input type="text" class="aly-search" id="alySearch" placeholder="Search user, action, or IP…">
      </div>
      <button class="aly-pill active" data-filter="">All</button>
      <button class="aly-pill" data-filter="LOGIN"><span class="aly-dot d-green"></span>Login</button>
      <button class="aly-pill" data-filter="LOGOUT"><span class="aly-dot d-amber"></span>Logout</button>
      <button class="aly-pill" data-filter="CREATE"><span class="aly-dot d-blue"></span>Create</button>
      <button class="aly-pill" data-filter="DELETE"><span class="aly-dot d-red"></span>Delete</button>
    </div>

    {{-- Table --}}
    <table id="activityTable" class="display">
      <thead>
        <tr>
          <th>#</th>
          <th>User</th>
          <th>Action</th>
          <th>Type</th>
          <th>Description</th>
          <th>IP Address</th>
          <th>Timestamp</th>
          <th>ActionRaw</th>
        </tr>
      </thead>
      <tbody>
        @php
          $avColors = ['av-0','av-1','av-2','av-3','av-4','av-5'];
          $colorMap = [];
          $colorIdx = 0;
          $i = 1;
          $badges = [
            'LOGIN'          => 'ab-login',
            'LOGOUT'         => 'ab-logout',
            'CREATE'         => 'ab-create',
            'UPDATE'         => 'ab-update',
            'DELETE'         => 'ab-delete',
            'FLAGGED_EWASTE' => 'ab-delete',
            'USER_TOGGLE'    => 'ab-update',
          ];
          $dots = [
            'LOGIN'  => 'd-green',
            'LOGOUT' => 'd-amber',
            'CREATE' => 'd-blue',
            'UPDATE' => 'd-blue',
            'DELETE' => 'd-red',
          ];
        @endphp

        @forelse($logs as $log)
          @php
            $uid      = $log->user_id ?? 0;
            $name     = strtoupper($log->user->full_name ?? 'Unknown');
            $uname    = $log->user->username ?? '';
            $action   = strtoupper($log->action ?? '');
            $itemType = $log->item_type ?? '';
            $desc     = $log->description ?? '';
            $ip       = $log->ip_address ?? '::1';
            $ts       = \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i:s');

            if (!isset($colorMap[$uid])) {
              $colorMap[$uid] = $avColors[$colorIdx % count($avColors)];
              $colorIdx++;
            }
            $avClass  = $colorMap[$uid];
            $words    = explode(' ', $name);
            $initials = implode('', array_map(fn($w) => $w[0] ?? '', array_slice($words, 0, 2)));
            $badgeCls = $badges[$action] ?? 'ab-default';
            $dotCls   = $dots[$action]   ?? 'd-blue';
          @endphp
          <tr>
            <td><span class="aly-num">{{ $i++ }}</span></td>
            <td>
              <div class="aly-user">
                <div class="aly-avatar {{ $avClass }}">{{ $initials }}</div>
                <div>
                  <div class="aly-uname">{{ $name }}</div>
                  <div class="aly-uun">{{ $uname }}</div>
                </div>
              </div>
            </td>
            <td>
              <span class="aly-badge {{ $badgeCls }}">
                <span class="aly-dot {{ $dotCls }}"></span>
                {{ $action }}
              </span>
            </td>
            <td>
              @if($itemType)
                <span class="aly-type">{{ $itemType }}</span>
              @else
                <span style="color:var(--aly-faint);font-size:12px;">—</span>
              @endif
            </td>
            <td><span class="aly-desc">{{ $desc }}</span></td>
            <td><span class="aly-ip">{{ $ip }}</span></td>
            <td><span class="aly-ts">{{ $ts }}</span></td>
            <td>{{ $action }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="8" style="text-align:center;padding:40px;color:var(--aly-muted)">
              <i class="bi bi-inbox" style="font-size:28px;display:block;margin-bottom:8px;opacity:.4"></i>
              No activity found.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>

    {{-- Footer: info + pagination appended here by JS --}}
    <div class="aly-footer"></div>

  </div>

</div>

@endsection

@push('scripts')
<script>
$(document).ready(function () {
  var dt = $('#activityTable').DataTable({
    pageLength: 15,
    lengthChange: false,
    dom: 'tip',
    order: [],
    language: {
      info: 'Showing _START_–_END_ of _TOTAL_ entries',
      paginate: { previous: '‹', next: '›' }
    },
    columnDefs: [
      { orderable: false, targets: [0, 4, 5] },
      { visible: false, targets: 7 }
    ]
  });

  // Move info + pagination into the card footer
  $('#activityTable_info').appendTo('.aly-footer');
  $('#activityTable_paginate').appendTo('.aly-footer');

  // Search
  $('#alySearch').on('keyup', function () {
    dt.search(this.value).draw();
  });

  // Filter pills
  $('.aly-pill').on('click', function () {
    $('.aly-pill').removeClass('active');
    $(this).addClass('active');
    var val = $(this).data('filter');
    dt.column(7).search(val ? '^' + val + '$' : '', true, false).draw();
  });

  // Dark mode sync
  function syncDark() {
    var wrap = document.querySelector('.aly-wrap');
    if (!wrap) return;
    var b = document.body, h = document.documentElement;
    var dark = b.classList.contains('dark-mode') || b.classList.contains('dark') ||
               b.getAttribute('data-theme') === 'dark' ||
               h.classList.contains('dark-mode') || h.classList.contains('dark') ||
               h.getAttribute('data-theme') === 'dark' || h.getAttribute('data-bs-theme') === 'dark';
    wrap.classList.toggle('aly-dark', dark);
  }
  syncDark();
  var obs = new MutationObserver(syncDark);
  obs.observe(document.body, { attributes: true, attributeFilter: ['class','data-theme','data-bs-theme'] });
  obs.observe(document.documentElement, { attributes: true, attributeFilter: ['class','data-theme','data-bs-theme'] });
});
</script>
@endpush
