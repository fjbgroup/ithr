<?php

namespace App\Http\Middleware\IT;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckActiveUser
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('it')->check() && !Auth::guard('it')->user()->is_active) {
            Auth::guard('it')->logout();
            return redirect()->route('it.login')->with('error', 'Your account has been deactivated.');
        }
        return $next($request);
    }
}
