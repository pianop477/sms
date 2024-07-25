<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Result</title>
    <style>
        body {
            font-family: 'Arial, sans-serif';
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Student Result for {{ $student->first_name }} {{ $student->last_name }}</h1>
    <p>Year: {{ $year }}</p>
    <p>Exam Type: {{ $type }}</p>

    <table>
        <thead>
            <tr>
                <th>Course Name</th>
                <th>Course Code</th>
                <th>Score</th>
                <th>Grade</th>
                <th>Remark</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($results as $result)
                <tr>
                    <td>{{ $result->course_name }}</td>
                    <td>{{ $result->course_code }}</td>
                    <td>{{ $result->score }}</td>
                    <td>{{ $result->grade }}</td>
                    <td>{{ $result->remark }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Summary</h2>
    <p>Average Score: {{ $summary['average'] }}</p>
    <p>Total Marks: {{ $summary['total_marks'] }}</p>
    <p>Position: {{ $currentStudentPosition }}</p>
</body>
</html>
