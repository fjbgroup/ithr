<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_bookings', function (Blueprint $col) {
            $col->id();
            $col->foreignId('room_id')->constrained('meeting_rooms')->onDelete('cascade');
            $col->foreignId('booked_by_id')->constrained('users')->onDelete('cascade');
            $col->string('booked_by_name', 150);
            $col->date('booking_date');
            $col->time('start_time');
            $col->time('end_time');
            $col->text('purpose');
            $col->integer('attendees')->default(1);
            $col->enum('status', ['Pending', 'Approved', 'Rejected', 'CancelRequested', 'EditRequested'])->default('Pending');
            
            $col->foreignId('approved_by_id')->nullable()->constrained('users')->onDelete('no action');
            $col->string('approved_by_name', 150)->nullable();
            $col->timestamp('approved_at')->nullable();
            $col->string('rejection_reason', 500)->nullable();
            $col->string('cancel_reason', 500)->nullable();
            
            // Proposed edits
            $col->foreignId('proposed_room_id')->nullable()->constrained('meeting_rooms')->onDelete('no action');
            $col->date('proposed_date')->nullable();
            $col->time('proposed_start_time')->nullable();
            $col->time('proposed_end_time')->nullable();
            $col->text('proposed_purpose')->nullable();
            $col->integer('proposed_attendees')->nullable();
            $col->string('edit_reason', 500)->nullable();
            
            $col->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_bookings');
    }
};
