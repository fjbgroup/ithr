<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wt_master_data', function (Blueprint $table) {
            $table->increments('id');
            $table->string('category')->index(); // model | department | position | ownership_type
            $table->string('value');
            $table->timestamps();

            $table->unique(['category', 'value']);
        });

        $this->seed();
    }

    public function down(): void
    {
        Schema::dropIfExists('wt_master_data');
    }

    /**
     * Seed the master data from the hardcoded constants plus the distinct
     * values already present in walkie_talkies, so nothing disappears from
     * the existing dropdowns once they are sourced from this table.
     */
    private function seed(): void
    {
        $now = now();

        $seeds = [
            'model' => ['R7', 'P8200', 'P8268', 'P8600I', 'P8660I', 'P8260'],
            'ownership_type' => ['INDIVIDUAL', 'SHARED', 'SPARE', 'UNALLOCATED'],
            'department' => [],
            'position' => [],
        ];

        if (Schema::hasTable('walkie_talkies')) {
            $seeds['model'] = array_merge($seeds['model'], DB::table('walkie_talkies')->pluck('model')->all());
            $seeds['department'] = DB::table('walkie_talkies')->pluck('department')->all();
            $seeds['position'] = DB::table('walkie_talkies')->pluck('position')->all();
            $seeds['ownership_type'] = array_merge(
                $seeds['ownership_type'],
                DB::table('walkie_talkies')->pluck('ownership_type')->all(),
                DB::table('walkie_talkies')->pluck('ownership_type_to_be')->all()
            );
        }

        $rows = [];
        foreach ($seeds as $category => $values) {
            $normalized = collect($values)
                ->map(fn ($value) => strtoupper(trim((string) $value)))
                ->filter()
                ->unique()
                ->values();

            foreach ($normalized as $value) {
                $rows[] = [
                    'category' => $category,
                    'value' => $value,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($rows, 200) as $chunk) {
            DB::table('wt_master_data')->insert($chunk);
        }
    }
};
