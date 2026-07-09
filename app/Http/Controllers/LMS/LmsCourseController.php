<?php

namespace App\Http\Controllers\LMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrainingCourse;
use App\Models\User;
use App\Models\Company;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\AuditLogger;

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
        $users = User::orderBy('name')->get();
        $companies = Company::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        
        return view('lms.courses.index', compact('courses', 'users', 'companies', 'departments'));
    }



    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'         => 'required|string|max:500',
            'training_type' => 'required|in:Internal,External',
            'company'       => 'nullable|string|max:100',
            'department'    => 'nullable|string|max:100',
            'start_date'    => 'nullable|date',
            'end_date'      => 'nullable|date',
            'duration'      => 'nullable|string|max:100',
            'pic_id'        => 'nullable|exists:users,id',
        ]);

        if (empty($validated['end_date']) && !empty($validated['start_date'])) {
            $validated['end_date'] = $validated['start_date'];
            if (empty($validated['duration'])) $validated['duration'] = '1 day';
        }

        $prefix = $validated['training_type'] === 'Internal' ? 'INT' : 'EXT';
        $maxSeq = DB::table('training_courses')
            ->where('code', 'like', $prefix . '%')
            ->pluck('code')
            ->filter(fn($c) => preg_match('/^[A-Z]{3}[0-9]+$/', $c))
            ->map(fn($c) => (int)substr($c, 3))
            ->max();
        $validated['code'] = $prefix . str_pad((int)($maxSeq ?? 0) + 1, 3, '0', STR_PAD_LEFT);
        
        $validated['platform'] = 'LMS';

        $course = TrainingCourse::create($validated);

        AuditLogger::log('create', 'training',
            'Created online course "' . $course->title . '" (' . $course->code . ').',
            ['course_id' => $course->id, 'type' => $course->training_type]
        );

        return redirect()->route('lms.courses.index')->with('success', 'Online course created successfully.');
    }



    public function update(Request $request, TrainingCourse $course)
    {
        $validated = $request->validate([
            'title'         => 'required|string|max:500',
            'training_type' => 'required|in:Internal,External',
            'company'       => 'nullable|string|max:100',
            'department'    => 'nullable|string|max:100',
            'start_date'    => 'nullable|date',
            'end_date'      => 'nullable|date',
            'duration'      => 'nullable|string|max:100',
            'pic_id'        => 'nullable|exists:users,id',
        ]);

        if (empty($validated['end_date']) && !empty($validated['start_date'])) {
            $validated['end_date'] = $validated['start_date'];
            if (empty($validated['duration'])) $validated['duration'] = '1 day';
        }

        $course->update($validated);

        AuditLogger::log('update', 'training',
            'Updated online course "' . $course->title . '" (' . $course->code . ').',
            ['course_id' => $course->id]
        );

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
