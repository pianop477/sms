<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class compiled_results extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'class_id', 'exam_type_id', 'source_results', 'compiled_term',
        'total_score', 'average_score', 'status', 'school_id', 'course_id'
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'source_results' => 'array',
    ];
}
