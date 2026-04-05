<?php
// app/Events/PaymentUpdated.php

namespace App\Events;

use App\Models\school_fees_payment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentUpdated
{
    use Dispatchable, SerializesModels;

    public $payment;

    public function __construct(school_fees_payment $payment)
    {
        $this->payment = $payment;
    }
}
