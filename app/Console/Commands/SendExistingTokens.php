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

    protected $description = 'Send tokens to students who already qualify for current academic year but never received token';

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
     * Get total paid for specific academic year
     */
    private function getTotalPaidForAcademicYear($student, $academicYear)
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
     * Get current active installment for the student
     * 🔥 IMPORTANT: Using DATE only, not TIME
     */
    private function getCurrentActiveInstallment($student, $feeStructureId, $academicYear)
    {
        $today = Carbon::today(); // Returns date only: 2026-04-02 00:00:00

        // Get all installments for this academic year that are active based on DATE
        $allInstallments = FeeInstallment::where('fee_structure_id', $feeStructureId)
            ->where('academic_year', $academicYear)
            ->whereDate('start_date', '<=', $today)  // 🔥 Using whereDate()
            ->whereDate('end_date', '>=', $today)    // 🔥 Using whereDate()
            ->orderBy('order')
            ->get();

        if ($allInstallments->isEmpty()) {
            return null;
        }

        $totalPaid = $this->getTotalPaidForAcademicYear($student, $academicYear);

        // Find the highest order installment that is fully paid
        $lastPaidInstallment = null;
        foreach ($allInstallments as $installment) {
            if ($totalPaid >= $installment->cumulative_required) {
                $lastPaidInstallment = $installment;
            } else {
                break;
            }
        }

        // If no installment is paid, check the first installment
        if (!$lastPaidInstallment) {
            $firstInstallment = $allInstallments->first();
            // First installment is already active (we filtered by date above)
            return $firstInstallment;
        }

        // Check if the next installment exists and is active
        $nextInstallment = $allInstallments->where('order', $lastPaidInstallment->order + 1)->first();

        if ($nextInstallment) {
            // Next installment is already active (we filtered by date above)
            return $nextInstallment;
        }

        // All installments are paid, return the last one
        return $lastPaidInstallment;
    }

    public function handle()
    {
        $academicYear = $this->getCurrentAcademicYear();
        $today = Carbon::today()->toDateString();

        $this->info('🚀 Checking for students who qualify for academic year ' . $academicYear . '...');
        $this->info("📅 Current date (for comparison): {$today}");
        $this->newLine();

        $query = Student::whereHas('feeAssignment', function ($q) {
            $q->where('is_active', true);
        });

        if ($this->option('school-id')) {
            $query->where('school_id', $this->option('school-id'));
        }

        $students = $query->get();
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');
        $chunkSize = (int) $this->option('chunk');

        $stats = [
            'processed' => 0,
            'eligible' => 0,
            'already_have_token' => 0,
            'new_tokens' => 0,
            'sms_sent' => 0,
            'errors' => 0,
            'no_installment' => 0,
            'wrong_academic_year' => 0
        ];

        $progressBar = $this->output->createProgressBar($students->count());
        $progressBar->start();

        foreach ($students->chunk($chunkSize) as $chunk) {
            foreach ($chunk as $student) {
                $stats['processed']++;
                $result = $this->processStudent($student, $dryRun, $force, $academicYear);

                if ($result === 'eligible') $stats['eligible']++;
                elseif ($result === 'already_have') $stats['already_have_token']++;
                elseif ($result === 'new_token') $stats['new_tokens']++;
                elseif ($result === 'sms_sent') $stats['sms_sent']++;
                elseif ($result === 'error') $stats['errors']++;
                elseif ($result === 'no_installment') $stats['no_installment']++;
                elseif ($result === 'wrong_academic_year') $stats['wrong_academic_year']++;

                $progressBar->advance();
            }
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info('📈 ========== SUMMARY ==========');
        $this->info("📅 Academic Year: {$academicYear}");
        $this->info("📅 Current Date: {$today}");
        $this->info("✅ Processed: {$stats['processed']}");
        $this->info("✅ Eligible students: {$stats['eligible']}");
        $this->info("   ├─ Already had token: {$stats['already_have_token']}");
        $this->info("   └─ New tokens created: {$stats['new_tokens']}");
        $this->info("📱 SMS Sent: {$stats['sms_sent']}");
        $this->info("⚠️  No active installment: {$stats['no_installment']}");
        $this->info("⚠️  Wrong academic year: {$stats['wrong_academic_year']}");
        $this->info("❌ Errors: {$stats['errors']}");

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN - No changes made');
        }

        return 0;
    }

    private function processStudent($student, $dryRun, $force, $academicYear)
    {
        try {
            $student->load('class');

            $assignment = StudentFeeAssignment::with('feeStructure')
                ->where('student_id', $student->id)
                ->where('is_active', true)
                ->first();

            if (!$assignment || !$assignment->feeStructure) {
                return 'not_eligible';
            }

            $feeStructureId = $assignment->fee_structure_id;
            $feeStructure = $assignment->feeStructure;

            $totalPaid = $this->getTotalPaidForAcademicYear($student, $academicYear);
            $currentInstallment = $this->getCurrentActiveInstallment($student, $feeStructureId, $academicYear);

            if (!$currentInstallment) {
                if (!$dryRun) {
                    $this->line("\n   ⏭️  {$student->admission_number} - No active installment for academic year {$academicYear}");
                }
                return 'no_installment';
            }

            if ($currentInstallment->academic_year != $academicYear) {
                if (!$dryRun) {
                    $this->line("\n   ⚠️  {$student->admission_number} - Installment {$currentInstallment->name} is for {$currentInstallment->academic_year}, not {$academicYear}");
                }
                return 'wrong_academic_year';
            }

            if ($totalPaid < $currentInstallment->cumulative_required) {
                if (!$dryRun) {
                    $this->line("\n   ⏭️  {$student->admission_number} - Payment insufficient");
                    $this->line("      Total Paid: " . number_format($totalPaid, 0) . " / Required: " . number_format($currentInstallment->cumulative_required, 0));
                }
                return 'not_eligible';
            }

            $existingToken = FeeClearanceToken::where([
                'student_id' => $student->id,
                'installment_id' => $currentInstallment->id,
            ])->first();

            if ($existingToken && !$force) {
                if (!$dryRun) {
                    $this->line("\n   ⏭️  {$student->admission_number} - Already has token for {$currentInstallment->name}");
                }
                return 'already_have';
            }

            if ($dryRun) {
                $this->line("\n   [DRY RUN] {$student->admission_number} - {$student->first_name} {$student->last_name}");
                $this->line("      Class: " . ($student->class->class_name ?? 'N/A'));
                $this->line("      Academic Year: {$academicYear}");
                $this->line("      Installment: {$currentInstallment->name} (Order: {$currentInstallment->order})");
                $this->line("      Installment Period: " . Carbon::parse($currentInstallment->start_date)->format('d/m/Y') . " - " . Carbon::parse($currentInstallment->end_date)->format('d/m/Y'));
                $this->line("      Total Paid: " . number_format($totalPaid, 0) . " / Required: " . number_format($currentInstallment->cumulative_required, 0));
                return 'eligible';
            }

            $this->line("\n   📝 {$student->admission_number} - {$student->first_name} {$student->last_name}");
            $this->line("      Class: " . ($student->class->class_name ?? 'N/A'));
            $this->line("      Academic Year: {$academicYear}");
            $this->line("      Fee Structure: {$feeStructure->name}");
            $this->line("      Installment: {$currentInstallment->name} (Order: {$currentInstallment->order})");
            $this->line("      Installment Period: " . Carbon::parse($currentInstallment->start_date)->format('d/m/Y') . " - " . Carbon::parse($currentInstallment->end_date)->format('d/m/Y'));
            $this->line("      Total Paid: " . number_format($totalPaid, 0) . " / Required: " . number_format($currentInstallment->cumulative_required, 0));

            $service = new FeeClearanceService();
            $tokenResult = $service->process($student);

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

            $parent = Parents::with('user')->find($student->parent_id);
            $school = school::find($student->school_id);

            if ($parent && $parent->user && $parent->user->phone) {
                $formattedToken = substr($token->token, 0, 3) . '-' . substr($token->token, 3, 3);
                $link = $this->appBaseUrl . '/tokens/verify';

                $formatedPhone = $this->formatPhoneNumberForSms($parent->user->phone);
                $message = "Gate Pass No yako ni:\n" .
                    "{$formattedToken}\n" .
                    "Kwa ajili ya: {$student->first_name} {$student->last_name}\n" .
                    "Malipo ya: {$currentInstallment->name}\n" .
                    "Expiry: " . Carbon::parse($currentInstallment->end_date)->format('d/m/Y') . "\n" .
                    "Hakiki hapa: {$link}\n";

                try {
                    $smsService = new NextSmsService();
                    $smsService->sendSmsByNext(
                        $school->sender_id ?? 'SHULE APP',
                        $formatedPhone,
                        $message,
                        'fee_clearance_' . time()
                    );
                    // Log::info("      [SIMULATION] SMS sent to: {$formatedPhone} with message: {$message}");

                    $this->line("      📱 SMS sent to: {$formatedPhone}");
                    $this->line("      📨 Token: {$formattedToken}");

                    return 'sms_sent';
                } catch (\Exception $e) {
                    $this->line("      ⚠️  SMS failed to send: " . $e->getMessage());
                    Log::error('SMS sending failed', [
                        'student_id' => $student->id,
                        'token' => $token->token,
                        'error' => $e->getMessage()
                    ]);
                    return 'new_token';
                }
            } else {
                $this->line("      ⚠️  No phone number found for parent");
                Log::warning('No parent phone found', [
                    'student_id' => $student->id,
                    'parent_id' => $student->parent_id
                ]);
                return 'new_token';
            }
        } catch (\Exception $e) {
            if (!$dryRun) {
                $this->error("\n   ❌ Error for {$student->admission_number}: {$e->getMessage()}");
            }
            Log::error('Token generation failed', [
                'student_id' => $student->id,
                'error' => $e->getMessage()
            ]);
            return 'error';
        }
    }
}
