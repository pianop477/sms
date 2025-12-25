<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class otps extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'otp', 'expires_at', 'used', 'locked_until', 'attempts', 'ip_address', 'user_agent'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
