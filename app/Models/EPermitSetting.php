<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EPermitSetting extends Model
{
    protected $table = 'e_permit_settings';

    protected $fillable = [
        'school_id',
        'auto_approve_enabled',
        'max_requests_per_day',
        'late_return_grace_hours',
        'require_parent_photo',
        'allowed_reasons'
    ];

    protected $casts = [
        'auto_approve_enabled' => 'boolean',
        'require_parent_photo' => 'boolean',
        'allowed_reasons' => 'array'
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
