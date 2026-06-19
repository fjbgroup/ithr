<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('asset_number')->nullable()->index();
            $table->string('asset_class')->nullable();
            $table->string('description')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('location')->nullable();
            $table->string('condition_status')->nullable();
            $table->string('item_status')->nullable()->index();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->string('fa_code')->nullable();
            $table->string('years_purchase')->nullable();
            $table->decimal('total_cost', 15, 2)->nullable();
            $table->decimal('accumulated', 15, 2)->nullable();
            $table->decimal('nbv_at', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
