<?php

namespace App\Http\Controllers\WT\Admin;

use App\Http\Controllers\WT\Controller;
use App\Models\WT\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class InterfaceSwitchController extends Controller
{
    public function switch(Request $request, $role)
    {
        // Only admin_it can switch roles
        if (Auth::guard('wt')->user()->wt_role !== 'admin_it') {
            return back()->with('error', 'Unauthorized access.');
        }

        // Validate allowed roles
        if (!in_array($role, ['admin_it', 'admin'], true)) {
            return back()->with('error', 'Invalid role selection.');
        }

        // Store the target view mode in session
        session(['view_mode' => $role]);

        // Redirect based on the chosen view mode
        return match($role) {
            'admin_it' => redirect()->route('wt.admin.dashboard'),
            'admin'    => redirect()->route('wt.admin.walkies.myInventory'),
            default    => redirect()->route('wt.admin.dashboard'),
        };
    }

    public function impersonateExecutive(Request $request)
    {
        if (Auth::guard('wt')->user()->wt_role !== 'admin_it') {
            return back()->with('error', 'Unauthorized access.');
        }

        $validated = $request->validate([
            'executive_user_id' => ['required', 'integer', Rule::exists(User::class, 'id')],
        ]);

        $executive = User::where('wt_role', 'admin')
            ->where('id', $validated['executive_user_id'])
            ->firstOrFail();

        $ictUserId = Auth::guard('wt')->id();

        $request->session()->put('impersonator_admin_it_id', $ictUserId);
        $request->session()->forget('view_mode');

        Auth::guard('wt')->login($executive);
        $request->session()->regenerate();
        $request->session()->put('impersonator_admin_it_id', $ictUserId);

        return redirect()
            ->route('wt.admin.walkies.myInventory')
            ->with('success', 'You are now accessing the selected Executive account.');
    }

    public function stopImpersonating(Request $request)
    {
        $ictUserId = $request->session()->get('impersonator_admin_it_id');

        if (! $ictUserId) {
            return redirect()->route('wt.admin.dashboard');
        }

        $ictUser = User::where('wt_role', 'admin_it')
            ->where('id', $ictUserId)
            ->firstOrFail();

        Auth::guard('wt')->login($ictUser);
        $request->session()->regenerate();
        $request->session()->forget('impersonator_admin_it_id');
        $request->session()->put('view_mode', 'admin_it');

        return redirect()
            ->route('wt.admin.dashboard')
            ->with('success', 'Returned to ICT account.');
    }
}


