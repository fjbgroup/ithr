<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_records', function (Blueprint $table) {
            $table->increments('maintenance_id');
            $table->unsignedInteger('walkie_id')->nullable()->index();
            $table->unsignedInteger('temporary_spare_walkie_id')->nullable();
            $table->boolean('temporary_spare_requested')->default(false);
            $table->text('temporary_spare_request_note')->nullable();
            $table->timestamp('temporary_spare_assigned_at')->nullable();
            $table->timestamp('temporary_spare_returned_at')->nullable();
            $table->timestamp('original_returned_at')->nullable();
            $table->string('original_returned_by')->nullable();
            $table->string('radio_id')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('model')->nullable();
            $table->string('current_ownership')->nullable();
            $table->string('department_name')->nullable();
            $table->date('received_date')->nullable();
            $table->timestamp('ict_received_at')->nullable();
            $table->string('ict_received_by')->nullable();
            $table->date('repair_date')->nullable();
            $table->boolean('done')->default(false);
            $table->date('finish_date')->nullable();
            $table->text('issue_description')->nullable();
            $table->string('issue')->nullable();
            $table->text('remarks')->nullable();
            $table->date('maintenance_date')->nullable();
            $table->string('status')->nullable()->index();
            $table->string('request_source')->nullable();
            $table->unsignedInteger('submit_to_admin_id')->nullable();
            $table->unsignedInteger('handled_by')->nullable();
            $table->string('reporter_name')->nullable();
            $table->string('reporter_staff_id')->nullable();
            $table->string('designation')->nullable();
            $table->string('phone_no')->nullable();
            $table->string('handover_person')->nullable();
            $table->timestamp('handover_at')->nullable();
            $table->string('pickup_person')->nullable();
            $table->timestamp('pickup_at')->nullable();
            $table->string('ownership_type')->nullable();
            $table->string('shared_with')->nullable();
            $table->string('sector')->nullable();
            $table->string('location')->nullable();
            $table->string('problem_possible')->nullable();
            $table->json('evidence_paths')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_records');
    }
};
