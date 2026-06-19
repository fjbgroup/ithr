<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ewaste_items')) {
            return;
        }
        Schema::table('ewaste_items', function (Blueprint $table) {
            $table->timestamp('dismissed_at')->nullable()->after('created_by');
        });
    }

    public function down(): void
    {
        Schema::table('ewaste_items', function (Blueprint $table) {
            $table->dropColumn('dismissed_at');
        });
    }
};
