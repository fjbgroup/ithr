<?php

namespace App\Models\IT;

use Illuminate\Database\Eloquent\Model;

class EwasteRequest extends Model
{
    protected $fillable = [
        'type', 'requested_by', 'inventory_id',
        'asset_number', 'asset_class', 'description', 'serial_number',
        'notes', 'status', 'reviewed_by', 'reviewed_at',
    ];

    public function requester()     { return $this->belongsTo(User::class, 'requested_by'); }
    public function reviewer()      { return $this->belongsTo(User::class, 'reviewed_by'); }
    public function inventoryItem() { return $this->belongsTo(InventoryItem::class, 'inventory_id'); }
}

