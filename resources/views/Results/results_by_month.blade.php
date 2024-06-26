@extends('SRTDashboard.frame')

@section('content')
<div class="col-md-12">
    <div class="card mt-5">
        <div class="card-body">
            <h4 class="text-uppercase text-center">United Republic of Tanzania - President Office tamisemi</h4>
            <h4 class="text-uppercase text-center">{{$school->first()->school_name}}</h4>
            <h5 class="header-title text-uppercase text-center">{{$results->first()->exam_type}} Results - {{$month}}, {{$year}}</h5>
            <h5 class="header-title text-uppercase text-center">Class: {{$results->first()->class_name}}</h5>
            <div class="row">
                <div class="col-6">
                    <p class="">{{\Carbon\Carbon::parse($results->first()->exam_date)->format('d/m/Y')}}</p>
                </div>
                <div class="col-6">
                    <p class="float-right">Term: <span class="text-uppercase">{{$results->first()->Exam_term}}</span></p>
                </div>
            </div>
            <ul class="d-flex float-right">
                <li class="mr-3">
                    <a href="{{ url()->previous() }}" class="no-print"><i class="fas fa-arrow-circle-left text-secondary" style="font-size: 1.3rem;"></i></a>
                </li>
                <li>
                    <a href="#" onclick="scrollToTopAndPrint(); return false;" class="no-print">
                        <i class="ti-printer text-secondary" style="font-size: 1.3rem;"></i>
                    </a>
                </li>
            </ul>
            <hr>
            <!-- Summary of students by gender -->
            <div class="row">
                <div class="col-4">
                    <table class="table table-bordered text-center">
                        <tr>
                            <th colspan="2" class="text-center text-capitalize" style="background:lightcyan">Number of students</th>
                        </tr>
                        <tr>
                            <th>Gender</th>
                            <th>No of students</th>
                        </tr>
                        <tr>
                            <td>Boys</td>
                            <td>{{ $totalMaleStudents }}</td>
                        </tr>
                        <tr>
                            <td>Girls</td>
                            <td>{{ $totalFemaleStudents }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-8">
                    <table class="table table-bordered">
                        <tr>
                            <th colspan="4" class="text-uppercase text-center" style="background: lightcyan">Subjectwise average Ranking</th>
                        </tr>
                        <tr class="text-center">
                            <th>Course Name</th>
                            <th>Average</th>
                            <th>Grade</th>
                            <th>Rank</th>
                        </tr>
                        @foreach ($sortedCourses as $course)
                        <tr class="text-center">
                            <td class="text-uppercase">{{ $course['course_name'] }}</td>
                            <td>{{ number_format($course['average_score'], 2) }}</td>
                            @if ($course['grade'] == 'A')
                                <td class="alert alert-success">{{ $course['grade'] }}</td>
                            @elseif ($course['grade'] == 'B')
                                <td class="alert alert-primary">{{ $course['grade'] }}</td>
                            @elseif ($course['grade'] == 'C')
                                <td class="alert alert-warning">{{ $course['grade'] }}</td>
                            @elseif ($course['grade'] == 'D')
                                <td class="alert alert-secondary">{{ $course['grade'] }}</td>
                            @else
                                <td class="alert alert-danger">{{ $course['grade'] }}</td>
                            @endif
                            <td>{{ $course['position'] }}</td>
                        </tr>
                    @endforeach
                    </table>
                </div>
            </div>
            <hr>

            <!-- Evaluation score table -->
            <div class="row">
                <div class="col-8">
                    <table class="table table-bordered text-center">
                        <tr>
                            <th colspan="6" class="text-center text-uppercase" style="background:lightcyan;">Subjectwise Evaluation Summary</th>
                        </tr>
                        <tr>
                            <th>Course</th>
                            <th>A</th>
                            <th>B</th>
                            <th>C</th>
                            <th>D</th>
                            <th>E</th>
                        </tr>
                        @foreach ( $evaluationScores as $courseId => $grades )
                        <tr>
                            <td class="text-uppercase">{{ $results->firstWhere('course_id', $courseId)->course_code }}</td>
                            <td>{{ $grades['A'] }}</td>
                            <td>{{ $grades['B'] }}</td>
                            <td>{{ $grades['C'] }}</td>
                            <td>{{ $grades['D'] }}</td>
                            <td>{{ $grades['E'] }}</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
                <div class="col-4">
                    <table class="table table-bordered">
                        <tr>
                            <th colspan="2" class="text-uppercase text-center" style="background: lightgreen">Class Average</th>
                        </tr>
                        <tr>
                            <td class="text-center">
                                <h4>{{ number_format($totalAverageScore, 4) }}</h4>
                                @if ($totalAverageScore >= 41 && $totalAverageScore <=  50)
                                    <span style="font-size:1.5rem" class="text-success">A</span>
                                @elseif ($totalAverageScore >= 31 && $totalAverageScore >=  40)
                                    <span style="font-size:1.5rem" class="text-primary">B</span>
                                @elseif ($totalAverageScore >= 21 && $totalAverageScore >=  30)
                                    <span style="font-size:1.5rem" class="text-warning">C</span>
                                @elseif ($totalAverageScore >= 11 && $totalAverageScore >=  20)
                                    <span style="font-size:1.5rem" class="text-secondary">D</span>
                                @else
                                    <span style="font-size:1.5rem" class="text-danger">E</span>
                                @endif
                            </td>
                            <td>
                                <h4 class="text-center">{{number_format($sumOfCourseAverages, 4)}}</h4>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <hr>
            <h4 class="text-uppercase text-center">Student Results</h4>
            <table class="table table-bordered table-hover table-responsive-md">
                <thead>
                    <tr class="text-uppercase text-center">
                        <th>No</th>
                        <th>Student Name</th>
                        <th>Gender</th>
                        <th>Stream</th>
                        @foreach ($results->groupBy('course_id')->keys() as $courseId)
                            <th>{{ $results->firstWhere('course_id', $courseId)->course_code }}</th>
                        @endforeach
                        <th>Total Marks</th>
                        <th>Average</th>
                        <th>Grade</th>
                        <th>Position</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sortedStudentsResults as $index => $studentResult)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="text-uppercase">{{ $studentResult['student_name'] }}</td>
                            <td class="text-uppercase text-center">{{ $studentResult['gender'][0] }}</td>
                            <td class="text-uppercase text-center">{{$studentResult['group']}}</td>
                            @foreach ($studentResult['courses'] as $course)
                                <td class="text-uppercase text-center">{{ $course['score'] }}</td>
                            @endforeach
                            <td class="text-center">{{ $studentResult['total_marks'] }}</td>
                            <td class="text-center">{{ number_format($studentResult['average'], 3) }}</td>
                            <td class="text-uppercase text-center">{{ $studentResult['grade'] }}</td>
                            <td class="text-center">{{ $studentResult['position'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Footer -->
        <div class="footer mt-5" style="position: fixed; bottom: 0; width: 100%; border-top: 1px solid #ddd; padding-top: 10px;">
            <div class="row">
                <div class="col-8">
                    <p class="">Printed by: {{ Auth::user()->email}}</p>
                </div>
                <div class="col-4">
                    <p class="">{{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
                </div>
            </div>
            <script type="text/php">
                if ( isset($pdf) ) {
                    $pdf->page_text(270, 770, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, array(0,0,0));
                }
            </script>
        </div>

@endsection

@section('styles')
<style>
    @media print {
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        @page {
            margin: 20mm;
        }
        thead {
            display: table-header-group;
        }
        tbody {
            display: table-row-group;
        }
    }
</style>
@endsection



