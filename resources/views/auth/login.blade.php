<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — {{ config('app.name', 'HR Admin System') }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body class="login-body">
<div class="login-wrapper">
  <div class="login-card">

    <!-- Brand -->
    <div class="login-brand">
      <div class="brand-icon">
        <img src="{{ asset('assets/images/logo.png') }}" alt="FJB" style="width:50px;height:50px;object-fit:contain;">
      </div>
      <h1 class="brand-name">HR Admin System</h1>
      <p class="brand-sub">Staff Login</p>
    </div>

    @if ($errors->any())
    <div class="alert alert-error" style="margin-bottom:1rem;">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      @foreach ($errors->all() as $error)
          <div>{{ $error }}</div>
      @endforeach
    </div>
    @endif

    @if (session('status'))
    <div class="alert alert-success" style="margin-bottom:1rem;">
      {{ session('status') }}
    </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
      @csrf
      <div class="form-group" style="margin-bottom:1rem;">
        <label style="font-size:.82rem;font-weight:600;">Staff ID</label>
        <input type="text" name="staff_no" placeholder="e.g. 0000001" required autofocus
               autocapitalize="none" autocorrect="off" spellcheck="false"
               autocomplete="username" inputmode="text"
               style="font-size:1rem;" value="{{ old('staff_no') }}">
      </div>
      <div class="form-group" style="margin-bottom:1rem;">
        <label style="font-size:.82rem;font-weight:600;">Password</label>
        <input type="password" name="password" placeholder="Enter your password" required
               autocomplete="current-password" style="font-size:1rem;">
      </div>
      
      <div style="font-size:.78rem;color:#64748b;margin-bottom:1rem;text-align:right;">
        <a href="{{ route('password.request') }}" style="color:#6366f1;">Forgot password?</a>
      </div>

      <button type="submit" class="btn btn-primary btn-full">Sign In →</button>
    </form>

  </div>
</div>
<script src="{{ asset('assets/js/app.js') }}"></script>
</body>
</html>