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
        $globalYear = session('global_year', date('Y'));
        
        if ($user->isAdmin() || $user->isCeo()) {
            $totalStaff = Staff::count();
            
            $trainingQuery = TrainingAttendance::join('training_courses', 'training_attendances.course_id', '=', 'training_courses.id');
            if ($globalYear !== 'all') {
                $trainingQuery->whereYear('training_courses.start_date', $globalYear);
            }
            $totalTraining = $trainingQuery->count();

            $bookingsQuery = RoomBooking::query();
            if ($globalYear !== 'all') {
                $bookingsQuery->whereYear('booking_date', $globalYear);
            } else {
                $bookingsQuery->where('booking_date', '>=', date('Y-m-d'));
            }
            $totalBookings = $bookingsQuery->count();
            
            $pendingReqs = UpdateRequest::where('status', 'Pending')->count();

            // Training type split
            $typeSplitQuery = TrainingAttendance::join('training_courses', 'training_attendances.course_id', '=', 'training_courses.id');
            if ($globalYear !== 'all') {
                $typeSplitQuery->whereYear('training_courses.start_date', $globalYear);
            }
            $typeSplit = $typeSplitQuery->select(
                    DB::raw("SUM(CASE WHEN COALESCE(training_attendances.training_type, training_courses.training_type, 'External') = 'External' THEN 1 ELSE 0 END) as ext_cnt"),
                    DB::raw("SUM(CASE WHEN COALESCE(training_attendances.training_type, training_courses.training_type, 'External') = 'Internal' THEN 1 ELSE 0 END) as int_cnt")
                )->first();
            $extCnt = (int)($typeSplit->ext_cnt ?? 0);
            $intCnt = (int)($typeSplit->int_cnt ?? 0);

            // Monthly trend (last 6 months - ignore global year for trend or adapt it? Trend is usually rolling, let's keep it as is or adapt)
            $monthTrendQuery = TrainingAttendance::join('training_courses', 'training_attendances.course_id', '=', 'training_courses.id');
            if ($globalYear !== 'all') {
                $monthTrendQuery->whereYear('training_courses.start_date', $globalYear);
            } else {
                $monthTrendQuery->where('training_courses.start_date', '>=', now()->subMonths(5)->startOfMonth())
                                ->where('training_courses.start_date', '<=', now()->endOfMonth());
            }
            
            $monthTrend = $monthTrendQuery->select(
                    DB::raw("DATEPART(year, training_courses.start_date) as yr"),
                    DB::raw("DATEPART(month, training_courses.start_date) as mo"),
                    DB::raw("COUNT(DISTINCT training_courses.id) as cnt")
                )->groupBy(
                    DB::raw("DATEPART(year, training_courses.start_date)"),
                    DB::raw("DATEPART(month, training_courses.start_date)")
                )->orderBy('yr')->orderBy('mo')->get()
                ->map(function ($row) {
                    $date = \Carbon\Carbon::createFromDate($row->yr, $row->mo, 1);
                    $row->lbl = $date->format('M');
                    $row->ym  = $date->format('Y-m');
                    return $row;
                });

            // Top 5 departments
            $topDeptsQuery = Department::join('staff', 'staff.department_id', '=', 'departments.id')
                ->join('training_attendances', 'training_attendances.staff_id', '=', 'staff.id')
                ->join('training_courses', 'training_attendances.course_id', '=', 'training_courses.id');
            if ($globalYear !== 'all') {
                $topDeptsQuery->whereYear('training_courses.start_date', $globalYear);
            }
            $topDepts = $topDeptsQuery->select('departments.name', 'departments.company', DB::raw("COUNT(training_attendances.id) as cnt"))
                ->groupBy('departments.id', 'departments.name', 'departments.company')
                ->orderBy('cnt', 'DESC')->limit(5)->get();

            // Pending requests
            $pendingList = UpdateRequest::where('status', 'Pending')->latest()->limit(4)->get();

            // Recent training
            $recentTrainingQuery = TrainingAttendance::with(['staff.department', 'course'])
                ->join('staff', 'training_attendances.staff_id', '=', 'staff.id')
                ->join('training_courses', 'training_attendances.course_id', '=', 'training_courses.id');
            if ($globalYear !== 'all') {
                $recentTrainingQuery->whereYear('training_courses.start_date', $globalYear);
            }
            $recentTraining = $recentTrainingQuery->select('training_attendances.*', 'staff.name as emp_name', 'training_courses.code as training_code', 'training_courses.title as training_title')
                ->latest('training_attendances.created_at')->limit(5)->get();

            $todayBookingsQuery = RoomBooking::with('room');
            if ($globalYear !== 'all' && $globalYear != date('Y')) {
                // If it's a past year, maybe todayBookings doesn't make sense, but let's just show all for that year or leave it as today
                // Let's leave today bookings as strictly today regardless of year, since it's "Today's Schedule"
                $todayBookingsQuery->where('booking_date', date('Y-m-d'));
            } else {
                $todayBookingsQuery->where('booking_date', date('Y-m-d'));
            }
            $todayBookings = $todayBookingsQuery->orderBy('start_time')->get();

            return view('dashboard.index', compact(
                'totalStaff', 'totalTraining', 'totalBookings', 'pendingReqs',
                'extCnt', 'intCnt', 'monthTrend', 'topDepts', 'pendingList', 'recentTraining', 'todayBookings'
            ));

        } else {
            // Staff logic
            $myStaff = Staff::where('staff_no', $user->staff_no)->first();
            $myStaffId = $myStaff ? $myStaff->id : 0;

            $myStatsThisYearQuery = TrainingAttendance::join('training_courses', 'training_attendances.course_id', '=', 'training_courses.id')
                    ->where('training_attendances.staff_id', $myStaffId);
            if ($globalYear !== 'all') {
                $myStatsThisYearQuery->whereYear('training_courses.start_date', $globalYear);
            }

            $myUpcomingBookingsQuery = RoomBooking::where('booked_by_id', $user->id);
            if ($globalYear !== 'all') {
                $myUpcomingBookingsQuery->whereYear('booking_date', $globalYear);
            } else {
                $myUpcomingBookingsQuery->where('booking_date', '>=', date('Y-m-d'));
            }

            $myStats = [
                'total_training' => TrainingAttendance::where('staff_id', $myStaffId)->count(),
                'completed' => TrainingAttendance::where('staff_id', $myStaffId)->where('status', 'Completed')->count(),
                'this_year' => $myStatsThisYearQuery->count(),
                'upcoming_bookings' => $myUpcomingBookingsQuery->count(),
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

            return view('dashboard.index', compact('myStats', 'recentTraining', 'todayBookings', 'myStaff'));
        }
    }
}
