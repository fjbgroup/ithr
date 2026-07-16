@extends('layouts.app')

@section('title', 'Master Data')

@section('content')
<div class="page-header">
    <div>
        <h2>Master Data</h2>
        <p class="page-subtitle">Centralized reference data for the entire system</p>
    </div>
    @if(Auth::user()->isAdminIT())
    <button class="btn btn-primary" onclick="openAddModal()">
        + Add {{ $tabLabels[$activeTab] }}
    </button>
    @endif
</div>

<!-- Tab navigation -->
<div class="md-tabs">
    @foreach($tabLabels as $key => $label)
    <a href="{{ route('master-data.index', ['tab' => $key]) }}" class="md-tab {{ $activeTab === $key ? 'active' : '' }}">
        {{ Str::plural($label) }}
        <span class="md-tab-count">{{ $counts[$key] }}</span>
    </a>
    @endforeach
</div>

<!-- Filter bar -->
<form id="md-filter-form" method="GET" action="{{ route('master-data.index') }}" class="md-filter-bar">
    <input type="hidden" name="tab" value="{{ $activeTab }}">
    <div class="app-search md-filter-search">
        <svg class="app-search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" name="q" placeholder="Search..." value="{{ $search }}">
    </div>
    @if(in_array($activeTab, ['departments', 'courses']))
    <div class="md-filter-select">
        <select name="company" class="form-control">
            <option value="">All Companies</option>
            @foreach($allCompanies as $co)
            <option value="{{ $co->code }}" {{ $cFilter === $co->code ? 'selected' : '' }}>
                {{ $co->code }} — {{ $co->name }}
            </option>
            @endforeach
        </select>
    </div>
    @endif
    <div class="md-filter-actions">
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="{{ route('master-data.index', ['tab' => $activeTab]) }}" class="btn btn-outline">Clear</a>
    </div>
</form>

<div id="md-results">
@if($activeTab === 'departments')
    <!-- Pie chart -->
    @php
        $totals = $data['totals'] ?? [];
        $totalAll = array_sum($totals);
        $colorPalette = ['#22c55e','#3b82f6','#f59e0b','#a855f7','#ef4444','#06b6d4','#f97316','#84cc16'];
        $badgePalette = ['admin-it','dept-blue','admin-hr','dept-purple','badge-outline','badge-outline','badge-outline','badge-outline'];
        $companyColors = [];
        $companyBadges = [];
        foreach ($allCompanies as $i => $co) {
            $companyColors[$co->code] = $colorPalette[$i % count($colorPalette)];
            $companyBadges[$co->code] = $badgePalette[$i % count($badgePalette)];
        }
    @endphp
    @if($totalAll > 0)
    <div class="card" style="margin-bottom:1.25rem;padding:1.5rem 2rem;">
        <div style="display:flex;align-items:center;justify-content:center;gap:3rem;flex-wrap:wrap;">
            <canvas id="deptPieChart" width="220" height="220" style="flex-shrink:0;cursor:pointer;display:block;"></canvas>
            <div id="deptPieLegend" style="min-width:180px;">
                @foreach($allCompanies as $co)
                @php $cnt = $totals[$co->code] ?? 0; $color = $companyColors[$co->code]; @endphp
                @if($cnt > 0)
                <div class="dept-pie-row" data-co="{{ $co->code }}" style="display:flex;align-items:center;gap:.75rem;padding:.5rem .65rem;border-radius:8px;margin-bottom:.4rem;cursor:default;transition:background .15s;">
                    <span style="width:14px;height:14px;border-radius:50%;background:{{ $color }};flex-shrink:0;"></span>
                    <span style="font-weight:600;font-size:.9rem;min-width:48px;">{{ $co->code }}</span>
                    <span style="font-size:.88rem;font-weight:700;">{{ $cnt }}</span>
                    <span style="font-size:.8rem;color:var(--text-muted);">({{ round($cnt/$totalAll*100,1) }}%)</span>
                </div>
                @endif
                @endforeach
            </div>
        </div>
    </div>
    <script>
    (function(){
        const totals = @json($totals);
        const colors = @json($companyColors);
        const entries = Object.entries(totals).filter(([,v])=>+v>0).map(([k,v])=>[k,+v]);
        const total = entries.reduce((s,[,v])=>s+v,0);
        const canvas = document.getElementById('deptPieChart');
        const ctx = canvas.getContext('2d');
        const cx = 110, cy = 110, r = 96, rHover = 103;
        let slices = [], hovered = -1;

        function buildSlices() {
            slices = [];
            let angle = -Math.PI / 2;
            entries.forEach(([co, cnt]) => {
                const sweep = (cnt / total) * 2 * Math.PI;
                slices.push({ co, cnt, color: colors[co] || '#94a3b8', start: angle, end: angle + sweep });
                angle += sweep;
            });
        }

        function draw(prog) {
            if (prog === undefined) prog = 1;
            const sweepMax = -Math.PI / 2 + prog * 2 * Math.PI;
            ctx.clearRect(0, 0, 220, 220);
            slices.forEach((s, i) => {
                if (s.start >= sweepMax) return;          // slice not reached by the sweep yet
                const end = Math.min(s.end, sweepMax);     // clamp slice to the animated sweep
                const rad = i === hovered ? rHover : r;
                ctx.beginPath();
                ctx.moveTo(cx, cy);
                ctx.arc(cx, cy, rad, s.start, end);
                ctx.closePath();
                ctx.fillStyle = s.color;
                ctx.fill();
                if (i === hovered) {
                    ctx.strokeStyle = '#fff';
                    ctx.lineWidth = 3;
                    ctx.stroke();
                }
            });
            ctx.beginPath();
            ctx.arc(cx, cy, 44, 0, 2 * Math.PI);
            ctx.fillStyle = getComputedStyle(document.documentElement).getPropertyValue('--card-bg') || '#fff';
            ctx.fill();
            ctx.save();
            ctx.globalAlpha = prog;                        // fade the centre label in
            ctx.fillStyle = getComputedStyle(document.documentElement).getPropertyValue('--text') || '#111';
            ctx.font = 'bold 26px system-ui,sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(total, cx, cy - 6);
            ctx.font = '11px system-ui,sans-serif';
            ctx.fillStyle = getComputedStyle(document.documentElement).getPropertyValue('--text-muted') || '#888';
            ctx.fillText('departments', cx, cy + 14);
            ctx.restore();
        }

        let animId = null;
        function animateIn() {
            if (animId) cancelAnimationFrame(animId);
            const dur = 750, t0 = performance.now();
            (function frame(now) {
                const p = Math.min(1, (now - t0) / dur);
                draw(1 - Math.pow(1 - p, 3));              // easeOutCubic sweep
                if (p < 1) animId = requestAnimationFrame(frame);
            })(t0);
        }

        function getSliceAt(x, y) {
            const dx = x - cx, dy = y - cy;
            const dist = Math.sqrt(dx*dx + dy*dy);
            if (dist < 44 || dist > rHover) return -1;
            let angle = Math.atan2(dy, dx);
            if (angle < -Math.PI/2) angle += 2*Math.PI;
            for (let i = 0; i < slices.length; i++) {
                let s = slices[i].start, e = slices[i].end;
                if (s < -Math.PI/2) { s += 2*Math.PI; e += 2*Math.PI; }
                if (angle >= s && angle < e) return i;
            }
            return -1;
        }

        canvas.addEventListener('mousemove', function(e) {
            const rect = canvas.getBoundingClientRect();
            const x = (e.clientX - rect.left) * (canvas.width / rect.width);
            const y = (e.clientY - rect.top) * (canvas.height / rect.height);
            const idx = getSliceAt(x, y);
            if (idx !== hovered) {
                hovered = idx;
                draw();
                document.querySelectorAll('.dept-pie-row').forEach(r => {
                    r.style.background = idx >= 0 && r.dataset.co === slices[idx]?.co ? 'var(--hover-bg,#f1f5f9)' : '';
                });
            }
        });
        canvas.addEventListener('mouseleave', function() {
            hovered = -1;
            draw();
            document.querySelectorAll('.dept-pie-row').forEach(r => r.style.background = '');
        });

        buildSlices();

        // Play the sweep animation whenever the chart scrolls into view.
        const reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (reduce || !window.IntersectionObserver) {
            draw();
        } else {
            draw(0);
            const io = new IntersectionObserver(function(entries) {
                entries.forEach(function(e) { if (e.isIntersecting) animateIn(); });
            }, { threshold: 0.35 });
            io.observe(canvas);
        }
    })();
    </script>
    @endif

    <div class="card">
        <div class="card-header">
            <h3>Departments <span class="table-count">{{ count($data['rows']) }} shown</span></h3>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Department Name</th>
                        <th>Company</th>
                        <th>Active Staff</th>
                        <th>Created</th>
                        @if(Auth::user()->isAdminIT())<th class="td-actions">Actions</th>@endif
                    </tr>
                </thead>
                <tbody>
                @forelse($data['rows'] as $i => $d)
                <tr>
                    <td class="td-num">{{ $i + 1 }}</td>
                    <td><strong>{{ $d->name }}</strong></td>
                    <td>
                        @php
                            $badgeClass = $companyBadges[$d->company] ?? 'badge-outline';
                        @endphp
                        <span class="badge-role {{ $badgeClass }}">
                            {{ $d->company }}
                        </span>
                    </td>
                    <td>
                        @if($d->staff_count > 0)
                        <button class="btn-link-count" onclick="openStaffListModal({{ $d->id }}, '{{ addslashes($d->name) }}')">
                            {{ $d->staff_count }}
                        </button>
                        @else<span class="td-zero">0</span>@endif
                    </td>
                    <td class="td-muted">{{ $d->created_at->format('d M Y') }}</td>
                    @if(Auth::user()->isAdminIT())
                    <td class="td-actions">
                        <button class="btn btn-outline btn-sm" onclick='openEditModal({id:{{ $d->id }}, name:"{{ addslashes($d->name) }}", company:"{{ $d->company }}", can_delete: {{ $d->staff_count == 0 ? "true" : "false" }}, delete_label: "{{ addslashes($d->name) }}"})'>Edit</button>
                        @if($d->staff_count > 0)<span class="td-in-use">In use</span>@endif
                    </td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="6" class="td-muted" style="text-align:center;padding:2.5rem;">No departments found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

@elseif($activeTab === 'companies')
    <div class="card">
        <div class="card-header">
            <h3>Companies <span class="table-count">{{ count($data['rows']) }} shown</span></h3>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Code</th>
                        <th>Full Name</th>
                        <th>Departments</th>
                        <th>Active Staff</th>
                        @if(Auth::user()->isAdminIT())<th class="td-actions">Actions</th>@endif
                    </tr>
                </thead>
                <tbody>
                @forelse($data['rows'] as $i => $c)
                <tr>
                    <td class="td-num">{{ $i + 1 }}</td>
                    <td><strong>{{ $c->code }}</strong></td>
                    <td>{{ $c->name }}</td>
                    <td>
                        @if($c->dept_count > 0)
                        <button class="btn btn-ghost btn-sm" style="color:var(--primary);text-decoration:underline;padding:2px 6px;" onclick='showCompanyDepts({{ $c->id }}, "{{ addslashes($c->name) }}")'>{{ $c->dept_count }}</button>
                        @else
                        {{ $c->dept_count }}
                        @endif
                    </td>
                    <td>
                        @if($c->staff_count > 0)
                        <button class="btn btn-ghost btn-sm" style="color:var(--primary);text-decoration:underline;padding:2px 6px;" onclick='showCompanyStaff({{ $c->id }}, "{{ addslashes($c->name) }}")'>{{ $c->staff_count }}</button>
                        @else
                        {{ $c->staff_count }}
                        @endif
                    </td>
                    @if(Auth::user()->isAdminIT())
                    <td class="td-actions">
                        <button class="btn btn-outline btn-sm" onclick='openEditModal({id:{{ $c->id }}, code:"{{ $c->code }}", name:"{{ addslashes($c->name) }}", can_delete: {{ ($c->staff_count == 0 && $c->dept_count == 0) ? "true" : "false" }}, delete_label: "{{ $c->code }}"})'>Edit</button>
                        @if($c->staff_count > 0 || $c->dept_count > 0)<span class="td-in-use">In use</span>@endif
                    </td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="6" class="td-muted" style="text-align:center;padding:2.5rem;">No companies found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

@elseif($activeTab === 'courses')
    <div class="card">
        <div class="card-header">
            <h3>Training Courses <span class="table-count">{{ count($data['rows']) }} shown</span></h3>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Code</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Company</th>
                        <th>Date</th>
                        <th>Attendances</th>
                        @if(Auth::user()->isAdminIT())<th class="td-actions">Actions</th>@endif
                    </tr>
                </thead>
                <tbody>
                @forelse($data['rows'] as $i => $c)
                <tr>
                    <td class="td-num">{{ $i + 1 }}</td>
                    <td><strong>{{ $c->code }}</strong></td>
                    <td>{{ $c->title }}</td>
                    <td>
                        <span class="badge-role {{ $c->training_type === 'Internal' ? 'admin-hr' : 'admin-it' }}">
                            {{ $c->training_type ?? 'External' }}
                        </span>
                    </td>
                    <td>{{ $c->company }}</td>
                    <td class="td-muted">{{ $c->start_date ? \Carbon\Carbon::parse($c->start_date)->format('d M Y') : '—' }}</td>
                    <td>
                        @if($c->att_count > 0)
                        <button class="btn btn-ghost btn-sm" style="color:var(--primary);text-decoration:underline;padding:2px 6px;" onclick='showCourseAttendance({{ $c->id }}, "{{ addslashes($c->title) }}")'>{{ $c->att_count }}</button>
                        @else
                        {{ $c->att_count }}
                        @endif
                    </td>
                    @if(Auth::user()->isAdminIT())
                    <td class="td-actions">
                        <button class="btn btn-outline btn-sm" onclick='openEditModal({
                            id:{{ $c->id }},
                            code:"{{ $c->code }}",
                            title:"{{ addslashes($c->title) }}",
                            training_type:"{{ $c->training_type ?? 'External' }}",
                            company:"{{ $c->company }}",
                            start_date:"{{ $c->start_date ?? '' }}",
                            can_delete: {{ $c->att_count == 0 ? "true" : "false" }},
                            delete_label: "{{ $c->code }}"
                        })'>Edit</button>
                        @if($c->att_count > 0)<span class="td-in-use">In use</span>@endif
                    </td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="8" class="td-muted" style="text-align:center;padding:2.5rem;">No training courses found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

@elseif($activeTab === 'positions')
    <div class="card">
        <div class="card-header">
            <h3>Job Positions <span class="table-count">{{ count($data['rows']) }} shown</span></h3>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Position Title</th>
                        <th>Staff Using</th>
                        @if(Auth::user()->isAdminIT())<th class="td-actions">Actions</th>@endif
                    </tr>
                </thead>
                <tbody>
                @forelse($data['rows'] as $i => $p)
                <tr>
                    <td class="td-num">{{ $i + 1 }}</td>
                    <td>{{ $p->title }}</td>
                    <td>
                        @if($p->staff_count > 0)
                        <button class="btn btn-ghost btn-sm" style="color:var(--primary);text-decoration:underline;padding:2px 6px;" onclick='showPositionStaff({{ $p->id }}, "{{ addslashes($p->title) }}")'>{{ $p->staff_count }}</button>
                        @else
                        {{ $p->staff_count }}
                        @endif
                    </td>
                    @if(Auth::user()->isAdminIT())
                    <td class="td-actions">
                        <button class="btn btn-outline btn-sm" onclick='openEditModal({id:{{ $p->id }}, title:"{{ addslashes($p->title) }}", can_delete: {{ $p->staff_count == 0 ? "true" : "false" }}, delete_label: "{{ addslashes($p->title) }}"})'>Edit</button>
                        @if($p->staff_count > 0)<span class="td-in-use">In use</span>@endif
                    </td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="4" class="td-muted" style="text-align:center;padding:2.5rem;">No positions found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

@elseif($activeTab === 'transport')
    <div class="card">
        <div class="card-header">
            <h3>Transport Modes <span class="table-count">{{ count($data['rows']) }} shown</span></h3>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Transport Mode</th>
                        <th>Times Used</th>
                        @if(Auth::user()->isAdminIT())<th class="td-actions">Actions</th>@endif
                    </tr>
                </thead>
                <tbody>
                @forelse($data['rows'] as $i => $t)
                <tr>
                    <td class="td-num">{{ $i + 1 }}</td>
                    <td>{{ $t->name }}</td>
                    <td>
                        @if($t->usage_count > 0)
                        <button class="btn btn-ghost btn-sm" style="color:var(--primary);text-decoration:underline;padding:2px 6px;" onclick='showTransportTravel({{ $t->id }}, "{{ addslashes($t->name) }}")'>{{ $t->usage_count }}</button>
                        @else
                        {{ $t->usage_count }}
                        @endif
                    </td>
                    @if(Auth::user()->isAdminIT())
                    <td class="td-actions">
                        <button class="btn btn-outline btn-sm" onclick='openEditModal({id:{{ $t->id }}, name:"{{ addslashes($t->name) }}", can_delete: {{ $t->usage_count == 0 ? "true" : "false" }}, delete_label: "{{ addslashes($t->name) }}"})'>Edit</button>
                        @if($t->usage_count > 0)<span class="td-in-use">In use</span>@endif
                    </td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="4" class="td-muted" style="text-align:center;padding:2.5rem;">No transport modes found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

@elseif($activeTab === 'settings')
    <div class="card">
        <div class="card-header">
            <h3>System Settings <span class="table-count">{{ count($data['rows']) }} shown</span></h3>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Setting Key</th>
                        <th>Value</th>
                        @if(Auth::user()->isAdminIT())<th class="td-actions">Actions</th>@endif
                    </tr>
                </thead>
                <tbody>
                @forelse($data['rows'] as $i => $s)
                <tr>
                    <td class="td-num">{{ $i + 1 }}</td>
                    <td><code>{{ $s->setting_key }}</code></td>
                    <td>{{ $s->setting_value }}</td>
                    @if(Auth::user()->isAdminIT())
                    <td class="td-actions">
                        <button class="btn btn-outline btn-sm" onclick='openEditModal({id:{{ $s->id }}, setting_key:"{{ $s->setting_key }}", setting_value:"{{ addslashes($s->setting_value) }}", can_delete: true, delete_label: "{{ $s->setting_key }}"})'>Edit</button>
                    </td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="4" class="td-muted" style="text-align:center;padding:2.5rem;">No settings found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif
</div>

<!-- Staff List Modal -->
<div class="modal" id="staffListModal">
    <div class="modal-box" style="max-width:580px;">
        <div class="modal-header">
            <h3 id="staffListModalTitle">Staff in Department</h3>
            <button class="modal-close" onclick="closeStaffListModal()">✕</button>
        </div>
        <div id="staffListBody" style="max-height:420px;overflow-y:auto;">
            <div style="padding:2rem;text-align:center;color:var(--muted);">Loading…</div>
        </div>
    </div>
</div>

<div class="modal" id="companyDeptsModal">
    <div class="modal-box" style="max-width:580px;">
        <div class="modal-header">
            <h3 id="companyDeptsModalTitle">Departments</h3>
            <button class="modal-close" onclick="closeModal()">✕</button>
        </div>
        <div id="companyDeptsBody" style="max-height:420px;overflow-y:auto;">
            <div style="padding:2rem;text-align:center;color:var(--muted);">Loading…</div>
        </div>
    </div>
</div>

<div class="modal" id="companyStaffModal">
    <div class="modal-box" style="max-width:580px;">
        <div class="modal-header">
            <h3 id="companyStaffModalTitle">Active Staff</h3>
            <button class="modal-close" onclick="closeModal()">✕</button>
        </div>
        <div id="companyStaffBody" style="max-height:420px;overflow-y:auto;">
            <div style="padding:2rem;text-align:center;color:var(--muted);">Loading…</div>
        </div>
    </div>
</div>

<div class="modal" id="genericListModal">
    <div class="modal-box" style="max-width:580px;">
        <div class="modal-header">
            <h3 id="genericListModalTitle">Details</h3>
            <button class="modal-close" onclick="closeModal()">✕</button>
        </div>
        <div id="genericListBody" style="max-height:420px;overflow-y:auto;">
            <div style="padding:2rem;text-align:center;color:var(--muted);">Loading…</div>
        </div>
    </div>
</div>

@if(Auth::user()->isAdminIT())
<!-- Main Add/Edit Modal -->
<div class="modal" id="mainModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 id="mainModalTitle">Add Record</h3>
            <button class="modal-close" onclick="closeMainModal()">✕</button>
        </div>
        <form method="POST" action="{{ route('master-data.store') }}">
            @csrf
            <input type="hidden" name="tab" value="{{ $activeTab }}">

            <div class="modal-body">
                @if($activeTab === 'departments')
                <div class="form-group">
                    <label class="form-label">Department Name <span class="req">*</span></label>
                    <input type="text" name="name" id="f_name" class="form-control" required placeholder="e.g. HUMAN RESOURCE/ADMIN">
                </div>
                <div class="form-group">
                    <label class="form-label">Company <span class="req">*</span></label>
                    <select name="company" id="f_company" class="form-control" required>
                        @foreach($allCompanies as $co)
                        <option value="{{ $co->code }}">{{ $co->code }} — {{ $co->name }}</option>
                        @endforeach
                    </select>
                </div>
                @elseif($activeTab === 'companies')
                <div class="form-group">
                    <label class="form-label">Company Code <span class="req">*</span></label>
                    <input type="text" name="code" id="f_code" class="form-control" required placeholder="e.g. FJB" style="text-transform:uppercase;">
                </div>
                <div class="form-group">
                    <label class="form-label">Full Name <span class="req">*</span></label>
                    <input type="text" name="name" id="f_name" class="form-control" required placeholder="e.g. FGV Johor Bulkers Sdn Bhd">    
                </div>
                @elseif($activeTab === 'courses')
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Course Code <span class="req">*</span></label>
                        <input type="text" name="code" id="f_code" class="form-control" required placeholder="e.g. HR-001" style="text-transform:uppercase;">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Training Type</label>
                        <select name="training_type" id="f_training_type" class="form-control">
                            <option value="External">External</option>
                            <option value="Internal">Internal</option>
                        </select>
                    </div>
                    <div class="form-group form-full">
                        <label class="form-label">Course Title <span class="req">*</span></label>
                        <input type="text" name="title" id="f_title" class="form-control" required placeholder="Full course title">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Company</label>
                        <select name="company" id="f_company" class="form-control">
                            @foreach($allCompanies as $co)
                            <option value="{{ $co->code }}">{{ $co->code }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Training Date</label>
                        <input type="date" name="start_date" id="f_start_date" class="form-control">
                    </div>
                </div>
                @elseif($activeTab === 'positions')
                <div class="form-group">
                    <label class="form-label">Position Title <span class="req">*</span></label>
                    <input type="text" name="title" id="f_title" class="form-control" required placeholder="e.g. Senior Executive">
                </div>
                @elseif($activeTab === 'transport')
                <div class="form-group">
                    <label class="form-label">Transport Mode <span class="req">*</span></label>
                    <input type="text" name="name" id="f_name" class="form-control" required placeholder="e.g. Flight">
                </div>
                @elseif($activeTab === 'settings')
                <div class="form-group">
                    <label class="form-label">Setting Key <span class="req">*</span></label>
                    <input type="text" name="setting_key" id="f_setting_key" class="form-control" required placeholder="e.g. app_name">
                </div>
                <div class="form-group">
                    <label class="form-label">Value <span class="req">*</span></label>
                    <textarea name="setting_value" id="f_setting_value" class="form-control" required rows="3"></textarea>
                </div>
                @endif
            </div>

            <div class="modal-footer" style="display:flex; justify-content:space-between; align-items:center;">
                <div id="modalDeleteBtnContainer" style="display:none;">
                    <button type="button" class="btn btn-danger-soft" id="modalDeleteBtn">Delete</button>
                </div>
                <div style="display:flex; gap:0.5rem; margin-left:auto;">
                    <button type="button" class="btn btn-outline" onclick="closeMainModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="formSubmitBtn">Add</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal" id="deleteModal">
    <div class="modal-box modal-sm">
        <div class="modal-header">
            <h3>Confirm Delete</h3>
            <button class="modal-close" onclick="closeDeleteModal()">✕</button>
        </div>
        <form method="POST" action="">
            @csrf
            @method('DELETE')
            <input type="hidden" name="tab" value="{{ $activeTab }}">
            <div class="modal-body">
                <p>Delete <strong id="deleteName"></strong>?</p>
                <p style="color:var(--danger);font-size:.85rem;">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeDeleteModal()">Cancel</button>
                <button type="submit" class="btn btn-danger">Delete</button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script>
const TAB = '{{ $activeTab }}';
const TAB_LABEL = '{{ $tabLabels[$activeTab] }}';

function openAddModal() {
    const form = document.querySelector('#mainModal form');
    form.action = "{{ route('master-data.store') }}";
    
    let methodInput = form.querySelector('input[name="_method"]');
    if (methodInput) methodInput.remove();
    
    document.getElementById('mainModalTitle').textContent = 'Add ' + TAB_LABEL;
    document.getElementById('formSubmitBtn').textContent = 'Add ' + TAB_LABEL;
    
    if (document.getElementById('modalDeleteBtnContainer')) {
        document.getElementById('modalDeleteBtnContainer').style.display = 'none';
    }
    
    clearFormFields();
    openModal('mainModal');
}

function openEditModal(data) {
    const form = document.querySelector('#mainModal form');
    form.action = "{{ url('master-data') }}/" + data.id;
    
    let methodInput = form.querySelector('input[name="_method"]');
    if (!methodInput) {
        methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        form.appendChild(methodInput);
    }
    methodInput.value = 'PUT';
    
    document.getElementById('mainModalTitle').textContent = 'Edit ' + TAB_LABEL;
    document.getElementById('formSubmitBtn').textContent = 'Save Changes';
    
    if (document.getElementById('modalDeleteBtnContainer')) {
        if (data.can_delete) {
            document.getElementById('modalDeleteBtnContainer').style.display = 'block';
            document.getElementById('modalDeleteBtn').onclick = function() {
                closeMainModal();
                confirmDelete(data.id, data.delete_label);
            };
        } else {
            document.getElementById('modalDeleteBtnContainer').style.display = 'none';
        }
    }
    
    if (TAB === 'departments') { 
        setVal('f_name', data.name); 
        setVal('f_company', data.company); 
    }
    else if (TAB === 'companies') { 
        setVal('f_code', data.code); 
        setVal('f_name', data.name); 
    }
    else if (TAB === 'courses') { 
        setVal('f_code', data.code); 
        setVal('f_title', data.title); 
        setVal('f_training_type', data.training_type); 
        setVal('f_company', data.company); 
        setVal('f_start_date', data.start_date); 
    }
    else if (TAB === 'positions') { 
        setVal('f_title', data.title); 
    }
    else if (TAB === 'transport') { 
        setVal('f_name', data.name); 
    }
    else if (TAB === 'settings') { 
        setVal('f_setting_key', data.setting_key); 
        setVal('f_setting_value', data.setting_value); 
    }
    
    openModal('mainModal');
}

function confirmDelete(id, label) {
    const form = document.querySelector('#deleteModal form');
    form.action = "{{ url('master-data') }}/" + id;
    document.getElementById('deleteName').textContent = label;
    openModal('deleteModal');
}

function closeMainModal() { closeModal(); }
function closeDeleteModal() { closeModal(); }

function setVal(id, val) { 
    const el = document.getElementById(id); 
    if (el) el.value = val ?? ''; 
}

function clearFormFields() {
    ['f_name', 'f_code', 'f_title', 'f_start_date', 'f_setting_key', 'f_setting_value'].forEach(id => setVal(id, ''));
    setVal('f_training_type', 'External');
}

// Staff list modal functions
function openStaffListModal(deptId, deptName) {
    document.getElementById('staffListModalTitle').textContent = deptName;
    document.getElementById('staffListBody').innerHTML = '<div style="padding:2rem;text-align:center;color:var(--muted);">Loading…</div>';  
    openModal('staffListModal');
    
    fetch("{{ url('master-data/staff-list') }}/" + deptId)
        .then(r => r.json())
        .then(data => {
            if (!data.length) {
                document.getElementById('staffListBody').innerHTML = '<div style="padding:2rem;text-align:center;color:var(--muted);">No active staff in this department.</div>';
                return;
            }
            const rows = data.map((s, i) => `<tr>
                <td class="td-num">${i + 1}</td>
                <td><strong>${s.name}</strong></td>
                <td class="td-muted">${s.staff_no}</td>
                <td class="td-muted">${s.position || '—'}</td>
            </tr>`).join('');
            document.getElementById('staffListBody').innerHTML =
                `<table class="table"><thead><tr><th>#</th><th>Name</th><th>Staff No.</th><th>Position</th></tr></thead><tbody>${rows}</tbody></table>`;
        })
        .catch(() => {
            document.getElementById('staffListBody').innerHTML = '<div style="padding:2rem;text-align:center;color:var(--danger);">Failed to load staff.</div>';
        });
}

function showCompanyDepts(companyId, companyName) {
    document.getElementById('companyDeptsModalTitle').textContent = 'Departments — ' + companyName;
    document.getElementById('companyDeptsBody').innerHTML = '<div style="padding:2rem;text-align:center;color:var(--muted);">Loading…</div>';
    openModal('companyDeptsModal');
    
    fetch("{{ url('master-data/company-depts') }}/" + companyId)
        .then(r => r.json())
        .then(data => {
            if (!data.length) {
                document.getElementById('companyDeptsBody').innerHTML = '<div style="padding:2rem;text-align:center;color:var(--muted);">No departments found.</div>';
                return;
            }
            const rows = data.map((d, i) => `<tr>
                <td class="td-num">${i + 1}</td>
                <td><strong>${d.name}</strong></td>
            </tr>`).join('');
            document.getElementById('companyDeptsBody').innerHTML =
                `<table class="table"><thead><tr><th>#</th><th>Name</th></tr></thead><tbody>${rows}</tbody></table>`;
        })
        .catch(() => {
            document.getElementById('companyDeptsBody').innerHTML = '<div style="padding:2rem;text-align:center;color:var(--danger);">Failed to load departments.</div>';
        });
}

function showCompanyStaff(companyId, companyName) {
    document.getElementById('companyStaffModalTitle').textContent = 'Active Staff — ' + companyName;
    document.getElementById('companyStaffBody').innerHTML = '<div style="padding:2rem;text-align:center;color:var(--muted);">Loading…</div>';
    openModal('companyStaffModal');
    
    fetch("{{ url('master-data/company-staff') }}/" + companyId)
        .then(r => r.json())
        .then(data => {
            if (!data.length) {
                document.getElementById('companyStaffBody').innerHTML = '<div style="padding:2rem;text-align:center;color:var(--muted);">No active staff found.</div>';
                return;
            }
            const rows = data.map((s, i) => `<tr>
                <td class="td-num">${i + 1}</td>
                <td><strong>${s.name}</strong></td>
                <td class="td-muted">${s.staff_no || '—'}</td>
                <td class="td-muted">${s.position || '—'}</td>
            </tr>`).join('');
            document.getElementById('companyStaffBody').innerHTML =
                `<table class="table"><thead><tr><th>#</th><th>Name</th><th>Staff No.</th><th>Position</th></tr></thead><tbody>${rows}</tbody></table>`;
        })
        .catch(() => {
            document.getElementById('companyStaffBody').innerHTML = '<div style="padding:2rem;text-align:center;color:var(--danger);">Failed to load staff.</div>';
        });
}

function showCourseAttendance(courseId, courseTitle) {
    document.getElementById('genericListModalTitle').textContent = 'Attendances — ' + courseTitle;
    document.getElementById('genericListBody').innerHTML = '<div style="padding:2rem;text-align:center;color:var(--muted);">Loading…</div>';
    openModal('genericListModal');
    
    fetch("{{ url('master-data/course-attendance') }}/" + courseId)
        .then(r => r.json())
        .then(data => {
            if (!data.length) {
                document.getElementById('genericListBody').innerHTML = '<div style="padding:2rem;text-align:center;color:var(--muted);">No attendances found.</div>';
                return;
            }
            const rows = data.map((d, i) => `<tr>
                <td class="td-num">${i + 1}</td>
                <td><strong>${d.name}</strong></td>
                <td class="td-muted">${d.staff_no}</td>
                <td>${d.status}</td>
            </tr>`).join('');
            document.getElementById('genericListBody').innerHTML =
                `<table class="table"><thead><tr><th>#</th><th>Name</th><th>Staff No.</th><th>Status</th></tr></thead><tbody>${rows}</tbody></table>`;
        })
        .catch(() => {
            document.getElementById('genericListBody').innerHTML = '<div style="padding:2rem;text-align:center;color:var(--danger);">Failed to load attendances.</div>';
        });
}

function showPositionStaff(posId, posTitle) {
    document.getElementById('genericListModalTitle').textContent = 'Staff in Position — ' + posTitle;
    document.getElementById('genericListBody').innerHTML = '<div style="padding:2rem;text-align:center;color:var(--muted);">Loading…</div>';
    openModal('genericListModal');
    
    fetch("{{ url('master-data/position-staff') }}/" + posId)
        .then(r => r.json())
        .then(data => {
            if (!data.length) {
                document.getElementById('genericListBody').innerHTML = '<div style="padding:2rem;text-align:center;color:var(--muted);">No active staff found.</div>';
                return;
            }
            const rows = data.map((d, i) => `<tr>
                <td class="td-num">${i + 1}</td>
                <td><strong>${d.name}</strong></td>
                <td class="td-muted">${d.staff_no || '—'}</td>
                <td class="td-muted">${d.department}</td>
            </tr>`).join('');
            document.getElementById('genericListBody').innerHTML =
                `<table class="table"><thead><tr><th>#</th><th>Name</th><th>Staff No.</th><th>Department</th></tr></thead><tbody>${rows}</tbody></table>`;
        })
        .catch(() => {
            document.getElementById('genericListBody').innerHTML = '<div style="padding:2rem;text-align:center;color:var(--danger);">Failed to load staff.</div>';
        });
}

function showTransportTravel(transportId, transportName) {
    document.getElementById('genericListModalTitle').textContent = 'Travels using — ' + transportName;
    document.getElementById('genericListBody').innerHTML = '<div style="padding:2rem;text-align:center;color:var(--muted);">Loading…</div>';
    openModal('genericListModal');
    
    fetch("{{ url('master-data/transport-travel') }}/" + transportId)
        .then(r => r.json())
        .then(data => {
            if (!data.length) {
                document.getElementById('genericListBody').innerHTML = '<div style="padding:2rem;text-align:center;color:var(--muted);">No travel requests found.</div>';
                return;
            }
            const rows = data.map((d, i) => `<tr>
                <td class="td-num">${i + 1}</td>
                <td><strong>${d.ref_no}</strong></td>
                <td>${d.staff_name}</td>
                <td class="td-muted">${d.destination}</td>
            </tr>`).join('');
            document.getElementById('genericListBody').innerHTML =
                `<table class="table"><thead><tr><th>#</th><th>Ref No.</th><th>Staff</th><th>Destination</th></tr></thead><tbody>${rows}</tbody></table>`;
        })
        .catch(() => {
            document.getElementById('genericListBody').innerHTML = '<div style="padding:2rem;text-align:center;color:var(--danger);">Failed to load travel requests.</div>';
        });
}

function closeStaffListModal() {
    closeModal();
}


liveSearch(document.getElementById('md-filter-form'), 'md-results');
</script>
@endsection
