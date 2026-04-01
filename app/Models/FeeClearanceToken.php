<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeClearanceToken extends Model
{
    protected $fillable = [
        'student_id',
        'fee_structure_id',
        'installment_id',
        'token',
        'expires_at',
        'status',
    ];

    protected $dates = ['expires_at'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function installment()
    {
        return $this->belongsTo(FeeInstallment::class, 'installment_id');
    }

    public function structure()
    {
        return $this->belongsTo(FeeStructure::class, 'fee_structure_id');
    }
}
