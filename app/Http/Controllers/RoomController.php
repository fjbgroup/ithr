<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MeetingRoom;
use App\Models\RoomBooking;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        // If root route and logged in, go to dashboard
        if ($request->is('/') && Auth::check()) {
            return redirect()->route('dashboard');
        }

        // If root route and guest, show landing page
        if ($request->is('/') && !Auth::check()) {
            return $this->landing($request);
        }

        $viewDate = $request->get('date', date('Y-m-d'));
        $viewMode = $request->get('view', 'day');
        if (!in_array($viewMode, ['day', 'week', 'month', 'manage', 'my-bookings'])) {
            $viewMode = 'day';
        }

        $user = Auth::user();
        if ($viewMode === 'manage' && (!$user || !$user->isAdminIT())) {
            return redirect()->route('rooms.index')->with('error', 'Unauthorized.');
        }

        if ($viewMode === 'my-bookings' && !$user) {
            return redirect()->route('rooms.index')->with('error', 'Please login to view your bookings.');
        }

        // Date calculations
        $ts = strtotime($viewDate);
        $rangeStart = $viewDate;
        $rangeEnd = $viewDate;

        if ($viewMode === 'week') {
            $dow = (int)date('N', $ts);
            $rangeStart = date('Y-m-d', strtotime('-' . ($dow - 1) . ' days', $ts));
            $rangeEnd = date('Y-m-d', strtotime('+' . (7 - $dow) . ' days', $ts));
        } elseif ($viewMode === 'month') {
            $rangeStart = date('Y-m-01', $ts);
            $rangeEnd = date('Y-m-t', $ts);
        }

        $rooms = MeetingRoom::with(['pics'])->orderBy('id')->get();
        
        $bookingsQuery = RoomBooking::with(['room', 'proposedRoom', 'user.department'])
            ->where('status', '!=', 'Rejected');

        if ($viewMode === 'my-bookings') {
            $allRangeBookings = $bookingsQuery->where('booked_by_id', $user->id)
                ->orderBy('booking_date', 'DESC')
                ->orderBy('start_time', 'DESC')
                ->paginate(20);
        } else {
            $allRangeBookings = $bookingsQuery->whereBetween('booking_date', [$rangeStart, $rangeEnd])
                ->orderBy('booking_date')
                ->orderBy('room_id')
                ->orderBy('start_time')
                ->get();
        }

        // PIC / Approval Logic
        $myPicRoomIds = [];
        $isPic = false;
        $canApprove = false;
        $pendingForMe = collect();
        $myPendingBookings = collect();
        $myBookings = collect();

        if ($user) {
            // Auto-mark booking notifications as read
            Notification::where('user_id', $user->id)
                ->where('type', 'booking')
                ->where('is_read', false)
                ->update(['is_read' => true]);

            $myPicRoomIds = DB::table('room_pics')
                ->where('user_id', $user->id)
                ->pluck('room_id')
                ->toArray();
            $isPic = !empty($myPicRoomIds);
            $canApprove = $user->isAdminIT() || $isPic;

            if ($canApprove) {
                $query = RoomBooking::with(['room', 'proposedRoom'])
                    ->whereIn('status', ['Pending', 'CancelRequested', 'EditRequested']);

                if (!$user->isAdminIT()) {
                    $query->whereIn('room_id', $myPicRoomIds);
                }
                $pendingForMe = $query->orderBy('booking_date')->orderBy('start_time')->get();
            }

            $myPendingBookings = RoomBooking::with(['room', 'proposedRoom'])
                ->where('booked_by_id', $user->id)
                ->whereIn('status', ['Pending', 'CancelRequested', 'EditRequested'])
                ->orderBy('booking_date')
                ->orderBy('start_time')
                ->get();

            // Small list for the top section (only if not in my-bookings view or always keep it?)
            // Let's keep it for dashboard-like feel on the main schedule page
            $myBookings = RoomBooking::with('room')
                ->where('booked_by_id', $user->id)
                ->where('booking_date', '>=', date('Y-m-d'))
                ->orderBy('booking_date')->orderBy('start_time')->limit(10)->get();
        }

        $allUsers = [];
        if ($user && $user->isAdminIT()) {
            $allUsers = User::where('is_active', true)->orderBy('name')->get();
        }

        $colorMap = [
            'navy' => '#1e3a8a', 'blue' => '#3b82f6', 'sky' => '#0ea5e9',
            'indigo' => '#6366f1', 'purple' => '#a855f7', 'pink' => '#ec4899',
            'rose' => '#f43f5e', 'red' => '#ef4444', 'orange' => '#f97316',
            'amber' => '#f59e0b', 'yellow' => '#eab308', 'lime' => '#84cc16',
            'green' => '#22c55e', 'emerald' => '#10b981', 'teal' => '#14b8a6',
            'cyan' => '#06b6d4', 'slate' => '#64748b'
        ];
        $roomEmojis = ['Meeting' => '🤝', 'Discussion' => '💬', 'Training' => '📚', 'Interview' => '👥', 'Presentation' => '📽️', 'Video' => '🎥', 'Focus' => '🧘'];
        $isPastDay = strtotime($viewDate) < strtotime(date('Y-m-d'));

        return view('rooms.index', compact(
            'rooms', 'allRangeBookings', 'viewDate', 'viewMode',
            'rangeStart', 'rangeEnd', 'pendingForMe', 'canApprove', 'allUsers',
            'colorMap', 'roomEmojis', 'isPastDay', 'myBookings', 'myPendingBookings'
        ));
    }

    public function pollBookings(Request $request)
    {
        $date = $request->get('date', date('Y-m-d'));
        $view = $request->get('view', 'day');

        $ts = strtotime($date);
        $rangeStart = $date;
        $rangeEnd = $date;

        if ($view === 'week') {
            $dow = (int)date('N', $ts);
            $rangeStart = date('Y-m-d', strtotime('-' . ($dow - 1) . ' days', $ts));
            $rangeEnd = date('Y-m-d', strtotime('+' . (7 - $dow) . ' days', $ts));
        } elseif ($view === 'month') {
            $rangeStart = date('Y-m-01', $ts);
            $rangeEnd = date('Y-m-t', $ts);
        }

        return response()->json(
            RoomBooking::with(['room', 'user.department'])
                ->where('status', '!=', 'Rejected')
                ->whereBetween('booking_date', [$rangeStart, $rangeEnd])
                ->orderBy('booking_date')
                ->orderBy('room_id')
                ->orderBy('start_time')
                ->get()
        );
    }

    public function landing(Request $request)
    {
        $viewDate = $request->get('date', date('Y-m-d'));
        $viewMode = $request->get('view', 'day');
        if (!in_array($viewMode, ['day', 'week', 'month'])) {
            $viewMode = 'day';
        }

        $ts = strtotime($viewDate);
        $rangeStart = $viewDate;
        $rangeEnd = $viewDate;

        if ($viewMode === 'week') {
            $dow = (int)date('N', $ts);
            $rangeStart = date('Y-m-d', strtotime('-' . ($dow - 1) . ' days', $ts));
            $rangeEnd = date('Y-m-d', strtotime('+' . (7 - $dow) . ' days', $ts));
        } elseif ($viewMode === 'month') {
            $rangeStart = date('Y-m-01', $ts);
            $rangeEnd = date('Y-m-t', $ts);
        }

        $rooms = MeetingRoom::with(['pics'])->orderBy('id')->get();
        $allRangeBookings = RoomBooking::with(['room', 'proposedRoom', 'user.department'])
            ->whereBetween('booking_date', [$rangeStart, $rangeEnd])
            ->where('status', '!=', 'Rejected')
            ->orderBy('booking_date')
            ->orderBy('room_id')
            ->orderBy('start_time')
            ->get();

        $colorMap = [
            'navy' => '#1e3a8a', 'blue' => '#3b82f6', 'sky' => '#0ea5e9',
            'indigo' => '#6366f1', 'purple' => '#a855f7', 'pink' => '#ec4899',
            'rose' => '#f43f5e', 'red' => '#ef4444', 'orange' => '#f97316',
            'amber' => '#f59e0b', 'yellow' => '#eab308', 'lime' => '#84cc16',
            'green' => '#22c55e', 'emerald' => '#10b981', 'teal' => '#14b8a6',
            'cyan' => '#06b6d4', 'slate' => '#64748b'
        ];
        $roomEmojis = ['Meeting' => '🤝', 'Discussion' => '💬', 'Training' => '📚', 'Interview' => '👥', 'Presentation' => '📽️', 'Video' => '🎥', 'Focus' => '🧘'];
        $isPastDay = strtotime($viewDate) < strtotime(date('Y-m-d'));

        return view('welcome', compact('rooms', 'allRangeBookings', 'viewDate', 'viewMode', 'rangeStart', 'rangeEnd', 'colorMap', 'roomEmojis', 'isPastDay'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isAdminIT()) {
            return back()->with('error', 'Unauthorized.');
        }

        $validated = $request->validate([
            'room_name' => 'required|string|max:255',
            'room_description' => 'nullable|string',
            'room_capacity' => 'required|integer|min:1',
            'room_color' => 'required|string',
            'room_pics' => 'nullable|array',
            'room_pics.*' => 'nullable|exists:users,id',
        ]);

        $room = MeetingRoom::create([
            'name' => $validated['room_name'],
            'description' => $validated['room_description'],
            'capacity' => $validated['room_capacity'],
            'color_class' => $validated['room_color'],
        ]);

        if (!empty($validated['room_pics'])) {
            $picIds = array_slice(array_filter(array_unique($validated['room_pics'])), 0, 2);
            $level = 1;
            foreach ($picIds as $pid) {
                if ($pid) {
                    $room->pics()->attach($pid, ['level' => $level, 'added_by' => Auth::id()]);
                    $level++;
                }
            }
        }

        return back()->with('success', 'Room added successfully.');
    }

    public function update(Request $request, MeetingRoom $room)
    {
        if (!Auth::user()->isAdminIT()) {
            return back()->with('error', 'Unauthorized.');
        }

        $validated = $request->validate([
            'room_name' => 'required|string|max:255',
            'room_description' => 'nullable|string',
            'room_capacity' => 'required|integer|min:1',
            'room_color' => 'required|string',
            'room_pics' => 'nullable|array',
            'room_pics.*' => 'nullable|exists:users,id',
        ]);

        $room->update([
            'name' => $validated['room_name'],
            'description' => $validated['room_description'],
            'capacity' => $validated['room_capacity'],
            'color_class' => $validated['room_color'],
        ]);

        $room->pics()->detach();
        if (!empty($validated['room_pics'])) {
            $picIds = array_slice(array_filter(array_unique($validated['room_pics'])), 0, 2);
            $level = 1;
            foreach ($picIds as $pid) {
                if ($pid) {
                    $room->pics()->attach($pid, ['level' => $level, 'added_by' => Auth::id()]);
                    $level++;
                }
            }
        }

        return back()->with('success', 'Room updated successfully.');
    }

    public function destroy(MeetingRoom $room)
    {
        if (!Auth::user()->isAdminIT()) {
            return back()->with('error', 'Unauthorized.');
        }

        $room->bookings()->delete();
        $room->pics()->detach();
        $room->delete();

        return back()->with('success', 'Room deleted successfully.');
    }
}
