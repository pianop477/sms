<?php

namespace App\Services;

use App\Models\Student;
use App\Models\FeeInstallment;
use App\Models\FeeClearanceToken;
use App\Models\school_fees;
use App\Models\school_fees_payment;
use App\Models\StudentFeeAssignment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class FeeClearanceService
{
    protected $tokenGenerator;

    public function __construct()
    {
        $this->tokenGenerator = new TokenGeneratorService();
    }

    private function getCurrentAcademicYear()
    {
        return date('Y');
    }

    private function getStudentFeeAssignment(Student $student, int $academicYear)
    {
        return StudentFeeAssignment::where('student_id', $student->id)
            ->where('academic_year', $academicYear)
            ->where('is_active', true)
            ->first();
    }

    private function getTotalPaidForAcademicYear(Student $student, $academicYear)
    {
        $cacheKey = "total_paid_{$student->id}_{$academicYear}";
        return cache()->remember($cacheKey, 60, function () use ($student, $academicYear) {
            return school_fees_payment::whereHas('bill', function ($q) use ($student, $academicYear) {
                $q->where('student_id', $student->id)
                    ->where('academic_year', $academicYear);
            })->sum('amount');
        });
    }

    private function getTargetInstallment(Student $student, $feeStructureId, $academicYear)
    {
        $totalPaid = $this->getTotalPaidForAcademicYear($student, $academicYear);
        $installments = FeeInstallment::where('fee_structure_id', $feeStructureId)
            ->where('academic_year', $academicYear)
            ->orderBy('order')
            ->get();

        if ($installments->isEmpty()) {
            return null;
        }

        $targetInstallment = null;
        foreach ($installments as $installment) {
            if ($totalPaid >= $installment->cumulative_required) {
                $targetInstallment = $installment;
            } else {
                break;
            }
        }

        if (!$targetInstallment) {
            $targetInstallment = $installments->first();
        }

        return $targetInstallment;
    }

    public function evaluate(Student $student, ?int $academicYear = null)
    {
        $academicYear = $academicYear ?? $this->getCurrentAcademicYear();
        $totalPaid = $this->getTotalPaidForAcademicYear($student, $academicYear);
        $assignment = $this->getStudentFeeAssignment($student, $academicYear);

        if (!$assignment) {
            return [
                'eligible' => false,
                'reason' => 'no_fee_structure',
                'installment' => null,
                'academic_year' => $academicYear
            ];
        }

        $feeStructureId = $assignment->fee_structure_id;
        $targetInstallment = $this->getTargetInstallment($student, $feeStructureId, $academicYear);

        if (!$targetInstallment) {
            return [
                'eligible' => false,
                'reason' => 'no_installment_found',
                'installment' => null,
                'academic_year' => $academicYear
            ];
        }

        if ($totalPaid < $targetInstallment->cumulative_required) {
            return [
                'eligible' => false,
                'reason' => 'insufficient_payment',
                'installment' => $targetInstallment,
                'total_paid' => $totalPaid,
                'required' => $targetInstallment->cumulative_required,
                'academic_year' => $academicYear
            ];
        }

        return [
            'eligible' => true,
            'reason' => 'eligible',
            'installment' => $targetInstallment,
            'total_paid' => $totalPaid,
            'required' => $targetInstallment->cumulative_required,
            'academic_year' => $academicYear
        ];
    }

    /**
     * Generate or update token only if student qualifies for the current installment period.
     * Resets notification_sent flag whenever token is created or updated.
     */
    public function process(Student $student, ?int $academicYear = null)
    {
        $academicYear = $academicYear ?? $this->getCurrentAcademicYear();
        $evaluation = $this->evaluate($student, $academicYear);

        if (!$evaluation['eligible']) {
            return null;
        }

        $targetInstallment = $evaluation['installment'];

        // Check if the reached installment is the CURRENT installment (by date)
        $today = Carbon::today();
        $isCurrent = $today->between(
            Carbon::parse($targetInstallment->start_date),
            Carbon::parse($targetInstallment->end_date)
        );

        if (!$isCurrent) {
            // Student may have paid ahead or behind, but no token for non‑current period
            return null;
        }

        // Find existing token (unique per student/year thanks to DB constraint)
        $existingToken = FeeClearanceToken::where('student_id', $student->id)
            ->where('academic_year', $academicYear)
            ->first();

        if ($existingToken) {
            // Check which fields need update
            $changed = false;

            if ($existingToken->installment_id != $targetInstallment->id) {
                $existingToken->installment_id = $targetInstallment->id;
                $changed = true;
            }
            if ($existingToken->fee_structure_id != $targetInstallment->fee_structure_id) {
                $existingToken->fee_structure_id = $targetInstallment->fee_structure_id;
                $changed = true;
            }
            if ($existingToken->expires_at != $targetInstallment->end_date) {
                $existingToken->expires_at = $targetInstallment->end_date;
                $changed = true;
            }
            if ($existingToken->status != 'active') {
                $existingToken->status = 'active';
                $changed = true;
            }

            if ($changed) {
                // Reset notification flag so SMS will be sent again
                $existingToken->notification_sent = false;
                $existingToken->save();
            }

            return $existingToken;
        }

        // Create new token (with notification_sent = false)
        return DB::transaction(function () use ($student, $targetInstallment, $academicYear) {
            // Double‑check to avoid race condition
            $checkAgain = FeeClearanceToken::where('student_id', $student->id)
                ->where('academic_year', $academicYear)
                ->first();
            if ($checkAgain) {
                return $checkAgain;
            }

            $token = $this->tokenGenerator->generateUniqueToken();
            return FeeClearanceToken::create([
                'student_id' => $student->id,
                'academic_year' => $academicYear,
                'fee_structure_id' => $targetInstallment->fee_structure_id,
                'installment_id' => $targetInstallment->id,
                'token' => $token,
                'expires_at' => $targetInstallment->end_date,
                'status' => 'active',
                'notification_sent' => false,
            ]);
        });
    }

    /**
     * Sync token after payment correction – resets notification_sent if token is updated.
     */
    public function syncTokenAfterPaymentCorrection(Student $student, ?int $academicYear = null)
    {
        $academicYear = $academicYear ?? $this->getCurrentAcademicYear();
        $evaluation = $this->evaluate($student, $academicYear);

        if (!$evaluation['eligible']) {
            // Student no longer qualifies: expire any active token
            $activeToken = FeeClearanceToken::where('student_id', $student->id)
                ->where('academic_year', $academicYear)
                ->where('status', 'active')
                ->first();

            if ($activeToken) {
                $activeToken->update(['status' => 'expired']);
                Log::warning('Token expired after payment correction', [
                    'student_id' => $student->id,
                    'academic_year' => $academicYear,
                    'token' => $activeToken->token,
                ]);
            }
            return null;
        }

        $targetInstallment = $evaluation['installment'];

        // Look for any token (any status) for this student/year
        $existingToken = FeeClearanceToken::where('student_id', $student->id)
            ->where('academic_year', $academicYear)
            ->first();

        if ($existingToken) {
            // Update existing token and reset notification flag
            $existingToken->update([
                'installment_id' => $targetInstallment->id,
                'fee_structure_id' => $targetInstallment->fee_structure_id,
                'expires_at' => $targetInstallment->end_date,
                'status' => 'active',
                'notification_sent' => false,
                'updated_at' => now(),
            ]);
            return $existingToken;
        } else {
            // Create new token (will also set notification_sent = false)
            return $this->process($student, $academicYear);
        }
    }

    /**
     * Mark token as notified (SMS sent).
     */
    public function markNotificationSent($token)
    {
        if ($token && !$token->notification_sent) {
            $token->update(['notification_sent' => true]);
        }
    }

    /**
     * Check if token needs notification (i.e., exists and not yet sent).
     */
    public function needsNotification($token)
    {
        return $token && !$token->notification_sent;
    }

    /**
     * Verify token – fixed regex to keep alphanumeric.
     */
    public function verifyToken($inputToken, ?int $academicYear = null)
    {
        try {
            $cleanToken = strtoupper(preg_replace('/[^A-Z0-9]/', '', $inputToken));
            $academicYear = $academicYear ?? $this->getCurrentAcademicYear();

            $token = FeeClearanceToken::with(['student', 'installment'])
                ->where('token', $cleanToken)
                ->where('academic_year', $academicYear)
                ->where('status', 'active')
                ->where('expires_at', '>', now())
                ->first();

            if (!$token) {
                return [
                    'valid' => false,
                    'reason' => 'invalid_or_expired',
                    'token' => null
                ];
            }

            return [
                'valid' => true,
                'reason' => 'valid',
                'token' => $token,
                'student' => $token->student,
                'installment' => $token->installment,
                'expires_at' => $token->expires_at,
                'academic_year' => $token->academic_year
            ];
        } catch (\Exception $e) {
            Log::error('Token verification error', [
                'input' => $inputToken,
                'error' => $e->getMessage()
            ]);

            return [
                'valid' => false,
                'reason' => 'error',
                'token' => null
            ];
        }
    }

    public function getEligibilityDetails(Student $student, ?int $academicYear = null)
    {
        $academicYear = $academicYear ?? $this->getCurrentAcademicYear();
        $totalPaid = $this->getTotalPaidForAcademicYear($student, $academicYear);
        $assignment = $this->getStudentFeeAssignment($student, $academicYear);

        if (!$assignment) {
            return [
                'eligible' => false,
                'reason' => 'No fee structure assigned for this year',
                'academic_year' => $academicYear,
                'total_paid' => $totalPaid,
                'installments' => []
            ];
        }

        $installments = FeeInstallment::where('fee_structure_id', $assignment->fee_structure_id)
            ->where('academic_year', $academicYear)
            ->orderBy('order')
            ->get();

        $currentActiveToken = FeeClearanceToken::where('student_id', $student->id)
            ->where('academic_year', $academicYear)
            ->where('status', 'active')
            ->first();

        $details = [];
        foreach ($installments as $inst) {
            $isReached = ($totalPaid >= $inst->cumulative_required);

            $details[] = [
                'name' => $inst->name,
                'order' => $inst->order,
                'cumulative_required' => $inst->cumulative_required,
                'start_date' => Carbon::parse($inst->start_date)->format('d/m/Y'),
                'end_date' => Carbon::parse($inst->end_date)->format('d/m/Y'),
                'is_reached' => $isReached
            ];
        }

        return [
            'student_id' => $student->id,
            'student_name' => $student->first_name . ' ' . $student->last_name,
            'academic_year' => $academicYear,
            'total_paid' => $totalPaid,
            'fee_structure' => $assignment->feeStructure->name ?? 'N/A',
            'current_active_token' => $currentActiveToken ? [
                'token' => $currentActiveToken->token,
                'expires_at' => $currentActiveToken->expires_at->format('d/m/Y'),
                'installment_id' => $currentActiveToken->installment_id
            ] : null,
            'installments' => $details
        ];
    }

    public function processTokensForYear(int $academicYear, ?int $schoolId = null): array
    {
        $query = Student::query();
        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }
        $students = $query->get();
        $results = [
            'total' => $students->count(),
            'processed' => 0,
            'failed' => 0,
            'errors' => []
        ];

        foreach ($students as $student) {
            try {
                $token = $this->process($student, $academicYear);
                if ($token) {
                    $results['processed']++;
                }
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Student {$student->admission_number}: " . $e->getMessage();
            }
        }
        return $results;
    }

    /**
     * Helper to know if token was just created (useful for controllers).
     */
    public function isNewlyCreatedToken($token): bool
    {
        return $token->wasRecentlyCreated ?? false;
    }

    public function clearCache($studentId, $academicYear)
    {
        $cacheKey = "total_paid_{$studentId}_{$academicYear}";
        cache()->forget($cacheKey);
    }
}
