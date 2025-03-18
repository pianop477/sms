<?php

namespace App\Console\Commands;

use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteGraduatedStudents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'students:delete-graduated-students';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'delete student who graduated one year ago';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $oneYearAgo = Carbon::now()->subYear(6);

        $deletedStudents = Student::where('graduated', 1)
                        ->where('status', 0)
                        ->whereDate('updated_at', '<=', $oneYearAgo) // Assuming you have a 'graduation_date' column
                        ->delete();

        // Output the result to the console
        $this->info("Deleted $deletedStudents students who graduated more than 6 months ago.");
    }
}
