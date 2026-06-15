<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingFeedback extends Model
{
    protected $table = 'training_feedbacks';

    protected $fillable = [
        'attendance_id', 'staff_id', 'course_id',
        'content_rating', 'trainer_rating', 'venue_rating', 'overall_rating',
        'would_recommend', 'comments',
    ];

    protected $casts = ['would_recommend' => 'boolean'];

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(TrainingAttendance::class, 'attendance_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(TrainingCourse::class, 'course_id');
    }
}
