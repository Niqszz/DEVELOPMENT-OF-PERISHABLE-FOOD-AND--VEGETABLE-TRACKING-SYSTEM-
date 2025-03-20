<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearEnvironmentSensorLogs extends Command
{
    protected $signature = 'logs:clear-environment-sensor';
    protected $description = 'Clear all environment sensor logs in user directories every 24 hours';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $baseDir = public_path('profile'); // Base directory for user profiles

        // Loop through each user directory
        foreach (File::directories($baseDir) as $userDir) {
            $logDir = $userDir . '/log/environment_sensor_log';

            // Check if the log directory exists and clear logs if so
            if (File::exists($logDir)) {
                File::cleanDirectory($logDir); // Remove all files in the directory
                $this->info("Cleared logs in: " . $logDir);
            }
        }

        $this->info('Environment sensor logs cleared successfully.');
        return 0;
    }
}
