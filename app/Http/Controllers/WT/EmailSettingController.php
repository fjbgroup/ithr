<?php

namespace App\Http\Controllers\WT;

use App\Http\Controllers\Controller;
use App\Models\IT\EmailSetting;
use App\Models\WT\User;
use App\Services\SystemNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailSettingController extends Controller
{
    public function index()
    {
        $authUser   = Auth::guard('wt')->user();
        $settings   = EmailSetting::all_settings();
        $configured = !empty($settings['smtp_host']) && !empty($settings['smtp_user']) && !empty($settings['smtp_pass']);

        if (!$authUser->isAdmin()) {
            return view('wt.email-settings.staff', compact('configured', 'authUser'));
        }

        $admins = User::where('role', 'admin')->where('is_active', 1)->orderBy('name')->get(['name', 'email', 'role']);
        return view('wt.email-settings.index', compact('settings', 'admins', 'configured'));
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

        $html = '<div style="font-family:Arial,sans-serif;max-width:480px;margin:32px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08)"><div style="background:#142b47;padding:22px 28px"><div style="color:#fff;font-size:17px;font-weight:700">Email is working!</div></div><div style="padding:24px 28px;color:#475569;font-size:14px;line-height:1.7">Your SMTP settings are configured correctly. Admins will now receive email notifications.</div></div>';
        $sent = SystemNotifier::sendEmail($to, $to, 'Walkie Talkie System - Test Email', $html);
        return back()->with($sent ? 'success' : 'error', $sent ? 'Test email sent to ' . $to . '. Check your inbox!' : 'Could not send email. Check your SMTP settings and try again.');
    }
}
