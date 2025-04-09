@extends('SRTDashboard.frame')

@section('content')
    <div class="row">
        <div class="col-md-4 mt-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title text-center text-uppercase">Subjects By Class</h4>
                    <p class="text-danger">Select class to view subjects</p>
                    @if ($classes->isEmpty())
                        <div class="alert alert-warning text-center">
                            <p>No Classes records found!</p>
                        </div>

                        @else
                        <ul class="list-group">
                            @foreach ($classes as $class)
                            <a href="{{route('courses.view.class', ['id' => Hashids::encode($class->id)])}}">
                                <li class="list-group-item text-primary align-items-center text-uppercase">
                                    <i class="ti-angle-double-right"></i> {{$class->class_name}}
                                </li>
                            </a>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-8 mt-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title text-center text-uppercase">All Registered Subjects</h4>
                    <div class="row">
                        <div class="col-8">
                            <p class="text-success">All available courses</p>
                        </div>
                        <div class="col-4">
                            <button type="button" class="btn btn-primary float-right btn-xs" data-toggle="modal" data-target=".bd-example-modal-lg"><i class="fas fa-plus"></i> New Subject
                            </button>
                            <div class="modal fade bd-example-modal-lg">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Register New Subject</h5>
                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                        </div>
                                        <div class="modal-body">
                                            <form class="needs-validation" novalidate="" action="{{route('course.registration')}}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="form-row">
                                                    <div class="col-md-6 mb-3">
                                                        <input type="hidden" name="school_id" value="{{Auth::user()->school_id}}">
                                                        <label for="validationCustom01">Subject Name</label>
                                                        <input type="text" required name="sname" class="form-control text-capitalize" id="validationCustom01" placeholder="Course Name" value="{{old('name')}}" required="">
                                                        @error('sname')
                                                        <div class="text-danger">
                                                            {{$message}}
                                                        </div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="validationCustom02">Code</label>
                                                        <input type="text" required name="scode" class="form-control text-uppercase" id="validationCustom02" placeholder="course Code" required="" value="{{old('code')}}" required>
                                                        @error('scode')
                                                        <div class="text-danger">
                                                           {{$message}}
                                                        </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                            <button type="submit" id="saveButton" class="btn btn-success">Save</button>
                                        </div>
                                    </div>
                                </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if ($subjects->isEmpty())
                        <div class="alert alert-warning text-center">
                            <p class="text-danger">No any course registered!</p>
                        </div>

                        @else
                        <table class="table table-responsive-md table-bordered table-hover" id="myTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Course Name</th>
                                    <th>Course Code</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($subjects as $course )
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td class="text-uppercase">{{$course->course_name}}</td>
                                        <td class="text-uppercase">{{$course->course_code}}</td>
                                        <td class="text-center">
                                            @if ($course->status == 1)
                                                    <span class="badge bg-success text-white">Active</span>
                                                @else
                                                    <span class="badge bg-danger text-white">Blocked</span>
                                            @endif
                                        </td>
                                        <td>
                                            <ul class="d-flex">
                                                @if ($course->status == 1)
                                                    <li class="mr-3">
                                                        <a href="{{route('course.edit', ['id' => Hashids::encode($course->id)])}}"><i class="fas fa-pencil text-primary"></i></a>
                                                    </li>
                                                    <li class="mr-3">
                                                        <form action="{{route('courses.block', ['id' => Hashids::encode($course->id)])}}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class=" btn btn-link p-0" onclick="return confirm('Are you sure you want to Block {{strtoupper($course->course_name)}} Course?')"><i class="ti-na text-danger"></i></button>
                                                        </form>
                                                    </li>
                                                @else
                                                    <li class="mr-3">
                                                        <form action="{{route('courses.unblock', ['id' => Hashids::encode($course->id)])}}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class=" btn btn-link p-0" onclick="return confirm('Are you sure you want to unblock {{strtoupper($course->course_name)}} Course?')"><i class="ti-reload text-success"></i></button>
                                                        </form>
                                                    </li>
                                                @endif
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.querySelector(".needs-validation");
            const submitButton = document.getElementById("saveButton"); // Tafuta button kwa ID

            if (!form || !submitButton) return; // Kama form au button haipo, acha script isifanye kazi

            form.addEventListener("submit", function (event) {
                event.preventDefault(); // Zuia submission ya haraka

                // Disable button na badilisha maandishi
                submitButton.disabled = true;
                submitButton.innerHTML = `<span class="spinner-border spinner-border-sm text-white" role="status" aria-hidden="true"></span> Please Wait...`;

                // Hakikisha form haina errors kabla ya kutuma
                if (!form.checkValidity()) {
                    form.classList.add("was-validated");
                    submitButton.disabled = false; // Warudishe button kama kuna errors
                    submitButton.innerHTML = "Save";
                    return;
                }

                // Chelewesha submission kidogo ili button ibadilike kwanza
                setTimeout(() => {
                    form.submit();
                }, 500);
            });
        });
    </script>
@endsection
