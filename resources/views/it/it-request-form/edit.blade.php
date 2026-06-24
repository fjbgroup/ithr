@extends('it.layouts.app')

@section('title', 'Edit IT Request #' . $form->id)
@section('page_title', 'IT Request Form — Edit')

@section('content')
<style>
.itr-edit-section { background:var(--surface);border:1px solid var(--border);border-radius:12px;overflow:hidden;margin-bottom:14px; }
.itr-edit-head { display:flex;align-items:center;gap:10px;padding:14px 20px;border-bottom:1px solid var(--border);background:var(--body-bg); }
.itr-edit-num { width:24px;height:24px;border-radius:50%;background:rgba(2,132,199,.12);color:var(--accent);font-size:11px;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.itr-edit-title { font-family:'DM Sans',sans-serif;font-size:13.5px;font-weight:700;color:var(--text); }
.itr-edit-body { padding:20px; }
.eg2 { display:grid;grid-template-columns:1fr 1fr;gap:16px; }
.eg3 { display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px; }
.eg4 { display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:16px; }
.ef { margin-bottom:14px; }
.ef:last-child { margin-bottom:0; }
.itr-elabel { font-size:11.5px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px; }
.itr-ereq { color:#dc2626; }
.itr-einput { width:100%;font-family:'DM Sans',sans-serif;font-size:13.5px;padding:9px 13px;border:1.5px solid var(--border);border-radius:8px;background:var(--surface);color:var(--text);outline:none;box-sizing:border-box;transition:border-color .15s; }
.itr-einput:focus { border-color:var(--accent) !important;box-shadow:0 0 0 3px rgba(2,132,199,.1) !important; }
.itr-einput.is-error { border-color:#dc2626 !important;box-shadow:0 0 0 3px rgba(220,38,38,.1) !important; }
.itr-efield-error { color:#dc2626;font-size:11.5px;margin-top:4px;display:flex;align-items:center;gap:4px; }
.itr-epill-group { display:flex;flex-wrap:wrap;gap:8px;margin-top:4px; }
.itr-epill { display:inline-flex;align-items:center;gap:5px;padding:7px 14px;border-radius:20px;border:1.5px solid var(--border);background:var(--surface);color:var(--muted);font-size:12.5px;font-weight:600;cursor:pointer;transition:all .15s;user-select:none; }
.itr-epill:hover { border-color:var(--accent);color:var(--accent); }
.itr-epill.checked { background:rgba(2,132,199,.12);border-color:var(--accent);color:var(--accent); }
.itr-echip-grid { display:flex;flex-wrap:wrap;gap:8px;margin-top:4px; }
.itr-echip { display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:20px;border:1.5px solid var(--border);background:var(--surface);color:var(--muted);font-size:12.5px;font-weight:600;cursor:pointer;transition:all .15s;user-select:none; }
.itr-echip:hover { border-color:var(--accent);color:var(--accent); }
.itr-echip.checked { background:rgba(2,132,199,.12);border-color:var(--accent);color:var(--accent); }
.itr-echip .chip-dot { width:7px;height:7px;border-radius:50%;background:var(--border);flex-shrink:0;transition:background .15s; }
.itr-echip.checked .chip-dot { background:var(--accent); }
@media(max-width:720px) { .eg2,.eg3,.eg4 { grid-template-columns:1fr; } }
</style>

{{-- Page header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:8px">
  <div>
    @if($isAdmin)
    <a href="{{ route('it.it-request-form.show', $form->id) }}" style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--accent);text-decoration:none;margin-bottom:12px">
      <i class="bi bi-arrow-left"></i> Back to Details
    </a>
    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:5px">
      Request Forms &rsaquo; <a href="{{ route('it.it-request-form') }}" style="color:var(--accent);text-decoration:none">IT Request Forms</a> &rsaquo; <a href="{{ route('it.it-request-form.show', $form->id) }}" style="color:var(--accent);text-decoration:none">#{{ $form->id }}</a> &rsaquo; <span style="color:var(--text)">Edit</span>
    </div>
    <h4 style="font-family:'DM Sans',sans-serif;font-weight:800;font-size:22px;color:var(--text);margin:0 0 2px">Edit IT Request #{{ $form->id }}</h4>
    <p style="font-size:13px;color:var(--muted);margin:0">Submitted by {{ $form->submittedBy?->full_name ?? 'Unknown' }} on {{ $form->created_at->format('d M Y, H:i') }}</p>
    @else
    <a href="{{ route('it.it-request-form') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--accent);text-decoration:none;margin-bottom:12px">
      <i class="bi bi-arrow-left"></i> Back to IT Request Forms
    </a>
    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:5px">
      Request Forms &rsaquo; <a href="{{ route('it.it-request-form') }}" style="color:var(--accent);text-decoration:none">IT Request Forms</a> &rsaquo; <span style="color:var(--text)">Resume Draft #{{ $form->id }}</span>
    </div>
    <h4 style="font-family:'DM Sans',sans-serif;font-weight:800;font-size:22px;color:var(--text);margin:0 0 2px">Resume Draft — {{ ucfirst($form->request_type) }} Request</h4>
    <p style="font-size:13px;color:var(--muted);margin:0">Draft saved on {{ $form->updated_at->format('d M Y, H:i') }}. You can edit and submit, or save again as a draft.</p>
    @endif
  </div>
</div>

@php
  $typeMap = [
    'hardware' => ['label'=>'Hardware','color'=>'#3b82f6','bg'=>'rgba(59,130,246,.1)','icon'=>'bi-laptop'],
    'software' => ['label'=>'Software','color'=>'#8b5cf6','bg'=>'rgba(139,92,246,.1)','icon'=>'bi-code-slash'],
    'system'   => ['label'=>'System',  'color'=>'#10b981','bg'=>'rgba(16,185,129,.1)', 'icon'=>'bi-hdd-network'],
    'service'  => ['label'=>'Service', 'color'=>'#0284c7','bg'=>'rgba(2,132,199,.1)',  'icon'=>'bi-wifi'],
  ];
  $t = $typeMap[$form->request_type] ?? ['label'=>ucfirst($form->request_type),'color'=>'#64748b','bg'=>'rgba(100,116,139,.1)','icon'=>'bi-question-circle'];
@endphp

<form method="POST" action="{{ route('it.it-request-form.update', $form->id) }}" enctype="multipart/form-data" onsubmit="return collectEditChips()">
@csrf
@method('PUT')
<input type="hidden" name="request_type" value="{{ $form->request_type }}">

{{-- Subject row --}}
<div class="itr-edit-section">
  <div class="itr-edit-head">
    <span style="display:inline-flex;align-items:center;gap:6px;background:{{ $t['bg'] }};color:{{ $t['color'] }};border-radius:20px;padding:4px 12px;font-size:12px;font-weight:700">
      <i class="bi {{ $t['icon'] }}"></i>{{ $t['label'] }} Request
    </span>
    <div style="font-size:13px;font-weight:600;color:var(--text)">Request Subject</div>
  </div>
  <div class="itr-edit-body">
    <div class="eg2">
      <div class="ef">
        <div class="itr-elabel">Subject <span class="itr-ereq">*</span></div>
        <input class="itr-einput{{ $errors->has('subject') ? ' is-error' : '' }}" type="text" name="subject" value="{{ old('subject', $form->subject) }}" maxlength="200" required/>
        @error('subject')<div class="itr-efield-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
      </div>
      <div class="ef">
        <div class="itr-elabel">Status</div>
        <input class="itr-einput" type="text" value="{{ $form->status }}" readonly style="background:var(--body-bg);cursor:default"/>
      </div>
    </div>
  </div>
</div>

{{-- ── Section 1: Type-specific ── --}}
<div class="itr-edit-section">
  <div class="itr-edit-head">
    <div class="itr-edit-num">1</div>
    <div class="itr-edit-title">
      @if($form->request_type === 'hardware') Request Type &amp; Item Selection
      @elseif($form->request_type === 'software') Software Request Details
      @elseif($form->request_type === 'system') System Request Details
      @else Service Request Type
      @endif
    </div>
  </div>
  <div class="itr-edit-body">

    @if($form->request_type === 'hardware')
    <input type="hidden" name="hw_request_type" id="hw_req_type_val" value="{{ old('hw_request_type', $form->hw_request_type) }}">
    <div class="eg2 ef">
      <div>
        <div class="itr-elabel">Type of Request <span class="itr-ereq">*</span></div>
        <div class="itr-epill-group" id="hw-req-type-grp">
          @foreach(['new'=>'New','replacement'=>'Replacement','transfer_staff'=>'Transfer to Other Staff','transfer_company'=>'Transfer to Other Company'] as $val => $lbl)
          <div class="itr-epill{{ ($form->hw_request_type === $val || old('hw_request_type') === $val) ? ' checked' : '' }}"
            onclick="editPillSelect(this,'hw_req_type_val','{{ $val }}')">{{ $lbl }}</div>
          @endforeach
        </div>
        @error('hw_request_type')<div class="itr-efield-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
      </div>
      <div>
        <div class="itr-elabel">Type of Item <span class="itr-ereq">*</span></div>
        <div class="itr-echip-grid" id="hw-items-grid">
          @foreach(['Laptop','Desktop PC','Printer','Handphone','Tablet','IP Phone','Switch/Hub','UPS','Allow Install Software','Allow USB Drive','Color Printing Quota','Other'] as $item)
          <div class="itr-echip{{ in_array($item, old('hw_items', $form->hw_items ?? [])) ? ' checked' : '' }}"
            onclick="this.classList.toggle('checked')"><span class="chip-dot"></span>{{ $item }}</div>
          @endforeach
        </div>
        @error('hw_items')<div class="itr-efield-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
      </div>
    </div>
    <div class="eg2">
      <div class="ef">
        <div class="itr-elabel">PC / Laptop No. <span class="itr-ereq">*</span></div>
        <input class="itr-einput{{ $errors->has('hw_pc_laptop_no') ? ' is-error' : '' }}" type="text" name="hw_pc_laptop_no" value="{{ old('hw_pc_laptop_no', $form->hw_pc_laptop_no) }}" required/>
        @error('hw_pc_laptop_no')<div class="itr-efield-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
      </div>
      <div class="ef">
        <div class="itr-elabel">Printer No. <span class="itr-ereq">*</span></div>
        <input class="itr-einput{{ $errors->has('hw_printer_no') ? ' is-error' : '' }}" type="text" name="hw_printer_no" value="{{ old('hw_printer_no', $form->hw_printer_no) }}" required/>
        @error('hw_printer_no')<div class="itr-efield-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
      </div>
    </div>

    @elseif($form->request_type === 'software')
    <input type="hidden" name="sw_request_type" id="sw_req_type_val" value="{{ old('sw_request_type', $form->sw_request_type) }}">
    <input type="hidden" name="sw_budgeted"     id="sw_budgeted_val"  value="{{ old('sw_budgeted', $form->sw_budgeted) }}">
    <input type="hidden" name="sw_opex_capex"   id="sw_opex_val"      value="{{ old('sw_opex_capex', $form->sw_opex_capex) }}">
    <div class="eg2 ef">
      <div>
        <div class="itr-elabel">Type of Request <span class="itr-ereq">*</span></div>
        <div class="itr-epill-group" id="sw-req-type-grp">
          @foreach(['new'=>'New','amendment'=>'Amendment'] as $val => $lbl)
          <div class="itr-epill{{ ($form->sw_request_type === $val || old('sw_request_type') === $val) ? ' checked' : '' }}"
            onclick="editPillSelect(this,'sw_req_type_val','{{ $val }}')">{{ $lbl }}</div>
          @endforeach
        </div>
        @error('sw_request_type')<div class="itr-efield-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
      </div>
      <div class="ef">
        <div class="itr-elabel">Software Name / Suggestion</div>
        <input class="itr-einput" type="text" name="sw_software_name" value="{{ old('sw_software_name', $form->sw_software_name) }}"/>
      </div>
    </div>
    <div class="eg4 ef">
      <div>
        <div class="itr-elabel">Budgeted? <span class="itr-ereq">*</span></div>
        <div class="itr-epill-group" style="margin-top:4px" id="sw-budgeted-grp">
          @foreach(['yes'=>'Yes','no'=>'No'] as $val => $lbl)
          <div class="itr-epill{{ ($form->sw_budgeted === $val || old('sw_budgeted') === $val) ? ' checked' : '' }}"
            onclick="editPillSelect(this,'sw_budgeted_val','{{ $val }}')">{{ $lbl }}</div>
          @endforeach
        </div>
        @error('sw_budgeted')<div class="itr-efield-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
      </div>
      <div>
        <div class="itr-elabel">Opex / Capex <span class="itr-ereq">*</span></div>
        <div class="itr-epill-group" style="margin-top:4px" id="sw-opex-grp">
          @foreach(['opex'=>'Opex','capex'=>'Capex'] as $val => $lbl)
          <div class="itr-epill{{ ($form->sw_opex_capex === $val || old('sw_opex_capex') === $val) ? ' checked' : '' }}"
            onclick="editPillSelect(this,'sw_opex_val','{{ $val }}')">{{ $lbl }}</div>
          @endforeach
        </div>
        @error('sw_opex_capex')<div class="itr-efield-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
      </div>
      <div class="ef">
        <div class="itr-elabel">Cost Center <span class="itr-ereq">*</span></div>
        <input class="itr-einput{{ $errors->has('sw_cost_center') ? ' is-error' : '' }}" type="text" name="sw_cost_center" value="{{ old('sw_cost_center', $form->sw_cost_center) }}" placeholder="CC-XXX" required/>
        @error('sw_cost_center')<div class="itr-efield-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
      </div>
      <div class="ef">
        <div class="itr-elabel">Expected Product Value <span class="itr-ereq">*</span></div>
        <input class="itr-einput{{ $errors->has('sw_expected_value') ? ' is-error' : '' }}" type="text" name="sw_expected_value" value="{{ old('sw_expected_value', $form->sw_expected_value) }}" placeholder="RM" required/>
        @error('sw_expected_value')<div class="itr-efield-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
      </div>
    </div>

    @elseif($form->request_type === 'system')
    <input type="hidden" name="sys_request_type" id="sys_req_type_val" value="{{ old('sys_request_type', $form->sys_request_type) }}">
    <div class="eg2 ef">
      <div>
        <div class="itr-elabel">Type of Request <span class="itr-ereq">*</span></div>
        <div class="itr-epill-group" id="sys-req-type-grp">
          @foreach(['new'=>'New','amendment'=>'Amendment'] as $val => $lbl)
          <div class="itr-epill{{ ($form->sys_request_type === $val || old('sys_request_type') === $val) ? ' checked' : '' }}"
            onclick="editPillSelect(this,'sys_req_type_val','{{ $val }}')">{{ $lbl }}</div>
          @endforeach
        </div>
        @error('sys_request_type')<div class="itr-efield-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
      </div>
      <div>
        <div class="itr-elabel">Type of Item <span class="itr-ereq">*</span></div>
        <div class="itr-echip-grid" id="sys-items-grid">
          @foreach(['Email','Shared Folder','SAP','FGVHub','Procurehere','e-Daftar','e-CRM (SSC)','Other'] as $item)
          <div class="itr-echip{{ in_array($item, old('sys_items', $form->sys_items ?? [])) ? ' checked' : '' }}"
            onclick="this.classList.toggle('checked')"><span class="chip-dot"></span>{{ $item }}</div>
          @endforeach
        </div>
        @error('sys_items')<div class="itr-efield-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
      </div>
    </div>

    @else {{-- service --}}
    <div class="ef">
      <div class="itr-elabel">Type of Request <span class="itr-ereq">*</span></div>
      <div class="itr-echip-grid" id="svc-items-grid">
        @foreach(['Network (Reserve IP, Open Port, Internet Access)'] as $item)
        <div class="itr-echip{{ in_array($item, old('svc_items', $form->svc_items ?? [])) ? ' checked' : '' }}"
          onclick="this.classList.toggle('checked')"><span class="chip-dot"></span>{{ $item }}</div>
        @endforeach
      </div>
      @error('svc_items')<div class="itr-efield-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
    </div>
    @endif

  </div>
</div>

{{-- ── Section 2: User Type & Justification ── --}}
<div class="itr-edit-section">
  <div class="itr-edit-head"><div class="itr-edit-num">2</div><div class="itr-edit-title">Type of User &amp; Justification</div></div>
  <div class="itr-edit-body">
    <div class="eg2 ef">
      <div>
        <div class="ef">
          <div class="itr-elabel">Type of User <span class="itr-ereq">*</span></div>
          <select class="itr-einput{{ $errors->has('user_type') ? ' is-error' : '' }}" name="user_type" required>
            <option value="">-- Select --</option>
            @foreach(['New Hire','Intern','Resign','Existing','Vendor'] as $opt)
            <option {{ old('user_type', $form->user_type) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
            @endforeach
          </select>
          @error('user_type')<div class="itr-efield-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
        </div>
        <div class="ef">
          <div class="itr-elabel">Exit / Join Date <span class="itr-ereq">*</span></div>
          <input class="itr-einput{{ $errors->has('exit_join_date') ? ' is-error' : '' }}" type="date" name="exit_join_date" value="{{ old('exit_join_date', $form->exit_join_date?->format('Y-m-d')) }}" style="max-width:200px" required/>
          @error('exit_join_date')<div class="itr-efield-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
        </div>
      </div>
      <div class="ef">
        <div class="itr-elabel">Justification <span class="itr-ereq">*</span></div>
        <textarea class="itr-einput{{ $errors->has('justification') ? ' is-error' : '' }}" name="justification" rows="5" placeholder="Describe why this request is needed…" required>{{ old('justification', $form->justification) }}</textarea>
        @error('justification')<div class="itr-efield-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
      </div>
    </div>
    <div class="ef">
      <div class="itr-elabel">Supporting Document</div>
      @if($form->document_path)
      <div style="margin-bottom:8px">
        <a href="{{ Storage::url($form->document_path) }}" target="_blank"
          style="display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:var(--accent);background:rgba(2,132,199,.08);border:1px solid rgba(2,132,199,.2);border-radius:7px;padding:6px 14px;text-decoration:none">
          <i class="bi bi-paperclip"></i> Current Attachment
        </a>
        <span style="font-size:12px;color:var(--muted);margin-left:8px">Upload a new file below to replace it.</span>
      </div>
      @endif
      <input type="file" name="document" style="font-family:'DM Sans',sans-serif;font-size:13px;color:var(--text)"/>
      <div style="font-size:11.5px;color:var(--muted);margin-top:4px">Max 2MB · PDF, DOC, DOCX, JPG, JPEG, PNG</div>
      @error('document')<div class="itr-efield-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
    </div>
  </div>
</div>

{{-- ── Section 3: User Details ── --}}
<div class="itr-edit-section">
  <div class="itr-edit-head"><div class="itr-edit-num">3</div><div class="itr-edit-title">User Details</div></div>
  <div class="itr-edit-body">
    <div class="eg2 ef">
      <div class="ef">
        <div class="itr-elabel">Name <span class="itr-ereq">*</span></div>
        <input class="itr-einput{{ $errors->has('user_name') ? ' is-error' : '' }}" type="text" name="user_name" value="{{ old('user_name', $form->user_name) }}" required/>
        @error('user_name')<div class="itr-efield-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
      </div>
      <div class="ef">
        <div class="itr-elabel">Email <span class="itr-ereq">*</span></div>
        <input class="itr-einput{{ $errors->has('user_email') ? ' is-error' : '' }}" type="email" name="user_email" value="{{ old('user_email', $form->user_email) }}" required/>
        @error('user_email')<div class="itr-efield-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
      </div>
    </div>
    <div class="ef">
      <div class="itr-elabel">Address <span class="itr-ereq">*</span></div>
      <input class="itr-einput{{ $errors->has('user_address') ? ' is-error' : '' }}" type="text" name="user_address" value="{{ old('user_address', $form->user_address) }}" required/>
      @error('user_address')<div class="itr-efield-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
    </div>
    <div class="eg4">
      <div class="ef">
        <div class="itr-elabel">Department <span class="itr-ereq">*</span></div>
        <input class="itr-einput{{ $errors->has('user_department') ? ' is-error' : '' }}" type="text" name="user_department" value="{{ old('user_department', $form->user_department) }}" required/>
        @error('user_department')<div class="itr-efield-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
      </div>
      <div class="ef">
        <div class="itr-elabel">Designation <span class="itr-ereq">*</span></div>
        <input class="itr-einput{{ $errors->has('user_designation') ? ' is-error' : '' }}" type="text" name="user_designation" value="{{ old('user_designation', $form->user_designation) }}" required/>
        @error('user_designation')<div class="itr-efield-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
      </div>
      <div class="ef">
        <div class="itr-elabel">Staff ID <span class="itr-ereq">*</span></div>
        <input class="itr-einput{{ $errors->has('user_staff_id') ? ' is-error' : '' }}" type="text" name="user_staff_id" value="{{ old('user_staff_id', $form->user_staff_id) }}" placeholder="12345678" required/>
        @error('user_staff_id')<div class="itr-efield-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
      </div>
      <div class="ef">
        <div class="itr-elabel">Contact No. <span class="itr-ereq">*</span></div>
        <input class="itr-einput{{ $errors->has('user_contact') ? ' is-error' : '' }}" type="text" name="user_contact" value="{{ old('user_contact', $form->user_contact) }}" required/>
        @error('user_contact')<div class="itr-efield-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
      </div>
    </div>
  </div>
</div>

{{-- ── Section 4: Requester Details ── --}}
<div class="itr-edit-section">
  <div class="itr-edit-head"><div class="itr-edit-num">4</div><div class="itr-edit-title">Requester Details</div></div>
  <div class="itr-edit-body">
    <div class="eg3 ef">
      <div class="ef">
        <div class="itr-elabel">Name <span class="itr-ereq">*</span></div>
        <input class="itr-einput{{ $errors->has('req_name') ? ' is-error' : '' }}" type="text" name="req_name" value="{{ old('req_name', $form->req_name) }}" placeholder="Enter a name or email address…" required/>
        @error('req_name')<div class="itr-efield-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
      </div>
      <div class="ef">
        <div class="itr-elabel">Department <span class="itr-ereq">*</span></div>
        <input class="itr-einput{{ $errors->has('req_department') ? ' is-error' : '' }}" type="text" name="req_department" value="{{ old('req_department', $form->req_department) }}" required/>
        @error('req_department')<div class="itr-efield-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
      </div>
      <div class="ef">
        <div class="itr-elabel">Staff ID <span class="itr-ereq">*</span></div>
        <input class="itr-einput{{ $errors->has('req_staff_id') ? ' is-error' : '' }}" type="text" name="req_staff_id" value="{{ old('req_staff_id', $form->req_staff_id) }}" required/>
        @error('req_staff_id')<div class="itr-efield-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
      </div>
    </div>
    <div class="eg3">
      <div class="ef">
        <div class="itr-elabel">Designation <span class="itr-ereq">*</span></div>
        <input class="itr-einput{{ $errors->has('req_designation') ? ' is-error' : '' }}" type="text" name="req_designation" value="{{ old('req_designation', $form->req_designation) }}" required/>
        @error('req_designation')<div class="itr-efield-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
      </div>
      <div class="ef">
        <div class="itr-elabel">Contact <span class="itr-ereq">*</span></div>
        <input class="itr-einput{{ $errors->has('req_contact') ? ' is-error' : '' }}" type="text" name="req_contact" value="{{ old('req_contact', $form->req_contact) }}" required/>
        @error('req_contact')<div class="itr-efield-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
      </div>
      <div class="ef">
        <div class="itr-elabel">Company <span class="itr-ereq">*</span></div>
        <select class="itr-einput{{ $errors->has('req_company') ? ' is-error' : '' }}" name="req_company" required>
          <option value="">&lt; Select Company &gt;</option>
          @foreach(['FGV Johor Bulkers Sdn Bhd','FGV Holdings Berhad','FGV Plantation'] as $co)
          <option {{ old('req_company', $form->req_company) === $co ? 'selected' : '' }}>{{ $co }}</option>
          @endforeach
        </select>
        @error('req_company')<div class="itr-efield-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
      </div>
    </div>
  </div>
</div>

{{-- ── Section 5: Approver Details ── --}}
<div class="itr-edit-section">
  <div class="itr-edit-head"><div class="itr-edit-num">5</div><div class="itr-edit-title">Approver Details</div></div>
  <div class="itr-edit-body">
    <div class="ef">
      <div class="itr-elabel">Name <span class="itr-ereq">*</span></div>
      <input class="itr-einput{{ $errors->has('approver_name') ? ' is-error' : '' }}" type="text" name="approver_name" value="{{ old('approver_name', $form->approver_name) }}" placeholder="Enter a name or email address…" required/>
      @error('approver_name')<div class="itr-efield-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
    </div>
    <div class="eg4">
      <div class="ef">
        <div class="itr-elabel">Department</div>
        <input class="itr-einput" type="text" name="approver_department" value="{{ old('approver_department', $form->approver_department) }}"/>
      </div>
      <div class="ef">
        <div class="itr-elabel">Designation</div>
        <input class="itr-einput" type="text" name="approver_designation" value="{{ old('approver_designation', $form->approver_designation) }}"/>
      </div>
      <div class="ef">
        <div class="itr-elabel">Contact</div>
        <input class="itr-einput" type="text" name="approver_contact" value="{{ old('approver_contact', $form->approver_contact) }}"/>
      </div>
      <div class="ef">
        <div class="itr-elabel">Company <span class="itr-ereq">*</span></div>
        <select class="itr-einput{{ $errors->has('approver_company') ? ' is-error' : '' }}" name="approver_company" required>
          <option value="">&lt; Select Company &gt;</option>
          @foreach(['FGV Johor Bulkers Sdn Bhd','FGV Holdings Berhad','FGV Plantation'] as $co)
          <option {{ old('approver_company', $form->approver_company) === $co ? 'selected' : '' }}>{{ $co }}</option>
          @endforeach
        </select>
        @error('approver_company')<div class="itr-efield-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
      </div>
    </div>
  </div>
</div>

{{-- Action bar --}}
@if($isAdmin)
<div style="display:flex;align-items:center;gap:12px;margin-top:6px">
  <button type="submit"
    style="font-family:'DM Sans',sans-serif;font-size:13px;font-weight:700;padding:10px 28px;background:#142b47;color:#fff;border:none;border-radius:9px;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:background .15s"
    onmouseover="this.style.background='#254a78'" onmouseout="this.style.background='#142b47'">
    <i class="bi bi-floppy-fill"></i> Update Request
  </button>
  <a href="{{ route('it.it-request-form.show', $form->id) }}"
    style="font-family:'DM Sans',sans-serif;font-size:13px;font-weight:600;padding:10px 20px;background:var(--surface);border:1.5px solid var(--border);border-radius:9px;color:var(--text);text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all .15s"
    onmouseover="this.style.borderColor='var(--accent)';this.style.color='var(--accent)'"
    onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text)'">
    Cancel
  </a>
</div>
@else
<p style="font-size:12px;color:var(--muted);margin:6px 0 10px">Fields marked <strong style="color:#dc2626">*</strong> are required only when submitting to IT Admin. You may <strong>Save as Draft</strong> at any time with incomplete fields.</p>
<div style="display:flex;align-items:center;gap:12px;margin-top:6px;flex-wrap:wrap">
  <button type="submit" name="action" value="submit"
    style="font-family:'DM Sans',sans-serif;font-size:13px;font-weight:700;padding:10px 28px;background:#142b47;color:#fff;border:none;border-radius:9px;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:background .15s"
    onmouseover="this.style.background='#254a78'" onmouseout="this.style.background='#142b47'">
    <i class="bi bi-send-fill"></i> Submit Request
  </button>
  <button type="submit" formnovalidate name="action" value="draft"
    style="font-family:'DM Sans',sans-serif;font-size:13px;font-weight:700;padding:10px 22px;background:var(--surface);color:#64748b;border:1.5px solid var(--border);border-radius:9px;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all .15s"
    onmouseover="this.style.borderColor='#64748b'" onmouseout="this.style.borderColor='var(--border)'">
    <i class="bi bi-floppy-fill"></i> Save as Draft
  </button>
  <a href="{{ route('it.it-request-form') }}"
    style="font-family:'DM Sans',sans-serif;font-size:13px;font-weight:600;padding:10px 20px;background:var(--surface);border:1.5px solid var(--border);border-radius:9px;color:var(--text);text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all .15s"
    onmouseover="this.style.borderColor='var(--accent)';this.style.color='var(--accent)'"
    onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text)'">
    Cancel
  </a>
</div>
@endif

</form>

<script>
function editPillSelect(el, hiddenId, value) {
  var grp = el.parentElement;
  grp.querySelectorAll('.itr-epill').forEach(function(p) { p.classList.remove('checked'); });
  el.classList.add('checked');
  var h = document.getElementById(hiddenId);
  if (h) h.value = value;
}

function collectEditChips() {
  var type = '{{ $form->request_type }}';
  var chipDefs = {
    hardware: ['hw-items-grid',  'hw_items'],
    system:   ['sys-items-grid', 'sys_items'],
    service:  ['svc-items-grid', 'svc_items'],
  };
  if (chipDefs[type]) {
    var gridId    = chipDefs[type][0];
    var fieldName = chipDefs[type][1];
    var grid = document.getElementById(gridId);
    if (grid) {
      var form = grid.closest('form');
      form.querySelectorAll('input[name="' + fieldName + '[]"]').forEach(function(el) { el.remove(); });
      grid.querySelectorAll('.itr-echip.checked').forEach(function(chip) {
        var input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = fieldName + '[]';
        input.value = chip.textContent.trim();
        form.appendChild(input);
      });
    }
  }
  return true;
}
</script>
@endsection

