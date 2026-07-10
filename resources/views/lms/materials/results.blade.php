@extends('lms.layout.app')
@section('title', 'Quiz Results')
@section('content')
<div class="page-container" style="padding: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px;">
        <div>
            <h2 style="font-family: 'Inter', sans-serif; font-size: 20px; font-weight: 700; color: var(--text); margin: 0 0 4px 0;">Quiz Results: {{ $material->title }}</h2>
            <p style="font-size: 13px; color: var(--muted); margin: 0;">Course: {{ $course->title }} ({{ $course->code }})</p>
        </div>
        <div>
            <a href="{{ route('lms.courses.show', $course->id) }}" class="btn btn-outline" style="display: inline-flex; align-items: center; gap: 8px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                <span class="btn-label">Back to Course</span>
            </a>
        </div>
    </div>

    <div class="table-card" style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left;">Staff Name</th>
                    <th style="text-align: left;">Staff No</th>
                    <th style="text-align: left;">Department</th>
                    <th style="text-align: right;">Score</th>
                    <th style="text-align: right;">Completed At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($results as $progress)
                <tr>
                    <td style="font-weight: 600;">{{ $progress->staff->name }}</td>
                    <td style="color: var(--muted);">{{ $progress->staff->staff_no }}</td>
                    <td>{{ $progress->staff->department->name ?? 'N/A' }}</td>
                    <td style="text-align: right;">
                        <span style="font-weight: 700; color: {{ $progress->score >= 50 ? '#16a34a' : '#ef4444' }};">
                            {{ $progress->score }}%
                        </span>
                    </td>
                    <td style="text-align: right; color: var(--muted); font-size: 13px;">
                        {{ $progress->updated_at->format('d M Y, h:i A') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 32px; color: var(--muted);">
                        No attendees have completed this quiz yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
