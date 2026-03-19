<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class school extends Model
{
    use HasFactory;
    protected $fillable = [
        'school_name',
        'school_reg_no',
        'postal_addres',
        'postal_name',
        'country',
        'logo',
        'abbriv_code',
        'status',
        'reg_date',
        'service_start_date',
        'service_end_date',
        'service_duration', 'package',
        'sender_id', 'school_email', 'school_phone', 'school_alternative_phone'
    ];

    protected $guarded = ['id', 'created_at', 'updated_at'];


    /**
     * Get all of the comments for the school
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function User()
    {
        return $this->hasMany(User::class, 'school_id', 'id');
    }

    /**
     * Get all of the comments for the school
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'school_id');
    }
}
