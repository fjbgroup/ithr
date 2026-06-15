<style>
/* ══ My Requests ══ */
.myr-wrap{max-width:860px;margin:0 auto}
.myr-hero{background:linear-gradient(135deg,var(--navy,#142b47) 0%,#1e3a5f 100%);border-radius:16px;padding:28px 32px;margin-bottom:28px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap}
.myr-hero-title{font-family:'DM Sans',sans-serif;font-size:24px;font-weight:800;color:#fff;margin:0 0 5px}
.myr-hero-sub{font-size:13px;color:rgba(255,255,255,.55);margin:0}
.myr-hero-badge{background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.18);border-radius:10px;padding:12px 20px;text-align:center;flex-shrink:0}
.myr-hero-badge-num{font-size:28px;font-weight:800;color:#fff;line-height:1;font-family:'DM Sans',sans-serif}
.myr-hero-badge-lbl{font-size:10px;color:rgba(255,255,255,.55);text-transform:uppercase;letter-spacing:.07em;margin-top:3px}
.myr-tiles{display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:12px;margin-bottom:28px}
.myr-tile{background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:16px}
.myr-tile-icon{width:36px;height:36px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:15px;margin-bottom:10px}
.myr-tile-num{font-size:26px;font-weight:800;color:var(--text);line-height:1;font-family:'DM Sans',sans-serif}
.myr-tile-lbl{font-size:10px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-top:3px}
.myr-section{background:var(--surface);border:1px solid var(--border);border-radius:14px;overflow:hidden;margin-bottom:20px}
.myr-section-hdr{display:flex;align-items:center;gap:10px;padding:14px 18px;border-bottom:1px solid var(--border);background:var(--body-bg)}
.myr-section-hdr-accent{width:4px;height:20px;border-radius:3px;flex-shrink:0}
.myr-section-hdr-text{font-family:'DM Sans',sans-serif;font-size:14px;font-weight:800;color:var(--text);flex:1}
.myr-section-hdr-count{font-size:11px;font-weight:700;border-radius:20px;padding:3px 12px}
.myr-row{border-bottom:1px solid var(--border)}
.myr-row:last-child{border-bottom:none}
.myr-row-top{display:flex;align-items:flex-start;gap:14px;padding:14px 18px}
.myr-row-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0;margin-top:1px}
.myr-row-body{flex:1;min-width:0}
.myr-row-title{font-size:13.5px;font-weight:700;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:4px}
.myr-row-meta{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.myr-tag{display:inline-block;background:rgba(37,99,235,.1);color:#2563eb;border-radius:5px;padding:2px 8px;font-size:11px;font-weight:700;white-space:nowrap}
.myr-tag-muted{font-size:11px;color:var(--muted);white-space:nowrap}
.myr-row-right{display:flex;flex-direction:column;align-items:flex-end;gap:7px;flex-shrink:0;min-width:100px}
.myr-row-date{font-size:11px;color:var(--muted);text-align:right;line-height:1.6}
.myr-badge-pending{display:inline-flex;align-items:center;gap:4px;background:rgba(217,119,6,.1);color:#d97706;border-radius:20px;padding:3px 10px;font-size:11px;font-weight:700;white-space:nowrap}
.myr-badge-approved{display:inline-flex;align-items:center;gap:4px;background:rgba(22,163,74,.1);color:#16a34a;border-radius:20px;padding:3px 10px;font-size:11px;font-weight:700;white-space:nowrap}
.myr-badge-rejected{display:inline-flex;align-items:center;gap:4px;background:rgba(220,38,38,.1);color:#dc2626;border-radius:20px;padding:3px 10px;font-size:11px;font-weight:700;white-space:nowrap}
.myr-retract{display:inline-flex;align-items:center;gap:4px;background:var(--body-bg);color:var(--muted);border:1px solid var(--border);border-radius:7px;padding:4px 10px;font-size:11px;font-weight:600;text-decoration:none;white-space:nowrap;transition:border-color .12s,color .12s;cursor:pointer}
.myr-retract:hover{border-color:#dc2626;color:#dc2626}
.myr-detail{border-top:1px solid var(--border);padding:11px 18px 11px 72px;background:var(--body-bg)}
.myr-detail-label{font-size:11px;font-weight:700;margin-bottom:7px;display:flex;align-items:center;gap:5px}
.myr-detail-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:5px 18px}
.myr-detail-kv{font-size:11px;color:var(--muted)}
.myr-detail-kv strong{color:var(--text);display:block;font-size:10px;text-transform:uppercase;letter-spacing:.05em;margin-bottom:1px}
.myr-progress-bar{border-top:1px solid var(--border);padding:12px 18px;background:var(--body-bg);display:flex;align-items:center;overflow-x:auto}
.myr-bulk-drop{display:none;border-top:1px solid var(--border);background:var(--body-bg)}
.myr-bulk-row{display:flex;align-items:center;gap:10px;padding:8px 18px 8px 72px;border-bottom:1px solid var(--border);font-size:12px}
.myr-bulk-row:last-child{border-bottom:none}
.myr-pager{display:flex;align-items:center;justify-content:space-between;padding:10px 18px;border-top:1px solid var(--border);background:var(--body-bg)}
.myr-pager-info{font-size:11px;color:var(--muted);font-weight:600}
.myr-pager-btns{display:flex;gap:6px}
.myr-pager-btn{background:var(--surface);border:1px solid var(--border);border-radius:7px;padding:5px 12px;font-size:12px;font-weight:600;color:var(--text);cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .12s}
.myr-pager-btn:hover:not(:disabled){border-color:var(--accent);color:var(--accent)}
.myr-pager-btn:disabled{opacity:.4;cursor:not-allowed}
.myr-empty{padding:40px 24px;text-align:center}
.myr-empty-icon{font-size:36px;display:block;margin-bottom:12px;opacity:.3}
.myr-empty-title{font-size:13px;font-weight:700;color:var(--text);margin-bottom:4px}
.myr-empty-sub{font-size:11px;color:var(--muted)}
</style>

@php
  $myAddCount  = $myAdds->count();
  $myEwCount   = $myEw->count();
  $myDispCount = $myDisposals->count();
  $myDelCount  = $myDeletes->count();
  $myEditCount = $myEdits->count();

  function myrBadge($status) {
    if ($status === 'Pending')  return '<span class="myr-badge-pending"><span style="width:5px;height:5px;border-radius:50%;background:#d97706;display:inline-block"></span>Pending</span>';
    if ($status === 'Approved') return '<span class="myr-badge-approved"><i class="bi bi-check-lg" style="font-size:10px"></i>Approved</span>';
    return '<span class="myr-badge-rejected"><i class="bi bi-x-lg" style="font-size:10px"></i>Rejected</span>';
  }
  function myrDetailBlock($status, $kvs) {
    $clr = $status === 'Approved' ? '#16a34a' : '#dc2626';
    $ico = $status === 'Approved' ? 'check-circle-fill' : 'x-circle-fill';
    $lbl = $status === 'Approved' ? 'Approved' : 'Rejected';
    $html  = '<div class="myr-detail">';
    $html .= '<div class="myr-detail-label" style="color:'.$clr.'"><i class="bi bi-'.$ico.'" style="font-size:13px"></i>'.$lbl.'</div>';
    $html .= '<div class="myr-detail-grid">';
    foreach ($kvs as $k => $v) {
      if ($v === '' || $v === null) continue;
      $html .= '<div class="myr-detail-kv"><strong>'.e($k).'</strong>'.e($v).'</div>';
    }
    $html .= '</div></div>';
    return $html;
  }
@endphp

<div class="myr-wrap">

<div class="myr-hero">
  <div>
    <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:rgba(255,255,255,.4);margin-bottom:6px">IT Assets</div>
    <h4 class="myr-hero-title">My Requests</h4>
    <p class="myr-hero-sub">Track all your submitted requests</p>
  </div>
  <div style="display:flex;gap:10px;flex-wrap:wrap">
    <div class="myr-hero-badge">
      <div class="myr-hero-badge-num">{{ $totalMy }}</div>
      <div class="myr-hero-badge-lbl">Total</div>
    </div>
    @if($myPending > 0)
    <div class="myr-hero-badge" style="background:rgba(217,119,6,.25);border-color:rgba(217,119,6,.5)">
      <div class="myr-hero-badge-num" style="color:#fbbf24">{{ $myPending }}</div>
      <div class="myr-hero-badge-lbl">Pending</div>
    </div>
    @endif
  </div>
</div>

<div class="myr-tiles">
  @foreach([
    ['#16a34a','bi-plus-circle-fill', $myAddCount,  'Add Asset'],
    ['#d97706','bi-recycle',          $myEwCount,   'E-Waste'],
    ['#7c3aed','bi-pen-fill',         $myDispCount, 'Disposal'],
    ['#dc2626','bi-trash-fill',       $myDelCount,  'Delete'],
    ['#2563eb','bi-pencil-square',    $myEditCount, 'Edit'],
  ] as [$clr,$ico,$n,$lbl])
  <div class="myr-tile" style="border-top:3px solid {{ $clr }}">
    <div class="myr-tile-icon" style="background:{{ $clr }}1a;color:{{ $clr }}"><i class="bi {{ $ico }}"></i></div>
    <div class="myr-tile-num">{{ $n }}</div>
    <div class="myr-tile-lbl">{{ $lbl }}</div>
  </div>
  @endforeach
</div>

{{-- ── SECTION 1: ADD ASSET ── --}}
@php
  $addRows = [];
  foreach ($myAdds as $req) {
    $resolved = $req->status !== 'Pending';
    $row  = '<div class="myr-row">';
    $row .= '<div class="myr-row-top">';
    $row .= '<div class="myr-row-icon" style="background:rgba(22,163,74,.1)"><i class="bi bi-plus-circle" style="color:#16a34a"></i></div>';
    $row .= '<div class="myr-row-body">';
    $row .= '<div class="myr-row-title">'.e($req->description).'</div>';
    $row .= '<div class="myr-row-meta">';
    if ($req->asset_class)  $row .= '<span class="myr-tag">'.e($req->asset_class).'</span>';
    if ($req->asset_number) $row .= '<span class="myr-tag-muted" style="color:var(--accent);font-weight:600">'.e($req->asset_number).'</span>';
    if ($req->serial_number)$row .= '<span class="myr-tag-muted">S/N '.e($req->serial_number).'</span>';
    if ($req->brand || $req->model) $row .= '<span class="myr-tag-muted">'.e(trim(($req->brand??'').' '.($req->model??''))).'</span>';
    $row .= '</div></div>';
    $row .= '<div class="myr-row-right">';
    $row .= '<div class="myr-row-date">'.\Carbon\Carbon::parse($req->created_at)->format('d M Y').'<br>'.\Carbon\Carbon::parse($req->created_at)->format('H:i').'</div>';
    $row .= myrBadge($req->status);
    if ($req->status === 'Pending') {
      $row .= '<form method="POST" action="'.route('it.requests.add.retract', $req->id).'" style="display:inline" onsubmit="return confirm(\'Retract this add request?\')"><input type="hidden" name="_token" value="'.csrf_token().'"><input type="hidden" name="_method" value="DELETE"><button type="submit" class="myr-retract"><i class="bi bi-arrow-counterclockwise"></i> Retract</button></form>';
    }
    $row .= '</div></div>';
    if ($resolved) $row .= myrDetailBlock($req->status, [
      'Description' => $req->description,
      'Asset Class'  => $req->asset_class ?? '',
      'Location'     => $req->location ?? '',
      'Reviewed'     => $req->reviewed_at ? \Carbon\Carbon::parse($req->reviewed_at)->format('d M Y, H:i') : '',
    ]);
    $row .= '</div>';
    $addRows[] = $row;
  }
@endphp

@php
  function myrSection($id, $title, $icon, $color, $bgAlpha, $rows, $emptyIcon, $emptyTitle, $emptySub, $perPage = 5) {
    $count = count($rows);
    $html  = '<div class="myr-section">';
    $html .= '<div class="myr-section-hdr">';
    $html .= '<div class="myr-section-hdr-accent" style="background:'.$color.'"></div>';
    $html .= '<i class="bi '.$icon.'" style="color:'.$color.';font-size:15px"></i>';
    $html .= '<span class="myr-section-hdr-text">'.$title.'</span>';
    $html .= '<span class="myr-section-hdr-count" style="background:'.$bgAlpha.';color:'.$color.'">'.$count.'</span>';
    $html .= '</div>';
    if ($count === 0) {
      $html .= '<div class="myr-empty"><i class="bi '.$emptyIcon.' myr-empty-icon" style="color:'.$color.'"></i>';
      $html .= '<div class="myr-empty-title">'.$emptyTitle.'</div><div class="myr-empty-sub">'.$emptySub.'</div></div>';
    } else {
      $html .= '<div class="myr-list">';
      foreach ($rows as $i => $rowHtml) {
        $hidden = $i >= $perPage ? ' style="display:none"' : '';
        $html .= '<div class="myr-list-page-item" data-sec="'.e($id).'" data-idx="'.$i.'"'.$hidden.'>'.$rowHtml.'</div>';
      }
      $html .= '</div>';
      if ($count > $perPage) {
        $pages = ceil($count / $perPage);
        $html .= '<div class="myr-pager" id="pager-'.$id.'" data-sec="'.$id.'" data-page="0" data-total="'.$count.'" data-per="'.$perPage.'" data-pages="'.$pages.'">';
        $html .= '<span class="myr-pager-info" id="pinfo-'.$id.'">Showing 1&ndash;'.min($perPage,$count).' of '.$count.'</span>';
        $html .= '<div class="myr-pager-btns">';
        $html .= '<button class="myr-pager-btn" id="pprev-'.$id.'" onclick="myrPage(\''.addslashes($id).'\', -1)" disabled><i class="bi bi-chevron-left" style="font-size:11px"></i> Prev</button>';
        $html .= '<button class="myr-pager-btn" id="pnext-'.$id.'" onclick="myrPage(\''.addslashes($id).'\', 1)">Next <i class="bi bi-chevron-right" style="font-size:11px"></i></button>';
        $html .= '</div></div>';
      }
    }
    $html .= '</div>';
    return $html;
  }
@endphp

{{-- ── SECTION 2: E-WASTE ── --}}
@php
  $ewRows = [];
  foreach ($myEw as $req) {
    $resolved = $req->status !== 'Pending';
    $row  = '<div class="myr-row">';
    $row .= '<div class="myr-row-top">';
    $row .= '<div class="myr-row-icon" style="background:rgba(217,119,6,.1)"><i class="bi bi-recycle" style="color:#d97706"></i></div>';
    $row .= '<div class="myr-row-body">';
    $row .= '<div class="myr-row-meta" style="margin-bottom:4px">';
    $row .= '<span style="display:inline-flex;align-items:center;gap:4px;background:rgba(22,163,74,.1);color:#16a34a;border-radius:5px;padding:2px 8px;font-size:10px;font-weight:700"><i class="bi bi-plus-circle-fill"></i> Add to E-Waste</span>';
    $row .= '</div>';
    $row .= '<div class="myr-row-title">'.e($req->description).'</div>';
    $row .= '<div class="myr-row-meta">';
    if ($req->asset_class)  $row .= '<span class="myr-tag">'.e($req->asset_class).'</span>';
    if ($req->asset_number) $row .= '<span class="myr-tag-muted" style="color:var(--accent);font-weight:600">'.e($req->asset_number).'</span>';
    if ($req->serial_number)$row .= '<span class="myr-tag-muted">S/N '.e($req->serial_number).'</span>';
    $row .= '</div></div>';
    $row .= '<div class="myr-row-right">';
    $row .= '<div class="myr-row-date">'.\Carbon\Carbon::parse($req->created_at)->format('d M Y').'<br>'.\Carbon\Carbon::parse($req->created_at)->format('H:i').'</div>';
    $row .= myrBadge($req->status);
    if ($req->status === 'Pending') {
      $row .= '<form method="POST" action="'.route('it.requests.ewaste.retract', $req->id).'" style="display:inline" onsubmit="return confirm(\'Retract this e-waste request?\')"><input type="hidden" name="_token" value="'.csrf_token().'"><input type="hidden" name="_method" value="DELETE"><button type="submit" class="myr-retract"><i class="bi bi-arrow-counterclockwise"></i> Retract</button></form>';
    }
    $row .= '</div></div>';
    if ($resolved) $row .= myrDetailBlock($req->status, [
      'Asset'    => $req->description,
      'Class'    => $req->asset_class ?? '',
      'Reviewed' => $req->reviewed_at ? \Carbon\Carbon::parse($req->reviewed_at)->format('d M Y, H:i') : '',
    ]);
    $row .= '</div>';
    $ewRows[] = $row;
  }
@endphp

{{-- ── SECTION 3: DISPOSAL (ewaste_items) ── --}}
@php
  $dispRows = [];
  $dgi = 0;
  foreach ($myDisposals as $req) {
    $did = 'dgd'.$dgi++;
    $stepClr = function($st) {
      if ($st === 1) return ['#16a34a','rgba(22,163,74,.12)','bi-check-circle-fill'];
      if ($st === 2) return ['#dc2626','rgba(239,68,68,.12)','bi-x-circle-fill'];
      return ['#94a3b8','rgba(148,163,184,.15)','bi-circle'];
    };
    [$c1,$b1,$i1] = $stepClr($req->hou_status==='Checked'?1:($req->hou_status==='Rejected'?2:0));
    [$c2,$b2,$i2] = $stepClr($req->gm_status==='Checked'?1:($req->gm_status==='Rejected'?2:0));
    [$c3,$b3,$i3] = $stepClr($req->ceo_status==='Approved'?1:($req->ceo_status==='Rejected'?2:0));
    [$c4,$b4,$i4] = $stepClr(in_array($req->finance_status??'',['EWaste','Disposal'])?1:0);
    if (in_array($req->finance_status??'',['EWaste','Disposal'])) $ob='Approved';
    elseif (in_array($req->ceo_status??'',['Rejected'])||in_array($req->gm_status??'',['Rejected'])||in_array($req->hou_status??'',['Rejected'])) $ob='Rejected';
    else $ob='Pending';

    $row  = '<div class="myr-row">';
    $row .= '<div class="myr-row-top">';
    $row .= '<div class="myr-row-icon" style="background:rgba(124,58,237,.1)"><i class="bi bi-pen-fill" style="color:#7c3aed"></i></div>';
    $row .= '<div class="myr-row-body">';
    $row .= '<div class="myr-row-title">'.e($req->description).'</div>';
    $row .= '<div class="myr-row-meta">';
    if ($req->asset_class)  $row .= '<span class="myr-tag">'.e($req->asset_class).'</span>';
    if ($req->asset_number) $row .= '<span class="myr-tag-muted" style="color:var(--accent);font-weight:600">'.e($req->asset_number).'</span>';
    if ($req->serial_number)$row .= '<span class="myr-tag-muted">S/N '.e($req->serial_number).'</span>';
    $row .= '</div></div>';
    $row .= '<div class="myr-row-right">';
    $dateFlagged = $req->date_flagged ?? $req->created_at;
    $row .= '<div class="myr-row-date">'.\Carbon\Carbon::parse($dateFlagged)->format('d M Y').'<br>'.\Carbon\Carbon::parse($req->created_at)->format('H:i').'</div>';
    $row .= myrBadge($ob);
    $row .= '</div></div>';
    // Progress bar
    $row .= '<div class="myr-progress-bar">';
    foreach ([[$b1,$c1,$i1,'HOU'],[$b2,$c2,$i2,'GM'],[$b3,$c3,$i3,'CEO'],[$b4,$c4,$i4,'Finance']] as $k => [$bg,$clr,$ico,$lbl]) {
      $row .= '<div style="display:flex;align-items:center;flex-shrink:0">';
      $row .= '<div style="display:flex;flex-direction:column;align-items:center;gap:4px;min-width:62px">';
      $row .= '<div style="width:26px;height:26px;border-radius:50%;background:'.$bg.';display:flex;align-items:center;justify-content:center"><i class="bi '.$ico.'" style="color:'.$clr.';font-size:11px"></i></div>';
      $row .= '<span style="font-size:10px;font-weight:700;color:'.$clr.';text-transform:uppercase;letter-spacing:.04em">'.$lbl.'</span>';
      $row .= '</div>';
      if ($k < 3) $row .= '<div style="width:28px;height:2px;background:var(--border);margin-bottom:14px;flex-shrink:0"></div>';
      $row .= '</div>';
    }
    if (($req->finance_status??'')==='EWaste') {
      $row .= '<div style="margin-left:10px;display:inline-flex;align-items:center;gap:5px;background:rgba(13,148,136,.1);border-radius:7px;padding:4px 10px;flex-shrink:0"><i class="bi bi-recycle" style="color:#0d9488;font-size:11px"></i><span style="font-size:10px;font-weight:700;color:#0d9488">E-Waste</span></div>';
    } elseif (($req->finance_status??'')==='Disposal') {
      $row .= '<div style="margin-left:10px;display:inline-flex;align-items:center;gap:5px;background:rgba(239,68,68,.1);border-radius:7px;padding:4px 10px;flex-shrink:0"><i class="bi bi-trash3-fill" style="color:#dc2626;font-size:11px"></i><span style="font-size:10px;font-weight:700;color:#dc2626">Disposal</span></div>';
    }
    $row .= '</div>';
    if ($ob !== 'Pending') $row .= myrDetailBlock($ob, array_filter([
      'Outcome' => $ob==='Approved'?(($req->finance_status==='EWaste')?'Routed to E-Waste':(($req->finance_status==='Disposal')?'Routed to Disposal':'Approved')):'Rejected',
      'HOU'     => ($req->hou_signed_name??'')?e($req->hou_signed_name).($req->hou_signed_at?' · '.\Carbon\Carbon::parse($req->hou_signed_at)->format('d M Y'):''):'',
      'GM'      => ($req->gm_signed_name??'')?e($req->gm_signed_name).($req->gm_signed_at?' · '.\Carbon\Carbon::parse($req->gm_signed_at)->format('d M Y'):''):'',
      'CEO'     => ($req->ceo_signed_name??'')?e($req->ceo_signed_name).($req->ceo_signed_at?' · '.\Carbon\Carbon::parse($req->ceo_signed_at)->format('d M Y'):''):'',
    ]));
    $row .= '</div>';
    $dispRows[] = $row;
  }
@endphp

{{-- ── SECTION 4: DELETE ── --}}
@php
  $delRows = [];
  foreach ($myDeletes as $req) {
    $resolved = $req->status !== 'Pending';
    $desc    = $req->inventoryItem?->description ?? $req->asset_description ?? '—';
    $assetNo = $req->inventoryItem?->asset_number ?? $req->asset_number ?? '—';
    $cls     = $req->inventoryItem?->asset_class ?? $req->asset_class ?? '';
    $row  = '<div class="myr-row">';
    $row .= '<div class="myr-row-top">';
    $row .= '<div class="myr-row-icon" style="background:rgba(239,68,68,.1)"><i class="bi bi-trash" style="color:#dc2626"></i></div>';
    $row .= '<div class="myr-row-body">';
    $row .= '<div class="myr-row-title">'.e($desc).'</div>';
    $row .= '<div class="myr-row-meta">';
    if ($cls)          $row .= '<span class="myr-tag">'.e($cls).'</span>';
    if ($assetNo)      $row .= '<span class="myr-tag-muted" style="color:var(--accent);font-weight:600">'.e($assetNo).'</span>';
    if ($req->reason)  $row .= '<span class="myr-tag-muted" style="font-style:italic">"'.e($req->reason).'"</span>';
    $row .= '</div></div>';
    $row .= '<div class="myr-row-right">';
    $row .= '<div class="myr-row-date">'.\Carbon\Carbon::parse($req->created_at)->format('d M Y').'<br>'.\Carbon\Carbon::parse($req->created_at)->format('H:i').'</div>';
    $row .= myrBadge($req->status);
    if ($req->status === 'Pending') {
      $row .= '<form method="POST" action="'.route('it.requests.delete.retract', $req->id).'" style="display:inline" onsubmit="return confirm(\'Retract this delete request?\')"><input type="hidden" name="_token" value="'.csrf_token().'"><input type="hidden" name="_method" value="DELETE"><button type="submit" class="myr-retract"><i class="bi bi-arrow-counterclockwise"></i> Retract</button></form>';
    }
    $row .= '</div></div>';
    if ($resolved) $row .= myrDetailBlock($req->status, [
      'Asset'    => $desc,
      'Asset No' => $assetNo,
      'Reason'   => $req->reason ?? '',
      'Reviewed' => $req->reviewed_at ? \Carbon\Carbon::parse($req->reviewed_at)->format('d M Y, H:i') : '',
    ]);
    $row .= '</div>';
    $delRows[] = $row;
  }
@endphp

{{-- ── SECTION 5: EDIT ── --}}
@php
  $editRows = [];
  foreach ($myEdits as $req) {
    $resolved = $req->status !== 'Pending';
    $row  = '<div class="myr-row">';
    $row .= '<div class="myr-row-top">';
    $row .= '<div class="myr-row-icon" style="background:rgba(37,99,235,.1)"><i class="bi bi-pencil-square" style="color:#2563eb"></i></div>';
    $row .= '<div class="myr-row-body">';
    $row .= '<div class="myr-row-title">'.e($req->description).'</div>';
    $currentDesc = $req->inventoryItem?->description ?? '—';
    $row .= '<div style="font-size:11px;color:var(--muted);margin-bottom:4px">Currently: <em>'.e($currentDesc).'</em></div>';
    $row .= '<div class="myr-row-meta">';
    if ($req->asset_class)   $row .= '<span class="myr-tag">'.e($req->asset_class).'</span>';
    if ($req->serial_number) $row .= '<span class="myr-tag-muted">S/N '.e($req->serial_number).'</span>';
    if ($req->location)      $row .= '<span class="myr-tag-muted"><i class="bi bi-geo-alt" style="font-size:10px"></i> '.e($req->location).'</span>';
    $row .= '</div></div>';
    $row .= '<div class="myr-row-right">';
    $row .= '<div class="myr-row-date">'.\Carbon\Carbon::parse($req->created_at)->format('d M Y').'<br>'.\Carbon\Carbon::parse($req->created_at)->format('H:i').'</div>';
    $row .= myrBadge($req->status);
    $row .= '</div></div>';
    if ($resolved) {
      $kv = ['Asset Class'=>$req->asset_class??'','Location'=>$req->location??''];
      if ($req->description !== $currentDesc) $kv['New Description'] = $req->description;
      if ($req->reviewed_at) $kv['Reviewed'] = \Carbon\Carbon::parse($req->reviewed_at)->format('d M Y, H:i');
      $row .= myrDetailBlock($req->status, $kv);
    }
    $row .= '</div>';
    $editRows[] = $row;
  }
@endphp

{!! myrSection('add',  'Add Asset Requests',  'bi-plus-circle-fill','#16a34a','rgba(22,163,74,.1)',  $addRows,  'bi-box-seam',     'No add asset requests yet',  'When you request a new asset, it will appear here') !!}
{!! myrSection('ew',   'E-Waste Requests',    'bi-recycle',         '#d97706','rgba(217,119,6,.1)', $ewRows,   'bi-recycle',      'No e-waste requests yet',    'E-waste requests will appear here') !!}
{!! myrSection('disp', 'Disposal Requests',   'bi-pen-fill',        '#7c3aed','rgba(124,58,237,.1)',$dispRows, 'bi-pen',          'No disposal requests yet',   'Write-off forms you submit will appear here') !!}
{!! myrSection('del',  'Delete Requests',     'bi-trash-fill',      '#dc2626','rgba(220,38,38,.1)', $delRows,  'bi-trash',        'No delete requests yet',     'Asset deletion requests will appear here') !!}
{!! myrSection('edit', 'Edit Asset Requests', 'bi-pencil-square',   '#2563eb','rgba(37,99,235,.1)', $editRows, 'bi-pencil-square','No edit requests yet',       'Your IT asset edit requests will appear here') !!}

</div>{{-- /myr-wrap --}}

<script>
function myrPage(id, dir) {
  var pager = document.getElementById('pager-'+id);
  if (!pager) return;
  var page=parseInt(pager.dataset.page), total=parseInt(pager.dataset.total),
      per=parseInt(pager.dataset.per),   pages=parseInt(pager.dataset.pages);
  var np = page + dir;
  if (np < 0 || np >= pages) return;
  document.querySelectorAll('.myr-list-page-item[data-sec="'+id+'"]').forEach(function(el) {
    var idx = parseInt(el.dataset.idx);
    el.style.display = (idx >= np*per && idx < (np+1)*per) ? '' : 'none';
  });
  pager.dataset.page = np;
  var from=np*per+1, to=Math.min((np+1)*per,total);
  document.getElementById('pinfo-'+id).innerHTML = 'Showing '+from+'&ndash;'+to+' of '+total;
  document.getElementById('pprev-'+id).disabled = np===0;
  document.getElementById('pnext-'+id).disabled = np>=pages-1;
}
</script>

