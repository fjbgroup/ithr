<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('family_members', function (Blueprint $table) {
            $table->string('phone_country_code', 20)->nullable()->after('use_employee_phone');
            $table->string('phone_device_type', 50)->nullable()->after('phone_number');
            $table->string('region_of_birth', 100)->nullable()->after('country_of_birth');
            $table->date('occupation_effective_date')->nullable()->after('occupation');
        });
    }

    public function down(): void
    {
        Schema::table('family_members', function (Blueprint $table) {
            $table->dropColumn(['phone_country_code', 'phone_device_type', 'region_of_birth', 'occupation_effective_date']);
        });
    }
};
