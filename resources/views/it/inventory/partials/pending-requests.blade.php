<style>
/* â•â• Pending Requests (admin) — mirrors My Requests layout â•â• */
.apr-wrap{max-width:860px;margin:0 auto}
.apr-hero{background:linear-gradient(135deg,var(--navy,#142b47) 0%,#1e3a5f 100%);border-radius:16px;padding:28px 32px;margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap}
.apr-hero-title{font-family:'Inter',sans-serif;font-size:24px;font-weight:800;color:#fff;margin:0 0 5px}
.apr-hero-sub{font-size:13px;color:rgba(255,255,255,.55);margin:0}
.apr-hero-badge{background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.18);border-radius:10px;padding:12px 20px;text-align:center;flex-shrink:0}
.apr-hero-badge-num{font-size:28px;font-weight:800;color:#fff;line-height:1;font-family:'Inter',sans-serif}
.apr-hero-badge-lbl{font-size:10px;color:rgba(255,255,255,.55);text-transform:uppercase;letter-spacing:.07em;margin-top:3px}

/* Tab bar */
.apr-tabs{display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap}
.apr-tab{display:inline-flex;align-items:center;gap:8px;padding:9px 16px;border-radius:10px;border:1.5px solid var(--border);background:var(--surface);cursor:pointer;font-family:'Inter',sans-serif;font-size:13px;font-weight:700;color:var(--text);transition:all .15s;white-space:nowrap}
.apr-tab:hover{border-color:currentColor;opacity:.85}
.apr-tab .apr-tab-icon{font-size:14px;flex-shrink:0}
.apr-tab .apr-tab-badge{font-size:11px;font-weight:800;border-radius:20px;padding:2px 8px;background:rgba(0,0,0,.06);color:inherit;transition:background .15s}
.apr-tab.active .apr-tab-badge{background:rgba(255,255,255,.25);color:#fff}

/* Tab panels */
.apr-tab-panel{display:none}
.apr-tab-panel.active{display:block}

/* Section */
.apr-section{background:var(--surface);border:1px solid var(--border);border-radius:14px;overflow:hidden;margin-bottom:20px}
.apr-section-hdr{display:flex;align-items:center;gap:10px;padding:14px 18px;border-bottom:1px solid var(--border);background:var(--body-bg)}
.apr-section-hdr-accent{width:4px;height:20px;border-radius:3px;flex-shrink:0}
.apr-section-hdr-text{font-family:'Inter',sans-serif;font-size:14px;font-weight:800;color:var(--text);flex:1}
.apr-section-hdr-count{font-size:11px;font-weight:700;border-radius:20px;padding:3px 12px}

/* Rows */
.apr-row{border-bottom:1px solid var(--border)}
.apr-row:last-child{border-bottom:none}
.apr-row-top{display:flex;align-items:flex-start;gap:14px;padding:14px 18px}
.apr-row-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0;margin-top:1px}
.apr-row-body{flex:1;min-width:0}
.apr-row-title{font-size:13.5px;font-weight:700;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:4px}
.apr-row-meta{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.apr-tag{display:inline-block;background:rgba(37,99,235,.1);color:#2563eb;border-radius:5px;padding:2px 8px;font-size:11px;font-weight:700;white-space:nowrap}
.apr-tag-muted{font-size:11px;color:var(--muted);white-space:nowrap}
.apr-requester{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:600;color:var(--text);background:var(--body-bg);border-radius:6px;padding:3px 8px;border:1px solid var(--border);white-space:nowrap}
.apr-row-right{display:flex;flex-direction:column;align-items:flex-end;gap:7px;flex-shrink:0;min-width:130px}
.apr-row-date{font-size:11px;color:var(--muted);text-align:right;line-height:1.6}
.apr-actions{display:flex;gap:5px;flex-wrap:wrap;justify-content:flex-end}
.apr-btn-approve{display:inline-flex;align-items:center;gap:4px;border:none;border-radius:7px;padding:5px 12px;font-size:11px;font-weight:700;cursor:pointer;white-space:nowrap;color:#fff}
.apr-btn-reject{display:inline-flex;align-items:center;gap:4px;background:var(--body-bg);color:var(--muted);border:1px solid var(--border);border-radius:7px;padding:5px 12px;font-size:11px;font-weight:700;cursor:pointer;white-space:nowrap;transition:border-color .12s,color .12s}
.apr-btn-reject:hover{border-color:#dc2626;color:#dc2626}

/* Empty */
.apr-empty{padding:40px 24px;text-align:center}
.apr-empty-icon{font-size:36px;display:block;margin-bottom:12px;opacity:.3}
.apr-empty-title{font-size:13px;font-weight:700;color:var(--text);margin-bottom:4px}
.apr-empty-sub{font-size:11px;color:var(--muted)}
</style>

<div class="apr-wrap">

{{-- Hero --}}
<div class="apr-hero">
  <div>
    <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:rgba(255,255,255,.4);margin-bottom:6px">IT Assets</div>
    <h4 class="apr-hero-title">Pending Requests</h4>
    <p class="apr-hero-sub">Review and approve or reject staff requests</p>
  </div>
  <div style="display:flex;gap:10px;flex-wrap:wrap">
    <div class="apr-hero-badge">
      <div class="apr-hero-badge-num">{{ $totalPending }}</div>
      <div class="apr-hero-badge-lbl">Pending</div>
    </div>
  </div>
</div>

{{-- Tab buttons --}}
<div class="apr-tabs">
  @foreach([
    ['add',  '#16a34a', 'bi-plus-circle-fill', $pendingAddCount, 'Add Asset'],
    ['ew',   '#d97706', 'bi-recycle',           $pendingEwCount,  'E-Waste'],
    ['del',  '#dc2626', 'bi-trash-fill',         $pendingDelCount, 'Delete'],
    ['edit', '#2563eb', 'bi-pencil-square',     $pendingEditCount,'Edit Asset'],
  ] as [$tid, $clr, $ico, $n, $lbl])
  <button
    class="apr-tab{{ $loop->first ? ' active' : '' }}"
    id="aprtab-{{ $tid }}"
    data-clr="{{ $clr }}"
    onclick="aprSwitchTab('{{ $tid }}')"
    style="{{ $loop->first ? 'background:'.$clr.';border-color:'.$clr.';color:#fff' : 'color:'.$clr.';border-color:var(--border)' }}"
  >
    <i class="bi {{ $ico }} apr-tab-icon"></i>
    {{ $lbl }}
    <span class="apr-tab-badge">{{ $n }}</span>
  </button>
  @endforeach
</div>

{{-- ── TAB 1: ADD ASSET ── --}}
<div class="apr-tab-panel active" id="aprpanel-add">
  <div class="apr-section">
    <div class="apr-section-hdr">
      <div class="apr-section-hdr-accent" style="background:#16a34a"></div>
      <i class="bi bi-plus-circle-fill" style="color:#16a34a;font-size:15px"></i>
      <span class="apr-section-hdr-text">Add Asset Requests</span>
      <span class="apr-section-hdr-count" style="background:rgba(22,163,74,.1);color:#16a34a">{{ $pendingAddCount }}</span>
    </div>
    @if($pendingAddCount === 0)
    <div class="apr-empty">
      <i class="bi bi-check2-circle apr-empty-icon" style="color:#16a34a"></i>
      <div class="apr-empty-title">No pending add requests</div>
      <div class="apr-empty-sub">All add asset requests have been reviewed</div>
    </div>
    @else
    @foreach($pendingAdds as $req)
    <div class="apr-row">
      <div class="apr-row-top">
        <div class="apr-row-icon" style="background:rgba(22,163,74,.1)"><i class="bi bi-plus-circle" style="color:#16a34a"></i></div>
        <div class="apr-row-body">
          <div class="apr-row-title">{{ $req->description }}</div>
          <div class="apr-row-meta" style="margin-bottom:6px">
            @if($req->asset_class)<span class="apr-tag">{{ $req->asset_class }}</span>@endif
            @if($req->asset_number)<span class="apr-tag-muted" style="color:var(--accent);font-weight:600">{{ $req->asset_number }}</span>@endif
            @if($req->serial_number)<span class="apr-tag-muted">S/N {{ $req->serial_number }}</span>@endif
            @if($req->brand || $req->model)<span class="apr-tag-muted">{{ trim(($req->brand ?? '').' '.($req->model ?? '')) }}</span>@endif
          </div>
          <span class="apr-requester"><i class="bi bi-person-fill" style="font-size:10px"></i>{{ $req->requester?->full_name ?? '—' }}</span>
        </div>
        <div class="apr-row-right">
          <div class="apr-row-date">{{ \Carbon\Carbon::parse($req->created_at)->format('d M Y') }}<br>{{ \Carbon\Carbon::parse($req->created_at)->format('H:i') }}</div>
          <div class="apr-actions">
            <form method="POST" action="{{ route('it.requests.add.approve', $req->id) }}" style="display:inline" onsubmit="return confirm('Approve and add this asset to IT Assets?')">
              @csrf
              <button type="submit" class="apr-btn-approve" style="background:#16a34a"><i class="bi bi-check-lg"></i> Approve</button>
            </form>
            <form method="POST" action="{{ route('it.requests.add.reject', $req->id) }}" style="display:inline">
              @csrf
              <button type="submit" class="apr-btn-reject"><i class="bi bi-x-lg"></i> Reject</button>
            </form>
          </div>
        </div>
      </div>
    </div>
    @endforeach
    @endif
  </div>
</div>

{{-- ── TAB 2: E-WASTE ── --}}
<div class="apr-tab-panel" id="aprpanel-ew">
  <div class="apr-section">
    <div class="apr-section-hdr">
      <div class="apr-section-hdr-accent" style="background:#d97706"></div>
      <i class="bi bi-recycle" style="color:#d97706;font-size:15px"></i>
      <span class="apr-section-hdr-text">E-Waste Requests</span>
      <span class="apr-section-hdr-count" style="background:rgba(217,119,6,.1);color:#d97706">{{ $pendingEwCount }}</span>
    </div>
    @if($pendingEwCount === 0)
    <div class="apr-empty">
      <i class="bi bi-recycle apr-empty-icon" style="color:#d97706"></i>
      <div class="apr-empty-title">No pending e-waste requests</div>
      <div class="apr-empty-sub">All e-waste requests have been reviewed</div>
    </div>
    @else
    @foreach($pendingEw as $req)
    <div class="apr-row">
      <div class="apr-row-top">
        <div class="apr-row-icon" style="background:rgba(217,119,6,.1)"><i class="bi bi-recycle" style="color:#d97706"></i></div>
        <div class="apr-row-body">
          <div class="apr-row-meta" style="margin-bottom:4px">
            <span style="display:inline-flex;align-items:center;gap:4px;background:rgba(22,163,74,.1);color:#16a34a;border-radius:5px;padding:2px 8px;font-size:10px;font-weight:700"><i class="bi bi-plus-circle-fill"></i> Add to E-Waste</span>
          </div>
          <div class="apr-row-title">{{ $req->description }}</div>
          <div class="apr-row-meta" style="margin-bottom:6px">
            @if($req->asset_class)<span class="apr-tag">{{ $req->asset_class }}</span>@endif
            @if($req->asset_number)<span class="apr-tag-muted" style="color:var(--accent);font-weight:600">{{ $req->asset_number }}</span>@endif
            @if($req->serial_number)<span class="apr-tag-muted">S/N {{ $req->serial_number }}</span>@endif
          </div>
          <span class="apr-requester"><i class="bi bi-person-fill" style="font-size:10px"></i>{{ $req->requester?->full_name ?? '—' }}</span>
        </div>
        <div class="apr-row-right">
          <div class="apr-row-date">{{ \Carbon\Carbon::parse($req->created_at)->format('d M Y') }}<br>{{ \Carbon\Carbon::parse($req->created_at)->format('H:i') }}</div>
          <div class="apr-actions">
            <form method="POST" action="{{ route('it.requests.ewaste.approve', $req->id) }}" style="display:inline" onsubmit="return confirm('Approve this e-waste request?')">
              @csrf
              <button type="submit" class="apr-btn-approve" style="background:#d97706"><i class="bi bi-check-lg"></i> Approve</button>
            </form>
            <form method="POST" action="{{ route('it.requests.ewaste.reject', $req->id) }}" style="display:inline">
              @csrf
              <button type="submit" class="apr-btn-reject"><i class="bi bi-x-lg"></i> Reject</button>
            </form>
          </div>
        </div>
      </div>
    </div>
    @endforeach
    @endif
  </div>
</div>

{{-- ── TAB 3: DELETE ── --}}
<div class="apr-tab-panel" id="aprpanel-del">
  <div class="apr-section">
    <div class="apr-section-hdr">
      <div class="apr-section-hdr-accent" style="background:#dc2626"></div>
      <i class="bi bi-trash-fill" style="color:#dc2626;font-size:15px"></i>
      <span class="apr-section-hdr-text">Delete Requests</span>
      <span class="apr-section-hdr-count" style="background:rgba(220,38,38,.1);color:#dc2626">{{ $pendingDelCount }}</span>
    </div>
    @if($pendingDelCount === 0)
    <div class="apr-empty">
      <i class="bi bi-trash apr-empty-icon" style="color:#dc2626"></i>
      <div class="apr-empty-title">No pending delete requests</div>
      <div class="apr-empty-sub">All delete requests have been reviewed</div>
    </div>
    @else
    @foreach($pendingDeletes as $req)
    @php
      $delDesc    = $req->inventoryItem?->description ?? $req->asset_description ?? '(Asset removed)';
      $delClass   = $req->inventoryItem?->asset_class ?? $req->asset_class ?? '';
      $delAssetNo = $req->inventoryItem?->asset_number ?? $req->asset_number ?? '—';
    @endphp
    <div class="apr-row">
      <div class="apr-row-top">
        <div class="apr-row-icon" style="background:rgba(239,68,68,.1)"><i class="bi bi-trash" style="color:#dc2626"></i></div>
        <div class="apr-row-body">
          <div class="apr-row-title">{{ $delDesc }}</div>
          @if($req->non_it_id)
          <div style="margin-bottom:4px"><span style="display:inline-flex;align-items:center;gap:4px;background:rgba(124,58,237,.1);color:#7c3aed;border-radius:5px;padding:2px 8px;font-size:10px;font-weight:700"><i class="bi bi-box-seam"></i> Non-IT Asset</span></div>
          @endif
          <div class="apr-row-meta" style="margin-bottom:6px">
            @if($delClass)<span class="apr-tag">{{ $delClass }}</span>@endif
            <span class="apr-tag-muted" style="color:var(--accent);font-weight:600">{{ $delAssetNo }}</span>
            @if($req->reason)<span class="apr-tag-muted" style="font-style:italic">"{{ $req->reason }}"</span>@endif
          </div>
          <span class="apr-requester"><i class="bi bi-person-fill" style="font-size:10px"></i>{{ $req->requester?->full_name ?? '—' }}</span>
        </div>
        <div class="apr-row-right">
          <div class="apr-row-date">{{ \Carbon\Carbon::parse($req->created_at)->format('d M Y') }}<br>{{ \Carbon\Carbon::parse($req->created_at)->format('H:i') }}</div>
          <div class="apr-actions">
            <form method="POST" action="{{ route('it.requests.delete.approve', $req->id) }}" style="display:inline" onsubmit="return confirm('Permanently delete this asset? This cannot be undone.')">
              @csrf
              <button type="submit" class="apr-btn-approve" style="background:#dc2626"><i class="bi bi-check-lg"></i> Approve</button>
            </form>
            <form method="POST" action="{{ route('it.requests.delete.reject', $req->id) }}" style="display:inline">
              @csrf
              <button type="submit" class="apr-btn-reject"><i class="bi bi-x-lg"></i> Reject</button>
            </form>
          </div>
        </div>
      </div>
    </div>
    @endforeach
    @endif
  </div>
</div>

{{-- ── TAB 4: EDIT ASSET ── --}}
<div class="apr-tab-panel" id="aprpanel-edit">
  <div class="apr-section">
    <div class="apr-section-hdr">
      <div class="apr-section-hdr-accent" style="background:#2563eb"></div>
      <i class="bi bi-pencil-square" style="color:#2563eb;font-size:15px"></i>
      <span class="apr-section-hdr-text">Edit Asset Requests</span>
      <span class="apr-section-hdr-count" style="background:rgba(37,99,235,.1);color:#2563eb">{{ $pendingEditCount }}</span>
    </div>
    @if($pendingEditCount === 0)
    <div class="apr-empty">
      <i class="bi bi-pencil-square apr-empty-icon" style="color:#2563eb"></i>
      <div class="apr-empty-title">No pending edit requests</div>
      <div class="apr-empty-sub">All edit requests have been reviewed</div>
    </div>
    @else
    @foreach($pendingEdits as $req)
    <div class="apr-row">
      <div class="apr-row-top">
        <div class="apr-row-icon" style="background:rgba(37,99,235,.1)"><i class="bi bi-pencil-square" style="color:#2563eb"></i></div>
        <div class="apr-row-body">
          <div class="apr-row-title">{{ $req->description }}</div>
          @if($req->asset_type === 'non_it')
          <div style="margin-bottom:4px"><span style="display:inline-flex;align-items:center;gap:4px;background:rgba(124,58,237,.1);color:#7c3aed;border-radius:5px;padding:2px 8px;font-size:10px;font-weight:700"><i class="bi bi-box-seam"></i> Non-IT Asset</span></div>
          @else
          <div style="font-size:11px;color:var(--muted);margin-bottom:4px">Currently: <em>{{ $req->inventoryItem?->description ?? '—' }}</em></div>
          @endif
          <div class="apr-row-meta" style="margin-bottom:6px">
            @if($req->asset_class)<span class="apr-tag">{{ $req->asset_class }}</span>@endif
            @if($req->asset_number)<span class="apr-tag-muted" style="color:var(--accent);font-weight:600">{{ $req->asset_number }}</span>@endif
            @if($req->serial_number)<span class="apr-tag-muted">S/N {{ $req->serial_number }}</span>@endif
            @if($req->location)<span class="apr-tag-muted"><i class="bi bi-geo-alt" style="font-size:10px"></i> {{ $req->location }}</span>@endif
          </div>
          <span class="apr-requester"><i class="bi bi-person-fill" style="font-size:10px"></i>{{ $req->requester?->full_name ?? '—' }}</span>
        </div>
        <div class="apr-row-right">
          <div class="apr-row-date">{{ \Carbon\Carbon::parse($req->created_at)->format('d M Y') }}<br>{{ \Carbon\Carbon::parse($req->created_at)->format('H:i') }}</div>
          <div class="apr-actions">
            <form method="POST" action="{{ route('it.requests.edit.approve', $req->id) }}" style="display:inline" onsubmit="return confirm('Apply these changes to the asset?')">
              @csrf
              <button type="submit" class="apr-btn-approve" style="background:#2563eb"><i class="bi bi-check-lg"></i> Approve</button>
            </form>
            <form method="POST" action="{{ route('it.requests.edit.reject', $req->id) }}" style="display:inline">
              @csrf
              <button type="submit" class="apr-btn-reject"><i class="bi bi-x-lg"></i> Reject</button>
            </form>
          </div>
        </div>
      </div>
    </div>
    @endforeach
    @endif
  </div>
</div>

</div>{{-- /apr-wrap --}}

<script>
function aprSwitchTab(id) {
  var tabIds = ['add','ew','del','edit'];
  var colors = {add:'#16a34a',ew:'#d97706',del:'#dc2626',edit:'#2563eb'};

  tabIds.forEach(function(tid) {
    var tab   = document.getElementById('aprtab-' + tid);
    var panel = document.getElementById('aprpanel-' + tid);
    if (!tab || !panel) return;
    var clr = colors[tid];
    if (tid === id) {
      tab.classList.add('active');
      tab.style.background  = clr;
      tab.style.borderColor = clr;
      tab.style.color       = '#fff';
      panel.classList.add('active');
    } else {
      tab.classList.remove('active');
      tab.style.background  = '';
      tab.style.borderColor = 'var(--border)';
      tab.style.color       = clr;
      panel.classList.remove('active');
    }
  });
}
</script>
