<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'teacher_id',
        'class_id',
        'attendance_status',
        'attendance_date',
        'school_id'
    ];

    protected $guarded = ['id'];
}
