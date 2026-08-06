<?php

namespace App\Models\IT;

use Illuminate\Database\Eloquent\Model;

class AddAssetRequest extends Model
{
    protected $fillable = [
        'requested_by', 'asset_number', 'asset_class', 'description',
        'serial_number', 'brand', 'model', 'location', 'notes', 'status',
        'reviewed_by', 'reviewed_at',

        'domain', 'ip_address', 'os_code', 'sp', 'asset_type', 'asset_name',
        'fqdn', 'mac_address', 'memory_mb', 'nr_processors', 'processor',
        'state', 'last_patched', 'last_full_backup', 'last_full_image',
        'order_number', 'comments', 'building', 'department', 'branch_office',
        'bar_code', 'manufacturer', 'contact', 'scan_server', 'chrome_os_device_id',
        'system_sku', 'purchase_date', 'warranty_date',
    ];

    public function requester()  { return $this->belongsTo(User::class, 'requested_by'); }
    public function reviewer()   { return $this->belongsTo(User::class, 'reviewed_by'); }
}

