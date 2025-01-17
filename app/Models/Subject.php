<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'course_name',
        'course_code',
        'status'
    ];

    protected $guarded = ['id', 'created_at', 'updated_at'];
}
