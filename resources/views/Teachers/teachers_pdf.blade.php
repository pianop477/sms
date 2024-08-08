<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Teachers export</title>
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
                        <h5 class="text-uppercase">Teachers List</h5>
                    </div>
                </div>
                @if ($teachers->isEmpty())
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
                            <th style="text-align: center">WorkerID</th>
                            <th>Gender</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Dob</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Qualification</th>
                            <th style="text-align: center">Joined</th>
                            <th>Street</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($teachers as $teacher)
                            <tr>
                                <td style="text-transform: uppercase; text-align:center">{{ $teacher->school_reg_no . '/' . $teacher->joined . '/' . str_pad($teacher->id, 3, '0', STR_PAD_LEFT) }}</td>
                                <td style="text-transform: uppercase; text-align:center; width:5px;">{{ $teacher->gender[0] }}</td>
                                <td style="text-transform: capitalize">{{ $teacher->first_name }}</td>
                                <td style="text-transform: capitalize">{{ $teacher->last_name }}</td>
                                <td>{{ \Carbon\Carbon::parse($teacher->dob)->format('d/M/Y') }}</td>
                                <td>{{ $teacher->phone }}</td>
                                <td>{{ $teacher->email }}</td>
                                <td style="text-transform: capitalize">
                                    @if ($teacher->qualification == 1)
                                        {{ __('Masters Degree') }}
                                    @elseif ($teacher->qualification == 2)
                                        {{ __('Bachelor Degree') }}
                                    @elseif ($teacher->qualification == 3)
                                        {{ __('Diploma') }}
                                    @else
                                        {{ __('Certificate') }}
                                    @endif
                                </td>
                                <td style="text-align: center">{{ $teacher->joined }}</td>
                                <td class="" style="text-transform: capitalize">{{ $teacher->address }}</td>
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
