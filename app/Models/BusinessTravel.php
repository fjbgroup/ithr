<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessTravel extends Model
{
    protected $table = 'business_travel';

    protected $fillable = [
        'staff_id', 'travel_type', 'destination', 'purpose', 'departure_date', 'return_date',
        'transport', 'notes', 'created_by'
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
