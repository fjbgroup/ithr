<?php

namespace App\Models\IT;

use Illuminate\Database\Eloquent\Model;

class DisposalItem extends Model
{
    protected $connection = 'it_mysql';
    protected $fillable = [
        'asset_number', 'asset_class', 'description', 'serial_number',
        'disposal_status', 'disposal_method', 'vendor_collector', 'certificate_number',
        'notes', 'date_flagged', 'date_disposed', 'created_by',
    ];

    protected $casts = [
        'date_flagged'  => 'date',
        'date_disposed' => 'date',
    ];

    public function creator() { return $this->belongsTo(\App\Models\User::class, 'created_by'); }
}

