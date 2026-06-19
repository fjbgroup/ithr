<?php

namespace App\Models\WT;

use Illuminate\Database\Eloquent\Model;

class PasswordResetRequest extends Model
{
    protected $table = 'password_reset_requests';
    protected $connection = 'wt_mysql';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'staff_id',
        'requester_name',
        'requested_password',
        'justification',
        'status',
        'reviewed_by',
        'requested_at',
        'reviewed_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by', 'user_id');
    }
}

