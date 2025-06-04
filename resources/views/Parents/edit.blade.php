@extends('SRTDashboard.frame')

@section('content')
<div class="row">
    <div class="col-12 mt-2">
        <div class="row">
            <div class="col-10">
                <h4 class="text-capitalize ">Parents/Guardian Information</h4>
            </div>
            <div class="col-2">
                <a href="{{route('Parents.index')}}" class="btn btn-info float-right btn-xs"><i class="fas fa-arrow-circle-left"></i> Back</a>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-3 mt-2">
                <div class="card card-outline" style="border-top: 3px solid blue;">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            @if ($parents->image == Null)
                                @if ($parents->gender == 'male')
                                    <img src="{{asset('assets/img/profile/avatar.jpg')}}" alt="" class="profile-user img img-fluid rounded-circle" style="width: 100px;">
                                @else
                                    <img src="{{asset('assets/img/profile/avatar-female.jpg')}}" alt="" class="profile-user img img-fluid rounded-circle" style="width: 100px;">
                                @endif
                            @else
                                <img src="{{asset('assets/img/profile/'. $parents->image)}}" alt="" class="profile-user img img-fluid rounded-circle" style="width: 100px;">
                            @endif
                        </div>
                        <h6 class="profile-username text-center text-primary text-uppercase">
                            <b>{{ucwords(strtolower($parents->first_name. ' '. $parents->last_name))}}</b>
                        </h6>
                        <br>
                        <br>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item">
                                <b>Gender</b>
                                <span class="float-right text-capitalize">{{$parents->gender}}</span>
                            </li>
                            <li class="list-group-item">
                                <b>Status</b>
                                @if ($parents->status === 1)
                                    <span class="float-right badge badge-success text-capitalize">Active</span>
                                @else
                                    <span class="float-right badge badge-secondary text-capitalize">Inactive</span>
                                @endif
                            </li>
                        </ul>
                        {{-- <a href="{{route('parent.edit.student', ['students' => Hashids::encode($students->id)])}}" class="btn btn-block btn-primary" data-toggle="tooltip" data-placement="button" title="Edit"> Edit</a> --}}
                    </div>
                </div>
            </div>
            <div class="col-md-9 mt-2">
                <div class="card">
                    <div class="card-header p-1">
                        <ul class="nav nav-pills flex-column flex-sm-row">
                            <li class="flex-sm-fill text-sm-center nav-item">
                                <a class="nav-link active" title="Profile" href="#profile" data-toggle="tab"><i class="fas fa-user"></i> Profile</a>
                            </li>
                            <li class="flex-sm-fill text-sm-center nav-item">
                                <a class="nav-link" href="#students" title="Student list" data-toggle="tab"><i class="fas fa-user-graduate"></i> Student List</a>
                            </li>
                            <li class="flex-sm-fill text-sm-center nav-item">
                                <a class="nav-link" href="#edit" title="Update" data-toggle="tab"><i class="fas fa-user-pen"></i> Edit Information</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            {{-- profile tab pane --}}
                            <div class="active tab-pane" id="profile">
                                <table class="table table-condensed table-responsive-md">
                                    <tbody>
                                        <tr>
                                            <th><b><i class="fas fa-phone"></i> Phone</b></th>
                                            <td class="text-uppercase">{{$parents->phone}}</td>
                                        </tr>
                                        <tr>
                                            <th><b><i class="fas fa-envelope"></i> Email</b></th>
                                            <td>{{$parents->email ?? 'Email not provided'}}</td>
                                        </tr>
                                        <tr>
                                            <th><b>Registration Date</b></th>
                                            <td>
                                                @if ($parents->created_at == Null)
                                                    {{_('Unknown')}}
                                                @else
                                                    {{\Carbon\Carbon::parse($parents->user_created_at)->format('d-m-Y')}}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><i class="fas fa-location-dot"></i> <b>Street Address</b></th>
                                            <td class="text-capitalize">{{$parents->address}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            {{-- student tab pane --}}
                             <div class="tab-pane" id="students">
                                <p class="text-center"><strong>Students Information</strong></span></p>
                                {{-- table here --}}
                                <hr>
                                <div class="row">
                                    @foreach ($students as $student)
                                    <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                                        <div class="p-3 border rounded shadow-sm">
                                            <p><strong>Admission Number: </strong><span class="text-uppercase">{{$student->admission_number}}</span></p>
                                            <p><strong>Name:</strong>
                                                <span class="text-uppercase" style="text-decoration: underline">
                                                    <a href="{{route('Students.show', ['student' => Hashids::encode($student->id)])}}">{{$student->first_name}} {{$student->middle_name}} {{$student->last_name}} - {{$student->gender}}</a>
                                                </span>
                                            </p>
                                            <p><strong>Class:</strong> <span class="text-uppercase">{{$student->class_name}} - {{$student->class_code}}</span></p>
                                            <form action="{{route('Students.destroy', ['student' => Hashids::encode($student->id)])}}" method="POST">
                                                @csrf
                                                <button class="btn btn-danger btn-xs p-1" onclick="return confirm('Are you sure you want to block {{strtoupper($student->first_name)}} {{strtoupper($student->middle_name)}} {{strtoupper($student->last_name)}}?')">
                                                    <i class="ti-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            {{-- edit tab pane --}}
                            <div class="tab-pane" id="edit">
                                <p class="text-center"><strong>Edit Parent/Guardian Information</strong></span></p>
                                <form action="{{route('Parents.update', ['parents' => Hashids::encode($parents->id)])}}" method="POST" novalidate="" class="needs-validation" onsubmit="preventDefault();">
                                    @csrf
                                    @method('PUT')
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label">First Name</label>
                                            <div class="input-group input-group-outline mb-3">
                                                <input type="text" name="fname" class="form-control" value="{{$parents->first_name}}">
                                            </div>
                                            @error('fname')<span class="text-danger text-sm">{{$message}}</span>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Last Name</label>
                                            <div class="input-group input-group-outline mb-3">
                                                <input type="text" name="lname" class="form-control" value="{{$parents->last_name}}">
                                            </div>
                                            @error('lname')<span class="text-danger text-sm">{{$message}}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label">Phone</label>
                                            <div class="input-group input-group-outline mb-3">
                                                <input type="text" name="phone" class="form-control" value="{{$parents->phone}}">
                                            </div>
                                            @error('phone')<span class="text-danger text-sm">{{$message}}</span>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Email</label>
                                            <div class="input-group input-group-outline mb-3">
                                                <input type="email" name="email" class="form-control" value="{{$parents->email}}">
                                            </div>
                                            @error('email')<span class="text-danger text-sm">{{$message}}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                             <label class="form-label">Gender</label>
                                            <div class="input-group input-group-outline mb-3">
                                                <select name="gender" id="" class="form-control text-capitalize">
                                                    <option value="{{$parents->gender}}">{{$parents->gender}}</option>
                                                    <option value="male">Male</option>
                                                    <option value="female">Female</option>
                                                </select>
                                            </div>
                                            @error('gender')<span class="text-danger text-sm">{{$message}}</span>@enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Address</label>
                                            <div class="input-group input-group-outline mb-3">
                                                <input type="text" name="street" class="form-control" value="{{$parents->address}}">
                                            </div>
                                            @error('street')<span class="text-danger text-sm">{{$message}}</span>@enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Photo: <span class="text-danger text-sm">Maximum 1MB</span></label>
                                            <div class="input-group input-group-outline mb-3">
                                                <input type="file" name="file" class="form-control" value="">
                                            </div>
                                            @error('image')<span class="text-danger text-sm">{{$message}}</span>@enderror
                                        </div>
                                    </div>
                                    <button type="submit" id="saveButton" class="btn btn-success">Save Changes</button>
                                </form>
                            </div>
                        </div>
                    </div>
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
                submitButton.innerHTML = "Save Changes";
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
