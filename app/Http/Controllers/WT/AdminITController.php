<?php

namespace App\Http\Controllers\WT;

use App\Models\Staff;
use App\Models\WT\AccessRequest;
use App\Models\WT\Handover;
use App\Models\WT\PasswordResetRequest;
use App\Models\WT\User;
use App\Models\WT\UserActivityLog;
use App\Services\SystemNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminITController extends Controller
{
    public function index()
    {
        $roleCounts = [
            'total' => User::whereIn('wt_role', ['admin_it', 'admin'])->count(),
            'admin_it' => User::where('wt_role', 'admin_it')->count(),
            'admin' => User::where('wt_role', 'admin')->count(),
        ];

        $users = User::query()
            ->whereIn('wt_role', ['admin_it', 'admin'])
            ->select('users.*')
            ->selectSub(
                AccessRequest::selectRaw('COUNT(*)')
                    ->whereColumn('access_requests.user_id', 'users.id'),
                'request_count'
            )
            ->selectSub(
                Handover::selectRaw('COUNT(*)')
                    ->whereColumn('walkie_talkie_handovers.user_id', 'users.id'),
                'handover_count'
            )
            ->selectSub(
                UserActivityLog::selectRaw('MAX(created_at)')
                    ->whereColumn('user_activity_logs.user_id', 'users.id'),
                'last_activity_at'
            )
            ->orderByRaw("
                CASE
                    WHEN wt_role = 'admin_it' THEN 1
                    ELSE 2
                END
            ")
            ->orderByDesc('id')
            ->get();

        $pendingPasswordResetRequests = PasswordResetRequest::with('user')
            ->where('status', 'Pending')
            ->orderByDesc('requested_at')
            ->get();

        return view('wt.admin.adminit.adminit', [
            'pageTitle' => 'Management ICT',
            'roleCounts' => $roleCounts,
            'users' => $users,
            'accounts' => $users,
            'pendingPasswordResetRequests' => $pendingPasswordResetRequests,
        ]);
    }

    public function users()
    {
        return $this->index();
    }

    public function staffSearch(Request $request)
    {
        $q = trim($request->get('q', ''));
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $results = Staff::with('department')
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('staff_no', 'like', "%{$q}%");
            })
            ->where('is_active', 1)
            ->orderBy('name')
            ->limit(15)
            ->get()
            ->map(fn($s) => [
                'staff_no'   => $s->staff_no,
                'name'       => Str::upper($s->name),
                'dept_name'  => Str::upper($s->department?->name ?? ''),
                'position'   => Str::upper($s->position ?? ''),
            ]);

        return response()->json($results);
    }

    public function storeManager(Request $request)
    {
        $request->validate([
            'staff_id'  => 'required|string|max:50',
            'full_name' => 'required|string|max:255',
            'department'=> 'required|string|max:255',
            'position'  => 'required|string|max:255',
        ]);

        $staffNo  = trim($request->staff_id);
        $existing = User::whereRaw('TRIM(staff_no) = ?', [$staffNo])->first();

        if ($existing) {
            if ($existing->wt_role !== null) {
                UserActivityLog::create([
                    'user_id'       => Auth::guard('wt')->id(),
                    'username'      => Auth::guard('wt')->user()->username,
                    'event_type'    => 'user_management',
                    'event_action'  => 'create_executive_account_blocked',
                    'event_details' => $existing->full_name . ' already has WT access (wt_role: ' . $existing->wt_role . ')',
                    'ip_address'    => $request->ip(),
                    'user_agent'    => $request->userAgent(),
                    'created_at'    => now(),
                ]);

                return back()
                    ->withInput()
                    ->with('error', $existing->full_name . ' already has Walkie Talkie system access.');
            }

            // Staff without WT access — grant it by setting wt_role.
            // Their HR role and IT role remain unchanged.
            $existing->update([
                'full_name'  => trim($request->full_name),
                'dept_name'  => Str::upper(trim($request->department)),
                'position'   => Str::upper(trim($request->position)),
                'wt_role'    => 'admin',
            ]);

            if ($request->filled('password')) {
                $request->validate(['password' => 'min:6|confirmed']);
                $existing->update(['password' => Hash::make($request->password)]);
            }

            UserActivityLog::create([
                'user_id'       => Auth::guard('wt')->id(),
                'username'      => Auth::guard('wt')->user()->username,
                'event_type'    => 'user_management',
                'event_action'  => 'grant_wt_access',
                'event_details' => 'Granted WT Executive access to existing user #' . $existing->id . ' - ' . $existing->username,
                'ip_address'    => $request->ip(),
                'user_agent'    => $request->userAgent(),
                'created_at'    => now(),
            ]);

            return redirect()
                ->route('wt.admin.users.index')
                ->with('success', 'WT access granted to ' . $existing->full_name . ' as Executive.');
        }

        // Brand-new user — password is required
        $request->validate(['password' => 'required|string|min:6|confirmed']);

        if (\App\Models\IT\EmailSetting::requireStaffRegistry()) {
            $staff = \App\Models\Staff::where('staff_no', $staffNo)->first();
            if (!$staff) {
                return back()->withErrors(['staff_id' => 'Staff record is required and must exist in the Staff Registry.'])->withInput();
            }
        }

        $user = User::create([
            'staff_id'  => $staffNo,
            'username'  => $staffNo,
            'full_name' => trim($request->full_name),
            'dept_name' => Str::upper(trim($request->department)),
            'position'  => Str::upper(trim($request->position)),
            'password'  => Hash::make($request->password),
            'role'      => 'staff',
            'wt_role'   => 'admin',
            'is_active' => 1,
        ]);

        UserActivityLog::create([
            'user_id'       => Auth::guard('wt')->id(),
            'username'      => Auth::guard('wt')->user()->username,
            'event_type'    => 'user_management',
            'event_action'  => 'create_executive_account',
            'event_details' => 'Created executive account #' . $user->id . ' - ' . $user->username,
            'ip_address'    => $request->ip(),
            'user_agent'    => $request->userAgent(),
            'created_at'    => now(),
        ]);

        return redirect()
            ->route('wt.admin.users.index')
            ->with('success', 'Executive account created successfully.');
    }


    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'staff_id' => ['required', 'string', 'max:50', Rule::unique(User::class, 'staff_no')->ignore($user->id)],
            'username' => ['required', 'string', 'max:50', Rule::unique(User::class, 'staff_no')->ignore($user->id)],
            'full_name' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'wt_role' => 'required|in:admin,admin_it',
        ]);

        $original = [
            'staff_id' => $user->staff_id,
            'username' => $user->username,
            'full_name' => $user->full_name,
            'department' => $user->department,
            'position' => $user->position,
            'wt_role' => $user->wt_role,
        ];

        $user->update($validated);

        UserActivityLog::create([
            'user_id' => Auth::guard('wt')->id(),
            'username' => Auth::guard('wt')->user()->username,
            'event_type' => 'user_management',
            'event_action' => 'update',
            'event_details' => 'Updated user #' . $user->user_id . ' from ' . json_encode($original) . ' to ' . json_encode($validated),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return back()->with('success', 'User information updated successfully.');
    }

    public function destroyUser(Request $request, User $user)
    {
        if (Auth::guard('wt')->id() === $user->user_id) {
            return back()->with('error', 'You cannot revoke your own WT access.');
        }

        $userLabel = $user->username . ' (' . ($user->staff_id ?: '-') . ')';
        $userId = $user->user_id;

        $user->update(['wt_role' => null]);

        UserActivityLog::create([
            'user_id' => Auth::guard('wt')->id(),
            'username' => Auth::guard('wt')->user()->username,
            'event_type' => 'user_management',
            'event_action' => 'revoke_wt_access',
            'event_details' => 'Revoked WT access for user #' . $userId . ' - ' . $userLabel,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return back()->with('success', 'WT access revoked for ' . $user->full_name . '.');
    }

    public function resetUserPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        UserActivityLog::create([
            'user_id' => Auth::guard('wt')->id(),
            'username' => Auth::guard('wt')->user()->username,
            'event_type' => 'user_management',
            'event_action' => 'reset_password',
            'event_details' => 'Reset password for user #' . $user->user_id . ' - ' . $user->username,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return back()->with('success', 'User password reset successfully.');
    }

    public function approvePasswordReset(Request $request, PasswordResetRequest $passwordResetRequest)
    {
        if ($passwordResetRequest->status !== 'Pending') {
            return back()->with('error', 'This password reset request has already been processed.');
        }

        $user = $passwordResetRequest->user ?: User::where('staff_no', $passwordResetRequest->staff_id)->first();
        if (! $user) {
            $passwordResetRequest->update([
                'status' => 'Rejected',
                'reviewed_by' => Auth::guard('wt')->id(),
                'reviewed_at' => now(),
            ]);

            return back()->with('error', 'Unable to find the user for this password reset request.');
        }

        $passwordResetRequest->update([
            'status' => 'Approved',
            'reviewed_by' => Auth::guard('wt')->id(),
            'reviewed_at' => now(),
        ]);

        SystemNotifier::notifyUser(
            $user,
            'Reset Password Diluluskan',
            'Permintaan reset password anda telah diluluskan. ICT akan uruskan penetapan kata laluan baharu untuk akaun anda.',
            'approved'
        );

        UserActivityLog::create([
            'user_id' => Auth::guard('wt')->id(),
            'username' => Auth::guard('wt')->user()->username,
            'event_type' => 'action',
            'event_action' => 'approve_request',
            'event_details' => 'Approved forgot password request for user #' . $user->user_id . ' - ' . $user->username . '. Password will be handled manually by ICT.',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return back()->with('success', 'Password reset request approved. ICT can now reset the password.');
    }

    public function rejectPasswordReset(Request $request, PasswordResetRequest $passwordResetRequest)
    {
        if ($passwordResetRequest->status !== 'Pending') {
            return back()->with('error', 'This password reset request has already been processed.');
        }

        $passwordResetRequest->update([
            'status' => 'Rejected',
            'reviewed_by' => Auth::guard('wt')->id(),
            'reviewed_at' => now(),
        ]);

        $user = $passwordResetRequest->user ?: User::where('staff_no', $passwordResetRequest->staff_id)->first();
        if ($user) {
            SystemNotifier::notifyUser(
                $user,
                'Reset Password Ditolak',
                'Permintaan reset password anda telah ditolak oleh ICT.',
                'rejected'
            );
        }

        UserActivityLog::create([
            'user_id' => Auth::guard('wt')->id(),
            'username' => Auth::guard('wt')->user()->username,
            'event_type' => 'action',
            'event_action' => 'reject_request',
            'event_details' => 'Rejected forgot password request for staff ID ' . $passwordResetRequest->staff_id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return back()->with('success', 'Password reset request rejected.');
    }
}
