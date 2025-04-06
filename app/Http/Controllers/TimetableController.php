<?php

namespace App\Http\Controllers;

use App\Models\class_learning_courses;
use Illuminate\Http\Request;
use App\Models\SchoolTimetableSetting;
use App\Models\Timetable;
use App\Models\ClassLearningCourse;
use App\Models\school_timetable_settings;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use RealRashid\SweetAlert\Facades\Alert;
use Vinkla\Hashids\Facades\Hashids;

class TimetableController extends Controller
{
    public function showSettingsForm()
    {
        $timetableSettings = school_timetable_settings::all();
        return view('Timetable.settings', compact('timetableSettings'));
    }

    public function storeSettings(Request $request)
    {
        $request->validate([
            'day_start_time' => 'required',
            'period_duration' => 'required|integer',
            'day_end_time' => 'required',
            'active_days' => 'required|array',
        ]);

        $schoolId = Auth::user()->school_id;

        school_timetable_settings::updateOrCreate(
            ['school_id' => $schoolId],
            $request->only([
                'day_start_time', 'period_duration',
                'first_break_start', 'first_break_end',
                'second_break_start', 'second_break_end',
                'day_end_time', 'active_days'
            ])
        );

        Alert()->toast('settings saved!', 'success');
        return back();
    }

    public function showGenerator()
    {
        return view('timetable.generate');
    }

    public function generateTimetable()
    {
        $schoolId = Auth::user()->school_id;

        $settings = school_timetable_settings::where('school_id', $schoolId)->first();
        if (!$settings) {
            Alert()->toast('Please set up timetable settings first!', 'error');
            return back();
        }

        $assignmentsGrouped = class_learning_courses::where('school_id', $schoolId)->get()->groupBy('class_id');

        $start = Carbon::createFromTimeString($settings->day_start_time);
        $end = Carbon::createFromTimeString($settings->day_end_time);
        $period = $settings->period_duration;

        // Clean existing timetable
        Timetable::where('school_id', $schoolId)->delete();

        foreach ($assignmentsGrouped as $classId => $assignments) {
            foreach ($settings->active_days as $day) {
                $currentTime = $start->copy();

                // Shuffle assignments per day per class
                $shuffledAssignments = $assignments->shuffle();
                $assignmentIndex = 0;

                while ($currentTime->lt($end)) {
                    $nextTime = $currentTime->copy()->addMinutes($period);

                    // Skip if in break time
                    if (
                        ($settings->first_break_start && $currentTime->between(Carbon::parse($settings->first_break_start), Carbon::parse($settings->first_break_end))) ||
                        ($settings->second_break_start && $currentTime->between(Carbon::parse($settings->second_break_start), Carbon::parse($settings->second_break_end)))
                    ) {
                        $currentTime = $nextTime;
                        continue;
                    }

                    // Loop back if we run out of subjects
                    if ($assignmentIndex >= $shuffledAssignments->count()) {
                        $assignmentIndex = 0;
                        $shuffledAssignments = $shuffledAssignments->shuffle();
                    }

                    $assignment = $shuffledAssignments[$assignmentIndex];

                    // Ensure teacher not double-booked
                    $alreadyAssigned = Timetable::where('day_of_week', $day)
                        ->where('start_time', $currentTime->format('H:i:s'))
                        ->where('teacher_id', $assignment->teacher_id)
                        ->exists();

                    if (!$alreadyAssigned) {
                        Timetable::create([
                            'school_id' => $schoolId,
                            'class_id' => $classId,
                            'teacher_id' => $assignment->teacher_id,
                            'course_id' => $assignment->course_id,
                            'day_of_week' => $day,
                            'start_time' => $currentTime->format('H:i:s'),
                            'end_time' => $nextTime->format('H:i:s'),
                        ]);
                        $assignmentIndex++;
                    }

                    $currentTime = $nextTime;
                }
            }
        }

        // Pata timetable baada ya kuigenerate
        $classTimetables = Timetable::with(['course', 'teacher', 'class'])
            ->where('school_id', $schoolId)
            ->orderBy('class_id')
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get()
            ->groupBy('class_id');

        return view('timetable.generated', compact('classTimetables', 'settings'));
    }

    public function deleteTimetable ($id)
    {
        $timetable_id = Hashids::decode($id);
        $timetable = school_timetable_settings::find($timetable_id[0]);

        if ($timetable) {
            $timetable->delete();
            Alert()->toast('Timetable settings deleted successfully!', 'success');
            return back();
        } else {
            Alert()->toast('Timetable settings not found!', 'error');
            return back();
        }
    }
}

