<?php

namespace App\Models\IT;

use Illuminate\Database\Eloquent\Model;

class AssetClass extends Model
{
    public $timestamps = false;
    protected $fillable = ['name', 'type', 'sort_order', 'group_id'];

    public function group() { return $this->belongsTo(AssetGroup::class); }
}

