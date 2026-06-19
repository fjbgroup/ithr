<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transport_modes', function (Blueprint $col) {
            $col->id();
            $col->string('name', 100)->unique();
            $col->timestamps();
        });

        Schema::create('business_travel', function (Blueprint $col) {
            $col->id();
            $col->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $col->string('destination', 255);
            $col->text('purpose')->nullable();
            $col->date('departure_date');
            $col->date('return_date');
            $col->string('transport', 100)->nullable();
            $col->text('notes')->nullable();
            $col->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $col->timestamps();
        });

        Schema::create('system_settings', function (Blueprint $col) {
            $col->id();
            $col->string('setting_key', 100)->unique();
            $col->text('setting_value')->nullable();
            $col->timestamps();
        });

        Schema::create('positions', function (Blueprint $col) {
            $col->id();
            $col->string('title', 200)->unique();
            $col->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('positions');
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('business_travel');
        Schema::dropIfExists('transport_modes');
    }
};
