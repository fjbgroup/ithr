<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Department;
use App\Models\Position;
use App\Models\SystemSetting;
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
        $validTabs = ['departments', 'companies', 'courses', 'positions', 'transport', 'settings'];
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
            'settings'    => SystemSetting::count(),
        ];

        $tabLabels = [
            'departments' => 'Department',
            'companies'   => 'Company',
            'courses'     => 'Training Course',
            'positions'   => 'Position',
            'transport'   => 'Transport Mode',
            'settings'    => 'System Setting',
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
        } elseif ($activeTab === 'settings') {
            $query = SystemSetting::query();
            if ($search) {
                $query->where('setting_key', 'LIKE', "%$search%")
                      ->orWhere('setting_value', 'LIKE', "%$search%");
            }
            $data['rows'] = $query->orderBy('setting_key')->get();
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
            Company::findOrFail($id)->update($request->only(['code', 'name']));
            AuditLogger::log('update', 'master_data', 'Updated company "' . $request->name . '" #' . $id . '.');
        } elseif ($tab === 'courses') {
            TrainingCourse::findOrFail($id)->update($request->only(['code', 'title', 'training_type', 'company', 'start_date']));
            AuditLogger::log('update', 'master_data', 'Updated training course "' . $request->title . '" #' . $id . '.');
        } elseif ($tab === 'positions') {
            Position::findOrFail($id)->update($request->only(['title']));
            AuditLogger::log('update', 'master_data', 'Updated position "' . $request->title . '" #' . $id . '.');
        } elseif ($tab === 'transport') {
            TransportMode::findOrFail($id)->update($request->only(['name']));
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
            if ($dept->staff()->where('is_active', 1)->count() > 0) {
                return redirect()->route('master-data.index', ['tab' => $tab])->with('error', 'Cannot delete department with active staff.');
            }
            AuditLogger::log('delete', 'master_data', 'Deleted department "' . $dept->name . '" #' . $id . '.');
            $dept->delete();
        } elseif ($tab === 'companies') {
            $company = Company::findOrFail($id);
            $staffCount = Staff::where('company', $company->code)->where('is_active', 1)->count();
            $deptCount = Department::where('company', $company->code)->count();
            if ($staffCount > 0 || $deptCount > 0) {
                return redirect()->route('master-data.index', ['tab' => $tab])->with('error', 'Cannot delete company in use.');
            }
            AuditLogger::log('delete', 'master_data', 'Deleted company "' . $company->name . '" (code: ' . $company->code . ').');
            $company->delete();
        } elseif ($tab === 'courses') {
            $course = TrainingCourse::findOrFail($id);
            if ($course->staff()->count() > 0) {
                return redirect()->route('master-data.index', ['tab' => $tab])->with('error', 'Cannot delete course with attendances.');
            }
            AuditLogger::log('delete', 'master_data', 'Deleted training course "' . $course->title . '" (' . $course->code . ').');
            $course->delete();
        } elseif ($tab === 'positions') {
            $pos = Position::findOrFail($id);
            if (Staff::where('position', $pos->title)->where('is_active', 1)->count() > 0) {
                return redirect()->route('master-data.index', ['tab' => $tab])->with('error', 'Cannot delete position in use.');
            }
            AuditLogger::log('delete', 'master_data', 'Deleted position "' . $pos->title . '".');
            $pos->delete();
        } elseif ($tab === 'transport') {
            $tm = TransportMode::findOrFail($id);
            if (DB::table('business_travel')->where('transport', $tm->name)->count() > 0) {
                return redirect()->route('master-data.index', ['tab' => $tab])->with('error', 'Cannot delete transport mode in use.');
            }
            AuditLogger::log('delete', 'master_data', 'Deleted transport mode "' . $tm->name . '".');
            $tm->delete();
        } elseif ($tab === 'settings') {
            $setting = SystemSetting::findOrFail($id);
            AuditLogger::log('delete', 'master_data', 'Deleted system setting "' . $setting->setting_key . '".');
            $setting->delete();
        }

        return redirect()->route('master-data.index', ['tab' => $tab])->with('success', 'Record deleted successfully.');
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
