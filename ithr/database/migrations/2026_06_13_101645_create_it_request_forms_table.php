<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('it_request_forms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('submitted_by')->nullable()->index();
            $table->string('request_type')->nullable(); // hardware, software, system, service
            $table->string('subject')->nullable();
            $table->string('status')->default('New'); // New, Draft, Approved, Rejected

            // Hardware fields
            $table->string('hw_request_type')->nullable();
            $table->json('hw_items')->nullable();
            $table->string('hw_pc_laptop_no')->nullable();
            $table->string('hw_printer_no')->nullable();

            // Software fields
            $table->string('sw_request_type')->nullable();
            $table->string('sw_software_name')->nullable();
            $table->string('sw_budgeted')->nullable();
            $table->string('sw_opex_capex')->nullable();
            $table->string('sw_cost_center')->nullable();
            $table->decimal('sw_expected_value', 15, 2)->nullable();

            // System fields
            $table->string('sys_request_type')->nullable();
            $table->json('sys_items')->nullable();

            // Service fields
            $table->json('svc_items')->nullable();

            // User / requester info
            $table->string('user_type')->nullable();
            $table->date('exit_join_date')->nullable();
            $table->text('justification')->nullable();
            $table->string('document_path')->nullable();

            $table->string('user_name')->nullable();
            $table->string('user_email')->nullable();
            $table->string('user_address')->nullable();
            $table->string('user_department')->nullable();
            $table->string('user_designation')->nullable();
            $table->string('user_staff_id')->nullable();
            $table->string('user_contact')->nullable();

            // Requester contact
            $table->string('req_name')->nullable();
            $table->string('req_department')->nullable();
            $table->string('req_staff_id')->nullable();
            $table->string('req_designation')->nullable();
            $table->string('req_contact')->nullable();
            $table->string('req_company')->nullable();

            // Approver info
            $table->string('approver_name')->nullable();
            $table->string('approver_department')->nullable();
            $table->string('approver_designation')->nullable();
            $table->string('approver_contact')->nullable();
            $table->string('approver_company')->nullable();

            // Review
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('approval_remarks')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('it_request_forms');
    }
};
