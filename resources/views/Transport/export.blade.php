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
            /* line-height: 2px; */
            margin: 0; /* Remove default margin */
            padding: 0; /* Remove default padding */
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

        .container {
            display: flex;
            padding: 10px;
            flex-direction: row;
            flex-wrap: wrap;
            border-bottom: 2px solid gray;
        }
        .logo {
            position: absolute;
            width: 50px;
            left: 7px;
            top: 5px;
            color: inherit;
        }
        .header {
            text-align: center;
            position: relative;
            top: 0;
            left: 40px;
            text-transform: uppercase;
            line-height: 1px;
        }
        th, td {
            border: 1px solid black;
        }
        .table {
            width: 100%;
            border: 1px solid black;
            border-collapse: collapse;
            font-size: 11px
        }
        thead {
                display: table-header-group;
                background-color: gray; /* Adds a gray background to thead */
            }
            tbody {
                display: table-row-group;
            }
            .table th,
            .table td {
                /* border: 1px solid black; */
                text-transform: capitalize;
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
                        <h5 class="text-uppercase">students in Bus Number - {{$trans->bus_no}}</h5>
                        <h5>Driver Name: {{$trans->driver_name}}</h5>
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
                <p>Bus Routine: <span style="font-weight: bold; text-transform:uppercase">{{$trans->routine}}</span></p>
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student Name</th>
                            <th>Gender</th>
                            <th>Class Name</th>
                            <th>Stream</th>
                            <th>Street</th>
                            <th>Parent Phone</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td style="text-transform: capitalize">{{ $student->first_name }} {{ $student->middle_name }} {{ $student->last_name }}</td>
                                <td style="text-transform: uppercase; text-align:center; width:5px;">{{ $student->gender[0] }}</td>
                                <td style="text-transform:uppercase; text-align:center">{{ $student->class_code}}</td>
                                <td style="text-transform:uppercase; text-align:center">{{ $student->group}}</td>
                                <td class="" style="text-transform: capitalize">{{ $student->address }}</td>
                                <td>{{$student->phone}}</td>
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
    <div class="footer">
        <footer>
            <div class="page-number"></div>
        </footer>
    </div>
</body>
</html>
