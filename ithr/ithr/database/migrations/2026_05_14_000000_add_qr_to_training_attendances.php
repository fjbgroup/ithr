<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('training_attendances', function (Blueprint $table) {
            $table->string('qr_token', 64)->nullable()->unique();
            $table->timestamp('qr_used_at')->nullable()->after('qr_token');
        });
    }

    public function down(): void
    {
        Schema::table('training_attendances', function (Blueprint $table) {
            $table->dropUnique(['qr_token']);
            $table->dropColumn(['qr_token', 'qr_used_at']);
        });
    }
};
