<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Models\StudentFeeAssignment;
use App\Models\FeeStructure;
use App\Models\FeeInstallment;
use App\Models\FeeClearanceToken;
use App\Models\school;
use App\Models\Parents;
use App\Models\school_fees;
use App\Models\school_fees_payment;
use App\Services\FeeClearanceService;
use App\Services\NextSmsService;
use App\Traits\formatPhoneTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendExistingTokens extends Command
{
    use formatPhoneTrait;

    protected $signature = 'tokens:send-existing
                            {--school-id= : Process specific school}
                            {--academic-year= : Academic year to process (default: current year)}
                            {--dry-run : Run without sending actual SMS}
                            {--chunk=50 : Number of records per chunk}
                            {--force : Force send even if token exists}';

    protected $description = 'Send tokens to students who qualify for CURRENT installment based on date and payments';

    protected $appBaseUrl;

    public function __construct()
    {
        $this->appBaseUrl = config('app.url', 'http://localhost');
        parent::__construct();
    }

    /**
     * Get current academic year
     */
    private function getCurrentAcademicYear()
    {
        if ($this->option('academic-year')) {
            return $this->option('academic-year');
        }
        return date('Y');
    }

    /**
     * Get student's fee assignment for specific academic year
     */
    private function getStudentFeeAssignment($student, $academicYear)
    {
        return StudentFeeAssignment::where('student_id', $student->id)
            ->where('academic_year', $academicYear)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get total paid for specific academic year
     */
    private function getTotalPaidForAcademicYear($student, $academicYear)
    {
        $bills = school_fees::where('student_id', $student->id)
            ->where('academic_year', $academicYear)
            ->where('is_cancelled', false)
            ->pluck('id');

        if ($bills->isEmpty()) {
            return 0;
        }

        return school_fees_payment::whereIn('student_fee_id', $bills)->sum('amount');
    }

    /**
     * ✅ IMPROVED: Get current installment based on DATE + Payments
     *
     * Logic:
     * 1. Find installment where current date is between start_date and end_date
     * 2. Check if student has paid enough for that installment
     * 3. If not, check previous installment
     * 4. NEVER generate token for past installments
     */
    private function getCurrentInstallment($student, $feeStructureId, $academicYear)
    {
        $today = Carbon::today();
        $totalPaid = $this->getTotalPaidForAcademicYear($student, $academicYear);

        // ✅ Get all installments for this fee structure
        $installments = FeeInstallment::where('fee_structure_id', $feeStructureId)
            ->where('academic_year', $academicYear)
            ->orderBy('order')
            ->get();

        if ($installments->isEmpty()) {
            return null;
        }

        // ✅ Find current installment based on date
        $currentInstallment = null;
        $previousInstallment = null;

        foreach ($installments as $installment) {
            $startDate = Carbon::parse($installment->start_date);
            $endDate = Carbon::parse($installment->end_date);

            // Check if today is within this installment period
            if ($today->between($startDate, $endDate)) {
                $currentInstallment = $installment;
                break;
            }

            // Track the last installment that has ended (for payment verification)
            if ($endDate->lt($today)) {
                $previousInstallment = $installment;
            }
        }

        // ✅ If we found a current installment based on date
        if ($currentInstallment) {
            // Check if student has paid enough for this installment
            if ($totalPaid >= $currentInstallment->cumulative_required) {
                return $currentInstallment;
            }

            // If not enough payment, check if they paid for previous installment
            if ($previousInstallment && $totalPaid >= $previousInstallment->cumulative_required) {
                // They paid previous but not current - still return current (they need to pay)
                return $currentInstallment;
            }

            // No payment for current or previous
            return null;
        }

        // ✅ If no current installment found (between periods), check if we're in a gap
        // Find the next upcoming installment
        $nextInstallment = null;
        foreach ($installments as $installment) {
            $startDate = Carbon::parse($installment->start_date);
            if ($startDate->gt($today)) {
                $nextInstallment = $installment;
                break;
            }
        }

        if ($nextInstallment) {
            // Check if student has paid for all previous installments
            $allPreviousPaid = true;
            foreach ($installments as $installment) {
                $endDate = Carbon::parse($installment->end_date);
                if ($endDate->lt($today)) {
                    if ($totalPaid < $installment->cumulative_required) {
                        $allPreviousPaid = false;
                        break;
                    }
                }
            }

            if ($allPreviousPaid) {
                return $nextInstallment;
            }
        }

        return null;
    }

    public function handle()
    {
        $academicYear = $this->getCurrentAcademicYear();
        $today = Carbon::today()->toDateString();

        $this->info('🚀 Checking for students who qualify for CURRENT installment...');
        $this->info("📅 Current date: {$today}");
        $this->newLine();

        // Filter students with assignment for the specific academic year
        $studentIds = StudentFeeAssignment::where('academic_year', $academicYear)
            ->where('is_active', true)
            ->pluck('student_id');

        $query = Student::whereIn('id', $studentIds);

        if ($this->option('school-id')) {
            $query->where('school_id', $this->option('school-id'));
        }

        $students = $query->get();
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');
        $chunkSize = (int) $this->option('chunk');

        if ($students->isEmpty()) {
            $this->warn("⚠️  No students found with fee assignment for academic year {$academicYear}");
            return 0;
        }

        $stats = [
            'processed' => 0,
            'eligible' => 0,
            'already_have_token' => 0,
            'new_tokens' => 0,
            'sms_sent' => 0,
            'errors' => 0,
            'no_installment' => 0,
            'insufficient_payment' => 0,
            'no_assignment' => 0,
            'past_installment_skipped' => 0
        ];

        $progressBar = $this->output->createProgressBar($students->count());
        $progressBar->start();

        foreach ($students->chunk($chunkSize) as $chunk) {
            foreach ($chunk as $student) {
                $stats['processed']++;
                $result = $this->processStudent($student, $dryRun, $force, $academicYear, $stats);

                if ($result === 'eligible') $stats['eligible']++;
                elseif ($result === 'already_have') $stats['already_have_token']++;
                elseif ($result === 'new_token') $stats['new_tokens']++;
                elseif ($result === 'sms_sent') $stats['sms_sent']++;
                elseif ($result === 'error') $stats['errors']++;
                elseif ($result === 'no_installment') $stats['no_installment']++;
                elseif ($result === 'insufficient_payment') $stats['insufficient_payment']++;
                elseif ($result === 'no_assignment') $stats['no_assignment']++;
                elseif ($result === 'past_installment_skipped') $stats['past_installment_skipped']++;

                $progressBar->advance();
            }
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info('📈 ========== SUMMARY ==========');
        $this->info("📅 Academic Year: {$academicYear}");
        $this->info("📅 Current Date: {$today}");
        $this->info("✅ Processed: {$stats['processed']}");
        $this->info("✅ Eligible students (current installment): {$stats['eligible']}");
        $this->info("   ├─ Already had token: {$stats['already_have_token']}");
        $this->info("   └─ New tokens created: {$stats['new_tokens']}");
        $this->info("📱 SMS Sent: {$stats['sms_sent']}");
        $this->info("⚠️  Past installment skipped (no token): {$stats['past_installment_skipped']}");
        $this->info("⚠️  No installment found: {$stats['no_installment']}");
        $this->info("⚠️  Insufficient payment: {$stats['insufficient_payment']}");
        $this->info("⚠️  No assignment: {$stats['no_assignment']}");
        $this->info("❌ Errors: {$stats['errors']}");

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN - No changes were made');
        }

        return 0;
    }

    private function processStudent($student, $dryRun, $force, $academicYear, &$stats)
    {
        try {
            $student->load('class');

            // Get assignment for this specific academic year
            $assignment = $this->getStudentFeeAssignment($student, $academicYear);

            if (!$assignment) {
                if (!$dryRun) {
                    $this->line("\n   ⏭️  {$student->admission_number} - No fee assignment for year {$academicYear}");
                }
                return 'no_assignment';
            }

            $feeStructure = $assignment->feeStructure;
            if (!$feeStructure) {
                return 'no_assignment';
            }

            $feeStructureId = $feeStructure->id;
            $totalPaid = $this->getTotalPaidForAcademicYear($student, $academicYear);

            // ✅ Get CURRENT installment based on DATE (not just payments)
            $currentInstallment = $this->getCurrentInstallment($student, $feeStructureId, $academicYear);

            if (!$currentInstallment) {
                // Check if there are any installments that have passed
                $today = Carbon::today();
                $anyPastInstallment = FeeInstallment::where('fee_structure_id', $feeStructureId)
                    ->where('academic_year', $academicYear)
                    ->where('end_date', '<', $today)
                    ->exists();

                if ($anyPastInstallment && !$dryRun) {
                    $this->line("\n   ⏭️  {$student->admission_number} - Past installment detected, no token generated");
                    $this->line("      Student needs to pay current installment first");
                } elseif (!$dryRun) {
                    $this->line("\n   ⏭️  {$student->admission_number} - No active installment period found");
                }
                return 'past_installment_skipped';
            }

            // Check if student has paid enough for current installment
            if ($totalPaid < $currentInstallment->cumulative_required) {
                if (!$dryRun) {
                    $this->line("\n   ⏭️  {$student->admission_number} - Payment insufficient for current installment");
                    $this->line("      Current Installment: {$currentInstallment->name}");
                    $this->line("      Period: " . Carbon::parse($currentInstallment->start_date)->format('d/m/Y') . " - " . Carbon::parse($currentInstallment->end_date)->format('d/m/Y'));
                    $this->line("      Total Paid: " . number_format($totalPaid, 0) . " / Required: " . number_format($currentInstallment->cumulative_required, 0));
                }
                return 'insufficient_payment';
            }

            // Check if token already exists for this academic year and installment
            $existingToken = FeeClearanceToken::where('student_id', $student->id)
                ->where('academic_year', $academicYear)
                ->where('installment_id', $currentInstallment->id)
                ->where('status', 'active')
                ->first();

            if ($existingToken && !$force) {
                if (!$dryRun) {
                    $this->line("\n   ⏭️  {$student->admission_number} - Already has active token for {$currentInstallment->name}");
                }
                return 'already_have';
            }

            if ($dryRun) {
                $this->line("\n   [DRY RUN] {$student->admission_number} - {$student->first_name} {$student->last_name}");
                $this->line("      Class: " . ($student->class->class_name ?? 'N/A'));
                $this->line("      Academic Year: {$academicYear}");
                $this->line("      Current Installment: {$currentInstallment->name} (Order: {$currentInstallment->order})");
                $this->line("      Period: " . Carbon::parse($currentInstallment->start_date)->format('d/m/Y') . " - " . Carbon::parse($currentInstallment->end_date)->format('d/m/Y'));
                $this->line("      Total Paid: " . number_format($totalPaid, 0) . " / Required: " . number_format($currentInstallment->cumulative_required, 0));
                return 'eligible';
            }

            $this->line("\n   📝 {$student->admission_number} - {$student->first_name} {$student->last_name}");
            $this->line("      Class: " . ($student->class->class_name ?? 'N/A'));
            $this->line("      Academic Year: {$academicYear}");
            $this->line("      Current Installment: {$currentInstallment->name} (Order: {$currentInstallment->order})");
            $this->line("      Period: " . Carbon::parse($currentInstallment->start_date)->format('d/m/Y') . " - " . Carbon::parse($currentInstallment->end_date)->format('d/m/Y'));
            $this->line("      Total Paid: " . number_format($totalPaid, 0) . " / Required: " . number_format($currentInstallment->cumulative_required, 0));

            // Generate token using service with academic year
            $service = new FeeClearanceService();
            $tokenResult = $service->process($student, $academicYear);

            if (!$tokenResult) {
                $this->error("      ❌ Failed to generate token");
                return 'error';
            }

            $token = $tokenResult;
            $isNewToken = $token->wasRecentlyCreated ?? false;

            if ($isNewToken) {
                $this->line("      ✅ New Token: {$token->token}");
            } else {
                $this->line("      ⏭️  Token already existed: {$token->token}");
                return 'already_have';
            }

            $this->line("      Expires: " . Carbon::parse($token->expires_at)->format('d/m/Y'));

            // Send SMS
            $parent = Parents::with('user')->find($student->parent_id);
            $school = school::find($student->school_id);

            if ($parent && $parent->user && $parent->user->phone) {
                $formattedToken = substr($token->token, 0, 3) . '-' . substr($token->token, 3, 3);
                $link = $this->appBaseUrl . '/tokens/verify';

                $formattedPhone = $this->formatPhoneNumberForSms($parent->user->phone);
                $message = "GATE PASS No: {$formattedToken}\n" .
                    "Jina: {$student->first_name} {$student->last_name}\n" .
                    "Awamu: {$currentInstallment->name}\n" .
                    "Expiry: " . Carbon::parse($currentInstallment->end_date)->format('d/m/Y') . "\n" .
                    "Hakiki hapa: {$link}\n";

                try {
                    $smsService = new NextSmsService();
                    $payload = [
                        'from' => $school->sender_id ?? 'SHULE APP',
                        'to' => $formattedPhone,
                        'text' => $message,
                        'reference' => 'fee_clearance_' . time()
                    ];
                    $smsService->sendSmsByNext(
                        $payload['from'],
                        $payload['to'],
                        $payload['text'],
                         $payload['reference']
                    );

                    $this->line("      📱 SMS sent to: {$formattedPhone}");
                    $this->line("      📨 Gate Pass: {$formattedToken}");

                    return 'sms_sent';
                } catch (\Exception $e) {
                    $this->line("      ⚠️  SMS failed: " . $e->getMessage());
                    Log::error('SMS sending failed', [
                        'student_id' => $student->id,
                        'token' => $token->token,
                        'error' => $e->getMessage()
                    ]);
                    return 'new_token';
                }
            } else {
                $this->line("      ⚠️  No parent phone number found");
                return 'new_token';
            }
        } catch (\Exception $e) {
            if (!$dryRun) {
                $this->error("\n   ❌ Error: {$e->getMessage()}");
            }
            Log::error('Token generation failed', [
                'student_id' => $student->id,
                'academic_year' => $academicYear,
                'error' => $e->getMessage()
            ]);
            return 'error';
        }
    }
}
