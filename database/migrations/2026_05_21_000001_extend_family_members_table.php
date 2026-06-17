<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('family_members', function (Blueprint $table) {
            $table->string('phone_country_code', 20)->nullable();
            $table->string('phone_device_type', 50)->nullable();
            $table->string('region_of_birth', 100)->nullable();
            $table->date('occupation_effective_date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('family_members', function (Blueprint $table) {
            $table->dropColumn(['phone_country_code', 'phone_device_type', 'region_of_birth', 'occupation_effective_date']);
        });
    }
};
