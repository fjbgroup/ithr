<?php

namespace App\Models\IT;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = [
        'asset_number', 'asset_class', 'description', 'serial_number',
        'brand', 'model', 'location', 'condition_status', 'item_status',
        'purchase_date', 'purchase_price', 'notes', 'created_by',
        'fa_code', 'years_purchase', 'total_cost', 'accumulated', 'nbv_at', 'warranty_date',
    ];

    protected $casts = [
        'purchase_date'  => 'date',
        'warranty_date'  => 'date',
        'purchase_price' => 'decimal:2',
        'total_cost'     => 'decimal:2',
        'accumulated'    => 'decimal:2',
        'nbv_at'         => 'decimal:2',
    ];

    public function creator()        { return $this->belongsTo(User::class, 'created_by'); }
    public function ewasteItems()    { return $this->hasMany(EwasteItem::class, 'original_inventory_id'); }
    public function deleteRequests() { return $this->hasMany(DeleteRequest::class, 'inventory_id'); }
    public function editRequests()   { return $this->hasMany(EditAssetRequest::class, 'asset_id'); }
    public function ewasteRequests() { return $this->hasMany(EwasteRequest::class, 'inventory_id'); }
}

