@extends('it.layouts.app')

@section('title', 'Disposal Items')
@section('page_title', 'Disposal Items')

@section('content')
@php $user = auth('it')->user(); @endphp

<!-- PAGE HEADER -->
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px">
  <div>
    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:5px">
      Disposal &rsaquo; <span style="color:var(--accent)">Disposal Items</span>
    </div>
    <h4 style="font-family:'DM Sans',sans-serif;font-weight:800;font-size:22px;color:var(--text);margin:0">Disposal Items</h4>
    <p style="font-size:13px;color:var(--muted);margin:4px 0 0">Items sent to disposal by IT Admin or Finance Admin</p>
  </div>
  @if($user->isAdminOrFinance())
  <div style="display:flex;gap:8px;align-items:center">
    <button onclick="document.getElementById('diImportModal').style.display='flex'"
      class="btn-secondary-custom" style="padding:10px 18px;font-size:13px;gap:7px">
      <i class="bi bi-file-earmark-excel-fill" style="color:#16a34a"></i> Import Excel
    </button>
  </div>
  @endif
</div>

<!-- STAT CARDS -->
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:20px">
  @foreach([
    ['bi-box-seam',     'rgba(59,130,246,.12)', '#2563eb', $total,    'Total Items', '#2563eb'],
    ['bi-check-circle', 'rgba(22,163,74,.12)',  '#16a34a', $approved, 'Approved',    '#16a34a'],
    ['bi-trash3',       'rgba(239,68,68,.12)',  '#dc2626', $disposed, 'Disposed',    '#dc2626'],
  ] as [$icon,$bg,$color,$val,$lbl,$border])
  <div style="background:var(--surface);border:1px solid var(--border);border-left:4px solid {{ $border }};border-radius:10px;padding:16px 20px;display:flex;align-items:center;gap:14px">
    <div style="width:42px;height:42px;border-radius:10px;background:{{ $bg }};display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0">
      <i class="bi {{ $icon }}" style="color:{{ $color }}"></i>
    </div>
    <div>
      <div style="font-size:26px;font-weight:800;color:var(--text);line-height:1">{{ $val }}</div>
      <div style="font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-top:3px">{{ $lbl }}</div>
    </div>
  </div>
  @endforeach
</div>

<!-- FILTER BAR -->
<div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:16px">
  <form method="GET" action="{{ route('it.disposal.index') }}" id="diFilterForm" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;width:100%">
    <div style="position:relative;flex:1;min-width:220px">
      <i class="bi bi-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:13px;z-index:1"></i>
      <input type="text" id="diSearchInput" name="di_search" value="{{ $search }}" placeholder="Search asset no., class, description, serial..."
        autocomplete="off"
        style="width:100%;padding:9px 12px 9px 34px;background:var(--surface);border:1.5px solid var(--border);border-radius:8px;color:var(--text);font-size:13px;font-family:'DM Sans',sans-serif;outline:none"
        onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
      <ul id="diSearchDropdown" style="display:none;position:absolute;top:calc(100% + 4px);left:0;right:0;background:var(--surface);border:1.5px solid var(--border);border-radius:8px;margin:0;padding:4px 0;list-style:none;max-height:240px;overflow-y:auto;z-index:9999;box-shadow:0 8px 24px rgba(0,0,0,.15)"></ul>
    </div>
    <select name="di_class" onchange="this.form.submit()"
      style="padding:9px 14px;background:var(--surface);border:1.5px solid var(--border);border-radius:8px;color:var(--text);font-size:13px;font-family:'DM Sans',sans-serif;outline:none;min-width:140px">
      <option value="">All Classes</option>
      @foreach($assetClasses as $cls)
      <option value="{{ $cls }}" {{ $class == $cls ? 'selected' : '' }}>{{ $cls }}</option>
      @endforeach
    </select>
    <select name="di_status" onchange="this.form.submit()"
      style="padding:9px 14px;background:var(--surface);border:1.5px solid var(--border);border-radius:8px;color:var(--text);font-size:13px;font-family:'DM Sans',sans-serif;outline:none;min-width:130px">
      <option value="">All Status</option>
      <option value="Approved" {{ $status === 'Approved' ? 'selected' : '' }}>Approved</option>
      <option value="Disposed" {{ $status === 'Disposed' ? 'selected' : '' }}>Disposed</option>
    </select>
    <button type="submit"
      style="padding:9px 20px;background:var(--accent);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;white-space:nowrap;display:flex;align-items:center;gap:6px">
      <i class="bi bi-funnel-fill"></i> Filter
    </button>
    @if($search || $class || $status)
    <a href="{{ route('it.disposal.index') }}" style="padding:9px 16px;background:var(--surface);color:var(--muted);border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;white-space:nowrap;font-family:'DM Sans',sans-serif">Clear</a>
    @endif
  </form>
</div>

<style>.di-table td,.di-table th{vertical-align:middle!important;padding:8px 10px!important;white-space:nowrap}.di-table td:nth-child(3),.di-table th:nth-child(3){white-space:normal;word-break:break-word;min-width:130px}</style>
@if($total === 0 && !$search && !$class && !$status)
<div style="background:var(--surface);border:1.5px dashed var(--border);border-radius:14px;padding:64px 20px;text-align:center">
  <i class="bi bi-box-arrow-right" style="font-size:36px;color:var(--muted);display:block;margin-bottom:14px"></i>
  <div style="font-weight:700;font-size:15px;color:var(--text);margin-bottom:4px">No disposal items yet</div>
  <div style="font-size:13px;color:var(--muted);margin-bottom:16px">Items disposed from E-Waste will appear here</div>
</div>
@else
<div id="diLiveResults">
@if($items->isEmpty())
<div style="background:var(--surface);border:1.5px dashed var(--border);border-radius:14px;padding:48px 20px;text-align:center">
  <i class="bi bi-search" style="font-size:32px;color:var(--muted);display:block;margin-bottom:12px"></i>
  <div style="font-weight:700;font-size:15px;color:var(--text);margin-bottom:4px">No records match your filter</div>
  <a href="{{ route('it.disposal.index') }}" style="font-size:13px;color:var(--accent)">Clear filters</a>
</div>
@else
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
</div>{{-- #diLiveResults --}}
@endif

@if($user->isAdminOrFinance())
{{-- Add Disposal Item Modal --}}
<div id="addDisposalModal" style="display:none;position:fixed;inset:0;z-index:9000;background:rgba(0,0,0,.45);align-items:center;justify-content:center;padding:20px">
  <div style="background:var(--surface);border-radius:16px;width:100%;max-width:620px;max-height:92vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.3)">
    <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center">
      <div style="font-size:15px;font-weight:700;color:var(--text)">
        <i class="bi bi-box-arrow-right me-2" style="color:var(--accent)"></i>Add Disposal Item
      </div>
      <button onclick="document.getElementById('addDisposalModal').style.display='none'" style="background:none;border:none;cursor:pointer;font-size:20px;color:var(--muted)">&times;</button>
    </div>
    <form method="POST" action="{{ route('it.disposal.store') }}">
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
          <label class="form-label">Status</label>
          <select name="disposal_status" class="form-select">
            <option value="Approved">Approved</option>
            <option value="Disposed">Disposed</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Date Flagged</label>
          <input type="date" name="date_flagged" class="form-control" value="{{ date('Y-m-d') }}">
        </div>
        <div class="col-md-3">
          <label class="form-label">Date Disposed</label>
          <input type="date" name="date_disposed" class="form-control">
        </div>
        <div class="col-md-4">
          <label class="form-label">Disposal Method</label>
          <input type="text" name="disposal_method" class="form-control">
        </div>
        <div class="col-md-4">
          <label class="form-label">Vendor / Collector</label>
          <input type="text" name="vendor_collector" class="form-control">
        </div>
        <div class="col-md-4">
          <label class="form-label">Certificate Number</label>
          <input type="text" name="certificate_number" class="form-control">
        </div>
        <div class="col-12">
          <label class="form-label">Notes</label>
          <textarea name="notes" class="form-control" rows="2"></textarea>
        </div>
      </div>
      <div style="padding:16px 24px;border-top:1px solid var(--border);display:flex;gap:8px">
        <button type="submit" class="btn-primary-custom">
          <i class="bi bi-plus-lg"></i> Add to Disposal
        </button>
        <button type="button" onclick="document.getElementById('addDisposalModal').style.display='none'" class="btn-secondary-custom">
          <i class="bi bi-x"></i> Cancel
        </button>
      </div>
    </form>
  </div>
</div>

{{-- Edit Disposal Item overlay (populated by JS) --}}
<div id="editDisposalOverlay" style="display:none;position:fixed;inset:0;z-index:9000;background:rgba(0,0,0,.45);align-items:center;justify-content:center;padding:20px"></div>

{{-- Import Excel Modal --}}
<div id="diImportModal" style="display:none;position:fixed;inset:0;z-index:9000;align-items:center;justify-content:center;background:rgba(15,23,42,.55);backdrop-filter:blur(3px);padding:1rem">
  <div style="background:var(--surface);border-radius:16px;width:100%;max-width:720px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.2);display:flex;flex-direction:column">
    <div style="background:linear-gradient(135deg,var(--navy,#142b47),var(--navy-mid,#254a78));padding:20px 24px;border-radius:16px 16px 0 0;display:flex;align-items:center;gap:14px;flex-shrink:0">
      <div style="width:42px;height:42px;border-radius:10px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;font-size:20px">
        <i class="bi bi-file-earmark-excel-fill" style="color:#22c55e"></i>
      </div>
      <div style="flex:1">
        <div style="color:#fff;font-size:16px;font-weight:700">Import Disposal Items from Excel</div>
        <div style="color:rgba(255,255,255,.6);font-size:12px;margin-top:2px">Upload an .xlsx, .xls or .csv file to bulk-import items</div>
      </div>
      <button onclick="diCloseImport()" style="background:rgba(255,255,255,.12);border:none;border-radius:8px;color:rgba(255,255,255,.8);width:32px;height:32px;cursor:pointer;font-size:18px;display:flex;align-items:center;justify-content:center">&times;</button>
    </div>
    <div style="padding:24px;flex:1">
      <div style="background:rgba(2,132,199,.06);border:1.5px solid rgba(2,132,199,.2);border-radius:10px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;gap:12px">
        <i class="bi bi-info-circle-fill" style="color:var(--accent,#0284c7);font-size:18px;flex-shrink:0"></i>
        <div style="flex:1;font-size:13px;color:var(--text)"><strong>First time?</strong> Download the template to see the correct column format.</div>
        <a href="{{ route('it.disposal.import-template') }}" download
          style="display:inline-flex;align-items:center;gap:6px;background:var(--navy,#142b47);color:#fff;border-radius:7px;padding:7px 14px;font-size:12px;font-weight:700;text-decoration:none;white-space:nowrap;flex-shrink:0">
          <i class="bi bi-download"></i> Download Template
        </a>
      </div>
      <div style="margin-bottom:16px">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:8px">Expected Columns</div>
        <div style="display:flex;flex-wrap:wrap;gap:6px">
          @foreach(['Asset Number','Asset Class','Description','Serial Number','Status','Disposal Method','Vendor/Collector','Certificate Number','Date Flagged','Date Disposed','Notes'] as $col)
          <span style="background:rgba(2,132,199,.08);color:var(--accent,#0284c7);border-radius:5px;padding:3px 10px;font-size:11px;font-weight:600">{{ $col }}</span>
          @endforeach
        </div>
        <div style="font-size:11px;color:var(--muted);margin-top:6px">Header row is always skipped &nbsp;·&nbsp; Columns are matched by keyword</div>
      </div>
      <div id="diImportDropzone" onclick="document.getElementById('diImportFileInput').click()"
        style="border:2px dashed var(--border);border-radius:12px;padding:32px 20px;text-align:center;cursor:pointer;transition:all .2s;margin-bottom:16px"
        ondragover="event.preventDefault();this.style.borderColor='var(--accent)';this.style.background='rgba(2,132,199,.04)'"
        ondragleave="this.style.borderColor='var(--border)';this.style.background=''"
        ondrop="event.preventDefault();this.style.borderColor='var(--border)';this.style.background='';diHandleFile(event.dataTransfer.files[0])">
        <i class="bi bi-cloud-upload-fill" style="font-size:36px;color:var(--muted);display:block;margin-bottom:10px;opacity:.5"></i>
        <div style="font-size:14px;font-weight:600;color:var(--text);margin-bottom:4px">Drop your file here or click to browse</div>
        <div style="font-size:12px;color:var(--muted)">.xlsx, .xls, .csv — max 5 000 rows</div>
        <input type="file" id="diImportFileInput" accept=".xlsx,.xls,.csv" style="display:none" onchange="diHandleFile(this.files[0])">
      </div>
      <div id="diImportPreview" style="display:none">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:8px">Preview (first 5 rows)</div>
        <div style="overflow-x:auto;border-radius:8px;border:1px solid var(--border)">
          <table id="diImportPreviewTable" style="width:100%;border-collapse:collapse;font-size:12px;font-family:'DM Sans',sans-serif"></table>
        </div>
        <div id="diImportRowCount" style="font-size:12px;color:var(--muted);margin-top:8px"></div>
      </div>
      <div id="diImportStatus" style="display:none;margin-top:16px"></div>
    </div>
    <div style="background:var(--body-bg,#f1f5f9);border-top:1px solid var(--border);padding:16px 24px;border-radius:0 0 16px 16px;display:flex;align-items:center;gap:10px;flex-shrink:0">
      <button id="diImportSubmitBtn" onclick="diSubmitImport()"
        style="display:none;background:var(--navy,#142b47);color:#fff;border:none;border-radius:8px;padding:10px 24px;font-size:13.5px;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;align-items:center;gap:7px">
        <i class="bi bi-upload"></i> Import Items
      </button>
      <button onclick="diCloseImport()"
        style="background:var(--surface);color:var(--text);border:1.5px solid var(--border);border-radius:8px;padding:10px 20px;font-size:13.5px;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif">
        Cancel
      </button>
    </div>
  </div>
</div>
@endif

@endsection

@push('scripts')
<script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>
<script>
// ── Disposal Import ──
var _diImportRows = [];

function diMapHeader(h) {
  h = String(h).toLowerCase().trim();
  if (h.includes('asset') && (h.includes('no') || h.includes('num') || h.includes('#'))) return 'asset_number';
  if (h.includes('class') || h.includes('categ') || h.includes('type')) return 'asset_class';
  if (h.includes('desc') || h.includes('name')) return 'description';
  if (h.includes('serial') || h.includes('s/n')) return 'serial_number';
  if (h.includes('status')) return 'disposal_status';
  if (h.includes('method')) return 'disposal_method';
  if (h.includes('vendor') || h.includes('collector')) return 'vendor_collector';
  if (h.includes('cert')) return 'certificate_number';
  if (h.includes('flag') || (h.includes('date') && !h.includes('dispos'))) return 'date_flagged';
  if (h.includes('dispos') && h.includes('date')) return 'date_disposed';
  if (h.includes('date')) return 'date_flagged';
  if (h.includes('note') || h.includes('remark')) return 'notes';
  return null;
}

function diHandleFile(file) {
  if (!file) return;
  document.getElementById('diImportStatus').style.display = 'none';
  document.getElementById('diImportPreview').style.display = 'none';
  document.getElementById('diImportSubmitBtn').style.display = 'none';
  var reader = new FileReader();
  reader.onload = function(e) {
    try {
      var wb = XLSX.read(e.target.result, {type:'array', cellDates:true});
      var ws = wb.Sheets[wb.SheetNames[0]];
      var rawRows = XLSX.utils.sheet_to_json(ws, {header:1, defval:''});
      if (rawRows.length < 2) { diShowError('File has no data rows.'); return; }
      var headerRowIdx = 0;
      for (var r = 0; r < Math.min(rawRows.length, 15); r++) {
        var nonEmpty = rawRows[r].filter(function(c){ return c !== '' && c !== null && c !== undefined; });
        if (nonEmpty.length >= 2 && rawRows[r].some(function(h){ return diMapHeader(String(h)) !== null; })) {
          headerRowIdx = r; break;
        }
      }
      var headers = rawRows[headerRowIdx].map(function(h){ return String(h).trim(); });
      var colMap = {};
      headers.forEach(function(h, i) {
        var field = diMapHeader(h);
        if (field && !colMap.hasOwnProperty(field)) colMap[field] = i;
      });
      _diImportRows = [];
      for (var r = headerRowIdx + 1; r < rawRows.length; r++) {
        var raw = rawRows[r];
        if (raw.every(function(c){ return c === '' || c === null || c === undefined; })) continue;
        var obj = {};
        Object.keys(colMap).forEach(function(key) {
          var val = raw[colMap[key]];
          if (val instanceof Date) {
            obj[key] = val.getFullYear() + '-' + String(val.getMonth()+1).padStart(2,'0') + '-' + String(val.getDate()).padStart(2,'0');
          } else { obj[key] = val !== undefined ? String(val).trim() : ''; }
        });
        _diImportRows.push(obj);
      }
      if (!_diImportRows.length) { diShowError('No data rows found after header.'); return; }
      var tbl = document.getElementById('diImportPreviewTable');
      var keys = Object.keys(colMap);
      tbl.innerHTML = '<tr style="background:#f8fafc">' + keys.map(function(k){
        return '<th style="padding:8px 12px;border-bottom:1px solid #e2e8f0;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#64748b;white-space:nowrap">' + k.replace(/_/g,' ') + '</th>';
      }).join('') + '</tr>' + _diImportRows.slice(0, 5).map(function(row){
        return '<tr>' + keys.map(function(k){
          return '<td style="padding:8px 12px;border-bottom:1px solid #f1f5f9;font-size:12px;color:#1e293b;white-space:nowrap">' + diEsc(row[k] || '—') + '</td>';
        }).join('') + '</tr>';
      }).join('');
      document.getElementById('diImportRowCount').textContent = _diImportRows.length + ' row' + (_diImportRows.length !== 1 ? 's' : '') + ' ready to import' + (_diImportRows.length > 5 ? ' (showing first 5)' : '');
      document.getElementById('diImportPreview').style.display = 'block';
      document.getElementById('diImportSubmitBtn').style.display = 'inline-flex';
    } catch(err) { diShowError('Could not read file: ' + err.message); }
  };
  reader.readAsArrayBuffer(file);
}

function diSubmitImport() {
  if (!_diImportRows.length) return;
  var btn = document.getElementById('diImportSubmitBtn');
  btn.disabled = true; btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Importing...';
  fetch('{{ route("it.disposal.import-excel") }}', {
    method: 'POST',
    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content},
    body: JSON.stringify(_diImportRows)
  }).then(function(r){ return r.json(); }).then(function(res){
    var html = '';
    if (res.inserted > 0) html += '<div style="background:#dcfce7;border:1px solid #bbf7d0;border-radius:8px;padding:12px 16px;display:flex;align-items:center;gap:10px;margin-bottom:10px"><i class="bi bi-check-circle-fill" style="color:#16a34a;font-size:18px;flex-shrink:0"></i><div><strong style="color:#166534">' + res.inserted + ' item' + (res.inserted !== 1 ? 's' : '') + ' imported successfully!</strong>' + (res.skipped > 0 ? ' <span style="color:#64748b;font-size:12px">(' + res.skipped + ' skipped)</span>' : '') + '</div></div>';
    if (res.skipped > 0 && res.inserted === 0) html += '<div style="background:#fee2e2;border:1px solid #fecaca;border-radius:8px;padding:12px 16px;margin-bottom:10px"><strong style="color:#991b1b">No items imported.</strong> All ' + res.skipped + ' rows skipped.</div>';
    if (res.errors && res.errors.length) html += '<div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:8px;padding:12px 16px;max-height:160px;overflow-y:auto"><div style="font-size:12px;font-weight:700;color:#92400e;margin-bottom:6px">Issues (' + res.errors.length + ')</div>' + res.errors.map(function(e){ return '<div style="font-size:12px;color:#92400e;padding:2px 0">• ' + diEsc(e) + '</div>'; }).join('') + '</div>';
    var s = document.getElementById('diImportStatus'); s.innerHTML = html; s.style.display = 'block';
    btn.disabled = false; btn.innerHTML = '<i class="bi bi-upload"></i> Import Items';
    if (res.inserted > 0) setTimeout(function(){ location.reload(); }, 2000);
  }).catch(function(err){
    diShowError('Request failed: ' + err.message);
    btn.disabled = false; btn.innerHTML = '<i class="bi bi-upload"></i> Import Items';
  });
}

function diCloseImport() {
  document.getElementById('diImportModal').style.display = 'none';
  document.getElementById('diImportFileInput').value = '';
  document.getElementById('diImportPreview').style.display = 'none';
  document.getElementById('diImportStatus').style.display = 'none';
  document.getElementById('diImportSubmitBtn').style.display = 'none';
  _diImportRows = [];
}

function diShowError(msg) {
  var s = document.getElementById('diImportStatus');
  s.innerHTML = '<div style="background:#fee2e2;border:1px solid #fecaca;border-radius:8px;padding:12px 16px;color:#991b1b;font-size:13px"><i class="bi bi-x-circle-fill me-2"></i>' + diEsc(msg) + '</div>';
  s.style.display = 'block';
}

function diEsc(str){ return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
</script>
<style>
#diSearchDropdown li {
  padding: 8px 14px;
  font-size: 13px;
  font-family: 'DM Sans', sans-serif;
  color: var(--text);
  cursor: pointer;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
#diSearchDropdown li:hover,
#diSearchDropdown li.ac-active {
  background: rgba(59,130,246,.08);
  color: var(--accent);
}
</style>
<script>
(function () {
  var input    = document.getElementById('diSearchInput');
  var dropdown = document.getElementById('diSearchDropdown');
  var form     = document.getElementById('diFilterForm');
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
      fetch('{{ route("it.disposal.autocomplete") }}?q=' + encodeURIComponent(q))
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
</script>

<script>
@if($user->isAdminOrFinance())
var _dispItems = @json($items->keyBy('id'));

function openEditDisposal(id) {
  var d = _dispItems[id];
  if (!d) return;
  var classOpts = @json($assetClasses);
  var classSelects = classOpts.map(function(c){
    return '<option value="'+esc(c)+'"'+(d.asset_class===c?' selected':'')+'>'+esc(c)+'</option>';
  }).join('');
  var statuses = ['Approved','Disposed'];
  var statusOpts = statuses.map(function(s){
    return '<option value="'+s+'"'+(d.disposal_status===s?' selected':'')+'>'+s+'</option>';
  }).join('');

  var html = '<div style="background:var(--surface);border-radius:16px;width:100%;max-width:620px;max-height:92vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.3)">'
    + '<div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center">'
    + '<div style="font-size:15px;font-weight:700;color:var(--text)"><i class="bi bi-box-arrow-right me-2" style="color:var(--accent)"></i>Edit Disposal Item</div>'
    + '<button onclick="document.getElementById(\'editDisposalOverlay\').style.display=\'none\'" style="background:none;border:none;cursor:pointer;font-size:20px;color:var(--muted)">&times;</button>'
    + '</div>'
    + '<form method="POST" action="/disposal/'+id+'/update">'
    + '<input type="hidden" name="_token" value="{{ csrf_token() }}">'
    + '<div style="padding:24px" class="row g-3">'
    + '<div class="col-md-3"><label class="form-label">Asset Number</label><input type="text" name="asset_number" class="form-control" value="'+esc(d.asset_number||'')+'"></div>'
    + '<div class="col-md-3"><label class="form-label">Asset Class <span style="color:var(--red)">*</span></label><select name="asset_class" class="form-select" required>'+classSelects+'</select></div>'
    + '<div class="col-md-6"><label class="form-label">Description <span style="color:var(--red)">*</span></label><input type="text" name="description" class="form-control" required value="'+esc(d.description||'')+'"></div>'
    + '<div class="col-md-3"><label class="form-label">Serial Number</label><input type="text" name="serial_number" class="form-control" value="'+esc(d.serial_number||'')+'"></div>'
    + '<div class="col-md-3"><label class="form-label">Status</label><select name="disposal_status" class="form-select">'+statusOpts+'</select></div>'
    + '<div class="col-md-3"><label class="form-label">Date Flagged</label><input type="date" name="date_flagged" class="form-control" value="'+(d.date_flagged?d.date_flagged.substring(0,10):'')+'"></div>'
    + '<div class="col-md-3"><label class="form-label">Date Disposed</label><input type="date" name="date_disposed" class="form-control" value="'+(d.date_disposed?d.date_disposed.substring(0,10):'')+'"></div>'
    + '<div class="col-md-4"><label class="form-label">Disposal Method</label><input type="text" name="disposal_method" class="form-control" value="'+esc(d.disposal_method||'')+'"></div>'
    + '<div class="col-md-4"><label class="form-label">Vendor / Collector</label><input type="text" name="vendor_collector" class="form-control" value="'+esc(d.vendor_collector||'')+'"></div>'
    + '<div class="col-md-4"><label class="form-label">Certificate Number</label><input type="text" name="certificate_number" class="form-control" value="'+esc(d.certificate_number||'')+'"></div>'
    + '<div class="col-12"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2">'+esc(d.notes||'')+'</textarea></div>'
    + '</div>'
    + '<div style="padding:16px 24px;border-top:1px solid var(--border);display:flex;gap:8px">'
    + '<button type="submit" class="btn-primary-custom"><i class="bi bi-check-lg"></i> Update Item</button>'
    + '<button type="button" onclick="document.getElementById(\'editDisposalOverlay\').style.display=\'none\'" class="btn-secondary-custom"><i class="bi bi-x"></i> Cancel</button>'
    + '</div></form></div>';

  var ov = document.getElementById('editDisposalOverlay');
  ov.innerHTML = html;
  ov.style.display = 'flex';
}

function esc(s){ return String(s).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
@endif

// Live search
(function () {
  var input   = document.getElementById('diSearchInput');
  var results = document.getElementById('diLiveResults');
  var form    = document.getElementById('diFilterForm');
  if (!input || !results || !form) return;
  var timer;
  input.addEventListener('input', function () {
    clearTimeout(timer);
    timer = setTimeout(function () {
      var params = new URLSearchParams();
      params.set('di_search', input.value.trim());
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

