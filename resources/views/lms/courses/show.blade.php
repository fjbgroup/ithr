@extends('lms.layout.app')
@section('title', 'Course Materials')
@section('content')
<div class="max-w-5xl mx-auto">
  <div class="flex justify-between items-end mb-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-800">{{ $course->title }}</h2>
      <p class="text-slate-500">Code: {{ $course->code }} | PIC: {{ $course->pic ? $course->pic->name : 'None' }}</p>
    </div>
    <div class="flex gap-2">
      <a href="{{ route('lms.courses.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-600 hover:bg-slate-50 transition font-medium">Back</a>
      <a href="{{ route('lms.courses.materials.create', $course->id) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition font-medium">+ Add Material</a>
    </div>
  </div>

  @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
      {{ session('success') }}
    </div>
  @endif

  <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <table class="w-full text-left">
      <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
        <tr>
          <th class="p-4 w-16">Order</th>
          <th class="p-4">Title</th>
          <th class="p-4">Type</th>
          <th class="p-4 text-right">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($course->materials as $material)
        <tr class="hover:bg-slate-50 transition">
          <td class="p-4 text-slate-500 font-medium">{{ $material->order }}</td>
          <td class="p-4 text-slate-800 font-medium">{{ $material->title }}</td>
          <td class="p-4">
            <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded text-xs font-semibold uppercase">{{ $material->type }}</span>
          </td>
          <td class="p-4 text-right space-x-2">
            <a href="{{ route('lms.courses.materials.edit', [$course->id, $material->id]) }}" class="text-indigo-600 hover:underline">Edit</a>
            <form action="{{ route('lms.courses.materials.destroy', [$course->id, $material->id]) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this material?');">
              @csrf @method('DELETE')
              <button type="submit" class="text-red-500 hover:underline">Delete</button>
            </form>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="4" class="p-8 text-center text-slate-500">No materials added yet.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
