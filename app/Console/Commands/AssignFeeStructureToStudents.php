<?php

namespace App\Console\Commands;

use App\Models\FeeInstallment;
use Illuminate\Console\Command;
use App\Models\Student;
use App\Models\FeeStructure;
use App\Models\Grade;
use App\Models\StudentFeeAssignment;
use App\Models\SchoolFee;
use App\Models\Installment;
use App\Models\school_fees;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssignFeeStructureToStudents extends Command
{
    protected $signature = 'students:assign-fee-structure
                            {--school-id= : Assign for specific school}
                            {--class-id= : Assign for specific class}
                            {--student-id= : Assign for specific student}
                            {--chunk=100 : Number of records to process per chunk}
                            {--dry-run : Run without actually updating database}
                            {--force : Force reassign even if already assigned}
                            {--show-details : Show detailed assignment info per student}
                            {--skip-transport-check : Skip checking transport status changes}
                            {--skip-class-check : Skip checking class changes}
                            {--full-sync : Full synchronization - check all students regardless of previous assignment}
                            {--academic-year= : Manually specify academic year (optional, auto-detected if not provided)}';

    protected $description = 'Assign fee structures to students - automatically detects academic year from existing bills';

    public function handle()
    {
        $startTime = microtime(true);

        // ✅ AUTOMATIC: Determine academic year from existing bills
        $academicYear = $this->determineAcademicYear();

        $this->info("🚀 Starting fee structure assignment for academic year {$academicYear}...");
        $this->newLine();

        // Build query
        $query = Student::with(['class']);

        if ($this->option('school-id')) {
            $query->where('school_id', $this->option('school-id'));
            $this->info("📌 Filtering by school ID: " . $this->option('school-id'));
        }

        if ($this->option('class-id')) {
            $query->where('class_id', $this->option('class-id'));
            $this->info("📌 Filtering by class ID: " . $this->option('class-id'));
        }

        if ($this->option('student-id')) {
            $query->where('id', $this->option('student-id'));
            $this->info("📌 Filtering by student ID: " . $this->option('student-id'));
        }

        $totalStudents = $query->count();

        if ($totalStudents === 0) {
            $this->error('❌ No students found!');
            return 1;
        }

        $this->info("📊 Total students to process: {$totalStudents}");
        $this->newLine();

        // Display available structures for the determined academic year
        $this->displayAvailableStructures($academicYear);

        $dryRun = $this->option('dry-run');
        $force = $this->option('force');
        $showDetails = $this->option('show-details');
        $fullSync = $this->option('full-sync');

        // By default, check everything UNLESS explicitly skipped
        $skipTransportCheck = $this->option('skip-transport-check');
        $skipClassCheck = $this->option('skip-class-check');

        $shouldCheckTransport = !$skipTransportCheck;
        $shouldCheckClass = !$skipClassCheck;

        $this->info('🔍 DETECTION SETTINGS:');
        $this->line("   ✓ Academic Year: {$academicYear} (AUTO-DETECTED FROM EXISTING BILLS)");
        $this->line("   ✓ Transport status changes: " . ($shouldCheckTransport ? 'ENABLED' : 'DISABLED'));
        $this->line("   ✓ Class changes: " . ($shouldCheckClass ? 'ENABLED' : 'DISABLED'));
        $this->line("   ✓ Full sync mode: " . ($fullSync ? 'YES (recheck all)' : 'NO (only changed)'));
        $this->newLine();

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN MODE: No changes will be made');
            $this->newLine();
        }

        $stats = [
            'processed' => 0,
            'assigned' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => 0,
            'class_specific' => 0,
            'general' => 0,
            'no_class' => 0,
            'transport_changes' => 0,
            'class_changes' => 0,
            'reassigned' => 0,
            'has_bills' => 0,
            'no_bills' => 0
        ];

        $progressBar = $this->output->createProgressBar($totalStudents);
        $progressBar->start();

        Student::with(['class'])
            ->chunk((int) $this->option('chunk'), function ($students) use (&$stats, $dryRun, $force, $showDetails, $shouldCheckTransport, $shouldCheckClass, $fullSync, $progressBar, $academicYear) {
                foreach ($students as $student) {
                    $stats['processed']++;

                    // ✅ Check if student has existing bills for this academic year
                    $hasBills = school_fees::where('student_id', $student->id)
                        ->where('academic_year', $academicYear)
                        ->exists();

                    if ($hasBills) {
                        $stats['has_bills']++;
                    } else {
                        $stats['no_bills']++;
                        if ($showDetails) {
                            $this->line("\n   ⏭️  {$student->admission_number} - No existing bills for year {$academicYear}, skipping assignment");
                        }
                        $progressBar->advance();
                        continue;
                    }

                    // Get current eligibility criteria
                    $hasTransport = !is_null($student->transport_id);
                    $currentClassId = $student->class_id;

                    // Get previously assigned fee structure for THIS academic year
                    $previousAssignment = StudentFeeAssignment::where('student_id', $student->id)
                        ->where('academic_year', $academicYear)
                        ->first();

                    $previousStructureId = $previousAssignment->fee_structure_id ?? null;
                    $previousTransportStatus = $previousAssignment->had_transport ?? false;
                    $previousClassId = $previousAssignment->assigned_class_id ?? null;

                    // Check if changes occurred
                    $transportChanged = $shouldCheckTransport && ($previousTransportStatus != $hasTransport);
                    $classChanged = $shouldCheckClass && ($previousClassId != $currentClassId);

                    // Determine if we need to reassign
                    $needsReassignment = $force || $fullSync || $transportChanged || $classChanged || !$previousAssignment;

                    $result = $this->assignForStudent(
                        $student,
                        $academicYear,
                        $dryRun,
                        $force || $needsReassignment,
                        $showDetails,
                        $transportChanged,
                        $classChanged,
                        $hasTransport,
                        $currentClassId,
                        $previousTransportStatus,
                        $previousClassId,
                        $fullSync,
                        $hasBills
                    );

                    // Update statistics
                    if ($result['status'] === 'assigned') {
                        $stats['assigned']++;
                        if ($result['type'] === 'class_specific') $stats['class_specific']++;
                        if ($result['type'] === 'general') $stats['general']++;
                    } elseif ($result['status'] === 'updated') {
                        $stats['updated']++;
                    } elseif ($result['status'] === 'reassigned') {
                        $stats['reassigned']++;
                        if ($result['trigger'] === 'transport') $stats['transport_changes']++;
                        if ($result['trigger'] === 'class') $stats['class_changes']++;
                    } elseif ($result['status'] === 'skipped') {
                        $stats['skipped']++;
                    } elseif ($result['status'] === 'error') {
                        $stats['errors']++;
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
     * ✅ AUTOMATICALLY determine academic year from existing bills
     */
    private function determineAcademicYear(): int
    {
        // 1. If manually specified via flag
        if ($this->option('academic-year')) {
            $this->info("📅 Using manually specified academic year: " . $this->option('academic-year'));
            return (int) $this->option('academic-year');
        }

        // 2. Check school_fees table for latest academic year with bills
        $latestBillYear = school_fees::max('academic_year');
        if ($latestBillYear) {
            $this->info("📅 Detected academic year from existing bills: {$latestBillYear}");
            return (int) $latestBillYear;
        }

        // 3. Check student_fee_assignments for latest year
        $latestAssignmentYear = StudentFeeAssignment::max('academic_year');
        if ($latestAssignmentYear) {
            $this->info("📅 Detected academic year from existing assignments: {$latestAssignmentYear}");
            return (int) $latestAssignmentYear;
        }

        // 4. Default to current year
        $currentYear = (int) date('Y');
        $this->info("📅 No existing bills found. Using current year: {$currentYear}");
        return $currentYear;
    }

    private function assignForStudent($student, $academicYear, $dryRun, $force, $showDetails, $transportChanged = false, $classChanged = false, $hasTransport = null, $currentClassId = null, $previousTransportStatus = null, $previousClassId = null, $fullSync = false, $hasBills = false)
    {
        try {
            $classId = $student->class_id;
            $className = $student->class ? $student->class->class_name : 'NO CLASS';

            // Use passed value or get from student
            $hasTransport = $hasTransport ?? !is_null($student->transport_id);
            $currentClassId = $currentClassId ?? $student->class_id;

            // Check if student has a class
            if (!$classId) {
                if ($showDetails) {
                    $this->warn("\n⚠️  Student {$student->admission_number} has NO CLASS assigned!");
                }
                return ['status' => 'skipped', 'type' => 'no_class'];
            }

            // Find appropriate fee structure
            $selectedStructure = $this->findBestFeeStructure($student->school_id, $classId, $hasTransport);

            if (!$selectedStructure) {
                if ($showDetails) {
                    $this->warn("\n⚠️  No fee structure for {$className} - " . ($hasTransport ? 'With Transport' : 'Without Transport'));
                }
                return ['status' => 'skipped', 'type' => null];
            }

            $assignmentType = $selectedStructure->class_id ? 'class_specific' : 'general';
            $structureType = $selectedStructure->class_id ? "Class-specific ({$className})" : "General (All Classes)";

            // Get existing assignment for THIS academic year
            $existingAssignment = StudentFeeAssignment::where('student_id', $student->id)
                ->where('academic_year', $academicYear)
                ->first();

            $existingStructureId = $existingAssignment->fee_structure_id ?? null;

            // Check if reassignment is needed due to changes
            $isReassignment = false;
            $triggerReason = null;

            if ($transportChanged) {
                $isReassignment = true;
                $triggerReason = 'transport';
                if ($showDetails) {
                    $this->line("\n   🔄 TRANSPORT CHANGE: {$student->admission_number}");
                    $this->line("      From: " . ($previousTransportStatus ? 'Has Transport' : 'No Transport'));
                    $this->line("      To: " . ($hasTransport ? 'Has Transport' : 'No Transport'));
                }
            }

            if ($classChanged) {
                $isReassignment = true;
                $triggerReason = 'class';
                if ($showDetails) {
                    $this->line("\n   🔄 CLASS CHANGE: {$student->admission_number}");
                    $oldClass = Grade::find($previousClassId);
                    $newClass = Grade::find($currentClassId);
                    $this->line("      From: " . ($oldClass->class_name ?? 'Unknown'));
                    $this->line("      To: " . ($newClass->class_name ?? 'Unknown'));
                }
            }

            // Check if update needed
            if (!$force && !$fullSync && !$transportChanged && !$classChanged && $existingStructureId == $selectedStructure->id) {
                if ($showDetails) {
                    $this->line("\n   ⏭️  SKIP: {$student->admission_number} - Already assigned for {$academicYear}");
                }
                return ['status' => 'skipped', 'type' => null];
            }

            // Show details if requested
            if ($showDetails) {
                $this->line("\n   📝 {$student->admission_number} - {$student->first_name} {$student->last_name}");
                $this->line("      Academic Year: {$academicYear}");
                $this->line("      Class: {$className}");
                $this->line("      Transport: " . ($hasTransport ? 'Yes' : 'No'));
                $this->line("      Structure Type: {$structureType}");
                $this->line("      Structure: {$selectedStructure->name}");
                $this->line("      Amount: " . number_format($selectedStructure->total_amount, 0) . " TZS");
                $this->line("      Has Existing Bills: " . ($hasBills ? 'Yes' : 'No'));
                if ($existingStructureId && $existingStructureId != $selectedStructure->id) {
                    $oldStructure = FeeStructure::find($existingStructureId);
                    $this->line("      PREVIOUS: " . ($oldStructure->name ?? 'N/A'));
                    if ($transportChanged) $this->line("      REASON: Transport status changed");
                    elseif ($classChanged) $this->line("      REASON: Class changed");
                    elseif ($fullSync) $this->line("      REASON: Full sync mode");
                }
            }

            $status = 'assigned';
            if ($existingStructureId && $existingStructureId != $selectedStructure->id) {
                $status = $isReassignment ? 'reassigned' : 'updated';
            }

            if (!$dryRun) {
                DB::transaction(function () use ($student, $selectedStructure, $hasTransport, $currentClassId, $triggerReason, $academicYear) {
                    // Create or update assignment for THIS academic year
                    StudentFeeAssignment::updateOrCreate(
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
                            'last_reassign_reason' => $triggerReason
                        ]
                    );

                    // Update student's current fee_structure_id only if this is current year
                    if ($academicYear == date('Y')) {
                        $student->update(['fee_structure_id' => $selectedStructure->id]);
                    }
                });
            }

            return [
                'status' => $status,
                'type' => $assignmentType,
                'trigger' => $triggerReason
            ];
        } catch (\Exception $e) {
            Log::error('Failed to assign fee structure', [
                'student_id' => $student->id,
                'academic_year' => $academicYear,
                'error' => $e->getMessage()
            ]);

            if ($showDetails) {
                $this->error("\n   ❌ Error: {$e->getMessage()}");
            }

            return ['status' => 'error', 'type' => null];
        }
    }

    /**
     * Find the best fee structure for a student
     */
    private function findBestFeeStructure($schoolId, $classId, $hasTransport)
    {
        // Priority 1: Class-specific with matching transport
        $structure = FeeStructure::where('school_id', $schoolId)
            ->where('class_id', $classId)
            ->where('transport_applies', $hasTransport)
            ->where('is_hostel_class', false)
            ->first();

        if ($structure) return $structure;

        // Priority 2: General structure with matching transport
        $structure = FeeStructure::where('school_id', $schoolId)
            ->whereNull('class_id')
            ->where('transport_applies', $hasTransport)
            ->where('is_hostel_class', false)
            ->first();

        if ($structure) return $structure;

        // Priority 3: Hostel class structure
        $structure = FeeStructure::where('school_id', $schoolId)
            ->where('class_id', $classId)
            ->where('is_hostel_class', true)
            ->first();

        if ($structure) return $structure;

        // Priority 4: Any structure with matching transport (fallback)
        $structure = FeeStructure::where('school_id', $schoolId)
            ->where('transport_applies', $hasTransport)
            ->first();

        return $structure;
    }

    private function displayAvailableStructures($academicYear)
    {
        $schoolId = $this->option('school-id');

        $structures = FeeStructure::with('class')
            ->when($schoolId, function ($query) use ($schoolId) {
                $query->where('school_id', $schoolId);
            })
            ->orderByRaw('CASE WHEN class_id IS NULL THEN 1 ELSE 0 END')
            ->orderBy('class_id')
            ->get();

        if ($structures->isEmpty()) {
            $this->warn('⚠️  No fee structures found!');
            return;
        }

        $this->info('📋 Available Fee Structures:');
        $this->newLine();

        $general = $structures->whereNull('class_id');
        if ($general->count() > 0) {
            $this->line("   🌍 GENERAL STRUCTURES (Apply to all classes):");
            foreach ($general as $s) {
                $type = $s->transport_applies ? '🚌 With Transport' : '🚶 Without Transport';
                $this->line("      • {$type}: {$s->name} - " . number_format($s->total_amount, 0) . " TZS");
            }
            $this->newLine();
        }

        $specific = $structures->whereNotNull('class_id');
        if ($specific->count() > 0) {
            $this->line("   📚 CLASS-SPECIFIC STRUCTURES:");
            foreach ($specific as $s) {
                $className = $s->class ? $s->class->class_name : 'Unknown';
                $type = $s->transport_applies ? '🚌 With Transport' : '🚶 Without Transport';
                $hostel = $s->is_hostel_class ? ' [HOSTEL]' : '';
                $this->line("      • {$className}{$hostel}: {$type} - " . number_format($s->total_amount, 0) . " TZS");
            }
        }

        $this->newLine();
    }

    private function displaySummary($stats, $startTime, $dryRun, $academicYear)
    {
        $executionTime = round(microtime(true) - $startTime, 2);

        $this->info('📈 ========== SUMMARY for ' . $academicYear . ' ==========');
        $this->info("✅ Processed: {$stats['processed']}");
        $this->info("   ├─ Has existing bills: {$stats['has_bills']}");
        $this->info("   └─ No bills (skipped): {$stats['no_bills']}");
        $this->info("✅ New Assignments: {$stats['assigned']}");
        $this->info("   ├─ Class-Specific: {$stats['class_specific']}");
        $this->info("   └─ General: {$stats['general']}");
        $this->info("🔄 Updated: {$stats['updated']}");

        if ($stats['reassigned'] > 0) {
            $this->info("🔄 REASSIGNED (Auto-detected changes): {$stats['reassigned']}");
            if ($stats['transport_changes'] > 0) {
                $this->info("   ├─ Transport Changes: {$stats['transport_changes']}");
            }
            if ($stats['class_changes'] > 0) {
                $this->info("   └─ Class Changes: {$stats['class_changes']}");
            }
        }

        $this->info("⚠️  Skipped: {$stats['skipped']}");
        $this->info("❌ Errors: {$stats['errors']}");
        $this->info("⏱️  Execution time: {$executionTime} seconds");

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN - No changes made');
        }
    }
}
