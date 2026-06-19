<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use App\Models\RoomBooking;
use App\Models\UpdateRequest;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
        'staff_no', 'role',
        'department_id', 'position', 'company',
        'is_active', 'staff_id',
        // Extended fields shared across all modules:
        'department', 'dept_name',
        'must_change_password', 'last_login',
        'avatar', 'signature_img', 'phone_no',
        // Backward-compat aliases — resolved via mutators, not stored directly:
        'username', 'full_name',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at'    => 'datetime',
            'password'             => 'hashed',
            'is_active'            => 'boolean',
            'must_change_password' => 'boolean',
            'last_login'           => 'datetime',
        ];
    }

    // ── Backward-compat accessors (IT/WT used different column names) ──────

    public function getFullNameAttribute(): string
    {
        return $this->attributes['name'] ?? '';
    }

    public function setFullNameAttribute(string $value): void
    {
        $this->attributes['name'] = $value;
    }

    public function getUsernameAttribute(): string
    {
        return $this->attributes['staff_no'] ?? '';
    }

    public function setUsernameAttribute(string $value): void
    {
        $this->attributes['staff_no'] = $value;
    }

    // Maps legacy user_id alias (WT module) to the primary key
    public function getUserIdAttribute(): mixed
    {
        return $this->attributes['id'] ?? null;
    }

    // ── Role helpers — covers all three modules ────────────────────────────

    public function isAdminIT(): bool        { return $this->role === 'admin_it'; }
    public function isAdminHR(): bool        { return $this->role === 'admin_hr'; }
    public function isAdmin(): bool          { return in_array($this->role, ['admin_it', 'admin_hr', 'admin']); }
    public function isCeo(): bool            { return $this->role === 'ceo'; }
    public function isStaff(): bool          { return $this->role === 'staff'; }
    public function isFinanceAdmin(): bool   { return $this->role === 'finance_admin'; }
    public function isHOU(): bool            { return $this->role === 'hou'; }
    public function isGM(): bool             { return $this->role === 'gm'; }
    public function isSignatory(): bool      { return in_array($this->role, ['hou', 'gm']); }
    public function isReadOnlyViewer(): bool { return in_array($this->role, ['hou', 'gm', 'ceo']); }
    public function isAdminOrFinance(): bool { return in_array($this->role, ['admin_it', 'admin', 'finance_admin']); }
    public function canApproveWriteOff(): bool { return in_array($this->role, ['admin_it', 'admin', 'ceo']); }
    public function canWrite(): bool         { return !$this->isCeo(); }

    public function getRoleLabel(): string
    {
        return match($this->role) {
            'admin_it'      => 'Admin (IT)',
            'admin_hr'      => 'Admin (HR)',
            'admin'         => 'IT Admin',
            'finance_admin' => 'Finance Admin',
            'hou'           => 'Head of Unit',
            'gm'            => 'General Manager',
            'staff'         => 'Staff',
            'ceo'           => 'CEO',
            default         => ucfirst($this->role),
        };
    }

    public function roleName(): string
    {
        return $this->getRoleLabel();
    }

    // ── HR relationships ──────────────────────────────────────────────────

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(RoomBooking::class, 'booked_by_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // ── HR notification helpers ───────────────────────────────────────────

    public function getUnreadBookingCount(): int
    {
        return $this->notifications()->where('type', 'booking')->where('is_read', false)->count();
    }

    public function getUnreadRequestCount(): int
    {
        return $this->notifications()->where('type', 'request')->where('is_read', false)->count();
    }

    public function getUnreadTravelCount(): int
    {
        return $this->notifications()->where('type', 'travel')->where('is_read', false)->count();
    }

    public function getPendingBookingCount(): int
    {
        if ($this->isAdminIT()) {
            return RoomBooking::whereIn('status', ['Pending', 'CancelRequested', 'EditRequested'])->count();
        }

        $myPicRoomIds = DB::table('room_pics')
            ->where('user_id', $this->id)
            ->pluck('room_id');

        if ($myPicRoomIds->isEmpty()) return 0;

        return RoomBooking::whereIn('room_id', $myPicRoomIds)
            ->whereIn('status', ['Pending', 'CancelRequested', 'EditRequested'])
            ->count();
    }

    public function getPendingRequestCount(): int
    {
        if (!$this->isAdmin()) return 0;
        return UpdateRequest::where('status', 'Pending')->count();
    }
}
