<?php

namespace App\Http\Controllers\IT\Auth;

use App\Http\Controllers\IT\Controller;
use App\Models\IT\PasswordResetRequest;
use App\Models\IT\User;
use App\Services\IT\ActivityLogService;
use App\Services\SsoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class LoginController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('it')->check()) return redirect()->route('it.dashboard');
        return view('it.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $input = trim($request->username);
        $user = User::whereRaw('TRIM(staff_no) = ?', [$input])
            ->orWhere('email', $input)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['login' => 'Invalid username / staff ID or password.'])->withInput(['username' => $input]);
        }

        if (!$user->is_active) {
            return back()->withErrors(['login' => 'Your account is deactivated. Please contact admin.'])->withInput(['username' => $input]);
        }

        if ($user->it_role === null) {
            return back()
                ->with('it_access_denied', true)
                ->with('error', "You don't have access to this system.")
                ->withInput(['username' => $input]);
        }

        Auth::guard('it')->login($user);
        $request->session()->regenerate();
        session(['it_last_activity' => time()]);
        SsoService::markAuthenticated($user->id);

        try {
            $user->update(['last_login' => now()]);
            ActivityLogService::log('LOGIN', 'user', $user->id, 'User logged in', $user->id);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('IT login post-auth step failed: ' . $e->getMessage());
        }

        if ($user->must_change_password) {
            return redirect()->route('it.password.change');
        }

        return redirect()->route('it.dashboard');
    }

    public function signup(Request $request)
    {
        return back()->with('error', 'Self-registration is disabled. Please contact your IT administrator to create an account.');
    }
}
