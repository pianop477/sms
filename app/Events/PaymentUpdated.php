<?php

namespace App\Events;

use App\Models\school_fees_payment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentUpdated
{
    use Dispatchable, SerializesModels;

    public $payment;
    public $old_amount;

    public function __construct(school_fees_payment $payment, $old_amount)
    {
        $this->payment = $payment;
        $this->old_amount = $old_amount;
    }
}
