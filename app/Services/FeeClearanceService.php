<?php

namespace App\Services;

use App\Models\Student;
use App\Models\FeeInstallment;
use App\Models\FeeClearanceToken;
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
     * Evaluate if student qualifies for current installment
     */
    public function evaluate(Student $student)
    {
        // 1. Get total paid from payments
        $totalPaid = $student->payments()->sum('amount');

        // 2. Get assigned fee structure
        $assignment = $student->feeAssignment;
        if (!$assignment) {
            // Log::info("Student {$student->id} has no fee assignment");
            // return [
            //     'eligible' => false,
            //     'reason' => 'no_fee_structure',
            //     'installment' => null
            // ];
        }

        $feeStructureId = $assignment->fee_structure_id;

        // 3. Get CURRENT installment (time-based)
        $currentInstallment = FeeInstallment::where('fee_structure_id', $feeStructureId)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('order')
            ->first();

        if (!$currentInstallment) {
            // Log::info("Student {$student->id} has no active installment for current date");
            return [
                'eligible' => false,
                'reason' => 'no_active_installment',
                'installment' => null
            ];
        }

        // Check if current date is within installment period
        $now = Carbon::now();
        $startDate = Carbon::parse($currentInstallment->start_date);
        $endDate = Carbon::parse($currentInstallment->end_date);

        if ($now->lt($startDate)) {
            return [
                'eligible' => false,
                'reason' => 'installment_not_started',
                'installment' => $currentInstallment
            ];
        }

        if ($now->gt($endDate)) {
            return [
                'eligible' => false,
                'reason' => 'installment_expired',
                'installment' => $currentInstallment
            ];
        }

        // 4. Check if student has reached required cumulative amount
        if ($totalPaid < $currentInstallment->cumulative_required) {
            // Log::info("Student {$student->id} not eligible: Paid {$totalPaid} < Required {$currentInstallment->cumulative_required}");
            return [
                'eligible' => false,
                'reason' => 'insufficient_payment',
                'installment' => $currentInstallment,
                'total_paid' => $totalPaid,
                'required' => $currentInstallment->cumulative_required,
                'shortfall' => $currentInstallment->cumulative_required - $totalPaid
            ];
        }

        // Student is eligible
        // Log::info("Student {$student->id} is ELIGIBLE for token", [
        //     'installment' => $currentInstallment->name,
        //     'total_paid' => $totalPaid,
        //     'required' => $currentInstallment->cumulative_required
        // ]);

        return [
            'eligible' => true,
            'reason' => 'eligible',
            'installment' => $currentInstallment,
            'total_paid' => $totalPaid,
            'required' => $currentInstallment->cumulative_required
        ];
    }

    /**
     * Check if token was already sent for this installment
     */
    public function wasTokenSent(Student $student, FeeInstallment $installment)
    {
        // Check if any token exists (even expired) for this installment
        $token = FeeClearanceToken::where([
            'student_id' => $student->id,
            'installment_id' => $installment->id,
        ])->first();

        if ($token) {
            // Log::info("Student {$student->id} already had token for {$installment->name} (sent at: {$token->created_at})");
            return $token;
        }

        return null;
    }

    /**
     * Generate token for eligible student (only once per installment)
     */
    public function generateToken(Student $student, FeeInstallment $installment)
    {
        return DB::transaction(function () use ($student, $installment) {

            // Check if token already exists (active or expired)
            $existingToken = FeeClearanceToken::where([
                'student_id' => $student->id,
                'installment_id' => $installment->id,
            ])->first();

            if ($existingToken) {
                // Log::info("Student {$student->id} already has token for {$installment->name}", [
                //     'token' => $existingToken->token,
                //     'status' => $existingToken->status,
                //     'expires_at' => $existingToken->expires_at
                // ]);
                return $existingToken;
            }

            // Generate unique 6-character alphanumeric token
            $token = $this->tokenGenerator->generateUniqueToken();

            // Create new token (expires at end of installment)
            $newToken = FeeClearanceToken::create([
                'student_id' => $student->id,
                'fee_structure_id' => $installment->fee_structure_id,
                'installment_id' => $installment->id,
                'token' => $token,
                'expires_at' => $installment->end_date,
                'status' => 'active',
            ]);

            // Log::info("Generated NEW token for student {$student->id}", [
            //     'token' => $token,
            //     'installment' => $installment->name,
            //     'expires_at' => $installment->end_date
            // ]);

            return $newToken;
        });
    }

    /**
     * Full process: evaluate + generate token (only if eligible and not already sent)
     */
    public function process(Student $student)
    {
        // Step 1: Evaluate if student is eligible
        $evaluation = $this->evaluate($student);

        if (!$evaluation['eligible']) {
            // Log::info("Student {$student->id} not eligible: {$evaluation['reason']}");
            return null;
        }

        $installment = $evaluation['installment'];

        // Step 2: Check if token already exists
        $existingToken = $this->wasTokenSent($student, $installment);

        if ($existingToken) {
            // Log::info("Student {$student->id} already received token for {$installment->name}. Not sending again.");
            return $existingToken;
        }

        // Step 3: Generate new token (only once per installment)
        $token = $this->generateToken($student, $installment);

        // Log::info("NEW token generated for student {$student->id} for {$installment->name}");

        return $token;
    }

    /**
     * Check token validity (for verification)
     */
    public function verifyToken($inputToken)
    {
        try {
            // Convert to uppercase and remove any dashes or spaces
            $cleanToken = strtoupper(preg_replace('/[^A-Z0-9]/', '', $inputToken));

            // Log::info('Verifying token', [
            //     'input' => $inputToken,
            //     'cleaned' => $cleanToken
            // ]);

            // Find token
            $token = FeeClearanceToken::with(['student', 'installment'])
                ->where('token', $cleanToken)
                ->where('status', 'active')
                ->where('expires_at', '>', now())
                ->first();

            if (!$token) {
                // Check if token exists but expired
                $expiredToken = FeeClearanceToken::where('token', $cleanToken)
                    ->where('expires_at', '<=', now())
                    ->first();

                if ($expiredToken) {
                    // Log::info('Token expired', [
                    //     'token' => $cleanToken,
                    //     'expires_at' => $expiredToken->expires_at
                    // ]);
                    return [
                        'valid' => false,
                        'reason' => 'expired',
                        'token' => null
                    ];
                }

                // Log::info('Token not found', ['token' => $cleanToken]);
                return [
                    'valid' => false,
                    'reason' => 'not_found',
                    'token' => null
                ];
            }

            // Log::info('Token found and valid', [
            //     'token' => $token->token,
            //     'student_id' => $token->student_id,
            //     'expires_at' => $token->expires_at
            // ]);

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
}
