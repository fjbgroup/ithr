<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')->group(base_path('routes/wt.php'));
            Route::middleware('web')->group(base_path('routes/it.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role'          => \App\Http\Middleware\RoleMiddleware::class,
            'readonly.ceo'  => \App\Http\Middleware\ReadOnlyCeo::class,
            // Walkie Talkie guards
            'wt.auth'       => \App\Http\Middleware\WT\Authenticate::class,
            'wt.guest'      => \App\Http\Middleware\WT\RedirectIfAuthenticated::class,
            'wt.role'       => \App\Http\Middleware\WT\CheckRole::class,
            // IT guards
            'it.auth'       => \App\Http\Middleware\IT\Authenticate::class,
            'it.guest'      => \App\Http\Middleware\IT\RedirectIfAuthenticated::class,
            'it.role'       => \App\Http\Middleware\IT\RoleMiddleware::class,
            'it.active'     => \App\Http\Middleware\IT\CheckActiveUser::class,
            'it.session.timeout' => \App\Http\Middleware\IT\CheckSessionTimeout::class,
            'it.must.change.pass' => \App\Http\Middleware\IT\CheckMustChangePassword::class,
        ]);
        $middleware->appendToGroup('web', \App\Http\Middleware\ReadOnlyCeo::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
