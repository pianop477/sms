<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class school_timetable_settings extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'day_start_time',
        'period_duration',
        'first_break_start',
        'first_break_end',
        'second_break_start',
        'second_break_end',
        'day_end_time',
        'active_days',
    ];

    protected $casts = [
        'active_days' => 'array', // handle JSON array as PHP array
        'day_start_time' => 'datetime:H:i',
        'day_end_time' => 'datetime:H:i',
        'first_break_start' => 'datetime:H:i',
        'first_break_end' => 'datetime:H:i',
        'second_break_start' => 'datetime:H:i',
        'second_break_end' => 'datetime:H:i',
    ];
}
