<div class="table-card">
  <div style="overflow-x:auto">
    <table class="table table-hover data-table" style="font-family:'Inter',sans-serif">
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
            style="color:var(--accent);font-size:13px;font-weight:600;text-decoration:none;font-family:'Inter',sans-serif">
            {{ $item->asset_number ?: '—' }}
          </a>
        </td>
        <td style="font-size:13px;font-family:'Inter',sans-serif">{{ $item->fa_code ?: '—' }}</td>
        <td style="font-weight:500;font-size:13px;font-family:'Inter',sans-serif">{{ $item->description }}</td>
        <td style="font-size:13px;font-family:'Inter',sans-serif">{{ $item->years_purchase ?: '—' }}</td>
        <td style="font-size:13px;font-family:'Inter',sans-serif">{{ $item->location ?: '—' }}</td>
        <td style="font-size:13px;font-family:'Inter',sans-serif">{{ $item->total_cost ? number_format($item->total_cost, 2) : '—' }}</td>
        <td style="font-size:13px;font-family:'Inter',sans-serif">{{ $item->accumulated ? number_format($item->accumulated, 2) : '—' }}</td>
        <td style="font-size:13px;font-family:'Inter',sans-serif">{{ $item->nbv_at ? number_format($item->nbv_at, 2) : '—' }}</td>
        <td style="width:1%;white-space:nowrap">
          <button onclick="openQRModal({{ $item->id }},{{ json_encode($item->asset_number) }},{{ json_encode($item->description) }},{{ json_encode($item->asset_class) }},{{ json_encode($item->serial_number) }},{{ json_encode($item->brand) }},{{ json_encode($item->model) }},{{ json_encode($item->location) }})"
            style="font-size:12px;color:#7c3aed;background:rgba(124,58,237,.1);border:none;border-radius:6px;padding:4px 7px;font-family:'Inter',sans-serif;cursor:pointer;display:inline-flex;align-items:center" title="View QR Code">
            <i class="bi bi-qr-code" style="font-size:13px"></i>
          </button>
        </td>
        <td style="width:1%;white-space:nowrap">
          <div style="display:flex;align-items:center;gap:4px;flex-wrap:nowrap">
            {{-- E-Waste button --}}
            @if(($display_status === 'Active' || $user->isAdmin()) && !$user->isReadOnlyViewer())
            <a href="{{ route('it.writeoff.index') }}?item_id={{ $item->id }}"
              style="font-size:11px;font-weight:700;color:#16a34a;background:rgba(22,163,74,.1);border:none;border-radius:6px;cursor:pointer;padding:4px 8px;white-space:nowrap;font-family:'Inter',sans-serif;text-decoration:none;display:inline-flex;align-items:center;gap:4px">
              <i class="bi bi-recycle" style="font-size:11px"></i> E-Waste
            </a>
            @endif
            {{-- Edit button --}}
            @if(!$user->isReadOnlyViewer() && ($display_status === 'Active' || $user->isAdmin()))
            <button onclick="openEditModal({{ $item->id }})"
              style="font-size:11px;font-weight:700;color:var(--text);text-decoration:none;white-space:nowrap;font-family:'Inter',sans-serif;padding:4px 8px;border:1px solid var(--border);border-radius:6px;background:var(--surface);cursor:pointer">
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
                style="font-size:13px;color:#dc2626;background:rgba(239,68,68,.1);border:none;border-radius:6px;padding:4px 7px;font-family:'Inter',sans-serif;cursor:pointer;display:inline-flex;align-items:center">
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

