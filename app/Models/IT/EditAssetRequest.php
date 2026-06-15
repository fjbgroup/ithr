<?php

namespace App\Models\IT;

use Illuminate\Database\Eloquent\Model;

class EditAssetRequest extends Model
{
    protected $connection = 'it_mysql';
    public $timestamps = false;
    protected $fillable = [
        'asset_type', 'asset_id', 'requested_by', 'status',
        'asset_number', 'asset_class', 'fa_code', 'description', 'serial_number',
        'brand', 'model', 'location', 'condition_status', 'purchase_date',
        'purchase_price', 'years_purchase', 'total_cost', 'accumulated', 'nbv_at',
        'notes', 'reviewed_by', 'reviewed_at',
    ];

    public function requester()    { return $this->belongsTo(User::class, 'requested_by'); }
    public function reviewer()     { return $this->belongsTo(User::class, 'reviewed_by'); }
    public function inventoryItem(){ return $this->belongsTo(InventoryItem::class, 'asset_id'); }
}

