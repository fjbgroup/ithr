<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ewaste_requests', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->unsignedBigInteger('requested_by')->nullable()->index();
            $table->unsignedBigInteger('inventory_id')->nullable()->index();
            $table->string('asset_number')->nullable();
            $table->string('asset_class')->nullable();
            $table->string('description')->nullable();
            $table->string('serial_number')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('Pending')->index();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ewaste_requests');
    }
};
