<?php

namespace App\Http\Controllers\IT;

use App\Models\IT\ActivityLog;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $logs = ActivityLog::with('user')
            ->orderByDesc('created_at')
            ->limit(200)
            ->get();

        $totalCount = ActivityLog::count();

        $today = now()->toDateString();
        $todayStats = ActivityLog::whereDate('created_at', $today)
            ->selectRaw('action, COUNT(*) as cnt')
            ->groupBy('action')
            ->pluck('cnt', 'action')
            ->toArray();

        $activeUsers = ActivityLog::where('action', 'LOGIN')
            ->whereDate('created_at', $today)
            ->distinct('user_id')
            ->count('user_id');

        return view('it.activity.index', compact('logs', 'totalCount', 'todayStats', 'activeUsers'));
    }
}

