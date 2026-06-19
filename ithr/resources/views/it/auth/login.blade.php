@extends('it.layouts.auth')

@section('title', 'Sign In')

@section('content')
<style>
:root {
  --bg:#ffffff; --surface:#fff; --surface2:#f7f8fa;
  --border:#e2e5ea; --text:#1a1f2e; --muted:#6b7280;
  --accent:#38bdf8; --accent-h:#0284c7; --navy:#142b47; --red:#dc2626; --green:#16a34a;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{min-height:100vh;background:linear-gradient(135deg,#0a1628 0%,#0f2347 50%,#142b5e 100%);font-family:'DM Sans',sans-serif;
  display:flex;align-items:center;justify-content:center;padding:20px;
  position:relative;overflow-x:hidden}
body::before{content:'';position:fixed;inset:0;
  background-image:radial-gradient(circle,rgba(56,189,248,0.06) 1px,transparent 1px);
  background-size:28px 28px;z-index:0;opacity:.5}
body::after{content:'';position:fixed;width:600px;height:600px;
  background:radial-gradient(circle,rgba(56,189,248,.12) 0%,transparent 65%);
  top:-200px;right:-200px;z-index:0;animation:orb 10s ease-in-out infinite alternate}
@keyframes orb{to{transform:translate(-60px,100px)}}
.wrap{position:relative;z-index:1;width:100%;max-width:480px}
.brand{display:flex;align-items:center;gap:12px;justify-content:center;margin-bottom:28px}
.brand img{width:48px;height:48px;object-fit:contain;background:transparent}
.brand-text{font-family:'DM Sans',sans-serif;font-size:20px;font-weight:800;color:#fff;
  text-transform:uppercase;letter-spacing:.06em;line-height:1.3}
.brand-text span{color:#cbd5e1;display:block;font-size:15px;letter-spacing:.05em;font-weight:800}
.card{background:var(--surface);border:1px solid var(--border);border-radius:20px;
  overflow:hidden;box-shadow:0 4px 6px rgba(0,0,0,.04),0 20px 40px rgba(0,0,0,.07)}
.form-body{padding:32px 36px}
.form-title{font-family:'DM Sans',sans-serif;font-size:24px;font-weight:800;color:var(--text);margin-bottom:4px}
.form-sub{color:var(--muted);font-size:13px;margin-bottom:26px}
.field{margin-bottom:18px}
label{display:block;font-size:11px;font-weight:700;color:var(--muted);
  text-transform:uppercase;letter-spacing:.09em;margin-bottom:7px}
.input-wrap{position:relative;display:flex;align-items:center}
.input-wrap i{position:absolute;left:14px;color:#9ca3af;font-size:15px;pointer-events:none;z-index:1}
input[type="text"],input[type="email"],input[type="password"]{
  width:100%;background:var(--surface2);border:1.5px solid var(--border);border-radius:10px;
  color:var(--text);padding:13px 14px 13px 42px;font-size:14px;font-family:'DM Sans',sans-serif;
  transition:border-color .2s,box-shadow .2s;outline:none}
input:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(56,189,248,.15);background:#fff}
input::placeholder{color:#bfc5cc}
.row-2{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.btn-submit{width:100%;margin-top:6px;background:var(--navy);color:#fff;border:none;
  border-radius:10px;padding:14px;font-family:'DM Sans',sans-serif;font-size:14px;font-weight:700;
  letter-spacing:.04em;cursor:pointer;transition:background .2s,transform .1s,box-shadow .2s;
  display:flex;align-items:center;justify-content:center;gap:8px;
  box-shadow:0 4px 14px rgba(20,43,71,.25)}
.btn-submit:hover{background:var(--accent-h);box-shadow:0 6px 20px rgba(20,43,71,.3);transform:translateY(-1px)}
.alert{border-radius:10px;padding:12px 16px;font-size:13px;display:flex;align-items:center;gap:9px;margin-bottom:18px;font-weight:500}
.alert-error{background:#fee2e2;border:1px solid #fecaca;color:#991b1b}
.alert-success{background:#dcfce7;border:1px solid #bbf7d0;color:#166534}
.divider{text-align:center;padding:16px 36px;border-top:1px solid var(--border);font-size:11px;color:#94a3b8;font-weight:600;letter-spacing:.07em;text-transform:uppercase}
</style>

<div class="wrap">
  <div class="brand">
    <img src="{{ asset('assets/images/fjb-logo.png') }}" alt="FJB Logo">
    <div class="brand-text">FJB Inventory<span>Management System</span></div>
  </div>

  <div class="card">
    <div class="form-body">

      <div class="form-title">Welcome Back</div>
      <div class="form-sub">Sign in to access the inventory portal</div>

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
          <label>Username</label>
          <div class="input-wrap">
            <i class="bi bi-person"></i>
            <input type="text" name="username" placeholder="Username or Staff ID"
              value="{{ old('username') }}" required autofocus autocomplete="username">
          </div>
        </div>
        <div class="field">
          <label>Password</label>
          <div style="display:flex;align-items:center;gap:10px">
            <div class="input-wrap" style="flex:1">
              <i class="bi bi-lock"></i>
              <input type="password" name="password" id="password-input" placeholder="Enter your password" required>
            </div>
            <button type="button" onclick="togglePassword()"
              style="flex-shrink:0;background:none;border:none;cursor:pointer;color:#9ca3af;font-size:18px;padding:4px;transition:color .2s"
              onmouseover="this.style.color='#0284c7'" onmouseout="this.style.color='#9ca3af'">
              <i class="bi bi-eye" id="eye-icon"></i>
            </button>
          </div>
          <div style="text-align:right;margin-top:8px">
            <a href="{{ route('it.password.forgot') }}"
              style="font-size:12px;color:var(--accent);text-decoration:none;font-weight:600;letter-spacing:.01em">
              <i class="bi bi-key" style="font-size:11px"></i> Forgot Password?
            </a>
          </div>
        </div>
        <button type="submit" class="btn-submit">Sign In <i class="bi bi-arrow-right"></i></button>
      </form>

    </div>
    <div class="divider">FGV Johor Bulkers Sdn Bhd</div>
  </div>
  <div style="text-align:center;margin-top:1.25rem;font-size:.8rem;color:#94a3b8">
    <a href="{{ url('/') }}" style="color:#94a3b8;text-decoration:none;" onmouseover="this.style.color='#0284c7'" onmouseout="this.style.color='#94a3b8'">
      &larr; Back to Portal
    </a>
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


