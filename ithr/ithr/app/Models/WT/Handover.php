<?php

namespace App\Models\WT;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Handover extends Model
{
    use HasFactory;

    protected $table = 'walkie_talkie_handovers';
    protected $connection = 'wt_mysql';

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
        'issued_at',
        'returned_at'
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

