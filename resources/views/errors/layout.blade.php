<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('code') &mdash; FJB Management System</title>
@include('partials.favicons')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --navy:   #0f223b;
    --navy2:  #1b3a63;
    --blue:   #1a4b8c;
    --sky:    #38bdf8;
    --sky-dk: #0284c7;
  }

  body {
    font-family: 'DM Sans', sans-serif;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    background:
      radial-gradient(circle at 15% 15%, rgba(56,189,248,.15), transparent 40%),
      radial-gradient(circle at 85% 85%, rgba(26,75,140,.3), transparent 45%),
      linear-gradient(135deg, var(--navy) 0%, var(--navy2) 55%, var(--blue) 100%);
    padding: 2rem 1.5rem;
    color: #fff;
  }

  .error-card {
    max-width: 520px;
    width: 100%;
    animation: rise .5s cubic-bezier(.16,.84,.44,1) both;
  }

  @keyframes rise {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: none; }
  }

  .error-logo {
    width: 64px; height: 64px;
    background: rgba(255,255,255,.95);
    border-radius: 20px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 2rem;
    box-shadow: 0 12px 32px rgba(0,0,0,.3);
  }
  .error-logo img { width: 42px; height: 42px; object-fit: contain; }

  .error-code {
    font-size: 6rem;
    font-weight: 800;
    line-height: 1;
    letter-spacing: -.04em;
    background: linear-gradient(135deg, #fff 0%, var(--sky) 100%);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 1rem;
  }

  .error-title {
    font-size: 1.6rem;
    font-weight: 700;
    letter-spacing: -.01em;
    margin-bottom: .75rem;
  }

  .error-message {
    font-size: 1rem;
    line-height: 1.6;
    color: rgba(234,244,255,.7);
    margin-bottom: 2.25rem;
  }

  .error-actions {
    display: flex;
    gap: .75rem;
    justify-content: center;
    flex-wrap: wrap;
  }

  .btn {
    font-family: inherit;
    font-size: .9rem;
    font-weight: 600;
    padding: .8rem 1.6rem;
    border-radius: 12px;
    text-decoration: none;
    cursor: pointer;
    border: 1px solid transparent;
    transition: transform .2s ease, background .2s ease, border-color .2s ease, box-shadow .2s ease;
    display: inline-flex;
    align-items: center;
    gap: .5rem;
  }
  .btn-primary {
    background: linear-gradient(135deg, var(--sky) 0%, var(--sky-dk) 100%);
    color: #fff;
    box-shadow: 0 10px 28px -8px rgba(56,189,248,.6);
  }
  .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 16px 36px -10px rgba(56,189,248,.7);
  }
  .btn-ghost {
    background: rgba(255,255,255,.06);
    border-color: rgba(255,255,255,.18);
    color: #fff;
  }
  .btn-ghost:hover {
    transform: translateY(-2px);
    background: rgba(255,255,255,.12);
    border-color: rgba(56,189,248,.45);
  }

  .error-footer {
    margin-top: 3rem;
    font-size: .75rem;
    color: rgba(255,255,255,.3);
  }

  @media (max-width: 500px) {
    .error-code { font-size: 4.5rem; }
    .error-title { font-size: 1.3rem; }
  }
</style>
</head>
<body>

  <div class="error-card">
    <div class="error-logo">
      <img src="{{ asset('assets/images/logo.png') }}" alt="FJB">
    </div>

    <div class="error-code">@yield('code')</div>
    <h1 class="error-title">@yield('title')</h1>
    <p class="error-message">@yield('message')</p>

    <div class="error-actions">
      <a href="{{ url('/') }}" class="btn btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18" stroke-linecap="round" stroke-linejoin="round">
          <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
        </svg>
        Back to portal
      </a>
      <a href="javascript:history.back()" class="btn btn-ghost">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="18" height="18" stroke-linecap="round" stroke-linejoin="round">
          <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
        </svg>
        Go back
      </a>
    </div>
  </div>

  <div class="error-footer">
    &copy; {{ date('Y') }} FJB Group &mdash; Internal Systems Portal
  </div>

</body>
</html>
