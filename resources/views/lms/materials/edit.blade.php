@extends('lms.layout.app')
@section('title', 'Edit Material')
@section('content')
<div class="max-w-4xl mx-auto">
  <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8">
    <h2 class="text-2xl font-bold text-slate-800 mb-6">Edit Material: {{ $material->title }}</h2>
    
    <form action="{{ route('lms.courses.materials.update', [$course->id, $material->id]) }}" method="POST" enctype="multipart/form-data">
      @csrf @method('PUT')
      <div class="grid grid-cols-2 gap-6 mb-6">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Title</label>
          <input type="text" name="title" value="{{ $material->title }}" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring focus:ring-indigo-200">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Material Type</label>
          <input type="text" value="{{ ucfirst($material->type) }}" disabled class="w-full px-4 py-2 border border-slate-300 rounded-lg bg-slate-50 text-slate-500">
        </div>
      </div>

      @if($material->type !== 'quiz')
      <div class="mb-6">
        <label class="block text-sm font-medium text-slate-700 mb-1">Replace File (Leave empty to keep current file)</label>
        <input type="file" name="file" class="w-full border border-slate-300 rounded-lg p-2 text-sm">
        @if($material->file_path)
          <p class="text-xs text-blue-600 mt-1">Current file: <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank">View File</a></p>
        @endif
      </div>
      @endif
      
      <div class="mb-6">
        <label class="block text-sm font-medium text-slate-700 mb-1">Text Content</label>
        <textarea name="content" rows="4" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring focus:ring-indigo-200">{{ $material->content }}</textarea>
      </div>
      
      <div class="flex justify-end gap-3">
        <a href="{{ route('lms.courses.show', $course->id) }}" class="px-5 py-2 text-slate-600 font-medium hover:text-slate-800 transition">Cancel</a>
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-5 py-2 rounded-lg transition">Update Material</button>
      </div>
    </form>
  </div>
</div>
@endsection
