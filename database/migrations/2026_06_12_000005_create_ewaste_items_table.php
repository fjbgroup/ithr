<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ewaste_items', function (Blueprint $table) {
            $table->id();
            $table->string('asset_number')->nullable()->index();
            $table->string('asset_class')->nullable();
            $table->string('description')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->unsignedBigInteger('original_inventory_id')->nullable()->index();
            $table->string('condition_on_disposal')->nullable();
            $table->string('disposal_status')->nullable()->index();
            $table->date('date_flagged')->nullable();
            $table->date('date_disposed')->nullable();
            $table->string('disposal_method')->nullable();
            $table->decimal('weight_kg', 10, 2)->nullable();
            $table->string('vendor_collector')->nullable();
            $table->string('certificate_number')->nullable();
            $table->string('asset_source')->nullable();
            $table->string('batch_id')->nullable();
            $table->string('writeoff_name')->nullable();
            $table->string('writeoff_designation')->nullable();
            $table->date('writeoff_date')->nullable();
            $table->text('writeoff_signature')->nullable();
            $table->string('writeoff_sig_img')->nullable();
            $table->unsignedBigInteger('checked_by_user_id')->nullable();
            $table->string('hou_status')->nullable();
            $table->string('hou_signed_name')->nullable();
            $table->timestamp('hou_signed_at')->nullable();
            $table->text('hou_remark')->nullable();
            $table->string('hou_sig_img')->nullable();
            $table->unsignedBigInteger('gm1_user_id')->nullable();
            $table->unsignedBigInteger('gm2_user_id')->nullable();
            $table->unsignedBigInteger('current_gm_user_id')->nullable();
            $table->timestamp('gm_assigned_at')->nullable();
            $table->string('gm_status')->nullable();
            $table->string('gm_signed_name')->nullable();
            $table->timestamp('gm_signed_at')->nullable();
            $table->text('gm_remark')->nullable();
            $table->string('gm_sig_img')->nullable();
            $table->unsignedBigInteger('ceo_user_id')->nullable();
            $table->string('ceo_status')->nullable();
            $table->string('ceo_signed_name')->nullable();
            $table->timestamp('ceo_signed_at')->nullable();
            $table->text('ceo_remark')->nullable();
            $table->string('ceo_sig_img')->nullable();
            $table->string('finance_status')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ewaste_items');
    }
};
