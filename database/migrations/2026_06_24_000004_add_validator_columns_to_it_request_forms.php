<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('it_request_forms', function (Blueprint $table) {
            $table->unsignedBigInteger('validated_by')->nullable()->after('hou_remarks');
            $table->timestamp('validated_at')->nullable()->after('validated_by');
            $table->text('validator_remarks')->nullable()->after('validated_at');
        });
    }

    public function down(): void
    {
        Schema::table('it_request_forms', function (Blueprint $table) {
            $table->dropColumn(['validated_by', 'validated_at', 'validator_remarks']);
        });
    }
};
