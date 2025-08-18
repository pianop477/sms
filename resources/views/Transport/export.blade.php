<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Students</title>
    <style>
        /* Inline your Bootstrap CSS styles here */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        @media print {
            .no-print {
                display: none;
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

        .container {
            display: flex;
            padding: 5px;
            flex-direction: row;
            flex-wrap: wrap;
        }
        .logo {
            position: absolute;
            width: 70px;
            left: 7px;
            top: 5px;
            color: inherit;
        }
        .header h4, .header h5, .header h6 {
            margin: 2px 0; /* Kupunguza nafasi juu na chini */
            line-height: 1.2; /* Kufanya mistari iwe karibu */
            text-align: center;
            text-transform: uppercase;
        }
        .table {
            width: 100%;
            border: 1px solid black;
            border-collapse: collapse;
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

    </style>
</head>
<body>
    <div class="col-md-12">
        <div class="card mt-2">
            <div class="card-body">
                <div class="container">
                    <div class="logo">
                        <img src="{{public_path('assets/img/logo/'. Auth::user()->school->logo)}}" alt="" style="max-width: 80px;">
                    </div>
                    <div class="header">
                        <h4 class="text-uppercase">{{Auth::user()->school->school_name}}</h4>
                        <h5 class="text-uppercase">students in Bus Number - {{$transport->bus_no}}</h5>
                        <h5>Driver Name: {{$transport->driver_name}}</h5>
                    </div>
                </div>
                @if ($students->isEmpty())
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-warning text-center mt-3" role="alert">
                                <p>No records found!</p>
                            </div>
                        </div>
                    </div>
                @else
                <p style="margin-top: 30px">Bus Routine: <span style="font-weight: bold; text-transform:uppercase">{{$transport->routine}}</span></p>
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student Name</th>
                            <th>Gender</th>
                            <th>Class</th>
                            <th>Stream</th>
                            <th>Street</th>
                            <th>Phone</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td style="text-transform: capitalize">{{ ucwords(strtolower($student->first_name. ' '. $student->middle_name. ' '. $student->last_name  )) }}</td>
                                <td style="text-transform: uppercase; text-align:center; width:5px;">{{ $student->gender[0] }}</td>
                                <td style="text-transform:uppercase; text-align:center">{{ $student->class_code}}</td>
                                <td style="text-transform:uppercase; text-align:center">{{ $student->group}}</td>
                                <td class="" style="text-transform: capitalize">{{ $student->address }}</td>
                                <td>{{$student->parent_phone}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <h6>Total Students: {{count($students)}}</h6>
                </div>
            @endif
            </div>
        </div>
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
