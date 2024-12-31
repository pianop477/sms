<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Examination_result extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_id',
        'course_id',
        'class_id',
        'teacher_id',
        'exam_type_id',
        'school_id',
        'exam_date',
        'Exam_term',
        'score',
        'marking_style',
        'status'
    ];

    protected $guarded = ['id'];
}
