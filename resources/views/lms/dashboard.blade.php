@extends('lms.layout.app')

@section('title', 'Learning Dashboard')

@section('content')
<div class="lms-page-container">
    <div class="lms-hero">
        <div style="position: relative; z-index: 1;">
            <div class="lms-hero-title">Welcome to Your Learning Journey</div>
            <div class="lms-hero-subtitle">Expand your skills, complete your mandatory training, and grow.</div>
        </div>
        <div style="position: relative; z-index: 1; opacity: 0.9;">
            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
        </div>
    </div>

    <div style="margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between;">
        <h2 style="font-family: var(--lms-font-heading); font-size: 1.5rem; font-weight: 700; color: var(--text); margin: 0;">My Enrolled Courses</h2>
    </div>

    <div class="lms-course-grid">
        @forelse($enrolledCourses as $course)
        <div class="lms-course-card">
            <div class="lms-course-thumbnail">
                <div class="lms-course-type-badge">{{ $course->training_type }}</div>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8"/></svg>
            </div>
            <div class="lms-course-content">
                <h3 class="lms-course-title">{{ $course->title }}</h3>
                <div class="lms-course-code">Code: {{ $course->code }}</div>
                
                <div style="display: flex; justify-content: flex-end;">
                    <a href="{{ route('lms.learn.show', $course->id) }}" class="lms-btn-play">
                        <span class="btn-label">Continue Learning</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="lms-course-card" style="grid-column: 1 / -1; padding: 60px 20px; text-align: center; border-style: dashed; background: transparent; box-shadow: none;">
            <div style="width: 80px; height: 80px; margin: 0 auto 20px; background: rgba(99, 102, 241, 0.1); color: var(--lms-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"></path><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
            </div>
            <h3 style="font-family: var(--lms-font-heading); font-size: 1.2rem; font-weight: 700; color: var(--text); margin: 0 0 8px 0;">No courses assigned yet.</h3>
            <p style="font-size: 0.95rem; color: var(--muted); margin: 0;">When you are enrolled in a course, it will appear here.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection