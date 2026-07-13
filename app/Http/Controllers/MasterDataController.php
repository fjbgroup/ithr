<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Department;
use App\Models\Position;
use App\Models\TrainingCourse;
use App\Models\TransportMode;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\AuditLogger;

class MasterDataController extends Controller
{
    public function index(Request $request)
    {
        $activeTab = $request->query('tab', 'departments');
        $validTabs = ['departments', 'companies', 'courses', 'positions', 'transport'];
        if (!in_array($activeTab, $validTabs)) {
            $activeTab = 'departments';
        }

        $search = trim($request->query('q', ''));
        $cFilter = $request->query('company', '');

        $data = [];
        $counts = [
            'departments' => Department::count(),
            'companies'   => Company::count(),
            'courses'     => TrainingCourse::count(),
            'positions'   => Position::count(),
            'transport'   => TransportMode::count(),
        ];

        $tabLabels = [
            'departments' => 'Department',
            'companies'   => 'Company',
            'courses'     => 'Training Course',
            'positions'   => 'Position',
            'transport'   => 'Transport Mode',
        ];

        if ($activeTab === 'departments') {
            $query = Department::withCount(['staff' => function($q) {
                $q->where('is_active', 1);
            }]);
            if ($cFilter) {
                $query->where('company', $cFilter);
            }
            if ($search) {
                $query->where('name', 'LIKE', "%$search%");
            }
            $data['rows'] = $query->orderBy('company')->orderBy('name')->get();
            $data['totals'] = Department::select('company', DB::raw('count(*) as count'))
                ->groupBy('company')
                ->pluck('count', 'company')
                ->toArray();
        } elseif ($activeTab === 'companies') {
            $query = Company::query();
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%")
                      ->orWhere('code', 'LIKE', "%$search%");
                });
            }
            // Add staff_count and dept_count
            $data['rows'] = $query->orderBy('code')->get()->map(function($company) {
                $company->staff_count = Staff::where('company', $company->code)->where('is_active', 1)->count();
                $company->dept_count = Department::where('company', $company->code)->count();
                return $company;
            });
        } elseif ($activeTab === 'courses') {
            $query = TrainingCourse::withCount('staff as att_count');
            if ($cFilter) {
                $query->where('company', $cFilter);
            }
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('code', 'LIKE', "%$search%")
                      ->orWhere('title', 'LIKE', "%$search%");
                });
            }
            $data['rows'] = $query->orderBy('code')->get();
        } elseif ($activeTab === 'positions') {
            $query = Position::query();
            if ($search) {
                $query->where('title', 'LIKE', "%$search%");
            }
            $data['rows'] = $query->orderBy('title')->get()->map(function($pos) {
                $pos->staff_count = Staff::where('position', $pos->title)->where('is_active', 1)->count();
                return $pos;
            });
        } elseif ($activeTab === 'transport') {
            $query = TransportMode::query();
            if ($search) {
                $query->where('name', 'LIKE', "%$search%");
            }
            $data['rows'] = $query->orderBy('name')->get()->map(function($tm) {
                $tm->usage_count = DB::table('business_travel')->where('transport', $tm->name)->count();
                return $tm;
            });
        }

        $allCompanies = Company::orderBy('code')->get();

        return view('master_data.index', compact('activeTab', 'search', 'cFilter', 'data', 'counts', 'tabLabels', 'allCompanies'));
    }

    public function store(Request $request)
    {
        $tab = $request->input('tab');

        if ($tab === 'departments') {
            Department::create($request->only(['name', 'company']));
            AuditLogger::log('create', 'master_data', 'Added department "' . $request->name . '" (' . $request->company . ').');
        } elseif ($tab === 'companies') {
            Company::create($request->only(['code', 'name']));
            AuditLogger::log('create', 'master_data', 'Added company "' . $request->name . '" (code: ' . $request->code . ').');
        } elseif ($tab === 'courses') {
            TrainingCourse::create($request->only(['code', 'title', 'training_type', 'company', 'start_date']));
            AuditLogger::log('create', 'master_data', 'Added training course "' . $request->title . '".');
        } elseif ($tab === 'positions') {
            Position::create($request->only(['title']));
            AuditLogger::log('create', 'master_data', 'Added position "' . $request->title . '".');
        } elseif ($tab === 'transport') {
            TransportMode::create($request->only(['name']));
            AuditLogger::log('create', 'master_data', 'Added transport mode "' . $request->name . '".');
        } elseif ($tab === 'settings') {
            SystemSetting::create($request->only(['setting_key', 'setting_value']));
            AuditLogger::log('create', 'master_data', 'Added system setting "' . $request->setting_key . '".');
        }

        return redirect()->route('master-data.index', ['tab' => $tab])->with('success', 'Record added successfully.');
    }

    public function update(Request $request, $id)
    {
        $tab = $request->input('tab');

        if ($tab === 'departments') {
            Department::findOrFail($id)->update($request->only(['name', 'company']));
            AuditLogger::log('update', 'master_data', 'Updated department "' . $request->name . '" #' . $id . '.');
        } elseif ($tab === 'companies') {
            $company = Company::findOrFail($id);
            $oldCode = $company->code;
            $newCode = strtoupper(trim($request->input('code')));

            DB::transaction(function () use ($company, $request, $oldCode, $newCode, $id) {
                $request->merge(['code' => $newCode]);
                $company->update($request->only(['code', 'name']));

                if ($oldCode !== $newCode) {
                    $staffCount = Staff::where('company', $oldCode)->count();
                    $userCount  = DB::table('users')->where('company', $oldCode)->count();
                    $deptCount  = Department::where('company', $oldCode)->count();
                    $courseCount = TrainingCourse::where('company', $oldCode)->count();

                    Staff::where('company', $oldCode)->update(['company' => $newCode]);
                    DB::table('users')->where('company', $oldCode)->update(['company' => $newCode]);
                    Department::where('company', $oldCode)->update(['company' => $newCode]);
                    TrainingCourse::where('company', $oldCode)->update(['company' => $newCode]);

                    AuditLogger::log('update', 'master_data',
                        'Changed company code "' . $oldCode . '" → "' . $newCode . '" (name: "' . $request->name . '"). ' .
                        'Cascaded to: ' . $staffCount . ' staff, ' . $userCount . ' users, ' .
                        $deptCount . ' departments, ' . $courseCount . ' courses.'
                    );
                } else {
                    AuditLogger::log('update', 'master_data', 'Updated company "' . $request->name . '" #' . $id . '.');
                }
            });
        } elseif ($tab === 'courses') {
            TrainingCourse::findOrFail($id)->update($request->only(['code', 'title', 'training_type', 'company', 'start_date']));
            AuditLogger::log('update', 'master_data', 'Updated training course "' . $request->title . '" #' . $id . '.');
        } elseif ($tab === 'positions') {
            $pos = Position::findOrFail($id);
            $oldTitle = $pos->title;
            $newTitle = $request->input('title');
            $pos->update($request->only(['title']));
            if ($oldTitle !== $newTitle) {
                Staff::where('position', $oldTitle)->update(['position' => $newTitle]);
                DB::table('users')->where('position', $oldTitle)->update(['position' => $newTitle]);
            }
            AuditLogger::log('update', 'master_data', 'Updated position "' . $request->title . '" #' . $id . '.');
        } elseif ($tab === 'transport') {
            $tm = TransportMode::findOrFail($id);
            $oldName = $tm->name;
            $newName = $request->input('name');
            $tm->update($request->only(['name']));
            if ($oldName !== $newName) {
                DB::table('business_travel')->where('transport', $oldName)->update(['transport' => $newName]);
            }
            AuditLogger::log('update', 'master_data', 'Updated transport mode "' . $request->name . '" #' . $id . '.');
        } elseif ($tab === 'settings') {
            SystemSetting::findOrFail($id)->update($request->only(['setting_key', 'setting_value']));
            AuditLogger::log('update', 'master_data', 'Updated system setting "' . $request->setting_key . '" #' . $id . '.');
        }

        return redirect()->route('master-data.index', ['tab' => $tab])->with('success', 'Record updated successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $tab = $request->input('tab');
        if ($tab === 'departments') {
            $dept = Department::findOrFail($id);
            AuditLogger::log('delete', 'master_data', 'Deleted department "' . $dept->name . '" #' . $id . '.');
            $dept->delete();
        } elseif ($tab === 'companies') {
            $company = Company::findOrFail($id);
            AuditLogger::log('delete', 'master_data', 'Deleted company "' . $company->name . '" (code: ' . $company->code . ').');
            $company->delete();
        } elseif ($tab === 'courses') {
            $course = TrainingCourse::findOrFail($id);
            AuditLogger::log('delete', 'master_data', 'Deleted training course "' . $course->title . '" (' . $course->code . ').');
            $course->delete();
        } elseif ($tab === 'positions') {
            $pos = Position::findOrFail($id);
            AuditLogger::log('delete', 'master_data', 'Deleted position "' . $pos->title . '".');
            $pos->delete();
        } elseif ($tab === 'transport') {
            $tm = TransportMode::findOrFail($id);
            AuditLogger::log('delete', 'master_data', 'Deleted transport mode "' . $tm->name . '".');
            $tm->delete();
        } elseif ($tab === 'settings') {
            $setting = SystemSetting::findOrFail($id);
            AuditLogger::log('delete', 'master_data', 'Deleted system setting "' . $setting->setting_key . '".');
            $setting->delete();
        }

        return redirect()->route('master-data.index', ['tab' => $tab])->with('success', 'Record deleted successfully.');
    }

    public function listDetails(Request $request)
    {
        $type = $request->query('type');
        $id = $request->query('id');

        $headers = [];
        $rows = [];

        if ($type === 'dept_staff') {
            $headers = ['Name', 'Staff No.', 'Position'];
            $staff = Staff::where('department_id', $id)->where('is_active', 1)->orderBy('name')->get();
            foreach ($staff as $s) {
                $rows[] = [$s->name, $s->staff_no, $s->position ?? '—'];
            }
        } elseif ($type === 'company_depts') {
            $headers = ['Department Name', 'Active Staff'];
            $depts = Department::where('company', $id)
                ->withCount(['staff' => function($q) { $q->where('is_active', 1); }])
                ->orderBy('name')->get();
            foreach ($depts as $d) {
                $rows[] = [$d->name, $d->staff_count];
            }
        } elseif ($type === 'company_staff') {
            $headers = ['Name', 'Staff No.', 'Position', 'Department'];
            $staff = Staff::where('company', $id)->where('is_active', 1)->with('department')->orderBy('name')->get();
            
            foreach ($staff as $s) {
                $rows[] = [$s->name, $s->staff_no, $s->position, $s->department->name ?? '—'];
            }
        } elseif ($type === 'course_staff') {
            $headers = ['Name', 'Staff No.', 'Department'];
            $course = TrainingCourse::with(['staff' => function($q) { $q->with('department'); }])->find($id);
            if ($course) {
                foreach ($course->staff as $s) {
                    $rows[] = [$s->name, $s->staff_no, $s->department->name ?? '—'];
                }
            }
        } elseif ($type === 'position_staff') {
            $headers = ['Name', 'Staff No.', 'Company', 'Department'];
            $staff = Staff::where('position', $id)->where('is_active', 1)->with('department')->orderBy('name')->get();
            foreach ($staff as $s) {
                $rows[] = [$s->name, $s->staff_no, $s->company, $s->department->name ?? '—'];
            }
        } elseif ($type === 'transport_usage') {
            $headers = ['Staff Name', 'Destination', 'Departure Date', 'Return Date'];
            $trips = DB::table('business_travel')
                ->join('staff', 'business_travel.staff_id', '=', 'staff.id')
                ->where('business_travel.transport', $id)
                ->orderByDesc('business_travel.departure_date')
                ->select('staff.name as staff_name', 'business_travel.destination', 'business_travel.departure_date', 'business_travel.return_date')
                ->get();
            foreach ($trips as $t) {
                $rows[] = [
                    $t->staff_name, 
                    $t->destination, 
                    $t->departure_date ? date('d M Y', strtotime($t->departure_date)) : '—', 
                    $t->return_date ? date('d M Y', strtotime($t->return_date)) : '—'
                ];
            }
        }

        return response()->json(compact('headers', 'rows'));
    }

    public function staffList($deptId)
    {
        $staff = Staff::where('department_id', $deptId)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get(['id', 'name', 'staff_no', 'position']);
            
        return response()->json($staff);
    }
}
