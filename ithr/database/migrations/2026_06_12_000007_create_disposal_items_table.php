<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disposal_items', function (Blueprint $table) {
            $table->id();
            $table->string('asset_number')->nullable()->index();
            $table->string('asset_class')->nullable();
            $table->string('description')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('disposal_status')->nullable()->index();
            $table->string('disposal_method')->nullable();
            $table->string('vendor_collector')->nullable();
            $table->string('certificate_number')->nullable();
            $table->text('notes')->nullable();
            $table->date('date_flagged')->nullable();
            $table->date('date_disposed')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disposal_items');
    }
};
