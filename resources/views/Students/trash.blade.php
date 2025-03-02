
@extends('SRTDashboard.frame')
@section('content')
<div class="col-12">
</div>
    <div class="row">
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="header-title text-center text-uppercase">Deleted Account - Students</h4>
                        </div>
                        <div class="col-4">
                                <div class="btn-group float-right btn-xs" role="group" aria-label="Button group with nested dropdown">
                                    <div class="btn-group" role="group">
                                      <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Deleted Accounts
                                      </button>
                                      <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                        <a href="{{route('Teachers.trashed')}}" class="dropdown-item">Teachers</a>
                                        <a class="dropdown-item" href="{{route('students.trash')}}">Students</a>
                                        <a class="dropdown-item" href="#">Parents</a>
                                      </div>
                                    </div>
                                  </div>
                        </div>
                    </div>
                    <div class="single-table">
                        <div class="table-responsive">
                            <table class="table table-hover progress-table" id="myTable">
                                <thead class="text-uppercase">
                                    <tr>
                                        <th scope="col">Admission No</th>
                                        <th scope="col">Student Name</th>
                                        <th scope="col">Gender</th>
                                        <th scope="col">Class</th>
                                        <th scope="col">status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($students as $student )
                                        <tr>
                                            <td>{{$student->admission_number}}</td>
                                            <td class="text-capitalize">
                                                {{$student->first_name}} {{$student->middle_name}} {{$student->last_name}}
                                            </td>
                                            <td class="text-capitalize">{{$student->gender[0]}}</td>
                                            <td>{{$student->class_name}}</td>
                                            <td>
                                                @if ($student->status == 1)
                                                <span class="status-p bg-success">Active</span>
                                                @elseif ($student->status == 2)
                                                <span class="status-p bg-danger">Deleted</span>
                                                @else
                                                <span class="status-p bg-secondary">Blocked</span>
                                                @endif
                                            </td>
                                            <td>
                                                <ul class="d-flex">
                                                    <li class="mr-3">
                                                        <form action="{{route('student.restored.trash', ['student' => Hashids::encode($student->id)])}}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="btn btn-link p-0" onclick="return confirm('Do you want to restore this student account?')">
                                                                <i class="fas fa-refresh text-success"></i>
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <a href="{{route('student.delete.permanent', ['student' => Hashids::encode($student->id)])}}"><i class="fas fa-trash text-danger" onclick="return confirm('Do you want to delete this student account permanently?')"></i></a>
                                                    </li>
                                                </ul>
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
