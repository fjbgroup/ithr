@extends('it.layouts.app')

@section('title', 'Asset Detail')
@section('page_title', 'Asset Detail')

@section('content')
<div style="max-width:800px">
  <div style="margin-bottom:16px">
    <a href="{{ route('it.inventory.index') }}" style="color:var(--accent);text-decoration:none;font-size:13px;font-weight:600">
      <i class="bi bi-arrow-left"></i> Back to Inventory
    </a>
  </div>

  <div class="table-card" style="margin-bottom:20px">
    <div class="table-card-header">
      <div>
        <div class="table-card-title">{{ $asset->description }}</div>
        <div style="font-size:11px;color:var(--muted);margin-top:2px">Asset ID #{{ $asset->id }}</div>
      </div>
      <span class="badge-status {{ $asset->item_status === 'Active' ? 'bs-active' : ($asset->item_status === 'Disposed' ? 'bs-disposed' : 'bs-pending') }}">
        {{ $asset->item_status }}
      </span>
    </div>
    <div style="padding:24px;display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:18px">
      @php
        $fields = [
          'Asset Number' => $asset->asset_number,
          'Asset Class' => $asset->asset_class,
          'FA Code' => $asset->fa_code,
          'Serial Number' => $asset->serial_number,
          'Brand' => $asset->brand,
          'Model' => $asset->model,
          'Location' => $asset->location,
          'Condition' => $asset->condition_status,
          'Purchase Date' => $asset->purchase_date,
          'Purchase Price' => $asset->purchase_price ? 'RM ' . number_format($asset->purchase_price, 2) : null,
          'Years of Purchase' => $asset->years_purchase,
          'Total Cost' => $asset->total_cost ? 'RM ' . number_format($asset->total_cost, 2) : null,
          'Accumulated' => $asset->accumulated ? 'RM ' . number_format($asset->accumulated, 2) : null,
          'NBV At' => $asset->nbv_at ? 'RM ' . number_format($asset->nbv_at, 2) : null,
          'Registered By' => $asset->creator?->full_name,
          'Registered On' => $asset->created_at?->format('d M Y'),
        ];
      @endphp
      @foreach($fields as $label => $value)
      @if($value)
      <div>
        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:4px">{{ $label }}</div>
        <div style="font-size:14px;font-weight:600;color:var(--text)">{{ $value }}</div>
      </div>
      @endif
      @endforeach
    </div>
    @if($asset->notes)
    <div style="padding:0 24px 24px">
      <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:4px">Notes</div>
      <div style="font-size:13px;color:var(--text)">{{ $asset->notes }}</div>
    </div>
    @endif
  </div>

  @if($asset->ewasteItems && $asset->ewasteItems->count())
  <div class="table-card">
    <div class="table-card-header">
      <span class="table-card-title">E-Waste History</span>
    </div>
    <table class="table" style="width:100%">
      <thead>
        <tr>
          <th>Date</th>
          <th>Disposal Status</th>
          <th>Disposal Method</th>
          <th>Vendor</th>
        </tr>
      </thead>
      <tbody>
        @foreach($asset->ewasteItems as $ew)
        <tr>
          <td>{{ $ew->created_at?->format('d M Y') }}</td>
          <td><span class="badge-status {{ $ew->disposal_status === 'Collected' ? 'bs-active' : 'bs-pending' }}">{{ $ew->disposal_status }}</span></td>
          <td>{{ $ew->disposal_method ?: '—' }}</td>
          <td>{{ $ew->vendor_collector ?: '—' }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif
</div>
@endsection

