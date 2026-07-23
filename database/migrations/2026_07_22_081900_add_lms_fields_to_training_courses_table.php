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
        Schema::table('training_courses', function (Blueprint $table) {
            $table->boolean('is_open_enrollment')->default(false)->after('platform');
            $table->date('due_date')->nullable()->after('end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('training_courses', function (Blueprint $table) {
            $table->dropColumn(['is_open_enrollment', 'due_date']);
        });
    }
};
