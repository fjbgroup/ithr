<?php

namespace App\Http\Controllers\WT;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\WT\PasswordResetRequest;
use App\Models\WT\User;
use App\Models\WT\UserActivityLog;
use App\Services\SsoService;
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

        $user = User::whereRaw('TRIM(staff_no) = ?', [trim($credentials['staff_no'])])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            if (!$user->is_active) {
                return back()
                    ->with('error', 'Your account is inactive. Please contact ICT.')
                    ->onlyInput('staff_no');
            }

            if ($user->wt_role === null) {
                return back()
                    ->with('wt_access_denied', true)
                    ->with('error', "You don't have access to this system.")
                    ->onlyInput('staff_no');
            }

            Auth::guard('wt')->login($user);
            $request->session()->regenerate();
            SsoService::markAuthenticated($user->id);

            UserActivityLog::create([
                'user_id'     => $user->user_id,
                'username'    => $user->username,
                'event_type'  => 'login',
                'event_action'=> 'login_success',
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
                'created_at'  => now(),
            ]);

            if ($user->wt_role === 'user') {
                return redirect()->route('wt.user.dashboard');
            }

            if ($user->wt_role === 'admin') {
                return redirect()->route('wt.admin.walkies.myInventory');
            }

            return redirect()->route('wt.admin.dashboard');
        }

        UserActivityLog::create([
            'user_id'      => $user ? $user->user_id : 0,
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

        SsoService::clearAuthentication();
        Auth::guard('wt')->logout();
        Auth::guard('web')->logout();
        Auth::guard('it')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'requester_name' => 'required|string|max:255',
            'staff_id' => ['required', 'string', Rule::exists(User::class, 'staff_no')],
            'justification' => 'required|string|max:2000',
        ]);

        $user = User::where('staff_no', $request->staff_id)->first();

        if (! $user) {
            return back()->with('error', 'Staff No. not found.');
        }

        $hasPendingRequest = PasswordResetRequest::where('staff_id', $request->staff_id)
            ->where('status', 'Pending')
            ->exists();

        if ($hasPendingRequest) {
            return back()->with('error', 'A password reset request is already pending ICT approval.');
        }

        PasswordResetRequest::create([
            'user_id' => $user->user_id,
            'staff_id' => $request->staff_id,
            'requester_name' => trim((string) $request->requester_name),
            'requested_password' => '',
            'justification' => trim((string) $request->justification),
            'status' => 'Pending',
            'requested_at' => now(),
        ]);

        $itUsers = User::where('wt_role', 'admin_it')->get();
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
            'user_id'      => $user->user_id,
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
