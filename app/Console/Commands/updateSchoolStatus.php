<?php

namespace App\Console\Commands;

use App\Models\school;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class updateSchoolStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'school:check-active-school';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and update school status if the service end date has passed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $today = Carbon::now();

            // Tafuta shule zote zilizomaliza muda wa huduma
            $expiredSchools = School::where('service_end_date', '<', $today)
                                    ->where('status', 1)
                                    ->get();

            if ($expiredSchools->isEmpty()) {
                $this->info('No expired schools found.');
                return;
            }

            foreach ($expiredSchools as $school) {
                $school->update(['status' => 2]); // Set status to inactive

                // Disable all users of the school
                User::where('school_id', $school->id)
                    ->where('status', 1)
                    ->update(['status' => 0]);

                // Log::info("School {$school->school_name} (ID: {$school->id}) has been disabled.");
            }

            $this->info(count($expiredSchools) . ' schools have been updated to inactive status.');
        } catch (\Exception $e) {
            // Log::error('Error in schools:disable-expired command: ' . $e->getMessage());
            $this->error('An error occurred while disabling expired schools.');
        }
    }
}
