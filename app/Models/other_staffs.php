<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class other_staffs extends Model
{
    use HasFactory;

    protected $fillable =[
        'staff_id',
        'first_name',
        'last_name',
        'gender',
        'date_of_birth',
        'phone',
        'email',
        'street_address',
        'job_title',
        'joining_year',
        'educational_level',
        'profile_image',
        'usertype',
        'status',
    ];

    protected $table = 'other_staffs';
    protected $primaryKey = 'id';
    protected $casts = [
        'date_of_birth' => 'date',
        'date_of_birth' => 'date:Y-m-d'
    ];
}
