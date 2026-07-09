<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'LMS') - FJB E-Learning</title>
@include('partials.favicons')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<style>
  body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
  .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(255,255,255,0.3); }
  .sidebar-link { transition: all 0.2s; }
  .sidebar-link:hover, .sidebar-link.active { background-color: #f3e8ff; color: #7e22ce; border-right: 3px solid #7e22ce; }
</style>
@stack('styles')
</head>
<body class="text-slate-800 antialiased">
  <!-- Navbar -->
  <nav class="glass sticky top-0 z-50 px-6 py-3 flex items-center justify-between">
    <div class="flex items-center gap-4">
      <a href="{{ route('home') }}" class="text-slate-500 hover:text-slate-800 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      </a>
      <h1 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-purple-600 to-indigo-600">E-Learning LMS</h1>
    </div>
    <div class="flex items-center gap-4">
      <span class="font-medium">{{ Auth::user()->name }}</span>
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button class="text-sm text-red-500 hover:text-red-700 font-medium transition">Logout</button>
      </form>
    </div>
  </nav>

  <div class="flex min-h-[calc(100vh-60px)]">
    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r border-slate-200 flex-shrink-0 py-6 flex flex-col gap-2">
      <div class="px-6 mb-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Student</div>
      <a href="{{ route('lms.dashboard') }}" class="sidebar-link {{ request()->routeIs('lms.dashboard') ? 'active' : '' }} px-6 py-2.5 font-medium text-slate-600 flex items-center gap-3">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
        My Dashboard
      </a>

      @if(Auth::user()->isAdminHR() || Auth::user()->isAdminIT() || \App\Models\TrainingCourse::where('pic_id', Auth::user()->id)->exists())
      <div class="px-6 mt-6 mb-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Management</div>
      <a href="{{ route('lms.courses.index') }}" class="sidebar-link {{ request()->routeIs('lms.courses.*') ? 'active' : '' }} px-6 py-2.5 font-medium text-slate-600 flex items-center gap-3">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        Manage Courses
      </a>
      @endif
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-8 overflow-y-auto">
      @yield('content')
    </main>
  </div>

  <script>
    // Basic notifications etc.
  </script>
  @stack('scripts')
</body>
</html>