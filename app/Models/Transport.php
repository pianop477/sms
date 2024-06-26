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
        'school_id'
    ];

    protected $guarded = ['id'];

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
