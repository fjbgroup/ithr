<?php

namespace App\Models\WT;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Handover extends Model
{
    use HasFactory;

    protected $table = 'walkie_talkie_handovers';

    protected $fillable = [
        'access_request_id',
        'user_id',
        'radio_id',
        'walkie_talkie_id',
        'staff_name',
        'shared_with',
        'staff_no',
        'position',
        'department',
        'notes',
        'pickup_recipient_name',
        'pickup_recipient_signature',
        'pickup_recipient_signed_at',
        'handover_by_name',
        'handover_by_signature',
        'handover_by_signed_at',
        'checked_by_name',
        'checked_by_signature',
        'checked_by_signed_at',
        'accessories_snapshot',
        'policy_accepted_at',
        'pickup_completed_at',
        'issued_at',
        'returned_at'
    ];

    protected $casts = [
        'pickup_recipient_signed_at' => 'datetime',
        'handover_by_signed_at' => 'datetime',
        'checked_by_signed_at' => 'datetime',
        'policy_accepted_at' => 'datetime',
        'pickup_completed_at' => 'datetime',
        'issued_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function walkieTalkie()
    {
        return $this->belongsTo(WalkieTalkie::class, 'radio_id', 'radio_id');
    }

    public function accessRequest()
    {
        return $this->belongsTo(AccessRequest::class, 'access_request_id');
    }
}
