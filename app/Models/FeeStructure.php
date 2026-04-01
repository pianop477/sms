<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    protected $fillable = ['school_id', 'class_id', 'name', 'total_amount', 'transport_applies', 'is_hostel_class'];

    protected $casts = [
        'total_amount' => 'decimal:2'
    ];

    public function school()
    {
        return $this->belongsTo(school::class);
    }

    public function class()
    {
        return $this->belongsTo(Grade::class, 'class_id');
    }

    public function installments()
    {
        return $this->hasMany(FeeInstallment::class);
    }

    public function studentAssignments()
    {
        return $this->hasMany(StudentFeeAssignment::class);
    }

    // Helper method to get appropriate fee structure for a student
    public static function getForStudent($student)
    {
        $hasTransport = !is_null($student->transport_id);
        $type = $hasTransport ? 'Transport' : 'Non-Transport';

        // First try to get class-specific structure
        $structure = self::where('school_id', $student->school_id)
            ->where('class_id', $student->class_id)
            ->where('name', 'like', "%{$type}%")
            ->first();

        // If not found, get general structure
        if (!$structure) {
            $structure = self::where('school_id', $student->school_id)
                ->whereNull('class_id')
                ->where('name', 'like', "%{$type}%")
                ->first();
        }

        return $structure;
    }
}
