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
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <div class="login-form-head">
                        <h4>Reset Password</h4>
                    </div>
                    @if (Session::has('error'))
                    <div class="alert alert-danger text-center" role="alert">{{Session::get('error')}}</div>
                    @endif
                    @if (Session::has('success'))
                        <div class="alert alert-success text-center" role="alert">{{Session::get('success')}}</div>
                    @endif
                    <div class="login-form-body">
                        <input type="hidden" name="token" value="{{ $token }}">
                        <div class="form-gp">
                            <label for="exampleInputEmail1">Email address</label>
                            <input type="email" id="exampleInputEmail1" name="email" value="{{ $email ?? old('email') }}">
                            <i class="ti-email"></i>
                            @error('email')
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
                        <div class="form-gp">
                            <label for="exampleInputPassword1">Confirm Password</label>
                            <input type="password" id="exampleInputPassword1" name="password_confirmation" value="{{old('password')}}">
                            <i class="ti-lock"></i>
                            @error('password_confirmation')
                                <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>

                        <div class="submit-btn-area">
                            <button id="form_submit" type="submit">Reset Password <i class="ti-arrow-right"></i></button>
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
