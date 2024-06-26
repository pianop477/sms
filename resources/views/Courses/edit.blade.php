@extends('SRTDashboard.frame')
    @section('content')
    <div class="card mt-5">
        <div class="card-body">
            <h4 class="header-title">Edit Course Information</h4>
            <form class="needs-validation" novalidate="" action="{{route('courses.update', $courses->id)}}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom01">Course Name</label>
                        <input type="text" name="name" class="form-control text-uppercase" id="validationCustom01" placeholder="course name" value="{{$courses->course_name}}" required="">
                        @error('name')
                        <div class="invalid-feedback">
                            {{$message}}
                        </div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom02">Course Code</label>
                        <input type="text" name="code" class="form-control text-uppercase" id="validationCustom02" placeholder="Last name" required="" value="{{$courses->course_code}}">
                        @error('code')
                        <div class="invalid-feedback">
                           {{$message}}
                        </div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom01">Class</label>
                        <input type="text" disabled name="class" class="form-control text-uppercase" value="{{$courses->class_name}}">
                        @error('class')
                        <div class="invalid-feedback">
                            {{$message}}
                        </div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">Update</button>
            </form>
        </div>
    </div>

    @endsection
