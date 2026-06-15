<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReadOnlyCeo
{
    /**
     * Non-GET routes a read-only CEO is still allowed to hit (session/UX actions
     * that don't modify business data).
     */
    private array $allowed = [
        'logout',
        'notifications.mark-read',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('wt', 'wt/*', 'it', 'it/*')) {
            return $next($request);
        }

        if ($request->user()
            && $request->user()->isCeo()
            && !in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'])
            && !$request->routeIs(...$this->allowed)
        ) {
            return redirect()->back()->with('error', 'You have read-only access.');
        }

        return $next($request);
    }
}
