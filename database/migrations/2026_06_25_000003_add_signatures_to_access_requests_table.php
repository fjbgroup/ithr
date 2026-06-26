<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('access_requests', function (Blueprint $table) {
            $table->mediumText('request_signature')->nullable()->after('approval_remark');
            $table->mediumText('return_signature')->nullable()->after('return_phone_no');
        });
    }

    public function down(): void
    {
        Schema::table('access_requests', function (Blueprint $table) {
            $table->dropColumn(['request_signature', 'return_signature']);
        });
    }
};
