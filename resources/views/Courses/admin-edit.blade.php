@extends('SRTDashboard.frame')
    @section('content')
    <div class="card mt-5">
        <div class="card-body">
            <h4 class="header-title">Change Subject Teacher</h4>
            <form class="needs-validation" novalidate="" action="{{route('courses.assigned.teacher', ['id' => Hashids::encode($classCourse->id)])}}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom01">Course Name</label>
                        <input type="text" name="course_id" disabled class="form-control text-uppercase" id="validationCustom01" placeholder="course name" value="{{$classCourse->course_name}}" required="">
                        @error('course_id')
                        <div class="text-danger">
                            {{$message}}
                        </div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom02">Class</label>
                        <input type="text" name="class_id" disabled class="form-control text-uppercase" id="validationCustom02" placeholder="Last name" required="" value="{{$classCourse->class_name}}">
                        @error('class_id')
                        <div class="text-danger">
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
                        @error('teacher_id')
                        <div class="text-danger">
                            {{$message}}
                        </div>
                        @enderror
                    </div>
                </div>
                <button class="btn btn-success" id="saveButton" type="submit">Save changes</button>
            </form>
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
                    submitButton.innerHTML = "Save changes";
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
