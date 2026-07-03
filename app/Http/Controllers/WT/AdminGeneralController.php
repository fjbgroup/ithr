<?php

namespace App\Http\Controllers\WT; // Semak ejaan ini

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminGeneralController extends Controller
{
    public function index()
    {
        return view('wt.admin.admingeneral.admin'); // Pastikan fail blade ini wujud
    }

    public function profile()
    {
        return view('wt.admin.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = auth('wt')->user();
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'phone_no' => 'nullable|string|max:50',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $validated['full_name'] = Str::upper(trim($validated['full_name']));
        $validated['department'] = Str::upper(trim($validated['department']));
        $validated['position'] = Str::upper(trim($validated['position']));
        $validated['phone_no'] = trim((string) ($validated['phone_no'] ?? '')) ?: null;

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }
}

