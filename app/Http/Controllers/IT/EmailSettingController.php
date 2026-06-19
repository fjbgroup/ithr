<?php

namespace App\Http\Controllers\IT;

use App\Models\IT\EmailSetting;
use App\Models\IT\User;
use App\Services\IT\ActivityLogService;
use App\Services\IT\NotificationService;
use Illuminate\Http\Request;

class EmailSettingController extends Controller
{
    public function index()
    {
        $settings   = EmailSetting::all_settings();
        $admins     = User::whereIn('role', ['admin', 'finance_admin'])->where('is_active', 1)->orderByRaw('role, name')->get(['name', 'email', 'role']);
        $configured = !empty($settings['smtp_host']) && !empty($settings['smtp_user']) && !empty($settings['smtp_pass']);
        return view('it.email-settings.index', compact('settings', 'admins', 'configured'));
    }

    public function update(Request $request)
    {
        $fields = ['smtp_host', 'smtp_port', 'smtp_encryption', 'smtp_user', 'smtp_pass', 'smtp_from', 'smtp_from_name'];
        foreach ($fields as $key) {
            $value = trim($request->input($key, ''));
            if ($key === 'smtp_pass' && $value === '') continue;
            EmailSetting::updateOrCreate(['setting_key' => $key], ['setting_value' => $value]);
        }
        ActivityLogService::log('UPDATE', 'system', 0, 'Updated email SMTP settings');
        return back()->with('success', 'Email settings saved.');
    }

    public function testEmail(Request $request)
    {
        $to = trim($request->test_to ?? '');
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) return back()->with('error', 'Invalid email address.');

        $html = '<div style="font-family:Arial,sans-serif;max-width:480px;margin:32px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08)"><div style="background:#142b47;padding:22px 28px"><div style="color:#fff;font-size:17px;font-weight:700">Email is working!</div></div><div style="padding:24px 28px;color:#475569;font-size:14px;line-height:1.7">Your SMTP settings are configured correctly. Admins will now receive email notifications when users submit requests.</div></div>';
        $sent = NotificationService::sendEmail($to, $to, 'FJB Inventory - Test Email', $html);
        return back()->with($sent ? 'success' : 'error', $sent ? 'Test email sent to ' . $to . '. Check your inbox!' : 'Could not send email. Check your SMTP settings and try again.');
    }
}
