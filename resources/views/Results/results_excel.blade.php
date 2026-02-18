<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Results Excel Export</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .header-section {
            text-align: center;
            margin-bottom: 20px;
        }
        .division-i {
            background-color: #75f430;
        }
        .division-ii {
            background-color: #99faed;
        }
        .division-iii {
            background-color: #eddc71;
        }
        .division-iv {
            background-color: #b6b0b0;
        }
        .division-zero {
            background-color: #eb4b4b;
        }
    </style>
</head>
<body>
    <!-- HEADER SECTION -->
    <div class="header-section">
        <h3>THE UNITED REPUBLIC OF TANZANIA</h3>
        <h4>THE PRESIDENT'S OFFICE - RALG</h4>
        <h4>{{ strtoupper(auth()->user()->school->school_name) }}</h4>
    </div>

    <!-- EXAM DETAILS -->
    <table>
        <tr>
            <th colspan="2">{{ strtoupper($results->first()->class_name) }} {{ strtoupper($results->first()->exam_type) }} RESULTS</th>
        </tr>
        <tr>
            <td><strong>EXAM DATE:</strong></td>
            <td>{{ \Carbon\Carbon::parse($date)->format('d.m.Y') }}</td>
        </tr>
        <tr>
            <td><strong>TERM:</strong></td>
            <td>{{ strtoupper($results->first()->Exam_term) }}</td>
        </tr>
        <tr>
            <td><strong>NUMBER OF CANDIDATES:</strong></td>
            <td>{{ $totalUniqueStudents }}</td>
        </tr>

        @if($marking_style != 3)
        <tr>
            <td><strong>CLASS AVERAGE:</strong></td>
            <td>{{ number_format($sumOfCourseAverages, 4) }}</td>
        </tr>
        <tr>
            <td><strong>AVERAGE OF:</strong></td>
            <td>{{ number_format($generalClassAvg, 2) }}</td>
        </tr>
        @endif
    </table>

    @if($marking_style == 3)
    <!-- DIVISION PERFORMANCE SUMMARY FOR MARKING STYLE 3 -->
    <table>
        <tr style="background: #bba338; color: black;">
            <th colspan="6">DIVISION PERFORMANCE SUMMARY</th>
        </tr>
        <tr>
            <th>Gender</th>
            <th>I</th>
            <th>II</th>
            <th>III</th>
            <th>IV</th>
            <th>0</th>
        </tr>
        <tr>
            @php
                // Calculate division counts for girls
                $girlsDivisions = ['I' => 0, 'II' => 0, 'III' => 0, 'IV' => 0, '0' => 0];
                // Calculate division counts for boys
                $boysDivisions = ['I' => 0, 'II' => 0, 'III' => 0, 'IV' => 0, '0' => 0];

                foreach ($sortedStudentsResults as $student) {
                    if (isset($student['division'])) {
                        if ($student['gender'] == 'female') {
                            $girlsDivisions[$student['division']]++;
                        } elseif ($student['gender'] == 'male') {
                            $boysDivisions[$student['division']]++;
                        }
                    }
                }
            @endphp
            <td>Girls</td>
            <td>{{ $girlsDivisions['I'] }}</td>
            <td>{{ $girlsDivisions['II'] }}</td>
            <td>{{ $girlsDivisions['III'] }}</td>
            <td>{{ $girlsDivisions['IV'] }}</td>
            <td>{{ $girlsDivisions['0'] }}</td>
        </tr>
        <tr>
            <td>Boys</td>
            <td>{{ $boysDivisions['I'] }}</td>
            <td>{{ $boysDivisions['II'] }}</td>
            <td>{{ $boysDivisions['III'] }}</td>
            <td>{{ $boysDivisions['IV'] }}</td>
            <td>{{ $boysDivisions['0'] }}</td>
        </tr>
        <tr>
            <td><strong>Total</strong></td>
            <td><strong>{{ $divisionAggregate['I'] ?? 0 }}</strong></td>
            <td><strong>{{ $divisionAggregate['II'] ?? 0 }}</strong></td>
            <td><strong>{{ $divisionAggregate['III'] ?? 0 }}</strong></td>
            <td><strong>{{ $divisionAggregate['IV'] ?? 0 }}</strong></td>
            <td><strong>{{ $divisionAggregate['0'] ?? 0 }}</strong></td>
        </tr>
    </table>
    @else
    <!-- OVERALL GRADE SUMMARY FOR MARKING STYLES 1 & 2 -->
    <table>
        <tr style="background: #bba338; color: black;">
            <th colspan="6">OVERALL GRADE SUMMARY</th>
        </tr>
        <tr>
            <th>Gender</th>
            <th>A</th>
            <th>B</th>
            <th>C</th>
            <th>D</th>
            <th>E</th>
        </tr>
        <tr>
            <td>Girls</td>
            <td>{{ $totalFemaleGrades['A'] }}</td>
            <td>{{ $totalFemaleGrades['B'] }}</td>
            <td>{{ $totalFemaleGrades['C'] }}</td>
            <td>{{ $totalFemaleGrades['D'] }}</td>
            <td>{{ $totalFemaleGrades['E'] }}</td>
        </tr>
        <tr>
            <td>Boys</td>
            <td>{{ $totalMaleGrades['A'] }}</td>
            <td>{{ $totalMaleGrades['B'] }}</td>
            <td>{{ $totalMaleGrades['C'] }}</td>
            <td>{{ $totalMaleGrades['D'] }}</td>
            <td>{{ $totalMaleGrades['E'] }}</td>
        </tr>
        <tr>
            <td><strong>Total</strong></td>
            <td><strong>{{ $totalFemaleGrades['A'] + $totalMaleGrades['A'] }}</strong></td>
            <td><strong>{{ $totalFemaleGrades['B'] + $totalMaleGrades['B'] }}</strong></td>
            <td><strong>{{ $totalFemaleGrades['C'] + $totalMaleGrades['C'] }}</strong></td>
            <td><strong>{{ $totalFemaleGrades['D'] + $totalMaleGrades['D'] }}</strong></td>
            <td><strong>{{ $totalFemaleGrades['E'] + $totalMaleGrades['E'] }}</strong></td>
        </tr>
    </table>
    @endif

    <!-- STUDENTS PERFORMANCE -->
    <table>
        <tr style="background: #bba338; color: black;">
            <th colspan="{{ $marking_style == 3 ? 8 : 6 }}">STUDENTS PERFORMANCE</th>
        </tr>
        <thead>
            <tr>
                <th>S/N</th>
                <th>Adm.No.</th>
                <th>Sex</th>
                <th>Student Name</th>

                @if($marking_style == 3)
                    <th>AGGT</th>
                    <th>DIV</th>
                @endif

                @foreach ($results->groupBy('course_id')->keys() as $courseId)
                    <th>{{ strtoupper($results->firstWhere('course_id', $courseId)->course_code) }}</th>
                @endforeach

                @if($marking_style != 3)
                    <th>TOTAL</th>
                    <th>AVG</th>
                    <th>GRADE</th>
                @endif
                <th>RANK</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sortedStudentsResults as $index => $studentResult)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ strtoupper($studentResult['admission_number']) }}</td>
                <td>{{ strtoupper($studentResult['gender'][0]) }}</td>
                <td>{{ ucwords(strtolower($studentResult['student_name'])) }}</td>

                @if($marking_style == 3)
                    <td>{{ $studentResult['aggregate_points'] ?? 'N/A' }}</td>
                    <td class="division-{{ strtolower($studentResult['division']) }}">
                        {{ $studentResult['division'] === '0' ? '0' : $studentResult['division'] }}
                    </td>
                @endif

                @foreach ($studentResult['courses'] as $course)
                    <td>
                        @if($marking_style == 3)
                             {{ $course['grade'] ?? 'X' }}
                        @else
                            {{ $course['score'] ?? 'X' }}
                        @endif
                    </td>
                @endforeach

                @if($marking_style != 3)
                    <td>{{ $studentResult['total_marks'] }}</td>
                    <td>{{ number_format($studentResult['average'], 2) }}</td>
                    <td>{{ $studentResult['grade'] === 'ABS' ? 'X' : $studentResult['grade'] }}</td>
                @endif
                <td>{{ $studentResult['grade'] === 'ABS' ? 'X' : $studentResult['position'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- SUBJECTWISE RANKINGS -->
    <table>
        <tr style="background: #bba338; color: black;">
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
            <td>{{ ucwords(strtolower($course['course_name'])) }}</td>
            <td>{{ strtoupper($course['course_code']) }}</td>
            <td>{{ number_format($course['average_score'], 2) }}</td>
            <td>{{ $course['position'] }}</td>
            <td>{{ strtoupper($course['grade']) }}</td>
        </tr>
        @endforeach
    </table>

    @if($marking_style != 3)
    <!-- SUBJECTWISE PERFORMANCE SUMMARY FOR MARKING STYLES 1 & 2 -->
    <table>
        <tr style="background: #bba338; color: black;">
            <th colspan="16">SUBJECTWISE PERFORMANCE SUMMARY</th>
        </tr>
        <tr>
            <th rowspan="2">SUBJECTS</th>
            <th colspan="3">A</th>
            <th colspan="3">B</th>
            <th colspan="3">C</th>
            <th colspan="3">D</th>
            <th colspan="3">E</th>
        </tr>
        <tr>
            <th>Boys</th>
            <th>Girls</th>
            <th>Total</th>
            <th>Boys</th>
            <th>Girls</th>
            <th>Total</th>
            <th>Boys</th>
            <th>Girls</th>
            <th>Total</th>
            <th>Boys</th>
            <th>Girls</th>
            <th>Total</th>
            <th>Boys</th>
            <th>Girls</th>
            <th>Total</th>
        </tr>
        @foreach ($subjectGradesByGender as $courseId => $grades)
        <tr>
            <td>{{ strtoupper($courses->find($courseId)->course_code) }}</td>

            <!-- Grade A -->
            <td>{{ $grades['A']['male'] }}</td>
            <td>{{ $grades['A']['female'] }}</td>
            <td>{{ $grades['A']['male'] + $grades['A']['female'] }}</td>

            <!-- Grade B -->
            <td>{{ $grades['B']['male'] }}</td>
            <td>{{ $grades['B']['female'] }}</td>
            <td>{{ $grades['B']['male'] + $grades['B']['female'] }}</td>

            <!-- Grade C -->
            <td>{{ $grades['C']['male'] }}</td>
            <td>{{ $grades['C']['female'] }}</td>
            <td>{{ $grades['C']['male'] + $grades['C']['female'] }}</td>

            <!-- Grade D -->
            <td>{{ $grades['D']['male'] }}</td>
            <td>{{ $grades['D']['female'] }}</td>
            <td>{{ $grades['D']['male'] + $grades['D']['female'] }}</td>

            <!-- Grade E -->
            <td>{{ $grades['E']['male'] }}</td>
            <td>{{ $grades['E']['female'] }}</td>
            <td>{{ $grades['E']['male'] + $grades['E']['female'] }}</td>
        </tr>
        @endforeach
    </table>
    @else
    <!-- SUBJECTWISE PERFORMANCE SUMMARY FOR MARKING STYLE 3 -->
    <table>
        <tr style="background: #bba338; color: black;">
            <th colspan="19">SUBJECTWISE PERFORMANCE SUMMARY</th>
        </tr>
        <tr>
            <th rowspan="2">SUBJECTS</th>
            <th colspan="3">A</th>
            <th colspan="3">B</th>
            <th colspan="3">C</th>
            <th colspan="3">D</th>
            <th colspan="3">F</th>
            <th colspan="3">ABS</th>
        </tr>
        <tr>
            <th>Boys</th>
            <th>Girls</th>
            <th>Total</th>
            <th>Boys</th>
            <th>Girls</th>
            <th>Total</th>
            <th>Boys</th>
            <th>Girls</th>
            <th>Total</th>
            <th>Boys</th>
            <th>Girls</th>
            <th>Total</th>
            <th>Boys</th>
            <th>Girls</th>
            <th>Total</th>
            <th>Boys</th>
            <th>Girls</th>
            <th>Total</th>
        </tr>
        @foreach ($subjectGradesByGender as $courseId => $grades)
        <tr>
            <td>{{ strtoupper($courses->find($courseId)->course_code) }}</td>

            <!-- Grade A -->
            <td>{{ $grades['A']['male'] }}</td>
            <td>{{ $grades['A']['female'] }}</td>
            <td>{{ $grades['A']['male'] + $grades['A']['female'] }}</td>

            <!-- Grade B -->
            <td>{{ $grades['B']['male'] }}</td>
            <td>{{ $grades['B']['female'] }}</td>
            <td>{{ $grades['B']['male'] + $grades['B']['female'] }}</td>

            <!-- Grade C -->
            <td>{{ $grades['C']['male'] }}</td>
            <td>{{ $grades['C']['female'] }}</td>
            <td>{{ $grades['C']['male'] + $grades['C']['female'] }}</td>

            <!-- Grade D -->
            <td>{{ $grades['D']['male'] }}</td>
            <td>{{ $grades['D']['female'] }}</td>
            <td>{{ $grades['D']['male'] + $grades['D']['female'] }}</td>

            <!-- Grade F -->
            <td>{{ $grades['F']['male'] }}</td>
            <td>{{ $grades['F']['female'] }}</td>
            <td>{{ $grades['F']['male'] + $grades['F']['female'] }}</td>

            <!-- ABS -->
            <td>{{ $grades['ABS']['male'] }}</td>
            <td>{{ $grades['ABS']['female'] }}</td>
            <td>{{ $grades['ABS']['male'] + $grades['ABS']['female'] }}</td>
        </tr>
        @endforeach
    </table>
    @endif

    <!-- FOOTER -->
    <table>
        <tr>
            <td colspan="2">
                <strong>Printed By:</strong> {{ auth()->user()->name }}
            </td>
        </tr>
        <tr>
            <td><strong>Printed At:</strong> {{ now()->format('d-M-Y H:i') }}</td>
            <td><strong>School:</strong> {{ auth()->user()->school->school_name }}</td>
        </tr>
    </table>
</body>
</html>
