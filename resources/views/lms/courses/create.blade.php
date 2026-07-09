@extends('lms.layout.app')
@section('title', 'Create Course')
@section('content')
<div class="page-container" style="padding: 20px;">
    <div style="max-width: 800px; margin: 0 auto;">
        <div class="form-card">
            <h2 style="font-family: 'Inter', sans-serif; font-size: 20px; font-weight: 700; color: var(--text); margin: 0 0 24px 0;">Create Online Course</h2>
            
            <form action="{{ route('lms.courses.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 20px;">
                @csrf
                <div>
                    <label class="form-label" style="display: block; margin-bottom: 8px;">Course Code</label>
                    <input type="text" name="code" required class="form-control" style="width: 100%;">
                </div>
                
                <div>
                    <label class="form-label" style="display: block; margin-bottom: 8px;">Course Title</label>
                    <input type="text" name="title" required class="form-control" style="width: 100%;">
                </div>
                
                <div>
                    <label class="form-label" style="display: block; margin-bottom: 8px;">Duration (e.g. 2 Hours, 30 Mins)</label>
                    <input type="text" name="duration" class="form-control" style="width: 100%;">
                </div>

                <div>
                    <label class="form-label" style="display: block; margin-bottom: 8px;">Assign PIC (Optional)</label>
                    <select name="pic_id" class="form-select" style="width: 100%;">
                        <option value="">-- No PIC / Admin Only --</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                    <p style="font-size: 11px; color: var(--muted); margin: 6px 0 0 0;">PICs can manage this course's materials.</p>
                </div>
                
                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 16px;">
                    <a href="{{ route('lms.courses.index') }}" class="btn btn-outline" style="text-decoration: none;">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Course</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
