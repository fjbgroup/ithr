<?php

namespace App\Http\Controllers\IT\Auth;

use App\Http\Controllers\IT\Controller;
use App\Services\IT\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    public function showForm()
    {
        return view('it.auth.change-password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'new_password'     => 'required|string|min:6',
            'confirm_password' => 'required|string|same:new_password',
        ]);

        if ($request->new_password === 'password') {
            return back()->withErrors(['new_password' => 'You cannot use the default password. Please choose a new unique password.']);
        }

        $user = Auth::guard('it')->user();
        $user->update([
            'password'             => Hash::make($request->new_password),
            'must_change_password' => false,
        ]);

        ActivityLogService::log('PASSWORD_RESET', 'user', $user->id, 'User changed password after default reset');

        return back()->with('success', 'Password updated successfully! Redirecting to dashboardâ€¦');
    }
}

