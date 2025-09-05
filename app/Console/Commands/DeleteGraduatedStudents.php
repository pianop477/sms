<?php

namespace App\Console\Commands;

use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

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
        try {
            $oneYearAgo = Carbon::now()->subYear();
            // $sixMonthsAgo = Carbon::now()->subMonth(6);
            $deletedCount = 0;

            // Pata wanafunzi waliomaliza zaidi ya mwaka mmoja uliopita
            Student::where('graduated', 1)
                ->where('status', 0)
                ->whereDate('updated_at', '<=', $oneYearAgo)
                ->chunk(50, function ($students) use (&$deletedCount) {
                    foreach ($students as $student) {
                        // Angalia kama mwanafunzi ana picha na ifute
                        if (!empty($student->image)) {
                            $imagePath = public_path("assets/img/students/{$student->image}");

                            if (File::exists($imagePath)) {
                                File::delete($imagePath);
                                // Log::info("Student photo deleted: ID {$student->id}, Image: {$student->image}");
                            }
                        }

                        // Futa mwanafunzi kutoka kwenye database
                        $student->delete();
                        $deletedCount++;
                    }
                });

            // Ujumbe wa mwisho
            $this->info("Deleted $deletedCount students who graduated a year ago with their photos.");
        } catch (\Exception $e) {
            // Log::error("Error deleting old graduates: " . $e->getMessage());
            $this->error("An error occurred while deleting old students.");
        }
    }
}
