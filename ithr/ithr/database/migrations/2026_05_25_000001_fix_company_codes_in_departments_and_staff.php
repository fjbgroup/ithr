<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $map = [
            '4300 FGV Bulkers Sdn Bhd'       => 'FBSB',
            '4810 FGV Johor Bulkers Sdn Bhd'  => 'FJB',
            '4310 FGV Grains Terminal Sdn'    => 'FGT',
            '4850 Langsat Bulkers Sdn Bhd'    => 'LBSB',
        ];

        foreach ($map as $fullName => $code) {
            DB::table('departments')->where('company', $fullName)->update(['company' => $code]);
            DB::table('staff')->where('company', $fullName)->update(['company' => $code]);
        }
    }

    public function down(): void
    {
        $map = [
            'FBSB' => '4300 FGV Bulkers Sdn Bhd',
            'FJB'  => '4810 FGV Johor Bulkers Sdn Bhd',
            'FGT'  => '4310 FGV Grains Terminal Sdn',
            'LBSB' => '4850 Langsat Bulkers Sdn Bhd',
        ];

        foreach ($map as $code => $fullName) {
            DB::table('departments')->where('company', $code)->update(['company' => $fullName]);
            DB::table('staff')->where('company', $code)->update(['company' => $fullName]);
        }
    }
};
