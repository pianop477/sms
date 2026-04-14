<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class school_fees extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'student_id',
        'academic_year',
        'service_id',
        'cancelled_at',
        'created_at',
        'created_by',
        'amount',
        'due_date',
        'status',
        'is_cancelled',
        'cancel_reason',
        'control_number',
        'class_id',
        'batch_id',
        'description'
    ];

    /**
     * Payments made against this bill
     */
    public function payments()
    {
        return $this->hasMany(school_fees_payment::class, 'student_fee_id', 'id');
    }

    /**
     * Student for this bill
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    /**
     * Service (payment type) for this bill
     */
    public function service()
    {
        return $this->belongsTo(payment_service::class, 'service_id', 'id');
    }
}
