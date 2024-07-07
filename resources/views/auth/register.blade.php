
<!DOCTYPE html>
<html lang="en">

@include('SRTDashboard.header')
<body class="">
  <div class="container position-sticky z-index-sticky top-0">
    <div class="row">
      <div class="col-12">
      </div>
    </div>
  </div>
  <main class="main-content  mt-0">
    <section>
      <div class="page-header min-vh-100">
        <div class="container">
          <div class="row">
            <div class="col-6 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 start-0 text-center justify-content-center flex-column">
              <div class="position-relative bg-gradient-primary h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center" style="background-image: url('../assets/img/illustrations/illustration-signup.jpg'); background-size: cover;">
              </div>
            </div>
            <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column ms-auto me-auto ms-lg-auto me-lg-5">
              <div class="card card-plain">
                <div class="card-header">
                  <h4 class="font-weight-bolder">ShuleApp</h4>
                  <p class="mb-0">Parents Registration Form</p>
                </div>
                <div class="card-body">
                    @if (Session::has('success'))
                        <div class="alert alert-success">
                            {{Session::get('success')}}
                        </div>
                    @endif
                  <form role="form" method="POST" action="{{route('users.create')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="input-group input-group-outline mb-3">
                      <label class="form-label">First Name</label>
                      <input type="text" class="form-control" name="fname" value="{{old('fname')}}">
                      @error('fname')
                      <span class="text-danger">{{$message}}</span>
                      @enderror
                    </div>
                    <div class="input-group input-group-outline mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-control" name="lname" value="{{old('lname')}}">
                        @error('fname')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                      </div>
                    <div class="input-group input-group-outline mb-3">
                      <label class="form-label">Email</label>
                      <input type="email" class="form-control" name="email" value="{{old('email')}}">
                      @error('email')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="input-group input-group-outline mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" name="phone" value="{{old('phone')}}">
                        @error('phone')
                          <span class="text-danger">{{$message}}</span>
                          @enderror
                      </div>
                      <div class="input-group input-group-outline mb-3">
                        <label class="form-label"></label>
                        <select name="gender" id="" class="form-control">
                            <option value="">--Select gender--</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                        @error('gender')
                          <span class="text-danger">{{$message}}</span>
                          @enderror
                      </div>
                      <input type="hidden" name="usertype" value="4">
                      <div class="input-group input-group-outline mb-3">
                        <label class="form-label">Street/Village/Ward</label>
                        <input type="text" class="form-control" name="street" value="{{old('street')}}">
                        @error('street')
                          <span class="text-danger">{{$message}}</span>
                          @enderror
                      </div>
                      <div class="input-group input-group-outline mb-3">
                        <label class="form-label"></label>
                        <select name="school" id="" class="form-control text-capitalize">
                            <option value="">--Select School--</option>
                            @foreach ($schools as $school )
                                <option value="{{$school->id}}">{{$school->school_name}}</option>
                            @endforeach
                        </select>
                        @error('school')
                          <span class="text-danger">{{$message}}</span>
                          @enderror
                      </div>
                    <div class="input-group input-group-outline mb-3">
                      <label class="form-label">Password</label>
                      <input type="password" class="form-control" name="password">
                      @error('password')
                          <span class="text-danger">{{$message}}</span>
                      @enderror
                    </div>
                    <div class="input-group input-group-outline mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" name="password_confirmation">
                        @error('password_confirmation')
                          <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <label class="form-label">Profile Picture: <span class="text-danger">Must not exceed 2 MB's</span></label>
                    <div class="input-group input-group-outline mb-3">
                        <input type="file" class="form-control" name="image">
                        @error('image')
                          <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="text-center">
                      <button type="submit" class="btn btn-lg bg-gradient-primary btn-lg w-100 mt-4 mb-0">Register</button>
                    </div>
                  </form>
                </div>
                @if (Route::has('login'))
                    <div class="card-footer text-center pt-0 px-lg-2 px-1">
                        <p class="mb-2 text-sm mx-auto">
                        Already have an account?
                        <a href="{{route('login')}}" class="text-primary text-gradient font-weight-bold">Sign in</a>
                        </p>
                    </div>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
  <!--   Core JS Files   -->
  @include('SRTDashboard.script')
</body>

</html>
