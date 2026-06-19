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
        if (Schema::hasTable('it_users')) return;
        Schema::create('it_users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('full_name');
            $table->string('email')->nullable();
            $table->string('role')->default('user');
            $table->string('department')->nullable();
            $table->string('dept_name')->nullable();
            $table->string('staff_id')->nullable();
            $table->string('avatar')->nullable();
            $table->string('signature_img')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('must_change_password')->default(false);
            $table->timestamp('last_login')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('it_users');
    }
};
