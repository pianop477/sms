@extends('SRTDashboard.frame')
    @section('content')
    <div class="card mt-5">
        <div class="card-body">
            <div class="row">
                <div class="col-10">
                    <h4 class="header-title">Edit Course Details</h4>
                </div>
                <div class="col-2">
                    <a href="{{route('courses.index')}}"><i class="fas fa-arrow-circle-left"></i> Back</a>
                </div>
            </div>
            <form class="needs-validation" novalidate="" action="{{route('course.update', $course->id)}}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom01">Course Name</label>
                        <input type="text" required name="sname" class="form-control text-uppercase" id="validationCustom01" value="{{$course->course_name}}" required="">
                        @error('name')
                        <div class="invalid-feedback">
                            {{$message}}
                        </div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom01">Course Code</label>
                        <input type="text" required name="scode" class="form-control text-uppercase" id="validationCustom01" placeholder="" value="{{$course->course_code}}" required="">
                        @error('name')
                        <div class="invalid-feedback">
                            {{$message}}
                        </div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-success" type="submit">Save Changes</button>
            </form>
        </div>
    </div>
    @endsection
