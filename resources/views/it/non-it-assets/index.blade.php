@extends('it.layouts.app')

@section('title', 'Non-IT Assets')
@section('page_title', 'Non-IT Assets')

@section('content')
@php $user = auth('it')->user(); @endphp

<style>
/* ── NIT Form Card ── */
.nit-form-card {
  background: var(--surface); border: 1px solid var(--border);
  border-radius: 14px; overflow: hidden;
  box-shadow: 0 1px 3px rgba(0,0,0,.08), 0 4px 16px rgba(0,0,0,.06);
  margin-bottom: 28px;
}
.nit-form-header {
  background: linear-gradient(135deg, var(--navy, #142b47) 0%, var(--navy-mid, #254a78) 100%);
  padding: 20px 28px; display: flex; align-items: center; gap: 14px;
}
.nit-form-header-icon {
  width: 42px; height: 42px; border-radius: 10px;
  background: rgba(255,255,255,.15);
  display: flex; align-items: center; justify-content: center;
  font-size: 20px; color: #fff; flex-shrink: 0;
}
.nit-form-header-title { color: #fff; font-size: 16px; font-weight: 700; line-height: 1.2; }
.nit-form-header-sub   { color: rgba(255,255,255,.6); font-size: 12px; margin-top: 2px; }
.nit-form-body   { padding: 28px; }
.nit-section-label {
  font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em;
  color: var(--muted); margin-bottom: 14px; padding-bottom: 8px;
  border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 8px;
}
.nit-section-label i { font-size: 13px; }
.nit-field label {
  display: block; font-size: 12px; font-weight: 600; color: var(--muted);
  text-transform: uppercase; letter-spacing: .06em; margin-bottom: 6px;
}
.nit-field label .req { color: var(--red, #ef4444); margin-left: 3px; }
.nit-field input, .nit-field select, .nit-field textarea {
  width: 100%; padding: 10px 14px;
  background: var(--body-bg, #f1f5f9); border: 1.5px solid var(--border);
  border-radius: 8px; font-family: 'Inter', sans-serif; font-size: 13.5px;
  color: var(--text); transition: border-color .18s, box-shadow .18s; outline: none;
}
.nit-field input:focus, .nit-field select:focus, .nit-field textarea:focus {
  border-color: var(--accent, #0284c7); background: var(--surface);
  box-shadow: 0 0 0 3px rgba(2,132,199,.12);
}
.nit-field input::placeholder { color: var(--muted); opacity: .65; }
.nit-field textarea { resize: vertical; min-height: 80px; }
.nit-field .field-hint { font-size: 11px; color: var(--muted); margin-top: 5px; }
.nit-divider { height: 1px; background: var(--border); margin: 24px 0; }
.nit-form-footer {
  background: var(--body-bg, #f1f5f9); border-top: 1px solid var(--border);
  padding: 16px 28px; display: flex; align-items: center; gap: 10px;
}
.nit-select-grid { display: flex; gap: 10px; flex-wrap: wrap; }
.nit-select-opt  {
  flex: 1; min-width: 110px; padding: 10px 14px;
  border: 1.5px solid var(--border); border-radius: 8px;
  background: var(--body-bg); cursor: pointer; text-align: center;
  transition: all .15s; font-size: 13px; font-weight: 600; color: var(--muted);
  display: flex; flex-direction: column; align-items: center; gap: 5px;
}
.nit-select-opt:hover { border-color: var(--accent); color: var(--accent); background: rgba(2,132,199,.05); }
.nit-select-opt i { font-size: 18px; }
/* ── NIT Table ── */
.nit-table { width:100%; table-layout:auto; }
.nit-table td,.nit-table th{vertical-align:middle!important;padding:5px 8px!important;white-space:nowrap;font-size:12.5px}
.nit-scroll-wrap::-webkit-scrollbar{height:6px}
.nit-scroll-wrap::-webkit-scrollbar-track{background:var(--border);border-radius:3px}
.nit-scroll-wrap::-webkit-scrollbar-thumb{background:var(--accent);border-radius:3px}
/* Description column: allow wrapping and take remaining space */
.nit-table td:nth-child(4),.nit-table th:nth-child(4){white-space:normal;word-break:break-word;width:100%;min-width:150px}
/* Location column: allow wrapping */
.nit-table td:nth-child(6),.nit-table th:nth-child(6){white-space:normal;word-break:break-word;min-width:100px}
/* Actions & QR columns: never wrap, just enough for the buttons */
.nit-table td:last-child, .nit-table th:last-child,
.nit-table td:nth-last-child(2), .nit-table th:nth-last-child(2) { white-space:nowrap; width:1%; }
</style>

{{-- ══ ADD FORM (modal popup when Add Asset clicked) ══ --}}
<div id="nitAddFormSection" style="display:none;position:fixed;inset:0;z-index:9000;background:rgba(0,0,0,.5);align-items:flex-start;justify-content:center;padding:72px 24px 24px" onclick="if(event.target===this)closeNitAddForm()">
  <div style="background:#fff;border-radius:12px;width:100%;max-width:960px;max-height:92vh;overflow-y:auto;box-shadow:0 24px 64px rgba(0,0,0,.25);font-family:'Inter',sans-serif">

    {{-- Header --}}
    <div style="background:#1e2d40;border-radius:12px 12px 0 0;padding:20px 28px;display:flex;align-items:center;justify-content:space-between">
      <div style="display:flex;align-items:center;gap:14px">
        <div style="width:40px;height:40px;background:rgba(255,255,255,.12);border-radius:8px;display:flex;align-items:center;justify-content:center">
          <i class="bi bi-boxes" style="color:#fff;font-size:18px"></i>
        </div>
        <div>
          <div style="font-size:16px;font-weight:700;color:#fff;line-height:1.2">Register New Non-IT Asset</div>
          <div style="font-size:12px;color:rgba(255,255,255,.55);margin-top:2px">Fill in the details to register a non-IT asset</div>
        </div>
      </div>
      <button onclick="closeNitAddForm()" style="background:rgba(255,255,255,.1);border:none;cursor:pointer;width:32px;height:32px;border-radius:6px;color:#fff;font-size:18px;display:flex;align-items:center;justify-content:center;line-height:1">&times;</button>
    </div>

    <form method="POST" action="{{ route('it.non-it.store') }}">
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
            <input type="text" name="asset_number" placeholder="e.g. NIT-001"
              style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"
              onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
            <div style="font-size:11px;color:var(--muted);margin-top:4px">Leave blank to assign manually later</div>
          </div>
          <div>
            <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">F/A Code</label>
            <input type="text" name="fa_code" placeholder="e.g. 4100000047"
              style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"
              onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
          </div>
          <div>
            <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Asset Class <span style="color:#e53e3e">*</span></label>
            @if($assetClasses->isEmpty())
            <select name="asset_class" required disabled
              style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box">
              <option value="">— No classes yet —</option>
            </select>
            <div style="font-size:11px;color:#dc2626;margin-top:4px">
              <i class="bi bi-exclamation-triangle-fill"></i>
              No Non-IT classes found. Go to <a href="{{ route('it.asset-classes.index') }}" style="color:#0284c7;font-weight:600">Asset Classes</a> and add some first.
            </div>
            @else
            <select name="asset_class" required
              style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"
              onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
              <option value="" disabled selected>— Select Class —</option>
              @foreach($assetClasses as $cls)
              <option value="{{ $cls->name }}">{{ $cls->name }}</option>
              @endforeach
            </select>
            <div style="font-size:11px;color:var(--muted);margin-top:4px">Manage classes under <a href="{{ route('it.asset-classes.index') }}" style="color:#0284c7">Asset Classes</a></div>
            @endif
          </div>
          <div>
            <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Date Registered</label>
            <input type="date" name="date_registered" value="{{ date('Y-m-d') }}"
              style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"
              onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
          </div>
          <div>
            <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Warranty Until</label>
            <input type="date" name="warranty_date"
              style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"
              onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
          </div>
        </div>
        <div>
          <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Description <span style="color:#e53e3e">*</span></label>
          <input type="text" name="description" required placeholder="e.g. Ergonomic Office Chair — Black Mesh, 5-wheel base"
            style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"
            onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
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
            <input type="number" name="years_purchase" placeholder="e.g. 2017" min="1990" max="2099"
              style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"
              onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
          </div>
          <div>
            <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Total Cost (RM)</label>
            <input type="number" name="total_cost" placeholder="0.00" step="0.01" min="0"
              style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"
              onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
          </div>
          <div>
            <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Accumulated (RM)</label>
            <input type="number" name="accumulated" placeholder="0.00" step="0.01" min="0"
              style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"
              onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
          </div>
          <div>
            <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">NBV AT (RM)</label>
            <input type="number" name="nbv_at" placeholder="0.00" step="0.01" min="0"
              style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"
              onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
          </div>
        </div>
      </div>

      {{-- Section: Location & Brand --}}
      <div style="padding:24px 28px;border-top:1px solid var(--border)">
        <div style="display:flex;align-items:center;gap:7px;margin-bottom:18px">
          <i class="bi bi-geo-alt" style="font-size:13px;color:var(--muted)"></i>
          <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted)">Location &amp; Brand</span>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
          <div>
            <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Location</label>
            <select name="location"
              style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"
              onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
              <option value="">— Select Location —</option>
              @foreach($locations as $loc)
              <option value="{{ $loc->name }}">{{ $loc->name }}</option>
              @endforeach
            </select>
            @if($locations->isEmpty())
            <div style="font-size:11px;color:#dc2626;margin-top:4px"><i class="bi bi-exclamation-triangle-fill"></i> No locations yet. Add them in <a href="{{ route('it.locations.index') }}" style="color:#0284c7">Masterdata &rsaquo; Locations</a>.</div>
            @endif
          </div>
          <div>
            <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Brand</label>
            <select name="brand"
              style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;box-sizing:border-box"
              onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
              <option value="">— Select Brand —</option>
              @foreach($brands as $brand)
              <option value="{{ $brand->name }}">{{ $brand->name }}</option>
              @endforeach
            </select>
            @if($brands->isEmpty())
            <div style="font-size:11px;color:#dc2626;margin-top:4px"><i class="bi bi-exclamation-triangle-fill"></i> No brands yet. Add them in <a href="{{ route('it.brands.index') }}" style="color:#0284c7">Masterdata &rsaquo; Brands</a>.</div>
            @endif
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
            style="width:100%;padding:9px 12px;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;outline:none;resize:vertical;box-sizing:border-box"
            onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'"></textarea>
        </div>
      </div>

      {{-- Section: Condition --}}
      <div style="padding:24px 28px;border-top:1px solid var(--border)">
        <div style="display:flex;align-items:center;gap:7px;margin-bottom:18px">
          <i class="bi bi-activity" style="font-size:13px;color:var(--muted)"></i>
          <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted)">Condition</span>
        </div>
        <input type="hidden" name="item_status" value="Active">
        <div style="max-width:520px">
          <label style="display:block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px">Condition</label>
          <input type="hidden" name="condition_status" id="add_condition_status_val" value="Good">
          <div class="nit-select-grid" id="addCondGrid">
            <div class="nit-select-opt selected" data-color="#16a34a" data-bg="rgba(22,163,74,.1)"
              onclick="selectOpt(this,'addCondGrid','add_condition_status_val','Good')"
              style="border-color:#16a34a;background:rgba(22,163,74,.1);color:#16a34a">
              <i class="bi bi-emoji-smile-fill" style="color:#16a34a"></i>Good
            </div>
            <div class="nit-select-opt" data-color="#d97706" data-bg="rgba(217,119,6,.1)"
              onclick="selectOpt(this,'addCondGrid','add_condition_status_val','Fair')">
              <i class="bi bi-emoji-neutral-fill"></i>Fair
            </div>
            <div class="nit-select-opt" data-color="#dc2626" data-bg="rgba(239,68,68,.1)"
              onclick="selectOpt(this,'addCondGrid','add_condition_status_val','Poor')">
              <i class="bi bi-emoji-frown-fill"></i>Poor
            </div>
            <div class="nit-select-opt" data-color="#64748b" data-bg="rgba(100,116,139,.1)"
              onclick="selectOpt(this,'addCondGrid','add_condition_status_val','For Disposal')">
              <i class="bi bi-trash2-fill"></i>For Disposal
            </div>
          </div>
        </div>
      </div>

      {{-- Footer --}}
      <div style="padding:16px 28px;border-top:1px solid var(--border);display:flex;align-items:center;gap:10px">
        <button type="submit"
          style="padding:10px 22px;background:#1e2d40;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif;display:flex;align-items:center;gap:7px">
          <i class="bi bi-plus-lg"></i>
          @if($user->isAdminOrFinance()) Register Asset @else Submit Request @endif
        </button>
        <button type="button" onclick="closeNitAddForm()"
          style="padding:10px 20px;background:#fff;color:var(--text);border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;font-family:'Inter',sans-serif;display:flex;align-items:center;gap:6px">
          <i class="bi bi-x"></i> Cancel
        </button>
      </div>
    </form>
  </div>
</div>

{{-- ══ PAGE HEADER ══ --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px">
  <div>
    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:5px">
      All Assets &rsaquo; <span style="color:var(--accent)">Non-IT Assets</span>
    </div>
    <h4 style="font-family:'Inter',sans-serif;font-weight:800;font-size:22px;color:var(--text);margin:0">Non-IT Assets</h4>
    <p style="font-size:13px;color:var(--muted);margin:4px 0 0">Furniture, equipment, vehicles and other non-IT assets</p>
  </div>
  @if(!$user->isReadOnlyViewer())
  <div id="nitHeaderBtns" style="display:flex;gap:8px;align-items:center">
    @if($user->isAdminOrFinance())
    <button onclick="document.getElementById('nitImportModal').style.display='flex'"
      class="btn-secondary-custom" style="padding:10px 18px;font-size:13px;gap:7px">
      <i class="bi bi-file-earmark-excel-fill" style="color:#16a34a"></i> Import Excel
    </button>
    @endif
    <a href="#" onclick="openNitAddForm();return false" class="btn-primary-custom" style="padding:10px 20px;font-size:13px">
      <i class="bi bi-{{ $user->isAdminOrFinance() ? 'plus-lg' : 'send' }}"></i>
      {{ $user->isAdminOrFinance() ? 'Add Asset' : 'Request to Add' }}
    </a>
  </div>
  @endif
</div>

{{-- ══ STAT STRIP ══ --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px">
  @foreach([
    ['bi-boxes',              'rgba(2,132,199,.12)',   '#0284c7', $nit_total,   'Total Assets', '#0284c7'],
    ['bi-check-circle-fill',  'rgba(22,163,74,.12)',   '#16a34a', $nit_active,  'Active',       '#16a34a'],
    ['bi-tools',              'rgba(245,158,11,.12)',  '#d97706', $nit_repair,  'In Repair',    '#d97706'],
    ['bi-trash3-fill',        'rgba(239,68,68,.12)',   '#dc2626', $nit_disp,    'Disposed',     '#dc2626'],
  ] as [$icon,$bg,$color,$val,$lbl,$border])
  <div style="background:var(--surface);border:1px solid var(--border);border-left:4px solid {{ $border }};border-radius:12px;padding:16px 20px;display:flex;align-items:center;gap:14px;box-shadow:0 1px 3px rgba(0,0,0,.07),0 4px 14px rgba(0,0,0,.05)">
    <div style="width:44px;height:44px;border-radius:10px;background:{{ $bg }};display:flex;align-items:center;justify-content:center;font-size:19px;flex-shrink:0">
      <i class="bi {{ $icon }}" style="color:{{ $color }}"></i>
    </div>
    <div>
      <div style="font-size:26px;font-weight:800;color:var(--text);line-height:1;font-family:'Inter',sans-serif">{{ number_format($val) }}</div>
      <div style="font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-top:3px">{{ $lbl }}</div>
    </div>
  </div>
  @endforeach
</div>

{{-- ══ FILTER BAR ══ --}}
<div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:16px">
  <form method="GET" action="{{ route('it.non-it.index') }}" id="nitFilterForm" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;width:100%">
    <div style="position:relative;flex:1;min-width:220px">
      <i class="bi bi-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:13px"></i>
      <input type="text" name="nit_search" id="nitSearchInput" value="{{ $search }}"
        placeholder="Search asset no., class, description, location..."
        autocomplete="off"
        style="width:100%;padding:9px 12px 9px 34px;background:var(--surface);border:1.5px solid var(--border);border-radius:8px;color:var(--text);font-size:13px;font-family:'Inter',sans-serif;outline:none"
        onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
      <div id="nitSearchSuggestions" style="display:none;position:absolute;top:calc(100% + 4px);left:0;right:0;z-index:500;background:#fff;border:1.5px solid var(--border);border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);overflow:hidden;max-height:260px;overflow-y:auto"></div>
    </div>
    <select name="nit_class" onchange="this.form.submit()"
      style="padding:9px 14px;background:var(--surface);border:1.5px solid var(--border);border-radius:8px;color:var(--text);font-size:13px;font-family:'Inter',sans-serif;outline:none;min-width:140px">
      <option value="">All Classes</option>
      @foreach($nitClassesUsed->sort() as $cl)
      <option value="{{ $cl }}" {{ $class === $cl ? 'selected' : '' }}>{{ $cl }}</option>
      @endforeach
    </select>
    <select name="nit_status" onchange="this.form.submit()"
      style="padding:9px 14px;background:var(--surface);border:1.5px solid var(--border);border-radius:8px;color:var(--text);font-size:13px;font-family:'Inter',sans-serif;outline:none;min-width:130px">
      <option value="">All Status</option>
      <option value="Active"                      {{ $status === 'Active'                      ? 'selected' : '' }}>Active</option>
      <option value="Disposed"                    {{ $status === 'Disposed'                    ? 'selected' : '' }}>Disposed</option>
      <option value="Pending for Write-Off"       {{ $status === 'Pending for Write-Off'       ? 'selected' : '' }}>Pending</option>
    </select>
    <select name="nit_location" onchange="this.form.submit()"
      style="padding:9px 14px;background:var(--surface);border:1.5px solid var(--border);border-radius:8px;color:var(--text);font-size:13px;font-family:'Inter',sans-serif;outline:none;min-width:140px">
      <option value="">All Locations</option>
      @foreach($allLocations as $loc)
      <option value="{{ $loc }}" {{ $location === $loc ? 'selected' : '' }}>{{ $loc }}</option>
      @endforeach
    </select>
    <button type="submit"
      style="padding:9px 20px;background:var(--navy,#142b47);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif;white-space:nowrap;display:flex;align-items:center;gap:6px">
      <i class="bi bi-funnel-fill"></i> Filter
    </button>
    @if($search || $class || $status || $location)
    <a href="{{ route('it.non-it.index') }}"
      style="padding:9px 16px;background:var(--surface);color:var(--muted);border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;white-space:nowrap;font-family:'Inter',sans-serif">
      Clear
    </a>
    @endif
  </form>
</div>

{{-- ══ EDIT FORM (shown inline above table when Edit clicked) ══ --}}
<div id="nitEditFormSection" style="display:none">
  <div class="nit-form-card">
    <div class="nit-form-header">
      <div class="nit-form-header-icon">
        <i class="bi bi-pencil-square" id="nitEditFormIcon"></i>
      </div>
      <div>
        <div class="nit-form-header-title" id="nitEditFormTitle">Edit Non-IT Asset</div>
        <div class="nit-form-header-sub" id="nitEditFormSub">Update asset details below</div>
      </div>
    </div>
    <form method="POST" id="nitEditFormEl" action="">
      @csrf
      <div class="nit-form-body">

        <div class="nit-section-label"><i class="bi bi-tag-fill"></i> Asset Identity</div>
        <div class="row g-3 mb-4">
          <div class="col-md-3 nit-field">
            <label>Asset Number</label>
            <input type="text" name="asset_number" id="nef_asset_number" placeholder="e.g. NIT-001">
            <div class="field-hint">Leave blank to assign manually later</div>
          </div>
          <div class="col-md-3 nit-field">
            <label>F/A Code</label>
            <input type="text" name="fa_code" id="nef_fa_code" placeholder="e.g. 4100000047">
          </div>
          <div class="col-md-3 nit-field">
            <label>Asset Class <span class="req">*</span></label>
            <select name="asset_class" id="nef_asset_class" required>
              <option value="" disabled>— Select Class —</option>
              @foreach($assetClasses as $cls)
              <option value="{{ $cls->name }}">{{ $cls->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3 nit-field">
            <label>Date Registered</label>
            <input type="date" name="date_registered" id="nef_date_registered">
          </div>
          <div class="col-md-3 nit-field">
            <label>Warranty Until</label>
            <input type="date" name="warranty_date" id="nef_warranty_date">
          </div>
          <div class="col-12 nit-field">
            <label>Description <span class="req">*</span></label>
            <input type="text" name="description" id="nef_description" required placeholder="e.g. Ergonomic Office Chair — Black Mesh, 5-wheel base">
          </div>
        </div>

        <div class="nit-divider"></div>

        <div class="nit-section-label"><i class="bi bi-currency-dollar"></i> Financial Details</div>
        <div class="row g-3 mb-4">
          <div class="col-md-3 nit-field">
            <label>Years Purchase</label>
            <input type="number" name="years_purchase" id="nef_years_purchase" placeholder="e.g. 2017" min="1990" max="2099">
          </div>
          <div class="col-md-3 nit-field">
            <label>Total Cost (RM)</label>
            <input type="number" name="total_cost" id="nef_total_cost" placeholder="0.00" step="0.01" min="0">
          </div>
          <div class="col-md-3 nit-field">
            <label>Accumulated (RM)</label>
            <input type="number" name="accumulated" id="nef_accumulated" placeholder="0.00" step="0.01" min="0">
          </div>
          <div class="col-md-3 nit-field">
            <label>NBV AT (RM)</label>
            <input type="number" name="nbv_at" id="nef_nbv_at" placeholder="0.00" step="0.01" min="0">
          </div>
        </div>

        <div class="nit-divider"></div>

        <div class="nit-section-label"><i class="bi bi-geo-alt-fill"></i> Brand, Location &amp; Notes</div>
        <div class="row g-3 mb-4">
          <div class="col-md-4 nit-field">
            <label>Brand</label>
            <select name="brand" id="nef_brand">
              <option value="">— Select Brand —</option>
              @foreach($brands as $brand)
              <option value="{{ $brand->name }}">{{ $brand->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4 nit-field">
            <label>Location</label>
            <select name="location" id="nef_location">
              <option value="">— Select Location —</option>
              @foreach($locations as $loc)
              <option value="{{ $loc->name }}">{{ $loc->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4 nit-field">
            <label>Notes</label>
            <input type="text" name="notes" id="nef_notes" placeholder="Any additional remarks...">
          </div>
        </div>

        <div class="nit-divider"></div>

        <div class="nit-section-label"><i class="bi bi-activity"></i> Condition</div>
        <div class="row g-3">
          <div class="col-md-6 nit-field">
            <label>Condition</label>
            <input type="hidden" name="condition_status" id="edit_condition_status_val" value="Good">
            <div class="nit-select-grid" id="editCondGrid">
              <div class="nit-select-opt" data-color="#16a34a" data-bg="rgba(22,163,74,.1)"
                onclick="selectOpt(this,'editCondGrid','edit_condition_status_val','Good')">
                <i class="bi bi-emoji-smile-fill"></i>Good
              </div>
              <div class="nit-select-opt" data-color="#d97706" data-bg="rgba(217,119,6,.1)"
                onclick="selectOpt(this,'editCondGrid','edit_condition_status_val','Fair')">
                <i class="bi bi-emoji-neutral-fill"></i>Fair
              </div>
              <div class="nit-select-opt" data-color="#dc2626" data-bg="rgba(239,68,68,.1)"
                onclick="selectOpt(this,'editCondGrid','edit_condition_status_val','Poor')">
                <i class="bi bi-emoji-frown-fill"></i>Poor
              </div>
              <div class="nit-select-opt" data-color="#64748b" data-bg="rgba(100,116,139,.1)"
                onclick="selectOpt(this,'editCondGrid','edit_condition_status_val','For Disposal')">
                <i class="bi bi-trash2-fill"></i>For Disposal
              </div>
            </div>
          </div>
        </div>

      </div>
      <div class="nit-form-footer">
        <button type="submit" class="btn-primary-custom" id="nitEditSubmitBtn" style="padding:10px 24px;font-size:13.5px">
          <i class="bi bi-check-lg"></i> Save Changes
        </button>
        <a href="#" onclick="closeNitEditForm();return false" class="btn-secondary-custom" style="padding:10px 20px;font-size:13.5px">
          <i class="bi bi-x-lg"></i> Cancel
        </a>
        <div style="margin-left:auto;font-size:12px;color:var(--muted)" id="nitEditLastUpdated"></div>
      </div>
    </form>
  </div>
</div>

{{-- ══ TABLE / EMPTY STATE ══ --}}
@if($nit_total === 0 && !$search && !$class && !$status && !$location)
<div style="background:var(--surface);border:1.5px dashed var(--border);border-radius:14px;padding:72px 20px;text-align:center">
  <i class="bi bi-boxes" style="font-size:44px;color:var(--muted);display:block;margin-bottom:14px;opacity:.4"></i>
  <div style="font-weight:700;font-size:15px;color:var(--text);margin-bottom:6px">No non-IT assets registered yet</div>
  <div style="font-size:13px;color:var(--muted);margin-bottom:20px">Register furniture, equipment, vehicles and other assets here</div>
  @if($user->isAdminOrFinance())
  <a href="#" onclick="openNitAddForm();return false" class="btn-primary-custom" style="font-size:13px">
    <i class="bi bi-plus-lg"></i> Register First Asset
  </a>
  @endif
</div>

@elseif($items->isEmpty())
<div style="background:var(--surface);border:1.5px dashed var(--border);border-radius:14px;padding:48px 20px;text-align:center">
  <i class="bi bi-search" style="font-size:32px;color:var(--muted);display:block;margin-bottom:12px;opacity:.4"></i>
  <div style="font-weight:700;font-size:15px;color:var(--text);margin-bottom:6px">No records match your filter</div>
  <a href="{{ route('it.non-it.index') }}" style="font-size:13px;color:var(--accent)">Clear filters</a>
</div>

@else

{{-- BULK ACTION BAR --}}
<div id="nitBulkBar" style="display:none;position:sticky;top:12px;z-index:100;margin-bottom:12px">
  <div style="background:#1a2332;color:#fff;border-radius:10px;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 4px 20px rgba(0,0,0,.3)">
    <span style="font-family:'Inter',sans-serif;font-weight:700;font-size:14px">
      <i class="bi bi-check2-square me-2"></i><span id="nitBulkCount">0</span> item(s) selected
    </span>
    <div style="display:flex;gap:8px;align-items:center">
      <form method="POST" action="{{ route('it.non-it.bulk-destroy') }}" id="nitBulkDeleteForm" style="display:inline">
        @csrf
        <input type="hidden" name="bulk_action" value="delete">
        <div id="nit_delete_ids"></div>
        <button type="button" onclick="nitSubmitBulk('delete')"
          style="background:#fdecec;color:#dc2626;border:1px solid #fecaca;border-radius:7px;padding:7px 16px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:6px">
          <i class="bi bi-trash"></i> Delete
        </button>
      </form>
      @if(!$user->isReadOnlyViewer())
      <button type="button" onclick="nitSubmitBulkDispose()"
        style="background:#fff7ed;color:#ea580c;border:1px solid #fed7aa;border-radius:7px;padding:7px 16px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:6px">
        <i class="bi bi-recycle"></i> Dispose
      </button>
      @endif
      <button type="button" onclick="nitClearSelection()"
        style="background:rgba(255,255,255,.1);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:7px;padding:7px 14px;font-size:13px;cursor:pointer">
        <i class="bi bi-x"></i>
      </button>
    </div>
  </div>
</div>

<div id="nitLiveResults">
<div style="background:var(--surface);border:1px solid var(--border);border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.08),0 4px 16px rgba(0,0,0,.06)">
  <div style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
    <span style="font-size:13px;color:var(--muted);font-weight:500">
      <strong style="color:var(--text)">{{ number_format($filtered_total) }}</strong> asset{{ $filtered_total !== 1 ? 's' : '' }}
      @if($search || $class || $status || $location)
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
        <td style="width:1%;white-space:nowrap">
          <button onclick="openNitQRModal({{ $row->id }}, '{{ addslashes(e($row->asset_number ?? 'N/A')) }}', '{{ addslashes(e($row->description)) }}', '{{ addslashes(e($row->fa_code ?? '')) }}', '{{ addslashes(e($row->location ?? '')) }}')"
            style="font-size:12px;color:#7c3aed;background:rgba(124,58,237,.1);border:none;border-radius:6px;padding:4px 7px;font-family:'Inter',sans-serif;cursor:pointer;display:inline-flex;align-items:center" title="View QR Code">
            <i class="bi bi-qr-code" style="font-size:13px"></i>
          </button>
        </td>
        @if(!$user->isReadOnlyViewer())
        <td style="width:1%;white-space:nowrap">
          <div style="display:flex;align-items:center;gap:4px;flex-wrap:nowrap">
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
              <button type="submit"
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
</div>{{-- #nitLiveResults --}}
@endif

@if($user->isAdminOrFinance())
{{-- ══ IMPORT EXCEL MODAL ══ --}}
<div id="nitImportModal" style="display:none;position:fixed;inset:0;z-index:9000;align-items:center;justify-content:center;background:rgba(15,23,42,.55);backdrop-filter:blur(3px);padding:1rem">
  <div style="background:var(--surface);border-radius:16px;width:100%;max-width:720px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.2);display:flex;flex-direction:column">

    <div style="background:linear-gradient(135deg,var(--navy,#142b47),var(--navy-mid,#254a78));padding:20px 24px;border-radius:16px 16px 0 0;display:flex;align-items:center;gap:14px;flex-shrink:0">
      <div style="width:42px;height:42px;border-radius:10px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;font-size:20px">
        <i class="bi bi-file-earmark-excel-fill" style="color:#22c55e"></i>
      </div>
      <div style="flex:1">
        <div style="color:#fff;font-size:16px;font-weight:700">Import Non-IT Assets from Excel</div>
        <div style="color:rgba(255,255,255,.6);font-size:12px;margin-top:2px">Upload an .xlsx, .xls or .csv file to bulk-import assets</div>
      </div>
      <button onclick="closeImportModal('nitImportModal','nitImportFileInput','nitImportPreview','nitImportStatus')" style="background:rgba(255,255,255,.12);border:none;border-radius:8px;color:rgba(255,255,255,.8);width:32px;height:32px;cursor:pointer;font-size:18px;display:flex;align-items:center;justify-content:center">&times;</button>
    </div>

    <div style="padding:24px;flex:1">
      <div style="background:rgba(2,132,199,.06);border:1.5px solid rgba(2,132,199,.2);border-radius:10px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;gap:12px">
        <i class="bi bi-info-circle-fill" style="color:var(--accent,#0284c7);font-size:18px;flex-shrink:0"></i>
        <div style="flex:1;font-size:13px;color:var(--text)"><strong>First time?</strong> Download the template to see the correct column format.</div>
        <a href="{{ route('it.non-it.import-template') }}" download
          style="display:inline-flex;align-items:center;gap:6px;background:var(--navy,#142b47);color:#fff;border-radius:7px;padding:7px 14px;font-size:12px;font-weight:700;text-decoration:none;white-space:nowrap;flex-shrink:0">
          <i class="bi bi-download"></i> Download Template
        </a>
      </div>

      <div style="margin-bottom:16px">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:8px">Expected Columns</div>
        <div style="display:flex;flex-wrap:wrap;gap:6px">
          @foreach(['Asset Number','Asset Class','F/A Code','Description','Location','Status','Condition','Date Registered','Years Purchase','Total Cost','Accumulated','NBV AT','Notes'] as $col)
          <span style="background:rgba(2,132,199,.08);color:var(--accent,#0284c7);border-radius:5px;padding:3px 10px;font-size:11px;font-weight:600">{{ $col }}</span>
          @endforeach
        </div>
        <div style="font-size:11px;color:var(--muted);margin-top:6px">Header row is always skipped &nbsp;·&nbsp; Columns are matched by keyword — unrecognised columns are ignored</div>
      </div>

      <div id="nitImportDropzone" onclick="document.getElementById('nitImportFileInput').click()"
        style="border:2px dashed var(--border);border-radius:12px;padding:32px 20px;text-align:center;cursor:pointer;transition:all .2s;margin-bottom:16px"
        ondragover="event.preventDefault();this.style.borderColor='var(--accent)';this.style.background='rgba(2,132,199,.04)'"
        ondragleave="this.style.borderColor='var(--border)';this.style.background=''"
        ondrop="event.preventDefault();this.style.borderColor='var(--border)';this.style.background='';handleImportFile(event.dataTransfer.files[0])">
        <i class="bi bi-cloud-upload-fill" style="font-size:36px;color:var(--muted);display:block;margin-bottom:10px;opacity:.5"></i>
        <div style="font-size:14px;font-weight:600;color:var(--text);margin-bottom:4px">Drop your file here or click to browse</div>
        <div style="font-size:12px;color:var(--muted)">.xlsx, .xls, .csv — max 5 000 rows</div>
        <input type="file" id="nitImportFileInput" accept=".xlsx,.xls,.csv" style="display:none"
          onchange="handleImportFile(this.files[0])">
      </div>

      <div id="nitImportPreview" style="display:none">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:8px">Preview (first 5 rows)</div>
        <div style="overflow-x:auto;border-radius:8px;border:1px solid var(--border)">
          <table id="nitImportPreviewTable" style="width:100%;border-collapse:collapse;font-size:12px;font-family:'Inter',sans-serif"></table>
        </div>
        <div id="nitImportRowCount" style="font-size:12px;color:var(--muted);margin-top:8px"></div>
      </div>

      <div id="nitImportStatus" style="display:none;margin-top:16px"></div>
    </div>

    <div style="background:var(--body-bg,#f1f5f9);border-top:1px solid var(--border);padding:16px 24px;border-radius:0 0 16px 16px;display:flex;align-items:center;gap:10px;flex-shrink:0">
      <button id="nitImportSubmitBtn" onclick="submitNitImport()"
        style="display:none;background:var(--navy,#142b47);color:#fff;border:none;border-radius:8px;padding:10px 24px;font-size:13.5px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif;align-items:center;gap:7px">
        <i class="bi bi-upload"></i> Import Assets
      </button>
      <button onclick="closeImportModal('nitImportModal','nitImportFileInput','nitImportPreview','nitImportStatus')"
        style="background:var(--surface);color:var(--text);border:1.5px solid var(--border);border-radius:8px;padding:10px 20px;font-size:13.5px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif">
        Cancel
      </button>
    </div>
  </div>
</div>
@endif

{{-- ══ NON-IT ASSET QR MODAL ══ --}}
<div id="nitQrModal" onclick="if(event.target===this)closeNitQRModal()"
  style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,.65);z-index:999999;align-items:center;justify-content:center;padding:20px">
  <div style="background:var(--surface);border-radius:16px;max-width:400px;width:100%;box-shadow:0 24px 60px rgba(0,0,0,.4)">
    <div style="background:var(--surface);border-radius:16px 16px 0 0;padding:16px 20px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border)">
      <div style="display:flex;align-items:center;gap:9px">
        <i class="bi bi-qr-code" style="color:#7c3aed;font-size:16px"></i>
        <div style="font-size:14px;font-weight:700;color:var(--text)">Asset QR Code</div>
      </div>
      <button onclick="closeNitQRModal()" style="background:none;border:none;color:#9ca3af;font-size:20px;cursor:pointer;line-height:1">&times;</button>
    </div>
    <div style="padding:22px">
      <div id="nitQrModalAssetId" style="font-size:18px;font-weight:800;color:var(--text);font-family:'Inter',sans-serif;line-height:1"></div>
      <div id="nitQrModalDesc" style="font-size:12px;color:var(--muted);margin-top:3px;margin-bottom:16px"></div>
      <div style="background:var(--body-bg);border:1px solid var(--border);border-radius:12px;padding:18px;display:flex;flex-direction:column;align-items:center;gap:10px;margin-bottom:16px">
        <div id="nitQrModalCode" style="background:#fff;padding:10px;border-radius:8px;display:flex;align-items:center;justify-content:center;min-height:150px"></div>
        <div style="font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.07em">Scan to view asset details</div>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
        <button onclick="printNitQR()"
          style="display:flex;align-items:center;justify-content:center;gap:7px;background:var(--body-bg);color:var(--text);border:1.5px solid var(--border);border-radius:9px;padding:10px;font-size:13px;font-weight:700;cursor:pointer;font-family:'Inter',sans-serif">
          <i class="bi bi-printer-fill"></i> Print QR
        </button>
        <button id="nitQrOpenBtn"
          style="display:flex;align-items:center;justify-content:center;gap:7px;background:var(--accent);color:#fff;border:none;border-radius:9px;padding:10px;font-size:13px;font-weight:700;cursor:pointer;font-family:'Inter',sans-serif">
          <i class="bi bi-box-arrow-up-right"></i> Open Page
        </button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
// ── Items data for JS edit form ──
var _nitItems = @json($items->keyBy('id'));

// ── Status/Condition tile selector ──
function selectOpt(el, gridId, hiddenId, val) {
  document.querySelectorAll('#' + gridId + ' .nit-select-opt').forEach(function(o) {
    o.classList.remove('selected');
    o.style.borderColor = '';
    o.style.background  = '';
    o.style.color       = '';
    var ic = o.querySelector('i'); if (ic) ic.style.color = '';
  });
  var clr = el.dataset.color || 'var(--accent)';
  var bg  = el.dataset.bg    || 'rgba(2,132,199,.1)';
  el.classList.add('selected');
  el.style.borderColor = clr;
  el.style.background  = bg;
  el.style.color       = clr;
  var ic = el.querySelector('i'); if (ic) ic.style.color = clr;
  document.getElementById(hiddenId).value = val;
}

function nitSetGrid(gridId, hiddenId, val) {
  document.querySelectorAll('#' + gridId + ' .nit-select-opt').forEach(function(o) {
    o.classList.remove('selected');
    o.style.borderColor = o.style.background = o.style.color = '';
    var ic = o.querySelector('i'); if (ic) ic.style.color = '';
  });
  document.querySelectorAll('#' + gridId + ' .nit-select-opt').forEach(function(o) {
    var onclick = o.getAttribute('onclick') || '';
    var match = onclick.match(/'([^']+)'\s*\)$/);
    if (match && match[1] === val) {
      var clr = o.dataset.color || 'var(--accent)';
      var bg  = o.dataset.bg    || 'rgba(2,132,199,.1)';
      o.classList.add('selected');
      o.style.borderColor = clr; o.style.background = bg; o.style.color = clr;
      var ic = o.querySelector('i'); if (ic) ic.style.color = clr;
    }
  });
  document.getElementById(hiddenId).value = val;
}

// ── Add form ──
function openNitAddForm() {
  document.getElementById('nitAddFormSection').style.display = 'flex';
}
function closeNitAddForm() {
  document.getElementById('nitAddFormSection').style.display = 'none';
}

// ── Edit form ──
function openNitEditFormById(id) {
  var d = _nitItems[id]; if (!d) return;
  var isAdmin = {{ $user->isAdminOrFinance() ? 'true' : 'false' }};

  document.getElementById('nitEditFormEl').action = '{{ url("it/non-it-assets") }}/' + id;
  document.getElementById('nef_asset_number').value    = d.asset_number  || '';
  document.getElementById('nef_fa_code').value         = d.fa_code       || '';
  document.getElementById('nef_description').value     = d.description   || '';
  document.getElementById('nef_brand').value           = d.brand         || '';
  document.getElementById('nef_location').value        = d.location      || '';
  document.getElementById('nef_notes').value           = d.notes         || '';
  document.getElementById('nef_warranty_date').value   = d.warranty_date ? d.warranty_date.substring(0,10) : '';
  document.getElementById('nef_years_purchase').value  = d.years_purchase || '';
  document.getElementById('nef_total_cost').value      = d.total_cost    !== null ? d.total_cost    : '';
  document.getElementById('nef_accumulated').value     = d.accumulated   !== null ? d.accumulated   : '';
  document.getElementById('nef_nbv_at').value          = d.nbv_at        !== null ? d.nbv_at        : '';
  if (d.date_registered) {
    var dr = typeof d.date_registered === 'string' ? d.date_registered.substring(0,10) : '';
    document.getElementById('nef_date_registered').value = dr;
  }
  var acSel = document.getElementById('nef_asset_class');
  for (var i=0;i<acSel.options.length;i++) {
    if (acSel.options[i].value === d.asset_class) { acSel.selectedIndex = i; break; }
  }

  nitSetGrid('editCondGrid',   'edit_condition_status_val', d.condition_status || 'Good');

  if (!isAdmin) {
    document.getElementById('nitEditFormTitle').textContent = 'Request to Edit Non-IT Asset';
    document.getElementById('nitEditFormSub').textContent   = 'Your changes will be reviewed before applying';
    document.getElementById('nitEditFormIcon').className    = 'bi bi-send';
    document.getElementById('nitEditSubmitBtn').innerHTML   = '<i class="bi bi-send"></i> Submit Edit Request';
  } else {
    document.getElementById('nitEditFormTitle').textContent = 'Edit Non-IT Asset';
    document.getElementById('nitEditFormSub').textContent   = 'Update asset details below';
    document.getElementById('nitEditFormIcon').className    = 'bi bi-pencil-square';
    document.getElementById('nitEditSubmitBtn').innerHTML   = '<i class="bi bi-check-lg"></i> Save Changes';
  }

  if (d.updated_at) {
    var dt = new Date(d.updated_at);
    document.getElementById('nitEditLastUpdated').innerHTML =
      '<i class="bi bi-clock"></i> Last updated ' + dt.toLocaleDateString('en-GB',{day:'2-digit',month:'short',year:'numeric'}) + ', ' + dt.toLocaleTimeString('en-GB',{hour:'2-digit',minute:'2-digit'});
  } else {
    document.getElementById('nitEditLastUpdated').innerHTML = '';
  }

  document.getElementById('nitEditFormSection').style.display = '';
  document.getElementById('nitHeaderBtns') && (document.getElementById('nitHeaderBtns').style.display = 'none');
  document.getElementById('nitEditFormSection').scrollIntoView({behavior:'smooth',block:'start'});
}
function closeNitEditForm() {
  document.getElementById('nitEditFormSection').style.display = 'none';
  document.getElementById('nitHeaderBtns') && (document.getElementById('nitHeaderBtns').style.display = 'flex');
}

// ── Bulk selection ──
const nitSelectedIds = new Set();

function nitSyncCheckboxes() {
  var rows = document.querySelectorAll('tbody .nit-row-check');
  var selectAll = document.getElementById('nitSelectAll');
  if (!selectAll) return;
  var checked = 0;
  rows.forEach(function(cb){ cb.checked = nitSelectedIds.has(cb.value); if (cb.checked) checked++; });
  selectAll.checked       = rows.length > 0 && checked === rows.length;
  selectAll.indeterminate = checked > 0 && checked < rows.length;
}

document.addEventListener('change', function(e) {
  if (e.target.classList.contains('nit-row-check')) {
    if (e.target.checked) nitSelectedIds.add(e.target.value);
    else nitSelectedIds.delete(e.target.value);
    nitSyncCheckboxes(); nitUpdateBulkBar();
  }
  if (e.target.id === 'nitSelectAll') {
    document.querySelectorAll('tbody .nit-row-check').forEach(function(cb){
      cb.checked = e.target.checked;
      if (e.target.checked) nitSelectedIds.add(cb.value);
      else nitSelectedIds.delete(cb.value);
    });
    nitUpdateBulkBar();
  }
});

function nitUpdateBulkBar() {
  var bar = document.getElementById('nitBulkBar'); if (!bar) return;
  var count = nitSelectedIds.size;
  document.getElementById('nitBulkCount').textContent = count;
  bar.style.display = count > 0 ? 'block' : 'none';
}

function nitClearSelection() {
  nitSelectedIds.clear();
  var sa = document.getElementById('nitSelectAll');
  if (sa) { sa.checked = false; sa.indeterminate = false; }
  document.querySelectorAll('tbody .nit-row-check').forEach(function(cb){ cb.checked = false; });
  nitUpdateBulkBar();
}

function nitSubmitBulk(action) {
  if (!nitSelectedIds.size) return;
  if (!confirm('Delete ' + nitSelectedIds.size + ' selected asset(s)? This cannot be undone.')) return;
  var form = document.getElementById('nitBulkDeleteForm');
  var container = document.getElementById('nit_delete_ids');
  container.innerHTML = '';
  nitSelectedIds.forEach(function(id){
    var inp = document.createElement('input');
    inp.type = 'hidden'; inp.name = 'selected_ids[]'; inp.value = id;
    container.appendChild(inp);
  });
  form.submit();
}

function nitSubmitBulkDispose() {
  if (!nitSelectedIds.size) return;
  if (!confirm('Send ' + nitSelectedIds.size + ' selected asset(s) to the write-off / dispose form?')) return;
  var ids = Array.from(nitSelectedIds).join(',');
  window.location.href = '{{ route('it.writeoff.index') }}?bulk_nit_ids=' + ids;
}

// ── Search Autocomplete ──
(function() {
  var inp = document.getElementById('nitSearchInput');
  var box = document.getElementById('nitSearchSuggestions');
  if (!inp || !box) return;
  var timer;
  inp.addEventListener('input', function() {
    clearTimeout(timer);
    var q = this.value.trim();
    if (q.length < 1) { box.style.display = 'none'; return; }
    timer = setTimeout(function() {
      fetch('{{ route('it.non-it.suggestions') }}?q=' + encodeURIComponent(q))
        .then(function(r) { return r.json(); })
        .then(function(items) {
          if (!items.length) { box.style.display = 'none'; return; }
          box.innerHTML = items.map(function(d) {
            return '<div class="nit-ac-item" style="padding:9px 14px;font-size:13px;cursor:pointer;font-family:\'Inter\',sans-serif;color:var(--text);border-bottom:1px solid var(--border)">' + String(d).replace(/</g, '&lt;') + '</div>';
          }).join('');
          var last = box.querySelector('.nit-ac-item:last-child');
          if (last) last.style.borderBottom = 'none';
          box.style.display = 'block';
        })
        .catch(function() { box.style.display = 'none'; });
    }, 220);
  });
  box.addEventListener('click', function(e) {
    var item = e.target.closest('.nit-ac-item');
    if (item) { inp.value = item.textContent; box.style.display = 'none'; inp.form.submit(); }
  });
  box.addEventListener('mouseover', function(e) {
    var item = e.target.closest('.nit-ac-item');
    if (item) item.style.background = '#f1f5f9';
  });
  box.addEventListener('mouseout', function(e) {
    var item = e.target.closest('.nit-ac-item');
    if (item) item.style.background = '';
  });
  document.addEventListener('click', function(e) {
    if (!inp.contains(e.target) && !box.contains(e.target)) box.style.display = 'none';
  });
})();

// ── QR Modal ──
var _nitQrAssetURL = '';

function openNitQRModal(id, assetNo, desc, faCode, location) {
  _nitQrAssetURL = '{{ url("it/non-it-assets") }}/' + id;
  document.getElementById('nitQrModalAssetId').textContent = assetNo + (faCode ? ' · ' + faCode : '');
  document.getElementById('nitQrModalDesc').textContent    = desc;
  document.getElementById('nitQrOpenBtn').onclick = function(){ window.open(_nitQrAssetURL, '_blank'); };
  var lines = [
    'FJB JOHOR BULKERS SDN BHD','------------------------------',
    'ASET NO: ' + assetNo, 'DESC: ' + desc,
  ];
  if (faCode)   lines.push('F/A CODE: ' + faCode);
  if (location) lines.push('LOC: '      + location);
  lines.push('------------------------------');
  lines.push('FGV JOHOR BULKERS SDN BHD');
  var container = document.getElementById('nitQrModalCode');
  container.innerHTML = '';
  new QRCode(container, {
    text: lines.join('\n'), width: 150, height: 150,
    colorDark: '#1a2332', colorLight: '#ffffff',
    correctLevel: QRCode.CorrectLevel.M
  });
  document.getElementById('nitQrModal').style.display = 'flex';
  var sb = document.querySelector('.sidebar'), tb = document.querySelector('.topbar');
  if (sb) sb.style.zIndex = '0';
  if (tb) tb.style.zIndex = '0';
}

function closeNitQRModal() {
  document.getElementById('nitQrModal').style.display = 'none';
  var sb = document.querySelector('.sidebar'), tb = document.querySelector('.topbar');
  if (sb) sb.style.zIndex = '';
  if (tb) tb.style.zIndex = '';
}

function printNitQR() {
  var img = document.querySelector('#nitQrModalCode img');
  if (!img) { alert('QR code not ready yet, please wait a moment.'); return; }
  var assetId   = document.getElementById('nitQrModalAssetId').textContent;
  var assetDesc = document.getElementById('nitQrModalDesc').textContent;
  var win = window.open('','_blank','width=400,height=500');
  win.document.write('<html><head><title>Print QR</title></head><body style="text-align:center;font-family:sans-serif;padding:20px">'
    + '<div style="font-size:16px;font-weight:700;margin-bottom:4px">'+assetId+'</div>'
    + '<div style="font-size:12px;color:#666;margin-bottom:16px">'+assetDesc+'</div>'
    + '<img src="'+img.src+'" style="width:200px;height:200px">'
    + '</body></html>');
  win.document.close();
  win.focus(); win.print(); win.close();
}

// ── Import Excel ──
var _importRows = [];

function mapHeaderNit(h) {
  h = String(h).toLowerCase().trim();
  var exact = {
    'asset number':'asset_number','asset no':'asset_number','asset no.':'asset_number',
    'asset#':'asset_number','asset num':'asset_number',
    'f/a code':'fa_code','fa code':'fa_code','fa_code':'fa_code','fixed asset code':'fa_code',
    'f/a':'fa_code','fixed asset no':'fa_code','fixed asset number':'fa_code',
    'asset class':'asset_class','class':'asset_class','category':'asset_class',
    'type':'asset_class','item type':'asset_class',
    'description':'description','desc':'description','name':'description',
    'item description':'description','asset description':'description','details':'description',
    'location':'location','loc':'location','place':'location','room':'location',
    'area':'location','department':'location','dept':'location',
    'status':'item_status','item status':'item_status','asset status':'item_status',
    'condition':'condition_status','condition status':'condition_status',
    'date registered':'date_registered','registration date':'date_registered',
    'reg date':'date_registered','registered date':'date_registered',
    'acquired date':'date_registered','date':'date_registered',
    'years purchase':'years_purchase','year purchase':'years_purchase','purchase year':'years_purchase',
    'years of purchase':'years_purchase','year of purchase':'years_purchase',
    'total cost':'total_cost','cost':'total_cost','purchase price':'total_cost','price':'total_cost',
    'accumulated':'accumulated','accumulated depreciation':'accumulated','accum':'accumulated','accum.':'accumulated',
    'nbv at':'nbv_at','nbv':'nbv_at','net book value':'nbv_at','net book value at':'nbv_at',
    'notes':'notes','note':'notes','remarks':'notes','remark':'notes',
    'comment':'notes','comments':'notes','memo':'notes',
  };
  if (exact[h]) return exact[h];
  if (h.includes('f/a') || (h.includes('fixed') && h.includes('asset'))) return 'fa_code';
  if (h.includes('nbv'))                                                   return 'nbv_at';
  if (h.includes('accum'))                                                 return 'accumulated';
  if (h.includes('total') && (h.includes('cost') || h.includes('price'))) return 'total_cost';
  if (h.includes('year') && (h.includes('purch') || h.includes('buy')))   return 'years_purchase';
  if (h.includes('desc'))                                                  return 'description';
  if (h.includes('class') || h.includes('categ'))                         return 'asset_class';
  if (h.includes('asset') && (h.includes('no') || h.includes('num') || h.includes('#'))) return 'asset_number';
  if (h.includes('locat') || h.includes('room') || h.includes('floor'))  return 'location';
  if (h.includes('status'))                                               return 'item_status';
  if (h.includes('condition'))                                            return 'condition_status';
  if (h.includes('date'))                                                 return 'date_registered';
  if (h.includes('note') || h.includes('remark') || h.includes('comment')) return 'notes';
  return null;
}

function handleImportFile(file) {
  if (!file) return;
  document.getElementById('nitImportStatus').style.display = 'none';
  document.getElementById('nitImportPreview').style.display = 'none';
  document.getElementById('nitImportSubmitBtn').style.display = 'none';
  var reader = new FileReader();
  reader.onload = function(e) {
    try {
      var wb = XLSX.read(e.target.result, {type:'array', cellDates:true});
      var ws = wb.Sheets[wb.SheetNames[0]];
      var rawRows = XLSX.utils.sheet_to_json(ws, {header:1, defval:''});
      if (rawRows.length < 2) { showNitImportError('File has no data rows.'); return; }
      var headerRowIdx = 0;
      for (var r=0; r<Math.min(rawRows.length,15); r++) {
        var row = rawRows[r];
        var nonEmpty = row.filter(function(c){ return c!==''&&c!==null&&c!==undefined; });
        if (nonEmpty.length >= 2) {
          var hasKnown = row.some(function(h){ return mapHeaderNit(String(h)) !== null; });
          if (hasKnown) { headerRowIdx = r; break; }
        }
      }
      var headers = rawRows[headerRowIdx].map(function(h){ return String(h).trim(); });
      var localColMap = {};
      headers.forEach(function(h,i){
        var field = mapHeaderNit(h);
        if (field && !localColMap.hasOwnProperty(field)) localColMap[field] = i;
      });
      _importRows = [];
      for (var r=headerRowIdx+1; r<rawRows.length; r++) {
        var raw = rawRows[r];
        if (raw.every(function(c){ return c===''||c===null||c===undefined; })) continue;
        var obj = {};
        Object.keys(localColMap).forEach(function(key){
          var val = raw[localColMap[key]];
          if (val instanceof Date) {
            obj[key] = val.getFullYear()+'-'+String(val.getMonth()+1).padStart(2,'0')+'-'+String(val.getDate()).padStart(2,'0');
          } else { obj[key] = val!==undefined ? String(val).trim() : ''; }
        });
        _importRows.push(obj);
      }
      if (_importRows.length === 0) { showNitImportError('No data rows found after header.'); return; }
      var tbl = document.getElementById('nitImportPreviewTable');
      var displayKeys = Object.keys(localColMap);
      tbl.innerHTML = '<tr style="background:#f8fafc">'+displayKeys.map(function(k){
        return '<th style="padding:8px 12px;border-bottom:1px solid #e2e8f0;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#64748b;white-space:nowrap">'+k.replace(/_/g,' ')+'</th>';
      }).join('')+'</tr>'+_importRows.slice(0,5).map(function(row){
        return '<tr>'+displayKeys.map(function(k){
          return '<td style="padding:8px 12px;border-bottom:1px solid #f1f5f9;font-size:12px;color:#1e293b;white-space:nowrap">'+escHtml(row[k]||'—')+'</td>';
        }).join('')+'</tr>';
      }).join('');
      document.getElementById('nitImportRowCount').textContent = _importRows.length+' row'+(_importRows.length!==1?'s':'')+' ready to import'+(_importRows.length>5?' (showing first 5)':'');
      document.getElementById('nitImportPreview').style.display = 'block';
      document.getElementById('nitImportSubmitBtn').style.display = 'inline-flex';
    } catch(err) { showNitImportError('Could not read file: '+err.message); }
  };
  reader.readAsArrayBuffer(file);
}

function submitNitImport() {
  if (_importRows.length === 0) return;
  var btn = document.getElementById('nitImportSubmitBtn');
  btn.disabled = true; btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Importing...';
  fetch('{{ route("it.non-it.import-excel") }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
    },
    body: JSON.stringify(_importRows)
  })
  .then(function(r){ return r.json(); })
  .then(function(res){
    var html = '';
    if (res.inserted > 0) html += '<div style="background:#dcfce7;border:1px solid #bbf7d0;border-radius:8px;padding:12px 16px;display:flex;align-items:center;gap:10px;margin-bottom:10px"><i class="bi bi-check-circle-fill" style="color:#16a34a;font-size:18px;flex-shrink:0"></i><div><strong style="color:#166534">'+res.inserted+' asset'+(res.inserted!==1?'s':'')+' imported successfully!</strong>'+(res.skipped>0?' <span style="color:#64748b;font-size:12px">('+res.skipped+' skipped)</span>':'')+'</div></div>';
    if (res.skipped > 0 && res.inserted === 0) html += '<div style="background:#fee2e2;border:1px solid #fecaca;border-radius:8px;padding:12px 16px;margin-bottom:10px"><strong style="color:#991b1b">No assets imported.</strong> All '+res.skipped+' rows skipped.</div>';
    if (res.errors && res.errors.length > 0) html += '<div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:8px;padding:12px 16px;max-height:160px;overflow-y:auto"><div style="font-size:12px;font-weight:700;color:#92400e;margin-bottom:6px">Issues ('+res.errors.length+')</div>'+res.errors.map(function(e){ return '<div style="font-size:12px;color:#92400e;padding:2px 0">• '+escHtml(e)+'</div>'; }).join('')+'</div>';
    var s = document.getElementById('nitImportStatus'); s.innerHTML = html; s.style.display = 'block';
    btn.disabled = false; btn.innerHTML = '<i class="bi bi-upload"></i> Import Assets';
    if (res.inserted > 0) setTimeout(function(){ location.reload(); }, 2000);
  })
  .catch(function(err){
    showNitImportError('Request failed: '+err.message);
    btn.disabled = false; btn.innerHTML = '<i class="bi bi-upload"></i> Import Assets';
  });
}

function closeImportModal(modalId,fileInputId,previewId,statusId) {
  document.getElementById(modalId).style.display = 'none';
  document.getElementById(fileInputId).value = '';
  document.getElementById(previewId).style.display = 'none';
  document.getElementById(statusId).style.display = 'none';
  document.getElementById('nitImportSubmitBtn').style.display = 'none';
  _importRows = [];
}

function showNitImportError(msg) {
  var s = document.getElementById('nitImportStatus');
  s.innerHTML = '<div style="background:#fee2e2;border:1px solid #fecaca;border-radius:8px;padding:12px 16px;color:#991b1b;font-size:13px"><i class="bi bi-x-circle-fill me-2"></i>'+escHtml(msg)+'</div>';
  s.style.display = 'block';
}

function escHtml(str){ return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

// Live search
(function () {
  var input   = document.getElementById('nitSearchInput');
  var results = document.getElementById('nitLiveResults');
  var form    = document.getElementById('nitFilterForm');
  if (!input || !results || !form) return;
  var timer;
  input.addEventListener('input', function () {
    clearTimeout(timer);
    timer = setTimeout(function () {
      var params = new URLSearchParams();
      params.set('nit_search', input.value.trim());
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

