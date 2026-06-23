@extends('it.layouts.app')

@section('title', 'IT Assets')
@section('page_title', 'IT Assets')

@push('styles')
<style>
/* Let DataTables handle table width — just style cells */
.data-table { width:100% !important; }
.data-table td, .data-table th { vertical-align:middle !important; padding:8px 10px !important; white-space:nowrap; }
/* Description column: allow wrapping so it absorbs spare space */
.data-table td:nth-child(4), .data-table th:nth-child(4) { white-space:normal; word-break:break-word; min-width:100px; }
/* Actions column: never wrap, just enough for the buttons */
.data-table td:last-child, .data-table th:last-child { white-space:nowrap; width:1%; }
</style>
@endpush

@section('content')
@php
  $user = auth('it')->user();
  $f_search   = request('search', '');
  $f_class    = request('class', '');
  $f_status   = request('status', '');
  $f_location = request('location', '');

  $stat_total   = \App\Models\IT\InventoryItem::count();
  $stat_active  = \App\Models\IT\InventoryItem::where('item_status', 'Active')->count();
  $stat_ewaste  = \App\Models\IT\EwasteItem::whereIn('disposal_status', ['Approved','Collected'])->count();
  $stat_pending = \App\Models\IT\EwasteItem::where('disposal_status', 'Pending')->count();

  $all_locations = \App\Models\IT\InventoryItem::whereNotNull('location')->where('location', '!=', '')->distinct()->orderBy('location')->pluck('location');
@endphp

@if(request('view') !== 'pending_requests')
<!-- PAGE HEADER -->
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
    <button onclick="openAddModal()" class="btn-primary-custom" style="padding:10px 20px;font-size:13px">
      <i class="bi bi-{{ $user->isAdminOrFinance() ? 'plus-lg' : 'send' }}"></i>
      {{ $user->isAdminOrFinance() ? 'Add Asset' : 'Request to Add' }}
    </button>
    @endif
  </div>
</div>

<!-- STAT STRIP -->
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px">
  @foreach([
    ['bi-box-seam-fill',    'rgba(2,132,199,.12)',  '#0284c7', $stat_total,   'Total Assets',    '#0284c7'],
    ['bi-check-circle-fill','rgba(22,163,74,.12)',   '#16a34a', $stat_active,  'Active',          '#16a34a'],
    ['bi-recycle',          'rgba(59,130,246,.12)',  '#2563eb', $stat_ewaste,  'In E-Waste',      '#2563eb'],
    ['bi-hourglass-split',  'rgba(217,119,6,.12)',   '#d97706', $stat_pending, 'Pending Approval','#d97706'],
  ] as [$icon,$bg,$color,$val,$lbl,$border])
  <div style="background:var(--surface);border:1px solid var(--border);border-left:4px solid {{ $border }};border-radius:12px;padding:16px 20px;display:flex;align-items:center;gap:14px;box-shadow:0 1px 3px rgba(0,0,0,.07),0 4px 14px rgba(0,0,0,.05)">
    <div style="width:44px;height:44px;border-radius:10px;background:{{ $bg }};display:flex;align-items:center;justify-content:center;font-size:19px;flex-shrink:0">
      <i class="bi {{ $icon }}" style="color:{{ $color }}"></i>
    </div>
    <div>
      <div style="font-size:26px;font-weight:800;color:var(--text);line-height:1;font-family:'DM Sans',sans-serif">{{ number_format($val) }}</div>
      <div style="font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-top:3px">{{ $lbl }}</div>
    </div>
  </div>
  @endforeach
</div>

<!-- FILTER BAR -->
<div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:16px">
  <form method="GET" action="{{ route('it.inventory.index') }}" id="filterForm" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;width:100%">
    <div style="position:relative;flex:1;min-width:220px">
      <i class="bi bi-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:13px;z-index:1"></i>
      <input type="text" id="mainSearchInput" name="search" value="{{ $f_search }}" placeholder="Search asset no., class, description, serial..."
        autocomplete="off"
        style="width:100%;padding:9px 12px 9px 34px;background:var(--surface);border:1.5px solid var(--border);border-radius:8px;color:var(--text);font-size:13px;font-family:'DM Sans',sans-serif;outline:none"
        onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
      <div id="mainSearchSuggestions" style="display:none;position:absolute;top:100%;left:0;right:0;z-index:1000;background:var(--surface);border:1.5px solid var(--accent);border-radius:8px;box-shadow:0 4px 16px rgba(0,0,0,.15);margin-top:3px;overflow:hidden"></div>
    </div>
    <select name="class" onchange="this.form.submit()"
      style="padding:9px 14px;background:var(--surface);border:1.5px solid var(--border);border-radius:8px;color:var(--text);font-size:13px;font-family:'DM Sans',sans-serif;outline:none;min-width:140px">
      <option value="">All Classes</option>
      @foreach($assetClasses as $cls)
      <option value="{{ $cls->name }}" {{ $f_class === $cls->name ? 'selected' : '' }}>{{ $cls->name }}</option>
      @endforeach
    </select>
    <select name="status" onchange="this.form.submit()"
      style="padding:9px 14px;background:var(--surface);border:1.5px solid var(--border);border-radius:8px;color:var(--text);font-size:13px;font-family:'DM Sans',sans-serif;outline:none;min-width:130px">
      <option value="">All Status</option>
      <option value="Active"                {{ $f_status === 'Active'                ? 'selected' : '' }}>Active</option>
      <option value="E-Waste"               {{ $f_status === 'E-Waste'               ? 'selected' : '' }}>E-Waste</option>
      <option value="Pending"               {{ $f_status === 'Pending'               ? 'selected' : '' }}>Pending</option>
      <option value="Collected"             {{ $f_status === 'Collected'             ? 'selected' : '' }}>Collected</option>
    </select>
    <select name="location" onchange="this.form.submit()"
      style="padding:9px 14px;background:var(--surface);border:1.5px solid var(--border);border-radius:8px;color:var(--text);font-size:13px;font-family:'DM Sans',sans-serif;outline:none;min-width:140px">
      <option value="">All Locations</option>
      @foreach($all_locations as $loc)
      <option value="{{ $loc }}" {{ $f_location === $loc ? 'selected' : '' }}>{{ $loc }}</option>
      @endforeach
    </select>
    <button type="submit"
      style="padding:9px 20px;background:var(--navy,#142b47);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;white-space:nowrap;display:flex;align-items:center;gap:6px">
      <i class="bi bi-funnel-fill"></i> Filter
    </button>
    @if($f_search || $f_class || $f_status || $f_location)
    <a href="{{ route('it.inventory.index') }}"
      style="padding:9px 16px;background:var(--surface);color:var(--muted);border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;white-space:nowrap;font-family:'DM Sans',sans-serif">
      Clear
    </a>
    @endif
  </form>
</div>

@endif {{-- end pending_requests guard --}}

@if(request('view') === 'pending_requests' && $user->isAdmin())
  @include('it.inventory.partials.pending-requests', compact('pendingAdds','pendingEw','pendingDeletes','pendingEdits','pendingAddCount','pendingEwCount','pendingDelCount','pendingEditCount','totalPending'))
@elseif(request('view') === 'my_requests' && !$user->isReadOnlyViewer())
  @include('it.inventory.partials.my-requests', compact('myAdds','myEw','myDeletes','myEdits','myDisposals','myPending','totalMy'))
@else

{{-- BULK ACTION BAR (hidden until items selected) --}}
@if(!$user->isReadOnlyViewer())
<div id="bulkBar" style="display:none;position:sticky;top:12px;z-index:100;margin-bottom:12px">
  <div style="background:#1A2332;color:#fff;border-radius:10px;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 4px 20px rgba(0,0,0,.3)">
    <span style="font-family:'DM Sans',sans-serif;font-weight:700;font-size:14px">
      <i class="bi bi-check2-square me-2"></i><span id="bulkCount">0</span> item(s) selected
    </span>
    <div style="display:flex;gap:8px">
      <button type="button" id="flagEwasteBtn" onclick="goToWriteoff()"
        style="background:#E7F6ED;color:#15803d;border:1px solid #bbf7d0;border-radius:7px;padding:7px 16px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:6px">
        <i class="bi bi-recycle"></i> Flag as E-Waste
      </button>
      @if($user->isAdmin())
      <form method="POST" action="{{ route('it.inventory.bulk-destroy') }}" id="bulkDeleteForm" style="display:inline">
        @csrf
        <input type="hidden" name="bulk_action" value="delete">
        <div id="delete_ids"></div>
        <button type="button" onclick="submitBulkDelete()"
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
<div id="inventoryLiveResults">
<div class="table-card">
  <div>
    <table class="table table-hover data-table" style="font-family:'DM Sans',sans-serif">
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
        <th>QR</th>
        <th>ACTIONS</th>
      </tr></thead>
      <tbody>
      @forelse($items as $item)
      @php
        $ew = $item->ewasteItems->sortByDesc('id')->first();
        if ($ew) {
            $display_status = match($ew->disposal_status) {
                'Pending'   => 'Pending E-Waste',
                'Approved'  => 'E-Waste',
                'Collected' => 'Collected',
                default     => $item->item_status,
            };
        } else {
            $display_status = $item->item_status;
        }
      @endphp
      <tr>
        <td>
          <input type="checkbox" class="row-check" value="{{ $item->id }}"
            {{ (!$user->isAdmin() && $display_status !== 'Active') ? 'disabled style="cursor:not-allowed;opacity:.3;width:15px;height:15px"' : 'style="cursor:pointer;accent-color:var(--accent);width:15px;height:15px"' }}>
        </td>
        <td>
          <a href="{{ route('it.asset.show', $item->id) }}"
            style="color:var(--accent);font-size:13px;font-weight:600;text-decoration:none;font-family:'DM Sans',sans-serif">
            {{ $item->asset_number ?: '—' }}
          </a>
        </td>
        <td style="font-size:13px;font-family:'DM Sans',sans-serif">{{ $item->fa_code ?: '—' }}</td>
        <td style="font-weight:500;font-size:13px;font-family:'DM Sans',sans-serif">{{ $item->description }}</td>
        <td style="font-size:13px;font-family:'DM Sans',sans-serif">{{ $item->years_purchase ?: '—' }}</td>
        <td style="font-size:13px;font-family:'DM Sans',sans-serif">{{ $item->location ?: '—' }}</td>
        <td style="font-size:13px;font-family:'DM Sans',sans-serif">{{ $item->total_cost ? number_format($item->total_cost, 2) : '—' }}</td>
        <td style="font-size:13px;font-family:'DM Sans',sans-serif">{{ $item->accumulated ? number_format($item->accumulated, 2) : '—' }}</td>
        <td style="font-size:13px;font-family:'DM Sans',sans-serif">{{ $item->nbv_at ? number_format($item->nbv_at, 2) : '—' }}</td>
        <td>
          <button onclick="openQRModal({{ $item->id }},{{ json_encode($item->asset_number) }},{{ json_encode($item->description) }},{{ json_encode($item->asset_class) }},{{ json_encode($item->serial_number) }},{{ json_encode($item->brand) }},{{ json_encode($item->model) }},{{ json_encode($item->location) }})"
            style="font-size:12px;color:#7c3aed;background:rgba(124,58,237,.1);border:none;border-radius:6px;padding:4px 7px;font-family:'DM Sans',sans-serif;cursor:pointer;display:inline-flex;align-items:center" title="View QR Code">
            <i class="bi bi-qr-code" style="font-size:13px"></i>
          </button>
        </td>
        <td>
          <div style="display:flex;align-items:center;gap:4px;flex-wrap:nowrap">
            {{-- E-Waste button --}}
            @if(($display_status === 'Active' || $user->isAdmin()) && !$user->isReadOnlyViewer())
            <a href="{{ route('it.writeoff.index') }}?item_id={{ $item->id }}"
              style="font-size:11px;font-weight:700;color:#16a34a;background:rgba(22,163,74,.1);border:none;border-radius:6px;cursor:pointer;padding:4px 8px;white-space:nowrap;font-family:'DM Sans',sans-serif;text-decoration:none;display:inline-flex;align-items:center;gap:4px">
              <i class="bi bi-recycle" style="font-size:11px"></i> E-Waste
            </a>
            @endif
            {{-- Edit button --}}
            @if(!$user->isReadOnlyViewer() && ($display_status === 'Active' || $user->isAdmin()))
            <button onclick="openEditModal({{ $item->id }})"
              style="font-size:11px;font-weight:700;color:var(--text);text-decoration:none;white-space:nowrap;font-family:'DM Sans',sans-serif;padding:4px 8px;border:1px solid var(--border);border-radius:6px;background:var(--surface);cursor:pointer">
              {{ $user->isAdminOrFinance() ? 'Edit' : 'Request Edit' }}
            </button>
            @endif
            {{-- Delete button --}}
            @if($user->isAdmin())
            <form method="POST" action="{{ route('it.inventory.destroy', $item->id) }}" style="display:inline"
                  onsubmit="return confirm('Delete asset &quot;{{ addslashes($item->description) }}&quot;? This cannot be undone.')">
              @csrf
              @method('DELETE')
              <button type="submit" title="Delete Asset"
                style="font-size:13px;color:#dc2626;text-decoration:none;background:rgba(239,68,68,.1);border:none;border-radius:6px;padding:4px 7px;display:inline-flex;align-items:center;cursor:pointer">
                <i class="bi bi-trash"></i>
              </button>
            </form>
            @elseif($display_status === 'Active' && !$user->isReadOnlyViewer())
            <form method="POST" action="{{ route('it.inventory.destroy', $item->id) }}" style="display:inline"
                  onsubmit="return confirm('Delete asset &quot;{{ addslashes($item->description) }}&quot;? This cannot be undone.')">
              @csrf
              @method('DELETE')
              <button type="submit" title="Request Delete"
                style="font-size:13px;color:#dc2626;background:rgba(239,68,68,.1);border:none;border-radius:6px;padding:4px 7px;font-family:'DM Sans',sans-serif;cursor:pointer;display:inline-flex;align-items:center">
                <i class="bi bi-trash"></i>
              </button>
            </form>
            @endif
          </div>
        </td>
      </tr>
      @empty
      <tr><td colspan="11" style="text-align:center;padding:40px;color:var(--muted)">No assets found.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
  @if($items->hasPages())
  <div style="padding:16px 20px;border-top:1px solid var(--border)">
    {{ $items->withQueryString()->links() }}
  </div>
  @endif
</div>
</div>{{-- #inventoryLiveResults --}}

@endif {{-- end main table view --}}

{{-- Add Modal --}}
<div id="addModal" style="display:none;position:fixed;inset:0;z-index:9000;background:rgba(0,0,0,.5);align-items:center;justify-content:center;padding:24px">
  <div style="background:#fff;border-radius:12px;width:100%;max-width:960px;max-height:92vh;overflow-y:auto;box-shadow:0 24px 64px rgba(0,0,0,.25);font-family:'DM Sans',sans-serif">

    {{-- Header band --}}
    <div style="background:#1e2d40;border-radius:12px 12px 0 0;padding:20px 28px;display:flex;align-items:center;justify-content:space-between">
      <div style="display:flex;align-items:center;gap:14px">
        <div style="width:40px;height:40px;background:rgba(255,255,255,.12);border-radius:8px;display:flex;align-items:center;justify-content:center">
          <i class="bi bi-box-seam" style="color:#fff;font-size:18px"></i>
        </div>
        <div>
          <div style="font-size:16px;font-weight:700;color:#fff;line-height:1.2">
            @if($user->isAdminOrFinance()) Register New IT Asset @else Request to Add Asset @endif
          </div>
          <div style="font-size:12px;color:rgba(255,255,255,.55);margin-top:2px">Fill in the details to register a new IT asset</div>
        </div>
      </div>
      <button onclick="document.getElementById('addModal').style.display='none'" style="background:rgba(255,255,255,.1);border:none;cursor:pointer;width:32px;height:32px;border-radius:6px;color:#fff;font-size:18px;display:flex;align-items:center;justify-content:center;line-height:1">&times;</button>
    </div>

    <form method="POST" action="{{ route('it.inventory.store') }}">
      @csrf

      {{-- Section: Asset Identity --}}
      <div style="padding:24px 28px">
        <div style="display:flex;align-items:center;gap:7px;margin-bottom:18px">
          <i class="bi bi-tag" style="font-size:13px;color:var(--muted)"></i>
          <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted)">Asset Identity</span>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:16px;margin-bottom:16px">
          <div>
            <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Asset Number</label>
            <input type="text" name="asset_number" placeholder="e.g. OEPC1401"
              style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'DM Sans',sans-serif;outline:none;box-sizing:border-box"
              onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
            <div style="font-size:11px;color:var(--muted);margin-top:4px">Leave blank to assign later</div>
          </div>
          <div>
            <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">F/A Code</label>
            <input type="text" name="fa_code" placeholder="e.g. 4100000047"
              style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'DM Sans',sans-serif;outline:none;box-sizing:border-box"
              onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
          </div>
          <div>
            <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Asset Class <span style="color:#e53e3e">*</span></label>
            <select name="asset_class" required
              style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'DM Sans',sans-serif;outline:none;box-sizing:border-box"
              onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
              <option value="">— Select Class —</option>
              @foreach($assetClasses as $cls)
              <option value="{{ $cls->name }}">{{ $cls->name }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Serial Number</label>
            <input type="text" name="serial_number" placeholder="e.g. SGH629QBBY"
              style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'DM Sans',sans-serif;outline:none;box-sizing:border-box"
              onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
          </div>
        </div>
        <div>
          <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Description <span style="color:#e53e3e">*</span></label>
          <div style="position:relative">
            <input type="text" id="addDescInput" name="description" required placeholder="e.g. HP ELITEONE 800 G2 23, AIO, NOTEBOOK..." autocomplete="off"
              style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'DM Sans',sans-serif;outline:none;box-sizing:border-box"
              onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
            <div id="descSuggestions" style="display:none;position:absolute;top:calc(100% + 4px);left:0;right:0;z-index:500;background:#fff;border:1.5px solid var(--border);border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);overflow:hidden;max-height:260px;overflow-y:auto"></div>
          </div>
          <div style="font-size:11px;color:#d97706;margin-top:4px;display:flex;align-items:center;gap:4px">
            <i class="bi bi-lightbulb" style="font-size:11px"></i> Type an abbreviation or existing name.
          </div>
        </div>
      </div>

      {{-- Section: Financial Details --}}
      <div style="padding:24px 28px;border-top:1px solid var(--border)">
        <div style="display:flex;align-items:center;gap:7px;margin-bottom:18px">
          <i class="bi bi-currency-dollar" style="font-size:13px;color:var(--muted)"></i>
          <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted)">Financial Details</span>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:16px">
          <div>
            <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Years Purchase</label>
            <input type="number" name="years_purchase" placeholder="e.g. 2017"
              style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'DM Sans',sans-serif;outline:none;box-sizing:border-box"
              onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
          </div>
          <div>
            <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Total Cost (RM)</label>
            <input type="number" step="0.01" name="total_cost" placeholder="0.00"
              style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'DM Sans',sans-serif;outline:none;box-sizing:border-box"
              onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
          </div>
          <div>
            <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Accumulated (RM)</label>
            <input type="number" step="0.01" name="accumulated" placeholder="0.00"
              style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'DM Sans',sans-serif;outline:none;box-sizing:border-box"
              onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
          </div>
          <div>
            <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">NBV AT (RM)</label>
            <input type="number" step="0.01" name="nbv_at" placeholder="0.00"
              style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'DM Sans',sans-serif;outline:none;box-sizing:border-box"
              onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
          </div>
        </div>
      </div>

      {{-- Section: Technical Details --}}
      <div style="padding:24px 28px;border-top:1px solid var(--border)">
        <div style="display:flex;align-items:center;gap:7px;margin-bottom:18px">
          <i class="bi bi-gear" style="font-size:13px;color:var(--muted)"></i>
          <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted)">Technical Details</span>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px">
          <div>
            <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Location</label>
            <input type="text" name="location" placeholder="e.g. Server Room 1"
              style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'DM Sans',sans-serif;outline:none;box-sizing:border-box"
              onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
          </div>
          <div>
            <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Brand</label>
            <div style="position:relative">
              <input type="text" id="addBrandInput" name="brand" placeholder="e.g. HP, LENOVO, DELL..." autocomplete="off"
                style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'DM Sans',sans-serif;outline:none;box-sizing:border-box"
                onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
              <div id="brandSuggestions" style="display:none;position:absolute;top:calc(100% + 4px);left:0;right:0;z-index:500;background:#fff;border:1.5px solid var(--border);border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);overflow:hidden;max-height:260px;overflow-y:auto"></div>
            </div>
          </div>
          <div>
            <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Model</label>
            <div style="position:relative">
              <input type="text" id="addModelInput" name="model" placeholder="e.g. ELITEONE 800 G2..." autocomplete="off"
                style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'DM Sans',sans-serif;outline:none;box-sizing:border-box"
                onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
              <div id="modelSuggestions" style="display:none;position:absolute;top:calc(100% + 4px);left:0;right:0;z-index:500;background:#fff;border:1.5px solid var(--border);border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);overflow:hidden;max-height:260px;overflow-y:auto"></div>
            </div>
          </div>
        </div>
      </div>

      {{-- Section: Notes --}}
      <div style="padding:24px 28px;border-top:1px solid var(--border)">
        <div style="display:flex;align-items:center;gap:7px;margin-bottom:18px">
          <i class="bi bi-journal-text" style="font-size:13px;color:var(--muted)"></i>
          <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted)">Notes</span>
        </div>
        <div>
          <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Notes</label>
          <textarea name="notes" rows="3" placeholder="Any additional remarks about this asset..."
            style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'DM Sans',sans-serif;outline:none;resize:vertical;box-sizing:border-box"
            onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'"></textarea>
        </div>
      </div>

      {{-- Footer --}}
      <div style="padding:16px 28px;border-top:1px solid var(--border);display:flex;align-items:center;gap:10px">
        <button type="submit"
          style="padding:10px 22px;background:#1e2d40;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;display:flex;align-items:center;gap:7px">
          <i class="bi bi-plus-lg"></i>
          @if($user->isAdminOrFinance()) Register Asset @else Submit Request @endif
        </button>
        <button type="button" onclick="document.getElementById('addModal').style.display='none'"
          style="padding:10px 20px;background:#fff;color:var(--text);border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;font-family:'DM Sans',sans-serif;display:flex;align-items:center;gap:6px">
          <i class="bi bi-x"></i> Cancel
        </button>
      </div>
    </form>
  </div>
</div>

{{-- Import Modal --}}
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

{{-- Edit overlay --}}
<div id="editOverlay" style="display:none;position:fixed;inset:0;z-index:9000;background:rgba(0,0,0,.45);align-items:center;justify-content:center;padding:20px"></div>

{{-- QR Code Modal --}}
<div id="qrModal" style="display:none;position:fixed;inset:0;z-index:9100;background:rgba(0,0,0,.5);align-items:center;justify-content:center;padding:20px">
  <div style="background:var(--surface);border-radius:16px;width:100%;max-width:380px;box-shadow:0 20px 60px rgba(0,0,0,.3);overflow:hidden;font-family:'DM Sans',sans-serif">
    <div style="padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
      <div style="font-size:14px;font-weight:700;color:var(--text);display:flex;align-items:center;gap:8px">
        <i class="bi bi-qr-code" style="color:#7c3aed"></i> QR Code
      </div>
      <button onclick="document.getElementById('qrModal').style.display='none'" style="background:none;border:none;cursor:pointer;font-size:20px;color:var(--muted);line-height:1">&times;</button>
    </div>
    <div style="padding:24px;text-align:center">
      <div id="qrCodeContainer" style="display:inline-block;padding:12px;background:#fff;border-radius:10px;border:1px solid var(--border);margin-bottom:16px"></div>
      <div id="qrAssetNo"    style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:4px"></div>
      <div id="qrDescription" style="font-size:12px;color:var(--muted);margin-bottom:6px"></div>
      <div id="qrClass"      style="display:inline-block;background:rgba(59,130,246,.1);color:#2563eb;border-radius:5px;padding:2px 8px;font-size:11px;font-weight:700;margin-bottom:16px"></div>
      <div style="display:flex;gap:8px;justify-content:center">
        <button onclick="printQR()"    class="btn-secondary-custom"><i class="bi bi-printer"></i> Print</button>
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
function openAddModal() { document.getElementById('addModal').style.display = 'flex'; }

var _editData = @json($items->keyBy('id'));

function openEditModal(id) {
  var d = _editData[id];
  if (!d) return;
  var isAdmin = {{ $user->isAdmin() ? 'true' : 'false' }};
  var isAdminOrFinance = {{ $user->isAdminOrFinance() ? 'true' : 'false' }};
  var btnLabel = isAdminOrFinance ? 'Save Changes' : 'Submit Edit Request';
  var html = '<div style="background:var(--surface);border-radius:16px;width:100%;max-width:640px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.3)">'
    + '<div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center">'
    + '<div style="font-size:15px;font-weight:700;color:var(--text)">' + (isAdminOrFinance ? 'Edit Asset' : 'Request to Edit Asset') + '</div>'
    + '<button onclick="document.getElementById(\'editOverlay\').style.display=\'none\'" style="background:none;border:none;cursor:pointer;font-size:20px;color:var(--muted)">&times;</button>'
    + '</div>'
    + '<form method="POST" action="/inventory/'+id+'">'
    + '<input type="hidden" name="_token" value="{{ csrf_token() }}">'
    + '<input type="hidden" name="_method" value="POST">'
    + '<div style="padding:24px;display:grid;grid-template-columns:1fr 1fr;gap:14px">'
    + field('Description *','description','text',d.description,'span 2')
    + field('Asset Class','asset_class','text',d.asset_class)
    + field('Asset Number','asset_number','text',d.asset_number)
    + field('FA Code','fa_code','text',d.fa_code)
    + field('Purchase Price (RM)','purchase_price','number',d.purchase_price)
    + field('Serial Number','serial_number','text',d.serial_number)
    + field('Brand','brand','text',d.brand)
    + field('Model','model','text',d.model)
    + field('Location','location','text',d.location)
    + field('Years of Purchase','years_purchase','number',d.years_purchase)
    + field('Total Cost (RM)','total_cost','number',d.total_cost)
    + field('Accumulated (RM)','accumulated','number',d.accumulated)
    + field('NBV At (RM)','nbv_at','number',d.nbv_at)
    + statusSel('condition_status','Condition',d.condition_status,['Good','Fair','Poor','For Disposal'])
    + (isAdminOrFinance ? statusSel('item_status','Status',d.item_status,['Active','In Repair','Disposed','Reserved','Collected']) : '')
    + '<div style="grid-column:span 2"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2">'+esc(d.notes||'')+'</textarea></div>'
    + '</div>'
    + '<div style="padding:16px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:8px">'
    + '<button type="button" onclick="document.getElementById(\'editOverlay\').style.display=\'none\'" class="btn-secondary-custom">Cancel</button>'
    + '<button type="submit" class="btn-primary-custom"><i class="bi bi-check-lg"></i> '+btnLabel+'</button>'
    + '</div></form></div>';
  document.getElementById('editOverlay').innerHTML = html;
  document.getElementById('editOverlay').style.display = 'flex';
}
function field(lbl,name,type,val,span) {
  var s = span ? 'grid-column:'+span+';' : '';
  return '<div style="'+s+'"><label class="form-label">'+lbl+'</label><input type="'+type+'" name="'+name+'" class="form-control" value="'+esc(val||'')+'"></div>';
}
function statusSel(name,lbl,val,opts) {
  return '<div><label class="form-label">'+lbl+'</label><select name="'+name+'" class="form-select">'
    + opts.map(function(o){ return '<option value="'+o+'" '+(val===o?'selected':'')+'>'+o+'</option>'; }).join('')
    + '</select></div>';
}
function esc(s){ return String(s).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;'); }

// ── PERSISTENT CROSS-PAGE SELECTION ──
const _SEL_KEY = 'fjb_sel_inventory';
const selectedIds = new Set(JSON.parse(sessionStorage.getItem(_SEL_KEY) || '[]'));
function _persistSel() { sessionStorage.setItem(_SEL_KEY, JSON.stringify([...selectedIds])); }

function syncCheckboxes() {
  const rows = document.querySelectorAll('tbody .row-check:not(:disabled)');
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
    _persistSel();
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
    _persistSel();
    updateBulkBar();
  }
});

window._onDtDraw = function() {
  syncCheckboxes();
  updateBulkBar();
};

function clearSelection() {
  selectedIds.clear();
  _persistSel();
  const selectAll = document.querySelector('thead #selectAll');
  if (selectAll) { selectAll.checked = false; selectAll.indeterminate = false; }
  document.querySelectorAll('tbody .row-check:not(:disabled)').forEach(cb => cb.checked = false);
  updateBulkBar();
}

function goToWriteoff() {
  if (!selectedIds.size) return;
  window.location.href = '{{ route("it.writeoff.index") }}?bulk_ids=' + Array.from(selectedIds).join(',');
}

function submitBulkDelete() {
  if (!selectedIds.size) return;
  if (!confirm('Permanently delete ' + selectedIds.size + ' selected asset(s)? This cannot be undone.')) return;
  const form = document.getElementById('bulkDeleteForm');
  const container = document.getElementById('delete_ids');
  container.innerHTML = '';
  selectedIds.forEach(id => {
    const inp = document.createElement('input');
    inp.type = 'hidden'; inp.name = 'selected_ids[]'; inp.value = id;
    container.appendChild(inp);
  });
  selectedIds.clear();
  _persistSel();
  form.submit();
}

function updateBulkBar() {
  const bar   = document.getElementById('bulkBar');
  if (!bar) return;
  const count = selectedIds.size;
  document.getElementById('bulkCount').textContent = count;
  bar.style.display = count > 0 ? 'block' : 'none';
}

function startImport() {
  var file = document.getElementById('importFile').files[0];
  if (!file) { alert('Please select a file first.'); return; }
  var reader = new FileReader();
  reader.onload = function(e) {
    var wb = XLSX.read(e.target.result, { type: 'array', cellDates: true });
    var ws = wb.Sheets[wb.SheetNames[0]];
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
      var color = d.inserted > 0 ? '#166534' : '#991b1b';
      var bg = d.inserted > 0 ? '#dcfce7' : '#fee2e2';
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

// ── QR CODE MODAL ──
var _qrInstance = null;
var _qrCurrentId = null;
var _qrAssetData = {};

function openQRModal(id, assetNo, desc, cls, serial, brand, model, location) {
  _qrAssetData = { id: id, assetNo: assetNo, desc: desc, cls: cls };
  _qrCurrentId = id;

  var sep = '------------------------------';
  var na  = function(v) { return (v && String(v).trim()) ? String(v).trim().toUpperCase() : 'N/A'; };

  // Plain text payload — iOS camera shows this as a TEXT card, not a browser link
  var text = 'FJB JOHOR BULKERS SDN BHD\n'
    + sep + '\n'
    + 'ASET NO: ' + na(assetNo)  + '\n'
    + 'DESC: '    + na(desc)     + '\n'
    + 'CLASS: '   + na(cls)      + '\n'
    + 'S/N: '     + na(serial)   + '\n'
    + 'BRAND: '   + na(brand)    + '\n'
    + 'MODEL: '   + na(model)    + '\n'
    + 'LOC: '     + na(location) + '\n'
    + sep + '\n'
    + 'FJB JOHOR BULKERS SDN BHD';

  document.getElementById('qrAssetNo').textContent     = assetNo || ('Asset #' + id);
  document.getElementById('qrDescription').textContent = desc    || '';
  document.getElementById('qrClass').textContent       = cls     || '';

  var container = document.getElementById('qrCodeContainer');
  container.innerHTML = '';
  _qrInstance = new QRCode(container, {
    text: text, width: 180, height: 180,
    colorDark: '#142b47', colorLight: '#ffffff',
    correctLevel: QRCode.CorrectLevel.M
  });

  document.getElementById('qrModal').style.display = 'flex';
}

function printQR() {
  var canvas = document.querySelector('#qrCodeContainer canvas');
  if (!canvas) return;
  var w = window.open('', '_blank');
  w.document.write('<html><body style="text-align:center;padding:40px;font-family:sans-serif">');
  w.document.write('<img src="' + canvas.toDataURL() + '" style="width:200px;height:200px"><br>');
  w.document.write('<div style="margin-top:12px;font-size:16px;font-weight:700">' + (_qrAssetData.assetNo || '') + '</div>');
  w.document.write('<div style="font-size:13px;color:#666;margin-top:4px">' + (_qrAssetData.desc || '') + '</div>');
  w.document.write('</body></html>');
  w.document.close(); w.focus();
  setTimeout(function() { w.print(); w.close(); }, 300);
}

function downloadQR() {
  var canvas = document.querySelector('#qrCodeContainer canvas');
  if (!canvas) return;
  var link = document.createElement('a');
  link.download = 'qr_' + (_qrAssetData.assetNo || _qrCurrentId) + '.png';
  link.href = canvas.toDataURL();
  link.click();
}

document.getElementById('qrModal').addEventListener('click', function(e) {
  if (e.target === this) this.style.display = 'none';
});

// ── AUTOCOMPLETE HELPER ──
function makeAutocomplete(inputId, boxId, url, minLen) {
  var inp = document.getElementById(inputId);
  var box = document.getElementById(boxId);
  if (!inp || !box) return;
  var timer;

  inp.addEventListener('input', function() {
    clearTimeout(timer);
    var q = this.value.trim();
    if (q.length < minLen) { box.style.display = 'none'; return; }
    timer = setTimeout(function() {
      fetch(url + '?q=' + encodeURIComponent(q))
        .then(function(r) { return r.json(); })
        .then(function(items) {
          if (!items.length) { box.style.display = 'none'; return; }
          box.innerHTML = items.map(function(d) {
            return '<div class="ac-item" style="padding:9px 14px;font-size:13px;cursor:pointer;font-family:\'DM Sans\',sans-serif;color:var(--text);border-bottom:1px solid var(--border)">' + String(d).replace(/</g,'&lt;') + '</div>';
          }).join('');
          var last = box.querySelector('.ac-item:last-child');
          if (last) last.style.borderBottom = 'none';
          box.style.display = 'block';
        })
        .catch(function() { box.style.display = 'none'; });
    }, 220);
  });

  box.addEventListener('click', function(e) {
    var item = e.target.closest('.ac-item');
    if (item) { inp.value = item.textContent; box.style.display = 'none'; }
  });
  box.addEventListener('mouseover', function(e) {
    var item = e.target.closest('.ac-item');
    if (item) item.style.background = '#f1f5f9';
  });
  box.addEventListener('mouseout', function(e) {
    var item = e.target.closest('.ac-item');
    if (item) item.style.background = '';
  });
  document.addEventListener('click', function(e) {
    if (!inp.contains(e.target) && !box.contains(e.target)) box.style.display = 'none';
  });
}

makeAutocomplete('addDescInput',  'descSuggestions',  '{{ route("it.inventory.descriptions") }}', 2);
makeAutocomplete('addBrandInput', 'brandSuggestions', '{{ route("it.inventory.brands") }}',        1);
makeAutocomplete('addModelInput', 'modelSuggestions', '{{ route("it.inventory.models") }}',        1);

// ── MAIN SEARCH AUTOCOMPLETE ──
(function() {
  var inp = document.getElementById('mainSearchInput');
  var box = document.getElementById('mainSearchSuggestions');
  if (!inp || !box) return;
  var timer, active = -1;

  inp.addEventListener('input', function() {
    clearTimeout(timer);
    active = -1;
    var q = this.value.trim();
    if (q.length < 2) { box.style.display = 'none'; return; }
    timer = setTimeout(function() {
      fetch('{{ route("it.inventory.search-suggestions") }}?q=' + encodeURIComponent(q))
        .then(function(r) { return r.json(); })
        .then(function(items) {
          if (!items.length) { box.style.display = 'none'; return; }
          box.innerHTML = items.map(function(item) {
            return '<div class="ms-item" data-value="' + encodeURIComponent(item.value) + '"'
              + ' style="padding:9px 14px;font-size:13px;cursor:pointer;font-family:\'DM Sans\',sans-serif;color:var(--text);border-bottom:1px solid var(--border)">'
              + String(item.label).replace(/</g, '&lt;') + '</div>';
          }).join('');
          var last = box.querySelector('.ms-item:last-child');
          if (last) last.style.borderBottom = 'none';
          box.style.display = 'block';
          box.querySelectorAll('.ms-item').forEach(function(el) {
            el.addEventListener('mouseenter', function() { setActive(Array.from(box.querySelectorAll('.ms-item')).indexOf(el)); });
            el.addEventListener('click', function() { selectItem(el); });
          });
        })
        .catch(function() { box.style.display = 'none'; });
    }, 220);
  });

  inp.addEventListener('keydown', function(e) {
    var items = box.querySelectorAll('.ms-item');
    if (!items.length) return;
    if (e.key === 'ArrowDown') { e.preventDefault(); setActive(Math.min(active + 1, items.length - 1)); }
    else if (e.key === 'ArrowUp') { e.preventDefault(); setActive(Math.max(active - 1, -1)); }
    else if (e.key === 'Enter' && active >= 0) { e.preventDefault(); selectItem(items[active]); }
    else if (e.key === 'Escape') { box.style.display = 'none'; active = -1; }
  });

  document.addEventListener('click', function(e) {
    if (!inp.contains(e.target) && !box.contains(e.target)) { box.style.display = 'none'; active = -1; }
  });

  function setActive(idx) {
    var items = box.querySelectorAll('.ms-item');
    items.forEach(function(el, i) { el.style.background = i === idx ? 'rgba(99,102,241,.1)' : ''; });
    active = idx;
  }

  function selectItem(el) {
    inp.value = decodeURIComponent(el.dataset.value);
    box.style.display = 'none';
    active = -1;
    document.getElementById('filterForm').submit();
  }
})();

// Live search
(function () {
  var input   = document.getElementById('mainSearchInput');
  var results = document.getElementById('inventoryLiveResults');
  var form    = document.getElementById('filterForm');
  if (!input || !results || !form) return;
  var timer;
  input.addEventListener('input', function () {
    clearTimeout(timer);
    timer = setTimeout(function () {
      var params = new URLSearchParams();
      params.set('search', input.value.trim());
      form.querySelectorAll('select[name]').forEach(function (sel) {
        if (sel.value) params.set(sel.name, sel.value);
      });
      params.set('partial', '1');
      results.style.opacity = '0.4';
      fetch('?' + params.toString())
        .then(function (r) { return r.text(); })
        .then(function (html) { results.innerHTML = html; results.style.opacity = '1'; })
        .catch(function () { results.style.opacity = '1'; });
    }, 400);
  });
})();
</script>
@endpush

