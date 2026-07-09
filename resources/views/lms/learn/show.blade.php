@extends('lms.layout.app')
@section('title', $course->title)
@section('content')
<div class="page-container" style="padding: 20px;">
    <div class="hd-banner" style="margin-bottom: 24px;">
        <div class="hd-banner-left">
            <div class="hd-greeting">
                {{ $course->title }}
            </div>
            <div class="hd-date">Code: {{ $course->code }}</div>
        </div>
    </div>

    <div style="display: flex; gap: 24px; flex-wrap: wrap;">
        <!-- Sidebar for Materials List -->
        <div style="flex: 1; min-width: 300px; max-width: 350px;">
            <div class="table-card" style="position: sticky; top: 80px;">
                <div style="padding: 16px; background: var(--table-head-bg); border-bottom: 1px solid var(--border);">
                    <h3 style="font-family: 'Inter', sans-serif; font-size: 15px; font-weight: 700; color: var(--text); margin: 0;">Course Contents</h3>
                </div>
                <ul style="list-style: none; margin: 0; padding: 0; max-height: 60vh; overflow-y: auto;">
                    @foreach($materials as $idx => $m)
                    @php
                        $isCompleted = isset($progress[$m->id]) && $progress[$m->id]->is_completed;
                        $isActive = request('material_id', $materials->first()->id ?? 0) == $m->id;
                    @endphp
                    <li style="border-bottom: 1px solid var(--border);">
                        <a href="?material_id={{ $m->id }}" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; text-decoration: none; transition: background 0.15s; {{ $isActive ? 'background: var(--table-hover); border-left: 3px solid var(--accent);' : 'background: var(--surface);' }}">
                            <div style="{{ $isCompleted ? 'color: #16a34a;' : 'color: var(--muted); opacity: 0.5;' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                            </div>
                            <div style="flex: 1;">
                                <p style="font-size: 13.5px; font-weight: 600; color: var(--text); margin: 0 0 2px 0;">{{ $idx + 1 }}. {{ $m->title }}</p>
                                <p style="font-size: 11px; color: var(--muted); text-transform: uppercase; margin: 0; letter-spacing: 0.05em;">{{ $m->type }}</p>
                            </div>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Main Content Area -->
        <div style="flex: 2; min-width: 0;">
            @php
                $currentMaterialId = request('material_id', $materials->first()->id ?? 0);
                $currentMaterial = $materials->firstWhere('id', $currentMaterialId);
            @endphp
            
            @if($currentMaterial)
            <div class="form-card">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px;">
                    <h2 style="font-family: 'Inter', sans-serif; font-size: 20px; font-weight: 700; color: var(--text); margin: 0;">{{ $currentMaterial->title }}</h2>
                    @if($currentMaterial->type !== 'quiz')
                        @if(isset($progress[$currentMaterial->id]) && $progress[$currentMaterial->id]->is_completed)
                        <span class="badge-status bs-active" style="display: inline-flex; align-items: center; gap: 4px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            Completed
                        </span>
                        @else
                        <button onclick="markComplete({{ $course->id }}, {{ $currentMaterial->id }})" class="btn btn-primary btn-sm">Mark as Complete</button>
                        @endif
                    @endif
                </div>

                @if($currentMaterial->content)
                <div style="font-size: 14px; color: var(--text); line-height: 1.6; margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid var(--border);">
                    {!! nl2br(e($currentMaterial->content)) !!}
                </div>
                @endif

                @if($currentMaterial->type === 'video' && $currentMaterial->file_path)
                <video controls style="width: 100%; border-radius: 8px; border: 1px solid var(--border); margin-bottom: 16px;" onended="markComplete({{ $course->id }}, {{ $currentMaterial->id }})">
                    <source src="{{ asset('storage/' . $currentMaterial->file_path) }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                @elseif($currentMaterial->type === 'pdf' && $currentMaterial->file_path)
                <iframe src="{{ asset('storage/' . $currentMaterial->file_path) }}" style="width: 100%; height: 600px; border-radius: 8px; border: 1px solid var(--border); margin-bottom: 16px;"></iframe>
                @elseif($currentMaterial->type === 'quiz')
                    @if(isset($progress[$currentMaterial->id]) && $progress[$currentMaterial->id]->is_completed)
                    <div style="background: rgba(34, 197, 94, 0.05); border: 1px solid rgba(34, 197, 94, 0.2); border-radius: 12px; padding: 32px; text-align: center;">
                        <h3 style="font-size: 18px; font-weight: 700; color: #16a34a; margin: 0 0 8px 0;">Quiz Completed!</h3>
                        <div style="font-size: 36px; font-weight: 800; color: #15803d; margin: 0 0 8px 0;">{{ $progress[$currentMaterial->id]->score }}%</div>
                        <p style="font-size: 14px; color: #16a34a; margin: 0;">You have successfully completed this quiz.</p>
                    </div>
                    @else
                    <form action="{{ route('lms.learn.quiz.submit', [$course->id, $currentMaterial->id]) }}" method="POST">
                        @csrf
                        <div style="display: flex; flex-direction: column; gap: 24px;">
                            @foreach($currentMaterial->questions as $idx => $q)
                            <div style="background: var(--body-bg); border: 1px solid var(--border); border-radius: 8px; padding: 20px;">
                                <p style="font-weight: 600; color: var(--text); margin: 0 0 16px 0;">{{ $idx + 1 }}. {{ $q->question }}</p>
                                <div style="display: flex; flex-direction: column; gap: 10px;">
                                    @foreach($q->options as $opt)
                                    <label style="display: flex; align-items: center; gap: 12px; padding: 12px; background: var(--surface); border: 1px solid var(--border); border-radius: 6px; cursor: pointer; transition: border-color 0.15s;">
                                        <input type="radio" name="answers[{{ $q->id }}]" value="{{ trim($opt) }}" required style="accent-color: var(--accent);">
                                        <span style="font-size: 13.5px; color: var(--text);">{{ trim($opt) }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div style="margin-top: 32px; display: flex; justify-content: flex-end;">
                            <button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-size: 14px;">Submit Quiz</button>
                        </div>
                    </form>
                    @endif
                @endif
            </div>
            @else
            <div class="form-card" style="padding: 48px; text-align: center;">
                <h2 style="font-size: 18px; font-weight: 600; color: var(--text); margin: 0;">No materials available.</h2>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
  function markComplete(courseId, materialId) {
    fetch(`/lms/learn/${courseId}/complete/${materialId}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      }
    })
    .then(res => res.json())
    .then(data => {
      if(data.success) {
        window.location.reload();
      }
    });
  }
</script>
@endpush
