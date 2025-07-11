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
        'remarks',
        'verify_token',
        'qr_code_path',
    ];

    protected $dates = [
        'start_date' => 'datetime:Y-m-d',
        'end_date' => 'datetime:Y-m-d',
        'approved_at' => 'datetime:Y-m-d',
        'applied_at' => 'datetime:Y-m-d',
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
