<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Blade;
use App\Services\AuditLogger;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Blade::anonymousComponentPath(resource_path('views/wt/components'), 'wt');

        // @canwrite ... @endcanwrite — hides write controls (add/edit/delete/import)
        // from read-only roles (CEO). Backend writes are also blocked by ReadOnlyCeo middleware.
        Blade::if('canwrite', function () {
            $user = auth()->user();
            return $user && $user->canWrite();
        });

        Event::listen(Login::class, function (Login $event) {
            // Check if it's already logged (optional, but good to avoid double logs if we missed some manual calls)
            AuditLogger::log('login', 'auth', 'User ' . $event->user->name . ' logged in successfully.');
        });

        Event::listen(Logout::class, function (Logout $event) {
            if ($event->user) {
                AuditLogger::log('logout', 'auth', 'User ' . $event->user->name . ' logged out.');
            }
        });

        Event::listen(Failed::class, function (Failed $event) {
            AuditLogger::log('login', 'auth',
                'Failed login attempt for staff no: ' . ($event->credentials['staff_no'] ?? 'unknown') . '.',
                ['credentials' => array_diff_key($event->credentials, ['password' => ''])]
            );
        });
    }
}
