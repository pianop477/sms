<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class DeleteOldAttendanceReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:old-attendance-reports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete attendance report files older than 24 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        // Path to the 'attendances' folder
        $folderPath = public_path('attendances');

        // Check if the folder exists
        if (!File::exists($folderPath)) {
            $this->info('The folder does not exist.');
            return;
        }

        // Get all files in the folder
        $files = File::files($folderPath);

        // Get the current time
        $now = Carbon::now();

        // Loop through each file in the directory
        foreach ($files as $file) {
            // Get the file's last modification time
            $fileModifiedTime = Carbon::createFromTimestamp(File::lastModified($file));

            // Check if the file is older than 24 hours
            if ($fileModifiedTime->diffInHours($now) > 24) {
                // Delete the file if older than 24 hours
                File::delete($file);

                // Inform the user that the file was deleted
                $this->info('Deleted: ' . $file->getFilename());
            }
        }

        $this->info('Old attendance report files deletion process completed.');
    }
}
