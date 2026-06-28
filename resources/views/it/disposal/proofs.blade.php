@extends('it.layouts.app')

@section('title', 'Collected Proofs')
@section('page_title', 'Collected Proofs')

@section('content')
@php $user = auth('it')->user(); @endphp

<!-- PAGE HEADER -->
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px">
  <div>
    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:5px">
      Disposal &rsaquo; <span style="color:var(--accent)">Collected Proofs</span>
    </div>
    <h4 style="font-family:'Inter',sans-serif;font-weight:800;font-size:22px;color:var(--text);margin:0">Collected Proofs</h4>
    <p style="font-size:13px;color:var(--muted);margin:4px 0 0">Disposal items confirmed as physically disposed</p>
  </div>
  <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
    <a href="{{ route('it.disposal.index') }}" class="btn-secondary-custom" style="padding:8px 14px;font-size:12px">
      <i class="bi bi-arrow-left"></i> Back to Disposal Items
    </a>
  </div>
</div>

<!-- STAT STRIP -->
<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px;margin-bottom:24px">
  <div style="background:var(--surface);border:1px solid var(--border);border-left:4px solid #dc2626;border-radius:10px;padding:16px 20px;display:flex;align-items:center;gap:14px">
    <div style="width:42px;height:42px;border-radius:10px;background:rgba(239,68,68,.12);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0">
      <i class="bi bi-trash3" style="color:#dc2626"></i>
    </div>
    <div>
      <div style="font-size:26px;font-weight:800;color:var(--text);line-height:1">{{ $dpCount }}</div>
      <div style="font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-top:3px">Total Disposed</div>
    </div>
  </div>
  <div style="background:var(--surface);border:1px solid var(--border);border-left:4px solid #2563eb;border-radius:10px;padding:16px 20px;display:flex;align-items:center;gap:14px">
    <div style="width:42px;height:42px;border-radius:10px;background:rgba(37,99,235,.12);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0">
      <i class="bi bi-calendar-check" style="color:#2563eb"></i>
    </div>
    <div>
      <div style="font-size:26px;font-weight:800;color:var(--text);line-height:1">{{ $dpThisMonth }}</div>
      <div style="font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-top:3px">This Month</div>
    </div>
  </div>
</div>

<!-- TABLE CARD -->
@if($dpCount === 0)
<div style="background:var(--surface);border:1.5px dashed var(--border);border-radius:14px;padding:64px 20px;text-align:center">
  <i class="bi bi-trash3" style="font-size:36px;color:var(--muted);display:block;margin-bottom:14px"></i>
  <div style="font-weight:700;font-size:15px;color:var(--text);margin-bottom:4px">No disposed items yet</div>
  <div style="font-size:13px;color:var(--muted);margin-bottom:16px">Items marked as Disposed in Disposal Items will appear here</div>
  <a href="{{ route('it.disposal.index') }}" class="btn-primary-custom" style="font-size:13px">
    <i class="bi bi-box-arrow-right"></i> Go to Disposal Items
  </a>
</div>

@else
<div class="table-card">
  <div style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
    <div style="font-size:13px;color:var(--muted)">
      <strong style="color:var(--text)">{{ $dpCount }}</strong> disposed item{{ $dpCount !== 1 ? 's' : '' }}
    </div>
  </div>
  <div class="table-responsive">
    <table class="table table-hover" style="font-family:'Inter',sans-serif;margin:0">
      <thead><tr>
        <th style="width:36px">#</th>
        <th>Asset</th>
        <th>Serial No.</th>
        <th>Date Disposed</th>
        <th>Disposal Method</th>
        <th>Vendor / Collector</th>
        <th>Certificate No.</th>
        <th>Added By</th>
        @if($user->isAdmin())<th>Actions</th>@endif
      </tr></thead>
      <tbody>
      @foreach($items as $i => $item)
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
          @if($item->date_disposed)
          <div style="font-size:13px;font-weight:600;color:var(--text)">{{ $item->date_disposed->format('d M Y') }}</div>
          <div style="font-size:11px;color:var(--muted)">{{ $item->date_disposed->format('l') }}</div>
          @else
          <span style="color:var(--muted)">—</span>
          @endif
        </td>
        <td style="font-size:13px;color:var(--muted)">{{ $item->disposal_method ?: '—' }}</td>
        <td style="font-size:13px;color:var(--muted)">{{ $item->vendor_collector ?: '—' }}</td>
        <td>
          @if($item->certificate_number)
          <span style="display:inline-flex;align-items:center;gap:5px;background:rgba(37,99,235,.08);color:#2563eb;border:1px solid rgba(37,99,235,.2);border-radius:7px;padding:5px 12px;font-size:12px;font-weight:700">
            <i class="bi bi-file-earmark-text"></i> {{ $item->certificate_number }}
          </span>
          @else
          <span style="display:inline-flex;align-items:center;gap:5px;background:var(--body-bg);color:var(--muted);border:1px solid var(--border);border-radius:7px;padding:5px 12px;font-size:12px;font-weight:600">
            <i class="bi bi-dash"></i> None
          </span>
          @endif
        </td>
        <td style="font-size:13px;color:var(--muted)">{{ $item->creator?->full_name ?? '—' }}</td>
        @if($user->isAdmin())
        <td>
          <div style="display:flex;gap:6px;align-items:center">
            <form method="POST" action="{{ route('it.disposal.restore', $item->id) }}" style="margin:0"
                  onsubmit="return confirm('Revert this item back to Approved?')">
              @csrf
              <button type="submit"
                style="display:inline-flex;align-items:center;gap:4px;background:rgba(239,68,68,.07);color:#dc2626;border:1px solid rgba(239,68,68,.2);border-radius:6px;padding:5px 11px;font-size:11px;font-weight:700;cursor:pointer;font-family:'Inter',sans-serif">
                <i class="bi bi-arrow-counterclockwise"></i> Undo
              </button>
            </form>
          </div>
        </td>
        @endif
      </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif

@endsection

