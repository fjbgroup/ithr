<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Superseded by the unified users table (see 2026_06_18_000001_extend_users_for_unified_auth).
        // Kept so migrate:fresh can run without errors; the table is dropped later.
        if (Schema::hasTable('wt_users')) return;
        Schema::create('wt_users', function (Blueprint $table) {
            $table->increments('user_id');
            $table->string('staff_id')->nullable()->index();
            $table->string('username')->unique();
            $table->string('full_name');
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->string('phone_no')->nullable();
            $table->string('password');
            $table->string('role')->default('user');
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wt_users');
    }
};
