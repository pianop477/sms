<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CleanupOldReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:old-reports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete exported user-agent report files older than 1 week';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $reportPath = storage_path('app/reports');

        if (!File::exists($reportPath)) {
            $this->info("No reports directory found.");
            return 0;
        }

        $deleted = 0;
        $now = Carbon::now();

        foreach (File::files($reportPath) as $file) {
            if ($now->diffInDays(Carbon::createFromTimestamp($file->getMTime())) > 7) {
                File::delete($file->getRealPath());
                $deleted++;
            }
        }

        if ($deleted > 0) {
            $this->info("Deleted $deleted report file(s) older than 1 week.");
        } else {
            $this->info("No old report files to delete.");
        }

        return 0;
    }
}
