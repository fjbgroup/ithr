<?php

namespace App\Models\WT;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
    protected $connection = 'wt_mysql';
    protected $primaryKey = 'user_id';
    public $timestamps = false;

    protected $fillable = [
        'staff_id',
        'username',
        'full_name',
        'department',
        'position',
        'phone_no',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password'  => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return $this->attributes['full_name'] ?? '';
    }
}
