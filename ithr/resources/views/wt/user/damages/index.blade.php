@extends(request()->routeIs('wt.admin.*') ? 'wt.layouts.admin' : 'wt.layouts.user')

@php
    $routePrefix = request()->routeIs('wt.admin.*') ? 'wt.admin' : 'wt.user';
    $isAdminRoute = request()->routeIs('wt.admin.*');
@endphp

@section('title', 'Report Faulty')
@section('page_title', 'Report Faulty')

@push('styles')
<style>
.fh-action-card{display:block;text-decoration:none;background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:22px;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 14px rgba(0,0,0,.07);transition:box-shadow .2s,transform .2s}
.fh-action-card:hover{box-shadow:0 6px 24px rgba(0,0,0,.12);transform:translateY(-2px)}
.fh-action-icon{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;margin-bottom:14px}
.fh-action-count{font-size:28px;font-weight:800;color:var(--text);line-height:1;font-family:'DM Sans',sans-serif;margin-bottom:4px}
.fh-action-label{font-size:14px;font-weight:700;color:var(--text);margin-bottom:4px}
.fh-action-desc{font-size:12px;color:var(--muted);line-height:1.5}
.fh-record{padding:14px 0;border-bottom:1px solid var(--border)}
.fh-record:last-child{border-bottom:none}
.fh-badge{display:inline-flex;padding:2px 10px;border-radius:20px;font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;border:1px solid}
</style>
@endpush

@section('content')
@php
    $recentDamageRequests = $recentDamageRequests ?? collect();
    $recentCompletedDamageRequests = $recentCompletedDamageRequests ?? collect();
    $recentDamageId = session('recent_damage_id');
@endphp

<div style="margin-bottom:18px">
  <div style="font-size:16px;font-weight:800;color:var(--text)">Report Faulty</div>
  <p style="margin-top:4px;font-size:12px;color:var(--muted)">Open the faulty reporting module and go straight to the action you need.</p>
</div>

@if(session('success'))
<div class="alert-success-custom mb-4"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
@endif

<div class="row g-3 mb-4">
  <div class="col-md-4">
    <a href="{{ route($routePrefix . '.damages.form', $isAdminRoute ? ['mode' => $mode] : []) }}" class="fh-action-card">
      <div class="fh-action-icon" style="background:rgba(2,132,199,.12);color:#0284c7"><i class="fa-solid fa-plus"></i></div>
      <div class="fh-action-label">New Request</div>
      <div class="fh-action-desc">Create a new faulty report form and submit it for review.</div>
      <div style="margin-top:12px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--accent)">Open Form &rarr;</div>
    </a>
  </div>
  <div class="col-md-4">
    <a href="{{ route($routePrefix . '.damages.status', array_merge(['bucket' => 'pending'], $isAdminRoute ? ['mode' => $mode] : [])) }}" class="fh-action-card">
      <div class="fh-action-icon" style="background:rgba(245,158,11,.12);color:#d97706"><i class="fa-solid fa-hourglass-half"></i></div>
      <div class="fh-action-count">{{ $summary['pending'] }}</div>
      <div class="fh-action-label">Pending Status</div>
      <div class="fh-action-desc">See reports waiting for executive review, ICT review, or repair progress.</div>
    </a>
  </div>
  <div class="col-md-4">
    <a href="{{ route($routePrefix . '.damages.status', array_merge(['bucket' => 'completed'], $isAdminRoute ? ['mode' => $mode] : [])) }}" class="fh-action-card">
      <div class="fh-action-icon" style="background:rgba(34,197,94,.12);color:#16a34a"><i class="fa-solid fa-circle-check"></i></div>
      <div class="fh-action-count">{{ $summary['completed'] }}</div>
      <div class="fh-action-label">Completed</div>
      <div class="fh-action-desc">View faulty reports that have already been completed or resolved.</div>
    </a>
  </div>
</div>

@if($recentDamageRequests->isNotEmpty())
<div class="table-card mb-4">
  <div class="table-card-header">
    <i class="fas fa-clock" style="color:var(--muted);font-size:15px"></i>
    <span class="table-card-title">Latest Submitted Requests</span>
    <a href="{{ route($routePrefix . '.damages.status', array_merge(['bucket' => 'pending'], $isAdminRoute ? ['mode' => $mode] : [])) }}" style="margin-left:auto;font-size:11px;font-weight:700;color:var(--accent);text-decoration:none;text-transform:uppercase;letter-spacing:.06em">View All</a>
  </div>
  <div style="padding:4px 20px 8px">
    @foreach($recentDamageRequests as $record)
    @php
        $status = strtoupper((string) $record->status);
        $isDone = (bool) $record->done || in_array($status, ['DONE','COMPLETED','REPAIRED','RESOLVED'], true);
        $hasReplacementRequest = str_contains((string)($record->remarks ?? ''), 'REPLACEMENT REQUESTED');
        $isHighlighted = (int) $recentDamageId === (int) $record->maintenance_id;
    @endphp
    <div class="fh-record" style="{{ $isHighlighted ? 'background:rgba(34,197,94,.04);margin:0 -20px;padding:14px 20px;border-radius:0;' : '' }}">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap">
        <div style="min-width:0">
          <div style="display:flex;flex-wrap:wrap;align-items:center;gap:6px;margin-bottom:6px">
            <span style="font-size:11px;font-weight:800;color:var(--accent)">#{{ str_pad($record->maintenance_id, 4, '0', STR_PAD_LEFT) }}</span>
            @if($isDone)
            <span class="fh-badge" style="background:rgba(34,197,94,.1);color:#16a34a;border-color:rgba(34,197,94,.3)">Already Fixed / Ready To Collect</span>
            @else
            <span class="fh-badge" style="background:rgba(245,158,11,.1);color:#d97706;border-color:rgba(245,158,11,.3)">Processing</span>
            @endif
            @if($hasReplacementRequest)
            <span class="fh-badge" style="background:rgba(34,197,94,.1);color:#16a34a;border-color:rgba(34,197,94,.3)">Replacement Requested</span>
            @endif
            @if($isHighlighted)
            <span class="fh-badge" style="background:rgba(34,197,94,.1);color:#16a34a;border-color:rgba(34,197,94,.3)">Just Submitted</span>
            @endif
          </div>
          <div style="font-size:13px;font-weight:700;color:var(--text)">{{ $record->model ?: 'NO MODEL' }}{{ $record->radio_id ? ' - '.$record->radio_id : ($record->serial_number ? ' - '.$record->serial_number : '') }}</div>
          <div style="margin-top:3px;font-size:11px;color:var(--muted)">{{ $record->problem_possible ?: ($record->issue_description ?: 'No problem details saved yet.') }}</div>
          <div style="margin-top:3px;font-size:10px;color:var(--muted)">Submitted: {{ $record->received_date ? \Carbon\Carbon::parse($record->received_date)->format('d M Y') : '-' }}</div>
        </div>
      </div>
    </div>
    @endforeach
  </div>
</div>
@endif

@if($recentCompletedDamageRequests->isNotEmpty())
<div class="table-card">
  <div class="table-card-header">
    <i class="fas fa-check-circle" style="color:var(--muted);font-size:15px"></i>
    <span class="table-card-title">Latest Completed Requests</span>
    <a href="{{ route($routePrefix . '.damages.status', array_merge(['bucket' => 'completed'], $isAdminRoute ? ['mode' => $mode] : [])) }}" style="margin-left:auto;font-size:11px;font-weight:700;color:var(--accent);text-decoration:none;text-transform:uppercase;letter-spacing:.06em">View All</a>
  </div>
  <div style="padding:4px 20px 8px">
    @foreach($recentCompletedDamageRequests as $record)
    @php
        $status = strtoupper((string) $record->status);
        $hasReplacementRequest = str_contains((string)($record->remarks ?? ''), 'REPLACEMENT REQUESTED');
    @endphp
    <div class="fh-record">
      <div style="display:flex;flex-wrap:wrap;align-items:center;gap:6px;margin-bottom:6px">
        <span style="font-size:11px;font-weight:800;color:var(--accent)">#{{ str_pad($record->maintenance_id, 4, '0', STR_PAD_LEFT) }}</span>
        @if(in_array($status, ['REJECTED','REFUSED'], true))
        <span class="fh-badge" style="background:rgba(239,68,68,.1);color:#dc2626;border-color:rgba(239,68,68,.3)">Rejected</span>
        @else
        <span class="fh-badge" style="background:rgba(34,197,94,.1);color:#16a34a;border-color:rgba(34,197,94,.3)">Already Fixed / Ready To Collect</span>
        @endif
        @if($hasReplacementRequest)
        <span class="fh-badge" style="background:rgba(34,197,94,.1);color:#16a34a;border-color:rgba(34,197,94,.3)">Replacement Requested</span>
        @endif
      </div>
      <div style="font-size:13px;font-weight:700;color:var(--text)">{{ $record->model ?: 'NO MODEL' }}{{ $record->radio_id ? ' - '.$record->radio_id : ($record->serial_number ? ' - '.$record->serial_number : '') }}</div>
      <div style="margin-top:3px;font-size:11px;color:var(--muted)">{{ $record->problem_possible ?: ($record->issue_description ?: 'No problem details saved yet.') }}</div>
      <div style="margin-top:3px;font-size:10px;color:var(--muted)">Completed: {{ $record->finish_date ? \Carbon\Carbon::parse($record->finish_date)->format('d M Y') : ($record->updated_at ? \Carbon\Carbon::parse($record->updated_at)->format('d M Y') : '-') }}</div>
    </div>
    @endforeach
  </div>
</div>
@endif

@endsection
