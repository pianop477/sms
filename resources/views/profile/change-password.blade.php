@extends('SRTDashboard.frame')
@section('content')
<div class="col-12">
    <div class="card mt-5">
        <div class="card-body">
            <h4 class="header-title">Change Password Form</h4>
            @if (Session::has('errors'))
                <div class="alert alert-danger" role="alert">
                    {{Session::get('errors')}}
                </div>
            @endif
            <form class="needs-validation" novalidate="" action="{{route('change.new.password')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <!-- Current Password -->
                    <div class="col-md-4 mb-3">
                        <label for="currentPassword">Current Password</label>
                        <div class="input-group">
                            <input type="password" name="current_password" class="form-control" id="currentPassword" placeholder="" value="{{old('current_password')}}" required>
                            <div class="input-group-append">
                                <span class="input-group-text toggle-password" data-target="currentPassword">
                                    <i class="ti-eye"></i>
                                </span>
                            </div>
                        </div>
                        @error('current_password')
                        <div class="invalid-feedback">
                            <span>{{$message}}</span>
                        </div>
                        @enderror
                    </div>
                    <!-- New Password -->
                    <div class="col-md-4 mb-3">
                        <label for="newPassword">New Password</label>
                        <div class="input-group">
                            <input type="password" name="new_password" class="form-control" id="newPassword" placeholder="" required value="{{old('new_password')}}">
                            <div class="input-group-append">
                                <span class="input-group-text toggle-password" data-target="newPassword">
                                    <i class="ti-eye"></i>
                                </span>
                            </div>
                        </div>
                        @error('new_password')
                        <div class="invalid-feedback">
                            <span>{{$message}}</span>
                        </div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="col-md-4 mb-3">
                        <label for="confirmPassword">Confirm Password</label>
                        <div class="input-group">
                            <input type="password" name="confirm_password" class="form-control" id="confirmPassword" placeholder="" required value="{{old('confirm_password')}}">
                            <div class="input-group-append">
                                <span class="input-group-text toggle-password" data-target="confirmPassword">
                                    <i class="ti-eye"></i>
                                </span>
                            </div>
                        </div>
                        @error('confirm_password')
                        <div class="invalid-feedback">
                            <span>{{$message}}</span>
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <button class="btn btn-success float-right" id="saveButton" type="submit">Save Password</button>
                </div>
            </form>

            <!-- FontAwesome Icons -->
            <!-- JavaScript for Show/Hide Password -->
            <script>
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

                //disable button
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
                    submitButton.innerHTML = "Save Password";
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
