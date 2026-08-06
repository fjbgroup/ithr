<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('it_peripheral_loans', function (Blueprint $table) {
            $table->id();
            $table->string('referral_code')->unique();
            $table->unsignedBigInteger('staff_id');
            $table->unsignedBigInteger('inventory_item_id');
            $table->string('status')->default('Pending Verification');
            $table->text('notes')->nullable();
            
            // Timestamps and tracking
            $table->timestamp('pickup_verified_at')->nullable();
            $table->unsignedBigInteger('pickup_verified_by')->nullable();
            
            $table->timestamp('return_verified_at')->nullable();
            // user return doesn't need a "by" since it's the staff themselve
            
            $table->timestamp('endorsed_at')->nullable();
            $table->unsignedBigInteger('endorsed_by')->nullable();
            
            $table->timestamps();

            // Foreign keys
            $table->foreign('staff_id')->references('id')->on('staff');
            $table->foreign('inventory_item_id')->references('id')->on('inventory_items');
            $table->foreign('pickup_verified_by')->references('id')->on('users');
            $table->foreign('endorsed_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('it_peripheral_loans');
    }
};
