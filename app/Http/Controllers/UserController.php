<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Staff;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Services\AuditLogger;
use App\Models\IT\EmailSetting;
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

        if (EmailSetting::requireStaffRegistry() && !$staff) {
            return back()->withErrors(['staff_no' => 'Staff record is required and must exist in the Staff Registry. Please select a valid staff member.'])->withInput();
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

    public function resetPassword(Request $request, User $user)
    {
        $user->password = Hash::make('password');
        $user->save();

        AuditLogger::log('update', 'users',
            'Reset password for ' . $user->name . ' to default.',
            ['user_id' => $user->id]
        );

        return redirect()->route('users.index')->with('success', 'Password reset successfully for ' . $user->name . '.');
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

    /**
     * Admin (IT) only — flip the global Microsoft Authenticator (TOTP / 2FA)
     * master switch on/off. When OFF, 2FA is skipped system-wide for login and
     * password reset; users keep their existing setup and it resumes when the
     * switch is turned back ON.
     */
    public function toggleTotp(Request $request)
    {
        if (! Auth::user()->isAdminIT()) {
            abort(403);
        }

        $enable = $request->boolean('enable');
        EmailSetting::setTotpEnabled($enable);

        AuditLogger::log('update', 'settings',
            'Microsoft Authenticator (2FA) ' . ($enable ? 'ENABLED' : 'DISABLED') . ' (global master switch).',
            ['totp_enabled' => $enable]
        );

        return redirect()->back()->with('success',
            $enable
                ? 'Microsoft Authenticator (2FA) has been enabled.'
                : 'Microsoft Authenticator (2FA) has been disabled. It will be skipped for login and password reset until you re-enable it.'
        );
    }

    /**
     * Admin only — flip the global "require staff registry" master switch.
     */
    public function toggleRequireStaffRegistry(Request $request)
    {
        if (! Auth::user()->isAdminIT() && ! Auth::user()->isAdminHR()) {
            abort(403);
        }

        $enable = $request->boolean('enable');
        EmailSetting::setRequireStaffRegistry($enable);

        AuditLogger::log('update', 'settings',
            'Require Staff Registry ' . ($enable ? 'ENABLED' : 'DISABLED') . ' (global master switch).',
            ['require_staff_registry' => $enable]
        );

        return redirect()->back()->with('success',
            $enable
                ? 'Creating user accounts now requires an existing Staff Registry record.'
                : 'Creating user accounts without a Staff Registry record is now allowed.'
        );
    }

    /**
     * Admin (IT) only — flip the global "email sending" master switch.
     * When OFF, the whole HR system stops sending outgoing email until
     * re-enabled. Users with Microsoft Authenticator (TOTP) are unaffected.
     */
    public function toggleEmailSending(Request $request)
    {
        if (! Auth::user()->isAdminIT()) {
            abort(403);
        }

        $enable = $request->boolean('enable');
        EmailSetting::setEmailEnabled($enable);

        AuditLogger::log('update', 'settings',
            'Email sending ' . ($enable ? 'ENABLED' : 'DISABLED') . ' (global master switch).',
            ['email_enabled' => $enable]
        );

        return redirect()->back()->with('success',
            $enable
                ? 'Email sending has been enabled.'
                : 'Email sending has been disabled. The system will not send any email until you re-enable it.'
        );
    }

    /**
     * Admin only - toggle maintenance mode for specific systems.
     */
    public function toggleSystemStatus(Request $request, $system)
    {
        if (! Auth::user()->isAdminIT()) {
            abort(403);
        }

        if (! in_array($system, ['it', 'wt', 'lms'])) {
            abort(404);
        }

        $enable = $request->boolean('enable');
        EmailSetting::setSystemEnabled($system, $enable);

        $systemName = strtoupper($system);
        AuditLogger::log('update', 'settings',
            "{$systemName} System " . ($enable ? 'ENABLED' : 'DISABLED') . ' (maintenance mode toggle).',
            ["system_{$system}_enabled" => $enable]
        );

        return redirect()->back()->with('success',
            $enable
                ? "{$systemName} System has been enabled (Maintenance mode off)."
                : "{$systemName} System has been disabled (Maintenance mode on)."
        );
    }

    public function searchStaff(Request $request)
    {
        $staffId = trim($request->sr_staffid ?? '');
        $name    = trim($request->sr_name ?? '');
        $deptId  = (int)($request->sr_dept ?? 0);
        // Combined term used by the IR and Travel autocompletes (matches staff no OR name).
        $term    = trim($request->search_staff ?? '');

        if (!$staffId && !$name && !$deptId && !$term) {
            return response()->json([]);
        }

        $query = Staff::with('department')->where('is_active', 1);

        $query->where(function($q) use ($staffId, $name, $deptId, $term) {
            if ($staffId) {
                $q->orWhere('staff_no', 'LIKE', "%{$staffId}%");
            }
            if ($name) {
                $q->orWhere('name', 'LIKE', "%{$name}%");
            }
            if ($deptId) {
                $q->orWhere('department_id', $deptId);
            }
            if ($term) {
                $q->orWhere('staff_no', 'LIKE', "%{$term}%")
                  ->orWhere('name', 'LIKE', "%{$term}%");
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

    /**
     * Update the logged-in user's profile, including avatar upload and staff details sync.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:30',
            'gender' => 'nullable|string|in:Male,Female,Other',
            'date_of_birth' => 'nullable|date',
            'ic_number' => 'nullable|string|max:30',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $userUpdate = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        // Handle Avatar Upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $userUpdate['avatar'] = $path;
        }

        $user->update($userUpdate);

        // Sync with linked Staff record
        if ($user->staff_id) {
            $staff = Staff::find($user->staff_id);
            if ($staff) {
                $staff->update([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone_number' => $validated['phone_number'] ?? null,
                    'gender' => $validated['gender'] ?? null,
                    'date_of_birth' => $validated['date_of_birth'] ?? null,
                    'ic_number' => $validated['ic_number'] ?? null,
                ]);
            }
        }

        AuditLogger::log('update', 'profile',
            'Updated self profile and personal details for ' . $user->name . '.',
            ['user_id' => $user->id]
        );

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}
