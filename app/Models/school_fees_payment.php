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
    protected static function boot()
    {
        parent::boot();

        // Dispatch event when payment is updated
        static::updating(function ($payment) {
            $oldAmount = $payment->getOriginal('amount');
            if ($oldAmount != $payment->amount) {
                event(new PaymentUpdated($payment, $oldAmount));
            }
        });

        // Dispatch event when payment is deleted
        static::deleted(function ($payment) {
            event(new PaymentDeleted($payment));
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
