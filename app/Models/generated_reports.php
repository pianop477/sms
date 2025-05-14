<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class generated_reports extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'class_id',
        'school_id',
        'exam_dates',
        'combine_option',
        'created_by',
        'term',
        'status',
    ];

    protected $casts = [
        'exam_dates' => 'array', // Automatically convert JSON to array
    ];
}
