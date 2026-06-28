@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Welcome Banner -->
<div class="hd-banner">
    <div class="hd-banner-left">
        <div class="hd-greeting">
            @php
                $hour = date('H');
                $greeting = $hour < 12 ? 'Morning' : ($hour < 17 ? 'Afternoon' : 'Evening');
            @endphp
            Good {{ $greeting }}, {{ explode(' ', Auth::user()->name)[0] }}
        </div>
        <div class="hd-date">{{ date('l, d F Y') }} &nbsp;Â·&nbsp; {{ Auth::user()->getRoleLabel() }}</div>
    </div>
    <div class="hd-banner-right">
        <div class="hd-clock" id="hdClock"></div>
    </div>
</div>

@if(Auth::user()->isAdmin() || Auth::user()->isCeo())

<!-- KPI Row -->
<div class="hd-kpi-row">
    <div class="hd-kpi hd-kpi-blue">
        <div class="hd-kpi-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div class="hd-kpi-body">
            <div class="hd-kpi-val">{{ number_format($totalStaff) }}</div>
            <div class="hd-kpi-lbl">Total Staff</div>
        </div>
        <a href="{{ url('/staff') }}" class="hd-kpi-link">View â†’</a>
    </div>
    <div class="hd-kpi hd-kpi-green">
        <div class="hd-kpi-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
        </div>
        <div class="hd-kpi-body">
            <div class="hd-kpi-val">{{ number_format($totalTraining) }}</div>
            <div class="hd-kpi-lbl">Training {{ date('Y') }}</div>
        </div>
        <a href="{{ url('/training') }}" class="hd-kpi-link">View â†’</a>
    </div>
    <div class="hd-kpi hd-kpi-red {{ $pendingReqs > 0 ? 'hd-kpi-alert' : '' }}">
        <div class="hd-kpi-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        </div>
        <div class="hd-kpi-body">
            <div class="hd-kpi-val">{{ number_format($pendingReqs) }}</div>
            <div class="hd-kpi-lbl">Pending Requests</div>
        </div>
        <a href="{{ url('/requests') }}" class="hd-kpi-link">View â†’</a>
    </div>
    <div class="hd-kpi hd-kpi-amber">
        <div class="hd-kpi-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        </div>
        <div class="hd-kpi-body">
            <div class="hd-kpi-val">{{ number_format($totalBookings) }}</div>
            <div class="hd-kpi-lbl">Upcoming Bookings</div>
        </div>
        <a href="{{ url('/rooms') }}" class="hd-kpi-link">Report â†’</a>
    </div>
</div>

<!-- Middle Row: Training Overview + Today Bookings -->
<div class="hd-mid-row">

    <!-- Training Overview Card -->
    <div class="card hd-training-card" style="border-top:4px solid #22c55e;">
        <div class="card-header">
            <h3>Training Overview <span class="hd-year-badge">{{ date('Y') }}</span></h3>
            <a href="{{ url('/training-report') }}" class="btn btn-sm btn-ghost">Full Report â†’</a>
        </div>
        <div class="hd-training-body">

            <!-- Type split â€” two stat chips + combined bar -->
            @php $typeTotal = ($extCnt + $intCnt) ?: 1; @endphp
            <div>
                <div class="hd-to-type-row">
                    <div class="hd-to-chip">
                        <span class="hd-to-chip-dot" style="background:#f97316;"></span>
                        <span class="hd-to-chip-lbl">External</span>
                        <span class="hd-to-chip-val">{{ number_format($extCnt) }}</span>
                        <span class="hd-to-chip-pct">{{ round($extCnt / $typeTotal * 100) }}%</span>
                    </div>
                    <div class="hd-to-chip">
                        <span class="hd-to-chip-dot" style="background:#22c55e;"></span>
                        <span class="hd-to-chip-lbl">Internal</span>
                        <span class="hd-to-chip-val">{{ number_format($intCnt) }}</span>
                        <span class="hd-to-chip-pct">{{ round($intCnt / $typeTotal * 100) }}%</span>
                    </div>
                </div>
                <div class="hd-pill-track" style="height:8px;margin-top:.45rem;">
                    <div style="width:{{ round($extCnt / $typeTotal * 100) }}%;background:#f97316;height:100%;border-radius:99px 0 0 99px;"></div>
                    <div style="flex:1;background:#22c55e;height:100%;border-radius:0 99px 99px 0;"></div>
                </div>
            </div>

            <!-- Top departments â€” donut pie -->
            <div>
                <div class="hd-trend-label">Top Departments</div>
                @php
                    $colors = ['#3b82f6','#8b5cf6','#f59e0b','#10b981','#ef4444'];
                    $deptTotal = $topDepts->sum('cnt') ?: 1;
                @endphp
                @if ($topDepts->isEmpty())
                    <div class="hd-empty" style="padding:.5rem 0;">No training records yet.</div>
                @else
                    @php $ang = -90; $cx = 100; $cy = 100; $r = 85; $ri = 54; @endphp
                    <div class="hd-to-pie-wrap hd-to-pie-side">
                        <svg class="hd-sv-pie hd-sv-pie-lg" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                            @foreach ($topDepts as $i => $d)
                                @php
                                    $c = $colors[$i] ?? '#64748b';
                                    $frac = $d->cnt / $deptTotal;
                                    $sweep = $frac >= 0.999 ? 359.9999 : $frac * 360;
                                    $a1 = deg2rad($ang); $a2 = deg2rad($ang + $sweep);
                                    $x1 = round($cx + $r * cos($a1), 4); $y1 = round($cy + $r * sin($a1), 4);
                                    $x2 = round($cx + $r * cos($a2), 4); $y2 = round($cy + $r * sin($a2), 4);
                                    $ix1 = round($cx + $ri * cos($a2), 4); $iy1 = round($cy + $ri * sin($a2), 4);
                                    $ix2 = round($cx + $ri * cos($a1), 4); $iy2 = round($cy + $ri * sin($a1), 4);
                                    $lg = ($sweep > 180) ? 1 : 0;
                                    $pd = "M{$x1},{$y1} A{$r},{$r} 0 {$lg},1 {$x2},{$y2} L{$ix1},{$iy1} A{$ri},{$ri} 0 {$lg},0 {$ix2},{$iy2}Z";
                                    $ang += $sweep;
                                @endphp
                                <path d="{{ $pd }}" fill="{{ $c }}" class="hd-sv-slice"
                                      data-cnt="{{ $d->cnt }}" data-sub="{{ round($frac * 100) }}%"
                                      data-name="{{ $d->name }}" data-color="{{ $c }}" />
                            @endforeach
                            <text x="100" y="95" class="hd-pie-sv">{{ number_format($deptTotal) }}</text>
                            <text x="100" y="113" class="hd-pie-sl">Total</text>
                        </svg>
                        <div class="hd-dept-right">
                            <div class="hd-dept-side-panel">
                                <span class="hd-dept-sp-dot"></span>
                                <div class="hd-dept-sp-body">
                                    <div class="hd-dept-sp-name"></div>
                                    <div class="hd-dept-sp-meta">
                                        <span class="hd-dept-sp-cnt"></span>
                                        <span class="hd-dept-sp-pct"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="hd-to-pie-legend">
                                @foreach ($topDepts as $i => $d)
                                    @php $c = $colors[$i] ?? '#64748b'; @endphp
                                    <div class="hd-to-pie-leg-row">
                                        <span class="hd-leg-dot" style="background:{{ $c }};flex-shrink:0;"></span>
                                        <span class="hd-to-pie-leg-name" title="{{ $d->name }}">{{ strlen($d->name) > 22 ? substr($d->name,0,22).'â€¦' : $d->name }}</span>
                                        <span class="hd-to-pie-leg-cnt">{{ $d->cnt }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- Right column -->
    <div class="hd-right-col">

        <!-- Today's Room Schedule -->
        <div class="card" style="margin-bottom:1rem;border-top:4px solid #f59e0b;">
            <div class="card-header">
                <h3>Today's Room Schedule</h3>
                <a href="{{ url('/rooms') }}" class="btn btn-sm btn-ghost">Book Room â†’</a>
            </div>
            @if ($todayBookings->isEmpty())
                <div class="hd-empty">No meetings scheduled for today.</div>
            @else
                <div class="hd-booking-list">
                    @foreach ($todayBookings->take(5) as $b)
                        @php
                            $isApp = $b->status === 'Approved';
                            $clr = $isApp ? '#16a34a' : '#d97706';
                            
                            $startTs = strtotime($b->booking_date . ' ' . $b->start_time);
                            $endTs = strtotime($b->booking_date . ' ' . $b->end_time);
                            $nowTs = time();
                            $isLive = ($isApp && $nowTs >= $startTs && $nowTs <= $endTs);
                        @endphp
                        <div class="hd-booking-row" style="border-left-color:{{ $clr }}; position: relative;">
                            <div class="hd-bk-time">
                                {{ substr($b->start_time, 0, 5) }}
                                <span>{{ substr($b->end_time, 0, 5) }}</span>
                            </div>
                            <div class="hd-bk-info">
                                <div class="hd-bk-room">{{ $b->room->name }}</div>
                                <div class="hd-bk-purpose">{{ $b->purpose }}</div>
                            </div>
                            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: .2rem;">
                                <div style="font-size:.68rem; font-weight:700; color:{{ $clr }}; text-transform:uppercase;">
                                    {{ $isApp ? 'OK' : 'Pending' }}
                                </div>
                                @if($isLive)
                                    <div style="display: flex; align-items: center; gap: .25rem; font-size: .6rem; font-weight: 800; color: #ef4444;">
                                        <span style="width: 6px; height: 6px; background: #ef4444; border-radius: 50%; display: inline-block; animation: pulse 1.5s infinite;"></span>
                                        LIVE
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                    @if ($todayBookings->count() > 5)
                        <div style="text-align:center; padding-top:.5rem;">
                            <a href="{{ url('/rooms') }}" style="font-size:.75rem; color:var(--muted); text-decoration:none;">+ {{ $todayBookings->count() - 5 }} more meetings today</a>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Pending Requests -->
        <div class="card" style="border-top:4px solid #ef4444;">
            <div class="card-header">
                <h3>Pending Requests @if ($pendingReqs > 0)<span class="badge-count">{{ $pendingReqs }}</span>@endif</h3>  
                <a href="{{ url('/requests') }}" class="btn btn-sm btn-ghost">View All â†’</a>
            </div>
            @if ($pendingList->isEmpty())
                <div class="hd-empty">No pending requests.</div>
            @else
                <div class="hd-req-list">
                    @foreach ($pendingList as $r)
                    <div class="hd-req-row">
                        <div class="hd-req-avatar">{{ strtoupper(substr($r->requester_name,0,1)) }}</div>
                        <div class="hd-req-info">
                            <div class="hd-req-name">{{ $r->requester_name }}</div>
                            <div class="hd-req-field"><em>{{ $r->record_type }}</em> â€” {{ substr($r->record_reference,0,35) }}</div>
                        </div>
                        <div class="hd-req-age">{{ floor(now()->diffInDays($r->created_at)) }}d</div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>

<!-- Bottom: Recent Training Activity -->
<div class="card" style="margin-top:1rem;border-top:4px solid #22c55e;">
    <div class="card-header">
        <h3>Recent Training Activity</h3>
        <a href="{{ url('/training') }}" class="btn btn-sm btn-ghost">View All â†’</a>
    </div>
    @if ($recentTraining->isEmpty())
        <div class="hd-empty">No training records yet.</div>
    @else
        <div class="table-wrap"><table class="table">
            <thead>
                <tr><th>Staff</th><th>Department</th><th>Course</th><th>Status</th><th>Date</th></tr>
            </thead>
            <tbody>
            @foreach ($recentTraining as $t)
            <tr>
                <td style="font-weight:600;">{{ $t->emp_name }}</td>
                <td style="font-size:.8rem;color:var(--muted);">{{ $t->staff->department->name ?? 'â€”' }}</td>
                <td>
                    <a href="{{ url('/training?view=course&course_id=' . $t->course_id) }}" style="text-decoration:none;"><code class="training-code training-code-link">{{ $t->training_code }}</code></a>
                    <span style="font-size:.82rem;margin-left:.4rem;">{{ substr($t->training_title,0,45) }}â€¦</span>    
                </td>
                <td><span class="status-badge status-{{ strtolower(str_replace(' ','-',$t->status)) }}">{{ $t->status }}</span></td>    
                <td style="font-size:.8rem;color:var(--muted);white-space:nowrap;">{{ $t->created_at->format('d M Y') }}</td>     
            </tr>
            @endforeach
            </tbody>
        </table></div>
    @endif
</div>

@else {{-- STAFF VIEW --}}

<!-- Staff KPI Row -->
<div class="hd-kpi-row">
    <div class="hd-kpi hd-kpi-green">
        <div class="hd-kpi-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <div class="hd-kpi-body">
            <div class="hd-kpi-val">{{ $myStats['total_training'] }}</div>
            <div class="hd-kpi-lbl">Total Trainings</div>
        </div>
        <a href="{{ url('/training') }}" class="hd-kpi-link">View â†’</a>
    </div>
    <div class="hd-kpi hd-kpi-blue">
        <div class="hd-kpi-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
        </div>
        <div class="hd-kpi-body">
            <div class="hd-kpi-val">{{ $myStats['this_year'] }}</div>
            <div class="hd-kpi-lbl">This Year</div>
        </div>
        <a href="{{ url('/training') }}" class="hd-kpi-link">View â†’</a>
    </div>
    <div class="hd-kpi hd-kpi-purple">
        <div class="hd-kpi-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
        </div>
        <div class="hd-kpi-body">
            <div class="hd-kpi-val">{{ $myStats['family_count'] }}</div>
            <div class="hd-kpi-lbl">Family Records</div>
        </div>
        <a href="{{ url('/family') }}" class="hd-kpi-link">View â†’</a>
    </div>
    <div class="hd-kpi hd-kpi-amber">
        <div class="hd-kpi-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        </div>
        <div class="hd-kpi-body">
            <div class="hd-kpi-val">{{ $myStats['upcoming_bookings'] }}</div>
            <div class="hd-kpi-lbl">My Bookings</div>
        </div>
        <a href="{{ url('/rooms') }}" class="hd-kpi-link">Book â†’</a>
    </div>
</div>

<div class="hd-mid-row">

    <!-- My Recent Training -->
    <div class="card hd-training-card">
        <div class="card-header">
            <h3>My Training Activity</h3>
            <a href="{{ url('/training') }}" class="btn btn-sm btn-ghost">View All â†’</a>
        </div>
        @if ($recentTraining->isEmpty())
            <div class="hd-empty">No training records yet.</div>
        @else
            <div class="hd-activity-list">
            @foreach ($recentTraining as $t)
            <div class="hd-activity-row">
                <div class="hd-act-type-dot {{ ($t->resolved_type ?? 'External') === 'Internal' ? 'hd-int' : 'hd-ext' }}"></div>
                <div class="hd-act-body">
                    <div class="hd-act-title">
                        <a href="{{ url('/training?view=course&course_id=' . $t->course_id) }}" style="text-decoration:none;"><code class="training-code training-code-link" style="font-size:.72rem;">{{ $t->training_code }}</code></a>
                        {{ substr($t->training_title,0,55) }}â€¦
                    </div>
                    <div class="hd-act-meta">
                        <span class="hd-type-chip {{ strtolower($t->resolved_type ?? 'external') }}">{{ $t->resolved_type ?? 'External' }}</span>
                        <span>{{ $t->created_at->format('d M Y') }}</span>
                    </div>
                </div>
                <span class="status-badge status-{{ strtolower(str_replace(' ','-',$t->status)) }}">{{ $t->status }}</span>
            </div>
            @endforeach
            </div>
        @endif

        <!-- completion mini-stat -->
        @if ($myStats['total_training'] > 0)
            @php $compPct = round($myStats['completed'] / $myStats['total_training'] * 100); @endphp
            <div class="hd-comp-bar-wrap">
                <div style="display:flex;justify-content:space-between;font-size:.78rem;margin-bottom:.35rem;">
                    <span style="color:var(--muted);">Completion rate</span>
                    <strong>{{ $compPct }}% ({{ $myStats['completed'] }}/{{ $myStats['total_training'] }})</strong>
                </div>
                <div class="hd-type-bar-track">
                    <div class="hd-type-bar-fill" style="width:{{ $compPct }}%;background:#16a34a;"></div>
                </div>
            </div>
        @endif
    </div>

    <!-- Right column -->
    <div class="hd-right-col">
        <!-- Today's Room Schedule -->
        <div class="card" style="margin-bottom:1rem;">
            <div class="card-header">
                <h3>Today's Room Schedule</h3>
                <a href="{{ url('/rooms') }}" class="btn btn-sm btn-ghost">Full View â†’</a>
            </div>
            @if ($todayBookings->isEmpty())
                <div class="hd-empty">No meetings scheduled for today.</div>
            @else
                <div class="hd-booking-list">
                    @foreach ($todayBookings->take(5) as $b)
                        @php
                            $isApp = $b->status === 'Approved';
                            $clr = $isApp ? '#16a34a' : '#d97706';
                            
                            $startTs = strtotime($b->booking_date . ' ' . $b->start_time);
                            $endTs = strtotime($b->booking_date . ' ' . $b->end_time);
                            $nowTs = time();
                            $isLive = ($isApp && $nowTs >= $startTs && $nowTs <= $endTs);
                        @endphp
                        <div class="hd-booking-row" style="border-left-color:{{ $clr }}; position: relative;">
                            <div class="hd-bk-time">
                                {{ substr($b->start_time, 0, 5) }}
                                <span>{{ substr($b->end_time, 0, 5) }}</span>
                            </div>
                            <div class="hd-bk-info">
                                <div class="hd-bk-room">{{ $b->room->name }}</div>
                                <div class="hd-bk-purpose">{{ $b->purpose }}</div>
                            </div>
                            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: .2rem;">
                                <div style="font-size:.68rem; font-weight:700; color:{{ $clr }}; text-transform:uppercase;">
                                    {{ $isApp ? 'OK' : 'Pending' }}
                                </div>
                                @if($isLive)
                                    <div style="display: flex; align-items: center; gap: .25rem; font-size: .6rem; font-weight: 800; color: #ef4444;">
                                        <span style="width: 6px; height: 6px; background: #ef4444; border-radius: 50%; display: inline-block; animation: pulse 1.5s infinite;"></span>
                                        LIVE
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

@endif
@endsection

@section('scripts')
<script>
(function() {
    function tick() {
        var el = document.getElementById('hdClock');
        if (!el) return;
        var now = new Date();
        var h = String(now.getHours()).padStart(2,'0');
        var m = String(now.getMinutes()).padStart(2,'0');
        var s = String(now.getSeconds()).padStart(2,'0');
        el.textContent = h + ':' + m + ':' + s;
    }
    tick();
    setInterval(tick, 1000);
})();

// Interactive SVG donut pies
(function() {
    document.querySelectorAll('.hd-sv-pie').forEach(function(svg) {
        var slices = svg.querySelectorAll('.hd-sv-slice');
        if (!slices.length) return;
        var wrap      = svg.parentElement;
        var sidePanel = wrap ? wrap.querySelector('.hd-dept-side-panel') : null;
        var valEl     = svg.querySelector('.hd-pie-sv');
        var lblEl     = svg.querySelector('.hd-pie-sl');
        var defVal    = valEl ? valEl.textContent : '';
        var defLbl    = lblEl ? lblEl.textContent : '';
        slices.forEach(function(s) {
            s.addEventListener('mouseenter', function() {
                if (sidePanel) {
                    var color = s.getAttribute('data-color') || '#64748b';
                    sidePanel.querySelector('.hd-dept-sp-dot').style.background = color;
                    sidePanel.querySelector('.hd-dept-sp-dot').style.boxShadow = '0 0 0 3px ' + color + '33';
                    sidePanel.querySelector('.hd-dept-sp-name').textContent = s.getAttribute('data-name') || '';
                    sidePanel.querySelector('.hd-dept-sp-cnt').textContent = s.getAttribute('data-cnt') + ' trainings';
                    sidePanel.querySelector('.hd-dept-sp-pct').textContent = s.getAttribute('data-sub');
                    sidePanel.style.borderLeftColor = color;
                    sidePanel.classList.add('active');
                } else {
                    if (valEl) valEl.textContent = s.getAttribute('data-cnt');
                    if (lblEl) lblEl.textContent = s.getAttribute('data-sub');
                }
            });
            s.addEventListener('mouseleave', function() {
                if (sidePanel) {
                    sidePanel.classList.remove('active');
                } else {
                    if (valEl) valEl.textContent = defVal;
                    if (lblEl) lblEl.textContent = defLbl;
                }
            });
        });

        // Entrance animation â€” stagger the slices, then play it whenever the
        // chart scrolls into view (replays each time it is "directed to").
        slices.forEach(function(s, i) { s.style.animationDelay = (0.06 * i) + 's'; });

        if (window.IntersectionObserver) {
            var io = new IntersectionObserver(function(entries) {
                entries.forEach(function(e) {
                    if (e.isIntersecting) {
                        svg.classList.remove('hd-anim-in');
                        void svg.getBoundingClientRect();   // force reflow so it can replay
                        svg.classList.add('hd-anim-in');
                    } else {
                        svg.classList.remove('hd-anim-in');
                    }
                });
            }, { threshold: 0.35 });
            io.observe(svg);
        } else {
            svg.classList.add('hd-anim-in');
        }
    });
})();
</script>
@endsection

@section('styles')
<style>
/* â€”â€” Welcome Banner â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€” */
.hd-banner {
    display: flex; align-items: center; justify-content: space-between;
    background: linear-gradient(135deg, var(--navy) 0%, #254a78 60%, #1a6aa8 100%);
    color: white;
    border-radius: 14px;
    padding: 1.4rem 1.75rem;
    margin-bottom: 1.25rem;
    box-shadow: 0 4px 20px rgba(20,43,71,.25);
}
.hd-greeting { font-size: 1.35rem; font-weight: 700; font-family: 'Inter', sans-serif; }
.hd-date     { font-size: .82rem; color: rgba(255,255,255,.65); margin-top: .2rem; }
.hd-clock    { font-size: 1.6rem; font-weight: 700; font-variant-numeric: tabular-nums; letter-spacing: .05em; color: var(--sky); }

/* â€”â€” KPI Row â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€” */
.hd-kpi-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.25rem; }
.hd-kpi {
    background: var(--surface); border-radius: 12px; padding: 1rem 1.1rem;
    display: flex; align-items: center; gap: .85rem;
    box-shadow: var(--shadow); border-left: 4px solid transparent;
    position: relative; overflow: hidden;
}
.hd-kpi-icon {
    width: 44px; height: 44px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.hd-kpi-body  { flex: 1; min-width: 0; }
.hd-kpi-val   { font-size: 1.65rem; font-weight: 800; line-height: 1.1; color: var(--text); }
.hd-kpi-lbl   { font-size: .72rem; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .04em; margin-top: .15rem; }
.hd-kpi-link  { font-size: .75rem; color: var(--muted); white-space: nowrap; padding: .25rem .5rem; border-radius: 6px; transition: background .15s; }
.hd-kpi-link:hover { background: var(--bg); }

.hd-kpi-blue  { border-left-color: #3b82f6; }
.hd-kpi-blue .hd-kpi-icon  { background: #eff6ff; color: #2563eb; }
.hd-kpi-green { border-left-color: #22c55e; }
.hd-kpi-green .hd-kpi-icon { background: #f0fdf4; color: #16a34a; }
.hd-kpi-amber { border-left-color: #f59e0b; }
.hd-kpi-amber .hd-kpi-icon { background: #fffbeb; color: #d97706; }
.hd-kpi-red   { border-left-color: #ef4444; }
.hd-kpi-red .hd-kpi-icon   { background: #fef2f2; color: #dc2626; }
.hd-kpi-purple { border-left-color: #8b5cf6; }
.hd-kpi-purple .hd-kpi-icon { background: #f5f3ff; color: #7c3aed; }
.hd-kpi-alert { animation: hd-pulse 2s infinite; }
@keyframes hd-pulse { 0%,100%{box-shadow:var(--shadow)} 50%{box-shadow:0 0 0 4px rgba(239,68,68,.15),var(--shadow)} }
@keyframes pulse {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
}

/* â€”â€” Mid Row â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€” */
.hd-mid-row { display: grid; grid-template-columns: 1fr 360px; gap: 1rem; align-items: start; }
.hd-right-col { display: flex; flex-direction: column; gap: 0; }
.hd-training-card .hd-training-body { padding: 1rem 1.25rem; display: flex; flex-direction: column; gap: .9rem; }

/* â€”â€” Compact Training Overview â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€” */
.hd-to-type-row { display: flex; gap: .5rem; }
.hd-to-chip { flex: 1; display: flex; align-items: center; gap: .4rem; background: var(--bg); border-radius: 8px; padding: .45rem .6rem; font-size: .8rem; }
.hd-to-chip-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.hd-to-chip-lbl { color: var(--muted); font-weight: 500; flex: 1; }
.hd-to-chip-val { font-weight: 800; color: var(--text); }
.hd-to-chip-pct { font-size: .7rem; color: var(--muted); background: var(--surface); padding: .1rem .3rem; border-radius: 4px; flex-shrink: 0; }

.hd-to-bars { display: flex; flex-direction: column; gap: .28rem; }
.hd-to-bar-row { display: flex; align-items: center; gap: .5rem; font-size: .78rem; }
.hd-to-bar-mo { width: 36px; color: var(--muted); font-weight: 500; flex-shrink: 0; font-size: .72rem; }
.hd-to-bar-track { flex: 1; height: 7px; background: var(--border); border-radius: 99px; overflow: hidden; }
.hd-to-bar-fill { height: 100%; border-radius: 99px; transition: width .6s; }
.hd-to-bar-cnt { width: 28px; text-align: right; font-weight: 700; color: var(--text); flex-shrink: 0; font-size: .76rem; }

.hd-to-dept-list { display: flex; flex-direction: column; gap: .32rem; }
.hd-to-dept-row { display: flex; align-items: flex-start; gap: .45rem; }
.hd-to-dept-rank { font-size: .7rem; font-weight: 800; width: 14px; flex-shrink: 0; margin-top: .15rem; text-align: center; }
.hd-to-dept-body { flex: 1; min-width: 0; }
.hd-to-dept-name-row { display: flex; align-items: center; gap: .3rem; font-size: .78rem; margin-bottom: .18rem; }
.hd-to-dept-name { font-weight: 600; flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.hd-to-dept-cnt { font-weight: 700; color: var(--text); flex-shrink: 0; margin-left: auto; font-size: .76rem; }
.hd-to-dept-bar-track { height: 5px; background: var(--border); border-radius: 99px; overflow: hidden; }
.hd-to-dept-bar { height: 100%; border-radius: 99px; transition: width .6s; }

/* â€”â€” SVG Donut Pie (shared: trend + departments) â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€” */
.hd-to-pie-wrap { display: flex; flex-direction: column; align-items: center; gap: .65rem; }
.hd-sv-pie { width: 150px; height: 150px; flex-shrink: 0; overflow: visible; }
.hd-sv-pie.hd-sv-pie-lg { width: 200px; height: 200px; }

/* â€”â€” Departments side layout â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€” */
.hd-to-pie-side { flex-direction: row !important; align-items: center; gap: 1.25rem; }
.hd-dept-right { display: flex; flex-direction: column; gap: .55rem; flex: 1; min-width: 0; }
.hd-dept-right .hd-to-pie-legend { flex-wrap: nowrap; flex-direction: column; justify-content: flex-start; gap: .32rem; }
.hd-dept-right .hd-to-pie-leg-row { font-size: .82rem; gap: .45rem; }
.hd-dept-right .hd-to-pie-leg-name { max-width: 160px; font-weight: 600; }
.hd-dept-right .hd-to-pie-leg-cnt { font-size: .82rem; }

/* Side hover panel */
.hd-dept-side-panel { display: none; align-items: center; gap: .65rem; background: var(--bg); border-radius: 10px; padding: .65rem .85rem; border-left: 4px solid #64748b; }
.hd-dept-side-panel.active { display: flex; }
.hd-dept-sp-dot { width: 14px; height: 14px; border-radius: 50%; flex-shrink: 0; transition: box-shadow .15s; }
.hd-dept-sp-body { flex: 1; min-width: 0; }
.hd-dept-sp-name { font-size: .88rem; font-weight: 700; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.hd-dept-sp-meta { display: flex; gap: .5rem; margin-top: .2rem; font-size: .78rem; align-items: center; }
.hd-dept-sp-cnt { font-weight: 700; color: var(--text); }
.hd-dept-sp-pct { color: var(--muted); background: var(--surface); padding: .1rem .35rem; border-radius: 4px; font-weight: 600; }
.hd-sv-slice { cursor: pointer; stroke: white; stroke-width: 2; transition: filter .15s, stroke-width .15s; }
.hd-sv-slice:hover { filter: brightness(1.12); stroke-width: 3; }
.hd-pie-sv { text-anchor: middle; dominant-baseline: middle; font-size: 28px; font-weight: 800; fill: #1e293b; font-family: 'Inter', sans-serif; }
.hd-pie-sl { text-anchor: middle; dominant-baseline: middle; font-size: 13px; font-weight: 600; fill: #94a3b8; font-family: 'Inter', sans-serif; text-transform: uppercase; letter-spacing: 1px; }
.hd-to-pie-legend { display: flex; flex-wrap: wrap; gap: .22rem .65rem; justify-content: center; width: 100%; }
.hd-to-pie-leg-row { display: flex; align-items: center; gap: .35rem; font-size: .74rem; flex: 0 0 auto; }
.hd-to-pie-leg-name { max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-weight: 500; }
.hd-to-pie-leg-cnt { font-weight: 700; color: var(--text); }

/* â€”â€” Donut entrance animation (plays when scrolled/navigated into view) â€”â€” */
.hd-sv-pie .hd-sv-slice {
    opacity: 0;
    transform-box: view-box;
    transform-origin: 100px 100px;
}
.hd-sv-pie.hd-anim-in .hd-sv-slice {
    animation: hd-slice-in .6s cubic-bezier(.22, 1, .36, 1) both;
}
@keyframes hd-slice-in {
    from { opacity: 0; transform: scale(.35) rotate(-110deg); }
    60%  { opacity: 1; }
    to   { opacity: 1; transform: scale(1) rotate(0); }
}
.hd-sv-pie .hd-pie-sv,
.hd-sv-pie .hd-pie-sl { opacity: 0; }
.hd-sv-pie.hd-anim-in .hd-pie-sv,
.hd-sv-pie.hd-anim-in .hd-pie-sl {
    animation: hd-pie-text-in .45s ease .4s both;
}
@keyframes hd-pie-text-in {
    from { opacity: 0; transform: translateY(4px); }
    to   { opacity: 1; transform: translateY(0); }
}
@media (prefers-reduced-motion: reduce) {
    .hd-sv-pie .hd-sv-slice,
    .hd-sv-pie .hd-pie-sv,
    .hd-sv-pie .hd-pie-sl {
        opacity: 1 !important;
        animation: none !important;
        transform: none !important;
    }
}

/* â€”â€” Year badge â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€” */
.hd-year-badge {
    display: inline-block; background: var(--sky-light); color: var(--sky-dark);
    font-size: .72rem; font-weight: 700; padding: .1rem .5rem; border-radius: 20px;
    margin-left: .4rem; vertical-align: middle;
}

/* â€”â€” Type split â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€” */
.hd-type-split { display: flex; flex-direction: column; gap: .6rem; }
.hd-type-item  { display: flex; flex-direction: column; gap: .2rem; }
.hd-type-top   { display: flex; align-items: center; gap: .4rem; font-size: .83rem; }
.hd-type-dot   { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
.hd-type-name  { flex: 1; font-weight: 600; }
.hd-type-cnt   { font-weight: 700; color: var(--text); }
.hd-type-bar-track { height: 7px; background: var(--border); border-radius: 99px; overflow: hidden; }
.hd-type-bar-fill  { height: 100%; border-radius: 99px; transition: width .6s; }
.hd-pill-track { height: 10px; background: var(--border); border-radius: 99px; overflow: hidden; display: flex; }

/* â€”â€” 6-month trend â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€” */
.hd-trend-label { font-size: .72rem; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .05em; margin-bottom: .6rem; }

/* â€”â€” Donut pie chart (shared: trend + departments) â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€” */
.hd-pie-wrap { display: flex; align-items: center; gap: 1.25rem; }
.hd-pie {
    width: 130px; height: 130px; border-radius: 50%; flex-shrink: 0;
    position: relative; box-shadow: inset 0 0 0 1px rgba(0,0,0,.04);
}
.hd-pie-hole {
    position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%);
    width: 78px; height: 78px; background: var(--surface); border-radius: 50%;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
}
.hd-pie-total { font-size: 1.35rem; font-weight: 800; color: var(--text); line-height: 1; }
.hd-pie-cap   { font-size: .62rem; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .05em; margin-top: .15rem; }
.hd-legend { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: .45rem; }
.hd-leg-row { display: flex; align-items: center; gap: .5rem; font-size: .78rem; }
.hd-leg-dot, .hd-dept-dot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
.hd-leg-name { flex: 1; min-width: 0; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: flex; align-items: center; gap: .35rem; }
.hd-dept-co   { font-size: .68rem; color: var(--muted); background: var(--bg); padding: .05rem .3rem; border-radius: 4px; flex-shrink: 0; }
.hd-leg-cnt  { font-size: .78rem; font-weight: 700; color: var(--text); flex-shrink: 0; }
.hd-leg-pct  { font-size: .72rem; font-weight: 600; color: var(--muted); width: 38px; text-align: right; flex-shrink: 0; }

/* â€”â€” Booking list â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€” */
.hd-booking-list { padding: .5rem .75rem .75rem; display: flex; flex-direction: column; gap: .4rem; }
.hd-booking-row {
    display: flex; align-items: center; gap: .65rem;
    padding: .55rem .75rem; border-radius: 8px;
    border-left: 4px solid transparent; background: var(--bg);
}
.hd-bk-time  { font-size: .78rem; font-weight: 700; color: var(--navy); min-width: 42px; line-height: 1.3; }
.hd-bk-time span { display: block; font-weight: 400; color: var(--muted); }
.hd-bk-info  { flex: 1; min-width: 0; }
.hd-bk-room  { font-size: .82rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.hd-bk-purpose { font-size: .74rem; color: var(--muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

/* â€”â€” Requests list â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€” */
.hd-req-list { padding: .5rem .75rem .75rem; display: flex; flex-direction: column; gap: .4rem; }
.hd-req-row  { display: flex; align-items: center; gap: .65rem; padding: .5rem .25rem; border-bottom: 1px solid var(--border); }
.hd-req-row:last-child { border-bottom: none; }
.hd-req-avatar { width: 32px; height: 32px; background: linear-gradient(135deg,var(--navy),var(--navy-mid)); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: .82rem; flex-shrink: 0; }
.hd-req-info { flex: 1; min-width: 0; }
.hd-req-name { font-size: .82rem; font-weight: 600; }
.hd-req-field { font-size: .74rem; color: var(--muted); }
.hd-req-age  { font-size: .72rem; color: var(--muted); background: var(--bg); padding: .15rem .4rem; border-radius: 6px; flex-shrink: 0; }    

/* â€”â€” Activity list (staff) â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€” */
.hd-activity-list { padding: .5rem 1.25rem; display: flex; flex-direction: column; gap: 0; }
.hd-activity-row  { display: flex; align-items: flex-start; gap: .75rem; padding: .7rem 0; border-bottom: 1px solid var(--border); }
.hd-activity-row:last-child { border-bottom: none; }
.hd-act-type-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; margin-top: .35rem; }
.hd-ext { background: #f97316; }
.hd-int { background: #22c55e; }
.hd-act-body  { flex: 1; min-width: 0; }
.hd-act-title { font-size: .83rem; font-weight: 500; line-height: 1.4; }
.hd-act-meta  { display: flex; align-items: center; gap: .5rem; margin-top: .25rem; font-size: .74rem; color: var(--muted); }
.hd-type-chip { padding: .1rem .45rem; border-radius: 20px; font-size: .7rem; font-weight: 700; }
.hd-type-chip.external { background: #fff7ed; color: #c2410c; }
.hd-type-chip.internal { background: #f0fdf4; color: #15803d; }
.hd-comp-bar-wrap { padding: .75rem 1.25rem 1rem; border-top: 1px solid var(--border); }

/* â€”â€” Empty state â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€” */
.hd-empty { padding: 1.5rem; text-align: center; color: var(--muted); font-size: .85rem; }

/* â€”â€” Responsive â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€”â€” */
@media (max-width: 1100px) {
    .hd-kpi-row { grid-template-columns: repeat(2, 1fr); }
    .hd-mid-row { grid-template-columns: 1fr; }
}

@media (max-width: 768px) {
    .hd-banner   { padding: 1.1rem 1.25rem; }
    .hd-greeting { font-size: 1.15rem; }
    .hd-clock    { font-size: 1.3rem; }
}

@media (max-width: 640px) {
    .hd-kpi-row  { grid-template-columns: 1fr 1fr; gap: .65rem; }
    .hd-banner   { flex-direction: column; gap: .3rem; align-items: flex-start; padding: 1rem 1.1rem; }
    .hd-clock    { font-size: 1.1rem; }
    .hd-greeting { font-size: 1rem; }
}

@media (max-width: 480px) {
    .hd-kpi-row      { gap: .5rem; margin-bottom: 1rem; grid-template-columns: 1fr 1fr; }
    .hd-kpi          { padding: .65rem .75rem; gap: .5rem; border-left-width: 3px; }
    .hd-kpi-icon     { width: 32px; height: 32px; border-radius: 7px; }
    .hd-kpi-icon svg { width: 16px; height: 16px; }
    .hd-kpi-val      { font-size: 1.2rem; }
    .hd-kpi-lbl      { font-size: .65rem; }
    .hd-kpi-link     { display: none; }

    .hd-banner       { padding: .75rem .875rem; margin-bottom: 1rem; border-radius: 10px; }
    .hd-banner-right { display: none; }
    .hd-greeting     { font-size: .9rem; }
    .hd-date         { font-size: .7rem; }

    .hd-dept-co      { display: none; }
    .hd-pie          { width: 110px; height: 110px; }
    .hd-pie-hole     { width: 66px; height: 66px; }
    .hd-pie-total    { font-size: 1.1rem; }
    .hd-leg-row      { font-size: .72rem; }

    .hd-training-card .hd-training-body { padding: .75rem .875rem; gap: .875rem; }
    .hd-type-top { font-size: .78rem; }
    .hd-type-cnt { font-size: .78rem; }
    .hd-type-bar-track { height: 5px; }
}

@media (max-width: 360px) {
    .hd-kpi-row { grid-template-columns: 1fr; }
    .hd-greeting { font-size: .85rem; }
    .hd-pie-wrap { flex-direction: column; gap: .75rem; }
    .hd-legend { width: 100%; }
}
</style>
@endsection
