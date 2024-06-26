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
        'status'
    ];

    protected $guarded = ['id'];


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
