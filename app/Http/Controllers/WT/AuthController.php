<?php

namespace App\Http\Controllers\WT;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\WT\PasswordResetRequest;
use App\Models\WT\User;
use App\Models\WT\UserActivityLog;
use App\Services\SystemNotifier;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('wt.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'staff_no' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('staff_id', $credentials['staff_no'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            if ($user->role !== 'admin_it') {
                UserActivityLog::create([
                    'user_id'      => $user->id,
                    'username'     => $user->username,
                    'event_type'   => 'login',
                    'event_action' => 'login_blocked_unsupported_role',
                    'event_details'=> 'Login blocked because this account role is no longer allowed in the system.',
                    'ip_address'   => $request->ip(),
                    'user_agent'   => $request->userAgent(),
                    'created_at'   => now(),
                ]);

                return back()
                    ->with('error', 'Access denied. Only ICT Admin accounts may log in here.')
                    ->onlyInput('staff_no');
            }

            Auth::guard('wt')->login($user);
            $request->session()->regenerate();

            UserActivityLog::create([
                'user_id'    => $user->id,
                'username'   => $user->username,
                'event_type' => 'login',
                'event_action' => 'login_success',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);

            if ($user->role === 'admin_it') {
                return redirect()->route('wt.admin.dashboard');
            } elseif ($user->role === 'admin') {
                return redirect()->route('wt.admin.requests.create.shared');
            }

            return redirect()->route('wt.login');
        }

        UserActivityLog::create([
            'user_id'      => $user ? $user->id : 0,
            'username'     => $user ? $user->username : $credentials['staff_no'],
            'event_type'   => 'login',
            'event_action' => $user ? 'login_failed_wrong_password' : 'login_failed_user_not_found',
            'ip_address'   => $request->ip(),
            'user_agent'   => $request->userAgent(),
            'created_at'   => now(),
        ]);

        return back()->with('error', $user ? 'Wrong password!' : 'Staff No. not found!')->onlyInput('staff_no');
    }

    public function logout(Request $request)
    {
        if (Auth::guard('wt')->check()) {
            UserActivityLog::create([
                'user_id'      => Auth::guard('wt')->id(),
                'username'     => Auth::guard('wt')->user()->username,
                'event_type'   => 'logout',
                'event_action' => 'user_logout',
                'event_details'=> json_encode(['msg' => 'User logged out']),
                'ip_address'   => $request->ip(),
                'user_agent'   => $request->userAgent(),
                'created_at'   => now(),
            ]);
        }

        Auth::guard('wt')->logout();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'requester_name' => 'required|string|max:255',
            'staff_id' => ['required', 'string', Rule::exists(User::class, 'staff_id')],
            'justification' => 'required|string|max:2000',
        ]);

        $user = User::where('staff_id', $request->staff_id)->first();

        if (! $user || ! in_array($user->role, ['admin', 'admin_it'], true)) {
            return back()->with('error', 'Password reset is only available for Executive and ICT accounts.');
        }

        $hasPendingRequest = PasswordResetRequest::where('staff_id', $request->staff_id)
            ->where('status', 'Pending')
            ->exists();

        if ($hasPendingRequest) {
            return back()->with('error', 'A password reset request is already pending ICT approval.');
        }

        PasswordResetRequest::create([
            'user_id' => $user->id,
            'staff_id' => $request->staff_id,
            'requester_name' => trim((string) $request->requester_name),
            'requested_password' => '',
            'justification' => trim((string) $request->justification),
            'status' => 'Pending',
            'requested_at' => now(),
        ]);

        $itUsers = User::where('role', 'admin_it')->get();
        SystemNotifier::notifyUsers(
            $itUsers,
            'Reset Password Request Baru',
            "Permintaan reset password untuk {$user->username} menunggu semakan ICT.",
            'request_submitted'
        );
        SystemNotifier::notifyUser(
            $user,
            'Permintaan Reset Password Dihantar',
            'Permintaan reset password anda telah dihantar dan sedang menunggu semakan ICT.',
            'request_sent'
        );

        UserActivityLog::create([
            'user_id'      => $user->id,
            'username'     => $user->username,
            'event_type'   => 'action',
            'event_action' => 'reset_requested',
            'event_details'=> 'Password reset request submitted by ' . trim((string) $request->requester_name) . ' and waiting for ICT approval for ' . $user->username,
            'ip_address'   => $request->ip(),
            'user_agent'   => $request->userAgent(),
            'created_at'   => now(),
        ]);

        return redirect()->route('wt.login')->with('success', 'Password reset request submitted. ICT will review your request and handle the password reset.');
    }
}


