@extends('SRTDashboard.frame')

@section('content')
<div class="row">
    <div class="col-12 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-8">
                        <h4 class="header-title text-uppercase">Class Teacher For {{ $classes->class_name}}</h4>
                    </div>
                    <div class="col-2">
                        <a href="{{route('Classes.index', $classes->id)}}" class=""><i class="fas fa-arrow-circle-left text-secondary" style="font-size: 2rem;"></i></a>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-link p-0" data-toggle="modal" data-target=".bd-example-modal-lg"><i class="fas fa-user-plus text-secondary" style="font-size: 2rem;"></i>
                        </button>
                        <div class="modal fade bd-example-modal-lg">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Assign Class Teacher</h5>
                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <form class="needs-validation" novalidate="" action="{{route('Class.teacher.assign', $classes->id)}}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-row">
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustom01">Teacher's Name</label>
                                                    <select name="teacher" id="" class="form-control text-capitalize">
                                                        <option value="">-- Select Class Teacher --</option>
                                                        @if ($teachers->isEmpty())
                                                            <option value="" class="text-danger">No teachers found</option>
                                                        @else
                                                            @foreach ($teachers as $teacher)
                                                                <option value="{{$teacher->id}}">{{$teacher->teacher_first_name. ' '. $teacher->teacher_last_name}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    @error('fname')
                                                    <div class="invalid-feedback">
                                                        {{$message}}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustom02">Class Group</label>
                                                    <input type="text" name="group" class="form-control text-uppercase" id="validationCustom02" placeholder="Enter Class Group A, B or C" required="" value="{{old('lname')}}">
                                                    @error('lname')
                                                    <div class="invalid-feedback">
                                                       {{$message}}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-success">Assign</button>
                                    </div>
                                </div>
                            </form>
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
                                    <th scope="col">Class Name</th>
                                    <th scope="col">Class Group</th>
                                    <th scope="col">Teacher Name</th>
                                    <th scope="col" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($classTeacher as $teacher )
                                    <tr class="text-capitalize">
                                        <td>{{$loop->iteration}}</td>
                                        <td class="text-capitalize">{{$teacher->class_name}}</td>
                                        <td class="text-capitalize text-center">{{$teacher->group}}</td>
                                        <td>{{$teacher->teacher_first_name. ' '. $teacher->teacher_last_name}}</td>
                                        <td>
                                            <ul class="d-flex justify-content-center">
                                                <li class="mr-3">
                                                    <a href="{{route('roles.edit', $teacher->id)}}"><i class="ti-pencil"></i></a>
                                                </li>
                                                <li>
                                                    <a href="{{route('roles.destroy', $teacher->id)}}" onclick="return confirm('Are you sure you want to remove {{ strtoupper($teacher->teacher_first_name) }} {{ strtoupper($teacher->teacher_last_name) }} from this class?')">
                                                        <i class="ti-trash text-danger"></i>
                                                    </a>
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
