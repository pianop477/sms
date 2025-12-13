<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transport extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_name',
        'gender',
        'phone',
        'bus_no',
        'routine',
        'school_id',
        'staff_id',
        'email',
        'job_title',
        'educational_level',
        'street_address',
        'usertype',
        'status',
        'date_of_birth',
        'joining_year',
        'profile_image', 'nida',
    ];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Get all of the comments for the Transport
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'transport_id');
    }
}
