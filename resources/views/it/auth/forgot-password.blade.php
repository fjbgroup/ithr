@extends('it.layouts.auth')

@section('title', 'Forgot Password')

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

/* ── Card ── */
.card{
  width:100%;max-width:480px;
  border-radius:22px;overflow:hidden;
  box-shadow:0 30px 70px -20px rgba(8,20,40,.55),0 2px 8px rgba(8,20,40,.2);
  animation:auth-rise .55s cubic-bezier(.16,.84,.44,1) both;
}
@keyframes auth-rise{
  from{opacity:0;transform:translateY(18px) scale(.985);}
  to{opacity:1;transform:none;}
}

/* Panel */
.card-right{
  background:#ffffff;
  padding:2.5rem 2.5rem 2rem;
  display:flex;flex-direction:column;
}

.form-title{font-size:26px;font-weight:800;color:#0f172a;margin-bottom:4px;}
.form-sub{font-size:13px;color:#64748b;margin-bottom:24px;line-height:1.6;}

.icon-row{
  display:flex;align-items:center;gap:12px;margin-bottom:20px;
}
.icon-circle{
  width:46px;height:46px;border-radius:50%;
  background:rgba(247,148,29,.12);border:1.5px solid rgba(247,148,29,.3);
  display:flex;align-items:center;justify-content:center;
  font-size:20px;color:#F7941D;flex-shrink:0;
}

.info-note{
  background:rgba(247,148,29,.07);border:1px solid rgba(247,148,29,.22);
  border-radius:10px;padding:12px 15px;
  display:flex;gap:10px;align-items:flex-start;margin-bottom:20px;
}
.info-note i{color:#F7941D;font-size:14px;flex-shrink:0;margin-top:2px;}
.info-note span{font-size:12.5px;color:#64748b;line-height:1.6;}

.field{margin-bottom:18px;}
.field label{
  display:block;font-size:12px;font-weight:600;color:#374151;
  margin-bottom:7px;
}
.input-wrap{position:relative;display:flex;align-items:center;}
.input-wrap i.input-icon{
  position:absolute;left:13px;color:#9ca3af;font-size:14px;pointer-events:none;z-index:1;
}
input[type="text"]{
  width:100%;background:#f8fafc;
  border:1.5px solid #e2e8f0;border-radius:10px;
  color:#0f172a;padding:12px 14px 12px 38px;
  font-size:14px;font-family:'DM Sans',sans-serif;
  transition:border-color .2s,box-shadow .2s;outline:none;
}
input:focus{border-color:#F7941D;box-shadow:0 0 0 3px rgba(247,148,29,.18);background:#fff;}
input::placeholder{color:#94a3b8;}

.btn-submit{
  width:100%;margin-top:4px;
  background:linear-gradient(135deg,#FFB84D 0%,#F7941D 60%,#C96800 100%);color:#fff;border:none;
  border-radius:10px;padding:14px;
  font-family:'DM Sans',sans-serif;font-size:14px;font-weight:700;
  letter-spacing:.03em;cursor:pointer;
  display:flex;align-items:center;justify-content:center;gap:8px;
  transition:filter .2s,box-shadow .2s,transform .1s;
  box-shadow:0 4px 14px rgba(247,148,29,.4);
  text-decoration:none;
}
.btn-submit:hover{filter:brightness(1.08);box-shadow:0 6px 20px rgba(247,148,29,.5);transform:translateY(-1px);}

.btn-back{
  width:100%;margin-top:10px;
  background:transparent;color:#64748b;
  border:1.5px solid #e2e8f0;border-radius:10px;padding:13px;
  font-family:'DM Sans',sans-serif;font-size:13px;font-weight:600;
  letter-spacing:.02em;cursor:pointer;
  display:flex;align-items:center;justify-content:center;gap:7px;
  transition:border-color .2s,color .2s,background .2s;
  text-decoration:none;
}
.btn-back:hover{border-color:#F7941D;color:#F7941D;background:rgba(247,148,29,.04);}

.alert{border-radius:10px;padding:11px 14px;font-size:13px;display:flex;align-items:center;gap:9px;margin-bottom:16px;font-weight:500;}
.alert-error{background:#fee2e2;border:1px solid #fecaca;color:#991b1b;}

/* Success state */
.success-state{text-align:center;padding:12px 0 8px;}
.success-icon{
  width:64px;height:64px;border-radius:50%;
  background:rgba(22,163,74,.1);border:2px solid rgba(22,163,74,.25);
  display:flex;align-items:center;justify-content:center;
  font-size:28px;color:#16a34a;margin:0 auto 18px;
}
.success-state h3{font-size:20px;font-weight:800;color:#0f172a;margin-bottom:8px;}
.success-state p{font-size:13px;color:#64748b;line-height:1.6;margin-bottom:24px;}
</style>

<div class="card">
  <div class="card-right">

    @if(session('success'))

    <div class="success-state">
      <div class="success-icon"><i class="bi bi-check-lg"></i></div>
      <h3>Request Sent!</h3>
      <p>{{ session('success') }}</p>
      <a href="{{ route('it.login') }}" class="btn-submit">
        <i class="bi bi-arrow-left"></i> Back to Sign In
      </a>
    </div>

    @else

    <div class="icon-row">
      <div class="icon-circle"><i class="bi bi-key-fill"></i></div>
      <div>
        <div class="form-title">Forgot Password</div>
      </div>
    </div>
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
          <i class="bi bi-person input-icon"></i>
          <input type="text" name="fp_username" placeholder="Your login username"
            value="{{ old('fp_username') }}" required autofocus>
        </div>
      </div>
      <div class="field">
        <label>Staff ID</label>
        <div class="input-wrap">
          <i class="bi bi-person-badge input-icon"></i>
          <input type="text" name="fp_staff_id" placeholder="e.g. FJB-0012"
            value="{{ old('fp_staff_id') }}" required>
        </div>
      </div>
      <button type="submit" class="btn-submit">
        <i class="bi bi-send-fill"></i> Send Reset Request
      </button>
    </form>

    <a href="{{ route('it.login') }}" class="btn-back">
      <i class="bi bi-arrow-left"></i> Back to Sign In
    </a>

    @endif

  </div>
</div>
@endsection
