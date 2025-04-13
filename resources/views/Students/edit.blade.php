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
            <div class="col-10">
                <h4 class="header-title">Edit Students Information</h4>
            </div>
            <div class="col-2">
                <a href="{{route('create.selected.class', ['class' => Hashids::encode($students->grade_class_id)])}}" class=""><i class="fas fa-arrow-circle-left text-secondary" style="font-size: 2rem;"></i></a>
            </div>
        </div>
        <form class="needs-validation" novalidate="" action="{{route('students.update.records', ['students' => Hashids::encode($students->id)])}}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-row">
                <div class="col-md-4">
                    <div class="avatar position-relative">
                        @if (!empty($students->image))
                            <img src="{{ asset('assets/img/students/' . $students->image) }}" alt="profile_image" class="shadow-sm" style="max-width: 150px; object-fit:cover; border-radius:50px;">
                        @else
                            <i class="fas fa-user-graduate text-secondary" style="font-size: 8rem;"></i>
                        @endif
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">First Name</label>
                    <input type="text" name="fname" class="form-control text-capitalize" id="validationCustom01" value="{{$students->first_name}}" required>
                    @error('fname')
                        <div class="text-danger">{{$message}}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">Middle Name</label>
                    <input type="text" name="middle" class="form-control text-capitalize" id="validationCustom01" value="{{$students->middle_name}}" required>
                    @error('middle')
                        <div class="text-danger">{{$message}}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">Last Name</label>
                    <input type="text" name="lname" class="form-control text-capitalize" id="validationCustom01" value="{{$students->last_name}}" required>
                    @error('lname')
                        <div class="text-danger">{{$message}}</div>
                    @enderror
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">Gender</label>
                    <select name="gender" id="" class='form-control text-capitalize'>
                        <option value="{{$students->gender}}">{{$students->gender}}</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                    @error('gender')
                        <div class="text-danger">{{$message}}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">Date of Birth</label>
                    <input type="date" name="dob" class="form-control" id="validationCustom01" value="{{$students->dob}}" required min="{{\Carbon\Carbon::now()->subYears(17)->format('Y-m-d')}}" max="{{\Carbon\Carbon::now()->subYears(2)->format('Y-m-d')}}">
                    @error('dob')
                        <div class="text-danger">{{$message}}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">Select Bus Number: <span class="text-danger">if using/change school bus</span></label>
                    <select name="driver" id="validationCustom01" class="form-control text-capitalize">
                        <option value="">--Home Alone--</option>
                        @if ($students->transport == NULL)
                            <option value="">-- Select Bus Number --</option>
                               @if ($buses->isEmpty())
                                   <option value="" class="text-danger">No buses found</option>
                               @else
                                @foreach ($buses as $bus )
                                    <option value="{{$bus->id}}">bus {{$bus->bus_no}}</option>
                                @endforeach
                               @endif
                        @else
                            <option value="{{$students->transport_id}}" selected>bus {{$students->bus_no}}</option>
                            @if ($buses->isEmpty())
                                <option value="" class="text-danger">No buses found</option>
                            @else
                                @foreach ($buses as $bus)
                                    <option value="{{$bus->id}}">bus {{$bus->bus_no}}</option>
                                @endforeach
                            @endif
                        @endif
                    </select>
                    @error('driver')
                    <div class="text-danger">{{$message}}</div>
                    @enderror
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-3 mb-3">
                    <label for="validationCustomUsername">Parent/Guardian Name</label>
                        <select name="parent" id="parentSelect" class="form-control select2 text-capitalize" required>
                            <option value="{{$parents->parent_id}}" selected>{{ucwords(strtolower($parents->first_name))}} {{ucwords(strtolower($parents->last_name))}}</option>
                            @foreach ($allParents as $parent)
                                <option value="{{$parent->parent_id}}">
                                    {{ucwords(strtolower($parent->first_name))}} {{ucwords(strtolower($parent->last_name))}}
                                </option>
                            @endforeach
                        </select>
                        @error('parent')
                        <div class="invalid-feedback">
                            {{$message}}
                        </div>
                        @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label for="validationCustom01">Class</label>
                    <select name="class" id="validationCustom01" class="form-control text-uppercase" required>
                        <option value="{{$students->class_id}}">{{$students->class_name}}</option>
                        @if ($classes->isEmpty())
                            <option value="" class="text-danger">No classes found</option>
                        @else
                            @foreach ($classes as $class)
                                <option value="{{$class->id}}">{{$class->class_name}}</option>
                            @endforeach
                        @endif
                    </select>
                    @error('class')
                    <div class="text-danger">{{$message}}</div>
                    @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label for="validationCustom01">Stream</label>
                    {{-- <input type="text" name="group" class="form-control text-capitalize" id="validationCustom01" value="{{$students->group}}"> --}}
                    <select name="group" id="validationCustom02" required class="form-control">
                        <option value="{{$students->group}}" selected>{{$students->group}}</option>
                        <option value="a">A</option>
                        <option value="b">B</option>
                        <option value="c">C</option>
                    </select>
                    @error('group')
                    <div class="text-danger">{{$message}}</div>
                    @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label for="validationCustom01">Photo <span class="text-sm text-danger">Maximum 1MB - Blue background</span></label>
                    <input type="file" name="image" class="form-control text-capitalize" id="validationCustom01" value="{{old('image')}}">
                    @error('image')
                    <div class="text-danger">{{$message}}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <button class="btn btn-success" id="saveButton" type="submit">Save Changes</button>
            </div>

            <script>
                window.onload = function() {
                    // Hakikisha jQuery na Select2 inapatikana
                    if (typeof $.fn.select2 !== 'undefined') {
                        // Fanya initialization ya Select2
                        $('#parentSelect').select2({
                            placeholder: "Search Parent...",
                            allowClear: true
                        }).on('select2:open', function () {
                            $('.select2-results__option').css('text-transform', 'capitalize');  // Capitalize option text
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
