<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsProgress extends Model
{
    protected $table = 'lms_progress';

    protected $fillable = ['staff_id', 'course_id', 'material_id', 'is_completed', 'score'];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function course()
    {
        return $this->belongsTo(TrainingCourse::class, 'course_id');
    }

    public function material()
    {
        return $this->belongsTo(LmsMaterial::class, 'material_id');
    }
}
