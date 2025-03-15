<!DOCTYPE html>
<html lang="en">
    <head>
        @include('SRTDashboard.header')
    </head>
    <body>
        @include('SRTDashboard.preloader')
        <!-- preloader area end -->
        <!-- login area start -->
        <div class="login-area login-bg">
            <div class="container">
                <div class="login-box ptb--100">
                    <form method="POST" action="{{route('login')}}" class="needs-validation" novalidate>
                        @csrf
                        <div class="login-form-head">
                            <h4>Sign In</h4>
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
                                <div class="input-group">
                                    <input type="password" id="exampleInputPassword1" name="password" placeholder="Password" class="form-control">
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
                                <button id="saveButton" type="submit">Sign in <i class="ti-arrow-right"></i></button>
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
        <script>
            setInterval(function() {
                fetch("{{ url('/check-session') }}")
                .then(response => response.json())
                .then(data => {
                    if (!data.active) {
                        alert("Session Expired! Please login.");
                        window.location.reload(); // Refresh page ili kupata CSRF token mpya
                    }
                });
            }, 1000 * 60 * 5); // Angalia session kila baada ya dakika 5


            // Disable button baada ya kubofya ili kuzuia double submission
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
                        submitButton.innerHTML = "Sign In";
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
            @include('SRTDashboard.script')
        </script>

        @include('sweetalert::alert')
    </body>
</html>
