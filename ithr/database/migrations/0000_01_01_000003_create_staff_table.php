<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $col) {
            $col->id();
            $col->string('staff_no', 50)->unique();
            $col->string('name', 200);
            $col->string('position', 200)->nullable();
            $col->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $col->string('company', 50)->default('FJB');
            $col->string('company_id', 50)->nullable();
            $col->string('email', 150)->nullable();
            $col->date('date_joined')->nullable();
            $col->date('date_of_birth')->nullable();
            $col->string('ic_number', 20)->nullable();
            $col->string('employment_status', 50)->nullable();
            $col->date('last_promotion_date')->nullable();
            $col->string('gender', 10)->nullable();
            $col->string('location', 150)->nullable();
            $col->string('compensation_grade', 50)->nullable();
            $col->string('management_level', 100)->nullable();
            $col->string('job_level', 150)->nullable();
            $col->string('job_category', 100)->nullable();
            $col->boolean('is_active')->default(1);
            $col->string('phone_number', 20)->nullable();
            $col->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
