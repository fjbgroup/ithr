@extends('lms.layout.app')
@section('title', 'Create Course')
@section('content')
<div class="max-w-3xl mx-auto">
  <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8">
    <h2 class="text-2xl font-bold text-slate-800 mb-6">Create Online Course</h2>
    
    <form action="{{ route('lms.courses.store') }}" method="POST">
      @csrf
      <div class="mb-4">
        <label class="block text-sm font-medium text-slate-700 mb-1">Course Code</label>
        <input type="text" name="code" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring focus:ring-indigo-200 focus:border-indigo-500">
      </div>
      
      <div class="mb-4">
        <label class="block text-sm font-medium text-slate-700 mb-1">Course Title</label>
        <input type="text" name="title" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring focus:ring-indigo-200 focus:border-indigo-500">
      </div>
      
      <div class="mb-4">
        <label class="block text-sm font-medium text-slate-700 mb-1">Duration (e.g. 2 Hours, 30 Mins)</label>
        <input type="text" name="duration" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring focus:ring-indigo-200 focus:border-indigo-500">
      </div>

      <div class="mb-6">
        <label class="block text-sm font-medium text-slate-700 mb-1">Assign PIC (Optional)</label>
        <select name="pic_id" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring focus:ring-indigo-200 focus:border-indigo-500">
          <option value="">-- No PIC / Admin Only --</option>
          @foreach($users as $u)
            <option value="{{ $u->id }}">{{ $u->name }}</option>
          @endforeach
        </select>
        <p class="text-xs text-slate-500 mt-1">PICs can manage this course's materials.</p>
      </div>
      
      <div class="flex justify-end gap-3">
        <a href="{{ route('lms.courses.index') }}" class="px-5 py-2 text-slate-600 font-medium hover:text-slate-800 transition">Cancel</a>
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-5 py-2 rounded-lg transition">Save Course</button>
      </div>
    </form>
  </div>
</div>
@endsection
