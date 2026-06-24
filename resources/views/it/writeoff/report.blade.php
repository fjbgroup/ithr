<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
<title>Write-Off Authorization Form</title>
@include('partials.favicons')
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  @if(request()->boolean('embed'))
  <style>.page-wrap{margin:0 auto;}</style>
  @endif
  <style>
    @page { size: A4 portrait; margin: 15mm 18mm; }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: Arial, sans-serif;
      background: #d0d0d0;
      color: #1a1a1a;
      font-size: 12px;
      padding: 24px 0;
    }

    .page-wrap {
      width: 210mm;
      min-height: 297mm;
      margin: 0 auto;
      padding: 0;
      background: #fff;
      box-shadow: 0 4px 24px rgba(0,0,0,.25);
      display: flex;
      flex-direction: column;
    }

    /* ── Screen nav (hidden in embed & print) ── */
    .screen-nav {
      background: #142b47; color: #fff;
      padding: 10px 18px;
      display: flex; align-items: center; justify-content: space-between;
      border-radius: 8px; margin-bottom: 16px;
    }
    .screen-nav .nav-title { font-size: 13px; font-weight: 600; }
    .screen-nav .nav-actions { display: flex; gap: 8px; }
    .btn-nav {
      display: inline-flex; align-items: center; gap: 5px;
      padding: 6px 13px; border-radius: 7px; border: none; cursor: pointer;
      font-size: 12px; font-weight: 600; text-decoration: none;
    }
    .btn-print { background: #0284c7; color: #fff; }
    .btn-back  { background: rgba(255,255,255,.15); color: #fff; }

    /* ── Document card ── */
    .doc {
      background: #fff;
      border: none;
      box-shadow: none;
      display: flex;
      flex-direction: column;
      flex: 1;
    }

    /* ── Letterhead ── */
    .letterhead {
      padding: 20px 28px 14px;
      border-bottom: 3px solid #1a1a1a;
    }
    .letterhead-inner {
      display: flex;
      align-items: center;
      gap: 18px;
    }
    .letterhead-logo img {
      height: 60px;
    }
    .letterhead-logo .logo-placeholder {
      width: 60px; height: 60px;
      border: 2px solid #1a1a1a;
      display: flex; align-items: center; justify-content: center;
      font-size: 9px; font-weight: 700; color: #1a1a1a; text-align: center;
    }
    .letterhead-text {
      flex: 1;
    }
    .letterhead-text .org-name {
      font-size: 15px; font-weight: 700; color: #1a1a1a;
      text-transform: uppercase; letter-spacing: .5px;
    }
    .letterhead-text .org-sub {
      font-size: 11px; color: #444; margin-top: 2px;
    }
    .form-title-block {
      text-align: center;
      margin-top: 12px;
      padding-top: 10px;
      border-top: 1px solid #ccc;
    }
    .form-title-block .form-title {
      font-size: 14px; font-weight: 700; text-transform: uppercase;
      letter-spacing: 1.5px; color: #1a1a1a;
    }
    .form-title-block .form-subtitle {
      font-size: 10px; color: #555; margin-top: 3px; letter-spacing: .3px;
    }

    /* ── Meta info strip ── */
    .meta-strip {
      padding: 10px 28px;
      border-bottom: 1px solid #ccc;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 6px 32px;
    }
    .meta-row {
      display: flex; align-items: baseline; gap: 6px;
      font-size: 11px;
    }
    .meta-row .ml { font-weight: 700; color: #333; white-space: nowrap; min-width: 110px; }
    .meta-row .mv { color: #1a1a1a; border-bottom: 1px solid #999; flex: 1; padding-bottom: 1px; }
    .meta-row .mv.approved { color: #16a34a; font-weight: 700; }

    /* ── Section headings ── */
    .section-head {
      background: #1a1a1a;
      color: #fff;
      padding: 6px 28px;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .8px;
    }

    /* ── Asset table ── */
    .asset-wrap { padding: 0 28px 16px; }
    .asset-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 11px;
      margin-top: 12px;
    }
    .asset-table th {
      background: #f4f4f4;
      border: 1px solid #999;
      padding: 7px 9px;
      text-align: left;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .4px;
      font-size: 10px;
    }
    .asset-table td {
      border: 1px solid #999;
      padding: 7px 9px;
      vertical-align: top;
    }
    .asset-table tbody tr:nth-child(even) td { background: #fafafa; }

    /* ── Authorization row ── */
    .auth-wrap { padding: 0 28px 20px; flex: 1; }
    .auth-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      border: 1px solid #999;
      margin-top: 12px;
    }
    .auth-cell {
      padding: 12px 10px 10px;
      border-right: 1px solid #999;
      display: flex;
      flex-direction: column;
    }
    .auth-cell:last-child { border-right: none; }

    .auth-role {
      font-size: 9px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .6px;
      color: #333;
      margin-bottom: 4px;
    }
    .auth-sub {
      font-size: 9px;
      color: #666;
      margin-bottom: 10px;
    }
    .auth-sig-area {
      flex: 1;
      min-height: 72px;
      display: flex;
      align-items: flex-end;
      justify-content: flex-start;
      border-bottom: 1px solid #333;
      margin-bottom: 8px;
      padding-bottom: 4px;
    }
    .auth-sig-area img {
      max-height: 68px;
      max-width: 100%;
    }
    .auth-sig-area .no-sig {
      font-size: 9px;
      color: #bbb;
      font-style: italic;
    }
    .auth-name  { font-size: 11px; font-weight: 700; color: #1a1a1a; margin-bottom: 2px; }
    .auth-desig { font-size: 10px; color: #444; margin-bottom: 3px; }
    .auth-date  { font-size: 10px; color: #777; }

    /* ── Footer ── */
    .doc-footer {
      padding: 8px 28px;
      border-top: 1px solid #ccc;
      display: flex; align-items: center; justify-content: space-between;
      font-size: 9px; color: #888;
      background: #f9f9f9;
    }

    /* ── Print ── */
    @media print {
      body  { background: #fff; padding: 0; }
      .no-print { display: none !important; }
      .screen-nav { display: none; }
      .page-wrap { width: 100%; min-height: unset; box-shadow: none; margin: 0; padding: 0; display: block; }
      .doc { min-height: unset; flex: none; box-shadow: none; border: none; }
      .auth-wrap { flex: none; }
      .auth-grid { page-break-inside: avoid; }
    }
  </style>
</head>
<body>
<div class="page-wrap">

  {{-- Screen navigation — hidden when embedded in overlay --}}
  @if(!request()->boolean('embed'))
  <div class="screen-nav no-print">
    <div class="nav-title"><i class="bi bi-file-earmark-text" style="margin-right:6px"></i>Write-Off Authorization Form</div>
    <div class="nav-actions">
      <button onclick="window.print()" class="btn-nav btn-print"><i class="bi bi-printer"></i> Print</button>
      <a href="{{ url()->previous() }}" class="btn-nav btn-back"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
  </div>
  @endif

  <div class="doc">

    {{-- ── Letterhead ── --}}
    <div class="letterhead">
      <div class="letterhead-inner">
        <div class="letterhead-logo">
          @if(file_exists(public_path('assets/logo.png')))
            <img src="{{ asset('assets/logo.png') }}" alt="Logo">
          @else
            <div class="logo-placeholder">FJB<br>LOGO</div>
          @endif
        </div>
        <div class="letterhead-text">
          <div class="org-name">FJB Inventory Management System</div>
          <div class="org-sub">Asset Management Department &bull; Pengurusan Aset</div>
        </div>
      </div>
      <div class="form-title-block">
        <div class="form-title">Asset Write-Off Authorization Form</div>
        <div class="form-subtitle">Borang Kelulusan Penghapusan Aset</div>
      </div>
    </div>

    {{-- ── Meta strip ── --}}
    <div class="meta-strip">
      <div class="meta-row">
        <span class="ml">Submitted By</span>
        <span class="mv">{{ $item->writeoff_name ?: ($item->creator?->full_name ?? '—') }}</span>
      </div>
      <div class="meta-row">
        <span class="ml">Date Submitted</span>
        <span class="mv">{{ $item->writeoff_date ? $item->writeoff_date->format('d M Y') : ($item->created_at?->format('d M Y') ?? '—') }}</span>
      </div>
      <div class="meta-row">
        <span class="ml">No. of Items</span>
        <span class="mv">{{ $items->count() }} item{{ $items->count() !== 1 ? 's' : '' }}</span>
      </div>
      <div class="meta-row">
        <span class="ml">Reference / Batch</span>
        <span class="mv">{{ $item->batch_id ?: 'Single Submission' }}</span>
      </div>
      <div class="meta-row">
        <span class="ml">Designation</span>
        <span class="mv">{{ $item->writeoff_designation ?: '—' }}</span>
      </div>
      <div class="meta-row">
        <span class="ml">Status</span>
        @php
          $st = $item->ceo_status === 'Approved' ? 'CEO Approved' : ($item->disposal_status === 'Rejected' ? 'Rejected' : 'Pending');
        @endphp
        <span class="mv {{ $item->ceo_status === 'Approved' ? 'approved' : '' }}">
          {{ $st }}
          @if($item->finance_status && $item->finance_status !== 'Pending')
            &bull; {{ $item->finance_status }}
          @endif
        </span>
      </div>
    </div>

    {{-- ── Asset Details ── --}}
    <div class="section-head">Asset Details / Butiran Aset</div>
    <div class="asset-wrap">
      <table class="asset-table">
        <thead>
          <tr>
            <th style="width:32px">#</th>
            <th style="width:100px">Asset No.</th>
            <th style="width:90px">Class</th>
            <th>Description</th>
            <th style="width:110px">Serial No.</th>
            <th style="width:110px">Brand / Model</th>
            <th style="width:48px">Source</th>
          </tr>
        </thead>
        <tbody>
          @foreach($items as $i => $row)
          <tr>
            <td style="text-align:center">{{ $i + 1 }}</td>
            <td><code style="font-size:10px">{{ $row->asset_number ?: '—' }}</code></td>
            <td>{{ $row->asset_class }}</td>
            <td style="font-weight:600">{{ $row->description }}</td>
            <td><code style="font-size:10px;color:#555">{{ $row->serial_number ?: '—' }}</code></td>
            <td>{{ trim(($row->brand ?? '') . ' ' . ($row->model ?? '')) ?: '—' }}</td>
            <td style="text-align:center;font-weight:700;font-size:10px">{{ $row->asset_source ?? 'IT' }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- ── Authorization ── --}}
    <div class="section-head">Authorization &amp; Signatures / Kelulusan &amp; Tandatangan</div>
    <div class="auth-wrap">
      <div class="auth-grid">

        {{-- Proposed By --}}
        <div class="auth-cell">
          <div class="auth-role">Proposed By</div>
          <div class="auth-sub">Write-Off Initiator</div>
          <div class="auth-sig-area">
            @if($item->writeoff_sig_img)
              <img src="{{ $item->writeoff_sig_img }}" alt="Signature">
            @else
              <span class="no-sig">No signature captured</span>
            @endif
          </div>
          <div class="auth-name">{{ $item->writeoff_name ?: '—' }}</div>
          <div class="auth-desig">{{ $item->writeoff_designation ?: '—' }}</div>
          <div class="auth-date">{{ $item->writeoff_date ? $item->writeoff_date->format('d M Y') : '—' }}</div>
        </div>

        {{-- Checked By (HOU) --}}
        <div class="auth-cell">
          <div class="auth-role">Checked By</div>
          <div class="auth-sub">Head of Unit (HOU)</div>
          <div class="auth-sig-area">
            @if($item->hou_sig_img)
              <img src="{{ $item->hou_sig_img }}" alt="HOU Signature">
            @elseif($item->hou_status === 'Pending')
              <span class="no-sig">Awaiting HOU</span>
            @else
              <span class="no-sig">Not yet reached</span>
            @endif
          </div>
          <div class="auth-name">{{ $item->hou_signed_name ?: ($item->houUser?->full_name ?? '—') }}</div>
          <div class="auth-desig">Head of Unit</div>
          <div class="auth-date">
            @if($item->hou_signed_at) {{ $item->hou_signed_at->format('d M Y') }}
            @elseif($item->hou_status) {{ $item->hou_status }}
            @else —
            @endif
          </div>
        </div>

        {{-- Recommended By (GM) --}}
        <div class="auth-cell">
          <div class="auth-role">Recommended By</div>
          <div class="auth-sub">General Manager (GM)</div>
          <div class="auth-sig-area">
            @if($item->gm_sig_img)
              <img src="{{ $item->gm_sig_img }}" alt="GM Signature">
            @elseif($item->gm_status === 'Pending')
              <span class="no-sig">Awaiting GM</span>
            @else
              <span class="no-sig">Not yet reached</span>
            @endif
          </div>
          <div class="auth-name">{{ $item->gm_signed_name ?: ($item->currentGmUser?->full_name ?? '—') }}</div>
          <div class="auth-desig">General Manager</div>
          <div class="auth-date">
            @if($item->gm_signed_at) {{ $item->gm_signed_at->format('d M Y') }}
            @elseif($item->gm_status) {{ $item->gm_status }}
            @else —
            @endif
          </div>
        </div>

        {{-- Approved By (CEO) --}}
        <div class="auth-cell">
          <div class="auth-role">Approved By</div>
          <div class="auth-sub">Chief Executive Officer</div>
          <div class="auth-sig-area">
            @if($item->ceo_sig_img)
              <img src="{{ $item->ceo_sig_img }}" alt="CEO Signature">
            @elseif($item->ceo_status === 'Pending')
              <span class="no-sig">Awaiting CEO</span>
            @else
              <span class="no-sig">Not yet reached</span>
            @endif
          </div>
          <div class="auth-name">{{ $item->ceo_signed_name ?: ($item->ceoUser?->full_name ?? '—') }}</div>
          <div class="auth-desig">Chief Executive Officer</div>
          <div class="auth-date">
            @if($item->ceo_signed_at) {{ $item->ceo_signed_at->format('d M Y') }}
            @elseif($item->ceo_status) {{ $item->ceo_status }}
            @else —
            @endif
          </div>
        </div>

      </div>
    </div>

    {{-- ── Footer ── --}}
    <div class="doc-footer">
      <span>FJB Inventory Management System &bull; Pengurusan Aset</span>
      <span>Generated: {{ now()->format('d M Y, h:i A') }}</span>
    </div>

  </div>{{-- end .doc --}}
</div>{{-- end .page-wrap --}}
</body>
</html>

