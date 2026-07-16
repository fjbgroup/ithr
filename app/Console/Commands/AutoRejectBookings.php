<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RoomBooking;
use App\Models\Notification;
use Carbon\Carbon;

class AutoRejectBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:auto-reject';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically reject pending booking requests that have not been responded to for more than 24 hours (i.e. made the previous day).';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Find bookings that are still pending or edit requested and were created more than 24 hours ago
        $expiredBookings = RoomBooking::with('room')
            ->whereIn('status', ['Pending', 'EditRequested'])
            ->where('created_at', '<', Carbon::now()->subHours(24))
            ->get();

        $count = 0;
        foreach ($expiredBookings as $booking) {
            $booking->update([
                'status' => 'Rejected',
                'rejection_reason' => 'Automatically rejected due to no response from approver after 24 hours.',
            ]);

            // Notify the user who made the booking
            Notification::create([
                'user_id' => $booking->booked_by_id,
                'type' => 'booking',
                'title' => 'Booking Automatically Rejected',
                'message' => 'Your booking request for ' . ($booking->room->name ?? 'room') . ' on ' . date('d M Y', strtotime($booking->booking_date)) . ' was automatically rejected due to no response from the approver within 24 hours.',
                'link' => route('rooms.index', ['date' => $booking->booking_date]),
            ]);

            $count++;
        }

        $this->info("Successfully auto-rejected {$count} bookings.");
    }
}
