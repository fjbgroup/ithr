<?php

namespace App\Http\Controllers;

use App\Mail\TravelMail;
use App\Models\BusinessTravel;
use App\Models\Department;
use App\Models\Notification;
use App\Models\Staff;
use App\Models\TransportMode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Services\AuditLogger;

class TravelController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->isAdmin() || $user->isCeo();
        $isStaff = $user->role === 'staff';

        $search = $request->query('q');
        $dept_filter = $request->query('dept');
        $month_filter = $request->query('month');
        $staff_filter = $request->query('staff_id');

        $query = BusinessTravel::with(['staff.department', 'creator']);

        if ($isStaff) {
            $query->whereHas('staff', function($q) use ($user) {
                $q->where('staff_no', $user->staff_no);
            });
        } else {
            if ($search) {
                $query->where(function($q) use ($search) {
                    $like = "%$search%";
                    $q->where('destination', 'like', $like)
                      ->orWhere('purpose', 'like', $like)
                      ->orWhereHas('staff', function($sq) use ($like) {
                          $sq->where('name', 'like', $like)
                            ->orWhere('staff_no', 'like', $like);
                      });
                });
            }
            if ($dept_filter) {
                $query->whereHas('staff', function($q) use ($dept_filter) {
                    $q->where('department_id', $dept_filter);
                });
            }
            if ($staff_filter) {
                $query->where('staff_id', $staff_filter);
            }
        }

        if ($month_filter) {
            $query->where(DB::raw("DATE_FORMAT(departure_date, '%Y-%m')"), $month_filter);
        }

        $travels = $query->orderBy('departure_date', 'DESC')->get();

        // Stats
        $today = Carbon::today()->format('Y-m-d');
        $totalTrips = $travels->count();
        $uniqueStaff = $travels->pluck('staff_id')->unique()->count();
        $activeNow = $travels->filter(fn($t) => $t->departure_date <= $today && $t->return_date >= $today)->count();
        $upcomingTrips = $travels->filter(fn($t) => $t->departure_date > $today)->count();

        $departments = $isStaff ? collect() : Department::orderBy('company')->orderBy('name')->get();
        $transportModes = TransportMode::orderBy('name')->get();

        return view('travel.index', [
            'travels' => $travels,
            'isAdmin' => $isAdmin,
            'isStaff' => $isStaff,
            'search' => $search,
            'dept_filter' => $dept_filter,
            'month_filter' => $month_filter,
            'staff_filter' => $staff_filter,
            'totalTrips' => $totalTrips,
            'uniqueStaff' => $uniqueStaff,
            'activeNow' => $activeNow,
            'upcomingTrips' => $upcomingTrips,
            'departments' => $departments,
            'transportModes' => $transportModes
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'destination' => 'required',
            'departure_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:departure_date',
        ]);

        if ($user->role === 'staff') {
            $staff = Staff::where('staff_no', $user->staff_no)->first();
            $staff_id = $staff->id;
        } else {
            $staff_id = $request->staff_id;
        }

        $travel = BusinessTravel::create([
            'staff_id' => $staff_id,
            'destination' => $request->destination,
            'purpose' => $request->purpose,
            'departure_date' => $request->departure_date,
            'return_date' => $request->return_date,
            'transport' => $request->transport,
            'notes' => $request->notes,
            'created_by' => $user->id,
        ]);

        if ($user->role === 'staff') {
            $this->notifyAdmins($travel, 'created');
        }

        $travelStaff = Staff::find($staff_id);
        AuditLogger::log('create', 'travel',
            'Added travel record for ' . ($travelStaff->name ?? 'staff #' . $staff_id) . ' to ' . $request->destination . ' (' . $request->departure_date . ' – ' . $request->return_date . ').',
            ['travel_id' => $travel->id]
        );

        return redirect()->route('travel.index')->with('success', 'Travel record saved successfully');
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $travel = BusinessTravel::findOrFail($id);

        if ($user->role === 'staff') {
            $staff = Staff::where('staff_no', $user->staff_no)->first();
            if ($travel->staff_id != $staff->id) {
                abort(403);
            }
            $staff_id = $staff->id;
        } else {
            $staff_id = $request->staff_id;
        }

        $request->validate([
            'destination' => 'required',
            'departure_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:departure_date',
        ]);

        $travel->update([
            'staff_id' => $staff_id,
            'destination' => $request->destination,
            'purpose' => $request->purpose,
            'departure_date' => $request->departure_date,
            'return_date' => $request->return_date,
            'transport' => $request->transport,
            'notes' => $request->notes,
        ]);

        if ($user->role === 'staff') {
            $this->notifyAdmins($travel, 'updated');
        }

        AuditLogger::log('update', 'travel',
            'Updated travel record #' . $id . ' for ' . ($travel->staff->name ?? 'staff') . ' to ' . $request->destination . '.',
            ['travel_id' => $id]
        );

        return redirect()->route('travel.index')->with('success', 'Travel record updated successfully');
    }

    private function notifyAdmins(BusinessTravel $travel, string $action)
    {
        $user = Auth::user();
        $travel->load('staff');
        $staffName = $travel->staff->name ?? $user->name;

        $admins = User::whereIn('role', ['admin_it', 'admin_hr'])
            ->where('is_active', 1)
            ->get();

        \Log::info("Travel Notification: Staff '{$staffName}' {$action} a travel record. Found " . $admins->count() . " active admins.");

        foreach ($admins as $admin) {
            $title = 'Travel Record ' . ucfirst($action);
            $message = $staffName . ' ' . $action . ' their travel record to ' . $travel->destination . ' (' . $travel->departure_date . ' – ' . $travel->return_date . ').';

            Notification::create([
                'user_id' => $admin->id,
                'type'    => 'travel',
                'title'   => $title,
                'message' => $message,
                'link'    => route('travel.index'),
            ]);

            if ($admin->email) {
                try {
                    Mail::to($admin->email)->send(new TravelMail(
                        $title,
                        $staffName . ' has ' . $action . ' their own travel record.',
                        $travel
                    ));
                    \Log::info("TravelMail sent to admin ID {$admin->id} ({$admin->email}) for action: {$action}");
                } catch (\Exception $e) {
                    \Log::error("TravelMail failed for admin ID {$admin->id} ({$admin->email}): " . $e->getMessage());
                }
            } else {
                \Log::warning("Travel Notification: Admin ID {$admin->id} has no email address. Skipping email.");
            }
        }
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $travel = BusinessTravel::with('staff')->findOrFail($id);

        if ($user->role === 'staff') {
            $staff = Staff::where('staff_no', $user->staff_no)->first();
            if ($travel->staff_id != $staff->id) {
                abort(403);
            }
        }

        AuditLogger::log('delete', 'travel',
            'Deleted travel record #' . $id . ' for ' . ($travel->staff->name ?? 'staff') . ' to ' . $travel->destination . '.',
            ['travel_id' => $id]
        );

        $travel->delete();
        return redirect()->route('travel.index')->with('success', 'Travel record deleted successfully');
    }

    public function show($id)
    {
        $travel = BusinessTravel::with('staff')->findOrFail($id);
        return response()->json($travel);
    }
}
