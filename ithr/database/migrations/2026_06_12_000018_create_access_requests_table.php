<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('access_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->nullable()->index();
            $table->string('request_type')->nullable();
            $table->string('accessory_request_mode')->nullable();
            $table->text('replacement_return_note')->nullable();
            $table->string('radio_id')->nullable();
            $table->unsignedInteger('walkie_inventory_id')->nullable();
            $table->json('assigned_walkie_inventory_ids')->nullable();
            $table->json('assigned_radio_ids')->nullable();
            $table->string('assigned_serial_number')->nullable();
            $table->json('assigned_serial_numbers')->nullable();
            $table->string('full_name')->nullable();
            $table->string('staff_id')->nullable();
            $table->date('request_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->string('ownership_type')->nullable();
            $table->string('shared_with')->nullable();
            $table->string('bay_from')->nullable();
            $table->text('accessories')->nullable();
            $table->unsignedInteger('submit_to_admin_id')->nullable();
            $table->unsignedInteger('handled_by')->nullable();
            $table->string('sector')->nullable();
            $table->string('location')->nullable();
            $table->string('event_name')->nullable();
            $table->unsignedInteger('quantity')->nullable();
            $table->unsignedInteger('duration_days')->nullable();
            $table->json('pic_details')->nullable();
            $table->string('pickup_method')->nullable();
            $table->string('pickup_representative_name')->nullable();
            $table->timestamp('requested_pickup_at')->nullable();
            $table->text('pickup_note')->nullable();
            $table->text('justifications')->nullable();
            $table->string('status')->nullable()->index();
            $table->text('approval_remark')->nullable();
            $table->date('return_date')->nullable();
            $table->string('return_person')->nullable();
            $table->string('return_department')->nullable();
            $table->string('return_phone_no')->nullable();
            $table->string('return_status')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_requests');
    }
};
