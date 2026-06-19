<?php

namespace App\Models\IT;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    public $timestamps = false;
    protected $table = 'it_notifications';

    protected $fillable = ['user_id', 'type', 'title', 'message', 'link', 'is_read'];

    protected $casts = [
        'is_read'    => 'boolean',
        'created_at' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }
}

