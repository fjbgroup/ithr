<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('non_it_assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_number')->nullable()->index();
            $table->string('asset_class')->nullable();
            $table->string('description')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('location')->nullable();
            $table->string('item_status')->nullable()->index();
            $table->string('condition_status')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->date('date_registered')->nullable();
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
        Schema::dropIfExists('non_it_assets');
    }
};
