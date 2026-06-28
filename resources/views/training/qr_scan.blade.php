<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Mark Attendance â€” {{ config('app.name', 'HR System') }}</title>
@include('partials.favicons')
<script>
    if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.setAttribute('data-theme', 'dark');
    }
</script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
    --bg: #f8fafc; --card: #fff; --border: #e2e8f0;
    --text: #1e293b; --muted: #64748b; --accent: #6366f1;
}
[data-theme="dark"] {
    --bg: #0f172a; --card: #1e293b; --border: #334155;
    --text: #f1f5f9; --muted: #94a3b8; --accent: #818cf8;
}
body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1.5rem; }
.scan-card { background: var(--card); border: 1.5px solid var(--border); border-radius: 16px; padding: 2rem 1.75rem; max-width: 400px; width: 100%; text-align: center; }
.scan-logo { font-size: .75rem; font-weight: 700; letter-spacing: .08em; color: var(--muted); text-transform: uppercase; margin-bottom: 1.5rem; }
.scan-icon { width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem; }
.scan-icon-ok { background: #ede9fe; }
.scan-icon-err { background: #fee2e2; }
.scan-icon-warn { background: #fef9c3; }
.scan-title { font-size: 1.15rem; font-weight: 700; margin-bottom: .4rem; }
.scan-sub { font-size: .85rem; color: var(--muted); margin-bottom: 1.5rem; line-height: 1.5; }
.scan-info { background: var(--bg); border: 1.5px solid var(--border); border-radius: 10px; padding: 1rem; text-align: left; margin-bottom: 1.5rem; display: flex; flex-direction: column; gap: .6rem; }
.scan-info-row { display: flex; gap: .6rem; align-items: flex-start; font-size: .82rem; }
.scan-info-label { color: var(--muted); flex-shrink: 0; width: 70px; }
.scan-info-val { font-weight: 600; }
.scan-btn { width: 100%; padding: .85rem; border-radius: 10px; border: none; font-size: .95rem; font-weight: 700; cursor: pointer; font-family: inherit; transition: opacity .15s; }
.scan-btn:active { opacity: .85; }
.scan-btn-primary { background: var(--accent); color: #fff; }
.scan-btn-ghost { background: transparent; border: 1.5px solid var(--border); color: var(--muted); margin-top: .6rem; }
.scan-user-chip { display: inline-flex; align-items: center; gap: .4rem; background: #ede9fe; color: #4f46e5; font-size: .78rem; font-weight: 600; padding: .25rem .65rem; border-radius: 999px; margin-bottom: 1.25rem; }
[data-theme="dark"] .scan-user-chip { background: #312e81; color: #a5b4fc; }
</style>
</head>
<body>

<div class="scan-card">
    <div class="scan-logo">{{ config('app.name', 'HR System') }}</div>

    @if($error === 'used')
        <div class="scan-icon scan-icon-err">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        </div>
        <h1 class="scan-title">Already Used</h1>
        <p class="scan-sub">This QR code was already scanned on <strong>{{ $attendance->qr_used_at?->format('d M Y \a\t H:i') }}</strong>. Each code can only be used once.</p>
        <a href="{{ route('training.index') }}"><button class="scan-btn scan-btn-ghost">Go to My Training</button></a>

    @elseif($error === 'wrong_user')
        <div class="scan-icon scan-icon-warn">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        </div>
        <h1 class="scan-title">Wrong Account</h1>
        <p class="scan-sub">This QR code is assigned to <strong>{{ $attendance->staff->name ?? 'another staff member' }}</strong>. You are currently logged in as a different account.</p>
        <a href="{{ route('training.index') }}"><button class="scan-btn scan-btn-ghost">Go to My Training</button></a>

    @else
        <div class="scan-icon scan-icon-ok">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><path d="M14 14h.01M14 18h.01M18 14h.01M18 18h.01"/></svg>
        </div>
        <h1 class="scan-title">Confirm Attendance</h1>

        <div class="scan-user-chip">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Logged in as {{ Auth::user()->name }}
        </div>

        <div class="scan-info">
            <div class="scan-info-row">
                <span class="scan-info-label">Course</span>
                <span class="scan-info-val">{{ $attendance->course->title }}</span>
            </div>
            <div class="scan-info-row">
                <span class="scan-info-label">Code</span>
                <span class="scan-info-val">{{ $attendance->course->code }}</span>
            </div>
            <div class="scan-info-row">
                <span class="scan-info-label">Date</span>
                <span class="scan-info-val">
                    @if($attendance->course->start_date)
                        @if($attendance->course->end_date && $attendance->course->end_date !== $attendance->course->start_date)
                            {{ \Carbon\Carbon::parse($attendance->course->start_date)->format('d M') }} â€“ {{ \Carbon\Carbon::parse($attendance->course->end_date)->format('d M Y') }}
                        @else
                            {{ \Carbon\Carbon::parse($attendance->course->start_date)->format('d M Y') }}
                        @endif
                    @else
                        TBD
                    @endif
                </span>
            </div>
            <div class="scan-info-row">
                <span class="scan-info-label">Venue</span>
                <span class="scan-info-val">{{ $attendance->course->venue ?: 'TBD' }}</span>
            </div>
        </div>

        <form method="POST" action="{{ route('training.qr.submit', $attendance->qr_token) }}">
            @csrf
            <button type="submit" class="scan-btn scan-btn-primary">Mark My Attendance</button>
        </form>
        <a href="{{ route('training.index') }}"><button class="scan-btn scan-btn-ghost">Cancel</button></a>
    @endif
</div>

</body>
</html>
