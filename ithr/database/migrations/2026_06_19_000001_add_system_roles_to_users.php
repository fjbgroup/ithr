<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('it_role', [
                'admin_it', 'admin', 'finance_admin', 'hou', 'gm', 'ceo', 'user',
            ])->nullable()->default(null)->after('role');

            $table->enum('wt_role', [
                'admin_it', 'admin', 'user',
            ])->nullable()->default(null)->after('it_role');
        });

        // Backfill it_role from current role for users who had IT access
        DB::statement("
            UPDATE users
            SET it_role = role
            WHERE role IN ('admin_it', 'admin', 'finance_admin', 'hou', 'gm', 'ceo', 'user')
        ");

        // Backfill wt_role from current role for users who had WT access
        DB::statement("
            UPDATE users
            SET wt_role = role
            WHERE role IN ('admin_it', 'admin')
        ");

        // Reset module-specific roles on the base column back to 'staff'
        // (admin, user, finance_admin are not HR roles)
        DB::statement("
            UPDATE users
            SET role = 'staff'
            WHERE role IN ('admin', 'user', 'finance_admin')
        ");
    }

    public function down(): void
    {
        // Restore role from it_role for users who have IT-specific roles
        DB::statement("
            UPDATE users
            SET role = it_role
            WHERE it_role IN ('admin_it', 'finance_admin', 'hou', 'gm', 'ceo', 'user')
              AND role = 'staff'
        ");
        DB::statement("
            UPDATE users
            SET role = wt_role
            WHERE wt_role = 'admin'
              AND role = 'staff'
        ");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['it_role', 'wt_role']);
        });
    }
};
