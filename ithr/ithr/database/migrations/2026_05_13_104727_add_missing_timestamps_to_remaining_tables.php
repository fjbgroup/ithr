<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'updated_at')) {
                $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            }
        });

        Schema::table('meeting_rooms', function (Blueprint $table) {
            if (!Schema::hasColumn('meeting_rooms', 'created_at')) {
                $table->timestamp('created_at')->nullable()->useCurrent();
            }
            if (!Schema::hasColumn('meeting_rooms', 'updated_at')) {
                $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            }
        });

        Schema::table('positions', function (Blueprint $table) {
            if (!Schema::hasColumn('positions', 'updated_at')) {
                $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            }
        });

        Schema::table('transport_modes', function (Blueprint $table) {
            if (!Schema::hasColumn('transport_modes', 'updated_at')) {
                $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('updated_at');
        });
        Schema::table('meeting_rooms', function (Blueprint $table) {
            $table->dropColumn(['created_at', 'updated_at']);
        });
        Schema::table('positions', function (Blueprint $table) {
            $table->dropColumn('updated_at');
        });
        Schema::table('transport_modes', function (Blueprint $table) {
            $table->dropColumn('updated_at');
        });
    }
};
