<?php

namespace App\Models\IT;

use Illuminate\Database\Eloquent\Model;

class NonItAsset extends Model
{
    protected $fillable = [
        'asset_number', 'asset_class', 'description', 'serial_number', 'brand', 'model',
        'location', 'item_status', 'condition_status', 'notes', 'created_by',
        'date_registered', 'fa_code', 'years_purchase', 'total_cost', 'accumulated', 'nbv_at', 'warranty_date',
    ];

    protected $casts = [
        'date_registered' => 'date',
        'warranty_date'   => 'date',
        'total_cost'      => 'decimal:2',
        'accumulated'     => 'decimal:2',
        'nbv_at'          => 'decimal:2',
    ];

    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}

