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
.qr-btn-primary { background: #6366f1; color: #fff; }
.qr-btn-primary:hover { background: #4f46e5; color: #fff; }
.qr-btn-ghost { background: transparent; color: var(--text-muted, #64748b); border: 1.5px solid var(--border, #e2e8f0); }
.qr-btn-ghost:hover { background: var(--hover, #f1f5f9); }
.qr-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1.25rem;
}
.qr-card {
    background: var(--card-bg, #fff);
    border: 1.5px solid var(--border, #e2e8f0);
    border-radius: 12px;
    padding: 1.25rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: .75rem;
    text-align: center;
}
.qr-card svg { display: block; }
.qr-card-name {
    font-size: .85rem;
    font-weight: 600;
    color: var(--text-primary, #1e293b);
    line-height: 1.3;
}
.qr-card-no {
    font-size: .75rem;
    color: var(--text-muted, #64748b);
}
.qr-badge {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    font-size: .72rem;
    font-weight: 600;
    padding: .2rem .55rem;
    border-radius: 999px;
}
.qr-badge-done { background: #dcfce7; color: #15803d; }
.qr-badge-pending { background: #fef9c3; color: #854d0e; }
.qr-empty {
    padding: 3rem 1rem;
    text-align: center;
    color: var(--text-muted, #64748b);
    font-size: .9rem;
}
@media print {
    .sidebar, .app-topbar, .qr-actions, .qr-btn-ghost { display: none !important; }
    .main-content { margin: 0 !important; padding: 1rem !important; }
    .qr-card { break-inside: avoid; }
}
</style>
@endsection

@section('content')
<div class="qr-page-header">
    <div>
        <h1 class="qr-page-title">QR Attendance — {{ $course->title }}</h1>
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
        <form method="POST" action="{{ route('training.qr.generate', $course->id) }}">
            @csrf
            <button type="submit" class="qr-btn qr-btn-primary">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><path d="M14 14h.01M14 18h.01M18 14h.01M18 18h.01"/></svg>
                Generate QR Codes ({{ $attendances->whereNull('qr_token')->count() }} pending)
            </button>
        </form>
        @else
        <button onclick="window.print()" class="qr-btn qr-btn-primary">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Print QR Codes
        </button>
        @endif
    </div>
</div>

@if(session('success'))
<div style="background:#dcfce7;color:#15803d;padding:.75rem 1rem;border-radius:8px;margin-bottom:1rem;font-size:.85rem;">
    {{ session('success') }}
</div>
@endif

@if($attendances->isEmpty())
<div class="qr-empty">
    <p>No staff registered for this course yet.</p>
    <p>Add attendees via the <a href="{{ route('training.index', ['view' => 'courses']) }}">Courses</a> page first.</p>
</div>
@else
<div class="qr-grid">
    @foreach($attendances as $att)
    <div class="qr-card">
        @if($att->qr_token)
            {!! QrCode::size(160)->generate(route('training.qr.scan', $att->qr_token)) !!}
        @else
            <div style="width:160px;height:160px;display:flex;align-items:center;justify-content:center;background:var(--hover,#f1f5f9);border-radius:8px;color:var(--text-muted,#94a3b8);font-size:.75rem;">
                Not generated
            </div>
        @endif
        <div>
            <div class="qr-card-name">{{ $att->staff->name ?? '—' }}</div>
            <div class="qr-card-no">{{ $att->staff->staff_no ?? '' }}</div>
        </div>
        @if($att->qr_used_at)
            <span class="qr-badge qr-badge-done">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                Scanned {{ $att->qr_used_at->format('d M H:i') }}
            </span>
        @elseif($att->qr_token)
            <span class="qr-badge qr-badge-pending">Pending scan</span>
        @else
            <span class="qr-badge" style="background:#f1f5f9;color:#64748b;">No QR yet</span>
        @endif
    </div>
    @endforeach
</div>
@endif
@endsection
