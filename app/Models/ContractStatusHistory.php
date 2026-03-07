<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractStatusHistory extends Model
{
    protected $fillable = [
        'contract_id',
        'previous_status',
        'new_status',
        'changed_by',
        'reason',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    public function contract()
    {
        return $this->belongsTo(school_constracts::class);
    }
}
