<?php

namespace App\Models\WT;

use Illuminate\Database\Eloquent\Model;

class MasterData extends Model
{
    protected $table = 'wt_master_data';

    protected $fillable = [
        'category',
        'value',
    ];

    /**
     * Categories managed by the WT master data module and their human labels.
     */
    public const CATEGORIES = [
        'model' => 'Model',
        'department' => 'Location',
        'position' => 'Position',
        'ownership_type' => 'Ownership Type',
    ];

    private const BLOCKED_VALUES = [
        'position' => ['JAILANI'],
    ];

    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeVisible($query)
    {
        foreach (self::BLOCKED_VALUES as $category => $values) {
            $query->where(function ($query) use ($category, $values) {
                $query->where('category', '!=', $category)
                    ->orWhereNotIn('value', $values);
            });
        }

        return $query;
    }

    public static function isBlockedValue(string $category, mixed $value): bool
    {
        $normalized = strtoupper(trim((string) $value));

        return in_array($normalized, self::BLOCKED_VALUES[$category] ?? [], true);
    }

    /**
     * Curated values for a category, as an ordered collection of strings.
     */
    public static function valuesFor(string $category)
    {
        return static::query()
            ->where('category', $category)
            ->visible()
            ->orderBy('value')
            ->pluck('value');
    }
}
