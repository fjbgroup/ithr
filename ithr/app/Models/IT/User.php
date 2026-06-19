<?php

namespace App\Models\IT;

use App\Models\User as BaseUser;

class User extends BaseUser
{
    // Inherits: $table (users), $fillable, $hidden, casts(), all role helpers,
    // full_name/username/user_id accessors, and HR relationships from BaseUser.

    // ── IT-specific: staff_id alias maps to staff_no ───────────────────────

    public function getStaffIdAttribute(): ?string
    {
        return $this->attributes['staff_no'] ?? null;
    }

    public function setStaffIdAttribute(string $value): void
    {
        $this->attributes['staff_no'] = $value;
    }

    // ── IT role helpers (override base model to use it_role) ─────────────

    public function isAdmin(): bool          { return in_array($this->it_role, ['admin_it', 'admin']); }
    public function isAdminOrFinance(): bool { return in_array($this->it_role, ['admin_it', 'admin', 'finance_admin']); }

    // ── IT-specific relationships ─────────────────────────────────────────

    public function inventoryItems()        { return $this->hasMany(InventoryItem::class, 'created_by'); }
    public function ewasteItems()           { return $this->hasMany(EwasteItem::class, 'created_by'); }
    public function activityLogs()          { return $this->hasMany(ActivityLog::class, 'user_id'); }
    public function userNotifications()     { return $this->hasMany(Notification::class, 'user_id'); }
    public function addAssetRequests()      { return $this->hasMany(AddAssetRequest::class, 'requested_by'); }
    public function deleteRequests()        { return $this->hasMany(DeleteRequest::class, 'requested_by'); }
    public function editAssetRequests()     { return $this->hasMany(EditAssetRequest::class, 'requested_by'); }
    public function passwordResetRequests() { return $this->hasMany(PasswordResetRequest::class, 'user_id'); }
}
