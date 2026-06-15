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
        <div class="hd-date">{{ date('l, d F Y') }} &nbsp;·&nbsp; {{ Auth::user()->getRoleLabel() }}</div>
    </div>
    <div class="hd-banner-right">
        <div class="hd-clock" id="hdClock"></div>
    </div>
</div>

@if(Auth::user()->isAdmin())

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
        <a href="{{ url('/staff') }}" class="hd-kpi-link">View →</a>
    </div>
    <div class="hd-kpi hd-kpi-green">
        <div class="hd-kpi-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
        </div>
        <div class="hd-kpi-body">
            <div class="hd-kpi-val">{{ number_format($totalTraining) }}</div>
            <div class="hd-kpi-lbl">Training {{ date('Y') }}</div>
        </div>
        <a href="{{ url('/training') }}" class="hd-kpi-link">View →</a>
    </div>
    <div class="hd-kpi hd-kpi-red {{ $pendingReqs > 0 ? 'hd-kpi-alert' : '' }}">
        <div class="hd-kpi-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        </div>
        <div class="hd-kpi-body">
            <div class="hd-kpi-val">{{ number_format($pendingReqs) }}</div>
            <div class="hd-kpi-lbl">Pending Requests</div>
        </div>
        <a href="{{ url('/requests') }}" class="hd-kpi-link">View →</a>
    </div>
    <div class="hd-kpi hd-kpi-amber">
        <div class="hd-kpi-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        </div>
        <div class="hd-kpi-body">
            <div class="hd-kpi-val">{{ number_format($totalBookings) }}</div>
            <div class="hd-kpi-lbl">Upcoming Bookings</div>
        </div>
        <a href="{{ url('/rooms') }}" class="hd-kpi-link">Report →</a>
    </div>
</div>

<!-- Middle Row: Training Overview + Today Bookings -->
<div class="hd-mid-row">

    <!-- Training Overview Card -->
    <div class="card hd-training-card" style="border-top:4px solid #22c55e;">
        <div class="card-header">
            <h3>Training Overview <span class="hd-year-badge">{{ date('Y') }}</span></h3>
            <a href="{{ url('/training-report') }}" class="btn btn-sm btn-ghost">Full Report →</a>
        </div>
        <div class="hd-training-body">

            <!-- Type split -->
            @php $typeTotal = ($extCnt + $intCnt) ?: 1; @endphp
            <div class="hd-type-split">
                <div class="hd-type-item">
                    <div class="hd-type-top">
                        <span class="hd-type-dot" style="background:#f97316;"></span>
                        <span class="hd-type-name">External</span>
                        <span class="hd-type-cnt">{{ number_format($extCnt) }}</span>
                    </div>
                    <div class="hd-type-bar-track">
                        <div class="hd-type-bar-fill" style="width:{{ round($extCnt / $typeTotal * 100) }}%;background:#f97316;"></div>      
                    </div>
                </div>
                <div class="hd-type-item">
                    <div class="hd-type-top">
                        <span class="hd-type-dot" style="background:#22c55e;"></span>
                        <span class="hd-type-name">Internal</span>
                        <span class="hd-type-cnt">{{ number_format($intCnt) }}</span>
                    </div>
                    <div class="hd-type-bar-track">
                        <div class="hd-type-bar-fill" style="width:{{ round($intCnt / $typeTotal * 100) }}%;background:#22c55e;"></div>      
                    </div>
                </div>
                <!-- combined pill -->
                <div style="margin-top:.5rem;">
                    <div class="hd-pill-track">
                        <div style="width:{{ round($extCnt / $typeTotal * 100) }}%;background:#f97316;height:100%;border-radius:99px 0 0 99px;"></div>
                        <div style="flex:1;background:#22c55e;height:100%;border-radius:0 99px 99px 0;"></div>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:.72rem;color:var(--muted);margin-top:.25rem;">
                        <span>{{ round($extCnt / $typeTotal * 100) }}% External</span>
                        <span>{{ round($intCnt / $typeTotal * 100) }}% Internal</span>
                    </div>
                </div>
            </div>

            <!-- 6-month trend -->
            <div class="hd-trend">
                <div class="hd-trend-label">6-Month Trend</div>
                <div class="hd-trend-bars">
                    @php $trendMax = $monthTrend->max('cnt') ?: 1; @endphp
                    @foreach ($monthTrend as $m)
                        @php $h = $trendMax > 0 ? max(4, round($m->cnt / $trendMax * 100)) : 4; @endphp
                        <div class="hd-trend-col" title="{{ $m->lbl }}: {{ $m->cnt }}">
                            <div class="hd-trend-cnt">{{ $m->cnt }}</div>
                            <div class="hd-trend-bar" style="height:{{ $h }}px;"></div>
                            <div class="hd-trend-mon">{{ $m->lbl }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Top departments -->
            <div class="hd-dept-section">
                <div class="hd-trend-label">Top Departments</div>
                @php $deptMax = $topDepts->max('cnt') ?: 1; @endphp
                @foreach ($topDepts as $i => $d)
                    @php
                        $pct = round($d->cnt / $deptMax * 100);
                        $colors = ['#3b82f6','#8b5cf6','#f59e0b','#10b981','#ef4444'];
                        $c = $colors[$i] ?? '#64748b';
                    @endphp
                    <div class="hd-dept-row">
                        <div class="hd-dept-name" title="{{ $d->name }}">
                            <span class="hd-dept-dot" style="background:{{ $c }};"></span>
                            {{ strlen($d->name) > 28 ? substr($d->name,0,28).'…' : $d->name }}
                            <span class="hd-dept-co">{{ $d->company }}</span>
                        </div>
                        <div class="hd-dept-bar-wrap">
                            <div class="hd-dept-bar" style="width:{{ $pct }}%;background:{{ $c }};"></div>
                        </div>
                        <span class="hd-dept-cnt">{{ $d->cnt }}</span>
                    </div>
                @endforeach
            </div>

        </div>
    </div>

    <!-- Right column -->
    <div class="hd-right-col">

        <!-- Today's Room Schedule -->
        <div class="card" style="margin-bottom:1rem;border-top:4px solid #f59e0b;">
            <div class="card-header">
                <h3>Today's Room Schedule</h3>
                <a href="{{ url('/rooms') }}" class="btn btn-sm btn-ghost">Book Room →</a>
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
                <a href="{{ url('/requests') }}" class="btn btn-sm btn-ghost">View All →</a>
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
                            <div class="hd-req-field"><em>{{ $r->record_type }}</em> — {{ substr($r->record_reference,0,35) }}</div>
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
        <a href="{{ url('/training') }}" class="btn btn-sm btn-ghost">View All →</a>
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
                <td style="font-size:.8rem;color:var(--muted);">{{ $t->course->department->name ?? '—' }}</td>
                <td>
                    <a href="{{ url('/training?view=course&course_id=' . $t->course_id) }}" style="text-decoration:none;"><code class="training-code training-code-link">{{ $t->training_code }}</code></a>
                    <span style="font-size:.82rem;margin-left:.4rem;">{{ substr($t->training_title,0,45) }}…</span>    
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
        <a href="{{ url('/training') }}" class="hd-kpi-link">View →</a>
    </div>
    <div class="hd-kpi hd-kpi-blue">
        <div class="hd-kpi-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
        </div>
        <div class="hd-kpi-body">
            <div class="hd-kpi-val">{{ $myStats['this_year'] }}</div>
            <div class="hd-kpi-lbl">This Year</div>
        </div>
        <a href="{{ url('/training') }}" class="hd-kpi-link">View →</a>
    </div>
    <div class="hd-kpi hd-kpi-purple">
        <div class="hd-kpi-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
        </div>
        <div class="hd-kpi-body">
            <div class="hd-kpi-val">{{ $myStats['family_count'] }}</div>
            <div class="hd-kpi-lbl">Family Records</div>
        </div>
        <a href="{{ url('/family') }}" class="hd-kpi-link">View →</a>
    </div>
    <div class="hd-kpi hd-kpi-amber">
        <div class="hd-kpi-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        </div>
        <div class="hd-kpi-body">
            <div class="hd-kpi-val">{{ $myStats['upcoming_bookings'] }}</div>
            <div class="hd-kpi-lbl">My Bookings</div>
        </div>
        <a href="{{ url('/rooms') }}" class="hd-kpi-link">Book →</a>
    </div>
</div>

<div class="hd-mid-row">

    <!-- My Recent Training -->
    <div class="card hd-training-card">
        <div class="card-header">
            <h3>My Training Activity</h3>
            <a href="{{ url('/training') }}" class="btn btn-sm btn-ghost">View All →</a>
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
                        {{ substr($t->training_title,0,55) }}…
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
                <a href="{{ url('/rooms') }}" class="btn btn-sm btn-ghost">Full View →</a>
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
</script>
@endsection

@section('styles')
<style>
/* —— Welcome Banner ————————————————————————————————————————————————— */
.hd-banner {
    display: flex; align-items: center; justify-content: space-between;
    background: linear-gradient(135deg, var(--navy) 0%, #254a78 60%, #1a6aa8 100%);
    color: white;
    border-radius: 14px;
    padding: 1.4rem 1.75rem;
    margin-bottom: 1.25rem;
    box-shadow: 0 4px 20px rgba(20,43,71,.25);
}
.hd-greeting { font-size: 1.35rem; font-weight: 700; font-family: 'DM Sans', sans-serif; }
.hd-date     { font-size: .82rem; color: rgba(255,255,255,.65); margin-top: .2rem; }
.hd-clock    { font-size: 1.6rem; font-weight: 700; font-variant-numeric: tabular-nums; letter-spacing: .05em; color: var(--sky); }

/* —— KPI Row ———————————————————————————————————————————————————————— */
.hd-kpi-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.25rem; }
.hd-kpi {
    background: white; border-radius: 12px; padding: 1rem 1.1rem;
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

/* —— Mid Row ———————————————————————————————————————————————————————— */
.hd-mid-row { display: grid; grid-template-columns: 1fr 360px; gap: 1rem; align-items: start; }
.hd-right-col { display: flex; flex-direction: column; gap: 0; }
.hd-training-card .hd-training-body { padding: 1.1rem 1.25rem; display: flex; flex-direction: column; gap: 1.25rem; }

/* —— Year badge ————————————————————————————————————————————————————— */
.hd-year-badge {
    display: inline-block; background: var(--sky-light); color: var(--sky-dark);
    font-size: .72rem; font-weight: 700; padding: .1rem .5rem; border-radius: 20px;
    margin-left: .4rem; vertical-align: middle;
}

/* —— Type split ————————————————————————————————————————————————————— */
.hd-type-split { display: flex; flex-direction: column; gap: .6rem; }
.hd-type-item  { display: flex; flex-direction: column; gap: .2rem; }
.hd-type-top   { display: flex; align-items: center; gap: .4rem; font-size: .83rem; }
.hd-type-dot   { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
.hd-type-name  { flex: 1; font-weight: 600; }
.hd-type-cnt   { font-weight: 700; color: var(--text); }
.hd-type-bar-track { height: 7px; background: var(--border); border-radius: 99px; overflow: hidden; }
.hd-type-bar-fill  { height: 100%; border-radius: 99px; transition: width .6s; }
.hd-pill-track { height: 10px; background: var(--border); border-radius: 99px; overflow: hidden; display: flex; }

/* —— 6-month trend —————————————————————————————————————————————————— */
.hd-trend-label { font-size: .72rem; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .05em; margin-bottom: .6rem; }
.hd-trend-bars { display: flex; align-items: flex-end; gap: .5rem; height: 80px; }
.hd-trend-col  { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 2px; cursor: default; }
.hd-trend-cnt  { font-size: .7rem; font-weight: 700; color: var(--navy); }
.hd-trend-bar  { width: 100%; background: linear-gradient(180deg,#38bdf8,#0284c7); border-radius: 4px 4px 0 0; min-height: 4px; transition: height .5s; }
.hd-trend-mon  { font-size: .68rem; color: var(--muted); font-weight: 600; }

/* —— Dept bars —————————————————————————————————————————————————————— */
.hd-dept-row { display: flex; align-items: center; gap: .5rem; margin-bottom: .5rem; }
.hd-dept-name { font-size: .78rem; font-weight: 500; width: 180px; flex-shrink: 0; display: flex; align-items: center; gap: .35rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.hd-dept-dot  { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.hd-dept-co   { font-size: .68rem; color: var(--muted); background: var(--bg); padding: .05rem .3rem; border-radius: 4px; flex-shrink: 0; }   
.hd-dept-bar-wrap { flex: 1; height: 8px; background: var(--border); border-radius: 99px; overflow: hidden; }
.hd-dept-bar  { height: 100%; border-radius: 99px; transition: width .6s; }
.hd-dept-cnt  { font-size: .78rem; font-weight: 700; color: var(--text); width: 30px; text-align: right; flex-shrink: 0; }

/* —— Booking list ——————————————————————————————————————————————————— */
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

/* —— Requests list —————————————————————————————————————————————————— */
.hd-req-list { padding: .5rem .75rem .75rem; display: flex; flex-direction: column; gap: .4rem; }
.hd-req-row  { display: flex; align-items: center; gap: .65rem; padding: .5rem .25rem; border-bottom: 1px solid var(--border); }
.hd-req-row:last-child { border-bottom: none; }
.hd-req-avatar { width: 32px; height: 32px; background: linear-gradient(135deg,var(--navy),var(--navy-mid)); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: .82rem; flex-shrink: 0; }
.hd-req-info { flex: 1; min-width: 0; }
.hd-req-name { font-size: .82rem; font-weight: 600; }
.hd-req-field { font-size: .74rem; color: var(--muted); }
.hd-req-age  { font-size: .72rem; color: var(--muted); background: var(--bg); padding: .15rem .4rem; border-radius: 6px; flex-shrink: 0; }    

/* —— Activity list (staff) —————————————————————————————————————————— */
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

/* —— Empty state ———————————————————————————————————————————————————— */
.hd-empty { padding: 1.5rem; text-align: center; color: var(--muted); font-size: .85rem; }

/* —— Responsive ————————————————————————————————————————————————————— */
@media (max-width: 1100px) {
    .hd-kpi-row { grid-template-columns: repeat(2, 1fr); }
    .hd-mid-row { grid-template-columns: 1fr; }
    .hd-dept-name { width: 140px; }
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

    .hd-dept-name    { width: auto; flex: 0 1 100px; min-width: 0; font-size: .72rem; }
    .hd-dept-co      { display: none; }
    .hd-dept-bar-wrap { min-width: 40px; height: 6px; }
    .hd-dept-cnt     { font-size: .72rem; width: 24px; }

    .hd-trend-bars   { height: 48px; gap: .35rem; }
    .hd-trend-cnt    { font-size: .6rem; }
    .hd-trend-mon    { font-size: .55rem; }

    .hd-training-card .hd-training-body { padding: .75rem .875rem; gap: .875rem; }
    .hd-type-top { font-size: .78rem; }
    .hd-type-cnt { font-size: .78rem; }
    .hd-type-bar-track { height: 5px; }
}

@media (max-width: 360px) {
    .hd-kpi-row { grid-template-columns: 1fr; }
    .hd-greeting { font-size: .85rem; }
    .hd-dept-name { flex: 0 1 80px; }
}
</style>
@endsection
