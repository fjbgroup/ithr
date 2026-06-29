<?php

namespace App\Http\Middleware\IT;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    // Route role params use short names; map to it_role column values
    private array $aliases = [
        'admin' => 'admin_it',
        'user'  => 'user',
    ];

    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if (!Auth::guard('it')->check()) {
            return redirect()->route('it.login');
        }

        $itRole = Auth::guard('it')->user()->it_role;

        if ($itRole === null) {
            Auth::guard('it')->logout();
            return redirect()->route('it.login')
                ->with('it_access_denied', true)
                ->with('error', "You don't have access to this system.");
        }

        foreach ($roles as $role) {
            $normalized = $this->aliases[$role] ?? $role;
            if ($itRole === $normalized || $itRole === $role) {
                return $next($request);
            }
        }

        return redirect()->route('it.dashboard')->with('error', 'Access denied.');
    }
}
