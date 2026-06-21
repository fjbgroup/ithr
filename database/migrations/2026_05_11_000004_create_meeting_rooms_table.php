<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meeting_rooms', function (Blueprint $col) {
            $col->id();
            $col->string('name', 100);
            $col->text('description')->nullable();
            $col->integer('capacity')->default(1);
            $col->string('color_class', 30)->default('room-blue');
            $col->timestamps();
        });

        Schema::create('room_pics', function (Blueprint $col) {
            $col->id();
            $col->foreignId('room_id')->constrained('meeting_rooms')->onDelete('cascade');
            $col->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $col->tinyInteger('level')->default(1);
            $col->foreignId('added_by')->nullable()->constrained('users')->onDelete('no action');
            $col->timestamps();
            $col->unique(['room_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_pics');
        Schema::dropIfExists('meeting_rooms');
    }
};
