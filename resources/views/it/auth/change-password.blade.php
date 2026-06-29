@extends('it.layouts.auth')

@section('title', 'Reset Password')

@section('content')
<style>
:root {
  --bg:#f0f2f5; --surface:#fff; --surface2:#f7f8fa;
  --border:#e2e5ea; --text:#1a1f2e; --muted:#6b7280;
  --accent:#F7941D; --accent-h:#C96800; --red:#dc2626; --green:#16a34a;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{min-height:100vh;background:var(--bg);font-family:'Inter',sans-serif;
  display:flex;align-items:center;justify-content:center;padding:20px;
  position:relative;overflow-x:hidden}
body::before{content:'';position:fixed;inset:0;
  background-image:radial-gradient(circle,#d1d5db 1px,transparent 1px);
  background-size:28px 28px;z-index:0;opacity:.5}
body::after{content:'';position:fixed;width:600px;height:600px;
  background:radial-gradient(circle,rgba(247,148,29,.13) 0%,transparent 65%);
  top:-200px;right:-200px;z-index:0;animation:orb 10s ease-in-out infinite alternate}
@keyframes orb{to{transform:translate(-60px,100px)}}

.wrap{position:relative;z-index:1;width:100%;max-width:460px}

.brand{display:flex;align-items:center;gap:12px;justify-content:center;margin-bottom:28px}
.brand img{width:48px;height:48px;object-fit:contain}
.brand-text{font-family:'Inter',sans-serif;font-size:13px;font-weight:800;color:var(--text);
  text-transform:uppercase;letter-spacing:.06em;line-height:1.3}
.brand-text span{color:var(--accent);display:block;font-size:11px;letter-spacing:.1em}

.card{background:var(--surface);border:1px solid var(--border);border-radius:20px;
  overflow:hidden;box-shadow:0 4px 6px rgba(0,0,0,.04),0 20px 40px rgba(0,0,0,.07)}

.card-header{padding:20px 36px 0;border-bottom:none}

.warning-banner{background:rgba(247,148,29,.08);border:1.5px solid rgba(247,148,29,.3);
  border-radius:12px;padding:14px 18px;display:flex;gap:12px;align-items:flex-start;margin-bottom:22px}
.warning-banner i{color:var(--accent);font-size:20px;flex-shrink:0;margin-top:1px}
.warning-banner strong{display:block;font-size:13px;font-weight:700;color:var(--text);margin-bottom:3px}
.warning-banner p{font-size:12.5px;color:var(--muted);margin:0;line-height:1.5}

.form-body{padding:32px 36px}
.form-title{font-family:'Inter',sans-serif;font-size:22px;font-weight:800;color:var(--text);margin-bottom:4px}
.form-sub{color:var(--muted);font-size:13px;margin-bottom:24px}

.field{margin-bottom:18px}
label{display:block;font-size:11px;font-weight:700;color:var(--muted);
  text-transform:uppercase;letter-spacing:.09em;margin-bottom:7px}
.input-wrap{position:relative;display:flex;align-items:center}
.input-wrap i{position:absolute;left:14px;color:#9ca3af;font-size:15px;pointer-events:none;z-index:1}
input[type="password"]{
  width:100%;background:var(--surface2);border:1.5px solid var(--border);border-radius:10px;
  color:var(--text);padding:13px 14px 13px 42px;font-size:14px;font-family:'Inter',sans-serif;
  transition:border-color .2s,box-shadow .2s;outline:none}
input:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(247,148,29,.18);background:#fff}
input::placeholder{color:#bfc5cc}

.strength-bar{height:4px;border-radius:2px;background:var(--border);margin-top:8px;overflow:hidden}
.strength-fill{height:100%;border-radius:2px;transition:width .3s,background .3s;width:0}

.btn-submit{width:100%;margin-top:6px;background:linear-gradient(135deg,#FFB84D 0%,#F7941D 60%,#C96800 100%);color:#fff;border:none;
  border-radius:10px;padding:14px;font-family:'Inter',sans-serif;font-size:14px;font-weight:700;
  letter-spacing:.04em;cursor:pointer;transition:filter .2s,transform .1s,box-shadow .2s;
  display:flex;align-items:center;justify-content:center;gap:8px;
  box-shadow:0 4px 14px rgba(247,148,29,.4)}
.btn-submit:hover{filter:brightness(1.08);box-shadow:0 6px 20px rgba(247,148,29,.5);transform:translateY(-1px)}
.btn-submit:active{transform:none;box-shadow:none}

.alert{border-radius:10px;padding:11px 14px;font-size:13px;margin-bottom:20px;display:flex;align-items:center;gap:9px}
.alert-error{background:#fef2f2;border:1px solid #fecaca;color:var(--red)}
.alert-success{background:#f0fdf4;border:1px solid #bbf7d0;color:var(--green)}

.divider{text-align:center;padding:16px 36px;border-top:1px solid var(--border);
  font-size:12px;color:var(--muted)}

@media(max-width:480px){.form-body{padding:24px 20px}}
</style>

<div class="wrap">

  <div class="brand">
    <img src="{{ asset('assets/img/logo.png') }}" alt="FJB Logo" onerror="this.style.display='none'">
    <div class="brand-text">FJB Pasir Gudang<span>IT Inventory System</span></div>
  </div>

  <div class="card">
    <div class="form-body">
      <div class="form-title">🔐 Set New Password</div>
      <div class="form-sub">Hi, <strong>{{ auth('it')->user()->full_name }}</strong> — your account is using a temporary password.</div>

      @if(session('error'))
      <div class="alert alert-error"><i class="bi bi-exclamation-triangle-fill"></i>{{ session('error') }}</div>
      @endif
      @if($errors->any())
      <div class="alert alert-error"><i class="bi bi-exclamation-triangle-fill"></i>{{ $errors->first() }}</div>
      @endif
      @if(session('success'))
      <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i>{{ session('success') }}</div>
      @endif

      @if(!session('success'))
      <div class="warning-banner">
        <i class="bi bi-shield-exclamation"></i>
        <div>
          <strong>Password Change Required</strong>
          <p>Your password has been reset to a temporary default. You must set a new password before continuing. The default password cannot be reused.</p>
        </div>
      </div>

      <form method="POST" action="{{ route('it.password.change.update') }}">
        @csrf
        <div class="field">
          <label>New Password</label>
          <div class="input-wrap">
            <i class="bi bi-lock"></i>
            <input type="password" name="new_password" id="new_password"
              placeholder="Min. 6 characters — not 'password'" required
              oninput="checkStrength(this.value)">
          </div>
          <div class="strength-bar"><div class="strength-fill" id="strength-fill"></div></div>
        </div>
        <div class="field">
          <label>Confirm New Password</label>
          <div class="input-wrap">
            <i class="bi bi-lock-fill"></i>
            <input type="password" name="confirm_password" placeholder="Repeat your new password" required>
          </div>
        </div>
        <button type="submit" class="btn-submit">
          <i class="bi bi-shield-check"></i> Set New Password
        </button>
      </form>
      @endif

    </div>
    <div class="divider">FGV Johor Bulkers Sdn Bhd</div>
  </div>

</div>
<script>
function checkStrength(val) {
  const fill = document.getElementById('strength-fill');
  let score = 0;
  if (val.length >= 6)  score++;
  if (val.length >= 10) score++;
  if (/[A-Z]/.test(val)) score++;
  if (/[0-9]/.test(val)) score++;
  if (/[^A-Za-z0-9]/.test(val)) score++;
  const pct   = (score / 5) * 100;
  const color = score <= 1 ? '#dc2626' : score <= 2 ? '#f59e0b' : score <= 3 ? '#3b82f6' : '#16a34a';
  fill.style.width      = pct + '%';
  fill.style.background = color;
}
@if(session('success'))
setTimeout(() => { window.location.href = '{{ route('it.dashboard') }}'; }, 2000);
@endif
</script>
@endsection

