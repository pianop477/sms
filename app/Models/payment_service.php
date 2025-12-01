<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class payment_service extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_name', 'amount', 'payment_mode', 'expiry_duration', 'collection_account', 'status', 'cancel_reason'
    ];

    protected $guarded = ['id'];
}
