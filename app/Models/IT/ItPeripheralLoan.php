<?php

namespace App\Models\IT;

use Illuminate\Database\Eloquent\Model;
use App\Models\Staff;

class ItPeripheralLoan extends Model
{
    protected $fillable = [
        'referral_code',
        'staff_id',
        'inventory_item_id',
        'status',
        'pickup_verified_at',
        'pickup_verified_by',
        'return_verified_at',
        'endorsed_at',
        'endorsed_by',
        'notes',
        'loan_start_date',
        'loan_end_date',
    ];

    protected $casts = [
        'pickup_verified_at' => 'datetime',
        'return_verified_at' => 'datetime',
        'endorsed_at' => 'datetime',
        'loan_start_date' => 'date',
        'loan_end_date' => 'date',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function pickupVerifiedBy()
    {
        return $this->belongsTo(User::class, 'pickup_verified_by');
    }

    public function endorsedBy()
    {
        return $this->belongsTo(User::class, 'endorsed_by');
    }
}
