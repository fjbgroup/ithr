<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingAttendance extends Model
{
    protected $table = 'training_attendances';
    protected $fillable = ['staff_id', 'course_id', 'status', 'training_type', 'remarks', 'created_by', 'qr_token', 'qr_used_at'];

    protected $casts = ['qr_used_at' => 'datetime'];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(TrainingCourse::class, 'course_id');
    }
}
