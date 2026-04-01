<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Models\FeeStructure;
use App\Models\StudentFeeAssignment;
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
                            {--show-details : Show detailed assignment info per student}';

    protected $description = 'Assign fee structures to students - uses class-specific first, then general';

    public function handle()
    {
        $startTime = microtime(true);
        $this->info('🚀 Starting fee structure assignment...');
        $this->newLine();

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

        $this->displayAvailableStructures();

        $dryRun = $this->option('dry-run');
        $force = $this->option('force');
        $showDetails = $this->option('show-details');
        $chunkSize = (int) $this->option('chunk');

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
            'no_class' => 0
        ];

        $progressBar = $this->output->createProgressBar($totalStudents);
        $progressBar->start();

        Student::with(['class'])
            ->chunk($chunkSize, function ($students) use (&$stats, $dryRun, $force, $showDetails, $progressBar) {
                foreach ($students as $student) {
                    $stats['processed']++;
                    $result = $this->assignForStudent($student, $dryRun, $force, $showDetails);

                    if ($result['status'] === 'assigned') {
                        $stats['assigned']++;
                        if ($result['type'] === 'class_specific') $stats['class_specific']++;
                        if ($result['type'] === 'general') $stats['general']++;
                    } elseif ($result['status'] === 'updated') {
                        $stats['updated']++;
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

        $this->displaySummary($stats, $startTime, $dryRun);

        return 0;
    }

    private function assignForStudent($student, $dryRun, $force, $showDetails)
    {
        try {
            $classId = $student->class_id;
            $className = $student->class ? $student->class->class_name : 'NO CLASS';
            $hasTransport = !is_null($student->transport_id);

            // Check if student has a class
            if (!$classId) {
                if ($showDetails) {
                    $this->warn("\n⚠️  Student {$student->admission_number} has NO CLASS assigned!");
                }
                return ['status' => 'skipped', 'type' => 'no_class'];
            }

            // STEP 1: Try to find class-specific structure
            $selectedStructure = FeeStructure::where('school_id', $student->school_id)
                ->where('class_id', $classId)
                ->where('transport_applies', $hasTransport)
                ->where('is_hostel_class', false)
                ->first();

            $assignmentType = 'class_specific';
            $structureType = "Class-specific ({$className})";

            // STEP 2: If no class-specific structure, use general structure (class_id = null)
            if (!$selectedStructure) {
                $selectedStructure = FeeStructure::where('school_id', $student->school_id)
                    ->whereNull('class_id')
                    ->where('transport_applies', $hasTransport)
                    ->where('is_hostel_class', false)
                    ->first();

                $assignmentType = 'general';
                $structureType = "General (All Classes)";
            }

            // STEP 3: If still no structure, check for hostel class
            if (!$selectedStructure) {
                $selectedStructure = FeeStructure::where('school_id', $student->school_id)
                    ->where('class_id', $classId)
                    ->where('is_hostel_class', true)
                    ->first();

                if ($selectedStructure) {
                    $assignmentType = 'hostel';
                    $structureType = "Hostel Class ({$className})";
                }
            }

            // STEP 4: Last resort - try any structure
            if (!$selectedStructure) {
                $selectedStructure = FeeStructure::where('school_id', $student->school_id)
                    ->where('transport_applies', $hasTransport)
                    ->first();

                if ($selectedStructure) {
                    $assignmentType = 'fallback';
                    $structureType = "Fallback";
                }
            }

            if (!$selectedStructure) {
                if ($showDetails) {
                    $this->warn("\n⚠️  No fee structure for {$className} - " . ($hasTransport ? 'With Transport' : 'Without Transport'));
                }
                return ['status' => 'skipped', 'type' => null];
            }

            $existingStructureId = $student->fee_structure_id;

            // Check if update needed
            if (!$force && $existingStructureId == $selectedStructure->id) {
                if ($showDetails) {
                    $this->line("\n   ⏭️  SKIP: {$student->admission_number} - Already assigned");
                }
                return ['status' => 'skipped', 'type' => null];
            }

            // Show details if requested
            if ($showDetails) {
                $this->line("\n   📝 {$student->admission_number} - {$student->first_name} {$student->last_name}");
                $this->line("      Class: {$className}");
                $this->line("      Transport: " . ($hasTransport ? 'Yes' : 'No'));
                $this->line("      Structure Type: {$structureType}");
                $this->line("      Structure: {$selectedStructure->name}");
                $this->line("      Amount: " . number_format($selectedStructure->total_amount, 0) . " TZS");
                if ($existingStructureId) {
                    $oldStructure = FeeStructure::find($existingStructureId);
                    $this->line("      Previous: " . ($oldStructure->name ?? 'N/A'));
                }
            }

            if (!$dryRun) {
                DB::transaction(function () use ($student, $selectedStructure) {
                    $student->update([
                        'fee_structure_id' => $selectedStructure->id
                    ]);

                    StudentFeeAssignment::updateOrCreate(
                        ['student_id' => $student->id],
                        [
                            'fee_structure_id' => $selectedStructure->id,
                            'assigned_class_id' => $student->class_id,
                            'assignment_reason' => $selectedStructure->class_id ? 'class_specific' : 'general',
                            'is_active' => true,
                        ]
                    );
                });

                // Log::channel('fee_assignment')->info('Assigned fee structure', [
                //     'student_id' => $student->id,
                //     'student_name' => $student->first_name . ' ' . $student->last_name,
                //     'class_name' => $className,
                //     'has_transport' => $hasTransport,
                //     'fee_structure_id' => $selectedStructure->id,
                //     'fee_structure_name' => $selectedStructure->name,
                //     'assignment_type' => $assignmentType
                // ]);
            }

            return [
                'status' => $existingStructureId ? 'updated' : 'assigned',
                'type' => $assignmentType
            ];

        } catch (\Exception $e) {
            Log::error('Failed to assign fee structure', [
                'student_id' => $student->id,
                'error' => $e->getMessage()
            ]);

            if ($showDetails) {
                $this->error("\n   ❌ Error: {$e->getMessage()}");
            }

            return ['status' => 'error', 'type' => null];
        }
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

        // Show general structures first
        $general = $structures->whereNull('class_id');
        if ($general->count() > 0) {
            $this->line("   🌍 GENERAL STRUCTURES (Apply to all classes without specific structure):");
            foreach ($general as $s) {
                $type = $s->transport_applies ? '🚌 With Transport' : '🚶 Without Transport';
                $this->line("      • {$type}: {$s->name} - " . number_format($s->total_amount, 0) . " TZS");
            }
            $this->newLine();
        }

        // Show class-specific structures
        $specific = $structures->whereNotNull('class_id');
        if ($specific->count() > 0) {
            $this->line("   📚 CLASS-SPECIFIC STRUCTURES (Override general):");
            foreach ($specific as $s) {
                $className = $s->class ? $s->class->class_name : 'Unknown';
                $type = $s->transport_applies ? '🚌 With Transport' : '🚶 Without Transport';
                $hostel = $s->is_hostel_class ? ' [HOSTEL]' : '';
                $this->line("      • {$className}{$hostel}: {$type} - " . number_format($s->total_amount, 0) . " TZS");
            }
        }

        $this->newLine();
        $this->info("💡 NOTE: Students get class-specific structure if available, otherwise general structure applies.");
        $this->newLine();
    }

    private function displaySummary($stats, $startTime, $dryRun)
    {
        $executionTime = round(microtime(true) - $startTime, 2);

        $this->info('📈 ========== SUMMARY ==========');
        $this->info("✅ Processed: {$stats['processed']}");
        $this->info("✅ New Assignments: {$stats['assigned']}");
        $this->info("   ├─ Class-Specific: {$stats['class_specific']}");
        $this->info("   └─ General: {$stats['general']}");
        $this->info("🔄 Updated: {$stats['updated']}");
        $this->info("⚠️  Skipped: {$stats['skipped']}");
        $this->info("❌ Errors: {$stats['errors']}");
        $this->info("⏱️  Execution time: {$executionTime} seconds");

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN - No changes made');
        }
    }
}
