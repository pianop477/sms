<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class DeleteOldContractFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contracts:delete-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete contract application files older than 1 year from the last update';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $directoryPath = storage_path('app/public/contracts/contract_application'); // Path to your files directory

        if (!File::exists($directoryPath)) {
            $this->error('Directory does not exist!');
            return;
        }

        // Get all files in the directory
        $files = File::allFiles($directoryPath);

        $deletedCount = 0; // To keep track of how many files were deleted

        // Loop through the files and check their last modified date
        foreach ($files as $file) {
            // Get the last modified timestamp of the file
            $lastModified = Carbon::createFromTimestamp(File::lastModified($file));
            $oneYearAgo = Carbon::now()->subYear(); // Calculate the date 1 year ago

            // If the file hasn't been updated in the last year, delete it
            if ($lastModified->lt($oneYearAgo)) {
                // Delete the file
                File::delete($file);
                $deletedCount++;
                $this->info("Deleted: " . $file->getFilename());
            }
        }

        $this->info("$deletedCount files deleted successfully.");
    }
}
