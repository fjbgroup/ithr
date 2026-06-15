<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mark Attendance — {{ config('app.name', 'HR System') }}</title>
<script>
    if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.setAttribute('data-theme', 'dark');
    }
</script>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
    --bg: #f8fafc; --card: #fff; --border: #e2e8f0;
    --text: #1e293b; --muted: #64748b; --accent: #6366f1; --danger: #ef4444;
}
[data-theme="dark"] {
    --bg: #0f172a; --card: #1e293b; --border: #334155;
    --text: #f1f5f9; --muted: #94a3b8; --accent: #818cf8; --danger: #f87171;
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
    width: 56px; height: 56px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 1.25rem;
}
.icon-ok  { background: #ede9fe; }
.icon-err { background: #fee2e2; }
[data-theme="dark"] .icon-ok  { background: #2e1065; }
[data-theme="dark"] .icon-err { background: #450a0a; }
.title { font-size: 1.15rem; font-weight: 700; margin-bottom: .35rem; }
.sub { font-size: .85rem; color: var(--muted); line-height: 1.55; margin-bottom: 1.5rem; }
.course-name { font-weight: 600; color: var(--text); }
.divider { border: none; border-top: 1.5px solid var(--border); margin: 1.25rem 0; }
/* form */
.field { text-align: left; margin-bottom: 1rem; }
.field label { display: block; font-size: .78rem; font-weight: 600; color: var(--muted); margin-bottom: .35rem; letter-spacing: .03em; text-transform: uppercase; }
.field input {
    width: 100%; padding: .7rem .85rem;
    border: 1.5px solid var(--border); border-radius: 8px;
    background: var(--bg); color: var(--text);
    font-size: .95rem; font-family: inherit;
    outline: none; transition: border-color .15s;
}
.field input:focus { border-color: var(--accent); }
.field .err { font-size: .78rem; color: var(--danger); margin-top: .3rem; }
.btn {
    display: block; width: 100%; padding: .85rem;
    border-radius: 10px; border: none;
    background: var(--accent); color: #fff;
    font-size: .95rem; font-weight: 700; cursor: pointer;
    font-family: inherit; transition: opacity .15s; margin-top: .25rem;
}
.btn:active { opacity: .85; }
.btn-ghost {
    display: block; width: 100%; padding: .75rem;
    border-radius: 10px; border: 1.5px solid var(--border);
    background: transparent; color: var(--muted);
    font-size: .9rem; font-weight: 600; cursor: pointer;
    font-family: inherit; text-decoration: none; text-align: center;
    transition: opacity .15s; margin-top: .65rem;
}
</style>
</head>
<body>

<div class="card">
    <div class="logo">{{ config('app.name', 'HR System') }}</div>

    @if($expired)
        {{-- ── Expired state ── --}}
        <div class="icon icon-err">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
        </div>
        <h1 class="title">QR Code Expired</h1>
        <p class="sub">
            This QR code is no longer valid.<br>
            Please scan the <strong>live code on the projector</strong> to mark your attendance for
            <strong class="course-name">{{ $course->title }}</strong>.
        </p>
        <a href="{{ route('training.index') }}" class="btn-ghost">Go to My Training</a>

    @else
        {{-- ── Login form ── --}}
        <div class="icon icon-ok">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2.5">
                <rect x="3" y="11" width="18" height="11" rx="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
        </div>
        <h1 class="title">Mark Attendance</h1>
        <p class="sub">Enter your Staff ID and password to record attendance for <strong class="course-name">{{ $course->title }}</strong>.</p>

        <hr class="divider">

        <form method="POST" action="{{ route('attendance.verify.submit', $course->id) }}?token={{ $token }}">
            @csrf
            <div class="field">
                <label for="staff_no">Staff ID</label>
                <input id="staff_no" name="staff_no" type="text" autocomplete="username"
                       value="{{ old('staff_no') }}" placeholder="e.g. EMP001" autofocus>
                @error('staff_no')
                    <div class="err">{{ $message }}</div>
                @enderror
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" autocomplete="current-password" placeholder="••••••••">
            </div>
            <button type="submit" class="btn">Mark My Attendance</button>
        </form>
    @endif
</div>

</body>
</html>
