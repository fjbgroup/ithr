<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomBooking extends Model
{
    protected $fillable = [
        'room_id', 'booked_by_id', 'booked_by_name', 'booking_date', 'start_time', 'end_time',
        'is_full_day', 'purpose', 'attendees', 'status', 'approved_by_id', 'approved_by_name',
        'approved_at', 'rejection_reason', 'cancel_reason', 'proposed_room_id', 'proposed_date',
        'proposed_start_time', 'proposed_end_time', 'proposed_purpose', 'proposed_attendees',
        'edit_reason'
    ];

    protected $casts = [
        'is_full_day' => 'boolean',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(MeetingRoom::class, 'room_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'booked_by_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function proposedRoom(): BelongsTo
    {
        return $this->belongsTo(MeetingRoom::class, 'proposed_room_id');
    }
}
