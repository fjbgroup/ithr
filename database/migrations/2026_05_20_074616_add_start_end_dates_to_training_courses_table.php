<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('training_courses', function (Blueprint $table) {
            $table->renameColumn('training_date', 'start_date');
        });

        Schema::table('training_courses', function (Blueprint $table) {
            $table->date('end_date')->nullable()->after('start_date');
        });
        
        // Populate end_date with start_date for existing records
        DB::table('training_courses')->whereNull('end_date')->update([
            'end_date' => DB::raw('start_date')
        ]);
    }

    public function down(): void
    {
        Schema::table('training_courses', function (Blueprint $table) {
            $table->dropColumn('end_date');
            $table->renameColumn('start_date', 'training_date');
        });
    }
};
