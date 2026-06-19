@if($items->isEmpty())
<div style="background:var(--surface);border:1.5px dashed var(--border);border-radius:14px;padding:48px 20px;text-align:center">
  <i class="bi bi-search" style="font-size:32px;color:var(--muted);display:block;margin-bottom:12px"></i>
  <div style="font-weight:700;font-size:15px;color:var(--text);margin-bottom:4px">No records match your filter</div>
  <a href="{{ route('it.disposal.index') }}" style="font-size:13px;color:var(--accent)">Clear filters</a>
</div>
@else
<style>.di-table td,.di-table th{vertical-align:middle!important;padding:8px 10px!important;white-space:nowrap}.di-table td:nth-child(3),.di-table th:nth-child(3){white-space:normal;word-break:break-word;min-width:130px}</style>
<div class="table-card">
  <div style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
    <span style="font-size:13px;color:var(--muted);font-weight:500">
      <strong style="color:var(--text)">{{ number_format($items->total()) }}</strong> record{{ $items->total() !== 1 ? 's' : '' }}
      @if($search || $class || $status)
        &nbsp;<span style="color:var(--accent)">(filtered)</span>
      @endif
    </span>
  </div>
  <div class="table-responsive">
    <table class="table table-hover di-table" style="font-family:'DM Sans',sans-serif;width:100%">
      <thead><tr>
        <th>ASSET NO.</th><th>CLASS</th><th>DESCRIPTION</th><th>SERIAL NO.</th>
        <th>STATUS</th><th>DATE FLAGGED</th><th>DATE DISPOSED</th><th>ADDED BY</th><th>ACTIONS</th>
      </tr></thead>
      <tbody>
      @foreach($items as $item)
      <tr>
        <td><span style="color:var(--accent);font-size:13px;font-weight:600">{{ $item->asset_number ?: '—' }}</span></td>
        <td><span style="display:inline-block;background:rgba(59,130,246,.1);color:#2563eb;border-radius:5px;padding:2px 9px;font-size:11px;font-weight:700;letter-spacing:.04em">{{ $item->asset_class }}</span></td>
        <td style="font-weight:500;font-size:13px">{{ $item->description }}</td>
        <td style="font-size:13px;color:var(--muted)">{{ $item->serial_number ?: '—' }}</td>
        <td>
          @if($item->disposal_status === 'Approved')
          <span style="display:inline-flex;align-items:center;gap:5px;background:rgba(22,163,74,.1);color:#16a34a;border-radius:20px;padding:4px 12px;font-size:12px;font-weight:600">
            <span style="width:6px;height:6px;background:#16a34a;border-radius:50%;display:inline-block"></span> Approved
          </span>
          @else
          <span style="display:inline-flex;align-items:center;gap:5px;background:rgba(239,68,68,.1);color:#dc2626;border-radius:20px;padding:4px 12px;font-size:12px;font-weight:600">
            <span style="width:6px;height:6px;background:#dc2626;border-radius:50%;display:inline-block"></span> Disposed
          </span>
          @endif
        </td>
        <td style="font-size:13px">{{ $item->date_flagged  ? $item->date_flagged->format('d/m/Y')  : '—' }}</td>
        <td style="font-size:13px">{{ $item->date_disposed ? $item->date_disposed->format('d/m/Y') : '—' }}</td>
        <td style="font-size:12px;color:var(--muted)">{{ $item->creator?->full_name ?? '—' }}</td>
        <td>
          <div style="display:flex;align-items:center;gap:4px;flex-wrap:nowrap">
            @if($user->isAdminOrFinance())
              @if($item->disposal_status === 'Approved')
              <form method="POST" action="{{ route('it.disposal.disposed', $item->id) }}" style="display:inline"
                    onsubmit="return confirm('Mark as Disposed?')">
                @csrf
                <button type="submit"
                  style="font-size:11px;font-weight:700;color:#dc2626;background:rgba(239,68,68,.1);border:none;border-radius:6px;padding:4px 8px;cursor:pointer;text-decoration:none;white-space:nowrap;display:inline-flex;align-items:center;gap:3px;font-family:'DM Sans',sans-serif">
                  <i class="bi bi-trash3" style="font-size:11px"></i> Dispose
                </button>
              </form>
              @else
              <form method="POST" action="{{ route('it.disposal.restore', $item->id) }}" style="display:inline"
                    onsubmit="return confirm('Revert to Approved?')">
                @csrf
                <button type="submit"
                  style="font-size:11px;font-weight:700;color:#c2590a;border:none;border-radius:6px;padding:4px 8px;background:rgba(245,158,11,.1);cursor:pointer;white-space:nowrap;font-family:'DM Sans',sans-serif">&#x21A9; Undo</button>
              </form>
              @endif
              <button onclick="openEditDisposal({{ $item->id }})"
                style="font-size:11px;font-weight:700;color:var(--text);white-space:nowrap;padding:4px 8px;border:1px solid var(--border);border-radius:6px;background:var(--surface);cursor:pointer;font-family:'DM Sans',sans-serif">
                <i class="bi bi-pencil"></i> Edit
              </button>
            @endif
            @if($user->isAdmin())
            <form method="POST" action="{{ route('it.disposal.destroy', $item->id) }}" style="display:inline"
                  onsubmit="return confirm('Delete this record?')">
              @csrf @method('DELETE')
              <button type="submit"
                style="font-size:13px;color:#dc2626;text-decoration:none;background:rgba(239,68,68,.1);border:none;border-radius:6px;padding:4px 7px;cursor:pointer;display:inline-flex;align-items:center;font-family:'DM Sans',sans-serif">
                <i class="bi bi-trash"></i>
              </button>
            </form>
            @endif
          </div>
        </td>
      </tr>
      @endforeach
      </tbody>
    </table>
  </div>
  @if($items->hasPages())
  <div style="padding:16px 20px;border-top:1px solid var(--border)">
    {{ $items->withQueryString()->links() }}
  </div>
  @endif
</div>
@endif

