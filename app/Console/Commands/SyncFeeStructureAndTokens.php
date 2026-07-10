<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Models\StudentFeeAssignment;
use App\Models\FeeClearanceToken;
use App\Models\FeeInstallment;
use App\Models\FeeStructure;
use App\Models\Grade;
use App\Models\school_fees;
use App\Models\school_fees_payment;
use App\Models\Parents;
use App\Models\school;
use App\Services\NextSmsService;
use App\Services\TokenGeneratorService;
use App\Traits\formatPhoneTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SyncFeeStructureAndTokens extends Command
{
    use formatPhoneTrait;

    protected $signature = 'fee:sync-all
                            {--academic-year= : Academic year (auto-detected if not provided)}
                            {--student-id= : Process specific student}
                            {--school-id= : Process students from specific school}
                            {--class-id= : Process students from specific class}
                            {--dry-run : Run without making changes}
                            {--show-details : Show detailed information}
                            {--force : Force sync even if no changes detected}
                            {--chunk=100 : Number of records to process per chunk}';

    protected $description = 'Intelligent fee sync - updates assignments & tokens, sends SMS only when token becomes active from inactive/expired state';

    protected $tokenGenerator;

    public function __construct()
    {
        parent::__construct();
        $this->tokenGenerator = new TokenGeneratorService();
    }

    public function handle()
    {
        $startTime = microtime(true);

        $academicYear = $this->determineAcademicYear();
        $dryRun = $this->option('dry-run');
        $showDetails = $this->option('show-details');
        $force = $this->option('force');

        $this->info('🔄 Starting intelligent fee sync...');
        $this->info("📅 Academic Year: {$academicYear}");
        $this->newLine();

        // Build student query
        $query = Student::with(['class', 'transport']);

        if ($this->option('student-id')) {
            $query->where('id', $this->option('student-id'));
            $this->info("📌 Filtering by student ID: " . $this->option('student-id'));
        }

        if ($this->option('school-id')) {
            $query->where('school_id', $this->option('school-id'));
            $this->info("📌 Filtering by school ID: " . $this->option('school-id'));
        }

        if ($this->option('class-id')) {
            $query->where('class_id', $this->option('class-id'));
            $this->info("📌 Filtering by class ID: " . $this->option('class-id'));
        }

        $totalStudents = $query->count();

        if ($totalStudents === 0) {
            $this->error('❌ No students found!');
            return 1;
        }

        $this->info("📊 Total students to process: {$totalStudents}");
        $this->newLine();

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN MODE: No changes will be made');
            $this->newLine();
        }

        $stats = [
            'processed' => 0,
            'assignments_updated' => 0,
            'tokens_created' => 0,
            'tokens_updated' => 0,
            'tokens_deactivated' => 0,
            'sms_sent' => 0,
            'no_changes' => 0,
            'errors' => 0,
            'transport_changes' => 0,
            'class_changes' => 0,
            'structure_changes' => 0
        ];

        $progressBar = $this->output->createProgressBar($totalStudents);
        $progressBar->start();

        Student::with(['class', 'transport'])
            ->chunk((int) $this->option('chunk'), function ($students) use (&$stats, $dryRun, $showDetails, $force, $progressBar, $academicYear) {
                foreach ($students as $student) {
                    $stats['processed']++;

                    $result = $this->processStudent(
                        $student,
                        $academicYear,
                        $dryRun,
                        $showDetails,
                        $force
                    );

                    // Update statistics
                    switch ($result['action']) {
                        case 'assignment_updated':
                            $stats['assignments_updated']++;
                            if ($result['transport_changed'] ?? false) $stats['transport_changes']++;
                            if ($result['class_changed'] ?? false) $stats['class_changes']++;
                            if ($result['structure_changed'] ?? false) $stats['structure_changes']++;
                            break;
                        case 'token_created':
                            $stats['tokens_created']++;
                            if ($result['sms_sent'] ?? false) $stats['sms_sent']++;
                            break;
                        case 'token_updated':
                            $stats['tokens_updated']++;
                            if ($result['sms_sent'] ?? false) $stats['sms_sent']++;
                            break;
                        case 'token_deactivated':
                            $stats['tokens_deactivated']++;
                            break;
                        case 'no_changes':
                            $stats['no_changes']++;
                            break;
                        case 'error':
                            $stats['errors']++;
                            break;
                    }

                    $progressBar->advance();
                }
            });

        $progressBar->finish();
        $this->newLine(2);

        $this->displaySummary($stats, $startTime, $dryRun, $academicYear);

        return 0;
    }

    /**
     * Process a single student - updates assignment AND token
     */
    private function processStudent($student, int $academicYear, bool $dryRun, bool $showDetails, bool $force): array
    {
        try {
            // ============================================
            // STEP 1: CHECK FOR CHANGES IN STUDENT DATA
            // ============================================
            $hasTransport = !is_null($student->transport_id);
            $currentClassId = $student->class_id;

            // Get existing assignment
            $existingAssignment = StudentFeeAssignment::where('student_id', $student->id)
                ->where('academic_year', $academicYear)
                ->first();

            $previousStructureId = $existingAssignment->fee_structure_id ?? null;
            $previousTransportStatus = $existingAssignment->had_transport ?? false;
            $previousClassId = $existingAssignment->assigned_class_id ?? null;

            // Find the correct fee structure
            $selectedStructure = $this->findBestFeeStructure($student->school_id, $currentClassId, $hasTransport);

            if (!$selectedStructure) {
                if ($showDetails) {
                    $className = $student->class ? $student->class->class_name : 'NO CLASS';
                    $this->warn("\n   ⚠️  {$student->admission_number} - No fee structure found for class: {$className}");
                }
                return ['action' => 'no_changes', 'reason' => 'no_fee_structure'];
            }

            // Detect changes
            $transportChanged = ($previousTransportStatus != $hasTransport);
            $classChanged = ($previousClassId != $currentClassId);
            $structureChanged = ($previousStructureId != $selectedStructure->id);

            $assignmentNeedsUpdate = $force || $transportChanged || $classChanged || $structureChanged || !$existingAssignment;

            // ============================================
            // STEP 2: GET CURRENT TOKEN STATUS (BEFORE ANY CHANGES)
            // ============================================
            $existingToken = FeeClearanceToken::where('student_id', $student->id)
                ->where('academic_year', $academicYear)
                ->first();

            $previousTokenStatus = $existingToken ? $existingToken->status : 'none';
            $previousTokenInstallmentId = $existingToken ? $existingToken->installment_id : null;

            // ============================================
            // STEP 3: UPDATE ASSIGNMENT IF NEEDED
            // ============================================
            $assignmentUpdated = false;

            if ($assignmentNeedsUpdate && !$dryRun) {
                DB::transaction(function () use ($student, $selectedStructure, $hasTransport, $currentClassId, $academicYear, $transportChanged, $classChanged, &$assignmentUpdated) {
                    $assignment = StudentFeeAssignment::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'academic_year' => $academicYear
                        ],
                        [
                            'fee_structure_id' => $selectedStructure->id,
                            'assigned_class_id' => $currentClassId,
                            'had_transport' => $hasTransport,
                            'assignment_reason' => $selectedStructure->class_id ? 'class_specific' : 'general',
                            'is_active' => true,
                            'last_reassigned_at' => now(),
                            'last_reassign_reason' => $this->determineReason($transportChanged, $classChanged)
                        ]
                    );

                    $assignmentUpdated = $assignment->wasRecentlyCreated || $assignment->wasChanged();

                    // Update student's current fee_structure_id
                    if ($academicYear == date('Y')) {
                        $student->update(['fee_structure_id' => $selectedStructure->id]);
                    }
                });
            }

            // ============================================
            // STEP 4: CALCULATE CORRECT INSTALLMENT
            // ============================================
            $totalPaid = $this->getTotalPaidForAcademicYear($student, $academicYear);
            $targetInstallment = $this->getTargetInstallment(
                $selectedStructure->id,
                $academicYear,
                $totalPaid
            );

            if (!$targetInstallment) {
                if ($showDetails) {
                    $this->line("\n   ⏭️  {$student->admission_number} - No installments found");
                }
                return ['action' => 'no_changes', 'reason' => 'no_installments'];
            }

            $isEligible = $totalPaid >= $targetInstallment->cumulative_required;

            // ============================================
            // STEP 5: UPDATE TOKEN BASED ON ELIGIBILITY
            // ============================================
            $tokenAction = 'no_changes';
            $smsSent = false;
            $tokenUpdated = false;

            if (!$dryRun) {
                if (!$isEligible) {
                    // Deactivate token if exists and is active
                    if ($existingToken && $existingToken->status !== 'inactive' && $existingToken->status !== 'expired') {
                        $existingToken->status = 'inactive';
                        $existingToken->save();
                        $tokenAction = 'token_deactivated';
                        $tokenUpdated = true;

                        if ($showDetails) {
                            $this->line("\n   🔴 {$student->admission_number} - Token DEACTIVATED (insufficient payment)");
                        }
                    } else {
                        $tokenAction = 'no_changes';
                        if ($showDetails) {
                            $this->line("\n   ⏭️  {$student->admission_number} - No active token (insufficient payment)");
                        }
                    }
                } else {
                    // Eligible - create or update token
                    if ($existingToken) {
                        // Check if token needs updating
                        $needsUpdate = false;
                        $changes = [];

                        if ($existingToken->installment_id != $targetInstallment->id) {
                            $needsUpdate = true;
                            $changes[] = "installment";
                        }
                        if ($existingToken->fee_structure_id != $selectedStructure->id) {
                            $needsUpdate = true;
                            $changes[] = "fee_structure";
                        }
                        if ($existingToken->expires_at != $targetInstallment->end_date) {
                            $needsUpdate = true;
                            $changes[] = "expiry_date";
                        }
                        if ($existingToken->status !== 'active') {
                            $needsUpdate = true;
                            $changes[] = "status: {$existingToken->status} → active";
                        }

                        // Only update if there are changes OR force flag
                        if ($force || $needsUpdate || $assignmentUpdated) {
                            $oldStatus = $existingToken->status;

                            $existingToken->installment_id = $targetInstallment->id;
                            $existingToken->fee_structure_id = $selectedStructure->id;
                            $existingToken->expires_at = $targetInstallment->end_date;
                            $existingToken->status = 'active';
                            $existingToken->save();

                            $tokenUpdated = true;
                            $tokenAction = 'token_updated';

                            // ============================================
                            // ★★★ INTELLIGENT SMS LOGIC ★★★
                            // Send SMS ONLY IF: token was NOT active before
                            // ============================================
                            $wasActiveBefore = in_array($previousTokenStatus, ['active']);

                            if (!$wasActiveBefore && !empty($changes)) {
                                $smsSent = $this->sendTokenSms($student, $academicYear);
                                if ($showDetails) {
                                    $this->line("\n   📱 SMS SENT: Token became active from {$previousTokenStatus}");
                                }
                            } else {
                                if ($showDetails) {
                                    $this->line("\n   📝 Token updated but NO SMS: Token was already active");
                                }
                            }

                            if ($showDetails) {
                                $this->line("\n   🔄 {$student->admission_number} - Token UPDATED");
                                foreach ($changes as $change) {
                                    $this->line("      • {$change}");
                                }
                                $this->line("      • Previous status: {$previousTokenStatus}");
                                $this->line("      • New status: active");
                                $this->line("      • SMS sent: " . ($smsSent ? '✅ YES' : '❌ NO'));
                            }
                        } else {
                            $tokenAction = 'no_changes';
                            if ($showDetails) {
                                $this->line("\n   ✅ {$student->admission_number} - Token already correct");
                            }
                        }
                    } else {
                        // Create new token
                        $tokenCode = $this->tokenGenerator->generateUniqueToken();
                        $newToken = FeeClearanceToken::create([
                            'student_id' => $student->id,
                            'academic_year' => $academicYear,
                            'fee_structure_id' => $selectedStructure->id,
                            'installment_id' => $targetInstallment->id,
                            'token' => $tokenCode,
                            'expires_at' => $targetInstallment->end_date,
                            'status' => 'active',
                        ]);

                        $tokenUpdated = true;
                        $tokenAction = 'token_created';

                        // ============================================
                        // ★★★ INTELLIGENT SMS LOGIC ★★★
                        // Send SMS for NEW token (was no token before)
                        // ============================================
                        $smsSent = $this->sendTokenSms($student, $academicYear);

                        if ($showDetails) {
                            $this->line("\n   🟢 {$student->admission_number} - New Token CREATED");
                            $this->line("      • Token: {$tokenCode}");
                            $this->line("      • Installment: {$targetInstallment->name}");
                            $this->line("      • Expires: " . Carbon::parse($targetInstallment->end_date)->format('d/m/Y'));
                            $this->line("      • SMS sent: " . ($smsSent ? '✅ YES' : '❌ NO'));
                        }
                    }
                }
            }

            // ============================================
            // STEP 6: RETURN RESULT
            // ============================================
            // Determine the main action
            $mainAction = 'no_changes';
            if ($assignmentUpdated && ($tokenAction === 'token_created' || $tokenAction === 'token_updated')) {
                $mainAction = 'assignment_and_token_updated';
            } elseif ($assignmentUpdated) {
                $mainAction = 'assignment_updated';
            } elseif ($tokenAction === 'token_created' || $tokenAction === 'token_updated') {
                $mainAction = $tokenAction;
            } elseif ($tokenAction === 'token_deactivated') {
                $mainAction = $tokenAction;
            }

            return [
                'action' => $mainAction,
                'assignment_updated' => $assignmentUpdated,
                'token_updated' => $tokenUpdated,
                'transport_changed' => $transportChanged,
                'class_changed' => $classChanged,
                'structure_changed' => $structureChanged,
                'sms_sent' => $smsSent,
                'previous_token_status' => $previousTokenStatus,
                'new_token_status' => $existingToken ? $existingToken->status : 'active'
            ];

        } catch (\Exception $e) {
            Log::error('Fee sync error', [
                'student_id' => $student->id,
                'academic_year' => $academicYear,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($showDetails) {
                $this->error("\n   ❌ {$student->admission_number} - Error: " . $e->getMessage());
            }

            return ['action' => 'error', 'reason' => $e->getMessage()];
        }
    }

    /**
     * Send SMS with token details - ONLY for newly active tokens
     */
    private function sendTokenSms($student, int $academicYear): bool
    {
        try {
            $token = FeeClearanceToken::where('student_id', $student->id)
                ->where('academic_year', $academicYear)
                ->where('status', 'active')
                ->first();

            if (!$token) {
                return false;
            }

            $parent = Parents::with('user')->find($student->parent_id);
            $school = school::find($student->school_id);

            if (!$parent || !$parent->user || !$parent->user->phone) {
                Log::info('No parent phone for SMS', ['student_id' => $student->id]);
                return false;
            }

            $phone = $this->formatPhoneNumberForSms($parent->user->phone);
            $installment = $token->installment;

            $message = "GATEPASS NA. ni: {$token->token}\n" .
                "Jina la Mtoto: {$student->first_name} {$student->last_name}\n" .
                "Awamu: " . ($installment->name ?? 'Current') . "\n" .
                "Mwisho wa Awamu: " . Carbon::parse($token->expires_at)->format('d/m/Y') . "\n" .
                "Hakiki uwapo shuleni/kwenye Basi";

            $smsService = new NextSmsService();
            $payload = [
                'from' => $school->sender_id ?? 'SHULE APP',
                'to' => $phone,
                'text' => $message,
                'reference' => 'fee_sync_' . time() . '_' . $student->id,
            ];

            $smsService->sendSmsByNext($payload['from'], $payload['to'], $payload['text'], $payload['reference']);

            Log::info('SMS sent for token activation', [
                'student_id' => $student->id,
                'token' => $token->token,
                'phone' => $phone
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('SMS sending failed', [
                'student_id' => $student->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get total paid for a student in an academic year
     */
    private function getTotalPaidForAcademicYear($student, int $academicYear): float
    {
        return (float) school_fees_payment::whereHas('bill', function ($q) use ($student, $academicYear) {
            $q->where('student_id', $student->id)
                ->where('academic_year', $academicYear);
        })->sum('amount');
    }

    /**
     * Get the target installment based on cumulative amount
     */
    private function getTargetInstallment(int $feeStructureId, int $academicYear, float $totalPaid)
    {
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

    /**
     * Find the best fee structure for a student
     */
    private function findBestFeeStructure($schoolId, $classId, $hasTransport)
    {
        // PRIORITY 1: Hostel class structure
        $structure = FeeStructure::where('school_id', $schoolId)
            ->where('class_id', $classId)
            ->where('is_hostel_class', true)
            ->first();
        if ($structure) return $structure;

        // PRIORITY 2: Class-specific with matching transport
        $structure = FeeStructure::where('school_id', $schoolId)
            ->where('class_id', $classId)
            ->where('transport_applies', $hasTransport)
            ->where('is_hostel_class', false)
            ->first();
        if ($structure) return $structure;

        // PRIORITY 3: General with matching transport
        $structure = FeeStructure::where('school_id', $schoolId)
            ->whereNull('class_id')
            ->where('transport_applies', $hasTransport)
            ->where('is_hostel_class', false)
            ->first();
        if ($structure) return $structure;

        // PRIORITY 4: Fallback to any general structure
        $structure = FeeStructure::where('school_id', $schoolId)
            ->whereNull('class_id')
            ->where('is_hostel_class', false)
            ->first();
        if ($structure) return $structure;

        return null;
    }

    /**
     * Determine the reason for reassignment
     */
    private function determineReason(bool $transportChanged, bool $classChanged): string
    {
        if ($transportChanged && $classChanged) return 'both_changed';
        if ($transportChanged) return 'transport_change';
        if ($classChanged) return 'class_change';
        return 'manual_update';
    }

    /**
     * Determine academic year
     */
    private function determineAcademicYear(): int
    {
        if ($this->option('academic-year')) {
            $this->info("📅 Using manually specified academic year: " . $this->option('academic-year'));
            return (int) $this->option('academic-year');
        }

        $currentYear = (int) date('Y');

        // Check latest from assignments
        $latestAssignmentYear = StudentFeeAssignment::max('academic_year');
        if ($latestAssignmentYear) {
            if ($latestAssignmentYear > $currentYear) {
                $this->warn("⚠️  Found assignments for future year {$latestAssignmentYear}. Using current year {$currentYear}.");
                return $currentYear;
            }
            $this->info("📅 Detected academic year from assignments: {$latestAssignmentYear}");
            return (int) $latestAssignmentYear;
        }

        // Check from fee structures
        $latestStructureYear = FeeStructure::max('academic_year');
        if ($latestStructureYear) {
            $this->info("📅 Detected academic year from fee structures: {$latestStructureYear}");
            return (int) $latestStructureYear;
        }

        $this->info("📅 Using current year: {$currentYear}");
        return $currentYear;
    }

    private function displaySummary($stats, $startTime, $dryRun, $academicYear)
    {
        $executionTime = round(microtime(true) - $startTime, 2);

        $this->info('📈 ========== SYNC SUMMARY ==========');
        $this->info("📅 Academic Year: {$academicYear}");
        $this->info("✅ Processed: {$stats['processed']}");
        $this->newLine();

        $this->info("📋 ASSIGNMENT UPDATES:");
        $this->info("   ├─ Assignments Updated: {$stats['assignments_updated']}");
        if ($stats['transport_changes'] > 0) {
            $this->info("   │  ├─ Transport Changes: {$stats['transport_changes']}");
        }
        if ($stats['class_changes'] > 0) {
            $this->info("   │  └─ Class Changes: {$stats['class_changes']}");
        }
        if ($stats['structure_changes'] > 0) {
            $this->info("   └─ Structure Changes: {$stats['structure_changes']}");
        }

        $this->newLine();
        $this->info("🔑 TOKEN UPDATES:");
        $this->info("   ├─ Tokens Created: {$stats['tokens_created']}");
        $this->info("   ├─ Tokens Updated: {$stats['tokens_updated']}");
        $this->info("   └─ Tokens Deactivated: {$stats['tokens_deactivated']}");

        $this->newLine();
        $this->info("📱 SMS:");
        $this->info("   └─ SMS Sent: {$stats['sms_sent']} (Only for newly activated tokens)");

        $this->newLine();
        $this->info("⏭️  No Changes: {$stats['no_changes']}");
        $this->info("❌ Errors: {$stats['errors']}");
        $this->info("⏱️  Execution time: {$executionTime} seconds");

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN - No changes made');
        }
    }
}
