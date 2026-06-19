<?php

namespace App\Models\IT;

use Illuminate\Database\Eloquent\Model;

class EwasteItem extends Model
{
    protected $connection = 'it_mysql';
    protected $fillable = [
        'asset_number', 'asset_class', 'description', 'serial_number', 'brand', 'model',
        'original_inventory_id', 'condition_on_disposal', 'disposal_status',
        'date_flagged', 'date_disposed', 'disposal_method', 'weight_kg',
        'vendor_collector', 'certificate_number', 'asset_source', 'batch_id',
        'writeoff_name', 'writeoff_designation', 'writeoff_date', 'writeoff_signature', 'writeoff_sig_img',
        'checked_by_user_id', 'hou_status', 'hou_signed_name', 'hou_signed_at', 'hou_remark', 'hou_sig_img',
        'gm1_user_id', 'gm2_user_id', 'current_gm_user_id', 'gm_assigned_at',
        'gm_status', 'gm_signed_name', 'gm_signed_at', 'gm_remark', 'gm_sig_img',
        'ceo_user_id', 'ceo_status', 'ceo_signed_name', 'ceo_signed_at', 'ceo_remark', 'ceo_sig_img',
        'finance_status', 'notes', 'created_by',
    ];

    protected $casts = [
        'date_flagged'   => 'date',
        'date_disposed'  => 'date',
        'writeoff_date'  => 'date',
        'hou_signed_at'  => 'datetime',
        'gm_assigned_at' => 'datetime',
        'gm_signed_at'   => 'datetime',
        'ceo_signed_at'  => 'datetime',
    ];

    public function inventoryItem() { return $this->belongsTo(InventoryItem::class, 'original_inventory_id'); }
    public function creator()       { return $this->belongsTo(User::class, 'created_by'); }
    public function houUser()       { return $this->belongsTo(User::class, 'checked_by_user_id'); }
    public function gm1User()       { return $this->belongsTo(User::class, 'gm1_user_id'); }
    public function gm2User()       { return $this->belongsTo(User::class, 'gm2_user_id'); }
    public function currentGmUser() { return $this->belongsTo(User::class, 'current_gm_user_id'); }
    public function ceoUser()       { return $this->belongsTo(User::class, 'ceo_user_id'); }
}

