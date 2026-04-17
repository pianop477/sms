<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EPermitTrackingLog extends Model
{
    protected $table = 'e_permit_tracking_logs';

    protected $fillable = [
        'e_permit_id',
        'teacher_id',
        'action',
        'stage',
        'comment',
        'metadata',
        'ip_address'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    public function ePermit(): BelongsTo
    {
        return $this->belongsTo(EPermit::class, 'e_permit_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }
}
