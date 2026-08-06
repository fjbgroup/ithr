@extends('it.layouts.app')

@section('title', 'IT Peripheral Loan')
@section('page_title', 'IT Peripheral Loan')

@push('styles')
<style>
  .badge-status.pending { background: rgba(245,158,11,.12); color: #d97706; border-radius: 4px; padding: 4px 8px; font-weight: 600; font-size: 11px; }
  .badge-status.completed { background: rgba(34,197,94,.12); color: #16a34a; border-radius: 4px; padding: 4px 8px; font-weight: 600; font-size: 11px; }
  
  .data-table { width:100% !important; border-collapse: collapse; }
  .data-table td, .data-table th { vertical-align:middle !important; padding:12px 16px !important; }
  .data-table th { text-transform:uppercase; font-size:11px; font-weight:700; letter-spacing:0.05em; color:var(--muted); border-bottom: 2px solid var(--border); background: #f8fafc; white-space:nowrap; }
  .data-table td { font-size: 13px; border-bottom: 1px solid var(--border); }
</style>
@endpush

@section('content')
<div class="page-body">
  <!-- PAGE HEADER -->
  <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px">
    <div>
      <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:5px">
        Inventory &rsaquo; <span style="color:var(--accent)">IT Peripheral Loan</span>
      </div>
      <h4 style="font-family:'Inter',sans-serif;font-weight:800;font-size:22px;color:var(--text);margin:0">IT Peripheral Loan</h4>
      <p style="font-size:13px;color:var(--muted);margin:4px 0 0">Manage and track peripheral loan requests</p>
    </div>
    <div style="display:flex;gap:8px;align-items:center">
      <button onclick="document.getElementById('newLoanModal').style.display='flex'" class="btn-primary-custom" style="padding:10px 20px;font-size:13px">
        <i class="bi bi-plus-lg"></i> Submit Loan Form
      </button>
    </div>
  </div>
  <!-- STAT STRIP -->
  <div class="stats-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-bottom:24px">
    @foreach([
      ['bi-box-seam-fill',    'rgba(2,132,199,.12)',  '#0284c7', $stat_total,     'Total Loans',      '#0284c7'],
      ['bi-clock-history',    'rgba(217,119,6,.12)',  '#d97706', $stat_pending,   'Pending Pickup',   '#d97706'],
      ['bi-person-workspace', 'rgba(59,130,246,.12)', '#2563eb', $stat_active,    'Currently Loaned', '#2563eb'],
      ['bi-check-circle-fill','rgba(22,163,74,.12)',  '#16a34a', $stat_completed, 'Completed',        '#16a34a'],
    ] as [$icon,$bg,$color,$val,$lbl,$border])
    <div style="background:var(--surface,#fff);border:1px solid var(--border,#e2e8f0);border-left:4px solid {{ $border }};border-radius:12px;padding:16px 20px;display:flex;align-items:center;gap:14px;box-shadow:0 1px 3px rgba(0,0,0,.07),0 4px 14px rgba(0,0,0,.05)">
      <div style="width:44px;height:44px;border-radius:10px;background:{{ $bg }};display:flex;align-items:center;justify-content:center;font-size:19px;flex-shrink:0">
        <i class="bi {{ $icon }}" style="color:{{ $color }}"></i>
      </div>
      <div>
        <div style="font-size:26px;font-weight:800;color:var(--text,#1e293b);line-height:1;font-family:'Inter',sans-serif">{{ number_format($val) }}</div>
        <div style="font-size:11px;color:var(--muted,#64748b);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-top:3px">{{ $lbl }}</div>
      </div>
    </div>
    @endforeach
  </div>

  @if(session('success'))
    <div class="alert-success-custom"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert-danger-custom"><i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}</div>
  @endif

  <div class="table-card mt-4">
    <div class="table-card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px">
      <div class="table-card-title">Loan Records</div>
      <form method="GET" action="{{ route('it.loans.index') }}" style="display:flex; gap:8px; align-items:center; margin:0">
        <select name="year" onchange="this.form.submit()" class="form-select" style="min-width:100px; padding:6px 12px; font-size:13px">
          <option value="">Any Year</option>
          @for($y = date('Y'); $y >= 2020; $y--)
            <option value="{{ $y }}" @selected(request('year') == $y)>{{ $y }}</option>
          @endfor
        </select>
        
        <select name="month" onchange="this.form.submit()" class="form-select" style="min-width:110px; padding:6px 12px; font-size:13px">
          <option value="">Any Month</option>
          @foreach(['01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'May','06'=>'Jun','07'=>'Jul','08'=>'Aug','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec'] as $m => $name)
            <option value="{{ $m }}" @selected(request('month') == $m)>{{ $name }}</option>
          @endforeach
        </select>

        <select name="department_id" onchange="this.form.submit()" class="form-select" style="min-width:150px; padding:6px 12px; font-size:13px">
          <option value="">All Departments</option>
          @if(isset($departments))
            @foreach($departments as $dept)
              <option value="{{ $dept->id }}" @selected(request('department_id') == $dept->id)>{{ $dept->name }}</option>
            @endforeach
          @endif
        </select>

        <select name="asset_class" onchange="this.form.submit()" class="form-select" style="min-width:150px; padding:6px 12px; font-size:13px">
          <option value="">All Asset Categories</option>
          @if(isset($assetClasses))
            @foreach($assetClasses as $ac)
              <option value="{{ $ac->name }}" @selected(request('asset_class') == $ac->name)>{{ $ac->name }}</option>
            @endforeach
          @endif
        </select>

        @if(request()->hasAny(['year', 'month', 'department_id', 'asset_class']))
          <a href="{{ route('it.loans.index') }}" class="btn btn-sm btn-outline-secondary" style="font-size:13px; padding:6px 12px">Clear</a>
        @endif
      </form>
    </div>
    <div class="table-responsive" style="border:1px solid var(--border);border-radius:8px;overflow:hidden">
      <table class="data-table" id="loansTable">
        <thead>
          <tr>
            <th>Date</th>
            <th>Referral Code</th>
            <th>Staff</th>
            <th>Item</th>
            <th>Loan Period</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($loans as $loan)
          <tr>
            <td>{{ $loan->created_at->format('d M Y') }}</td>
            <td><strong>{{ $loan->referral_code }}</strong></td>
            <td>
              {{ $loan->staff->name ?? 'Unknown Staff' }}<br>
              <span class="text-muted" style="font-size:11px">{{ $loan->staff->department->name ?? '' }}</span>
            </td>
            <td>
              {{ $loan->inventoryItem->description ?? 'Unknown Item' }}<br>
              <span class="text-muted" style="font-size:11px">{{ $loan->inventoryItem->asset_number ?? '' }}</span>
            </td>
            <td>
              <span style="font-size:13px">
                {{ $loan->loan_start_date ? $loan->loan_start_date->format('d M Y') : 'N/A' }} 
                <br>to<br> 
                {{ $loan->loan_end_date ? $loan->loan_end_date->format('d M Y') : 'N/A' }}
              </span>
            </td>
            <td>
              @if($loan->status === 'Pending Verification' || $loan->status === 'Pending Return' || $loan->status === 'Pending Endorse')
                <span class="badge-status pending">{{ $loan->status }}</span>
              @else
                <span class="badge-status completed">{{ $loan->status }}</span>
              @endif
            </td>
            <td>
              @if($loan->status === 'Completed')
                <a href="{{ route('it.loans.receipt', $loan->id) }}" class="btn-icon btn-view" title="View Receipt">
                  <i class="bi bi-file-earmark-text"></i>
                </a>
              @endif
              
              @if(auth('it')->user()->isAdmin())
                @if($loan->status === 'Pending Verification')
                  <form action="{{ route('it.loans.verify-pickup', $loan->id) }}" method="POST" style="display:inline-block">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-primary" onclick="return confirm('Verify user has picked up this item?')">Verify Pickup</button>
                  </form>
                @endif
                @if($loan->status === 'Pending Endorse' || $loan->status === 'Pending Return')
                  <form action="{{ route('it.loans.endorse-return', $loan->id) }}" method="POST" style="display:inline-block">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-success" onclick="return confirm('Endorse that the item is safely returned?')">Endorse Return</button>
                  </form>
                @endif
              @endif

              @php
                $staff = \App\Models\Staff::where('email', auth('it')->user()->email)->first();
                $isOwnLoan = $staff && $loan->staff_id === $staff->id;
              @endphp

              @if($loan->status === 'Pending Return' && $isOwnLoan)
                <form action="{{ route('it.loans.user-return', $loan->id) }}" method="POST" style="display:inline-block">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-outline-warning" onclick="return confirm('Initiate return process? Please hand the item back to IT.')">Return Item</button>
                </form>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- New Loan Modal -->
<div id="newLoanModal" style="display:none;position:fixed;inset:0;z-index:9000;background:rgba(0,0,0,.5);align-items:center;justify-content:center;padding:24px">
  <div class="form-card" style="width:100%;max-width:500px;background:#fff;border-radius:12px;padding:24px;position:relative">
    <button type="button" onclick="document.getElementById('newLoanModal').style.display='none'" style="position:absolute;top:16px;right:16px;background:none;border:none;font-size:24px;cursor:pointer;color:#666;line-height:1">&times;</button>
    <h5 class="mb-4 font-weight-bold">Submit Loan Form</h5>
    <form action="{{ route('it.loans.store') }}" method="POST">
      @csrf
      <div class="mb-3">
        <label class="form-label">Asset Category</label>
        <select class="form-select" id="assetCategorySelect" required>
          <option value="">-- Select Category --</option>
          @foreach($assetClasses as $ac)
            <option value="{{ $ac->name }}">{{ $ac->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Available Item</label>
        <select class="form-select" name="inventory_item_id" id="availableItemSelect" required disabled>
          <option value="">-- Select Category First --</option>
        </select>
      </div>
      <div class="row mb-3">
        <div class="col-6">
          <label class="form-label">Start Date</label>
          <input type="date" name="loan_start_date" class="form-control" required>
        </div>
        <div class="col-6">
          <label class="form-label">End Date</label>
          <input type="date" name="loan_end_date" class="form-control" required>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">Notes / Reason (Optional)</label>
        <textarea class="form-control" name="notes" rows="3" placeholder="State reason for loan..."></textarea>
      </div>
      <div class="d-flex justify-content-end mt-4 gap-2">
        <button type="button" class="btn-secondary-custom" onclick="document.getElementById('newLoanModal').style.display='none'">Cancel</button>
        <button type="submit" class="btn-primary-custom" id="submitLoanBtn" disabled>Submit Loan</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const categorySelect = document.getElementById('assetCategorySelect');
  const itemSelect = document.getElementById('availableItemSelect');
  const submitBtn = document.getElementById('submitLoanBtn');

  categorySelect.addEventListener('change', function() {
    const catId = this.value;
    itemSelect.innerHTML = '<option value="">Loading...</option>';
    itemSelect.disabled = true;
    submitBtn.disabled = true;

    if (!catId) {
      itemSelect.innerHTML = '<option value="">-- Select Category First --</option>';
      return;
    }

    fetch(`{{ route('it.loans.available-items') }}?asset_class=${catId}`)
      .then(response => response.json())
      .then(data => {
        itemSelect.innerHTML = '<option value="">-- Select Item --</option>';
        if (data.length === 0) {
          itemSelect.innerHTML = '<option value="">No available items in this category.</option>';
        } else {
          data.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item.id;
            opt.textContent = `${item.asset_number} - ${item.description} (${item.brand} ${item.model})`;
            itemSelect.appendChild(opt);
          });
          itemSelect.disabled = false;
        }
      })
      .catch(err => {
        console.error(err);
        itemSelect.innerHTML = '<option value="">Error loading items</option>';
      });
  });

  itemSelect.addEventListener('change', function() {
    submitBtn.disabled = !this.value;
  });
});
</script>
@endpush
