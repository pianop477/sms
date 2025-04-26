@extends('SRTDashboard.frame')
@section('content')
<div class="card mt-5">
    <div class="card-body">
        <div class="row">
            <div class="col-10">
                <h4 class="header-title">Edit Students Information</h4>
            </div>
            <div class="col-2">
                <a href="{{route('home')}}" class=""><i class="fas fa-arrow-circle-left text-secondary" style="font-size: 2rem;"></i></a>
            </div>
        </div>
        <form class="needs-validation" novalidate="" action="{{route('parent.update.student', ['students' => Hashids::encode($students->id)])}}" method="POST" enctype="multipart/form-data">
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
                <div class="col-md-4 mb-3">
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
                <div class="col-md-4 mb-3">
                    <label for="validationCustom01">Stream</label>
                    {{-- <input type="text" name="group" class="form-control text-capitalize" id="validationCustom01" value="{{$students->group}}"> --}}
                    <select name="group" id="validationCustom02" required class="form-control">
                        <option value="{{$students->group}}" selected>Stream {{$students->group}}</option>
                        <option value="a">Stream A</option>
                        <option value="b">Stream B</option>
                        <option value="c">Stream C</option>
                    </select>
                    @error('group')
                    <div class="text-danger">{{$message}}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
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
