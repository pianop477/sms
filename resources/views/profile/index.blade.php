@extends('SRTDashboard.frame')
@section('content')
  <div class="card card-body mx-2 mx-md-4 mt-n4">
    <div class="row gx-4 mb-2">
      <div class="col-auto">
        <div class="avatar avatar-xl position-relative">
            @if ($user->image)
            <img src="{{asset('assets/img/profile/'. $user->image)}}" alt="profile_image" class="profile-img border-radius-lg shadow-sm" style="width: 150px; object-fit:cover; border-radius: 50px;">

            @else
                    @if ($user->gender == 'male')
                        <img src="{{asset('assets/img/profile/avatar.jpg')}}" alt="" class="profile-img border-radius-lg shadow-sm" style="width: 120px; object-fit:cover">
                        @else
                        <img src="{{asset('assets/img/profile/avatar-female.jpg')}}" alt="" class="profile-img border-radius-lg shadow-sm" style="width: 120px; object-fit:cover">
                    @endif
            @endif
        </div>
      </div>
      <div class="col-auto my-auto">
        <div class="h-100">
          <h5 class="mb-1">
            <span class="text-capitalize">{{$user->first_name. ' '. $user->last_name}}</span>
          </h5>
            @if ($user->usertype == 1)
              <span class="badge bg-success text-white">{{_('System Administrator')}}</span>

            @elseif ($user->usertype == 2)

              <span class="badge bg-primary text-white">{{_('School Manager')}}</span>

            @elseif ($user->usertype == 3)
                @if ($teacher->role_id == 1)
                    <p class="">ID No: <span class="text-uppercase"><strong>{{$teacher->member_id}}</strong></span></p>
                    <span class="badge bg-secondary text-white text-capitalize">{{$teacher->role_name}}</span>
                @elseif ($teacher->role_id == 2)
                    <p class="">ID No: <span class="text-uppercase"><strong>{{$teacher->member_id}}</strong></span></p>
                    <span class="badge bg-success text-white text-capitalize">{{$teacher->role_name}}</span>
                @elseif ($teacher->role_id == 3)
                    <p class="">ID No: <span class="text-uppercase"><strong>{{$teacher->member_id}}</strong></span></p>
                    <span class="badge bg-primary text-white text-capitalize">{{$teacher->role_name}}</span>
                @else
                    <p class="">ID No: <span class="text-uppercase"><strong>{{$teacher->member_id}}</strong></span></p>
                    <span class="badge bg-info text-white text-capitalize">{{$teacher->role_name}}</span>
                @endif
            @else
                <span class="text-white badge bg-primary">{{_('Parent')}}</span>
            @endif
        </div>
      </div>
    </div>
    <div class="row">
        <div class="col-12 col-xl-4">
          <div class="card h-auto">
            <div class="card-body p-3">
              <h6 class="text-uppercase text-body text-xs font-weight-bolder">Account details</h6>
              <hr>
              <ul class="list-group">
                <li class="list-group-item border-0 ps-0 pt-0 text-sm text-capitalize"><strong class="text-dark">Full Name:</strong> &nbsp; {{$user->first_name. ' '. Auth::user()->last_name}}</li>
                <li class="list-group-item border-0 ps-0 text-sm text-capitalize"><strong class="text-dark">Mobile:</strong> &nbsp; {{$user->phone}}</li>
                <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Email:</strong> &nbsp; {{$user->email ?? 'Null'}}</li>
                <li class="list-group-item border-0 ps-0 text-sm text-capitalize"><strong class="text-dark">Gender:</strong> &nbsp; {{$user->gender}}</li>
                <li class="list-group-item border-0 ps-0 pb-0">
                  <strong class="text-dark text-sm">Social:</strong> &nbsp;
                  <a class="btn btn-facebook btn-simple mb-0 ps-1 pe-2 py-0" href="javascript:;">
                    <i class="fab fa-facebook fa-lg text-primary"></i>
                  </a>
                  <a class="btn btn-twitter btn-simple mb-0 ps-1 pe-2 py-0" href="javascript:;">
                    <i class="fab fa-twitter fa-lg text-primary"></i>
                  </a>
                  <a class="btn btn-instagram btn-simple mb-0 ps-1 pe-2 py-0" href="javascript:;">
                    <i class="fab fa-instagram fa-lg"></i>
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <div class="col-12 col-xl-8">
          <div class="card h-auto">
            <div class="card-header">
              <h6 class="text-uppercase text-body text-xs font-weight-bolder">Edit Profile Details</h6>
            </div>
            <div class="card-body p-2">
                <form action="{{route('update.profile', $user->id)}}" method="POST" enctype="multipart/form-data" class="needs-valiation" novalidate>
                    @csrf
                    @method('PUT')
                <div class="">
                    <label class="form-label">First Name</label>
                    <div class="input-group input-group-outline mb-3">
                        <input type="text" name="fname" class="form-control text-uppercase" value="{{$user->first_name}}">
                    </div>
                    @error('fname')<span class="text-danger text-sm">{{$message}}</span>@enderror
                </div>
                <div class="">
                    <label class="form-label">Last Name</label>
                    <div class="input-group input-group-outline mb-3">
                        <input type="text" name="lname" class="form-control text-uppercase" value="{{$user->last_name}}">
                    </div>
                    @error('lname')<span class="text-danger text-sm">{{$message}}</span>@enderror
                </div>
                <div class="">
                    <label class="form-label">Phone</label>
                    <div class="input-group input-group-outline mb-3">
                        <input type="text" name="phone" class="form-control text-uppercase" value="{{$user->phone}}">
                    </div>
                    @error('phone')<span class="text-danger text-sm">{{$message}}</span>@enderror
                </div>
                <div class="">
                    <label class="form-label">Email</label>
                    <div class="input-group input-group-outline mb-3">
                        <input type="email" name="email" class="form-control" value="{{$user->email}}">
                    </div>
                    @error('email')<span class="text-danger text-sm">{{$message}}</span>@enderror
                </div>
                <div class="">
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
                <div class="">
                    <label class="form-label">Photo: <span class="text-danger text-sm">Maximum 1MB</span></label>
                    <div class="input-group input-group-outline mb-3">
                        <input type="file" name="image" class="form-control" value="">
                    </div>
                    @error('image')<span class="text-danger text-sm">{{$message}}</span>@enderror
                </div>
                <button type="submit" id="saveButton" class="btn btn-success btn-sm">Save</button>
            </form>
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
@endsection
