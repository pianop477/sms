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
                <form method="POST" action="{{route('login')}}">
                    @csrf
                    <div class="login-form-head">
                        <h4>Sign In</h4>
                        <p>Hello there, Sign in and start Session</p>
                    </div>
                    @if (Session::has('error'))
                    <div class="alert alert-danger text-center" role="alert">{{Session::get('error')}}</div>
                    @endif
                    @if (Session::has('success'))
                        <div class="alert alert-success text-center" role="alert">{{Session::get('success')}}</div>
                    @endif
                    @error('email')
                        <div class="alert alert-danger text-center" role="alert">{{$message}}</div>
                    @enderror
                    <div class="login-form-body">
                        <div class="form-gp">
                            <label for="exampleInputEmail1">Email or Phone</label>
                            <input type="text" id="exampleInputEmail1" name="username" value="{{old('username')}}">
                            <i class="ti-email"></i>
                            @error('username')
                            <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="form-gp">
                            <label for="exampleInputPassword1">Password</label>
                            <input type="password" id="exampleInputPassword1" name="password" value="{{old('password')}}">
                            <i class="ti-lock"></i>
                            @error('password')
                                <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="row mb-4 rmber-area">
                            <div class="col-6">
                                <div class="custom-control custom-checkbox mr-sm-2">
                                    <input type="checkbox" name="remember" class="custom-control-input" id="customControlAutosizing" value="">
                                    <label class="custom-control-label" for="customControlAutosizing">Remember Me</label>
                                </div>
                            </div>
                            @if (Route::has('password.reset'))
                            <div class="col-6 text-right">
                                <a href="{{route('password.request')}}">Forgot Password?</a>
                            </div>
                            @endif
                        </div>
                        <div class="submit-btn-area">
                            <button id="form_submit" type="submit">Login <i class="ti-arrow-right"></i></button>
                        </div>
                        @if (Route::has('users.form'))
                        <div class="form-footer text-center mt-1">
                            <p class="text-muted">Don't have an account? <a href="{{route('users.form')}}">Sign up</a></p>
                            <p class="text-muted">Nothing else? <a href="{{route('welcome')}}"> Home</a></p>
                        </div>
                        @endif
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
