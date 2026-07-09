@extends('lms.layout.app')
@section('title', 'Manage Courses')
@section('content')
<div class="max-w-6xl mx-auto">
  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-slate-800">Manage Online Courses</h2>
    <a href="{{ route('lms.courses.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition">+ New Course</a>
  </div>

  <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <table class="w-full text-left">
      <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
        <tr>
          <th class="p-4">Code</th>
          <th class="p-4">Title</th>
          <th class="p-4">PIC</th>
          <th class="p-4 text-right">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        @foreach($courses as $course)
        <tr class="hover:bg-slate-50 transition">
          <td class="p-4 font-medium text-slate-800">{{ $course->code }}</td>
          <td class="p-4 text-slate-600">{{ $course->title }}</td>
          <td class="p-4 text-slate-600">{{ $course->pic ? $course->pic->name : '-' }}</td>
          <td class="p-4 text-right space-x-2">
            <a href="{{ route('lms.courses.show', $course->id) }}" class="text-blue-600 hover:underline">Manage</a>
            <a href="{{ route('lms.courses.edit', $course->id) }}" class="text-indigo-600 hover:underline">Edit</a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection