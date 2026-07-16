<?php

namespace App\Services;

use App\Models\WT\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SystemNotifier
{
    /**
     * Send a notification to a single WT user.
     */
    public static function notifyUser(User $user, string $title, string $message, string $category = 'general'): void
    {
        \Illuminate\Support\Facades\DB::table('wt_notifications')
            ->insert([
                'id'              => (string) Str::uuid(),
                'type'            => 'App\Notifications\WtSystemNotification',
                'notifiable_type' => User::class,
                'notifiable_id'   => $user->user_id,
                'data'            => json_encode([
                    'title'    => $title,
                    'message'  => $message,
                    'category' => $category,
                ]),
                'read_at'    => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        // Send email notification
        if (!empty($user->email)) {
            static::sendEmail($user->email, $user->full_name ?? $user->user_id, $title, $message);
        }
    }

    /**
     * Send a notification to multiple WT users.
     *
     * @param User[]|Collection $users
     */
    public static function notifyUsers($users, string $title, string $message, string $category = 'general'): void
    {
        foreach ($users as $user) {
            static::notifyUser($user, $title, $message, $category);
        }
    }

    /**
     * Send email using Laravel's Mail facade.
     */
    public static function sendEmail(string $toEmail, string $toName, string $title, string $message): bool
    {
        $htmlBody = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body style="margin:0;padding:0;background:#f1f5f9;font-family:\'Inter\',Arial,sans-serif">
<div style="max-width:520px;margin:36px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08)">
  <div style="background:#142b47;padding:22px 28px;display:flex;align-items:center;gap:12px">
    <div style="width:36px;height:36px;border-radius:9px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0">&#128225;</div>
    <div style="color:#fff;font-size:15px;font-weight:700">' . htmlspecialchars($title) . '</div>
  </div>
  <div style="padding:28px 28px 24px">
    <p style="margin:0;font-size:14px;color:#334155;line-height:1.7">' . htmlspecialchars($message) . '</p>
  </div>
  <div style="padding:14px 28px;background:#f8fafc;border-top:1px solid #e2e8f0;font-size:11px;color:#94a3b8">
    This is an automated notification from the FJB Walkie Talkie System. Please do not reply to this email.
  </div>
</div>
</body></html>';

        try {
            Mail::html($htmlBody, function ($mail) use ($toEmail, $toName, $title) {
                $mail->to($toEmail, $toName)
                     ->subject($title);
            });
            return true;
        } catch (\Throwable $e) {
            Log::error('Walkie Talkie Email send failed: ' . $e->getMessage());
            return false;
        }
    }
}
