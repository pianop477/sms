<?php

namespace App\Services;

use App\Models\class_learning_courses;
use App\Models\ClassLearningCourse;
use App\Models\Grade;
use App\Models\school;
use App\Models\timetable_settings;
use App\Models\TimetableSetting;
use Carbon\Carbon;

class TimetableGenerator
{
    protected $school;
    protected $settings;
    protected $periodDuration;

    public function __construct($schoolId)
    {
        $this->school = school::findOrFail($schoolId);
        $this->settings = timetable_settings::where('school_id', $schoolId)->first();
        $this->periodDuration = [
            'lower' => $this->settings->lower_primary_period,
            'upper' => $this->settings->upper_primary_period
        ];
    }

    public function generateForClass($classId)
    {
        $class = Grade::findOrFail($classId);
        $courses = class_learning_courses::with('subject', 'teacher')
            ->where('class_id', $classId)
            ->where('status', 1)
            ->get();

        $isLowerPrimary = $class->class_level <= 2; // Kindergarten to Standard 2
        $periodMinutes = $isLowerPrimary ? $this->periodDuration['lower'] : $this->periodDuration['upper'];

        // Ratiba ya juma nzima
        $weeklySchedule = [];

        foreach ($this->settings->working_days as $day) {
            $dailySchedule = $this->generateDailySchedule($day, $courses, $periodMinutes);
            $weeklySchedule[$day] = $dailySchedule;
        }

        return $weeklySchedule;
    }

    protected function generateDailySchedule($day, $courses, $periodMinutes)
    {
        $startTime = Carbon::parse($this->settings->school_start_time);
        $endTime = Carbon::parse($this->settings->school_end_time);

        $schedule = [];
        $currentTime = $startTime->copy();

        // Ongeza vipindi vya kawaida
        while ($currentTime->lt($endTime)) {
            // Break ya chai (10:00 - 10:20)
            if ($currentTime->format('H:i') == '10:00') {
                $schedule[] = [
                    'type' => 'break',
                    'name' => 'Tea Break',
                    'start' => $currentTime->format('H:i'),
                    'end' => $currentTime->addMinutes($this->settings->tea_break_duration)->format('H:i'),
                    'duration' => $this->settings->tea_break_duration
                ];
                continue;
            }

            // Break ya chakula (12:30 - 13:00)
            if ($currentTime->format('H:i') == '12:30') {
                $schedule[] = [
                    'type' => 'break',
                    'name' => 'Lunch Break',
                    'start' => $currentTime->format('H:i'),
                    'end' => $currentTime->addMinutes($this->settings->lunch_break_duration)->format('H:i'),
                    'duration' => $this->settings->lunch_break_duration
                ];
                continue;
            }

            // Chagua somo la nasibu kwa siku hii
            $randomCourse = $courses->random();

            $schedule[] = [
                'type' => 'subject',
                'name' => $randomCourse->subject->ncourse_ame,
                'teacher' => $randomCourse->teacher->full_name,
                'start' => $currentTime->format('H:i'),
                'end' => $currentTime->addMinutes($periodMinutes)->format('H:i'),
                'duration' => $periodMinutes
            ];
        }

        return $schedule;
    }
}
