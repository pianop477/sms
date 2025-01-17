<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class class_learning_courses extends Model
{
    use HasFactory;

    protected $fillable = ['class_id', 'teacher_id', 'course_id', 'school_id', 'status'];

    protected $guard = ['id', 'created_at', 'updated_at'];
}
