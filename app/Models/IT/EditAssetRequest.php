<?php

namespace App\Models\IT;

use Illuminate\Database\Eloquent\Model;

class EditAssetRequest extends Model
{
    protected $fillable = [
        'asset_type', 'asset_id', 'requested_by', 'status',
        'asset_number', 'asset_class', 'fa_code', 'description', 'serial_number',
        'brand', 'model', 'location', 'condition_status', 'purchase_date',
        'purchase_price', 'years_purchase', 'total_cost', 'accumulated', 'nbv_at',
        'notes', 'reviewed_by', 'reviewed_at',

        'domain', 'ip_address', 'os_code', 'sp', 'asset_type', 'asset_name',
        'fqdn', 'mac_address', 'memory_mb', 'nr_processors', 'processor',
        'state', 'last_patched', 'last_full_backup', 'last_full_image',
        'order_number', 'comments', 'building', 'department', 'branch_office',
        'bar_code', 'manufacturer', 'contact', 'scan_server', 'chrome_os_device_id',
        'system_sku', 'purchase_date', 'warranty_date',
    ];

    public function requester()    { return $this->belongsTo(User::class, 'requested_by'); }
    public function reviewer()     { return $this->belongsTo(User::class, 'reviewed_by'); }
    public function inventoryItem(){ return $this->belongsTo(InventoryItem::class, 'asset_id'); }
}

