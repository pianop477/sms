<?php

namespace App\Listeners;

use App\Services\FeeClearanceService;
use App\Models\FeeClearanceToken;
use App\Models\StudentFeeAssignment;
use App\Models\school_fees_payment;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SyncTokenOnPaymentChange
{
    protected $feeClearanceService;

    public function __construct(FeeClearanceService $feeClearanceService)
    {
        $this->feeClearanceService = $feeClearanceService;
    }

    /**
     * Handle payment update or delete events
     */
    public function handle($event)
    {
        try {
            // Get payment and student
            $payment = $event->payment;
            $student = $payment->student;

            if (!$student) {
                Log::warning('Payment event triggered but student not found', [
                    'payment_id' => $payment->id ?? null
                ]);
                return;
            }

            // ✅ Get academic year from payment or use current year
            $academicYear = $payment->academic_year ?? date('Y');

            // Log::info('🔁 Payment change detected - syncing token', [
            //     'student_id' => $student->id,
            //     'student_name' => $student->first_name . ' ' . $student->last_name,
            //     'academic_year' => $academicYear,
            //     'payment_id' => $payment->id,
            //     'payment_amount' => $payment->amount ?? 0,
            //     'event_type' => get_class($event)
            // ]);

            // ✅ Remove cache lock to ensure updates happen
            // $cacheKey = "token_sync_{$student->id}_{$academicYear}";
            // if (cache()->has($cacheKey)) {
            //     return;
            // }
            // cache()->put($cacheKey, true, 5);

            // ✅ Check if student has fee assignment for this academic year
            $assignment = StudentFeeAssignment::where('student_id', $student->id)
                ->where('academic_year', $academicYear)
                ->where('is_active', true)
                ->first();

            if (!$assignment) {
                // Log::info('Student has no fee assignment for this academic year', [
                //     'student_id' => $student->id,
                //     'academic_year' => $academicYear
                // ]);
                return;
            }

            // ✅ Clear any cached payment totals
            // Force fresh calculation by clearing static cache if any
            $this->feeClearanceService->clearCache($student->id, $academicYear);

            // ✅ Re-evaluate student eligibility with academic year
            $evaluation = $this->feeClearanceService->evaluate($student, $academicYear);

            // Log::info('📊 Evaluation result', [
            //     'student_id' => $student->id,
            //     'eligible' => $evaluation['eligible'],
            //     'reason' => $evaluation['reason'] ?? 'N/A',
            //     'total_paid' => $evaluation['total_paid'] ?? 0,
            //     'required' => $evaluation['required'] ?? 0
            // ]);

            // ✅ Get current active token for this academic year
            $activeToken = FeeClearanceToken::where('student_id', $student->id)
                ->where('academic_year', $academicYear)
                ->where('status', 'active')
                ->first();

            if (!$evaluation['eligible']) {
                // Student no longer qualifies - expire any active token
                if ($activeToken) {
                    $activeToken->update([
                        'status' => 'expired',
                        'updated_at' => now()
                    ]);

                    Log::warning('⚠️ Token expired due to payment correction', [
                        'student_id' => $student->id,
                        'student_name' => $student->first_name . ' ' . $student->last_name,
                        'academic_year' => $academicYear,
                        'token' => $activeToken->token,
                        'reason' => $evaluation['reason'] ?? 'payment_correction_made_student_ineligible',
                        'total_paid' => $evaluation['total_paid'] ?? 0,
                        'required' => $evaluation['required'] ?? 0
                    ]);
                }
                return;
            }

            $targetInstallment = $evaluation['installment'];

            if (!$targetInstallment) {
                Log::warning('Eligible but no target installment found', [
                    'student_id' => $student->id,
                    'academic_year' => $academicYear
                ]);
                return;
            }

            if ($activeToken) {
                // ✅ Check if token is for correct installment OR expiry date needs update
                $needsUpdate = false;
                $changes = [];

                if ($activeToken->installment_id != $targetInstallment->id) {
                    $needsUpdate = true;
                    $changes['installment_id'] = $targetInstallment->id;
                    $changes['old_installment'] = $activeToken->installment_id;
                    $changes['new_installment'] = $targetInstallment->id;
                }

                if ($activeToken->expires_at != $targetInstallment->end_date) {
                    $needsUpdate = true;
                    $changes['expires_at'] = $targetInstallment->end_date;
                    $changes['old_expiry'] = $activeToken->expires_at;
                    $changes['new_expiry'] = $targetInstallment->end_date;
                }

                if ($needsUpdate) {
                    // ✅ Update token to correct installment and expiry
                    $activeToken->update([
                        'installment_id' => $targetInstallment->id,
                        'fee_structure_id' => $targetInstallment->fee_structure_id,
                        'expires_at' => $targetInstallment->end_date,
                        'updated_at' => now()
                    ]);

                    // Log::info('✅ Token updated due to payment change', [
                    //     'student_id' => $student->id,
                    //     'student_name' => $student->first_name . ' ' . $student->last_name,
                    //     'academic_year' => $academicYear,
                    //     'token' => $activeToken->token,
                    //     'changes' => $changes
                    // ]);
                } else {
                    // Log::info('✅ Token already correct for student', [
                    //     'student_id' => $student->id,
                    //     'academic_year' => $academicYear,
                    //     'token' => $activeToken->token,
                    //     'installment' => $targetInstallment->name
                    // ]);
                }
            } else {
                // ✅ No active token, but student is eligible - create new token
                $newToken = $this->feeClearanceService->process($student, $academicYear);

                if ($newToken) {
                    $formattedToken = substr($newToken->token, 0, 3) . '-' . substr($newToken->token, 3, 3);

                    // Log::info('✅ New token created after payment change', [
                    //     'student_id' => $student->id,
                    //     'student_name' => $student->first_name . ' ' . $student->last_name,
                    //     'academic_year' => $academicYear,
                    //     'token' => $formattedToken,
                    //     'installment_id' => $targetInstallment->id,
                    //     'installment_name' => $targetInstallment->name,
                    //     'expires_at' => $targetInstallment->end_date
                    // ]);
                } else {
                    Log::error('❌ Failed to create token after payment change', [
                        'student_id' => $student->id,
                        'academic_year' => $academicYear,
                        'evaluation' => $evaluation
                    ]);
                }
            }

            // ✅ Clear cache after processing
            // cache()->forget($cacheKey);

        } catch (\Exception $e) {
            Log::error('❌ Error syncing token on payment change', [
                'student_id' => $student->id ?? null,
                'academic_year' => $payment->academic_year ?? date('Y'),
                'payment_id' => $payment->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
