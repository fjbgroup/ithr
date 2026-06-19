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

    public static function get(string $key, string $default = ''): string
    {
        return static::find($key)?->setting_value ?? $default;
    }

    public static function all_settings(): array
    {
        return static::all()->pluck('setting_value', 'setting_key')->toArray();
    }
}

