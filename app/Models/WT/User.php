<?php

namespace App\Models\WT;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
    protected $connection = 'wt_mysql';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'staff_id',
        'username',
        'name',
        'department',
        'department_id',
        'position',
        'phone_no',
        'password',
        'role',
        'is_active',
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

    // WT views/controllers use ->full_name — map to 'name'
    public function getFullNameAttribute(): string
    {
        return $this->name ?? '';
    }

    public function setFullNameAttribute(string $value): void
    {
        $this->attributes['name'] = $value;
    }
}
