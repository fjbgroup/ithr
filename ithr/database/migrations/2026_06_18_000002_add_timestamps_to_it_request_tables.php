<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['add_asset_requests', 'delete_requests', 'edit_asset_requests', 'ewaste_requests'] as $table) {
            if (! Schema::hasColumn($table, 'created_at') && ! Schema::hasColumn($table, 'updated_at')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->timestamps();
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['add_asset_requests', 'delete_requests', 'edit_asset_requests', 'ewaste_requests'] as $table) {
            if (Schema::hasColumn($table, 'created_at') && Schema::hasColumn($table, 'updated_at')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropTimestamps();
                });
            }
        }
    }
};
