<?php

namespace App\Services;

use App\Models\Student;
use App\Models\FeeInstallment;
use App\Models\FeeClearanceToken;
use App\Models\school_fees;
use App\Models\school_fees_payment;
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
     * Get total paid for specific academic year
     */
    private function getTotalPaidForAcademicYear(Student $student, $academicYear)
    {
        $bills = school_fees::where('student_id', $student->id)
            ->where('academic_year', $academicYear)
            ->pluck('id');

        if ($bills->isEmpty()) {
            return 0;
        }

        return school_fees_payment::whereIn('student_fee_id', $bills)->sum('amount');
    }

    /**
     * Get the target installment based on total paid
     * Returns the highest installment that student has qualified for
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
     * Evaluate if student qualifies for token
     * Returns target installment (not necessarily current active)
     */
    public function evaluate(Student $student)
    {
        $currentAcademicYear = $this->getCurrentAcademicYear();
        $totalPaid = $this->getTotalPaidForAcademicYear($student, $currentAcademicYear);

        $assignment = $student->feeAssignment;
        if (!$assignment) {
            return [
                'eligible' => false,
                'reason' => 'no_fee_structure',
                'installment' => null
            ];
        }

        $feeStructureId = $assignment->fee_structure_id;
        $targetInstallment = $this->getTargetInstallment($student, $feeStructureId, $currentAcademicYear);

        if (!$targetInstallment) {
            return [
                'eligible' => false,
                'reason' => 'no_installment_found',
                'installment' => null
            ];
        }

        // Check if student has reached cumulative required for this installment
        if ($totalPaid < $targetInstallment->cumulative_required) {
            return [
                'eligible' => false,
                'reason' => 'insufficient_payment',
                'installment' => $targetInstallment,
                'total_paid' => $totalPaid,
                'required' => $targetInstallment->cumulative_required
            ];
        }

        return [
            'eligible' => true,
            'reason' => 'eligible',
            'installment' => $targetInstallment,
            'total_paid' => $totalPaid,
            'required' => $targetInstallment->cumulative_required
        ];
    }

    /**
     * Update existing token expiry date or create new token if needed
     * 🔥 IMPORTANT: Only send SMS when token is FIRST created, not on updates
     */
    public function process(Student $student)
    {
        $evaluation = $this->evaluate($student);

        if (!$evaluation['eligible']) {
            // Log::info("Student {$student->id} not eligible: {$evaluation['reason']}");
            return null;
        }

        $targetInstallment = $evaluation['installment'];

        // Check if student already has an active token
        $existingToken = FeeClearanceToken::where('student_id', $student->id)
            ->where('status', 'active')
            ->first();

        if ($existingToken) {
            // Check if the existing token is for a different installment
            if ($existingToken->installment_id != $targetInstallment->id) {
                // 🔥 UPDATE: Extend the expiry date to the new installment's end date
                $oldExpiry = $existingToken->expires_at;
                $newExpiry = $targetInstallment->end_date;

                $existingToken->update([
                    'fee_structure_id' => $targetInstallment->fee_structure_id,
                    'installment_id' => $targetInstallment->id,
                    'expires_at' => $newExpiry
                ]);

                // 🔥 NO SMS SENT - Just return the updated token
                return $existingToken;
            } else {
                // Token already exists for the correct installment
                // Log::info("Student {$student->id} already has valid token for {$targetInstallment->name}");
                return $existingToken;
            }
        }

        // No active token exists - create new one (SMS will be sent)
        $isNewToken = true;

        return DB::transaction(function () use ($student, $targetInstallment, $isNewToken) {

            // Double check no token was created in between
            $existingToken = FeeClearanceToken::where('student_id', $student->id)
                ->where('status', 'active')
                ->first();

            if ($existingToken) {
                return $existingToken;
            }

            $token = $this->tokenGenerator->generateUniqueToken();

            $newToken = FeeClearanceToken::create([
                'student_id' => $student->id,
                'fee_structure_id' => $targetInstallment->fee_structure_id,
                'installment_id' => $targetInstallment->id,
                'token' => $token,
                'expires_at' => $targetInstallment->end_date,
                'status' => 'active',
            ]);

            return $newToken;
        });
    }

    /**
     * Check if this is a newly created token (for SMS sending)
     */
    public function isNewlyCreatedToken($token)
    {
        return $token->wasRecentlyCreated ?? false;
    }

    /**
     * Generate token (for backward compatibility)
     */
    private function generateToken(Student $student, FeeInstallment $installment)
    {
        return DB::transaction(function () use ($student, $installment) {

            $existingToken = FeeClearanceToken::where([
                'student_id' => $student->id,
                'installment_id' => $installment->id,
            ])->first();

            if ($existingToken) {
                return $existingToken;
            }

            $token = $this->tokenGenerator->generateUniqueToken();

            return FeeClearanceToken::create([
                'student_id' => $student->id,
                'fee_structure_id' => $installment->fee_structure_id,
                'installment_id' => $installment->id,
                'token' => $token,
                'expires_at' => $installment->end_date,
                'status' => 'active',
            ]);
        });
    }

    /**
     * Check token validity
     */
    public function verifyToken($inputToken)
    {
        try {
            $cleanToken = strtoupper(preg_replace('/[^A-Z0-9]/', '', $inputToken));

            $token = FeeClearanceToken::with(['student', 'installment'])
                ->where('token', $cleanToken)
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
                'expires_at' => $token->expires_at
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
     * Get eligibility details for debugging
     */
    public function getEligibilityDetails(Student $student)
    {
        $currentAcademicYear = $this->getCurrentAcademicYear();
        $totalPaid = $this->getTotalPaidForAcademicYear($student, $currentAcademicYear);
        $assignment = $student->feeAssignment;

        if (!$assignment) {
            return [
                'eligible' => false,
                'reason' => 'No fee structure assigned',
                'academic_year' => $currentAcademicYear,
                'total_paid' => $totalPaid,
                'installments' => []
            ];
        }

        $installments = FeeInstallment::where('fee_structure_id', $assignment->fee_structure_id)
            ->where('academic_year', $currentAcademicYear)
            ->orderBy('order')
            ->get();

        $currentActiveToken = FeeClearanceToken::where('student_id', $student->id)
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
            'academic_year' => $currentAcademicYear,
            'total_paid' => $totalPaid,
            'fee_structure' => $assignment->feeStructure->name ?? 'N/A',
            'current_active_token' => $currentActiveToken ? [
                'token' => $currentActiveToken->token,
                'expires_at' => $currentActiveToken->expires_at,
                'installment_id' => $currentActiveToken->installment_id
            ] : null,
            'installments' => $details
        ];
    }

    /**
     * Sync token after payment correction
     * This method recalculates and updates token based on current payments
     */
    public function syncTokenAfterPaymentCorrection(Student $student)
    {
        $evaluation = $this->evaluate($student);
        $activeToken = FeeClearanceToken::where('student_id', $student->id)
            ->where('status', 'active')
            ->first();

        if (!$evaluation['eligible']) {
            // Student no longer qualifies - expire token
            if ($activeToken) {
                $activeToken->update(['status' => 'expired']);
                Log::warning('Token expired after payment correction', [
                    'student_id' => $student->id,
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
                //     'token' => $activeToken->token,
                //     'new_installment' => $targetInstallment->name
                // ]);
            }
            return $activeToken;
        } else {
            // Create new token
            return $this->process($student);
        }
    }
}
