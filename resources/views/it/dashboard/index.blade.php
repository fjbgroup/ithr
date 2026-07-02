@extends('it.layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@push('styles')
<style>
/* ── All colours via CSS vars → auto light/dark ── */
.ds-card{
  display:flex;align-items:center;gap:18px;text-decoration:none;
  background:var(--surface);border:1px solid var(--border);border-radius:12px;
  padding:22px 24px;
  box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 14px rgba(0,0,0,.07);
  transition:box-shadow .2s,transform .2s;height:100%
}
.ds-card:hover{box-shadow:0 6px 24px rgba(0,0,0,.15);transform:translateY(-2px)}
.ds-icon{width:46px;height:46px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0}
.ds-num{font-size:30px;font-weight:800;color:var(--text);line-height:1;font-family:'Inter',sans-serif}
.ds-lbl{font-size:12px;color:var(--muted);margin-top:5px;font-weight:500}
.ds-card-title{font-size:14px;font-weight:800;color:var(--text);line-height:1.25}
.ds-card-sub{font-size:12px;color:var(--muted);margin-top:4px;line-height:1.35}

.ds-box{background:var(--surface);border:1px solid var(--border);border-radius:12px;overflow:clip;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 14px rgba(0,0,0,.07)}
.ds-box-head{padding:18px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:12px;position:sticky;top:60px;z-index:5;background:var(--surface)}
.ds-box-title{font-size:14px;font-weight:700;color:var(--text)}
.ds-box-sub{font-size:12px;color:var(--muted);margin-top:2px}

.ds-tab{padding:6px 16px;border-radius:7px;border:none;font-size:12px;font-weight:700;cursor:pointer;font-family:'Inter',sans-serif;transition:all .15s}
.ds-tab.on{background:var(--navy,#142b47);color:#fff}
.ds-tab.off{background:transparent;color:var(--muted)}

.ds-feed-row{display:flex;gap:12px;align-items:flex-start;padding:14px 24px;border-bottom:1px solid var(--border)}
.ds-feed-row:last-child{border-bottom:none}
.ds-feed-dot{width:7px;height:7px;border-radius:50%;background:var(--border);flex-shrink:0;margin-top:5px}
.ds-feed-main{font-size:13px;color:var(--text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.ds-feed-meta{font-size:11px;color:var(--muted);margin-top:3px}

.ds-qa{display:flex;align-items:center;gap:12px;padding:13px 16px;border:1px solid var(--border);border-radius:10px;text-decoration:none;background:var(--surface);transition:background .12s,border-color .12s}
.ds-qa:hover{background:var(--body-bg);border-color:var(--muted)}
.ds-qa-icon{width:36px;height:36px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0}
.ds-qa-lbl{font-size:13px;font-weight:600;color:var(--text);flex:1}

.ds-tbl{width:100%;border-collapse:collapse;font-size:13px}
.ds-tbl th{padding:13px 22px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);background:var(--body-bg);border-bottom:1px solid var(--border);white-space:nowrap;text-align:left}
.ds-tbl td{padding:14px 22px;border-bottom:1px solid var(--border);vertical-align:middle;color:var(--text)}
.ds-tbl tbody tr:last-child td{border-bottom:none}
.ds-tbl tbody tr:hover td{background:var(--body-bg)}

/* Status bar */
.ds-status-bar{background:var(--surface);border:1px solid var(--border);border-radius:12px;margin-bottom:24px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 14px rgba(0,0,0,.07)}
.ds-status-cell{padding:18px 24px;border-right:1px solid var(--border);display:flex;align-items:center;gap:12px}
.ds-status-cell:last-child{border-right:none}

/* Industrial chart wrapper — keeps dark canvas but themed frame */
.ds-chart-wrap{background:var(--surface);border:1px solid var(--border);border-radius:14px;overflow:clip;box-shadow:0 4px 24px rgba(0,0,0,.12)}
.ds-chart-head{padding:18px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;position:sticky;top:60px;z-index:5;background:var(--surface)}
.ds-chart-ticker{background:var(--body-bg);border-bottom:1px solid var(--border);padding:7px 24px;display:flex;align-items:center;gap:24px;flex-wrap:wrap}
.ds-chart-footer{padding:0 28px 18px;display:flex;align-items:center;justify-content:flex-end;gap:16px;background:var(--surface)}

@keyframes blink{0%,100%{opacity:1}50%{opacity:.3}}
</style>
@endpush

@section('content')
@php $user = auth('it')->user(); @endphp

<!-- ═══ SYSTEM STATUS BAR ═══ -->
<div style="background:linear-gradient(160deg,#FFB84D 0%,#F7941D 50%,#C96800 100%);border-radius:14px;margin-bottom:24px;overflow:hidden;box-shadow:0 8px 28px rgba(247,148,29,.32)">
  <div style="display:flex;align-items:center;flex-wrap:wrap;gap:0;padding:22px 28px">

    <!-- Greeting -->
    <div style="flex:1;min-width:220px">
      <div style="font-size:20px;font-weight:800;color:#fff;font-family:'Inter',sans-serif;line-height:1.2" id="dsGreeting">
        Good Morning, {{ $user->full_name }}
      </div>
      <div style="font-size:12px;color:rgba(255,255,255,.78);margin-top:5px;font-weight:500">
        {{ now()->format('l, d F Y') }} &nbsp;·&nbsp; {{ $user->isAdmin() ? 'IT Admin' : $user->getItRoleLabel() }}
      </div>
    </div>

    <!-- Stats -->
    <div style="display:flex;align-items:center;gap:0;border-left:1px solid rgba(255,255,255,.24);border-right:1px solid rgba(255,255,255,.24);margin:0 28px" id="itDashboardStats" data-auto-refresh="true">
      <div style="padding:0 28px;text-align:center">
        <div style="font-size:11px;font-weight:600;color:rgba(255,255,255,.76);text-transform:uppercase;letter-spacing:.08em;margin-bottom:4px">Total Assets</div>
        <div style="font-size:26px;font-weight:800;color:#fff;font-family:'Inter',sans-serif;line-height:1">{{ number_format($totalAll) }}</div>
      </div>
      <div style="width:1px;height:36px;background:rgba(255,255,255,.24)"></div>
      <div style="padding:0 28px;text-align:center">
        <div style="font-size:11px;font-weight:600;color:rgba(255,255,255,.76);text-transform:uppercase;letter-spacing:.08em;margin-bottom:4px">Active</div>
        <div style="font-size:26px;font-weight:800;color:#fff;font-family:'Inter',sans-serif;line-height:1">{{ number_format($activeIT) }}</div>
      </div>
      <div style="width:1px;height:36px;background:rgba(255,255,255,.24)"></div>
      <div style="padding:0 28px;text-align:center">
        <div style="font-size:11px;font-weight:600;color:rgba(255,255,255,.76);text-transform:uppercase;letter-spacing:.08em;margin-bottom:4px">Pending</div>
        <div style="font-size:26px;font-weight:800;color:{{ $pendingAll > 0 ? '#fff7ed' : 'rgba(255,255,255,.42)' }};font-family:'Inter',sans-serif;line-height:1">{{ number_format($pendingAll) }}</div>
      </div>
    </div>

    <!-- Live Clock -->
    <div style="text-align:right">
      <div id="dsLiveClock" style="font-size:28px;font-weight:800;color:#fff;font-family:'Inter',sans-serif;letter-spacing:.04em;line-height:1;font-variant-numeric:tabular-nums;min-width:168px;display:inline-block;text-align:right"></div>
      <div style="font-size:10px;color:rgba(255,255,255,.74);margin-top:4px;font-weight:600;text-transform:uppercase;letter-spacing:.08em">FJB Inventory System</div>
    </div>

  </div>
</div>


<!-- ═══ COMMON ROLE CARDS ═══ -->
<div class="row g-4 mb-4">
  <div class="col-sm-6 col-xl-3">
    <a href="{{ route('it.inventory.index') }}" class="ds-card">
      <div class="ds-icon" style="background:rgba(2,132,199,.12);color:#0284c7"><i class="bi bi-box-seam"></i></div>
      <div>
        <div class="ds-card-title">IT Assets</div>
        <div class="ds-card-sub">View inventory records</div>
      </div>
    </a>
  </div>
  <div class="col-sm-6 col-xl-3">
    <a href="{{ route('it.non-it.index') }}" class="ds-card">
      <div class="ds-icon" style="background:rgba(20,184,166,.12);color:#0f766e"><i class="bi bi-boxes"></i></div>
      <div>
        <div class="ds-card-title">Non-IT Assets</div>
        <div class="ds-card-sub">View non-IT records</div>
      </div>
    </a>
  </div>
  <div class="col-sm-6 col-xl-3">
    <a href="{{ route('it.writeoff.index') }}" class="ds-card">
      <div class="ds-icon" style="background:rgba(245,158,11,.14);color:#d97706"><i class="bi bi-pen-fill"></i></div>
      <div>
        <div class="ds-card-title">Write Off</div>
        <div class="ds-card-sub">Open write-off workflow</div>
      </div>
    </a>
  </div>
  <div class="col-sm-6 col-xl-3">
    <a href="{{ route('it.role-metric') }}" class="ds-card">
      <div class="ds-icon" style="background:rgba(99,102,241,.13);color:#4f46e5"><i class="bi bi-table"></i></div>
      <div>
        <div class="ds-card-title">Role Metric</div>
        <div class="ds-card-sub">Read access matrix</div>
      </div>
    </a>
  </div>
</div>

<!-- ═══ CHART ROW ═══ -->
<div class="row g-4 mb-4">
  <div class="col-12">
    <div class="ds-chart-wrap">

      <!-- Header -->
      <div class="ds-chart-head">
        <div style="display:flex;align-items:center;gap:14px">
          <div style="width:3px;height:32px;background:linear-gradient(180deg,#f97316,#fbbf24);border-radius:2px;flex-shrink:0"></div>
          <div>
            <div style="font-size:13px;font-weight:800;color:var(--text);letter-spacing:.04em;text-transform:uppercase">Asset Class Distribution</div>
            <div style="font-size:11px;color:var(--muted);margin-top:2px;letter-spacing:.06em;text-transform:uppercase">Inventory Analysis</div>
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:16px">
          <!-- Total badge -->
          <div id="chartTotalBadge" style="background:var(--body-bg);border:1px solid var(--border);border-radius:8px;padding:6px 14px;display:flex;align-items:center;gap:8px">
            <div style="width:6px;height:6px;border-radius:50%;background:#f97316;flex-shrink:0"></div>
            <span style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.07em">Total&nbsp;</span>
            <span id="chartTotalNum" style="font-size:16px;font-weight:800;color:var(--text);font-family:'Inter',sans-serif">{{ $itTotal }}</span>
          </div>
          <!-- Tabs -->
          <div style="display:flex;background:var(--body-bg);border:1px solid var(--border);border-radius:8px;padding:3px;gap:2px">
            <button id="tabIT" onclick="switchTab('it')"
              style="padding:6px 18px;border-radius:6px;border:none;font-size:11px;font-weight:700;cursor:pointer;font-family:'Inter',sans-serif;letter-spacing:.06em;text-transform:uppercase;background:#f97316;color:#fff;transition:all .2s">
              IT Assets
            </button>
            <button id="tabNIT" onclick="switchTab('nit')"
              style="padding:6px 18px;border-radius:6px;border:none;font-size:11px;font-weight:700;cursor:pointer;font-family:'Inter',sans-serif;letter-spacing:.06em;text-transform:uppercase;background:transparent;color:var(--muted);transition:all .2s">
              Non-IT
            </button>
          </div>
        </div>
      </div>

      <!-- Ticker -->
      <div class="ds-chart-ticker">
        <span style="font-size:10px;font-weight:700;color:#f97316;text-transform:uppercase;letter-spacing:.1em">LIVE DATA</span>
        <span style="font-size:10px;color:var(--muted);font-weight:600">IT CLASSES: <span style="color:var(--text)">{{ $itChartData->count() }}</span></span>
        <span style="font-size:10px;color:var(--muted);font-weight:600">NON-IT CLASSES: <span style="color:var(--text)">{{ $nitChartData->count() }}</span></span>
        <span style="font-size:10px;color:var(--muted);font-weight:600">IT ASSETS: <span style="color:var(--text)">{{ $itTotal }}</span></span>
        <span style="font-size:10px;color:var(--muted);font-weight:600">NON-IT ASSETS: <span style="color:var(--text)">{{ $nitTotal }}</span></span>
      </div>

      <!-- Canvas -->
      <div style="padding:24px 28px 8px;height:360px;position:relative;background:var(--body-bg)">
        @if($itChartData->isEmpty() && $nitChartData->isEmpty())
        <div style="padding:80px;text-align:center;color:#334155;font-size:13px;text-transform:uppercase;letter-spacing:.08em">No assets registered yet.</div>
        @else
        <canvas id="inventoryChart" style="width:100%;height:100%"></canvas>
        @endif
      </div>

      <!-- Footer -->
      <div class="ds-chart-footer" style="padding:10px 28px 14px">
        <span style="font-size:10px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.08em">Units per class</span>
        <div style="flex:1;height:1px;background:var(--border);margin:0 12px"></div>
        <span style="font-size:10px;color:var(--muted);font-weight:600">Top 8 shown</span>
      </div>
    </div>
  </div>
</div>

<!-- ═══ BOTTOM ROW ═══ -->
<div class="row g-4 mb-4">

  <!-- Activity / Quick Actions -->
  <div class="col-lg-4">
    @if($user->isAdminOrFinance())
    <div class="ds-box" style="height:100%">
      <div class="ds-box-head">
        <div>
          <div class="ds-box-title">Recent Activity</div>
          <div class="ds-box-sub">Latest system actions</div>
        </div>
        <a href="{{ route('it.activity.index') }}" style="font-size:11px;font-weight:700;color:#0284c7;text-decoration:none">View all →</a>
      </div>
      @forelse($recentActivity as $log)
      <div class="ds-feed-row">
        <div class="ds-feed-dot"></div>
        <div style="min-width:0;flex:1">
          <div class="ds-feed-main">{{ $log->description ?? $log->action }}</div>
          <div class="ds-feed-meta">{{ $log->full_name ?? 'System' }} · {{ \Carbon\Carbon::parse($log->created_at)->format('d M, H:i') }}</div>
        </div>
      </div>
      @empty
      <div style="padding:48px 24px;text-align:center;color:var(--muted);font-size:13px">No activity yet.</div>
      @endforelse
    </div>

    @else
    <div class="ds-box">
      <div class="ds-box-head"><div class="ds-box-title">Quick Actions</div></div>
      <div style="padding:16px;display:flex;flex-direction:column;gap:8px">
        <a href="{{ route('it.writeoff.index') }}" class="ds-qa">
          <div class="ds-qa-icon" style="background:#fef9c3;color:#ca8a04"><i class="bi bi-pen"></i></div>
          <span class="ds-qa-lbl">Submit Write-Off</span>
          <i class="bi bi-chevron-right" style="color:var(--border);font-size:11px"></i>
        </a>
        <a href="{{ route('it.it-request-form') }}" class="ds-qa">
          <div class="ds-qa-icon" style="background:#ede9fe;color:#7c3aed"><i class="bi bi-file-text"></i></div>
          <span class="ds-qa-lbl">IT Request Form</span>
          <i class="bi bi-chevron-right" style="color:var(--border);font-size:11px"></i>
        </a>
        <a href="{{ route('it.profile') }}" class="ds-qa">
          <div class="ds-qa-icon" style="background:#dcfce7;color:#16a34a"><i class="bi bi-person"></i></div>
          <span class="ds-qa-lbl">My Profile</span>
          <i class="bi bi-chevron-right" style="color:var(--border);font-size:11px"></i>
        </a>
      </div>
    </div>
    @endif
  </div>

  <!-- Recently Added -->
  <div class="col-lg-8">
    <div class="ds-box" style="height:100%">
      <div class="ds-box-head">
        <div>
          <div class="ds-box-title">Recently Added Assets</div>
          <div class="ds-box-sub">Latest entries in the inventory</div>
        </div>
        <a href="{{ route('it.inventory.index') }}" style="font-size:11px;font-weight:700;color:#0284c7;text-decoration:none">View all →</a>
      </div>
      <div class="table-responsive" id="recentAssetsTableWrap" data-auto-refresh="true">
        <table class="ds-tbl">
          <thead><tr><th>Asset #</th><th>Class</th><th>Description</th><th>Status</th></tr></thead>
          <tbody>
          @forelse($recentAssets as $row)
          @php
            $sbg = ['Active'=>'#dcfce7','In Repair'=>'#fef9c3','Disposed'=>'#fee2e2'][$row->item_status] ?? '#f1f5f9';
            $sc  = ['Active'=>'#16a34a','In Repair'=>'#ca8a04','Disposed'=>'#dc2626'][$row->item_status] ?? '#64748b';
          @endphp
          <tr>
            <td><a href="{{ route('it.inventory.index') }}?action=edit&id={{ $row->id }}" style="font-weight:700;color:#0284c7;text-decoration:none">{{ $row->asset_number ?: '—' }}</a></td>
            <td><span style="background:var(--body-bg);border:1px solid var(--border);border-radius:5px;padding:3px 10px;font-size:11px;font-weight:600;color:var(--muted)">{{ $row->asset_class }}</span></td>
            <td style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $row->description }}</td>
            <td><span style="background:{{ $sbg }};color:{{ $sc }};border-radius:20px;padding:4px 12px;font-size:11px;font-weight:600">{{ $row->item_status }}</span></td>
          </tr>
          @empty
          <tr><td colspan="4" style="text-align:center;color:var(--muted);padding:32px">No assets yet.</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ── LIVE CLOCK + GREETING ──
(function(){
  var el = document.getElementById('dsLiveClock');
  var gr = document.getElementById('dsGreeting');
  var name = '{{ $user->full_name }}';
  function tick(){
    var n=new Date(),h=n.getHours(),m=String(n.getMinutes()).padStart(2,'0'),s=String(n.getSeconds()).padStart(2,'0');
    var ampm=h>=12?'PM':'AM',h12=h%12||12;
    if(el) el.textContent=String(h12).padStart(2,'0')+':'+m+':'+s+' '+ampm;
    if(gr){
      var salute = h < 12 ? 'Good Morning' : h < 18 ? 'Good Afternoon' : 'Good Evening';
      gr.textContent = salute + ', ' + name;
    }
  }
  tick(); setInterval(tick,1000);
})();

// ── INDUSTRIAL CHART ──
(function(){
  var itLabels  = @json($itChartData->pluck('label'));
  var itData    = @json($itChartData->pluck('value'));
  var nitLabels = @json($nitChartData->pluck('label'));
  var nitData   = @json($nitChartData->pluck('value'));
  var itTotal   = {{ $itTotal }};
  var nitTotal  = {{ $nitTotal }};

  var canvas = document.getElementById('inventoryChart');
  if (!canvas) return;

  var itPalette  = ['#f97316','#fb923c','#fbbf24','#f59e0b','#ea580c','#c2410c','#e57b10','#d97706'];
  var nitPalette = ['#0284c7','#0ea5e9','#38bdf8','#0369a1','#075985','#60a5fa','#3b82f6','#2563eb'];

  function isDark(){ return document.documentElement.getAttribute('data-theme')==='dark'; }

  function buildGradients(ctx, data, palette){
    return data.map(function(_,i){
      var c=palette[i%palette.length];
      var g=ctx.createLinearGradient(0,0,0,300);
      g.addColorStop(0,c); g.addColorStop(0.75,c+'cc'); g.addColorStop(1,c+'44');
      return g;
    });
  }
  function buildBorders(data,palette){ return data.map(function(_,i){return palette[i%palette.length];}); }

  function gridColor(){ return isDark()?'rgba(55,65,81,0.6)':'rgba(226,232,240,0.8)'; }
  function tickColorX(tab){ return (tab||currentTab)==='it'?'#f97316':'#0284c7'; }
  function tickColorY(){ return isDark()?'#6b7280':'#94a3b8'; }
  function labelBg(){ return isDark()?'rgba(17,24,39,0.85)':'rgba(255,255,255,0.9)'; }
  function tooltipBg(){ return isDark()?'#111827':'#1e293b'; }

  var currentTab='it';
  var ctx=canvas.getContext('2d');

  var chart=new Chart(canvas,{
    type:'bar',
    data:{
      labels:itLabels,
      datasets:[{
        label:'Units',
        data:itData,
        backgroundColor:buildGradients(ctx,itData,itPalette),
        borderColor:buildBorders(itData,itPalette),
        borderWidth:1.5,
        borderRadius:{topLeft:4,topRight:4},
        borderSkipped:'bottom',
        maxBarThickness:72,
        barPercentage:0.72,
      }]
    },
    options:{
      responsive:true,
      maintainAspectRatio:false,
      layout:{padding:{top:28,right:8,bottom:0,left:0}},
      plugins:{
        legend:{display:false},
        tooltip:{
          backgroundColor:tooltipBg(),
          titleColor:'#f97316',
          bodyColor:'#94a3b8',
          footerColor:'#64748b',
          borderColor:'#f97316',
          borderWidth:1,
          padding:14,
          cornerRadius:6,
          displayColors:false,
          titleFont:{size:12,weight:'800',family:"'Inter',sans-serif"},
          bodyFont:{size:13,weight:'700',family:"'Inter',sans-serif"},
          footerFont:{size:10,family:"'Inter',sans-serif"},
          callbacks:{
            title:function(i){return i[0].label;},
            label:function(c){return '  '+c.raw+' units';},
            footer:function(i){
              var t=i[0].chart.data.datasets[0].data.reduce(function(a,b){return+a+(+b);},0);
              return '  '+(t>0?(i[0].raw/t*100).toFixed(1):0)+'% of total';
            }
          }
        }
      },
      scales:{
        x:{
          grid:{display:false},
          border:{display:false},
          ticks:{
            color:function(){ return tickColorX(currentTab); },
            font:{size:10,family:"'Inter',sans-serif",weight:'700'},
            maxRotation:0
          }
        },
        y:{
          beginAtZero:true,
          border:{display:false},
          grid:{
            color:function(){ return gridColor(); },
            lineWidth:1,
            drawTicks:false
          },
          ticks:{
            color:function(){ return tickColorY(); },
            font:{size:10,family:"'Inter',sans-serif",weight:'600'},
            precision:0,padding:10,
            callback:function(v){return v%1===0?v:'';}
          }
        }
      },
      animation:{
        duration:700,easing:'easeOutQuart',
        onComplete:function(){
          var ch=this,c=ch.ctx;
          ch.data.datasets.forEach(function(ds,di){
            var meta=ch.getDatasetMeta(di);
            meta.data.forEach(function(bar,i){
              var val=ds.data[i];
              if(!val&&val!==0)return;
              c.save();
              c.font="700 11px 'Inter',sans-serif";
              c.textAlign='center'; c.textBaseline='bottom';
              var s=String(val), tw=c.measureText(s).width+12;
              c.fillStyle=labelBg();
              c.strokeStyle=currentTab==='it'?'#f97316':'#0284c7';
              c.lineWidth=1;
              c.beginPath();
              c.roundRect(bar.x-tw/2,bar.y-22,tw,17,3);
              c.fill(); c.stroke();
              c.fillStyle=currentTab==='it'?'#f97316':'#0284c7';
              c.fillText(s,bar.x,bar.y-7);
              c.restore();
            });
          });
        }
      }
    }
  });

  // Update chart when theme toggles
  window._chartThemeUpdate = function(){
    chart.options.plugins.tooltip.backgroundColor = tooltipBg();
    chart.update('none');
  };

  window.switchTab=function(tab){
    currentTab=tab;
    var it=(tab==='it');
    var btnIT=document.getElementById('tabIT'),btnNIT=document.getElementById('tabNIT');
    btnIT.style.background  = it?'#f97316':'transparent';
    btnIT.style.color       = it?'#fff':'var(--muted)';
    btnNIT.style.background = it?'transparent':'#0284c7';
    btnNIT.style.color      = it?'var(--muted)':'#fff';

    var labels=it?itLabels:nitLabels, data=it?itData:nitData, palette=it?itPalette:nitPalette;
    document.getElementById('chartTotalNum').textContent = it?itTotal:nitTotal;
    chart.data.labels=labels;
    chart.data.datasets[0].data=data;
    chart.data.datasets[0].backgroundColor=buildGradients(chart.ctx,data,palette);
    chart.data.datasets[0].borderColor=buildBorders(data,palette);
    chart.data.datasets[0].label=it?'IT Assets':'Non-IT Assets';
    chart.update('active');
  };
})();
</script>
@endpush
