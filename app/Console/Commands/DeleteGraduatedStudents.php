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
        $oneYearAgo = Carbon::now()->subYear(1);

        // Tafuta wanafunzi waliomaliza zaidi ya mwaka 1 uliyopita na hawana status hai
        $students = Student::where('graduated', 1)
                    ->where('status', 0)
                    ->whereDate('updated_at', '<=', $oneYearAgo)
                    ->get();

        $deletedCount = 0;

        foreach ($students as $student) {
            // Hakikisha kuna jina la picha kwenye database
            if (!empty($student->image)) {
                $imagePath = public_path("assets/img/students/{$student->image}");

                // Angalia kama picha ipo na ifute
                if (File::exists($imagePath)) {
                    File::delete($imagePath);
                    $this->info("student photo {$student->id} ({$student->image}) deleted.");
                }
            }

            // Futa mwanafunzi kwenye database
            $student->delete();
            $deletedCount++;
        }

        // Output ujumbe wa jumla
        $this->info("Students $deletedCount who graduated 1 year ago was deleted with their photos.");
    }
}
