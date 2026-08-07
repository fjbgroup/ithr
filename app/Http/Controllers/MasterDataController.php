<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Department;
use App\Models\Position;
use App\Models\TrainingCourse;
use App\Models\TransportMode;
use App\Models\Staff;
use App\Models\MeetingRoom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\MeetingRoomPicAssigned;
use App\Services\AuditLogger;

class MasterDataController extends Controller
{
    public function index(Request $request)
    {
        $activeTab = $request->query('tab', 'departments');
        $validTabs = ['departments', 'companies', 'courses', 'positions', 'transport', 'faqs_hr', 'rooms'];
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
            'faqs_hr'     => \App\Models\Faq::where('system', 'HR')->count(),
            'rooms'       => MeetingRoom::count(),
        ];

        $tabLabels = [
            'departments' => 'Department',
            'companies'   => 'Company',
            'courses'     => 'Training Course',
            'positions'   => 'Position',
            'transport'   => 'Transport Mode',
            'faqs_hr'     => 'Chatbot FAQ',
            'rooms'       => 'Meeting Room',
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
        } elseif ($activeTab === 'faqs_hr') {
            $query = \App\Models\Faq::query();
            $query->where('system', 'HR');
            
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('question', 'LIKE', "%$search%")
                      ->orWhere('answer', 'LIKE', "%$search%");
                });
            }
            $data['rows'] = $query->orderBy('sort_order')->get();
        } elseif ($activeTab === 'rooms') {
            $query = MeetingRoom::with('pics');
            if ($search) {
                $query->where('name', 'LIKE', "%$search%");
            }
            $data['rows'] = $query->orderBy('name')->get();
        }

        $allCompanies = Company::orderBy('code')->get();
        
        $allUsers = [];
        $colorMap = [];
        if ($activeTab === 'rooms') {
            $allUsers = User::where('is_active', true)->orderBy('name')->get();
            $colorMap = [
                'room-navy' => '#1e3a8a', 'room-blue' => '#3b82f6', 'room-sky' => '#0ea5e9',
                'room-indigo' => '#6366f1', 'room-purple' => '#a855f7', 'room-pink' => '#ec4899',
                'room-rose' => '#f43f5e', 'room-red' => '#ef4444', 'room-orange' => '#f97316',
                'room-amber' => '#f59e0b', 'room-yellow' => '#eab308', 'room-lime' => '#84cc16',
                'room-green' => '#22c55e', 'room-emerald' => '#10b981', 'room-teal' => '#14b8a6',
                'room-cyan' => '#06b6d4', 'room-slate' => '#64748b'
            ];
        }

        return view('master_data.index', compact('activeTab', 'search', 'cFilter', 'data', 'counts', 'tabLabels', 'allCompanies', 'allUsers', 'colorMap'));
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
        } elseif ($tab === 'faqs_hr') {
            $data = $request->only(['system', 'question', 'answer', 'sort_order']);
            $data['system'] = 'HR';
            $data['is_active'] = $request->has('is_active') ? 1 : 0;
            if (empty($data['sort_order'])) $data['sort_order'] = 0;
            \App\Models\Faq::create($data);
            AuditLogger::log('create', 'master_data', 'Added FAQ for system "' . $data['system'] . '".');
        } elseif ($tab === 'rooms') {
            $validated = $request->validate([
                'room_name' => 'required|string|max:255',
                'room_description' => 'nullable|string',
                'room_capacity' => 'required|integer|min:1',
                'room_color' => 'required|string',
                'room_pics' => 'nullable|array',
                'room_pics.*' => 'nullable|exists:users,id',
            ]);

            $room = MeetingRoom::create([
                'name' => $validated['room_name'],
                'description' => $validated['room_description'],
                'capacity' => $validated['room_capacity'],
                'color_class' => $validated['room_color'],
            ]);

            if (!empty($validated['room_pics'])) {
                $picIds = array_slice(array_filter(array_unique($validated['room_pics'])), 0, 2);
                $level = 1;
                foreach ($picIds as $pid) {
                    if ($pid) {
                        $room->pics()->attach($pid, ['level' => $level, 'added_by' => Auth::id()]);
                        $level++;
                        
                        $picUser = User::find($pid);
                        if ($picUser && $picUser->email) {
                            Mail::to($picUser->email)->queue(new MeetingRoomPicAssigned($room->name, $picUser->name));
                        }
                    }
                }
            }
            AuditLogger::log('create', 'master_data', 'Added meeting room "' . $room->name . '".');
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
        } elseif ($tab === 'faqs_hr') {
            $data = $request->only(['question', 'answer', 'sort_order']);
            $data['is_active'] = $request->has('is_active') ? 1 : 0;
            if (empty($data['sort_order'])) $data['sort_order'] = 0;
            \App\Models\Faq::findOrFail($id)->update($data);
            AuditLogger::log('update', 'master_data', 'Updated FAQ #' . $id . '.');
        } elseif ($tab === 'rooms') {
            $validated = $request->validate([
                'room_name' => 'required|string|max:255',
                'room_description' => 'nullable|string',
                'room_capacity' => 'required|integer|min:1',
                'room_color' => 'required|string',
                'room_pics' => 'nullable|array',
                'room_pics.*' => 'nullable|exists:users,id',
            ]);

            $room = MeetingRoom::findOrFail($id);
            $room->update([
                'name' => $validated['room_name'],
                'description' => $validated['room_description'],
                'capacity' => $validated['room_capacity'],
                'color_class' => $validated['room_color'],
            ]);

            $oldPicIds = $room->pics->pluck('id')->toArray();
            $room->pics()->detach();
            if (!empty($validated['room_pics'])) {
                $picIds = array_slice(array_filter(array_unique($validated['room_pics'])), 0, 2);
                $level = 1;
                foreach ($picIds as $pid) {
                    if ($pid) {
                        $room->pics()->attach($pid, ['level' => $level, 'added_by' => Auth::id()]);
                        $level++;
                        
                        if (!in_array($pid, $oldPicIds)) {
                            $picUser = User::find($pid);
                            if ($picUser && $picUser->email) {
                                Mail::to($picUser->email)->queue(new MeetingRoomPicAssigned($room->name, $picUser->name));
                            }
                        }
                    }
                }
            }
            AuditLogger::log('update', 'master_data', 'Updated meeting room "' . $room->name . '".');
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
        } elseif ($tab === 'faqs_hr') {
            $faq = \App\Models\Faq::findOrFail($id);
            AuditLogger::log('delete', 'master_data', 'Deleted FAQ #' . $id . '.');
            $faq->delete();
        } elseif ($tab === 'rooms') {
            $room = MeetingRoom::findOrFail($id);
            AuditLogger::log('delete', 'master_data', 'Deleted meeting room "' . $room->name . '".');
            $room->bookings()->delete();
            $room->pics()->detach();
            $room->delete();
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

    public function companyDepts($companyId)
    {
        $company = \App\Models\Company::findOrFail($companyId);
        $depts = \App\Models\Department::where('company', $company->code)
            ->orderBy('name')
            ->get(['id', 'name']);
            
        return response()->json($depts);
    }

    public function companyStaff($companyId)
    {
        $company = \App\Models\Company::findOrFail($companyId);
        $staff = \App\Models\Staff::where('company', $company->code)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get(['id', 'name', 'staff_no', 'position']);
            
        return response()->json($staff);
    }

    public function courseAttendance($id)
    {
        $course = \App\Models\TrainingCourse::findOrFail($id);
        $attendance = \App\Models\TrainingAttendance::with('staff')
            ->where('course_id', $course->id)
            ->get()
            ->map(function ($att) {
                return [
                    'name' => $att->staff->name ?? 'Unknown',
                    'staff_no' => $att->staff->staff_no ?? '-',
                    'status' => $att->status ?? 'Attended'
                ];
            });
            
        return response()->json($attendance);
    }

    public function positionStaff($id)
    {
        $position = \App\Models\Position::findOrFail($id);
        $staff = \App\Models\Staff::where('position', $position->title)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get(['name', 'staff_no', 'department_id'])
            ->map(function ($s) {
                return [
                    'name' => $s->name,
                    'staff_no' => $s->staff_no,
                    'department' => $s->department->name ?? '-'
                ];
            });
            
        return response()->json($staff);
    }

    public function transportTravel($id)
    {
        $transport = \App\Models\TransportMode::findOrFail($id);
        $travels = \App\Models\BusinessTravel::with('staff')
            ->where('transport', $transport->name)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($t) {
                return [
                    'ref_no' => 'TRV-' . str_pad($t->id, 5, '0', STR_PAD_LEFT),
                    'staff_name' => $t->staff->name ?? 'Unknown',
                    'destination' => $t->destination ?? '-'
                ];
            });
            
        return response()->json($travels);
    }
}
