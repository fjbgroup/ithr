<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function list()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->latest()
            ->limit(20)
            ->get();
            
        return response()->json($notifications);
    }

    public function unreadCount()
    {
        return response()->json([
            'count' => Notification::where('user_id', Auth::id())->where('is_read', false)->count(),
            'latest_id' => Notification::where('user_id', Auth::id())->latest()->value('id') ?? 0,
        ]);
    }

    public function markRead(Request $request)
    {
        $ids = $request->input('ids', []);
        
        Notification::whereIn('id', $ids)
            ->where('user_id', Auth::id())
            ->update(['is_read' => true]);
            
        return response()->json(['success' => true]);
    }
}
