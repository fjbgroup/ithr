<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Two-Factor Verification — {{ config('app.name', 'HR Admin System') }}</title>
@include('partials.favicons')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
<script>!function(){var t=localStorage.getItem('fjb-theme')||localStorage.getItem('color-theme')||localStorage.getItem('theme');if(t==='dark')document.documentElement.classList.add('dark');}();</script>
<style>
  :root {
    --lg-navy:   #0f223b;
    --lg-blue:   #1a4b8c;
    --lg-sky:    #38bdf8;
    --lg-sky-dk: #0284c7;
    --lg-border: #e2e8f0;
    --lg-text:   #1e293b;
    --lg-muted:  #64748b;
    --lg-card:   #fff;
    --lg-input:  #f8fafc;
    --lg-input-focus: #fff;
  }
  html.dark {
    --lg-border: #334155;
    --lg-text:   #e2e8f0;
    --lg-muted:  #94a3b8;
    --lg-card:   #1e293b;
    --lg-input:  #0f172a;
    --lg-input-focus: #1e293b;
  }
  .auth-page {
    min-height: 100vh;
    display: flex; align-items: center; justify-content: center;
    padding: 1.25rem;
    background: linear-gradient(135deg, #f1f3f5 0%, #e9ecef 55%, #dee2e6 100%);
    background-attachment: fixed;
  }
  .auth-card {
    width: 100%; max-width: 420px;
    background: var(--lg-card);
    border-radius: 22px;
    padding: 2.75rem 2.5rem;
    box-shadow: 0 30px 70px -20px rgba(8,20,40,.55), 0 2px 8px rgba(8,20,40,.2);
    animation: auth-rise .55s cubic-bezier(.16,.84,.44,1) both;
  }
  @keyframes auth-rise {
    from { opacity: 0; transform: translateY(18px) scale(.985); }
    to   { opacity: 1; transform: none; }
  }
  .tfa-icon {
    width: 58px; height: 58px;
    border-radius: 16px;
    margin: 0 auto 1.25rem;
    display: flex; align-items: center; justify-content: center;
    color: #fff;
    background: linear-gradient(135deg, var(--lg-blue), var(--lg-sky-dk));
    box-shadow: 0 12px 24px -10px rgba(2,132,199,.7);
  }
  .auth-card h1 {
    font-size: 1.45rem; font-weight: 700; color: var(--lg-text);
    text-align: center; letter-spacing: -.01em;
  }
  .auth-card .sub {
    margin-top: .45rem; margin-bottom: 1.75rem;
    color: var(--lg-muted); font-size: .9rem; text-align: center; line-height: 1.5;
  }
  .auth-field { margin-bottom: 1.25rem; }
  .auth-field label {
    display: block; font-size: .8rem; font-weight: 600;
    color: var(--lg-text); margin-bottom: .45rem;
  }
  .otp-input {
    width: 100%;
    padding: .85rem 1rem;
    font-family: inherit;
    font-size: 1.5rem; font-weight: 600;
    text-align: center; letter-spacing: .5em;
    color: var(--lg-text);
    background: var(--lg-input);
    border: 1.5px solid var(--lg-border);
    border-radius: 11px;
    transition: border-color .18s ease, box-shadow .18s ease, background .18s ease;
  }
  .otp-input::placeholder { letter-spacing: .3em; color: #cbd5e1; }
  .otp-input:focus {
    outline: none; background: var(--lg-input-focus);
    border-color: var(--lg-sky);
    box-shadow: 0 0 0 4px rgba(56,189,248,.16);
  }
  .auth-submit {
    width: 100%; padding: .85rem 1rem;
    font-family: inherit; font-size: .95rem; font-weight: 600; color: #fff;
    background: linear-gradient(135deg, var(--lg-blue), var(--lg-sky-dk));
    border: none; border-radius: 11px; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: .5rem;
    box-shadow: 0 10px 22px -8px rgba(2,132,199,.6);
    transition: transform .15s ease, box-shadow .15s ease, filter .15s ease;
  }
  .auth-submit:hover { filter: brightness(1.06); box-shadow: 0 14px 26px -8px rgba(2,132,199,.7); }
  .auth-submit:active { transform: translateY(1px); }
  .auth-alert {
    display: flex; align-items: flex-start; gap: .55rem;
    padding: .8rem .9rem; border-radius: 11px;
    font-size: .85rem; margin-bottom: 1.25rem; line-height: 1.45;
  }
  .auth-alert svg { flex-shrink: 0; margin-top: 1px; }
  .auth-alert-error { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
  .tfa-back { text-align: center; margin-top: 1.5rem; }
  .tfa-back a {
    font-size: .82rem; color: #94a3b8; text-decoration: none;
  }
  .tfa-back a:hover { color: var(--lg-sky-dk); }
</style>
</head>
<body class="auth-page">
  <div class="auth-card">
    <div class="tfa-icon">
      <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
    </div>

    <h1>Two-Step Verification</h1>
    <p class="sub">
      @if(!empty($userName))
        Hi <strong>{{ explode(' ', $userName)[0] }}</strong> — open
      @else
        Open
      @endif
      your <strong>Microsoft Authenticator</strong> app and enter the 6-digit code to continue.
    </p>

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

    <form method="POST" action="{{ route('login.2fa.verify') }}">
      @csrf
      <div class="auth-field">
        <label for="otp">Authentication Code</label>
        <input type="text" id="otp" name="otp" class="otp-input"
               inputmode="numeric" autocomplete="one-time-code"
               maxlength="6" pattern="[0-9]*" placeholder="000000"
               required autofocus
               autocapitalize="none" autocorrect="off" spellcheck="false">
      </div>

      <button type="submit" class="auth-submit">
        Verify &amp; Sign In
        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
      </button>
    </form>

    <div class="tfa-back">
      <a href="{{ route('login') }}">&larr; Back to Sign in</a>
    </div>
  </div>

<script>
  // Keep only digits and auto-submit once 6 digits are entered.
  // Guard against double-submission: the first request regenerates the session
  // (new CSRF token), so a second concurrent POST would get a 419 Page Expired.
  (function () {
    var input = document.getElementById('otp');
    if (!input) return;
    var submitted = false;
    var form = input.form;

    // Block any subsequent submit events (e.g. Enter key, button click) once
    // the form has already been submitted programmatically.
    form.addEventListener('submit', function (e) {
      if (submitted) { e.preventDefault(); return; }
      submitted = true;
    });

    input.addEventListener('input', function () {
      this.value = this.value.replace(/\D/g, '').slice(0, 6);
      if (this.value.length === 6 && !submitted) {
        submitted = true;
        // Disable the button so the user can't click it while navigating away.
        var btn = form.querySelector('button[type="submit"]');
        if (btn) { btn.disabled = true; btn.style.opacity = '0.6'; }
        form.submit();
      }
    });
  })();
</script>
</body>
</html>
