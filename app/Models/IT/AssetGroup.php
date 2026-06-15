<?php

namespace App\Models\IT;

use Illuminate\Database\Eloquent\Model;

class AssetGroup extends Model
{
    protected $connection = 'it_mysql';
    public $timestamps = false;
    protected $fillable = ['name', 'sort_order'];

    public function assetClasses() { return $this->hasMany(AssetClass::class, 'group_id'); }
}

