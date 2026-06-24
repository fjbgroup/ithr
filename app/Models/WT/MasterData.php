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
        'department' => 'Department',
        'position' => 'Position',
        'ownership_type' => 'Ownership Type',
    ];

    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Curated values for a category, as an ordered collection of strings.
     */
    public static function valuesFor(string $category)
    {
        return static::query()
            ->where('category', $category)
            ->orderBy('value')
            ->pluck('value');
    }
}
