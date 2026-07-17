<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'HR Admin') }} - @yield('title', 'Login')</title>
    @include('partials.favicons')
    <script>
        (function () {
            const stored = localStorage.getItem('fjb-theme') || localStorage.getItem('color-theme') || localStorage.getItem('theme');
            const theme = stored || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.classList.toggle('dark', theme === 'dark');
            document.documentElement.setAttribute('data-theme', theme);
            document.documentElement.style.colorScheme = theme;
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}?v={{ time() }}">
  <meta name="view-transition" content="same-origin">
  <style>
    html.theme-transitioning::view-transition-old(root),
    html.theme-transitioning::view-transition-new(root) { animation: none; mix-blend-mode: normal; display: block; }
    html.theme-transition-expand::view-transition-new(root) { z-index: 2; }
    html.theme-transition-expand::view-transition-old(root) { z-index: 1; }
    html.theme-transition-shrink::view-transition-old(root) { z-index: 2; }
    html.theme-transition-shrink::view-transition-new(root) { z-index: 1; }
  </style>
</head>
<body class="login-body">
    <div style="position: fixed; top: 1.5rem; right: 1.5rem; z-index: 1000;">
        <button onclick="toggleTheme(event)" class="theme-toggle-btn" type="button" aria-label="Toggle theme">
            <svg class="sun-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
            <svg class="moon-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
        </button>
    </div>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <div class="brand-icon">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" style="width:48px;height:48px;object-fit:contain;">
                </div>
                <h1>{{ config('app.name', 'HR Admin') }}</h1>
                <p>Human Resource Management System</p>
            </div>
            
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin:0; padding-left:20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script>
        function toggleTheme() {
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            const next = isDark ? 'light' : 'dark';
            document.documentElement.classList.toggle('dark', next === 'dark');
            document.documentElement.setAttribute('data-theme', next);
            document.documentElement.style.colorScheme = next;
            localStorage.setItem('fjb-theme', next);
            localStorage.setItem('color-theme', next);
            localStorage.setItem('theme', next);
        }
    </script>
</body>
</html>


