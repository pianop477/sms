<!doctype html>
<html class="no-js" lang="en">
@include('SRTDashboard.header')
<body>
    @include('SRTDashboard.preloader')
    <!-- preloader area end -->
    <!-- login area start -->
    <div class="login-area login-bg">
        <div class="container">
            <div class="login-box ptb--100">
                <form role="form" method="POST" action="{{route('users.create')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="login-form-head">
                        <h4>Parents Registration Form</h4>
                    </div>
                    <div class="login-form-body">
                        <div class="form-gp">
                            <label for="exampleInputName1">First Name</label>
                            <input type="text" id="exampleInputName1" name="fname" value="{{old('fname')}}">
                            <i class="ti-user"></i>
                            @error('fname')
                                <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="form-gp">
                            <label for="exampleInputName2">Last Name</label>
                            <input type="text" id="exampleInputName2" name="lname" value="{{old('lname')}}">
                            <i class="ti-user"></i>
                            @error('lname')
                                <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="form-gp">
                            <label for="exampleInputEmail1">Email</label>
                            <input type="email" id="exampleInputEmail1" name="email" value="{{old('email')}}">
                            <i class="ti-email"></i>
                            @error('email')
                                <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="form-gp">
                            {{-- <label for="exampleInputGender">Gender</label> --}}
                            <select name="gender" id="exampleInputGender" class="form-control">
                                <option value="">--Select Gender--</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                            @error('gender')
                                <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="form-gp">
                            <label for="exampleInputPhone">Phone Number</label>
                            <input type="tel" id="exampleInputPhone" name="phone" value="{{old('phone')}}">
                            <i class="ti-mobile"></i>
                            @error('phone')
                                <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="form-gp">
                            <label for="exampleInputStreet">Street Name/Location</label>
                            <input type="text" id="exampleInputStreet" name="street" value="{{old('street')}}">
                            <i class="ti-location-pin"></i>
                            @error('street')
                                <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="form-gp">
                            <label for="exampleInputPassword1">Password</label>
                            <div class="input-group">
                                <input type="password" id="exampleInputPassword1" name="password" class="form-control">
                                <div class="input-group-append">
                                    <span class="input-group-text toggle-password" data-target="exampleInputPassword1">
                                        <i class="fa fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                            @error('password')
                                <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="form-gp">
                            <label for="exampleInputPassword2">Repeat Password</label>
                            <div class="input-group">
                                <input type="password" id="exampleInputPassword2" name="password_confirmation" class="form-control">
                                <div class="input-group-append">
                                    <span class="input-group-text toggle-password" data-target="exampleInputPassword2">
                                        <i class="fa fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                            @error('password_confirmation')
                                <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>

                        <div class="form-gp">
                            {{-- <label for="exampleInputSchool">School Name</label> --}}
                            <select name="school" id="exampleInputSchool" class="form-control text-capitalize">
                                <option value="">--Select School Name--</option>
                                @if ($schools->isEmpty())
                                    <option value="" class="text-danger">No schools found</option>
                                @else
                                    @foreach ($schools as $school )
                                        <option value="{{$school->id}}">{{$school->school_name}}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('school')
                                <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>

                        <div class="submit-btn-area">
                            <button id="form_submit" type="submit">Submit <i class="ti-arrow-right"></i></button>
                        </div>
                        <div class="form-footer text-center mt-4">
                            <p class="text-muted">Do you have an Account? <a href="{{route('login')}}">Login here</a></p>
                        </div>
                    </div>
                </form>
                <!-- JavaScript to Toggle Password Visibility -->
                <script>
                    document.querySelectorAll(".toggle-password").forEach(item => {
                        item.addEventListener("click", function() {
                            let input = document.getElementById(this.getAttribute("data-target"));
                            if (input.type === "password") {
                                input.type = "text";
                                this.innerHTML = '<i class="fa fa-eye-slash"></i>';
                            } else {
                                input.type = "password";
                                this.innerHTML = '<i class="fa fa-eye"></i>';
                            }
                        });
                    });
                </script>
            </div>
        </div>
    </div>
    <!-- login area end -->
    @include('SRTDashboard.script')
    @include('sweetalert::alert')
</body>

</html>
