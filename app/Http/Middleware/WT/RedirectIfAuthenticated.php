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

            if ($user->wt_role === null) {
                Auth::guard('wt')->logout();
                session()->flash('wt_access_denied', true);
                session()->flash('error', "You don't have access to this system.");
                return redirect()->route('wt.login');
            }

            if ($user->wt_role === 'user') {
                return redirect()->route('wt.user.dashboard');
            }
            return redirect()->route('wt.admin.dashboard');
        }
        return $next($request);
    }
}
