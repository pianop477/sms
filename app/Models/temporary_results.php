<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class temporary_results extends Model
{
    use HasFactory;

    protected $fillable = [
    'student_id',
    'course_id',
    'class_id',
    'teacher_id',
    'exam_type_id',
    'school_id',
    'score',
    'exam_term',
    'marking_style',
    'status',
    'exam_date',
    'expiry_date',

    ];

    protected $guarded = ['id', 'updated_at', 'created_at'];
}
