<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\IT\User as ITUser;
use App\Models\WT\User as WTUser;

class SsoService
{
    public static function markAuthenticated(int $userId): void
    {
        session(['_sso_user_id' => $userId]);
    }

    public static function clearAuthentication(): void
    {
        session()->forget('_sso_user_id');
    }

    public static function attemptAutoLogin(string $guard): bool
    {
        $userId = session('_sso_user_id');
        if (!$userId) {
            return false;
        }

        // Use the guard's own model class so guard-specific methods are available
        // on the authenticated user instance within the same request.
        $model = match ($guard) {
            'it'    => ITUser::find($userId),
            'wt'    => WTUser::find($userId),
            default => User::find($userId),
        };

        if (!$model || !$model->is_active) {
            return false;
        }

        if ($guard === 'wt' && $model->wt_role === null) {
            return false;
        }

        if ($guard === 'it' && $model->it_role === null) {
            return false;
        }

        Auth::guard($guard)->login($model);

        return true;
    }
}
