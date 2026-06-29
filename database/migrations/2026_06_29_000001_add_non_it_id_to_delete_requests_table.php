<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delete_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('non_it_id')->nullable()->index()->after('inventory_id');
        });
    }

    public function down(): void
    {
        Schema::table('delete_requests', function (Blueprint $table) {
            $table->dropColumn('non_it_id');
        });
    }
};
