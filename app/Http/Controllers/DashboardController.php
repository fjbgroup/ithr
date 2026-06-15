<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\TrainingAttendance;
use App\Models\RoomBooking;
use App\Models\UpdateRequest;
use App\Models\Department;
use App\Models\FamilyMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin() || $user->isCeo()) {
            $totalStaff = Staff::count();
            $totalTraining = TrainingAttendance::join('training_courses', 'training_attendances.course_id', '=', 'training_courses.id')
                ->whereYear('training_courses.start_date', date('Y'))
                ->count();
            $totalBookings = RoomBooking::where('booking_date', '>=', date('Y-m-d'))->count();
            $pendingReqs = UpdateRequest::where('status', 'Pending')->count();

            // Training type split
            $typeSplit = TrainingAttendance::join('training_courses', 'training_attendances.course_id', '=', 'training_courses.id')
                ->whereYear('training_courses.start_date', date('Y'))
                ->select(
                    DB::raw("SUM(COALESCE(training_attendances.training_type, training_courses.training_type, 'External') = 'External') as ext_cnt"),
                    DB::raw("SUM(COALESCE(training_attendances.training_type, training_courses.training_type, 'External') = 'Internal') as int_cnt")
                )->first();
            $extCnt = (int)($typeSplit->ext_cnt ?? 0);
            $intCnt = (int)($typeSplit->int_cnt ?? 0);

            // Monthly trend (last 6 months)
            $monthTrend = TrainingAttendance::join('training_courses', 'training_attendances.course_id', '=', 'training_courses.id')
                ->where('training_courses.start_date', '>=', now()->subMonths(5)->startOfMonth())
                ->select(
                    DB::raw("DATE_FORMAT(training_courses.start_date, '%b') as lbl"),
                    DB::raw("DATE_FORMAT(training_courses.start_date, '%Y-%m') as ym"),
                    DB::raw("COUNT(*) as cnt")
                )->groupBy('ym', 'lbl')->orderBy('ym')->get();

            // Top 5 departments
            $topDepts = Department::join('staff', 'staff.department_id', '=', 'departments.id')
                ->join('training_attendances', 'training_attendances.staff_id', '=', 'staff.id')
                ->join('training_courses', 'training_attendances.course_id', '=', 'training_courses.id')
                ->whereYear('training_courses.start_date', date('Y'))
                ->select('departments.name', 'departments.company', DB::raw("COUNT(training_attendances.id) as cnt"))
                ->groupBy('departments.id', 'departments.name', 'departments.company')
                ->orderBy('cnt', 'DESC')->limit(5)->get();

            // Pending requests
            $pendingList = UpdateRequest::where('status', 'Pending')->latest()->limit(4)->get();

            // Recent training
            $recentTraining = TrainingAttendance::with(['staff.department', 'course'])
                ->join('staff', 'training_attendances.staff_id', '=', 'staff.id')
                ->join('training_courses', 'training_attendances.course_id', '=', 'training_courses.id')
                ->select('training_attendances.*', 'staff.name as emp_name', 'training_courses.code as training_code', 'training_courses.title as training_title')
                ->latest('training_attendances.created_at')->limit(5)->get();

            $todayBookings = RoomBooking::with('room')
                ->where('booking_date', date('Y-m-d'))
                ->orderBy('start_time')->get();

            return view('dashboard.index', compact(
                'totalStaff', 'totalTraining', 'totalBookings', 'pendingReqs',
                'extCnt', 'intCnt', 'monthTrend', 'topDepts', 'pendingList', 'recentTraining', 'todayBookings'
            ));

        } else {
            // Staff logic
            $myStaff = Staff::where('staff_no', $user->staff_no)->first();
            $myStaffId = $myStaff ? $myStaff->id : 0;

            $myStats = [
                'total_training' => TrainingAttendance::where('staff_id', $myStaffId)->count(),
                'completed' => TrainingAttendance::where('staff_id', $myStaffId)->where('status', 'Completed')->count(),
                'this_year' => TrainingAttendance::join('training_courses', 'training_attendances.course_id', '=', 'training_courses.id')
                    ->where('training_attendances.staff_id', $myStaffId)
                    ->whereYear('training_courses.start_date', date('Y'))->count(),
                'upcoming_bookings' => RoomBooking::where('booked_by_id', $user->id)->where('booking_date', '>=', date('Y-m-d'))->count(),
                'family_count' => FamilyMember::where('staff_id', $myStaffId)->count(),
            ];

            $myBookings = RoomBooking::with('room')
                ->where('booked_by_id', $user->id)
                ->where('booking_date', '>=', date('Y-m-d'))
                ->orderBy('booking_date')->orderBy('start_time')->limit(4)->get();

            $recentTraining = TrainingAttendance::join('training_courses', 'training_attendances.course_id', '=', 'training_courses.id')
                ->where('training_attendances.staff_id', $myStaffId)
                ->select('training_attendances.*', 'training_courses.code as training_code', 'training_courses.title as training_title', 
                         DB::raw("COALESCE(training_attendances.training_type, training_courses.training_type, 'External') as resolved_type"))
                ->latest('training_attendances.created_at')->limit(5)->get();

            $todayBookings = RoomBooking::with('room')
                ->where('booking_date', date('Y-m-d'))
                ->orderBy('start_time')->get();

            return view('dashboard.index', compact('myStats', 'recentTraining', 'todayBookings'));
        }
    }
}
