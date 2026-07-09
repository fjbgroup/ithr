<?php

namespace App\Http\Controllers\LMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrainingCourse;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LmsCourseController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = TrainingCourse::online();

        if (!$user->isAdminHR() && !$user->isAdminIT()) {
            $query->where('pic_id', $user->id);
        }

        $courses = $query->latest()->get();
        return view('lms.courses.index', compact('courses'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get();
        return view('lms.courses.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:training_courses',
            'pic_id' => 'nullable|exists:users,id',
            'duration' => 'nullable|string',
        ]);

        $course = new TrainingCourse();
        $course->platform = 'LMS';
        $course->training_type = 'Online';
        $course->title = $request->title;
        $course->code = $request->code;
        $course->pic_id = $request->pic_id;
        $course->duration = $request->duration;
        $course->save();

        return redirect()->route('lms.courses.index')->with('success', 'Online course created successfully.');
    }

    public function edit(TrainingCourse $course)
    {
        $users = User::orderBy('name')->get();
        return view('lms.courses.edit', compact('course', 'users'));
    }

    public function update(Request $request, TrainingCourse $course)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:training_courses,code,'.$course->id,
            'pic_id' => 'nullable|exists:users,id',
            'duration' => 'nullable|string',
        ]);

        $course->title = $request->title;
        $course->code = $request->code;
        $course->pic_id = $request->pic_id;
        $course->duration = $request->duration;
        $course->save();

        return redirect()->route('lms.courses.index')->with('success', 'Course updated successfully.');
    }

    public function show(TrainingCourse $course)
    {
        return view('lms.courses.show', compact('course'));
    }

    public function destroy(TrainingCourse $course)
    {
        $course->delete();
        return redirect()->route('lms.courses.index')->with('success', 'Course deleted successfully.');
    }
}
