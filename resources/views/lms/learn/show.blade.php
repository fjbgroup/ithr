@extends('lms.layout.app')
@section('title', $course->title)
@section('content')
<div class="lms-page-container">
    <div class="lms-hero" style="padding: 32px 32px; margin-bottom: 32px;">
        <div style="position: relative; z-index: 1;">
            <div class="lms-hero-title">{{ $course->title }}</div>
            <div class="lms-hero-subtitle">Code: {{ $course->code }}</div>
        </div>
    </div>

    <div style="display: flex; gap: 32px; flex-wrap: wrap;">
        <!-- Sidebar for Materials List -->
        <div style="flex: 1; min-width: 320px; max-width: 350px;">
            <div class="lms-sidebar-container">
                <div class="lms-sidebar-header">
                    <h3>Course Contents</h3>
                </div>
                <div style="max-height: 70vh; overflow-y: auto;">
                    @foreach($materials as $idx => $m)
                    @php
                        $isCompleted = isset($progress[$m->id]) && $progress[$m->id]->is_completed;
                        $isActive = request('material_id', $materials->first()->id ?? 0) == $m->id;
                        
                        $isLocked = false;
                        if ($idx > 0) {
                            $prevMaterial = $materials[$idx - 1];
                            $isLocked = !(isset($progress[$prevMaterial->id]) && $progress[$prevMaterial->id]->is_completed);
                        }
                    @endphp
                    
                    @if($isLocked)
                    <div class="lms-nav-item" style="opacity: 0.6; cursor: not-allowed;" title="Please complete the previous material first">
                    @else
                    <a href="?material_id={{ $m->id }}" class="lms-nav-item {{ $isActive ? 'active' : '' }} {{ $isCompleted ? 'completed' : '' }}">
                    @endif
                        <div class="lms-icon-wrap">
                            @if($isLocked)
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            @elseif($isCompleted)
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            @else
                                @if($m->type === 'video')
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                @elseif($m->type === 'pdf')
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                @elseif($m->type === 'quiz')
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                                @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                                @endif
                            @endif
                        </div>
                        <div style="flex: 1;">
                            <div class="lms-nav-title">{{ $idx + 1 }}. {{ $m->title }}</div>
                            <div class="lms-nav-type">{{ $m->type }}</div>
                        </div>
                    @if($isLocked)
                    </div>
                    @else
                    </a>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div style="flex: 2; min-width: 0;">
            @php
                $requestedId = request('material_id', $materials->first()->id ?? 0);
                $currentMaterial = $materials->firstWhere('id', $requestedId);
                
                if ($currentMaterial) {
                    $currIdx = $materials->search(function($m) use ($currentMaterial) {
                        return $m->id === $currentMaterial->id;
                    });
                    
                    if ($currIdx > 0) {
                        $prevMaterial = $materials[$currIdx - 1];
                        if (!(isset($progress[$prevMaterial->id]) && $progress[$prevMaterial->id]->is_completed)) {
                            $currentMaterial = null; // Locked
                        }
                    }
                }
                
                if (!$currentMaterial) {
                    // Find first incomplete
                    foreach ($materials as $m) {
                        if (!(isset($progress[$m->id]) && $progress[$m->id]->is_completed)) {
                            $currentMaterial = $m;
                            break;
                        }
                    }
                    if (!$currentMaterial) {
                        $currentMaterial = $materials->last();
                    }
                }
            @endphp
            
            @if($currentMaterial)
            <div class="lms-content-card">
                <div class="lms-content-header">
                    <h2 class="lms-content-title">{{ $currentMaterial->title }}</h2>
                    @if($currentMaterial->type !== 'quiz')
                        @if(isset($progress[$currentMaterial->id]) && $progress[$currentMaterial->id]->is_completed)
                        <div class="lms-status-badge lms-status-completed">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            Completed
                        </div>
                        @else
                        <button onclick="markComplete({{ $course->id }}, {{ $currentMaterial->id }})" class="lms-btn-play">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <span class="btn-label">Mark as Complete</span>
                        </button>
                        @endif
                    @endif
                </div>

                @if($currentMaterial->content)
                <div style="font-size: 1.05rem; color: var(--text); line-height: 1.8; margin-bottom: 32px;">
                    {!! nl2br(e($currentMaterial->content)) !!}
                </div>
                @endif

                @if($currentMaterial->type === 'video' && $currentMaterial->file_path)
                <div class="lms-media-container">
                    <video controls style="width: 100%; display: block;" onended="markComplete({{ $course->id }}, {{ $currentMaterial->id }})">
                        <source src="{{ asset('storage/' . $currentMaterial->file_path) }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
                @elseif($currentMaterial->type === 'pdf' && $currentMaterial->file_path)
                <div class="lms-media-container" style="height: 700px;">
                    <iframe src="{{ asset('storage/' . $currentMaterial->file_path) }}" style="width: 100%; height: 100%; border: none;"></iframe>
                </div>
                @elseif($currentMaterial->type === 'quiz')
                    @if(isset($progress[$currentMaterial->id]) && $progress[$currentMaterial->id]->is_completed)
                    <div class="lms-quiz-success">
                        <div style="display: flex; justify-content: center; margin-bottom: 24px;">
                            <div style="width: 80px; height: 80px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </div>
                        </div>
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin: 0 0 8px 0;">Quiz Completed!</h3>
                        <div class="lms-quiz-success-score">{{ $progress[$currentMaterial->id]->score }}%</div>
                        <p style="font-size: 1.1rem; opacity: 0.9; margin: 0;">You have successfully completed this quiz.</p>
                    </div>
                    @else
                    <form action="{{ route('lms.learn.quiz.submit', [$course->id, $currentMaterial->id]) }}" method="POST" id="quizForm">
                        @csrf
                        <div style="display: flex; flex-direction: column;">
                            @foreach($currentMaterial->questions as $idx => $q)
                            <div class="lms-quiz-question">
                                <div class="lms-quiz-qtext">{{ $idx + 1 }}. {{ $q->question }}</div>
                                <div class="lms-quiz-options">
                                    @foreach($q->options as $opt)
                                    <label class="lms-quiz-option">
                                        <input type="radio" name="answers[{{ $q->id }}]" value="{{ trim($opt) }}" required>
                                        <span class="lms-quiz-label">{{ trim($opt) }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div style="margin-top: 32px; display: flex; justify-content: flex-end;">
                            <button type="submit" class="lms-btn-play">
                                <span class="btn-label">Submit Answers</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
                            </button>
                        </div>
                    </form>
                    @endif
                @endif
            </div>
            @else
            <div class="lms-content-card" style="padding: 80px; text-align: center;">
                <div style="width: 80px; height: 80px; margin: 0 auto 24px; background: rgba(99, 102, 241, 0.1); color: var(--lms-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                </div>
                <h2 style="font-family: var(--lms-font-heading); font-size: 1.5rem; font-weight: 700; color: var(--text); margin: 0 0 8px 0;">No materials available.</h2>
                <p style="color: var(--muted); margin: 0;">Check back later when materials are added.</p>
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
        // Create confetti effect
        for(let i=0; i<30; i++) {
            let conf = document.createElement('div');
            conf.className = 'confetti';
            conf.style.left = Math.random() * 100 + 'vw';
            conf.style.backgroundColor = ['#6366f1', '#ec4899', '#14b8a6', '#f59e0b'][Math.floor(Math.random()*4)];
            conf.style.animation = `confettifall ${Math.random()*2 + 1}s ease-in forwards`;
            document.body.appendChild(conf);
        }
        
        if (data.courseCompleted) {
            setTimeout(() => {
                alert("Congratulations! You have completed the entire course.");
                window.location.reload();
            }, 500);
        } else {
            setTimeout(() => window.location.reload(), 1500);
        }
      }
    });
  }

  // Add confetti for successful quiz submission
  const quizForm = document.getElementById('quizForm');
  if(quizForm) {
      quizForm.addEventListener('submit', function() {
        for(let i=0; i<50; i++) {
            let conf = document.createElement('div');
            conf.className = 'confetti';
            conf.style.left = Math.random() * 100 + 'vw';
            conf.style.backgroundColor = ['#6366f1', '#ec4899', '#14b8a6', '#f59e0b'][Math.floor(Math.random()*4)];
            conf.style.animation = `confettifall ${Math.random()*2 + 1.5}s ease-in forwards`;
            document.body.appendChild(conf);
        }
      });
  }
</script>
@endpush
