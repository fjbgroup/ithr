<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Mark Attendance — {{ config('app.name', 'HR System') }}</title>
@include('partials.favicons')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script>
    if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.setAttribute('data-theme', 'dark');
    }
</script>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
    --navy:   #0f223b;
    --blue:   #1a4b8c;
    --sky:    #38bdf8;
    --sky-dk: #0284c7;
    --bg-grad-1: #0f223b; --bg-grad-2: #1b3a63; --bg-grad-3: #1a4b8c;
    --card: #ffffff; --border: #e2e8f0; --field-bg: #f8fafc;
    --text: #1e293b; --muted: #64748b; --danger: #dc2626; --success: #16a34a;
}
[data-theme="dark"] {
    --bg-grad-1: #060d18; --bg-grad-2: #0f1d33; --bg-grad-3: #14315a;
    --card: #1e293b; --border: #334155; --field-bg: #0f172a;
    --text: #f1f5f9; --muted: #94a3b8; --danger: #f87171; --success: #4ade80;
}
body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    color: var(--text);
    min-height: 100vh; display: flex; align-items: center;
    justify-content: center; padding: 1.5rem;
    background:
      radial-gradient(circle at 0% 0%, rgba(56,189,248,.18), transparent 42%),
      radial-gradient(circle at 100% 100%, rgba(26,75,140,.35), transparent 45%),
      linear-gradient(135deg, var(--bg-grad-1) 0%, var(--bg-grad-2) 55%, var(--bg-grad-3) 100%);
    background-attachment: fixed;
}
.card {
    background: var(--card); border: 1px solid var(--border);
    border-radius: 22px; padding: 2.5rem 2rem;
    max-width: 400px; width: 100%; text-align: center;
    box-shadow: 0 30px 70px -20px rgba(8,20,40,.55), 0 2px 8px rgba(8,20,40,.2);
    animation: rise .55s cubic-bezier(.16,.84,.44,1) both;
}
@keyframes rise {
    from { opacity: 0; transform: translateY(18px) scale(.985); }
    to   { opacity: 1; transform: none; }
}
.logo { font-size: .72rem; font-weight: 700; letter-spacing: .08em; color: var(--muted); text-transform: uppercase; margin-bottom: 1.5rem; }
.icon {
    width: 60px; height: 60px; border-radius: 16px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 1.25rem;
}
.icon-ok  { background: linear-gradient(135deg, var(--blue), var(--sky-dk)); box-shadow: 0 10px 22px -8px rgba(2,132,199,.6); }
.icon-err { background: #fee2e2; }
[data-theme="dark"] .icon-err { background: #450a0a; }
.title { font-size: 1.3rem; font-weight: 700; margin-bottom: .4rem; letter-spacing: -.01em; }
.sub { font-size: .88rem; color: var(--muted); line-height: 1.55; margin-bottom: 1.5rem; }
.course-name { font-weight: 600; color: var(--text); }
.divider { border: none; border-top: 1px solid var(--border); margin: 1.5rem 0; }
/* form */
.field { text-align: left; margin-bottom: 1.05rem; }
.field label { display: block; font-size: .8rem; font-weight: 600; color: var(--text); margin-bottom: .45rem; }
.input-wrap { position: relative; }
.input-wrap > svg.lead {
    position: absolute; left: .9rem; top: 50%;
    transform: translateY(-50%); color: var(--muted); pointer-events: none;
}
.input-wrap input {
    width: 100%; padding: .8rem 1rem .8rem 2.6rem;
    border: 1.5px solid var(--border); border-radius: 11px;
    background: var(--field-bg); color: var(--text);
    font-size: .95rem; font-family: inherit;
    outline: none; transition: border-color .18s ease, box-shadow .18s ease, background .18s ease;
}
.input-wrap input::placeholder { color: #94a3b8; }
.input-wrap input:focus {
    border-color: var(--sky);
    box-shadow: 0 0 0 4px rgba(56,189,248,.16);
}
.input-wrap input.has-toggle { padding-right: 2.9rem; }
.toggle {
    position: absolute; right: .35rem; top: 50%;
    transform: translateY(-50%);
    background: none; border: none; padding: .5rem; cursor: pointer;
    color: var(--muted); display: flex; align-items: center; border-radius: 8px;
}
.toggle:hover { color: var(--text); }
.err { font-size: .78rem; color: var(--danger); margin-top: .35rem; }
.btn {
    display: flex; align-items: center; justify-content: center; gap: .5rem;
    width: 100%; padding: .9rem;
    border-radius: 11px; border: none;
    background: linear-gradient(135deg, var(--blue), var(--sky-dk)); color: #fff;
    font-size: .95rem; font-weight: 600; cursor: pointer;
    font-family: inherit; margin-top: .35rem;
    box-shadow: 0 10px 22px -8px rgba(2,132,199,.6);
    transition: transform .15s ease, box-shadow .15s ease, filter .15s ease;
}
.btn:hover { filter: brightness(1.06); box-shadow: 0 14px 26px -8px rgba(2,132,199,.7); }
.btn:active { transform: translateY(1px); }
.btn-ghost {
    display: block; width: 100%; padding: .8rem;
    border-radius: 11px; border: 1.5px solid var(--border);
    background: transparent; color: var(--muted);
    font-size: .9rem; font-weight: 600; cursor: pointer;
    font-family: inherit; text-decoration: none; text-align: center;
    transition: border-color .15s, color .15s; margin-top: .65rem;
}
.btn-ghost:hover { color: var(--text); border-color: var(--muted); }
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

    @elseif(!empty($inactive))
        {{-- ── Inactive staff state ── --}}
        <div class="icon icon-err">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
        </div>
        <h1 class="title">Account Inactive</h1>
        <p class="sub">
            Your account is currently <strong>inactive</strong>.<br>
            You cannot mark attendance for
            <strong class="course-name">{{ $course->title }}</strong>.<br>
            Please contact HR for assistance.
        </p>
        <a href="{{ route('dashboard') }}" class="btn-ghost">Back to Dashboard</a>

    @else
        {{-- ── Login form ── --}}
        <div class="icon icon-ok">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.5">
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
                <div class="input-wrap">
                    <svg class="lead" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <input id="staff_no" name="staff_no" type="text" autocomplete="username"
                           autocapitalize="none" autocorrect="off" spellcheck="false"
                           value="{{ old('staff_no') }}" placeholder="e.g. EMP001" autofocus>
                </div>
                @error('staff_no')
                    <div class="err">{{ $message }}</div>
                @enderror
            </div>
            <div class="field">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <svg class="lead" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    <input id="password" name="password" type="password" class="has-toggle" autocomplete="current-password" placeholder="********">
                    <button type="button" class="toggle" aria-label="Show password" onclick="togglePassword(this)">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn">
                Mark My Attendance
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="20 6 9 17 4 12"/></svg>
            </button>
        </form>
    @endif
</div>

<script>
  function togglePassword(btn) {
    const input = btn.parentElement.querySelector('input');
    const showing = input.type === 'text';
    input.type = showing ? 'password' : 'text';
    btn.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
    btn.innerHTML = showing
      ? '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>'
      : '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>';
  }
</script>

</body>
</html>
