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
