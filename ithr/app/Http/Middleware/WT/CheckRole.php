<?php

namespace App\Http\Middleware\WT;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::guard('wt')->check()) {
            return redirect()->route('wt.login');
        }

        $actualRole = Auth::guard('wt')->user()->role;
        $userRole = $actualRole;

        if ($actualRole === 'admin_it' && $request->session()->has('view_mode')) {
            $viewMode = $request->session()->get('view_mode', $actualRole);
            $userRole = in_array($viewMode, ['admin_it', 'admin'], true) ? $viewMode : $actualRole;
        }

        if ($actualRole === 'admin_it' && $userRole === 'admin_it') {
            return $next($request);
        }

        if (in_array($userRole, $roles, true)) {
            return $next($request);
        }

        return match($userRole) {
            'admin_it' => redirect()->route('wt.admin.dashboard'),
            'admin' => redirect()->route('wt.admin.requests.index'),
            default => redirect()->route('wt.login'),
        };
    }
}
