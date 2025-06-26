<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Subject Results Report</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
            padding-bottom: 50px; /* Ongeza padding chini kwa footer */
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header img {
            float: left;
            width: 70px;
            height: auto;
        }

        .header h3, .header h4, .header h5 {
            margin: 2px;
            text-transform: uppercase;
            font-size: 16px;
        }

        hr {
            border: 0;
            border-top: 2px solid #ccc;
            margin: 10px 0;
        }

        .info, .summary, .grades {
            margin-bottom: 15px;
        }

        .info p, .summary p {
            margin: 2px 0;
        }

        .summary-table, .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .summary-table th,
        .summary-table td,
        .results-table th,
        .results-table td {
            border: 1px solid #aaa;
            padding: 4px;
            text-align: center;
        }

        .summary-table th {
            background-color: #343a40;
            color: #fff;
        }

        .results-table th {
            background-color: #f1f1f1;
        }

        .average-box {
            padding: 8px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            width: fit-content;
            margin-top: 10px;
        }

        .grade-badge {
            padding: 2px 8px;
            border-radius: 3px;
            color: #fff;
            font-weight: bold;
        }

        .grade-A { background-color: #28a745; }
        .grade-B { background-color: #17a2b8; }
        .grade-C { background-color: #ffc107; color: #000; }
        .grade-D { background-color: #fd7e14; }
        .grade-E { background-color: #dc3545; }

        .absent {
            background-color: #dc3545;
            color: #fff;
            padding: 2px 6px;
            font-weight: bold;
        }
        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 25px;
            font-size: 10px;
            border-top: 1px solid #ddd;
            padding: 4px 20px;
            text-align: center;
            background-color: white; /* Hakikisha footer ina background */
            z-index: 1000; /* Hakikisha footer iko juu ya content */
        }
        footer .page-number:after {
        content: "Page " counter(page);
        }
        footer .copyright {
        float: left;
        margin-left: 10px;
        }
        footer .printed {
        float: right;
        margin-right: 10px;
        }
        /* Clear floats */
        footer:after {
        content: "";
        display: table;
        clear: both;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('assets/img/logo/' . Auth::user()->school->logo) }}" alt="logo">
        <h3>THE UNITED REPUBLIC OF TANZANIA</h3>
        <h4>THE PRESIDENT'S OFFICE - RALG</h4>
        <h4>{{ Auth::user()->school->school_name }} - {{ Auth::user()->school->postal_address }}, {{ Auth::user()->school->postal_name }}</h4>
        <h5>{{ $results->first()->exam_type }} Results - {{ $month }} {{ $year }}</h5>
    </div>

    <hr>

    <div class="info">
        <p><strong>Subject:</strong> {{ strtoupper($subjectCourse->course_name) }} - {{ strtoupper($subjectCourse->course_code) }}</p>
        <p><strong>Teacher:</strong> {{ ucwords(strtolower($results->first()->teacher_firstname)) }} {{ strtoupper($results->first()->teacher_lastname[0]) }}.</p>
        <p><strong>Exam Date:</strong> {{ \Carbon\Carbon::parse($results->first()->exam_date)->format('d F Y') }}</p>
        <p><strong>Term:</strong> {{ strtoupper($results->first()->Exam_term) }}</p>
    </div>

    <div class="grades">
        <p><strong>Performance Summary</strong></p>
        <table class="summary-table">
            <tr>
                <th>Grade</th>
                <th>A</th>
                <th>B</th>
                <th>C</th>
                <th>D</th>
                <th>E</th>
            </tr>
            <tr>
                <td><strong>Number</strong></td>
                <td>{{ $gradeCounts['A'] }}</td>
                <td>{{ $gradeCounts['B'] }}</td>
                <td>{{ $gradeCounts['C'] }}</td>
                <td>{{ $gradeCounts['D'] }}</td>
                <td>{{ $gradeCounts['E'] }}</td>
            </tr>
        </table>
    </div>

    <div class="average-box">
        <p><strong>Subject Average:</strong> {{ number_format($averageScore, 2) }}</p>
        <p><strong>Grade:</strong>
            <span class="grade-badge grade-{{ $averageGrade }}">{{ $averageGrade }} -
                @php
                    echo match($averageGrade) {
                        'A' => 'Excellent',
                        'B' => 'Good',
                        'C' => 'Pass',
                        'D' => 'Poor',
                        'E' => 'Fail',
                        default => 'N/A'
                    };
                @endphp
            </span>
        </p>
    </div>

    <hr>

    <h4 style="text-align:center;">Students Examination Records</h4>
    <table class="results-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Admission No.</th>
                <th>Student Name</th>
                <th>Gender</th>
                <th>Stream</th>
                <th>Marks</th>
                <th>Grade</th>
                <th>Position</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($results as $result)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ strtoupper($result->admission_number) }}</td>
                <td style="text-align: left">{{ ucwords(strtolower($result->first_name . ' ' . $result->middle_name . ' ' . $result->last_name)) }}</td>
                <td>{{ ucfirst($result->gender[0]) }}</td>
                <td>{{ strtoupper($result->group) }}</td>
                <td>
                    @if ($result->score === null)
                        <span class="absent">X</span>
                    @else
                        {{ $result->score }}
                    @endif
                </td>
                <td>{{ $result->grade }}</td>
                <td>
                    @if ($result->score === null)
                        <span class="absent">X</span>
                    @else
                        {{ $result->position }}
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <footer>
        <span class="copyright">
        &copy; {{ ucwords(strtolower(Auth::user()->school->school_name)) }} – {{ date('Y') }}
        </span>
        <span class="page-number"></span>
        <span class="printed">
        Printed at: {{ now()->format('d-M-Y H:i') }}
        </span>
    </footer>
</body>
</html>
