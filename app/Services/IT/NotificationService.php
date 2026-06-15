<?php

namespace App\Services\IT;

use App\Models\IT\Notification;
use App\Models\IT\User;
use App\Models\IT\EmailSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public static function create(int $userId, string $type, string $title, string $message, string $link = ''): void
    {
        Notification::create([
            'user_id' => $userId,
            'type'    => $type,
            'title'   => $title,
            'message' => $message,
            'link'    => $link,
        ]);
    }

    public static function notifyAdmins(string $type, string $title, string $message, string $link = ''): void
    {
        $admins = User::whereIn('role', ['admin_it', 'admin'])->where('is_active', true)->get();
        foreach ($admins as $admin) {
            static::create($admin->id, $type, $title, $message, $link);
        }
    }

    public static function notifyUser(int $userId, string $type, string $title, string $message, string $link = ''): void
    {
        static::create($userId, $type, $title, $message, $link);
    }

    public static function sendEmail(string $toEmail, string $toName, string $subject, string $htmlBody): bool
    {
        try {
            $cfg = EmailSetting::all_settings();
            if (empty($cfg['smtp_host'])) return false;

            Config::set('mail.mailers.smtp.host', $cfg['smtp_host']);
            Config::set('mail.mailers.smtp.port', $cfg['smtp_port'] ?? 587);
            Config::set('mail.mailers.smtp.encryption', $cfg['smtp_encryption'] ?? 'tls');
            Config::set('mail.mailers.smtp.username', $cfg['smtp_user'] ?? null);
            Config::set('mail.mailers.smtp.password', $cfg['smtp_pass'] ?? null);
            Config::set('mail.from.address', $cfg['smtp_from'] ?? '');
            Config::set('mail.from.name', $cfg['smtp_from_name'] ?? 'FJB Inventory');

            Mail::html($htmlBody, function ($m) use ($toEmail, $toName, $subject) {
                $m->to($toEmail, $toName)->subject($subject);
            });
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
