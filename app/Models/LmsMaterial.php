<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsMaterial extends Model
{
    protected $fillable = ['course_id', 'title', 'type', 'file_path', 'content', 'order'];

    public function course()
    {
        return $this->belongsTo(TrainingCourse::class, 'course_id');
    }

    public function questions()
    {
        return $this->hasMany(LmsQuizQuestion::class, 'material_id');
    }

    public function progress()
    {
        return $this->hasMany(LmsProgress::class, 'material_id');
    }
}
