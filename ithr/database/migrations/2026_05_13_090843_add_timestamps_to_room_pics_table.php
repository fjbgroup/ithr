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
        Schema::table('room_pics', function (Blueprint $table) {
            // Check if updated_at exists, add if not
            if (!Schema::hasColumn('room_pics', 'updated_at')) {
                $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_pics', function (Blueprint $table) {
            $table->dropColumn('updated_at');
        });
    }
};
