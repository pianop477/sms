<?php

namespace App\Models;

use App\Events\PaymentDeleted;
use App\Events\PaymentUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class school_fees_payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'student_id',
        'student_fee_id',
        'amount',
        'payment_mode',
        'installment',
        'approved_by',
        'approved_at'
    ];

    protected $guarded = ['id'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

     /**
     * Boot the model
     */
    protected static function booted()
    {
        // static::created(function ($payment) {
        //     event(new PaymentCreated($payment, null));
        // });

        static::updated(function ($payment) {
            event(new PaymentUpdated($payment, $payment->getOriginal()));
        });

        static::deleted(function ($payment) {
            event(new PaymentDeleted($payment, null));
        });
    }

    /**
     * Get the bill that owns the payment
     */
    public function bill()
    {
        return $this->belongsTo(school_fees::class, 'student_fee_id');
    }
}
