<?php

namespace App\Http\Controllers\WT;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserDashboardController extends Controller
{
    public function index()
    {
        return view('wt.user.dashboard');
    }

    public function profile()
    {
        return view('wt.user.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = auth('wt')->user();
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'phone_no' => 'nullable|string|max:50',
        ]);

        $validated['full_name'] = Str::upper(trim($validated['full_name']));
        $validated['department'] = Str::upper(trim($validated['department']));
        $validated['position'] = Str::upper(trim($validated['position']));
        $validated['phone_no'] = trim((string) ($validated['phone_no'] ?? '')) ?: null;

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function policies()
    {
        return view('wt.user.policies');
    }
}


