@extends('it.layouts.auth')

@section('title', 'Forgot Password')

@section('content')
<style>
:root {
  --bg:#ffffff; --surface:#fff; --surface2:#f7f8fa;
  --border:#e2e5ea; --text:#1a1f2e; --muted:#6b7280;
  --accent:#0284c7; --accent-h:#e07d1a; --red:#dc2626; --green:#16a34a;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{min-height:100vh;background:linear-gradient(135deg,#ffffff 0%,#e0f2fe 50%,#bae6fd 100%);font-family:'DM Sans',sans-serif;
  display:flex;align-items:center;justify-content:center;padding:20px;
  position:relative;overflow-x:hidden}
body::before{content:'';position:fixed;inset:0;
  background-image:radial-gradient(circle,rgba(2,132,199,0.08) 1px,transparent 1px);
  background-size:28px 28px;z-index:0;opacity:.6}
body::after{content:'';position:fixed;width:600px;height:600px;
  background:radial-gradient(circle,rgba(2,132,199,.18) 0%,transparent 65%);
  top:-200px;right:-200px;z-index:0;animation:orb 10s ease-in-out infinite alternate}
@keyframes orb{to{transform:translate(-60px,100px)}}

.wrap{position:relative;z-index:1;width:100%;max-width:460px}

.brand{display:flex;align-items:center;gap:12px;justify-content:center;margin-bottom:28px}
.brand img{width:48px;height:48px;object-fit:contain}
.brand-text{font-family:'DM Sans',sans-serif;font-size:13px;font-weight:800;color:#000;
  text-transform:uppercase;letter-spacing:.06em;line-height:1.3}
.brand-text span{color:#000;display:block;font-size:11px;letter-spacing:.1em}

.card{background:var(--surface);border:1px solid var(--border);border-radius:20px;
  overflow:hidden;box-shadow:0 4px 6px rgba(0,0,0,.04),0 20px 40px rgba(0,0,0,.07)}

.back-bar{padding:14px 36px;border-bottom:1px solid var(--border);display:flex;align-items:center}
.back-link{display:inline-flex;align-items:center;gap:7px;font-size:12px;font-weight:600;
  color:var(--muted);text-decoration:none;transition:color .2s;letter-spacing:.02em}
.back-link:hover{color:var(--accent)}
.back-link i{font-size:13px}

.form-body{padding:32px 36px}
.icon-circle{width:52px;height:52px;border-radius:50%;background:rgba(2,132,199,.1);
  border:1.5px solid rgba(2,132,199,.25);display:flex;align-items:center;justify-content:center;
  font-size:22px;color:var(--accent);margin-bottom:18px}
.form-title{font-family:'DM Sans',sans-serif;font-size:22px;font-weight:800;color:var(--text);margin-bottom:4px}
.form-sub{color:var(--muted);font-size:13px;margin-bottom:24px;line-height:1.6}

.info-note{background:rgba(2,132,199,.06);border:1px solid rgba(2,132,199,.2);
  border-radius:10px;padding:13px 16px;display:flex;gap:11px;align-items:flex-start;margin-bottom:22px}
.info-note i{color:var(--accent);font-size:15px;flex-shrink:0;margin-top:1px}
.info-note span{font-size:12.5px;color:var(--muted);line-height:1.6}

.field{margin-bottom:18px}
label{display:block;font-size:11px;font-weight:700;color:var(--muted);
  text-transform:uppercase;letter-spacing:.09em;margin-bottom:7px}
.input-wrap{position:relative;display:flex;align-items:center}
.input-wrap i{position:absolute;left:14px;color:#9ca3af;font-size:15px;pointer-events:none}
input[type="text"]{
  width:100%;background:var(--surface2);border:1.5px solid var(--border);border-radius:10px;
  color:var(--text);padding:13px 14px 13px 42px;font-size:14px;font-family:'DM Sans',sans-serif;
  transition:border-color .2s,box-shadow .2s;outline:none}
input:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(2,132,199,.1);background:#fff}
input::placeholder{color:#bfc5cc}

.btn-submit{width:100%;margin-top:4px;background:var(--accent);color:#fff;border:none;
  border-radius:10px;padding:14px;font-family:'DM Sans',sans-serif;font-size:14px;font-weight:700;
  letter-spacing:.04em;cursor:pointer;transition:background .2s,transform .1s,box-shadow .2s;
  display:flex;align-items:center;justify-content:center;gap:8px;
  box-shadow:0 4px 14px rgba(2,132,199,.35)}
.btn-submit:hover{background:var(--accent-h);box-shadow:0 6px 20px rgba(2,132,199,.45);transform:translateY(-1px)}
.btn-submit:active{transform:none;box-shadow:none}
.btn-submit:disabled{opacity:.6;cursor:not-allowed;transform:none}

.alert{border-radius:10px;padding:12px 15px;font-size:13px;margin-bottom:20px;display:flex;align-items:flex-start;gap:9px;line-height:1.5}
.alert-error{background:#fef2f2;border:1px solid #fecaca;color:var(--red)}
.alert-success{background:#f0fdf4;border:1px solid #bbf7d0;color:var(--green)}
.alert i{flex-shrink:0;margin-top:1px}

.success-state{text-align:center;padding:10px 0 6px}
.success-icon{width:64px;height:64px;border-radius:50%;background:rgba(22,163,74,.1);
  border:2px solid rgba(22,163,74,.25);display:flex;align-items:center;justify-content:center;
  font-size:28px;color:var(--green);margin:0 auto 18px}
.success-state h3{font-family:'DM Sans',sans-serif;font-size:18px;font-weight:800;color:var(--text);margin-bottom:8px}
.success-state p{font-size:13px;color:var(--muted);line-height:1.6;margin-bottom:22px}
.btn-back{display:inline-flex;align-items:center;gap:7px;background:var(--accent);color:#fff;
  border:none;border-radius:10px;padding:12px 24px;font-family:'DM Sans',sans-serif;font-size:13px;
  font-weight:700;letter-spacing:.04em;cursor:pointer;text-decoration:none;
  box-shadow:0 4px 14px rgba(2,132,199,.35);transition:background .2s,transform .1s}
.btn-back:hover{background:var(--accent-h);transform:translateY(-1px)}

.divider{text-align:center;padding:16px 36px;border-top:1px solid var(--border);
  font-size:12px;color:var(--muted)}

@media(max-width:480px){.form-body{padding:24px 20px}.back-bar{padding:14px 20px}}
</style>

<div class="wrap">

  <div class="brand">
    <img src="{{ asset('assets/img/logo.png') }}" alt="FJB Logo" onerror="this.style.display='none'">
    <div class="brand-text">FJB Pasir Gudang<span>Inventory System</span></div>
  </div>

  <div class="card">
    <div class="back-bar">
      <a href="{{ route('it.login') }}" class="back-link"><i class="bi bi-arrow-left"></i> Back to Sign In</a>
    </div>

    <div class="form-body">

      @if(session('success'))
      <div class="success-state">
        <div class="success-icon"><i class="bi bi-check-lg"></i></div>
        <h3>Request Sent!</h3>
        <p>{{ session('success') }}</p>
        <a href="{{ route('it.login') }}" class="btn-back"><i class="bi bi-arrow-left"></i> Back to Sign In</a>
      </div>

      @else

      <div class="icon-circle"><i class="bi bi-key-fill"></i></div>
      <div class="form-title">Forgot Password</div>
      <div class="form-sub">Enter your username and Staff ID to request a password reset from the admin.</div>

      @if(session('error'))
      <div class="alert alert-error"><i class="bi bi-exclamation-triangle-fill"></i>{{ session('error') }}</div>
      @endif
      @if($errors->any())
      <div class="alert alert-error"><i class="bi bi-exclamation-triangle-fill"></i>{{ $errors->first() }}</div>
      @endif

      <div class="info-note">
        <i class="bi bi-info-circle-fill"></i>
        <span>Admin will verify your Staff ID and reset your password to a temporary default. You'll be required to set a new password on your next login.</span>
      </div>

      <form method="POST" action="{{ route('it.password.forgot.submit') }}">
        @csrf
        <div class="field">
          <label>Username</label>
          <div class="input-wrap">
            <i class="bi bi-person"></i>
            <input type="text" name="fp_username" placeholder="Your login username"
              value="{{ old('fp_username') }}" required autofocus>
          </div>
        </div>
        <div class="field">
          <label>Staff ID</label>
          <div class="input-wrap">
            <i class="bi bi-person-badge"></i>
            <input type="text" name="fp_staff_id" placeholder="e.g. FJB-0012"
              value="{{ old('fp_staff_id') }}" required>
          </div>
        </div>
        <button type="submit" class="btn-submit">
          <i class="bi bi-send-fill"></i> Send Reset Request
        </button>
      </form>

      @endif

    </div>
    <div class="divider">FJV Johor Bulkers Sdn Bhd</div>
  </div>

</div>
@endsection

