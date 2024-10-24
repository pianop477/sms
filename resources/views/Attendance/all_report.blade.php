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
            position: fixed;
            bottom: -30px;
            align-content: space-around;
            font-size: 12px;
            /* border-top: 1px solid black; */
        }
        .page-number:before {
            content: "Page " counter(page);
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
                <img src="{{public_path('assets/img/logo/'. Auth::user()->school->logo)}}" alt="" style="max-width: 80px;">
            </div>
            <div class="header">
                <h3>{{ Auth::user()->school->school_name }} - P.O Box {{ Auth::user()->school->postal_address }}, {{ Auth::user()->school->postal_name }}</h3>
                <h4>class attendance report for </h4>
                <h4 class="text-uppercase">Academic year {{\Carbon\Carbon::parse($attendances->first()->attendance_date)->format('F, Y')}}</h4>
                <h4>Class Name: {{$attendances->first()->class_name}}</h4>
            </div>

            {{-- looping attendances records --}}
            @foreach ($datas as $date => $attendances)
                <div class="date-section">
                    <div class="summary-header">
                        <p>attendance report summary</p>
                    </div>
                    <div class="summary-content">
                        <p style="border: 1px solid gray; padding:5px; background:rgb(190, 190, 190);">Date: <strong>{{\Carbon\Carbon::parse($date)->format('d/F/Y')}}</strong></p>
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
                    <hr>
                    <h5 style="text-transform:capitalize; text-align:center; font-size:20px;">students attendance records</h5>
                    <table class="table" style="text-transform: capitalize">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th style="text-align: center">Admission No.</th>
                                <th>Student Name</th>
                                <th style="text-align:center" class="text-center">Gender</th>
                                <th style="text-align:center" class="text-center">Stream</th>
                                <th style="text-align: center;">attendance Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($attendances as $index => $attendance )
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td style="text-transform: uppercase;">{{$attendance->school_reg_no}}/{{ $attendance->admission_number }}</td>
                                    <td style="text-transform:capitalize">{{ $attendance->first_name }} {{ $attendance->middle_name }} {{ $attendance->last_name }}</td>
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
    <div class="footer">
        <footer>
            <div class="page-number"></div>
        </footer>
    </div>
</body>
</html>
