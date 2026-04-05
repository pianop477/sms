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

    /**
     * Get current academic year
     */
    private function getCurrentAcademicYear()
    {
        return date('Y');
    }

    /**
     * ✅ IMPROVED: Get student's fee assignment for a specific year
     */
    private function getStudentFeeAssignment(Student $student, int $academicYear)
    {
        return StudentFeeAssignment::where('student_id', $student->id)
            ->where('academic_year', $academicYear)
            ->where('is_active', true)
            ->first();
    }

    private function getTotalPaidForAcademicYear(Student $student, $academicYear)
    {
        // ✅ Use cache to avoid repeated queries
        $cacheKey = "total_paid_{$student->id}_{$academicYear}";

        return cache()->remember($cacheKey, 60, function () use ($student, $academicYear) {
            return school_fees_payment::whereHas('schoolFee', function ($q) use ($student, $academicYear) {
                $q->where('student_id', $student->id)
                    ->where('academic_year', $academicYear);
            })->sum('amount');
        });
    }

    /**
     * ✅ IMPROVED: Get the target installment based on total paid and academic year
     */
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

        // Find the highest installment the student has qualified for
        $targetInstallment = null;
        foreach ($installments as $installment) {
            if ($totalPaid >= $installment->cumulative_required) {
                $targetInstallment = $installment;
            } else {
                break;
            }
        }

        // If no installment reached, take the first one
        if (!$targetInstallment) {
            $targetInstallment = $installments->first();
        }

        return $targetInstallment;
    }

    /**
     * ✅ IMPROVED: Evaluate if student qualifies for token (with academic_year)
     */
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

        // Check if student has reached cumulative required for this installment
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
     * ✅ IMPROVED: Process token with academic_year awareness
     */
    public function process(Student $student, ?int $academicYear = null)
    {
        $academicYear = $academicYear ?? $this->getCurrentAcademicYear();
        $evaluation = $this->evaluate($student, $academicYear);

        if (!$evaluation['eligible']) {
            // Log::info("Student {$student->id} not eligible for year {$academicYear}: {$evaluation['reason']}");
            return null;
        }

        $targetInstallment = $evaluation['installment'];

        // Check if student already has an active token for this academic year
        $existingToken = FeeClearanceToken::where('student_id', $student->id)
            ->where('academic_year', $academicYear)
            ->where('status', 'active')
            ->first();

        if ($existingToken) {
            // Check if the existing token is for a different installment
            if ($existingToken->installment_id != $targetInstallment->id) {
                // Update the expiry date to the new installment's end date
                $newExpiry = $targetInstallment->end_date;

                $existingToken->update([
                    'fee_structure_id' => $targetInstallment->fee_structure_id,
                    'installment_id' => $targetInstallment->id,
                    'expires_at' => $newExpiry,
                    'updated_at' => now()
                ]);

                // Log::info("Token updated for student {$student->id} for year {$academicYear}");
                return $existingToken;
            } else {
                // Token already exists for the correct installment
                return $existingToken;
            }
        }

        // No active token exists - create new one
        return DB::transaction(function () use ($student, $targetInstallment, $academicYear) {
            // Double check no token was created in between
            $existingToken = FeeClearanceToken::where('student_id', $student->id)
                ->where('academic_year', $academicYear)
                ->where('status', 'active')
                ->first();

            if ($existingToken) {
                return $existingToken;
            }

            $token = $this->tokenGenerator->generateUniqueToken();

            $newToken = FeeClearanceToken::create([
                'student_id' => $student->id,
                'academic_year' => $academicYear,
                'fee_structure_id' => $targetInstallment->fee_structure_id,
                'installment_id' => $targetInstallment->id,
                'token' => $token,
                'expires_at' => $targetInstallment->end_date,
                'status' => 'active',
            ]);

            // Log::info("New token created for student {$student->id} for year {$academicYear}");
            return $newToken;
        });
    }

    /**
     * ✅ IMPROVED: Verify token with academic_year
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

    /**
     * ✅ IMPROVED: Get eligibility details with academic_year
     */
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

    /**
     * ✅ IMPROVED: Sync token after payment correction
     */
    public function syncTokenAfterPaymentCorrection(Student $student, ?int $academicYear = null)
    {
        $academicYear = $academicYear ?? $this->getCurrentAcademicYear();
        $evaluation = $this->evaluate($student, $academicYear);

        $activeToken = FeeClearanceToken::where('student_id', $student->id)
            ->where('academic_year', $academicYear)
            ->where('status', 'active')
            ->first();

        if (!$evaluation['eligible']) {
            // Student no longer qualifies - expire token
            if ($activeToken) {
                $activeToken->update(['status' => 'expired']);
                Log::warning('Token expired after payment correction', [
                    'student_id' => $student->id,
                    'academic_year' => $academicYear,
                    'token' => $activeToken->token,
                    'reason' => 'payment_correction'
                ]);
            }
            return null;
        }

        $targetInstallment = $evaluation['installment'];

        if ($activeToken) {
            // Update existing token if needed
            if ($activeToken->installment_id != $targetInstallment->id) {
                $activeToken->update([
                    'installment_id' => $targetInstallment->id,
                    'fee_structure_id' => $targetInstallment->fee_structure_id,
                    'expires_at' => $targetInstallment->end_date
                ]);
                // Log::info('Token updated after payment correction', [
                //     'student_id' => $student->id,
                //     'academic_year' => $academicYear,
                //     'token' => $activeToken->token,
                //     'new_installment' => $targetInstallment->name
                // ]);
            }
            return $activeToken;
        } else {
            // Create new token
            return $this->process($student, $academicYear);
        }
    }

    /**
     * ✅ NEW: Process tokens for all students in a given year
     */
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

    public function isNewlyCreatedToken($token): bool
    {
        return $token->wasRecentlyCreated ?? false;
    }

    public function clearCache($studentId, $academicYear)
    {
        $cacheKey = "total_paid_{$studentId}_{$academicYear}";
        cache()->forget($cacheKey);

        // Log::info('Cleared payment cache', [
        //     'student_id' => $studentId,
        //     'academic_year' => $academicYear,
        //     'cache_key' => $cacheKey
        // ]);
    }
}
