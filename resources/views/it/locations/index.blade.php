@extends('it.layouts.app')

@section('title', 'Locations')
@section('page_title', 'Locations')

@section('content')

@push('styles')
<style>
.md-card{background:var(--surface);border:1px solid var(--border);border-radius:14px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.07),0 4px 14px rgba(0,0,0,.04)}
.md-card-head{padding:18px 20px 14px;border-bottom:1px solid var(--border)}
.md-card-title-row{display:flex;align-items:center;gap:10px;margin-bottom:14px}
.md-card-accent{width:4px;height:28px;border-radius:2px;flex-shrink:0}
.md-card-meta{flex:1}
.md-card-meta h6{font-size:14px;font-weight:800;color:var(--text);margin:0;line-height:1.2}
.md-card-meta small{font-size:11px;color:var(--muted)}
.md-stat-row{display:grid;grid-template-columns:repeat(3,1fr);gap:8px}
.md-stat-box{background:var(--body-bg);border:1px solid var(--border);border-radius:10px;padding:11px 14px;text-align:center}
.md-stat-num{font-size:20px;font-weight:800;color:var(--text);line-height:1;font-family:'Inter',sans-serif}
.md-stat-lbl{font-size:10px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.07em;margin-top:3px}
.md-add-row{background:var(--body-bg);border-bottom:1px solid var(--border);padding:12px 20px;display:flex;align-items:center;gap:8px}
.md-add-input{flex:1;border:1.5px solid var(--border);border-radius:8px;padding:8px 12px;font-size:13px;color:var(--text);font-family:'Inter',sans-serif;background:var(--surface);outline:none;transition:border-color .2s,box-shadow .2s;min-width:0}
.md-add-input:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(2,132,199,.1)}
.md-add-btn{display:inline-flex;align-items:center;gap:5px;border:none;border-radius:8px;padding:8px 16px;font-size:12px;font-weight:700;cursor:pointer;font-family:'Inter',sans-serif;white-space:nowrap;color:#fff;background:var(--navy,#142b47)}
.md-table{width:100%;border-collapse:collapse}
.md-table thead th{background:var(--body-bg);padding:9px 16px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.09em;color:var(--muted);border-bottom:1px solid var(--border);text-align:left;white-space:nowrap}
.md-table tbody td{padding:11px 16px;border-bottom:1px solid var(--border);font-size:13px;vertical-align:middle;color:var(--text)}
.md-table tbody tr:last-child td{border-bottom:none}
.md-table tbody tr:hover td{background:var(--body-bg)}
.md-pill{display:inline-flex;align-items:center;gap:6px;border-radius:7px;padding:4px 11px;border:1px solid}
.md-count-pill{border-radius:20px;padding:3px 10px;font-size:11px;font-weight:700;border:1px solid}
.md-status-pill{border-radius:20px;padding:3px 10px;font-size:11px;font-weight:700;display:inline-flex;align-items:center;gap:4px;border:1px solid}
.md-btn-sm{display:inline-flex;align-items:center;justify-content:center;width:29px;height:29px;border-radius:7px;font-size:12px;cursor:pointer;text-decoration:none;border:1px solid transparent}
.btn-edit{background:rgba(37,99,235,.08);border-color:rgba(37,99,235,.2);color:#2563eb}
.btn-del{background:rgba(239,68,68,.08);border-color:rgba(239,68,68,.2);color:#dc2626}
.btn-del-dis{background:var(--body-bg);border-color:var(--border);color:var(--muted);cursor:not-allowed;opacity:.5}
.md-edit-form{display:none;align-items:center;gap:6px}
.md-edit-input{font-size:12px;font-weight:700;color:var(--text);background:var(--body-bg);border:1.5px solid var(--accent);border-radius:7px;padding:5px 10px;width:220px;font-family:'Inter',sans-serif;text-transform:uppercase;outline:none}
.md-btn-save{font-size:11px;font-weight:700;color:#fff;background:var(--navy,#142b47);border:none;border-radius:6px;padding:5px 11px;cursor:pointer;font-family:'Inter',sans-serif}
.md-btn-cancel{font-size:11px;font-weight:600;color:var(--muted);background:transparent;border:1.5px solid var(--border);border-radius:6px;padding:5px 9px;cursor:pointer;font-family:'Inter',sans-serif}
.md-empty{padding:44px 20px;text-align:center;color:var(--muted)}
.md-empty i{font-size:28px;display:block;margin-bottom:10px;opacity:.3}
.md-empty p{font-size:13px;font-weight:600;margin:0}
.md-empty span{font-size:12px;margin-top:4px;display:block}
</style>
@endpush

<!-- PAGE HEADER -->
<div style="margin-bottom:24px">
  <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:5px">
    Masterdata &rsaquo; <span style="color:var(--accent)">Locations</span>
  </div>
  <h4 style="font-family:'Inter',sans-serif;font-weight:800;font-size:22px;color:var(--text);margin:0">Locations</h4>
  <p style="font-size:13px;color:var(--muted);margin:4px 0 0">Manage asset locations used in IT and Non-IT assets</p>
</div>

@php
  $total    = $locations->count();
  $itItems  = $locations->sum('it_count');
  $nitItems = $locations->sum('nit_count');
@endphp

<div class="md-card">

  <!-- Card header -->
  <div class="md-card-head">
    <div class="md-card-title-row">
      <div class="md-card-accent" style="background:#0d9488"></div>
      <div class="md-card-meta">
        <h6>Asset Locations</h6>
        <small>Used in IT Assets and Non-IT Assets</small>
      </div>
      <span style="background:rgba(13,148,136,.1);color:#0d9488;border:1px solid rgba(13,148,136,.2);border-radius:20px;padding:4px 12px;font-size:11px;font-weight:700;display:inline-flex;align-items:center;gap:5px">
        <i class="bi bi-geo-alt-fill" style="font-size:10px"></i> Masterdata
      </span>
    </div>
    <div class="md-stat-row">
      <div class="md-stat-box" style="background:rgba(13,148,136,.08);border-color:rgba(13,148,136,.2)">
        <div class="md-stat-num" style="color:#0d9488">{{ $total }}</div>
        <div class="md-stat-lbl">Total Locations</div>
      </div>
      <div class="md-stat-box" style="background:rgba(22,163,74,.07);border-color:rgba(22,163,74,.15)">
        <div class="md-stat-num" style="color:#16a34a">{{ $itItems }}</div>
        <div class="md-stat-lbl">IT Assets</div>
      </div>
      <div class="md-stat-box" style="background:rgba(124,58,237,.07);border-color:rgba(124,58,237,.15)">
        <div class="md-stat-num" style="color:#7c3aed">{{ $nitItems }}</div>
        <div class="md-stat-lbl">Non-IT Assets</div>
      </div>
    </div>
  </div>

  <!-- Add row -->
  <form method="POST" action="{{ route('it.locations.store') }}" class="md-add-row">
    @csrf
    <input type="text" name="name" class="md-add-input" placeholder="New location name, e.g. SERVER ROOM" required
      oninput="this.value=this.value.toUpperCase()">
    <button type="submit" class="md-add-btn">
      <i class="bi bi-plus-lg"></i> Add Location
    </button>
  </form>

  <!-- Table -->
  @if($locations->isEmpty())
  <div class="md-empty">
    <i class="bi bi-geo-alt"></i>
    <p>No locations yet</p>
    <span>Add a location above to get started</span>
  </div>
  @else
  <div class="table-responsive">
    <table class="md-table">
      <thead><tr>
        <th style="width:32px">#</th>
        <th>Location</th>
        <th>IT Assets</th>
        <th>Non-IT Assets</th>
        <th>Status</th>
        <th style="width:72px;text-align:center">Actions</th>
      </tr></thead>
      <tbody>
      @foreach($locations as $i => $loc)
      <tr>
        <td style="color:var(--muted);font-size:12px">{{ $i + 1 }}</td>
        <td>
          <div id="label-{{ $loc->id }}" class="md-pill" style="background:rgba(13,148,136,.08);border-color:rgba(13,148,136,.25)">
            <div style="width:6px;height:6px;border-radius:50%;background:#0d9488;flex-shrink:0"></div>
            <span style="font-size:12px;font-weight:700;color:#0d9488;letter-spacing:.04em">{{ $loc->name }}</span>
          </div>
          <form id="editform-{{ $loc->id }}" method="POST" action="{{ route('it.locations.update', $loc->id) }}" class="md-edit-form">
            @csrf
            <input type="text" name="name" value="{{ $loc->name }}" required class="md-edit-input" oninput="this.value=this.value.toUpperCase()">
            <button type="submit" class="md-btn-save">Save</button>
            <button type="button" class="md-btn-cancel" onclick="cancelEdit({{ $loc->id }})">Cancel</button>
          </form>
        </td>
        <td>
          <span class="md-count-pill" style="background:{{ $loc->it_count > 0 ? 'rgba(2,132,199,.08)' : 'var(--body-bg)' }};color:{{ $loc->it_count > 0 ? '#0284c7' : 'var(--muted)' }};border-color:{{ $loc->it_count > 0 ? 'rgba(2,132,199,.25)' : 'var(--border)' }}">
            {{ $loc->it_count }} item{{ $loc->it_count !== 1 ? 's' : '' }}
          </span>
        </td>
        <td>
          <span class="md-count-pill" style="background:{{ $loc->nit_count > 0 ? 'rgba(124,58,237,.08)' : 'var(--body-bg)' }};color:{{ $loc->nit_count > 0 ? '#7c3aed' : 'var(--muted)' }};border-color:{{ $loc->nit_count > 0 ? 'rgba(124,58,237,.25)' : 'var(--border)' }}">
            {{ $loc->nit_count }} item{{ $loc->nit_count !== 1 ? 's' : '' }}
          </span>
        </td>
        <td>
          @php $inUse = ($loc->it_count + $loc->nit_count) > 0; @endphp
          @if($inUse)
          <span class="md-status-pill" style="background:rgba(22,163,74,.08);color:#15803d;border-color:rgba(22,163,74,.2)">
            <i class="bi bi-check-circle-fill" style="font-size:10px"></i> In Use
          </span>
          @else
          <span class="md-status-pill" style="background:var(--body-bg);color:var(--muted);border-color:var(--border)">
            <i class="bi bi-dash-circle" style="font-size:10px"></i> Unused
          </span>
          @endif
        </td>
        <td>
          <div id="actions-{{ $loc->id }}" style="display:flex;gap:5px;justify-content:center">
            <button class="md-btn-sm btn-edit" onclick="startEdit({{ $loc->id }})" title="Rename">
              <i class="bi bi-pencil-fill"></i>
            </button>
            @if(!$inUse)
            <form method="POST" action="{{ route('it.locations.destroy', $loc->id) }}" style="display:inline"
              onsubmit="return confirm('Remove location \'{{ addslashes($loc->name) }}\'?')">
              @csrf @method('DELETE')
              <button type="submit" class="md-btn-sm btn-del" title="Delete">
                <i class="bi bi-trash-fill"></i>
              </button>
            </form>
            @else
            <span class="md-btn-sm btn-del-dis" title="In use â€” cannot delete"><i class="bi bi-trash-fill"></i></span>
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

@endsection

@push('scripts')
<script>
function startEdit(id) {
  document.getElementById('label-'+id).style.display='none';
  document.getElementById('editform-'+id).style.display='flex';
  document.getElementById('actions-'+id).style.display='none';
  document.querySelector('#editform-'+id+' input[name="name"]').focus();
}
function cancelEdit(id) {
  document.getElementById('label-'+id).style.display='inline-flex';
  document.getElementById('editform-'+id).style.display='none';
  document.getElementById('actions-'+id).style.display='flex';
}
</script>
@endpush
