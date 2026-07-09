@extends('lms.layout.app')
@section('title', 'Dashboard')
@section('content')
<div class="max-w-5xl mx-auto">
  <h2 class="text-2xl font-bold mb-6 text-slate-800">My Learning Dashboard</h2>

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($enrolledCourses as $course)
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-md transition">
      <h3 class="font-bold text-lg text-slate-800 mb-2">{{ $course->title }}</h3>
      <p class="text-sm text-slate-500 mb-4">Code: {{ $course->code }}</p>
      <a href="{{ route('lms.learn.show', $course->id) }}" class="inline-block bg-purple-600 hover:bg-purple-700 text-white font-medium px-4 py-2 rounded-lg transition">
        Go to Course
      </a>
    </div>
    @empty
    <div class="col-span-full p-12 text-center bg-white rounded-2xl border border-slate-100">
      <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
      <h3 class="text-lg font-medium text-slate-700">No courses assigned yet.</h3>
      <p class="text-slate-500 mt-1">When you are assigned an online course, it will appear here.</p>
    </div>
    @endforelse
  </div>
</div>
@endsection