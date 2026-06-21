<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') return;
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin_it','admin_hr','staff','ceo') NOT NULL DEFAULT 'staff'");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') return;
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin_it','admin_hr','staff') NOT NULL DEFAULT 'staff'");
    }
};
