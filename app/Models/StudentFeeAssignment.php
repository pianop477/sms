<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentFeeAssignment extends Model
{
    protected $fillable = [
        'student_id',
        'fee_structure_id',
        'assigned_class_id',
        'assignment_reason',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'assigned_at' => 'datetime',
        'effective_from' => 'datetime',
        'effective_to' => 'datetime'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Get the fee structure for this assignment
     */
    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class, 'fee_structure_id');
    }

    /**
     * Get the class that was assigned (snapshot)
     */
    public function assignedClass()
    {
        return $this->belongsTo(Grade::class, 'assigned_class_id');
    }
}
