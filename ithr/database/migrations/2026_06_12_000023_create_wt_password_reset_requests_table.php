<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wt_password_reset_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->nullable()->index();
            $table->string('staff_id')->nullable();
            $table->string('requester_name')->nullable();
            $table->string('requested_password')->nullable();
            $table->text('justification')->nullable();
            $table->string('status')->default('pending')->index();
            $table->unsignedInteger('reviewed_by')->nullable();
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wt_password_reset_requests');
    }
};
