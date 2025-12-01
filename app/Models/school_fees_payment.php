<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class school_fees_payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id', 'student_id', 'student_fee_id', 'amount',
        'payment_mode', 'installment', 'approved_by', 'approved_at'
    ];

    protected $guarded = ['id'];
}
