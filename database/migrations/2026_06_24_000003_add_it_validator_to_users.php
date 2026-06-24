<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_it_validator')->default(false)->after('it_role');
        });

        // Grant validator role to the specific user
        DB::table('users')
            ->where('name', 'Mohd Azrull Bin Masnam')
            ->where('it_role', 'hou')
            ->update(['is_it_validator' => true]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_it_validator');
        });
    }
};
