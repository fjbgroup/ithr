@extends('it.layouts.app')

@section('title', 'Asset Classes')
@section('page_title', 'Asset Classes')

@section('content')

@push('styles')
<style>
/* ── Asset Classes Page ── */
.ac-card{background:var(--surface);border:1px solid var(--border);border-radius:14px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.07),0 4px 14px rgba(0,0,0,.04)}
.ac-card-head{padding:18px 20px 14px;border-bottom:1px solid var(--border)}
.ac-card-title-row{display:flex;align-items:center;gap:10px;margin-bottom:14px}
.ac-card-accent{width:4px;height:28px;border-radius:2px;flex-shrink:0}
.ac-card-meta{flex:1}
.ac-card-meta h6{font-size:14px;font-weight:800;color:var(--text);margin:0;line-height:1.2}
.ac-card-meta small{font-size:11px;color:var(--muted)}
.ac-badge{border-radius:20px;padding:4px 12px;font-size:11px;font-weight:700;display:inline-flex;align-items:center;gap:5px;white-space:nowrap}
.ac-stat-row{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;padding:0;margin:0}
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
.ac-edit-input{font-size:12px;font-weight:700;color:var(--text);background:var(--body-bg);border:1.5px solid var(--accent);border-radius:7px;padding:5px 10px;width:150px;font-family:'Inter',sans-serif;text-transform:uppercase;outline:none}
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
    Admin &rsaquo; <span style="color:var(--accent)">Asset Classes</span>
  </div>
  <h4 style="font-family:'Inter',sans-serif;font-weight:800;font-size:22px;color:var(--text);margin:0">Asset Classes</h4>
  <p style="font-size:13px;color:var(--muted);margin:4px 0 0">Manage asset classes for IT and Non-IT assets</p>
</div>

@php
  $itTotal  = $itClasses->count();
  $itUsed   = $itClasses->filter(fn($c) => $c->it_count > 0)->count();
  $itItems  = $itClasses->sum('it_count');

  $nitTotal = $nitClasses->count();
  $nitUsed  = $nitClasses->filter(fn($c) => $c->nit_count > 0)->count();
  $nitItems = $nitClasses->sum('nit_count');
@endphp

<!-- CARDS ROW -->
<div class="row g-3">

  <!-- ══ IT ASSET CLASSES ══ -->
  <div class="col-lg-6" id="itCard">
    <div class="ac-card">

      <!-- Card header: title + badge -->
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
        <!-- Stats -->
        <div class="ac-stat-row">
          <div class="ac-stat-box" style="background:rgba(2,132,199,.08);border-color:rgba(2,132,199,.2)">
            <div class="ac-stat-num" style="color:#0284c7">{{ $itTotal }}</div>
            <div class="ac-stat-lbl">Classes</div>
          </div>
          <div class="ac-stat-box" style="background:rgba(22,163,74,.07);border-color:rgba(22,163,74,.15)">
            <div class="ac-stat-num" style="color:#16a34a">{{ $itItems }}</div>
            <div class="ac-stat-lbl">IT Assets</div>
          </div>
          <div class="ac-stat-box" style="background:var(--body-bg);border-color:var(--border)">
            <div class="ac-stat-num" style="color:#64748b">{{ $itTotal - $itUsed }}</div>
            <div class="ac-stat-lbl">Unused</div>
          </div>
        </div>
      </div>

      <!-- Add row -->
      <form method="POST" action="{{ route('it.asset-classes.store') }}" class="ac-add-row">
        @csrf
        <input type="hidden" name="type" value="it">
        <input type="text" name="name" class="ac-add-input" placeholder="New class name, e.g. MONITOR" required
          oninput="this.value=this.value.toUpperCase()">
        <button type="submit" class="ac-add-btn" style="background:var(--navy,#142b47)">
          <i class="bi bi-plus-lg"></i> Add Class
        </button>
      </form>

      <!-- Table -->
      @if($itClasses->isEmpty())
      <div class="ac-empty">
        <i class="bi bi-tags"></i>
        <p>No IT asset classes yet</p>
        <span>Add a class above to get started</span>
      </div>
      @else
      <div class="table-responsive">
        <table class="ac-table">
          <thead><tr>
            <th style="width:32px">#</th>
            <th>Asset Class</th>
            <th>Items</th>
            <th>Status</th>
            <th style="width:72px;text-align:center">Actions</th>
          </tr></thead>
          <tbody>
          @foreach($itClasses as $i => $cls)
          <tr>
            <td style="color:var(--muted);font-size:12px">{{ $i + 1 }}</td>
            <td>
              <div id="label-{{ $cls->id }}" class="ac-class-pill" style="background:rgba(2,132,199,.08);border-color:rgba(2,132,199,.25)">
                <div style="width:6px;height:6px;border-radius:50%;background:#0284c7;flex-shrink:0"></div>
                <span style="font-size:12px;font-weight:700;color:#0284c7;letter-spacing:.04em">{{ $cls->name }}</span>
              </div>
              <form id="editform-{{ $cls->id }}" method="POST" action="{{ route('it.asset-classes.update', $cls->id) }}" class="ac-edit-form">
                @csrf
                <input type="hidden" name="type" value="it">
                <input type="text" name="name" value="{{ $cls->name }}" required class="ac-edit-input" oninput="this.value=this.value.toUpperCase()">
                <button type="submit" class="ac-btn-save">Save</button>
                <button type="button" class="ac-btn-cancel" onclick="cancelEdit({{ $cls->id }})">Cancel</button>
              </form>
            </td>
            <td>
              <span class="ac-count-pill" style="background:{{ $cls->it_count > 0 ? 'rgba(2,132,199,.08)' : 'var(--body-bg)' }};color:{{ $cls->it_count > 0 ? '#0284c7' : 'var(--muted)' }};border-color:{{ $cls->it_count > 0 ? 'rgba(2,132,199,.25)' : 'var(--border)' }}">
                {{ $cls->it_count }} item{{ $cls->it_count !== 1 ? 's' : '' }}
              </span>
            </td>
            <td>
              @if($cls->it_count > 0)
              <span class="ac-status-pill" style="background:rgba(22,163,74,.08);color:#15803d;border-color:rgba(22,163,74,.2)">
                <i class="bi bi-check-circle-fill" style="font-size:10px"></i> In Use
              </span>
              @else
              <span class="ac-status-pill" style="background:var(--body-bg);color:var(--muted);border-color:var(--border)">
                <i class="bi bi-dash-circle" style="font-size:10px"></i> Unused
              </span>
              @endif
            </td>
            <td>
              <div id="actions-{{ $cls->id }}" style="display:flex;gap:5px;justify-content:center">
                <button class="ac-btn-sm btn-edit" onclick="startEdit({{ $cls->id }})" title="Rename">
                  <i class="bi bi-pencil-fill"></i>
                </button>
                @if($cls->it_count === 0 && $cls->nit_count === 0)
                <form method="POST" action="{{ route('it.asset-classes.destroy', $cls->id) }}" style="display:inline"
                  onsubmit="return confirm('Remove class \'{{ addslashes($cls->name) }}\'?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="ac-btn-sm btn-del" title="Delete">
                    <i class="bi bi-trash-fill"></i>
                  </button>
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

  <!-- ══ NON-IT ASSET CLASSES ══ -->
  <div class="col-lg-6" id="nitCard">
    <div class="ac-card">

      <!-- Card header: title + badge -->
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
        <!-- Stats -->
        <div class="ac-stat-row">
          <div class="ac-stat-box" style="background:rgba(124,58,237,.08);border-color:rgba(124,58,237,.2)">
            <div class="ac-stat-num" style="color:#7c3aed">{{ $nitTotal }}</div>
            <div class="ac-stat-lbl">Classes</div>
          </div>
          <div class="ac-stat-box" style="background:rgba(22,163,74,.07);border-color:rgba(22,163,74,.15)">
            <div class="ac-stat-num" style="color:#16a34a">{{ $nitItems }}</div>
            <div class="ac-stat-lbl">Non-IT Assets</div>
          </div>
          <div class="ac-stat-box" style="background:var(--body-bg);border-color:var(--border)">
            <div class="ac-stat-num" style="color:#64748b">{{ $nitTotal - $nitUsed }}</div>
            <div class="ac-stat-lbl">Unused</div>
          </div>
        </div>
      </div>

      <!-- Add row -->
      <form method="POST" action="{{ route('it.asset-classes.store') }}" class="ac-add-row">
        @csrf
        <input type="hidden" name="type" value="non_it">
        <input type="text" name="name" class="ac-add-input" placeholder="New class name, e.g. FURNITURE" required
          oninput="this.value=this.value.toUpperCase()">
        <button type="submit" class="ac-add-btn" style="background:#7c3aed">
          <i class="bi bi-plus-lg"></i> Add Class
        </button>
      </form>

      <!-- Table -->
      @if($nitClasses->isEmpty())
      <div class="ac-empty">
        <i class="bi bi-boxes"></i>
        <p>No Non-IT asset classes yet</p>
        <span>Add a class above to get started</span>
      </div>
      @else
      <div class="table-responsive">
        <table class="ac-table">
          <thead><tr>
            <th style="width:32px">#</th>
            <th>Asset Class</th>
            <th>Items</th>
            <th>Status</th>
            <th style="width:72px;text-align:center">Actions</th>
          </tr></thead>
          <tbody>
          @foreach($nitClasses as $i => $cls)
          <tr>
            <td style="color:var(--muted);font-size:12px">{{ $i + 1 }}</td>
            <td>
              <div id="label-{{ $cls->id }}" class="ac-class-pill" style="background:rgba(124,58,237,.08);border-color:rgba(124,58,237,.25)">
                <div style="width:6px;height:6px;border-radius:50%;background:#7c3aed;flex-shrink:0"></div>
                <span style="font-size:12px;font-weight:700;color:#7c3aed;letter-spacing:.04em">{{ $cls->name }}</span>
              </div>
              <form id="editform-{{ $cls->id }}" method="POST" action="{{ route('it.asset-classes.update', $cls->id) }}" class="ac-edit-form">
                @csrf
                <input type="hidden" name="type" value="non_it">
                <input type="text" name="name" value="{{ $cls->name }}" required class="ac-edit-input" oninput="this.value=this.value.toUpperCase()">
                <button type="submit" class="ac-btn-save">Save</button>
                <button type="button" class="ac-btn-cancel" onclick="cancelEdit({{ $cls->id }})">Cancel</button>
              </form>
            </td>
            <td>
              <span class="ac-count-pill" style="background:{{ $cls->nit_count > 0 ? 'rgba(124,58,237,.08)' : 'var(--body-bg)' }};color:{{ $cls->nit_count > 0 ? '#7c3aed' : 'var(--muted)' }};border-color:{{ $cls->nit_count > 0 ? 'rgba(124,58,237,.25)' : 'var(--border)' }}">
                {{ $cls->nit_count }} item{{ $cls->nit_count !== 1 ? 's' : '' }}
              </span>
            </td>
            <td>
              @if($cls->nit_count > 0)
              <span class="ac-status-pill" style="background:rgba(22,163,74,.08);color:#15803d;border-color:rgba(22,163,74,.2)">
                <i class="bi bi-check-circle-fill" style="font-size:10px"></i> In Use
              </span>
              @else
              <span class="ac-status-pill" style="background:var(--body-bg);color:var(--muted);border-color:var(--border)">
                <i class="bi bi-dash-circle" style="font-size:10px"></i> Unused
              </span>
              @endif
            </td>
            <td>
              <div id="actions-{{ $cls->id }}" style="display:flex;gap:5px;justify-content:center">
                <button class="ac-btn-sm btn-edit" onclick="startEdit({{ $cls->id }})" title="Rename">
                  <i class="bi bi-pencil-fill"></i>
                </button>
                @if($cls->it_count === 0 && $cls->nit_count === 0)
                <form method="POST" action="{{ route('it.asset-classes.destroy', $cls->id) }}" style="display:inline"
                  onsubmit="return confirm('Remove class \'{{ addslashes($cls->name) }}\'?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="ac-btn-sm btn-del" title="Delete">
                    <i class="bi bi-trash-fill"></i>
                  </button>
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

</div><!-- /row -->

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
// Scroll to the right card after add/edit/delete
(function(){
  var t = '{{ session("scroll_target", "") }}';
  var el = document.getElementById(t);
  if (el && t) el.scrollIntoView({behavior:'smooth', block:'start'});
})();
</script>
@endpush

