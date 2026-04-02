<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeInstallment extends Model
{
    protected $fillable = [
        'fee_structure_id',
        'name',
        'amount',
        'cumulative_required',
        'start_date',
        'end_date',
        'academic_year',
        'order'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'amount' => 'decimal:2',
        'cumulative_required' => 'decimal:2',
    ];

    /**
     * Get the fee structure that owns this installment
     */
    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class, 'fee_structure_id');
    }

    /**
     * Get the tokens for this installment
     */
    public function tokens()
    {
        return $this->hasMany(FeeClearanceToken::class, 'installment_id');
    }
}
