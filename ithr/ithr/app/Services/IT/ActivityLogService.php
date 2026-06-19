<?php

namespace App\Services\IT;

use App\Models\IT\ActivityLog;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    public static function log(string $action, string $itemType, int $itemId, string $description, ?int $userId = null): void
    {
        ActivityLog::create([
            'user_id'     => $userId ?? auth('it')->id(),
            'action'      => $action,
            'item_type'   => $itemType,
            'item_id'     => $itemId,
            'description' => $description,
            'ip_address'  => Request::ip(),
        ]);
    }
}
