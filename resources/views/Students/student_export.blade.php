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
            padding: 5px;
            flex-direction: row;
            flex-wrap: wrap;
        }
        .logo {
            position: absolute;
            width: 50px;
            left: 7px;
            top: 20px;
            color: inherit;
        }
        .header {
            text-align: center;
            position: relative;
            top: 0;
            left: 40px;
            margin-bottom: 10px;
            text-transform: uppercase;
            font-size: 24px;
            color: #343a40;
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
                        <h5 class="text-uppercase">{{$students->first()->class_name}} Students</h5>
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
                <table class="table">
                    <thead>
                        <tr>
                            <th style="text-align: center">Admission No</th>
                            <th>Gender</th>
                            <th>Full Name</th>
                            <th>Dob</th>
                            <th>Street</th>
                            <th>Parent Phone</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                            <tr>
                                <td style="text-transform: uppercase; text-align:center">{{ $student->admission_number }}</td>
                                <td style="text-transform: uppercase; text-align:center;">{{ $student->gender[0] }}</td>
                                <td style="text-transform: capitalize">{{ ucwords(strtolower($student->first_name. ' '. $student->middle_name. ' '. $student->last_name)) }}</td>
                                <td>{{ \Carbon\Carbon::parse($student->dob)->format('d/m/Y') }}</td>
                                <td class="" style="text-transform: capitalize">{{ $student->address }}</td>
                                <td>{{$student->phone}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
