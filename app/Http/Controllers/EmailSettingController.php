<?php

namespace App\Http\Controllers;

use App\Models\IT\EmailSetting;
use App\Models\User;
use App\Services\SystemNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailSettingController extends Controller
{
    public function index()
    {
        $authUser   = Auth::user();
        $settings   = EmailSetting::all_settings();
        $configured = !empty($settings['smtp_host']) && !empty($settings['smtp_user']) && !empty($settings['smtp_pass']);

        if (!$authUser->isAdmin()) {
            return view('email-settings.staff', compact('configured', 'authUser'));
        }

        $admins = User::whereIn('role', ['admin_hr', 'admin_it'])->where('is_active', 1)->orderBy('name')->get(['name', 'email', 'role']);
        return view('email-settings.index', compact('settings', 'admins', 'configured'));
    }

    public function update(Request $request)
    {
        $fields = ['smtp_host', 'smtp_port', 'smtp_encryption', 'smtp_user', 'smtp_pass', 'smtp_from', 'smtp_from_name'];
        foreach ($fields as $key) {
            $value = trim($request->input($key, ''));
            if ($key === 'smtp_pass' && $value === '') continue;
            EmailSetting::updateOrCreate(['setting_key' => $key], ['setting_value' => $value]);
        }
        return back()->with('success', 'Email settings saved.');
    }

    public function testEmail(Request $request)
    {
        $to = trim($request->test_to ?? '');
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) return back()->with('error', 'Invalid email address.');

        $message = 'Your SMTP settings are configured correctly. Admins will now receive email notifications.';
        $sent = SystemNotifier::sendEmail($to, $to, 'HR System - Test Email', $message);
        return back()->with($sent ? 'success' : 'error', $sent ? 'Test email sent to ' . $to . '. Check your inbox!' : 'Could not send email. Check your SMTP settings and try again.');
    }
}
