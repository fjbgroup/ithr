@extends('it.layouts.app')

@section('title', 'IT Request Form')
@section('page_title', 'IT Request Form')

@section('content')
@if($user->isAdmin())

{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     IT ADMIN — INBOX VIEW
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
<style>
.itr-admin-stat { background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:18px 20px;display:flex;align-items:center;gap:14px;transition:box-shadow .15s; }
.itr-admin-stat:hover { box-shadow:0 4px 18px rgba(0,0,0,.07); }
.itr-admin-stat-icon { width:42px;height:42px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:19px;flex-shrink:0; }
.itr-admin-stat-val { font-family:'Inter',sans-serif;font-size:26px;font-weight:800;color:var(--text);line-height:1; }
.itr-admin-stat-lbl { font-size:11.5px;color:var(--muted);margin-top:3px;font-weight:500; }

.itr-type-pill { display:inline-flex;align-items:center;gap:5px;border-radius:20px;padding:2px 10px;font-size:11px;font-weight:700;line-height:1.8; }
.itr-status-dot { display:inline-flex;align-items:center;gap:5px;border-radius:20px;padding:4px 11px;font-size:11.5px;font-weight:600;white-space:nowrap; }
.itr-status-dot span { width:6px;height:6px;border-radius:50%;flex-shrink:0;display:inline-block; }

.itr-admin-row { display:grid;align-items:center;border-bottom:1px solid var(--border);transition:background .12s;cursor:pointer;text-decoration:none;color:inherit; }
.itr-admin-row:hover { background:var(--body-bg); }
.itr-admin-row:last-child { border-bottom:none; }
.itr-admin-row.is-new { border-left:3px solid #d97706; }
.itr-admin-row.is-new:not(:hover) { background:rgba(217,119,6,.025); }
.itr-admin-row.is-pending-it { border-left:3px solid #0284c7; }
.itr-admin-row.is-pending-it:not(:hover) { background:rgba(2,132,199,.025); }
.itr-admin-row.is-pending-validation { border-left:3px solid #7c3aed; }
.itr-admin-row.is-pending-validation:not(:hover) { background:rgba(124,58,237,.025); }

/* Bulk checkbox styles */
.bulk-cb { width:15px;height:15px;cursor:pointer;accent-color:var(--accent);flex-shrink:0; }

/* Archive */
.itr-archive-btn { display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:7px;border:1.5px solid transparent;background:transparent;cursor:pointer;color:var(--muted);font-size:14px;transition:all .15s;flex-shrink:0; }
.itr-archive-btn:hover { background:rgba(217,119,6,.1);border-color:rgba(217,119,6,.3);color:#d97706; }

@media(max-width:900px){
  .itr-admin-cols { grid-template-columns:1fr 1fr 1fr !important; }
}
</style>

{{-- Page header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:10px">
  <div>
    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:5px">
      Request Forms &rsaquo; <span style="color:var(--accent)">IT Request Forms</span>
    </div>
    <h4 style="font-family:'Inter',sans-serif;font-weight:800;font-size:22px;color:var(--text);margin:0 0 3px">IT Request Inbox</h4>
    <p style="font-size:13px;color:var(--muted);margin:0">Review and manage all IT service requests from staff</p>
  </div>
  @if($countNew > 0 || $countPendingIT > 0)
  <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(217,119,6,.1);border:1.5px solid rgba(217,119,6,.3);border-radius:12px;padding:10px 18px">
    <span style="width:8px;height:8px;background:#d97706;border-radius:50%;display:inline-block;animation:itr-pulse 1.6s infinite"></span>
    <span style="font-size:13px;font-weight:700;color:#d97706">{{ $countNew + $countPendingIT }} pending review</span>
  </div>
  @endif
</div>
<style>
@keyframes itr-pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(1.3)} }
</style>

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

{{-- Stat cards (3 cards) --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:22px" class="itr-admin-cols">
  <div class="itr-admin-stat">
    <div class="itr-admin-stat-icon" style="background:rgba(2,132,199,.1);color:#0284c7"><i class="bi bi-file-earmark-text-fill"></i></div>
    <div>
      <div class="itr-admin-stat-val">{{ $total }}</div>
      <div class="itr-admin-stat-lbl">Total Requests</div>
    </div>
  </div>
  <div class="itr-admin-stat">
    <div class="itr-admin-stat-icon" style="background:rgba(22,163,74,.1);color:#16a34a"><i class="bi bi-check-circle-fill"></i></div>
    <div>
      <div class="itr-admin-stat-val">{{ $countApproved }}</div>
      <div class="itr-admin-stat-lbl">Approved</div>
    </div>
  </div>
  <div class="itr-admin-stat">
    <div class="itr-admin-stat-icon" style="background:rgba(220,38,38,.1);color:#dc2626"><i class="bi bi-x-circle-fill"></i></div>
    <div>
      <div class="itr-admin-stat-val">{{ $countRejected }}</div>
      <div class="itr-admin-stat-lbl">Rejected</div>
    </div>
  </div>
</div>

{{-- Main list card --}}
<div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;overflow:hidden">

  {{-- Card header with integrated filters --}}
  <div style="padding:16px 20px;border-bottom:1px solid var(--border);background:var(--body-bg)">
    <form method="GET" action="{{ route('it.it-request-form') }}" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
      <div style="position:relative;flex:1;min-width:200px">
        <i class="bi bi-search" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:12px;pointer-events:none"></i>
        <input type="text" name="itr_search" value="{{ $search }}" placeholder="Search subject, name, department…"
          style="width:100%;padding:8px 12px 8px 32px;font-family:'Inter',sans-serif;font-size:13px;border:1.5px solid var(--border);border-radius:8px;background:var(--surface);color:var(--text);outline:none;box-sizing:border-box;transition:border-color .15s"
          onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
      </div>
      <select name="itr_type" onchange="this.form.submit()"
        style="font-family:'Inter',sans-serif;font-size:13px;padding:8px 30px 8px 12px;border:1.5px solid var(--border);border-radius:8px;background:var(--surface);color:{{ $type ? 'var(--text)' : 'var(--muted)' }};outline:none;cursor:pointer;appearance:none;background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E\");background-repeat:no-repeat;background-position:right 10px center">
        <option value="">All Types</option>
        <option value="hardware" {{ $type === 'hardware' ? 'selected' : '' }}>Hardware</option>
        <option value="software" {{ $type === 'software' ? 'selected' : '' }}>Software</option>
        <option value="system"   {{ $type === 'system'   ? 'selected' : '' }}>System</option>
        <option value="service"  {{ $type === 'service'  ? 'selected' : '' }}>Service</option>
      </select>
      <select name="itr_status" onchange="this.form.submit()"
        style="font-family:'Inter',sans-serif;font-size:13px;padding:8px 30px 8px 12px;border:1.5px solid var(--border);border-radius:8px;background:var(--surface);color:{{ $status ? 'var(--text)' : 'var(--muted)' }};outline:none;cursor:pointer;appearance:none;background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2020/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E\");background-repeat:no-repeat;background-position:right 10px center">
        <option value="">All Status</option>
        <option value="New"        {{ $status === 'New'        ? 'selected' : '' }}>Pending HOU</option>
        <option value="Pending IT"         {{ $status === 'Pending IT'         ? 'selected' : '' }}>Pending IT Approval</option>
        <option value="Pending Validation" {{ $status === 'Pending Validation' ? 'selected' : '' }}>Pending Validation</option>
        <option value="Approved"   {{ $status === 'Approved'   ? 'selected' : '' }}>Approved</option>
        <option value="Rejected"   {{ $status === 'Rejected'   ? 'selected' : '' }}>Rejected</option>
        <option value="Draft"      {{ $status === 'Draft'      ? 'selected' : '' }}>Draft</option>
      </select>
      <button type="submit"
        style="font-family:'Inter',sans-serif;font-size:13px;font-weight:600;padding:8px 16px;background:var(--accent);color:#fff;border:none;border-radius:8px;cursor:pointer;display:flex;align-items:center;gap:6px;transition:background .15s"
        onmouseover="this.style.background='var(--accent-h)'" onmouseout="this.style.background='var(--accent)'">
        <i class="bi bi-funnel-fill"></i> Filter
      </button>
      @if($search || $type || $status)
      <a href="{{ route('it.it-request-form') }}"
        style="font-size:12.5px;font-weight:600;color:var(--muted);text-decoration:none;display:inline-flex;align-items:center;gap:5px;padding:8px 4px;white-space:nowrap;transition:color .15s"
        onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--muted)'">
        <i class="bi bi-x-circle"></i> Clear filters
      </a>
      @endif
      <div style="margin-left:auto;font-size:12.5px;color:var(--muted);white-space:nowrap">
        <strong style="color:var(--text)">{{ number_format($forms->total()) }}</strong> result{{ $forms->total() !== 1 ? 's' : '' }}
        @if($search || $type || $status)<span style="color:var(--accent);margin-left:3px">(filtered)</span>@endif
      </div>
    </form>
  </div>

  {{-- Admin bulk bar --}}
  <div id="adminBulkBar" style="display:none;padding:10px 20px;background:rgba(2,132,199,.07);border-bottom:1px solid rgba(2,132,199,.2);align-items:center;gap:12px;flex-wrap:wrap">
    <span id="adminBulkCount" style="font-size:13px;font-weight:700;color:#0284c7;min-width:80px"></span>
    <button id="adminApproveBtn" onclick="openBulkModal('admin','approve')" style="font-family:'Inter',sans-serif;font-size:12.5px;font-weight:700;padding:7px 16px;background:#16a34a;color:#fff;border:none;border-radius:8px;cursor:pointer;display:inline-flex;align-items:center;gap:6px"><i class="bi bi-check-circle-fill"></i> Approve Selected</button>
    <button id="adminRejectBtn"  onclick="openBulkModal('admin','reject')"  style="font-family:'Inter',sans-serif;font-size:12.5px;font-weight:700;padding:7px 16px;background:#dc2626;color:#fff;border:none;border-radius:8px;cursor:pointer;display:inline-flex;align-items:center;gap:6px"><i class="bi bi-x-circle-fill"></i> Reject Selected</button>
    <button id="adminArchiveBtn" onclick="openBulkModal('admin','archive')" style="font-family:'Inter',sans-serif;font-size:12.5px;font-weight:700;padding:7px 16px;background:#d97706;color:#fff;border:none;border-radius:8px;cursor:pointer;display:none;align-items:center;gap:6px"><i class="bi bi-archive-fill"></i> Archive Selected</button>
    <button onclick="clearBulkSelection('admin')" style="font-family:'Inter',sans-serif;font-size:12px;font-weight:600;padding:7px 12px;background:transparent;border:1.5px solid var(--border);border-radius:8px;color:var(--muted);cursor:pointer">Clear</button>
  </div>

  {{-- Column headers --}}
  <div style="display:grid;grid-template-columns:40px 2fr 1fr 1fr 130px 120px;padding:10px 20px;background:var(--body-bg);border-bottom:1px solid var(--border);align-items:center">
    <div style="display:flex;align-items:center"><input type="checkbox" class="bulk-cb" id="adminSelectAll" title="Select all"></div>
    <div style="font-size:10.5px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em">Request</div>
    <div style="font-size:10.5px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em">Submitted By</div>
    <div style="font-size:10.5px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em">Department</div>
    <div style="font-size:10.5px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em">Date</div>
    <div style="font-size:10.5px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em">Status</div>
  </div>

  {{-- Rows --}}
  @forelse($forms as $form)
  @php
    $typeMap = [
      'hardware' => ['label'=>'Hardware','color'=>'#3b82f6','bg'=>'rgba(59,130,246,.1)','icon'=>'bi-laptop'],
      'software' => ['label'=>'Software','color'=>'#8b5cf6','bg'=>'rgba(139,92,246,.1)','icon'=>'bi-code-slash'],
      'system'   => ['label'=>'System',  'color'=>'#10b981','bg'=>'rgba(16,185,129,.1)', 'icon'=>'bi-hdd-network'],
      'service'  => ['label'=>'Service', 'color'=>'#0284c7','bg'=>'rgba(2,132,199,.1)',  'icon'=>'bi-wifi'],
    ];
    $t = $typeMap[$form->request_type] ?? ['label'=>ucfirst($form->request_type),'color'=>'#64748b','bg'=>'rgba(100,116,139,.1)','icon'=>'bi-question-circle'];
  @endphp
  @php $adminCbable = in_array($form->status, ['Pending IT', 'Approved', 'Rejected']); @endphp
  <a href="{{ route('it.it-request-form.show', $form->id) }}"
    class="itr-admin-row {{ $form->status === 'New' ? 'is-new' : ($form->status === 'Pending IT' ? 'is-pending-it' : ($form->status === 'Pending Validation' ? 'is-pending-validation' : '')) }}"
    style="grid-template-columns:40px 2fr 1fr 1fr 130px 120px;padding:14px 20px;gap:12px">

    {{-- Checkbox --}}
    <div style="display:flex;align-items:center" onclick="event.preventDefault();event.stopPropagation()">
      @if($adminCbable)
      <input type="checkbox" class="bulk-cb admin-cb" value="{{ $form->id }}" data-status="{{ $form->status }}" onchange="updateBulkBar('admin')">
      @endif
    </div>

    {{-- Request: subject + type badge + ID --}}
    <div style="min-width:0">
      <div style="display:flex;align-items:center;gap:8px;margin-bottom:5px;flex-wrap:wrap">
        <span class="itr-type-pill" style="background:{{ $t['bg'] }};color:{{ $t['color'] }}">
          <i class="bi {{ $t['icon'] }}" style="font-size:10px"></i>{{ $t['label'] }}
        </span>
        <span style="font-size:11px;color:var(--muted);font-weight:500">#{{ $form->id }}</span>
      </div>
      <div style="font-size:13.5px;font-weight:700;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $form->subject }}</div>
    </div>

    {{-- Submitted by --}}
    <div style="display:flex;align-items:center;gap:9px;min-width:0">
      <div style="width:30px;height:30px;border-radius:50%;background:rgba(2,132,199,.1);color:var(--accent);display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0"><i class="bi bi-person-fill"></i></div>
      <div style="min-width:0">
        <div style="font-size:13px;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $form->submittedBy?->full_name ?? '—' }}</div>
        <div style="font-size:11px;color:var(--muted);margin-top:1px">{{ $form->submittedBy?->getItRoleLabel() ?? '' }}</div>
      </div>
    </div>

    {{-- Department --}}
    <div style="font-size:13px;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;align-self:center">
      {{ $form->user_department ?: ($form->submittedBy?->dept_name ?? '—') }}
    </div>

    {{-- Date --}}
    <div style="align-self:center">
      <div style="font-size:13px;font-weight:500;color:var(--text)">{{ $form->created_at->format('d M Y') }}</div>
      <div style="font-size:11px;color:var(--muted);margin-top:1px">{{ $form->created_at->format('H:i') }}</div>
    </div>

    {{-- Status --}}
    <div style="align-self:center">
      @if($form->status === 'New')
      <span class="itr-status-dot" style="background:rgba(217,119,6,.1);color:#d97706">
        <span style="background:#d97706"></span>Pending HOU
      </span>
      @elseif($form->status === 'Pending IT')
      <span class="itr-status-dot" style="background:rgba(2,132,199,.1);color:#0284c7">
        <span style="background:#0284c7"></span>Pending IT
      </span>
      @elseif($form->status === 'Pending Validation')
      <span class="itr-status-dot" style="background:rgba(124,58,237,.1);color:#7c3aed">
        <span style="background:#7c3aed"></span>Pending Validation
      </span>
      @elseif($form->status === 'Approved')
      <span class="itr-status-dot" style="background:rgba(22,163,74,.1);color:#16a34a">
        <span style="background:#16a34a"></span>Approved
      </span>
      @elseif($form->status === 'Rejected')
      <span class="itr-status-dot" style="background:rgba(220,38,38,.1);color:#dc2626">
        <span style="background:#dc2626"></span>Rejected
      </span>
      @else
      <span class="itr-status-dot" style="background:rgba(100,116,139,.1);color:#64748b">
        <span style="background:#64748b"></span>Draft
      </span>
      @endif
    </div>

  </a>
  @empty
  <div style="padding:56px 20px;text-align:center;color:var(--muted)">
    <i class="bi bi-inbox" style="font-size:32px;display:block;margin-bottom:10px;opacity:.35"></i>
    <div style="font-size:14px;font-weight:600;color:var(--text);margin-bottom:4px">No requests found</div>
    <div style="font-size:13px">Try adjusting your search or filters.</div>
  </div>
  @endforelse

  @if($forms->hasPages())
  <div style="padding:16px 20px;border-top:1px solid var(--border)">
    {{ $forms->withQueryString()->links() }}
  </div>
  @endif
</div>

{{-- â•â• ARCHIVE SECTION â•â• --}}
<div style="margin-top:32px" id="admin-archive-section">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;flex-wrap:wrap;gap:8px">
    <div>
      <div style="font-family:'Inter',sans-serif;font-size:15px;font-weight:800;color:var(--text);margin-bottom:2px">
        <i class="bi bi-archive-fill" style="color:#d97706;margin-right:7px"></i>Archive
        @if($archivedForms->total())
        <span style="font-size:12px;font-weight:600;color:#d97706;background:rgba(217,119,6,.1);border:1px solid rgba(217,119,6,.25);border-radius:20px;padding:2px 10px;margin-left:8px">{{ $archivedForms->total() }}</span>
        @endif
      </div>
      <div style="font-size:12.5px;color:var(--muted)">Decided requests that have been moved out of the active inbox.</div>
    </div>
    <button onclick="toggleArchive()" id="archiveToggleBtn"
      style="font-family:'Inter',sans-serif;font-size:12.5px;font-weight:600;padding:7px 14px;background:transparent;border:1.5px solid var(--border);border-radius:9px;color:var(--muted);cursor:pointer;display:inline-flex;align-items:center;gap:6px;transition:all .15s"
      onmouseover="this.style.borderColor='#d97706';this.style.color='#d97706'" onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--muted)'">
      <i class="bi bi-chevron-down" id="archiveToggleIcon"></i> Show Archive
    </button>
  </div>

  <div id="archiveList" style="display:{{ request('archive_page') ? 'block' : 'none' }}">
    @if($archivedForms->total())
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;overflow:hidden">

      {{-- Headers --}}
      <div style="display:grid;grid-template-columns:2fr 1fr 1fr 130px 120px 80px;padding:10px 20px;background:var(--body-bg);border-bottom:1px solid var(--border);align-items:center">
        <div style="font-size:10.5px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em">Request</div>
        <div style="font-size:10.5px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em">Submitted By</div>
        <div style="font-size:10.5px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em">Department</div>
        <div style="font-size:10.5px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em">Date</div>
        <div style="font-size:10.5px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em">Status</div>
        <div style="font-size:10.5px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em">Action</div>
      </div>

      @foreach($archivedForms as $af)
      @php
        $afTypeMap = [
          'hardware' => ['label'=>'Hardware','color'=>'#3b82f6','bg'=>'rgba(59,130,246,.1)','icon'=>'bi-laptop'],
          'software' => ['label'=>'Software','color'=>'#8b5cf6','bg'=>'rgba(139,92,246,.1)','icon'=>'bi-code-slash'],
          'system'   => ['label'=>'System',  'color'=>'#10b981','bg'=>'rgba(16,185,129,.1)', 'icon'=>'bi-hdd-network'],
          'service'  => ['label'=>'Service', 'color'=>'#0284c7','bg'=>'rgba(2,132,199,.1)',  'icon'=>'bi-wifi'],
        ];
        $aft = $afTypeMap[$af->request_type] ?? ['label'=>ucfirst($af->request_type),'color'=>'#64748b','bg'=>'rgba(100,116,139,.1)','icon'=>'bi-question-circle'];
      @endphp
      <div style="display:grid;grid-template-columns:2fr 1fr 1fr 130px 120px 80px;padding:13px 20px;border-bottom:1px solid var(--border);align-items:center;gap:12px;opacity:.8">

        {{-- Request --}}
        <div style="min-width:0">
          <div style="display:flex;align-items:center;gap:7px;margin-bottom:4px;flex-wrap:wrap">
            <span style="display:inline-flex;align-items:center;gap:5px;background:{{ $aft['bg'] }};color:{{ $aft['color'] }};border-radius:20px;padding:2px 10px;font-size:11px;font-weight:700">
              <i class="bi {{ $aft['icon'] }}" style="font-size:10px"></i>{{ $aft['label'] }}
            </span>
            <span style="font-size:11px;color:var(--muted);font-weight:500">#{{ $af->id }}</span>
          </div>
          <a href="{{ route('it.it-request-form.show', $af->id) }}"
            style="font-size:13px;font-weight:700;color:var(--text);text-decoration:none;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block"
            onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--text)'">
            {{ $af->subject }}
          </a>
        </div>

        {{-- Submitted by --}}
        <div style="display:flex;align-items:center;gap:8px;min-width:0">
          <div style="width:28px;height:28px;border-radius:50%;background:rgba(100,116,139,.1);color:#64748b;display:flex;align-items:center;justify-content:center;font-size:12px;flex-shrink:0"><i class="bi bi-person-fill"></i></div>
          <div style="min-width:0">
            <div style="font-size:12.5px;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $af->submittedBy?->full_name ?? '—' }}</div>
          </div>
        </div>

        {{-- Department --}}
        <div style="font-size:12.5px;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
          {{ $af->user_department ?: ($af->submittedBy?->dept_name ?? '—') }}
        </div>

        {{-- Date --}}
        <div>
          <div style="font-size:12.5px;font-weight:500;color:var(--text)">{{ $af->created_at->format('d M Y') }}</div>
          <div style="font-size:11px;color:var(--muted);margin-top:1px">{{ $af->created_at->format('H:i') }}</div>
        </div>

        {{-- Status --}}
        <div>
          @if($af->status === 'Approved')
          <span class="itr-status-dot" style="background:rgba(22,163,74,.1);color:#16a34a"><span style="background:#16a34a"></span>Approved</span>
          @else
          <span class="itr-status-dot" style="background:rgba(220,38,38,.1);color:#dc2626"><span style="background:#dc2626"></span>Rejected</span>
          @endif
        </div>

        {{-- Unarchive --}}
        <div>
          <form method="POST" action="{{ route('it.it-request-form.unarchive', $af->id) }}" style="margin:0">
            @csrf
            <button type="submit"
              style="font-family:'Inter',sans-serif;font-size:11.5px;font-weight:600;padding:5px 10px;background:transparent;border:1.5px solid var(--border);border-radius:7px;color:var(--muted);cursor:pointer;display:inline-flex;align-items:center;gap:5px;transition:all .15s;white-space:nowrap"
              onmouseover="this.style.borderColor='var(--accent)';this.style.color='var(--accent)'" onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--muted)'"
              title="Move back to inbox">
              <i class="bi bi-arrow-counterclockwise"></i> Restore
            </button>
          </form>
        </div>

      </div>
      @endforeach

      @if($archivedForms->hasPages())
      <div style="padding:14px 20px;border-top:1px solid var(--border)">
        {{ $archivedForms->withQueryString()->links() }}
      </div>
      @endif

    </div>
    @else
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:36px 20px;text-align:center;color:var(--muted)">
      <i class="bi bi-archive" style="font-size:28px;display:block;margin-bottom:10px;opacity:.35"></i>
      <div style="font-size:13.5px;font-weight:600;color:var(--text);margin-bottom:4px">No archived requests</div>
      <div style="font-size:12.5px">Decided requests you archive will appear here.</div>
    </div>
    @endif
  </div>
</div>

{{-- Archive toggle JS --}}
<script>
function toggleArchive() {
  var list = document.getElementById('archiveList');
  var open = list.style.display !== 'none';
  list.style.display = open ? 'none' : 'block';
  document.getElementById('archiveToggleBtn').innerHTML = open
    ? '<i class="bi bi-chevron-down" id="archiveToggleIcon"></i> Show Archive'
    : '<i class="bi bi-chevron-up"   id="archiveToggleIcon"></i> Hide Archive';
}
// If archive_page param present, update button label to reflect expanded state
(function(){
  @if(request('archive_page'))
  var btn = document.getElementById('archiveToggleBtn');
  if (btn) btn.innerHTML = '<i class="bi bi-chevron-up" id="archiveToggleIcon"></i> Hide Archive';
  @endif
})();
</script>

{{-- Admin bulk modal + JS --}}
<div id="bulkModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:10000;align-items:center;justify-content:center">
  <div style="background:var(--surface);border-radius:14px;padding:28px 28px 24px;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,.25);margin:16px">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px">
      <div id="bulkModalIcon" style="width:40px;height:40px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0"></div>
      <div id="bulkModalTitle" style="font-family:'Inter',sans-serif;font-size:16px;font-weight:800;color:var(--text)"></div>
    </div>
    <div id="bulkModalDesc" style="font-size:13px;color:var(--muted);margin-bottom:16px"></div>
    <form id="bulkForm" method="POST">
      @csrf
      <div id="bulkIdsContainer"></div>
      <div id="bulkRemarksDiv" style="margin-bottom:16px">
        <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:6px">Remarks <span style="font-weight:400;color:var(--muted)">(optional — applies to all selected)</span></label>
        <textarea name="remarks" id="bulkRemarks" rows="3"
          style="width:100%;font-family:'Inter',sans-serif;font-size:13px;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;background:var(--surface);color:var(--text);outline:none;resize:vertical;box-sizing:border-box"
          onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'"
          placeholder="Add a remark (optional)…"></textarea>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end">
        <button type="button" onclick="closeBulkModal()" style="font-family:'Inter',sans-serif;font-size:13px;font-weight:600;padding:9px 18px;background:var(--body-bg);border:1.5px solid var(--border);border-radius:8px;color:var(--text);cursor:pointer">Cancel</button>
        <button type="submit" id="bulkModalSubmit" style="font-family:'Inter',sans-serif;font-size:13px;font-weight:700;padding:9px 22px;border:none;border-radius:8px;color:#fff;cursor:pointer"></button>
      </div>
    </form>
  </div>
</div>

<script>
var _bulkRoutes = {
  admin: {
    approve: '{{ route("it.it-request-form.bulk-admin-approve") }}',
    reject:  '{{ route("it.it-request-form.bulk-admin-reject") }}',
    archive: '{{ route("it.it-request-form.bulk-admin-archive") }}'
  }
};

function updateBulkBar(group) {
  var cbs    = Array.from(document.querySelectorAll('.' + group + '-cb:checked'));
  var bar    = document.getElementById(group === 'admin' ? 'adminBulkBar' : (group === 'hou' ? 'houBulkBar' : 'valBulkBar'));
  var countEl= document.getElementById(group === 'admin' ? 'adminBulkCount' : (group === 'hou' ? 'houBulkCount' : 'valBulkCount'));
  if (cbs.length > 0) {
    bar.style.display = 'flex';
    countEl.textContent = cbs.length + ' selected';
    if (group === 'admin') {
      var hasPending = cbs.some(function(cb){ return cb.dataset.status === 'Pending IT'; });
      var hasDecided = cbs.some(function(cb){ return cb.dataset.status === 'Approved' || cb.dataset.status === 'Rejected'; });
      document.getElementById('adminApproveBtn').style.display = hasPending ? 'inline-flex' : 'none';
      document.getElementById('adminRejectBtn').style.display  = hasPending ? 'inline-flex' : 'none';
      document.getElementById('adminArchiveBtn').style.display = hasDecided ? 'inline-flex' : 'none';
    }
  } else {
    bar.style.display = 'none';
  }
  var allCbs = document.querySelectorAll('.' + group + '-cb');
  var saEl = document.getElementById(group === 'admin' ? 'adminSelectAll' : (group === 'hou' ? 'houSelectAll' : 'valSelectAll'));
  if (saEl) saEl.checked = allCbs.length > 0 && cbs.length === allCbs.length;
}

function toggleSelectAll(group) {
  var saEl   = document.getElementById(group === 'admin' ? 'adminSelectAll' : (group === 'hou' ? 'houSelectAll' : 'valSelectAll'));
  var allCbs = document.querySelectorAll('.' + group + '-cb');
  allCbs.forEach(function(cb){ cb.checked = saEl.checked; });
  updateBulkBar(group);
}

function clearBulkSelection(group) {
  document.querySelectorAll('.' + group + '-cb').forEach(function(cb){ cb.checked = false; });
  var saEl = document.getElementById(group === 'admin' ? 'adminSelectAll' : (group === 'hou' ? 'houSelectAll' : 'valSelectAll'));
  if (saEl) saEl.checked = false;
  updateBulkBar(group);
}

function openBulkModal(group, action) {
  var allChecked = Array.from(document.querySelectorAll('.' + group + '-cb:checked'));
  var cbs;
  if (group === 'admin' && action === 'archive') {
    cbs = allChecked.filter(function(cb){ return cb.dataset.status === 'Approved' || cb.dataset.status === 'Rejected'; });
  } else if (group === 'admin' && (action === 'approve' || action === 'reject')) {
    cbs = allChecked.filter(function(cb){ return cb.dataset.status === 'Pending IT'; });
  } else {
    cbs = allChecked;
  }
  if (!cbs.length) return;
  var icon  = document.getElementById('bulkModalIcon');
  var title = document.getElementById('bulkModalTitle');
  var desc  = document.getElementById('bulkModalDesc');
  var sub   = document.getElementById('bulkModalSubmit');
  var form  = document.getElementById('bulkForm');
  var cont  = document.getElementById('bulkIdsContainer');
  var remarksDiv = document.getElementById('bulkRemarksDiv');
  document.getElementById('bulkRemarks').value = '';
  cont.innerHTML = '';
  cbs.forEach(function(cb){
    var inp = document.createElement('input');
    inp.type = 'hidden'; inp.name = 'ids[]'; inp.value = cb.value;
    cont.appendChild(inp);
  });
  var routeMap = _bulkRoutes[group] || {};
  form.action = routeMap[action] || '#';
  var n = cbs.length;
  if (action === 'approve') {
    remarksDiv.style.display = '';
    icon.style.background = 'rgba(22,163,74,.12)'; icon.style.color = '#16a34a';
    icon.innerHTML = '<i class="bi bi-check-circle-fill"></i>';
    title.textContent = 'Approve ' + n + ' Request' + (n > 1 ? 's' : '');
    desc.textContent  = 'This will approve all ' + n + ' selected request' + (n > 1 ? 's' : '') + ' at once. An optional remark applies to all.';
    sub.textContent = 'Approve All'; sub.style.background = '#16a34a';
  } else if (action === 'archive') {
    remarksDiv.style.display = 'none';
    icon.style.background = 'rgba(217,119,6,.12)'; icon.style.color = '#d97706';
    icon.innerHTML = '<i class="bi bi-archive-fill"></i>';
    title.textContent = 'Archive ' + n + ' Request' + (n > 1 ? 's' : '');
    desc.textContent  = 'Move ' + n + ' decided request' + (n > 1 ? 's' : '') + ' to the archive section. You can restore them at any time.';
    sub.textContent = 'Archive'; sub.style.background = '#d97706';
  } else {
    remarksDiv.style.display = '';
    icon.style.background = 'rgba(220,38,38,.12)'; icon.style.color = '#dc2626';
    icon.innerHTML = '<i class="bi bi-x-circle-fill"></i>';
    title.textContent = 'Reject ' + n + ' Request' + (n > 1 ? 's' : '');
    desc.textContent  = 'This will reject all ' + n + ' selected request' + (n > 1 ? 's' : '') + '. An optional remark applies to all.';
    sub.textContent = 'Reject All'; sub.style.background = '#dc2626';
  }
  document.getElementById('bulkModal').style.display = 'flex';
  if (action !== 'archive') setTimeout(function(){ document.getElementById('bulkRemarks').focus(); }, 80);
}

function closeBulkModal() { document.getElementById('bulkModal').style.display = 'none'; }
document.getElementById('bulkModal').addEventListener('click', function(e){ if (e.target === this) closeBulkModal(); });

// Wire up admin select-all
var adminSA = document.getElementById('adminSelectAll');
if (adminSA) adminSA.addEventListener('change', function(){ toggleSelectAll('admin'); });
</script>

@else
@if($user->isStaff())
{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     STAFF — READ-ONLY VIEW
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
<div style="margin-bottom:10px">
  <div style="font-family:'Inter',sans-serif;font-size:22px;font-weight:800;color:var(--text);letter-spacing:-.3px;margin-bottom:4px">IT Request Form</div>
  <div style="font-size:13px;color:var(--muted)">View IT requests that have been submitted on your behalf.</div>
</div>

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #86efac;border-radius:9px;padding:12px 16px;margin-bottom:16px;font-size:13px;color:#166534;display:flex;align-items:center;gap:8px">
  <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
</div>
@endif
@if(session('error'))
<div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:9px;padding:12px 16px;margin-bottom:16px;font-size:13px;color:#991b1b;display:flex;align-items:center;gap:8px">
  <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
</div>
@endif

@php
  $sfTypeMap = [
    'hardware' => ['label'=>'Hardware','color'=>'#3b82f6','bg'=>'rgba(59,130,246,.1)','icon'=>'bi-laptop'],
    'software' => ['label'=>'Software','color'=>'#8b5cf6','bg'=>'rgba(139,92,246,.1)','icon'=>'bi-code-slash'],
    'system'   => ['label'=>'System',  'color'=>'#10b981','bg'=>'rgba(16,185,129,.1)','icon'=>'bi-hdd-network'],
    'service'  => ['label'=>'Service', 'color'=>'#0284c7','bg'=>'rgba(2,132,199,.1)', 'icon'=>'bi-wifi'],
  ];
  $sfStatusMap = [
    'New'                => ['label'=>'Pending HOU Approval','color'=>'#d97706','bg'=>'rgba(217,119,6,.1)'],
    'Pending IT'         => ['label'=>'Pending IT Approval', 'color'=>'#0284c7','bg'=>'rgba(2,132,199,.1)'],
    'Pending Validation' => ['label'=>'Pending Validation',  'color'=>'#7c3aed','bg'=>'rgba(124,58,237,.1)'],
    'Approved'           => ['label'=>'Approved',            'color'=>'#16a34a','bg'=>'rgba(22,163,74,.1)'],
    'Rejected'           => ['label'=>'Rejected',            'color'=>'#dc2626','bg'=>'rgba(220,38,38,.1)'],
    'Draft'              => ['label'=>'Draft',               'color'=>'#64748b','bg'=>'rgba(100,116,139,.1)'],
  ];
@endphp

<div style="margin-top:24px">
  <div style="margin-bottom:14px">
    <div style="font-family:'Inter',sans-serif;font-size:15px;font-weight:800;color:var(--text);margin-bottom:2px">
      <i class="bi bi-clipboard2-check-fill" style="color:var(--accent);margin-right:7px"></i>My IT Requests
      @if(isset($staffForms) && $staffForms->count())
      <span style="font-size:12px;font-weight:600;color:var(--accent);background:rgba(2,132,199,.1);border:1px solid rgba(2,132,199,.25);border-radius:20px;padding:2px 10px;margin-left:8px">{{ $staffForms->count() }}</span>
      @endif
    </div>
    <div style="font-size:12.5px;color:var(--muted)">IT requests submitted on your behalf by your manager or department head.</div>
  </div>

  @if(isset($staffForms) && $staffForms->count())
  <div style="display:flex;flex-direction:column;gap:12px">
    @foreach($staffForms as $sf)
    @php
      $sft = $sfTypeMap[$sf->request_type] ?? ['label'=>ucfirst($sf->request_type),'color'=>'#64748b','bg'=>'rgba(100,116,139,.1)','icon'=>'bi-question-circle'];
      $sfs = $sfStatusMap[$sf->status] ?? ['label'=>$sf->status,'color'=>'#64748b','bg'=>'rgba(100,116,139,.1)'];
      $sfBorder = in_array($sf->status, ['Approved']) ? 'rgba(22,163,74,.35)' :
                  (in_array($sf->status, ['Rejected']) ? 'rgba(220,38,38,.35)' :
                  (in_array($sf->status, ['New','Pending IT','Pending Validation']) ? 'rgba(217,119,6,.35)' : 'var(--border)'));
    @endphp
    <div style="background:var(--surface);border:1.5px solid {{ $sfBorder }};border-radius:14px;overflow:hidden;transition:box-shadow .15s"
         onmouseover="this.style.boxShadow='0 4px 18px rgba(0,0,0,.07)'" onmouseout="this.style.boxShadow='none'">
      <div style="padding:14px 18px;display:flex;align-items:center;gap:12px;flex-wrap:wrap;border-bottom:1px solid var(--border)">
        <span style="display:inline-flex;align-items:center;gap:6px;background:{{ $sft['bg'] }};color:{{ $sft['color'] }};border-radius:20px;padding:3px 11px;font-size:11.5px;font-weight:700;flex-shrink:0">
          <i class="bi {{ $sft['icon'] }}" style="font-size:11px"></i>{{ $sft['label'] }}
        </span>
        <div style="flex:1;min-width:0">
          <div style="font-size:13.5px;font-weight:700;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
            {{ $sf->subject ?? 'Untitled Request' }}
          </div>
          <div style="font-size:11px;color:var(--muted);margin-top:2px">
            #{{ $sf->id }} &middot; Submitted {{ $sf->created_at->format('d M Y, H:i') }}
            @if($sf->submittedBy) &middot; By {{ $sf->submittedBy->full_name }} @endif
          </div>
        </div>
        <span style="display:inline-flex;align-items:center;gap:5px;background:{{ $sfs['bg'] }};color:{{ $sfs['color'] }};border-radius:20px;padding:3px 11px;font-size:11.5px;font-weight:700;flex-shrink:0">
          <span style="width:6px;height:6px;border-radius:50%;background:{{ $sfs['color'] }};display:inline-block"></span>{{ $sfs['label'] }}
        </span>
        <a href="{{ route('it.it-request-form.staff-show', $sf->id) }}"
          style="display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:700;color:var(--accent);border:1.5px solid var(--accent);border-radius:20px;padding:4px 14px;text-decoration:none;flex-shrink:0;background:transparent;transition:background .15s"
          onmouseover="this.style.background='rgba(2,132,199,.08)'" onmouseout="this.style.background='transparent'">
          <i class="bi bi-eye"></i> View
        </a>
      </div>
      {{-- Status tracker --}}
      @php
        $sfS1 = 'done';
        $sfS2 = in_array($sf->status, ['New','Pending IT','Pending Validation']) ? 'active' : ($sf->status === 'Draft' ? 'pending' : 'done');
        $sfS3 = in_array($sf->status, ['Approved','Rejected']) ? ($sf->status === 'Approved' ? 'approved' : 'rejected') : 'pending';
        if ($sf->status === 'Draft') { $sfS1 = 'pending'; $sfS2 = 'pending'; }
      @endphp
      <div style="padding:16px 20px">
        <div style="display:flex;align-items:flex-start;gap:0">
          @php $c1 = $sfS1==='done' ? '#16a34a' : '#cbd5e1'; $bg1 = $sfS1==='done' ? 'rgba(22,163,74,.12)' : 'rgba(203,213,225,.2)'; @endphp
          <div style="display:flex;flex-direction:column;align-items:center;gap:5px;min-width:80px">
            <div style="width:34px;height:34px;border-radius:50%;background:{{ $bg1 }};border:2px solid {{ $c1 }};display:flex;align-items:center;justify-content:center;font-size:15px;color:{{ $c1 }}">
              <i class="bi {{ $sfS1==='done' ? 'bi-check-lg' : 'bi-send-fill' }}"></i>
            </div>
            <div style="font-size:10.5px;font-weight:700;color:{{ $c1 }};text-align:center;line-height:1.3">Submitted</div>
          </div>
          @php $line1 = $sfS1==='done' ? '#16a34a' : '#e2e8f0'; @endphp
          <div style="flex:1;height:2px;background:{{ $line1 }};margin-top:16px;border-radius:2px"></div>
          @php
            $c2 = $sfS2==='active' ? '#d97706' : ($sfS2==='done' ? '#16a34a' : '#cbd5e1');
            $bg2 = $sfS2==='active' ? 'rgba(217,119,6,.12)' : ($sfS2==='done' ? 'rgba(22,163,74,.12)' : 'rgba(203,213,225,.2)');
            $i2  = $sfS2==='active' ? 'bi-hourglass-split' : ($sfS2==='done' ? 'bi-check-lg' : 'bi-hourglass');
          @endphp
          <div style="display:flex;flex-direction:column;align-items:center;gap:5px;min-width:80px">
            <div style="width:34px;height:34px;border-radius:50%;background:{{ $bg2 }};border:2px solid {{ $c2 }};display:flex;align-items:center;justify-content:center;font-size:14px;color:{{ $c2 }}">
              <i class="bi {{ $i2 }}"></i>
            </div>
            <div style="font-size:10.5px;font-weight:700;color:{{ $c2 }};text-align:center;line-height:1.3">Under Review</div>
          </div>
          @php $line2 = ($sfS3==='approved' || $sfS3==='rejected') ? '#16a34a' : '#e2e8f0'; @endphp
          <div style="flex:1;height:2px;background:{{ $line2 }};margin-top:16px;border-radius:2px"></div>
          @php
            $c3 = $sfS3==='approved' ? '#16a34a' : ($sfS3==='rejected' ? '#dc2626' : '#cbd5e1');
            $bg3 = $sfS3==='approved' ? 'rgba(22,163,74,.12)' : ($sfS3==='rejected' ? 'rgba(220,38,38,.12)' : 'rgba(203,213,225,.2)');
            $i3  = $sfS3==='approved' ? 'bi-check-circle-fill' : ($sfS3==='rejected' ? 'bi-x-circle-fill' : 'bi-circle');
          @endphp
          <div style="display:flex;flex-direction:column;align-items:center;gap:5px;min-width:80px">
            <div style="width:34px;height:34px;border-radius:50%;background:{{ $bg3 }};border:2px solid {{ $c3 }};display:flex;align-items:center;justify-content:center;font-size:14px;color:{{ $c3 }}">
              <i class="bi {{ $i3 }}"></i>
            </div>
            <div style="font-size:10.5px;font-weight:700;color:{{ $c3 }};text-align:center;line-height:1.3">Decision</div>
          </div>
        </div>
      </div>
    </div>
    @endforeach
  </div>
  @else
  <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:48px 20px;text-align:center;color:var(--muted)">
    <i class="bi bi-inbox" style="font-size:32px;display:block;margin-bottom:12px;opacity:.35"></i>
    <div style="font-size:14px;font-weight:600;color:var(--text);margin-bottom:5px">No IT requests yet</div>
    <div style="font-size:13px">When your manager submits an IT request on your behalf, it will appear here.</div>
  </div>
  @endif
</div>
@else
{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     NON-ADMIN — EXISTING 2-STEP WIZARD
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
<style>
/* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
   IT REQUEST FORM — WIZARD REDESIGN
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */
.itr-wrap { max-width: 100%; }

.itr-page-title {
  font-family:'Inter',sans-serif;
  font-size: 22px; font-weight: 800;
  color: var(--text); letter-spacing: -.3px; margin-bottom: 4px;
}
.itr-page-sub { font-size: 13px; color: var(--muted); margin-bottom: 24px; }

/* ── Progress ── */
.itr-progress { display: flex; align-items: center; gap: 0; margin-bottom: 28px; }
.itr-step { display: flex; align-items: center; gap: 8px; font-size: 12.5px; font-weight: 600; color: var(--muted); }
.itr-step-num {
  width: 26px; height: 26px; border-radius: 50%;
  border: 2px solid var(--border);
  display: flex; align-items: center; justify-content: center;
  font-size: 11px; font-weight: 700;
  background: var(--surface); color: var(--muted); transition: all .2s;
}
.itr-step.done .itr-step-num { background: var(--accent); border-color: var(--accent); color: white; }
.itr-step.active .itr-step-num { background: var(--accent); border-color: var(--accent); color: white; box-shadow: 0 0 0 4px rgba(2,132,199,.2); }
.itr-step.active { color: var(--accent); }
.itr-step.done { color: var(--text); }
.itr-step-line { flex: 1; height: 2px; background: var(--border); margin: 0 10px; border-radius: 2px; transition: background .3s; }
.itr-step-line.done { background: var(--accent); }

/* ── Step 1 hardware intro ── */
.itr-hw-intro { background:var(--surface); border:2px solid var(--border); border-radius:18px; overflow:hidden; }
.itr-hw-intro-banner { background:linear-gradient(135deg,rgba(59,130,246,.12) 0%,rgba(2,132,199,.08) 100%); border-bottom:1px solid var(--border); padding:36px 40px; display:flex; align-items:center; gap:28px; }
.itr-hw-intro-icon { width:72px; height:72px; border-radius:18px; background:rgba(59,130,246,.15); color:#3b82f6; display:flex; align-items:center; justify-content:center; font-size:34px; flex-shrink:0; }
.itr-hw-intro-title { font-family:'Inter',sans-serif; font-size:22px; font-weight:800; color:var(--text); margin-bottom:6px; }
.itr-hw-intro-sub { font-size:13.5px; color:var(--muted); line-height:1.6; }
.itr-hw-items-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:0; }
.itr-hw-item { display:flex; align-items:center; gap:12px; padding:16px 20px; border-top:1px solid var(--border); }
.itr-hw-item:nth-child(3n+2) { border-left:1px solid var(--border); border-right:1px solid var(--border); }
.itr-hw-item-icon { width:36px; height:36px; border-radius:10px; background:rgba(59,130,246,.1); color:#3b82f6; display:flex; align-items:center; justify-content:center; font-size:17px; flex-shrink:0; }
.itr-hw-item-name { font-size:13px; font-weight:600; color:var(--text); }
.itr-hw-item-desc { font-size:11.5px; color:var(--muted); margin-top:1px; }
.itr-hw-cta { padding:24px 40px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; background:var(--body-bg); border-top:1px solid var(--border); }
.itr-hw-cta-note { font-size:12.5px; color:var(--muted); }
.itr-hw-btn { display:inline-flex; align-items:center; gap:8px; padding:11px 28px; background:var(--accent); color:#fff; border:none; border-radius:10px; font-family:'Inter',sans-serif; font-size:14px; font-weight:700; cursor:pointer; transition:all .15s; }
.itr-hw-btn:hover { background:#0272b1; box-shadow:0 4px 16px rgba(2,132,199,.35); transform:translateY(-1px); }
.itr-locked-note { font-size: 12px; color: var(--muted); text-align: center; margin-top: 6px; min-height: 18px; }

/* ── Step 2 ── */
#step2 { display: none; }
.itr-selected-banner {
  display: flex; align-items: center; gap: 12px;
  background: var(--surface); border: 1.5px solid var(--accent);
  border-radius: 12px; padding: 14px 18px; margin-bottom: 20px;
}
.itr-selected-pill {
  display: flex; align-items: center; gap: 8px;
  background: rgba(2,132,199,.1); color: var(--accent);
  font-size: 13px; font-weight: 700; padding: 5px 14px; border-radius: 20px;
}
.itr-selected-pill i { font-size: 15px; }
.itr-banner-text { font-size: 13px; color: var(--muted); flex: 1; }
.itr-change-btn {
  display: flex; align-items: center; gap: 6px;
  font-family:'Inter',sans-serif;
  font-size: 12.5px; font-weight: 600; color: var(--red);
  background: rgba(239,68,68,.07); border: 1.5px solid rgba(239,68,68,.2);
  border-radius: 8px; padding: 7px 14px; cursor: pointer; transition: all .15s;
}
.itr-change-btn:hover { background: rgba(239,68,68,.14); }

.itr-subject-row { display: grid; grid-template-columns: 1fr 160px; gap: 14px; margin-bottom: 20px; }

/* ── Section cards ── */
.itr-section { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; overflow: hidden; margin-bottom: 14px; }
.itr-section-head { display: flex; align-items: center; gap: 10px; padding: 14px 20px; border-bottom: 1px solid var(--border); background: var(--body-bg); }
.itr-section-num { width: 24px; height: 24px; border-radius: 50%; background: rgba(2,132,199,.12); color: var(--accent); font-size: 11px; font-weight: 800; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.itr-section-title { font-family:'Inter',sans-serif; font-size: 13.5px; font-weight: 700; color: var(--text); flex: 1; }
.itr-section-body { padding: 20px; }

/* ── Fields ── */
.itr-label { font-size: 12px; font-weight: 600; color: var(--text); margin-bottom: 6px; display: flex; align-items: center; gap: 3px; }
.itr-req { color: var(--red); }
.itr-hint { font-size: 11px; color: var(--muted); margin-top: 4px; }
.itr-input { width: 100%; font-family:'Inter',sans-serif; font-size: 13px; color: var(--text); background: var(--surface); border: 1.5px solid var(--border); border-radius: 8px; padding: 9px 12px; outline: none; transition: border-color .15s, box-shadow .15s; }
.itr-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(2,132,199,.12); }
.itr-input::placeholder { color: var(--muted); opacity: .6; }
textarea.itr-input { resize: vertical; min-height: 100px; }
select.itr-input { appearance: none; cursor: pointer; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 10px center; padding-right: 32px; }
.itr-input[readonly] { background: var(--body-bg); color: var(--muted); cursor: default; }

/* ── Grid ── */
.g2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.g3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; }
.g4 { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 12px; }
.fg { margin-bottom: 14px; }
.fg:last-child { margin-bottom: 0; }

/* ── Radio pills ── */
.itr-radio-group { display: flex; flex-wrap: wrap; gap: 8px; }
.itr-radio-pill { display: flex; align-items: center; gap: 7px; padding: 7px 14px; border: 1.5px solid var(--border); border-radius: 20px; cursor: pointer; font-size: 12.5px; font-weight: 500; color: var(--text); background: var(--surface); transition: all .15s; user-select: none; }
.itr-radio-pill input { display: none; }
.itr-radio-pill:hover { border-color: var(--accent); color: var(--accent); }
.itr-radio-pill.checked { border-color: var(--accent); background: rgba(2,132,199,.08); color: var(--accent); font-weight: 600; }

/* ── Check chips ── */
.itr-check-grid { display: flex; flex-wrap: wrap; gap: 8px; }
.itr-check-chip { display: flex; align-items: center; gap: 6px; padding: 6px 13px; border: 1.5px solid var(--border); border-radius: 20px; cursor: pointer; font-size: 12.5px; color: var(--text); background: var(--surface); transition: all .15s; user-select: none; }
.itr-check-chip input { display: none; }
.itr-check-chip:hover { border-color: var(--accent); }
.itr-check-chip.checked { border-color: var(--accent); background: rgba(2,132,199,.08); color: var(--accent); font-weight: 600; }
.chip-dot { width: 7px; height: 7px; border-radius: 50%; border: 1.5px solid currentColor; transition: all .15s; }
.itr-check-chip.checked .chip-dot { background: var(--accent); border-color: var(--accent); }

/* ── Upload ── */
.itr-upload-zone { border: 2px dashed var(--border); border-radius: 10px; padding: 18px 20px; transition: border-color .15s; }
.itr-upload-zone:hover { border-color: var(--accent); }
.itr-notice { display: flex; align-items: flex-start; gap: 9px; font-size: 12px; line-height: 1.6; padding: 9px 12px; border-radius: 7px; margin-bottom: 8px; }
.itr-notice.warn { background: #fffbeb; border: 1px solid #fcd34d; color: #78350f; }
.itr-notice.warn strong { color: var(--red); }
.itr-notice.info { background: #f0f9ff; border: 1px solid #bae6fd; color: #0c4a6e; }
.itr-notice i { font-size: 14px; flex-shrink: 0; margin-top: 1px; }
.itr-upload-row { display: flex; align-items: center; gap: 10px; margin-top: 10px; }
.itr-upload-row input[type="file"] { display: none; }
.itr-filename { flex: 1; font-size: 12.5px; color: var(--muted); background: var(--body-bg); border: 1.5px solid var(--border); border-radius: 7px; padding: 8px 12px; }
.itr-browse-btn { font-family:'Inter',sans-serif; font-size: 12.5px; font-weight: 600; padding: 8px 18px; background: var(--body-bg); border: 1.5px solid var(--border); border-radius: 7px; color: var(--text); cursor: pointer; transition: all .15s; }
.itr-browse-btn:hover { border-color: var(--accent); color: var(--accent); }

.itr-divider { border: none; border-top: 1px solid var(--border); margin: 18px 0; }

/* ── Inline field errors ── */
.itr-field-error { color: #dc2626; font-size: 11.5px; margin-top: 4px; display: flex; align-items: center; gap: 4px; }
.itr-field-error i { font-size: 12px; flex-shrink: 0; }
.itr-input.is-error { border-color: #dc2626 !important; box-shadow: 0 0 0 3px rgba(220,38,38,.1) !important; }

/* ── Name Searchable Dropdown ── */
.itr-name-dd { position:relative; }
.itr-name-trigger { display:flex;align-items:center;justify-content:space-between;border:1.5px solid var(--border);border-radius:8px;padding:9px 12px;cursor:pointer;background:var(--surface);font-size:13px;transition:border-color .15s,box-shadow .15s;min-height:40px;user-select:none; }
.itr-name-trigger:hover { border-color:var(--accent); }
.itr-name-trigger.is-open { border-color:var(--accent);box-shadow:0 0 0 3px rgba(2,132,199,.12); }
.itr-name-display { color:var(--text);flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap; }
.itr-name-display.is-placeholder { color:var(--muted);opacity:.6; }
.itr-name-arrow { color:var(--muted);font-size:11px;flex-shrink:0;margin-left:8px;transition:transform .2s; }
.itr-name-trigger.is-open .itr-name-arrow { transform:rotate(180deg); }
.itr-name-panel { position:fixed;z-index:9999;background:#fff;border:1.5px solid #0284c7;border-radius:8px;box-shadow:0 6px 24px rgba(0,0,0,.15);overflow:hidden; }
.itr-name-search-input { width:100%;border:none;border-bottom:1px solid #e2e8f0;padding:9px 12px;font-size:13px;outline:none;box-sizing:border-box;font-family:'Inter',sans-serif; }
.itr-name-list { max-height:220px;overflow-y:auto; }
.itr-name-item { padding:10px 14px;cursor:pointer;font-size:13px;border-bottom:1px solid #f1f5f9; }
.itr-name-item:hover { background:#f0f9ff; }
.itr-name-item-name { font-weight:600;color:#0f172a; }
.itr-name-item-dept { font-size:11px;color:#64748b;margin-top:2px; }
.itr-name-noresult { padding:12px 14px;font-size:13px;color:#64748b; }

/* ── Actions ── */
.itr-action-bar { display: flex; gap: 10px; margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border); }
.itr-btn-submit { font-family:'Inter',sans-serif; font-size: 13.5px; font-weight: 700; padding: 11px 28px; background: var(--accent); color: white; border: none; border-radius: 9px; cursor: pointer; transition: background .15s; display: flex; align-items: center; gap: 7px; }
.itr-btn-submit:hover { background: var(--accent-h); }
.itr-btn-draft { font-family:'Inter',sans-serif; font-size: 13px; font-weight: 600; padding: 11px 22px; background: var(--body-bg); border: 1.5px solid var(--border); border-radius: 9px; color: var(--text); cursor: pointer; transition: all .15s; display: flex; align-items: center; gap: 7px; }
.itr-btn-draft:hover { border-color: var(--accent); color: var(--accent); }

/* ── Paired-row form grid ── */
.itr-hw-layout { display:grid;grid-template-columns:1fr 1fr;gap:14px;align-items:start; }
.itr-hw-layout > .itr-section { margin-bottom:0; }
.itr-hw-full { grid-column:1 / -1; }
/* ── Doc upload zone ── */
.itr-doc-zone { display:flex;align-items:center;gap:12px;padding:13px 16px;background:var(--body-bg);border:1.5px dashed var(--border);border-radius:9px;transition:border-color .15s; }
.itr-doc-zone:hover { border-color:var(--accent); }
/* ── Section head hint ── */
.itr-section-hint { margin-left:auto;font-size:11.5px;color:var(--muted);font-weight:500; }

@media (max-width: 900px)  { .itr-hw-layout { grid-template-columns:1fr; } }
@media (max-width: 720px)  { .g2,.g3,.g4,.itr-subject-row { grid-template-columns:1fr; } }
</style>

<div class="itr-wrap">
  <div class="itr-page-title">IT Request Form</div>
  <div class="itr-page-sub">Submit a new IT service request in just two steps.</div>


  <!-- Progress -->
  <div class="itr-progress">
    <div class="itr-step active" id="prog-step1"><div class="itr-step-num">1</div> Choose Request Type</div>
    <div class="itr-step-line" id="prog-line"></div>
    <div class="itr-step" id="prog-step2"><div class="itr-step-num">2</div> Fill in Details</div>
  </div>

  <!-- â•â• STEP 1 â•â• -->
  <div id="step1">
    <div class="itr-hw-intro">
      <div class="itr-hw-intro-banner">
        <div class="itr-hw-intro-icon"><i class="bi bi-laptop"></i></div>
        <div>
          <div class="itr-hw-intro-title">Hardware Request</div>
          <div class="itr-hw-intro-sub">Submit a request for IT hardware equipment. All requests are reviewed and approved by IT Administration before processing.</div>
        </div>
      </div>
      <div class="itr-hw-items-grid">
        <div class="itr-hw-item">
          <div class="itr-hw-item-icon"><i class="bi bi-laptop"></i></div>
          <div><div class="itr-hw-item-name">Laptop / Desktop</div><div class="itr-hw-item-desc">Workstations &amp; portables</div></div>
        </div>
        <div class="itr-hw-item">
          <div class="itr-hw-item-icon"><i class="bi bi-printer"></i></div>
          <div><div class="itr-hw-item-name">Printer</div><div class="itr-hw-item-desc">Office printing devices</div></div>
        </div>
        <div class="itr-hw-item">
          <div class="itr-hw-item-icon"><i class="bi bi-phone"></i></div>
          <div><div class="itr-hw-item-name">Mobile &amp; IP Phone</div><div class="itr-hw-item-desc">Handphones &amp; desk phones</div></div>
        </div>
        <div class="itr-hw-item">
          <div class="itr-hw-item-icon"><i class="bi bi-tablet"></i></div>
          <div><div class="itr-hw-item-name">Tablet</div><div class="itr-hw-item-desc">Portable tablet devices</div></div>
        </div>
        <div class="itr-hw-item">
          <div class="itr-hw-item-icon"><i class="bi bi-hdd-network"></i></div>
          <div><div class="itr-hw-item-name">Network Equipment</div><div class="itr-hw-item-desc">Switch, hub &amp; UPS units</div></div>
        </div>
        <div class="itr-hw-item">
          <div class="itr-hw-item-icon"><i class="bi bi-three-dots"></i></div>
          <div><div class="itr-hw-item-name">Other</div><div class="itr-hw-item-desc">Peripherals &amp; accessories</div></div>
        </div>
      </div>
      <div class="itr-hw-cta">
        <div class="itr-hw-cta-note"><i class="bi bi-info-circle" style="margin-right:5px"></i>Ensure your department head approves this request before submission.</div>
        <button type="button" class="itr-hw-btn" onclick="selectType('hardware')">
          <i class="bi bi-arrow-right-circle-fill"></i> Begin Request
        </button>
      </div>
    </div>
  </div>

  @php
    $ongoingForms = isset($myForms) ? $myForms->whereIn('status', ['Draft','New','Pending IT','Pending Validation'])->values() : collect();
    $decidedForms = isset($myForms) ? $myForms->whereIn('status', ['Approved','Rejected'])->values() : collect();

    // Shared card renderer macro — defined as a PHP closure to avoid duplication
    $renderCard = function($mf) {
      $mfTypeMap = [
        'hardware' => ['label'=>'Hardware','color'=>'#3b82f6','bg'=>'rgba(59,130,246,.1)','icon'=>'bi-laptop'],
        'software' => ['label'=>'Software','color'=>'#8b5cf6','bg'=>'rgba(139,92,246,.1)','icon'=>'bi-code-slash'],
        'system'   => ['label'=>'System',  'color'=>'#10b981','bg'=>'rgba(16,185,129,.1)','icon'=>'bi-hdd-network'],
        'service'  => ['label'=>'Service', 'color'=>'#0284c7','bg'=>'rgba(2,132,199,.1)', 'icon'=>'bi-wifi'],
      ];
      return $mfTypeMap[$mf->request_type] ?? ['label'=>ucfirst($mf->request_type),'color'=>'#64748b','bg'=>'rgba(100,116,139,.1)','icon'=>'bi-question-circle'];
    };
  @endphp

  {{-- â•â• SECTION 1: ACTIVE REQUESTS â•â• --}}
  <div style="margin-top:32px" id="my-submissions">
    <div style="margin-bottom:14px">
      <div style="font-family:'Inter',sans-serif;font-size:15px;font-weight:800;color:var(--text);margin-bottom:2px">
        <i class="bi bi-clipboard2-pulse-fill" style="color:var(--accent);margin-right:7px"></i>Active Requests
        @if($ongoingForms->count())
        <span style="font-size:12px;font-weight:600;color:var(--accent);background:rgba(2,132,199,.1);border:1px solid rgba(2,132,199,.25);border-radius:20px;padding:2px 10px;margin-left:8px">{{ $ongoingForms->count() }}</span>
        @endif
      </div>
      <div style="font-size:12.5px;color:var(--muted)">Your drafts and requests currently under review.</div>
    </div>

    @if($ongoingForms->count())
    <div style="display:flex;flex-direction:column;gap:12px">
      @foreach($ongoingForms as $mf)
      @php
        $mt = $renderCard($mf);
        $isDraft    = $mf->status === 'Draft';
        $isNew      = $mf->status === 'New';
        $isApproved = false;
        $isRejected = false;
        if ($isDraft)      { $s1='pending'; $s2='pending'; $s3='pending'; $borderColor='var(--border)'; }
        elseif ($isNew)    { $s1='done'; $s2='active'; $s3='pending'; $borderColor='rgba(217,119,6,.4)'; }
        else               { $s1='done'; $s2='active'; $s3='pending'; $borderColor='rgba(217,119,6,.4)'; }
      @endphp
      <div style="background:var(--surface);border:1.5px solid {{ $borderColor }};border-radius:14px;overflow:hidden;transition:box-shadow .15s"
           onmouseover="this.style.boxShadow='0 4px 18px rgba(0,0,0,.07)'" onmouseout="this.style.boxShadow='none'">
        {{-- Card header --}}
        <div style="padding:14px 18px;display:flex;align-items:center;gap:12px;flex-wrap:wrap;border-bottom:1px solid var(--border)">
          <span style="display:inline-flex;align-items:center;gap:6px;background:{{ $mt['bg'] }};color:{{ $mt['color'] }};border-radius:20px;padding:3px 11px;font-size:11.5px;font-weight:700;flex-shrink:0">
            <i class="bi {{ $mt['icon'] }}" style="font-size:11px"></i>{{ $mt['label'] }}
          </span>
          <div style="flex:1;min-width:0">
            <div style="font-size:13.5px;font-weight:700;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
              {{ $mf->subject ?? 'Untitled Draft' }}
            </div>
            <div style="font-size:11px;color:var(--muted);margin-top:2px">
              #{{ $mf->id }} &middot; {{ $isDraft ? 'Saved' : 'Submitted' }} {{ $mf->created_at->format('d M Y, H:i') }}
            </div>
          </div>
          @if($isDraft)
          <a href="{{ route('it.it-request-form.edit', $mf->id) }}"
            style="display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:700;color:var(--accent);border:1.5px solid var(--accent);border-radius:20px;padding:4px 12px;text-decoration:none;flex-shrink:0;background:transparent;transition:background .15s"
            onmouseover="this.style.background='rgba(2,132,199,.08)'" onmouseout="this.style.background='transparent'">
            <i class="bi bi-pencil-square"></i> Resume Draft
          </a>
          <form method="POST" action="{{ route('it.it-request-form.draft.destroy', $mf->id) }}"
            onsubmit="return confirm('Delete this draft? This cannot be undone.')" style="display:inline;margin:0">
            @csrf @method('DELETE')
            <button type="submit"
              style="display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:700;color:#dc2626;border:1.5px solid rgba(220,38,38,.35);border-radius:20px;padding:4px 12px;background:transparent;cursor:pointer;font-family:inherit;transition:background .15s"
              onmouseover="this.style.background='rgba(220,38,38,.07)'" onmouseout="this.style.background='transparent'">
              <i class="bi bi-trash3"></i> Delete
            </button>
          </form>
          @endif
        </div>
        {{-- Status tracker --}}
        <div style="padding:16px 20px">
          <div style="display:flex;align-items:flex-start;gap:0">
            @php
              $c1 = $s1==='done' ? '#16a34a' : '#cbd5e1';
              $bg1 = $s1==='done' ? 'rgba(22,163,74,.12)' : 'rgba(203,213,225,.2)';
            @endphp
            <div style="display:flex;flex-direction:column;align-items:center;gap:5px;min-width:80px">
              <div style="width:34px;height:34px;border-radius:50%;background:{{ $bg1 }};border:2px solid {{ $c1 }};display:flex;align-items:center;justify-content:center;font-size:15px;color:{{ $c1 }}">
                <i class="bi {{ $s1==='done' ? 'bi-check-lg' : 'bi-send-fill' }}"></i>
              </div>
              <div style="font-size:10.5px;font-weight:700;color:{{ $c1 }};text-align:center;line-height:1.3">Submitted</div>
            </div>
            @php $line1 = ($s1==='done') ? '#16a34a' : '#e2e8f0'; @endphp
            <div style="flex:1;height:2px;background:{{ $line1 }};margin-top:16px;border-radius:2px"></div>
            @php
              $c2 = $s2==='active' ? '#d97706' : '#cbd5e1';
              $bg2 = $s2==='active' ? 'rgba(217,119,6,.12)' : 'rgba(203,213,225,.2)';
              $i2  = $s2==='active' ? 'bi-hourglass-split' : 'bi-hourglass';
            @endphp
            <div style="display:flex;flex-direction:column;align-items:center;gap:5px;min-width:80px">
              <div style="width:34px;height:34px;border-radius:50%;background:{{ $bg2 }};border:2px solid {{ $c2 }};display:flex;align-items:center;justify-content:center;font-size:14px;color:{{ $c2 }}">
                <i class="bi {{ $i2 }}"></i>
              </div>
              <div style="font-size:10.5px;font-weight:700;color:{{ $c2 }};text-align:center;line-height:1.3">Under Review</div>
            </div>
            <div style="flex:1;height:2px;background:#e2e8f0;margin-top:16px;border-radius:2px"></div>
            <div style="display:flex;flex-direction:column;align-items:center;gap:5px;min-width:80px">
              <div style="width:34px;height:34px;border-radius:50%;background:rgba(203,213,225,.2);border:2px solid #cbd5e1;display:flex;align-items:center;justify-content:center;font-size:14px;color:#cbd5e1">
                <i class="bi bi-circle"></i>
              </div>
              <div style="font-size:10.5px;font-weight:700;color:#cbd5e1;text-align:center;line-height:1.3">Decision</div>
            </div>
          </div>
        </div>
      </div>
      @endforeach
    </div>
    @else
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:36px 20px;text-align:center;color:var(--muted)">
      <i class="bi bi-inbox" style="font-size:28px;display:block;margin-bottom:10px;opacity:.35"></i>
      <div style="font-size:13.5px;font-weight:600;color:var(--text);margin-bottom:4px">No active requests</div>
      <div style="font-size:12.5px">Drafts and requests under review will appear here.</div>
    </div>
    @endif
  </div>

  {{-- â•â• SECTION 2: DECIDED REQUESTS â•â• --}}
  <div style="margin-top:28px" id="my-decided">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;flex-wrap:wrap;gap:8px">
      <div>
        <div style="font-family:'Inter',sans-serif;font-size:15px;font-weight:800;color:var(--text);margin-bottom:2px">
          <i class="bi bi-check2-circle" style="color:#16a34a;margin-right:7px"></i>Decided Requests
          @if($decidedForms->count())
          <span style="font-size:12px;font-weight:600;color:#64748b;background:rgba(100,116,139,.1);border:1px solid rgba(100,116,139,.2);border-radius:20px;padding:2px 10px;margin-left:8px">{{ $decidedForms->count() }}</span>
          @endif
        </div>
        <div style="font-size:12.5px;color:var(--muted)">Requests that have been approved or rejected.</div>
      </div>
      <div style="display:flex;align-items:center;gap:8px">
        @if($decidedForms->count())
        <form method="POST" action="{{ route('it.it-request-form.clear-all') }}"
          onsubmit="return confirm('Clear all decided requests (Approved / Rejected) from your list?')" style="margin:0">
          @csrf
          <button type="submit"
            style="display:inline-flex;align-items:center;gap:6px;font-size:12.5px;font-weight:600;color:#64748b;border:1.5px solid rgba(100,116,139,.3);border-radius:9px;padding:7px 14px;background:transparent;cursor:pointer;font-family:'Inter',sans-serif;transition:all .15s"
            onmouseover="this.style.borderColor='rgba(100,116,139,.6)';this.style.color='#334155'" onmouseout="this.style.borderColor='rgba(100,116,139,.3)';this.style.color='#64748b'">
            <i class="bi bi-x-circle"></i> Clear All
          </button>
        </form>
        <button onclick="toggleDecided()" id="decidedToggleBtn"
          style="font-family:'Inter',sans-serif;font-size:12.5px;font-weight:600;padding:7px 14px;background:transparent;border:1.5px solid var(--border);border-radius:9px;color:var(--muted);cursor:pointer;display:inline-flex;align-items:center;gap:6px;transition:all .15s"
          onmouseover="this.style.borderColor='var(--accent)';this.style.color='var(--accent)'" onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--muted)'">
          <i class="bi bi-chevron-down" id="decidedToggleIcon"></i> Show
        </button>
        @endif
      </div>
    </div>

    <div id="decidedList" style="display:none">
      @if($decidedForms->count())
      <div style="display:flex;flex-direction:column;gap:12px">
        @foreach($decidedForms as $mf)
        @php
          $mt = $renderCard($mf);
          $isApproved = $mf->status === 'Approved';
          $isRejected = $mf->status === 'Rejected';
          $borderColor = $isApproved ? 'rgba(22,163,74,.4)' : 'rgba(220,38,38,.4)';
        @endphp
        <div style="background:var(--surface);border:1.5px solid {{ $borderColor }};border-radius:14px;overflow:hidden;transition:box-shadow .15s"
             onmouseover="this.style.boxShadow='0 4px 18px rgba(0,0,0,.07)'" onmouseout="this.style.boxShadow='none'">
          {{-- Card header --}}
          <div style="padding:14px 18px;display:flex;align-items:center;gap:12px;flex-wrap:wrap;border-bottom:1px solid var(--border)">
            <span style="display:inline-flex;align-items:center;gap:6px;background:{{ $mt['bg'] }};color:{{ $mt['color'] }};border-radius:20px;padding:3px 11px;font-size:11.5px;font-weight:700;flex-shrink:0">
              <i class="bi {{ $mt['icon'] }}" style="font-size:11px"></i>{{ $mt['label'] }}
            </span>
            <div style="flex:1;min-width:0">
              <div style="font-size:13.5px;font-weight:700;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                {{ $mf->subject }}
              </div>
              <div style="font-size:11px;color:var(--muted);margin-top:2px">
                #{{ $mf->id }} &middot; Submitted {{ $mf->created_at->format('d M Y, H:i') }}
              </div>
            </div>
            {{-- Status badge --}}
            @if($isApproved)
            <span style="display:inline-flex;align-items:center;gap:5px;background:rgba(22,163,74,.1);color:#16a34a;border:1px solid rgba(22,163,74,.3);border-radius:20px;padding:4px 12px;font-size:12px;font-weight:700;flex-shrink:0">
              <i class="bi bi-check-circle-fill"></i> Approved
            </span>
            @else
            <span style="display:inline-flex;align-items:center;gap:5px;background:rgba(220,38,38,.1);color:#dc2626;border:1px solid rgba(220,38,38,.3);border-radius:20px;padding:4px 12px;font-size:12px;font-weight:700;flex-shrink:0">
              <i class="bi bi-x-circle-fill"></i> Rejected
            </span>
            @endif
          </div>
          {{-- Status tracker --}}
          <div style="padding:16px 20px">
            <div style="display:flex;align-items:flex-start;gap:0">
              <div style="display:flex;flex-direction:column;align-items:center;gap:5px;min-width:80px">
                <div style="width:34px;height:34px;border-radius:50%;background:rgba(22,163,74,.12);border:2px solid #16a34a;display:flex;align-items:center;justify-content:center;font-size:15px;color:#16a34a">
                  <i class="bi bi-check-lg"></i>
                </div>
                <div style="font-size:10.5px;font-weight:700;color:#16a34a;text-align:center;line-height:1.3">Submitted</div>
              </div>
              <div style="flex:1;height:2px;background:#16a34a;margin-top:16px;border-radius:2px"></div>
              <div style="display:flex;flex-direction:column;align-items:center;gap:5px;min-width:80px">
                <div style="width:34px;height:34px;border-radius:50%;background:rgba(22,163,74,.12);border:2px solid #16a34a;display:flex;align-items:center;justify-content:center;font-size:14px;color:#16a34a">
                  <i class="bi bi-check-lg"></i>
                </div>
                <div style="font-size:10.5px;font-weight:700;color:#16a34a;text-align:center;line-height:1.3">Reviewed</div>
              </div>
              <div style="flex:1;height:2px;background:{{ $isApproved ? '#16a34a' : '#dc2626' }};margin-top:16px;border-radius:2px"></div>
              @if($isApproved)
              <div style="display:flex;flex-direction:column;align-items:center;gap:5px;min-width:80px">
                <div style="width:34px;height:34px;border-radius:50%;background:rgba(22,163,74,.12);border:2px solid #16a34a;display:flex;align-items:center;justify-content:center;font-size:14px;color:#16a34a">
                  <i class="bi bi-check-circle-fill"></i>
                </div>
                <div style="font-size:10.5px;font-weight:700;color:#16a34a;text-align:center;line-height:1.3">Approved</div>
              </div>
              @else
              <div style="display:flex;flex-direction:column;align-items:center;gap:5px;min-width:80px">
                <div style="width:34px;height:34px;border-radius:50%;background:rgba(220,38,38,.12);border:2px solid #dc2626;display:flex;align-items:center;justify-content:center;font-size:14px;color:#dc2626">
                  <i class="bi bi-x-circle-fill"></i>
                </div>
                <div style="font-size:10.5px;font-weight:700;color:#dc2626;text-align:center;line-height:1.3">Rejected</div>
              </div>
              @endif
            </div>

            {{-- Remarks --}}
            @if($mf->approval_remarks)
            <div style="margin-top:14px;padding:10px 14px;background:var(--body-bg);border:1px solid var(--border);border-radius:8px;display:flex;align-items:flex-start;gap:8px">
              <i class="bi bi-chat-left-text-fill" style="color:var(--muted);font-size:13px;flex-shrink:0;margin-top:1px"></i>
              <div>
                <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px">Remarks from IT Admin</div>
                <div style="font-size:12.5px;color:var(--text)">{{ $mf->approval_remarks }}</div>
              </div>
            </div>
            @endif
            {{-- Review date --}}
            @if($mf->reviewed_at)
            <div style="margin-top:10px;font-size:11px;color:var(--muted);text-align:right">
              Reviewed on {{ \Carbon\Carbon::parse($mf->reviewed_at)->format('d M Y, H:i') }}
            </div>
            @endif
          </div>
        </div>
        @endforeach
      </div>
      @else
      <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:36px 20px;text-align:center;color:var(--muted)">
        <i class="bi bi-check2-circle" style="font-size:28px;display:block;margin-bottom:10px;opacity:.35"></i>
        <div style="font-size:13.5px;font-weight:600;color:var(--text);margin-bottom:4px">No decided requests</div>
        <div style="font-size:12.5px">Approved and rejected requests will appear here.</div>
      </div>
      @endif
    </div>
  </div>

  <script>
  function toggleDecided() {
    var list = document.getElementById('decidedList');
    var open = list.style.display !== 'none';
    list.style.display = open ? 'none' : 'block';
    document.getElementById('decidedToggleBtn').innerHTML = open
      ? '<i class="bi bi-chevron-down" id="decidedToggleIcon"></i> Show'
      : '<i class="bi bi-chevron-up"   id="decidedToggleIcon"></i> Hide';
  }
  </script>

  {{-- â•â• HOU: PENDING APPROVAL REQUESTS â•â• --}}
  @if($user->it_role === 'hou')
  <div style="margin-top:32px" id="hou-pending-section">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;flex-wrap:wrap;gap:8px">
      <div>
        <div style="font-family:'Inter',sans-serif;font-size:15px;font-weight:800;color:var(--text);margin-bottom:2px">
          <i class="bi bi-person-check-fill" style="color:#7c3aed;margin-right:7px"></i>Pending Approval Requests
        </div>
        <div style="font-size:12.5px;color:var(--muted)">IT requests from your staff that have listed you as approver.</div>
      </div>
      @if($pendingApprovals->count())
      <div style="display:inline-flex;align-items:center;gap:7px;background:rgba(124,58,237,.1);border:1.5px solid rgba(124,58,237,.25);border-radius:10px;padding:7px 14px">
        <span style="width:7px;height:7px;background:#7c3aed;border-radius:50%;display:inline-block;animation:itr-pulse 1.6s infinite"></span>
        <span style="font-size:12.5px;font-weight:700;color:#7c3aed">{{ $pendingApprovals->count() }} pending</span>
      </div>
      @endif
    </div>

    @if($pendingApprovals->count())
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;overflow:hidden">

      {{-- HOU bulk bar --}}
      <div id="houBulkBar" style="display:none;padding:10px 20px;background:rgba(124,58,237,.07);border-bottom:1px solid rgba(124,58,237,.2);align-items:center;gap:12px;flex-wrap:wrap">
        <span id="houBulkCount" style="font-size:13px;font-weight:700;color:#7c3aed;min-width:80px"></span>
        <button onclick="openBulkModal('hou','approve')" style="font-family:'Inter',sans-serif;font-size:12.5px;font-weight:700;padding:7px 16px;background:#16a34a;color:#fff;border:none;border-radius:8px;cursor:pointer;display:inline-flex;align-items:center;gap:6px"><i class="bi bi-check-circle-fill"></i> Approve Selected</button>
        <button onclick="openBulkModal('hou','reject')"  style="font-family:'Inter',sans-serif;font-size:12.5px;font-weight:700;padding:7px 16px;background:#dc2626;color:#fff;border:none;border-radius:8px;cursor:pointer;display:inline-flex;align-items:center;gap:6px"><i class="bi bi-x-circle-fill"></i> Reject Selected</button>
        <button onclick="clearBulkSelection('hou')" style="font-family:'Inter',sans-serif;font-size:12px;font-weight:600;padding:7px 12px;background:transparent;border:1.5px solid var(--border);border-radius:8px;color:var(--muted);cursor:pointer">Clear</button>
      </div>

      {{-- Column headers --}}
      <div style="display:grid;grid-template-columns:40px 2fr 1fr 1fr 110px auto;padding:10px 20px;background:var(--body-bg);border-bottom:1px solid var(--border);align-items:center">
        <div style="display:flex;align-items:center"><input type="checkbox" class="bulk-cb" id="houSelectAll" onchange="toggleSelectAll('hou')" title="Select all"></div>
        <div style="font-size:10.5px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em">Request</div>
        <div style="font-size:10.5px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em">Submitted By</div>
        <div style="font-size:10.5px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em">Department</div>
        <div style="font-size:10.5px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em">Date</div>
        <div style="font-size:10.5px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em">Actions</div>
      </div>

      @foreach($pendingApprovals as $pa)
      @php
        $paTypeMap = [
          'hardware' => ['label'=>'Hardware','color'=>'#3b82f6','bg'=>'rgba(59,130,246,.1)','icon'=>'bi-laptop'],
          'software' => ['label'=>'Software','color'=>'#8b5cf6','bg'=>'rgba(139,92,246,.1)','icon'=>'bi-code-slash'],
          'system'   => ['label'=>'System',  'color'=>'#10b981','bg'=>'rgba(16,185,129,.1)','icon'=>'bi-hdd-network'],
          'service'  => ['label'=>'Service', 'color'=>'#0284c7','bg'=>'rgba(2,132,199,.1)', 'icon'=>'bi-wifi'],
        ];
        $pt = $paTypeMap[$pa->request_type] ?? ['label'=>ucfirst($pa->request_type),'color'=>'#64748b','bg'=>'rgba(100,116,139,.1)','icon'=>'bi-question-circle'];
      @endphp
      <div style="display:grid;grid-template-columns:40px 2fr 1fr 1fr 110px auto;padding:13px 20px;border-bottom:1px solid var(--border);align-items:center;gap:12px;transition:background .12s"
           onmouseover="this.style.background='var(--body-bg)'" onmouseout="this.style.background='transparent'">

        {{-- Checkbox --}}
        <div style="display:flex;align-items:center">
          <input type="checkbox" class="bulk-cb hou-cb" value="{{ $pa->id }}" onchange="updateBulkBar('hou')">
        </div>

        {{-- Request: type + subject --}}
        <div style="min-width:0">
          <div style="display:flex;align-items:center;gap:7px;margin-bottom:4px;flex-wrap:wrap">
            <span style="display:inline-flex;align-items:center;gap:5px;background:{{ $pt['bg'] }};color:{{ $pt['color'] }};border-radius:20px;padding:2px 10px;font-size:11px;font-weight:700">
              <i class="bi {{ $pt['icon'] }}" style="font-size:10px"></i>{{ $pt['label'] }}
            </span>
            <span style="font-size:11px;color:var(--muted);font-weight:500">#{{ $pa->id }}</span>
          </div>
          <div style="font-size:13px;font-weight:700;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
            {{ $pa->subject ?? '(No subject)' }}
          </div>
          @if($pa->justification)
          <div style="font-size:11.5px;color:var(--muted);margin-top:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
            {{ Str::limit(strip_tags($pa->justification), 120) }}
          </div>
          @endif
        </div>

        {{-- Submitted by --}}
        <div style="display:flex;align-items:center;gap:8px;min-width:0">
          <div style="width:28px;height:28px;border-radius:50%;background:rgba(124,58,237,.1);color:#7c3aed;display:flex;align-items:center;justify-content:center;font-size:12px;flex-shrink:0">
            <i class="bi bi-person-fill"></i>
          </div>
          <div style="min-width:0">
            <div style="font-size:12.5px;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
              {{ $pa->req_name ?: ($pa->submittedBy?->full_name ?? '—') }}
            </div>
            <div style="font-size:11px;color:var(--muted);margin-top:1px">{{ $pa->req_designation ?: '—' }}</div>
          </div>
        </div>

        {{-- Department --}}
        <div style="font-size:12.5px;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
          {{ $pa->req_department ?: '—' }}
        </div>

        {{-- Date --}}
        <div>
          <div style="font-size:12.5px;font-weight:500;color:var(--text)">{{ $pa->created_at->format('d M Y') }}</div>
          <div style="font-size:11px;color:var(--muted);margin-top:1px">{{ $pa->created_at->format('H:i') }}</div>
        </div>

        {{-- Actions --}}
        <div style="display:flex;align-items:center;gap:7px;flex-shrink:0;flex-wrap:wrap">
          <a href="{{ route('it.it-request-form.hou-show', $pa->id) }}"
            style="display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:600;color:var(--accent);border:1.5px solid rgba(2,132,199,.3);border-radius:7px;padding:5px 11px;text-decoration:none;background:transparent;transition:all .15s"
            onmouseover="this.style.background='rgba(2,132,199,.08)'" onmouseout="this.style.background='transparent'">
            <i class="bi bi-eye-fill"></i> Open
          </a>
          <button onclick="houAction({{ $pa->id }},'approve')"
            style="display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:600;color:#16a34a;border:1.5px solid rgba(22,163,74,.3);border-radius:7px;padding:5px 11px;background:transparent;cursor:pointer;font-family:inherit;transition:all .15s"
            onmouseover="this.style.background='rgba(22,163,74,.08)'" onmouseout="this.style.background='transparent'">
            <i class="bi bi-check-circle-fill"></i> Approve
          </button>
          <button onclick="houAction({{ $pa->id }},'reject')"
            style="display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:600;color:#dc2626;border:1.5px solid rgba(220,38,38,.3);border-radius:7px;padding:5px 11px;background:transparent;cursor:pointer;font-family:inherit;transition:all .15s"
            onmouseover="this.style.background='rgba(220,38,38,.08)'" onmouseout="this.style.background='transparent'">
            <i class="bi bi-x-circle-fill"></i> Reject
          </button>
        </div>

      </div>
      @endforeach

    </div>
    @else
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:36px 20px;text-align:center;color:var(--muted)">
      <i class="bi bi-inbox" style="font-size:28px;display:block;margin-bottom:10px;opacity:.35"></i>
      <div style="font-size:13.5px;font-weight:600;color:var(--text);margin-bottom:4px">No pending requests</div>
      <div style="font-size:12.5px">Requests from your staff will appear here once submitted.</div>
    </div>
    @endif
  </div>

  {{-- HOU inline action modal --}}
  <div id="houActionModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:10000;align-items:center;justify-content:center">
    <div style="background:var(--surface);border-radius:14px;padding:28px 28px 24px;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,.25);margin:16px">
      <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px">
        <div id="houModalIcon" style="width:40px;height:40px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0"></div>
        <div id="houModalTitle" style="font-family:'Inter',sans-serif;font-size:16px;font-weight:800;color:var(--text)"></div>
      </div>
      <form id="houActionForm" method="POST">
        @csrf
        <div style="margin-bottom:16px">
          <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:6px">
            Remarks <span id="houRemarksNote" style="font-weight:400;color:var(--muted)"></span>
          </label>
          <textarea name="approval_remarks" id="houRemarks" rows="3"
            style="width:100%;font-family:'Inter',sans-serif;font-size:13px;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;background:var(--surface);color:var(--text);outline:none;resize:vertical;box-sizing:border-box"
            onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'"
            placeholder="Add a remark (optional)…"></textarea>
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end">
          <button type="button" onclick="closeHouModal()"
            style="font-family:'Inter',sans-serif;font-size:13px;font-weight:600;padding:9px 18px;background:var(--body-bg);border:1.5px solid var(--border);border-radius:8px;color:var(--text);cursor:pointer">
            Cancel
          </button>
          <button type="submit" id="houModalSubmit"
            style="font-family:'Inter',sans-serif;font-size:13px;font-weight:700;padding:9px 22px;border:none;border-radius:8px;color:#fff;cursor:pointer">
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
  var _bulkRoutes = {
    hou: {
      approve: '{{ route("it.it-request-form.bulk-hou-approve") }}',
      reject:  '{{ route("it.it-request-form.bulk-hou-reject") }}'
    },
    val: {
      approve: '{{ route("it.it-request-form.bulk-validator-approve") }}',
      reject:  '{{ route("it.it-request-form.bulk-validator-reject") }}'
    }
  };

  function updateBulkBar(group) {
    var cbs    = document.querySelectorAll('.' + group + '-cb:checked');
    var barId  = group === 'hou' ? 'houBulkBar' : 'valBulkBar';
    var cntId  = group === 'hou' ? 'houBulkCount' : 'valBulkCount';
    var saId   = group === 'hou' ? 'houSelectAll' : 'valSelectAll';
    var bar    = document.getElementById(barId);
    var cntEl  = document.getElementById(cntId);
    if (!bar) return;
    if (cbs.length > 0) { bar.style.display = 'flex'; cntEl.textContent = cbs.length + ' selected'; }
    else { bar.style.display = 'none'; }
    var allCbs = document.querySelectorAll('.' + group + '-cb');
    var saEl   = document.getElementById(saId);
    if (saEl) saEl.checked = allCbs.length > 0 && cbs.length === allCbs.length;
  }

  function toggleSelectAll(group) {
    var saEl   = document.getElementById(group === 'hou' ? 'houSelectAll' : 'valSelectAll');
    document.querySelectorAll('.' + group + '-cb').forEach(function(cb){ cb.checked = saEl.checked; });
    updateBulkBar(group);
  }

  function clearBulkSelection(group) {
    document.querySelectorAll('.' + group + '-cb').forEach(function(cb){ cb.checked = false; });
    var saEl = document.getElementById(group === 'hou' ? 'houSelectAll' : 'valSelectAll');
    if (saEl) saEl.checked = false;
    updateBulkBar(group);
  }

  function openBulkModal(group, action) {
    var cbs = document.querySelectorAll('.' + group + '-cb:checked');
    if (!cbs.length) return;
    var icon  = document.getElementById('bulkModalIcon');
    var title = document.getElementById('bulkModalTitle');
    var desc  = document.getElementById('bulkModalDesc');
    var sub   = document.getElementById('bulkModalSubmit');
    var form  = document.getElementById('bulkForm');
    var cont  = document.getElementById('bulkIdsContainer');
    document.getElementById('bulkRemarks').value = '';
    cont.innerHTML = '';
    cbs.forEach(function(cb){
      var inp = document.createElement('input');
      inp.type = 'hidden'; inp.name = 'ids[]'; inp.value = cb.value;
      cont.appendChild(inp);
    });
    form.action = (_bulkRoutes[group] || {})[action] || '#';
    var n = cbs.length;
    if (action === 'approve') {
      icon.style.background = 'rgba(22,163,74,.12)'; icon.style.color = '#16a34a';
      icon.innerHTML = '<i class="bi bi-check-circle-fill"></i>';
      title.textContent = 'Approve ' + n + ' Request' + (n > 1 ? 's' : '');
      desc.textContent  = 'All ' + n + ' selected request' + (n > 1 ? 's' : '') + ' will be approved. The remark below applies to all.';
      sub.textContent = 'Approve All'; sub.style.background = '#16a34a';
    } else {
      icon.style.background = 'rgba(220,38,38,.12)'; icon.style.color = '#dc2626';
      icon.innerHTML = '<i class="bi bi-x-circle-fill"></i>';
      title.textContent = 'Reject ' + n + ' Request' + (n > 1 ? 's' : '');
      desc.textContent  = 'All ' + n + ' selected request' + (n > 1 ? 's' : '') + ' will be rejected. The remark below applies to all.';
      sub.textContent = 'Reject All'; sub.style.background = '#dc2626';
    }
    document.getElementById('bulkModal').style.display = 'flex';
    setTimeout(function(){ document.getElementById('bulkRemarks').focus(); }, 80);
  }

  function closeBulkModal() { document.getElementById('bulkModal').style.display = 'none'; }

  var houApproveBase = '{{ url("/it/it-request-form") }}';
  function houAction(id, action) {
    var modal  = document.getElementById('houActionModal');
    var icon   = document.getElementById('houModalIcon');
    var title  = document.getElementById('houModalTitle');
    var submit = document.getElementById('houModalSubmit');
    var note   = document.getElementById('houRemarksNote');
    var form   = document.getElementById('houActionForm');
    document.getElementById('houRemarks').value = '';

    if (action === 'approve') {
      icon.style.background = 'rgba(22,163,74,.12)'; icon.style.color = '#16a34a';
      icon.innerHTML = '<i class="bi bi-check-circle-fill"></i>';
      title.textContent = 'Approve Request';
      submit.textContent = 'Approve'; submit.style.background = '#16a34a';
      note.textContent = '(optional)';
      form.action = houApproveBase + '/' + id + '/hou-approve';
    } else {
      icon.style.background = 'rgba(220,38,38,.12)'; icon.style.color = '#dc2626';
      icon.innerHTML = '<i class="bi bi-x-circle-fill"></i>';
      title.textContent = 'Reject Request';
      submit.textContent = 'Reject'; submit.style.background = '#dc2626';
      note.textContent = '(optional)';
      form.action = houApproveBase + '/' + id + '/hou-reject';
    }
    modal.style.display = 'flex';
    setTimeout(function() { document.getElementById('houRemarks').focus(); }, 80);
  }
  function closeHouModal() { document.getElementById('houActionModal').style.display = 'none'; }
  document.getElementById('houActionModal').addEventListener('click', function(e) { if (e.target === this) closeHouModal(); });
  </script>
  @endif

  {{-- Shared bulk modal (non-admin: HOU + Validator) --}}
  @if($user->it_role === 'hou' || $user->isItValidator())
  <div id="bulkModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:10000;align-items:center;justify-content:center">
    <div style="background:var(--surface);border-radius:14px;padding:28px 28px 24px;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,.25);margin:16px">
      <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px">
        <div id="bulkModalIcon" style="width:40px;height:40px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0"></div>
        <div id="bulkModalTitle" style="font-family:'Inter',sans-serif;font-size:16px;font-weight:800;color:var(--text)"></div>
      </div>
      <div id="bulkModalDesc" style="font-size:13px;color:var(--muted);margin-bottom:16px"></div>
      <form id="bulkForm" method="POST">
        @csrf
        <div id="bulkIdsContainer"></div>
        <div style="margin-bottom:16px">
          <label style="font-size:12px;font-weight:600;color:var(--text);display:block;margin-bottom:6px">Remarks <span style="font-weight:400;color:var(--muted)">(optional — applies to all selected)</span></label>
          <textarea name="remarks" id="bulkRemarks" rows="3"
            style="width:100%;font-family:'Inter',sans-serif;font-size:13px;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;background:var(--surface);color:var(--text);outline:none;resize:vertical;box-sizing:border-box"
            onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'"
            placeholder="Add a remark (optional)…"></textarea>
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end">
          <button type="button" onclick="closeBulkModal()" style="font-family:'Inter',sans-serif;font-size:13px;font-weight:600;padding:9px 18px;background:var(--body-bg);border:1.5px solid var(--border);border-radius:8px;color:var(--text);cursor:pointer">Cancel</button>
          <button type="submit" id="bulkModalSubmit" style="font-family:'Inter',sans-serif;font-size:13px;font-weight:700;padding:9px 22px;border:none;border-radius:8px;color:#fff;cursor:pointer"></button>
        </div>
      </form>
    </div>
  </div>
  <script>
  document.getElementById('bulkModal').addEventListener('click', function(e){ if (e.target === this) closeBulkModal(); });
  </script>
  @endif

  {{-- â•â• VALIDATOR: PENDING VALIDATION REQUESTS (Mohd Azrull only) â•â• --}}
  @if($user->isItValidator())
  <div style="margin-top:32px" id="validator-pending-section">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;flex-wrap:wrap;gap:8px">
      <div>
        <div style="font-family:'Inter',sans-serif;font-size:15px;font-weight:800;color:var(--text);margin-bottom:2px">
          <i class="bi bi-patch-check-fill" style="color:#7c3aed;margin-right:7px"></i>Pending Validation
        </div>
        <div style="font-size:12.5px;color:var(--muted)">IT requests approved by IT Admin that are awaiting your final validation.</div>
      </div>
      @if($pendingValidations->count())
      <div style="display:inline-flex;align-items:center;gap:7px;background:rgba(124,58,237,.1);border:1.5px solid rgba(124,58,237,.25);border-radius:10px;padding:7px 14px">
        <span style="width:7px;height:7px;background:#7c3aed;border-radius:50%;display:inline-block;animation:itr-pulse 1.6s infinite"></span>
        <span style="font-size:12.5px;font-weight:700;color:#7c3aed">{{ $pendingValidations->count() }} awaiting validation</span>
      </div>
      @endif
    </div>

    @if($pendingValidations->count())
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;overflow:hidden">

      {{-- Validator bulk bar --}}
      <div id="valBulkBar" style="display:none;padding:10px 20px;background:rgba(124,58,237,.07);border-bottom:1px solid rgba(124,58,237,.2);align-items:center;gap:12px;flex-wrap:wrap">
        <span id="valBulkCount" style="font-size:13px;font-weight:700;color:#7c3aed;min-width:80px"></span>
        <button onclick="openBulkModal('val','approve')" style="font-family:'Inter',sans-serif;font-size:12.5px;font-weight:700;padding:7px 16px;background:#16a34a;color:#fff;border:none;border-radius:8px;cursor:pointer;display:inline-flex;align-items:center;gap:6px"><i class="bi bi-patch-check-fill"></i> Validate &amp; Approve Selected</button>
        <button onclick="openBulkModal('val','reject')"  style="font-family:'Inter',sans-serif;font-size:12.5px;font-weight:700;padding:7px 16px;background:#dc2626;color:#fff;border:none;border-radius:8px;cursor:pointer;display:inline-flex;align-items:center;gap:6px"><i class="bi bi-x-circle-fill"></i> Reject Selected</button>
        <button onclick="clearBulkSelection('val')" style="font-family:'Inter',sans-serif;font-size:12px;font-weight:600;padding:7px 12px;background:transparent;border:1.5px solid var(--border);border-radius:8px;color:var(--muted);cursor:pointer">Clear</button>
      </div>

      <div style="display:grid;grid-template-columns:40px 2fr 1fr 1fr 110px auto;padding:10px 20px;background:var(--body-bg);border-bottom:1px solid var(--border);align-items:center">
        <div style="display:flex;align-items:center"><input type="checkbox" class="bulk-cb" id="valSelectAll" onchange="toggleSelectAll('val')" title="Select all"></div>
        <div style="font-size:10.5px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em">Request</div>
        <div style="font-size:10.5px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em">Submitted By</div>
        <div style="font-size:10.5px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em">Department</div>
        <div style="font-size:10.5px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em">Date</div>
        <div style="font-size:10.5px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em">Actions</div>
      </div>
      @foreach($pendingValidations as $pv)
      @php
        $pvTypeMap = [
          'hardware' => ['label'=>'Hardware','color'=>'#3b82f6','bg'=>'rgba(59,130,246,.1)','icon'=>'bi-laptop'],
          'software' => ['label'=>'Software','color'=>'#8b5cf6','bg'=>'rgba(139,92,246,.1)','icon'=>'bi-code-slash'],
          'system'   => ['label'=>'System',  'color'=>'#10b981','bg'=>'rgba(16,185,129,.1)','icon'=>'bi-hdd-network'],
          'service'  => ['label'=>'Service', 'color'=>'#0284c7','bg'=>'rgba(2,132,199,.1)', 'icon'=>'bi-wifi'],
        ];
        $pvt = $pvTypeMap[$pv->request_type] ?? ['label'=>ucfirst($pv->request_type),'color'=>'#64748b','bg'=>'rgba(100,116,139,.1)','icon'=>'bi-question-circle'];
      @endphp
      <div style="display:grid;grid-template-columns:40px 2fr 1fr 1fr 110px auto;padding:13px 20px;border-bottom:1px solid var(--border);align-items:center;gap:12px;transition:background .12s"
           onmouseover="this.style.background='var(--body-bg)'" onmouseout="this.style.background='transparent'">
        {{-- Checkbox --}}
        <div style="display:flex;align-items:center">
          <input type="checkbox" class="bulk-cb val-cb" value="{{ $pv->id }}" onchange="updateBulkBar('val')">
        </div>
        <div style="min-width:0">
          <div style="display:flex;align-items:center;gap:7px;margin-bottom:4px;flex-wrap:wrap">
            <span style="display:inline-flex;align-items:center;gap:5px;background:{{ $pvt['bg'] }};color:{{ $pvt['color'] }};border-radius:20px;padding:2px 10px;font-size:11px;font-weight:700">
              <i class="bi {{ $pvt['icon'] }}" style="font-size:10px"></i>{{ $pvt['label'] }}
            </span>
            <span style="font-size:11px;color:var(--muted);font-weight:500">#{{ $pv->id }}</span>
          </div>
          <div style="font-size:13px;font-weight:700;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $pv->subject ?? '(No subject)' }}</div>
          @if($pv->justification)
          <div style="font-size:11.5px;color:var(--muted);margin-top:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ Str::limit(strip_tags($pv->justification), 120) }}</div>
          @endif
        </div>
        <div style="display:flex;align-items:center;gap:8px;min-width:0">
          <div style="width:28px;height:28px;border-radius:50%;background:rgba(124,58,237,.1);color:#7c3aed;display:flex;align-items:center;justify-content:center;font-size:12px;flex-shrink:0">
            <i class="bi bi-person-fill"></i>
          </div>
          <div style="min-width:0">
            <div style="font-size:12.5px;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
              {{ $pv->req_name ?: ($pv->submittedBy?->full_name ?? '—') }}
            </div>
            <div style="font-size:11px;color:var(--muted);margin-top:1px">{{ $pv->req_designation ?: '—' }}</div>
          </div>
        </div>
        <div style="font-size:12.5px;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $pv->user_department ?: '—' }}</div>
        <div>
          <div style="font-size:12.5px;font-weight:500;color:var(--text)">{{ $pv->created_at->format('d M Y') }}</div>
          <div style="font-size:11px;color:var(--muted);margin-top:1px">{{ $pv->created_at->format('H:i') }}</div>
        </div>
        <div style="display:flex;align-items:center;gap:7px;flex-shrink:0;flex-wrap:wrap">
          <a href="{{ route('it.it-request-form.validator-show', $pv->id) }}"
            style="display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:600;color:#7c3aed;border:1.5px solid rgba(124,58,237,.3);border-radius:7px;padding:5px 11px;text-decoration:none;background:transparent;transition:all .15s"
            onmouseover="this.style.background='rgba(124,58,237,.08)'" onmouseout="this.style.background='transparent'">
            <i class="bi bi-eye-fill"></i> Open
          </a>
        </div>
      </div>
      @endforeach
    </div>
    @else
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:36px 20px;text-align:center;color:var(--muted)">
      <i class="bi bi-patch-check" style="font-size:28px;display:block;margin-bottom:10px;opacity:.35"></i>
      <div style="font-size:13.5px;font-weight:600;color:var(--text);margin-bottom:4px">No pending validations</div>
      <div style="font-size:12.5px">Requests approved by IT Admin will appear here for your validation.</div>
    </div>
    @endif
  </div>
  @endif

  <!-- â•â• STEP 2 â•â• -->
  <div id="step2">

    <div class="itr-selected-banner">
      <div class="itr-selected-pill"><i id="banner-icon"></i><span id="banner-label"></span></div>
      <div class="itr-banner-text">Fields marked <strong style="color:var(--red)">*</strong> are required only when submitting to IT Admin. You may <strong>Save as Draft</strong> at any time with incomplete fields.</div>
      <button type="button" class="itr-change-btn" onclick="changeType()"><i class="bi bi-arrow-left"></i> Go Back</button>
    </div>

    <!-- Shared subject row (rendered per-form via JS copy, but we place it inside each form below) -->

    <!-- â–‘â–‘ HARDWARE â–‘â–‘ -->
    <form id="form-hardware" style="display:none" method="POST" action="{{ route('it.it-request-form.store') }}" enctype="multipart/form-data" onsubmit="return validateAndSubmit('hardware', event)" novalidate>
      @csrf
      <input type="hidden" name="request_type" value="hardware">
      <!-- Hidden fields for pills/chips -->
      <input type="hidden" name="hw_request_type" id="hw_request_type_val">
      <!-- hw_items[] injected dynamically by JS -->

      <div class="itr-hw-layout">

      {{-- Title / Subject --}}
      <div class="itr-section itr-hw-full">
        <div class="itr-section-head">
          <i class="bi bi-tag" style="color:var(--muted);font-size:15px;flex-shrink:0"></i>
          <div class="itr-section-title">Request Title</div>
        </div>
        <div class="itr-section-body">
          <div class="fg">
            <div class="itr-label">Title</div>
            <input class="itr-input{{ $errors->has('subject') ? ' is-error' : '' }}"
                   type="text" name="subject" id="itr-subject"
                   value="{{ old('subject') }}"
                   placeholder="Brief description of what this request is for…"
                   maxlength="200"/>
            @error('subject')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
          </div>
        </div>
      </div>

      {{-- Section 1: Request Type & Item Selection (full width) --}}
      <div class="itr-section itr-hw-full">
        <div class="itr-section-head">
          <div class="itr-section-num">1</div>
          <i class="bi bi-laptop" style="color:var(--muted);font-size:15px;flex-shrink:0"></i>
          <div class="itr-section-title">Request Type &amp; Item Selection</div>
        </div>
        <div class="itr-section-body">
          <div class="g2">
            <div>
              <div class="itr-label">Type of Request <span class="itr-req">*</span></div>
              <div class="itr-radio-group" id="hw-req-type">
                <div class="itr-radio-pill" data-group="hw-req-type" onclick="pillSelect(this,'hw_request_type_val','new')"> New</div>
                <div class="itr-radio-pill" data-group="hw-req-type" onclick="pillSelect(this,'hw_request_type_val','replacement')"> Replacement</div>
                <div class="itr-radio-pill" data-group="hw-req-type" onclick="pillSelect(this,'hw_request_type_val','transfer_staff')"> Transfer to Other Staff</div>
                <div class="itr-radio-pill" data-group="hw-req-type" onclick="pillSelect(this,'hw_request_type_val','transfer_company')"> Transfer to Other Company</div>
              </div>
              @error('hw_request_type')<div class="itr-field-error" style="margin-top:6px"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
              <div class="itr-field-error itr-js-error" id="err-hw-req-type" style="display:none;margin-top:6px"><i class="bi bi-exclamation-circle-fill"></i> Please select a type of request.</div>
            </div>
            <div>
              <div class="itr-label" style="margin-bottom:10px">Type of Item <span class="itr-req">*</span></div>
              <input class="itr-input{{ $errors->has('hw_items') ? ' is-error' : '' }}" type="text" name="hw_items[]" value="{{ old('hw_items.0') }}" placeholder="e.g. Laptop, Desktop PC, Printer…" required/>
              @error('hw_items')<div class="itr-field-error" style="margin-top:6px"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
              <div class="itr-field-error itr-js-error" id="err-hw-items" style="display:none;margin-top:6px"><i class="bi bi-exclamation-circle-fill"></i> Please enter a type of item.</div>
            </div>
          </div>
        </div>
      </div>

      {{-- Section 2: User Classification (full width) --}}
      <div class="itr-section itr-hw-full">
        <div class="itr-section-head">
          <div class="itr-section-num">2</div>
          <i class="bi bi-person-badge" style="color:var(--muted);font-size:15px;flex-shrink:0"></i>
          <div class="itr-section-title">User Classification</div>
          <span class="itr-section-hint">Who is this request for?</span>
        </div>
        <div class="itr-section-body">
          <div class="fg">
            <div class="itr-label">Type of User <span class="itr-req">*</span></div>
            <select class="itr-input{{ $errors->has('user_type') ? ' is-error' : '' }}" name="user_type" required>
              <option value="">-- Select --</option>
              <option {{ old('user_type') === 'New Hire' ? 'selected' : '' }}>New Hire</option>
              <option {{ old('user_type') === 'Intern' ? 'selected' : '' }}>Intern</option>
              <option {{ old('user_type') === 'Resign' ? 'selected' : '' }}>Resign</option>
              <option {{ old('user_type') === 'Existing' ? 'selected' : '' }}>Existing</option>
              <option {{ old('user_type') === 'Vendor' ? 'selected' : '' }}>Vendor</option>
            </select>
            @error('user_type')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
          </div>
          <div class="fg" style="margin-bottom:0">
            <div class="itr-label">Exit / Join Date <span class="itr-req">*</span></div>
            <input class="itr-input{{ $errors->has('exit_join_date') ? ' is-error' : '' }}" type="date" name="exit_join_date" value="{{ old('exit_join_date') }}" required/>
            @error('exit_join_date')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
          </div>
        </div>
      </div>

      {{-- Section 3: Justification (full width) --}}
      <div class="itr-section itr-hw-full">
        <div class="itr-section-head">
          <div class="itr-section-num">3</div>
          <i class="bi bi-chat-square-text" style="color:var(--muted);font-size:15px;flex-shrink:0"></i>
          <div class="itr-section-title">Justification</div>
          <span class="itr-section-hint">Why is this request needed?</span>
        </div>
        <div class="itr-section-body">
          <div class="fg">
            <div class="itr-quill-wrap{{ $errors->has('justification') ? ' is-error' : '' }}" id="itr-justify-wrap">
              <div id="itr-justify-editor"></div>
            </div>
            <input type="hidden" name="justification" id="itr-justify-hidden">
            @error('justification')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
          </div>
          <div style="border-top:1px solid var(--border);margin:18px -20px 18px;"></div>
          <div>
            <div class="itr-label" style="display:flex;align-items:center;gap:7px;margin-bottom:10px">
              <i class="bi bi-paperclip"></i> Supporting Document
              <span style="font-weight:400;color:var(--muted);text-transform:none;letter-spacing:0">(Optional)</span>
            </div>
            <div class="itr-notice warn"><i class="bi bi-exclamation-triangle-fill"></i><div><strong>(!) Bagi permohonan pertukaran laptop/desktop,</strong> sila sertakan <strong>Report Diagnosis dari Prodata</strong>. Permohonan akan ditolak sekiranya Report Diagnosis tidak disertakan.</div></div>
            <div class="itr-notice info" style="margin-bottom:10px"><i class="bi bi-info-circle-fill"></i><div>Sila pastikan nama lampiran tidak mengandungi simbol berikut: &amp; @ # $ % ^ * ( ) { } [ ] \ / : ' " dan saiz lampiran tidak melebihi <strong>2MB</strong></div></div>
            <div class="itr-doc-zone">
              <i class="bi bi-cloud-upload" style="font-size:20px;color:var(--muted);flex-shrink:0"></i>
              <div style="flex:1;min-width:0">
                <input type="file" id="hw-file" name="document" onchange="setFilename('hw-file','hw-fname')" style="display:none"/>
                <label for="hw-file" style="display:inline-flex;align-items:center;gap:6px;font-size:12.5px;font-weight:600;color:var(--accent);cursor:pointer">
                  <i class="bi bi-folder2-open"></i> Browse file
                </label>
                <span id="hw-fname" style="font-size:12.5px;color:var(--muted);margin-left:8px">No file chosen</span>
              </div>
              <div style="font-size:11.5px;color:var(--muted);flex-shrink:0">Max 2 MB · PDF, DOC, DOCX, JPG, PNG</div>
            </div>
          </div>
        </div>
      </div>

      {{-- Section 4: User Details --}}
      <div class="itr-section">
        <div class="itr-section-head">
          <div class="itr-section-num">4</div>
          <i class="bi bi-person" style="color:var(--muted);font-size:15px;flex-shrink:0"></i>
          <div class="itr-section-title">User Details</div>
          <span class="itr-section-hint">Who will use this item?</span>
        </div>
        <div class="itr-section-body">
          <div class="g2">
            <div class="fg">
              <div class="itr-label">Name <span class="itr-req">*</span></div>
              <input type="hidden" name="user_name" id="user_name_val" value="{{ old('user_name') }}">
              <div class="itr-name-dd{{ $errors->has('user_name') ? ' is-error' : '' }}" id="user_name_wrap">
                <div class="itr-name-trigger" id="user_name_trigger" onclick="toggleStaffDD('user_name')">
                  <span class="itr-name-display{{ old('user_name') ? '' : ' is-placeholder' }}" id="user_name_display">{{ old('user_name') ?: '-- Select user name --' }}</span>
                  <i class="bi bi-chevron-down itr-name-arrow"></i>
                </div>
                <div class="itr-name-panel" id="user_name_panel" style="display:none">
                  <input class="itr-name-search-input" type="text" id="user_name_search" placeholder="Filter by name…" oninput="filterStaffDD('user_name',this.value)" autocomplete="off">
                  <div class="itr-name-list" id="user_name_list"></div>
                </div>
              </div>
              @error('user_name')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
            </div>
            <div class="fg">
              <div class="itr-label">Email <span class="itr-req">*</span></div>
              <input class="itr-input{{ $errors->has('user_email') ? ' is-error' : '' }}" type="email" name="user_email" value="{{ old('user_email') }}" required/>
              @error('user_email')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
            </div>
          </div>
          <div class="g2">
            <div class="fg">
              <div class="itr-label">Staff ID <span class="itr-req">*</span></div>
              <input class="itr-input{{ $errors->has('user_staff_id') ? ' is-error' : '' }}" type="text" name="user_staff_id" value="{{ old('user_staff_id') }}" placeholder="e.g. 12345678" required/>
              @error('user_staff_id')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
            </div>
            <div class="fg">
              <div class="itr-label">Contact No. <span class="itr-req">*</span></div>
              <input class="itr-input{{ $errors->has('user_contact') ? ' is-error' : '' }}" type="text" name="user_contact" value="{{ old('user_contact') }}" required/>
              @error('user_contact')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
            </div>
          </div>
          <div class="g2">
            <div class="fg">
              <div class="itr-label">Department <span class="itr-req">*</span></div>
              <input class="itr-input{{ $errors->has('user_department') ? ' is-error' : '' }}" type="text" name="user_department" value="{{ old('user_department') }}" required/>
              @error('user_department')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
            </div>
            <div class="fg">
              <div class="itr-label">Designation <span class="itr-req">*</span></div>
              <input class="itr-input{{ $errors->has('user_designation') ? ' is-error' : '' }}" type="text" name="user_designation" value="{{ old('user_designation') }}" required/>
              @error('user_designation')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
            </div>
          </div>
          <div class="g2">
            <div class="fg">
              <div class="itr-label">Address <span class="itr-req">*</span></div>
              <input class="itr-input{{ $errors->has('user_address') ? ' is-error' : '' }}" type="text" name="user_address" value="{{ old('user_address') }}" required/>
              @error('user_address')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
            </div>
          </div>
        </div>
      </div>

      {{-- Section 5: Requester Details --}}
      <div class="itr-section">
        <div class="itr-section-head">
          <div class="itr-section-num">5</div>
          <i class="bi bi-person-check" style="color:var(--muted);font-size:15px;flex-shrink:0"></i>
          <div class="itr-section-title">Requester Details</div>
        </div>
        <div class="itr-section-body">
          <div class="fg">
            <div class="itr-label">Name <span class="itr-req">*</span></div>
            <input type="hidden" name="req_name" id="req_name_val" value="{{ old('req_name') }}">
            <div class="itr-name-dd{{ $errors->has('req_name') ? ' is-error' : '' }}" id="req_name_wrap">
              <div class="itr-name-trigger" id="req_name_trigger" onclick="toggleStaffDD('req_name')">
                <span class="itr-name-display{{ old('req_name') ? '' : ' is-placeholder' }}" id="req_name_display">{{ old('req_name') ?: '-- Select staff name --' }}</span>
                <i class="bi bi-chevron-down itr-name-arrow"></i>
              </div>
              <div class="itr-name-panel" id="req_name_panel" style="display:none">
                <input class="itr-name-search-input" type="text" id="req_name_search" placeholder="Filter by name…" oninput="filterStaffDD('req_name',this.value)" autocomplete="off">
                <div class="itr-name-list" id="req_name_list"></div>
              </div>
            </div>
            @error('req_name')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
          </div>
          <div class="fg">
            <div class="itr-label">Staff ID <span class="itr-req">*</span></div>
            <input class="itr-input{{ $errors->has('req_staff_id') ? ' is-error' : '' }}" type="text" name="req_staff_id" value="{{ old('req_staff_id') }}" required/>
            @error('req_staff_id')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
          </div>
          <div class="fg">
            <div class="itr-label">Contact <span class="itr-req">*</span></div>
            <input class="itr-input{{ $errors->has('req_contact') ? ' is-error' : '' }}" type="text" name="req_contact" value="{{ old('req_contact') }}" required/>
            @error('req_contact')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
          </div>
          <div class="fg">
            <div class="itr-label">Department <span class="itr-req">*</span></div>
            <input class="itr-input{{ $errors->has('req_department') ? ' is-error' : '' }}" type="text" name="req_department" value="{{ old('req_department') }}" required/>
            @error('req_department')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
          </div>
          <div class="fg" style="margin-bottom:0">
            <div class="itr-label">Designation <span class="itr-req">*</span></div>
            <input class="itr-input{{ $errors->has('req_designation') ? ' is-error' : '' }}" type="text" name="req_designation" value="{{ old('req_designation') }}" required/>
            @error('req_designation')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
          </div>
        </div>
      </div>

      {{-- Section 6: Approver Details (full width) --}}
      <div class="itr-section itr-hw-full">
        <div class="itr-section-head">
          <div class="itr-section-num">6</div>
          <i class="bi bi-patch-check" style="color:var(--muted);font-size:15px;flex-shrink:0"></i>
          <div class="itr-section-title">Approver Details</div>
        </div>
        <div class="itr-section-body">
          <div class="fg">
            <div class="itr-label">Name <span class="itr-req">*</span></div>
            <input type="hidden" name="approver_name" id="approver_name_val" value="{{ old('approver_name') }}">
            <div class="itr-name-dd{{ $errors->has('approver_name') ? ' is-error' : '' }}" id="approver_name_wrap">
              <div class="itr-name-trigger" id="approver_name_trigger" onclick="toggleStaffDD('approver_name')">
                <span class="itr-name-display{{ old('approver_name') ? '' : ' is-placeholder' }}" id="approver_name_display">{{ old('approver_name') ?: '-- Select approver name --' }}</span>
                <i class="bi bi-chevron-down itr-name-arrow"></i>
              </div>
              <div class="itr-name-panel" id="approver_name_panel" style="display:none">
                <input class="itr-name-search-input" type="text" id="approver_name_search" placeholder="Filter by name…" oninput="filterStaffDD('approver_name',this.value)" autocomplete="off">
                <div class="itr-name-list" id="approver_name_list"></div>
              </div>
            </div>
            @error('approver_name')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
          </div>
          <div class="fg">
            <div class="itr-label">Department <span class="itr-req">*</span></div>
            <input class="itr-input{{ $errors->has('approver_department') ? ' is-error' : '' }}" id="approver_department" type="text" name="approver_department" value="{{ old('approver_department') }}" required/>
            @error('approver_department')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
          </div>
          <div class="fg">
            <div class="itr-label">Designation <span class="itr-req">*</span></div>
            <input class="itr-input{{ $errors->has('approver_designation') ? ' is-error' : '' }}" type="text" name="approver_designation" value="{{ old('approver_designation') }}" required/>
            @error('approver_designation')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
          </div>
          <div class="fg" style="margin-bottom:0">
            <div class="itr-label">Contact <span class="itr-req">*</span></div>
            <input class="itr-input{{ $errors->has('approver_contact') ? ' is-error' : '' }}" id="approver_contact" type="text" name="approver_contact" value="{{ old('approver_contact') }}" required/>
            @error('approver_contact')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
          </div>
        </div>
      </div>

      </div>{{-- /hw-layout --}}
      <div class="itr-action-bar">
        <button type="submit" name="action" value="submit" class="itr-btn-submit"><i class="bi bi-send-fill"></i> Submit Request</button>
        <button type="submit" name="action" value="draft" class="itr-btn-draft" onclick="sessionStorage.removeItem('itr_form_state')"><i class="bi bi-floppy"></i> Save as Draft</button>
      </div>
    </form>
  </div><!-- /step2 -->
</div><!-- /itr-wrap -->

{{-- Navigation-away confirmation modal --}}
<div id="itr-nav-confirm-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;">
  <div style="background:#fff;border-radius:12px;padding:32px 28px;max-width:400px;width:90%;box-shadow:0 8px 32px rgba(0,0,0,.22);text-align:center;">
    <div style="font-size:1.1rem;font-weight:700;color:#1e293b;margin-bottom:10px;">Unsaved Changes</div>
    <p style="color:#64748b;margin:0 0 24px;line-height:1.6;font-size:.95rem;">You have unsaved changes on this form. Would you like to save them as a draft before leaving?</p>
    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
      <button id="itr-nav-cancel-btn" type="button" style="padding:10px 22px;border-radius:8px;border:1.5px solid #cbd5e1;background:#fff;color:#374151;font-weight:600;cursor:pointer;font-size:.9rem;">Cancel</button>
      <button id="itr-nav-discard-btn" type="button" style="padding:10px 22px;border-radius:8px;border:1.5px solid #dc2626;background:#fff;color:#dc2626;font-weight:600;cursor:pointer;font-size:.9rem;">Discard</button>
      <button id="itr-nav-draft-btn" type="button" style="padding:10px 22px;border-radius:8px;border:none;background:#2563eb;color:#fff;font-weight:600;cursor:pointer;font-size:.9rem;"><i class="bi bi-floppy"></i> Save as Draft</button>
    </div>
  </div>
</div>

<script>
const typeConfig = {
  hardware: { label: 'Hardware', icon: 'bi-laptop', form: 'form-hardware' },
};
let activeType = null;
var _accountName = {!! json_encode($user->full_name ?? '') !!};

function selectType(type) {
  activeType = type;
  saveFormState();

  const cfg = typeConfig[type];
  document.getElementById('banner-icon').className = 'bi ' + cfg.icon;
  document.getElementById('banner-label').textContent = cfg.label + ' Request';

  Object.values(typeConfig).forEach(c => { document.getElementById(c.form).style.display = 'none'; });
  document.getElementById(cfg.form).style.display = 'block';

  document.getElementById('prog-step1').className = 'itr-step done';
  document.getElementById('prog-step2').className = 'itr-step active';
  document.getElementById('prog-line').className = 'itr-step-line done';

  const s1 = document.getElementById('step1');
  const ms = document.getElementById('my-submissions');
  const md = document.getElementById('my-decided');
  const houSec = document.getElementById('hou-pending-section');
  const valSec = document.getElementById('validator-pending-section');
  s1.style.opacity = '0';
  if (ms) ms.style.opacity = '0';
  if (md) md.style.opacity = '0';
  if (houSec) houSec.style.opacity = '0';
  if (valSec) valSec.style.opacity = '0';
  setTimeout(() => {
    s1.style.display = 'none';
    if (ms) ms.style.display = 'none';
    if (md) md.style.display = 'none';
    if (houSec) houSec.style.display = 'none';
    if (valSec) valSec.style.display = 'none';
    const s2 = document.getElementById('step2');
    s2.style.display = 'block'; s2.style.opacity = '0';
    requestAnimationFrame(() => { s2.style.transition = 'opacity .25s'; s2.style.opacity = '1'; });
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }, 200);
}

function changeType() {
  sessionStorage.removeItem('itr_form_state');
  activeType = null;
  document.getElementById('prog-step1').className = 'itr-step active';
  document.getElementById('prog-step2').className = 'itr-step';
  document.getElementById('prog-line').className = 'itr-step-line';
  const s2 = document.getElementById('step2');
  s2.style.opacity = '0';
  setTimeout(() => {
    s2.style.display = 'none';
    const s1 = document.getElementById('step1');
    const ms = document.getElementById('my-submissions');
    const houSec = document.getElementById('hou-pending-section');
    const valSec = document.getElementById('validator-pending-section');
    const mdEl = document.getElementById('my-decided');
    s1.style.display = 'block'; s1.style.opacity = '0';
    if (ms) { ms.style.display = 'block'; ms.style.opacity = '0'; }
    if (mdEl) { mdEl.style.display = 'block'; mdEl.style.opacity = '0'; }
    if (houSec) { houSec.style.display = 'block'; houSec.style.opacity = '0'; }
    if (valSec) { valSec.style.display = 'block'; valSec.style.opacity = '0'; }
    requestAnimationFrame(() => {
      s1.style.transition = 'opacity .25s'; s1.style.opacity = '1';
      if (ms) { ms.style.transition = 'opacity .25s'; ms.style.opacity = '1'; }
      if (mdEl) { mdEl.style.transition = 'opacity .25s'; mdEl.style.opacity = '1'; }
      if (houSec) { houSec.style.transition = 'opacity .25s'; houSec.style.opacity = '1'; }
      if (valSec) { valSec.style.transition = 'opacity .25s'; valSec.style.opacity = '1'; }
    });
  }, 200);
}

function pillSelect(el, hiddenId, value) {
  var groupId = el.getAttribute('data-group');
  document.getElementById(groupId).querySelectorAll('.itr-radio-pill').forEach(p => p.classList.remove('checked'));
  el.classList.add('checked');
  if (hiddenId) document.getElementById(hiddenId).value = value;
  saveFormState();
}

function chipToggle(el) {
  el.classList.toggle('checked');
  saveFormState();
}

function setFilename(inputId, displayId) {
  const f = document.getElementById(inputId).files[0];
  document.getElementById(displayId).textContent = f ? f.name : 'No file chosen';
}


function saveFormState() {
  if (!activeType || !typeConfig[activeType]) return;
  var form = document.getElementById(typeConfig[activeType].form);
  if (!form) return;
  var state = { type: activeType, fields: {}, pills: {}, chips: {}, names: {} };
  form.querySelectorAll('input:not([type=file]):not([type=hidden]), select, textarea').forEach(function(el) {
    if (el.name) state.fields[el.name] = el.value;
  });
  // Save name-dropdown hidden inputs (excluded by the selector above)
  ['user_name_val', 'req_name_val', 'approver_name_val'].forEach(function(id) {
    var el = document.getElementById(id);
    if (el && el.name) state.fields[el.name] = el.value;
  });
  // Save name trigger display texts so the trigger label survives a refresh
  ['user_name', 'req_name', 'approver_name'].forEach(function(prefix) {
    var disp = document.getElementById(prefix + '_display');
    if (disp) state.names[prefix] = { text: disp.textContent, placeholder: disp.classList.contains('is-placeholder') };
  });
  // Sync Quill content to hidden input and capture it
  if (window._itrJustifyEditor) {
    var _jh = document.getElementById('itr-justify-hidden');
    if (_jh) _jh.value = window._itrJustifyEditor.root.innerHTML;
    state.fields['justification'] = window._itrJustifyEditor.root.innerHTML;
  }
  [['hw_request_type_val','hw-req-type']].forEach(function(p) {
    var el = document.getElementById(p[0]); if (el) state.pills[p[1]] = el.value;
  });
  ['hw-items-grid'].forEach(function(id) {
    var g = document.getElementById(id); if (!g) return;
    state.chips[id] = Array.from(g.querySelectorAll('.itr-check-chip.checked'))
                          .map(function(c) { return c.textContent.trim(); });
  });
  try { sessionStorage.setItem('itr_form_state', JSON.stringify(state)); } catch(e) {}
}

function validateAndSubmit(type, event) {
  // Use event.submitter (which button triggered submit) — immune to onclick order or hidden-input state
  var isDraft = !!(event && event.submitter && event.submitter.value === 'draft');

  if (isDraft) {
    sessionStorage.removeItem('itr_form_state');
    return true;
  }

  var form = document.getElementById('form-' + type);
  form.querySelectorAll('.itr-js-error').forEach(function(el) { el.style.display = 'none'; });

  var firstErr = null;
  function showErr(id) {
    var el = document.getElementById(id);
    if (el) { el.style.display = 'flex'; if (!firstErr) firstErr = el; }
  }

  if (type === 'hardware') {
    if (!document.getElementById('hw_request_type_val').value) showErr('err-hw-req-type');
    var hwItemInput = document.querySelector('#form-hardware input[name="hw_items[]"]');
    if (!hwItemInput || !hwItemInput.value.trim()) showErr('err-hw-items');
  }

  if (firstErr) {
    firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
    return false;
  }
  sessionStorage.removeItem('itr_form_state');
  return true;
}

// Collect checked chips into hidden array inputs before form submit
function collectChips(gridId, fieldName) {
  const grid = document.getElementById(gridId);
  if (!grid) return;
  // Remove previously injected hidden inputs for this field
  const form = grid.closest('form');
  form.querySelectorAll('input[name="' + fieldName + '[]"]').forEach(el => el.remove());
  // Re-inject checked ones
  grid.querySelectorAll('.itr-check-chip.checked').forEach(chip => {
    const label = chip.textContent.trim();
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = fieldName + '[]';
    input.value = label;
    form.appendChild(input);
  });
}

// ── Restore state after validation redirect-back ──────────────
(function() {
  var oldType = @json(old('request_type'));
  if (!oldType || !typeConfig[oldType]) return;

  activeType = oldType;
  Object.keys(typeConfig).forEach(function(t) {
    var card = document.getElementById('card-' + t);
    if (card) {
      card.classList.toggle('selected', t === oldType);
      card.classList.toggle('locked', t !== oldType);
    }
  });
  var cfg = typeConfig[oldType];
  document.getElementById('banner-icon').className = 'bi ' + cfg.icon;
  document.getElementById('banner-label').textContent = cfg.label + ' Request';
  Object.values(typeConfig).forEach(function(c) {
    document.getElementById(c.form).style.display = 'none';
  });
  document.getElementById(cfg.form).style.display = 'block';
  document.getElementById('prog-step1').className = 'itr-step done';
  document.getElementById('prog-step2').className = 'itr-step active';
  document.getElementById('prog-line').className = 'itr-step-line done';
  document.getElementById('step1').style.display = 'none';
  var msEl = document.getElementById('my-submissions');
  if (msEl) msEl.style.display = 'none';
  var houSecEl = document.getElementById('hou-pending-section');
  if (houSecEl) houSecEl.style.display = 'none';
  var valSecEl = document.getElementById('validator-pending-section');
  if (valSecEl) valSecEl.style.display = 'none';
  var s2 = document.getElementById('step2');
  s2.style.display = 'block';
  s2.style.opacity = '1';

  restoreOldPill('hw-req-type',  'hw_request_type_val', @json(old('hw_request_type')));
})();

function restoreOldPill(groupId, hiddenId, value) {
  if (!value) return;
  var group = document.getElementById(groupId);
  if (!group) return;
  group.querySelectorAll('.itr-radio-pill').forEach(function(pill) {
    var m = (pill.getAttribute('onclick') || '').match(/'([^']+)'\s*\)\s*$/);
    if (m && m[1] === value) {
      group.querySelectorAll('.itr-radio-pill').forEach(function(p) { p.classList.remove('checked'); });
      pill.classList.add('checked');
      var el = document.getElementById(hiddenId);
      if (el) el.value = value;
    }
  });
}

function restoreOldChips(gridId, values) {
  if (!values || !values.length) return;
  var grid = document.getElementById(gridId);
  if (!grid) return;
  grid.querySelectorAll('.itr-check-chip').forEach(function(chip) {
    if (values.indexOf(chip.textContent.trim()) !== -1) {
      chip.classList.add('checked');
    }
  });
}

// ── Persist form state across page refreshes ──────────────────
function restoreFromSession() {
  if (@json(old('request_type'))) return; // already handled by Laravel old()
  var saved = sessionStorage.getItem('itr_form_state');
  if (!saved) return;
  var state;
  try { state = JSON.parse(saved); } catch(e) { return; }
  if (!state || !state.type || !typeConfig[state.type]) return;

  var cfg = typeConfig[state.type];
  activeType = state.type;
  Object.keys(typeConfig).forEach(function(t) {
    var card = document.getElementById('card-' + t);
    if (card) {
      card.classList.toggle('selected', t === state.type);
      card.classList.toggle('locked',   t !== state.type);
    }
  });
  document.getElementById('banner-icon').className   = 'bi ' + cfg.icon;
  document.getElementById('banner-label').textContent = cfg.label + ' Request';
  Object.values(typeConfig).forEach(function(c) { document.getElementById(c.form).style.display = 'none'; });
  document.getElementById(cfg.form).style.display = 'block';
  document.getElementById('prog-step1').className = 'itr-step done';
  document.getElementById('prog-step2').className = 'itr-step active';
  document.getElementById('prog-line').className  = 'itr-step-line done';
  document.getElementById('step1').style.display  = 'none';
  var msEl = document.getElementById('my-submissions');
  if (msEl) msEl.style.display = 'none';
  var houSecEl = document.getElementById('hou-pending-section');
  if (houSecEl) houSecEl.style.display = 'none';
  var valSecEl = document.getElementById('validator-pending-section');
  if (valSecEl) valSecEl.style.display = 'none';
  var s2 = document.getElementById('step2');
  s2.style.display = 'block'; s2.style.opacity = '1';

  var form = document.getElementById(cfg.form);
  Object.keys(state.fields || {}).forEach(function(name) {
    form.querySelectorAll('[name="' + name + '"]').forEach(function(el) {
      if (el.type !== 'file') el.value = state.fields[name];
    });
  });
  // Restore name trigger display texts
  Object.keys(state.names || {}).forEach(function(prefix) {
    var n = state.names[prefix];
    var disp = document.getElementById(prefix + '_display');
    if (disp) {
      disp.textContent = n.text;
      disp.classList.toggle('is-placeholder', n.placeholder);
    }
  });

  restoreOldPill('hw-req-type', 'hw_request_type_val', (state.pills||{})['hw-req-type']);
}

restoreFromSession();

['form-hardware'].forEach(function(id) {
  var f = document.getElementById(id);
  if (f) { f.addEventListener('input', saveFormState); f.addEventListener('change', saveFormState); }
});

// ── Staff / Approver Searchable Dropdowns ────────────────────â”€
const staffList = @json($staffList ?? []);
const houList   = @json($houList ?? []);

function _esc(s) {
  return String(s||'').replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

// Lift all panels to <body> at startup so they are never clipped by
// parent overflow:hidden or trapped by a parent CSS transform context.
document.querySelectorAll('.itr-name-panel').forEach(function(p) {
  document.body.appendChild(p);
});

function _positionPanel(prefix) {
  var t = document.getElementById(prefix + '_trigger');
  var p = document.getElementById(prefix + '_panel');
  if (!t || !p) return;
  var r = t.getBoundingClientRect();
  p.style.top   = (r.bottom + 4) + 'px';
  p.style.left  = r.left + 'px';
  p.style.width = r.width + 'px';
}

function _closeAllDD() {
  document.querySelectorAll('.itr-name-panel').forEach(function(p) { p.style.display = 'none'; });
  document.querySelectorAll('.itr-name-trigger.is-open').forEach(function(t) { t.classList.remove('is-open'); });
}

// ── Staff (user / requester) dropdown ────────────────────────â”€
function renderStaffList(prefix, query) {
  var q = query.trim().toLowerCase();
  var items = q ? staffList.filter(function(s){ return s.name && s.name.toLowerCase().includes(q); }) : staffList;
  var el = document.getElementById(prefix + '_list');
  if (!el) return;
  if (!items.length) { el.innerHTML = '<div class="itr-name-noresult">No results found</div>'; return; }
  el.innerHTML = items.map(function(s) {
    return '<div class="itr-name-item" onmousedown="pickStaff(\'' + prefix + '\',this)"'
      + ' data-name="'     + _esc(s.name)     + '"'
      + ' data-dept="'     + _esc(s.dept)     + '"'
      + ' data-staff-no="' + _esc(s.staff_no) + '"'
      + ' data-email="'    + _esc(s.email)    + '"'
      + ' data-position="' + _esc(s.position) + '"'
      + ' data-phone="'    + _esc(s.phone)    + '">'
      + '<div class="itr-name-item-name">' + _esc(s.name) + '</div>'
      + (s.dept ? '<div class="itr-name-item-dept">' + _esc(s.dept) + '</div>' : '')
      + '</div>';
  }).join('');
}

function openStaffDD(prefix) {
  _closeAllDD();
  var panel   = document.getElementById(prefix + '_panel');
  var trigger = document.getElementById(prefix + '_trigger');
  if (!panel) return;
  _positionPanel(prefix);
  panel.style.display = 'block';
  if (trigger) trigger.classList.add('is-open');
  renderStaffList(prefix, '');
  var si = document.getElementById(prefix + '_search');
  if (si) {
    si.value = '';
    setTimeout(function() { si.focus(); }, 0);
  }
}
function closeStaffDD(prefix) {
  var panel   = document.getElementById(prefix + '_panel');
  var trigger = document.getElementById(prefix + '_trigger');
  if (panel)   panel.style.display = 'none';
  if (trigger) trigger.classList.remove('is-open');
}
function toggleStaffDD(prefix) {
  var panel = document.getElementById(prefix + '_panel');
  if (!panel) return;
  if (panel.style.display === 'none') openStaffDD(prefix); else closeStaffDD(prefix);
}
function filterStaffDD(prefix, query) { renderStaffList(prefix, query); }

function pickStaff(prefix, el) {
  var name     = el.dataset.name     || '';
  var dept     = el.dataset.dept     || '';
  var staffNo  = el.dataset.staffNo  || '';
  var email    = el.dataset.email    || '';
  var position = el.dataset.position || '';
  var phone    = el.dataset.phone    || '';

  var valEl  = document.getElementById(prefix + '_val');
  var dispEl = document.getElementById(prefix + '_display');
  if (valEl)  valEl.value = name;
  if (dispEl) { dispEl.textContent = name; dispEl.classList.remove('is-placeholder'); }
  closeStaffDD(prefix);

  var scope = (activeType ? document.getElementById('form-' + activeType) : null) || document;
  function setF(sel, val) { var f = scope.querySelector(sel); if (f) f.value = val; }

  if (prefix === 'user_name') {
    setF('[name="user_department"]',  dept);
    setF('[name="user_staff_id"]',    staffNo);
    setF('[name="user_email"]',       email);
    setF('[name="user_contact"]',     phone);
    setF('[name="user_designation"]', position);
    // Mirror into Requester Details only when the selected person is the logged-in user
    if (_accountName && name === _accountName) {
      var reqVal  = document.getElementById('req_name_val');
      var reqDisp = document.getElementById('req_name_display');
      if (reqVal)  reqVal.value = name;
      if (reqDisp) { reqDisp.textContent = name; reqDisp.classList.remove('is-placeholder'); }
      setF('[name="req_department"]',   dept);
      setF('[name="req_staff_id"]',     staffNo);
      setF('[name="req_contact"]',      email);
      setF('[name="req_designation"]',  position);
    }
  } else if (prefix === 'approver_name') {
    setF('[name="approver_department"]',  dept);
    setF('[name="approver_contact"]',     email);
    setF('[name="approver_designation"]', position);
  } else {
    setF('[name="req_department"]',   dept);
    setF('[name="req_staff_id"]',     staffNo);
    setF('[name="req_contact"]',      email);
    setF('[name="req_designation"]',  position);
  }
  saveFormState();
}

// ── HOU (approver) dropdown ──────────────────────────────────â”€
function renderHouList(prefix, query) {
  var q = query.trim().toLowerCase();
  var items = q ? houList.filter(function(s){ return s.name && s.name.toLowerCase().includes(q); }) : houList;
  var el = document.getElementById(prefix + '_list');
  if (!el) return;
  if (!items.length) { el.innerHTML = '<div class="itr-name-noresult">No results found</div>'; return; }
  el.innerHTML = items.map(function(s) {
    return '<div class="itr-name-item" onmousedown="pickHou(\'' + prefix + '\',this)"'
      + ' data-name="' + _esc(s.name) + '" data-dept="' + _esc(s.dept) + '" data-email="' + _esc(s.email) + '">'
      + '<div class="itr-name-item-name">' + _esc(s.name) + '</div>'
      + (s.dept ? '<div class="itr-name-item-dept">' + _esc(s.dept) + '</div>' : '')
      + '</div>';
  }).join('');
}

function openHouDD(prefix) {
  _closeAllDD();
  var panel   = document.getElementById(prefix + '_panel');
  var trigger = document.getElementById(prefix + '_trigger');
  if (!panel) return;
  _positionPanel(prefix);
  panel.style.display = 'block';
  if (trigger) trigger.classList.add('is-open');
  renderHouList(prefix, '');
  var si = document.getElementById(prefix + '_search');
  if (si) {
    si.value = '';
    setTimeout(function() { si.focus(); }, 0);
  }
}
function closeHouDD(prefix) {
  var panel   = document.getElementById(prefix + '_panel');
  var trigger = document.getElementById(prefix + '_trigger');
  if (panel)   panel.style.display = 'none';
  if (trigger) trigger.classList.remove('is-open');
}
function toggleHouDD(prefix) {
  var panel = document.getElementById(prefix + '_panel');
  if (!panel) return;
  if (panel.style.display === 'none') openHouDD(prefix); else closeHouDD(prefix);
}
function filterHouDD(prefix, query) { renderHouList(prefix, query); }

function pickHou(prefix, el) {
  var name  = el.dataset.name  || '';
  var dept  = el.dataset.dept  || '';
  var email = el.dataset.email || '';

  var valEl  = document.getElementById(prefix + '_val');
  var dispEl = document.getElementById(prefix + '_display');
  if (valEl)  valEl.value = name;
  if (dispEl) { dispEl.textContent = name; dispEl.classList.remove('is-placeholder'); }
  closeHouDD(prefix);

  var scope = (activeType ? document.getElementById('form-' + activeType) : null) || document;
  function setF(sel, val) { var f = scope.querySelector(sel); if (f) f.value = val; }
  setF('[name="approver_department"]', dept);
  setF('[name="approver_contact"]',    email);
  saveFormState();
}

// Close panels when the user scrolls the page (no capture — only fires on window scroll,
// not on element scrolls or programmatic scrolls triggered by focus/layout changes).
window.addEventListener('scroll', function() { _closeAllDD(); });

// Close panels on mousedown outside (using mousedown ensures item picks via onmousedown win the race)
document.addEventListener('mousedown', function(e) {
  document.querySelectorAll('.itr-name-dd').forEach(function(wrap) {
    if (wrap.contains(e.target)) return;
    var panelId = wrap.id.replace(/_wrap$/, '_panel');
    var panel   = document.getElementById(panelId);
    if (panel && panel.contains(e.target)) return;
    if (panel) panel.style.display = 'none';
    var t = wrap.querySelector('.itr-name-trigger');
    if (t) t.classList.remove('is-open');
  });
});

// Validate name dropdowns on submit (skip for draft)
document.getElementById('form-hardware').addEventListener('submit', function(e) {
  if (e.submitter && e.submitter.value === 'draft') return;
  var ok = true;
  [
    { id: 'user_name_val',     wrap: 'user_name_wrap'     },
    { id: 'req_name_val',      wrap: 'req_name_wrap'      },
    { id: 'approver_name_val', wrap: 'approver_name_wrap' },
  ].forEach(function(f) {
    var v = document.getElementById(f.id);
    if (v && !v.value.trim()) {
      ok = false;
      var w = document.getElementById(f.wrap);
      if (w) {
        var t = w.querySelector('.itr-name-trigger');
        if (t) { t.style.borderColor = '#dc2626'; setTimeout(function(){ t.style.borderColor = ''; }, 2500); }
      }
    }
  });
  if (!ok) e.preventDefault();
});

// Navigation-away guard: intercept sidebar/module links while the form is active
(function() {
  // Move modal to <body> so position:fixed isn't broken by a transformed ancestor
  document.body.appendChild(document.getElementById('itr-nav-confirm-modal'));
  var pendingNavHref = null;

  function _step2Active() {
    var s2 = document.getElementById('step2');
    return s2 && s2.style.display !== 'none' && s2.style.display !== '';
  }

  document.addEventListener('click', function(e) {
    var a = e.target.closest('a[href]');
    if (!a) return;
    var href = a.getAttribute('href');
    if (!href || href === '#' || href.startsWith('javascript')) return;
    if (!_step2Active()) return;
    try {
      var dest = new URL(href, window.location.href);
      if (dest.pathname === window.location.pathname) return;
    } catch(ex) { return; }
    e.preventDefault();
    e.stopPropagation();
    pendingNavHref = dest.href;
    document.getElementById('itr-nav-confirm-modal').style.display = 'flex';
  }, true);

  document.getElementById('itr-nav-cancel-btn').addEventListener('click', function() {
    pendingNavHref = null;
    document.getElementById('itr-nav-confirm-modal').style.display = 'none';
  });

  document.getElementById('itr-nav-discard-btn').addEventListener('click', function() {
    sessionStorage.removeItem('itr_form_state');
    document.getElementById('itr-nav-confirm-modal').style.display = 'none';
    if (pendingNavHref) window.location.href = pendingNavHref;
  });

  document.getElementById('itr-nav-draft-btn').addEventListener('click', function() {
    document.getElementById('itr-nav-confirm-modal').style.display = 'none';
    var draftBtn = document.querySelector('button[name="action"][value="draft"]');
    if (draftBtn) draftBtn.click();
  });
})();
</script>
@endif {{-- end isStaff else --}}
@endif
@endsection

@push('styles')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<style>
.itr-quill-wrap{border:1.5px solid var(--border);border-radius:8px;overflow:hidden;transition:border-color .15s,box-shadow .15s;}
.itr-quill-wrap.focused{border-color:var(--accent)!important;box-shadow:0 0 0 3px rgba(2,132,199,.1)!important;}
.itr-quill-wrap.is-error{border-color:#dc2626!important;box-shadow:0 0 0 3px rgba(220,38,38,.1)!important;}
.itr-quill-wrap .ql-toolbar.ql-snow{border:none!important;border-bottom:1px solid var(--border)!important;background:var(--body-bg);font-family:'Inter',sans-serif;padding:6px 10px;}
.itr-quill-wrap .ql-container.ql-snow{border:none!important;background:var(--surface);}
.itr-quill-wrap .ql-editor{min-height:180px;padding:9px 13px;font-family:'Inter',sans-serif;font-size:13.5px;color:var(--text);}
.itr-quill-wrap .ql-editor.ql-blank::before{color:var(--muted);opacity:.6;font-style:normal;}
</style>
@endpush

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
(function(){
  if (!document.getElementById('itr-justify-editor')) return;

  window._itrJustifyEditor = new Quill('#itr-justify-editor', {
    theme: 'snow',
    placeholder: 'Describe why this request is needed…',
    modules: {
      toolbar: [
        [{ font: [] }, { size: [] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ color: [] }, { background: [] }],
        [{ list: 'ordered' }, { list: 'bullet' }],
        [{ align: [] }],
        ['clean']
      ]
    }
  });

  var _jv = {!! json_encode(old('justification')) !!};
  if (_jv) {
    window._itrJustifyEditor.root.innerHTML = _jv;
  } else {
    try {
      var _ss = JSON.parse(sessionStorage.getItem('itr_form_state') || 'null');
      if (_ss && _ss.fields && _ss.fields.justification) {
        window._itrJustifyEditor.root.innerHTML = _ss.fields.justification;
      }
    } catch(e) {}
  }

  window._itrJustifyEditor.on('text-change', function() { saveFormState(); });

  window._itrJustifyEditor.on('selection-change', function(range) {
    var wrap = document.getElementById('itr-justify-wrap');
    if (wrap) wrap.classList.toggle('focused', !!range);
  });

  var _orig = window.validateAndSubmit;
  window.validateAndSubmit = function(type, event) {
    var hidden = document.getElementById('itr-justify-hidden');
    if (hidden && window._itrJustifyEditor) {
      hidden.value = window._itrJustifyEditor.root.innerHTML;
    }
    return _orig(type, event);
  };
})();
</script>
@endpush
