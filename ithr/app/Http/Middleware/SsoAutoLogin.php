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
            SsoService::attemptAutoLogin($guard);
        }

        return $next($request);
    }
}
