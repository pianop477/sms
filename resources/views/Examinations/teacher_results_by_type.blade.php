<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Shule | App</title>

    <style>
        /* Inline your Bootstrap CSS styles here */

        @media print {
            .no-print {
                display: none;
            }
        @import url('https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
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
            @page {
                margin: 15mm;
                @top-left {
                        content: none;
                    }
                    @top-right {
                        content: none;
                    }
                    @bottom-left {
                        content: none;
                    }
                    @bottom-right {
                        content: none;
                    }
            }
            thead {
                display: table-header-group;
                background-color: gray; /* Adds a gray background to thead */
            }
            tbody {
                display: table-row-group;
            }
            body {
                color: black; /* Sets text color to black */
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
            .logo {
                color: inherit; /* Ensure logo color is not changed */
            }
        }
        .print-only {
            display: none;
        }
    </style>
</head>
<body>
    <div class="col-md-12 mt-2">
        <div class="card">
            @if ($results->isEmpty())
                <div class="card-body mt-3">
                    <div class="row">
                        <div class="col-12 mt-2">
                            <div class="alert alert-warning text-center">
                                <p>No Results records found!</p>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="card-body">
                    <div class="row">
                        <div class="col-2">
                            <div class="logo">
                                <img src="{{public_path('assets/img/logo/' .Auth::user()->school->logo)}}" alt="" class="" style="max-width: 100px; object-fit:cover;">
                            </div>
                        </div>
                        <div class="col-8 text-center text-uppercase">
                            <h4>{{_('the united republic of tanzania')}}</h4>
                            <h5>{{_("the president's office - ralg")}}</h5>
                            <h5>{{Auth::user()->school->school_name}} - P.O Box {{Auth::user()->school->postal_address}}, {{Auth::user()->school->postal_name}}</h5>
                            <h6>{{$results->first()->exam_type}} results for {{$month}} - {{$year}}</h6>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12 mt-2">
                            <h6 class="text-capitalize text-center border-bottom p-2">Results Details Summary</h6>
                        </div>
                    </div>
                    <div class="row border-bottom">
                        <div class="col-4 mt-2">
                            <p class="text-capitalize text-center p-2 border-bottom font-weight-bold">Examination Results Details</p>
                            <ul class="text-capitalize">
                                <li class="border-bottom">examination type: <strong>{{$results->first()->exam_type}}</strong></li>
                                <li class="border-bottom">month: <strong>{{$month}}</strong></li>
                                <li class="border-bottom">Date: <strong>{{\Carbon\Carbon::parse($results->first()->exam_date)->format('d-F-Y')}}</strong></li>
                                <li class="border-bottom">Course name: <strong>{{$courses->course_name}} - {{$courses->course_code}}</strong></li>
                                <li class="border-bottom">Course Teacher: <strong>{{$results->first()->teacher_firstname}}, {{$results->first()->teacher_lastname[0]}}</strong></li>
                            </ul>
                        </div>
                        <div class="col-4 mt-2" style="border-left: 1px solid black;">
                            <p class="text-capitalize text-center p-2 border-bottom font-weight-bold">students statistics by gender</p>
                            <ul class="text-capitalize">
                                <li class="border-bottom">total students: <strong>{{$results->count()}}</strong></li>
                                <li class="border-bottom">males: <strong>{{$maleStudents}}</strong></li>
                                <li class="border-bottom">females: <strong>{{$femaleStudents}}</strong></li>
                            </ul>
                        </div>
                        <div class="col-4 mt-2" style="border-left: 1px solid black;">
                            <p class="text-capitalize text-center p-2 border-bottom font-weight-bold">students performance statistics</p>
                            <ul class="text-capitalize">
                                <li class="border-bottom">Average Score: <strong>{{$averageScore}}</strong></li>
                                <li class="border-bottom">Average Grade: <strong>{{$averageGrade}}</strong></li>
                                <li class="border-bottom">Number of As: <strong>{{$gradeCounts['A']}}</strong></li>
                                <li class="border-bottom">Number of Bs: <strong>{{$gradeCounts['B']}}</strong></li>
                                <li class="border-bottom">Number of Cs: <strong>{{$gradeCounts['C']}}</strong></li>
                                <li class="border-bottom">Number of Ds: <strong>{{$gradeCounts['D']}}</strong></li>
                                <li class="border-bottom">Number of Es: <strong>{{$gradeCounts['E']}}</strong></li>
                            </ul>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mt-2">
                            <h6 class="text-capitalize text-center border-bottom p-2">examination results by position</h6>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mt-2">
                            <table class="table table-bordered table-responsive">
                                <thead>
                                    <tr class="text-uppercase">
                                        <th>#</th>
                                        <th>First Name</th>
                                        <th>Middle Name</th>
                                        <th>Last Name</th>
                                        <th>Gender</th>
                                        <th>Score</th>
                                        <th>Grade</th>
                                        <th>Position</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results as $result)
                                        <tr>
                                            <td>{{$result->studentId}}</td>
                                            <td>{{$result->first_name}}</td>
                                            <td>{{$result->middle_name}}</td>
                                            <td>{{$result->last_name}}</td>
                                            <td class="text-capitalize">{{$result->gender[0]}}</td>
                                            <td>{{$result->score}}</td>
                                            <td>{{$result->grade}}</td>
                                            <td>{{$result->position}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
