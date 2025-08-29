<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class daily_report_details extends Model
{
    use HasFactory;

    protected $fillable = [
        'tod_roster_id',
        'report_date',
        'parade',
        'break_time',
        'lunch_time',
        'teachers_attendance',
        'student_attendance',
        'event',
        'tod_remarks',
        'headteacher_comment',
    ];
}
