@extends('SRTDashboard.frame')
    @section('content')
    <div class="card mt-5">
        <div class="card-body">
            <h4 class="header-title">Change Subject Teacher</h4>
            <form class="needs-validation" novalidate="" action="{{route('courses.assigned.teacher', $courses->id)}}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom01">Course Name</label>
                        <input type="text" name="name" disabled class="form-control text-uppercase" id="validationCustom01" placeholder="course name" value="{{$courses->course_name}}" required="">
                        @error('name')
                        <div class="invalid-feedback">
                            {{$message}}
                        </div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom02">Course Code</label>
                        <input type="text" name="code" disabled class="form-control text-uppercase" id="validationCustom02" placeholder="Last name" required="" value="{{$courses->course_code}}">
                        @error('code')
                        <div class="invalid-feedback">
                           {{$message}}
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom01">Class</label>
                        <input type="text" name="class" disabled class="form-control text-uppercase" id="validationCustom01" value="{{$courses->class_name}}">
                        @error('class')
                        <div class="invalid-feedback">
                            {{$message}}
                        </div>
                         @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom01">Subject Teacher</label>
                        <select name="teacher" id="validationCustom01" class="form-control text-uppercase">
                            <option value="{{$courses->teacher_id}}" selected>{{$courses->first_name}} {{$courses->last_name}}</option>
                            @foreach ($teachers as $teacher)
                            <option value="{{$teacher->id}}">{{$teacher->first_name}} {{$teacher->last_name}}</option>
                            @endforeach
                        </select>
                        @error('class')
                        <div class="invalid-feedback">
                            {{$message}}
                        </div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">Assign</button>
            </form>
        </div>
    </div>

    @endsection
