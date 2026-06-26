<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('walkie_talkies', function (Blueprint $table) {
            if (! Schema::hasColumn('walkie_talkies', 'wt_warranty_start_date')) {
                $table->date('wt_warranty_start_date')->nullable()->after('special_use_returned');
            }

            if (! Schema::hasColumn('walkie_talkies', 'wt_warranty_end_date')) {
                $table->date('wt_warranty_end_date')->nullable()->after('wt_warranty_start_date');
            }

            if (! Schema::hasColumn('walkie_talkies', 'battery_warranty_start_date')) {
                $table->date('battery_warranty_start_date')->nullable()->after('wt_warranty_end_date');
            }

            if (! Schema::hasColumn('walkie_talkies', 'battery_warranty_end_date')) {
                $table->date('battery_warranty_end_date')->nullable()->after('battery_warranty_start_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('walkie_talkies', function (Blueprint $table) {
            $columns = [
                'wt_warranty_start_date',
                'wt_warranty_end_date',
                'battery_warranty_start_date',
                'battery_warranty_end_date',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('walkie_talkies', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
