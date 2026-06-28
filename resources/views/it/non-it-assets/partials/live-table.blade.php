@if($items->isEmpty())
<div style="background:var(--surface);border:1.5px dashed var(--border);border-radius:14px;padding:48px 20px;text-align:center">
  <i class="bi bi-search" style="font-size:32px;color:var(--muted);display:block;margin-bottom:12px;opacity:.4"></i>
  <div style="font-weight:700;font-size:15px;color:var(--text);margin-bottom:6px">No records match your filter</div>
  <a href="{{ route('it.non-it.index') }}" style="font-size:13px;color:var(--accent)">Clear filters</a>
</div>
@else
<div style="background:var(--surface);border:1px solid var(--border);border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.08),0 4px 16px rgba(0,0,0,.06)">
  <div style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
    <span style="font-size:13px;color:var(--muted);font-weight:500">
      <strong style="color:var(--text)">{{ number_format($items->count()) }}</strong> asset{{ $items->count() !== 1 ? 's' : '' }}
      @if($search || $class || $status)
        &nbsp;<span style="color:var(--accent)">(filtered)</span>
      @endif
    </span>
  </div>
  <div class="nit-scroll-wrap" style="overflow-x:auto">
    <table class="table table-hover nit-table" style="font-family:'Inter',sans-serif;min-width:100%">
      <thead><tr>
        <th style="width:40px"><input type="checkbox" id="nitSelectAll" style="cursor:pointer;accent-color:var(--accent);width:15px;height:15px"></th>
        <th>ASSET NO.</th>
        <th>F/A CODE</th>
        <th>DESCRIPTION</th>
        <th>YEARS PURCHASE</th>
        <th>LOCATION</th>
        <th>TOTAL COST</th>
        <th>ACCUMULATED</th>
        <th>NBV AT</th>
        <th style="width:1%;white-space:nowrap">QR</th>
        @if(!$user->isReadOnlyViewer())<th style="width:1%;white-space:nowrap">ACTIONS</th>@endif
      </tr></thead>
      <tbody>
      @foreach($items as $row)
      <tr>
        <td><input type="checkbox" class="nit-row-check" value="{{ $row->id }}"
          style="cursor:pointer;accent-color:var(--accent);width:15px;height:15px"></td>
        <td>
          @if($user->isAdminOrFinance())
          <a href="#" onclick="openNitEditFormById({{ $row->id }});return false"
            style="color:var(--accent);font-size:13px;font-weight:600;text-decoration:none">
            {{ $row->asset_number ?: '—' }}
          </a>
          @else
          <span style="color:var(--accent);font-size:13px;font-weight:600">{{ $row->asset_number ?: '—' }}</span>
          @endif
        </td>
        <td style="font-size:13px;color:var(--muted)">{{ $row->fa_code ?: '—' }}</td>
        <td style="font-weight:500;font-size:13px">{{ $row->description }}</td>
        <td style="font-size:13px;color:var(--muted)">{{ $row->years_purchase ?: '—' }}</td>
        <td style="font-size:13px;color:var(--muted)">{{ $row->location ?: '—' }}</td>
        <td style="font-size:13px;color:var(--muted)">{{ $row->total_cost !== null ? 'RM '.number_format((float)$row->total_cost,2) : '—' }}</td>
        <td style="font-size:13px;color:var(--muted)">{{ $row->accumulated !== null ? 'RM '.number_format((float)$row->accumulated,2) : '—' }}</td>
        <td style="font-size:13px;color:var(--muted)">{{ $row->nbv_at !== null ? 'RM '.number_format((float)$row->nbv_at,2) : '—' }}</td>
        <td>
          <button onclick="openNitQRModal({{ $row->id }}, '{{ addslashes(e($row->asset_number ?? 'N/A')) }}', '{{ addslashes(e($row->description)) }}', '{{ addslashes(e($row->fa_code ?? '')) }}', '{{ addslashes(e($row->location ?? '')) }}')"
            style="font-size:12px;color:#7c3aed;background:rgba(124,58,237,.1);border:none;border-radius:6px;padding:4px 7px;font-family:'Inter',sans-serif;cursor:pointer;display:inline-flex;align-items:center" title="View QR Code">
            <i class="bi bi-qr-code" style="font-size:13px"></i>
          </button>
        </td>
        @if(!$user->isReadOnlyViewer())
        <td>
          <div style="display:flex;align-items:center;gap:4px">
            @if(!in_array($row->item_status, ['Disposed', 'Pending for Write-Off', 'Pending to E-Waste/Disposal']))
            <a href="{{ route('it.writeoff.index') }}?nit_id={{ $row->id }}" title="Dispose"
              style="font-size:13px;color:#dc2626;background:rgba(220,38,38,.08);border:1px solid rgba(220,38,38,.2);border-radius:6px;padding:4px 7px;text-decoration:none;display:inline-flex;align-items:center;gap:4px">
              <i class="bi bi-trash3-fill"></i> Dispose
            </a>
            @endif
            @if($user->isAdminOrFinance())
            <a href="#" onclick="openNitEditFormById({{ $row->id }});return false" title="Edit"
              style="font-size:13px;color:var(--text);text-decoration:none;padding:4px 7px;border:1px solid var(--border);border-radius:6px;background:var(--surface);display:inline-flex;align-items:center;gap:4px">
              <i class="bi bi-pencil"></i> Edit
            </a>
            <form method="POST" action="{{ route('it.non-it.destroy', $row->id) }}" style="display:inline" onsubmit="return confirm('Delete this asset? This cannot be undone.')">
              @csrf
              @method('DELETE')
              <button type="submit" title="Delete"
                style="font-size:13px;color:#dc2626;background:rgba(239,68,68,.1);border:none;border-radius:6px;padding:4px 7px;display:inline-flex;align-items:center;cursor:pointer">
                <i class="bi bi-trash"></i>
              </button>
            </form>
            @else
            @if(isset($pendingEditIds[$row->id]))
            <span title="Edit Request Pending" style="font-size:13px;color:#d97706;background:rgba(245,158,11,.1);border-radius:6px;padding:4px 7px;display:inline-flex;align-items:center">
              <i class="bi bi-hourglass-split"></i>
            </span>
            @else
            <a href="#" onclick="openNitEditFormById({{ $row->id }});return false" title="Request Edit"
              style="font-size:13px;color:var(--text);text-decoration:none;padding:4px 7px;border:1px solid var(--border);border-radius:6px;background:var(--surface);display:inline-flex;align-items:center;gap:4px">
              <i class="bi bi-pencil"></i> Edit
            </a>
            @endif
            @endif
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

