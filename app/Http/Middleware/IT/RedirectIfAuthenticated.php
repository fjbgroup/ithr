<?php

namespace App\Http\Middleware\IT;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('it')->check()) {
            return redirect()->route('it.dashboard');
        }
        return $next($request);
    }
}
