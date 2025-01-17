<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Class_teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id', 'class_id', 'group', 'school_id'
    ];

    protected $guarded = ['id', 'created_at', 'updated_at'];
}
