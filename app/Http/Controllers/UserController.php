<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Staff;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Services\AuditLogger;
use PragmaRX\Google2FA\Google2FA;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['department', 'staff.department'])
            ->orderBy('role')
            ->orderBy('name')
            ->get();

        $departments = Department::orderBy('company')->orderBy('name')->get();
        $positions = Position::orderBy('title')->get();

        return view('users.index', compact('users', 'departments', 'positions'));
    }

    public function show(User $user)
    {
        $user->load(['staff.department', 'staff.travelRecords', 'bookings.room']);
        
        $trainings = [];
        if ($user->staff_id) {
            $trainings = \App\Models\TrainingAttendance::with('course')
                ->where('staff_id', $user->staff_id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('users.show', compact('user', 'trainings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:staff,admin_hr,admin_it,ceo',
            'staff_no' => 'nullable|string|max:50',
            'department_id' => 'nullable|exists:departments,id',
            'position' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:50',
        ]);

        $staff = null;
        if (!empty($validated['staff_no'])) {
            $staff = Staff::where('staff_no', $validated['staff_no'])->first();
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'staff_no' => $validated['staff_no'],
            'staff_id' => $staff ? $staff->id : null,
            'department_id' => $validated['department_id'],
            'position' => $validated['position'],
            'company' => $validated['company'] ?? 'FJB',
            'is_active' => true,
        ]);

        AuditLogger::log('create', 'users',
            'Created user account for ' . $user->name . ' (' . $user->role . ').',
            ['user_id' => $user->id, 'role' => $user->role]
        );

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $user)
    {
        $isSelf = auth()->id() === $user->id;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:6',
            'role' => $isSelf ? 'nullable' : 'required|string|in:staff,admin_hr,admin_it,ceo',
            'staff_no' => 'nullable|string|max:50',
            'department_id' => 'nullable|exists:departments,id',
            'position' => 'nullable|string|max:255',
        ]);

        $staff = null;
        if (!empty($validated['staff_no'])) {
            $staff = Staff::where('staff_no', $validated['staff_no'])->first();
        }

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'staff_no' => $validated['staff_no'],
            'staff_id' => $staff ? $staff->id : null,
            'department_id' => $validated['department_id'],
            'position' => $validated['position'],
        ];

        if (!$isSelf) {
            $userData['role'] = $validated['role'];
        }

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);

        // Sync email to linked staff record
        if ($user->staff_id) {
            $user->staff->update(['email' => $user->email]);
        }

        AuditLogger::log('update', 'users',
            'Updated user account for ' . $user->name . '.',
            ['user_id' => $user->id]
        );

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()->route('users.index')->with('error', 'You cannot delete your own account.');
        }

        AuditLogger::log('delete', 'users',
            'Deleted user account for ' . $user->name . ' (' . $user->role . ').',
            ['deleted_user_id' => $user->id, 'role' => $user->role]
        );
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    public function toggleStaffStatus(Request $request, User $user)
    {
        if (!$user->staff_id || !$user->staff) {
            return redirect()->route('users.index')->with('error', 'No staff record linked to this user.');
        }

        $staff = $user->staff;
        $staff->is_active = $request->boolean('is_active');
        $staff->save();

        $status = $staff->is_active ? 'activated' : 'deactivated';
        AuditLogger::log('toggle', 'staff',
            'Staff HR status for ' . $user->name . ' was ' . $status . '.',
            ['user_id' => $user->id, 'staff_id' => $staff->id, 'is_active' => $staff->is_active]
        );

        return redirect()->route('users.index')->with('success', 'Staff HR status updated.');
    }

    public function toggleActive(Request $request, User $user)
    {
        if (auth()->id() === $user->id) {
            return response()->json(['error' => 'You cannot disable your own account.'], 403);
        }

        $user->is_active = $request->is_active;
        $user->save();

        $status = $user->is_active ? 'activated' : 'deactivated';
        AuditLogger::log('toggle', 'users',
            'User account for ' . $user->name . ' was ' . $status . '.',
            ['user_id' => $user->id, 'is_active' => $user->is_active]
        );

        return redirect()->route('users.index')->with('success', 'User status updated.');
    }

    public function accountSecurity()
    {
        return view('users.account_security');
    }

    public function totpSetup(Request $request)
    {
        $user = auth()->user();
        $google2fa = new Google2FA();

        $pendingSecret = $google2fa->generateSecretKey();
        $request->session()->put('totp_pending_secret', $pendingSecret);

        $qrUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email ?: $user->staff_no,
            $pendingSecret
        );

        $qrSvg = (string) QrCode::format('svg')->size(200)->generate($qrUrl);

        return view('users.totp_setup', compact('qrSvg', 'pendingSecret'));
    }

    public function totpConfirm(Request $request)
    {
        $request->validate([
            'totp_code' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ]);

        $pendingSecret = $request->session()->get('totp_pending_secret');

        if (!$pendingSecret) {
            return redirect()->route('totp.setup')
                ->with('error', 'Setup session expired. Please start again.');
        }

        $google2fa = new Google2FA();

        if (!$google2fa->verifyKey($pendingSecret, $request->totp_code)) {
            return back()->with('error', 'Invalid code. Check your Authenticator app and try again.');
        }

        $user = auth()->user();
        $user->totp_secret = $pendingSecret;
        $user->save();

        $request->session()->forget('totp_pending_secret');

        AuditLogger::log('update', 'users',
            'Microsoft Authenticator (TOTP) set up for ' . $user->name . '.',
            ['user_id' => $user->id]
        );

        return redirect()->route('account.security')
            ->with('success', 'Microsoft Authenticator set up successfully. You can now use it to reset your password.');
    }

    public function totpRemove(Request $request)
    {
        $user = auth()->user();
        $user->totp_secret = null;
        $user->save();

        AuditLogger::log('update', 'users',
            'Microsoft Authenticator (TOTP) removed for ' . $user->name . '.',
            ['user_id' => $user->id]
        );

        return redirect()->route('account.security')
            ->with('success', 'Microsoft Authenticator removed.');
    }

    public function searchStaff(Request $request)
    {
        $staffId = trim($request->sr_staffid ?? '');
        $name    = trim($request->sr_name ?? '');
        $deptId  = (int)($request->sr_dept ?? 0);

        if (!$staffId && !$name && !$deptId) {
            return response()->json([]);
        }

        $query = Staff::with('department')->where('is_active', 1);

        $query->where(function($q) use ($staffId, $name, $deptId) {
            if ($staffId) {
                $q->orWhere('staff_no', 'LIKE', "%{$staffId}%");
            }
            if ($name) {
                $q->orWhere('name', 'LIKE', "%{$name}%");
            }
            if ($deptId) {
                $q->orWhere('department_id', $deptId);
            }
        });

        $staff = $query->orderBy('name')->limit(50)->get();

        return response()->json($staff->map(function($s) {
            return [
                'id' => $s->id,
                'staff_no' => $s->staff_no,
                'name' => $s->name,
                'position' => $s->position,
                'email' => $s->email,
                'department_id' => $s->department_id,
                'dept_name' => $s->department ? $s->department->name : '—'
            ];
        }));
    }
}
