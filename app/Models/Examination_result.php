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

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function subject()
    {
        return $this->hasMany(Examination_result::class, 'course_id');
    }

    public function Student()
    {
        return $this->hasMany(Examination_result::class, 'student_id');
    }

    public function class()
    {
        return $this->hasMany(Examination_result::class, 'class_id');
    }

    public function school()
    {
        return $this->hasMany(Examination_result::class, 'school_id');
    }

    public function Teacher()
    {
        return $this->hasMany(Examination_result::class, 'teacher_id');
    }

    public function Examination()
    {
        return $this->hasMany(Examination_result::class, 'exam_type_id');
    }
}
