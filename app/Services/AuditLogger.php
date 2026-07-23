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
            $ip = request()->ip();
            $mac = self::getMacAddress($ip);

            ActivityLog::create([
                'user_id'     => $user?->id,
                'user_name'   => $user?->name,
                'user_role'   => $user?->role,
                'action'      => $action,
                'module'      => $module,
                'description' => $description,
                'ip_address'  => $ip,
                'mac_address' => $mac,
                'properties'  => empty($properties) ? null : $properties,
            ]);
        } catch (\Throwable $e) {
            \Log::error('AuditLogger::log failed: ' . $e->getMessage());
        }
    }

    private static function getMacAddress(?string $ip): ?string
    {
        if (!$ip || $ip === '127.0.0.1' || $ip === '::1') {
            return null;
        }

        $macPattern = '/([a-fA-F0-9]{2}[:-]){5}[a-fA-F0-9]{2}/';
        @exec('arp -a ' . escapeshellarg($ip), $output, $status);

        if ($status === 0 && !empty($output)) {
            foreach ($output as $line) {
                if (preg_match($macPattern, $line, $matches)) {
                    return str_replace('-', ':', strtoupper($matches[0]));
                }
            }
        }
        return null;
    }
}
