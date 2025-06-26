<?php

namespace App\Http\Controllers;

use App\Services\TimetableGenerator;
use App\Models\Grade;
use App\Models\Timetable;

class TimetableController extends Controller
{
    public function generate($classId)
    {
        $class = Grade::findOrFail($classId);
        $generator = new TimetableGenerator(auth()->user()->school_id);

        $timetable = $generator->generateForClass($classId);

        // Hifadhi kwenye database
        Timetable::updateOrCreate(
            ['class_id' => $classId, 'school_id' => auth()->user()->school_id],
            [
                'schedule' => json_encode($timetable),
                'effective_date' => now(),
                'expiry_date' => now()->addMonths(3)
            ]
        );

        return response()->json([
            'success' => true,
            'timetable' => $timetable
        ]);
    }

    public function view($classId)
    {
        $timetable = Timetable::where('class_id', $classId)
            ->where('school_id', auth()->user()->school_id)
            ->latest()
            ->first();

        return view('timetables.view', [
            'timetable' => json_decode($timetable->schedule, true),
            'class' => Grade::find($classId)
        ]);
    }

    protected function distributeCoursesWeekly($courses)
    {
        // Mfano: Hisabati 7 vipindi kwa wiki
        $requiredPeriods = [
            'Mathematics' => 7,
            'Kiswahili' => 7,
            'English' => 7,
            'Science' => 5,
            // Soma zingine
        ];

        $distribution = [];

        foreach ($courses as $course) {
            $subjectName = $course->subject->name;
            $required = $requiredPeriods[$subjectName] ?? 3; // Chukua 3 kama haijaainishwa

            $distribution[$subjectName] = [
                'course' => $course,
                'required' => $required,
                'assigned' => 0
            ];
        }

        return $distribution;
    }
}
