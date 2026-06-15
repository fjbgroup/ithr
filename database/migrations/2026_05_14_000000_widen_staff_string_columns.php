<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->string('company', 200)->default('')->change();
            $table->string('company_id', 200)->nullable()->change();
            $table->string('operation_support', 100)->nullable()->change();
            $table->string('gender', 50)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->string('company', 50)->default('FJB')->change();
            $table->string('company_id', 50)->nullable()->change();
            $table->string('operation_support', 20)->nullable()->change();
            $table->string('gender', 10)->nullable()->change();
        });
    }
};
