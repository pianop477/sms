<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeClearanceToken extends Model
{
    // Tumia values zilizopo kwenye ENUM
    const STATUS_ACTIVE = 'active';
    const STATUS_EXPIRED = 'expired';
    // const STATUS_INACTIVE = 'inactive'; // ← HII HAIPO, ISITUMIE

    protected $fillable = [
        'student_id',
        'academic_year',
        'total_amount',
        'amount_paid',
        'fee_structure_id',
        'installment_id',
        'token',
        'generated_at',
        'expires_at',
        'status',
        'cleared_at'
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'expires_at' => 'datetime',
        'cleared_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2'
    ];

    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isExpired()
    {
        return $this->status === self::STATUS_EXPIRED;
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class);
    }

    public function installment()
    {
        return $this->belongsTo(FeeInstallment::class);
    }
}