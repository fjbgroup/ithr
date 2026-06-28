@extends('layouts.app')

@section('title', 'Staff Registry Report')

@section('content')
<div class="page-header">
    <div>
        <h2>Staff Registry Report</h2>
        <p class="page-subtitle">Employee headcount and distribution across departments</p>
    </div>
    <div class="header-actions" style="display:flex;gap:.5rem;">
        <a href="{{ route('report.export') }}" class="btn btn-outline">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="8" y1="13" x2="16" y2="13"/><line x1="8" y1="17" x2="16" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            Export Excel
        </a>
        <button class="btn btn-outline" onclick="window.print()">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Print Report
        </button>
    </div>
</div>

<div class="stats-grid" style="margin-bottom:1.5rem;">
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(56,189,248,.12);color:#0284c7;">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $totalStaff }}</div>
            <div class="stat-label">Total Active Staff</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(22,163,74,.1);color:#16a34a;">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $totalDepts }}</div>
            <div class="stat-label">Departments</div>
        </div>
    </div>
    @php
    $coColors = [
        ['bg' => 'rgba(56,189,248,.1)',  'text' => '#0284c7'],
        ['bg' => 'rgba(22,163,74,.1)',   'text' => '#16a34a'],
        ['bg' => 'rgba(245,158,11,.1)',  'text' => '#d97706'],
        ['bg' => 'rgba(99,102,241,.1)',  'text' => '#4f46e5'],
        ['bg' => 'rgba(236,72,153,.1)',  'text' => '#be185d'],
        ['bg' => 'rgba(8,145,178,.1)',   'text' => '#0891b2'],
    ];
    @endphp
    @foreach($allCompanies as $i => $co)
    @php $clr = $coColors[$i % count($coColors)]; $cnt = $companyCounts[$co->code] ?? 0; @endphp
    <div class="stat-card clickable-stat" onclick="openCompanyStaffModal('{{ $co->code }}', '{{ addslashes($co->name) }}')">
        <div class="stat-icon" style="background:{{ $clr['bg'] }};color:{{ $clr['text'] }};">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $cnt }}</div>
            <div class="stat-label">{{ $co->code }} Staff</div>
        </div>
    </div>
    @endforeach
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">
    <div class="card">
        <div class="card-header"><h3 class="card-title">Headcount by Company</h3></div>
        <div style="padding:1.25rem;">
            @php
                $pieTotals = []; $pieColors = [];
                foreach ($allCompanies as $i => $co) {
                    $pieTotals[$co->code] = $companyCounts[$co->code] ?? 0;
                    $pieColors[$co->code] = $coColors[$i % count($coColors)]['text'];
                }
            @endphp
            @if(array_sum($pieTotals) > 0)
            <div style="display:flex;justify-content:center;margin-bottom:1.5rem;">
                <canvas id="companyPieChart" width="200" height="200" style="cursor:pointer;display:block;"></canvas>
            </div>
            @endif
            <div style="display:grid;grid-template-columns:repeat(2, 1fr);gap:1rem;margin-bottom:1.5rem;">
                @foreach($allCompanies as $i => $co)
                @php $clr = $coColors[$i % count($coColors)]; $cnt = $companyCounts[$co->code] ?? 0; @endphp
                <div class="co-stat-box" data-co="{{ $co->code }}" onclick="openCompanyStaffModal('{{ $co->code }}', '{{ addslashes($co->name) }}')" style="background:{{ $clr['bg'] }};border:1px solid {{ $clr['text'] }}33;border-radius:8px;padding:1rem;text-align:center;cursor:pointer;transition:box-shadow .15s;">
                    <div style="font-size:2rem;font-weight:700;color:{{ $clr['text'] }};">{{ $cnt }}</div>
                    <div style="font-size:.8rem;color:var(--muted);font-weight:600;margin-top:.25rem;">{{ $co->code }}</div>
                </div>
                @endforeach
            </div>
            
            <h4 style="font-size:.85rem;font-weight:700;margin-bottom:.75rem;color:var(--text);">Top 8 Positions</h4>
            <div style="display:flex;flex-direction:column;gap:.5rem;">
                @foreach($posRows as $pos)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:.5rem .75rem;background:var(--bg);border-radius:6px;border:1px solid var(--border);">
                    <span style="font-size:.8rem;font-weight:600;color:var(--text-muted);">{{ $pos->position }}</span>
                    <span style="background:var(--navy);color:#fff;font-size:.7rem;font-weight:700;padding:.1rem .5rem;border-radius:10px;">{{ $pos->total }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3 class="card-title">Department Distribution</h3></div>
        <div class="table-responsive">
            <table class="table" style="font-size:.85rem;">
                <thead>
                    <tr>
                        <th>Department</th>
                        <th style="text-align:center;">Co.</th>
                        <th style="text-align:right;">Headcount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($deptRows as $row)
                    <tr>
                        <td><strong>{{ $row->dept }}</strong></td>
                        <td style="text-align:center;"><span class="badge badge-outline" style="font-size:.65rem;">{{ $row->company }}</span></td>
                        <td style="text-align:right;">
                            @if($row->headcount > 0)
                            <button class="btn-link-count" onclick="openDeptStaffModal({{ $row->id }}, '{{ addslashes($row->dept) }}')">
                                {{ $row->headcount }}
                            </button>
                            @else
                            <span style="font-weight:700;color:#94a3b8;padding-right:.75rem;">0</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Company Staff Modal -->
<div class="modal" id="companyStaffModal">
    <div class="modal-box" style="max-width:600px;">
        <div class="modal-header">
            <h3 id="companyStaffModalTitle">Staff List</h3>
            <button class="modal-close" onclick="closeModal()">✕</button>
        </div>
        <div id="companyStaffBody" style="max-height:450px;overflow-y:auto;">
            <div style="padding:2.5rem;text-align:center;color:var(--muted);">
                <div class="spinner" style="margin-bottom:1rem;"></div>
                Loading staff registry…
            </div>
        </div>
    </div>
</div>

<style>
.clickable-stat { cursor: pointer; transition: transform .15s ease, box-shadow .15s ease; }
.clickable-stat:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(0,0,0,.1); }
.co-stat-box:hover { opacity: 0.8; }
.btn-link-count {
    background: none; border: none; color: var(--navy); font-weight: 700; font-family: inherit;
    cursor: pointer; padding: .2rem .75rem; border-radius: 4px; transition: .15s; text-align: right; width: 100%;
}
.btn-link-count:hover { background: rgba(20,43,71,.05); color: var(--primary); }
</style>
@endsection

@section('scripts')
<script>
// Animated company-headcount donut (sweeps in when scrolled into view).
(function () {
    const canvas = document.getElementById('companyPieChart');
    if (!canvas) return;
    const totals  = @json($pieTotals ?? []);
    const colors  = @json($pieColors ?? []);
    const entries = Object.entries(totals).filter(([, v]) => +v > 0).map(([k, v]) => [k, +v]);
    const total   = entries.reduce((s, [, v]) => s + v, 0);
    if (!total) return;

    const ctx = canvas.getContext('2d');
    const cx = 100, cy = 100, r = 88, rHover = 94, rHole = 42;
    let slices = [], hovered = -1;

    let angle = -Math.PI / 2;
    entries.forEach(([co, cnt]) => {
        const sweep = (cnt / total) * 2 * Math.PI;
        slices.push({ co, cnt, color: colors[co] || '#94a3b8', start: angle, end: angle + sweep });
        angle += sweep;
    });

    function draw(prog) {
        if (prog === undefined) prog = 1;
        const sweepMax = -Math.PI / 2 + prog * 2 * Math.PI;
        ctx.clearRect(0, 0, 200, 200);
        slices.forEach((s, i) => {
            if (s.start >= sweepMax) return;
            const end = Math.min(s.end, sweepMax);
            const rad = i === hovered ? rHover : r;
            ctx.beginPath(); ctx.moveTo(cx, cy); ctx.arc(cx, cy, rad, s.start, end); ctx.closePath();
            ctx.fillStyle = s.color; ctx.fill();
            if (i === hovered) { ctx.strokeStyle = '#fff'; ctx.lineWidth = 3; ctx.stroke(); }
        });
        ctx.beginPath(); ctx.arc(cx, cy, rHole, 0, 2 * Math.PI);
        ctx.fillStyle = getComputedStyle(document.documentElement).getPropertyValue('--card-bg') || '#fff';
        ctx.fill();
        ctx.save();
        ctx.globalAlpha = prog;
        ctx.fillStyle = getComputedStyle(document.documentElement).getPropertyValue('--text') || '#111';
        ctx.font = 'bold 26px system-ui,sans-serif'; ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
        ctx.fillText(total, cx, cy - 6);
        ctx.font = '11px system-ui,sans-serif';
        ctx.fillStyle = getComputedStyle(document.documentElement).getPropertyValue('--text-muted') || '#888';
        ctx.fillText('staff', cx, cy + 14);
        ctx.restore();
    }

    let animId = null;
    function animateIn() {
        if (animId) cancelAnimationFrame(animId);
        const dur = 750, t0 = performance.now();
        (function frame(now) {
            const p = Math.min(1, (now - t0) / dur);
            draw(1 - Math.pow(1 - p, 3));
            if (p < 1) animId = requestAnimationFrame(frame);
        })(t0);
    }

    function sliceAt(x, y) {
        const dx = x - cx, dy = y - cy, dist = Math.sqrt(dx * dx + dy * dy);
        if (dist < rHole || dist > rHover) return -1;
        let a = Math.atan2(dy, dx); if (a < -Math.PI / 2) a += 2 * Math.PI;
        for (let i = 0; i < slices.length; i++) {
            let s = slices[i].start, e = slices[i].end;
            if (s < -Math.PI / 2) { s += 2 * Math.PI; e += 2 * Math.PI; }
            if (a >= s && a < e) return i;
        }
        return -1;
    }
    function highlightBoxes(co) {
        document.querySelectorAll('.co-stat-box').forEach(function (b) {
            b.style.boxShadow = (co && b.dataset.co === co) ? '0 0 0 2px ' + (colors[co] || '#94a3b8') : '';
        });
    }

    canvas.addEventListener('mousemove', function (e) {
        const rect = canvas.getBoundingClientRect();
        const x = (e.clientX - rect.left) * (canvas.width / rect.width);
        const y = (e.clientY - rect.top) * (canvas.height / rect.height);
        const idx = sliceAt(x, y);
        if (idx !== hovered) { hovered = idx; draw(); highlightBoxes(idx >= 0 ? slices[idx].co : null); }
    });
    canvas.addEventListener('mouseleave', function () { hovered = -1; draw(); highlightBoxes(null); });

    const reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduce || !window.IntersectionObserver) {
        draw();
    } else {
        draw(0);
        const io = new IntersectionObserver(function (entries) {
            entries.forEach(function (en) { if (en.isIntersecting) animateIn(); });
        }, { threshold: 0.35 });
        io.observe(canvas);
    }
})();

function openCompanyStaffModal(code, name) {
    document.getElementById('companyStaffModalTitle').textContent = 'Active Staff: ' + name;
    document.getElementById('companyStaffBody').innerHTML = 
        '<div style="padding:2.5rem;text-align:center;color:var(--muted);"><div class="spinner" style="margin-bottom:1rem;"></div>Loading staff registry…</div>';
    
    openModal('companyStaffModal');
    
    fetch("{{ url('report/company-staff') }}/" + code)
        .then(r => r.json())
        .then(data => {
            renderStaffTable(data);
        })
        .catch(err => {
            showError();
        });
}

function openDeptStaffModal(id, name) {
    document.getElementById('companyStaffModalTitle').textContent = 'Staff in ' + name;
    document.getElementById('companyStaffBody').innerHTML = 
        '<div style="padding:2.5rem;text-align:center;color:var(--muted);"><div class="spinner" style="margin-bottom:1rem;"></div>Loading staff registry…</div>';
    
    openModal('companyStaffModal');
    
    fetch("{{ url('master-data/staff-list') }}/" + id)
        .then(r => r.json())
        .then(data => {
            renderStaffTable(data);
        })
        .catch(err => {
            showError();
        });
}

function renderStaffTable(data) {
    if (!data.length) {
        document.getElementById('companyStaffBody').innerHTML = 
            '<div style="padding:3rem;text-align:center;color:var(--muted);">No active staff records found.</div>';
        return;
    }
    
    let html = '<table class="table" style="font-size:.85rem;"><thead><tr><th>#</th><th>Name</th><th>Staff No</th><th>Position</th></tr></thead><tbody>';
    data.forEach((s, i) => {
        html += `<tr>
            <td class="td-num">${i + 1}</td>
            <td><strong>${s.name}</strong></td>
            <td class="td-muted"><code>${s.staff_no}</code></td>
            <td class="td-muted">${s.position || '—'}</td>
        </tr>`;
    });
    html += '</tbody></table>';
    document.getElementById('companyStaffBody').innerHTML = html;
}

function showError() {
    document.getElementById('companyStaffBody').innerHTML = 
        '<div style="padding:3rem;text-align:center;color:var(--danger);">Failed to load staff list. Please try again.</div>';
}
</script>
@endsection

