<?php

namespace App\Http\Middleware\IT;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckMustChangePassword
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('it')->check() && Auth::guard('it')->user()->must_change_password) {
            if (!$request->routeIs('it.password.change') && !$request->routeIs('it.password.change.update') && !$request->routeIs('it.logout')) {
                return redirect()->route('it.password.change');
            }
        }
        return $next($request);
    }
}
