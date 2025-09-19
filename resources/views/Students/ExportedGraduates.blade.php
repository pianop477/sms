<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Graduated Students {{$year}} - {{Auth::user()->school->school_name}}</title>
    <style>
        /* Main Styles */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            font-size: 12px;
            color: #333;
        }

        /* Header Styles */
        .header-container {
            display: flex;
            padding: 5px;
            flex-direction: row;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }

        .logo {
            position: absolute;
            width: 70px;
            left: 7px;
            top: 20px;
            color: inherit;
        }

        .school-info {
           text-align: center;
            position: relative;
            top: 0;
            left: 40px;
            margin-bottom: 10px;
        }

        .school-name {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            color: #343a40;
        }

        .report-title {
            font-size: 16px;
            margin: 5px 0 0 0;
            color: #555;
        }

        .report-subtitle {
            font-size: 14px;
            margin: 10px 0 0 0;
            color: #666;
        }

        /* Table Styles */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            page-break-inside: auto;
        }

        .data-table th {
            background-color: #343a40;
            color: white;
            padding: 6px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
        }

        .data-table td {
            padding: 5px;
            border: 1px solid #ddd;
            vertical-align: middle;
        }

        .data-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .student-photo {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            border: 1px solid #ddd;
        }

        /* Footer Styles */
        @page {
            margin-top: 8mm;
            margin-bottom: 12mm; /* Ongeza nafasi ya chini kwa footer */
            margin-left: 8mm;
            margin-right: 8mm;
        }
        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4mm; /*urefu wa footer*/
            font-size: 10px;
            padding-top: 8px;
            border-top: 1px solid #ddd;
            text-align: center;
            background-color: white;
            z-index: 1000;
        }
        footer .page-number:after {
            /* content: "Page " counter(page); */
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

        /* Print Specific Styles */
        @media print {
            body {
                padding: 0;
                background: white;
            }

            .no-print {
                display: none;
            }

            .data-table {
                font-size: 10px;
            }

            .data-table th,
            .data-table td {
                padding: 4px;
            }

            .footer {
                position: fixed;
                bottom: 0;
            }
        }

        /* Additional Utility Classes */
        .text-center {
            text-align: center;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .text-capitalize {
            text-transform: capitalize;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }

        .status-graduated {
            background-color: #28a745;
            color: white;
        }

         .page-number {
            flex: 1; /* Push printed-on to the far right */
            text-align: left;
        }

        .page-number:before {
            content: "Page " counter(page);
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header-container">
        <div class="logo-container">
            <img class="logo" src="{{ public_path('assets/img/logo/'. Auth::user()->school->logo) }}" alt="School Logo">
        </div>
        <div class="school-info">
            <h1 class="school-name">{{ Auth::user()->school->school_name }}</h1>
            <h2 class="report-title">STANDARD SEVEN GRADUATION REPORT</h2>
            <h3 class="report-subtitle">GRADUATION YEAR: {{ $year }}</h3>
        </div>
    </div>

    <!-- Report Details -->
    <div style="margin-bottom: 15px;">
        <div style="float: left; width: 50%;">
            <p><strong>Report Date:</strong> {{ date('d/m/Y') }}</p>
            <p><strong>Total Graduates:</strong> {{ $studentExport->count() }}</p>
        </div>
        <div style="float: right; width: 50%; text-align: right;">
            {{-- <p><strong>School Code:</strong> {{ Auth::user()->school->school_reg_no }}</p> --}}
            <p><strong>Printed By:</strong> {{ ucwords(strtolower(Auth::user()->first_name))}} {{ ucwords(strtolower(Auth::user()->last_name))}}</p>
        </div>
        <div style="clear: both;"></div>
    </div>

    <!-- Students Table -->
    @if ($studentExport->isEmpty())
        <div style="text-align: center; padding: 20px; border: 1px solid #ddd; margin-top: 20px;">
            <p style="font-style: italic; color: #666;">No graduated students found for {{ $year }}</p>
        </div>
    @else
        <table class="data-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="10%">Photo</th>
                    <th width="15%">Admission No</th>
                    <th width="10%">Gender</th>
                    <th width="40%">Full Name</th>
                    <th width="10%">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($studentExport as $index => $student)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">
                            @php
                                $imageName = $student->image;
                                $imagePath = public_path('assets/img/students/' . $imageName);

                                if (!empty($imageName) && file_exists($imagePath)) {
                                    $avatarImage = public_path('assets/img/students/' . $imageName);
                                } else {
                                    $avatarImage = public_path('assets/img/students/student.jpg');
                                }
                            @endphp
                            <img class="student-photo" src="{{ $avatarImage }}" alt="Student Photo">
                        </td>
                        <td class="text-uppercase text-center">{{ strtoupper($student->admission_number) }}</td>
                        <td class="text-uppercase text-center">{{ ucwords(strtolower($student->gender)) }}</td>
                        <td class="text-capitalize">{{ ucwords(strtolower($student->first_name.' '.$student->middle_name.' '.$student->last_name)) }}</td>
                        <td class="text-center">
                            <span class="status-badge status-graduated">GRADUATED</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Footer Section -->
    <footer>
        <span class="copyright">
        &copy; {{ ucwords(strtolower(Auth::user()->school->school_name)) }} – {{ date('Y') }}
        </span>
        <span class="page-number"></span>
        <span class="printed">
        Printed at: {{ now()->format('d-m-Y H:i') }}
        </span>
    </footer>
</body>
</html>
