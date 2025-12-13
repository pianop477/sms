<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'school_id',
        'dob',
        'qualification',
        'address',
        'role_id',
        'member_id',
        'status', 'nida', 'form_four_index_number', 'form_four_completion_year',
    ];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Get the user that owns the Teacher
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
