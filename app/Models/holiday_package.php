<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class holiday_package extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'class_id',
        'title',
        'description',
        'year',
        'term',
        'file_path',
        'release_date',
        'due_date',
        'is_active',
        'download_count',
        'issued_by'
    ];

    protected $table = 'holiday_packages';
    protected $primaryKey = 'id';

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
