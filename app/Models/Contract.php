<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'school_id',
        'contract_type',
        'start_date',
        'end_date',
        'duration',
        'application_file',
        'status',
        'approved_at',
        'applied_at',
        'remarks'
    ];

    protected $dates = [
        'start_date',
        'end_date',
        'approved_at',
        'applied_at',
    ];

    protected $casts = [
        'status' => 'string',
        'contract_type' => 'string',
    ];

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];
}
