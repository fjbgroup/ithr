<?php

namespace App\Models\WT;

use Illuminate\Database\Eloquent\Model;

class AccessRequest extends Model
{
    protected $table = 'access_requests';
    
    // Legacy schema uses created_at only (no updated_at column).
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'request_type',
        'accessory_request_mode',
        'replacement_return_note',
        'radio_id',
        'walkie_inventory_id',
        'assigned_walkie_inventory_ids',
        'assigned_radio_ids',
        'assigned_serial_number',
        'assigned_serial_numbers',
        'full_name',
        'staff_id',
        'request_date',
        'end_date',
        'department',
        'position',
        'ownership_type',
        'shared_with',
        'bay_from',
        'accessories',
        'submit_to_admin_id',
        'handled_by',
        'sector',
        'location',
        'event_name',
        'quantity',
        'duration_days',
        'pic_details',
        'pickup_method',
        'pickup_representative_name',
        'requested_pickup_at',
        'pickup_note',
        'justifications',
        'status',
        'approval_remark',
        'return_date',
        'return_person',
        'return_department',
        'return_phone_no',
        'return_status',
    ];

    protected $casts = [
        'assigned_walkie_inventory_ids' => 'array',
        'assigned_radio_ids' => 'array',
        'assigned_serial_numbers' => 'array',
        'pic_details' => 'array',
        'requested_pickup_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function walkieTalkie()
    {
        return $this->belongsTo(WalkieTalkie::class, 'walkie_inventory_id', 'walkie_id');
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by', 'id');
    }

    public function submitToAdmin()
    {
        return $this->belongsTo(User::class, 'submit_to_admin_id', 'id');
    }

    public function handover()
    {
        return $this->hasOne(Handover::class, 'access_request_id');
    }
}

