<?php

namespace App\Models\WT;

use Illuminate\Database\Eloquent\Model;

class WalkieTalkie extends Model
{
    protected $table = 'walkie_talkies';
    protected $primaryKey = 'walkie_id';
    public $timestamps = false; // Only has created_at

    protected $fillable = [
        'radio_id',
        'model',
        'serial_number',
        'status',
        'ownership_type',
        'shared_with',
        'ownership',
        'position',
        'department',
        'location',
        'executive',
        'temporary_radio_id',
        'remark',
        'tracking_ref',
        'need_to_change_id',
        'id_change_done',
        'ownership_type_to_be',
        'is_special_use',
        'special_use_returned',
        'wt_warranty_start_date',
        'wt_warranty_end_date',
        'battery_warranty_start_date',
        'battery_warranty_end_date',
        'created_at'
    ];

    public function maintenanceRecords()
    {
        return $this->hasMany(MaintenanceRecord::class, 'walkie_id', 'walkie_id');
    }
}
