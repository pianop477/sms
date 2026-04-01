<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'dob',
        'gender',
        'parent_id',
        'class_id',
        'group',
        'transport_id',
        'school_id',
        'admission_number',
        'status',
        'graduated',
        'graduated_at'
    ];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Get the user that owns the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function class()
    {
        return $this->belongsTo(Grade::class);
    }

    /**
     * Get the user that owns the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parents()
    {
        return $this->belongsTo(Parents::class);
    }

    /**
     * Get the user that owns the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function schools()
    {
        return $this->belongsTo(school::class);
    }

    /**
     * Get the user that owns the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transport()
    {
        return $this->belongsTo(Transport::class);
    }

    public function feeAssignment()
    {
        return $this->hasOne(StudentFeeAssignment::class, 'student_id')->where('is_active', true);
    }
    public function payments()
    {
        return $this->hasMany(school_fees_payment::class, 'student_id');
    }

    public function tokens()
    {
        return $this->hasMany(FeeClearanceToken::class);
    }
}
