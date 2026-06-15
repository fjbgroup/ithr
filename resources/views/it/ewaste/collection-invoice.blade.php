<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-Waste Collection Invoice — {{ $ref }}</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Segoe UI', Arial, sans-serif; background: #f1f5f9; color: #1e293b; font-size: 13px; }
    .page-wrap { max-width: 950px; margin: 0 auto; padding: 20px; }

    /* Screen nav */
    .screen-nav {
      background: #142b47; color: #fff; padding: 12px 20px;
      display: flex; align-items: center; justify-content: space-between;
      border-radius: 10px; margin-bottom: 20px;
    }
    .screen-nav .nav-title { font-size: 14px; font-weight: 600; }
    .screen-nav .nav-actions { display: flex; gap: 8px; }
    .btn-nav {
      display: inline-flex; align-items: center; gap: 6px;
      padding: 6px 14px; border-radius: 8px; border: none; cursor: pointer;
      font-size: 12px; font-weight: 600; text-decoration: none; transition: opacity .15s;
    }
    .btn-nav:hover { opacity: .85; }
    .btn-print { background: #0284c7; color: #fff; }
    .btn-back  { background: rgba(255,255,255,.15); color: #fff; }

    /* Invoice card */
    .invoice-card {
      background: #fff; border-radius: 12px;
      box-shadow: 0 2px 16px rgba(0,0,0,.1);
      overflow: hidden;
    }

    /* Header */
    .invoice-header {
      background: linear-gradient(135deg, #142b47 0%, #0f2038 100%); color: #fff;
      padding: 28px 36px 24px;
      display: flex; align-items: flex-start; justify-content: space-between;
    }
    .invoice-header .brand { display: flex; align-items: center; gap: 14px; }
    .invoice-header .brand img { height: 48px; filter: brightness(0) invert(1); }
    .invoice-header .brand-text h1 { font-size: 17px; font-weight: 700; }
    .invoice-header .brand-text p  { font-size: 11px; opacity: .7; margin-top: 3px; }
    .invoice-header .inv-label { text-align: right; }
    .invoice-header .inv-title { font-size: 20px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase; }
    .invoice-header .inv-ref   { font-size: 13px; font-weight: 600; opacity: .85; margin-top: 6px; }
    .invoice-header .inv-date  { font-size: 11px; opacity: .65; margin-top: 3px; }

    /* Info strip */
    .info-strip {
      background: #f8fafc; border-bottom: 1px solid #e2e8f0;
      padding: 16px 36px;
      display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;
    }
    .info-cell { display: flex; flex-direction: column; gap: 3px; }
    .info-cell .label { font-size: 10px; color: #64748b; text-transform: uppercase; letter-spacing: .5px; }
    .info-cell .value { font-size: 14px; font-weight: 700; color: #1e293b; }
    .info-cell .value.green { color: #16a34a; }

    /* Badge */
    .badge {
      display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600;
    }
    .badge-collected { background: #dcfce7; color: #16a34a; }

    /* Items table */
    .items-section { padding: 24px 36px; }
    .items-section h3 { font-size: 13px; font-weight: 700; color: #142b47; margin-bottom: 14px; text-transform: uppercase; letter-spacing: .5px; }
    table { width: 100%; border-collapse: collapse; font-size: 12px; }
    thead tr { background: #142b47; color: #fff; }
    thead th { padding: 10px 12px; text-align: left; font-weight: 600; font-size: 11px; text-transform: uppercase; letter-spacing: .3px; }
    tbody tr { border-bottom: 1px solid #f1f5f9; }
    tbody tr:hover { background: #f8fafc; }
    tbody td { padding: 10px 12px; vertical-align: top; }
    tfoot tr { background: #f1f5f9; }
    tfoot td { padding: 10px 12px; font-weight: 700; font-size: 13px; }

    /* Summary + signatures */
    .bottom-section {
      padding: 24px 36px; border-top: 1px solid #e2e8f0;
      display: grid; grid-template-columns: 1fr 1fr; gap: 24px;
    }
    .summary-box {
      background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 10px; padding: 18px;
    }
    .summary-box h4 { font-size: 12px; font-weight: 700; color: #0284c7; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 12px; }
    .summary-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e0f2fe; font-size: 13px; }
    .summary-row:last-child { border-bottom: none; font-weight: 700; }

    .sig-area { display: flex; flex-direction: column; gap: 14px; }
    .sig-block {
      border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px; background: #fafbfc; flex: 1;
    }
    .sig-block .sig-role { font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .6px; margin-bottom: 10px; }
    .sig-block .sig-line { border-bottom: 1px dashed #cbd5e1; height: 48px; margin-bottom: 8px; }
    .sig-block .sig-name { font-size: 12px; font-weight: 600; color: #1e293b; }
    .sig-block .sig-title { font-size: 11px; color: #64748b; }

    /* Footer */
    .invoice-footer {
      background: #f8fafc; border-top: 1px solid #e2e8f0;
      padding: 14px 36px;
      display: flex; align-items: center; justify-content: space-between;
      font-size: 11px; color: #94a3b8;
    }

    /* Empty state */
    .empty-state {
      padding: 60px 36px; text-align: center; color: #94a3b8;
    }
    .empty-state i { font-size: 48px; display: block; margin-bottom: 12px; }

    @media print {
      body { background: #fff; }
      .no-print { display: none !important; }
      .page-wrap { padding: 0; max-width: 100%; }
      .invoice-card { box-shadow: none; border-radius: 0; }
    }
  </style>
</head>
<body>
<div class="page-wrap">

  {{-- Screen nav --}}
  <div class="screen-nav no-print">
    <div class="nav-title"><i class="bi bi-receipt me-2"></i>E-Waste Collection Invoice</div>
    <div class="nav-actions">
      <button onclick="window.print()" class="btn-nav btn-print"><i class="bi bi-printer"></i> Print</button>
      <a href="{{ route('it.ewaste.collected') }}" class="btn-nav btn-back"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
  </div>

  {{-- Date range filter (screen only) --}}
  <div class="no-print" style="background:#fff;border-radius:10px;padding:16px 20px;margin-bottom:16px;display:flex;align-items:center;gap:14px;box-shadow:0 1px 6px rgba(0,0,0,.08)">
    <form method="GET" action="{{ route('it.ewaste.collection-invoice') }}" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
      <label style="font-size:12px;font-weight:600;color:#64748b">Date Range:</label>
      <input type="date" name="from" value="{{ $from }}" class="form-control" style="max-width:160px;font-size:12px;padding:6px 10px;border:1px solid #e2e8f0;border-radius:7px">
      <span style="font-size:12px;color:#94a3b8">to</span>
      <input type="date" name="to" value="{{ $to }}" class="form-control" style="max-width:160px;font-size:12px;padding:6px 10px;border:1px solid #e2e8f0;border-radius:7px">
      <button type="submit" style="padding:6px 14px;border-radius:7px;border:none;background:#0284c7;color:#fff;font-size:12px;font-weight:600;cursor:pointer">
        <i class="bi bi-search"></i> Filter
      </button>
    </form>
  </div>

  {{-- Invoice card --}}
  <div class="invoice-card">

    {{-- Header --}}
    <div class="invoice-header">
      <div class="brand">
        @if(file_exists(public_path('assets/logo.png')))
        <img src="{{ asset('assets/logo.png') }}" alt="FJB Logo">
        @endif
        <div class="brand-text">
          <h1>FJB Inventory Management</h1>
          <p>E-Waste Collection Record</p>
        </div>
      </div>
      <div class="inv-label">
        <div class="inv-title">Collection Invoice</div>
        <div class="inv-ref">Ref: {{ $ref }}</div>
        <div class="inv-date">Generated: {{ now()->format('d M Y, h:i A') }}</div>
      </div>
    </div>

    {{-- Info strip --}}
    <div class="info-strip">
      <div class="info-cell">
        <div class="label">Period From</div>
        <div class="value">{{ \Carbon\Carbon::parse($from)->format('d M Y') }}</div>
      </div>
      <div class="info-cell">
        <div class="label">Period To</div>
        <div class="value">{{ \Carbon\Carbon::parse($to)->format('d M Y') }}</div>
      </div>
      <div class="info-cell">
        <div class="label">Status</div>
        <div class="value"><span class="badge badge-collected">Collected</span></div>
      </div>
      <div class="info-cell">
        <div class="label">Total Items</div>
        <div class="value green">{{ $items->count() }} item{{ $items->count() !== 1 ? 's' : '' }}</div>
      </div>
    </div>

    @if($items->isEmpty())
    <div class="empty-state">
      <i class="bi bi-inbox"></i>
      No collected items found for the selected date range.
    </div>
    @else

    {{-- Items table --}}
    <div class="items-section">
      <h3>Collected E-Waste Items</h3>
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Description</th>
            <th>Serial No.</th>
            <th>Asset Class</th>
            <th>Disposal Method</th>
            <th>Vendor / Collector</th>
            <th>Cert. No.</th>
            <th>Date Collected</th>
          </tr>
        </thead>
        <tbody>
          @foreach($items as $i => $row)
          <tr>
            <td>{{ $i + 1 }}</td>
            <td>
              <div style="font-weight:600">{{ $row->description }}</div>
              @if($row->brand || $row->model)
              <div style="font-size:11px;color:#64748b">{{ trim(($row->brand ?? '') . ' ' . ($row->model ?? '')) }}</div>
              @endif
            </td>
            <td><code style="font-size:11px;color:#64748b">{{ $row->serial_number ?: '—' }}</code></td>
            <td style="font-size:12px">{{ $row->asset_class }}</td>
            <td style="font-size:12px">{{ $row->disposal_method ?: '—' }}</td>
            <td style="font-size:12px">{{ $row->vendor_collector ?: '—' }}</td>
            <td><code style="font-size:11px">{{ $row->certificate_number ?: '—' }}</code></td>
            <td style="font-size:12px">{{ $row->date_disposed ? $row->date_disposed->format('d M Y') : '—' }}</td>
          </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr>
            <td colspan="7" style="text-align:right;color:#64748b;font-size:12px">Total Items Collected:</td>
            <td>{{ $items->count() }}</td>
          </tr>
        </tfoot>
      </table>
    </div>

    {{-- Summary + Signatures --}}
    <div class="bottom-section">
      <div class="summary-box">
        <h4>Collection Summary</h4>
        <div class="summary-row">
          <span>Total Items Collected</span>
          <span>{{ $items->count() }}</span>
        </div>
        @php
          $byClass = $items->groupBy('asset_class');
          $topClass = $byClass->sortByDesc(fn($g) => $g->count())->keys()->first();
        @endphp
        @if($topClass)
        <div class="summary-row">
          <span>Top Asset Class</span>
          <span>{{ $topClass }} ({{ $byClass[$topClass]->count() }})</span>
        </div>
        @endif
        <div class="summary-row">
          <span>Period</span>
          <span>{{ \Carbon\Carbon::parse($from)->format('d M Y') }} – {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</span>
        </div>
        <div class="summary-row">
          <span style="font-weight:700">Invoice Reference</span>
          <span>{{ $ref }}</span>
        </div>
      </div>

      <div class="sig-area">
        <div class="sig-block">
          <div class="sig-role">Prepared By</div>
          <div class="sig-line"></div>
          <div class="sig-name">{{ auth('it')->user()->full_name }}</div>
          <div class="sig-title">{{ auth('it')->user()->roleName() ?? 'Finance / Admin' }}</div>
        </div>
        <div class="sig-block">
          <div class="sig-role">Approved By</div>
          <div class="sig-line"></div>
          <div class="sig-name">___________________________</div>
          <div class="sig-title">Authorised Signatory</div>
        </div>
      </div>
    </div>

    @endif

    {{-- Footer --}}
    <div class="invoice-footer">
      <span>FJB Inventory Management System &bull; E-Waste Collection Invoice</span>
      <span>Ref: {{ $ref }}</span>
    </div>

  </div>{{-- end invoice-card --}}
</div>{{-- end page-wrap --}}
</body>
</html>

