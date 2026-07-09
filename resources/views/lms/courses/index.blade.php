@extends('lms.layout.app')
@section('title', 'Manage Courses')
@section('content')
<div class="page-container" style="padding: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h2 style="font-family: 'Inter', sans-serif; font-size: 20px; font-weight: 700; color: var(--text); margin: 0;">Manage Online Courses</h2>
        <a href="{{ route('lms.courses.create') }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            <span class="btn-label">New Course</span>
        </a>
    </div>

    <div class="table-card" style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left;">Code</th>
                    <th style="text-align: left;">Title</th>
                    <th style="text-align: left;">PIC</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($courses as $course)
                <tr>
                    <td style="font-weight: 600;">{{ $course->code }}</td>
                    <td>{{ $course->title }}</td>
                    <td>{{ $course->pic ? $course->pic->name : '-' }}</td>
                    <td style="text-align: right;">
                        <a href="{{ route('lms.courses.show', $course->id) }}" class="btn btn-outline btn-sm" style="display: inline-flex; align-items: center; gap: 6px; margin-right: 8px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            <span>Manage</span>
                        </a>
                        <a href="{{ route('lms.courses.edit', $course->id) }}" class="btn btn-outline btn-sm" style="display: inline-flex; align-items: center; gap: 6px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            <span>Edit</span>
                        </a>
                    </td>
                </tr>
                @endforeach
                @if($courses->isEmpty())
                <tr>
                    <td colspan="4" style="text-align: center; padding: 32px; color: var(--muted);">
                        No online courses found. Click 'New Course' to create one.
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection