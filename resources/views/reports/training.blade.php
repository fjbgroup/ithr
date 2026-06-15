@extends('layouts.app')

@section('title', 'Training Report')

@section('content')
@php
    $report_view = request('rv', 'monthly');
    $monthNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    
    // Derived data
    $topCourses = $reports->groupBy('course_id')->map(function($group) {
        $first = $group->first();
        return (object)[
            'code' => $first->course_code,
            'title' => $first->course_title,
            'training_type' => $first->resolved_type,
            'cnt' => $group->count()
        ];
    })->sortByDesc('cnt')->take(8);
    $maxCourse = $topCourses->max('cnt') ?: 1;

    $byCourse = $reports->groupBy('course_id')->map(function($group) {
        $first = $group->first();
        return [
            'course_id' => $first->course_id,
            'code'      => $first->course_code,
            'title'     => $first->course_title,
            'date'      => $first->start_date,
            'type'      => $first->resolved_type,
            'attendees' => $group,
        ];
    })->sortByDesc('date');

    $maxMonthVal = 0;
    foreach(range(1,12) as $m) {
        $md = $monthlyData->get($m);
        if($md && $md->total > $maxMonthVal) $maxMonthVal = $md->total;
    }
    $maxMonthVal = $maxMonthVal ?: 1;

    $filterQs = array_filter([
        'year'    => $year_f,
        'month'   => $month_f,
        'type'    => $type_filter,
        'company' => $company_f,
        'dept'    => $dept_id
    ]);
@endphp

<div class="page-header">
    <div>
        <h2>Training Report</h2>
        <p class="page-subtitle">{{ $report_view === 'courses' ? 'Per-course attendee list' : 'Training attendance by month' }} — {{ $year_f }}</p> 
    </div>
    <div class="header-actions" style="display:flex;gap:.5rem;align-items:center;flex-wrap:wrap;">
        <a href="{{ route('training-report', array_merge($filterQs, ['rv' => 'monthly'])) }}" class="btn btn-sm {{ $report_view !== 'courses' ? 'btn-primary' : 'btn-outline' }}">Monthly</a>        
        <a href="{{ route('training-report', array_merge($filterQs, ['rv' => 'courses'])) }}" class="btn btn-sm {{ $report_view === 'courses'  ? 'btn-primary' : 'btn-outline' }}">By Course</a>     
        <a href="{{ route('report.training.export', $filterQs) }}" class="btn btn-outline btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:5px;vertical-align:middle;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            Excel
        </a>
        <button class="btn btn-outline btn-sm" onclick="window.print()">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:5px;vertical-align:middle;"><path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1-2-2h-2"/><path d="M6 14h12v8H6z"/></svg>
            Print
        </button>
    </div>
</div>

{{-- Filter Card --}}
<div class="card" style="margin-bottom:1.25rem;">
    <form method="GET" action="{{ route('training-report') }}" style="display:flex;gap:.75rem;flex-wrap:wrap;align-items:flex-end;padding:1rem 1.25rem;">
        <input type="hidden" name="rv" value="{{ $report_view }}">

        <div class="form-group" style="margin-bottom:0;">
            <label class="filter-label">Year</label>
            <select name="year" class="filter-select" onchange="this.form.submit()">
                @for ($y = date('Y'); $y >= 2022; $y--)
                <option value="{{ $y }}" {{ $year_f == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>

        <div class="form-group" style="margin-bottom:0;">
            <label class="filter-label">Month</label>
            <select name="month" class="filter-select">
                <option value="">All Months</option>
                @for ($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ $month_f == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                @endfor
            </select>
        </div>

        <div class="form-group" style="margin-bottom:0;">
            <label class="filter-label">Training Type</label>
            <select name="type" class="filter-select">
                <option value="">All Types</option>
                <option value="External" {{ $type_filter === 'External' ? 'selected' : '' }}>External</option>
                <option value="Internal" {{ $type_filter === 'Internal' ? 'selected' : '' }}>Internal</option>
            </select>
        </div>

        <div class="form-group" style="margin-bottom:0;">
            <label class="filter-label">Company</label>
            <select name="company" class="filter-select">
                <option value="">All Companies</option>
                @foreach(\App\Models\Company::orderBy('code')->get() as $co)
                <option value="{{ $co->code }}" {{ $company_f === $co->code ? 'selected' : '' }}>{{ $co->code }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group" style="margin-bottom:0;">
            <label class="filter-label">Department</label>
            <select name="dept" class="filter-select" style="min-width:200px;">
                <option value="">All Departments</option>
                @foreach ($departments as $d)
                <option value="{{ $d->id }}" {{ $dept_id == $d->id ? 'selected' : '' }}>
                    [{{ $d->company }}] {{ $d->name }}
                </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary btn-sm" style="height:38px;">Apply</button>
        <a href="{{ route('training-report') }}" class="btn btn-ghost btn-sm" style="height:38px;line-height:38px;">Reset</a>
    </form>
</div>

{{-- KPI Cards --}}
<div class="rpt-kpi-row">
    <div class="rpt-kpi ext-kpi">
        <div class="rpt-kpi-icon" style="background:#fff7ed;color:#c2410c;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
        </div>
        <div>
            <div class="rpt-kpi-val ext-val">{{ number_format($extCourses) }}</div>
            <div class="rpt-kpi-lbl">External Courses</div>
        </div>
    </div>
    <div class="rpt-kpi int-kpi">
        <div class="rpt-kpi-icon" style="background:#f0fdf4;color:#15803d;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        </div>
        <div>
            <div class="rpt-kpi-val int-val">{{ number_format($intCourses) }}</div>
            <div class="rpt-kpi-lbl">Internal Courses</div>
        </div>
    </div>
    <div class="rpt-kpi">
        <div class="rpt-kpi-icon" style="background:#faf5ff;color:#7c3aed;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div>
            <div class="rpt-kpi-val">{{ number_format($unique_staff) }}</div>
            <div class="rpt-kpi-lbl">Unique Staff</div>
        </div>
    </div>
    <div class="rpt-kpi">
        <div class="rpt-kpi-icon" style="background:#f8fafc;color:#475569;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div>
            <div class="rpt-kpi-val">{{ $month_f ? date('M', mktime(0,0,0,$month_f,1)) : 'Full Year' }}</div>
            <div class="rpt-kpi-lbl">{{ $year_f }} {{ $type_filter ? "· $type_filter" : '' }}</div>
        </div>
    </div>
</div>

@if ($report_view !== 'courses')
{{-- Monthly Chart --}}
<div class="card" style="margin-bottom:1.25rem;">
    <div class="card-header" style="align-items:center;">
        <h3 class="card-title">Training by Month — {{ $year_f }}</h3>
        <div style="display:flex;gap:.75rem;align-items:center;font-size:.78rem;">
            <span><span style="display:inline-block;width:10px;height:10px;border-radius:3px;background:#f97316;margin-right:4px;"></span>External</span>
            <span><span style="display:inline-block;width:10px;height:10px;border-radius:3px;background:#22c55e;margin-right:4px;"></span>Internal</span>
        </div>
    </div>
    <div class="monthly-chart-wrap">
        @foreach (range(1,12) as $m)
            @php
                $md = $monthlyData->get($m) ?? (object)['total'=>0,'ext_cnt'=>0,'int_cnt'=>0,'unique_staff'=>0,'unique_courses'=>0];
                $active = ($month_f == $m);
                $extH = $maxMonthVal > 0 ? round((int)$md->ext_cnt / $maxMonthVal * 100) : 0;
                $intH = $maxMonthVal > 0 ? round((int)$md->int_cnt / $maxMonthVal * 100) : 0;
                $href = route('training-report', array_merge($filterQs, ['month' => ($month_f == $m ? '' : $m)]));
            @endphp
        <a href="{{ $href }}" class="month-col {{ $active ? 'month-active' : '' }}" title="{{ date('F Y', mktime(0,0,0,$m,1,$year_f)) }}: {{ $md->total }} total">
            <div class="month-bars">
                <div class="month-bar-ext" style="height:{{ $extH }}%" title="External: {{ $md->ext_cnt }}"></div>
                <div class="month-bar-int" style="height:{{ $intH }}%" title="Internal: {{ $md->int_cnt }}"></div>
            </div>
            <div class="month-total">{{ $md->total ?: '' }}</div>
            <div class="month-name {{ $active ? 'month-name-active' : '' }}">{{ $monthNames[$m-1] }}</div>
            @if ($active)
            <div class="month-detail-tip">
                <div class="mdt-row"><span class="mdt-ext">■</span> Ext: {{ $md->ext_cnt }}</div>
                <div class="mdt-row"><span class="mdt-int">■</span> Int: {{ $md->int_cnt }}</div>
                <div class="mdt-row" style="margin-top:2px;">👤 {{ $md->unique_staff }} staff</div>
            </div>
            @endif
        </a>
        @endforeach
    </div>
</div>

{{-- Two-column: Top Courses + External/Internal breakdown --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">

    {{-- Top Courses --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title">Top Courses</h3></div>
        <div style="padding:.75rem 1.25rem;">
        @if ($topCourses->isEmpty())
            <p style="color:var(--muted);text-align:center;padding:1.5rem 0;font-size:.875rem;">No data for selected filters.</p>
        @else
            @foreach ($topCourses as $tc)
                @php
                    $pct = round($tc->cnt / $maxCourse * 100);
                    $isExt = $tc->training_type === 'External';
                    $barColor = $isExt ? '#f97316' : '#22c55e';
                @endphp
            <div style="margin-bottom:.8rem;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.3rem;">
                    <div style="font-size:.8rem;font-weight:600;flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:200px;" title="{{ $tc->title }}">
                        <code class="training-code" style="font-size:.7rem;">{{ $tc->code }}</code>
                        {{ Str::limit($tc->title, 30) }}
                    </div>
                    <div style="display:flex;gap:.4rem;align-items:center;flex-shrink:0;">
                        <span class="type-badge type-{{ strtolower($tc->training_type) }}">{{ $tc->training_type }}</span>
                        <strong style="font-size:.82rem;">{{ $tc->cnt }}</strong>
                    </div>
                </div>
                <div style="height:6px;background:var(--border);border-radius:99px;overflow:hidden;">
                    <div style="height:100%;width:{{ $pct }}%;background:{{ $barColor }};border-radius:99px;transition:width .5s;"></div>
                </div>
            </div>
            @endforeach
        @endif
        </div>
    </div>

    {{-- External vs Internal Breakdown --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title">External vs Internal</h3></div>
        <div style="padding:1.25rem;">
            @if ($total_attendees > 0)
                @php
                    $ep = round($ext_count / $total_attendees * 100);
                    $ip = 100 - $ep;
                @endphp
            {{-- Donut-style summary --}}
            <div class="type-split-bars">
                <div class="tsplit-item">
                    <div class="tsplit-label">
                        <span class="tsplit-dot ext-dot-sq"></span>External
                        <span class="tsplit-pct">{{ $ep }}%</span>
                    </div>
                    <div class="tsplit-track">
                        <div class="tsplit-fill" style="width:{{ $ep }}%;background:#f97316;"></div>
                    </div>
                    <div class="tsplit-count">{{ number_format($ext_count) }}</div>
                </div>
                <div class="tsplit-item">
                    <div class="tsplit-label">
                        <span class="tsplit-dot int-dot-sq"></span>Internal
                        <span class="tsplit-pct">{{ $ip }}%</span>
                    </div>
                    <div class="tsplit-track">
                        <div class="tsplit-fill" style="width:{{ $ip }}%;background:#22c55e;"></div>
                    </div>
                    <div class="tsplit-count">{{ number_format($int_count) }}</div>
                </div>
            </div>
            {{-- Combined bar --}}
            <div style="margin-top:1.25rem;">
                <div style="font-size:.78rem;color:var(--muted);margin-bottom:.4rem;">Combined proportion</div>
                <div style="height:12px;background:var(--border);border-radius:99px;overflow:hidden;display:flex;">
                    <div style="width:{{ $ep }}%;background:#f97316;"></div>
                    <div style="flex:1;background:#22c55e;"></div>
                </div>
            </div>
            {{-- Monthly quick-stats if filtered --}}
            @if ($month_f)
            <div style="margin-top:1.25rem;padding:1rem;background:#f8fafc;border-radius:8px;font-size:.8rem;">
                <strong>{{ date('F', mktime(0,0,0,$month_f,1)) }} {{ $year_f }}:</strong><br>
                <span style="color:#c2410c;">External: {{ $ext_count }}</span> &nbsp;·&nbsp;
                <span style="color:#15803d;">Internal: {{ $int_count }}</span>
            </div>
            @endif
            @else
            <p style="color:var(--muted);text-align:center;padding:2rem 0;font-size:.875rem;">No data for selected filters.</p>
            @endif
        </div>
    </div>
</div>

{{-- Detail Table --}}
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Training Records</h3>
        <div style="display:flex;gap:.5rem;align-items:center;">
            <span style="font-size:.8rem;color:var(--muted);">{{ $total_attendees }} records</span>
            <div class="app-search" style="flex:none;min-width:0;width:200px;">
                <svg class="app-search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="rptSearch" placeholder="Search…" oninput="filterRptTable(this.value)">
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table" id="rptTable">
            <thead>
                <tr>
                    <th>Date</th><th>Type</th><th>Course</th>
                    <th>Staff</th><th>Department</th><th>Status</th>
                </tr>
            </thead>
            <tbody>
            @if ($reports->isEmpty())
                <tr><td colspan="6" class="empty-state" style="text-align:center;padding:2rem;color:var(--muted);">No records for selected filters.</td></tr>
            @else
                @foreach ($reports as $r)
                <tr class="rpt-row">
                    <td style="white-space:nowrap;">
                        @if ($r->start_date && $r->start_date !== '0000-00-00')
                        <div style="font-weight:600;">{{ date('d M', strtotime($r->start_date)) }}</div>
                        <div style="font-size:.72rem;color:var(--muted);">{{ date('Y', strtotime($r->start_date)) }}</div>
                        @else<span style="color:var(--muted);font-size:.8rem;">—</span>@endif
                    </td>
                    <td>
                        <span class="type-badge type-{{ strtolower($r->resolved_type) }}">{{ $r->resolved_type }}</span>
                    </td>
                    <td style="max-width:260px;">
                        <a href="{{ route('training.index', ['view' => 'list', 'course_id' => $r->course_id]) }}" style="text-decoration:none;"><code class="training-code training-code-link" style="font-size:.7rem;">{{ $r->course_code }}</code></a>
                        <div style="font-size:.82rem;font-weight:500;margin-top:2px;white-space:normal;line-height:1.3;">
                            {{ $r->course_title }}
                        </div>
                    </td>
                    <td>
                        <strong style="font-size:.85rem;">{{ $r->staff_name }}</strong>
                        <div style="font-size:.72rem;"><a href="{{ route('staff.show', $r->staff_id) }}" style="text-decoration:none;color:#6366f1;border-bottom:1px dashed #6366f1;">{{ $r->staff_no }}</a></div>
                    </td>
                    <td>
                        <span class="dept-badge">{{ $r->dept_name ?? '—' }}</span>
                        <div style="font-size:.7rem;color:#94a3b8;margin-top:2px;">{{ $r->company }}</div>
                    </td>
                    <td><span class="status-badge status-{{ strtolower(str_replace(' ','-',$r->status)) }}">{{ $r->status }}</span></td>
                </tr>
                @endforeach
            @endif
            </tbody>
        </table>
    </div>
</div>

<script>
function filterRptTable(q) {
    q = q.toLowerCase();
    document.querySelectorAll('#rptTable tbody .rpt-row').forEach(r => {
        r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}
</script>
@endif

@if ($report_view === 'courses')
{{-- By Course View --}}
<div style="margin-bottom:1rem;">
    <div class="app-search" style="max-width:420px;">
        <svg class="app-search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="courseSearch" placeholder="Search course title or code…" oninput="filterCourses(this.value)">
    </div>
</div>
@if ($byCourse->isEmpty())
<div class="card">
    <div class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
        <p>No training records for the selected filters.</p>
    </div>
</div>
@else
@foreach ($byCourse as $course)
<div class="card course-report-card" style="margin-bottom:1.25rem;"
     data-search="{{ strtolower($course['code'] . ' ' . $course['title']) }}"
     id="crc-{{ $course['course_id'] }}">
    <div class="card-header" style="align-items:center;gap:.75rem;flex-wrap:wrap;">
        <div style="flex:1;min-width:0;">
            <div style="display:flex;gap:.5rem;align-items:center;flex-wrap:wrap;margin-bottom:.3rem;">
                <code class="training-code" style="font-size:.75rem;">{{ $course['code'] }}</code>
                <span class="type-badge type-{{ strtolower($course['type']) }}">{{ $course['type'] }}</span>
                @if ($course['date'] && $course['date'] !== '0000-00-00')
                <span style="font-size:.78rem;color:var(--muted);">{{ date('d M Y', strtotime($course['date'])) }}</span>
                @endif
                <span style="font-size:.78rem;color:var(--muted);">{{ count($course['attendees']) }} attendee{{ count($course['attendees'])!==1?'s':'' }}</span>
            </div>
            <div style="font-size:.92rem;font-weight:600;">{{ $course['title'] }}</div>
        </div>
        <button class="btn btn-outline btn-sm" onclick="printCourse({{ $course['course_id'] }})" style="flex-shrink:0;">Print</button>
    </div>
    <div class="table-wrap">
        <table class="table" style="font-size:.83rem;">
            <thead>
                <tr><th>#</th><th>Staff No</th><th>Name</th><th>Department</th><th>Position</th><th>Status</th></tr>
            </thead>
            <tbody>
            @foreach ($course['attendees'] as $i => $a)
            <tr>
                <td style="color:var(--muted);font-size:.78rem;">{{ $i + 1 }}</td>
                <td><code style="color:#6366f1;font-size:.78rem;">{{ $a->staff_no }}</code></td>
                <td style="font-weight:500;">{{ $a->staff_name }}</td>
                <td><span class="dept-badge" style="font-size:.73rem;">{{ $a->dept_name ?? '—' }}</span></td>
                <td style="color:var(--muted);">{{ $a->position ?? '—' }}</td>
                <td><span class="status-badge status-{{ strtolower(str_replace(' ','-',$a->status)) }}">{{ $a->status }}</span></td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endforeach
@endif
<script>
function filterCourses(q) {
    q = q.toLowerCase().trim();
    document.querySelectorAll('.course-report-card').forEach(c => {
        c.style.display = !q || c.dataset.search.includes(q) ? '' : 'none';
    });
}
function printCourse(cid) {
    document.querySelectorAll('.course-report-card').forEach(c => c.classList.add('no-print'));
    const t = document.getElementById('crc-' + cid);
    if (t) t.classList.remove('no-print');
    window.print();
    document.querySelectorAll('.course-report-card').forEach(c => c.classList.remove('no-print'));
}
</script>
@endif

<style>
/* ── KPI Row ────────────────────────────────────────────────────────── */
.rpt-kpi-row { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:1rem; margin-bottom:1.25rem; }
.rpt-kpi { background:#fff; border:1px solid var(--border); border-radius:12px; padding:1rem 1.25rem; display:flex; align-items:center; gap:.85rem; box-shadow:var(--shadow); }
.rpt-kpi-icon { width:42px; height:42px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.rpt-kpi-val { font-size:1.6rem; font-weight:700; color:var(--text); line-height:1.1; }
.rpt-kpi-lbl { font-size:.7rem; color:var(--muted); font-weight:600; text-transform:uppercase; letter-spacing:.04em; margin-top:.2rem; }
.ext-val { color:#c2410c; }
.int-val { color:#15803d; }

/* ── Monthly Bar Chart ──────────────────────────────────────────────── */
.monthly-chart-wrap {
    display:flex; align-items:flex-end; gap:4px;
    padding:1rem 1.25rem 0;
    height:180px; /* total height of chart area */
    position:relative;
}
.month-col {
    flex:1; display:flex; flex-direction:column; align-items:center;
    cursor:pointer; text-decoration:none; position:relative;
    padding-bottom:2rem; /* space for month label */
}
.month-col:hover .month-bars { opacity:.85; }
.month-active { }
.month-bars {
    width:100%; display:flex; align-items:flex-end; gap:1px;
    height:110px;
    border-bottom:2px solid var(--border);
    padding-bottom:2px;
}
.month-bar-ext {
    flex:1; background:#f97316; border-radius:3px 3px 0 0;
    min-height:0; transition:height .5s;
}
.month-bar-int {
    flex:1; background:#22c55e; border-radius:3px 3px 0 0;
    min-height:0; transition:height .5s;
}
.month-total { font-size:.7rem; font-weight:700; color:var(--muted); margin-top:.2rem; min-height:1em; }
.month-name { font-size:.7rem; color:var(--muted); font-weight:600; margin-top:.15rem; }
.month-name-active { color:var(--navy); font-weight:700; }
.month-active .month-bars { outline:2px solid var(--navy); outline-offset:2px; border-radius:3px; }

/* tooltip on active month */
.month-detail-tip {
    position:absolute; bottom:100%; left:50%; transform:translateX(-50%);
    background:var(--navy); color:#fff; border-radius:8px;
    padding:.45rem .65rem; font-size:.72rem; white-space:nowrap;
    box-shadow:0 4px 12px rgba(0,0,0,.2); z-index:10; margin-bottom:.25rem;
}
.month-detail-tip::after {
    content:''; position:absolute; top:100%; left:50%; transform:translateX(-50%);
    border:5px solid transparent; border-top-color:var(--navy);
}
.mdt-row { line-height:1.6; }
.mdt-ext { color:#fb923c; }
.mdt-int { color:#4ade80; }

/* ── Type Split Bars ────────────────────────────────────────────────── */
.type-split-bars { display:flex; flex-direction:column; gap:.9rem; }
.tsplit-item { display:flex; flex-direction:column; gap:.25rem; }
.tsplit-label { display:flex; align-items:center; gap:.4rem; font-size:.82rem; font-weight:600; }
.tsplit-pct { margin-left:auto; color:var(--muted); font-weight:400; }
.tsplit-track { height:12px; background:var(--border); border-radius:99px; overflow:hidden; }
.tsplit-fill { height:100%; border-radius:99px; transition:width .6s; }
.tsplit-count { font-size:.78rem; color:var(--muted); text-align:right; }
.tsplit-dot { display:inline-block; width:10px; height:10px; border-radius:2px; }
.ext-dot-sq { background:#f97316; }
.int-dot-sq { background:#22c55e; }

/* ── Filter label ───────────────────────────────────────────────────── */
.filter-label { display:block; font-size:.73rem; font-weight:600; color:var(--muted); text-transform:uppercase; letter-spacing:.04em; margin-bottom:.3rem; }

/* ── Type badges (reuse from training.php) ─────────────────────────── */
.type-badge { display:inline-block; padding:.15rem .55rem; border-radius:20px; font-size:.72rem; font-weight:700; }
.type-external { background:#fff7ed; color:#c2410c; border:1px solid #fed7aa; }
.type-internal { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }

@media (max-width: 640px) {
    .rpt-kpi-row { grid-template-columns: repeat(2, 1fr); gap:.65rem; }
    .rpt-kpi { padding:.75rem .9rem; gap:.6rem; }
    .rpt-kpi-icon { width:34px; height:34px; }
    .rpt-kpi-val { font-size:1.3rem; }
    .monthly-chart-wrap { height:130px; padding:.75rem .875rem 0; }
    .month-bars { height:80px; }
}

@media print {
    .filter-bar, .header-actions, .sidebar, .topbar { display:none !important; }
    .main-content { margin:0 !important; padding:0 !important; }
    .card { box-shadow:none !important; border:1px solid #e5e7eb !important; }
    .no-print { display:none !important; }
}
</style>
@endsection
