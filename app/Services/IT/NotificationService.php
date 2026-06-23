<?php

namespace App\Services\IT;

use App\Models\IT\Notification;
use App\Models\IT\User;
use App\Models\IT\EmailSetting;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Mailer as SymfonyMailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

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
        $admins = User::whereIn('it_role', ['admin_it', 'admin'])->where('is_active', true)->get();
        foreach ($admins as $admin) {
            static::create($admin->id, $type, $title, $message, $link);
        }
    }

    public static function notifyUser(int $userId, string $type, string $title, string $message, string $link = ''): void
    {
        static::create($userId, $type, $title, $message, $link);
    }

    public static function notifyUserWithEmail(int $userId, string $type, string $title, string $message, string $link = ''): void
    {
        static::create($userId, $type, $title, $message, $link);

        $user = User::find($userId);
        if (!$user || empty($user->email)) return;

        static::sendEmail($user->email, $user->full_name, $title, static::buildEmailBody($title, $message, $link));
    }

    private static function buildEmailBody(string $title, string $message, string $link = ''): string
    {
        $linkHtml = $link
            ? '<p style="margin:24px 0 0"><a href="' . htmlspecialchars($link) . '" style="display:inline-block;padding:11px 24px;background:#142b47;color:#fff;border-radius:8px;text-decoration:none;font-size:13px;font-weight:700;letter-spacing:.01em">View Details &rarr;</a></p>'
            : '';

        return '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body style="margin:0;padding:0;background:#f1f5f9;font-family:\'DM Sans\',Arial,sans-serif">
<div style="max-width:520px;margin:36px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08)">
  <div style="background:#142b47;padding:22px 28px;display:flex;align-items:center;gap:12px">
    <div style="width:36px;height:36px;border-radius:9px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0">&#128274;</div>
    <div style="color:#fff;font-size:15px;font-weight:700">' . htmlspecialchars($title) . '</div>
  </div>
  <div style="padding:28px 28px 24px">
    <p style="margin:0;font-size:14px;color:#334155;line-height:1.7">' . htmlspecialchars($message) . '</p>
    ' . $linkHtml . '
  </div>
  <div style="padding:14px 28px;background:#f8fafc;border-top:1px solid #e2e8f0;font-size:11px;color:#94a3b8">
    This is an automated notification from the FJB IT Inventory System. Please do not reply to this email.
  </div>
</div>
</body></html>';
    }

    public static function sendEmail(string $toEmail, string $toName, string $subject, string $htmlBody): bool
    {
        try {
            $cfg = EmailSetting::all_settings();
            if (empty($cfg['smtp_host'])) return false;

            $host     = $cfg['smtp_host'];
            $port     = (int)($cfg['smtp_port'] ?? 587);
            $enc      = $cfg['smtp_encryption'] ?? 'tls';
            $user     = $cfg['smtp_user'] ?? '';
            $pass     = $cfg['smtp_pass'] ?? '';
            $from     = !empty($cfg['smtp_from']) ? $cfg['smtp_from'] : $user;
            $fromName = $cfg['smtp_from_name'] ?? 'FJB Inventory';

            // true = SSL-on-connect (port 465); false = STARTTLS negotiation (port 587)
            $useSsl = ($enc === 'ssl' || $port === 465);

            $transport = new EsmtpTransport($host, $port, $useSsl);
            $transport->setUsername($user);
            $transport->setPassword($pass);

            $mailer = new SymfonyMailer($transport);

            $email = (new Email())
                ->from(new Address($from, $fromName))
                ->to(new Address($toEmail, $toName))
                ->subject($subject)
                ->html($htmlBody);

            $mailer->send($email);
            return true;
        } catch (\Throwable $e) {
            \Log::error('SMTP send failed: ' . $e->getMessage());
            return false;
        }
    }
}
