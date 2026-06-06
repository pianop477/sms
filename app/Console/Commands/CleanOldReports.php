<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ReportJob;
use Illuminate\Support\Facades\File;

class CleanOldReports extends Command
{
    protected $signature = 'reports:clean';
    protected $description = 'Delete old report files and records older than 7 days';

    public function handle()
    {
        $oldJobs = ReportJob::where('created_at', '<', now()->subDays(7))
            ->where('status', 'completed')
            ->get();

        $count = 0;
        foreach ($oldJobs as $job) {
            if (file_exists($job->file_path)) {
                unlink($job->file_path);
            }
            $job->delete();
            $count++;
        }

        $this->info("Cleaned {$count} old reports.");
    }
}
