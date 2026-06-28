<div class="table-card">
  <div style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
    <span style="font-size:13px;color:var(--muted);font-weight:500">
      <strong style="color:var(--text)">{{ number_format($items->total()) }}</strong> record{{ $items->total() !== 1 ? 's' : '' }}
      @if(request('ew_search') || request('ew_class') || request('ew_status'))
        &nbsp;<span style="color:var(--accent)">(filtered)</span>
      @endif
    </span>
  </div>
  <div>
    <table class="table table-hover data-table" style="font-family:'Inter',sans-serif">
      <thead><tr>
        @if($user->isAdminOrFinance())
        <th style="width:40px">
          <input type="checkbox" id="ewSelectAll" style="cursor:pointer;accent-color:var(--accent);width:15px;height:15px">
        </th>
        @endif
        <th>ASSET NO.</th><th>CLASS</th><th>DESCRIPTION</th>
        <th>SERIAL NO.</th><th>STATUS</th><th>DATE FLAGGED</th>
        @if($user->isAdminOrFinance())<th>ACTIONS</th>@endif
      </tr></thead>
      <tbody>
      @forelse($items as $item)
      <tr>
        @if($user->isAdminOrFinance())
        <td>
          <input type="checkbox" class="ew-row-check" value="{{ $item->id }}"
            data-status="{{ $item->disposal_status }}"
            style="cursor:pointer;accent-color:var(--accent);width:15px;height:15px">
        </td>
        @endif
        <td>
          <a href="#" onclick="openEwasteEdit({{ $item->id }});return false;"
            style="color:var(--accent);font-size:13px;font-weight:600;text-decoration:none;font-family:'Inter',sans-serif">
            {{ $item->asset_number ?: '—' }}
          </a>
        </td>
        <td>
          <span style="display:inline-block;background:rgba(59,130,246,.1);color:#2563eb;border-radius:5px;padding:2px 9px;font-size:11px;font-weight:700;letter-spacing:.04em;font-family:'Inter',sans-serif">
            {{ $item->asset_class }}
          </span>
        </td>
        <td style="font-weight:500;font-size:13px;font-family:'Inter',sans-serif">{{ $item->description }}</td>
        <td style="font-size:13px;font-family:'Inter',sans-serif;color:var(--muted)">{{ $item->serial_number ?: '—' }}</td>
        <td>
          @if($item->disposal_status === 'Collected')
            <span style="display:inline-flex;align-items:center;gap:5px;background:rgba(22,163,74,.1);color:#16a34a;border-radius:20px;padding:4px 12px;font-size:12px;font-weight:600;font-family:'Inter',sans-serif">
              <span style="width:6px;height:6px;background:#16a34a;border-radius:50%;display:inline-block"></span> Collected
            </span>
          @else
            <span style="display:inline-flex;align-items:center;gap:5px;background:rgba(245,158,11,.1);color:#d97706;border-radius:20px;padding:4px 12px;font-size:12px;font-weight:600;font-family:'Inter',sans-serif">
              <span style="width:6px;height:6px;background:#d97706;border-radius:50%;display:inline-block"></span> E-Waste
            </span>
          @endif
        </td>
        <td style="font-size:13px;font-family:'Inter',sans-serif">{{ $item->date_flagged ? $item->date_flagged->format('d/m/Y') : '—' }}</td>
        @if($user->isAdminOrFinance())
        <td>
          <div style="display:flex;align-items:center;gap:4px;flex-wrap:nowrap">
            @if($item->disposal_status === 'Approved')
              <form method="POST" action="{{ route('it.ewaste.collect', $item->id) }}" style="display:inline" onsubmit="return confirm('Mark this item as collected?')">
                @csrf
                <button type="submit"
                  style="font-size:11px;font-weight:700;color:#16a34a;background:rgba(22,163,74,.1);border:none;border-radius:6px;padding:4px 8px;cursor:pointer;white-space:nowrap;font-family:'Inter',sans-serif;display:inline-flex;align-items:center;gap:4px">
                  <i class="bi bi-truck" style="font-size:11px"></i> Collected
                </button>
              </form>
            @elseif(in_array($item->disposal_status, ['Collected','Disposed']))
              <form method="POST" action="{{ route('it.ewaste.restore', $item->id) }}" style="display:inline" onsubmit="return confirm('Revert this item back to Approved?')">
                @csrf
                <button type="submit"
                  style="font-size:11px;font-weight:700;color:#c2590a;border:none;border-radius:6px;padding:4px 8px;background:rgba(245,158,11,.1);cursor:pointer;white-space:nowrap;font-family:'Inter',sans-serif">&#x21A9; Undo</button>
              </form>
            @endif

            <button onclick="openEwasteEdit({{ $item->id }})"
              style="font-size:11px;font-weight:700;color:var(--text);white-space:nowrap;font-family:'Inter',sans-serif;padding:4px 8px;border:1px solid var(--border);border-radius:6px;background:var(--surface);cursor:pointer">Edit</button>

            @if(!in_array($item->disposal_status, ['Collected','Disposed']))
            <form method="POST" action="{{ route('it.ewaste.restore', $item->id) }}" style="display:inline" onsubmit="return confirm('Restore this item back to IT Assets?')">
              @csrf
              <button type="submit"
                style="font-size:11px;font-weight:700;color:#16a34a;border:none;background:rgba(22,163,74,.1);border-radius:6px;padding:4px 8px;cursor:pointer;white-space:nowrap;font-family:'Inter',sans-serif">Restore</button>
            </form>
            @endif

            <form method="POST" action="{{ route('it.ewaste.destroy', $item->id) }}" style="display:inline" onsubmit="return confirm('Delete this record?')">
              @csrf @method('DELETE')
              <button type="submit" title="Delete"
                style="font-size:13px;color:#dc2626;background:rgba(239,68,68,.1);border:none;border-radius:6px;padding:4px 7px;cursor:pointer;font-family:'Inter',sans-serif;display:inline-flex;align-items:center">
                <i class="bi bi-trash"></i>
              </button>
            </form>
          </div>
        </td>
        @endif
      </tr>
      @empty
      <tr><td colspan="{{ $user->isAdminOrFinance() ? 8 : 6 }}" style="text-align:center;padding:40px;color:var(--muted)">No e-waste items found.</td></tr>
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

