<?php

namespace App\Http\Controllers\IT;

use App\Services\IT\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        return view('it.profile.index', ['user' => Auth::guard('it')->user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::guard('it')->user();
        $request->validate([
            'full_name'  => 'required|string|max:100',
            'email'      => 'nullable|email|max:100',
            'department' => 'nullable|string|max:100',
            'dept_name'  => 'nullable|string|max:100',
            'avatar'     => 'nullable|image|max:2048',
        ]);

        $data = [
            'full_name'  => $request->full_name,
            'email'      => $request->email,
            'department' => $request->department,
            'dept_name'  => $request->dept_name,
        ];

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        $user->update($data);
        ActivityLogService::log('UPDATE', 'user', $user->id, 'Updated profile');
        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::guard('it')->user();

        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        $user->update(['password' => Hash::make($request->new_password)]);
        ActivityLogService::log('UPDATE', 'user', $user->id, 'Changed password');
        return back()->with('success', 'Password changed successfully.');
    }

    public function updateSignature(Request $request)
    {
        $user = Auth::guard('it')->user();

        if ($request->hasFile('signature_file')) {
            $request->validate(['signature_file' => 'required|image|max:2048']);

            if ($user->signature_img && Storage::disk('public')->exists($user->signature_img)) {
                Storage::disk('public')->delete($user->signature_img);
            }

            $path = $request->file('signature_file')->store('signatures', 'public');
            $user->update(['signature_img' => $path]);
            ActivityLogService::log('UPDATE', 'user', $user->id, 'Updated signature');
            return back()->with('success', 'Signature saved successfully.');
        }

        if ($request->filled('sig_canvas_data')) {
            $raw = $request->sig_canvas_data;

            if (!preg_match('/^data:image\/(png|jpeg|gif|webp);base64,/', $raw, $m)) {
                return back()->with('error', 'Invalid signature format.');
            }

            $imgData = base64_decode(substr($raw, strpos($raw, ',') + 1));
            if ($imgData === false || strlen($imgData) > 2 * 1024 * 1024) {
                return back()->with('error', 'Invalid or oversized signature data.');
            }

            $ext      = ($m[1] === 'jpeg') ? 'jpg' : $m[1];
            $filename = 'sig_' . $user->id . '_' . time() . '.' . $ext;

            Storage::disk('public')->makeDirectory('signatures');

            if ($user->signature_img && Storage::disk('public')->exists($user->signature_img)) {
                Storage::disk('public')->delete($user->signature_img);
            }

            Storage::disk('public')->put('signatures/' . $filename, $imgData);
            $user->update(['signature_img' => 'signatures/' . $filename]);
            ActivityLogService::log('UPDATE', 'user', $user->id, 'Updated signature');
            return back()->with('success', 'Signature saved successfully.');
        }

        return back()->with('error', 'No signature provided.');
    }

    public function clearSignature()
    {
        $user = Auth::guard('it')->user();

        if ($user->signature_img && Storage::disk('public')->exists($user->signature_img)) {
            Storage::disk('public')->delete($user->signature_img);
        }

        $user->update(['signature_img' => null]);
        ActivityLogService::log('UPDATE', 'user', $user->id, 'Cleared signature');
        return back()->with('success', 'Signature cleared.');
    }

    public function serveSignature()
    {
        $user = Auth::guard('it')->user();

        if (!$user->signature_img || !Storage::disk('public')->exists($user->signature_img)) {
            abort(404);
        }

        return Storage::disk('public')->response($user->signature_img);
    }
}

