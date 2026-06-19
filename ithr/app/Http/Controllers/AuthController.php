<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Services\AuditLogger;
use App\Services\SsoService;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showForgot()
    {
        return view('auth.forgot');
    }

    public function restartForgot()
    {
        session()->forget(['fp_step', 'fp_user_id', 'fp_user_name', 'fp_email']);
        return redirect()->route('password.request');
    }

    public function requestOtp(Request $request)
    {
        $request->validate([
            'staff_no' => 'required|string',
        ]);

        $user = User::where('staff_no', $request->staff_no)->where('is_active', 1)->first();

        if (!$user) {
            return back()->with('error', 'Staff ID not found. Please check and try again.')->withInput();
        }

        if (!$user->email) {
            return back()->with('error', 'No email address found for this Staff ID. Please contact HR to update your email.')->withInput();
        }

        // Invalidate any existing unused OTPs
        DB::table('password_resets')
            ->where('user_id', $user->id)
            ->where('used', 0)
            ->update(['used' => 1]);

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('password_resets')->insert([
            'user_id' => $user->id,
            'otp_code' => $otp,
            'expires_at' => Carbon::now()->addMinutes(15),
            'used' => 0,
            'created_at' => Carbon::now(),
        ]);

        // Send OTP via email
        try {
            $htmlContent = "<p>Hi <strong>" . e($user->name) . "</strong>,</p>
                            <p>Your one-time password (OTP) for resetting your account is:</p>
                            <p style='font-size:32px;font-weight:700;letter-spacing:.3em;font-family:monospace;color:#4f46e5;text-align:center;'>$otp</p>
                            <p>This OTP is valid for <strong>15 minutes</strong>. Do not share it with anyone.</p>
                            <hr style='border:none;border-top:1px solid #e5e7eb;margin:1.5rem 0;'>
                            <p style='font-size:.85em;color:#6b7280;'>If you did not request a password reset, please ignore this email.</p>";

            Mail::html($htmlContent, function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Password Reset OTP — ' . config('app.name'));
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send OTP email. Please try again later. Error: ' . $e->getMessage())->withInput();
        }

        $maskedEmail = substr($user->email, 0, 3) . str_repeat('*', max(0, strpos($user->email, '@') - 3)) . substr($user->email, strpos($user->email, '@'));

        session([
            'fp_user_id'   => $user->id,
            'fp_user_name' => $user->name,
            'fp_email'     => $maskedEmail,
            'fp_step'      => 'verify',
        ]);

        return redirect()->route('password.request');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $userId = session('fp_user_id');

        if (!$userId) {
            return redirect()->route('password.request');
        }

        $record = DB::table('password_resets')
            ->where('user_id', $userId)
            ->where('otp_code', $request->otp)
            ->where('used', 0)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$record) {
            return back()->with('error', 'Invalid or expired OTP. Please try again.');
        }

        // Mark OTP as used
        DB::table('password_resets')
            ->where('id', $record->id)
            ->update(['used' => 1]);

        session(['fp_step' => 'reset']);

        return redirect()->route('password.request');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        $userId = session('fp_user_id');
        $step = session('fp_step');

        if (!$userId || $step !== 'reset') {
            return redirect()->route('password.request');
        }

        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('password.request')->with('error', 'User not found.');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Clear all session reset data
        session()->forget(['fp_user_id', 'fp_user_name', 'fp_email', 'fp_step']);

        return redirect()->route('login')->with('status', 'Your password has been reset successfully. You can now login.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'staff_no' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $user = User::where('staff_no', $credentials['staff_no'])->first();

        if ($user && !$user->is_active) {
            return back()->withErrors([
                'staff_no' => 'Your account has been disabled. Please contact the administrator.',
            ]);
        }

        if (Auth::attempt(['staff_no' => $credentials['staff_no'], 'password' => $credentials['password']], $request->filled('remember'))) {
            $request->session()->regenerate();
            SsoService::markAuthenticated(Auth::id());

            if ($request->session()->has('pending_booking')) {
                return redirect()->route('rooms.bookings.process-pending');
            }

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'staff_no' => 'Invalid Staff ID or password. Please try again.',
        ])->onlyInput('staff_no');
    }

    public function logout(Request $request)
    {
        SsoService::clearAuthentication();
        Auth::guard('web')->logout();
        Auth::guard('it')->logout();
        Auth::guard('wt')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
