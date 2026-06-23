<?php

namespace App\Http\Controllers\WT;

use Illuminate\Http\Request;
use App\Models\WT\WalkieTalkie;
use App\Models\WT\UserActivityLog;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $statusCounts = WalkieTalkie::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        $statusOrder = [
            'IN USE',
            'UNUSED',
            'REPAIRING',
            'FAULTY',
            'B.E.R',
            'LOST',
            'CALIBRATING',
            'TEMPORARY',
            'CHANGE ID',
            'UNKNOWN',
        ];

        $statusPalette = [
            'IN USE' => 'rgba(59, 130, 246, 0.85)',
            'UNUSED' => 'rgba(107, 114, 128, 0.85)',
            'REPAIRING' => 'rgba(236, 72, 153, 0.85)',
            'FAULTY' => 'rgba(245, 158, 11, 0.85)',
            'B.E.R' => 'rgba(16, 185, 129, 0.85)',
            'LOST' => 'rgba(139, 92, 246, 0.85)',
            'CALIBRATING' => 'rgba(20, 184, 166, 0.85)',
            'TEMPORARY' => 'rgba(249, 115, 22, 0.85)',
            'CHANGE ID' => 'rgba(99, 102, 241, 0.85)',
            'UNKNOWN' => 'rgba(148, 163, 184, 0.85)',
        ];

        $statusAliases = [
            'INUSE' => 'IN USE',
            'IN USE' => 'IN USE',
            'UNUSED' => 'UNUSED',
            'SPARE' => 'UNUSED',
            'REPAIR' => 'REPAIRING',
            'REPAIRING' => 'REPAIRING',
            'UNDER REPAIR' => 'REPAIRING',
            'FAULTY' => 'FAULTY',
            'B.E.R' => 'B.E.R',
            'BER' => 'B.E.R',
            'LOST' => 'LOST',
            'CALIBRATING' => 'CALIBRATING',
            'TEMPORARY' => 'TEMPORARY',
            'CHANGE ID' => 'CHANGE ID',
            'UNKNOWN' => 'UNKNOWN',
        ];

        $normalizedCounts = [];

        foreach ($statusCounts as $row) {
            $rawStatus = trim((string) $row->status);
            $statusLookup = strtoupper($rawStatus);
            $statusLabel = $rawStatus === ''
                ? 'UNKNOWN'
                : ($statusAliases[$statusLookup] ?? $statusLookup);

            $normalizedCounts[$statusLabel] = ($normalizedCounts[$statusLabel] ?? 0) + (int) $row->total;
        }

        $statusCountArray = [];
        $originalLabels = [];
        $originalValues = [];
        $originalColors = [];

        foreach ($statusOrder as $statusLabel) {
            if (! isset($normalizedCounts[$statusLabel])) {
                continue;
            }

            $statusKey = strtolower(str_replace([' ', '.'], '', $statusLabel));
            $statusCountArray[$statusKey] = $normalizedCounts[$statusLabel];
            $originalLabels[] = $statusLabel;
            $originalValues[] = $normalizedCounts[$statusLabel];
            $originalColors[] = $statusPalette[$statusLabel];
            unset($normalizedCounts[$statusLabel]);
        }

        foreach ($normalizedCounts as $statusLabel => $total) {
            $statusKey = strtolower(str_replace([' ', '.'], '', $statusLabel));
            $statusCountArray[$statusKey] = $total;
            $originalLabels[] = $statusLabel;
            $originalValues[] = $total;
            $originalColors[] = 'rgba(100, 116, 139, 0.85)';
        }

        $totalWalkie = WalkieTalkie::count();

        $recentWalkies = WalkieTalkie::orderBy('walkie_id', 'desc')->take(5)->get();
        $recentActivities = UserActivityLog::with('user')
            ->orderByDesc('created_at')
            ->take(6)
            ->get();

        return view('wt.admin.dashboard', compact(
            'statusCountArray', 
            'originalLabels', 
            'originalValues', 
            'originalColors', 
            'totalWalkie', 
            'recentWalkies',
            'recentActivities'
        ));
    }
}

