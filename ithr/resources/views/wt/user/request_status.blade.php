@extends('wt.layouts.user')

@section('title', 'Request Status')
@section('page_title', 'Request Status')

@push('styles')
<style>
.rs-filter-card{display:block;text-decoration:none;background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:16px 18px;transition:box-shadow .2s,transform .2s,border-color .15s;box-shadow:0 1px 3px rgba(0,0,0,.06)}
.rs-filter-card:hover{box-shadow:0 4px 16px rgba(0,0,0,.1);transform:translateY(-1px)}
.rs-filter-card.active{border-width:2px}
.rs-filter-label{font-size:9px;font-weight:900;text-transform:uppercase;letter-spacing:.14em;margin-bottom:8px}
.rs-filter-count{font-size:26px;font-weight:800;color:var(--text);line-height:1;font-family:'Inter',sans-serif}
.rs-req-card{background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:16px;margin-bottom:12px}
.rs-badge{display:inline-flex;padding:2px 10px;border-radius:20px;font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;border:1px solid}
</style>
@endpush

@section('content')
@php
    $activeStatusFilter = $activeStatusFilter ?? null;
    $statusFilterLabels = [
        'draft'      => 'Draft',
        'processing' => 'Processing',
        'ready'      => 'Ready To Collect',
        'approved'   => 'Approved',
        'history'    => 'Return History',
        'rejected'   => 'Rejected',
    ];

    $statusBadge = function ($request) {
        if (($request->source_type ?? 'request') === 'repair') {
            return ($request->status_group ?? 'processing') === 'ready'
                ? ['text' => 'Already Fixed / Ready To Collect', 'bg' => 'rgba(14,165,233,.1)', 'color' => '#0369a1', 'border' => 'rgba(14,165,233,.3)']
                : ['text' => 'Processing', 'bg' => 'rgba(245,158,11,.1)', 'color' => '#d97706', 'border' => 'rgba(245,158,11,.3)'];
        }
        return match ($request->status) {
            'Draft'                                       => ['text' => 'Draft',              'bg' => 'rgba(100,116,139,.1)',  'color' => '#475569', 'border' => 'rgba(100,116,139,.3)'],
            'Pending Admin Approval', 'Pending IT Approval' => ['text' => !empty($request->return_status) ? 'Return Processing' : 'Processing', 'bg' => 'rgba(245,158,11,.1)', 'color' => '#d97706', 'border' => 'rgba(245,158,11,.3)'],
            'Pending Staff Pickup'                        => ['text' => 'Ready To Collect',   'bg' => 'rgba(14,165,233,.1)',   'color' => '#0369a1', 'border' => 'rgba(14,165,233,.3)'],
            'Approved'                                    => ['text' => 'Approved',            'bg' => 'rgba(34,197,94,.1)',    'color' => '#16a34a', 'border' => 'rgba(34,197,94,.3)'],
            'Returned'                                    => ['text' => 'Returned / History',  'bg' => 'rgba(99,102,241,.1)',   'color' => '#4338ca', 'border' => 'rgba(99,102,241,.3)'],
            'Rejected'                                    => ['text' => 'Rejected',            'bg' => 'rgba(239,68,68,.1)',    'color' => '#dc2626', 'border' => 'rgba(239,68,68,.3)'],
            default                                       => ['text' => $request->status ?: 'Unknown', 'bg' => 'rgba(100,116,139,.1)', 'color' => '#475569', 'border' => 'rgba(100,116,139,.3)'],
        };
    };

    $filterCards = [
        'draft'      => ['label' => 'Draft',            'count' => $statusSummary['draft'],          'color' => '#64748b', 'border_active' => '#94a3b8'],
        'processing' => ['label' => 'Processing',       'count' => $statusSummary['processing'],     'color' => '#d97706', 'border_active' => '#f59e0b'],
        'ready'      => ['label' => 'Ready To Collect', 'count' => $statusSummary['ready'],          'color' => '#0369a1', 'border_active' => '#0284c7'],
        'approved'   => ['label' => 'Approved',         'count' => $statusSummary['approved'],       'color' => '#16a34a', 'border_active' => '#22c55e'],
        'history'    => ['label' => 'History',          'count' => $statusSummary['history'] ?? 0,  'color' => '#4338ca', 'border_active' => '#6366f1'],
        'rejected'   => ['label' => 'Rejected',         'count' => $statusSummary['rejected'],       'color' => '#dc2626', 'border_active' => '#ef4444'],
    ];
@endphp

<div style="margin-bottom:18px">
  <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.08em;color:var(--muted)">My Requests &rsaquo; Status</div>
  <p style="margin-top:4px;font-size:12px;color:var(--muted)">Check the latest status and return history of your requests. History is kept for {{ $historyRetentionYears ?? 5 }} years.</p>
</div>

{{-- Status Filter Cards --}}
<div class="row g-3 mb-4">
  @foreach($filterCards as $key => $fc)
  <div class="col-6 col-md-4 col-xl-2">
    <a href="{{ route('wt.user.requests.status', ['status' => $key]) }}"
       class="rs-filter-card {{ $activeStatusFilter === $key ? 'active' : '' }}"
       style="{{ $activeStatusFilter === $key ? 'border-color:'.$fc['border_active'].';' : '' }}">
      <div class="rs-filter-label" style="color:{{ $fc['color'] }}">{{ $fc['label'] }}</div>
      <div class="rs-filter-count">{{ $fc['count'] }}</div>
    </a>
  </div>
  @endforeach
</div>

{{-- Requests Table Card --}}
<div class="table-card">
  <div class="table-card-header">
    <i class="fas fa-list-check" style="color:var(--muted);font-size:15px"></i>
    <span class="table-card-title">{{ $activeStatusFilter ? ($statusFilterLabels[$activeStatusFilter] ?? 'My Request Status') : 'My Request Status' }}</span>
    @if($activeStatusFilter)
    <a href="{{ route('wt.user.requests.status') }}" style="margin-left:auto;font-size:11px;font-weight:700;color:var(--muted);text-decoration:none;text-transform:uppercase;letter-spacing:.06em">View All</a>
    @endif
  </div>

  <div style="padding:16px 20px">
    @forelse($requestStatuses as $request)
    @php
        $badge = $statusBadge($request);
        $isTemporaryRequest = $request->is_temporary ?? false;
        $isRepairRequest = ($request->source_type ?? 'request') === 'repair';
    @endphp
    <div class="rs-req-card">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap">
        <div style="min-width:0">
          <div style="font-size:9px;font-weight:900;text-transform:uppercase;letter-spacing:.14em;color:var(--muted)">
            {{ $isRepairRequest ? 'Repair Request' : 'Request' }} #{{ str_pad($request->id, 5, '0', STR_PAD_LEFT) }}
          </div>
          <div style="margin-top:4px;font-size:13px;font-weight:700;color:var(--text)">{{ $request->title ?: 'Walkie Talkie Request' }}</div>
          @if($isRepairRequest)
          <div style="margin-top:3px;font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#d97706">Faulty Walkie Talkie</div>
          @endif
          @if($isTemporaryRequest)
          <div style="margin-top:3px;font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#7c3aed">Temporary Request &times;{{ $request->quantity ?: 1 }}</div>
          @endif
        </div>
        <span class="rs-badge" style="background:{{ $badge['bg'] }};color:{{ $badge['color'] }};border-color:{{ $badge['border'] }};white-space:nowrap">
          {{ $badge['text'] }}
        </span>
      </div>

      @if(!$isRepairRequest)
      <div style="margin-top:12px;margin-bottom:8px">
        @include('wt.partials.approval-flow', ['request' => $request, 'compact' => true])
      </div>
      @endif

      <div style="margin-top:10px;display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:4px 16px">
        <div style="font-size:11px;color:var(--muted)">Request Date: <span style="color:var(--text);font-weight:600">{{ $request->request_date ? \Carbon\Carbon::parse($request->request_date)->format('d M Y') : '-' }}</span></div>
        @if($isTemporaryRequest)
        <div style="font-size:11px;color:var(--muted)">Period: <span style="color:var(--text);font-weight:600">{{ $request->request_date ? \Carbon\Carbon::parse($request->request_date)->format('d M Y') : '-' }} &ndash; {{ $request->end_date ? \Carbon\Carbon::parse($request->end_date)->format('d M Y') : '-' }}</span></div>
        @php $userStatusDays = max(1,(int)($request->duration_days ?: 1)); @endphp
        <div style="font-size:11px;color:var(--muted)">Duration: <span style="color:var(--text);font-weight:600">{{ $userStatusDays }} {{ \Illuminate\Support\Str::plural('day',$userStatusDays) }}</span></div>
        @endif
        <div style="font-size:11px;color:var(--muted)">Department: <span style="color:var(--text);font-weight:600">{{ $request->department ?: '-' }}</span></div>
        <div style="font-size:11px;color:var(--muted)">Position: <span style="color:var(--text);font-weight:600">{{ $request->position ?: '-' }}</span></div>
        <div style="font-size:11px;color:var(--muted)">{{ $isRepairRequest ? 'Unit Info' : 'Radio ID' }}: <span style="color:var(--text);font-weight:600">{{ $request->radio_id ?: '-' }}</span></div>
        @if(!empty($request->approval_remark))
        <div style="font-size:11px;color:var(--muted)">Remark by Approval: <span style="color:var(--text);font-weight:600">{{ $request->approval_remark }}</span></div>
        @endif
        @if($request->note)
        <div style="font-size:11px;color:var(--muted)">Note: <span style="color:var(--text);font-weight:600">{{ $request->note }}</span></div>
        @endif
      </div>
    </div>
    @empty
    <div style="padding:32px;text-align:center;color:var(--muted);font-size:13px">No request status records found.</div>
    @endforelse
  </div>
</div>

@endsection
