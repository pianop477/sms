@extends('SRTDashboard.frame')
    @section('content')
    <div class="card mt-5">
        <div class="card-body">
            <h4 class="header-title">Change Subject Teacher</h4>
            <form class="needs-validation" novalidate="" action="{{route('courses.assigned.teacher', $classCourse->id)}}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom01">Course Name</label>
                        <input type="text" name="course_id" disabled class="form-control text-uppercase" id="validationCustom01" placeholder="course name" value="{{$classCourse->course_name}}" required="">
                        @error('name')
                        <div class="invalid-feedback">
                            {{$message}}
                        </div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom02">Class</label>
                        <input type="text" name="class_id" disabled class="form-control text-uppercase" id="validationCustom02" placeholder="Last name" required="" value="{{$classCourse->class_name}}">
                        @error('code')
                        <div class="invalid-feedback">
                           {{$message}}
                        </div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom01">Subject Teacher</label>
                        <select name="teacher_id" id="validationCustom01" class="form-control text-uppercase">
                            <option value="{{$classCourse->teacherId}}" selected>{{$classCourse->first_name}} {{$classCourse->last_name}}</option>
                            @if ($teachers->isEmpty())
                                <option value="" class="text-danger">No Teachers found</option>
                            @else
                                @foreach ($teachers as $teacher)
                                    <option value="{{$teacher->id}}">{{$teacher->first_name}} {{$teacher->last_name}}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('class')
                        <div class="invalid-feedback">
                            {{$message}}
                        </div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-success" type="submit">Save changes</button>
            </form>
        </div>
    </div>

    @endsection
