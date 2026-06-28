<style>
.apr-wrap{max-width:900px;margin:0 auto}
.apr-hero{background:linear-gradient(135deg,var(--navy,#142b47) 0%,#1e3a5f 100%);border-radius:18px;padding:32px 36px;margin-bottom:32px;display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap}
.apr-hero-title{font-family:'Inter',sans-serif;font-size:26px;font-weight:800;color:#fff;margin:0 0 6px}
.apr-hero-sub{font-size:13px;color:rgba(255,255,255,.6);margin:0}
.apr-hero-badge{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);border-radius:12px;padding:16px 24px;text-align:center;flex-shrink:0}
.apr-hero-badge-num{font-size:32px;font-weight:800;color:#fff;line-height:1;font-family:'Inter',sans-serif}
.apr-hero-badge-lbl{font-size:11px;color:rgba(255,255,255,.6);text-transform:uppercase;letter-spacing:.07em;margin-top:4px}
.apr-tiles{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:14px;margin-bottom:36px}
.apr-tile{background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px;transition:box-shadow .15s,transform .15s}
.apr-tile:hover{box-shadow:0 4px 20px rgba(0,0,0,.1);transform:translateY(-2px)}
.apr-tile-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px}
.apr-tile-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0}
.apr-tile-num{font-size:30px;font-weight:800;color:var(--text);line-height:1;font-family:'Inter',sans-serif}
.apr-tile-lbl{font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.07em;margin-top:4px}
.apr-section{margin-bottom:28px}
.apr-section-hdr{display:flex;align-items:center;gap:12px;margin-bottom:14px}
.apr-section-hdr-line{width:4px;height:24px;border-radius:3px;flex-shrink:0}
.apr-section-hdr-text{font-family:'Inter',sans-serif;font-size:16px;font-weight:800;color:var(--text);flex:1}
.apr-section-hdr-count{font-size:11px;font-weight:700;border-radius:20px;padding:4px 14px}
.apr-cards{display:flex;flex-direction:column;gap:12px}
.apr-card{background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px 22px;display:flex;align-items:flex-start;gap:16px;transition:box-shadow .12s,border-color .12s}
.apr-card:hover{box-shadow:0 4px 16px rgba(0,0,0,.08)}
.apr-card-icon{width:46px;height:46px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:19px;flex-shrink:0}
.apr-card-body{flex:1;min-width:0}
.apr-card-title{font-size:14px;font-weight:700;color:var(--text);margin-bottom:6px}
.apr-card-tags{display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-bottom:8px}
.apr-tag{display:inline-block;background:rgba(59,130,246,.1);color:#2563eb;border-radius:5px;padding:2px 8px;font-size:11px;font-weight:700}
.apr-tag-muted{font-size:11px;color:var(--muted)}
.apr-card-right{display:flex;flex-direction:column;align-items:flex-end;gap:8px;flex-shrink:0;min-width:130px}
.apr-date{font-size:11px;color:var(--muted);text-align:right;line-height:1.7}
.apr-actions{display:flex;gap:6px;flex-wrap:wrap;justify-content:flex-end}
.apr-btn-approve{display:inline-flex;align-items:center;gap:5px;border:none;border-radius:8px;padding:6px 14px;font-size:12px;font-weight:700;cursor:pointer;text-decoration:none;white-space:nowrap;color:#fff}
.apr-btn-reject{display:inline-flex;align-items:center;gap:5px;background:var(--body-bg);color:var(--muted);border:1.5px solid var(--border);border-radius:8px;padding:6px 14px;font-size:12px;font-weight:700;cursor:pointer;text-decoration:none;white-space:nowrap;transition:border-color .12s,color .12s}
.apr-btn-reject:hover{border-color:#dc2626;color:#dc2626}
.apr-requester{display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:600;color:var(--text);background:var(--body-bg);border-radius:7px;padding:4px 10px;border:1px solid var(--border)}
.apr-empty{background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:52px 24px;text-align:center}
.apr-empty-icon{font-size:40px;display:block;margin-bottom:14px;opacity:.35}
.apr-empty-title{font-size:14px;font-weight:700;color:var(--text);margin-bottom:6px}
.apr-empty-sub{font-size:12px;color:var(--muted)}
</style>

<div class="apr-wrap">

<!-- HERO HEADER -->
<div class="apr-hero">
  <div>
    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:rgba(255,255,255,.45);margin-bottom:8px">IT Assets</div>
    <h4 class="apr-hero-title">Pending Requests</h4>
    <p class="apr-hero-sub">Review and approve or reject staff requests</p>
  </div>
  <div style="display:flex;gap:12px;flex-wrap:wrap">
    <div class="apr-hero-badge">
      <div class="apr-hero-badge-num">{{ $totalPending }}</div>
      <div class="apr-hero-badge-lbl">Pending</div>
    </div>
  </div>
</div>

<!-- TILES -->
<div class="apr-tiles">
  <div class="apr-tile" style="border-top:3px solid #16a34a">
    <div class="apr-tile-top">
      <div class="apr-tile-icon" style="background:rgba(22,163,74,.1)"><i class="bi bi-plus-circle-fill" style="color:#16a34a"></i></div>
    </div>
    <div class="apr-tile-num">{{ $pendingAddCount }}</div>
    <div class="apr-tile-lbl">Add Asset</div>
  </div>
  <div class="apr-tile" style="border-top:3px solid #d97706">
    <div class="apr-tile-top">
      <div class="apr-tile-icon" style="background:rgba(217,119,6,.1)"><i class="bi bi-recycle" style="color:#d97706"></i></div>
    </div>
    <div class="apr-tile-num">{{ $pendingEwCount }}</div>
    <div class="apr-tile-lbl">E-Waste</div>
  </div>
  <div class="apr-tile" style="border-top:3px solid #dc2626">
    <div class="apr-tile-top">
      <div class="apr-tile-icon" style="background:rgba(239,68,68,.1)"><i class="bi bi-trash-fill" style="color:#dc2626"></i></div>
    </div>
    <div class="apr-tile-num">{{ $pendingDelCount }}</div>
    <div class="apr-tile-lbl">Delete</div>
  </div>
  <div class="apr-tile" style="border-top:3px solid #2563eb">
    <div class="apr-tile-top">
      <div class="apr-tile-icon" style="background:rgba(37,99,235,.1)"><i class="bi bi-pencil-square" style="color:#2563eb"></i></div>
    </div>
    <div class="apr-tile-num">{{ $pendingEditCount }}</div>
    <div class="apr-tile-lbl">Edit Asset</div>
  </div>
</div>

<!-- SECTION 1: ADD ASSET -->
<div class="apr-section">
  <div class="apr-section-hdr">
    <div class="apr-section-hdr-line" style="background:#16a34a"></div>
    <i class="bi bi-plus-circle-fill" style="color:#16a34a;font-size:16px"></i>
    <span class="apr-section-hdr-text">Add Asset Requests</span>
    <span class="apr-section-hdr-count" style="background:rgba(22,163,74,.12);color:#16a34a">{{ $pendingAddCount }}</span>
  </div>
  @if($pendingAddCount === 0)
  <div class="apr-empty">
    <i class="bi bi-check2-circle apr-empty-icon" style="color:#16a34a"></i>
    <div class="apr-empty-title">No pending add requests</div>
    <div class="apr-empty-sub">All add asset requests have been reviewed</div>
  </div>
  @else
  <div class="apr-cards">
    @foreach($pendingAdds as $req)
    <div class="apr-card">
      <div class="apr-card-icon" style="background:rgba(22,163,74,.1)"><i class="bi bi-plus-circle" style="color:#16a34a"></i></div>
      <div class="apr-card-body">
        <div class="apr-card-title">{{ $req->description }}</div>
        <div class="apr-card-tags">
          @if($req->asset_class)<span class="apr-tag">{{ $req->asset_class }}</span>@endif
          @if($req->asset_number)<span class="apr-tag-muted" style="color:var(--accent);font-weight:600">{{ $req->asset_number }}</span>@endif
          @if($req->serial_number)<span class="apr-tag-muted">S/N {{ $req->serial_number }}</span>@endif
          @if($req->brand || $req->model)<span class="apr-tag-muted">{{ trim(($req->brand ?? '').' '.($req->model ?? '')) }}</span>@endif
        </div>
        <span class="apr-requester"><i class="bi bi-person-fill" style="font-size:11px"></i>{{ $req->requester?->full_name ?? '—' }}</span>
      </div>
      <div class="apr-card-right">
        <div class="apr-date">{{ \Carbon\Carbon::parse($req->created_at)->format('d M Y') }}<br>{{ \Carbon\Carbon::parse($req->created_at)->format('H:i') }}</div>
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
    @endforeach
  </div>
  @endif
</div>

<!-- SECTION 2: E-WASTE -->
<div class="apr-section">
  <div class="apr-section-hdr">
    <div class="apr-section-hdr-line" style="background:#d97706"></div>
    <i class="bi bi-recycle" style="color:#d97706;font-size:16px"></i>
    <span class="apr-section-hdr-text">E-Waste Requests</span>
    <span class="apr-section-hdr-count" style="background:rgba(217,119,6,.12);color:#d97706">{{ $pendingEwCount }}</span>
  </div>
  @if($pendingEwCount === 0)
  <div class="apr-empty">
    <i class="bi bi-recycle apr-empty-icon" style="color:#d97706"></i>
    <div class="apr-empty-title">No pending e-waste requests</div>
    <div class="apr-empty-sub">All e-waste requests have been reviewed</div>
  </div>
  @else
  <div class="apr-cards">
    @foreach($pendingEw as $req)
    <div class="apr-card">
      <div class="apr-card-icon" style="background:rgba(217,119,6,.1)"><i class="bi bi-recycle" style="color:#d97706"></i></div>
      <div class="apr-card-body">
        <div class="apr-card-tags" style="margin-bottom:6px">
          <span style="display:inline-flex;align-items:center;gap:4px;background:rgba(22,163,74,.12);color:#16a34a;border-radius:6px;padding:2px 9px;font-size:10px;font-weight:700"><i class="bi bi-plus-circle-fill"></i> Add to E-Waste</span>
        </div>
        <div class="apr-card-title">{{ $req->description }}</div>
        <div class="apr-card-tags">
          @if($req->asset_class)<span class="apr-tag">{{ $req->asset_class }}</span>@endif
          @if($req->asset_number)<span class="apr-tag-muted" style="color:var(--accent);font-weight:600">{{ $req->asset_number }}</span>@endif
          @if($req->serial_number)<span class="apr-tag-muted">S/N {{ $req->serial_number }}</span>@endif
        </div>
        <span class="apr-requester"><i class="bi bi-person-fill" style="font-size:11px"></i>{{ $req->requester?->full_name ?? '—' }}</span>
      </div>
      <div class="apr-card-right">
        <div class="apr-date">{{ \Carbon\Carbon::parse($req->created_at)->format('d M Y') }}<br>{{ \Carbon\Carbon::parse($req->created_at)->format('H:i') }}</div>
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
    @endforeach
  </div>
  @endif
</div>

<!-- SECTION 3: DELETE -->
<div class="apr-section">
  <div class="apr-section-hdr">
    <div class="apr-section-hdr-line" style="background:#dc2626"></div>
    <i class="bi bi-trash-fill" style="color:#dc2626;font-size:16px"></i>
    <span class="apr-section-hdr-text">Delete Requests</span>
    <span class="apr-section-hdr-count" style="background:rgba(239,68,68,.12);color:#dc2626">{{ $pendingDelCount }}</span>
  </div>
  @if($pendingDelCount === 0)
  <div class="apr-empty">
    <i class="bi bi-trash apr-empty-icon" style="color:#dc2626"></i>
    <div class="apr-empty-title">No pending delete requests</div>
    <div class="apr-empty-sub">All delete requests have been reviewed</div>
  </div>
  @else
  <div class="apr-cards">
    @foreach($pendingDeletes as $req)
    @php
      $delDesc   = $req->inventoryItem?->description ?? $req->asset_description ?? '(Asset removed)';
      $delClass  = $req->inventoryItem?->asset_class ?? $req->asset_class ?? '';
      $delAssetNo = $req->inventoryItem?->asset_number ?? $req->asset_number ?? '—';
    @endphp
    <div class="apr-card">
      <div class="apr-card-icon" style="background:rgba(239,68,68,.1)"><i class="bi bi-trash" style="color:#dc2626"></i></div>
      <div class="apr-card-body">
        <div class="apr-card-title">{{ $delDesc }}</div>
        <div class="apr-card-tags">
          @if($delClass)<span class="apr-tag">{{ $delClass }}</span>@endif
          <span class="apr-tag-muted" style="color:var(--accent);font-weight:600">{{ $delAssetNo }}</span>
          @if($req->reason)<span class="apr-tag-muted" style="font-style:italic">"{{ $req->reason }}"</span>@endif
        </div>
        <span class="apr-requester"><i class="bi bi-person-fill" style="font-size:11px"></i>{{ $req->requester?->full_name ?? '—' }}</span>
      </div>
      <div class="apr-card-right">
        <div class="apr-date">{{ \Carbon\Carbon::parse($req->created_at)->format('d M Y') }}<br>{{ \Carbon\Carbon::parse($req->created_at)->format('H:i') }}</div>
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
    @endforeach
  </div>
  @endif
</div>

<!-- SECTION 4: EDIT ASSET REQUESTS -->
<div class="apr-section">
  <div class="apr-section-hdr">
    <div class="apr-section-hdr-line" style="background:#2563eb"></div>
    <i class="bi bi-pencil-square" style="color:#2563eb;font-size:16px"></i>
    <span class="apr-section-hdr-text">Edit Asset Requests</span>
    <span class="apr-section-hdr-count" style="background:rgba(37,99,235,.12);color:#2563eb">{{ $pendingEditCount }}</span>
  </div>
  @if($pendingEditCount === 0)
  <div class="apr-empty">
    <i class="bi bi-pencil-square apr-empty-icon" style="color:#2563eb"></i>
    <div class="apr-empty-title">No pending edit requests</div>
    <div class="apr-empty-sub">All edit requests have been reviewed</div>
  </div>
  @else
  <div class="apr-cards">
    @foreach($pendingEdits as $req)
    <div class="apr-card">
      <div class="apr-card-icon" style="background:rgba(37,99,235,.1)"><i class="bi bi-pencil-square" style="color:#2563eb"></i></div>
      <div class="apr-card-body">
        <div class="apr-card-title">{{ $req->description }}</div>
        <div style="font-size:11px;color:var(--muted);margin-bottom:6px">Current: <em>{{ $req->inventoryItem?->description ?? '—' }}</em></div>
        <div class="apr-card-tags">
          @if($req->asset_class)<span class="apr-tag">{{ $req->asset_class }}</span>@endif
          @if($req->asset_number)<span class="apr-tag-muted" style="color:var(--accent);font-weight:600">{{ $req->asset_number }}</span>@endif
          @if($req->serial_number)<span class="apr-tag-muted">S/N {{ $req->serial_number }}</span>@endif
          @if($req->location)<span class="apr-tag-muted"><i class="bi bi-geo-alt" style="font-size:10px"></i> {{ $req->location }}</span>@endif
        </div>
        <span class="apr-requester"><i class="bi bi-person-fill" style="font-size:11px"></i>{{ $req->requester?->full_name ?? '—' }}</span>
      </div>
      <div class="apr-card-right">
        <div class="apr-date">{{ \Carbon\Carbon::parse($req->created_at)->format('d M Y') }}<br>{{ \Carbon\Carbon::parse($req->created_at)->format('H:i') }}</div>
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
    @endforeach
  </div>
  @endif
</div>

</div>{{-- /apr-wrap --}}

