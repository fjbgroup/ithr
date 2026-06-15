<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Attendance Marked — {{ config('app.name', 'HR System') }}</title>
<script>
    if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.setAttribute('data-theme', 'dark');
    }
</script>
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
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    background: var(--bg); color: var(--text);
    min-height: 100vh; display: flex; align-items: center;
    justify-content: center; padding: 1.5rem;
}
.card {
    background: var(--card); border: 1.5px solid var(--border);
    border-radius: 16px; padding: 2rem 1.75rem;
    max-width: 380px; width: 100%; text-align: center;
}
.logo { font-size: .72rem; font-weight: 700; letter-spacing: .08em; color: var(--muted); text-transform: uppercase; margin-bottom: 1.5rem; }
.icon {
    width: 64px; height: 64px; border-radius: 18px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 1.25rem; background: #d1fae5;
}
[data-theme="dark"] .icon { background: #064e3b; }
.title { font-size: 1.2rem; font-weight: 700; margin-bottom: .5rem; }
.message {
    font-size: .9rem; color: var(--muted); line-height: 1.6;
    margin-bottom: 1.75rem;
}
.message strong { color: var(--text); }
.divider { border: none; border-top: 1.5px solid var(--border); margin-bottom: 1.75rem; }
.detail-row {
    display: flex; justify-content: space-between; align-items: center;
    font-size: .82rem; padding: .4rem 0;
}
.detail-label { color: var(--muted); }
.detail-val { font-weight: 600; text-align: right; max-width: 60%; }
.detail-block { margin-bottom: 1.75rem; }
.btn {
    display: block; width: 100%; padding: .85rem;
    border-radius: 10px; border: none;
    background: var(--accent); color: #fff;
    font-size: .95rem; font-weight: 700; cursor: pointer;
    font-family: inherit; text-decoration: none; text-align: center;
    transition: opacity .15s;
}
.btn:active { opacity: .85; }
</style>
</head>
<body>

<div class="card">
    <div class="logo">{{ config('app.name', 'HR System') }}</div>

    <div class="icon">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
            <polyline points="22 4 12 14.01 9 11.01"/>
        </svg>
    </div>

    <h1 class="title">Attendance Marked!</h1>
    <p class="message">
        Attendance marked successfully for<br>
        <strong>{{ $course->title }}</strong><br>
        as <strong>{{ $staffName }}</strong>.
    </p>

    <hr class="divider">

    <div class="detail-block">
        @if($course->start_date)
        <div class="detail-row">
            <span class="detail-label">Date</span>
            <span class="detail-val">{{ \Carbon\Carbon::parse($course->start_date)->format('d M Y') }}</span>
        </div>
        @endif
        @if($course->venue)
        <div class="detail-row">
            <span class="detail-label">Venue</span>
            <span class="detail-val">{{ $course->venue }}</span>
        </div>
        @endif
        <div class="detail-row">
            <span class="detail-label">Status</span>
            <span class="detail-val" style="color:#059669; font-weight:700;">Completed</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Recorded at</span>
            <span class="detail-val">{{ now()->format('d M Y, H:i') }}</span>
        </div>
    </div>

    <a href="{{ route('training.index') }}" class="btn">View My Training</a>
</div>

</body>
</html>
