<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('edit_asset_requests', function (Blueprint $table) {
            $table->id();
            $table->string('asset_type')->nullable();
            $table->unsignedBigInteger('asset_id')->nullable()->index();
            $table->unsignedBigInteger('requested_by')->nullable()->index();
            $table->string('status')->default('Pending')->index();
            $table->string('asset_number')->nullable();
            $table->string('asset_class')->nullable();
            $table->string('fa_code')->nullable();
            $table->string('description')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('location')->nullable();
            $table->string('condition_status')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 15, 2)->nullable();
            $table->string('years_purchase')->nullable();
            $table->decimal('total_cost', 15, 2)->nullable();
            $table->decimal('accumulated', 15, 2)->nullable();
            $table->decimal('nbv_at', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('edit_asset_requests');
    }
};
