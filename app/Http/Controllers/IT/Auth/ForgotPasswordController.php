<?php

namespace App\Http\Controllers\IT\Auth;

use App\Http\Controllers\IT\Controller;
use App\Models\IT\PasswordResetRequest;
use App\Models\IT\User;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    public function showForm()
    {
        return view('it.auth.forgot-password');
    }

    public function submit(Request $request)
    {
        $request->validate([
            'fp_username' => 'required|string',
            'fp_staff_id' => 'required|string',
        ]);

        $username = trim($request->fp_username);
        $staffId  = trim($request->fp_staff_id);

        $user = User::where('username', $username)->where('is_active', true)->first();

        if ($user && strtolower(trim($user->staff_id ?? '')) === strtolower($staffId)) {
            $existing = PasswordResetRequest::where('user_id', $user->id)->where('status', 'pending')->first();
            if ($existing) {
                return back()->with('success', 'A reset request is already pending. Please wait for an admin to process it.');
            }

            PasswordResetRequest::create([
                'user_id'      => $user->id,
                'username'     => $username,
                'full_name'    => $user->full_name,
                'staff_id'     => $staffId,
                'requested_at' => now(),
            ]);
            return back()->with('success', 'Request submitted! An admin will reset your password shortly. You can then log in with the default password.');
        }

        return back()->withErrors(['fp_username' => 'Username or Staff ID not recognised. Please check your details.']);
    }
}
