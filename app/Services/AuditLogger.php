<?php

namespace App\Services;

use App\Models\ActivityLog;

class AuditLogger
{
    public static function log(
        string $action,
        string $module,
        string $description,
        array $properties = []
    ): void {
        try {
            $user = auth()->user();

            ActivityLog::create([
                'user_id'     => $user?->id,
                'user_name'   => $user?->name,
                'user_role'   => $user?->role,
                'action'      => $action,
                'module'      => $module,
                'description' => $description,
                'ip_address'  => request()->ip(),
                'properties'  => empty($properties) ? null : $properties,
            ]);
        } catch (\Throwable $e) {
            \Log::error('AuditLogger::log failed: ' . $e->getMessage());
        }
    }
}
