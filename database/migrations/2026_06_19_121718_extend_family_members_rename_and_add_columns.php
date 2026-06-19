<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Rename legacy columns to match model expectations
        Schema::table('family_members', function (Blueprint $table) {
            $table->renameColumn('name', 'family_member_name');
            $table->renameColumn('id_no', 'nric_no');
        });

        // Add all missing columns from model $fillable
        Schema::table('family_members', function (Blueprint $table) {
            $table->string('dependent_id', 50)->nullable()->after('nric_no');
            $table->string('gender', 20)->nullable()->after('dependent_id');
            $table->string('city_of_birth', 100)->nullable()->after('gender');
            $table->string('country_of_birth', 100)->nullable()->after('city_of_birth');
            $table->string('nationality', 100)->nullable()->after('country_of_birth');
            $table->string('citizenship_status', 100)->nullable()->after('nationality');
            $table->boolean('use_employee_address')->default(0)->after('citizenship_status');
            $table->boolean('use_employee_phone')->default(0)->after('use_employee_address');
            $table->boolean('is_fulltime_student')->default(0)->after('use_employee_phone');
            $table->date('student_start_date')->nullable()->after('is_fulltime_student');
            $table->date('student_end_date')->nullable()->after('student_start_date');
            $table->string('occupation', 100)->nullable()->after('student_end_date');
            $table->boolean('is_disabled')->default(0)->after('occupation');
            $table->boolean('is_terminated')->default(0)->after('is_disabled');
            $table->date('effective_date')->nullable()->after('is_terminated');
            $table->string('company_code', 50)->nullable()->after('effective_date');
            $table->string('company_name', 200)->nullable()->after('company_code');
            $table->string('region_name', 100)->nullable()->after('company_name');
            $table->unsignedBigInteger('created_by')->nullable()->after('region_name');
        });
    }

    public function down(): void
    {
        Schema::table('family_members', function (Blueprint $table) {
            $table->dropColumn([
                'dependent_id', 'gender', 'city_of_birth', 'country_of_birth',
                'nationality', 'citizenship_status', 'use_employee_address',
                'use_employee_phone', 'is_fulltime_student', 'student_start_date',
                'student_end_date', 'occupation', 'is_disabled', 'is_terminated',
                'effective_date', 'company_code', 'company_name', 'region_name', 'created_by',
            ]);
        });

        Schema::table('family_members', function (Blueprint $table) {
            $table->renameColumn('family_member_name', 'name');
            $table->renameColumn('nric_no', 'id_no');
        });
    }
};
