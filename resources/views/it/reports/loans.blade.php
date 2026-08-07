@extends('it.layouts.app')

@section('title', 'IT Peripheral Loans Report')
@section('page_title', 'IT Peripheral Loans Report')

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
.filter-bar select,.filter-bar input{background:var(--bg);border:1px solid var(--border);border-radius:7px;color:var(--text);font-size:13px;padding:7px 10px;font-family:'Inter',sans-serif;outline:none;transition:border-color .15s}
.filter-bar select:focus,.filter-bar input:focus{border-color:var(--accent)}
.btn-export{display:inline-flex;align-items:center;gap:6px;padding:9px 16px;background:#16a34a;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;font-family:'Inter',sans-serif;text-decoration:none;transition:background .15s}
.btn-export:hover{background:#15803d;color:#fff}
.btn-filter{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:var(--accent);color:#fff;border:none;border-radius:7px;font-size:13px;font-weight:700;cursor:pointer;font-family:'Inter',sans-serif;transition:background .15s}
.btn-filter:hover{background:var(--accent-h)}
.btn-reset{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;background:var(--surface);color:var(--muted);border:1.5px solid var(--border);border-radius:7px;font-size:13px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif;text-decoration:none;transition:all .15s}
.btn-reset:hover{border-color:var(--accent);color:var(--accent)}
.it-report-table{min-width:1220px;table-layout:auto}
.badge-status.pending { background: rgba(245,158,11,.12); color: #d97706; border-radius: 4px; padding: 4px 8px; font-weight: 600; font-size: 11px; }
.badge-status.completed { background: rgba(34,197,94,.12); color: #16a34a; border-radius: 4px; padding: 4px 8px; font-weight: 600; font-size: 11px; }
</style>

<!-- PAGE HEADER -->
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;gap:16px;flex-wrap:wrap">
  <div>
    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:5px">
      Admin &rsaquo; <span style="color:var(--accent)">Reports</span>
    </div>
    <h4 style="font-family:'Inter',sans-serif;font-weight:800;font-size:22px;color:var(--text);margin:0">IT Peripheral Loans Report</h4>
    <p style="font-size:13px;color:var(--muted);margin:4px 0 0">System-wide peripheral loans analytics</p>
  </div>
  <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
    <a href="{{ route('it.reports.it') }}" class="rpt-tab">
      <i class="bi bi-box-seam-fill"></i> IT Assets
    </a>
    <a href="{{ route('it.reports.non-it') }}" class="rpt-tab">
      <i class="bi bi-archive-fill"></i> Non-IT Assets
    </a>
    <a href="{{ route('it.reports.loans') }}" class="rpt-tab active">
      <i class="bi bi-person-workspace"></i> Peripheral Loans
    </a>
    <a href="{{ route('it.reports.loans.export') }}?{{ http_build_query(request()->only(['year','month','company_code','department_id','asset_class'])) }}"
       class="btn-export">
      <i class="bi bi-file-earmark-excel-fill"></i> Export Excel
    </a>
  </div>
</div>

<!-- STAT CARDS -->
<div class="stats-grid">
  @foreach([
    ['Total Loans',    'bi-card-checklist',  '#0284c7','rgba(2,132,199,.12)',  $stats['total']],
    ['Pending Verification', 'bi-clock-history', '#d97706','rgba(217,119,6,.12)',  $stats['pending']],
    ['Currently Loaned', 'bi-person-workspace', '#2563eb','rgba(37,99,235,.12)', $stats['active']],
    ['Completed',      'bi-check-circle-fill', '#16a34a','rgba(22,163,74,.12)',  $stats['completed']],
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
<form method="GET" action="{{ route('it.reports.loans') }}" class="filter-bar">
  <div>
    <label>Year</label>
    <select name="year" style="width:120px">
      <option value="">Any Year</option>
      @for($y = date('Y'); $y >= 2020; $y--)
        <option value="{{ $y }}" @selected(request('year') == $y)>{{ $y }}</option>
      @endfor
    </select>
  </div>
  <div>
    <label>Month</label>
    <select name="month" style="width:140px">
      <option value="">Any Month</option>
      @foreach(['01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'May','06'=>'Jun','07'=>'Jul','08'=>'Aug','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec'] as $m => $name)
        <option value="{{ $m }}" @selected(request('month') == $m)>{{ $name }}</option>
      @endforeach
    </select>
  </div>
  <div>
    <label>Company</label>
    <select name="company_code" id="filterCompany" style="width:140px">
      <option value="">All Companies</option>
      @foreach($companies as $company)
        <option value="{{ $company->code }}" @selected(request('company_code') == $company->code)>{{ $company->code }}</option>
      @endforeach
    </select>
  </div>
  <div>
    <label>Department</label>
    <select name="department_id" id="filterDepartment" style="width:180px">
      <option value="">All Departments</option>
      @foreach($departments as $dept)
        <option value="{{ $dept->id }}" data-company="{{ $dept->company }}" @selected(request('department_id') == $dept->id)>{{ $dept->name }}</option>
      @endforeach
    </select>
  </div>
  <div>
    <label>Asset Category</label>
    <select name="asset_class" style="width:180px">
      <option value="">All Categories</option>
      @foreach($assetClasses as $ac)
        <option value="{{ $ac->name }}" @selected(request('asset_class') == $ac->name)>{{ $ac->name }}</option>
      @endforeach
    </select>
  </div>
  <div style="display:flex;gap:8px;align-items:flex-end">
    <button type="submit" class="btn-filter"><i class="bi bi-funnel-fill"></i> Filter</button>
    @if(request()->hasAny(['year','month','company_code','department_id','asset_class']))
    <a href="{{ route('it.reports.loans') }}" class="btn-reset"><i class="bi bi-x-lg"></i> Reset</a>
    @endif
  </div>
</form>

<!-- FULL TABLE -->
<div class="table-card">
  <div class="table-card-header" style="justify-content:space-between">
    <div>
      <div class="table-card-title">Peripheral Loans</div>
      <div style="font-size:12px;color:var(--muted);margin-top:2px">
        Showing {{ $loans->firstItem() ?? 0 }}-{{ $loans->lastItem() ?? 0 }} of {{ $loans->total() }} records
      </div>
    </div>
  </div>
  <div class="table-responsive">
    <table class="table it-report-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Date Requested</th>
          <th>Referral Code</th>
          <th>Staff Name</th>
          <th>Department</th>
          <th>Item Description</th>
          <th>Asset Category</th>
          <th>Asset No.</th>
          <th>Loan Period</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
      @forelse($loans as $i => $loan)
      <tr>
        <td style="color:var(--muted);font-size:12px">{{ $loans->firstItem() + $i }}</td>
        <td style="font-size:12px;white-space:nowrap;color:var(--muted)">{{ $loan->created_at->format('d/m/Y') }}</td>
        <td><strong style="color:var(--text);font-size:12px">{{ $loan->referral_code }}</strong></td>
        <td style="font-size:12px">{{ $loan->staff->name ?? 'Unknown' }}</td>
        <td style="font-size:12px;color:var(--muted)">{{ $loan->staff->department->name ?? 'Unknown' }}</td>
        <td style="font-size:12px;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $loan->inventoryItem->description ?? '' }}">
          {{ $loan->inventoryItem->description ?? 'Unknown' }}
        </td>
        <td style="font-size:12px;color:var(--muted)">{{ $loan->inventoryItem->asset_class ?? 'Unknown' }}</td>
        <td><code style="color:var(--accent);font-size:12px">{{ $loan->inventoryItem->asset_number ?? 'Unknown' }}</code></td>
        <td style="font-size:12px">
          {{ $loan->loan_start_date ? $loan->loan_start_date->format('d/m/y') : 'N/A' }} 
          <br>to<br> 
          {{ $loan->loan_end_date ? $loan->loan_end_date->format('d/m/y') : 'N/A' }}
        </td>
        <td>
          @if(in_array($loan->status, ['Pending Verification', 'Pending Return', 'Pending Endorse']))
            <span class="badge-status pending">{{ $loan->status }}</span>
          @else
            <span class="badge-status completed">{{ $loan->status }}</span>
          @endif
        </td>
      </tr>
      @empty
      <tr><td colspan="10" style="text-align:center;color:var(--muted);padding:40px">No peripheral loans match the selected filters.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
  @if($loans->hasPages())
  <div style="padding:14px 20px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px">
    <div style="font-size:12px;color:var(--muted)">
      Page {{ $loans->currentPage() }} of {{ $loans->lastPage() }}
    </div>
    {{ $loans->links() }}
  </div>
  @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const companySelect = document.getElementById('filterCompany');
  const deptSelect = document.getElementById('filterDepartment');
  const allDeptOptions = Array.from(deptSelect.options);

  function filterDepartments() {
    const selectedCompany = companySelect.value;
    let hasSelectedValidOption = false;

    deptSelect.innerHTML = '';
    
    allDeptOptions.forEach(opt => {
      if (opt.value === '' || selectedCompany === '' || opt.dataset.company === selectedCompany) {
        deptSelect.appendChild(opt);
        if (opt.selected) hasSelectedValidOption = true;
      }
    });

    if (!hasSelectedValidOption && deptSelect.options.length > 0) {
      deptSelect.value = '';
    }
  }

  companySelect.addEventListener('change', filterDepartments);
  filterDepartments();
});
</script>
@endsection
