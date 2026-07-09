@extends('lms.layout.app')

@section('title', 'Learning Dashboard')

@section('content')
<div class="hd-banner" style="margin-bottom: 24px;">
    <div class="hd-banner-left">
        <div class="hd-greeting">
            My Learning Dashboard
        </div>
        <div class="hd-date">View and access your assigned training courses</div>
    </div>
</div>

<div class="page-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
    @forelse($enrolledCourses as $course)
    <div class="table-card" style="padding: 20px; display: flex; flex-direction: column;">
        <h3 style="font-family: 'Inter', sans-serif; font-size: 16px; font-weight: 700; color: var(--text); margin: 0 0 8px 0;">{{ $course->title }}</h3>
        <div style="font-size: 13px; color: var(--muted); margin-bottom: 16px; flex: 1;">Code: {{ $course->code }}</div>
        
        <a href="{{ route('lms.learn.show', $course->id) }}" class="btn btn-primary" style="text-align: center; justify-content: center;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px;"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
            <span class="btn-label">Go to Course</span>
        </a>
    </div>
    @empty
    <div class="table-card" style="grid-column: 1 / -1; padding: 40px 20px; text-align: center;">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--border)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 16px;"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        <h3 style="font-family: 'Inter', sans-serif; font-size: 16px; font-weight: 700; color: var(--text); margin: 0 0 4px 0;">No courses assigned yet.</h3>
        <p style="font-size: 13px; color: var(--muted); margin: 0;">When you are assigned an online course, it will appear here.</p>
    </div>
    @endforelse
</div>
@endsection