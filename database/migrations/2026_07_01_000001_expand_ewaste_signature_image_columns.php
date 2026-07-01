<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        foreach ($this->signatureColumns() as $column) {
            if ($driver === 'sqlsrv') {
                DB::statement("ALTER TABLE ewaste_items ALTER COLUMN [{$column}] NVARCHAR(MAX) NULL");
            } elseif ($driver === 'mysql') {
                DB::statement("ALTER TABLE ewaste_items MODIFY COLUMN `{$column}` LONGTEXT NULL");
            }
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        foreach ($this->signatureColumns() as $column) {
            if ($driver === 'sqlsrv') {
                DB::statement("ALTER TABLE ewaste_items ALTER COLUMN [{$column}] NVARCHAR(255) NULL");
            } elseif ($driver === 'mysql') {
                DB::statement("ALTER TABLE ewaste_items MODIFY COLUMN `{$column}` VARCHAR(255) NULL");
            }
        }
    }

    private function signatureColumns(): array
    {
        return [
            'writeoff_sig_img',
            'hou_sig_img',
            'gm_sig_img',
            'ceo_sig_img',
        ];
    }
};
