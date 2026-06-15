<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $col) {
            $col->id();
            $col->integer('user_id')->nullable();
            $col->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $col->string('user_name', 200)->nullable();
            $col->string('user_role', 30)->nullable();
            $col->string('action', 30)->index();
            $col->string('module', 30)->index();
            $col->string('description', 500);
            $col->string('ip_address', 45)->nullable();
            $col->json('properties')->nullable();
            $col->timestamp('created_at')->nullable()->useCurrent();
            $col->index(['module', 'action']);
            $col->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
