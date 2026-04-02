<?php

namespace App\Listeners;

use App\Services\FeeClearanceService;
use App\Models\FeeClearanceToken;
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
        $payment = $event->payment;
        $student = $payment->student;


        // Re-evaluate student eligibility
        $evaluation = $this->feeClearanceService->evaluate($student);

        // Get current active token
        $activeToken = FeeClearanceToken::where('student_id', $student->id)
            ->where('status', 'active')
            ->first();

        if (!$evaluation['eligible']) {
            // Student no longer qualifies - expire any active token
            if ($activeToken) {
                $activeToken->update(['status' => 'expired']);
                Log::warning('Token expired due to payment correction', [
                    'student_id' => $student->id,
                    'token' => $activeToken->token,
                    'reason' => 'payment_correction_made_student_ineligible'
                ]);
            }
            return;
        }

        $targetInstallment = $evaluation['installment'];

        if ($activeToken) {
            // Check if token is for correct installment
            if ($activeToken->installment_id != $targetInstallment->id) {
                // Update token to correct installment
                $activeToken->update([
                    'installment_id' => $targetInstallment->id,
                    'fee_structure_id' => $targetInstallment->fee_structure_id,
                    'expires_at' => $targetInstallment->end_date
                ]);

            }
        } else {
            // No active token, but student is eligible - create new token
            $newToken = $this->feeClearanceService->process($student);
        }
    }
}
