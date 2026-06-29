<?php

namespace App\Models\IT;

use Illuminate\Database\Eloquent\Model;

class DeleteRequest extends Model
{
    protected $fillable = [
        'inventory_id', 'non_it_id', 'requested_by', 'reason',
        'asset_number', 'asset_class', 'asset_description',
        'status', 'reviewed_by', 'reviewed_at',
    ];

    public function inventoryItem() { return $this->belongsTo(InventoryItem::class, 'inventory_id'); }
    public function nonItAsset()    { return $this->belongsTo(NonItAsset::class, 'non_it_id'); }
    public function requester()     { return $this->belongsTo(User::class, 'requested_by'); }
    public function reviewer()      { return $this->belongsTo(User::class, 'reviewed_by'); }
}

