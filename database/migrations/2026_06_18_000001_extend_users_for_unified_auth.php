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
            $table->string('department', 100)->nullable()->after('position');
            $table->string('dept_name', 200)->nullable()->after('department');
            $table->boolean('must_change_password')->default(false)->after('is_active');
            $table->timestamp('last_login')->nullable()->after('must_change_password');
            $table->string('avatar', 255)->nullable()->after('last_login');
            $table->text('signature_img')->nullable()->after('avatar');
            $table->string('phone_no', 50)->nullable()->after('signature_img');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin_it','admin_hr','finance_admin','hou','gm','ceo','staff','user','admin') NOT NULL DEFAULT 'staff'");
            DB::statement("UPDATE users u LEFT JOIN departments d ON u.department_id = d.id SET u.dept_name = TRIM(d.name) WHERE u.department_id IS NOT NULL AND d.name IS NOT NULL");
            DB::statement("UPDATE users u INNER JOIN staff s ON TRIM(s.staff_no) = TRIM(u.staff_no) SET u.phone_no = s.phone_number WHERE s.phone_number IS NOT NULL AND s.phone_number != ''");
        } elseif (DB::getDriverName() === 'sqlsrv') {
            DB::statement("UPDATE u SET u.dept_name = TRIM(d.name) FROM users u LEFT JOIN departments d ON u.department_id = d.id WHERE u.department_id IS NOT NULL AND d.name IS NOT NULL");
            DB::statement("UPDATE u SET u.phone_no = s.phone_number FROM users u INNER JOIN staff s ON TRIM(s.staff_no) = TRIM(u.staff_no) WHERE s.phone_number IS NOT NULL AND s.phone_number != ''");
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['department', 'dept_name', 'must_change_password', 'last_login', 'avatar', 'signature_img', 'phone_no']);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin_it','admin_hr','staff','ceo') NOT NULL DEFAULT 'staff'");
        }
    }
};
