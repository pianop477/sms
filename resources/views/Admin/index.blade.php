@extends('SRTDashboard.frame')

@section('content')
<div class="row">
    <div class="col-12 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-10">
                        <h4 class="header-title text-uppercase text-center">Super users Admin Accounts</h4>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-xs btn-info" data-toggle="modal" data-target=".bd-example-modal-lg">
                            <i class="fas fa-user-plus"></i> New User
                        </button>
                        <div class="modal fade bd-example-modal-lg">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">users Registration Form</h5>
                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <form class="needs-validation" novalidate="" action="{{route('admin.accounts.registration')}}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-row">
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustom01">First name</label>
                                                    <input type="text" name="fname" class="form-control" id="validationCustom01" placeholder="First name" value="{{old('fname')}}" required="">
                                                    @error('fname')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="validationCustom02">Last name</label>
                                                    <input type="text" name="lname" class="form-control" id="validationCustom02" placeholder="Last name" required="" value="{{old('lname')}}">
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
                                                        <input type="email" name="email" class="form-control" id="validationCustomUsername" placeholder="Email ID" aria-describedby="inputGroupPrepend" required="" value="{{old('email')}}">
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
                                                    <input type="text" name="phone" class="form-control" id="validationCustom02" placeholder="Phone Number" required="" value="{{old('phone')}}">
                                                    @error('phone')
                                                    <div class="text-danger">{{$message}}</div>
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
                <div class="single-table">
                    <div class="table-responsive">
                        <table class="table table-hover progress-table" id="myTable">
                            <thead class="text-uppercase">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Full name</th>
                                    <th scope="col">Gender</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">status</th>
                                    <th scope="col" class="text-center">action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user )
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td class="text-capitalize">{{$user->first_name. ' '. $user->last_name}}</td>
                                        <td class="text-capitalize">{{$user->gender[0]}}</td>
                                        <td>{{$user->phone}}</td>
                                        <td>{{$user->email}}</td>
                                        <td>
                                            @if ($user->status ==1)
                                                <span class="badge bg-success text-white">{{_('Active')}}</span>
                                                @else
                                                <span class="badge bg-danger text-white">{{_('Blocked')}}</span>
                                            @endif
                                        </td>
                                        @if ($user->id == 1)
                                            <td class="text-center">
                                                <span class="badge bg-success text-white">Super User</span>
                                            </td>
                                        @else
                                            @if ($user->status == 1)
                                            <td>
                                                <ul class="d-flex justify-content-center">
                                                    <li class="mr-3">
                                                        <form action="{{route('admin.account.block', ['user' => Hashids::encode($user->id)])}}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="btn btn-link p-0" onclick="return confirm('Are you sure you want to Block {{strtoupper($user->first_name)}} {{strtoupper($user->last_name)}}?')"><i class="fas fa-ban text-info"></i></button>
                                                        </form>
                                                    </li>
                                                    <li><a href="{{route('admin.account.destroy', ['user' => Hashids::encode($user->id)])}}" onclick="return confirm('Are you sure you want to delete {{strtoupper($user->first_name)}} {{strtoupper($user->last_name)}} Permanently?')" class="text-danger"><i class="ti-trash"></i></a></li>
                                                </ul>
                                            </td>
                                            @else
                                            <td>
                                                <ul class="d-flex justify-content-center">
                                                    <li class="mr-3">
                                                        <form action="{{route('admin.account.unblock', ['user' => Hashids::encode($user->id)])}}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="btn btn-link p-0" onclick="return confirm('Are you sure you want to Unblock {{strtoupper($user->first_name)}} {{strtoupper($user->last_name)}}?')"><i class="ti-reload text-success"></i></button>
                                                        </form>
                                                    </li>
                                                    <li><a href="{{route('admin.account.destroy', ['user' => Hashids::encode($user->id)])}}" onclick="return confirm('Are you sure you want to delete {{strtoupper($user->first_name)}} {{strtoupper($user->last_name)}} Permanently?')" class="text-danger"><i class="ti-trash"></i></a></li>
                                                </ul>
                                            </td>
                                            @endif
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
                submitButton.innerHTML = "Saving...";

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
