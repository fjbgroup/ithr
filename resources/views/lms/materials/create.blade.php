@extends('lms.layout.app')
@section('title', 'Add Material')
@section('content')
<div class="max-w-4xl mx-auto">
  <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8">
    <h2 class="text-2xl font-bold text-slate-800 mb-6">Add Material to: {{ $course->title }}</h2>
    
    <form action="{{ route('lms.courses.materials.store', $course->id) }}" method="POST" enctype="multipart/form-data" id="material-form">
      @csrf
      <div class="grid grid-cols-2 gap-6 mb-6">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Title</label>
          <input type="text" name="title" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring focus:ring-indigo-200">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Material Type</label>
          <select name="type" id="material-type" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring focus:ring-indigo-200">
            <option value="video">Video</option>
            <option value="pdf">PDF</option>
            <option value="quiz">Quiz</option>
          </select>
        </div>
      </div>

      <!-- File Upload (Video/PDF) -->
      <div id="file-upload-section" class="mb-6">
        <label class="block text-sm font-medium text-slate-700 mb-1">Upload File (PDF or Video max 50MB)</label>
        <input type="file" name="file" class="w-full border border-slate-300 rounded-lg p-2 text-sm">
      </div>
      
      <!-- Text Content (Optional) -->
      <div id="text-content-section" class="mb-6">
        <label class="block text-sm font-medium text-slate-700 mb-1">Text Content (Optional details)</label>
        <textarea name="content" rows="4" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring focus:ring-indigo-200"></textarea>
      </div>

      <!-- Quiz Questions (Only for Quiz) -->
      <div id="quiz-section" class="mb-6 hidden bg-slate-50 p-4 rounded-lg border border-slate-200">
        <h3 class="font-bold text-slate-700 mb-3">Quiz Questions</h3>
        <p class="text-sm text-slate-500 mb-4">Add multiple-choice questions here.</p>
        
        <div id="questions-container">
          <!-- Question 1 -->
          <div class="mb-4 pb-4 border-b border-slate-200">
            <input type="text" name="questions[0][question]" placeholder="Question text" class="w-full px-3 py-2 border border-slate-300 rounded-lg mb-2">
            <input type="text" name="questions[0][options]" placeholder="Options (comma separated: A, B, C, D)" class="w-full px-3 py-2 border border-slate-300 rounded-lg mb-2">
            <input type="text" name="questions[0][correct_answer]" placeholder="Correct Answer (must match one option exactly)" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
          </div>
        </div>
        <button type="button" id="add-question" class="text-sm font-medium text-indigo-600 hover:underline">+ Add another question</button>
      </div>
      
      <div class="flex justify-end gap-3">
        <a href="{{ route('lms.courses.show', $course->id) }}" class="px-5 py-2 text-slate-600 font-medium hover:text-slate-800 transition">Cancel</a>
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-5 py-2 rounded-lg transition">Save Material</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.getElementById('material-type').addEventListener('change', function() {
    const val = this.value;
    const fileSec = document.getElementById('file-upload-section');
    const txtSec = document.getElementById('text-content-section');
    const quizSec = document.getElementById('quiz-section');
    
    if (val === 'quiz') {
      fileSec.classList.add('hidden');
      quizSec.classList.remove('hidden');
    } else {
      fileSec.classList.remove('hidden');
      quizSec.classList.add('hidden');
    }
  });

  let qCount = 1;
  document.getElementById('add-question').addEventListener('click', function() {
    const container = document.getElementById('questions-container');
    const div = document.createElement('div');
    div.className = 'mb-4 pb-4 border-b border-slate-200';
    div.innerHTML = `
      <input type="text" name="questions[${qCount}][question]" placeholder="Question text" class="w-full px-3 py-2 border border-slate-300 rounded-lg mb-2">
      <input type="text" name="questions[${qCount}][options]" placeholder="Options (comma separated: A, B, C, D)" class="w-full px-3 py-2 border border-slate-300 rounded-lg mb-2">
      <input type="text" name="questions[${qCount}][correct_answer]" placeholder="Correct Answer (must match one option exactly)" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
    `;
    container.appendChild(div);
    qCount++;
  });
</script>
@endpush
