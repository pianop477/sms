<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Results Excel Export</title>
</head>
<body>
    <!-- STUDENT-WISE PERFORMANCE -->
    <table>
            <tr><th>THE UNITED REPUBLIC OF TANZANIA</th></tr>
            <tr><th>THE PRESIDENT'S OFFICE - RALG</th></tr>
            <tr><th>{{strtoupper(Auth::user()->school->school_name)}}</th></tr>
    </table>
    <table>
            <tr><th>{{strtoupper($results->first()->class_name)}} {{strtoupper($results->first()->exam_type)}} Results - {{\Carbon\Carbon::parse($date)->format('d.m.Y')}}</th></tr>
            <tr><th style="font-weight:normal">TERM: {{strtoupper($results->first()->Exam_term)}}</th></tr>
            <tr><th style="font-weight:normal">NUMBER OF CANDIDATES: {{$totalUniqueStudents}}</th></tr>
            <tr><th style="font-weight:normal">CLASS AVERAGE: <strong>{{number_format($sumOfCourseAverages, 4)}}</strong>
                @if($results->first()->marking_style == 1)
                    @if ($generalClassAvg >= 40.5)
                        <span style="background:rgb(117, 244, 48); padding:2px 10px; ">GRADE A (EXCELLENT)</span>
                    @elseif ($generalClassAvg >= 30.5)
                        <span style="background:rgb(153, 250, 237); padding:2px 10px;">GRADE B (GOOD)</span>
                    @elseif ($generalClassAvg >= 20.5)
                        <span style="background:rgb(237, 220, 113); padding:2px 10px;">GRADE C (PASS)</span>
                    @elseif ($generalClassAvg >= 10.5)
                        <span style="background:rgb(182, 176, 176); padding:2px 10px;"> GRADE D (POOR)</span>
                    @elseif($generalClassAvg <= 10.4)
                        <span style="background:rgb(235, 75, 75); padding:2px 10px;">GRADE E (FAIL)</span>
                    @endif
                    @else
                        @if ($generalClassAvg >= 80.5)
                            <span style="background:rgb(117, 244, 48); padding:2px 10px;">GRADE A (EXCELLENT)</span>
                        @elseif ($generalClassAvg >= 60.5)
                            <span style="background:rgb(153, 250, 237); padding:2px 10px;">GRADE B (GOOD)</span>
                        @elseif ($generalClassAvg >= 40.5)
                            <span style="background:rgb(237, 220, 113); padding:2px 10px">GRADE C (PASS)</span>
                        @elseif ($generalClassAvg >= 20.5)
                            <span style="background:rgb(182, 176, 176); padding:2px 10px;"> GRADE D (POOR)</span>
                        @elseif($generalClassAvg <= 20.4)
                            <span style="background:rgb(235, 75, 75); padding:2px 10px;">GRADE E (FAIL)</span>
                        @endif
                    @endif
            </th></tr>
            <tr><th style="font-weight:normal">AVERAGE OF: <strong>{{number_format($generalClassAvg, 2)}}</strong></th></tr>
    </table>
    <table class="table" style="text-align:center; width:60%">
        <tr style="background: rgb(187, 163, 56); color:black">
            <th colspan="6">OVERALL GRADE SUMMARY</th>
        </tr>
        <tr>
            <td>Gender</td>
            <td>A</td>
            <td>B</td>
            <td>C</td>
            <td>D</td>
            <td>E</td>
        </tr>
        <tr>
            <td>Girls</td>
            <td>{{$totalFemaleGrades['A']}}</td>
            <td>{{$totalFemaleGrades['B']}}</td>
            <td>{{$totalFemaleGrades['C']}}</td>
            <td>{{$totalFemaleGrades['D']}}</td>
            <td>{{$totalFemaleGrades['E']}}</td>
        </tr>
        <tr>
            <td>Boys</td>
            <td>{{$totalMaleGrades['A']}}</td>
            <td>{{$totalMaleGrades['B']}}</td>
            <td>{{$totalMaleGrades['C']}}</td>
            <td>{{$totalMaleGrades['D']}}</td>
            <td>{{$totalMaleGrades['E']}}</td>
        </tr>
        <tr>
            <td>Total</td>
            <td>{{$totalFemaleGrades['A'] + $totalMaleGrades['A']}}</td>
            <td>{{$totalFemaleGrades['B'] +$totalMaleGrades['B']}}</td>
            <td>{{$totalFemaleGrades['C'] +$totalMaleGrades['C']}}</td>
            <td>{{$totalFemaleGrades['D'] +$totalMaleGrades['D']}}</td>
            <td>{{$totalFemaleGrades['E'] +$totalMaleGrades['E']}}</td>
        </tr>
    </table>
    <table>
        <tr style="background: rgb(187, 163, 56); color:black">
            <th colspan="6">STUDENTS PERFORMANCE</th>
        </tr>
        <thead>
            <tr>
                <th>Adm.No.</th>
                <th>Sex</th>
                <th>Student Name</th>
                @foreach ($results->groupBy('course_id')->keys() as $courseId)
                    <th>{{ strtoupper($results->firstWhere('course_id', $courseId)->course_code) }}</th>
                @endforeach
                <th>Total</th>
                <th>Avg</th>
                <th>Grade</th>
                <th>Rank</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sortedStudentsResults as $studentResult)
            <tr>
                <td>{{ strtoupper($studentResult['admission_number']) }}</td>
                <td>{{ strtoupper($studentResult['gender'][0]) }}</td>
                <td>{{ ucwords(strtolower($studentResult['student_name'])) }}</td>
                @foreach ($studentResult['courses'] as $course)
                    <td>{{ $course['score'] ?? 'X' }}</td>
                @endforeach
                <td>{{ $studentResult['total_marks'] }}</td>
                <td>{{ number_format($studentResult['average'], 2) }}</td>
                <td>{{ $studentResult['grade'] === 'ABS' ? 'ABS' : $studentResult['grade'] }}</td>
                <td>{{ $studentResult['grade'] === 'ABS' ? 'X' : $studentResult['position'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- SUBJECT-WISE RANKINGS -->
    <table>
        <tr>
            <th colspan="5">SUBJECTWISE RANKINGS</th>
        </tr>
        <tr>
            <th>Subject Name</th>
            <th>Code</th>
            <th>Average</th>
            <th>Position</th>
            <th>Grade</th>
        </tr>
        @foreach ($sortedCourses as $course)
        <tr>
            <td>{{ ucwords(strtolower($course['course_name'] ))}}</td>
            <td>{{ strtoupper($course['course_code']) }}</td>
            <td>{{ number_format($course['average_score'], 2) }}</td>
            <td>{{ $course['position'] }}</td>
            <td>{{ strtoupper($course['grade'] )}}</td>
        </tr>
        @endforeach
    </table>
</body>
</html>
