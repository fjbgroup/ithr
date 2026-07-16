@extends('lms.layout.app')
@section('title', 'Edit Material')
@section('content')
<div class="page-container" style="padding: 20px;">
    <div style="max-width: 800px; margin: 0 auto;">
        <div class="form-card">
            <h2 style="font-family: 'Inter', sans-serif; font-size: 20px; font-weight: 700; color: var(--text); margin: 0 0 24px 0;">Edit Material: {{ $material->title }}</h2>
            
            <form action="{{ route('lms.courses.materials.update', [$course->id, $material->id]) }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 20px;">
                @csrf @method('PUT')
                @if($errors->any())
                    <div style="background: #fef2f2; color: #dc2626; padding: 12px; border-radius: 6px; border: 1px solid #fecaca; font-size: 13px;">
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
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
                
                @if($material->type === 'quiz')
                <!-- Quiz Questions (Only for Quiz) -->
                <div id="quiz-section" style="background: var(--body-bg); border: 1px solid var(--border); border-radius: 8px; padding: 20px;">
                    <h3 style="font-family: 'Inter', sans-serif; font-size: 15px; font-weight: 700; color: var(--text); margin: 0 0 8px 0;">Quiz Questions</h3>
                    <p style="font-size: 12px; color: var(--muted); margin: 0 0 16px 0;">Edit multiple-choice questions. Use the radio button to select the correct answer.</p>
                    
                    <div id="questions-container" style="display: flex; flex-direction: column; gap: 24px;">
                        @forelse($material->questions as $qIdx => $q)
                        <div class="question-block" style="padding-bottom: 20px; border-bottom: 1px solid var(--border);">
                            <input type="text" name="questions[{{ $qIdx }}][question]" value="{{ $q->question }}" placeholder="Question text" required class="form-control" style="width: 100%; margin-bottom: 12px; font-weight: 600;">
                            
                            <div class="options-container" style="display: flex; flex-direction: column; gap: 8px;">
                                @foreach($q->options as $optIdx => $opt)
                                @php $isCorrect = trim($opt) === trim($q->correct_answer); @endphp
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <input type="radio" name="questions[{{ $qIdx }}][correct_index]" value="{{ $optIdx }}" {{ $isCorrect ? 'checked' : '' }} required title="Mark as correct answer" style="accent-color: var(--accent); width: 16px; height: 16px;">
                                    <input type="text" name="questions[{{ $qIdx }}][options][]" value="{{ $opt }}" placeholder="Option {{ chr(65 + $optIdx) }}" required class="form-control" style="flex: 1;">
                                </div>
                                @endforeach
                            </div>
                            <button type="button" class="add-option-btn" onclick="addOption({{ $qIdx }}, this)" style="margin-top: 10px; background: none; border: none; color: var(--accent); font-size: 12px; font-weight: 600; cursor: pointer;">+ Add Option</button>
                        </div>
                        @empty
                        <!-- Question 0 -->
                        <div class="question-block" style="padding-bottom: 20px; border-bottom: 1px solid var(--border);">
                            <input type="text" name="questions[0][question]" placeholder="Question text" required class="form-control" style="width: 100%; margin-bottom: 12px; font-weight: 600;">
                            
                            <div class="options-container" style="display: flex; flex-direction: column; gap: 8px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <input type="radio" name="questions[0][correct_index]" value="0" required title="Mark as correct answer" style="accent-color: var(--accent); width: 16px; height: 16px;">
                                    <input type="text" name="questions[0][options][]" placeholder="Option A" required class="form-control" style="flex: 1;">
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <input type="radio" name="questions[0][correct_index]" value="1" required title="Mark as correct answer" style="accent-color: var(--accent); width: 16px; height: 16px;">
                                    <input type="text" name="questions[0][options][]" placeholder="Option B" required class="form-control" style="flex: 1;">
                                </div>
                            </div>
                            <button type="button" class="add-option-btn" onclick="addOption(0, this)" style="margin-top: 10px; background: none; border: none; color: var(--accent); font-size: 12px; font-weight: 600; cursor: pointer;">+ Add Option</button>
                        </div>
                        @endforelse
                    </div>
                    <button type="button" id="add-question" style="margin-top: 16px; background: none; border: none; color: var(--accent); font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: underline;">+ Add another question</button>
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

@section('scripts')
<script>
  let qCount = {{ $material->questions ? max(1, $material->questions->count()) : 1 }};
  
  const addQuestionBtn = document.getElementById('add-question');
  if (addQuestionBtn) {
    addQuestionBtn.addEventListener('click', function() {
        const container = document.getElementById('questions-container');
        const div = document.createElement('div');
        div.className = 'question-block';
        div.style = 'padding-bottom: 20px; border-bottom: 1px solid var(--border);';
        div.innerHTML = `
        <input type="text" name="questions[${qCount}][question]" placeholder="Question text" required class="form-control" style="width: 100%; margin-bottom: 12px; font-weight: 600;">
        <div class="options-container" style="display: flex; flex-direction: column; gap: 8px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <input type="radio" name="questions[${qCount}][correct_index]" value="0" required title="Mark as correct answer" style="accent-color: var(--accent); width: 16px; height: 16px;">
                <input type="text" name="questions[${qCount}][options][]" placeholder="Option A" required class="form-control" style="flex: 1;">
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <input type="radio" name="questions[${qCount}][correct_index]" value="1" required title="Mark as correct answer" style="accent-color: var(--accent); width: 16px; height: 16px;">
                <input type="text" name="questions[${qCount}][options][]" placeholder="Option B" required class="form-control" style="flex: 1;">
            </div>
        </div>
        <button type="button" class="add-option-btn" onclick="addOption(${qCount}, this)" style="margin-top: 10px; background: none; border: none; color: var(--accent); font-size: 12px; font-weight: 600; cursor: pointer;">+ Add Option</button>
        `;
        container.appendChild(div);
        qCount++;
    });
  }

  window.addOption = function(qIndex, btn) {
    const container = btn.previousElementSibling;
    const optCount = container.children.length;
    const div = document.createElement('div');
    div.style = 'display: flex; align-items: center; gap: 8px;';
    div.innerHTML = `
        <input type="radio" name="questions[${qIndex}][correct_index]" value="${optCount}" required title="Mark as correct answer" style="accent-color: var(--accent); width: 16px; height: 16px;">
        <input type="text" name="questions[${qIndex}][options][]" placeholder="Option ${String.fromCharCode(65 + optCount)}" required class="form-control" style="flex: 1;">
    `;
    container.appendChild(div);
  };
</script>
@endsection
