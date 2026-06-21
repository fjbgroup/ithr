<?php

namespace App\Http\Controllers;

use App\Models\TrainingCourse;
use App\Models\TrainingAttendance;
use App\Models\Department;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use App\Services\AuditLogger;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TrainingExport;

class TrainingController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->isAdmin() || $user->isCeo();

        $view = $request->query('view', $isAdmin ? 'by_dept' : 'list');
        if (!$isAdmin && $view !== 'list') {
            $view = 'list';
        }

        $dept_filter = $request->query('dept');
        $company_f = $request->query('company');
        $search = $request->query('q');
        $course_id = $request->query('course_id');
        $status_f = $request->query('status');
        $type_filter = $request->query('type'); // External | Internal | ''
        $staff_filter = $isAdmin ? $request->query('staff_filter') : null;
        $year_filter = $request->query('year');
        $no_dept = $request->query('no_dept');

        // Stats for the top bar
        if (!$isAdmin) {
            $typeTotals = DB::table('training_attendances as ta')
                ->join('staff as s', 'ta.staff_id', '=', 's.id')
                ->where('s.staff_no', $user->staff_no)
                ->select(
                    DB::raw('COUNT(*) as grand_total'),
                    DB::raw("SUM(CASE WHEN ta.training_type = 'External' THEN 1 ELSE 0 END) as ext_total"),
                    DB::raw("SUM(CASE WHEN ta.training_type = 'Internal' THEN 1 ELSE 0 END) as int_total"),
                    DB::raw('1 as unique_staff'),
                    DB::raw('COUNT(DISTINCT ta.course_id) as unique_courses')
                )->first();
        } else {
            $typeTotals = DB::table('training_attendances as ta')
                ->select(
                    DB::raw('COUNT(*) as grand_total'),
                    DB::raw("SUM(CASE WHEN ta.training_type = 'External' THEN 1 ELSE 0 END) as ext_total"),
                    DB::raw("SUM(CASE WHEN ta.training_type = 'Internal' THEN 1 ELSE 0 END) as int_total"),
                    DB::raw('COUNT(DISTINCT ta.staff_id) as unique_staff'),
                    DB::raw('COUNT(DISTINCT ta.course_id) as unique_courses')
                )->first();
        }

        $courseTypeCounts = DB::table('training_courses')
            ->select(
                DB::raw("SUM(CASE WHEN training_type = 'External' THEN 1 ELSE 0 END) as ext_courses"),
                DB::raw("SUM(CASE WHEN training_type = 'Internal' THEN 1 ELSE 0 END) as int_courses")
            )->first();

        $data = [
            'view' => $view,
            'typeTotals' => $typeTotals,
            'courseTypeCounts' => $courseTypeCounts,
            'dept_filter' => $dept_filter,
            'company_f' => $company_f,
            'search' => $search,
            'status_f' => $status_f,
            'type_filter' => $type_filter,
            'staff_filter' => $staff_filter,
            'year_filter' => $year_filter,
            'no_dept' => $no_dept,
            'isAdmin' => $isAdmin,
            'course_id' => $course_id,
            'years' => TrainingCourse::whereNotNull('start_date')
                        ->selectRaw('YEAR(start_date) as year')
                        ->distinct()
                        ->orderBy('year', 'desc')
                        ->pluck('year'),
        ];

        if ($view === 'by_dept') {
            $query = DB::table('training_attendances as ta')
                ->join('staff as s', 'ta.staff_id', '=', 's.id')
                ->leftJoin('departments as d', 's.department_id', '=', 'd.id')
                ->select(
                    'd.id', 'd.name', 'd.company',
                    DB::raw('COUNT(DISTINCT s.id) as staff_count'),
                    DB::raw('COUNT(DISTINCT ta.id) as training_count'),
                    DB::raw("SUM(CASE WHEN ta.training_type = 'External' THEN 1 ELSE 0 END) as ext_count"),
                    DB::raw("SUM(CASE WHEN ta.training_type = 'Internal' THEN 1 ELSE 0 END) as int_count"),
                    DB::raw('COUNT(DISTINCT ta.course_id) as unique_courses')
                );

            if ($dept_filter) $query->where('d.id', $dept_filter);
            if ($company_f) $query->where('d.company', $company_f);

            $data['dept_summaries'] = $query->groupBy('d.id', 'd.name', 'd.company')
                ->orderByRaw('CASE WHEN d.id IS NULL THEN 1 ELSE 0 END')
                ->orderBy('d.company')
                ->orderBy('d.name')
                ->get();

        } elseif ($view === 'list') {
            $query = DB::table('training_attendances as ta')
                ->join('staff as s', 'ta.staff_id', '=', 's.id')
                ->leftJoin('departments as d', 's.department_id', '=', 'd.id')
                ->join('training_courses as tc', 'ta.course_id', '=', 'tc.id')
                ->select(
                    'ta.*', 's.staff_no', 's.name as emp_name', 's.position',
                    'd.name as dept_name', 'd.company',
                    'tc.code as training_code', 'tc.title as training_title', 'tc.start_date', 'tc.end_date', 'tc.venue',
                    DB::raw("COALESCE(ta.training_type, tc.training_type, 'External') as training_type")
                );

            if (!$isAdmin) {
                $query->where('s.staff_no', $user->staff_no);
            } elseif ($staff_filter) {
                $query->where('ta.staff_id', $staff_filter);
            } elseif ($no_dept) {
                $query->whereNull('s.department_id');
            } elseif ($dept_filter) {
                $query->where('s.department_id', $dept_filter);
            }

            if ($company_f) $query->where('s.company', $company_f);
            if ($type_filter) $query->where('ta.training_type', $type_filter);
            if ($search) {
                $query->where(function($q) use ($search) {
                    $like = "%$search%";
                    $q->where('s.name', 'like', $like)
                      ->orWhere('s.staff_no', 'like', $like)
                      ->orWhere('tc.code', 'like', $like)
                      ->orWhere('tc.title', 'like', $like);
                });
            }
            if ($status_f) $query->where('ta.status', $status_f);
            if ($course_id) $query->where('ta.course_id', $course_id);
            if ($year_filter) $query->whereYear('tc.start_date', $year_filter);

            $data['attendances'] = $query->orderBy('tc.start_date', 'desc')->orderBy('ta.created_at', 'desc')->get();

        } elseif ($view === 'courses') {
            $query = TrainingCourse::query();
            if ($company_f) $query->where('company', $company_f);
            if ($type_filter) $query->where('training_type', $type_filter);
            if ($search) {
                $query->where(function($q) use ($search) {
                    $like = "%$search%";
                    $q->where('title', 'like', $like)
                      ->orWhere('code', 'like', $like);
                });
            }
            $data['courses'] = $query->with(['staff' => fn($q) => $q->withPivot('status')->with('department')])->orderBy('code')->get();
        }

        // Always pass lightweight lists for the Add Course / Add Attendance modals
        $data['allStaff']       = collect();
        $data['allCourses']     = collect();
        $data['allDepartments'] = collect();
        if ($isAdmin) {
            $data['allStaff']       = Staff::select('id', 'name', 'staff_no')->where('is_active', 1)->orderBy('name')->get();
            $data['allCourses']     = TrainingCourse::select('id', 'title', 'code', 'training_type')->orderBy('code')->get();
            $data['allDepartments'] = Department::select('id', 'name', 'company')->orderBy('company')->orderBy('name')->get();
        }

        return view('training.index', $data);
    }

    public function storeCourse(Request $request)
    {
        if (!Auth::user() || !(Auth::user()->role === 'admin_it' || Auth::user()->role === 'admin_hr')) {
            return redirect()->route('training.index')->with('error', 'Unauthorized.');
        }

        $validated = $request->validate([
            'title'         => 'required|string|max:500',
            'training_type' => 'required|in:Internal,External',
            'company'       => 'nullable|string|max:50',
            'start_date'    => 'nullable|date',
            'end_date'      => 'nullable|date',
            'venue'         => 'nullable|string|max:500',
            'duration'      => 'nullable|string|max:100',
            'is_private'    => 'boolean',
            'staff_ids'     => 'nullable|array',
            'staff_ids.*'   => 'exists:staff,id',
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

        $course = TrainingCourse::create($validated);

        AuditLogger::log('create', 'training',
            'Created training course "' . $course->title . '" (' . $course->code . ').',
            ['course_id' => $course->id, 'type' => $course->training_type]
        );

        if (!empty($validated['is_private']) && !empty($request->input('staff_ids'))) {
            $now = now();
            $inserts = [];
            foreach (array_unique($request->input('staff_ids')) as $sid) {
                $inserts[] = [
                    'staff_id'      => $sid,
                    'course_id'     => $course->id,
                    'status'        => 'Scheduled',
                    'training_type' => $course->training_type,
                    'created_by'    => Auth::id(),
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ];
            }
            TrainingAttendance::insertOrIgnore($inserts);
        }

        return redirect()->route('training.index', ['view' => 'courses'])
            ->with('success', 'Course "' . $course->title . '" created.');
    }

    public function updateCourse(Request $request, TrainingCourse $course)
    {
        if (!Auth::user() || !(Auth::user()->role === 'admin_it' || Auth::user()->role === 'admin_hr')) {
            return redirect()->route('training.index')->with('error', 'Unauthorized.');
        }

        $validated = $request->validate([
            'title'         => 'required|string|max:500',
            'training_type' => 'required|in:Internal,External',
            'company'       => 'nullable|string|max:50',
            'start_date'    => 'nullable|date',
            'end_date'      => 'nullable|date',
            'venue'         => 'nullable|string|max:500',
            'duration'      => 'nullable|string|max:100',
            'is_private'    => 'boolean',
            'staff_ids'     => 'nullable|array',
            'staff_ids.*'   => 'exists:staff,id',
        ]);

        if (empty($validated['end_date']) && !empty($validated['start_date'])) {
            $validated['end_date'] = $validated['start_date'];
            if (empty($validated['duration'])) $validated['duration'] = '1 day';
        }

        $course->update($validated);

        AuditLogger::log('update', 'training',
            'Updated training course "' . $course->title . '" (' . $course->code . ').',
            ['course_id' => $course->id]
        );

        if (!empty($validated['is_private']) && !empty($request->input('staff_ids'))) {
            $now = now();
            $inserts = [];
            foreach (array_unique($request->input('staff_ids')) as $sid) {
                $inserts[] = [
                    'staff_id'      => $sid,
                    'course_id'     => $course->id,
                    'status'        => 'Scheduled',
                    'training_type' => $course->training_type,
                    'created_by'    => Auth::id(),
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ];
            }
            TrainingAttendance::insertOrIgnore($inserts);
        }

        return redirect()->route('training.index', ['view' => 'courses'])
            ->with('success', 'Course "' . $course->title . '" updated.');
    }

    public function storeAttendance(Request $request)
    {
        if (!Auth::user() || !(Auth::user()->role === 'admin_it' || Auth::user()->role === 'admin_hr')) {
            return redirect()->route('training.index')->with('error', 'Unauthorized.');
        }

        $validated = $request->validate([
            'staff_id'      => 'required|exists:staff,id',
            'course_id'     => 'required|exists:training_courses,id',
            'status'        => 'required|in:Completed,In Progress,Scheduled',
            'training_type' => 'required|in:Internal,External',
            'remarks'       => 'nullable|string|max:500',
        ]);

        $exists = TrainingAttendance::where('staff_id', $validated['staff_id'])
            ->where('course_id', $validated['course_id'])
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'This staff member is already registered for that course.');
        }

        $validated['created_by'] = Auth::id();
        TrainingAttendance::create($validated);

        return redirect()->route('training.index', ['view' => 'list'])
            ->with('success', 'Attendance record added.');
    }

    public function qrPage(TrainingCourse $course)
    {
        if (!Auth::user() || !(Auth::user()->role === 'admin_it' || Auth::user()->role === 'admin_hr')) {
            return redirect()->route('training.index')->with('error', 'Unauthorized.');
        }

        $attendances = TrainingAttendance::where('course_id', $course->id)
            ->with('staff.department')
            ->get();

        // Anonymous feedback summary — aggregate only, no staff identity exposed.
        $feedbacks = \App\Models\TrainingFeedback::where('course_id', $course->id)->get();
        $feedbackStats = null;
        if ($feedbacks->isNotEmpty()) {
            $recs = $feedbacks->whereNotNull('would_recommend');
            $feedbackStats = [
                'count'         => $feedbacks->count(),
                'scanned'       => $attendances->whereNotNull('qr_used_at')->count(),
                'content'       => round($feedbacks->avg('content_rating'), 1),
                'trainer'       => round($feedbacks->avg('trainer_rating'), 1),
                'venue'         => round($feedbacks->avg('venue_rating'), 1),
                'overall'       => round($feedbacks->avg('overall_rating'), 1),
                'recommend_pct' => $recs->isNotEmpty() ? round($recs->avg('would_recommend') * 100) : null,
                // Shuffled so comment order can't be matched to scan order.
                'comments'      => $feedbacks->pluck('comments')
                    ->map(fn ($c) => trim((string) $c))
                    ->filter()
                    ->shuffle()
                    ->values(),
            ];
        }

        return view('training.qr_page', compact('course', 'attendances', 'feedbackStats'));
    }

    public function courseExport(TrainingCourse $course)
    {
        if (!Auth::user() || !(Auth::user()->role === 'admin_it' || Auth::user()->role === 'admin_hr')) {
            return redirect()->route('training.index')->with('error', 'Unauthorized.');
        }

        $filename = 'training_' . $course->code . '_' . date('Ymd') . '.xlsx';
        return Excel::download(new TrainingExport(['course_id' => $course->id]), $filename);
    }

    public function qrGenerate(Request $request, TrainingCourse $course)
    {
        if (!Auth::user() || !(Auth::user()->role === 'admin_it' || Auth::user()->role === 'admin_hr')) {
            return redirect()->route('training.index')->with('error', 'Unauthorized.');
        }

        $count = 0;
        TrainingAttendance::where('course_id', $course->id)
            ->whereNull('qr_token')
            ->each(function ($attendance) use (&$count) {
                $attendance->update(['qr_token' => bin2hex(random_bytes(32))]);
                $count++;
            });

        return redirect()->route('training.qr.page', $course->id)
            ->with('success', "Generated QR codes for {$count} attendee(s).");
    }

    public function qrScan(string $token)
    {
        $attendance = TrainingAttendance::where('qr_token', $token)->with('staff', 'course')->first();

        if (!$attendance) {
            abort(404);
        }

        if ($attendance->qr_used_at) {
            return view('training.qr_scan', [
                'attendance' => $attendance,
                'error'      => 'used',
            ]);
        }

        $staffUser = $attendance->staff?->user;
        if (!$staffUser || $staffUser->id !== Auth::id()) {
            return view('training.qr_scan', [
                'attendance' => $attendance,
                'error'      => 'wrong_user',
            ]);
        }

        return view('training.qr_scan', ['attendance' => $attendance, 'error' => null]);
    }

    public function qrSubmit(Request $request, string $token)
    {
        $attendance = TrainingAttendance::where('qr_token', $token)->with('staff')->first();

        if (!$attendance) {
            abort(404);
        }

        if ($attendance->qr_used_at) {
            return redirect()->route('training.qr.scan', $token)->with('error', 'This QR code has already been used.');
        }

        $staffUser = $attendance->staff?->user;
        if (!$staffUser || $staffUser->id !== Auth::id()) {
            return redirect()->route('training.qr.scan', $token)->with('error', 'This QR code is not assigned to you.');
        }

        DB::transaction(function () use ($attendance) {
            $attendance->update([
                'qr_used_at' => now(),
                'status'     => 'Completed',
            ]);
        });

        return redirect()->route('training.index')
            ->with('success', 'Attendance marked successfully. Welcome to the training!');
    }

    public function importPage()
    {
        if (!Auth::user() || !(Auth::user()->role === 'admin_it' || Auth::user()->role === 'admin_hr')) {
            return redirect()->route('training.index')->with('error', 'Unauthorized.');
        }
        return view('training.import');
    }

    public function downloadTemplate(Request $request)
    {
        $type = $request->query('type', 'simple');

        return response()->streamDownload(function () use ($type) {
            $f = fopen('php://output', 'w');
            if ($type === 'internal') {
                fputcsv($f, ['DATE TRAINING', 'VENUE', 'NAME OF COURSE', 'ID STAFF']);
                fputcsv($f, ['2025-01-15', 'Main Hall FJB', 'Fire Safety Awareness', '3600134']);
                fputcsv($f, ['2025-02-10', 'Training Room A', 'First Aid & CPR', '3600201']);
            } elseif ($type === 'external') {
                fputcsv($f, ['REF NO', 'NAME OF COURSE', 'VENUE', 'TRAINING DATE', 'ID PETUGAS']);
                fputcsv($f, ['1', 'ISO 9001 Awareness', 'NIOSH KL', '2025-03-20', '3600134']);
                fputcsv($f, ['2', 'Forklift Handling', 'DOSH Selangor', '2025-04-05', '3600201']);
            } else {
                fputcsv($f, ['staff_no', 'course_title', 'training_type', 'start_date', 'venue', 'status']);
                fputcsv($f, ['3600134', 'Fire Safety Awareness', 'Internal', '2025-01-15', 'Main Hall FJB', 'Completed']);
                fputcsv($f, ['3600134', 'Forklift Handling', 'External', '2025-02-20', 'NIOSH KL', 'Completed']);
            }
            fclose($f);
        }, "training_template_{$type}.csv", ['Content-Type' => 'text/csv']);
    }

    public function import(Request $request)
    {
        if (!Auth::user() || !(Auth::user()->role === 'admin_it' || Auth::user()->role === 'admin_hr')) {
            return redirect()->route('training.index')->with('error', 'Unauthorized access.');
        }

        $request->validate([
            'csv_type' => 'required|in:internal,external,simple',
            'csv_file' => 'required|file|mimes:csv,txt,xlsx,xls'
        ]);

        $csvType = $request->input('csv_type');
        $file = $request->file('csv_file');
        $ext = strtolower($file->getClientOriginalExtension());
        $filepath = $file->getRealPath();

        $all = $this->readFileRows($filepath, $ext);

        DB::beginTransaction();
        try {
            $courseCache = [];
            $attCreated  = 0;
            $attSkipped  = 0;
            $missed      = [];

            $maxSeq = DB::table('training_courses')
                ->pluck('code')
                ->filter(fn($c) => preg_match('/^[A-Z]{3}[0-9]+$/', $c))
                ->map(fn($c) => (int)substr($c, 3))
                ->max();
            $seq = (int)($maxSeq ?? 0);

            if ($csvType === 'internal') {
                $hIdx = $this->findHeaderRowIdx($all, ['NAME OF COURSE', 'COURSE', 'TAJUK KURSUS', 'ID STAFF', 'STAFF NO']) ?? 3;
                $cols = $this->mapCols($all[$hIdx] ?? [], [
                    'date'    => ['DATE TRAINING', 'DATE'],
                    'venue'   => ['VENUE'],
                    'title'   => ['NAME OF COURSE', 'COURSE', 'TAJUK KURSUS'],
                    'staffNo' => ['ID STAFF', 'STAFF NO', 'STAFF ID', 'ID PETUGAS'],
                ]);
                $iDate    = $cols['date']    ?? 0;
                $iVenue   = $cols['venue']   ?? 1;
                $iTitle   = $cols['title']   ?? 2;
                $iStaffNo = $cols['staffNo'] ?? 6;
                $data = array_slice($all, $hIdx + 1);
            } elseif ($csvType === 'external') {
                $hIdx = $this->findHeaderRowIdx($all, ['ID PETUGAS', 'REF NO', 'TRAINING PROGRAMME', 'NO RUJUKAN']) ?? 4;

                // External files often use a two-row merged header. Combine them.
                $headerRow = $all[$hIdx] ?? [];
                $subRow    = $all[$hIdx + 1] ?? [];
                $maxCol    = max(count($headerRow), count($subRow));
                $combined  = [];
                for ($ci = 0; $ci < $maxCol; $ci++) {
                    $top = strtoupper(trim((string)($headerRow[$ci] ?? '')));
                    $bot = strtoupper(trim((string)($subRow[$ci]    ?? '')));
                    $combined[$ci] = trim($top . ' ' . $bot);
                }

                $cols = $this->mapCols($combined, [
                    'refNo'   => ['REF NO', 'REF', 'NO', 'RUJUKAN'],
                    'title'   => ['NAME OF COURSE', 'COURSE', 'PROGRAM', 'NAMA KURSUS', 'TRAINING PROGRAMME', 'PROGRAMME'],
                    'date'    => ['DATE TRAINING', 'TRAINING DATE', 'TARIKH'],
                    'venue'   => ['VENUE', 'TEMPAT'],
                    'staffNo' => ['ID PETUGAS', 'ID STAFF', 'STAFF NO', 'ID'],
                ]);

                $iVenue   = $cols['venue']   ?? 4;
                $iTitle   = $cols['title']   ?? 5;
                $iRefNo   = $cols['refNo']   ?? 1;
                $iStaffNo = $cols['staffNo'] ?? 11;

                // Date fallback: prefer a cell containing 'TRAINING', then any cell with 'DATE'
                if (isset($cols['date'])) {
                    $iDate = $cols['date'];
                } else {
                    $iDate = 3;
                    foreach ($combined as $ci => $val) {
                        if (str_contains($val, 'TRAINING')) { $iDate = $ci; break; }
                    }
                    if ($iDate === 3) {
                        foreach ($combined as $ci => $val) {
                            if (str_contains($val, 'DATE')) { $iDate = $ci; break; }
                        }
                    }
                }

                // If subRow contains data-like values instead of header-like values, start from hIdx + 1
                $testVal = strtoupper(trim((string)($subRow[$iStaffNo] ?? '')));
                $dataStartOffset = (str_contains($testVal, 'STAFF') || str_contains($testVal, 'ID') || $testVal === '') ? 2 : 1;
                $data = array_slice($all, $hIdx + $dataStartOffset);
            } else { 
                $data = array_slice($all, 1);
            }

            foreach ($data as $c) {
                if ($csvType === 'internal') {
                    $staffNo = trim((string)($c[$iStaffNo] ?? ''));
                    $title   = trim((string)($c[$iTitle]   ?? ''));
                    if ($staffNo === '' || $title === '') continue;
                    // Skip if staffNo looks like a header (e.g. "STAFF NO" or "ID")
                    if (str_contains(strtoupper($staffNo), 'STAFF') || strtoupper($staffNo) === 'ID') continue;
                    
                    $cid = $this->findOrMakeCourse($courseCache, $title, $this->parseDateStr((string)($c[$iDate] ?? '')), 'Internal', trim((string)($c[$iVenue] ?? '')), 'INT', $seq);
                    $status = 'Completed';
                    $ttype  = 'Internal';
                } elseif ($csvType === 'external') {
                    $refNo   = trim((string)($c[$iRefNo]   ?? ''));
                    $staffNo = trim((string)($c[$iStaffNo] ?? ''));
                    $title   = trim((string)($c[$iTitle]   ?? ''));
                    
                    if ($refNo === '' && $staffNo === '') continue;
                    // Skip header rows if offset was wrong
                    if (str_contains(strtoupper($staffNo), 'STAFF') || strtoupper($staffNo) === 'ID') continue;
                    if ($staffNo === '' || $title === '') continue;

                    $cid = $this->findOrMakeCourse($courseCache, $title, $this->parseDateStr((string)($c[$iDate] ?? '')), 'External', trim((string)($c[$iVenue] ?? '')), 'EXT', $seq);
                    $status = 'Completed';
                    $ttype  = 'External';
                } else { 
                    $staffNo = trim((string)($c[0] ?? ''));
                    $title   = trim((string)($c[1] ?? ''));
                    if ($staffNo === '' || $title === '') continue;
                    if (str_contains(strtoupper($staffNo), 'STAFF') || strtoupper($staffNo) === 'ID') continue;

                    $ttype  = in_array($c[2] ?? '', ['Internal','External']) ? $c[2] : 'External';
                    $status = in_array($c[5] ?? '', ['Completed','In Progress','Scheduled']) ? $c[5] : 'Completed';
                    $prefix = $ttype === 'Internal' ? 'INT' : 'EXT';
                    $cid = $this->findOrMakeCourse($courseCache, $title, $this->parseDateStr((string)($c[3] ?? '')), $ttype, trim((string)($c[4] ?? '')), $prefix, $seq);
                }

                $staff = Staff::where('staff_no', $staffNo)->first();
                if (!$staff) { $missed[] = $staffNo; continue; }

                $existing = TrainingAttendance::where('staff_id', $staff->id)->where('course_id', $cid)->exists();
                if (!$existing) {
                    TrainingAttendance::create([
                        'staff_id' => $staff->id,
                        'course_id' => $cid,
                        'status' => $status,
                        'training_type' => $ttype,
                        'created_by' => Auth::id() ?? 1
                    ]);
                    $attCreated++;
                } else {
                    $attSkipped++;
                }
            }
            DB::commit();

            AuditLogger::log('import', 'training',
                'Imported training records: ' . $attCreated . ' created, ' . $attSkipped . ' skipped.',
                ['created' => $attCreated, 'skipped' => $attSkipped, 'type' => $csvType]
            );

            return redirect()->route('training.index', ['view' => 'list'])->with('success', "Import Summary: $attCreated imported, $attSkipped skipped.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('training.index')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function deleteAll()
    {
        TrainingAttendance::query()->delete();
        TrainingCourse::query()->delete();

        AuditLogger::log('delete', 'training', 'Deleted all training attendance records and courses.');

        return redirect()->route('training.index')->with('success', 'All records cleared.');
    }

    public function deleteByType(Request $request)
    {
        if (!Auth::user() || !(Auth::user()->role === 'admin_it' || Auth::user()->role === 'admin_hr')) {
            return redirect()->route('training.index')->with('error', 'Unauthorized.');
        }

        $type = $request->input('type');
        if (!in_array($type, ['Internal', 'External'])) {
            return redirect()->back()->with('error', 'Invalid type.');
        }

        // Delete courses of the given type — attendance rows cascade automatically
        TrainingCourse::where('training_type', $type)->delete();

        AuditLogger::log('delete', 'training', 'Deleted all ' . $type . ' training records.');

        return redirect()->route('training.import-page')
            ->with('success', "All {$type} training records deleted.");
    }

    private function findOrMakeCourse(&$cache, $title, $dateArr, $type, $venue, $prefix, &$seq)
    {
        $startDate = $dateArr['start_date'] ?? null;
        $endDate   = $dateArr['end_date'] ?? null;
        $duration  = $dateArr['duration'] ?? null;

        $key = $title . '||' . ($startDate ?? '') . '||' . ($endDate ?? '') . '||' . $type;
        if (array_key_exists($key, $cache)) return $cache[$key];

        $course = TrainingCourse::where('title', $title)->where('training_type', $type)
            ->where(function($q) use ($startDate) {
                if ($startDate) $q->where('start_date', $startDate);
                else $q->whereNull('start_date');
            })
            ->where(function($q) use ($endDate) {
                if ($endDate) $q->where('end_date', $endDate);
                else $q->whereNull('end_date');
            })
            ->first();

        if ($course) $id = $course->id;
        else {
            $code = $prefix . str_pad(++$seq, 3, '0', STR_PAD_LEFT);
            $newCourse = TrainingCourse::create([
                'code' => $code, 
                'title' => $title, 
                'company' => 'FJB', 
                'start_date' => $startDate, 
                'end_date' => $endDate,
                'training_type' => $type, 
                'venue' => $venue,
                'duration' => $duration
            ]);
            $id = $newCourse->id;
        }
        return $cache[$key] = $id;
    }

    private function readFileRows($filepath, $ext) {
        if (in_array($ext, ['xlsx', 'xls'])) {
            $reader = IOFactory::createReaderForFile($filepath);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($filepath);
            $rows = [];
            foreach ($spreadsheet->getWorksheetIterator() as $ws) {
                $highestCol = $ws->getHighestColumn();
                foreach ($ws->getRowIterator() as $row) {
                    $iter = $row->getCellIterator('A', $highestCol);
                    $iter->setIterateOnlyExistingCells(false);
                    $rowData = [];
                    foreach ($iter as $cell) {
                        $val = $cell->getValue();
                        if ($val !== null && $cell->getDataType() === DataType::TYPE_NUMERIC && (float)$val >= 40000 && (float)$val < 55000) {
                            $val = date('Y-m-d', ExcelDate::excelToTimestamp((float)$val));
                        } else {
                            $val = trim((string)($cell->getValue() ?? ''));
                        }
                        $rowData[] = $val;
                    }
                    $rows[] = $rowData;
                }
            }
            return $rows;
        }
        $rows = [];
        $fh = fopen($filepath, 'r');
        $bom = fread($fh, 3);
        if ($bom !== "\xEF\xBB\xBF") rewind($fh);
        while (($row = fgetcsv($fh)) !== false) $rows[] = array_map('trim', $row);
        fclose($fh);
        return $rows;
    }

    private function parseDateStr($raw) {
        $result = ['start_date' => null, 'end_date' => null, 'duration' => null];
        $s = trim(preg_replace('/[\r\n]+/', ' ', $raw));
        if ($s === '') return $result;

        // Pattern for "12-13/1/2026" or "12-13 Jan 2026" or "12 - 13 Jan 2026"
        if (preg_match('/^(\d+)\s*[-–]\s*(\d+)\s*[\/\s](.+)$/i', $s, $m)) {
            $startDay = (int)$m[1];
            $endDay   = (int)$m[2];
            $rest     = trim($m[3]);

            try {
                $rest = trim(str_replace(['/', '\\'], ' ', $rest));
                $baseDateStr = $endDay . ' ' . $rest;
                $dtEnd = null;
                if (preg_match('/^\d+\s+\d+\s+\d+$/', $baseDateStr)) {
                    try { $dtEnd = \Carbon\Carbon::createFromFormat('j n Y', $baseDateStr); } catch (\Exception $e) {}
                }
                if (!$dtEnd) {
                    try { $dtEnd = \Carbon\Carbon::parse($baseDateStr); } catch (\Exception $e) {}
                }
                if ($dtEnd) {
                    $days = ($endDay - $startDay) + 1;
                    if ($days > 0) {
                        $dtStart = $dtEnd->copy()->day($startDay);
                        return [
                            'start_date' => $dtStart->format('Y-m-d'),
                            'end_date'   => $dtEnd->format('Y-m-d'),
                            'duration'   => $days . ($days > 1 ? ' days' : ' day')
                        ];
                    }
                }
            } catch (\Exception $e) {}
        }

        // Try d/m/Y or d-m-Y directly for single dates
        if (preg_match('/^\d+[\/\-]\d+[\/\-]\d+$/', $s)) {
            try {
                $val = str_replace('-', '/', $s);
                $dstr = \Carbon\Carbon::createFromFormat('j/n/Y', $val)->format('Y-m-d');
                return ['start_date' => $dstr, 'end_date' => $dstr, 'duration' => '1 day'];
            } catch (\Exception $e) {}
        }

        try {
            $dt = \Carbon\Carbon::parse($s);
            $dstr = $dt->format('Y-m-d');
            return ['start_date' => $dstr, 'end_date' => $dstr, 'duration' => '1 day'];
        } catch (\Exception $e) {}

        return $result;
    }

    private function findHeaderRowIdx($all, $markers) {
        $markers = (array)$markers;
        foreach (array_slice($all, 0, 15, true) as $idx => $row) {
            foreach ($row as $cell) {
                $uCell = strtoupper(trim((string)$cell));
                foreach ($markers as $m) {
                    if (str_contains($uCell, strtoupper($m))) return $idx;
                }
            }
        }
        return null;
    }

    private function mapCols($row, $searchMap) {
        $res = [];
        foreach ($row as $ci => $cell) {
            $u = strtoupper(trim((string)$cell));
            foreach ($searchMap as $k => $pats) {
                if (!isset($res[$k])) {
                    foreach ((array)$pats as $p) if (str_contains($u, strtoupper($p))) { $res[$k] = $ci; break; }
                }
            }
        }
        return $res;
    }
}
