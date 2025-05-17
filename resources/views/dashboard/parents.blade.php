@extends('SRTDashboard.frame')

@section('content')
<div class="col-lg-12">
    <div class="row">
        <div class="col-md-4 mt-md-5 mb-3">
            <div class="card" style="background: #c84fe0">
                <div class="">
                    <div class="p-4 d-flex justify-content-between align-items-center">
                        <div class="seofct-icon"><i class="fas fa-user-graduate"></i> My Children</div>
                        <h2 class="text-white">{{count($students)}}</h2>
                    </div>
                    <canvas id="seolinechart2" height="50"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-8 mt-md-5 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="header-title text-uppercase">My children list</h4>
                        </div>
                        <div class="col-4">
                            <button type="button" class="btn btn-xs btn-info float-right" data-toggle="modal" data-target=".bd-example-modal-lg">
                                <i class="fas fa-plus"></i> New Student
                            </button>
                            <div class="modal fade bd-example-modal-lg">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title text-uppercase">Student Registration Form</h5>
                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                        </div>
                                        <div class="modal-body">
                                            <p class="text-danger text-center text-capitalize">Please fill the form with valid information</p>
                                            <hr>
                                            <form class="needs-validation" novalidate="" action="{{route('register.student')}}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="form-row">
                                                    <div class="col-md-4 mb-3">
                                                        <label for="validationCustom01">First Name</label>
                                                        <input type="text" name="fname" class="form-control" id="validationCustom01" placeholder="" value="{{old('fname')}}" required="">
                                                        @error('fname')
                                                        <div class="text-danger">{{$message}}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="validationCustom02">Middle Name</label>
                                                        <input type="text" name="middle" class="form-control" id="validationCustom02" placeholder="" required="" value="{{old('middle')}}">
                                                        @error('middle')
                                                        <div class="text-danger">{{$message}}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="validationCustom02">Last Name</label>
                                                        <input type="text" name="lname" class="form-control" id="validationCustom02" placeholder="" required="" value="{{old('lname')}}">
                                                        @error('lname')
                                                        <div class="text-danger">{{$message}}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-row">
                                                    <div class="col-md-4 mb-3">
                                                        <label for="validationCustom01">Gender</label>
                                                        <select name="gender" id="validationCustom01" class="form-control text-capitalize" required>
                                                            <option value="">-- Select Gender --</option>
                                                            <option value="male">Male</option>
                                                            <option value="female">Female</option>
                                                        </select>
                                                        @error('gender')
                                                        <div class="text-danger">{{$message}}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="validationCustom02">Date of Birth</label>
                                                        <input type="date" name="dob" class="form-control" id="validationCustom02" placeholder="" required="" value="{{old('dob')}}" min="{{\Carbon\Carbon::now()->subYears(17)->format('Y-m-d')}}" max="{{\Carbon\Carbon::now()->subYears(2)->format('Y-m-d')}}">
                                                        @error('dob')
                                                        <div class="text-danger">{{$message}}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="validationCustomUsername">Class</label>
                                                        <div class="input-group">
                                                            <select name="grade" id="" class="form-control text-uppercase" required>
                                                                <option value="">--Select Class--</option>
                                                                @if ($classes->isEmpty())
                                                                    <option value="" class="text-danger">No classes found</option>
                                                                @else
                                                                    @foreach ($classes as $class )
                                                                        <option value="{{$class->id}}">{{$class->class_name}}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                            @error('grade')
                                                            <div class="text-danger">{{$message}}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-row">
                                                    <div class="col-md-4 mb-3">
                                                        <label for="validationCustom01">Stream</label>
                                                        {{-- <input type="text" name="group" id="validationCustomUsername" class="form-control" placeholder="Enter A, B or C" id="validationCustom02" value="{{old('group')}}" required> --}}
                                                        <select name="group" id="validationCustom02" required class="form-control">
                                                            <option value="">--Select Stream--</option>
                                                            <option value="a">Stream A</option>
                                                            <option value="b">Stream B</option>
                                                            <option value="c">Stream C</option>
                                                        </select>
                                                        @error('group')
                                                        <div class="text-danger">{{$message}}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="validationCustomUsername">School Bus Number: <small class="text-sm text-danger">select if using school bus</small></label>
                                                        <div class="input-group">
                                                            <select name="driver" id="" class="form-control text-capitalize">
                                                                <option value="">--Select School Bus Number--</option>
                                                                @if ($buses->isEmpty())
                                                                    <option value="" class="text-danger">There is no school bus records</option>
                                                                @else
                                                                    @foreach ($buses as $bus )
                                                                        <option value="{{$bus->id}}">Bus No. {{$bus->bus_no}}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                            @error('driver')
                                                            <div class="text-danger">{{$message}}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="validationCustomUsername">Photo :<small class="text-sm text-danger"> (Optional) - with blue background</small></label>
                                                        <div class="input-group">
                                                            <input type="file" name="image" id="validationCustomUsername" class="form-control" value="{{old('image')}}">
                                                            @error('image')
                                                            <div class="text-danger">{{$message}}</div>
                                                            @enderror
                                                        </div>
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
                    {{-- table for students lies here --}}
                    <div class="single-table">
                        <div class="table-responsive">
                            <table class="table" id="">
                                <thead class="text-capitalize">
                                    <tr class="">
                                        <th scope="col">Student Name</th>
                                        <th scope="col" style="">Class</th>
                                        <th scope="col" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($students as $student )
                                        <tr>
                                            <td class="text-uppercase">
                                                {{-- <a href="{{route('parent.show.student', ['student' => Hashids::encode($student->id)])}}"></a> --}}
                                                {{$student->first_name. ' '.$student->middle_name.' ' .$student->last_name}}
                                            </td>
                                            <td class="text-uppercase">{{$student->class_code}} {{$student->group}}</td>
                                            <td>
                                                <ul class="d-flex justify-content-center">
                                                    <a href="{{route('students.profile', ['student' => Hashids::encode($student->id)])}}" class="btn btn-xs btn-success">Manage</a>
                                                </ul>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<div class="col-lg-12">
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

<hr class="dark horizontal py-0">
@endsection
