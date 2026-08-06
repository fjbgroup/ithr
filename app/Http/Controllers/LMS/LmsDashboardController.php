<?php

namespace App\Http\Controllers\LMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TrainingCourse;

class LmsDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $globalYear = session('global_year', 'all');
        $applyYear = function ($query) use ($globalYear) {
            if ($globalYear !== 'all') {
                $query->whereYear('created_at', $globalYear);
            }
        };
        
        // Courses where the user's staff record is enrolled
        $enrolledCourses = collect();
        $availableCourses = collect();
        if ($user->staff) {
            $enrolledCourses = $user->staff->courses()
                ->where('platform', 'LMS')
                ->where($applyYear)
                ->get();
                
            $availableCourses = TrainingCourse::online()
                ->where('is_open_enrollment', true)
                ->whereNotIn('id', $enrolledCourses->pluck('id'))
                ->where($applyYear)
                ->get();
        }

        // If user is Admin or PIC, they can manage courses
        $isManager = $user->isAdminHR() || $user->isAdminIT() || TrainingCourse::where('pic_id', $user->id)->exists();

        return view('lms.dashboard', compact('enrolledCourses', 'availableCourses', 'isManager'));
    }
}
