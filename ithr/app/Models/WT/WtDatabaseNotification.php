<?php

namespace App\Models\WT;

use Illuminate\Notifications\DatabaseNotification;

class WtDatabaseNotification extends DatabaseNotification
{
    protected $table = 'wt_notifications';
}
