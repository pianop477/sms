@extends('SRTDashboard.frame')

@section('content')
<div class="row">
    <div class="col-12 mt-2">
        <div class="row">
            <div class="col-10">
                <h4 class="text-capitalize ">User Account</h4>
            </div>
            <div class="col-2">
                <a href="{{route('home')}}" class="btn btn-info float-right btn-xs"><i class="fas fa-arrow-circle-left"></i> Back</a>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-3 mt-2">
                <div class="card card-outline" style="border-top: 3px solid blue;">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            @if ($user->image == Null)
                                @if ($user->gender == 'male')
                                    <img src="{{asset('assets/img/profile/avatar.jpg')}}" alt="" class="profile-user img img-fluid rounded-circle" width="300px" height="300px">
                                @else
                                    <img src="{{asset('assets/img/profile/avatar-female.jpg')}}" alt="" class="profile-user img img-fluid rounded-circle" width="300px" height="300px">
                                @endif
                            @else
                                <img src="{{asset('assets/img/profile/'. $user->image)}}" alt="" class="profile-user img img-fluid rounded-circle" width="300px" height="300px">
                            @endif
                        </div>
                        <h6 class="profile-username text-center text-primary text-uppercase">
                            <b>{{ucwords(strtolower($user->first_name. ' '. $user->last_name))}}</b>
                        </h6>
                        <p class="text-muted text-center">
                            @if ($user->usertype == 1)
                                <b><span class="text-muted">{{_('#System Administrator')}}</span></b>
                            @elseif ($user->usertype == 2)
                                <b><span class="text-muted">{{_('#School Administrator')}}</span></b>
                            @elseif($user->usertype == 3)
                                <b><span class="text-muted">{{_('#Teacher')}}</span></b><br>
                            @else
                                <b><span class="text-muted">{{_('#Parent')}}</span></b>
                            @endif
                        </p>
                        <br>
                        <br>
                        <ul class="list-group list-group-flush mb-3">
                            @if ($user->usertype == 3)
                            <li class="list-group-item">
                                <b>Member ID#</b>
                                <span class="text-muted float-right text-uppercase">{{$user->member_id}}</span>
                            </li>
                            @endif
                            <li class="list-group-item">
                                <b>Role</b>
                                @if ($user->usertype == 1 || $user->usertype == 2)
                                    <span class="badge bg-primary text-white float-right text-capitalize">{{_('Admin')}}</span>
                                @elseif ($user->usertype == 3)
                                    <span class="badge bg-primary text-white float-right text-capitalize">{{$user->role_name}}</span>
                                @else
                                    <span class="badge bg-primary text-white float-right text-capitalize">{{_('Parent')}}</span>
                                @endif
                            </li>
                            <li class="list-group-item">
                                <b>Gender</b>
                                <span class="float-right text-capitalize">{{$user->gender}}</span>
                            </li>
                            <li class="list-group-item">
                                <b>Status</b>
                                @if ($user->status === 1)
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
                                <a class="nav-link" href="#edit" title="Update" data-toggle="tab"><i class="fas fa-user-pen"></i> Edit Account</a>
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
                                            <td class="text-uppercase">{{$user->phone}}</td>
                                        </tr>
                                        <tr>
                                            <th><b><i class="fas fa-envelope"></i> Email</b></th>
                                            <td>{{$user->email ?? 'Email not provided'}}</td>
                                        </tr>
                                        <tr>
                                            <th><b>Registration Date</b></th>
                                            <td>
                                                @if ($user->created_at == Null)
                                                    {{_('Unknown')}}
                                                @else
                                                    {{\Carbon\Carbon::parse($user->created_at)->format('d-m-Y')}}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            @if ($user->usertype == 3 || $user->usertype == 4)
                                                <th><i class="fas fa-location-dot"></i> <b>Street Address</b></th>
                                                <td class="text-capitalize">{{$user->teacher_address ?? $user->parent_address}}</td>
                                            @endif
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            {{-- parent tab pane --}}
                            <div class="tab-pane" id="edit">
                                <p class="text-center"><strong>Edit Account Information</strong></span></p>
                                <form action="{{route('update.profile', $user->id)}}" method="POST" enctype="multipart/form-data" novalidate="" class="needs-validation" onsubmit="preventDefault();">
                                    @csrf
                                    @method('PUT')
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label">First Name</label>
                                            <div class="input-group input-group-outline mb-3">
                                                <input type="text" name="fname" class="form-control" value="{{$user->first_name}}">
                                            </div>
                                            @error('fname')<span class="text-danger text-sm">{{$message}}</span>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Last Name</label>
                                            <div class="input-group input-group-outline mb-3">
                                                <input type="text" name="lname" class="form-control" value="{{$user->last_name}}">
                                            </div>
                                            @error('lname')<span class="text-danger text-sm">{{$message}}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label">Phone</label>
                                            <div class="input-group input-group-outline mb-3">
                                                <input type="text" name="phone" class="form-control" value="{{$user->phone}}">
                                            </div>
                                            @error('phone')<span class="text-danger text-sm">{{$message}}</span>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Email</label>
                                            <div class="input-group input-group-outline mb-3">
                                                <input type="email" name="email" class="form-control" value="{{$user->email}}">
                                            </div>
                                            @error('email')<span class="text-danger text-sm">{{$message}}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                             <label class="form-label">Gender</label>
                                            <div class="input-group input-group-outline mb-3">
                                                <select name="gender" id="" class="form-control text-capitalize">
                                                    <option value="{{$user->gender}}">{{$user->gender}}</option>
                                                    <option value="male">Male</option>
                                                    <option value="female">Female</option>
                                                </select>
                                            </div>
                                            @error('gender')<span class="text-danger text-sm">{{$message}}</span>@enderror
                                        </div>
                                        @if ($user->usertype == 3 || $user->usertype == 4)
                                            <div class="col-md-4">
                                                <label class="form-label">Address</label>
                                                <div class="input-group input-group-outline mb-3">
                                                    <input type="text" name="address" class="form-control" value="{{$user->parent_address ?? $user->teacher_address}}">
                                                </div>
                                                @error('address')<span class="text-danger text-sm">{{$message}}</span>@enderror
                                            </div>
                                        @endif
                                        <div class="col-md-4">
                                            <label class="form-label">Photo: <span class="text-danger text-sm">Maximum 1MB</span></label>
                                            <div class="input-group input-group-outline mb-3">
                                                <input type="file" name="image" class="form-control" value="">
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
