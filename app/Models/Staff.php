<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class Staff extends Model
{
    protected $table = 'staff';

    protected $fillable = [
        'staff_no', 'name', 'position', 'department_id', 'company', 'company_id', 'email',
        'date_joined', 'date_of_birth', 'ic_number', 'employment_status', 'last_promotion_date', 'gender', 'location',
        'compensation_grade', 'management_level', 'job_level',
        'job_category', 'is_active', 'phone_number'
    ];

    /**
     * Get the staff's age.
     */
    public function getAgeAttribute(): ?int
    {
        if (!$this->date_of_birth) {
            return null;
        }
        return Carbon::parse($this->date_of_birth)->age;
    }

    /**
     * Get the years of service in 'Xy Ym' format.
     */
    public function getYosAttribute(): ?string
    {
        if (!$this->date_joined) {
            return null;
        }
        $joined = Carbon::parse($this->date_joined);
        $now = now();
        
        if ($joined->isFuture()) {
            return '0Years';
        }
        
        $diff = $joined->diff($now);
        $years = $diff->y;
        
        if ($years == 0) {
            return '< 1Year';
        }
        
        return $years . 'Years';
    }

    /**
     * Get the balance until retirement at 60.
     */
    public function getBalanceUntilRetireAttribute(): ?int
    {
        $age = $this->age;
        if ($age === null) {
            return null;
        }
        return max(0, 60 - $age);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function familyMembers(): HasMany
    {
        return $this->hasMany(FamilyMember::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(TrainingCourse::class, 'training_attendances', 'staff_id', 'course_id')
                    ->withPivot('id', 'status', 'training_type');
    }

    public function irRecords(): HasMany
    {
        return $this->hasMany(StaffIr::class);
    }

    public function travelRecords(): HasMany
    {
        return $this->hasMany(BusinessTravel::class);
    }
}
