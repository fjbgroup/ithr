<?php

namespace App\Models\IT;

use Illuminate\Database\Eloquent\Model;

class PasswordResetRequest extends Model
{
    protected $connection = 'it_mysql';
    public $timestamps = false;
    protected $fillable = ['user_id', 'username', 'full_name', 'staff_id', 'status', 'resolved_at', 'resolved_by'];

    public function user()     { return $this->belongsTo(User::class, 'user_id'); }
    public function resolver() { return $this->belongsTo(User::class, 'resolved_by'); }
}

