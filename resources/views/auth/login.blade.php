<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — {{ config('app.name', 'HR Admin System') }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
<style>
  :root {
    --lg-navy:   #0f223b;
    --lg-navy-2: #1b3a63;
    --lg-blue:   #1a4b8c;
    --lg-sky:    #38bdf8;
    --lg-sky-dk: #0284c7;
    --lg-border: #e2e8f0;
    --lg-text:   #1e293b;
    --lg-muted:  #64748b;
  }

  .auth-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1.25rem;
    background: linear-gradient(135deg, #f1f3f5 0%, #e9ecef 55%, #dee2e6 100%);
    background-attachment: fixed;
  }

  .auth-shell {
    width: 100%;
    max-width: 940px;
    display: grid;
    grid-template-columns: 1.05fr .95fr;
    background: #fff;
    border-radius: 22px;
    overflow: hidden;
    box-shadow: 0 30px 70px -20px rgba(8,20,40,.55), 0 2px 8px rgba(8,20,40,.2);
    animation: auth-rise .55s cubic-bezier(.16,.84,.44,1) both;
  }

  @keyframes auth-rise {
    from { opacity: 0; transform: translateY(18px) scale(.985); }
    to   { opacity: 1; transform: none; }
  }

  /* ---------- Showcase panel ---------- */
  .auth-aside {
    position: relative;
    padding: 3rem 2.75rem;
    color: #eaf4ff;
    background:
      radial-gradient(circle at 85% 15%, rgba(56,189,248,.35), transparent 50%),
      linear-gradient(155deg, var(--lg-navy) 0%, var(--lg-blue) 100%);
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }
  .auth-aside::after {
    content: "";
    position: absolute;
    width: 320px; height: 320px;
    right: -120px; bottom: -120px;
    background: radial-gradient(circle, rgba(56,189,248,.28), transparent 65%);
    border-radius: 50%;
  }
  .auth-aside-logo {
    width: 64px; height: 64px;
    border-radius: 16px;
    background: transparent;
    display: flex; align-items: center; justify-content: center;
  }
  .auth-aside h2 {
    font-size: 1.9rem;
    font-weight: 700;
    line-height: 1.2;
    margin: 2.25rem 0 .85rem;
    letter-spacing: -.01em;
  }
  .auth-aside p {
    font-size: .95rem;
    color: rgba(234,244,255,.78);
    line-height: 1.6;
    max-width: 30ch;
  }
  .auth-features {
    margin-top: auto;
    display: flex;
    flex-direction: column;
    gap: .9rem;
    position: relative;
    z-index: 1;
  }
  .auth-feature {
    display: flex; align-items: center; gap: .7rem;
    font-size: .88rem;
    color: rgba(234,244,255,.9);
  }
  .auth-feature span {
    width: 30px; height: 30px;
    flex-shrink: 0;
    border-radius: 9px;
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.18);
    display: flex; align-items: center; justify-content: center;
    color: var(--lg-sky);
  }

  /* ---------- Form panel ---------- */
  .auth-main {
    padding: 3rem 2.75rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }
  .auth-main-head { margin-bottom: 1.75rem; }
  .auth-main-head h1 {
    font-size: 1.55rem;
    font-weight: 700;
    color: var(--lg-text);
    letter-spacing: -.01em;
  }
  .auth-main-head p {
    margin-top: .4rem;
    color: var(--lg-muted);
    font-size: .9rem;
  }

  .auth-field { margin-bottom: 1.15rem; }
  .auth-field label {
    display: block;
    font-size: .8rem;
    font-weight: 600;
    color: var(--lg-text);
    margin-bottom: .45rem;
  }
  .auth-input-wrap { position: relative; }
  .auth-input-wrap > svg.lead {
    position: absolute;
    left: .9rem; top: 50%;
    transform: translateY(-50%);
    color: var(--lg-muted);
    pointer-events: none;
  }
  .auth-input-wrap input {
    width: 100%;
    padding: .8rem 1rem .8rem 2.6rem;
    font-family: inherit;
    font-size: .95rem;
    color: var(--lg-text);
    background: #f8fafc;
    border: 1.5px solid var(--lg-border);
    border-radius: 11px;
    transition: border-color .18s ease, box-shadow .18s ease, background .18s ease;
  }
  .auth-input-wrap input::placeholder { color: #94a3b8; }
  .auth-input-wrap input:focus {
    outline: none;
    background: #fff;
    border-color: var(--lg-sky);
    box-shadow: 0 0 0 4px rgba(56,189,248,.16);
  }
  .auth-input-wrap input.has-toggle { padding-right: 2.9rem; }
  .auth-toggle {
    position: absolute;
    right: .35rem; top: 50%;
    transform: translateY(-50%);
    background: none; border: none;
    padding: .5rem;
    cursor: pointer;
    color: var(--lg-muted);
    display: flex; align-items: center;
    border-radius: 8px;
  }
  .auth-toggle:hover { color: var(--lg-text); }

  .auth-row {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    margin: -.25rem 0 1.4rem;
  }
  .auth-row a {
    font-size: .82rem;
    font-weight: 600;
    color: var(--lg-sky-dk);
  }
  .auth-row a:hover { text-decoration: underline; }

  .auth-submit {
    width: 100%;
    padding: .85rem 1rem;
    font-family: inherit;
    font-size: .95rem;
    font-weight: 600;
    color: #fff;
    background: linear-gradient(135deg, var(--lg-blue), var(--lg-sky-dk));
    border: none;
    border-radius: 11px;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: .5rem;
    box-shadow: 0 10px 22px -8px rgba(2,132,199,.6);
    transition: transform .15s ease, box-shadow .15s ease, filter .15s ease;
  }
  .auth-submit:hover { filter: brightness(1.06); box-shadow: 0 14px 26px -8px rgba(2,132,199,.7); }
  .auth-submit:active { transform: translateY(1px); }

  .auth-alert {
    display: flex; align-items: flex-start; gap: .55rem;
    padding: .8rem .9rem;
    border-radius: 11px;
    font-size: .85rem;
    margin-bottom: 1.25rem;
    line-height: 1.45;
  }
  .auth-alert svg { flex-shrink: 0; margin-top: 1px; }
  .auth-alert-error   { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
  .auth-alert-success { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }

  .auth-mobile-brand { display: none; }

  @media (max-width: 800px) {
    .auth-shell { grid-template-columns: 1fr; max-width: 420px; }
    .auth-aside { display: none; }
    .auth-main { padding: 2.5rem 1.75rem; }
    .auth-mobile-brand {
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      margin-bottom: 1.75rem;
    }
    .auth-mobile-brand .auth-aside-logo { margin-bottom: .9rem; }
    .auth-mobile-brand h1 { font-size: 1.4rem; font-weight: 700; color: var(--lg-text); }
    .auth-mobile-brand p { font-size: .85rem; color: var(--lg-muted); margin-top: .25rem; }
    .auth-main-head { display: none; }
  }
</style>
</head>
<body class="auth-page">
  <div class="auth-shell">

    <!-- Showcase -->
    <aside class="auth-aside">
      <div class="auth-aside-logo">
        <img src="{{ asset('assets/images/logo.png') }}" alt="FJB" style="width:56px;height:56px;object-fit:contain;">
      </div>
      <h2>Welcome to the<br>HR Admin System</h2>
      <p>Manage staff, training, meeting rooms and more — all from one secure workspace.</p>

      <div class="auth-features">
        <div class="auth-feature">
          <span><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg></span>
          Staff &amp; family records
        </div>
        <div class="auth-feature">
          <span><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg></span>
          Training &amp; attendance
        </div>
        <div class="auth-feature">
          <span><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
          Meeting room booking
        </div>
      </div>
    </aside>

    <!-- Form -->
    <main class="auth-main">
      <div class="auth-mobile-brand">
        <div class="auth-aside-logo">
          <img src="{{ asset('assets/images/logo.png') }}" alt="FJB" style="width:56px;height:56px;object-fit:contain;">
        </div>
        <h1>HR Admin System</h1>
        <p>Sign in to your account</p>
      </div>

      <div class="auth-main-head">
        <h1>Sign in</h1>
        <p>Enter your credentials to access your account.</p>
      </div>

      @if (session()->has('pending_booking'))
      @php $pendingSlots = session('pending_booking'); $slotCount = count($pendingSlots); @endphp
      <div class="auth-alert" style="background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe;margin-bottom:1.25rem;">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" flex-shrink="0" style="flex-shrink:0;margin-top:1px;"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <div>
          <strong>Pending booking:</strong>
          {{ $slotCount }} room slot{{ $slotCount > 1 ? 's' : '' }} waiting — sign in to confirm
          @if($slotCount === 1)
            ({{ $pendingSlots[0]['room_name'] ?? '' }}, {{ \Carbon\Carbon::parse($pendingSlots[0]['booking_date'])->format('d M') }}, {{ substr($pendingSlots[0]['start_time'],0,5) }}–{{ substr($pendingSlots[0]['end_time'],0,5) }})
          @endif
        </div>
      </div>
      @endif

      @if ($errors->any())
      <div class="auth-alert auth-alert-error">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <div>
          @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
          @endforeach
        </div>
      </div>
      @endif

      @if (session('status'))
      <div class="auth-alert auth-alert-success">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <div>{{ session('status') }}</div>
      </div>
      @endif

      <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="auth-field">
          <label for="staff_no">Staff ID</label>
          <div class="auth-input-wrap">
            <svg class="lead" xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <input type="text" id="staff_no" name="staff_no" placeholder="e.g. 0000001" required autofocus
                   autocapitalize="none" autocorrect="off" spellcheck="false"
                   autocomplete="username" inputmode="text" value="{{ old('staff_no') }}">
          </div>
        </div>

        <div class="auth-field">
          <label for="password">Password</label>
          <div class="auth-input-wrap">
            <svg class="lead" xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <input type="password" id="password" name="password" class="has-toggle" placeholder="Enter your password" required
                   autocomplete="current-password">
            <button type="button" class="auth-toggle" aria-label="Show password" onclick="togglePassword(this)">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
        </div>

        <div class="auth-row">
          <a href="{{ route('password.request') }}">Forgot password?</a>
        </div>

        <button type="submit" class="auth-submit">
          Sign In
          <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </button>
      </form>

      <div style="text-align:center;margin-top:1.5rem;">
        <a href="{{ url('/') }}" style="font-size:.82rem;color:#94a3b8;text-decoration:none;" onmouseover="this.style.color='#0284c7'" onmouseout="this.style.color='#94a3b8'">
          &larr; Back to Portal
        </a>
      </div>

      <div style="text-align:center;margin-top:.75rem;">
        <div style="font-size:10px;color:#64748b;font-weight:700;text-transform:uppercase;letter-spacing:.08em;margin-bottom:8px;">Switch System</div>
        <div style="display:flex;justify-content:center;gap:6px;flex-wrap:wrap;">
          <span style="display:inline-flex;align-items:center;padding:5px 12px;border-radius:20px;border:1px solid rgba(255,255,255,.08);font-size:11px;font-weight:600;color:#3d4d5c;background:rgba(255,255,255,.03);cursor:default;">
            HR System
          </span>
          <a href="{{ url('/wt') }}" style="display:inline-flex;align-items:center;padding:5px 12px;border-radius:20px;border:1px solid rgba(255,255,255,.15);font-size:11px;font-weight:600;color:#94a3b8;text-decoration:none;background:rgba(255,255,255,.06);transition:all .2s" onmouseover="this.style.background='rgba(255,255,255,.15)';this.style.color='#e2e8f0'" onmouseout="this.style.background='rgba(255,255,255,.06)';this.style.color='#94a3b8'">
            WT System
          </a>
          <a href="{{ url('/it/login') }}" style="display:inline-flex;align-items:center;padding:5px 12px;border-radius:20px;border:1px solid rgba(255,255,255,.15);font-size:11px;font-weight:600;color:#94a3b8;text-decoration:none;background:rgba(255,255,255,.06);transition:all .2s" onmouseover="this.style.background='rgba(255,255,255,.15)';this.style.color='#e2e8f0'" onmouseout="this.style.background='rgba(255,255,255,.06)';this.style.color='#94a3b8'">
            IT System
          </a>
        </div>
      </div>
    </main>

  </div>

<script src="{{ asset('assets/js/app.js') }}"></script>
<script>
  function togglePassword(btn) {
    const input = btn.parentElement.querySelector('input');
    const showing = input.type === 'text';
    input.type = showing ? 'password' : 'text';
    btn.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
    btn.innerHTML = showing
      ? '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>'
      : '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>';
  }
</script>
</body>
</html>
