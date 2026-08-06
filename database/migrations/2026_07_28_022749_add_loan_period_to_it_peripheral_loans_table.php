<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('it_peripheral_loans', function (Blueprint $table) {
            $table->date('loan_start_date')->nullable()->after('notes');
            $table->date('loan_end_date')->nullable()->after('loan_start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('it_peripheral_loans', function (Blueprint $table) {
            $table->dropColumn(['loan_start_date', 'loan_end_date']);
        });
    }
};
