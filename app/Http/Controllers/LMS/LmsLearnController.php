<?php

namespace App\Http\Controllers\LMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrainingCourse;
use App\Models\LmsMaterial;
use App\Models\LmsProgress;
use App\Models\TrainingAttendance;
use Illuminate\Support\Facades\Auth;

class LmsLearnController extends Controller
{
    public function show(TrainingCourse $course)
    {
        $user = Auth::user();
        
        if (!$user->staff) {
            return redirect()->back()->with('error', 'You must have a staff record to access courses.');
        }

        $materials = $course->materials()->get();
        $progress = LmsProgress::where('staff_id', $user->staff->id)
            ->where('course_id', $course->id)
            ->get()
            ->keyBy('material_id');

        return view('lms.learn.show', compact('course', 'materials', 'progress'));
    }

    public function completeMaterial(Request $request, TrainingCourse $course, LmsMaterial $material)
    {
        $user = Auth::user();
        if (!$user->staff) return response()->json(['success' => false, 'message' => 'Not a staff member'], 403);

        $progress = LmsProgress::firstOrCreate(
            ['staff_id' => $user->staff->id, 'course_id' => $course->id, 'material_id' => $material->id]
        );

        $progress->is_completed = true;
        $progress->save();

        $courseCompleted = $this->checkCourseCompletion($course, $user->staff);

        return response()->json(['success' => true, 'courseCompleted' => $courseCompleted]);
    }

    public function submitQuiz(Request $request, TrainingCourse $course, LmsMaterial $material)
    {
        $user = Auth::user();
        if (!$user->staff) return redirect()->back()->with('error', 'Not a staff member');
        
        if ($material->type !== 'quiz') {
            return redirect()->back()->with('error', 'Invalid material type');
        }

        $answers = $request->input('answers', []);
        $correctCount = 0;
        $totalQuestions = $material->questions->count();

        foreach ($material->questions as $q) {
            if (isset($answers[$q->id]) && trim($answers[$q->id]) === trim($q->correct_answer)) {
                $correctCount++;
            }
        }

        $score = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100) : 0;

        $progress = LmsProgress::firstOrCreate(
            ['staff_id' => $user->staff->id, 'course_id' => $course->id, 'material_id' => $material->id]
        );

        $progress->is_completed = true;
        $progress->score = $score;
        $progress->save();

        $courseCompleted = $this->checkCourseCompletion($course, $user->staff);
        
        $msg = "Quiz completed! You scored {$score}%.";
        if ($courseCompleted) {
            $msg .= " Congratulations! You have completed the entire course.";
        }

        return redirect()->back()->with('success', $msg);
    }

    private function checkCourseCompletion(TrainingCourse $course, $staff)
    {
        $totalMaterials = $course->materials()->count();
        if ($totalMaterials === 0) return false;

        $completedCount = LmsProgress::where('staff_id', $staff->id)
            ->where('course_id', $course->id)
            ->where('is_completed', true)
            ->count();

        if ($completedCount >= $totalMaterials) {
            $attendance = TrainingAttendance::where('staff_id', $staff->id)
                ->where('course_id', $course->id)
                ->first();
                
            if ($attendance) {
                if ($attendance->status !== 'Completed') {
                    $attendance->update(['status' => 'Completed']);
                }
            } else {
                TrainingAttendance::create([
                    'staff_id' => $staff->id,
                    'course_id' => $course->id,
                    'status' => 'Completed',
                    'training_type' => $course->training_type ?? 'Internal',
                    'created_by' => Auth::id() ?? 1,
                ]);
            }
            return true;
        }
        return false;
    }
}
