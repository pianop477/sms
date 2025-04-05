<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'class_id',
        'teacher_id',
        'course_id',
        'day_of_week',
        'start_time',
        'end_time',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function course()
    {
        return $this->belongsTo(Subject::class, 'course_id');
    }

    public function class()
    {
        return $this->belongsTo(Grade::class, 'class_id');
    }

}
