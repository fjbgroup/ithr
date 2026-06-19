<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedBigInteger('group_id')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_classes');
    }
};
