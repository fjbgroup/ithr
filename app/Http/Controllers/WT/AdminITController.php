<?php

namespace App\Http\Controllers\WT;

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
            'total' => User::whereIn('role', ['admin_it', 'admin'])->count(),
            'admin_it' => User::where('role', 'admin_it')->count(),
            'admin' => User::where('role', 'admin')->count(),
        ];

        $users = User::query()
            ->select('users.*')
            ->selectSub(
                AccessRequest::selectRaw('COUNT(*)')
                    ->whereColumn('access_requests.user_id', 'users.user_id'),
                'request_count'
            )
            ->selectSub(
                Handover::selectRaw('COUNT(*)')
                    ->whereColumn('walkie_talkie_handovers.user_id', 'users.user_id'),
                'handover_count'
            )
            ->selectSub(
                UserActivityLog::selectRaw('MAX(created_at)')
                    ->whereColumn('user_activity_logs.user_id', 'users.user_id'),
                'last_activity_at'
            )
            ->orderByRaw("
                CASE
                    WHEN role = 'admin_it' THEN 1
                    ELSE 2
                END
            ")
            ->orderByDesc('user_id')
            ->get();

        $pendingPasswordResetRequests = PasswordResetRequest::with('user')
            ->where('status', 'Pending')
            ->orderByDesc('requested_at')
            ->get();

        return view('wt.admin.adminit.adminit', [
            'pageTitle' => 'Management ICT',
            'roleCounts' => $roleCounts,
            'users' => $users,
            'pendingPasswordResetRequests' => $pendingPasswordResetRequests,
        ]);
    }

    public function users()
    {
        return $this->index();
    }

    public function storeManager(Request $request)
    {
        $validated = $request->validate([
            'staff_id' => ['required', 'string', 'max:50', Rule::unique(User::class, 'staff_id')],
            'username' => ['required', 'string', 'max:50', Rule::unique(User::class, 'username')],
            'full_name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'staff_id' => trim($validated['staff_id']),
            'username' => trim($validated['username']),
            'full_name' => Str::upper(trim($validated['full_name'])),
            'department' => Str::upper(trim($validated['department'])),
            'position' => Str::upper(trim($validated['position'])),
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
            'created_at' => now(),
        ]);

        UserActivityLog::create([
            'user_id' => Auth::guard('wt')->id(),
            'username' => Auth::guard('wt')->user()->username,
            'event_type' => 'user_management',
            'event_action' => 'create_executive_account',
            'event_details' => 'Created executive account #' . $user->user_id . ' - ' . $user->username,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()
            ->route('wt.admin.users.index')
            ->with('success', 'Executive account created successfully.');
    }


    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'staff_id' => ['required', 'string', 'max:50', Rule::unique(User::class, 'staff_id')->ignore($user->user_id)],
            'username' => ['required', 'string', 'max:50', Rule::unique(User::class, 'username')->ignore($user->user_id)],
            'full_name' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'role' => 'required|in:admin,admin_it',
        ]);

        $original = [
            'staff_id' => $user->staff_id,
            'username' => $user->username,
            'full_name' => $user->full_name,
            'department' => $user->department,
            'position' => $user->position,
            'role' => $user->role,
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
            return back()->with('error', 'You cannot delete your own account.');
        }

        $deletedUserLabel = $user->username . ' (' . ($user->staff_id ?: '-') . ')';
        $deletedUserId = $user->user_id;

        $user->delete();

        UserActivityLog::create([
            'user_id' => Auth::guard('wt')->id(),
            'username' => Auth::guard('wt')->user()->username,
            'event_type' => 'user_management',
            'event_action' => 'delete',
            'event_details' => 'Deleted user #' . $deletedUserId . ' - ' . $deletedUserLabel,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return back()->with('success', 'User deleted successfully.');
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

        $user = $passwordResetRequest->user ?: User::where('staff_id', $passwordResetRequest->staff_id)->first();
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

        $user = $passwordResetRequest->user ?: User::where('staff_id', $passwordResetRequest->staff_id)->first();
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



