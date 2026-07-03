<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('walkie_talkie_handovers', function (Blueprint $table) {
            $table->string('checked_by_name')->nullable()->after('handover_by_signed_at');
            $table->mediumText('checked_by_signature')->nullable()->after('checked_by_name');
            $table->timestamp('checked_by_signed_at')->nullable()->after('checked_by_signature');
        });
    }

    public function down(): void
    {
        Schema::table('walkie_talkie_handovers', function (Blueprint $table) {
            $table->dropColumn([
                'checked_by_name',
                'checked_by_signature',
                'checked_by_signed_at',
            ]);
        });
    }
};
