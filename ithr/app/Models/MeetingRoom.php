<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MeetingRoom extends Model
{
    protected $fillable = ['name', 'description', 'capacity', 'color_class'];

    public function bookings(): HasMany
    {
        return $this->hasMany(RoomBooking::class, 'room_id');
    }

    public function pics(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'room_pics', 'room_id', 'user_id')
                    ->withPivot('level', 'added_by')
                    ->withTimestamps()
                    ->orderBy('level');
    }
}
