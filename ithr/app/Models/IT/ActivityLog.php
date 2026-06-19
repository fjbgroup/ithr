<?php

namespace App\Models\IT;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public $timestamps = false;
    protected $table = 'it_activity_log';

    protected $fillable = ['user_id', 'action', 'item_type', 'item_id', 'description', 'ip_address'];

    public function user() { return $this->belongsTo(User::class); }
}

