<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TrainingCourse extends Model
{
    protected $fillable = ['code', 'title', 'training_type', 'company', 'department', 'start_date', 'end_date', 'venue', 'duration', 'is_private', 'platform', 'pic_id'];

    protected $casts = ['is_private' => 'boolean'];

    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class, 'training_attendances', 'course_id', 'staff_id');
    }

    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_id');
    }

    public function materials()
    {
        return $this->hasMany(LmsMaterial::class, 'course_id')->orderBy('order');
    }

    public function progress()
    {
        return $this->hasMany(LmsProgress::class, 'course_id');
    }

    public function scopeOnline($query)
    {
        return $query->where('platform', 'LMS');
    }

    public function scopePhysical($query)
    {
        return $query->where('platform', 'HR');
    }
}
