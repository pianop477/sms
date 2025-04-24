<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ShowBlockedUserAgents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'security:blocked-user-agents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display all login attempts blocked due to suspicious User-Agent strings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $logPath = storage_path('logs/laravel.log');

        if (!File::exists($logPath)) {
            $this->error("Log file not found.");
            return 1;
        }

        $lines = File::lines($logPath)
                    ->filter(fn($line) => str_contains($line, 'Blocked suspicious user agent'))
                    ->values();

        if ($lines->isEmpty()) {
            $this->info("No suspicious user agents found.");
            return 0;
        }

        $this->info("Found {$lines->count()} blocked entries.");

        // Ask export format
        $format = $this->choice('Choose export format', ['none', 'csv', 'json'], 0);

        if ($format !== 'none') {
            $filename = 'blocked_user_agents_' . now()->format('Ymd_His') . '.' . $format;
            $filepath = storage_path("app/reports/$filename");

            File::ensureDirectoryExists(storage_path('app/reports'));

            if ($format === 'csv') {
                $csvContent = "timestamp,message\n";
                foreach ($lines as $line) {
                    // Extract timestamp & message
                    preg_match('/^\[([^\]]+)\] (.+)$/', $line, $matches);
                    $csvContent .= "\"{$matches[1]}\",\"{$matches[2]}\"\n";
                }
                File::put($filepath, $csvContent);
            }

            if ($format === 'json') {
                $entries = [];
                foreach ($lines as $line) {
                    preg_match('/^\[([^\]]+)\] (.+)$/', $line, $matches);
                    $entries[] = [
                        'timestamp' => $matches[1] ?? '',
                        'message' => $matches[2] ?? $line,
                    ];
                }
                File::put($filepath, json_encode($entries, JSON_PRETTY_PRINT));
            }

            $this->info("Exported to: $filepath");
        }

        return 0;
    }
}
