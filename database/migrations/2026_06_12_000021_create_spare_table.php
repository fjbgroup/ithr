<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spare', function (Blueprint $table) {
            $table->increments('replacement_id');
            $table->unsignedInteger('original_walkie_id')->nullable()->index();
            $table->string('original_radio_id')->nullable();
            $table->unsignedInteger('spare_walkie_id')->nullable()->index();
            $table->string('spare_radio_id')->nullable();
            $table->date('replacement_date')->nullable();
            $table->date('return_date')->nullable();
            $table->string('status')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spare');
    }
};
