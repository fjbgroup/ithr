<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\IT\EmailSetting;
use App\Services\AuditLogger;
use App\Services\SsoService;
use PragmaRX\Google2FA\Google2FA;

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
        session()->forget(['fp_step', 'fp_user_id', 'fp_user_name', 'fp_email', 'fp_method']);
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

        // ── TOTP path: HR users ──────────────────────────────────────────────
        // Skip the authenticator entirely when 2FA is disabled system-wide;
        // HR users then fall through to the email OTP path below.
        if ($user->isHrUser() && EmailSetting::totpEnabled()) {
            if (!$user->hasTotpSetup()) {
                return back()->with('error',
                    'Your account requires Microsoft Authenticator for password reset. ' .
                    'Please log in and set it up under User Accounts → Account Security.'
                )->withInput();
            }

            session([
                'fp_user_id'   => $user->id,
                'fp_user_name' => $user->name,
                'fp_email'     => null,
                'fp_step'      => 'verify',
                'fp_method'    => 'totp',
            ]);

            return redirect()->route('password.request');
        }

        // ── Email OTP path: non-HR users ─────────────────────────────────────
        // If the global email master switch is OFF, no OTP email can be sent.
        // Block here so the user isn't told "OTP sent" for an email that never goes out.
        if (!EmailSetting::emailEnabled()) {
            return back()->with('error',
                'Password reset by email is temporarily unavailable. Please contact the administrator to reset your password.'
            )->withInput();
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
            'fp_method'    => 'email',
        ]);

        return redirect()->route('password.request');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $userId = session('fp_user_id');
        $method = session('fp_method', 'email');

        if (!$userId) {
            return redirect()->route('password.request');
        }

        // ── TOTP verification ────────────────────────────────────────────────
        if ($method === 'totp') {
            $user = User::find($userId);

            if (!$user || !$user->hasTotpSetup()) {
                return redirect()->route('password.request')
                    ->with('error', 'Invalid session. Please start again.');
            }

            $google2fa = new Google2FA();

            if (!$google2fa->verifyKey($user->totp_secret, $request->otp)) {
                return back()->with('error',
                    'Invalid authenticator code. Codes expire every 30 seconds — try again with the current code.'
                );
            }

            session(['fp_step' => 'reset']);
            return redirect()->route('password.request');
        }

        // ── Email OTP verification ───────────────────────────────────────────
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
        session()->forget(['fp_user_id', 'fp_user_name', 'fp_email', 'fp_method', 'fp_step']);

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

        // Verify credentials without logging in yet, so we can interpose the
        // Microsoft Authenticator (TOTP) challenge when the user has it enabled.
        if (!Auth::validate(['staff_no' => $credentials['staff_no'], 'password' => $credentials['password']])) {
            return back()->withErrors([
                'staff_no' => 'Invalid Staff ID or password. Please try again.',
            ])->onlyInput('staff_no');
        }

        // Microsoft Authenticator enabled → require a 6-digit code before login.
        // The global master switch can skip 2FA system-wide without clearing setups.
        if ($user->hasTotpSetup() && EmailSetting::totpEnabled()) {
            $request->session()->put('2fa.user_id', $user->id);
            $request->session()->put('2fa.remember', $request->filled('remember'));
            return redirect()->route('login.2fa');
        }

        return $this->completeLogin($request, $user, $request->filled('remember'));
    }

    /**
     * Show the Microsoft Authenticator challenge after correct credentials.
     */
    public function showTwoFactor(Request $request)
    {
        $userId = $request->session()->get('2fa.user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);
        if (!$user || !$user->hasTotpSetup()) {
            $request->session()->forget(['2fa.user_id', '2fa.remember']);
            return redirect()->route('login');
        }

        return view('auth.two-factor', ['userName' => $user->name]);
    }

    /**
     * Verify the Microsoft Authenticator code and complete login.
     */
    public function verifyTwoFactor(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $userId = $request->session()->get('2fa.user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);
        if (!$user || !$user->hasTotpSetup()) {
            $request->session()->forget(['2fa.user_id', '2fa.remember']);
            return redirect()->route('login')->with('error', 'Invalid session. Please log in again.');
        }

        if ($user && !$user->is_active) {
            $request->session()->forget(['2fa.user_id', '2fa.remember']);
            return redirect()->route('login')->withErrors([
                'staff_no' => 'Your account has been disabled. Please contact the administrator.',
            ]);
        }

        $google2fa = new Google2FA();

        if (!$google2fa->verifyKey($user->totp_secret, $request->otp)) {
            return back()->withErrors([
                'otp' => 'Invalid authenticator code. Codes expire every 30 seconds — try again with the current code.',
            ]);
        }

        $remember = (bool) $request->session()->get('2fa.remember', false);
        $request->session()->forget(['2fa.user_id', '2fa.remember']);

        return $this->completeLogin($request, $user, $remember);
    }

    /**
     * Log the user in and redirect to their intended destination.
     */
    protected function completeLogin(Request $request, User $user, bool $remember)
    {
        Auth::login($user, $remember);
        $request->session()->regenerate();
        SsoService::markAuthenticated(Auth::id());

        if ($request->session()->has('pending_booking')) {
            return redirect()->route('rooms.bookings.process-pending');
        }

        return redirect()->intended(route('dashboard'));
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
