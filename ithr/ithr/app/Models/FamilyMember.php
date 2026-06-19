<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyMember extends Model
{
    protected $fillable = [
        'staff_id', 'family_member_name', 'relationship', 'date_of_birth', 'emergency_contact',
        'phone_number', 'nric_no', 'dependent_id', 'gender', 'city_of_birth', 'country_of_birth',
        'nationality', 'citizenship_status', 'use_employee_address', 'use_employee_phone',
        'phone_country_code', 'phone_device_type', 'region_of_birth',
        'is_fulltime_student', 'student_start_date', 'student_end_date', 'occupation',
        'occupation_effective_date', 'is_disabled', 'is_terminated', 'effective_date',
        'company_code', 'company_name', 'region_name', 'created_by',
    ];

    protected $appends = ['display_name'];

    public function getDisplayNameAttribute()
    {
        return $this->family_member_name ?: 'Unknown';
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }
}
