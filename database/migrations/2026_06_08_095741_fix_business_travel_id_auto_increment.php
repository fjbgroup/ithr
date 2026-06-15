<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $pk = DB::select("SELECT COLUMN_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'business_travel' AND CONSTRAINT_NAME = 'PRIMARY'");
        if (empty($pk)) {
            DB::statement('ALTER TABLE business_travel MODIFY COLUMN id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY');
        } else {
            DB::statement('ALTER TABLE business_travel MODIFY COLUMN id INT(11) NOT NULL AUTO_INCREMENT');
        }
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE business_travel MODIFY COLUMN id INT(11) NOT NULL');
        DB::statement('ALTER TABLE business_travel DROP PRIMARY KEY');
    }
};
