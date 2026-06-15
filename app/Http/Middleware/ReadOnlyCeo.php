<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReadOnlyCeo
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->isCeo() && !in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'])) {
            return redirect()->back()->with('error', 'You have read-only access.');
        }

        return $next($request);
    }
}
