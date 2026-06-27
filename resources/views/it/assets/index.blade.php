@extends('it.layouts.app')

@section('title', 'IT Assets')
@section('page_title', 'IT Assets')

@push('styles')
<style>
/* ── DATA TABLE ── */
.data-table { min-width:100%; }
.data-table td, .data-table th { vertical-align:middle !important; padding:5px 8px !important; white-space:nowrap; font-size:12.5px; }
/* Description column: allow wrapping so it absorbs spare space */
.data-table td:nth-child(4), .data-table th:nth-child(4) { white-space:normal; word-break:break-word; min-width:100px; }
/* Actions column: never wrap, just enough for the buttons */
.data-table td:last-child, .data-table th:last-child { white-space:nowrap; width:1%; }
.data-scroll-wrap::-webkit-scrollbar{height:6px}
.data-scroll-wrap::-webkit-scrollbar-track{background:var(--border);border-radius:3px}
.data-scroll-wrap::-webkit-scrollbar-thumb{background:var(--accent);border-radius:3px}
</style>
@endpush

@section('content')
@php $user = auth('it')->user(); @endphp

{{-- ── PENDING REQUESTS VIEW (admin) ── --}}
@if(request('view') === 'pending_requests' && $user->isAdmin())
  @include('it.inventory.partials.pending-requests', ['pendingItems' => $pendingItems ?? collect()])

{{-- ── MY REQUESTS VIEW (non-admin) ── --}}
@elseif(request('view') === 'my_requests' && !$user->isReadOnlyViewer())
  @include('it.inventory.partials.my-requests', ['myRequests' => $myRequestItems ?? collect()])

{{-- ── MAIN LIST VIEW ── --}}
@else

{{-- PAGE HEADER --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px">
  <div>
    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:5px">
      All Assets &rsaquo; <span style="color:var(--accent)">IT Assets</span>
    </div>
    <h4 style="font-family:'DM Sans',sans-serif;font-weight:800;font-size:22px;color:var(--text);margin:0">IT Assets</h4>
    <p style="font-size:13px;color:var(--muted);margin:4px 0 0">Manage and track all registered IT equipment</p>
  </div>
  <div style="display:flex;gap:8px;align-items:center">
    @if($user->isAdminOrFinance())
    <button onclick="document.getElementById('importModal').style.display='flex'"
      class="btn-secondary-custom" style="padding:10px 18px;font-size:13px;gap:7px">
      <i class="bi bi-file-earmark-excel-fill" style="color:#16a34a"></i> Import Excel
    </button>
    @endif
    @if(!$user->isReadOnlyViewer())
    <button class="btn-primary-custom" style="padding:10px 20px;font-size:13px" onclick="openAddModal()">
      <i class="bi bi-{{ $user->isAdminOrFinance() ? 'plus-lg' : 'send' }}"></i>
      {{ $user->isAdminOrFinance() ? 'Add Asset' : 'Request to Add' }}
    </button>
    @endif
  </div>
</div>

{{-- STAT STRIP --}}
@php
$statItems = [
  ['bi-box-seam-fill',    'rgba(2,132,199,.12)',  '#0284c7', $stats['total'] ?? 0,   'Total Assets',    '#0284c7'],
  ['bi-check-circle-fill','rgba(22,163,74,.12)',   '#16a34a', $stats['active'] ?? 0,  'Active',          '#16a34a'],
  ['bi-recycle',          'rgba(59,130,246,.12)',  '#2563eb', $stats['ewaste'] ?? 0,  'In E-Waste',      '#2563eb'],
  ['bi-hourglass-split',  'rgba(217,119,6,.12)',   '#d97706', $stats['pending'] ?? 0, 'Pending Approval','#d97706'],
];
@endphp
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px">
  @foreach($statItems as [$icon,$bg,$color,$val,$lbl,$border])
  <div style="background:var(--surface);border:1px solid var(--border);border-left:4px solid {{ $border }};border-radius:12px;padding:16px 20px;display:flex;align-items:center;gap:14px;box-shadow:0 1px 3px rgba(0,0,0,.07),0 4px 14px rgba(0,0,0,.05)">
    <div style="width:44px;height:44px;border-radius:10px;background:{{ $bg }};display:flex;align-items:center;justify-content:center;font-size:19px;flex-shrink:0">
      <i class="bi {{ $icon }}" style="color:{{ $color }}"></i>
    </div>
    <div>
      <div style="font-size:26px;font-weight:800;color:var(--text);line-height:1;font-family:'DM Sans',sans-serif">{{ $val }}</div>
      <div style="font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-top:3px">{{ $lbl }}</div>
    </div>
  </div>
  @endforeach
</div>

{{-- FILTER BAR --}}
@php
  $fSearch   = request('search', '');
  $fClass    = request('class', '');
  $fStatus   = request('status', '');
  $fLocation = request('location', '');
  $hasFilter = $fSearch || $fClass || $fStatus || $fLocation;
@endphp
<div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:16px">
  <form method="GET" action="{{ route('it.inventory.index') }}" id="filterForm" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;width:100%">
    <div style="position:relative;flex:1;min-width:220px">
      <i class="bi bi-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:13px"></i>
      <input type="text" name="search" value="{{ $fSearch }}" placeholder="Search asset no., class, description, serial..."
        style="width:100%;padding:9px 12px 9px 34px;background:var(--surface);border:1.5px solid var(--border);border-radius:8px;color:var(--text);font-size:13px;font-family:'DM Sans',sans-serif;outline:none"
        onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
    </div>
    <select name="class" onchange="this.form.submit()"
      style="padding:9px 14px;background:var(--surface);border:1.5px solid var(--border);border-radius:8px;color:var(--text);font-size:13px;font-family:'DM Sans',sans-serif;outline:none;min-width:140px">
      <option value="">All Classes</option>
      @foreach($assetClasses as $cls)
      <option value="{{ $cls->name }}" {{ $fClass === $cls->name ? 'selected' : '' }}>{{ $cls->name }}</option>
      @endforeach
    </select>
    <select name="status" onchange="this.form.submit()"
      style="padding:9px 14px;background:var(--surface);border:1.5px solid var(--border);border-radius:8px;color:var(--text);font-size:13px;font-family:'DM Sans',sans-serif;outline:none;min-width:130px">
      <option value="">All Status</option>
      <option value="Active"    {{ $fStatus === 'Active'    ? 'selected' : '' }}>Active</option>
      <option value="E-Waste"   {{ $fStatus === 'E-Waste'   ? 'selected' : '' }}>E-Waste</option>
      <option value="Pending"   {{ $fStatus === 'Pending'   ? 'selected' : '' }}>Pending</option>
      <option value="Collected" {{ $fStatus === 'Collected' ? 'selected' : '' }}>Collected</option>
    </select>
    <select name="location" onchange="this.form.submit()"
      style="padding:9px 14px;background:var(--surface);border:1.5px solid var(--border);border-radius:8px;color:var(--text);font-size:13px;font-family:'DM Sans',sans-serif;outline:none;min-width:140px">
      <option value="">All Locations</option>
      @foreach($locations ?? [] as $loc)
      <option value="{{ $loc }}" {{ $fLocation === $loc ? 'selected' : '' }}>{{ $loc }}</option>
      @endforeach
    </select>
    <button type="submit"
      style="padding:9px 20px;background:var(--navy,#142b47);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;white-space:nowrap;display:flex;align-items:center;gap:6px">
      <i class="bi bi-funnel-fill"></i> Filter
    </button>
    @if($hasFilter)
    <a href="{{ route('it.inventory.index') }}"
      style="padding:9px 16px;background:var(--surface);color:var(--muted);border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;white-space:nowrap;font-family:'DM Sans',sans-serif">
      Clear
    </a>
    @endif
  </form>
</div>

{{-- BULK ACTION BAR (hidden until items selected) --}}
@if(!$user->isReadOnlyViewer())
<div id="bulkBar" style="display:none;position:sticky;top:12px;z-index:100;margin-bottom:12px">
  <div style="background:#1A2332;color:#fff;border-radius:10px;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 4px 20px rgba(0,0,0,.3)">
    <span style="font-family:'DM Sans',sans-serif;font-weight:700;font-size:14px">
      <i class="bi bi-check2-square me-2"></i><span id="bulkCount">0</span> item(s) selected
    </span>
    <div style="display:flex;gap:8px">
      <form method="POST" action="{{ route('it.inventory.index') }}" id="bulkEwasteForm" style="display:inline">
        @csrf
        <input type="hidden" name="bulk_action" value="ewaste">
        <div id="ewaste_ids"></div>
        <button type="button" id="flagEwasteBtn" onclick="goToWriteoff()"
          style="background:#E7F6ED;color:#15803d;border:1px solid #bbf7d0;border-radius:7px;padding:7px 16px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:6px">
          <i class="bi bi-recycle"></i> Flag as E-Waste
        </button>
      </form>
      @if($user->isAdmin())
      <form method="POST" action="{{ route('it.inventory.index') }}" id="bulkDeleteForm" style="display:inline">
        @csrf
        <input type="hidden" name="bulk_action" value="delete">
        <div id="delete_ids"></div>
        <button type="button" onclick="submitBulk('delete')"
          style="background:#FDECEC;color:#dc2626;border:1px solid #fecaca;border-radius:7px;padding:7px 16px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:6px">
          <i class="bi bi-trash"></i> Delete
        </button>
      </form>
      @endif
      <button type="button" onclick="clearSelection()"
        style="background:rgba(255,255,255,.1);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:7px;padding:7px 14px;font-size:13px;cursor:pointer">
        <i class="bi bi-x"></i>
      </button>
    </div>
  </div>
</div>
@endif

{{-- TABLE --}}
<div style="background:var(--surface);border:1px solid var(--border);border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.08),0 4px 16px rgba(0,0,0,.06)">
  <div style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
    <span style="font-size:13px;color:var(--muted);font-weight:500">
      <strong style="color:var(--text)">{{ number_format($items->total()) }}</strong>
      record{{ $items->total() !== 1 ? 's' : '' }}
      @if($hasFilter)
        &nbsp;<span style="color:var(--accent)">(filtered)</span>
      @endif
    </span>
  </div>
  <div class="data-scroll-wrap" style="overflow-x:auto">
    <table class="table table-hover data-table" style="font-family:'DM Sans',sans-serif;min-width:100%">
      <thead><tr>
        <th style="width:40px"><input type="checkbox" id="selectAll" style="cursor:pointer;accent-color:var(--accent);width:15px;height:15px"></th>
        <th>ASSET NO.</th>
        <th>F/A CODE</th>
        <th>DESCRIPTION</th>
        <th>YEARS PURCHASE</th>
        <th>LOCATION</th>
        <th>TOTAL COST</th>
        <th>ACCUMULATED</th>
        <th>NBV AT</th>
        <th style="width:1%;white-space:nowrap">QR</th>
        <th style="width:1%;white-space:nowrap">ACTIONS</th>
      </tr></thead>
      <tbody>
        @forelse($items as $item)
        @php
          $displayStatus = $item->item_status;
          $ewStatus = $item->ew_status ?? null;
          if ($ewStatus === 'Collected') $displayStatus = 'Collected';
          elseif ($ewStatus === 'Pending')  $displayStatus = 'Pending';
          elseif ($ewStatus === 'Approved') $displayStatus = 'E-Waste';

          $isEwaste  = !empty($ewStatus) || in_array($item->item_status, ['Collected','Disposed']);
          $isLocked  = $user->isReadOnlyViewer() || (!$user->isAdmin() && $isEwaste);
        @endphp
        <tr>
          {{-- Checkbox --}}
          <td>
            <input type="checkbox" class="row-check" value="{{ $item->id }}"
              data-ewaste="{{ $isEwaste ? '1' : '0' }}"
              @if($isEwaste)
                disabled style="cursor:not-allowed;opacity:.3;width:15px;height:15px"
              @else
                style="cursor:pointer;accent-color:var(--accent);width:15px;height:15px"
              @endif
            >
          </td>

          {{-- Asset No. --}}
          <td>
            <a href="{{ route('it.inventory.index') }}?action=edit&id={{ $item->id }}"
              style="color:var(--accent);font-size:13px;font-weight:600;text-decoration:none;font-family:'DM Sans',sans-serif">
              {{ $item->asset_number ?: '—' }}
            </a>
          </td>

          {{-- F/A Code --}}
          <td style="font-size:13px;color:var(--muted);font-family:'DM Sans',sans-serif">{{ $item->fa_code ?: '—' }}</td>

          {{-- Description --}}
          <td style="font-weight:500;font-size:13px;font-family:'DM Sans',sans-serif">{{ $item->description }}</td>

          {{-- Years Purchase --}}
          <td style="font-size:13px;color:var(--muted);font-family:'DM Sans',sans-serif">{{ $item->years_purchase ?: '—' }}</td>

          {{-- Location --}}
          <td style="font-size:13px;font-family:'DM Sans',sans-serif">{{ $item->location ?: '—' }}</td>

          {{-- Total Cost --}}
          <td style="font-size:13px;color:var(--muted);font-family:'DM Sans',sans-serif">{{ $item->total_cost !== null ? 'RM '.number_format((float)$item->total_cost,2) : '—' }}</td>

          {{-- Accumulated --}}
          <td style="font-size:13px;color:var(--muted);font-family:'DM Sans',sans-serif">{{ $item->accumulated !== null ? 'RM '.number_format((float)$item->accumulated,2) : '—' }}</td>

          {{-- NBV At --}}
          <td style="font-size:13px;color:var(--muted);font-family:'DM Sans',sans-serif">{{ $item->nbv_at !== null ? 'RM '.number_format((float)$item->nbv_at,2) : '—' }}</td>

          {{-- QR Code --}}
          <td>
            <button
              onclick="openQRModal({{ $item->id }}, '{{ addslashes($item->asset_number ?: 'N/A') }}', '{{ addslashes($item->description) }}', '{{ addslashes($item->asset_class) }}', '{{ addslashes($item->serial_number ?: '') }}', '{{ addslashes($item->brand ?: '') }}', '{{ addslashes($item->model ?: '') }}', '{{ addslashes($item->location ?: '') }}')"
              style="font-size:12px;color:#7c3aed;background:rgba(124,58,237,.1);border:none;border-radius:6px;padding:4px 7px;font-family:'DM Sans',sans-serif;cursor:pointer;display:inline-flex;align-items:center" title="View QR Code">
              <i class="bi bi-qr-code" style="font-size:13px"></i>
            </button>
          </td>

          {{-- Actions --}}
          <td>
            <div style="display:flex;align-items:center;gap:4px;flex-wrap:nowrap">
              {{-- E-Waste button / status --}}
              @if($item->item_status !== 'Disposed' && $item->item_status !== 'Collected' && empty($ewStatus) && !$user->isReadOnlyViewer())
              <a href="{{ route('it.writeoff.index') }}?item_id={{ $item->id }}" title="Flag as E-Waste"
                style="font-size:13px;color:#16a34a;background:rgba(22,163,74,.1);border-radius:6px;padding:4px 7px;text-decoration:none;display:inline-flex;align-items:center;gap:4px">
                <i class="bi bi-recycle"></i> E-Waste
              </a>
              @elseif(!empty($ewStatus) && !$user->isReadOnlyViewer())
              <span style="font-size:13px;color:#d97706;background:rgba(245,158,11,.1);border-radius:6px;padding:4px 7px;display:inline-flex;align-items:center" title="@if($ewStatus==='Pending')Pending@elseif($ewStatus==='Approved')E-Waste@elseif($ewStatus==='Collected')Collected@else Disposed@endif">
                <i class="bi bi-@if($ewStatus==='Pending')hourglass-split@elseif($ewStatus==='Collected')check-circle-fill@else recycle@endif"></i>
              </span>
              @endif

              {{-- Edit / Request Edit --}}
              @if(!$isLocked)
                @if(!$user->isAdminOrFinance() && isset($pendingEditIds[$item->id]))
                  <span title="Edit Request Pending" style="font-size:13px;color:#d97706;background:rgba(245,158,11,.1);border-radius:6px;padding:4px 7px;display:inline-flex;align-items:center">
                    <i class="bi bi-hourglass-split"></i>
                  </span>
                @else
                  <a href="{{ route('it.inventory.index') }}?action=edit&id={{ $item->id }}"
                    title="{{ $user->isAdminOrFinance() ? 'Edit' : 'Request Edit' }}"
                    style="font-size:13px;color:var(--text);text-decoration:none;padding:4px 7px;border:1px solid var(--border);border-radius:6px;background:var(--surface);display:inline-flex;align-items:center;gap:4px">
                    <i class="bi bi-pencil"></i> Edit
                  </a>
                @endif

                {{-- Delete / Request Delete --}}
                @if($user->isAdmin())
                  <a href="{{ route('it.inventory.index') }}?action=delete&id={{ $item->id }}"
                    onclick="return confirm('Permanently delete this asset?')"
                    title="Delete Asset" style="font-size:13px;color:#dc2626;text-decoration:none;background:rgba(239,68,68,.1);border-radius:6px;padding:4px 7px;display:inline-flex;align-items:center">
                    <i class="bi bi-trash"></i>
                  </a>
                @else
                  @if(isset($pendingDeleteIds[$item->id]))
                    <span title="Delete Request Pending" style="font-size:13px;color:#d97706;background:rgba(245,158,11,.1);border-radius:6px;padding:4px 7px;white-space:nowrap;font-family:'DM Sans',sans-serif;display:inline-flex;align-items:center">
                      <i class="bi bi-hourglass-split"></i>
                    </span>
                  @else
                    <button onclick="openDeleteRequest({{ $item->id }}, '{{ addslashes($item->description) }}')"
                      title="Request Delete" style="font-size:13px;color:#dc2626;background:rgba(239,68,68,.1);border:none;border-radius:6px;padding:4px 7px;font-family:'DM Sans',sans-serif;cursor:pointer;display:inline-flex;align-items:center">
                      <i class="bi bi-trash"></i>
                    </button>
                  @endif
                @endif
              @else
                <span style="font-size:11px;color:var(--muted);font-style:italic;font-family:'DM Sans',sans-serif">—</span>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="11" style="text-align:center;padding:40px;color:var(--muted)">No assets found.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endif {{-- end main list view --}}

{{-- ── ADD ASSET MODAL ── --}}
<div id="addModal" style="display:none;position:fixed;inset:0;z-index:9000;background:rgba(0,0,0,.45);align-items:center;justify-content:center;padding:20px">
  <div style="background:var(--surface);border-radius:16px;width:100%;max-width:640px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.3)">
    <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center">
      <div style="font-size:15px;font-weight:700;color:var(--text)">
        @if($user->isAdminOrFinance()) Add IT Asset @else Request to Add Asset @endif
      </div>
      <button onclick="document.getElementById('addModal').style.display='none'" style="background:none;border:none;cursor:pointer;font-size:20px;color:var(--muted)">&times;</button>
    </div>
    <form method="POST" action="{{ route('it.inventory.store') }}">
      @csrf
      <div style="padding:24px;display:grid;grid-template-columns:1fr 1fr;gap:14px">
        <div style="grid-column:span 2">
          <label class="form-label">Description *</label>
          <input type="text" name="description" class="form-control" required>
        </div>
        <div>
          <label class="form-label">Asset Class *</label>
          <select name="asset_class" class="form-select" required>
            <option value="">Select class</option>
            @foreach($assetClasses as $cls)
            <option value="{{ $cls->name }}">{{ $cls->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="form-label">Asset Number</label>
          <input type="text" name="asset_number" class="form-control">
        </div>
        <div>
          <label class="form-label">FA Code</label>
          <input type="text" name="fa_code" class="form-control">
        </div>
        <div>
          <label class="form-label">Serial Number</label>
          <input type="text" name="serial_number" class="form-control">
        </div>
        <div>
          <label class="form-label">Brand</label>
          <input type="text" name="brand" class="form-control">
        </div>
        <div>
          <label class="form-label">Model</label>
          <input type="text" name="model" class="form-control">
        </div>
        <div>
          <label class="form-label">Location</label>
          <input type="text" name="location" class="form-control">
        </div>
        <div>
          <label class="form-label">Purchase Date</label>
          <input type="date" name="purchase_date" class="form-control">
        </div>
        <div>
          <label class="form-label">Purchase Price (RM)</label>
          <input type="number" step="0.01" name="purchase_price" class="form-control">
        </div>
        <div>
          <label class="form-label">Years of Purchase</label>
          <input type="number" name="years_purchase" class="form-control">
        </div>
        <div>
          <label class="form-label">Total Cost (RM)</label>
          <input type="number" step="0.01" name="total_cost" class="form-control">
        </div>
        <div>
          <label class="form-label">Accumulated (RM)</label>
          <input type="number" step="0.01" name="accumulated" class="form-control">
        </div>
        <div>
          <label class="form-label">NBV At (RM)</label>
          <input type="number" step="0.01" name="nbv_at" class="form-control">
        </div>
        <div>
          <label class="form-label">Condition</label>
          <select name="condition_status" class="form-select">
            <option value="Good">Good</option>
            <option value="Fair">Fair</option>
            <option value="Poor">Poor</option>
            <option value="Damaged">Damaged</option>
          </select>
        </div>
        <div style="grid-column:span 2">
          <label class="form-label">Notes</label>
          <textarea name="notes" class="form-control" rows="2"></textarea>
        </div>
      </div>
      <div style="padding:16px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:8px">
        <button type="button" onclick="document.getElementById('addModal').style.display='none'" class="btn-secondary-custom">Cancel</button>
        <button type="submit" class="btn-primary-custom">
          <i class="bi bi-check-lg"></i>
          @if($user->isAdminOrFinance()) Save Asset @else Submit Request @endif
        </button>
      </div>
    </form>
  </div>
</div>

{{-- ── IMPORT MODAL ── --}}
@if($user->isAdminOrFinance())
<div id="importModal" style="display:none;position:fixed;inset:0;z-index:9000;background:rgba(0,0,0,.45);align-items:center;justify-content:center;padding:20px">
  <div style="background:var(--surface);border-radius:16px;width:100%;max-width:480px;box-shadow:0 20px 60px rgba(0,0,0,.3)">
    <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center">
      <div style="font-size:15px;font-weight:700;color:var(--text)">Import Assets from Excel</div>
      <button onclick="document.getElementById('importModal').style.display='none'" style="background:none;border:none;cursor:pointer;font-size:20px;color:var(--muted)">&times;</button>
    </div>
    <div style="padding:24px">
      <div style="background:rgba(2,132,199,.06);border:1px solid rgba(2,132,199,.2);border-radius:10px;padding:14px 16px;font-size:13px;color:var(--text);margin-bottom:16px">
        <strong>Instructions:</strong> Download the template first, fill in your data, then upload the file. Supported: .xlsx, .xls, .csv
      </div>
      <label class="form-label">Select File</label>
      <input type="file" id="importFile" class="form-control" accept=".xlsx,.xls,.csv">
      <div id="importResult" style="margin-top:12px;display:none"></div>
    </div>
    <div style="padding:16px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:8px">
      <a href="{{ route('it.inventory.template') }}" class="btn-secondary-custom"><i class="bi bi-download"></i> Template</a>
      <button onclick="startImport()" class="btn-primary-custom"><i class="bi bi-upload"></i> Upload</button>
    </div>
  </div>
</div>
@endif

{{-- ── DELETE REQUEST MODAL (non-admin) ── --}}
@if(!$user->isAdmin())
<div id="deleteReqModal" style="display:none;position:fixed;inset:0;z-index:9000;background:rgba(0,0,0,.45);align-items:center;justify-content:center;padding:20px">
  <div style="background:var(--surface);border-radius:16px;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,.3)">
    <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center">
      <div style="font-size:15px;font-weight:700;color:var(--text)">Request Deletion</div>
      <button onclick="document.getElementById('deleteReqModal').style.display='none'" style="background:none;border:none;cursor:pointer;font-size:20px;color:var(--muted)">&times;</button>
    </div>
    <form method="POST" id="deleteReqForm" action="">
      @csrf
      <div style="padding:24px">
        <p id="deleteReqDesc" style="font-size:13px;color:var(--text);margin-bottom:16px"></p>
        <label class="form-label">Reason for deletion</label>
        <textarea name="reason" class="form-control" rows="3" placeholder="Provide a reason..."></textarea>
      </div>
      <div style="padding:16px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:8px">
        <button type="button" onclick="document.getElementById('deleteReqModal').style.display='none'" class="btn-secondary-custom">Cancel</button>
        <button type="submit" class="btn-primary-custom" style="background:#dc2626"><i class="bi bi-send"></i> Submit Request</button>
      </div>
    </form>
  </div>
</div>
@endif

{{-- ── QR CODE MODAL ── --}}
<div id="qrModal" style="display:none;position:fixed;inset:0;z-index:9100;background:rgba(0,0,0,.5);align-items:center;justify-content:center;padding:20px">
  <div style="background:var(--surface);border-radius:16px;width:100%;max-width:400px;box-shadow:0 20px 60px rgba(0,0,0,.3);overflow:hidden">
    <div style="padding:18px 22px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
      <div style="font-size:14px;font-weight:700;color:var(--text);display:flex;align-items:center;gap:8px">
        <i class="bi bi-qr-code" style="color:#7c3aed"></i> QR Code
      </div>
      <button onclick="document.getElementById('qrModal').style.display='none'" style="background:none;border:none;cursor:pointer;font-size:20px;color:var(--muted)">&times;</button>
    </div>
    <div style="padding:24px;text-align:center">
      <div id="qrCodeContainer" style="display:inline-block;padding:12px;background:#fff;border-radius:10px;border:1px solid var(--border);margin-bottom:16px"></div>
      <div id="qrAssetNo" style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:4px"></div>
      <div id="qrDescription" style="font-size:12px;color:var(--muted);margin-bottom:4px"></div>
      <div id="qrClass" style="display:inline-block;background:rgba(59,130,246,.1);color:#2563eb;border-radius:5px;padding:2px 8px;font-size:11px;font-weight:700;margin-bottom:16px"></div>
      <div style="display:flex;gap:8px;justify-content:center">
        <button onclick="printQR()" class="btn-secondary-custom"><i class="bi bi-printer"></i> Print</button>
        <button onclick="downloadQR()" class="btn-primary-custom"><i class="bi bi-download"></i> Download</button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.sheetjs.com/xlsx-0.20.0/package/dist/xlsx.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
// ── ADD MODAL ──
function openAddModal() {
  document.getElementById('addModal').style.display = 'flex';
}

// ── DELETE REQUEST MODAL (non-admin) ──
function openDeleteRequest(id, desc) {
  var form = document.getElementById('deleteReqForm');
  if (!form) return;
  form.action = '/inventory?action=request_delete&id=' + id;
  document.getElementById('deleteReqDesc').textContent = 'Request to delete: "' + desc + '"';
  document.getElementById('deleteReqModal').style.display = 'flex';
}

// ── PERSISTENT CROSS-PAGE SELECTION ──
const selectedIds = new Set();

function syncCheckboxes() {
  const rows      = document.querySelectorAll('tbody .row-check:not(:disabled)');
  const selectAll = document.querySelector('thead #selectAll');
  if (!selectAll) return;
  let checkedCount = 0;
  rows.forEach(cb => {
    cb.checked = selectedIds.has(cb.value);
    if (cb.checked) checkedCount++;
  });
  const total = rows.length;
  selectAll.checked       = total > 0 && checkedCount === total;
  selectAll.indeterminate = checkedCount > 0 && checkedCount < total;
}

document.addEventListener('change', function(e) {
  if (e.target.classList.contains('row-check') && !e.target.disabled) {
    if (e.target.checked) selectedIds.add(e.target.value);
    else selectedIds.delete(e.target.value);
    syncCheckboxes();
    updateBulkBar();
  }
});

document.addEventListener('change', function(e) {
  if (e.target.id === 'selectAll') {
    document.querySelectorAll('tbody .row-check:not(:disabled)').forEach(cb => {
      cb.checked = e.target.checked;
      if (e.target.checked) selectedIds.add(cb.value);
      else selectedIds.delete(cb.value);
    });
    updateBulkBar();
  }
});

window._onDtDraw = function() {
  syncCheckboxes();
  updateBulkBar();
};

function clearSelection() {
  selectedIds.clear();
  document.querySelectorAll('.row-check').forEach(cb => cb.checked = false);
  const sa = document.querySelector('#selectAll');
  if (sa) { sa.checked = false; sa.indeterminate = false; }
  updateBulkBar();
}

function updateBulkBar() {
  const bar   = document.getElementById('bulkBar');
  const count = document.getElementById('bulkCount');
  if (!bar) return;
  if (selectedIds.size > 0) {
    bar.style.display = 'block';
    if (count) count.textContent = selectedIds.size;
  } else {
    bar.style.display = 'none';
  }
}

function submitBulk(type) {
  if (selectedIds.size === 0) return;
  if (type === 'delete') {
    if (!confirm('Permanently delete ' + selectedIds.size + ' asset(s)? This cannot be undone.')) return;
    const form = document.getElementById('bulkDeleteForm');
    document.getElementById('delete_ids').innerHTML = '';
    selectedIds.forEach(id => {
      const input = document.createElement('input');
      input.type = 'hidden'; input.name = 'selected_ids[]'; input.value = id;
      document.getElementById('delete_ids').appendChild(input);
    });
    form.submit();
  }
}

function goToWriteoff() {
  if (selectedIds.size === 0) return;
  const ids = Array.from(selectedIds).join(',');
  window.location.href = '{{ route("it.writeoff.index") }}?bulk_ids=' + ids;
}

// ── QR CODE MODAL ──
var _qrInstance = null;
var _qrCurrentId = null;
var _qrAssetData = {};

function openQRModal(id, assetNo, desc, cls, serial, brand, model, location) {
  _qrAssetData = { id, assetNo, desc, cls, serial, brand, model, location };
  _qrCurrentId = id;
  const url = window.location.origin + '/asset/' + id;

  document.getElementById('qrAssetNo').textContent  = assetNo || ('Asset #' + id);
  document.getElementById('qrDescription').textContent = desc;
  document.getElementById('qrClass').textContent    = cls;

  const container = document.getElementById('qrCodeContainer');
  container.innerHTML = '';
  _qrInstance = new QRCode(container, {
    text: url, width: 180, height: 180,
    colorDark: '#142b47', colorLight: '#ffffff',
    correctLevel: QRCode.CorrectLevel.M
  });

  document.getElementById('qrModal').style.display = 'flex';
}

function printQR() {
  const canvas = document.querySelector('#qrCodeContainer canvas');
  if (!canvas) return;
  const w = window.open('', '_blank');
  w.document.write('<html><body style="text-align:center;padding:40px;font-family:sans-serif">');
  w.document.write('<img src="' + canvas.toDataURL() + '" style="width:200px;height:200px"><br>');
  w.document.write('<div style="margin-top:12px;font-size:16px;font-weight:700">' + (_qrAssetData.assetNo || '') + '</div>');
  w.document.write('<div style="font-size:13px;color:#666;margin-top:4px">' + (_qrAssetData.desc || '') + '</div>');
  w.document.write('</body></html>');
  w.document.close();
  w.focus();
  setTimeout(() => { w.print(); w.close(); }, 300);
}

function downloadQR() {
  const canvas = document.querySelector('#qrCodeContainer canvas');
  if (!canvas) return;
  const link = document.createElement('a');
  link.download = 'qr_' + (_qrAssetData.assetNo || _qrCurrentId) + '.png';
  link.href = canvas.toDataURL();
  link.click();
}

// Close QR modal on backdrop click
document.getElementById('qrModal').addEventListener('click', function(e) {
  if (e.target === this) this.style.display = 'none';
});

// ── IMPORT ──
function startImport() {
  var file = document.getElementById('importFile').files[0];
  if (!file) { alert('Please select a file first.'); return; }
  var reader = new FileReader();
  reader.onload = function(e) {
    var wb   = XLSX.read(e.target.result, { type: 'array', cellDates: true });
    var ws   = wb.Sheets[wb.SheetNames[0]];
    var rows = XLSX.utils.sheet_to_json(ws, { defval: '' });
    if (!rows.length) { alert('No data found.'); return; }
    var result = document.getElementById('importResult');
    result.style.display = 'block';
    result.innerHTML = '<div style="color:var(--muted);font-size:13px">Uploading ' + rows.length + ' rows...</div>';
    fetch('{{ route("it.inventory.import") }}', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
      body: JSON.stringify(rows)
    }).then(function(r){ return r.json(); }).then(function(d) {
      var color  = d.inserted > 0 ? '#166534' : '#991b1b';
      var bg     = d.inserted > 0 ? '#dcfce7' : '#fee2e2';
      var border = d.inserted > 0 ? '#bbf7d0' : '#fecaca';
      result.innerHTML = '<div style="background:'+bg+';border:1px solid '+border+';border-radius:8px;padding:12px 14px;font-size:13px;color:'+color+'">'
        + '<strong>' + d.inserted + ' assets imported</strong>, ' + d.skipped + ' skipped.'
        + (d.errors && d.errors.length ? '<div style="margin-top:8px">' + d.errors.slice(0,5).join('<br>') + '</div>' : '')
        + '</div>';
      if (d.inserted > 0) setTimeout(function(){ location.reload(); }, 1500);
    }).catch(function(){ result.innerHTML = '<div style="color:#991b1b;font-size:13px">Upload failed. Please try again.</div>'; });
  };
  reader.readAsArrayBuffer(file);
}
</script>
@endpush
