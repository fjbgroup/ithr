<?php

namespace App\Http\Controllers\WT\Admin;

use App\Http\Controllers\WT\Controller;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class DatabaseBackupController extends Controller
{
    public function download()
    {
        $connection = config('database.default');
        $database = config("database.connections.{$connection}");

        abort_unless(($database['driver'] ?? null) === 'mysql', 400, 'Database backup only supports MySQL.');

        $databaseName = (string) ($database['database'] ?? '');
        abort_if($databaseName === '', 400, 'Database name is missing.');

        $backupDirectory = storage_path('app/backups');
        File::ensureDirectoryExists($backupDirectory);

        $timestamp = now()->format('Ymd_His');
        $backupPath = $backupDirectory.DIRECTORY_SEPARATOR.$databaseName.'_'.$timestamp.'.sql';
        $mysqldump = $this->mysqldumpPath();

        $command = [
            $mysqldump,
            '--host='.(string) ($database['host'] ?? '127.0.0.1'),
            '--port='.(string) ($database['port'] ?? 3306),
            '--user='.(string) ($database['username'] ?? 'root'),
            '--single-transaction',
            '--routines',
            '--triggers',
            '--databases',
            $databaseName,
            '--result-file='.$backupPath,
        ];

        $password = (string) ($database['password'] ?? '');

        if ($password !== '') {
            $command[] = '--password='.$password;
        }

        $process = new Process($command);
        $process->setTimeout(120);
        $process->run();

        abort_unless($process->isSuccessful() && File::exists($backupPath), 500, 'Database backup failed.');

        return response()->download($backupPath)->deleteFileAfterSend(true);
    }

    private function mysqldumpPath(): string
    {
        $xamppPath = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';

        if (PHP_OS_FAMILY === 'Windows' && File::exists($xamppPath)) {
            return $xamppPath;
        }

        return 'mysqldump';
    }
}


