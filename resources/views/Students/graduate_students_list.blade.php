@extends('SRTDashboard.frame')

@section('content')
<div class="row">
    <div class="col-12 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-8">
                        <h4 class="header-title text-uppercase">Graduated Students - {{$year}}</h4>
                    </div>
                    @if ($GraduatedStudents->isNotEmpty())
                        <div class="col-2">
                            <a href="{{route('graduate.students.export', ['year' => $year])}}" target="_blank" class="float-right btn btn-primary btn-xs"><i class="fas fa-cloud-arrow-down"></i> Export</a>
                        </div>
                        <div class="col-2">
                            <a href="{{route('graduate.students')}}" class="float-right btn btn-info btn-xs">
                                <i class="fas fa-chevron-left"></i> Back
                            </a>
                        </div>
                    @endif
                </div>
                <div class="single-table">
                    <div class="table-responsive">
                        <table class="table table-hover progress-table" id="myTable">
                            <thead class="text-uppercase">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col" class="text-center">Admission No.</th>
                                    <th scope="col">First Name</th>
                                    <th scope="col">Middle Name</th>
                                    <th scope="col">Last Name</th>
                                    <th scope="col" class="text-center">Gender</th>
                                    <th scope="col" class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($GraduatedStudents as $student )
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td class="text-uppercase text-center">{{$student->school_reg_no}}/{{$student->admission_number}}</td>
                                        <td class="text-uppercase">{{$student->first_name}}</td>
                                        <td class="text-uppercase">{{$student->middle_name}}</td>
                                        <td class="text-uppercase">{{$student->last_name}}</td>
                                        <td class="text-center text-uppercase">{{$student->gender[0]}}</td>
                                        <td class="text-center text-capitalize">
                                            <span class="badge bg-danger text-white">{{_('Graduated')}}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
