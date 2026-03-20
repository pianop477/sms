<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'notification_type',
        'pending_count',
        'active_count',
        'sent_at',
        'notification_date',
        'unique_key'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'notification_date' => 'date'
    ];
}
