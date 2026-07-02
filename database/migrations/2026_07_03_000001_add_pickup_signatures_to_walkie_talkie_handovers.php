<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('walkie_talkie_handovers', function (Blueprint $table) {
            $table->string('pickup_recipient_name')->nullable()->after('notes');
            $table->mediumText('pickup_recipient_signature')->nullable()->after('pickup_recipient_name');
            $table->timestamp('pickup_recipient_signed_at')->nullable()->after('pickup_recipient_signature');
            $table->string('handover_by_name')->nullable()->after('pickup_recipient_signed_at');
            $table->mediumText('handover_by_signature')->nullable()->after('handover_by_name');
            $table->timestamp('handover_by_signed_at')->nullable()->after('handover_by_signature');
            $table->text('accessories_snapshot')->nullable()->after('handover_by_signed_at');
            $table->timestamp('policy_accepted_at')->nullable()->after('accessories_snapshot');
            $table->timestamp('pickup_completed_at')->nullable()->after('policy_accepted_at');
        });
    }

    public function down(): void
    {
        Schema::table('walkie_talkie_handovers', function (Blueprint $table) {
            $table->dropColumn([
                'pickup_recipient_name',
                'pickup_recipient_signature',
                'pickup_recipient_signed_at',
                'handover_by_name',
                'handover_by_signature',
                'handover_by_signed_at',
                'accessories_snapshot',
                'policy_accepted_at',
                'pickup_completed_at',
            ]);
        });
    }
};
