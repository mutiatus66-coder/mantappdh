<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class DbRollback extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:rollback {time : The time to rollback to (e.g., 10m, 30m, 6h, 24h, 3d)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback the database to a specific time in the past';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $timeArg = $this->argument('time');
        $targetTime = $this->parseTimeArg($timeArg);

        if (!$targetTime) {
            $this->error('Invalid time format. Use something like 10m, 30m, 6h, 24h, 3d.');
            return;
        }

        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            $this->error('No backups found in ' . $backupDir);
            return;
        }

        $files = File::files($backupDir);
        if (empty($files)) {
            $this->error('No backup files found.');
            return;
        }

        $closestBackup = null;
        $smallestDiff = null;

        foreach ($files as $file) {
            if ($file->getExtension() !== 'sqlite') continue;
            
            // We only want to look at main backups, not pre_rollback safety backups, unless necessary.
            // Let's filter by filename starting with 'backup_'
            if (!str_starts_with($file->getFilename(), 'backup_')) continue;

            $modifiedTime = Carbon::createFromTimestamp($file->getMTime());
            $diffInSeconds = abs($modifiedTime->diffInSeconds($targetTime));

            if (is_null($smallestDiff) || $diffInSeconds < $smallestDiff) {
                $smallestDiff = $diffInSeconds;
                $closestBackup = $file;
            }
        }

        if (!$closestBackup) {
            $this->error('Could not find a suitable backup file.');
            return;
        }

        $closestTime = Carbon::createFromTimestamp($closestBackup->getMTime());
        
        $this->info("Target rollback time: " . $targetTime->format('Y-m-d H:i:s'));
        $this->info("Found closest backup: " . $closestTime->format('Y-m-d H:i:s'));
        $this->warn("This will OVERWRITE your current database with the backup from " . $closestTime->diffForHumans() . ".");
        
        if ($this->confirm('Do you wish to continue?', false)) {
            $dbPath = database_path('database.sqlite');
            
            // Optionally backup the current state before rolling back just in case
            $safetyBackupPath = $backupDir . '/pre_rollback_' . Carbon::now()->format('Y-m-d_H-i-s') . '.sqlite';
            File::copy($dbPath, $safetyBackupPath);
            $this->info("Created safety backup of current state at: " . basename($safetyBackupPath));

            File::copy($closestBackup->getPathname(), $dbPath);
            $this->info('Database rolled back successfully!');
        } else {
            $this->info('Rollback cancelled.');
        }
    }

    private function parseTimeArg($arg)
    {
        $now = Carbon::now();
        
        if (preg_match('/^(\d+)(m|h|d)$/i', $arg, $matches)) {
            $value = (int)$matches[1];
            $unit = strtolower($matches[2]);
            
            return match($unit) {
                'm' => $now->copy()->subMinutes($value),
                'h' => $now->copy()->subHours($value),
                'd' => $now->copy()->subDays($value),
                default => null,
            };
        }
        
        return null;
    }
}
