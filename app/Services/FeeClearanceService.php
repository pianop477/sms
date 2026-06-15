<?php

namespace App\Services;

use App\Models\Student;
use App\Models\FeeInstallment;
use App\Models\FeeClearanceToken;
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

    private function getCurrentAcademicYear(): int
    {
        return (int) date('Y');
    }

    private function getStudentFeeAssignment(Student $student, int $academicYear)
    {
        return StudentFeeAssignment::where('student_id', $student->id)
            ->where('academic_year', $academicYear)
            ->where('is_active', true)
            ->first();
    }

    private function getTotalPaidForAcademicYear(Student $student, int $academicYear): float
    {
        $cacheKey = "total_paid_{$student->id}_{$academicYear}";
        return cache()->remember($cacheKey, 60, function () use ($student, $academicYear) {
            return (float) school_fees_payment::whereHas('bill', function ($q) use ($student, $academicYear) {
                $q->where('student_id', $student->id)
                    ->where('academic_year', $academicYear);
            })->sum('amount');
        });
    }

    private function getTargetInstallment(Student $student, int $feeStructureId, int $academicYear)
    {
        $totalPaid = $this->getTotalPaidForAcademicYear($student, $academicYear);
        $installments = FeeInstallment::where('fee_structure_id', $feeStructureId)
            ->where('academic_year', $academicYear)
            ->orderBy('order')
            ->get();

        if ($installments->isEmpty()) {
            return null;
        }

        $target = null;
        foreach ($installments as $inst) {
            if ($totalPaid >= $inst->cumulative_required) {
                $target = $inst;
            } else {
                break;
            }
        }
        return $target ?? $installments->first();
    }

    public function evaluate(Student $student, ?int $academicYear = null): array
    {
        $academicYear = $academicYear ?? $this->getCurrentAcademicYear();
        $assignment = $this->getStudentFeeAssignment($student, $academicYear);

        if (!$assignment) {
            return [
                'eligible' => false,
                'reason' => 'no_fee_structure',
                'installment' => null,
                'academic_year' => $academicYear,
            ];
        }

        $targetInstallment = $this->getTargetInstallment($student, $assignment->fee_structure_id, $academicYear);
        if (!$targetInstallment) {
            return [
                'eligible' => false,
                'reason' => 'no_installment_found',
                'installment' => null,
                'academic_year' => $academicYear,
            ];
        }

        $totalPaid = $this->getTotalPaidForAcademicYear($student, $academicYear);
        if ($totalPaid < $targetInstallment->cumulative_required) {
            return [
                'eligible' => false,
                'reason' => 'insufficient_payment',
                'installment' => $targetInstallment,
                'total_paid' => $totalPaid,
                'required' => $targetInstallment->cumulative_required,
                'academic_year' => $academicYear,
            ];
        }

        return [
            'eligible' => true,
            'reason' => 'eligible',
            'installment' => $targetInstallment,
            'total_paid' => $totalPaid,
            'required' => $targetInstallment->cumulative_required,
            'academic_year' => $academicYear,
        ];
    }

    /**
     * Main method: generate or update token for a student.
     * NO isCurrent check – token always based on the highest installment reached.
     * Returns ['token' => Token|null, 'action' => 'created'|'updated'|'none'].
     */
    public function process(Student $student, ?int $academicYear = null): array
    {
        $academicYear = $academicYear ?? $this->getCurrentAcademicYear();
        $evaluation = $this->evaluate($student, $academicYear);

        if (!$evaluation['eligible']) {
            return ['token' => null, 'action' => 'none'];
        }

        $targetInstallment = $evaluation['installment'];

        // Look for existing token (any status) for this student & year
        $existingToken = FeeClearanceToken::where('student_id', $student->id)
            ->where('academic_year', $academicYear)
            ->first();

        if ($existingToken) {
            $oldInstallmentId = $existingToken->installment_id;
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
                $existingToken->save();
                $action = ($oldInstallmentId != $targetInstallment->id) ? 'updated' : 'none';
                return ['token' => $existingToken, 'action' => $action];
            }
            return ['token' => $existingToken, 'action' => 'none'];
        }

        // Create new token
        $newToken = DB::transaction(function () use ($student, $targetInstallment, $academicYear) {
            $checkAgain = FeeClearanceToken::where('student_id', $student->id)
                ->where('academic_year', $academicYear)
                ->first();
            if ($checkAgain) {
                return $checkAgain;
            }

            $tokenCode = $this->tokenGenerator->generateUniqueToken();
            return FeeClearanceToken::create([
                'student_id'       => $student->id,
                'academic_year'    => $academicYear,
                'fee_structure_id' => $targetInstallment->fee_structure_id,
                'installment_id'   => $targetInstallment->id,
                'token'            => $tokenCode,
                'expires_at'       => $targetInstallment->end_date,
                'status'           => 'active',
            ]);
        });

        $action = ($newToken->wasRecentlyCreated ?? false) ? 'created' : 'none';
        return ['token' => $newToken, 'action' => $action];
    }

    /**
     * Verify a token (for the API endpoint).
     */
    public function verifyToken(string $inputToken, ?int $academicYear = null): array
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
                    'valid'   => false,
                    'reason'  => 'invalid_or_expired',
                    'token'   => null,
                ];
            }

            return [
                'valid'         => true,
                'reason'        => 'valid',
                'token'         => $token,
                'student'       => $token->student,
                'installment'   => $token->installment,
                'expires_at'    => $token->expires_at,
                'academic_year' => $token->academic_year,
            ];
        } catch (\Exception $e) {
            Log::error('Token verification error', [
                'input' => $inputToken,
                'error' => $e->getMessage(),
            ]);
            return [
                'valid'  => false,
                'reason' => 'error',
                'token'  => null,
            ];
        }
    }

    /**
     * Get detailed eligibility information (used for reporting).
     */
    public function getEligibilityDetails(Student $student, ?int $academicYear = null): array
    {
        $academicYear = $academicYear ?? $this->getCurrentAcademicYear();
        $totalPaid = $this->getTotalPaidForAcademicYear($student, $academicYear);
        $assignment = $this->getStudentFeeAssignment($student, $academicYear);

        if (!$assignment) {
            return [
                'eligible'      => false,
                'reason'        => 'No fee structure assigned for this year',
                'academic_year' => $academicYear,
                'total_paid'    => $totalPaid,
                'installments'  => [],
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
                'name'                => $inst->name,
                'order'               => $inst->order,
                'cumulative_required' => $inst->cumulative_required,
                'start_date'          => Carbon::parse($inst->start_date)->format('d/m/Y'),
                'end_date'            => Carbon::parse($inst->end_date)->format('d/m/Y'),
                'is_reached'          => $isReached,
            ];
        }

        return [
            'student_id'           => $student->id,
            'student_name'         => $student->first_name . ' ' . $student->last_name,
            'academic_year'        => $academicYear,
            'total_paid'           => $totalPaid,
            'fee_structure'        => $assignment->feeStructure->name ?? 'N/A',
            'current_active_token' => $currentActiveToken ? [
                'token'         => $currentActiveToken->token,
                'expires_at'    => $currentActiveToken->expires_at->format('d/m/Y'),
                'installment_id'=> $currentActiveToken->installment_id,
            ] : null,
            'installments' => $details,
        ];
    }

    /**
     * Sync a single student's token after payment correction.
     * This method is used by the SyncTokensAfterCorrection command.
     */
    public function syncTokenAfterPaymentCorrection(Student $student, ?int $academicYear = null)
    {
        // Simply delegate to process() and return the token (or null)
        $result = $this->process($student, $academicYear);
        return $result['token'];
    }

    /**
     * Process tokens for all students in a given year (bulk operation).
     */
    public function processTokensForYear(int $academicYear, ?int $schoolId = null): array
    {
        $query = Student::query();
        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }
        $students = $query->get();
        $results = [
            'total'     => $students->count(),
            'processed' => 0,
            'failed'    => 0,
            'errors'    => [],
        ];

        foreach ($students as $student) {
            try {
                $result = $this->process($student, $academicYear);
                if ($result['token']) {
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
     * Helper to clear payment cache for a student.
     */
    public function clearCache(int $studentId, int $academicYear): void
    {
        $cacheKey = "total_paid_{$studentId}_{$academicYear}";
        cache()->forget($cacheKey);
    }
}
