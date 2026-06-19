<?php

namespace App\Http\Middleware\IT;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSessionTimeout
{
    protected int $timeout = 1800;

    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('it')->check()) {
            $lastActivity = session('it_last_activity');
            if ($lastActivity && (time() - $lastActivity) > $this->timeout) {
                Auth::guard('it')->logout();
                $request->session()->forget('it_last_activity');
                return redirect()->route('it.login')->with('timeout', 1);
            }
            session(['it_last_activity' => time()]);
        }
        return $next($request);
    }
}
