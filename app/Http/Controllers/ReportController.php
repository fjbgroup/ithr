<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\Company;
use App\Models\Department;
use App\Models\TrainingAttendance;
use App\Models\TrainingCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\StaffExport;
use App\Exports\TrainingExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function staffReport()
    {
        $allCompanies = Company::orderBy('code')->get();
        $totalStaff   = Staff::where('is_active', 1)->count();
        $totalDepts   = Department::count();

        $companyCounts = Staff::where('is_active', 1)
            ->select('company', DB::raw('COUNT(*) as count'))
            ->groupBy('company')
            ->pluck('count', 'company')
            ->toArray();

        $deptRows = Department::leftJoin('staff', function($join) {
                $join->on('staff.department_id', '=', 'departments.id')
                     ->where('staff.is_active', '=', 1);
            })
            ->select('departments.id', 'departments.name as dept', 'departments.company', DB::raw('COUNT(staff.id) as headcount'))
            ->groupBy('departments.id', 'departments.name', 'departments.company')
            ->havingRaw('COUNT(staff.id) > 0')
            ->orderBy('departments.company')
            ->orderBy('headcount', 'DESC')
            ->get();

        $posRows = Staff::where('is_active', 1)
            ->whereNotNull('position')
            ->where('position', '!=', '')
            ->select('position', DB::raw('COUNT(*) as total'))
            ->groupBy('position')
            ->orderBy('total', 'DESC')
            ->limit(8)
            ->get();

        return view('reports.staff', compact('totalStaff', 'totalDepts', 'allCompanies', 'companyCounts', 'deptRows', 'posRows'));
    }

    public function staffExport()
    {
        return Excel::download(new StaffExport, 'staff_report_' . date('Ymd') . '.xlsx');
    }

    public function companyStaffList($company)
    {
        $staff = Staff::where('company', $company)
            ->where('is_active', 1)
            ->select('name', 'staff_no', 'position')
            ->orderBy('name')
            ->get();

        return response()->json($staff);
    }

    public function trainingReport(Request $request)
    {
        $dept_id     = $request->get('dept');
        $month_f     = $request->get('month');
        $year_f      = $request->get('year', date('Y'));
        $type_filter = $request->get('type');
        $company_f   = $request->get('company');

        $departments = Department::orderBy('company')->orderBy('name')->get();

        $query = TrainingAttendance::join('staff', 'training_attendances.staff_id', '=', 'staff.id')
            ->join('training_courses', 'training_attendances.course_id', '=', 'training_courses.id')
            ->leftJoin('departments', 'staff.department_id', '=', 'departments.id');

        if ($year_f) {
            $query->whereYear('training_courses.start_date', $year_f);
        }
        if ($dept_id) {
            $query->where('staff.department_id', $dept_id);
        }
        if ($month_f) {
            $query->whereMonth('training_courses.start_date', $month_f);
        }
        if ($type_filter) {
            $query->where(function($q) use ($type_filter) {
                $q->where('training_attendances.training_type', $type_filter)
                  ->orWhere(function($sq) use ($type_filter) {
                      $sq->whereNull('training_attendances.training_type')
                         ->where('training_courses.training_type', $type_filter);
                  });
            });
        }
        if ($company_f) {
            $query->where('departments.company', $company_f);
        }

        $reports = $query->select(
                'training_attendances.*',
                'staff.staff_no', 'staff.name as staff_name', 'staff.position',
                'departments.name as dept_name', 'departments.company',
                'training_courses.code as course_code', 'training_courses.title as course_title',
                'training_courses.start_date',
                DB::raw("COALESCE(training_attendances.training_type, training_courses.training_type, 'External') as resolved_type")
            )
            ->orderBy('training_courses.start_date', 'DESC')
            ->orderBy('departments.name')
            ->get();

        $total_attendees = $reports->count();
        $unique_courses  = $reports->pluck('course_id')->unique()->count();
        $unique_staff    = $reports->pluck('staff_no')->unique()->count();
        $ext_count       = $reports->filter(fn($r) => $r->resolved_type === 'External')->count();
        $int_count       = $total_attendees - $ext_count;

        // Course counts per type
        $ccQuery = TrainingCourse::query();
        if ($dept_id) {
            $ccQuery->join('training_attendances', 'training_courses.id', '=', 'training_attendances.course_id')
                    ->join('staff', 'training_attendances.staff_id', '=', 'staff.id')
                    ->where('staff.department_id', $dept_id);
        }
        if ($year_f) $ccQuery->whereYear('start_date', $year_f);
        if ($month_f) $ccQuery->whereMonth('start_date', $month_f);
        if ($company_f) $ccQuery->where('training_courses.company', $company_f);

        $courseTypeCounts = $ccQuery->select(
            DB::raw("COUNT(DISTINCT CASE WHEN COALESCE(training_courses.training_type,'External') = 'External' THEN training_courses.id END) as ext_courses"),
            DB::raw("COUNT(DISTINCT CASE WHEN COALESCE(training_courses.training_type,'External') = 'Internal' THEN training_courses.id END) as int_courses")
        )->first();

        $extCourses = $courseTypeCounts->ext_courses ?? 0;
        $intCourses = $courseTypeCounts->int_courses ?? 0;

        // Monthly breakdown
        $monthlyQuery = TrainingAttendance::join('training_courses', 'training_attendances.course_id', '=', 'training_courses.id')
            ->join('staff', 'training_attendances.staff_id', '=', 'staff.id')
            ->leftJoin('departments', 'staff.department_id', '=', 'departments.id')
            ->whereYear('training_courses.start_date', $year_f);

        if ($dept_id) $monthlyQuery->where('staff.department_id', $dept_id);
        if ($type_filter) {
            $monthlyQuery->where(function($q) use ($type_filter) {
                $q->where('training_attendances.training_type', $type_filter)
                  ->orWhere(function($sq) use ($type_filter) {
                      $sq->whereNull('training_attendances.training_type')
                         ->where('training_courses.training_type', $type_filter);
                  });
            });
        }
        if ($company_f) $monthlyQuery->where('departments.company', $company_f);

        $monthlyData = $monthlyQuery->select(
            DB::raw("MONTH(training_courses.start_date) as mon"),
            DB::raw("COUNT(*) as total"),
            DB::raw("SUM(CASE WHEN COALESCE(training_attendances.training_type, training_courses.training_type, 'External') = 'External' THEN 1 ELSE 0 END) as ext_cnt"),
            DB::raw("SUM(CASE WHEN COALESCE(training_attendances.training_type, training_courses.training_type, 'External') = 'Internal' THEN 1 ELSE 0 END) as int_cnt"),
            DB::raw("COUNT(DISTINCT training_attendances.staff_id) as unique_staff"),
            DB::raw("COUNT(DISTINCT training_attendances.course_id) as unique_courses")
        )->groupBy(DB::raw("MONTH(training_courses.start_date)"))->get()->keyBy('mon');

        return view('reports.training', compact(
            'reports', 'departments', 'total_attendees', 'unique_courses', 'unique_staff', 
            'ext_count', 'int_count', 'extCourses', 'intCourses', 'monthlyData',
            'dept_id', 'month_f', 'year_f', 'type_filter', 'company_f'
        ));
    }

    public function trainingExport(Request $request)
    {
        $filters = $request->only(['dept', 'month', 'year', 'type', 'company']);
        return Excel::download(new TrainingExport($filters), 'training_report_' . date('Ymd') . '.xlsx');
    }
}
