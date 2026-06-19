@extends('layouts.app')

@section('styles')
<style>
.qr-page-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}
.qr-page-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-primary, #1e293b);
    margin: 0 0 .25rem;
}
.qr-page-meta {
    font-size: .8rem;
    color: var(--text-muted, #64748b);
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}
.qr-actions {
    display: flex;
    gap: .5rem;
    flex-shrink: 0;
    flex-wrap: wrap;
}
.qr-btn {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    padding: .45rem .9rem;
    border-radius: 8px;
    font-size: .82rem;
    font-weight: 600;
    cursor: pointer;
    border: none;
    text-decoration: none;
}
.qr-btn-primary  { background: #6366f1; color: #fff; }
.qr-btn-primary:hover  { background: #4f46e5; color: #fff; }
.qr-btn-success  { background: #22c55e; color: #fff; }
.qr-btn-success:hover  { background: #16a34a; color: #fff; }
.qr-btn-ghost { background: transparent; color: var(--text-muted, #64748b); border: 1.5px solid var(--border, #e2e8f0); }
.qr-btn-ghost:hover { background: var(--hover, #f1f5f9); }
/* ── Attendee list ── */
.att-card {
    background: var(--card-bg, #fff);
    border: 1.5px solid var(--border, #e2e8f0);
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 2rem;
}
.att-card-head {
    padding: .75rem 1.25rem;
    border-bottom: 1px solid var(--border, #e2e8f0);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
}
.att-card-title {
    font-size: .88rem;
    font-weight: 700;
    color: var(--text-primary, #1e293b);
    margin: 0;
}
.att-summary {
    display: flex;
    gap: 1.25rem;
    flex-wrap: wrap;
}
.att-stat {
    font-size: .75rem;
    font-weight: 600;
    color: var(--text-muted, #64748b);
}
.att-stat span {
    font-weight: 800;
    color: var(--text-primary, #1e293b);
}
.att-empty {
    padding: 3rem 1rem;
    text-align: center;
    color: var(--text-muted, #64748b);
    font-size: .9rem;
}
.att-badge {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    font-size: .72rem;
    font-weight: 600;
    padding: .2rem .55rem;
    border-radius: 999px;
    white-space: nowrap;
}
.att-badge-done    { background: #dcfce7; color: #15803d; }
.att-badge-pending { background: #fef9c3; color: #854d0e; }
.att-badge-none    { background: #f1f5f9; color: #64748b; }
/* ── Feedback panel ── */
.fb-panel {
    background: var(--card-bg, #fff);
    border: 1.5px solid var(--border, #e2e8f0);
    border-radius: 12px;
    padding: 1.5rem;
}
.fb-panel-head {
    display: flex; align-items: baseline; justify-content: space-between;
    gap: 1rem; flex-wrap: wrap; margin-bottom: 1.25rem;
}
.fb-panel-title { font-size: 1.05rem; font-weight: 700; color: var(--text-primary, #1e293b); margin: 0; }
.fb-panel-sub { font-size: .78rem; color: var(--text-muted, #64748b); }
.fb-metrics {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem; margin-bottom: 1.5rem;
}
.fb-metric {
    border: 1.5px solid var(--border, #e2e8f0);
    border-radius: 10px; padding: .9rem 1rem;
}
.fb-metric-label { font-size: .72rem; font-weight: 600; color: var(--text-muted, #64748b); text-transform: uppercase; letter-spacing: .04em; margin-bottom: .4rem; }
.fb-metric-val { font-size: 1.4rem; font-weight: 700; color: var(--text-primary, #1e293b); display: flex; align-items: baseline; gap: .35rem; }
.fb-metric-val small { font-size: .8rem; font-weight: 600; color: var(--text-muted, #64748b); }
.fb-stars { font-size: .9rem; letter-spacing: .05em; }
.fb-stars .on { color: #f59e0b; }
.fb-stars .off { color: var(--border, #cbd5e1); }
.fb-comments-title { font-size: .8rem; font-weight: 700; color: var(--text-muted, #64748b); text-transform: uppercase; letter-spacing: .04em; margin: 0 0 .75rem; }
.fb-comment {
    border-left: 3px solid var(--border, #e2e8f0);
    padding: .5rem 0 .5rem .85rem; margin-bottom: .65rem;
    font-size: .88rem; color: var(--text-primary, #334155); line-height: 1.5;
}
.fb-empty { padding: 2rem 1rem; text-align: center; color: var(--text-muted, #64748b); font-size: .88rem; }
</style>
@endsection

@section('content')
<div class="qr-page-header">
    <div>
        <h1 class="qr-page-title">Attendees — {{ $course->title }}</h1>
        <div class="qr-page-meta">
            <span>{{ $course->code }}</span>
            <span>
                @if($course->start_date)
                    @if($course->end_date && $course->end_date !== $course->start_date)
                        {{ \Carbon\Carbon::parse($course->start_date)->format('d M') }} – {{ \Carbon\Carbon::parse($course->end_date)->format('d M Y') }}
                    @else
                        {{ \Carbon\Carbon::parse($course->start_date)->format('d M Y') }}
                    @endif
                @else
                    Date TBD
                @endif
            </span>
            <span>{{ $course->venue ?: 'Venue TBD' }}</span>
            <span>{{ $attendances->count() }} registered</span>
        </div>
    </div>
    <div class="qr-actions">
        <a href="{{ route('training.index', ['view' => 'courses']) }}" class="qr-btn qr-btn-ghost">
            ← Back
        </a>
        @if($attendances->whereNull('qr_token')->count() > 0)
        <form method="POST" action="{{ route('training.qr.generate', $course->id) }}" style="display:contents;">
            @csrf
            <button type="submit" class="qr-btn qr-btn-ghost">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><path d="M14 14h.01M14 18h.01M18 14h.01M18 18h.01"/></svg>
                Generate QR ({{ $attendances->whereNull('qr_token')->count() }} pending)
            </button>
        </form>
        @endif
        <a href="{{ route('training.course.export', $course->id) }}" class="qr-btn qr-btn-success">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Generate Report
        </a>
    </div>
</div>

@if(session('success'))
<div style="background:#dcfce7;color:#15803d;padding:.75rem 1rem;border-radius:8px;margin-bottom:1rem;font-size:.85rem;">
    {{ session('success') }}
</div>
@endif

{{-- ── Attendee List ── --}}
<div class="att-card">
    <div class="att-card-head">
        <h2 class="att-card-title">Registered Attendees</h2>
        @php
            $scannedCount = $attendances->whereNotNull('qr_used_at')->count();
            $pendingCount = $attendances->whereNotNull('qr_token')->whereNull('qr_used_at')->count();
            $noQrCount    = $attendances->whereNull('qr_token')->count();
        @endphp
        <div class="att-summary">
            <div class="att-stat">Total: <span>{{ $attendances->count() }}</span></div>
            <div class="att-stat">Scanned: <span style="color:#15803d;">{{ $scannedCount }}</span></div>
            <div class="att-stat">Pending: <span style="color:#854d0e;">{{ $pendingCount }}</span></div>
            @if($noQrCount > 0)
            <div class="att-stat">No QR: <span style="color:#64748b;">{{ $noQrCount }}</span></div>
            @endif
        </div>
    </div>

    @if($attendances->isEmpty())
    <div class="att-empty">
        <p>No staff registered for this course yet.</p>
        <p>Add attendees via the <a href="{{ route('training.index', ['view' => 'courses']) }}">Courses</a> page first.</p>
    </div>
    @else
    <div style="overflow-x:auto;">
        <table class="table table-compact" style="margin:0;">
            <thead>
                <tr>
                    <th style="width:2.5rem;">#</th>
                    <th>Name</th>
                    <th>Staff No</th>
                    <th>Department</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:center;">QR / Attendance</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendances as $i => $att)
                <tr>
                    <td style="color:var(--text-muted,#94a3b8);font-size:.73rem;font-weight:600;">{{ $i + 1 }}</td>
                    <td>
                        @php
                            $initials = collect(explode(' ', $att->staff->name ?? ''))->take(2)->map(fn($w) => strtoupper($w[0] ?? ''))->implode('');
                        @endphp
                        <div style="display:flex;align-items:center;gap:.6rem;">
                            <div style="width:30px;height:30px;border-radius:50%;background:#eef2ff;color:#6366f1;font-size:.65rem;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;">{{ $initials }}</div>
                            <span style="font-weight:700;color:var(--text-primary,#1e293b);">{{ $att->staff->name ?? '—' }}</span>
                        </div>
                    </td>
                    <td style="font-size:.78rem;color:var(--text-muted,#64748b);">{{ $att->staff->staff_no ?? '—' }}</td>
                    <td style="font-size:.78rem;color:var(--text-muted,#64748b);">{{ $att->staff->department->name ?? '—' }}</td>
                    <td style="text-align:center;">
                        <span class="status-badge {{ $att->status === 'Completed' ? 'status-completed' : ($att->status === 'In Progress' ? 'status-in-progress' : 'status-scheduled') }}">
                            {{ $att->status }}
                        </span>
                    </td>
                    <td style="text-align:center;">
                        @if($att->qr_used_at)
                            <span class="att-badge att-badge-done">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                Scanned {{ $att->qr_used_at->format('d M H:i') }}
                            </span>
                        @elseif($att->qr_token)
                            <span class="att-badge att-badge-pending">Pending scan</span>
                        @else
                            <span class="att-badge att-badge-none">No QR</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- ── Anonymous training feedback summary ── --}}
@php
    $renderStars = function ($val) {
        $rounded = (int) round($val);
        $out = '';
        for ($i = 1; $i <= 5; $i++) {
            $out .= '<span class="' . ($i <= $rounded ? 'on' : 'off') . '">★</span>';
        }
        return $out;
    };
@endphp
<div class="fb-panel">
    <div class="fb-panel-head">
        <div>
            <h2 class="fb-panel-title">Participant Feedback</h2>
            <div class="fb-panel-sub">Anonymous — individual responses are not identifiable.</div>
        </div>
        @if($feedbackStats)
        <div class="fb-panel-sub">
            {{ $feedbackStats['count'] }} of {{ $feedbackStats['scanned'] }} attendees responded
        </div>
        @endif
    </div>

    @if(!$feedbackStats)
        <div class="fb-empty">No feedback submitted yet.</div>
    @else
        <div class="fb-metrics">
            @foreach([
                'overall' => 'Overall experience',
                'content' => 'Course content',
                'trainer' => 'Trainer',
                'venue'   => 'Venue & facilities',
            ] as $key => $label)
            <div class="fb-metric">
                <div class="fb-metric-label">{{ $label }}</div>
                <div class="fb-metric-val">
                    {{ number_format($feedbackStats[$key], 1) }} <small>/ 5</small>
                </div>
                <div class="fb-stars">{!! $renderStars($feedbackStats[$key]) !!}</div>
            </div>
            @endforeach

            <div class="fb-metric">
                <div class="fb-metric-label">Would recommend</div>
                <div class="fb-metric-val">
                    @if($feedbackStats['recommend_pct'] !== null)
                        {{ $feedbackStats['recommend_pct'] }}<small>%</small>
                    @else
                        <small>No data</small>
                    @endif
                </div>
            </div>
        </div>

        @if($feedbackStats['comments']->isNotEmpty())
        <div>
            <h3 class="fb-comments-title">Comments ({{ $feedbackStats['comments']->count() }})</h3>
            @foreach($feedbackStats['comments'] as $comment)
                <div class="fb-comment">{{ $comment }}</div>
            @endforeach
        </div>
        @endif
    @endif
</div>
@endsection
