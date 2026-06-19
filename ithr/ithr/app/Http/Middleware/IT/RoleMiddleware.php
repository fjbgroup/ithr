<?php

namespace App\Http\Middleware\IT;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    // fjb_unified uses admin_it/staff; FJB routes pass admin/user
    private array $aliases = [
        'admin' => 'admin_it',
        'user'  => 'staff',
    ];

    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if (!Auth::guard('it')->check()) {
            return redirect()->route('it.login');
        }

        $userRole = Auth::guard('it')->user()->role;

        foreach ($roles as $role) {
            $normalized = $this->aliases[$role] ?? $role;
            if ($userRole === $normalized || $userRole === $role) {
                return $next($request);
            }
        }

        return redirect()->route('it.dashboard')->with('error', 'Access denied.');
    }
}
