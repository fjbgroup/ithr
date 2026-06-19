<?php

namespace App\Http\Middleware\WT;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('wt')->check()) {
            $user = Auth::guard('wt')->user();
            if ($user->role === 'user') {
                return redirect()->route('wt.user.dashboard');
            }
            return redirect()->route('wt.admin.dashboard');
        }
        return $next($request);
    }
}
