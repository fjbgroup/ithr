@extends('layouts.app')

@section('title', 'Training Records')

@section('content')
<div class="tr-container">

{{-- ── STATS BANNER ───────────────────────────────────────── --}}
@php
    if ($isAdmin) {
        $extCourses = $courseTypeCounts->ext_courses ?? 0;
        $intCourses = $courseTypeCounts->int_courses ?? 0;
    } else {
        $extCourses = $typeTotals->ext_total ?? 0;
        $intCourses = $typeTotals->int_total ?? 0;
    }
    $totalC      = max(1, $extCourses + $intCourses);
    $extP        = round(($extCourses / $totalC) * 100);
    $intP        = 100 - $extP;
    $uniqueStaff = $typeTotals->unique_staff ?? 0;
@endphp

<div class="tr-stats-banner">
    <div class="tr-stats-grid">
        <div class="tr-stat-item border-r">
            <div class="tr-stat-icon-wrap bg-orange">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#f97316" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
            </div>
            <div>
                <div class="tr-stat-label color-orange">{{ $isAdmin ? 'External' : 'My External' }}</div>
                <div class="tr-stat-value-wrap">
                    <span class="tr-stat-value">{{ $extCourses }}</span>
                    <span class="tr-stat-unit">Courses</span>
                </div>
            </div>
        </div>

        <div class="tr-stat-item border-r px-lg">
            <div class="tr-stat-icon-wrap bg-green">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </div>
            <div>
                <div class="tr-stat-label color-green">{{ $isAdmin ? 'Internal' : 'My Internal' }}</div>
                <div class="tr-stat-value-wrap">
                    <span class="tr-stat-value">{{ $intCourses }}</span>
                    <span class="tr-stat-unit">Courses</span>
                </div>
            </div>
        </div>

        <div class="tr-stat-item border-r px-lg">
            <div class="tr-stat-icon-wrap bg-indigo">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            @if($isAdmin)
            <div>
                <div class="tr-stat-label color-indigo">Staff Joined</div>
                <div class="tr-stat-value">{{ $uniqueStaff }}</div>
            </div>
            @else
            <div>
                <div class="tr-stat-label color-indigo">My Records</div>
                <div class="tr-stat-value">{{ $typeTotals->grand_total ?? 0 }}</div>
            </div>
            @endif
        </div>

        <div class="tr-stat-item border-r px-lg">
            <div class="tr-stat-icon-wrap bg-slate">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2.2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            </div>
            <div>
                <div class="tr-stat-label color-slate">{{ $isAdmin ? 'Total Courses' : 'My Courses' }}</div>
                <div class="tr-stat-value">{{ $extCourses + $intCourses }}</div>
            </div>
        </div>

        <div class="tr-stat-progress-wrap">
            <div class="tr-progress-header">
                <span class="tr-progress-label">Distribution</span>
                <span class="tr-progress-pct">{{ $extP }}% / {{ $intP }}%</span>
            </div>
            <div class="tr-progress-track">
                <div style="display:flex;height:100%;">
                    <div style="width:{{ $extP }}%;background:linear-gradient(90deg,#fb923c,#f97316);border-radius:99px 0 0 99px;transition:width .5s;"></div>
                    <div style="width:{{ $intP }}%;background:linear-gradient(90deg,#4ade80,#22c55e);border-radius:0 99px 99px 0;transition:width .5s;"></div>
                </div>
            </div>
            <div class="tr-progress-legend">
                <span class="tr-legend-item"><span class="tr-dot bg-orange"></span>Ext {{ $extP }}%</span>
                <span class="tr-legend-item"><span class="tr-dot bg-green"></span>Int {{ $intP }}%</span>
            </div>
        </div>
    </div>
</div>

{{-- ── PAGE HEADER ─────────────────────────────────────────── --}}
<div class="page-header" style="margin-bottom:1rem;">
    <p class="page-subtitle" style="margin:0;">{{ $isAdmin ? 'Training attendance by department and course' : 'Your personal training attendance records' }}</p>
    @canwrite
    <div class="header-actions">
        <button onclick="openQrScanner()" class="btn btn-outline btn-sm" style="gap:.35rem;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><path d="M14 14h.01M14 17h3M17 14v3M20 20h.01"/></svg>
            Scan QR
        </button>
        @if($isAdmin)
        <a href="{{ route('training.import-page') }}" class="btn btn-outline btn-sm">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            Import CSV
        </a>
        <button onclick="openModal('course-modal')" class="btn btn-outline btn-sm">+ Course</button>
        <button onclick="openModal('attendance-modal')" class="btn btn-primary btn-sm">+ Attendance</button>
        @endif
    </div>
    @endcanwrite
</div>

{{-- ── VIEW TABS ────────────────────────────────────────────── --}}
@if($isAdmin)
<div class="tr-tabs">
    <a href="?view=by_dept" class="tr-tab {{ $view === 'by_dept' ? 'active' : '' }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
        By Department
    </a>
    <a href="?view=list" class="tr-tab {{ $view === 'list' ? 'active' : '' }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"></path></svg>
        List View
    </a>
    <a href="?view=courses" class="tr-tab {{ $view === 'courses' ? 'active' : '' }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
        Courses
    </a>
</div>
@endif

{{-- ── TYPE FILTER (list + courses only) ───────────────────── --}}
@if($view !== 'by_dept')
<div style="margin-bottom:.75rem;">
    <div class="tr-type-pills">
        <a href="?view={{ $view }}&type=&dept={{ $dept_filter }}&course_id={{ $course_id }}&q={{ $search }}&year={{ $year_filter }}"
           class="tr-type-pill {{ !$type_filter ? 'active' : '' }}">All</a>
        <a href="?view={{ $view }}&type=External&dept={{ $dept_filter }}&course_id={{ $course_id }}&q={{ $search }}&year={{ $year_filter }}"
           class="tr-type-pill {{ $type_filter === 'External' ? 'active tr-pill-ext' : '' }}">External</a>
        <a href="?view={{ $view }}&type=Internal&dept={{ $dept_filter }}&course_id={{ $course_id }}&q={{ $search }}&year={{ $year_filter }}"
           class="tr-type-pill {{ $type_filter === 'Internal' ? 'active tr-pill-int' : '' }}">Internal</a>
    </div>
</div>
@endif

{{-- ══════════════════════════════════════════════════════════
     BY DEPARTMENT VIEW
══════════════════════════════════════════════════════════ --}}
@if($view === 'by_dept')

<div class="tr-search-bar">
    <div class="app-search" style="flex:1;">
        <svg class="app-search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="dept-search" placeholder="Search department…" oninput="filterDeptCards()">
    </div>
    <span class="tr-search-count">{{ count($dept_summaries) }} departments</span>
</div>

<div class="tr-dept-grid">
    @foreach($dept_summaries as $dept)
    @php
        $isUnassigned = $dept->id === null;
        $deptLabel    = $isUnassigned ? 'Unassigned / No Department' : $dept->name;
        $companyLabel = $dept->company ?? '';
        $tot = max(1, $dept->training_count);
        $eP  = round(($dept->ext_count / $tot) * 100);
        $iP  = 100 - $eP;
        $accentColor = $isUnassigned ? '#94a3b8' : ($dept->ext_count >= $dept->int_count ? '#f97316' : '#22c55e');
        $viewHref = $isUnassigned
            ? '?view=list&no_dept=1&year=' . $year_filter
            : '?view=list&dept=' . $dept->id . '&year=' . $year_filter;
    @endphp
    <div data-name="{{ strtolower($deptLabel) }}" data-company="{{ strtolower($companyLabel) }}"
         class="tr-dept-card{{ $isUnassigned ? ' tr-dept-card-unassigned' : '' }}" style="border-left-color:{{ $accentColor }};">
        <div class="tr-dept-card-inner">
            <div class="tr-dept-header">
                <div style="flex:1;min-width:0;">
                    @if($companyLabel)
                    <span class="tr-dept-co-badge">{{ $companyLabel }}</span>
                    @endif
                    <h3 class="tr-dept-title{{ $isUnassigned ? ' tr-dept-title-muted' : '' }}">{{ $deptLabel }}</h3>
                </div>
                <a href="{{ $viewHref }}" class="btn btn-ghost btn-sm" style="white-space:nowrap;">
                    View <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            </div>
            <div class="tr-dept-stats">
                <div class="tr-dept-stat">
                    <div class="tr-dstat-val">{{ $dept->staff_count }}</div>
                    <div class="tr-dstat-lbl">Staff</div>
                </div>
                <div class="tr-dept-stat">
                    <div class="tr-dstat-val">{{ $dept->training_count }}</div>
                    <div class="tr-dstat-lbl">Total</div>
                </div>
                <div class="tr-dept-stat">
                    <div class="tr-dstat-val color-orange">{{ $dept->ext_count ?? 0 }}</div>
                    <div class="tr-dstat-lbl">Ext</div>
                </div>
                <div class="tr-dept-stat">
                    <div class="tr-dstat-val color-green">{{ $dept->int_count ?? 0 }}</div>
                    <div class="tr-dstat-lbl">Int</div>
                </div>
            </div>
            <div class="tr-dept-progress">
                <span class="tr-dp-pct color-orange">{{ $eP }}%</span>
                <div class="tr-dp-track">
                    <div style="display:flex;height:100%;">
                        <div style="width:{{ $eP }}%;background:linear-gradient(90deg,#fb923c,#f97316);"></div>
                        <div style="width:{{ $iP }}%;background:linear-gradient(90deg,#4ade80,#22c55e);"></div>
                    </div>
                </div>
                <span class="tr-dp-pct color-green">{{ $iP }}%</span>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ══════════════════════════════════════════════════════════
     LIST VIEW
══════════════════════════════════════════════════════════ --}}
@elseif($view === 'list')

<form method="GET" class="tr-filter-bar">
    <input type="hidden" name="view" value="list">
    <input type="hidden" name="type" value="{{ $type_filter }}">
    <div class="app-search" style="flex:1;min-width:160px;">
        <svg class="app-search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" name="q" value="{{ $search }}" placeholder="Search name or course…">
    </div>
    @if($isAdmin)
    <select name="dept" class="tr-select">
        <option value="">All Departments</option>
        @foreach($allDepartments as $d)
        <option value="{{ $d->id }}" {{ (string)$dept_filter === (string)$d->id ? 'selected' : '' }}>{{ $d->name }}</option>
        @endforeach
    </select>
    <select name="course_id" class="tr-select">
        <option value="">All Courses</option>
        @foreach($allCourses as $c)
        <option value="{{ $c->id }}" {{ (string)$course_id === (string)$c->id ? 'selected' : '' }}>[{{ $c->code }}] {{ $c->title }}</option>
        @endforeach
    </select>
    @endif
    <select name="year" class="tr-select">
        <option value="">All Years</option>
        @foreach($years as $y)
        <option value="{{ $y }}" {{ (string)$year_filter === (string)$y ? 'selected' : '' }}>{{ $y }}</option>
        @endforeach
    </select>
    <div class="tr-filter-actions">
        <button type="submit" class="btn btn-primary btn-sm">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            Search
        </button>
        @if($search || $dept_filter || $no_dept || $course_id || $year_filter)
        <a href="?view=list&type={{ $type_filter }}" class="tr-clear-btn">Clear</a>
        @endif
    </div>
    <span class="tr-search-count hide-on-mobile">{{ count($attendances) }} records</span>
</form>

<div class="card overflow-hidden">
    <div class="table-wrap">
        <table class="table table-compact" id="list-table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Course Details</th>
                    <th style="text-align:center;">Type</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:center;">Date</th>
                    @if(!$isAdmin) <th style="text-align:center;">Actions</th> @endif
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $att)
                @php
                    $initials = collect(explode(' ', $att->emp_name))->take(2)->map(fn($w) => strtoupper($w[0] ?? ''))->implode('');
                @endphp
                <tr>
                    <td>
                        <div class="tr-emp-cell">
                            <div class="tr-emp-avatar">{{ $initials }}</div>
                            <div>
                                <div style="font-weight:700;color:var(--text);">{{ $att->emp_name }}</div>
                                <div style="font-size:.72rem;color:var(--muted);margin-top:.1rem;">{{ $att->staff_no }} &middot; {{ $att->dept_name }}</div>
                                @if($att->position)
                                <div style="font-size:.7rem;color:#6366f1;margin-top:.1rem;font-style:italic;">{{ $att->position }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="font-weight:700;color:var(--text);">{{ $att->training_title }}</div>
                        <div style="font-size:.72rem;color:var(--muted);margin-top:.1rem;">{{ $att->training_code }} &middot; {{ $att->venue ?: 'N/A' }}</div>
                    </td>
                    <td style="text-align:center;">
                        <span class="tr-type-badge {{ $att->training_type === 'Internal' ? 'tr-int' : 'tr-ext' }}">{{ $att->training_type }}</span>
                    </td>
                    <td style="text-align:center;">
                        <span class="status-badge {{ $att->status === 'Completed' ? 'status-completed' : ($att->status === 'In Progress' ? 'status-in-progress' : 'status-scheduled') }}">
                            {{ $att->status }}
                        </span>
                    </td>
                    <td style="text-align:center;">
                        @if($att->start_date)
                        <div class="tr-date-cell">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            @if($att->end_date && $att->end_date !== $att->start_date)
                                {{ \Carbon\Carbon::parse($att->start_date)->format('d M') }} – {{ \Carbon\Carbon::parse($att->end_date)->format('d M Y') }}
                            @else
                                {{ \Carbon\Carbon::parse($att->start_date)->format('d M Y') }}
                            @endif
                        </div>
                        @else
                        <span style="color:var(--muted);">—</span>
                        @endif
                    </td>
                    @if(!$isAdmin)
                    <td style="text-align:center;">
                        <button class="btn btn-sm btn-outline" style="padding:.15rem .4rem;font-size:.65rem;" 
                                onclick="openRequestModal('Training Record', {{ $att->id }}, '{{ $att->training_title }}')">
                            Request Update
                        </button>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                            <p>No training records found</p>
                            <span style="font-size:.8rem;color:var(--muted);">Try adjusting the filter or importing data.</span>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     COURSES VIEW
══════════════════════════════════════════════════════════ --}}
@elseif($view === 'courses')

<div class="tr-search-bar" style="flex-wrap:wrap;gap:.6rem;">
    <div class="app-search" style="flex:1;min-width:180px;">
        <svg class="app-search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="course-search" placeholder="Search course title or code…" oninput="filterCourseCards()">
    </div>
    <div style="display:flex;align-items:center;gap:.6rem;flex-wrap:wrap;">
        <select id="year-filter" class="tr-select" style="min-width:105px;height:32px;padding:0 .5rem;font-size:.75rem;" onchange="filterCourseCards()">
            <option value="">All Years</option>
            @foreach($years as $year)
                <option value="{{ $year }}" {{ (string)$year_filter === (string)$year ? 'selected' : '' }}>{{ $year }}</option>
            @endforeach
        </select>
        <select id="course-time-filter" class="tr-select" style="min-width:140px;height:32px;padding:0 .5rem;font-size:.75rem;" onchange="handleCourseFilterChange(this.value)">
            <option value="all" {{ !request('sort') ? 'selected' : '' }}>All Time</option>
            <option value="upcoming">Upcoming</option>
            <option value="past">Past</option>
            <option value="recent" {{ request('sort') === 'recent' ? 'selected' : '' }}>Recently Added</option>
        </select>
    </div>
    <span class="tr-search-count" id="course-count-label">{{ count($courses) }} courses</span>
</div>

<div class="tr-course-grid" id="course-grid">
    @foreach($courses as $c)
    @php
        $isInt        = $c->training_type === 'Internal';
        $attendeeCount = $c->staff->count();
    @endphp
    <div data-title="{{ strtolower($c->title) }}" data-code="{{ strtolower($c->code) }}"
         data-start-date="{{ $c->start_date ?? '' }}"
         data-end-date="{{ $c->end_date ?? '' }}"
         class="tr-course-card" style="border-bottom:3px solid {{ $isInt ? '#22c55e' : '#f97316' }};">
        <div class="tr-course-body">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.6rem;">
                <div style="display:flex;align-items:center;gap:.35rem;">
                    <span class="tr-type-badge {{ $isInt ? 'tr-int' : 'tr-ext' }}">{{ $c->training_type }}</span>
                    @if($c->is_private)
                    <span style="font-size:.6rem;font-weight:700;background:#fef9c3;color:#92400e;border:1px solid #fde68a;border-radius:4px;padding:.1rem .35rem;letter-spacing:.04em;">PRIVATE</span>
                    @endif
                </div>
                <span class="tr-course-code">{{ $c->code }}</span>
            </div>
            <button onclick="openCourseModal({{ $c->id }})" class="tr-course-title">{{ $c->title }}</button>
            <div class="tr-course-meta">
                <div class="tr-course-info">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    @if($c->start_date)
                        @if($c->end_date && $c->end_date !== $c->start_date)
                            {{ \Carbon\Carbon::parse($c->start_date)->format('d M') }} – {{ \Carbon\Carbon::parse($c->end_date)->format('d M Y') }}
                        @else
                            {{ \Carbon\Carbon::parse($c->start_date)->format('d M Y') }}
                        @endif
                    @else
                        Date TBD
                    @endif
                </div>
                <div class="tr-course-info">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    {{ $c->venue ?: 'Venue TBD' }}
                </div>
                <div class="tr-course-info">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg>
                    {{ $attendeeCount }} {{ $attendeeCount === 1 ? 'attendee' : 'attendees' }}
                </div>
            </div>
            @if($isAdmin)
            <div style="margin-top:.75rem;padding-top:.75rem;border-top:1px solid var(--border,#e2e8f0);">
                @canwrite
                <a href="{{ route('training.qr.page', $c->id) }}"
                   style="display:inline-flex;align-items:center;gap:.35rem;font-size:.75rem;font-weight:600;color:#6366f1;text-decoration:none;padding:.3rem .65rem;border:1.5px solid #c7d2fe;border-radius:6px;transition:background .15s;"
                   onmouseover="this.style.background='#ede9fe'" onmouseout="this.style.background='transparent'">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Attendees
                </a>
                <a href="{{ route('training.course.export', $c->id) }}"
                   style="display:inline-flex;align-items:center;gap:.35rem;font-size:.75rem;font-weight:600;color:#16a34a;text-decoration:none;padding:.3rem .65rem;border:1.5px solid #bbf7d0;border-radius:6px;transition:background .15s;margin-left:.4rem;"
                   onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='transparent'">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Report
                </a>
                @endcanwrite
                <a href="{{ route('training.projector', $c->id) }}"
                   target="_blank"
                   style="display:inline-flex;align-items:center;gap:.35rem;font-size:.75rem;font-weight:600;color:#0891b2;text-decoration:none;padding:.3rem .65rem;border:1.5px solid #a5f3fc;border-radius:6px;transition:background .15s;margin-left:.4rem;"
                   onmouseover="this.style.background='#cffafe'" onmouseout="this.style.background='transparent'">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                    Projector
                </a>
                @canwrite
                <button onclick="openEditCourse({{ $c->id }})"
                   style="display:inline-flex;align-items:center;gap:.35rem;font-size:.75rem;font-weight:600;color:#475569;padding:.3rem .65rem;border:1.5px solid #cbd5e1;border-radius:6px;transition:background .15s;margin-left:.4rem;background:none;cursor:pointer;"
                   onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Edit
                </button>
                @endcanwrite
            </div>
            @endif
        </div>
    </div>
    @endforeach
</div>

<script>
const courseAttendees = {
    @foreach($courses as $c)
    {{ $c->id }}: {
        title: @json($c->title),
        type: @json($c->training_type),
        code: @json($c->code),
        start_date: @json($c->start_date ?? ''),
        end_date: @json($c->end_date ?? ''),
        display_date: @json($c->start_date ? ($c->end_date && $c->end_date !== $c->start_date ? \Carbon\Carbon::parse($c->start_date)->format('d M') . ' – ' . \Carbon\Carbon::parse($c->end_date)->format('d M Y') : \Carbon\Carbon::parse($c->start_date)->format('d M Y')) : 'TBD'),
        venue: @json($c->venue ?: 'TBD'),
        attendees: [
            @foreach($c->staff as $s)
            { name: @json($s->name), staff_no: @json($s->staff_no), dept: @json($s->department?->name ?? ''), status: @json($s->pivot->status ?? 'Completed') },
            @endforeach
        ]
    },
    @endforeach
};
const courseData = {
    @foreach($courses as $c)
    {{ $c->id }}: {
        title: @json($c->title),
        type: @json($c->training_type),
        company: @json($c->company ?? ''),
        start_date: @json($c->start_date ?? ''),
        end_date: @json($c->end_date ?? ''),
        venue: @json($c->venue ?? ''),
        duration: @json($c->duration ?? ''),
        platform: @json($c->platform ?? 'HR'),
        pic_id: @json($c->pic_id ?? ''),
    },
    @endforeach
};
</script>

@endif

{{-- ── ATTENDEES MODAL ─────────────────────────────────────── --}}
<div class="modal" id="attendees-modal">
    <div class="modal-box" style="max-width:640px;max-height:85vh;overflow:hidden;display:flex;flex-direction:column;">
        <div class="modal-header" style="flex-shrink:0;">
            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:center;gap:.45rem;margin-bottom:.3rem;">
                    <span id="am-type-badge" class="tr-type-badge"></span>
                    <span id="am-code" class="tr-course-code"></span>
                </div>
                <div id="am-title" style="font-size:.95rem;font-weight:800;color:var(--navy);line-height:1.4;"></div>
            </div>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <div style="padding:.6rem 1.5rem;border-bottom:1px solid var(--border);display:flex;gap:1.25rem;flex-wrap:wrap;flex-shrink:0;">
            <span class="tr-modal-meta">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <span id="am-date-text"></span>
            </span>
            <span class="tr-modal-meta">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <span id="am-venue-text"></span>
            </span>
            <span class="tr-modal-meta">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                <span id="am-count"></span>
            </span>
        </div>
        <div id="am-body" style="overflow-y:auto;flex:1;"></div>
    </div>
</div>

</div>{{-- end tr-container --}}

{{-- ── ADMIN MODALS (outside tr-container so z-index stacks correctly) ── --}}
@if($isAdmin)

<div class="modal" id="course-modal">
    <div class="modal-box" style="max-width:520px;">
        <div class="modal-header cm-modal-header">
            <div style="display:flex;align-items:center;gap:.75rem;">
                <div class="cm-header-icon">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <div>
                    <h3 style="color:white;margin:0;font-size:.95rem;">Add New Course</h3>
                    <p style="color:rgba(255,255,255,.6);font-size:.75rem;margin:.1rem 0 0;">Course code is auto-generated</p>
                </div>
            </div>
            <button class="modal-close" onclick="closeModal()" style="color:rgba(255,255,255,.6);font-size:1.4rem;">×</button>
        </div>
        <form action="{{ route('training.courses.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-grid">

                    <div class="cm-section-label form-full">Course Details</div>

                    <div class="form-group form-full">
                        <label>Course Title <span style="color:var(--danger);">*</span></label>
                        <input type="text" name="title" required placeholder="e.g. Fire Safety Awareness">
                    </div>
                    <div class="form-group">
                        <label>Type <span style="color:var(--danger);">*</span></label>
                        <div style="display:flex;gap:.4rem;margin-top:.2rem;">
                            <label class="type-chip type-chip-ext">
                                <input type="radio" name="training_type" value="External" checked>
                                <span>
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                                    External
                                </span>
                            </label>
                            <label class="type-chip type-chip-int">
                                <input type="radio" name="training_type" value="Internal">
                                <span>
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                                    Internal
                                </span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Company</label>
                        <select name="company" id="create-company" onchange="filterDepartments('create-company', 'create-department')">
                            <option value="" data-code="">-- Optional --</option>
                            @foreach($companies as $c)
                                <option value="{{ $c->name }}" data-code="{{ $c->code }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Department</label>
                        <select name="department" id="create-department">
                            <option value="" data-company="">-- Optional --</option>
                            @foreach($allDepartments as $d)
                                <option value="{{ $d->name }}" data-company="{{ $d->company }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="cm-section-label form-full">Schedule &amp; Location</div>

                    <div class="form-group">
                        <label>Start Date <span style="color:var(--danger);">*</span></label>
                        <input type="date" name="start_date" id="ac-start-date" required>
                    </div>
                    <div class="form-group">
                        <label>End Date</label>
                        <input type="date" name="end_date" id="ac-end-date">
                        <span style="font-size:.7rem;color:var(--muted);margin-top:.2rem;display:block;">Leave blank for 1-day course</span>
                    </div>
                    <div class="form-group">
                        <label>Venue</label>
                        <input type="text" name="venue" placeholder="e.g. Main Hall FJB">
                    </div>
                    <div class="form-group">
                        <label>Duration</label>
                        <input type="text" name="duration" placeholder="e.g. 2 days">
                    </div>

                    <div class="cm-section-label form-full">Settings</div>

                    <div class="form-group form-full">
                        <label>Platform</label>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.5rem;margin-top:.35rem;">
                            <label class="vis-card">
                                <input type="radio" name="platform" value="HR" checked>
                                <div class="vis-card-inner">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                    <div>
                                        <div class="vis-card-title">Physical</div>
                                        <div class="vis-card-desc">In-person training (HR)</div>
                                    </div>
                                </div>
                            </label>
                            <label class="vis-card">
                                <input type="radio" name="platform" value="LMS">
                                <div class="vis-card-inner">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                                    <div>
                                        <div class="vis-card-title">Online</div>
                                        <div class="vis-card-desc">E-Learning module (LMS)</div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="form-group form-full">
                        <label>Person In Charge (PIC)</label>
                        <input type="hidden" name="pic_id" id="create-pic-id">
                        <div class="live-search-wrap" id="create-pic-search-wrap">
                            <div class="live-search-input-wrap">
                                <svg class="live-search-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                                <input type="text" id="create-pic-search" class="live-search-input" placeholder="Search by name…" autocomplete="off">
                                <button type="button" class="live-search-clear" id="create-pic-clear" onclick="clearCreatePicSearch()" style="display:none;">×</button>
                            </div>
                            <div class="live-search-results" id="create-pic-results"></div>
                        </div>
                        <div id="create-pic-selected" class="live-search-selected" style="display:none;"></div>
                        <span style="font-size:.7rem;color:var(--muted);margin-top:.2rem;display:block;">PICs can manage this course and take attendance.</span>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary" style="display:flex;align-items:center;gap:.4rem;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12l7-7 7 7"/></svg>
                    Create Course
                </button>
            </div>
        </form>
    </div>
</div>

<div class="modal" id="edit-course-modal">
    <div class="modal-box" style="max-width:520px;">
        <div class="modal-header cm-modal-header" style="background:linear-gradient(135deg,#1a3a2a 0%,#14532d 100%);">
            <div style="display:flex;align-items:center;gap:.75rem;">
                <div class="cm-header-icon">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </div>
                <div>
                    <h3 style="color:white;margin:0;font-size:.95rem;">Edit Course</h3>
                    <p style="color:rgba(255,255,255,.6);font-size:.75rem;margin:.1rem 0 0;">Course code cannot be changed</p>
                </div>
            </div>
            <button class="modal-close" onclick="closeModal()" style="color:rgba(255,255,255,.6);font-size:1.4rem;">×</button>
        </div>
        <form id="edit-course-form" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-grid">

                    <div class="cm-section-label form-full">Course Details</div>

                    <div class="form-group form-full">
                        <label>Course Title <span style="color:var(--danger);">*</span></label>
                        <input type="text" name="title" id="ec-title" required placeholder="e.g. Fire Safety Awareness">
                    </div>
                    <div class="form-group">
                        <label>Type <span style="color:var(--danger);">*</span></label>
                        <div style="display:flex;gap:.4rem;margin-top:.2rem;">
                            <label class="type-chip type-chip-ext">
                                <input type="radio" name="training_type" id="ec-type-ext" value="External">
                                <span>
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                                    External
                                </span>
                            </label>
                            <label class="type-chip type-chip-int">
                                <input type="radio" name="training_type" id="ec-type-int" value="Internal">
                                <span>
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                                    Internal
                                </span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Company</label>
                        <select name="company" id="ec-company" onchange="filterDepartments('ec-company', 'ec-department')">
                            <option value="" data-code="">-- Optional --</option>
                            @foreach($companies as $c)
                                <option value="{{ $c->name }}" data-code="{{ $c->code }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Department</label>
                        <select name="department" id="ec-department">
                            <option value="" data-company="">-- Optional --</option>
                            @foreach($allDepartments as $d)
                                <option value="{{ $d->name }}" data-company="{{ $d->company }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="cm-section-label form-full">Schedule &amp; Location</div>

                    <div class="form-group">
                        <label>Start Date <span style="color:var(--danger);">*</span></label>
                        <input type="date" name="start_date" id="ec-start-date" required>
                    </div>
                    <div class="form-group">
                        <label>End Date</label>
                        <input type="date" name="end_date" id="ec-end-date">
                        <span style="font-size:.7rem;color:var(--muted);margin-top:.2rem;display:block;">Leave blank for 1-day course</span>
                    </div>
                    <div class="form-group">
                        <label>Venue</label>
                        <input type="text" name="venue" id="ec-venue" placeholder="e.g. Main Hall FJB">
                    </div>
                    <div class="form-group">
                        <label>Duration</label>
                        <input type="text" name="duration" id="ec-duration" placeholder="e.g. 2 days">
                    </div>

                    <div class="cm-section-label form-full">Settings</div>

                    <div class="form-group form-full">
                        <label>Platform</label>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.5rem;margin-top:.35rem;">
                            <label class="vis-card">
                                <input type="radio" name="platform" id="ec-platform-hr" value="HR">
                                <div class="vis-card-inner">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                    <div>
                                        <div class="vis-card-title">Physical</div>
                                        <div class="vis-card-desc">In-person training (HR)</div>
                                    </div>
                                </div>
                            </label>
                            <label class="vis-card">
                                <input type="radio" name="platform" id="ec-platform-lms" value="LMS">
                                <div class="vis-card-inner">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                                    <div>
                                        <div class="vis-card-title">Online</div>
                                        <div class="vis-card-desc">E-Learning module (LMS)</div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="form-group form-full">
                        <label>Person In Charge (PIC)</label>
                        <input type="hidden" name="pic_id" id="edit-pic-id">
                        <div class="live-search-wrap" id="edit-pic-search-wrap">
                            <div class="live-search-input-wrap">
                                <svg class="live-search-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                                <input type="text" id="edit-pic-search" class="live-search-input" placeholder="Search by name…" autocomplete="off">
                                <button type="button" class="live-search-clear" id="edit-pic-clear" onclick="clearEditPicSearch()" style="display:none;">×</button>
                            </div>
                            <div class="live-search-results" id="edit-pic-results"></div>
                        </div>
                        <div id="edit-pic-selected" class="live-search-selected" style="display:none;"></div>
                        <span style="font-size:.7rem;color:var(--muted);margin-top:.2rem;display:block;">PICs can manage this course and take attendance.</span>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary" style="display:flex;align-items:center;gap:.4rem;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<div class="modal" id="attendance-modal">
    <div class="modal-box" style="max-width:520px;">
        <div class="modal-header">
            <div style="display:flex;align-items:center;gap:.75rem;">
                <div class="tr-modal-icon" style="background:#eef2ff;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div>
                    <h3>Add Attendance Record</h3>
                    <div style="font-size:.73rem;color:var(--muted);margin-top:.1rem;">Link a staff member to a course</div>
                </div>
            </div>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <form action="{{ route('training.attendance.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label>Staff Member <span style="color:var(--danger);">*</span></label>
                    <input type="hidden" name="staff_id" id="att-staff-id">
                    <div class="live-search-wrap" id="staff-search-wrap">
                        <div class="live-search-input-wrap">
                            <svg class="live-search-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                            <input type="text" id="att-staff-search" class="live-search-input" placeholder="Search by name or staff no…" autocomplete="off">
                            <button type="button" class="live-search-clear" id="att-staff-clear" onclick="clearStaffSearch()" style="display:none;">×</button>
                        </div>
                        <div class="live-search-results" id="att-staff-results"></div>
                    </div>
                    <div id="att-staff-selected" class="live-search-selected" style="display:none;"></div>
                </div>
                <div class="form-group">
                    <label>Course <span style="color:var(--danger);">*</span></label>
                    <input type="hidden" name="course_id" id="att-course-id">
                    <div class="live-search-wrap" id="course-search-wrap">
                        <div class="live-search-input-wrap">
                            <svg class="live-search-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                            <input type="text" id="att-course-search" class="live-search-input" placeholder="Search by code or title…" autocomplete="off">
                            <button type="button" class="live-search-clear" id="att-course-clear" onclick="clearCourseSearch()" style="display:none;">×</button>
                        </div>
                        <div class="live-search-results" id="att-course-results"></div>
                    </div>
                    <div id="att-course-selected" class="live-search-selected" style="display:none;"></div>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Status <span style="color:var(--danger);">*</span></label>
                        <select name="status" required>
                            <option value="Completed">Completed</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Scheduled">Scheduled</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Training Type <span style="color:var(--danger);">*</span></label>
                        <select name="training_type" required>
                            <option value="External">External</option>
                            <option value="Internal">Internal</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Remarks</label>
                    <textarea name="remarks" rows="2" placeholder="Optional notes…"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Record</button>
            </div>
        </form>
    </div>
</div>

@endif
@endsection

@section('styles')
<style>
/* ── Container ── */
.tr-container { padding: 0; background: var(--bg); min-height: 100vh; }

/* ── Stats Banner ── */
.tr-stats-banner {
    background: var(--surface);
    border-radius: 12px;
    box-shadow: var(--shadow);
    border: 1px solid var(--border);
    border-top: 3px solid #4f46e5;
    padding: .75rem 1.25rem;
    margin-bottom: 1rem;
}
.tr-stats-grid { display: grid; grid-template-columns: repeat(4, 1fr) 1.5fr; gap: 0; align-items: center; }
.tr-stat-item { display: flex; align-items: center; gap: .55rem; }
.tr-stat-icon-wrap { width: 28px; height: 28px; border-radius: 7px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.tr-stat-label { font-size: .62rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; margin-bottom: .12rem; }
.tr-stat-value { font-size: 1.35rem; font-weight: 800; color: var(--text); line-height: 1; }
.tr-stat-unit  { font-size: .6rem; font-weight: 700; color: var(--muted); text-transform: uppercase; }
.tr-stat-value-wrap { display: flex; align-items: baseline; gap: .25rem; }
.tr-stat-progress-wrap { padding-left: 1.25rem; }
.tr-progress-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: .4rem; }
.tr-progress-label { font-size: .6rem; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .05em; }
.tr-progress-pct   { font-size: .6rem; font-weight: 700; color: var(--muted); }
.tr-progress-track { height: 6px; background: #f1f5f9; border-radius: 99px; overflow: hidden; margin-bottom: .4rem; }
.tr-progress-legend { display: flex; gap: .75rem; }
.tr-legend-item { display: flex; align-items: center; gap: .25rem; font-size: .62rem; font-weight: 700; color: var(--muted); }
.tr-dot { width: 6px; height: 6px; border-radius: 50%; display: inline-block; }

/* ── View Tabs ── */
.tr-tabs { display: flex; gap: .35rem; flex-wrap: wrap; margin-bottom: .75rem; }
.tr-tab  {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .38rem .85rem; border-radius: 99px;
    font-size: .8rem; font-weight: 700;
    border: 1.5px solid var(--border);
    text-decoration: none;
    transition: background .15s, color .15s, border-color .15s;
    background: var(--surface); color: var(--muted);
}
.tr-tab:hover:not(.active) { border-color: #94a3b8; color: var(--text); }
.tr-tab.active { background: var(--navy); color: #fff; border-color: var(--navy); box-shadow: 0 2px 8px rgba(20,43,71,.2); }

/* ── Type Filter Pills ── */
.tr-type-pills {
    display: inline-flex; background: var(--surface);
    border: 1.5px solid var(--border); border-radius: 99px;
    padding: .2rem; gap: .1rem; box-shadow: var(--shadow);
}
.tr-type-pill  { padding: .26rem .78rem; border-radius: 99px; font-size: .74rem; font-weight: 700; text-decoration: none; transition: .15s; color: var(--muted); }
.tr-type-pill.active          { background: var(--navy);  color: #fff; }
.tr-type-pill.active.tr-pill-ext { background: #fff7ed; color: #ea580c; }
.tr-type-pill.active.tr-pill-int { background: #f0fdf4; color: #16a34a; }

/* button reset for pill buttons */
button.tr-type-pill { background: none; border: none; cursor: pointer; font-family: inherit; display: inline-flex; align-items: center; }
button.tr-type-pill.active { background: var(--navy); color: #fff; }

/* ── Type Badges ── */
.tr-type-badge { display: inline-flex; padding: .22rem .6rem; border-radius: 99px; font-size: .67rem; font-weight: 700; text-transform: uppercase; }
.tr-type-badge.tr-ext { background: #fff7ed; color: #ea580c; }
.tr-type-badge.tr-int { background: #f0fdf4; color: #16a34a; }

/* ── Search Bar ── */
.tr-search-bar {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: 10px; box-shadow: var(--shadow);
    padding: .7rem 1rem; margin-bottom: .85rem;
    display: flex; gap: .6rem; align-items: center; flex-wrap: wrap;
}
.tr-search-input-wrap { position: relative; display: flex; align-items: center; }
.tr-search-icon       { position: absolute; left: .7rem; pointer-events: none; }
.tr-search-input {
    width: 100%; padding: .45rem .7rem .45rem 2.1rem;
    border: 1.5px solid var(--border); border-radius: 8px;
    font-size: .82rem; color: var(--text); outline: none;
    background: var(--surface); transition: border-color .15s; font-family: inherit;
}
.tr-search-input:focus { border-color: #6366f1; }
.tr-search-count { font-size: .73rem; color: var(--muted); font-weight: 600; white-space: nowrap; }

/* ── Dept Cards ── */
.tr-dept-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
.tr-dept-card {
    background: var(--surface); border-radius: 12px;
    border: 1px solid var(--border); box-shadow: var(--shadow);
    border-left: 4px solid #6366f1;
    transition: transform .18s, box-shadow .18s;
}
.tr-dept-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-lg); }
.tr-dept-card-inner { padding: 1rem; }
.tr-dept-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: .85rem; gap: .5rem; }
.tr-dept-co-badge {
    display: inline-block; padding: .12rem .4rem; border-radius: 5px;
    font-size: .6rem; font-weight: 700; background: #eef2ff; color: #6366f1;
    text-transform: uppercase; letter-spacing: .04em; margin-bottom: .3rem;
}
.tr-dept-title { font-size: .9rem; font-weight: 800; color: var(--navy); line-height: 1.3; margin: 0; }
.tr-dept-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: .35rem; text-align: center; margin-bottom: .75rem; }
.tr-dept-stat  { }
.tr-dstat-val  { font-size: 1.1rem; font-weight: 800; color: #6366f1; line-height: 1.1; }
.tr-dstat-lbl  { font-size: .56rem; font-weight: 700; color: var(--muted); text-transform: uppercase; margin-top: .15rem; }
.tr-dept-progress { display: flex; align-items: center; gap: .35rem; }
.tr-dp-pct  { font-size: .56rem; font-weight: 700; white-space: nowrap; }
.tr-dp-track { flex: 1; height: 5px; background: #f1f5f9; border-radius: 99px; overflow: hidden; }

/* ── List View Filter Bar ── */
.tr-filter-bar {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: 10px; box-shadow: var(--shadow);
    padding: .7rem 1rem; margin-bottom: .85rem;
    display: flex; gap: .55rem; align-items: center; flex-wrap: wrap;
}
.tr-select {
    padding: .43rem .65rem; border: 1.5px solid var(--border); border-radius: 8px;
    font-size: .82rem; color: var(--text); background: var(--surface);
    outline: none; min-width: 140px; font-family: inherit;
}
.tr-filter-actions { display: flex; align-items: center; gap: .4rem; }
.tr-clear-btn { font-size: .78rem; font-weight: 600; color: var(--muted); text-decoration: none; white-space: nowrap; }
.tr-clear-btn:hover { color: var(--text); }

/* ── List Table Cells ── */
.tr-emp-cell   { display: flex; align-items: center; gap: .65rem; }
.tr-emp-avatar {
    width: 32px; height: 32px; border-radius: 50%;
    background: #eef2ff; color: #6366f1;
    font-size: .68rem; font-weight: 800;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.tr-date-cell  { display: inline-flex; align-items: center; gap: .3rem; font-size: .77rem; font-weight: 600; color: var(--text); }

/* ── Courses Grid ── */
.tr-course-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: .9rem; }
.tr-course-card {
    background: var(--surface); border-radius: 11px;
    border: 1px solid var(--border); box-shadow: var(--shadow);
    overflow: hidden; display: flex; flex-direction: column;
    transition: transform .18s, box-shadow .18s;
}
.tr-course-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-lg); }
.tr-course-body  { padding: .9rem; flex: 1; display: flex; flex-direction: column; }
.tr-course-title {
    font-size: .85rem; font-weight: 800; color: var(--navy); line-height: 1.4;
    margin-bottom: auto; text-align: left;
    background: none; border: none; cursor: pointer; padding: 0; width: 100%;
    display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;
    font-family: inherit; transition: color .15s;
}
.tr-course-title:hover { color: #6366f1; }
.tr-course-code { font-size: .6rem; font-family: monospace; font-weight: 700; color: var(--muted); }
.tr-course-meta { margin-top: .8rem; padding-top: .65rem; border-top: 1px solid var(--border); display: flex; flex-direction: column; gap: .3rem; }
.tr-course-info { display: flex; align-items: center; gap: .4rem; font-size: .7rem; color: var(--muted); }

/* ── Modal Helpers ── */
.tr-modal-meta { display: flex; align-items: center; gap: .3rem; font-size: .73rem; color: var(--muted); }
.tr-modal-icon { width: 36px; height: 36px; border-radius: 9px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }

/* ── Course Modal Redesign ── */
.cm-modal-header {
    background: linear-gradient(135deg, var(--navy) 0%, #1e3a5f 100%);
    border-radius: 14px 14px 0 0;
    padding: 1.25rem 1.5rem;
    border-bottom: none;
}
.cm-header-icon {
    width: 34px; height: 34px; border-radius: 9px;
    background: rgba(255,255,255,.15);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.cm-section-label {
    font-size: .68rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .07em; color: var(--muted);
    padding-bottom: .4rem;
    border-bottom: 1px solid var(--border);
}
/* Type chips */
.type-chip { cursor: pointer; }
.type-chip input[type="radio"] { display: none; }
.type-chip span {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .38rem .72rem;
    border-radius: 99px;
    border: 1.5px solid var(--border);
    font-size: .78rem; font-weight: 500;
    color: var(--text); background: var(--surface);
    transition: background .13s, border-color .13s, color .13s;
    user-select: none; white-space: nowrap;
}
.type-chip:hover span { border-color: var(--muted); }
.type-chip-ext input:checked + span { background: #fff7ed; border-color: #f97316; color: #ea580c; }
.type-chip-ext input:checked + span svg { stroke: #ea580c; }
.type-chip-int input:checked + span { background: #f0fdf4; border-color: #22c55e; color: #16a34a; }
.type-chip-int input:checked + span svg { stroke: #16a34a; }
/* Visibility cards */
.vis-card { cursor: pointer; }
.vis-card input[type="radio"] { display: none; }
.vis-card-inner {
    display: flex; align-items: center; gap: .6rem;
    padding: .6rem .85rem;
    border: 1.5px solid var(--border);
    border-radius: 10px;
    background: var(--surface);
    transition: border-color .13s, background .13s;
}
.vis-card:hover .vis-card-inner { border-color: var(--muted); }
.vis-card input:checked + .vis-card-inner { border-color: var(--navy); background: #eef2ff; }
.vis-card input:checked + .vis-card-inner svg { stroke: var(--navy); }
.vis-card-title { font-size: .82rem; font-weight: 700; color: var(--text); line-height: 1.2; }
.vis-card-desc { font-size: .71rem; color: var(--muted); margin-top: .1rem; }
.vis-card input:checked + .vis-card-inner .vis-card-title { color: var(--navy); }

/* ── Color Utilities ── */
.bg-orange { background: #fff7ed; } .color-orange { color: #f97316; }
.bg-green  { background: #f0fdf4; } .color-green  { color: #22c55e; }
.bg-indigo { background: #eef2ff; } .color-indigo { color: #6366f1; }
.bg-slate  { background: #f8fafc; } .color-slate  { color: #64748b; }
.border-r  { border-right: 1px solid var(--border); }
.px-lg     { padding-left: 1.25rem; padding-right: 1.25rem; }

/* ── Dark Mode ── */
[data-theme="dark"] .tr-progress-track { background: #1e293b; }
[data-theme="dark"] .tr-dp-track       { background: #1e293b; }
[data-theme="dark"] .tr-dept-co-badge  { background: rgba(99,102,241,.2); color: #a5b4fc; }
[data-theme="dark"] .tr-dstat-val      { color: #a5b4fc; }
[data-theme="dark"] .tr-type-badge.tr-ext { background: rgba(249,115,22,.15); color: #fb923c; }
[data-theme="dark"] .tr-type-badge.tr-int { background: rgba(34,197,94,.15);  color: #4ade80; }
[data-theme="dark"] .tr-type-pill.active.tr-pill-ext { background: rgba(249,115,22,.2); color: #fb923c; }
[data-theme="dark"] .tr-type-pill.active.tr-pill-int { background: rgba(34,197,94,.2);  color: #4ade80; }

/* ── Unassigned dept card ── */
.tr-dept-card-unassigned { opacity: .88; }
.tr-dept-title-muted { color: var(--muted) !important; font-style: italic; }

/* ── Responsive: 1100px ── */
@media (max-width: 1100px) {
    .tr-stats-grid { grid-template-columns: repeat(2, 1fr); gap: .75rem; }
    .tr-stat-item.border-r { border-right: none; }
    .tr-stat-progress-wrap { grid-column: 1 / -1; padding-left: 0; padding-top: .6rem; border-top: 1px solid var(--border); }
    .tr-dept-grid   { grid-template-columns: repeat(2, 1fr); }
    .tr-course-grid { grid-template-columns: repeat(3, 1fr); }
}

/* ── Responsive: 768px ── */
@media (max-width: 768px) {
    .tr-stats-banner { padding: .6rem .875rem; }
    .tr-dept-grid    { grid-template-columns: 1fr; }
    .tr-course-grid  { grid-template-columns: repeat(2, 1fr); }
    .tr-filter-bar   { padding: .55rem .75rem; gap: .35rem; }
    .tr-select       { flex: 1; min-width: 100px; }
    .tr-filter-actions { width: 100%; justify-content: space-between; }
    .tr-filter-actions .btn { flex: 1; justify-content: center; font-size: .75rem; }
    .tr-stat-value { font-size: 1.2rem; }
}

/* ── Responsive: 480px ── */
@media (max-width: 480px) {
    .tr-stats-grid  { grid-template-columns: 1fr 1fr; gap: .5rem; }
    .tr-stat-item   { padding: 0; gap: .4rem; }
    .tr-stat-icon-wrap { width: 24px; height: 24px; border-radius: 6px; }
    .tr-stat-icon-wrap svg { width: 14px; height: 14px; }
    .tr-stat-label { font-size: .58rem; }
    .tr-stat-value { font-size: 1.1rem; }
    .tr-stat-progress-wrap { grid-column: span 2; }
    .tr-progress-header { margin-bottom: .25rem; }
    .tr-progress-pct { font-size: .55rem; }
    .tr-progress-track { height: 5px; }
    .tr-course-grid { grid-template-columns: 1fr; }
}

@media (max-width: 360px) {
    .tr-stats-grid { grid-template-columns: 1fr; }
    .tr-stat-progress-wrap { grid-column: span 1; }
    .tr-search-bar { padding: .5rem; }
    .tr-dept-header { flex-direction: column; gap: .25rem; }
}

/* ── Live Search ── */
.live-search-wrap { position: relative; }
.live-search-input-wrap {
    display: flex;
    align-items: center;
    gap: .45rem;
    border: 1.5px solid var(--border);
    border-radius: 8px;
    padding: 0 .75rem;
    background: var(--surface);
    transition: border-color .15s;
}
.live-search-input-wrap:focus-within { border-color: var(--primary); }
.live-search-icon { flex-shrink: 0; color: var(--muted); }
.live-search-input {
    flex: 1;
    border: none;
    outline: none;
    background: transparent;
    padding: .55rem 0;
    font-size: .875rem;
    color: var(--text);
}
.live-search-clear {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1.1rem;
    color: var(--muted);
    padding: 0;
    line-height: 1;
}
.live-search-clear:hover { color: var(--danger); }
.live-search-results {
    position: absolute;
    top: calc(100% + 4px);
    left: 0; right: 0;
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: 8px;
    max-height: 200px;
    overflow-y: auto;
    z-index: 200;
    display: none;
    box-shadow: 0 4px 16px rgba(0,0,0,.1);
}
.live-search-results.open { display: block; }
.live-search-item {
    padding: .55rem .85rem;
    cursor: pointer;
    font-size: .84rem;
    display: flex;
    flex-direction: column;
    gap: .1rem;
    border-bottom: 1px solid var(--border);
}
.live-search-item:last-child { border-bottom: none; }
.live-search-item:hover, .live-search-item.active { background: var(--bg); }
.live-search-item .ls-main { font-weight: 600; color: var(--text); }
.live-search-item .ls-sub { font-size: .73rem; color: var(--muted); }
.live-search-item .ls-highlight { color: var(--primary); }
.live-search-empty {
    padding: .75rem .85rem;
    font-size: .83rem;
    color: var(--muted);
    text-align: center;
}
.live-search-selected {
    margin-top: .4rem;
    padding: .45rem .75rem;
    background: #eef2ff;
    border-radius: 6px;
    font-size: .82rem;
    color: var(--primary);
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: .4rem;
}
.live-search-selected::before {
    content: '✓';
    font-size: .85rem;
}
</style>
@endsection

@section('scripts')
<script>
function filterDepartments(companySelectId, deptSelectId) {
    const compSelect = document.getElementById(companySelectId);
    const deptSelect = document.getElementById(deptSelectId);
    if (!compSelect || !deptSelect) return;
    
    const selectedOpt = compSelect.options[compSelect.selectedIndex];
    const companyCode = selectedOpt ? selectedOpt.getAttribute('data-code') : '';
    
    for (let i = 1; i < deptSelect.options.length; i++) {
        const opt = deptSelect.options[i];
        const optCompany = opt.getAttribute('data-company');
        
        if (!companyCode || optCompany === companyCode) {
            opt.hidden = false;
            opt.disabled = false;
            opt.style.display = '';
        } else {
            opt.hidden = true;
            opt.disabled = true;
            opt.style.display = 'none';
            if (opt.selected) {
                deptSelect.value = '';
            }
        }
    }
}

function filterDeptCards() {
    const q = document.getElementById('dept-search').value.toLowerCase();
    document.querySelectorAll('[data-name]').forEach(card => {
        card.style.display = (card.dataset.name.includes(q) || card.dataset.company.includes(q)) ? '' : 'none';
    });
}

let courseTimeFilter = 'all';

function handleCourseFilterChange(val) {
    if (val === 'recent') {
        const url = new URL(window.location.href);
        url.searchParams.set('sort', 'recent');
        window.location.href = url.toString();
    } else {
        const url = new URL(window.location.href);
        if (url.searchParams.has('sort')) {
            url.searchParams.delete('sort');
            url.hash = 'time_filter=' + val;
            window.location.href = url.toString();
            return;
        }
        
        courseTimeFilter = val;
        filterCourseCards();
    }
}

function filterCourseCards() {
    const q = document.getElementById('course-search').value.toLowerCase();
    const yearFilterEl = document.getElementById('year-filter');
    const yearFilter = yearFilterEl ? yearFilterEl.value : '';
    const today = new Date(); today.setHours(0,0,0,0);
    let visible = 0;
    document.querySelectorAll('#course-grid [data-title]').forEach(card => {
        const matchText = card.dataset.title.includes(q) || card.dataset.code.includes(q);
        
        let matchTime = true;
        if (courseTimeFilter !== 'all') {
            const d = card.dataset.startDate;
            if (!d) {
                matchTime = courseTimeFilter === 'upcoming';
            } else {
                const courseDate = new Date(d); courseDate.setHours(0,0,0,0);
                matchTime = courseTimeFilter === 'upcoming' ? courseDate >= today : courseDate < today;
            }
        }

        let matchYear = true;
        if (yearFilter) {
            const d = card.dataset.startDate;
            if (d) {
                const year = new Date(d).getFullYear().toString();
                matchYear = year === yearFilter;
            } else {
                matchYear = false;
            }
        }

        const show = matchText && matchTime && matchYear;
        card.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    const lbl = document.getElementById('course-count-label');
    if (lbl) lbl.textContent = visible + (visible === 1 ? ' course' : ' courses');
}

function openRequestModal(type, id, ref) {
    document.getElementById('ur_record_type').value = type;
    document.getElementById('ur_record_id').value = id;
    document.getElementById('ur_record_reference').value = ref;
    document.getElementById('ur_display_ref').textContent = ref;
    openModal('requestUpdateModal');
}

function openCourseModal(id) {
    const c = courseAttendees[id];
    if (!c) return;

    const isInt = c.type === 'Internal';
    const badge = document.getElementById('am-type-badge');
    badge.textContent = c.type;
    badge.className = 'tr-type-badge ' + (isInt ? 'tr-int' : 'tr-ext');

    document.getElementById('am-code').textContent       = c.code;
    document.getElementById('am-title').textContent      = c.title;
    document.getElementById('am-date-text').textContent  = c.display_date;
    document.getElementById('am-venue-text').textContent = c.venue;
    document.getElementById('am-count').textContent      = c.attendees.length + (c.attendees.length === 1 ? ' attendee' : ' attendees');

    const statusClass = s => s === 'Completed' ? 'status-completed' : (s === 'In Progress' ? 'status-in-progress' : 'status-scheduled');

    let html = '';
    if (c.attendees.length === 0) {
        html = `<div class="empty-state" style="padding:3rem 1.5rem;">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <p>No attendees yet</p>
            <span style="font-size:.8rem;color:var(--muted);">Import or add attendance records to see them here.</span>
        </div>`;
    } else {
        html = `<table class="table" style="font-size:.82rem;">
            <thead><tr><th>#</th><th>Name</th><th>Staff No</th><th>Department</th><th style="text-align:center;">Status</th></tr></thead><tbody>`;
        c.attendees.forEach((a, i) => {
            const init = a.name.split(' ').slice(0,2).map(w => w[0]||'').join('').toUpperCase();
            html += `<tr>
                <td style="color:var(--muted);font-size:.73rem;font-weight:600;">${i+1}</td>
                <td>
                    <div class="tr-emp-cell">
                        <div class="tr-emp-avatar">${init}</div>
                        <span style="font-weight:700;color:var(--text);">${a.name}</span>
                    </div>
                </td>
                <td style="color:var(--muted);font-size:.77rem;">${a.staff_no}</td>
                <td style="color:var(--muted);font-size:.77rem;">${a.dept || '—'}</td>
                <td style="text-align:center;"><span class="status-badge ${statusClass(a.status)}">${a.status}</span></td>
            </tr>`;
        });
        html += '</tbody></table>';
    }
    document.getElementById('am-body').innerHTML = html;
    openModal('attendees-modal');
}

/* ── Open/Private picker toggle ── */
function togglePrivatePicker(ctx, val) {
    document.getElementById(ctx === 'create' ? 'create-private-picker' : 'edit-private-picker')
        .style.display = val === '1' ? '' : 'none';
}

/* ── Multi-select staff picker ── */
function buildMultiStaffPicker(searchId, resultsId, tagsId, hiddenId) {
    const input    = document.getElementById(searchId);
    const results  = document.getElementById(resultsId);
    const tagsEl   = document.getElementById(tagsId);
    const hiddenEl = document.getElementById(hiddenId);
    const selected = {};

    function renderTags() {
        tagsEl.innerHTML = Object.entries(selected).map(([id, name]) =>
            `<span style="display:inline-flex;align-items:center;gap:.3rem;background:#eef2ff;color:#4338ca;font-size:.72rem;font-weight:700;border-radius:5px;padding:.2rem .5rem;">
                ${name}
                <button type="button" onclick="removeMsTag('${id}','${searchId}')" style="background:none;border:none;cursor:pointer;color:#6366f1;font-size:.85rem;line-height:1;padding:0;">×</button>
            </span>`
        ).join('');
        hiddenEl.innerHTML = Object.keys(selected).map(id =>
            `<input type="hidden" name="staff_ids[]" value="${id}">`
        ).join('');
    }

    window['addMsTag_' + searchId] = function(id, name) {
        selected[id] = name;
        input.value = '';
        results.classList.remove('open');
        renderTags();
    };
    window['removeMsTag_' + searchId] = function(id) {
        delete selected[id];
        renderTags();
    };
    window['clearMsPicker_' + searchId] = function() {
        Object.keys(selected).forEach(k => delete selected[k]);
        renderTags();
        input.value = '';
        results.classList.remove('open');
    };

    input.addEventListener('input', function() {
        const q = this.value.trim();
        if (!q) { results.classList.remove('open'); return; }
        const matches = attStaffData.filter(d =>
            (d.name.toLowerCase().includes(q.toLowerCase()) ||
             d.staff_no.toLowerCase().includes(q.toLowerCase())) &&
            !selected[d.id]
        ).slice(0, 20);
        results.innerHTML = matches.length
            ? matches.map(d => `<div class="live-search-item" data-id="${d.id}" data-name="${d.name}">
                    <span class="ls-main">${highlight(d.name, q)}</span>
                    <span class="ls-sub">${d.staff_no}</span>
                </div>`).join('')
            : '<div class="live-search-empty">No results</div>';
        results.querySelectorAll('.live-search-item').forEach(el => {
            el.addEventListener('mousedown', function(e) {
                e.preventDefault();
                window['addMsTag_' + searchId](this.dataset.id, this.dataset.name);
            });
        });
        results.classList.add('open');
    });
    input.addEventListener('blur', () => setTimeout(() => results.classList.remove('open'), 150));
}

function removeMsTag(id, searchId) {
    window['removeMsTag_' + searchId](id);
}

/* ── Edit Course modal ── */
function openEditCourse(id) {
    const d = courseData[id];
    if (!d) return;
    document.getElementById('edit-course-form').action = '/training/courses/' + id;
    document.getElementById('ec-title').value    = d.title;
    document.getElementById(d.type === 'Internal' ? 'ec-type-int' : 'ec-type-ext').checked = true;
    document.getElementById('ec-company').value  = d.company || '';
    if (typeof filterDepartments === 'function') filterDepartments(d.company || '', 'ec-department');
    document.getElementById('ec-department').value = d.department || '';
    document.getElementById('ec-start-date').value = d.start_date || '';
    document.getElementById('ec-end-date').value   = d.end_date || '';
    document.getElementById('ec-venue').value    = d.venue;
    document.getElementById('ec-duration').value = d.duration;
    document.getElementById(d.platform === 'LMS' ? 'ec-platform-lms' : 'ec-platform-hr').checked = true;
    document.getElementById('edit-pic-id').value = d.pic_id || '';
    if (d.pic_id && allUsersData) {
        const pic = allUsersData.find(u => u.id == d.pic_id);
        if (pic) {
            document.getElementById('edit-pic-search').value = '';
            document.getElementById('edit-pic-clear').style.display = 'none';
            document.getElementById('edit-pic-selected').textContent = pic.name;
            document.getElementById('edit-pic-selected').style.display = 'flex';
        } else {
            if (clearEditPicSearch) clearEditPicSearch();
        }
    } else {
        if (clearEditPicSearch) clearEditPicSearch();
    }
    openModal('edit-course-modal');
}

/* ── Attendance & PIC live-search ── */
@php
$mappedStaff = $allStaff->map(fn($s) => ['id' => $s->id, 'name' => $s->name, 'staff_no' => $s->staff_no])->values()->all();
$mappedCourses = $allCourses->map(function($c) {
    return [
        'id' => $c->id, 'code' => $c->code, 'title' => $c->title, 
        'company' => $c->company, 'department' => $c->department,
        'start_date' => $c->start_date ? \Carbon\Carbon::parse($c->start_date)->format('Y-m-d') : '',
        'end_date' => $c->end_date ? \Carbon\Carbon::parse($c->end_date)->format('Y-m-d') : '',
        'venue' => $c->venue, 'duration' => $c->duration,
        'platform' => $c->platform, 'pic_id' => $c->pic_id, 'training_type' => $c->training_type
    ];
})->values()->all();
$mappedUsers = isset($allUsers) ? $allUsers->map(fn($u) => ['id' => $u->id, 'name' => $u->name])->values()->all() : [];
@endphp

const attStaffData = @json($mappedStaff);
const attCourseData = @json($mappedCourses);
const allUsersData = @json($mappedUsers);

function highlight(text, q) {
    if (!q) return text;
    const i = text.toLowerCase().indexOf(q.toLowerCase());
    if (i === -1) return text;
    return text.slice(0, i) + '<span class="ls-highlight">' + text.slice(i, i + q.length) + '</span>' + text.slice(i + q.length);
}

function buildSearchFn(data, inputId, resultsId, hiddenId, selectedId, clearId, mainFn, subFn, labelFn) {
    const input   = document.getElementById(inputId);
    const results = document.getElementById(resultsId);
    const hidden  = document.getElementById(hiddenId);
    const selected= document.getElementById(selectedId);
    const clearBtn= document.getElementById(clearId);

    function select(item) {
        hidden.value = item.id;
        input.value  = '';
        clearBtn.style.display = 'none';
        results.classList.remove('open');
        selected.textContent = labelFn(item);
        selected.style.display = 'flex';
    }

    function clear() {
        hidden.value = '';
        input.value  = '';
        clearBtn.style.display = 'none';
        results.classList.remove('open');
        selected.style.display = 'none';
    }

    input.addEventListener('input', function () {
        const q = this.value.trim();
        clearBtn.style.display = q ? '' : 'none';
        if (!q) { results.classList.remove('open'); return; }
        const matches = data.filter(d =>
            mainFn(d).toLowerCase().includes(q.toLowerCase()) ||
            subFn(d).toLowerCase().includes(q.toLowerCase())
        ).slice(0, 20);
        if (matches.length === 0) {
            results.innerHTML = '<div class="live-search-empty">No results found</div>';
        } else {
            results.innerHTML = matches.map(d =>
                `<div class="live-search-item" data-id="${d.id}" onclick="void(0)">
                    <span class="ls-main">${highlight(mainFn(d), q)}</span>
                    <span class="ls-sub">${highlight(subFn(d), q)}</span>
                </div>`
            ).join('');
            results.querySelectorAll('.live-search-item').forEach(el => {
                el.addEventListener('mousedown', function (e) {
                    e.preventDefault();
                    const id = parseInt(this.dataset.id);
                    const item = data.find(d => d.id === id);
                    if (item) select(item);
                });
            });
        }
        results.classList.add('open');
        if (hidden.value) { selected.style.display = 'none'; hidden.value = ''; }
    });

    input.addEventListener('blur', function () {
        setTimeout(() => results.classList.remove('open'), 150);
    });

    return clear;
}

let clearStaffSearch, clearCourseSearch, clearCreatePicSearch, clearEditPicSearch;

document.addEventListener('DOMContentLoaded', function () {
    clearStaffSearch = buildSearchFn(
        attStaffData,
        'att-staff-search', 'att-staff-results', 'att-staff-id', 'att-staff-selected', 'att-staff-clear',
        d => d.name, d => d.staff_no, d => d.name + ' (' + d.staff_no + ')'
    );
    clearCourseSearch = buildSearchFn(
        attCourseData,
        'att-course-search', 'att-course-results', 'att-course-id', 'att-course-selected', 'att-course-clear',
        d => d.title, d => d.code, d => '[' + d.code + '] ' + d.title
    );
    clearCreatePicSearch = buildSearchFn(
        allUsersData,
        'create-pic-search', 'create-pic-results', 'create-pic-id', 'create-pic-selected', 'create-pic-clear',
        d => d.name, d => '', d => d.name
    );
    clearEditPicSearch = buildSearchFn(
        allUsersData,
        'edit-pic-search', 'edit-pic-results', 'edit-pic-id', 'edit-pic-selected', 'edit-pic-clear',
        d => d.name, d => '', d => d.name
    );
    buildMultiStaffPicker('create-staff-search', 'create-staff-results', 'create-staff-tags', 'create-staff-hidden');
    buildMultiStaffPicker('edit-staff-search',   'edit-staff-results',   'edit-staff-tags',   'edit-staff-hidden');

    /* reset search fields when modal closes */
    document.querySelector('#attendance-modal .modal-close').addEventListener('click', function () {
        clearStaffSearch(); clearCourseSearch();
    });
    document.querySelector('#attendance-modal .btn-ghost').addEventListener('click', function () {
        clearStaffSearch(); clearCourseSearch();
    });

    if (document.getElementById('course-grid')) {
        const hashParams = new URLSearchParams(window.location.hash.substring(1));
        if (hashParams.has('time_filter')) {
            const tf = hashParams.get('time_filter');
            const filterEl = document.getElementById('course-time-filter');
            if (filterEl) {
                filterEl.value = tf;
                courseTimeFilter = tf;
            }
            // Remove the hash from URL without scrolling or reloading
            history.replaceState(null, null, ' ');
        }
        filterCourseCards();
    }

    /* client-side guard: require both fields before submit */
    document.querySelector('#attendance-modal form').addEventListener('submit', function (e) {
        const sid = document.getElementById('att-staff-id').value;
        const cid = document.getElementById('att-course-id').value;
        if (!sid) {
            e.preventDefault();
            document.getElementById('att-staff-search').focus();
            document.getElementById('att-staff-search').style.outline = '2px solid var(--danger)';
            setTimeout(() => document.getElementById('att-staff-search').style.outline = '', 1500);
        } else if (!cid) {
            e.preventDefault();
            document.getElementById('att-course-search').focus();
            document.getElementById('att-course-search').style.outline = '2px solid var(--danger)';
            setTimeout(() => document.getElementById('att-course-search').style.outline = '', 1500);
        }
    });
});
</script>

{{-- ── QR SCANNER MODAL ─────────────────────────────────────── --}}
<style>
#qr-reader { border-radius:12px; overflow:hidden; }
#qr-reader video { border-radius:12px; }
#qr-reader img { display:none !important; }
#qr-reader__dashboard_section_csr button { display:none !important; }
</style>

<div id="qr-scan-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.65);z-index:9999;align-items:center;justify-content:center;padding:1rem;">
    <div style="background:var(--card,#fff);border-radius:16px;padding:1.5rem;width:100%;max-width:360px;text-align:center;position:relative;">
        <button onclick="closeQrScanner()" style="position:absolute;top:.75rem;right:.75rem;background:none;border:none;cursor:pointer;color:var(--muted,#64748b);padding:.25rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
        <p style="font-size:.72rem;font-weight:700;letter-spacing:.08em;color:var(--muted,#64748b);text-transform:uppercase;margin-bottom:1rem;">Scan Attendance QR</p>
        <div id="qr-reader" style="width:100%;"></div>
        <p id="qr-status" style="margin-top:.85rem;font-size:.82rem;color:var(--muted,#64748b);">Point camera at the QR code on the projector</p>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
(function () {
    var overlay  = document.getElementById('qr-scan-overlay');
    var status   = document.getElementById('qr-status');
    var scanner  = null;

    window.openQrScanner = function () {
        overlay.style.display = 'flex';
        status.textContent = 'Starting camera…';

        scanner = new Html5Qrcode('qr-reader');
        scanner.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: { width: 220, height: 220 } },
            function (decodedText) {
                if (decodedText.indexOf('attendance/verify') !== -1) {
                    status.textContent = 'QR detected! Marking attendance…';
                    closeQrScanner();
                    window.location.href = decodedText;
                }
            },
            function () {}
        ).then(function () {
            status.textContent = 'Point camera at the QR code on the projector';
        }).catch(function (err) {
            status.textContent = 'Camera unavailable. ' + (typeof err === 'string' ? err : (err.message || 'Please allow camera access.'));
        });
    };

    window.closeQrScanner = function () {
        overlay.style.display = 'none';
        if (scanner) {
            scanner.stop().catch(function () {});
            scanner.clear();
            scanner = null;
        }
    };
}());
</script>
@endsection
