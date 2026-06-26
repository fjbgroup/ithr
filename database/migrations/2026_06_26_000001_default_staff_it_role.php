<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Policy: in the IT system every HR staff defaults to the "Staff" role
        // (it_role = 'user') so they can log in with their own Staff ID.
        // Staff already assigned a higher role (admin/finance_admin/hou/gm/ceo)
        // keep it — we only touch accounts with no IT role yet.
        DB::table('users')
            ->whereNull('it_role')
            ->whereNotNull('staff_id')   // only HR-staff-linked accounts
            ->update(['it_role' => 'user']);
    }

    public function down(): void
    {
        // Revert only the default-granted Staff access; leave elevated roles intact.
        DB::table('users')
            ->where('it_role', 'user')
            ->whereNotNull('staff_id')
            ->update(['it_role' => null]);
    }
};
