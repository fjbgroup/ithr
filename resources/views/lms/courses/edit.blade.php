@extends('lms.layout.app')
@section('title', 'Edit Course')
@section('content')
<div class="page-container" style="padding: 20px;">
    <div style="max-width: 800px; margin: 0 auto;">
        <div class="form-card">
            <h2 style="font-family: 'Inter', sans-serif; font-size: 20px; font-weight: 700; color: var(--text); margin: 0 0 24px 0;">Edit Course: {{ $course->title }}</h2>
            
            <form action="{{ route('lms.courses.update', $course->id) }}" method="POST" style="display: flex; flex-direction: column; gap: 20px;">
                @csrf
                @method('PUT')
                <div>
                    <label class="form-label" style="display: block; margin-bottom: 8px;">Course Code</label>
                    <input type="text" name="code" value="{{ $course->code }}" required class="form-control" style="width: 100%;">
                </div>
                
                <div>
                    <label class="form-label" style="display: block; margin-bottom: 8px;">Course Title</label>
                    <input type="text" name="title" value="{{ $course->title }}" required class="form-control" style="width: 100%;">
                </div>
                
                <div>
                    <label class="form-label" style="display: block; margin-bottom: 8px;">Duration (e.g. 2 Hours, 30 Mins)</label>
                    <input type="text" name="duration" value="{{ $course->duration }}" class="form-control" style="width: 100%;">
                </div>

                <div>
                    <label class="form-label" style="display: block; margin-bottom: 8px;">Assign PIC (Optional)</label>
                    <select name="pic_id" class="form-select" style="width: 100%;">
                        <option value="">-- No PIC / Admin Only --</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ $course->pic_id == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 16px;">
                    <a href="{{ route('lms.courses.index') }}" class="btn btn-outline" style="text-decoration: none;">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Course</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
