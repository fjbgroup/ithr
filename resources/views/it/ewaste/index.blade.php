@extends('it.layouts.app')

@section('title', 'E-Waste Management')
@section('page_title', 'E-Waste Management')

@section('content')
@php $user = auth('it')->user(); @endphp

<!-- PAGE HEADER -->
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px">
  <div>
    <h4 style="font-family:'DM Sans',sans-serif;font-weight:800;font-size:22px;color:var(--text);margin:0">E-Waste Management</h4>
    <p style="font-size:13px;color:var(--muted);margin:4px 0 0">Track and manage devices flagged for disposal</p>
  </div>
  @if($user->isAdminOrFinance())
  <button class="btn-primary-custom" style="padding:10px 20px;font-size:13px" onclick="document.getElementById('addEwasteModal').style.display='flex'">
    <i class="bi bi-plus-lg"></i> Add Item
  </button>
  @else
  <button class="btn-primary-custom" style="padding:10px 20px;font-size:13px" onclick="document.getElementById('addEwasteModal').style.display='flex'">
    <i class="bi bi-send"></i> Request to Add Item
  </button>
  @endif
</div>

<!-- FILTER BAR -->
<div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:16px">
  <form method="GET" action="{{ route('it.ewaste.index') }}" id="ewFilterForm" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;width:100%">
    <div style="position:relative;flex:1;min-width:220px">
      <i class="bi bi-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:13px;z-index:1"></i>
      <input type="text" id="ewSearchInput" name="ew_search" value="{{ request('ew_search') }}" placeholder="Search asset no., class, description, serial..."
        autocomplete="off"
        style="width:100%;padding:9px 12px 9px 34px;background:var(--surface);border:1.5px solid var(--border);border-radius:8px;color:var(--text);font-size:13px;font-family:'DM Sans',sans-serif;outline:none"
        onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
      <ul id="ewSearchDropdown" style="display:none;position:absolute;top:calc(100% + 4px);left:0;right:0;background:var(--surface);border:1.5px solid var(--border);border-radius:8px;margin:0;padding:4px 0;list-style:none;max-height:240px;overflow-y:auto;z-index:9999;box-shadow:0 8px 24px rgba(0,0,0,.15)"></ul>
    </div>
    <select name="ew_class" onchange="this.form.submit()"
      style="padding:9px 14px;background:var(--surface);border:1.5px solid var(--border);border-radius:8px;color:var(--text);font-size:13px;font-family:'DM Sans',sans-serif;outline:none;min-width:140px">
      <option value="">All Classes</option>
      @foreach($assetClasses as $cls)
      <option value="{{ $cls }}" {{ request('ew_class') == $cls ? 'selected' : '' }}>{{ $cls }}</option>
      @endforeach
    </select>
    <select name="ew_status" onchange="this.form.submit()"
      style="padding:9px 14px;background:var(--surface);border:1.5px solid var(--border);border-radius:8px;color:var(--text);font-size:13px;font-family:'DM Sans',sans-serif;outline:none;min-width:130px">
      <option value="">All Status</option>
      <option value="E-Waste"   {{ request('ew_status') == 'E-Waste'   ? 'selected' : '' }}>E-Waste</option>
      <option value="Collected" {{ request('ew_status') == 'Collected' ? 'selected' : '' }}>Collected</option>
    </select>
    <button type="submit"
      style="padding:9px 20px;background:var(--accent);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;white-space:nowrap;display:flex;align-items:center;gap:6px">
      <i class="bi bi-funnel-fill"></i> Filter
    </button>
    @if(request('ew_search') || request('ew_class') || request('ew_status'))
    <a href="{{ route('it.ewaste.index') }}"
      style="padding:9px 16px;background:var(--surface);color:var(--muted);border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;white-space:nowrap;font-family:'DM Sans',sans-serif">
      Clear
    </a>
    @endif
  </form>
</div>

@if($user->isAdminOrFinance())
<!-- BULK ACTION BAR -->
<div id="ewBulkBar" style="display:none;background:#1a2332;border-radius:10px;padding:12px 20px;align-items:center;justify-content:space-between;gap:12px;margin-bottom:8px">
  <span style="font-family:'DM Sans',sans-serif;font-weight:700;font-size:14px;color:#fff;display:flex;align-items:center;gap:8px">
    <i class="bi bi-check2-square"></i>
    <span id="ewBulkCount">0</span> item(s) selected
  </span>
  <div style="display:flex;gap:8px;align-items:center">
    <div style="position:relative" id="ewActionDropdownWrap">
      <button type="button" onclick="toggleEwDropdown()"
        style="display:flex;align-items:center;gap:10px;padding:7px 14px;background:rgba(255,255,255,.1);color:#fff;border:1px solid rgba(255,255,255,.25);border-radius:7px;font-size:13px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;min-width:200px;justify-content:space-between">
        <span id="ewActionLabel">— Choose Action —</span>
        <i class="bi bi-chevron-down" style="font-size:11px"></i>
      </button>
      <div id="ewActionMenu"
        style="display:none;position:absolute;right:0;top:calc(100% + 6px);background:#fff;border:1px solid #e4e8ef;border-radius:9px;overflow:hidden;min-width:220px;box-shadow:0 8px 24px rgba(0,0,0,.18);z-index:99999">
        <div id="ewOptCollectDiv" onclick="selectEwAction('bulk_collect','✓ Mark as Collected')"
          style="padding:11px 16px;font-size:13px;font-weight:600;color:#15803d;cursor:pointer;display:flex;align-items:center;gap:8px;transition:background .1s"
          onmouseover="this.style.background='#f0fdf4'" onmouseout="this.style.background=''">
          <i class="bi bi-truck"></i> Mark as Collected
        </div>
        <div id="ewOptCollect" style="height:1px;background:#f0f2f5"></div>
        <div onclick="selectEwAction('bulk_restore','↩ Restore to IT Assets')"
          style="padding:11px 16px;font-size:13px;font-weight:600;color:#92400e;cursor:pointer;display:flex;align-items:center;gap:8px;transition:background .1s"
          onmouseover="this.style.background='#fff7ed'" onmouseout="this.style.background=''">
          <i class="bi bi-arrow-counterclockwise"></i> Restore to IT Assets
        </div>
        <div style="height:1px;background:#f0f2f5"></div>
        <div onclick="selectEwAction('bulk_delete','✕ Delete')"
          style="padding:11px 16px;font-size:13px;font-weight:600;color:#dc2626;cursor:pointer;display:flex;align-items:center;gap:8px;transition:background .1s"
          onmouseover="this.style.background='#fff1f2'" onmouseout="this.style.background=''">
          <i class="bi bi-trash-fill"></i> Delete
        </div>
      </div>
    </div>
    <input type="hidden" id="ewBulkActionValue" value="">
    <button type="button" onclick="applyEwBulk()"
      style="background:var(--accent);color:#fff;border:none;border-radius:7px;padding:7px 18px;font-size:13px;font-weight:700;cursor:pointer;font-family:'DM Sans',sans-serif">
      Apply
    </button>
    <button type="button" onclick="clearEwSelection()"
      style="background:rgba(255,255,255,.1);color:#fff;border:1px solid rgba(255,255,255,.2);border-radius:7px;padding:7px 12px;font-size:13px;cursor:pointer">
      <i class="bi bi-x"></i>
    </button>
  </div>
</div>
@endif

<style>
.data-table { width:100% !important; }
.data-table td, .data-table th { vertical-align:middle !important; padding:8px 10px !important; white-space:nowrap; }
.data-table td:nth-child({{ $user->isAdminOrFinance() ? '4' : '3' }}),
.data-table th:nth-child({{ $user->isAdminOrFinance() ? '4' : '3' }}) { white-space:normal; word-break:break-word; min-width:100px; }
.data-table td:last-child, .data-table th:last-child { white-space:nowrap; width:1%; }
</style>

<div id="ewLiveResults">
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
    <table class="table table-hover data-table" style="font-family:'DM Sans',sans-serif">
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
            style="color:var(--accent);font-size:13px;font-weight:600;text-decoration:none;font-family:'DM Sans',sans-serif">
            {{ $item->asset_number ?: '—' }}
          </a>
        </td>
        <td>
          <span style="display:inline-block;background:rgba(59,130,246,.1);color:#2563eb;border-radius:5px;padding:2px 9px;font-size:11px;font-weight:700;letter-spacing:.04em;font-family:'DM Sans',sans-serif">
            {{ $item->asset_class }}
          </span>
        </td>
        <td style="font-weight:500;font-size:13px;font-family:'DM Sans',sans-serif">{{ $item->description }}</td>
        <td style="font-size:13px;font-family:'DM Sans',sans-serif;color:var(--muted)">{{ $item->serial_number ?: '—' }}</td>
        <td>
          @if($item->disposal_status === 'Collected')
            <span style="display:inline-flex;align-items:center;gap:5px;background:rgba(22,163,74,.1);color:#16a34a;border-radius:20px;padding:4px 12px;font-size:12px;font-weight:600;font-family:'DM Sans',sans-serif">
              <span style="width:6px;height:6px;background:#16a34a;border-radius:50%;display:inline-block"></span> Collected
            </span>
          @else
            <span style="display:inline-flex;align-items:center;gap:5px;background:rgba(245,158,11,.1);color:#d97706;border-radius:20px;padding:4px 12px;font-size:12px;font-weight:600;font-family:'DM Sans',sans-serif">
              <span style="width:6px;height:6px;background:#d97706;border-radius:50%;display:inline-block"></span> E-Waste
            </span>
          @endif
        </td>
        <td style="font-size:13px;font-family:'DM Sans',sans-serif">{{ $item->date_flagged ? $item->date_flagged->format('d/m/Y') : '—' }}</td>
        @if($user->isAdminOrFinance())
        <td>
          <div style="display:flex;align-items:center;gap:4px;flex-wrap:nowrap">
            @if($item->disposal_status === 'Approved')
              <form method="POST" action="{{ route('it.ewaste.collect', $item->id) }}" style="display:inline" onsubmit="return confirm('Mark this item as collected?')">
                @csrf
                <button type="submit"
                  style="font-size:11px;font-weight:700;color:#16a34a;background:rgba(22,163,74,.1);border:none;border-radius:6px;padding:4px 8px;cursor:pointer;white-space:nowrap;font-family:'DM Sans',sans-serif;display:inline-flex;align-items:center;gap:4px">
                  <i class="bi bi-truck" style="font-size:11px"></i> Collected
                </button>
              </form>
            @elseif(in_array($item->disposal_status, ['Collected','Disposed']))
              <form method="POST" action="{{ route('it.ewaste.restore', $item->id) }}" style="display:inline" onsubmit="return confirm('Revert this item back to Approved?')">
                @csrf
                <button type="submit"
                  style="font-size:11px;font-weight:700;color:#c2590a;border:none;border-radius:6px;padding:4px 8px;background:rgba(245,158,11,.1);cursor:pointer;white-space:nowrap;font-family:'DM Sans',sans-serif">&#x21A9; Undo</button>
              </form>
            @endif

            <button onclick="openEwasteEdit({{ $item->id }})"
              style="font-size:11px;font-weight:700;color:var(--text);white-space:nowrap;font-family:'DM Sans',sans-serif;padding:4px 8px;border:1px solid var(--border);border-radius:6px;background:var(--surface);cursor:pointer">Edit</button>

            @if(!in_array($item->disposal_status, ['Collected','Disposed']))
            <form method="POST" action="{{ route('it.ewaste.restore', $item->id) }}" style="display:inline" onsubmit="return confirm('Restore this item back to IT Assets?')">
              @csrf
              <button type="submit"
                style="font-size:11px;font-weight:700;color:#16a34a;border:none;background:rgba(22,163,74,.1);border-radius:6px;padding:4px 8px;cursor:pointer;white-space:nowrap;font-family:'DM Sans',sans-serif">Restore</button>
            </form>
            @endif

            <form method="POST" action="{{ route('it.ewaste.destroy', $item->id) }}" style="display:inline" onsubmit="return confirm('Delete this record?')">
              @csrf @method('DELETE')
              <button type="submit" title="Delete"
                style="font-size:13px;color:#dc2626;background:rgba(239,68,68,.1);border:none;border-radius:6px;padding:4px 7px;cursor:pointer;font-family:'DM Sans',sans-serif;display:inline-flex;align-items:center">
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
</div>{{-- #ewLiveResults --}}

{{-- Add E-Waste Modal --}}
<div id="addEwasteModal" style="display:none;position:fixed;inset:0;z-index:9000;background:rgba(0,0,0,.45);align-items:center;justify-content:center;padding:20px">
  <div style="background:var(--surface);border-radius:16px;width:100%;max-width:620px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.3)">
    <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center">
      <div style="font-size:15px;font-weight:700;color:var(--text)">
        <i class="bi bi-recycle me-2" style="color:#16a34a"></i>
        {{ $user->isAdminOrFinance() ? 'Add E-Waste Item' : 'Request to Add E-Waste Item' }}
      </div>
      <button onclick="document.getElementById('addEwasteModal').style.display='none'" style="background:none;border:none;cursor:pointer;font-size:20px;color:var(--muted)">&times;</button>
    </div>
    @if(!$user->isAdminOrFinance())
    <div style="margin:16px 24px 0;background:rgba(37,99,235,.07);border:1px solid rgba(37,99,235,.2);border-radius:10px;padding:12px 16px;display:flex;align-items:center;gap:10px">
      <i class="bi bi-info-circle-fill" style="color:#2563eb;font-size:16px;flex-shrink:0"></i>
      <span style="font-size:13px;color:#1d4ed8;font-weight:500">Your request will be reviewed by admin before the item appears in E-Waste.</span>
    </div>
    @endif
    <form method="POST" action="{{ route('it.ewaste.store') }}">
      @csrf
      <div style="padding:24px" class="row g-3">
        <div class="col-md-3">
          <label class="form-label">Asset Number</label>
          <input type="text" name="asset_number" class="form-control">
        </div>
        <div class="col-md-3">
          <label class="form-label">Asset Class <span style="color:var(--red)">*</span></label>
          <select name="asset_class" class="form-select" required>
            @foreach($assetClasses as $cls)
            <option value="{{ $cls }}">{{ $cls }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Description <span style="color:var(--red)">*</span></label>
          <input type="text" name="description" class="form-control" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Serial Number</label>
          <input type="text" name="serial_number" class="form-control">
        </div>
        <div class="col-md-3">
          <label class="form-label">Date Flagged</label>
          <input type="date" name="date_flagged" class="form-control" value="{{ date('Y-m-d') }}">
        </div>
        <div class="col-md-3">
          <label class="form-label">Weight (kg)</label>
          <input type="number" name="weight_kg" step="0.01" class="form-control">
        </div>
        <div class="col-12">
          <label class="form-label">Notes</label>
          <textarea name="notes" class="form-control" rows="2"></textarea>
        </div>
      </div>
      <div style="padding:16px 24px;border-top:1px solid var(--border);display:flex;gap:8px">
        <button type="submit" class="btn-primary-custom">
          <i class="bi bi-{{ $user->isAdminOrFinance() ? 'check-lg' : 'send' }}"></i>
          {{ $user->isAdminOrFinance() ? 'Add Record' : 'Submit Request' }}
        </button>
        <button type="button" onclick="document.getElementById('addEwasteModal').style.display='none'" class="btn-secondary-custom">
          <i class="bi bi-x"></i> Cancel
        </button>
      </div>
    </form>
  </div>
</div>

{{-- Edit overlay (populated by JS) --}}
<div id="ewasteEditOverlay" style="display:none;position:fixed;inset:0;z-index:9000;background:rgba(0,0,0,.45);align-items:center;justify-content:center;padding:20px"></div>

@endsection

@push('scripts')
<style>
#ewSearchDropdown li {
  padding: 8px 14px;
  font-size: 13px;
  font-family: 'DM Sans', sans-serif;
  color: var(--text);
  cursor: pointer;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
#ewSearchDropdown li:hover,
#ewSearchDropdown li.ac-active {
  background: rgba(59,130,246,.08);
  color: var(--accent);
}
</style>
<script>
(function () {
  var input    = document.getElementById('ewSearchInput');
  var dropdown = document.getElementById('ewSearchDropdown');
  var form     = document.getElementById('ewFilterForm');
  var timer;

  function closeDropdown() { dropdown.style.display = 'none'; }

  function highlight(q, text) {
    var idx = text.toLowerCase().indexOf(q.toLowerCase());
    if (idx === -1) return escHtml(text);
    return escHtml(text.slice(0, idx))
      + '<strong style="color:var(--accent)">' + escHtml(text.slice(idx, idx + q.length)) + '</strong>'
      + escHtml(text.slice(idx + q.length));
  }

  function escHtml(s) {
    return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  }

  function renderItems(data, q) {
    if (!data.length) { closeDropdown(); return; }
    dropdown.innerHTML = data.map(function(item) {
      return '<li data-value="' + item.replace(/"/g, '&quot;') + '">' + highlight(q, item) + '</li>';
    }).join('');
    dropdown.style.display = 'block';
  }

  input.addEventListener('input', function () {
    clearTimeout(timer);
    var q = this.value.trim();
    if (q.length < 2) { closeDropdown(); return; }
    timer = setTimeout(function () {
      fetch('{{ route("it.ewaste.autocomplete") }}?q=' + encodeURIComponent(q))
        .then(function(r) { return r.json(); })
        .then(function(data) { renderItems(data, q); })
        .catch(function() { closeDropdown(); });
    }, 220);
  });

  dropdown.addEventListener('mousedown', function (e) {
    var li = e.target.closest('li');
    if (!li) return;
    e.preventDefault();
    input.value = li.getAttribute('data-value');
    closeDropdown();
    form.submit();
  });

  input.addEventListener('keydown', function (e) {
    var items  = dropdown.querySelectorAll('li');
    var active = dropdown.querySelector('li.ac-active');
    if (e.key === 'ArrowDown') {
      e.preventDefault();
      var next = active ? active.nextElementSibling : items[0];
      if (active) active.classList.remove('ac-active');
      if (next) next.classList.add('ac-active');
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      var prev = active ? active.previousElementSibling : items[items.length - 1];
      if (active) active.classList.remove('ac-active');
      if (prev) prev.classList.add('ac-active');
    } else if (e.key === 'Enter') {
      if (active && dropdown.style.display !== 'none') {
        e.preventDefault();
        input.value = active.getAttribute('data-value');
        closeDropdown();
        form.submit();
      }
    } else if (e.key === 'Escape') {
      closeDropdown();
    }
  });

  document.addEventListener('click', function (e) {
    if (!input.contains(e.target) && !dropdown.contains(e.target)) {
      closeDropdown();
    }
  });
})();

var _ewItems = @json($items->keyBy('id'));

function openEwasteEdit(id) {
  var d = _ewItems[id];
  if (!d) return;
  var classOpts = @json($assetClasses);
  var classSelects = classOpts.map(function(c){
    return '<option value="'+esc(c)+'"'+(d.asset_class===c?' selected':'')+'>'+esc(c)+'</option>';
  }).join('');

  var html = '<div style="background:var(--surface);border-radius:16px;width:100%;max-width:620px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.3)">'
    + '<div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center">'
    + '<div style="font-size:15px;font-weight:700;color:var(--text)"><i class="bi bi-recycle me-2" style="color:#16a34a"></i>Edit E-Waste Record</div>'
    + '<button onclick="document.getElementById(\'ewasteEditOverlay\').style.display=\'none\'" style="background:none;border:none;cursor:pointer;font-size:20px;color:var(--muted)">&times;</button>'
    + '</div>'
    + '<form method="POST" action="/ewaste/'+id+'">'
    + '<input type="hidden" name="_token" value="{{ csrf_token() }}">'
    + '<div style="padding:24px" class="row g-3">'
    + '<div class="col-md-3"><label class="form-label">Asset Number</label><input type="text" name="asset_number" class="form-control" value="'+esc(d.asset_number||'')+'"></div>'
    + '<div class="col-md-3"><label class="form-label">Asset Class <span style="color:var(--red)">*</span></label><select name="asset_class" class="form-select" required>'+classSelects+'</select></div>'
    + '<div class="col-md-6"><label class="form-label">Description <span style="color:var(--red)">*</span></label><input type="text" name="description" class="form-control" required value="'+esc(d.description||'')+'"></div>'
    + '<div class="col-md-3"><label class="form-label">Serial Number</label><input type="text" name="serial_number" class="form-control" value="'+esc(d.serial_number||'')+'"></div>'
    + '<div class="col-md-3"><label class="form-label">Date Flagged</label><input type="date" name="date_flagged" class="form-control" value="'+(d.date_flagged?d.date_flagged.substring(0,10):'')+'"></div>'
    + '<div class="col-md-3"><label class="form-label">Date Disposed</label><input type="date" name="date_disposed" class="form-control" value="'+(d.date_disposed?d.date_disposed.substring(0,10):'')+'"></div>'
    + '<div class="col-md-3"><label class="form-label">Weight (kg)</label><input type="number" step="0.01" name="weight_kg" class="form-control" value="'+(d.weight_kg||'')+'"></div>'
    + '<div class="col-12"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2">'+esc(d.notes||'')+'</textarea></div>'
    + '</div>'
    + '<div style="padding:16px 24px;border-top:1px solid var(--border);display:flex;gap:8px">'
    + '<button type="submit" class="btn-primary-custom"><i class="bi bi-check-lg"></i> Update Record</button>'
    + '<button type="button" onclick="document.getElementById(\'ewasteEditOverlay\').style.display=\'none\'" class="btn-secondary-custom"><i class="bi bi-x"></i> Cancel</button>'
    + '</div></form></div>';

  var ov = document.getElementById('ewasteEditOverlay');
  ov.innerHTML = html;
  ov.style.display = 'flex';
}

function esc(s){ return String(s).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;'); }

@if($user->isAdminOrFinance())
const _EW_SEL_KEY = 'fjb_sel_ewaste';
const ewSelected = new Set(JSON.parse(sessionStorage.getItem(_EW_SEL_KEY) || '[]'));
function _persistEwSel() { sessionStorage.setItem(_EW_SEL_KEY, JSON.stringify([...ewSelected])); }

function updateEwBulkBar() {
  document.querySelectorAll('tbody .ew-row-check').forEach(cb => {
    if (cb.checked) ewSelected.add(cb.value);
    else ewSelected.delete(cb.value);
  });
  const bar = document.getElementById('ewBulkBar');
  const count = ewSelected.size;
  document.getElementById('ewBulkCount').textContent = count;
  bar.style.display = count > 0 ? 'flex' : 'none';

  const anyNotCollected = [...document.querySelectorAll('tbody .ew-row-check:checked')]
    .some(cb => cb.getAttribute('data-status') !== 'Collected');
  const collectOpt = document.getElementById('ewOptCollect');
  const collectDiv = document.getElementById('ewOptCollectDiv');
  if (collectOpt) collectOpt.style.display = anyNotCollected ? '' : 'none';
  if (collectDiv) collectDiv.style.display = anyNotCollected ? '' : 'none';
}

function syncEwCheckboxes() {
  const rows = document.querySelectorAll('tbody .ew-row-check');
  const selectAll = document.querySelector('thead #ewSelectAll');
  if (!selectAll) return;
  let checkedCount = 0;
  rows.forEach(cb => {
    cb.checked = ewSelected.has(cb.value);
    if (cb.checked) checkedCount++;
  });
  selectAll.checked       = rows.length > 0 && checkedCount === rows.length;
  selectAll.indeterminate = checkedCount > 0 && checkedCount < rows.length;
  updateEwBulkBar();
}

document.addEventListener('change', function(e) {
  if (e.target.classList.contains('ew-row-check')) {
    if (e.target.checked) ewSelected.add(e.target.value);
    else ewSelected.delete(e.target.value);
    _persistEwSel();
    syncEwCheckboxes();
  }
});

document.addEventListener('change', function(e) {
  if (e.target.id === 'ewSelectAll') {
    document.querySelectorAll('tbody .ew-row-check').forEach(cb => {
      cb.checked = e.target.checked;
      if (e.target.checked) ewSelected.add(cb.value);
      else ewSelected.delete(cb.value);
    });
    _persistEwSel();
    updateEwBulkBar();
  }
});

window._onDtDraw = function() {
  const selectAll = document.querySelector('thead #ewSelectAll');
  if (selectAll) { selectAll.checked = false; selectAll.indeterminate = false; }
  syncEwCheckboxes();
};

function clearEwSelection() {
  ewSelected.clear();
  _persistEwSel();
  document.querySelectorAll('tbody .ew-row-check').forEach(cb => cb.checked = false);
  const selectAll = document.querySelector('thead #ewSelectAll');
  if (selectAll) { selectAll.checked = false; selectAll.indeterminate = false; }
  document.getElementById('ewBulkBar').style.display = 'none';
  document.getElementById('ewBulkCount').textContent = '0';
  document.getElementById('ewBulkActionValue').value = '';
  document.getElementById('ewActionLabel').textContent = '— Choose Action —';
}

function toggleEwDropdown() {
  var menu = document.getElementById('ewActionMenu');
  menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}

function selectEwAction(value, label) {
  document.getElementById('ewBulkActionValue').value = value;
  document.getElementById('ewActionLabel').textContent = label;
  document.getElementById('ewActionMenu').style.display = 'none';
}

document.addEventListener('click', function(e) {
  var wrap = document.getElementById('ewActionDropdownWrap');
  if (wrap && !wrap.contains(e.target)) {
    document.getElementById('ewActionMenu').style.display = 'none';
  }
});

function applyEwBulk() {
  var action = document.getElementById('ewBulkActionValue').value;
  if (!action) { alert('Please choose an action from the dropdown.'); return; }
  if (!ewSelected.size) return;

  var labels = {
    bulk_collect: 'Mark ' + ewSelected.size + ' item(s) as Collected?',
    bulk_restore: 'Restore ' + ewSelected.size + ' item(s) back to IT Assets?',
    bulk_delete:  'Permanently delete ' + ewSelected.size + ' item(s)? This cannot be undone.',
  };
  if (!confirm(labels[action])) return;

  // Build a form and POST it
  var form = document.createElement('form');
  form.method = 'POST';
  form.action = '{{ route("it.ewaste.bulk") }}';
  form.style.display = 'none';

  var token = document.createElement('input');
  token.type = 'hidden'; token.name = '_token'; token.value = '{{ csrf_token() }}';
  form.appendChild(token);

  var actionInput = document.createElement('input');
  actionInput.type = 'hidden'; actionInput.name = 'bulk_action'; actionInput.value = action;
  form.appendChild(actionInput);

  ewSelected.forEach(function(id) {
    var inp = document.createElement('input');
    inp.type = 'hidden'; inp.name = 'selected_ids[]'; inp.value = id;
    form.appendChild(inp);
  });

  ewSelected.clear();
  _persistEwSel();
  document.body.appendChild(form);
  form.submit();
}
@endif

// Live search
(function () {
  var input   = document.getElementById('ewSearchInput');
  var results = document.getElementById('ewLiveResults');
  var form    = document.getElementById('ewFilterForm');
  if (!input || !results || !form) return;
  var timer;
  input.addEventListener('input', function () {
    clearTimeout(timer);
    timer = setTimeout(function () {
      var params = new URLSearchParams();
      params.set('ew_search', input.value.trim());
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

