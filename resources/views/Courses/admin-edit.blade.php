@extends('SRTDashboard.frame')
    @section('content')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- Pakia Select2 CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<!-- Pakia Select2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<style>
    /* Override Select2 default styles to match Bootstrap form-control */
    .select2-container .select2-selection--single {
        height: 38px !important;  /* Ensure same height as form-control */
        border: 1px solid #ccc !important; /* Border to match Bootstrap */
        border-radius: 4px !important; /* Rounded corners to match Bootstrap */
        padding: 6px 12px !important; /* Padding to match form-control */
    }
    .select2-container {
    width: 100% !important; /* Ensure Select2 takes full width of the parent */
    }

    .select2-container {
        width: 100% !important; /* Set full width for Select2 container */
        max-width: 100% !important; /* Ensure it does not exceed container */
    }

    .select2-selection--single {
        width: 100% !important; /* Set width of the selection box */
    }
    .select2-selection--single {
        width: 100% !important; /* Ensure selection box inside Select2 also takes full width */
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #495057; /* Match the default text color */
        line-height: 26px; /* Align text */
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 30px; /* Arrow should be aligned */
    }

</style>
    <div class="card mt-5">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="header-title">Change Subject Teacher</h4>
                </div>
                <div class="col-md-4">
                    <a href="{{route('courses.view.class', ['id' => Hashids::encode($classCourse->class_id)])}}" class="btn btn-info btn-xs float-right"><i class="fas fa-arrow-circle-left"></i> Back</a>
                </div>
            </div>
            <hr>
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
                        <select name="teacher_id" id="selectTeacher" class="form-control select2 select2-enhanced text-capitalize">
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
        window.onload = function() {
            if (typeof $.fn.select2 !== 'undefined') {
                // Apply Select2 to all elements with class 'select2-enhanced'
                $('.select2-enhanced').select2({
                    placeholder: "Search...",
                    allowClear: true
                }).on('select2:open', function () {
                    $('.select2-results__option').css('text-transform', 'capitalize');
                });
            } else {
                console.error("Select2 haijapakiwa!");
            }
        };


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
