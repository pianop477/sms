<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WebAuthnCredential;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CleanOldWebAuthnDevices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webauthn:clean-old-devices
                            {--months=6 : Delete devices older than specified months}
                            {--force : Force deletion without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete WebAuthn devices that haven\'t been used for more than 6 months';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $months = $this->option('months');
        $cutoffDate = Carbon::now()->subMonths($months);

        $this->info("Deleting WebAuthn devices last created before: {$cutoffDate->format('Y-m-d H:i:s')}");

        // Get old devices
        $oldDevices = WebAuthnCredential::where('created_at', '<', $cutoffDate)
            ->orWhereNull('created_at') // Devices never used
            ->get();

        $count = $oldDevices->count();

        if ($count === 0) {
            $this->info('No old devices found to delete.');
            return 0;
        }

        $this->warn("Found {$count} device(s) that are older than {$months} months.");

        // Show devices to be deleted
        $this->table(
            ['ID', 'User ID', 'Device Name', 'Last created', 'Created At'],
            $oldDevices->map(function ($device) {
                return [
                    $device->id,
                    $device->user_id,
                    $device->name ?? 'Unnamed',
                    $device->created_at?->format('Y-m-d') ?? 'Never',
                    $device->created_at->format('Y-m-d'),
                ];
            })
        );

        // Confirm deletion unless force flag is used
        if (!$this->option('force') && !$this->confirm('Do you wish to delete these devices?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        // Delete devices
        $deleted = $oldDevices->each->delete();

        // Log the cleanup action
        Log::info("Cleaned up {$deleted->count()} old WebAuthn devices", [
            'cutoff_date' => $cutoffDate,
            'months' => $months
        ]);

        $this->info("Successfully deleted {$deleted->count()} old device(s).");

        return 0;
    }
}
