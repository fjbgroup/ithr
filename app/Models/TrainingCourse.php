<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TrainingCourse extends Model
{
    protected $fillable = ['code', 'title', 'training_type', 'company', 'start_date', 'end_date', 'venue', 'duration', 'is_private'];

    protected $casts = ['is_private' => 'boolean'];

    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class, 'training_attendances', 'course_id', 'staff_id');
    }
}
