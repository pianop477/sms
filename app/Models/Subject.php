<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'teacher_id',
        'school_id',
        'course_name',
        'course_code',
    ];

    protected $guarded = ['id'];
}
