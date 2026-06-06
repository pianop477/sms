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
use Hashids;
use Crypt;

class GenerateBulkStudentReportsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public $timeout = 3600; // 1 hour
    public $tries = 1;

    protected $jobId;
    protected $school;
    protected $year;
    protected $class;
    protected $examType;
    protected $month;
    protected $date;
    protected $userId;

    public function __construct($jobId, $school, $year, $class, $examType, $month, $date, $userId)
    {
        $this->jobId = $jobId;
        $this->school = $school;
        $this->year = $year;
        $this->class = $class;
        $this->examType = $examType;
        $this->month = $month;
        $this->date = $date;
        $this->userId = $userId;
    }

    public function handle()
    {
        try {
            // Update job status to processing
            ReportJob::where('job_id', $this->jobId)->update([
                'status' => 'processing',
                'processed_students' => 0
            ]);

            // Decode IDs
            $school_id = Hashids::decode($this->school);
            $class_id = Hashids::decode($this->class);
            $exam_id = Hashids::decode($this->examType);

            $schools = school::find($school_id[0]);

            // Get marking style
            $marking_style = Examination_result::query()
                ->where('school_id', $schools->id)
                ->where('class_id', $class_id[0])
                ->where('exam_type_id', $exam_id[0])
                ->whereDate('exam_date', $this->date)
                ->value('marking_style') ?? 1;

            // Get student IDs
            $studentIds = Student::where('class_id', $class_id[0])
                ->where('school_id', $schools->id)
                ->where('status', 1)
                ->orderBy('admission_number')
                ->pluck('id');

            $totalStudents = $studentIds->count();

            ReportJob::where('job_id', $this->jobId)->update([
                'total_students' => $totalStudents
            ]);

            // Get all results
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
                    'students.first_name',
                    'students.middle_name',
                    'students.last_name',
                    'students.admission_number',
                    'students.group',
                    'students.image',
                    'students.gender',
                    'students.id as student_id',
                    'subjects.course_name',
                    'subjects.course_code',
                    'grades.class_name',
                    'examinations.exam_type',
                    'examination_results.Exam_term',
                    'teacher_users.first_name as teacher_first_name',
                    'teacher_users.last_name as teacher_last_name',
                    'schools.school_name',
                    'schools.postal_address',
                    'schools.postal_name',
                    'schools.logo',
                    'schools.country',
                    'schools.school_phone',
                    'schools.school_email'
                )
                ->where('examination_results.school_id', $schools->id)
                ->where('examination_results.class_id', $class_id[0])
                ->where('examination_results.exam_type_id', $exam_id[0])
                ->where('students.status', 1)
                ->whereDate('examination_results.exam_date', $this->date)
                ->get();

            $resultsByStudent = $allResults->groupBy('student_id');

            // Pre-calculate subject scores for ranking
            $subjectScoresCache = [];
            foreach ($allResults->groupBy('course_id') as $courseId => $courseResults) {
                $subjectScoresCache[$courseId] = $courseResults->pluck('score', 'student_id');
            }

            // Pre-calculate ranks
            $subjectRanksCache = [];
            foreach ($subjectScoresCache as $courseId => $scores) {
                $sortedScores = $scores->sortDesc();
                $rank = 1;
                $previousScore = null;
                $sameRankCount = 0;
                $ranks = [];

                foreach ($sortedScores as $studentId => $score) {
                    if ($previousScore !== null && $score < $previousScore) {
                        $rank += $sameRankCount;
                        $sameRankCount = 1;
                    } else {
                        $sameRankCount++;
                    }
                    $ranks[$studentId] = $rank;
                    $previousScore = $score;
                }
                $subjectRanksCache[$courseId] = $ranks;
            }

            // Calculate student averages
            $studentAverages = [];
            foreach ($resultsByStudent as $studentId => $studentResults) {
                $studentAverages[$studentId] = $studentResults->avg('score');
            }

            // Calculate class rankings
            arsort($studentAverages);
            $rank = 1;
            $studentRankings = [];
            $previousAvg = null;
            $sameRankCount = 0;

            foreach ($studentAverages as $studentId => $avg) {
                if ($previousAvg !== null && $avg < $previousAvg) {
                    $rank += $sameRankCount;
                    $sameRankCount = 1;
                } else {
                    $sameRankCount++;
                }
                $studentRankings[$studentId] = $rank;
                $previousAvg = $avg;
            }
            $rankings = collect($studentRankings);

            // Generate HTML for ALL students (one big PDF)
            $allHtmlPages = [];
            $processedCount = 0;
            $schoolInfo = $schools;

            foreach ($studentIds as $studentId) {
                $studentResults = $resultsByStudent[$studentId] ?? collect();

                if ($studentResults->isEmpty()) {
                    continue;
                }

                $totalMarks = $studentResults->sum('score');
                $averageScore = $studentResults->avg('score');

                foreach ($studentResults as $result) {
                    $ranksForSubject = $subjectRanksCache[$result->course_id] ?? [];
                    $result->courseRank = $ranksForSubject[$studentId] ?? '-';
                    $grade = $this->calculateGrade($result->score, $marking_style);
                    $result->grade = $grade;
                    $result->remarks = $this->getRemarksForGrade($grade, $marking_style, $result->score);
                }

                $aggregatePoints = 0;
                $division = null;
                if ($marking_style == 3) {
                    $gradePoints = ['A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'F' => 5, 'ABS' => 6];
                    foreach ($studentResults as $result) {
                        $courseGrade = $this->calculateGrade($result->score, $marking_style);
                        $aggregatePoints += $gradePoints[$courseGrade] ?? 6;
                    }
                    $division = $this->calculateDivisionForStyle3($aggregatePoints, $studentResults->count());
                }

                $studentObject = new \stdClass();
                $studentObject->id = $studentId;
                $studentObject->first_name = $studentResults->first()->first_name;
                $studentObject->middle_name = $studentResults->first()->middle_name;
                $studentObject->last_name = $studentResults->first()->last_name;
                $studentObject->admission_number = $studentResults->first()->admission_number;
                $studentObject->group = $studentResults->first()->group;
                $studentObject->gender = $studentResults->first()->gender;
                $studentObject->image = $studentResults->first()->image;
                $studentObject->class_name = $allResults->first()->class_name;

                $studentRank = $studentRankings[$studentId] ?? 1;
                $overallGradeInfo = $this->calculateOverallGrade($averageScore, $marking_style, $division);

                $qrPng = $this->generateQRCodeForStudent(
                    $studentObject,
                    $this->date,
                    $totalMarks,
                    $averageScore,
                    $studentRank,
                    $rankings->count(),
                    $marking_style,
                    $division,
                    $aggregatePoints,
                    $schoolInfo
                );

                $html = view('Results.individual_student_report_pdf', [
                    'results' => $studentResults,
                    'studentId' => $studentObject,
                    'totalScore' => $totalMarks,
                    'averageScore' => $averageScore,
                    'studentRank' => $studentRank,
                    'rankings' => $rankings,
                    'division' => $division,
                    'aggregatePoints' => $aggregatePoints,
                    'marking_style' => $marking_style,
                    'date' => $this->date,
                    'qrPng' => $qrPng,
                    'overallGrade' => $overallGradeInfo['grade'],
                    'gradeComment' => $overallGradeInfo['comment'],
                    'schoolInfo' => $schoolInfo,
                ])->render();

                $allHtmlPages[] = $html;
                $processedCount++;

                // Update progress
                ReportJob::where('job_id', $this->jobId)->update([
                    'processed_students' => $processedCount
                ]);

                unset($studentResults);
            }

            // Generate single PDF
            $fullHtml = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body>' .
                        implode('<div style="page-break-after: always;"></div>', $allHtmlPages) .
                        '</body></html>';

            $pdf = Pdf::loadHTML($fullHtml);
            $pdf->setOption('isHtml5ParserEnabled', true);
            $pdf->setOption('isPhpEnabled', true);
            $pdf->setPaper('A4', 'portrait');

            // Save PDF
            $folderPath = storage_path('app/reports');
            if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0755, true);
            }

            $className = $allResults->first()->class_name ?? 'students';
            $safeClassName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $className);
            $fileName = "{$safeClassName}_student_reports_" . Carbon::now()->format('Ymd_His') . ".pdf";
            $filePath = $folderPath . '/' . $fileName;

            $pdf->save($filePath);

            // Update job as completed
            ReportJob::where('job_id', $this->jobId)->update([
                'status' => 'completed',
                'file_path' => $filePath,
                'file_name' => $fileName
            ]);

        } catch (\Exception $e) {
            Log::error('Job failed: ' . $e->getMessage());

            ReportJob::where('job_id', $this->jobId)->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);
        }
    }

    private function calculateGrade($score, $marking_style)
    {
        if ($score === null || $score === 0) return 'ABS';

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

    private function getRemarksForGrade($grade, $marking_style, $score = null)
    {
        if ($score === null || $score === 0) return 'ABSENT';
        $remarks = ['A' => 'Excellent', 'B' => 'Good', 'C' => 'Pass', 'D' => 'Poor', 'E' => 'Fail', 'F' => 'Fail'];
        return $remarks[$grade] ?? '-';
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

    private function generateQRCodeForStudent($student, $date, $totalScore, $averageScore, $studentRank, $totalStudents, $marking_style, $division = null, $aggregatePoints = null, $schoolInfo = null)
    {
        try {
            $verificationData = [
                'student_name' => trim($student->first_name . ' ' . $student->middle_name . ' ' . $student->last_name),
                'admission_number' => $student->admission_number,
                'class' => $student->class_name ?? 'N/A',
                'report_type' => 'Academic Progress Report',
                'term' => 'I',
                'school' => $schoolInfo ? $schoolInfo->school_name : 'N/A',
                'report_date' => Carbon::parse($date)->format('Y-m-d'),
                'report_id' => sha1($student->id . $date),
                'issued_at' => now()->timestamp,
                'total_score' => $totalScore,
                'average_score' => round($averageScore, 2),
                'student_rank' => $studentRank,
                'total_students' => $totalStudents,
                'marking_style' => $marking_style,
            ];
            if ($marking_style == 3 && $division) {
                $verificationData['division'] = $division;
                $verificationData['aggregate_points'] = $aggregatePoints;
            }
            $verificationData['signature'] = hash_hmac('sha256', json_encode($verificationData), config('app.key'));
            $encryptedPayload = Crypt::encryptString(json_encode($verificationData));
            $verificationUrl = route('report.verify', ['payload' => $encryptedPayload]);
            $result = Builder::create()->writer(new PngWriter())->data($verificationUrl)->size(120)->margin(4)->build();
            return base64_encode($result->getString());
        } catch (\Exception $e) {
            return '';
        }
    }
}
