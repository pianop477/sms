<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TodRoster extends Model
{
    use HasFactory;

    protected $fillable = [
        'roster_id',
        'teacher_id',
        'start_date',
        'end_date',
        'created_by',
        'updated_by',
        'status',
        'is_completed',
    ];
}
