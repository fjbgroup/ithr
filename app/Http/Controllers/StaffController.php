<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\User;
use App\Models\Department;
use App\Models\Company;
use App\Models\Position;
use App\Imports\StaffImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Traits\ImportsExcel;
use App\Imports\SimpleToArray;
use Illuminate\Support\Str;
use App\Services\AuditLogger;

class StaffController extends Controller
{
    use ImportsExcel;
    /**
     * Display a listing of the staff.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Only admin_hr can see the full staff registry.
        // All other users should only see their own profile.
        if (!$user->isAdminHR()) {
            $staff = Staff::where('staff_no', $user->staff_no)->first();
            if ($staff) {
                return redirect()->route('staff.show', $staff->id);
            }
            return abort(403, 'Unauthorized access to staff directory. Only HR Admins can view the registry.');
        }

        // Admin view
        $query = Staff::with('department');

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('staff_no', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%");
            });
        }

        if ($request->filled('company')) {
            $companyCode = trim($request->company);
            $labels = [$companyCode];
            
            // Fetch from Company table
            foreach (Company::where('code', $companyCode)->pluck('name') as $name) {
                $labels[] = trim($name);
                $labels[] = trim(preg_replace('/^\d+\s+/', '', $name));
            }

            // Hardcode fallbacks to ensure headcount accuracy
            if (strtoupper($companyCode) === 'FJB') {
                $labels[] = 'FGV Johor Bulkers Sdn Bhd';
                $labels[] = '4810 FGV Johor Bulkers Sdn Bhd';
            } elseif (strtoupper($companyCode) === 'FGVB') {
                $labels[] = 'FGV Bulkers Sdn Bhd';
                $labels[] = '4300 FGV Bulkers Sdn Bhd';
                $labels[] = 'FBSB';
            } elseif (strtoupper($companyCode) === 'LBSB') {
                $labels[] = 'Langsat Bulkers Sdn Bhd';
                $labels[] = '4850 Langsat Bulkers Sdn Bhd';
            } elseif (strtoupper($companyCode) === 'FGT') {
                $labels[] = 'FGV Grains Terminal Sdn';
                $labels[] = '4310 FGV Grains Terminal Sdn';
                $labels[] = 'FGVGT';
            }

            $query->whereIn('company', array_unique($labels));
        }

        if ($request->filled('dept')) {
            $query->where('department_id', $request->dept);
        }

        $staff_list = $query->get();
        $departments = Department::orderBy('company')->orderBy('name')->get();
        $companies = Company::orderBy('code')->get();
        $positions = Position::orderBy('title')->get();

        return view('staff.index', compact('staff_list', 'departments', 'companies', 'positions'));
    }

    /**
     * Display the specified staff.
     */
    public function show($id)
    {
        $user = Auth::user();
        $staff = Staff::with([
            'department',
            'familyMembers',
            'courses',
            'irRecords',
            'travelRecords',
            'user.bookings.room'
        ])->findOrFail($id);

        // Security check: only admin_hr can see other staff profiles
        if (!$user->isAdminHR() && $user->staff_no !== $staff->staff_no) {
            abort(403, 'Unauthorized access to staff profile. Only HR Admins can view other staff details.');
        }

        return view('staff.show', compact('staff'));
    }

    /**
     * Store a newly created staff in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'staff_no' => 'required|unique:staff,staff_no',
            'name' => 'required|string|max:255',
            'company' => 'required|string',
            'department_id' => 'nullable|exists:departments,id',
            'position' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'date_joined' => 'nullable|date',
            'date_of_birth' => 'nullable|date',
            'ic_number' => 'nullable|string|max:20',
            'employment_status' => 'nullable|string|max:50',
            'last_promotion_date' => 'nullable|date',
            'gender' => 'nullable|string',
            'location' => 'nullable|string',
            'compensation_grade' => 'nullable|string',
            'management_level' => 'nullable|string',
            'job_level' => 'nullable|string',
            'job_category' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $staff = Staff::create($validated);

            // Auto-create user account
            User::create([
                'staff_no' => $staff->staff_no,
                'name' => $staff->name,
                'email' => $staff->email,
                'password' => Hash::make('password'),
                'role' => 'staff',
                'it_role' => 'user',
                'department_id' => $staff->department_id,
                'position' => $staff->position,
                'company' => $staff->company,
                'is_active' => true,
                'staff_id' => $staff->id,
            ]);
        });

        AuditLogger::log('create', 'staff',
            'Created staff record for ' . $validated['name'] . ' (' . $validated['staff_no'] . ').'
        );

        return redirect()->route('staff.index')->with('success', 'Staff record and user account created successfully.');
    }

    /**
     * Update the specified staff in storage.
     */
    public function update(Request $request, $id)
    {
        $staff = Staff::findOrFail($id);
        
        $validated = $request->validate([
            'staff_no' => 'required|unique:staff,staff_no,' . $id,
            'name' => 'required|string|max:255',
            'company' => 'required|string',
            'department_id' => 'nullable|exists:departments,id',
            'position' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'date_joined' => 'nullable|date',
            'date_of_birth' => 'nullable|date',
            'ic_number' => 'nullable|string|max:20',
            'employment_status' => 'nullable|string|max:50',
            'last_promotion_date' => 'nullable|date',
            'gender' => 'nullable|string',
            'location' => 'nullable|string',
            'compensation_grade' => 'nullable|string',
            'management_level' => 'nullable|string',
            'job_level' => 'nullable|string',
            'job_category' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        DB::transaction(function () use ($staff, $validated) {
            $staff->update($validated);

            // Sync with user account
            if ($staff->user) {
                $staff->user->update([
                    'staff_no' => $staff->staff_no,
                    'email' => $staff->email,
                    'name' => $staff->name,
                    'department_id' => $staff->department_id,
                    'position' => $staff->position,
                    'company' => $staff->company,
                ]);
            }
        });

        AuditLogger::log('update', 'staff',
            'Updated staff record for ' . $staff->name . ' (' . $staff->staff_no . ').'
        );

        return redirect()->back()->with('success', 'Staff record updated successfully.');
    }

    /**
     * Remove the specified staff from storage.
     */
    public function destroy($id)
    {
        $staff = Staff::findOrFail($id);
        $staffName = $staff->name;
        $staffNo   = $staff->staff_no;

        DB::transaction(function () use ($staff) {
            // Delete linked user account first
            if ($staff->user) {
                $staff->user->delete();
            }
            $staff->delete();
        });

        AuditLogger::log('delete', 'staff',
            'Deleted staff record for ' . $staffName . ' (' . $staffNo . ').'
        );

        return redirect()->route('staff.index')->with('success', 'Staff record deleted successfully.');
    }

    /**
     * Remove multiple staff records from storage.
     */
    public function bulkDestroy(Request $request)
    {
        $ids = $request->ids ?? [];
        
        if (empty($ids)) {
            return redirect()->back()->with('error', 'No staff records selected.');
        }

        DB::transaction(function () use ($ids) {
            // Delete linked user accounts first
            User::whereIn('staff_id', $ids)->delete();
            Staff::whereIn('id', $ids)->delete();
        });

        AuditLogger::log('delete', 'staff',
            'Bulk deleted ' . count($ids) . ' staff record(s).',
            ['ids' => $ids]
        );

        return redirect()->route('staff.index')->with('success', count($ids) . ' staff records deleted successfully.');
    }

    /**
     * Generate the next available Staff ID.
     */
    public function generateStaffId()
    {
        $maxStaffNo = Staff::pluck('staff_no')
            ->filter(fn($no) => ctype_digit((string)$no))
            ->map(fn($no) => (int)$no)
            ->max();

        if (!$maxStaffNo) {
            return response()->json(['staff_no' => '0001']);
        }

        $nextNo = $maxStaffNo + 1;
        return response()->json(['staff_no' => str_pad($nextNo, 4, '0', STR_PAD_LEFT)]);
    }

    /**
     * Bulk add staff records.
     */
    public function bulkStore(Request $request)
    {
        $rows = $request->rows ?? [];
        $added = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            if (empty($row['staff_no']) || empty($row['name'])) {
                $skipped++;
                continue;
            }

            if (Staff::where('staff_no', $row['staff_no'])->exists()) {
                $skipped++;
                continue;
            }

            DB::transaction(function () use ($row, &$added, &$skipped) {
                $staff = Staff::create([
                    'staff_no' => $row['staff_no'],
                    'name' => $row['name'],
                    'company' => $row['company'] ?? 'FJB',
                    'department_id' => $row['department_id'] ?: null,
                    'position' => $row['position'] ?: null,
                    'date_joined' => $row['date_joined'] ?: null,
                    'is_active' => true,
                ]);

                User::create([
                    'staff_no' => $staff->staff_no,
                    'name' => $staff->name,
                    'email' => null,
                    'password' => Hash::make('password'),
                    'role' => 'staff',
                    'it_role' => 'user',
                    'department_id' => $staff->department_id,
                    'position' => $staff->position,
                    'company' => $staff->company,
                    'is_active' => true,
                    'staff_id' => $staff->id,
                ]);

                $added++;
            });
        }

        AuditLogger::log('create', 'staff',
            'Bulk added ' . $added . ' staff record(s), skipped ' . $skipped . '.',
            ['added' => $added, 'skipped' => $skipped]
        );

        return redirect()->route('staff.index')->with('success', "Bulk add completed: $added added, $skipped skipped.");
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls,csv|max:10240']);

        $mapping    = $request->input('mapping', []);
        $sheetIndex = max(0, (int) $request->input('sheet_index', 0));
        $headerRow  = max(1, (int) $request->input('header_row', 1));

        $allSheets   = Excel::toArray(new SimpleToArray, $request->file('file'));
        $sheetData   = array_values($allSheets)[$sheetIndex] ?? [];
        $rawHeadings = $sheetData[$headerRow - 1] ?? [];
        $headings    = array_map(fn($h) => Str::slug((string) $h, '_'), $rawHeadings);
        $dataRows    = array_slice($sheetData, $headerRow);

        $rows = collect($dataRows)
            ->filter(fn($r) => count(array_filter((array) $r, fn($v) => $v !== null && $v !== '')) > 0)
            ->map(fn($row) => collect(array_combine(
                $headings,
                array_pad(array_values((array) $row), count($headings), null)
            )));

        $import = new StaffImport(is_array($mapping) ? $mapping : []);
        $import->processRows($rows);

        $msg = "Import complete: {$import->imported} record(s) imported/updated.";
        if (!empty($import->skipped)) {
            $n = count($import->skipped);
            $msg .= " Skipped {$n}: " . implode('; ', array_slice($import->skipped, 0, 5));
            if ($n > 5) $msg .= " … and " . ($n - 5) . " more.";
        }

        AuditLogger::log('import', 'staff',
            'Imported staff records: ' . $import->imported . ' imported/updated.',
            ['imported' => $import->imported, 'skipped' => count($import->skipped)]
        );

        return redirect()->route('staff.index')->with('success', $msg);
    }

    public function downloadTemplate()
    {
        $headers = [
            'Employee ID', 'Legal Full Name', 'Date of Birth', 'Gender',
            'Age', 'Location',
            'Position', 'Compensation Grade', 'Management Level',
            'Job Level - Primary Position', 'Job Category', 'Job Family',
            'Job Classifications', 'Company', 'Company - ID', 'Yos',
            'balance until retire 60 years', 'Hire Date',
        ];

        $export = new class($headers) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
            public function __construct(private array $headers) {}
            public function array(): array { return []; }
            public function headings(): array { return $this->headers; }
        };

        return \Maatwebsite\Excel\Facades\Excel::download($export, 'staff_import_template.xlsx');
    }

    public function archivedStaff(Request $request)
    {
        $search = trim($request->query('q', ''));
        $filter = $request->query('filter', ''); // 'disabled' | 'inactive' | ''

        $query = Staff::with(['department', 'user'])
            ->where(function ($q) {
                $q->where('is_active', false)
                  ->orWhereHas('user', fn($uq) => $uq->where('is_active', false));
            });

        if ($search) {
            $like = "%$search%";
            $query->where(function ($q) use ($like) {
                $q->where('name', 'like', $like)
                  ->orWhere('staff_no', 'like', $like)
                  ->orWhereHas('department', fn($dq) => $dq->where('name', 'like', $like));
            });
        }

        if ($filter === 'disabled') {
            $query->whereHas('user', fn($uq) => $uq->where('is_active', false));
        } elseif ($filter === 'inactive') {
            $query->where('is_active', false);
        }

        $archivedStaff = $query->orderBy('name')->get();

        return view('archived_staff.index', compact('archivedStaff', 'search', 'filter'));
    }
}
