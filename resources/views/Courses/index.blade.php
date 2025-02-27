@extends('SRTDashboard.frame')
@section('content')
<div class="row">
    <!-- table primary start -->
    <div class="col-lg-12 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-8">
                        @if (isset($message))
                            <h4 class="header-title">{{ $message }}</h4>
                        @else
                            <h4 class="header-title">Courses List for <span class="text-uppercase"><strong>{{$class->class_name}}</strong></span></h4>
                        @endif
                    </div>
                    <div class="col-2">
                        <a href="{{route('courses.index')}}" class="btn btn-info text-white btn-xs"><i class="fas fa-circle-arrow-left"></i> Back</a>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-xs btn-primary float-right" data-toggle="modal" data-target=".bd-example-modal-lg"><i class="fas fa-plus"></i> Assign
                        </button>
                        <div class="modal fade bd-example-modal-lg">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Assign Teaching Subject</h5>
                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <form class="needs-validation" novalidate="" action="{{route('course.assign')}}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-row">
                                                <div class="col-md-4 mb-3">
                                                    <input type="hidden" name="school_id" value="{{Auth::user()->school_id}}">
                                                    <label for="validationCustom01">Select Subject</label>
                                                        <select name="course_id" id="validationCustom01" class="form-control text-capitalize" required>
                                                            <option value="">--Select Course--</option>
                                                            @if ($courses->isEmpty())
                                                                <option value="" class="text-danger">{{_('No courses found')}}</option>
                                                            @else
                                                                @foreach ($courses as $course)
                                                                    <option value="{{$course->id}}">{{$course->course_name}}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        @error('course_id')
                                                        <div class="invalid-feedback">
                                                            {{$message}}
                                                        </div>
                                                        @enderror
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <input type="hidden" name="school_id" value="{{Auth::user()->school_id}}">
                                                    <label for="validationCustom01">Select Class</label>
                                                        <select name="class_id" id="validationCustom01" class="form-control text-uppercase" required>
                                                            <option value="{{$class->id}}" selected class="text-uppercase">{{$class->class_name}}</option>
                                                        </select>
                                                        @error('class_id')
                                                        <div class="invalid-feedback">
                                                            {{$message}}
                                                        </div>
                                                        @enderror
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustom01">Select Teacher</label>
                                                        <select name="teacher_id" id="validationCustom01" class="form-control text-capitalize" required>
                                                            <option value="">--Select Teacher--</option>
                                                            @if ($teachers->isEmpty())
                                                                <option value="" class="text-danger">{{_('No teachers found')}}</option>
                                                            @else
                                                                @foreach ($teachers as $teacher )
                                                                    <option value="{{$teacher->id}}">{{$teacher->first_name}} {{$teacher->last_name}}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        @error('teacher_id')
                                                        <div class="invalid-feedback">
                                                            {{$message}}
                                                        </div>
                                                        @enderror
                                                </div>
                                            </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-success">Save</button>
                                    </div>
                                </div>
                            </form>
                            </div>
                        </div>
                    </div>
                </div>
                @if (isset($message))
                    <div class="alert alert-warning" role="alert">
                        <h6 class="text-center">{{ $message }}</h6>
                    </div>
                @elseif ($classCourse->isEmpty())
                    <div class="alert alert-warning" role="alert">
                        <h6 class="text-center">No courses assigned for this class</h6>
                    </div>
                @else
                    <div class="single-table">
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="text-uppercase bg-info">
                                    <tr class="text-white">
                                        <th scope="col">#</th>
                                        <th scope="col">Course Name</th>
                                        <th scope="col">Course Code</th>
                                        <th scope="col">Subject Teacher</th>
                                        <th scope="col">Status</th>
                                        <th scope="col" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($classCourse as $course)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td class="text-capitalize">{{ $course->course_name }}</td>
                                            <td class="text-uppercase text-center">{{ $course->course_code }}</td>
                                            <td class="text-capitalize">{{ $course->first_name }} {{ $course->last_name }}</td>
                                            <td>
                                                @if ($course->status == 1)
                                                    <span class="badge bg-success text-white">{{ __('Active') }}</span>
                                                @else
                                                    <span class="badge bg-danger text-white">{{ __('Blocked') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($course->status == 1)
                                                <ul class="d-flex justify-content-center">
                                                    <li class="mr-3">
                                                        <a href="{{route('courses.assign', $course->id)}}"><i class="ti-pencil text-primary"></i></a>
                                                    </li>
                                                    <li class="mr-3">
                                                        <form action="{{route('block.assigned.course', $course->id)}}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <button class="btn btn-link p-0"onclick="return confirm('Are you sure you want to block {{strtoupper($course->course_name)}} Course?')"><i class="ti-na text-info"></i></button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <a href="{{route('courses.delete', $course->id)}}" onclick="return confirm('Are you sure you want to Delete {{strtoupper($course->course_name)}} Course permanently?')"><i class="ti-trash text-danger"></i></a>
                                                    </li>
                                                </ul>
                                                @else
                                                <ul class="d-flex justify-content-center">
                                                    <li class="mr-3">
                                                        <form action="{{route('unblock.assigned.course', $course->id)}}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <button class="btn btn-link p-0" onclick="return confirm('Are you sure you want to unblock {{strtoupper($course->course_name)}} Course?')"><i class="ti-reload text-success"></i></button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <a href="{{route('courses.delete', $course->id)}}" onclick="return confirm('Are you sure you want to Delete {{strtoupper($course->course_name)}} Course permanently?')"><i class="ti-trash text-danger"></i></a>
                                                    </li>
                                                </ul>
                                                @endif
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
    <!-- table primary end -->
</div>
@endsection
