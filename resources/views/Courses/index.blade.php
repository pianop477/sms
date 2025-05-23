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
                            <h4 class="header-title text-uppercase">Courses List: <span class="" style="text-decoration: underline"><strong>{{$class->class_name}}</strong></span></h4>
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
                                                        <select name="course_id" id="parentSelect" class="form-control select2 select2-enhanced text-capitalize" required>
                                                            <option value="">--Select Course--</option>
                                                            @if ($courses->isEmpty())
                                                                <option value="" class="text-danger">{{_('No courses found')}}</option>
                                                            @else
                                                                @foreach ($courses as $course)
                                                                    <option value="{{$course->id}}">{{ucwords(strtolower($course->course_name))}}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        @error('course_id')
                                                        <div class="text-danger">
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
                                                        <div class="text-danger">
                                                            {{$message}}
                                                        </div>
                                                        @enderror
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustom01">Select Teacher</label>
                                                        <select name="teacher_id" id="selectTeacher" class="form-control select2 select2-enhanced text-capitalize" required>
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
                <hr>
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
                                <thead class="text-capitalize bg-info">
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
                                            <td class="text-uppercase">{{ $course->course_code }}</td>
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
                                                        <a href="{{route('courses.assign', ['id' => Hashids::encode($course->id)])}}"><i class="ti-pencil text-primary"></i></a>
                                                    </li>
                                                    <li class="mr-3">
                                                        <form action="{{route('block.assigned.course', ['id' => Hashids::encode($course->id)])}}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <button class="btn btn-link p-0"onclick="return confirm('Are you sure you want to block {{strtoupper($course->course_name)}} Course?')"><i class="ti-na text-info"></i></button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <a href="{{route('courses.delete', ['id' => Hashids::encode($course->id)])}}" onclick="return confirm('Are you sure you want to Delete {{strtoupper($course->course_name)}} Course permanently?')"><i class="ti-trash text-danger"></i></a>
                                                    </li>
                                                </ul>
                                                @else
                                                <ul class="d-flex justify-content-center">
                                                    <li class="mr-3">
                                                        <form action="{{route('unblock.assigned.course', ['id' => Hashids::encode($course->id)])}}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <button class="btn btn-link p-0" onclick="return confirm('Are you sure you want to unblock {{strtoupper($course->course_name)}} Course?')"><i class="ti-reload text-success"></i></button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <a href="{{route('courses.delete', ['id' => Hashids::encode($course->id)])}}" onclick="return confirm('Are you sure you want to Delete {{strtoupper($course->course_name)}} Course permanently?')"><i class="ti-trash text-danger"></i></a>
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
</div>
@endsection
