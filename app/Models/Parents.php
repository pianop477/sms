<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parents extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'school_id',
        'address',
        'status'
    ];

    protected $guarded = ['id'];

    /**
     * Get all of the students for the Parents
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'parent_id');
    }

    /**
     * Get the user associated with the Parents
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'user_id');
    }

}
