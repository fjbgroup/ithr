<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UpdateRequest extends Model
{
    protected $fillable = [
        'requester_id', 
        'requester_name', 
        'record_type', 
        'record_id', 
        'record_reference', 
        'message', 
        'status', 
        'admin_note'
    ];

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }
}
