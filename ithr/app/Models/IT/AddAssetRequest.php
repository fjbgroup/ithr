<?php

namespace App\Models\IT;

use Illuminate\Database\Eloquent\Model;

class AddAssetRequest extends Model
{
    protected $connection = 'it_mysql';
    public $timestamps = false;
    protected $fillable = [
        'requested_by', 'asset_number', 'asset_class', 'description',
        'serial_number', 'brand', 'model', 'location', 'notes', 'status',
        'reviewed_by', 'reviewed_at',
    ];

    public function requester()  { return $this->belongsTo(User::class, 'requested_by'); }
    public function reviewer()   { return $this->belongsTo(User::class, 'reviewed_by'); }
}

