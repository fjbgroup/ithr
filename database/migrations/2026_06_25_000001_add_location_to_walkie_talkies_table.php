<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('walkie_talkies', function (Blueprint $table) {
            if (! Schema::hasColumn('walkie_talkies', 'location')) {
                $table->string('location', 50)->nullable()->after('department');
            }
        });
    }

    public function down(): void
    {
        Schema::table('walkie_talkies', function (Blueprint $table) {
            if (Schema::hasColumn('walkie_talkies', 'location')) {
                $table->dropColumn('location');
            }
        });
    }
};
