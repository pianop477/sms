<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EPermit extends Model
{
    protected $table = 'e_permits';

    protected $fillable = [
        'permit_number',
        'student_id',
        'parent_id',
        'guardian_name',
        'guardian_phone',
        'guardian_type',
        'relationship',
        'reason',
        'other_reason',
        'departure_date',
        'departure_time',
        'expected_return_date',
        'status',
        'rejection_reason',
        'class_teacher_id',
        'class_teacher_approved_at',
        'class_teacher_action',
        'class_teacher_comment',
        'duty_teacher_id',
        'duty_teacher_approved_at',
        'duty_teacher_action',
        'duty_teacher_comment',
        'academic_teacher_id',
        'academic_teacher_approved_at',
        'academic_teacher_action',
        'academic_teacher_comment',
        'head_teacher_id',
        'head_teacher_approved_at',
        'head_teacher_action',
        'head_teacher_comment',
        'actual_return_date',
        'is_late_return',
        'late_return_reason',
        'returned_alone',
        'return_accompanied_by',
        'return_guardian_type',
        'return_relationship',
        'verified_by',
        'verified_at',
        'pdf_path',
        'qr_code_path',
        'duty_teacher_skipped',
        'duty_teacher_skipped_reason',
        'duty_teacher_skipped_by',
        'duty_teacher_skipped_at',
    ];

    protected $casts = [
        'departure_time' => 'datetime',
        'class_teacher_approved_at' => 'datetime',
        'duty_teacher_approved_at' => 'datetime',
        'academic_teacher_approved_at' => 'datetime',
        'head_teacher_approved_at' => 'datetime',
        'verified_at' => 'datetime',
        'actual_return_date' => 'datetime',
        'departure_date' => 'date',
        'expected_return_date' => 'date',
        'is_late_return' => 'boolean',
        'returned_alone' => 'boolean',
        'duty_teacher_skipped' => 'boolean',
        'duty_teacher_skipped_at' => 'datetime',
    ];

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Parents::class, 'parent_id');
    }

    public function classTeacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'class_teacher_id');
    }

    public function dutyTeacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'duty_teacher_id');
    }

    public function academicTeacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'academic_teacher_id');
    }

    public function headTeacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'head_teacher_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'verified_by');
    }

    public function trackingLogs(): HasMany
    {
        return $this->hasMany(EPermitTrackingLog::class, 'e_permit_id');
    }

    // Helper Methods
    public function getCurrentStageAttribute(): string
    {
        $stages = [
            'pending_class_teacher' => 'Class Teacher Review',
            'pending_duty_teacher' => 'Duty Teacher Review',
            'pending_academic' => 'Academic Teacher Review',
            'pending_head' => 'Head Teacher Review',
            'approved' => 'Approved - Ready for Collection',
            'rejected' => 'Rejected',
            'completed' => 'Completed - Student Returned',
        ];

        return $stages[$this->status] ?? 'Unknown';
    }

    public function getStageNumberAttribute(): int
    {
        $stages = [
            'pending_class_teacher' => 1,
            'pending_duty_teacher' => 2,
            'pending_academic' => 3,
            'pending_head' => 4,
            'approved' => 5,
        ];

        return $stages[$this->status] ?? 0;
    }

    public function getTotalStagesAttribute(): int
    {
        return 5; // Total approval stages
    }

    public function getProgressPercentageAttribute(): float
    {
        if ($this->status === 'approved' || $this->status === 'completed') {
            return 100;
        }

        if ($this->status === 'rejected') {
            return 0;
        }

        $stageNumber = $this->stage_number;
        return ($stageNumber / $this->total_stages) * 100;
    }

    public function isActive(): bool
    {
        return in_array($this->status, [
            'pending_class_teacher',
            'pending_duty_teacher',
            'pending_academic',
            'pending_head',
            'approved'
        ]);
    }

    public function canBeApprovedBy(Teacher $teacher): bool
    {
        switch ($this->status) {
            case 'pending_class_teacher':
                return $teacher->id === $this->class_teacher_id;
            case 'pending_duty_teacher':
                return $teacher->id === $this->duty_teacher_id;
            case 'pending_academic':
                return $teacher->role_id === 3; // Academic teacher role
            case 'pending_head':
                return $teacher->role_id === 2; // Head teacher role
            default:
                return false;
        }
    }
}
