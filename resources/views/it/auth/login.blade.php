@extends('it.layouts.auth')

@section('title', 'Sign In')

@section('content')
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{
  min-height:100vh;
  background:linear-gradient(135deg,#EEF0F3 0%,#ECEFF1 30%,#EDEFF2 65%,#ECEFF2 100%);
  background-attachment:fixed;
  font-family:'DM Sans',sans-serif;
  display:flex;align-items:center;justify-content:center;padding:24px;
}

/* ── Two-panel card ── */
.card{
  width:100%;max-width:940px;
  display:grid;grid-template-columns:1.05fr .95fr;
  border-radius:22px;overflow:hidden;
  box-shadow:0 30px 70px -20px rgba(8,20,40,.55),0 2px 8px rgba(8,20,40,.2);
  animation:auth-rise .55s cubic-bezier(.16,.84,.44,1) both;
}
@keyframes auth-rise{
  from{opacity:0;transform:translateY(18px) scale(.985);}
  to{opacity:1;transform:none;}
}

/* Left panel */
.card-left{
  background:linear-gradient(160deg,#FFB84D 0%,#F7941D 50%,#C96800 100%);
  padding:3rem 2.75rem;
  display:flex;flex-direction:column;
  position:relative;overflow:hidden;
}
.card-left::before{
  content:'';position:absolute;bottom:-120px;left:-80px;
  width:340px;height:340px;
  background:radial-gradient(circle,rgba(255,255,255,.06) 0%,transparent 65%);
  border-radius:50%;
}
.card-left::after{
  content:'';position:absolute;top:-60px;right:-60px;
  width:220px;height:220px;
  background:radial-gradient(circle,rgba(255,255,255,.05) 0%,transparent 65%);
  border-radius:50%;
}
.left-logo{
  margin-bottom:32px;flex-shrink:0;
}
.left-logo img{width:56px;height:56px;object-fit:contain;}
.left-title{
  font-size:1.9rem;font-weight:700;color:#ffffff;
  line-height:1.2;margin-bottom:12px;
}
.left-sub{
  font-size:13px;color:rgba(255,255,255,.65);
  line-height:1.6;margin-bottom:36px;
}
.left-features{list-style:none;display:flex;flex-direction:column;gap:14px;margin-top:auto;}
.left-features li{
  display:flex;align-items:center;gap:12px;
  font-size:13px;color:rgba(255,255,255,.80);font-weight:500;
}
.feat-icon{
  display:flex;align-items:center;justify-content:center;
  width:34px;height:34px;border-radius:9px;flex-shrink:0;
  background:rgba(255,255,255,.12);
}
.feat-icon i{font-size:15px;color:rgba(255,255,255,.85);}

/* Right panel */
.card-right{
  background:#ffffff;
  padding:3rem 2.75rem;display:flex;flex-direction:column;justify-content:center;
}
.form-title{font-size:26px;font-weight:800;color:#0f172a;margin-bottom:4px;}
.form-sub{font-size:13px;color:#64748b;margin-bottom:28px;}
.field{margin-bottom:18px;}
.field label{
  display:block;font-size:12px;font-weight:600;color:#374151;
  margin-bottom:7px;
}
.input-wrap{position:relative;display:flex;align-items:center;}
.input-wrap i.input-icon{
  position:absolute;left:13px;color:#9ca3af;font-size:14px;pointer-events:none;z-index:1;
}
input[type="text"],input[type="email"],input[type="password"]{
  width:100%;background:#f8fafc;
  border:1.5px solid #e2e8f0;border-radius:10px;
  color:#0f172a;padding:12px 40px 12px 38px;
  font-size:14px;font-family:'DM Sans',sans-serif;
  transition:border-color .2s,box-shadow .2s;outline:none;
}
input:focus{border-color:#F7941D;box-shadow:0 0 0 3px rgba(247,148,29,.18);background:#fff;}
input::placeholder{color:#94a3b8;}
.pw-toggle{
  position:absolute;right:12px;
  background:none;border:none;cursor:pointer;
  color:#9ca3af;font-size:16px;padding:4px;
  transition:color .2s;z-index:1;
}
.pw-toggle:hover{color:#F7941D;}
.forgot-link{
  display:block;text-align:right;margin-top:7px;
  font-size:12px;font-weight:600;color:#F7941D;
  text-decoration:none;
}
.forgot-link:hover{text-decoration:underline;}
.btn-submit{
  width:100%;margin-top:4px;
  background:linear-gradient(135deg,#FFB84D 0%,#F7941D 60%,#C96800 100%);color:#fff;border:none;
  border-radius:10px;padding:14px;
  font-family:'DM Sans',sans-serif;font-size:14px;font-weight:700;
  letter-spacing:.03em;cursor:pointer;
  display:flex;align-items:center;justify-content:center;gap:8px;
  transition:filter .2s,box-shadow .2s,transform .1s;
  box-shadow:0 4px 14px rgba(247,148,29,.4);
}
.btn-submit:hover{filter:brightness(1.08);box-shadow:0 6px 20px rgba(247,148,29,.5);transform:translateY(-1px);}

.alert{border-radius:10px;padding:11px 14px;font-size:13px;display:flex;align-items:center;gap:9px;margin-bottom:16px;font-weight:500;}
.alert-error{background:#fee2e2;border:1px solid #fecaca;color:#991b1b;}
.alert-success{background:#dcfce7;border:1px solid #bbf7d0;color:#166534;}

/* Bottom links inside right panel */
.card-footer{margin-top:20px;text-align:center;}
.back-link{
  font-size:12px;color:#64748b;text-decoration:none;font-weight:500;
  display:inline-flex;align-items:center;gap:5px;
  transition:color .2s;
}
.back-link:hover{color:#F7941D;}
.switch-label{
  font-size:10px;font-weight:700;color:#94a3b8;
  text-transform:uppercase;letter-spacing:.1em;
  margin:10px 0 6px;
}
.switch-links{display:flex;justify-content:center;align-items:center;gap:4px;}
.switch-links a{
  font-size:12px;font-weight:600;color:#64748b;
  text-decoration:none;padding:2px 4px;
  transition:color .2s;
}
.switch-links a:hover{color:#F7941D;}
.switch-links .sep{color:#cbd5e1;font-size:11px;}
.switch-links .current{color:#F7941D;font-weight:700;}
</style>

<div class="card">

  {{-- Left panel --}}
  <div class="card-left">
    <div class="left-logo">
      <img src="{{ asset('assets/images/fjb-logo.png') }}" alt="FJB Logo">
    </div>
    <div class="left-title">Welcome to the<br>Asset Management System</div>
    <div class="left-sub">Manage IT assets, service requests and more — all from one secure workspace.</div>
    <ul class="left-features">
      <li>
        <div class="feat-icon"><i class="bi bi-laptop"></i></div>
        Asset &amp; inventory tracking
      </li>
      <li>
        <div class="feat-icon"><i class="bi bi-ticket-detailed"></i></div>
        IT request management
      </li>
      <li>
        <div class="feat-icon"><i class="bi bi-archive"></i></div>
        Write-off &amp; disposal records
      </li>
    </ul>
  </div>

  {{-- Right panel --}}
  <div class="card-right">
    <div class="form-title">Sign In</div>
    <div class="form-sub">Enter your credentials to access your account.</div>

    @if(session('error'))
    <div class="alert alert-error"><i class="bi bi-exclamation-triangle-fill"></i>{{ session('error') }}</div>
    @endif
    @if(session('success'))
    <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i>{{ session('success') }}</div>
    @endif
    @if($errors->has('login'))
    <div class="alert alert-error"><i class="bi bi-exclamation-triangle-fill"></i>{{ $errors->first('login') }}</div>
    @endif
    @if(session('timeout'))
    <div class="alert alert-error"><i class="bi bi-clock-history"></i>Your session expired. Please sign in again.</div>
    @endif

    <form method="POST" action="{{ route('it.login.submit') }}">
      @csrf
      <div class="field">
        <label>Staff ID</label>
        <div class="input-wrap">
          <i class="bi bi-person input-icon"></i>
          <input type="text" name="username" placeholder="e.g. 0000001"
            value="{{ old('username') }}" required autofocus autocomplete="username">
        </div>
      </div>
      <div class="field">
        <label>Password</label>
        <div class="input-wrap">
          <i class="bi bi-lock input-icon"></i>
          <input type="password" name="password" id="password-input" placeholder="Enter your password" required>
          <button type="button" class="pw-toggle" onclick="togglePassword()">
            <i class="bi bi-eye" id="eye-icon"></i>
          </button>
        </div>
        <a href="{{ route('it.password.forgot') }}" class="forgot-link">Forgot password?</a>
      </div>
      <button type="submit" class="btn-submit">Sign In &nbsp;<i class="bi bi-arrow-right"></i></button>
    </form>

    <div class="card-footer">
      <a href="{{ url('/') }}" class="back-link"><i class="bi bi-arrow-left"></i> Back to Portal</a>
      <div class="switch-label">Switch System</div>
      <div class="switch-links">
        <a href="{{ url('/login') }}">HR System</a>
        <span class="sep">·</span>
        <a href="{{ url('/wt') }}">WT System</a>
        <span class="sep">·</span>
        <span class="current">IT System</span>
      </div>
    </div>
  </div>

</div>

<script>
function togglePassword() {
  const input = document.getElementById('password-input');
  const icon  = document.getElementById('eye-icon');
  if (input.type === 'password') { input.type = 'text'; icon.className = 'bi bi-eye-slash'; }
  else { input.type = 'password'; icon.className = 'bi bi-eye'; }
}
</script>
@endsection


