<?php

namespace App\Http\Middleware\WT;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('wt')->check()) {
            $fallbackUserId = Auth::guard('web')->id() ?: $request->session()->get('_sso_user_id');
            $fallbackUser = $fallbackUserId
                ? \App\Models\WT\User::where('id', $fallbackUserId)
                    ->whereNotNull('wt_role')
                    ->first()
                : null;

            if ($fallbackUser && (! isset($fallbackUser->is_active) || $fallbackUser->is_active)) {
                Auth::guard('wt')->login($fallbackUser);

                return $next($request);
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('wt.login');
        }
        return $next($request);
    }
}
