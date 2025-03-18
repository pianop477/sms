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
    protected $signature = 'school:update-school-status';

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
        //

        $today = Carbon::now();

        //fetch all expired schools
        $expiredSchools = school::where('service_end_date', '<', $today)->where('status', 1)->get();

        foreach ($expiredSchools as $school) {
            $school->status = 2; // Set status to inactive
            $school->save();

            //disable all users of the school
            User::where('school_id', $school->id)->where('status', 1)->update(['status' => 0]);
        }

        $this->info(count($expiredSchools) . ' schools have been updated to inactive status.');
    }
}
