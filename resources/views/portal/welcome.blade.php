<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>FJB Management System</title>
@include('partials.favicons')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --navy:     #0f223b;
    --navy2:    #1b3a63;
    --blue:     #1a4b8c;
    --sky:      #38bdf8;
    --sky-dk:   #0284c7;
    --text:     #1e293b;
    --muted:    #64748b;
    --border:   #e2e8f0;
  }

  body {
    font-family: 'Inter', sans-serif;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background:
      radial-gradient(circle at 15% 15%, rgba(56,189,248,.15), transparent 40%),
      radial-gradient(circle at 85% 85%, rgba(26,75,140,.3), transparent 45%),
      linear-gradient(135deg, var(--navy) 0%, var(--navy2) 55%, var(--blue) 100%);
    padding: 2rem 1.5rem;
  }

  .portal-header {
    text-align: center;
    margin-bottom: 3.5rem;
    animation: rise .5s cubic-bezier(.16,.84,.44,1) both;
  }
  .portal-logo {
    width: 72px; height: 72px;
    background: rgba(255,255,255,.95);
    border-radius: 22px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 1.5rem;
    box-shadow: 0 12px 32px rgba(0,0,0,.3);
  }
  .portal-logo img { width: 48px; height: 48px; object-fit: contain; }
  .portal-header h1 {
    font-size: 2.2rem;
    font-weight: 800;
    color: #fff;
    letter-spacing: -.02em;
  }
  .portal-header p {
    margin-top: .6rem;
    color: rgba(234,244,255,.7);
    font-size: 1rem;
  }

  .portal-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
    width: 100%;
    max-width: 960px;
    animation: rise .55s .1s cubic-bezier(.16,.84,.44,1) both;
  }

  @keyframes rise {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: none; }
  }

  .module-card {
    background: rgba(255,255,255,.06);
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 20px;
    padding: 2.25rem 1.75rem 2rem;
    text-decoration: none;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
    transition: transform .2s ease, background .2s ease, border-color .2s ease, box-shadow .2s ease;
    backdrop-filter: blur(6px);
    cursor: pointer;
  }
  .module-card:hover {
    transform: translateY(-5px);
    background: rgba(255,255,255,.1);
    border-color: rgba(56,189,248,.45);
    box-shadow: 0 20px 50px -10px rgba(0,0,0,.4), 0 0 0 1px rgba(56,189,248,.2);
  }
  .module-card:active { transform: translateY(-2px); }

  .module-icon {
    width: 54px; height: 54px;
    border-radius: 16px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
  }
  .module-icon svg { width: 26px; height: 26px; }

  .icon-wt   { background: rgba(251,191,36,.18);  color: #fbbf24; }
  .icon-it   { background: rgba(56,189,248,.18);  color: #38bdf8; }
  .icon-hr   { background: rgba(74,222,128,.18);  color: #4ade80; }

  .module-body h2 {
    font-size: 1.2rem;
    font-weight: 700;
    color: #fff;
    letter-spacing: -.01em;
  }
  .module-body p {
    margin-top: .35rem;
    font-size: .875rem;
    color: rgba(234,244,255,.65);
    line-height: 1.5;
  }

  .module-features {
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: .45rem;
    margin-top: .25rem;
  }
  .module-features li {
    font-size: .8rem;
    color: rgba(234,244,255,.55);
    display: flex;
    align-items: center;
    gap: .45rem;
  }
  .module-features li::before {
    content: '';
    width: 5px; height: 5px;
    border-radius: 50%;
    background: currentColor;
    flex-shrink: 0;
    opacity: .7;
  }

  .module-arrow {
    margin-top: auto;
    align-self: flex-end;
    color: rgba(255,255,255,.3);
    transition: color .2s, transform .2s;
  }
  .module-card:hover .module-arrow {
    color: var(--sky);
    transform: translateX(4px);
  }

  .portal-footer {
    margin-top: 3rem;
    font-size: .8rem;
    color: rgba(255,255,255,.3);
    text-align: center;
    animation: rise .6s .2s cubic-bezier(.16,.84,.44,1) both;
  }

  @media (max-width: 780px) {
    .portal-grid { grid-template-columns: 1fr; max-width: 420px; }
    .portal-header h1 { font-size: 1.75rem; }
  }
  @media (max-width: 500px) {
    .portal-header h1 { font-size: 1.5rem; }
  }
</style>
</head>
<body>

  <div class="portal-header">
    <div class="portal-logo">
      <img src="{{ asset('assets/images/logo.png') }}" alt="FJB">
    </div>
    <h1>FJB Management System</h1>
    <p>Select a module to continue</p>
  </div>

  <div class="portal-grid">

    {{-- Walkie Talkie --}}
    <a href="{{ route('wt.login') }}" class="module-card">
      <div class="module-icon icon-wt">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M14.5 2h-5L8 7h8l-1.5-5z"/>
          <rect x="8" y="7" width="8" height="14" rx="2"/>
          <line x1="12" y1="10" x2="12" y2="13"/>
          <circle cx="12" cy="16" r="1"/>
        </svg>
      </div>
      <div class="module-body">
        <h2>Walkie Talkie</h2>
        <p>Manage walkie talkie inventory, borrowing requests, and maintenance.</p>
        <ul class="module-features">
          <li>Device inventory & tracking</li>
          <li>Borrow / return requests</li>
          <li>Maintenance & fault reports</li>
        </ul>
      </div>
      <svg class="module-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="20" height="20">
        <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
      </svg>
    </a>

    {{-- IT Management --}}
    <a href="{{ route('it.login') }}" class="module-card">
      <div class="module-icon icon-it">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="2" y="3" width="20" height="14" rx="2"/>
          <line x1="8" y1="21" x2="16" y2="21"/>
          <line x1="12" y1="17" x2="12" y2="21"/>
        </svg>
      </div>
      <div class="module-body">
        <h2>IT Management</h2>
        <p>Track IT and non-IT assets, handle write-offs, e-waste and disposal.</p>
        <ul class="module-features">
          <li>IT & non-IT asset inventory</li>
          <li>Write-off approval workflow</li>
          <li>E-waste & disposal tracking</li>
        </ul>
      </div>
      <svg class="module-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="20" height="20">
        <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
      </svg>
    </a>

    {{-- HR Admin --}}
    <a href="{{ route('hr.home') }}" class="module-card">
      <div class="module-icon icon-hr">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
          <circle cx="9" cy="7" r="4"/>
          <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
          <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
      </div>
      <div class="module-body">
        <h2>HR Admin</h2>
        <p>Manage staff records, training, meeting room bookings, and travel.</p>
        <ul class="module-features">
          <li>Staff & family records</li>
          <li>Training & attendance</li>
          <li>Meeting room booking</li>
        </ul>
      </div>
      <svg class="module-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" width="20" height="20">
        <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
      </svg>
    </a>

  </div>

  <div class="portal-footer" style="margin-top: 3rem; margin-bottom: 2rem;">
    <div style="margin-bottom: 0.5rem;">
        <img src="{{ asset('assets/images/footer.jpg') }}" alt="IT Logo" style="max-height: 45px; width: auto; object-fit: contain;">
    </div>
    <div style="font-size: 0.85rem; color: rgba(255,255,255,.5); font-weight: 500; margin-bottom: 0.5rem;">
        Develop by IT team
    </div>
    <div style="font-size: 0.75rem; color: rgba(255,255,255,.3);">
        &copy; {{ date('Y') }} FJB Group &mdash; Internal Systems Portal
    </div>
  </div>

</body>
</html>
