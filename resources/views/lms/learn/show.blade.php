@extends('lms.layout.app')
@section('title', $course->title)
@section('content')
<div class="max-w-6xl mx-auto flex flex-col md:flex-row gap-8">
  
  <!-- Sidebar for Materials List -->
  <div class="w-full md:w-1/3 order-2 md:order-1">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden sticky top-24">
      <div class="p-4 bg-slate-50 border-b border-slate-200">
        <h3 class="font-bold text-slate-800">Course Contents</h3>
      </div>
      <ul class="divide-y divide-slate-100 max-h-[60vh] overflow-y-auto">
        @foreach($materials as $idx => $m)
          @php
            $isCompleted = isset($progress[$m->id]) && $progress[$m->id]->is_completed;
          @endphp
          <li>
            <a href="?material_id={{ $m->id }}" class="flex items-center gap-3 p-4 hover:bg-slate-50 transition {{ request('material_id', $materials->first()->id ?? 0) == $m->id ? 'bg-indigo-50 border-l-4 border-indigo-600' : '' }}">
              <div class="{{ $isCompleted ? 'text-green-500' : 'text-slate-300' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              </div>
              <div class="flex-1">
                <p class="text-sm font-medium {{ $isCompleted ? 'text-slate-500' : 'text-slate-700' }}">{{ $idx + 1 }}. {{ $m->title }}</p>
                <p class="text-xs text-slate-400 uppercase">{{ $m->type }}</p>
              </div>
            </a>
          </li>
        @endforeach
      </ul>
    </div>
  </div>

  <!-- Main Content Area -->
  <div class="w-full md:w-2/3 order-1 md:order-2">
    @php
      $currentMaterialId = request('material_id', $materials->first()->id ?? 0);
      $currentMaterial = $materials->firstWhere('id', $currentMaterialId);
    @endphp
    
    @if($currentMaterial)
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
        <div class="flex justify-between items-start mb-6">
          <h2 class="text-2xl font-bold text-slate-800">{{ $currentMaterial->title }}</h2>
          @if($currentMaterial->type !== 'quiz')
            @if(isset($progress[$currentMaterial->id]) && $progress[$currentMaterial->id]->is_completed)
              <span class="inline-flex items-center gap-1 text-green-600 font-medium text-sm bg-green-50 px-3 py-1 rounded-full">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Completed
              </span>
            @else
              <button onclick="markComplete({{ $course->id }}, {{ $currentMaterial->id }})" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-4 py-2 rounded-lg text-sm transition">Mark as Complete</button>
            @endif
          @endif
        </div>

        @if($currentMaterial->content)
          <div class="prose max-w-none text-slate-600 mb-6 border-b border-slate-100 pb-6">
            {!! nl2br(e($currentMaterial->content)) !!}
          </div>
        @endif

        @if($currentMaterial->type === 'video' && $currentMaterial->file_path)
          <video controls class="w-full rounded-xl shadow-sm mb-4" onended="markComplete({{ $course->id }}, {{ $currentMaterial->id }})">
            <source src="{{ asset('storage/' . $currentMaterial->file_path) }}" type="video/mp4">
            Your browser does not support the video tag.
          </video>
        @elseif($currentMaterial->type === 'pdf' && $currentMaterial->file_path)
          <iframe src="{{ asset('storage/' . $currentMaterial->file_path) }}" class="w-full h-[600px] rounded-xl border border-slate-200 mb-4"></iframe>
        @elseif($currentMaterial->type === 'quiz')
          
          @if(isset($progress[$currentMaterial->id]) && $progress[$currentMaterial->id]->is_completed)
            <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-8 text-center">
              <h3 class="text-xl font-bold text-indigo-900 mb-2">Quiz Completed!</h3>
              <div class="text-4xl font-black text-indigo-600 mb-2">{{ $progress[$currentMaterial->id]->score }}%</div>
              <p class="text-indigo-700 text-sm">You have successfully completed this quiz.</p>
            </div>
          @else
            <form action="{{ route('lms.learn.quiz.submit', [$course->id, $currentMaterial->id]) }}" method="POST">
              @csrf
              <div class="space-y-6">
                @foreach($currentMaterial->questions as $idx => $q)
                  <div class="bg-slate-50 border border-slate-200 rounded-lg p-5">
                    <p class="font-medium text-slate-800 mb-3">{{ $idx + 1 }}. {{ $q->question }}</p>
                    <div class="space-y-2">
                      @foreach($q->options as $opt)
                        <label class="flex items-center gap-3 p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:border-indigo-400 transition">
                          <input type="radio" name="answers[{{ $q->id }}]" value="{{ trim($opt) }}" required class="text-indigo-600 w-4 h-4">
                          <span class="text-slate-700 text-sm">{{ trim($opt) }}</span>
                        </label>
                      @endforeach
                    </div>
                  </div>
                @endforeach
              </div>
              <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-xl shadow-md transition transform hover:-translate-y-0.5">Submit Quiz</button>
              </div>
            </form>
          @endif
        @endif

      </div>
    @else
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
        <h2 class="text-xl font-bold text-slate-700">No materials available.</h2>
      </div>
    @endif
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
