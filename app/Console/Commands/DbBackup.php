<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class DbBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a snapshot of the SQLite database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dbPath = database_path('database.sqlite');
        
        if (!File::exists($dbPath)) {
            $this->error('Database file not found at ' . $dbPath);
            return;
        }

        $backupDir = storage_path('app/backups');
        
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $backupPath = $backupDir . '/backup_' . $timestamp . '.sqlite';

        File::copy($dbPath, $backupPath);

        $this->info("Database backed up successfully to: {$backupPath}");

        // Cleanup backups older than 4 days
        $files = File::files($backupDir);
        $retentionDate = Carbon::now()->subDays(4);

        $deletedCount = 0;
        foreach ($files as $file) {
            $lastModified = Carbon::createFromTimestamp($file->getMTime());
            if ($lastModified->lessThan($retentionDate)) {
                File::delete($file->getPathname());
                $deletedCount++;
            }
        }

        if ($deletedCount > 0) {
            $this->info("Cleaned up {$deletedCount} old backup(s).");
        }
    }
}
