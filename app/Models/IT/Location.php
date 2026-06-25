<?php

namespace App\Models\IT;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    public $timestamps = false;
    protected $fillable = ['name', 'sort_order'];
}
