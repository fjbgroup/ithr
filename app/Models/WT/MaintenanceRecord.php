<?php

namespace App\Models\WT;

use Illuminate\Database\Eloquent\Model;

class MaintenanceRecord extends Model
{
    protected $table = 'maintenance_records';
    protected $primaryKey = 'maintenance_id';
    public $timestamps = false; // No timestamps on this table

    protected $fillable = [
        'walkie_id',
        'temporary_spare_walkie_id',
        'temporary_spare_requested',
        'temporary_spare_request_note',
        'temporary_spare_assigned_at',
        'temporary_spare_returned_at',
        'original_returned_at',
        'original_returned_by',
        'radio_id',
        'serial_number',
        'model',
        'current_ownership',
        'department_name',
        'received_date',
        'ict_received_at',
        'ict_received_by',
        'repair_date',
        'done',
        'finish_date',
        'issue_description',
        'issue',
        'remarks',
        'maintenance_date',
        'status',
        'request_source',
        'submit_to_admin_id',
        'handled_by',
        'reporter_name',
        'reporter_staff_id',
        'designation',
        'phone_no',
        'handover_person',
        'handover_at',
        'pickup_person',
        'pickup_at',
        'ownership_type',
        'shared_with',
        'location',
        'problem_possible',
        'evidence_paths',
    ];

    protected $casts = [
        'evidence_paths' => 'array',
        'temporary_spare_requested' => 'boolean',
        'handover_at' => 'datetime',
        'pickup_at' => 'datetime',
    ];

    public function walkieTalkie()
    {
        return $this->belongsTo(WalkieTalkie::class, 'walkie_id', 'walkie_id');
    }

    public function temporarySpareWalkie()
    {
        return $this->belongsTo(WalkieTalkie::class, 'temporary_spare_walkie_id', 'walkie_id');
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by', 'id');
    }

    public function submitToAdmin()
    {
        return $this->belongsTo(User::class, 'submit_to_admin_id', 'id');
    }
}
