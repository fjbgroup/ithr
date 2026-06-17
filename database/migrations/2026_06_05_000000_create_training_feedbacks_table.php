<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_feedbacks', function (Blueprint $col) {
            $col->id();
            $col->unsignedBigInteger('attendance_id');
            $col->unsignedBigInteger('staff_id');
            $col->unsignedBigInteger('course_id');
            $col->unsignedTinyInteger('content_rating')->nullable();
            $col->unsignedTinyInteger('trainer_rating')->nullable();
            $col->unsignedTinyInteger('venue_rating')->nullable();
            $col->unsignedTinyInteger('overall_rating')->nullable();
            $col->boolean('would_recommend')->nullable();
            $col->string('comments', 1000)->nullable();
            $col->timestamps();

            $col->unique('attendance_id');
            $col->index('course_id');
            $col->index('staff_id');

            $col->foreign('attendance_id')->references('id')->on('training_attendances')->onDelete('cascade');
            $col->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
            $col->foreign('course_id')->references('id')->on('training_courses')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_feedbacks');
    }
};
