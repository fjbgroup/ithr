<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Allow 'ceo' as an HR role. HR roles are independent from it_role/wt_role,
     * so a staff member can be CEO in HR regardless of their IT/WT role.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlsrv') {
            $this->dropRoleCheckConstraints();
            DB::statement("ALTER TABLE users ADD CONSTRAINT CK_users_role CHECK (role IN ('staff','admin_hr','admin_it','ceo'))");
        } elseif (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('staff','admin_hr','admin_it','ceo') NOT NULL DEFAULT 'staff'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlsrv') {
            // Demote any CEO back to staff so the narrower constraint can apply.
            DB::statement("UPDATE users SET role = 'staff' WHERE role = 'ceo'");
            $this->dropRoleCheckConstraints();
            DB::statement("ALTER TABLE users ADD CONSTRAINT CK_users_role CHECK (role IN ('staff','admin_hr','admin_it'))");
        } elseif (DB::getDriverName() === 'mysql') {
            DB::statement("UPDATE users SET role = 'staff' WHERE role = 'ceo'");
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('staff','admin_hr','admin_it') NOT NULL DEFAULT 'staff'");
        }
    }

    /**
     * Drop every CHECK constraint currently bound to the users.role column
     * (the original table creation left auto-named, duplicated constraints).
     */
    private function dropRoleCheckConstraints(): void
    {
        $constraints = DB::select("
            SELECT cc.name AS name
            FROM sys.check_constraints cc
            JOIN sys.columns col
              ON cc.parent_object_id = col.object_id
             AND cc.parent_column_id = col.column_id
            WHERE OBJECT_NAME(cc.parent_object_id) = 'users'
              AND col.name = 'role'
        ");

        foreach ($constraints as $c) {
            DB::statement("ALTER TABLE users DROP CONSTRAINT [{$c->name}]");
        }
    }
};
