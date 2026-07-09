@extends('lms.layout.app')
@section('title', 'Course Materials')
@section('content')
<div class="page-container" style="padding: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px;">
        <div>
            <h2 style="font-family: 'Inter', sans-serif; font-size: 20px; font-weight: 700; color: var(--text); margin: 0 0 4px 0;">{{ $course->title }}</h2>
            <p style="font-size: 13px; color: var(--muted); margin: 0;">Code: {{ $course->code }} | PIC: {{ $course->pic ? $course->pic->name : 'None' }}</p>
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('lms.courses.index') }}" class="btn btn-outline" style="display: inline-flex; align-items: center; gap: 8px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                <span class="btn-label">Back</span>
            </a>
            <a href="{{ route('lms.courses.materials.create', $course->id) }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <span class="btn-label">Add Material</span>
            </a>
        </div>
    </div>

    @if(session('success'))
    <div style="background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2); color: #15803d; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px; font-size: 14px; display: flex; align-items: center; gap: 8px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        {{ session('success') }}
    </div>
    @endif

    <div class="table-card" style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left; width: 60px;">Order</th>
                    <th style="text-align: left;">Title</th>
                    <th style="text-align: left;">Type</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($course->materials as $material)
                <tr>
                    <td style="color: var(--muted); font-weight: 600;">{{ $material->order }}</td>
                    <td style="font-weight: 600;">{{ $material->title }}</td>
                    <td>
                        <span style="padding: 4px 8px; background: var(--table-hover); color: var(--text); border-radius: 4px; font-size: 11px; font-weight: 600; text-transform: uppercase;">{{ $material->type }}</span>
                    </td>
                    <td style="text-align: right;">
                        <a href="{{ route('lms.courses.materials.edit', [$course->id, $material->id]) }}" class="btn btn-outline btn-sm" style="display: inline-flex; align-items: center; gap: 6px; margin-right: 8px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            <span>Edit</span>
                        </a>
                        <form action="{{ route('lms.courses.materials.destroy', [$course->id, $material->id]) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Delete this material?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline btn-sm" style="display: inline-flex; align-items: center; gap: 6px; color: #ef4444; border-color: rgba(239, 68, 68, 0.2);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                <span>Delete</span>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 32px; color: var(--muted);">
                        No materials added yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
