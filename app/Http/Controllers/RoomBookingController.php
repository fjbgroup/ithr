<?php

namespace App\Http\Controllers;

use App\Models\RoomBooking;
use App\Models\MeetingRoom;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingMail;
use Carbon\Carbon;
use App\Services\AuditLogger;

class RoomBookingController extends Controller
{
    public function pending()
    {
        return redirect()->route('rooms.index');
    }

    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $slots = [];
        $slotsJson = $request->input('slots');

        if ($slotsJson) {
            $slots = json_decode($slotsJson, true);
        } else {
            $slots[] = [
                'room_id' => $request->room_id,
                'booking_date' => $request->booking_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'is_full_day' => $request->boolean('is_full_day'),
                'attendees' => $request->attendees,
                'purpose' => $request->purpose,
            ];
        }

        if (empty($slots)) {
            return back()->with('error', 'No booking slots provided.');
        }

        return $this->finishBooking($user, $slots);
    }

    public function holdGuestBooking(Request $request)
    {
        $request->validate(['slots' => 'required|string']);

        $slots = json_decode($request->input('slots'), true);
        if (empty($slots) || !is_array($slots)) {
            return redirect()->route('hr.home')->with('error', 'No valid booking slots provided.');
        }

        session(['pending_booking' => $slots]);

        return redirect()->route('login');
    }

    public function processPendingBooking(Request $request)
    {
        $slots = session('pending_booking');
        if (empty($slots)) {
            return redirect()->route('rooms.index');
        }

        session()->forget('pending_booking');

        return $this->finishBooking(Auth::user(), $slots);
    }

    private function finishBooking(\App\Models\User $user, array $slots)
    {
        $result = $this->processSlots($user, $slots);

        if ($result['inserted'] === 0) {
            return redirect()->route('rooms.index')->with('error', 'Booking failed. Possible conflict or past date.');
        }

        AuditLogger::log('create', 'rooms',
            'Submitted ' . $result['inserted'] . ' room booking(s) for approval.',
            ['inserted' => $result['inserted'], 'skipped' => $result['skipped']]
        );

        $firstDate = $slots[0]['booking_date'];
        if ($result['skipped'] > 0 || $result['inserted'] > 1) {
            return redirect()->route('rooms.index', ['date' => $firstDate])
                ->with('success', "Processed bookings: {$result['inserted']} successful, {$result['skipped']} skipped.");
        }

        return redirect()->route('rooms.index', ['date' => $firstDate])
            ->with('success', 'Booking submitted for approval.');
    }

    private function processSlots(\App\Models\User $user, array $slots): array
    {
        $inserted = 0;
        $skipped = 0;
        $notificationTargets = [];

        foreach ($slots as $slot) {
            $roomId = $slot['room_id'];
            $date = $slot['booking_date'];
            $isFullDay = !empty($slot['is_full_day']);
            $startTime = $isFullDay ? '07:00' : $slot['start_time'];
            $endTime = $isFullDay ? '20:00' : $slot['end_time'];
            $purpose = $slot['purpose'] ?? '';
            $attendees = $slot['attendees'] ?? 1;

            if (!$roomId || !$date || !$startTime || !$endTime) { $skipped++; continue; }
            if ($isFullDay && !Carbon::parse($date)->startOfDay()->gt(Carbon::today())) { $skipped++; continue; }
            if (Carbon::parse($date . ' ' . $startTime)->isPast()) { $skipped++; continue; }

            $conflict = RoomBooking::where('room_id', $roomId)
                ->where('booking_date', $date)
                ->where('status', '!=', 'Rejected')
                ->where('start_time', '<', $endTime)
                ->where('end_time', '>', $startTime)
                ->exists();
            if ($conflict) { $skipped++; continue; }

            $booking = RoomBooking::create([
                'room_id'        => $roomId,
                'booked_by_id'   => $user->id,
                'booked_by_name' => $user->name,
                'booking_date'   => $date,
                'start_time'     => $startTime,
                'end_time'       => $endTime,
                'is_full_day'    => $isFullDay,
                'purpose'        => $purpose,
                'attendees'      => $attendees,
                'status'         => 'Pending',
            ]);
            $inserted++;

            $room    = MeetingRoom::find($roomId);
            $picIds  = $room->pics()->pluck('users.id')->toArray();
            $adminIds = User::where('role', 'admin_it')->pluck('id')->toArray();
            $targets = array_unique(array_merge($picIds, $adminIds));

            foreach ($targets as $tid) {
                $notificationTargets[$tid][] = [
                    'room' => $room->name,
                    'date' => $date,
                    'time' => substr($startTime, 0, 5) . '–' . substr($endTime, 0, 5),
                ];
            }

            $booking->setRelation('room', $room);
            $this->sendMail(
                $targets,
                'New Booking Request',
                "{$user->name} has submitted a meeting room booking that requires your review and approval.",
                $booking
            );
        }

        foreach ($notificationTargets as $targetId => $bookings) {
            $count = count($bookings);
            $first = $bookings[0];
            $msg = $user->name . ' requested ' . ($count > 1 ? "$count bookings" : $first['room']) .
                   ($count === 1 ? ' on ' . date('d M', strtotime($first['date'])) . ' ' . $first['time'] : '');

            Notification::create([
                'user_id'  => $targetId,
                'type'     => 'booking',
                'title'    => 'New Booking Request' . ($count > 1 ? 's' : ''),
                'message'  => $msg,
                'link'     => route('rooms.index', ['date' => $bookings[0]['date']]),
                'is_read'  => false,
            ]);
        }

        return compact('inserted', 'skipped');
    }

    public function update(Request $request, RoomBooking $booking)
    {
        $user = Auth::user();
        if ($booking->booked_by_id != $user->id || !in_array($booking->status, ['Pending', 'Approved'])) {
            return back()->with('error', 'Unauthorized or invalid status.');
        }

        $bookingDate = $request->booking_date;
        $startTime = $request->start_time;
        $endTime = $request->end_time;
        $purpose = $request->purpose;
        $attendees = $request->attendees;
        $roomId = $request->room_id;

        $newStart = Carbon::parse($bookingDate . ' ' . $startTime);
        if ($newStart->isPast()) {
            return back()->with('error', 'Cannot set booking to a past time.');
        }

        $booking->update([
            'status' => 'EditRequested',
            'proposed_room_id' => $roomId,
            'proposed_date' => $bookingDate,
            'proposed_start_time' => $startTime,
            'proposed_end_time' => $endTime,
            'proposed_purpose' => $purpose,
            'proposed_attendees' => $attendees,
            'edit_reason' => $request->edit_reason
        ]);

        // Notify PICs and Admins
        $room = $booking->room;
        $picIds = $room->pics()->pluck('users.id')->toArray();
        $adminIds = User::where('role', 'admin_it')->pluck('id')->toArray();
        $targets = array_unique(array_merge($picIds, $adminIds));

        foreach ($targets as $tid) {
            Notification::create([
                'user_id' => $tid,
                'type' => 'booking',
                'title' => 'Booking Edit Request',
                'message' => $user->name . ' requested changes to their booking for ' . $room->name . ' on ' . date('d M Y', strtotime($booking->booking_date)) . '.',
                'link' => route('rooms.index', ['date' => $booking->booking_date]),
            ]);
        }

        $this->sendMail(
            $targets,
            'Booking Edit Request',
            "{$user->name} has requested changes to their meeting room booking and the request requires your review.",
            $booking,
            $request->edit_reason ? 'Reason for edit: ' . $request->edit_reason : null
        );

        AuditLogger::log('update', 'rooms',
            'Requested edit for booking #' . $booking->id . ' for ' . ($room->name ?? 'room') . ' on ' . $booking->booking_date . '.',
            ['booking_id' => $booking->id, 'reason' => $request->edit_reason]
        );

        return back()->with('success', 'Edit request submitted.');
    }

    public function cancelRequest(Request $request, RoomBooking $booking)
    {
        $user = Auth::user();
        if ($booking->booked_by_id != $user->id || in_array($booking->status, ['Rejected', 'CancelRequested', 'EditRequested'])) {
            return back()->with('error', 'Unauthorized or invalid status.');
        }

        $booking->update([
            'status' => 'CancelRequested',
            'cancel_reason' => $request->cancel_reason
        ]);

        $room = $booking->room;
        $picIds = $room->pics()->pluck('users.id')->toArray();
        $adminIds = User::where('role', 'admin_it')->pluck('id')->toArray();
        $targets = array_unique(array_merge($picIds, $adminIds));

        foreach ($targets as $tid) {
            Notification::create([
                'user_id' => $tid,
                'type' => 'booking',
                'title' => 'Booking Cancellation Request',
                'message' => $user->name . ' requested to cancel their booking for ' . $room->name . ' on ' . date('d M Y', strtotime($booking->booking_date)) . '.',
                'link' => route('rooms.index', ['date' => $booking->booking_date]),
            ]);
        }

        $this->sendMail(
            $targets,
            'Booking Cancellation Request',
            "{$user->name} has requested to cancel their meeting room booking and the request requires your review.",
            $booking,
            $request->cancel_reason ? 'Reason: ' . $request->cancel_reason : null
        );

        AuditLogger::log('update', 'rooms',
            'Requested cancellation of booking #' . $booking->id . ' for ' . ($room->name ?? 'room') . ' on ' . $booking->booking_date . '.',
            ['booking_id' => $booking->id, 'reason' => $request->cancel_reason]
        );

        return back()->with('success', 'Cancellation request submitted.');
    }

    public function approve(Request $request, RoomBooking $booking)
    {
        $user = Auth::user();
        if (!$user->isAdminIT() && !$this->isPicForRoom($booking->room_id, $user->id)) {
            return back()->with('error', 'Unauthorized.');
        }

        if ($booking->status !== 'Pending') {
            return back()->with('error', 'Booking is not pending.');
        }

        $booking->update([
            'status' => 'Approved',
            'approved_by_id' => $user->id,
            'approved_by_name' => $user->name,
            'approved_at' => now(),
        ]);

        // Auto-reject conflicts
        $conflicts = RoomBooking::where('room_id', $booking->room_id)
            ->where('booking_date', $booking->booking_date)
            ->where('status', 'Pending')
            ->where('id', '!=', $booking->id)
            ->where('start_time', '<', $booking->end_time)
            ->where('end_time', '>', $booking->start_time)
            ->get();

        foreach ($conflicts as $c) {
            $c->update([
                'status' => 'Rejected',
                'approved_by_id' => $user->id,
                'approved_by_name' => $user->name,
                'approved_at' => now(),
                'rejection_reason' => 'Automatically rejected — a conflicting booking was approved.'
            ]);

            Notification::create([
                'user_id' => $c->booked_by_id,
                'type' => 'booking',
                'title' => 'Booking Rejected',
                'message' => 'Your booking for ' . $booking->room->name . ' on ' . date('d M Y', strtotime($booking->booking_date)) . ' was automatically rejected due to conflict.',
                'link' => route('rooms.index', ['date' => $booking->booking_date]),
            ]);

            $c->setRelation('room', $booking->room);
            $this->sendMail(
                [$c->booked_by_id],
                'Booking Rejected',
                'Your meeting room booking was automatically rejected because a conflicting booking was approved.',
                $c,
                'Reason: Automatically rejected — a conflicting booking was approved.'
            );
        }

        Notification::create([
            'user_id' => $booking->booked_by_id,
            'type' => 'booking',
            'title' => 'Booking Approved',
            'message' => 'Your booking for ' . $booking->room->name . ' on ' . date('d M Y', strtotime($booking->booking_date)) . ' has been approved.',
            'link' => route('rooms.index', ['date' => $booking->booking_date]),
        ]);

        $this->sendMail(
            [$booking->booked_by_id],
            'Booking Approved',
            'Great news! Your meeting room booking has been approved by ' . $user->name . '.',
            $booking
        );

        AuditLogger::log('approve', 'rooms',
            'Approved room booking #' . $booking->id . ' for ' . ($booking->room->name ?? 'room') . ' on ' . $booking->booking_date . '.',
            ['booking_id' => $booking->id]
        );

        return back()->with('success', 'Booking approved.');
    }

    public function reject(Request $request, RoomBooking $booking)
    {
        $user = Auth::user();
        if (!$user->isAdminIT() && !$this->isPicForRoom($booking->room_id, $user->id)) {
            return back()->with('error', 'Unauthorized.');
        }

        if ($booking->status !== 'Pending') {
            return back()->with('error', 'Booking is not pending.');
        }

        $booking->update([
            'status' => 'Rejected',
            'approved_by_id' => $user->id,
            'approved_by_name' => $user->name,
            'approved_at' => now(),
            'rejection_reason' => $request->rejection_reason
        ]);

        Notification::create([
            'user_id' => $booking->booked_by_id,
            'type' => 'booking',
            'title' => 'Booking Rejected',
            'message' => 'Your booking for ' . $booking->room->name . ' on ' . date('d M Y', strtotime($booking->booking_date)) . ' was rejected.',
            'link' => route('rooms.index', ['date' => $booking->booking_date]),
        ]);

        $this->sendMail(
            [$booking->booked_by_id],
            'Booking Rejected',
            'Unfortunately, your meeting room booking has been rejected.',
            $booking,
            $request->rejection_reason ? 'Reason: ' . $request->rejection_reason : null
        );

        AuditLogger::log('reject', 'rooms',
            'Rejected room booking #' . $booking->id . ' for ' . ($booking->room->name ?? 'room') . ' on ' . $booking->booking_date . '.',
            ['booking_id' => $booking->id, 'reason' => $request->rejection_reason]
        );

        return back()->with('success', 'Booking rejected.');
    }

    public function approveCancel(Request $request, RoomBooking $booking)
    {
        $user = Auth::user();
        if (!$user->isAdminIT() && !$this->isPicForRoom($booking->room_id, $user->id)) {
            return back()->with('error', 'Unauthorized.');
        }

        if ($booking->status !== 'CancelRequested') {
            return back()->with('error', 'No cancellation request found.');
        }

        Notification::create([
            'user_id' => $booking->booked_by_id,
            'type' => 'booking',
            'title' => 'Cancellation Approved',
            'message' => 'Your cancellation request for ' . $booking->room->name . ' on ' . date('d M Y', strtotime($booking->booking_date)) . ' has been approved.',
            'link' => route('rooms.index', ['date' => $booking->booking_date]),
        ]);

        $this->sendMail(
            [$booking->booked_by_id],
            'Cancellation Approved',
            'Your request to cancel the meeting room booking has been approved. The booking has been removed.',
            $booking,
            null,
            '#15803d' // Green
        );

        AuditLogger::log('approve', 'rooms',
            'Approved cancellation of booking #' . $booking->id . ' for ' . ($booking->room->name ?? 'room') . ' on ' . $booking->booking_date . '.',
            ['booking_id' => $booking->id]
        );

        $booking->delete();

        return back()->with('success', 'Booking cancelled and removed.');
    }

    public function rejectCancel(Request $request, RoomBooking $booking)
    {
        $user = Auth::user();
        if (!$user->isAdminIT() && !$this->isPicForRoom($booking->room_id, $user->id)) {
            return back()->with('error', 'Unauthorized.');
        }

        if ($booking->status !== 'CancelRequested') {
            return back()->with('error', 'No cancellation request found.');
        }

        $booking->update([
            'status' => $booking->approved_at ? 'Approved' : 'Pending',
            'cancel_reason' => null
        ]);

        Notification::create([
            'user_id' => $booking->booked_by_id,
            'type' => 'booking',
            'title' => 'Cancellation Rejected',
            'message' => 'Your cancellation request for ' . $booking->room->name . ' on ' . date('d M Y', strtotime($booking->booking_date)) . ' was declined.',
            'link' => route('rooms.index', ['date' => $booking->booking_date]),
        ]);

        $this->sendMail(
            [$booking->booked_by_id],
            'Cancellation Request Declined',
            'Your request to cancel the meeting room booking has been declined. The booking remains active.',
            $booking,
            null,
            '#b91c1c' // Red
        );

        AuditLogger::log('reject', 'rooms',
            'Rejected cancellation request for booking #' . $booking->id . ' for ' . ($booking->room->name ?? 'room') . '.',
            ['booking_id' => $booking->id]
        );

        return back()->with('success', 'Cancellation request rejected.');
    }

    public function approveEdit(Request $request, RoomBooking $booking)
    {
        $user = Auth::user();
        if (!$user->isAdminIT() && !$this->isPicForRoom($booking->room_id, $user->id)) {
            return back()->with('error', 'Unauthorized.');
        }

        if ($booking->status !== 'EditRequested') {
            return back()->with('error', 'No edit request found.');
        }

        $booking->update([
            'room_id' => $booking->proposed_room_id ?: $booking->room_id,
            'booking_date' => $booking->proposed_date ?: $booking->booking_date,
            'start_time' => $booking->proposed_start_time ?: $booking->start_time,
            'end_time' => $booking->proposed_end_time ?: $booking->end_time,
            'purpose' => $booking->proposed_purpose ?: $booking->purpose,
            'attendees' => $booking->proposed_attendees ?: $booking->attendees,
            'status' => 'Approved',
            'approved_by_id' => $user->id,
            'approved_by_name' => $user->name,
            'approved_at' => now(),
            'proposed_room_id' => null,
            'proposed_date' => null,
            'proposed_start_time' => null,
            'proposed_end_time' => null,
            'proposed_purpose' => null,
            'proposed_attendees' => null,
            'edit_reason' => null,
        ]);

        Notification::create([
            'user_id' => $booking->booked_by_id,
            'type' => 'booking',
            'title' => 'Edit Request Approved',
            'message' => 'Your booking changes have been approved.',
            'link' => route('rooms.index', ['date' => $booking->booking_date]),
        ]);

        $this->sendMail(
            [$booking->booked_by_id],
            'Booking Edit Approved',
            'Your requested changes to the meeting room booking have been approved by ' . $user->name . '.',
            $booking
        );

        AuditLogger::log('approve', 'rooms',
            'Approved edit request for booking #' . $booking->id . ' for ' . ($booking->room->name ?? 'room') . '.',
            ['booking_id' => $booking->id]
        );

        return back()->with('success', 'Edit request approved.');
    }

    public function rejectEdit(Request $request, RoomBooking $booking)
    {
        $user = Auth::user();
        if (!$user->isAdminIT() && !$this->isPicForRoom($booking->room_id, $user->id)) {
            return back()->with('error', 'Unauthorized.');
        }

        if ($booking->status !== 'EditRequested') {
            return back()->with('error', 'No edit request found.');
        }

        $booking->update([
            'status' => $booking->approved_at ? 'Approved' : 'Pending',
            'proposed_room_id' => null,
            'proposed_date' => null,
            'proposed_start_time' => null,
            'proposed_end_time' => null,
            'proposed_purpose' => null,
            'proposed_attendees' => null,
            'edit_reason' => null,
        ]);

        Notification::create([
            'user_id' => $booking->booked_by_id,
            'type' => 'booking',
            'title' => 'Edit Request Rejected',
            'message' => 'Your edit request was declined. Original booking remains active.',
            'link' => route('rooms.index', ['date' => $booking->booking_date]),
        ]);

        $this->sendMail(
            [$booking->booked_by_id],
            'Booking Edit Declined',
            'Your request to modify the meeting room booking has been declined by ' . $user->name . '. The original booking remains active.',
            $booking,
            null,
            '#b91c1c' // Red
        );

        AuditLogger::log('reject', 'rooms',
            'Rejected edit request for booking #' . $booking->id . ' for ' . ($booking->room->name ?? 'room') . '.',
            ['booking_id' => $booking->id]
        );

        return back()->with('success', 'Edit request rejected.');
    }

    private function isPicForRoom($roomId, $userId)
    {
        return DB::table('room_pics')
            ->where('room_id', $roomId)
            ->where('user_id', $userId)
            ->exists();
    }

    private function sendMail(array $userIds, string $heading, string $body, RoomBooking $booking, ?string $extraNote = null, ?string $color = null): void
    {
        if (empty($userIds)) return;

        if (!$color) {
            $color = match ($booking->status) {
                'Approved' => '#15803d', // Green
                'Rejected' => '#b91c1c', // Red
                'Pending', 'CancelRequested', 'EditRequested' => '#b45309', // Yellow
                default => '#1e40af', // Blue
            };
        }

        $booking->loadMissing('room');
        $recipients = User::whereIn('id', $userIds)->whereNotNull('email')->where('email', '!=', '')->get();
        foreach ($recipients as $recipient) {
            try {
                Mail::to($recipient->email)->send(new BookingMail($heading, $body, $booking, $extraNote, $color));
            } catch (\Exception $e) {
                \Log::error('BookingMail failed for user ' . $recipient->id . ': ' . $e->getMessage());
            }
        }
    }
}
