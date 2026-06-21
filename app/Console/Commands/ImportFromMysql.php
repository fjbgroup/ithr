<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PDO;

class ImportFromMysql extends Command
{
    protected $signature = 'import:from-mysql';
    protected $description = 'Copy all data from old MySQL fjb_system into SQL Server fjb_system';

    // Tables in dependency order (parents before children)
    private array $tables = [
        'departments',
        'companies',
        'staff',
        'users',
        'positions',
        'transport_modes',
        'meeting_rooms',
        'room_pics',
        'room_bookings',
        'notifications',
        'training_courses',
        'training_attendances',
        'training_feedbacks',
        'update_requests',
        'family_members',
        'staff_ir',
        'business_travel',
        'activity_logs',
        'asset_groups',
        'asset_classes',
        'inventory_items',
        'ewaste_items',
        'ewaste_requests',
        'disposal_items',
        'non_it_assets',
        'add_asset_requests',
        'delete_requests',
        'edit_asset_requests',
        'it_activity_log',
        'it_notifications',
        'it_request_forms',
        'email_settings',
        'walkie_talkies',
        'access_requests',
        'walkie_talkie_handovers',
        'maintenance_records',
        'spare',
        'user_activity_logs',
        'wt_notifications',
        'wt_database_notifications',
    ];

    // Tables whose primary key is not named 'id'
    private array $customPk = [
        'maintenance_records'  => 'maintenance_id',
        'walkie_talkies'       => 'walkie_id',
        'email_settings'       => 'setting_key',
    ];

    public function handle(): int
    {
        $src = DB::connection('mysql_source');
        $pdo = DB::connection('sqlsrv')->getPdo();

        $this->info('Preparing SQL Server...');
        $this->fixNullableUniqueIndexes($pdo);
        $this->disableAllConstraints($pdo);
        $this->clearAllTables($pdo);

        $this->info('Starting import...');
        $this->newLine();

        foreach ($this->tables as $table) {
            if (!$this->mysqlTableExists($src, $table)) {
                $this->line("  <fg=yellow>SKIP</>  {$table} (not in MySQL)");
                continue;
            }

            if (!$this->sqlsrvTableExists($pdo, $table)) {
                $this->line("  <fg=yellow>SKIP</>  {$table} (not in SQL Server)");
                continue;
            }

            $count = $src->table($table)->count();
            if ($count === 0) {
                $this->line("  <fg=gray>EMPTY</> {$table}");
                continue;
            }

            $this->output->write("  Importing {$table} ({$count} rows)...");

            try {
                $hasIdentity = $this->hasIdentityColumn($pdo, $table);
                if ($hasIdentity) {
                    $pdo->exec("SET IDENTITY_INSERT [{$table}] ON");
                }

                $pk = $this->customPk[$table] ?? 'id';
                $src->table($table)->orderBy($pk)->chunk(500, function ($rows) use ($pdo, $table) {
                    $rows = array_map(fn($r) => (array) $r, $rows->all());
                    if (empty($rows)) return;

                    $columns = array_keys($rows[0]);
                    $colList = implode(', ', array_map(fn($c) => "[{$c}]", $columns));
                    $placeholders = implode(', ', array_fill(0, count($columns), '?'));
                    $sql = "INSERT INTO [{$table}] ({$colList}) VALUES ({$placeholders})";

                    $stmt = $pdo->prepare($sql);
                    foreach ($rows as $row) {
                        $stmt->execute(array_values($row));
                    }
                });

                if ($hasIdentity) {
                    $pdo->exec("SET IDENTITY_INSERT [{$table}] OFF");
                }
                $this->output->writeln(' <fg=green>DONE</>');
            } catch (\Throwable $e) {
                try { $pdo->exec("SET IDENTITY_INSERT [{$table}] OFF"); } catch (\Throwable) {}
                $this->output->writeln(' <fg=red>FAIL</>');
                $this->warn('    ' . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info('Re-enabling constraints...');
        $this->enableAllConstraints($pdo);

        $this->info('Done! Check any FAIL lines above.');
        return self::SUCCESS;
    }

    private function fixNullableUniqueIndexes(PDO $pdo): void
    {
        // SQL Server unique indexes treat NULL = NULL (only one NULL allowed).
        // Recreate as filtered indexes (WHERE col IS NOT NULL) for nullable unique columns.
        $filtered = [
            'users' => [
                'users_staff_id_unique' => 'staff_id',
            ],
            'training_attendances' => [
                'training_attendances_qr_token_unique' => 'qr_token',
            ],
        ];

        foreach ($filtered as $table => $indexes) {
            foreach ($indexes as $indexName => $column) {
                try {
                    $pdo->exec("IF EXISTS (
                        SELECT 1 FROM sys.indexes
                        WHERE name = '{$indexName}' AND object_id = OBJECT_ID('{$table}')
                    ) DROP INDEX [{$indexName}] ON [{$table}]");

                    $pdo->exec("CREATE UNIQUE INDEX [{$indexName}]
                        ON [{$table}] ([{$column}])
                        WHERE [{$column}] IS NOT NULL");
                } catch (\Throwable $e) {
                    $this->warn("Could not fix index {$indexName}: " . $e->getMessage());
                }
            }
        }
    }

    private function disableAllConstraints(PDO $pdo): void
    {
        foreach ($this->tables as $table) {
            try {
                $pdo->exec("IF OBJECT_ID('{$table}') IS NOT NULL
                    ALTER TABLE [{$table}] NOCHECK CONSTRAINT ALL");
            } catch (\Throwable) {}
        }
    }

    private function enableAllConstraints(PDO $pdo): void
    {
        foreach ($this->tables as $table) {
            try {
                $pdo->exec("IF OBJECT_ID('{$table}') IS NOT NULL
                    ALTER TABLE [{$table}] WITH CHECK CHECK CONSTRAINT ALL");
            } catch (\Throwable) {}
        }
    }

    private function clearAllTables(PDO $pdo): void
    {
        foreach (array_reverse($this->tables) as $table) {
            try {
                $pdo->exec("IF OBJECT_ID('{$table}') IS NOT NULL DELETE FROM [{$table}]");
            } catch (\Throwable) {}
        }
    }

    private function mysqlTableExists($connection, string $table): bool
    {
        try {
            $connection->table($table)->limit(1)->get();
            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    private function sqlsrvTableExists(PDO $pdo, string $table): bool
    {
        $stmt = $pdo->query("SELECT OBJECT_ID('{$table}')");
        $result = $stmt->fetchColumn();
        return $result !== null;
    }

    private function hasIdentityColumn(PDO $pdo, string $table): bool
    {
        $stmt = $pdo->query(
            "SELECT COUNT(*) FROM sys.columns
             WHERE object_id = OBJECT_ID('{$table}') AND is_identity = 1"
        );
        return (int) $stmt->fetchColumn() > 0;
    }
}
