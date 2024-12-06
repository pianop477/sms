@extends('SRTDashboard.frame')

@section('content')
<div class="row">
    <div class="col-12 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-8">
                        <h4 class="header-title text-uppercase text-center">Deleted Teachers Accounts</h4>
                    </div>
                    <div class="col-4">
                        <div class="btn-group float-right btn-xs" role="group" aria-label="Button group with nested dropdown">
                            <div class="btn-group" role="group">
                              <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Deleted Account
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
                                    <th scope="col">#</th>
                                    <th scope="col">Teacher's Name</th>
                                    <th scope="col">Gender</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Joined</th>
                                    <th scope="col">status</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($teachers as $teacher )
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td class="text-capitalize">{{$teacher->first_name. ' '. $teacher->last_name}}</td>
                                        <td class="text-capitalize">{{$teacher->gender[0]}}</td>
                                        <td>{{$teacher->phone}}</td>
                                        <td>{{$teacher->email}}</td>
                                        <td>{{$teacher->joined}}</td>
                                        <td>
                                            @if ($teacher->status ==2)
                                                <span class="badge bg-danger text-white">{{_('Deleted')}}</span>
                                            @endif
                                        </td>
                                        @if ($teacher->status == 1)
                                        <td>
                                            <ul class="d-flex justify-content-center">
                                                <li class="mr-3"><a href="{{route('Teachers.show.profile', $teacher->id)}}" class="text-primary"><i class="fa fa-eye"></i></a></li>
                                                <li class="mr-3">
                                                    <form action="{{route('update.teacher.status', $teacher->id)}}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-link p-0" onclick="return confirm('Are you sure you want to Block {{strtoupper($teacher->first_name)}} {{strtoupper($teacher->last_name)}}?')"><i class="fas fa-ban text-danger"></i></button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </td>
                                        @else
                                        <td>
                                            <ul class="d-flex justify-content-center">
                                                <li class="mr-3">
                                                    <form action="{{route('teachers.restore', $teacher->id)}}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-link p-0" onclick="return confirm('Are you sure you want to Unblock {{strtoupper($teacher->first_name)}} {{strtoupper($teacher->last_name)}}?')"><i class="ti-reload text-success"></i></button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </td>
                                        @endif
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
