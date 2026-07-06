<?php

namespace App\Http\Controllers;

use App\Models\FamilyMember;
use App\Models\Staff;
use App\Models\Department;
use App\Imports\FamilyImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Traits\ImportsExcel;
use App\Imports\SimpleToArray;
use Illuminate\Support\Str;
use App\Services\AuditLogger;

class FamilyController extends Controller
{
    use ImportsExcel;
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->isAdminHR();
        $search = $request->query('search');

        $all_staff = Staff::with('department')->where('is_active', 1)->orderBy('name')->get();
        $relationships = ['Spouse', 'Child', 'Parent', 'Sibling'];

        $query = FamilyMember::with(['staff.department']);

        if (!$isAdmin) {
            $query->whereHas('staff', function($q) use ($user) {
                $q->where('staff_no', $user->staff_no);
            });
        } elseif ($search) {
            $query->where(function($q) use ($search) {
                $like = "%$search%";
                $q->where('family_member_name', 'like', $like)
                  ->orWhereHas('staff', function($sq) use ($like) {
                      $sq->where('name', 'like', $like)
                        ->orWhere('staff_no', 'like', $like)
                        ->orWhereHas('department', function($dq) use ($like) {
                            $dq->where('name', 'like', $like);
                        });
                  });
            });
        }

        $records = $query->get()->sortBy(function($record) {
            return ($record->staff?->name ?? '') . $record->family_member_name;
        });

        $grouped = $records->groupBy('staff_id');

        return view('family.index', [
            'grouped' => $grouped,
            'all_staff' => $all_staff,
            'relationships' => $relationships,
            'search' => $search,
            'isAdmin' => $isAdmin,
            'records' => $records
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->isAdminHR();

        $request->validate([
            'name' => 'required',
            'relationship' => 'required',
        ]);

        if ($isAdmin) {
            $staff_id = $request->staff_id;
        } else {
            $staff = Staff::where('staff_no', $user->staff_no)->first();
            $staff_id = $staff->id;
        }

        FamilyMember::create([
            'staff_id' => $staff_id,
            'family_member_name' => trim($request->name),
            'relationship' => $request->relationship,
            'date_of_birth' => $request->date_of_birth ?: null,
            'emergency_contact' => $request->emergency_contact ?: 'No',
            'phone_number' => trim($request->phone_number),
        ]);

        $familyStaff = Staff::find($staff_id);
        AuditLogger::log('create', 'family',
            'Added family member "' . trim($request->name) . '" (' . $request->relationship . ') for ' . ($familyStaff->name ?? 'staff #' . $staff_id) . '.'
        );

        return redirect()->route('family.index')->with('success', 'Family record saved successfully');
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $isAdmin = $user->isAdminHR();
        $familyMember = FamilyMember::findOrFail($id);

        if (!$isAdmin) {
            $staff = Staff::where('staff_no', $user->staff_no)->first();
            if ($familyMember->staff_id != $staff->id) {
                abort(403);
            }
            $staff_id = $staff->id;
        } else {
            $staff_id = $request->staff_id;
        }

        $familyMember->update([
            'staff_id' => $staff_id,
            'family_member_name' => trim($request->name),
            'relationship' => $request->relationship,
            'date_of_birth' => $request->date_of_birth ?: null,
            'phone_number' => trim($request->phone_number),
            'emergency_contact' => $request->emergency_contact ?: 'No',
        ]);

        AuditLogger::log('update', 'family',
            'Updated family member "' . trim($request->name) . '" (record #' . $id . ').'
        );

        return redirect()->route('family.index')->with('success', 'Family record updated successfully');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $isAdmin = $user->isAdminHR();
        $familyMember = FamilyMember::findOrFail($id);

        if (!$isAdmin) {
            $staff = Staff::where('staff_no', $user->staff_no)->first();
            if ($familyMember->staff_id != $staff->id) {
                abort(403);
            }
        }

        AuditLogger::log('delete', 'family',
            'Deleted family member "' . $familyMember->family_member_name . '" (' . $familyMember->relationship . ') record #' . $id . '.'
        );

        $familyMember->delete();
        return redirect()->route('family.index')->with('success', 'Family record deleted successfully');
    }

    public function bulkDestroy(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->isAdminHR();

        $ids = array_filter((array) $request->input('ids', []), 'is_numeric');
        if (empty($ids)) {
            return redirect()->route('family.index')->with('error', 'No records selected.');
        }

        $members = FamilyMember::whereIn('id', $ids)->get();

        if (!$isAdmin) {
            $staff = Staff::where('staff_no', $user->staff_no)->first();
            foreach ($members as $member) {
                if ($member->staff_id != $staff->id) abort(403);
            }
        }

        foreach ($members as $member) {
            AuditLogger::log('delete', 'family',
                'Deleted family member "' . $member->family_member_name . '" (' . $member->relationship . ') record #' . $member->id . '.'
            );
            $member->delete();
        }

        return redirect()->route('family.index')->with('success', $members->count() . ' family record(s) deleted successfully.');
    }

    public function import(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin_it', 'admin_hr'])) abort(403);

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

        $import = new FamilyImport(is_array($mapping) ? $mapping : []);
        $import->processRows($rows);

        $msg = "Import complete: {$import->imported} record(s) imported/updated.";
        if (!empty($import->skipped)) {
            $n = count($import->skipped);
            $msg .= " Skipped {$n}: " . implode('; ', array_slice($import->skipped, 0, 5));
            if ($n > 5) $msg .= " … and " . ($n - 5) . " more.";
        }
        return redirect()->route('family.index')->with('success', $msg);
    }

    public function downloadTemplate()
    {
        $headers = [
            'staff_no', 'name', 'relationship', 'date_of_birth', 'effective_date',
            'nric_no', 'dependent_id', 'gender', 'emergency_contact', 'phone_number',
            'city_of_birth', 'country_of_birth', 'nationality', 'citizenship_status',
            'region_of_birth', 'use_employee_address', 'use_employee_phone',
            'is_fulltime_student', 'student_start_date', 'student_end_date',
            'occupation', 'occupation_effective_date', 'is_disabled', 'is_terminated',
            'company_code', 'company_name', 'region_name',
        ];
        return response()->streamDownload(function () use ($headers) {
            $f = fopen('php://output', 'w');
            fputcsv($f, $headers);
            fclose($f);
        }, 'family_import_template.csv', ['Content-Type' => 'text/csv']);
    }
}
