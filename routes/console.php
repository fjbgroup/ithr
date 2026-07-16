<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Auto-archive decided IT requests older than 30 days
Schedule::command('it:auto-archive')->daily();

// Auto-reject bookings that have been pending for > 24 hours
Schedule::command('booking:auto-reject')->hourly();

