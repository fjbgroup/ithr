@extends(request()->routeIs('wt.admin.*') ? 'wt.layouts.admin' : 'wt.layouts.user')

@php
    $routePrefix = request()->routeIs('wt.admin.*') ? 'wt.admin' : 'wt.user';
    $isAdminRoute = request()->routeIs('wt.admin.*');
@endphp

@section('title', $pageTitle)
@section('page_title', $pageTitle)

@push('styles')
<style>
.fs-badge{display:inline-flex;padding:2px 10px;border-radius:20px;font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;border:1px solid}
.fs-record{padding:16px 20px;border-bottom:1px solid var(--border)}
.fs-record:last-child{border-bottom:none}
</style>
@endpush

@section('content')

<div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:20px">
  <div>
    <div style="font-size:16px;font-weight:800;color:var(--text)">{{ $pageTitle }}</div>
    <p style="margin-top:4px;font-size:12px;color:var(--muted)">{{ $pageDescription }}</p>
  </div>
  <a href="{{ route($routePrefix . '.damages.create', $isAdminRoute ? ['mode' => $mode] : []) }}" class="btn-secondary-custom">
    <i class="fa-solid fa-grid-2"></i> Back To Faulty Module
  </a>
</div>

{{-- Summary stat --}}
<div style="background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:20px 22px;box-shadow:var(--shadow);margin-bottom:20px;display:inline-block;min-width:180px">
  <div style="font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:.14em;color:var(--muted);margin-bottom:8px">{{ strtoupper($bucket) }}</div>
  <div style="font-size:30px;font-weight:800;color:var(--text);line-height:1;font-family:'Inter',sans-serif">
    {{ $bucket === 'pending' ? $summary['pending'] : ($bucket === 'drafts' ? $summary['drafts'] : $summary['completed']) }}
  </div>
</div>

{{-- Records list --}}
<div class="table-card">
  <div class="table-card-header">
    <i class="fas fa-list" style="color:var(--muted);font-size:15px"></i>
    <span class="table-card-title">{{ $pageTitle }}</span>
    @if($bucket === 'drafts')
    <a href="{{ route($routePrefix . '.damages.form', $isAdminRoute ? ['mode' => $mode] : []) }}" class="btn-primary-custom" style="margin-left:auto;padding:6px 14px;font-size:11px">
      <i class="fa-solid fa-plus"></i> New Request
    </a>
    @endif
  </div>

  @if($records->isEmpty())
  <div style="padding:40px;text-align:center;color:var(--muted);font-size:13px">No records found in this section.</div>
  @else
  @foreach($records as $record)
  @php
      $status = strtoupper((string) $record->status);
      $isManagerOwnedDraft = $status === 'DRAFT' && ($record->request_source ?? null) === 'manager_on_behalf_draft';
      $isDone = (bool) $record->done || $status === 'DONE';

      $badgeBg = match(true) {
          $status === 'DRAFT'              => 'rgba(100,116,139,.1)',
          $status === 'READY TO COLLECT'  => 'rgba(34,197,94,.1)',
          $isDone                          => 'rgba(34,197,94,.1)',
          default                          => 'rgba(245,158,11,.1)',
      };
      $badgeColor = match(true) {
          $status === 'DRAFT'              => '#475569',
          $status === 'READY TO COLLECT'  => '#16a34a',
          $isDone                          => '#16a34a',
          default                          => '#d97706',
      };
      $badgeBorder = match(true) {
          $status === 'DRAFT'              => 'rgba(100,116,139,.3)',
          $status === 'READY TO COLLECT'  => 'rgba(34,197,94,.3)',
          $isDone                          => 'rgba(34,197,94,.3)',
          default                          => 'rgba(245,158,11,.3)',
      };
  @endphp
  <div class="fs-record">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap">
      <div style="min-width:0;flex:1">
        <div style="display:flex;flex-wrap:wrap;align-items:center;gap:6px;margin-bottom:8px">
          <span style="font-size:11px;font-weight:800;color:var(--accent)">#{{ str_pad($record->maintenance_id, 4, '0', STR_PAD_LEFT) }}</span>
          <span class="fs-badge" style="background:{{ $badgeBg }};color:{{ $badgeColor }};border-color:{{ $badgeBorder }}">{{ $record->status ?: 'Pending' }}</span>
          @if($isManagerOwnedDraft)
          <span class="fs-badge" style="background:rgba(14,165,233,.1);color:#0369a1;border-color:rgba(14,165,233,.3)">Saved by Executive</span>
          @endif
          @if($record->ict_received_at && !$record->done)
          <span class="fs-badge" style="background:rgba(14,165,233,.1);color:#0369a1;border-color:rgba(14,165,233,.3)">Repairing</span>
          @endif
          @if($record->temporary_spare_assigned_at && !$record->temporary_spare_returned_at)
          <span class="fs-badge" style="background:rgba(99,102,241,.1);color:#4338ca;border-color:rgba(99,102,241,.3)">Spare / New Given</span>
          @elseif($record->temporary_spare_requested)
          <span class="fs-badge" style="background:rgba(14,165,233,.1);color:#0369a1;border-color:rgba(14,165,233,.3)">Temporary WT Requested</span>
          @endif
          @if($record->original_returned_at)
          <span class="fs-badge" style="background:rgba(34,197,94,.1);color:#16a34a;border-color:rgba(34,197,94,.3)">Original WT Returned</span>
          @endif
        </div>
        <div style="font-size:13px;font-weight:700;color:var(--text)">{{ $record->model ?: 'NO MODEL' }}{{ $record->radio_id ? ' - '.$record->radio_id : '' }}</div>
        <div style="margin-top:3px;font-size:11px;color:var(--muted)">{{ $record->problem_possible ?: ($record->issue_description ?: 'No problem details saved yet.') }}</div>
        <div style="margin-top:3px;font-size:10px;color:var(--muted)">
          Reporter: {{ $record->reporter_name ?: '-' }} &bull; Submitted: {{ $record->received_date ? \Carbon\Carbon::parse($record->received_date)->format('d M Y') : '-' }}
          @if($record->ict_received_at) &bull; ICT Received: {{ \Carbon\Carbon::parse($record->ict_received_at)->format('d M Y') }} @endif
          @if($record->temporary_spare_assigned_at) &bull; Spare/New Given: {{ \Carbon\Carbon::parse($record->temporary_spare_assigned_at)->format('d M Y') }} @elseif($record->temporary_spare_requested) &bull; Temporary WT Requested @endif
          @if($record->original_returned_at) &bull; Original Returned: {{ \Carbon\Carbon::parse($record->original_returned_at)->format('d M Y') }} @endif
        </div>
        @if($record->handover_at || $record->pickup_at)
        <div style="margin-top:3px;font-size:10px;color:#16a34a">
          Handover: {{ $record->handover_at ? \Carbon\Carbon::parse($record->handover_at)->format('d M Y, h:i A') : '-' }} by {{ $record->handover_person ?: '-' }}
          &bull; Pickup: {{ $record->pickup_at ? \Carbon\Carbon::parse($record->pickup_at)->format('d M Y, h:i A') : '-' }} by {{ $record->pickup_person ?: '-' }}
        </div>
        @endif
        @if(!empty($record->remarks))
        <div style="margin-top:4px;font-size:10px;font-weight:600;color:var(--accent)">{{ $record->remarks }}</div>
        @endif
        @if(!empty($record->temporary_spare_request_note))
        <div style="margin-top:4px;font-size:10px;font-weight:600;color:#0369a1">Temporary WT: {{ $record->temporary_spare_request_note }}</div>
        @endif
      </div>
      <div style="display:flex;align-items:center;gap:8px;flex-shrink:0">
        @if($bucket === 'drafts' && !($isManagerOwnedDraft && !$isAdminRoute))
        <a href="{{ route($routePrefix . '.damages.form', array_merge($isAdminRoute ? ['mode' => $mode] : [], ['draft' => $record->maintenance_id])) }}" class="btn-secondary-custom" style="padding:6px 12px;font-size:11px">
          <i class="fa-solid fa-pen"></i> Continue
        </a>
        @elseif($bucket === 'drafts' && $isManagerOwnedDraft && !$isAdminRoute)
        <a href="{{ route($routePrefix . '.damages.show', ['damage' => $record->maintenance_id]) }}" class="btn-secondary-custom" style="padding:6px 12px;font-size:11px;color:#0369a1;border-color:rgba(14,165,233,.3)">
          <i class="fa-solid fa-eye"></i> View Only
        </a>
        @endif
      </div>
    </div>
  </div>
  @endforeach
  @endif
</div>

@endsection
