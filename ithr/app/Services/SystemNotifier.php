<?php

namespace App\Services;

use App\Models\WT\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

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
}
