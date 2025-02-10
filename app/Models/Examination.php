<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Examination extends Model
{
    use HasFactory;
    protected $fillable = [
        'exam_type', 'school_id', 'status', 'symbolic_abbr'
    ];

    protected $guarded = ['id', 'created_at', 'updated_at'];
}
