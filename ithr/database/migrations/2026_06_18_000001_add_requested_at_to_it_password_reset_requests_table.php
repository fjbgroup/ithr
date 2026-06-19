<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('it_password_reset_requests', 'requested_at')) {
            return;
        }

        Schema::table('it_password_reset_requests', function (Blueprint $table) {
            $table->timestamp('requested_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('it_password_reset_requests', 'requested_at')) {
            return;
        }

        Schema::table('it_password_reset_requests', function (Blueprint $table) {
            $table->dropColumn('requested_at');
        });
    }
};
