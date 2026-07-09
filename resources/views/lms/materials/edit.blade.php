@extends('lms.layout.app')
@section('title', 'Edit Material')
@section('content')
<div class="page-container" style="padding: 20px;">
    <div style="max-width: 800px; margin: 0 auto;">
        <div class="form-card">
            <h2 style="font-family: 'Inter', sans-serif; font-size: 20px; font-weight: 700; color: var(--text); margin: 0 0 24px 0;">Edit Material: {{ $material->title }}</h2>
            
            <form action="{{ route('lms.courses.materials.update', [$course->id, $material->id]) }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 20px;">
                @csrf @method('PUT')
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                    <div>
                        <label class="form-label" style="display: block; margin-bottom: 8px;">Title</label>
                        <input type="text" name="title" value="{{ $material->title }}" required class="form-control" style="width: 100%;">
                    </div>
                    <div>
                        <label class="form-label" style="display: block; margin-bottom: 8px;">Material Type</label>
                        <input type="text" value="{{ ucfirst($material->type) }}" disabled class="form-control" style="width: 100%; background: var(--body-bg); color: var(--muted);">
                    </div>
                </div>

                @if($material->type !== 'quiz')
                <div>
                    <label class="form-label" style="display: block; margin-bottom: 8px;">Replace File (Leave empty to keep current file)</label>
                    <input type="file" name="file" class="form-control" style="width: 100%; padding: 8px;">
                    @if($material->file_path)
                        <p style="font-size: 11px; color: var(--accent); margin: 6px 0 0 0;">Current file: <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank" style="color: var(--accent); text-decoration: underline;">View File</a></p>
                    @endif
                </div>
                @endif
                
                <div>
                    <label class="form-label" style="display: block; margin-bottom: 8px;">Text Content</label>
                    <textarea name="content" rows="4" class="form-control" style="width: 100%;">{{ $material->content }}</textarea>
                </div>
                
                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 16px;">
                    <a href="{{ route('lms.courses.show', $course->id) }}" class="btn btn-outline" style="text-decoration: none;">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Material</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
