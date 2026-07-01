@extends(request()->routeIs('wt.admin.*') ? 'wt.layouts.admin' : 'wt.layouts.user')

@php
    $routePrefix = request()->routeIs('wt.admin.*') ? 'wt.admin' : 'wt.user';
    $isAdminRoute = request()->routeIs('wt.admin.*');
    $recordStatus = strtoupper((string) $record->status);
    $recordIsDone = (bool) $record->done || $recordStatus === 'DONE';
    $replacementRequested = str_contains((string) ($record->remarks ?? ''), 'REPLACEMENT REQUESTED');
    $isManagerOwnedDraft = $recordStatus === 'DRAFT' && ($record->request_source ?? null) === 'manager_on_behalf_draft';
@endphp

@section('title', 'View Faulty Report')
@section('page_title', 'View Faulty Report')

@push('styles')
<style>
.fshow-badge{display:inline-flex;padding:3px 10px;border-radius:20px;font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;border:1px solid}
.fshow-info-cell{background:var(--body-bg);border:1px solid var(--border);border-radius:10px;padding:14px 16px;margin-bottom:0}
.fshow-info-label{font-size:9px;font-weight:900;text-transform:uppercase;letter-spacing:.14em;color:var(--muted);margin-bottom:6px}
.fshow-info-val{font-size:13px;font-weight:700;color:var(--text)}
.fshow-info-sub{font-size:11px;color:var(--muted);margin-top:3px}
.fshow-note{font-size:13px;font-weight:600;line-height:1.75;color:var(--text);white-space:pre-line;overflow-wrap:anywhere}
.fshow-note-panel{border-color:rgba(14,165,233,.28);background:rgba(14,165,233,.05)}
.fshow-note-panel .fshow-info-label{color:#0369a1}
.fshow-note-secondary{margin-top:12px;border-top:1px solid var(--border);padding-top:12px}
</style>
@endpush

@section('content')

<div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:20px">
  <div>
    <div style="font-size:16px;font-weight:800;color:var(--text)">View Faulty Report</div>
    <p style="margin-top:4px;font-size:12px;color:var(--muted)">Full read-only details for this faulty report.</p>
  </div>
  <a href="{{ route($routePrefix . '.damages.status', $isAdminRoute ? ['bucket' => 'drafts', 'mode' => $mode] : ['bucket' => 'drafts']) }}" class="btn-secondary-custom">
    <i class="fa-solid fa-arrow-left"></i> Back To Drafts
  </a>
</div>

<div class="table-card">
  <div class="table-card-header">
    <div style="min-width:0;flex:1">
      <div style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.1em;color:var(--muted)">Faulty Report Details</div>
      <div style="font-size:15px;font-weight:700;color:var(--text);margin-top:4px">Faulty Report #{{ str_pad($record->maintenance_id, 4, '0', STR_PAD_LEFT) }}</div>
      <div style="font-size:11px;color:var(--muted);margin-top:2px">{{ $isManagerOwnedDraft ? 'Saved by executive as an on-behalf draft.' : 'This record is displayed in read-only mode.' }}</div>
    </div>
    <div style="display:flex;flex-wrap:wrap;gap:6px;flex-shrink:0">
      @php
          $mainBadgeBg = match(true) {
              $recordIsDone   => 'rgba(34,197,94,.1)',
              $recordStatus === 'DRAFT' => 'rgba(100,116,139,.1)',
              default         => 'rgba(245,158,11,.1)',
          };
          $mainBadgeColor = match(true) {
              $recordIsDone   => '#16a34a',
              $recordStatus === 'DRAFT' => '#475569',
              default         => '#d97706',
          };
          $mainBadgeBorder = match(true) {
              $recordIsDone   => 'rgba(34,197,94,.3)',
              $recordStatus === 'DRAFT' => 'rgba(100,116,139,.3)',
              default         => 'rgba(245,158,11,.3)',
          };
      @endphp
      <span class="fshow-badge" style="background:{{ $mainBadgeBg }};color:{{ $mainBadgeColor }};border-color:{{ $mainBadgeBorder }}">{{ $record->status ?: 'Pending' }}</span>
      @if($isManagerOwnedDraft)
      <span class="fshow-badge" style="background:rgba(14,165,233,.1);color:#0369a1;border-color:rgba(14,165,233,.3)">Saved by Executive</span>
      @endif
      @if($replacementRequested)
      <span class="fshow-badge" style="background:rgba(34,197,94,.1);color:#16a34a;border-color:rgba(34,197,94,.3)"><i class="fa-solid fa-circle-check" style="margin-right:5px"></i>Replacement Requested</span>
      @endif
      @if($record->ict_received_at && !$recordIsDone)
      <span class="fshow-badge" style="background:rgba(14,165,233,.1);color:#0369a1;border-color:rgba(14,165,233,.3)">Repairing</span>
      @endif
      @if($record->temporary_spare_assigned_at && !$record->temporary_spare_returned_at)
      <span class="fshow-badge" style="background:rgba(99,102,241,.1);color:#4338ca;border-color:rgba(99,102,241,.3)">Spare / New Given</span>
      @elseif($record->temporary_spare_requested)
      <span class="fshow-badge" style="background:rgba(14,165,233,.1);color:#0369a1;border-color:rgba(14,165,233,.3)">Temporary WT Requested</span>
      @endif
      @if($record->original_returned_at)
      <span class="fshow-badge" style="background:rgba(34,197,94,.1);color:#16a34a;border-color:rgba(34,197,94,.3)">Original WT Returned</span>
      @endif
    </div>
  </div>

  <div style="padding:20px 22px">
    <div class="row g-3">
      <div class="col-md-6">
        <div class="fshow-info-cell">
          <div class="fshow-info-label">Reporter</div>
          <div class="fshow-info-val">{{ strtoupper($record->reporter_name ?: '-') }}</div>
          <div class="fshow-info-sub">{{ strtoupper($record->reporter_staff_id ?: '-') }}</div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="fshow-info-cell">
          <div class="fshow-info-label">Department</div>
          <div class="fshow-info-val">{{ strtoupper($record->department_name ?: '-') }}</div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="fshow-info-cell">
          <div class="fshow-info-label">Ownership / Deployment</div>
          <div class="fshow-info-val">{{ strtoupper($record->ownership_type ?: '-') }}</div>
          <div class="fshow-info-sub">Shared With: {{ strtoupper($record->shared_with ?: '-') }}</div>
          <div class="fshow-info-sub">Location: {{ strtoupper($record->location ?: '-') }}</div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="fshow-info-cell">
          <div class="fshow-info-label">Device Details</div>
          <div class="fshow-info-val">{{ strtoupper($record->model ?: '-') }}</div>
          <div class="fshow-info-sub">Radio ID: {{ strtoupper($record->radio_id ?: '-') }}</div>
          <div class="fshow-info-sub">Serial No: {{ strtoupper($record->serial_number ?: '-') }}</div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="fshow-info-cell">
          <div class="fshow-info-label">Submission Info</div>
          <div class="fshow-info-val">Submitted: {{ $record->received_date ? \Carbon\Carbon::parse($record->received_date)->format('d M Y') : '-' }}</div>
          <div class="fshow-info-sub">Phone No: {{ $record->phone_no ?: '-' }}</div>
          <div class="fshow-info-sub">Current Status: {{ $record->status ?: '-' }}</div>
          <div class="fshow-info-sub">ICT Received: {{ $record->ict_received_at ? \Carbon\Carbon::parse($record->ict_received_at)->format('d M Y') : '-' }}</div>
          <div class="fshow-info-sub">Temporary WT Needed: {{ is_null($record->temporary_spare_requested) ? 'NOT ANSWERED' : ($record->temporary_spare_requested ? 'YES' : 'NO') }}</div>
          <div class="fshow-info-sub">Spare/New Given: {{ $record->temporary_spare_assigned_at ? \Carbon\Carbon::parse($record->temporary_spare_assigned_at)->format('d M Y') : '-' }}</div>
          <div class="fshow-info-sub">Original Returned: {{ $record->original_returned_at ? \Carbon\Carbon::parse($record->original_returned_at)->format('d M Y') : '-' }}</div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="fshow-info-cell">
          <div class="fshow-info-label">Workflow</div>
          <div class="fshow-info-val">Source: {{ strtoupper(str_replace('_', ' ', $record->request_source ?: 'USER')) }}</div>
          <div class="fshow-info-sub">Done: {{ $recordIsDone ? 'YES' : 'NO' }}</div>
        </div>
      </div>
      <div class="col-12">
        <div class="fshow-info-cell" style="border-color:rgba(34,197,94,.3);background:rgba(34,197,94,.04)">
          <div class="fshow-info-label" style="color:#16a34a">Pickup &amp; Handover</div>
          <div class="fshow-info-val">Handover to ICT: {{ strtoupper($record->handover_person ?: '-') }} &bull; {{ $record->handover_at ? \Carbon\Carbon::parse($record->handover_at)->format('d M Y, h:i A') : '-' }}</div>
          <div class="fshow-info-sub" style="color:#16a34a">Pickup after ICT approval: {{ strtoupper($record->pickup_person ?: '-') }} &bull; {{ $record->pickup_at ? \Carbon\Carbon::parse($record->pickup_at)->format('d M Y, h:i A') : '-' }}</div>
          <div class="fshow-info-sub" style="color:#16a34a">Location: ICT Department Sejurumus</div>
        </div>
      </div>
      <div class="col-12">
        <div class="fshow-info-cell">
          <div class="fshow-info-label">Problem Reported</div>
          <div class="fshow-note">{!! nl2br(e($record->problem_possible ?: ($record->issue_description ?: '-'))) !!}</div>
        </div>
      </div>
      <div class="col-12">
        <div class="fshow-info-cell fshow-note-panel">
          <div class="fshow-info-label">Remarks / Replacement Details</div>
          <div class="fshow-note">{!! nl2br(e($record->remarks ?: 'No additional remarks.')) !!}</div>
          @if($record->temporary_spare_request_note)
          <div class="fshow-note-secondary">
            <div class="fshow-info-label" style="margin-bottom:4px">Temporary WT Note</div>
            <div class="fshow-note" style="color:#0369a1">{!! nl2br(e($record->temporary_spare_request_note)) !!}</div>
          </div>
          @endif
        </div>
      </div>
      <div class="col-12">
        <div class="fshow-info-cell">
          <div class="fshow-info-label">Evidence Uploaded</div>
          @if(is_array($record->evidence_paths) && count($record->evidence_paths))
          <div style="margin-top:8px;display:flex;flex-wrap:wrap;gap:8px">
            @foreach($record->evidence_paths as $path)
            <a href="{{ \Illuminate\Support\Facades\Storage::url($path) }}" target="_blank" rel="noopener noreferrer" class="btn-secondary-custom" style="padding:6px 12px;font-size:11px">
              <i class="fa-solid fa-paperclip"></i> File {{ $loop->iteration }}
            </a>
            @endforeach
          </div>
          @else
          <div class="fshow-info-sub">No evidence uploaded.</div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
