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
        'reviewed_by', 'reviewed_at', 'approval_remarks',
    ];

    protected $casts = [
        'hw_items'       => 'array',
        'sys_items'      => 'array',
        'svc_items'      => 'array',
        'exit_join_date' => 'date',
        'reviewed_at'    => 'datetime',
    ];

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by', 'id');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by', 'id');
    }
}

