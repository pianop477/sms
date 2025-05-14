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
                <form method="POST" action="{{ route('password.update') }}" class="needs-validation" novalidate>
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
                                <div class="input-group">
                                    <input type="password" id="exampleInputPassword1" name="password" value="{{old('password')}}" placeholder="Password" class="form-control">
                                    <div class="input-group-append">
                                        <span class="input-group-text toggle-password" data-target="exampleInputPassword1">
                                            <i class="ti-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                @error('password')
                                <div class="text-danger">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="form-gp">
                            <label for="exampleInputPassword1">Password</label>
                                <div class="input-group">
                                    <input type="password" id="exampleInputPassword1" name="password_confirmation" value="{{old('password')}}" placeholder="Password" class="form-control">
                                    <div class="input-group-append">
                                        <span class="input-group-text toggle-password" data-target="exampleInputPassword1">
                                            <i class="ti-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                @error('password')
                                <div class="text-danger">{{$message}}</div>
                                @enderror
                        </div>

                        <div class="submit-btn-area">
                            <button id="saveButton" type="submit">Reset Password <i class="ti-arrow-right"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- login area end -->

    @include('SRTDashboard.script')
    @include('sweetalert::alert')
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
                    submitButton.innerHTML = "Reset Password";
                    return;
                }

                // Chelewesha submission kidogo ili button ibadilike kwanza
                setTimeout(() => {
                    form.submit();
                }, 500);
            });
        });

        //show and hide password field
            document.querySelectorAll(".toggle-password").forEach(item => {
                item.addEventListener("click", function() {
                    let input = document.getElementById(this.getAttribute("data-target"));
                        if (input.type === "password") {
                            input.type = "text";
                                this.innerHTML = '<i class="fa fa-eye-slash"></i>';
                            } else {
                                input.type = "password";
                                this.innerHTML = '<i class="ti-eye"></i>';
                            }
                });
            });
    </script>
</body>

</html>
