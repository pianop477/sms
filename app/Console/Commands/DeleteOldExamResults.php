<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class DeleteOldExamResults extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:old-exam-results';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete exam result files older than a certain time period';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Path to the 'exam_results' folder
        $folderPath = public_path('exam_results');

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
            if ($fileModifiedTime->diffInHours($now) > 24) {
                // Delete the file if older than 2 minutes
                File::delete($file);

                // Inform the user that the file was deleted
                $this->info('Deleted: ' . $file->getFilename());
            }
        }

        $this->info('Old exam result files deletion process completed.');
    }
}
