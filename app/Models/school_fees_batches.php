<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class school_fees_batches extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_name', 'batch_number', 'created_by', 'year'
    ];

    protected $guarded = ['id'];

}
