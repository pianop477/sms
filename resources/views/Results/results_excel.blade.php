<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Results Excel Export</title>
</head>
<body>
    <!-- STUDENT-WISE PERFORMANCE -->
    <table>
        <thead>
            <tr>
                <th>Adm.No.</th>
                <th>Sex</th>
                <th>Student Name</th>
                @foreach ($results->groupBy('course_id')->keys() as $courseId)
                    <th>{{ $results->firstWhere('course_id', $courseId)->course_code }}</th>
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
                <td>{{ $studentResult['admission_number'] }}</td>
                <td>{{ $studentResult['gender'][0] }}</td>
                <td>{{ $studentResult['student_name'] }}</td>
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
            <td>{{ $course['course_name'] }}</td>
            <td>{{ $course['course_code'] }}</td>
            <td>{{ number_format($course['average_score'], 2) }}</td>
            <td>{{ $course['position'] }}</td>
            <td>{{ $course['grade'] }}</td>
        </tr>
        @endforeach
    </table>
</body>
</html>
