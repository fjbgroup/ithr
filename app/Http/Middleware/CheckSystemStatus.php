<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\IT\EmailSetting;
use Illuminate\Support\Facades\Auth;

class CheckSystemStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $system 'it', 'wt', or 'lms'
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $system)
    {
        if (EmailSetting::systemEnabled($system)) {
            return $next($request);
        }

        // If system is disabled, check if user is an ADMIN IT.
        // We must check all relevant guards because this middleware can be applied anywhere.
        $isAdmin = false;

        // Check HR / main guard
        if (Auth::guard('web')->check() && Auth::guard('web')->user()->isAdminIT()) {
            $isAdmin = true;
        }
        // Check IT guard
        elseif (Auth::guard('it')->check() && Auth::guard('it')->user()->role === 'admin_it') {
            $isAdmin = true;
        }
        // Check WT guard
        elseif (Auth::guard('wt')->check() && Auth::guard('wt')->user()->role === 'admin_it') {
            $isAdmin = true;
        }

        if ($isAdmin) {
            // Logged-in IT admins can bypass maintenance mode
            return $next($request);
        }

        // If not an admin, block access and redirect to maintenance page
        return redirect()->route('system.maintenance', ['system' => $system]);
    }
}
