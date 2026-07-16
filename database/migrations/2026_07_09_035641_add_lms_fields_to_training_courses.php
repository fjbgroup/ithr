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
            $table->string('platform')->default('HR')->after('id'); // HR or LMS
            $table->unsignedBigInteger('pic_id')->nullable()->after('platform');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('training_courses', function (Blueprint $table) {
            $table->dropColumn(['platform', 'pic_id']);
        });
    }
};
