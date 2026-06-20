<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Class_teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id', 'class_id', 'group', 'school_id'
    ];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    // Kwa sababu class_teachers table ina class_id
    // Inarejelea grades table, hivyo ni BelongsTo
    public function class(): BelongsTo
    {
        return $this->belongsTo(Grade::class, 'class_id');
    }

    // Pia ongeza relationship kwa teacher
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    // Relationship kwa grade (alternative)
    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class, 'class_id');
    }
}
