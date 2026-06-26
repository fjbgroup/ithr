@extends('it.layouts.app')

@section('title', 'IT Request #' . $form->id)
@section('page_title', 'IT Request Form — View')

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
<div style="margin-bottom:20px">
  <div style="margin-bottom:12px">
    <a href="{{ route('it.it-request-form') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--accent);text-decoration:none">
      <i class="bi bi-arrow-left"></i> Back to My Requests
    </a>
  </div>
  <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:5px">
    Request Forms &rsaquo; <a href="{{ route('it.it-request-form') }}" style="color:var(--accent);text-decoration:none">IT Request Forms</a> &rsaquo; <span style="color:var(--text)">#{{ $form->id }}</span>
  </div>
  <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
    <div>
      <h4 style="font-family:'DM Sans',sans-serif;font-weight:800;font-size:22px;color:var(--text);margin:0 0 2px">IT Request #{{ $form->id }}</h4>
      <p style="font-size:13px;color:var(--muted);margin:0">Submitted {{ $form->created_at->format('d M Y, H:i') }}</p>
    </div>
    <span style="display:inline-flex;align-items:center;gap:5px;background:rgba(100,116,139,.08);color:#64748b;border:1px solid rgba(100,116,139,.25);border-radius:20px;padding:4px 12px;font-size:12px;font-weight:700;margin-left:auto">
      <i class="bi bi-eye"></i> View Only
    </span>
  </div>
</div>

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
    <span style="width:7px;height:7px;background:#d97706;border-radius:50%;display:inline-block"></span>Pending HOU Approval
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
    <div style="font-size:14px;font-weight:700;color:var(--text)">{{ $form->subject ?? '—' }}</div>
  </div>
</div>

{{-- Status tracker --}}
@php
  $sfS1 = 'done';
  $sfS2 = in_array($form->status, ['New','Pending IT','Pending Validation']) ? 'active' : ($form->status === 'Draft' ? 'pending' : 'done');
  $sfS3 = in_array($form->status, ['Approved','Rejected']) ? $form->status : 'pending';
  if ($form->status === 'Draft') { $sfS1 = 'pending'; $sfS2 = 'pending'; }
@endphp
<div style="background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:20px 24px;margin-bottom:20px">
  <div style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:16px">Request Progress</div>
  <div style="display:flex;align-items:flex-start;gap:0">
    @php $c1 = $sfS1==='done' ? '#16a34a' : '#cbd5e1'; $bg1 = $sfS1==='done' ? 'rgba(22,163,74,.12)' : 'rgba(203,213,225,.2)'; @endphp
    <div style="display:flex;flex-direction:column;align-items:center;gap:6px;min-width:90px">
      <div style="width:38px;height:38px;border-radius:50%;background:{{ $bg1 }};border:2px solid {{ $c1 }};display:flex;align-items:center;justify-content:center;font-size:16px;color:{{ $c1 }}">
        <i class="bi {{ $sfS1==='done' ? 'bi-check-lg' : 'bi-send-fill' }}"></i>
      </div>
      <div style="font-size:11px;font-weight:700;color:{{ $c1 }};text-align:center;line-height:1.3">Submitted</div>
    </div>
    @php $line1 = $sfS1==='done' ? '#16a34a' : '#e2e8f0'; @endphp
    <div style="flex:1;height:2px;background:{{ $line1 }};margin-top:19px;border-radius:2px"></div>
    @php
      $c2 = $sfS2==='active' ? '#d97706' : ($sfS2==='done' ? '#16a34a' : '#cbd5e1');
      $bg2 = $sfS2==='active' ? 'rgba(217,119,6,.12)' : ($sfS2==='done' ? 'rgba(22,163,74,.12)' : 'rgba(203,213,225,.2)');
      $i2  = $sfS2==='active' ? 'bi-hourglass-split' : ($sfS2==='done' ? 'bi-check-lg' : 'bi-hourglass');
    @endphp
    <div style="display:flex;flex-direction:column;align-items:center;gap:6px;min-width:90px">
      <div style="width:38px;height:38px;border-radius:50%;background:{{ $bg2 }};border:2px solid {{ $c2 }};display:flex;align-items:center;justify-content:center;font-size:15px;color:{{ $c2 }}">
        <i class="bi {{ $i2 }}"></i>
      </div>
      <div style="font-size:11px;font-weight:700;color:{{ $c2 }};text-align:center;line-height:1.3">Under Review</div>
    </div>
    @php $line2 = in_array($sfS3, ['Approved','Rejected']) ? '#16a34a' : '#e2e8f0'; @endphp
    <div style="flex:1;height:2px;background:{{ $line2 }};margin-top:19px;border-radius:2px"></div>
    @php
      $c3 = $sfS3==='Approved' ? '#16a34a' : ($sfS3==='Rejected' ? '#dc2626' : '#cbd5e1');
      $bg3 = $sfS3==='Approved' ? 'rgba(22,163,74,.12)' : ($sfS3==='Rejected' ? 'rgba(220,38,38,.12)' : 'rgba(203,213,225,.2)');
      $i3  = $sfS3==='Approved' ? 'bi-check-circle-fill' : ($sfS3==='Rejected' ? 'bi-x-circle-fill' : 'bi-circle');
    @endphp
    <div style="display:flex;flex-direction:column;align-items:center;gap:6px;min-width:90px">
      <div style="width:38px;height:38px;border-radius:50%;background:{{ $bg3 }};border:2px solid {{ $c3 }};display:flex;align-items:center;justify-content:center;font-size:15px;color:{{ $c3 }}">
        <i class="bi {{ $i3 }}"></i>
      </div>
      <div style="font-size:11px;font-weight:700;color:{{ $c3 }};text-align:center;line-height:1.3">Decision</div>
    </div>
  </div>
</div>

{{-- HOU Review Result --}}
@if($form->hou_reviewed_by)
@php
  $houApproved = in_array($form->status, ['Pending IT','Pending Validation','Approved']) || ($form->status === 'Rejected' && $form->reviewed_by);
  $houColor  = $houApproved ? '#16a34a' : '#dc2626';
  $houBg     = $houApproved ? 'rgba(22,163,74,.06)' : 'rgba(220,38,38,.06)';
  $houBorder = $houApproved ? 'rgba(22,163,74,.25)' : 'rgba(220,38,38,.25)';
  $houIcon   = $houApproved ? 'bi-check-circle-fill' : 'bi-x-circle-fill';
  $houLabel  = $houApproved ? 'Approved by HOU' : 'Rejected by HOU';
@endphp
<div style="background:{{ $houBg }};border:1.5px solid {{ $houBorder }};border-radius:12px;padding:16px 20px;margin-bottom:14px">
  <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
    <i class="bi {{ $houIcon }}" style="font-size:20px;color:{{ $houColor }};flex-shrink:0"></i>
    <div style="flex:1">
      <div style="font-family:'DM Sans',sans-serif;font-size:14px;font-weight:700;color:{{ $houColor }}">
        {{ $houLabel }} — {{ $form->houReviewedBy?->full_name ?? '—' }}
        <span style="font-size:12px;font-weight:500;color:var(--muted);margin-left:8px">
          {{ $form->hou_reviewed_at ? $form->hou_reviewed_at->format('d M Y, H:i') : '' }}
        </span>
      </div>
      @if($form->hou_remarks)
      <div style="font-size:13px;color:var(--text);margin-top:6px;padding-top:6px;border-top:1px solid {{ $houBorder }}">
        <span style="font-weight:600;color:var(--muted);font-size:11.5px;text-transform:uppercase;letter-spacing:.04em">HOU Remarks: </span>{{ $form->hou_remarks }}
      </div>
      @endif
    </div>
  </div>
</div>
@endif

{{-- Admin Review Result --}}
@if($form->reviewed_by)
@php
  $isApproved = in_array($form->status, ['Approved','Pending Validation']);
  $isNeedsUpd = $form->status === 'Draft' && $form->reviewed_by;
  if ($isApproved)    { $rColor='#16a34a'; $rBg='rgba(22,163,74,.06)'; $rBorder='rgba(22,163,74,.25)'; $rIcon='bi-check-circle-fill'; $rLabel='Approved by IT Admin'; }
  elseif ($isNeedsUpd){ $rColor='#d97706'; $rBg='rgba(217,119,6,.06)'; $rBorder='rgba(217,119,6,.25)'; $rIcon='bi-arrow-clockwise'; $rLabel='Update Requested by IT Admin'; }
  else                 { $rColor='#dc2626'; $rBg='rgba(220,38,38,.06)'; $rBorder='rgba(220,38,38,.25)'; $rIcon='bi-x-circle-fill'; $rLabel='Rejected by IT Admin'; }
@endphp
<div style="background:{{ $rBg }};border:1.5px solid {{ $rBorder }};border-radius:12px;padding:16px 20px;margin-bottom:14px">
  <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
    <i class="bi {{ $rIcon }}" style="font-size:20px;color:{{ $rColor }};flex-shrink:0"></i>
    <div style="flex:1">
      <div style="font-family:'DM Sans',sans-serif;font-size:14px;font-weight:700;color:{{ $rColor }}">
        {{ $rLabel }} — {{ $form->reviewedBy?->full_name ?? '—' }}
        <span style="font-size:12px;font-weight:500;color:var(--muted);margin-left:8px">
          {{ $form->reviewed_at ? $form->reviewed_at->format('d M Y, H:i') : '' }}
        </span>
      </div>
      @if($form->approval_remarks)
      <div style="font-size:13px;color:var(--text);margin-top:6px;padding-top:6px;border-top:1px solid {{ $rBorder }}">
        <span style="font-weight:600;color:var(--muted);font-size:11.5px;text-transform:uppercase;letter-spacing:.04em">Remarks: </span>{{ $form->approval_remarks }}
      </div>
      @endif
    </div>
  </div>
</div>
@endif

{{-- Validator Review Result --}}
@if($form->validated_by)
@php
  $valApproved = $form->status === 'Approved';
  $valColor  = $valApproved ? '#16a34a' : '#dc2626';
  $valBg     = $valApproved ? 'rgba(22,163,74,.06)' : 'rgba(220,38,38,.06)';
  $valBorder = $valApproved ? 'rgba(22,163,74,.25)' : 'rgba(220,38,38,.25)';
  $valIcon   = $valApproved ? 'bi-patch-check-fill' : 'bi-x-circle-fill';
  $valLabel  = $valApproved ? 'Validated & Approved' : 'Rejected by Validator';
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

{{-- Section 1: Type-specific details --}}
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
    @else
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

{{-- Section 2: User Type & Justification --}}
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

{{-- Section 3: User Details (the staff member) --}}
<div class="itr-show-section">
  <div class="itr-show-head"><div class="itr-show-num">3</div><div class="itr-show-title">User Details</div></div>
  <div class="itr-show-body">
    <div class="sg3 sf">
      <div class="sf"><div class="itr-field-label">Name</div><div class="itr-field-value">{{ $form->user_name ?: '—' }}</div></div>
      <div class="sf"><div class="itr-field-label">Email</div><div class="itr-field-value">{{ $form->user_email ?: '—' }}</div></div>
      <div class="sf"><div class="itr-field-label">Staff ID</div><div class="itr-field-value">{{ $form->user_staff_id ?: '—' }}</div></div>
    </div>
    <div class="sg3">
      <div class="sf"><div class="itr-field-label">Department</div><div class="itr-field-value">{{ $form->user_department ?: '—' }}</div></div>
      <div class="sf"><div class="itr-field-label">Designation</div><div class="itr-field-value">{{ $form->user_designation ?: '—' }}</div></div>
      <div class="sf"><div class="itr-field-label">Contact</div><div class="itr-field-value">{{ $form->user_contact ?: '—' }}</div></div>
    </div>
  </div>
</div>

{{-- Section 4: Requester Details --}}
<div class="itr-show-section">
  <div class="itr-show-head"><div class="itr-show-num">4</div><div class="itr-show-title">Requester Details</div></div>
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

{{-- Section 5: Approver Details --}}
<div class="itr-show-section">
  <div class="itr-show-head"><div class="itr-show-num">5</div><div class="itr-show-title">Approver Details</div></div>
  <div class="itr-show-body">
    <div class="sf"><div class="itr-field-label">Name</div><div class="itr-field-value">{{ $form->approver_name ?: '—' }}</div></div>
    <div class="sg3">
      <div class="sf"><div class="itr-field-label">Department</div><div class="itr-field-value">{{ $form->approver_department ?: '—' }}</div></div>
      <div class="sf"><div class="itr-field-label">Designation</div><div class="itr-field-value">{{ $form->approver_designation ?: '—' }}</div></div>
      <div class="sf"><div class="itr-field-label">Contact</div><div class="itr-field-value">{{ $form->approver_contact ?: '—' }}</div></div>
    </div>
  </div>
</div>

@endsection
