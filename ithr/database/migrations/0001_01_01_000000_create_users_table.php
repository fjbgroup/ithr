<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $col) {
            $col->id();
            $col->string('staff_no', 50)->nullable();
            $col->string('name', 200);
            $col->string('email', 150)->nullable();
            $col->timestamp('email_verified_at')->nullable();
            $col->string('password');
            $col->enum('role', ['admin_it', 'admin_hr', 'staff'])->default('staff');
            $col->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $col->string('position', 200)->nullable();
            $col->string('company', 50)->default('FJB');
            $col->boolean('is_active')->default(1);
            $col->foreignId('staff_id')->nullable()->unique()->constrained('staff')->onDelete('set null');
            $col->rememberToken();
            $col->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('otp_code', 6);
            $table->datetime('expires_at');
            $table->boolean('used')->default(0);
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
