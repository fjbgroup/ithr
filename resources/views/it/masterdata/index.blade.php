@extends('it.layouts.app')

@section('title', 'Masterdata')
@section('page_title', 'Masterdata')

@section('content')

@push('styles')
<style>
/* ── Tab Bar ── */
.md-tab-bar{display:flex;gap:4px;background:var(--body-bg);border:1px solid var(--border);border-radius:10px;padding:4px;margin-bottom:24px;width:fit-content}
.md-tab{display:inline-flex;align-items:center;gap:7px;padding:9px 20px;border-radius:7px;font-size:13px;font-weight:600;cursor:pointer;border:none;font-family:'Inter',sans-serif;transition:all .15s;color:var(--muted);background:transparent}
.md-tab:hover{color:var(--text);background:rgba(0,0,0,.04)}
.md-tab.active{background:var(--surface);color:var(--navy,#142b47);box-shadow:0 1px 4px rgba(0,0,0,.1)}
.md-tab i{font-size:14px}

/* ── Shared card styles ── */
.ac-card{background:var(--surface);border:1px solid var(--border);border-radius:14px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.07),0 4px 14px rgba(0,0,0,.04)}
.ac-card-head{padding:18px 20px 14px;border-bottom:1px solid var(--border)}
.ac-card-title-row{display:flex;align-items:center;gap:10px;margin-bottom:14px}
.ac-card-accent{width:4px;height:28px;border-radius:2px;flex-shrink:0}
.ac-card-meta{flex:1}
.ac-card-meta h6{font-size:14px;font-weight:800;color:var(--text);margin:0;line-height:1.2}
.ac-card-meta small{font-size:11px;color:var(--muted)}
.ac-badge{border-radius:20px;padding:4px 12px;font-size:11px;font-weight:700;display:inline-flex;align-items:center;gap:5px;white-space:nowrap}
.ac-stat-row{display:grid;grid-template-columns:repeat(3,1fr);gap:8px}
.ac-stat-box{background:var(--body-bg);border:1px solid var(--border);border-radius:10px;padding:11px 14px;text-align:center}
.ac-stat-num{font-size:20px;font-weight:800;color:var(--text);line-height:1;font-family:'Inter',sans-serif}
.ac-stat-lbl{font-size:10px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.07em;margin-top:3px}
.ac-add-row{background:var(--body-bg);border-bottom:1px solid var(--border);padding:12px 20px;display:flex;align-items:center;gap:8px}
.ac-add-input{flex:1;border:1.5px solid var(--border);border-radius:8px;padding:8px 12px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;background:var(--surface);outline:none;transition:border-color .2s,box-shadow .2s;min-width:0}
.ac-add-input:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(2,132,199,.1)}
.ac-add-btn{display:inline-flex;align-items:center;gap:5px;border:none;border-radius:8px;padding:8px 16px;font-size:12px;font-weight:700;cursor:pointer;font-family:'Inter',sans-serif;white-space:nowrap;color:#fff}
.ac-table{width:100%;border-collapse:collapse}
.ac-table thead th{background:var(--body-bg);padding:9px 16px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.09em;color:var(--muted);border-bottom:1px solid var(--border);text-align:left;white-space:nowrap}
.ac-table tbody td{padding:11px 16px;border-bottom:1px solid var(--border);font-size:13px;vertical-align:middle;color:var(--text)}
.ac-table tbody tr:last-child td{border-bottom:none}
.ac-table tbody tr:hover td{background:var(--body-bg)}
.ac-class-pill{display:inline-flex;align-items:center;gap:6px;border-radius:7px;padding:4px 11px;border:1px solid}
.ac-count-pill{border-radius:20px;padding:3px 10px;font-size:11px;font-weight:700;border:1px solid}
.ac-status-pill{border-radius:20px;padding:3px 10px;font-size:11px;font-weight:700;display:inline-flex;align-items:center;gap:4px;border:1px solid}
.ac-btn-sm{display:inline-flex;align-items:center;justify-content:center;width:29px;height:29px;border-radius:7px;font-size:12px;cursor:pointer;text-decoration:none;border:1px solid transparent}
.btn-edit{background:rgba(37,99,235,.08);border-color:rgba(37,99,235,.2);color:#2563eb}
.btn-del{background:rgba(239,68,68,.08);border-color:rgba(239,68,68,.2);color:#dc2626}
.btn-del-dis{background:var(--body-bg);border-color:var(--border);color:var(--muted);cursor:not-allowed;opacity:.5}
.ac-edit-form{display:none;align-items:center;gap:6px}
.ac-edit-input{font-size:12px;font-weight:700;color:var(--text);background:var(--body-bg);border:1.5px solid var(--accent);border-radius:7px;padding:5px 10px;width:160px;font-family:'Inter',sans-serif;text-transform:uppercase;outline:none}
.ac-btn-save{font-size:11px;font-weight:700;color:#fff;background:var(--navy,#142b47);border:none;border-radius:6px;padding:5px 11px;cursor:pointer;font-family:'Inter',sans-serif}
.ac-btn-cancel{font-size:11px;font-weight:600;color:var(--muted);background:transparent;border:1.5px solid var(--border);border-radius:6px;padding:5px 9px;cursor:pointer;font-family:'Inter',sans-serif}
.ac-empty{padding:44px 20px;text-align:center;color:var(--muted)}
.ac-empty i{font-size:28px;display:block;margin-bottom:10px;opacity:.3}
.ac-empty p{font-size:13px;font-weight:600;margin:0}
.ac-empty span{font-size:12px;margin-top:4px;display:block}
</style>
@endpush

<!-- PAGE HEADER -->
<div style="margin-bottom:24px">
  <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:5px">
    Admin &rsaquo; <span style="color:var(--accent)">Masterdata</span>
  </div>
  <h4 style="font-family:'Inter',sans-serif;font-weight:800;font-size:22px;color:var(--text);margin:0">Masterdata</h4>
  <p style="font-size:13px;color:var(--muted);margin:4px 0 0">Manage asset classes, brands, and locations</p>
</div>

<!-- TAB BAR -->
<div class="md-tab-bar">
  <button class="md-tab {{ $tab === 'classes' ? 'active' : '' }}" onclick="switchTab('classes')">
    <i class="bi bi-tags-fill"></i> Asset Classes
  </button>
  <button class="md-tab {{ $tab === 'brands' ? 'active' : '' }}" onclick="switchTab('brands')">
    <i class="bi bi-bookmark-fill"></i> Brands
  </button>
  <button class="md-tab {{ $tab === 'locations' ? 'active' : '' }}" onclick="switchTab('locations')">
    <i class="bi bi-geo-alt-fill"></i> Locations
  </button>
</div>

{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
{{-- PANEL: Asset Classes --}}
{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
<div id="panel-classes" style="{{ $tab !== 'classes' ? 'display:none' : '' }}">
@php
  $itTotal  = $itClasses->count();
  $itUsed   = $itClasses->filter(fn($c) => $c->it_count > 0)->count();
  $itItems  = $itClasses->sum('it_count');
  $nitTotal = $nitClasses->count();
  $nitUsed  = $nitClasses->filter(fn($c) => $c->nit_count > 0)->count();
  $nitItems = $nitClasses->sum('nit_count');
@endphp
<div class="row g-3">

  {{-- IT Asset Classes --}}
  <div class="col-lg-6" id="itCard">
    <div class="ac-card">
      <div class="ac-card-head">
        <div class="ac-card-title-row">
          <div class="ac-card-accent" style="background:#0284c7"></div>
          <div class="ac-card-meta">
            <h6>IT Asset Classes</h6>
            <small>Used in IT Assets &amp; E-Waste</small>
          </div>
          <span class="ac-badge" style="background:rgba(2,132,199,.1);color:#0284c7;border:1px solid rgba(2,132,199,.2)">
            <i class="bi bi-box-seam-fill" style="font-size:10px"></i> IT
          </span>
        </div>
        <div class="ac-stat-row">
          <div class="ac-stat-box" style="background:rgba(2,132,199,.08);border-color:rgba(2,132,199,.2)">
            <div class="ac-stat-num" style="color:#0284c7">{{ $itTotal }}</div>
            <div class="ac-stat-lbl">Classes</div>
          </div>
          <div class="ac-stat-box" style="background:rgba(22,163,74,.07);border-color:rgba(22,163,74,.15)">
            <div class="ac-stat-num" style="color:#16a34a">{{ $itItems }}</div>
            <div class="ac-stat-lbl">IT Assets</div>
          </div>
          <div class="ac-stat-box">
            <div class="ac-stat-num" style="color:#64748b">{{ $itTotal - $itUsed }}</div>
            <div class="ac-stat-lbl">Unused</div>
          </div>
        </div>
      </div>
      <form method="POST" action="{{ route('it.asset-classes.store') }}" class="ac-add-row">
        @csrf
        <input type="hidden" name="type" value="it">
        <input type="text" name="name" class="ac-add-input" placeholder="New class name, e.g. MONITOR" required
          oninput="this.value=this.value.toUpperCase()">
        <button type="submit" class="ac-add-btn" style="background:var(--navy,#142b47)">
          <i class="bi bi-plus-lg"></i> Add Class
        </button>
      </form>
      @if($itClasses->isEmpty())
      <div class="ac-empty"><i class="bi bi-tags"></i><p>No IT asset classes yet</p><span>Add a class above to get started</span></div>
      @else
      <div class="table-responsive">
        <table class="ac-table">
          <thead><tr><th style="width:32px">#</th><th>Asset Class</th><th>Items</th><th>Status</th><th style="width:72px;text-align:center">Actions</th></tr></thead>
          <tbody>
          @foreach($itClasses as $i => $cls)
          <tr>
            <td style="color:var(--muted);font-size:12px">{{ $i + 1 }}</td>
            <td>
              <div id="label-cls-{{ $cls->id }}" class="ac-class-pill" style="background:rgba(2,132,199,.08);border-color:rgba(2,132,199,.25)">
                <div style="width:6px;height:6px;border-radius:50%;background:#0284c7;flex-shrink:0"></div>
                <span style="font-size:12px;font-weight:700;color:#0284c7;letter-spacing:.04em">{{ $cls->name }}</span>
              </div>
              <form id="editform-cls-{{ $cls->id }}" method="POST" action="{{ route('it.asset-classes.update', $cls->id) }}" class="ac-edit-form">
                @csrf
                <input type="hidden" name="type" value="it">
                <input type="text" name="name" value="{{ $cls->name }}" required class="ac-edit-input" oninput="this.value=this.value.toUpperCase()">
                <button type="submit" class="ac-btn-save">Save</button>
                <button type="button" class="ac-btn-cancel" onclick="cancelEdit('cls-{{ $cls->id }}')">Cancel</button>
              </form>
            </td>
            <td>
              <span class="ac-count-pill" style="background:{{ $cls->it_count > 0 ? 'rgba(2,132,199,.08)' : 'var(--body-bg)' }};color:{{ $cls->it_count > 0 ? '#0284c7' : 'var(--muted)' }};border-color:{{ $cls->it_count > 0 ? 'rgba(2,132,199,.25)' : 'var(--border)' }}">
                {{ $cls->it_count }} item{{ $cls->it_count !== 1 ? 's' : '' }}
              </span>
            </td>
            <td>
              @if($cls->it_count > 0)
              <span class="ac-status-pill" style="background:rgba(22,163,74,.08);color:#15803d;border-color:rgba(22,163,74,.2)"><i class="bi bi-check-circle-fill" style="font-size:10px"></i> In Use</span>
              @else
              <span class="ac-status-pill" style="background:var(--body-bg);color:var(--muted);border-color:var(--border)"><i class="bi bi-dash-circle" style="font-size:10px"></i> Unused</span>
              @endif
            </td>
            <td>
              <div id="actions-cls-{{ $cls->id }}" style="display:flex;gap:5px;justify-content:center">
                <button class="ac-btn-sm btn-edit" onclick="startEdit('cls-{{ $cls->id }}')" title="Rename"><i class="bi bi-pencil-fill"></i></button>
                @if($cls->it_count === 0 && $cls->nit_count === 0)
                <form method="POST" action="{{ route('it.asset-classes.destroy', $cls->id) }}" style="display:inline"
                  onsubmit="return confirm('Remove class \'{{ addslashes($cls->name) }}\'?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="ac-btn-sm btn-del" title="Delete"><i class="bi bi-trash-fill"></i></button>
                </form>
                @else
                <span class="ac-btn-sm btn-del-dis" title="In use — cannot delete"><i class="bi bi-trash-fill"></i></span>
                @endif
              </div>
            </td>
          </tr>
          @endforeach
          </tbody>
        </table>
      </div>
      @endif
    </div>
  </div>

  {{-- Non-IT Asset Classes --}}
  <div class="col-lg-6" id="nitCard">
    <div class="ac-card">
      <div class="ac-card-head">
        <div class="ac-card-title-row">
          <div class="ac-card-accent" style="background:#7c3aed"></div>
          <div class="ac-card-meta">
            <h6>Non-IT Asset Classes</h6>
            <small>Used in Non-IT Assets</small>
          </div>
          <span class="ac-badge" style="background:rgba(124,58,237,.1);color:#7c3aed;border:1px solid rgba(124,58,237,.2)">
            <i class="bi bi-boxes" style="font-size:10px"></i> Non-IT
          </span>
        </div>
        <div class="ac-stat-row">
          <div class="ac-stat-box" style="background:rgba(124,58,237,.08);border-color:rgba(124,58,237,.2)">
            <div class="ac-stat-num" style="color:#7c3aed">{{ $nitTotal }}</div>
            <div class="ac-stat-lbl">Classes</div>
          </div>
          <div class="ac-stat-box" style="background:rgba(22,163,74,.07);border-color:rgba(22,163,74,.15)">
            <div class="ac-stat-num" style="color:#16a34a">{{ $nitItems }}</div>
            <div class="ac-stat-lbl">Non-IT Assets</div>
          </div>
          <div class="ac-stat-box">
            <div class="ac-stat-num" style="color:#64748b">{{ $nitTotal - $nitUsed }}</div>
            <div class="ac-stat-lbl">Unused</div>
          </div>
        </div>
      </div>
      <form method="POST" action="{{ route('it.asset-classes.store') }}" class="ac-add-row">
        @csrf
        <input type="hidden" name="type" value="non_it">
        <input type="text" name="name" class="ac-add-input" placeholder="New class name, e.g. FURNITURE" required
          oninput="this.value=this.value.toUpperCase()">
        <button type="submit" class="ac-add-btn" style="background:#7c3aed">
          <i class="bi bi-plus-lg"></i> Add Class
        </button>
      </form>
      @if($nitClasses->isEmpty())
      <div class="ac-empty"><i class="bi bi-boxes"></i><p>No Non-IT asset classes yet</p><span>Add a class above to get started</span></div>
      @else
      <div class="table-responsive">
        <table class="ac-table">
          <thead><tr><th style="width:32px">#</th><th>Asset Class</th><th>Items</th><th>Status</th><th style="width:72px;text-align:center">Actions</th></tr></thead>
          <tbody>
          @foreach($nitClasses as $i => $cls)
          <tr>
            <td style="color:var(--muted);font-size:12px">{{ $i + 1 }}</td>
            <td>
              <div id="label-cls-{{ $cls->id }}" class="ac-class-pill" style="background:rgba(124,58,237,.08);border-color:rgba(124,58,237,.25)">
                <div style="width:6px;height:6px;border-radius:50%;background:#7c3aed;flex-shrink:0"></div>
                <span style="font-size:12px;font-weight:700;color:#7c3aed;letter-spacing:.04em">{{ $cls->name }}</span>
              </div>
              <form id="editform-cls-{{ $cls->id }}" method="POST" action="{{ route('it.asset-classes.update', $cls->id) }}" class="ac-edit-form">
                @csrf
                <input type="hidden" name="type" value="non_it">
                <input type="text" name="name" value="{{ $cls->name }}" required class="ac-edit-input" oninput="this.value=this.value.toUpperCase()">
                <button type="submit" class="ac-btn-save">Save</button>
                <button type="button" class="ac-btn-cancel" onclick="cancelEdit('cls-{{ $cls->id }}')">Cancel</button>
              </form>
            </td>
            <td>
              <span class="ac-count-pill" style="background:{{ $cls->nit_count > 0 ? 'rgba(124,58,237,.08)' : 'var(--body-bg)' }};color:{{ $cls->nit_count > 0 ? '#7c3aed' : 'var(--muted)' }};border-color:{{ $cls->nit_count > 0 ? 'rgba(124,58,237,.25)' : 'var(--border)' }}">
                {{ $cls->nit_count }} item{{ $cls->nit_count !== 1 ? 's' : '' }}
              </span>
            </td>
            <td>
              @if($cls->nit_count > 0)
              <span class="ac-status-pill" style="background:rgba(22,163,74,.08);color:#15803d;border-color:rgba(22,163,74,.2)"><i class="bi bi-check-circle-fill" style="font-size:10px"></i> In Use</span>
              @else
              <span class="ac-status-pill" style="background:var(--body-bg);color:var(--muted);border-color:var(--border)"><i class="bi bi-dash-circle" style="font-size:10px"></i> Unused</span>
              @endif
            </td>
            <td>
              <div id="actions-cls-{{ $cls->id }}" style="display:flex;gap:5px;justify-content:center">
                <button class="ac-btn-sm btn-edit" onclick="startEdit('cls-{{ $cls->id }}')" title="Rename"><i class="bi bi-pencil-fill"></i></button>
                @if($cls->it_count === 0 && $cls->nit_count === 0)
                <form method="POST" action="{{ route('it.asset-classes.destroy', $cls->id) }}" style="display:inline"
                  onsubmit="return confirm('Remove class \'{{ addslashes($cls->name) }}\'?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="ac-btn-sm btn-del" title="Delete"><i class="bi bi-trash-fill"></i></button>
                </form>
                @else
                <span class="ac-btn-sm btn-del-dis" title="In use — cannot delete"><i class="bi bi-trash-fill"></i></span>
                @endif
              </div>
            </td>
          </tr>
          @endforeach
          </tbody>
        </table>
      </div>
      @endif
    </div>
  </div>

</div>{{-- /row --}}
</div>{{-- /panel-classes --}}

{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
{{-- PANEL: Brands --}}
{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
<div id="panel-brands" style="{{ $tab !== 'brands' ? 'display:none' : '' }}">
@php
  $brandTotal    = $brands->count();
  $brandItItems  = $brands->sum('it_count');
  $brandNitItems = $brands->sum('nit_count');
@endphp
<div class="ac-card">
  <div class="ac-card-head">
    <div class="ac-card-title-row">
      <div class="ac-card-accent" style="background:#0284c7"></div>
      <div class="ac-card-meta">
        <h6>Asset Brands</h6>
        <small>Used in IT and Non-IT Assets</small>
      </div>
      <span class="ac-badge" style="background:rgba(2,132,199,.1);color:#0284c7;border:1px solid rgba(2,132,199,.2)">
        <i class="bi bi-bookmark-fill" style="font-size:10px"></i> Brands
      </span>
    </div>
    <div class="ac-stat-row">
      <div class="ac-stat-box" style="background:rgba(2,132,199,.08);border-color:rgba(2,132,199,.2)">
        <div class="ac-stat-num" style="color:#0284c7">{{ $brandTotal }}</div>
        <div class="ac-stat-lbl">Total Brands</div>
      </div>
      <div class="ac-stat-box" style="background:rgba(22,163,74,.07);border-color:rgba(22,163,74,.15)">
        <div class="ac-stat-num" style="color:#16a34a">{{ $brandItItems }}</div>
        <div class="ac-stat-lbl">IT Assets</div>
      </div>
      <div class="ac-stat-box" style="background:rgba(124,58,237,.07);border-color:rgba(124,58,237,.15)">
        <div class="ac-stat-num" style="color:#7c3aed">{{ $brandNitItems }}</div>
        <div class="ac-stat-lbl">Non-IT Assets</div>
      </div>
    </div>
  </div>
  <form method="POST" action="{{ route('it.brands.store') }}" class="ac-add-row">
    @csrf
    <input type="text" name="name" class="ac-add-input" placeholder="New brand name, e.g. HP" required
      oninput="this.value=this.value.toUpperCase()">
    <button type="submit" class="ac-add-btn" style="background:var(--navy,#142b47)">
      <i class="bi bi-plus-lg"></i> Add Brand
    </button>
  </form>
  @if($brands->isEmpty())
  <div class="ac-empty"><i class="bi bi-bookmark"></i><p>No brands yet</p><span>Add a brand above to get started</span></div>
  @else
  <div class="table-responsive">
    <table class="ac-table">
      <thead><tr><th style="width:32px">#</th><th>Brand</th><th>IT Assets</th><th>Non-IT Assets</th><th>Status</th><th style="width:72px;text-align:center">Actions</th></tr></thead>
      <tbody>
      @foreach($brands as $i => $brand)
      @php $brandInUse = ($brand->it_count + $brand->nit_count) > 0; @endphp
      <tr>
        <td style="color:var(--muted);font-size:12px">{{ $i + 1 }}</td>
        <td>
          <div id="label-brand-{{ $brand->id }}" class="ac-class-pill" style="background:rgba(2,132,199,.08);border-color:rgba(2,132,199,.25)">
            <div style="width:6px;height:6px;border-radius:50%;background:#0284c7;flex-shrink:0"></div>
            <span style="font-size:12px;font-weight:700;color:#0284c7;letter-spacing:.04em">{{ $brand->name }}</span>
          </div>
          <form id="editform-brand-{{ $brand->id }}" method="POST" action="{{ route('it.brands.update', $brand->id) }}" class="ac-edit-form">
            @csrf
            <input type="text" name="name" value="{{ $brand->name }}" required class="ac-edit-input" oninput="this.value=this.value.toUpperCase()">
            <button type="submit" class="ac-btn-save">Save</button>
            <button type="button" class="ac-btn-cancel" onclick="cancelEdit('brand-{{ $brand->id }}')">Cancel</button>
          </form>
        </td>
        <td>
          <span class="ac-count-pill" style="background:{{ $brand->it_count > 0 ? 'rgba(2,132,199,.08)' : 'var(--body-bg)' }};color:{{ $brand->it_count > 0 ? '#0284c7' : 'var(--muted)' }};border-color:{{ $brand->it_count > 0 ? 'rgba(2,132,199,.25)' : 'var(--border)' }}">
            {{ $brand->it_count }} item{{ $brand->it_count !== 1 ? 's' : '' }}
          </span>
        </td>
        <td>
          <span class="ac-count-pill" style="background:{{ $brand->nit_count > 0 ? 'rgba(124,58,237,.08)' : 'var(--body-bg)' }};color:{{ $brand->nit_count > 0 ? '#7c3aed' : 'var(--muted)' }};border-color:{{ $brand->nit_count > 0 ? 'rgba(124,58,237,.25)' : 'var(--border)' }}">
            {{ $brand->nit_count }} item{{ $brand->nit_count !== 1 ? 's' : '' }}
          </span>
        </td>
        <td>
          @if($brandInUse)
          <span class="ac-status-pill" style="background:rgba(22,163,74,.08);color:#15803d;border-color:rgba(22,163,74,.2)"><i class="bi bi-check-circle-fill" style="font-size:10px"></i> In Use</span>
          @else
          <span class="ac-status-pill" style="background:var(--body-bg);color:var(--muted);border-color:var(--border)"><i class="bi bi-dash-circle" style="font-size:10px"></i> Unused</span>
          @endif
        </td>
        <td>
          <div id="actions-brand-{{ $brand->id }}" style="display:flex;gap:5px;justify-content:center">
            <button class="ac-btn-sm btn-edit" onclick="startEdit('brand-{{ $brand->id }}')" title="Rename"><i class="bi bi-pencil-fill"></i></button>
            @if(!$brandInUse)
            <form method="POST" action="{{ route('it.brands.destroy', $brand->id) }}" style="display:inline"
              onsubmit="return confirm('Remove brand \'{{ addslashes($brand->name) }}\'?')">
              @csrf @method('DELETE')
              <button type="submit" class="ac-btn-sm btn-del" title="Delete"><i class="bi bi-trash-fill"></i></button>
            </form>
            @else
            <span class="ac-btn-sm btn-del-dis" title="In use — cannot delete"><i class="bi bi-trash-fill"></i></span>
            @endif
          </div>
        </td>
      </tr>
      @endforeach
      </tbody>
    </table>
  </div>
  @endif
</div>
</div>{{-- /panel-brands --}}

{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
{{-- PANEL: Locations --}}
{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
<div id="panel-locations" style="{{ $tab !== 'locations' ? 'display:none' : '' }}">
@php
  $locTotal    = $locations->count();
  $locItItems  = $locations->sum('it_count');
  $locNitItems = $locations->sum('nit_count');
@endphp
<div class="ac-card">
  <div class="ac-card-head">
    <div class="ac-card-title-row">
      <div class="ac-card-accent" style="background:#0d9488"></div>
      <div class="ac-card-meta">
        <h6>Asset Locations</h6>
        <small>Used in IT and Non-IT Assets</small>
      </div>
      <span class="ac-badge" style="background:rgba(13,148,136,.1);color:#0d9488;border:1px solid rgba(13,148,136,.2)">
        <i class="bi bi-geo-alt-fill" style="font-size:10px"></i> Locations
      </span>
    </div>
    <div class="ac-stat-row">
      <div class="ac-stat-box" style="background:rgba(13,148,136,.08);border-color:rgba(13,148,136,.2)">
        <div class="ac-stat-num" style="color:#0d9488">{{ $locTotal }}</div>
        <div class="ac-stat-lbl">Total Locations</div>
      </div>
      <div class="ac-stat-box" style="background:rgba(22,163,74,.07);border-color:rgba(22,163,74,.15)">
        <div class="ac-stat-num" style="color:#16a34a">{{ $locItItems }}</div>
        <div class="ac-stat-lbl">IT Assets</div>
      </div>
      <div class="ac-stat-box" style="background:rgba(124,58,237,.07);border-color:rgba(124,58,237,.15)">
        <div class="ac-stat-num" style="color:#7c3aed">{{ $locNitItems }}</div>
        <div class="ac-stat-lbl">Non-IT Assets</div>
      </div>
    </div>
  </div>
  <form method="POST" action="{{ route('it.locations.store') }}" class="ac-add-row">
    @csrf
    <input type="text" name="name" class="ac-add-input" placeholder="New location name, e.g. SERVER ROOM" required
      oninput="this.value=this.value.toUpperCase()">
    <button type="submit" class="ac-add-btn" style="background:#0d9488">
      <i class="bi bi-plus-lg"></i> Add Location
    </button>
  </form>
  @if($locations->isEmpty())
  <div class="ac-empty"><i class="bi bi-geo-alt"></i><p>No locations yet</p><span>Add a location above to get started</span></div>
  @else
  <div class="table-responsive">
    <table class="ac-table">
      <thead><tr><th style="width:32px">#</th><th>Location</th><th>IT Assets</th><th>Non-IT Assets</th><th>Status</th><th style="width:72px;text-align:center">Actions</th></tr></thead>
      <tbody>
      @foreach($locations as $i => $loc)
      @php $locInUse = ($loc->it_count + $loc->nit_count) > 0; @endphp
      <tr>
        <td style="color:var(--muted);font-size:12px">{{ $i + 1 }}</td>
        <td>
          <div id="label-loc-{{ $loc->id }}" class="ac-class-pill" style="background:rgba(13,148,136,.08);border-color:rgba(13,148,136,.25)">
            <div style="width:6px;height:6px;border-radius:50%;background:#0d9488;flex-shrink:0"></div>
            <span style="font-size:12px;font-weight:700;color:#0d9488;letter-spacing:.04em">{{ $loc->name }}</span>
          </div>
          <form id="editform-loc-{{ $loc->id }}" method="POST" action="{{ route('it.locations.update', $loc->id) }}" class="ac-edit-form">
            @csrf
            <input type="text" name="name" value="{{ $loc->name }}" required class="ac-edit-input" oninput="this.value=this.value.toUpperCase()">
            <button type="submit" class="ac-btn-save">Save</button>
            <button type="button" class="ac-btn-cancel" onclick="cancelEdit('loc-{{ $loc->id }}')">Cancel</button>
          </form>
        </td>
        <td>
          <span class="ac-count-pill" style="background:{{ $loc->it_count > 0 ? 'rgba(2,132,199,.08)' : 'var(--body-bg)' }};color:{{ $loc->it_count > 0 ? '#0284c7' : 'var(--muted)' }};border-color:{{ $loc->it_count > 0 ? 'rgba(2,132,199,.25)' : 'var(--border)' }}">
            {{ $loc->it_count }} item{{ $loc->it_count !== 1 ? 's' : '' }}
          </span>
        </td>
        <td>
          <span class="ac-count-pill" style="background:{{ $loc->nit_count > 0 ? 'rgba(124,58,237,.08)' : 'var(--body-bg)' }};color:{{ $loc->nit_count > 0 ? '#7c3aed' : 'var(--muted)' }};border-color:{{ $loc->nit_count > 0 ? 'rgba(124,58,237,.25)' : 'var(--border)' }}">
            {{ $loc->nit_count }} item{{ $loc->nit_count !== 1 ? 's' : '' }}
          </span>
        </td>
        <td>
          @if($locInUse)
          <span class="ac-status-pill" style="background:rgba(22,163,74,.08);color:#15803d;border-color:rgba(22,163,74,.2)"><i class="bi bi-check-circle-fill" style="font-size:10px"></i> In Use</span>
          @else
          <span class="ac-status-pill" style="background:var(--body-bg);color:var(--muted);border-color:var(--border)"><i class="bi bi-dash-circle" style="font-size:10px"></i> Unused</span>
          @endif
        </td>
        <td>
          <div id="actions-loc-{{ $loc->id }}" style="display:flex;gap:5px;justify-content:center">
            <button class="ac-btn-sm btn-edit" onclick="startEdit('loc-{{ $loc->id }}')" title="Rename"><i class="bi bi-pencil-fill"></i></button>
            @if(!$locInUse)
            <form method="POST" action="{{ route('it.locations.destroy', $loc->id) }}" style="display:inline"
              onsubmit="return confirm('Remove location \'{{ addslashes($loc->name) }}\'?')">
              @csrf @method('DELETE')
              <button type="submit" class="ac-btn-sm btn-del" title="Delete"><i class="bi bi-trash-fill"></i></button>
            </form>
            @else
            <span class="ac-btn-sm btn-del-dis" title="In use — cannot delete"><i class="bi bi-trash-fill"></i></span>
            @endif
          </div>
        </td>
      </tr>
      @endforeach
      </tbody>
    </table>
  </div>
  @endif
</div>
</div>{{-- /panel-locations --}}

@endsection

@push('scripts')
<script>
var _currentTab = '{{ $tab }}';
var _panels = ['classes','brands','locations'];

function switchTab(tab) {
  _panels.forEach(function(p) {
    document.getElementById('panel-'+p).style.display = p === tab ? '' : 'none';
  });
  document.querySelectorAll('.md-tab').forEach(function(btn) {
    btn.classList.toggle('active', btn.getAttribute('onclick') === "switchTab('"+tab+"')");
  });
  _currentTab = tab;
  var url = new URL(window.location.href);
  url.searchParams.set('tab', tab);
  history.replaceState(null, '', url.toString());
}

function startEdit(key) {
  document.getElementById('label-'+key).style.display='none';
  document.getElementById('editform-'+key).style.display='flex';
  document.getElementById('actions-'+key).style.display='none';
  document.querySelector('#editform-'+key+' input[name="name"]').focus();
}
function cancelEdit(key) {
  document.getElementById('label-'+key).style.display='inline-flex';
  document.getElementById('editform-'+key).style.display='none';
  document.getElementById('actions-'+key).style.display='flex';
}

// Scroll to itCard / nitCard after asset class add/rename (uses session scroll_target)
(function(){
  var t = '{{ session("scroll_target", "") }}';
  if (t) {
    var el = document.getElementById(t);
    if (el) { switchTab('classes'); el.scrollIntoView({behavior:'smooth',block:'start'}); }
  }
})();

// Stamp all CRUD forms with current tab so back() lands on the right panel
document.querySelectorAll('form[method="POST"]').forEach(function(f) {
  var url = new URL(f.action, window.location.href);
  url.searchParams.set('_from_tab', _currentTab);
  f.action = url.toString();
});
</script>
@endpush
