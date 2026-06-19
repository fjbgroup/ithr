<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('user_name')) {
            $query->where('user_name', 'like', '%' . $request->user_name . '%');
        }

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(50)->withQueryString();

        $modules = [
            'auth'        => 'Auth',
            'staff'       => 'Staff',
            'users'       => 'User Accounts',
            'rooms'       => 'Meeting Rooms',
            'training'    => 'Training',
            'family'      => 'Family Info',
            'travel'      => 'Travel',
            'ir'          => 'IR Records',
            'requests'    => 'Update Requests',
            'settings'    => 'Settings',
            'master_data' => 'Master Data',
        ];

        $actions = [
            'login'   => 'Login',
            'logout'  => 'Logout',
            'create'  => 'Create',
            'update'  => 'Update',
            'delete'  => 'Delete',
            'approve' => 'Approve',
            'reject'  => 'Reject',
            'import'  => 'Import',
            'toggle'  => 'Toggle',
            'resolve' => 'Resolve',
            'dismiss' => 'Dismiss',
        ];

        return view('audit.index', compact('logs', 'modules', 'actions'));
    }
}
