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
                            <label for="exampleInputName1">Last Name</label>
                            <input type="text" id="exampleInputName1" name="lname" value="{{old('lname')}}">
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
                            <select name="gender" id="exampleInputEmail1" class="form-control">
                                <option value="">--Select Gender--</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                            @error('gender')
                                <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="form-gp">
                            <label for="exampleInputEmail1">Phone Number</label>
                            <input type="tel" id="exampleInputEmail1" name="phone" value="{{old('phone')}}">
                            <i class="ti-mobile"></i>
                            @error('phone')
                                <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="form-gp">
                            <label for="exampleInputEmail1">Street Name/Location</label>
                            <input type="text" id="exampleInputEmail1" name="street" value="{{old('street')}}">
                            <i class="ti-location-pin"></i>
                            @error('street')
                                <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="form-gp">
                            <label for="exampleInputPassword1">Password</label>
                            <input type="password" id="exampleInputPassword1" name="password">
                            <i class="ti-lock"></i>
                            @error('password')
                                <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="form-gp">
                            <label for="exampleInputPassword2">Repeat Password</label>
                            <input type="password" id="exampleInputPassword2" name="password_confirmation">
                            <i class="ti-lock"></i>
                            @error('password_confirmation')
                                <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="form-gp">
                            <select name="school" id="exampleInputEmail1" class="form-control text-capitalize">
                                <option value="">--Select School Name--</option>
                                @if ($schools->isEmpty())
                                    <option value="" class="text-danger">no schools found</option>
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
            </div>
        </div>
    </div>
    <!-- login area end -->
    @include('SRTDashboard.script')
    @include('sweetalert::alert')
</body>

</html>
