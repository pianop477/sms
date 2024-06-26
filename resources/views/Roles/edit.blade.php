@extends('SRTDashboard.frame')
    @section('content')
    <div class="card mt-5">
        <div class="card-body">
            <div class="row">
                <div class="col-10">
                    <h4 class="header-title">Change Class Teacher</h4>
                </div>
                <div class="col-2">
                    <a href="{{route('Class.Teachers', $classTeacher->class_id)}}"><i class="fas fa-arrow-circle-left text-secondary" style="font-size: 2rem;"></i></a>
                </div>
            </div>
            <form class="needs-validation" novalidate="" action="{{route('roles.update', $classTeacher->id)}}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom01">Class Name</label>
                        <input type="text" name="name" disabled class="form-control text-uppercase" id="validationCustom01" placeholder="course name" value="{{$classTeacher->class_name}}" required="">
                        @error('name')
                        <div class="invalid-feedback">
                            {{$message}}
                        </div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom02">Class Code</label>
                        <input type="text" name="code" disabled class="form-control text-uppercase" id="validationCustom02" placeholder="Last name" required="" value="{{$classTeacher->class_code}}">
                        @error('code')
                        <div class="invalid-feedback">
                           {{$message}}
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom02">Class Group</label>
                        <input type="text" disabled name="group" class="form-control text-uppercase" id="validationCustom02" placeholder="" required="" value="{{$classTeacher->group}}">
                        @error('group')
                        <div class="invalid-feedback">
                           {{$message}}
                        </div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom01">Teacher's Name</label>
                        <select name="teacher" id="validationCustom01" class="form-control text-uppercase">
                            <option value="{{$classTeacher->teacher_id}}" selected>{{$classTeacher->first_name}} {{$classTeacher->last_name}}</option>
                            @foreach ($teachers as $teacher )
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
                <button class="btn btn-primary" type="submit">Save</button>
            </form>
        </div>
    </div>

    @endsection
