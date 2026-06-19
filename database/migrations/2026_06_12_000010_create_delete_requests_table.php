<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delete_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_id')->nullable()->index();
            $table->unsignedBigInteger('requested_by')->nullable()->index();
            $table->text('reason')->nullable();
            $table->string('asset_number')->nullable();
            $table->string('asset_class')->nullable();
            $table->string('asset_description')->nullable();
            $table->string('status')->default('Pending')->index();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delete_requests');
    }
};
