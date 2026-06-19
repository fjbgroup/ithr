<?php

namespace App\Models\IT;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $connection = 'it_mysql';

    protected $fillable = [
        'username', 'password', 'full_name', 'email', 'role',
        'department', 'dept_name', 'staff_id', 'avatar', 'signature_img',
        'is_active', 'must_change_password', 'last_login',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_active'            => 'boolean',
        'must_change_password' => 'boolean',
        'last_login'           => 'datetime',
    ];

    // fjb_unified role enum: admin_it, admin_hr, finance_admin, hou, gm, ceo, staff
    // Map 'admin' → 'admin_it', 'user' → 'staff' for IT module
    public function isAdmin(): bool          { return in_array($this->role, ['admin_it', 'admin']); }
    public function isFinanceAdmin(): bool   { return $this->role === 'finance_admin'; }
    public function isAdminOrFinance(): bool { return in_array($this->role, ['admin_it', 'admin', 'finance_admin']); }
    public function isHOU(): bool            { return $this->role === 'hou'; }
    public function isGM(): bool             { return $this->role === 'gm'; }
    public function isCEO(): bool            { return $this->role === 'ceo'; }
    public function isSignatory(): bool      { return in_array($this->role, ['hou', 'gm']); }
    public function isReadOnlyViewer(): bool { return in_array($this->role, ['hou', 'gm', 'ceo']); }
    public function canApproveWriteOff(): bool { return in_array($this->role, ['admin_it', 'admin', 'ceo']); }

    public function roleName(): string
    {
        return [
            'admin_it'      => 'IT Admin',
            'admin'         => 'IT Admin',
            'admin_hr'      => 'HR Admin',
            'finance_admin' => 'Finance Admin',
            'hou'           => 'Head of Unit',
            'gm'            => 'General Manager',
            'ceo'           => 'CEO',
            'staff'         => 'Staff',
            'user'          => 'Staff',
        ][$this->role] ?? ucfirst($this->role);
    }

    public function inventoryItems()        { return $this->hasMany(InventoryItem::class, 'created_by'); }
    public function ewasteItems()           { return $this->hasMany(EwasteItem::class, 'created_by'); }
    public function activityLogs()          { return $this->hasMany(ActivityLog::class, 'user_id'); }
    public function userNotifications()     { return $this->hasMany(Notification::class, 'user_id'); }
    public function addAssetRequests()      { return $this->hasMany(AddAssetRequest::class, 'requested_by'); }
    public function deleteRequests()        { return $this->hasMany(DeleteRequest::class, 'requested_by'); }
    public function editAssetRequests()     { return $this->hasMany(EditAssetRequest::class, 'requested_by'); }
    public function passwordResetRequests() { return $this->hasMany(PasswordResetRequest::class, 'user_id'); }
}
