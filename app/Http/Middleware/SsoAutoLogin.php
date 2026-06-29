<?php

namespace App\Http\Middleware;

use App\Services\SsoService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SsoAutoLogin
{
    public function handle(Request $request, Closure $next)
    {
        if (! session('_sso_user_id')) {
            return $next($request);
        }

        $path = $request->path();

        if ($path === 'it' || str_starts_with($path, 'it/')) {
            $guard = 'it';
        } elseif ($path === 'wt' || str_starts_with($path, 'wt/')) {
            $guard = 'wt';
        } else {
            $guard = 'web';
        }

        if (! Auth::guard($guard)->check()) {
            $loggedIn = SsoService::attemptAutoLogin($guard);

            if (! $loggedIn && $guard === 'wt') {
                $userId = session('_sso_user_id');
                $wtUser = \App\Models\WT\User::find($userId);
                if ($wtUser && $wtUser->is_active && $wtUser->wt_role === null) {
                    session()->flash('wt_access_denied', true);
                    session()->flash('error', "You don't have access to this system.");
                }
            }

            if (! $loggedIn && $guard === 'it') {
                $userId = session('_sso_user_id');
                $itUser = \App\Models\IT\User::find($userId);
                if ($itUser && $itUser->is_active && $itUser->it_role === null) {
                    session()->flash('it_access_denied', true);
                    session()->flash('error', "You don't have access to this system.");
                }
            }
        }

        return $next($request);
    }
}
