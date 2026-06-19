<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('update_requests', function (Blueprint $col) {
            $col->id();
            $col->foreignId('requester_id')->constrained('users')->onDelete('cascade');
            $col->string('requester_name', 100);
            $col->enum('record_type', ['Training Record', 'Family Information', 'Staff Data']);
            $col->integer('record_id');
            $col->string('record_reference', 200);
            $col->text('message');
            $col->enum('status', ['Pending', 'Resolved', 'Dismissed'])->default('Pending');
            $col->text('admin_note')->nullable();
            $col->timestamps();
        });

        Schema::create('family_members', function (Blueprint $col) {
            $col->id();
            $col->foreignId('staff_id')->nullable()->constrained('staff')->onDelete('cascade');
            $col->string('employee_name', 200)->nullable(); // Legacy support
            $col->string('name', 200);
            $col->string('relationship', 50);
            $col->date('date_of_birth')->nullable();
            $col->string('id_no', 50)->nullable();
            $col->string('emergency_contact', 10)->default('No');
            $col->string('phone_number', 50)->nullable();
            $col->timestamps();
        });

        Schema::create('staff_ir', function (Blueprint $col) {
            $col->id();
            $col->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $col->string('title', 300);
            $col->date('date');
            $col->enum('type', ['Verbal', 'Written'])->default('Verbal');
            $col->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $col->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_ir');
        Schema::dropIfExists('family_members');
        Schema::dropIfExists('update_requests');
    }
};
