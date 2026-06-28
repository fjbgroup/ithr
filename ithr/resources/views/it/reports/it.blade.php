@extends('it.layouts.app')

@section('title', 'IT Assets Report')
@section('page_title', 'IT Assets Report')

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
    <h4 style="font-family:'Inter',sans-serif;font-weight:800;font-size:22px;color:var(--text);margin:0">IT Assets Report</h4>
    <p style="font-size:13px;color:var(--muted);margin:4px 0 0">System-wide inventory and disposal analytics</p>
  </div>
  <!-- Tab switcher -->
  <div style="display:flex;align-items:center;gap:8px">
    <a href="{{ route('it.reports.it') }}" class="rpt-tab active">
      <i class="bi bi-box-seam-fill"></i> IT Assets
    </a>
    <a href="{{ route('it.reports.non-it') }}" class="rpt-tab">
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
    ['Total Assets',    'bi-box-seam-fill',    '#2563eb','rgba(37,99,235,.12)',  $totalAssets],
    ['Active',          'bi-check-circle-fill', '#16a34a','rgba(22,163,74,.12)', $activeAssets],
    ['E-Waste Pending', 'bi-hourglass-split',   '#d97706','rgba(245,158,11,.12)',$ewastePending],
    ['Collected',       'bi-bag-check-fill',    '#0d9488','rgba(13,148,136,.12)',$ewasteCollected],
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
  <!-- Assets by Class -->
  <div class="col-md-5">
    <div class="table-card h-100">
      <div class="table-card-header">
        <div>
          <div class="table-card-title">Assets by Class</div>
          <div style="font-size:12px;color:var(--muted);margin-top:2px">Inventory distribution</div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table">
          <thead><tr><th>Asset Class</th><th>Count</th><th>% of Total</th></tr></thead>
          <tbody>
          @foreach($classes as $c)
          <tr>
            <td>
              <span style="display:inline-flex;align-items:center;gap:6px;background:rgba(2,132,199,.08);border:1px solid rgba(2,132,199,.2);border-radius:6px;padding:3px 10px;font-size:12px;font-weight:700;color:var(--accent-h)">
                {{ $c->asset_class }}
              </span>
            </td>
            <td><strong style="color:var(--accent)">{{ $c->c }}</strong></td>
            <td>
              <div style="display:flex;align-items:center;gap:8px">
                <div style="flex:1;background:var(--border);border-radius:4px;height:6px;max-width:100px">
                  <div style="height:6px;border-radius:4px;background:var(--accent);width:{{ $totalAssets > 0 ? round($c->c / $totalAssets * 100) : 0 }}%"></div>
                </div>
                <span style="font-size:12px;color:var(--muted);min-width:36px">{{ $totalAssets > 0 ? round($c->c / $totalAssets * 100, 1) : 0 }}%</span>
              </div>
            </td>
          </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Status Breakdown -->
  <div class="col-md-7">
    <div class="table-card h-100">
      <div class="table-card-header">
        <div>
          <div class="table-card-title">Status Breakdown</div>
          <div style="font-size:12px;color:var(--muted);margin-top:2px">Asset distribution by current status</div>
        </div>
      </div>
      <div style="padding:20px">
        @php
        $statusColors = [
          'Active'    => ['#16a34a','rgba(22,163,74,.1)',   'bi-check-circle-fill'],
          'Disposed'  => ['#dc2626','rgba(220,38,38,.1)',   'bi-trash3-fill'],
          'In Repair' => ['#7c3aed','rgba(124,58,237,.1)',  'bi-tools'],
          'Reserved'  => ['#2563eb','rgba(37,99,235,.1)',   'bi-bookmark-fill'],
        ];
        @endphp
        @foreach($statusRows as $sr)
        @php
          [$sc, $bg, $ico] = $statusColors[$sr->item_status] ?? ['#6b7280','rgba(107,114,128,.1)','bi-circle'];
          $pct = $totalAssets > 0 ? round($sr->c / $totalAssets * 100, 1) : 0;
        @endphp
        <div class="sb-row">
          <div style="width:36px;height:36px;border-radius:9px;background:{{ $bg }};color:{{ $sc }};display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0">
            <i class="bi {{ $ico }}"></i>
          </div>
          <div style="flex:1;min-width:0">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:5px">
              <span style="font-size:13px;font-weight:700;color:var(--text)">{{ $sr->item_status }}</span>
              <span style="font-size:13px;font-weight:800;color:{{ $sc }}">{{ $sr->c }} <span style="font-size:11px;font-weight:600;color:var(--muted)">({{ $pct }}%)</span></span>
            </div>
            <div style="background:var(--border);border-radius:4px;height:7px">
              <div style="height:7px;border-radius:4px;background:{{ $sc }};width:{{ $pct }}%;transition:width .4s ease"></div>
            </div>
          </div>
        </div>
        @endforeach
        @if($statusRows->isEmpty())
        <div style="text-align:center;color:var(--muted);padding:28px">No assets recorded yet</div>
        @endif
        <div style="margin-top:16px;padding:12px 16px;background:rgba(37,99,235,.06);border:1px solid rgba(37,99,235,.2);border-radius:8px;font-size:12px;color:#1d4ed8;display:flex;align-items:center;gap:8px">
          <i class="bi bi-info-circle-fill"></i>
          Total of <strong>{{ $totalAssets }}</strong> IT assets across <strong>{{ $statusRows->count() }}</strong> status {{ $statusRows->count() === 1 ? 'category' : 'categories' }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

