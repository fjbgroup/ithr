<?php

namespace App\Http\Controllers\WT;

use Illuminate\Http\Request;
use App\Models\WT\UserActivityLog;

class ActivityLogController extends Controller
{
    public function index()
    {
        // Get all logs, ordered latest first
        $logs = UserActivityLog::orderBy('id', 'desc')->get();
        return view('wt.admin.activity_logs.index', compact('logs'));
    }
}


