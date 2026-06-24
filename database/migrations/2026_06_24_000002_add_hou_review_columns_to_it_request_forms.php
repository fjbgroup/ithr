<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('it_request_forms', function (Blueprint $table) {
            $table->unsignedBigInteger('hou_reviewed_by')->nullable()->after('approver_company');
            $table->timestamp('hou_reviewed_at')->nullable()->after('hou_reviewed_by');
            $table->text('hou_remarks')->nullable()->after('hou_reviewed_at');
        });
    }

    public function down(): void
    {
        Schema::table('it_request_forms', function (Blueprint $table) {
            $table->dropColumn(['hou_reviewed_by', 'hou_reviewed_at', 'hou_remarks']);
        });
    }
};
