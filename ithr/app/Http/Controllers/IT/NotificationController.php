<?php

namespace App\Http\Controllers\IT;

use App\Models\IT\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function ajax(Request $request)
    {
        $userId = Auth::guard('it')->id();

        if ($request->query('action') === 'count') {
            $unread = Notification::where('user_id', $userId)->where('is_read', false)->count();
            return response()->json(['count' => $unread]);
        }

        $recent = Notification::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->map(function ($n) {
                return [
                    'id'         => $n->id,
                    'type'       => $n->type,
                    'title'      => $n->title,
                    'message'    => $n->message,
                    'link'       => $n->link,
                    'is_read'    => $n->is_read,
                    'created_at' => $n->created_at ? $n->created_at->format('Y-m-d H:i:s') : '',
                ];
            });

        return response()->json($recent);
    }

    public function markRead(Request $request)
    {
        $userId = Auth::guard('it')->id();
        if ($id = $request->id) {
            Notification::where('id', $id)->where('user_id', $userId)->update(['is_read' => true]);
        } else {
            Notification::where('user_id', $userId)->update(['is_read' => true]);
        }
        return response()->json(['success' => true]);
    }
}

