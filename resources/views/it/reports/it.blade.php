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
.filter-bar{background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:16px 18px;margin-bottom:20px;display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap}
.filter-bar label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);display:block;margin-bottom:5px}
.filter-bar select,.filter-bar input{background:var(--bg);border:1px solid var(--border);border-radius:7px;color:var(--text);font-size:13px;padding:7px 10px;font-family:'DM Sans',sans-serif;outline:none;transition:border-color .15s}
.filter-bar select:focus,.filter-bar input:focus{border-color:var(--accent)}
.btn-export{display:inline-flex;align-items:center;gap:6px;padding:9px 16px;background:#16a34a;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;font-family:'DM Sans',sans-serif;text-decoration:none;transition:background .15s}
.btn-export:hover{background:#15803d;color:#fff}
.btn-filter{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:var(--accent);color:#fff;border:none;border-radius:7px;font-size:13px;font-weight:700;cursor:pointer;font-family:'DM Sans',sans-serif;transition:background .15s}
.btn-filter:hover{background:var(--accent-h)}
.btn-reset{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;background:var(--surface);color:var(--muted);border:1.5px solid var(--border);border-radius:7px;font-size:13px;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;text-decoration:none;transition:all .15s}
.btn-reset:hover{border-color:var(--accent);color:var(--accent)}
</style>

<!-- PAGE HEADER -->
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;gap:16px;flex-wrap:wrap">
  <div>
    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:5px">
      Admin &rsaquo; <span style="color:var(--accent)">Reports</span>
    </div>
    <h4 style="font-family:'DM Sans',sans-serif;font-weight:800;font-size:22px;color:var(--text);margin:0">IT Assets Report</h4>
    <p style="font-size:13px;color:var(--muted);margin:4px 0 0">System-wide inventory and disposal analytics</p>
  </div>
  <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
    <a href="{{ route('it.reports.it') }}" class="rpt-tab active">
      <i class="bi bi-box-seam-fill"></i> IT Assets
    </a>
    <a href="{{ route('it.reports.non-it') }}" class="rpt-tab">
      <i class="bi bi-archive-fill"></i> Non-IT Assets
    </a>
    <a href="{{ route('it.reports.it.export') }}?{{ http_build_query(request()->only(['status','class','date_from','date_to','search'])) }}"
       class="btn-export">
      <i class="bi bi-file-earmark-excel-fill"></i> Export Excel
    </a>
  </div>
</div>

<!-- STAT CARDS -->
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:24px">
  @foreach([
    ['Total Assets', 'bi-box-seam-fill',    '#2563eb','rgba(37,99,235,.12)',  $totalAssets],
    ['Active',       'bi-check-circle-fill', '#16a34a','rgba(22,163,74,.12)', $activeAssets],
    ['Collected',    'bi-bag-check-fill',    '#0d9488','rgba(13,148,136,.12)',$ewasteCollected],
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

<!-- FILTER BAR -->
<form method="GET" action="{{ route('it.reports.it') }}" class="filter-bar">
  <div>
    <label>Search</label>
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Asset no., description, serial…" style="width:220px">
  </div>
  <div>
    <label>Status</label>
    <select name="status" style="width:140px">
      <option value="">All Statuses</option>
      @foreach(['Active','Disposed','In Repair','Reserved'] as $s)
      <option value="{{ $s }}" @selected(request('status') === $s)>{{ $s }}</option>
      @endforeach
    </select>
  </div>
  <div>
    <label>Asset Class</label>
    <select name="class" style="width:160px">
      <option value="">All Classes</option>
      @foreach($allClasses as $cl)
      <option value="{{ $cl }}" @selected(request('class') === $cl)>{{ $cl }}</option>
      @endforeach
    </select>
  </div>
  <div>
    <label>Purchase Date From</label>
    <input type="date" name="date_from" value="{{ request('date_from') }}">
  </div>
  <div>
    <label>Purchase Date To</label>
    <input type="date" name="date_to" value="{{ request('date_to') }}">
  </div>
  <div style="display:flex;gap:8px;align-items:flex-end">
    <button type="submit" class="btn-filter"><i class="bi bi-funnel-fill"></i> Filter</button>
    @if(request()->hasAny(['search','status','class','date_from','date_to']))
    <a href="{{ route('it.reports.it') }}" class="btn-reset"><i class="bi bi-x-lg"></i> Reset</a>
    @endif
  </div>
</form>

<!-- FULL ASSET TABLE -->
<div class="table-card">
  <div class="table-card-header" style="justify-content:space-between">
    <div>
      <div class="table-card-title">IT Assets</div>
      <div style="font-size:12px;color:var(--muted);margin-top:2px">
        Showing {{ $items->firstItem() ?? 0 }}–{{ $items->lastItem() ?? 0 }} of {{ $items->total() }} records
      </div>
    </div>
    <div style="display:flex;align-items:center;gap:10px">
      @php
      $sc = ['Active'=>'#16a34a','Disposed'=>'#dc2626','In Repair'=>'#7c3aed','Reserved'=>'#2563eb'];
      @endphp
      @foreach($statusRows as $sr)
      <span style="display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:600;color:{{ $sc[$sr->item_status] ?? 'var(--muted)' }}">
        <span style="width:8px;height:8px;border-radius:50%;background:{{ $sc[$sr->item_status] ?? 'var(--muted)' }};display:inline-block"></span>
        {{ $sr->item_status }}: {{ $sr->c }}
      </span>
      @endforeach
    </div>
  </div>
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>#</th>
          <th>Asset No.</th>
          <th>Class</th>
          <th>Description</th>
          <th>Serial No.</th>
          <th>Brand / Model</th>
          <th>Location</th>
          <th>Status</th>
          <th>Condition</th>
          <th>Purchase Date</th>
          <th>Purchase Price</th>
        </tr>
      </thead>
      <tbody>
      @forelse($items as $i => $item)
      @php
      $colors = ['Active'=>['#16a34a','rgba(22,163,74,.1)'],'Disposed'=>['#dc2626','rgba(220,38,38,.1)'],'In Repair'=>['#7c3aed','rgba(124,58,237,.1)'],'Reserved'=>['#2563eb','rgba(37,99,235,.1)']];
      [$sc, $sbg] = $colors[$item->item_status] ?? ['#6b7280','rgba(107,114,128,.1)'];
      @endphp
      <tr>
        <td style="color:var(--muted);font-size:12px">{{ $items->firstItem() + $i }}</td>
        <td><code style="color:var(--accent);font-size:12px">{{ $item->asset_number ?: '—' }}</code></td>
        <td>
          <span style="background:rgba(2,132,199,.08);border:1px solid rgba(2,132,199,.2);border-radius:6px;padding:2px 8px;font-size:11px;font-weight:700;color:var(--accent-h)">
            {{ $item->asset_class }}
          </span>
        </td>
        <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:13px" title="{{ $item->description }}">{{ $item->description ?: '—' }}</td>
        <td style="font-size:12px;color:var(--muted)">{{ $item->serial_number ?: '—' }}</td>
        <td style="font-size:12px">{{ trim(($item->brand ?? '') . ' ' . ($item->model ?? '')) ?: '—' }}</td>
        <td style="font-size:12px;color:var(--muted)">{{ $item->location ?: '—' }}</td>
        <td>
          <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $sbg }};color:{{ $sc }}">
            <span style="width:5px;height:5px;border-radius:50%;background:{{ $sc }};display:inline-block"></span>
            {{ $item->item_status }}
          </span>
        </td>
        <td style="font-size:12px;color:var(--muted)">{{ $item->condition_status ?: '—' }}</td>
        <td style="font-size:12px;white-space:nowrap;color:var(--muted)">{{ $item->purchase_date?->format('d/m/Y') ?? '—' }}</td>
        <td style="font-size:12px;white-space:nowrap">{{ $item->purchase_price ? 'RM '.number_format($item->purchase_price, 2) : '—' }}</td>
      </tr>
      @empty
      <tr><td colspan="11" style="text-align:center;color:var(--muted);padding:40px">No assets match the selected filters.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
  @if($items->hasPages())
  <div style="padding:14px 20px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px">
    <div style="font-size:12px;color:var(--muted)">
      Page {{ $items->currentPage() }} of {{ $items->lastPage() }}
    </div>
    {{ $items->links() }}
  </div>
  @endif
</div>
@endsection
