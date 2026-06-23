@extends('it.layouts.app')

@section('title', 'IT Request Form')
@section('page_title', 'IT Request Form')

@section('content')
@if($user->isAdmin())

{{-- ══════════════════════════════════════════
     IT ADMIN — INBOX VIEW
══════════════════════════════════════════ --}}
<style>
.itr-admin-stat { background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:18px 20px;display:flex;align-items:center;gap:14px;transition:box-shadow .15s; }
.itr-admin-stat:hover { box-shadow:0 4px 18px rgba(0,0,0,.07); }
.itr-admin-stat-icon { width:42px;height:42px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:19px;flex-shrink:0; }
.itr-admin-stat-val { font-family:'DM Sans',sans-serif;font-size:26px;font-weight:800;color:var(--text);line-height:1; }
.itr-admin-stat-lbl { font-size:11.5px;color:var(--muted);margin-top:3px;font-weight:500; }

.itr-type-pill { display:inline-flex;align-items:center;gap:5px;border-radius:20px;padding:2px 10px;font-size:11px;font-weight:700;line-height:1.8; }
.itr-status-dot { display:inline-flex;align-items:center;gap:5px;border-radius:20px;padding:4px 11px;font-size:11.5px;font-weight:600;white-space:nowrap; }
.itr-status-dot span { width:6px;height:6px;border-radius:50%;flex-shrink:0;display:inline-block; }

.itr-admin-row { display:grid;align-items:center;border-bottom:1px solid var(--border);transition:background .12s;cursor:pointer;text-decoration:none;color:inherit; }
.itr-admin-row:hover { background:var(--body-bg); }
.itr-admin-row:last-child { border-bottom:none; }
.itr-admin-row.is-new { border-left:3px solid #d97706; }
.itr-admin-row.is-new:not(:hover) { background:rgba(217,119,6,.025); }

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
    <h4 style="font-family:'DM Sans',sans-serif;font-weight:800;font-size:22px;color:var(--text);margin:0 0 3px">IT Request Inbox</h4>
    <p style="font-size:13px;color:var(--muted);margin:0">Review and manage all IT service requests from staff</p>
  </div>
  @if($countNew > 0)
  <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(217,119,6,.1);border:1.5px solid rgba(217,119,6,.3);border-radius:12px;padding:10px 18px">
    <span style="width:8px;height:8px;background:#d97706;border-radius:50%;display:inline-block;animation:itr-pulse 1.6s infinite"></span>
    <span style="font-size:13px;font-weight:700;color:#d97706">{{ $countNew }} pending review</span>
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

{{-- Stat cards (4 cards) --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:22px" class="itr-admin-cols">
  <div class="itr-admin-stat">
    <div class="itr-admin-stat-icon" style="background:rgba(2,132,199,.1);color:#0284c7"><i class="bi bi-file-earmark-text-fill"></i></div>
    <div>
      <div class="itr-admin-stat-val">{{ $total }}</div>
      <div class="itr-admin-stat-lbl">Total Requests</div>
    </div>
  </div>
  <div class="itr-admin-stat" style="border-color:{{ $countNew ? 'rgba(217,119,6,.35)' : 'var(--border)' }}">
    <div class="itr-admin-stat-icon" style="background:rgba(217,119,6,.1);color:#d97706"><i class="bi bi-hourglass-split"></i></div>
    <div>
      <div class="itr-admin-stat-val" style="{{ $countNew ? 'color:#d97706' : '' }}">{{ $countNew }}</div>
      <div class="itr-admin-stat-lbl">Pending Review</div>
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
          style="width:100%;padding:8px 12px 8px 32px;font-family:'DM Sans',sans-serif;font-size:13px;border:1.5px solid var(--border);border-radius:8px;background:var(--surface);color:var(--text);outline:none;box-sizing:border-box;transition:border-color .15s"
          onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
      </div>
      <select name="itr_type" onchange="this.form.submit()"
        style="font-family:'DM Sans',sans-serif;font-size:13px;padding:8px 30px 8px 12px;border:1.5px solid var(--border);border-radius:8px;background:var(--surface);color:{{ $type ? 'var(--text)' : 'var(--muted)' }};outline:none;cursor:pointer;appearance:none;background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E\");background-repeat:no-repeat;background-position:right 10px center">
        <option value="">All Types</option>
        <option value="hardware" {{ $type === 'hardware' ? 'selected' : '' }}>Hardware</option>
        <option value="software" {{ $type === 'software' ? 'selected' : '' }}>Software</option>
        <option value="system"   {{ $type === 'system'   ? 'selected' : '' }}>System</option>
        <option value="service"  {{ $type === 'service'  ? 'selected' : '' }}>Service</option>
      </select>
      <select name="itr_status" onchange="this.form.submit()"
        style="font-family:'DM Sans',sans-serif;font-size:13px;padding:8px 30px 8px 12px;border:1.5px solid var(--border);border-radius:8px;background:var(--surface);color:{{ $status ? 'var(--text)' : 'var(--muted)' }};outline:none;cursor:pointer;appearance:none;background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2020/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E\");background-repeat:no-repeat;background-position:right 10px center">
        <option value="">All Status</option>
        <option value="New"      {{ $status === 'New'      ? 'selected' : '' }}>Pending Review</option>
        <option value="Approved" {{ $status === 'Approved' ? 'selected' : '' }}>Approved</option>
        <option value="Rejected" {{ $status === 'Rejected' ? 'selected' : '' }}>Rejected</option>
        <option value="Draft"    {{ $status === 'Draft'    ? 'selected' : '' }}>Draft</option>
      </select>
      <button type="submit"
        style="font-family:'DM Sans',sans-serif;font-size:13px;font-weight:600;padding:8px 16px;background:var(--accent);color:#fff;border:none;border-radius:8px;cursor:pointer;display:flex;align-items:center;gap:6px;transition:background .15s"
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

  {{-- Column headers --}}
  <div style="display:grid;grid-template-columns:2fr 1fr 1fr 130px 120px;padding:10px 20px;background:var(--body-bg);border-bottom:1px solid var(--border)">
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
  <a href="{{ route('it.it-request-form.show', $form->id) }}"
    class="itr-admin-row {{ $form->status === 'New' ? 'is-new' : '' }}"
    style="grid-template-columns:2fr 1fr 1fr 130px 120px;padding:14px 20px;gap:12px">

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
        <div style="font-size:11px;color:var(--muted);margin-top:1px">{{ $form->submittedBy?->roleName() ?? '' }}</div>
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
        <span style="background:#d97706"></span>Pending
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

@else
{{-- ══════════════════════════════════════════
     NON-ADMIN — EXISTING 2-STEP WIZARD
══════════════════════════════════════════ --}}
<style>
/* ══════════════════════════════════════════
   IT REQUEST FORM — WIZARD REDESIGN
══════════════════════════════════════════ */
.itr-wrap { max-width: 900px; }

.itr-page-title {
  font-family:'DM Sans',sans-serif;
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

/* ── Step 1 type cards ── */
.itr-type-grid { display: grid; grid-template-columns: 1fr; gap: 14px; margin-bottom: 8px; max-width: 320px; }
.itr-type-card {
  background: var(--surface); border: 2px solid var(--border);
  border-radius: 14px; padding: 22px 16px; cursor: pointer;
  transition: all .2s; text-align: center; position: relative; user-select: none;
}
.itr-type-card:hover { border-color: var(--accent); box-shadow: 0 4px 20px rgba(2,132,199,.15); transform: translateY(-2px); }
.itr-type-card.selected { border-color: var(--accent); background: rgba(2,132,199,.06); box-shadow: 0 4px 20px rgba(2,132,199,.2); }
.itr-type-card.locked { opacity: .35; cursor: not-allowed; pointer-events: none; filter: grayscale(.4); }
.itr-type-icon { width: 52px; height: 52px; border-radius: 14px; margin: 0 auto 14px; display: flex; align-items: center; justify-content: center; font-size: 24px; transition: all .2s; }
.hw-icon { background: rgba(59,130,246,.1); color: #3b82f6; }
.sw-icon { background: rgba(139,92,246,.1); color: #8b5cf6; }
.sys-icon { background: rgba(16,185,129,.1); color: #10b981; }
.svc-icon { background: rgba(2,132,199,.1); color: var(--accent); }
.itr-type-card.selected .itr-type-icon { transform: scale(1.08); }
.itr-type-name { font-family:'DM Sans',sans-serif; font-size: 14px; font-weight: 700; color: var(--text); margin-bottom: 6px; }
.itr-type-desc { font-size: 11.5px; color: var(--muted); line-height: 1.5; }
.itr-type-check { position: absolute; top: 10px; right: 10px; width: 20px; height: 20px; border-radius: 50%; background: var(--accent); color: white; display: none; align-items: center; justify-content: center; font-size: 11px; }
.itr-type-card.selected .itr-type-check { display: flex; }
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
  font-family:'DM Sans',sans-serif;
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
.itr-section-title { font-family:'DM Sans',sans-serif; font-size: 13.5px; font-weight: 700; color: var(--text); flex: 1; }
.itr-section-body { padding: 20px; }

/* ── Fields ── */
.itr-label { font-size: 12px; font-weight: 600; color: var(--text); margin-bottom: 6px; display: flex; align-items: center; gap: 3px; }
.itr-req { color: var(--red); }
.itr-hint { font-size: 11px; color: var(--muted); margin-top: 4px; }
.itr-input { width: 100%; font-family:'DM Sans',sans-serif; font-size: 13px; color: var(--text); background: var(--surface); border: 1.5px solid var(--border); border-radius: 8px; padding: 9px 12px; outline: none; transition: border-color .15s, box-shadow .15s; }
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
.itr-browse-btn { font-family:'DM Sans',sans-serif; font-size: 12.5px; font-weight: 600; padding: 8px 18px; background: var(--body-bg); border: 1.5px solid var(--border); border-radius: 7px; color: var(--text); cursor: pointer; transition: all .15s; }
.itr-browse-btn:hover { border-color: var(--accent); color: var(--accent); }

.itr-divider { border: none; border-top: 1px solid var(--border); margin: 18px 0; }

/* ── Inline field errors ── */
.itr-field-error { color: #dc2626; font-size: 11.5px; margin-top: 4px; display: flex; align-items: center; gap: 4px; }
.itr-field-error i { font-size: 12px; flex-shrink: 0; }
.itr-input.is-error { border-color: #dc2626 !important; box-shadow: 0 0 0 3px rgba(220,38,38,.1) !important; }

/* ── Actions ── */
.itr-action-bar { display: flex; gap: 10px; margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border); }
.itr-btn-submit { font-family:'DM Sans',sans-serif; font-size: 13.5px; font-weight: 700; padding: 11px 28px; background: var(--accent); color: white; border: none; border-radius: 9px; cursor: pointer; transition: background .15s; display: flex; align-items: center; gap: 7px; }
.itr-btn-submit:hover { background: var(--accent-h); }
.itr-btn-draft { font-family:'DM Sans',sans-serif; font-size: 13px; font-weight: 600; padding: 11px 22px; background: var(--body-bg); border: 1.5px solid var(--border); border-radius: 9px; color: var(--text); cursor: pointer; transition: all .15s; display: flex; align-items: center; gap: 7px; }
.itr-btn-draft:hover { border-color: var(--accent); color: var(--accent); }

@media (max-width: 720px) {
  .g2,.g3,.g4,.itr-subject-row { grid-template-columns: 1fr; }
}
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

  <!-- ══ STEP 1 ══ -->
  <div id="step1">
    <div class="itr-type-grid">
      <div class="itr-type-card" id="card-hardware" onclick="selectType('hardware')">
        <div class="itr-type-check"><i class="bi bi-check"></i></div>
        <div class="itr-type-icon hw-icon"><i class="bi bi-laptop"></i></div>
        <div class="itr-type-name">Hardware</div>
        <div class="itr-type-desc">Laptops, desktops, printers, phones &amp; peripherals</div>
      </div>
    </div>
    <div class="itr-locked-note" id="locked-note"></div>
  </div>

  <!-- ══ MY SUBMISSIONS ══ -->
  @if(isset($myForms) && $myForms->count())
  <div style="margin-top:28px" id="my-submissions">
    <div style="font-family:'DM Sans',sans-serif;font-size:15px;font-weight:800;color:var(--text);margin-bottom:4px">My Submissions</div>
    <div style="font-size:13px;color:var(--muted);margin-bottom:14px">Track the status of your previously submitted IT service requests.</div>
    <div style="display:flex;flex-direction:column;gap:10px">
      @foreach($myForms as $mf)
      @php
        $mfTypeMap = [
          'hardware' => ['label'=>'Hardware','color'=>'#3b82f6','bg'=>'rgba(59,130,246,.1)','icon'=>'bi-laptop'],
          'software' => ['label'=>'Software','color'=>'#8b5cf6','bg'=>'rgba(139,92,246,.1)','icon'=>'bi-code-slash'],
          'system'   => ['label'=>'System',  'color'=>'#10b981','bg'=>'rgba(16,185,129,.1)','icon'=>'bi-hdd-network'],
          'service'  => ['label'=>'Service', 'color'=>'#0284c7','bg'=>'rgba(2,132,199,.1)', 'icon'=>'bi-wifi'],
        ];
        $mt = $mfTypeMap[$mf->request_type] ?? ['label'=>ucfirst($mf->request_type),'color'=>'#64748b','bg'=>'rgba(100,116,139,.1)','icon'=>'bi-question-circle'];
      @endphp
      <div style="background:var(--surface);border:1.5px solid var(--border);border-radius:12px;padding:14px 18px;display:flex;align-items:center;gap:14px;flex-wrap:wrap;transition:border-color .15s"
        onmouseover="this.style.borderColor='var(--accent)'" onmouseout="this.style.borderColor='var(--border)'">

        {{-- Type badge --}}
        <span style="display:inline-flex;align-items:center;gap:6px;background:{{ $mt['bg'] }};color:{{ $mt['color'] }};border-radius:20px;padding:4px 12px;font-size:12px;font-weight:700;flex-shrink:0">
          <i class="bi {{ $mt['icon'] }}"></i>{{ $mt['label'] }}
        </span>

        {{-- Subject --}}
        <div style="flex:1;min-width:0">
          <div style="font-size:13.5px;font-weight:700;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $mf->subject ?? 'Untitled Draft' }}</div>
          <div style="font-size:11.5px;color:var(--muted);margin-top:2px">{{ $mf->status === 'Draft' ? 'Saved' : 'Submitted' }} {{ $mf->created_at->format('d M Y') }}</div>
        </div>

        {{-- Remarks preview (if any) --}}
        @if($mf->approval_remarks)
        <div style="font-size:12px;color:var(--muted);max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;flex-shrink:0" title="{{ $mf->approval_remarks }}">
          <i class="bi bi-chat-left-text" style="margin-right:3px"></i>{{ $mf->approval_remarks }}
        </div>
        @endif

        {{-- Status badge --}}
        @if($mf->status === 'Approved')
        <span style="display:inline-flex;align-items:center;gap:5px;background:rgba(22,163,74,.1);color:#16a34a;border-radius:20px;padding:4px 12px;font-size:12px;font-weight:700;flex-shrink:0">
          <i class="bi bi-check-circle-fill"></i> Approved
        </span>
        @elseif($mf->status === 'Rejected')
        <span style="display:inline-flex;align-items:center;gap:5px;background:rgba(220,38,38,.1);color:#dc2626;border-radius:20px;padding:4px 12px;font-size:12px;font-weight:700;flex-shrink:0">
          <i class="bi bi-x-circle-fill"></i> Rejected
        </span>
        @elseif($mf->status === 'New')
        <span style="display:inline-flex;align-items:center;gap:5px;background:rgba(217,119,6,.1);color:#d97706;border-radius:20px;padding:4px 12px;font-size:12px;font-weight:700;flex-shrink:0">
          <span style="width:7px;height:7px;background:#d97706;border-radius:50%;display:inline-block"></span> Pending Review
        </span>
        @elseif($mf->reviewed_by)
        <span style="display:inline-flex;align-items:center;gap:5px;background:rgba(217,119,6,.1);color:#d97706;border-radius:20px;padding:4px 12px;font-size:12px;font-weight:700;flex-shrink:0">
          <i class="bi bi-arrow-clockwise"></i> Needs Update
        </span>
        @else
        <span style="display:inline-flex;align-items:center;gap:5px;background:rgba(100,116,139,.1);color:#64748b;border-radius:20px;padding:4px 12px;font-size:12px;font-weight:700;flex-shrink:0">
          <i class="bi bi-floppy-fill"></i> Draft
        </span>
        @endif

        {{-- Resume Draft / Delete Draft buttons --}}
        @if($mf->status === 'Draft')
        <a href="{{ route('it.it-request-form.edit', $mf->id) }}"
          style="display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:700;color:var(--accent);border:1.5px solid var(--accent);border-radius:20px;padding:4px 12px;text-decoration:none;flex-shrink:0;transition:all .15s;background:transparent"
          onmouseover="this.style.background='rgba(2,132,199,.08)'"
          onmouseout="this.style.background='transparent'">
          <i class="bi bi-pencil-square"></i> Resume Draft
        </a>
        <form method="POST" action="{{ route('it.it-request-form.draft.destroy', $mf->id) }}"
          onsubmit="return confirm('Delete this draft? This cannot be undone.')" style="display:inline;margin:0">
          @csrf
          @method('DELETE')
          <button type="submit"
            style="display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:700;color:#dc2626;border:1.5px solid rgba(220,38,38,.35);border-radius:20px;padding:4px 12px;background:transparent;cursor:pointer;font-family:inherit;transition:all .15s"
            onmouseover="this.style.background='rgba(220,38,38,.07)'"
            onmouseout="this.style.background='transparent'">
            <i class="bi bi-trash3"></i> Delete
          </button>
        </form>
        @endif

        {{-- Date --}}
        <div style="font-size:12px;color:var(--muted);text-align:right;flex-shrink:0">
          {{ $mf->created_at->format('d/m/Y') }}
        </div>
      </div>
      @endforeach
    </div>
  </div>
  @endif

  <!-- ══ STEP 2 ══ -->
  <div id="step2">

    <div class="itr-selected-banner">
      <div class="itr-selected-pill"><i id="banner-icon"></i><span id="banner-label"></span></div>
      <div class="itr-banner-text">Fields marked <strong style="color:var(--red)">*</strong> are required only when submitting to IT Admin. You may <strong>Save as Draft</strong> at any time with incomplete fields.</div>
      <button type="button" class="itr-change-btn" onclick="changeType()"><i class="bi bi-arrow-left"></i> Change Type</button>
    </div>

    <!-- Shared subject row (rendered per-form via JS copy, but we place it inside each form below) -->

    <!-- ░░ HARDWARE ░░ -->
    <form id="form-hardware" style="display:none" method="POST" action="{{ route('it.it-request-form.store') }}" enctype="multipart/form-data" onsubmit="return validateAndSubmit('hardware', event)" novalidate>
      @csrf
      <input type="hidden" name="request_type" value="hardware">
      <!-- Hidden fields for pills/chips -->
      <input type="hidden" name="hw_request_type" id="hw_request_type_val">
      <!-- hw_items[] injected dynamically by JS -->

      <div class="itr-subject-row">
        <div class="fg" style="margin-bottom:0">
          <div class="itr-label">Request Subject <span class="itr-req">*</span></div>
          <input class="itr-input{{ $errors->has('subject') ? ' is-error' : '' }}" type="text" name="subject" value="{{ old('subject') }}" placeholder="Briefly describe your request…" maxlength="200" required/>
          @error('subject')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
          <div class="itr-hint">(i) Max 200 characters</div>
        </div>
        <div class="fg" style="margin-bottom:0">
          <div class="itr-label">Status</div>
          <input class="itr-input" type="text" value="New" readonly/>
        </div>
      </div>

      <div class="itr-section">
        <div class="itr-section-head"><div class="itr-section-num">1</div><div class="itr-section-title">Request Type &amp; Item Selection</div></div>
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
              <div class="itr-check-grid" id="hw-items-grid">
                <div class="itr-check-chip" onclick="chipToggle(this)"><span class="chip-dot"></span>Laptop</div>
                <div class="itr-check-chip" onclick="chipToggle(this)"><span class="chip-dot"></span>Desktop PC</div>
                <div class="itr-check-chip" onclick="chipToggle(this)"><span class="chip-dot"></span>Printer</div>
                <div class="itr-check-chip" onclick="chipToggle(this)"><span class="chip-dot"></span>Handphone</div>
                <div class="itr-check-chip" onclick="chipToggle(this)"><span class="chip-dot"></span>Tablet</div>
                <div class="itr-check-chip" onclick="chipToggle(this)"><span class="chip-dot"></span>IP Phone</div>
                <div class="itr-check-chip" onclick="chipToggle(this)"><span class="chip-dot"></span>Switch/Hub</div>
                <div class="itr-check-chip" onclick="chipToggle(this)"><span class="chip-dot"></span>UPS</div>
                <div class="itr-check-chip" onclick="chipToggle(this)"><span class="chip-dot"></span>Walkie-Talkie</div>
                <div class="itr-check-chip" onclick="chipToggle(this)"><span class="chip-dot"></span>Allow Install Software</div>
                <div class="itr-check-chip" onclick="chipToggle(this)"><span class="chip-dot"></span>Allow USB Drive</div>
                <div class="itr-check-chip" onclick="chipToggle(this)"><span class="chip-dot"></span>Color Printing Quota</div>
                <div class="itr-check-chip" onclick="chipToggle(this)"><span class="chip-dot"></span>Other</div>
              </div>
              @error('hw_items')<div class="itr-field-error" style="margin-top:6px"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
              <div class="itr-field-error itr-js-error" id="err-hw-items" style="display:none;margin-top:6px"><i class="bi bi-exclamation-circle-fill"></i> Please select at least one item.</div>
            </div>
          </div>
          <hr class="itr-divider"/>
          <div class="g2">
            <div class="fg">
              <div class="itr-label">PC/Laptop No. <span class="itr-req">*</span></div>
              <input class="itr-input{{ $errors->has('hw_pc_laptop_no') ? ' is-error' : '' }}" type="text" name="hw_pc_laptop_no" value="{{ old('hw_pc_laptop_no') }}" required/>
              @error('hw_pc_laptop_no')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
              <div class="itr-hint">Please provide your current PC/Laptop No.</div>
            </div>
            <div class="fg">
              <div class="itr-label">Printer No. <span class="itr-req">*</span></div>
              <input class="itr-input{{ $errors->has('hw_printer_no') ? ' is-error' : '' }}" type="text" name="hw_printer_no" value="{{ old('hw_printer_no') }}" required/>
              @error('hw_printer_no')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
              <div class="itr-hint">Please provide your current Printer No.</div>
            </div>
          </div>
        </div>
      </div>

      <div class="itr-section">
        <div class="itr-section-head"><div class="itr-section-num">2</div><div class="itr-section-title">Type of User &amp; Justification</div></div>
        <div class="itr-section-body">
          <div class="g2 fg">
            <div>
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
              <div class="fg"><div class="itr-label">Exit Date <span class="itr-req">*</span></div><input class="itr-input" type="date" name="exit_join_date" value="{{ old('exit_join_date') }}" style="max-width:200px" required/><div class="itr-hint">MM/DD/YYYY</div></div>
            </div>
            <div>
              <div class="itr-label">Justification <span class="itr-req">*</span></div>
              <textarea class="itr-input{{ $errors->has('justification') ? ' is-error' : '' }}" name="justification" placeholder="Describe why this request is needed…" required>{{ old('justification') }}</textarea>
              @error('justification')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
            </div>
          </div>
          <div class="itr-label" style="margin-bottom:10px">Supporting Document</div>
          <div class="itr-upload-zone">
            <div class="itr-notice warn"><i class="bi bi-exclamation-triangle-fill"></i><div><strong>(!) Bagi permohonan pertukaran laptop/desktop,</strong> sila sertakan <strong>Report Diagnosis dari Prodata</strong>. Permohonan akan ditolak sekiranya Report Diagnosis tidak disertakan.</div></div>
            <div class="itr-notice info"><i class="bi bi-info-circle-fill"></i><div>Sila pastikan nama lampiran tidak mengandungi simbol berikut: &amp; @ # $ % ^ * ( ) { } [ ] \ / : ' " dan saiz lampiran tidak melebihi <strong>2MB</strong></div></div>
            <div class="itr-upload-row"><div class="itr-filename" id="hw-fname">No file chosen</div><input type="file" id="hw-file" name="document" onchange="setFilename('hw-file','hw-fname')"/><label for="hw-file" class="itr-browse-btn"><i class="bi bi-paperclip"></i> Browse</label></div>
          </div>
        </div>
      </div>

      <div class="itr-section">
        <div class="itr-section-head"><div class="itr-section-num">3</div><div class="itr-section-title">User Details</div></div>
        <div class="itr-section-body">
          <div class="g2 fg">
            <div class="fg">
              <div class="itr-label">Name <span class="itr-req">*</span></div>
              <input class="itr-input{{ $errors->has('user_name') ? ' is-error' : '' }}" type="text" name="user_name" value="{{ old('user_name') }}" required/>
              @error('user_name')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
            </div>
            <div class="fg">
              <div class="itr-label">Email <span class="itr-req">*</span></div>
              <input class="itr-input{{ $errors->has('user_email') ? ' is-error' : '' }}" type="email" name="user_email" value="{{ old('user_email') }}" required/>
              @error('user_email')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
            </div>
          </div>
          <div class="fg">
            <div class="itr-label">Address <span class="itr-req">*</span></div>
            <input class="itr-input{{ $errors->has('user_address') ? ' is-error' : '' }}" type="text" name="user_address" value="{{ old('user_address') }}" required/>
            @error('user_address')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
          </div>
          <div class="g4">
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
            <div class="fg">
              <div class="itr-label">Staff ID <span class="itr-req">*</span></div>
              <input class="itr-input{{ $errors->has('user_staff_id') ? ' is-error' : '' }}" type="text" name="user_staff_id" value="{{ old('user_staff_id') }}" placeholder="12345678" required/>
              @error('user_staff_id')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
            </div>
            <div class="fg">
              <div class="itr-label">Contact No. <span class="itr-req">*</span></div>
              <input class="itr-input{{ $errors->has('user_contact') ? ' is-error' : '' }}" type="text" name="user_contact" value="{{ old('user_contact') }}" required/>
              @error('user_contact')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
            </div>
          </div>
        </div>
      </div>

      <div class="itr-section">
        <div class="itr-section-head"><div class="itr-section-num">4</div><div class="itr-section-title">Requester Details</div></div>
        <div class="itr-section-body">
          <div class="g3 fg">
            <div class="fg">
              <div class="itr-label">Name <span class="itr-req">*</span></div>
              <input class="itr-input{{ $errors->has('req_name') ? ' is-error' : '' }}" type="text" name="req_name" value="{{ old('req_name') }}" placeholder="Enter a name or email address…" required/>
              @error('req_name')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
            </div>
            <div class="fg">
              <div class="itr-label">Department <span class="itr-req">*</span></div>
              <input class="itr-input{{ $errors->has('req_department') ? ' is-error' : '' }}" type="text" name="req_department" value="{{ old('req_department') }}" required/>
              @error('req_department')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
            </div>
            <div class="fg">
              <div class="itr-label">Staff ID <span class="itr-req">*</span></div>
              <input class="itr-input{{ $errors->has('req_staff_id') ? ' is-error' : '' }}" type="text" name="req_staff_id" value="{{ old('req_staff_id') }}" required/>
              @error('req_staff_id')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
            </div>
          </div>
          <div class="g3">
            <div class="fg">
              <div class="itr-label">Designation <span class="itr-req">*</span></div>
              <input class="itr-input{{ $errors->has('req_designation') ? ' is-error' : '' }}" type="text" name="req_designation" value="{{ old('req_designation') }}" required/>
              @error('req_designation')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
            </div>
            <div class="fg">
              <div class="itr-label">Contact <span class="itr-req">*</span></div>
              <input class="itr-input{{ $errors->has('req_contact') ? ' is-error' : '' }}" type="text" name="req_contact" value="{{ old('req_contact') }}" required/>
              @error('req_contact')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
            </div>
            <div class="fg">
              <div class="itr-label">Company <span class="itr-req">*</span></div>
              <select class="itr-input{{ $errors->has('req_company') ? ' is-error' : '' }}" name="req_company" required>
                <option value="">&lt; Select Company &gt;</option>
                <option {{ old('req_company') === 'FGV Bulkers Sdn Bhd' ? 'selected' : '' }}>FGV Bulkers Sdn Bhd</option>
                <option {{ old('req_company') === 'FGV Johor Bulkers' ? 'selected' : '' }}>FGV Johor Bulkers</option>
                <option {{ old('req_company') === 'Langsat Bulkers Sdn Bhd' ? 'selected' : '' }}>Langsat Bulkers Sdn Bhd</option>
                <option {{ old('req_company') === 'FGV Grains Terminal Sdn Bhd' ? 'selected' : '' }}>FGV Grains Terminal Sdn Bhd</option>
              </select>
              @error('req_company')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
            </div>
          </div>
        </div>
      </div>

      <div class="itr-section">
        <div class="itr-section-head"><div class="itr-section-num">5</div><div class="itr-section-title">Approver Details</div></div>
        <div class="itr-section-body">
          <div class="fg">
            <div class="itr-label">Name <span class="itr-req">*</span></div>
            <input class="itr-input{{ $errors->has('approver_name') ? ' is-error' : '' }}" type="text" name="approver_name" value="{{ old('approver_name') }}" placeholder="Enter a name or email address…" required/>
            @error('approver_name')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
          </div>
          <div class="g4">
            <div class="fg"><div class="itr-label">Department <span class="itr-req">*</span></div><input class="itr-input{{ $errors->has('approver_department') ? ' is-error' : '' }}" type="text" name="approver_department" value="{{ old('approver_department') }}" required/>@error('approver_department')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror</div>
            <div class="fg"><div class="itr-label">Designation <span class="itr-req">*</span></div><input class="itr-input{{ $errors->has('approver_designation') ? ' is-error' : '' }}" type="text" name="approver_designation" value="{{ old('approver_designation') }}" required/>@error('approver_designation')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror</div>
            <div class="fg"><div class="itr-label">Contact <span class="itr-req">*</span></div><input class="itr-input{{ $errors->has('approver_contact') ? ' is-error' : '' }}" type="text" name="approver_contact" value="{{ old('approver_contact') }}" required/>@error('approver_contact')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror</div>
            <div class="fg">
              <div class="itr-label">Company <span class="itr-req">*</span></div>
              <select class="itr-input{{ $errors->has('approver_company') ? ' is-error' : '' }}" name="approver_company" required>
                <option value="">&lt; Select Company &gt;</option>
                <option {{ old('approver_company') === 'FGV Bulkers Sdn Bhd' ? 'selected' : '' }}>FGV Bulkers Sdn Bhd</option>
                <option {{ old('approver_company') === 'FGV Johor Bulkers' ? 'selected' : '' }}>FGV Johor Bulkers</option>
                <option {{ old('approver_company') === 'Langsat Bulkers Sdn Bhd' ? 'selected' : '' }}>Langsat Bulkers Sdn Bhd</option>
                <option {{ old('approver_company') === 'FGV Grains Terminal Sdn Bhd' ? 'selected' : '' }}>FGV Grains Terminal Sdn Bhd</option>
              </select>
              @error('approver_company')<div class="itr-field-error"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</div>@enderror
            </div>
          </div>
        </div>
      </div>
      <div class="itr-action-bar">
        <button type="submit" name="action" value="submit" class="itr-btn-submit"><i class="bi bi-send-fill"></i> Submit Request</button>
        <button type="submit" name="action" value="draft" class="itr-btn-draft" onclick="collectChips('hw-items-grid','hw_items');sessionStorage.removeItem('itr_form_state')"><i class="bi bi-floppy"></i> Save as Draft</button>
      </div>
    </form>
  </div><!-- /step2 -->
</div><!-- /itr-wrap -->

<script>
const typeConfig = {
  hardware: { label: 'Hardware',  icon: 'bi-laptop',      form: 'form-hardware' }
};
let activeType = null;

function selectType(type) {
  activeType = type;
  saveFormState();
  Object.keys(typeConfig).forEach(t => {
    const card = document.getElementById('card-' + t);
    card.classList.toggle('selected', t === type);
    card.classList.toggle('locked', t !== type);
  });
  document.getElementById('locked-note').textContent = 'Other request types are locked. Click "Change Type" to start over.';

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
  s1.style.opacity = '0';
  if (ms) ms.style.opacity = '0';
  setTimeout(() => {
    s1.style.display = 'none';
    if (ms) ms.style.display = 'none';
    const s2 = document.getElementById('step2');
    s2.style.display = 'block'; s2.style.opacity = '0';
    requestAnimationFrame(() => { s2.style.transition = 'opacity .25s'; s2.style.opacity = '1'; });
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }, 200);
}

function changeType() {
  sessionStorage.removeItem('itr_form_state');
  activeType = null;
  Object.keys(typeConfig).forEach(t => { document.getElementById('card-' + t).classList.remove('selected','locked'); });
  document.getElementById('locked-note').textContent = '';
  document.getElementById('prog-step1').className = 'itr-step active';
  document.getElementById('prog-step2').className = 'itr-step';
  document.getElementById('prog-line').className = 'itr-step-line';
  const s2 = document.getElementById('step2');
  s2.style.opacity = '0';
  setTimeout(() => {
    s2.style.display = 'none';
    const s1 = document.getElementById('step1');
    const ms = document.getElementById('my-submissions');
    s1.style.display = 'block'; s1.style.opacity = '0';
    if (ms) { ms.style.display = 'block'; ms.style.opacity = '0'; }
    requestAnimationFrame(() => {
      s1.style.transition = 'opacity .25s'; s1.style.opacity = '1';
      if (ms) { ms.style.transition = 'opacity .25s'; ms.style.opacity = '1'; }
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
  var state = { type: activeType, fields: {}, pills: {}, chips: {} };
  form.querySelectorAll('input:not([type=file]):not([type=hidden]), select, textarea').forEach(function(el) {
    if (el.name) state.fields[el.name] = el.value;
  });
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

  var chipMap = { hardware: ['hw-items-grid', 'hw_items'] };

  if (isDraft) {
    // Collect chips so partial selections are saved, but always allow submission
    if (chipMap[type]) collectChips(chipMap[type][0], chipMap[type][1]);
    sessionStorage.removeItem('itr_form_state');
    return true;
  }

  // Non-draft: collect chips then run full JS validation
  if (chipMap[type]) collectChips(chipMap[type][0], chipMap[type][1]);

  var form = document.getElementById('form-' + type);
  form.querySelectorAll('.itr-js-error').forEach(function(el) { el.style.display = 'none'; });

  var firstErr = null;
  function showErr(id) {
    var el = document.getElementById(id);
    if (el) { el.style.display = 'flex'; if (!firstErr) firstErr = el; }
  }

  if (type === 'hardware') {
    if (!document.getElementById('hw_request_type_val').value) showErr('err-hw-req-type');
    if (!document.querySelectorAll('#hw-items-grid .itr-check-chip.checked').length) showErr('err-hw-items');
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
    card.classList.toggle('selected', t === oldType);
    card.classList.toggle('locked', t !== oldType);
  });
  document.getElementById('locked-note').textContent = 'Other request types are locked. Click "Change Type" to start over.';
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
  var s2 = document.getElementById('step2');
  s2.style.display = 'block';
  s2.style.opacity = '1';

  restoreOldPill('hw-req-type',  'hw_request_type_val', @json(old('hw_request_type')));
  restoreOldChips('hw-items-grid', @json(old('hw_items', [])));
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
    document.getElementById('card-' + t).classList.toggle('selected', t === state.type);
    document.getElementById('card-' + t).classList.toggle('locked',   t !== state.type);
  });
  document.getElementById('locked-note').textContent = 'Other request types are locked. Click "Change Type" to start over.';
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
  var s2 = document.getElementById('step2');
  s2.style.display = 'block'; s2.style.opacity = '1';

  var form = document.getElementById(cfg.form);
  Object.keys(state.fields || {}).forEach(function(name) {
    form.querySelectorAll('[name="' + name + '"]').forEach(function(el) {
      if (el.type !== 'file') el.value = state.fields[name];
    });
  });

  restoreOldPill('hw-req-type', 'hw_request_type_val', (state.pills||{})['hw-req-type']);
  restoreOldChips('hw-items-grid', (state.chips||{})['hw-items-grid'] || []);
}

restoreFromSession();

['form-hardware'].forEach(function(id) {
  var f = document.getElementById(id);
  if (f) { f.addEventListener('input', saveFormState); f.addEventListener('change', saveFormState); }
});
</script>
@endif
@endsection

