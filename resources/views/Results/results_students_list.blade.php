@extends('SRTDashboard.frame')
@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<div class="row">
    <div class="col-12 mt-5">
        <div class="card">
            <div class="card-body">
                @if ($studentsResults->isEmpty())
                <div class="alert alert-warning">
                    No students found
                </div>
                @else
                <div class="row">
                    <div class="col-8">
                        <h4 class="header-title text-uppercase">generate report for {{$classId->class_name ?? ''}}</h4>
                    </div>
                    <div class="col-4">
                        <a href="{{route('results.monthsByExamType', [$school, 'year' => $year, 'class' => $class, 'examType' => $examType, 'months' => $month])}}" class="float-right">
                            <i class="fas fa-arrow-circle-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="single-table">
                    <div class="table-responsive">
                        <table class="table table-hover progress-table" id="myTable">
                            <thead class="text-uppercase">
                                <tr class="text-center">
                                    <th>#</th>
                                    <th scope="col">Admission No</th>
                                    <th scope="col">First Name</th>
                                    <th scope="col">Middle Name</th>
                                    <th scope="col">Last Name</th>
                                    <th scope="col" class="text-center">Gender</th>
                                    <th>Phone</th>
                                    <th scope="col" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($studentsResults as $student)
                                    <tr class="text-center">
                                        <td>{{$loop->iteration}}</td>
                                        <td class="text-uppercase">{{$student->admission_number}}</td>
                                        <td class="text-uppercase">{{$student->first_name}}</td>
                                        <td class="text-uppercase">{{$student->middle_name}}</td>
                                        <td class="text-uppercase">{{$student->last_name}}</td>
                                        <td class="text-center text-uppercase">{{$student->gender[0]}}</td>
                                        <td>{{$student->phone}}</td>
                                        <td>
                                            <ul class="d-flex justify-content-center">
                                                <li class="mr-3">
                                                    <a href="{{route('download.individual.report', ['school' => $school, 'year' => $year, 'class' => $class, 'examType' => $examType, 'month' => $month, 'student' => $student->student_id])}}" target="_blank" class="btn btn-xs btn-success" onclick="return confirm('Are you sure you want to download report?')">
                                                        Report Preview
                                                    </a>
                                                </li>
                                                <li class="mr-3">
                                                    <form action="{{route('sms.results', ['school' => $school, 'year' => $year, 'class' => $class, 'examType' => $examType, 'month' => $month, 'student' => $student->student_id])}}" method="POST" role="form">
                                                        @csrf
                                                        <button class="btn btn-danger btn-xs" onclick="return confirm('Are you sure you want to Re-send SMS?')">Send SMS</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
