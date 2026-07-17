<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Sign In') — FJB Inventory System</title>
@include('partials.favicons')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<link href="{{ asset('it-assets/css/style.css') }}" rel="stylesheet">
<script>!function(){var t=localStorage.getItem('fjb-theme')||localStorage.getItem('color-theme')||localStorage.getItem('theme');if(t==='dark')document.documentElement.classList.add('dark');}();</script>
<style>
  .auth-theme-toggle {
    position: absolute; top: 1.5rem; right: 1.5rem; z-index: 1000;
    background: #fff; border: 1px solid #e2e8f0; color: #64748b;
    cursor: pointer; padding: 10px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    transition: all .2s; outline: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  }
  html.dark .auth-theme-toggle { background: #0f172a; border-color: #334155; color: #94a3b8; }
  .auth-theme-toggle:hover { color: #0f172a; border-color: #F7941D; }
  html.dark .auth-theme-toggle:hover { color: #f8fafc; border-color: #F7941D; }
  html:not(.dark) .auth-theme-toggle .icon-sun { display: none; }
  html.dark .auth-theme-toggle .icon-moon { display: none; }
</style>
</head>
<body>
  <button onclick="toggleTheme()" class="auth-theme-toggle" aria-label="Toggle theme">
    <svg class="icon-sun" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
    <svg class="icon-moon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
  </button>
@yield('content')
<script>
  function toggleTheme() {
    const isDark = document.documentElement.classList.contains('dark');
    const next = isDark ? 'light' : 'dark';
    document.documentElement.classList.toggle('dark', next === 'dark');
    localStorage.setItem('fjb-theme', next);
    localStorage.setItem('color-theme', next);
    localStorage.setItem('theme', next);
  }
</script>
</body>
</html>
