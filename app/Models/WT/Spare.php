<?php

namespace App\Models\WT;

use Illuminate\Database\Eloquent\Model;

class Spare extends Model
{
    protected $table = 'spare';
    protected $primaryKey = 'replacement_id';
    public $timestamps = false;

    protected $fillable = [
        'original_walkie_id',
        'original_radio_id',
        'spare_walkie_id',
        'spare_radio_id',
        'replacement_date',
        'return_date',
        'status',
    ];

    public function originalWalkieTalkie()
    {
        return $this->belongsTo(WalkieTalkie::class, 'original_walkie_id', 'walkie_id');
    }

    public function spareWalkieTalkie()
    {
        return $this->belongsTo(WalkieTalkie::class, 'spare_walkie_id', 'walkie_id');
    }
}

