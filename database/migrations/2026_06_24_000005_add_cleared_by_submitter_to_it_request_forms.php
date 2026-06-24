<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('it_request_forms', function (Blueprint $table) {
            $table->boolean('cleared_by_submitter')->default(false)->after('validator_remarks');
        });
    }

    public function down(): void
    {
        Schema::table('it_request_forms', function (Blueprint $table) {
            $table->dropColumn('cleared_by_submitter');
        });
    }
};
