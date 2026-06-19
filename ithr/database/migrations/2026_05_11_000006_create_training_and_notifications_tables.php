<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $col) {
            $col->id();
            $col->foreignId('user_id')->constrained()->onDelete('cascade');
            $col->string('type', 30)->default('info');
            $col->string('title', 150);
            $col->string('message', 500)->nullable();
            $col->string('link', 255)->nullable();
            $col->boolean('is_read')->default(0);
            $col->timestamps();
            $col->index(['user_id', 'is_read']);
        });

        Schema::create('training_courses', function (Blueprint $col) {
            $col->id();
            $col->string('code', 50)->unique();
            $col->string('title', 300);
            $col->string('training_type', 20)->default('External');
            $col->string('company', 50)->nullable();
            $col->date('training_date')->nullable();
            $col->string('venue', 255)->nullable();
            $col->string('duration', 100)->nullable();
            $col->timestamps();
        });

        Schema::create('training_attendances', function (Blueprint $col) {
            $col->id();
            $col->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $col->foreignId('course_id')->constrained('training_courses')->onDelete('cascade');
            $col->timestamps();
            $col->unique(['staff_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_attendances');
        Schema::dropIfExists('training_courses');
        Schema::dropIfExists('notifications');
    }
};
