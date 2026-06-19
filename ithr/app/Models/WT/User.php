<?php

namespace App\Models\WT;

use App\Models\User as BaseUser;

class User extends BaseUser
{
    // Inherits: $table (users), $fillable, $hidden, casts(), all role helpers,
    // full_name/username/user_id accessors, and HR relationships from BaseUser.

    // ── WT-specific: staff_id alias maps to staff_no ───────────────────────

    public function getStaffIdAttribute(): ?string
    {
        return $this->attributes['staff_no'] ?? null;
    }

    public function setStaffIdAttribute(string $value): void
    {
        $this->attributes['staff_no'] = $value;
    }

    // WT stored department as a plain string — maps to dept_name column
    public function getDepartmentAttribute(): ?string
    {
        return $this->attributes['dept_name'] ?? null;
    }

    public function setDepartmentAttribute(string $value): void
    {
        $this->attributes['dept_name'] = $value;
    }

    // ── WT-specific notifications (overrides base HR notifications) ─────────

    public function notifications()
    {
        return $this->morphMany(WtDatabaseNotification::class, 'notifiable')->latest();
    }

    public function readNotifications()
    {
        return $this->notifications()->whereNotNull('read_at');
    }

    public function unreadNotifications()
    {
        return $this->notifications()->whereNull('read_at');
    }
}
