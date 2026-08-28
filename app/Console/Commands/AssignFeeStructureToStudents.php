<?php

namespace App\Console\Commands;

use App\Models\FeeStructure;
use App\Models\Grade;
use App\Models\Student;
use App\Models\StudentFeeAssignment;
use App\Models\school_fees;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssignFeeStructureToStudents extends Command
{
    protected $signature = 'students:assign-fee-structure
                            {--school-id= : Assign for a specific school}
                            {--class-id= : Assign for a specific class}
                            {--student-id= : Assign for a specific student}
                            {--chunk=100 : Number of records to process per chunk}
                            {--dry-run : Preview changes without writing to the database}
                            {--force : Force reassignment even when no change is detected}
                            {--show-details : Show detailed information per student}
                            {--skip-transport-check : Do not check transport changes}
                            {--skip-class-check : Do not check class changes}
                            {--full-sync : Process students even when they have no existing bills}
                            {--academic-year= : Manually specify academic year}';

    protected $description = 'Safely assign fee structures to students without deleting existing bills or payment records';

    public function handle(): int
    {
        $startTime = microtime(true);

        if (!$this->validateOptions()) {
            return self::FAILURE;
        }

        $schoolId = $this->option('school-id')
            ? (int) $this->option('school-id')
            : null;

        $academicYear = $this->determineAcademicYear($schoolId);

        $dryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');
        $showDetails = (bool) $this->option('show-details');
        $fullSync = (bool) $this->option('full-sync');

        $shouldCheckTransport = !(bool) $this->option('skip-transport-check');
        $shouldCheckClass = !(bool) $this->option('skip-class-check');

        $this->info("🚀 Starting SAFE fee structure assignment for academic year {$academicYear}");
        $this->newLine();

        /*
         * IMPORTANT:
         * Use the same query for filtering, counting and processing.
         */
        $query = Student::query()
            ->with(['class', 'transport']);

        if ($schoolId !== null) {
            $query->where('school_id', $schoolId);
            $this->info("📌 Filtering by school ID: {$schoolId}");
        }

        if ($this->option('class-id')) {
            $classId = (int) $this->option('class-id');

            $query->where('class_id', $classId);
            $this->info("📌 Filtering by class ID: {$classId}");
        }

        if ($this->option('student-id')) {
            $studentId = (int) $this->option('student-id');

            $query->where('id', $studentId);
            $this->info("📌 Filtering by student ID: {$studentId}");
        }

        $totalStudents = (clone $query)->count();

        if ($totalStudents === 0) {
            $this->error('❌ No students found for the selected filters.');

            return self::FAILURE;
        }

        $this->info("📊 Total students to process: {$totalStudents}");
        $this->newLine();

        $this->displayAvailableStructures($schoolId);

        $this->displaySettings(
            $academicYear,
            $shouldCheckTransport,
            $shouldCheckClass,
            $fullSync,
            $dryRun
        );

        $stats = [
            'processed' => 0,
            'assigned' => 0,
            'reassigned' => 0,
            'skipped' => 0,
            'errors' => 0,
            'class_specific' => 0,
            'general' => 0,
            'transport_changes' => 0,
            'class_changes' => 0,
            'structure_changes' => 0,
            'has_bills' => 0,
            'no_bills' => 0,
        ];

        $progressBar = $this->output->createProgressBar($totalStudents);
        $progressBar->start();

        $query
            ->orderBy('id')
            ->chunkById(
                (int) $this->option('chunk'),
                function ($students) use (
                    &$stats,
                    $dryRun,
                    $force,
                    $showDetails,
                    $shouldCheckTransport,
                    $shouldCheckClass,
                    $fullSync,
                    $progressBar,
                    $academicYear
                ) {
                    foreach ($students as $student) {
                        try {
                            $stats['processed']++;

                            $result = $this->processStudent(
                                $student,
                                $academicYear,
                                $dryRun,
                                $force,
                                $fullSync,
                                $shouldCheckTransport,
                                $shouldCheckClass,
                                $showDetails
                            );

                            if ($result['has_bills']) {
                                $stats['has_bills']++;
                            } else {
                                $stats['no_bills']++;
                            }

                            if ($result['status'] === 'assigned') {
                                $stats['assigned']++;
                            }

                            if ($result['status'] === 'reassigned') {
                                $stats['reassigned']++;
                            }

                            if ($result['status'] === 'skipped') {
                                $stats['skipped']++;
                            }

                            if ($result['transport_changed']) {
                                $stats['transport_changes']++;
                            }

                            if ($result['class_changed']) {
                                $stats['class_changes']++;
                            }

                            if ($result['structure_changed']) {
                                $stats['structure_changes']++;
                            }

                            if ($result['assignment_type'] === 'class_specific') {
                                $stats['class_specific']++;
                            }

                            if ($result['assignment_type'] === 'general') {
                                $stats['general']++;
                            }
                        } catch (\Throwable $e) {
                            $stats['errors']++;

                            Log::error('Fee structure assignment failed', [
                                'student_id' => $student->id,
                                'admission_number' => $student->admission_number,
                                'academic_year' => $academicYear,
                                'message' => $e->getMessage(),
                            ]);

                            if ($showDetails) {
                                $this->error(
                                    "\n❌ Student {$student->id} ({$student->admission_number}): {$e->getMessage()}"
                                );
                            }
                        } finally {
                            $progressBar->advance();
                        }
                    }
                },
                'id'
            );

        $progressBar->finish();
        $this->newLine(2);

        $this->displaySummary(
            $stats,
            $startTime,
            $dryRun,
            $academicYear
        );

        return $stats['errors'] > 0
            ? self::FAILURE
            : self::SUCCESS;
    }

    /**
     * Process one student safely.
     *
     * IMPORTANT:
     * This method NEVER deletes:
     * - school_fees
     * - school_fees_payments
     * - fee_installments
     */
    private function processStudent(
        Student $student,
        int $academicYear,
        bool $dryRun,
        bool $force,
        bool $fullSync,
        bool $shouldCheckTransport,
        bool $shouldCheckClass,
        bool $showDetails
    ): array {
        $result = [
            'status' => 'skipped',
            'has_bills' => false,
            'transport_changed' => false,
            'class_changed' => false,
            'structure_changed' => false,
            'assignment_type' => null,
        ];

        /*
         * Read existing bills only.
         * No update or deletion is performed here.
         */
        $hasBills = school_fees::query()
            ->where('student_id', $student->id)
            ->where('academic_year', $academicYear)
            ->whereNotIn('status', ['expired', 'cancelled'])
            ->exists();

        $result['has_bills'] = $hasBills;

        /*
         * Normally process students with existing active bills.
         * --full-sync or --force allows processing without bills.
         */
        if (!$hasBills && !$fullSync && !$force) {
            if ($showDetails) {
                $this->line(
                    "\n⏭️ {$student->admission_number} - No active bills for {$academicYear}; skipped."
                );
            }

            return $result;
        }

        $hasTransport = !is_null($student->transport_id);
        $currentClassId = $student->class_id;

        /*
         * Previous assignment for the same academic year.
         */
        $previousAssignment = StudentFeeAssignment::query()
            ->where('student_id', $student->id)
            ->where('academic_year', $academicYear)
            ->first();

        $previousStructureId = $previousAssignment?->fee_structure_id;
        $previousTransportStatus = (bool) ($previousAssignment?->had_transport ?? false);
        $previousClassId = $previousAssignment?->assigned_class_id;

        /*
         * If this is the first assignment, it is NOT a class
         * or transport change. It is simply an initial assignment.
         */
        $transportChanged = $previousAssignment !== null
            && $shouldCheckTransport
            && ($previousTransportStatus !== $hasTransport);

        $classChanged = $previousAssignment !== null
            && $shouldCheckClass
            && ((int) $previousClassId !== (int) $currentClassId);

        $selectedStructure = $this->findBestFeeStructure(
            (int) $student->school_id,
            $currentClassId ? (int) $currentClassId : null,
            $hasTransport
        );

        if (!$selectedStructure) {
            if ($showDetails) {
                $className = $student->class?->class_name ?? 'NO CLASS';

                $this->warn(
                    "\n⚠️ {$student->admission_number} - No matching fee structure found. "
                    . "Class: {$className}; Transport: "
                    . ($hasTransport ? 'Yes' : 'No')
                );
            }

            return $result;
        }

        $structureChanged = $previousAssignment === null
            || (int) $previousStructureId !== (int) $selectedStructure->id;

        $needsReassignment =
            $force
            || $fullSync
            || $previousAssignment === null
            || $transportChanged
            || $classChanged
            || $structureChanged;

        if (!$needsReassignment) {
            if ($showDetails) {
                $this->line(
                    "\n⏭️ SKIP: {$student->admission_number} - No changes detected."
                );
            }

            return $result;
        }

        $result['transport_changed'] = $transportChanged;
        $result['class_changed'] = $classChanged;
        $result['structure_changed'] = $structureChanged;

        $result['assignment_type'] = $selectedStructure->class_id
            ? 'class_specific'
            : 'general';

        if ($showDetails) {
            $this->showStudentChanges(
                $student,
                $previousAssignment,
                $previousStructureId,
                $previousClassId,
                $selectedStructure,
                $transportChanged,
                $classChanged,
                $structureChanged,
                $hasTransport
            );
        }

        $reason = $this->determineAssignmentReason(
            $previousAssignment !== null,
            $transportChanged,
            $classChanged,
            $structureChanged,
            $force,
            $fullSync
        );

        if ($dryRun) {
            if ($showDetails) {
                $this->line(
                    "   🧪 DRY RUN: Would assign '{$selectedStructure->name}' "
                    . "to {$student->admission_number}"
                );
            }

            $result['status'] = $previousAssignment
                ? 'reassigned'
                : 'assigned';

            return $result;
        }

        /*
         * SAFE DATABASE TRANSACTION
         *
         * Only StudentFeeAssignment and Student are updated.
         */
        DB::transaction(function () use (
            $student,
            $selectedStructure,
            $hasTransport,
            $currentClassId,
            $academicYear,
            $reason
        ) {
            StudentFeeAssignment::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'academic_year' => $academicYear,
                ],
                [
                    'fee_structure_id' => $selectedStructure->id,
                    'assigned_class_id' => $currentClassId,
                    'had_transport' => $hasTransport,
                    'assignment_reason' => $selectedStructure->class_id
                        ? 'class_specific'
                        : 'general',
                    'is_active' => true,
                    'last_reassigned_at' => now(),
                    'last_reassign_reason' => $reason,
                ]
            );

            /*
             * Synchronize the student's current fee structure
             * only for the current year.
             */
            if ($academicYear === (int) now()->year) {
                $student->update([
                    'fee_structure_id' => $selectedStructure->id,
                ]);
            }

            /*
             * PRODUCTION SAFETY:
             *
             * DO NOT DELETE OR MODIFY:
             *
             * school_fees
             * school_fees_payments
             * fee_installments
             */
        });

        $result['status'] = $previousAssignment
            ? 'reassigned'
            : 'assigned';

        return $result;
    }

    /**
     * Determine why the assignment is being updated.
     */
    private function determineAssignmentReason(
        bool $hasPreviousAssignment,
        bool $transportChanged,
        bool $classChanged,
        bool $structureChanged,
        bool $force,
        bool $fullSync
    ): string {
        if (!$hasPreviousAssignment) {
            return 'initial';
        }

        if ($transportChanged && $classChanged) {
            return 'both_changed';
        }

        if ($transportChanged) {
            return 'transport_change';
        }

        if ($classChanged) {
            return 'class_change';
        }

        if ($structureChanged) {
            return 'structure_change';
        }

        if ($force) {
            return 'forced';
        }

        if ($fullSync) {
            return 'full_sync';
        }

        return 'updated';
    }

    /**
     * Determine academic year safely.
     */
    private function determineAcademicYear(?int $schoolId = null): int
    {
        if ($this->option('academic-year')) {
            $academicYear = (int) $this->option('academic-year');

            $this->info(
                "📅 Using manually specified academic year: {$academicYear}"
            );

            return $academicYear;
        }

        $currentYear = (int) now()->year;

        /*
         * Check latest academic year from bills.
         */
        $billQuery = school_fees::query();

        if ($schoolId !== null) {
            $studentIds = Student::query()
                ->where('school_id', $schoolId)
                ->select('id');

            $billQuery->whereIn('student_id', $studentIds);
        }

        $latestBillYear = $billQuery->max('academic_year');

        if ($latestBillYear !== null) {
            $latestBillYear = (int) $latestBillYear;

            if ($latestBillYear <= $currentYear) {
                $this->info(
                    "📅 Detected academic year from existing bills: {$latestBillYear}"
                );

                return $latestBillYear;
            }

            $this->warn(
                "⚠️ Future academic year {$latestBillYear} found. Using current year {$currentYear}."
            );
        }

        /*
         * Fallback to previous assignments.
         */
        $assignmentQuery = StudentFeeAssignment::query();

        if ($schoolId !== null) {
            $studentIds = Student::query()
                ->where('school_id', $schoolId)
                ->select('id');

            $assignmentQuery->whereIn('student_id', $studentIds);
        }

        $latestAssignmentYear = $assignmentQuery->max('academic_year');

        if ($latestAssignmentYear !== null) {
            $latestAssignmentYear = (int) $latestAssignmentYear;

            if ($latestAssignmentYear <= $currentYear) {
                $this->info(
                    "📅 Detected academic year from assignments: {$latestAssignmentYear}"
                );

                return $latestAssignmentYear;
            }
        }

        $this->info(
            "📅 No usable previous academic year found. Using current year: {$currentYear}"
        );

        return $currentYear;
    }

    /**
     * Find the most appropriate fee structure.
     */
    private function findBestFeeStructure(
        int $schoolId,
        ?int $classId,
        bool $hasTransport
    ): ?FeeStructure {
        /*
         * PRIORITY 1:
         * Hostel structure for the student's class.
         */
        if ($classId !== null) {
            $structure = FeeStructure::query()
                ->where('school_id', $schoolId)
                ->where('class_id', $classId)
                ->where('is_hostel_class', true)
                ->orderBy('id')
                ->first();

            if ($structure) {
                return $structure;
            }
        }

        /*
         * PRIORITY 2:
         * Class-specific structure matching transport status.
         */
        if ($classId !== null) {
            $structure = FeeStructure::query()
                ->where('school_id', $schoolId)
                ->where('class_id', $classId)
                ->where('transport_applies', $hasTransport)
                ->where('is_hostel_class', false)
                ->orderBy('id')
                ->first();

            if ($structure) {
                return $structure;
            }
        }

        /*
         * PRIORITY 3:
         * General structure matching transport status.
         */
        $structure = FeeStructure::query()
            ->where('school_id', $schoolId)
            ->whereNull('class_id')
            ->where('transport_applies', $hasTransport)
            ->where('is_hostel_class', false)
            ->orderBy('id')
            ->first();

        if ($structure) {
            return $structure;
        }

        /*
         * PRIORITY 4:
         * Any general non-hostel structure.
         */
        return FeeStructure::query()
            ->where('school_id', $schoolId)
            ->whereNull('class_id')
            ->where('is_hostel_class', false)
            ->orderBy('id')
            ->first();
    }

    /**
     * Validate command options.
     */
    private function validateOptions(): bool
    {
        $chunk = $this->option('chunk');

        if (!ctype_digit((string) $chunk) || (int) $chunk < 1) {
            $this->error('❌ --chunk must be a positive integer.');

            return false;
        }

        foreach ([
            'school-id',
            'class-id',
            'student-id',
            'academic-year',
        ] as $option) {
            $value = $this->option($option);

            if (
                $value !== null
                && $value !== ''
                && (!ctype_digit((string) $value) || (int) $value < 1)
            ) {
                $this->error(
                    "❌ --{$option} must be a valid positive integer."
                );

                return false;
            }
        }

        return true;
    }

    /**
     * Display command settings.
     */
    private function displaySettings(
        int $academicYear,
        bool $shouldCheckTransport,
        bool $shouldCheckClass,
        bool $fullSync,
        bool $dryRun
    ): void {
        $this->info('🔍 DETECTION SETTINGS:');
        $this->line("   ✓ Academic Year: {$academicYear}");
        $this->line(
            '   ✓ Transport changes: '
            . ($shouldCheckTransport ? 'ENABLED' : 'DISABLED')
        );
        $this->line(
            '   ✓ Class changes: '
            . ($shouldCheckClass ? 'ENABLED' : 'DISABLED')
        );
        $this->line(
            '   ✓ Full sync: '
            . ($fullSync ? 'ENABLED' : 'DISABLED')
        );
        $this->line(
            '   ✓ Dry run: '
            . ($dryRun ? 'YES - NO DATABASE WRITES' : 'NO')
        );
        $this->line(
            '   🛡️ Financial safety: Existing bills and payments are untouched.'
        );

        $this->newLine();
    }

    /**
     * Display available fee structures.
     */
    private function displayAvailableStructures(?int $schoolId = null): void
    {
        $structures = FeeStructure::query()
            ->with('class')
            ->when(
                $schoolId !== null,
                fn (Builder $query) => $query->where('school_id', $schoolId)
            )
            ->orderByRaw('CASE WHEN class_id IS NULL THEN 1 ELSE 0 END')
            ->orderBy('class_id')
            ->orderBy('id')
            ->get();

        if ($structures->isEmpty()) {
            $this->warn('⚠️ No fee structures found.');

            return;
        }

        $this->info('📋 Available Fee Structures:');
        $this->newLine();

        $general = $structures->whereNull('class_id');

        if ($general->isNotEmpty()) {
            $this->line('   🌍 GENERAL STRUCTURES:');

            foreach ($general as $structure) {
                $type = $structure->transport_applies
                    ? '🚌 With Transport'
                    : '🚶 Without Transport';

                $this->line(
                    "      • {$type}: {$structure->name} - "
                    . number_format($structure->total_amount, 0)
                    . ' TZS'
                );
            }

            $this->newLine();
        }

        $specific = $structures->whereNotNull('class_id');

        if ($specific->isNotEmpty()) {
            $this->line('   📚 CLASS-SPECIFIC STRUCTURES:');

            foreach ($specific as $structure) {
                $className = $structure->class?->class_name ?? 'Unknown';

                $type = $structure->transport_applies
                    ? '🚌 With Transport'
                    : '🚶 Without Transport';

                $hostel = $structure->is_hostel_class
                    ? ' [HOSTEL]'
                    : '';

                $this->line(
                    "      • {$className}{$hostel}: {$type} - "
                    . number_format($structure->total_amount, 0)
                    . ' TZS'
                );
            }
        }

        $this->newLine();
    }

    /**
     * Display changes for a student.
     */
    private function showStudentChanges(
        Student $student,
        ?StudentFeeAssignment $previousAssignment,
        ?int $previousStructureId,
        ?int $previousClassId,
        FeeStructure $selectedStructure,
        bool $transportChanged,
        bool $classChanged,
        bool $structureChanged,
        bool $hasTransport
    ): void {
        $this->line("\n🔄 PROCESSING: {$student->admission_number}");

        if (!$previousAssignment) {
            $this->line('   🆕 Initial assignment');
        }

        if ($transportChanged) {
            $this->line(
                '   🚌 Transport: '
                . ($previousAssignment->had_transport ? 'Yes' : 'No')
                . ' → '
                . ($hasTransport ? 'Yes' : 'No')
            );
        }

        if ($classChanged) {
            $oldClass = $previousClassId
                ? Grade::find($previousClassId)
                : null;

            $this->line(
                '   📚 Class: '
                . ($oldClass?->class_name ?? 'Unknown')
                . ' → '
                . ($student->class?->class_name ?? 'Unknown')
            );
        }

        if ($structureChanged) {
            $oldStructure = $previousStructureId
                ? FeeStructure::find($previousStructureId)
                : null;

            $this->line(
                '   💰 Structure: '
                . ($oldStructure?->name ?? 'None')
                . ' → '
                . $selectedStructure->name
            );
        }
    }

    /**
     * Display final summary.
     */
    private function displaySummary(
        array $stats,
        float $startTime,
        bool $dryRun,
        int $academicYear
    ): void {
        $executionTime = round(microtime(true) - $startTime, 2);

        $this->info(
            "📈 ========== SAFE ASSIGNMENT SUMMARY FOR {$academicYear} =========="
        );

        $this->info("✅ Processed: {$stats['processed']}");
        $this->info("   ├─ Has existing bills: {$stats['has_bills']}");
        $this->info("   └─ No bills: {$stats['no_bills']}");

        $this->info("🆕 Initial assignments: {$stats['assigned']}");
        $this->info("🔄 Reassignments: {$stats['reassigned']}");
        $this->info("⚠️ Skipped: {$stats['skipped']}");
        $this->info("❌ Errors: {$stats['errors']}");

        $this->info('🔍 Changes detected:');
        $this->info("   ├─ Transport: {$stats['transport_changes']}");
        $this->info("   ├─ Class: {$stats['class_changes']}");
        $this->info("   └─ Structure: {$stats['structure_changes']}");

        $this->info('📋 Assignment type:');
        $this->info("   ├─ Class-specific: {$stats['class_specific']}");
        $this->info("   └─ General: {$stats['general']}");

        $this->info("⏱️ Execution time: {$executionTime} seconds");

        if ($dryRun) {
            $this->warn(
                '🧪 DRY RUN COMPLETE - No database records were changed.'
            );
        }

        $this->info(
            '🛡️ SAFETY CONFIRMED: This command does not delete school fees, installments, or payment records.'
        );
    }
}