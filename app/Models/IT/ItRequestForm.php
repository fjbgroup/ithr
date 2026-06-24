<?php

namespace App\Models\IT;

use Illuminate\Database\Eloquent\Model;

class ItRequestForm extends Model
{
    protected $fillable = [
        'submitted_by', 'request_type', 'subject', 'status',
        'hw_request_type', 'hw_items', 'hw_pc_laptop_no', 'hw_printer_no',
        'sw_request_type', 'sw_software_name', 'sw_budgeted', 'sw_opex_capex', 'sw_cost_center', 'sw_expected_value',
        'sys_request_type', 'sys_items',
        'svc_items',
        'user_type', 'exit_join_date', 'justification', 'document_path',
        'user_name', 'user_email', 'user_address', 'user_department', 'user_designation', 'user_staff_id', 'user_contact',
        'req_name', 'req_department', 'req_staff_id', 'req_designation', 'req_contact', 'req_company',
        'approver_name', 'approver_department', 'approver_designation', 'approver_contact', 'approver_company',
        'hou_reviewed_by', 'hou_reviewed_at', 'hou_remarks',
        'reviewed_by', 'reviewed_at', 'approval_remarks',
        'validated_by', 'validated_at', 'validator_remarks',
        'cleared_by_submitter',
        'is_archived',
    ];

    protected $casts = [
        'submitted_by'    => 'integer',
        'reviewed_by'     => 'integer',
        'hou_reviewed_by' => 'integer',
        'hw_items'        => 'array',
        'sys_items'       => 'array',
        'svc_items'       => 'array',
        'exit_join_date'  => 'date',
        'reviewed_at'     => 'datetime',
        'hou_reviewed_at' => 'datetime',
        'validated_by'          => 'integer',
        'validated_at'          => 'datetime',
        'cleared_by_submitter'  => 'boolean',
        'is_archived'           => 'boolean',
    ];

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by', 'id');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by', 'id');
    }

    public function houReviewedBy()
    {
        return $this->belongsTo(User::class, 'hou_reviewed_by', 'id');
    }

    public function validatedBy()
    {
        return $this->belongsTo(User::class, 'validated_by', 'id');
    }
}

