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

    protected $description = 'Assign fee structures to students - automatically detects academic year from existing bills and intelligently handles changes';

    public function handle()
    {
        $startTime = microtime(true);

        // ✅ AUTOMATIC: Determine academic year from existing bills (with future-year guard)
        $academicYear = $this->determineAcademicYear();

        $this->info("🚀 Starting fee structure assignment for academic year {$academicYear}...");
        $this->newLine();

        // Build query
        $query = Student::with(['class', 'transport']);

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
        $this->displayAvailableStructures();

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
        $this->line("   ✓ Academic Year: {$academicYear} (AUTO-DETECTED FROM EXISTING BILLS, overridable with --academic-year)");
        $this->line("   ✓ Transport status changes: " . ($shouldCheckTransport ? 'ENABLED' : 'DISABLED'));
        $this->line("   ✓ Class changes: " . ($shouldCheckClass ? 'ENABLED' : 'DISABLED'));
        $this->line("   ✓ Full sync mode: " . ($fullSync ? 'YES (recheck all, including students without bills)' : 'NO (only changed)'));
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

        Student::with(['class', 'transport'])
            ->chunk((int) $this->option('chunk'), function ($students) use (&$stats, $dryRun, $force, $showDetails, $shouldCheckTransport, $shouldCheckClass, $fullSync, $progressBar, $academicYear) {
                foreach ($students as $student) {
                    $stats['processed']++;

                    // ✅ Check if student has existing bills for this academic year
                    $hasBills = school_fees::where('student_id', $student->id)
                        ->where('academic_year', $academicYear)
                        ->whereNotIn('status', ['expired', 'cancelled'])
                        ->exists();

                    if ($hasBills) {
                        $stats['has_bills']++;
                    } else {
                        $stats['no_bills']++;
                        // 🔧 FIX: Only skip if NOT in full-sync mode and NOT forced
                        if (!$fullSync && !$force) {
                            if ($showDetails) {
                                $this->line("\n   ⏭️  {$student->admission_number} - No existing bills for year {$academicYear}, skipping assignment (use --full-sync to assign anyway)");
                            }
                            $progressBar->advance();
                            continue;
                        }
                        if ($showDetails) {
                            $this->warn("\n   🔄 FULL SYNC: {$student->admission_number} - No bills for {$academicYear} but assigning fee structure anyway");
                        }
                    }

                    // ✅ Get current eligibility criteria from student model
                    $hasTransport = !is_null($student->transport_id);
                    $currentClassId = $student->class_id;

                    // ✅ Get previously assigned fee structure for THIS academic year
                    $previousAssignment = StudentFeeAssignment::where('student_id', $student->id)
                        ->where('academic_year', $academicYear)
                        ->first();

                    $previousStructureId = $previousAssignment->fee_structure_id ?? null;

                    // 🔥 CRITICAL FIX: Get previous values from assignment, NOT from student
                    // These values represent the last known state when assignment was created/updated
                    $previousTransportStatus = $previousAssignment->had_transport ?? false;
                    $previousClassId = $previousAssignment->assigned_class_id ?? null;

                    // ✅ Check if changes occurred by comparing current vs previous (from assignment)
                    $transportChanged = $shouldCheckTransport && ($previousTransportStatus != $hasTransport);
                    $classChanged = $shouldCheckClass && ($previousClassId != $currentClassId);

                    // ✅ Determine if we need to reassign
                    $needsReassignment = $force || $fullSync || $transportChanged || $classChanged || !$previousAssignment;

                    // ✅ Find the correct fee structure based on current student data
                    $selectedStructure = $this->findBestFeeStructure($student->school_id, $currentClassId, $hasTransport);

                    // ✅ If no structure found, skip
                    if (!$selectedStructure) {
                        if ($showDetails) {
                            $className = $student->class ? $student->class->class_name : 'NO CLASS';
                            $this->warn("\n   ⚠️  {$student->admission_number} - No fee structure found for class: {$className}, transport: " . ($hasTransport ? 'Yes' : 'No'));
                        }
                        $stats['skipped']++;
                        $progressBar->advance();
                        continue;
                    }

                    // ✅ Check if the selected structure is different from the one currently assigned
                    $structureChanged = $previousStructureId != $selectedStructure->id;

                    // ✅ Only proceed if there's a change OR forced
                    if (!$needsReassignment && !$structureChanged) {
                        if ($showDetails) {
                            $this->line("\n   ⏭️  SKIP: {$student->admission_number} - No changes detected");
                        }
                        $stats['skipped']++;
                        $progressBar->advance();
                        continue;
                    }

                    // ✅ Log the changes
                    if ($transportChanged || $classChanged || $structureChanged) {
                        if ($showDetails) {
                            $this->line("\n   🔄 CHANGE DETECTED: {$student->admission_number}");
                            if ($transportChanged) {
                                $this->line("      🚌 Transport: " . ($previousTransportStatus ? 'Yes' : 'No') . " → " . ($hasTransport ? 'Yes' : 'No'));
                            }
                            if ($classChanged) {
                                $oldClass = Grade::find($previousClassId);
                                $newClass = Grade::find($currentClassId);
                                $this->line("      📚 Class: " . ($oldClass->class_name ?? 'Unknown') . " → " . ($newClass->class_name ?? 'Unknown'));
                            }
                            if ($structureChanged) {
                                $oldStructure = FeeStructure::find($previousStructureId);
                                $this->line("      💰 Structure: " . ($oldStructure->name ?? 'None') . " → " . $selectedStructure->name);
                            }
                        }
                    }

                    // ✅ Perform the assignment update
                    if (!$dryRun) {
                        DB::transaction(function () use ($student, $selectedStructure, $hasTransport, $currentClassId, $academicYear, $transportChanged, $classChanged) {
                            // Determine the reason for this assignment
                            $reason = 'initial';
                            if ($transportChanged) $reason = 'transport_change';
                            if ($classChanged) $reason = 'class_change';
                            if ($transportChanged && $classChanged) $reason = 'both_changed';

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
                                    'last_reassign_reason' => $reason
                                ]
                            );

                            // Update student's current fee_structure_id only if this is current year
                            if ($academicYear == date('Y')) {
                                $student->update(['fee_structure_id' => $selectedStructure->id]);
                            }

                            // ✅ Clear any cached or old installment data if needed
                            // This ensures the new structure is reflected in fee generation
                            FeeInstallment::where('student_id', $student->id)
                                ->where('academic_year', $academicYear)
                                ->delete();
                        });
                    }

                    // ✅ Update statistics
                    if ($transportChanged) $stats['transport_changes']++;
                    if ($classChanged) $stats['class_changes']++;
                    if ($structureChanged || $transportChanged || $classChanged) {
                        $stats['reassigned']++;
                    } else {
                        $stats['assigned']++;
                    }

                    if ($selectedStructure->class_id) {
                        $stats['class_specific']++;
                    } else {
                        $stats['general']++;
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
     * ✅ AUTOMATICALLY determine academic year from existing bills, but avoid future years
     */
    private function determineAcademicYear(): int
    {
        // 1. If manually specified via flag
        if ($this->option('academic-year')) {
            $this->info("📅 Using manually specified academic year: " . $this->option('academic-year'));
            return (int) $this->option('academic-year');
        }

        $currentYear = (int) date('Y');

        // 2. Check school_fees table for latest academic year with bills
        $latestBillYear = school_fees::max('academic_year');
        if ($latestBillYear) {
            // If the detected year is in the future, warn and use current year instead
            if ($latestBillYear > $currentYear) {
                $this->warn("⚠️  Found bills for future year {$latestBillYear}. Likely test data. Using current year {$currentYear} instead.");
                $this->line("   💡 To force assignment for {$latestBillYear}, use --academic-year={$latestBillYear}");
                return $currentYear;
            }
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
        $this->info("📅 No existing bills found. Using current year: {$currentYear}");
        return $currentYear;
    }

    /**
     * Find the best fee structure for a student
     * Returns null if no exact match found (no fallback)
     */
    private function findBestFeeStructure($schoolId, $classId, $hasTransport)
    {
        // PRIORITY 1: Hostel class structure (ignores transport because hostel overrides)
        $structure = FeeStructure::where('school_id', $schoolId)
            ->where('class_id', $classId)
            ->where('is_hostel_class', true)
            ->first();
        if ($structure) return $structure;

        // PRIORITY 2: Class-specific non-hostel with matching transport
        $structure = FeeStructure::where('school_id', $schoolId)
            ->where('class_id', $classId)
            ->where('transport_applies', $hasTransport)
            ->where('is_hostel_class', false)
            ->first();
        if ($structure) return $structure;

        // PRIORITY 3: General structure with matching transport (non-hostel)
        $structure = FeeStructure::where('school_id', $schoolId)
            ->whereNull('class_id')
            ->where('transport_applies', $hasTransport)
            ->where('is_hostel_class', false)
            ->first();
        if ($structure) return $structure;

        // PRIORITY 4: Fallback to any general structure without transport check
        $structure = FeeStructure::where('school_id', $schoolId)
            ->whereNull('class_id')
            ->where('is_hostel_class', false)
            ->first();
        if ($structure) return $structure;

        // NO MATCH FOUND – return null (student will be skipped)
        return null;
    }

    private function displayAvailableStructures()
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
                $hostel = $s->is_hostel_class ? ' [HOSTEL]' : '';
                $this->line("      • {$type}{$hostel}: {$s->name} - " . number_format($s->total_amount, 0) . " TZS");
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
        $this->info("   └─ No bills (processed anyway): {$stats['no_bills']}");
        $this->info("🔄 Reassigned (Changes detected): {$stats['reassigned']}");
        if ($stats['transport_changes'] > 0) {
            $this->info("   ├─ Transport Changes: {$stats['transport_changes']}");
        }
        if ($stats['class_changes'] > 0) {
            $this->info("   └─ Class Changes: {$stats['class_changes']}");
        }
        $this->info("📋 Assignment Type:");
        $this->info("   ├─ Class-Specific: {$stats['class_specific']}");
        $this->info("   └─ General: {$stats['general']}");
        $this->info("⚠️  Skipped (No changes): {$stats['skipped']}");
        $this->info("❌ Errors: {$stats['errors']}");
        $this->info("⏱️  Execution time: {$executionTime} seconds");

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN - No changes made');
        }

        $currentYear = (int) date('Y');
        if ($academicYear != $currentYear) {
            $this->warn("⚠️  NOTE: Assigned for year {$academicYear} which is different from current year {$currentYear}");
            $this->line("   Use --academic-year={$currentYear} if you meant this year");
        }
    }
}
