<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class deleteOldReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:student-old-reports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete old reports of students from the server';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        // Path to the 'exam_results' folder
        $folderPath = storage_path('reports');

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

            // Check if the file is older than 2 minutes
            if ($fileModifiedTime->diffInHours($now) > 3) {
                // Delete the file if older than 2 minutes
                File::delete($file);

                // Inform the user that the file was deleted
                $this->info('Deleted: ' . $file->getFilename());
            }
        }

        $this->info('Old reports files deletion process completed.');

    }
}
