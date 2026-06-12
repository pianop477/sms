<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\ReportJob;
use App\Models\Student;
use App\Models\school;
use App\Models\Examination_result;
use App\Models\generated_reports;
use App\Models\Grade;
use Hashids;
use setasign\Fpdi\Fpdi;
use Crypt;
use DB;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

class GenerateBulkReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public $timeout = 7200;   // 2 hours max
    public $tries = 3;

    protected $jobId;
    protected $params;

    public function __construct($jobId, $params)
    {
        $this->jobId = $jobId;
        $this->params = $params;
    }

    public function handle()
    {
        try {
            ReportJob::where('job_id', $this->jobId)->update([
                'status' => 'processing',
                'processed_students' => 0
            ]);

            $reportType = $this->params['report_type'];

            if ($reportType === 'individual') {
                $this->generateIndividualReports();
            } else {
                $this->generateCombinedReports();
            }

        } catch (\Exception $e) {
            Log::error('Bulk Report Generation failed: ' . $e->getMessage());
            ReportJob::where('job_id', $this->jobId)->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Generate individual student reports (one PDF per student, then merge)
     */
    private function generateIndividualReports()
    {
        $schoolId = Hashids::decode($this->params['school'])[0];
        $classId = Hashids::decode($this->params['class'])[0];
        $examId = Hashids::decode($this->params['examType'])[0];
        $date = $this->params['date'];

        $schoolInfo = school::find($schoolId);
        $marking_style = Examination_result::query()
            ->where('school_id', $schoolId)
            ->where('class_id', $classId)
            ->where('exam_type_id', $examId)
            ->whereDate('exam_date', $date)
            ->value('marking_style') ?? 1;

        $studentIds = Student::where('class_id', $classId)
            ->where('school_id', $schoolId)
            ->where('status', 1)
            ->orderBy('admission_number')
            ->pluck('id');

        $totalStudents = $studentIds->count();
        ReportJob::where('job_id', $this->jobId)->update(['total_students' => $totalStudents]);

        // Temp folder for individual PDFs
        $tempDir = storage_path('app/temp_pdfs/' . uniqid());
        if (!File::exists($tempDir)) File::makeDirectory($tempDir, 0777, true);

        // ------------------- Pre-calculate rankings (once) -------------------
        $allResults = Examination_result::query()
            ->join('students', 'students.id', '=', 'examination_results.student_id')
            ->join('subjects', 'subjects.id', '=', 'examination_results.course_id')
            ->join('grades', 'grades.id', '=', 'examination_results.class_id')
            ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
            ->join('schools', 'schools.id', '=', 'examination_results.school_id')
            ->leftJoin('teachers', 'teachers.id', '=', 'examination_results.teacher_id')
            ->leftJoin('users as teacher_users', 'teacher_users.id', '=', 'teachers.user_id')
            ->select(
                'examination_results.*',
                'students.first_name', 'students.middle_name', 'students.last_name',
                'students.admission_number', 'students.group', 'students.image', 'students.gender',
                'students.id as student_id',
                'subjects.course_name', 'subjects.course_code', 'grades.class_name',
                'examinations.exam_type', 'examination_results.Exam_term',
                'teacher_users.first_name as teacher_first_name', 'teacher_users.last_name as teacher_last_name',
                'schools.school_name', 'schools.postal_address', 'schools.postal_name',
                'schools.logo', 'schools.country', 'schools.school_phone', 'schools.school_email'
            )
            ->where('examination_results.school_id', $schoolId)
            ->where('examination_results.class_id', $classId)
            ->where('examination_results.exam_type_id', $examId)
            ->where('students.status', 1)
            ->whereDate('examination_results.exam_date', $date)
            ->get();

        $resultsByStudent = $allResults->groupBy('student_id');

        // Subject ranks
        $subjectRanksCache = [];
        foreach ($allResults->groupBy('course_id') as $courseId => $courseResults) {
            $scores = $courseResults->pluck('score', 'student_id');
            $sortedScores = $scores->sortDesc();
            $rank = 1; $prev = null; $skip = 0; $ranks = [];
            foreach ($sortedScores as $sid => $score) {
                if ($prev !== null && $score < $prev) {
                    $rank += $skip;
                    $skip = 1;
                } else {
                    $skip++;
                }
                $ranks[$sid] = $rank;
                $prev = $score;
            }
            $subjectRanksCache[$courseId] = $ranks;
        }

        // Overall student averages and ranks
        $studentAverages = [];
        foreach ($resultsByStudent as $sid => $studentResults) {
            $studentAverages[$sid] = $studentResults->avg('score');
        }
        arsort($studentAverages);
        $rank = 1; $prevAvg = null; $skip = 0; $studentRankings = [];
        foreach ($studentAverages as $sid => $avg) {
            if ($prevAvg !== null && $avg < $prevAvg) {
                $rank += $skip;
                $skip = 1;
            } else {
                $skip++;
            }
            $studentRankings[$sid] = $rank;
            $prevAvg = $avg;
        }
        $rankingsCount = count($studentRankings);

        // ------------------- Generate PDF for each student -------------------
        $processedCount = 0;
        foreach ($studentIds as $studentId) {
            $studentResults = $resultsByStudent[$studentId] ?? collect();
            if ($studentResults->isEmpty()) continue;

            $totalMarks = $studentResults->sum('score');
            $averageScore = $studentResults->avg('score');

            foreach ($studentResults as $result) {
                $result->courseRank = $subjectRanksCache[$result->course_id][$studentId] ?? '-';
                $result->grade = $this->calculateGrade($result->score, $marking_style);
            }

            // Aggregate points & division for marking style 3
            $aggregatePoints = 0;
            $division = null;
            if ($marking_style == 3) {
                $gradePoints = ['A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'F' => 5, 'ABS' => 6];
                foreach ($studentResults as $res) {
                    $aggregatePoints += $gradePoints[$res->grade] ?? 6;
                }
                $division = $this->calculateDivisionForStyle3($aggregatePoints, $studentResults->count());
            }

            $studentObj = new \stdClass();
            $studentObj->id = $studentId;
            $studentObj->first_name = $studentResults->first()->first_name;
            $studentObj->middle_name = $studentResults->first()->middle_name;
            $studentObj->last_name = $studentResults->first()->last_name;
            $studentObj->admission_number = $studentResults->first()->admission_number;
            $studentObj->group = $studentResults->first()->group;
            $studentObj->gender = $studentResults->first()->gender;
            $studentObj->image = $studentResults->first()->image;
            $studentObj->class_name = $allResults->first()->class_name;

            $studentRank = $studentRankings[$studentId] ?? 1;
            $overallGradeInfo = $this->calculateOverallGrade($averageScore, $marking_style, $division);

            // QR code with short data
            $qrBase64 = $this->generateQRCodeShort($studentObj, $date, $totalMarks, $averageScore, $studentRank, $marking_style, $division, $aggregatePoints, $schoolInfo);

            $html = view('Results.individual_student_report_pdf', [
                'results' => $studentResults,
                'studentId' => $studentObj,
                'totalScore' => $totalMarks,
                'averageScore' => $averageScore,
                'studentRank' => $studentRank,
                'rankings' => $rankingsCount,
                'division' => $division,
                'aggregatePoints' => $aggregatePoints,
                'marking_style' => $marking_style,
                'date' => $date,
                'qrPng' => $qrBase64,
                'overallGrade' => $overallGradeInfo['grade'],
                'gradeComment' => $overallGradeInfo['comment'],
                'schoolInfo' => $schoolInfo,
            ])->render();

            $pdf = Pdf::loadHTML($html);
            $pdf->setOption('isHtml5ParserEnabled', true);
            $pdf->setOption('isPhpEnabled', true);
            $pdf->setPaper('A4', 'portrait');

            $tempFile = $tempDir . '/' . $studentObj->admission_number . '.pdf';
            $pdf->save($tempFile);

            $processedCount++;
            ReportJob::where('job_id', $this->jobId)->update(['processed_students' => $processedCount]);

            // Free memory
            unset($studentResults, $html, $pdf);
            gc_collect_cycles();
        }

        // ------------------- Merge all PDFs -------------------
        $mergedPath = $this->mergePdfs($tempDir);

        // Final storage
        $finalDir = storage_path('app/reports');
        if (!File::exists($finalDir)) File::makeDirectory($finalDir, 0755, true);
        $className = $allResults->first()->class_name ?? 'students';
        $safeClassName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $className);
        $fileName = "{$safeClassName}_student_reports_" . Carbon::now()->format('Ymd_His') . ".pdf";
        $finalPath = $finalDir . '/' . $fileName;

        File::move($mergedPath, $finalPath);
        File::deleteDirectory($tempDir);

        ReportJob::where('job_id', $this->jobId)->update([
            'status' => 'completed',
            'file_path' => $finalPath,
            'file_name' => $fileName
        ]);
    }

    /**
     * Generate combined reports (e.g., multiple exam dates) using same chunk+merge approach
     */
    private function generateCombinedReports()
    {
        $schoolId = Hashids::decode($this->params['school'])[0];
        $classId = Hashids::decode($this->params['class'])[0];
        $reportId = Hashids::decode($this->params['report'])[0];

        $reports = generated_reports::find($reportId);
        if (!$reports) throw new \Exception('Report not found');

        $examDates = $reports->exam_dates;
        $combineOption = $reports->combine_option ?? 'individual';
        $storedClassId = $reports->class_id ?? $classId;

        // Get marking style
        $firstExamResult = Examination_result::query()
            ->where('class_id', $storedClassId)
            ->where('school_id', $schoolId)
            ->whereIn(DB::raw('DATE(exam_date)'), $examDates)
            ->first();
        $markingStyle = $firstExamResult ? ($firstExamResult->marking_style ?? 2) : 2;

        $schoolInfo = school::find($schoolId);
        $className = $reports->class_name ?? Grade::find($storedClassId)->class_name ?? 'N/A';

        $studentIds = Student::where('class_id', $classId)
            ->where('school_id', $schoolId)
            ->where('status', 1)
            ->orderBy('admission_number')
            ->pluck('id');

        $totalStudents = $studentIds->count();
        ReportJob::where('job_id', $this->jobId)->update(['total_students' => $totalStudents]);
        if ($totalStudents == 0) throw new \Exception('No students found');

        // Fetch all results once
        $allResults = Examination_result::query()
            ->join('students', 'students.id', '=', 'examination_results.student_id')
            ->join('grades', 'grades.id', '=', 'examination_results.class_id')
            ->join('subjects', 'subjects.id', '=', 'examination_results.course_id')
            ->join('examinations', 'examinations.id', '=', 'examination_results.exam_type_id')
            ->join('schools', 'schools.id', '=', 'examination_results.school_id')
            ->join('teachers', 'teachers.id', '=', 'examination_results.teacher_id')
            ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
            ->select(
                'examination_results.*', 'students.id as studentId', 'students.first_name', 'students.middle_name',
                'students.last_name', 'students.status', 'students.group', 'students.gender', 'students.image',
                'students.admission_number', 'subjects.id as subjectId', 'subjects.course_name', 'subjects.course_code',
                'grades.class_name', 'grades.class_code', 'examinations.exam_type', 'examinations.symbolic_abbr',
                'schools.school_name', 'schools.logo', 'schools.postal_address', 'schools.postal_name',
                'schools.school_email', 'schools.school_phone', 'schools.country',
                'users.first_name as teacher_first_name', 'users.last_name as teacher_last_name', 'teachers.id as teacher_id'
            )
            ->where('students.status', 1)
            ->where('examination_results.class_id', $storedClassId)
            ->where('examination_results.school_id', $schoolId)
            ->whereIn(DB::raw('DATE(exam_date)'), $examDates)
            ->get();

        if ($allResults->isEmpty()) throw new \Exception('No results found');

        $resultsByStudent = $allResults->groupBy('studentId');
        $examHeaders = $this->getExamHeadersForBulkCombined($allResults);
        $examSpecifications = $this->getExamSpecificationsForBulkCombined($allResults);
        $allStudentAverages = $this->calculateAllStudentAveragesForBulkCombined($resultsByStudent, $combineOption);

        // Temp folder
        $tempDir = storage_path('app/temp_pdfs/' . uniqid());
        if (!File::exists($tempDir)) File::makeDirectory($tempDir, 0777, true);

        $processedCount = 0;
        foreach ($studentIds as $studentId) {
            $student = Student::find($studentId);
            if (!$student) continue;
            $student->class_name = $className;
            $studentResults = $resultsByStudent[$student->id] ?? collect();
            if ($studentResults->isEmpty()) continue;

            $studentReportData = $this->generateStudentReportDataForBulkCombined(
                $student, $studentResults, $examHeaders, $combineOption, $markingStyle,
                $allStudentAverages, $reports, $schoolInfo
            );

            // QR code short version
            $qrBase64 = $this->generateQRForBulkCombinedShort($studentReportData, $reports, $schoolInfo);

            $html = view('generated_reports.compiled_report_bulk', [
                'students' => $studentReportData['students'],
                'finalData' => $studentReportData['finalData'],
                'studentGeneralAverage' => $studentReportData['studentGeneralAverage'],
                'totalScoreForStudent' => $studentReportData['totalScoreForStudent'],
                'generalPosition' => $studentReportData['generalPosition'],
                'totalStudents' => $studentReportData['totalStudents'],
                'subjectCount' => $studentReportData['subjectCount'],
                'examAverages' => $studentReportData['examAverages'],
                'examHeaders' => $examHeaders,
                'examSpecifications' => $examSpecifications,
                'schoolInfo' => $schoolInfo,
                'reports' => $reports,
                'qrPng' => $qrBase64,
                'year' => $this->params['year'],
                'class' => $this->params['class'],
                'school' => $this->params['school'],
                'report' => $this->params['report'],
                'markingStyle' => $markingStyle,
                'className' => $className,
                'currentStudent' => $processedCount + 1,
                'totalStudents' => $totalStudents,
            ])->render();

            $pdf = Pdf::loadHTML($html);
            $pdf->setOption('isHtml5ParserEnabled', true);
            $pdf->setOption('isPhpEnabled', true);
            $pdf->setPaper('A4', 'portrait');

            $tempFile = $tempDir . '/' . $student->admission_number . '.pdf';
            $pdf->save($tempFile);

            $processedCount++;
            ReportJob::where('job_id', $this->jobId)->update(['processed_students' => $processedCount]);

            unset($studentReportData, $html, $pdf);
            gc_collect_cycles();
        }

        // Merge all PDFs
        $mergedPath = $this->mergePdfs($tempDir);
        $finalDir = storage_path('app/reports');
        if (!File::exists($finalDir)) File::makeDirectory($finalDir, 0755, true);
        $safeClassName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $className);
        $safeReportTitle = preg_replace('/[^a-zA-Z0-9_-]/', '_', $reports->title);
        $fileName = "{$safeClassName}_{$safeReportTitle}_combined_report_" . Carbon::now()->format('Ymd_His') . ".pdf";
        $finalPath = $finalDir . '/' . $fileName;

        File::move($mergedPath, $finalPath);
        File::deleteDirectory($tempDir);

        ReportJob::where('job_id', $this->jobId)->update([
            'status' => 'completed',
            'file_path' => $finalPath,
            'file_name' => $fileName,
            'report_title' => $reports->title
        ]);
    }

    /**
     * Merge all PDFs in a folder using FPDI
     */
    private function mergePdfs($tempDir)
    {
        $pdfFiles = glob($tempDir . '/*.pdf');
        if (empty($pdfFiles)) {
            throw new \Exception('No PDF files to merge');
        }
        sort($pdfFiles);

        $pdf = new Fpdi();
        foreach ($pdfFiles as $file) {
            $pageCount = $pdf->setSourceFile($file);
            for ($i = 1; $i <= $pageCount; $i++) {
                $template = $pdf->importPage($i);
                $size = $pdf->getTemplateSize($template);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($template);
            }
        }

        $mergedDir = storage_path('app/temp_merged');
        if (!File::exists($mergedDir)) File::makeDirectory($mergedDir, 0777, true);
        $mergedFile = $mergedDir . '/' . uniqid('merged_') . '.pdf';
        $pdf->Output($mergedFile, 'F');
        return $mergedFile;
    }

    // ------------------- HELPER METHODS -------------------

    private function calculateGrade($score, $marking_style)
    {
        if ($score === null || $score == 0) return 'ABS';
        if ($marking_style == 1) {
            if ($score >= 41) return 'A';
            if ($score >= 31) return 'B';
            if ($score >= 21) return 'C';
            if ($score >= 11) return 'D';
            return 'E';
        } elseif ($marking_style == 2) {
            if ($score >= 81) return 'A';
            if ($score >= 61) return 'B';
            if ($score >= 41) return 'C';
            if ($score >= 21) return 'D';
            return 'E';
        } else {
            if ($score >= 80) return 'A';
            if ($score >= 60) return 'B';
            if ($score >= 40) return 'C';
            if ($score >= 20) return 'D';
            return 'F';
        }
    }

    private function calculateDivisionForStyle3($aggregatePoints, $totalSubjects)
    {
        if ($totalSubjects == 0) return '0';
        $avg = $aggregatePoints / $totalSubjects;
        if ($avg <= 1.5) return 'I';
        if ($avg <= 2.5) return 'II';
        if ($avg <= 3.5) return 'III';
        if ($avg <= 4.5) return 'IV';
        return '0';
    }

    private function calculateOverallGrade($averageScore, $marking_style, $division = null)
    {
        if ($marking_style == 3 && $division) {
            $grades = ['I' => 'EXCELLENT', 'II' => 'GOOD', 'III' => 'PASS', 'IV' => 'POOR', '0' => 'FAIL'];
            return ['grade' => $division, 'comment' => $grades[$division] ?? 'PASS'];
        }
        if ($marking_style == 1) {
            if ($averageScore >= 40.5) return ['grade' => 'A', 'comment' => 'EXCELLENT'];
            if ($averageScore >= 30.5) return ['grade' => 'B', 'comment' => 'GOOD'];
            if ($averageScore >= 20.5) return ['grade' => 'C', 'comment' => 'PASS'];
            if ($averageScore >= 10.5) return ['grade' => 'D', 'comment' => 'POOR'];
            return ['grade' => 'E', 'comment' => 'FAIL'];
        }
        if ($averageScore >= 80.5) return ['grade' => 'A', 'comment' => 'EXCELLENT'];
        if ($averageScore >= 60.5) return ['grade' => 'B', 'comment' => 'GOOD'];
        if ($averageScore >= 40.5) return ['grade' => 'C', 'comment' => 'PASS'];
        if ($averageScore >= 20.5) return ['grade' => 'D', 'comment' => 'POOR'];
        return ['grade' => 'E', 'comment' => 'FAIL'];
    }

    /**
     * QR code with minimal data to avoid DomPDF rendering issues
     */
    private function generateQRCodeShort($student, $date, $totalScore, $averageScore, $studentRank, $marking_style, $division, $aggregatePoints, $schoolInfo)
    {
        try {
            $data = [
                'adm' => $student->admission_number,
                'dt' => Carbon::parse($date)->toDateString(),
                'avg' => round($averageScore, 1),
                'rk' => $studentRank,
            ];
            if ($marking_style == 3 && $division) {
                $data['div'] = $division;
                $data['pts'] = $aggregatePoints;
            }
            $data['sig'] = hash_hmac('sha256', json_encode($data), config('app.key'));
            $encrypted = Crypt::encryptString(json_encode($data));
            $url = route('report.verify', ['payload' => $encrypted]);

            $result = Builder::create()
                ->writer(new PngWriter())
                ->data($url)
                ->size(100)
                ->margin(4)
                ->build();
            return base64_encode($result->getString());
        } catch (\Exception $e) {
            Log::error('QR short generation failed: ' . $e->getMessage());
            return '';
        }
    }

    private function generateQRForBulkCombinedShort($studentData, $reports, $schoolInfo)
    {
        try {
            $student = $studentData['students'];
            $data = [
                'adm' => $student->admission_number,
                'rep' => $reports->id,
                'avg' => round($studentData['studentGeneralAverage'] ?? 0, 1),
                'rk' => $studentData['generalPosition'] ?? 0,
            ];
            if (($reports->marking_style ?? 2) == 3 && isset($studentData['division'])) {
                $data['div'] = $studentData['division'];
                $data['pts'] = $studentData['aggregatePoints'] ?? 0;
            }
            $data['sig'] = hash_hmac('sha256', json_encode($data), config('app.key'));
            $encrypted = Crypt::encryptString(json_encode($data));
            $url = route('report.verify', ['payload' => $encrypted]);

            $result = Builder::create()
                ->writer(new PngWriter())
                ->data($url)
                ->size(100)
                ->margin(4)
                ->build();
            return base64_encode($result->getString());
        } catch (\Exception $e) {
            Log::error('QR combined short generation failed: ' . $e->getMessage());
            return '';
        }
    }

    // ---------- Methods for combined report pre-computation (unchanged logic) ----------
    private function getExamHeadersForBulkCombined($results)
    {
        return $results->map(function ($item) {
            return [
                'abbr' => $item->symbolic_abbr,
                'date' => $item->exam_date,
                'display' => $item->symbolic_abbr . ' ' . Carbon::parse($item->exam_date)->format('d M Y')
            ];
        })->unique(function ($item) {
            return $item['abbr'] . $item['date'];
        })->values()->toArray();
    }

    private function getExamSpecificationsForBulkCombined($results)
    {
        return $results->map(function ($item) {
            return (object)[
                'abbr' => $item->symbolic_abbr ?? 'N/A',
                'full_name' => $item->exam_type ?? 'N/A',
                'date' => $item->exam_date
            ];
        })->unique(function ($item) {
            return $item->abbr . $item->full_name;
        })->values()->keyBy('abbr');
    }

    private function calculateAllStudentAveragesForBulkCombined($resultsByStudent, $combineOption)
    {
        $allStudentAverages = [];
        foreach ($resultsByStudent as $stdId => $studentResults) {
            $groupedBySubject = $studentResults->groupBy('subjectId');
            $studentTotalAvg = 0;
            $studentSubjectCount = 0;
            foreach ($groupedBySubject as $subjectResults) {
                if ($combineOption == 'individual') {
                    $subjectAvg = $subjectResults->avg('score') ?? 0;
                } elseif ($combineOption == 'sum') {
                    $subjectAvg = $subjectResults->count() > 0 ? $subjectResults->sum('score') / $subjectResults->count() : 0;
                } else {
                    $subjectAvg = $subjectResults->avg('score') ?? 0;
                }
                $studentTotalAvg += round($subjectAvg, 2);
                $studentSubjectCount++;
            }
            $allStudentAverages[$stdId] = $studentSubjectCount > 0 ? round($studentTotalAvg / $studentSubjectCount, 2) : 0;
        }
        arsort($allStudentAverages);
        $generalRanked = [];
        $rank = 1;
        $prev = null;
        $skip = 0;
        foreach ($allStudentAverages as $std_id => $avg) {
            if ($prev !== null && $avg < $prev) {
                $rank += $skip;
                $skip = 1;
            } else {
                $skip++;
            }
            $generalRanked[$std_id] = $rank;
            $prev = $avg;
        }
        return ['averages' => $allStudentAverages, 'ranks' => $generalRanked, 'total_students' => count($generalRanked)];
    }

    private function generateStudentReportDataForBulkCombined($student, $studentResults, $examHeaders, $combineOption, $markingStyle, $allStudentAverages, $reports, $schoolInfo)
    {
        $classResultsGrouped = $studentResults->groupBy('subjectId');
        $finalData = [];
        $studentTotalMarks = 0;
        $subjectCount = 0;

        foreach ($classResultsGrouped as $subjectId => $subjectResults) {
            $first = $subjectResults->first();
            $subjectName = $first->course_name;
            $subjectCode = $first->course_code;
            $teacher = $first->teacher_first_name ? $first->teacher_first_name . '. ' . substr($first->teacher_last_name, 0, 1) : 'N/A';
            $examScores = [];
            $total = 0;
            $average = 0;

            if ($combineOption == 'individual') {
                foreach ($examHeaders as $exam) {
                    $score = $subjectResults->where('symbolic_abbr', $exam['abbr'])->where('exam_date', $exam['date'])->first()->score ?? null;
                    $examScores[$exam['abbr'] . '_' . $exam['date']] = $score;
                }
                $total = collect($examScores)->filter()->sum();
                $average = collect($examScores)->filter()->avg() ?? 0;
            } elseif ($combineOption == 'sum') {
                foreach ($examHeaders as $exam) {
                    $score = $subjectResults->where('symbolic_abbr', $exam['abbr'])->where('exam_date', $exam['date'])->first()->score ?? null;
                    $examScores[$exam['abbr'] . '_' . $exam['date']] = $score;
                }
                $total = collect($examScores)->sum();
                $average = count(array_filter($examScores)) > 0 ? $total / count(array_filter($examScores)) : 0;
            } else {
                foreach ($examHeaders as $exam) {
                    $score = $subjectResults->where('symbolic_abbr', $exam['abbr'])->where('exam_date', $exam['date'])->first()->score ?? null;
                    $examScores[$exam['abbr'] . '_' . $exam['date']] = $score;
                }
                $filtered = collect($examScores)->filter();
                $average = $filtered->count() > 0 ? $filtered->avg() : 0;
                $total = $average;
            }

            $subjectPosition = $this->calculateSubjectPositionForBulkCombined($subjectId, $student->id, $combineOption, $reports->exam_dates, $reports->class_id, $reports->school_id);
            $finalData[] = [
                'subjectName' => $subjectName,
                'subjectCode' => $subjectCode,
                'teacher' => $teacher,
                'examScores' => $examScores,
                'total' => round($total, 2),
                'average' => round($average, 2),
                'position' => $subjectPosition,
                'grade' => $this->calculateGradeForMarkingStyle($average, $markingStyle),
            ];
            $studentTotalMarks += $average;
            $subjectCount++;
        }

        $examAverages = [];
        foreach ($examHeaders as $exam) {
            $totalPerExam = 0;
            $countPerExam = 0;
            $abbr = $exam['abbr'];
            $date = $exam['date'];
            foreach ($finalData as $subject) {
                $score = $subject['examScores'][$abbr . '_' . $date] ?? null;
                if (is_numeric($score)) {
                    $totalPerExam += $score;
                    $countPerExam++;
                }
            }
            $examAverages[$abbr . '_' . $date] = $countPerExam > 0 ? round($totalPerExam / $countPerExam, 2) : 0;
        }

        $studentGeneralAverage = $subjectCount > 0 ? round($studentTotalMarks / $subjectCount, 2) : 0;
        $totalScoreForStudent = round($studentTotalMarks, 2);
        $generalPosition = $allStudentAverages['ranks'][$student->id] ?? '-';
        $totalStudents = $allStudentAverages['total_students'];

        $aggregatePoints = null;
        $division = null;
        if ($markingStyle == 3) {
            $gradePoints = ['A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'F' => 5];
            $totalPoints = 0;
            foreach ($finalData as $subject) {
                $totalPoints += $gradePoints[$subject['grade']] ?? 5;
            }
            $aggregatePoints = $totalPoints;
            $division = $this->calculateDivisionForStyle3($aggregatePoints, $subjectCount);
        }

        $student->class_name = $reports->class_name ?? ($student->class_name ?? 'N/A');
        return [
            'students' => $student,
            'finalData' => $finalData,
            'studentGeneralAverage' => $studentGeneralAverage,
            'totalScoreForStudent' => $totalScoreForStudent,
            'generalPosition' => $generalPosition,
            'totalStudents' => $totalStudents,
            'subjectCount' => $subjectCount,
            'examAverages' => $examAverages,
            'division' => $division,
            'aggregatePoints' => $aggregatePoints,
        ];
    }

    private function calculateSubjectPositionForBulkCombined($subjectId, $studentId, $combineOption, $examDates, $storedClassId, $schoolId)
    {
        $subjectResultsAll = DB::table('examination_results')
            ->where('course_id', $subjectId)
            ->where('class_id', $storedClassId)
            ->where('school_id', $schoolId)
            ->whereIn(DB::raw('DATE(exam_date)'), $examDates)
            ->get()
            ->groupBy('student_id');

        $allStudentSubjectAverages = [];
        foreach ($subjectResultsAll as $stdId => $results) {
            if ($combineOption == 'individual') {
                $avg = $results->avg('score') ?? 0;
            } elseif ($combineOption == 'sum') {
                $avg = $results->count() > 0 ? $results->sum('score') / $results->count() : 0;
            } else {
                $avg = $results->avg('score') ?? 0;
            }
            $allStudentSubjectAverages[$stdId] = round($avg, 2);
        }
        arsort($allStudentSubjectAverages);
        $position = 1;
        $prev = null;
        $skip = 0;
        $positions = [];
        foreach ($allStudentSubjectAverages as $std_id => $avg) {
            if ($prev !== null && $avg < $prev) {
                $position += $skip;
                $skip = 1;
            } else {
                $skip++;
            }
            $positions[$std_id] = $position;
            $prev = $avg;
        }
        return $positions[$studentId] ?? '-';
    }

    private function calculateGradeForMarkingStyle($average, $markingStyle)
    {
        if ($markingStyle == 1) {
            if ($average >= 40.5) return 'A';
            if ($average >= 30.5) return 'B';
            if ($average >= 20.5) return 'C';
            if ($average >= 10.5) return 'D';
            return 'E';
        } elseif ($markingStyle == 2) {
            if ($average >= 80.5) return 'A';
            if ($average >= 60.5) return 'B';
            if ($average >= 40.5) return 'C';
            if ($average >= 20.5) return 'D';
            return 'E';
        } else {
            if ($average >= 80) return 'A';
            if ($average >= 60) return 'B';
            if ($average >= 40) return 'C';
            if ($average >= 20) return 'D';
            return 'F';
        }
    }
}
