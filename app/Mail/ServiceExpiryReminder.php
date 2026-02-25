<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ServiceExpiryReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $school;
    public $admin;
    public $daysLeft;

    public function __construct($school, $admin)
    {
        $this->school = $school;
        $this->admin = $admin;
        $this->daysLeft = now()->diffInDays($school->service_end_date, false);
    }

    public function build()
    {
        return $this->subject('⚠️ Service Expiry Reminder - ' . $this->school->school_name)
                    ->view('emails.expiry_notify');
    }
}
