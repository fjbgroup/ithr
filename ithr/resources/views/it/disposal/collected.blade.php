@extends('it.layouts.app')

@section('title', 'Collected Proofs')
@section('page_title', 'Collected Proofs')

@section('content')

<!-- PAGE HEADER -->
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px">
  <div>
    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:5px">
      E-Waste &rsaquo; <span style="color:var(--accent)">Collected Proofs</span>
    </div>
    <h4 style="font-family:'Inter',sans-serif;font-weight:800;font-size:22px;color:var(--text);margin:0">Collected Proofs</h4>
    <p style="font-size:13px;color:var(--muted);margin:4px 0 0">E-waste items confirmed as physically collected for disposal</p>
  </div>
  <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
    <form method="GET" action="{{ route('it.ewaste.collection-invoice') }}" target="_blank" style="display:flex;align-items:center;gap:6px;flex-wrap:wrap">
      <div style="display:flex;align-items:center;gap:6px;background:var(--surface);border:1.5px solid var(--border);border-radius:9px;padding:5px 10px">
        <i class="bi bi-calendar3" style="color:var(--muted);font-size:13px;flex-shrink:0"></i>
        <input type="date" name="from" id="invoiceFrom" value="{{ now()->toDateString() }}"
          style="background:transparent;border:none;color:var(--text);font-size:12px;font-family:'Inter',sans-serif;outline:none;cursor:pointer">
        <span style="font-size:12px;color:var(--muted)">—</span>
        <input type="date" name="to" id="invoiceTo" value="{{ now()->toDateString() }}"
          style="background:transparent;border:none;color:var(--text);font-size:12px;font-family:'Inter',sans-serif;outline:none;cursor:pointer">
        <button type="button" onclick="resetToToday()" title="Reset to today"
          style="background:rgba(2,132,199,.12);color:var(--accent);border:none;border-radius:5px;padding:2px 8px;font-size:10px;font-weight:700;cursor:pointer;font-family:'Inter',sans-serif;white-space:nowrap">
          Today
        </button>
      </div>
      <button type="submit" class="btn-primary-custom" style="padding:8px 16px;font-size:12px;gap:6px">
        <i class="bi bi-file-earmark-text-fill"></i> Generate Invoice
      </button>
    </form>
    <a href="{{ route('it.ewaste.collection-invoice') }}" target="_blank" class="btn-secondary-custom" style="padding:8px 14px;font-size:12px">
      <i class="bi bi-printer"></i> All Items
    </a>
  </div>
</div>

<!-- STAT STRIP -->
<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px;margin-bottom:24px">
  <div style="background:var(--surface);border:1px solid var(--border);border-left:4px solid #16a34a;border-radius:10px;padding:16px 20px;display:flex;align-items:center;gap:14px">
    <div style="width:42px;height:42px;border-radius:10px;background:rgba(22,163,74,.12);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0">
      <i class="bi bi-truck" style="color:#16a34a"></i>
    </div>
    <div>
      <div style="font-size:26px;font-weight:800;color:var(--text);line-height:1">{{ $cpCount }}</div>
      <div style="font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-top:3px">Total Collected</div>
    </div>
  </div>
  <div style="background:var(--surface);border:1px solid var(--border);border-left:4px solid #2563eb;border-radius:10px;padding:16px 20px;display:flex;align-items:center;gap:14px">
    <div style="width:42px;height:42px;border-radius:10px;background:rgba(37,99,235,.12);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0">
      <i class="bi bi-calendar-check" style="color:#2563eb"></i>
    </div>
    <div>
      <div style="font-size:26px;font-weight:800;color:var(--text);line-height:1">{{ $cpThisMonth }}</div>
      <div style="font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-top:3px">This Month</div>
    </div>
  </div>
</div>

<!-- TABLE CARD -->
@if($cpCount === 0)
<div style="background:var(--surface);border:1.5px dashed var(--border);border-radius:14px;padding:64px 20px;text-align:center">
  <i class="bi bi-truck" style="font-size:36px;color:var(--muted);display:block;margin-bottom:14px"></i>
  <div style="font-weight:700;font-size:15px;color:var(--text);margin-bottom:4px">No items collected yet</div>
  <div style="font-size:13px;color:var(--muted);margin-bottom:16px">Items marked as Collected in E-Waste will appear here</div>
  <a href="{{ route('it.ewaste.index') }}" class="btn-primary-custom" style="font-size:13px">
    <i class="bi bi-recycle"></i> Go to E-Waste
  </a>
</div>

@else
<div class="table-card">
  <div style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
    <div style="font-size:13px;color:var(--muted)">
      <strong style="color:var(--text)">{{ $cpCount }}</strong> collected item{{ $cpCount !== 1 ? 's' : '' }}
    </div>
  </div>
  <div class="table-responsive">
    <table class="table table-hover" style="font-family:'Inter',sans-serif;margin:0">
      <thead><tr>
        <th style="width:36px">#</th>
        <th>Asset</th>
        <th>Serial No.</th>
        <th>Authorised By</th>
        <th>Date Collected</th>
        <th>Proof Doc</th>
        <th>Actions</th>
      </tr></thead>
      <tbody>
      @foreach($items as $i => $item)
      @php
        $proofFile = '';
        if ($item->notes && preg_match('/Proof: ([^\s|]+)/', $item->notes, $pm)) {
          $proofFile = $pm[1];
        }
        $hasProof = $proofFile && file_exists(public_path($proofFile));
        $dateDisposed = $item->date_disposed ? $item->date_disposed->format('d M Y') : null;
        $dayName      = $item->date_disposed ? $item->date_disposed->format('l') : null;
      @endphp
      <tr>
        <td style="color:var(--muted);font-size:12px">{{ $i + 1 }}</td>
        <td>
          <div style="font-size:13px;font-weight:700;color:var(--text)">{{ $item->description }}</div>
          <div style="display:flex;align-items:center;gap:6px;margin-top:3px">
            <span style="background:rgba(59,130,246,.1);color:#2563eb;border-radius:4px;padding:1px 7px;font-size:11px;font-weight:700">{{ $item->asset_class }}</span>
            @if($item->asset_number)
            <span style="font-size:12px;color:var(--accent);font-weight:600">{{ $item->asset_number }}</span>
            @endif
          </div>
        </td>
        <td style="font-size:13px;color:var(--muted)">{{ $item->serial_number ?: '—' }}</td>
        <td>
          @if($item->writeoff_name)
          <div style="font-size:13px;font-weight:600;color:var(--text)">{{ $item->writeoff_name }}</div>
          @if($item->writeoff_designation)
          <div style="font-size:11px;color:var(--muted)">{{ $item->writeoff_designation }}</div>
          @endif
          @else
          <span style="font-size:13px;color:var(--muted)">{{ optional($item->creator)->full_name ?: '—' }}</span>
          @endif
        </td>
        <td>
          @if($dateDisposed)
          <div style="font-size:13px;font-weight:600;color:var(--text)">{{ $dateDisposed }}</div>
          <div style="font-size:11px;color:var(--muted)">{{ $dayName }}</div>
          @else
          <span style="color:var(--muted)">—</span>
          @endif
        </td>
        <td>
          @if($hasProof)
          <a href="{{ asset($proofFile) }}" target="_blank"
            style="display:inline-flex;align-items:center;gap:5px;background:rgba(37,99,235,.08);color:#2563eb;border:1px solid rgba(37,99,235,.2);border-radius:7px;padding:5px 12px;font-size:12px;font-weight:700;text-decoration:none">
            <i class="bi bi-file-earmark-text"></i> View
          </a>
          @else
          <span style="display:inline-flex;align-items:center;gap:5px;background:var(--body-bg);color:var(--muted);border:1px solid var(--border);border-radius:7px;padding:5px 12px;font-size:12px;font-weight:600">
            <i class="bi bi-dash"></i> None
          </span>
          @endif
        </td>
        <td>
          <div style="display:flex;gap:6px;align-items:center">
            <a href="{{ route('it.ewaste.collection-invoice', ['from' => $item->date_disposed?->toDateString(), 'to' => $item->date_disposed?->toDateString()]) }}" target="_blank"
              style="display:inline-flex;align-items:center;gap:4px;background:rgba(2,132,199,.1);color:var(--accent);border:1px solid rgba(2,132,199,.25);border-radius:6px;padding:5px 11px;font-size:11px;font-weight:700;text-decoration:none">
              <i class="bi bi-receipt"></i> Invoice
            </a>
            <form method="POST" action="{{ route('it.ewaste.uncollect', $item->id) }}" style="margin:0"
              onsubmit="return confirm('Revert this item back to Approved?')">
              @csrf
              <button type="submit"
                style="display:inline-flex;align-items:center;gap:4px;background:rgba(239,68,68,.07);color:#dc2626;border:1px solid rgba(239,68,68,.2);border-radius:6px;padding:5px 11px;font-size:11px;font-weight:700;cursor:pointer;font-family:'Inter',sans-serif">
                <i class="bi bi-arrow-counterclockwise"></i> Undo
              </button>
            </form>
          </div>
        </td>
      </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif

@push('scripts')
<script>
function resetToToday() {
  const today = new Date().toISOString().split('T')[0];
  document.getElementById('invoiceFrom').value = today;
  document.getElementById('invoiceTo').value   = today;
}
</script>
@endpush

@endsection

