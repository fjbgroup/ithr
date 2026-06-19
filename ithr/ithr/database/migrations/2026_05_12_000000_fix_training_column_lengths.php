<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('training_courses', function (Blueprint $table) {
            $table->string('code', 150)->change();
            $table->string('title', 500)->change();
            $table->string('training_type', 50)->default('External')->change();
            $table->string('company', 50)->nullable()->change();
            $table->string('venue', 500)->nullable()->change();
        });

        Schema::table('training_attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('training_attendances', 'training_type')) {
                $table->string('training_type', 50)->default('External');
            } else {
                $table->string('training_type', 50)->default('External')->change();
            }
            
            if (!Schema::hasColumn('training_attendances', 'status')) {
                $table->string('status', 50)->default('Completed');
            } else {
                $table->string('status', 50)->default('Completed')->change();
            }
        });
    }

    public function down(): void
    {
    }
};
