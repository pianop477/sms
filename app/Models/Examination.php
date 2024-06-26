<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Examination extends Model
{
    use HasFactory;
    protected $fillable = [
        'exam_type', 'school_id', 'status',
    ];

    protected $guarded = ['id'];
}
