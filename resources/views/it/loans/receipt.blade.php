@extends('it.layouts.app')

@section('title', 'IT Peripheral Loan Receipt')

@section('content')
<div class="topbar">
  <div class="topbar-left">
    <div class="topbar-title">Loan Receipt</div>
    <div class="topbar-breadcrumb">Inventory / IT Peripheral Loan / Receipt</div>
  </div>
  <div class="topbar-right">
    <button class="btn-primary-custom" onclick="window.print()">
      <i class="bi bi-printer"></i> Print Receipt
    </button>
  </div>
</div>

<div class="page-body">
  <div class="form-card mx-auto mt-4" style="max-width: 800px;">
    <div class="text-center mb-4 pb-3 border-bottom">
      <img src="{{ asset('assets/img/logo_transparent.png') }}" alt="Logo" style="height: 60px; margin-bottom: 10px;">
      <h4>IT Peripheral Loan Receipt</h4>
      <p class="text-muted mb-0">Record of Completed Peripheral Loan</p>
    </div>

    <div class="row mb-4">
      <div class="col-md-6">
        <label class="form-label">Referral Code</label>
        <div class="font-weight-bold fs-5">{{ $loan->referral_code }}</div>
      </div>
      <div class="col-md-6 text-md-end">
        <label class="form-label">Status</label>
        <div><span class="badge bg-success p-2">COMPLETED</span></div>
      </div>
    </div>

    <div class="row mb-4">
      <div class="col-md-6">
        <label class="form-label">Borrower Details</label>
        <div class="p-3 bg-light rounded border">
          <strong>{{ $loan->staff->name ?? 'N/A' }}</strong><br>
          <span class="text-muted">ID: {{ $loan->staff->staff_id ?? 'N/A' }}</span><br>
          <span class="text-muted">Dept: {{ $loan->staff->department->name ?? 'N/A' }}</span>
        </div>
      </div>
      <div class="col-md-6">
        <label class="form-label">Asset Details</label>
        <div class="p-3 bg-light rounded border">
          <strong>{{ $loan->inventoryItem->description ?? 'N/A' }}</strong><br>
          <span class="text-muted">Asset No: {{ $loan->inventoryItem->asset_number ?? 'N/A' }}</span><br>
          <span class="text-muted">S/N: {{ $loan->inventoryItem->serial_number ?? 'N/A' }}</span>
        </div>
      </div>
    </div>
    
    <div class="mb-4">
      <label class="form-label">Loan Period</label>
      <div class="p-3 bg-light rounded border">
        <strong>From:</strong> {{ $loan->loan_start_date ? $loan->loan_start_date->format('d M Y') : 'N/A' }}<br>
        <strong>To:</strong> {{ $loan->loan_end_date ? $loan->loan_end_date->format('d M Y') : 'N/A' }}
      </div>
    </div>

    <div class="mb-4">
      <label class="form-label">Timeline & Verification</label>
      <table class="table table-bordered text-center mt-2">
        <thead class="bg-light">
          <tr>
            <th>Event</th>
            <th>Date / Time</th>
            <th>Verified By</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="fw-bold text-start">Application Submitted</td>
            <td>{{ $loan->created_at->format('d M Y, h:i A') }}</td>
            <td>-</td>
          </tr>
          <tr>
            <td class="fw-bold text-start">Item Picked Up</td>
            <td>{{ $loan->pickup_verified_at ? $loan->pickup_verified_at->format('d M Y, h:i A') : 'N/A' }}</td>
            <td>{{ $loan->pickupVerifiedBy->name ?? 'N/A' }}</td>
          </tr>
          <tr>
            <td class="fw-bold text-start">User Initiated Return</td>
            <td>{{ $loan->return_verified_at ? $loan->return_verified_at->format('d M Y, h:i A') : 'N/A' }}</td>
            <td>-</td>
          </tr>
          <tr>
            <td class="fw-bold text-start">Return Endorsed</td>
            <td>{{ $loan->endorsed_at ? $loan->endorsed_at->format('d M Y, h:i A') : 'N/A' }}</td>
            <td>{{ $loan->endorsedBy->name ?? 'N/A' }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    @if($loan->notes)
    <div class="mb-4">
      <label class="form-label">Notes</label>
      <div class="p-3 bg-light rounded border">
        {{ $loan->notes }}
      </div>
    </div>
    @endif

    <div class="mt-5 text-center text-muted" style="font-size: 11px;">
      <p>This is a computer generated receipt and requires no physical signature.<br>Generated on: {{ now()->format('d M Y, h:i A') }}</p>
    </div>
  </div>
</div>

<style>
@media print {
  body * {
    visibility: hidden;
  }
  .form-card, .form-card * {
    visibility: visible;
  }
  .form-card {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    box-shadow: none !important;
    border: none !important;
  }
  .topbar {
    display: none !important;
  }
}
</style>
@endsection
