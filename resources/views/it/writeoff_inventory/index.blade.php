@extends('it.layouts.app')

@section('title', 'Write Off Inventory')
@section('page_title', 'Write Off Inventory')

@section('content')

<style>
.ds-card{display:flex;align-items:center;gap:18px;text-decoration:none;background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:22px 24px;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 14px rgba(0,0,0,.07);transition:box-shadow .2s,transform .2s;height:100%}
.ds-card:hover{box-shadow:0 6px 24px rgba(0,0,0,.15);transform:translateY(-2px)}
.ds-icon{width:46px;height:46px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0}
.ds-num{font-size:30px;font-weight:800;color:var(--text);line-height:1;font-family:'Inter',sans-serif}
.ds-lbl{font-size:12px;color:var(--muted);margin-top:5px;font-weight:500}
</style>

@php
  $batchPendingCount = $pendingGroups->filter(fn($g) => $g->count() > 1)->count();
  $soloPendingCount  = $pendingGroups->filter(fn($g) => $g->count() === 1)->count();
  $processedTotal    = $processedGroups->sum(fn($g) => $g->count());
@endphp

{{-- ══ PAGE HEADER ══ --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px">
  <div>
    <h4 style="font-family:'Inter',sans-serif;font-weight:800;font-size:22px;color:var(--text);margin:0;letter-spacing:-.3px">
      Write Off Inventory
    </h4>
    <p style="font-size:13px;color:var(--muted);margin:4px 0 0">
      CEO-approved write-offs awaiting Finance routing to E-Waste or Disposal
    </p>
  </div>
  @if($pendingCount > 0)
  <span style="background:rgba(217,119,6,.12);color:#d97706;border-radius:20px;padding:6px 18px;font-size:13px;font-weight:700;align-self:center;letter-spacing:.02em">
    {{ $pendingCount }} awaiting decision
  </span>
  @endif
</div>

{{-- ══ STAT CARDS ══ --}}
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="ds-card" style="border-top:3px solid #d97706">
      <div class="ds-icon" style="background:#fef9c3;color:#ca8a04"><i class="bi bi-clock-history"></i></div>
      <div>
        <div class="ds-num">{{ $pendingGroups->count() }}</div>
        <div class="ds-lbl">Pending Decisions</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="ds-card" style="border-top:3px solid #2563eb">
      <div class="ds-icon" style="background:#dbeafe;color:#2563eb"><i class="bi bi-box-seam"></i></div>
      <div>
        <div class="ds-num">{{ $pendingCount }}</div>
        <div class="ds-lbl">Items to Route</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="ds-card" style="border-top:3px solid #7c3aed">
      <div class="ds-icon" style="background:#ede9fe;color:#7c3aed"><i class="bi bi-layers"></i></div>
      <div>
        <div class="ds-num">{{ $batchPendingCount }}</div>
        <div class="ds-lbl">Batched Submissions</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="ds-card" style="border-top:3px solid #16a34a">
      <div class="ds-icon" style="background:#dcfce7;color:#16a34a"><i class="bi bi-check2-all"></i></div>
      <div>
        <div class="ds-num">{{ $processedTotal }}</div>
        <div class="ds-lbl">Already Routed</div>
      </div>
    </div>
  </div>
</div>

{{-- ══ PENDING FINANCE DECISION ══ --}}
<div style="margin-bottom:32px">
  {{-- Section header --}}
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
    <div style="display:flex;align-items:center;gap:10px">
      <div style="width:4px;height:22px;background:#d97706;border-radius:2px"></div>
      <span style="font-family:'Inter',sans-serif;font-weight:800;font-size:15px;color:var(--text);text-transform:uppercase;letter-spacing:.04em">
        Pending Finance Decision
      </span>
      @if($pendingCount > 0)
      <span style="background:rgba(245,158,11,.15);color:#d97706;border-radius:20px;padding:2px 12px;font-size:11px;font-weight:700">
        {{ $pendingCount }} item{{ $pendingCount > 1 ? 's' : '' }}
      </span>
      @endif
    </div>
  </div>

  @if($pendingCount === 0)
  <div class="table-card" style="padding:52px 20px;text-align:center">
    <i class="bi bi-check-circle" style="font-size:36px;display:block;margin-bottom:12px;color:#16a34a"></i>
    <div style="font-size:15px;font-weight:700;color:var(--text)">All clear</div>
    <div style="font-size:13px;color:var(--muted);margin-top:4px">No write-offs awaiting Finance routing</div>
  </div>
  @else

  @foreach($pendingGroups as $groupKey => $items)
    @php $first = $items->first(); $isBatch = $items->count() > 1; @endphp

    <div class="table-card" style="margin-bottom:10px;border-left:4px solid #d97706;border-radius:10px;overflow:visible">

      {{-- Card header --}}
      <div style="padding:16px 20px;display:flex;align-items:flex-start;justify-content:space-between;gap:20px{{ $isBatch ? ';cursor:pointer' : '' }}"
           @if($isBatch) onclick="toggleBatch('{{ $groupKey }}')" @endif>

        {{-- Left: identity + approval chain --}}
        <div style="flex:1;min-width:0">
          @if($isBatch)
          {{-- Batch label --}}
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">
            <i class="bi bi-layers" style="color:#d97706;font-size:14px"></i>
            <span style="font-weight:800;font-size:13px;color:var(--text);text-transform:uppercase;letter-spacing:.05em">Batch Submission</span>
            <span style="background:#d97706;color:#fff;border-radius:20px;padding:2px 10px;font-size:10px;font-weight:700;letter-spacing:.04em">
              {{ $items->count() }} ITEMS
            </span>
            <i class="bi bi-chevron-down" id="icon-{{ $groupKey }}" style="font-size:11px;color:var(--muted);margin-left:2px;transition:.15s"></i>
          </div>
          @else
          {{-- Solo item identity --}}
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px">
            <i class="bi bi-tag" style="color:var(--accent);font-size:13px"></i>
            <span style="font-weight:800;font-size:13px;color:var(--accent);font-family:monospace;letter-spacing:.03em">
              {{ $first->asset_number ?: '—' }}
            </span>
            @if($first->asset_class)
            <span style="background:rgba(59,130,246,.1);color:#2563eb;border-radius:5px;padding:2px 9px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.04em">
              {{ $first->asset_class }}
            </span>
            @endif
          </div>
          <div style="font-size:13px;font-weight:600;color:var(--text);margin-bottom:6px">
            {{ $first->description }}
          </div>
          @if($first->serial_number)
          <div style="font-size:11px;color:var(--muted);font-family:monospace">SN: {{ $first->serial_number }}</div>
          @endif
          @endif

          {{-- Approval chain chips (shared for both) --}}
          <div style="display:flex;flex-wrap:wrap;align-items:center;gap:6px;{{ $isBatch ? '' : 'margin-top:8px' }}">
            @if($first->writeoff_name)
            <span style="display:inline-flex;align-items:center;gap:4px;background:rgba(100,116,139,.1);color:var(--muted);border-radius:6px;padding:3px 9px;font-size:11px;font-weight:600">
              <i class="bi bi-person" style="font-size:10px"></i> {{ $first->writeoff_name }}
              @if($first->writeoff_designation)
              <span style="opacity:.7">· {{ $first->writeoff_designation }}</span>
              @endif
            </span>
            @endif
            @if($first->hou_signed_name && $first->hou_status === 'Checked')
            <span style="display:inline-flex;align-items:center;gap:4px;background:rgba(124,58,237,.08);color:#7c3aed;border-radius:6px;padding:3px 9px;font-size:11px;font-weight:700">
              <i class="bi bi-check-circle-fill" style="font-size:10px"></i> HOU · {{ $first->hou_signed_name }}
            </span>
            @endif
            @if($first->gm_signed_name && $first->gm_status === 'Checked')
            <span style="display:inline-flex;align-items:center;gap:4px;background:rgba(13,148,136,.08);color:#0d9488;border-radius:6px;padding:3px 9px;font-size:11px;font-weight:700">
              <i class="bi bi-check-circle-fill" style="font-size:10px"></i> GM · {{ $first->gm_signed_name }}
            </span>
            @endif
            @if($first->ceo_signed_name)
            <span style="display:inline-flex;align-items:center;gap:4px;background:rgba(22,163,74,.08);color:#16a34a;border-radius:6px;padding:3px 9px;font-size:11px;font-weight:700">
              <i class="bi bi-shield-check" style="font-size:10px"></i> CEO · {{ $first->ceo_signed_name }}
            </span>
            @endif
            @if($first->ceo_signed_at)
            <span style="font-size:11px;color:var(--muted)">{{ $first->ceo_signed_at->format('d M Y') }}</span>
            @endif
          </div>
        </div>

        {{-- Right: action buttons --}}
        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px;flex-shrink:0"
             @if($isBatch) onclick="event.stopPropagation()" @endif>
          <button onclick="openFormModal('{{ route('it.writeoff.report', $first->id) }}')"
             style="display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:700;color:#2563eb;background:rgba(59,130,246,.08);border:1.5px solid rgba(59,130,246,.2);border-radius:7px;padding:6px 14px;white-space:nowrap;transition:.15s;cursor:pointer"
             onmouseover="this.style.background='rgba(59,130,246,.14)'" onmouseout="this.style.background='rgba(59,130,246,.08)'">
            <i class="bi bi-file-earmark-text"></i> View Form
          </button>
          <div style="display:flex;gap:6px">
            <a href="{{ $isBatch ? route('it.writeoff-inventory.batch-route-ewaste', $first->batch_id) : route('it.writeoff-inventory.route-ewaste', $first->id) }}"
               onclick="return confirm('{{ $isBatch ? 'Route all '.$items->count().' items in this batch to E-Waste?' : 'Route &quot;'.addslashes($first->description).'&quot; to E-Waste?' }}')"
               style="display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:700;color:#fff;background:#0d9488;border-radius:7px;padding:7px 14px;text-decoration:none;white-space:nowrap;transition:.15s"
               onmouseover="this.style.background='#0f766e'" onmouseout="this.style.background='#0d9488'">
              <i class="bi bi-recycle"></i> E-Waste
            </a>
            <a href="{{ $isBatch ? route('it.writeoff-inventory.batch-route-disposal', $first->batch_id) : route('it.writeoff-inventory.route-disposal', $first->id) }}"
               onclick="return confirm('{{ $isBatch ? 'Route all '.$items->count().' items in this batch to Disposal?' : 'Route &quot;'.addslashes($first->description).'&quot; to Disposal?' }}')"
               style="display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:700;color:#fff;background:#dc2626;border-radius:7px;padding:7px 14px;text-decoration:none;white-space:nowrap;transition:.15s"
               onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">
              <i class="bi bi-trash3-fill"></i> Disposal
            </a>
          </div>
        </div>

      </div>{{-- end card header --}}

      {{-- Expandable items list (batch only) --}}
      @if($isBatch)
      <div id="batch-{{ $groupKey }}" style="display:none;border-top:1px solid var(--border);background:rgba(245,158,11,.02)">
        <table style="width:100%;font-size:12px;border-collapse:collapse">
          <thead>
            <tr style="border-bottom:1px solid var(--border)">
              <th style="padding:8px 20px;text-align:left;color:var(--muted);font-weight:700;font-size:10px;text-transform:uppercase;letter-spacing:.08em;width:160px">Asset No.</th>
              <th style="padding:8px 12px;text-align:left;color:var(--muted);font-weight:700;font-size:10px;text-transform:uppercase;letter-spacing:.08em;width:120px">Class</th>
              <th style="padding:8px 12px;text-align:left;color:var(--muted);font-weight:700;font-size:10px;text-transform:uppercase;letter-spacing:.08em">Description</th>
              <th style="padding:8px 20px;text-align:left;color:var(--muted);font-weight:700;font-size:10px;text-transform:uppercase;letter-spacing:.08em;width:180px">Serial No.</th>
            </tr>
          </thead>
          <tbody>
            @foreach($items as $item)
            <tr style="border-bottom:1px solid var(--border)">
              <td style="padding:10px 20px;font-weight:700;color:var(--accent);font-family:monospace;font-size:12px">{{ $item->asset_number ?: '—' }}</td>
              <td style="padding:10px 12px">
                <span style="background:rgba(59,130,246,.1);color:#2563eb;border-radius:5px;padding:2px 8px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.04em">{{ $item->asset_class }}</span>
              </td>
              <td style="padding:10px 12px;font-weight:500;color:var(--text)">{{ $item->description }}</td>
              <td style="padding:10px 20px;color:var(--muted);font-family:monospace;font-size:11px">{{ $item->serial_number ?: '—' }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @endif

    </div>{{-- end table-card --}}
  @endforeach

  @endif
</div>

{{-- ══ PROCESSED HISTORY ══ --}}
@if($processedGroups->isNotEmpty())
<div>
  {{-- Section header --}}
  <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
    <div style="width:4px;height:22px;background:#16a34a;border-radius:2px"></div>
    <span style="font-family:'Inter',sans-serif;font-weight:800;font-size:15px;color:var(--text);text-transform:uppercase;letter-spacing:.04em">
      Processed History
    </span>
    <span style="background:rgba(22,163,74,.1);color:#16a34a;border-radius:20px;padding:2px 12px;font-size:11px;font-weight:700">
      {{ $processedTotal }} routed
    </span>
  </div>

  @foreach($processedGroups as $groupKey => $items)
    @php $first = $items->first(); $isBatch = $items->count() > 1; @endphp

    <div class="table-card" style="margin-bottom:8px;border-left:4px solid {{ $first->finance_status === 'EWaste' ? '#0d9488' : '#dc2626' }};border-radius:10px;overflow:visible">

      <div style="padding:13px 20px;display:flex;align-items:center;gap:16px{{ $isBatch ? ';cursor:pointer' : '' }}"
           @if($isBatch) onclick="toggleBatch('proc-{{ $groupKey }}')" @endif>

        {{-- Left: identity --}}
        <div style="flex:1;display:flex;align-items:center;gap:12px;min-width:0;flex-wrap:wrap">
          @if($isBatch)
          <div style="display:flex;align-items:center;gap:6px">
            <i class="bi bi-layers" style="font-size:12px;color:var(--muted)"></i>
            <span style="font-weight:700;font-size:12px;color:var(--text);text-transform:uppercase;letter-spacing:.04em">Batch</span>
            <span style="background:rgba(99,102,241,.1);color:#4f46e5;border-radius:20px;padding:1px 8px;font-size:10px;font-weight:700">{{ $items->count() }} items</span>
            <i class="bi bi-chevron-down" id="icon-proc-{{ $groupKey }}" style="font-size:10px;color:var(--muted)"></i>
          </div>
          @else
          <span style="font-weight:700;font-size:12px;color:var(--accent);font-family:monospace">{{ $first->asset_number ?: '—' }}</span>
          <span style="font-size:12px;font-weight:500;color:var(--text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:300px">{{ $first->description }}</span>
          @endif

          {{-- Status pill --}}
          @if($first->finance_status === 'EWaste')
          <span style="display:inline-flex;align-items:center;gap:4px;background:rgba(13,148,136,.1);color:#0d9488;border-radius:20px;padding:3px 10px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.04em">
            <i class="bi bi-recycle" style="font-size:10px"></i> E-Waste
          </span>
          @elseif($first->finance_status === 'Disposal')
          <span style="display:inline-flex;align-items:center;gap:4px;background:rgba(239,68,68,.1);color:#dc2626;border-radius:20px;padding:3px 10px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.04em">
            <i class="bi bi-trash3-fill" style="font-size:10px"></i> Disposal
          </span>
          @endif

          {{-- CEO & date --}}
          @if($first->ceo_signed_name)
          <span style="display:inline-flex;align-items:center;gap:4px;font-size:11px;color:var(--muted)">
            <i class="bi bi-shield-check" style="font-size:10px;color:#16a34a"></i> {{ $first->ceo_signed_name }}
          </span>
          @endif
          @if($first->updated_at)
          <span style="font-size:11px;color:var(--muted)">{{ $first->updated_at->format('d M Y') }}</span>
          @endif
        </div>

        {{-- Right: view form + expand stop --}}
        <div @if($isBatch) onclick="event.stopPropagation()" @endif>
          <button onclick="openFormModal('{{ route('it.writeoff.report', $first->id) }}')"
             style="display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;color:#2563eb;background:rgba(59,130,246,.08);border:1.5px solid rgba(59,130,246,.2);border-radius:6px;padding:5px 12px;white-space:nowrap;cursor:pointer">
            <i class="bi bi-file-earmark-text"></i> View Form
          </button>
        </div>

      </div>

      {{-- Expandable items --}}
      @if($isBatch)
      <div id="batch-proc-{{ $groupKey }}" style="display:none;border-top:1px solid var(--border);background:rgba(0,0,0,.015)">
        <table style="width:100%;font-size:12px;border-collapse:collapse">
          <thead>
            <tr style="border-bottom:1px solid var(--border)">
              <th style="padding:7px 20px;text-align:left;color:var(--muted);font-weight:700;font-size:10px;text-transform:uppercase;letter-spacing:.08em;width:160px">Asset No.</th>
              <th style="padding:7px 12px;text-align:left;color:var(--muted);font-weight:700;font-size:10px;text-transform:uppercase;letter-spacing:.08em;width:120px">Class</th>
              <th style="padding:7px 12px;text-align:left;color:var(--muted);font-weight:700;font-size:10px;text-transform:uppercase;letter-spacing:.08em">Description</th>
              <th style="padding:7px 20px;text-align:left;color:var(--muted);font-weight:700;font-size:10px;text-transform:uppercase;letter-spacing:.08em">Serial No.</th>
            </tr>
          </thead>
          <tbody>
            @foreach($items as $item)
            <tr style="border-bottom:1px solid var(--border)">
              <td style="padding:8px 20px;font-weight:700;color:var(--accent);font-family:monospace;font-size:12px">{{ $item->asset_number ?: '—' }}</td>
              <td style="padding:8px 12px">
                <span style="background:rgba(59,130,246,.1);color:#2563eb;border-radius:5px;padding:2px 8px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.04em">{{ $item->asset_class }}</span>
              </td>
              <td style="padding:8px 12px;font-weight:500;color:var(--text)">{{ $item->description }}</td>
              <td style="padding:8px 20px;color:var(--muted);font-family:monospace;font-size:11px">{{ $item->serial_number ?: '—' }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @endif

    </div>
  @endforeach
</div>
@endif

{{-- ══ WRITE-OFF FORM OVERLAY ══ --}}
<div id="formOverlay"
     style="display:none;position:fixed;inset:0;z-index:1055;background:rgba(0,0,0,.55);align-items:center;justify-content:center"
     onclick="if(event.target===this)closeFormModal()">
  <div style="position:relative;width:96%;max-width:960px;max-height:92vh;display:flex;flex-direction:column;border-radius:12px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.3)">

    {{-- Header --}}
    <div style="background:#142b47;color:#fff;padding:14px 20px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0">
      <div style="display:flex;align-items:center;gap:10px">
        <i class="bi bi-file-earmark-text" style="font-size:18px"></i>
        <span style="font-weight:700;font-size:15px;font-family:'Inter',sans-serif">Write-Off Report</span>
      </div>
      <div style="display:flex;align-items:center;gap:8px">
        <button onclick="printReport()"
                style="display:inline-flex;align-items:center;gap:6px;background:#0284c7;color:#fff;border:none;border-radius:7px;padding:7px 16px;font-size:12px;font-weight:700;cursor:pointer"
                onmouseover="this.style.background='#0369a1'" onmouseout="this.style.background='#0284c7'">
          <i class="bi bi-printer"></i> Print / Download PDF
        </button>
        <button onclick="closeFormModal()"
                style="background:rgba(255,255,255,.15);color:#fff;border:none;border-radius:6px;width:32px;height:32px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:20px;line-height:1">
          &times;
        </button>
      </div>
    </div>

    {{-- Iframe --}}
    <iframe id="reportIframe" src=""
            style="flex:1;border:none;min-height:78vh;background:#f1f5f9"></iframe>
  </div>
</div>

<script>
window.openFormModal = function (url) {
  document.getElementById('reportIframe').src = url + '?embed=1';
  var overlay = document.getElementById('formOverlay');
  overlay.style.display = 'flex';
  document.body.style.overflow = 'hidden';
};

window.closeFormModal = function () {
  document.getElementById('formOverlay').style.display = 'none';
  document.getElementById('reportIframe').src = '';
  document.body.style.overflow = '';
};

window.printReport = function () {
  var iframe = document.getElementById('reportIframe');
  iframe.contentWindow.focus();
  iframe.contentWindow.print();
};

function toggleBatch(key) {
  var row  = document.getElementById('batch-' + key);
  var icon = document.getElementById('icon-' + key);
  if (!row) return;
  var opening = row.style.display === 'none';
  row.style.display = opening ? '' : 'none';
  if (icon) {
    icon.classList.toggle('bi-chevron-down', !opening);
    icon.classList.toggle('bi-chevron-up',    opening);
  }
}
</script>

@endsection

