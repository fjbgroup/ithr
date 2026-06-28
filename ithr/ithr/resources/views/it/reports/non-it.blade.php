@extends('it.layouts.app')

@section('title', 'Non-IT Assets Report')
@section('page_title', 'Non-IT Assets Report')

@section('content')
<style>
.rpt-stat{background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:18px 20px;display:flex;align-items:center;gap:14px;border-left:4px solid transparent}
.rpt-stat-icon{width:42px;height:42px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
.rpt-stat-val{font-size:26px;font-weight:800;color:var(--text);line-height:1}
.rpt-stat-lbl{font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-top:3px}
.rpt-tab{display:inline-flex;align-items:center;gap:7px;padding:9px 18px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;transition:all .15s;border:1.5px solid var(--border);color:var(--muted)}
.rpt-tab:hover{border-color:var(--accent);color:var(--accent)}
.rpt-tab.active{background:var(--accent);color:#fff;border-color:var(--accent)}
.sb-row{display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid var(--border)}
.sb-row:last-child{border-bottom:none}
</style>

<!-- PAGE HEADER -->
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;gap:16px;flex-wrap:wrap">
  <div>
    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:5px">
      Admin &rsaquo; <span style="color:var(--accent)">Reports</span>
    </div>
    <h4 style="font-family:'Inter',sans-serif;font-weight:800;font-size:22px;color:var(--text);margin:0">Non-IT Assets Report</h4>
    <p style="font-size:13px;color:var(--muted);margin:4px 0 0">System-wide inventory and disposal analytics</p>
  </div>
  <!-- Tab switcher -->
  <div style="display:flex;align-items:center;gap:8px">
    <a href="{{ route('it.reports.it') }}" class="rpt-tab">
      <i class="bi bi-box-seam-fill"></i> IT Assets
    </a>
    <a href="{{ route('it.reports.non-it') }}" class="rpt-tab active">
      <i class="bi bi-archive-fill"></i> Non-IT Assets
    </a>
    <button onclick="window.print()"
      style="display:inline-flex;align-items:center;gap:6px;padding:9px 16px;background:var(--surface);color:var(--muted);border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif;transition:all .15s"
      onmouseover="this.style.borderColor='var(--accent)';this.style.color='var(--accent)'"
      onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--muted)'">
      <i class="bi bi-printer"></i> Print
    </button>
  </div>
</div>

<!-- STAT CARDS -->
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px">
  @foreach([
    ['Total Assets', 'bi-archive-fill',        '#2563eb','rgba(37,99,235,.12)',   $nitTotal],
    ['Active',       'bi-check-circle-fill',    '#16a34a','rgba(22,163,74,.12)',  $nitActive],
    ['In Repair',    'bi-tools',                '#7c3aed','rgba(124,58,237,.12)', $nitRepair],
    ['Disposed',     'bi-trash3-fill',          '#dc2626','rgba(220,38,38,.12)',  $nitDisposed],
  ] as [$lbl, $icon, $color, $bg, $val])
  <div class="rpt-stat" style="border-left-color:{{ $color }}">
    <div class="rpt-stat-icon" style="background:{{ $bg }};color:{{ $color }}">
      <i class="bi {{ $icon }}"></i>
    </div>
    <div>
      <div class="rpt-stat-val">{{ $val }}</div>
      <div class="rpt-stat-lbl">{{ $lbl }}</div>
    </div>
  </div>
  @endforeach
</div>

<div class="row g-3">
  <!-- Non-IT Assets by Class -->
  <div class="col-md-5">
    <div class="table-card h-100">
      <div class="table-card-header">
        <div>
          <div class="table-card-title">Non-IT Assets by Class</div>
          <div style="font-size:12px;color:var(--muted);margin-top:2px">Breakdown by asset category</div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table">
          <thead><tr><th>Asset Class</th><th>Count</th><th>% of Total</th></tr></thead>
          <tbody>
          @foreach($nitClasses as $r)
          <tr>
            <td>
              <span style="display:inline-flex;align-items:center;gap:6px;background:rgba(2,132,199,.08);border:1px solid rgba(2,132,199,.2);border-radius:6px;padding:3px 10px;font-size:12px;font-weight:700;color:var(--accent-h)">
                {{ $r->asset_class }}
              </span>
            </td>
            <td><strong style="color:var(--accent)">{{ $r->c }}</strong></td>
            <td>
              <div style="display:flex;align-items:center;gap:8px">
                <div style="flex:1;background:var(--border);border-radius:4px;height:6px;max-width:100px">
                  <div style="height:6px;border-radius:4px;background:var(--accent);width:{{ $nitTotal > 0 ? round($r->c / $nitTotal * 100) : 0 }}%"></div>
                </div>
                <span style="font-size:12px;color:var(--muted);min-width:36px">{{ $nitTotal > 0 ? round($r->c / $nitTotal * 100, 1) : 0 }}%</span>
              </div>
            </td>
          </tr>
          @endforeach
          @if($nitClasses->isEmpty())
          <tr><td colspan="3" style="text-align:center;color:var(--muted);padding:28px">No non-IT asset records yet</td></tr>
          @endif
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Recent Non-IT Assets -->
  <div class="col-md-7">
    <div class="table-card h-100">
      <div class="table-card-header">
        <div>
          <div class="table-card-title">Recent Non-IT Assets</div>
          <div style="font-size:12px;color:var(--muted);margin-top:2px">Latest 20 entries</div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table">
          <thead><tr><th>Asset No.</th><th>Class</th><th>Description</th><th>Status</th><th>Date Registered</th></tr></thead>
          <tbody>
          @php
          $sc = ['Active'=>'bs-active','In Repair'=>'bs-repair','Disposed'=>'bs-disposed','Reserved'=>'bs-pending'];
          @endphp
          @forelse($nitRecent as $item)
          <tr>
            <td><code style="color:var(--accent)">{{ $item->asset_number ?: '—' }}</code></td>
            <td style="font-size:12px">{{ $item->asset_class }}</td>
            <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $item->description }}</td>
            <td><span class="badge-status {{ $sc[$item->item_status] ?? '' }}">{{ $item->item_status }}</span></td>
            <td style="font-size:12px;white-space:nowrap">{{ $item->date_registered ? \Carbon\Carbon::parse($item->date_registered)->format('d/m/Y') : '—' }}</td>
          </tr>
          @empty
          <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:28px">No records</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

