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
                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="login-form-head">
                        <h4>Reset Password</h4>
                    </div>
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    @error('email')
                        <div class="alert alert-danger text-center" role="alert">{{$message}}</div>
                    @enderror
                    <div class="login-form-body">
                        <div class="form-gp">
                            <label for="exampleInputEmail1">Email address</label>
                            <input type="email" id="exampleInputEmail1" name="email" value="{{old('email')}}">
                            <i class="ti-email"></i>
                            @error('email')
                            <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>

                        <div class="submit-btn-area">
                            <button id="form_submit" type="submit">Send Password Reset Link <i class="ti-arrow-right"></i></button>
                        </div>
                        @if (Route::has('users.form'))
                        <div class="form-footer text-center mt-1">
                            <p class="text-muted">Already have an account? <a href="{{route('login')}}">Sign In</a></p>
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
