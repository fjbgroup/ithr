@extends('it.layouts.app')

@section('title', 'IT Request #' . $form->id)
@section('page_title', 'IT Request Form — Details')

@section('content')
<style>
.itr-show-section { background:var(--surface);border:1px solid var(--border);border-radius:12px;overflow:hidden;margin-bottom:14px; }
.itr-show-head { display:flex;align-items:center;gap:10px;padding:14px 20px;border-bottom:1px solid var(--border);background:var(--body-bg); }
.itr-show-num { width:24px;height:24px;border-radius:50%;background:rgba(2,132,199,.12);color:var(--accent);font-size:11px;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.itr-show-title { font-family:'DM Sans',sans-serif;font-size:13.5px;font-weight:700;color:var(--text); }
.itr-show-body { padding:20px; }
.itr-field-label { font-size:11.5px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px; }
.itr-field-value { font-size:13.5px;color:var(--text);font-weight:500;word-break:break-word; }
.itr-field-value.empty { color:var(--muted);font-style:italic; }
.itr-rich-text p { margin:0 0 6px; }
.itr-rich-text p:last-child { margin-bottom:0; }
.itr-rich-text ul,.itr-rich-text ol { margin:0 0 6px;padding-left:20px; }
.itr-rich-text strong { font-weight:700; }
.itr-rich-text em { font-style:italic; }
.itr-chip-list { display:flex;flex-wrap:wrap;gap:6px;margin-top:2px; }
.itr-chip-ro { display:inline-flex;align-items:center;background:rgba(2,132,199,.08);color:var(--accent);border-radius:20px;padding:4px 12px;font-size:12px;font-weight:600;border:1px solid rgba(2,132,199,.2); }
.sg2 { display:grid;grid-template-columns:1fr 1fr;gap:16px; }
.sg3 { display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px; }
.sg4 { display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:16px; }
.sf { margin-bottom:14px; }
.sf:last-child { margin-bottom:0; }
@media(max-width:720px) { .sg2,.sg3,.sg4 { grid-template-columns:1fr; } }
</style>

{{-- Back link + page header --}}
@php $isHou = $isHou ?? false; @endphp
<div style="margin-bottom:20px">
  <div style="margin-bottom:12px">
    <a href="{{ route('it.it-request-form') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--accent);text-decoration:none">
      <i class="bi bi-arrow-left"></i> {{ $isHou ? 'Back to IT Request Form' : 'Back to IT Requests' }}
    </a>
  </div>
  <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:5px">
    Request Forms &rsaquo; <a href="{{ route('it.it-request-form') }}" style="color:var(--accent);text-decoration:none">IT Request Forms</a> &rsaquo; <span style="color:var(--text)">#{{ $form->id }}</span>
  </div>
  <h4 style="font-family:'DM Sans',sans-serif;font-weight:800;font-size:22px;color:var(--text);margin:0 0 2px">IT Request #{{ $form->id }}</h4>
  <p style="font-size:13px;color:var(--muted);margin:0">Submitted {{ $form->created_at->format('d M Y, H:i') }}</p>
</div>

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #86efac;border-radius:9px;padding:12px 16px;margin-bottom:18px;font-size:13px;color:#166534;display:flex;align-items:center;gap:8px">
  <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
</div>
@endif
@if(session('error'))
<div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:9px;padding:12px 16px;margin-bottom:18px;font-size:13px;color:#991b1b;display:flex;align-items:center;gap:8px">
  <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
</div>
@endif

{{-- Summary banner --}}
@php
  $typeMap = [
    'hardware' => ['label'=>'Hardware','color'=>'#3b82f6','bg'=>'rgba(59,130,246,.1)','icon'=>'bi-laptop'],
    'software' => ['label'=>'Software','color'=>'#8b5cf6','bg'=>'rgba(139,92,246,.1)','icon'=>'bi-code-slash'],
    'system'   => ['label'=>'System',  'color'=>'#10b981','bg'=>'rgba(16,185,129,.1)', 'icon'=>'bi-hdd-network'],
    'service'  => ['label'=>'Service', 'color'=>'#0284c7','bg'=>'rgba(2,132,199,.1)',  'icon'=>'bi-wifi'],
  ];
  $t = $typeMap[$form->request_type] ?? ['label'=>ucfirst($form->request_type),'color'=>'#64748b','bg'=>'rgba(100,116,139,.1)','icon'=>'bi-question-circle'];
@endphp
<div style="background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:18px 20px;margin-bottom:20px;display:flex;gap:16px;flex-wrap:wrap;align-items:center">
  <span style="display:inline-flex;align-items:center;gap:6px;background:{{ $t['bg'] }};color:{{ $t['color'] }};border-radius:20px;padding:5px 14px;font-size:13px;font-weight:700">
    <i class="bi {{ $t['icon'] }}"></i>{{ $t['label'] }} Request
  </span>

  @if($form->status === 'New')
  <span style="display:inline-flex;align-items:center;gap:6px;background:rgba(217,119,6,.1);color:#d97706;border-radius:20px;padding:5px 14px;font-size:13px;font-weight:700">
    <span style="width:7px;height:7px;background:#d97706;border-radius:50%;display:inline-block"></span>New
  </span>
  @elseif($form->status === 'Pending IT')
  <span style="display:inline-flex;align-items:center;gap:6px;background:rgba(2,132,199,.1);color:#0284c7;border-radius:20px;padding:5px 14px;font-size:13px;font-weight:700">
    <span style="width:7px;height:7px;background:#0284c7;border-radius:50%;display:inline-block"></span>Pending IT Approval
  </span>
  @elseif($form->status === 'Pending Validation')
  <span style="display:inline-flex;align-items:center;gap:6px;background:rgba(124,58,237,.1);color:#7c3aed;border-radius:20px;padding:5px 14px;font-size:13px;font-weight:700">
    <span style="width:7px;height:7px;background:#7c3aed;border-radius:50%;display:inline-block"></span>Pending Validation
  </span>
  @elseif($form->status === 'Approved')
  <span style="display:inline-flex;align-items:center;gap:6px;background:rgba(22,163,74,.1);color:#16a34a;border-radius:20px;padding:5px 14px;font-size:13px;font-weight:700">
    <span style="width:7px;height:7px;background:#16a34a;border-radius:50%;display:inline-block"></span>Approved
  </span>
  @elseif($form->status === 'Rejected')
  <span style="display:inline-flex;align-items:center;gap:6px;background:rgba(220,38,38,.1);color:#dc2626;border-radius:20px;padding:5px 14px;font-size:13px;font-weight:700">
    <span style="width:7px;height:7px;background:#dc2626;border-radius:50%;display:inline-block"></span>Rejected
  </span>
  @else
  <span style="display:inline-flex;align-items:center;gap:6px;background:rgba(100,116,139,.1);color:#64748b;border-radius:20px;padding:5px 14px;font-size:13px;font-weight:700">
    <span style="width:7px;height:7px;background:#64748b;border-radius:50%;display:inline-block"></span>Draft
  </span>
  @endif

  <div style="display:flex;align-items:center;gap:8px;margin-left:4px">
    <div style="width:32px;height:32px;border-radius:50%;background:rgba(2,132,199,.12);color:var(--accent);display:flex;align-items:center;justify-content:center;font-size:14px"><i class="bi bi-person-fill"></i></div>
    <div>
      <div style="font-size:13px;font-weight:700;color:var(--text)">{{ $form->submittedBy?->full_name ?? 'Unknown' }}</div>
      <div style="font-size:11px;color:var(--muted)">{{ $form->submittedBy?->getItRoleLabel() ?? '' }}</div>
    </div>
  </div>
  <div style="margin-left:auto;text-align:right">
    <div style="font-size:12px;color:var(--muted)">Subject</div>
    <div style="font-size:14px;font-weight:700;color:var(--text)">{{ $form->subject }}</div>
  </div>
</div>

{{-- ── HOU Review Card (hidden once IT Admin approves onward) ── --}}
@if($form->hou_reviewed_by && !in_array($form->status, ['Pending Validation', 'Approved']))
@php
  $houActedApprove = in_array($form->status, ['Pending IT', 'Approved']) || ($form->status === 'Rejected' && $form->reviewed_by);
  $houResultColor  = $houActedApprove ? '#16a34a' : '#dc2626';
  $houResultBg     = $houActedApprove ? 'rgba(22,163,74,.06)' : 'rgba(220,38,38,.06)';
  $houResultBorder = $houActedApprove ? 'rgba(22,163,74,.25)' : 'rgba(220,38,38,.25)';
  $houResultIcon   = $houActedApprove ? 'bi-check-circle-fill' : 'bi-x-circle-fill';
  $houResultLabel  = $houActedApprove ? 'Approved by HOU' : 'Rejected by HOU';
@endphp
<div style="background:{{ $houResultBg }};border:1.5px solid {{ $houResultBorder }};border-radius:12px;padding:16px 20px;margin-bottom:14px">
  <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
    <i class="bi {{ $houResultIcon }}" style="font-size:20px;color:{{ $houResultColor }};flex-shrink:0"></i>
    <div style="flex:1">
      <div style="font-family:'DM Sans',sans-serif;font-size:14px;font-weight:700;color:{{ $houResultColor }}">
        {{ $houResultLabel }} — {{ $form->houReviewedBy?->full_name ?? '—' }}
        <span style="font-size:12px;font-weight:500;color:var(--muted);margin-left:8px">
          {{ $form->hou_reviewed_at ? $form->hou_reviewed_at->format('d M Y, H:i') : '' }}
        </span>
      </div>
      @if($form->hou_remarks)
      <div style="font-size:13px;color:var(--text);margin-top:6px;padding-top:6px;border-top:1px solid {{ $houResultBorder }}">
        <span style="font-weight:600;color:var(--muted);font-size:11.5px;text-transform:uppercase;letter-spacing:.04em">HOU Remarks: </span>{{ $form->hou_remarks }}
      </div>
      @endif
    </div>
  </div>
</div>
@endif

{{-- ── Admin Review Result Card (shown after admin has acted) ── --}}
@if($form->reviewed_by)
@php
  $isApproved    = in_array($form->status, ['Approved', 'Pending Validation']);
  $isNeedsUpdate = $form->status === 'Draft' && $form->reviewed_by;
  if ($isApproved) {
    $resultColor = '#16a34a'; $resultBg = 'rgba(22,163,74,.06)'; $resultBorder = 'rgba(22,163,74,.25)'; $resultIcon = 'bi-check-circle-fill'; $resultLabel = 'Approved by IT Admin';
  } elseif ($isNeedsUpdate) {
    $resultColor = '#d97706'; $resultBg = 'rgba(217,119,6,.06)'; $resultBorder = 'rgba(217,119,6,.25)'; $resultIcon = 'bi-arrow-clockwise'; $resultLabel = 'Update Requested by IT Admin';
  } else {
    $resultColor = '#dc2626'; $resultBg = 'rgba(220,38,38,.06)'; $resultBorder = 'rgba(220,38,38,.25)'; $resultIcon = 'bi-x-circle-fill'; $resultLabel = 'Rejected by IT Admin';
  }
@endphp
<div style="background:{{ $resultBg }};border:1.5px solid {{ $resultBorder }};border-radius:12px;padding:16px 20px;margin-bottom:20px">
  <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
    <i class="bi {{ $resultIcon }}" style="font-size:20px;color:{{ $resultColor }};flex-shrink:0"></i>
    <div style="flex:1">
      <div style="font-family:'DM Sans',sans-serif;font-size:14px;font-weight:700;color:{{ $resultColor }}">
        {{ $resultLabel }} — {{ $form->reviewedBy?->full_name ?? '—' }}
        <span style="font-size:12px;font-weight:500;color:var(--muted);margin-left:8px">
          {{ $form->reviewed_at ? $form->reviewed_at->format('d M Y, H:i') : '' }}
        </span>
      </div>
      @if($form->approval_remarks)
      <div style="font-size:13px;color:var(--text);margin-top:6px;padding-top:6px;border-top:1px solid {{ $resultBorder }}">
        <span style="font-weight:600;color:var(--muted);font-size:11.5px;text-transform:uppercase;letter-spacing:.04em">Remarks: </span>{{ $form->approval_remarks }}
      </div>
      @endif
    </div>
  </div>
</div>
@endif

{{-- ── HOU Action Bar (status=New: assigned HOU reviews) ── --}}
@php $isHou = $isHou ?? false; @endphp
@if($isHou && $form->status === 'New')
<div style="background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:16px 20px;margin-bottom:20px;display:flex;align-items:center;gap:12px;flex-wrap:wrap">
  <div style="flex:1;min-width:0">
    <div style="font-family:'DM Sans',sans-serif;font-size:13.5px;font-weight:700;color:var(--text)">Review this Request</div>
    <div style="font-size:12px;color:var(--muted);margin-top:2px">This request from your staff is awaiting your decision. If approved, it will be forwarded to IT Admin.</div>
  </div>
  <button onclick="openReviewModal('approve')"
    style="font-family:'DM Sans',sans-serif;font-size:13px;font-weight:700;padding:9px 20px;background:#16a34a;color:#fff;border:none;border-radius:9px;cursor:pointer;display:inline-flex;align-items:center;gap:7px;transition:background .15s"
    onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
    <i class="bi bi-check-circle-fill"></i> Approve
  </button>
  <button onclick="openReviewModal('reject')"
    style="font-family:'DM Sans',sans-serif;font-size:13px;font-weight:700;padding:9px 20px;background:#dc2626;color:#fff;border:none;border-radius:9px;cursor:pointer;display:inline-flex;align-items:center;gap:7px;transition:background .15s"
    onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">
    <i class="bi bi-x-circle-fill"></i> Reject
  </button>
</div>
@endif

{{-- ── Validator Review Card (shown after Mohd Azrull has acted) ── --}}
@if($form->validated_by)
@php
  $valApproved = $form->status === 'Approved';
  $valColor    = $valApproved ? '#16a34a' : '#dc2626';
  $valBg       = $valApproved ? 'rgba(22,163,74,.06)' : 'rgba(220,38,38,.06)';
  $valBorder   = $valApproved ? 'rgba(22,163,74,.25)' : 'rgba(220,38,38,.25)';
  $valIcon     = $valApproved ? 'bi-patch-check-fill' : 'bi-x-circle-fill';
  $valLabel    = $valApproved ? 'Validated & Approved' : 'Rejected by Validator';
@endphp
<div style="background:{{ $valBg }};border:1.5px solid {{ $valBorder }};border-radius:12px;padding:16px 20px;margin-bottom:20px">
  <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
    <i class="bi {{ $valIcon }}" style="font-size:20px;color:{{ $valColor }};flex-shrink:0"></i>
    <div style="flex:1">
      <div style="font-family:'DM Sans',sans-serif;font-size:14px;font-weight:700;color:{{ $valColor }}">
        {{ $valLabel }} — {{ $form->validatedBy?->full_name ?? '—' }}
        <span style="font-size:12px;font-weight:500;color:var(--muted);margin-left:8px">
          {{ $form->validated_at ? $form->validated_at->format('d M Y, H:i') : '' }}
        </span>
      </div>
      @if($form->validator_remarks)
      <div style="font-size:13px;color:var(--text);margin-top:6px;padding-top:6px;border-top:1px solid {{ $valBorder }}">
        <span style="font-weight:600;color:var(--muted);font-size:11.5px;text-transform:uppercase;letter-spacing:.04em">Validator Remarks: </span>{{ $form->validator_remarks }}
      </div>
      @endif
    </div>
  </div>
</div>
@endif

{{-- ── Admin Action Bar (status=Pending IT: IT Admin gives final decision) ── --}}
@if($user->isAdmin() && $form->status === 'Pending IT')
<div style="background:var(--surface);border:1px solid rgba(2,132,199,.3);border-radius:12px;padding:16px 20px;margin-bottom:20px;display:flex;align-items:center;gap:12px;flex-wrap:wrap">
  <div style="flex:1;min-width:0">
    <div style="font-family:'DM Sans',sans-serif;font-size:13.5px;font-weight:700;color:var(--text)">Final Approval Required</div>
    <div style="font-size:12px;color:var(--muted);margin-top:2px">This request has been approved by the HOU and is now awaiting your final decision.</div>
  </div>
  <button onclick="openReviewModal('approve')"
    style="font-family:'DM Sans',sans-serif;font-size:13px;font-weight:700;padding:9px 20px;background:#16a34a;color:#fff;border:none;border-radius:9px;cursor:pointer;display:inline-flex;align-items:center;gap:7px;transition:background .15s"
    onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
    <i class="bi bi-check-circle-fill"></i> Approve
  </button>
  <button onclick="openReviewModal('update')"
    style="font-family:'DM Sans',sans-serif;font-size:13px;font-weight:700;padding:9px 20px;background:#d97706;color:#fff;border:none;border-radius:9px;cursor:pointer;display:inline-flex;align-items:center;gap:7px;transition:background .15s"
    onmouseover="this.style.background='#b45309'" onmouseout="this.style.background='#d97706'">
    <i class="bi bi-arrow-clockwise"></i> Request Update
  </button>
  <button onclick="openReviewModal('reject')"
    style="font-family:'DM Sans',sans-serif;font-size:13px;font-weight:700;padding:9px 20px;background:#dc2626;color:#fff;border:none;border-radius:9px;cursor:pointer;display:inline-flex;align-items:center;gap:7px;transition:background .15s"
    onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">
    <i class="bi bi-x-circle-fill"></i> Reject
  </button>
</div>
@endif

{{-- ── Validator Action Bar (only Mohd Azrull, status=Pending Validation) ── --}}
@php $isValidator = $isValidator ?? false; @endphp
@if($isValidator && $form->status === 'Pending Validation')
<div style="background:var(--surface);border:1px solid rgba(124,58,237,.3);border-radius:12px;padding:16px 20px;margin-bottom:20px;display:flex;align-items:center;gap:12px;flex-wrap:wrap">
  <div style="flex:1;min-width:0">
    <div style="font-family:'DM Sans',sans-serif;font-size:13.5px;font-weight:700;color:var(--text)">Validation Required</div>
    <div style="font-size:12px;color:var(--muted);margin-top:2px">This request has been approved by the IT Admin and is awaiting your final validation.</div>
  </div>
  <button onclick="openReviewModal('approve')"
    style="font-family:'DM Sans',sans-serif;font-size:13px;font-weight:700;padding:9px 20px;background:#16a34a;color:#fff;border:none;border-radius:9px;cursor:pointer;display:inline-flex;align-items:center;gap:7px;transition:background .15s"
    onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
    <i class="bi bi-patch-check-fill"></i> Validate & Approve
  </button>
  <button onclick="openReviewModal('reject')"
    style="font-family:'DM Sans',sans-serif;font-size:13px;font-weight:700;padding:9px 20px;background:#dc2626;color:#fff;border:none;border-radius:9px;cursor:pointer;display:inline-flex;align-items:center;gap:7px;transition:background .15s"
    onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">
    <i class="bi bi-x-circle-fill"></i> Reject
  </button>
</div>
@endif

{{-- ── Section 1: Type-specific details ── --}}
<div class="itr-show-section">
  <div class="itr-show-head">
    <div class="itr-show-num">1</div>
    <div class="itr-show-title">
      @if($form->request_type === 'hardware') Request Type &amp; Item Selection
      @elseif($form->request_type === 'software') Software Request Details
      @elseif($form->request_type === 'system') System Request Details
      @else Service Request Type
      @endif
    </div>
  </div>
  <div class="itr-show-body">

    @if($form->request_type === 'hardware')
    <div class="sg2 sf">
      <div>
        <div class="itr-field-label">Type of Request</div>
        @php $hwLabels = ['new'=>'New','replacement'=>'Replacement','transfer_staff'=>'Transfer to Other Staff','transfer_company'=>'Transfer to Other Company']; @endphp
        <div class="itr-field-value">{{ $hwLabels[$form->hw_request_type] ?? ($form->hw_request_type ?? '—') }}</div>
      </div>
      <div>
        <div class="itr-field-label">Type of Item</div>
        @if(!empty($form->hw_items))
        <div class="itr-chip-list">
          @foreach($form->hw_items as $item)
          <span class="itr-chip-ro">{{ $item }}</span>
          @endforeach
        </div>
        @else
        <div class="itr-field-value empty">None selected</div>
        @endif
      </div>
    </div>
    <div class="sg2">
      <div class="sf"><div class="itr-field-label">PC / Laptop No.</div><div class="itr-field-value">{{ $form->hw_pc_laptop_no ?? '—' }}</div></div>
      <div class="sf"><div class="itr-field-label">Printer No.</div><div class="itr-field-value">{{ $form->hw_printer_no ?? '—' }}</div></div>
    </div>

    @elseif($form->request_type === 'software')
    <div class="sg2 sf">
      <div>
        <div class="itr-field-label">Type of Request</div>
        <div class="itr-field-value">{{ $form->sw_request_type ? ucfirst($form->sw_request_type) : '—' }}</div>
      </div>
      <div>
        <div class="itr-field-label">Software Name / Suggestion</div>
        <div class="itr-field-value">{{ $form->sw_software_name ?: '—' }}</div>
      </div>
    </div>
    <div class="sg4">
      <div class="sf"><div class="itr-field-label">Budgeted?</div><div class="itr-field-value">{{ $form->sw_budgeted ? ucfirst($form->sw_budgeted) : '—' }}</div></div>
      <div class="sf"><div class="itr-field-label">Opex / Capex</div><div class="itr-field-value">{{ $form->sw_opex_capex ? ucfirst($form->sw_opex_capex) : '—' }}</div></div>
      <div class="sf"><div class="itr-field-label">Cost Center</div><div class="itr-field-value">{{ $form->sw_cost_center ?: '—' }}</div></div>
      <div class="sf"><div class="itr-field-label">Expected Value (RM)</div><div class="itr-field-value">{{ $form->sw_expected_value ?: '—' }}</div></div>
    </div>

    @elseif($form->request_type === 'system')
    <div class="sg2 sf">
      <div>
        <div class="itr-field-label">Type of Request</div>
        <div class="itr-field-value">{{ $form->sys_request_type ? ucfirst($form->sys_request_type) : '—' }}</div>
      </div>
      <div>
        <div class="itr-field-label">Type of Item</div>
        @if(!empty($form->sys_items))
        <div class="itr-chip-list">
          @foreach($form->sys_items as $item)
          <span class="itr-chip-ro">{{ $item }}</span>
          @endforeach
        </div>
        @else
        <div class="itr-field-value empty">None selected</div>
        @endif
      </div>
    </div>

    @else {{-- service --}}
    <div class="sf">
      <div class="itr-field-label">Type of Request</div>
      @if(!empty($form->svc_items))
      <div class="itr-chip-list">
        @foreach($form->svc_items as $item)
        <span class="itr-chip-ro">{{ $item }}</span>
        @endforeach
      </div>
      @else
      <div class="itr-field-value empty">None selected</div>
      @endif
    </div>
    @endif

  </div>
</div>

{{-- ── Section 2: User Type & Justification ── --}}
<div class="itr-show-section">
  <div class="itr-show-head"><div class="itr-show-num">2</div><div class="itr-show-title">Type of User &amp; Justification</div></div>
  <div class="itr-show-body">
    <div class="sg2 sf">
      <div>
        <div class="sg2">
          <div class="sf"><div class="itr-field-label">Type of User</div><div class="itr-field-value">{{ $form->user_type ?? '—' }}</div></div>
          <div class="sf"><div class="itr-field-label">Exit / Join Date</div><div class="itr-field-value">{{ $form->exit_join_date ? $form->exit_join_date->format('d/m/Y') : '—' }}</div></div>
        </div>
      </div>
      <div>
        <div class="itr-field-label">Justification</div>
        <div class="itr-field-value itr-rich-text" style="line-height:1.6">{!! $form->justification ?: '—' !!}</div>
      </div>
    </div>
    @if($form->document_path)
    <div class="sf">
      <div class="itr-field-label">Supporting Document</div>
      <a href="{{ Storage::url($form->document_path) }}" target="_blank"
        style="display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:var(--accent);background:rgba(2,132,199,.08);border:1px solid rgba(2,132,199,.2);border-radius:7px;padding:6px 14px;text-decoration:none;margin-top:4px">
        <i class="bi bi-paperclip"></i> Download Attachment
      </a>
    </div>
    @endif
  </div>
</div>

{{-- ── Section 3: Requester Details ── --}}
<div class="itr-show-section">
  <div class="itr-show-head"><div class="itr-show-num">3</div><div class="itr-show-title">Requester Details</div></div>
  <div class="itr-show-body">
    <div class="sg3 sf">
      <div class="sf"><div class="itr-field-label">Name</div><div class="itr-field-value">{{ $form->req_name ?: '—' }}</div></div>
      <div class="sf"><div class="itr-field-label">Department</div><div class="itr-field-value">{{ $form->req_department ?: '—' }}</div></div>
      <div class="sf"><div class="itr-field-label">Staff ID</div><div class="itr-field-value">{{ $form->req_staff_id ?: '—' }}</div></div>
    </div>
    <div class="sg2">
      <div class="sf"><div class="itr-field-label">Designation</div><div class="itr-field-value">{{ $form->req_designation ?: '—' }}</div></div>
      <div class="sf"><div class="itr-field-label">Contact</div><div class="itr-field-value">{{ $form->req_contact ?: '—' }}</div></div>
    </div>
  </div>
</div>

{{-- ── Section 4: Approver Details ── --}}
<div class="itr-show-section">
  <div class="itr-show-head"><div class="itr-show-num">4</div><div class="itr-show-title">Approver Details</div></div>
  <div class="itr-show-body">
    <div class="sf"><div class="itr-field-label">Name</div><div class="itr-field-value">{{ $form->approver_name ?: '—' }}</div></div>
    <div class="sg3">
      <div class="sf"><div class="itr-field-label">Department</div><div class="itr-field-value">{{ $form->approver_department ?: '—' }}</div></div>
      <div class="sf"><div class="itr-field-label">Designation</div><div class="itr-field-value">{{ $form->approver_designation ?: '—' }}</div></div>
      <div class="sf"><div class="itr-field-label">Contact</div><div class="itr-field-value">{{ $form->approver_contact ?: '—' }}</div></div>
    </div>
  </div>
</div>

{{-- ── Review Modal ── --}}
@if(($isHou && $form->status === 'New') || ($user->isAdmin() && $form->status === 'Pending IT') || ($isValidator && $form->status === 'Pending Validation'))
<div id="reviewModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9000;align-items:center;justify-content:center;padding:20px">
  <div style="background:var(--surface);border-radius:14px;padding:28px;width:100%;max-width:440px;box-shadow:0 20px 60px rgba(0,0,0,.25);position:relative">
    <button onclick="closeReviewModal()" style="position:absolute;top:16px;right:16px;background:none;border:none;cursor:pointer;color:var(--muted);font-size:18px;line-height:1"><i class="bi bi-x-lg"></i></button>

    <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px">
      <div id="modalIconWrap" style="width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0"></div>
      <div>
        <h5 id="modalTitle" style="font-family:'DM Sans',sans-serif;font-size:16px;font-weight:800;color:var(--text);margin:0"></h5>
        <p style="font-size:12px;color:var(--muted);margin:2px 0 0">IT Request #{{ $form->id }} — {{ $form->subject }}</p>
      </div>
    </div>

    <form id="reviewForm" method="POST">
      @csrf
      <div style="margin-bottom:16px">
        <label id="remarksLabel" style="font-size:12px;font-weight:600;color:var(--text);text-transform:uppercase;letter-spacing:.04em;display:block;margin-bottom:6px">
          Remarks <span id="remarksOptional" style="font-weight:400;color:var(--muted);text-transform:none">(optional)</span>
        </label>
        <textarea name="{{ ($isValidator ?? false) ? 'validator_remarks' : 'approval_remarks' }}" rows="3"
          placeholder="Add a remark or reason…"
          style="width:100%;font-family:'DM Sans',sans-serif;font-size:13px;color:var(--text);background:var(--surface);border:1.5px solid var(--border);border-radius:8px;padding:10px 12px;outline:none;resize:vertical;box-sizing:border-box;transition:border-color .15s"
          onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'"></textarea>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end">
        <button type="button" onclick="closeReviewModal()"
          style="font-family:'DM Sans',sans-serif;font-size:13px;font-weight:600;padding:9px 20px;background:var(--body-bg);border:1.5px solid var(--border);border-radius:8px;color:var(--text);cursor:pointer;transition:all .15s"
          onmouseover="this.style.borderColor='var(--accent)';this.style.color='var(--accent)'"
          onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text)'">
          Cancel
        </button>
        <button type="submit" id="modalSubmitBtn"
          style="font-family:'DM Sans',sans-serif;font-size:13px;font-weight:700;padding:9px 24px;border:none;border-radius:8px;color:#fff;cursor:pointer;transition:background .15s">
        </button>
      </div>
    </form>
  </div>
</div>

<script>
@if($isHou ?? false)
var approveUrl = '{{ route("it.it-request-form.hou-approve", $form->id) }}';
var rejectUrl  = '{{ route("it.it-request-form.hou-reject",  $form->id) }}';
var updateUrl  = '';
@elseif($isValidator ?? false)
var approveUrl = '{{ route("it.it-request-form.validator-approve", $form->id) }}';
var rejectUrl  = '{{ route("it.it-request-form.validator-reject",  $form->id) }}';
var updateUrl  = '';
@else
var approveUrl = '{{ route("it.it-request-form.approve", $form->id) }}';
var rejectUrl  = '{{ route("it.it-request-form.reject",  $form->id) }}';
var updateUrl  = '{{ route("it.it-request-form.request-update", $form->id) }}';
@endif

function openReviewModal(action) {
  var modal = document.getElementById('reviewModal');
  var iconWrap = document.getElementById('modalIconWrap');
  var title = document.getElementById('modalTitle');
  var submitBtn = document.getElementById('modalSubmitBtn');
  var form = document.getElementById('reviewForm');
  var optional = document.getElementById('remarksOptional');

  if (action === 'approve') {
    iconWrap.style.background = 'rgba(22,163,74,.12)';
    iconWrap.style.color = '#16a34a';
    iconWrap.innerHTML = '<i class="bi bi-check-circle-fill"></i>';
    title.textContent = 'Approve Request';
    submitBtn.textContent = 'Approve';
    submitBtn.style.background = '#16a34a';
    submitBtn.onmouseover = function() { this.style.background = '#15803d'; };
    submitBtn.onmouseout  = function() { this.style.background = '#16a34a'; };
    optional.textContent = '(optional)';
    form.action = approveUrl;
  } else if (action === 'update') {
    iconWrap.style.background = 'rgba(217,119,6,.12)';
    iconWrap.style.color = '#d97706';
    iconWrap.innerHTML = '<i class="bi bi-arrow-clockwise"></i>';
    title.textContent = 'Request Update';
    submitBtn.textContent = 'Send for Update';
    submitBtn.style.background = '#d97706';
    submitBtn.onmouseover = function() { this.style.background = '#b45309'; };
    submitBtn.onmouseout  = function() { this.style.background = '#d97706'; };
    optional.textContent = '— explain what needs to be changed';
    form.action = updateUrl;
  } else {
    iconWrap.style.background = 'rgba(220,38,38,.12)';
    iconWrap.style.color = '#dc2626';
    iconWrap.innerHTML = '<i class="bi bi-x-circle-fill"></i>';
    title.textContent = 'Reject Request';
    submitBtn.textContent = 'Reject';
    submitBtn.style.background = '#dc2626';
    submitBtn.onmouseover = function() { this.style.background = '#b91c1c'; };
    submitBtn.onmouseout  = function() { this.style.background = '#dc2626'; };
    optional.textContent = '(optional)';
    form.action = rejectUrl;
  }

  modal.style.display = 'flex';
  setTimeout(function() { modal.querySelector('textarea').focus(); }, 80);
}

function closeReviewModal() {
  document.getElementById('reviewModal').style.display = 'none';
  document.getElementById('reviewForm').querySelector('textarea').value = '';
}

document.getElementById('reviewModal').addEventListener('click', function(e) {
  if (e.target === this) closeReviewModal();
});
</script>
@endif

@endsection

