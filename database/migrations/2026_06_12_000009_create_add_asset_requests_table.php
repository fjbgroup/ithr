<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('add_asset_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requested_by')->nullable()->index();
            $table->string('asset_number')->nullable();
            $table->string('asset_class')->nullable();
            $table->string('description')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('Pending')->index();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('add_asset_requests');
    }
};
