<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class school_fees extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id', 'student_id', 'academic_year', 'service_id',  'cancelled_at', 'created_at', 'created_by',
        'amount', 'due_date', 'status', 'is_cancelled', 'cancel_reason', 'control_number', 'class_id', 'batch_id'
    ];

    public function payments()
    {
        return $this->hasMany(school_fees_payment::class, 'student_fee_id', 'id');
    }
}
