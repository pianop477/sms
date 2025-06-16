<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>General Class Attendance Report</title>
    <link rel="stylesheet" href="{{public_path('assets/css/print_layout.css')}}">
    <style>
       .footer {
            width: 100%;
            padding: 10px 20px;
            position: fixed;
            bottom: 0;
            left: 0;
            background-color: #f8f9fa;
            font-size: 12px;
            border-top: 1px solid #ddd;
        }

        .footer-content {
            display: flex;
            flex-direction: row; /* Ensure horizontal layout */
            justify-content: space-between; /* Push items to both ends */
            align-items: center;
            width: 100%;
        }

        .page-number {
            flex: 1; /* Push printed-on to the far right */
            text-align: left;
        }

        .printed-on {
            text-align: right;
        }

        .page-number:before {
            content: "Page " counter(page);
        }

        /* Inline your Bootstrap CSS styles here */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            margin-bottom: 40px;
        }
        @media print {
            .no-print {
                display: none;
            }
            h1, h2, h4, h5, h6 {
                text-transform: uppercase;
                text-align: center
            }
            .print-only {
                display: block;
            }
            .footer {
                position: fixed;
                bottom: 0;
                width: 100%;
                border-top: 1px solid #ddd;
                padding-top: 10px;
            }
            thead {
                display: table-header-group;
                background-color: gray; /* Adds a gray background to thead */
            }
            tbody {
                display: table-row-group;
            }
            .table {
                border: 1px solid black;
                border-collapse: collapse;
                width: 100%;
            }
            .table th,
            .table td {
                border: 1px solid black;
            }
        }

        .table {
            width: 100%;
            border: 1px solid black;
            border-collapse: collapse;
            font-size: 12px;
        }

        .table th, .table td {
            border: 1px solid #dee2e6;
            padding: 2px;
            text-align: left;
            text-transform: capitalize
        }

        .table th {
            background-color: #343a40;
            color: #fff;
            text-align: center;
        }

        .table td {
            background-color: #fff;
        }

        .table img {
            display: block;
            margin: 0 auto;
            width: 40px;
            height: 40px;
            border-radius: 50%;
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


    </style>
</head>
<body>
    <div class="container">
        @if (isset($datas) && $datas->isNotEmpty())
            <div class="title">
                <h3>{{_('the united republic of tanzania')}}</h3>
                <h4>{{_("the president's office - ralg")}}</h4>
            </div>
            <div class="logo">
                <img src="{{public_path('assets/img/logo/'. Auth::user()->school->logo)}}" alt="" style="max-width: 100px;">
            </div>
            <div class="header">
                <h3>{{ Auth::user()->school->school_name }}</h3>
                <h4>{{ Auth::user()->school->postal_address }}</h4>
                <h4>{{ Auth::user()->school->postal_name }} - {{Auth::user()->school->country}}</h4>
                <h4>attendance report Month: {{\Carbon\Carbon::parse($attendances->first()->attendance_date)->format('F, Y')}} </h4>
                <h4>Class: {{$attendances->first()->class_name}}</h4>
            </div>

            {{-- looping attendances records --}}
            @foreach ($datas as $date => $attendances)
                <div class="date-section">
                    <div class="summary-header">
                        <p>attendance summary</p>
                    </div>
                    <div class="summary-content">
                        <p style="border: 1px solid gray; padding:5px; background:rgb(190, 190, 190);">Date: <strong>{{\Carbon\Carbon::parse($date)->format('F d, Y')}}</strong></p>
                        <table class="table" style="text-align: center">
                            <tr>
                                <th>Gender</th>
                                <th>Present</th>
                                <th>Absent</th>
                                <th>Permision</th>
                                <th>Total</th>
                            </tr>
                            <tr>
                                <td>Male</td>
                                <td>{{ $maleSummary[$date]['present'] }}</td>
                                <td>{{ $maleSummary[$date]['absent'] }}</td>
                                <td>{{ $maleSummary[$date]['permission'] }}</td>
                                <td>{{ $maleSummary[$date]['present'] + $maleSummary[$date]['absent'] + $maleSummary[$date]['permission'] }}</td>
                            </tr>
                            <tr>
                                <td>Female</td>
                                <td>{{ $femaleSummary[$date]['present'] }}</td>
                                <td>{{ $femaleSummary[$date]['absent'] }}</td>
                                <td>{{ $femaleSummary[$date]['permission'] }}</td>
                                <td>{{ $femaleSummary[$date]['present'] + $femaleSummary[$date]['absent'] + $femaleSummary[$date]['permission'] }}</td>
                            </tr>
                        </table>
                    </div>
                    <h5 style="text-transform:capitalize; text-align:center; font-size:20px;">students attendance records</h5>
                    <table class="table" style="text-transform: capitalize">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th style="text-align: center">Adm No.</th>
                                <th>Student Name</th>
                                <th style="text-align:center" class="text-center">Gender</th>
                                <th style="text-align:center" class="text-center">Stream</th>
                                <th style="text-align: center;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($attendances as $index => $attendance )
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td style="text-transform: uppercase;">{{$attendance->admission_number }}</td>
                                    <td>{{ ucwords(strtolower($attendance->first_name . ' ' . $attendance->middle_name . ' ' . $attendance->last_name)) }}</td>
                                    <td style="text-align:center; text-transform:capitalize">{{ $attendance->gender[0] }}</td>
                                    <td style="text-align:center; text-transform:capitalize">{{ $attendance->group }}</td>
                                    <td style="text-transform: capitalize; text-align:center">{{ ucfirst($attendance->attendance_status) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        @else
            <p>No data found!</p>
        @endif
    </div>
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
