<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class daily_report_attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_report_id',
        'class_id',
        'group',
        'registered_boys',
        'registered_girls',
        'present_boys',
        'present_girls',
        'absent_boys',
        'absent_girls',
        'permission_boys',
        'permission_girls',
    ];
}
