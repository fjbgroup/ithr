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
            && ($request->user()->isCeo() || $request->user()->isDeactivatedStaff())
            && !in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'])
            && !$request->routeIs(...$this->allowed)
        ) {
            $msg = $request->user()->isCeo() ? 'You have read-only access.' : 'Your account is deactivated. You have read-only access to your records.';
            return redirect()->back()->with('error', $msg);
        }

        return $next($request);
    }
}
