<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsQuizQuestion extends Model
{
    protected $fillable = ['material_id', 'question', 'options', 'correct_answer'];

    protected $casts = [
        'options' => 'array',
    ];

    public function material()
    {
        return $this->belongsTo(LmsMaterial::class, 'material_id');
    }
}
