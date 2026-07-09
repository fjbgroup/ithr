<?php

namespace App\Models\IT;

use Illuminate\Database\Eloquent\Model;

class EmailSetting extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'setting_key';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['setting_key', 'setting_value'];

    /** Setting key for the global "all email sending" master switch. */
    public const KEY_EMAIL_ENABLED = 'email_enabled';

    /** Setting key for the global "Microsoft Authenticator (2FA)" master switch. */
    public const KEY_TOTP_ENABLED = 'totp_enabled';

    /** Setting key for the global "Require Staff Registry for New Accounts" switch. */
    public const KEY_REQUIRE_STAFF_REGISTRY = 'require_staff_registry';

    public static function get(string $key, string $default = ''): string
    {
        return static::find($key)?->setting_value ?? $default;
    }

    /**
     * Whether the system is allowed to send outgoing email.
     * Defaults to ON. Fails open (returns true) if the settings table
     * is unreachable so a transient DB issue can't silently disable mail.
     */
    public static function emailEnabled(): bool
    {
        try {
            return static::get(self::KEY_EMAIL_ENABLED, '1') === '1';
        } catch (\Throwable $e) {
            return true;
        }
    }

    /** Flip the global email master switch on/off. */
    public static function setEmailEnabled(bool $on): void
    {
        static::updateOrCreate(
            ['setting_key' => self::KEY_EMAIL_ENABLED],
            ['setting_value' => $on ? '1' : '0']
        );
    }

    /**
     * Whether Microsoft Authenticator (TOTP / 2FA) is active system-wide.
     * Defaults to ON. Fails open (returns true) if the settings table is
     * unreachable so a transient DB issue can't silently weaken security.
     */
    public static function totpEnabled(): bool
    {
        try {
            return static::get(self::KEY_TOTP_ENABLED, '1') === '1';
        } catch (\Throwable $e) {
            return true;
        }
    }

    /** Flip the global Microsoft Authenticator (2FA) master switch on/off. */
    public static function setTotpEnabled(bool $on): void
    {
        static::updateOrCreate(
            ['setting_key' => self::KEY_TOTP_ENABLED],
            ['setting_value' => $on ? '1' : '0']
        );
    }

    /**
     * Whether new user accounts require a valid staff registry record.
     */
    public static function requireStaffRegistry(): bool
    {
        try {
            return static::get(self::KEY_REQUIRE_STAFF_REGISTRY, '0') === '1';
        } catch (\Throwable $e) {
            return false;
        }
    }

    /** Flip the global require staff registry master switch on/off. */
    public static function setRequireStaffRegistry(bool $on): void
    {
        static::updateOrCreate(
            ['setting_key' => self::KEY_REQUIRE_STAFF_REGISTRY],
            ['setting_value' => $on ? '1' : '0']
        );
    }

    public static function all_settings(): array
    {
        return static::all()->pluck('setting_value', 'setting_key')->toArray();
    }
}

