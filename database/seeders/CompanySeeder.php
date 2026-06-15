<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('companies')->upsert(
            [
                ['code' => 'FBSB', 'name' => '4300 FGV Bulkers Sdn Bhd'],
                ['code' => 'FJB',  'name' => '4810 FGV Johor Bulkers Sdn Bhd'],
                ['code' => 'FGT',  'name' => '4310 FGV Grains Terminal Sdn'],
                ['code' => 'LBSB', 'name' => '4850 Langsat Bulkers Sdn Bhd'],
            ],
            ['code'],
            ['name']
        );
    }
}
