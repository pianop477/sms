@extends('SRTDashboard.frame')
    @section('content')
    <div class="card mt-5">
        <div class="card-body">
            <div class="row">
                <div class="col-10">
                    <h4 class="header-title text-uppercase">Edit/Change Class Teacher</h4>
                </div>
                <div class="col-2">
                    <a href="{{route('Class.Teachers', ['class' => Hashids::encode($classTeacher->class_id)])}}" class="btn btn-info btn-xs float-right"><i class="fas fa-arrow-circle-left" style=""></i> Back</a>
                </div>
            </div>
            <form class="needs-validation" novalidate="" action="{{route('roles.update.class.teacher', ['classTeacher' => Hashids::encode($classTeacher->id)])}}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom01">Class Name</label>
                        <input type="text" name="name" disabled class="form-control text-uppercase" id="validationCustom01" placeholder="course name" value="{{$classTeacher->class_name}}" required="">
                        @error('name')
                        <div class="text-danger">
                            {{$message}}
                        </div>
                        @enderror
                    </div>
                    <div class="col-md-2"></div>
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom02">Class Code</label>
                        <input type="text" name="code" disabled class="form-control text-uppercase" id="validationCustom02" placeholder="Last name" required="" value="{{$classTeacher->class_code}}">
                        @error('code')
                        <div class="text-danger">
                           {{$message}}
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom02">Stream</label>
                        <input type="text" disabled name="group" class="form-control text-uppercase" id="validationCustom02" placeholder="" required="" value="{{$classTeacher->group}}">
                        </select>
                        @error('group')
                        <div class="text-danger">
                           {{$message}}
                        </div>
                        @enderror
                    </div>
                    <div class="col-md-2"></div>
                    <div class="col-md-4 mb-3">
                        <label for="validationCustom01">Teacher's Name</label>
                        <select name="teacher" id="validationCustom01" class="form-control text-capitalize" required>
                            <option value="{{$classTeacher->teacher_id}}" selected>{{$classTeacher->first_name}} {{$classTeacher->last_name}}</option>
                            @if ($teachers->isEmpty())
                                <option value="" class="text-danger">No teachers found</option>
                            @else
                                @foreach ($teachers as $teacher )
                                    <option value="{{$teacher->id}}">{{$teacher->first_name}} {{$teacher->last_name}}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('teacher')
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
