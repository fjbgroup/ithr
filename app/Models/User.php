<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use App\Models\RoomBooking;
use App\Models\UpdateRequest;
use App\Models\SystemSetting;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'staff_no',
        'role',
        'department_id',
        'position',
        'company',
        'is_active',
        'staff_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(RoomBooking::class, 'booked_by_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function isAdminIT(): bool
    {
        return $this->role === 'admin_it';
    }

    public function isAdminHR(): bool
    {
        return $this->role === 'admin_hr';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin_it', 'admin_hr']);
    }

    public function isCeo(): bool
    {
        return $this->role === 'ceo';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isModuleEnabled(string $module): bool
    {
        // Admin IT and CEO always see all modules
        if ($this->isAdminIT() || $this->isCeo()) return true;

        try {
            $setting = SystemSetting::where('setting_key', 'module_' . $module)->first();
            return $setting ? (bool)$setting->setting_value : true;
        } catch (\Exception $e) {
            return true;
        }
    }

    public function getRoleLabel(): string
    {
        return match($this->role) {
            'admin_it' => 'Admin (IT)',
            'admin_hr' => 'Admin (HR)',
            'staff'    => 'Staff',
            'ceo'      => 'CEO',
            default    => ucfirst($this->role),
        };
    }

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

        // Check if user is PIC for any room
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
