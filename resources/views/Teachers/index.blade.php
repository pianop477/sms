@extends('SRTDashboard.frame')

@section('content')
<div class="row">
    <div class="col-12 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-8">
                        <h4 class="header-title text-capitalize">Registered Teachers</h4>
                    </div>
                    <div class="col-2 mt-2">
                        <div class="btn-group" role="group">
                            <button id="btnGroupDrop" type="button" class="btn btn-primary btn-xs dropdown-toggle float-right" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-cloud-arrow-down"></i> Export
                            </button>
                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                <a class="dropdown-item" href="{{route('teachers.excel.export')}}" target=""><i class="fas fa-file-excel"></i> Excel</a>
                                <a class="dropdown-item" href="{{route('teachers.pdf.export')}}" target="_blank"><i class="fas fa-file-pdf"></i> pdf</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-xs btn-info float-right" data-toggle="modal" data-target=".bd-example-modal-lg">
                            <i class="fas fa-user-plus"></i> New Teacher
                        </button>
                        <div class="modal fade bd-example-modal-lg">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Teachers Registration Form</h5>
                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <form class="needs-validation" novalidate="" action="{{route('Teachers.store')}}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-row">
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustom01">First name</label>
                                                    <input type="text" required name="fname" class="form-control" id="validationCustom01" placeholder="First name" value="{{old('fname')}}" required="">
                                                    @error('fname')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustom02">Other names</label>
                                                    <input type="text" required name="lname" class="form-control" id="validationCustom02" placeholder="Middle & Last name" required="" value="{{old('lname')}}">
                                                    @error('lname')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustomUsername">Email</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text" id="inputGroupPrepend">@</span>
                                                        </div>
                                                        <input type="email" name="email" class="form-control" id="validationCustomUsername" placeholder="Email ID" aria-describedby="inputGroupPrepend" value="{{old('email')}}">
                                                        @error('email')
                                                        <div class="text-danger">{{$message}}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustom01">Gender</label>
                                                    <select name="gender" id="validationCustom01" class="form-control text-capitalize" required>
                                                        <option value="">-- select gender --</option>
                                                        <option value="male">male</option>
                                                        <option value="female">female</option>
                                                    </select>
                                                    @error('gender')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustom02">Mobile Phone</label>
                                                    <input type="text" required name="phone" class="form-control" id="validationCustom02" placeholder="Phone Number" required="" value="{{old('phone')}}">
                                                    @error('phone')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustomUsername">Qualification</label>
                                                    <div class="input-group">
                                                        <select name="qualification" id="validationCustomUsername" class="form-control text-uppercase" required>
                                                            <option value="">-- Select Qualification --</option>
                                                            <option value="1">Masters</option>
                                                            <option value="2">Degree</option>
                                                            <option value="3">Diploma</option>
                                                            <option value="4">Certificate</option>
                                                        </select>
                                                        @error('school')
                                                        <div class="text-danger">{{$message}}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustom01">Date of Birth</label>
                                                    <input type="date" required name="dob" class="form-control" id="validationCustom02" value="{{old('dob')}}" required min="{{\Carbon\Carbon::now()->subYears(60)->format('Y-m-d')}}" max="{{\Carbon\Carbon::now()->subYears(18)->format('Y-m-d')}}">
                                                    @error('dob')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustom01">Year Joined</label>
                                                    <select name="joined" id="" class="form-control" required>
                                                        <option value="">-- Select Year --</option>
                                                        @for ($year = date('Y'); $year >= 2010; $year--)
                                                            <option value="{{ $year }}">{{ $year }}</option>
                                                        @endfor
                                                    </select>
                                                    @error('joined')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustomUsername">Street/Village</label>
                                                    <div class="input-group">
                                                        <input type="text" required name="street" class="form-control" id="validationCustom02" value="{{old('street')}}" placeholder="Street Address" required>
                                                        @error('street')
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
                <div class="single-table">
                    <div class="table-responsive">
                        <table class="table table-hover progress-table" id="myTable">
                            <thead class="text-capitalize">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Member#</th>
                                    <th scope="col">Teacher's Name</th>
                                    <th scope="col">Gender</th>
                                    <th scope="col">role</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Joined</th>
                                    <th scope="col">status</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($teachers as $teacher )
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{strtoupper($teacher->member_id)}}</td>
                                        <td class="d-flex align-items-center">
                                            @php
                                                // Determine the image path
                                                $imageName = $teacher->image;
                                                $imagePath = public_path('assets/img/profile/' . $imageName);

                                                // Check if the image exists and is not empty
                                                if (!empty($imageName) && file_exists($imagePath)) {
                                                    $avatarImage = asset('assets/img/profile/' . $imageName);
                                                } else {
                                                    // Use default avatar based on gender
                                                    $avatarImage = asset('assets/img/profile/' . ($teacher->gender == 'male' ? 'avatar.jpg' : 'avatar-female.jpg'));
                                                }
                                            @endphp
                                            <img src="{{ $avatarImage }}" alt="" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                                            <span class="text-capitalize ms-2" style="margin-left: 5px"> {{ucwords(strtolower($teacher->first_name. ' '. $teacher->last_name))}}</span>
                                        </td>
                                        <td class="text-capitalize">{{$teacher->gender[0]}}</td>
                                        <td class="text-capitalize text-white">
                                            @if ($teacher->role_id == 1)
                                                <span class="badge bg-danger">{{$teacher->role_name}}</span>
                                            @elseif ($teacher->role_id == 3)
                                                <span class="badge bg-info">{{$teacher->role_name}}</span>
                                            @else
                                                <span class="badge bg-success">{{$teacher->role_name}}</span>
                                            @endif
                                        </td>
                                        <td>{{$teacher->phone}}</td>
                                        <td>{{$teacher->joined}}</td>
                                        <td>
                                            @if ($teacher->status ==1)
                                                <span class="badge bg-success text-white">{{_('Active')}}</span>
                                                @else
                                                <span class="badge bg-danger text-white">{{_('Blocked')}}</span>
                                            @endif
                                        </td>
                                        @if ($teacher->status == 1)
                                        <td>
                                            <ul class="d-flex justify-content-center">
                                                <li class="mr-3"><a href="{{route('teacher.profile', ['teacher' => Hashids::encode($teacher->id)])}}" class="text-primary"><i class="fa fa-eye"></i></a></li>
                                                <li class="mr-3">
                                                    <form action="{{route('update.teacher.status', ['teacher' => Hashids::encode($teacher->id)])}}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-link p-0" onclick="return confirm('Are you sure you want to Block {{strtoupper($teacher->first_name)}} {{strtoupper($teacher->last_name)}}?')"><i class="fas fa-ban text-info"></i></button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="{{route('Teachers.remove', ['teacher' => Hashids::encode($teacher->id)])}}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <button class="btn btn-link p-0" type="submit" onclick="return confirm('Are you sure you want to Delete {{ strtoupper($teacher->first_name) }} {{ strtoupper($teacher->last_name) }} Permanently?')"><i class="ti-trash text-danger"></i></button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </td>
                                        @else
                                        <td>
                                            <ul class="d-flex justify-content-center">
                                                <li class="mr-3">
                                                    <form action="{{route('teachers.restore', ['teacher' => Hashids::encode($teacher->id)])}}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-link p-0" onclick="return confirm('Are you sure you want to Unblock {{strtoupper($teacher->first_name)}} {{strtoupper($teacher->last_name)}}?')"><i class="ti-reload text-success"></i></button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="{{route('Teachers.remove', ['teacher' => Hashids::encode($teacher->id)])}}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <button class="btn btn-link p-0" type="submit" onclick="return confirm('Are you sure you want to Delete {{ strtoupper($teacher->first_name) }} {{ strtoupper($teacher->last_name) }} Permanently?')"><i class="ti-trash text-danger"></i></button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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
</div>
@endsection
