@extends('it.layouts.app')

@section('title', 'Write Off Authorisation')
@section('page_title', 'Write Off Authorisation')

@section('content')
@php $user = auth('it')->user(); @endphp

<style>
  .wo-hou-table {
    width: 100%;
    table-layout: fixed;
    border-collapse: separate;
    border-spacing: 0;
  }
  .wo-hou-table th {
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: var(--muted);
    background: var(--surface2);
    border-bottom: 1px solid var(--border);
    padding: 10px 14px;
    white-space: nowrap;
  }
  .wo-hou-table td {
    padding: 14px;
    border-bottom: 1px solid var(--border);
    vertical-align: middle;
  }
  .wo-hou-table tbody tr:last-child td {
    border-bottom: 0;
  }
  .wo-hou-submission {
    min-width: 260px;
  }
  .wo-hou-title-row {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    min-width: 0;
  }
  .wo-hou-title {
    font-size: 13px;
    font-weight: 750;
    color: var(--text);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  .wo-hou-count {
    flex: 0 0 auto;
    border-radius: 999px;
    background: rgba(2,132,199,.10);
    color: var(--accent);
    font-size: 11px;
    font-weight: 800;
    padding: 3px 8px;
  }
  .wo-hou-asset-list {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    max-width: 100%;
  }
  .wo-hou-asset {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    max-width: 260px;
    border: 1px solid var(--border);
    border-radius: 7px;
    background: var(--body-bg);
    padding: 5px 8px;
    font-size: 11px;
    color: var(--text);
  }
  .wo-hou-asset-name {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  .wo-hou-asset-no {
    color: var(--muted);
    font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
    font-size: 10px;
    white-space: nowrap;
  }
  .wo-hou-more {
    color: var(--muted);
    font-size: 11px;
    font-weight: 700;
    padding: 5px 2px;
  }
  .wo-hou-open-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    border: 1px solid var(--border);
    border-radius: 7px;
    background: var(--surface);
    color: var(--accent);
    padding: 5px 9px;
    font-size: 11px;
    font-weight: 800;
    line-height: 1;
    cursor: pointer;
    white-space: nowrap;
  }
  .wo-hou-open-btn:hover {
    background: var(--body-bg);
    border-color: rgba(2,132,199,.35);
  }
  .wo-hou-person {
    font-size: 12px;
    font-weight: 650;
    color: var(--text);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  .wo-hou-date {
    color: var(--muted);
    font-size: 12px;
    white-space: nowrap;
  }
  .wo-hou-action {
    text-align: right;
    white-space: nowrap;
  }
  .wo-hou-modal-asset {
    display: grid;
    grid-template-columns: 34px minmax(0, 1fr) 150px 120px;
    gap: 12px;
    align-items: center;
    border: 1px solid var(--border);
    border-radius: 8px;
    background: var(--surface);
    padding: 10px 12px;
  }
  .wo-staff-form-modal {
    position: fixed;
    inset: 0;
    display: none;
    align-items: flex-start;
    justify-content: center;
    padding: 20px;
    background: rgba(0,0,0,.65);
    overflow-y: auto;
    overscroll-behavior: contain;
    z-index: 99999;
  }
  .wo-staff-form-card {
    position: sticky;
    top: 20px;
    width: 100%;
    max-width: 860px;
    max-height: calc(100vh - 40px);
    display: flex;
    flex-direction: column;
    margin: 0 auto;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 24px 60px rgba(0,0,0,.35);
    font-family: Arial, sans-serif;
    overflow: hidden;
  }
  .wo-staff-form-body {
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
  }
  .wo-staff-form-footer {
    flex: 0 0 auto;
  }
  .pfa-hou-list {
    display: grid;
    gap: 12px;
    padding: 16px 20px 20px;
  }
  .pfa-hou-card {
    border: 1px solid var(--border);
    border-radius: 10px;
    background: var(--surface);
    overflow: hidden;
  }
  .pfa-hou-card-main {
    display: grid;
    grid-template-columns: minmax(0, 1.3fr) minmax(360px, .9fr);
    gap: 18px;
    padding: 16px 18px;
    align-items: start;
  }
  .pfa-hou-title-row {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
    margin-bottom: 8px;
  }
  .pfa-hou-title {
    font-size: 14px;
    font-weight: 800;
    color: var(--text);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  .pfa-hou-meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    font-size: 11px;
    color: var(--muted);
    margin-bottom: 10px;
  }
  .pfa-hou-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    border-radius: 999px;
    padding: 3px 9px;
    font-size: 11px;
    font-weight: 800;
    white-space: nowrap;
  }
  .pfa-hou-assets {
    display: grid;
    gap: 8px;
  }
  .pfa-hou-asset-summary {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    border: 1px solid var(--border);
    border-radius: 8px;
    background: var(--body-bg);
    padding: 9px 10px;
  }
  .pfa-hou-asset-summary-text {
    min-width: 0;
    font-size: 12px;
    color: var(--muted);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  .pfa-hou-asset-dropdown {
    display: none;
    border: 1px solid var(--border);
    border-radius: 8px;
    background: var(--body-bg);
    padding: 8px;
    max-height: 240px;
    overflow-y: auto;
  }
  .pfa-hou-asset {
    display: grid;
    grid-template-columns: 30px minmax(0, 1fr);
    align-items: center;
    gap: 10px;
    border: 1px solid var(--border);
    border-radius: 8px;
    background: var(--body-bg);
    padding: 8px 10px;
    color: var(--text);
  }
  .pfa-hou-asset + .pfa-hou-asset {
    margin-top: 8px;
  }
  .pfa-hou-asset-icon {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    background: rgba(2,132,199,.10);
    color: var(--accent);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    flex-shrink: 0;
  }
  .pfa-hou-asset-title {
    font-size: 12.5px;
    font-weight: 750;
    color: var(--text);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  .pfa-hou-asset-meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 3px;
    font-size: 10px;
    color: var(--muted);
  }
  .pfa-hou-asset-meta code {
    font-size: 10px;
    color: var(--muted);
    background: transparent;
    padding: 0;
  }
  .pfa-hou-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 10px;
  }
  .wo-page-controls {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 12px 20px;
    border-top: 1px solid var(--border);
    background: var(--body-bg);
    flex-wrap: wrap;
  }
  .wo-page-links {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
  }
  .wo-page-link {
    min-width: 30px;
    height: 30px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--border);
    border-radius: 7px;
    background: var(--surface);
    color: var(--muted);
    text-decoration: none;
    font-size: 12px;
    font-weight: 800;
  }
  .wo-page-link.is-active {
    background: var(--accent);
    border-color: var(--accent);
    color: #fff;
  }
  html.dark #woFormModal > div,
  html.dark #houSignModal > div,
  html.dark #gmSignModal > div,
  html.dark #ceoSignModal > div,
  html.dark #assignHOUModal > div {
    background: var(--surface) !important;
    border: 1px solid var(--border) !important;
    color: var(--text) !important;
  }
  html.dark #woFormModal [style*="background:#fff"]:not(canvas),
  html.dark #woFormModal [style*="background: #fff"]:not(canvas),
  html.dark #houSignModal [style*="background:#fff"]:not(canvas),
  html.dark #houSignModal [style*="background: #fff"]:not(canvas),
  html.dark #gmSignModal [style*="background:#fff"]:not(canvas),
  html.dark #gmSignModal [style*="background: #fff"]:not(canvas),
  html.dark #ceoSignModal [style*="background:#fff"]:not(canvas),
  html.dark #ceoSignModal [style*="background: #fff"]:not(canvas),
  html.dark [style*="background:#f9f9f9"],
  html.dark [style*="background: #f9f9f9"],
  html.dark [style*="background:#f9fafb"],
  html.dark [style*="background: #f9fafb"],
  html.dark [style*="background:#f4f4f4"],
  html.dark [style*="background: #f4f4f4"] {
    background: var(--surface2) !important;
  }
  html.dark #woSigCanvas {
    filter: invert(1) hue-rotate(180deg);
  }
  html.dark [style*="background:#fffbeb"] {
    background: rgba(245,158,11,.12) !important;
  }
  html.dark #woFormModal [style*="color:#142b47"],
  html.dark #houSignModal [style*="color:#142b47"],
  html.dark #gmSignModal [style*="color:#142b47"],
  html.dark #ceoSignModal [style*="color:#142b47"],
  html.dark [style*="color:#1a1a1a"],
  html.dark [style*="color:#333"],
  html.dark [style*="color:#374151"] {
    color: var(--text) !important;
  }
  html.dark [style*="color:#444"],
  html.dark [style*="color:#555"],
  html.dark [style*="color:#666"],
  html.dark [style*="color:#777"],
  html.dark [style*="color:#888"],
  html.dark [style*="color:#999"] {
    color: var(--muted) !important;
  }
  html.dark #woFormModal table,
  html.dark #houSignModal table,
  html.dark #gmSignModal table,
  html.dark #ceoSignModal table,
  html.dark #woFormModal td,
  html.dark #houSignModal td,
  html.dark #gmSignModal td,
  html.dark #ceoSignModal td,
  html.dark #woFormModal th,
  html.dark #houSignModal th,
  html.dark #gmSignModal th,
  html.dark #ceoSignModal th {
    border-color: #475569 !important;
  }
  html.dark #woFormModal [style*="border-bottom:1px solid #000"],
  html.dark #houSignModal [style*="border-bottom:1.5px solid #000"],
  html.dark #gmSignModal [style*="border-bottom:1.5px solid #000"],
  html.dark #ceoSignModal [style*="border-bottom:1.5px solid #000"],
  html.dark [style*="border-bottom:1px solid #999"],
  html.dark [style*="border-top:1px solid #ccc"],
  html.dark [style*="border:1px solid #d1d5db"],
  html.dark [style*="border:1px solid #999"] {
    border-color: #64748b !important;
  }
  html.dark #woFormModal [style*="border:1.5px solid #d1d5db"] {
    background: var(--surface2) !important;
    border-color: var(--border) !important;
    color: var(--text) !important;
  }
  .pfa-hou-flow {
    display: grid;
    grid-template-columns: 1fr 26px 1fr 26px 1fr;
    align-items: start;
    gap: 0;
  }
  .pfa-step {
    min-width: 0;
    text-align: center;
  }
  .pfa-step-dot {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 2px solid var(--border);
    background: var(--surface);
    margin-bottom: 7px;
  }
  .pfa-step-label {
    font-size: 10px;
    font-weight: 800;
    color: var(--text);
    text-transform: uppercase;
    letter-spacing: .05em;
  }
  .pfa-step-sub {
    font-size: 11px;
    color: var(--muted);
    margin-top: 3px;
    line-height: 1.35;
    word-break: break-word;
  }
  .pfa-connector {
    height: 2px;
    margin-top: 16px;
    background: var(--border);
    border-radius: 999px;
  }
  .pfa-step-done .pfa-step-dot {
    border-color: #16a34a;
    background: #16a34a;
    color: #fff;
  }
  .pfa-step-active .pfa-step-dot {
    border-color: #f59e0b;
    background: #fffbeb;
    color: #d97706;
  }
  .pfa-step-wait .pfa-step-dot {
    color: var(--muted);
  }
  .pfa-connector-done {
    background: #16a34a;
  }
  @media (max-width: 760px) {
    .wo-hou-table,
    .wo-hou-table thead,
    .wo-hou-table tbody,
    .wo-hou-table tr,
    .wo-hou-table th,
    .wo-hou-table td {
      display: block;
      width: 100%;
    }
    .wo-hou-table thead {
      display: none;
    }
    .wo-hou-table tr {
      padding: 12px 14px;
      border-bottom: 1px solid var(--border);
    }
    .wo-hou-table td {
      padding: 4px 0;
      border-bottom: 0;
    }
    .wo-hou-action {
      text-align: left;
      padding-top: 10px !important;
    }
    .wo-hou-title-row {
      flex-wrap: wrap;
    }
    .wo-hou-modal-asset {
      grid-template-columns: 28px minmax(0, 1fr);
    }
    .wo-hou-modal-asset-meta {
      grid-column: 2;
    }
    .pfa-hou-list {
      padding: 12px 14px 16px;
    }
    .pfa-hou-card-main {
      grid-template-columns: 1fr;
      gap: 16px;
      padding: 14px;
    }
    .pfa-hou-flow {
      grid-template-columns: 1fr;
      gap: 10px;
    }
    .pfa-step {
      display: grid;
      grid-template-columns: 34px minmax(0, 1fr);
      column-gap: 10px;
      text-align: left;
      align-items: center;
    }
    .pfa-step-dot {
      margin-bottom: 0;
    }
    .pfa-connector {
      display: none;
    }
  }
</style>

<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px">
  <div>
    <h4 style="font-family:'Inter',sans-serif;font-weight:800;font-size:22px;color:var(--text);margin:0">Write Off Authorisation</h4>
    <p style="font-size:13px;color:var(--muted);margin:4px 0 0">Authorise IT and Non-IT assets for E-Waste and Disposal</p>
  </div>
</div>

<div style="display:flex;align-items:stretch;margin-bottom:28px;background:var(--surface);border:1px solid var(--border);border-radius:12px;overflow:hidden">
  @foreach([
    ['bi-pen-fill',                   '1', 'Fill Write-Off Form'],
    ['bi-file-earmark-arrow-up-fill', '2', 'Submit Digital Write-Off Document'],
    ['bi-hourglass-split',            '3', 'Wait for Approval'],
    ['bi-archive-fill',               '4', 'Moved to Write-Off Inventory'],
    ['bi-currency-dollar',            '5', 'Will Be Decided by Finance'],
  ] as $i => [$icon, $num, $label])
  <div style="flex:1;display:flex;align-items:center;padding:14px 16px;{{ $i < 4 ? 'border-right:1px solid var(--border)' : '' }}">
    <div style="width:32px;height:32px;border-radius:50%;background:var(--surface2);display:flex;align-items:center;justify-content:center;flex-shrink:0">
      <i class="bi {{ $icon }}" style="color:var(--muted);font-size:13px"></i>
    </div>
    <div style="margin-left:10px">
      <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--muted)">STEP {{ $num }}</div>
      <div style="font-size:12px;font-weight:600;color:var(--text)">{{ $label }}</div>
    </div>
  </div>
  @endforeach
</div>

{{-- ══════════════════════════════════════════════════════════════
     SECTION A: WRITE-OFF SUBMISSION FORM
     Shown when navigating from inventory with item params
══════════════════════════════════════════════════════════════ --}}
@if($woItem || $nitItem || $bulkItems->count() || $bulkNitItems->count())
@php
  $allItems = collect();
  if ($woItem)             $allItems->push((object)['label'=>'IT Asset','item'=>$woItem,'source'=>'IT']);
  if ($nitItem)            $allItems->push((object)['label'=>'Non-IT Asset','item'=>$nitItem,'source'=>'NIT']);
  foreach ($bulkItems    as $bi) $allItems->push((object)['label'=>'IT Asset','item'=>$bi,'source'=>'IT']);
  foreach ($bulkNitItems as $bn) $allItems->push((object)['label'=>'Non-IT Asset','item'=>$bn,'source'=>'NIT']);
  $woMode = ($bulkItems->count() || $bulkNitItems->count())
    ? ($bulkNitItems->count() && !$bulkItems->count() ? 'bulk_nit' : 'bulk')
    : ($nitItem && !$woItem ? 'nit' : 'single');
@endphp
<div class="table-card" style="margin-bottom:24px">

  {{-- Card header --}}
  <div style="display:flex;align-items:center;gap:10px;padding:20px 24px;border-bottom:1px solid var(--border)">
    <div style="width:36px;height:36px;background:rgba(2,132,199,.12);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
      <i class="bi bi-pen-fill" style="color:var(--accent);font-size:16px"></i>
    </div>
    <div>
      <div style="font-family:'Inter',sans-serif;font-weight:700;font-size:15px;color:var(--text)">Write-Off Authorisation Form</div>
      <div style="font-size:12px;color:var(--muted)">{{ $allItems->count() }} asset{{ $allItems->count() !== 1 ? 's' : '' }} selected &mdash; Complete and sign the digital write-off form, then submit for HOU review</div>
    </div>
    <a href="{{ route('it.writeoff.index') }}" class="btn-secondary-custom" style="margin-left:auto;font-size:12px;padding:5px 12px">
      <i class="bi bi-x"></i> Cancel
    </a>
  </div>

  {{-- Asset list --}}
  <div style="padding:14px 24px;border-bottom:1px solid var(--border);background:var(--body-bg)">
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px">
      <span style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em">Assets for Write-Off</span>
      <span style="background:rgba(2,132,199,.12);color:var(--accent);border-radius:20px;padding:2px 10px;font-size:11px;font-weight:700">{{ $allItems->count() }} item{{ $allItems->count() !== 1 ? 's' : '' }}</span>
    </div>
    <div style="max-height:260px;overflow-y:auto;border:1px solid var(--border);border-radius:10px;background:var(--surface)">
      <div style="display:grid;grid-template-columns:32px 2fr 110px 1fr 120px 60px;gap:0;padding:8px 14px;background:var(--surface2);border-bottom:1px solid var(--border);position:sticky;top:0;z-index:1">
        <span style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted)">#</span>
        <span style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted)">Description</span>
        <span style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted)">Asset No.</span>
        <span style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted)">Class</span>
        <span style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted)">Serial No.</span>
        <span style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);text-align:right">Type</span>
      </div>
      @foreach($allItems as $entry)
      <div style="display:grid;grid-template-columns:32px 2fr 110px 1fr 120px 60px;gap:0;padding:10px 14px;{{ !$loop->last ? 'border-bottom:1px solid var(--border);' : '' }}background:{{ $loop->even ? 'var(--surface2)' : 'var(--surface)' }}">
        <span style="font-size:12px;color:var(--muted);font-weight:600;line-height:1.6">{{ $loop->iteration }}</span>
        <span style="font-size:13px;font-weight:600;color:var(--text);padding-right:12px;word-break:break-word">{{ $entry->item->description ?: '—' }}</span>
        <span style="font-size:12px;color:var(--text);font-family:monospace;padding-right:10px">{{ $entry->item->asset_number ?: '—' }}</span>
        <span style="font-size:12px;color:var(--text);padding-right:10px">{{ $entry->item->asset_class ?: '—' }}</span>
        <span style="font-size:12px;color:var(--text);font-family:monospace;padding-right:10px">{{ $entry->item->serial_number ?: '—' }}</span>
        <span style="text-align:right">
          <span style="font-size:10px;font-weight:700;border-radius:20px;padding:2px 8px;{{ $entry->source === 'IT' ? 'background:rgba(2,132,199,.12);color:#0284c7' : 'background:rgba(217,119,6,.12);color:#d97706' }}">{{ $entry->source }}</span>
        </span>
      </div>
      @endforeach
    </div>
  </div>

  <div style="padding:20px 24px">
    <form id="woSubmitForm" method="POST" action="{{ route('it.writeoff.submit') }}" enctype="multipart/form-data">
      @csrf
      @if($woItem)        <input type="hidden" name="item_id"            value="{{ $woItem->id }}"> @endif
      @if($nitItem)       <input type="hidden" name="nit_item_id"        value="{{ $nitItem->id }}"> @endif
      @if($bulkIdsRaw)    <input type="hidden" name="bulk_item_ids"      value="{{ $bulkIdsRaw }}"> @endif
      @if($bulkNitIdsRaw) <input type="hidden" name="bulk_nit_item_ids"  value="{{ $bulkNitIdsRaw }}"> @endif

      {{-- Hidden fields populated by the modal on Confirm & Close --}}
      <input type="hidden" name="writeoff_name"        value="{{ $user->full_name }}">
      <input type="hidden" name="writeoff_designation" value="{{ $user->getItRoleLabel() }}">
      <input type="hidden" name="writeoff_date"        value="{{ now()->toDateString() }}">
      <input type="hidden" name="writeoff_sig_img"     id="woSigImgField"  value="">
      <input type="hidden" name="hou_user_id"          id="houHiddenField" value="">

      {{-- Open Write-Off Form button row --}}
      <div style="background:var(--body-bg);border:1.5px solid var(--border);border-radius:12px;padding:20px 22px;margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap">
        <div style="display:flex;align-items:center;gap:12px">
          <div style="width:40px;height:40px;background:rgba(20,43,71,.1);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="bi bi-file-earmark-text-fill" style="color:#142b47;font-size:18px"></i>
          </div>
          <div>
            <div style="font-weight:700;font-size:14px;color:var(--text)">Write-Off Form</div>
            <div style="font-size:12px;color:var(--muted);margin-top:2px">Fill in the write-off form — your details and selected asset are pre-loaded</div>
          </div>
        </div>
        <button type="button" onclick="openWOFormModal('{{ $woMode }}')"
          style="display:inline-flex;align-items:center;gap:8px;background:#142b47;color:#fff;border:none;border-radius:9px;padding:10px 22px;font-size:13px;font-weight:700;cursor:pointer;white-space:nowrap">
          <i class="bi bi-file-earmark-plus-fill"></i> Open Write-Off Form
        </button>
      </div>
      {{-- Success indicator (shown after modal confirm) --}}
      <div id="singleFormStatus" style="display:none;background:rgba(22,163,74,.08);border:1px solid rgba(22,163,74,.25);border-radius:9px;padding:12px 16px;margin-bottom:16px;align-items:center;gap:10px">
        <i class="bi bi-check-circle-fill" style="color:#16a34a;font-size:16px"></i>
        <span style="font-size:13px;font-weight:600;color:#16a34a">Write-Off Form completed and signed. Ready to submit.</span>
      </div>

      {{-- Error message --}}
      <div id="noFileMsg" style="display:none;background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.25);border-radius:8px;padding:10px 14px;margin-bottom:14px;font-size:13px;color:#dc2626;font-weight:600">
        <i class="bi bi-exclamation-circle-fill me-2"></i>Please open and complete the Write-Off Form before submitting.
      </div>

      <hr style="border:none;border-top:1px dashed var(--border);margin:16px 0">

      {{-- Submit / Cancel --}}
      <div style="display:flex;gap:8px">
        <button type="submit" id="submitBtn" onclick="return validateWOForm()" class="btn-primary-custom">
          <i class="bi bi-send-fill"></i> Submit for Approval
        </button>
        <a href="{{ route('it.writeoff.index') }}" class="btn-secondary-custom"><i class="bi bi-x"></i> Cancel</a>
      </div>
    </form>
  </div>
</div>
@endif

{{-- ══════════════════════════════════════════════════════════════
     SECTION B: HOU QUEUE
══════════════════════════════════════════════════════════════ --}}
@if($user->isHOU())
<div class="table-card" style="margin-bottom:24px">
  <div class="table-card-header">
    <div class="table-card-title">
      <i class="bi bi-pen me-2" style="color:var(--accent)"></i>My Write-Off Queue (HOU)
      @if($myHouCount) <span style="background:var(--accent);color:#fff;border-radius:20px;padding:2px 10px;font-size:11px;margin-left:8px">{{ $myHouCount }}</span> @endif
    </div>
  </div>
  <div style="overflow-x:auto">
    <table class="wo-hou-table">
      <thead>
        <tr>
          <th style="width:52%">Submission</th>
          <th style="width:22%">Submitted By</th>
          <th style="width:12%">Date</th>
          <th style="width:14%;text-align:right">Action</th>
        </tr>
      </thead>
      <tbody>
        @php
          $houGrouped = $myHouQueue->groupBy(fn($i) => $i->batch_id ?: ('single_'.$i->id));
        @endphp
        @forelse($houGrouped as $batchKey => $houGroup)
        @php
          $houFirst = $houGroup->first();
          $houIsBatch = $houGroup->count() > 1;
          $houItemsData = $houGroup->map(fn($i) => [
            'id'              => $i->id,
            'description'     => $i->description ?? '',
            'asset_number'    => $i->asset_number ?? '',
            'serial_number'   => $i->serial_number ?? '',
            'asset_class'     => $i->asset_class ?? '',
            'writeoff_name'   => $i->writeoff_name ?? '',
            'writeoff_date'   => $i->writeoff_date ? $i->writeoff_date->format('d/m/Y') : '',
            'writeoff_sig_img' => $i->writeoff_sig_img ?? '',
          ])->values()->toJson();
        @endphp
        <tr>
          <td class="wo-hou-submission">
            <div class="wo-hou-title-row">
              <div class="wo-hou-title" title="{{ $houIsBatch ? 'Bulk Write-Off' : $houFirst->description }}">
                {{ $houIsBatch ? 'Bulk Write-Off' : $houFirst->description }}
              </div>
              <span class="wo-hou-count">{{ $houGroup->count() }} {{ $houGroup->count() === 1 ? 'item' : 'items' }}</span>
              <button type="button"
                class="wo-hou-open-btn"
                data-items="{{ $houItemsData }}"
                data-title="{{ e($houIsBatch ? 'Bulk Write-Off' : $houFirst->description) }}"
                onclick="openHouAssetsModal(this)">
                <i class="bi bi-box-arrow-up-right"></i> Open
              </button>
            </div>
            <div class="wo-hou-asset-list">
              @foreach($houGroup->take(2) as $gi)
                <span class="wo-hou-asset" title="{{ $gi->description }}{{ $gi->asset_number ? ' - '.$gi->asset_number : '' }}">
                  <span class="wo-hou-asset-name">{{ $gi->description ?: 'Asset' }}</span>
                  @if($gi->asset_number)
                    <span class="wo-hou-asset-no">{{ $gi->asset_number }}</span>
                  @endif
                </span>
              @endforeach
              @if($houGroup->count() > 2)
                <span class="wo-hou-more">+{{ $houGroup->count() - 2 }} more</span>
              @endif
            </div>
          </td>
          <td>
            <div class="wo-hou-person" title="{{ $houFirst->creator?->full_name ?? '—' }}">{{ $houFirst->creator?->full_name ?? '—' }}</div>
          </td>
          <td class="wo-hou-date">{{ $houFirst->created_at?->format('d M Y') }}</td>
          <td class="wo-hou-action">
            <button type="button"
              data-items="{{ $houItemsData }}"
              onclick="openHouFormModal(this)"
              class="btn-primary-custom" style="padding:7px 12px;font-size:11px;white-space:nowrap">
              <i class="bi bi-file-earmark-text"></i> Sign Form
            </button>
          </td>
        </tr>
        @empty
        <tr><td colspan="4" style="text-align:center;padding:32px;color:var(--muted)">No pending write-offs for your review.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@if($houHistoryCount)
@php
  $houHistoryGrouped = $houHistory->groupBy(fn($i) => $i->batch_id ?: ('single_'.$i->id))->values();
  $houHistoryPerPage = 5;
  $houHistoryPage = max(1, (int) request('hou_history_page', 1));
  $houHistoryPages = max(1, (int) ceil($houHistoryGrouped->count() / $houHistoryPerPage));
  $houHistoryPage = min($houHistoryPage, $houHistoryPages);
  $houHistoryPaged = $houHistoryGrouped->forPage($houHistoryPage, $houHistoryPerPage);
@endphp
<div class="table-card" style="margin-bottom:24px">
  <div class="table-card-header">
    <div class="table-card-title" style="font-size:13px"><i class="bi bi-clock-history me-2"></i>My HOU History (Last {{ $houHistoryCount }})</div>
  </div>
  <div class="pfa-hou-list">
    @foreach($houHistoryPaged as $historyGroup)
    @php
      $historyFirst = $historyGroup->first();
      $historyCount = $historyGroup->count();
      $historyIsBatch = $historyCount > 1;
      $historyStatus = $historyGroup->contains(fn($i) => $i->hou_status === 'Rejected') ? 'Rejected' : 'Checked';
      $historyRemark = $historyGroup->pluck('hou_remark')->filter()->unique()->implode(' | ');
      $historyColId = 'houHistoryAssets_'.md5($historyFirst->batch_id ?: ('single_'.$historyFirst->id));
      $historyTitle = $historyIsBatch ? 'Bulk Write-Off' : ($historyFirst->description ?: 'Write-Off Submission');
    @endphp
    <div class="pfa-hou-card">
      <div class="pfa-hou-card-main">
        <div style="min-width:0">
          <div class="pfa-hou-title-row">
            <div class="pfa-hou-title" title="{{ $historyTitle }}">{{ $historyTitle }}</div>
            <span class="pfa-hou-pill" style="background:rgba(2,132,199,.10);color:var(--accent)">{{ $historyCount }} {{ $historyCount === 1 ? 'item' : 'items' }}</span>
            <span class="badge-status {{ $historyStatus === 'Checked' ? 'bs-active' : 'bs-disposed' }}">{{ $historyStatus }}</span>
          </div>
          <div class="pfa-hou-meta">
            <span><i class="bi bi-calendar-check"></i> {{ $historyFirst->hou_signed_at?->format('d M Y') ?? '—' }}</span>
            <span><i class="bi bi-person"></i> {{ $historyFirst->creator?->full_name ?? '—' }}</span>
          </div>
          <div class="pfa-hou-assets">
            <div class="pfa-hou-asset-summary">
              <div class="pfa-hou-asset-summary-text">
                {{ $historyCount === 1 ? ($historyFirst->description ?: 'Asset reviewed') : (($historyFirst->description ?: 'First asset') . ' +' . ($historyCount - 1) . ' more') }}
              </div>
              <button type="button" onclick="pfaToggle('{{ $historyColId }}')" class="wo-hou-open-btn" style="padding:6px 9px">
                <i id="{{ $historyColId }}_icon" class="bi bi-chevron-down" style="font-size:10px;transition:transform .2s"></i>
                <span id="{{ $historyColId }}_txt" data-closed-label="View {{ $historyCount }} {{ $historyCount === 1 ? 'asset' : 'assets' }}">View {{ $historyCount }} {{ $historyCount === 1 ? 'asset' : 'assets' }}</span>
              </button>
            </div>
            <div id="{{ $historyColId }}" class="pfa-hou-asset-dropdown">
              @foreach($historyGroup as $hi)
              <div class="pfa-hou-asset" title="{{ $hi->description }}{{ $hi->asset_number ? ' - '.$hi->asset_number : '' }}">
                <div class="pfa-hou-asset-icon"><i class="bi bi-pc-display-horizontal"></i></div>
                <div style="min-width:0">
                  <div class="pfa-hou-asset-title">{{ $hi->description ?: 'Asset reviewed' }}</div>
                  <div class="pfa-hou-asset-meta">
                    <span>{{ $hi->asset_class ?: 'Unclassified' }}</span>
                    <span>&bull;</span>
                    <code>{{ $hi->asset_number ?: 'No asset no.' }}</code>
                    @if($hi->serial_number)
                      <span>&bull;</span>
                      <code>{{ $hi->serial_number }}</code>
                    @endif
                  </div>
                </div>
              </div>
              @endforeach
            </div>
          </div>
          <div class="pfa-hou-actions">
            <a href="{{ route('it.writeoff.report', $historyFirst->id) }}" target="_blank" class="wo-hou-open-btn" style="text-decoration:none;padding:5px 8px">
              <i class="bi bi-file-earmark-text"></i> Report
            </a>
          </div>
        </div>
        <div style="border:1px solid var(--border);border-radius:9px;background:var(--body-bg);padding:12px 14px">
          <div style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">HOU Review</div>
          <div style="font-size:13px;font-weight:800;color:var(--text)">{{ $historyFirst->hou_signed_name ?: ($historyFirst->houUser?->full_name ?? '—') }}</div>
          <div style="font-size:11px;color:var(--muted);margin-top:3px">{{ $historyFirst->hou_signed_at?->format('d M Y H:i') ?? '—' }}</div>
          @if($historyRemark)
            <div style="font-size:12px;color:var(--muted);margin-top:10px;line-height:1.45">{{ $historyRemark }}</div>
          @endif
        </div>
      </div>
    </div>
    @endforeach
  </div>
  @if($houHistoryPages > 1)
  <div class="wo-page-controls">
    <div style="font-size:12px;color:var(--muted)">Page {{ $houHistoryPage }} of {{ $houHistoryPages }} · {{ $houHistoryGrouped->count() }} submissions</div>
    <div class="wo-page-links">
      @for($i = 1; $i <= $houHistoryPages; $i++)
        <a class="wo-page-link {{ $i === $houHistoryPage ? 'is-active' : '' }}" href="{{ request()->fullUrlWithQuery(['hou_history_page' => $i]) }}">{{ $i }}</a>
      @endfor
    </div>
  </div>
  @endif
</div>
@endif
@endif

{{-- HOU Asset List Modal --}}
<div id="houAssetsModal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.55);z-index:9100;align-items:center;justify-content:center;padding:20px">
  <div style="width:100%;max-width:720px;background:var(--surface);border:1px solid var(--border);border-radius:12px;box-shadow:0 24px 70px rgba(0,0,0,.28);overflow:hidden">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding:16px 18px;border-bottom:1px solid var(--border);background:var(--surface)">
      <div style="min-width:0">
        <div id="houAssetsModalTitle" style="font-size:15px;font-weight:800;color:var(--text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">Submission Assets</div>
        <div id="houAssetsModalMeta" style="font-size:12px;color:var(--muted);margin-top:3px"></div>
      </div>
      <button type="button" onclick="closeHouAssetsModal()" style="width:32px;height:32px;border:1px solid var(--border);border-radius:8px;background:var(--surface);color:var(--muted);font-size:20px;line-height:1;cursor:pointer">&times;</button>
    </div>
    <div style="padding:16px 18px;max-height:60vh;overflow-y:auto">
      <div id="houAssetsModalList" style="display:grid;gap:8px"></div>
    </div>
    <div style="display:flex;justify-content:flex-end;padding:12px 18px;border-top:1px solid var(--border);background:var(--body-bg)">
      <button type="button" onclick="closeHouAssetsModal()" class="btn-secondary-custom" style="font-size:12px;padding:7px 14px">Close</button>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     SECTION C: GM QUEUE
══════════════════════════════════════════════════════════════ --}}
@if($user->isGM())
<div class="table-card" style="margin-bottom:24px">
  <div class="table-card-header">
    <div class="table-card-title">
      <i class="bi bi-pen me-2" style="color:var(--accent)"></i>My Write-Off Queue (GM)
      @if($myGmCount) <span style="background:var(--accent);color:#fff;border-radius:20px;padding:2px 10px;font-size:11px;margin-left:8px">{{ $myGmCount }}</span> @endif
    </div>
  </div>
  <div style="overflow-x:auto">
    <table class="table data-table" style="width:100%">
      <thead><tr>
        <th>#</th><th>Write-Off Submission</th><th>Items</th><th>HOU Checked By</th><th>Submitted By</th><th>Action</th>
      </tr></thead>
      <tbody>
        @php
          $gmGrouped = $myGmQueue->groupBy(fn($i) => $i->batch_id ?: ('single_'.$i->id));
          $gmRowNum = 0;
        @endphp
        @forelse($gmGrouped as $batchKey => $gmGroup)
        @php
          $gmRowNum++;
          $gmFirst = $gmGroup->first();
          $gmIsBatch = $gmGroup->count() > 1;
          $gmItemsData = $gmGroup->map(fn($i) => [
            'id'               => $i->id,
            'description'      => $i->description ?? '',
            'asset_number'     => $i->asset_number ?? '',
            'serial_number'    => $i->serial_number ?? '',
            'asset_class'      => $i->asset_class ?? '',
            'writeoff_name'    => $i->writeoff_name ?? '',
            'writeoff_date'    => $i->writeoff_date ? $i->writeoff_date->format('d/m/Y') : '',
            'writeoff_sig_img' => $i->writeoff_sig_img ?? '',
            'hou_signed_name'  => $i->hou_signed_name ?? '',
            'hou_signed_at'    => $i->hou_signed_at ? $i->hou_signed_at->format('d/m/Y') : '',
            'hou_sig_img'      => $i->hou_sig_img ?? '',
          ])->values()->toJson();
        @endphp
        <tr>
          <td>{{ $gmRowNum }}</td>
          <td>
            @if($gmIsBatch)
              <div style="font-weight:600;color:var(--text)">Bulk Write-Off</div>
              <div style="font-size:11px;color:var(--muted);margin-bottom:6px">{{ $gmGroup->count() }} assets submitted together</div>
              <div style="background:var(--body-bg);border:1px solid var(--border);border-radius:6px;padding:6px 10px;max-height:110px;overflow-y:auto">
                @foreach($gmGroup as $gi)
                <div style="font-size:11px;padding:2px 0;{{ !$loop->last ? 'border-bottom:1px solid var(--border);' : '' }}color:var(--text)">
                  {{ $gi->description }} <span style="font-size:10px;color:var(--muted);font-family:monospace">{{ $gi->asset_number ?: '' }}</span>
                </div>
                @endforeach
              </div>
            @else
              <div style="font-weight:500">{{ $gmFirst->description }}</div>
              <div style="font-size:11px;color:var(--muted)">{{ $gmFirst->asset_class }}</div>
            @endif
          </td>
          <td><span style="background:rgba(2,132,199,.1);color:var(--accent);border-radius:20px;padding:2px 10px;font-size:12px;font-weight:700">{{ $gmGroup->count() }}</span></td>
          <td>
            <div style="font-size:13px">{{ $gmFirst->hou_signed_name ?: ($gmFirst->houUser?->full_name ?? '—') }}</div>
            @if($gmFirst->hou_signed_at)
            <div style="font-size:11px;color:var(--muted)">{{ $gmFirst->hou_signed_at->format('d M Y') }}</div>
            @endif
          </td>
          <td>{{ $gmFirst->creator?->full_name ?? '—' }}</td>
          <td>
            <button type="button"
              data-items="{{ $gmItemsData }}"
              onclick="openGmFormModal(this)"
              class="btn-primary-custom" style="padding:5px 14px;font-size:11px;white-space:nowrap">
              <i class="bi bi-file-earmark-text"></i> Sign Form
            </button>
          </td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center;padding:32px;color:var(--muted)">No pending write-offs for GM review.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@if($gmHistoryCount)
@php
  $gmHistoryGrouped = $gmHistory->groupBy(fn($i) => $i->batch_id ?: ('single_'.$i->id))->values();
  $gmHistoryPerPage = 5;
  $gmHistoryPage = max(1, (int) request('gm_history_page', 1));
  $gmHistoryPages = max(1, (int) ceil($gmHistoryGrouped->count() / $gmHistoryPerPage));
  $gmHistoryPage = min($gmHistoryPage, $gmHistoryPages);
  $gmHistoryPaged = $gmHistoryGrouped->forPage($gmHistoryPage, $gmHistoryPerPage);
@endphp
<div class="table-card" style="margin-bottom:24px">
  <div class="table-card-header">
    <div class="table-card-title" style="font-size:13px"><i class="bi bi-clock-history me-2"></i>My GM History (Last {{ $gmHistoryCount }})</div>
  </div>
  <div class="pfa-hou-list">
    @foreach($gmHistoryPaged as $historyGroup)
    @php
      $historyFirst = $historyGroup->first();
      $historyCount = $historyGroup->count();
      $historyIsBatch = $historyCount > 1;
      $historyStatus = $historyGroup->contains(fn($i) => $i->gm_status === 'Rejected') ? 'Rejected' : 'Checked';
      $historyRemark = $historyGroup->pluck('gm_remark')->filter()->unique()->implode(' | ');
      $historyColId = 'gmHistoryAssets_'.md5($historyFirst->batch_id ?: ('single_'.$historyFirst->id));
      $historyTitle = $historyIsBatch ? 'Bulk Write-Off' : ($historyFirst->description ?: 'Write-Off Submission');
    @endphp
    <div class="pfa-hou-card">
      <div class="pfa-hou-card-main">
        <div style="min-width:0">
          <div class="pfa-hou-title-row">
            <div class="pfa-hou-title" title="{{ $historyTitle }}">{{ $historyTitle }}</div>
            <span class="pfa-hou-pill" style="background:rgba(2,132,199,.10);color:var(--accent)">{{ $historyCount }} {{ $historyCount === 1 ? 'item' : 'items' }}</span>
            <span class="badge-status {{ $historyStatus === 'Checked' ? 'bs-active' : 'bs-disposed' }}">{{ $historyStatus }}</span>
          </div>
          <div class="pfa-hou-meta">
            <span><i class="bi bi-calendar-check"></i> {{ $historyFirst->gm_signed_at?->format('d M Y') ?? '—' }}</span>
            <span><i class="bi bi-person"></i> {{ $historyFirst->creator?->full_name ?? '—' }}</span>
          </div>
          <div class="pfa-hou-assets">
            <div class="pfa-hou-asset-summary">
              <div class="pfa-hou-asset-summary-text">
                {{ $historyCount === 1 ? ($historyFirst->description ?: 'Asset reviewed') : (($historyFirst->description ?: 'First asset') . ' +' . ($historyCount - 1) . ' more') }}
              </div>
              <button type="button" onclick="pfaToggle('{{ $historyColId }}')" class="wo-hou-open-btn" style="padding:6px 9px">
                <i id="{{ $historyColId }}_icon" class="bi bi-chevron-down" style="font-size:10px;transition:transform .2s"></i>
                <span id="{{ $historyColId }}_txt" data-closed-label="View {{ $historyCount }} {{ $historyCount === 1 ? 'asset' : 'assets' }}">View {{ $historyCount }} {{ $historyCount === 1 ? 'asset' : 'assets' }}</span>
              </button>
            </div>
            <div id="{{ $historyColId }}" class="pfa-hou-asset-dropdown">
              @foreach($historyGroup as $hi)
              <div class="pfa-hou-asset" title="{{ $hi->description }}{{ $hi->asset_number ? ' - '.$hi->asset_number : '' }}">
                <div class="pfa-hou-asset-icon"><i class="bi bi-pc-display-horizontal"></i></div>
                <div style="min-width:0">
                  <div class="pfa-hou-asset-title">{{ $hi->description ?: 'Asset reviewed' }}</div>
                  <div class="pfa-hou-asset-meta">
                    <span>{{ $hi->asset_class ?: 'Unclassified' }}</span>
                    <span>&bull;</span>
                    <code>{{ $hi->asset_number ?: 'No asset no.' }}</code>
                    @if($hi->serial_number)
                      <span>&bull;</span>
                      <code>{{ $hi->serial_number }}</code>
                    @endif
                  </div>
                </div>
              </div>
              @endforeach
            </div>
          </div>
          <div class="pfa-hou-actions">
            <a href="{{ route('it.writeoff.report', $historyFirst->id) }}" target="_blank" class="wo-hou-open-btn" style="text-decoration:none;padding:5px 8px">
              <i class="bi bi-file-earmark-text"></i> Report
            </a>
          </div>
        </div>
        <div style="border:1px solid var(--border);border-radius:9px;background:var(--body-bg);padding:12px 14px">
          <div style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">GM Review</div>
          <div style="font-size:13px;font-weight:800;color:var(--text)">{{ $historyFirst->gm_signed_name ?: ($historyFirst->currentGmUser?->full_name ?? '—') }}</div>
          <div style="font-size:11px;color:var(--muted);margin-top:3px">{{ $historyFirst->gm_signed_at?->format('d M Y H:i') ?? '—' }}</div>
          @if($historyRemark)
            <div style="font-size:12px;color:var(--muted);margin-top:10px;line-height:1.45">{{ $historyRemark }}</div>
          @endif
        </div>
      </div>
    </div>
    @endforeach
  </div>
  @if($gmHistoryPages > 1)
  <div class="wo-page-controls">
    <div style="font-size:12px;color:var(--muted)">Page {{ $gmHistoryPage }} of {{ $gmHistoryPages }} · {{ $gmHistoryGrouped->count() }} submissions</div>
    <div class="wo-page-links">
      @for($i = 1; $i <= $gmHistoryPages; $i++)
        <a class="wo-page-link {{ $i === $gmHistoryPage ? 'is-active' : '' }}" href="{{ request()->fullUrlWithQuery(['gm_history_page' => $i]) }}">{{ $i }}</a>
      @endfor
    </div>
  </div>
  @endif
</div>
@endif
@endif

{{-- ══════════════════════════════════════════════════════════════
     SECTION D: CEO QUEUE
══════════════════════════════════════════════════════════════ --}}
@if($user->isCEO())
<div class="table-card" style="margin-bottom:24px">
  <div class="table-card-header">
    <div class="table-card-title">
      <i class="bi bi-shield-check me-2" style="color:var(--accent)"></i>CEO Approval Queue
      @if($myCeoCount) <span style="background:var(--accent);color:#fff;border-radius:20px;padding:2px 10px;font-size:11px;margin-left:8px">{{ $myCeoCount }}</span> @endif
    </div>
  </div>
  @php
    $ceoGrouped = $myCeoQueue->groupBy(fn($i) => $i->batch_id ?: ('single_'.$i->id))->values();
    $ceoQueuePerPage = 5;
    $ceoQueuePage = max(1, (int) request('ceo_queue_page', 1));
    $ceoQueuePages = max(1, (int) ceil($ceoGrouped->count() / $ceoQueuePerPage));
    $ceoQueuePage = min($ceoQueuePage, $ceoQueuePages);
    $ceoGroupedPaged = $ceoGrouped->forPage($ceoQueuePage, $ceoQueuePerPage);
  @endphp
  @if($ceoGrouped->isEmpty())
  <div style="padding:48px 20px;text-align:center;color:var(--muted)">No pending write-offs awaiting CEO approval.</div>
  @else
  <div class="pfa-hou-list">
    @foreach($ceoGroupedPaged as $ceoGroup)
    @php
      $ceoFirst = $ceoGroup->first();
      $ceoCount = $ceoGroup->count();
      $ceoIsBatch = $ceoCount > 1;
      $ceoColId = 'ceoQueueAssets_'.md5($ceoFirst->batch_id ?: ('single_'.$ceoFirst->id));
      $ceoTitle = $ceoIsBatch ? 'Bulk Write-Off' : ($ceoFirst->description ?: 'Write-Off Submission');
      $ceoRemark = $ceoGroup->pluck('gm_remark')->filter()->unique()->implode(' | ');
      $ceoItemsData = $ceoGroup->map(fn($i) => [
        'id'               => $i->id,
        'description'      => $i->description ?? '',
        'asset_number'     => $i->asset_number ?? '',
        'serial_number'    => $i->serial_number ?? '',
        'asset_class'      => $i->asset_class ?? '',
        'writeoff_name'    => $i->writeoff_name ?? '',
        'writeoff_date'    => $i->writeoff_date ? $i->writeoff_date->format('d/m/Y') : '',
        'writeoff_sig_img' => $i->writeoff_sig_img ?? '',
        'hou_signed_name'  => $i->hou_signed_name ?? '',
        'hou_signed_at'    => $i->hou_signed_at ? $i->hou_signed_at->format('d/m/Y') : '',
        'hou_sig_img'      => $i->hou_sig_img ?? '',
        'gm_signed_name'   => $i->gm_signed_name ?? '',
        'gm_signed_at'     => $i->gm_signed_at ? $i->gm_signed_at->format('d/m/Y') : '',
        'gm_sig_img'       => $i->gm_sig_img ?? '',
      ])->values()->toJson();
    @endphp
    <div class="pfa-hou-card">
      <div class="pfa-hou-card-main">
        <div style="min-width:0">
          <div class="pfa-hou-title-row">
            <div class="pfa-hou-title" title="{{ $ceoTitle }}">{{ $ceoTitle }}</div>
            <span class="pfa-hou-pill" style="background:rgba(2,132,199,.10);color:var(--accent)">{{ $ceoCount }} {{ $ceoCount === 1 ? 'item' : 'items' }}</span>
            <span class="badge-status bs-pending">Pending CEO</span>
          </div>
          <div class="pfa-hou-meta">
            <span><i class="bi bi-calendar-check"></i> {{ $ceoFirst->gm_signed_at?->format('d M Y') ?? '—' }}</span>
            <span><i class="bi bi-person"></i> {{ $ceoFirst->creator?->full_name ?? '—' }}</span>
          </div>
          <div class="pfa-hou-assets">
            <div class="pfa-hou-asset-summary">
              <div class="pfa-hou-asset-summary-text">
                {{ $ceoCount === 1 ? ($ceoFirst->description ?: 'Asset awaiting CEO approval') : (($ceoFirst->description ?: 'First asset') . ' +' . ($ceoCount - 1) . ' more') }}
              </div>
              <button type="button" onclick="pfaToggle('{{ $ceoColId }}')" class="wo-hou-open-btn" style="padding:6px 9px">
                <i id="{{ $ceoColId }}_icon" class="bi bi-chevron-down" style="font-size:10px;transition:transform .2s"></i>
                <span id="{{ $ceoColId }}_txt" data-closed-label="View {{ $ceoCount }} {{ $ceoCount === 1 ? 'asset' : 'assets' }}">View {{ $ceoCount }} {{ $ceoCount === 1 ? 'asset' : 'assets' }}</span>
              </button>
            </div>
            <div id="{{ $ceoColId }}" class="pfa-hou-asset-dropdown">
              @foreach($ceoGroup as $ci)
              <div class="pfa-hou-asset" title="{{ $ci->description }}{{ $ci->asset_number ? ' - '.$ci->asset_number : '' }}">
                <div class="pfa-hou-asset-icon"><i class="bi bi-pc-display-horizontal"></i></div>
                <div style="min-width:0">
                  <div class="pfa-hou-asset-title">{{ $ci->description ?: 'Asset awaiting CEO approval' }}</div>
                  <div class="pfa-hou-asset-meta">
                    <span>{{ $ci->asset_class ?: 'Unclassified' }}</span>
                    <span>&bull;</span>
                    <code>{{ $ci->asset_number ?: 'No asset no.' }}</code>
                    @if($ci->serial_number)
                      <span>&bull;</span>
                      <code>{{ $ci->serial_number }}</code>
                    @endif
                  </div>
                </div>
              </div>
              @endforeach
            </div>
          </div>
          <div class="pfa-hou-actions">
            <a href="{{ route('it.writeoff.report', $ceoFirst->id) }}" target="_blank" class="wo-hou-open-btn" style="text-decoration:none;padding:5px 8px">
              <i class="bi bi-file-earmark-text"></i> Report
            </a>
            <button type="button"
              data-items="{{ $ceoItemsData }}"
              onclick="openCeoFormModal(this)"
              class="btn-primary-custom" style="padding:5px 10px;font-size:11px;white-space:nowrap">
              <i class="bi bi-shield-check"></i> Review Form
            </button>
          </div>
        </div>
        <div style="border:1px solid var(--border);border-radius:9px;background:var(--body-bg);padding:12px 14px">
          <div style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">GM Review</div>
          <div style="font-size:13px;font-weight:800;color:var(--text)">{{ $ceoFirst->gm_signed_name ?: ($ceoFirst->currentGmUser?->full_name ?? '—') }}</div>
          <div style="font-size:11px;color:var(--muted);margin-top:3px">{{ $ceoFirst->gm_signed_at?->format('d M Y H:i') ?? '—' }}</div>
          @if($ceoRemark)
            <div style="font-size:12px;color:var(--muted);margin-top:10px;line-height:1.45">{{ $ceoRemark }}</div>
          @endif
        </div>
      </div>
    </div>
    @endforeach
  </div>
  @if($ceoQueuePages > 1)
  <div class="wo-page-controls">
    <div style="font-size:12px;color:var(--muted)">Page {{ $ceoQueuePage }} of {{ $ceoQueuePages }} · {{ $ceoGrouped->count() }} submissions</div>
    <div class="wo-page-links">
      @for($i = 1; $i <= $ceoQueuePages; $i++)
        <a class="wo-page-link {{ $i === $ceoQueuePage ? 'is-active' : '' }}" href="{{ request()->fullUrlWithQuery(['ceo_queue_page' => $i]) }}">{{ $i }}</a>
      @endfor
    </div>
  </div>
  @endif
  @endif
</div>

@if($ceoHistoryCount)
@php
  $ceoHistoryGrouped = $ceoHistory->groupBy(fn($i) => $i->batch_id ?: ('single_'.$i->id))->values();
  $ceoHistoryPerPage = 5;
  $ceoHistoryPage = max(1, (int) request('ceo_history_page', 1));
  $ceoHistoryPages = max(1, (int) ceil($ceoHistoryGrouped->count() / $ceoHistoryPerPage));
  $ceoHistoryPage = min($ceoHistoryPage, $ceoHistoryPages);
  $ceoHistoryPaged = $ceoHistoryGrouped->forPage($ceoHistoryPage, $ceoHistoryPerPage);
@endphp
<div class="table-card" style="margin-bottom:24px">
  <div class="table-card-header">
    <div class="table-card-title" style="font-size:13px"><i class="bi bi-clock-history me-2"></i>CEO History (Last {{ $ceoHistoryCount }})</div>
  </div>
  <div class="pfa-hou-list">
    @foreach($ceoHistoryPaged as $historyGroup)
    @php
      $historyFirst = $historyGroup->first();
      $historyCount = $historyGroup->count();
      $historyIsBatch = $historyCount > 1;
      $historyStatus = $historyGroup->contains(fn($i) => $i->ceo_status === 'Rejected') ? 'Rejected' : 'Approved';
      $historyRemark = $historyGroup->pluck('ceo_remark')->filter()->unique()->implode(' | ');
      $historyColId = 'ceoHistoryAssets_'.md5($historyFirst->batch_id ?: ('single_'.$historyFirst->id));
      $historyTitle = $historyIsBatch ? 'Bulk Write-Off' : ($historyFirst->description ?: 'Write-Off Submission');
    @endphp
    <div class="pfa-hou-card">
      <div class="pfa-hou-card-main">
        <div style="min-width:0">
          <div class="pfa-hou-title-row">
            <div class="pfa-hou-title" title="{{ $historyTitle }}">{{ $historyTitle }}</div>
            <span class="pfa-hou-pill" style="background:rgba(2,132,199,.10);color:var(--accent)">{{ $historyCount }} {{ $historyCount === 1 ? 'item' : 'items' }}</span>
            <span class="badge-status {{ $historyStatus === 'Approved' ? 'bs-active' : 'bs-disposed' }}">{{ $historyStatus }}</span>
          </div>
          <div class="pfa-hou-meta">
            <span><i class="bi bi-calendar-check"></i> {{ $historyFirst->ceo_signed_at?->format('d M Y') ?? '—' }}</span>
            <span><i class="bi bi-person"></i> {{ $historyFirst->creator?->full_name ?? '—' }}</span>
          </div>
          <div class="pfa-hou-assets">
            <div class="pfa-hou-asset-summary">
              <div class="pfa-hou-asset-summary-text">
                {{ $historyCount === 1 ? ($historyFirst->description ?: 'Asset reviewed') : (($historyFirst->description ?: 'First asset') . ' +' . ($historyCount - 1) . ' more') }}
              </div>
              <button type="button" onclick="pfaToggle('{{ $historyColId }}')" class="wo-hou-open-btn" style="padding:6px 9px">
                <i id="{{ $historyColId }}_icon" class="bi bi-chevron-down" style="font-size:10px;transition:transform .2s"></i>
                <span id="{{ $historyColId }}_txt" data-closed-label="View {{ $historyCount }} {{ $historyCount === 1 ? 'asset' : 'assets' }}">View {{ $historyCount }} {{ $historyCount === 1 ? 'asset' : 'assets' }}</span>
              </button>
            </div>
            <div id="{{ $historyColId }}" class="pfa-hou-asset-dropdown">
              @foreach($historyGroup as $hi)
              <div class="pfa-hou-asset" title="{{ $hi->description }}{{ $hi->asset_number ? ' - '.$hi->asset_number : '' }}">
                <div class="pfa-hou-asset-icon"><i class="bi bi-pc-display-horizontal"></i></div>
                <div style="min-width:0">
                  <div class="pfa-hou-asset-title">{{ $hi->description ?: 'Asset reviewed' }}</div>
                  <div class="pfa-hou-asset-meta">
                    <span>{{ $hi->asset_class ?: 'Unclassified' }}</span>
                    <span>&bull;</span>
                    <code>{{ $hi->asset_number ?: 'No asset no.' }}</code>
                    @if($hi->serial_number)
                      <span>&bull;</span>
                      <code>{{ $hi->serial_number }}</code>
                    @endif
                  </div>
                </div>
              </div>
              @endforeach
            </div>
          </div>
          <div class="pfa-hou-actions">
            <a href="{{ route('it.writeoff.report', $historyFirst->id) }}" target="_blank" class="wo-hou-open-btn" style="text-decoration:none;padding:5px 8px">
              <i class="bi bi-file-earmark-text"></i> Report
            </a>
          </div>
        </div>
        <div style="border:1px solid var(--border);border-radius:9px;background:var(--body-bg);padding:12px 14px">
          <div style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">CEO Review</div>
          <div style="font-size:13px;font-weight:800;color:var(--text)">{{ $historyFirst->ceo_signed_name ?: 'CEO' }}</div>
          <div style="font-size:11px;color:var(--muted);margin-top:3px">{{ $historyFirst->ceo_signed_at?->format('d M Y H:i') ?? '—' }}</div>
          @if($historyRemark)
            <div style="font-size:12px;color:var(--muted);margin-top:10px;line-height:1.45">{{ $historyRemark }}</div>
          @endif
        </div>
      </div>
    </div>
    @endforeach
  </div>
  @if($ceoHistoryPages > 1)
  <div class="wo-page-controls">
    <div style="font-size:12px;color:var(--muted)">Page {{ $ceoHistoryPage }} of {{ $ceoHistoryPages }} · {{ $ceoHistoryGrouped->count() }} submissions</div>
    <div class="wo-page-links">
      @for($i = 1; $i <= $ceoHistoryPages; $i++)
        <a class="wo-page-link {{ $i === $ceoHistoryPage ? 'is-active' : '' }}" href="{{ request()->fullUrlWithQuery(['ceo_history_page' => $i]) }}">{{ $i }}</a>
      @endfor
    </div>
  </div>
  @endif
</div>
@endif
@endif


{{-- ══════════════════════════════════════════════════════════════
     SECTION G: PENDING FINAL APPROVAL  (moved above Section F)
══════════════════════════════════════════════════════════════ --}}
@if(!$user->isAdmin())
<div class="table-card" style="margin-bottom:24px">
  <div style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
    <div>
      <span style="font-family:'Inter',sans-serif;font-weight:700;font-size:14px;color:var(--text)">Pending Final Approval</span>
      <div style="font-size:12px;color:var(--muted);margin-top:2px">
        {{ $user->isHOU() ? 'Write-offs you checked — tracking GM and CEO approval' : 'Items signed by GM — awaiting CEO review and decision' }}
      </div>
    </div>
    @if($woCount > 0)
    <span style="background:rgba(245,158,11,.12);color:#d97706;border-radius:20px;padding:3px 12px;font-size:12px;font-weight:700">{{ $woCount }} pending</span>
    @endif
  </div>

  @if($woCount === 0)
  <div style="padding:48px 20px;text-align:center">
    <i class="bi bi-check-circle" style="font-size:32px;display:block;margin-bottom:10px;color:#16a34a"></i>
    <div style="font-size:14px;font-weight:600;color:var(--text)">No pending approvals</div>
    <div style="font-size:12px;color:var(--muted);margin-top:4px">All write-off requests have been processed</div>
  </div>
  @else
  @php
    $groupedQueue = $woQueue->groupBy(fn($i) => $i->batch_id ?: 'solo_'.$i->id)->values();
    $pfaPerPage = 5;
    $pfaPage = max(1, (int) request('pfa_page', 1));
    $pfaPages = max(1, (int) ceil($groupedQueue->count() / $pfaPerPage));
    $pfaPage = min($pfaPage, $pfaPages);
    $groupedQueuePaged = $groupedQueue->forPage($pfaPage, $pfaPerPage);
  @endphp
  @if($user->isHOU())
  <div class="pfa-hou-list">
    @foreach($groupedQueuePaged as $qItems)
    @php
      $qFirst = $qItems->first();
      $qCount = $qItems->count();
      $qIsBatch = $qCount > 1;
      $qColId = 'pfaHouBatch_'.md5($qFirst->batch_id ?: ('solo_'.$qFirst->id));
      $qTitle = $qIsBatch ? 'Bulk Write-Off' : ($qFirst->description ?: 'Write-Off Submission');
      $qClasses = $qItems->pluck('asset_class')->filter()->unique()->values();
      $gmDone = $qFirst->gm_status === 'Checked';
      $gmPending = $qFirst->gm_status === 'Pending';
      $ceoDone = $qFirst->ceo_status === 'Approved';
      $ceoRejected = $qFirst->ceo_status === 'Rejected';
      $ceoPending = $qFirst->ceo_status === 'Pending';
      $gmLabel = $gmDone ? ($qFirst->gm_signed_name ?: 'GM signed') : ($gmPending ? 'Awaiting '.($qFirst->currentGmUser?->full_name ?? 'GM') : 'Waiting');
      $ceoLabel = $ceoRejected ? 'Rejected' : ($ceoDone ? 'Approved' : ($ceoPending ? 'Awaiting CEO' : 'After GM'));
    @endphp
    <div class="pfa-hou-card">
      <div class="pfa-hou-card-main">
        <div style="min-width:0">
          <div class="pfa-hou-title-row">
            <div class="pfa-hou-title" title="{{ $qTitle }}">{{ $qTitle }}</div>
            <span class="pfa-hou-pill" style="background:rgba(2,132,199,.10);color:var(--accent)">{{ $qCount }} {{ $qCount === 1 ? 'item' : 'items' }}</span>
          </div>
          <div class="pfa-hou-meta">
            <span><i class="bi bi-calendar3"></i> {{ $qFirst->created_at?->format('d M Y') }}</span>
            @if($qFirst->writeoff_name)
              <span><i class="bi bi-person"></i> {{ $qFirst->writeoff_name }}</span>
            @endif
            @foreach($qClasses as $cls)
              <span class="badge-status bs-repair" style="font-size:10px">{{ $cls }}</span>
            @endforeach
          </div>
          <div class="pfa-hou-assets">
            <div class="pfa-hou-asset-summary">
              <div class="pfa-hou-asset-summary-text">
                {{ $qCount === 1 ? ($qFirst->description ?: 'Asset awaiting approval') : (($qFirst->description ?: 'First asset') . ' +' . ($qCount - 1) . ' more') }}
              </div>
              <button type="button" onclick="pfaToggle('{{ $qColId }}')" class="wo-hou-open-btn" style="padding:6px 9px">
                <i id="{{ $qColId }}_icon" class="bi bi-chevron-down" style="font-size:10px;transition:transform .2s"></i>
                <span id="{{ $qColId }}_txt" data-closed-label="View {{ $qCount }} {{ $qCount === 1 ? 'asset' : 'assets' }}">View {{ $qCount }} {{ $qCount === 1 ? 'asset' : 'assets' }}</span>
              </button>
            </div>
            <div id="{{ $qColId }}" class="pfa-hou-asset-dropdown">
            @foreach($qItems as $qi)
              <div class="pfa-hou-asset" title="{{ $qi->description }}{{ $qi->asset_number ? ' - '.$qi->asset_number : '' }}">
                <div class="pfa-hou-asset-icon"><i class="bi bi-pc-display-horizontal"></i></div>
                <div style="min-width:0">
                  <div class="pfa-hou-asset-title">{{ $qi->description ?: 'Asset awaiting approval' }}</div>
                  <div class="pfa-hou-asset-meta">
                    <span>{{ $qi->asset_class ?: 'Unclassified' }}</span>
                    <span>&bull;</span>
                    <code>{{ $qi->asset_number ?: 'No asset no.' }}</code>
                    @if($qi->serial_number)
                      <span>&bull;</span>
                      <code>{{ $qi->serial_number }}</code>
                    @endif
                  </div>
                </div>
              </div>
            @endforeach
            </div>
          </div>
          <div class="pfa-hou-actions">
            <a href="{{ route('it.writeoff.report', $qFirst->id) }}" target="_blank" class="wo-hou-open-btn" style="text-decoration:none;padding:5px 8px">
              <i class="bi bi-file-earmark-text"></i> Report
            </a>
          </div>
        </div>
        <div class="pfa-hou-flow" aria-label="Write-off approval progress">
          <div class="pfa-step pfa-step-done">
            <div class="pfa-step-dot"><i class="bi bi-check-lg"></i></div>
            <div>
              <div class="pfa-step-label">HOU</div>
              <div class="pfa-step-sub">{{ $qFirst->hou_signed_name ?: ($qFirst->houUser?->full_name ?? 'Approved') }}</div>
            </div>
          </div>
          <div class="pfa-connector pfa-connector-done"></div>
          <div class="pfa-step {{ $gmDone ? 'pfa-step-done' : 'pfa-step-active' }}">
            <div class="pfa-step-dot"><i class="bi {{ $gmDone ? 'bi-check-lg' : 'bi-hourglass-split' }}"></i></div>
            <div>
              <div class="pfa-step-label">GM</div>
              <div class="pfa-step-sub">{{ $gmLabel }}</div>
            </div>
          </div>
          <div class="pfa-connector {{ $gmDone ? 'pfa-connector-done' : '' }}"></div>
          <div class="pfa-step {{ $ceoDone ? 'pfa-step-done' : ($ceoPending || $ceoRejected ? 'pfa-step-active' : 'pfa-step-wait') }}">
            <div class="pfa-step-dot"><i class="bi {{ $ceoDone ? 'bi-check-lg' : ($ceoRejected ? 'bi-x-lg' : ($ceoPending ? 'bi-hourglass-split' : 'bi-circle')) }}"></i></div>
            <div>
              <div class="pfa-step-label">CEO</div>
              <div class="pfa-step-sub">{{ $ceoLabel }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endforeach
  </div>
  @if($pfaPages > 1)
  <div class="wo-page-controls">
    <div style="font-size:12px;color:var(--muted)">Page {{ $pfaPage }} of {{ $pfaPages }} · {{ $groupedQueue->count() }} submissions</div>
    <div class="wo-page-links">
      @for($i = 1; $i <= $pfaPages; $i++)
        <a class="wo-page-link {{ $i === $pfaPage ? 'is-active' : '' }}" href="{{ request()->fullUrlWithQuery(['pfa_page' => $i]) }}">{{ $i }}</a>
      @endfor
    </div>
  </div>
  @endif
  @else
  <div style="overflow-x:auto">
    <table class="table" style="width:100%">
      <thead><tr>
        <th>Asset No.</th><th>Class</th><th>Description</th><th>Serial No.</th>
        <th>Date Submitted</th><th>Authorised By</th><th>Checked By (HOU)</th><th>Signed By (GM)</th><th>CEO Status</th>
        @if($user->isAdmin())<th>Actions</th>@endif
      </tr></thead>
      <tbody>
        @foreach($groupedQueue as $qItems)
        @php
          $qFirst   = $qItems->first();
          $qIsBatch = $qItems->count() > 1;
          $qCount   = $qItems->count();
          $qColId   = 'pfaBatch_'.md5($qFirst->batch_id ?: ('solo_'.$qFirst->id));
        @endphp
        {{-- Main row --}}
        <tr>
          {{-- Asset No. --}}
          <td>
            @if($qIsBatch)
              <span style="background:rgba(124,58,237,.1);color:#7c3aed;border-radius:6px;padding:2px 8px;font-size:11px;font-weight:700;white-space:nowrap">{{ $qCount }} assets</span>
            @else
              <code>{{ $qFirst->asset_number ?: '—' }}</code>
            @endif
          </td>
          {{-- Class --}}
          <td>
            @if($qIsBatch)
              @foreach($qItems->pluck('asset_class')->filter()->unique() as $cls)
              <span class="badge-status bs-repair" style="font-size:11px;display:inline-block;margin-bottom:2px">{{ $cls }}</span>
              @endforeach
            @else
              <span class="badge-status bs-repair" style="font-size:11px">{{ $qFirst->asset_class ?: '—' }}</span>
            @endif
          </td>
          {{-- Description --}}
          <td>
            @if($qIsBatch)
              <div style="font-weight:600;color:var(--text);margin-bottom:4px">Bulk Write-Off — {{ $qCount }} Assets</div>
              <button onclick="pfaToggle('{{ $qColId }}')" style="display:inline-flex;align-items:center;gap:4px;background:none;border:none;cursor:pointer;font-size:11px;color:var(--muted);padding:0;font-weight:600">
                <i id="{{ $qColId }}_icon" class="bi bi-chevron-down" style="font-size:10px;transition:transform .2s"></i>
                <span id="{{ $qColId }}_txt">Show {{ $qCount }} items</span>
              </button>
              <div id="{{ $qColId }}" style="display:none;margin-top:8px">
                <ol style="margin:0;padding-left:16px;font-size:12px;color:var(--text)">
                  @foreach($qItems as $qi)
                  <li style="margin-bottom:3px">
                    <span style="font-weight:500">{{ $qi->description }}</span>
                    @if($qi->asset_number)<code style="font-size:11px;margin-left:4px">{{ $qi->asset_number }}</code>@endif
                  </li>
                  @endforeach
                </ol>
              </div>
            @else
              <div style="font-weight:500">{{ $qFirst->description }}</div>
            @endif
          </td>
          {{-- Serial No. --}}
          <td style="font-size:12px;color:var(--muted)">
            @if($qIsBatch)—
            @else{{ $qFirst->serial_number ?: '—' }}
            @endif
          </td>
          {{-- Date --}}
          <td style="font-size:12px;color:var(--muted)">{{ $qFirst->created_at?->format('d/m/Y') }}</td>
          {{-- Authorised By --}}
          <td style="font-size:12px">
            @if($qFirst->writeoff_name)
              <div style="font-weight:600">{{ $qFirst->writeoff_name }}</div>
              @if($qFirst->writeoff_designation)<div style="font-size:11px;color:var(--muted)">{{ $qFirst->writeoff_designation }}</div>@endif
            @else<span style="color:var(--muted)">—</span>
            @endif
          </td>
          {{-- HOU --}}
          <td style="font-size:12px">
            @if($qFirst->hou_status === 'Checked' && $qFirst->houUser)
              <div style="display:inline-flex;align-items:center;gap:5px;background:rgba(124,58,237,.08);color:#7c3aed;border:1px solid rgba(124,58,237,.2);border-radius:6px;padding:3px 10px;font-size:11px;font-weight:700">
                <i class="bi bi-check-circle-fill" style="font-size:10px"></i> {{ $qFirst->houUser->full_name }}
              </div>
            @else<span style="color:var(--muted)">—</span>
            @endif
          </td>
          {{-- GM --}}
          <td style="font-size:12px">
            @if($qFirst->gm_status === 'Checked' && $qFirst->gm_signed_name)
              <div style="display:inline-flex;align-items:center;gap:5px;background:rgba(13,148,136,.08);color:#0d9488;border:1px solid rgba(13,148,136,.2);border-radius:6px;padding:3px 10px;font-size:11px;font-weight:700">
                <i class="bi bi-check-circle-fill" style="font-size:10px"></i> {{ $qFirst->gm_signed_name }}
              </div>
            @elseif($qFirst->gm_status === 'Pending')
              <div style="display:inline-flex;align-items:center;gap:5px;background:rgba(245,158,11,.1);color:#d97706;border:1px solid rgba(245,158,11,.24);border-radius:6px;padding:3px 10px;font-size:11px;font-weight:700">
                <i class="bi bi-hourglass-split" style="font-size:10px"></i> Awaiting {{ $qFirst->currentGmUser?->full_name ?? 'GM' }}
              </div>
            @else<span style="color:var(--muted)">—</span>
            @endif
          </td>
          {{-- CEO Status --}}
          <td>
            @if($qFirst->ceo_status === 'Approved')
              <span class="badge-status bs-active">CEO Approved</span>
            @elseif($qFirst->ceo_status === 'Rejected')
              <span class="badge-status bs-disposed">CEO Rejected</span>
            @elseif($qFirst->ceo_status === 'Pending')
              <span class="badge-status bs-pending">Awaiting CEO</span>
            @elseif($qFirst->gm_status === 'Pending')
              <span style="color:var(--muted);font-size:12px">Waiting for GM</span>
            @else
              <span style="color:var(--muted);font-size:12px">—</span>
            @endif
          </td>
          {{-- Admin Actions --}}
          @if($user->isAdmin())
          <td>
            <div style="display:flex;gap:4px;flex-wrap:wrap">
              @foreach($qItems as $qi)
              <a href="{{ route('it.writeoff.report', $qi->id) }}" target="_blank" class="btn-icon btn-view" title="Report {{ $qi->asset_number ?: $qi->description }}">
                <i class="bi bi-file-earmark-text"></i>
              </a>
              @endforeach
              <form method="POST" action="{{ route('it.writeoff.reject', $qFirst->id) }}" style="display:inline"
                    onsubmit="return confirm('Reject and delete this write-off? The item will be restored.')">
                @csrf
                <button type="submit" class="btn-icon btn-delete" title="Reject & Delete"><i class="bi bi-x-circle"></i></button>
              </form>
            </div>
          </td>
          @endif
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif
  @endif
</div>
@endif

<script>
function pfaToggle(id) {
  var el   = document.getElementById(id);
  var icon = document.getElementById(id + '_icon');
  var txt  = document.getElementById(id + '_txt');
  if (!el) return;
  var open = window.getComputedStyle(el).display !== 'none';
  el.style.display     = open ? 'none' : 'block';
  if (icon) icon.style.transform = open ? 'rotate(0deg)' : 'rotate(180deg)';
  if (txt) txt.textContent = open ? (txt.dataset.closedLabel || 'Show items') : 'Hide items';
}
</script>

{{-- ══════════════════════════════════════════════════════════════
     SECTION F: MY WRITE-OFF SUBMISSIONS (moved below Section G)
══════════════════════════════════════════════════════════════ --}}
@if(!$user->isHOU() && !$user->isGM() && !$user->isCEO())
<div style="background:var(--surface);border:1px solid var(--border);border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 14px rgba(0,0,0,.07);margin-bottom:24px">

  {{-- Section header --}}
  <div style="padding:16px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:12px">
    <div style="display:flex;align-items:center;gap:10px">
      <div style="width:3px;height:22px;background:#7c3aed;border-radius:2px;flex-shrink:0"></div>
      <i class="bi bi-pen-fill" style="color:#7c3aed;font-size:15px"></i>
      <span style="font-size:14px;font-weight:700;color:var(--text)">My Write-Off Submissions</span>
    </div>
    <div style="display:flex;align-items:center;gap:10px">
      @if($myWoCount > 0)
      <span style="background:rgba(124,58,237,.12);color:#7c3aed;border-radius:20px;padding:4px 14px;font-size:12px;font-weight:700">{{ $myWoCount }} submitted</span>
      <form method="POST" action="{{ route('it.writeoff.dismiss-all') }}" style="display:inline"
            onsubmit="return confirm('Clear all submissions from this view? They will no longer appear here.')">
        @csrf
        <button type="submit" style="font-size:12px;font-weight:600;color:#dc2626;background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.25);border-radius:7px;padding:6px 14px;cursor:pointer;display:inline-flex;align-items:center;gap:5px;font-family:'Inter',sans-serif">
          <i class="bi bi-trash"></i> Clear All
        </button>
      </form>
      @endif
    </div>
  </div>

  {{-- Submission cards --}}
  <div style="padding:20px 24px;display:flex;flex-direction:column;gap:16px">
  @php $groupedWoItems = $myWoItems->groupBy(fn($i) => $i->batch_id ?: 'solo_'.$i->id); @endphp
  @forelse($groupedWoItems as $groupKey => $groupItems)
  @php
    $first      = $groupItems->first();
    $isBatch    = str_starts_with($groupKey, 'BATCH_');
    $batchCount = $groupItems->count();
    $categories = $groupItems->pluck('asset_class')->filter()->unique()->values();
    $batchLabel = $isBatch
      ? 'Bulk Write-Off — '.$batchCount.' '.($batchCount === 1 ? 'Asset' : 'Assets')
      : 'Write-Off — '.\Illuminate\Support\Str::limit($first->description, 40);

    // ── Step done / rejected booleans ──────────────────────────
    $sDone = [
      in_array($first->hou_status,      ['Checked']),
      in_array($first->gm_status,       ['Checked']),
      in_array($first->ceo_status,      ['Approved']),
      in_array($first->finance_status,  ['EWaste','Disposal']),
    ];
    $sRej = [
      $first->hou_status      === 'Rejected',
      $first->gm_status       === 'Rejected',
      $first->ceo_status      === 'Rejected',
      false,
    ];

    // ── Find the active (current pending) step ─────────────────
    $anyRejected   = in_array(true, $sRej);
    $activeStepIdx = 4; // default: all done
    foreach ($sDone as $si => $done) {
      if ($sRej[$si]) { $activeStepIdx = $si; break; }
      if (!$done)     { $activeStepIdx = $si; break; }
    }

    // ── Overall badge ──────────────────────────────────────────
    $overallStatus = $anyRejected ? 'Rejected' : ($sDone[3] ? 'Completed' : ($sDone[2] ? 'CEO Approved' : 'Pending'));
    $statusBg    = match(true) {
      $anyRejected  => 'rgba(220,38,38,.1)',
      $sDone[3]     => 'rgba(22,163,74,.1)',
      $sDone[2]     => 'rgba(22,163,74,.1)',
      default       => 'rgba(245,158,11,.1)',
    };
    $statusColor = match(true) {
      $anyRejected  => '#dc2626',
      $sDone[3]     => '#16a34a',
      $sDone[2]     => '#16a34a',
      default       => '#f59e0b',
    };

    $collapseId   = 'batch_'.md5($groupKey);
    $stepLabels   = ['HOU', 'GM', 'CEO', 'Finance'];
    $stepSubLabel = [];
    $financeLabel = $first->finance_status === 'EWaste' ? 'E-Waste'
                  : ($first->finance_status === 'Disposal' ? 'Disposal' : null);
    foreach ($stepLabels as $si => $lbl) {
      if ($sRej[$si])                             $stepSubLabel[$si] = 'Rejected';
      elseif ($sDone[$si])                        $stepSubLabel[$si] = ($si === 3 ? ($financeLabel ?? 'Routed') : 'Approved');
      elseif ($si === $activeStepIdx && !$anyRejected) $stepSubLabel[$si] = 'Pending';
      else                                        $stepSubLabel[$si] = 'Waiting';
    }
  @endphp

  <div style="background:var(--surface);border:1px solid var(--border);border-radius:12px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.04)">

    {{-- ── Card header ── --}}
    <div style="padding:16px 20px;display:flex;align-items:center;gap:16px">
      <div style="width:44px;height:44px;border-radius:10px;background:rgba(124,58,237,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0">
        <i class="bi bi-collection-fill" style="color:#7c3aed;font-size:19px"></i>
      </div>
      <div style="flex:1;min-width:0">
        <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:5px">{{ $batchLabel }}</div>
        <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap">
          <span style="background:rgba(124,58,237,.1);color:#7c3aed;border-radius:20px;padding:2px 10px;font-size:11px;font-weight:700">{{ $batchCount }} {{ $batchCount === 1 ? 'item' : 'items' }}</span>
          @foreach($categories as $cat)
          <span style="font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.05em">{{ $cat }}</span>
          @endforeach
        </div>
      </div>
      <div style="text-align:right;flex-shrink:0;display:flex;flex-direction:column;align-items:flex-end;gap:8px">
        <div style="font-size:12px;color:var(--muted);font-weight:500;line-height:1.6">
          {{ $first->created_at?->format('d M Y') }}<br>{{ $first->created_at?->format('H:i') }}
        </div>
        <span style="background:{{ $statusBg }};color:{{ $statusColor }};border-radius:20px;padding:3px 12px;font-size:11px;font-weight:700;display:inline-flex;align-items:center;gap:5px;white-space:nowrap">
          <span style="width:6px;height:6px;border-radius:50%;background:{{ $statusColor }};flex-shrink:0;display:inline-block"></span>
          {{ $overallStatus }}
        </span>
        <form method="POST" action="{{ route('it.writeoff.dismiss-batch') }}" style="display:inline">
          @csrf
          @if($isBatch)
            <input type="hidden" name="batch_id" value="{{ $groupKey }}">
          @else
            <input type="hidden" name="item_id" value="{{ $first->id }}">
          @endif
          <button type="submit" title="Dismiss from view"
            style="font-size:11px;font-weight:600;color:var(--muted);background:transparent;border:1px solid var(--border);border-radius:6px;padding:3px 10px;cursor:pointer;font-family:'Inter',sans-serif;display:inline-flex;align-items:center;gap:4px">
            <i class="bi bi-x"></i> Dismiss
          </button>
        </form>
      </div>
    </div>

    {{-- ── Approval Workflow Stepper (always visible) ── --}}
    @php
      // Pre-compute all 4 node styles before rendering to avoid scope issues
      // Actual DB values: hou/gm/ceo_status start as 'Pending' (not null) once submitted
      $houSt = $first->hou_status;      // null | 'Pending' | 'Checked' | 'Rejected'
      $gmSt  = $first->gm_status;       // null | 'Pending' | 'Checked' | 'Rejected'
      $ceoSt = $first->ceo_status;      // null | 'Pending' | 'Approved' | 'Rejected'
      $finSt = $first->finance_status;  // null | 'Pending' | 'EWaste'   | 'Disposal'

      // Step is "done" when it has a positive completion value
      $sd = [
        in_array($houSt, ['Checked']),
        in_array($gmSt,  ['Checked']),
        in_array($ceoSt, ['Approved']),
        in_array($finSt, ['EWaste', 'Disposal']),
      ];
      // Step is "rejected"
      $sr = [$houSt === 'Rejected', $gmSt === 'Rejected', $ceoSt === 'Rejected', false];
      // Step is "active pending" — has been handed to this approver but not yet actioned
      $sp = [
        in_array($houSt, ['Pending']),
        in_array($gmSt,  ['Pending']),
        in_array($ceoSt, ['Pending']),
        in_array($finSt, ['Pending']),
      ];

      $stepNodes = [];
      foreach ([['HOU',0],['GM',1],['CEO',2],['Finance',3]] as [$nodeLabel, $ni]) {
        if ($sr[$ni]) {
          $stepNodes[] = ['label'=>$nodeLabel,'sublabel'=>'Rejected', 'bg'=>'#fef2f2','border'=>'#dc2626','icon'=>'bi-x-lg','iconColor'=>'#dc2626','labelColor'=>'#dc2626','subColor'=>'#dc2626','subBg'=>'rgba(220,38,38,.1)','lineDone'=>false];
        } elseif ($sd[$ni]) {
          $sl = $ni === 3 ? ($finSt === 'EWaste' ? 'E-Waste' : 'Disposal') : 'Approved';
          $stepNodes[] = ['label'=>$nodeLabel,'sublabel'=>$sl, 'bg'=>'#7c3aed','border'=>'#7c3aed','icon'=>'bi-check-lg','iconColor'=>'#fff','labelColor'=>'#7c3aed','subColor'=>'#16a34a','subBg'=>'rgba(22,163,74,.1)','lineDone'=>true];
        } elseif ($sp[$ni]) {
          $stepNodes[] = ['label'=>$nodeLabel,'sublabel'=>'Pending', 'bg'=>'#fffbeb','border'=>'#f59e0b','icon'=>'bi-clock-fill','iconColor'=>'#f59e0b','labelColor'=>'#92400e','subColor'=>'#f59e0b','subBg'=>'rgba(245,158,11,.1)','lineDone'=>false];
        } else {
          $stepNodes[] = ['label'=>$nodeLabel,'sublabel'=>'Waiting', 'bg'=>'var(--surface)','border'=>'var(--border)','icon'=>null,'iconColor'=>null,'labelColor'=>'var(--muted)','subColor'=>'var(--muted)','subBg'=>'transparent','lineDone'=>false];
        }
      }
    @endphp
    <div style="padding:16px 24px 20px;border-top:1px solid var(--border);background:var(--body-bg)">
      <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:14px">Approval Workflow</div>
      <div style="display:flex;align-items:flex-start">
        @foreach($stepNodes as $nIdx => $node)
        {{-- Step node --}}
        <div style="display:flex;flex-direction:column;align-items:center;gap:6px;flex-shrink:0;min-width:64px">
          <div style="width:40px;height:40px;border-radius:50%;background:{{ $node['bg'] }};border:2px solid {{ $node['border'] }};display:flex;align-items:center;justify-content:center">
            @if($node['icon'])
              <i class="bi {{ $node['icon'] }}" style="color:{{ $node['iconColor'] }};font-size:14px"></i>
            @else
              <span style="width:8px;height:8px;border-radius:50%;background:var(--border);display:block"></span>
            @endif
          </div>
          <div style="font-size:11px;font-weight:700;color:{{ $node['labelColor'] }};text-transform:uppercase;letter-spacing:.05em;text-align:center">{{ $node['label'] }}</div>
          <span style="background:{{ $node['subBg'] }};color:{{ $node['subColor'] }};border-radius:20px;padding:2px 8px;font-size:10px;font-weight:700;white-space:nowrap">{{ $node['sublabel'] }}</span>
        </div>
        {{-- Connector line --}}
        @if($nIdx < 3)
        <div style="flex:1;height:2px;background:{{ $node['lineDone'] ? '#7c3aed' : 'var(--border)' }};margin:0 4px;margin-top:19px;min-width:16px;border-radius:2px"></div>
        @endif
        @endforeach
      </div>
    </div>

    {{-- ── Collapse toggle for inline form ── --}}
    <div onclick="woToggleBatch('{{ $collapseId }}')"
      style="padding:10px 20px;border-top:1px solid var(--border);cursor:pointer;display:flex;align-items:center;justify-content:space-between;user-select:none;background:var(--surface)">
      <div style="display:flex;align-items:center;gap:6px;font-size:12px;font-weight:600;color:var(--muted)">
        <i id="{{ $collapseId }}_icon" class="bi bi-chevron-down" style="font-size:11px;transition:transform .2s"></i>
        View Submitted Form
      </div>
      <a href="{{ route('it.writeoff.report', $first->id) }}" target="_blank"
        onclick="event.stopPropagation()"
        style="display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:600;color:#0284c7;text-decoration:none;background:rgba(2,132,199,.08);border:1px solid rgba(2,132,199,.2);border-radius:6px;padding:4px 11px">
        <i class="bi bi-box-arrow-up-right"></i> View Full Report
      </a>
    </div>

    {{-- ── Expandable: inline read-only write-off form ── --}}
    <div id="{{ $collapseId }}" style="display:none;background:var(--body-bg);border-top:1px solid var(--border)">
      <div style="margin:16px 20px;background:#fff;border:1px solid #d1d5db;border-radius:10px;overflow:hidden;font-family:Arial,sans-serif;font-size:11px;color:#1a1a1a">

        {{-- Mini Letterhead --}}
        <div style="padding:14px 20px 10px;border-bottom:2px solid #1a1a1a;text-align:center">
          <div style="font-weight:800;font-size:13px;text-transform:uppercase;letter-spacing:.5px">FGV Johor Bulkers Sdn Bhd</div>
          <div style="font-size:10px;color:#555;margin-top:2px">(197401003452 / 20547-U)</div>
          <div style="font-weight:700;font-size:12px;margin-top:8px;letter-spacing:.06em;text-transform:uppercase">Asset Write-Off Authorization Form</div>
          <div style="font-size:10px;color:#666;margin-top:2px">Borang Kelulusan Penghapusan Aset</div>
        </div>

        {{-- Meta Strip --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:5px 24px;padding:10px 20px;border-bottom:1px solid #ccc">
          <div style="display:flex;align-items:baseline;gap:6px;font-size:10.5px">
            <span style="font-weight:700;color:#333;white-space:nowrap;min-width:110px">Submitted By</span>
            <span style="color:#1a1a1a;border-bottom:1px solid #999;flex:1;padding-bottom:1px">{{ $first->writeoff_name ?: '—' }}</span>
          </div>
          <div style="display:flex;align-items:baseline;gap:6px;font-size:10.5px">
            <span style="font-weight:700;color:#333;white-space:nowrap;min-width:110px">Date Submitted</span>
            <span style="color:#1a1a1a;border-bottom:1px solid #999;flex:1;padding-bottom:1px">{{ $first->writeoff_date ? $first->writeoff_date->format('d M Y') : ($first->created_at?->format('d M Y') ?? '—') }}</span>
          </div>
          <div style="display:flex;align-items:baseline;gap:6px;font-size:10.5px">
            <span style="font-weight:700;color:#333;white-space:nowrap;min-width:110px">No. of Items</span>
            <span style="color:#1a1a1a;border-bottom:1px solid #999;flex:1;padding-bottom:1px">{{ $batchCount }} item{{ $batchCount !== 1 ? 's' : '' }}</span>
          </div>
          <div style="display:flex;align-items:baseline;gap:6px;font-size:10.5px">
            <span style="font-weight:700;color:#333;white-space:nowrap;min-width:110px">Reference / Batch</span>
            <span style="color:#1a1a1a;border-bottom:1px solid #999;flex:1;padding-bottom:1px">{{ $first->batch_id ?: 'Single Submission' }}</span>
          </div>
          <div style="display:flex;align-items:baseline;gap:6px;font-size:10.5px">
            <span style="font-weight:700;color:#333;white-space:nowrap;min-width:110px">Designation</span>
            <span style="color:#1a1a1a;border-bottom:1px solid #999;flex:1;padding-bottom:1px">{{ $first->writeoff_designation ?: '—' }}</span>
          </div>
          <div style="display:flex;align-items:baseline;gap:6px;font-size:10.5px">
            <span style="font-weight:700;color:#333;white-space:nowrap;min-width:110px">Status</span>
            <span style="color:{{ $statusColor }};font-weight:700;border-bottom:1px solid #999;flex:1;padding-bottom:1px">{{ $overallStatus }}</span>
          </div>
        </div>

        {{-- Asset Details --}}
        <div style="background:#1a1a1a;color:#fff;padding:5px 20px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px">Asset Details / Butiran Aset</div>
        <div style="padding:0 20px 14px;overflow-x:auto">
          <table style="width:100%;border-collapse:collapse;font-size:10.5px;margin-top:10px">
            <thead>
              <tr>
                <th style="background:#f4f4f4;border:1px solid #999;padding:6px 8px;text-align:left;font-weight:700;text-transform:uppercase;letter-spacing:.4px;font-size:9.5px;width:28px">#</th>
                <th style="background:#f4f4f4;border:1px solid #999;padding:6px 8px;text-align:left;font-weight:700;text-transform:uppercase;letter-spacing:.4px;font-size:9.5px;width:90px">Asset No.</th>
                <th style="background:#f4f4f4;border:1px solid #999;padding:6px 8px;text-align:left;font-weight:700;text-transform:uppercase;letter-spacing:.4px;font-size:9.5px;width:80px">Class</th>
                <th style="background:#f4f4f4;border:1px solid #999;padding:6px 8px;text-align:left;font-weight:700;text-transform:uppercase;letter-spacing:.4px;font-size:9.5px">Description</th>
                <th style="background:#f4f4f4;border:1px solid #999;padding:6px 8px;text-align:left;font-weight:700;text-transform:uppercase;letter-spacing:.4px;font-size:9.5px;width:100px">Serial No.</th>
                <th style="background:#f4f4f4;border:1px solid #999;padding:6px 8px;text-align:left;font-weight:700;text-transform:uppercase;letter-spacing:.4px;font-size:9.5px;width:100px">Brand / Model</th>
                <th style="background:#f4f4f4;border:1px solid #999;padding:6px 8px;text-align:left;font-weight:700;text-transform:uppercase;letter-spacing:.4px;font-size:9.5px;width:44px">Source</th>
              </tr>
            </thead>
            <tbody>
              @foreach($groupItems as $rowIdx => $bi)
              <tr style="{{ $rowIdx % 2 === 1 ? 'background:#fafafa' : '' }}">
                <td style="border:1px solid #999;padding:6px 8px;text-align:center">{{ $rowIdx + 1 }}</td>
                <td style="border:1px solid #999;padding:6px 8px"><code style="font-size:9.5px">{{ $bi->asset_number ?: '—' }}</code></td>
                <td style="border:1px solid #999;padding:6px 8px">{{ $bi->asset_class }}</td>
                <td style="border:1px solid #999;padding:6px 8px;font-weight:600">{{ $bi->description }}</td>
                <td style="border:1px solid #999;padding:6px 8px"><code style="font-size:9.5px;color:#555">{{ $bi->serial_number ?: '—' }}</code></td>
                <td style="border:1px solid #999;padding:6px 8px">{{ trim(($bi->brand ?? '') . ' ' . ($bi->model ?? '')) ?: '—' }}</td>
                <td style="border:1px solid #999;padding:6px 8px;text-align:center;font-weight:700;font-size:9.5px">{{ $bi->asset_source ?? 'IT' }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        {{-- Authorization & Signatures --}}
        <div style="background:#1a1a1a;color:#fff;padding:5px 20px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px">Authorization &amp; Signatures / Kelulusan &amp; Tandatangan</div>
        <div style="padding:0 20px 16px">
          <div class="stats-grid" style="border:1px solid #999;margin-top:12px">

            {{-- Proposed By --}}
            <div style="padding:10px;border-right:1px solid #999;display:flex;flex-direction:column">
              <div style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#333;margin-bottom:3px">Proposed By</div>
              <div style="font-size:9px;color:#666;margin-bottom:8px">Write-Off Initiator</div>
              <div style="flex:1;min-height:64px;display:flex;align-items:flex-end;border-bottom:1px solid #333;margin-bottom:6px;padding-bottom:4px">
                @if($first->writeoff_sig_img)
                  <img src="{{ $first->writeoff_sig_img }}" alt="Signature" style="max-height:60px;max-width:100%">
                @else
                  <span style="font-size:9px;color:#bbb;font-style:italic">No signature captured</span>
                @endif
              </div>
              <div style="font-size:11px;font-weight:700;color:#1a1a1a;margin-bottom:2px">{{ $first->writeoff_name ?: '—' }}</div>
              <div style="font-size:10px;color:#444;margin-bottom:2px">{{ $first->writeoff_designation ?: '—' }}</div>
              <div style="font-size:10px;color:#777">{{ $first->writeoff_date ? $first->writeoff_date->format('d M Y') : '—' }}</div>
            </div>

            {{-- Checked By (HOU) --}}
            <div style="padding:10px;border-right:1px solid #999;display:flex;flex-direction:column">
              <div style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#333;margin-bottom:3px">Checked By</div>
              <div style="font-size:9px;color:#666;margin-bottom:8px">Head of Unit (HOU)</div>
              <div style="flex:1;min-height:64px;display:flex;align-items:flex-end;border-bottom:1px solid #333;margin-bottom:6px;padding-bottom:4px">
                @if($first->hou_sig_img)
                  <img src="{{ $first->hou_sig_img }}" alt="HOU Signature" style="max-height:60px;max-width:100%">
                @elseif($first->hou_status === 'Pending')
                  <span style="font-size:9px;color:#bbb;font-style:italic">Awaiting HOU</span>
                @else
                  <span style="font-size:9px;color:#bbb;font-style:italic">Not yet reached</span>
                @endif
              </div>
              <div style="font-size:11px;font-weight:700;color:#1a1a1a;margin-bottom:2px">{{ $first->hou_signed_name ?: ($first->houUser?->full_name ?? '—') }}</div>
              <div style="font-size:10px;color:#444;margin-bottom:2px">Head of Unit</div>
              <div style="font-size:10px;color:#777">
                @if($first->hou_signed_at) {{ $first->hou_signed_at->format('d M Y') }}
                @elseif($first->hou_status) {{ $first->hou_status }}
                @else —
                @endif
              </div>
            </div>

            {{-- Recommended By (GM) --}}
            <div style="padding:10px;border-right:1px solid #999;display:flex;flex-direction:column">
              <div style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#333;margin-bottom:3px">Recommended By</div>
              <div style="font-size:9px;color:#666;margin-bottom:8px">General Manager (GM)</div>
              <div style="flex:1;min-height:64px;display:flex;align-items:flex-end;border-bottom:1px solid #333;margin-bottom:6px;padding-bottom:4px">
                @if($first->gm_sig_img)
                  <img src="{{ $first->gm_sig_img }}" alt="GM Signature" style="max-height:60px;max-width:100%">
                @elseif($first->gm_status === 'Pending')
                  <span style="font-size:9px;color:#bbb;font-style:italic">Awaiting GM</span>
                @else
                  <span style="font-size:9px;color:#bbb;font-style:italic">Not yet reached</span>
                @endif
              </div>
              <div style="font-size:11px;font-weight:700;color:#1a1a1a;margin-bottom:2px">{{ $first->gm_signed_name ?: ($first->currentGmUser?->full_name ?? '—') }}</div>
              <div style="font-size:10px;color:#444;margin-bottom:2px">General Manager</div>
              <div style="font-size:10px;color:#777">
                @if($first->gm_signed_at) {{ $first->gm_signed_at->format('d M Y') }}
                @elseif($first->gm_status) {{ $first->gm_status }}
                @else —
                @endif
              </div>
            </div>

            {{-- Approved By (CEO) --}}
            <div style="padding:10px;display:flex;flex-direction:column">
              <div style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#333;margin-bottom:3px">Approved By</div>
              <div style="font-size:9px;color:#666;margin-bottom:8px">Chief Executive Officer</div>
              <div style="flex:1;min-height:64px;display:flex;align-items:flex-end;border-bottom:1px solid #333;margin-bottom:6px;padding-bottom:4px">
                @if($first->ceo_sig_img)
                  <img src="{{ $first->ceo_sig_img }}" alt="CEO Signature" style="max-height:60px;max-width:100%">
                @elseif($first->ceo_status === 'Pending')
                  <span style="font-size:9px;color:#bbb;font-style:italic">Awaiting CEO</span>
                @else
                  <span style="font-size:9px;color:#bbb;font-style:italic">Not yet reached</span>
                @endif
              </div>
              <div style="font-size:11px;font-weight:700;color:#1a1a1a;margin-bottom:2px">{{ $first->ceo_signed_name ?: ($first->ceoUser?->full_name ?? '—') }}</div>
              <div style="font-size:10px;color:#444;margin-bottom:2px">Chief Executive Officer</div>
              <div style="font-size:10px;color:#777">
                @if($first->ceo_signed_at) {{ $first->ceo_signed_at->format('d M Y') }}
                @elseif($first->ceo_status) {{ $first->ceo_status }}
                @else —
                @endif
              </div>
            </div>

          </div>
        </div>

        {{-- Mini Footer --}}
        <div style="padding:6px 20px;border-top:1px solid #ccc;display:flex;align-items:center;justify-content:space-between;font-size:9px;color:#888;background:#f9f9f9">
          <span>FJB Inventory Management System &bull; Pengurusan Aset</span>
          <a href="{{ route('it.writeoff.report', $first->id) }}" target="_blank"
            style="display:inline-flex;align-items:center;gap:4px;font-size:10px;font-weight:600;color:#0284c7;text-decoration:none">
            <i class="bi bi-printer"></i> Print / Full Report
          </a>
        </div>

      </div>
    </div>

  </div>
  @empty
  <div style="text-align:center;padding:48px;color:var(--muted);font-size:13px">You have not submitted any write-off items yet.</div>
  @endforelse
  </div>

</div>

<script>
function woToggleBatch(id) {
  var el   = document.getElementById(id);
  var icon = document.getElementById(id + '_icon');
  var open = el.style.display !== 'none';
  el.style.display     = open ? 'none' : 'block';
  icon.style.transform = open ? 'rotate(0deg)' : 'rotate(180deg)';
}
</script>
@endif


{{-- ══════════════════════════════════════════════════════════════
     MODALS
══════════════════════════════════════════════════════════════ --}}

{{-- APPLICATION FOR ASSET DISPOSAL Modal --}}
<div id="woFormModal" class="wo-staff-form-modal">
  <div class="wo-staff-form-card">

    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid #ddd;flex:0 0 auto">
      <span style="font-weight:700;font-size:14px;color:#142b47">
        <i class="bi bi-file-earmark-text-fill" style="color:#38bdf8;margin-right:6px"></i>Write-Off Form
      </span>
      <button onclick="closeWOFormModal()" style="background:none;border:none;font-size:22px;cursor:pointer;color:#9ca3af;line-height:1">&times;</button>
    </div>

    <div class="wo-staff-form-body">
    {{-- Print Area --}}
    <div id="woPrintArea" style="padding:28px 32px">
      {{-- Company Header --}}
      <div style="text-align:center;margin-bottom:18px">
        <div style="font-weight:800;font-size:15px;text-decoration:underline;letter-spacing:.03em">FGV JOHOR BULKERS SDN BHD</div>
        <div style="font-size:12px;color:#444;margin-top:3px">(197401003452 / 20547-U)</div>
        <div style="font-weight:700;font-size:13px;margin-top:12px;letter-spacing:.06em">APPLICATION FOR ASSET DISPOSAL</div>
      </div>

      {{-- Items Table (rows injected by JS) --}}
      <table id="woItemTable" style="width:100%;border-collapse:collapse;font-size:11px;margin-bottom:20px">
        <thead>
          <tr>
            <th style="border:1px solid #000;padding:5px 6px;text-align:center;width:28px">No.</th>
            <th style="border:1px solid #000;padding:5px 6px;text-align:center">Description of item to be written-off</th>
            <th style="border:1px solid #000;padding:5px 6px;text-align:center;width:50px">Year</th>
            <th style="border:1px solid #000;padding:5px 6px;text-align:center;width:55px">Age (Year)</th>
            <th style="border:1px solid #000;padding:5px 6px;text-align:center;width:55px">Quantity</th>
            <th style="border:1px solid #000;padding:5px 6px;text-align:center;width:70px">Purchase Cost (RM)</th>
            <th style="border:1px solid #000;padding:5px 6px;text-align:center;width:80px">Accumulated Depreciation (RM)</th>
            <th style="border:1px solid #000;padding:5px 6px;text-align:center;width:65px">Net Book Value (RM)</th>
            <th style="border:1px solid #000;padding:5px 6px;text-align:center;width:90px">Reason to dispose</th>
            <th style="border:1px solid #000;padding:5px 6px;text-align:center;width:90px">Estimated cost of repair (RM)</th>
          </tr>
        </thead>
        <tbody id="woItemBody"></tbody>
      </table>

      {{-- Signatures Table --}}
      <table style="width:100%;border-collapse:collapse;font-size:11px;margin-bottom:0">
        <tr>
          {{-- Proposed By --}}
          <td style="border:1px solid #000;padding:10px 12px;vertical-align:top;width:33%">
            <div style="font-weight:700;margin-bottom:10px">PROPOSED BY :</div>
            <div style="margin-bottom:6px">Signature :
              <canvas id="woSigCanvas" width="180" height="60"
                style="border-bottom:1px solid #000;display:block;margin-top:6px;cursor:crosshair;background:#fff;touch-action:none"></canvas>
            </div>
            <div style="margin-top:4px;margin-bottom:6px">Name : <strong>{{ $user->full_name }}</strong></div>
            <div>Date : {{ now()->format('d/m/Y') }}</div>
            <div style="margin-top:8px;display:flex;gap:6px;flex-wrap:wrap">
              <button type="button" onclick="clearSig()"
                style="font-size:10px;padding:2px 8px;border:1px solid #ccc;border-radius:4px;cursor:pointer;background:#f9f9f9">
                Clear Signature
              </button>
              @if($savedSigUrl)
              <button type="button" onclick="useSavedSig()"
                style="font-size:10px;padding:2px 8px;border:1px solid #0284c7;border-radius:4px;cursor:pointer;background:#e0f2fe;color:#0284c7;font-weight:700">
                <i class="bi bi-pen"></i> Use Saved Sig
              </button>
              @else
              <a href="{{ route('it.profile') }}" target="_blank"
                style="font-size:10px;padding:2px 8px;border:1px solid #ccc;border-radius:4px;background:#f9f9f9;text-decoration:none;color:#555">
                Upload Sig in Profile
              </a>
              @endif
            </div>
          </td>
          {{-- Checked By (HOU) --}}
          <td style="border:1px solid #000;padding:10px 12px;vertical-align:top;width:33%">
            <div style="font-weight:700;margin-bottom:10px">CHECKED BY :</div>
            <div style="margin-bottom:6px">Signature :<div style="border-bottom:1px solid #000;height:40px;margin-top:6px"></div></div>
            <div style="margin-bottom:6px;margin-top:4px">
              Name :
              @if($houUsers->count())
              <select id="woHouSelect" onchange="syncHou(this)"
                style="border:none;border-bottom:1px solid #000;font-size:11px;font-family:Arial,sans-serif;background:transparent;outline:none;padding:1px 2px;cursor:pointer;min-width:120px">
                <option value="">— Select HOU —</option>
                @foreach($houUsers as $hu)
                <option value="{{ $hu->id }}">{{ $hu->full_name }}@if($hu->dept_name) ({{ $hu->dept_name }})@endif</option>
                @endforeach
              </select>
              @else
              <span style="font-size:10px;color:#999;margin-left:4px;font-style:italic">No HOU found</span>
              @endif
            </div>
            <div>Date : {{ now()->format('d/m/Y') }}</div>
          </td>
          {{-- Recommendation Committee --}}
          <td style="border:1px solid #000;padding:10px 12px;vertical-align:top;width:34%">
            <div style="font-weight:700;margin-bottom:10px">RECOMMENDATION COMMITTEE :</div>
            <div style="margin-bottom:6px">Signature :<div style="border-bottom:1px solid #000;height:40px;margin-top:6px"></div></div>
            <div style="margin-bottom:6px;margin-top:4px">Name : <em>General Manager</em></div>
            <div>Date : <span style="display:inline-block;border-bottom:1px solid #000;width:80px"></span></div>
          </td>
        </tr>
      </table>

      {{-- CEO Approval --}}
      <div style="border:1px solid #000;border-top:none;padding:14px 16px;text-align:center">
        <div style="font-weight:700;font-size:12px;text-decoration:underline;margin-bottom:6px">APPROVAL</div>
        <div style="font-size:11px;margin-bottom:20px">I approve / not approve this disposal request.</div>
        <div style="display:inline-block;text-align:center;margin-bottom:4px">
          <div style="border-bottom:1px solid #000;width:220px;height:35px;margin:0 auto 4px"></div>
          <div style="font-size:11px">Mohd Izuddin Bin Selamat</div>
          <div style="font-size:11px;font-weight:700">Chief Executive Officer</div>
        </div>
        <div style="font-size:11px;margin-top:10px">Date : <span style="display:inline-block;border-bottom:1px solid #000;width:140px;margin-left:4px"></span></div>
      </div>
    </div>{{-- /woPrintArea --}}
    </div>

    {{-- Modal Footer --}}
    <div class="wo-staff-form-footer" style="padding:14px 20px;border-top:1px solid #e5e7eb;display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap;background:#f9fafb;border-radius:0 0 12px 12px">
      <button type="button" onclick="printWOForm()"
        style="display:inline-flex;align-items:center;gap:7px;background:#fff;color:#374151;border:1.5px solid #d1d5db;border-radius:8px;padding:9px 18px;font-size:13px;font-weight:600;cursor:pointer">
        <i class="bi bi-printer-fill"></i> Print Form
      </button>
      <button type="button" onclick="confirmWOForm()"
        style="display:inline-flex;align-items:center;gap:7px;background:#142b47;color:#fff;border:none;border-radius:8px;padding:9px 22px;font-size:13px;font-weight:700;cursor:pointer">
        <i class="bi bi-check-lg"></i> Confirm &amp; Close
      </button>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     HOU SIGN FORM MODAL
══════════════════════════════════════════════════════════════ --}}
<div id="houSignModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,.65);z-index:9000;align-items:flex-start;justify-content:center;padding:20px;overflow-y:auto">
  <div style="background:#fff;border-radius:12px;width:100%;max-width:860px;margin:auto;box-shadow:0 24px 60px rgba(0,0,0,.35);font-family:Arial,sans-serif">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid #ddd">
      <span style="font-weight:700;font-size:14px;color:#142b47">
        <i class="bi bi-pen" style="color:#38bdf8;margin-right:6px"></i>HOU — Write-Off Review Form
        <span id="houSignCount" style="font-size:12px;font-weight:600;color:#0284c7;margin-left:8px"></span>
      </span>
      <button onclick="document.getElementById('houSignModal').style.display='none'" style="background:none;border:none;font-size:22px;cursor:pointer;color:#9ca3af;line-height:1">&times;</button>
    </div>
    <div style="padding:28px 32px">
      <div style="text-align:center;margin-bottom:18px">
        <div style="font-weight:800;font-size:15px;text-decoration:underline;letter-spacing:.03em">FGV JOHOR BULKERS SDN BHD</div>
        <div style="font-size:12px;color:#444;margin-top:3px">(197401003452 / 20547-U)</div>
        <div style="font-weight:700;font-size:13px;margin-top:12px;letter-spacing:.06em">APPLICATION FOR ASSET DISPOSAL</div>
      </div>
      <table id="houSignItemTable" style="width:100%;border-collapse:collapse;font-size:11px;margin-bottom:20px">
        <thead>
          <tr>
            <th style="border:1px solid #000;padding:5px 6px;text-align:center;width:28px">No.</th>
            <th style="border:1px solid #000;padding:5px 6px;text-align:center">Description of item to be written-off</th>
            <th style="border:1px solid #000;padding:5px 6px;text-align:center;width:90px">Asset No.</th>
            <th style="border:1px solid #000;padding:5px 6px;text-align:center;width:80px">Serial No.</th>
            <th style="border:1px solid #000;padding:5px 6px;text-align:center;width:80px">Class</th>
          </tr>
        </thead>
        <tbody id="houSignItemBody"></tbody>
      </table>
      <table style="width:100%;border-collapse:collapse;font-size:11px;margin-bottom:0">
        <tr>
          <td style="border:1px solid #000;padding:10px 12px;vertical-align:top;width:33%">
            <div style="font-weight:700;margin-bottom:10px">PROPOSED BY :</div>
            <img id="houProposedSig" src="" style="height:60px;max-width:100%;display:block;margin-bottom:6px;border-bottom:1px solid #000;object-fit:contain">
            <div style="margin-bottom:4px">Name : <strong id="houProposedName"></strong></div>
            <div>Date : <span id="houProposedDate"></span></div>
          </td>
          <td style="border:1px solid #000;padding:10px 12px;vertical-align:top;width:33%;background:#fffbeb">
            <div style="font-weight:700;margin-bottom:6px">CHECKED BY :</div>
            <div style="margin-bottom:4px;font-size:10px;color:#6b7280">Draw your signature below:</div>
            <canvas id="houSigCanvas" width="200" height="56"
              style="border-bottom:1.5px solid #000;display:block;margin-bottom:6px;cursor:crosshair;background:#fff;touch-action:none;width:100%"></canvas>
            <input type="hidden" id="houSigInput">
            <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:6px">
              <button type="button" onclick="clearCanvas('houSigCanvas','houSigInput')"
                style="font-size:10px;padding:2px 8px;border:1px solid #ccc;border-radius:4px;cursor:pointer;background:#f9f9f9">Clear</button>
              @if($savedSigUrl)
              <button type="button" onclick="loadSavedSigTo('{{ $savedSigUrl }}','houSigCanvas','houSigInput')"
                style="font-size:10px;padding:2px 8px;border:1px solid #0284c7;border-radius:4px;cursor:pointer;background:#e0f2fe;color:#0284c7;font-weight:700">
                <i class="bi bi-pen"></i> Use Profile Signature</button>
              @else
              <a href="{{ route('it.profile') }}" target="_blank"
                style="font-size:10px;padding:2px 8px;border:1px solid #ccc;border-radius:4px;background:#f9f9f9;text-decoration:none;color:#555">
                Upload Sig in Profile</a>
              @endif
            </div>
            <div>Name : <strong>{{ auth('it')->user()->full_name }}</strong></div>
            <div>Date : {{ now()->format('d/m/Y') }}</div>
          </td>
          <td style="border:1px solid #000;padding:10px 12px;vertical-align:top;width:34%">
            <div style="font-weight:700;margin-bottom:10px">RECOMMENDATION COMMITTEE :</div>
            <div style="border-bottom:1px solid #000;width:100%;height:40px;margin-bottom:8px"></div>
            <div style="font-size:10px;font-style:italic">
              <em>General Manager</em><br>Date : <span style="display:inline-block;border-bottom:1px solid #000;width:90px"></span>
            </div>
          </td>
        </tr>
      </table>
      <div style="border:1px solid #000;border-top:none;padding:14px 16px;text-align:center">
        <div style="font-weight:700;font-size:12px;text-decoration:underline;margin-bottom:6px">APPROVAL</div>
        <div style="font-size:11px;margin-bottom:16px">I approve / not approve this disposal request.</div>
        <div style="display:inline-block;text-align:center">
          <div style="border-bottom:1px solid #000;width:220px;height:35px;margin:0 auto 4px"></div>
          <div style="font-size:11px">Mohd Izuddin Bin Selamat</div>
          <div style="font-size:11px;font-weight:700">Chief Executive Officer</div>
        </div>
        <div style="font-size:11px;margin-top:10px">Date : <span style="display:inline-block;border-bottom:1px solid #000;width:140px;margin-left:4px"></span></div>
      </div>
    </div>
    <form id="houSignForm" method="POST" action="{{ route('it.writeoff.hou-sign') }}">
      @csrf
      <input type="hidden" name="hou_sign_id" id="houSignId">
      <input type="hidden" name="hou_sig_img" id="houSigFormInput">
      <div style="padding:16px 24px;background:#f9fafb;border-top:1px solid #e5e7eb;display:flex;flex-direction:column;gap:12px">
        <div style="display:flex;gap:20px">
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-family:'Inter',sans-serif;font-size:13px;font-weight:600">
            <input type="radio" name="hou_action" value="approve" checked> Approve / Check
          </label>
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-family:'Inter',sans-serif;font-size:13px;font-weight:600">
            <input type="radio" name="hou_action" value="reject"> Reject
          </label>
        </div>
        <div>
          <label class="form-label">Remark (optional)</label>
          <textarea name="hou_remark" class="form-control" rows="2" placeholder="Optional remark..."></textarea>
        </div>
      </div>
      <div style="padding:14px 24px;border-top:1px solid #e5e7eb;display:flex;gap:8px;justify-content:flex-end;background:#f9fafb;border-radius:0 0 12px 12px">
        <button type="button" onclick="document.getElementById('houSignModal').style.display='none'" class="btn-secondary-custom">Cancel</button>
        <button type="submit" onclick="syncHouSig()" class="btn-primary-custom"><i class="bi bi-check-lg"></i> Submit Signature</button>
      </div>
    </form>
  </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     GM SIGN FORM MODAL
══════════════════════════════════════════════════════════════ --}}
<div id="gmSignModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,.65);z-index:9000;align-items:flex-start;justify-content:center;padding:20px;overflow-y:auto">
  <div style="background:#fff;border-radius:12px;width:100%;max-width:860px;margin:auto;box-shadow:0 24px 60px rgba(0,0,0,.35);font-family:Arial,sans-serif">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid #ddd">
      <span style="font-weight:700;font-size:14px;color:#142b47">
        <i class="bi bi-pen" style="color:#2dd4bf;margin-right:6px"></i>GM — Write-Off Review Form
        <span id="gmSignCount" style="font-size:12px;font-weight:600;color:#0d9488;margin-left:8px"></span>
      </span>
      <button onclick="document.getElementById('gmSignModal').style.display='none'" style="background:none;border:none;font-size:22px;cursor:pointer;color:#9ca3af;line-height:1">&times;</button>
    </div>
    <div style="padding:28px 32px">
      <div style="text-align:center;margin-bottom:18px">
        <div style="font-weight:800;font-size:15px;text-decoration:underline;letter-spacing:.03em">FGV JOHOR BULKERS SDN BHD</div>
        <div style="font-size:12px;color:#444;margin-top:3px">(197401003452 / 20547-U)</div>
        <div style="font-weight:700;font-size:13px;margin-top:12px;letter-spacing:.06em">APPLICATION FOR ASSET DISPOSAL</div>
      </div>
      <table id="gmSignItemTable" style="width:100%;border-collapse:collapse;font-size:11px;margin-bottom:20px">
        <thead>
          <tr>
            <th style="border:1px solid #000;padding:5px 6px;text-align:center;width:28px">No.</th>
            <th style="border:1px solid #000;padding:5px 6px;text-align:center">Description of item to be written-off</th>
            <th style="border:1px solid #000;padding:5px 6px;text-align:center;width:90px">Asset No.</th>
            <th style="border:1px solid #000;padding:5px 6px;text-align:center;width:80px">Serial No.</th>
            <th style="border:1px solid #000;padding:5px 6px;text-align:center;width:80px">Class</th>
          </tr>
        </thead>
        <tbody id="gmSignItemBody"></tbody>
      </table>
      <table style="width:100%;border-collapse:collapse;font-size:11px;margin-bottom:0">
        <tr>
          <td style="border:1px solid #000;padding:10px 12px;vertical-align:top;width:33%">
            <div style="font-weight:700;margin-bottom:10px">PROPOSED BY :</div>
            <img id="gmProposedSig" src="" style="height:60px;max-width:100%;display:block;margin-bottom:6px;border-bottom:1px solid #000;object-fit:contain">
            <div style="margin-bottom:4px">Name : <strong id="gmProposedName"></strong></div>
            <div>Date : <span id="gmProposedDate"></span></div>
          </td>
          <td style="border:1px solid #000;padding:10px 12px;vertical-align:top;width:33%">
            <div style="font-weight:700;margin-bottom:10px">CHECKED BY :</div>
            <img id="gmHouSig" src="" style="height:56px;max-width:100%;display:block;margin-bottom:6px;border-bottom:1px solid #000;object-fit:contain">
            <div style="margin-bottom:4px">Name : <strong id="gmHouSignedName"></strong></div>
            <div>Date : <span id="gmHouSignedDate"></span></div>
          </td>
          <td style="border:1px solid #000;padding:10px 12px;vertical-align:top;width:34%;background:#f0fdf4">
            <div style="font-weight:700;margin-bottom:6px">RECOMMENDATION COMMITTEE :</div>
            <div style="margin-bottom:4px;font-size:10px;color:#6b7280">Draw your signature below (left slot):</div>
            <canvas id="gmSigCanvas" width="200" height="50"
              style="border-bottom:1.5px solid #000;display:block;margin-bottom:6px;cursor:crosshair;background:#fff;touch-action:none;width:100%"></canvas>
            <input type="hidden" id="gmSigInput">
            <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:6px">
              <button type="button" onclick="clearCanvas('gmSigCanvas','gmSigInput')"
                style="font-size:10px;padding:2px 8px;border:1px solid #ccc;border-radius:4px;cursor:pointer;background:#f9f9f9">Clear</button>
              @if($savedSigUrl)
              <button type="button" onclick="loadSavedSigTo('{{ $savedSigUrl }}','gmSigCanvas','gmSigInput')"
                style="font-size:10px;padding:2px 8px;border:1px solid #0284c7;border-radius:4px;cursor:pointer;background:#e0f2fe;color:#0284c7;font-weight:700">
                <i class="bi bi-pen"></i> Use Profile Signature</button>
              @else
              <a href="{{ route('it.profile') }}" target="_blank"
                style="font-size:10px;padding:2px 8px;border:1px solid #ccc;border-radius:4px;background:#f9f9f9;text-decoration:none;color:#555">
                Upload Sig in Profile</a>
              @endif
            </div>
            <div style="display:flex;justify-content:space-between;font-size:10px;font-style:italic">
              <div style="width:45%"><em><strong>{{ auth('it')->user()->full_name }}</strong></em><br><em>General Manager</em><br>Date : {{ now()->format('d/m/Y') }}</div>
              <div style="width:45%;border-left:1px solid #ccc;padding-left:6px"><em>General Manager</em><br>Date : <span style="display:inline-block;border-bottom:1px solid #000;width:60px"></span></div>
            </div>
          </td>
        </tr>
      </table>
      <div style="border:1px solid #000;border-top:none;padding:14px 16px;text-align:center">
        <div style="font-weight:700;font-size:12px;text-decoration:underline;margin-bottom:6px">APPROVAL</div>
        <div style="font-size:11px;margin-bottom:16px">I approve / not approve this disposal request.</div>
        <div style="display:inline-block;text-align:center">
          <div style="border-bottom:1px solid #000;width:220px;height:35px;margin:0 auto 4px"></div>
          <div style="font-size:11px">Mohd Izuddin Bin Selamat</div>
          <div style="font-size:11px;font-weight:700">Chief Executive Officer</div>
        </div>
        <div style="font-size:11px;margin-top:10px">Date : <span style="display:inline-block;border-bottom:1px solid #000;width:140px;margin-left:4px"></span></div>
      </div>
    </div>
    <form id="gmSignForm" method="POST" action="{{ route('it.writeoff.gm-sign') }}">
      @csrf
      <input type="hidden" name="gm_sign_id" id="gmSignId">
      <input type="hidden" name="gm_sig_img" id="gmSigFormInput">
      <div style="padding:16px 24px;background:#f9fafb;border-top:1px solid #e5e7eb;display:flex;flex-direction:column;gap:12px">
        <div style="display:flex;gap:20px">
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-family:'Inter',sans-serif;font-size:13px;font-weight:600">
            <input type="radio" name="gm_action" value="approve" checked> Approve / Check
          </label>
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-family:'Inter',sans-serif;font-size:13px;font-weight:600">
            <input type="radio" name="gm_action" value="reject"> Reject
          </label>
        </div>
        <div>
          <label class="form-label">Remark (optional)</label>
          <textarea name="gm_remark" class="form-control" rows="2" placeholder="Optional remark..."></textarea>
        </div>
      </div>
      <div style="padding:14px 24px;border-top:1px solid #e5e7eb;display:flex;gap:8px;justify-content:flex-end;background:#f9fafb;border-radius:0 0 12px 12px">
        <button type="button" onclick="document.getElementById('gmSignModal').style.display='none'" class="btn-secondary-custom">Cancel</button>
        <button type="submit" onclick="syncGmSig()" class="btn-primary-custom"><i class="bi bi-check-lg"></i> Submit Signature</button>
      </div>
    </form>
  </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     CEO SIGN FORM MODAL
══════════════════════════════════════════════════════════════ --}}
<div id="ceoSignModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,.65);z-index:9000;align-items:flex-start;justify-content:center;padding:20px;overflow-y:auto">
  <div style="background:#fff;border-radius:12px;width:100%;max-width:860px;margin:auto;box-shadow:0 24px 60px rgba(0,0,0,.35);font-family:Arial,sans-serif">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid #ddd">
      <span style="font-weight:700;font-size:14px;color:#142b47">
        <i class="bi bi-shield-check" style="color:#f59e0b;margin-right:6px"></i>CEO — Write-Off Approval Form
        <span id="ceoSignCount" style="font-size:12px;font-weight:600;color:#d97706;margin-left:8px"></span>
      </span>
      <button onclick="document.getElementById('ceoSignModal').style.display='none'" style="background:none;border:none;font-size:22px;cursor:pointer;color:#9ca3af;line-height:1">&times;</button>
    </div>
    <div style="padding:28px 32px">
      <div style="text-align:center;margin-bottom:18px">
        <div style="font-weight:800;font-size:15px;text-decoration:underline;letter-spacing:.03em">FGV JOHOR BULKERS SDN BHD</div>
        <div style="font-size:12px;color:#444;margin-top:3px">(197401003452 / 20547-U)</div>
        <div style="font-weight:700;font-size:13px;margin-top:12px;letter-spacing:.06em">APPLICATION FOR ASSET DISPOSAL</div>
      </div>
      <table id="ceoSignItemTable" style="width:100%;border-collapse:collapse;font-size:11px;margin-bottom:20px">
        <thead>
          <tr>
            <th style="border:1px solid #000;padding:5px 6px;text-align:center;width:28px">No.</th>
            <th style="border:1px solid #000;padding:5px 6px;text-align:center">Description of item to be written-off</th>
            <th style="border:1px solid #000;padding:5px 6px;text-align:center;width:90px">Asset No.</th>
            <th style="border:1px solid #000;padding:5px 6px;text-align:center;width:80px">Serial No.</th>
            <th style="border:1px solid #000;padding:5px 6px;text-align:center;width:80px">Class</th>
          </tr>
        </thead>
        <tbody id="ceoSignItemBody"></tbody>
      </table>
      <table style="width:100%;border-collapse:collapse;font-size:11px;margin-bottom:0">
        <tr>
          <td style="border:1px solid #000;padding:10px 12px;vertical-align:top;width:33%">
            <div style="font-weight:700;margin-bottom:10px">PROPOSED BY :</div>
            <img id="ceoProposedSig" src="" style="height:60px;max-width:100%;display:block;margin-bottom:6px;border-bottom:1px solid #000;object-fit:contain">
            <div style="margin-bottom:4px">Name : <strong id="ceoProposedName"></strong></div>
            <div>Date : <span id="ceoProposedDate"></span></div>
          </td>
          <td style="border:1px solid #000;padding:10px 12px;vertical-align:top;width:33%">
            <div style="font-weight:700;margin-bottom:10px">CHECKED BY :</div>
            <img id="ceoHouSig" src="" style="height:56px;max-width:100%;display:block;margin-bottom:6px;border-bottom:1px solid #000;object-fit:contain">
            <div style="margin-bottom:4px">Name : <strong id="ceoHouSignedName"></strong></div>
            <div>Date : <span id="ceoHouSignedDate"></span></div>
          </td>
          <td style="border:1px solid #000;padding:10px 12px;vertical-align:top;width:34%">
            <div style="font-weight:700;margin-bottom:10px">RECOMMENDATION COMMITTEE :</div>
            <div style="margin-bottom:6px">
              <img id="ceoGmSig" src="" style="height:50px;max-width:100%;display:block;border-bottom:1px solid #000;object-fit:contain">
            </div>
            <div style="font-size:10px;font-style:italic">
              <strong id="ceoGmSignedName"></strong><br><em>General Manager</em><br>Date : <span id="ceoGmSignedDate"></span>
            </div>
          </td>
        </tr>
      </table>
      <div style="border:1px solid #000;border-top:none;padding:14px 16px;background:#fffbeb">
        <div style="font-weight:700;font-size:12px;text-decoration:underline;margin-bottom:6px;text-align:center">APPROVAL</div>
        <div style="font-size:11px;margin-bottom:10px;text-align:center">I approve / not approve this disposal request.</div>
        <div style="text-align:center">
          <canvas id="ceoSigCanvas" width="220" height="56"
            style="border-bottom:1.5px solid #000;display:block;margin:0 auto 6px;cursor:crosshair;background:#fff;touch-action:none;max-width:220px"></canvas>
          <input type="hidden" id="ceoSigInput">
          <div style="display:flex;gap:6px;justify-content:center;flex-wrap:wrap;margin-bottom:6px">
            <button type="button" onclick="clearCanvas('ceoSigCanvas','ceoSigInput')"
              style="font-size:10px;padding:2px 8px;border:1px solid #ccc;border-radius:4px;cursor:pointer;background:#f9f9f9">Clear</button>
            @if($savedSigUrl)
            <button type="button" onclick="loadSavedSigTo('{{ $savedSigUrl }}','ceoSigCanvas','ceoSigInput')"
              style="font-size:10px;padding:2px 8px;border:1px solid #0284c7;border-radius:4px;cursor:pointer;background:#e0f2fe;color:#0284c7;font-weight:700">
              <i class="bi bi-pen"></i> Use Profile Signature</button>
            @else
            <a href="{{ route('it.profile') }}" target="_blank"
              style="font-size:10px;padding:2px 8px;border:1px solid #ccc;border-radius:4px;background:#f9f9f9;text-decoration:none;color:#555">
              Upload Sig in Profile</a>
            @endif
          </div>
          <div style="font-size:11px">{{ auth('it')->user()->full_name }}</div>
          <div style="font-size:11px;font-weight:700">Chief Executive Officer</div>
          <div style="font-size:11px;margin-top:6px">Date : {{ now()->format('d/m/Y') }}</div>
        </div>
      </div>
    </div>
    <form id="ceoSignForm" method="POST" action="{{ route('it.writeoff.ceo-approve') }}">
      @csrf
      <input type="hidden" name="ceo_sign_id" id="ceoSignId">
      <input type="hidden" name="ceo_sig_img" id="ceoSigFormInput">
      <div style="padding:16px 24px;background:#f9fafb;border-top:1px solid #e5e7eb;display:flex;flex-direction:column;gap:12px">
        <div style="display:flex;gap:20px">
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-family:'Inter',sans-serif;font-size:13px;font-weight:600">
            <input type="radio" name="ceo_action" value="approve" checked> Approve
          </label>
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-family:'Inter',sans-serif;font-size:13px;font-weight:600">
            <input type="radio" name="ceo_action" value="reject"> Reject
          </label>
        </div>
        <div>
          <label class="form-label">Remark (optional)</label>
          <textarea name="ceo_remark" class="form-control" rows="2" placeholder="Optional remark..."></textarea>
        </div>
      </div>
      <div style="padding:14px 24px;border-top:1px solid #e5e7eb;display:flex;gap:8px;justify-content:flex-end;background:#f9fafb;border-radius:0 0 12px 12px">
        <button type="button" onclick="document.getElementById('ceoSignModal').style.display='none'" class="btn-secondary-custom">Cancel</button>
        <button type="submit" onclick="syncCeoSig()" class="btn-primary-custom"><i class="bi bi-shield-check"></i> Submit Approval</button>
      </div>
    </form>
  </div>
</div>

{{-- Assign HOU Modal (admin) --}}
@if($user->isAdmin())
<div id="assignHOUModal" style="display:none;position:fixed;inset:0;z-index:9000;background:rgba(0,0,0,.45);align-items:center;justify-content:center;padding:20px">
  <div style="background:var(--surface);border-radius:16px;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,.3)">
    <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center">
      <div style="font-size:15px;font-weight:700;color:var(--text)"><i class="bi bi-person-plus me-2"></i>Assign to HOU</div>
      <button onclick="document.getElementById('assignHOUModal').style.display='none'" style="background:none;border:none;cursor:pointer;font-size:20px;color:var(--muted)">&times;</button>
    </div>
    <form id="assignHOUForm" method="POST" action="{{ route('it.writeoff.assign-hou') }}">
      @csrf
      <input type="hidden" name="selected_ids[]" id="assignHouItemId">
      <div style="padding:24px">
        <label class="form-label">Select HOU User *</label>
        <select name="hou_user_id" class="form-select" required>
          <option value="">Select...</option>
          @foreach($houUsers as $hu)
          <option value="{{ $hu->id }}">{{ $hu->full_name }}@if($hu->dept_name) — {{ $hu->dept_name }}@endif</option>
          @endforeach
        </select>
      </div>
      <div style="padding:16px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:8px">
        <button type="button" onclick="document.getElementById('assignHOUModal').style.display='none'" class="btn-secondary-custom">Cancel</button>
        <button type="submit" class="btn-primary-custom"><i class="bi bi-check-lg"></i> Assign</button>
      </div>
    </form>
  </div>
</div>
@endif

@endsection

@push('scripts')
<script>
// ── Signature canvas utility ──────────────────────────────────
var _canvasState = {};

function initCanvas(canvasId, inputId) {
  var cv = document.getElementById(canvasId);
  if (!cv) return;
  var rect = cv.getBoundingClientRect();
  if (rect.width > 0) cv.width = Math.round(rect.width);
  var ctx = cv.getContext('2d');
  ctx.strokeStyle = '#142b47'; ctx.lineWidth = 2; ctx.lineCap = 'round';
  var drawing = false;
  function getPos(e) {
    var r = cv.getBoundingClientRect(), t = e.touches ? e.touches[0] : e;
    return { x: (t.clientX - r.left) * (cv.width / r.width),
             y: (t.clientY - r.top)  * (cv.height / r.height) };
  }
  cv.onmousedown  = function(e){ drawing=true; ctx.beginPath(); var p=getPos(e); ctx.moveTo(p.x,p.y); };
  cv.onmousemove  = function(e){ if(!drawing) return; var p=getPos(e); ctx.lineTo(p.x,p.y); ctx.stroke(); };
  cv.onmouseup    = function(){ drawing=false; document.getElementById(inputId).value=cv.toDataURL(); };
  cv.ontouchstart = function(e){ e.preventDefault(); drawing=true; ctx.beginPath(); var p=getPos(e); ctx.moveTo(p.x,p.y); };
  cv.ontouchmove  = function(e){ e.preventDefault(); if(!drawing) return; var p=getPos(e); ctx.lineTo(p.x,p.y); ctx.stroke(); };
  cv.ontouchend   = function(){ drawing=false; document.getElementById(inputId).value=cv.toDataURL(); };
  _canvasState[canvasId] = { ctx, inputId };
}

function clearCanvas(canvasId, inputId) {
  var cv = document.getElementById(canvasId);
  if (cv) { cv.getContext('2d').clearRect(0,0,cv.width,cv.height); }
  var inp = document.getElementById(inputId);
  if (inp) inp.value = '';
}

function loadSavedSigTo(url, canvasId, inputId) {
  var cv = document.getElementById(canvasId); if (!cv) return;
  var ctx = cv.getContext('2d');
  var img = new Image(); img.crossOrigin='anonymous';
  img.onload = function() {
    ctx.clearRect(0,0,cv.width,cv.height);
    var scale = Math.min(cv.width / img.naturalWidth, cv.height / img.naturalHeight);
    var w = img.naturalWidth * scale;
    var h = img.naturalHeight * scale;
    var x = (cv.width - w) / 2;
    var y = (cv.height - h) / 2;
    ctx.drawImage(img, x, y, w, h);
    document.getElementById(inputId).value = cv.toDataURL();
  };
  img.src = url;
}

// ── WO FORM MODAL ─────────────────────────────────────────────
var _woFormMode = 'single';
var _woSigDrawing = false, _woSigCtx, _woSigCanvas;
var _woFormSigned = false;
var _savedSigUrl  = {!! json_encode($savedSigUrl ?: '') !!};

var _singleItem   = {!! $woItem    ? json_encode(['no'=>$woItem->asset_number??'—','desc'=>($woItem->description??'').($woItem->brand?' ('.$woItem->brand.')':''),'class'=>$woItem->asset_class??'','serial'=>$woItem->serial_number??'','year'=>$woItem->years_purchase??'','total_cost'=>$woItem->total_cost??'','accumulated'=>$woItem->accumulated??'','nbv_at'=>$woItem->nbv_at??'']) : 'null' !!};
var _nitItem2     = {!! $nitItem   ? json_encode(['no'=>$nitItem->asset_number??'—','desc'=>$nitItem->description??'','class'=>$nitItem->asset_class??'','serial'=>$nitItem->serial_number??'','year'=>$nitItem->years_purchase??'','total_cost'=>$nitItem->total_cost??'','accumulated'=>$nitItem->accumulated??'','nbv_at'=>$nitItem->nbv_at??'']) : 'null' !!};
var _bulkItems2   = {!! $bulkItems->count()    ? json_encode($bulkItems->map(fn($i)=>['no'=>$i->asset_number??'—','desc'=>($i->description??'').($i->brand?' ('.$i->brand.')':''),'class'=>$i->asset_class??'','serial'=>$i->serial_number??'','year'=>$i->years_purchase??'','total_cost'=>$i->total_cost??'','accumulated'=>$i->accumulated??'','nbv_at'=>$i->nbv_at??''])->values()) : '[]' !!};
var _bulkNITItems2= {!! $bulkNitItems->count() ? json_encode($bulkNitItems->map(fn($i)=>['no'=>$i->asset_number??'—','desc'=>$i->description??'','class'=>$i->asset_class??'','serial'=>$i->serial_number??'','year'=>$i->years_purchase??'','total_cost'=>$i->total_cost??'','accumulated'=>$i->accumulated??'','nbv_at'=>$i->nbv_at??''])->values()) : '[]' !!};

function escHtml(s){return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}

function openWOFormModal(mode){
  _woFormMode=mode;
  document.getElementById('woFormModal').style.display='flex';
  var tbody=document.getElementById('woItemBody');
  tbody.innerHTML='';
  var items=mode==='nit'&&_nitItem2?[_nitItem2]:mode==='bulk_nit'?_bulkNITItems2:mode==='single'&&_singleItem?[_singleItem]:_bulkItems2;
  var curYear=new Date().getFullYear();
  items.forEach(function(item,i){
    var yr=item.year?String(item.year):'';
    var age=(item.year&&!isNaN(parseInt(item.year)))?String(curYear-parseInt(item.year)):'';
    var tc=item.total_cost?String(item.total_cost):'';
    var acc=item.accumulated?String(item.accumulated):'';
    var nbv=item.nbv_at?String(item.nbv_at):'';
    var desc=escHtml(item.desc+(item['class']?' ['+item['class']+']':'')+(item.no&&item.no!=='—'?' | '+item.no:''));
    var tr=document.createElement('tr');
    tr.innerHTML='<td style="border:1px solid #000;padding:4px 6px;text-align:center">'+(i+1)+'</td>'+
      '<td style="border:1px solid #000;padding:4px 6px"><input style="width:100%;border:none;outline:none;font-size:11px;background:transparent" value="'+desc+'"></td>'+
      '<td style="border:1px solid #000;padding:4px 6px"><input style="width:100%;border:none;outline:none;font-size:11px;text-align:center;background:transparent" value="'+escHtml(yr)+'" placeholder="Year"></td>'+
      '<td style="border:1px solid #000;padding:4px 6px"><input style="width:100%;border:none;outline:none;font-size:11px;text-align:center;background:transparent" value="'+escHtml(age)+'" placeholder="Age"></td>'+
      '<td style="border:1px solid #000;padding:4px 6px"><input style="width:100%;border:none;outline:none;font-size:11px;text-align:center;background:transparent" value="1"></td>'+
      '<td style="border:1px solid #000;padding:4px 6px"><input style="width:100%;border:none;outline:none;font-size:11px;text-align:center;background:transparent" value="'+escHtml(tc)+'" placeholder="—"></td>'+
      '<td style="border:1px solid #000;padding:4px 6px"><input style="width:100%;border:none;outline:none;font-size:11px;text-align:center;background:transparent" value="'+escHtml(acc)+'" placeholder="—"></td>'+
      '<td style="border:1px solid #000;padding:4px 6px"><input style="width:100%;border:none;outline:none;font-size:11px;text-align:center;background:transparent" value="'+escHtml(nbv)+'" placeholder="—"></td>'+
      '<td style="border:1px solid #000;padding:4px 6px"><input style="width:100%;border:none;outline:none;font-size:11px;background:transparent" placeholder="Obsolete / Damaged"></td>'+
      '<td style="border:1px solid #000;padding:4px 6px"><input style="width:100%;border:none;outline:none;font-size:11px;text-align:center;background:transparent" placeholder="—"></td>';
    tbody.appendChild(tr);
  });
  for(var r=0;r<Math.max(0,7-items.length);r++){
    var tr=document.createElement('tr');
    tr.innerHTML='<td style="border:1px solid #000;padding:4px 6px;text-align:center;color:#ccc">'+(items.length+r+1)+'</td>'+
      '<td style="border:1px solid #000;padding:4px 6px"><input style="width:100%;border:none;outline:none;font-size:11px;background:transparent"></td>'+
      '<td style="border:1px solid #000;padding:4px 6px"><input style="width:100%;border:none;outline:none;font-size:11px;text-align:center;background:transparent"></td>'+
      '<td style="border:1px solid #000;padding:4px 6px"><input style="width:100%;border:none;outline:none;font-size:11px;text-align:center;background:transparent"></td>'+
      '<td style="border:1px solid #000;padding:4px 6px"><input style="width:100%;border:none;outline:none;font-size:11px;text-align:center;background:transparent"></td>'+
      '<td style="border:1px solid #000;padding:4px 6px"><input style="width:100%;border:none;outline:none;font-size:11px;text-align:center;background:transparent"></td>'+
      '<td style="border:1px solid #000;padding:4px 6px"><input style="width:100%;border:none;outline:none;font-size:11px;text-align:center;background:transparent"></td>'+
      '<td style="border:1px solid #000;padding:4px 6px"><input style="width:100%;border:none;outline:none;font-size:11px;text-align:center;background:transparent"></td>'+
      '<td style="border:1px solid #000;padding:4px 6px"><input style="width:100%;border:none;outline:none;font-size:11px;background:transparent"></td>'+
      '<td style="border:1px solid #000;padding:4px 6px"><input style="width:100%;border:none;outline:none;font-size:11px;text-align:center;background:transparent"></td>';
    tbody.appendChild(tr);
  }
  _woSigCanvas=document.getElementById('woSigCanvas');
  _woSigCtx=_woSigCanvas.getContext('2d');
  _woSigCtx.strokeStyle='#000';_woSigCtx.lineWidth=1.8;_woSigCtx.lineCap='round';_woSigCtx.lineJoin='round';
  function getPos(e){var r=_woSigCanvas.getBoundingClientRect(),t=e.touches?e.touches[0]:e;return{x:t.clientX-r.left,y:t.clientY-r.top};}
  _woSigCanvas.onmousedown =function(e){_woSigDrawing=true;var p=getPos(e);_woSigCtx.beginPath();_woSigCtx.moveTo(p.x,p.y);};
  _woSigCanvas.onmousemove =function(e){if(!_woSigDrawing)return;var p=getPos(e);_woSigCtx.lineTo(p.x,p.y);_woSigCtx.stroke();};
  _woSigCanvas.onmouseup   =function(){_woSigDrawing=false;};
  _woSigCanvas.onmouseleave=function(){_woSigDrawing=false;};
  _woSigCanvas.ontouchstart=function(e){e.preventDefault();_woSigDrawing=true;var p=getPos(e);_woSigCtx.beginPath();_woSigCtx.moveTo(p.x,p.y);};
  _woSigCanvas.ontouchmove =function(e){e.preventDefault();if(!_woSigDrawing)return;var p=getPos(e);_woSigCtx.lineTo(p.x,p.y);_woSigCtx.stroke();};
  _woSigCanvas.ontouchend  =function(){_woSigDrawing=false;};
}
function closeWOFormModal(){document.getElementById('woFormModal').style.display='none';}
function clearSig(){if(_woSigCtx&&_woSigCanvas)_woSigCtx.clearRect(0,0,_woSigCanvas.width,_woSigCanvas.height);_woFormSigned=false;}
function useSavedSig(){
  if(!_savedSigUrl){alert('No saved signature found. Upload your signature in Profile Settings.');return;}
  var img=new Image();img.crossOrigin='anonymous';
  img.onload=function(){
    _woSigCtx.clearRect(0,0,_woSigCanvas.width,_woSigCanvas.height);
    var s=Math.min(_woSigCanvas.width/img.naturalWidth,_woSigCanvas.height/img.naturalHeight,1);
    _woSigCtx.drawImage(img,(_woSigCanvas.width-img.naturalWidth*s)/2,(_woSigCanvas.height-img.naturalHeight*s)/2,img.naturalWidth*s,img.naturalHeight*s);
  };img.src=_savedSigUrl;
}
function isSigBlank(){
  if(!_woSigCanvas)return true;
  var d=_woSigCtx.getImageData(0,0,_woSigCanvas.width,_woSigCanvas.height).data;
  for(var i=3;i<d.length;i+=4)if(d[i]>0)return false;
  return true;
}
function confirmWOForm(){
  if(isSigBlank()){alert('Please draw your signature in the "Proposed By" section before confirming.');return;}
  var houSel=document.getElementById('woHouSelect');
  if(houSel&&houSel.options.length>1&&!houSel.value){alert('Please select a Head of Unit in the "Checked By" section before confirming.');houSel.focus();return;}
  if(houSel&&houSel.value){var hf=document.getElementById('houHiddenField');if(hf)hf.value=houSel.value;}
  _woFormSigned=true;
  var sig=_woSigCanvas?_woSigCanvas.toDataURL('image/png'):'';
  if(sig){var f=document.getElementById('woSigImgField');if(f)f.value=sig;}
  var st=document.getElementById('singleFormStatus');if(st)st.style.display='flex';
  closeWOFormModal();
}
function printWOForm(){
  var content=document.getElementById('woPrintArea').innerHTML;
  var w=window.open('','_blank','width=900,height=700');
  w.document.write('<html><head><title>Write-Off Form</title><style>body{font-family:Arial,sans-serif;font-size:11px;padding:20px}table{width:100%;border-collapse:collapse}@media print{button{display:none}}</style></head><body>'+content+'</body></html>');
  w.document.close();w.focus();w.print();
}
function validateWOForm(){
  if(!_woFormSigned){
    var msg=document.getElementById('noFileMsg');
    if(msg){msg.style.display='block';msg.scrollIntoView({behavior:'smooth',block:'center'});}
    return false;
  }
  return true;
}
function syncHou(sel){var hf=document.getElementById('houHiddenField');if(hf)hf.value=sel.value;}

// ── Build items table for sign form modals ────────────────────
function buildSignDocItemTable(tbodyId, items) {
  var tbody = document.getElementById(tbodyId);
  tbody.innerHTML = '';
  items.forEach(function(item, i) {
    var tr = document.createElement('tr');
    tr.innerHTML =
      '<td style="border:1px solid #000;padding:4px 6px;text-align:center">' + (i+1) + '</td>' +
      '<td style="border:1px solid #000;padding:4px 6px;font-size:11px">' + escHtml(item.description) + (item.asset_class ? ' [' + escHtml(item.asset_class) + ']' : '') + '</td>' +
      '<td style="border:1px solid #000;padding:4px 6px;font-size:11px;text-align:center;font-family:monospace">' + escHtml(item.asset_number || '—') + '</td>' +
      '<td style="border:1px solid #000;padding:4px 6px;font-size:11px;text-align:center;font-family:monospace">' + escHtml(item.serial_number || '—') + '</td>' +
      '<td style="border:1px solid #000;padding:4px 6px;font-size:11px;text-align:center">' + escHtml(item.asset_class || '—') + '</td>';
    tbody.appendChild(tr);
  });
}

// ── HOU Form Modal ────────────────────────────────────────────
function openHouAssetsModal(btn) {
  var items = JSON.parse(btn.dataset.items || '[]');
  var title = btn.dataset.title || 'Submission Assets';
  var modal = document.getElementById('houAssetsModal');
  var list = document.getElementById('houAssetsModalList');
  var titleEl = document.getElementById('houAssetsModalTitle');
  var metaEl = document.getElementById('houAssetsModalMeta');

  titleEl.textContent = title;
  metaEl.textContent = items.length + ' listed asset' + (items.length === 1 ? '' : 's');
  list.innerHTML = '';

  items.forEach(function(item, i) {
    var row = document.createElement('div');
    row.className = 'wo-hou-modal-asset';
    row.innerHTML =
      '<div style="width:24px;height:24px;border-radius:50%;background:rgba(2,132,199,.10);color:var(--accent);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800">' + (i + 1) + '</div>' +
      '<div style="min-width:0">' +
        '<div style="font-size:13px;font-weight:750;color:var(--text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="' + escHtml(item.description || 'Asset') + '">' + escHtml(item.description || 'Asset') + '</div>' +
        '<div style="font-size:11px;color:var(--muted);margin-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">' + escHtml(item.asset_class || '-') + '</div>' +
      '</div>' +
      '<div class="wo-hou-modal-asset-meta" style="font-size:11px;color:var(--text);font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="' + escHtml(item.asset_number || '-') + '">' + escHtml(item.asset_number || '-') + '</div>' +
      '<div class="wo-hou-modal-asset-meta" style="font-size:11px;color:var(--muted);font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="' + escHtml(item.serial_number || '-') + '">' + escHtml(item.serial_number || '-') + '</div>';
    list.appendChild(row);
  });

  if (!items.length) {
    list.innerHTML = '<div style="text-align:center;color:var(--muted);padding:24px">No assets found for this submission.</div>';
  }

  modal.style.display = 'flex';
}

function closeHouAssetsModal() {
  document.getElementById('houAssetsModal').style.display = 'none';
}

function openHouFormModal(btn) {
  var items = JSON.parse(btn.dataset.items);
  var ids   = items.map(function(i){ return i.id; }).join(',');
  var first = items[0] || {};
  document.getElementById('houSignId').value = ids;
  document.getElementById('houSignCount').textContent = items.length + ' item' + (items.length !== 1 ? 's' : '');
  document.getElementById('houProposedName').textContent = first.writeoff_name || '—';
  document.getElementById('houProposedDate').textContent = first.writeoff_date || '—';
  var houProposedSig = document.getElementById('houProposedSig');
  if (houProposedSig) houProposedSig.src = first.writeoff_sig_img || '';
  buildSignDocItemTable('houSignItemBody', items);
  clearCanvas('houSigCanvas','houSigInput');
  initCanvas('houSigCanvas','houSigInput');
  document.getElementById('houSignModal').style.display = 'flex';
  document.getElementById('houSignModal').scrollTop = 0;
}

function syncHouSig() {
  var cv = document.getElementById('houSigCanvas');
  if (cv) document.getElementById('houSigFormInput').value = cv.toDataURL('image/png');
}

// ── GM Form Modal ─────────────────────────────────────────────
function openGmFormModal(btn) {
  var items = JSON.parse(btn.dataset.items);
  var ids   = items.map(function(i){ return i.id; }).join(',');
  var first = items[0] || {};
  document.getElementById('gmSignId').value = ids;
  document.getElementById('gmSignCount').textContent = items.length + ' item' + (items.length !== 1 ? 's' : '');
  document.getElementById('gmProposedName').textContent  = first.writeoff_name   || '—';
  document.getElementById('gmProposedDate').textContent  = first.writeoff_date   || '—';
  document.getElementById('gmHouSignedName').textContent = first.hou_signed_name || '—';
  document.getElementById('gmHouSignedDate').textContent = first.hou_signed_at   || '—';
  var gmProposedSig = document.getElementById('gmProposedSig');
  if (gmProposedSig) gmProposedSig.src = first.writeoff_sig_img || '';
  var gmHouSig = document.getElementById('gmHouSig');
  if (gmHouSig) gmHouSig.src = first.hou_sig_img || '';
  buildSignDocItemTable('gmSignItemBody', items);
  clearCanvas('gmSigCanvas','gmSigInput');
  initCanvas('gmSigCanvas','gmSigInput');
  document.getElementById('gmSignModal').style.display = 'flex';
  document.getElementById('gmSignModal').scrollTop = 0;
}

function syncGmSig() {
  var cv = document.getElementById('gmSigCanvas');
  if (cv) document.getElementById('gmSigFormInput').value = cv.toDataURL('image/png');
}

// ── CEO Form Modal ────────────────────────────────────────────
function openCeoFormModal(btn) {
  var items = JSON.parse(btn.dataset.items);
  var ids   = items.map(function(i){ return i.id; }).join(',');
  var first = items[0] || {};
  document.getElementById('ceoSignId').value = ids;
  document.getElementById('ceoSignCount').textContent = items.length + ' item' + (items.length !== 1 ? 's' : '');
  document.getElementById('ceoProposedName').textContent  = first.writeoff_name   || '—';
  document.getElementById('ceoProposedDate').textContent  = first.writeoff_date   || '—';
  document.getElementById('ceoHouSignedName').textContent = first.hou_signed_name || '—';
  document.getElementById('ceoHouSignedDate').textContent = first.hou_signed_at   || '—';
  document.getElementById('ceoGmSignedName').textContent  = first.gm_signed_name  || '—';
  document.getElementById('ceoGmSignedDate').textContent  = first.gm_signed_at    || '—';
  var ceoProposedSig = document.getElementById('ceoProposedSig');
  if (ceoProposedSig) ceoProposedSig.src = first.writeoff_sig_img || '';
  var ceoHouSig = document.getElementById('ceoHouSig');
  if (ceoHouSig) ceoHouSig.src = first.hou_sig_img || '';
  var ceoGmSig = document.getElementById('ceoGmSig');
  if (ceoGmSig) ceoGmSig.src = first.gm_sig_img || '';
  buildSignDocItemTable('ceoSignItemBody', items);
  clearCanvas('ceoSigCanvas','ceoSigInput');
  initCanvas('ceoSigCanvas','ceoSigInput');
  document.getElementById('ceoSignModal').style.display = 'flex';
  document.getElementById('ceoSignModal').scrollTop = 0;
}

function syncCeoSig() {
  var cv = document.getElementById('ceoSigCanvas');
  if (cv) document.getElementById('ceoSigFormInput').value = cv.toDataURL('image/png');
}

// ── Assign HOU Modal ──────────────────────────────────────────
function openAssignHOU(id) {
  document.getElementById('assignHouItemId').value = id;
  document.getElementById('assignHOUModal').style.display = 'flex';
}


</script>
@endpush
